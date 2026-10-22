<!--begin::Company Locations & Address Details (God-Level Design Aligned with Enterprise SaaS Standard)-->
@php
    $primary_city = '';
    $primary_tagify_json = '';
    $primary_address = '';
    $additional_addresses = [];
    $addressId = 0;

    if (($page_event ?? '') !== 'ADD' && isset($client_location_arr) && is_iterable($client_location_arr)) {
        foreach ($client_location_arr as $idx => $clVal) {
            $cName = $clVal->city ?? '';
            $sName = '';
            $coName = $clVal->country_name ?? '';

            if (!empty($clVal->city_id)) {
                $cRow = DB::table('city_list')
                    ->leftJoin('state_list', 'state_list.id', '=', 'city_list.state_id')
                    ->leftJoin('countries', 'countries.id', '=', 'state_list.country_id')
                    ->select('city_list.city', 'state_list.state', 'countries.country_name')
                    ->where('city_list.city_id', $clVal->city_id)
                    ->first();
                if ($cRow) {
                    $cName = $cRow->city ?: $cName;
                    $sName = $cRow->state ?: '';
                    $coName = $cRow->country_name ?: $coName;
                }
            }

            $canonical = $cName . ($sName ? ', ' . $sName : '') . ($coName ? ', ' . $coName : '');
            $tagifyJson = json_encode([['value' => $cName . ($sName ? ' | ' . $sName : '') . ($coName ? ' | ' . $coName : '')]]);

            if ($idx === 0) {
                $primary_city = $canonical;
                $primary_tagify_json = $tagifyJson;
                $primary_address = $clVal->address ?? '';
            } else {
                $additional_addresses[] = [
                    'id' => $idx,
                    'address' => $clVal->address ?? '',
                    'city' => $canonical,
                    'tagify_json' => $tagifyJson,
                ];
                $addressId = $idx;
            }
        }
    }
@endphp

