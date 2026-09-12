<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>{{ ucwords(@$application->full_name ?: @$application->name ?: 'Candidate') }} — Online ATS Resume Studio & Document Editor | JERO.works</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
  <script src="https://cdnjs.cloudflare.com/ajax/libs/mammoth/1.5.0/mammoth.browser.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.11.338/pdf.min.js"></script>
  <script src="/js/pdfmake.min.js"></script>
  <script src="/js/vfs_fonts.min.js"></script>
  <script src="/js/pdf-lib.min.js"></script>
  <script src="/js/opendoc/AgyCvOptimizer.js"></script>
  <style>
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body { font-family: 'Poppins', -apple-system, BlinkMacSystemFont, sans-serif; background: #f8fafc; color: #1e293b; height: 100vh; display: flex; flex-direction: column; overflow: hidden; }

    /* Top Navigation Header */
    .studio-header { height: 56px; background: #ffffff; border-bottom: 1px solid #e2e8f0; display: flex; align-items: center; justify-content: space-between; padding: 0 20px; z-index: 100; flex-shrink: 0; box-shadow: 0 1px 3px rgba(0,0,0,0.03); }
    .header-left { display: flex; align-items: center; gap: 12px; }
    .brand-mark { width: 32px; height: 32px; border-radius: 8px; background: linear-gradient(135deg, #0d9488 0%, #0284c7 100%); display: flex; align-items: center; justify-content: center; color: #fff; font-weight: 800; font-size: 15px; box-shadow: 0 2px 6px rgba(13,148,136,.25); }
    .brand-title { font-size: 14px; font-weight: 700; color: #0f172a; letter-spacing: -0.2px; }
    .brand-title span { color: #0d9488; }
    .v-divider { width: 1px; height: 22px; background: #e2e8f0; margin: 0 4px; }
    .pill-candidate { background: #ccfbf1; color: #0f766e; font-size: 11px; font-weight: 600; padding: 3px 10px; border-radius: 999px; border: 1px solid #99f6e4; max-width: 180px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .pill-role { color: #64748b; font-size: 11.5px; font-weight: 500; max-width: 220px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }

    .header-center { display: flex; align-items: center; gap: 8px; }
    .mode-select { padding: 6px 12px; font-size: 12px; font-family: 'Poppins', sans-serif; font-weight: 600; color: #0f172a; background: #f8fafc; border: 1.5px solid #cbd5e1; border-radius: 7px; outline: none; cursor: pointer; transition: all .15s; }
    .mode-select:focus { border-color: #0d9488; background: #fff; box-shadow: 0 0 0 3px rgba(13,148,136,.12); }

    .header-right { display: flex; align-items: center; gap: 10px; }
    .counter-badge { font-size: 11px; color: #64748b; font-weight: 500; }
    .btn-header { display: inline-flex; align-items: center; gap: 6px; padding: 7px 14px; border-radius: 6px; font-size: 12px; font-weight: 600; cursor: pointer; transition: all .15s; font-family: 'Poppins', sans-serif; }
    .btn-copy { background: #f1f5f9; color: #334155; border: 1px solid #cbd5e1; }
    .btn-copy:hover { background: #e2e8f0; }
    .btn-download { background: #0d9488; color: #ffffff; border: none; box-shadow: 0 2px 6px rgba(13,148,136,.25); }
    .btn-download:hover { background: #0f766e; box-shadow: 0 4px 10px rgba(13,148,136,.35); }
    .btn-download:disabled { opacity: 0.65; cursor: not-allowed; }

    /* Top Keyword Highlights Card (Direct Match to User Screenshot) */
    .top-panel { background: #ffffff; border-bottom: 1px solid #e2e8f0; padding: 14px 24px; box-shadow: 0 1px 4px rgba(0,0,0,0.02); flex-shrink: 0; }
    .top-panel-head { display: flex; align-items: center; justify-content: space-between; gap: 16px; margin-bottom: 12px; }
    .panel-titles h2 { font-size: 14px; font-weight: 700; color: #0f172a; margin: 0; }
    .panel-titles p { font-size: 11.5px; color: #64748b; margin: 2px 0 0; }
    .panel-stats { display: flex; align-items: center; gap: 8px; }
    .stat-box { min-width: 84px; background: #ffffff; border: 1.5px solid #e2e8f0; border-radius: 8px; padding: 6px 12px; text-align: center; }
    .stat-val { display: block; font-size: 15px; font-weight: 700; color: #0f172a; line-height: 1.2; }
    .stat-lbl { font-size: 9.5px; color: #94a3b8; text-transform: uppercase; letter-spacing: .4px; font-weight: 600; white-space: nowrap; }
    .stat-box.ats-box { border-color: #bbf7d0; background: #f0fdf4; }
    .stat-box.ats-box .stat-val { color: #15803d; }
    .stat-box.ats-box .stat-lbl { color: #15803d; }

    .search-row { display: flex; gap: 10px; margin-bottom: 10px; }
    .search-input { flex: 1; padding: 8px 14px; border: 1.5px solid #e2e8f0; border-radius: 8px; font-size: 12px; outline: none; font-family: 'Poppins', sans-serif; transition: border-color .15s; }
    .search-input:focus { border-color: #6366f1; }
    .btn-add-check { padding: 8px 18px; border: none; background: #6366f1; color: #fff; border-radius: 8px; font-size: 12px; font-weight: 600; cursor: pointer; white-space: nowrap; font-family: 'Poppins', sans-serif; transition: background .15s; }
    .btn-add-check:hover { background: #4f46e5; }

    .skills-row { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; }
    .skills-label { font-size: 10.5px; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: .5px; flex-shrink: 0; }
    .chips-container { display: flex; flex-wrap: wrap; gap: 6px; }
    .kw-chip { display: inline-flex; align-items: center; gap: 6px; padding: 3px 10px; border-radius: 999px; font-size: 11px; font-weight: 600; cursor: pointer; border: 1.5px solid #fef08a; background: #fef9c3; color: #854d0e; transition: all .12s; user-select: none; }
    .kw-chip:hover { background: #fef08a; transform: translateY(-1px); }
    .kw-chip .chip-count { font-size: 10px; font-weight: 700; background: #fef08a; color: #713f12; border-radius: 999px; padding: 1px 6px; }
    .kw-chip.missing { background: #f1f5f9; border-color: #e2e8f0; color: #64748b; }
    .kw-chip.missing .chip-count { background: #e2e8f0; color: #64748b; }

    /* Rich Document Editing Toolbar */
    .editor-toolbar { background: #ffffff; border-bottom: 1px solid #e2e8f0; padding: 7px 20px; display: flex; align-items: center; justify-content: space-between; gap: 12px; flex-wrap: wrap; z-index: 50; flex-shrink: 0; }
    .toolbar-left { display: flex; align-items: center; gap: 12px; flex-wrap: wrap; }
    .file-pill { display: flex; align-items: center; gap: 6px; font-size: 12px; font-weight: 600; color: #334155; max-width: 240px; }
    .file-pill svg { width: 16px; height: 16px; color: #6366f1; flex-shrink: 0; }
    .file-pill span { white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }

    .hit-navigator { display: flex; align-items: center; gap: 4px; font-size: 11px; font-weight: 600; color: #64748b; }
    .nav-btn { width: 26px; height: 26px; border: 1px solid #e2e8f0; background: #fff; border-radius: 6px; display: inline-flex; align-items: center; justify-content: center; color: #475569; cursor: pointer; }
    .nav-btn:hover { background: #f1f5f9; color: #6366f1; }

    .toolbar-tools { display: flex; align-items: center; gap: 4px; flex-wrap: wrap; }
    .tool-btn { height: 28px; padding: 0 8px; border: 1px solid #e2e8f0; background: #ffffff; border-radius: 5px; font-size: 11.5px; font-weight: 600; color: #475569; cursor: pointer; display: inline-flex; align-items: center; gap: 4px; font-family: 'Poppins', sans-serif; transition: all .12s; }
    .tool-btn:hover { background: #f8fafc; border-color: #cbd5e1; color: #0f172a; }
    .tool-btn.btn-active { background: #e0f2fe; border-color: #7dd3fc; color: #0369a1; }
    .tool-select { height: 28px; padding: 0 6px; border: 1px solid #e2e8f0; border-radius: 5px; font-size: 11px; font-family: 'Poppins', sans-serif; font-weight: 500; color: #334155; background: #fff; outline: none; cursor: pointer; }
    .color-picker { width: 28px; height: 28px; border: 1px solid #e2e8f0; border-radius: 5px; padding: 2px; cursor: pointer; background: #fff; }

    /* Full Page Viewer Area */
    .document-viewport { flex: 1; background: #525659; overflow-y: auto; padding: 28px 16px 60px; display: flex; flex-direction: column; align-items: center; gap: 24px; position: relative; }

    /* Document Sheet (Matching Resume / CV tab .rcv-html) */
    .document-sheet {
      background: #ffffff;
      width: 100%;
      max-width: 820px;
      margin: 0 auto;
      padding: 48px 56px;
      color: #1f2937;
      font-size: 13px;
      line-height: 1.65;
      box-shadow: 0 4px 16px rgba(0,0,0,.35);
      word-wrap: break-word;
      min-height: 1080px;
      border-radius: 2px;
      outline: none;
      position: relative;
      transition: box-shadow 0.15s;
    }
    .document-sheet:focus { box-shadow: 0 6px 22px rgba(0,0,0,.45), 0 0 0 2px rgba(13,148,136,.35); }
    .document-sheet * { max-width: 100%; font-size: inherit; }
    .document-sheet p { margin: 0 0 8px; }
    .document-sheet h1, .document-sheet h2, .document-sheet h3 { font-size: 14px; font-weight: 700; margin: 14px 0 6px; color: #0f172a; }
    .document-sheet ul, .document-sheet ol { margin: 0 0 8px 20px; }
    .document-sheet table { border-collapse: collapse; width: 100%; margin: 8px 0; }
    .document-sheet td, .document-sheet th { border: 1px solid #e2e8f0; padding: 5px 8px; }
    .document-sheet img { height: auto; max-width: 100%; }
    .document-sheet mark.rcv-hit { background: rgba(251,191,36,.55); color: inherit; border-radius: 2px; padding: 0 2px; }
    .document-sheet mark.rcv-hit-active { background: rgba(249,115,22,.65); box-shadow: 0 0 0 2px rgba(249,115,22,.4); }
    .document-sheet mark.rcv-redact { background: #111827; color: transparent !important; border-radius: 2px; user-select: none; }

    /* Pre-Screened Report Page Mockup */
    .report-sheet {
      background: #ffffff;
      width: 100%;
      max-width: 820px;
      margin: 0 auto;
      padding: 44px 50px;
      box-shadow: 0 4px 16px rgba(0,0,0,.35);
      border-radius: 2px;
      min-height: 1080px;
      position: relative;
    }
    .report-sheet-head { display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid #be185d; padding-bottom: 12px; margin-bottom: 20px; }
    .report-title-box { display: flex; align-items: center; gap: 10px; }
    .report-tag { background: #be185d; color: #fff; font-size: 10px; font-weight: 700; padding: 3px 8px; border-radius: 4px; }
    .report-title { font-size: 16px; font-weight: 700; color: #9d174d; margin: 0; }
    .report-badge { font-size: 11px; font-weight: 700; color: #be185d; background: #fce7f3; padding: 3px 10px; border-radius: 999px; }

    .report-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; margin-bottom: 20px; }
    .report-item { background: #fdf2f8; border: 1px solid #fbcfe8; border-radius: 8px; padding: 10px 14px; }
    .report-item-label { font-size: 10px; font-weight: 600; color: #9d174d; text-transform: uppercase; margin-bottom: 2px; }
    .report-item-val { font-size: 12.5px; font-weight: 700; color: #1e293b; }

    .report-divider-banner { text-align: center; font-size: 10.5px; color: #9d174d; font-weight: 600; padding: 10px; border: 1px dashed #f9a8d4; border-radius: 6px; background: #fff1f2; margin-top: 30px; }

    /* Company Branding Header Block */
    .brand-header-box { background: #f0fdfa; border: 1.5px solid #99f6e4; border-radius: 8px; padding: 12px 18px; margin-bottom: 22px; display: flex; align-items: center; justify-content: space-between; user-select: none; }
    .brand-header-left { display: flex; align-items: center; gap: 14px; }
    .brand-header-logo { height: 36px; max-width: 140px; object-fit: contain; }
    .brand-header-name { font-size: 13.5px; font-weight: 700; color: #0f766e; }
    .brand-header-sub { font-size: 10.5px; color: #64748b; }
    .brand-header-badge { font-size: 10px; font-weight: 600; background: #ccfbf1; color: #0f766e; padding: 2px 8px; border-radius: 999px; }

    /* Page Break Line */
    .rcv-page-break { page-break-after: always; border-top: 2px dashed #cbd5e1; margin: 24px 0; text-align: center; position: relative; }
    .rcv-page-break::after { content: 'PAGE BREAK'; position: absolute; top: -10px; left: 50%; transform: translateX(-50%); background: #ffffff; padding: 0 10px; font-size: 9px; font-weight: 700; color: #94a3b8; letter-spacing: 1px; }

    /* Loading Spinner */
    .spin { display: inline-block; width: 13px; height: 13px; border: 2px solid #fff; border-top-color: transparent; border-radius: 50%; animation: spin .6s linear infinite; }
    .spin-teal { border-color: #0d9488; border-top-color: transparent; }
    @keyframes spin { to { transform: rotate(360deg); } }

    /* Signature Modal */
    .sign-modal-backdrop { position: fixed; inset: 0; background: rgba(0,0,0,0.5); display: none; align-items: center; justify-content: center; z-index: 1000; }
    .sign-modal-box { background: #fff; border-radius: 12px; width: 440px; max-width: 90%; padding: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.25); }
    .sign-modal-title { font-size: 14px; font-weight: 700; color: #0f172a; margin-bottom: 12px; display: flex; justify-content: space-between; }
    .sign-canvas { border: 1.5px dashed #cbd5e1; border-radius: 8px; background: #fafbfc; width: 100%; height: 160px; cursor: crosshair; }
    .sign-actions { display: flex; justify-content: flex-end; gap: 8px; margin-top: 14px; }
  </style>
</head>
<body>

  <!-- Top Navigation Header -->
  <header class="studio-header">
    <div class="header-left">
      <div class="brand-mark">J</div>
      <div class="brand-title">JERO<span>.works</span> ATS Studio</div>
      <div class="v-divider"></div>
      <span class="pill-candidate" id="topCandName">{{ ucwords(@$application->full_name ?: @$application->name ?: 'Candidate') }}</span>
      <span class="pill-role" title="{{ $job->title ?? ($application->job_title ?? 'Verified Profile') }}">{{ $job->title ?? ($application->job_title ?? 'Verified Profile') }}</span>
    </div>

    <div class="header-center">
      <label style="font-size:11.5px;font-weight:600;color:#475569;display:flex;align-items:center;gap:6px;">
        <span>Format / Mode:</span>
        <select id="modeSelector" class="mode-select">
          <option value="61" {{ $optId == 61 ? 'selected' : '' }}>61. Edit &amp; Optimize Resume (Clean Vector PDF)</option>
          <option value="62" {{ $optId == 62 ? 'selected' : '' }}>62. Edit &amp; Optimize Resume + Company Branding</option>
          <option value="63" {{ $optId == 63 ? 'selected' : '' }}>63. Edit &amp; Optimize Resume + Highlighted Keywords</option>
          <option value="64" {{ $optId == 64 ? 'selected' : '' }}>64. Edit &amp; Optimize Resume + Pre-Screened Report</option>
          <option value="65" {{ $optId == 65 ? 'selected' : '' }}>65. Edit &amp; Optimize Resume + Pre-Screened Report + Highlighted Keywords</option>
        </select>
      </label>
    </div>

    <div class="header-right">
      <span id="counterStats" class="counter-badge">0 words • 0 chars</span>
      <button type="button" id="btnCopyText" class="btn-header btn-copy">📋 Copy Text</button>
      <button type="button" id="btnDownload" class="btn-header btn-download">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:14px;height:14px;"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
        <span id="btnDlText">Download Resume (PDF)</span>
      </button>
    </div>
  </header>

  <!-- Top Keyword Highlights Card (Matches User Screenshot) -->
  <div class="top-panel">
    <div class="top-panel-head">
      <div class="panel-titles">
        <h2>Keyword Highlights</h2>
        <p>Job skills matched against this résumé • Real-time ATS optimization</p>
      </div>
      <div class="panel-stats">
        <div class="stat-box"><span class="stat-val" id="statMatched">0</span><span class="stat-lbl">Matched</span></div>
        <div class="stat-box"><span class="stat-val" id="statMissing">0</span><span class="stat-lbl">Missing</span></div>
        <div class="stat-box"><span class="stat-val" id="statHighlights">0</span><span class="stat-lbl">Highlights</span></div>
        <div class="stat-box ats-box"><span class="stat-val" id="statScore">--%</span><span class="stat-lbl">ATS Score</span></div>
      </div>
    </div>
    <div class="search-row">
      <input type="text" id="kwInput" class="search-input" placeholder="Add a keyword to check &amp; highlight…" autocomplete="off">
      <button type="button" id="kwAddBtn" class="btn-add-check">Add &amp; Check</button>
    </div>
    <div class="skills-row">
      <span class="skills-label">Job Key Skills</span>
      <div class="chips-container" id="chipsContainer"></div>
    </div>
  </div>

  <!-- Rich Document Editing Toolbar -->
  <div class="editor-toolbar">
    <div class="toolbar-left">
      <div class="file-pill">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
        <span title="{{ basename($application->resume) }}">{{ basename($application->resume) }}</span>
      </div>
      <div class="hit-navigator">
        <button type="button" id="navPrevHit" class="nav-btn" title="Previous match"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:13px;height:13px;"><polyline points="18 15 12 9 6 15"/></svg></button>
        <span id="hitNavCount" style="padding:0 4px;">0/0</span>
        <button type="button" id="navNextHit" class="nav-btn" title="Next match"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:13px;height:13px;"><polyline points="6 9 12 15 18 9"/></svg></button>
      </div>
    </div>

    <div class="toolbar-tools">
      <!-- Formatting -->
      <button type="button" class="tool-btn" data-cmd="bold" title="Bold (Ctrl+B)"><strong>B</strong></button>
      <button type="button" class="tool-btn" data-cmd="italic" title="Italic (Ctrl+I)"><em>I</em></button>
      <button type="button" class="tool-btn" data-cmd="underline" title="Underline (Ctrl+U)"><u>U</u></button>
      <button type="button" class="tool-btn" data-cmd="strikeThrough" title="Strikethrough"><s>S</s></button>
      <select id="selFormatBlock" class="tool-select" title="Format Block">
        <option value="p">Paragraph</option>
        <option value="h1">Heading 1</option>
        <option value="h2">Heading 2</option>
        <option value="h3">Heading 3</option>
      </select>
      <select id="selFontSize" class="tool-select" title="Font Size">
        <option value="2">11px</option>
        <option value="3" selected>13px</option>
        <option value="4">15px</option>
        <option value="5">18px</option>
      </select>
      <input type="color" id="pickerColor" class="color-picker" value="#1e293b" title="Text Color">
      <button type="button" class="tool-btn" data-cmd="insertUnorderedList" title="Bullet List">• List</button>
      <button type="button" class="tool-btn" data-cmd="insertOrderedList" title="Numbered List">1. List</button>

      <div style="width:1px;height:20px;background:#e2e8f0;margin:0 2px;"></div>

      <!-- Advanced Document Tools -->
      <button type="button" id="toolHighlight" class="tool-btn" title="Highlight Selected Text" style="background:#fef9c3;color:#854d0e;border-color:#fef08a;">🖍 Highlight</button>
      <button type="button" id="toolWhiteout" class="tool-btn" title="Whiteout / Redact Selected Text" style="background:#f1f5f9;color:#0f172a;">⬛ Whiteout</button>
      <button type="button" id="toolInsertImage" class="tool-btn" title="Insert Image / Logo">🖼 Image</button>
      <button type="button" id="toolSign" class="tool-btn" title="Sign Document">✍ Sign</button>
      <button type="button" id="toolPageBreak" class="tool-btn" title="Insert Page Break">📄 Page</button>
      <button type="button" class="tool-btn" data-cmd="undo" title="Undo (Ctrl+Z)">↶</button>
      <button type="button" class="tool-btn" data-cmd="redo" title="Redo (Ctrl+Y)">↷</button>
      <input type="file" id="imageFileInput" accept="image/*" style="display:none;">
    </div>
  </div>

  <!-- Full-Page Document Viewport (#525659 dark background matching screenshot) -->
  <main class="document-viewport" id="viewport">

    <!-- Pre-Screened Report Page Mockup (Active for Options 64 & 65) -->
    <div class="report-sheet" id="reportPage" style="display:none;">
      <div class="report-sheet-head">
        <div class="report-title-box">
          <span class="report-tag">PAGE 1</span>
          <h1 class="report-title">Pre-Screened Candidate Evaluation Report</h1>
        </div>
        <span class="report-badge">Verified Candidate</span>
      </div>

      <div class="report-grid">
        <div class="report-item">
          <div class="report-item-label">Candidate Name</div>
          <div class="report-item-val">{{ ucwords(@$application->full_name ?: @$application->name ?: 'Candidate') }}</div>
        </div>
        <div class="report-item">
          <div class="report-item-label">Target Role</div>
          <div class="report-item-val">{{ $job->title ?? ($application->job_title ?? 'Verified Candidate') }}</div>
        </div>
        <div class="report-item">
          <div class="report-item-label">Total Experience</div>
          <div class="report-item-val">{{ @$application->total_exp ? (@$application->total_exp . ' Years') : 'Verified' }}</div>
        </div>
        <div class="report-item">
          <div class="report-item-label">Current Designation</div>
          <div class="report-item-val">{{ ucwords(@$application->current_designation ?: 'Specialist') }}</div>
        </div>
        <div class="report-item">
          <div class="report-item-label">Notice Period</div>
          <div class="report-item-val">{{ @$application->np_id ?: 'Immediate / Serving' }}</div>
        </div>
        <div class="report-item">
          <div class="report-item-label">Presented By</div>
          <div class="report-item-val">{{ ucwords(auth()->user()->name ?? 'JERO Recruiter') }}</div>
        </div>
      </div>

      <div style="background:#fdf2f8;border:1px solid #fbcfe8;border-radius:8px;padding:14px;margin-bottom:18px;">
        <div style="font-size:11px;font-weight:700;color:#9d174d;margin-bottom:6px;">VERIFIED CANDIDATE PROFILE SUMMARY</div>
        <div style="font-size:12px;color:#334155;line-height:1.6;">
          {{ \Illuminate\Support\Str::limit(strip_tags((string)(@$application->cover_letter ?: @$application->career_summary ?: @$application->comment ?? 'Prescreened candidate verified with industry experience.')), 450) }}
        </div>
      </div>

      <div class="report-divider-banner">
        ── End of Pre-Screened Report (Page 1) • Edited Live Résumé begins on Page 2 ──
      </div>
    </div>

    <!-- Candidate Résumé Document Sheet (ContentEditable Live Preview) -->
    <div class="document-sheet rcv-html" id="docSheet" contenteditable="true" spellcheck="false">
      <!-- Company Branding Header (Active for Option 62) -->
      <div class="brand-header-box" id="brandHeaderBox" style="display:none;" contenteditable="false">
        <div class="brand-header-left">
          @if(!empty($companyLogo))
            <img src="{{ $companyLogo }}" alt="Company Logo" class="brand-header-logo">
          @endif
          <div>
            <div class="brand-header-name">{{ strtoupper($companyName ?: 'CONFIDENTIAL CANDIDATE') }}</div>
            <div class="brand-header-sub">Represented Candidate • Verified ATS Optimized</div>
          </div>
        </div>
        <span class="brand-header-badge">Branded Profile</span>
      </div>

      <!-- Real Original Résumé Body -->
      <div id="resumeBody">
        <div id="loadingPlaceholder" style="display:flex;flex-direction:column;align-items:center;justify-content:center;padding:120px 20px;color:#64748b;gap:12px;" contenteditable="false">
          <div class="spin spin-teal" style="width:28px;height:28px;border-width:2.5px;"></div>
          <span style="font-size:13px;font-weight:600;color:#0f172a;">Loading original candidate résumé from Cloudflare R2…</span>
          <span style="font-size:11px;color:#64748b;">Rendering exact high-fidelity preview for live editing</span>
        </div>
      </div>
    </div>

  </main>

  <!-- Signature Modal -->
  <div class="sign-modal-backdrop" id="signModal">
    <div class="sign-modal-box">
      <div class="sign-modal-title">
        <span>Sign Document</span>
        <button type="button" id="btnSignClose" style="background:none;border:none;font-size:18px;cursor:pointer;color:#64748b;">&times;</button>
      </div>
      <p style="font-size:11.5px;color:#64748b;margin-bottom:8px;">Draw your signature below with your mouse or stylus:</p>
      <canvas id="signCanvas" class="sign-canvas" width="400" height="160"></canvas>
      <div class="sign-actions">
        <button type="button" id="btnSignClear" class="tool-btn" style="padding:6px 12px;">Clear</button>
        <button type="button" id="btnSignInsert" class="btn-add-check" style="padding:6px 14px;">Insert Signature</button>
      </div>
    </div>
  </div>

  <script>
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

    var docSheet = document.getElementById('docSheet');
    var resumeBody = document.getElementById('resumeBody');
    var modeSelector = document.getElementById('modeSelector');

    function esc(s){ return String(s||'').replace(/[&<>"]/g, function(c){ return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;'}[c]; }); }
    function escRe(s){ return String(s||'').replace(/[.*+?^${}()|[\]\\]/g, '\\$&'); }

    // Initialize Keywords
    jobSkills.forEach(function(sk){
      if(sk && sk.trim()) keywords[sk.trim()] = { type: 'job', active: true, count: 0 };
    });

    function countOccurrences(){
      var text = resumeBody.innerText || '';
      Object.keys(keywords).forEach(function(k){
        var re = new RegExp('\\b' + escRe(k) + '\\b', 'gi');
        var m = text.match(re);
        keywords[k].count = m ? m.length : 0;
      });
    }

    function renderChips(){
      var container = document.getElementById('chipsContainer');
      container.innerHTML = '';
      var matched = 0, missing = 0, totalHits = 0;
      Object.keys(keywords).forEach(function(k){
        var item = keywords[k];
        if(item.count > 0) { matched++; totalHits += item.count; } else { missing++; }
        var chip = document.createElement('span');
        chip.className = 'kw-chip' + (item.count > 0 ? '' : ' missing');
        chip.innerHTML = esc(k) + ' <span class="chip-count">' + item.count + '</span>';
        chip.onclick = function(){
          toggleKeywordHighlight(k);
        };
        container.appendChild(chip);
      });
      document.getElementById('statMatched').textContent = matched;
      document.getElementById('statMissing').textContent = missing;
      document.getElementById('statHighlights').textContent = totalHits;

      // Update words & chars
      var fullText = resumeBody.innerText || '';
      var words = fullText.trim() ? fullText.trim().split(/\s+/).length : 0;
      document.getElementById('counterStats').textContent = words + ' words • ' + fullText.length + ' chars';

      // Update ATS Score
      if(!optimizer && OptClass) optimizer = new OptClass({ jobSkills: jobSkills, candSkills: candSkills, candidateName: candName, targetJobTitle: jobTitle });
      if(optimizer){
        var res = optimizer.calculateAtsScore(fullText);
        var scoreBox = document.getElementById('statScore');
        scoreBox.textContent = res.score + '%';
        scoreBox.style.color = res.score >= 80 ? '#15803d' : (res.score >= 50 ? '#b45309' : '#b91c1c');
      }
    }

    function toggleKeywordHighlight(k){
      if(!keywords[k]) return;
      // Scroll to next match in document
      var activeMarks = Array.prototype.slice.call(resumeBody.querySelectorAll('mark.rcv-hit'));
      var match = activeMarks.filter(function(m){ return (m.innerText || '').toLowerCase() === k.toLowerCase(); })[0];
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
      var mode = parseInt(modeSelector.value, 10) || 61;
      var isHighlighted = (mode === 63 || mode === 65);

      // Clean existing marks
      var marks = Array.prototype.slice.call(resumeBody.querySelectorAll('mark.rcv-hit'));
      marks.forEach(function(m){
        var p = m.parentNode;
        while(m.firstChild) p.insertBefore(m.firstChild, m);
        p.removeChild(m);
      });

      if(!isHighlighted) {
        hits = [];
        document.getElementById('hitNavCount').textContent = '0/0';
        return;
      }

      var activeKeys = Object.keys(keywords).filter(function(k){ return keywords[k].active; });
      if(!activeKeys.length) return;
      var sorted = activeKeys.sort(function(a,b){ return b.length - a.length; });
      var pat = sorted.map(escRe).join('|');
      if(!pat) return;
      var re = new RegExp('\\b(' + pat + ')\\b', 'gi');
      highlightInNode(resumeBody, re, 'rcv-hit');

      hits = Array.prototype.slice.call(resumeBody.querySelectorAll('mark.rcv-hit'));
      document.getElementById('hitNavCount').textContent = hits.length ? ('1/' + hits.length) : '0/0';
      curHit = 0;
    }

    function gotoHit(dir){
      if(!hits.length) return;
      if(curHit >= 0 && hits[curHit]) hits[curHit].classList.remove('rcv-hit-active');
      curHit = (curHit + dir + hits.length) % hits.length;
      var target = hits[curHit];
      target.classList.add('rcv-hit-active');
      target.scrollIntoView({ behavior: 'smooth', block: 'center' });
      document.getElementById('hitNavCount').textContent = (curHit + 1) + '/' + hits.length;
    }

    function syncModeViews(){
      var mode = parseInt(modeSelector.value, 10) || 61;
      var isBranded = (mode === 62);
      var hasReport = (mode === 64 || mode === 65);

      document.getElementById('brandHeaderBox').style.display = isBranded ? 'flex' : 'none';
      document.getElementById('reportPage').style.display = hasReport ? 'block' : 'none';

      var btnLbl = document.getElementById('btnDlText');
      if(isBranded) btnLbl.textContent = 'Download Resume (Branded)';
      else if(hasReport && (mode === 65)) btnLbl.textContent = 'Download Report + Highlighted';
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

      // 3. Fetch from available resume sources if needed
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

      // 4. Parse bytes based on file extension
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

      // 5. Fallback if binary unavailable
      if(openerText){
        var fLines = openerText.split('\\n');
        var fHtml = fLines.map(function(l){ return l.trim() ? ('<p>' + esc(l.trim()) + '</p>') : '<br>'; }).join('');
        displayResume(fHtml);
      } else {
        displayResume('<p><strong>' + esc(candName) + '</strong></p><p>' + esc(jobTitle) + '</p><p>Candidate resume loaded for live editing.</p>');
      }
    }

    function displayResume(html){
      var placeholder = document.getElementById('loadingPlaceholder');
      if(placeholder) placeholder.remove();
      resumeBody.innerHTML = html;
      syncModeViews();
    }

    // Input & Add Keyword
    document.getElementById('kwAddBtn').onclick = function(){
      var inp = document.getElementById('kwInput');
      var val = (inp.value || '').trim();
      if(!val) return;
      keywords[val] = { type: 'custom', active: true, count: 0 };
      inp.value = '';
      countOccurrences();
      renderChips();
      applyHighlights();
    };
    document.getElementById('kwInput').addEventListener('keydown', function(e){
      if(e.key === 'Enter') { e.preventDefault(); document.getElementById('kwAddBtn').click(); }
    });

    // Match Navigation
    document.getElementById('navPrevHit').onclick = function(){ gotoHit(-1); };
    document.getElementById('navNextHit').onclick = function(){ gotoHit(1); };

    // Mode Selector change
    modeSelector.addEventListener('change', function(){
      syncModeViews();
    });

    // In-place editing sync
    var docInputDebounce = null;
    docSheet.addEventListener('input', function(){
      clearTimeout(docInputDebounce);
      docInputDebounce = setTimeout(function(){
        countOccurrences();
        renderChips();
      }, 150);
    });

    // Formatting Toolbar Buttons
    document.querySelectorAll('.tool-btn[data-cmd]').forEach(function(btn){
      btn.addEventListener('click', function(e){
        e.preventDefault();
        var cmd = this.getAttribute('data-cmd');
        document.execCommand(cmd, false, null);
        docSheet.focus();
      });
    });

    document.getElementById('selFormatBlock').addEventListener('change', function(){
      var val = this.value;
      document.execCommand('formatBlock', false, val);
      docSheet.focus();
    });

    document.getElementById('selFontSize').addEventListener('change', function(){
      var val = this.value;
      document.execCommand('fontSize', false, val);
      docSheet.focus();
    });

    document.getElementById('pickerColor').addEventListener('input', function(){
      document.execCommand('foreColor', false, this.value);
    });

    // Advanced Editing Tools
    document.getElementById('toolHighlight').onclick = function(){
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

    document.getElementById('toolWhiteout').onclick = function(){
      var sel = window.getSelection();
      if(!sel.rangeCount || sel.isCollapsed) return;
      var range = sel.getRangeAt(0);
      var mark = document.createElement('mark');
      mark.className = 'rcv-redact';
      mark.appendChild(range.extractContents());
      range.insertNode(mark);
    };

    document.getElementById('toolPageBreak').onclick = function(){
      var hr = document.createElement('div');
      hr.className = 'rcv-page-break';
      hr.contentEditable = 'false';
      var sel = window.getSelection();
      if(sel.rangeCount){
        var range = sel.getRangeAt(0);
        range.insertNode(hr);
      } else {
        resumeBody.appendChild(hr);
      }
    };

    // Image Insertion
    document.getElementById('toolInsertImage').onclick = function(){
      document.getElementById('imageFileInput').click();
    };
    document.getElementById('imageFileInput').addEventListener('change', function(e){
      var file = e.target.files && e.target.files[0];
      if(!file) return;
      var reader = new FileReader();
      reader.onload = function(evt){
        var img = document.createElement('img');
        img.src = evt.target.result;
        img.style.maxWidth = '220px';
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

    // Signature Modal & Canvas
    var signCanvas = document.getElementById('signCanvas');
    var signCtx = signCanvas.getContext('2d');
    var isDrawing = false;
    signCtx.lineWidth = 2.5;
    signCtx.strokeStyle = '#0f172a';
    signCtx.lineCap = 'round';

    function getCanvasPos(e){
      var r = signCanvas.getBoundingClientRect();
      var clientX = e.clientX || (e.touches && e.touches[0] && e.touches[0].clientX);
      var clientY = e.clientY || (e.touches && e.touches[0] && e.touches[0].clientY);
      return { x: clientX - r.left, y: clientY - r.top };
    }
    signCanvas.addEventListener('mousedown', function(e){ isDrawing = true; var pos = getCanvasPos(e); signCtx.beginPath(); signCtx.moveTo(pos.x, pos.y); });
    signCanvas.addEventListener('mousemove', function(e){ if(!isDrawing) return; var pos = getCanvasPos(e); signCtx.lineTo(pos.x, pos.y); signCtx.stroke(); });
    signCanvas.addEventListener('mouseup', function(){ isDrawing = false; });
    signCanvas.addEventListener('touchstart', function(e){ isDrawing = true; var pos = getCanvasPos(e); signCtx.beginPath(); signCtx.moveTo(pos.x, pos.y); e.preventDefault(); });
    signCanvas.addEventListener('touchmove', function(e){ if(!isDrawing) return; var pos = getCanvasPos(e); signCtx.lineTo(pos.x, pos.y); signCtx.stroke(); e.preventDefault(); });
    signCanvas.addEventListener('touchend', function(){ isDrawing = false; });

    document.getElementById('toolSign').onclick = function(){
      document.getElementById('signModal').style.display = 'flex';
    };
    document.getElementById('btnSignClose').onclick = function(){
      document.getElementById('signModal').style.display = 'none';
    };
    document.getElementById('btnSignClear').onclick = function(){
      signCtx.clearRect(0, 0, signCanvas.width, signCanvas.height);
    };
    document.getElementById('btnSignInsert').onclick = function(){
      var dataUri = signCanvas.toDataURL('image/png');
      var img = document.createElement('img');
      img.src = dataUri;
      img.style.height = '48px';
      img.style.display = 'block';
      img.style.margin = '10px 0';
      var sel = window.getSelection();
      if(sel.rangeCount){
        var range = sel.getRangeAt(0);
        range.insertNode(img);
      } else {
        resumeBody.appendChild(img);
      }
      document.getElementById('signModal').style.display = 'none';
      signCtx.clearRect(0, 0, signCanvas.width, signCanvas.height);
    };

    // Copy text
    document.getElementById('btnCopyText').onclick = function(){
      var text = resumeBody.innerText || '';
      navigator.clipboard.writeText(text).then(function(){
        alert('Resume text copied to clipboard!');
      }).catch(function(){ alert('Could not copy text.'); });
    };

    // Download Resume (PDF)
    document.getElementById('btnDownload').onclick = async function(){
      var btn = document.getElementById('btnDownload');
      var orig = btn.innerHTML;
      btn.disabled = true;
      btn.innerHTML = '<span class="spin"></span> Generating PDF…';
      try {
        var mode = parseInt(modeSelector.value, 10) || 61;
        var isBranded = (mode === 62);
        var isHighlighted = (mode === 63 || mode === 65);
        var hasReport = (mode === 64 || mode === 65);
        var resumeText = (resumeBody.innerText || '').trim();
        if(!resumeText) throw new Error('Resume content is empty.');

        if(!OptClass) OptClass = window.AgyCvOptimizer || (window.opener && window.opener.AgyCvOptimizer);
        if(!OptClass) throw new Error('Optimizer engine is initializing, please try again.');

        var optInstance = new OptClass({ jobSkills: jobSkills, candidateName: candName, targetJobTitle: jobTitle });
        var cleanCand = (candName || 'Candidate').replace(/[^a-zA-Z0-9_\\-]/g, '_');

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
          var sfx = isBranded ? '_Branded' : (isHighlighted ? '_Highlighted' : '');
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

    // Initial sync and load
    syncModeViews();
    loadOriginalResume();
  </script>
</body>
</html>