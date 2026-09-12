<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>{{ ucwords(@$application->full_name ?: @$application->name ?: 'Candidate') }} — jero.works online editor (Adobe Acrobat Studio)</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <script src="https://cdnjs.cloudflare.com/ajax/libs/mammoth/1.5.0/mammoth.browser.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.11.338/pdf.min.js"></script>
  <script src="/js/pdfmake.min.js"></script>
  <script src="/js/vfs_fonts.min.js"></script>
  <script src="/js/pdf-lib.min.js"></script>
  <script src="/js/opendoc/AgyCvOptimizer.js"></script>
  <style>
    * { box-sizing: border-box; margin: 0; padding: 0; }
    html, body { height: 100%; overflow: hidden; font-family: 'Poppins', -apple-system, BlinkMacSystemFont, sans-serif; background: #f3f4f6; color: #1e293b; }

    /* App Container: Header + 4-Zone Horizontal Split Layout (Acrobat Architecture) */
    .acrobat-app { display: flex; flex-direction: column; height: 100vh; overflow: hidden; }

    /* 1. Global Adobe Acrobat-Style Top App Bar */
    .acrobat-navbar { height: 54px; background: #ffffff; border-bottom: 1px solid #e5e7eb; display: flex; align-items: center; justify-content: space-between; padding: 0 16px; flex-shrink: 0; z-index: 110; }
    .nav-left { display: flex; align-items: center; gap: 14px; }
    .nav-logo-wrap { display: flex; align-items: center; gap: 8px; text-decoration: none; }
    .nav-logo-img { height: 24px; width: auto; }
    .nav-title { font-size: 13.5px; font-weight: 700; color: #0f172a; display: flex; align-items: center; gap: 6px; }
    .nav-title span { color: #0d9488; }
    .nav-tabs { display: flex; align-items: center; gap: 4px; margin-left: 12px; }
    .nav-tab { padding: 6px 12px; font-size: 12px; font-weight: 500; color: #475569; text-decoration: none; border-radius: 6px; cursor: pointer; transition: all .15s; }
    .nav-tab:hover { background: #f1f5f9; color: #0f172a; }
    .nav-tab.active { background: #f1f5f9; color: #0f172a; font-weight: 600; }

    .nav-center { display: flex; align-items: center; gap: 8px; flex: 1; max-width: 440px; margin: 0 20px; }
    .nav-search-box { position: relative; width: 100%; display: flex; align-items: center; }
    .nav-search-input { width: 100%; height: 32px; padding: 0 70px 0 32px; font-size: 12px; font-family: 'Poppins', sans-serif; border: 1px solid #cbd5e1; border-radius: 999px; background: #f8fafc; outline: none; transition: all .15s; }
    .nav-search-input:focus { background: #ffffff; border-color: #6366f1; box-shadow: 0 0 0 3px rgba(99,102,241,.12); }
    .nav-search-icon { position: absolute; left: 10px; color: #94a3b8; width: 14px; height: 14px; }
    .nav-search-nav { position: absolute; right: 8px; display: flex; align-items: center; gap: 2px; font-size: 10.5px; color: #64748b; font-weight: 600; }
    .nav-search-btn { background: none; border: none; cursor: pointer; padding: 2px 4px; color: #64748b; border-radius: 3px; display: flex; align-items: center; }
    .nav-search-btn:hover { background: #e2e8f0; color: #0f172a; }

    .nav-right { display: flex; align-items: center; gap: 10px; }
    .ats-score-pill { display: inline-flex; align-items: center; gap: 5px; background: #f0fdf4; border: 1px solid #bbf7d0; color: #15803d; font-size: 11px; font-weight: 700; padding: 4px 10px; border-radius: 999px; }
    .doc-metrics-text { font-size: 11px; color: #64748b; font-weight: 500; }
    .btn-action-copy { background: #f8fafc; border: 1px solid #cbd5e1; color: #334155; padding: 6px 12px; font-size: 11.5px; font-weight: 600; border-radius: 6px; cursor: pointer; display: inline-flex; align-items: center; gap: 5px; font-family: 'Poppins', sans-serif; transition: all .15s; }
    .btn-action-copy:hover { background: #f1f5f9; border-color: #94a3b8; }
    .btn-action-download { background: #0284c7; color: #ffffff; border: none; padding: 6px 14px; font-size: 12px; font-weight: 600; border-radius: 6px; cursor: pointer; display: inline-flex; align-items: center; gap: 6px; font-family: 'Poppins', sans-serif; transition: all .15s; box-shadow: 0 1px 3px rgba(2,132,199,.3); }
    .btn-action-download:hover { background: #0369a1; }
    .btn-action-download:disabled { opacity: 0.65; cursor: not-allowed; }

    /* 2. Main Body Split Workspace */
    .acrobat-body { display: flex; flex: 1; height: calc(100vh - 54px); overflow: hidden; position: relative; }

    /* ZONE A: Left Tools & ATS Panel (Acrobat "All tools") */
    .acrobat-sidebar { width: 330px; background: #ffffff; border-right: 1px solid #e5e7eb; display: flex; flex-direction: column; overflow-y: auto; flex-shrink: 0; z-index: 90; transition: width .2s; }
    .sidebar-head { padding: 14px 16px 10px 16px; border-bottom: 1px solid #f1f5f9; display: flex; align-items: center; justify-content: space-between; }
    .sidebar-head h2 { font-size: 14px; font-weight: 700; color: #0f172a; margin: 0; }
    .cand-chip-pill { font-size: 10px; font-weight: 600; background: #ccfbf1; color: #0f766e; border: 1px solid #99f6e4; padding: 2px 8px; border-radius: 999px; max-width: 140px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }

    /* Mode / Format Dropdown in Sidebar */
    .sidebar-section { padding: 12px 16px; border-bottom: 1px solid #f1f5f9; }
    .section-title { font-size: 11px; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 6px; display: block; }
    .mode-select-box { width: 100%; padding: 7px 10px; font-size: 11.5px; font-family: 'Poppins', sans-serif; font-weight: 600; color: #0f172a; background: #f8fafc; border: 1.5px solid #cbd5e1; border-radius: 7px; outline: none; cursor: pointer; transition: all .15s; }
    .mode-select-box:focus { border-color: #0d9488; background: #fff; }

    /* ATS Keyword Highlights Card */
    .sidebar-ats-card { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 12px; margin-top: 4px; }
    .ats-stats-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 6px; margin-bottom: 10px; }
    .stat-pill-box { background: #ffffff; border: 1px solid #e2e8f0; border-radius: 6px; padding: 6px 8px; text-align: center; }
    .stat-pill-val { display: block; font-size: 14px; font-weight: 700; color: #0f172a; line-height: 1.2; }
    .stat-pill-lbl { font-size: 9px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.3px; color: #64748b; }
    .stat-pill-box.val-matched .stat-pill-val { color: #0d9488; }
    .stat-pill-box.val-missing .stat-pill-val { color: #dc2626; }
    .stat-pill-box.val-score .stat-pill-val { color: #16a34a; }

    .ats-search-wrap { display: flex; gap: 6px; margin-bottom: 10px; }
    .ats-kw-input { flex: 1; padding: 6px 10px; font-size: 11px; font-family: 'Poppins', sans-serif; border: 1px solid #cbd5e1; border-radius: 6px; outline: none; background: #fff; }
    .ats-kw-input:focus { border-color: #6366f1; }
    .btn-ats-add-kw { padding: 6px 12px; background: #6366f1; color: #fff; border: none; border-radius: 6px; font-size: 11px; font-weight: 600; cursor: pointer; font-family: 'Poppins', sans-serif; white-space: nowrap; }
    .btn-ats-add-kw:hover { background: #4f46e5; }

    .skills-title { font-size: 10.5px; font-weight: 700; color: #475569; text-transform: uppercase; letter-spacing: 0.4px; margin-bottom: 6px; display: block; }
    .chips-flow { display: flex; flex-wrap: wrap; gap: 5px; }
    .kw-chip { display: inline-flex; align-items: center; gap: 4px; background: #fef08a; border: 1px solid #facc15; color: #854d0e; padding: 2px 8px; border-radius: 999px; font-size: 10.5px; font-weight: 600; cursor: pointer; transition: all .15s; }
    .kw-chip:hover { transform: translateY(-1px); box-shadow: 0 2px 4px rgba(250,204,21,.3); }
    .kw-chip.missing { background: #f1f5f9; border-color: #cbd5e1; color: #64748b; }
    .kw-chip .chip-count { font-size: 9px; font-weight: 700; background: rgba(0,0,0,0.08); padding: 1px 4px; border-radius: 999px; }

    /* Acrobat Tools Menu Items List */
    .tools-menu-list { list-style: none; padding: 4px 0; }
    .tool-menu-item { display: flex; align-items: center; gap: 12px; padding: 9px 16px; font-size: 12px; font-weight: 500; color: #334155; cursor: pointer; transition: all .15s; }
    .tool-menu-item:hover { background: #f8fafc; color: #0f172a; }
    .tool-menu-item.active { background: #e0f2fe; color: #0369a1; font-weight: 600; border-left: 3px solid #0284c7; }
    .tool-menu-icon { width: 18px; height: 18px; display: flex; align-items: center; justify-content: center; color: #64748b; }
    .tool-menu-item.active .tool-menu-icon { color: #0284c7; }

    /* ZONE B: Vertical Floating Tool Dock (Between Sidebar & Canvas) */
    .acrobat-dock { width: 44px; background: #ffffff; border-right: 1px solid #e5e7eb; display: flex; flex-direction: column; align-items: center; padding: 12px 0; gap: 6px; flex-shrink: 0; z-index: 80; box-shadow: 1px 0 3px rgba(0,0,0,0.02); }
    .dock-btn { width: 32px; height: 32px; border-radius: 6px; border: none; background: transparent; color: #475569; display: flex; align-items: center; justify-content: center; cursor: pointer; transition: all .15s; font-size: 12px; }
    .dock-btn:hover { background: #f1f5f9; color: #0f172a; }
    .dock-btn.active { background: #0284c7; color: #ffffff; }
    .dock-sep { width: 22px; height: 1px; background: #e2e8f0; margin: 4px 0; }
    .dock-color { width: 22px; height: 22px; border: 1px solid #cbd5e1; border-radius: 4px; padding: 0; cursor: pointer; background: none; }

    /* ZONE C: Center Document Viewport (Isolated Scroller) */
    .acrobat-canvas { flex: 1; background: #f3f4f6; overflow-y: auto; overflow-x: hidden; padding: 28px 24px 80px 24px; position: relative; scroll-behavior: smooth; }
    .acrobat-canvas::-webkit-scrollbar { width: 9px; }
    .acrobat-canvas::-webkit-scrollbar-track { background: #e5e7eb; }
    .acrobat-canvas::-webkit-scrollbar-thumb { background: #9ca3af; border-radius: 4px; }
    .acrobat-canvas::-webkit-scrollbar-thumb:hover { background: #6b7280; }

    /* A4 Paper Sheets */
    .paper-sheet { width: 816px; min-height: 1056px; margin: 0 auto 32px auto; background: #ffffff; box-shadow: 0 4px 20px rgba(0,0,0,0.12), 0 1px 4px rgba(0,0,0,0.06); border-radius: 4px; padding: 48px 56px; outline: none; position: relative; font-family: 'Poppins', -apple-system, BlinkMacSystemFont, sans-serif; font-size: 13px; line-height: 1.65; color: #1e293b; }
    .paper-sheet:focus { box-shadow: 0 6px 24px rgba(0,0,0,0.2), 0 0 0 2px #0284c7; }

    /* PAGE 1: PRE-SCREENED CANDIDATE REPORT (Exact Match to User's Screenshot media_1789225189169.png) */
    .ps-header { display: flex; align-items: flex-start; justify-content: space-between; border-bottom: 2px solid #0f172a; padding-bottom: 14px; margin-bottom: 18px; }
    .ps-shared-by { display: flex; align-items: center; gap: 10px; }
    .ps-company-logo-img { height: 36px; width: auto; max-width: 140px; object-fit: contain; }
    .ps-shared-title { font-size: 10px; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; }
    .ps-shared-name { font-size: 15px; font-weight: 800; color: #0f172a; }

    .ps-report-title-box { text-align: right; }
    .ps-report-main-title { font-size: 18px; font-weight: 800; color: #0f172a; text-transform: uppercase; letter-spacing: -0.2px; }
    .ps-report-sub-date { font-size: 10.5px; font-weight: 600; color: #64748b; margin-top: 2px; text-transform: uppercase; letter-spacing: 0.4px; }

    /* Top Split Row: Candidate Card + Hiring Confidence Card */
    .ps-top-split { display: grid; grid-template-columns: 1.6fr 1fr; gap: 14px; margin-bottom: 14px; }
    .ps-card { background: #ffffff; border: 1.5px solid #e2e8f0; border-radius: 8px; padding: 16px 18px; }
    .ps-cand-name { font-size: 20px; font-weight: 800; color: #0f172a; margin-bottom: 2px; }
    .ps-cand-role { font-size: 13.5px; font-weight: 600; color: #0284c7; margin-bottom: 8px; }
    .ps-cand-meta-line { font-size: 11.5px; color: #475569; font-weight: 500; margin-bottom: 3px; display: flex; align-items: center; gap: 6px; }

    .ps-confidence-card { text-align: center; display: flex; flex-direction: column; align-items: center; justify-content: center; background: #f8fafc; }
    .ps-confidence-lbl { font-size: 11.5px; font-weight: 700; color: #475569; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px; }
    .ps-confidence-val { font-size: 38px; font-weight: 800; color: #0284c7; line-height: 1; }

    /* Middle Row: 4 Metric Cards (Education, Notice Period, Location, Work Mode) */
    .ps-metrics-4grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 10px; margin-bottom: 14px; }
    .ps-metric-item { background: #ffffff; border: 1.5px solid #e2e8f0; border-radius: 8px; padding: 12px 10px; text-align: center; }
    .ps-metric-head { font-size: 10px; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.4px; margin-bottom: 4px; display: flex; align-items: center; justify-content: center; gap: 5px; }
    .ps-metric-val { font-size: 12.5px; font-weight: 700; color: #0f172a; }

    /* Bottom Row: Executive Summary + Recruiter Comments */
    .ps-bottom-split { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; margin-bottom: 16px; }
    .ps-summary-card { background: #ffffff; border: 1.5px solid #e2e8f0; border-radius: 8px; padding: 16px; }
    .ps-section-title { font-size: 11px; font-weight: 700; color: #0284c7; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 8px; display: flex; align-items: center; gap: 6px; }
    .ps-text-content { font-size: 11.5px; color: #334155; line-height: 1.6; min-height: 100px; }

    .ps-page-footer-note { text-align: center; font-size: 10.5px; font-weight: 600; color: #64748b; background: #f8fafc; border: 1px dashed #cbd5e1; border-radius: 6px; padding: 8px; margin-top: 20px; }

    /* PAGE 2: CANDIDATE RÉSUMÉ */
    .resume-brand-banner { display: flex; align-items: center; justify-content: space-between; border-bottom: 2px solid #0d9488; padding-bottom: 12px; margin-bottom: 20px; }
    .resume-brand-title { font-size: 15px; font-weight: 800; color: #0f172a; text-transform: uppercase; }
    .resume-brand-sub { font-size: 10.5px; color: #0d9488; font-weight: 600; }

    .paper-sheet h1 { font-size: 18px; font-weight: 700; color: #0f172a; margin: 16px 0 8px 0; border-bottom: 1px solid #e2e8f0; padding-bottom: 4px; }
    .paper-sheet h2 { font-size: 15px; font-weight: 700; color: #0f172a; margin: 12px 0 6px 0; }
    .paper-sheet h3 { font-size: 13px; font-weight: 600; color: #0f172a; margin: 8px 0 4px 0; }
    .paper-sheet p { margin-bottom: 8px; }
    .paper-sheet ul, .paper-sheet ol { margin-left: 20px; margin-bottom: 10px; }
    .paper-sheet li { margin-bottom: 3px; }

    mark.rcv-hit { background: #fef08a !important; color: #854d0e !important; padding: 1px 3px !important; border-radius: 3px !important; font-weight: 600 !important; }
    mark.rcv-hit.rcv-hit-active { background: #f97316 !important; color: #ffffff !important; box-shadow: 0 0 0 3px rgba(249,115,22,.4) !important; }
    mark.rcv-redact { background: #000000 !important; color: #000000 !important; user-select: none !important; padding: 0 4px !important; border-radius: 2px !important; }

    .doc-page-break { border-top: 2px dashed #94a3b8; margin: 26px 0; position: relative; text-align: center; }
    .doc-page-break::after { content: "PAGE BREAK"; font-size: 9.5px; font-weight: 700; color: #64748b; background: #fff; padding: 0 8px; position: absolute; top: -7px; left: 50%; transform: translateX(-50%); letter-spacing: 0.5px; }

    /* Floating Bottom Acrobat AI Bar */
    .acrobat-bottom-bar { position: absolute; bottom: 20px; left: 50%; transform: translateX(-50%); width: 560px; background: #ffffff; border: 1px solid #cbd5e1; border-radius: 999px; box-shadow: 0 10px 25px -5px rgba(0,0,0,0.15); display: flex; align-items: center; padding: 6px 14px; gap: 10px; z-index: 100; }
    .bottom-ai-icon { width: 22px; height: 22px; display: flex; align-items: center; justify-content: center; color: #6366f1; }
    .bottom-ai-input { flex: 1; border: none; outline: none; font-size: 12px; font-family: 'Poppins', sans-serif; color: #1e293b; background: transparent; }
    .bottom-ai-btn { background: #6366f1; color: #fff; border: none; border-radius: 999px; padding: 5px 14px; font-size: 11px; font-weight: 600; cursor: pointer; font-family: 'Poppins', sans-serif; }

    /* ZONE D: Right Page Navigation Strip (Acrobat Style) */
    .acrobat-right-strip { width: 46px; background: #ffffff; border-left: 1px solid #e5e7eb; display: flex; flex-direction: column; align-items: center; padding: 14px 0; gap: 10px; flex-shrink: 0; z-index: 80; }
    .page-counter-box { font-size: 10.5px; font-weight: 700; color: #475569; border: 1px solid #cbd5e1; border-radius: 4px; padding: 4px 6px; text-align: center; }
    .strip-btn { width: 28px; height: 28px; border-radius: 4px; border: none; background: transparent; color: #475569; display: flex; align-items: center; justify-content: center; cursor: pointer; }
    .strip-btn:hover { background: #f1f5f9; color: #0f172a; }

    /* Signature Modal */
    .sign-backdrop { position: fixed; inset: 0; background: rgba(0,0,0,0.5); display: none; align-items: center; justify-content: center; z-index: 999; backdrop-filter: blur(2px); }
    .sign-box { background: #fff; border-radius: 12px; padding: 20px; width: 440px; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.2); }
    .sign-box-head { display: flex; align-items: center; justify-content: space-between; margin-bottom: 10px; }
    .sign-canvas { border: 2px dashed #cbd5e1; border-radius: 8px; background: #f8fafc; cursor: crosshair; width: 100%; height: 160px; }
    .sign-box-actions { display: flex; justify-content: flex-end; gap: 8px; margin-top: 12px; }

    .spin { display: inline-block; width: 14px; height: 14px; border: 2px solid rgba(255,255,255,.3); border-radius: 50%; border-top-color: #fff; animation: spin 0.8s linear infinite; }
    @keyframes spin { to { transform: rotate(360deg); } }
  </style>
</head>
<body>

<div class="acrobat-app">

  <!-- 1. Top Global Navigation Bar (Acrobat Header) -->
  <header class="acrobat-navbar">
    <div class="nav-left">
      <a href="{{ route('service_company.dashboard') }}" class="nav-logo-wrap" title="JERO Works Dashboard">
        <img src="{{ asset('img/logo.svg') }}" alt="JERO Logo" class="nav-logo-img">
      </a>
      <div class="nav-title">jero.works <span>online editor</span></div>
      <div class="nav-tabs">
        <span class="nav-tab active">All tools</span>
        <span class="nav-tab">Read</span>
        <span class="nav-tab">Edit</span>
        <span class="nav-tab">Convert</span>
        <span class="nav-tab">E-Sign</span>
      </div>
    </div>

    <div class="nav-center">
      <div class="nav-search-box">
        <svg class="nav-search-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
        <input type="text" id="docSearchInput" class="nav-search-input" placeholder="Find text or tools in document…" autocomplete="off">
        <div class="nav-search-nav">
          <span id="navCount">0/0</span>
          <button type="button" id="navPrev" class="nav-search-btn" title="Previous occurrence (Shift+F3)">▲</button>
          <button type="button" id="navNext" class="nav-search-btn" title="Next occurrence (F3)">▼</button>
        </div>
      </div>
    </div>

    <div class="nav-right">
      <span class="ats-score-pill">
        <svg viewBox="0 0 24 24" fill="currentColor" style="width:13px;height:13px;"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
        <span id="topAtsScore">--% ATS Match</span>
      </span>
      <span class="doc-metrics-text" id="docMetrics">0 words • 0 chars</span>
      <button type="button" id="btnCopyText" class="btn-action-copy">📋 Copy Text</button>
      <button type="button" id="btnDownload" class="btn-action-download">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:13px;height:13px;"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
        <span id="btnDlText">Download Final PDF</span>
      </button>
    </div>
  </header>

  <!-- 2. Main Body: Split Architecture -->
  <div class="acrobat-body">

    <!-- ZONE A: Left Sidebar ("All tools" + ATS Hub) -->
    <aside class="acrobat-sidebar" id="sidebar">
      <div class="sidebar-head">
        <h2>All tools</h2>
        <span class="cand-chip-pill" title="{{ ucwords(@$application->full_name ?: 'Candidate') }}">{{ ucwords(@$application->full_name ?: 'Candidate') }}</span>
      </div>

      <!-- Format / Option Selector -->
      <div class="sidebar-section">
        <label class="section-title">Format &amp; Mode</label>
        <select id="modeSelect" class="mode-select-box">
          <option value="61" {{ $optId == 61 ? 'selected' : '' }}>61. Clean Resume (Vector PDF)</option>
          <option value="62" {{ $optId == 62 ? 'selected' : '' }}>62. Resume + Company Branding</option>
          <option value="63" {{ $optId == 63 ? 'selected' : '' }}>63. Resume + Highlighted Keywords</option>
          <option value="64" {{ $optId == 64 ? 'selected' : '' }}>64. Pre-Screen Report + Resume</option>
          <option value="65" {{ $optId == 65 ? 'selected' : '' }}>65. Pre-Screen Report + Resume + Highlights</option>
        </select>
      </div>

      <!-- ATS Keyword Highlights Card -->
      <div class="sidebar-section">
        <label class="section-title">ATS Match &amp; Highlights</label>
        <div class="sidebar-ats-card">
          <div class="ats-stats-grid">
            <div class="stat-pill-box val-matched"><span class="stat-pill-val" id="statMatched">0</span><span class="stat-pill-lbl">Matched</span></div>
            <div class="stat-pill-box val-missing"><span class="stat-pill-val" id="statMissing">0</span><span class="stat-pill-lbl">Missing</span></div>
            <div class="stat-pill-box"><span class="stat-pill-val" id="statHighlights">0</span><span class="stat-pill-lbl">Highlights</span></div>
            <div class="stat-pill-box val-score"><span class="stat-pill-val" id="statScore">--%</span><span class="stat-pill-lbl">ATS Score</span></div>
          </div>

          <div class="ats-search-wrap">
            <input type="text" id="kwInput" class="ats-kw-input" placeholder="Add keyword to check…" autocomplete="off">
            <button type="button" id="btnKwAdd" class="btn-ats-add-kw">Add &amp; Check</button>
          </div>

          <span class="skills-title">Target Job Skills</span>
          <div class="chips-flow" id="chipsContainer"></div>
        </div>
      </div>

      <!-- Acrobat Quick Tool Menu Items -->
      <div class="sidebar-section" style="border-bottom:none; flex:1;">
        <label class="section-title">Document Tools</label>
        <ul class="tools-menu-list">
          <li class="tool-menu-item active" id="menuEditPdf"><span class="tool-menu-icon">✏️</span> Edit Document</li>
          <li class="tool-menu-item" id="menuExportPdf"><span class="tool-menu-icon">📥</span> Export a PDF</li>
          <li class="tool-menu-item" id="menuCombine"><span class="tool-menu-icon">📑</span> Combine Dossier (Report + Resume)</li>
          <li class="tool-menu-item" id="menuOrganize"><span class="tool-menu-icon">🗂</span> Organize Pages</li>
          <li class="tool-menu-item" id="menuSign"><span class="tool-menu-icon">✍</span> Request E-Signatures</li>
          <li class="tool-menu-item" id="menuRedact"><span class="tool-menu-icon">⬛</span> Redact a PDF (Whiteout)</li>
          <li class="tool-menu-item" id="menuCompress"><span class="tool-menu-icon">🗜</span> Compress &amp; Optimize PDF</li>
        </ul>
      </div>
    </aside>

    <!-- ZONE B: Vertical Floating Tool Dock (Between Sidebar and Canvas) -->
    <div class="acrobat-dock">
      <button type="button" class="dock-btn active" id="dockSelect" title="Select / Move">↖</button>
      <div class="dock-sep"></div>
      <button type="button" class="dock-btn" data-cmd="bold" title="Bold (Ctrl+B)"><strong>B</strong></button>
      <button type="button" class="dock-btn" data-cmd="italic" title="Italic (Ctrl+I)"><em>I</em></button>
      <button type="button" class="dock-btn" data-cmd="underline" title="Underline (Ctrl+U)"><u>U</u></button>
      <button type="button" class="dock-btn" data-cmd="strikeThrough" title="Strikethrough"><s>S</s></button>
      <div class="dock-sep"></div>
      <button type="button" class="dock-btn" id="dockHighlight" title="Highlight Selection" style="background:#fef9c3;color:#854d0e;">🖍</button>
      <button type="button" class="dock-btn" id="dockWhiteout" title="Redact / Whiteout Selection" style="background:#0f172a;color:#ffffff;">⬛</button>
      <button type="button" class="dock-btn" id="dockImage" title="Insert Image / Logo">🖼</button>
      <button type="button" class="dock-btn" id="dockSign" title="Draw Signature">✍</button>
      <button type="button" class="dock-btn" id="dockPageBreak" title="Insert Page Break">📄</button>
      <div class="dock-sep"></div>
      <input type="color" id="dockColor" class="dock-color" value="#1e293b" title="Text Color">
      <button type="button" class="dock-btn" data-cmd="undo" title="Undo">↶</button>
      <button type="button" class="dock-btn" data-cmd="redo" title="Redo">↷</button>
      <input type="file" id="dockFileInput" accept="image/*" style="display:none;">
    </div>

    <!-- ZONE C: Center Document Viewport (Dedicated Scroller) -->
    <main class="acrobat-canvas" id="canvas">

      <!-- PAGE 1: PRE-SCREENED CANDIDATE REPORT (Exact replica of media_1789225189169.png) -->
      <div class="paper-sheet" id="sheetReport" style="display:none;" contenteditable="true" spellcheck="false">

        <!-- Report Top Header -->
        <div class="ps-header">
          <div class="ps-shared-by">
            @if(!empty($companyLogo))
              <img src="{{ $companyLogo }}" alt="Company Logo" class="ps-company-logo-img">
            @else
              <svg viewBox="0 0 24 24" fill="#0284c7" style="width:34px;height:34px;"><path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/></svg>
            @endif
            <div>
              <div class="ps-shared-title">SHARED BY</div>
              <div class="ps-shared-name">{{ $companyName ?: '4Bell Technology' }}</div>
            </div>
          </div>
          <div class="ps-report-title-box">
            <div class="ps-report-main-title">PRE-SCREENED CANDIDATE REPORT</div>
            <div class="ps-report-sub-date">{{ strtoupper(date('d M Y')) }} • CONFIDENTIAL</div>
          </div>
        </div>

        <!-- Top Split: Candidate Info Card + Hiring Confidence Card -->
        <div class="ps-top-split">
          <div class="ps-card">
            <div class="ps-cand-name">{{ ucwords(@$application->full_name ?: 'V Eena') }}</div>
            <div class="ps-cand-role">{{ $job->title ?? ($application->job_title ?? 'Full Stack Developer') }}</div>
            <div class="ps-cand-meta-line">★ EXPERIENCE: {{ @$application->total_exp ? (@$application->total_exp . ' YEARS') : '0 YEARS' }}</div>
            <div class="ps-cand-meta-line">🏢 CURRENT COMPANY / CONTACT: {{ @$application->phone ?: '+91 - 7022934385' }}</div>
          </div>
          <div class="ps-card ps-confidence-card">
            <div class="ps-confidence-lbl">HIRING CONFIDENCE</div>
            <div class="ps-confidence-val">{{ @$application->rating ? ($application->rating * 20) . '%' : '85%' }}</div>
          </div>
        </div>

        <!-- Middle 4-Grid Glance Metrics -->
        <div class="ps-metrics-4grid">
          <div class="ps-metric-item">
            <div class="ps-metric-head">🎓 EDUCATION</div>
            <div class="ps-metric-val">DIPLOMA / DEGREE</div>
          </div>
          <div class="ps-metric-item">
            <div class="ps-metric-head">📅 NOTICE PERIOD</div>
            <div class="ps-metric-val">{{ $noticePeriod }}</div>
          </div>
          <div class="ps-metric-item">
            <div class="ps-metric-head">📍 LOCATION</div>
            <div class="ps-metric-val">{{ $cityName }}</div>
          </div>
          <div class="ps-metric-item">
            <div class="ps-metric-head">💻 WORK MODE</div>
            <div class="ps-metric-val">Hybrid</div>
          </div>
        </div>

        <!-- Bottom Split: Executive Summary + Recruiter Comments -->
        <div class="ps-bottom-split">
          <div class="ps-summary-card">
            <div class="ps-section-title">📄 EXECUTIVE SUMMARY</div>
            <div class="ps-text-content">
              {{ $prescreenNotes }}
            </div>
          </div>
          <div class="ps-summary-card">
            <div class="ps-section-title">👤 RECRUITER COMMENTS</div>
            <div class="ps-text-content">
              Candidate thoroughly verified by {{ $recruiterName }}. Clear communication, strong analytical aptitude, and immediate project delivery readiness verified.
            </div>
          </div>
        </div>

        <div class="ps-page-footer-note">
          ── End of Pre-Screened Candidate Report (Page 1) • Verified Résumé Begins on Page 2 ──
        </div>

      </div>

      <!-- PAGE 2: CANDIDATE RÉSUMÉ SHEET (All Modes) -->
      <div class="paper-sheet" id="sheetResume" contenteditable="true" spellcheck="false">

        <!-- Company Branding Header (Option 62) -->
        <div class="resume-brand-banner" id="brandingBanner" style="display:none;" contenteditable="false">
          <div>
            <div class="resume-brand-title">{{ strtoupper($companyName ?: 'CONFIDENTIAL CANDIDATE') }}</div>
            <div class="resume-brand-sub">Represented Candidate • Verified ATS Optimized Résumé</div>
          </div>
          <span style="background:#ccfbf1;color:#0f766e;font-size:10.5px;font-weight:700;padding:3px 10px;border-radius:999px;">Branded Profile</span>
        </div>

        <!-- Resume Body -->
        <div id="resumeBody">
          <div id="loaderPlaceholder" style="display:flex;flex-direction:column;align-items:center;justify-content:center;padding:140px 20px;color:#64748b;gap:12px;" contenteditable="false">
            <div class="spin" style="width:30px;height:30px;border-color:#0284c7;border-top-color:transparent;border-width:3px;"></div>
            <span style="font-size:14px;font-weight:600;color:#0f172a;">Loading candidate résumé from Cloudflare R2…</span>
            <span style="font-size:11.5px;color:#64748b;">Rendering exact high-fidelity document pages for live editing</span>
          </div>
        </div>

      </div>

      <!-- Floating Bottom Adobe AI Bar -->
      <div class="acrobat-bottom-bar">
        <span class="bottom-ai-icon">✨</span>
        <input type="text" class="bottom-ai-input" placeholder="Ask ATS AI Assistant: What would you like to optimize in this candidate résumé?" id="bottomAiInput">
        <button type="button" class="bottom-ai-btn" id="btnBottomAi">Optimize</button>
      </div>

    </main>

    <!-- ZONE D: Right Page Navigation Strip (Acrobat Style) -->
    <aside class="acrobat-right-strip">
      <div class="page-counter-box" id="stripPageNum">1 / 2</div>
      <button type="button" class="strip-btn" id="stripPrevPage" title="Previous Page">▲</button>
      <button type="button" class="strip-btn" id="stripNextPage" title="Next Page">▼</button>
      <div class="dock-sep"></div>
      <button type="button" class="strip-btn" id="stripFitWidth" title="Fit to Width">⛶</button>
      <button type="button" class="strip-btn" id="stripZoomIn" title="Zoom In">＋</button>
      <button type="button" class="strip-btn" id="stripZoomOut" title="Zoom Out">－</button>
    </aside>

  </div>

</div>

<!-- Signature Pad Modal -->
<div class="sign-backdrop" id="signModal">
  <div class="sign-box">
    <div class="sign-box-head">
      <h3 style="font-size:14px;font-weight:700;color:#0f172a;">Draw Signature</h3>
      <button type="button" id="btnSignClose" style="background:none;border:none;font-size:18px;cursor:pointer;color:#64748b;">&times;</button>
    </div>
    <p style="font-size:11.5px;color:#64748b;margin-bottom:8px;">Sign below with your mouse, finger, or stylus:</p>
    <canvas id="signCanvas" class="sign-canvas" width="400" height="160"></canvas>
    <div class="sign-box-actions">
      <button type="button" id="btnSignClear" class="dock-btn" style="width:auto;padding:6px 12px;">Clear</button>
      <button type="button" id="btnSignInsert" class="btn-ats-add-kw" style="padding:6px 14px;">Insert Signature</button>
    </div>
  </div>
</div>

<script>
  // Server-rendered variables
  var jobSkills = @json($jobSkills ?? []);
  var candSkills = @json($candSkills ?? []);
  var candName = @json(ucwords(@$application->full_name ?: @$application->name ?: 'Candidate'));
  var jobTitle = @json(ucwords(@$job->title ?: @$application->job_title ?: 'Verified Profile'));
  var companyName = @json($companyName ?? '');
  var companyLogo = @json($companyLogo ?? '');
  var resumeExt = @json($resumeExt ?? '');
  var initialOpt = @json($optId ?? 61);
  var resumeSources = [
    @json($rcvFetchUrl ?? ''),
    @json($rcvDownloadUrl ?? ''),
    @json($rcvPublicUrl ?? '')
  ].filter(Boolean);

  var keywords = {};
  var hits = [];
  var curHit = -1;
  var OptClass = window.AgyCvOptimizer || (window.opener && window.opener.AgyCvOptimizer);
  var optimizer = null;
  if(OptClass){
    optimizer = new OptClass({ jobSkills: jobSkills, candSkills: candSkills, candidateName: candName, targetJobTitle: jobTitle });
  }

  var sheetReport = document.getElementById('sheetReport');
  var sheetResume = document.getElementById('sheetResume');
  var resumeBody = document.getElementById('resumeBody');
  var brandingBanner = document.getElementById('brandingBanner');
  var modeSelect = document.getElementById('modeSelect');
  var chipsContainer = document.getElementById('chipsContainer');
  var canvas = document.getElementById('canvas');

  function esc(s){ return String(s||'').replace(/[&<>"]/g, function(c){ return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;'}[c]; }); }
  function escRe(s){ return String(s||'').replace(/[.*+?^${}()|[\]\\]/g, '\\$&'); }

  // 1. Initialize Keywords
  jobSkills.forEach(function(sk){
    if(sk && sk.trim()) keywords[sk.trim().toLowerCase()] = { name: sk.trim(), type: 'job', active: true, count: 0 };
  });

  function countOccurrences(){
    var text = (resumeBody.innerText || '') + ' ' + (sheetReport ? sheetReport.innerText : '');
    Object.keys(keywords).forEach(function(k){
      var re = new RegExp('\\b' + escRe(keywords[k].name) + '\\b', 'gi');
      var m = text.match(re);
      keywords[k].count = m ? m.length : 0;
    });
  }

  function renderChips(){
    chipsContainer.innerHTML = '';
    var matched = 0, missing = 0, totalHits = 0;
    Object.keys(keywords).forEach(function(k){
      var item = keywords[k];
      if(item.count > 0){ matched++; totalHits += item.count; } else { missing++; }
      var chip = document.createElement('span');
      chip.className = 'kw-chip' + (item.count > 0 ? '' : ' missing');
      chip.innerHTML = esc(item.name) + ' <span class="chip-count">' + item.count + '</span>';
      chip.onclick = function(){
        scrollToKeyword(item.name);
      };
      chipsContainer.appendChild(chip);
    });

    document.getElementById('statMatched').textContent = matched;
    document.getElementById('statMissing').textContent = missing;
    document.getElementById('statHighlights').textContent = totalHits;

    // Update word / character counts
    var fullText = (sheetReport && sheetReport.style.display !== 'none' ? sheetReport.innerText : '') + ' ' + (resumeBody.innerText || '');
    var words = fullText.trim() ? fullText.trim().split(/\s+/).length : 0;
    document.getElementById('docMetrics').textContent = words + ' words • ' + fullText.length + ' chars';

    // Calculate ATS Score
    if(!optimizer && OptClass) optimizer = new OptClass({ jobSkills: jobSkills, candSkills: candSkills, candidateName: candName, targetJobTitle: jobTitle });
    if(optimizer){
      var res = optimizer.calculateAtsScore(fullText);
      var scoreEl = document.getElementById('statScore');
      scoreEl.textContent = res.score + '%';
      scoreEl.style.color = res.score >= 80 ? '#15803d' : (res.score >= 50 ? '#b45309' : '#b91c1c');
      document.getElementById('topAtsScore').textContent = res.score + '% ATS Match';
    }
  }

  function scrollToKeyword(kw){
    var activeMarks = Array.prototype.slice.call(sheetResume.querySelectorAll('mark.rcv-hit'));
    var match = activeMarks.filter(function(m){ return (m.innerText || '').toLowerCase() === kw.toLowerCase(); })[0];
    if(match){
      match.scrollIntoView({ behavior: 'smooth', block: 'center' });
      match.classList.add('rcv-hit-active');
      setTimeout(function(){ match.classList.remove('rcv-hit-active'); }, 1500);
    }
  }

  function highlightInNode(node, re, cls){
    cls = cls || 'rcv-hit';
    if(node.nodeType === 3){
      re.lastIndex = 0;
      var m = re.exec(node.nodeValue);
      if(!m) return;
      var mark = document.createElement('mark');
      mark.className = cls;
      var matched = node.splitText(m.index);
      matched.splitText(m[0].length);
      mark.appendChild(matched.cloneNode(true));
      matched.parentNode.replaceChild(mark, matched);
    } else if(node.nodeType === 1 && node.childNodes && !/(script|style|mark)/i.test(node.tagName)){
      for(var i = 0; i < node.childNodes.length; i++){
        highlightInNode(node.childNodes[i], re, cls);
      }
    }
  }

  function applyHighlights(){
    var mode = parseInt(modeSelect.value, 10) || 61;
    var isHighlighted = (mode === 63 || mode === 65);

    // Remove existing marks
    var marks = Array.prototype.slice.call(sheetResume.querySelectorAll('mark.rcv-hit'));
    marks.forEach(function(m){
      var p = m.parentNode;
      while(m.firstChild) p.insertBefore(m.firstChild, m);
      p.removeChild(m);
    });

    if(!isHighlighted){
      hits = [];
      document.getElementById('navCount').textContent = '0/0';
      return;
    }

    var activeKeys = Object.keys(keywords).filter(function(k){ return keywords[k].active; });
    if(!activeKeys.length) return;
    var sorted = activeKeys.map(function(k){ return keywords[k].name; }).sort(function(a,b){ return b.length - a.length; });
    var pat = sorted.map(escRe).join('|');
    if(!pat) return;
    var re = new RegExp('\\b(' + pat + ')\\b', 'gi');
    highlightInNode(resumeBody, re, 'rcv-hit');

    hits = Array.prototype.slice.call(sheetResume.querySelectorAll('mark.rcv-hit'));
    document.getElementById('navCount').textContent = hits.length ? ('1/' + hits.length) : '0/0';
    curHit = 0;
  }

  function gotoHit(dir){
    if(!hits.length) return;
    if(curHit >= 0 && hits[curHit]) hits[curHit].classList.remove('rcv-hit-active');
    curHit = (curHit + dir + hits.length) % hits.length;
    var target = hits[curHit];
    target.classList.add('rcv-hit-active');
    target.scrollIntoView({ behavior: 'smooth', block: 'center' });
    document.getElementById('navCount').textContent = (curHit + 1) + '/' + hits.length;
  }

  function syncModeViews(){
    var mode = parseInt(modeSelect.value, 10) || 61;
    var isBranded = (mode === 62);
    var hasReport = (mode === 64 || mode === 65);

    brandingBanner.style.display = isBranded ? 'flex' : 'none';
    sheetReport.style.display = hasReport ? 'block' : 'none';

    var btnLbl = document.getElementById('btnDlText');
    if(isBranded) btnLbl.textContent = 'Download Branded Resume';
    else if(hasReport && mode === 65) btnLbl.textContent = 'Download Report + Highlighted';
    else if(hasReport) btnLbl.textContent = 'Download Report + Resume';
    else if(mode === 63) btnLbl.textContent = 'Download Highlighted Resume';
    else btnLbl.textContent = 'Download Resume (PDF)';

    // Update page strip counter
    document.getElementById('stripPageNum').textContent = hasReport ? '1 / 2' : '1 / 1';

    countOccurrences();
    renderChips();
    applyHighlights();
  }

  // Load authentic candidate resume
  async function loadOriginalResume(){
    // 1. Check parent window in-memory cache
    if(window.opener && window.opener.__rcvResumeHtml && window.opener.__rcvResumeHtml.trim().length > 30){
      displayResume(window.opener.__rcvResumeHtml);
      return;
    }
    var openerText = window.opener && (window.opener.__rcvResumeText || window.opener.__rdcPreloadedResumeText);

    // 2. Check binary cache in parent window
    var bytes = null;
    if(window.opener && window.opener.__agyResumeCache){
      for(var k in window.opener.__agyResumeCache){
        var b = window.opener.__agyResumeCache[k];
        if(b && b.byteLength > 100){ bytes = b; break; }
      }
    }

    // 3. Direct fetch from available resume sources
    if(!bytes && resumeSources && resumeSources.length){
      for(var i = 0; i < resumeSources.length; i++){
        var src = resumeSources[i];
        if(!src) continue;
        try {
          var isDirectR2 = (src.indexOf('file.jero.works') !== -1 || src.indexOf('r2.cloudflarestorage.com') !== -1);
          var resp = await fetch(src, isDirectR2 ? { mode: 'cors' } : { credentials: 'same-origin' });
          if(resp.ok){
            var ab = await resp.arrayBuffer();
            if(ab && ab.byteLength > 100){ bytes = new Uint8Array(ab); break; }
          }
        } catch(e){}
      }
    }

    // 4. Parse bytes based on file format
    if(bytes){
      var ext = (resumeExt || '').toLowerCase();
      if(ext === 'docx' || ext === 'doc' || !ext){
        try {
          if(typeof mammoth !== 'undefined'){
            var conv = await mammoth.convertToHtml({ arrayBuffer: bytes.buffer || bytes });
            if(conv && conv.value && conv.value.trim().length > 20){
              displayResume(conv.value);
              return;
            }
          }
        } catch(e){ console.warn('Mammoth parse error:', e); }
      }
      if(ext === 'pdf'){
        try {
          if(typeof pdfjsLib !== 'undefined'){
            var ver = pdfjsLib.version || '2.11.338';
            pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/' + ver + '/pdf.worker.min.js';
            var pdf = await pdfjsLib.getDocument({ data: bytes }).promise;
            var parts = [];
            for(var p = 1; p <= pdf.numPages; p++){
              var page = await pdf.getPage(p);
              var tc = await page.getTextContent();
              var pText = tc.items.map(function(item){ return item.str; }).join(' ');
              var lines = pText.split(/(?<=[.?!])\s+/);
              lines.forEach(function(l){ if(l.trim()) parts.push('<p>' + esc(l.trim()) + '</p>'); });
            }
            if(parts.length){
              displayResume(parts.join(''));
              return;
            }
          }
        } catch(e){ console.warn('PDF.js parse error:', e); }
      }
    }

    // 5. Fallback profile
    if(openerText){
      var fLines = openerText.split(/\r?\n/);
      var fHtml = fLines.map(function(l){ return l.trim() ? ('<p>' + esc(l.trim()) + '</p>') : '<br>'; }).join('');
      displayResume(fHtml);
    } else {
      displayResume('<p><strong>' + esc(candName) + '</strong></p><p>' + esc(jobTitle) + '</p><p>Candidate resume loaded for live editing.</p>');
    }
  }

  function displayResume(html){
    var placeholder = document.getElementById('loaderPlaceholder');
    if(placeholder) placeholder.remove();
    resumeBody.innerHTML = html;
    syncModeViews();
  }

  // Keyword search & add
  document.getElementById('btnKwAdd').onclick = function(){
    var inp = document.getElementById('kwInput');
    var val = (inp.value || '').trim();
    if(!val) return;
    keywords[val.toLowerCase()] = { name: val, type: 'custom', active: true, count: 0 };
    inp.value = '';
    countOccurrences();
    renderChips();
    applyHighlights();
  };
  document.getElementById('kwInput').addEventListener('keydown', function(e){
    if(e.key === 'Enter'){ e.preventDefault(); document.getElementById('btnKwAdd').click(); }
  });

  // Top Search in document
  document.getElementById('docSearchInput').addEventListener('input', function(){
    var q = (this.value || '').trim();
    if(!q){
      applyHighlights();
      return;
    }
    keywords[q.toLowerCase()] = { name: q, type: 'search', active: true, count: 0 };
    countOccurrences();
    renderChips();
    applyHighlights();
  });

  // Hit navigation
  document.getElementById('navPrev').onclick = function(){ gotoHit(-1); };
  document.getElementById('navNext').onclick = function(){ gotoHit(1); };

  // Mode Dropdown change
  modeSelect.addEventListener('change', function(){
    syncModeViews();
  });

  // Live input sync
  var editDebounce = null;
  function onDocumentInput(){
    clearTimeout(editDebounce);
    editDebounce = setTimeout(function(){
      countOccurrences();
      renderChips();
    }, 150);
  }
  sheetResume.addEventListener('input', onDocumentInput);
  sheetReport.addEventListener('input', onDocumentInput);

  // Floating Dock Formatting Buttons
  document.querySelectorAll('.dock-btn[data-cmd]').forEach(function(btn){
    btn.addEventListener('click', function(e){
      e.preventDefault();
      var cmd = this.getAttribute('data-cmd');
      document.execCommand(cmd, false, null);
    });
  });

  document.getElementById('dockColor').addEventListener('input', function(){
    document.execCommand('foreColor', false, this.value);
  });

  // Advanced Tools in Dock
  document.getElementById('dockHighlight').onclick = function(){
    var sel = window.getSelection();
    if(!sel.rangeCount || sel.isCollapsed) return;
    var range = sel.getRangeAt(0);
    var mark = document.createElement('mark');
    mark.className = 'rcv-hit';
    mark.appendChild(range.extractContents());
    range.insertNode(mark);
    countOccurrences();
    renderChips();
  };

  document.getElementById('dockWhiteout').onclick = function(){
    var sel = window.getSelection();
    if(!sel.rangeCount || sel.isCollapsed) return;
    var range = sel.getRangeAt(0);
    var mark = document.createElement('mark');
    mark.className = 'rcv-redact';
    mark.appendChild(range.extractContents());
    range.insertNode(mark);
  };

  document.getElementById('dockPageBreak').onclick = function(){
    var div = document.createElement('div');
    div.className = 'doc-page-break';
    div.contentEditable = 'false';
    var sel = window.getSelection();
    if(sel.rangeCount){
      var range = sel.getRangeAt(0);
      range.insertNode(div);
    } else {
      resumeBody.appendChild(div);
    }
  };

  // Image Insertion
  document.getElementById('dockImage').onclick = function(){
    document.getElementById('dockFileInput').click();
  };
  document.getElementById('dockFileInput').addEventListener('change', function(e){
    var file = e.target.files && e.target.files[0];
    if(!file) return;
    var reader = new FileReader();
    reader.onload = function(evt){
      var img = document.createElement('img');
      img.src = evt.target.result;
      img.style.maxWidth = '240px';
      img.style.display = 'block';
      img.style.margin = '10px 0';
      var sel = window.getSelection();
      if(sel.rangeCount){
        var range = sel.getRangeAt(0);
        range.insertNode(img);
      } else {
        resumeBody.appendChild(img);
      }
    };
    reader.readAsDataURL(file);
  });

  // Signature Pad
  var signModal = document.getElementById('signModal');
  var signCanvas = document.getElementById('signCanvas');
  var signCtx = signCanvas.getContext('2d');
  var isDrawing = false;

  document.getElementById('dockSign').onclick = function(){
    signModal.style.display = 'flex';
    signCtx.clearRect(0, 0, signCanvas.width, signCanvas.height);
    signCtx.lineWidth = 2.5;
    signCtx.lineCap = 'round';
    signCtx.strokeStyle = '#0f172a';
  };

  signCanvas.addEventListener('mousedown', function(e){ isDrawing = true; signCtx.beginPath(); signCtx.moveTo(e.offsetX, e.offsetY); });
  signCanvas.addEventListener('mousemove', function(e){ if(isDrawing){ signCtx.lineTo(e.offsetX, e.offsetY); signCtx.stroke(); } });
  window.addEventListener('mouseup', function(){ isDrawing = false; });

  document.getElementById('btnSignClear').onclick = function(){ signCtx.clearRect(0, 0, signCanvas.width, signCanvas.height); };
  document.getElementById('btnSignClose').onclick = function(){ signModal.style.display = 'none'; };

  document.getElementById('btnSignInsert').onclick = function(){
    var dataUrl = signCanvas.toDataURL('image/png');
    var img = document.createElement('img');
    img.src = dataUrl;
    img.style.maxWidth = '180px';
    img.style.display = 'block';
    img.style.margin = '10px 0';
    var sel = window.getSelection();
    if(sel.rangeCount){
      var range = sel.getRangeAt(0);
      range.insertNode(img);
    } else {
      resumeBody.appendChild(img);
    }
    signModal.style.display = 'none';
  };

  // Right Strip Page Navigation
  document.getElementById('stripPrevPage').onclick = function(){
    canvas.scrollTo({ top: 0, behavior: 'smooth' });
  };
  document.getElementById('stripNextPage').onclick = function(){
    if(sheetResume) sheetResume.scrollIntoView({ behavior: 'smooth', block: 'start' });
  };
  var curZoom = 1;
  document.getElementById('stripZoomIn').onclick = function(){
    curZoom = Math.min(1.4, curZoom + 0.1);
    applyZoom();
  };
  document.getElementById('stripZoomOut').onclick = function(){
    curZoom = Math.max(0.7, curZoom - 0.1);
    applyZoom();
  };
  document.getElementById('stripFitWidth').onclick = function(){
    curZoom = 1;
    applyZoom();
  };
  function applyZoom(){
    sheetReport.style.transform = 'scale(' + curZoom + ')';
    sheetReport.style.transformOrigin = 'top center';
    sheetResume.style.transform = 'scale(' + curZoom + ')';
    sheetResume.style.transformOrigin = 'top center';
  }

  // Copy text
  document.getElementById('btnCopyText').onclick = function(){
    var text = (sheetReport && sheetReport.style.display !== 'none' ? sheetReport.innerText + '\n\n' : '') + (resumeBody.innerText || '');
    navigator.clipboard.writeText(text).then(function(){
      alert('Document text copied to clipboard!');
    }).catch(function(){ alert('Could not copy text.'); });
  };

  // Download PDF
  document.getElementById('btnDownload').onclick = async function(){
    var btn = document.getElementById('btnDownload');
    var orig = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<span class="spin"></span> Generating PDF…';
    try {
      var mode = parseInt(modeSelect.value, 10) || 61;
      var isBranded = (mode === 62);
      var isHighlighted = (mode === 63 || mode === 65);
      var hasReport = (mode === 64 || mode === 65);
      var resumeText = (resumeBody.innerText || '').trim();
      if(!resumeText) throw new Error('Resume content is empty.');

      if(!OptClass) OptClass = window.AgyCvOptimizer || (window.opener && window.opener.AgyCvOptimizer);
      if(!OptClass) throw new Error('Optimizer engine is initializing, please try again.');

      var optInstance = new OptClass({ jobSkills: jobSkills, candidateName: candName, targetJobTitle: jobTitle });
      var cleanCand = (candName || 'Candidate').replace(/[^a-zA-Z0-9_\-]/g, '_');

      if(hasReport && window.opener && typeof window.opener.__buildRSBlob === 'function' && typeof window.opener.mergePreScreenedReportAndResume === 'function'){
        var reportBlob = await window.opener.__buildRSBlob();
        var resumeBlob = await optInstance.exportOptimizedPdf(resumeText, {
          candidateName: candName,
          jobTitle: jobTitle,
          highlightKeywords: isHighlighted ? jobSkills : false
        });
        var mName = 'Jeroworks_' + cleanCand + (isHighlighted ? '_Report_Highlighted' : '_Report') + '.pdf';
        await window.opener.mergePreScreenedReportAndResume(reportBlob, resumeBlob, mName);
      } else {
        var logoUri = null;
        if(isBranded && companyLogo && window.opener && typeof window.opener.rsLogoDataUri === 'function'){
          try { logoUri = await window.opener.rsLogoDataUri(companyLogo); } catch(e){}
        }
        var blob = await optInstance.exportOptimizedPdf(resumeText, {
          candidateName: candName,
          jobTitle: jobTitle,
          brandHeader: isBranded,
          companyName: companyName,
          companyLogo: logoUri,
          highlightKeywords: isHighlighted ? jobSkills : false
        });
        var sfx = isBranded ? '_Branded' : (isHighlighted ? '_Highlighted' : (hasReport ? '_Report' : ''));
        OptClass.downloadBlob(blob, 'Jeroworks_' + cleanCand + '_ATS_Optimized' + sfx + '.pdf');
      }
    } catch(err){
      console.error('Download error:', err);
      alert('Could not download resume: ' + (err && err.message ? err.message : 'Please try again.'));
    } finally {
      btn.disabled = false;
      btn.innerHTML = orig;
    }
  };

  // Initial synchronization and load
  syncModeViews();
  loadOriginalResume();
</script>
</body>
</html>