{{--
  God-Level Company Profile — Page 1 (Company Profile & Company Logo)
  100% Replicates Add Internal Client Step 1 Architecture (Rule 20 Design Standard)
  Preserves 100% of Form Bindings, tenanted scope, and JeroIndustryIntelligenceEngine integration
  Optimized for 15.6" Screens (1366x768 & 1920x1080) and smaller screens
--}}
<?php
  $selectedIndName = '';
  if (!empty($industry)) {
      if (!empty($department)) {
          foreach ($department as $k => $val) {
              $currId = is_object($val) ? ($val->ind_id ?? $k) : (is_array($val) ? ($val['ind_id'] ?? $k) : $k);
              $currName = is_object($val) ? ($val->ind_name ?? $val->name ?? '') : (is_array($val) ? ($val['ind_name'] ?? $val['name'] ?? '') : $val);
              $decodedName = html_entity_decode((string)$currName, ENT_QUOTES | ENT_HTML5, 'UTF-8');
              if (((string)$currId === (string)$industry || strcasecmp($decodedName, (string)$industry) === 0) && !empty($currName)) {
                  $selectedIndName = ucwords($decodedName);
                  break;
              }
          }
      }
      if (empty($selectedIndName)) {
          $manifest = \App\Services\IndustryTaxonomyService::getGlobalManifest();
          foreach ($manifest['industries'] ?? [] as $mInd) {
              $mName = html_entity_decode((string)($mInd['name'] ?? ''), ENT_QUOTES | ENT_HTML5, 'UTF-8');
              if ((string)$mInd['id'] === (string)$industry || strcasecmp($mName, (string)$industry) === 0) {
                  $selectedIndName = ucwords($mName);
                  break;
              }
          }
      }
  }

  $background_image = !empty($logo) ? $logo : (!empty($account->logo) ? $account->logo : (!empty($client->logo) ? $client->logo : ''));
  if ($background_image && !preg_match('/^https?:\/\//i', $background_image) && !str_starts_with($background_image, 'data:')) {
      $background_image = asset('user-uploads/company-logo/' . $background_image);
  }

  $profileRestriction = $profileRestriction ?? app(\App\Services\CompanyProfileRestrictionService::class)->getRestrictionStatus($account ?? ($company ?? ($company_id ?? (auth()->check() ? auth()->user()->company_id : 1))));
  $isProfileLocked = (bool) ($profileRestriction['is_locked'] ?? false);
  $lockMessage = (string) ($profileRestriction['lock_message'] ?? '');
  $lockReason = (string) ($profileRestriction['lock_reason'] ?? '');
  $warningTitle = (string) ($profileRestriction['warning_title'] ?? 'Editing Restricted');
  $warningMessage = (string) ($profileRestriction['warning_message'] ?? "You can't edit these company details. Please contact your Administrator for assistance.");
?>

