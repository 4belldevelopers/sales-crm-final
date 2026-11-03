{{--
  God-Level Company Profile — Step 4: Headquarters & Additional Locations
  100% Replicates Add Client Step 3 Address Design System (Rule 20 Design Standard)
  Unified V5 Address Engine, Debounced City Search, and Dynamic Branch Locations
  Optimized for 15.6" Screens (1366x768 & 1920x1080)
--}}
@php
    $primary_city = '';
    $primary_tagify_json = '';
    $primary_address = '';
    $primary_postcode = '';
    $additional_addresses = [];
    $addressCount = 0;

    if (isset($client_location) && is_iterable($client_location)) {
        foreach ($client_location as $idx => $clVal) {
            $cName = $clVal->city_name ?? ($clVal->city ?? '');
            $sName = $clVal->state_name ?? '';
            $coName = $clVal->country_name ?? '';

            $canonical = trim($cName . ($sName ? ', ' . $sName : '') . ($coName ? ', ' . $coName : ''), ', ');
            $parts = array_filter([$cName, $sName, $coName]);
            $tagifyJson = json_encode([['value' => implode(' | ', $parts)]]);

            if ($idx === 0) {
                $primary_city = $canonical;
                $primary_tagify_json = $tagifyJson;
                $primary_address = $clVal->address ?? '';
                $primary_postcode = $clVal->postcode ?? '';
            } else {
                $additional_addresses[] = [
                    'id' => $idx,
                    'address' => $clVal->address ?? '',
                    'city' => $canonical,
                    'postcode' => $clVal->postcode ?? '',
                    'tagify_json' => $tagifyJson,
                ];
                $addressCount = $idx;
            }
        }
    }

    // Fallback: If no client_location exists yet, check company->address
    if (empty($primary_address) && !empty($account->address)) {
        $primary_address = $account->address;
    }
@endphp

