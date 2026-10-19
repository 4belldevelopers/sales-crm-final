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

        <!-- 2-Column Fields Grid (8 Fields Total) -->
        <div class="god-fields-grid">

          <!-- ROW 1, COL 1: Company Website with Fetch Details Button -->
          <div class="client-field fv-plugins-icon-container god-field-wrap">
            <div class="god-field-header">
              <label class="god-field-label" for="website">Company Website</label>
              <span class="god-badge god-badge-autofilled" id="badge_website" style="{{ !empty($website) ? '' : 'display:none;' }}">
                <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                <span>Auto-filled</span>
              </span>
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

          <!-- ROW 1, COL 2: Company Name -->
          <div class="client-field fv-plugins-icon-container god-field-wrap">
            <div class="god-field-header">
              <label class="god-field-label" for="client_id">Company Name</label>
              <span class="god-badge god-badge-autofilled" id="badge_name" style="{{ !empty($name) ? '' : 'display:none;' }}">
                <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                <span>Auto-filled</span>
              </span>
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

          <!-- ROW 2, COL 1: Industry (Combobox with JeroIndustryIntelligenceEngine) -->
          <div class="client-field fv-plugins-icon-container god-field-wrap">
            <div class="god-field-header">
              <label class="god-field-label" for="industry_display">Industry</label>
              <span class="god-badge god-badge-autofilled" id="badge_industry" style="{{ !empty($industry) ? '' : 'display:none;' }}">
                <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                <span>Auto-filled</span>
              </span>
            </div>

            <!-- Native select kept synchronized for form POST & validation -->
            <select name="industry" id="industry" style="position:absolute; width:1px; height:1px; opacity:0; pointer-events:none;" tabindex="-1" aria-hidden="true">
              <option value="">Select industry</option>
              @if(!empty($department))
                @foreach($department as $key => $val)
                  <option value="{{ $val->ind_id }}" {{ $val->ind_id == $industry ? 'selected="selected"' : '' }}>{{ ucwords($val->ind_name) }}</option>
                @endforeach
              @endif
            </select>

            <!-- God-Level Enterprise Industry Combobox Component -->
            <div class="jero-ind-select-wrap god-ind-wrap" id="jero_ind_select_wrap">
              <div class="god-input-box jero-ind-input-box {{ $selectedIndName ? 'has-value' : '' }}" id="jero_ind_input_box">
                <span class="god-input-left-icon jero-ind-search-icon">
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="4" y="2" width="16" height="20" rx="2" ry="2"/>
                    <line x1="9" y1="6" x2="9" y2="6.01"/>
                    <line x1="15" y1="6" x2="15" y2="6.01"/>
                    <path d="M10 22v-4h4v4"/>
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
                <div class="jero-ind-tabs" id="jero_ind_tabs">
                  <button type="button" class="jero-ind-tab-btn active" data-sector="all">All <span class="tab-count" id="jero_ind_count_all">0</span></button>
                </div>
                <div class="jero-ind-recent-bar" id="jero_ind_recent_bar" style="display:none;"></div>
                <div class="jero-ind-list-container" id="jero_ind_list_container"></div>
                <div class="jero-ind-footer">
                  <div class="jero-ind-sync-status" id="jero_ind_sync_status">
                    <span class="jero-ind-sync-dot"></span>
                    <span>Live Synced</span>
                  </div>
                  <span class="jero-ind-footer-hint">↑↓ Navigate • Enter Select • Esc Close</span>
                </div>
              </div>
            </div>
          </div>

          <!-- ROW 2, COL 2: Company Size -->
          <div class="client-field fv-plugins-icon-container god-field-wrap">
            <div class="god-field-header">
              <label class="god-field-label" for="company_sizes">Company Size</label>
              <span class="god-badge god-badge-suggested" id="badge_company_sizes" style="{{ !empty($company_sizes) ? '' : 'display:none;' }}">
                <span class="god-badge-dot">●</span>
                <span>Suggested</span>
              </span>
            </div>
            <div class="god-input-box god-select-box">
              <span class="god-input-left-icon">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
                  <circle cx="9" cy="7" r="4"/>
                  <path d="M22 21v-2a4 4 0 0 0-3-3.87"/>
                  <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                </svg>
              </span>
              <select name="company_sizes" class="god-native-select" id="company_sizes">
                <option value="">Select company size</option>
                <option value="1-10"         {{ @$company_sizes == '1-10'         ? 'selected' : '' }}>1–10 employees</option>
                <option value="11-50"        {{ @$company_sizes == '11-50'        ? 'selected' : '' }}>11–50 employees</option>
                <option value="51-200"       {{ @$company_sizes == '51-200'       ? 'selected' : '' }}>51–200 employees</option>
                <option value="201-500"      {{ @$company_sizes == '201-500'      ? 'selected' : '' }}>201–500 employees</option>
                <option value="501-1000"     {{ @$company_sizes == '501-1000'     ? 'selected' : '' }}>501–1,000 employees</option>
                <option value="1001-5000"    {{ @$company_sizes == '1001-5000'    ? 'selected' : '' }}>1,001–5,000</option>
                <option value="5001-10000"   {{ @$company_sizes == '5001-10000'   ? 'selected' : '' }}>5,001–10,000 employees</option>
                <option value="10000-999999" {{ @$company_sizes == '10000-999999' ? 'selected' : '' }}>10,000+ employees</option>
              </select>
              <span class="god-input-right-chevron">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m6 9 6 6 6-6"/></svg>
              </span>
            </div>
          </div>

          <!-- ROW 3, COL 1: Company Type -->
          <div class="client-field fv-plugins-icon-container god-field-wrap">
            <div class="god-field-header">
              <label class="god-field-label" for="company_type">Company Type</label>
              <span class="god-badge god-badge-autofilled" id="badge_company_type" style="{{ isset($company_type) && $company_type !== '' ? '' : 'display:none;' }}">
                <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                <span>Auto-filled</span>
              </span>
            </div>
            <div class="god-input-box god-select-box">
              <span class="god-input-left-icon">
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
              <select class="god-native-select" name="company_type" id="company_type">
                <option value="">Select company type</option>
                @if(!empty($companyType))
                  @foreach($companyType as $key => $val)
                    <option value="{{ $key }}" {{ $key == $company_type ? 'selected="selected"' : '' }}>{{ ucwords($val) }}</option>
                  @endforeach
                @endif
              </select>
              <span class="god-input-right-chevron">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m6 9 6 6 6-6"/></svg>
              </span>
            </div>
          </div>

          <!-- ROW 3, COL 2: Founded Year -->
          <div class="client-field fv-plugins-icon-container god-field-wrap">
            <div class="god-field-header">
              <label class="god-field-label" for="founded">Founded Year</label>
              <span class="god-badge god-badge-autofilled" id="badge_founded" style="{{ !empty($founded) ? '' : 'display:none;' }}">
                <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                <span>Auto-filled</span>
              </span>
            </div>
            <div class="god-input-box">
              <span class="god-input-left-icon">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                  <line x1="16" y1="2" x2="16" y2="6"/>
                  <line x1="8" y1="2" x2="8" y2="6"/>
                  <line x1="3" y1="10" x2="21" y2="10"/>
                </svg>
              </span>
              <input type="number"
                     class="god-native-input"
                     name="founded"
                     id="founded"
                     value="{{ $founded ?? '' }}"
                     placeholder="2010"
                     min="1800"
                     max="2099">
            </div>
          </div>

          <!-- ROW 4, COL 1: Career / Jobs Page URL with External Link Button -->
          <div class="client-field fv-plugins-icon-container god-field-wrap">
            <div class="god-field-header">
              <label class="god-field-label" for="career_page_url">Career / Jobs Page URL</label>
              <span class="god-badge god-badge-autofilled" id="badge_career_page_url" style="{{ !empty($career_page_url) ? '' : 'display:none;' }}">
                <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                <span>Auto-filled</span>
              </span>
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

          <!-- ROW 4, COL 2: Tax ID / Business Reg No with Helper Note -->
          <div class="client-field fv-plugins-icon-container god-field-wrap">
            <div class="god-field-header">
              <label class="god-field-label" for="tax_id">Tax ID / Business Reg No</label>
              <span class="god-badge {{ !empty($tax_id) ? 'god-badge-autofilled' : 'god-badge-notfound' }}" id="badge_tax_id">
                @if(!empty($tax_id))
                  <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                  <span>Auto-filled</span>
                @else
                  <span class="god-badge-dot">●</span>
                  <span>Not found</span>
                @endif
              </span>
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
            <div class="god-field-helper-note" id="tax_id_helper" style="{{ !empty($tax_id) ? 'display:none;' : '' }}">
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="10"/>
                <line x1="12" y1="16" x2="12" y2="12"/>
                <line x1="12" y1="8" x2="12.01" y2="8"/>
              </svg>
              <span>Not available from website. You can add this later.</span>
            </div>
          </div>

        </div>

        <!-- Step 1 Continuation Footer Button -->
        <div class="god-step-footer mt-4 pt-2 d-flex justify-content-end align-items-center">
          <button type="button" class="btn client-top-next-btn client-nav-next god-continue-next-btn" id="continueButton">
            <span>Continue to Description</span>
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
              <path d="M5 12h14"/>
              <path d="m12 5 7 7-7 7"/>
            </svg>
          </button>
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
