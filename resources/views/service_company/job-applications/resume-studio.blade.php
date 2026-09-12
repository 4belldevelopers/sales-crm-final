<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>{{ ucwords(@$application->full_name ?: @$application->name ?: 'Candidate') }} — jero.works</title>
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

    .acrobat-app { display: flex; flex-direction: column; height: 100vh; overflow: hidden; }

    /* 1. Global Clean Top Navigation Bar */
    .acrobat-navbar { height: 54px; background: #ffffff; border-bottom: 1px solid #e5e7eb; display: flex; align-items: center; justify-content: space-between; padding: 0 18px; flex-shrink: 0; z-index: 110; }
    .nav-left { display: flex; align-items: center; gap: 14px; }
    .nav-logo-wrap { display: flex; align-items: center; gap: 8px; text-decoration: none; }
    .nav-logo-img { height: 26px; width: auto; }
    .nav-title { font-size: 15px; font-weight: 700; color: #0f172a; }
    .cand-badge-pill { display: flex; align-items: center; gap: 6px; background: #f8fafc; border: 1px solid #e2e8f0; padding: 4px 12px; border-radius: 999px; margin-left: 8px; }
    .cand-badge-name { font-size: 11.5px; font-weight: 600; color: #0f172a; max-width: 170px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .cand-badge-role { font-size: 11px; font-weight: 500; color: #64748b; max-width: 220px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }

    .nav-center { display: flex; align-items: center; gap: 8px; flex: 1; max-width: 440px; margin: 0 24px; }
    .nav-search-box { position: relative; width: 100%; display: flex; align-items: center; }
    .nav-search-input { width: 100%; height: 34px; padding: 0 16px 0 34px; font-size: 12px; font-family: 'Poppins', sans-serif; border: 1px solid #cbd5e1; border-radius: 999px; background: #f8fafc; outline: none; transition: all .15s; }
    .nav-search-input:focus { background: #ffffff; border-color: #0284c7; box-shadow: 0 0 0 3px rgba(2,132,199,.12); }
    .nav-search-icon { position: absolute; left: 11px; color: #94a3b8; width: 14px; height: 14px; }

    .nav-right { display: flex; align-items: center; gap: 12px; }
    .btn-action-download { background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%); color: #ffffff; border: none; padding: 8px 18px; font-size: 12px; font-weight: 600; border-radius: 7px; cursor: pointer; display: inline-flex; align-items: center; gap: 7px; font-family: 'Poppins', sans-serif; transition: all .15s; box-shadow: 0 2px 6px rgba(2,132,199,.3); }
    .btn-action-download:hover { background: linear-gradient(135deg, #0369a1 0%, #075985 100%); box-shadow: 0 4px 10px rgba(2,132,199,.4); }
    .btn-action-download:disabled { opacity: 0.65; cursor: not-allowed; }

    /* 2. Main Body Split Workspace */
    .acrobat-body { display: flex; flex: 1; height: calc(100vh - 54px); overflow: hidden; position: relative; }

    /* ZONE A: Left Sidebar (All Editing & ATS Features) */
    .acrobat-sidebar { width: 330px; background: #ffffff; border-right: 1px solid #e5e7eb; display: flex; flex-direction: column; overflow-y: auto; flex-shrink: 0; z-index: 90; }
    .acrobat-sidebar::-webkit-scrollbar { width: 6px; }
    .acrobat-sidebar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 3px; }

    .sidebar-block { padding: 14px 16px; border-bottom: 1px solid #f1f5f9; }
    .sidebar-block-title { font-size: 11px; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 8px; display: flex; align-items: center; gap: 6px; }

    /* ATS Keyword Highlights Card */
    .sidebar-ats-card { background: #f8fafc; border: 1.5px solid #e2e8f0; border-radius: 8px; padding: 12px; }
    .ats-stats-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 6px; margin-bottom: 10px; }
    .stat-pill-box { background: #ffffff; border: 1px solid #e2e8f0; border-radius: 6px; padding: 6px 8px; text-align: center; }
    .stat-pill-val { display: block; font-size: 14px; font-weight: 700; color: #0f172a; line-height: 1.2; }
    .stat-pill-lbl { font-size: 9px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.3px; color: #64748b; }
    .stat-pill-box.val-matched .stat-pill-val { color: #0d9488; }
    .stat-pill-box.val-missing .stat-pill-val { color: #dc2626; }
    .stat-pill-box.val-score .stat-pill-val { color: #16a34a; }

    .ats-search-wrap { display: flex; gap: 6px; margin-bottom: 10px; }
    .ats-kw-input { flex: 1; padding: 6px 10px; font-size: 11.5px; font-family: 'Poppins', sans-serif; border: 1px solid #cbd5e1; border-radius: 6px; outline: none; background: #fff; }
    .ats-kw-input:focus { border-color: #6366f1; }
    .btn-ats-add-kw { padding: 6px 12px; background: #6366f1; color: #fff; border: none; border-radius: 6px; font-size: 11px; font-weight: 600; cursor: pointer; font-family: 'Poppins', sans-serif; white-space: nowrap; }
    .btn-ats-add-kw:hover { background: #4f46e5; }

    .chips-flow { display: flex; flex-wrap: wrap; gap: 5px; }
    .kw-chip { display: inline-flex; align-items: center; gap: 4px; background: #fef08a; border: 1px solid #facc15; color: #854d0e; padding: 2px 8px; border-radius: 999px; font-size: 10.5px; font-weight: 600; cursor: pointer; transition: all .15s; }
    .kw-chip:hover { transform: translateY(-1px); box-shadow: 0 2px 4px rgba(250,204,21,.3); }
    .kw-chip.missing { background: #f1f5f9; border-color: #cbd5e1; color: #64748b; }
    .kw-chip .chip-count { font-size: 9px; font-weight: 700; background: rgba(0,0,0,0.08); padding: 1px 4px; border-radius: 999px; }

    /* Editing Tools Section */
    .edit-tools-grid { display: flex; flex-direction: column; gap: 10px; }
    .tool-row { display: flex; align-items: center; gap: 6px; flex-wrap: wrap; }
    .tool-select { flex: 1; min-width: 90px; border: 1px solid #cbd5e1; border-radius: 6px; padding: 5px 8px; font-size: 11.5px; font-family: 'Poppins', sans-serif; color: #1e293b; background: #fff; outline: none; }
    .tool-color-input { width: 30px; height: 30px; border: 1px solid #cbd5e1; border-radius: 6px; padding: 0; cursor: pointer; background: none; }

    .tool-btn-group { display: flex; align-items: center; gap: 4px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; padding: 2px 4px; width: 100%; justify-content: space-between; }
    .btn-format { background: transparent; border: none; padding: 5px 8px; border-radius: 4px; font-size: 11.5px; font-weight: 600; color: #334155; cursor: pointer; transition: all .15s; font-family: 'Poppins', sans-serif; }
    .btn-format:hover { background: #e2e8f0; color: #0f172a; }

    .action-tools-list { display: flex; flex-direction: column; gap: 6px; margin-top: 4px; }
    .btn-action-tool { display: flex; align-items: center; gap: 8px; padding: 7px 10px; border-radius: 6px; font-size: 11.5px; font-weight: 600; border: 1px solid #e2e8f0; background: #ffffff; color: #334155; cursor: pointer; transition: all .15s; font-family: 'Poppins', sans-serif; }
    .btn-action-tool:hover { background: #f8fafc; border-color: #cbd5e1; color: #0f172a; }
    .btn-tool-highlight { background: #fef9c3; border-color: #fde047; color: #854d0e; }
    .btn-tool-highlight:hover { background: #fef08a; }
    .btn-tool-whiteout { background: #0f172a; border-color: #0f172a; color: #ffffff; }
    .btn-tool-whiteout:hover { background: #1e293b; color: #ffffff; }

    /* ZONE B: Center Document Viewport (Dedicated Scroller) */
    .acrobat-canvas { flex: 1; background: #f3f4f6; overflow-y: auto; overflow-x: hidden; padding: 28px 24px 80px 24px; position: relative; scroll-behavior: smooth; }
    .acrobat-canvas::-webkit-scrollbar { width: 9px; }
    .acrobat-canvas::-webkit-scrollbar-track { background: #e5e7eb; }
    .acrobat-canvas::-webkit-scrollbar-thumb { background: #9ca3af; border-radius: 4px; }

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

    .ps-top-split { display: grid; grid-template-columns: 1.6fr 1fr; gap: 14px; margin-bottom: 14px; }
    .ps-card { background: #ffffff; border: 1.5px solid #e2e8f0; border-radius: 8px; padding: 16px 18px; }
    .ps-cand-name { font-size: 20px; font-weight: 800; color: #0f172a; margin-bottom: 2px; }
    .ps-cand-role { font-size: 13.5px; font-weight: 600; color: #0284c7; margin-bottom: 8px; }
    .ps-cand-meta-line { font-size: 11.5px; color: #475569; font-weight: 500; margin-bottom: 3px; display: flex; align-items: center; gap: 6px; }

    .ps-confidence-card { text-align: center; display: flex; flex-direction: column; align-items: center; justify-content: center; background: #f8fafc; }
    .ps-confidence-lbl { font-size: 11.5px; font-weight: 700; color: #475569; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px; }
    .ps-confidence-val { font-size: 38px; font-weight: 800; color: #0284c7; line-height: 1; }

    .ps-metrics-4grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 10px; margin-bottom: 14px; }
    .ps-metric-item { background: #ffffff; border: 1.5px solid #e2e8f0; border-radius: 8px; padding: 12px 10px; text-align: center; }
    .ps-metric-head { font-size: 10px; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.4px; margin-bottom: 4px; display: flex; align-items: center; justify-content: center; gap: 5px; }
    .ps-metric-val { font-size: 12.5px; font-weight: 700; color: #0f172a; }

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

    /* ZONE C: Right Page Navigation Strip */
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

  <!-- 1. Top Global Clean Navigation Bar -->
  <header class="acrobat-navbar">
    <div class="nav-left">
      <a href="{{ route('service_company.dashboard') }}" class="nav-logo-wrap" title="JERO Works Dashboard">
        <img src="{{ asset('img/logo.svg') }}" alt="JERO Logo" class="nav-logo-img">
      </a>
      <div class="nav-title">jero.works</div>
      <div class="cand-badge-pill">
        <span class="cand-badge-name" title="{{ ucwords(@$application->full_name ?: 'Candidate') }}">{{ ucwords(@$application->full_name ?: 'Candidate') }}</span>
        <span class="cand-badge-role" title="{{ $job->title ?? ($application->job_title ?? 'Verified Profile') }}">• {{ $job->title ?? ($application->job_title ?? 'Verified Profile') }}</span>
      </div>
    </div>

    <div class="nav-center">
      <div class="nav-search-box">
        <svg class="nav-search-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
        <input type="text" id="docSearchInput" class="nav-search-input" placeholder="Find text in document…" autocomplete="off">
      </div>
    </div>

    <div class="nav-right">
      <button type="button" id="btnDownload" class="btn-action-download">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:14px;height:14px;"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
        <span id="btnDlText">Download Final PDF</span>
      </button>
    </div>
  </header>

  <!-- 2. Main Body: Split Architecture -->
  <div class="acrobat-body">

    <!-- ZONE A: Left Sidebar (All Editing & ATS Features) -->
    <aside class="acrobat-sidebar" id="sidebar">

      <!-- Hidden modeSelect input to honor URL opt parameter -->
      <input type="hidden" id="modeSelect" value="{{ ($optId ?? (int)request('opt', 61)) }}">

      <!-- Section 1: ATS Keyword Highlights & Match Score -->
      <div class="sidebar-block">
        <label class="sidebar-block-title">ATS Match &amp; Highlights</label>
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

          <span style="font-size:10.5px;font-weight:700;color:#475569;text-transform:uppercase;letter-spacing:0.4px;margin-bottom:6px;display:block;">Target Job Skills</span>
          <div class="chips-flow" id="chipsContainer"></div>
        </div>
      </div>

      <!-- Section 2: All Document Editing Features -->
      <div class="sidebar-block">
        <label class="sidebar-block-title">Document Editing Tools</label>
        <div class="edit-tools-grid">

          <!-- Style & Font Selectors -->
          <div class="tool-row">
            <select id="selFormat" class="tool-select" title="Text Style">
              <option value="p">Paragraph</option>
              <option value="h1">Heading 1</option>
              <option value="h2">Heading 2</option>
              <option value="h3">Heading 3</option>
            </select>
            <select id="selSize" class="tool-select" title="Font Size" style="max-width:80px;">
              <option value="2">11px</option>
              <option value="3" selected>13px</option>
              <option value="4">15px</option>
              <option value="5">18px</option>
            </select>
            <input type="color" id="pickerColor" class="tool-color-input" value="#1e293b" title="Text Color">
          </div>

          <div class="tool-row">
            <select id="selFont" class="tool-select" title="Font Family">
              <option value="Poppins, sans-serif">Poppins</option>
              <option value="Inter, sans-serif">Inter</option>
              <option value="Calibri, sans-serif">Calibri</option>
              <option value="Arial, sans-serif">Arial</option>
              <option value="'Times New Roman', serif">Times New Roman</option>
            </select>
          </div>

          <!-- Format Buttons -->
          <div class="tool-btn-group">
            <button type="button" class="btn-format" data-cmd="bold" title="Bold (Ctrl+B)"><strong>B</strong></button>
            <button type="button" class="btn-format" data-cmd="italic" title="Italic (Ctrl+I)"><em>I</em></button>
            <button type="button" class="btn-format" data-cmd="underline" title="Underline (Ctrl+U)"><u>U</u></button>
            <button type="button" class="btn-format" data-cmd="strikeThrough" title="Strikethrough"><s>S</s></button>
            <button type="button" class="btn-format" data-cmd="insertUnorderedList" title="Bullet List">• List</button>
            <button type="button" class="btn-format" data-cmd="insertOrderedList" title="Numbered List">1. List</button>
          </div>

          <!-- Action Power Tools -->
          <div class="action-tools-list">
            <button type="button" id="btnToolHighlight" class="btn-action-tool btn-tool-highlight">
              <span>🖍</span> Highlight Selected Text
            </button>
            <button type="button" id="btnToolWhiteout" class="btn-action-tool btn-tool-whiteout">
              <span>⬛</span> Whiteout / Redact Sensitive Data
            </button>
            <button type="button" id="btnToolImage" class="btn-action-tool">
              <span>🖼</span> Insert Image or Logo
            </button>
            <button type="button" id="btnToolSign" class="btn-action-tool">
              <span>✍</span> Draw &amp; Insert Signature
            </button>
            <button type="button" id="btnToolPageBreak" class="btn-action-tool">
              <span>📄</span> Insert Page Break
            </button>
            <div style="display:flex;gap:6px;margin-top:2px;">
              <button type="button" class="btn-action-tool" data-cmd="undo" style="flex:1;justify-content:center;">↶ Undo</button>
              <button type="button" class="btn-action-tool" data-cmd="redo" style="flex:1;justify-content:center;">↷ Redo</button>
            </div>
            <input type="file" id="fileImageInput" accept="image/*" style="display:none;">
          </div>

        </div>
      </div>

    </aside>

    <!-- ZONE B: Center Document Viewport (Dedicated Scroller) -->
    <main class="acrobat-canvas" id="canvas">

      <!-- PAGE 1: PRE-SCREENED CANDIDATE REPORT (Exact replica of media_1789225189169.png) -->
      <div class="paper-sheet" id="sheetReport" style="display:none;" contenteditable="true" spellcheck="false">

        <!-- Report Top Header -->
        <div class="ps-header">
          <div class="ps-shared-by">
            @if(!empty($companyLogo ?? null))
              <img src="{{ $companyLogo }}" alt="Company Logo" class="ps-company-logo-img">
            @else
              <svg viewBox="0 0 24 24" fill="#0284c7" style="width:34px;height:34px;"><path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/></svg>
            @endif
            <div>
              <div class="ps-shared-title">SHARED BY</div>
              <div class="ps-shared-name">{{ ($companyName ?? null) ?: '4Bell Technology' }}</div>
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
            <div class="ps-metric-val">{{ $noticePeriod ?? (@$application->np_id ?: 'Immediate / Serving') }}</div>
          </div>
          <div class="ps-metric-item">
            <div class="ps-metric-head">📍 LOCATION</div>
            <div class="ps-metric-val">{{ $cityName ?? 'Verified Location' }}</div>
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
              {{ $prescreenNotes ?? (@$application->comment ?: @$application->cover_letter ?: 'Candidate thoroughly pre-screened and assessed for technical competency, cultural fit, and delivery readiness.') }}
            </div>
          </div>
          <div class="ps-summary-card">
            <div class="ps-section-title">👤 RECRUITER COMMENTS</div>
            <div class="ps-text-content">
              Candidate thoroughly verified by {{ $recruiterName ?? (auth()->user()->name ?? 'Senior Technical Recruiter') }}. Clear communication, strong analytical aptitude, and immediate project delivery readiness verified.
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
            <div class="resume-brand-title">{{ strtoupper(($companyName ?? null) ?: 'CONFIDENTIAL CANDIDATE') }}</div>
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

    </main>

    <!-- ZONE C: Right Page Navigation Strip -->
    <aside class="acrobat-right-strip">
      <div class="page-counter-box" id="stripPageNum">1 / 2</div>
      <button type="button" class="strip-btn" id="stripPrevPage" title="Previous Page">▲</button>
      <button type="button" class="strip-btn" id="stripNextPage" title="Next Page">▼</button>
      <div style="width:22px;height:1px;background:#e2e8f0;margin:4px 0;"></div>
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
      <button type="button" id="btnSignClear" class="btn-action-tool" style="width:auto;padding:6px 12px;">Clear</button>
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
  var initialOpt = @json($optId ?? (int)request('opt', 61));
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

    var elMatched = document.getElementById('statMatched');
    if(elMatched) elMatched.textContent = matched;
    var elMissing = document.getElementById('statMissing');
    if(elMissing) elMissing.textContent = missing;
    var elHighlights = document.getElementById('statHighlights');
    if(elHighlights) elHighlights.textContent = totalHits;

    // Calculate ATS Score
    var fullText = (sheetReport && sheetReport.style.display !== 'none' ? sheetReport.innerText : '') + ' ' + (resumeBody.innerText || '');
    if(!optimizer && OptClass) optimizer = new OptClass({ jobSkills: jobSkills, candSkills: candSkills, candidateName: candName, targetJobTitle: jobTitle });
    if(optimizer){
      var res = optimizer.calculateAtsScore(fullText);
      var scoreEl = document.getElementById('statScore');
      if(scoreEl){
        scoreEl.textContent = res.score + '%';
        scoreEl.style.color = res.score >= 80 ? '#15803d' : (res.score >= 50 ? '#b45309' : '#b91c1c');
      }
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
    var mode = parseInt(modeSelect ? modeSelect.value : initialOpt, 10) || 61;
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
    curHit = 0;
  }

  function syncModeViews(){
    var mode = parseInt(modeSelect ? modeSelect.value : initialOpt, 10) || 61;
    var isBranded = (mode === 62);
    var hasReport = (mode === 64 || mode === 65);

    if(brandingBanner) brandingBanner.style.display = isBranded ? 'flex' : 'none';
    if(sheetReport) sheetReport.style.display = hasReport ? 'block' : 'none';

    var btnLbl = document.getElementById('btnDlText');
    if(btnLbl){
      if(isBranded) btnLbl.textContent = 'Download Branded Resume';
      else if(hasReport && mode === 65) btnLbl.textContent = 'Download Report + Highlighted';
      else if(hasReport) btnLbl.textContent = 'Download Report + Resume';
      else if(mode === 63) btnLbl.textContent = 'Download Highlighted Resume';
      else btnLbl.textContent = 'Download Resume (PDF)';
    }

    var pageCounter = document.getElementById('stripPageNum');
    if(pageCounter) pageCounter.textContent = hasReport ? '1 / 2' : '1 / 1';

    countOccurrences();
    renderChips();
    applyHighlights();
  }

  // Load authentic candidate resume
  async function loadOriginalResume(){
    if(window.opener && window.opener.__rcvResumeHtml && window.opener.__rcvResumeHtml.trim().length > 30){
      displayResume(window.opener.__rcvResumeHtml);
      return;
    }
    var openerText = window.opener && (window.opener.__rcvResumeText || window.opener.__rdcPreloadedResumeText);

    var bytes = null;
    if(window.opener && window.opener.__agyResumeCache){
      for(var k in window.opener.__agyResumeCache){
        var b = window.opener.__agyResumeCache[k];
        if(b && b.byteLength > 100){ bytes = b; break; }
      }
    }

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

  // Formatting Toolbar Buttons
  document.querySelectorAll('[data-cmd]').forEach(function(btn){
    btn.addEventListener('click', function(e){
      e.preventDefault();
      var cmd = this.getAttribute('data-cmd');
      document.execCommand(cmd, false, null);
    });
  });

  document.getElementById('selFormat').addEventListener('change', function(){
    document.execCommand('formatBlock', false, this.value);
  });

  document.getElementById('selFont').addEventListener('change', function(){
    document.execCommand('fontName', false, this.value);
  });

  document.getElementById('selSize').addEventListener('change', function(){
    document.execCommand('fontSize', false, this.value);
  });

  document.getElementById('pickerColor').addEventListener('input', function(){
    document.execCommand('foreColor', false, this.value);
  });

  // Action Tools
  document.getElementById('btnToolHighlight').onclick = function(){
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

  document.getElementById('btnToolWhiteout').onclick = function(){
    var sel = window.getSelection();
    if(!sel.rangeCount || sel.isCollapsed) return;
    var range = sel.getRangeAt(0);
    var mark = document.createElement('mark');
    mark.className = 'rcv-redact';
    mark.appendChild(range.extractContents());
    range.insertNode(mark);
  };

  document.getElementById('btnToolPageBreak').onclick = function(){
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
  document.getElementById('btnToolImage').onclick = function(){
    document.getElementById('fileImageInput').click();
  };
  document.getElementById('fileImageInput').addEventListener('change', function(e){
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

  document.getElementById('btnToolSign').onclick = function(){
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

  // Download PDF
  document.getElementById('btnDownload').onclick = async function(){
    var btn = document.getElementById('btnDownload');
    var orig = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<span class="spin"></span> Generating PDF…';
    try {
      var mode = parseInt(modeSelect ? modeSelect.value : initialOpt, 10) || 61;
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