<div class="pb-5 client-address-step profile-address-step" data-wizard-type="step-content">
  
  <!-- ==========================================
       TOP INFORMATIONAL BANNER
       ========================================== -->
  <div class="client-addr-top-banner mb-3" style="background:#f0f7ff; border:1px solid #dbeafe; border-radius:10px; padding:12px 16px; display:flex; align-items:center; gap:10px; font-size:12px; color:#1d4ed8; font-weight:500;">
    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="flex-shrink-0">
      <circle cx="12" cy="12" r="10"></circle>
      <line x1="12" y1="16" x2="12" y2="12"></line>
      <line x1="12" y1="8" x2="12.01" y2="8"></line>
    </svg>
    <span>The first location is designated as your primary headquarters. You can add regional branch offices or remote development hubs below.</span>
  </div>

  <div class="client-step-layout client-address-layout">
    
    <!-- ==========================================
         LEFT MAIN COLUMN: Locations & Addresses
         ========================================== -->
    <div class="client-main-column">
      
      <!-- CARD 1: PRIMARY HEADQUARTERS CARD -->
      <section class="card client-section-card client-addr-primary-card mb-3" id="profile_addr_card_primary" style="background:#ffffff; border:1px solid #e2e8f0; border-radius:12px; box-shadow:0 1px 3px rgba(0,0,0,0.03); overflow:hidden;">
        <!-- Card Header -->
        <div class="card-header border-0 d-flex align-items-center justify-content-between" style="background:#ffffff; border-bottom:1px solid #f8fafc !important; padding:12px 18px !important;">
          <div class="d-flex align-items-center gap-3">
            <div class="client-addr-card-icon-box" style="width:36px; height:36px; border-radius:9px; background:#fef9c3; display:inline-flex; align-items:center; justify-content:center; color:#ca8a04; flex-shrink:0;">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#ca8a04" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                <polyline points="9 22 9 12 15 12 15 22"></polyline>
              </svg>
            </div>
            <div>
              <h3 class="client-section-heading mb-0" style="font-size:14px; font-weight:700; color:#101828; margin:0 0 2px 0;">Corporate Headquarters</h3>
              <p class="client-section-copy mb-0" style="font-size:11px; color:#64748b; margin:0;">Primary registered office and principal operating facility.</p>
            </div>
          </div>
          <span class="badge" style="background:#fef9c3; color:#ca8a04; font-size:11px; font-weight:700; padding:4px 10px; border-radius:6px; letter-spacing:0.5px;">
            HEADQUARTERS
          </span>
        </div>

        <!-- Card Body -->
        <div class="card-body" style="padding:16px 18px !important;">
          <div class="client-subcard client-hq-subcard" id="client_addr_card_0" style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:10px; padding:14px;">
            <div class="client-subcard-header d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom" style="border-color:#e2e8f0 !important;">
              <div class="d-flex align-items-center gap-2">
                <div style="width:24px; height:24px; border-radius:6px; background:#fef9c3; display:inline-flex; align-items:center; justify-content:center; color:#ca8a04;">
                  <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#ca8a04" stroke-width="2">
                    <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                    <polyline points="9 22 9 12 15 12 15 22"></polyline>
                  </svg>
                </div>
                <div>
                  <h4 class="client-subcard-title mb-0" style="font-size:12.5px; font-weight:700; color:#0f172a; margin:0;">Main Office</h4>
                  <span class="client-subcard-subtitle" style="font-size:10.5px; color:#64748b;">Primary billing and operations hub</span>
                </div>
              </div>
              <span class="badge" style="background:#dcfce7; color:#15803d; font-size:10px; font-weight:700; padding:2px 8px; border-radius:12px;">PRIMARY</span>
            </div>

            <div class="client-subcard-body">
              <!-- Field: City Autocomplete -->
              <div class="client-addr-field mb-3 position-relative">
                <label class="form-label mb-1" for="city_search_0" style="font-size:12px; font-weight:700; color:#0f172a;">City / Location <span class="text-danger">*</span></label>
                <div class="position-relative client-addr-input-wrap">
                  <span class="position-absolute" style="top:50%; left:12px; transform:translateY(-50%); pointer-events:none; color:#94a3b8;">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                      <circle cx="11" cy="11" r="8"></circle>
                      <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                    </svg>
                  </span>
                  <input type="text"
                         class="form-control client-addr-input client-addr-city-search"
                         id="city_search_0"
                         data-cid="0"
                         value="{{ $primary_city }}"
                         placeholder="Search city (e.g. Pune, New York, London, Singapore...)"
                         style="height:38px; padding-left:34px; padding-right:32px; font-size:12px; border:1px solid #cbd5e1; border-radius:8px; background:#ffffff; color:#0f172a;"
                         autocomplete="off"
                         spellcheck="false">
                  <span class="position-absolute" style="top:50%; right:12px; transform:translateY(-50%); pointer-events:none; color:#94a3b8;">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
                  </span>
                  <div class="client-addr-city-dropdown" id="city_dropdown_0" style="display:none; position:absolute; top:calc(100% + 4px); left:0; right:0; background:#ffffff; border:1px solid #e2e8f0; border-radius:8px; box-shadow:0 10px 25px rgba(0,0,0,0.08); z-index:1050; max-height:220px; overflow-y:auto;"></div>
                </div>
                <!-- Synchronized JSON Tagify input for Controller -->
                <input type="hidden" name="kt_tagify_work_location[0]" id="kt_tagify_work_location_0" value="<?php echo htmlspecialchars($primary_tagify_json, ENT_QUOTES); ?>">
                <div class="text-danger" id="city_err_0" style="display:none; font-size:11.5px; margin-top:4px;">Please select your headquarters city.</div>
              </div>

              <!-- Field: Full Address -->
              <div class="client-addr-field mb-3">
                <label class="form-label mb-1" for="address_0" style="font-size:12px; font-weight:700; color:#0f172a;">Full Address <span class="text-danger">*</span></label>
                <textarea class="form-control"
                          name="address[0]"
                          id="address_0"
                          rows="2"
                          placeholder="Building, street, suite, floor, area..."
                          style="font-size:12px; border:1px solid #cbd5e1; border-radius:8px; background:#ffffff; color:#0f172a; resize:vertical;">{{ $primary_address }}</textarea>
                <div class="text-danger" id="addr_err_0" style="display:none; font-size:11.5px; margin-top:4px;">Please enter your headquarters address.</div>
              </div>

              <!-- Field: Postal / ZIP Code -->
              <div class="client-addr-field">
                <label class="form-label mb-1" for="postcode_0" style="font-size:12px; font-weight:700; color:#0f172a;">Postal / ZIP Code</label>
                <input type="text"
                       class="form-control"
                       name="postcode[0]"
                       id="postcode_0"
                       value="{{ $primary_postcode }}"
                       placeholder="e.g. 411001, 10001, W1B 3HH"
                       style="height:36px; max-width:240px; font-size:12px; border:1px solid #cbd5e1; border-radius:8px; background:#ffffff; color:#0f172a;">
              </div>
            </div>
          </div>
        </div>
      </section>

      <!-- CARD 2: ADDITIONAL BRANCH OFFICES CONTAINER -->
      <section class="card client-section-card mb-3" style="background:#ffffff; border:1px solid #e2e8f0; border-radius:12px; box-shadow:0 1px 3px rgba(0,0,0,0.03); overflow:hidden;">
        <div class="card-header border-0 d-flex align-items-center justify-content-between" style="background:#ffffff; border-bottom:1px solid #f8fafc !important; padding:12px 18px !important;">
          <div class="d-flex align-items-center gap-3">
            <div style="width:36px; height:36px; border-radius:9px; background:#fef9c3; display:inline-flex; align-items:center; justify-content:center; color:#ca8a04; flex-shrink:0;">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#ca8a04" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <rect x="2" y="7" width="20" height="14" rx="2" ry="2"/>
                <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/>
              </svg>
            </div>
            <div>
              <h3 class="client-section-heading mb-0" style="font-size:14px; font-weight:700; color:#101828; margin:0 0 2px 0;">Additional Offices</h3>
              <p class="client-section-copy mb-0" style="font-size:11px; color:#64748b; margin:0;">Branch offices, regional centers, or development hubs.</p>
            </div>
          </div>
          <button type="button" class="btn btn-sm" id="profile_add_address_btn" onclick="window.addAdditionalBranchAddress ? window.addAdditionalBranchAddress() : false;" style="background:#f1f5f9; color:#1e293b; border:1px solid #cbd5e1; font-weight:600; font-size:11.5px; border-radius:7px; padding:6px 14px; display:inline-flex; align-items:center; gap:5px; cursor:pointer;">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
            <span>Add Additional Location</span>
          </button>
        </div>

        <div class="card-body" style="padding:16px 18px !important;">
          <!-- Hidden address ID counter for dynamic serialization -->
          <input type="hidden" name="addressId" id="addressId" value="{{ $addressCount }}">

          <!-- Container for Dynamic Branch Sub-Cards -->
          <div id="additional_addresses_container" class="d-flex flex-column gap-3">
            @foreach($additional_addresses as $bAddr)
              @php $cid = $bAddr['id']; @endphp
              <div class="client-subcard client-branch-card" id="client_addr_card_{{ $cid }}" style="background:#ffffff; border:1px solid #e2e8f0; border-radius:10px; padding:14px;">
                <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom" style="border-color:#e2e8f0 !important;">
                  <div class="d-flex align-items-center gap-2">
                    <div style="width:24px; height:24px; border-radius:6px; background:#f1f5f9; display:inline-flex; align-items:center; justify-content:center; color:#475569;">
                      <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                    </div>
                    <span style="font-size:12.5px; font-weight:700; color:#0f172a;">Branch Office #{{ $cid }}</span>
                  </div>
                  <button type="button" class="btn btn-sm btn-link text-danger p-0" onclick="window.removeAddressCard ? window.removeAddressCard({{ $cid }}) : false;" style="font-size:11px; font-weight:600; text-decoration:none; cursor:pointer;">
                    Remove Location
                  </button>
                </div>

                <div class="client-subcard-body">
                  <div class="client-addr-field mb-3 position-relative">
                    <label class="form-label mb-1" for="branch_city_search_{{ $cid }}" style="font-size:12px; font-weight:700; color:#0f172a;">City / Location <span class="text-danger">*</span></label>
                    <div class="position-relative client-addr-input-wrap">
                      <input type="text"
                             class="form-control client-addr-input branch-city-search"
                             id="branch_city_search_{{ $cid }}"
                             data-cid="{{ $cid }}"
                             value="{{ $bAddr['city'] }}"
                             placeholder="Search city..."
                             style="height:38px; padding-left:12px; font-size:12px; border:1px solid #cbd5e1; border-radius:8px;"
                             autocomplete="off">
                      <div class="client-addr-city-dropdown" id="branch_city_dropdown_{{ $cid }}" style="display:none; position:absolute; top:calc(100% + 4px); left:0; right:0; background:#ffffff; border:1px solid #e2e8f0; border-radius:8px; z-index:1050; max-height:200px; overflow-y:auto;"></div>
                    </div>
                    <input type="hidden" name="kt_tagify_work_location[{{ $cid }}]" id="kt_tagify_work_location_{{ $cid }}" value="<?php echo htmlspecialchars($bAddr['tagify_json'], ENT_QUOTES); ?>">
                  </div>

                  <div class="client-addr-field mb-3">
                    <label class="form-label mb-1" for="address_{{ $cid }}" style="font-size:12px; font-weight:700; color:#0f172a;">Address <span class="text-danger">*</span></label>
                    <textarea class="form-control" name="address[{{ $cid }}]" id="address_{{ $cid }}" rows="2" placeholder="Building, street, area..." style="font-size:12px; border:1px solid #cbd5e1; border-radius:8px;">{{ $bAddr['address'] }}</textarea>
                  </div>

                  <div class="client-addr-field">
                    <label class="form-label mb-1" for="postcode_{{ $cid }}" style="font-size:12px; font-weight:700; color:#0f172a;">Postal / ZIP Code</label>
                    <input type="text" class="form-control" name="postcode[{{ $cid }}]" id="postcode_{{ $cid }}" value="{{ $bAddr['postcode'] }}" placeholder="Postal code" style="height:36px; max-width:240px; font-size:12px; border:1px solid #cbd5e1; border-radius:8px;">
                  </div>
                </div>
              </div>
            @endforeach
          </div>

          <!-- Empty state when no branch locations -->
          <div id="no_additional_branches_msg" style="{{ count($additional_addresses) > 0 ? 'display:none;' : 'display:block;' }} text-align:center; padding:20px 10px; color:#94a3b8; font-size:11.5px; font-style:italic;">
            No additional branch locations added yet. Click "+ Add Additional Location" above to add branch offices.
          </div>
        </div>
      </section>

    </div>

    <!-- ==========================================
         RIGHT SIDEBAR: Location Guidelines
         ========================================== -->
    <aside class="client-side-column">
      
      <div class="card client-helper-card" style="background:#ffffff; border:1px solid #e2e8f0; border-radius:12px; padding:16px 18px !important; margin-bottom:12px !important;">
        <h4 style="font-size:12.5px; font-weight:700; color:#0f172a; margin:0 0 10px; display:flex; align-items:center; gap:8px;">
          <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
          <span>Location Intelligence</span>
        </h4>
        <p style="font-size:11px; color:#64748b; line-height:1.55; margin:0 0 10px;">
          Your designated Headquarters location configures your default local timezone, regulatory legal jurisdiction, and primary job posting radius.
        </p>
        <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:8px; padding:10px 12px; font-size:11px; color:#334155;">
          <strong>Tip:</strong> Branch locations automatically expand your regional talent pipeline and recruiter dispatch zones.
        </div>
      </div>

    </aside>

  </div>
</div>
