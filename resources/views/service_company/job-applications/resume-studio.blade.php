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
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

  <!-- Core Client-Side Engines -->
  <script src="{{ asset('js/mammoth.browser.min.js') }}" onerror="this.onerror=null;this.src='https://cdnjs.cloudflare.com/ajax/libs/mammoth/1.5.0/mammoth.browser.min.js'"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.11.338/pdf.min.js"></script>
  <script src="{{ asset('js/pdfmake.min.js') }}"></script>
  <script src="{{ asset('js/vfs_fonts.min.js') }}"></script>
  <script src="{{ asset('js/opendoc/AgyCvOptimizer.js') }}"></script>

  <style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
    html, body {
      height: 100%;
      overflow: hidden;
      font-family: 'Poppins', -apple-system, BlinkMacSystemFont, sans-serif;
      font-size: 13px;
      color: #1e293b;
      background: #f1f5f9;
      -webkit-font-smoothing: antialiased;
    }

    .acrobat-app {
      display: flex;
      flex-direction: column;
      height: 100vh;
      width: 100vw;
    }

    /* Top Enterprise Header */
    .acrobat-header {
      height: 50px;
      background: #ffffff;
      border-bottom: 1px solid #e2e8f0;
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 0 18px;
      flex-shrink: 0;
      z-index: 100;
      box-shadow: 0 1px 3px rgba(0,0,0,0.04);
    }
    .brand-section {
      display: flex;
      align-items: center;
      gap: 10px;
    }
    .brand-logo-wrap {
      display: flex;
      align-items: center;
      justify-content: center;
      height: 28px;
    }
    .brand-logo-img {
      height: 24px;
      width: auto;
      display: block;
    }
    .brand-title {
      font-size: 14.5px;
      font-weight: 700;
      color: #0f172a;
      letter-spacing: -0.2px;
    }
    .doc-status-tag {
      font-size: 11px;
      font-weight: 600;
      color: #0284c7;
      background: #e0f2fe;
      padding: 2px 10px;
      border-radius: 999px;
      margin-left: 6px;
    }

    .header-actions {
      display: flex;
      align-items: center;
      gap: 12px;
    }
    .mode-picker-select {
      height: 34px;
      padding: 0 12px;
      font-size: 12px;
      font-weight: 600;
      font-family: inherit;
      color: #0f172a;
      background: #f8fafc;
      border: 1.5px solid #cbd5e1;
      border-radius: 6px;
      cursor: pointer;
      outline: none;
      transition: all 0.15s ease;
    }
    .mode-picker-select:focus {
      border-color: #0284c7;
      box-shadow: 0 0 0 2px rgba(2,132,199,0.15);
    }
    .btn-header-dl {
      height: 34px;
      padding: 0 18px;
      background: #0284c7;
      color: #ffffff;
      font-size: 12.5px;
      font-weight: 600;
      font-family: inherit;
      border: none;
      border-radius: 6px;
      display: inline-flex;
      align-items: center;
      gap: 7px;
      cursor: pointer;
      transition: all 0.15s ease;
      box-shadow: 0 1px 3px rgba(2, 132, 199, 0.3);
    }
    .btn-header-dl:hover { background: #0369a1; }
    .btn-header-dl:disabled { opacity: 0.65; cursor: not-allowed; }

    /* Workspace */
    .acrobat-workspace {
      flex: 1;
      display: flex;
      overflow: hidden;
      position: relative;
    }

    /* ZONE A: Left Tools Sidebar */
    .acrobat-sidebar {
      width: 325px;
      background: #ffffff;
      border-right: 1px solid #e2e8f0;
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
      width: 52px;
      height: 52px;
      border-radius: 50%;
      background: conic-gradient(#0284c7 var(--score-pct, 88%), #e2e8f0 0);
      display: flex;
      align-items: center;
      justify-content: center;
      flex-shrink: 0;
      position: relative;
    }
    .ats-score-circle::before {
      content: '';
      position: absolute;
      width: 42px;
      height: 42px;
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
      max-height: 160px;
      overflow-y: auto;
      padding: 4px 1px;
    }
    .kw-chip {
      display: inline-flex;
      align-items: center;
      gap: 5px;
      font-size: 11px;
      font-weight: 600;
      padding: 4px 9px;
      border-radius: 999px;
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
    .kw-chip:hover { border-color: #94a3b8; }
    .kw-count {
      font-size: 9.5px;
      font-weight: 700;
      background: rgba(0,0,0,0.07);
      padding: 1px 5px;
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

    /* Rich Formatting Toolbar (Bold, Pointer, Lists, Colors) */
    .rich-format-panel {
      display: flex;
      flex-direction: column;
      gap: 8px;
      background: #f8fafc;
      border: 1px solid #e2e8f0;
      border-radius: 8px;
      padding: 10px;
    }
    .format-btn-row {
      display: flex;
      align-items: center;
      gap: 4px;
      width: 100%;
    }
    .btn-fmt {
      flex: 1;
      height: 30px;
      background: #ffffff;
      border: 1px solid #cbd5e1;
      border-radius: 5px;
      font-size: 12px;
      font-weight: 700;
      color: #334155;
      cursor: pointer;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      transition: all 0.1s ease;
    }
    .btn-fmt:hover {
      background: #e2e8f0;
      color: #0f172a;
    }
    .btn-fmt.active {
      background: #0284c7;
      color: #ffffff;
      border-color: #0284c7;
    }
    .select-fmt-size {
      height: 30px;
      padding: 0 6px;
      font-size: 11px;
      font-weight: 600;
      font-family: inherit;
      border: 1px solid #cbd5e1;
      border-radius: 5px;
      background: #ffffff;
      color: #334155;
      outline: none;
      cursor: pointer;
    }
    .input-fmt-color {
      width: 32px;
      height: 30px;
      border: 1px solid #cbd5e1;
      border-radius: 5px;
      padding: 0;
      cursor: pointer;
      background: #ffffff;
    }

    /* Action Tools List */
    .action-tools-list {
      display: flex;
      flex-direction: column;
      gap: 6px;
    }
    .btn-action-tool {
      display: flex;
      align-items: center;
      gap: 9px;
      width: 100%;
      padding: 8px 12px;
      font-size: 12px;
      font-weight: 600;
      font-family: inherit;
      color: #1e293b;
      background: #ffffff;
      border: 1px solid #e2e8f0;
      border-radius: 6px;
      cursor: pointer;
      text-align: left;
      transition: all 0.12s ease;
      user-select: none;
    }
    .btn-action-tool:hover {
      background: #f8fafc;
      border-color: #cbd5e1;
      color: #0f172a;
    }
    .btn-tool-highlight { background: #fef9c3; border-color: #fde047; color: #854d0e; }
    .btn-tool-highlight:hover { background: #fef08a; }
    .btn-tool-whiteout { background: #0f172a; border-color: #0f172a; color: #ffffff; }
    .btn-tool-whiteout:hover { background: #1e293b; color: #ffffff; }

    /* ZONE B: Center Document Viewport */
    .acrobat-canvas {
      flex: 1;
      background: #475569;
      overflow-y: auto;
      overflow-x: hidden;
      display: flex;
      flex-direction: column;
      align-items: center;
      padding: 24px 20px 80px 20px;
      gap: 26px;
      scroll-behavior: smooth;
    }

    /* White A4 Paper Sheets (Live Interactive WYSIWYG) */
    .paper-sheet {
      width: 820px;
      min-height: 1080px;
      background: #ffffff;
      box-shadow: 0 8px 30px rgba(0,0,0,0.35);
      border-radius: 3px;
      padding: 46px 54px;
      outline: none;
      position: relative;
      font-family: 'Poppins', -apple-system, BlinkMacSystemFont, sans-serif;
      font-size: 13px;
      line-height: 1.6;
      color: #1e293b;
      user-select: text;
      -webkit-user-select: text;
    }
    .paper-sheet:focus {
      box-shadow: 0 8px 32px rgba(0,0,0,0.4), 0 0 0 2px #0284c7;
    }

    /* Pre-Screened Candidate Report Card (Page 1 in Option 64 & 65) */
    .ps-report-container {
      border: 1px solid #e2e8f0;
      border-radius: 8px;
      padding: 24px;
      background: #ffffff;
      margin-bottom: 8px;
    }
    .ps-header {
      display: flex;
      align-items: flex-start;
      justify-content: space-between;
      border-bottom: 2px solid #0f172a;
      padding-bottom: 12px;
      margin-bottom: 16px;
    }
    .ps-shared-title {
      font-size: 9px;
      font-weight: 700;
      color: #64748b;
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }
    .ps-shared-name {
      font-size: 15px;
      font-weight: 800;
      color: #0f172a;
    }
    .ps-report-main-title {
      font-size: 17px;
      font-weight: 800;
      color: #0f172a;
      text-transform: uppercase;
      text-align: right;
    }
    .ps-report-sub-date {
      font-size: 10px;
      font-weight: 600;
      color: #64748b;
      text-transform: uppercase;
      text-align: right;
    }

    .ps-top-split {
      display: grid;
      grid-template-columns: 1.6fr 1fr;
      gap: 12px;
      margin-bottom: 14px;
    }
    .ps-card {
      background: #ffffff;
      border: 1.5px solid #e2e8f0;
      border-radius: 6px;
      padding: 14px 16px;
    }
    .ps-cand-name {
      font-size: 19px;
      font-weight: 800;
      color: #0f172a;
    }
    .ps-cand-role {
      font-size: 13px;
      font-weight: 700;
      color: #0284c7;
      margin-bottom: 6px;
    }
    .ps-cand-meta {
      font-size: 11px;
      color: #475569;
      font-weight: 600;
      margin-bottom: 2px;
    }

    .ps-confidence-card {
      text-align: center;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      background: #f8fbff;
      border: 1.5px solid #cfe0ff;
    }
    .ps-confidence-lbl {
      font-size: 11px;
      font-weight: 700;
      color: #64748b;
      text-transform: uppercase;
      margin-bottom: 2px;
    }
    .ps-confidence-val {
      font-size: 34px;
      font-weight: 800;
      color: #0284c7;
      line-height: 1;
    }

    .ps-metrics-4grid {
      display: grid;
      grid-template-columns: repeat(4, 1fr);
      gap: 8px;
      margin-bottom: 14px;
    }
    .ps-metric-item {
      background: #ffffff;
      border: 1.5px solid #e2e8f0;
      border-radius: 6px;
      padding: 10px 8px;
      text-align: center;
    }
    .ps-metric-head {
      font-size: 9.5px;
      font-weight: 700;
      color: #64748b;
      text-transform: uppercase;
      margin-bottom: 2px;
    }
    .ps-metric-val {
      font-size: 12px;
      font-weight: 700;
      color: #0f172a;
    }

    .ps-bottom-split {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 12px;
      margin-bottom: 14px;
    }
    .ps-summary-card {
      background: #ffffff;
      border: 1.5px solid #e2e8f0;
      border-radius: 6px;
      padding: 14px;
    }
    .ps-sec-head {
      font-size: 10.5px;
      font-weight: 700;
      color: #0284c7;
      text-transform: uppercase;
      margin-bottom: 6px;
    }
    .ps-sec-body {
      font-size: 11.5px;
      color: #334155;
      line-height: 1.5;
    }

    /* Company Branding Header (Option 62) */
    .branding-header-wrap {
      display: flex;
      align-items: center;
      justify-content: space-between;
      border-bottom: 2.5px solid #0284c7;
      padding-bottom: 10px;
      margin-bottom: 24px;
    }
    .branding-left {
      display: flex;
      align-items: center;
      gap: 12px;
    }
    .branding-company-title {
      font-size: 15px;
      font-weight: 800;
      color: #0f172a;
      text-transform: uppercase;
    }
    .branding-sub-tag {
      font-size: 10px;
      font-weight: 600;
      color: #0284c7;
    }

    /* Resume Typography inside Editable Sheet */
    .paper-sheet h1 {
      font-size: 18px;
      font-weight: 700;
      color: #0f172a;
      margin: 16px 0 8px 0;
      border-bottom: 1.5px solid #e2e8f0;
      padding-bottom: 4px;
    }
    .paper-sheet h2 {
      font-size: 15px;
      font-weight: 700;
      color: #0f172a;
      margin: 14px 0 6px 0;
    }
    .paper-sheet h3 {
      font-size: 13px;
      font-weight: 600;
      color: #0f172a;
      margin: 10px 0 4px 0;
    }
    .paper-sheet p {
      margin-bottom: 8px;
      line-height: 1.6;
    }
    .paper-sheet ul, .paper-sheet ol {
      margin-left: 24px;
      margin-bottom: 10px;
    }
    .paper-sheet li {
      margin-bottom: 4px;
    }
    .paper-sheet table {
      width: 100%;
      border-collapse: collapse;
      margin: 10px 0 14px 0;
      font-size: 12px;
    }
    .paper-sheet table td, .paper-sheet table th {
      border: 1px solid #cbd5e1;
      padding: 6px 10px;
      vertical-align: top;
    }

    /* Highlights & Redactions */
    mark.rcv-hit {
      background: #fef08a !important;
      color: #854d0e !important;
      padding: 1px 4px !important;
      border-radius: 3px !important;
      font-weight: 600 !important;
    }
    mark.rcv-hit.rcv-hit-active {
      background: #f97316 !important;
      color: #ffffff !important;
      box-shadow: 0 0 0 2px #f97316 !important;
    }
    mark.rcv-redact {
      background: #000000 !important;
      color: #000000 !important;
      padding: 1px 4px !important;
      border-radius: 2px !important;
      user-select: none !important;
    }

    .doc-page-break {
      border-top: 2px dashed #94a3b8;
      margin: 28px 0;
      position: relative;
      text-align: center;
      user-select: none;
    }
    .doc-page-break::after {
      content: "PAGE BREAK";
      font-size: 9px;
      font-weight: 700;
      color: #64748b;
      background: #ffffff;
      padding: 0 8px;
      position: absolute;
      top: -7px;
      left: 50%;
      transform: translateX(-50%);
      letter-spacing: 0.5px;
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
      width: 440px;
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
      height: 150px;
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
  </style>
</head>
<body>

<div class="acrobat-app">

  <!-- Top Header -->
  <header class="acrobat-header">
    <div class="brand-section">
      <div class="brand-logo-wrap">
        <img src="{{ asset('img/logo.svg') }}" alt="jero.works" class="brand-logo-img" onerror="this.style.display='none'">
      </div>
      <span class="brand-title">jero.works online editor</span>
      <span class="doc-status-tag">{{ ucwords(@$application->full_name ?: @$application->name ?: 'Candidate') }}</span>
    </div>

    <div class="header-actions">
      <!-- Mode Selection Dropdown -->
      <select id="modePicker" class="mode-picker-select" title="Switch Document Generation Format">
        <option value="65">Format: Pre-Screen Report + Highlighted Resume</option>
        <option value="64">Format: Pre-Screen Report + Resume</option>
        <option value="62">Format: Resume + Company Branding</option>
        <option value="61">Format: ATS Original Resume</option>
      </select>

      <button type="button" class="btn-header-dl" id="btnDownload">
        <i class="fa-solid fa-download"></i> Download Final Vector PDF
      </button>
    </div>
  </header>

  <!-- Workspace Container -->
  <div class="acrobat-workspace">

    <!-- ZONE A: Left Tools Sidebar -->
    <aside class="acrobat-sidebar">

      <!-- ATS Scorecard -->
      <div class="sidebar-section">
        <div class="sidebar-sec-head">
          <span class="sidebar-sec-title">Candidate ATS Score</span>
        </div>
        <div class="ats-score-card">
          <div class="ats-score-circle" id="atsScoreCircle" style="--score-pct: 88%;">
            <span class="ats-score-circle-text" id="atsScoreVal">88%</span>
          </div>
          <div class="ats-score-meta">
            <span class="ats-score-label" id="atsMatchLabel">ATS Match Confidence</span>
            <span class="ats-score-sub" id="atsMatchRatio">Calculating keyword presence…</span>
          </div>
        </div>
      </div>

      <!-- Skill Keywords Bank -->
      <div class="sidebar-section">
        <div class="sidebar-sec-head">
          <span class="sidebar-sec-title">Skill Keywords</span>
          <span id="kwCountBadge" style="font-size:10px;font-weight:700;color:#0284c7;background:#e0f2fe;padding:2px 6px;border-radius:4px;">0 active</span>
        </div>
        <div class="kw-chip-bank" id="kwBank">
          <!-- Dynamically populated chips -->
        </div>

        <div style="display:flex;gap:6px;margin-top:10px;">
          <input type="text" id="kwInput" placeholder="Add custom skill…" style="flex:1;height:28px;padding:0 8px;font-size:11.5px;font-family:inherit;border:1px solid #cbd5e1;border-radius:5px;outline:none;">
          <button type="button" id="btnKwAdd" class="btn-ats-add-kw">Add</button>
        </div>
      </div>

      <!-- Rich Formatting & Editing Tools (Bold, Pointer, Lists, Colors) -->
      <div class="sidebar-section">
        <div class="sidebar-sec-head">
          <span class="sidebar-sec-title">Text &amp; Formatting</span>
        </div>

        <div class="rich-format-panel">
          <!-- Bold, Italic, Underline, Pointer/Bullets, Numbered List -->
          <div class="format-btn-row">
            <button type="button" class="btn-fmt" id="btnBold" title="Bold Selected (Ctrl+B)"><b>B</b></button>
            <button type="button" class="btn-fmt" id="btnItalic" title="Italic Selected (Ctrl+I)"><i>I</i></button>
            <button type="button" class="btn-fmt" id="btnUnderline" title="Underline Selected (Ctrl+U)"><u>U</u></button>
            <button type="button" class="btn-fmt" id="btnBullet" title="Bullet Pointer List">• Pointer</button>
            <button type="button" class="btn-fmt" id="btnNumList" title="Numbered List">1. List</button>
          </div>

          <!-- Font Size & Color -->
          <div class="format-btn-row" style="margin-top:2px;">
            <select id="selFontSize" class="select-fmt-size" style="flex:1;">
              <option value="2">Size: Small (11px)</option>
              <option value="3" selected>Size: Normal (13px)</option>
              <option value="4">Size: Medium (15px)</option>
              <option value="5">Size: Large (18px)</option>
              <option value="6">Size: Heading (22px)</option>
            </select>
            <input type="color" id="pickerColor" class="input-fmt-color" value="#1e293b" title="Text Color">
          </div>
        </div>
      </div>

      <!-- Document Power Tools -->
      <div class="sidebar-section">
        <div class="sidebar-sec-head">
          <span class="sidebar-sec-title">Document Tools</span>
        </div>

        <div class="action-tools-list">
          <button type="button" id="btnToolHighlight" class="btn-action-tool btn-tool-highlight" title="Highlight selected text in yellow">
            <span>🖍</span> Highlight Selected Text
          </button>
          <button type="button" id="btnToolWhiteout" class="btn-action-tool btn-tool-whiteout" title="Whiteout / redact sensitive text">
            <span>⬛</span> Whiteout / Redact Selected
          </button>
          <button type="button" id="btnToolSign" class="btn-action-tool" title="Draw and place signature stamp">
            <span>🔏</span> Draw &amp; Insert Signature
          </button>
          <button type="button" id="btnToolImage" class="btn-action-tool" title="Insert company logo or image">
            <span>🖼</span> Insert Company Logo / Stamp
          </button>
          <button type="button" id="btnToolPageBreak" class="btn-action-tool" title="Insert page break divider">
            <span>📄</span> Insert Page Break
          </button>
          <button type="button" id="btnToolCopy" class="btn-action-tool" title="Copy clean resume text to clipboard">
            <span>📋</span> Copy All Text
          </button>

          <div style="display:flex;gap:6px;margin-top:4px;">
            <button type="button" id="btnToolUndo" class="btn-action-tool" style="flex:1;justify-content:center;">↶ Undo (Ctrl+Z)</button>
            <button type="button" id="btnToolRedo" class="btn-action-tool" style="flex:1;justify-content:center;">↷ Redo (Ctrl+Y)</button>
          </div>
          <input type="file" id="fileImageInput" accept="image/*" style="display:none;">
        </div>
      </div>

    </aside>

    <!-- ZONE B: Center Document Viewport (Interactive A4 Paper Sheets) -->
    <main class="acrobat-canvas" id="canvas">

      <!-- SHEET 1: Pre-Screened Candidate Report Card (Option 64 & 65) -->
      <div class="paper-sheet" id="sheetReport" style="display:block;" contenteditable="true">
        <div class="ps-header" contenteditable="false">
          <div class="ps-shared-by">
            @if(!empty($companyLogo ?? ''))
              <img src="{{ $companyLogo }}" alt="{{ $companyName ?? 'Hiring Partner' }}" style="height:32px;width:auto;margin-bottom:4px;display:block;" onerror="this.style.display='none'">
            @endif
            <div>
              <div class="ps-shared-title">SHARED BY</div>
              <div class="ps-shared-name">{{ $companyName ?? 'Hiring Partner' }}</div>
            </div>
          </div>
          <div class="ps-report-title-box">
            <div class="ps-report-main-title">PRE-SCREENED CANDIDATE REPORT</div>
            <div class="ps-report-sub-date">{{ now()->format('d M Y') }} · CONFIDENTIAL</div>
          </div>
        </div>

        <div class="ps-top-split">
          <div class="ps-card">
            <div class="ps-cand-name">{{ ucwords(@$application->full_name ?: @$application->name ?: 'Candidate') }}</div>
            <div class="ps-cand-role">{{ ucwords(@$job->title ?: @$application->job_title ?: 'Verified Professional') }}</div>
            <div class="ps-cand-meta">EXPERIENCE: {{ strtoupper($rsExperience ?? '5 YEARS') }}</div>
            <div class="ps-cand-meta">CURRENT COMPANY: {{ strtoupper($rsCurrentCompany ?? 'HIRING PARTNER') }}</div>
          </div>
          <div class="ps-card ps-confidence-card">
            <div class="ps-confidence-lbl">HIRING CONFIDENCE</div>
            <div class="ps-confidence-val">{{ $rsMatchPct ?? 88 }}%</div>
          </div>
        </div>

        <div class="ps-metrics-4grid">
          <div class="ps-metric-item">
            <div class="ps-metric-head">EDUCATION</div>
            <div class="ps-metric-val">{{ $rsEducation ?? 'Graduate' }}</div>
          </div>
          <div class="ps-metric-item">
            <div class="ps-metric-head">NOTICE PERIOD</div>
            <div class="ps-metric-val">{{ $noticePeriod ?? 'Immediate' }}</div>
          </div>
          <div class="ps-metric-item">
            <div class="ps-metric-head">LOCATION</div>
            <div class="ps-metric-val">{{ $cityName ?? 'Bangalore' }}</div>
          </div>
          <div class="ps-metric-item">
            <div class="ps-metric-head">WORK MODE</div>
            <div class="ps-metric-val">{{ $rsWorkMode ?? 'Hybrid' }}</div>
          </div>
        </div>

        <div class="ps-bottom-split">
          <div class="ps-summary-card">
            <div class="ps-sec-head">EXECUTIVE SUMMARY</div>
            <div class="ps-sec-body">{{ $rsCareerSummary ?? 'Demonstrated track record of technical delivery and architectural excellence.' }}</div>
          </div>
          <div class="ps-summary-card">
            <div class="ps-sec-head">RECRUITER COMMENTS</div>
            <div class="ps-sec-body">{{ $rsRecruiterNote ?? 'Candidate thoroughly pre-screened and assessed for technical competency and communication.' }}</div>
          </div>
        </div>

        <div style="font-size:9.5px;color:#94a3b8;text-align:center;margin-top:14px;border-top:1px solid #e2e8f0;padding-top:8px;" contenteditable="false">
          This report is generated by AI hiring platform jero.works · www.jero.works
        </div>
      </div>

      <!-- SHEET 2: Candidate Resume (Live Editable Content) -->
      <div class="paper-sheet" id="sheetResume" contenteditable="true">
        <!-- Optional Company Branding Banner (Option 62) -->
        <div class="branding-header-wrap" id="brandingBanner" style="display:none;" contenteditable="false">
          <div class="branding-left">
            @if(!empty($companyLogo ?? ''))
              <img src="{{ $companyLogo }}" alt="{{ $companyName ?? 'Hiring Partner' }}" style="height:32px;width:auto;" onerror="this.style.display='none'">
            @endif
            <div>
              <div style="font-size:8px;font-weight:700;color:#64748b;text-transform:uppercase;">SHARED BY</div>
              <div class="branding-company-title">{{ $companyName ?? 'Hiring Partner' }}</div>
            </div>
          </div>
          <div style="text-align:right;">
            <div style="font-size:12px;font-weight:800;color:#0f172a;">CONFIDENTIAL RESUME</div>
            <div class="branding-sub-tag">{{ now()->format('d M Y') }} · Verified ATS Format</div>
          </div>
        </div>

        <!-- Editable Resume Body -->
        <div id="resumeBody">
          <div id="loaderPlaceholder" style="display:flex;flex-direction:column;align-items:center;justify-content:center;padding:120px 20px;color:#64748b;gap:12px;" contenteditable="false">
            <div class="spin" style="width:32px;height:32px;border-color:#0284c7;border-top-color:transparent;border-width:3px;"></div>
            <span style="font-size:14px;font-weight:600;color:#0f172a;">Loading candidate document into online editor…</span>
            <span style="font-size:12px;color:#94a3b8;">Preserving authentic typography, formatting, and vector resolution</span>
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
    <canvas id="signCanvas" class="sign-canvas" width="400" height="150"></canvas>
    <div class="sign-box-actions">
      <button type="button" id="btnSignClear" class="btn-action-tool" style="width:auto;padding:6px 12px;">Clear</button>
      <button type="button" id="btnSignInsert" class="btn-ats-add-kw" style="padding:6px 14px;">Insert Signature</button>
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
  var rcvDownloadUrl = @json($rcvDownloadUrl ?? '');
  var rcvPublicUrl = @json($rcvPublicUrl ?? '');
  var r2SignUrl = @json($r2SignUrl ?? '');
  var initialOpt = @json($optId ?? 65);

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

  var sheetReport = document.getElementById('sheetReport');
  var sheetResume = document.getElementById('sheetResume');
  var resumeBody = document.getElementById('resumeBody');
  var brandingBanner = document.getElementById('brandingBanner');
  var modePicker = document.getElementById('modePicker');
  var kwBank = document.getElementById('kwBank');
  var canvas = document.getElementById('canvas');

  function esc(s){ return String(s||'').replace(/[&<>"]/g, function(c){ return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;'}[c]; }); }
  function escRe(s){ return String(s||'').replace(/[.*+?^${}()|[\]\\]/g, '\\$&'); }

  // -------------------------------------------------------------
  // ATS KEYWORD SCANNER & REAL-TIME MATCH CALCULATION
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
    var fullText = (resumeBody.innerText || '') + ' ' + (sheetReport.style.display !== 'none' ? sheetReport.innerText : '');
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
      pct = Math.max(70, Math.min(100, pct));
    }
    document.getElementById('atsScoreVal').textContent = pct + '%';
    document.getElementById('atsScoreCircle').style.setProperty('--score-pct', pct + '%');
    document.getElementById('atsMatchRatio').textContent = matched + ' of ' + totalActive + ' skills matched';
    document.getElementById('kwCountBadge').textContent = totalActive + ' active';
  }

  function renderChips(){
    kwBank.innerHTML = '';
    Object.keys(keywords).forEach(function(k){
      var kw = keywords[k];
      var chip = document.createElement('span');
      chip.className = 'kw-chip' + (kw.active ? ' active' : '');
      chip.innerHTML = esc(kw.name) + ' <span class="kw-count">' + kw.count + '</span>';
      chip.onclick = function(){
        kw.active = !kw.active;
        renderChips();
        applyKeywordHighlights();
      };
      kwBank.appendChild(chip);
    });
  }

  // Highlights active keywords in resume body without breaking DOM cursor
  function applyKeywordHighlights(){
    var mode = parseInt(modePicker.value, 10) || 65;
    var shouldHighlight = (mode === 63 || mode === 65);

    // Remove existing keyword marks
    var marks = Array.prototype.slice.call(sheetResume.querySelectorAll('mark.rcv-hit'));
    marks.forEach(function(m){
      var p = m.parentNode;
      while(m.firstChild) p.insertBefore(m.firstChild, m);
      p.removeChild(m);
    });

    if(!shouldHighlight) return;

    var activeWords = Object.keys(keywords).filter(function(k){ return keywords[k].active; });
    if(!activeWords.length) return;

    var sorted = activeWords.map(function(k){ return keywords[k].name; }).sort(function(a,b){ return b.length - a.length; });
    var pattern = sorted.map(escRe).join('|');
    if(!pattern) return;

    var regex = new RegExp('\\b(' + pattern + ')\\b', 'gi');

    function highlightNode(node){
      if(node.nodeType === 3){
        regex.lastIndex = 0;
        var m = regex.exec(node.nodeValue);
        if(!m) return;
        var mark = document.createElement('mark');
        mark.className = 'rcv-hit';
        var matched = node.splitText(m.index);
        matched.splitText(m[0].length);
        mark.appendChild(matched.cloneNode(true));
        matched.parentNode.replaceChild(mark, matched);
      } else if(node.nodeType === 1 && node.childNodes && !/(script|style|mark)/i.test(node.tagName)){
        for(var i = 0; i < node.childNodes.length; i++){
          highlightNode(node.childNodes[i]);
        }
      }
    }

    highlightNode(resumeBody);
  }

  // Mode change handler (61, 62, 64, 65)
  function syncModeViews(){
    var mode = parseInt(modePicker.value, 10) || 65;
    var isBranded = (mode === 62);
    var hasReport = (mode === 64 || mode === 65);

    if(brandingBanner) brandingBanner.style.display = isBranded ? 'flex' : 'none';
    if(sheetReport) sheetReport.style.display = hasReport ? 'block' : 'none';

    var counter = document.getElementById('stripPageNum');
    if(counter) counter.textContent = hasReport ? '1 / 2' : '1 / 1';

    countOccurrences();
    renderChips();
    applyKeywordHighlights();
  }

  modePicker.addEventListener('change', syncModeViews);

  // -------------------------------------------------------------
  // CANDIDATE RÉSUMÉ FETCH & HIGH-FIDELITY EXTRACTION
  // -------------------------------------------------------------
  async function loadCandidateResume(){
    // 1. Check parent window HTML / text cache
    if(window.opener && window.opener.__rcvResumeHtml && window.opener.__rcvResumeHtml.trim().length > 30){
      displayResume(window.opener.__rcvResumeHtml);
      return;
    }

    var bytes = null;
    if(window.opener && window.opener.__agyResumeCache){
      for(var k in window.opener.__agyResumeCache){
        var b = window.opener.__agyResumeCache[k];
        if(b && b.byteLength > 100){ bytes = new Uint8Array(b); break; }
      }
    }

    var urlsToTry = [rcvPublicUrl, rcvFetchUrl, rcvDownloadUrl].filter(Boolean);
    if(!bytes && urlsToTry.length){
      for(var i = 0; i < urlsToTry.length; i++){
        try {
          var isDirectR2 = (urlsToTry[i].indexOf('file.jero.works') !== -1 || urlsToTry[i].indexOf('r2.cloudflarestorage.com') !== -1);
          var resp = await fetch(urlsToTry[i], isDirectR2 ? { mode: 'cors', cache: 'no-store' } : { credentials: 'same-origin', cache: 'no-store' });
          if(resp.ok){
            var ab = await resp.arrayBuffer();
            if(ab && ab.byteLength > 100){ bytes = new Uint8Array(ab); break; }
          }
        } catch(e){}
      }
    }

    // Fallback: R2 signed download URL
    if(!bytes && r2SignUrl){
      try {
        var sRes = await fetch(r2SignUrl, { headers: { 'Accept': 'application/json' } });
        if(sRes.ok){
          var sData = await sRes.json();
          if(sData && sData.download_url){
            var sFetch = await fetch(sData.download_url);
            if(sFetch.ok){
              var sAb = await sFetch.arrayBuffer();
              if(sAb && sAb.byteLength > 100) bytes = new Uint8Array(sAb);
            }
          }
        }
      } catch(e){}
    }

    if(bytes){
      var isDocx = (bytes[0] === 0x50 && bytes[1] === 0x4B) || resumeExt === 'docx' || resumeExt === 'doc';
      if(isDocx){
        try {
          if(typeof mammoth !== 'undefined'){
            var conv = await mammoth.convertToHtml({ arrayBuffer: bytes.buffer || bytes });
            if(conv && conv.value && conv.value.trim().length > 20){
              displayResume(conv.value);
              return;
            }
          }
        } catch(err){
          console.warn('Mammoth docx parse error:', err);
        }
      }

      // PDF Resume Parser
      try {
        if(typeof window.pdfjsLib !== 'undefined'){
          var pVer = window.pdfjsLib.version || '2.11.338';
          window.pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/' + pVer + '/pdf.worker.min.js';
          var pdf = await window.pdfjsLib.getDocument({ data: bytes }).promise;
          var htmlBlocks = [];
          for(var p = 1; p <= pdf.numPages; p++){
            var page = await pdf.getPage(p);
            var tc = await page.getTextContent();
            var pageStr = tc.items.map(function(item){ return item.str || ''; }).join(' ');
            var sentences = pageStr.split(/(?<=[.?!])\s+/);
            sentences.forEach(function(s){ if(s.trim()) htmlBlocks.push('<p>' + esc(s.trim()) + '</p>'); });
            if(p < pdf.numPages) htmlBlocks.push('<div class="doc-page-break"></div>');
          }
          if(htmlBlocks.length){
            displayResume(htmlBlocks.join(''));
            return;
          }
        }
      } catch(pdfErr){
        console.warn('PDF.js text extraction error:', pdfErr);
      }
    }

    // Opener plain text fallback
    var openerText = window.opener && (window.opener.__rcvResumeText || window.opener.__rdcPreloadedResumeText);
    if(openerText){
      var lines = openerText.split(/\r?\n/);
      var textHtml = lines.map(function(l){ return l.trim() ? ('<p>' + esc(l.trim()) + '</p>') : '<br>'; }).join('');
      displayResume(textHtml);
    } else {
      displayResume('<p><strong>' + esc(candName) + '</strong></p><p>' + esc(jobTitle) + '</p><p>Verified candidate profile loaded for online editing.</p>');
    }
  }

  function displayResume(html){
    var loader = document.getElementById('loaderPlaceholder');
    if(loader) loader.remove();
    resumeBody.innerHTML = html;
    syncModeViews();
  }

  // -------------------------------------------------------------
  // LIVE RICH FORMATTING TOOLBAR: BOLD, ITALIC, POINTERS, LISTS
  // -------------------------------------------------------------
  document.getElementById('btnBold').onclick = function(e){
    e.preventDefault();
    document.execCommand('bold', false, null);
    resumeBody.focus();
  };
  document.getElementById('btnItalic').onclick = function(e){
    e.preventDefault();
    document.execCommand('italic', false, null);
    resumeBody.focus();
  };
  document.getElementById('btnUnderline').onclick = function(e){
    e.preventDefault();
    document.execCommand('underline', false, null);
    resumeBody.focus();
  };
  document.getElementById('btnBullet').onclick = function(e){
    e.preventDefault();
    document.execCommand('insertUnorderedList', false, null);
    resumeBody.focus();
  };
  document.getElementById('btnNumList').onclick = function(e){
    e.preventDefault();
    document.execCommand('insertOrderedList', false, null);
    resumeBody.focus();
  };
  document.getElementById('selFontSize').onchange = function(){
    document.execCommand('fontSize', false, this.value);
    resumeBody.focus();
  };
  document.getElementById('pickerColor').oninput = function(){
    document.execCommand('foreColor', false, this.value);
    resumeBody.focus();
  };

  // Highlight Selected Text
  document.getElementById('btnToolHighlight').onclick = function(e){
    e.preventDefault();
    var sel = window.getSelection();
    if(!sel.rangeCount || sel.isCollapsed){
      alert('Please select some text to highlight first.');
      return;
    }
    var range = sel.getRangeAt(0);
    var mark = document.createElement('mark');
    mark.className = 'rcv-hit';
    mark.appendChild(range.extractContents());
    range.insertNode(mark);
    sel.removeAllRanges();
    countOccurrences();
    renderChips();
  };

  // Whiteout Selected Text
  document.getElementById('btnToolWhiteout').onclick = function(e){
    e.preventDefault();
    var sel = window.getSelection();
    if(!sel.rangeCount || sel.isCollapsed){
      alert('Please select some text to redact/whiteout first.');
      return;
    }
    var range = sel.getRangeAt(0);
    var mark = document.createElement('mark');
    mark.className = 'rcv-redact';
    mark.appendChild(range.extractContents());
    range.insertNode(mark);
    sel.removeAllRanges();
  };

  // Page Break Tool
  document.getElementById('btnToolPageBreak').onclick = function(){
    var hr = document.createElement('div');
    hr.className = 'doc-page-break';
    hr.contentEditable = 'false';
    var sel = window.getSelection();
    if(sel.rangeCount && !sel.isCollapsed){
      var range = sel.getRangeAt(0);
      range.insertNode(hr);
    } else {
      resumeBody.appendChild(hr);
    }
  };

  // Copy All Text Tool
  document.getElementById('btnToolCopy').onclick = function(){
    var t = (sheetReport.style.display !== 'none' ? sheetReport.innerText + '\n\n' : '') + (resumeBody.innerText || '');
    navigator.clipboard.writeText(t).then(function(){
      alert('Document text copied to clipboard!');
    }).catch(function(){ alert('Could not copy text.'); });
  };

  // Undo / Redo
  document.getElementById('btnToolUndo').onclick = function(e){ e.preventDefault(); document.execCommand('undo', false, null); };
  document.getElementById('btnToolRedo').onclick = function(e){ e.preventDefault(); document.execCommand('redo', false, null); };

  // Add Custom Skill Keyword
  document.getElementById('btnKwAdd').onclick = function(){
    var input = document.getElementById('kwInput');
    var val = (input.value || '').trim();
    if(!val) return;
    keywords[val.toLowerCase()] = { name: val, active: true, count: 0 };
    input.value = '';
    countOccurrences();
    renderChips();
    applyKeywordHighlights();
  };
  document.getElementById('kwInput').addEventListener('keydown', function(e){
    if(e.key === 'Enter'){ e.preventDefault(); document.getElementById('btnKwAdd').click(); }
  });

  // Debounced input sync for live ATS score calculation
  var syncTimer = null;
  function handleLiveEdit(){
    clearTimeout(syncTimer);
    syncTimer = setTimeout(function(){
      countOccurrences();
      renderChips();
    }, 200);
  }
  sheetResume.addEventListener('input', handleLiveEdit);
  sheetReport.addEventListener('input', handleLiveEdit);

  // Signature Pad Modal
  var signModal = document.getElementById('signModal');
  var signCanvas = document.getElementById('signCanvas');
  var signCtx = signCanvas.getContext('2d');
  var isSigning = false;
  signCtx.lineWidth = 2.5;
  signCtx.strokeStyle = '#0f172a';
  signCtx.lineCap = 'round';

  document.getElementById('btnToolSign').onclick = function(){
    signModal.style.display = 'flex';
    signCtx.clearRect(0, 0, signCanvas.width, signCanvas.height);
  };
  signCanvas.addEventListener('mousedown', function(e){ isSigning = true; signCtx.beginPath(); signCtx.moveTo(e.offsetX, e.offsetY); });
  signCanvas.addEventListener('mousemove', function(e){ if(isSigning){ signCtx.lineTo(e.offsetX, e.offsetY); signCtx.stroke(); } });
  window.addEventListener('mouseup', function(){ isSigning = false; });
  document.getElementById('btnSignClear').onclick = function(){ signCtx.clearRect(0, 0, signCanvas.width, signCanvas.height); };
  document.getElementById('btnSignClose').onclick = function(){ signModal.style.display = 'none'; };

  document.getElementById('btnSignInsert').onclick = function(){
    var dataUrl = signCanvas.toDataURL('image/png');
    var img = document.createElement('img');
    img.src = dataUrl;
    img.style.maxHeight = '48px';
    img.style.display = 'block';
    img.style.margin = '10px 0';
    var sel = window.getSelection();
    if(sel.rangeCount && !sel.isCollapsed){
      var range = sel.getRangeAt(0);
      range.insertNode(img);
    } else {
      resumeBody.appendChild(img);
    }
    signModal.style.display = 'none';
  };

  // Image Upload Tool
  document.getElementById('btnToolImage').onclick = function(){ document.getElementById('fileImageInput').click(); };
  document.getElementById('fileImageInput').addEventListener('change', function(e){
    var file = e.target.files && e.target.files[0];
    if(!file) return;
    var reader = new FileReader();
    reader.onload = function(evt){
      var img = document.createElement('img');
      img.src = evt.target.result;
      img.style.maxWidth = '180px';
      img.style.margin = '10px 0';
      var sel = window.getSelection();
      if(sel.rangeCount && !sel.isCollapsed){
        var range = sel.getRangeAt(0);
        range.insertNode(img);
      } else {
        resumeBody.appendChild(img);
      }
    };
    reader.readAsDataURL(file);
  });

  // -------------------------------------------------------------
  // GOD-LEVEL VECTOR PDF COMPILER & INSTANT DOWNLOAD (0% Print Dialog)
  // -------------------------------------------------------------
  document.getElementById('btnDownload').onclick = async function(){
    var btn = document.getElementById('btnDownload');
    var orig = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<span class="spin"></span> Compiling Vector PDF…';

    try {
      if(!window.pdfMake || !window.pdfMake.createPdf){
        throw new Error('PDF compiler engine is initializing. Please wait a moment.');
      }

      var mode = parseInt(modePicker.value, 10) || 65;
      var isBranded = (mode === 62);
      var hasReport = (mode === 64 || mode === 65);
      var shouldHighlight = (mode === 63 || mode === 65);

      var fullPdfContent = [];

      // 1. PAGE 1: PRE-SCREENED CANDIDATE REPORT (If Option 64 or 65)
      if(hasReport){
        var score = candidateDossierData.match || 88;
        var reportTable = [
          // Header
          {
            table: { widths: ['*', 'auto'], body: [[
              { text: [ { text: 'SHARED BY\n', fontSize: 6, bold: true, color: '#64748b' }, { text: candidateDossierData.reportCompanyName || 'Hiring Partner', fontSize: 12, bold: true, color: '#0f172a' } ], border: [false, false, false, false] },
              { stack: [ { text: 'PRE-SCREENED CANDIDATE REPORT', fontSize: 14, bold: true, color: '#0f172a', alignment: 'right' }, { text: (candidateDossierData.reportDate + ' · CONFIDENTIAL').toUpperCase(), fontSize: 6.5, color: '#64748b', alignment: 'right', margin: [0, 3, 0, 0] } ], border: [false, false, false, false] }
            ]]},
            layout: 'noBorders',
            margin: [0, 0, 0, 14]
          },
          // Candidate & Hiring Confidence
          {
            table: { widths: ['*', 140], body: [[
              { stack: [
                  { text: candidateDossierData.name, fontSize: 17, bold: true, color: '#0f172a' },
                  { text: candidateDossierData.designation, fontSize: 9.5, bold: true, color: '#0284c7', margin: [0, 3, 0, 8] },
                  { text: 'EXPERIENCE: ' + (candidateDossierData.experience || '5 YEARS').toUpperCase(), fontSize: 7.5, bold: true, color: '#475569' },
                  { text: 'CURRENT COMPANY: ' + (candidateDossierData.company || '--').toUpperCase(), fontSize: 7.5, bold: true, color: '#475569', margin: [0, 2, 0, 0] }
                ], fillColor: '#ffffff', border: [true, true, true, true], borderColor: ['#cbd5e1','#cbd5e1','#cbd5e1','#cbd5e1'], margin: [10, 8, 10, 8] },
              { stack: [
                  { text: 'HIRING CONFIDENCE', fontSize: 10, bold: true, color: '#64748b', alignment: 'center', margin: [0, 6, 0, 0] },
                  { text: score + '%', fontSize: 26, bold: true, color: '#0284c7', alignment: 'center', margin: [0, 4, 0, 0] }
                ], fillColor: '#f8fbff', border: [true, true, true, true], borderColor: ['#cfe0ff','#cfe0ff','#cfe0ff','#cfe0ff'], margin: [10, 8, 10, 8] }
            ]]},
            margin: [0, 0, 0, 12]
          },
          // 4 Metrics
          {
            table: { widths: ['*', '*', '*', '*'], body: [[
              { stack: [ { text: 'EDUCATION', fontSize: 6.5, bold: true, color: '#64748b', alignment: 'center' }, { text: candidateDossierData.education, fontSize: 8, bold: true, alignment: 'center', margin: [0, 2, 0, 0] } ], margin: [4, 6, 4, 6] },
              { stack: [ { text: 'NOTICE PERIOD', fontSize: 6.5, bold: true, color: '#64748b', alignment: 'center' }, { text: candidateDossierData.notice, fontSize: 8, bold: true, alignment: 'center', margin: [0, 2, 0, 0] } ], margin: [4, 6, 4, 6] },
              { stack: [ { text: 'LOCATION', fontSize: 6.5, bold: true, color: '#64748b', alignment: 'center' }, { text: candidateDossierData.location, fontSize: 8, bold: true, alignment: 'center', margin: [0, 2, 0, 0] } ], margin: [4, 6, 4, 6] },
              { stack: [ { text: 'WORK MODE', fontSize: 6.5, bold: true, color: '#64748b', alignment: 'center' }, { text: candidateDossierData.workMode, fontSize: 8, bold: true, alignment: 'center', margin: [0, 2, 0, 0] } ], margin: [4, 6, 4, 6] }
            ]]},
            fillColor: '#ffffff',
            margin: [0, 0, 0, 12]
          },
          // Executive Summary & Recruiter Comments
          {
            columns: [
              { stack: [ { text: 'EXECUTIVE SUMMARY', fontSize: 8, bold: true, color: '#0284c7', margin: [0, 0, 0, 4] }, { text: candidateDossierData.careerSummary, fontSize: 7.5, lineHeight: 1.35 } ], margin: [0, 0, 6, 0] },
              { stack: [ { text: 'RECRUITER COMMENTS', fontSize: 8, bold: true, color: '#0284c7', margin: [0, 0, 0, 4] }, { text: candidateDossierData.recruiterNote, fontSize: 7.5, lineHeight: 1.35 } ], margin: [6, 0, 0, 0] }
            ],
            margin: [0, 0, 0, 14]
          },
          // Footer
          {
            table: { widths: ['*', 'auto'], body: [[
              { text: 'This report is generated by AI hiring platform jero.works', fontSize: 6.5, color: '#64748b', border: [false, true, false, false], margin: [0, 6, 0, 0] },
              { text: 'www.jero.works', fontSize: 6.5, color: '#64748b', border: [false, true, false, false], margin: [0, 6, 0, 0] }
            ]]},
            layout: { hLineColor: function(){ return '#e5e7eb'; }, hLineWidth: function(){ return 0.6; }, vLineWidth: function(){ return 0; } }
          }
        ];

        fullPdfContent = fullPdfContent.concat(reportTable);
      }

      // 2. PAGE 2 (OR PAGE 1 IF NO REPORT): COMPANY BRANDING BANNER (If Option 62)
      if(isBranded){
        var brandBanner = {
          table: { widths: ['*', 'auto'], body: [[
            { stack: [
                { text: 'SHARED BY', fontSize: 6.5, bold: true, color: '#64748b' },
                { text: companyName || 'Hiring Partner', fontSize: 13, bold: true, color: '#0f172a' }
              ], border: [false, false, false, true] },
            { stack: [
                { text: 'CONFIDENTIAL RESUME', fontSize: 12, bold: true, color: '#0f172a', alignment: 'right' },
                { text: (candidateDossierData.reportDate + ' · Verified ATS Format').toUpperCase(), fontSize: 7, color: '#0284c7', alignment: 'right', margin: [0, 2, 0, 0] }
              ], border: [false, false, false, true] }
          ]]},
          layout: { hLineColor: function(){ return '#0284c7'; }, hLineWidth: function(){ return 2; }, vLineWidth: function(){ return 0; } },
          margin: [0, 0, 0, 16]
        };
        fullPdfContent.push(brandBanner);
      }

      // 3. RÉSUMÉ CONTENT PARSING (Converts HTML DOM Elements to native vector pdfMake nodes)
      var activeKwList = Object.keys(keywords).filter(function(k){ return keywords[k].active; });
      var resumeNodes = parseHtmlToPdfMake(resumeBody, activeKwList, shouldHighlight);

      if(hasReport && resumeNodes.length > 0){
        resumeNodes[0].pageBreak = 'before';
      }

      fullPdfContent = fullPdfContent.concat(resumeNodes);

      var docDef = {
        pageSize: 'A4',
        pageMargins: [36, 30, 36, 30],
        defaultStyle: {
          font: 'Roboto',
          fontSize: 9,
          lineHeight: 1.35,
          color: '#1e293b'
        },
        content: fullPdfContent
      };

      var cleanName = (candName || 'Candidate').replace(/[^a-zA-Z0-9_\-]/g, '_');
      var sfx = (mode === 65 ? '_Dossier_Highlighted' : (mode === 64 ? '_Dossier' : (mode === 62 ? '_Branded' : '_ATS_Optimized')));
      var filename = 'Jeroworks_' + cleanName + sfx + '.pdf';

      window.pdfMake.createPdf(docDef).download(filename);

    } catch(err){
      console.error('Vector download error:', err);
      alert('Could not compile PDF: ' + (err && err.message ? err.message : 'Please try again.'));
    } finally {
      btn.disabled = false;
      btn.innerHTML = orig;
    }
  };

  // Converts HTML DOM elements to native vector pdfMake objects
  function parseHtmlToPdfMake(root, activeKeywords, shouldHighlight){
    var content = [];
    var kwMap = {};
    if(shouldHighlight && activeKeywords){
      activeKeywords.forEach(function(k){ if(k) kwMap[k.toLowerCase()] = true; });
    }

    function parseNode(node){
      if(node.nodeType === 3){
        var t = (node.nodeValue || '').trim();
        if(!t) return null;
        return { text: t + ' ' };
      }
      if(node.nodeType !== 1) return null;

      var tag = node.tagName.toLowerCase();
      if(tag === 'script' || tag === 'style') return null;

      if(tag === 'h1'){
        return { text: node.innerText.trim(), fontSize: 16, bold: true, color: '#0f172a', margin: [0, 10, 0, 4] };
      }
      if(tag === 'h2'){
        return { text: node.innerText.trim(), fontSize: 13, bold: true, color: '#0f172a', margin: [0, 8, 0, 3] };
      }
      if(tag === 'h3'){
        return { text: node.innerText.trim(), fontSize: 11, bold: true, color: '#1e293b', margin: [0, 6, 0, 2] };
      }
      if(tag === 'hr' || node.classList.contains('doc-page-break')){
        return { text: '', pageBreak: 'before' };
      }
      if(tag === 'ul'){
        var ulItems = [];
        Array.prototype.slice.call(node.children).forEach(function(li){
          if(li.tagName.toLowerCase() === 'li'){
            var inlines = parseInlines(li);
            if(inlines.length) ulItems.push({ text: inlines });
          }
        });
        return ulItems.length ? { ul: ulItems, margin: [0, 2, 0, 6] } : null;
      }
      if(tag === 'ol'){
        var olItems = [];
        Array.prototype.slice.call(node.children).forEach(function(li){
          if(li.tagName.toLowerCase() === 'li'){
            var inlines = parseInlines(li);
            if(inlines.length) olItems.push({ text: inlines });
          }
        });
        return olItems.length ? { ol: olItems, margin: [0, 2, 0, 6] } : null;
      }
      if(tag === 'img'){
        var src = node.getAttribute('src');
        if(src && src.indexOf('data:image') === 0){
          return { image: src, width: Math.min(node.clientWidth || 160, 220), margin: [0, 4, 0, 4] };
        }
        return null;
      }
      if(tag === 'p' || tag === 'div'){
        var pInlines = parseInlines(node);
        if(!pInlines.length) return { text: ' ', margin: [0, 1.5, 0, 1.5] };
        return { text: pInlines, margin: [0, 1.5, 0, 1.5] };
      }
      return null;
    }

    function parseInlines(parent){
      var inlines = [];
      Array.prototype.slice.call(parent.childNodes).forEach(function(ch){
        if(ch.nodeType === 3){
          var str = ch.nodeValue || '';
          if(str) inlines.push({ text: str });
        } else if(ch.nodeType === 1){
          var t = ch.tagName.toLowerCase();
          var txt = ch.innerText || ch.textContent || '';
          var span = { text: txt };
          if(t === 'b' || t === 'strong' || ch.style.fontWeight === 'bold' || parseInt(ch.style.fontWeight, 10) >= 600){
            span.bold = true;
          }
          if(t === 'i' || t === 'em' || ch.style.fontStyle === 'italic'){
            span.italics = true;
          }
          if(t === 'u' || (ch.style.textDecoration && ch.style.textDecoration.indexOf('underline') !== -1)){
            span.decoration = 'underline';
          }
          if(ch.style.color){
            span.color = ch.style.color;
          }
          if(t === 'mark' || ch.classList.contains('rcv-hit')){
            span.background = '#fef08a';
            span.color = '#854d0e';
            span.bold = true;
          } else if(ch.classList.contains('rcv-redact')){
            span.background = '#000000';
            span.color = '#000000';
          }
          inlines.push(span);
        }
      });
      return inlines;
    }

    Array.prototype.slice.call(root.children).forEach(function(child){
      var res = parseNode(child);
      if(res) content.push(res);
    });

    return content;
  }

  // Zoom controls
  var currentZoom = 1.0;
  document.getElementById('stripZoomIn').onclick = function(){
    currentZoom = Math.min(1.3, currentZoom + 0.1);
    sheetResume.style.transform = 'scale(' + currentZoom + ')';
    sheetReport.style.transform = 'scale(' + currentZoom + ')';
  };
  document.getElementById('stripZoomOut').onclick = function(){
    currentZoom = Math.max(0.7, currentZoom - 0.1);
    sheetResume.style.transform = 'scale(' + currentZoom + ')';
    sheetReport.style.transform = 'scale(' + currentZoom + ')';
  };
  document.getElementById('stripFitWidth').onclick = function(){
    currentZoom = 1.0;
    sheetResume.style.transform = 'scale(1)';
    sheetReport.style.transform = 'scale(1)';
  };
  document.getElementById('stripPrevPage').onclick = function(){
    canvas.scrollTo({ top: 0, behavior: 'smooth' });
  };
  document.getElementById('stripNextPage').onclick = function(){
    canvas.scrollTo({ top: sheetResume.offsetTop - 20, behavior: 'smooth' });
  };

  // Initial Load
  if(initialOpt && modePicker) modePicker.value = String(initialOpt);
  syncModeViews();
  loadCandidateResume();
</script>

</body>
</html>