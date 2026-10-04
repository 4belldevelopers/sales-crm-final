{{-- Dedicated Download Resume Center Drawer for In-House Bench Talent (Engine 1-B) --}}
@include('service_company.bench-candidates.partials._bench_download_resume_engine')

@php
    $cand = $cand ?? $candidate ?? null;
    $companyName = $companyName ?? (auth()->user()->company->company_name ?? 'Hiring Partner');
    $companyLogo = $companyLogo ?? '';
    $candId = (int) ($cand->id ?? 0);
    $candName = $cand->full_name ?? 'In-House Candidate';
    $candRole = $cand->current_designation ?? 'Consultant';
    $candLoc = $cand->location ?? $cand->city_display ?? '--';
    $candNotice = $cand->notice ?? 'Immediate';
    $candComm = $cand->commercials_formatted ?? 'Competitive';
    $candExp = $cand->experience ?? '--';
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
      height: 100%;
      min-height: 100vh;
      background: #f8fafc;
      position: relative;
    }
    #bench_download_resume_container * {
      box-sizing: border-box;
      font-family: inherit;
    }

    /* Sticky Header */
    .bdl-header {
      position: sticky;
      top: 0;
      z-index: 40;
      background: #ffffff;
      border-bottom: 1px solid #e2e8f0;
      padding: 14px 20px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 12px;
      box-shadow: 0 1px 3px rgba(0,0,0,0.03);
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
    .bdl-btn-primary {
      background: #2563eb;
      color: #ffffff;
      border: 1px solid #1d4ed8;
      border-radius: 7px;
      padding: 8px 16px;
      font-size: 12.5px;
      font-weight: 600;
      cursor: pointer;
      display: inline-flex;
      align-items: center;
      gap: 7px;
      transition: all 0.15s ease;
      white-space: nowrap;
    }
    .bdl-btn-primary:hover:not(:disabled) {
      background: #1d4ed8;
      box-shadow: 0 2px 6px rgba(37,99,235,0.25);
    }
    .bdl-btn-primary:disabled {
      opacity: 0.65;
      cursor: not-allowed;
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

    /* 3px Compilation Baseline Progress Bar */
    .bdl-progress-track {
      height: 3px;
      width: 100%;
      background: #e2e8f0;
      overflow: hidden;
      display: none;
      position: sticky;
      top: 67px;
      z-index: 41;
    }
    .bdl-progress-bar {
      height: 100%;
      width: 0%;
      background: linear-gradient(90deg, #2563eb, #06b6d4, #10b981);
      transition: width 0.25s ease;
    }

    /* Drawer Scrollable Content */
    .bdl-body {
      flex: 1 1 auto;
      overflow-y: auto;
      padding: 18px 22px 40px 22px;
      scrollbar-gutter: stable;
    }

    /* Candidate Quick Profile Card */
    .bdl-cand-card {
      background: #ffffff;
      border: 1px solid #e2e8f0;
      border-radius: 10px;
      padding: 14px 18px;
      margin-bottom: 20px;
      box-shadow: 0 1px 3px rgba(0,0,0,0.02);
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 16px;
    }
    .bdl-cand-info {
      display: flex;
      align-items: center;
      gap: 12px;
    }
    .bdl-cand-avatar {
      width: 44px;
      height: 44px;
      border-radius: 8px;
      background: #f1f5f9;
      color: #2563eb;
      font-size: 15px;
      font-weight: 700;
      display: flex;
      align-items: center;
      justify-content: center;
      border: 1px solid #e2e8f0;
      flex-shrink: 0;
    }
    .bdl-cand-name {
      font-size: 14px;
      font-weight: 700;
      color: #0f172a;
      margin: 0;
    }
    .bdl-cand-role {
      font-size: 12px;
      color: #2563eb;
      font-weight: 500;
      margin: 2px 0 0 0;
    }
    .bdl-cand-meta {
      display: flex;
      align-items: center;
      gap: 12px;
      font-size: 11.5px;
      color: #64748b;
      margin-top: 4px;
      flex-wrap: wrap;
    }
    .bdl-cand-meta span {
      display: inline-flex;
      align-items: center;
      gap: 4px;
    }
    .bdl-cand-status {
      display: flex;
      flex-direction: column;
      align-items: flex-end;
      gap: 4px;
    }
    .bdl-badge-avail {
      background: #dcfce7;
      color: #15803d;
      font-size: 11px;
      font-weight: 600;
      padding: 2px 8px;
      border-radius: 5px;
    }
    .bdl-badge-doc {
      font-size: 10.5px;
      font-weight: 600;
      padding: 2px 7px;
      border-radius: 4px;
      background: #f1f5f9;
      color: #475569;
      border: 1px solid #e2e8f0;
    }

    /* Section Cluster Headers */
    .bdl-cluster-header {
      font-size: 11.5px;
      font-weight: 700;
      color: #475569;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      margin: 20px 0 10px 0;
      display: flex;
      align-items: center;
      gap: 8px;
    }
    .bdl-cluster-header::after {
      content: '';
      flex: 1;
      height: 1px;
      background: #e2e8f0;
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
      border-radius: 8px;
      padding: 11px 14px;
      cursor: pointer;
      display: flex;
      align-items: flex-start;
      gap: 12px;
      transition: all 0.15s ease;
      position: relative;
    }
    .bdl-format-card:hover {
      border-color: #cbd5e1;
      background: #f8fafc;
    }
    .bdl-format-card.selected {
      border-color: #2563eb;
      background: #eff6ff;
      box-shadow: 0 0 0 1px #2563eb;
    }
    .bdl-radio-wrap {
      margin-top: 2px;
    }
    .bdl-radio-wrap input[type="radio"] {
      cursor: pointer;
      width: 15px;
      height: 15px;
      accent-color: #2563eb;
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
      display: flex;
      align-items: center;
      gap: 6px;
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
    .bdl-badge-default {
      background: #fef3c7;
      color: #b45309;
      font-size: 9.5px;
      font-weight: 700;
      padding: 1px 6px;
      border-radius: 4px;
      letter-spacing: 0.3px;
    }
    .bdl-badge-conf {
      background: #fee2e2;
      color: #b91c1c;
      font-size: 9.5px;
      font-weight: 700;
      padding: 1px 6px;
      border-radius: 4px;
      letter-spacing: 0.3px;
    }
    .bdl-badge-popular {
      background: #f3e8ff;
      color: #7e22ce;
      font-size: 9.5px;
      font-weight: 700;
      padding: 1px 6px;
      border-radius: 4px;
      letter-spacing: 0.3px;
    }

    /* Compilation Status Banner */
    .bdl-status-banner {
      display: none;
      background: #eff6ff;
      border: 1px solid #bfdbfe;
      border-radius: 8px;
      padding: 10px 14px;
      margin-bottom: 16px;
      align-items: center;
      gap: 10px;
      font-size: 12px;
      color: #1d4ed8;
      font-weight: 500;
    }
    .bdl-status-banner.active {
      display: flex;
    }
    .bdl-status-spinner {
      width: 16px;
      height: 16px;
      border: 2px solid #93c5fd;
      border-top-color: #1d4ed8;
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
          <span>100% Client-First Document Engine</span>
        </div>
      </div>
    </div>

    <div class="bdl-header-actions">
      <button type="button" class="bdl-btn-primary" id="btnBdlTriggerDownload">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:15px;height:15px;">
          <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
          <polyline points="7 10 12 15 17 10"/>
          <line x1="12" y1="15" x2="12" y2="3"/>
        </svg>
        <span id="lblBdlDownloadAction">Download Selected Format</span>
      </button>

      <button type="button" class="bdl-btn-close" id="btnBdlCloseDrawer" aria-label="Close drawer">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:16px;height:16px;">
          <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
        </svg>
      </button>
    </div>
  </div>

  {{-- 3PX COMPILATION PROGRESS BAR --}}
  <div class="bdl-progress-track" id="bdlProgressTrack">
    <div class="bdl-progress-bar" id="bdlProgressBar"></div>
  </div>

  {{-- SCROLLABLE DRAWER BODY --}}
  <div class="bdl-body">

    {{-- LIVE COMPILATION STATUS BANNER --}}
    <div class="bdl-status-banner" id="bdlStatusBanner">
      <div class="bdl-status-spinner"></div>
      <div style="flex:1;">
        <span id="bdlStatusStep" style="font-weight:600;">Compiling candidate presentation...</span>
        <span id="bdlStatusDetail" style="font-size:11px;color:#3b82f6;display:block;">Client WASM engine processing document...</span>
      </div>
    </div>

    {{-- CANDIDATE QUICK SUMMARY CARD --}}
    <div class="bdl-cand-card">
      <div class="bdl-cand-info">
        <div class="bdl-cand-avatar">
          {{ strtoupper(substr($candName, 0, 1)) }}{{ strtoupper(substr(strrchr($candName, ' ') ?: '', 1, 1)) }}
        </div>
        <div>
          <h3 class="bdl-cand-name">{{ ucwords($candName) }}</h3>
          <p class="bdl-cand-role">{{ ucwords($candRole) }}</p>
          <div class="bdl-cand-meta">
            <span>
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:12px;height:12px;"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
              {{ $candLoc }}
            </span>
            <span>
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:12px;height:12px;"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
              {{ $candExp }}
            </span>
            <span>
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:12px;height:12px;"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
              {{ $candComm }}
            </span>
          </div>
        </div>
      </div>

      <div class="bdl-cand-status">
        <span class="bdl-badge-avail">{{ $candNotice }}</span>
        <span class="bdl-badge-doc">{{ $hasResume ? ($resumeExt . ' Document Attached') : 'No Attached Document' }}</span>
      </div>
    </div>

    {{-- FORMAT SELECTION OPTIONS (15 ENTERPRISE FORMATS IN 5 CLUSTERS) --}}
    
    {{-- CLUSTER 1: STANDARD RESUMES --}}
    <div class="bdl-cluster-header">1. Standard Resumes</div>
    <div class="bdl-format-list">

      {{-- Format 1: Original Resume --}}
      <label class="bdl-format-card" data-format="1">
        <div class="bdl-radio-wrap">
          <input type="radio" name="bench_dl_format" value="1">
        </div>
        <div class="bdl-card-body">
          <div class="bdl-card-top">
            <h4 class="bdl-card-title">Original Candidate Resume</h4>
            <span class="text-muted fs-8">1 Coin</span>
          </div>
          <p class="bdl-card-desc">Downloads the original raw resume document without header branding or modifications.</p>
        </div>
      </label>

      {{-- Format 41: Resume + Company Branding --}}
      <label class="bdl-format-card" data-format="41">
        <div class="bdl-radio-wrap">
          <input type="radio" name="bench_dl_format" value="41">
        </div>
        <div class="bdl-card-body">
          <div class="bdl-card-top">
            <h4 class="bdl-card-title">Resume + Company Branding</h4>
            <span class="text-muted fs-8">2 Coins</span>
          </div>
          <p class="bdl-card-desc">Injects your company logo and verified in-house talent presentation banner at the top of page 1.</p>
        </div>
      </label>

    </div>

    {{-- CLUSTER 2: IN-HOUSE EVALUATION REPORT --}}
    <div class="bdl-cluster-header">2. In-House Evaluation Report</div>
    <div class="bdl-format-list">

      {{-- Format 5: Stand-alone In-House Evaluation Report --}}
      <label class="bdl-format-card" data-format="5">
        <div class="bdl-radio-wrap">
          <input type="radio" name="bench_dl_format" value="5">
        </div>
        <div class="bdl-card-body">
          <div class="bdl-card-top">
            <h4 class="bdl-card-title">In-House Evaluation Report</h4>
            <div class="bdl-badges">
              <span class="bdl-badge-popular">POPULAR</span>
              <span class="text-muted fs-8">1 Coin</span>
            </div>
          </div>
          <p class="bdl-card-desc">Stand-alone executive PDF dossier featuring evaluated skills matrix, gold star ratings, RM comments, and availability.</p>
        </div>
      </label>

    </div>

    {{-- CLUSTER 3: RECRUITER PACKAGES --}}
    <div class="bdl-cluster-header">3. Recruiter Packages</div>
    <div class="bdl-format-list">

      {{-- Format 42: Recruiter Package + Company Branding --}}
      <label class="bdl-format-card" data-format="42">
        <div class="bdl-radio-wrap">
          <input type="radio" name="bench_dl_format" value="42">
        </div>
        <div class="bdl-card-body">
          <div class="bdl-card-top">
            <h4 class="bdl-card-title">Recruiter Package + Company Branding</h4>
            <span class="text-muted fs-8">3 Coins</span>
          </div>
          <p class="bdl-card-desc">Concatenates the In-House Evaluation Report dossier with the company-branded candidate resume.</p>
        </div>
      </label>

      {{-- Format 43: Recruiter Package (Unbranded) --}}
      <label class="bdl-format-card" data-format="43">
        <div class="bdl-radio-wrap">
          <input type="radio" name="bench_dl_format" value="43">
        </div>
        <div class="bdl-card-body">
          <div class="bdl-card-top">
            <h4 class="bdl-card-title">Recruiter Package (Unbranded)</h4>
            <span class="text-muted fs-8">2 Coins</span>
          </div>
          <p class="bdl-card-desc">Neutral evaluation dossier combined with original candidate resume without any company logo.</p>
        </div>
      </label>

    </div>

    {{-- CLUSTER 4: HIGHLIGHTED SKILL RESUMES --}}
    <div class="bdl-cluster-header">4. Highlighted Skill Resumes</div>
    <div class="bdl-format-list">

      {{-- Format 44: Resume + Highlighted Keywords --}}
      <label class="bdl-format-card" data-format="44">
        <div class="bdl-radio-wrap">
          <input type="radio" name="bench_dl_format" value="44">
        </div>
        <div class="bdl-card-body">
          <div class="bdl-card-top">
            <h4 class="bdl-card-title">Resume + Highlighted Keywords</h4>
            <span class="text-muted fs-8">2 Coins</span>
          </div>
          <p class="bdl-card-desc">Applies vector highlights across the resume text matching candidate's verified skills and expertise.</p>
        </div>
      </label>

      {{-- Format 45: Resume + Highlighted Keywords + Company Branding --}}
      <label class="bdl-format-card" data-format="45">
        <div class="bdl-radio-wrap">
          <input type="radio" name="bench_dl_format" value="45">
        </div>
        <div class="bdl-card-body">
          <div class="bdl-card-top">
            <h4 class="bdl-card-title">Resume + Highlighted Keywords + Company Branding</h4>
            <span class="text-muted fs-8">3 Coins</span>
          </div>
          <p class="bdl-card-desc">Vector highlighted skill keywords with your corporate branding banner stamped on page 1.</p>
        </div>
      </label>

      {{-- Format 46 (DEFAULT ★): Evaluation Report + Resume + Highlighted Keywords + Company Branding --}}
      <label class="bdl-format-card selected" data-format="46">
        <div class="bdl-radio-wrap">
          <input type="radio" name="bench_dl_format" value="46" checked>
        </div>
        <div class="bdl-card-body">
          <div class="bdl-card-top">
            <h4 class="bdl-card-title">In-House Evaluation Report + Resume + Highlighted Keywords + Branding</h4>
            <div class="bdl-badges">
              <span class="bdl-badge-default">DEFAULT ★</span>
              <span class="text-muted fs-8">3 Coins</span>
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
        <div class="bdl-card-body">
          <div class="bdl-card-top">
            <h4 class="bdl-card-title">Recruiter Package + Highlighted Keywords (Unbranded)</h4>
            <span class="text-muted fs-8">2 Coins</span>
          </div>
          <p class="bdl-card-desc">Neutral evaluation dossier with vector highlighted skill keywords and zero branding.</p>
        </div>
      </label>

    </div>

    {{-- CLUSTER 5: PRIVACY & ANONYMIZATION SHIELDS --}}
    <div class="bdl-cluster-header">5. Privacy & Anonymization Shields</div>
    <div class="bdl-format-list">

      {{-- Format 48: Hide Contact Details --}}
      <label class="bdl-format-card" data-format="48">
        <div class="bdl-radio-wrap">
          <input type="radio" name="bench_dl_format" value="48">
        </div>
        <div class="bdl-card-body">
          <div class="bdl-card-top">
            <h4 class="bdl-card-title">Hide Contact Details</h4>
            <div class="bdl-badges">
              <span class="bdl-badge-conf">PII SHIELD</span>
              <span class="text-muted fs-8">2 Coins</span>
            </div>
          </div>
          <p class="bdl-card-desc">Redacts candidate phone numbers, primary & secondary email addresses, and external web links.</p>
        </div>
      </label>

      {{-- Format 49: Hide Candidate Name & Contact Details (Anti-Poaching Blind Profile) --}}
      <label class="bdl-format-card" data-format="49">
        <div class="bdl-radio-wrap">
          <input type="radio" name="bench_dl_format" value="49">
        </div>
        <div class="bdl-card-body">
          <div class="bdl-card-top">
            <h4 class="bdl-card-title">Hide Candidate Name & Contact Details (Blind Profile)</h4>
            <div class="bdl-badges">
              <span class="bdl-badge-conf">BLIND PROFILE</span>
              <span class="text-muted fs-8">2 Coins</span>
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
        <div class="bdl-card-body">
          <div class="bdl-card-top">
            <h4 class="bdl-card-title">Hide Contact Details + Company Branding</h4>
            <span class="text-muted fs-8">3 Coins</span>
          </div>
          <p class="bdl-card-desc">Contact channels redacted on high-resolution canvas with company branding stamped on header.</p>
        </div>
      </label>

      {{-- Format 51: Hide Contact Details + Evaluation Report + Company Branding --}}
      <label class="bdl-format-card" data-format="51">
        <div class="bdl-radio-wrap">
          <input type="radio" name="bench_dl_format" value="51">
        </div>
        <div class="bdl-card-body">
          <div class="bdl-card-top">
            <h4 class="bdl-card-title">Hide Contact Details + Evaluation Report + Company Branding</h4>
            <span class="text-muted fs-8">3 Coins</span>
          </div>
          <p class="bdl-card-desc">Evaluation report dossier merged with contact-redacted, company-branded candidate resume.</p>
        </div>
      </label>

      {{-- Format 52: Hide Contact Details + Evaluation Report (Unbranded) --}}
      <label class="bdl-format-card" data-format="52">
        <div class="bdl-radio-wrap">
          <input type="radio" name="bench_dl_format" value="52">
        </div>
        <div class="bdl-card-body">
          <div class="bdl-card-top">
            <h4 class="bdl-card-title">Hide Contact Details + Evaluation Report (Unbranded)</h4>
            <span class="text-muted fs-8">2 Coins</span>
          </div>
          <p class="bdl-card-desc">Neutral evaluation dossier combined with contact-redacted resume with zero company branding.</p>
        </div>
      </label>

      {{-- Format 53: Full Privacy: Hide Contact + Dossier + Keywords + Branding --}}
      <label class="bdl-format-card" data-format="53">
        <div class="bdl-radio-wrap">
          <input type="radio" name="bench_dl_format" value="53">
        </div>
        <div class="bdl-card-body">
          <div class="bdl-card-top">
            <h4 class="bdl-card-title">Full Privacy: Hide Contact + Dossier + Keywords + Branding</h4>
            <div class="bdl-badges">
              <span class="bdl-badge-conf">ALL-IN-ONE</span>
              <span class="text-muted fs-8">4 Coins</span>
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
        <div class="bdl-card-body">
          <div class="bdl-card-top">
            <h4 class="bdl-card-title">Full Privacy (Unbranded): Hide Contact + Dossier + Keywords</h4>
            <span class="text-muted fs-8">3 Coins</span>
          </div>
          <p class="bdl-card-desc">Neutral confidential package: Skills highlighted, contact channels redacted, merged with neutral dossier.</p>
        </div>
      </label>

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

  // Format Card Selection Handler
  $('#bench_download_resume_container .bdl-format-card').off('click').on('click', function(e) {
    if ($(e.target).is('input[type="radio"]')) return;
    var $radio = $(this).find('input[type="radio"]');
    $radio.prop('checked', true).trigger('change');
  });

  $('#bench_download_resume_container input[name="bench_dl_format"]').off('change').on('change', function() {
    $('#bench_download_resume_container .bdl-format-card').removeClass('selected');
    $(this).closest('.bdl-format-card').addClass('selected');
  });

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

  // Master Direct Download Trigger
  $('#btnBdlTriggerDownload').off('click').on('click', async function() {
    var $btn = $(this);
    var $lbl = $('#lblBdlDownloadAction');
    var $track = $('#bdlProgressTrack');
    var $bar = $('#bdlProgressBar');
    var $banner = $('#bdlStatusBanner');
    var $step = $('#bdlStatusStep');
    var $detail = $('#bdlStatusDetail');

    var selectedFormat = parseInt($('input[name="bench_dl_format"]:checked').val() || '46', 10);

    $btn.prop('disabled', true);
    $lbl.text('Compiling Document...');
    $track.show();
    $bar.css('width', '5%');
    $banner.addClass('active');

    function updateProgress(p) {
      if (!p) return;
      var pct = Math.min(100, Math.max(0, p.percent || 0));
      $bar.css('width', pct + '%');
      if (p.step) $step.text(p.step);
      if (p.detail) $detail.text(p.detail);
      $lbl.text('Compiling... ' + pct + '%');
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

      $lbl.text('Download Complete!');
      $bar.css('width', '100%');

      if (typeof toastr !== 'undefined') {
        toastr.success('Resume downloaded successfully!');
      }

      setTimeout(function() {
        $banner.removeClass('active');
        $track.hide();
        $bar.css('width', '0%');
        $btn.prop('disabled', false);
        $lbl.text('Download Selected Format');
      }, 1500);

    } catch (err) {
      console.error('[Bench Download Resume Center] Compilation Error:', err);
      $banner.removeClass('active');
      $track.hide();
      $btn.prop('disabled', false);
      $lbl.text('Download Selected Format');

      var errMsg = err.message || 'An error occurred during resume compilation.';
      if (typeof Swal !== 'undefined') {
        Swal.fire({
          icon: 'error',
          title: 'Download Failed',
          text: errMsg,
          confirmButtonColor: '#2563eb'
        });
      } else if (typeof toastr !== 'undefined') {
        toastr.error(errMsg);
      } else {
        alert(errMsg);
      }
    }
  });

})();
</script>
