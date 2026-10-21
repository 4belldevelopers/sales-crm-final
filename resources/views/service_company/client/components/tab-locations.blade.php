<!--begin::Company Locations & Address Details (God-Level Design Aligned with Reference media_1792565792953_2202cf86.png)-->
@php
    $primary_city = '';
    $primary_tagify_json = '';
    $primary_address = '';
    $additional_addresses = [];
    $addressId = 0;

    if (isset($client_location_arr) && is_iterable($client_location_arr)) {
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
  <div class="client-addr-top-banner mb-3" style="background:#f0f7ff; border:1px solid #dbeafe; border-radius:8px; padding:10px 14px; display:flex; align-items:center; gap:10px;">
    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="flex-shrink-0">
      <circle cx="12" cy="12" r="10"></circle>
      <line x1="12" y1="16" x2="12" y2="12"></line>
      <line x1="12" y1="8" x2="12.01" y2="8"></line>
    </svg>
    <span style="font-size:12px; color:#1e40af; font-weight:500; line-height:1.4;">The first location you enter will be marked as the primary headquarters. You can change this later.</span>
  </div>

  <div class="client-step-layout client-address-layout">
    
    <!-- ==========================================
         LEFT MAIN COLUMN: Company Locations
         ========================================== -->
    <div class="client-main-column">
      
      <!-- CARD 1: PRIMARY LOCATION (HEADQUARTERS) -->
      <section class="card client-section-card client-addr-primary-card mb-3" id="client_addr_card_0" style="background:#ffffff; border:1px solid #e2e8f0; border-radius:12px; box-shadow:0 1px 3px rgba(0,0,0,0.03); overflow:hidden; margin-bottom:14px !important;">
        <!-- Card Header -->
        <div class="card-header border-0 d-flex align-items-center justify-content-between" style="background:#ffffff; border-bottom:1px solid #f8fafc !important; padding:12px 18px !important;">
          <div class="d-flex align-items-center gap-2">
            <div style="width:36px; height:36px; border-radius:9px; background:#fef9c3; display:inline-flex; align-items:center; justify-content:center; color:#ca8a04; flex-shrink:0;">
              <svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="#ca8a04" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                <polyline points="9 22 9 12 15 12 15 22"></polyline>
              </svg>
            </div>
            <div>
              <h3 class="client-section-heading mb-0" style="font-size:14.5px; font-weight:700; color:#101828;">Primary Location</h3>
              <p class="client-section-copy mb-0" style="font-size:11px; color:#64748b;">Company headquarters or main office location.</p>
            </div>
          </div>
          <span class="badge client-hq-badge" style="background:#fef3c7; border:1px solid #fde68a; color:#92400e; font-size:10px; font-weight:700; padding:3px 10px; border-radius:9999px; letter-spacing:0.4px;">
            HEADQUARTERS
          </span>
        </div>

        <!-- Card Body -->
        <div class="card-body" style="padding:14px 18px !important;">
          <!-- Field: City -->
          <div class="client-addr-field mb-3">
            <label class="form-label mb-1" for="city_search_0" style="font-size:12px; font-weight:700; color:#0f172a;">City <span class="text-danger">*</span></label>
            <div class="position-relative client-addr-input-wrap">
              <span class="position-absolute" style="left:12px; top:50%; transform:translateY(-50%); color:#94a3b8; pointer-events:none; z-index:2;">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <circle cx="11" cy="11" r="8"></circle>
                  <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                </svg>
              </span>
              <input type="text" class="form-control client-addr-input client-addr-city-search" id="city_search_0" data-cid="0" value="{{ $primary_city }}" placeholder="Search for a city..." autocomplete="off" spellcheck="false" style="height:38px; padding-left:34px; padding-right:34px; font-size:12px; border:1px solid #cbd5e1; border-radius:8px; color:#1e293b;">
              <span class="position-absolute" style="right:12px; top:50%; transform:translateY(-50%); color:#94a3b8; pointer-events:none; z-index:2;">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <polyline points="6 9 12 15 18 9"></polyline>
                </svg>
              </span>
              <div class="client-addr-city-dropdown" id="city_dropdown_0"></div>
              <input type="hidden" name="kt_tagify_work_location[]" id="kt_tagify_work_location_0" value="{{ $primary_tagify_json }}">
            </div>
            <!-- Error indicator -->
            <div class="client-addr-error text-danger mt-1" id="city_err_0" style="display:none; font-size:11px; font-weight:600;">
              <svg width="13" height="13" viewBox="0 0 24 24" fill="currentColor" style="vertical-align:-2px;"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/></svg>
              <span>City selection is required for the primary location.</span>
            </div>
          </div>

          <!-- Field: Full Address -->
          <div class="client-addr-field">
            <label class="form-label mb-1" for="address_0" style="font-size:12px; font-weight:700; color:#0f172a;">Full Address <span class="text-danger">*</span></label>
            <div class="position-relative client-addr-textarea-wrap">
              <span class="position-absolute" style="left:12px; top:11px; color:#94a3b8; pointer-events:none; z-index:2;">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"></path>
                  <circle cx="12" cy="10" r="3"></circle>
                </svg>
              </span>
              <textarea class="form-control client-addr-textarea" name="address[]" id="address_0" rows="2" maxlength="500" placeholder="Building name, street, area, landmark, pincode, etc." style="padding:8px 12px 8px 34px; font-size:12px; border:1px solid #cbd5e1; border-radius:8px; color:#1e293b; resize:vertical; min-height:60px;">{{ $primary_address }}</textarea>
            </div>
            <div class="d-flex align-items-center justify-content-between mt-1">
              <span class="text-muted" style="font-size:10.5px;">Provide the complete registered or headquarters address.</span>
              <span class="client-addr-counter" style="font-size:10.5px; color:#94a3b8;"><span id="address_char_count_0">{{ strlen($primary_address) }}</span>/500</span>
            </div>
            <!-- Error indicator -->
            <div class="client-addr-error text-danger mt-1" id="address_err_0" style="display:none; font-size:11px; font-weight:600;">
              <svg width="13" height="13" viewBox="0 0 24 24" fill="currentColor" style="vertical-align:-2px;"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/></svg>
              <span>Full Address is required for the primary location.</span>
            </div>
          </div>
        </div>
      </section>

      <!-- CARD 2: ADDITIONAL LOCATIONS (OPTIONAL) -->
      <section class="card client-section-card client-addr-additional-card" id="client_addr_additional_card" style="background:#ffffff; border:1px solid #e2e8f0; border-radius:12px; box-shadow:0 1px 3px rgba(0,0,0,0.03); overflow:hidden;">
        <!-- Card Header -->
        <div class="card-header border-0 d-flex align-items-center justify-content-between" style="background:#ffffff; border-bottom:1px solid #f8fafc !important; padding:12px 18px !important;">
          <div class="d-flex align-items-center gap-2">
            <div style="width:36px; height:36px; border-radius:9px; background:#fef9c3; display:inline-flex; align-items:center; justify-content:center; color:#ca8a04; flex-shrink:0;">
              <svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="#ca8a04" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M3 21h18"></path>
                <path d="M5 21V7l8-4v18"></path>
                <path d="M19 21V11l-6-4"></path>
              </svg>
            </div>
            <div>
              <h3 class="client-section-heading mb-0" style="font-size:14.5px; font-weight:700; color:#101828;">Additional Locations (Optional)</h3>
              <p class="client-section-copy mb-0" style="font-size:11px; color:#64748b;">Add branch offices, development centres or other locations.</p>
            </div>
          </div>
          <button type="button" class="btn btn-sm" id="btn_add_another_address" style="background:#ffffff; border:1.5px solid #2563eb; color:#2563eb; border-radius:8px; padding:6px 14px; font-size:11.5px; font-weight:600; cursor:pointer; display:inline-flex; align-items:center; gap:4px; box-shadow:none;">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
            <span>Add Another Location</span>
          </button>
        </div>

        <!-- Card Body -->
        <div class="card-body" style="padding:14px 18px !important;">
          <!-- Dynamic Branches List -->
          <div class="client-additional-list" id="additional_addresses_container">
            @if (!empty($additional_addresses))
              @foreach ($additional_addresses as $addIdx => $addrItem)
                <div class="client-branch-card" id="clientAddr_{{ $addrItem['id'] }}" data-cid="{{ $addrItem['id'] }}" style="background:#ffffff; border:1px solid #e2e8f0; border-radius:10px; padding:14px 16px; margin-bottom:12px; box-shadow:0 1px 2px rgba(0,0,0,0.02);">
                  <!-- Branch Card Header -->
                  <div class="d-flex align-items-center justify-content-between mb-2">
                    <div class="d-flex align-items-center gap-2">
                      <div style="width:28px; height:28px; border-radius:7px; background:#f1f5f9; display:inline-flex; align-items:center; justify-content:center; color:#475569;">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg>
                      </div>
                      <div>
                        <div class="d-flex align-items-center gap-2">
                          <h4 class="client-branch-title mb-0" style="font-size:13px; font-weight:700; color:#0f172a;">Additional Location ({{ $addIdx + 1 }})</h4>
                          <span class="badge" style="background:#f1f5f9; color:#475569; font-size:9.5px; font-weight:600; padding:2px 6px; border-radius:4px;">OFFICE</span>
                        </div>
                      </div>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                      <button type="button" class="btn btn-xs" onclick="window.clientLocationManager ? window.clientLocationManager.setBranchAsHq({{ $addrItem['id'] }}) : false" style="font-size:10.5px; font-weight:600; padding:3px 8px; border-radius:5px; color:#b45309; border:1px solid #fde68a; background:#fefce8; cursor:pointer;" title="Set as Headquarters">
                        Set as Headquarters
                      </button>
                      <button type="button" class="btn btn-xs client-branch-remove-btn" onclick="removeAddressCard(this, 'clientAddr_{{ $addrItem['id'] }}')" style="font-size:10.5px; font-weight:600; padding:3px 8px; border-radius:5px; color:#dc2626; border:1px solid #fee2e2; background:#fef2f2; cursor:pointer;" title="Remove Location">
                        <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right:2px;"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                        Remove
                      </button>
                    </div>
                  </div>

                  <!-- Branch Form Fields -->
                  <div class="client-branch-body">
                    <!-- Branch City -->
                    <div class="client-addr-field mb-2">
                      <label class="form-label mb-1" for="branch_city_search_{{ $addrItem['id'] }}" style="font-size:11.5px; font-weight:600; color:#334155;">City <span class="text-danger">*</span></label>
                      <div class="position-relative client-addr-input-wrap">
                        <span class="position-absolute" style="left:11px; top:50%; transform:translateY(-50%); color:#94a3b8; pointer-events:none; z-index:2;">
                          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                        </span>
                        <input type="text" class="form-control client-addr-input client-addr-city-search branch-city-search" id="branch_city_search_{{ $addrItem['id'] }}" data-cid="{{ $addrItem['id'] }}" value="{{ $addrItem['city'] }}" placeholder="Search branch city..." autocomplete="off" style="height:36px; padding:0 32px; font-size:12px; border:1px solid #cbd5e1; border-radius:8px;">
                        <span class="position-absolute" style="right:11px; top:50%; transform:translateY(-50%); color:#94a3b8; pointer-events:none; z-index:2;">
                          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"></polyline></svg>
                        </span>
                        <div class="client-addr-city-dropdown branch-city-dropdown" id="branch_city_dropdown_{{ $addrItem['id'] }}"></div>
                        <input type="hidden" name="kt_tagify_work_location[]" id="kt_tagify_work_location_{{ $addrItem['id'] }}" value='{{ $addrItem['tagify_json'] }}'>
                      </div>
                      <div class="client-addr-error text-danger mt-1" id="branch_city_err_{{ $addrItem['id'] }}" style="display:none; font-size:10.5px;">City selection is required.</div>
                    </div>

                    <!-- Branch Address -->
                    <div class="client-addr-field">
                      <label class="form-label mb-1" for="address_{{ $addrItem['id'] }}" style="font-size:11.5px; font-weight:600; color:#334155;">Full Address <span class="text-danger">*</span></label>
                      <div class="position-relative client-addr-textarea-wrap">
                        <span class="position-absolute" style="left:11px; top:9px; color:#94a3b8; pointer-events:none; z-index:2;">
                          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg>
                        </span>
                        <textarea class="form-control client-addr-textarea" name="address[]" id="address_{{ $addrItem['id'] }}" rows="2" maxlength="500" placeholder="Building name, street, area, landmark, pincode, etc." style="padding:7px 10px 7px 32px; font-size:12px; border:1px solid #cbd5e1; border-radius:8px; resize:vertical; min-height:54px;">{{ $addrItem['address'] }}</textarea>
                      </div>
                      <div class="d-flex align-items-center justify-content-between mt-1">
                        <span class="text-muted" style="font-size:10.5px;">Accurate branch address.</span>
                        <span class="client-addr-counter" style="font-size:10.5px; color:#94a3b8;"><span id="address_char_count_{{ $addrItem['id'] }}">{{ strlen($addrItem['address']) }}</span>/500</span>
                      </div>
                      <div class="client-addr-error text-danger mt-1" id="branch_address_err_{{ $addrItem['id'] }}" style="display:none; font-size:10.5px;">Full Address is required.</div>
                    </div>
                  </div>
                </div>
              @endforeach
            @endif
          </div>

          <!-- Empty State (Matching media_1792565792953_2202cf86.png) -->
          <div class="client-addr-empty-box" id="client_additional_empty" style="border:1.5px dashed #bfdbfe; background:#f8fafc; border-radius:10px; padding:24px 16px; text-align:center; {{ !empty($additional_addresses) ? 'display:none;' : '' }}">
            <div style="width:40px; height:40px; border-radius:50%; background:#e0f2fe; display:inline-flex; align-items:center; justify-content:center; margin-bottom:8px;">
              <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M3 21h18"></path>
                <path d="M5 21V7l8-4v18"></path>
                <path d="M19 21V11l-6-4"></path>
              </svg>
            </div>
            <h5 style="font-size:12.5px; font-weight:700; color:#334155; margin:0 0 3px 0;">No additional locations added yet.</h5>
            <p style="font-size:11px; color:#64748b; margin:0;">Add branch offices, development centres or other company locations.</p>
          </div>
        </div>
      </section>

      <input type="hidden" name="addressId" id="addressId" value="{{ $addressId }}" />
    </div>

    <!-- ==========================================
         RIGHT SIDEBAR: AI Location Discovery
         ========================================== -->
    <aside class="client-side-column">
      
      <!-- AI Location Discovery Card -->
      <div class="card client-helper-card client-ai-location-card" style="background:#faf8ff; border:1px solid #ede9fe; border-radius:12px; padding:14px 16px !important; margin-bottom:12px !important;">
        <!-- Header -->
        <div class="d-flex align-items-center justify-content-between mb-1">
          <div class="d-flex align-items-center gap-2">
            <div style="width:26px; height:26px; border-radius:7px; background:#ede9fe; display:inline-flex; align-items:center; justify-content:center; color:#7040f5;">
              <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#7040f5" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                <path d="m12 3-1.9 5.8a2 2 0 0 1-1.3 1.3L3 12l5.8 1.9a2 2 0 0 1 1.3 1.3L12 21l1.9-5.8a2 2 0 0 1 1.3-1.3L21 12l-5.8-1.9a2 2 0 0 1-1.3-1.3L12 3z"/>
              </svg>
            </div>
            <h4 style="font-size:13.5px; font-weight:700; color:#101828; margin:0;">AI Location Discovery</h4>
          </div>
          <span class="badge" style="background:#ede9fe; color:#7040f5; font-weight:600; font-size:10px; padding:2px 7px; border-radius:6px;">Beta</span>
        </div>
        <p style="font-size:11px; color:#64748b; line-height:1.45; margin-bottom:12px;">We found 3 locations from the company website. Review and add them to your client profile.</p>

        <!-- Discovered Location 1: Pune (Recommended HQ) -->
        <div class="client-ai-loc-item mb-2" id="ai_loc_card_0" style="background:#f0fdf4; border:1px solid #bbf7d0; border-radius:10px; padding:12px 14px;">
          <div class="d-flex align-items-start justify-content-between mb-1">
            <div class="d-flex align-items-start gap-2">
              <div style="width:28px; height:28px; border-radius:7px; background:#dcfce7; display:inline-flex; align-items:center; justify-content:center; color:#16a34a; flex-shrink:0; margin-top:2px;">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="2"><path d="M3 21h18"/><path d="M5 21V7l8-4v18"/><path d="M19 21V11l-6-4"/></svg>
              </div>
              <div>
                <div class="d-flex align-items-center gap-2">
                  <span style="font-size:13.5px; font-weight:700; color:#0f172a;">Pune, Maharashtra, India</span>
                  <span class="badge" style="background:#dcfce7; color:#15803d; border-radius:9999px; font-size:9.5px; font-weight:600; padding:2px 8px;">Recommended</span>
                </div>
                <div style="font-size:11px; font-weight:600; color:#334155; margin-top:1px;">Corporate Office / Headquarters</div>
                <p style="font-size:10.5px; color:#64748b; margin:2px 0 0 0;">Listed as primary office on the company website.</p>
              </div>
            </div>
          </div>
          <!-- Meta & Actions -->
          <div class="d-flex align-items-center justify-content-between mt-2 pt-1 border-top flex-wrap gap-2" style="border-color:rgba(187,247,208,0.6) !important;">
            <div class="d-flex align-items-center gap-2" style="font-size:10.5px; color:#64748b;">
              <a href="javascript:void(0);" class="client-ai-source-link" style="color:#2563eb; text-decoration:none; display:inline-flex; align-items:center; gap:2px;">
                <span>Source: Company Website</span>
                <svg width="9" height="9" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path><polyline points="15 3 21 3 21 9"></polyline><line x1="10" y1="14" x2="21" y2="3"></line></svg>
              </a>
              <span class="d-inline-flex align-items-center gap-1" style="color:#16a34a; font-weight:500;">
                <span style="width:5px; height:5px; border-radius:50%; background:#16a34a; display:inline-block;"></span> Verified
              </span>
              <span>Checked recently</span>
            </div>
            <div class="d-flex align-items-center gap-2" id="ai_loc_actions_0">
              <button type="button" class="btn btn-sm" onclick="window.clientLocationManager ? window.clientLocationManager.addAiLocation(0, true) : false;" style="background:#f59e0b; color:#78350f; font-weight:600; font-size:11px; border:none; border-radius:6px; padding:4px 10px; cursor:pointer;">
                Set as Headquarters
              </button>
              <button type="button" class="btn btn-sm" onclick="window.clientLocationManager ? window.clientLocationManager.addAiLocation(0, false) : false;" style="background:#ffffff; color:#2563eb; border:1px solid #2563eb; font-weight:600; font-size:11px; border-radius:6px; padding:4px 10px; cursor:pointer;">
                Add as Office
              </button>
            </div>
          </div>
        </div>

        <!-- Discovered Location 2: Bengaluru (Development Centre) -->
        <div class="client-ai-loc-item mb-2" id="ai_loc_card_1" style="background:#ffffff; border:1px solid #e2e8f0; border-radius:10px; padding:12px 14px;">
          <div class="d-flex align-items-start justify-content-between mb-1">
            <div class="d-flex align-items-start gap-2">
              <div style="width:28px; height:28px; border-radius:7px; background:#f1f5f9; display:inline-flex; align-items:center; justify-content:center; color:#475569; flex-shrink:0; margin-top:2px;">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#475569" stroke-width="2"><path d="M3 21h18"/><path d="M5 21V7l8-4v18"/><path d="M19 21V11l-6-4"/></svg>
              </div>
              <div>
                <span style="font-size:13.5px; font-weight:700; color:#0f172a;">Bengaluru, Karnataka, India</span>
                <div style="font-size:11px; font-weight:600; color:#334155; margin-top:1px;">Development Centre</div>
                <p style="font-size:10.5px; color:#64748b; margin:2px 0 0 0;">Listed under our locations page.</p>
              </div>
            </div>
          </div>
          <!-- Meta & Actions -->
          <div class="d-flex align-items-center justify-content-between mt-2 pt-1 border-top flex-wrap gap-2" style="border-color:#f1f5f9 !important;">
            <div class="d-flex align-items-center gap-2" style="font-size:10.5px; color:#64748b;">
              <a href="javascript:void(0);" class="client-ai-source-link" style="color:#2563eb; text-decoration:none; display:inline-flex; align-items:center; gap:2px;">
                <span>Source: Company Website</span>
                <svg width="9" height="9" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path><polyline points="15 3 21 3 21 9"></polyline><line x1="10" y1="14" x2="21" y2="3"></line></svg>
              </a>
              <span class="d-inline-flex align-items-center gap-1" style="color:#64748b; font-weight:500;">
                <span style="width:5px; height:5px; border-radius:50%; background:#64748b; display:inline-block;"></span> Likely location
              </span>
              <span>Checked recently</span>
            </div>
            <div class="d-flex align-items-center gap-2" id="ai_loc_actions_1">
              <button type="button" class="btn btn-sm" onclick="window.clientLocationManager ? window.clientLocationManager.addAiLocation(1, false) : false;" style="background:#ffffff; color:#2563eb; border:1px solid #2563eb; font-weight:600; font-size:11px; border-radius:6px; padding:4px 10px; cursor:pointer;">
                Add as Office
              </button>
            </div>
          </div>
        </div>

        <!-- Discovered Location 3: Mumbai (Office) -->
        <div class="client-ai-loc-item mb-3" id="ai_loc_card_2" style="background:#ffffff; border:1px solid #e2e8f0; border-radius:10px; padding:12px 14px;">
          <div class="d-flex align-items-start justify-content-between mb-1">
            <div class="d-flex align-items-start gap-2">
              <div style="width:28px; height:28px; border-radius:7px; background:#f1f5f9; display:inline-flex; align-items:center; justify-content:center; color:#475569; flex-shrink:0; margin-top:2px;">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#475569" stroke-width="2"><path d="M3 21h18"/><path d="M5 21V7l8-4v18"/><path d="M19 21V11l-6-4"/></svg>
              </div>
              <div>
                <span style="font-size:13.5px; font-weight:700; color:#0f172a;">Mumbai, Maharashtra, India</span>
                <div style="font-size:11px; font-weight:600; color:#334155; margin-top:1px;">Office</div>
                <p style="font-size:10.5px; color:#64748b; margin:2px 0 0 0;">Listed as regional office.</p>
              </div>
            </div>
          </div>
          <!-- Meta & Actions -->
          <div class="d-flex align-items-center justify-content-between mt-2 pt-1 border-top flex-wrap gap-2" style="border-color:#f1f5f9 !important;">
            <div class="d-flex align-items-center gap-2" style="font-size:10.5px; color:#64748b;">
              <a href="javascript:void(0);" class="client-ai-source-link" style="color:#2563eb; text-decoration:none; display:inline-flex; align-items:center; gap:2px;">
                <span>Source: Company Website</span>
                <svg width="9" height="9" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path><polyline points="15 3 21 3 21 9"></polyline><line x1="10" y1="14" x2="21" y2="3"></line></svg>
              </a>
              <span class="d-inline-flex align-items-center gap-1" style="color:#64748b; font-weight:500;">
                <span style="width:5px; height:5px; border-radius:50%; background:#64748b; display:inline-block;"></span> Likely location
              </span>
              <span>Checked recently</span>
            </div>
            <div class="d-flex align-items-center gap-2" id="ai_loc_actions_2">
              <button type="button" class="btn btn-sm" onclick="window.clientLocationManager ? window.clientLocationManager.addAiLocation(2, false) : false;" style="background:#ffffff; color:#2563eb; border:1px solid #2563eb; font-weight:600; font-size:11px; border-radius:6px; padding:4px 10px; cursor:pointer;">
                Add as Office
              </button>
            </div>
          </div>
        </div>

        <!-- Bulk Action Row -->
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 pt-1">
          <button type="button" class="btn btn-sm" id="btn_add_all_ai_locations" onclick="window.clientLocationManager ? window.clientLocationManager.addAllAiLocations() : false;" style="background:#eff6ff; border:1px solid #bfdbfe; color:#1d4ed8; font-weight:600; font-size:11.5px; border-radius:6px; padding:6px 14px; display:inline-flex; align-items:center; gap:6px; cursor:pointer;">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="12 2 2 7 12 12 22 7 12 2"></polygon><polyline points="2 17 12 22 22 17"></polyline><polyline points="2 12 12 17 22 12"></polyline></svg>
            <span>Add all 3 locations</span>
          </button>
          <a href="javascript:void(0);" id="ai_view_source_link" target="_blank" style="color:#2563eb; text-decoration:none; font-size:11px; font-weight:500; display:inline-flex; align-items:center; gap:3px;">
            <span>View source</span>
            <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path><polyline points="15 3 21 3 21 9"></polyline><line x1="10" y1="14" x2="21" y2="3"></line></svg>
          </a>
        </div>
      </div>

      <!-- Quick Tips Card (Yellow banner matching media_1792565792953_2202cf86.png) -->
      <div class="card client-helper-card client-addr-tips-card" style="background:#fefce8; border:1px solid #fef08a; border-radius:12px; padding:14px 16px !important; margin-bottom:0 !important;">
        <div class="d-flex align-items-center gap-2 mb-2">
          <div style="width:26px; height:26px; border-radius:50%; background:#fef08a; display:inline-flex; align-items:center; justify-content:center; color:#854d0e; flex-shrink:0;">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#854d0e" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M9 18h6"></path>
              <path d="M10 22h4"></path>
              <path d="M12 2a7 7 0 0 0-7 7c0 2.38 1.19 4.47 3 5.74V17a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1v-2.26c1.81-1.27 3-3.36 3-5.74a7 7 0 0 0-7-7z"></path>
            </svg>
          </div>
          <h4 style="font-size:13px; font-weight:700; color:#713f12; margin:0;">Quick Tips</h4>
        </div>
        <div style="font-size:11px; color:#78350f; line-height:1.6;">
          <div class="d-flex align-items-start gap-2 mb-1">
            <svg width="13" height="13" viewBox="0 0 20 20" fill="#eab308" class="flex-shrink-0" style="margin-top:2px;"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
            <span>Start typing a city name to search our global database.</span>
          </div>
          <div class="d-flex align-items-start gap-2 mb-1">
            <svg width="13" height="13" viewBox="0 0 20 20" fill="#eab308" class="flex-shrink-0" style="margin-top:2px;"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
            <span>The first location will be marked as the primary headquarters.</span>
          </div>
          <div class="d-flex align-items-start gap-2 mb-1">
            <svg width="13" height="13" viewBox="0 0 20 20" fill="#eab308" class="flex-shrink-0" style="margin-top:2px;"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
            <span>Add branch offices or regional centres as additional locations.</span>
          </div>
          <div class="d-flex align-items-start gap-2">
            <svg width="13" height="13" viewBox="0 0 20 20" fill="#eab308" class="flex-shrink-0" style="margin-top:2px;"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
            <span>Click &ldquo;Add as Office&rdquo; to quickly use AI suggested locations.</span>
          </div>
        </div>
      </div>

    </aside>

  </div>
</div>

<style>
  /* ==========================================================================
     PAGE 3: COMPANY LOCATIONS & ADDRESS DETAILS STYLING (15.6" LAPTOP FIT)
     ========================================================================== */
  .client-address-layout {
    display: grid !important;
    width: 100% !important;
    max-width: 100% !important;
    min-width: 0 !important;
    grid-template-columns: minmax(0, 1.4fr) minmax(0, 1fr) !important;
    gap: 16px !important;
    align-items: start !important;
    box-sizing: border-box !important;
  }
  @media (max-width: 992px) {
    .client-address-layout {
      grid-template-columns: 1fr !important;
      gap: 14px !important;
    }
  }

  /* Form Fields */
  .client-addr-input {
    width: 100%;
    height: 38px;
    padding: 0 34px 0 34px;
    border: 1px solid #cbd5e1;
    border-radius: 8px;
    font-size: 12px;
    color: #1e293b;
    background: #ffffff;
    transition: border-color .15s ease, box-shadow .15s ease;
  }
  .client-addr-input:focus {
    outline: none;
    border-color: #2563eb;
    box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15);
  }
  .client-addr-input.is-invalid {
    border-color: #ef4444 !important;
    box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.12) !important;
  }

  /* Textarea */
  .client-addr-textarea {
    width: 100%;
    min-height: 58px;
    padding: 8px 12px 8px 34px;
    border: 1px solid #cbd5e1;
    border-radius: 8px;
    font-size: 12px;
    color: #1e293b;
    line-height: 1.5;
    background: #ffffff;
    transition: border-color .15s ease, box-shadow .15s ease;
    resize: vertical;
  }
  .client-addr-textarea:focus {
    outline: none;
    border-color: #2563eb;
    box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15);
  }
  .client-addr-textarea.is-invalid {
    border-color: #ef4444 !important;
    box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.12) !important;
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

  /* Compact Viewport Optimization for 15.6" Screens (1366×768 & 1920×1080 at 125%/150%) */
  @media (max-height: 820px) {
    .client-addr-top-banner {
      padding: 7px 12px !important;
      margin-bottom: 10px !important;
    }
    .client-section-card > .card-header {
      padding: 10px 14px !important;
    }
    .client-section-card > .card-body {
      padding: 12px 14px !important;
    }
    .client-addr-input {
      height: 35px !important;
      font-size: 11.5px !important;
    }
    .client-addr-textarea {
      min-height: 52px !important;
      font-size: 11.5px !important;
      padding: 6px 10px 6px 32px !important;
    }
    .client-ai-location-card {
      padding: 12px 14px !important;
    }
    .client-ai-loc-item {
      padding: 10px 12px !important;
      margin-bottom: 8px !important;
    }
  }
</style>
<!--end::Company Locations & Address Details-->