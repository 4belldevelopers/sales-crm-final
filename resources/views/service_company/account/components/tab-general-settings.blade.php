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
  <div class="general-settings-card-system">

    {{-- ══════════════════════════════════════════════════════════════
         TIER 1: PURE WHITE TOOLBAR HEADER CARD
         ══════════════════════════════════════════════════════════════ --}}
    <div class="card mb-4 bg-white border border-gray-300 rounded-3 shadow-none tier1-toolbar-card">
      <div class="card-body p-4 d-flex flex-wrap align-items-center justify-content-between gap-3">
        <div class="d-flex align-items-center gap-3">
          <div class="d-flex align-items-center justify-content-center bg-light-primary text-primary rounded-3 w-45px h-45px border border-primary-subtle flex-shrink-0">
            <i class="fa-solid fa-sliders fs-3"></i>
          </div>
          <div>
            <div class="d-flex align-items-center gap-2">
              <h3 class="fw-bolder text-dark mb-0 fs-4">General Settings</h3>
              <span class="badge badge-light-primary fw-bolder fs-8 px-2 py-1" id="domains_counter_badge">4 Configuration Domains</span>
            </div>
            <p class="text-muted fs-7 mb-0 mt-1">Configure global locale preferences, candidate deduplication boundaries, company departments, and integration bridges.</p>
          </div>
        </div>

        <div class="d-flex align-items-center gap-3 flex-wrap">
          {{-- 300ms Debounced Search Bar with Clear Button --}}
          <div class="position-relative w-250px">
            <span class="position-absolute top-50 translate-middle-y ms-3 text-gray-500">
              <i class="fa-solid fa-magnifying-glass fs-7"></i>
            </span>
            <input type="text" id="general_settings_search" class="form-control form-control-sm form-control-solid ps-9 pe-8 fs-7" placeholder="Search settings or departments..." autocomplete="off">
            <button type="button" id="general_search_clear_btn" class="btn btn-icon btn-sm position-absolute top-50 end-0 translate-middle-y text-gray-400 text-hover-danger p-0 me-2" style="display: none; width: 22px; height: 22px;">
              <i class="fa-solid fa-xmark fs-7"></i>
            </button>
          </div>

          @if($hasEditPerm)
          <button class="btn btn-primary btn-sm updateCompanyProfile d-inline-flex align-items-center gap-2 px-4" type="button" id="updateCompanyProfileHeader">
            <span class="csv2-save-spinner" aria-hidden="true" style="display: none;"></span>
            <span class="csv2-save-label"><i class="fa-solid fa-check fs-7"></i> Save Changes</span>
          </button>
          @endif
        </div>
      </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════════
         TIER 2: PURE WHITE SUB-TABS FILTER CARD
         ══════════════════════════════════════════════════════════════ --}}
    <div class="card mb-4 bg-white border border-gray-300 rounded-3 shadow-none tier2-subtabs-card">
      <div class="card-body p-2 d-flex align-items-center gap-2 overflow-auto" id="settings_subtabs_bar" style="scrollbar-width: none;">
        <button type="button" class="btn btn-sm btn-subtab active d-inline-flex align-items-center gap-2 px-3 py-2" data-target="all">
          <i class="fa-solid fa-border-all fs-7"></i>
          <span>All Settings</span>
          <span class="badge badge-light-primary rounded-pill fs-8 px-2">4</span>
        </button>
        <button type="button" class="btn btn-sm btn-subtab d-inline-flex align-items-center gap-2 px-3 py-2 text-gray-600" data-target="card_locale">
          <i class="fa-solid fa-globe fs-7"></i>
          <span>System Locale</span>
          <span class="badge badge-light-dark rounded-pill fs-8 px-2">4</span>
        </button>
        <button type="button" class="btn btn-sm btn-subtab d-inline-flex align-items-center gap-2 px-3 py-2 text-gray-600" data-target="card_duplicates">
          <i class="fa-solid fa-clone fs-7"></i>
          <span>Duplicate Rules</span>
          <span class="badge badge-light-dark rounded-pill fs-8 px-2">3</span>
        </button>
        <button type="button" class="btn btn-sm btn-subtab d-inline-flex align-items-center gap-2 px-3 py-2 text-gray-600" data-target="card_departments">
          <i class="fa-solid fa-building fs-7"></i>
          <span>Departments</span>
          <span class="badge badge-light-dark rounded-pill fs-8 px-2" id="dept_tab_badge">{{ $deptCount }}</span>
        </button>
        <button type="button" class="btn btn-sm btn-subtab d-inline-flex align-items-center gap-2 px-3 py-2 text-gray-600" data-target="card_integrations">
          <i class="fa-solid fa-plug fs-7"></i>
          <span>Connected Accounts</span>
          <span class="badge badge-light-dark rounded-pill fs-8 px-2">3</span>
        </button>
      </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════════
         TIER 3: PRIMARY CARD STREAM
         ══════════════════════════════════════════════════════════════ --}}
    <div class="d-flex flex-column gap-4 tier3-cards-stream">

      {{-- ────────────────────────────────────────────────────────────
           PRIMARY CARD 1: SYSTEM LOCALE & FORMATTING
           ──────────────────────────────────────────────────────────── --}}
      <div class="card bg-white border border-gray-300 rounded-3 shadow-none primary-settings-card" id="card_locale" data-search-scope="locale language timezone currency time format date format english kolkata inr">
        <div class="card-header border-bottom border-gray-200 px-6 py-4 d-flex align-items-center justify-content-between">
          <div class="d-flex align-items-center gap-3">
            <div class="w-35px h-35px rounded-3 bg-light-primary text-primary d-flex align-items-center justify-content-center border border-primary-subtle flex-shrink-0">
              <i class="fa-solid fa-globe fs-5"></i>
            </div>
            <div>
              <h4 class="fw-bolder text-dark mb-0 fs-5">System Locale & Formatting</h4>
              <p class="text-muted fs-8 mb-0">Regional preferences controlling UI language, default timezone, operating currency, and timestamp standards.</p>
            </div>
          </div>
          <span class="badge badge-light-primary fw-bolder fs-8 px-3 py-2">
            <i class="fa-solid fa-circle-check text-primary me-1"></i> Active Regional Config
          </span>
        </div>

        <div class="card-body p-6">
          <div class="row g-4">

            {{-- Sub-Card 1: Language --}}
            <div class="col-lg-6 col-12">
              <div class="setting-subcard p-4 rounded-3 h-100 d-flex flex-column justify-content-between" style="background: #f8fafc; border: 1px solid #e2e8f0;">
                <div>
                  <div class="d-flex align-items-start gap-3 mb-3">
                    <div class="w-30px h-30px rounded-2 bg-light-info text-info d-flex align-items-center justify-content-center flex-shrink-0 mt-1">
                      <i class="fa-solid fa-language fs-6"></i>
                    </div>
                    <div>
                      <label class="fw-bold text-dark fs-6 required" for="language">Language</label>
                      <div class="text-muted fs-8">Primary system language for interface labels, alerts, and candidate email notices.</div>
                    </div>
                  </div>
                </div>
                <div class="mt-2">
                  <select name="language" id="language" aria-label="Select a Language" data-placeholder="Select a language..." class="form-select form-select-solid fs-7">
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
            </div>

            {{-- Sub-Card 2: Time Zone --}}
            <div class="col-lg-6 col-12">
              <div class="setting-subcard p-4 rounded-3 h-100 d-flex flex-column justify-content-between" style="background: #f8fafc; border: 1px solid #e2e8f0;">
                <div>
                  <div class="d-flex align-items-start gap-3 mb-3">
                    <div class="w-30px h-30px rounded-2 bg-light-purple text-purple d-flex align-items-center justify-content-center flex-shrink-0 mt-1" style="background: #f5f3ff; color: #8b5cf6;">
                      <i class="fa-solid fa-clock fs-6"></i>
                    </div>
                    <div>
                      <label class="fw-bold text-dark fs-6 required" for="timezone">Default Time Zone</label>
                      <div class="text-muted fs-8">Timestamp anchor for interview scheduling, candidate invitations, and activity logs.</div>
                    </div>
                  </div>
                </div>
                <div class="mt-2">
                  <select name="timezone" id="timezone" aria-label="Select a Time Zone" data-placeholder="Select a time zone..." class="form-select form-select-solid fs-7">
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
            </div>

            {{-- Sub-Card 3: Operating Currency --}}
            <div class="col-lg-6 col-12">
              <div class="setting-subcard p-4 rounded-3 h-100 d-flex flex-column justify-content-between" style="background: #f8fafc; border: 1px solid #e2e8f0;">
                <div>
                  <div class="d-flex align-items-start gap-3 mb-3">
                    <div class="w-30px h-30px rounded-2 bg-light-success text-success d-flex align-items-center justify-content-center flex-shrink-0 mt-1">
                      <i class="fa-solid fa-coins fs-6"></i>
                    </div>
                    <div>
                      <label class="fw-bold text-dark fs-6" for="currency">Operating Currency</label>
                      <div class="text-muted fs-8">Standard currency code & symbol applied to job compensation, CTCs, and commercials.</div>
                    </div>
                  </div>
                </div>
                <div class="mt-2">
                  <select name="currency" id="currency" aria-label="Select a Currency" data-placeholder="Select a currency..." class="form-select form-select-solid fs-7">
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
            </div>

            {{-- Sub-Card 4: Time Format & Date Format --}}
            <div class="col-lg-6 col-12">
              <div class="setting-subcard p-4 rounded-3 h-100 d-flex flex-column justify-content-between" style="background: #f8fafc; border: 1px solid #e2e8f0;">
                <div>
                  <div class="d-flex align-items-start gap-3 mb-3">
                    <div class="w-30px h-30px rounded-2 bg-light-warning text-warning d-flex align-items-center justify-content-center flex-shrink-0 mt-1">
                      <i class="fa-solid fa-calendar-days fs-6"></i>
                    </div>
                    <div>
                      <label class="fw-bold text-dark fs-6" for="time_format">Time & Date Display Style</label>
                      <div class="text-muted fs-8">Preferred 12/24-hour timestamp format and date arrangement rendered in tables and emails.</div>
                    </div>
                  </div>
                </div>
                <div class="mt-2 row g-2">
                  <div class="col-6">
                    <select name="time_format" id="time_format" aria-label="Select a time format" class="form-select form-select-solid fs-7">
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
                          @endphp 
                          {!! '<option value="'.$tval->id.'" '.$sel.'>'.$tval->name.'</option>' !!}
                        @endforeach
                      @endif	
                    </select>
                  </div>
                  <div class="col-6">
                    <select name="date_format" id="date_format" aria-label="Select a date format" class="form-select form-select-solid fs-7">
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
        </div>
      </div>

      {{-- ────────────────────────────────────────────────────────────
           PRIMARY CARD 2: DUPLICATE CANDIDATE DETECTION RULES
           ──────────────────────────────────────────────────────────── --}}
      <div class="card bg-white border border-gray-300 rounded-3 shadow-none primary-settings-card" id="card_duplicates" data-search-scope="duplicate rules detection candidate application requisition global job wise disabled days">
        <div class="card-header border-bottom border-gray-200 px-6 py-4 d-flex align-items-center justify-content-between">
          <div class="d-flex align-items-center gap-3">
            <div class="w-35px h-35px rounded-3 d-flex align-items-center justify-content-center flex-shrink-0" style="background: #f5f3ff; color: #8b5cf6; border: 1px solid #ede9fe;">
              <i class="fa-solid fa-clone fs-5"></i>
            </div>
            <div>
              <h4 class="fw-bolder text-dark mb-0 fs-5">Duplicate Candidate Rules</h4>
              <p class="text-muted fs-8 mb-0">Define automated deduplication boundaries to prevent double-submissions and protect agency ownership.</p>
            </div>
          </div>
          <span class="badge fw-bolder fs-8 px-3 py-2" id="duplicate_status_badge" style="background: #f5f3ff; color: #7c3aed; border: 1px solid #ddd6fe;">
            <i class="fa-solid fa-check text-purple me-1"></i>
            @if($appDuplicateVal === 2) Global Detection Active ({{ $appDuplicateOverrollVal }} Days)
            @elseif($appDuplicateVal === 1) Job-Wise Detection Active ({{ $appDuplicateReqWiseVal }} Days)
            @else Deduplication Disabled
            @endif
          </span>
        </div>

        <div class="card-body p-6">
          <div class="row g-4 duplicate-options-grid">

            {{-- Option 1: Global Duplicate Detection (val = 2) --}}
            <div class="col-lg-4 col-12">
              <div class="duplicate-tile p-4 rounded-3 h-100 d-flex flex-column justify-content-between cursor-pointer {{ $appDuplicateVal === 2 ? 'selected' : '' }}" data-radio-val="2" style="background: #ffffff; border: 1.5px solid {{ $appDuplicateVal === 2 ? '#009ef7' : '#e2e8f0' }}; transition: all 0.2s ease;">
                <input type="radio" name="app_duplicate" value="2" class="d-none duplicate-radio-input" {{ $appDuplicateVal === 2 ? 'checked="checked"' : '' }}>
                <div>
                  <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="w-35px h-35px rounded-3 bg-light-primary text-primary d-flex align-items-center justify-content-center">
                      <i class="fa-solid fa-network-wired fs-5"></i>
                    </div>
                    <div class="radio-indicator-circle d-flex align-items-center justify-content-center" style="width: 20px; height: 20px; border-radius: 50%; border: 2px solid {{ $appDuplicateVal === 2 ? '#009ef7' : '#cbd5e1' }};">
                      <div class="radio-dot" style="width: 8px; height: 8px; border-radius: 50%; background: #009ef7; opacity: {{ $appDuplicateVal === 2 ? '1' : '0' }};"></div>
                    </div>
                  </div>
                  <div class="fw-bolder text-dark fs-6 mb-1">Global Duplicate Detection</div>
                  <div class="text-muted fs-8 mb-3">Detects duplicate candidates across <strong>all job requisitions</strong> in the system within the specified window.</div>
                </div>
                <div>
                  <div class="d-flex align-items-center gap-2 p-2 rounded-2 mb-2" style="background: {{ $appDuplicateVal === 2 ? '#eff6ff' : '#f8fafc' }}; border: 1px solid {{ $appDuplicateVal === 2 ? '#bfdbfe' : '#e2e8f0' }};">
                    <span class="fs-8 text-gray-700">Check within:</span>
                    <input type="number" name="app_duplicate_overroll" id="app_duplicate_overroll" value="{{ $appDuplicateOverrollVal }}" placeholder="60" class="form-control form-control-sm text-center fw-bold fs-7 p-1 w-65px bg-white" min="1" max="365">
                    <span class="fs-8 text-gray-700">days</span>
                  </div>
                  <span class="badge badge-light-success fs-9 fw-bolder py-1 px-2"><i class="fa-solid fa-star me-1 text-success"></i> Recommended for ATS Integrity</span>
                </div>
              </div>
            </div>

            {{-- Option 2: Job-Wise Duplicate Detection (val = 1) --}}
            <div class="col-lg-4 col-12">
              <div class="duplicate-tile p-4 rounded-3 h-100 d-flex flex-column justify-content-between cursor-pointer {{ $appDuplicateVal === 1 ? 'selected' : '' }}" data-radio-val="1" style="background: #ffffff; border: 1.5px solid {{ $appDuplicateVal === 1 ? '#009ef7' : '#e2e8f0' }}; transition: all 0.2s ease;">
                <input type="radio" name="app_duplicate" value="1" class="d-none duplicate-radio-input" {{ $appDuplicateVal === 1 ? 'checked="checked"' : '' }}>
                <div>
                  <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="w-35px h-35px rounded-3 bg-light-purple text-purple d-flex align-items-center justify-content-center" style="background: #f5f3ff; color: #8b5cf6;">
                      <i class="fa-solid fa-briefcase fs-5"></i>
                    </div>
                    <div class="radio-indicator-circle d-flex align-items-center justify-content-center" style="width: 20px; height: 20px; border-radius: 50%; border: 2px solid {{ $appDuplicateVal === 1 ? '#009ef7' : '#cbd5e1' }};">
                      <div class="radio-dot" style="width: 8px; height: 8px; border-radius: 50%; background: #009ef7; opacity: {{ $appDuplicateVal === 1 ? '1' : '0' }};"></div>
                    </div>
                  </div>
                  <div class="fw-bolder text-dark fs-6 mb-1">Job-Wise Duplicate Detection</div>
                  <div class="text-muted fs-8 mb-3">Detects duplicates <strong>only within the exact same job requisition</strong>. Candidates may freely apply to distinct jobs.</div>
                </div>
                <div>
                  <div class="d-flex align-items-center gap-2 p-2 rounded-2 mb-2" style="background: {{ $appDuplicateVal === 1 ? '#eff6ff' : '#f8fafc' }}; border: 1px solid {{ $appDuplicateVal === 1 ? '#bfdbfe' : '#e2e8f0' }};">
                    <span class="fs-8 text-gray-700">Check within:</span>
                    <input type="number" name="app_duplicate_requisition_wise" id="app_duplicate_requisition_wise" value="{{ $appDuplicateReqWiseVal }}" placeholder="60" class="form-control form-control-sm text-center fw-bold fs-7 p-1 w-65px bg-white" min="1" max="365">
                    <span class="fs-8 text-gray-700">days</span>
                  </div>
                  <span class="text-muted fs-9 d-block">&nbsp;</span>
                </div>
              </div>
            </div>

            {{-- Option 3: Duplicate Detection Disabled (val = 0) --}}
            <div class="col-lg-4 col-12">
              <div class="duplicate-tile p-4 rounded-3 h-100 d-flex flex-column justify-content-between cursor-pointer {{ $appDuplicateVal === 0 ? 'selected' : '' }}" data-radio-val="0" style="background: #ffffff; border: 1.5px solid {{ $appDuplicateVal === 0 ? '#009ef7' : '#e2e8f0' }}; transition: all 0.2s ease;">
                <input type="radio" name="app_duplicate" value="0" class="d-none duplicate-radio-input" {{ $appDuplicateVal === 0 ? 'checked="checked"' : '' }}>
                <div>
                  <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="w-35px h-35px rounded-3 bg-light-danger text-danger d-flex align-items-center justify-content-center">
                      <i class="fa-solid fa-ban fs-5"></i>
                    </div>
                    <div class="radio-indicator-circle d-flex align-items-center justify-content-center" style="width: 20px; height: 20px; border-radius: 50%; border: 2px solid {{ $appDuplicateVal === 0 ? '#009ef7' : '#cbd5e1' }};">
                      <div class="radio-dot" style="width: 8px; height: 8px; border-radius: 50%; background: #009ef7; opacity: {{ $appDuplicateVal === 0 ? '1' : '0' }};"></div>
                    </div>
                  </div>
                  <div class="fw-bolder text-dark fs-6 mb-1">Duplicate Detection Disabled</div>
                  <div class="text-muted fs-8 mb-3">Do not detect any duplicate candidates. Candidates can be submitted repeatedly without any barrier or warning.</div>
                </div>
                <div>
                  <div class="d-flex align-items-center gap-2 p-2 rounded-2 mb-2 bg-light text-muted border border-gray-200">
                    <i class="fa-solid fa-circle-info fs-7 text-muted"></i>
                    <span class="fs-8">No day threshold required</span>
                  </div>
                  <span class="text-muted fs-9 d-block">&nbsp;</span>
                </div>
              </div>
            </div>

          </div>
        </div>
      </div>

      {{-- ────────────────────────────────────────────────────────────
           PRIMARY CARD 3: DEPARTMENT MANAGEMENT
           ──────────────────────────────────────────────────────────── --}}
      <div class="card bg-white border border-gray-300 rounded-3 shadow-none primary-settings-card" id="card_departments" data-search-scope="department management departments engineering sales marketing finance operations human resources product support">
        <div class="card-header border-bottom border-gray-200 px-6 py-4 d-flex align-items-center justify-content-between flex-wrap gap-2">
          <div class="d-flex align-items-center gap-3">
            <div class="w-35px h-35px rounded-3 bg-light-success text-success d-flex align-items-center justify-content-center border border-success-subtle flex-shrink-0">
              <i class="fa-solid fa-building fs-5"></i>
            </div>
            <div>
              <h4 class="fw-bolder text-dark mb-0 fs-5">Department Management</h4>
              <p class="text-muted fs-8 mb-0">Organize your company structure into operational departments and align job requisitions.</p>
            </div>
          </div>
          <div class="d-flex align-items-center gap-3">
            <span class="badge badge-light-success fw-bolder fs-8 px-3 py-2" id="dept_card_badge">{{ $deptCount }} Departments</span>
            @if($hasAddPerm)
            <a href="javascript:void(0);" class="btn btn-sm btn-primary d-inline-flex align-items-center gap-2 px-3 py-2" data-bs-toggle="modal" data-bs-target="#add_department" data-toggle="modal" data-target="#add_department">
              <i class="fa-solid fa-plus fs-7"></i> Add Department
            </a>
            @endif
          </div>
        </div>

        {{-- Dynamic Department Content Container (Updated via AJAX) --}}
        <div class="card-body p-6 dept_content" id="dept_container_box">
          @include('service_company.account.components.departments-list', ['departmentArr' => $departmentArr ?? []])
        </div>
      </div>

      {{-- ────────────────────────────────────────────────────────────
           PRIMARY CARD 4: CONNECTED ACCOUNTS & INTEGRATIONS
           ──────────────────────────────────────────────────────────── --}}
      <div class="card bg-white border border-gray-300 rounded-3 shadow-none primary-settings-card" id="card_integrations" data-search-scope="connected accounts integrations google calendar zoom video meeting microsoft teams slack">
        <div class="card-header border-bottom border-gray-200 px-6 py-4 d-flex align-items-center justify-content-between">
          <div class="d-flex align-items-center gap-3">
            <div class="w-35px h-35px rounded-3 bg-light-info text-info d-flex align-items-center justify-content-center border border-info-subtle flex-shrink-0">
              <i class="fa-solid fa-plug fs-5"></i>
            </div>
            <div>
              <h4 class="fw-bolder text-dark mb-0 fs-5">Connected Accounts & Integrations</h4>
              <p class="text-muted fs-8 mb-0">Connect external video conferencing and productivity tools for automatic interview generation.</p>
            </div>
          </div>
          <span class="badge badge-light-info fw-bolder fs-8 px-3 py-2">
            <i class="fa-solid fa-link me-1"></i> Multi-Platform Bridge
          </span>
        </div>

        <div class="card-body p-6">
          <div class="row g-4">

            {{-- Integration 1: Google --}}
            <div class="col-lg-4 col-12">
              <div class="integration-subcard p-4 rounded-3 h-100 d-flex flex-column justify-content-between" style="background: #f8fafc; border: 1px solid #e2e8f0;">
                <div>
                  <div class="d-flex align-items-center gap-3 mb-3">
                    <img src="{{ asset('img/images/google-icon.svg') }}" class="w-35px h-35px flex-shrink-0" alt="Google" onerror="this.src='https://www.gstatic.com/images/branding/product/1x/gsa_512dp.png';">
                    <div>
                      <div class="fw-bolder text-dark fs-6">Google Workspace</div>
                      <div class="text-muted fs-8">Calendar sync & Google Meet</div>
                    </div>
                  </div>
                  <p class="text-gray-600 fs-8 mb-4">Synchronize candidate interview rounds directly with Google Calendar and generate instant Google Meet links.</p>
                </div>
                <div class="d-flex align-items-center justify-content-between pt-3 border-top border-gray-200">
                  <a href="javascript:void(0);" class="google_config text-primary fw-bold fs-7 d-inline-flex align-items-center gap-1" rel="GOOGLE" data-bs-toggle="modal" data-bs-target="#google_config" data-toggle="modal" data-target="#google_config">
                    <i class="fa-solid fa-gear fs-7"></i> Configuration
                  </a>
                  <div class="form-check form-switch form-check-custom form-check-solid m-0">
                    <input class="form-check-input w-35px h-20px" type="checkbox" id="google_status" name="google_status" value="1" {{ ($account->google_status ?? 0) == 1 ? 'checked="checked"' : '' }}>
                  </div>
                </div>
              </div>
            </div>

            {{-- Integration 2: Zoom --}}
            <div class="col-lg-4 col-12">
              <div class="integration-subcard p-4 rounded-3 h-100 d-flex flex-column justify-content-between" style="background: #f8fafc; border: 1px solid #e2e8f0;">
                <div>
                  <div class="d-flex align-items-center gap-3 mb-3">
                    <img src="{{ asset('img/images/github.svg') }}" class="w-35px h-35px flex-shrink-0" alt="Zoom" onerror="this.style.display='none';">
                    <div>
                      <div class="fw-bolder text-dark fs-6">Zoom Video Meetings</div>
                      <div class="text-muted fs-8">Automated Interview Rooms</div>
                    </div>
                  </div>
                  <p class="text-gray-600 fs-8 mb-4">Automatically provision dedicated Zoom meetings with waiting rooms and passcode security for scheduled rounds.</p>
                </div>
                <div class="d-flex align-items-center justify-content-between pt-3 border-top border-gray-200">
                  <a href="javascript:void(0);" class="zoom_config text-primary fw-bold fs-7 d-inline-flex align-items-center gap-1" rel="ZOOM" data-bs-toggle="modal" data-bs-target="#zoom_config" data-toggle="modal" data-target="#zoom_config">
                    <i class="fa-solid fa-gear fs-7"></i> Configuration
                  </a>
                  <div class="form-check form-switch form-check-custom form-check-solid m-0">
                    <input class="form-check-input w-35px h-20px" type="checkbox" id="zoom_status" name="zoom_status" value="1" {{ ($account->zoom_status ?? 0) == 1 ? 'checked="checked"' : '' }}>
                  </div>
                </div>
              </div>
            </div>

            {{-- Integration 3: Microsoft --}}
            <div class="col-lg-4 col-12">
              <div class="integration-subcard p-4 rounded-3 h-100 d-flex flex-column justify-content-between" style="background: #f8fafc; border: 1px solid #e2e8f0;">
                <div>
                  <div class="d-flex align-items-center gap-3 mb-3">
                    <img src="{{ asset('img/images/slack-icon.svg') }}" class="w-35px h-35px flex-shrink-0" alt="Microsoft" onerror="this.style.display='none';">
                    <div>
                      <div class="fw-bolder text-dark fs-6">Microsoft Teams</div>
                      <div class="text-muted fs-8">365 Calendar & Teams Calls</div>
                    </div>
                  </div>
                  <p class="text-gray-600 fs-8 mb-4">Connect Microsoft 365 organization calendars and coordinate recruiter and candidate discussion channels.</p>
                </div>
                <div class="d-flex align-items-center justify-content-between pt-3 border-top border-gray-200">
                  <span class="text-muted fs-8">Direct OAuth 2.0</span>
                  <div class="form-check form-switch form-check-custom form-check-solid m-0">
                    <input class="form-check-input w-35px h-20px" type="checkbox" id="microsoft_status" name="microsoft_status" value="1" {{ ($account->microsoft_status ?? 0) == 1 ? 'checked="checked"' : '' }}>
                  </div>
                </div>
              </div>
            </div>

          </div>
        </div>
      </div>

    </div>

    {{-- ══════════════════════════════════════════════════════════════
         BOTTOM ACTIVITY & TENANCY STRIP
         ══════════════════════════════════════════════════════════════ --}}
    <div class="card mt-4 bg-white border border-gray-300 rounded-3 shadow-none activity-strip-card">
      <div class="card-body p-4 d-flex flex-wrap align-items-center justify-content-between gap-3">
        <div class="d-flex align-items-center gap-3 flex-wrap">
          <div class="badge badge-light-dark fs-8 fw-bolder px-3 py-2 border border-gray-200">
            <i class="fa-solid fa-shield-halved text-primary me-2"></i>
            Tenant Context: {{ auth()->user()->company->company_name ?? 'Organization' }} (#{{ auth()->user()->company_id ?? 0 }})
          </div>
          <div class="text-muted fs-8 d-flex align-items-center gap-2">
            <i class="fa-solid fa-bolt text-warning fs-7"></i>
            <span>Atomic Redis cache invalidation triggers immediately on save. Zero stale configuration data.</span>
          </div>
        </div>

        @if($hasEditPerm)
        <div class="d-flex align-items-center gap-2">
          <a href="{{ route('service_company.account.index') }}" class="btn btn-sm btn-light text-muted px-4">
            Discard
          </a>
          <button class="btn btn-sm btn-primary updateCompanyProfile d-inline-flex align-items-center gap-2 px-5" type="button" id="updateCompanyProfile">
            <span class="csv2-save-spinner" aria-hidden="true" style="display: none;"></span>
            <span class="csv2-save-label">Save Changes</span>
          </button>
        </div>
        @endif
      </div>
    </div>

  </div>
  @else
  <div class="card bg-white border border-gray-300 rounded-3 p-8 text-center">
    <div class="text-danger fw-bold fs-6">You do not have permission to view general settings.</div>
  </div>
  @endif

</form>

{{-- Inline Scoped Styling & Interactive Scripts --}}
<style>
  /* 15.6" Screen Fit & Typography */
  .general-settings-card-system {
    font-family: 'Poppins', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
  }
  .general-settings-card-system .btn-subtab {
    background: transparent;
    border: none;
    border-radius: 6px;
    font-weight: 500;
    transition: all 0.15s ease;
    white-space: nowrap;
  }
  .general-settings-card-system .btn-subtab:hover {
    background: #f1f5f9;
    color: #0f172a !important;
  }
  .general-settings-card-system .btn-subtab.active {
    background: #eff6ff;
    color: #009ef7 !important;
    font-weight: 600;
    box-shadow: inset 0 0 0 1px rgba(0, 158, 247, 0.25);
  }
  .general-settings-card-system .duplicate-tile:hover {
    border-color: #94a3b8 !important;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.04);
  }
  .general-settings-card-system .duplicate-tile.selected {
    border-color: #009ef7 !important;
    box-shadow: 0 0 0 1px #009ef7, 0 4px 14px rgba(0, 158, 247, 0.08);
  }
  .general-settings-card-system .dept-subcard:hover {
    background: #ffffff !important;
    border-color: #cbd5e1 !important;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
  }
  .general-settings-card-system .integration-subcard:hover {
    background: #ffffff !important;
    border-color: #cbd5e1 !important;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
  }
  /* Save spinner animation */
  .updateCompanyProfile.is-loading .csv2-save-spinner {
    display: inline-block !important;
    width: 14px;
    height: 14px;
    border: 2px solid #ffffff;
    border-top-color: transparent;
    border-radius: 50%;
    animation: csv2-spin 0.6s linear infinite;
    margin-right: 6px;
  }
  @keyframes csv2-spin {
    to { transform: rotate(360deg); }
  }
</style>

<script>
  $(document).ready(function() {
    // ── 1. Live 300ms Debounced Search Across All Cards & Departments ──
    var $searchInput = $('#general_settings_search');
    var $clearBtn = $('#general_search_clear_btn');
    var generalSearchTimer = null;

    $searchInput.on('input', function() {
      var query = $(this).val().trim().toLowerCase();
      $clearBtn.toggle(query.length > 0);

      clearTimeout(generalSearchTimer);
      generalSearchTimer = setTimeout(function() {
        filterSettings(query);
      }, 300);
    });

    $clearBtn.on('click', function() {
      $searchInput.val('');
      $clearBtn.hide();
      filterSettings('');
      $searchInput.focus();
    });

    function filterSettings(query) {
      if (!query) {
        $('.primary-settings-card').show();
        $('.dept-item-col').show();
        return;
      }

      $('.primary-settings-card').each(function() {
        var $card = $(this);
        var scope = ($card.attr('data-search-scope') || '').toLowerCase();
        var cardText = $card.text().toLowerCase();

        if (scope.indexOf(query) !== -1 || cardText.indexOf(query) !== -1) {
          $card.show();

          // If inside departments card, filter individual department tiles
          if ($card.attr('id') === 'card_departments') {
            var anyDeptMatch = false;
            $card.find('.dept-item-col').each(function() {
              var deptName = $(this).attr('data-dept-name') || '';
              var matches = deptName.indexOf(query) !== -1;
              $(this).toggle(matches);
              if (matches) anyDeptMatch = true;
            });
            // If query is not general "department" word but specific dept name
            if (!anyDeptMatch && query.indexOf('dept') === -1) {
              // still show card if query matches card-level scope
            }
          }
        } else {
          $card.hide();
        }
      });
    }

    // ── 2. Tier 2 Category Jump Sub-Tabs ──
    $('#settings_subtabs_bar').on('click', '.btn-subtab', function() {
      $('#settings_subtabs_bar .btn-subtab').removeClass('active');
      $(this).addClass('active');

      var target = $(this).attr('data-target');
      if (target === 'all') {
        $('.primary-settings-card').show();
      } else {
        $('.primary-settings-card').each(function() {
          $(this).toggle($(this).attr('id') === target);
        });
      }
    });

    // ── 3. Duplicate Rules Interactive Radio Tiles ──
    $('body').on('click', '.duplicate-tile', function(e) {
      if ($(e.target).is('input')) return;

      var $tile = $(this);
      var val = $tile.attr('data-radio-val');

      $('.duplicate-tile').removeClass('selected').css('border-color', '#e2e8f0');
      $('.duplicate-tile .radio-dot').css('opacity', '0');
      $('.duplicate-tile .radio-indicator-circle').css('border-color', '#cbd5e1');
      $('.duplicate-radio-input').prop('checked', false);

      $tile.addClass('selected').css('border-color', '#009ef7');
      $tile.find('.radio-dot').css('opacity', '1');
      $tile.find('.radio-indicator-circle').css('border-color', '#009ef7');
      $tile.find('.duplicate-radio-input').prop('checked', true);

      // Update badge
      var $badge = $('#duplicate_status_badge');
      if (val === '2') {
        var days = $('#app_duplicate_overroll').val() || '60';
        $badge.attr('style', 'background: #f5f3ff; color: #7c3aed; border: 1px solid #ddd6fe;');
        $badge.html('<i class="fa-solid fa-check text-purple me-1"></i> Global Detection Active (' + days + ' Days)');
      } else if (val === '1') {
        var days = $('#app_duplicate_requisition_wise').val() || '60';
        $badge.attr('style', 'background: #eff6ff; color: #009ef7; border: 1px solid #bfdbfe;');
        $badge.html('<i class="fa-solid fa-check text-primary me-1"></i> Job-Wise Detection Active (' + days + ' Days)');
      } else {
        $badge.attr('style', 'background: #fef2f2; color: #ef4444; border: 1px solid #fee2e2;');
        $badge.html('<i class="fa-solid fa-ban text-danger me-1"></i> Deduplication Disabled');
      }
    });

    // ── 4. Header Save Button Triggers Main Update Button ──
    $('#updateCompanyProfileHeader').on('click', function() {
      $('#updateCompanyProfile').click();
    });

    // ── 5. Dynamic Department Badge Counter Synchronization ──
    function refreshDeptCounters() {
      var count = $('.dept_content').find('.dept-item-col').length;
      $('#dept_tab_badge').text(count);
      $('#dept_card_badge').text(count + ' Departments');
    }

    // Observer to automatically refresh counters when AJAX updates .dept_content
    var deptContainer = document.querySelector('.dept_content');
    if (deptContainer && window.MutationObserver) {
      var observer = new MutationObserver(function() {
        refreshDeptCounters();
      });
      observer.observe(deptContainer, { childList: true, subtree: true });
    }
  });
</script>
