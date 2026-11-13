{{-- 
  JERO ATS — Service Company General Settings Component (Rule 20 Modern Card + Sub-Card Enterprise Design System)
  Intent: Provides comprehensive system-wide settings including locale formatting, duplicate detection rules,
          department management, and connected account integrations with live debounced search and category jump filtering.
  Tenancy & Security: Strictly isolated by company_id. 0% MySQL reads on page requests. Atomic cache invalidation on save.
  Screen Fit: 100% fluid fit for 15.6" screens (1366x768 & 1920x1080) with zero horizontal overflow.
--}}

@php
  $userPerms = $userPermissions ?? [];
  $hasViewPerm = in_array('view_settings', $userPerms);
  $hasAddPerm = in_array('add_settings', $userPerms);
  $hasEditPerm = in_array('edit_settings', $userPerms);

  $deptCount = is_countable($departmentArr ?? null) ? count($departmentArr) : 0;
  $appDuplicateVal = (int) ($account->app_duplicate ?? 2);
  $appDuplicateOverrollVal = $appDuplicateVal === 2 ? ($account->app_duplicate_days ?? '60') : ($account->app_duplicate_days ?? '60');
  $appDuplicateReqWiseVal = $appDuplicateVal === 1 ? ($account->app_duplicate_days ?? '60') : ($account->app_duplicate_days ?? '60');
@endphp