<div class="pb-5 client-address-step" data-wizard-type="step-content">
  
  <!-- ==========================================
       TOP INFORMATIONAL BANNER
       ========================================== -->
  <div class="client-addr-top-banner mb-3">
    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="flex-shrink-0">
      <circle cx="12" cy="12" r="10"></circle>
      <line x1="12" y1="16" x2="12" y2="12"></line>
      <line x1="12" y1="8" x2="12.01" y2="8"></line>
    </svg>
    <span>The first location entered is designated as the primary headquarters. You can adjust headquarters or add branch offices at any time.</span>
  </div>

  <div class="client-step-layout client-address-layout">
    
    <!-- ==========================================
         LEFT MAIN COLUMN: Company Locations
         ========================================== -->
    <div class="client-main-column">
      
      <!-- ==========================================
           CARD 1: PRIMARY ADDRESS INFORMATION CARD (MAIN CONTAINER)
           ========================================== -->
      <section class="card client-section-card client-addr-primary-card mb-3" id="client_addr_card_primary">
        <!-- Card Header -->
        <div class="card-header border-0 d-flex align-items-center justify-content-between">
          <div class="d-flex align-items-center gap-3">
            <div class="client-addr-card-icon-box">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#ca8a04" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                <polyline points="9 22 9 12 15 12 15 22"></polyline>
              </svg>
            </div>
            <div>
              <h3 class="client-section-heading mb-0">Address Information</h3>
              <p class="client-section-copy mb-0">Primary corporate headquarters and registered office location.</p>
            </div>
          </div>
          <span class="badge client-hq-badge">
            HEADQUARTERS
          </span>
        </div>

        <!-- Card Body containing Headquarters Sub-Card -->
        <div class="card-body">
          <!-- SUB-CARD: Headquarters & Registered Office -->
          <div class="client-subcard client-hq-subcard" id="client_addr_card_0">
            <div class="client-subcard-header d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom">
              <div class="d-flex align-items-center gap-2">
                <div class="client-subcard-icon-box">
                  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#ca8a04" stroke-width="2">
                    <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                    <polyline points="9 22 9 12 15 12 15 22"></polyline>
                  </svg>
                </div>
                <div>
                  <h4 class="client-subcard-title mb-0">Headquarters (Main Office)</h4>
                  <span class="client-subcard-subtitle">Primary operational and official billing location</span>
                </div>
              </div>
              <span class="badge badge-hq-pill">PRIMARY</span>
            </div>

            <div class="client-subcard-body">
              <!-- Field: City -->
              <div class="client-addr-field mb-3">
                <label class="form-label mb-1" for="city_search_0">City / Location <span class="text-danger">*</span></label>
                <div class="position-relative client-addr-input-wrap">
                  <span class="position-absolute client-addr-field-icon-left">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                      <circle cx="11" cy="11" r="8"></circle>
                      <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                    </svg>
                  </span>
                  <input type="text" class="form-control client-addr-input client-addr-city-search" id="city_search_0" data-cid="0" value="{{ $primary_city }}" placeholder="Search for a city..." autocomplete="off" spellcheck="false">
                  <span class="position-absolute client-addr-field-icon-right">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                      <polyline points="6 9 12 15 18 9"></polyline>
                    </svg>
                  </span>
                  <div class="client-addr-city-dropdown" id="city_dropdown_0"></div>
                  <input type="hidden" name="kt_tagify_work_location[]" id="kt_tagify_work_location_0" value="{{ $primary_tagify_json }}">
                </div>
                <!-- Error indicator -->
                <div class="client-addr-error text-danger mt-1" id="city_err_0" style="display:none;">
                  <svg width="13" height="13" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/></svg>
                  <span>City selection is required for headquarters.</span>
                </div>
              </div>

              <!-- Field: Full Address -->
              <div class="client-addr-field">
                <label class="form-label mb-1" for="address_0">Full Address <span class="text-danger">*</span></label>
                <div class="position-relative client-addr-textarea-wrap">
                  <span class="position-absolute client-addr-field-icon-top">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                      <path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"></path>
                      <circle cx="12" cy="10" r="3"></circle>
                    </svg>
                  </span>
                  <textarea class="form-control client-addr-textarea" name="address[]" id="address_0" rows="2" maxlength="500" placeholder="Building name, street, area, landmark, pincode, etc.">{{ $primary_address }}</textarea>
                </div>
                <div class="d-flex align-items-center justify-content-between mt-1">
                  <span class="text-muted client-field-hint">Provide complete registered headquarters address.</span>
                  <span class="client-addr-counter"><span id="address_char_count_0">{{ strlen($primary_address) }}</span>/500</span>
                </div>
                <!-- Error indicator -->
                <div class="client-addr-error text-danger mt-1" id="address_err_0" style="display:none;">
                  <svg width="13" height="13" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/></svg>
                  <span>Full Address is required for headquarters.</span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>

      <!-- ==========================================
           CARD 2: DEDICATED ADDITIONAL LOCATIONS CARD
           ========================================== -->
      <section class="card client-section-card client-addr-additional-card" id="client_addr_additional_card">
        <!-- Card Header -->
        <div class="card-header border-0 d-flex align-items-center justify-content-between">
          <div class="d-flex align-items-center gap-3">
            <div class="client-addr-card-icon-box">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#ca8a04" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M3 21h18"></path>
                <path d="M5 21V7l8-4v18"></path>
                <path d="M19 21V11l-6-4"></path>
              </svg>
            </div>
            <div>
              <h3 class="client-section-heading mb-0">Additional Locations (Optional)</h3>
              <p class="client-section-copy mb-0">Add branch offices, development centres or regional hubs.</p>
            </div>
          </div>
          <span class="badge client-branch-count-badge" id="additional_locations_count_badge">
            {{ count($additional_addresses) }} {{ count($additional_addresses) === 1 ? 'Location' : 'Locations' }}
          </span>
        </div>

        <!-- Card Body -->
        <div class="card-body">
          <!-- Subtle Info Notice -->
          <div class="client-addr-notice mb-3">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#ca8a04" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="flex-shrink-0">
              <circle cx="12" cy="12" r="10"></circle>
              <line x1="12" y1="16" x2="12" y2="12"></line>
              <line x1="12" y1="8" x2="12.01" y2="8"></line>
            </svg>
            <span>Additional locations are optional. If added, both <strong>City</strong> and <strong>Full Address</strong> are required (or click <strong>Remove</strong> if not needed).</span>
          </div>

          <!-- Centered Empty State (When 0 locations added) -->
          <div class="client-addr-empty-state {{ empty($additional_addresses) ? '' : 'd-none' }}" id="client_additional_empty_state" style="{{ empty($additional_addresses) ? 'display:flex;' : 'display:none;' }}">
            <div class="empty-state-icon-circle">
              <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                <circle cx="12" cy="10" r="3"></circle>
              </svg>
            </div>
            <h5 class="empty-state-title">No additional locations added yet</h5>
            <p class="empty-state-subtitle">Add branch offices, development centres or regional hubs for this client organization.</p>
            <button type="button" class="btn btn-add-additional-location btn-add-first-location" id="btn_add_first_location">
              <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
              <span>Add Additional Location</span>
            </button>
          </div>

          <!-- Dynamic Sub-Cards Container (Cards appended directly above CTA action) -->
          <div class="client-additional-subcards-wrap" id="additional_addresses_container">
            @if (!empty($additional_addresses))
              @foreach ($additional_addresses as $addIdx => $addrItem)
                <div class="client-subcard client-branch-subcard client-branch-card" id="clientAddr_{{ $addrItem['id'] }}" data-cid="{{ $addrItem['id'] }}">
                  <!-- Sub-card Header -->
                  <div class="client-subcard-header d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom">
                    <div class="d-flex align-items-center gap-2">
                      <div class="client-subcard-icon-box office-icon">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                          <rect x="4" y="2" width="16" height="20" rx="2" ry="2"/>
                          <path d="M9 22v-4h6v4"/>
                          <line x1="8" y1="6" x2="8.01" y2="6"/>
                          <line x1="16" y1="6" x2="16.01" y2="6"/>
                          <line x1="12" y1="6" x2="12.01" y2="6"/>
                          <line x1="8" y1="10" x2="8.01" y2="10"/>
                          <line x1="12" y1="10" x2="12.01" y2="10"/>
                          <line x1="16" y1="10" x2="16.01" y2="10"/>
                          <line x1="8" y1="14" x2="8.01" y2="14"/>
                          <line x1="12" y1="14" x2="12.01" y2="14"/>
                          <line x1="16" y1="14" x2="16.01" y2="14"/>
                        </svg>
                      </div>
                      <div class="d-flex align-items-center gap-2">
                        <h4 class="client-subcard-title client-branch-title mb-0">Additional Location {{ $addIdx + 1 }}</h4>
                        <span class="badge badge-branch-tag">BRANCH</span>
                      </div>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                      <button type="button" class="btn btn-xs btn-set-primary" onclick="window.clientLocationManager ? window.clientLocationManager.setBranchAsHq({{ $addrItem['id'] }}) : false" title="Set as Primary">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="#f59e0b" stroke="#d97706" stroke-width="1.2" class="me-1"><path d="M5 16L3 5l5.5 5L12 4l3.5 6L21 5l-2 11H5m14 3c0 .6-.4 1-1 1H6c-.6 0-1-.4-1-1v-1h14v1z"/></svg>
                        <span>Set as Primary</span>
                      </button>
                      <button type="button" class="btn btn-xs btn-remove-subcard client-branch-remove-btn" onclick="removeAddressCard(this, 'clientAddr_{{ $addrItem['id'] }}')" title="Remove Location">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="me-1"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                        <span>Remove</span>
                      </button>
                    </div>
                  </div>

                  <!-- Sub-card Form Fields -->
                  <div class="client-subcard-body">
                    <!-- Row 1: City / Location -->
                    <div class="client-addr-field mb-2">
                      <label class="form-label mb-1" for="branch_city_search_{{ $addrItem['id'] }}">City / Location <span class="text-danger">*</span></label>
                      <div class="position-relative client-addr-input-wrap">
                        <span class="position-absolute client-addr-field-icon-left">
                          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                        </span>
                        <input type="text" class="form-control client-addr-input client-addr-city-search branch-city-search" id="branch_city_search_{{ $addrItem['id'] }}" data-cid="{{ $addrItem['id'] }}" value="{{ $addrItem['city'] }}" placeholder="Search for a city.." autocomplete="off">
                        <span class="position-absolute client-addr-field-icon-right">
                          <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"></polyline></svg>
                        </span>
                        <div class="client-addr-city-dropdown branch-city-dropdown" id="branch_city_dropdown_{{ $addrItem['id'] }}"></div>
                        <input type="hidden" name="kt_tagify_work_location[]" id="kt_tagify_work_location_{{ $addrItem['id'] }}" value='{{ $addrItem['tagify_json'] }}'>
                      </div>
                      <div class="client-addr-error text-danger mt-1" id="branch_city_err_{{ $addrItem['id'] }}" style="display:none;">City selection is required.</div>
                    </div>

                    <!-- Row 2: Full Address -->
                    <div class="client-addr-field">
                      <div class="position-relative client-addr-textarea-wrap">
                        <span class="position-absolute client-addr-field-icon-top">
                          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg>
                        </span>
                        <textarea class="form-control client-addr-textarea" name="address[]" id="address_{{ $addrItem['id'] }}" rows="2" maxlength="500" placeholder="Enter complete branch address..">{{ $addrItem['address'] }}</textarea>
                      </div>
                      <div class="d-flex align-items-center justify-content-between mt-1">
                        <span class="text-muted client-field-hint">Include building, street, area, city and postal code.</span>
                        <span class="client-addr-counter"><span id="address_char_count_{{ $addrItem['id'] }}">{{ strlen($addrItem['address']) }}</span> / 500</span>
                      </div>
                      <div class="client-addr-error text-danger mt-1" id="branch_address_err_{{ $addrItem['id'] }}" style="display:none;">Full Address is required.</div>
                    </div>
                  </div>
                </div>
              @endforeach
            @endif
          </div>

          <!-- Additional Locations Bottom Action CTA (Shown when >= 1 location added) -->
          <div class="client-add-location-cta-wrap text-center mt-3 {{ empty($additional_addresses) ? 'd-none' : '' }}" id="additional_location_cta_wrap" style="{{ empty($additional_addresses) ? 'display:none;' : 'display:flex;' }}">
            <button type="button" class="btn btn-add-additional-location" id="btn_add_another_address">
              <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
              <span>Add Additional Location</span>
            </button>
          </div>
        </div>
      </section>

      <input type="hidden" name="addressId" id="addressId" value="{{ $addressId }}" />
    </div>

    <!-- ==========================================
         RIGHT SIDEBAR: AI Location Discovery & Tips
         ========================================== -->
    <aside class="client-side-column">
      
      <!-- AI Location Discovery Card -->
      <div class="card client-helper-card client-ai-location-card">
        <!-- Header -->
        <div class="d-flex align-items-center justify-content-between mb-1">
          <div class="d-flex align-items-center gap-2">
            <div class="client-ai-icon-box">
              <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#7040f5" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                <path d="m12 3-1.9 5.8a2 2 0 0 1-1.3 1.3L3 12l5.8 1.9a2 2 0 0 1 1.3 1.3L12 21l1.9-5.8a2 2 0 0 1 1.3-1.3L21 12l-5.8-1.9a2 2 0 0 1-1.3-1.3L12 3z"/>
              </svg>
            </div>
            <h4 class="client-ai-title">AI Location Discovery</h4>
          </div>
          <span class="badge badge-ai-beta">Beta</span>
        </div>
        <p class="client-ai-desc">We found 3 locations from the company website. Review and add them to your client profile.</p>

        <!-- Discovered Location 1: Pune (Recommended HQ) -->
        <div class="client-ai-loc-item mb-2" id="ai_loc_card_0">
          <div class="d-flex align-items-start justify-content-between mb-1">
            <div class="d-flex align-items-start gap-2">
              <div class="ai-loc-symbol-box symbol-hq">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="2"><path d="M3 21h18"/><path d="M5 21V7l8-4v18"/><path d="M19 21V11l-6-4"/></svg>
              </div>
              <div>
                <div class="d-flex align-items-center gap-2">
                  <span class="ai-loc-city-name">Pune, Maharashtra, India</span>
                  <span class="badge badge-ai-recommended">Recommended</span>
                </div>
                <div class="ai-loc-type-label">Corporate Office / Headquarters</div>
                <p class="ai-loc-sub-note">Listed as primary office on the company website.</p>
              </div>
            </div>
          </div>
          <!-- Meta & Actions -->
          <div class="d-flex align-items-center justify-content-between mt-2 pt-1 border-top flex-wrap gap-2 ai-loc-footer-border">
            <div class="d-flex align-items-center gap-2 ai-loc-meta-text">
              <a href="javascript:void(0);" class="client-ai-source-link">
                <span>Source: Website</span>
                <svg width="9" height="9" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path><polyline points="15 3 21 3 21 9"></polyline><line x1="10" y1="14" x2="21" y2="3"></line></svg>
              </a>
              <span class="d-inline-flex align-items-center gap-1 ai-loc-verified">
                <span class="verified-dot"></span> Verified
              </span>
            </div>
            <div class="d-flex align-items-center gap-2" id="ai_loc_actions_0">
              <button type="button" class="btn btn-sm btn-ai-set-hq" onclick="window.clientLocationManager ? window.clientLocationManager.addAiLocation(0, true) : false;">
                Set as Headquarters
              </button>
              <button type="button" class="btn btn-sm btn-ai-add-office" onclick="window.clientLocationManager ? window.clientLocationManager.addAiLocation(0, false) : false;">
                Add as Office
              </button>
            </div>
          </div>
        </div>

        <!-- Discovered Location 2: Bengaluru (Development Centre) -->
        <div class="client-ai-loc-item mb-2" id="ai_loc_card_1">
          <div class="d-flex align-items-start justify-content-between mb-1">
            <div class="d-flex align-items-start gap-2">
              <div class="ai-loc-symbol-box symbol-office">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#475569" stroke-width="2"><path d="M3 21h18"/><path d="M5 21V7l8-4v18"/><path d="M19 21V11l-6-4"/></svg>
              </div>
              <div>
                <span class="ai-loc-city-name">Bengaluru, Karnataka, India</span>
                <div class="ai-loc-type-label">Development Centre</div>
                <p class="ai-loc-sub-note">Listed under company locations page.</p>
              </div>
            </div>
          </div>
          <!-- Meta & Actions -->
          <div class="d-flex align-items-center justify-content-between mt-2 pt-1 border-top flex-wrap gap-2 ai-loc-footer-border">
            <div class="d-flex align-items-center gap-2 ai-loc-meta-text">
              <a href="javascript:void(0);" class="client-ai-source-link">
                <span>Source: Website</span>
                <svg width="9" height="9" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path><polyline points="15 3 21 3 21 9"></polyline><line x1="10" y1="14" x2="21" y2="3"></line></svg>
              </a>
              <span class="d-inline-flex align-items-center gap-1 ai-loc-likely">
                <span class="likely-dot"></span> Likely location
              </span>
            </div>
            <div class="d-flex align-items-center gap-2" id="ai_loc_actions_1">
              <button type="button" class="btn btn-sm btn-ai-add-office" onclick="window.clientLocationManager ? window.clientLocationManager.addAiLocation(1, false) : false;">
                Add as Office
              </button>
            </div>
          </div>
        </div>

        <!-- Discovered Location 3: Mumbai (Office) -->
        <div class="client-ai-loc-item mb-3" id="ai_loc_card_2">
          <div class="d-flex align-items-start justify-content-between mb-1">
            <div class="d-flex align-items-start gap-2">
              <div class="ai-loc-symbol-box symbol-office">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#475569" stroke-width="2"><path d="M3 21h18"/><path d="M5 21V7l8-4v18"/><path d="M19 21V11l-6-4"/></svg>
              </div>
              <div>
                <span class="ai-loc-city-name">Mumbai, Maharashtra, India</span>
                <div class="ai-loc-type-label">Office</div>
                <p class="ai-loc-sub-note">Listed as regional business office.</p>
              </div>
            </div>
          </div>
          <!-- Meta & Actions -->
          <div class="d-flex align-items-center justify-content-between mt-2 pt-1 border-top flex-wrap gap-2 ai-loc-footer-border">
            <div class="d-flex align-items-center gap-2 ai-loc-meta-text">
              <a href="javascript:void(0);" class="client-ai-source-link">
                <span>Source: Website</span>
                <svg width="9" height="9" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path><polyline points="15 3 21 3 21 9"></polyline><line x1="10" y1="14" x2="21" y2="3"></line></svg>
              </a>
              <span class="d-inline-flex align-items-center gap-1 ai-loc-likely">
                <span class="likely-dot"></span> Likely location
              </span>
            </div>
            <div class="d-flex align-items-center gap-2" id="ai_loc_actions_2">
              <button type="button" class="btn btn-sm btn-ai-add-office" onclick="window.clientLocationManager ? window.clientLocationManager.addAiLocation(2, false) : false;">
                Add as Office
              </button>
            </div>
          </div>
        </div>

        <!-- Bulk Action Row -->
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 pt-1">
          <button type="button" class="btn btn-sm btn-ai-add-all" id="btn_add_all_ai_locations" onclick="window.clientLocationManager ? window.clientLocationManager.addAllAiLocations() : false;">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="12 2 2 7 12 12 22 7 12 2"></polygon><polyline points="2 17 12 22 22 17"></polyline><polyline points="2 12 12 17 22 12"></polyline></svg>
            <span>Add all 3 locations</span>
          </button>
          <a href="javascript:void(0);" id="ai_view_source_link" target="_blank" class="ai-source-view-link">
            <span>View source</span>
            <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path><polyline points="15 3 21 3 21 9"></polyline><line x1="10" y1="14" x2="21" y2="3"></line></svg>
          </a>
        </div>
      </div>

      <!-- Quick Tips Card (Yellow banner matching design system) -->
      <div class="card client-helper-card client-addr-tips-card">
        <div class="d-flex align-items-center gap-2 mb-2">
          <div class="client-tips-icon-box">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#854d0e" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M9 18h6"></path>
              <path d="M10 22h4"></path>
              <path d="M12 2a7 7 0 0 0-7 7c0 2.38 1.19 4.47 3 5.74V17a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1v-2.26c1.81-1.27 3-3.36 3-5.74a7 7 0 0 0-7-7z"></path>
            </svg>
          </div>
          <h4 class="client-tips-heading">Quick Tips</h4>
        </div>
        <div class="client-tips-list">
          <div class="d-flex align-items-start gap-2 mb-1">
            <svg width="13" height="13" viewBox="0 0 20 20" fill="#eab308" class="flex-shrink-0 mt-1"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
            <span>Start typing a city name to search our global database.</span>
          </div>
          <div class="d-flex align-items-start gap-2 mb-1">
            <svg width="13" height="13" viewBox="0 0 20 20" fill="#eab308" class="flex-shrink-0 mt-1"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
            <span>The first location is marked as primary headquarters.</span>
          </div>
          <div class="d-flex align-items-start gap-2 mb-1">
            <svg width="13" height="13" viewBox="0 0 20 20" fill="#eab308" class="flex-shrink-0 mt-1"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
            <span>Add branch offices or regional centres as additional locations.</span>
          </div>
          <div class="d-flex align-items-start gap-2">
            <svg width="13" height="13" viewBox="0 0 20 20" fill="#eab308" class="flex-shrink-0 mt-1"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
            <span>Click &ldquo;Add as Office&rdquo; to quickly populate AI discovered locations.</span>
          </div>
        </div>
      </div>

    </aside>

  </div>
