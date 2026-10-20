{{--
  God-Level Add Internal Client — Page 1 (Company Profile & Company Logo)
  Pixel-matched to user reference image (Reference: media_1792403786709_d614e0b3.png)
  Preserves 100% of Form Bindings, tenanted scope, and JeroIndustryIntelligenceEngine integration
  Optimized for 15.6" Screens (1366x768 & 1920x1080) and smaller screens
--}}
<?php
  $selectedIndName = '';
  if (!empty($industry) && !empty($department)) {
      foreach ($department as $val) {
          if ($val->ind_id == $industry) {
              $selectedIndName = ucwords($val->ind_name);
              break;
          }
      }
  }
  if (empty($selectedIndName) && !empty($custom_industry)) {
      $selectedIndName = $custom_industry;
  }
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
            <h3 class="god-card-title" id="god_company_profile_title">Company Profile</h3>
            <p class="god-card-subtitle">Review and update the information extracted from the company website.</p>
          </div>
        </div>

        <!-- 2-Column Fields Grid (Company Website & Name on separate rows) -->
        <div class="god-fields-grid">

          <!-- ROW 1 (Full Width): Company Website with Fetch Details Button -->
          <div class="client-field fv-plugins-icon-container god-field-wrap god-field-full">
            <div class="god-field-header">
              <label class="god-field-label" for="website">Company Website</label>
            </div>

            <div class="god-website-control-row">
              <div class="god-input-box god-website-input-box">
                <span class="god-input-left-icon">
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/>
                    <path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/>
                  </svg>
                </span>
                <input type="text"
                       class="god-native-input"
                       name="website"
                       id="website"
                       value="{{ $website ?? '' }}"
                       placeholder="https://stripe.com"
                       autocomplete="off"
                       spellcheck="false"
                       onkeydown="if(event.key==='Enter'||event.keyCode===13){event.preventDefault();if(window.fetchGodLevelCompanyIntelligence)window.fetchGodLevelCompanyIntelligence(this.value);return false;}"
                       onblur="if(this.value && this.value.length > 3 && this.value !== window._lastFetchedWebsite && window.fetchGodLevelCompanyIntelligence) window.fetchGodLevelCompanyIntelligence(this.value);">
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
              </div>

              <!-- Bright Yellow Fetch Details Button -->
              <button type="button"
                      class="btn god-fetch-btn"
                      id="client_auto_fetch"
                      title="Fetch and auto-fill company intelligence"
                      onclick="if(window.fetchGodLevelCompanyIntelligence)window.fetchGodLevelCompanyIntelligence(document.getElementById('website').value); return false;">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="currentColor">
                  <path d="m12 3-1.9 5.8a2 2 0 0 1-1.3 1.3L3 12l5.8 1.9a2 2 0 0 1 1.3 1.3L12 21l1.9-5.8a2 2 0 0 1 1.3-1.3L21 12l-5.8-1.9a2 2 0 0 1-1.3-1.3L12 3z"/>
                </svg>
                <span>Fetch Details</span>
              </button>
            </div>
          </div>

          <!-- ROW 2 (Full Width): Company Name -->
          <div class="client-field fv-plugins-icon-container god-field-wrap god-field-full">
            <div class="god-field-header">
              <label class="god-field-label" for="client_id">Company Name</label>
            </div>
            <div class="god-input-box">
              <span class="god-input-left-icon">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <rect x="4" y="2" width="16" height="20" rx="2" ry="2"/>
                  <line x1="9" y1="6" x2="9" y2="6.01"/>
                  <line x1="15" y1="6" x2="15" y2="6.01"/>
                  <line x1="9" y1="10" x2="9" y2="10.01"/>
                  <line x1="15" y1="10" x2="15" y2="10.01"/>
                  <path d="M10 22v-4h4v4"/>
                </svg>
              </span>
              <input type="text"
                     class="god-native-input"
                     name="name"
                     id="client_id"
                     value="{{ $name ?? '' }}"
                     placeholder="e.g. Stripe">
            </div>
          </div>

          <!-- ROW 3, COL 1: Industry (Combobox with JeroIndustryIntelligenceEngine) -->
          <div class="client-field fv-plugins-icon-container god-field-wrap">
            <div class="god-field-header">
              <label class="god-field-label" for="industry_display">Industry</label>
            </div>

            <!-- Native select kept synchronized for form POST & validation -->
            <select name="industry" id="industry" style="position:absolute; width:1px; height:1px; opacity:0; pointer-events:none;" tabindex="-1" aria-hidden="true">
              <option value="">Select industry</option>
              @if(!empty($department))
                @foreach($department as $key => $val)
                  <option value="{{ $val->ind_id }}" {{ $val->ind_id == $industry ? 'selected="selected"' : '' }}>{{ ucwords($val->ind_name) }}</option>
                @endforeach
              @endif
              @if(!empty($custom_industry) && $industry == 0)
                <option value="0" selected="selected">{{ $custom_industry }}</option>
              @endif
            </select>
            <input type="hidden" name="custom_industry" id="custom_industry" value="{{ htmlspecialchars($custom_industry ?? '') }}">

            <!-- God-Level Enterprise Industry Combobox Component -->
            <div class="jero-ind-select-wrap god-ind-wrap" id="jero_ind_select_wrap">
              <div class="god-input-box jero-ind-input-box {{ $selectedIndName ? 'has-value' : '' }}" id="jero_ind_input_box">
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
                       class="god-native-input jero-ind-input"
                       id="industry_display"
                       placeholder="Select industry"
                       autocomplete="off"
                       spellcheck="false"
                       role="combobox"
                       aria-expanded="false"
                       aria-haspopup="listbox"
                       aria-autocomplete="list"
                       value="{{ htmlspecialchars($selectedIndName) }}">
                <kbd class="jero-ind-kbd" id="jero_ind_kbd" title="Press / to search" style="{{ $selectedIndName ? 'display:none;' : '' }}">/</kbd>
                <button type="button" class="god-input-clear-btn jero-ind-clear-btn" id="jero_ind_clear_btn" aria-label="Clear industry" title="Clear selection" style="{{ $selectedIndName ? 'display:flex;' : 'display:none;' }}">
                  <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M18 6 6 18M6 6l12 12"/></svg>
                </button>
                <div class="god-input-right-chevron jero-ind-chevron" id="jero_ind_chevron" title="Toggle industry list" role="button" aria-label="Toggle industry list" tabindex="-1">
                  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m6 9 6 6 6-6"/></svg>
                </div>
              </div>

              <!-- Hierarchical Dropdown Menu Container -->
              <div class="jero-ind-dropdown" id="jero_ind_dropdown" style="display:none;" role="listbox" aria-label="Client Industries">
                <div class="jero-ind-tabs-wrap">
                  <button type="button" class="jero-ind-tab-nav jero-ind-tab-prev" id="jero_ind_tab_prev" aria-label="Scroll left" tabindex="-1">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"/></svg>
                  </button>
                  <div class="jero-ind-tabs" id="jero_ind_tabs">
                    <button type="button" class="jero-ind-tab-btn active" data-sector="all">All <span class="tab-count" id="jero_ind_count_all">0</span></button>
                  </div>
                  <button type="button" class="jero-ind-tab-nav jero-ind-tab-next" id="jero_ind_tab_next" aria-label="Scroll right" tabindex="-1">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"/></svg>
                  </button>
                </div>
                <div class="jero-ind-list-container" id="jero_ind_list_container"></div>
              </div>
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
                <button type="button" class="god-input-clear-btn jero-csize-clear-btn" id="jero_csize_clear_btn" aria-label="Clear company size" title="Clear selection" style="{{ $selectedSizeLabel ? 'display:flex;' : 'display:none;' }}">
                  <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M18 6 6 18M6 6l12 12"/></svg>
                </button>
                <div class="god-input-right-chevron jero-csize-chevron" id="jero_csize_chevron" title="Toggle company size list" role="button" aria-label="Toggle company size list" tabindex="-1">
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
              <label class="god-field-label" for="company_type_display">Company Type</label>
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

            <!-- God-Level Enterprise Company Type Combobox -->
            <div class="jero-ctype-select-wrap god-ctype-wrap" id="jero_ctype_select_wrap">
              <div class="god-input-box jero-ctype-input-box {{ $selectedCompTypeName ? 'has-value' : '' }}" id="jero_ctype_input_box">
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
                       class="god-native-input jero-ctype-input"
                       id="company_type_display"
                       placeholder="Select company type (e.g. LLC, GmbH, Pvt Ltd, Inc)"
                       autocomplete="off"
                       spellcheck="false"
                       role="combobox"
                       aria-expanded="false"
                       aria-haspopup="listbox"
                       aria-autocomplete="list"
                       aria-controls="jero_ctype_dropdown"
                       value="{{ htmlspecialchars($selectedCompTypeName) }}">
                <kbd class="jero-ctype-kbd" id="jero_ctype_kbd" title="Press / to search" style="{{ $selectedCompTypeName ? 'display:none;' : '' }}">/</kbd>
                <button type="button" class="god-input-clear-btn jero-ctype-clear-btn" id="jero_ctype_clear_btn" aria-label="Clear company type" title="Clear selection" style="{{ $selectedCompTypeName ? 'display:flex;' : 'display:none;' }}">
                  <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M18 6 6 18M6 6l12 12"/></svg>
                </button>
                <div class="god-input-right-chevron jero-ctype-chevron" id="jero_ctype_chevron" title="Toggle company type list" role="button" aria-label="Toggle company type list" tabindex="-1">
                  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m6 9 6 6 6-6"/></svg>
                </div>
              </div>

              <!-- Dropdown Menu Container (Search-First) -->
              <div class="jero-ctype-dropdown" id="jero_ctype_dropdown" style="display:none;" role="listbox" aria-label="Company Types">
                <div class="jero-ctype-list-container" id="jero_ctype_list_container"></div>
              </div>
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
                <kbd class="jero-founded-kbd" id="jero_founded_kbd" title="Press / to search" style="{{ !empty($founded) ? 'display:none;' : '' }}">/</kbd>
                <button type="button" class="god-input-clear-btn jero-founded-clear-btn" id="jero_founded_clear_btn" aria-label="Clear founded year" title="Clear selection" style="{{ !empty($founded) ? 'display:flex;' : 'display:none;' }}">
                  <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M18 6 6 18M6 6l12 12"/></svg>
                </button>
                <div class="god-input-right-chevron jero-founded-chevron" id="jero_founded_chevron" title="Toggle founded year list" role="button" aria-label="Toggle founded year list" tabindex="-1">
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
              <label class="god-field-label" for="tax_id">Tax ID / Business Reg No</label>
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
               accept=".png, .jpg, .jpeg, .webp"
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
          <span>Supported formats: PNG, JPG, WEBP (Max 2MB).<br>Used in job postings, candidate submissions, and client portals.</span>
        </div>

      </section>
    </div>

  </div>
</div>