<form id="setting_content" name="setting_content" class="form fv-plugins-bootstrap5 fv-plugins-framework" novalidate="novalidate" method="post">
  <meta name="csrf-token" content="{{ csrf_token() }}" />
  <input type="hidden" name="pagename" id="pagename" value="settings" />
  <input type="hidden" name="bench_show_mode" id="bench_show_mode" value="{{ $account->bench_show_mode ?? 0 }}" />
  @csrf
  @method('POST')

  @if($hasViewPerm)
  <div class="gs-wrapper general-settings-card-system">

    {{-- ══════════════════════════════════════════════════════════════
         TIER 1: PURE WHITE TOOLBAR HEADER CARD
         (Search bar and Save Changes button removed per user request.
          Dynamic Reset to Defaults action enabled)
         ══════════════════════════════════════════════════════════════ --}}
    <div class="gs-toolbar-card tier1-toolbar-card">
      <div class="gs-toolbar-left">
        <div class="gs-toolbar-icon-box">
          <i class="fas fa-sliders-h"></i>
        </div>
        <div>
          <div class="gs-toolbar-title-row">
            <h3 class="gs-toolbar-title">General Settings</h3>
          </div>
          <p class="gs-toolbar-sub">Control organization locale, duplicate detection rules, and company departments.</p>
        </div>
      </div>

      <div class="gs-toolbar-right">
        @if($hasEditPerm || $hasAddPerm)
        <button type="button" class="btn gs-btn-reset" id="gsResetSettingsBtn" title="Reset general settings to default">
          <i class="fas fa-undo-alt"></i> <span>Reset</span>
        </button>
        @endif
      </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════════
         TIER 3: PRIMARY CARD STREAM
         ══════════════════════════════════════════════════════════════ --}}
    <div class="gs-stream tier3-cards-stream">

      {{-- ────────────────────────────────────────────────────────────
           PRIMARY CARD 1: SYSTEM LOCALE & FORMATTING
           ──────────────────────────────────────────────────────────── --}}
      <div class="gs-primary-card primary-settings-card" id="card_locale" data-search-scope="locale language timezone currency time format date format english kolkata inr">
        <div class="gs-card-header">
          <div class="gs-card-header-left">
            <div class="gs-cat-icon gs-icon-blue">
              <i class="fas fa-globe"></i>
            </div>
            <div>
              <h4 class="gs-card-title">System Locale & Formatting</h4>
              <p class="gs-card-desc">Regional preferences controlling UI language, default timezone, operating currency, and timestamp standards.</p>
            </div>
          </div>
          <span class="gs-status-badge gs-badge-blue">
            <i class="fas fa-check-circle"></i> Standard Regional Configuration
          </span>
        </div>

        <div class="gs-card-body">
          <div class="gs-locale-grid">

            {{-- Sub-Card 1: Language --}}
            <div class="gs-subcard">
              <div>
                <div class="gs-subcard-head">
                  <div class="gs-field-icon gs-icon-blue">
                    <i class="fas fa-language"></i>
                  </div>
                  <div class="gs-label-wrap">
                    <label class="gs-field-label" for="language">System Language <span class="gs-req">*</span></label>
                    <div class="gs-field-help">Primary interface language used for system labels, candidate emails, and ATS workflows.</div>
                  </div>
                </div>
              </div>
              <div class="gs-control-wrap">
                <select name="language" id="language" aria-label="Select a Language" class="gs-select">
                  <option value="">Select a Language...</option>
                  @if(!empty($languagesArr))
                    @foreach($languagesArr as $lval)
                      @php 
                        $sel = ''; 
                        if ($account->language == $lval->id) {
                          $sel = 'selected="selected"'; 
                        } elseif ($lval->id == 1 && empty($account->language)) {
                          $sel = 'selected="selected"';
                        }
                      @endphp 
                      {!! '<option value="'.$lval->id.'" '.$sel.'>'.$lval->name.'</option>' !!}
                    @endforeach
                  @endif 
                </select>
              </div>
            </div>

            {{-- Sub-Card 2: Time Zone --}}
            <div class="gs-subcard">
              <div>
                <div class="gs-subcard-head">
                  <div class="gs-field-icon gs-icon-purple">
                    <i class="fas fa-clock"></i>
                  </div>
                  <div class="gs-label-wrap">
                    <label class="gs-field-label" for="timezone">Default Time Zone <span class="gs-req">*</span></label>
                    <div class="gs-field-help">Timestamp synchronization for interview scheduling, candidate notifications, and activity logs.</div>
                  </div>
                </div>
              </div>
              <div class="gs-control-wrap">
                <select name="timezone" id="timezone" aria-label="Select a Time Zone" class="gs-select">
                  <option value="">Select a timezone...</option>
                  @if(!empty($timezone))
                    <optgroup label="Popular Time Zones">
                      @foreach($timezone as $tval)
                        @if($tval->priority)
                          @php
                            $sel = '';
                            if ($account->timezone == $tval->time_zone) {
                              $sel = 'selected="selected"';
                            } elseif ($tval->time_zone == "Asia/Kolkata" && empty($account->timezone)) {
                              $sel = 'selected="selected"';
                            }
                          @endphp
                          {!! '<option value="'.$tval->time_zone.'" '.$sel.'>('.$tval->gmt_offse.') '.$tval->country_name.' ('.$tval->time_zone.') - '.$tval->timezone_abbreviation.'</option>' !!}
                        @endif
                      @endforeach
                    </optgroup>
                    <optgroup label="All Time Zones">
                      @foreach($timezone as $tval)
                        @if(!$tval->priority)
                          @php
                            $sel = '';
                            if ($account->timezone == $tval->time_zone) {
                              $sel = 'selected="selected"';
                            } elseif ($tval->time_zone == "Asia/Kolkata" && empty($account->timezone)) {
                              $sel = 'selected="selected"';
                            }
                          @endphp
                          {!! '<option value="'.$tval->time_zone.'" '.$sel.'>('.$tval->gmt_offse.') '.$tval->country_name.' ('.$tval->time_zone.') - '.$tval->timezone_abbreviation.'</option>' !!}
                        @endif
                      @endforeach
                    </optgroup>
                  @endif
                </select>
              </div>
            </div>

            {{-- Sub-Card 3: Operating Currency --}}
            <div class="gs-subcard">
              <div>
                <div class="gs-subcard-head">
                  <div class="gs-field-icon gs-icon-emerald">
                    <i class="fas fa-coins"></i>
                  </div>
                  <div class="gs-label-wrap">
                    <label class="gs-field-label" for="currency">Operating Currency</label>
                    <div class="gs-field-help">Default currency symbol & conversion anchor for job requisitions, candidate CTCs, and commercials.</div>
                  </div>
                </div>
              </div>
              <div class="gs-control-wrap">
                <select name="currency" id="currency" aria-label="Select a Currency" class="gs-select">
                  <option value="">Select a currency...</option>
                  @if(!empty($currencyArr))
                    @foreach($currencyArr as $cval)
                      @php
                        $sel = '';
                        if (!empty($account->currency)) {
                          if ($account->currency == $cval->id || (is_string($account->currency) && strtoupper($account->currency) === strtoupper($cval->code))) {
                            $sel = 'selected="selected"';
                          }
                        } elseif ($cval->id == 53) {
                          $sel = 'selected="selected"';
                        }
                      @endphp
                      {!! '<option value="'.$cval->id.'" '.$sel.'>'.$cval->code.' - '.$cval->country.'</option>' !!}
                    @endforeach
                  @endif
                </select>
              </div>
            </div>

            {{-- Sub-Card 4: Time & Date Display Style --}}
            <div class="gs-subcard">
              <div>
                <div class="gs-subcard-head">
                  <div class="gs-field-icon gs-icon-indigo">
                    <i class="fas fa-calendar-alt"></i>
                  </div>
                  <div class="gs-label-wrap">
                    <label class="gs-field-label" for="time_format">Time & Date Display Style</label>
                    <div class="gs-field-help">Preferred 12-hour or 24-hour timestamp standard rendered across recruitment pipeline.</div>
                  </div>
                </div>
              </div>
              <div class="gs-control-wrap gs-split-selects">
                <select name="time_format" id="time_format" aria-label="Select a time format" class="gs-select">
                  <option value="">Time Format...</option>
                  @if(!empty($time_formatArr))
                    @foreach($time_formatArr as $tval)
                      @php 
                        $sel = '';
                        if ($account->time_format == $tval->id) {
                          $sel = 'selected="selected"'; 
                        } elseif ($tval->id == 1 && empty($account->time_format)) {
                          $sel = 'selected="selected"'; 
                        }
                        $timeLabel = $tval->name;
                        if (stripos($tval->name, '12-hour') !== false) {
                            $timeLabel = '12-Hour Format (01-12 AM/PM)';
                        } elseif (stripos($tval->name, '24-hour') !== false) {
                            $timeLabel = '24-Hour Format (00-23)';
                        }
                      @endphp 
                      {!! '<option value="'.$tval->id.'" '.$sel.' title="'.htmlspecialchars($tval->name).'">'.$timeLabel.'</option>' !!}
                    @endforeach
                  @endif
                </select>
                <select name="date_format" id="date_format" aria-label="Select a date format" class="gs-select">
                  <option value="">Date Format...</option>
                  @if(!empty($date_formatArr))
                    @foreach($date_formatArr as $dval)
                      @php 
                        $sel = ''; 
                        if ($account->date_format == $dval->id) {
                          $sel = 'selected="selected"';
                        } elseif ($dval->id == 1 && empty($account->date_format)) {
                          $sel = 'selected="selected"';
                        }
                      @endphp 
                      {!! '<option value="'.$dval->id.'" '.$sel.'>'.$dval->name.'</option>' !!}
                    @endforeach
                  @endif
                </select>
              </div>
            </div>

          </div>
        </div>
      </div>

      {{-- ────────────────────────────────────────────────────────────
           PRIMARY CARD 2: DUPLICATE CANDIDATE DETECTION RULES
           ──────────────────────────────────────────────────────────── --}}
      <div class="gs-primary-card primary-settings-card" id="card_duplicates" data-search-scope="duplicate rules detection candidate application requisition global job wise disabled days">
        <div class="gs-card-header">
          <div class="gs-card-header-left">
            <div class="gs-cat-icon gs-icon-purple">
              <i class="fas fa-clone"></i>
            </div>
            <div>
              <h4 class="gs-card-title">Duplicate Candidate Detection Rules</h4>
              <p class="gs-card-desc">Define automated deduplication boundaries to prevent double-submissions and protect agency ownership.</p>
            </div>
          </div>
          <span class="gs-status-badge {{ $appDuplicateVal === 2 ? 'gs-badge-purple' : ($appDuplicateVal === 1 ? 'gs-badge-blue' : 'gs-badge-red') }}" id="duplicate_status_badge">
            <i class="fas {{ $appDuplicateVal === 0 ? 'fa-exclamation-triangle' : 'fa-check' }}"></i>
            @if($appDuplicateVal === 2) Global Detection Active ({{ $appDuplicateOverrollVal }} Days)
            @elseif($appDuplicateVal === 1) Job-Wise Detection Active ({{ $appDuplicateReqWiseVal }} Days)
            @else Deduplication Disabled
            @endif
          </span>
        </div>

        <div class="gs-card-body">
          <div class="gs-dup-grid">

            {{-- Option 1: Global Duplicate Detection (val = 2) --}}
            <div class="gs-dup-tile duplicate-tile {{ $appDuplicateVal === 2 ? 'selected' : '' }}" data-radio-val="2">
              <input type="radio" name="app_duplicate" value="2" class="d-none duplicate-radio-input" {{ $appDuplicateVal === 2 ? 'checked="checked"' : '' }}>
              <div>
                <div class="gs-dup-top">
                  <div class="gs-dup-icon gs-icon-blue">
                    <i class="fas fa-network-wired"></i>
                  </div>
                  <div class="gs-radio-circle">
                    <div class="gs-radio-dot"></div>
                  </div>
                </div>
                <div class="gs-dup-title">Global Duplicate Detection</div>
                <div class="gs-dup-desc">Detects identical candidates across <strong>all job requisitions</strong> across the entire company within the configured window.</div>
              </div>
              <div>
                <div class="gs-days-box">
                  <span>Check within:</span>
                  <input type="number" name="app_duplicate_overroll" id="app_duplicate_overroll" value="{{ $appDuplicateOverrollVal }}" placeholder="60" class="gs-days-input" min="1" max="365">
                  <span>days</span>
                </div>
                <span class="gs-rec-badge"><i class="fas fa-star"></i> Recommended for ATS Integrity</span>
              </div>
            </div>

            {{-- Option 2: Job-Wise Duplicate Detection (val = 1) --}}
            <div class="gs-dup-tile duplicate-tile {{ $appDuplicateVal === 1 ? 'selected' : '' }}" data-radio-val="1">
              <input type="radio" name="app_duplicate" value="1" class="d-none duplicate-radio-input" {{ $appDuplicateVal === 1 ? 'checked="checked"' : '' }}>
              <div>
                <div class="gs-dup-top">
                  <div class="gs-dup-icon gs-icon-purple">
                    <i class="fas fa-briefcase"></i>
                  </div>
                  <div class="gs-radio-circle">
                    <div class="gs-radio-dot"></div>
                  </div>
                </div>
                <div class="gs-dup-title">Job-Wise Duplicate Detection</div>
                <div class="gs-dup-desc">Detects duplicates <strong>only within the exact same job requisition</strong>. Candidates may freely apply to distinct jobs.</div>
              </div>
              <div>
                <div class="gs-days-box">
                  <span>Check within:</span>
                  <input type="number" name="app_duplicate_requisition_wise" id="app_duplicate_requisition_wise" value="{{ $appDuplicateReqWiseVal }}" placeholder="60" class="gs-days-input" min="1" max="365">
                  <span>days</span>
                </div>
                <span class="gs-spacer-tag">&nbsp;</span>
              </div>
            </div>

            {{-- Option 3: Duplicate Detection Disabled (val = 0) --}}
            <div class="gs-dup-tile duplicate-tile {{ $appDuplicateVal === 0 ? 'selected' : '' }}" data-radio-val="0">
              <input type="radio" name="app_duplicate" value="0" class="d-none duplicate-radio-input" {{ $appDuplicateVal === 0 ? 'checked="checked"' : '' }}>
              <div>
                <div class="gs-dup-top">
                  <div class="gs-dup-icon gs-icon-red">
                    <i class="fas fa-ban"></i>
                  </div>
                  <div class="gs-radio-circle">
                    <div class="gs-radio-dot"></div>
                  </div>
                </div>
                <div class="gs-dup-title">Deduplication Disabled</div>
                <div class="gs-dup-desc">Do not detect any duplicate candidates. Candidates can be submitted repeatedly without any system alert or barrier.</div>
              </div>
              <div>
                <div class="gs-days-box gs-days-muted">
                  <i class="fas fa-info-circle"></i>
                  <span>No day threshold required</span>
                </div>
                <span class="gs-spacer-tag">&nbsp;</span>
              </div>
            </div>

          </div>
        </div>
      </div>

      {{-- ────────────────────────────────────────────────────────────
           PRIMARY CARD 3: DEPARTMENT MANAGEMENT
           ──────────────────────────────────────────────────────────── --}}
      <div class="gs-primary-card primary-settings-card" id="card_departments" data-search-scope="department management departments engineering sales marketing finance operations human resources product support">
        <div class="gs-card-header">
          <div class="gs-card-header-left">
            <div class="gs-cat-icon gs-icon-emerald">
              <i class="fas fa-building"></i>
            </div>
            <div>
              <h4 class="gs-card-title">Department Management</h4>
              <p class="gs-card-desc">Structure internal organization units and assign job requisitions to departmental hierarchies.</p>
            </div>
          </div>
          <span class="gs-status-badge gs-badge-emerald" id="dept_card_badge">
            <i class="fas fa-sitemap"></i> {{ $deptCount }} Active Departments
          </span>
        </div>

        <div class="gs-card-body">
          <div class="gs-dept-actions-bar">
            <div class="gs-dept-summary-text">
              Departments allow recruiters to filter requisitions, manage hiring managers, and structure candidate funnels.
            </div>
            @if($hasAddPerm)
            <a href="javascript:void(0);" class="gs-btn-add-dept" data-bs-toggle="modal" data-bs-target="#add_department">
              <i class="fas fa-plus"></i> Add Department
            </a>
            @endif
          </div>

          <div class="dept_content" id="dept_container_box">
            @include('service_company.account.components.departments-list', ['departmentArr' => $departmentArr ?? []])
          </div>
        </div>
      </div>

      {{-- ────────────────────────────────────────────────────────────
           PRIMARY CARD 4: CONNECTED ACCOUNTS & INTEGRATIONS (Hidden per user request)
           ──────────────────────────────────────────────────────────── --}}
      <div class="gs-primary-card primary-settings-card" id="card_integrations" style="display:none;" data-search-scope="connected accounts integrations google calendar zoom video meeting microsoft teams slack">
        <div class="gs-card-header">
          <div class="gs-card-header-left">
            <div class="gs-cat-icon gs-icon-indigo">
              <i class="fas fa-plug"></i>
            </div>
            <div>
              <h4 class="gs-card-title">Connected Accounts & Integrations</h4>
              <p class="gs-card-desc">Connect external productivity and video conferencing suites for real-time interview automation.</p>
            </div>
          </div>
          <span class="gs-status-badge gs-badge-blue">
            <i class="fas fa-link"></i> Multi-Platform Bridge
          </span>
        </div>

        <div class="gs-card-body">
          <div class="gs-int-grid">

            {{-- Integration 1: Google Workspace --}}
            <div class="gs-int-subcard">
              <div class="gs-int-top">
                <div class="gs-int-logo gs-logo-google">
                  <i class="fab fa-google"></i>
                </div>
                <div>
                  <div class="gs-int-name">Google Workspace</div>
                  <div class="gs-int-desc">Synchronize calendar invites, interview slots, and automated Google Meet links.</div>
                </div>
              </div>
              <div class="gs-int-bottom">
                <a href="javascript:void(0);" class="google_config gs-int-config-link" rel="GOOGLE" data-bs-toggle="modal" data-bs-target="#google_config" data-toggle="modal" data-target="#google_config">
                  <i class="fas fa-cog"></i> Configuration
                </a>
                <label class="gs-switch">
                  <input type="checkbox" id="google_status" name="google_status" value="1" {{ ($account->google_status ?? 0) == 1 ? 'checked="checked"' : '' }}>
                  <span class="gs-slider"></span>
                </label>
              </div>
            </div>

            {{-- Integration 2: Zoom Video Meetings --}}
            <div class="gs-int-subcard">
              <div class="gs-int-top">
                <div class="gs-int-logo gs-logo-zoom">
                  <i class="fas fa-video"></i>
                </div>
                <div>
                  <div class="gs-int-name">Zoom Video Meetings</div>
                  <div class="gs-int-desc">Generate secure meeting links automatically when interview rounds are scheduled.</div>
                </div>
              </div>
              <div class="gs-int-bottom">
                <a href="javascript:void(0);" class="zoom_config gs-int-config-link" rel="ZOOM" data-bs-toggle="modal" data-bs-target="#zoom_config" data-toggle="modal" data-target="#zoom_config">
                  <i class="fas fa-cog"></i> Configuration
                </a>
                <label class="gs-switch">
                  <input type="checkbox" id="zoom_status" name="zoom_status" value="1" {{ ($account->zoom_status ?? 0) == 1 ? 'checked="checked"' : '' }}>
                  <span class="gs-slider"></span>
                </label>
              </div>
            </div>

            {{-- Integration 3: Microsoft Teams & 365 --}}
            <div class="gs-int-subcard">
              <div class="gs-int-top">
                <div class="gs-int-logo gs-logo-ms">
                  <i class="fab fa-microsoft"></i>
                </div>
                <div>
                  <div class="gs-int-name">Microsoft Teams & 365</div>
                  <div class="gs-int-desc">Integrate calendar schedules, round notifications, and candidate discussion bridges.</div>
                </div>
              </div>
              <div class="gs-int-bottom">
                <span class="gs-int-oauth-tag">Direct OAuth 2.0</span>
                <label class="gs-switch">
                  <input type="checkbox" id="microsoft_status" name="microsoft_status" value="1" {{ ($account->microsoft_status ?? 0) == 1 ? 'checked="checked"' : '' }}>
                  <span class="gs-slider"></span>
                </label>
              </div>
            </div>

          </div>
        </div>
      </div>

    </div>

  </div>
  @else
  <div class="gs-primary-card" style="padding: 36px; text-align: center;">
    <div style="color: #ef4444; font-weight: 600; font-size: 14px;">You do not have permission to view general settings.</div>
  </div>
  @endif

