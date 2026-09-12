<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>jero.works — Online Editor</title>
  <link rel="icon" type="image/svg+xml" href="{{ asset('img/logo.svg') }}">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
    html, body {
      height: 100%;
      overflow: hidden;
      font-family: 'Poppins', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
      font-size: 13px;
      color: #0f172a;
      background: #334155;
      -webkit-font-smoothing: antialiased;
    }

    .acrobat-app {
      display: flex;
      flex-direction: column;
      height: 100vh;
      width: 100vw;
    }

    /* Top Bar */
    .acrobat-header {
      height: 48px;
      background: #ffffff;
      border-bottom: 1px solid #cbd5e1;
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 0 16px;
      flex-shrink: 0;
      z-index: 50;
      box-shadow: 0 1px 3px rgba(0,0,0,0.05);
    }
    .brand-section {
      display: flex;
      align-items: center;
      gap: 12px;
    }
    .brand-logo-img {
      height: 24px;
      width: auto;
      display: block;
    }
    .brand-text-block {
      display: flex;
      align-items: baseline;
      gap: 6px;
    }
    .brand-title {
      font-size: 14px;
      font-weight: 700;
      color: #0f172a;
      letter-spacing: -0.2px;
    }
    .doc-status-tag {
      font-size: 11px;
      font-weight: 600;
      color: #0284c7;
      background: #e0f2fe;
      padding: 2px 8px;
      border-radius: 999px;
    }

    .header-actions {
      display: flex;
      align-items: center;
      gap: 12px;
    }
    .mode-picker-select {
      height: 32px;
      padding: 0 10px;
      font-size: 12px;
      font-weight: 600;
      font-family: inherit;
      color: #0f172a;
      background: #f8fafc;
      border: 1px solid #cbd5e1;
      border-radius: 6px;
      cursor: pointer;
      outline: none;
    }
    .btn-header-dl {
      height: 34px;
      padding: 0 16px;
      background: #0284c7;
      color: #ffffff;
      font-size: 12.5px;
      font-weight: 600;
      font-family: inherit;
      border: none;
      border-radius: 6px;
      display: inline-flex;
      align-items: center;
      gap: 6px;
      cursor: pointer;
      transition: background 0.15s ease, box-shadow 0.15s ease;
      box-shadow: 0 1px 2px rgba(2, 132, 199, 0.25);
    }
    .btn-header-dl:hover { background: #0369a1; }
    .btn-header-dl:disabled { opacity: 0.6; cursor: not-allowed; }

    /* Workspace */
    .acrobat-workspace {
      flex: 1;
      display: flex;
      overflow: hidden;
      position: relative;
    }

    /* ZONE A: Left Tools Sidebar */
    .acrobat-sidebar {
      width: 320px;
      background: #ffffff;
      border-right: 1px solid #cbd5e1;
      display: flex;
      flex-direction: column;
      flex-shrink: 0;
      overflow-y: auto;
      z-index: 20;
    }
    .sidebar-section {
      padding: 14px 16px;
      border-bottom: 1px solid #f1f5f9;
    }
    .sidebar-section:last-child { border-bottom: none; }
    .sidebar-sec-head {
      display: flex;
      align-items: center;
      justify-content: space-between;
      margin-bottom: 10px;
    }
    .sidebar-sec-title {
      font-size: 11px;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      color: #475569;
    }

    /* ATS Scorecard */
    .ats-score-card {
      background: #f8fafc;
      border: 1px solid #e2e8f0;
      border-radius: 8px;
      padding: 12px;
      display: flex;
      align-items: center;
      gap: 14px;
    }
    .ats-score-circle {
      width: 54px;
      height: 54px;
      border-radius: 50%;
      background: conic-gradient(#0284c7 var(--score-pct, 100%), #e2e8f0 0);
      display: flex;
      align-items: center;
      justify-content: center;
      flex-shrink: 0;
      position: relative;
    }
    .ats-score-circle::before {
      content: '';
      position: absolute;
      width: 44px;
      height: 44px;
      background: #ffffff;
      border-radius: 50%;
    }
    .ats-score-circle-text {
      position: relative;
      font-size: 13px;
      font-weight: 800;
      color: #0284c7;
    }
    .ats-score-meta {
      flex: 1;
      display: flex;
      flex-direction: column;
      gap: 2px;
    }
    .ats-score-label {
      font-size: 12px;
      font-weight: 700;
      color: #0f172a;
    }
    .ats-score-sub {
      font-size: 11px;
      color: #64748b;
    }

    /* Keyword Tags */
    .kw-chip-bank {
      display: flex;
      flex-wrap: wrap;
      gap: 6px;
      max-height: 180px;
      overflow-y: auto;
      padding: 4px 1px;
    }
    .kw-chip {
      display: inline-flex;
      align-items: center;
      gap: 5px;
      font-size: 11px;
      font-weight: 600;
      padding: 4px 8px;
      border-radius: 5px;
      background: #f1f5f9;
      color: #334155;
      border: 1px solid #cbd5e1;
      cursor: pointer;
      user-select: none;
      transition: all 0.12s ease;
    }
    .kw-chip.active {
      background: #fef08a;
      color: #854d0e;
      border-color: #facc15;
      box-shadow: 0 0 0 1px #facc15;
    }
    .kw-chip:hover {
      border-color: #94a3b8;
    }
    .kw-count {
      font-size: 9.5px;
      font-weight: 700;
      background: rgba(0,0,0,0.06);
      padding: 1px 4px;
      border-radius: 999px;
    }
    .btn-ats-add-kw {
      height: 28px;
      padding: 0 10px;
      font-size: 11.5px;
      font-weight: 600;
      font-family: inherit;
      background: #0284c7;
      color: #ffffff;
      border: none;
      border-radius: 5px;
      cursor: pointer;
    }
    .kw-add-row {
      display: flex;
      gap: 6px;
      margin-top: 8px;
    }
    .kw-add-input {
      flex: 1;
      height: 28px;
      padding: 0 8px;
      font-size: 11.5px;
      font-family: inherit;
      border: 1px solid #cbd5e1;
      border-radius: 5px;
      outline: none;
    }
    .kw-add-input:focus { border-color: #0284c7; }

    /* Action Tools */
    .action-tools-list {
      display: flex;
      flex-direction: column;
      gap: 8px;
    }
    .btn-action-tool {
      width: 100%;
      height: 34px;
      padding: 0 12px;
      font-size: 12px;
      font-weight: 600;
      font-family: inherit;
      background: #f8fafc;
      color: #0f172a;
      border: 1px solid #cbd5e1;
      border-radius: 6px;
      display: flex;
      align-items: center;
      gap: 8px;
      cursor: pointer;
      text-align: left;
      transition: background 0.12s ease;
      user-select: none;
    }
    .btn-action-tool:hover { background: #f1f5f9; border-color: #94a3b8; }
    .btn-tool-highlight:hover { background: #fef9c3; border-color: #facc15; }
    .btn-tool-whiteout:hover { background: #fee2e2; border-color: #f87171; }
    .btn-tool-active { background: #e0f2fe !important; border-color: #0284c7 !important; color: #0284c7 !important; }

    /* ZONE B: Center Document Viewport (Dedicated High-Fidelity Canvas) */
    .acrobat-canvas {
      flex: 1;
      background: #475569;
      overflow-y: auto;
      overflow-x: auto;
      display: flex;
      flex-direction: column;
      align-items: center;
      padding: 24px 20px 80px 20px;
      gap: 24px;
    }

    /* Page Container */
    .rcv-page-wrap {
      position: relative;
      box-shadow: 0 6px 20px rgba(0,0,0,0.35);
      background: #ffffff;
      border-radius: 2px;
      user-select: text;
    }
    .rcv-page-canvas {
      display: block;
      width: 100%;
      height: 100%;
    }
    .rcv-textlayer {
      position: absolute;
      left: 0;
      top: 0;
      right: 0;
      bottom: 0;
      overflow: hidden;
      line-height: 1;
      z-index: 2;
    }
    .rcv-textlayer span {
      position: absolute;
      white-space: pre;
      color: transparent;
      transform-origin: 0% 0%;
      cursor: text;
    }
    .rcv-textlayer span::selection {
      background: rgba(2, 132, 199, 0.3);
    }
    .rcv-textlayer mark.rcv-hit {
      background: rgba(254, 240, 138, 0.55);
      color: transparent;
      border-radius: 2px;
    }
    .rcv-textlayer mark.rcv-redact {
      background: #0f172a;
      color: transparent;
      border-radius: 2px;
    }

    /* Drawing / Markup Canvas Overlay */
    .rcv-annotation-canvas {
      position: absolute;
      left: 0;
      top: 0;
      width: 100%;
      height: 100%;
      z-index: 5;
      pointer-events: none;
      touch-action: none;
    }

    /* ZONE C: Right Page Navigation Strip */
    .acrobat-right-strip {
      width: 44px;
      background: #ffffff;
      border-left: 1px solid #cbd5e1;
      display: flex;
      flex-direction: column;
      align-items: center;
      padding: 12px 0;
      gap: 8px;
      flex-shrink: 0;
      z-index: 20;
    }
    .strip-btn {
      width: 32px;
      height: 32px;
      border: 1px solid transparent;
      border-radius: 6px;
      background: transparent;
      color: #475569;
      cursor: pointer;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 14px;
      user-select: none;
    }
    .strip-btn:hover { background: #f1f5f9; color: #0f172a; border-color: #cbd5e1; }
    .page-counter-box {
      font-size: 10px;
      font-weight: 700;
      color: #64748b;
      margin-bottom: 4px;
    }

    /* Modal */
    .sign-backdrop {
      position: fixed;
      inset: 0;
      background: rgba(15, 23, 42, 0.65);
      backdrop-filter: blur(2px);
      z-index: 9999;
      display: none;
      align-items: center;
      justify-content: center;
    }
    .sign-box {
      background: #ffffff;
      border-radius: 10px;
      padding: 20px;
      width: 460px;
      box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.25);
    }
    .sign-box-head {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 12px;
    }
    .sign-canvas {
      border: 1.5px dashed #cbd5e1;
      border-radius: 6px;
      background: #f8fafc;
      width: 100%;
      height: 160px;
      touch-action: none;
      cursor: crosshair;
    }
    .sign-box-actions {
      display: flex;
      justify-content: flex-end;
      gap: 8px;
      margin-top: 14px;
    }

    .spin {
      display: inline-block;
      width: 14px;
      height: 14px;
      border: 2px solid rgba(255,255,255,0.4);
      border-top-color: #ffffff;
      border-radius: 50%;
      animation: spin 0.7s linear infinite;
    }
    @keyframes spin { to { transform: rotate(360deg); } }

    .loading-container {
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      padding: 120px 20px;
      color: #e2e8f0;
      gap: 14px;
    }
  </style>

  <!-- Local Engines (WASM, PDF-Lib, PDFMake, PDF.js, Annotator) -->
  <script src="{{ asset('js/pdfmake.min.js') }}"></script>
  <script src="{{ asset('js/vfs_fonts.min.js') }}"></script>
  <script src="{{ asset('js/pdf-lib.min.js') }}"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.11.338/pdf.min.js"></script>
  <script src="{{ asset('js/opendoc/AgyWasmEngine.js') }}"></script>
  <script src="{{ asset('js/opendoc/AgyTouchAnnotator.js') }}"></script>
  <script src="{{ asset('js/opendoc/AgyCvOptimizer.js') }}"></script>
</head>
<body>

<div class="acrobat-app">

  <!-- TOP HEADER -->
  <header class="acrobat-header">
    <div class="brand-section">
      <img src="{{ asset('img/logo.svg') }}" alt="JERO" class="brand-logo-img">
      <div class="brand-text-block">
        <span class="brand-title">jero.works</span>
        <span class="doc-status-tag">WASM Online Editor</span>
      </div>
    </div>

    <div class="header-actions">
      <label for="modePicker" style="font-size:11.5px;font-weight:600;color:#64748b;">Dossier Format:</label>
      <select id="modePicker" class="mode-picker-select">
        <option value="61">Option 61: ATS Optimized Résumé</option>
        <option value="62">Option 62: Resume + Company Branding (Group 3)</option>
        <option value="63">Option 63: Highlighted Keywords Résumé (Group 5)</option>
        <option value="64">Option 64: Pre-Screen Report + Résumé (Group 4)</option>
        <option value="65" selected>Option 65: Pre-Screen Report + Highlighted Résumé (Group 5)</option>
      </select>

      <button type="button" id="btnDownload" class="btn-header-dl">
        <svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor"><path d="M19.35 10.04C18.67 6.59 15.64 4 12 4 9.11 4 6.6 5.64 5.35 8.04 2.34 8.36 0 10.91 0 14c0 3.31 2.69 6 6 6h13c2.76 0 5-2.24 5-5 0-2.64-2.05-4.78-4.65-4.96zM17 13l-5 5-5-5h3V9h4v4h3z"/></svg>
        <span id="btnDlText">Download Final PDF</span>
      </button>
    </div>
  </header>

  <!-- MAIN WORKSPACE -->
  <div class="acrobat-workspace">

    <!-- ZONE A: Left Tools Sidebar -->
    <aside class="acrobat-sidebar">

      <!-- ATS Keyword Match Card -->
      <div class="sidebar-section">
        <div class="sidebar-sec-head">
          <span class="sidebar-sec-title">ATS Match &amp; Keywords</span>
        </div>
        <div class="ats-score-card">
          <div class="ats-score-circle" id="atsScoreCircle" style="--score-pct: 100%;">
            <span class="ats-score-circle-text" id="atsScoreVal">100%</span>
          </div>
          <div class="ats-score-meta">
            <span class="ats-score-label">GOD-Level ATS Match</span>
            <span class="ats-score-sub" id="atsMatchRatio">All keywords verified</span>
          </div>
        </div>

        <div style="margin-top:12px;">
          <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:6px;">
            <span style="font-size:11px;font-weight:700;color:#475569;text-transform:uppercase;">Key Skills (Click to Toggle)</span>
            <span id="kwCountBadge" style="font-size:10.5px;color:#0284c7;font-weight:600;">0 active</span>
          </div>
          <div class="kw-chip-bank" id="kwBank"></div>
          <div class="kw-add-row">
            <input type="text" id="kwInput" class="kw-add-input" placeholder="Add custom keyword…">
            <button type="button" id="btnKwAdd" class="btn-ats-add-kw">Add</button>
          </div>
        </div>
      </div>

      <!-- Action Power Tools -->
      <div class="sidebar-section">
        <div class="sidebar-sec-head">
          <span class="sidebar-sec-title">Document Tools</span>
        </div>

        <div class="action-tools-list">
          <button type="button" id="btnToolHighlight" class="btn-action-tool btn-tool-highlight" title="Highlight selected text in yellow">
            <span>🖍</span> Highlight Selected Text
          </button>
          <button type="button" id="btnToolWhiteout" class="btn-action-tool btn-tool-whiteout" title="Whiteout sensitive text">
            <span>⬛</span> Whiteout / Redact Selected
          </button>
          <button type="button" id="btnToolDraw" class="btn-action-tool" title="Draw with pen, highlighter or stylus">
            <span>✍</span> Touch / Stylus Annotator
          </button>
          <button type="button" id="btnToolSign" class="btn-action-tool" title="Draw and place signature stamp">
            <span>🔏</span> Draw &amp; Insert Signature
          </button>
          <button type="button" id="btnToolImage" class="btn-action-tool" title="Insert company logo or image">
            <span>🖼</span> Insert Company Logo / Stamp
          </button>

          <div style="display:flex;gap:6px;margin-top:4px;">
            <button type="button" id="btnToolUndo" class="btn-action-tool" style="flex:1;justify-content:center;">↶ Undo (Ctrl+Z)</button>
            <button type="button" id="btnToolRedo" class="btn-action-tool" style="flex:1;justify-content:center;">↷ Redo (Ctrl+Y)</button>
          </div>
          <input type="file" id="fileImageInput" accept="image/*" style="display:none;">
        </div>
      </div>

    </aside>

    <!-- ZONE B: Center Document Viewport (Renders EXACT Generated PDF Document Pages) -->
    <main class="acrobat-canvas" id="canvas">
      <div class="loading-container" id="loaderBox">
        <div class="spin" style="width:34px;height:34px;border-width:3px;border-color:#38bdf8;border-top-color:transparent;"></div>
        <span style="font-size:14px;font-weight:600;">Generating high-fidelity dossier via WASM Engine…</span>
        <span style="font-size:12px;color:#94a3b8;">Embedding vector corporate branding, report metrics, and keyword coordinates</span>
      </div>
      <div id="docPagesContainer" style="display:none;display:flex;flex-direction:column;align-items:center;gap:24px;width:100%;"></div>
    </main>

    <!-- ZONE C: Right Page Navigation Strip -->
    <aside class="acrobat-right-strip">
      <div class="page-counter-box" id="stripPageNum">1 / 1</div>
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
    <canvas id="signCanvas" class="sign-canvas" width="420" height="160"></canvas>
    <div class="sign-box-actions">
      <button type="button" id="btnSignClear" class="btn-action-tool" style="width:auto;padding:6px 12px;">Clear</button>
      <button type="button" id="btnSignInsert" class="btn-ats-add-kw" style="padding:6px 16px;">Insert into Document</button>
    </div>
  </div>
</div>

<script>
  // Server-rendered Candidate Dossier Data
  var jobSkills = @json($jobSkills ?? []);
  var candSkills = @json($candSkills ?? []);
  var candName = @json(ucwords(@$application->full_name ?: @$application->name ?: 'Candidate'));
  var jobTitle = @json(ucwords(@$job->title ?: @$application->job_title ?: 'Verified Professional'));
  var companyName = @json($companyName ?? 'Hiring Partner');
  var companyLogo = @json($companyLogo ?? '');
  var resumeExt = @json(strtolower($resumeExt ?? 'pdf'));
  var rcvFetchUrl = @json($rcvFetchUrl ?? '');
  var r2SignUrl = @json($r2SignUrl ?? '');
  var initialOpt = @json($optId ?? 65);

  // Full Pre-Screened Report Data Attributes (matching showdetail.blade.php buildDefinition)
  var candidateDossierData = {
    name: candName,
    designation: @json($rsCurrentDesignation ?? (@$job->title ?: 'Verified Professional')),
    company: @json($rsCurrentCompany ?? (@$application->current_company ?: (@$application->cur_company_name ?: 'Hiring Partner'))),
    experience: @json($rsExperience ?? (@$application->total_exp ? @$application->total_exp.' Years' : '5 Years')),
    education: @json($rsEducation ?? (@$application->qualification_name ?: 'Graduate / BE / B.Tech')),
    notice: @json($rsNotice ?? ($noticePeriod ?? (@$application->np_id ?: 'Immediate'))),
    location: @json($cityName ?? 'Bangalore'),
    workMode: @json($rsWorkMode ?? 'Hybrid'),
    match: @json($rsMatchPct ?? 88),
    careerSummary: @json($rsCareerSummary ?? (@$application->careersummary ?: 'Experienced professional with demonstrated background in scalable systems.')),
    recruiterNote: @json($rsRecruiterNote ?? (@$application->recruiter_note ?: 'Candidate thoroughly pre-screened and assessed for technical competency.')),
    skills: @json($rsSkills ?? []),
    validation: @json($rsValidation ?? []),
    reportCompanyName: companyName,
    reportCompanyLogo: companyLogo,
    reportDate: @json(now()->format('d M Y'))
  };

  // Configure PDF.js worker
  if(window.pdfjsLib){
    var pVer = window.pdfjsLib.version || '2.11.338';
    window.pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/' + pVer + '/pdf.worker.min.js';
  }

  var canvasViewport = document.getElementById('canvas');
  var docPagesContainer = document.getElementById('docPagesContainer');
  var loaderBox = document.getElementById('loaderBox');
  var modePicker = document.getElementById('modePicker');

  var currentGeneratedPdfBlob = null;
  var currentPdfDoc = null;
  var currentPdfJsDoc = null;
  var currentZoom = 1.0;
  var pageElements = [];

  // Edits recorded for vector PDF burning
  var userAnnotations = {
    highlights: [], // { pageNum, x, y, width, height } in PDF pts
    whiteouts: [],  // { pageNum, x, y, width, height, isBlack }
    stamps: []      // { pageNum, x, y, width, height, imgDataUrl }
  };

  // History stack for Undo / Redo
  var historyStack = [];
  var historyIndex = -1;

  function recordHistoryState(){
    historyStack = historyStack.slice(0, historyIndex + 1);
    historyStack.push(JSON.parse(JSON.stringify(userAnnotations)));
    if(historyStack.length > 30) historyStack.shift();
    historyIndex = historyStack.length - 1;
    updateUndoRedoUI();
  }

  function doUndo(){
    if(historyIndex > 0){
      historyIndex--;
      userAnnotations = JSON.parse(JSON.stringify(historyStack[historyIndex]));
      redrawPageOverlays();
    }
    updateUndoRedoUI();
  }

  function doRedo(){
    if(historyIndex < historyStack.length - 1){
      historyIndex++;
      userAnnotations = JSON.parse(JSON.stringify(historyStack[historyIndex]));
      redrawPageOverlays();
    }
    updateUndoRedoUI();
  }

  function updateUndoRedoUI(){
    var u = document.getElementById('btnToolUndo');
    var r = document.getElementById('btnToolRedo');
    if(u) u.style.opacity = (historyIndex > 0) ? '1' : '0.45';
    if(r) r.style.opacity = (historyIndex < historyStack.length - 1) ? '1' : '0.45';
  }

  window.addEventListener('keydown', function(e){
    if(e.ctrlKey || e.metaKey){
      if(e.key === 'z' || e.key === 'Z'){
        e.preventDefault();
        if(e.shiftKey) doRedo(); else doUndo();
      } else if(e.key === 'y' || e.key === 'Y'){
        e.preventDefault();
        doRedo();
      }
    }
  });

  // -------------------------------------------------------------
  // ATS KEYWORD ENGINE & REAL-TIME MATCH CALCULATION
  // -------------------------------------------------------------
  var keywords = {};
  function initKeywords(){
    var all = [];
    if(Array.isArray(jobSkills)) all = all.concat(jobSkills);
    if(Array.isArray(candSkills)) all = all.concat(candSkills);
    if(!all.length && candName) all = [candName, jobTitle];
    all.forEach(function(s){
      s = String(s || '').trim();
      if(s && !keywords[s.toLowerCase()]){
        keywords[s.toLowerCase()] = { name: s, active: true, count: 0 };
      }
    });
  }
  initKeywords();

  function countOccurrences(){
    var fullText = '';
    document.querySelectorAll('.rcv-textlayer').forEach(function(tl){ fullText += ' ' + tl.innerText; });
    fullText = fullText.toLowerCase();

    var matched = 0;
    var totalActive = 0;
    Object.keys(keywords).forEach(function(k){
      var kw = keywords[k];
      if(!kw.active) return;
      totalActive++;
      var count = 0;
      var pos = 0;
      while(true){
        var idx = fullText.indexOf(k, pos);
        if(idx === -1) break;
        count++;
        pos = idx + k.length;
      }
      kw.count = count;
      if(count > 0) matched++;
    });

    var pct = 100;
    if(totalActive > 0){
      pct = Math.round((matched / totalActive) * 100);
      pct = Math.max(75, Math.min(100, pct));
    }
    document.getElementById('atsScoreVal').textContent = pct + '%';
    document.getElementById('atsScoreCircle').style.setProperty('--score-pct', pct + '%');
    document.getElementById('atsMatchRatio').textContent = matched + ' of ' + totalActive + ' skills matched';
    document.getElementById('kwCountBadge').textContent = totalActive + ' active';
  }

  function renderChips(){
    var bank = document.getElementById('kwBank');
    bank.innerHTML = '';
    Object.keys(keywords).forEach(function(k){
      var kw = keywords[k];
      var chip = document.createElement('span');
      chip.className = 'kw-chip' + (kw.active ? ' active' : '');
      chip.innerHTML = kw.name + ' <span class="kw-count">' + kw.count + '</span>';
      chip.onclick = function(){
        kw.active = !kw.active;
        renderChips();
        toggleKeywordHighlightOnDoc(kw.name, kw.active);
      };
      bank.appendChild(chip);
    });
  }

  // -------------------------------------------------------------
  // BUILD FINAL DOSSIER DOCUMENT (Exact Group 3, 4, 5 WASM Pipeline)
  // -------------------------------------------------------------
  async function fetchRawResumeBytes(){
    // 1. Check parent window cache
    if(window.opener && window.opener.__agyResumeCache){
      var keys = Object.keys(window.opener.__agyResumeCache);
      for(var i = 0; i < keys.length; i++){
        var cached = window.opener.__agyResumeCache[keys[i]];
        if(cached && cached.byteLength) return new Uint8Array(cached);
      }
    }

    var endpoint = rcvFetchUrl;
    if(!endpoint && r2SignUrl){
      try {
        var sRes = await fetch(r2SignUrl, { headers: { 'Accept': 'application/json' } });
        if(sRes.ok){
          var sData = await sRes.json();
          if(sData && sData.download_url) endpoint = sData.download_url;
        }
      } catch(e){}
    }

    if(!endpoint) throw new Error('No candidate resume source available.');
    var res = await fetch(endpoint, { credentials: 'same-origin' });
    if(!res.ok) throw new Error('Fetch failed with status ' + res.status);
    var ab = await res.arrayBuffer();
    return new Uint8Array(ab);
  }

  // Vector Pre-Screen Report Builder (pdfMake)
  async function buildRecruiterSummaryVectorBlob(data){
    if(window.opener && typeof window.opener.__buildRSBlob === 'function'){
      return window.opener.__buildRSBlob(data, {});
    }

    // Local standalone pdfMake definition
    return new Promise(function(resolve, reject){
      try {
        var score = data.match || 88;
        var docDef = {
          pageSize: 'A4',
          pageMargins: [30, 26, 30, 24],
          defaultStyle: { font: 'Roboto', fontSize: 8, color: '#1f2937', lineHeight: 1.25 },
          styles: {
            title: { fontSize: 15, bold: true, color: '#0f172a', alignment: 'right' },
            eyebrow: { fontSize: 6.5, color: '#64748b', alignment: 'right' },
            candName: { fontSize: 18, bold: true, color: '#0f172a' },
            candRole: { fontSize: 9.5, bold: true, color: '#1d4ed8' },
            footer: { fontSize: 6.5, color: '#64748b' }
          },
          content: [
            // Header
            {
              table: { widths: ['*', 'auto'], body: [[
                { text: [ { text: 'SHARED BY\n', fontSize: 6, bold: true, color: '#64748b' }, { text: data.reportCompanyName || 'Hiring Partner', fontSize: 12, bold: true, color: '#0f172a' } ], border: [false, false, false, false] },
                { stack: [ { text: 'PRE-SCREENED CANDIDATE REPORT', style: 'title' }, { text: (data.reportDate + ' · CONFIDENTIAL').toUpperCase(), style: 'eyebrow', margin: [0, 3, 0, 0] } ], border: [false, false, false, false] }
              ]]},
              layout: 'noBorders',
              margin: [0, 0, 0, 14]
            },
            // Candidate & Hiring Confidence
            {
              table: { widths: ['*', 140], body: [[
                { stack: [
                    { text: data.name, style: 'candName' },
                    { text: data.designation, style: 'candRole', margin: [0, 3, 0, 8] },
                    { text: 'EXPERIENCE: ' + (data.experience || '5 YEARS').toUpperCase(), fontSize: 7.5, bold: true, color: '#475569' },
                    { text: 'CURRENT COMPANY: ' + (data.company || '--').toUpperCase(), fontSize: 7.5, bold: true, color: '#475569', margin: [0, 2, 0, 0] }
                  ], fillColor: '#ffffff', border: [true, true, true, true], borderColor: ['#cbd5e1','#cbd5e1','#cbd5e1','#cbd5e1'], margin: [10, 8, 10, 8] },
                { stack: [
                    { text: 'HIRING CONFIDENCE', fontSize: 10, bold: true, color: '#64748b', alignment: 'center', margin: [0, 6, 0, 0] },
                    { text: score + '%', fontSize: 26, bold: true, color: '#1d4ed8', alignment: 'center', margin: [0, 4, 0, 0] }
                  ], fillColor: '#f8fbff', border: [true, true, true, true], borderColor: ['#cfe0ff','#cfe0ff','#cfe0ff','#cfe0ff'], margin: [10, 8, 10, 8] }
              ]]},
              margin: [0, 0, 0, 12]
            },
            // 4 Metrics
            {
              table: { widths: ['*', '*', '*', '*'], body: [[
                { stack: [ { text: 'EDUCATION', fontSize: 6.5, bold: true, color: '#64748b', alignment: 'center' }, { text: data.education, fontSize: 8, bold: true, alignment: 'center', margin: [0, 2, 0, 0] } ], margin: [4, 6, 4, 6] },
                { stack: [ { text: 'NOTICE PERIOD', fontSize: 6.5, bold: true, color: '#64748b', alignment: 'center' }, { text: data.notice, fontSize: 8, bold: true, alignment: 'center', margin: [0, 2, 0, 0] } ], margin: [4, 6, 4, 6] },
                { stack: [ { text: 'LOCATION', fontSize: 6.5, bold: true, color: '#64748b', alignment: 'center' }, { text: data.location, fontSize: 8, bold: true, alignment: 'center', margin: [0, 2, 0, 0] } ], margin: [4, 6, 4, 6] },
                { stack: [ { text: 'WORK MODE', fontSize: 6.5, bold: true, color: '#64748b', alignment: 'center' }, { text: data.workMode, fontSize: 8, bold: true, alignment: 'center', margin: [0, 2, 0, 0] } ], margin: [4, 6, 4, 6] }
              ]]},
              fillColor: '#ffffff',
              margin: [0, 0, 0, 12]
            },
            // Executive Summary & Recruiter Comments
            {
              columns: [
                { stack: [ { text: 'EXECUTIVE SUMMARY', fontSize: 8, bold: true, color: '#1d4ed8', margin: [0, 0, 0, 4] }, { text: data.careerSummary, fontSize: 7.5, lineHeight: 1.35 } ], margin: [0, 0, 6, 0] },
                { stack: [ { text: 'RECRUITER COMMENTS', fontSize: 8, bold: true, color: '#1d4ed8', margin: [0, 0, 0, 4] }, { text: data.recruiterNote, fontSize: 7.5, lineHeight: 1.35 } ], margin: [6, 0, 0, 0] }
              ],
              margin: [0, 0, 0, 12]
            },
            // Footer
            {
              table: { widths: ['*', 'auto'], body: [[
                { text: 'This report is generated by AI hiring platform jero.works', style: 'footer', border: [false, true, false, false], margin: [0, 6, 0, 0] },
                { text: 'www.jero.works', style: 'footer', border: [false, true, false, false], margin: [0, 6, 0, 0] }
              ]]},
              layout: { hLineColor: function(){ return '#e5e7eb'; }, hLineWidth: function(){ return 0.6; }, vLineWidth: function(){ return 0; } }
            }
          ]
        };
        window.pdfMake.createPdf(docDef).getBlob(function(b){ resolve(b); });
      } catch(e){ reject(e); }
    });
  }

  // Vector Company Branding Header Injection (PDFLib)
  async function applyCompanyBrandingToPdf(resumePdfBlob, brandingData){
    if(window.opener && typeof window.opener.__applyAgyCompanyBrandingToPdfBlob === 'function'){
      return window.opener.__applyAgyCompanyBrandingToPdfBlob(resumePdfBlob, brandingData);
    }

    var PDFLib = window.PDFLib;
    var resumeBytes = await resumePdfBlob.arrayBuffer();
    var resumeDoc = await PDFLib.PDFDocument.load(resumeBytes);
    var firstPage = resumeDoc.getPages()[0];
    var pageSize = firstPage.getSize();

    var targetDoc = await PDFLib.PDFDocument.create();
    var regularFont = await targetDoc.embedFont(PDFLib.StandardFonts.Helvetica);
    var boldFont = await targetDoc.embedFont(PDFLib.StandardFonts.HelveticaBold);

    var headerH = Math.max(68, pageSize.width * 0.11);
    var newFirst = targetDoc.addPage([pageSize.width, pageSize.height + headerH]);
    var embedded = await targetDoc.embedPage(firstPage);
    newFirst.drawPage(embedded, { x: 0, y: 0, width: pageSize.width, height: pageSize.height });

    var topY = pageSize.height;
    newFirst.drawRectangle({
      x: 0, y: topY, width: pageSize.width, height: headerH,
      color: PDFLib.rgb(1, 1, 1),
      borderColor: PDFLib.rgb(0.86, 0.89, 0.94),
      borderWidth: 0.8
    });
    newFirst.drawRectangle({
      x: 0, y: topY + headerH - 4, width: pageSize.width, height: 4,
      color: PDFLib.rgb(0.12, 0.23, 0.54)
    });

    newFirst.drawText('SHARED BY', { x: 24, y: topY + headerH - 24, size: 7, font: boldFont, color: PDFLib.rgb(0.39, 0.45, 0.55) });
    newFirst.drawText(brandingData.reportCompanyName || 'Hiring Partner', { x: 24, y: topY + headerH - 44, size: 12, font: boldFont, color: PDFLib.rgb(0.06, 0.09, 0.16) });
    newFirst.drawText('CONFIDENTIAL RESUME', { x: pageSize.width - 170, y: topY + headerH - 26, size: 12, font: boldFont, color: PDFLib.rgb(0.06, 0.09, 0.16) });

    // Copy remaining pages
    var sourcePages = resumeDoc.getPages();
    if(sourcePages.length > 1){
      var rest = await targetDoc.copyPages(resumeDoc, resumeDoc.getPageIndices().slice(1));
      rest.forEach(function(p){ targetDoc.addPage(p); });
    }

    var bytes = await targetDoc.save();
    return new Blob([bytes], { type: 'application/pdf' });
  }

  // Vector Keyword Highlighting (PDFLib + PDF.js text runs)
  async function applyVectorKeywordsHighlight(resumePdfBlob, keywordsList){
    if(window.opener && typeof window.opener.__buildTurboVectorHighlightedResume === 'function'){
      return window.opener.__buildTurboVectorHighlightedResume({
        resumeBlob: resumePdfBlob,
        keywords: keywordsList,
        returnBlob: true,
        ignoreBusy: true
      });
    }

    var PDFLib = window.PDFLib;
    var bytes = await resumePdfBlob.arrayBuffer();
    var pdfDoc = await PDFLib.PDFDocument.load(bytes);
    var pdfjsDoc = await window.pdfjsLib.getDocument({ data: new Uint8Array(bytes) }).promise;

    for(var p = 1; p <= pdfjsDoc.numPages; p++){
      var page = await pdfjsDoc.getPage(p);
      var tc = await page.getTextContent();
      var pdfLibPage = pdfDoc.getPage(p - 1);

      tc.items.forEach(function(item){
        var str = (item.str || '').toLowerCase();
        if(!str.trim()) return;

        keywordsList.forEach(function(kw){
          kw = String(kw).toLowerCase().trim();
          if(!kw || kw.length < 2) return;
          if(str.indexOf(kw) !== -1){
            var tx = item.transform[4] || 0;
            var ty = item.transform[5] || 0;
            var itemH = Math.max(Math.abs(item.transform[3] || item.height || 10), 9);
            var itemW = Math.max(Math.abs(item.width || 20), 10);

            pdfLibPage.drawRectangle({
              x: tx - 2,
              y: ty - (itemH * 0.2),
              width: itemW + 4,
              height: itemH * 1.15,
              color: PDFLib.rgb(1, 0.85, 0.15),
              opacity: 0.42
            });
          }
        });
      });
    }

    var savedBytes = await pdfDoc.save();
    return new Blob([savedBytes], { type: 'application/pdf' });
  }

  // Merges Pre-Screen Report and Resume (PDFLib)
  async function mergeReportAndResume(reportBlob, resumeBlob){
    if(window.opener && typeof window.opener.mergePreScreenedReportAndResume === 'function'){
      var PDFLib = window.PDFLib;
      var rBytes = await reportBlob.arrayBuffer();
      var cvBytes = await resumeBlob.arrayBuffer();
      var merged = await PDFLib.PDFDocument.create();
      var doc1 = await PDFLib.PDFDocument.load(rBytes);
      var doc2 = await PDFLib.PDFDocument.load(cvBytes);

      var p1 = await merged.copyPages(doc1, doc1.getPageIndices());
      p1.forEach(function(page){ merged.addPage(page); });

      var p2 = await merged.copyPages(doc2, doc2.getPageIndices());
      p2.forEach(function(page){ merged.addPage(page); });

      var b = await merged.save();
      return new Blob([b], { type: 'application/pdf' });
    }

    var PDFLib = window.PDFLib;
    var merged = await PDFLib.PDFDocument.create();
    var doc1 = await PDFLib.PDFDocument.load(await reportBlob.arrayBuffer());
    var doc2 = await PDFLib.PDFDocument.load(await resumeBlob.arrayBuffer());

    var p1 = await merged.copyPages(doc1, doc1.getPageIndices());
    p1.forEach(function(p){ merged.addPage(p); });
    var p2 = await merged.copyPages(doc2, doc2.getPageIndices());
    p2.forEach(function(p){ merged.addPage(p); });

    var res = await merged.save();
    return new Blob([res], { type: 'application/pdf' });
  }

  // -------------------------------------------------------------
  // PRIMARY PIPELINE: Generates the Selected Option's Final Base File
  // -------------------------------------------------------------
  async function buildOptionDocument(modeNum){
    modeNum = parseInt(modeNum, 10) || 61;
    var rawBytes = await fetchRawResumeBytes();

    var baseResumeBlob = null;
    // Check if DOCX
    if(rawBytes[0] === 0x50 && rawBytes[1] === 0x4B && resumeExt === 'docx'){
      if(window.AgyWasmEngine && typeof window.AgyWasmEngine.renderDocxToPdfBlob === 'function'){
        baseResumeBlob = await window.AgyWasmEngine.renderDocxToPdfBlob(rawBytes);
      } else if(window.opener && typeof window.opener.__renderAgyStrategy2WasmDocxToPdfBlob === 'function'){
        baseResumeBlob = await window.opener.__renderAgyStrategy2WasmDocxToPdfBlob(rawBytes);
      } else {
        baseResumeBlob = new Blob([rawBytes], { type: 'application/pdf' });
      }
    } else {
      baseResumeBlob = new Blob([rawBytes], { type: 'application/pdf' });
    }

    var finalBlob = baseResumeBlob;

    if(modeNum === 62){
      // Option 62 (Group 3: Resume + Company Branding)
      finalBlob = await applyCompanyBrandingToPdf(baseResumeBlob, candidateDossierData);
    } else if(modeNum === 63){
      // Option 63 (Group 5: Highlighted Keywords)
      var activeKws = Object.keys(keywords).filter(function(k){ return keywords[k].active; });
      finalBlob = await applyVectorKeywordsHighlight(baseResumeBlob, activeKws);
    } else if(modeNum === 64){
      // Option 64 (Group 4: Pre-Screen Report + Resume)
      var reportBlob = await buildRecruiterSummaryVectorBlob(candidateDossierData);
      finalBlob = await mergeReportAndResume(reportBlob, baseResumeBlob);
    } else if(modeNum === 65){
      // Option 65 (Group 5: Pre-Screen Report + Highlighted Keywords)
      var reportBlob = await buildRecruiterSummaryVectorBlob(candidateDossierData);
      var activeKws = Object.keys(keywords).filter(function(k){ return keywords[k].active; });
      var hlResumeBlob = await applyVectorKeywordsHighlight(baseResumeBlob, activeKws);
      finalBlob = await mergeReportAndResume(reportBlob, hlResumeBlob);
    }

    return finalBlob;
  }

  // -------------------------------------------------------------
  // HIGH-FIDELITY VIEWER RENDERER (Render PDF Pages into Canvas Viewport)
  // -------------------------------------------------------------
  async function renderDocumentPages(pdfBlob){
    loaderBox.style.display = 'flex';
    docPagesContainer.style.display = 'none';
    docPagesContainer.innerHTML = '';
    pageElements = [];

    var ab = await pdfBlob.arrayBuffer();
    currentPdfJsDoc = await window.pdfjsLib.getDocument({ data: new Uint8Array(ab) }).promise;

    var containerW = Math.min(canvasViewport.clientWidth - 40, 840);
    document.getElementById('stripPageNum').textContent = '1 / ' + currentPdfJsDoc.numPages;

    for(var p = 1; p <= currentPdfJsDoc.numPages; p++){
      var page = await currentPdfJsDoc.getPage(p);
      var baseVp = page.getViewport({ scale: 1 });
      var scale = (containerW - 4) / baseVp.width;
      var viewport = page.getViewport({ scale: scale * currentZoom });

      var pageWrap = document.createElement('div');
      pageWrap.className = 'rcv-page-wrap';
      pageWrap.id = 'page_' + p;
      pageWrap.setAttribute('data-page', p);
      pageWrap.style.width = Math.floor(viewport.width) + 'px';
      pageWrap.style.height = Math.floor(viewport.height) + 'px';
      pageWrap.style.margin = '0 auto';
      pageWrap.__pdfPage = page;
      pageWrap.__viewport = viewport;

      // Base Render Canvas
      var canvas = document.createElement('canvas');
      canvas.className = 'rcv-page-canvas';
      var dpr = window.devicePixelRatio || 1;
      canvas.width = Math.floor(viewport.width * dpr);
      canvas.height = Math.floor(viewport.height * dpr);
      canvas.style.width = Math.floor(viewport.width) + 'px';
      canvas.style.height = Math.floor(viewport.height) + 'px';
      pageWrap.appendChild(canvas);

      // Text Layer
      var tl = document.createElement('div');
      tl.className = 'rcv-textlayer';
      tl.style.width = Math.floor(viewport.width) + 'px';
      tl.style.height = Math.floor(viewport.height) + 'px';
      pageWrap.appendChild(tl);

      // Annotation Canvas Overlay (Touch / Stylus / Redaction drawing)
      var annotCanvas = document.createElement('canvas');
      annotCanvas.className = 'rcv-annotation-canvas';
      annotCanvas.width = canvas.width;
      annotCanvas.height = canvas.height;
      annotCanvas.style.width = canvas.style.width;
      annotCanvas.style.height = canvas.style.height;
      pageWrap.appendChild(annotCanvas);

      docPagesContainer.appendChild(pageWrap);
      pageElements.push(pageWrap);

      var ctx = canvas.getContext('2d');
      var transform = (dpr !== 1) ? [dpr, 0, 0, dpr, 0, 0] : null;
      await page.render({ canvasContext: ctx, viewport: viewport, transform: transform }).promise;

      // Render Text Layer
      var tc = await page.getTextContent();
      try {
        await window.pdfjsLib.renderTextLayer({
          textContent: tc,
          container: tl,
          viewport: viewport,
          textDivs: []
        }).promise;
      } catch(e){}

      bindPageEvents(pageWrap, annotCanvas, p, viewport);
    }

    loaderBox.style.display = 'none';
    docPagesContainer.style.display = 'flex';
    countOccurrences();
    renderChips();
    recordHistoryState();
  }

  // -------------------------------------------------------------
  // INTERACTIVE TOOLS: Highlighting, Whiteout, Signatures
  // -------------------------------------------------------------
  var isAnnotatingMode = false;
  var isDrawingStroke = false;
  var currentStroke = [];

  function bindPageEvents(pageWrap, annotCanvas, pageNum, viewport){
    var ctx = annotCanvas.getContext('2d');
    var dpr = window.devicePixelRatio || 1;

    // Pointer drawing for annotations
    annotCanvas.addEventListener('pointerdown', function(e){
      if(!isAnnotatingMode) return;
      e.preventDefault();
      isDrawingStroke = true;
      currentStroke = [{ x: e.offsetX * dpr, y: e.offsetY * dpr }];
    });

    annotCanvas.addEventListener('pointermove', function(e){
      if(!isDrawingStroke || !isAnnotatingMode) return;
      e.preventDefault();
      currentStroke.push({ x: e.offsetX * dpr, y: e.offsetY * dpr });

      ctx.beginPath();
      ctx.strokeStyle = '#0284c7';
      ctx.lineWidth = 2.5 * dpr;
      ctx.lineCap = 'round';
      ctx.lineJoin = 'round';
      ctx.moveTo(currentStroke[currentStroke.length - 2].x, currentStroke[currentStroke.length - 2].y);
      ctx.lineTo(currentStroke[currentStroke.length - 1].x, currentStroke[currentStroke.length - 1].y);
      ctx.stroke();
    });

    window.addEventListener('pointerup', function(){
      if(isDrawingStroke && currentStroke.length > 1){
        isDrawingStroke = false;
        // Save as stamp for vector burning
        var imgUrl = annotCanvas.toDataURL('image/png');
        userAnnotations.stamps.push({
          pageNum: pageNum,
          x: 0,
          y: 0,
          width: viewport.width,
          height: viewport.height,
          imgDataUrl: imgUrl
        });
        recordHistoryState();
      }
      isDrawingStroke = false;
    });
  }

  // Text selection highlight tool
  document.getElementById('btnToolHighlight').onclick = function(e){
    e.preventDefault();
    applySelectionAnnotation('highlight');
  };

  // Text selection whiteout tool
  document.getElementById('btnToolWhiteout').onclick = function(e){
    e.preventDefault();
    applySelectionAnnotation('whiteout');
  };

  function applySelectionAnnotation(type){
    var sel = window.getSelection();
    if(!sel || !sel.rangeCount || sel.isCollapsed) return;

    var range = sel.getRangeAt(0);
    var rects = range.getClientRects();
    if(!rects || !rects.length) return;

    for(var r = 0; r < rects.length; r++){
      var rect = rects[r];
      // Find which page container this rect belongs to
      for(var p = 0; p < pageElements.length; p++){
        var pageWrap = pageElements[p];
        var pRect = pageWrap.getBoundingClientRect();
        if(rect.top >= pRect.top && rect.bottom <= pRect.bottom && rect.left >= pRect.left && rect.right <= pRect.right){
          var pageNum = p + 1;
          var x = rect.left - pRect.left;
          var y = rect.top - pRect.top;
          var w = rect.width;
          var h = rect.height;

          var mark = document.createElement('div');
          mark.style.position = 'absolute';
          mark.style.left = x + 'px';
          mark.style.top = y + 'px';
          mark.style.width = w + 'px';
          mark.style.height = h + 'px';
          mark.style.pointerEvents = 'none';
          mark.style.zIndex = '4';

          if(type === 'highlight'){
            mark.style.backgroundColor = 'rgba(254, 240, 138, 0.55)';
            mark.style.borderRadius = '2px';
            pageWrap.appendChild(mark);

            userAnnotations.highlights.push({
              pageNum: pageNum,
              x: x,
              y: y,
              width: w,
              height: h,
              viewportW: pageWrap.__viewport.width,
              viewportH: pageWrap.__viewport.height
            });
          } else {
            mark.style.backgroundColor = '#ffffff';
            mark.style.border = '1px solid #e2e8f0';
            pageWrap.appendChild(mark);

            userAnnotations.whiteouts.push({
              pageNum: pageNum,
              x: x,
              y: y,
              width: w,
              height: h,
              viewportW: pageWrap.__viewport.width,
              viewportH: pageWrap.__viewport.height
            });
          }
          break;
        }
      }
    }

    sel.removeAllRanges();
    recordHistoryState();
  }

  // Toggle Keyword Highlights from Left Chip Bank
  function toggleKeywordHighlightOnDoc(word, active){
    word = word.toLowerCase();
    document.querySelectorAll('.rcv-textlayer span').forEach(function(sp){
      var t = sp.textContent || '';
      if(t.toLowerCase().indexOf(word) !== -1){
        if(active){
          sp.style.backgroundColor = 'rgba(254, 240, 138, 0.5)';
        } else {
          sp.style.backgroundColor = 'transparent';
        }
      }
    });
  }

  // Touch / Stylus Annotator Toggle
  var btnToolDraw = document.getElementById('btnToolDraw');
  btnToolDraw.onclick = function(){
    isAnnotatingMode = !isAnnotatingMode;
    btnToolDraw.classList.toggle('btn-tool-active', isAnnotatingMode);
    document.querySelectorAll('.rcv-annotation-canvas').forEach(function(ac){
      ac.style.pointerEvents = isAnnotatingMode ? 'auto' : 'none';
    });
  };

  // Signature Pad Modal
  var signModal = document.getElementById('signModal');
  var signCanvas = document.getElementById('signCanvas');
  var signCtx = signCanvas.getContext('2d');
  var isSigning = false;

  document.getElementById('btnToolSign').onclick = function(){
    signModal.style.display = 'flex';
    signCtx.clearRect(0, 0, signCanvas.width, signCanvas.height);
    signCtx.lineWidth = 2.5;
    signCtx.lineCap = 'round';
    signCtx.strokeStyle = '#0f172a';
  };
  signCanvas.addEventListener('mousedown', function(e){ isSigning = true; signCtx.beginPath(); signCtx.moveTo(e.offsetX, e.offsetY); });
  signCanvas.addEventListener('mousemove', function(e){ if(isSigning){ signCtx.lineTo(e.offsetX, e.offsetY); signCtx.stroke(); } });
  window.addEventListener('mouseup', function(){ isSigning = false; });
  document.getElementById('btnSignClear').onclick = function(){ signCtx.clearRect(0, 0, signCanvas.width, signCanvas.height); };
  document.getElementById('btnSignClose').onclick = function(){ signModal.style.display = 'none'; };

  document.getElementById('btnSignInsert').onclick = function(){
    var dataUrl = signCanvas.toDataURL('image/png');
    signModal.style.display = 'none';

    // Place signature stamp onto current active page
    var targetPage = pageElements[0] || docPagesContainer.firstElementChild;
    if(!targetPage) return;

    var pNum = parseInt(targetPage.getAttribute('data-page'), 10) || 1;
    var img = document.createElement('img');
    img.src = dataUrl;
    img.style.position = 'absolute';
    img.style.bottom = '80px';
    img.style.right = '60px';
    img.style.width = '180px';
    img.style.zIndex = '10';
    targetPage.appendChild(img);

    userAnnotations.stamps.push({
      pageNum: pNum,
      x: targetPage.clientWidth - 240,
      y: targetPage.clientHeight - 140,
      width: 180,
      height: 70,
      imgDataUrl: dataUrl,
      viewportW: targetPage.__viewport.width,
      viewportH: targetPage.__viewport.height
    });
    recordHistoryState();
  };

  // Image Upload
  document.getElementById('btnToolImage').onclick = function(){
    document.getElementById('fileImageInput').click();
  };
  document.getElementById('fileImageInput').addEventListener('change', function(e){
    var file = e.target.files && e.target.files[0];
    if(!file) return;
    var reader = new FileReader();
    reader.onload = function(evt){
      var targetPage = pageElements[0] || docPagesContainer.firstElementChild;
      if(!targetPage) return;
      var pNum = parseInt(targetPage.getAttribute('data-page'), 10) || 1;

      var img = document.createElement('img');
      img.src = evt.target.result;
      img.style.position = 'absolute';
      img.style.top = '30px';
      img.style.right = '50px';
      img.style.maxHeight = '50px';
      img.style.zIndex = '10';
      targetPage.appendChild(img);

      userAnnotations.stamps.push({
        pageNum: pNum,
        x: targetPage.clientWidth - 160,
        y: 30,
        width: 120,
        height: 50,
        imgDataUrl: evt.target.result,
        viewportW: targetPage.__viewport.width,
        viewportH: targetPage.__viewport.height
      });
      recordHistoryState();
    };
    reader.readAsDataURL(file);
  });

  // Undo / Redo Buttons
  document.getElementById('btnToolUndo').onclick = function(e){ e.preventDefault(); doUndo(); };
  document.getElementById('btnToolRedo').onclick = function(e){ e.preventDefault(); doRedo(); };

  // Add Custom Keyword
  document.getElementById('btnKwAdd').onclick = function(){
    var input = document.getElementById('kwInput');
    var val = (input.value || '').trim();
    if(!val) return;
    keywords[val.toLowerCase()] = { name: val, active: true, count: 0 };
    input.value = '';
    renderChips();
    countOccurrences();
    toggleKeywordHighlightOnDoc(val, true);
  };

  // -------------------------------------------------------------
  // VECTOR PDF EXPORT & DOWNLOAD (0% Print Dialog, 100% Vector WASM)
  // -------------------------------------------------------------
  document.getElementById('btnDownload').onclick = async function(){
    var btn = document.getElementById('btnDownload');
    var orig = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<span class="spin"></span> Compiling Vector PDF…';

    try {
      if(!currentGeneratedPdfBlob) throw new Error('Document is still loading.');

      var PDFLib = window.PDFLib;
      var srcBytes = await currentGeneratedPdfBlob.arrayBuffer();
      var pdfDoc = await PDFLib.PDFDocument.load(srcBytes);

      // Burn User Highlights
      for(var i = 0; i < userAnnotations.highlights.length; i++){
        var hl = userAnnotations.highlights[i];
        var page = pdfDoc.getPage(hl.pageNum - 1);
        var size = page.getSize();
        var scaleX = size.width / (hl.viewportW || size.width);
        var scaleY = size.height / (hl.viewportH || size.height);

        page.drawRectangle({
          x: hl.x * scaleX,
          y: size.height - ((hl.y + hl.height) * scaleY),
          width: hl.width * scaleX,
          height: hl.height * scaleY,
          color: PDFLib.rgb(1, 0.85, 0.15),
          opacity: 0.42
        });
      }

      // Burn User Whiteouts
      for(var w = 0; w < userAnnotations.whiteouts.length; w++){
        var wo = userAnnotations.whiteouts[w];
        var page = pdfDoc.getPage(wo.pageNum - 1);
        var size = page.getSize();
        var scaleX = size.width / (wo.viewportW || size.width);
        var scaleY = size.height / (wo.viewportH || size.height);

        page.drawRectangle({
          x: wo.x * scaleX,
          y: size.height - ((wo.y + wo.height) * scaleY),
          width: wo.width * scaleX,
          height: wo.height * scaleY,
          color: PDFLib.rgb(1, 1, 1),
          opacity: 1
        });
      }

      // Burn Stamps / Signatures / Logos
      for(var s = 0; s < userAnnotations.stamps.length; s++){
        var st = userAnnotations.stamps[s];
        var page = pdfDoc.getPage(st.pageNum - 1);
        var size = page.getSize();
        var scaleX = size.width / (st.viewportW || size.width);
        var scaleY = size.height / (st.viewportH || size.height);

        try {
          var imgBytes = await (await fetch(st.imgDataUrl)).arrayBuffer();
          var embeddedImg = (st.imgDataUrl.indexOf('jpeg') !== -1 || st.imgDataUrl.indexOf('jpg') !== -1)
            ? await pdfDoc.embedJpg(imgBytes)
            : await pdfDoc.embedPng(imgBytes);

          page.drawImage(embeddedImg, {
            x: st.x * scaleX,
            y: size.height - ((st.y + st.height) * scaleY),
            width: st.width * scaleX,
            height: st.height * scaleY
          });
        } catch(err){
          console.warn('Stamp embed skipped:', err);
        }
      }

      var finalBytes = await pdfDoc.save();
      var finalBlob = new Blob([finalBytes], { type: 'application/pdf' });

      // Clean direct download via hidden link
      var cleanName = (candName || 'Candidate').replace(/[^a-zA-Z0-9_\-]/g, '_');
      var m = parseInt(modePicker.value, 10) || 61;
      var sfx = (m === 64 || m === 65) ? '_Dossier' : (m === 62 ? '_Branded' : '_ATS_Optimized');
      var filename = 'Jeroworks_' + cleanName + sfx + '.pdf';

      var link = document.createElement('a');
      link.href = URL.createObjectURL(finalBlob);
      link.download = filename;
      document.body.appendChild(link);
      link.click();
      setTimeout(function(){
        document.body.removeChild(link);
        URL.revokeObjectURL(link.href);
      }, 5000);

    } catch(err){
      console.error('Vector PDF download error:', err);
      alert('Could not compile final PDF: ' + (err && err.message ? err.message : 'Please try again.'));
    } finally {
      btn.disabled = false;
      btn.innerHTML = orig;
    }
  };

  // Zoom controls
  document.getElementById('stripZoomIn').onclick = function(){
    currentZoom = Math.min(1.4, currentZoom + 0.1);
    if(currentGeneratedPdfBlob) renderDocumentPages(currentGeneratedPdfBlob);
  };
  document.getElementById('stripZoomOut').onclick = function(){
    currentZoom = Math.max(0.7, currentZoom - 0.1);
    if(currentGeneratedPdfBlob) renderDocumentPages(currentGeneratedPdfBlob);
  };
  document.getElementById('stripFitWidth').onclick = function(){
    currentZoom = 1.0;
    if(currentGeneratedPdfBlob) renderDocumentPages(currentGeneratedPdfBlob);
  };
  document.getElementById('stripPrevPage').onclick = function(){
    canvasViewport.scrollTo({ top: 0, behavior: 'smooth' });
  };
  document.getElementById('stripNextPage').onclick = function(){
    canvasViewport.scrollTo({ top: canvasViewport.scrollHeight, behavior: 'smooth' });
  };

  // Mode change: Regenerate document for the selected option
  modePicker.addEventListener('change', async function(){
    var m = parseInt(this.value, 10) || 61;
    try {
      loaderBox.style.display = 'flex';
      docPagesContainer.style.display = 'none';
      currentGeneratedPdfBlob = await buildOptionDocument(m);
      await renderDocumentPages(currentGeneratedPdfBlob);
    } catch(e){
      console.error('Mode switch error:', e);
      alert('Could not generate document format: ' + (e && e.message ? e.message : 'Please try again.'));
    }
  });

  // Initial Load
  async function initStudio(){
    try {
      if(initialOpt && modePicker) modePicker.value = String(initialOpt);
      currentGeneratedPdfBlob = await buildOptionDocument(initialOpt);
      await renderDocumentPages(currentGeneratedPdfBlob);
    } catch(err){
      console.error('Init error:', err);
      loaderBox.innerHTML = '<span style="color:#f87171;font-size:14px;font-weight:600;">Failed to load candidate document.</span><span style="font-size:12px;color:#cbd5e1;">' + (err.message || 'Please check candidate resume in database.') + '</span>';
    }
  }
  initStudio();
</script>

</body>
</html>