</div>

<style>
  /* ==========================================================================
     PAGE 3: COMPANY LOCATIONS & FULL ADDRESS REDESIGN (ENTERPRISE SAAS STANDARD)
     Card + Sub-Card Architecture Optimized for 15.6" Screens (1366x768 & 1920x1080)
     ========================================================================== */
  .client-address-layout {
    display: grid !important;
    width: 100% !important;
    max-width: 100% !important;
    min-width: 0 !important;
    grid-template-columns: minmax(0, 1.45fr) minmax(0, 1fr) !important;
    gap: 16px !important;
    align-items: start !important;
    box-sizing: border-box !important;
  }
  @media (max-width: 1024px) {
    .client-address-layout {
      grid-template-columns: 1fr !important;
      gap: 14px !important;
    }
  }

  /* Top Banner */
  .client-addr-top-banner {
    background: #f0f7ff !important;
    border: 1px solid #dbeafe !important;
    border-radius: 8px !important;
    padding: 9px 14px !important;
    display: flex !important;
    align-items: center !important;
    gap: 10px !important;
    font-size: 12px !important;
    color: #1e40af !important;
    font-weight: 500 !important;
    line-height: 1.4 !important;
  }

  /* Primary & Dedicated Section Cards */
  .client-section-card {
    background: #ffffff !important;
    border: 1px solid #e2e8f0 !important;
    border-radius: 12px !important;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02) !important;
    overflow: hidden !important;
  }
  .client-section-card > .card-header {
    background: #ffffff !important;
    border-bottom: 1px solid #f1f5f9 !important;
    padding: 12px 18px !important;
  }
  .client-section-card > .card-body {
    padding: 14px 18px !important;
  }

  .client-addr-card-icon-box {
    width: 36px !important;
    height: 36px !important;
    border-radius: 8px !important;
    background: #fef9c3 !important;
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    color: #ca8a04 !important;
    flex-shrink: 0 !important;
  }

  .client-section-heading {
    font-size: 14px !important;
    font-weight: 700 !important;
    color: #0f172a !important;
  }
  .client-section-copy {
    font-size: 11.5px !important;
    color: #64748b !important;
  }

  .client-hq-badge {
    background: #fef3c7 !important;
    border: 1px solid #fde68a !important;
    color: #92400e !important;
    font-size: 10px !important;
    font-weight: 700 !important;
    padding: 3px 10px !important;
    border-radius: 9999px !important;
    letter-spacing: 0.3px !important;
  }

  .client-branch-count-badge {
    background: #f1f5f9 !important;
    color: #475569 !important;
    font-size: 11px !important;
    font-weight: 600 !important;
    padding: 3px 10px !important;
    border-radius: 9999px !important;
    border: 1px solid #e2e8f0 !important;
  }

  /* Notice Alert */
  .client-addr-notice {
    background: #fffdf0 !important;
    border: 1px solid #fef08a !important;
    border-radius: 8px !important;
    padding: 8px 12px !important;
    display: flex !important;
    align-items: center !important;
    gap: 8px !important;
    font-size: 11.5px !important;
    color: #854d0e !important;
    line-height: 1.4 !important;
  }

  /* ==========================================
     SUB-CARD ARCHITECTURE
     ========================================== */
  .client-subcard {
    background: #ffffff !important;
    border: 1px solid #e2e8f0 !important;
    border-radius: 10px !important;
    padding: 13px 15px !important;
    margin-bottom: 12px !important;
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.02) !important;
    transition: border-color 0.15s ease, box-shadow 0.15s ease !important;
  }
  .client-subcard:hover {
    border-color: #cbd5e1 !important;
  }
  .client-subcard:last-child {
    margin-bottom: 0 !important;
  }
  .client-additional-subcards-wrap .client-subcard {
    margin-bottom: 10px !important;
  }

  .client-subcard-header {
    border-color: #f1f5f9 !important;
  }

  .client-subcard-icon-box {
    width: 26px !important;
    height: 26px !important;
    border-radius: 6px !important;
    background: #fef9c3 !important;
    color: #ca8a04 !important;
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    flex-shrink: 0 !important;
  }
  .client-subcard-icon-box.office-icon {
    background: #f1f5f9 !important;
    color: #475569 !important;
  }

  .client-subcard-title {
    font-size: 13px !important;
    font-weight: 700 !important;
    color: #0f172a !important;
  }
  .client-subcard-subtitle {
    font-size: 11px !important;
    color: #64748b !important;
    display: block !important;
  }

  .badge-hq-pill {
    background: #fef08a !important;
    color: #854d0e !important;
    font-size: 10px !important;
    font-weight: 700 !important;
    padding: 2px 7px !important;
    border-radius: 4px !important;
  }
  .badge-office-tag,
  .badge-branch-tag {
    background: #f1f5f9 !important;
    color: #475569 !important;
    font-size: 10px !important;
    font-weight: 700 !important;
    padding: 2px 8px !important;
    border-radius: 9999px !important;
    letter-spacing: 0.3px !important;
  }

  /* Sub-card Actions */
  .btn-set-primary,
  .btn-set-hq {
    font-size: 11px !important;
    font-weight: 600 !important;
    padding: 4px 10px !important;
    border-radius: 6px !important;
    color: #b45309 !important;
    border: 1px solid #fde68a !important;
    background: #fffdf0 !important;
    cursor: pointer !important;
    display: inline-flex !important;
    align-items: center !important;
    gap: 4px !important;
    transition: all 0.15s ease !important;
  }
  .btn-set-primary:hover,
  .btn-set-hq:hover {
    background: #fef3c7 !important;
    border-color: #f59e0b !important;
    color: #92400e !important;
  }

  .btn-remove-subcard {
    font-size: 11px !important;
    font-weight: 600 !important;
    padding: 4px 10px !important;
    border-radius: 6px !important;
    color: #ef4444 !important;
    border: 1px solid #fee2e2 !important;
    background: #ffffff !important;
    cursor: pointer !important;
    display: inline-flex !important;
    align-items: center !important;
    gap: 4px !important;
    transition: all 0.15s ease !important;
  }
  .btn-remove-subcard:hover {
    background: #fef2f2 !important;
    border-color: #fca5a5 !important;
    color: #dc2626 !important;
  }

  /* Form Controls */
  .client-addr-field-icon-left {
    left: 11px !important;
    top: 50% !important;
    transform: translateY(-50%) !important;
    color: #94a3b8 !important;
    pointer-events: none !important;
    z-index: 2 !important;
  }
  .client-addr-field-icon-right {
    right: 11px !important;
    top: 50% !important;
    transform: translateY(-50%) !important;
    color: #94a3b8 !important;
    pointer-events: none !important;
    z-index: 2 !important;
  }
  .client-addr-field-icon-top {
    left: 11px !important;
    top: 10px !important;
    color: #94a3b8 !important;
    pointer-events: none !important;
    z-index: 2 !important;
  }

  .client-addr-input {
    width: 100% !important;
    height: 36px !important;
    padding: 0 32px !important;
    border: 1px solid #cbd5e1 !important;
    border-radius: 8px !important;
    font-size: 12px !important;
    color: #1e293b !important;
    background: #ffffff !important;
    transition: border-color .15s ease, box-shadow .15s ease !important;
  }
  .client-addr-input:focus {
    outline: none !important;
    border-color: #2563eb !important;
    box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.12) !important;
  }
  .client-addr-input.is-invalid {
    border-color: #ef4444 !important;
    box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.12) !important;
  }

  .client-addr-textarea {
    width: 100% !important;
    min-height: 52px !important;
    padding: 7px 10px 7px 32px !important;
    border: 1px solid #cbd5e1 !important;
    border-radius: 8px !important;
    font-size: 12px !important;
    color: #1e293b !important;
    line-height: 1.45 !important;
    background: #ffffff !important;
    transition: border-color .15s ease, box-shadow .15s ease !important;
    resize: vertical !important;
  }
  .client-addr-textarea:focus {
    outline: none !important;
    border-color: #2563eb !important;
    box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.12) !important;
  }
  .client-addr-textarea.is-invalid {
    border-color: #ef4444 !important;
    box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.12) !important;
  }

  .client-field-hint {
    font-size: 10.5px !important;
    color: #64748b !important;
  }
  .client-addr-counter {
    font-size: 10.5px !important;
    color: #94a3b8 !important;
  }

  .client-addr-error {
    font-size: 11px !important;
    font-weight: 600 !important;
  }

  /* Empty State */
  .client-addr-empty-state {
    background: #fffdf0 !important;
    border: 1px solid #fde68a !important;
    border-radius: 10px !important;
    padding: 24px 20px !important;
    display: flex;
    flex-direction: column !important;
    align-items: center !important;
    justify-content: center !important;
    text-align: center !important;
    margin-bottom: 8px !important;
    transition: all 0.2s ease !important;
  }
  .client-addr-empty-state.d-none,
  .client-addr-empty-state[style*="display: none"],
  .client-addr-empty-state[style*="display:none"] {
    display: none !important;
  }
  .client-addr-empty-state:hover {
    border-color: #f59e0b !important;
  }
  .empty-state-icon-circle {
    width: 44px !important;
    height: 44px !important;
    border-radius: 50% !important;
    background: #eff6ff !important;
    border: 1px solid #dbeafe !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    margin-bottom: 10px !important;
    color: #2563eb !important;
  }
  .empty-state-title {
    font-size: 13.5px !important;
    font-weight: 700 !important;
    color: #0f172a !important;
    margin: 0 0 4px 0 !important;
  }
  .empty-state-subtitle {
    font-size: 11.5px !important;
    color: #64748b !important;
    margin: 0 0 14px 0 !important;
    max-width: 480px !important;
  }

  /* Centered CTA Buttons */
  .btn-add-first-location,
  .btn-add-additional-location {
    background: linear-gradient(180deg, #f59e0b 0%, #d97706 100%) !important;
    border: none !important;
    color: #ffffff !important;
    border-radius: 6px !important;
    padding: 8px 20px !important;
    font-size: 12px !important;
    font-weight: 700 !important;
    cursor: pointer !important;
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    gap: 6px !important;
    box-shadow: 0 1px 3px rgba(245, 158, 11, 0.3) !important;
    transition: all 0.15s ease !important;
  }
  .btn-add-first-location:hover,
  .btn-add-additional-location:hover {
    background: linear-gradient(180deg, #d97706 0%, #b45309 100%) !important;
    transform: translateY(-1px) !important;
    box-shadow: 0 2px 5px rgba(245, 158, 11, 0.4) !important;
    color: #ffffff !important;
  }
  .btn-add-first-location:active,
  .btn-add-additional-location:active {
    transform: translateY(0) !important;
  }

  .client-add-location-cta-wrap {
    display: flex;
    justify-content: center !important;
    align-items: center !important;
    width: 100% !important;
    padding: 6px 0 2px 0 !important;
  }
  .client-add-location-cta-wrap.d-none,
  .client-add-location-cta-wrap[style*="display: none"],
  .client-add-location-cta-wrap[style*="display:none"] {
    display: none !important;
  }

  /* Autocomplete Dropdown */
  .client-addr-city-dropdown {
    display: none;
    position: absolute;
    top: calc(100% + 4px);
    left: 0;
    right: 0;
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 8px;
    box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.05);
    max-height: 220px;
    overflow-y: auto;
    z-index: 1050;
  }
  .client-addr-city-dropdown.active {
    display: block;
  }
  .client-addr-dd-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 8px 12px;
    cursor: pointer;
    border-bottom: 1px solid #f1f5f9;
    transition: background .15s;
  }
  .client-addr-dd-item:hover, .client-addr-dd-item.is-highlighted {
    background: #f8fafc;
  }
  .client-addr-dd-left {
    display: flex;
    align-items: center;
    gap: 8px;
  }
  .client-addr-dd-city {
    font-size: 12px;
    font-weight: 600;
    color: #1e293b;
  }
  .client-addr-dd-sub {
    font-size: 11px;
    color: #64748b;
  }
  .client-addr-dd-badge {
    font-size: 10px;
    background: #f1f5f9;
    color: #475569;
    padding: 2px 6px;
    border-radius: 4px;
  }
  .client-addr-dd-empty {
    padding: 12px;
    text-align: center;
    font-size: 12px;
    color: #64748b;
  }

  /* ==========================================
     RIGHT SIDEBAR: AI Location Discovery
     ========================================== */
  .client-ai-location-card {
    background: #faf8ff !important;
    border: 1px solid #ede9fe !important;
    border-radius: 12px !important;
    padding: 14px 16px !important;
    margin-bottom: 12px !important;
  }
  .client-ai-icon-box {
    width: 26px !important;
    height: 26px !important;
    border-radius: 7px !important;
    background: #ede9fe !important;
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    color: #7040f5 !important;
  }
  .client-ai-title {
    font-size: 13.5px !important;
    font-weight: 700 !important;
    color: #101828 !important;
    margin: 0 !important;
  }
  .badge-ai-beta {
    background: #ede9fe !important;
    color: #7040f5 !important;
    font-weight: 600 !important;
    font-size: 10px !important;
    padding: 2px 7px !important;
    border-radius: 6px !important;
  }
  .client-ai-desc {
    font-size: 11px !important;
    color: #64748b !important;
    line-height: 1.45 !important;
    margin-bottom: 12px !important;
  }

  .client-ai-loc-item {
    background: #ffffff !important;
    border: 1px solid #e2e8f0 !important;
    border-radius: 10px !important;
    padding: 12px 14px !important;
  }
  #ai_loc_card_0 {
    background: #f0fdf4 !important;
    border-color: #bbf7d0 !important;
  }
  .ai-loc-symbol-box {
    width: 28px !important;
    height: 28px !important;
    border-radius: 7px !important;
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    flex-shrink: 0 !important;
    margin-top: 2px !important;
  }
  .symbol-hq {
    background: #dcfce7 !important;
    color: #16a34a !important;
  }
  .symbol-office {
    background: #f1f5f9 !important;
    color: #475569 !important;
  }

  .ai-loc-city-name {
    font-size: 13px !important;
    font-weight: 700 !important;
    color: #0f172a !important;
  }
  .badge-ai-recommended {
    background: #dcfce7 !important;
    color: #15803d !important;
    border-radius: 9999px !important;
    font-size: 9.5px !important;
    font-weight: 600 !important;
    padding: 2px 8px !important;
  }
  .ai-loc-type-label {
    font-size: 11px !important;
    font-weight: 600 !important;
    color: #334155 !important;
    margin-top: 1px !important;
  }
  .ai-loc-sub-note {
    font-size: 10.5px !important;
    color: #64748b !important;
    margin: 2px 0 0 0 !important;
  }
  .ai-loc-footer-border {
    border-color: rgba(226, 232, 240, 0.7) !important;
  }
  #ai_loc_card_0 .ai-loc-footer-border {
    border-color: rgba(187, 247, 208, 0.6) !important;
  }
  .ai-loc-meta-text {
    font-size: 10.5px !important;
    color: #64748b !important;
  }
  .client-ai-source-link {
    color: #2563eb !important;
    text-decoration: none !important;
    display: inline-flex !important;
    align-items: center !important;
    gap: 2px !important;
  }
  .ai-loc-verified {
    color: #16a34a !important;
    font-weight: 500 !important;
  }
  .verified-dot {
    width: 5px !important;
    height: 5px !important;
    border-radius: 50% !important;
    background: #16a34a !important;
    display: inline-block !important;
  }
  .ai-loc-likely {
    color: #64748b !important;
    font-weight: 500 !important;
  }
  .likely-dot {
    width: 5px !important;
    height: 5px !important;
    border-radius: 50% !important;
    background: #64748b !important;
    display: inline-block !important;
  }

  .btn-ai-set-hq {
    background: #f59e0b !important;
    color: #78350f !important;
    font-weight: 600 !important;
    font-size: 11px !important;
    border: none !important;
    border-radius: 6px !important;
    padding: 4px 10px !important;
    cursor: pointer !important;
  }
  .btn-ai-add-office {
    background: #ffffff !important;
    color: #2563eb !important;
    border: 1px solid #2563eb !important;
    font-weight: 600 !important;
    font-size: 11px !important;
    border-radius: 6px !important;
    padding: 4px 10px !important;
    cursor: pointer !important;
  }
  .btn-ai-add-all {
    background: #eff6ff !important;
    border: 1px solid #bfdbfe !important;
    color: #1d4ed8 !important;
    font-weight: 600 !important;
    font-size: 11.5px !important;
    border-radius: 6px !important;
    padding: 6px 14px !important;
    display: inline-flex !important;
    align-items: center !important;
    gap: 6px !important;
    cursor: pointer !important;
  }
  .ai-source-view-link {
    color: #2563eb !important;
    text-decoration: none !important;
    font-size: 11px !important;
    font-weight: 500 !important;
    display: inline-flex !important;
    align-items: center !important;
    gap: 3px !important;
  }

  /* Quick Tips Card */
  .client-addr-tips-card {
    background: #fefce8 !important;
    border: 1px solid #fef08a !important;
    border-radius: 12px !important;
    padding: 14px 16px !important;
    margin-bottom: 0 !important;
  }
  .client-tips-icon-box {
    width: 26px !important;
    height: 26px !important;
    border-radius: 50% !important;
    background: #fef08a !important;
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    color: #854d0e !important;
    flex-shrink: 0 !important;
  }
  .client-tips-heading {
    font-size: 13px !important;
    font-weight: 700 !important;
    color: #713f12 !important;
    margin: 0 !important;
  }
  .client-tips-list {
    font-size: 11px !important;
    color: #78350f !important;
    line-height: 1.55 !important;
  }

  /* ==========================================================================
     15.6" Laptop Viewport Responsiveness (1366x768 & 1920x1080 at 125%/150%)
     ========================================================================== */
  @media (max-height: 850px) {
    .client-addr-top-banner {
      padding: 7px 12px !important;
      margin-bottom: 10px !important;
    }
    .client-section-card > .card-header {
      padding: 10px 14px !important;
    }
    .client-section-card > .card-body {
      padding: 11px 14px !important;
    }
    .client-subcard {
      padding: 10px 12px !important;
      margin-bottom: 9px !important;
    }
    .client-subcard-header {
      margin-bottom: 8px !important;
      padding-bottom: 6px !important;
    }
    .client-addr-input {
      height: 34px !important;
      font-size: 11.5px !important;
    }
    .client-addr-textarea {
      min-height: 48px !important;
      font-size: 11.5px !important;
      padding: 6px 10px 6px 30px !important;
    }
    .client-ai-location-card {
      padding: 11px 13px !important;
    }
    .client-ai-loc-item {
      padding: 9px 11px !important;
      margin-bottom: 7px !important;
    }
    .client-addr-tips-card {
      padding: 11px 13px !important;
    }
  }
</style>
<!--end::Company Locations & Address Details-->