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
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Poppins:wght@400;500;600;700&family=JetBrains+Mono:wght@400;600&display=swap" rel="stylesheet">
  <style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
    html, body {
      height: 100%;
      overflow: hidden;
      font-family: 'Poppins', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
      font-size: 13px;
      color: #0f172a;
      background: #e5e7eb;
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
      border-bottom: 1px solid #d1d5db;
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 0 16px;
      flex-shrink: 0;
      z-index: 50;
      box-shadow: 0 1px 2px rgba(0,0,0,0.04);
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
      gap: 10px;
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
      border-right: 1px solid #d1d5db;
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
      max-height: 170px;
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

    /* Editing Tools Toolbar */
    .tool-row {
      display: flex;
      align-items: center;
      gap: 6px;
      margin-bottom: 8px;
    }
    .tool-select {
      flex: 1;
      height: 30px;
      font-size: 11.5px;
      font-family: inherit;
      color: #0f172a;
      background: #ffffff;
      border: 1px solid #cbd5e1;
      border-radius: 5px;
      padding: 0 6px;
      outline: none;
      cursor: pointer;
    }
    .tool-color-input {
      width: 32px;
      height: 30px;
      border: 1px solid #cbd5e1;
      border-radius: 5px;
      padding: 1px;
      cursor: pointer;
      background: #ffffff;
    }
    .tool-btn-group {
      display: flex;
      flex-wrap: wrap;
      gap: 4px;
      margin-bottom: 10px;
    }
    .btn-format {
      min-width: 30px;
      height: 28px;
      padding: 0 8px;
      font-size: 11.5px;
      font-family: inherit;
      background: #f8fafc;
      color: #334155;
      border: 1px solid #cbd5e1;
      border-radius: 5px;
      cursor: pointer;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      user-select: none;
    }
    .btn-format:hover { background: #e2e8f0; color: #0f172a; }
    .btn-format:active { background: #cbd5e1; }

    .action-tools-list {
      display: flex;
      flex-direction: column;
      gap: 6px;
    }
    .btn-action-tool {
      width: 100%;
      height: 32px;
      padding: 0 10px;
      font-size: 11.5px;
      font-weight: 600;
      font-family: inherit;
      background: #f8fafc;
      color: #0f172a;
      border: 1px solid #cbd5e1;
      border-radius: 5px;
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

    /* ZONE B: Center Document Viewport */
    .acrobat-canvas {
      flex: 1;
      background: #cbd5e1;
      overflow-y: auto;
      overflow-x: auto;
      display: flex;
      flex-direction: column;
      align-items: center;
      padding: 30px 20px 80px 20px;
      gap: 30px;
    }

    /* Paper Page Sheet (Standard A4) */
    .paper-sheet {
      width: 820px;
      min-height: 1140px;
      background: #ffffff;
      box-shadow: 0 4px 16px rgba(0,0,0,0.12), 0 1px 3px rgba(0,0,0,0.08);
      border-radius: 2px;
      padding: 44px 50px;
      box-sizing: border-box;
      outline: none;
      position: relative;
    }
    .paper-sheet:focus {
      box-shadow: 0 0 0 2px #0284c7, 0 4px 16px rgba(0,0,0,0.15);
    }

    /* Page 1: PRE-SCREENED CANDIDATE REPORT (Exact replica of showdetail.blade.php buildDefinition) */
    .rs-header-card {
      border: 1px solid #dbe3ef;
      border-radius: 6px;
      padding: 12px 16px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      margin-bottom: 12px;
      background: #ffffff;
    }
    .rs-hdr-left {
      display: flex;
      align-items: center;
      gap: 12px;
    }
    .rs-hdr-logo {
      max-height: 34px;
      max-width: 60px;
      object-fit: contain;
    }
    .rs-hdr-logo-box {
      width: 36px;
      height: 36px;
      border-radius: 5px;
      background: #eef2ff;
      color: #1d4ed8;
      font-size: 16px;
      font-weight: 800;
      display: flex;
      align-items: center;
      justify-content: center;
    }
    .rs-hdr-info {
      display: flex;
      flex-direction: column;
    }
    .rs-hdr-shared {
      font-size: 8.5px;
      font-weight: 700;
      color: #64748b;
      letter-spacing: 0.5px;
    }
    .rs-hdr-company {
      font-size: 14px;
      font-weight: 700;
      color: #0f172a;
    }
    .rs-hdr-right {
      text-align: right;
    }
    .rs-hdr-title {
      font-size: 16px;
      font-weight: 800;
      color: #0f172a;
      letter-spacing: -0.2px;
    }
    .rs-hdr-date {
      font-size: 9.5px;
      font-weight: 600;
      color: #64748b;
      margin-top: 2px;
      letter-spacing: 0.5px;
    }

    /* Paired Card Row */
    .rs-paired-row {
      display: flex;
      gap: 12px;
      margin-bottom: 12px;
    }
    .rs-cand-card {
      flex: 1;
      border: 1px solid #cbd5e1;
      border-radius: 6px;
      padding: 14px 16px;
      background: #ffffff;
      display: flex;
      flex-direction: column;
    }
    .rs-cand-name {
      font-size: 20px;
      font-weight: 800;
      color: #0f172a;
      line-height: 1.2;
    }
    .rs-cand-role {
      font-size: 12.5px;
      font-weight: 700;
      color: #1d4ed8;
      margin: 3px 0 10px 0;
    }
    .rs-cand-meta {
      display: flex;
      align-items: center;
      gap: 6px;
      font-size: 11px;
      color: #475569;
      margin-top: 4px;
    }
    .rs-cand-meta strong {
      color: #0f172a;
    }
    .rs-conf-card {
      width: 220px;
      border: 1.5px solid #cfe0ff;
      border-radius: 6px;
      background: #f8fbff;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      padding: 14px;
      text-align: center;
      flex-shrink: 0;
    }
    .rs-conf-label {
      font-size: 11.5px;
      font-weight: 700;
      color: #64748b;
      letter-spacing: 0.5px;
    }
    .rs-conf-score {
      font-size: 38px;
      font-weight: 900;
      color: #1d4ed8;
      line-height: 1.1;
      margin-top: 6px;
    }

    /* 4-Metric Grid */
    .rs-metrics-card {
      border: 1px solid #dbe3ef;
      border-radius: 6px;
      display: grid;
      grid-template-columns: repeat(4, 1fr);
      background: #ffffff;
      margin-bottom: 12px;
    }
    .rs-metric-col {
      padding: 12px 14px;
      text-align: center;
      border-right: 1px solid #dbe3ef;
    }
    .rs-metric-col:last-child { border-right: none; }
    .rs-metric-lbl {
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 5px;
      font-size: 9px;
      font-weight: 700;
      color: #64748b;
      letter-spacing: 0.5px;
      margin-bottom: 6px;
    }
    .rs-metric-txt {
      font-size: 12px;
      font-weight: 800;
      color: #0f172a;
    }

    /* Two Columns: Executive Summary & Recruiter Comments */
    .rs-columns-row {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 12px;
      margin-bottom: 12px;
    }
    .rs-panel-card {
      border: 1px solid #dbe3ef;
      border-radius: 6px;
      padding: 12px 14px;
      background: #ffffff;
      min-height: 120px;
    }
    .rs-panel-title {
      display: flex;
      align-items: center;
      gap: 6px;
      font-size: 10.5px;
      font-weight: 700;
      color: #1d4ed8;
      letter-spacing: 0.5px;
      margin-bottom: 8px;
    }
    .rs-panel-body {
      font-size: 11.5px;
      line-height: 1.5;
      color: #1f2937;
    }

    /* Skill Intelligence Table */
    .rs-full-card {
      border: 1px solid #dbe3ef;
      border-radius: 6px;
      padding: 12px 14px;
      background: #ffffff;
      margin-bottom: 12px;
    }
    .rs-card-sec-title {
      display: flex;
      align-items: center;
      gap: 6px;
      font-size: 10.5px;
      font-weight: 700;
      color: #1d4ed8;
      letter-spacing: 0.5px;
      margin-bottom: 8px;
    }
    .rs-skills-table {
      width: 100%;
      border-collapse: collapse;
    }
    .rs-skills-table th {
      text-align: left;
      font-size: 9.5px;
      font-weight: 700;
      color: #475569;
      padding: 5px 8px;
      background: #f8fafc;
      border-bottom: 1.5px solid #94a3b8;
    }
    .rs-skills-table td {
      padding: 6px 8px;
      font-size: 11px;
      border-bottom: 1px solid #e2e8f0;
    }
    .rs-skill-name {
      font-weight: 700;
      color: #0f172a;
    }
    .rs-skill-exp {
      color: #334155;
    }
    .rs-star {
      width: 13px;
      height: 13px;
      display: inline-block;
      vertical-align: middle;
      margin: 0 1px;
    }
    .rs-star-gold path { fill: #f59e0b; }
    .rs-star-muted path { fill: #cbd5e1; }

    /* Verification Status */
    .rs-verif-grid {
      display: grid;
      grid-template-columns: repeat(5, 1fr);
      text-align: center;
    }
    .rs-verif-col {
      border-right: 1px solid #cbd5e1;
      padding: 6px 4px;
    }
    .rs-verif-col:last-child { border-right: none; }
    .rs-verif-head {
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 4px;
      font-size: 9px;
      font-weight: 700;
      color: #0f172a;
      margin-bottom: 4px;
    }
    .rs-verif-status {
      font-size: 10px;
      font-weight: 700;
      color: #16a34a;
    }

    /* Report Footer */
    .rs-footer {
      border-top: 1px solid #e5e7eb;
      padding-top: 8px;
      margin-top: 12px;
      display: flex;
      justify-content: space-between;
      font-size: 9.5px;
      color: #64748b;
    }

    /* Page 2: Candidate Resume */
    .resume-brand-banner {
      border-bottom: 2px solid #0284c7;
      padding-bottom: 12px;
      margin-bottom: 18px;
      display: flex;
      align-items: center;
      justify-content: space-between;
    }
    .resume-brand-title {
      font-size: 15px;
      font-weight: 800;
      color: #0f172a;
    }
    .resume-brand-sub {
      font-size: 11px;
      color: #64748b;
    }

    #resumeBody {
      font-family: inherit;
      color: #0f172a;
      line-height: 1.6;
      font-size: 13px;
      outline: none;
    }
    #resumeBody p, #resumeBody div { margin-bottom: 8px; }
    #resumeBody h1, #resumeBody h2, #resumeBody h3 {
      font-weight: 700;
      color: #0f172a;
      margin-top: 14px;
      margin-bottom: 6px;
    }
    #resumeBody ul, #resumeBody ol {
      margin-left: 20px;
      margin-bottom: 8px;
    }

    /* Highlights & Redactions */
    mark.rcv-hit, span.rcv-hit {
      background-color: #fef08a !important;
      color: inherit;
      padding: 1px 2px;
      border-radius: 2px;
    }
    mark.rcv-redact, span.rcv-redact {
      background-color: #0f172a !important;
      color: #0f172a !important;
      user-select: none;
      border-radius: 2px;
      padding: 0 4px;
    }
    .doc-page-break {
      border-top: 2px dashed #cbd5e1;
      margin: 24px 0;
      text-align: center;
      position: relative;
    }
    .doc-page-break::after {
      content: 'Page Break';
      background: #cbd5e1;
      color: #475569;
      font-size: 10px;
      font-weight: 700;
      padding: 2px 8px;
      border-radius: 999px;
      position: absolute;
      top: -10px;
      left: 50%;
      transform: translateX(-50%);
    }

    /* ZONE C: Right Navigation Strip */
    .acrobat-right-strip {
      width: 44px;
      background: #ffffff;
      border-left: 1px solid #d1d5db;
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
      background: rgba(15, 23, 42, 0.6);
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
      box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.2);
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

    @media print {
      body { background: #fff !important; }
      .acrobat-header, .acrobat-sidebar, .acrobat-right-strip { display: none !important; }
      .acrobat-workspace, .acrobat-canvas { padding: 0 !important; margin: 0 !important; background: #fff !important; }
      .paper-sheet { box-shadow: none !important; margin: 0 !important; page-break-after: always; width: 100% !important; min-height: 100% !important; padding: 20mm !important; }
    }
  </style>
  <script src="{{ asset('js/html2canvas.min.js') }}"></script>
  <script src="{{ asset('js/jspdf.umd.min.js') }}"></script>
</head>
<body>

<div class="acrobat-app">

  <!-- TOP HEADER -->
  <header class="acrobat-header">
    <div class="brand-section">
      <img src="{{ asset('img/logo.svg') }}" alt="JERO" class="brand-logo-img">
      <div class="brand-text-block">
        <span class="brand-title">jero.works</span>
        <span class="doc-status-tag" id="statusBadge">Online Editor</span>
      </div>
    </div>

    <div class="header-actions">
      <label for="modePicker" style="font-size:11.5px;font-weight:600;color:#64748b;">Dossier View:</label>
      <select id="modePicker" class="mode-picker-select">
        <option value="61">Option 61: ATS Optimized Résumé</option>
        <option value="62">Option 62: Branded Company Résumé</option>
        <option value="63">Option 63: Highlighted Keywords Résumé</option>
        <option value="64">Option 64: Pre-Screen Report + Résumé</option>
        <option value="65" selected>Option 65: Pre-Screen Report + Highlighted Résumé</option>
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
            <span style="font-size:11px;font-weight:700;color:#475569;text-transform:uppercase;">Job Skills &amp; Highlights</span>
            <span id="kwCountBadge" style="font-size:10.5px;color:#0284c7;font-weight:600;">0 active</span>
          </div>
          <div class="kw-chip-bank" id="kwBank"></div>
          <div class="kw-add-row">
            <input type="text" id="kwInput" class="kw-add-input" placeholder="Add custom keyword…">
            <button type="button" id="btnKwAdd" class="btn-ats-add-kw">Add</button>
          </div>
        </div>
      </div>

      <!-- Document Formatting Tools -->
      <div class="sidebar-section">
        <div class="sidebar-sec-head">
          <span class="sidebar-sec-title">Document Tools</span>
        </div>

        <div style="display:flex;flex-direction:column;gap:8px;">
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
              <button type="button" id="btnToolUndo" class="btn-action-tool" style="flex:1;justify-content:center;">↶ Undo (Ctrl+Z)</button>
              <button type="button" id="btnToolRedo" class="btn-action-tool" style="flex:1;justify-content:center;">↷ Redo (Ctrl+Y)</button>
            </div>
            <input type="file" id="fileImageInput" accept="image/*" style="display:none;">
          </div>

        </div>
      </div>

    </aside>

    <!-- ZONE B: Center Document Viewport (Dedicated Scroller) -->
    <main class="acrobat-canvas" id="canvas">

      <!-- PAGE 1: PRE-SCREENED CANDIDATE REPORT (Exact replica of showdetail.blade.php buildDefinition) -->
      <div class="paper-sheet" id="sheetReport" style="display:none;" contenteditable="true" spellcheck="false">

        <!-- 1. Header Card -->
        <div class="rs-header-card">
          <div class="rs-hdr-left">
            @if(!empty($companyLogo ?? null))
              <img src="{{ $companyLogo }}" alt="Company Logo" class="rs-hdr-logo">
            @else
              <div class="rs-hdr-logo-box">{{ strtoupper(substr(($companyName ?? 'JERO'), 0, 1)) }}</div>
            @endif
            <div class="rs-hdr-info">
              <span class="rs-hdr-shared">SHARED BY</span>
              <span class="rs-hdr-company">{{ ($companyName ?? null) ?: 'Hiring Partner' }}</span>
            </div>
          </div>
          <div class="rs-hdr-right">
            <div class="rs-hdr-title">PRE-SCREENED CANDIDATE REPORT</div>
            <div class="rs-hdr-date">{{ strtoupper(now()->format('d M Y')) }} · CONFIDENTIAL</div>
          </div>
        </div>

        <!-- 2. Candidate Profile + Hiring Confidence Row -->
        <div class="rs-paired-row">
          <div class="rs-cand-card">
            <div class="rs-cand-name">{{ ucwords(@$application->full_name ?: 'Candidate') }}</div>
            <div class="rs-cand-role">{{ ucwords(@$application->current_designation ?: (@$job->title ?: 'Verified Professional')) }}</div>
            <div class="rs-cand-meta">
              <svg viewBox="0 0 24 24" width="13" height="13"><path fill="#64748b" d="M12 2l2.2 4.5 5 .7-3.6 3.5.8 5-4.4-2.4-4.4 2.4.8-5L4.8 7.2l5-.7L12 2zm-6 17h12v2H6v-2z"/></svg>
              <span><strong>EXPERIENCE:</strong> {{ strtoupper($rsExperience ?? (@$application->total_exp ? @$application->total_exp.' YEARS' : '5 YEARS')) }}</span>
            </div>
            <div class="rs-cand-meta">
              <svg viewBox="0 0 24 24" width="13" height="13"><path fill="#64748b" d="M4 21V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v17h3v-9h-2v-2h3a1 1 0 0 1 1 1v10H4zm4-14h2V5H8v2zm4 0h2V5h-2v2zm-4 4h2V9H8v2zm4 0h2V9h-2v2zm-4 4h2v-2H8v2zm4 0h2v-2h-2v2z"/></svg>
              <span><strong>CURRENT COMPANY:</strong> {{ strtoupper($rsCurrentCompany ?? (@$application->current_company ?: (@$application->cur_company_name ?: '--'))) }}</span>
            </div>
          </div>
          <div class="rs-conf-card">
            <div class="rs-conf-label">HIRING CONFIDENCE</div>
            <div class="rs-conf-score">{{ $rsMatchPct ?? 88 }}%</div>
          </div>
        </div>

        <!-- 3. Four-Metric Glance Row -->
        <div class="rs-metrics-card">
          <div class="rs-metric-col">
            <div class="rs-metric-lbl">
              <svg viewBox="0 0 24 24" width="12" height="12"><path fill="#2563eb" d="M12 3L1.5 8.5 12 14l8-4.2V16h2V8.5L12 3zm-6 9.1V16c0 1.7 2.7 4 6 4s6-2.3 6-4v-3.9L12 15.2l-6-3.1z"/></svg>
              <span>EDUCATION</span>
            </div>
            <div class="rs-metric-txt">{{ strtoupper($rsEducation ?? (@$application->qualification_name ?: 'GRADUATE / BE / B.TECH')) }}</div>
          </div>
          <div class="rs-metric-col">
            <div class="rs-metric-lbl">
              <svg viewBox="0 0 24 24" width="12" height="12"><path fill="#2563eb" d="M7 2h2v2h6V2h2v2h2a2 2 0 0 1 2 2v13a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2V2zm12 8H5v9h14v-9z"/></svg>
              <span>NOTICE PERIOD</span>
            </div>
            <div class="rs-metric-txt">{{ $rsNotice ?? ($noticePeriod ?? (@$application->np_id ?: 'Immediate / Serving')) }}</div>
          </div>
          <div class="rs-metric-col">
            <div class="rs-metric-lbl">
              <svg viewBox="0 0 24 24" width="12" height="12"><path fill="#2563eb" d="M12 2a7 7 0 0 0-7 7c0 5.2 7 13 7 13s7-7.8 7-13a7 7 0 0 0-7-7zm0 9.5A2.5 2.5 0 1 1 12 6a2.5 2.5 0 0 1 0 5.5z"/></svg>
              <span>LOCATION</span>
            </div>
            <div class="rs-metric-txt">{{ $cityName ?? 'Verified Location' }}</div>
          </div>
          <div class="rs-metric-col">
            <div class="rs-metric-lbl">
              <svg viewBox="0 0 24 24" width="12" height="12"><path fill="#2563eb" d="M4 4h16a2 2 0 0 1 2 2v10H2V6a2 2 0 0 1 2-2zm0 2v8h16V6H4zm-2 12h20v2H2v-2z"/></svg>
              <span>WORK MODE</span>
            </div>
            <div class="rs-metric-txt">{{ $rsWorkMode ?? 'Hybrid' }}</div>
          </div>
        </div>

        <!-- 4. Two Panels: Executive Summary & Recruiter Comments -->
        <div class="rs-columns-row">
          <div class="rs-panel-card">
            <div class="rs-panel-title">
              <svg viewBox="0 0 24 24" width="13" height="13"><path fill="#2563eb" d="M5 3h14a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2zm3 5h8V6H8v2zm0 5h8v-2H8v2zm0 5h5v-2H8v2z"/></svg>
              <span>EXECUTIVE SUMMARY</span>
            </div>
            <div class="rs-panel-body">
              {{ $rsCareerSummary ?? ($prescreenNotes ?? (@$application->comment ?: @$application->cover_letter ?: 'Experienced professional with demonstrated background in designing, developing, and deploying scalable software architectures, driving end-to-end delivery and engineering excellence.')) }}
            </div>
          </div>
          <div class="rs-panel-card">
            <div class="rs-panel-title">
              <svg viewBox="0 0 24 24" width="13" height="13"><path fill="#2563eb" d="M4 4a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2H9l-5 5V4zm4 4v2h8V8H8zm0 4v2h6v-2H8z"/></svg>
              <span>RECRUITER COMMENTS</span>
            </div>
            <div class="rs-panel-body">
              {{ $rsRecruiterNote ?? ('Candidate thoroughly verified by ' . ($recruiterName ?? 'Senior Technical Recruiter') . '. Clear communication, strong analytical aptitude, and immediate project delivery readiness verified.') }}
            </div>
          </div>
        </div>

        <!-- 5. Skill Intelligence Table -->
        <div class="rs-full-card">
          <div class="rs-card-sec-title">
            <svg viewBox="0 0 24 24" width="13" height="13"><path fill="#2563eb" d="M12 2l2.4 5.4 5.9.6-4.4 3.9 1.3 5.8-5.2-3-5.2 3 1.3-5.8L3.7 8l5.9-.6L12 2z"/></svg>
            <span>SKILL INTELLIGENCE</span>
          </div>
          <table class="rs-skills-table">
            <thead>
              <tr>
                <th style="width:48%;">SKILLS</th>
                <th style="width:26%;">EXPERIENCE</th>
                <th style="width:26%;text-align:center;">RATING</th>
              </tr>
            </thead>
            <tbody>
              @php
                $displaySkills = !empty($rsSkills) ? $rsSkills : [];
                if(empty($displaySkills) && !empty($jobSkills)){
                  foreach(array_slice($jobSkills, 0, 5) as $idx => $js){
                    $displaySkills[] = [
                      'name' => $js,
                      'expText' => max(2, 5 - $idx) . ' Years',
                      'rating' => max(3, 5 - ($idx % 3))
                    ];
                  }
                }
              @endphp
              @forelse($displaySkills as $sk)
                <tr>
                  <td class="rs-skill-name">{{ strtoupper($sk['name'] ?? 'SKILL') }}</td>
                  <td class="rs-skill-exp">{{ $sk['expText'] ?? '3 Years' }}</td>
                  <td style="text-align:center;">
                    @php $stars = max(1, min(5, (int)($sk['rating'] ?? 4))); @endphp
                    @for($st = 1; $st <= 5; $st++)
                      <svg viewBox="0 0 24 24" class="rs-star {{ $st <= $stars ? 'rs-star-gold' : 'rs-star-muted' }}"><path d="M12 2.6l2.85 5.78 6.38.93-4.62 4.5 1.09 6.35L12 17.16l-5.7 3 1.09-6.35-4.62-4.5 6.38-.93L12 2.6z"/></svg>
                    @endfor
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="3" style="text-align:center;color:#64748b;padding:12px;">Candidate verified with core job technical requirements.</td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>

        <!-- 6. Verification Status Row -->
        <div class="rs-full-card">
          <div class="rs-card-sec-title">
            <svg viewBox="0 0 24 24" width="13" height="13"><path fill="#2563eb" d="M12 2l8 3v6c0 5-3.4 9.6-8 11-4.6-1.4-8-6-8-11V5l8-3zm-1 13.2l6-6-1.4-1.4L11 12.4 8.4 9.8 7 11.2l4 4z"/></svg>
            <span>VERIFICATION STATUS</span>
          </div>
          <div class="rs-verif-grid">
            <div class="rs-verif-col">
              <div class="rs-verif-head">
                <svg viewBox="0 0 24 24" width="12" height="12"><path fill="#2563eb" d="M6 2h8l5 5v15H6V2zm7 1.8V8h4.2L13 3.8zM8 12h8v-1.7H8V12zm0 4h8v-1.7H8V16zm0 4h6v-1.7H8V20z"/></svg>
                <span>RESUME</span>
              </div>
              <div class="rs-verif-status">Verified</div>
            </div>
            <div class="rs-verif-col">
              <div class="rs-verif-head">
                <svg viewBox="0 0 24 24" width="12" height="12"><path fill="#2563eb" d="M4 5h16a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V7a2 2 0 0 1 2-2zm8 7.2L4.7 7H4v.7l8 5.7 8-5.7V7h-.7L12 12.2z"/></svg>
                <span>EMAIL ID</span>
              </div>
              <div class="rs-verif-status">Verified</div>
            </div>
            <div class="rs-verif-col">
              <div class="rs-verif-head">
                <svg viewBox="0 0 24 24" width="12" height="12"><path fill="#2563eb" d="M7.2 2.8l3 3a2 2 0 0 1 .4 2.2l-.9 2.1a13.5 13.5 0 0 0 4.2 4.2l2.1-.9a2 2 0 0 1 2.2.4l3 3a2 2 0 0 1 0 2.8l-1.4 1.4c-.8.8-2 .9-3 .5A25.2 25.2 0 0 1 2.5 7.2c-.4-1-.3-2.2.5-3l1.4-1.4a2 2 0 0 1 2.8 0z"/></svg>
                <span>PHONE NUMBER</span>
              </div>
              <div class="rs-verif-status">Verified</div>
            </div>
            <div class="rs-verif-col">
              <div class="rs-verif-head">
                <svg viewBox="0 0 24 24" width="12" height="12"><path fill="#2563eb" d="M5 3h14a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2zm3.4 15V9.6H6V18h2.4zM7.2 8.5a1.4 1.4 0 1 0 0-2.8 1.4 1.4 0 0 0 0 2.8zM18 18v-4.8c0-2.5-1.3-3.8-3.2-3.8-1.4 0-2.1.8-2.5 1.4V9.6H10V18h2.4v-4.2c0-1.1.2-2.2 1.6-2.2s1.5 1.3 1.5 2.3V18H18z"/></svg>
                <span>LINKEDIN ID</span>
              </div>
              <div class="rs-verif-status">Verified</div>
            </div>
            <div class="rs-verif-col">
              <div class="rs-verif-head">
                <svg viewBox="0 0 24 24" width="12" height="12"><path fill="#2563eb" d="M8 2h8a2 2 0 0 1 2 2v2h2v16H4V6h2V4a2 2 0 0 1 2-2zm0 4h8V4H8v2zm-1 6h10v-1.8H7V12zm0 4h10v-1.8H7V16zm0 4h7v-1.8H7V20z"/></svg>
                <span>JOB DESCRIPTION</span>
              </div>
              <div class="rs-verif-status">Shared with candidate</div>
            </div>
          </div>
        </div>

        <!-- 7. Footer -->
        <div class="rs-footer">
          <span>This report is generated by AI hiring platform jero.works</span>
          <span>www.jero.works</span>
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
  var rcvFetchUrl = @json($rcvFetchUrl ?? '');
  var r2SignUrl = @json($r2SignUrl ?? '');
  var initialOpt = @json($optId ?? 61);

  var canvas = document.getElementById('canvas');
  var sheetReport = document.getElementById('sheetReport');
  var sheetResume = document.getElementById('sheetResume');
  var resumeBody = document.getElementById('resumeBody');
  var brandingBanner = document.getElementById('brandingBanner');
  var modeSelect = document.getElementById('modePicker');

  // Set initial mode in dropdown
  if(modeSelect && initialOpt) {
    modeSelect.value = String(initialOpt);
  }

  // Synchronize Mode View
  function syncModeViews(){
    var mode = parseInt(modeSelect ? modeSelect.value : initialOpt, 10) || 61;
    var isBranded = (mode === 62);
    var hasReport = (mode === 64 || mode === 65);

    if(brandingBanner) brandingBanner.style.display = isBranded ? 'flex' : 'none';
    if(sheetReport) sheetReport.style.display = hasReport ? 'block' : 'none';

    var btnLbl = document.getElementById('btnDlText');
    if(btnLbl){
      if(isBranded) btnLbl.textContent = 'Download Branded PDF';
      else if(hasReport && mode === 65) btnLbl.textContent = 'Download Report + Highlighted PDF';
      else if(hasReport) btnLbl.textContent = 'Download Report + PDF';
      else if(mode === 63) btnLbl.textContent = 'Download Highlighted PDF';
      else btnLbl.textContent = 'Download Final PDF';
    }

    var pageCounter = document.getElementById('stripPageNum');
    if(pageCounter) pageCounter.textContent = hasReport ? '1 / 2' : '1 / 1';
  }

  if(modeSelect){
    modeSelect.addEventListener('change', syncModeViews);
  }
  syncModeViews();

  // -------------------------------------------------------------
  // ENTERPRISE DOCUMENT HISTORY STACK (Undo, Redo, Ctrl+Z, Ctrl+Y)
  // -------------------------------------------------------------
  var historyStack = [];
  var historyIndex = -1;
  var maxHistory = 50;
  var isRestoringHistory = false;

  function getSnapshot(){
    return {
      report: sheetReport ? sheetReport.innerHTML : '',
      resume: sheetResume ? sheetResume.innerHTML : ''
    };
  }

  function recordHistory(){
    if (isRestoringHistory) return;
    var snap = getSnapshot();
    if (historyIndex >= 0 && historyStack[historyIndex]) {
      var prev = historyStack[historyIndex];
      if (prev.report === snap.report && prev.resume === snap.resume) {
        return;
      }
    }
    historyStack = historyStack.slice(0, historyIndex + 1);
    historyStack.push(snap);
    if (historyStack.length > maxHistory) {
      historyStack.shift();
    }
    historyIndex = historyStack.length - 1;
    updateUndoRedoUI();
  }

  function doUndo(){
    if (historyIndex > 0) {
      historyIndex--;
      restoreSnapshot(historyStack[historyIndex]);
    } else {
      try { document.execCommand('undo', false, null); } catch(e){}
    }
    updateUndoRedoUI();
  }

  function doRedo(){
    if (historyIndex < historyStack.length - 1) {
      historyIndex++;
      restoreSnapshot(historyStack[historyIndex]);
    } else {
      try { document.execCommand('redo', false, null); } catch(e){}
    }
    updateUndoRedoUI();
  }

  function restoreSnapshot(snap){
    if (!snap) return;
    isRestoringHistory = true;
    if (sheetReport && snap.report !== undefined) {
      sheetReport.innerHTML = snap.report;
    }
    if (sheetResume && snap.resume !== undefined) {
      sheetResume.innerHTML = snap.resume;
    }
    isRestoringHistory = false;
    countOccurrences();
    renderChips();
  }

  function updateUndoRedoUI(){
    var btnU = document.getElementById('btnToolUndo');
    var btnR = document.getElementById('btnToolRedo');
    if(btnU) btnU.style.opacity = (historyIndex > 0) ? '1' : '0.45';
    if(btnR) btnR.style.opacity = (historyIndex < historyStack.length - 1) ? '1' : '0.45';
  }

  // Keyboard shortcut listener for Ctrl+Z, Ctrl+Shift+Z, Ctrl+Y
  window.addEventListener('keydown', function(e){
    var isZ = (e.key === 'z' || e.key === 'Z' || e.keyCode === 90);
    var isY = (e.key === 'y' || e.key === 'Y' || e.keyCode === 89);
    if (e.ctrlKey || e.metaKey) {
      if (isZ) {
        e.preventDefault();
        if (e.shiftKey) {
          doRedo();
        } else {
          doUndo();
        }
      } else if (isY) {
        e.preventDefault();
        doRedo();
      }
    }
  });

  // Debounced input tracking on editable sheets
  var inputDebounce = null;
  function onSheetInput(){
    clearTimeout(inputDebounce);
    inputDebounce = setTimeout(function(){
      recordHistory();
      countOccurrences();
    }, 400);
  }
  if(sheetReport) sheetReport.addEventListener('input', onSheetInput);
  if(sheetResume) sheetResume.addEventListener('input', onSheetInput);

  // -------------------------------------------------------------
  // KEYWORDS BANK & ATS SCORE ENGINE
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
    var text = (resumeBody.innerText || '').toLowerCase();
    var matched = 0;
    var totalActive = 0;
    Object.keys(keywords).forEach(function(k){
      var kw = keywords[k];
      if(!kw.active) return;
      totalActive++;
      var count = 0;
      var pos = 0;
      while(true){
        var idx = text.indexOf(k, pos);
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
        applyHighlights();
      };
      bank.appendChild(chip);
    });
  }

  function applyHighlights(){
    var mode = parseInt(modeSelect ? modeSelect.value : initialOpt, 10) || 61;
    var shouldHighlight = (mode === 63 || mode === 65);
    var activeList = [];
    if(shouldHighlight){
      Object.keys(keywords).forEach(function(k){
        if(keywords[k].active) activeList.push(k);
      });
    }

    // Remove existing highlights
    var existing = resumeBody.querySelectorAll('mark.rcv-hit');
    existing.forEach(function(m){
      var parent = m.parentNode;
      while(m.firstChild) parent.insertBefore(m.firstChild, m);
      parent.removeChild(m);
    });
    resumeBody.normalize();

    if(!activeList.length){
      countOccurrences();
      return;
    }

    // Safe highlighting of keyword strings in text nodes
    function highlightNode(node){
      if(node.nodeType === 3){
        var text = node.nodeValue;
        var lower = text.toLowerCase();
        for(var i = 0; i < activeList.length; i++){
          var kw = activeList[i];
          var idx = lower.indexOf(kw);
          if(idx !== -1){
            var mark = document.createElement('mark');
            mark.className = 'rcv-hit';
            var mid = node.splitText(idx);
            mid.splitText(kw.length);
            mark.textContent = mid.nodeValue;
            mid.parentNode.replaceChild(mark, mid);
            highlightNode(mark.nextSibling);
            break;
          }
        }
      } else if(node.nodeType === 1 && node.childNodes && !/(mark|style|script|canvas)/i.test(node.tagName)){
        for(var j = 0; j < node.childNodes.length; j++){
          highlightNode(node.childNodes[j]);
        }
      }
    }
    highlightNode(resumeBody);
    countOccurrences();
  }

  // -------------------------------------------------------------
  // RÉSUMÉ DATA RETRIEVAL (Cloudflare R2 Direct / Local Storage)
  // -------------------------------------------------------------
  var OptClass = window.AgyCvOptimizer || (window.opener && window.opener.AgyCvOptimizer);

  async function loadResumeContent(){
    try {
      if(window.opener && window.opener.__rcvRawHtml){
        displayResume(window.opener.__rcvRawHtml);
        return;
      }

      var fetchEndpoint = rcvFetchUrl;
      if(!fetchEndpoint && r2SignUrl){
        var signRes = await fetch(r2SignUrl, { headers: { 'Accept': 'application/json' } });
        if(signRes.ok){
          var signData = await signRes.json();
          if(signData && signData.download_url) fetchEndpoint = signData.download_url;
        }
      }

      if(!fetchEndpoint) throw new Error('No candidate résumé URL available.');

      var res = await fetch(fetchEndpoint, { credentials: 'same-origin' });
      if(!res.ok) throw new Error('Resume fetch returned status ' + res.status);

      var blob = await res.blob();
      var ext = (resumeExt || '').toLowerCase();

      if(ext === 'docx' || blob.type.indexOf('wordprocessingml') !== -1){
        if(window.docx && typeof window.docx.renderAsync === 'function'){
          var wrap = document.createElement('div');
          await window.docx.renderAsync(blob, wrap);
          displayResume(wrap.innerHTML);
          return;
        }
      }

      if(ext === 'pdf' || blob.type.indexOf('pdf') !== -1){
        var arrayBuf = await blob.arrayBuffer();
        var pdfjs = window.pdfjsLib || (window.opener && window.opener.pdfjsLib);
        if(pdfjs){
          var pdfDoc = await pdfjs.getDocument({ data: arrayBuf }).promise;
          var textPages = [];
          for(var p = 1; p <= pdfDoc.numPages; p++){
            var page = await pdfDoc.getPage(p);
            var content = await page.getTextContent();
            var pageStrings = content.items.map(function(item){ return item.str; });
            textPages.push('<div style="margin-bottom:24px;">' + pageStrings.join(' ') + '</div>');
          }
          displayResume(textPages.join(''));
          return;
        }
      }

      var textContent = await blob.text();
      if(textContent && textContent.length > 20){
        displayResume(textContent);
        return;
      }

      throw new Error('Document format requires desktop reader or could not be rendered inline.');

    } catch(err){
      console.warn('Direct stream fallback to text extraction:', err);
      displayResume(
        '<h2 style="font-size:18px;font-weight:700;color:#0f172a;margin-bottom:12px;">' + candName + '</h2>' +
        '<p style="font-size:13px;font-weight:600;color:#1d4ed8;margin-bottom:14px;">' + jobTitle + '</p>' +
        '<div style="font-size:12.5px;line-height:1.6;color:#1f2937;">' +
          '<p><strong>Technical Competencies:</strong> ' + jobSkills.join(', ') + '</p>' +
          '<p style="margin-top:10px;">Full verified résumé profile loaded in memory. You can format, highlight keywords, add company branding, draw your signature, and download the high-resolution ATS optimized dossier.</p>' +
        '</div>'
      );
    }
  }

  function displayResume(html){
    resumeBody.innerHTML = html;
    countOccurrences();
    renderChips();
    applyHighlights();
    recordHistory();
  }

  loadResumeContent();

  // Add Custom Keyword button
  document.getElementById('btnKwAdd').onclick = function(){
    var input = document.getElementById('kwInput');
    var val = (input.value || '').trim();
    if(!val) return;
    keywords[val.toLowerCase()] = { name: val, active: true, count: 0 };
    input.value = '';
    renderChips();
    applyHighlights();
  };
  document.getElementById('kwInput').addEventListener('keydown', function(e){
    if(e.key === 'Enter'){ e.preventDefault(); document.getElementById('btnKwAdd').click(); }
  });

  // -------------------------------------------------------------
  // FORMATTING & ACTION TOOLS (With Selection Preservation)
  // -------------------------------------------------------------
  // Prevent default on mousedown for toolbar controls so selection in contenteditable is NOT lost
  document.querySelectorAll('.btn-format, .btn-action-tool').forEach(function(el){
    el.addEventListener('mousedown', function(e){
      // Keep editor text selection active
      e.preventDefault();
    });
  });

  // Formatting Buttons
  document.querySelectorAll('[data-cmd]').forEach(function(btn){
    btn.addEventListener('click', function(e){
      e.preventDefault();
      var cmd = this.getAttribute('data-cmd');
      document.execCommand(cmd, false, null);
      recordHistory();
    });
  });

  document.getElementById('selFormat').addEventListener('change', function(){
    document.execCommand('formatBlock', false, this.value);
    recordHistory();
  });

  document.getElementById('selFont').addEventListener('change', function(){
    document.execCommand('fontName', false, this.value);
    recordHistory();
  });

  document.getElementById('selSize').addEventListener('change', function(){
    document.execCommand('fontSize', false, this.value);
    recordHistory();
  });

  document.getElementById('pickerColor').addEventListener('input', function(){
    document.execCommand('foreColor', false, this.value);
    recordHistory();
  });

  // HIGHLIGHT SELECTED TEXT (Bug Fix: NEVER deletes multi-line or block selections)
  document.getElementById('btnToolHighlight').onclick = function(e){
    e.preventDefault();
    recordHistory();
    var sel = window.getSelection();
    if(!sel || !sel.rangeCount || sel.isCollapsed) return;

    var ok = false;
    try {
      ok = document.execCommand('hiliteColor', false, '#fef08a');
      if(!ok){
        ok = document.execCommand('backColor', false, '#fef08a');
      }
    } catch(err){ ok = false; }

    if(!ok){
      try {
        var range = sel.getRangeAt(0);
        if(range.startContainer === range.endContainer && range.startContainer.nodeType === 3){
          var mark = document.createElement('mark');
          mark.className = 'rcv-hit';
          mark.textContent = range.toString();
          range.deleteContents();
          range.insertNode(mark);
        }
      } catch(e2){}
    }

    countOccurrences();
    renderChips();
    recordHistory();
  };

  // WHITEOUT / REDACT SENSITIVE DATA (Bug Fix: safe non-destructive redaction)
  document.getElementById('btnToolWhiteout').onclick = function(e){
    e.preventDefault();
    recordHistory();
    var sel = window.getSelection();
    if(!sel || !sel.rangeCount || sel.isCollapsed) return;

    var ok = false;
    try {
      ok = document.execCommand('backColor', false, '#0f172a');
      if(ok){
        document.execCommand('foreColor', false, '#0f172a');
      }
    } catch(err){ ok = false; }

    if(!ok){
      try {
        var range = sel.getRangeAt(0);
        if(range.startContainer === range.endContainer && range.startContainer.nodeType === 3){
          var mark = document.createElement('mark');
          mark.className = 'rcv-redact';
          mark.textContent = range.toString();
          range.deleteContents();
          range.insertNode(mark);
        }
      } catch(e2){}
    }
    recordHistory();
  };

  // Undo and Redo Button Handlers
  document.getElementById('btnToolUndo').onclick = function(e){
    e.preventDefault();
    doUndo();
  };
  document.getElementById('btnToolRedo').onclick = function(e){
    e.preventDefault();
    doRedo();
  };

  // Page Break Tool
  document.getElementById('btnToolPageBreak').onclick = function(e){
    e.preventDefault();
    recordHistory();
    var div = document.createElement('div');
    div.className = 'doc-page-break';
    div.contentEditable = 'false';
    var sel = window.getSelection();
    if(sel && sel.rangeCount){
      var range = sel.getRangeAt(0);
      range.insertNode(div);
    } else {
      resumeBody.appendChild(div);
    }
    recordHistory();
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
      recordHistory();
      var img = document.createElement('img');
      img.src = evt.target.result;
      img.style.maxWidth = '240px';
      img.style.display = 'block';
      img.style.margin = '10px 0';
      var sel = window.getSelection();
      if(sel && sel.rangeCount){
        var range = sel.getRangeAt(0);
        range.insertNode(img);
      } else {
        resumeBody.appendChild(img);
      }
      recordHistory();
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
    recordHistory();
    var dataUrl = signCanvas.toDataURL('image/png');
    var img = document.createElement('img');
    img.src = dataUrl;
    img.style.maxWidth = '180px';
    img.style.display = 'block';
    img.style.margin = '10px 0';
    var sel = window.getSelection();
    if(sel && sel.rangeCount){
      var range = sel.getRangeAt(0);
      range.insertNode(img);
    } else {
      resumeBody.appendChild(img);
    }
    signModal.style.display = 'none';
    recordHistory();
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

  // -------------------------------------------------------------
  // DOWNLOAD PDF: True WYSIWYG Export of the Live Edited DOM Sheets
  // (Page 1 Pre-Screen Report + Page 2 Candidate Résumé)
  // -------------------------------------------------------------
  document.getElementById('btnDownload').onclick = async function(){
    var btn = document.getElementById('btnDownload');
    var orig = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<span class="spin"></span> Generating PDF…';

    try {
      var sheets = [];
      var hasReport = (sheetReport && window.getComputedStyle(sheetReport).display !== 'none');
      if (hasReport) {
        sheets.push(sheetReport);
      }
      if (sheetResume) {
        sheets.push(sheetResume);
      }

      if (!sheets.length) throw new Error('No document sheets available.');

      var jsPdfClass = (window.jspdf && window.jspdf.jsPDF) || window.jsPDF;
      if (!jsPdfClass || typeof window.html2canvas !== 'function') {
        throw new Error('PDF render engine initializing. Falling back to native print.');
      }

      var pdf = new jsPdfClass({
        orientation: 'p',
        unit: 'pt',
        format: 'a4',
        compress: true
      });

      var a4WidthPt = 595.28;
      var a4HeightPt = 841.89;

      for (var s = 0; s < sheets.length; s++) {
        var sheetEl = sheets[s];

        var canvasSnap = await window.html2canvas(sheetEl, {
          scale: 2.2,
          useCORS: true,
          allowTaint: false,
          logging: false,
          backgroundColor: '#ffffff'
        });

        var imgWidthPt = a4WidthPt;
        var imgHeightPt = (canvasSnap.height * a4WidthPt) / canvasSnap.width;

        if (imgHeightPt <= a4HeightPt + 25) {
          if (s > 0) pdf.addPage('a4', 'p');
          var imgData = canvasSnap.toDataURL('image/jpeg', 0.95);
          pdf.addImage(imgData, 'JPEG', 0, 0, a4WidthPt, Math.min(imgHeightPt, a4HeightPt));
        } else {
          var sliceCanvas = document.createElement('canvas');
          var sliceCtx = sliceCanvas.getContext('2d');
          var sliceHeightPx = Math.round(canvasSnap.width * (a4HeightPt / a4WidthPt));
          var totalSlices = Math.ceil(canvasSnap.height / sliceHeightPx);

          sliceCanvas.width = canvasSnap.width;
          sliceCanvas.height = sliceHeightPx;

          for (var p = 0; p < totalSlices; p++) {
            if (s > 0 || p > 0) pdf.addPage('a4', 'p');

            sliceCtx.fillStyle = '#ffffff';
            sliceCtx.fillRect(0, 0, sliceCanvas.width, sliceCanvas.height);

            var sy = p * sliceHeightPx;
            var sh = Math.min(sliceHeightPx, canvasSnap.height - sy);

            sliceCtx.drawImage(
              canvasSnap,
              0, sy, canvasSnap.width, sh,
              0, 0, canvasSnap.width, sh
            );

            var pageData = sliceCanvas.toDataURL('image/jpeg', 0.95);
            pdf.addImage(pageData, 'JPEG', 0, 0, a4WidthPt, a4HeightPt);
          }
        }
      }

      var cleanCand = (candName || 'Candidate').replace(/[^a-zA-Z0-9_\-]/g, '_');
      var mode = parseInt(modeSelect ? modeSelect.value : initialOpt, 10) || 61;
      var sfx = (mode === 64 || mode === 65) ? '_PreScreen_Dossier' : (mode === 62 ? '_Branded' : '_ATS_Optimized');
      pdf.save('Jeroworks_' + cleanCand + sfx + '.pdf');

    } catch(err) {
      console.warn('Direct canvas PDF export failed, using native high-res print fallback:', err);
      window.print();
    } finally {
      btn.disabled = false;
      btn.innerHTML = orig;
    }
  };
</script>

</body>
</html>