<div class="pb-5 client-info-step" data-wizard-type="step-content" data-wizard-state="current">
  <div class="client-step-layout client-information-layout god-level-company-layout">
    
    <!-- =========================================================================
         LEFT CARD: Company Profile (Review and update extracted website info)
         ========================================================================= -->
    <div class="client-main-column client-information-main">
      <section class="card god-card god-company-profile-card" aria-labelledby="god_company_profile_title">
        
        <!-- Card Header: Blue building icon + Title + Subtitle -->
        <div class="god-card-header">
          <div class="god-card-header-icon blue-sky-icon">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <rect x="4" y="2" width="16" height="20" rx="2" ry="2"/>
              <line x1="9" y1="6" x2="9" y2="6.01"/>
              <line x1="15" y1="6" x2="15" y2="6.01"/>
              <line x1="9" y1="10" x2="9" y2="10.01"/>
              <line x1="15" y1="10" x2="15" y2="10.01"/>
              <line x1="9" y1="14" x2="9" y2="14.01"/>
              <line x1="15" y1="14" x2="15" y2="14.01"/>
              <path d="M10 22v-4h4v4"/>
            </svg>
          </div>
          <div class="god-card-header-titles">
            <div class="d-flex align-items-center gap-2">
              <h3 class="god-card-title mb-0" id="god_company_profile_title">Company Profile</h3>
              @if($isProfileLocked)
                <span class="badge" style="background:#fee2e2; color:#991b1b; font-size:11px; font-weight:600; padding:3px 8px; border-radius:6px; border:1px solid #fecaca;">
                  <i class="fa fa-lock me-1" style="font-size:10px;"></i>Editing Locked
                </span>
              @endif
            </div>
            <p class="god-card-subtitle">Review and update the information extracted from the company website.</p>
          </div>
        </div>

        @if($isProfileLocked)
          <!-- Authoritative Profile Lock Notification Banner -->
          <div class="god-profile-lock-alert p-4 rounded-3 d-flex align-items-center gap-3" 
               style="background: #fffbeb; border: 1px solid #fde68a; border-left: 4px solid #f59e0b; color: #92400e; margin: 0 24px 20px 24px;">
            <div class="d-flex align-items-center justify-content-center flex-shrink-0" 
                 style="width: 36px; height: 36px; border-radius: 50%; background: #fef3c7; color: #d97706;">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
              </svg>
            </div>
            <div class="flex-grow-1">
              <div class="fw-bold fs-6 text-gray-800 mb-0" id="profile_lock_notice_text">
                {{ $lockMessage }}
              </div>
              <div class="text-muted fs-7 mt-1">
                Company Website, Company Name, Industry, and Company Type are permanently read-only. Unrelated company profile details can still be updated.
              </div>
            </div>
          </div>
        @endif

        <!-- 2-Column Fields Grid (Company Website & Name on separate rows) -->
        <div class="god-fields-grid">

          <!-- ROW 1 (Full Width): Company Website with Fetch Details Button -->
          <div class="client-field fv-plugins-icon-container god-field-wrap god-field-full">
            <div class="god-field-header">
              <label class="god-field-label @if($isProfileLocked) is-locked-company-field @endif" for="website" @if($isProfileLocked) data-company-field-locked="true" data-locked-field-name="Company Website" @endif>
                Company Website (Optional)
                @if($isProfileLocked)
                  <span class="badge badge-light-secondary ms-2" style="font-size: 11px; padding: 2px 7px; background:#f1f5f9; color:#64748b; border:1px solid #e2e8f0; font-weight:500;">
                    <i class="fa fa-lock me-1" style="font-size:10px;"></i>Locked
                  </span>
                @endif
              </label>
            </div>

            <div class="god-website-control-row">
              <div class="god-input-box god-website-input-box @if($isProfileLocked) is-locked-company-field @endif"
                   @if($isProfileLocked) data-company-field-locked="true" data-locked-field-name="Company Website" style="background-color: #f8fafc !important; cursor: not-allowed;" @endif>
                <span class="god-input-left-icon">
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/>
                    <path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/>
                  </svg>
                </span>
                <input type="text"
                       class="god-native-input @if($isProfileLocked) is-locked-company-field @endif"
                       name="website"
                       id="website"
                       value="{{ $website ?? '' }}"
                       placeholder="https://stripe.com"
                       autocomplete="off"
                       spellcheck="false"
                       @if($isProfileLocked)
                         readonly="readonly"
                         tabindex="0"
                         data-company-field-locked="true"
                         data-locked-field-name="Company Website"
                         style="background-color: #f8fafc !important; cursor: not-allowed; color: #475569;"
                       @else
                         onkeydown="if(event.key==='Enter'||event.keyCode===13){event.preventDefault();if(window.fetchGodLevelCompanyIntelligence)window.fetchGodLevelCompanyIntelligence(this.value);return false;}"
                         onblur="if(this.value && this.value.length > 3 && this.value !== window._lastFetchedWebsite && window.fetchGodLevelCompanyIntelligence) window.fetchGodLevelCompanyIntelligence(this.value);"
                       @endif>
                @if(!$isProfileLocked)
                <button type="button"
                        class="god-input-clear-btn"
                        id="website_clear_btn"
                        title="Clear website"
                        aria-label="Clear website"
                        style="{{ !empty($website) ? 'display:flex;' : 'display:none;' }}">
                  <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                  </svg>
                </button>
                @endif
              </div>

              <!-- Bright Yellow Fetch Details Button -->
              <button type="button"
                      class="btn god-fetch-btn @if($isProfileLocked) is-locked-company-field @endif"
                      id="client_auto_fetch"
                      title="{{ $isProfileLocked ? 'Company details are locked' : 'Fetch and auto-fill company intelligence' }}"
                      @if($isProfileLocked)
                        data-company-field-locked="true"
                        data-locked-field-name="Company Website"
                        style="opacity: 0.5; cursor: not-allowed;"
                        onclick="if(window.showLockedCompanyFieldWarning){window.showLockedCompanyFieldWarning('Company Website');} return false;"
                      @else
                        onclick="if(window.fetchGodLevelCompanyIntelligence)window.fetchGodLevelCompanyIntelligence(document.getElementById('website').value); return false;"
                      @endif>
                <svg width="15" height="15" viewBox="0 0 24 24" fill="currentColor">
                  <path d="m12 3-1.9 5.8a2 2 0 0 1-1.3 1.3L3 12l5.8 1.9a2 2 0 0 1 1.3 1.3L12 21l1.9-5.8a2 2 0 0 1 1.3-1.3L21 12l-5.8-1.9a2 2 0 0 1-1.3-1.3L12 3z"/>
                </svg>
                <span>Fetch Details</span>
              </button>
            </div>
            @if(!$isProfileLocked)
            <!-- Checking indicator for Website -->
            <div id="website_checking_indicator" style="display:none; font-size:11.5px; color:#2563eb; margin-top:5px; font-weight:500;">
              <span class="spinner-border spinner-border-sm me-1" style="width:12px;height:12px;border-width:1.5px;" role="status"></span>
              <span>Checking domain availability...</span>
            </div>
            <!-- Dedicated Company Domain Duplicate Warning Container -->
            <div class="god-field-inline-error text-danger" id="website_duplicate_error" style="display:none; font-size:12px; margin-top:6px; font-weight:500;">
              <span class="d-inline-flex align-items-center gap-1">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                <span id="website_duplicate_error_text">This website domain is already registered to another organization.</span>
              </span>
            </div>
            @endif
          </div>

          <!-- ROW 2 (Full Width): Company Name -->
          <div class="client-field fv-plugins-icon-container god-field-wrap god-field-full">
            <div class="god-field-header">
              <label class="god-field-label @if($isProfileLocked) is-locked-company-field @endif" for="client_id" @if($isProfileLocked) data-company-field-locked="true" data-locked-field-name="Company Name" @endif>
                Company Name
                @if($isProfileLocked)
                  <span class="badge badge-light-secondary ms-2" style="font-size: 11px; padding: 2px 7px; background:#f1f5f9; color:#64748b; border:1px solid #e2e8f0; font-weight:500;">
                    <i class="fa fa-lock me-1" style="font-size:10px;"></i>Locked
                  </span>
                @endif
              </label>
            </div>
            <div class="god-input-box @if($isProfileLocked) is-locked-company-field @endif"
                 @if($isProfileLocked) data-company-field-locked="true" data-locked-field-name="Company Name" style="background-color: #f8fafc !important; cursor: not-allowed;" @endif>
              <span class="god-input-left-icon">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <rect x="4" y="2" width="16" height="20" rx="2" ry="2"/>
                  <line x1="9" y1="6" x2="9" y2="6.01"/>
                  <line x1="15" y1="6" x2="15" y2="6.01"/>
                  <line x1="9" y1="10" x2="9" y2="10.01"/>
                  <line x1="15" y1="10" x2="15" y2="10.01"/>
                  <line x1="9" y1="14" x2="9" y2="14.01"/>
                  <line x1="15" y1="14" x2="15" y2="14.01"/>
                  <path d="M10 22v-4h4v4"/>
                </svg>
              </span>
              <input type="text"
                     class="god-native-input @if($isProfileLocked) is-locked-company-field @endif"
                     name="name"
                     id="client_id"
                     value="{{ $name ?? ($company_name ?? '') }}"
                     placeholder="e.g. Stripe"
                     @if($isProfileLocked)
                       readonly="readonly"
                       tabindex="0"
                       data-company-field-locked="true"
                       data-locked-field-name="Company Name"
                       style="background-color: #f8fafc !important; cursor: not-allowed; color: #475569;"
                     @else
                       oninput="var h = document.getElementById('hidden_company_name'); if (h) h.value = this.value;"
                     @endif>
            </div>
            <input type="hidden" name="company_name" id="hidden_company_name" value="{{ $name ?? ($company_name ?? '') }}">
            @if(!$isProfileLocked)
            <!-- Company Name Duplicate Warning Container -->
            <div class="god-field-inline-error text-danger" id="company_duplicate_error" style="display:none; font-size:12px; margin-top:6px; font-weight:500;">
              <span class="d-inline-flex align-items-center gap-1">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                <span id="company_duplicate_error_text">A company with this name already exists in the system.</span>
              </span>
            </div>
            @endif
          </div>

          <!-- ROW 3, COL 1: Industry (Combobox with JeroIndustryIntelligenceEngine) -->
          <div class="client-field fv-plugins-icon-container god-field-wrap">
            <div class="god-field-header">
              <label class="god-field-label @if($isProfileLocked) is-locked-company-field @endif" for="industry_display" @if($isProfileLocked) data-company-field-locked="true" data-locked-field-name="Industry" @endif>
                Industry
                @if($isProfileLocked)
                  <span class="badge badge-light-secondary ms-2" style="font-size: 11px; padding: 2px 7px; background:#f1f5f9; color:#64748b; border:1px solid #e2e8f0; font-weight:500;">
                    <i class="fa fa-lock me-1" style="font-size:10px;"></i>Locked
                  </span>
                @endif
              </label>
            </div>

            <!-- Native select kept synchronized for form POST & validation -->
            <select name="industry" id="industry" style="position:absolute; width:1px; height:1px; opacity:0; pointer-events:none;" tabindex="-1" aria-hidden="true">
              <option value="">Select industry</option>
              @php $indFoundInDept = false; @endphp
              @if(!empty($department))
                @foreach($department as $key => $val)
                  @php
                    $optId = is_object($val) ? ($val->ind_id ?? $key) : (is_array($val) ? ($val['ind_id'] ?? $key) : $key);
                    $optNameRaw = is_object($val) ? ($val->ind_name ?? $val->name ?? '') : (is_array($val) ? ($val['ind_name'] ?? $val['name'] ?? '') : $val);
                    $optName = html_entity_decode((string)$optNameRaw, ENT_QUOTES | ENT_HTML5, 'UTF-8');
                    $isSel = ((string)$optId === (string)($industry ?? '') || (!empty($selectedIndName) && strcasecmp($optName, $selectedIndName) === 0));
                    if ($isSel) $indFoundInDept = true;
                  @endphp
                  @if(!empty($optId) && !empty($optName))
                    <option value="{{ $optId }}" {!! $isSel ? 'selected="selected"' : '' !!}>{{ ucwords($optName) }}</option>
                  @endif
                @endforeach
              @endif
              @if(!empty($industry) && !empty($selectedIndName) && !$indFoundInDept)
                <option value="{{ $industry }}" selected="selected">{{ $selectedIndName }}</option>
              @endif
            </select>
            <input type="hidden" name="industries" id="hidden_industries" value="{{ $industry ?? '' }}">

            <!-- God-Level Enterprise Industry Combobox Component -->
            <div class="jero-ind-select-wrap god-ind-wrap @if($isProfileLocked) is-locked-company-field @endif"
                 id="jero_ind_select_wrap"
                 @if($isProfileLocked) data-company-field-locked="true" data-locked-field-name="Industry" style="cursor: not-allowed;" @endif>
              <div class="god-input-box jero-ind-input-box {{ $selectedIndName ? 'has-value' : '' }} @if($isProfileLocked) is-locked-company-field @endif"
                   id="jero_ind_input_box"
                   @if($isProfileLocked) data-company-field-locked="true" data-locked-field-name="Industry" style="background-color: #f8fafc !important; cursor: not-allowed;" @endif>
                <span class="god-input-left-icon jero-ind-search-icon">
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M3 21h18"/>
                    <path d="M5 21V7l8-4v18"/>
                    <path d="M19 21V11l-6-4"/>
                    <path d="M9 9h1"/>
                    <path d="M9 13h1"/>
                    <path d="M9 17h1"/>
                  </svg>
                </span>
                <input type="text"
                       class="god-native-input jero-ind-input @if($isProfileLocked) is-locked-company-field @endif"
                       id="industry_display"
                       placeholder="Select industry"
                       autocomplete="off"
                       spellcheck="false"
                       role="combobox"
                       aria-expanded="false"
                       aria-haspopup="listbox"
                       aria-autocomplete="list"
                       value="{{ $selectedIndName }}"
                       @if($isProfileLocked)
                         readonly="readonly"
                         tabindex="0"
                         data-company-field-locked="true"
                         data-locked-field-name="Industry"
                         style="background-color: #f8fafc !important; cursor: not-allowed; color: #475569;"
                       @endif>
                @if(!$isProfileLocked)
                <button type="button" class="god-input-clear-btn jero-ind-clear-btn" id="jero_ind_clear_btn" aria-label="Clear industry" title="Clear selection" style="{{ $selectedIndName ? 'display:inline-flex;' : 'display:none;' }}">
                  <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M18 6 6 18M6 6l12 12"/></svg>
                </button>
                <div class="jero-dropdown-chevron jero-ind-chevron" id="jero_ind_chevron" title="Toggle industry list" role="button" aria-label="Toggle industry list" tabindex="-1">
                  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m6 9 6 6 6-6"/></svg>
                </div>
                @endif
              </div>

              @if(!$isProfileLocked)
              <!-- God-Level Enterprise Industry Dropdown Menu -->
              <div class="jero-ind-dropdown" id="jero_ind_dropdown" style="display:none;" role="listbox" aria-label="Company Industries">
                <div class="jero-ind-list-container" id="jero_ind_list_container"></div>
              </div>
              @endif
            </div>
          </div>

          <!-- ROW 3, COL 2: Company Size (Enterprise Custom Dropdown) -->
          @php
            $companySizeTiers = [
              '1-1'         => ['label' => 'Solo (1 employee)',        'badge' => '1',           'desc' => 'Individual practitioner / Freelancer'],
              '2-10'        => ['label' => '2 – 10 employees',          'badge' => '2 – 10',      'desc' => 'Seed / Startup stage team'],
              '11-50'       => ['label' => '11 – 50 employees',         'badge' => '11 – 50',     'desc' => 'Early growth stage business'],
              '51-200'      => ['label' => '51 – 200 employees',        'badge' => '51 – 200',    'desc' => 'Mid-sized established business'],
              '201-500'     => ['label' => '201 – 500 employees',       'badge' => '201 – 500',   'desc' => 'Upper mid-market company'],
              '501-1000'    => ['label' => '501 – 1,000 employees',     'badge' => '501 – 1K',    'desc' => 'Commercial scale organization'],
              '1001-5000'   => ['label' => '1,001 – 5,000 employees',   'badge' => '1K – 5K',     'desc' => 'Enterprise tier organization'],
              '5001-10000'  => ['label' => '5,001 – 10,000 employees',  'badge' => '5K – 10K',    'desc' => 'Large enterprise corporation'],
              '10001-50000' => ['label' => '10,001 – 50,000 employees', 'badge' => '10K – 50K',   'desc' => 'Global enterprise scale'],
              '50001-0'     => ['label' => '50,000+ employees',        'badge' => '50,000+',     'desc' => 'Multinational conglomerate']
            ];

            // Normalize any legacy 1-10 to 2-10 or match selected
            $currSizeKey = @$company_sizes;
            if ($currSizeKey === '1-10') {
              $currSizeKey = '2-10';
            } elseif (in_array($currSizeKey, ['50001-', '10000-999999'])) {
              $currSizeKey = '50001-0';
            }

            $selectedSizeLabel = isset($companySizeTiers[$currSizeKey]) ? $companySizeTiers[$currSizeKey]['label'] : '';
          @endphp
          <div class="client-field fv-plugins-icon-container god-field-wrap">
            <div class="god-field-header">
              <label class="god-field-label" for="company_sizes_display">Company Size</label>
            </div>

            <!-- Native select kept synchronized for 100% Form POST & Validation compatibility -->
            <select name="company_sizes" id="company_sizes" style="position:absolute; width:1px; height:1px; opacity:0; pointer-events:none;" tabindex="-1" aria-hidden="true">
              <option value="">Select company size</option>
              @foreach($companySizeTiers as $cVal => $cData)
                <option value="{{ $cVal }}" {{ (string)$currSizeKey === (string)$cVal ? 'selected="selected"' : '' }}>{{ $cData['label'] }}</option>
              @endforeach
            </select>
            <input type="hidden" name="company_size" id="hidden_company_size" value="{{ $currSizeKey }}">

            <!-- God-Level Enterprise Company Size Combobox -->
            <div class="jero-csize-select-wrap god-csize-wrap" id="jero_csize_select_wrap">
              <div class="god-input-box jero-csize-input-box {{ $selectedSizeLabel ? 'has-value' : '' }}" id="jero_csize_input_box" role="button" tabindex="0" aria-haspopup="listbox" aria-expanded="false">
                <span class="god-input-left-icon jero-csize-icon">
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
                    <circle cx="9" cy="7" r="4"/>
                    <path d="M22 21v-2a4 4 0 0 0-3-3.87"/>
                    <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                  </svg>
                </span>
                <input type="text"
                       class="god-native-input jero-csize-input"
                       id="company_sizes_display"
                       placeholder="Select company size"
                       readonly
                       autocomplete="off"
                       spellcheck="false"
                       value="{{ htmlspecialchars($selectedSizeLabel) }}"
                       style="cursor:pointer; user-select:none;">
                <button type="button" class="god-input-clear-btn jero-csize-clear-btn" id="jero_csize_clear_btn" aria-label="Clear company size" title="Clear selection" style="{{ $selectedSizeLabel ? 'display:inline-flex;' : 'display:none;' }}">
                  <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M18 6 6 18M6 6l12 12"/></svg>
                </button>
                <div class="jero-dropdown-chevron jero-csize-chevron" id="jero_csize_chevron" title="Toggle company size list" role="button" aria-label="Toggle company size list" tabindex="-1">
                  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m6 9 6 6 6-6"/></svg>
                </div>
              </div>

              <!-- Enterprise Custom Dropdown List -->
              <div class="jero-csize-dropdown" id="jero_csize_dropdown" style="display:none;" role="listbox" aria-label="Company Sizes">
                <div class="jero-csize-list-container" id="jero_csize_list_container">
                  @foreach($companySizeTiers as $cVal => $cData)
                    <div class="jero-csize-item {{ (string)$currSizeKey === (string)$cVal ? 'selected' : '' }}"
                         data-val="{{ $cVal }}"
                         data-label="{{ $cData['label'] }}"
                         role="option"
                         aria-selected="{{ (string)$currSizeKey === (string)$cVal ? 'true' : 'false' }}">
                      <div class="jero-csize-item-main">
                        <span class="jero-csize-item-icon">
                          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="7" r="4"/><path d="M5.5 21a6.5 6.5 0 0 1 13 0"/></svg>
                        </span>
                        <div class="jero-csize-item-info">
                          <div class="jero-csize-item-label">{{ $cData['label'] }}</div>
                          <div class="jero-csize-item-desc">{{ $cData['desc'] }}</div>
                        </div>
                      </div>
                      <div class="jero-csize-item-side">
                        <span class="jero-csize-item-badge">{{ $cData['badge'] }}</span>
                        <span class="jero-csize-item-check">
                          <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                        </span>
                      </div>
                    </div>
                  @endforeach
                </div>
              </div>
            </div>
          </div>

          <!-- ROW 4, COL 1: Company Type (Search-First Dual-Layer Intelligence Combobox) -->
          @php
            $companyTypeTaxonomy = $companyTypeTaxonomy ?? \App\Helper\GeneralMethod::companyTypeTaxonomy();
            $selectedCompTypeName = '';
            if (isset($company_type) && $company_type !== '') {
              foreach($companyTypeTaxonomy as $tItem) {
                if ((string)$tItem['id'] === (string)$company_type) {
                  $selectedCompTypeName = $tItem['display_name'];
                  break;
                }
              }
              if (!$selectedCompTypeName && isset($companyType[$company_type])) {
                $selectedCompTypeName = $companyType[$company_type];
              }
            }
          @endphp
          <div class="client-field fv-plugins-icon-container god-field-wrap">
            <div class="god-field-header">
              <label class="god-field-label @if($isProfileLocked) is-locked-company-field @endif" for="company_type_display" @if($isProfileLocked) data-company-field-locked="true" data-locked-field-name="Company Type" @endif>
                Company Type
                @if($isProfileLocked)
                  <span class="badge badge-light-secondary ms-2" style="font-size: 11px; padding: 2px 7px; background:#f1f5f9; color:#64748b; border:1px solid #e2e8f0; font-weight:500;">
                    <i class="fa fa-lock me-1" style="font-size:10px;"></i>Locked
                  </span>
                @endif
              </label>
            </div>

            <!-- Native select kept synchronized for 100% Form POST & Validation compatibility -->
            <select name="company_type" id="company_type" style="position:absolute; width:1px; height:1px; opacity:0; pointer-events:none;" tabindex="-1" aria-hidden="true">
              <option value="">Select company type</option>
              @if(!empty($companyType))
                @foreach($companyType as $key => $val)
                  <option value="{{ $key }}" {{ (string)$key === (string)$company_type ? 'selected="selected"' : '' }}>{{ ucwords($val) }}</option>
                @endforeach
              @endif
            </select>
            <input type="hidden" name="industries_type" id="hidden_industries_type" value="{{ $company_type ?? '' }}">

            <!-- God-Level Enterprise Company Type Combobox -->
            <div class="jero-ctype-select-wrap god-ctype-wrap @if($isProfileLocked) is-locked-company-field @endif"
                 id="jero_ctype_select_wrap"
                 @if($isProfileLocked) data-company-field-locked="true" data-locked-field-name="Company Type" style="cursor: not-allowed;" @endif>
              <div class="god-input-box jero-ctype-input-box {{ $selectedCompTypeName ? 'has-value' : '' }} @if($isProfileLocked) is-locked-company-field @endif"
                   id="jero_ctype_input_box"
                   @if($isProfileLocked) data-company-field-locked="true" data-locked-field-name="Company Type" style="background-color: #f8fafc !important; cursor: not-allowed;" @endif>
                <span class="god-input-left-icon jero-ctype-search-icon">
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M6 22V4a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v18Z"/>
                    <path d="M6 12H4a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2h2"/>
                    <path d="M18 9h2a2 2 0 0 1 2 2v9a2 2 0 0 1-2 2h-2"/>
                    <path d="M10 6h4"/>
                    <path d="M10 10h4"/>
                    <path d="M10 14h4"/>
                    <path d="M10 18h4"/>
                  </svg>
                </span>
                <input type="text"
                       class="god-native-input jero-ctype-input @if($isProfileLocked) is-locked-company-field @endif"
                       id="company_type_display"
                       placeholder="Select company type (e.g. LLC, GmbH, Pvt Ltd, Inc)"
                       autocomplete="off"
                       spellcheck="false"
                       role="combobox"
                       aria-expanded="false"
                       aria-haspopup="listbox"
                       aria-autocomplete="list"
                       aria-controls="jero_ctype_dropdown"
                       value="{{ htmlspecialchars($selectedCompTypeName) }}"
                       @if($isProfileLocked)
                         readonly="readonly"
                         tabindex="0"
                         data-company-field-locked="true"
                         data-locked-field-name="Company Type"
                         style="background-color: #f8fafc !important; cursor: not-allowed; color: #475569;"
                       @endif>
                @if(!$isProfileLocked)
                <button type="button" class="god-input-clear-btn jero-ctype-clear-btn" id="jero_ctype_clear_btn" aria-label="Clear company type" title="Clear selection" style="{{ $selectedCompTypeName ? 'display:inline-flex;' : 'display:none;' }}">
                  <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M18 6 6 18M6 6l12 12"/></svg>
                </button>
                <div class="jero-dropdown-chevron jero-ctype-chevron" id="jero_ctype_chevron" title="Toggle company type list" role="button" aria-label="Toggle company type list" tabindex="-1">
                  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m6 9 6 6 6-6"/></svg>
                </div>
                @endif
              </div>

              @if(!$isProfileLocked)
              <!-- Dropdown Menu Container (Search-First) -->
              <div class="jero-ctype-dropdown" id="jero_ctype_dropdown" style="display:none;" role="listbox" aria-label="Company Types">
                <div class="jero-ctype-list-container" id="jero_ctype_list_container"></div>
              </div>
              @endif
            </div>
          </div>

          <!-- ROW 4, COL 2: Founded Year (Enterprise Searchable Dropdown) -->
          <div class="client-field fv-plugins-icon-container god-field-wrap">
            <div class="god-field-header">
              <label class="god-field-label" for="founded_display">Founded Year</label>
            </div>

            <!-- Native hidden input kept synchronized for 100% Form POST, validation, and API compatibility -->
            <input type="hidden" name="founded" id="founded" value="{{ $founded ?? '' }}">

            <!-- God-Level Enterprise Founded Year Combobox -->
            <div class="jero-founded-select-wrap god-founded-wrap" id="jero_founded_select_wrap">
              <div class="god-input-box jero-founded-input-box {{ !empty($founded) ? 'has-value' : '' }}" id="jero_founded_input_box">
                <span class="god-input-left-icon jero-founded-calendar-icon">
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                    <line x1="16" y1="2" x2="16" y2="6"/>
                    <line x1="8" y1="2" x2="8" y2="6"/>
                    <line x1="3" y1="10" x2="21" y2="10"/>
                  </svg>
                </span>
                <input type="text"
                       class="god-native-input jero-founded-input"
                       id="founded_display"
                       placeholder="Select founded year"
                       autocomplete="off"
                       spellcheck="false"
                       role="combobox"
                       aria-expanded="false"
                       aria-haspopup="listbox"
                       aria-controls="jero_founded_dropdown"
                       readonly
                       value="{{ $founded ?? '' }}">
                <button type="button" class="god-input-clear-btn jero-founded-clear-btn" id="jero_founded_clear_btn" aria-label="Clear founded year" title="Clear selection" style="{{ !empty($founded) ? 'display:inline-flex;' : 'display:none;' }}">
                  <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M18 6 6 18M6 6l12 12"/></svg>
                </button>
                <div class="jero-dropdown-chevron jero-founded-chevron" id="jero_founded_chevron" title="Toggle founded year list" role="button" aria-label="Toggle founded year list" tabindex="-1">
                  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m6 9 6 6 6-6"/></svg>
                </div>
              </div>

              <!-- Dropdown Menu Container (Search-First) -->
              <div class="jero-founded-dropdown" id="jero_founded_dropdown" style="display:none;" role="listbox" aria-label="Founded Years">
                <div class="jero-founded-search-wrap">
                  <span class="jero-founded-search-icon">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                      <circle cx="11" cy="11" r="8"/>
                      <line x1="21" y1="21" x2="16.65" y2="16.65"/>
                    </svg>
                  </span>
                  <input type="text"
                         class="jero-founded-search-input"
                         id="founded_search_input"
                         placeholder="Search year..."
                         autocomplete="off"
                         spellcheck="false">
                </div>
                <div class="jero-founded-list-container" id="jero_founded_list_container"></div>
                <div class="jero-founded-footer">
                  <div class="jero-founded-sync-status">
                    <span class="jero-founded-calendar-dot"></span>
                    <span>Historical Registry (1800 – {{ date('Y') }})</span>
                  </div>
                  <span class="jero-founded-footer-hint">↑↓ Navigate • Enter Select • Esc Close</span>
                </div>
              </div>
            </div>
          </div>

          <!-- ROW 5, COL 1: Career / Jobs Page URL with External Link Button -->
          <div class="client-field fv-plugins-icon-container god-field-wrap">
            <div class="god-field-header">
              <label class="god-field-label" for="career_page_url">Career / Jobs Page URL</label>
            </div>
            <div class="god-input-box">
              <span class="god-input-left-icon">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/>
                  <path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/>
                </svg>
              </span>
              <input type="url"
                     class="god-native-input"
                     name="career_page_url"
                     id="career_page_url"
                     value="{{ $career_page_url ?? '' }}"
                     placeholder="https://stripe.com/jobs">
              <input type="hidden" name="career_page_link" id="hidden_career_page_link" value="{{ $career_page_url ?? '' }}">
              <button type="button"
                      class="god-input-external-btn"
                      id="career_url_open_btn"
                      title="Open career page in new tab"
                      aria-label="Open career page">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/>
                  <polyline points="15 3 21 3 21 9"/>
                  <line x1="10" y1="14" x2="21" y2="3"/>
                </svg>
              </button>
            </div>
          </div>

          <!-- ROW 5, COL 2: Tax ID / Business Reg No -->
          <div class="client-field fv-plugins-icon-container god-field-wrap">
            <div class="god-field-header">
              <label class="god-field-label" for="tax_id">Tax ID / Business Reg No / GST / Service Number (Optional)</label>
            </div>
            <div class="god-input-box">
              <span class="god-input-left-icon">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <rect x="3" y="4" width="18" height="16" rx="2"/>
                  <line x1="7" y1="8" x2="17" y2="8"/>
                  <line x1="7" y1="12" x2="13" y2="12"/>
                  <line x1="7" y1="16" x2="11" y2="16"/>
                </svg>
              </span>
              <input type="text"
                     class="god-native-input"
                     name="tax_id"
                     id="tax_id"
                     value="{{ $tax_id ?? '' }}"
                     placeholder="e.g. EIN, CIN, VAT, LEI">
            </div>
          </div>

        </div>

      </section>
    </div>

    <!-- =========================================================================
         RIGHT CARD: Company Logo (Set or update the company logo)
         ========================================================================= -->
    <div class="client-side-column client-information-sidebar god-logo-column">
      <section class="card god-card god-company-logo-card" aria-labelledby="god_company_logo_title">
        
        <!-- Card Header: Blue image icon + Title + Subtitle -->
        <div class="god-card-header">
          <div class="god-card-header-icon blue-sky-icon">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <rect x="3" y="3" width="18" height="18" rx="2" ry="2"/>
              <circle cx="8.5" cy="8.5" r="1.5"/>
              <polyline points="21 15 16 10 5 21"/>
            </svg>
          </div>
          <div class="god-card-header-titles">
            <h3 class="god-card-title" id="god_company_logo_title">Company Logo</h3>
            <p class="god-card-subtitle">Set or update the company logo.</p>
          </div>
        </div>

        <!-- Hidden Native File & Avatar Remove Inputs (Preserves controller upload contract) -->
        <input type="file"
               name="logo"
               id="client_logo_input"
               accept=".png, .jpg, .jpeg, .webp, .svg"
               onchange="loadFile(event)"
               style="display:none;" />
        <input type="hidden" name="avatar_remove" id="client_avatar_remove" value="0" />

        <!-- Large Logo Preview Container (Dashed border rounded box) -->
        <div class="god-logo-preview-box" id="company_logo_preview_box">
          <div class="god-logo-surface {{ !empty($background_image) ? 'has-image' : '' }}"
               id="company_logo_box"
               style="<?php echo !empty($background_image) ? 'background-image: url(' . $background_image . ');' : ''; ?>">
            @if(empty($background_image))
              <div class="god-logo-placeholder" id="logo_placeholder">
                <svg width="44" height="44" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                  <rect x="3" y="3" width="18" height="18" rx="2" ry="2"/>
                  <circle cx="8.5" cy="8.5" r="1.5"/>
                  <polyline points="21 15 16 10 5 21"/>
                </svg>
                <span class="placeholder-title">Company Logo</span>
                <span class="placeholder-sub">Click Change Logo or auto-fetch</span>
              </div>
            @endif
          </div>
        </div>

        <!-- Status Banner: Logo automatically detected from website -->
        <div class="god-logo-status-banner {{ !empty($background_image) ? 'is-detected' : 'is-empty' }}" id="logo_status_banner">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <polyline points="20 6 9 17 4 12"/>
          </svg>
          <span id="logo_status_text">{{ !empty($background_image) ? 'Logo automatically detected from website' : 'No logo detected yet' }}</span>
        </div>

        <!-- Action Buttons: Change Logo & Remove Logo -->
        <div class="god-logo-actions">
          <button type="button" class="btn god-logo-btn god-change-logo-btn" id="client_change_logo_btn">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"/>
              <circle cx="12" cy="13" r="4"/>
            </svg>
            <span>Change Logo</span>
          </button>

          <button type="button"
                  class="btn god-logo-btn god-remove-logo-btn"
                  id="client_remove_logo_btn"
                  style="{{ !empty($background_image) ? 'display:flex;' : 'display:none;' }}">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <polyline points="3 6 5 6 21 6"/>
              <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
            </svg>
            <span>Remove Logo</span>
          </button>
        </div>

        <!-- Supported formats helper note -->
        <div class="god-logo-formats-note">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="12" cy="12" r="10"/>
            <line x1="12" y1="16" x2="12" y2="12"/>
            <line x1="12" y1="8" x2="12.01" y2="8"/>
          </svg>
          <span>Supported formats: PNG, JPG, WEBP, SVG (Max 2MB).<br>Recommended: 400×400px. Used in job postings & client portals.</span>
        </div>

      </section>
    </div>

  </div>
