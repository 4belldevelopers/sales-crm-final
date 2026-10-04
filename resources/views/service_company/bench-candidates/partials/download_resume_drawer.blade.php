{{-- Dedicated Download Resume Center Drawer for In-House Bench Talent (Engine 1-B) --}}
@include('service_company.bench-candidates.partials._bench_download_resume_engine')

@php
    $cand = $cand ?? $candidate ?? null;
    $companyName = $companyName ?? (auth()->user()->company->company_name ?? 'Hiring Partner');
    $companyLogo = $companyLogo ?? '';
    $candId = (int) ($cand->id ?? 0);
    $candName = $cand->full_name ?? 'In-House Candidate';
    $hasResume = !empty($cand->has_resume);
    $resumeExt = strtoupper($cand->resume_extension ?? 'PDF');
@endphp

<div id="bench_download_resume_container" class="bench-dl-drawer-wrap">
  <style>
    #bench_download_resume_container {
      font-family: 'Poppins', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif !important;
      color: #1e293b;
      display: flex;
      flex-direction: column;
      height: 100vh;
      max-height: 100vh;
      background: #f8fafc;
      position: relative;
      overflow: hidden;
    }
    #bench_download_resume_container * {
      box-sizing: border-box;
      font-family: inherit;
    }

    /* ═══════════════════════════════════════════════════════════════════════════
       STICKY HEADER
       ═══════════════════════════════════════════════════════════════════════════ */
    .bdl-header {
      flex-shrink: 0;
      background: #ffffff;
      border-bottom: 1px solid #e2e8f0;
      padding: 14px 20px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 12px;
      box-shadow: 0 1px 3px rgba(0,0,0,0.03);
      z-index: 40;
    }
    .bdl-header-left {
      display: flex;
      align-items: center;
      gap: 12px;
    }
    .bdl-header-icon {
      width: 38px;
      height: 38px;
      border-radius: 9px;
      background: #eff6ff;
      color: #2563eb;
      display: flex;
      align-items: center;
      justify-content: center;
      flex-shrink: 0;
      border: 1px solid #dbeafe;
    }
    .bdl-header-icon svg {
      width: 20px;
      height: 20px;
    }
    .bdl-header-title {
      font-size: 15px;
      font-weight: 700;
      color: #0f172a;
      line-height: 1.2;
      margin: 0;
    }
    .bdl-header-subtitle {
      font-size: 11.5px;
      color: #64748b;
      margin: 2px 0 0 0;
      display: flex;
      align-items: center;
      gap: 6px;
    }
    .bdl-badge-bench {
      background: #e0f2fe;
      color: #0284c7;
      font-size: 10px;
      font-weight: 600;
      padding: 1px 7px;
      border-radius: 4px;
      text-transform: uppercase;
      letter-spacing: 0.3px;
    }
    .bdl-header-actions {
      display: flex;
      align-items: center;
      gap: 10px;
    }
    .bdl-btn-close {
      background: transparent;
      border: 1px solid #e2e8f0;
      border-radius: 7px;
      width: 34px;
      height: 34px;
      display: flex;
      align-items: center;
      justify-content: center;
      color: #64748b;
      cursor: pointer;
      transition: all 0.15s ease;
    }
    .bdl-btn-close:hover {
      background: #f1f5f9;
      color: #0f172a;
      border-color: #cbd5e1;
    }

    /* ═══════════════════════════════════════════════════════════════════════════
       SCROLLABLE DRAWER BODY
       ═══════════════════════════════════════════════════════════════════════════ */
    .bdl-body {
      flex: 1 1 auto;
      overflow-y: auto;
      padding: 16px 20px 24px 20px;
      scrollbar-gutter: stable;
    }

    /* Section Cluster Headers */
    .bdl-cluster-header {
      font-size: 11px;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 0.6px;
      margin: 22px 0 10px 0;
      display: flex;
      align-items: center;
      gap: 9px;
    }
    .bdl-cluster-header:first-of-type {
      margin-top: 4px;
    }
    .bdl-cluster-header::after {
      content: '';
      flex: 1;
      height: 1px;
      background: #e2e8f0;
    }
    .bdl-cluster-pill {
      font-size: 10px;
      font-weight: 700;
      padding: 2px 8px;
      border-radius: 4px;
      letter-spacing: 0.4px;
    }

    /* Formats Grid */
    .bdl-format-list {
      display: flex;
      flex-direction: column;
      gap: 8px;
    }
    .bdl-format-card {
      background: #ffffff;
      border: 1px solid #e2e8f0;
      border-radius: 9px;
      padding: 11px 14px;
      cursor: pointer;
      display: flex;
      align-items: flex-start;
      gap: 12px;
      transition: all 0.15s ease;
      position: relative;
    }
    .bdl-radio-wrap {
      margin-top: 3px;
      display: flex;
      align-items: center;
      justify-content: center;
    }
    .bdl-radio-wrap input[type="radio"] {
      cursor: pointer;
      width: 15px;
      height: 15px;
      margin: 0;
    }
    .bdl-card-icon {
      width: 32px;
      height: 32px;
      border-radius: 7px;
      display: flex;
      align-items: center;
      justify-content: center;
      flex-shrink: 0;
      margin-top: 1px;
      transition: all 0.15s ease;
    }
    .bdl-card-icon svg {
      width: 17px;
      height: 17px;
    }
    .bdl-card-body {
      flex: 1;
      min-width: 0;
    }
    .bdl-card-top {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 8px;
      flex-wrap: wrap;
    }
    .bdl-card-title {
      font-size: 13px;
      font-weight: 600;
      color: #0f172a;
      margin: 0;
      line-height: 1.3;
    }
    .bdl-card-desc {
      font-size: 11.5px;
      color: #64748b;
      margin: 3px 0 0 0;
      line-height: 1.35;
    }
    .bdl-badges {
      display: flex;
      align-items: center;
      gap: 6px;
    }
    .bdl-coin-tag {
      font-size: 11px;
      font-weight: 600;
      color: #64748b;
      background: #f1f5f9;
      padding: 1px 6px;
      border-radius: 4px;
    }

    /* ═══════════════════════════════════════════════════════════════════════════
       CLUSTER COLOR SCHEMES (Visual Differentiation)
       ═══════════════════════════════════════════════════════════════════════════ */

    /* Cluster 1: Standard Resumes (Sky Blue / Slate) */
    .bdl-cluster-sky .bdl-cluster-header {
      color: #0369a1;
    }
    .bdl-cluster-sky .bdl-cluster-pill {
      background: #e0f2fe;
      color: #0369a1;
      border: 1px solid #bae6fd;
    }
    .bdl-cluster-sky .bdl-card-icon {
      background: #f0f9ff;
      color: #0284c7;
      border: 1px solid #e0f2fe;
    }
    .bdl-cluster-sky .bdl-format-card:hover {
      border-color: #7dd3fc;
      background: #f8fafc;
    }
    .bdl-cluster-sky .bdl-format-card.selected {
      border-color: #0284c7;
      background: #f0f9ff;
      box-shadow: 0 0 0 1.5px #0284c7;
    }
    .bdl-cluster-sky .bdl-format-card.selected .bdl-card-icon {
      background: #0284c7;
      color: #ffffff;
      border-color: #0284c7;
    }
    .bdl-cluster-sky input[type="radio"] {
      accent-color: #0284c7;
    }

    /* Cluster 2: In-House Evaluation Report (Emerald / Forest Green) */
    .bdl-cluster-emerald .bdl-cluster-header {
      color: #047857;
    }
    .bdl-cluster-emerald .bdl-cluster-pill {
      background: #d1fae5;
      color: #065f46;
      border: 1px solid #a7f3d0;
    }
    .bdl-cluster-emerald .bdl-card-icon {
      background: #ecfdf5;
      color: #059669;
      border: 1px solid #d1fae5;
    }
    .bdl-cluster-emerald .bdl-format-card:hover {
      border-color: #6ee7b7;
      background: #f8fafc;
    }
    .bdl-cluster-emerald .bdl-format-card.selected {
      border-color: #059669;
      background: #ecfdf5;
      box-shadow: 0 0 0 1.5px #059669;
    }
    .bdl-cluster-emerald .bdl-format-card.selected .bdl-card-icon {
      background: #059669;
      color: #ffffff;
      border-color: #059669;
    }
    .bdl-cluster-emerald input[type="radio"] {
      accent-color: #059669;
    }
    .bdl-badge-popular {
      background: #d1fae5;
      color: #065f46;
      border: 1px solid #a7f3d0;
      font-size: 9.5px;
      font-weight: 700;
      padding: 1px 6px;
      border-radius: 4px;
      letter-spacing: 0.3px;
    }

    /* Cluster 3: Recruiter Packages (Royal Indigo / Violet) */
    .bdl-cluster-indigo .bdl-cluster-header {
      color: #4338ca;
    }
    .bdl-cluster-indigo .bdl-cluster-pill {
      background: #e0e7ff;
      color: #3730a3;
      border: 1px solid #c7d2fe;
    }
    .bdl-cluster-indigo .bdl-card-icon {
      background: #eef2ff;
      color: #4f46e5;
      border: 1px solid #e0e7ff;
    }
    .bdl-cluster-indigo .bdl-format-card:hover {
      border-color: #a5b4fc;
      background: #f8fafc;
    }
    .bdl-cluster-indigo .bdl-format-card.selected {
      border-color: #4f46e5;
      background: #eef2ff;
      box-shadow: 0 0 0 1.5px #4f46e5;
    }
    .bdl-cluster-indigo .bdl-format-card.selected .bdl-card-icon {
      background: #4f46e5;
      color: #ffffff;
      border-color: #4f46e5;
    }
    .bdl-cluster-indigo input[type="radio"] {
      accent-color: #4f46e5;
    }
    .bdl-badge-package {
      background: #e0e7ff;
      color: #3730a3;
      border: 1px solid #c7d2fe;
      font-size: 9.5px;
      font-weight: 700;
      padding: 1px 6px;
      border-radius: 4px;
      letter-spacing: 0.3px;
    }

    /* Cluster 4: Highlighted Skill Resumes (Warm Amber / Gold Honey) */
    .bdl-cluster-amber .bdl-cluster-header {
      color: #b45309;
    }
    .bdl-cluster-amber .bdl-cluster-pill {
      background: #fef3c7;
      color: #92400e;
      border: 1px solid #fde68a;
    }
    .bdl-cluster-amber .bdl-card-icon {
      background: #fffbeb;
      color: #d97706;
      border: 1px solid #fef3c7;
    }
    .bdl-cluster-amber .bdl-format-card:hover {
      border-color: #fcd34d;
      background: #f8fafc;
    }
    .bdl-cluster-amber .bdl-format-card.selected {
      border-color: #d97706;
      background: #fffbeb;
      box-shadow: 0 0 0 1.5px #d97706;
    }
    .bdl-cluster-amber .bdl-format-card.selected .bdl-card-icon {
      background: #d97706;
      color: #ffffff;
      border-color: #d97706;
    }
    .bdl-cluster-amber input[type="radio"] {
      accent-color: #d97706;
    }
    .bdl-badge-default {
      background: #fef3c7;
      color: #92400e;
      border: 1px solid #fcd34d;
      font-size: 9.5px;
      font-weight: 700;
      padding: 1px 6px;
      border-radius: 4px;
      letter-spacing: 0.3px;
    }

    /* Cluster 5: Privacy & Anonymization Shields (Crimson Rose / Ruby Red) */
    .bdl-cluster-rose .bdl-cluster-header {
      color: #be123c;
    }
    .bdl-cluster-rose .bdl-cluster-pill {
      background: #ffe4e6;
      color: #9f1239;
      border: 1px solid #fecdd3;
    }
    .bdl-cluster-rose .bdl-card-icon {
      background: #fff1f2;
      color: #e11d48;
      border: 1px solid #ffe4e6;
    }
    .bdl-cluster-rose .bdl-format-card:hover {
      border-color: #fda4af;
      background: #f8fafc;
    }
    .bdl-cluster-rose .bdl-format-card.selected {
      border-color: #e11d48;
      background: #fff1f2;
      box-shadow: 0 0 0 1.5px #e11d48;
    }
    .bdl-cluster-rose .bdl-format-card.selected .bdl-card-icon {
      background: #e11d48;
      color: #ffffff;
      border-color: #e11d48;
    }
    .bdl-cluster-rose input[type="radio"] {
      accent-color: #e11d48;
    }
    .bdl-badge-conf {
      background: #ffe4e6;
      color: #9f1239;
      border: 1px solid #fecdd3;
      font-size: 9.5px;
      font-weight: 700;
      padding: 1px 6px;
      border-radius: 4px;
      letter-spacing: 0.3px;
    }

    /* ═══════════════════════════════════════════════════════════════════════════
       STICKY FOOTER (Fixed at Bottom, Progress & Download Button)
       ═══════════════════════════════════════════════════════════════════════════ */
    .bdl-footer {
      flex-shrink: 0;
      background: #ffffff;
      border-top: 1px solid #e2e8f0;
      padding: 14px 20px;
      box-shadow: 0 -4px 14px rgba(0,0,0,0.05);
      z-index: 50;
      display: flex;
      flex-direction: column;
      gap: 10px;
    }

    /* Selected Format Info / Progress Display */
    .bdl-footer-meta {
      width: 100%;
    }
    .bdl-footer-sel-info {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 8px;
      font-size: 11.5px;
    }
    .bdl-footer-sel-label {
      color: #64748b;
      font-weight: 500;
      flex-shrink: 0;
    }
    .bdl-footer-sel-name {
      color: #0f172a;
      font-weight: 600;
      text-align: right;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
      max-width: 320px;
    }

    /* Progress Display During Compilation */
    .bdl-footer-progress {
      display: flex;
      flex-direction: column;
      gap: 5px;
    }
    .bdl-footer-progress-info {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 8px;
      font-size: 11.5px;
    }
    .bdl-footer-progress-step {
      font-weight: 600;
      color: #1d4ed8;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
    }
    .bdl-footer-progress-pct {
      font-weight: 700;
      color: #1d4ed8;
      font-size: 11px;
    }
    .bdl-footer-progress-track {
      height: 4px;
      width: 100%;
      background: #e2e8f0;
      border-radius: 2px;
      overflow: hidden;
    }
    .bdl-footer-progress-bar {
      height: 100%;
      width: 0%;
      background: linear-gradient(90deg, #2563eb, #06b6d4, #10b981);
      border-radius: 2px;
      transition: width 0.2s ease;
    }
    .bdl-footer-progress-detail {
      font-size: 10.5px;
      color: #64748b;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
    }

    /* Primary Action Download Button */
    .bdl-footer-action {
      width: 100%;
    }
    .bdl-btn-footer-download {
      width: 100%;
      background: #2563eb;
      color: #ffffff;
      border: 1px solid #1d4ed8;
      border-radius: 8px;
      padding: 11px 18px;
      font-size: 13.5px;
      font-weight: 600;
      cursor: pointer;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
      transition: all 0.15s ease;
      box-shadow: 0 1px 3px rgba(37,99,235,0.2);
    }
    .bdl-btn-footer-download:hover:not(:disabled) {
      background: #1d4ed8;
      box-shadow: 0 3px 8px rgba(37,99,235,0.35);
    }
    .bdl-btn-footer-download:disabled {
      opacity: 0.75;
      cursor: not-allowed;
      background: #3b82f6;
    }
    .bdl-btn-spinner {
      width: 16px;
      height: 16px;
      border: 2px solid rgba(255,255,255,0.4);
      border-top-color: #ffffff;
      border-radius: 50%;
      animation: bdlSpin 0.7s linear infinite;
      flex-shrink: 0;
    }
    @keyframes bdlSpin {
      to { transform: rotate(360deg); }
    }
  </style>

  {{-- STICKY HEADER --}}
  <div class="bdl-header">
    <div class="bdl-header-left">
      <div class="bdl-header-icon" aria-hidden="true">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
          <polyline points="7 10 12 15 17 10"/>
          <line x1="12" y1="15" x2="12" y2="3"/>
        </svg>
      </div>
      <div>
        <h2 class="bdl-header-title">Download Resume Center</h2>
        <div class="bdl-header-subtitle">
          <span class="bdl-badge-bench">In-House Talent</span>
          <span>Select presentation format to download</span>
        </div>
      </div>
    </div>

    <div class="bdl-header-actions">
      <button type="button" class="bdl-btn-close" id="btnBdlCloseDrawer" aria-label="Close drawer">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:16px;height:16px;">
          <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
        </svg>
      </button>
    </div>
  </div>

  {{-- SCROLLABLE DRAWER BODY (Candidate quick card removed) --}}
  <div class="bdl-body">

    {{-- FORMAT SELECTION OPTIONS (16 ENTERPRISE FORMATS IN 5 COLOR-CODED CLUSTERS) --}}

    {{-- CLUSTER 1: STANDARD RESUMES (SKY BLUE THEME) --}}
    <div class="bdl-cluster-sky">
      <div class="bdl-cluster-header">
        <span>1. Standard Resumes</span>
        <span class="bdl-cluster-pill">Standard</span>
      </div>
      <div class="bdl-format-list">

        {{-- Format 1: Original Resume --}}
        <label class="bdl-format-card" data-format="1">
          <div class="bdl-radio-wrap">
            <input type="radio" name="bench_dl_format" value="1">
          </div>
          <div class="bdl-card-icon" aria-hidden="true">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
              <polyline points="14 2 14 8 20 8"/>
              <line x1="16" y1="13" x2="8" y2="13"/>
              <line x1="16" y1="17" x2="8" y2="17"/>
              <polyline points="10 9 9 9 8 9"/>
            </svg>
          </div>
          <div class="bdl-card-body">
            <div class="bdl-card-top">
              <h4 class="bdl-card-title">Original Candidate Resume</h4>
              <span class="bdl-coin-tag">1 Coin</span>
            </div>
            <p class="bdl-card-desc">Downloads the original raw resume document without header branding or modifications.</p>
          </div>
        </label>

        {{-- Format 41: Resume + Company Branding --}}
        <label class="bdl-format-card" data-format="41">
          <div class="bdl-radio-wrap">
            <input type="radio" name="bench_dl_format" value="41">
          </div>
          <div class="bdl-card-icon" aria-hidden="true">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <rect x="2" y="3" width="20" height="14" rx="2" ry="2"/>
              <line x1="8" y1="21" x2="16" y2="21"/>
              <line x1="12" y1="17" x2="12" y2="21"/>
            </svg>
          </div>
          <div class="bdl-card-body">
            <div class="bdl-card-top">
              <h4 class="bdl-card-title">Resume + Company Branding</h4>
              <span class="bdl-coin-tag">2 Coins</span>
            </div>
            <p class="bdl-card-desc">Injects your company logo and verified in-house presentation banner at the top of page 1.</p>
          </div>
        </label>

      </div>
    </div>

    {{-- CLUSTER 2: IN-HOUSE EVALUATION REPORT (EMERALD THEME) --}}
    <div class="bdl-cluster-emerald">
      <div class="bdl-cluster-header">
        <span>2. In-House Evaluation Report</span>
        <span class="bdl-cluster-pill">Evaluation Dossier</span>
      </div>
      <div class="bdl-format-list">

        {{-- Format 5: Stand-alone In-House Evaluation Report --}}
        <label class="bdl-format-card" data-format="5">
          <div class="bdl-radio-wrap">
            <input type="radio" name="bench_dl_format" value="5">
          </div>
          <div class="bdl-card-icon" aria-hidden="true">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <circle cx="12" cy="8" r="7"/>
              <polyline points="8.21 13.89 7 23 12 20 17 23 15.79 13.88"/>
            </svg>
          </div>
          <div class="bdl-card-body">
            <div class="bdl-card-top">
              <h4 class="bdl-card-title">In-House Evaluation Report</h4>
              <div class="bdl-badges">
                <span class="bdl-badge-popular">POPULAR ★</span>
                <span class="bdl-coin-tag">1 Coin</span>
              </div>
            </div>
            <p class="bdl-card-desc">Stand-alone executive PDF dossier featuring evaluated skills matrix, gold star ratings, RM comments, and availability.</p>
          </div>
        </label>

      </div>
    </div>

    {{-- CLUSTER 3: RECRUITER PACKAGES (ROYAL INDIGO THEME) --}}
    <div class="bdl-cluster-indigo">
      <div class="bdl-cluster-header">
        <span>3. Recruiter Packages</span>
        <span class="bdl-cluster-pill">Recruiter Bundles</span>
      </div>
      <div class="bdl-format-list">

        {{-- Format 42: Recruiter Package + Company Branding --}}
        <label class="bdl-format-card" data-format="42">
          <div class="bdl-radio-wrap">
            <input type="radio" name="bench_dl_format" value="42">
          </div>
          <div class="bdl-card-icon" aria-hidden="true">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <polygon points="12 2 2 7 12 12 22 7 12 2"/>
              <polyline points="2 17 12 22 22 17"/>
              <polyline points="2 12 12 17 22 12"/>
            </svg>
          </div>
          <div class="bdl-card-body">
            <div class="bdl-card-top">
              <h4 class="bdl-card-title">Recruiter Package + Company Branding</h4>
              <div class="bdl-badges">
                <span class="bdl-badge-package">BUNDLE</span>
                <span class="bdl-coin-tag">3 Coins</span>
              </div>
            </div>
            <p class="bdl-card-desc">Concatenates the In-House Evaluation Report dossier with the company-branded candidate resume.</p>
          </div>
        </label>

        {{-- Format 43: Recruiter Package (Unbranded) --}}
        <label class="bdl-format-card" data-format="43">
          <div class="bdl-radio-wrap">
            <input type="radio" name="bench_dl_format" value="43">
          </div>
          <div class="bdl-card-icon" aria-hidden="true">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/>
              <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/>
            </svg>
          </div>
          <div class="bdl-card-body">
            <div class="bdl-card-top">
              <h4 class="bdl-card-title">Recruiter Package (Unbranded)</h4>
              <span class="bdl-coin-tag">2 Coins</span>
            </div>
            <p class="bdl-card-desc">Neutral evaluation dossier combined with original candidate resume without any company logo.</p>
          </div>
        </label>

      </div>
    </div>

    {{-- CLUSTER 4: HIGHLIGHTED SKILL RESUMES (WARM AMBER / GOLD THEME) --}}
    <div class="bdl-cluster-amber">
      <div class="bdl-cluster-header">
        <span>4. Highlighted Skill Resumes</span>
        <span class="bdl-cluster-pill">Skill Intelligence</span>
      </div>
      <div class="bdl-format-list">

        {{-- Format 44: Resume + Highlighted Keywords --}}
        <label class="bdl-format-card" data-format="44">
          <div class="bdl-radio-wrap">
            <input type="radio" name="bench_dl_format" value="44">
          </div>
          <div class="bdl-card-icon" aria-hidden="true">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="m9 11-6 6v3h3l6-6"/>
              <path d="m22 7-3-3a2.828 2.828 0 0 0-4 0l-1 1 7 7 1-1a2.828 2.828 0 0 0 0-4z"/>
            </svg>
          </div>
          <div class="bdl-card-body">
            <div class="bdl-card-top">
              <h4 class="bdl-card-title">Resume + Highlighted Keywords</h4>
              <span class="bdl-coin-tag">2 Coins</span>
            </div>
            <p class="bdl-card-desc">Applies sub-pixel vector highlights across the resume text matching candidate's verified skills.</p>
          </div>
        </label>

        {{-- Format 45: Resume + Highlighted Keywords + Company Branding --}}
        <label class="bdl-format-card" data-format="45">
          <div class="bdl-radio-wrap">
            <input type="radio" name="bench_dl_format" value="45">
          </div>
          <div class="bdl-card-icon" aria-hidden="true">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
            </svg>
          </div>
          <div class="bdl-card-body">
            <div class="bdl-card-top">
              <h4 class="bdl-card-title">Resume + Highlighted Keywords + Company Branding</h4>
              <span class="bdl-coin-tag">3 Coins</span>
            </div>
            <p class="bdl-card-desc">Vector highlighted skill keywords with your corporate branding banner stamped on page 1.</p>
          </div>
        </label>

        {{-- Format 46 (DEFAULT ★): In-House Evaluation Report + Resume + Highlighted Keywords + Branding --}}
        <label class="bdl-format-card selected" data-format="46">
          <div class="bdl-radio-wrap">
            <input type="radio" name="bench_dl_format" value="46" checked>
          </div>
          <div class="bdl-card-icon" aria-hidden="true">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
            </svg>
          </div>
          <div class="bdl-card-body">
            <div class="bdl-card-top">
              <h4 class="bdl-card-title">In-House Evaluation Report + Resume + Highlighted Keywords + Branding</h4>
              <div class="bdl-badges">
                <span class="bdl-badge-default">DEFAULT ★</span>
                <span class="bdl-coin-tag">3 Coins</span>
              </div>
            </div>
            <p class="bdl-card-desc">The authoritative hiring presentation: Full evaluation dossier + vector highlighted resume + corporate branding.</p>
          </div>
        </label>

        {{-- Format 47: Recruiter Package + Highlighted Keywords (Unbranded) --}}
        <label class="bdl-format-card" data-format="47">
          <div class="bdl-radio-wrap">
            <input type="radio" name="bench_dl_format" value="47">
          </div>
          <div class="bdl-card-icon" aria-hidden="true">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <line x1="4" y1="9" x2="20" y2="9"/>
              <line x1="4" y1="15" x2="20" y2="15"/>
              <line x1="10" y1="3" x2="8" y2="21"/>
              <line x1="16" y1="3" x2="14" y2="21"/>
            </svg>
          </div>
          <div class="bdl-card-body">
            <div class="bdl-card-top">
              <h4 class="bdl-card-title">Recruiter Package + Highlighted Keywords (Unbranded)</h4>
              <span class="bdl-coin-tag">2 Coins</span>
            </div>
            <p class="bdl-card-desc">Neutral evaluation dossier with vector highlighted skill keywords and zero branding.</p>
          </div>
        </label>

      </div>
    </div>

    {{-- CLUSTER 5: PRIVACY & ANONYMIZATION SHIELDS (CRIMSON ROSE THEME) --}}
    <div class="bdl-cluster-rose">
      <div class="bdl-cluster-header">
        <span>5. Privacy & Anonymization Shields</span>
        <span class="bdl-cluster-pill">Privacy Shield</span>
      </div>
      <div class="bdl-format-list">

        {{-- Format 48: Hide Contact Details --}}
        <label class="bdl-format-card" data-format="48">
          <div class="bdl-radio-wrap">
            <input type="radio" name="bench_dl_format" value="48">
          </div>
          <div class="bdl-card-icon" aria-hidden="true">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
            </svg>
          </div>
          <div class="bdl-card-body">
            <div class="bdl-card-top">
              <h4 class="bdl-card-title">Hide Contact Details</h4>
              <div class="bdl-badges">
                <span class="bdl-badge-conf">PII SHIELD</span>
                <span class="bdl-coin-tag">2 Coins</span>
              </div>
            </div>
            <p class="bdl-card-desc">Redacts candidate phone numbers, primary & secondary email addresses, and external web links.</p>
          </div>
        </label>

        {{-- Format 49: Hide Candidate Name & Contact Details (Blind Profile) --}}
        <label class="bdl-format-card" data-format="49">
          <div class="bdl-radio-wrap">
            <input type="radio" name="bench_dl_format" value="49">
          </div>
          <div class="bdl-card-icon" aria-hidden="true">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
              <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
            </svg>
          </div>
          <div class="bdl-card-body">
            <div class="bdl-card-top">
              <h4 class="bdl-card-title">Hide Candidate Name & Contact Details (Blind Profile)</h4>
              <div class="bdl-badges">
                <span class="bdl-badge-conf">BLIND PROFILE</span>
                <span class="bdl-coin-tag">2 Coins</span>
              </div>
            </div>
            <p class="bdl-card-desc">100% confidential anti-poaching format: Redacts full candidate name, contact numbers, email, and social profiles.</p>
          </div>
        </label>

        {{-- Format 50: Hide Contact Details + Company Branding --}}
        <label class="bdl-format-card" data-format="50">
          <div class="bdl-radio-wrap">
            <input type="radio" name="bench_dl_format" value="50">
          </div>
          <div class="bdl-card-icon" aria-hidden="true">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
              <line x1="12" y1="8" x2="12" y2="12"/>
              <line x1="12" y1="16" x2="12.01" y2="16"/>
            </svg>
          </div>
          <div class="bdl-card-body">
            <div class="bdl-card-top">
              <h4 class="bdl-card-title">Hide Contact Details + Company Branding</h4>
              <span class="bdl-coin-tag">3 Coins</span>
            </div>
            <p class="bdl-card-desc">Contact channels redacted on high-resolution canvas with company branding stamped on header.</p>
          </div>
        </label>

        {{-- Format 51: Hide Contact Details + Evaluation Report + Company Branding --}}
        <label class="bdl-format-card" data-format="51">
          <div class="bdl-radio-wrap">
            <input type="radio" name="bench_dl_format" value="51">
          </div>
          <div class="bdl-card-icon" aria-hidden="true">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
              <polyline points="9 12 11 14 15 10"/>
            </svg>
          </div>
          <div class="bdl-card-body">
            <div class="bdl-card-top">
              <h4 class="bdl-card-title">Hide Contact Details + Evaluation Report + Company Branding</h4>
              <span class="bdl-coin-tag">3 Coins</span>
            </div>
            <p class="bdl-card-desc">Evaluation report dossier merged with contact-redacted, company-branded candidate resume.</p>
          </div>
        </label>

        {{-- Format 52: Hide Contact Details + Evaluation Report (Unbranded) --}}
        <label class="bdl-format-card" data-format="52">
          <div class="bdl-radio-wrap">
            <input type="radio" name="bench_dl_format" value="52">
          </div>
          <div class="bdl-card-icon" aria-hidden="true">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
              <circle cx="12" cy="14" r="3"/>
            </svg>
          </div>
          <div class="bdl-card-body">
            <div class="bdl-card-top">
              <h4 class="bdl-card-title">Hide Contact Details + Evaluation Report (Unbranded)</h4>
              <span class="bdl-coin-tag">2 Coins</span>
            </div>
            <p class="bdl-card-desc">Neutral evaluation dossier combined with contact-redacted resume with zero company branding.</p>
          </div>
        </label>

        {{-- Format 53: Full Privacy: Hide Contact + Dossier + Keywords + Branding --}}
        <label class="bdl-format-card" data-format="53">
          <div class="bdl-radio-wrap">
            <input type="radio" name="bench_dl_format" value="53">
          </div>
          <div class="bdl-card-icon" aria-hidden="true">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
              <path d="M12 8v4"/>
              <path d="M12 16h.01"/>
            </svg>
          </div>
          <div class="bdl-card-body">
            <div class="bdl-card-top">
              <h4 class="bdl-card-title">Full Privacy: Hide Contact + Dossier + Keywords + Branding</h4>
              <div class="bdl-badges">
                <span class="bdl-badge-conf">ALL-IN-ONE</span>
                <span class="bdl-coin-tag">4 Coins</span>
              </div>
            </div>
            <p class="bdl-card-desc">Complete corporate suite: Evaluated skills highlighted first, PII redacted, branded, and merged with dossier.</p>
          </div>
        </label>

        {{-- Format 54: Full Privacy (Unbranded): Hide Contact + Dossier + Keywords --}}
        <label class="bdl-format-card" data-format="54">
          <div class="bdl-radio-wrap">
            <input type="radio" name="bench_dl_format" value="54">
          </div>
          <div class="bdl-card-icon" aria-hidden="true">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
              <circle cx="12" cy="16" r="1"/>
              <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
            </svg>
          </div>
          <div class="bdl-card-body">
            <div class="bdl-card-top">
              <h4 class="bdl-card-title">Full Privacy (Unbranded): Hide Contact + Dossier + Keywords</h4>
              <span class="bdl-coin-tag">3 Coins</span>
            </div>
            <p class="bdl-card-desc">Neutral confidential package: Skills highlighted, contact channels redacted, merged with neutral dossier.</p>
          </div>
        </label>

      </div>
    </div>

  </div>

  {{-- STICKY FOOTER (Fixed at Bottom with Progress & Download Resume Button) --}}
  <div class="bdl-footer">
    {{-- FOOTER META (Shows Selection or Live Progress) --}}
    <div class="bdl-footer-meta">
      {{-- IDLE SELECTION INFO --}}
      <div class="bdl-footer-sel-info" id="bdlFooterSelInfo">
        <span class="bdl-footer-sel-label">Selected Format:</span>
        <span class="bdl-footer-sel-name" id="bdlFooterSelName">In-House Evaluation Report + Resume + Highlighted Keywords + Branding</span>
      </div>

      {{-- LIVE COMPILATION PROGRESS BAR --}}
      <div class="bdl-footer-progress" id="bdlFooterProgress" style="display: none;">
        <div class="bdl-footer-progress-info">
          <span class="bdl-footer-progress-step" id="bdlStatusStep">Compiling candidate presentation...</span>
          <span class="bdl-footer-progress-pct" id="bdlStatusPct">0%</span>
        </div>
        <div class="bdl-footer-progress-track">
          <div class="bdl-footer-progress-bar" id="bdlProgressBar"></div>
        </div>
        <span class="bdl-footer-progress-detail" id="bdlStatusDetail">Client WASM engine processing document...</span>
      </div>
    </div>

    {{-- STICKY DOWNLOAD BUTTON --}}
    <div class="bdl-footer-action">
      <button type="button" class="bdl-btn-footer-download" id="btnBdlTriggerDownload">
        <span class="bdl-btn-spinner" id="bdlBtnSpinner" style="display: none;"></span>
        <svg id="bdlBtnIcon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:16px;height:16px;">
          <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
          <polyline points="7 10 12 15 17 10"/>
          <line x1="12" y1="15" x2="12" y2="3"/>
        </svg>
        <span id="lblBdlDownloadAction">Download Resume</span>
      </button>
    </div>
  </div>
</div>

<script>
(function() {
  'use strict';

  var candidateData = @json($cand);
  var companyData = {
    companyName: @json($companyName),
    companyLogo: @json($companyLogo)
  };

  // Pre-warm candidate resume in browser memory immediately upon opening drawer
  if (window.AgyBenchDownloadResumeEngine && typeof window.AgyBenchDownloadResumeEngine.preWarmCandidateResume === 'function') {
    window.AgyBenchDownloadResumeEngine.preWarmCandidateResume(candidateData);
  }

  // Update Footer Selected Format Text
  function updateSelectedFormatText($card) {
    if (!$card || !$card.length) return;
    var title = $card.find('.bdl-card-title').text().trim();
    if (title) {
      $('#bdlFooterSelName').text(title).attr('title', title);
    }
  }

  // Format Card Selection Handler
  $('#bench_download_resume_container .bdl-format-card').off('click').on('click', function(e) {
    if ($(e.target).is('input[type="radio"]')) return;
    var $radio = $(this).find('input[type="radio"]');
    $radio.prop('checked', true).trigger('change');
  });

  $('#bench_download_resume_container input[name="bench_dl_format"]').off('change').on('change', function() {
    $('#bench_download_resume_container .bdl-format-card').removeClass('selected');
    var $selectedCard = $(this).closest('.bdl-format-card');
    $selectedCard.addClass('selected');
    updateSelectedFormatText($selectedCard);
  });

  // Initialize selected format text on load
  updateSelectedFormatText($('#bench_download_resume_container .bdl-format-card.selected'));

  // Drawer Close Button Handler
  $('#btnBdlCloseDrawer').off('click').on('click', function() {
    var drawerEl = document.getElementById('kt_bench_download_resume_drawer');
    if (drawerEl && typeof KTDrawer !== 'undefined') {
      var d = KTDrawer.getInstance(drawerEl);
      if (d) d.hide();
    }
    $('#kt_bench_download_resume_drawer').removeClass('drawer-on');
    $('.drawer-overlay').remove();
  });

  // Sticky Footer Direct Download Trigger
  $('#btnBdlTriggerDownload').off('click').on('click', async function() {
    var $btn = $(this);
    var $lbl = $('#lblBdlDownloadAction');
    var $spinner = $('#bdlBtnSpinner');
    var $icon = $('#bdlBtnIcon');
    var $selInfo = $('#bdlFooterSelInfo');
    var $progress = $('#bdlFooterProgress');
    var $bar = $('#bdlProgressBar');
    var $pct = $('#bdlStatusPct');
    var $step = $('#bdlStatusStep');
    var $detail = $('#bdlStatusDetail');

    var selectedFormat = parseInt($('input[name="bench_dl_format"]:checked').val() || '46', 10);

    // Enter compiling state
    $btn.prop('disabled', true);
    $spinner.show();
    $icon.hide();
    $lbl.text('Download Resume (0%)...');
    $selInfo.hide();
    $progress.show();
    $bar.css('width', '5%');
    $pct.text('5%');
    $step.text('Initializing document compiler...');
    $detail.text('Client WASM engine processing document in browser RAM...');

    function updateProgress(p) {
      if (!p) return;
      var numPct = Math.min(100, Math.max(0, p.percent || 0));
      $bar.css('width', numPct + '%');
      $pct.text(numPct + '%');
      if (p.step) $step.text(p.step);
      if (p.detail) $detail.text(p.detail);
      $lbl.text('Download Resume (' + numPct + '%)...');
    }

    try {
      if (!window.AgyBenchDownloadResumeEngine || typeof window.AgyBenchDownloadResumeEngine.downloadCandidateFormat !== 'function') {
        throw new Error('Download Resume Engine is still initializing. Please try again in a moment.');
      }

      await window.AgyBenchDownloadResumeEngine.downloadCandidateFormat(
        candidateData,
        selectedFormat,
        companyData,
        updateProgress
      );

      $bar.css('width', '100%');
      $pct.text('100%');
      $step.text('Download ready!');
      $lbl.text('Download Resume');

      // Seamlessly reset back to ready state without toaster popup
      setTimeout(function() {
        $progress.hide();
        $selInfo.show();
        $spinner.hide();
        $icon.show();
        $btn.prop('disabled', false);
        $lbl.text('Download Resume');
      }, 1200);

    } catch (err) {
      console.error('[Bench Download Resume Center] Compilation Error:', err);
      $progress.hide();
      $selInfo.show();
      $spinner.hide();
      $icon.show();
      $btn.prop('disabled', false);
      $lbl.text('Download Resume');

      var errMsg = err.message || 'An error occurred during resume compilation.';
      if (typeof Swal !== 'undefined') {
        Swal.fire({
          icon: 'error',
          title: 'Download Failed',
          text: errMsg,
          confirmButtonColor: '#2563eb'
        });
      } else {
        alert(errMsg);
      }
    }
  });

})();
</script>