</form>

{{-- Dedicated Isolated Stylesheet for 1:1 Rule 20 Preview Fidelity --}}
<style>
  .gs-wrapper {
    width: 100%;
    font-family: 'Poppins', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    color: #0f172a;
    box-sizing: border-box;
  }
  .gs-wrapper *, .gs-wrapper *::before, .gs-wrapper *::after {
    box-sizing: border-box;
  }
  /* Guarantee FontAwesome 5 & 6 fonts render crisply without theme interference */
  .gs-wrapper i, .gs-wrapper [class*="fa-"] {
    font-family: "Font Awesome 6 Free", "Font Awesome 5 Free", "FontAwesome" !important;
    font-style: normal !important;
    line-height: 1 !important;
  }
  .gs-wrapper .fab {
    font-family: "Font Awesome 6 Brands", "Font Awesome 5 Brands" !important;
    font-weight: 400 !important;
  }
  .gs-wrapper .fas, .gs-wrapper .fa-solid {
    font-weight: 900 !important;
  }
  .gs-wrapper .far, .gs-wrapper .fa-regular {
    font-weight: 400 !important;
  }

  /* ── COLOR SYSTEM TOKENS ── */
  .gs-icon-blue { background: #eff6ff !important; color: #2563eb !important; border: 1px solid #dbeafe !important; }
  .gs-icon-blue i { color: #2563eb !important; }
  .gs-icon-purple { background: #f5f3ff !important; color: #8b5cf6 !important; border: 1px solid #ede9fe !important; }
  .gs-icon-purple i { color: #8b5cf6 !important; }
  .gs-icon-emerald { background: #ecfdf5 !important; color: #059669 !important; border: 1px solid #a7f3d0 !important; }
  .gs-icon-emerald i { color: #059669 !important; }
  .gs-icon-indigo { background: #eef2ff !important; color: #4f46e5 !important; border: 1px solid #e0e7ff !important; }
  .gs-icon-indigo i { color: #4f46e5 !important; }
  .gs-icon-red { background: #fef2f2 !important; color: #ef4444 !important; border: 1px solid #fee2e2 !important; }
  .gs-icon-red i { color: #ef4444 !important; }

  .gs-badge-blue { background: #eff6ff !important; color: #0284c7 !important; border: 1px solid #bae6fd !important; }
  .gs-badge-blue i { color: #0284c7 !important; }
  .gs-badge-purple { background: #f5f3ff !important; color: #7c3aed !important; border: 1px solid #ddd6fe !important; }
  .gs-badge-purple i { color: #7c3aed !important; }
  .gs-badge-emerald { background: #ecfdf5 !important; color: #059669 !important; border: 1px solid #a7f3d0 !important; }
  .gs-badge-emerald i { color: #059669 !important; }
  .gs-badge-red { background: #fef2f2 !important; color: #ef4444 !important; border: 1px solid #fee2e2 !important; }
  .gs-badge-red i { color: #ef4444 !important; }

  /* ── TIER 1: TOOLBAR HEADER CARD ── */
  .gs-toolbar-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    padding: 16px 20px;
    margin-bottom: 14px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
    box-shadow: 0 1px 3px rgba(15, 23, 42, 0.03);
    flex-wrap: wrap;
  }
  .gs-toolbar-left {
    display: flex;
    align-items: center;
    gap: 14px;
    min-width: 0;
  }
  .gs-toolbar-icon-box {
    width: 44px;
    height: 44px;
    border-radius: 10px;
    background: #eff6ff;
    color: #009ef7;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
    border: 1px solid #dbeafe;
    flex-shrink: 0;
  }
  .gs-toolbar-icon-box i { color: #009ef7 !important; }
  .gs-toolbar-title-row {
    display: flex;
    align-items: center;
    gap: 8px;
    flex-wrap: wrap;
  }
  .gs-toolbar-title {
    font-size: 16px;
    font-weight: 700;
    color: #0f172a;
    margin: 0;
  }
  .gs-toolbar-sub {
    font-size: 12.5px;
    color: #64748b;
    margin: 2px 0 0 0;
  }
  .gs-toolbar-right {
    display: flex;
    align-items: center;
    gap: 12px;
    flex-wrap: wrap;
  }
  .gs-btn-reset {
    height: 38px;
    padding: 0 16px;
    background: #f8fafc;
    color: #475569 !important;
    font-size: 13px;
    font-weight: 600;
    border: 1px solid #cbd5e1;
    border-radius: 8px;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    transition: all 0.2s ease;
  }
  .gs-btn-reset:hover {
    background: #f1f5f9;
    color: #0f172a !important;
    border-color: #94a3b8;
  }
  .gs-btn-reset i {
    color: #64748b !important;
  }
  .gs-btn-reset:hover i {
    color: #0f172a !important;
  }

  /* ── TIER 3: PRIMARY CARDS STREAM ── */
  .gs-stream {
    display: flex;
    flex-direction: column;
    gap: 18px;
  }
  .gs-primary-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    box-shadow: 0 1px 3px rgba(15, 23, 42, 0.02);
    overflow: hidden;
    transition: border-color 0.2s ease, box-shadow 0.2s ease;
  }
  .gs-primary-card:hover {
    border-color: #cbd5e1;
    box-shadow: 0 4px 12px rgba(15, 23, 42, 0.04);
  }
  .gs-card-header {
    padding: 16px 20px;
    border-bottom: 1px solid #f1f5f9;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 14px;
    background: #ffffff;
    flex-wrap: wrap;
    border-top-left-radius: 9px;
    border-top-right-radius: 9px;
  }
  #card_locale .gs-card-header {
    background: #eff6ff !important;
    border-bottom: 1px solid #bfdbfe !important;
  }
  #card_duplicates .gs-card-header {
    background: #f5f3ff !important;
    border-bottom: 1px solid #ddd6fe !important;
  }
  #card_departments .gs-card-header {
    background: #ecfdf5 !important;
    border-bottom: 1px solid #a7f3d0 !important;
  }
  #card_integrations .gs-card-header {
    background: #eef2ff !important;
    border-bottom: 1px solid #e0e7ff !important;
  }
  .gs-card-header-left {
    display: flex;
    align-items: center;
    gap: 12px;
  }
  .gs-cat-icon {
    width: 36px;
    height: 36px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 15px;
    flex-shrink: 0;
  }
  .gs-card-title {
    font-size: 15px;
    font-weight: 700;
    color: #0f172a;
    margin: 0;
  }
  .gs-card-desc {
    font-size: 12px;
    color: #64748b;
    margin: 1px 0 0 0;
  }
  .gs-status-badge {
    font-size: 11px;
    font-weight: 600;
    padding: 5px 11px;
    border-radius: 6px;
    display: inline-flex;
    align-items: center;
    gap: 6px;
  }
  .gs-card-body {
    padding: 20px;
  }

  /* ── SECTION 1: SYSTEM LOCALE SUB-CARDS ── */
  .gs-locale-grid {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 16px;
  }
  .gs-subcard {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    padding: 16px;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    transition: all 0.2s ease;
  }
  .gs-subcard:hover {
    background: #ffffff;
    border-color: #cbd5e1;
    box-shadow: 0 2px 6px rgba(15, 23, 42, 0.03);
  }
  .gs-subcard-head {
    display: flex;
    align-items: flex-start;
    gap: 10px;
    margin-bottom: 12px;
  }
  .gs-field-icon {
    width: 30px;
    height: 30px;
    border-radius: 7px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 13px;
    flex-shrink: 0;
    margin-top: 1px;
  }
  .gs-label-wrap { flex: 1; }
  .gs-field-label {
    font-size: 13px;
    font-weight: 600;
    color: #1e293b;
    display: flex;
    align-items: center;
    gap: 5px;
    margin: 0;
  }
  .gs-req { color: #ef4444; font-weight: 700; }
  .gs-field-help {
    font-size: 11.5px;
    color: #64748b;
    margin-top: 2px;
    line-height: 1.4;
  }
  .gs-control-wrap { margin-top: 4px; }
  .gs-split-selects { display: grid; grid-template-columns: 1.45fr 1fr; gap: 10px; }
  .gs-select {
    width: 100%;
    height: 38px;
    padding: 0 12px;
    font-size: 12.5px;
    font-weight: 500;
    color: #0f172a;
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 6px;
    outline: none;
    cursor: pointer;
    transition: all 0.2s ease;
  }
  .gs-select:focus {
    border-color: #009ef7;
    box-shadow: 0 0 0 3px rgba(0, 158, 247, 0.12);
  }

  /* ── SECTION 2: DUPLICATE DETECTION TILES ── */
  .gs-dup-grid {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 14px;
  }
  .gs-dup-tile {
    background: #f8fafc;
    border: 1.5px solid #e2e8f0;
    border-radius: 8px;
    padding: 16px;
    cursor: pointer;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    transition: all 0.2s ease;
  }
  .gs-dup-tile:hover {
    background: #ffffff;
    border-color: #94a3b8;
    box-shadow: 0 2px 8px rgba(15, 23, 42, 0.04);
  }
  .gs-dup-tile.selected {
    background: #ffffff;
    border-color: #009ef7;
    box-shadow: 0 0 0 1px #009ef7, 0 4px 12px rgba(0, 158, 247, 0.08);
  }
  .gs-dup-top {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    margin-bottom: 10px;
  }
  .gs-dup-icon {
    width: 34px;
    height: 34px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 14px;
  }
  .gs-radio-circle {
    width: 18px;
    height: 18px;
    border-radius: 50%;
    border: 2px solid #cbd5e1;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-top: 2px;
    transition: all 0.2s ease;
  }
  .gs-dup-tile.selected .gs-radio-circle { border-color: #009ef7; }
  .gs-radio-dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: #009ef7;
    opacity: 0;
    transform: scale(0.5);
    transition: all 0.2s ease;
  }
  .gs-dup-tile.selected .gs-radio-dot { opacity: 1; transform: scale(1); }
  .gs-dup-title { font-size: 13.5px; font-weight: 700; color: #0f172a; margin-bottom: 4px; }
  .gs-dup-desc { font-size: 11.5px; color: #64748b; line-height: 1.45; margin-bottom: 12px; }
  .gs-days-box {
    background: #f1f5f9;
    border: 1px solid #e2e8f0;
    border-radius: 6px;
    padding: 8px 10px;
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 12px;
    color: #334155;
  }
  .gs-dup-tile.selected .gs-days-box { background: #eff6ff; border-color: #bfdbfe; }
  .gs-days-muted { color: #94a3b8; }
  .gs-days-muted i { color: #94a3b8 !important; }
  .gs-days-input {
    width: 62px;
    height: 28px;
    text-align: center;
    font-weight: 700;
    color: #0f172a;
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 4px;
    outline: none;
    font-size: 12.5px;
  }
  .gs-days-input:focus { border-color: #009ef7; }
  .gs-rec-badge {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    font-size: 10px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.4px;
    padding: 3px 7px;
    border-radius: 4px;
    background: #ecfdf5;
    color: #059669;
    border: 1px solid #a7f3d0;
    margin-top: 8px;
  }
  .gs-rec-badge i { color: #059669 !important; }
  .gs-spacer-tag { display: inline-block; height: 22px; margin-top: 8px; }

  /* ── SECTION 3: DEPARTMENT SUB-CARDS ── */
  .gs-dept-actions-bar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 14px;
    flex-wrap: wrap;
    gap: 10px;
  }
  .gs-dept-summary-text { font-size: 12px; color: #64748b; }
  .gs-btn-add-dept {
    height: 34px;
    padding: 0 14px;
    background: #eff6ff;
    color: #009ef7 !important;
    font-size: 12px;
    font-weight: 600;
    border: 1px solid #bfdbfe;
    border-radius: 6px;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    transition: all 0.15s ease;
  }
  .gs-btn-add-dept i { color: #009ef7 !important; }
  .gs-btn-add-dept:hover {
    background: #009ef7;
    color: #ffffff !important;
    border-color: #009ef7;
  }
  .gs-btn-add-dept:hover i { color: #ffffff !important; }
  .gs-dept-grid {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 12px;
  }
  .gs-dept-subcard {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    padding: 12px 14px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    transition: all 0.15s ease;
  }
  .gs-dept-subcard:hover {
    background: #ffffff;
    border-color: #cbd5e1;
    box-shadow: 0 2px 6px rgba(15, 23, 42, 0.03);
  }
  .gs-dept-left {
    display: flex;
    align-items: center;
    gap: 10px;
    overflow: hidden;
  }
  .gs-dept-icon-box {
    width: 34px;
    height: 34px;
    border-radius: 7px;
    background: #eff6ff;
    color: #2563eb;
    border: 1px solid #dbeafe;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 13px;
    flex-shrink: 0;
  }
  .gs-dept-icon-box i { color: #2563eb !important; }
  .gs-dept-info { overflow: hidden; }
  .gs-dept-name {
    font-size: 13px;
    font-weight: 600;
    color: #1e293b;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }
  .gs-dept-tag { font-size: 10.5px; color: #94a3b8; }
  .gs-dept-delete-btn {
    width: 30px;
    height: 30px;
    border-radius: 6px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #94a3b8 !important;
    border: 1px solid transparent;
    flex-shrink: 0;
    transition: all 0.15s ease;
  }
  .gs-dept-delete-btn i { color: #94a3b8 !important; font-size: 13px; }
  .gs-dept-delete-btn:hover {
    background: #fef2f2;
    border-color: #fecaca;
  }
  .gs-dept-delete-btn:hover i { color: #ef4444 !important; }
  .gs-dept-empty {
    grid-column: 1 / -1;
    text-align: center;
    padding: 36px 20px;
  }
  .gs-dept-empty-icon {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    background: #eff6ff;
    color: #009ef7;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    margin-bottom: 10px;
  }
  .gs-dept-empty-icon i { color: #009ef7 !important; }
  .gs-dept-empty-title { font-weight: 700; color: #1e293b; font-size: 14px; }
  .gs-dept-empty-sub { color: #64748b; font-size: 12px; margin-top: 2px; }

  /* ── SECTION 4: INTEGRATIONS ── */
  .gs-int-grid {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 14px;
  }
  .gs-int-subcard {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    padding: 16px;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    transition: all 0.2s ease;
  }
  .gs-int-subcard:hover {
    background: #ffffff;
    border-color: #cbd5e1;
    box-shadow: 0 2px 6px rgba(15, 23, 42, 0.03);
  }
  .gs-int-top { display: flex; align-items: flex-start; gap: 12px; margin-bottom: 12px; }
  .gs-int-logo {
    width: 38px;
    height: 38px;
    border-radius: 8px;
    background: #ffffff;
    border: 1px solid #e2e8f0;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
    flex-shrink: 0;
  }
  .gs-logo-google i { color: #ea4335 !important; }
  .gs-logo-zoom i { color: #2d8cff !important; }
  .gs-logo-ms i { color: #00a4ef !important; }
  .gs-int-name { font-size: 13.5px; font-weight: 700; color: #0f172a; }
  .gs-int-desc { font-size: 11.5px; color: #64748b; line-height: 1.4; margin-top: 2px; }
  .gs-int-bottom {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding-top: 10px;
    border-top: 1px solid #e2e8f0;
  }
  .gs-int-config-link {
    font-size: 12px;
    font-weight: 600;
    color: #009ef7 !important;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 5px;
  }
  .gs-int-config-link i { color: #009ef7 !important; }
  .gs-int-config-link:hover { text-decoration: underline; }
  .gs-int-oauth-tag { font-size: 11.5px; color: #94a3b8; }

  /* Switch toggle */
  .gs-switch { position: relative; display: inline-block; width: 40px; height: 22px; margin: 0; }
  .gs-switch input { opacity: 0; width: 0; height: 0; }
  .gs-slider { position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background-color: #cbd5e1; transition: .2s; border-radius: 22px; }
  .gs-slider:before { position: absolute; content: ""; height: 16px; width: 16px; left: 3px; bottom: 3px; background-color: #ffffff; transition: .2s; border-radius: 50%; }
  .gs-switch input:checked + .gs-slider { background-color: #009ef7; }
  .gs-switch input:checked + .gs-slider:before { transform: translateX(18px); }



  /* ── 15.6" SCREEN FIT RESPONSIVE BREAKPOINTS ── */
  @media (max-width: 1199px) {
    .gs-dup-grid, .gs-dept-grid, .gs-int-grid {
      grid-template-columns: repeat(2, minmax(0, 1fr));
    }
  }
  @media (max-width: 767px) {
    .gs-locale-grid, .gs-dup-grid, .gs-dept-grid, .gs-int-grid, .gs-split-selects {
      grid-template-columns: 1fr;
    }
    .gs-toolbar-card { flex-direction: column; align-items: flex-start; }
    .gs-toolbar-right, .gs-search-box { width: 100%; }
  }
</style>

<script>
  $(document).ready(function() {
    // ── 1. Atomic Auto-Save on Settings Change ──
    function saveGeneralSettingsAjax(showToast) {
      var data = new FormData();
      var form_data = $('#setting_content').serializeArray();
      $.each(form_data, function (key, input) {
        data.append(input.name, input.value);
      });

      $.ajax({
        url: '{{ route('service_company.account.update', @$account->id) }}',
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        method: "POST",
        processData: false,
        contentType: false,
        data: data,
        success: function(res) {
          if (res.status === 'success') {
            if (showToast !== false) {
              if (typeof Swal !== 'undefined' && Swal.mixin) {
                const Toast = Swal.mixin({
                  toast: true,
                  position: 'top-end',
                  showConfirmButton: false,
                  timer: 2000,
                  timerProgressBar: true
                });
                Toast.fire({
                  icon: 'success',
                  title: 'Settings updated successfully'
                });
              } else if (typeof toastr !== 'undefined') {
                toastr.success('Settings updated successfully');
              }
            }
            if (res.tenant_defaults && window.JeroTenantDefaults) {
              window.JeroTenantDefaults.syncFromSettings(res.tenant_defaults);
            }
          } else if (res.status === 'warning') {
            if (typeof Swal !== 'undefined') {
              Swal.fire({
                text: res.message,
                icon: "error",
                buttonsStyling: false,
                confirmButtonText: "Ok, got it!",
                customClass: { confirmButton: "btn btn-light" }
              });
            }
          }
        }
      });
    }

    // Auto-save on select field change
    $('#language, #timezone, #currency, #time_format, #date_format').on('change', function() {
      saveGeneralSettingsAjax(true);
    });

    // ── 2. Duplicate Rules Interactive Radio Tiles with Auto-Save ──
    $('body').on('click', '.duplicate-tile', function(e) {
      if ($(e.target).is('input')) return;

      var $tile = $(this);
      var val = $tile.attr('data-radio-val');

      $('.duplicate-tile').removeClass('selected');
      $('.duplicate-radio-input').prop('checked', false);

      $tile.addClass('selected');
      $tile.find('.duplicate-radio-input').prop('checked', true);

      var $badge = $('#duplicate_status_badge');
      $badge.removeClass('gs-badge-purple gs-badge-blue gs-badge-red');
      if (val === '2') {
        var days = $('#app_duplicate_overroll').val() || '60';
        $badge.addClass('gs-badge-purple').html('<i class="fas fa-check"></i> Global Detection Active (' + days + ' Days)');
      } else if (val === '1') {
        var days = $('#app_duplicate_requisition_wise').val() || '60';
        $badge.addClass('gs-badge-blue').html('<i class="fas fa-check"></i> Job-Wise Detection Active (' + days + ' Days)');
      } else {
        $badge.addClass('gs-badge-red').html('<i class="fas fa-exclamation-triangle"></i> Deduplication Disabled');
      }

      saveGeneralSettingsAjax(true);
    });

    var dupDaysTimer = null;
    $('#app_duplicate_overroll, #app_duplicate_requisition_wise').on('input', function() {
      clearTimeout(dupDaysTimer);
      var self = this;
      dupDaysTimer = setTimeout(function() {
        var $tile = $(self).closest('.duplicate-tile');
        var val = $tile.attr('data-radio-val');
        var $badge = $('#duplicate_status_badge');
        if (val === '2') {
          $badge.html('<i class="fas fa-check"></i> Global Detection Active (' + $(self).val() + ' Days)');
        } else if (val === '1') {
          $badge.html('<i class="fas fa-check"></i> Job-Wise Detection Active (' + $(self).val() + ' Days)');
        }
        saveGeneralSettingsAjax(true);
      }, 500);
    });

    // ── 3. Reset Button: Confirm & Restore System Defaults ──
    $('#gsResetSettingsBtn').on('click', function(e) {
      e.preventDefault();

      if (typeof Swal === 'undefined') {
        if (confirm('Reset all general settings to system defaults? All current custom settings will be discarded.')) {
          performReset();
        }
        return;
      }

      Swal.fire({
        title: 'Reset to Default Settings?',
        text: 'All custom locale preferences and duplicate rules will be reset to system defaults (English, Asia/Kolkata IST, INR, 12-Hour format, Global Duplicate Detection 60 Days). All current changes will be discarded or deleted.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#009ef7',
        cancelButtonColor: '#64748b',
        confirmButtonText: 'Yes, Reset Defaults',
        cancelButtonText: 'Cancel',
        reverseButtons: true
      }).then(function(result) {
        if (result.isConfirmed) {
          performReset();
        }
      });

      function performReset() {
        $.ajax({
          url: '{{ route('service_company.account.resetGeneralSettings') }}',
          type: 'POST',
          headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          },
          beforeSend: function() {
            $('#gsResetSettingsBtn').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> <span>Resetting...</span>');
          },
          success: function(response) {
            if (response.status === 'success') {
              Swal.fire({
                icon: 'success',
                title: 'Settings Reset',
                text: response.message || 'General settings have been reset to system defaults.',
                timer: 1500,
                showConfirmButton: false
              }).then(function() {
                window.location.reload();
              });
            } else {
              Swal.fire('Error', response.message || 'Failed to reset settings.', 'error');
              $('#gsResetSettingsBtn').prop('disabled', false).html('<i class="fas fa-undo-alt"></i> <span>Reset</span>');
            }
          },
          error: function() {
            Swal.fire('Error', 'An error occurred while resetting settings.', 'error');
            $('#gsResetSettingsBtn').prop('disabled', false).html('<i class="fas fa-undo-alt"></i> <span>Reset</span>');
          }
        });
      }
    });

    // ── 4. Dynamic Department Badge Counter Synchronization ──
    function refreshDeptCounters() {
      var count = $('.dept_content').find('.dept-item-col').length;
      $('#dept_tab_badge').text(count);
      $('#dept_card_badge').html('<i class="fas fa-sitemap"></i> ' + count + ' Active Departments');
    }

    var deptContainer = document.querySelector('.dept_content');
    if (deptContainer && window.MutationObserver) {
      var observer = new MutationObserver(function() {
        refreshDeptCounters();
      });
      observer.observe(deptContainer, { childList: true, subtree: true });
    }
  });
</script>
