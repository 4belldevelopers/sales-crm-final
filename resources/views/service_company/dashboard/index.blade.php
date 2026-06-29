@extends('layouts.service_company_layout')
@push('head-script')
<script type="text/javascript" src="{{ asset('assets/charts/loader.js') }}{{ TM_VERSION }}"></script>
<script type="text/javascript" src="https://cdn.fusioncharts.com/fusioncharts/latest/fusioncharts.js"></script>

    <style>
        body {
            overflow-x: hidden;
        }

        .card-xxl-stretch-lock::after {
            content: "🔒";
            font-size: 31px;
            color: #333;
            position: absolute;
            background: #fffffffa;
            width: 100%;
            height: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        #requisitions_for_index_page,
        #interview_tracker_for_index_page,
        #joining_for_index_page,
        #offer_for_index_page {
            height: 350px !important;
            overflow-y: auto;
            display: block;
        }


        #requisitions_for_index_page thead,
        #interview_tracker_for_index_page thead,
        #joining_for_index_page thead,
        #offer_for_index_page thead {
            position: sticky;
            top: 0;
            background-color: #ffffff;
            z-index: 10;
        }


        #requisitions_for_index_page tbody tr {
            height: 82px;
            /* Adjust the height as per your requirement */
            position: relative;
        }

        #candidates_left_chart {
        height: 170px !important;
        width: 100% !important;
        }
        @media (min-width: 1200px) {
            .col-xl-cus-3 {
                flex: 0 0 auto;
                width: 30% !important;
                
            }
            .col-xl-cus-6 {
                flex: 0 0 auto;
                width: 40% !important;
            }
            
                .g-xl-10, .gx-xl-10 {
                    --bs-gutter-x: 0.5rem !important;
                }
                .mb-xl-10 {
                    margin-bottom: 0.5rem !important;
                }
                .row {
                   --bs-gutter-x: 0.5rem;
                }
                .mb-xl-12 {
                    margin-bottom: 1rem !important;
                }
            
        }

        .fs-2hx {
        font-size: 2rem !important;
    }

    #radialChart {
      max-width: 400px;
      margin: 50px auto;
    }

    .apexcharts-datalabel-value {
      font-size: 24px !important;
      font-weight: bold !important;
      fill: #0e1f40 !important;
    }

    .apexcharts-datalabel-label {
      font-size: 14px !important;
      font-weight: 600 !important;
      fill: #a3a9b4 !important;
    }
    
            
    </style>

  <style>
   

   #animation-container {
      position: fixed;
      top: 0;
      left: 0;
      width: 100vw;
      height: 100vh;
      pointer-events: none;
      z-index: 9999;
    }

    .congrats-message {
      position: absolute;
      top: 40%;
      left: 50%;
      transform: translate(-50%, -50%);
      font-size: 2.5em;
      color: #0bab08;
      background: #ffffffd6;
      padding: 10px;
      border-radius: 10%;
      font-weight: bold;
      animation: pop 1s ease-out;
      text-align: center;
      z-index: 10000;
    }

    @keyframes pop {
      0% { transform: scale(0) translate(-50%, -50%); }
      100% { transform: scale(1) translate(-50%, -50%); }
    }

    .balloon {
      position: absolute;
      width: 40px;
      height: 60px;
      background-color: red;
      border-radius: 50% 50% 50% 50% / 60% 60% 40% 40%;
      animation: float 10s linear infinite;
    }

    @keyframes float {
      0% { transform: translateY(100vh); opacity: 1; }
      100% { transform: translateY(-100vh); opacity: 0; }
    }

    .confetti {
      position: absolute;
      width: 10px;
      height: 10px;
      background-color: gold;
      animation: fall 3s linear infinite;
    }

    @keyframes fall {
      0% { transform: translateY(-20px); opacity: 1; }
      100% { transform: translateY(100vh); opacity: 0; }
    }



   
    


  </style>

  {{-- ===================== JERO DASHBOARD REDESIGN (2026) ===================== --}}
  <style id="jw-redesign">
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap');
    .kt_content_job{
        --jw-blue:#2b59ff; --jw-blue-dark:#1f47e0;
        --jw-ink:#0f1b3d; --jw-ink-2:#1b2b54;
        --jw-text:#1f2a44; --jw-muted:#8a93a8;
        --jw-line:#eceff5; --jw-bg:#f6f8fc;
        --jw-green:#22c55e; --jw-amber:#ff9f1a; --jw-red:#ef4444; --jw-purple:#8b5cf6; --jw-cyan:#06b6d4;
        --jw-radius:16px; --jw-shadow:0 6px 18px rgba(20,38,80,.05);
    }
    /* Poppins everywhere + compact scale for a 15.6" laptop. Base/body text capped at 15px. */
    .kt_content_job, .kt_content_job *{ font-family:'Poppins',-apple-system,'Segoe UI',Roboto,sans-serif!important; }
    .kt_content_job{ background:var(--jw-bg); font-size:13px; line-height:1.5; }
    /* tame Metronic's oversized utilities so nothing blows past a compact 15.6" layout */
    .kt_content_job .fs-2hx{ font-size:20px!important; }
    .kt_content_job .fs-2x,.kt_content_job .fs-1{ font-size:17px!important; }
    .kt_content_job .fs-2,.kt_content_job .fs-3,.kt_content_job .fs-4,.kt_content_job .fs-5,.kt_content_job .fs-6,.kt_content_job .card-label,.kt_content_job h1,.kt_content_job h2,.kt_content_job h3{ font-size:13px!important; }
    .kt_content_job .fs-7,.kt_content_job .fs-8,.kt_content_job small{ font-size:11px!important; }
    .kt_content_job .card-label,.kt_content_job h3.card-label{ font-size:15px!important; }

    /* ---- Generic card restyle ---- */
    .kt_content_job .card{
        border:1px solid var(--jw-line)!important;
        border-radius:var(--jw-radius)!important;
        box-shadow:var(--jw-shadow)!important;
        background:#fff;
    }
    .kt_content_job .card-header{ border-bottom:0!important; min-height:auto; }
    .kt_content_job .card-label,
    .kt_content_job h3.card-label{ font-weight:700!important; color:var(--jw-ink)!important; font-size:1.05rem; }

    /* section header helper (title + view all) */
    .jw-sechead{ display:flex; align-items:center; justify-content:space-between; }
    .jw-sechead .jw-title{ display:flex; align-items:center; gap:10px; font-weight:700; color:var(--jw-ink); font-size:1.05rem; }
    .jw-sechead .jw-title .jw-ico{ width:34px;height:34px;border-radius:10px;display:grid;place-items:center;background:#eef2ff;color:var(--jw-blue); }
    .jw-viewall{ color:var(--jw-blue)!important; font-weight:600; font-size:.85rem; text-decoration:none; white-space:nowrap; }
    .jw-viewall:hover{ text-decoration:underline; }

    /* ====== HERO (light, Metronic cards) ====== */
    .jw-card-soft{ border:1px solid var(--jw-line); border-radius:16px; background:#fff; box-shadow:var(--jw-shadow); }
    .jw-hd{ display:flex; align-items:center; justify-content:space-between; padding:13px 18px 0; }
    .jw-hd .ttl{ display:flex; align-items:center; gap:9px; font-weight:700; font-size:15px; color:var(--jw-ink); }
    .jw-hd .ttl .ic{ width:22px; height:22px; display:inline-flex; align-items:center; justify-content:center; }
    .jw-hd .ttl .ic svg{ width:22px; height:22px; display:block; }
    .jw-hd-right{ display:flex; align-items:center; gap:10px; font-size:12px; color:var(--jw-muted); }
    .jw-hd-right .dt{ color:var(--jw-blue); font-weight:600; }
    .jw-hd-right .sel{ border:1px solid var(--jw-line); border-radius:8px; padding:3px 10px; font-weight:600; color:var(--jw-text); display:inline-flex; align-items:center; gap:5px; }

    /* rank tiles */
    .jw-rank-row{ display:grid; grid-template-columns:repeat(3,minmax(0,1fr)); gap:12px; padding:14px 16px 18px; align-items:stretch; }
    .jw-rk{ display:flex; flex-direction:column; align-items:center; text-align:center; background:#fff; border:1px solid #e6ebf3; border-radius:12px; padding:14px 8px 13px; transition:.15s; }
    .jw-rk:hover{ border-color:#cdd8f0; box-shadow:0 6px 16px rgba(20,38,80,.06); }
    .jw-rk-lbl{ font-size:11px; color:var(--jw-blue); font-weight:600; line-height:1.3; margin-bottom:4px; min-height:29px; display:flex; align-items:center; justify-content:center; }
    .jw-rk-num{ position:relative; display:flex; align-items:center; justify-content:center; width:96px; height:90px; margin:2px auto 0; }
    .jw-rk-wreath{ position:absolute; inset:0; margin:auto; width:96px; height:96px; color:#2e2a4a; z-index:0; pointer-events:none; }
    .jw-rk-num b{ position:relative; z-index:1; font-size:clamp(23px,2vw,30px); font-weight:800; color:var(--jw-ink); letter-spacing:-1px; line-height:1; }
    /* keep multi-digit ranks inside the wreath opening */
    .jw-rk-num b.dlen-3{ font-size:clamp(19px,1.7vw,25px); letter-spacing:-1px; }
    .jw-rk-num b.dlen-4{ font-size:clamp(15px,1.4vw,20px); letter-spacing:-1.2px; }
    .jw-rk-num b.dlen-5,.jw-rk-num b.dlen-6{ font-size:clamp(12px,1.2vw,16px); letter-spacing:-1.4px; }
    .jw-rk-sub{ font-size:11px; color:var(--jw-muted); margin-top:6px; }
    .jw-rk-delta{ font-size:11px; font-weight:600; margin-top:4px; display:flex; align-items:center; gap:3px; justify-content:center; }
    .jw-rk-delta.up{ color:var(--jw-green); } .jw-rk-delta.down{ color:var(--jw-red); }
    .jw-rk-btn{ margin-top:auto; width:100%; display:block; font-size:11px; font-weight:600; color:var(--jw-blue); background:#fff; border:1px solid #d8e0f5; border-radius:8px; padding:6px 10px; cursor:pointer; transition:.15s; }
    .jw-rk-btn{ margin-top:11px; }
    .jw-rk-btn:hover{ background:var(--jw-blue); color:#fff; border-color:var(--jw-blue); }

    /* KPI tiles */
    /* Daily Performance card fills its height; the KPI content is centred so spare space stays as padding
       (sparklines keep a fixed, undistorted height instead of stretching) */
    .jw-dp-card{ display:flex; flex-direction:column; }
    .jw-dp-card .jw-kpi-row{ flex:1 1 auto; align-content:center; }
    .jw-kpi-row{ display:grid; grid-template-columns:repeat(4,minmax(0,1fr)); gap:8px; padding:16px 20px; }
    .jw-kpi{ padding:2px 12px 2px 0; min-width:0; display:flex; flex-direction:column; }
    .jw-kpi + .jw-kpi{ border-left:1px solid var(--jw-line); padding-left:16px; }
    .jw-kpi-t{ font-size:12.5px; color:var(--jw-ink); font-weight:600; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
    .jw-kpi-v{ font-size:clamp(18px,1.7vw,26px); font-weight:800; line-height:1.5; margin-top:2px; }
    .jw-kpi-d{ font-size:11px; font-weight:600; display:flex; align-items:center; gap:3px; margin-top:2px; }
    .jw-kpi-d.up{ color:var(--jw-green); } .jw-kpi-d.down{ color:var(--jw-red); } .jw-kpi-d.flat{ color:var(--jw-muted); }
    .jw-spark{ width:100%; height:54px; margin-top:12px; display:block; }

    /* company global ranking */
    .jw-cg{ display:grid; grid-template-columns:minmax(0,1.15fr) repeat(4,minmax(0,1fr)); gap:22px; align-items:center; padding:16px 20px; }
    .jw-cg-cap{ font-weight:700; font-size:15px; color:var(--jw-ink); display:flex; flex-direction:column; justify-content:center; gap:2px; }
    .jw-cg-cap small{ display:block; font-weight:500; font-size:11px; color:var(--jw-muted); margin-top:3px; }
    .jw-cg-item .nm{ font-size:12px; color:var(--jw-text); font-weight:600; margin-bottom:4px; }
    .jw-cg-item .nm .yu{ color:var(--jw-blue); }
    .jw-cg-item .pts{ font-size:12px; color:var(--jw-ink); font-weight:700; margin-bottom:7px; }
    .jw-cg-bar{ height:6px; border-radius:6px; background:#eef1f7; overflow:hidden; }
    .jw-cg-bar i{ display:block; height:100%; border-radius:6px; }
    /* ---- responsive: tuned for 13"–17" laptops then tablets/phones ---- */
    @media (max-width:1599.98px){ .jw-cg{ gap:18px; } }
    @media (max-width:1399.98px){
        .jw-hd{ padding:12px 14px 0; }
        .jw-hd .ttl{ font-size:14px; }
        .jw-rk{ padding:6px 4px; } .jw-kpi{ padding:4px 9px; }
        .jw-cg{ gap:14px; padding:14px 16px; }
    }
    @media (max-width:1199.98px){
        /* hero cards stack (Bootstrap col-xl) — give each full width breathing room */
        .jw-kpi-row{ grid-template-columns:repeat(4,minmax(0,1fr)); }
        .jw-cg{ grid-template-columns:repeat(2,minmax(0,1fr)); row-gap:16px; }
        .jw-cg-cap{ grid-column:1 / -1; }
    }
    @media (max-width:991.98px){
        .jw-kpi-row{ grid-template-columns:repeat(2,minmax(0,1fr)); }
        .jw-kpi:nth-child(2){ border-right:0; }
        .jw-kpi:nth-child(1),.jw-kpi:nth-child(2){ border-bottom:1px solid var(--jw-line); padding-bottom:10px; margin-bottom:6px; }
    }
    @media (max-width:575.98px){
        .jw-rank-row,.jw-kpi-row,.jw-cg{ grid-template-columns:1fr; }
        .jw-rk,.jw-kpi{ border-right:0; border-bottom:1px solid var(--jw-line); padding-bottom:12px; }
        .jw-rk:last-child,.jw-kpi:last-child{ border-bottom:0; }
        .jw-cg-cap{ grid-column:auto; margin-bottom:4px; }
    }

    /* ---- STAT TILES ---- */
    .jw-stat .card-header{ padding-top:20px; }
    .jw-stat .jw-stat-top{ display:flex; align-items:flex-start; gap:14px; }
    .jw-stat-ico{ width:46px;height:46px;border-radius:13px;display:grid;place-items:center;font-size:1.25rem;flex:none; }
    .jw-stat-ico.blue{ background:#e9f0ff;color:var(--jw-blue);}
    .jw-stat-ico.violet{ background:#f1ecff;color:#7c5cff;}
    .jw-stat-ico.green{ background:#e6f9ee;color:#15b86a;}
    .jw-stat-ico.amber{ background:#fff3e2;color:#f08c00;}
    .jw-stat .jw-num{ font-size:1.9rem;font-weight:800;color:var(--jw-ink);line-height:1;letter-spacing:-1px; }
    .jw-stat .jw-cap{ color:var(--jw-blue);font-weight:700;font-size:.82rem; }
    .jw-stat .jw-sub{ color:var(--jw-muted);font-size:.74rem; }
    .jw-delta{ display:inline-flex;align-items:center;gap:4px;font-size:.74rem;font-weight:600;margin-top:6px; }
    .jw-delta.up{ color:var(--jw-green);} .jw-delta.down{ color:var(--jw-red);} .jw-delta.flat{ color:var(--jw-muted);}
    .jw-legend{ display:flex;align-items:center;gap:7px;font-size:.78rem;color:var(--jw-text);margin-top:7px; }
    .jw-dot{ width:8px;height:8px;border-radius:50%;flex:none; }

    /* ---- Buttons / selects ---- */
    .kt_content_job .form-select{ border:1px solid var(--jw-line)!important; border-radius:11px!important; font-weight:600; color:var(--jw-text); font-size:.82rem; }
    .jw-pill{ border:1px solid var(--jw-line); background:#fff; border-radius:30px; padding:5px 14px; font-size:.8rem; font-weight:600; color:var(--jw-text); }

    /* ---- Tables ---- */
    .kt_content_job .table thead tr{ color:var(--jw-muted)!important; }
    .kt_content_job .table thead th{ font-size:.7rem!important; letter-spacing:.04em; }
    .jw-table td,.jw-table th{ vertical-align:middle; }
    .jw-avatar{ width:38px;height:38px;border-radius:50%;object-fit:cover;display:grid;place-items:center;font-weight:700;flex:none; }
    .jw-badge{ display:inline-flex;align-items:center;gap:5px;padding:4px 12px;border-radius:30px;font-size:.74rem;font-weight:600; }
    .jw-badge.green{ background:#e6f9ee;color:#15a85f;} .jw-badge.amber{ background:#fff3e2;color:#e08a00;} .jw-badge.red{ background:#fdecec;color:#e5484d;} .jw-badge.blue{ background:#e9f0ff;color:var(--jw-blue);}
    .jw-rank-pill{ font-weight:800;color:var(--jw-ink); }
    .jw-trend-up{ color:var(--jw-green);} .jw-trend-down{ color:var(--jw-red);}

    /* coins upgrade buttons keep brand colors but match radius */
    .kt_content_job .btn{ border-radius:11px; }

    /* ===== STAT CARDS (jobs / candidates / offers / joinings) ===== */
    .jw-sc{ display:flex; flex-direction:column; height:100%; padding:16px 16px 14px; }
    .jw-sc-head{ display:flex; gap:12px; align-items:flex-start; }
    .jw-sc-ico{ width:42px; height:42px; border-radius:11px; display:grid; place-items:center; flex:none; }
    .jw-sc-ico svg{ width:22px; height:22px; }
    .jw-sc-ico.blue{ background:#e9f0ff; color:#2b59ff; }
    .jw-sc-ico.violet{ background:#f1ecff; color:#7c5cff; }
    .jw-sc-ico.green{ background:#e6f9ee; color:#15b86a; }
    .jw-sc-ico.amber{ background:#fff3e2; color:#f08c00; }
    .jw-sc-label{ font-size:12.5px; font-weight:600; color:#2b59ff; }
    .jw-sc-num{ font-size:22px; font-weight:800; color:var(--jw-ink); line-height:1.3; }
    .jw-sc-meta{ font-size:11px; color:var(--jw-muted); }
    .jw-sc-breakdown{ margin-top:12px; display:flex; flex-direction:column; gap:7px; }
    .jw-sc-breakdown .r{ display:flex; align-items:center; font-size:12px; color:var(--jw-text); }
    .jw-sc-breakdown .r .nm{ flex:1; }
    .jw-sc-breakdown .r b{ font-weight:700; color:var(--jw-ink); }
    .jw-sc-breakdown .dot{ width:8px; height:8px; border-radius:50%; margin-right:8px; flex:none; }
    .jw-sc-delta{ margin-top:auto; padding-top:14px; font-size:11.5px; font-weight:600; display:flex; align-items:center; gap:4px; }
    .jw-sc-delta.up{ color:var(--jw-green); } .jw-sc-delta.down{ color:var(--jw-red); } .jw-sc-delta.flat{ color:var(--jw-muted); }

    /* ===== CHART CARDS ===== */
    .jw-ch{ padding:14px 16px 12px; height:100%; display:flex; flex-direction:column; }
    .jw-ch-head{ display:flex; align-items:center; justify-content:space-between; margin-bottom:8px; gap:8px; }
    .jw-ch-title{ font-size:14px; font-weight:700; color:var(--jw-ink); }
    .jw-ch-title small{ color:var(--jw-blue); font-weight:600; font-size:11px; margin-left:5px; }
    .jw-ch-sel{ border:1px solid var(--jw-line); border-radius:8px; padding:3px 9px; font-size:11px; font-weight:600; color:var(--jw-text); display:inline-flex; gap:5px; align-items:center; white-space:nowrap; }
    .jw-ch-body{ flex:1 1 auto; display:flex; align-items:center; min-height:0; }
    .jw-ch-body svg{ display:block; width:100%; height:auto; }

    /* funnel */
    .jw-fn{ display:flex; flex-direction:column; gap:10px; width:100%; padding:2px 0; }
    .jw-fn-row{ display:flex; align-items:center; gap:10px; font-size:12px; }
    .jw-fn-row .lbl{ width:74px; color:var(--jw-text); flex:none; }
    .jw-fn-row .track{ flex:1; height:14px; display:flex; align-items:center; }
    .jw-fn-row .track i{ display:block; height:100%; border-radius:4px; min-width:6px; }
    .jw-fn-row .val{ width:56px; text-align:right; font-weight:700; color:var(--jw-ink); flex:none; }

    /* interviews header total */
    .jw-iv-total{ font-size:26px; font-weight:800; color:var(--jw-ink); line-height:1.1; }
    .jw-iv-sub{ font-size:11px; color:var(--jw-muted); margin-bottom:2px; }

    /* ===== Offer Acceptance Rate ===== */
    .jw-acc{ display:flex; flex-direction:column; height:100%; padding:14px 18px 16px; }
    .jw-acc-desc{ font-size:12px; color:var(--jw-muted); margin:6px 0 0; }
    .jw-acc-body{ flex:1 1 auto; display:flex; flex-direction:column; justify-content:center; }
    .jw-acc-pct{ font-size:40px; font-weight:800; color:var(--jw-ink); letter-spacing:-1.5px; line-height:1; }
    .jw-acc-sub{ font-size:12.5px; color:var(--jw-blue); font-weight:600; margin:12px 0 12px; }
    .jw-acc-bar{ height:10px; border-radius:8px; background:#eef1f7; overflow:hidden; }
    .jw-acc-bar i{ display:block; height:100%; border-radius:8px; background:linear-gradient(90deg,#16a34a,#22c55e); }
    .jw-acc-foot{ font-size:11px; color:var(--jw-muted); margin-top:14px; }

    /* ===== data tables (recent jobs / recruiters) ===== */
    .jw-tbl{ width:100%; border-collapse:collapse; }
    .jw-tbl thead th{ text-align:left; font-size:11px; font-weight:600; color:var(--jw-muted); padding:8px 10px; border-bottom:1px solid var(--jw-line); white-space:nowrap; }
    .jw-tbl tbody td{ font-size:12.5px; color:var(--jw-text); padding:11px 10px; border-bottom:1px solid #f4f6fa; vertical-align:middle; }
    .jw-tbl tbody tr:last-child td{ border-bottom:0; }
    .jw-tbl .name{ font-weight:600; color:var(--jw-ink); }
    .jw-rc{ display:flex; align-items:center; gap:10px; }
    .jw-rc .av{ width:32px; height:32px; border-radius:50%; display:grid; place-items:center; font-weight:700; font-size:12px; color:#fff; flex:none; }
    .jw-rc .nm{ font-weight:600; color:var(--jw-blue); font-size:12.5px; }
    .jw-score{ font-weight:700; color:var(--jw-ink); }
    .jw-rankcell{ font-weight:700; color:var(--jw-ink); }

    /* ===== team / vendor mini reports ===== */
    .jw-rep{ padding:16px 16px 14px; height:100%; display:flex; flex-direction:column; }
    .jw-rep-title{ font-size:14px; font-weight:700; color:var(--jw-ink); }
    .jw-rep-date{ font-size:11px; color:var(--jw-muted); margin:6px 0 12px; }
    .jw-rep-head{ display:flex; align-items:center; gap:8px; font-size:10.5px; font-weight:600; letter-spacing:.04em; text-transform:uppercase; color:var(--jw-muted); padding-bottom:8px; margin-bottom:10px; border-bottom:1px solid var(--jw-line); }
    .jw-rep-head span:first-child{ flex:1; }
    .jw-rep-head span:nth-child(2){ width:46px; text-align:right; flex:none; }
    .jw-rep-head span:nth-child(3){ width:40px; text-align:right; flex:none; }
    .jw-rep-row{ margin-bottom:13px; }
    .jw-rep-row.me{ background:#f3f7ff; border-radius:8px; padding:8px 8px 9px; margin:0 -8px 12px; }
    .jw-rep-row.me .top .nm{ color:var(--jw-blue); font-weight:600; }
    .jw-rep-row .rk{ display:inline-grid; place-items:center; min-width:17px; height:17px; padding:0 3px; border-radius:5px; background:#eef2ff; color:var(--jw-blue); font-size:9.5px; font-weight:700; margin-right:8px; vertical-align:middle; }
    .jw-rep-row.me .rk{ background:var(--jw-blue); color:#fff; }
    .jw-rep-row .top{ display:flex; align-items:center; gap:8px; font-size:12px; margin-bottom:6px; }
    .jw-rep-row .top .nm{ flex:1; color:var(--jw-text); overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }
    .jw-rep-row .top .sc{ width:46px; text-align:right; font-weight:700; color:var(--jw-ink); flex:none; }
    .jw-rep-row .top .rkc{ width:40px; text-align:right; font-weight:700; color:var(--jw-blue); flex:none; }
    .jw-rep-row .bar{ height:6px; border-radius:6px; background:#eef1f7; overflow:hidden; }
    .jw-rep-row .bar i{ display:block; height:100%; border-radius:6px; min-width:6px; }
    .jw-rep-link{ margin-top:auto; padding-top:6px; font-size:12px; font-weight:600; color:var(--jw-blue); text-decoration:none; }
    .jw-rep-link:hover{ text-decoration:underline; }
    .jw-rep-empty{ font-size:12px; color:var(--jw-muted); padding:6px 0 14px; }

    /* ===== Daily Leaderboards ===== */
    .jw-lb-head{ display:flex; align-items:center; justify-content:space-between; gap:12px; margin-bottom:16px; flex-wrap:wrap; }
    .jw-lb-title{ display:flex; align-items:center; gap:12px; }
    .jw-lb-title .ic{ width:44px; height:44px; border-radius:12px; display:grid; place-items:center; background:#eef2ff; color:#2b59ff; flex:none; }
    .jw-lb-title .ic svg{ width:23px; height:23px; }
    .jw-lb-title h3{ font-size:18px!important; font-weight:800; color:var(--jw-ink); margin:0; line-height:1.2; }
    .jw-lb-title p{ font-size:12px; color:var(--jw-muted); margin:3px 0 0; }
    .jw-lb-meta{ display:flex; align-items:center; gap:10px; font-size:12px; color:var(--jw-muted); }
    .jw-lb-meta .upd{ display:inline-flex; align-items:center; gap:6px; }
    .jw-lb-meta .upd svg{ width:14px; height:14px; }
    .jw-lb-meta .refresh{ width:32px; height:32px; border:1px solid var(--jw-line); border-radius:9px; display:grid; place-items:center; color:var(--jw-muted); }
    .jw-lb-meta .refresh svg{ width:16px; height:16px; }
    .jw-lb-meta .refresh:hover{ color:var(--jw-blue); border-color:#cdd8f0; }
    .jw-lb-meta .datepill{ display:inline-flex; align-items:center; gap:7px; border:1px solid var(--jw-line); border-radius:9px; padding:7px 13px; font-weight:600; color:var(--jw-text); }
    .jw-lb-meta .datepill svg{ width:14px; height:14px; }

    .jw-lb{ background:#fff; border:1px solid var(--jw-line); border-radius:18px; box-shadow:var(--jw-shadow); padding:18px 20px 14px; }
    .jw-lb-card{ border:1px solid #e7ecf4; border-radius:14px; background:#fff; box-shadow:none; padding:14px 14px 8px; height:100%; }
    .jw-lb-card-head{ display:flex; align-items:center; gap:10px; margin-bottom:14px; }
    .jw-lb-card-head .ic{ width:34px; height:34px; border-radius:10px; display:grid; place-items:center; flex:none; }
    .jw-lb-card-head .ic svg{ width:18px; height:18px; }
    .jw-lb-card-head .ttl{ font-weight:700; font-size:14px; flex:1; }
    .jw-lb-card-head .cnt{ display:inline-flex; align-items:center; gap:5px; font-size:11px; color:var(--jw-muted); font-weight:500; white-space:nowrap; }
    .jw-lb-card-head .cnt svg{ width:13px; height:13px; }

    .jw-lb-sum{ display:grid; grid-template-columns:1fr 1fr; gap:10px; background:#f7f9fc; border-radius:12px; padding:12px 14px; margin-bottom:14px; }
    .jw-lb-sum .l{ font-size:11px; color:var(--jw-muted); margin-bottom:4px; }
    .jw-lb-sum .s-score .v{ font-size:23px; font-weight:800; line-height:1; }
    .jw-lb-sum .s-top{ border-left:1px solid var(--jw-line); padding-left:14px; }
    .jw-lb-sum .tp{ display:flex; align-items:center; gap:6px; font-size:13px; font-weight:700; color:var(--jw-ink); }
    .jw-lb-sum .tp .medal{ font-size:16px; flex:none; }
    .jw-lb-sum .tp .tpn{ overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }
    .jw-lb-sum .tp .tps{ margin-left:auto; font-weight:800; flex:none; }
    .jw-lb-sum .noleader{ display:inline-flex; align-items:center; gap:6px; font-size:12px; font-weight:600; border-radius:9px; padding:8px 11px; margin-top:12px; }
    .jw-lb-sum .noleader svg{ width:15px; height:15px; }

    .jw-lb-th{ display:flex; align-items:center; gap:8px; font-size:10px; font-weight:600; letter-spacing:.04em; text-transform:uppercase; color:var(--jw-muted); padding:0 4px 8px; border-bottom:1px solid var(--jw-line); margin-bottom:4px; }
    .jw-lb-row{ display:flex; align-items:center; gap:8px; padding:7px 4px; }
    .jw-lb-th .c-rk,.jw-lb-row .c-rk{ width:26px; flex:none; text-align:center; }
    .jw-lb-th .c-nm,.jw-lb-row .c-nm{ flex:1; display:flex; align-items:center; gap:8px; min-width:0; }
    .jw-lb-th .c-sc,.jw-lb-row .c-sc{ width:98px; flex:none; display:flex; align-items:center; gap:8px; justify-content:flex-end; }
    .jw-lb-th .c-rank,.jw-lb-row .c-rank{ width:34px; flex:none; text-align:right; font-weight:700; }
    .jw-lb-row .rkb{ display:inline-grid; place-items:center; width:22px; height:22px; border-radius:50%; font-size:11px; font-weight:700; color:#fff; }
    .jw-lb-row .rkb.g1{ background:linear-gradient(135deg,#f7b733,#f5a623); }
    .jw-lb-row .rkb.g2{ background:linear-gradient(135deg,#c2cad6,#9aa3b2); }
    .jw-lb-row .rkb.g3{ background:linear-gradient(135deg,#e2915a,#cd7f32); }
    .jw-lb-row .rkb.gn{ background:#eef1f7; color:var(--jw-muted); }
    .jw-lb-row .av{ width:26px; height:26px; border-radius:50%; display:grid; place-items:center; font-size:11px; font-weight:700; flex:none; }
    .jw-lb-row .c-nm .nm{ font-size:12.5px; font-weight:600; color:var(--jw-ink); overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }
    .jw-lb-row .youtag{ font-size:9px; font-weight:700; color:#fff; background:var(--jw-blue); border-radius:5px; padding:2px 6px; flex:none; }
    .jw-lb-row .c-sc .bar{ flex:1; height:6px; border-radius:6px; background:#eef1f7; overflow:hidden; min-width:30px; }
    .jw-lb-row .c-sc .bar i{ display:block; height:100%; border-radius:6px; }
    .jw-lb-row .c-sc b{ font-size:12.5px; font-weight:700; color:var(--jw-ink); min-width:16px; text-align:right; }
    .jw-lb-row.me{ background:#f3f7ff; border:1px solid #dbe6ff; border-radius:10px; margin-top:6px; }
    .jw-lb-empty{ text-align:center; color:var(--jw-muted); font-size:12px; padding:22px 0; }

    /* ===== Monthly Performers ===== */
    .jw-mp-card{ border:1px solid #e7ecf4; border-radius:14px; background:#fff; padding:14px 14px 10px; height:100%; }
    .jw-mp-ch{ display:flex; align-items:center; gap:10px; margin-bottom:12px; }
    .jw-mp-ch .ic{ width:34px; height:34px; border-radius:10px; display:grid; place-items:center; flex:none; }
    .jw-mp-ch .ic svg{ width:18px; height:18px; }
    .jw-mp-ch .ttl{ font-weight:700; font-size:14px; flex:1; }
    .jw-mp-ch .arr{ color:var(--jw-muted); font-size:16px; text-decoration:none; }
    .jw-mp-ch .arr:hover{ color:var(--jw-blue); }
    .jw-mp-stats{ display:grid; grid-template-columns:repeat(3,1fr); gap:8px; background:#f7f9fc; border-radius:12px; padding:11px 12px; margin-bottom:14px; }
    .jw-mp-stats .l{ font-size:10px; color:var(--jw-muted); margin-bottom:3px; white-space:nowrap; }
    .jw-mp-stats .v{ font-size:17px; font-weight:800; line-height:1; }
    .jw-mp-stats > div + div{ border-left:1px solid var(--jw-line); padding-left:10px; }

    .jw-mp-podium{ display:grid; grid-template-columns:1fr 1.12fr 1fr; gap:8px; align-items:end; margin-bottom:14px; }
    .jw-mp-podium .pod{ display:flex; flex-direction:column; align-items:center; text-align:center; border:1px solid #eef1f7; border-radius:12px; padding:12px 6px 10px; background:#fff; }
    .jw-mp-podium .pod.empty{ border:0; background:transparent; }
    .jw-mp-podium .pod.p1{ padding-top:16px; padding-bottom:14px; background:linear-gradient(180deg,#fffbf0,#fff); border-color:#f3e2b8; box-shadow:0 6px 16px rgba(245,166,35,.12); }
    .jw-mp-podium .pod .medal{ display:inline-grid; place-items:center; width:20px; height:20px; border-radius:50%; font-size:10.5px; font-weight:700; color:#fff; margin-bottom:8px; }
    .jw-mp-podium .pod .medal.g1{ background:linear-gradient(135deg,#f7b733,#f5a623); }
    .jw-mp-podium .pod .medal.g2{ background:linear-gradient(135deg,#c2cad6,#9aa3b2); }
    .jw-mp-podium .pod .medal.g3{ background:linear-gradient(135deg,#e2915a,#cd7f32); }
    .jw-mp-podium .pod .pav{ width:38px; height:38px; border-radius:50%; display:grid; place-items:center; font-weight:700; font-size:14px; margin-bottom:7px; }
    .jw-mp-podium .pod.p1 .pav{ width:46px; height:46px; }
    .jw-mp-podium .pod .pav svg{ width:20px; height:20px; }
    .jw-mp-podium .pod.p1 .pav svg{ width:24px; height:24px; }
    .jw-mp-podium .pod .pnm{ font-size:11.5px; font-weight:600; color:var(--jw-ink); max-width:100%; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }
    .jw-mp-podium .pod .pbig{ font-size:18px; font-weight:800; line-height:1.3; }
    .jw-mp-podium .pod.p1 .pbig{ font-size:21px; }
    .jw-mp-podium .pod .psub{ display:flex; flex-direction:column; gap:2px; font-size:10px; color:var(--jw-muted); margin-top:2px; }
    .jw-mp-podium .pod .psub b{ color:var(--jw-text); }

    .jw-mp-tbl{ width:100%; border-collapse:collapse; }
    .jw-mp-tbl thead th{ text-align:right; font-size:10px; font-weight:600; letter-spacing:.03em; text-transform:uppercase; color:var(--jw-muted); padding:6px 6px; border-bottom:1px solid var(--jw-line); }
    .jw-mp-tbl thead th:first-child{ text-align:left; }
    .jw-mp-tbl tbody td{ text-align:right; font-size:12px; padding:8px 6px; border-bottom:1px solid #f4f6fa; }
    .jw-mp-tbl tbody td:first-child{ text-align:left; }
    .jw-mp-tbl tbody tr:last-child td{ border-bottom:0; }
    .jw-mp-tbl .rc{ display:flex; align-items:center; gap:8px; }
    .jw-mp-tbl .rc .rkb{ display:inline-grid; place-items:center; width:20px; height:20px; border-radius:50%; font-size:10px; font-weight:700; color:#fff; flex:none; }
    .jw-mp-tbl .rc .rkb.g1{ background:linear-gradient(135deg,#f7b733,#f5a623); }
    .jw-mp-tbl .rc .rkb.g2{ background:linear-gradient(135deg,#c2cad6,#9aa3b2); }
    .jw-mp-tbl .rc .rkb.g3{ background:linear-gradient(135deg,#e2915a,#cd7f32); }
    .jw-mp-tbl .rc .rkb.gn{ background:#eef1f7; color:var(--jw-muted); }
    .jw-mp-tbl .rc .av{ width:24px; height:24px; border-radius:50%; display:grid; place-items:center; font-size:11px; font-weight:700; color:#fff; flex:none; }
    .jw-mp-tbl .rc .nm{ font-weight:600; color:var(--jw-ink); }
    .jw-mp-tbl .pct,.jw-mp-tbl .sc{ font-weight:600; color:var(--jw-ink); }
    .jw-mp-tbl .rank{ font-weight:700; }
    .jw-mp-tbl .youtag{ font-size:9px; font-weight:700; color:#fff; background:var(--jw-blue); border-radius:5px; padding:2px 6px; margin-left:6px; }
    .jw-mp-tbl tr.me td{ background:#f3f7ff; }
    .jw-mp-tbl tr.me td:first-child{ border-top-left-radius:8px; border-bottom-left-radius:8px; }
    .jw-mp-tbl tr.me td:last-child{ border-top-right-radius:8px; border-bottom-right-radius:8px; }

    /* ===== Recruiter Score Board (redesign · Metronic · Poppins · ≤14px) ===== */
    #your_rank_company .modal-dialog, #your_rank_global .modal-dialog{ max-width:1180px; }
    .sb-wrap{ font-family:'Poppins',sans-serif; }
    .rsb, .rsb *{ box-sizing:border-box; font-family:'Poppins',sans-serif; }
    .rsb{ font-size:12.5px; line-height:1.5; color:#1f2a44; }
    .rsb .muted{ color:#8a93a8; }

    /* header */
    .rsb-top{ display:flex; align-items:flex-start; justify-content:space-between; gap:12px; flex-wrap:wrap; margin-bottom:14px; }
    .rsb-top .l{ display:flex; align-items:center; gap:12px; min-width:0; }
    .rsb-tico{ width:44px; height:44px; border-radius:12px; display:grid; place-items:center; background:linear-gradient(150deg,#fbbf24,#f59e0b); color:#7a4a00; flex:none; box-shadow:0 6px 16px rgba(245,158,11,.3); }
    .rsb-tico svg{ width:23px; height:23px; }
    .rsb-ttl{ font-size:18px; font-weight:700; color:#0f1b3d; margin:0; line-height:1.15; display:flex; align-items:center; gap:8px; flex-wrap:wrap; }
    .rsb-ttl .mo{ color:#4f46e5; font-weight:700; }
    .rsb-ttl .badge-g{ font-size:10px; font-weight:700; color:#6d28d9; background:#ede9fe; border-radius:20px; padding:2px 9px; }
    .rsb-sub{ font-size:11.5px; color:#8a93a8; margin:3px 0 0; }
    .rsb-top .r{ display:flex; align-items:center; gap:8px; flex-wrap:wrap; }
    .rsb-pill{ display:inline-flex; align-items:center; gap:7px; border:1px solid #e6ebf3; border-radius:9px; padding:7px 12px; font-size:12px; font-weight:600; color:#1f2a44; background:#fff; }
    .rsb-pill svg{ width:14px; height:14px; color:#8a93a8; }
    .rsb-btn{ display:inline-flex; align-items:center; gap:7px; border:1px solid #e6ebf3; border-radius:9px; padding:7px 13px; font-size:12px; font-weight:600; color:#1f2a44; background:#fff; text-decoration:none; cursor:pointer; }
    .rsb-btn svg{ width:14px; height:14px; }
    .rsb-btn:hover{ background:#f7f9fc; color:#1f2a44; }
    .rsb-x{ width:32px; height:32px; border-radius:8px; display:grid; place-items:center; cursor:pointer; color:#0f1b3d; flex:none; }
    .rsb-x svg{ width:20px; height:20px; }
    .rsb-x:hover{ background:#f1f3f8; }

    /* overview */
    .rsb-ov{ display:grid; grid-template-columns:minmax(220px,1.12fr) 2.55fr; gap:12px; align-items:stretch; }
    .rsb-ovr{ display:grid; grid-template-columns:repeat(12,1fr); gap:12px; align-content:start; }
    .rsb-card{ border:1px solid #eef1f7; border-radius:14px; background:#fff; padding:14px; }

    /* hero */
    .rsb-hero{ border-radius:16px; padding:18px; color:#dfe4fb; text-align:center; display:flex; flex-direction:column; align-items:center; justify-content:center; gap:8px; background:radial-gradient(520px 220px at 80% -10%, rgba(124,92,255,.5), transparent 60%), linear-gradient(155deg,#312e81 0%,#4338ca 55%,#6d4aff 100%); }
    .rsb-hero .cap{ font-size:10px; font-weight:700; letter-spacing:1.5px; color:#b9c0e6; }
    .rsb-hero .big{ font-size:42px; font-weight:800; color:#fff; line-height:1; }
    .rsb-hero .delta{ font-size:11px; font-weight:600; background:rgba(34,197,94,.2); color:#8ff0b3; border-radius:20px; padding:3px 11px; }
    .rsb-medal{ width:66px; height:66px; border-radius:50%; display:grid; place-items:center; background:radial-gradient(circle at 50% 30%,#ffe49a,#f59e0b); color:#7a4a00; font-size:25px; box-shadow:0 8px 22px rgba(245,158,11,.4); margin-top:4px; }
    .rsb-tier{ font-size:12px; font-weight:700; color:#ffe49a; }

    /* mini stat cards */
    .rsb-mini .h{ display:flex; align-items:center; gap:8px; font-size:11.5px; color:#8a93a8; font-weight:600; margin-bottom:9px; }
    .rsb-mini .h .i{ width:26px; height:26px; border-radius:8px; display:grid; place-items:center; flex:none; }
    .rsb-mini .h .i svg{ width:15px; height:15px; }
    .rsb-mini .v{ font-size:21px; font-weight:800; color:#0f1b3d; line-height:1; }
    .rsb-mini .d{ font-size:11px; font-weight:500; margin-top:6px; }
    .a-rankc{ grid-column:span 3; } .a-rankg{ grid-column:span 3; } .a-pct{ grid-column:span 2; } .a-trend{ grid-column:span 4; }
    .a-ach{ grid-column:span 4; } .a-ai{ grid-column:span 5; } .a-mile{ grid-column:span 3; }

    /* sparkline */
    .rsb-spark{ position:relative; }
    .rsb-spark svg{ width:100%; height:46px; display:block; }
    .rsb-spark-now{ position:absolute; top:-2px; right:0; font-size:11px; font-weight:800; color:#4f46e5; background:#eef2ff; border-radius:6px; padding:2px 7px; }

    /* card head row */
    .rsb-ch{ display:flex; align-items:center; justify-content:space-between; gap:8px; margin-bottom:11px; }
    .rsb-ch .t{ display:flex; align-items:center; gap:7px; font-size:12.5px; font-weight:700; color:#0f1b3d; }
    .rsb-ch .t svg{ width:16px; height:16px; }
    .rsb-chip{ font-size:10.5px; font-weight:700; color:#4f46e5; background:#eef2ff; border-radius:20px; padding:2px 9px; }

    /* achievements */
    .rsb-ach{ display:grid; grid-template-columns:repeat(4,1fr); gap:8px; }
    .rsb-ach .it{ text-align:center; }
    .rsb-ach .it .ic{ width:38px; height:38px; border-radius:11px; display:grid; place-items:center; margin:0 auto 6px; background:#f1f3f8; color:#b3bccd; }
    .rsb-ach .it .ic svg{ width:20px; height:20px; }
    .rsb-ach .it .nm{ font-size:10px; font-weight:600; color:#3b4a66; line-height:1.25; display:block; }
    .rsb-ach .it.off{ opacity:.55; }
    .rsb-ach .it.off .nm{ color:#9aa3b2; }

    /* AI insight */
    .rsb-ai{ background:linear-gradient(135deg,#f5f3ff,#eef2ff); border-color:#e4e0fb; }
    .rsb-ai-ic{ width:20px; height:20px; border-radius:6px; display:inline-grid; place-items:center; color:#fff; font-weight:800; font-size:9px; background:linear-gradient(150deg,#6d4aff,#9b5cff); }
    .rsb-ai .bd p{ font-size:11.5px; color:#3b4a66; margin:0 0 6px; line-height:1.45; }
    .rsb-ai .bd p:last-child{ margin-bottom:0; }

    /* milestone */
    .rsb-mile .lbl{ font-size:12px; font-weight:700; color:#0f1b3d; margin-bottom:8px; }
    .rsb-mile .bar{ height:8px; border-radius:8px; background:#eef1f7; overflow:hidden; }
    .rsb-mile .bar i{ display:block; height:100%; border-radius:8px; background:linear-gradient(90deg,#6d4aff,#9b5cff); }
    .rsb-mile .num{ font-size:10.5px; color:#8a93a8; font-weight:600; text-align:right; margin-top:6px; }

    /* main grid */
    .rsb-main{ display:grid; grid-template-columns:0.95fr 2fr 1.05fr; gap:12px; margin-top:12px; }
    .rsb-h4{ font-size:13.5px; font-weight:700; color:#0f1b3d; margin:0 0 12px; }

    /* contribution */
    .rsb-contrib{ display:flex; flex-direction:column; }
    .rsb-donut{ position:relative; width:130px; height:130px; margin:4px auto 14px; }
    .rsb-donut svg{ transform:rotate(-90deg); width:100%; height:100%; }
    .rsb-donut .c{ position:absolute; inset:0; display:flex; flex-direction:column; align-items:center; justify-content:center; }
    .rsb-donut .c b{ font-size:19px; font-weight:800; color:#0f1b3d; } .rsb-donut .c span{ font-size:10px; color:#8a93a8; }
    .rsb-leg{ display:flex; flex-direction:column; gap:9px; font-size:12px; }
    .rsb-leg .r{ display:flex; align-items:center; gap:8px; }
    .rsb-leg .r .d{ width:9px; height:9px; border-radius:50%; flex:none; }
    .rsb-leg .r .nm{ flex:1; color:#3b4a66; } .rsb-leg .r b{ color:#0f1b3d; font-weight:700; }

    /* breakdown */
    .rsb-bd-h{ display:flex; align-items:center; justify-content:space-between; gap:10px; flex-wrap:wrap; margin-bottom:8px; }
    .rsb-bd-sub{ display:block; font-size:11px; color:#8a93a8; margin-top:2px; }
    .rsb-tabs{ display:inline-flex; background:#f3f5fa; border-radius:9px; padding:3px; gap:2px; }
    .rsb-tab{ border:0; background:transparent; font-size:11px; font-weight:600; color:#6b7689; border-radius:7px; padding:5px 11px; cursor:pointer; }
    .rsb-tab.on{ background:#fff; color:#4f46e5; box-shadow:0 1px 3px rgba(20,38,80,.1); }
    .rsb-tbl-wrap{ overflow-x:auto; }
    .rsb-tbl{ width:100%; border-collapse:collapse; }
    .rsb-tbl td{ font-size:12.5px; color:#3b4a66; padding:9px 12px; border-top:1px solid #f4f6fa; vertical-align:middle; }
    .rsb-tbl td.r{ text-align:right; }
    .rsb-grp-h td{ background:#f9fafd; font-size:11px; font-weight:700; color:#0f1b3d; text-transform:uppercase; letter-spacing:.3px; padding:8px 12px; border-top:1px solid #eef1f7; }
    .rsb-grp-h td:last-child{ color:#16a34a; font-size:12px; }
    .rsb-grp-h .dot{ display:inline-block; width:8px; height:8px; border-radius:50%; margin-right:7px; vertical-align:middle; }
    .rsb-tbl .ev b{ color:#0f1b3d; font-weight:600; } .rsb-tbl .ev span{ display:block; font-size:10.5px; color:#9aa3b2; }
    .rsb-tbl .ppa span{ border-radius:6px; padding:3px 8px; font-size:11px; font-weight:700; }
    .rsb-tbl .ppa .pos{ color:#2b59ff; background:#eef2ff; } .rsb-tbl .ppa .neg{ color:#e5484d; background:#fdecec; }
    .rsb-tbl .oc{ text-align:center; font-weight:600; color:#3b4a66; }
    .rsb-tbl .er{ text-align:right; font-weight:700; }
    .rsb-tbl .er.pos{ color:#16a34a; } .rsb-tbl .er.neg{ color:#e5484d; } .rsb-tbl .er.zero{ color:#9aa3b2; }

    /* side – leaderboard */
    .rsb-side{ display:flex; flex-direction:column; gap:12px; }
    .rsb-lb-list{ display:flex; flex-direction:column; }
    .rsb-lb-list .row{ display:flex; align-items:center; gap:9px; padding:7px 8px; border-radius:9px; }
    .rsb-lb-list .row.me{ background:#eef2ff; }
    .rsb-lb-list .rk{ width:16px; text-align:center; font-size:11px; font-weight:700; color:#8a93a8; flex:none; }
    .rsb-lb-list .av{ width:28px; height:28px; border-radius:50%; display:grid; place-items:center; font-size:10px; font-weight:700; color:#4f46e5; background:#e9ecf6; flex:none; }
    .rsb-lb-list .row.me .av{ background:#4f46e5; color:#fff; }
    .rsb-lb-list .nm{ flex:1; font-size:12px; font-weight:600; color:#1f2a44; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
    .rsb-lb-list .row.me .nm{ color:#4f46e5; }
    .rsb-lb-list .sc{ font-size:12.5px; font-weight:800; color:#0f1b3d; flex:none; }

    /* side – legend */
    .rsb-legend .row{ display:flex; align-items:center; gap:9px; padding:7px 0; border-top:1px solid #f4f6fa; }
    .rsb-legend .row:first-of-type{ border-top:0; }
    .rsb-legend .ic{ width:26px; height:26px; border-radius:8px; display:grid; place-items:center; font-size:14px; flex:none; }
    .rsb-legend .nm{ flex:1; font-size:12px; font-weight:600; color:#1f2a44; }
    .rsb-legend .th{ font-size:10.5px; font-weight:600; color:#8a93a8; }
    .rsb-legend .row.on{ background:#f9fafd; border-radius:8px; padding-left:6px; padding-right:6px; }
    .rsb-legend .row.on .nm{ color:#4f46e5; }
    .rsb-empty{ font-size:11.5px; color:#9aa3b2; margin:4px 0 0; text-align:center; }

    /* team-captain table */
    .rsb-cap{ margin-top:12px; }
    .rsb-pts{ font-size:13px; font-weight:800; } .rsb-pts.pos{ color:#16a34a; } .rsb-pts.neg{ color:#e5484d; }

    /* footer */
    .rsb-foot{ display:flex; align-items:center; justify-content:space-between; gap:12px; flex-wrap:wrap; background:#f7f9fc; border:1px solid #eef1f7; border-radius:14px; padding:12px 16px; margin-top:12px; }
    .rsb-foot .l{ display:flex; align-items:center; gap:8px; font-size:11.5px; color:#8a93a8; font-weight:500; }
    .rsb-foot .l svg{ width:16px; height:16px; }
    .rsb-foot .r{ display:flex; align-items:center; gap:14px; flex-wrap:wrap; }
    .rsb-foot .el{ font-size:11.5px; color:#8a93a8; font-weight:600; display:inline-flex; align-items:center; gap:8px; }
    .rsb-foot .el b{ font-size:11px; border-radius:20px; padding:3px 11px; }
    .rsb-foot .el b.yes{ color:#16a34a; background:#e6f9ee; } .rsb-foot .el b.no{ color:#e5484d; background:#fdecec; }
    .rsb-cta{ display:inline-flex; align-items:center; gap:7px; font-size:12px; font-weight:600; color:#fff; background:linear-gradient(150deg,#4f46e5,#6d4aff); border-radius:9px; padding:9px 16px; text-decoration:none; }
    .rsb-cta svg{ width:15px; height:15px; }
    .rsb-cta:hover{ color:#fff; opacity:.92; }

    /* responsive */
    @media (max-width:1100px){
        .rsb-ov{ grid-template-columns:1fr; }
        .rsb-main{ grid-template-columns:1fr 1fr; }
        .rsb-bd{ grid-column:1 / -1; order:3; }
        .rsb-contrib{ order:1; } .rsb-side{ order:2; }
    }
    @media (max-width:780px){
        .a-rankc,.a-rankg,.a-pct,.a-trend{ grid-column:span 6; }
        .a-ach,.a-ai,.a-mile{ grid-column:span 12; }
        .rsb-main{ grid-template-columns:1fr; }
        .rsb-contrib,.rsb-side,.rsb-bd{ order:0; }
        .rsb-ttl{ font-size:16px; }
    }

    /* ===== modal backdrop: force grey dim when a score-board modal is open ===== */
    html body .modal-backdrop.show{ display:block !important; opacity:.5 !important; background-color:#0b1020 !important; }

    /* ===== Export to PDF (browser print) ===== */
    @media print {
        @page { size: A4; margin: 12mm; }
        body * { visibility: hidden !important; }
        .modal.show, .modal.show *{ visibility: visible !important; }
        .modal.show{ position:absolute !important; left:0 !important; top:0 !important; right:0 !important; width:100% !important; height:auto !important; overflow:visible !important; display:block !important; background:#fff !important; padding:0 !important; }
        .modal.show .modal-dialog{ max-width:100% !important; width:100% !important; margin:0 !important; transform:none !important; }
        .modal.show .modal-content{ border:0 !important; box-shadow:none !important; border-radius:0 !important; }
        .modal.show .modal-body{ overflow:visible !important; height:auto !important; max-height:none !important; padding:0 4px !important; }
        .modal-backdrop{ display:none !important; }
        .rsb-btn, .rsb-x, .rsb-cta, .rsb-tabs{ display:none !important; }
        .rsb-hero, .rsb-medal, .rsb-ai-ic{ -webkit-print-color-adjust:exact !important; print-color-adjust:exact !important; }
        .rsb-ov, .rsb-main, .rsb-card, .rsb-foot, .rsb-cap{ break-inside:avoid; page-break-inside:avoid; }
    }

    /* ===== coins ===== */
    .jw-coin{ display:grid; grid-template-columns:0.85fr 1.15fr; gap:20px; padding:18px 20px; height:100%; align-items:start; }
    @media (max-width:575.98px){ .jw-coin{ grid-template-columns:1fr; } }
    .jw-coin-head{ display:flex; align-items:center; gap:8px; font-size:14px; font-weight:700; color:var(--jw-ink); margin-bottom:14px; }
    .jw-coin-head .jw-coin-ico{ width:26px; height:26px; flex:none; }
    .jw-coin-num{ font-size:25px; font-weight:800; color:var(--jw-ink); letter-spacing:-.5px; line-height:1.1; }
    .jw-coin-csub{ font-size:11px; color:var(--jw-muted); margin:2px 0 16px; }
    .jw-coin-bar{ height:9px; border-radius:7px; background:#eef1f7; overflow:hidden; }
    .jw-coin-bar i{ display:block; height:100%; border-radius:7px; }
    .jw-coin-pct{ font-size:11px; color:var(--jw-muted); margin:6px 0 16px; text-align:right; }
    .jw-coin-btn{ display:inline-block; font-size:12px; font-weight:600; color:#fff!important; border-radius:9px; padding:8px 18px; text-decoration:none; }
    .jw-usage-h{ font-size:13px; font-weight:700; color:var(--jw-ink); margin-bottom:6px; }
    .jw-utbl{ width:100%; border-collapse:collapse; }
    .jw-utbl th{ font-size:10.5px; font-weight:600; color:var(--jw-muted); padding:5px 4px; text-align:right; }
    .jw-utbl th:first-child{ text-align:left; }
    .jw-utbl td{ font-size:12px; color:var(--jw-text); padding:6px 4px; border-top:1px solid #f4f6fa; text-align:right; }
    .jw-utbl td.act{ color:var(--jw-ink); font-weight:500; text-align:left; }
    .jw-utbl td.neg{ color:var(--jw-red); font-weight:600; } .jw-utbl td.pos{ color:var(--jw-green); font-weight:600; }
    .jw-usage-link{ font-size:12px; font-weight:600; color:var(--jw-blue); text-decoration:none; display:inline-block; margin-top:10px; }

    /* ===== client / bench request cards ===== */
    .jw-req{ padding:18px 18px 16px; height:100%; display:flex; flex-direction:column; }
    .jw-req-head{ display:flex; align-items:center; gap:9px; font-size:13px; font-weight:700; color:var(--jw-ink); margin-bottom:16px; }
    .jw-req-ico{ width:34px; height:34px; border-radius:10px; display:grid; place-items:center; flex:none; }
    .jw-req-ico svg{ width:18px; height:18px; }
    .jw-req-num{ font-size:30px; font-weight:800; color:var(--jw-ink); line-height:1; }
    .jw-req-lbl{ font-size:12px; color:var(--jw-text); font-weight:600; margin-top:8px; }
    .jw-req-sub{ font-size:11px; color:var(--jw-muted); margin-top:4px; }
    .jw-req-link{ margin-top:auto; padding-top:14px; font-size:12px; font-weight:600; color:var(--jw-blue); text-decoration:none; }
    .jw-req-link:hover{ text-decoration:underline; }

    /* recent client jobs avatar */
    .jw-jav{ width:30px; height:30px; border-radius:8px; display:grid; place-items:center; font-weight:700; font-size:12px; flex:none; }
    .jw-jcell{ display:flex; align-items:center; gap:10px; }
    .jw-jcell .t{ font-weight:600; color:var(--jw-ink); }
    .jw-jcell .c{ font-size:11px; color:var(--jw-muted); }
    .jw-sub2{ font-size:10.5px; color:var(--jw-muted); }

    /* ===== department donut ===== */
    .jw-dept{ display:grid; grid-template-columns:auto 1fr; gap:24px; align-items:center; padding:10px 20px 14px; }
    @media (max-width:767.98px){ .jw-dept{ grid-template-columns:1fr; } }
    .jw-donut{ position:relative; width:148px; height:148px; margin:0 auto; flex:none; }
    .jw-donut svg{ width:100%; height:100%; transform:rotate(-90deg); }
    .jw-donut .ctr{ position:absolute; inset:0; display:flex; flex-direction:column; align-items:center; justify-content:center; }
    .jw-donut .ctr b{ font-size:19px; font-weight:800; color:var(--jw-ink); }
    .jw-donut .ctr span{ font-size:10px; color:var(--jw-muted); }
    .jw-dept-tbl{ width:100%; border-collapse:collapse; }
    .jw-dept-tbl td{ font-size:12px; padding:3.5px 8px; color:var(--jw-text); white-space:nowrap; }
    .jw-dept-tbl td:nth-child(2),.jw-dept-tbl td:nth-child(3){ text-align:right; font-weight:600; color:var(--jw-ink); }
    .jw-dept-tbl .dot{ width:8px; height:8px; border-radius:50%; display:inline-block; margin-right:8px; }
    .jw-dept-tbl tr.tot td{ border-top:1px solid var(--jw-line); font-weight:700; padding-top:7px; }

    /* footer */
    .jw-footer{ display:flex; align-items:center; justify-content:space-between; padding:20px 6px 10px; font-size:11px; color:var(--jw-muted); flex-wrap:wrap; gap:8px; }
    .jw-footer-links a{ color:var(--jw-muted); text-decoration:none; margin-left:18px; }
    .jw-footer-links a:hover{ color:var(--jw-blue); }

    /* ===== ranked tables (top recruiters / vendors) ===== */
    .jw-rank-tbl th, .jw-rank-tbl td{ padding:9px 8px; }
    .jw-rank-tbl td.jw-rkno{ width:28px; text-align:center; padding-left:0; padding-right:0; }
    .jw-rank-tbl td.jw-rkno b{ display:inline-grid; place-items:center; width:20px; height:20px; border-radius:50%; background:#eef2ff; color:var(--jw-blue); font-size:10.5px; font-weight:700; }
    .jw-rank-tbl .pct{ font-weight:600; color:var(--jw-ink); }
    .jw-rank-tbl .jw-score{ white-space:nowrap; }
  </style>

         {{-- <script src="https://cdn.jsdelivr.net/npm/chart.js"></script> --}}
@endpush
@section('content')
    @php
        use App\Helper\GeneralMethod;
        use Carbon\Carbon;
        $lock_unlock = GeneralMethod::checksubscription();

        $active_count = $counts->get('active', 0);
        $hold_count = $counts->get('hold', 0);
        $inactive_count = $counts->get('inactive', 0);
        $alljobs_count = $active_count + $hold_count + $inactive_count;

         $rankstartOfMonth = Carbon::now()->subMonth()->toDateTimeString();

        $startOfMonth = Carbon::now()->startOfMonth()->toDateTimeString();
        $endOfMonth = Carbon::now()->endOfMonth()->toDateTimeString();

        $startOfMonthForReport = Carbon::now()->startOfMonth()->toDateTimeString();
		$endOfMonthForReport = Carbon::now()->endOfMonth()->toDateTimeString();

        $startOfWeek = Carbon::now()->startOfWeek()->toDateTimeString();
		$endOfWeek = Carbon::now()->endOfWeek()->toDateTimeString();

        $startOfDayForReport = Carbon::now()->startOfDay()->toDateTimeString();
		$endOfDayForReport = Carbon::now()->endOfDay()->toDateTimeString();
        $startOfPreviousDay = Carbon::now()->startOfDay()->toDateTimeString();

        $startOfMonthcomman = Carbon::now()->startOfMonth()->subMonths(5);
		$endOfMonthcomman =  Carbon::now()->endOfMonth();


    @endphp
    <!--begin::Content-->
    <div class="content d-flex flex-column flex-column-fluid pt-5 pb-0 kt_content_job" id="kt_content">
        @if (session('error_token'))
            <div class="alert alert-danger">
                {{ session('error_token') }}
            </div>
        @endif
        @if (session('status_token'))
            <div class="alert alert-success">
                {{ session('status_token') }}
            </div>
        @endif

        {{-- @include('sections.demo-toolbar') --}}
        <div class="post d-flex flex-column-fluid" id="kt_post">
            <div id="kt_content_container" class="container-fluid px-3">

        {{-- ===================== REDESIGNED HERO ===================== --}}
        @php
            $jwCand  = is_array($candidateValues ?? null) ? array_values($candidateValues) : [];
            $jwInt   = is_array($interviewValues ?? null) ? array_values($interviewValues) : [];
            $jwOffer = is_array($candidateValuesCommoffer ?? null) ? array_values($candidateValuesCommoffer) : [];
            $jwJoin  = is_array($candidateValuesCommjoin ?? null) ? array_values($candidateValuesCommjoin) : [];

            $jwLastOf = function ($a) { return (is_array($a) && count($a)) ? (float) $a[count($a) - 1] : 0; };

            // % change of the latest point vs the previous point — computed once from existing arrays (no extra queries)
            $jwDelta = function ($vals) {
                $vals = array_values(array_map('floatval', $vals));
                $n = count($vals);
                if ($n < 2) { return [0, 'flat']; }
                $last = $vals[$n - 1]; $prev = $vals[$n - 2];
                if ($prev == 0) { $pct = $last > 0 ? 100 : 0; }
                else { $pct = (int) round((($last - $prev) / abs($prev)) * 100); }
                return [$pct, $pct > 0 ? 'up' : ($pct < 0 ? 'down' : 'flat')];
            };

            // smooth gradient sparkline (soft area + curved line + dots) — pure client-side SVG, zero server cost.
            // viewBox aspect (~3.3:1) is kept close to the rendered box so dots stay near-round and the line is crisp.
            $jwSparkSvg = function ($vals, $color, $gid) {
                $vals = array_values(array_map('floatval', $vals));
                $n = count($vals);
                if ($n < 2) { $vals = [0, 0]; $n = 2; }
                $max = max($vals); $min = min($vals); $rng = ($max - $min) ?: 1;
                $W = 120; $H = 36; $padX = 4; $top = 7; $bot = 6;
                $co = [];
                for ($i = 0; $i < $n; $i++) {
                    $x = $padX + ($i / ($n - 1)) * ($W - 2 * $padX);
                    $y = ($H - $bot) - (($vals[$i] - $min) / $rng) * ($H - $top - $bot);
                    $co[] = [round($x, 2), round($y, 2)];
                }
                // Catmull-Rom -> cubic bezier => smooth, non-jagged line
                $d = 'M' . $co[0][0] . ',' . $co[0][1];
                for ($i = 0; $i < $n - 1; $i++) {
                    $p0 = $co[max(0, $i - 1)]; $p1 = $co[$i]; $p2 = $co[$i + 1]; $p3 = $co[min($n - 1, $i + 2)];
                    $c1x = round($p1[0] + ($p2[0] - $p0[0]) / 6, 2); $c1y = round($p1[1] + ($p2[1] - $p0[1]) / 6, 2);
                    $c2x = round($p2[0] - ($p3[0] - $p1[0]) / 6, 2); $c2y = round($p2[1] - ($p3[1] - $p1[1]) / 6, 2);
                    $d .= ' C' . $c1x . ',' . $c1y . ' ' . $c2x . ',' . $c2y . ' ' . $p2[0] . ',' . $p2[1];
                }
                $area = $d . ' L' . $co[$n - 1][0] . ',' . $H . ' L' . $co[0][0] . ',' . $H . ' Z';
                $dots = '';
                foreach ($co as $c) { $dots .= '<circle cx="' . $c[0] . '" cy="' . $c[1] . '" r="1.5" fill="' . $color . '"/>'; }
                return '<defs><linearGradient id="' . $gid . '" x1="0" y1="0" x2="0" y2="1">'
                    . '<stop offset="0%" stop-color="' . $color . '" stop-opacity="0.18"/>'
                    . '<stop offset="100%" stop-color="' . $color . '" stop-opacity="0"/></linearGradient></defs>'
                    . '<path d="' . $area . '" fill="url(#' . $gid . ')"/>'
                    . '<path d="' . $d . '" fill="none" stroke="' . $color . '" stroke-width="1.8" stroke-linejoin="round" stroke-linecap="round" vector-effect="non-scaling-stroke"/>'
                    . $dots;
            };

            // U-shaped laurel wreath — almond leaves splayed along a stem arc, open wide at top,
            // nearly closed at bottom. Left branch generated then mirrored for the right.
            $wreath = (function () {
                $cx = 100; $cy = 100; $R = 72; $Rstem = 65;
                $angs = range(263, 117, 11.2);                       // ~14 leaves per branch
                $n = count($angs);
                $leaf = function ($x, $y, $a, $s) {
                    $L = 9.2 * $s; $w = 3.3 * $s;
                    return '<path d="M' . round(-$L, 1) . ' 0 Q0 ' . round(-$w, 1) . ' ' . round($L, 1) . ' 0 Q0 ' . round($w, 1) . ' ' . round(-$L, 1) . ' 0 Z" transform="translate(' . round($x, 1) . ' ' . round($y, 1) . ') rotate(' . round($a, 1) . ')"/>';
                };
                $leaves = ''; $sl = []; $sr = [];
                foreach ($angs as $i => $deg) {
                    $th  = deg2rad($deg);
                    $px  = $cx + $R * cos($th); $py = $cy - $R * sin($th);
                    $tan = rad2deg(atan2(-cos($th), -sin($th)));
                    $lean = $tan - 24;                               // splay leaves outward / upward
                    $s = 1 - 0.03 * abs($i - ($n - 1) / 2);          // taper slightly toward the tips
                    $s = max(0.72, $s);
                    $leaves .= $leaf($px, $py, $lean, $s);                       // left branch
                    $leaves .= $leaf(2 * $cx - $px, $py, 180 - $lean, $s);       // mirrored right branch
                    $sx = $cx + $Rstem * cos($th); $sy = $cy - $Rstem * sin($th);
                    $sl[] = round($sx, 1) . ' ' . round($sy, 1);
                    $sr[] = round(2 * $cx - $sx, 1) . ' ' . round($sy, 1);
                }
                $stems = '<path d="M' . implode(' L', $sl) . '" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>'
                       . '<path d="M' . implode(' L', $sr) . '" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>';
                return $stems . '<g fill="currentColor">' . $leaves . '</g>';
            })();

            $jwOwnCompanyId = auth()->user()->company_id;
            $jwTopCompanies = collect($topUsers_rank_global_company ?? [])->map(function ($c) { return (object) (array) $c; })->take(4);
            $jwMaxScore = (float) ($jwTopCompanies->max(function ($c) { return (float) (@$c->score ?? 0); }) ?: 1);

            $jwRanks = [
                ['My Rank (Internally)',    $ownUser['rank'] ?? '-',                $ownUser['score'] ?? 0,                '#your_rank_company'],
                ['My Rank (Globally)',      $ownUser_global['rank'] ?? '-',         $ownUser_global['score'] ?? 0,         '#your_rank_global'],
                ['Company Rank (Globally)', $ownUser_global_company['rank'] ?? '-', $ownUser_global_company['score'] ?? 0, '#company_rank_global'],
            ];

            // Daily Performance = company-wide TODAY's counts (last point of each company-scoped 7-day daily series)
            $jwOfferD = is_array($offerDailyValues ?? null) ? array_values($offerDailyValues) : [];
            $jwJoinD  = is_array($joinDailyValues ?? null) ? array_values($joinDailyValues) : [];
            $jwKpis = [
                ['Candidate Added', (int) $jwLastOf($jwCand),   '#2b59ff', $jwCand,   'vs yesterday'],
                ['Interviews',      (int) $jwLastOf($jwInt),    '#8b5cf6', $jwInt,     'vs yesterday'],
                ['Offers',          (int) $jwLastOf($jwOfferD), '#ffab00', $jwOfferD,  'vs yesterday'],
                ['Joinings',        (int) $jwLastOf($jwJoinD),  '#22c55e', $jwJoinD,   'vs yesterday'],
            ];
            $jwCgColors = ['#2b59ff', '#8b5cf6', '#3b82f6', '#2b59ff'];
        @endphp

        <div class="row g-3 mb-3">
            {{-- My Ranking Overview --}}
            <div class="col-xl-5 mb-3 mb-xl-0">
                <div class="jw-card-soft h-100">
                    <div class="jw-hd">
                        <div class="ttl"><span class="ic" style="color:#f5a623"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M7 4h10v5a5 5 0 0 1-10 0V4Z"/><path d="M17 5h2.4a1.6 1.6 0 0 1 0 5H17M7 5H4.6a1.6 1.6 0 0 0 0 5H7"/><path d="M12 14v3.2M9 21h6M10.4 18.4h3.2L14 21h-4Z"/></svg></span> My Ranking Overview</div>
                        <div class="jw-hd-right">{{ date('M Y', strtotime($rankstartOfMonth)) }}</div>
                    </div>
                    <div class="jw-rank-row">
                        @foreach ($jwRanks as $r)
                            <div class="jw-rk">
                                <div class="jw-rk-lbl">{{ $r[0] }}</div>
                                <div class="jw-rk-num">
                                    <svg class="jw-rk-wreath" viewBox="0 0 200 200" fill="currentColor" xmlns="http://www.w3.org/2000/svg">{!! $wreath !!}</svg>
                                    <b class="dlen-{{ strlen((string) $r[1]) }}">#{{ $r[1] }}</b>
                                </div>
                                <div class="jw-rk-sub">Score {{ number_format((float) $r[2]) }} pts</div>
                                <button type="button" class="jw-rk-btn" data-bs-toggle="modal" data-bs-target="{{ $r[3] }}">View Score</button>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Daily Performance --}}
            <div class="col-xl-7">
                <div class="jw-card-soft jw-dp-card h-100">
                    <div class="jw-hd">
                        <div class="ttl"><span class="ic" style="color:#2b59ff"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="5" width="18" height="16" rx="2.5"/><path d="M3 9.5h18M8 3v4M16 3v4"/></svg></span> Daily Performance</div>
                        <div class="jw-hd-right"><span class="dt">{{ date('d M Y') }}</span><span class="sel">Today ▾</span></div>
                    </div>
                    <div class="jw-kpi-row">
                        @foreach ($jwKpis as $i => $k)
                            @php [$dp, $dd] = $jwDelta($k[3]); @endphp
                            <div class="jw-kpi">
                                <div class="jw-kpi-t">{{ $k[0] }}</div>
                                <div class="jw-kpi-v" style="color:{{ $k[2] }}">{{ number_format($k[1]) }}</div>
                                <div class="jw-kpi-d {{ $dd }}"><span>{{ $dd === 'up' ? '▲' : ($dd === 'down' ? '▼' : '—') }}</span>{{ abs($dp) }}% {{ $k[4] }}</div>
                                <svg class="jw-spark" viewBox="0 0 120 36" preserveAspectRatio="none">{!! $jwSparkSvg($k[3], $k[2], 'jwsp' . $i) !!}</svg>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        {{-- Company Global Ranking removed per product decision (2026-06-29) — block intentionally deleted. --}}
        {{-- ===================== END REDESIGNED HERO ===================== --}}

        <!--begin::Row OLD ranking (replaced by hero above)-->
            <div class="row gx-5 gx-xl-10 d-none">
                <!--begin::Col-->
                <div class="col-xxl-3 mb-5 mb-xl-3 col-xl-3" style="position: relative">
            
                    <!--begin::List widget 9-->
                    <div class="card card-flush h-xl-100">
                        <!--begin::Header-->
                        <div class="card-header py-7" style="display: block !important;">
                                        
                                <!--begin::Statistics-->
                                <div class="m-0">   
                                    <!--begin::Heading-->
                                    <div class="d-flex align-items-center mb-2">          
                                        <!--begin::Title-->     
                                        <span class="fs-2hx fw-bold text-gray-800 me-2 lh-1 ls-n2">#{{$ownUser['rank']}}</span>
                                        <!--end::Title-->

                                                                <!--begin::Label-->  
                                            <span class="badge badge-light-success fs-base">                                    
                                                Scored   {{$ownUser['score']}}
                                            </span> 
                                            
                                            <!--end::Label-->            
                                                    
                                    </div>
                                    <!--end::Heading-->

                                    <!--begin::Description-->
                                    <span class="fs-6 fw-semibold text-gray-500">Your Rank Internally</span>
                                    <!--end::Description-->
                                </div>
                                <!--end::Statistics-->   
                                 <div class="card-toolbar justify-content-center mt-5">   
                                    <!--begin::Menu-->
                                    <button class="btn btn-color-gray-500 btn-active-color-primary justify-content-end" data-kt-menu-trigger="click" id="your_rank_company_toggle" data-bs-toggle="modal" data-bs-target="#your_rank_company" data-kt-menu-placement="bottom-end" data-kt-menu-overflow="true"  style="border: 1px solid #f5f2f2;">View Score                          
                                    </button>
 
                                    <!--end::Menu-->         
                                </div>           
                            
                        </div>
                        <!--end::Header-->   
                    </div>
                    <!--end::List widget 9-->


                <div style="position: absolute; top: 0px; right: 4px; background: #f3dff4; padding: 4px; border-radius: 5px;"> <span> {{ date("M Y ", strtotime($rankstartOfMonth)) }} </span></div>

                </div>
                <!--end::Col-->  

                <!--begin::Col-->
                <div class="col-xxl-3 mb-5 mb-xl-3 col-xl-3" style="position: relative">
            
                    <!--begin::List widget 9-->
                    <div class="card card-flush h-xl-100">
                        <!--begin::Header-->
                        <div class="card-header py-7" style="display: block !important;">
                                        
                                <!--begin::Statistics-->
                                <div class="m-0">   
                                    <!--begin::Heading-->
                                    <div class="d-flex align-items-center mb-2">          
                                        <!--begin::Title-->     
                                        <span class="fs-2hx fw-bold text-gray-800 me-2 lh-1 ls-n2">#{{$ownUser_global['rank']}}</span>
                                        <!--end::Title-->

                                                                <!--begin::Label-->  
                                            <span class="badge badge-light-success fs-base">                                    
                                                Scored  {{$ownUser_global['score']}}
                                            </span>  
                                            
                                            <!--end::Label-->            
                                                    
                                    </div>
                                    <!--end::Heading-->

                                    <!--begin::Description-->
                                    <span class="fs-6 fw-semibold text-gray-500">Your Rank Globally</span>
                                    <!--end::Description-->
                                </div>
                                <!--end::Statistics-->  
                                <div class="card-toolbar justify-content-center mt-5">   
                                    <!--begin::Menu-->
                                    <button class="btn btn-color-gray-500 btn-active-color-primary justify-content-end" data-kt-menu-trigger="click" id="your_rank_global_toggle" data-bs-toggle="modal" data-bs-target="#your_rank_global" data-kt-menu-placement="bottom-end" data-kt-menu-overflow="true"  style="border: 1px solid #f5f2f2;">View Score                          
                                    </button>
 
                                    <!--end::Menu-->         
                                </div>         
                            
                        </div>
                        <!--end::Header-->        
                    </div>
                    <!--end::List widget 9-->
                    <div style="position: absolute; top: 0px; right: 4px; background: #f3dff4; padding: 4px; border-radius: 5px;"> <span> {{ date("M Y ", strtotime($rankstartOfMonth)) }} </span></div>
                </div>
                <!--end::Col-->  
                <!--begin::Col-->
                <div class="col-xxl-3 mb-5 mb-xl-3 col-xl-3" style="position: relative">
            
                    <!--begin::List widget 9-->
                    <div class="card card-flush h-xl-100">
                        <!--begin::Header-->
                        <div class="card-header py-7" style="display: block !important;">
                                        
                                <!--begin::Statistics-->
                                <div class="m-0">   
                                    <!--begin::Heading-->
                                    <div class="d-flex align-items-center mb-2">          
                                        <!--begin::Title-->     
                                        <span class="fs-2hx fw-bold text-gray-800 me-2 lh-1 ls-n2">#{{$ownUser_global_company['rank']}}</span>
                                        <!--end::Title-->

                                                                <!--begin::Label-->  
                                            <span class="badge badge-light-success fs-base">                                    
                                                Scored   {{$ownUser_global_company['score']}}
                                            </span>  
                                            
                                            <!--end::Label-->            
                                                    
                                    </div>
                                    <!--end::Heading-->

                                    <!--begin::Description-->
                                    <span class="fs-6 fw-semibold text-gray-500">Company Rank Globally </span>
                                    <!--end::Description-->
                                </div>
                                <!--end::Statistics-->   
                                 <div class="card-toolbar justify-content-center mt-5">   
                                    <!--begin::Menu-->
                                    <button class="btn btn-color-gray-500 btn-active-color-primary justify-content-end" data-kt-menu-trigger="click" id="company_rank_global_toggle" data-bs-toggle="modal" data-bs-target="#company_rank_global" data-kt-menu-placement="bottom-end" data-kt-menu-overflow="true"  style="border: 1px solid #f5f2f2;">View Score                          
                                    </button>
 
                                    <!--end::Menu-->         
                                </div>           
                            
                        </div>
                        <!--end::Header-->   
                    </div>
                    <!--end::List widget 9-->
                     <div style="position: absolute; top: 0px; right: 4px; background: #f3dff4; padding: 4px; border-radius: 5px;"> <span> {{ date("M Y ", strtotime($rankstartOfMonth)) }} </span></div>

                </div>
                <!--end::Col-->
                <!--begin::Col-->
                <div class="col-xxl-3 mb-5 mb-xl-3 col-xl-3" style="display: block !important;">
            
                    <!--begin::List widget 9-->
                    <div class="card card-flush h-xl-100">
                        <!--begin::Header-->
                        <div class="card-header py-7" style="display: block !important;">
                                        
                                <!--begin::Statistics-->
                                <div class="m-0">   
                                    <!--begin::Heading-->
                                    <div class="d-flex align-items-center mb-2">          
                                        <!--begin::Title-->     
                                        <span class="fs-2hx fw-bold text-gray-800 me-2 mb-2 lh-1 ls-n2" style="font-size: 18px !important;"> Daily performance </span>
                                        <!--end::Title-->
                                    </div>
                                    <!--end::Heading-->
                                </div>   
                                
                                
                         <!--begin::Labels-->
                                <div class="d-flex flex-column content-justify-center w-100">
                                    <!--begin::Label-->
                                    <div class="d-flex fs-6 fw-semibold align-items-center">
                                        <!--begin::Bullet-->
                                        <div class="bullet w-8px h-6px rounded-2 me-3" style="background-color: #22c55e"></div>
                                        <!--end::Bullet-->

                                        <!--begin::Label-->
                                        <div class="text-gray-500 flex-grow-1 me-4">Score</div>
                                        <!--end::Label-->

                                        <!--begin::Stats-->
                                        <div class="fw-bolder text-gray-700 text-xxl-end">{{$ownUser_daily_internal_rank['score']}}</div>
                                        <!--end::Stats-->
                                    </div>
                                    <!--end::Label-->

                                    <!--begin::Label-->
                                    <div class="d-flex fs-6 fw-semibold align-items-center my-3">
                                        <!--begin::Bullet-->
                                        <div class="bullet w-8px h-6px rounded-2 me-3" style="background-color: #ffab00"></div>
                                        <!--end::Bullet-->

                                        <!--begin::Label-->
                                        <div class="text-gray-500 flex-grow-1 me-4">Internally Rank {{($ownUser_daily_internal_rank['rank']==1) ? '🏆' : ''}}</div>
                                        <!--end::Label-->

                                        <!--begin::Stats-->
                                        <div class="fw-bolder text-gray-700 text-xxl-end">{{$ownUser_daily_internal_rank['rank']}}</div>
                                        <!--end::Stats-->                    
                                    </div>
                                    <!--end::Label-->

                                    <!--begin::Label-->
                                    <div class="d-flex fs-6 fw-semibold align-items-center">
                                        <!--begin::Bullet-->
                                        <div class="bullet w-8px h-6px rounded-2 me-3" style="background-color: #ff0202"></div>
                                        <!--end::Bullet-->

                                        <!--begin::Label-->
                                        <div class="text-gray-500 flex-grow-1 me-4">Globally Rank {{($ownUser_daily_global_rank['rank']==1) ? '🏆' : ''}}</div>
                                        <!--end::Label-->

                                        <!--begin::Stats-->
                                        <div class=" fw-bolder text-gray-700 text-xxl-end">{{$ownUser_daily_global_rank['rank']}}</div>
                                        <!--end::Stats-->                      
                                    </div>
                                    <!--end::Label-->
                                </div>
                        <!--end::Labels-->
                            <div style="position: absolute; top: 0px; right: 4px; background: #f3dff4; padding: 4px; border-radius: 5px;"> <span> {{ date("d M Y") }} </span></div>
                            
                        </div>
                        <!--end::Header-->   
                    </div>
                    <!--end::List widget 9-->

                </div>
                <!--end::Col-->
            </div>
        <!--begin::Row-->



            {{-- ===================== STATS + CHARTS (redesigned, client-side SVG) ===================== --}}
            @php
                $jwAllJobs = (int) ($active_count + $hold_count + $inactive_count);
                [$dCand, $dirCand]   = $jwDelta($jwCand);
                [$dOffer, $dirOffer] = $jwDelta($jwOffer);
                [$dJoin, $dirJoin]   = $jwDelta($jwJoin);

                // candidate pipeline funnel — last 6 months stage counts (no "Added" row)
                $f6 = $funnel6m ?? [];
                $jwFunnel = [
                    ['In Review', (int) ($f6['inreview'] ?? 0),    '#2b59ff'],
                    ['Sourcing',  (int) ($f6['sourcing'] ?? 0),    '#14b8a6'],
                    ['Screening', (int) ($f6['screening'] ?? 0),   '#f59e0b'],
                    ['Evaluation',(int) ($f6['evaluation'] ?? 0),  '#fb923c'],
                    ['Offers',    (int) ($f6['offer'] ?? 0),       '#ec4899'],
                    ['Joined',    (int) ($f6['joined'] ?? 0),      '#8b5cf6'],
                ];
                $jwFunnelMax = max(1, max(array_map(fn ($r) => $r[1], $jwFunnel)));

                // monthly interviews
                $jwIntM = is_array($interviewValuesMonth ?? null) ? array_values($interviewValuesMonth) : [];
                $jwIntL = is_array($interviewlabelsMonth ?? null) ? array_values($interviewlabelsMonth) : [];
                $jwIntTotal = (int) ($interviewValuesTotalMonth ?? array_sum($jwIntM));
                [$dInt, $dirInt] = $jwDelta($jwIntM);

                // ---- SVG line chart (axes + labels) ----
                $jwLineChart = function ($labels, $vals, $color) {
                    $vals = array_values(array_map('floatval', $vals));
                    $labels = array_values($labels);
                    $n = count($vals);
                    if ($n < 2) { return ''; }
                    $W = 360; $H = 230; $mL = 30; $mR = 12; $mT = 18; $mB = 26;
                    $pW = $W - $mL - $mR; $pH = $H - $mT - $mB;
                    $mx = max($vals);
                    $niceMax = $mx <= 30 ? max(10, (int) (ceil($mx / 10) * 10)) : (int) (ceil($mx / 20) * 20);
                    $step = $niceMax <= 30 ? 10 : 20;
                    $steps = max(2, (int) round($niceMax / $step));
                    $grid = ''; $yl = '';
                    for ($s = 0; $s <= $steps; $s++) {
                        $yv = $niceMax / $steps * $s;
                        $y = $mT + $pH - ($yv / $niceMax) * $pH;
                        $grid .= '<line x1="' . $mL . '" y1="' . round($y, 1) . '" x2="' . ($W - $mR) . '" y2="' . round($y, 1) . '" stroke="#eef1f6" stroke-width="1"/>';
                        $yl .= '<text x="' . ($mL - 6) . '" y="' . round($y + 3, 1) . '" text-anchor="end" font-size="9" fill="#9aa3b2">' . (int) $yv . '</text>';
                    }
                    $co = [];
                    for ($i = 0; $i < $n; $i++) {
                        $x = $mL + ($i / ($n - 1)) * $pW;
                        $y = $mT + $pH - ($vals[$i] / $niceMax) * $pH;
                        $co[] = [round($x, 1), round($y, 1)];
                    }
                    $d = 'M' . $co[0][0] . ',' . $co[0][1];
                    for ($i = 0; $i < $n - 1; $i++) {
                        $p0 = $co[max(0, $i - 1)]; $p1 = $co[$i]; $p2 = $co[$i + 1]; $p3 = $co[min($n - 1, $i + 2)];
                        $d .= ' C' . round($p1[0] + ($p2[0] - $p0[0]) / 6, 1) . ',' . round($p1[1] + ($p2[1] - $p0[1]) / 6, 1)
                            . ' ' . round($p2[0] - ($p3[0] - $p1[0]) / 6, 1) . ',' . round($p2[1] - ($p3[1] - $p1[1]) / 6, 1)
                            . ' ' . $p2[0] . ',' . $p2[1];
                    }
                    $area = $d . ' L' . $co[$n - 1][0] . ',' . ($mT + $pH) . ' L' . $co[0][0] . ',' . ($mT + $pH) . ' Z';
                    $dots = ''; $vl = ''; $xl = '';
                    foreach ($co as $i => $c) {
                        $dots .= '<circle cx="' . $c[0] . '" cy="' . $c[1] . '" r="3" fill="#fff" stroke="' . $color . '" stroke-width="2"/>';
                        $vl .= '<text x="' . $c[0] . '" y="' . ($c[1] - 7) . '" text-anchor="middle" font-size="9" font-weight="600" fill="#3b4a66">' . (int) $vals[$i] . '</text>';
                        $xl .= '<text x="' . $c[0] . '" y="' . ($H - 8) . '" text-anchor="middle" font-size="8.5" fill="#9aa3b2">' . e($labels[$i] ?? '') . '</text>';
                    }
                    return '<svg viewBox="0 0 ' . $W . ' ' . $H . '" width="100%" preserveAspectRatio="xMidYMid meet" xmlns="http://www.w3.org/2000/svg">'
                        . '<defs><linearGradient id="jwlc" x1="0" y1="0" x2="0" y2="1"><stop offset="0%" stop-color="' . $color . '" stop-opacity="0.16"/><stop offset="100%" stop-color="' . $color . '" stop-opacity="0"/></linearGradient></defs>'
                        . $grid . '<path d="' . $area . '" fill="url(#jwlc)"/>'
                        . '<path d="' . $d . '" fill="none" stroke="' . $color . '" stroke-width="2" stroke-linejoin="round" stroke-linecap="round"/>'
                        . $dots . $yl . $vl . $xl . '</svg>';
                };

                // ---- SVG vertical bar chart ----
                $jwBarChart = function ($labels, $vals, $color) {
                    $vals = array_values(array_map('floatval', $vals));
                    $labels = array_values($labels);
                    $n = count($vals);
                    if ($n < 1) { return ''; }
                    $W = 300; $H = 210; $mT = 20; $mB = 24; $mL = 6; $mR = 6;
                    $pH = $H - $mT - $mB; $pW = $W - $mL - $mR;
                    $niceMax = max(10, (int) (ceil(max(1, max($vals)) / 10) * 10));
                    $slot = $pW / $n; $bw = $slot * 0.5;
                    $bars = ''; $vl = ''; $xl = '';
                    foreach ($vals as $i => $v) {
                        $bh = ($v / $niceMax) * $pH;
                        $x = $mL + $slot * $i + ($slot - $bw) / 2;
                        $y = $mT + $pH - $bh;
                        $bars .= '<rect x="' . round($x, 1) . '" y="' . round($y, 1) . '" width="' . round($bw, 1) . '" height="' . round($bh, 1) . '" rx="3" fill="' . $color . '"/>';
                        $vl .= '<text x="' . round($x + $bw / 2, 1) . '" y="' . round($y - 5, 1) . '" text-anchor="middle" font-size="9" font-weight="600" fill="#3b4a66">' . (int) $v . '</text>';
                        $xl .= '<text x="' . round($x + $bw / 2, 1) . '" y="' . ($H - 7) . '" text-anchor="middle" font-size="8.5" fill="#9aa3b2">' . e(substr($labels[$i] ?? '', 0, 3)) . '</text>';
                    }
                    return '<svg viewBox="0 0 ' . $W . ' ' . $H . '" width="100%" preserveAspectRatio="xMidYMid meet" xmlns="http://www.w3.org/2000/svg">' . $bars . $vl . $xl . '</svg>';
                };
            @endphp

            {{-- Row: 5 stat cards (requests + candidates / offers / joinings) --}}
            <div class="row g-3 mb-3">
                <div class="col-6 col-md-4 col-xl">
                    <div class="jw-card-soft jw-sc h-100">
                        <div class="jw-sc-head">
                            <span class="jw-sc-ico blue"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="8" r="3"/><path d="M3 20a6 6 0 0 1 12 0M16 5.5a3 3 0 0 1 0 5.5M21 20a6 6 0 0 0-5-5.9"/></svg></span>
                            <div><div class="jw-sc-label">Client Job Requests</div><div class="jw-sc-num">{{ number_format($total_pending ?? 0) }}</div><div class="jw-sc-meta">Pending Requests</div></div>
                        </div>
                        <a href="{{ url('service_company/jobs/job_request/0') }}" class="jw-sc-delta" style="color:var(--jw-blue);text-decoration:none">View All →</a>
                    </div>
                </div>
                <div class="col-6 col-md-4 col-xl">
                    <div class="jw-card-soft jw-sc h-100">
                        <div class="jw-sc-head">
                            <span class="jw-sc-ico" style="background:#e0f7fa;color:#0891b2"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="8" r="3"/><path d="M3 20a6 6 0 0 1 12 0M16 5.5a3 3 0 0 1 0 5.5M21 20a6 6 0 0 0-5-5.9"/></svg></span>
                            <div><div class="jw-sc-label">Bench Talent Requests</div><div class="jw-sc-num">{{ number_format($total_candidate_req ?? 0) }}</div><div class="jw-sc-meta">Pending Requests</div></div>
                        </div>
                        <a href="{{ url('service_company/talent-pool-candidate') }}" class="jw-sc-delta" style="color:var(--jw-blue);text-decoration:none">View All →</a>
                    </div>
                </div>
                <div class="col-6 col-md-4 col-xl">
                    <div class="jw-card-soft jw-sc h-100">
                        <div class="jw-sc-head">
                            <span class="jw-sc-ico violet"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="8" r="3"/><path d="M3 20a6 6 0 0 1 12 0M16 5.5a3 3 0 0 1 0 5.5M21 20a6 6 0 0 0-5-5.9"/></svg></span>
                            <div><div class="jw-sc-label">Candidates Added</div><div class="jw-sc-num">{{ number_format($total_candidates ?? 0) }}</div><div class="jw-sc-meta">This Month</div></div>
                        </div>
                        <div class="jw-sc-delta {{ $dirCand }}"><span>{{ $dirCand === 'up' ? '↑' : ($dirCand === 'down' ? '↓' : '—') }}</span>{{ abs($dCand) }}% vs yesterday</div>
                    </div>
                </div>
                <div class="col-6 col-md-4 col-xl">
                    <div class="jw-card-soft jw-sc h-100">
                        <div class="jw-sc-head">
                            <span class="jw-sc-ico green"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M14 3H7a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V8z"/><path d="M14 3v5h5M9 13h6M9 17h6"/></svg></span>
                            <div><div class="jw-sc-label">Offers <small style="color:#8a93a8;font-weight:500">({{ date('M Y') }})</small></div><div class="jw-sc-num">{{ number_format($offer_count ?? 0) }}</div><div class="jw-sc-meta">Total Offers</div></div>
                        </div>
                        <div class="jw-sc-delta {{ $dirOffer }}"><span>{{ $dirOffer === 'up' ? '↑' : ($dirOffer === 'down' ? '↓' : '—') }}</span>{{ abs($dOffer) }}% vs last month</div>
                    </div>
                </div>
                <div class="col-6 col-md-4 col-xl">
                    <div class="jw-card-soft jw-sc h-100">
                        <div class="jw-sc-head">
                            <span class="jw-sc-ico amber"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="3.2"/><path d="M5 20a7 7 0 0 1 14 0"/></svg></span>
                            <div><div class="jw-sc-label">Joinings <small style="color:#8a93a8;font-weight:500">({{ date('M Y') }})</small></div><div class="jw-sc-num">{{ number_format($joined_count ?? 0) }}</div><div class="jw-sc-meta">Total Joinings</div></div>
                        </div>
                        <div class="jw-sc-delta {{ $dirJoin }}"><span>{{ $dirJoin === 'up' ? '↑' : ($dirJoin === 'down' ? '↓' : '—') }}</span>{{ abs($dJoin) }}% vs last month</div>
                    </div>
                </div>
            </div>

            {{-- Row: 3 charts (trend / funnel / interviews) --}}
            <div class="row g-3 mb-3">
                <div class="col-xl-5 mb-3 mb-xl-0">
                    <div class="jw-card-soft jw-ch h-100">
                        <div class="jw-ch-head">
                            <div class="jw-ch-title">Candidates Added Trend</div>
                            <span class="jw-ch-sel">Last 6 Months ▾</span>
                        </div>
                        <div class="jw-ch-body">{!! $jwLineChart($addedMonthLabels ?? [], $addedMonthValues ?? [], '#2b59ff') !!}</div>
                    </div>
                </div>
                <div class="col-xl-3 mb-3 mb-xl-0">
                    <div class="jw-card-soft jw-ch h-100">
                        <div class="jw-ch-head">
                            <div class="jw-ch-title">Candidates Funnel <small>(Last 6 Months)</small></div>
                        </div>
                        <div class="jw-ch-body">
                            <div class="jw-fn">
                                @foreach ($jwFunnel as $f)
                                    <div class="jw-fn-row">
                                        <span class="lbl">{{ $f[0] }}</span>
                                        <span class="track"><i style="width:{{ max(4, round($f[1] / $jwFunnelMax * 100)) }}%;background:{{ $f[2] }}"></i></span>
                                        <span class="val">{{ number_format($f[1]) }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-4">
                    <div class="jw-card-soft jw-ch h-100">
                        <div class="jw-ch-head">
                            <div class="jw-ch-title">Interviews <small>(Last 6 Months)</small></div>
                        </div>
                        <div>
                            <div class="jw-iv-total">{{ number_format($jwIntTotal) }}</div>
                            <div class="jw-iv-sub">Total Interviews</div>
                            <div class="jw-sc-delta {{ $dirInt }}" style="margin-top:4px"><span>{{ $dirInt === 'up' ? '↑' : ($dirInt === 'down' ? '↓' : '—') }}</span>{{ abs($dInt) }}% vs last month</div>
                        </div>
                        <div class="jw-ch-body">{!! $jwBarChart($jwIntL, $jwIntM, '#2b59ff') !!}</div>
                    </div>
                </div>
            </div>

            <!--begin::Row OLD stat tiles (hidden; existing chart JS still binds these canvases)-->
            <div class="row gx-5 gx-xl-10 mb-xl-10 d-none">
                    <!--begin::Col-->
                    <div class="col-md-6 col-xl-cus-3 col-lg-6 col-xl-3 col-xxl-3 mb-2">
                        
                        <!--begin::Card widget 4-->
                        <div class="card card-flush h-md-50 mb-5 mb-xl-10 jw-stat">
                            <!--begin::Header-->
                            <div class="card-header pt-5 jw-stat-top">
                                <span class="jw-stat-ico blue">💼</span>
                                <!--begin::Title-->
                                <div class="card-title d-flex flex-column">
                                    <!--begin::Info--> 
                                    <div class="d-flex align-items-center">
                                        <!--begin::Currency-->
                                        <span class="fs-4 fw-semibold text-gray-500 me-1 align-self-start"></span>
                                        <!--end::Currency-->

                                        <!--begin::Amount-->
                                        <span class="fs-2hx fw-bold text-gray-900 me-2 lh-1 ls-n2">{{ $inactive_count+$active_count+$hold_count }}</span>
                                        <!--end::Amount-->

                                        <!--begin::Badge-->
                                        {{-- <span class="badge badge-light-success fs-base">                                
                                            <i class="ki-outline ki-arrow-up fs-5 text-success ms-n1"></i> 
                                            2.2%
                                        </span>             --}}
                                        <!--end::Badge--> 
                                    </div>
                                    <!--end::Info--> 

                                    <!--begin::Subtitle-->
                                    <span class="text-gray-500 pt-1 fw-semibold fs-6">All Jobs</span>
                                    <!--end::Subtitle--> 
                                </div>
                                <!--end::Title-->      
                            </div>
                            <!--end::Header-->

                            <!--begin::Card body-->
                            <div class="card-body pt-2 pb-4 d-flex align-items-center">
                                <!--begin::Chart-->
                                <div class="d-flex flex-center me-5 pt-2">
                                    <div 
                                        id="kt_card_widget_4_chart" 
                                        style="min-width: 70px; min-height: 70px" 
                                        data-kt-size="70" 
                                        data-kt-line="11">
                                        <span></span>
                                        <canvas id="allJobGrapg" height="70" width="70"></canvas>
                                    </div>
                                </div>
                                <!--end::Chart-->
                                
                                <!--begin::Labels-->
                                <div class="d-flex flex-column content-justify-center w-100">
                                    <!--begin::Label-->
                                    <div class="d-flex fs-6 fw-semibold align-items-center">
                                        <!--begin::Bullet-->
                                        <div class="bullet w-8px h-6px rounded-2 me-3" style="background-color: #22c55e"></div>
                                        <!--end::Bullet-->

                                        <!--begin::Label-->
                                        <div class="text-gray-500 flex-grow-1 me-4">Active Jobs</div>
                                        <!--end::Label-->

                                        <!--begin::Stats-->
                                        <div class="fw-bolder text-gray-700 text-xxl-end">{{ $active_count }}</div>
                                        <!--end::Stats-->
                                    </div>
                                    <!--end::Label-->

                                    <!--begin::Label-->
                                    <div class="d-flex fs-6 fw-semibold align-items-center my-3">
                                        <!--begin::Bullet-->
                                        <div class="bullet w-8px h-6px rounded-2 me-3" style="background-color: #ffab00"></div>
                                        <!--end::Bullet-->

                                        <!--begin::Label-->
                                        <div class="text-gray-500 flex-grow-1 me-4">Hold Jobs</div>
                                        <!--end::Label-->

                                        <!--begin::Stats-->
                                        <div class="fw-bolder text-gray-700 text-xxl-end">{{ $hold_count }}</div>
                                        <!--end::Stats-->                    
                                    </div>
                                    <!--end::Label-->

                                    <!--begin::Label-->
                                    <div class="d-flex fs-6 fw-semibold align-items-center">
                                        <!--begin::Bullet-->
                                        <div class="bullet w-8px h-6px rounded-2 me-3" style="background-color: #ff0202"></div>
                                        <!--end::Bullet-->

                                        <!--begin::Label-->
                                        <div class="text-gray-500 flex-grow-1 me-4">Closed Jobs</div>
                                        <!--end::Label-->

                                        <!--begin::Stats-->
                                        <div class=" fw-bolder text-gray-700 text-xxl-end">{{ $inactive_count }}</div>
                                        <!--end::Stats-->                      
                                    </div>
                                    <!--end::Label-->
                                </div>
                                <!--end::Labels-->
                            </div>
                            <!--end::Card body-->
                        </div>
                        <!--end::Card widget 4--> 

                        <!--begin::Card widget 5-->
                        <div class="card card-flush h-md-50 mb-xl-10 jw-stat">
                            <!--begin::Header-->
                            <div class="card-header pt-5 jw-stat-top">
                                <span class="jw-stat-ico green">📄</span>
                                <!--begin::Title-->
                                <div class="card-title d-flex flex-column">
                                    <!--begin::Info-->
                                    <div class="d-flex align-items-center">
                                        <!--begin::Amount-->
                                        <span class="fs-2hx fw-bold text-gray-900 me-2 lh-1 ls-n2">{{$candidateValuesTotaloffer}}</span>
                                        <!--end::Amount-->
                                        
                                        <!--begin::Badge-->
                                        {{-- <span class="badge badge-light-danger fs-base">                                
                                            <i class="ki-outline ki-arrow-down fs-5 text-danger ms-n1"></i> 
                                            2.2%
                                        </span>             --}}
                                        <!--end::Badge-->                
                                    </div>
                                    <!--end::Info--> 

                                    <!--begin::Subtitle-->
                                    <span class="text-gray-500 pt-1 fw-semibold fs-6">Offers In {{ date("M Y ", strtotime($startOfMonthcomman)) }} To {{ date("M Y ", strtotime($endOfMonthcomman)) }}</span>             
                                    <!--end::Subtitle--> 
                                </div>
                                <!--end::Title-->         
                            </div>
                            <!--end::Header-->

                            <!--begin::Card body-->
                            <div class="card-body d-flex align-items-end pt-0">
                                <!--begin::Progress-->
                                

                                <div class="d-flex align-items-center flex-column mt-3 w-100">
                                    <div 
                                        id="kt_card_widget_4_chart" 
                                        style="min-width: 100%; min-height: 200px">
                                        <canvas id="candidates_offer_chart" height="170"></canvas>
                                    </div>
                                </div>
                                <!--end::Progress-->
                            </div>
                            <!--end::Card body-->
                        </div>
                        <!--end::Card widget 5-->    
                    </div>
                    <!--end::Col-->

                    <!--begin::Col-->
                    <div class="col-md-6 col-xl-cus-3 col-lg-6 col-xl-3 col-xxl-3 mb-2">
                        <!--begin::Card widget 6-->
                        <div class="card card-flush  h-md-50 mb-5 mb-xl-10 jw-stat">
                            <!--begin::Header-->
                            <div class="card-header pt-5 jw-stat-top">
                                <span class="jw-stat-ico violet">👥</span>
                                <!--begin::Title-->
                                <div class="card-title d-flex flex-column">
                                    <!--begin::Info-->
                                    <div class="d-flex align-items-center">
                                        <!--begin::Amount-->
                                        <span class="fs-2hx fw-bold text-gray-900 me-2 lh-1 ls-n2">{{$candidateValuesTotal}}</span>
                                        <!--end::Amount-->

                                        <!--begin::Badge-->
                                        {{-- <span class="badge badge-light-success fs-base">                                
                                            <i class="ki-outline ki-arrow-up fs-5 text-success ms-n1"></i> 
                                            2.6%
                                        </span>             --}}
                                        <!--end::Badge--> 
                                    </div>
                                    <!--end::Info--> 

                                    <!--begin::Subtitle-->
                                    <span class="text-gray-500 pt-1 fw-semibold fs-6">Candidates added in last 7 days</span>
                                    <!--end::Subtitle--> 
                                </div>
                                <!--end::Title-->           
                            </div>
                            <!--end::Header-->

                            <!--begin::Card body-->
                            <div class="card-body d-flex align-items-end px-3 pb-10">
                                <!--begin::Chart-->
                                <div id="kt_card_widget_6_chart" class="w-100" style="height: 200px">
                                    <div 
                                        id="kt_card_widget_4_chart" 
                                        style="min-width: 100%; min-height: 200px">
                                        <canvas id="candidates_left_chart" height="170"></canvas>
                                    </div>
                                </div>
                                <!--end::Chart-->
                            </div>
                            <!--end::Card body-->
                        </div>
                        <!--end::Card widget 6--> 

                        <!--begin::Card widget 7-->
                        <div class="card card-flush h-md-50 mb-xl-10 jw-stat">
                            <!--begin::Header-->
                            <div class="card-header pt-5 jw-stat-top">
                                <span class="jw-stat-ico amber">🧑</span>
                                <!--begin::Title-->
                                <div class="card-title d-flex flex-column">
                                    <!--begin::Amount-->
                                    <span class="fs-2hx fw-bold text-gray-900 me-2 lh-1 ls-n2">{{$candidateValuesTotaljoin}}</span>
                                    <!--end::Amount-->           

                                    <!--begin::Subtitle-->
                                    <span class="text-gray-500 pt-1 fw-semibold fs-6">Joined In {{ date("M Y ", strtotime($startOfMonthcomman)) }} To {{ date("M Y ", strtotime($endOfMonthcomman)) }}</span>
                                    <!--end::Subtitle--> 
                                </div>
                                <!--end::Title-->           
                            </div>
                            <!--end::Header-->

                            <!--begin::Card body-->
                            <div class="card-body d-flex flex-column justify-content-end pe-0">
                                <!--begin::Title-->
                                <!--end::Title-->

                                <!--begin::Users group-->
                                <div class="symbol-group symbol-hover flex-nowrap">

                                    <div 
                                        id="kt_card_widget_4_chart" 
                                        style="min-width: 100%; min-height: 200px">
                                        <canvas id="candidates_join_chart" height="170"></canvas>
                                    </div>


                                           
                                </div>
                                <!--end::Users group-->
                            </div>
                            <!--end::Card body-->
                        </div>
                        <!--end::Card widget 7-->    
                    </div>
                    <!--end::Col-->

                    <!--begin::Col-->
                    <div class="col-lg-12 col-xl-cus-6 col-xl-6 col-xxl-6 mb-5 mb-xl-0">
                        <!--begin::Chart widget 3-->
                        <div class="card card-flush overflow-hidden h-md-100">
                            <!--begin::Header-->
                            <div class="card-header py-5">
                                <!--begin::Title-->
                                <h3 class="card-title align-items-start flex-column">   
                                    <span class="fs-2hx fw-bold text-gray-900 me-2 lh-1 ls-n2">{{$total_candidates}}</span>         
                                    <span class="text-gray-500 pt-1 fw-semibold fs-6">Candidates added In {{ date("M Y ", strtotime($startOfMonthForReport)) }}</span>
                                    {{-- <span class="text-gray-500 mt-1 fw-semibold fs-6">Users from all channels</span> --}}
                                </h3>
                                <!--end::Title-->
                            </div>
                            <!--end::Header-->

                            <!--begin::Card body-->
                            <div class="card-body d-flex justify-content-between flex-column pb-1 px-0" style="position: relative">  
                                <!--begin::Chart-->
                                {{-- <div style="position: absolute; bottom: 50px; width: 222px; height: 53px; background: #ffffff; z-index: 99;"> </div> --}}
                                <div id="kt_charts_widget_3" class="min-h-auto ps-4 pe-6" style="height: 300px">

                                    <canvas id="myChart" height="300"></canvas>

                                    
        
                                     {{-- <div id="chart-container" style="width: 600px; height: 420px; margin: auto;"></div> --}}
                                </div>
                                <!--end::Chart--> 
                            </div>
                            <!--end::Card body-->
                        </div>
                        <!--end::Chart widget 3-->    
                    </div>
                    
                    <!--end::Col--> 
            </div>
            <!--begin::Row-->

            {{-- ===================== TRENDS + TABLES (redesigned, client-side) ===================== --}}
            @php
                $jwAcceptOffers = (float) ($candidateValuesTotaloffer ?? 0);
                $jwAcceptJoins  = (float) ($candidateValuesTotaljoin ?? 0);
                $jwAcceptRate   = $jwAcceptOffers > 0 ? round($jwAcceptJoins / $jwAcceptOffers * 100, 1) : 0;
                $jwOfferLabels  = is_array($labelsCommoffer ?? null) ? array_values($labelsCommoffer) : [];
                $jwJoinLabels   = is_array($labelsCommjoin ?? null) ? array_values($labelsCommjoin) : [];

                // recruiters from the ranking data already computed for the page (no query)
                $jwRecruiters    = collect($topUsers_rank ?? [])->map(fn ($u) => (array) $u)->values();
                $jwRecruiterAvg  = $jwRecruiters->count() ? $jwRecruiters->avg(fn ($u) => (float) ($u['score'] ?? 0)) : 0;
                $jwOwnUserId     = auth()->user()->id;
                $jwAvPalette     = ['#2b59ff', '#8b5cf6', '#14b8a6', '#f59e0b', '#ec4899'];

                // 4 rows: if I'm in the top 3 -> show ranks 1-4; otherwise show ranks 1-3 + my own rank row
                $jwMyRow  = $jwRecruiters->firstWhere('id', $jwOwnUserId);
                $jwMyRank = $jwMyRow['rank'] ?? null;
                if ($jwMyRank !== null && $jwMyRank > 3) {
                    $jwRecruiterRows = $jwRecruiters->sortBy('rank')->take(3)->push($jwMyRow)->values();
                } else {
                    $jwRecruiterRows = $jwRecruiters->sortBy('rank')->take(4)->values();
                }

                // top 4 vendors (staffing partners) — from data already computed for the page (no query)
                $jwVL = is_array($staffingMonthlyLabels ?? null) ? array_values($staffingMonthlyLabels) : [];
                $jwVV = is_array($staffingMonthlyValues ?? null) ? array_values($staffingMonthlyValues) : [];
                $jwVendors = [];
                foreach ($jwVL as $i => $vn) { $jwVendors[] = ['name' => $vn, 'joinings' => (int) ($jwVV[$i] ?? 0)]; }
                $jwVendors = array_slice($jwVendors, 0, 4);
                $jwVendorTotal = max(1, array_sum(array_map(fn ($v) => $v['joinings'], $jwVendors)));
                $jwRankMonth = date('M Y', strtotime($rankstartOfMonth ?? 'now'));

                // top captains (captain-only score) — already computed in the controller (no query)
                $jwCaptains = collect($topUsersCaptain ?? [])->map(fn ($u) => (array) $u)->values();
            @endphp

            {{-- Row: offers trend / acceptance rate / joinings trend --}}
            <div class="row g-3 mb-3">
                <div class="col-xl-4 mb-3 mb-xl-0">
                    <div class="jw-card-soft jw-ch h-100">
                        <div class="jw-ch-head"><div class="jw-ch-title">Offers Trend <small>(Last 6 Months)</small></div></div>
                        <div class="jw-ch-body">{!! $jwLineChart($jwOfferLabels, $jwOffer, '#ffab00') !!}</div>
                    </div>
                </div>
                <div class="col-xl-4 mb-3 mb-xl-0">
                    <div class="jw-card-soft h-100">
                        <div class="jw-acc">
                            <div class="jw-ch-title">Offer Acceptance Rate <small>(Last 6 Months)</small></div>
                            <div class="jw-acc-desc">Offer → Joining conversion over the last 6 months</div>
                            <div class="jw-acc-body">
                                <div class="jw-acc-pct">{{ $jwAcceptRate }}%</div>
                                <div class="jw-acc-sub">Joinings {{ number_format($jwAcceptJoins) }} / Offers {{ number_format($jwAcceptOffers) }}</div>
                                <div class="jw-acc-bar"><i style="width:{{ min(100, $jwAcceptRate) }}%"></i></div>
                                <div class="jw-acc-foot">{{ number_format($jwAcceptJoins) }} of {{ number_format($jwAcceptOffers) }} offers converted to joinings</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-4">
                    <div class="jw-card-soft jw-ch h-100">
                        <div class="jw-ch-head"><div class="jw-ch-title">Joinings Trend <small>(Last 6 Months)</small></div></div>
                        <div class="jw-ch-body">{!! $jwLineChart($jwJoinLabels, $jwJoin, '#22c55e') !!}</div>
                    </div>
                </div>
            </div>

            {{-- ===================== MONTHLY PERFORMERS ===================== --}}
            @php
                // JOIN / O→J% are derived from the gamification points stored in ELASTIC_TEAM_RANK by
                // dividing each points field by its per-event weight (set in ServiceCompanyJobApplicationController).
                // Recruiter weights: candidateJoined = 1500 pts/join, offerReleased = 300 pts/offer.
                $recRows = collect($jwRecruiters)->map(function ($u) use ($jwOwnUserId) {
                    $u = (array) $u; $jn = (int) round(((float) ($u['candidateJoined'] ?? 0)) / 1500); $of = ((float) ($u['offerReleased'] ?? 0)) / 300;
                    return ['name' => ucwords(strtolower($u['name'] ?? 'Unknown')), 'rank' => (int) ($u['rank'] ?? 0), 'big' => (int) ($u['score'] ?? 0), 'j' => $jn, 'c' => $of > 0 ? min(100, (int) round($jn / $of * 100)) : 0, 'is_me' => (($u['id'] ?? null) == $jwOwnUserId)];
                })->sortBy('rank')->values();
                // Captain weights: teamCaptainCandidateJoined = 30 pts/join, teamCaptainOfferReleased = 20 pts/offer.
                $capRows = collect($jwCaptains)->map(function ($u, $i) use ($jwOwnUserId) {
                    $u = (array) $u; $jn = (int) round(((float) ($u['teamCaptainCandidateJoined'] ?? 0)) / 30); $of = ((float) ($u['teamCaptainOfferReleased'] ?? 0)) / 20;
                    return ['name' => ucwords(strtolower($u['name'] ?? 'Unknown')), 'rank' => $i + 1, 'big' => (int) ($u['captainScore'] ?? 0), 'j' => $jn, 'c' => $of > 0 ? min(100, (int) round($jn / $of * 100)) : 0, 'is_me' => (($u['id'] ?? null) == $jwOwnUserId)];
                })->values();
                $venTopJ = collect($jwVendors)->max(fn ($v) => (int) (((array) $v)['joinings'] ?? 0)) ?: 0;
                $venRows = collect($jwVendors)->map(function ($v, $i) use ($jwVendorTotal) {
                    $v = (array) $v; $jng = (int) ($v['joinings'] ?? 0);
                    return ['name' => ucwords(strtolower($v['name'] ?? 'Unknown')), 'rank' => $i + 1, 'big' => $jng, 'j' => $jng, 'c' => $jwVendorTotal > 0 ? min(100, (int) round($jng / $jwVendorTotal * 100)) : 0, 'is_me' => false];
                })->values();

                $mpTrophy = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M7 4h10v5a5 5 0 0 1-10 0V4Z"/><path d="M17 5h2.4a1.6 1.6 0 0 1 0 5H17M7 5H4.6a1.6 1.6 0 0 0 0 5H7"/><path d="M12 14v3.2M9 21h6M10.4 18.4h3.2L14 21h-4Z"/></svg>';
                $mpIcon = function ($k) use ($mpTrophy) {
                    if ($k === 'crown') return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M3 8l4 3 5-6 5 6 4-3-2 11H5L3 8Z"/></svg>';
                    if ($k === 'building') return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><rect x="4" y="3" width="10" height="18" rx="1.5"/><path d="M14 8h5a1 1 0 0 1 1 1v12M7 7h3M7 11h3M7 15h3"/></svg>';
                    if ($k === 'trophy') return $mpTrophy;
                    return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="8" r="3"/><path d="M3 20a6 6 0 0 1 12 0M16 5.5a3 3 0 0 1 0 5.5M21 20a6 6 0 0 0-5-5.9"/></svg>';
                };
                $rmeta = $recruiterMeta ?? []; $cmeta = $captainMeta ?? []; $vmeta = $vendorMeta ?? [];
                $jwPerf = [
                    ['title' => 'Top Performing Recruiters', 'icon' => 'team', 'topicon' => 'trophy', 'color' => '#2b59ff', 'bg' => '#eef2ff', 'hasScore' => true, 'nmcol' => 'Recruiter', 'jlbl' => 'Join', 'clbl' => 'O→J %', 'blbl' => 'Score', 'rows' => $recRows,
                        'stats' => [['Total Joins', number_format($rmeta['joins'] ?? 0)], ['Avg O→J %', ($rmeta['conv'] ?? 0) . '%'], ['Total Score', number_format($rmeta['score'] ?? 0)]]],
                    ['title' => 'Top Performing Captains', 'icon' => 'crown', 'topicon' => 'crown', 'color' => '#7c3aed', 'bg' => '#f3ecff', 'hasScore' => true, 'nmcol' => 'Captain', 'jlbl' => 'Join', 'clbl' => 'O→J %', 'blbl' => 'Score', 'rows' => $capRows,
                        'stats' => [['Total Joins', number_format($cmeta['joins'] ?? 0)], ['Avg O→J %', ($cmeta['conv'] ?? 0) . '%'], ['Total Score', number_format($cmeta['score'] ?? 0)]]],
                    ['title' => 'Top Performing Vendors', 'icon' => 'building', 'topicon' => 'building', 'color' => '#16a34a', 'bg' => '#e6f7ec', 'hasScore' => false, 'nmcol' => 'Vendor', 'jlbl' => 'Joinings', 'clbl' => 'Share', 'blbl' => 'Joinings', 'rows' => $venRows,
                        'stats' => [['Total Joinings', number_format($vmeta['joinings'] ?? 0)], ['Vendors', number_format($vmeta['count'] ?? 0)], ['Top Joins', number_format($venTopJ)]]],
                ];
            @endphp

            <div class="jw-lb mb-3">
                <div class="jw-lb-head">
                    <div class="jw-lb-title">
                        <span class="ic">{!! $mpTrophy !!}</span>
                        <div>
                            <h3>Monthly Performers</h3>
                            <p>Recognizing top performers across Recruiters, Captains &amp; Vendors</p>
                        </div>
                    </div>
                    <div class="jw-lb-meta">
                        <span class="datepill"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="5" width="18" height="16" rx="2.5"/><path d="M3 9.5h18M8 3v4M16 3v4"/></svg> {{ $jwRankMonth }}</span>
                    </div>
                </div>
                <div class="row g-3">
                    @foreach ($jwPerf as $lb)
                        @php
                            $rows = collect($lb['rows']);
                            $podium = $rows->take(3)->values();
                            $tableRows = $rows->slice(3)->values();
                            $p1 = $podium[0] ?? null; $p2 = $podium[1] ?? null; $p3 = $podium[2] ?? null;
                            $podOrdered = [['p2', $p2], ['p1', $p1], ['p3', $p3]];
                        @endphp
                        <div class="col-xl-4 mb-3 mb-xl-0">
                            <div class="jw-mp-card h-100 {{ $lock_unlock ? '' : 'card-xxl-stretch-lock' }}">
                                <div class="jw-mp-ch">
                                    <span class="ic" style="background:{{ $lb['bg'] }};color:{{ $lb['color'] }}">{!! $mpIcon($lb['icon']) !!}</span>
                                    <span class="ttl" style="color:{{ $lb['color'] }}">{{ $lb['title'] }}</span>
                                    <a href="javascript:void(0)" class="arr">→</a>
                                </div>
                                <table class="jw-mp-tbl">
                                    <thead><tr><th>{{ $lb['nmcol'] }}</th><th>{{ $lb['jlbl'] }}</th><th>{{ $lb['clbl'] }}</th>@if ($lb['hasScore'])<th>{{ $lb['blbl'] }}</th>@endif<th>Rank</th></tr></thead>
                                    <tbody>
                                        @forelse ($rows as $r)
                                            @php $av = $jwAvPalette[$loop->index % count($jwAvPalette)]; $medal = $r['rank'] == 1 ? 'g1' : ($r['rank'] == 2 ? 'g2' : ($r['rank'] == 3 ? 'g3' : 'gn')); @endphp
                                            <tr class="{{ ($r['is_me'] ?? false) ? 'me' : '' }}">
                                                <td><span class="rc"><span class="av" style="background:{{ $av }}">{{ strtoupper(mb_substr($r['name'], 0, 1)) }}</span><span class="nm">{{ \Illuminate\Support\Str::limit($r['name'], 14) }}@if ($r['is_me'] ?? false)<span class="youtag">YOU</span>@endif</span></span></td>
                                                <td>{{ number_format($r['j']) }}</td>
                                                <td class="pct">{{ $r['c'] }}%</td>
                                                @if ($lb['hasScore'])<td class="sc">{{ number_format($r['big']) }}</td>@endif
                                                <td class="rank" style="color:{{ $lb['color'] }}">#{{ $r['rank'] }}</td>
                                            </tr>
                                        @empty
                                            <tr><td colspan="5" style="text-align:center;color:#9aa3b2;padding:18px">No data</td></tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!--Start Interview Section (removed; hidden so existing chart JS still binds canvases) -->
                <div class="row gy-5 g-xl-10 d-none">
                    <!--begin::Col-->
                    <div class="col-xl-4 mb-xl-10">
                        
                        <!--begin::List widget 17-->
                        <div class="card card-flush h-xl-100">
                            <!--begin::Header-->
                            <div class="card-header pt-7">
                                <!--begin::Title-->
                                <h3 class="card-title align-items-start flex-column">
                                    <span class="card-label fw-bold text-gray-800">Upcoming Interviews</span>
                                </h3>
                                <!--end::Title-->
                                <!--end::Toolbar-->
                            </div>
                            <!--end::Header-->

                            <!--begin::Body-->
                            <div class="card-body pt-0"> 
                                
                                    <!--begin::Content-->                                  
                                    <div class="d-flex flex-stack my-5">
                                        <span class="text-gray-500 fs-7 fw-bold"></span>
                                        
                                        <span class="text-gray-500 fw-bold fs-7"></span>
                                    </div>                   
                                    <!--end::Content-->

                                    @if (count($upcomingData) > 0)
                                        @foreach ($upcomingData as $rowArr)

                                            @php
                                            $row = (object) $rowArr;
                                             $schedule_date = Carbon::parse($row->schedule_date)->format('M d');
                                             $schedule_date_explode = explode(" ",$schedule_date);
                                            @endphp
                                
                                            <!--begin::Item-->
                                            <div class="d-flex flex-stack">                 
                                                <!--begin::Wrapper-->                                  
                                                <div class="d-flex align-items-center me-3">
                                                    <!--begin::Icon-->                         
                                                    <a href="#" class="btn btn-sm btn-icon btn-bg-light btn-active-color-primary w-40px h-40px mr-2 text-center" style="display: inline-grid">
                                                      <span style="font-size: 12px; font-weight: 600;">{{$schedule_date_explode[0]}}</span><span style="color: #373bed; font-size: 17px; font-weight: 900;">{{$schedule_date_explode[1]}}</span>                
                                                    </a>                 
                                                    <!--end::Icon-->

                                                    <!--begin::Section-->                                  
                                                    <div class="flex-grow-1">
                                                        <a href="javascript:;" class="text-gray-800 text-hover-primary fs-5 fw-bold lh-0">{{ucwords(mb_substr(@$row->full_name, 0, 20))}}</a>
                                                        
                                                        <span class="text-gray-500 fw-semibold d-block fs-7">{{ucwords(mb_substr(@$row->current_designation, 0, 21))}}</span>
                                                    </div>                   
                                                    <!--end::Section-->
                                                </div>                   
                                                <!--end::Wrapper-->                 

                                                <!--begin::Value-->
                                                <span class="text-gray-800 fw-bold fs-6">
                                                    {{$row->schedule_to_time. ($row->timezone_abbreviation ? ' (' . $row->timezone_abbreviation . ')' : '')}} <br>
                                                    <span class="text-gray-500 fw-semibold d-block fs-7">
                                                    {{ucwords(mb_substr($row->mode_name, 0, 13))}} 
                                                    <span> 
                                                </span>
                                                <!--end::Value-->               
                                            </div>
                                            <!--end::Item-->

                                            <!--begin::Separator-->
                                            <div class="separator separator-dashed my-4"></div>
                                            <!--end::Separator-->
                                        @endforeach
                                    @else
                                        <table border="1" width="100%" cellpadding="10">
                                            <tbody>
                                                    <tr>
                                                        <td colspan="4" align="center">No records found</td>
                                                    </tr>
                                            </tbody>
                                        </table>
                                    @endif                                   
                                
                            </div>
                            <!--end::Body-->
                        </div>
                        <!--end::List widget 17-->     
                    </div>
                    <!--end::Col-->      

                    <!--begin::Col-->
                    <div class="col-xl-8 mb-5 mb-xl-10">
                        
                        <!--begin::Table widget 6-->
                        <div class="card card-flush h-xl-100">
                            <!--begin::Header-->
                            <div class="card-header pt-7" style="position: relative">
                                <!--begin::Title-->
                                <h3 class="card-title align-items-start flex-column">			
                                    <span class="card-label fw-bold text-gray-800">Candidates Interviews</span>
                                </h3>
                                <!--end::Title-->
                                 <span class="" style="position: absolute; right: 14px; top: 26px;"> 
                                   <select class="form-select form-select-lg form-select-solid" data-hide-search="true" id="sortingfilter" data-dropdown-css-class="w-150px" data-placeholder="Select an option" style="border: 1px solid #ffffff;" onchange="filtersdatagetmonth(this.value)">
                                        <option value="1">Last 6 Months</option>
                                        <option value="0">Last 7 Days</option>
                                    </select>
                                </span>
                                
                            </div>
                            <!--end::Header-->

                            <!--begin::Body-->
                            <div class="card-body">       
                                <!--begin::Tab Content-->
                                <div class="tab-content">
                                        <!--begin::Tap pane-->
                                        <div class="tab-pane fade active show" id="kt_stats_widget_6_tab_1" role="tabpanel">
                                            <!--begin::Table container-->

                                            <div id="kt_card_widget_6_chart" class="w-100" style="height: 200px">

                                                <div class="inetview_recent_chart d-none" id="kt_card_widget_4_chart" 
                                                    style="min-width: 100%; min-height: 200px">
                                                    <canvas id="inetview_recent_chart" height="300"></canvas>
                                                </div>
                                                
                                                <div class="inetview_recent_chart_month" id="kt_card_widget_4_chart" 
                                                        style="min-width: 100%; min-height: 200px">
                                                        <canvas id="inetview_recent_chart_month" height="230"></canvas>
                                                </div>

                                            </div>

                                                   
                                            <!--end::Table-->
                                        </div>
                                        <!--end::Tap pane-->
                                </div>
                                <!--end::Tab Content-->        
                            </div>
                            <!--end: Card Body-->
                        </div>
                        <!--end::Table widget 6-->   
                    </div>
                    <!--end::Col-->      
                </div>
            <!--End Interview Section-->

            {{-- ===================== TEAM/VENDOR REPORTS + COINS (redesigned, client-side) ===================== --}}
            @php
                // Daily Leaderboards — structured rows (top 5 + me) + per-group meta, all built in the controller (no query)
                $jwLeaderboards = [
                    ['title' => 'Teams Daily Report',    'col' => 'Team Member', 'unit' => 'Members',  'rows' => collect($teamDailyReport ?? []),    'meta' => $teamDailyMeta ?? [],    'color' => '#2b59ff', 'bg' => '#eef2ff', 'icon' => 'team'],
                    ['title' => 'Captains Daily Report', 'col' => 'Captain',     'unit' => 'Captains', 'rows' => collect($captainDailyReport ?? []), 'meta' => $captainDailyMeta ?? [], 'color' => '#7c3aed', 'bg' => '#f3ecff', 'icon' => 'crown'],
                    ['title' => 'Agency Daily Report',   'col' => 'Agency',      'unit' => 'Agencies', 'rows' => collect($agencyDailyReport ?? []),  'meta' => $agencyDailyMeta ?? [],  'color' => '#16a34a', 'bg' => '#e6f7ec', 'icon' => 'building'],
                ];
                $lbAvSets = [['#e9f0ff','#2b59ff'],['#e6f9ee','#16a34a'],['#fff3e2','#f08c00'],['#fdecef','#ec4899'],['#f1ecff','#7c5cff'],['#e0f7fa','#0891b2']];
                $lbAvatar = function ($name) use ($lbAvSets) { return $lbAvSets[ord(strtoupper(substr(trim($name) . 'A', 0, 1))) % count($lbAvSets)]; };
                $lbIcon = function ($k) {
                    if ($k === 'crown') return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M3 8l4 3 5-6 5 6 4-3-2 11H5L3 8Z"/></svg>';
                    if ($k === 'building') return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><rect x="4" y="3" width="10" height="18" rx="1.5"/><path d="M14 8h5a1 1 0 0 1 1 1v12M7 7h3M7 11h3M7 15h3"/></svg>';
                    return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="8" r="3"/><path d="M3 20a6 6 0 0 1 12 0M16 5.5a3 3 0 0 1 0 5.5M21 20a6 6 0 0 0-5-5.9"/></svg>';
                };
                $jwCoinFmt = fn ($v) => rtrim(rtrim(number_format((float) $v, 2), '0'), '.');
                $opPct    = ($actual_count ?? 0) > 0 ? min(100, max(0, (int) round(($remain_coins / $actual_count) * 100))) : (($remain_coins ?? 0) > 0 ? 100 : 0);
                $salesPct = ($actual_count_sales ?? 0) > 0 ? min(100, max(0, (int) round(($remain_coins_sales / $actual_count_sales) * 100))) : (($remain_coins_sales ?? 0) > 0 ? 100 : 0);
                // materialise the existing latest-5 log cursors once (no new queries)
                $jwOpLogs    = collect($coin_transection_logs ?? []);
                $jwSalesLogs = collect($coin_transection_sales_logs ?? []);
            @endphp

            {{-- ===================== DAILY LEADERBOARDS ===================== --}}
            <div class="jw-lb mb-3">
                <div class="jw-lb-head">
                    <div class="jw-lb-title">
                        <span class="ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M7 4h10v5a5 5 0 0 1-10 0V4Z"/><path d="M17 5h2.4a1.6 1.6 0 0 1 0 5H17M7 5H4.6a1.6 1.6 0 0 0 0 5H7"/><path d="M12 14v3.2M9 21h6M10.4 18.4h3.2L14 21h-4Z"/></svg></span>
                        <div>
                            <h3>Daily Leaderboards</h3>
                            <p>Track top performers across Teams, Captains &amp; Agencies</p>
                        </div>
                    </div>
                    <div class="jw-lb-meta">
                        <span class="upd"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2" stroke-linecap="round"/></svg> Updated {{ now()->format('d M, h:i A') }}</span>
                        <a href="{{ request()->fullUrlWithQuery(['getTopFive' => time()]) }}" class="refresh" title="Refresh"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M20 11a8 8 0 1 0-1 4M20 5v6h-6"/></svg></a>
                        <span class="datepill"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="5" width="18" height="16" rx="2.5"/><path d="M3 9.5h18M8 3v4M16 3v4"/></svg> {{ date('d M Y') }}</span>
                    </div>
                </div>
                <div class="row g-3">
                    @foreach ($jwLeaderboards as $lb)
                        @php
                            $rows  = collect($lb['rows'])->map(fn ($r) => (array) $r);
                            $rmax  = $rows->count() ? max(1, $rows->max(fn ($r) => (float) ($r['value'] ?? 0))) : 1;
                            $top   = $rows->first(fn ($r) => (int) ($r['value'] ?? 0) > 0);
                            $total = (int) ($lb['meta']['total'] ?? 0);
                            $count = (int) ($lb['meta']['count'] ?? $rows->count());
                        @endphp
                        <div class="col-xl-4 mb-3 mb-xl-0">
                            <div class="jw-lb-card h-100 {{ $lock_unlock ? '' : 'card-xxl-stretch-lock' }}">
                                <div class="jw-lb-card-head">
                                    <span class="ic" style="background:{{ $lb['bg'] }};color:{{ $lb['color'] }}">{!! $lbIcon($lb['icon']) !!}</span>
                                    <span class="ttl" style="color:{{ $lb['color'] }}">{{ $lb['title'] }}</span>
                                    <span class="cnt"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="8" r="3"/><path d="M3 20a6 6 0 0 1 12 0M16 5.5a3 3 0 0 1 0 5.5M21 20a6 6 0 0 0-5-5.9"/></svg> {{ $count }} {{ $lb['unit'] }}</span>
                                </div>
                                <div class="jw-lb-sum">
                                    <div class="s-score"><div class="l">Score</div><div class="v" style="color:{{ $lb['color'] }}">{{ number_format($top ? (int) ($top['value'] ?? 0) : 0) }}</div></div>
                                    <div class="s-top">
                                        @if ($top)
                                            <div class="l">Top Performer</div>
                                            <div class="tp"><span class="medal">🥇</span><span class="tpn">{{ \Illuminate\Support\Str::limit($top['name'] ?? '', 14) }}</span></div>
                                        @else
                                            <div class="noleader" style="background:{{ $lb['bg'] }};color:{{ $lb['color'] }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3l2.6 5.3 5.9.9-4.3 4.1 1 5.8L12 17.8 6.8 19l1-5.8L3.5 9.2l5.9-.9L12 3Z"/></svg> No Leader Yet</div>
                                        @endif
                                    </div>
                                </div>
                                <div class="jw-lb-th"><span class="c-nm">{{ $lb['col'] }}</span><span class="c-sc">Score</span><span class="c-rank">Rank</span></div>
                                @forelse ($rows as $r)
                                    @php
                                        $rk = (int) ($r['rank'] ?? $loop->iteration);
                                        $val = (int) ($r['value'] ?? 0);
                                        $me = $r['is_me'] ?? false;
                                        $av = $lbAvatar($r['name'] ?? '?');
                                        $medal = $rk == 1 ? 'g1' : ($rk == 2 ? 'g2' : ($rk == 3 ? 'g3' : 'gn'));
                                    @endphp
                                    <div class="jw-lb-row {{ $me ? 'me' : '' }}">
                                        <span class="c-nm"><span class="av" style="background:{{ $av[0] }};color:{{ $av[1] }}">{{ strtoupper(mb_substr($r['name'] ?? '?', 0, 1)) }}</span><span class="nm">{{ \Illuminate\Support\Str::limit($r['name'] ?? '', 13) }}</span>@if ($me)<span class="youtag">YOU</span>@endif</span>
                                        <span class="c-sc"><span class="bar"><i style="width:{{ max(5, round($val / $rmax * 100)) }}%;background:{{ $lb['color'] }}"></i></span><b>{{ $val }}</b></span>
                                        <span class="c-rank" style="color:{{ $lb['color'] }}">#{{ $rk }}</span>
                                    </div>
                                @empty
                                    <div class="jw-lb-empty">No records found</div>
                                @endforelse
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Row: Operational + Sales coins --}}
            <div class="row g-3 mb-3">
                @php
                    $jwCoinCards = [
                        ['Operational Coins', $remain_coins ?? 0, $opPct, '#5b3df5', $jwOpLogs, 'Operational Coin Usage', url('service_company/subscribe') . '?type=operational'],
                        ['Sales Coins', $remain_coins_sales ?? 0, $salesPct, '#16a34a', $jwSalesLogs, 'Sales Coin Usage', url('service_company/subscribe') . '?type=sales'],
                    ];
                @endphp
                @foreach ($jwCoinCards as $cc)
                    <div class="col-xl-6 mb-3 mb-xl-0">
                        <div class="jw-card-soft jw-coin h-100">
                            <div>
                                <div class="jw-coin-head" style="color:{{ $cc[3] }}">
                                    <svg class="jw-coin-ico" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><circle cx="12" cy="12" r="9"/><path d="M12 7v10M14.6 9.2c0-1-1.2-1.7-2.6-1.7s-2.6.8-2.6 1.8 1.2 1.6 2.6 1.8 2.6.8 2.6 1.9-1.2 1.7-2.6 1.7-2.6-.7-2.6-1.7" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                    <span style="color:var(--jw-ink)">{{ $cc[0] }}</span>
                                </div>
                                <div class="jw-coin-num">{{ $jwCoinFmt($cc[1]) }}</div>
                                <div class="jw-coin-csub">Total Remaining Coins</div>
                                <div class="jw-coin-bar"><i style="width:{{ $cc[2] }}%;background:{{ $cc[3] }}"></i></div>
                                <div class="jw-coin-pct">{{ $cc[2] }}% Remaining</div>
                                <a href="{{ route('service_company.subscribe.newindex') }}" class="jw-coin-btn" style="background:{{ $cc[3] }}">Upgrade Coins</a>
                            </div>
                            <div>
                                <div class="jw-usage-h">{{ $cc[5] }} <span style="color:var(--jw-muted);font-weight:500;font-size:11px">(Recent Activity)</span></div>
                                <table class="jw-utbl">
                                    <thead><tr><th>Activity</th><th>Coins</th><th>Date</th></tr></thead>
                                    <tbody>
                                        @forelse ($cc[4] as $t)
                                            @php $isCredit = strtolower($t->transection_type ?? '') === 'credit'; @endphp
                                            <tr>
                                                <td class="act">{{ \Illuminate\Support\Str::limit($t->event ?? '—', 18) }}</td>
                                                <td class="{{ $isCredit ? 'pos' : 'neg' }}">{{ $isCredit ? '+' : '-' }}{{ $jwCoinFmt($t->coins ?? 0) }}</td>
                                                <td>{{ \Carbon\Carbon::parse($t->created_at)->format('d M') }}</td>
                                            </tr>
                                        @empty
                                            <tr><td class="act" colspan="3" style="color:var(--jw-muted)">No recent activity</td></tr>
                                        @endforelse
                                    </tbody>
                                </table>
                                <a href="{{ $cc[6] }}" class="jw-usage-link">View Full Usage →</a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- old coins + teams/vendor (hidden; existing chart canvases still bound by JS) --}}
            <div class="d-none">
            <!--Start Subscription Section-->
                <div class="row gy-5 g-xl-10">
                    <!--begin::Col-->
                    <div class="col-xl-4 mb-xl-10">

                        <div class="card card-flush h-xl-100">
                            <!--begin::Card header-->
                            <div class="card-header pt-7">
                                <!--begin::Title-->
                                <h3 class="card-title align-items-start flex-column">
                                    <span class="card-label fw-bold text-gray-800">Operational Coins</span>
                                    <span class="text-gray-500 mt-1 fw-semibold fs-6"></span>
                                </h3>
                                <!--end::Title-->
                            </div>
                            <!--end::Card header-->
                            <!--begin::Body-->
                            <div class="card-body pt-2">
                                
                                <!--begin::Chart-->
                                <div class="d-flex flex-center w-100" style="position: relative"> 
                                    <canvas id="gaugeChart" height="50" width="50" style="display: block; box-sizing: border-box; height: 50px; width: 50px;"></canvas> 

                                    <div style="position: absolute; top: 172px; text-align: center;">
                                         <text x="150" y="145" text-anchor="middle" dominant-baseline="auto" font-size="12px" font-family="inherit" font-weight="700" fill="#99a1b7" class="apexcharts-text apexcharts-datalabel-label" style="font-family: inherit;">Total Remain Coins</text><br>
                                                        <text x="150" y="126" text-anchor="middle" dominant-baseline="auto" font-size="24px" font-family="inherit" font-weight="600" fill="#071437" class="apexcharts-text apexcharts-datalabel-value" style="font-family: inherit;">{{$remain_coins}}</text>
                                    </div>

                                </div>
                                <!--end::Chart-->         

                                <!--begin::Content-->
                                <div class="text-center w-100 position-relative" style="margin-top: -30px">      
                                
                                    <!--begin::Action-->
                                    <div class="mb-1 mb-xxl-1">
                                        <a href="{{ route('service_company.subscribe.newindex') }}" class="btn fw-semibold" style="background:#122ef5;color: #fff;">Upgrade</a>
                                    </div>
                                    <!--ed::Action-->
                                </div>
                                <!--end::Content-->
                            </div>
                            <!--end::Body-->                            
                        </div>
                        
                    </div>    
                    <!--end::Col-->

                    <!--begin::Col-->
                    <div class="col-xl-8 mb-5 mb-xl-10">
                                
                        <!--begin::Table Widget 4-->
                        <div class="card card-flush h-xl-100">
                            <!--begin::Card header-->
                            <div class="card-header pt-7" style="position: relative">
                                <!--begin::Title-->
                                <h3 class="card-title align-items-start flex-column">
                                    <span class="card-label fw-bold text-gray-800">Recent Coin Deductions</span>
                                    <span class="text-gray-500 mt-1 fw-semibold fs-6"></span>
                                </h3>
                                <!--end::Title-->
                                <span class="" style="position: absolute; right: 14px; top: 26px;"> 
                                    <button class="btn btn-color-gray-500 btn-active-color-primary justify-content-end" style="border: 1px solid #f5f2f2;"><a href="{{ url('service_company/subscribe') }}?type=operational" class="text-gray-800 mb-1"> Default Coin Deductions</a>                        
                                    </button> 
                                </span>
                            </div>
                            <!--end::Card header-->

                            <!--begin::Card body-->
                            <div class="card-body pt-2">
                                <!--begin::Table-->
                                <div id="kt_table_widget_4_table_wrapper" class="dt-container dt-bootstrap5 dt-empty-footer">
                                    <div id="" class="table-responsive">
                                        <table class="table align-middle table-row-dashed fs-6 gy-3 dataTable" id="kt_table_widget_4_table" style="width: 100%;">
                                            <!--begin::Table head-->
                                            <thead>
                                                <!--begin::Table row-->
                                                <tr class="text-start text-gray-500 fw-bold fs-7 text-uppercase gs-0">
                                                    <th class="min-w-100px dt-orderable-none" data-dt-column="0" rowspan="1" colspan="1">
                                                        <span class="dt-column-title">Date</span>
                                                        <span class="dt-column-order"></span>
                                                    </th>
                                                    <th class="min-w-100px dt-orderable-none" data-dt-column="1" rowspan="1" colspan="1">
                                                        <span class="dt-column-title">Activity</span>
                                                        <span class="dt-column-order"></span>
                                                    </th>
                                                    <th class="min-w-125px dt-orderable-none" data-dt-column="2" rowspan="1" colspan="1">
                                                        <span class="dt-column-title">Source</span>
                                                        <span class="dt-column-order"></span>
                                                    </th>
                                                    <th class="min-w-100px dt-orderable-none dt-type-numeric" data-dt-column="3" rowspan="1" colspan="1">
                                                        <span class="dt-column-title">Coins</span>
                                                        <span class="dt-column-order"></span>
                                                    </th>
                                                    <th class="min-w-100px dt-orderable-none dt-type-numeric" data-dt-column="4" rowspan="1" colspan="1">
                                                        <span class="dt-column-title">Balance</span>
                                                        <span class="dt-column-order"></span>
                                                    </th>
                                                </tr>
                                                <!--end::Table row-->
                                            </thead>
                                            <!--end::Table head-->

                                            <!--begin::Table body-->
                                                <tbody class="fw-bold text-gray-600">
                                                    @if(@$coin_transection_logs)
                                                        @foreach($coin_transection_logs as $transection_log)
                                                            <tr>
                                                                <td>
                                                                    <span class="text-dark fw-bolder fs-6">
                                                                        {{ Carbon::parse($transection_log->created_at)->format('d M, Y') }}
                                                                    </span>
                                                                    <span class="text-muted fw-bold d-block fs-7"> 
                                                                        {{ Carbon::parse($transection_log->created_at)->format('h:i:A') }}
                                                                    </span>
                                                                </td>
                                                                
                                                                <td class="">
                                                                    <span class="text-dark fw-bolder fs-6">
                                                                        {{ $transection_log->event }}  
                                                                    </span> 
                                                                <span class="fw-bold text-muted d-block fs-7">
                                                                    {{ ucwords($transection_log->event_type) }}
                                                                </span>                       
                                                                </td>
                                                                
                                                                <td class="">
                                                                    <span class="text-dark fw-bolder fs-6">
                                                                        {{ ($transection_log->event_by !=1) ? $transection_log->name : 'Free' }}
                                                                    </span>
                                                                    <span class="text-muted fw-bold d-block fs-7">
                                                                        {{ ($transection_log->event_by !=1) ? $transection_log->company_name : 'Default' }} 
                                                                    </span>
                                                                </td>
                                                                
                                                                <td class="dt-type-numeric">
                                                                    @if($transection_log->settlement_coins <= 0)
                                                                    <span class="text-dark fw-bolder  fs-6"> 
                                                                        @if($transection_log->transection_type==='credit')
                                                                        {{ ($transection_log->actual_coins) ? $transection_log->actual_coins : $transection_log->coins }} Coins
                                                                        @else
                                                                        {{ $transection_log->coins }} Coins
                                                                        @endif
                                                                    </span>
                                                                    <span class="fw-bold text-muted d-block fs-7"> 
                                                                        {{ ucwords($transection_log->transection_type) }}
                                                                    </span>      
                                                                    @else
                                                                        <span class="fw-bold text-muted d-block fs-7">Settlement</span>
                                                                            <span class="text-dark fw-bolder  fs-6">
                                                                                {{ $transection_log->settlement_coins }} Coins
                                                                            </span>
                                                                            <span class="fw-bold text-muted d-block fs-7"> 
                                                                                {{ ucwords($transection_log->transection_type) }}
                                                                            </span>
                                                                    @endif             
                                                                </td>
                                                                
                                                                <td class="dt-type-numeric">
                                                                    <span class="text-dark fw-bolder  fs-6">{{ ($transection_log->remain_coins) ? $transection_log->remain_coins : $transection_log->coins }} Coins</span>
                                                                    <span
                                                                        class="fw-bold text-muted d-block fs-7">
                                                                        Balance
                                                                    </span>
                                                                </td>
                                                                
                                                            </tr>
                                                        @endforeach
                                                    @endif
                                                </tbody>
                                            <!--end::Table body-->
                                        <tfoot></tfoot>
                                        </table>
                                    </div>
                                </div>
                                <!--end::Table-->
                            </div>
                            <!--end::Card body-->
                        </div>
                        <!--end::Table Widget 4-->    
                    </div>
                    <!--end::Col-->   
                </div>
            <!--End Subscription Section-->

            <!--Start Subscription Section-->
                <div class="row gy-5 g-xl-10">
                    <!--begin::Col-->
                    <div class="col-xl-4 mb-xl-10">

                        <div class="card card-flush h-xl-100">
                            <!--begin::Card header-->
                            <div class="card-header pt-7">
                                <!--begin::Title-->
                                <h3 class="card-title align-items-start flex-column">
                                    <span class="card-label fw-bold text-gray-800">Sales Coins</span>
                                    <span class="text-gray-500 mt-1 fw-semibold fs-6"></span>
                                </h3>
                                <!--end::Title-->
                            </div>
                            <!--end::Card header-->
                            <!--begin::Body-->
                            <div class="card-body pt-2">
                                
                                <!--begin::Chart-->
                                <div class="d-flex flex-center w-100" style="position: relative"> 
                                    <canvas id="gaugeChartSales" height="50" width="50" style="display: block; box-sizing: border-box; height: 50px; width: 50px;"></canvas> 

                                    <div style="position: absolute; top: 172px; text-align: center;">
                                         <text x="150" y="145" text-anchor="middle" dominant-baseline="auto" font-size="12px" font-family="inherit" font-weight="700" fill="#99a1b7" class="apexcharts-text apexcharts-datalabel-label" style="font-family: inherit;">Total Remain Coins</text><br>
                                                        <text x="150" y="126" text-anchor="middle" dominant-baseline="auto" font-size="24px" font-family="inherit" font-weight="600" fill="#071437" class="apexcharts-text apexcharts-datalabel-value" style="font-family: inherit;">{{$remain_coins_sales}}</text>
                                    </div>

                                </div>
                                <!--end::Chart-->         

                                <!--begin::Content-->
                                <div class="text-center w-100 position-relative" style="margin-top: -30px">      
                                
                                    <!--begin::Action-->
                                    <div class="mb-1 mb-xxl-1">
                                        <a href="{{ route('service_company.subscribe.newindex') }}" class="btn fw-semibold" style="background:#24ef21;color: #fff;">Upgrade</a>
                                    </div>
                                    <!--ed::Action-->
                                </div>
                                <!--end::Content-->
                            </div>
                            <!--end::Body-->                            
                        </div>
                        
                    </div>    
                    <!--end::Col-->

                    <!--begin::Col-->
                    <div class="col-xl-8 mb-5 mb-xl-10">
                                
                        <!--begin::Table Widget 4-->
                        <div class="card card-flush h-xl-100">
                            <!--begin::Card header-->
                            <div class="card-header pt-7" style="position: relative">
                                <!--begin::Title-->
                                <h3 class="card-title align-items-start flex-column">
                                    <span class="card-label fw-bold text-gray-800">Recent Coin Deductions</span>
                                    <span class="text-gray-500 mt-1 fw-semibold fs-6"></span>
                                </h3>
                                <!--end::Title-->

                                <span class="" style="position: absolute; right: 14px; top: 26px;"> 
                                    <button class="btn btn-color-gray-500 btn-active-color-primary justify-content-end" style="border: 1px solid #f5f2f2;"><a href="{{ url('service_company/subscribe') }}?type=sales" class="text-gray-800 mb-1">Default Coin Deductions</a>                       
                                    </button> 
                                </span>
                            </div>
                            <!--end::Card header-->

                            <!--begin::Card body-->
                            <div class="card-body pt-2">
                                <!--begin::Table-->
                                <div id="kt_table_widget_4_table_wrapper" class="dt-container dt-bootstrap5 dt-empty-footer">
                                    <div id="" class="table-responsive">
                                        <table class="table align-middle table-row-dashed fs-6 gy-3 dataTable" id="kt_table_widget_4_table" style="width: 100%;">
                                            <!--begin::Table head-->
                                            <thead>
                                                <!--begin::Table row-->
                                                <tr class="text-start text-gray-500 fw-bold fs-7 text-uppercase gs-0">
                                                    <th class="min-w-100px dt-orderable-none" data-dt-column="0" rowspan="1" colspan="1">
                                                        <span class="dt-column-title">Date</span>
                                                        <span class="dt-column-order"></span>
                                                    </th>
                                                    <th class="min-w-100px dt-orderable-none" data-dt-column="1" rowspan="1" colspan="1">
                                                        <span class="dt-column-title">Activity</span>
                                                        <span class="dt-column-order"></span>
                                                    </th>
                                                    <th class="min-w-125px dt-orderable-none" data-dt-column="2" rowspan="1" colspan="1">
                                                        <span class="dt-column-title">Source</span>
                                                        <span class="dt-column-order"></span>
                                                    </th>
                                                    <th class="min-w-100px dt-orderable-none dt-type-numeric" data-dt-column="3" rowspan="1" colspan="1">
                                                        <span class="dt-column-title">Coins</span>
                                                        <span class="dt-column-order"></span>
                                                    </th>
                                                    <th class="min-w-100px dt-orderable-none dt-type-numeric" data-dt-column="4" rowspan="1" colspan="1">
                                                        <span class="dt-column-title">Balance</span>
                                                        <span class="dt-column-order"></span>
                                                    </th>
                                                </tr>
                                                <!--end::Table row-->
                                            </thead>
                                            <!--end::Table head-->

                                            <!--begin::Table body-->
                                                <tbody class="fw-bold text-gray-600">
                                                    @if(@$coin_transection_sales_logs)
                                                        @foreach($coin_transection_sales_logs as $transection_log)
                                                            <tr>
                                                                <td>
                                                                    <span class="text-dark fw-bolder fs-6">
                                                                        {{ Carbon::parse($transection_log->created_at)->format('d M, Y') }}
                                                                    </span>
                                                                    <span class="text-muted fw-bold d-block fs-7"> 
                                                                        {{ Carbon::parse($transection_log->created_at)->format('h:i:A') }}
                                                                    </span>
                                                                </td>
                                                                
                                                                <td class="">
                                                                    <span class="text-dark fw-bolder fs-6">
                                                                        {{ $transection_log->event }}  
                                                                    </span> 
                                                                <span class="fw-bold text-muted d-block fs-7">
                                                                    {{ ucwords($transection_log->event_type) }}
                                                                </span>                       
                                                                </td>
                                                                
                                                                <td class="">
                                                                    <span class="text-dark fw-bolder fs-6">
                                                                        {{ ($transection_log->event_by != 1) ? $transection_log->name : 'Free' }}
                                                                    </span>
                                                                    <span class="text-muted fw-bold d-block fs-7">
                                                                        {{ ($transection_log->event_by !=1) ? $transection_log->company_name : 'Default' }} 
                                                                    </span>
                                                                </td>
                                                                
                                                                <td class="dt-type-numeric">
                                                                    @if($transection_log->settlement_coins <= 0)
                                                                    <span class="text-dark fw-bolder  fs-6"> 
                                                                        @if($transection_log->transection_type==='credit')
                                                                        {{ ($transection_log->actual_coins) ? $transection_log->actual_coins : $transection_log->coins }} Coins
                                                                        @else
                                                                        {{ $transection_log->coins }} Coins
                                                                        @endif
                                                                    </span>
                                                                    <span class="fw-bold text-muted d-block fs-7"> 
                                                                        {{ ucwords($transection_log->transection_type) }}
                                                                    </span>      
                                                                    @else
                                                                        <span class="fw-bold text-muted d-block fs-7">Settlement</span>
                                                                            <span class="text-dark fw-bolder  fs-6">
                                                                                {{ $transection_log->settlement_coins }} Coins
                                                                            </span>
                                                                            <span class="fw-bold text-muted d-block fs-7"> 
                                                                                {{ ucwords($transection_log->transection_type) }}
                                                                            </span>
                                                                    @endif             
                                                                </td>
                                                                
                                                                <td class="dt-type-numeric">
                                                                    <span class="text-dark fw-bolder  fs-6">{{ ($transection_log->remain_coins) ? $transection_log->remain_coins : $transection_log->coins }} Coins</span>
                                                                    <span
                                                                        class="fw-bold text-muted d-block fs-7">
                                                                        Balance
                                                                    </span>
                                                                </td>
                                                                
                                                            </tr>
                                                        @endforeach
                                                    @endif
                                                </tbody>
                                            <!--end::Table body-->
                                        <tfoot></tfoot>
                                        </table>
                                    </div>
                                </div>
                                <!--end::Table-->
                            </div>
                            <!--end::Card body-->
                        </div>
                        <!--end::Table Widget 4-->    
                    </div>
                    <!--end::Col-->   
                </div>
            <!--End Subscription Section-->





                <div class="row" style="margin-bottom: 0.5rem;">
               
                <!--begin::Col-->
                    <div class="col-xxl-6 col-xl-6 col-lg-12 col-md-12 col-sm-12 col-sm-12 pb-0 mt-0">
                        <div class="card card-xxl-stretch {{ $lock_unlock ? '' : 'card-xxl-stretch-lock' }}">
                           
                            <div class="card-header pt-5" style="border: none;">
                                <!--begin::Title-->
                                <div class="card-title d-flex flex-column">
                                    <!--begin::Info-->
                                    <div class="d-flex align-items-center">
                                       <!--begin::Amount-->
                                        <h3 class="card-label">Teams Monthly Joining Report</h3>
                                        <!--end::Amount-->
                                    </div>
                                    <!--end::Info-->
                                    <!--begin::Subtitle-->
                                    <span class="text-gray-500 pt-1 fw-semibold fs-6">{{ date("M Y ", strtotime($startOfMonthForReport)) }}</span>
                                    <!--end::Subtitle-->
                                </div>
                                <!--end::Title-->
                            </div>

                            
                            <div class="card-body py-3 mb-4">
                                <div class="tab-content">
                                    <div class="tab-pane fade show active kt_table_widget_5_tab">
                                        <div class="table-responsive">
                                            <div id="" class="w-100" style="height: 200px">
                                                <div 
                                                    id="kt_card_widget_4_chart" 
                                                    style="min-width: 100%; min-height: 200px">
                                                    <canvas id="team_left_chart" height="170"></canvas>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <!--end::Col-->

                <!--begin::Col-->
                    <div class="col-xxl-6 col-xl-6 col-lg-12 col-md-12 col-sm-12 col-sm-12 pb-0 mt-0">
                        <div class="card card-xxl-stretch {{ $lock_unlock ? '' : 'card-xxl-stretch-lock' }}">
                            
                            <div class="card-header pt-5" style="border: none;">
                                <!--begin::Title-->
                                <div class="card-title d-flex flex-column">
                                    <!--begin::Info-->
                                    <div class="d-flex align-items-center">
                                       <!--begin::Amount-->
                                        <h3 class="card-label">Teams Daily Report</h3>
                                        <!--end::Amount-->
                                    </div>
                                    <!--end::Info-->
                                    <!--begin::Subtitle-->
                                    <span class="text-gray-500 pt-1 fw-semibold fs-6">{{ date("d M Y", strtotime($startOfPreviousDay)) }} </span>
                                    <!--end::Subtitle-->
                                </div>
                                <!--end::Title-->
                            </div>

                            <div class="card-body py-3 mb-4">
                                <div class="tab-content">
                                    <div class="tab-pane fade show active kt_table_widget_5_tab">
                                        <div class="table-responsive">
                                             <div id="" class="w-100" style="height: 200px">
                                                <div 
                                                    id="kt_card_widget_4_chart" 
                                                    style="min-width: 100%; min-height: 200px">
                                                    <canvas id="team_right_chart" height="170"></canvas>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <!--end::Col-->
                </div>


                <div class="row" style="margin-bottom: 0.5rem;">
                    <!--begin::Col-->
                        <div class="col-xxl-6 col-xl-6 col-lg-12 col-md-12 col-sm-12 col-sm-12 pb-0 mt-0">
                            <div class="card card-xxl-stretch {{ $lock_unlock ? '' : 'card-xxl-stretch-lock' }}">
                               
                                <div class="card-header pt-5" style="border: none;">
                                    <!--begin::Title-->
                                    <div class="card-title d-flex flex-column">
                                        <!--begin::Info-->
                                        <div class="d-flex align-items-center">
                                           <!--begin::Amount-->
                                            <h3 class="card-label"> Vendor Monthly Joining Report </h3>
                                            <!--end::Amount-->
                                        </div>
                                        <!--end::Info-->
                                        <!--begin::Subtitle-->
                                        <span class="text-gray-500 pt-1 fw-semibold fs-6">{{ date("M Y ", strtotime($startOfMonthForReport)) }}</span>
                                        <!--end::Subtitle-->
                                    </div>
                                    <!--end::Title-->
                                </div>

                                <div class="card-body py-3 mb-4">
                                    <div class="tab-content">
                                        <div class="tab-pane fade show active kt_table_widget_5_tab">
                                            <div class="table-responsive">
                                                <div id="" class="w-100" style="height: 200px">
                                                    <div 
                                                        id="kt_card_widget_4_chart" 
                                                        style="min-width: 100%; min-height: 200px">
                                                        <canvas id="vendor_left_chart" height="170"></canvas>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <!--end::Col-->
    
                    <!--begin::Col-->
                        <div class="col-xxl-6 col-xl-6 col-lg-12 col-md-12 col-sm-12 col-sm-12 pb-0 mt-0">
                            <div class="card card-xxl-stretch {{ $lock_unlock ? '' : 'card-xxl-stretch-lock' }}">
                               
                                <div class="card-header pt-5" style="border: none;">
                                    <!--begin::Title-->
                                    <div class="card-title d-flex flex-column">
                                        <!--begin::Info-->
                                        <div class="d-flex align-items-center">
                                           <!--begin::Amount-->
                                            <h3 class="card-label">Vendor Daily Report</h3>
                                            <!--end::Amount-->
                                        </div>
                                        <!--end::Info-->
                                        <!--begin::Subtitle-->
                                        <span class="text-gray-500 pt-1 fw-semibold fs-6">{{ date("d M Y", strtotime($startOfPreviousDay)) }} </span>
                                        <!--end::Subtitle-->
                                    </div>
                                    <!--end::Title-->
                                </div>

                                <div class="card-body py-3 mb-4">
                                    <div class="tab-content">
                                        <div class="tab-pane fade show active kt_table_widget_5_tab">
                                            <div class="table-responsive">
                                                <div id="" class="w-100" style="height: 200px">
                                                    <div 
                                                        id="kt_card_widget_4_chart" 
                                                        style="min-width: 100%; min-height: 200px">
                                                        <canvas id="vendor_right_chart" height="170"></canvas>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <!--end::Col-->
                </div>
            </div>{{-- /old coins+teams/vendor hidden --}}

            {{-- ===================== CLIENT/BENCH REQUESTS + RECENT JOBS + DEPARTMENTS ===================== --}}
            @php
                $jwClientPending = (int) ($total_pending ?? 0);
                $jwBenchPending  = (int) ($total_candidate_req ?? 0);
                $jwClientJobs    = $final_list ?? [];
                $jwAvatar = function ($name) {
                    $sets = [['#e9f0ff', '#2b59ff'], ['#e6f9ee', '#16a34a'], ['#fff3e2', '#f08c00'], ['#fdecef', '#ec4899'], ['#f1ecff', '#7c5cff'], ['#e0f7fa', '#0891b2']];
                    return $sets[ord(strtoupper(substr(trim($name) . 'A', 0, 1))) % count($sets)];
                };
                $deptRows  = array_values(array_slice($graphobsdepartmentwise ?? [], 1));
                $deptTotal = array_sum(array_map(fn ($r) => (int) ($r[1] ?? 0), $deptRows));
                $jwDonut = '';
                if ($deptTotal > 0) {
                    $C = 2 * M_PI * 42; $cum = 0;
                    foreach ($deptRows as $row) {
                        $cnt = (int) ($row[1] ?? 0);
                        if ($cnt <= 0) { continue; }
                        $len = $cnt / $deptTotal * $C;
                        $jwDonut .= '<circle cx="60" cy="60" r="42" fill="none" stroke="' . ($row[2] ?? '#ccc') . '" stroke-width="16" stroke-dasharray="' . round($len, 2) . ' ' . round($C - $len, 2) . '" stroke-dashoffset="' . round(-$cum, 2) . '"/>';
                        $cum += $len;
                    }
                } else {
                    $jwDonut = '<circle cx="60" cy="60" r="42" fill="none" stroke="#eef1f7" stroke-width="16"/>';
                }
            @endphp

            {{-- Row: All Jobs + department wise jobs --}}
            <div class="row g-3 mb-3">
                <div class="col-12 col-xl-4 mb-3 mb-xl-0">
                    <div class="jw-card-soft jw-sc h-100" style="justify-content:center">
                        <div class="jw-sc-head">
                            <span class="jw-sc-ico blue"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="7" width="18" height="13" rx="2.5"/><path d="M8 7V5.5A1.5 1.5 0 0 1 9.5 4h5A1.5 1.5 0 0 1 16 5.5V7M3 12h18"/></svg></span>
                            <div><div class="jw-sc-label">All Jobs</div><div class="jw-sc-num">{{ number_format($jwAllJobs) }}</div><div class="jw-sc-meta">All Time</div></div>
                        </div>
                        <div class="jw-sc-breakdown">
                            <div class="r"><span class="dot" style="background:#22c55e"></span><span class="nm">Active Jobs</span><b>{{ number_format($active_count) }}</b></div>
                            <div class="r"><span class="dot" style="background:#ff9f1a"></span><span class="nm">On Hold Jobs</span><b>{{ number_format($hold_count) }}</b></div>
                            <div class="r"><span class="dot" style="background:#8b5cf6"></span><span class="nm">Closed Jobs</span><b>{{ number_format($inactive_count) }}</b></div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-xl-8">
                    <div class="jw-card-soft h-100">
                        <div style="display:flex;align-items:center;justify-content:space-between;padding:14px 20px 0;gap:8px">
                            <div class="jw-ch-title">Department Wise Jobs</div>
                            <span class="jw-ch-sel">All Departments ▾</span>
                        </div>
                        <div class="jw-dept">
                            <div class="jw-donut">
                                <svg viewBox="0 0 120 120" xmlns="http://www.w3.org/2000/svg">{!! $jwDonut !!}</svg>
                                <div class="ctr"><b>{{ number_format($deptTotal) }}</b><span>Active Jobs</span></div>
                            </div>
                            <table class="jw-dept-tbl">
                                @foreach ($deptRows as $row)
                                    @if ((int) ($row[1] ?? 0) > 0)
                                        <tr>
                                            <td><span class="dot" style="background:{{ $row[2] ?? '#ccc' }}"></span>{{ $row[0] ?? '' }}</td>
                                            <td>{{ number_format((int) ($row[1] ?? 0)) }}</td>
                                            <td>{{ $deptTotal > 0 ? round(($row[1] / $deptTotal) * 100, 1) : 0 }}%</td>
                                        </tr>
                                    @endif
                                @endforeach
                                <tr class="tot"><td>Total</td><td>{{ number_format($deptTotal) }}</td><td>100%</td></tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            {{-- old client/bench/department (hidden; google chart + bench table still bound) --}}
            <div class="d-none">
            @if($global_company_type != 3)
                <!--Start Job Request Section-->
                    <div class="row gy-5 g-xl-10">
                        <!--begin::Col-->
                        <div class="col-xl-4 mb-xl-10">
                            
                            <!--begin::Engage widget 1-->
                            <div class="card h-md-100" dir="ltr"> 
                                <!--begin::Body-->
                                <div class="card-body d-flex flex-column flex-center">  
                                    <!--begin::Heading-->
                                    <div class="mb-2">
                                        <!--begin::Title-->
                                        <h1 class="fw-semibold text-gray-800 text-center lh-lg">           
                                            Job Request <br> 
                                            <span class="fw-bolder">Pending </span>
                                        </h1>
                                        <!--end::Title--> 
                                        
                                        <!--begin::Illustration-->
                                        <div class="py-10 fs-1 text-center">
                                                {{$total_pending}}
                                        </div>
                                        <!--end::Illustration-->
                                    </div>
                                    <!--end::Heading-->
                                </div>
                                <!--end::Body-->
                            </div>
                            <!--end::Engage widget 1-->
                        </div>    
                        <!--end::Col-->

                        <!--begin::Col-->
                        <div class="col-xl-8 mb-5 mb-xl-10">
                                    
                            <!--begin::Table Widget 4-->
                            <div class="card card-flush h-xl-100">
                                <!--begin::Card header-->
                                <div class="card-header pt-7">
                                    <!--begin::Title-->
                                    <h3 class="card-title align-items-start flex-column">
                                        <span class="card-label fw-bold text-gray-800">Recent Client Jobs</span>
                                        <span class="text-gray-500 mt-1 fw-semibold fs-6"></span>
                                    </h3>
                                    <!--end::Title-->
                                </div>
                                <!--end::Card header-->

                                <!--begin::Card body-->
                                <div class="card-body pt-2">
                                    <!--begin::Table-->
                                    <div id="kt_table_widget_4_table_wrapper" class="dt-container dt-bootstrap5 dt-empty-footer">
                                        <div id="" class="table-responsive">
                                            <table class="table align-middle table-row-dashed fs-6 gy-3 dataTable" id="kt_table_widget_4_table" style="width: 100%;">
                                                <!--begin::Table head-->
                                                <thead>
                                                    <!--begin::Table row-->
                                                    <tr class="text-start text-gray-500 fw-bold fs-7 text-uppercase gs-0">
                                                        <th class="min-w-100px dt-orderable-none" data-dt-column="0" rowspan="1" colspan="1">
                                                            <span class="dt-column-title">Jobs</span>
                                                            <span class="dt-column-order"></span>
                                                        </th>
                                                        <th class="min-w-100px dt-orderable-none" data-dt-column="1" rowspan="1" colspan="1">
                                                            <span class="dt-column-title">Exp.</span>
                                                            <span class="dt-column-order"></span>
                                                        </th>
                                                        <th class="min-w-100px dt-orderable-none" data-dt-column="1" rowspan="1" colspan="1">
                                                            <span class="dt-column-title">Location</span>
                                                            <span class="dt-column-order"></span>
                                                        </th>
                                                        <th class="min-w-100px dt-orderable-none" data-dt-column="1" rowspan="1" colspan="1">
                                                            <span class="dt-column-title">Date</span>
                                                            <span class="dt-column-order"></span>
                                                        </th>
                                                        <th class="min-w-125px dt-orderable-none" data-dt-column="2" rowspan="1" colspan="1">
                                                            <span class="dt-column-title">Action</span>
                                                            <span class="dt-column-order"></span>
                                                        </th>
                                                    </tr>
                                                    <!--end::Table row-->
                                                </thead>
                                                <!--end::Table head-->

                                                <!--begin::Table body-->
                                                    <tbody class="fw-bold text-gray-600">
                                                        @if(@$final_list)
                                                            @foreach($final_list as $rowArr)
                                                            @php
                                                                $row = (object) $rowArr;
                                                                $row->id = $row->job_id;
                                                            @endphp
                                                                <tr>
                                                                    <td class="d-flex align-items-center">
                                                                        @php

                                                                            $bgTextDecore = $strVal = '';
                                                                            $str = trim(@$row->title);
                                                                            $str = $strVal = strtolower($str[0]);
                                                                            $str = ord($str);
                                                                            if ($str >= 97 && $str <= 101) {
                                                                                $bgTextDecore = 'fs-3 bg-light-danger text-danger';
                                                                            } elseif ($str >= 102 && $str <= 106) {
                                                                                $bgTextDecore = 'fs-3 bg-light-warning text-warning';
                                                                            } elseif ($str >= 107 && $str <= 111) {
                                                                                $bgTextDecore = 'fs-3 bg-light-primary text-primary';
                                                                            } elseif ($str >= 112 && $str <= 116) {
                                                                                $bgTextDecore = 'fs-3 bg-light-success text-success';
                                                                            } elseif ($str >= 117 && $str <= 121) {
                                                                                $bgTextDecore = 'fs-3 bg-dark text-secondary';
                                                                            } else {
                                                                                $bgTextDecore = 'fs-3 bg-light-secondary text-dark';
                                                                            }

                                                                            $icon =
                                                                                '<div data-row-id="' .
                                                                                $row->id .
                                                                                '" class="symbol symbol-circle symbol-40px overflow-hidden me-2 border-radius-5 client-show-detail" style="float:left">
                                                                            <a href="javascript:void(0);" data-row-id="' .
                                                                                $row->id .
                                                                                '" class="show-detail">
                                                                            <div class="symbol-label ' .
                                                                                $bgTextDecore .
                                                                                ' border-radius-5">' .
                                                                                ucwords($strVal) .
                                                                                '</div>
                                                                            </a>
                                                                        </div>';

                                                                            $logo_path = ''; //url('img/client-default.png');
                                                                            if (!empty(@$row->logo) && strlen(@$row->logo) > 10) {
                                                                                $logo_path = @$row->logo;
                                                                            }

                                                                            if (!empty($logo_path)) {
                                                                                $icon =
                                                                                    '<div data-row-id="' .
                                                                                    $row->id .
                                                                                    '" class="symbol symbol-circle symbol-40px overflow-hidden me-2  border-radius-5 client-show-detail">
                                                                                                <a href="javascript:void(0);">
                                                                                                    <div class="symbol-label border-radius-5">
                                                                                                    <img src="' .
                                                                                    $logo_path .
                                                                                    '" alt="Company Logo" class="border-radius-5 w-100">
                                                                                                    </div>
                                                                                                </a>
                                                                                            </div>';
                                                                            }

                                                                            $skilltag = '';
                                                                            $skilltagclass = '';
                                                                            if (empty($limit)) {
                                                                                $skillArr = $row->skillArr;
                                                                                if (!empty($skillArr)) {
                                                                                    foreach ($skillArr as $sval) {
                                                                                        $skilltag .=
                                                                                            '<span class="badge badge-light my-1">' . ucfirst($sval) . '</span>  ';
                                                                                    }
                                                                                }
                                                                            } else {
                                                                                $skilltagclass = 'd-none';
                                                                            }

                                                                            echo ' <div class=" d-flex align-items-center border-0" >
                                                                                    
                                                                                    ' .
                                                                                $icon .
                                                                                '
                                                                                
                                                                                <div class="d-flex flex-column"> 
                                                                                        <a href="javascript:void(0);" data-row-id="' .
                                                                                $row->id .
                                                                                '" class="text-dark fw-bolder text-hover-primary fs-6 client-show-detail " data-toggle="tooltip1" title="" data-bs-original-title="2">
                                                                                            ' .
                                                                                ucwords(mb_substr(@$row->title, 0, 15)) .
                                                                                '
                                                                                        </a> 
                                                                                        <a href="javascript:void(0);" data-row-id="' .
                                                                                $row->id .
                                                                                '" class="fw-bold text-muted d-block fs-7 text-hover-primary client-show-detail ">
                                                                                            ' .
                                                                                ucwords(mb_substr(@$row->company_name, 0, 15)) .
                                                                                '
                                                                                        </a> 
                                                                                    </div> 
                                                                                </div> ';

                                                                        @endphp
                                                                    </td>
                                                                    <td>
                                                                        @php
                                                                            echo '<div class="d-flex flex-column"> 
                                                                                        <span class="text-dark fw-bolder fs-6">' .
                                                                                @$row->max_exp .
                                                                                ' Y</span> 
                                                                                        <span class="text-muted fw-bold text-muted d-block fs-7">' .
                                                                                @$row->min_exp .
                                                                                ' Y</span> 
                                                                                    </div>';
                                                                        @endphp

                                                                    </td>
                                                                    <td>
                                                                        @php

                                                                            $country_id = $country_name = '';
                                                                            $jobid = $row->id;
                                                                            $client_loc = DB::table('client_location_jobs')
                                                                                ->join('client_location', 'client_location.id', '=', 'client_location_jobs.client_location_id')
                                                                                ->leftJoin('city_list', 'city_list.city_id', '=', 'client_location.city_id')
                                                                                ->select(
                                                                                    'client_location.address',
                                                                                    'client_location.city',
                                                                                    'client_location.postcode',
                                                                                    'client_location.country_id',
                                                                                    'city_list.city as city_name',
                                                                                )
                                                                                ->where('client_location_jobs.job_id', $jobid)
                                                                                ->first();

                                                                            $country_id = @$client_loc->country_id;
                                                                            if (@$country_id > 0) {
                                                                                $country_name = GeneralMethod::getCountryName($country_id);
                                                                                foreach ($country_name as $cval) {
                                                                                    $country_name = ucwords($cval['sortname']);
                                                                                }
                                                                            }

                                                                            $city = mb_substr(@$client_loc->city, 0, 6);
                                                                            // if (@$client_loc->country_id == 101) {
                                                                            $city = mb_substr(@$client_loc->city_name, 0, 6);
                                                                            // }

                                                                            $jobLocation = DB::table('client_location_jobs')
                                                                                ->where('client_location_jobs.job_id', $jobid)
                                                                                ->count();

                                                                            $jobCitySelected = DB::table('client_city_jobs')->where('client_city_jobs.job_id', $jobid)->count();

                                                                            $employment_type = 'Full-time';
                                                                            if ($row->employment_type == 1) {
                                                                                $employment_type = 'Contract';
                                                                            }

                                                                            if ($row->work_type == 'yes') {
                                                                                echo ' <div class="d-flex flex-column">
                                                                                <span class="text-dark fw-bolder fs-6">Remote</span>
                                                                                <div class="d-flex align-items-center">
                                                                                <span class="fw-bold text-muted d-block fs-7" data-toggle="tooltip4" title="' .
                                                                                    ucwords(@$employment_type) .
                                                                                    '" >' .
                                                                                    ucwords(mb_substr(@$employment_type, 0, 10)) .
                                                                                    '</span>
                                                                                </div>
                                                                                </div>
                                                                            </div>';
                                                                            } elseif ($row->work_type == 'no' || $row->work_type =='other') {
                                                                                if ($jobCitySelected == 1 || $jobCitySelected == 0) {
                                                                                    $client_loc = DB::table('client_city_jobs')
                                                                                        ->leftJoin('city_list', 'city_list.city_id', '=', 'client_city_jobs.city_id')
                                                                                        ->leftJoin('state_list', 'state_list.id', '=', 'city_list.state_id')
                                                                                        ->leftJoin('countries', 'countries.id', '=', 'state_list.country_id')
                                                                                        ->select('city_list.city as city_name', 'countries.country_name')
                                                                                        ->where('client_city_jobs.job_id', $jobid)
                                                                                        ->first();
                                                                                    $city = mb_substr(@$client_loc->city_name, 0, 21);
                                                                                    $country_name = mb_substr(@$client_loc->country_name, 0, 6);

                                                                                    echo ' <div class="d-flex flex-column">
                                                                                    <div class="d-flex align-items-center">
                                                                                        <span class="text-dark fw-bolder fs-6">' .
                                                                                        ucwords(mb_substr(@$city, 0, 10)) .
                                                                                        '</span>
                                                                                        </div>
                                                                                        
                                                                                    <span class="fw-bold text-muted d-block fs-7" data-toggle="tooltip4" title="' .
                                                                                        ucwords(@$employment_type) .
                                                                                        '" >' .
                                                                                        ucwords(mb_substr(@$employment_type, 0, 10)) .
                                                                                        '</span>
                                                                                    </div>
                                                                                    </div>
                                                                                </div>';
                                                                                } else {
                                                                                    echo ' <div class="d-flex flex-column">
                                                                                    <div class="d-flex align-items-center">
                                                                                        <span class="text-dark fw-bolder fs-6">Multiple</span>
                                                                                        </div>
                                                                                    <span class="fw-bold text-muted d-block fs-7" data-toggle="tooltip4" title="' .
                                                                                        ucwords(@$employment_type) .
                                                                                        '" >' .
                                                                                        ucwords(mb_substr(@$employment_type, 0, 10)) .
                                                                                        '</span>
                                                                                </div>
                                                                                </div>
                                                                            </div>';
                                                                                }
                                                                            } elseif ($row->work_type == 'any') {
                                                                                if ($jobCitySelected == 1 || $jobCitySelected == 0) {
                                                                                    $client_loc = DB::table('client_city_jobs')
                                                                                        ->leftJoin('city_list', 'city_list.city_id', '=', 'client_city_jobs.city_id')
                                                                                        ->leftJoin('state_list', 'state_list.id', '=', 'city_list.state_id')
                                                                                        ->leftJoin('countries', 'countries.id', '=', 'state_list.country_id')
                                                                                        ->select('city_list.city as city_name', 'countries.country_name')
                                                                                        ->where('client_city_jobs.job_id', $jobid)
                                                                                        ->first();
                                                                                    $city = mb_substr(@$client_loc->city_name, 0, 21);
                                                                                    $country_name = mb_substr(@$client_loc->country_name, 0, 6);

                                                                                    echo ' <div class="d-flex flex-column">
                                                                                    <div class="d-flex align-items-center">
                                                                                        <span class="text-dark fw-bolder fs-6">' .
                                                                                        ucwords(mb_substr(@$city, 0, 10)) .
                                                                                        '</span>
                                                                                        </div>
                                                                                        
                                                                                    <span class="fw-bold text-muted d-block fs-7" data-toggle="tooltip4" title="' .
                                                                                        ucwords(@$employment_type) .
                                                                                        '" >' .
                                                                                        ucwords(mb_substr(@$employment_type, 0, 10)) .
                                                                                        '</span>
                                                                                    </div>
                                                                                    </div>
                                                                                </div>';
                                                                                } else {
                                                                                    echo ' <div class="d-flex flex-column">
                                                                                <div class="d-flex align-items-center">
                                                                                <span class="text-dark fw-bolder fs-6">Remote</span>
                                                                                &nbsp;|&nbsp;
                                                                                        <span class="text-dark fw-bolder fs-6">Multiple</span>
                                                                                        </div>
                                                                                    <span class="fw-bold text-muted d-block fs-7" data-toggle="tooltip4" title="' .
                                                                                        ucwords(@$employment_type) .
                                                                                        '" >' .
                                                                                        ucwords(mb_substr(@$employment_type, 0, 10)) .
                                                                                        '</span>

                                                                                </div>
                                                                                </div>
                                                                            </div>';
                                                                                }
                                                                            }

                                                                        @endphp
                                                                    </td>                                                                
                                                                    <td class="">
                                                                        @php
                                                                            $total_positions = '';
                                                                            $total_positions = number_format($row->total_positions);

                                                                            $created_at = '';
                                                                            $created_at = $row->created_at;
                                                                            $newDate = GeneralMethod::ymdTodmyNotTime($created_at); //
                                                                        @endphp

                                                                        <span class="text-dark fw-bolder fs-6">
                                                                            {{ $newDate }}  
                                                                        </span> 
                                                                        <span class="fw-bold text-muted d-block fs-7">
                                                                            {{ $total_positions }} Opening
                                                                        </span>                       
                                                                    </td>                                                                
                                                                    <td class="">
                                                                    @php
                                                                            if($row->final_status==1){
                                                                            echo '<div class="d-flex my-4">  
                                                                                <a href="javascript:void(0);" data-row-status="accept" class="btn btn-sm btn-success">
                                                                                    Accepted
                                                                                </a> 
                                                                                </div>';
                                                                            }elseif($row->final_status==-1){
                                                                            echo '<div class="d-flex my-4">  
                                                                                <a href="javascript:void(0);" data-row-status="accept" class="btn btn-sm btn-danger">
                                                                                    Rejected
                                                                                </a> 
                                                                                </div>';
                                                                            }elseif($row->final_status==2){
                                                                            echo '<div class="d-flex my-4">  
                                                                                <a href="javascript:void(0);" data-row-status="accept" class="btn btn-sm btn-danger">
                                                                                    Removed
                                                                                </a> 
                                                                                </div>';
                                                                            }elseif($row->final_status==3){
                                                                            echo '<div class="d-flex my-4">  
                                                                                <a href="javascript:void(0);" data-row-status="accept" class="btn btn-sm btn-danger">
                                                                                    Expired
                                                                                </a> 
                                                                                </div>';
                                                                            }else{
                                                                            echo '<div class="d-flex my-4"> 
                                                                                <a href="javascript:void(0);" data-row-status="" data-row-id="' .$row->id .'"  class="btn btn-sm btn-warning me-2">
                                                                                    Pending
                                                                                </a> 
                                                                                <a href="'.url('service_company/jobs/job_request/0').'" data-row-status="" data-row-id="' . $row->id .'"  class="btn btn-sm btn-primary">
                                                                                    View
                                                                                </a> 
                                                                                </div>';
                                                                            }
                                                                            
                                                                        @endphp
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                        @endif
                                                    </tbody>
                                                <!--end::Table body-->
                                            <tfoot></tfoot>
                                            </table>
                                        </div>
                                    </div>
                                    <!--end::Table-->
                                </div>
                                <!--end::Card body-->
                            </div>
                            <!--end::Table Widget 4-->    
                        </div>
                        <!--end::Col-->   
                    </div>
                <!--End Job Request Section-->

            @endif

            <!--Start Candidate Request Section-->
                <div class="row gy-5 g-xl-10">
                    <!--begin::Col-->
                    <div class="col-xl-4 mb-xl-10">
                        
                        <!--begin::Engage widget 1-->
                        <div class="card h-md-100" dir="ltr"> 
                            <!--begin::Body-->
                            <div class="card-body d-flex flex-column flex-center">  
                                <!--begin::Heading-->
                                <div class="mb-2">
                                    <!--begin::Title-->
                                    <h1 class="fw-semibold text-gray-800 text-center lh-lg">           
                                        Bench Talent Request <br> 
                                        <span class="fw-bolder">Pending </span>
                                    </h1>
                                    <!--end::Title--> 
                                    
                                    <!--begin::Illustration-->
                                    <div class="py-10 fs-1 text-center">
                                            {{$total_candidate_req}}
                                    </div>
                                    <!--end::Illustration-->
                                </div>
                                <!--end::Heading-->
                            </div>
                            <!--end::Body-->
                        </div>
                        <!--end::Engage widget 1-->
                    </div>    
                    <!--end::Col-->

                    <!--begin::Col-->
                    <div class="col-xl-8 mb-5 mb-xl-10">
                                
                        <!--begin::Table Widget 4-->
                        <div class="card card-flush h-xl-100">
                            <!--begin::Card header-->
                            <div class="card-header pt-7">
                                <!--begin::Title-->
                                <h3 class="card-title align-items-start flex-column">
                                    <span class="card-label fw-bold text-gray-800">Recent Bench Talent Request</span>
                                    <span class="text-gray-500 mt-1 fw-semibold fs-6"></span>
                                </h3>
                                <!--end::Title-->
                            </div>
                            <!--end::Card header-->

                            <!--begin::Card body-->
                            <div class="card-body pt-2">
                                <!--begin::Table-->
                                <div id="kt_table_widget_4_table_wrapper" class="dt-container dt-bootstrap5 dt-empty-footer">
                                    <div id="" class="table-responsive">
                                        <table class="table align-middle table-row-dashed fs-6 gy-3 dataTable" id="kt_table_widget_4_table" style="width: 100%;">
                                            <!--begin::Table head-->
                                            <thead>
                                                <!--begin::Table row-->
                                                <tr class="text-start text-gray-500 fw-bold fs-7 text-uppercase gs-0">
                                                    <th class="min-w-100px dt-orderable-none" data-dt-column="0" rowspan="1" colspan="1">
                                                        <span class="dt-column-title">Name</span>
                                                        <span class="dt-column-order"></span>
                                                    </th>
                                                    <th class="min-w-100px dt-orderable-none" data-dt-column="1" rowspan="1" colspan="1">
                                                        <span class="dt-column-title">Exp.</span>
                                                        <span class="dt-column-order"></span>
                                                    </th>
                                                    <th class="min-w-100px dt-orderable-none" data-dt-column="1" rowspan="1" colspan="1">
                                                        <span class="dt-column-title">Jobs</span>
                                                        <span class="dt-column-order"></span>
                                                    </th>
                                                    <th class="min-w-100px dt-orderable-none" data-dt-column="1" rowspan="1" colspan="1">
                                                        <span class="dt-column-title">Date</span>
                                                        <span class="dt-column-order"></span>
                                                    </th>
                                                    <th class="min-w-125px dt-orderable-none" data-dt-column="2" rowspan="1" colspan="1">
                                                        <span class="dt-column-title">Action</span>
                                                        <span class="dt-column-order"></span>
                                                    </th>
                                                </tr>
                                                <!--end::Table row-->
                                            </thead>
                                            <!--end::Table head-->

                                            <!--begin::Table body-->
                                                <tbody class="fw-bold text-gray-600">
                                                    @if(@$candidate_final_list)
                                                    
                                                        @foreach($candidate_final_list as $rowArr)
                                                        @php
                                                            $row = (object) $rowArr;
                                                            $jobid = $row->source_jobid;
                                                        @endphp
                                                            <tr>
                                                                <td>
                                                                    @php
                                                                        $bgTextDecore = $strVal = '';
                                                                        $str = trim(@$row->full_name);
                                                                        $str = $strVal = strtolower($str[0]);
                                                                        $str = ord($str);
                                                                        if ($str >= 97 && $str <= 101) {
                                                                            $bgTextDecore = 'fs-3 bg-light-danger text-danger';
                                                                        } elseif ($str >= 102 && $str <= 106) {
                                                                            $bgTextDecore = 'fs-3 bg-light-warning text-warning';
                                                                        } elseif ($str >= 107 && $str <= 111) {
                                                                            $bgTextDecore = 'fs-3 bg-light-primary text-primary';
                                                                        } elseif ($str >= 112 && $str <= 116) {
                                                                            $bgTextDecore = 'fs-3 bg-light-success text-success';
                                                                        } elseif ($str >= 117 && $str <= 121) {
                                                                            $bgTextDecore = 'fs-3 bg-dark text-secondary';
                                                                        } else {
                                                                            $bgTextDecore = 'fs-3 bg-light-secondary text-dark';
                                                                        }

                                                                        $flag = 0;
                                                                        $duplicate = 0;
                                                                        $duplicate = $row->duplicate;
                                                                        $company_id = auth()->user()->roles[0]->company_id;
                                                                        $app_duplicate = auth()->user()->company->app_duplicate;
                                                                        $app_duplicate_days = auth()->user()->company->app_duplicate_days;

                                                                        $covid = '';
                                                                        if ($duplicate == 1) {
                                                                            // CHECK RECORD IS DUPLICATE
                                                                            if ($app_duplicate == 1 || $app_duplicate == 2) {
                                                                                // CHECK SYSTEM MODE SET IN DUPLICATE
                                                                                if ($app_duplicate == 1) {
                                                                                    // CHECK DUPLICATE JOBWISE WITH TIME INTERVAL IN DAYS FOR- 1
                                                                                    $application_cnt = DB::table('job_applications')
                                                                                        ->select('*')
                                                                                        ->where('company_id', $company_id)
                                                                                        ->where('job_id', $row->job_id)
                                                                                        ->where('email', $row->email)
                                                                                        ->orWhere('phone', $row->phone)
                                                                                        ->where(
                                                                                            'job_applications.created_at',
                                                                                            '>',
                                                                                            DB::raw('DATE_SUB(CURDATE(), INTERVAL ' . @$app_duplicate_days . ' DAY)'),
                                                                                        )
                                                                                        ->orderby('job_applications.id', 'DESC')
                                                                                        ->count();
                                                                                    if ($application_cnt > 1) {
                                                                                        $flag = 1;
                                                                                    } else {
                                                                                        $flag = 0;
                                                                                    }
                                                                                } elseif ($app_duplicate == 2) {
                                                                                    // CHECK DUPLICATE OVERALL SYSTEM WITH TIME INTERVAL IN DAYS FOR- 2
                                                                                    $application_cnt = DB::table('job_applications')
                                                                                        ->select('job_applications.id')
                                                                                        ->where('company_id', $company_id)
                                                                                        ->where('job_applications.email', $row->email)
                                                                                        ->orWhere('phone', $row->phone)
                                                                                        ->where(
                                                                                            'job_applications.created_at',
                                                                                            '>',
                                                                                            DB::raw('DATE_SUB(CURDATE(), INTERVAL ' . @$app_duplicate_days . ' DAY)'),
                                                                                        )
                                                                                        //->where('job_applications.duplicate', '=', 1 )
                                                                                        ->orderby('job_applications.id', 'DESC')
                                                                                        ->count();

                                                                                    if ($application_cnt > 1) {
                                                                                        $flag = 1;
                                                                                    } else {
                                                                                        $flag = 0;
                                                                                    }
                                                                                } else {
                                                                                    $flag = 0;
                                                                                }
                                                                            } else {
                                                                                $flag = 0;
                                                                            }
                                                                        } else {
                                                                            $flag = 0;
                                                                        }
                                                                        $skilltag = '';
                                                                        $skilltagclass = '';
                                                                        $skillArr = explode(',', $row->skills);
                                                                        $percentage =  GeneralMethod::skill_percentage($jobid,$skillArr);
                                                                        if (count($skillArr) > 0) {
                                                                            foreach ($skillArr as $sval) {
                                                                                $skilltag .= '<span class="badge badge-light my-1" style="font-weight: 300;">' . ucfirst($sval) . '</span>  ';
                                                                            }
                                                                        }

                                                                        if ($flag == 1) {
                                                                            echo '<div class="symbol symbol-circle symbol-40px overflow-hidden me-2 border-radius-5" style="float:left">
                                                                        <a href="javascript:void(0);"  data-row-id="' .
                                                                                @$row->id .
                                                                                '" class="pbench_show_application_detail btn btn-icon btn-light-danger pulse pulse-danger mr-5" data-toggle="tooltip" title="Duplicate">
                                                                        <span class="fs-3">' .
                                                                                strtoupper(@$strVal) .
                                                                                '</span>
                                                                        <span class="pulse-ring"></span>
                                                                        </a>
                                                                        </div>                  
                                                                        <div class="d-flex flex-column">
                                                                        <a href="javascript:void(0);"  data-row-id="' .
                                                                                @$row->id .
                                                                                '"  class="text-danger fw-bolder text-hover-primary fs-6 pbench_show_application_detail" data-toggle="tooltip" title="' .
                                                                                ucwords(@$row->full_name) .
                                                                                '">' .
                                                                                ucwords(mb_substr(@$row->full_name, 0, 7)) .
                                                                                $covid .
                                                                                '</a>
                                                                        <a href="javascript:void(0);"  data-row-id="' .
                                                                                @$row->id .
                                                                                '"  class="text-danger fw-bold d-block fs-7 text-hover-primary pbench_show_application_detail" data-toggle="tooltip" title="' .
                                                                                ucwords(@$row->current_designation) .
                                                                                '">' .
                                                                                ucwords(mb_substr(@$row->current_designation, 0, 7)) .
                                                                                '</a>
                                                                        </div>';
                                                                        } else {
                                                                            echo '<div class="symbol symbol-circle symbol-40px overflow-hidden me-2 border-radius-5" style="float:left">
                                                                        <a href="javascript:void(0);"   data-row-id="' .
                                                                                @$row->id .
                                                                                '" class="pbench_show_application_detail">
                                                                        <div class="symbol-label ' .
                                                                                @$bgTextDecore .
                                                                                ' border-radius-5">' .
                                                                                strtoupper(@$strVal) .
                                                                                '</div>
                                                                        </a>
                                                                        </div>                  
                                                                        <div class="d-flex flex-column">
                                                                        <a href="javascript:void(0);"  data-row-id="' .
                                                                                @$row->id .
                                                                                '"  class="text-dark fw-bolder text-hover-primary fs-6 pbench_show_application_detail" data-toggle="tooltip" title="' .
                                                                                ucwords(@$row->full_name) .
                                                                                '">' .
                                                                                ucwords(mb_substr(@$row->full_name, 0, 10)) .
                                                                                $covid .
                                                                                '</a>
                                                                        <a href="javascript:void(0);"  data-row-id="' .
                                                                                @$row->id .
                                                                                '"  class="text-muted fw-bold d-block fs-7 text-hover-primary pbench_show_application_detail" data-toggle="tooltip" title="' .
                                                                                ucwords(@$row->current_designation) .
                                                                                '">' .
                                                                                ucwords(mb_substr(@$row->current_designation, 0, 10)) .
                                                                                '</a>
                                                                        </div> ';
                                                                        }
                                                                    @endphp
                                                                </td>
                                                                <td>
                                                                     @php
                                                                        $total_exp = $row->total_exp;
                                                                        if($total_exp == '0.'){
                                                                        $total_exp = str_replace('.','',$total_exp);
                                                                        }
                                                                        $rel_exp = $row->rel_exp;
                                                                        if($total_exp == '0.'){
                                                                        $rel_exp = str_replace('.','',$rel_exp);
                                                                        }
                                                                        if($total_exp==0 && $rel_exp==0){
                                                                            $rel_exp = ''; 
                                                                        }
                                                                        $experience = floatval(@$row->total_exp); // Ensure it's a valid float
                                                                        // If experience is a whole number, convert it to an integer; otherwise, keep the decimal
                                                                        $formattedExperience = ($experience == intval($experience)) ? intval($experience) : $experience;

                                                                        $rel_experience = floatval(@$row->rel_exp); // Ensure it's a valid float
                                                                        // If experience is a whole number, convert it to an integer; otherwise, keep the decimal
                                                                        $rel_formattedExperience = ($rel_experience == intval($rel_experience)) ? intval($rel_experience) : $rel_experience;

                                                                        // echo $formattedExperience . ' Y';

                                                                            echo '<div class="d-flex flex-column">
                                                                        <a href="javascript:void(0);" class="text-dark fw-bolder fs-6">' .$formattedExperience . ' Y' . 
                                                                            '</a>
                                                                            <span class="text-muted fw-bold d-block fs-7">' . $rel_formattedExperience . ' Y'. 
                                                                            '</span>
                                                                        </div>';
                                                                    @endphp

                                                                </td>
                                                                <td>
                                                                    @php

                                                                     
                                                                        echo ' <div class="d-flex flex-column">
                                                                            <div class="d-flex align-items-center">
                                                                            <span class="text-dark fw-bolder fs-6">Remote</span>
                                                                            &nbsp;|&nbsp;
                                                                                    <span class="text-dark fw-bolder fs-6">Multiple</span>
                                                                                    </div>
                                                                                <span class="fw-bold text-muted d-block fs-7" data-toggle="tooltip4" title="' .
                                                                                    ucwords(mb_substr(@$row->title, 0, 20)) .
                                                                                    '" >' .
                                                                                    ucwords(mb_substr(@$row->client_name, 0, 15)) .
                                                                                    '</span>

                                                                            </div>
                                                                            </div>
                                                                        </div>';
                                                                            
                                                                        

                                                                    @endphp
                                                                </td>                                                                
                                                                <td class="">
                                                                    @php
                                                                        $posted_by_image = '';
                                                                        $posted_by_image = url('public/avatar.png');
                                                                        if (!empty($row->posted_by_image)) {
                                                                            $posted_by_image = url('public/user-uploads/team_profile_img/' . @$row->posted_by_image);
                                                                        }
                                                                        $dateupdate = GeneralMethod::ymdTodmyNotTime($row->updated_at);
                                                                        $comission_type = $row->comission_type == 1 ? 'Free' : 'Paid ';
                                                                        echo '<div class="d-flex flex-column">
                                                                        <span class="text-dark fw-bolder fs-6" data-toggle="tooltip" title="" data-bs-original-title="">' .
                                                                                $dateupdate .
                                                                                '</span>
                                                                        <span class="text-muted fw-bold text-muted d-block fs-7" data-toggle="tooltip">' .
                                                                                $comission_type .
                                                                                '</span> 
                                                                        </div>
                                                                        ';
                                                                    @endphp                     
                                                                </td>                                                                
                                                                <td class="">
                                                                   @php
                                                                            $name = '';
                                                                            $candidates_request = url('/service_company/talent-pool-candidate/request/' . GeneralMethod::value_encrypt($row->source_jobid));
                                                                        
                                                                            if ($row->ac_status==0) {
                                                                                echo '<div class="d-flex my-4"> 
                                                                                        <a href="javascript:void(0);" data-row-status="" data-row-id="' .$row->source_jobid .'"  class="btn btn-sm btn-warning me-2">
                                                                                            Pending
                                                                                        </a> 
                                                                                        <a href="'.$candidates_request.'" data-row-status="" data-row-id="' . $row->source_jobid .'"  class="btn btn-sm btn-primary">
                                                                                            View
                                                                                        </a> 
                                                                                        </div>';
                                                                            }elseif($row->ac_status==1){
                                                                                            echo '<div class="d-flex my-4">
                                                                                                <a href="javascript:void(0);" class="btn btn-sm btn-success">Accepted</a>
                                                                                        </div> ';
                                                                            } elseif($row->ac_status==2){
                                                                                            echo '<div class="d-flex my-4">
                                                                                                <a href="javascript:void(0);" class="btn btn-sm btn-danger">Expired</a>
                                                                                        </div> ';

                                                                            }else{
                                                                                        echo '<div class="d-flex my-4"><a href="javascript:void(0);" class="btn btn-sm btn-danger">Removed</a></div>';
                                                                            }
                                                                        @endphp
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    @endif
                                                </tbody>
                                            <!--end::Table body-->
                                        <tfoot></tfoot>
                                        </table>
                                    </div>
                                </div>
                                <!--end::Table-->
                            </div>
                            <!--end::Card body-->
                        </div>
                        <!--end::Table Widget 4-->    
                    </div>
                    <!--end::Col-->   
                </div>
            <!--End Candidate Request Section-->


                

            <!-- Department Wise Jobs -->
                <div class="row">
                    <!--begin::Col-->
                    <div class="col-md-12 col-lg-12 col-xl-12 col-xxl-12 mb-xl-12" style="height: 100%;">

                        
                                    <script type="text/javascript">
                                            google.charts.load("current", {packages:["corechart"]});
                                            google.charts.setOnLoadCallback(drawChart);
                                            function drawChart() {

                                               

                                                 // Check if all values are zero
                                                var allZero = '{{$department_true}}';
                                               
                                                // Replace data with dummy data if all values are zero
                                                if (allZero) {
                                                    var data = google.visualization.arrayToDataTable([
                                                    ['Status', 'Count'],
                                                    ['No Data Available', 0]
                                                    ]);
                                                }else{
                                                    var data = google.visualization.arrayToDataTable(
                                                        @json($graphobsdepartmentwise));
                                                }


                                                var options = {
                                                title: 'Department Wise Jobs',
                                                is3D: true,
                                                sliceVisibilityThreshold: 0,
                                                pieSliceTextStyle: {
                                                color: 'black', // Label text color
                                                fontSize: 12    // Font size for labels
                                                }
                                                };

                                                var chart = new google.visualization.ColumnChart(document.getElementById('piechart_3d'));
                                                chart.draw(data, options);
                                            }
                                    </script>
                                
                                    <div id="piechart_3d" style="height: 400px;"></div>
                        <!--end::Card widget 17-->

                    </div>
                    <!--end::Col-->
                </div>
               <!-- End Department Wise jobs -->

            </div>{{-- /old client/bench/department hidden --}}

            {{-- footer --}}
            <div class="jw-footer">
                <span>© {{ date('Y') }} By Jero.works Technology</span>
                <span class="jw-footer-links"><a href="javascript:void(0)">Terms &amp; Conditions</a><a href="javascript:void(0)">Privacy Policy</a></span>
            </div>
            </div>
        </div>
    </div>
    <!--end::Content-->

    <div class="modal fade" id="company_rank_global" tabindex="-1" aria-hidden="true" >
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content custmbg">
            <div class="modal-header flex-stack" style="position: relative;">
                <h2>Score Board {{date("F Y",strtotime($rankstartOfMonth))}} </h2>
                <div class="btn btn-sm btn-icon btn-active-color-primary data-bs-dismiss" data-bs-dismiss="modal"> <span class="svg-icon svg-icon-1">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                    <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1" transform="rotate(-45 6 17.3137)" fill="black" />
                    <rect x="7.41422" y="6" width="16" height="2" rx="1" transform="rotate(45 7.41422 6)" fill="black" />
                </svg>
                </span> </div>
            </div>
            <div class="modal-body scroll-y mx-5 mx-xl-18 pt-0 pb-15">
                            <div class="table-responsive">
                                <!--begin::Table-->
                                <table class="table table-row-dashed table-row-gray-200 align-middle gs-0 gy-4">
                                    <!--begin::Table head-->
                                    <thead>
                                        <tr class="border-0">
                                            <th class="p-0 min-w-150px">Events</th>
                                            <th class="p-0 min-w-150px">Points</th>
                                            <th class="p-0 min-w-150px">Activity</th>
                                            <th class="p-0 min-w-140px">Earned</th>
                                        </tr>
                                    </thead>
                                    <!--end::Table head-->

                                    <!--begin::Table body-->
                                    <tbody>
                                            <tr>
                                                <td>
                                                    <a href="#" class="text-gray-700 text-hover-primary mb-1 fs-6">InReview</a>
                                                </td>
                                                <td class="text-muted fw-bold">
                                                    +1                                    
                                                </td>
                                                 <td class="text-muted fw-bold">
                                                    <span class="">{{$ownUser_global_company['profileUpload']/1}}</span>
                                                </td>
                                                <td class="">
                                                    <span class="text-success">+{{$ownUser_global_company['profileUpload']}}</span>
                                                </td>
                                            </tr> 
                                            <tr>
                                                <td>
                                                    <a href="#" class="text-gray-700 text-hover-primary mb-1 fs-6">Sourcing</a>
                                                </td>
                                                <td class="text-muted fw-bold">
                                                    +1                                    
                                                </td>
                                                 <td class="text-muted fw-bold">
                                                    <span class="">{{$ownUser_global_company['sourcing']/1}}</span>
                                                </td>
                                                <td class="">
                                                    <span class="text-success">+{{$ownUser_global_company['sourcing']}}</span>
                                                </td>
                                            </tr> 
                                            <tr>
                                                <td>
                                                    <a href="#" class="text-gray-700 text-hover-primary mb-1 fs-6">Screening</a>
                                                </td>
                                                <td class="text-muted fw-bold">
                                                    +1                                    
                                                </td>
                                                 <td class="text-muted fw-bold">
                                                    <span class="">{{$ownUser_global_company['screening']/1}}</span>
                                                </td>
                                                <td class="">
                                                    <span class="text-success">+{{$ownUser_global_company['screening']}}</span>
                                                </td>
                                            </tr> 
                                            <tr>
                                                <td>
                                                    <a href="#" class="text-gray-700 text-hover-primary mb-1 fs-6">Evaluation</a>
                                                </td>
                                                <td class="text-muted fw-bold">
                                                    +1                                    
                                                </td>
                                                 <td class="text-muted fw-bold">
                                                    <span class="">{{$ownUser_global_company['evaluation']/1}}</span>
                                                </td>
                                                <td class="">
                                                    <span class="text-success">+{{$ownUser_global_company['evaluation']}}</span>
                                                </td>
                                            </tr> 
                                            <tr>
                                                <td>
                                                    <a href="#" class="text-gray-700 text-hover-primary mb-1 fs-6">Negotiation</a>
                                                </td>
                                                <td class="text-muted fw-bold">
                                                    +1                                    
                                                </td>
                                                 <td class="text-muted fw-bold">
                                                    <span class="">{{$ownUser_global_company['negotiation']/1}}</span>
                                                </td>
                                                <td class="">
                                                    <span class="text-success">+{{$ownUser_global_company['negotiation']}}</span>
                                                </td>
                                            </tr> 
                                            <tr>
                                                <td>
                                                    <a href="#" class="text-gray-700 text-hover-primary mb-1 fs-6">Offers Released</a>
                                                </td>
                                                <td class="text-muted fw-bold">
                                                    +300                                
                                                </td>
                                                 <td class="text-muted fw-bold">
                                                    <span class="">{{$ownUser_global_company['offerReleased']/300}}</span>
                                                </td>
                                                <td class="">
                                                    <span class="text-success">+{{$ownUser_global_company['offerReleased']}}</span>
                                                </td>
                                            </tr>  
                                            <tr>
                                                <td>
                                                    <a href="#" class="text-gray-700 text-hover-primary mb-1 fs-6">Joined & OnBoarded</a>
                                                </td>
                                                <td class="text-muted fw-bold">
                                                    +500                               
                                                </td>
                                                 <td class="text-muted fw-bold">
                                                    <span class="">{{$ownUser_global_company['candidateJoined']/1500}}</span>
                                                </td>
                                                <td class="">
                                                    <span class="text-success">+{{$ownUser_global_company['candidateJoined']}}</span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <a href="#" class="text-gray-700 text-hover-primary mb-1 fs-6">Joined Backed Out</a>
                                                </td>
                                                <td class="text-muted fw-bold">
                                                    -500                                    
                                                </td>
                                                 <td class="text-muted fw-bold">
                                                    <span class="">{{$ownUser_global_company['backout']/1500}}</span>
                                                </td>
                                                <td class="">
                                                    <span class="text-danger">{{$ownUser_global_company['backout']}}</span>
                                                </td>
                                            </tr>   
                                            <tr>
                                                <td>
                                                    <a href="#" class="text-gray-700 text-hover-primary mb-1 fs-6">Interviews Conducted</a>
                                                </td>
                                                <td class="text-muted fw-bold">
                                                    +10                                   
                                                </td>
                                                 <td class="text-muted fw-bold">
                                                    <span class="">{{$ownUser_global_company['interviewScheduled']/10}}</span>
                                                </td>
                                                <td class="">
                                                    <span class="text-success">+{{$ownUser_global_company['interviewScheduled']}}</span>
                                                </td>
                                            </tr>  
                                            <tr>
                                                <td>
                                                    <a href="#" class="text-gray-700 text-hover-primary mb-1 fs-6">Interview NoShow</a>
                                                </td>
                                                <td class="text-muted fw-bold">
                                                    -5                                    
                                                </td>
                                                 <td class="text-muted fw-bold">
                                                    <span class="">{{$ownUser_global_company['interviewNoShow']/5}}</span>
                                                </td>
                                                <td class="">
                                                    <span class="text-danger">{{($ownUser_global_company['interviewNoShow'] ? '' : '-')}}{{$ownUser_global_company['interviewNoShow']}}</span>
                                                </td>
                                            </tr>  
                                            {{-- <tr>
                                                <td>
                                                    <a href="#" class="text-gray-700 text-hover-primary mb-1 fs-6">Team Captain Offer Released</a>
                                                </td>
                                                <td class="text-muted fw-bold">
                                                    +20                               
                                                </td>
                                                 <td class="text-muted fw-bold">
                                                    <span class="">{{$ownUser_global_company['teamCaptainOfferReleased']/20}}</span>
                                                </td>
                                                <td class="">
                                                    <span class="text-success">+{{$ownUser_global_company['teamCaptainOfferReleased']}}</span>
                                                </td>
                                            </tr>  --}}
                                             {{-- <tr>
                                                <td>
                                                    <a href="#" class="text-gray-700 text-hover-primary mb-1 fs-6">Team Captain Joined & OnBoarded</a>
                                                </td>
                                                <td class="text-muted fw-bold">
                                                    +30                               
                                                </td>
                                                 <td class="text-muted fw-bold">
                                                    <span class="">{{$ownUser_global_company['teamCaptainCandidateJoined']/30}}</span>
                                                </td>
                                                <td class="">
                                                    <span class="text-success">+{{$ownUser_global_company['teamCaptainCandidateJoined']}}</span>
                                                </td>
                                            </tr>  --}}
                                             
                                            {{-- <tr>
                                                <td>
                                                    <a href="#" class="text-gray-700 text-hover-primary mb-1 fs-6">Team Captain Joined & Backed Out</a>
                                                </td>
                                                <td class="text-muted fw-bold">
                                                    -30                                    
                                                </td>
                                                 <td class="text-muted fw-bold">
                                                    <span class="">{{$ownUser_global_company['backoutTeam']/30}}</span>
                                                </td>
                                                <td class="">
                                                    <span class="text-danger">{{$ownUser_global_company['backoutTeam']}}</span>
                                                </td>
                                            </tr> --}}
                                            
                                            {{-- <tr>
                                                <td>
                                                    <a href="#" class="text-gray-700 text-hover-primary mb-1 fs-6">Team Captain Interviews Conducted</a>
                                                </td>
                                                <td class="text-muted fw-bold">
                                                    +2                             
                                                </td>
                                                 <td class="text-muted fw-bold">
                                                    <span class="">{{$ownUser_global_company['teamCaptainInterviewScheduled']/2}}</span>
                                                </td>
                                                <td class="">
                                                    <span class="text-success">+{{$ownUser_global_company['teamCaptainInterviewScheduled']}}</span>
                                                </td>
                                            </tr> --}}
                                            <tr>
                                                <td>
                                                    <a href="#" class="text-gray-700 text-hover-primary mb-1 fs-6">Jobs Posted</a>
                                                </td>
                                                <td class="text-muted fw-bold">
                                                    +2                                    
                                                </td>
                                                 <td class="text-muted fw-bold">
                                                    <span class="">{{$ownUser_global_company['jobsPosted']/2}}</span>
                                                </td>
                                                <td class="">
                                                    <span class="text-success">+{{$ownUser_global_company['jobsPosted']}}</span>
                                                </td>
                                            </tr>  
                                            <tr>
                                                <td>
                                                    <a href="#" class="text-gray-700 text-hover-primary mb-1 fs-6">Post Job to Vendor Market</a>
                                                </td>
                                                <td class="text-muted fw-bold">
                                                    +2                                    
                                                </td>
                                                 <td class="text-muted fw-bold">
                                                    <span class="">{{$ownUser_global_company['postJobToVendorMarket']/2}}</span>
                                                </td>
                                                <td class="">
                                                    <span class="text-success">+{{$ownUser_global_company['postJobToVendorMarket']}}</span>
                                                </td>
                                            </tr>  
                                            <tr>
                                                <td>
                                                    <a href="#" class="text-gray-700 text-hover-primary mb-1 fs-6">Outsource Jobs to Vendors</a>
                                                </td>
                                                <td class="text-muted fw-bold">
                                                    +5                                    
                                                </td>
                                                 <td class="text-muted fw-bold">
                                                    <span class="">{{$ownUser_global_company['outsourceJobsToVendors']/5}}</span>
                                                </td>
                                                <td class="">
                                                    <span class="text-success">+{{$ownUser_global_company['outsourceJobsToVendors']}}</span>
                                                </td>
                                            </tr>  
                                            <tr>
                                                <td>
                                                    <a href="#" class="text-gray-700 text-hover-primary mb-1 fs-6">List Bench Candidates</a>
                                                </td>
                                                <td class="text-muted fw-bold">
                                                    +3                                    
                                                </td>
                                                 <td class="text-muted fw-bold">
                                                    <span class="">{{$ownUser_global_company['listBenchCandidates']/3}}</span>
                                                </td>
                                                <td class="">
                                                    <span class="text-success">+{{$ownUser_global_company['listBenchCandidates']}}</span>
                                                </td>
                                            </tr>   
                                            <tr>
                                                <td>
                                                    <a href="#" class="text-gray-700 text-hover-primary mb-1 fs-6">Respond to Bench Requests</a>
                                                </td>
                                                <td class="text-muted fw-bold">
                                                    +4                                    
                                                </td>
                                                 <td class="text-muted fw-bold">
                                                    <span class="">{{$ownUser_global_company['respondToBenchRequests']/4}}</span>
                                                </td>
                                                <td class="">
                                                    <span class="text-success">+{{$ownUser_global_company['respondToBenchRequests']}}</span>
                                                </td>
                                            </tr> 
                                            <tr style="background: #d1ccd3;">
                                              <td colspan="3">
                                                    <a href="#" class="text-gray-900 fw-bold text-hover-primary mb-1 fs-3">Final Score</a>
                                                </td>
                                                <td class="">
                                                    <span class="fs-3">{{$ownUser_global_company['score']}}</span>
                                                </td>
                                            </tr>                                  
                                    </tbody>
                                    <!--end::Table body-->
                                </table>
                            </div>
            </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="your_rank_global" tabindex="-1" aria-hidden="true" >
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content custmbg" style="border-radius:18px;">
            <div class="modal-body sb-wrap" style="padding:20px 24px 24px;">
                @include('service_company.dashboard._scoreboard', ['U'=>$ownUser_global ?? null, 'totalCount'=>($recruiterMetaGlobal['count'] ?? 0), 'mode'=>'global', 'otherRank'=>($ownUser['rank'] ?? '-'), 'month'=>date('M Y', strtotime($rankstartOfMonth ?? 'now')), 'leaders'=>($topUsers_rank_global ?? collect())])
            </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="your_rank_company" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content custmbg" style="border-radius:18px;">
            <div class="modal-body sb-wrap" style="padding:20px 24px 24px;">
                @include('service_company.dashboard._scoreboard', ['U'=>$ownUser ?? null, 'totalCount'=>($recruiterMeta['count'] ?? 0), 'mode'=>'internal', 'otherRank'=>($ownUser_global['rank'] ?? '-'), 'month'=>date('M Y', strtotime($rankstartOfMonth ?? 'now')), 'leaders'=>($topUsers_rank ?? collect())])
            </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="your_rank_current" tabindex="-1" aria-hidden="true" >
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content custmbg">
            <div class="modal-header flex-stack" style="position: relative;">
                <h2>Score Board This Month</h2>
                <div class="btn btn-sm btn-icon btn-active-color-primary data-bs-dismiss" data-bs-dismiss="modal"> <span class="svg-icon svg-icon-1">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                    <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1" transform="rotate(-45 6 17.3137)" fill="black" />
                    <rect x="7.41422" y="6" width="16" height="2" rx="1" transform="rotate(45 7.41422 6)" fill="black" />
                </svg>
                </span> </div>
            </div>
            <div class="modal-body scroll-y mx-5 mx-xl-18 pt-0 pb-15">
                            <div class="table-responsive">
                                <!--begin::Table-->
                                <table class="table table-row-dashed table-row-gray-200 align-middle gs-0 gy-4">
                                    <!--begin::Table head-->
                                    <thead>
                                        <tr class="border-0">
                                            <th class="p-0 min-w-150px">Events</th>
                                            <th class="p-0 min-w-150px">Points</th>
                                            <th class="p-0 min-w-140px">Received</th>
                                        </tr>
                                    </thead>
                                    <!--end::Table head-->

                                    <!--begin::Table body-->
                                    <tbody>   
                                            <tr style="background: #f8f3f396;">
                                                <td colspan="3">
                                                    <a href="#" class="text-gray-900 fw-bold text-hover-primary mb-1 fs-6">As a individual performance</a>
                                                </td>
                                                <td class=
                                            </tr>
                                            <tr>
                                                <td>
                                                    <a href="#" class="text-gray-700 text-hover-primary mb-1 fs-6">InReview</a>
                                                </td>
                                                <td class="text-muted fw-bold">
                                                    +1                                    
                                                </td>
                                                <td class="">
                                                    <span class="text-success">+{{$ownUser_current['profileUpload']}}</span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <a href="#" class="text-gray-700 text-hover-primary mb-1 fs-6">Sourcing</a>
                                                </td>
                                                <td class="text-muted fw-bold">
                                                    +1                                    
                                                </td>
                                                 <td class="text-muted fw-bold">
                                                    <span class="">{{$ownUser_current['sourcing']/1}}</span>
                                                </td>
                                                <td class="">
                                                    <span class="text-success">+{{$ownUser_current['sourcing']}}</span>
                                                </td>
                                            </tr> 
                                            <tr>
                                                <td>
                                                    <a href="#" class="text-gray-700 text-hover-primary mb-1 fs-6">Screening</a>
                                                </td>
                                                <td class="text-muted fw-bold">
                                                    +1                                    
                                                </td>
                                                 <td class="text-muted fw-bold">
                                                    <span class="">{{$ownUser_current['screening']/1}}</span>
                                                </td>
                                                <td class="">
                                                    <span class="text-success">+{{$ownUser_current['screening']}}</span>
                                                </td>
                                            </tr> 
                                            <tr>
                                                <td>
                                                    <a href="#" class="text-gray-700 text-hover-primary mb-1 fs-6">Evaluation</a>
                                                </td>
                                                <td class="text-muted fw-bold">
                                                    +1                                    
                                                </td>
                                                 <td class="text-muted fw-bold">
                                                    <span class="">{{$ownUser_current['evaluation']/1}}</span>
                                                </td>
                                                <td class="">
                                                    <span class="text-success">+{{$ownUser_current['evaluation']}}</span>
                                                </td>
                                            </tr> 
                                            <tr>
                                                <td>
                                                    <a href="#" class="text-gray-700 text-hover-primary mb-1 fs-6">Negotiation</a>
                                                </td>
                                                <td class="text-muted fw-bold">
                                                    +1                                    
                                                </td>
                                                 <td class="text-muted fw-bold">
                                                    <span class="">{{$ownUser_current['negotiation']/1}}</span>
                                                </td>
                                                <td class="">
                                                    <span class="text-success">+{{$ownUser_current['negotiation']}}</span>
                                                </td>
                                            </tr> 
                                            <tr>
                                                <td>
                                                    <a href="#" class="text-gray-700 text-hover-primary mb-1 fs-6">Offers Released</a>
                                                </td>
                                                <td class="text-muted fw-bold">
                                                    +300                                  
                                                </td>
                                                <td class="">
                                                    <span class="text-success">+{{$ownUser_current['offerReleased']}}</span>
                                                </td>
                                            </tr>  
                                            <tr>
                                                <td>
                                                    <a href="#" class="text-gray-700 text-hover-primary mb-1 fs-6">Joined & OnBoarded</a>
                                                </td>
                                                <td class="text-muted fw-bold">
                                                    +500                                   
                                                </td>
                                                <td class="">
                                                    <span class="text-success">+{{$ownUser_current['candidateJoined']}}</span>
                                                </td>
                                            </tr> 
                                            <tr>
                                                <td>
                                                    <a href="#" class="text-gray-700 text-hover-primary mb-1 fs-6">Joined & Backed Out</a>
                                                </td>
                                                <td class="text-muted fw-bold">
                                                    -500                                    
                                                </td>
                                                 <td class="text-muted fw-bold">
                                                    <span class="">{{$ownUser_current['backout']/1500}}</span>
                                                </td>
                                                <td class="">
                                                    <span class="text-danger">{{$ownUser_current['backout']}}</span>
                                                </td>
                                            </tr>   
                                            <tr>
                                                <td>
                                                    <a href="#" class="text-gray-700 text-hover-primary mb-1 fs-6">Interviews Conducted</a>
                                                </td>
                                                <td class="text-muted fw-bold">
                                                    +10                                   
                                                </td>
                                                <td class="">
                                                    <span class="text-success">+{{$ownUser_current['interviewScheduled']}}</span>
                                                </td>
                                            </tr>  
                                            <tr>
                                                <td>
                                                    <a href="#" class="text-gray-700 text-hover-primary mb-1 fs-6">Interview NoShow</a>
                                                </td>
                                                <td class="text-muted fw-bold">
                                                    -5                                    
                                                </td>
                                                <td class="">
                                                    <span class="text-danger">{{($ownUser_current['interviewNoShow']) ? '' : '-'}}{{$ownUser_current['interviewNoShow']}}</span>
                                                </td>
                                            </tr> 

                                            <tr style="background: #f8f3f396;">
                                                <td colspan="3">
                                                    <a href="#" class="text-gray-900 fw-bold text-hover-primary mb-1 fs-6">As a Team Captain performance</a>
                                                </td>
                                                <td class=
                                            </tr>
                                            {{-- <tr>
                                                <td>
                                                    <a href="#" class="text-gray-700 text-hover-primary mb-1 fs-6">Interested Candidates</a>
                                                </td>
                                                <td class="text-muted fw-bold">
                                                    +2                            
                                                </td>
                                                <td class="">
                                                    <span class="text-success">+{{$ownUser_current['interestedCandidates']}}</span>
                                                </td>
                                            </tr> 
                                             <tr>
                                                <td>
                                                    <a href="#" class="text-gray-700 text-hover-primary mb-1 fs-6">NotInterested Candidates</a>
                                                </td>
                                                <td class="text-muted fw-bold">
                                                    -1                              
                                                </td>
                                                <td class="">
                                                    <span class="text-success">+{{$ownUser_current['notInterestedCandidates']}}</span>
                                                </td>
                                            </tr>  --}}
                                             {{-- <tr>
                                                <td>
                                                    <a href="#" class="text-gray-700 text-hover-primary mb-1 fs-6"> InterviewNoShow</a>
                                                </td>
                                                <td class="text-muted fw-bold">
                                                    -1                              
                                                </td>
                                                <td class="">
                                                    <span class="text-success">+{{$ownUser_current['teamCaptainInterviewNoShow']}}</span>
                                                </td>
                                            </tr>  --}}
                                             <tr>
                                                <td>
                                                    <a href="#" class="text-gray-700 text-hover-primary mb-1 fs-6"> Offer Released</a>
                                                </td>
                                                <td class="text-muted fw-bold">
                                                    +20                               
                                                </td>
                                                <td class="">
                                                    <span class="text-success">+{{$ownUser_current['teamCaptainOfferReleased']}}</span>
                                                </td>
                                            </tr> 
                                             <tr>
                                                <td>
                                                    <a href="#" class="text-gray-700 text-hover-primary mb-1 fs-6"> Joined & OnBoarded</a>
                                                </td>
                                                <td class="text-muted fw-bold">
                                                    +30                               
                                                </td>
                                                <td class="">
                                                    <span class="text-success">+{{$ownUser_current['teamCaptainCandidateJoined']}}</span>
                                                </td>
                                            </tr>  
                                            <tr>
                                                <td>
                                                    <a href="#" class="text-gray-700 text-hover-primary mb-1 fs-6">Joined & Backed Out</a>
                                                </td>
                                                <td class="text-muted fw-bold">
                                                    -30                                    
                                                </td>
                                                 <td class="text-muted fw-bold">
                                                    <span class="">{{$ownUser_current['backoutTeam']/30}}</span>
                                                </td>
                                                <td class="">
                                                    <span class="text-danger">{{$ownUser_current['backoutTeam']}}</span>
                                                </td>
                                            </tr>
                                             <tr>
                                                <td>
                                                    <a href="#" class="text-gray-700 text-hover-primary mb-1 fs-6"> Interviews Conducted</a>
                                                </td>
                                                <td class="text-muted fw-bold">
                                                    +2                             
                                                </td>
                                                <td class="">
                                                    <span class="text-success">+{{$ownUser_current['teamCaptainInterviewScheduled']}}</span>
                                                </td>
                                            </tr> 
                                             <tr style="background: #d1ccd3;">
                                              <td colspan="2">
                                                    <a href="#" class="text-gray-900 fw-bold text-hover-primary mb-1 fs-3">Final Score</a>
                                                </td>
                                                <td class="">
                                                    <span class="fs-3">{{$ownUser_current['score']}}</span>
                                                </td>
                                            </tr> 
                                                                           
                                    </tbody>
                                    <!--end::Table body-->
                                </table>
                            </div>
            </div>
            </div>
        </div>
    </div>


    @if($global_company_type==3);
        @if($ownUser_global['rank'] <= 3)
            <div id="animation-container">
                <div class="congrats-message" id="congratsText">🏆 Congratulations! 🏆</div>
            </div>
        @endif
    @else
        @if($ownUser['rank'] <= 3)
            <div id="animation-container">
                <div class="congrats-message" id="congratsText">🏆 Congratulations! 🏆</div>
            </div>
            <audio id="congratsSound" src="https://www.soundjay.com/human/sounds/applause-8.mp3" preload="auto"></audio>
        @endif
    @endif

    @php
    if($global_company_type==3){
        $topRankShow = $ownUser_global['rank'];
    }else{
        $topRankShow = $ownUser['rank'];
    }

    @endphp


@endsection








@push('footer-script')
    <script src="{{ asset('assets/plugins/jQueryUI/jquery-ui.min.js') }}{{ TM_VERSION }}"></script>
    <script>

       

      

        const colors = ['#ff4081', '#3f51b5', '#4caf50', '#ff9800', '#00bcd4'];
        const container = document.getElementById('animation-container');

        const rank = "{{$topRankShow}}"; // 👈 You can set this dynamically in JS
        const congratsText = document.getElementById("congratsText");
        congratsText.innerHTML = `🏆 Congratulations! You achieved 🏆 <br><strong>RANK #${rank}</strong>`;

        // const sound = document.getElementById('congratsSound');
        //     sound.play().catch((e) => {
        //     console.warn("Autoplay blocked. Sound will play on first user interaction.");
        //     document.addEventListener('click', () => {
        //         sound.play();
        //     }, { once: true });
        //  });



        // Generate balloons
        for (let i = 0; i < 20; i++) {
            let b = document.createElement('div');
            b.className = 'balloon';
            b.style.left = Math.random() * 100 + 'vw';
            b.style.backgroundColor = colors[Math.floor(Math.random() * colors.length)];
            b.style.animationDuration = (6 + Math.random() * 4) + 's';
            container.appendChild(b);
        }

        // Generate confetti
        for (let i = 0; i < 50; i++) {
            let c = document.createElement('div');
            c.className = 'confetti';
            c.style.left = Math.random() * 100 + 'vw';
            c.style.backgroundColor = colors[Math.floor(Math.random() * colors.length)];
            c.style.animationDuration = (2 + Math.random() * 2) + 's';
            c.style.width = Math.random() * 5 + 5 + 'px';
            c.style.height = c.style.width;
            container.appendChild(c);
        }

        // Remove animation container after 10 seconds
        setTimeout(() => {
            container.remove(); // ✅ Only removes the animation
        }, 10000);


        


    </script>

    

    <script>

         const showDataLabels = {
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


        const showDataLabelsvertical = {
            id: 'showDataLabelsvertical',
            afterDatasetsDraw(chart) {
                const { ctx } = chart;
                chart.data.datasets.forEach((dataset, i) => {
                    const meta = chart.getDatasetMeta(i);
                    meta.data.forEach((bar, index) => {
                        const value = dataset.data[index];
                        ctx.save();
                        ctx.fillStyle = 'black';
                        ctx.font = 'bold 12px sans-serif';
                        ctx.textAlign = 'left';  // align left for horizontal bars
                        ctx.textBaseline = 'middle';

                        // place text at the end of bar (right side)
                        ctx.fillText(value, bar.x + 1, bar.y);
                        ctx.restore();
                    });
                });
            }
        };


        // Get the canvas element
        const ctx_job_div = document.getElementById('allJobGrapg').getContext('2d');
    
        // Create the donut chart
        const allJobGrapg = new Chart(ctx_job_div, {
            type: 'doughnut',
            data: {
                labels: ['Active Jobs', 'Hold Jobs', 'Closed Jobs'], // Labels for the chart
                datasets: [{
                    label: 'Total',
                    data: ['{{ $active_count }}', '{{ $hold_count }}', '{{ $inactive_count }}'], // Values for the chart
                    backgroundColor: [
                        '#22c55e', // Green for 'Active Jobs'
                        '#ffab00', // Blue for 'Hold Jobs'
                        '#ff0202'  // Gray for 'Closed Jobs'
                    ],
                    hoverOffset: 4 // Highlight on hover
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false // Hide the legend in the chart area
                    }
                },
                cutout: '70%' // To create the donut hole
            }
        });
    </script>


    <script>
            const ctx = document.getElementById('myChart').getContext('2d');

                const countryChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: ['In Review','Sourcing', 'Screening', 'Evaluation', 'Negotiation', 'Offer','Joined'],
                    datasets: [{
                        label: 'Users',
                        data: ["{{$inreview_count}}", "{{$sourcing_count}}", "{{$screening_count}}", "{{$evaluation_count}}", "{{$negotiation_count}}", "{{$offer_count}}", "{{$joined_count}}"],
                        backgroundColor: [
                            '#bdbd26', // inreview_count
                            '#000000', // sourcing_count
                            '#c000ff', // screening_count
                            '#2896ff', // evaluation_count
                            '#ff4098', // negotiation_count
                            '#ffab00', // offer_count
                            '#06c732' // joined_count
                        ],
                        borderRadius: 10,
                        barThickness: 30
                    }]
                },
                options: {
                    indexAxis: 'y', // makes it horizontal
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: ctx => ctx.parsed.x.toLocaleString()
                            }
                        }
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
                        y: {
                            ticks: {
                                font: {
                                    weight: 'bold'
                                }
                            }
                        }
                    }
                },
                plugins: [showDataLabelsvertical] // ✅ custom labels plugin
            });

            
    </script>


     <script>
        const vctx = document.getElementById('gaugeChart').getContext('2d');

        new Chart(vctx, {
            type: 'doughnut',
            data: {
                datasets: [{
                    data: ["{{$actual_count}}", "{{$remain_coins}}"], // 75% filled
                    backgroundColor: ['#122ef5', '#E2E8F0'], // blue + light gray
                    borderWidth: 0
                }]
            },
            options: {
                rotation: -90,
                circumference: 180,
                cutout: '85%',
                responsive: true,
                plugins: {
                    legend: { display: false },
                    tooltip: { enabled: false },
                }
            }
        });
        
    </script>

    <script>
        const vctxs = document.getElementById('gaugeChartSales').getContext('2d');

        new Chart(vctxs, {
            type: 'doughnut',
            data: {
                datasets: [{
                    data: ["{{$actual_count_sales}}", "{{$remain_coins_sales}}"], // 75% filled
                    backgroundColor: ['#24ef21', '#E2E8F0'], // blue + light gray
                    borderWidth: 0
                }]
            },
            options: {
                rotation: -90,
                circumference: 180,
                cutout: '85%',
                responsive: true,
                plugins: {
                    legend: { display: false },
                    tooltip: { enabled: false },
                }
            }
        });
        
    </script>




   <script>
    const team_left_chart = document.getElementById('team_left_chart').getContext('2d');

    const teamLeftChart = new Chart(team_left_chart, {
        type: 'bar',
        data: {
            labels: {!! json_encode($teamMonthlyLabels) !!},
            datasets: [{
                label: 'Total Joined',
                data: {!! json_encode($tealMonthlyValues) !!},
                backgroundColor: [
                    '#06c732', // Blue 
                    '#06c732', // Blue 
                    '#06c732', // Blue 
                    '#06c732', // Blue  
                ],
                borderRadius: 5,
                borderSkipped: false,
                barThickness: 30
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            indexAxis: 'y', // Ensure vertical bars (default, optional)
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        precision: 0 // show integer values only
                    }
                }
            }
        },
        
    });
   </script>

   <script>
    const team_right_chart = document.getElementById('team_right_chart').getContext('2d');

    const teamRightChart = new Chart(team_right_chart, {
        type: 'bar',
        data: {
            labels: {!! json_encode($teamDailyLabels) !!},
            datasets: [{
                label: 'Total Candidates',
                data: {!! json_encode($tealDailyValues) !!},
                backgroundColor: [
                    '#2196f3', // Blue 
                    '#2196f3', // Blue 
                    '#2196f3', // Blue 
                    '#2196f3', // Blue 
                ],
                borderRadius: 5,
                borderSkipped: false,
                barThickness: 30
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            indexAxis: 'y', // Ensure vertical bars (default, optional)
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        precision: 0 // show integer values only
                    }
                }
            }
        },
         
    });
   </script>

    <script>
    const vendor_left_chart = document.getElementById('vendor_left_chart').getContext('2d');

    const vendorLeftChart = new Chart(vendor_left_chart, {
        type: 'bar',
        data: {
            labels: {!! json_encode($staffingMonthlyLabels) !!},
            datasets: [{
                label: 'Total Joined',
                data: {!! json_encode($staffingMonthlyValues) !!},
                backgroundColor: [
                    '#06c732', // Blue 
                    '#06c732', // Blue 
                    '#06c732', // Blue 
                    '#06c732', // Blue  
                ],
                borderRadius: 5,
                borderSkipped: false,
                barThickness: 30
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            indexAxis: 'y', // Ensure vertical bars (default, optional)
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        precision: 0 // show integer values only
                    }
                }
            }
        },
         
    });
   </script>

   <script>
    const vendor_right_chart = document.getElementById('vendor_right_chart').getContext('2d');

    const vendorRightChart = new Chart(vendor_right_chart, {
        type: 'bar',
        data: {
            labels: {!! json_encode($staffingDailyLabels) !!},
            datasets: [{
                label: 'Total Candidates',
                data: {!! json_encode($staffingDailyValues) !!},
                backgroundColor: [
                    '#2196f3', // Blue 
                    '#2196f3', // Blue 
                    '#2196f3', // Blue 
                    '#2196f3', // Blue 
                ],
                borderRadius: 5,
                borderSkipped: false,
                barThickness: 30
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            indexAxis: 'y', // Ensure vertical bars (default, optional)
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        precision: 0 // show integer values only
                    }
                }
            }
        },
         
    });
   </script>




   <script>
    const candidates_left_chart = document.getElementById('candidates_left_chart').getContext('2d');

    const allJobGraphBar = new Chart(candidates_left_chart, {
        type: 'bar',
        data: {
            labels: {!! json_encode($labels) !!},
            datasets: [{
                label: 'Total Candidates',
                data: {!! json_encode($candidateValues) !!},
                backgroundColor: [
                    '#2196f3', // Blue 
                    '#2196f3', // Blue 
                    '#2196f3', // Blue 
                    '#2196f3', // Blue 
                    '#2196f3', // Blue 
                    '#2196f3', // Blue 
                    '#2196f3'  // Blue 
                ],
                borderRadius: 5,
                borderSkipped: false,
                barThickness: 30
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        precision: 0 // show integer values only
                    }
                }
            }
        },
         plugins: [showDataLabels] // ✅ custom labels plugin
    });
   </script>

   <script>
    const candidates_join_chart = document.getElementById('candidates_join_chart').getContext('2d');

    const alljoinGraphBar = new Chart(candidates_join_chart, {
        type: 'bar',
        data: {
            labels: {!! json_encode($labelsCommjoin) !!},
            datasets: [{
                label: 'Total Candidates',
                data: {!! json_encode($candidateValuesCommjoin) !!},
                backgroundColor: [
                    '#06c732',  
                    '#06c732',  
                    '#06c732',  
                    '#06c732',  
                    '#06c732',  
                    '#06c732'  
                ],
                borderRadius: 5,
                borderSkipped: false,
                barThickness: 30
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        precision: 0 // show integer values only
                    }
                }
            }
        },
         plugins: [showDataLabels] // ✅ custom labels plugin
    });
   </script>

   <script>
    const candidates_offer_chart = document.getElementById('candidates_offer_chart').getContext('2d');

    const allofferGraphBar = new Chart(candidates_offer_chart, {
        type: 'bar',
        data: {
            labels: {!! json_encode($labelsCommoffer) !!},
            datasets: [{
                label: 'Total Candidates',
                data: {!! json_encode($candidateValuesCommoffer) !!},
                backgroundColor: [
                    '#ffab00', 
                    '#ffab00', 
                    '#ffab00', 
                    '#ffab00', 
                    '#ffab00', 
                    '#ffab00' 
                ],
                borderRadius: 5,
                borderSkipped: false,
                barThickness: 30
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        precision: 0 // show integer values only
                    }
                }
            }
        },
         plugins: [showDataLabels] // ✅ custom labels plugin
    });
   </script>

   

    <script>

    const inetview_recent_chart = document.getElementById('inetview_recent_chart').getContext('2d');

    const netview_recentGraphBar = new Chart(inetview_recent_chart, {
        type: 'bar',
        data: {
            labels: {!! json_encode($interviewlabels) !!},
            datasets: [{
                label: 'Total Interviews',
                data: {!! json_encode($interviewValues) !!},
                backgroundColor: [
                    '#2196f3', // Blue 
                    '#2196f3', // Blue 
                    '#2196f3', // Blue 
                    '#2196f3', // Blue 
                    '#2196f3', // Blue 
                    '#2196f3', // Blue 
                    '#2196f3'  // Blue 
                ],
                borderRadius: 5,
                borderSkipped: false,
                barThickness: 30
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        precision: 0 // show integer values only
                    }
                }
            }
        },
        plugins: [showDataLabels] // ✅ custom labels plugin
    });
   </script>


<script>

    const inetview_recent_chart_month = document.getElementById('inetview_recent_chart_month').getContext('2d');

    const netview_recentGraphBarMonth = new Chart(inetview_recent_chart_month, {
        type: 'bar',
        data: {
            labels: {!! json_encode($interviewlabelsMonth) !!},
            datasets: [{
                label: 'Total Interviews',
                data: {!! json_encode($interviewValuesMonth) !!},
                backgroundColor: [
                    '#2196f3', // Blue 
                    '#2196f3', // Blue 
                    '#2196f3', // Blue 
                    '#2196f3', // Blue 
                    '#2196f3', // Blue 
                    '#2196f3', // Blue 
                    '#2196f3'  // Blue 
                ],
                borderRadius: 5,
                borderSkipped: false,
                barThickness: 30
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        precision: 0 // show integer values only
                    }
                }
            }
        },
        plugins: [showDataLabels] // ✅ custom labels plugin
    });
   </script>



<script>

    const dataSource = {
        chart: {
            caption: "",
            subcaption: "",
            theme: "candy",
            is2d: "0",
            ishollow: "0",
            usesameslantangle: "0",
            plottooltext: "$label: <b>$percentValue ($dataValue)</b>",
            showvalues: "1",
            bgColor: "#ffffff",        // or "transparent"
            showBorder: "0",
            showCanvasBorder: "0",
            canvasBgColor: "#ffffff", // or "transparent"
            canvasBorderThickness: "0",
            decimals: "1"
        },
        data: {!! json_encode($funnel_chart) !!}
        };

        FusionCharts.ready(function() {
        var myChart = new FusionCharts({
            type: "funnel",
            renderAt: "chart-container",
            width: "100%",
            height: "100%",
            dataFormat: "json",
            dataSource
        }).render();
        });

</script>


   

    

    <script>
        function current_date() {
            var myDiv = document.getElementById("current_date");

            var date = new Date();
            var dayList = ["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"];
            var monthNames = ["January", "February", "March", "April", "May", "June", "July", "August", "September",
                "October", "November", "December"
            ];
            var dayName = dayList[date.getDay()];
            var monthName = monthNames[date.getMonth()];
            var today = `${dayName}, ${date.getDate()} ${monthName}, ${date.getFullYear()}`;

            var hour = date.getHours();
            var min = date.getMinutes();
            var sec = date.getSeconds();

            var time = hour + ":" + min + ":" + sec;
            myDiv.innerText = `${today}`;

        }
        window.onload = function () {
            setInterval(current_date, 1000);
        };
        // setInterval(current_date, 1000);

        function current_time() {
            var myDiv = document.getElementById("current_time");

            var now = new Date();

            var then = new Date("{!! @$user->last_login !!}");
            var minsDiff = Math.floor((now.getTime() - then.getTime()) / 1000 / 60);
            var hoursDiff = Math.floor(minsDiff / 60);
            minsDiff = minsDiff % 60;
            var titme = hoursDiff + ' Hours and ' + minsDiff + ' Miniute ago';

            myDiv.innerText = `${titme}`;
        }
        
        window.onload = function () {
            setInterval(current_time, 1000);
        };
        // setInterval(current_time, 1000);
    </script>
    <script>
        var updated = true;

        function showNewTodoForm() {
            let url = "{{ route('service_company.todo-items.create') }}"

            $.ajaxModal('#application-md-modal', url);
        }

        function initSortable() {
            let updates = {
                'pending-tasks': false,
                'completed-tasks': false
            };
            let completedFirstPosition = $('#completed-tasks').find('li.draggable').first().data('position');
            let pendingFirstPosition = $('#pending-tasks').find('li.draggable').first().data('position');

            $('#pending-tasks').sortable({
                connectWith: '#completed-tasks',
                cursor: 'move',
                handle: '.handle',
                stop: function(event, ui) {
                    const id = ui.item.data('id');
                    const oldPosition = ui.item.data('position');

                    if (updates['pending-tasks'] === true && updates['completed-tasks'] === true) {
                        const inverseIndex = completedFirstPosition > 0 ? completedFirstPosition - ui.item
                            .index() + 1 : 1;
                        const newPosition = inverseIndex;

                        updateTodoItem(id, position = {
                            oldPosition,
                            newPosition
                        }, status = 'completed');

                    } else if (updates['pending-tasks'] === true && updates['completed-tasks'] === false) {
                        const newPosition = pendingFirstPosition - ui.item.index();

                        updateTodoItem(id, position = {
                            oldPosition,
                            newPosition
                        });
                    }

                    updates['pending-tasks'] = false;
                    updates['completed-tasks'] = false;
                },
                update: function(event, ui) {
                    updates[$(this).attr('id')] = true;
                }
            }).disableSelection();

            $('#completed-tasks').sortable({
                connectWith: '#pending-tasks',
                cursor: 'move',
                handle: '.handle',
                stop: function(event, ui) {
                    const id = ui.item.data('id');
                    const oldPosition = ui.item.data('position');

                    if (updates['pending-tasks'] === true && updates['completed-tasks'] === true) {
                        const inverseIndex = pendingFirstPosition > 0 ? pendingFirstPosition - ui.item.index() +
                            1 : 1;
                        const newPosition = inverseIndex;

                        updateTodoItem(id, position = {
                            oldPosition,
                            newPosition
                        }, status = 'pending');
                    } else if (updates['pending-tasks'] === false && updates['completed-tasks'] === true) {
                        const newPosition = completedFirstPosition - ui.item.index();

                        updateTodoItem(id, position = {
                            oldPosition,
                            newPosition
                        });
                    }
                    updates['pending-tasks'] = false;
                    updates['completed-tasks'] = false;
                },
                update: function(event, ui) {
                    updates[$(this).attr('id')] = true;
                }
            }).disableSelection();
        }


        function filtersdatagetmonth(valSelected){
            if(valSelected==1){
                  $('.inetview_recent_chart_month').removeClass('d-none');
                  $('.inetview_recent_chart').addClass('d-none');
            }else{
                  $('.inetview_recent_chart_month').addClass('d-none');
                  $('.inetview_recent_chart').removeClass('d-none');
            }

        }



        function showUpdateTodoForm(id) {
            let url = "{{ route('service_company.todo-items.edit', ':id') }}"
            url = url.replace(':id', id);

            $.ajaxModal('#application-md-modal', url);
        }

        @if ($user->roles->count() > 0)
            $('#todo-items-list').html(`{!! $todoItemsView !!}`);
        @endif

        initSortable();

        $('body').on('change', '#todo-items-list input[name="status"]', function() {
            const id = $(this).data('id');
            let status = 'pending';

            if ($(this).is(':checked')) {
                status = 'completed';
            }

            updateTodoItem(id, null, status);
        })

        // Score Board -> Export Report (browser print / Save as PDF). Dynamic, zero server load.
        window.sbExportPdf = function (el) {
            var modal = el.closest('.modal');
            var isGlobal = modal && modal.id === 'your_rank_global';
            var prevTitle = document.title;
            var label = isGlobal ? 'Global' : 'Internal';
            document.title = 'Score-Board-' + label + '-' + new Date().toISOString().slice(0, 10);
            var done = function () { document.title = prevTitle; window.removeEventListener('afterprint', done); };
            window.addEventListener('afterprint', done);
            window.print();
            setTimeout(done, 1500);
        };

    </script>
@endpush
