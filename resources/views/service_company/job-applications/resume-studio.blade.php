<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>{{ ucwords(@$application->full_name ?: @$application->name ?: 'Candidate') }} — jero.works online editor</title>
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
    html, body { height: 100%; overflow: hidden; background: #f1f5f9; font-family: 'Poppins', -apple-system, BlinkMacSystemFont, sans-serif; color: #1e293b; }

    /* App Wrapper: Flex column with fixed header/controls and isolated scrollable viewport */
    .app-wrapper { display: flex; flex-direction: column; height: 100vh; overflow: hidden; }

    /* Top Enterprise Header */
    .top-header { height: 60px; background: #ffffff; border-bottom: 1px solid #e2e8f0; display: flex; align-items: center; justify-content: space-between; padding: 0 20px; flex-shrink: 0; z-index: 100; box-shadow: 0 1px 3px rgba(0,0,0,0.03); }
    .header-left { display: flex; align-items: center; gap: 14px; }
    .header-logo-link { display: flex; align-items: center; text-decoration: none; gap: 8px; }
    .header-logo-img { height: 26px; width: auto; display: block; }
    .header-title-box { display: flex; flex-direction: column; }
    .product-title { font-size: 14px; font-weight: 700; color: #0f172a; line-height: 1.2; letter-spacing: -0.3px; display: flex; align-items: center; gap: 6px; }
    .product-title span { color: #0d9488; }
    .studio-pill { font-size: 9px; font-weight: 700; text-transform: uppercase; background: #e0f2fe; color: #0369a1; padding: 1px 6px; border-radius: 4px; letter-spacing: 0.4px; }
    .header-vsep { width: 1px; height: 26px; background: #e2e8f0; margin: 0 4px; }
    .cand-badge { display: flex; align-items: center; gap: 8px; background: #f8fafc; border: 1px solid #e2e8f0; padding: 4px 12px; border-radius: 999px; }
    .cand-name { font-size: 12px; font-weight: 600; color: #0f172a; max-width: 170px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .role-name { font-size: 11px; font-weight: 500; color: #64748b; max-width: 200px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }

    .header-center { display: flex; align-items: center; gap: 8px; }
    .mode-label { font-size: 11.5px; font-weight: 600; color: #475569; display: flex; align-items: center; gap: 6px; }
    .mode-dropdown { padding: 6px 14px; font-size: 12px; font-family: 'Poppins', sans-serif; font-weight: 600; color: #0f172a; background: #f8fafc; border: 1.5px solid #cbd5e1; border-radius: 8px; outline: none; cursor: pointer; transition: all .15s; }
    .mode-dropdown:focus { border-color: #0d9488; background: #ffffff; box-shadow: 0 0 0 3px rgba(13,148,136,.12); }

    .header-right { display: flex; align-items: center; gap: 10px; }
    .stat-text { font-size: 11.5px; color: #64748b; font-weight: 500; }
    .btn-header { display: inline-flex; align-items: center; gap: 6px; padding: 8px 14px; border-radius: 7px; font-size: 12px; font-weight: 600; cursor: pointer; transition: all .15s; font-family: 'Poppins', sans-serif; }
    .btn-header-copy { background: #f8fafc; color: #334155; border: 1.5px solid #cbd5e1; }
    .btn-header-copy:hover { background: #f1f5f9; border-color: #94a3b8; }
    .btn-header-download { background: linear-gradient(135deg, #0d9488 0%, #0f766e 100%); color: #ffffff; border: none; box-shadow: 0 2px 6px rgba(13,148,136,.25); }
    .btn-header-download:hover { background: linear-gradient(135deg, #0f766e 0%, #115e59 100%); box-shadow: 0 4px 10px rgba(13,148,136,.35); }
    .btn-header-download:disabled { opacity: 0.65; cursor: not-allowed; }

    /* Pinned Control Cards Container (Fixed above document viewport) */
    .controls-panel { background: #ffffff; border-bottom: 1px solid #e2e8f0; flex-shrink: 0; box-shadow: 0 2px 8px rgba(0,0,0,0.03); z-index: 90; }

    /* Sub-Card 1: Real-time ATS & Keyword Highlights Card */
    .ats-card { padding: 12px 20px 10px 20px; border-bottom: 1px solid #f1f5f9; }
    .ats-card-header { display: flex; align-items: center; justify-content: space-between; gap: 16px; margin-bottom: 10px; }
    .ats-title-group h2 { font-size: 13.5px; font-weight: 700; color: #0f172a; margin: 0; display: flex; align-items: center; gap: 6px; }
    .ats-title-group p { font-size: 11px; color: #64748b; margin: 1px 0 0 0; }
    .ats-metrics-row { display: flex; align-items: center; gap: 8px; }
    .ats-metric-box { min-width: 82px; background: #f8fafc; border: 1.5px solid #e2e8f0; border-radius: 8px; padding: 5px 10px; text-align: center; }
    .ats-metric-val { display: block; font-size: 14px; font-weight: 700; color: #0f172a; line-height: 1.2; }
    .ats-metric-lbl { font-size: 9px; color: #64748b; text-transform: uppercase; letter-spacing: .4px; font-weight: 600; white-space: nowrap; }
    .ats-metric-box.box-ats { border-color: #bbf7d0; background: #f0fdf4; }
    .ats-metric-box.box-ats .ats-metric-val { color: #16a34a; }
    .ats-metric-box.box-ats .ats-metric-lbl { color: #15803d; }
    .ats-metric-box.box-matched .ats-metric-val { color: #0d9488; }
    .ats-metric-box.box-missing .ats-metric-val { color: #dc2626; }

    .ats-search-row { display: flex; gap: 8px; margin-bottom: 8px; }
    .ats-search-input { flex: 1; padding: 7px 12px; border: 1.5px solid #e2e8f0; border-radius: 7px; font-size: 11.5px; outline: none; font-family: 'Poppins', sans-serif; transition: border-color .15s; }
    .ats-search-input:focus { border-color: #6366f1; }
    .btn-ats-add { padding: 7px 16px; border: none; background: #6366f1; color: #fff; border-radius: 7px; font-size: 11.5px; font-weight: 600; cursor: pointer; white-space: nowrap; font-family: 'Poppins', sans-serif; transition: background .15s; }
    .btn-ats-add:hover { background: #4f46e5; }

    .ats-skills-strip { display: flex; align-items: center; gap: 8px; }
    .ats-skills-lbl { font-size: 10.5px; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; white-space: nowrap; }
    .ats-chips-wrapper { display: flex; flex-wrap: wrap; gap: 6px; align-items: center; flex: 1; }
    .kw-chip { display: inline-flex; align-items: center; gap: 5px; background: #fef08a; border: 1px solid #facc15; color: #854d0e; padding: 2px 9px; border-radius: 999px; font-size: 11px; font-weight: 600; cursor: pointer; transition: all .15s; user-select: none; }
    .kw-chip:hover { transform: translateY(-1px); box-shadow: 0 2px 4px rgba(250,204,21,.3); }
    .kw-chip.missing { background: #f1f5f9; border-color: #cbd5e1; color: #64748b; opacity: 0.85; }
    .kw-chip .chip-count { font-size: 9.5px; font-weight: 700; background: rgba(0,0,0,0.08); padding: 1px 5px; border-radius: 999px; }

    /* Sub-Card 2: Pinned Document Editing Ribbon */
    .editor-ribbon { padding: 8px 20px; background: #fafafa; border-top: 1px solid #f1f5f9; display: flex; align-items: center; justify-content: space-between; gap: 10px; flex-wrap: wrap; }
    .ribbon-left { display: flex; align-items: center; gap: 6px; flex-wrap: wrap; }
    .doc-pill { display: inline-flex; align-items: center; gap: 5px; background: #e0e7ff; color: #3730a3; border: 1px solid #c7d2fe; padding: 4px 10px; border-radius: 6px; font-size: 11px; font-weight: 600; max-width: 220px; }
    .doc-pill svg { width: 13px; height: 13px; flex-shrink: 0; }
    .doc-pill span { overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }

    .hit-nav-group { display: inline-flex; align-items: center; background: #ffffff; border: 1px solid #cbd5e1; border-radius: 6px; padding: 2px 6px; gap: 4px; font-size: 11px; font-weight: 600; color: #334155; }
    .hit-nav-btn { background: none; border: none; cursor: pointer; color: #475569; padding: 2px 4px; border-radius: 4px; display: flex; align-items: center; justify-content: center; }
    .hit-nav-btn:hover { background: #f1f5f9; color: #0f172a; }

    .tool-group { display: inline-flex; align-items: center; gap: 4px; background: #ffffff; border: 1px solid #cbd5e1; border-radius: 6px; padding: 2px 4px; }
    .tool-btn { background: none; border: none; padding: 4px 7px; border-radius: 4px; font-size: 11px; font-weight: 600; color: #334155; cursor: pointer; display: inline-flex; align-items: center; gap: 4px; font-family: 'Poppins', sans-serif; transition: all .1s; }
    .tool-btn:hover { background: #f1f5f9; color: #0f172a; }
    .tool-select { border: 1px solid #cbd5e1; border-radius: 4px; padding: 3px 6px; font-size: 11px; font-family: 'Poppins', sans-serif; font-weight: 500; color: #334155; background: #fff; outline: none; }
    .tool-color { width: 22px; height: 22px; border: 1px solid #cbd5e1; border-radius: 4px; padding: 0; cursor: pointer; background: none; }

    .btn-tool-highlight { background: #fef9c3 !important; color: #854d0e !important; border: 1px solid #fde047 !important; border-radius: 5px; padding: 3px 8px !important; }
    .btn-tool-highlight:hover { background: #fef08a !important; }
    .btn-tool-whiteout { background: #0f172a !important; color: #ffffff !important; border-radius: 5px; padding: 3px 8px !important; }
    .btn-tool-whiteout:hover { background: #1e293b !important; }
    .btn-tool-sign { background: #f0fdfa !important; color: #0f766e !important; border: 1px solid #99f6e4 !important; border-radius: 5px; padding: 3px 8px !important; }

    /* Sub-Card 3: Dedicated Document Viewport with Isolated Scroller */
    .document-viewport { flex: 1; overflow-y: auto; overflow-x: hidden; background: #525659; padding: 32px 16px 80px 16px; scroll-behavior: smooth; position: relative; }
    .document-viewport::-webkit-scrollbar { width: 10px; }
    .document-viewport::-webkit-scrollbar-track { background: #424649; }
    .document-viewport::-webkit-scrollbar-thumb { background: #6b7280; border-radius: 5px; }
    .document-viewport::-webkit-scrollbar-thumb:hover { background: #9ca3af; }

    /* Document Sheet (Standard A4 Paper Card Representation) */
    .paper-sheet { width: 816px; min-height: 1056px; margin: 0 auto 32px auto; background: #ffffff; box-shadow: 0 8px 30px rgba(0,0,0,0.35); border-radius: 4px; padding: 56px 64px; outline: none; position: relative; font-family: 'Poppins', -apple-system, BlinkMacSystemFont, sans-serif; font-size: 13px; line-height: 1.65; color: #1e293b; }
    .paper-sheet:focus { box-shadow: 0 8px 30px rgba(0,0,0,0.45), 0 0 0 2px #0d9488; }

    /* Page 1: Pre-Screened Report Dossier Card */
    .report-card { margin-bottom: 24px; }
    .report-dossier-header { display: flex; align-items: center; justify-content: space-between; border-bottom: 2px solid #0f172a; padding-bottom: 16px; margin-bottom: 20px; }
    .report-brand-title { font-size: 18px; font-weight: 800; color: #0f172a; text-transform: uppercase; letter-spacing: -0.3px; display: flex; align-items: center; gap: 8px; }
    .report-tag-badge { background: #0f172a; color: #ffffff; font-size: 10px; font-weight: 700; padding: 3px 8px; border-radius: 4px; letter-spacing: 0.5px; }
    .report-verification-stamp { display: inline-flex; align-items: center; gap: 5px; font-size: 11px; font-weight: 700; color: #15803d; background: #f0fdf4; border: 1.5px solid #86efac; padding: 4px 10px; border-radius: 6px; }

    .report-meta-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 14px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 16px; margin-bottom: 20px; }
    .meta-cell-lbl { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.4px; color: #64748b; margin-bottom: 2px; }
    .meta-cell-val { font-size: 13px; font-weight: 600; color: #0f172a; }

    .report-section-head { font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; color: #0f172a; border-left: 3px solid #0d9488; padding-left: 8px; margin: 18px 0 10px 0; }
    .report-summary-box { background: #fdf2f8; border: 1px solid #fbcfe8; border-radius: 8px; padding: 14px 16px; font-size: 12.5px; color: #334155; line-height: 1.6; margin-bottom: 18px; }

    .report-skills-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
    .report-skills-table th { background: #f1f5f9; text-align: left; padding: 8px 12px; font-size: 11px; font-weight: 700; text-transform: uppercase; color: #475569; border: 1px solid #e2e8f0; }
    .report-skills-table td { padding: 8px 12px; font-size: 12px; border: 1px solid #e2e8f0; color: #1e293b; }

    .report-page-banner { text-align: center; font-size: 11px; font-weight: 600; color: #64748b; background: #f1f5f9; border-radius: 6px; padding: 8px 12px; margin-top: 24px; border: 1px dashed #cbd5e1; }

    /* Company Branding Header (Option 62) */
    .company-branding-banner { display: flex; align-items: center; justify-content: space-between; border-bottom: 2px solid #0d9488; padding-bottom: 14px; margin-bottom: 24px; }
    .branding-left { display: flex; align-items: center; gap: 14px; }
    .branding-logo { max-height: 44px; max-width: 140px; object-fit: contain; }
    .branding-title { font-size: 16px; font-weight: 800; color: #0f172a; text-transform: uppercase; letter-spacing: -0.2px; }
    .branding-subtitle { font-size: 11px; color: #0d9488; font-weight: 600; }
    .branding-badge { background: #ccfbf1; color: #0f766e; font-size: 10.5px; font-weight: 700; padding: 3px 9px; border-radius: 999px; border: 1px solid #99f6e4; }

    /* In-Document Elements & Highlights */
    .paper-sheet h1 { font-size: 18px; font-weight: 700; color: #0f172a; margin: 18px 0 8px 0; border-bottom: 1px solid #e2e8f0; padding-bottom: 4px; }
    .paper-sheet h2 { font-size: 15px; font-weight: 700; color: #0f172a; margin: 14px 0 6px 0; }
    .paper-sheet h3 { font-size: 13.5px; font-weight: 600; color: #0f172a; margin: 10px 0 4px 0; }
    .paper-sheet p { margin-bottom: 8px; }
    .paper-sheet ul, .paper-sheet ol { margin-left: 20px; margin-bottom: 10px; }
    .paper-sheet li { margin-bottom: 3px; }

    mark.rcv-hit { background: #fef08a !important; color: #854d0e !important; padding: 1px 3px !important; border-radius: 3px !important; font-weight: 600 !important; }
    mark.rcv-hit.rcv-hit-active { background: #f97316 !important; color: #ffffff !important; box-shadow: 0 0 0 3px rgba(249,115,22,.4) !important; }
    mark.rcv-redact { background: #000000 !important; color: #000000 !important; user-select: none !important; padding: 0 4px !important; border-radius: 2px !important; }

    .doc-page-break { border-top: 2px dashed #94a3b8; margin: 30px 0; position: relative; text-align: center; }
    .doc-page-break::after { content: "PAGE BREAK"; font-size: 10px; font-weight: 700; color: #64748b; background: #fff; padding: 0 8px; position: absolute; top: -7px; left: 50%; transform: translateX(-50%); letter-spacing: 0.5px; }

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

<div class="app-wrapper">

  <!-- 1. Enterprise Top Header -->
  <header class="top-header">
    <div class="header-left">
      <a href="{{ route('service_company.dashboard') }}" class="header-logo-link" title="JERO Dashboard">
        <img src="{{ asset('img/logo.svg') }}" alt="JERO Logo" class="header-logo-img">
      </a>
      <div class="header-title-box">
        <div class="product-title">jero.works <span>online editor</span> <span class="studio-pill">ATS STUDIO</span></div>
      </div>
      <div class="header-vsep"></div>
      <div class="cand-badge">
        <span class="cand-name" title="{{ ucwords(@$application->full_name ?: @$application->name ?: 'Candidate') }}">{{ ucwords(@$application->full_name ?: @$application->name ?: 'Candidate') }}</span>
        <span class="role-name" title="{{ $job->title ?? ($application->job_title ?? 'Candidate') }}">• {{ $job->title ?? ($application->job_title ?? 'Verified Profile') }}</span>
      </div>
    </div>

    <div class="header-center">
      <label class="mode-label">
        <span>Format / Mode:</span>
        <select id="modeDropdown" class="mode-dropdown">
          <option value="61" {{ $optId == 61 ? 'selected' : '' }}>61. Edit &amp; Optimize Resume (Clean Vector PDF)</option>
          <option value="62" {{ $optId == 62 ? 'selected' : '' }}>62. Edit &amp; Optimize Resume + Company Branding</option>
          <option value="63" {{ $optId == 63 ? 'selected' : '' }}>63. Edit &amp; Optimize Resume + Highlighted Keywords</option>
          <option value="64" {{ $optId == 64 ? 'selected' : '' }}>64. Edit &amp; Optimize Resume + Pre-Screened Report</option>
          <option value="65" {{ $optId == 65 ? 'selected' : '' }}>65. Edit &amp; Optimize Resume + Pre-Screened Report + Highlighted Keywords</option>
        </select>
      </label>
    </div>

    <div class="header-right">
      <span class="stat-text" id="docStats">0 words • 0 chars</span>
      <button type="button" id="btnCopyText" class="btn-header btn-header-copy">📋 Copy Text</button>
      <button type="button" id="btnDownload" class="btn-header btn-header-download">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:14px;height:14px;"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
        <span id="btnDlText">Download Final PDF</span>
      </button>
    </div>
  </header>

  <!-- 2. Pinned Top Control Panels -->
  <div class="controls-panel">

    <!-- Sub-Card 1: Real-Time ATS & Keyword Highlights Card -->
    <div class="ats-card">
      <div class="ats-card-header">
        <div class="ats-title-group">
          <h2>Keyword Highlights</h2>
          <p>Job skills matched against this résumé • Real-time ATS match scoring</p>
        </div>
        <div class="ats-metrics-row">
          <div class="ats-metric-box box-matched"><span class="ats-metric-val" id="statMatched">0</span><span class="ats-metric-lbl">Matched</span></div>
          <div class="ats-metric-box box-missing"><span class="ats-metric-val" id="statMissing">0</span><span class="ats-metric-lbl">Missing</span></div>
          <div class="ats-metric-box"><span class="ats-metric-val" id="statHighlights">0</span><span class="ats-metric-lbl">Highlights</span></div>
          <div class="ats-metric-box box-ats"><span class="ats-metric-val" id="statScore">--%</span><span class="ats-metric-lbl">ATS Match</span></div>
        </div>
      </div>

      <div class="ats-search-row">
        <input type="text" id="kwSearchInput" class="ats-search-input" placeholder="Add a keyword to check &amp; highlight…" autocomplete="off">
        <button type="button" id="btnKwAdd" class="btn-ats-add">Add &amp; Check</button>
      </div>

      <div class="ats-skills-strip">
        <span class="ats-skills-lbl">Job Key Skills</span>
        <div class="ats-chips-wrapper" id="chipsContainer"></div>
      </div>
    </div>

    <!-- Sub-Card 2: Sticky Document Control Ribbon -->
    <div class="editor-ribbon">
      <div class="ribbon-left">
        <div class="doc-pill">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
          <span title="{{ basename($application->resume) }}">{{ basename($application->resume) }}</span>
        </div>

        <div class="hit-nav-group">
          <button type="button" id="navPrev" class="hit-nav-btn" title="Previous occurrence (Shift+F3)"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:12px;height:12px;"><polyline points="18 15 12 9 6 15"/></svg></button>
          <span id="navCount">0/0</span>
          <button type="button" id="navNext" class="hit-nav-btn" title="Next occurrence (F3)"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:12px;height:12px;"><polyline points="6 9 12 15 18 9"/></svg></button>
        </div>

        <div class="tool-group">
          <button type="button" class="tool-btn" data-cmd="bold" title="Bold (Ctrl+B)"><strong>B</strong></button>
          <button type="button" class="tool-btn" data-cmd="italic" title="Italic (Ctrl+I)"><em>I</em></button>
          <button type="button" class="tool-btn" data-cmd="underline" title="Underline (Ctrl+U)"><u>U</u></button>
          <button type="button" class="tool-btn" data-cmd="strikeThrough" title="Strikethrough"><s>S</s></button>
        </div>

        <select id="selFormat" class="tool-select" title="Paragraph Style">
          <option value="p">Paragraph</option>
          <option value="h1">Heading 1</option>
          <option value="h2">Heading 2</option>
          <option value="h3">Heading 3</option>
        </select>

        <select id="selFont" class="tool-select" title="Font Family">
          <option value="Poppins, sans-serif">Poppins</option>
          <option value="Inter, sans-serif">Inter</option>
          <option value="Calibri, sans-serif">Calibri</option>
          <option value="Arial, sans-serif">Arial</option>
          <option value="'Times New Roman', serif">Times</option>
        </select>

        <select id="selSize" class="tool-select" title="Font Size">
          <option value="2">11px</option>
          <option value="3" selected>13px</option>
          <option value="4">15px</option>
          <option value="5">18px</option>
        </select>

        <input type="color" id="pickerColor" class="tool-color" value="#1e293b" title="Text Color">

        <div class="tool-group">
          <button type="button" class="tool-btn" data-cmd="insertUnorderedList" title="Bullet List">• List</button>
          <button type="button" class="tool-btn" data-cmd="insertOrderedList" title="Numbered List">1. List</button>
        </div>

        <button type="button" id="btnHighlight" class="tool-btn btn-tool-highlight" title="Highlight Selected Text">🖍 Highlight</button>
        <button type="button" id="btnWhiteout" class="tool-btn btn-tool-whiteout" title="Redact / Whiteout">⬛ Whiteout</button>
        <button type="button" id="btnInsertImage" class="tool-btn" title="Insert Image / Logo">🖼 Image</button>
        <button type="button" id="btnSign" class="tool-btn btn-tool-sign" title="Draw Signature">✍ Sign</button>
        <button type="button" id="btnPageBreak" class="tool-btn" title="Insert Page Break">📄 Page</button>

        <div class="tool-group">
          <button type="button" class="tool-btn" data-cmd="undo" title="Undo (Ctrl+Z)">↶</button>
          <button type="button" class="tool-btn" data-cmd="redo" title="Redo (Ctrl+Y)">↷</button>
        </div>
        <input type="file" id="fileImageInput" accept="image/*" style="display:none;">
      </div>
    </div>

  </div>

  <!-- 3. Sub-Card 3: Dedicated Document Viewport (Isolated Scroller) -->
  <main class="document-viewport" id="viewport">

    <!-- Page 1: Pre-Screened Report Dossier Card (Options 64 & 65) -->
    <div class="paper-sheet report-card" id="pageReport" style="display:none;" contenteditable="true" spellcheck="false">
      <div class="report-dossier-header">
        <div>
          <span class="report-tag-badge">PAGE 1 • CONFIDENTIAL EVALUATION DOSSIER</span>
          <div class="report-brand-title">Pre-Screened Candidate Evaluation Report</div>
        </div>
        <div class="report-verification-stamp">
          <svg viewBox="0 0 24 24" fill="currentColor" style="width:14px;height:14px;"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
          Verified Candidate
        </div>
      </div>

      <div class="report-meta-grid">
        <div class="meta-cell">
          <div class="meta-cell-lbl">Candidate Name</div>
          <div class="meta-cell-val">{{ ucwords(@$application->full_name ?: @$application->name ?: 'Candidate') }}</div>
        </div>
        <div class="meta-cell">
          <div class="meta-cell-lbl">Target Role</div>
          <div class="meta-cell-val">{{ $job->title ?? ($application->job_title ?? 'Verified Role') }}</div>
        </div>
        <div class="meta-cell">
          <div class="meta-cell-lbl">Total Experience</div>
          <div class="meta-cell-val">{{ @$application->total_exp ? (@$application->total_exp . ' Years') : 'Verified Experience' }}</div>
        </div>
        <div class="meta-cell">
          <div class="meta-cell-lbl">Current Designation</div>
          <div class="meta-cell-val">{{ ucwords(@$application->current_designation ?: 'Specialist') }}</div>
        </div>
        <div class="meta-cell">
          <div class="meta-cell-lbl">Notice Period</div>
          <div class="meta-cell-val">{{ $noticePeriod }}</div>
        </div>
        <div class="meta-cell">
          <div class="meta-cell-lbl">Current Location</div>
          <div class="meta-cell-val">{{ $cityName }}</div>
        </div>
        <div class="meta-cell">
          <div class="meta-cell-lbl">Current / Expected CTC</div>
          <div class="meta-cell-val">{{ @$application->cur_sal ? (@$application->cur_sal . ' ' . @$application->cur_sal_unit) : 'Confidential' }} / {{ @$application->exp_sal ? (@$application->exp_sal . ' ' . @$application->exp_sal_unit) : 'Negotiable' }}</div>
        </div>
        <div class="meta-cell">
          <div class="meta-cell-lbl">Assessment Agency</div>
          <div class="meta-cell-val">{{ $companyName ?: '4Bell Technology' }}</div>
        </div>
        <div class="meta-cell">
          <div class="meta-cell-lbl">Lead Technical Recruiter</div>
          <div class="meta-cell-val">{{ $recruiterName }}</div>
        </div>
      </div>

      <div class="report-section-head">Key Competency Evaluation Scorecard</div>
      <table class="report-skills-table">
        <thead>
          <tr>
            <th style="width:40%;">Skill / Competency</th>
            <th style="width:30%;">Required by Job</th>
            <th style="width:30%;">Evaluation Status</th>
          </tr>
        </thead>
        <tbody>
          @foreach($jobSkills as $sk)
            <tr>
              <td><strong>{{ ucwords($sk) }}</strong></td>
              <td>Target Core Skill</td>
              <td><span style="color:#15803d;font-weight:600;">✓ Verified &amp; Screened</span></td>
            </tr>
          @endforeach
        </tbody>
      </table>

      <div class="report-section-head">Technical &amp; Cultural Assessment Summary</div>
      <div class="report-summary-box">
        {{ $prescreenNotes }}
      </div>

      <div class="report-page-banner">
        ── End of Pre-Screened Evaluation Dossier (Page 1) • Full Edited Résumé Continues on Page 2 ──
      </div>
    </div>

    <!-- Page 2+: Candidate Résumé Paper Sheet (All Modes) -->
    <div class="paper-sheet" id="pageResume" contenteditable="true" spellcheck="false">

      <!-- Company Branding Header (Option 62) -->
      <div class="company-branding-banner" id="brandingBanner" style="display:none;" contenteditable="false">
        <div class="branding-left">
          @if(!empty($companyLogo))
            <img src="{{ $companyLogo }}" alt="Company Logo" class="branding-logo">
          @endif
          <div>
            <div class="branding-title">{{ strtoupper($companyName ?: 'CONFIDENTIAL CANDIDATE') }}</div>
            <div class="branding-subtitle">Represented Candidate • Verified ATS Optimized Résumé</div>
          </div>
        </div>
        <span class="branding-badge">Branded Profile</span>
      </div>

      <!-- Resume Content Sheet -->
      <div id="resumeBody">
        <div id="loaderPlaceholder" style="display:flex;flex-direction:column;align-items:center;justify-content:center;padding:140px 20px;color:#64748b;gap:12px;" contenteditable="false">
          <div class="spin" style="width:30px;height:30px;border-color:#0d9488;border-top-color:transparent;border-width:3px;"></div>
          <span style="font-size:14px;font-weight:600;color:#0f172a;">Loading candidate résumé from Cloudflare R2…</span>
          <span style="font-size:11.5px;color:#64748b;">Rendering exact high-fidelity document pages for live editing</span>
        </div>
      </div>

    </div>

  </main>

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
      <button type="button" id="btnSignClear" class="tool-btn" style="padding:6px 12px;">Clear</button>
      <button type="button" id="btnSignInsert" class="btn-ats-add" style="padding:6px 14px;">Insert into Document</button>
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

  var pageReport = document.getElementById('pageReport');
  var pageResume = document.getElementById('pageResume');
  var resumeBody = document.getElementById('resumeBody');
  var brandingBanner = document.getElementById('brandingBanner');
  var modeDropdown = document.getElementById('modeDropdown');
  var chipsContainer = document.getElementById('chipsContainer');

  function esc(s){ return String(s||'').replace(/[&<>"]/g, function(c){ return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;'}[c]; }); }
  function escRe(s){ return String(s||'').replace(/[.*+?^${}()|[\]\\]/g, '\\$&'); }

  // 1. Initialize Keywords
  jobSkills.forEach(function(sk){
    if(sk && sk.trim()) keywords[sk.trim().toLowerCase()] = { name: sk.trim(), type: 'job', active: true, count: 0 };
  });

  function countOccurrences(){
    var text = (resumeBody.innerText || '') + ' ' + (pageReport ? pageReport.innerText : '');
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
    var fullText = (pageReport && pageReport.style.display !== 'none' ? pageReport.innerText : '') + ' ' + (resumeBody.innerText || '');
    var words = fullText.trim() ? fullText.trim().split(/\s+/).length : 0;
    document.getElementById('docStats').textContent = words + ' words • ' + fullText.length + ' chars';

    // Calculate ATS Score
    if(!optimizer && OptClass) optimizer = new OptClass({ jobSkills: jobSkills, candSkills: candSkills, candidateName: candName, targetJobTitle: jobTitle });
    if(optimizer){
      var res = optimizer.calculateAtsScore(fullText);
      var scoreEl = document.getElementById('statScore');
      scoreEl.textContent = res.score + '%';
      scoreEl.style.color = res.score >= 80 ? '#15803d' : (res.score >= 50 ? '#b45309' : '#b91c1c');
    }
  }

  function scrollToKeyword(kw){
    var activeMarks = Array.prototype.slice.call(pageResume.querySelectorAll('mark.rcv-hit'));
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
    var mode = parseInt(modeDropdown.value, 10) || 61;
    var isHighlighted = (mode === 63 || mode === 65);

    // Remove existing highlight tags
    var marks = Array.prototype.slice.call(pageResume.querySelectorAll('mark.rcv-hit'));
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

    hits = Array.prototype.slice.call(pageResume.querySelectorAll('mark.rcv-hit'));
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
    var mode = parseInt(modeDropdown.value, 10) || 61;
    var isBranded = (mode === 62);
    var hasReport = (mode === 64 || mode === 65);

    brandingBanner.style.display = isBranded ? 'flex' : 'none';
    pageReport.style.display = hasReport ? 'block' : 'none';

    var btnLbl = document.getElementById('btnDlText');
    if(isBranded) btnLbl.textContent = 'Download Resume (Branded)';
    else if(hasReport && mode === 65) btnLbl.textContent = 'Download Report + Highlighted';
    else if(hasReport) btnLbl.textContent = 'Download Report + Resume';
    else if(mode === 63) btnLbl.textContent = 'Download Resume (Highlighted)';
    else btnLbl.textContent = 'Download Resume (PDF)';

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
    var inp = document.getElementById('kwSearchInput');
    var val = (inp.value || '').trim();
    if(!val) return;
    keywords[val.toLowerCase()] = { name: val, type: 'custom', active: true, count: 0 };
    inp.value = '';
    countOccurrences();
    renderChips();
    applyHighlights();
  };
  document.getElementById('kwSearchInput').addEventListener('keydown', function(e){
    if(e.key === 'Enter'){ e.preventDefault(); document.getElementById('btnKwAdd').click(); }
  });

  // Hit navigation
  document.getElementById('navPrev').onclick = function(){ gotoHit(-1); };
  document.getElementById('navNext').onclick = function(){ gotoHit(1); };

  // Mode Dropdown change
  modeDropdown.addEventListener('change', function(){
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
  pageResume.addEventListener('input', onDocumentInput);
  pageReport.addEventListener('input', onDocumentInput);

  // Formatting Toolbar Buttons
  document.querySelectorAll('.tool-btn[data-cmd]').forEach(function(btn){
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

  // Advanced Tools
  document.getElementById('btnHighlight').onclick = function(){
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

  document.getElementById('btnWhiteout').onclick = function(){
    var sel = window.getSelection();
    if(!sel.rangeCount || sel.isCollapsed) return;
    var range = sel.getRangeAt(0);
    var mark = document.createElement('mark');
    mark.className = 'rcv-redact';
    mark.appendChild(range.extractContents());
    range.insertNode(mark);
  };

  document.getElementById('btnPageBreak').onclick = function(){
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
  document.getElementById('btnInsertImage').onclick = function(){
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

  document.getElementById('btnSign').onclick = function(){
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

  // Copy text
  document.getElementById('btnCopyText').onclick = function(){
    var text = (pageReport && pageReport.style.display !== 'none' ? pageReport.innerText + '\n\n' : '') + (resumeBody.innerText || '');
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
      var mode = parseInt(modeDropdown.value, 10) || 61;
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