</div>

@if($isProfileLocked)
<style>
/* Enterprise Locked Field Restricted Modal Styling */
.jero-locked-modal-popup {
    border-radius: 16px !important;
    padding: 24px 22px 22px !important;
    max-width: 430px !important;
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04) !important;
    border: 1px solid #e2e8f0 !important;
    background: #ffffff !important;
    font-family: 'Poppins', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif !important;
}
.jero-locked-modal-popup .swal2-html-container {
    margin: 0 !important;
    padding: 0 !important;
    overflow: visible !important;
}
.jero-locked-modal-popup .swal2-actions {
    margin: 18px 0 0 0 !important;
    padding: 0 !important;
    gap: 0 !important;
}
.jero-locked-modal-ok-btn {
    background-color: #ffc107 !important;
    color: #0f172a !important;
    font-weight: 600 !important;
    font-size: 13.5px !important;
    line-height: 1.5 !important;
    padding: 8px 38px !important;
    border-radius: 8px !important;
    border: 1px solid #f59e0b !important;
    box-shadow: 0 2px 4px rgba(245, 158, 11, 0.2) !important;
    cursor: pointer !important;
    transition: all 0.15s ease-in-out !important;
    outline: none !important;
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    font-family: inherit !important;
}
.jero-locked-modal-ok-btn:hover {
    background-color: #f59e0b !important;
    color: #ffffff !important;
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(245, 158, 11, 0.3) !important;
}
.jero-locked-modal-ok-btn:active {
    transform: translateY(0);
}
</style>
<script>
(function() {
    var isWarningOpen = false;

    function escapeHtml(str) {
        if (!str) return '';
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function resolveFieldName(target) {
        if (!target) return 'Company Details';
        if (typeof target === 'string') {
            var trimmed = target.trim();
            if (trimmed) return trimmed;
        }

        // 1. Closest element or ancestor with data-locked-field-name
        if (target.nodeType && typeof target.closest === 'function') {
            var elWithData = target.closest('[data-locked-field-name]');
            if (elWithData) {
                var name = elWithData.getAttribute('data-locked-field-name');
                if (name && name.trim()) return name.trim();
            }
        }

        // 2. Direct attribute check if target is an element
        if (target.getAttribute && typeof target.getAttribute === 'function') {
            var directName = target.getAttribute('data-locked-field-name');
            if (directName && directName.trim()) return directName.trim();
        }

        // 3. Identification by ID, name attribute, or for attribute
        var id = target.id || '';
        var nameAttr = (target.getAttribute && typeof target.getAttribute === 'function') ? (target.getAttribute('name') || '') : '';
        var forAttr = (target.getAttribute && typeof target.getAttribute === 'function') ? (target.getAttribute('for') || '') : '';

        if (id === 'website' || nameAttr === 'website' || forAttr === 'website' || id === 'client_auto_fetch' || (target.closest && target.closest('.god-website-control-row'))) {
            return 'Company Website';
        }
        if (id === 'client_id' || id === 'hidden_company_name' || nameAttr === 'name' || nameAttr === 'company_name' || forAttr === 'client_id') {
            return 'Company Name';
        }
        if (id === 'industry_display' || id === 'industry' || nameAttr === 'industry' || nameAttr === 'industries' || forAttr === 'industry_display' || (target.closest && target.closest('#jero_ind_select_wrap'))) {
            return 'Industry';
        }
        if (id === 'company_type_display' || id === 'company_type' || nameAttr === 'company_type' || nameAttr === 'industries_type' || forAttr === 'company_type_display' || (target.closest && target.closest('#jero_ctype_select_wrap'))) {
            return 'Company Type';
        }

        return 'Company Details';
    }

    function showLockedCompanyFieldWarning(targetOrName) {
        if (isWarningOpen) return;
        if (typeof Swal !== 'undefined' && typeof Swal.isVisible === 'function' && Swal.isVisible()) {
            return;
        }

        isWarningOpen = true;

        var resolvedField = resolveFieldName(targetOrName);
        var title = @json($warningTitle);
        var message = (resolvedField && resolvedField !== 'Company Details')
            ? "You can't edit " + resolvedField + ". Please contact your Administrator for assistance."
            : @json($warningMessage);

        var modalHtml = '<div class="jero-locked-modal-content" style="text-align: center; font-family: \'Poppins\', sans-serif;">'
            + '<div class="jero-locked-modal-icon-badge" style="width: 58px; height: 58px; margin: 4px auto 14px; border-radius: 50%; background: #fffbeb; border: 1.5px solid #fde68a; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 12px rgba(245, 158, 11, 0.12);">'
            + '  <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="#d97706" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">'
            + '    <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>'
            + '    <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>'
            + '  </svg>'
            + '</div>'
            + '<h3 class="jero-locked-modal-title" style="font-size: 19px; font-weight: 700; color: #0f172a; margin: 0 0 8px; line-height: 1.3;">' + escapeHtml(title) + '</h3>'
            + '<div class="jero-locked-field-badge" style="display: inline-flex; align-items: center; gap: 6px; padding: 4px 12px; background: #fff7ed; color: #c2410c; border: 1px solid #fed7aa; border-radius: 9999px; font-size: 12px; font-weight: 600; margin-bottom: 12px;">'
            + '  <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>'
            + '  <span>' + escapeHtml(resolvedField) + ' Locked</span>'
            + '</div>'
            + '<p class="jero-locked-modal-desc" style="font-size: 13.5px; line-height: 1.55; color: #475569; margin: 0 0 4px;">'
            + '  You can\'t edit <strong style="color: #0f172a; font-weight: 600;">' + escapeHtml(resolvedField) + '</strong>. Please contact your Administrator for assistance.'
            + '</p>'
            + '</div>';

        if (typeof Swal !== 'undefined' && typeof Swal.fire === 'function') {
            Swal.fire({
                html: modalHtml,
                showConfirmButton: true,
                confirmButtonText: 'OK',
                buttonsStyling: false,
                customClass: {
                    popup: 'jero-locked-modal-popup',
                    confirmButton: 'jero-locked-modal-ok-btn'
                },
                width: '420px',
                padding: '24px 20px',
                focusConfirm: true,
                didClose: function() {
                    setTimeout(function() {
                        isWarningOpen = false;
                    }, 200);
                }
            }).then(function() {
                setTimeout(function() {
                    isWarningOpen = false;
                }, 200);
            });
        } else {
            alert(title + "\n\n" + message);
            setTimeout(function() {
                isWarningOpen = false;
            }, 200);
        }
    }

    window.showLockedCompanyFieldWarning = showLockedCompanyFieldWarning;

    function interceptLockedInteraction(e) {
        if (e) {
            if (e.preventDefault) e.preventDefault();
            if (e.stopPropagation) e.stopPropagation();
            if (e.stopImmediatePropagation) e.stopImmediatePropagation();
        }
        var target = e ? (e.target || e.currentTarget) : null;
        if (target && typeof target.blur === 'function') {
            try { target.blur(); } catch (err) {}
        }
        showLockedCompanyFieldWarning(target);
        return false;
    }

    function initLockedInterceptors() {
        var lockedSelectors = [
            '#website',
            '#client_id',
            '#industry_display',
            '#company_type_display',
            '#client_auto_fetch',
            '#jero_ind_select_wrap',
            '#jero_ctype_select_wrap',
            '.is-locked-company-field',
            '[data-company-field-locked="true"]',
            '[data-locked-field-name]'
        ];

        var elements = document.querySelectorAll(lockedSelectors.join(','));
        elements.forEach(function(el) {
            // Mouse interactions
            ['click', 'mousedown', 'mouseup', 'dblclick'].forEach(function(evt) {
                el.addEventListener(evt, interceptLockedInteraction, true);
            });

            // Focus interactions (e.g. keyboard Tab navigation)
            ['focus', 'focusin'].forEach(function(evt) {
                el.addEventListener(evt, function(e) {
                    if (el && typeof el.blur === 'function') {
                        try { el.blur(); } catch (err) {}
                    }
                    interceptLockedInteraction(e);
                }, true);
            });

            // Keyboard interactions
            ['keydown', 'keypress', 'keyup'].forEach(function(evt) {
                el.addEventListener(evt, interceptLockedInteraction, true);
            });

            // Input, paste, cut and change interactions
            ['input', 'beforeinput', 'paste', 'cut', 'change'].forEach(function(evt) {
                el.addEventListener(evt, interceptLockedInteraction, true);
            });
        });

        // Also intercept clicks on label tags
        var labelSelectors = [
            'label[for="website"]',
            'label[for="client_id"]',
            'label[for="industry_display"]',
            'label[for="company_type_display"]'
        ];
        var labels = document.querySelectorAll(labelSelectors.join(','));
        labels.forEach(function(lbl) {
            ['click', 'mousedown'].forEach(function(evt) {
                lbl.addEventListener(evt, interceptLockedInteraction, true);
            });
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initLockedInterceptors);
    } else {
        initLockedInterceptors();
    }

    // Capture-phase document delegation for dynamically rendered or tab-switched DOM nodes
    document.addEventListener('click', function(e) {
        var locked = e.target && e.target.closest && e.target.closest('.is-locked-company-field, [data-company-field-locked="true"], [data-locked-field-name]');
        if (locked) {
            interceptLockedInteraction(e);
        }
    }, true);

    document.addEventListener('keydown', function(e) {
        var locked = e.target && e.target.closest && e.target.closest('.is-locked-company-field, [data-company-field-locked="true"], [data-locked-field-name]');
        if (locked) {
            interceptLockedInteraction(e);
        }
    }, true);

    document.addEventListener('paste', function(e) {
        var locked = e.target && e.target.closest && e.target.closest('.is-locked-company-field, [data-company-field-locked="true"], [data-locked-field-name]');
        if (locked) {
            interceptLockedInteraction(e);
        }
    }, true);
})();
</script>
@endif
