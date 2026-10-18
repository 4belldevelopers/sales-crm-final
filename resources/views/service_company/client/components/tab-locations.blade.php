<!--begin::Address Details-->
<?php
  /**
   * JERO GOD-LEVEL CANONICAL LOCATION & ADDRESS COMPONENT (V5 REDESIGN)
   * Scoped strictly to Service Company Client Onboarding.
   * Matches User Reference (media_1792064818634.png) Exactly.
   */
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
?>

<div class="pb-5 client-address-step" data-wizard-type="step-content">
  <div class="client-step-layout client-address-layout">
    <!-- LEFT MAIN COLUMN -->
    <div class="client-main-column">
      <!-- Header Banner Matching media_1792064818634.png -->
      <div class="client-addr-header-wrap">
        <div class="client-addr-header-top">
          <span class="client-addr-header-badge">
            <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="#1e293b" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/>
              <circle cx="12" cy="10" r="3"/>
            </svg>
          </span>
          <div>
            <h3 class="client-addr-header-title">Address Details</h3>
            <p class="client-addr-header-sub">Enter the city and full address of this client.</p>
          </div>
        </div>

        <!-- Light-blue Info Bar -->
        <div class="client-addr-info-banner">
          <svg class="client-addr-info-icon" viewBox="0 0 20 20" width="18" height="18" fill="currentColor">
            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
          </svg>
          <span>The first address you enter will be considered as the headquarters.</span>
        </div>
      </div>

      <!-- HEADQUARTERS (PRIMARY) CARD -->
      <div class="client-addr-card" id="client_addr_card_0">
        <div class="client-addr-card-head">
          <div class="client-addr-head-left">
            <span class="client-addr-icon-badge">
              <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="#1e293b" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                <polyline points="9 22 9 12 15 12 15 22"/>
              </svg>
            </span>
            <div>
              <h4 class="client-addr-title">Headquarters (Primary)</h4>
              <p class="client-addr-subtitle">Main office location and street address</p>
            </div>
          </div>
          <span class="client-addr-pill-badge">Primary</span>
        </div>

        <div class="client-addr-card-body">
          <!-- City Field -->
          <div class="client-addr-field">
            <label class="client-addr-label" for="city_search_0">City <span class="client-addr-required">*</span></label>
            <div class="client-addr-input-wrap">
              <svg class="client-addr-icon-left" viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="11" cy="11" r="8"/>
                <path d="m21 21-4.3-4.3"/>
              </svg>
              <input type="text" class="client-addr-input client-addr-city-search" id="city_search_0" data-cid="0" value="{{ $primary_city }}" placeholder="Search for a city..." autocomplete="off" spellcheck="false">
              <svg class="client-addr-icon-right" viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2">
                <polyline points="6 9 12 15 18 9"></polyline>
              </svg>
              <div class="client-addr-city-dropdown" id="city_dropdown_0"></div>
              <input type="hidden" name="kt_tagify_work_location[]" id="kt_tagify_work_location_0" value="{{ $primary_tagify_json }}">
            </div>
            <!-- Red Inline Error for City -->
            <div class="client-addr-error" id="city_err_0" style="display:none;">
              <svg viewBox="0 0 24 24" width="15" height="15" fill="currentColor">
                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/>
              </svg>
              <span>City selection is required for the primary location.</span>
            </div>
          </div>

          <!-- Full Address Field -->
          <div class="client-addr-field mt-4">
            <label class="client-addr-label" for="address_0">Full Address <span class="client-addr-required">*</span></label>
            <div class="client-addr-textarea-wrap">
              <svg class="client-addr-textarea-icon" viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2">
                <rect x="4" y="2" width="16" height="20" rx="2" ry="2"/>
                <path d="M9 22v-4h6v4"/>
                <path d="M8 6h.01"/>
                <path d="M16 6h.01"/>
                <path d="M12 6h.01"/>
                <path d="M12 10h.01"/>
                <path d="M12 14h.01"/>
                <path d="M16 10h.01"/>
                <path d="M16 14h.01"/>
                <path d="M8 10h.01"/>
                <path d="M8 14h.01"/>
              </svg>
              <textarea class="client-addr-textarea" name="address[]" id="address_0" rows="3" maxlength="500" placeholder="Building name, street, area, landmark, pincode, etc.">{{ $primary_address }}</textarea>
            </div>
            <div class="client-addr-textarea-footer">
              <span class="client-addr-help-text">Provide the complete registered or headquarters address for official correspondence.</span>
              <span class="client-addr-counter"><span id="address_char_count_0">{{ strlen($primary_address) }}</span>/500</span>
            </div>
            <!-- Red Inline Error for Full Address -->
            <div class="client-addr-error" id="address_err_0" style="display:none;">
              <svg viewBox="0 0 24 24" width="15" height="15" fill="currentColor">
                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/>
              </svg>
              <span>Full Address is required for the primary location.</span>
            </div>
          </div>

          <!-- Timezone & Core Hours Overlap (Auto-Inferred via HQ City) -->
          <div class="mt-4 pt-3" style="border-top:1px dashed #e2e8f0; display:grid; grid-template-columns:repeat(2, minmax(0, 1fr)); gap:16px;">
            <div>
              <label class="client-addr-label" for="timezone">Primary Operating Timezone</label>
              <div class="client-addr-input-wrap">
                <svg class="client-addr-icon-left" viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2">
                  <circle cx="12" cy="12" r="10"/>
                  <polyline points="12 6 12 12 16 14"/>
                </svg>
                <input type="text" class="client-addr-input" name="timezone" id="timezone" value="{{ @$detail_arr['timezone'] ?? '' }}" placeholder="e.g. America/New_York (UTC-5) or Asia/Kolkata (UTC+5:30)">
              </div>
              <span class="client-addr-help-text">Auto-calculated from headquarters city coordinates.</span>
            </div>
            <div>
              <label class="client-addr-label" for="required_hours_overlap">Daily Hours Overlap Required</label>
              <div class="client-addr-input-wrap">
                <svg class="client-addr-icon-left" viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2">
                  <circle cx="12" cy="12" r="3"/>
                  <path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M4.93 19.07l1.41-1.41M17.66 6.34l1.41-1.41"/>
                </svg>
                <input type="text" class="client-addr-input" name="required_hours_overlap" id="required_hours_overlap" value="{{ @$detail_arr['required_hours_overlap'] ?? '' }}" placeholder="e.g. 4 Hours Overlap with US Eastern">
              </div>
              <span class="client-addr-help-text">Mandatory schedule overlap for distributed candidate placement.</span>
            </div>
          </div>
        </div>
      </div>

      <!-- ADDITIONAL ADDRESS (OPTIONAL) CARD -->
      <div class="client-addr-card" id="client_addr_additional_card">
        <div class="client-addr-card-head">
          <div class="client-addr-head-left">
            <span class="client-addr-icon-badge">
              <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="#1e293b" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M3 21h18"/>
                <path d="M5 21V7l8-4v18"/>
                <path d="M19 21V11l-6-4"/>
                <path d="M9 9h1"/>
                <path d="M9 13h1"/>
                <path d="M9 17h1"/>
              </svg>
            </span>
            <div>
              <h4 class="client-addr-title">Additional Address (Optional)</h4>
              <p class="client-addr-subtitle">Add branch offices or other locations.</p>
            </div>
          </div>
          <button type="button" class="client-addr-btn-add" id="btn_add_another_address">
            + Add Another Address
          </button>
        </div>

        <div class="client-addr-card-body">
          <!-- Dynamic Branches List -->
          <div class="client-additional-list" id="additional_addresses_container">
            @if (!empty($additional_addresses))
              @foreach ($additional_addresses as $addIdx => $addrItem)
                <div class="client-branch-card" id="clientAddr_{{ $addrItem['id'] }}" data-cid="{{ $addrItem['id'] }}">
                  <div class="client-branch-head">
                    <div class="client-addr-head-left">
                      <span class="client-addr-icon-badge">
                        <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="#1e293b" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                          <path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/>
                          <circle cx="12" cy="10" r="3"/>
                        </svg>
                      </span>
                      <div>
                        <h4 class="client-branch-title">Additional Address ({{ $addIdx + 1 }})</h4>
                        <p class="client-addr-subtitle">Branch or regional office location and street address</p>
                      </div>
                    </div>
                    <button type="button" class="client-branch-remove-btn" onclick="removeAddressCard(this, 'clientAddr_{{ $addrItem['id'] }}')">
                      <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" style="margin-right:4px;">
                        <polyline points="3 6 5 6 21 6"></polyline>
                        <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                      </svg>
                      Remove
                    </button>
                  </div>

                  <div class="client-branch-body">
                    <!-- Branch City -->
                    <div class="client-addr-field">
                      <label class="client-addr-label" for="branch_city_search_{{ $addrItem['id'] }}">City <span class="client-addr-required">*</span></label>
                      <div class="client-addr-input-wrap">
                        <svg class="client-addr-icon-left" viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2">
                          <circle cx="11" cy="11" r="8"/>
                          <path d="m21 21-4.3-4.3"/>
                        </svg>
                        <input type="text" class="client-addr-input client-addr-city-search branch-city-search" id="branch_city_search_{{ $addrItem['id'] }}" data-cid="{{ $addrItem['id'] }}" value="{{ $addrItem['city'] }}" placeholder="Search branch city..." autocomplete="off" spellcheck="false">
                        <svg class="client-addr-icon-right" viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2">
                          <polyline points="6 9 12 15 18 9"></polyline>
                        </svg>
                        <div class="client-addr-city-dropdown branch-city-dropdown" id="branch_city_dropdown_{{ $addrItem['id'] }}"></div>
                        <input type="hidden" name="kt_tagify_work_location[]" id="kt_tagify_work_location_{{ $addrItem['id'] }}" value='{{ $addrItem['tagify_json'] }}'>
                      </div>
                      <div class="client-addr-error" id="branch_city_err_{{ $addrItem['id'] }}" style="display:none;">
                        <svg viewBox="0 0 24 24" width="15" height="15" fill="currentColor"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/></svg>
                        <span>City selection is required for Additional Address ({{ $addIdx + 1 }}).</span>
                      </div>
                    </div>

                    <!-- Branch Full Address -->
                    <div class="client-addr-field mt-3">
                      <label class="client-addr-label" for="address_{{ $addrItem['id'] }}">Full Address <span class="client-addr-required">*</span></label>
                      <div class="client-addr-textarea-wrap">
                        <svg class="client-addr-textarea-icon" viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2">
                          <rect x="4" y="2" width="16" height="20" rx="2" ry="2"/>
                          <path d="M9 22v-4h6v4"/>
                          <path d="M8 6h.01"/>
                          <path d="M16 6h.01"/>
                          <path d="M12 6h.01"/>
                        </svg>
                        <textarea class="client-addr-textarea" name="address[]" id="address_{{ $addrItem['id'] }}" rows="3" maxlength="500" placeholder="Building name, street, area, landmark, pincode, etc.">{{ $addrItem['address'] }}</textarea>
                      </div>
                      <div class="client-addr-textarea-footer">
                        <span class="client-addr-help-text">Provide the complete and accurate branch office address.</span>
                        <span class="client-addr-counter"><span id="address_char_count_{{ $addrItem['id'] }}">{{ strlen($addrItem['address']) }}</span>/500</span>
                      </div>
                      <div class="client-addr-error" id="branch_address_err_{{ $addrItem['id'] }}" style="display:none;">
                        <svg viewBox="0 0 24 24" width="15" height="15" fill="currentColor"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/></svg>
                        <span>Full Address is required for Additional Address ({{ $addIdx + 1 }}).</span>
                      </div>
                    </div>
                  </div>
                </div>
              @endforeach
            @endif
          </div>

          <!-- Empty State Box (Matching media_1792064818634.png) -->
          <div class="client-addr-empty-box" id="client_additional_empty" style="{{ !empty($additional_addresses) ? 'display:none;' : '' }}">
            <div class="client-addr-empty-icon-circle">
              <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="#2563eb" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M3 21h18"/>
                <path d="M5 21V7l8-4v18"/>
                <path d="M19 21V11l-6-4"/>
              </svg>
            </div>
            <p class="client-addr-empty-text">No additional addresses added yet. Click "+ Add Another Address" to add branch offices.</p>
          </div>
        </div>
      </div>

      <input type="hidden" name="addressId" id="addressId" value="{{ $addressId }}" />
    </div>

    <!-- RIGHT SIDEBAR COLUMN (Matching media_1792064818634.png) -->
    <aside class="client-side-column">
      <!-- AI ADDRESS ASSIST CARD (Lavender theme) -->
      <div class="client-addr-ai-card">
        <div class="client-addr-ai-header">
          <div class="client-addr-ai-title-wrap">
            <svg class="client-addr-ai-sparkle" viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="#7c3aed" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
              <path d="m12 3-1.9 5.8a2 2 0 0 1-1.3 1.3L3 12l5.8 1.9a2 2 0 0 1 1.3 1.3L12 21l1.9-5.8a2 2 0 0 1 1.3-1.3L21 12l-5.8-1.9a2 2 0 0 1-1.3-1.3L12 3z"/>
            </svg>
            <h4 class="client-addr-ai-title">AI Address Assist</h4>
            <span class="client-addr-ai-beta">Beta</span>
          </div>
          <p class="client-addr-ai-subtitle">Suggestions based on company profile and domain.</p>
        </div>

        <!-- Inner Box: Suggested Headquarters Address -->
        <div class="client-addr-ai-box">
          <div class="client-addr-ai-box-top">
            <div class="client-addr-ai-box-lbl">
              <svg viewBox="0 0 24 24" width="15" height="15" fill="none" stroke="#7c3aed" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="margin-right:6px;">
                <path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/>
                <circle cx="12" cy="10" r="3"/>
              </svg>
              <span>Suggested Headquarters Address</span>
            </div>
            <button type="button" class="client-addr-ai-apply-btn" id="btn_apply_ai_address_suggestion">
              ✨ Apply Suggestion
            </button>
          </div>
          <div class="client-addr-ai-company" id="ai_suggested_company_name">{{ $client->name ?? 'Jeroworks' }}</div>
          <div class="client-addr-ai-loc" id="ai_suggested_location">Pune, Maharashtra, India</div>
        </div>

        <!-- Other suggestions section -->
        <div class="client-addr-other-wrap">
          <span class="client-addr-other-title">Other suggestions</span>

          <!-- Item 1: Known Office Locations -->
          <div class="client-addr-suggest-item" id="suggest_known_offices_trigger" onclick="toggleSuggestionDetails('known_offices')">
            <div class="client-addr-suggest-left">
              <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="#6b7280" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <rect x="4" y="2" width="16" height="20" rx="2" ry="2"/>
                <path d="M9 22v-4h6v4"/>
                <path d="M8 6h.01"/><path d="M16 6h.01"/><path d="M12 6h.01"/>
              </svg>
              <span>Known Office Locations</span>
            </div>
            <svg class="client-addr-suggest-chevron" id="chevron_known_offices" viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="#9ca3af" stroke-width="2">
              <polyline points="6 9 12 15 18 9"></polyline>
            </svg>
          </div>
          <!-- Item 1 Collapsible Content -->
          <div class="client-addr-suggest-drawer" id="drawer_known_offices" style="display:none;">
            <div class="client-addr-suggest-list">
              <div class="client-addr-suggest-opt" onclick="applySuggestionDirect('Pune, Maharashtra, India')">
                <span class="client-addr-opt-name">Pune</span>
                <span class="client-addr-opt-meta">Maharashtra, India</span>
                <span class="client-addr-opt-action">Use</span>
              </div>
              <div class="client-addr-suggest-opt" onclick="applySuggestionDirect('Bengaluru, Karnataka, India')">
                <span class="client-addr-opt-name">Bengaluru</span>
                <span class="client-addr-opt-meta">Karnataka, India</span>
                <span class="client-addr-opt-action">Use</span>
              </div>
              <div class="client-addr-suggest-opt" onclick="applySuggestionDirect('Mumbai, Maharashtra, India')">
                <span class="client-addr-opt-name">Mumbai</span>
                <span class="client-addr-opt-meta">Maharashtra, India</span>
                <span class="client-addr-opt-action">Use</span>
              </div>
              <div class="client-addr-suggest-opt" onclick="applySuggestionDirect('Hyderabad, Telangana, India')">
                <span class="client-addr-opt-name">Hyderabad</span>
                <span class="client-addr-opt-meta">Telangana, India</span>
                <span class="client-addr-opt-action">Use</span>
              </div>
            </div>
          </div>

          <!-- Item 2: Nearby Cities -->
          <div class="client-addr-suggest-item" id="suggest_nearby_cities_trigger" onclick="toggleSuggestionDetails('nearby_cities')">
            <div class="client-addr-suggest-left">
              <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="#6b7280" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/>
                <circle cx="12" cy="10" r="3"/>
              </svg>
              <span>Nearby Cities</span>
            </div>
            <svg class="client-addr-suggest-chevron" id="chevron_nearby_cities" viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="#9ca3af" stroke-width="2">
              <polyline points="6 9 12 15 18 9"></polyline>
            </svg>
          </div>
          <!-- Item 2 Collapsible Content -->
          <div class="client-addr-suggest-drawer" id="drawer_nearby_cities" style="display:none;">
            <div class="client-addr-suggest-list">
              <div class="client-addr-suggest-opt" onclick="applySuggestionDirect('Mumbai, Maharashtra, India')">
                <span class="client-addr-opt-name">Mumbai</span>
                <span class="client-addr-opt-meta">148 km away</span>
                <span class="client-addr-opt-action">Use</span>
              </div>
              <div class="client-addr-suggest-opt" onclick="applySuggestionDirect('Navi Mumbai, Maharashtra, India')">
                <span class="client-addr-opt-name">Navi Mumbai</span>
                <span class="client-addr-opt-meta">125 km away</span>
                <span class="client-addr-opt-action">Use</span>
              </div>
              <div class="client-addr-suggest-opt" onclick="applySuggestionDirect('Thane, Maharashtra, India')">
                <span class="client-addr-opt-name">Thane</span>
                <span class="client-addr-opt-meta">155 km away</span>
                <span class="client-addr-opt-action">Use</span>
              </div>
            </div>
          </div>

          <!-- Item 3: Pincode Information -->
          <div class="client-addr-suggest-item" id="suggest_pincode_trigger" onclick="toggleSuggestionDetails('pincode')">
            <div class="client-addr-suggest-left">
              <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="#6b7280" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <rect x="2" y="4" width="20" height="16" rx="2"/>
                <path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/>
              </svg>
              <span>Pincode Information</span>
            </div>
            <svg class="client-addr-suggest-chevron" id="chevron_pincode" viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="#9ca3af" stroke-width="2">
              <polyline points="6 9 12 15 18 9"></polyline>
            </svg>
          </div>
          <!-- Item 3 Collapsible Content -->
          <div class="client-addr-suggest-drawer" id="drawer_pincode" style="display:none;">
            <div class="p-2 text-muted" style="font-size:12px; line-height:1.45;">
              Tip: Include the 6-digit postal pincode inside the Full Address field for accurate tax invoices and candidate proximity search.
            </div>
          </div>
        </div>
      </div>

      <!-- QUICK TIPS CARD (Yellow theme matching media_1792064818634.png) -->
      <div class="client-addr-tips-card">
        <div class="client-addr-tips-head">
          <span class="client-addr-tips-badge">
            <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="#854d0e" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M12 2v1"/>
              <path d="M12 21v1"/>
              <path d="M4.93 4.93l.7.7"/>
              <path d="M18.36 18.36l.7.7"/>
              <path d="M2 12h1"/>
              <path d="M21 12h1"/>
              <path d="M4.93 19.07l.7-.7"/>
              <path d="M18.36 5.64l.7-.7"/>
              <path d="M9 18h6"/>
              <path d="M10 22h4"/>
              <path d="M15 9.5a3.5 3.5 0 1 0-7 0c0 1.5 1 2.5 1.5 3.5h4c.5-1 1.5-2 1.5-3.5Z"/>
            </svg>
          </span>
          <h4 class="client-addr-tips-title">Quick Tips</h4>
        </div>
        <div class="client-addr-tips-body">
          <div class="client-addr-tip-bullet">
            <svg class="client-addr-tip-check" viewBox="0 0 20 20" width="16" height="16" fill="#eab308">
              <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
            </svg>
            <span>Start typing a city name to search our global database.</span>
          </div>
          <div class="client-addr-tip-bullet">
            <svg class="client-addr-tip-check" viewBox="0 0 20 20" width="16" height="16" fill="#eab308">
              <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
            </svg>
            <span>The first address is set as your company headquarters.</span>
          </div>
          <div class="client-addr-tip-bullet">
            <svg class="client-addr-tip-check" viewBox="0 0 20 20" width="16" height="16" fill="#eab308">
              <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
            </svg>
            <span>Add branch offices or regional centers as additional addresses.</span>
          </div>
          <div class="client-addr-tip-bullet">
            <svg class="client-addr-tip-check" viewBox="0 0 20 20" width="16" height="16" fill="#eab308">
              <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
            </svg>
            <span>Click "Apply Suggestion" to quickly use the AI suggested headquarters.</span>
          </div>
        </div>
      </div>
    </aside>
  </div>
</div>

<style>
  /* ==========================================================================
     EXACT DESIGN STYLES FOR ADDRESS DETAILS (media_1792064818634.png)
  /* Header Top & Info Banner */
  .client-addr-header-wrap {
    margin-bottom: 20px;
  }
  .client-addr-header-top {
    display: flex;
    align-items: center;
    gap: 12px;
  }
  .client-addr-header-badge {
    width: 44px;
    height: 44px;
    background: #fef9c3;
    border-radius: 10px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
  }
  .client-addr-header-title {
    font-size: 15px;
    font-weight: 700;
    color: #101A46;
    margin: 0 0 2px 0;
    line-height: 1.25;
  }
  .client-addr-header-sub {
    font-size: 12px;
    color: #5C6F9F;
    margin: 0;
  }
  .client-addr-info-banner {
    display: flex;
    align-items: center;
    gap: 10px;
    background: #eff7ff;
    border: 1px solid #dbeafe;
    border-radius: 6px;
    padding: 9px 14px;
    margin-top: 14px;
    color: #1e40af;
    font-size: 12px;
    font-weight: 500;
  }
  .client-addr-info-icon {
    color: #3b82f6;
    flex-shrink: 0;
  }

  /* Main White Cards */
  .client-addr-card {
    background: #ffffff;
    border: 1.5px solid #F6DF83 !important;
    border-radius: 12px;
    padding: 22px 24px;
    margin-bottom: 20px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
  }
  .client-branch-card {
    background: #ffffff;
    border: 1.5px solid #F6DF83 !important;
    border-radius: 12px;
    padding: 20px;
    margin-bottom: 16px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
  }
  .client-addr-card-head {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 18px;
  }
  .client-addr-head-left {
    display: flex;
    align-items: center;
    gap: 12px;
  }
  .client-addr-icon-badge {
    width: 38px;
    height: 38px;
    background: #fef9c3;
    border-radius: 8px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
  }
  .client-addr-title {
    font-size: 15px;
    font-weight: 700;
    color: #0f172a;
    margin: 0 0 2px 0;
  }
  .client-addr-subtitle {
    font-size: 12.5px;
    color: #64748b;
    margin: 0;
  }
  .client-addr-pill-badge {
    background: #fef08a;
    color: #854d0e;
    font-size: 11.5px;
    font-weight: 600;
    padding: 4px 12px;
    border-radius: 9999px;
  }

  /* Form Fields */
  .client-addr-field {
    width: 100%;
  }
  .client-addr-label {
    display: block;
    font-size: 13px;
    font-weight: 600;
    color: #334155;
    margin-bottom: 6px;
  }
  .client-addr-required {
    color: #ef4444;
  }

  /* Input wrapper with search icon left and chevron right */
  .client-addr-input-wrap {
    position: relative;
    width: 100%;
  }
  .client-addr-icon-left {
    position: absolute;
    left: 14px;
    top: 50%;
    transform: translateY(-50%);
    color: #94a3b8;
    pointer-events: none;
    z-index: 2;
  }
  .client-addr-icon-right {
    position: absolute;
    right: 14px;
    top: 50%;
    transform: translateY(-50%);
    color: #94a3b8;
    pointer-events: none;
    z-index: 2;
  }
  .client-addr-input {
    width: 100%;
    height: 44px;
    padding: 0 38px 0 42px;
    border: 1px solid #cbd5e1;
    border-radius: 8px;
    font-size: 13.5px;
    color: #1e293b;
    background: #ffffff;
    transition: border-color .15s ease, box-shadow .15s ease;
  }
  .client-addr-input:focus {
    outline: none;
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15);
  }
  .client-addr-input.is-invalid {
    border-color: #ef4444 !important;
    box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.12) !important;
  }

  /* Dropdown autocomplete */
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
    max-height: 260px;
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
    padding: 9px 14px;
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
    font-size: 13px;
    font-weight: 600;
    color: #1e293b;
  }
  .client-addr-dd-sub {
    font-size: 11.5px;
    color: #64748b;
  }
  .client-addr-dd-badge {
    font-size: 11px;
    background: #f1f5f9;
    color: #475569;
    padding: 2px 7px;
    border-radius: 4px;
  }
  .client-addr-dd-empty {
    padding: 14px;
    text-align: center;
    font-size: 13px;
    color: #64748b;
  }

  /* Textarea wrapper with office icon */
  .client-addr-textarea-wrap {
    position: relative;
    width: 100%;
  }
  .client-addr-textarea-icon {
    position: absolute;
    left: 14px;
    top: 14px;
    color: #94a3b8;
    pointer-events: none;
    z-index: 2;
  }
  .client-addr-textarea {
    width: 100%;
    min-height: 90px;
    padding: 12px 14px 12px 42px;
    border: 1px solid #cbd5e1;
    border-radius: 8px;
    font-size: 13.5px;
    color: #1e293b;
    line-height: 1.5;
    background: #ffffff;
    transition: border-color .15s ease, box-shadow .15s ease;
    resize: vertical;
  }
  .client-addr-textarea:focus {
    outline: none;
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15);
  }
  .client-addr-textarea.is-invalid {
    border-color: #ef4444 !important;
    box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.12) !important;
  }
  .client-addr-textarea-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 6px;
  }
  .client-addr-help-text {
    font-size: 12px;
    color: #64748b;
  }
  .client-addr-counter {
    font-size: 12px;
    color: #94a3b8;
    font-weight: 500;
  }

  /* Inline Error matching media_1792064818634.png */
  .client-addr-error {
    display: flex;
    align-items: center;
    gap: 6px;
    color: #dc2626;
    font-size: 12px;
    font-weight: 500;
    margin-top: 6px;
  }

  /* Additional Address Card Buttons & Empty State */
  .client-addr-btn-add {
    background: #ffffff;
    border: 1.5px solid #2563eb;
    color: #2563eb;
    border-radius: 8px;
    padding: 7px 16px;
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
    transition: all .15s ease;
    display: inline-flex;
    align-items: center;
    gap: 4px;
  }
  .client-addr-btn-add:hover {
    background: #eff6ff;
    border-color: #1d4ed8;
    color: #1d4ed8;
  }
  .client-addr-empty-box {
    border: 1.5px dashed #bfdbfe;
    background: #f8fafc;
    border-radius: 10px;
    padding: 32px 20px;
    text-align: center;
  }
  .client-addr-empty-icon-circle {
    width: 44px;
    height: 44px;
    background: #e0f2fe;
    border-radius: 50%;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 8px;
  }
  .client-addr-empty-text {
    font-size: 13px;
    color: #64748b;
    font-weight: 500;
    margin: 0;
  }

  /* Dynamic Additional Branch Cards */
  .client-branch-card {
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    padding: 18px 20px;
    margin-bottom: 16px;
    background: #ffffff;
    box-shadow: 0 1px 2px rgba(0,0,0,0.02);
  }
  .client-branch-head {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 14px;
  }
  .client-branch-title {
    font-size: 14px;
    font-weight: 700;
    color: #0f172a;
    margin: 0 0 2px 0;
  }
  .client-branch-remove-btn {
    background: #fef2f2;
    border: 1px solid #fee2e2;
    color: #dc2626;
    border-radius: 6px;
    padding: 5px 12px;
    font-size: 12px;
    font-weight: 600;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    transition: all .15s ease;
  }
  .client-branch-remove-btn:hover {
    background: #fee2e2;
    color: #b91c1c;
  }

  /* RIGHT SIDEBAR: AI ADDRESS ASSIST (Lavender Card) */
  .client-addr-ai-card {
    background: #f5f3ff;
    border: 1px solid #ede9fe;
    border-radius: 12px;
    padding: 20px;
  }
  .client-addr-ai-title-wrap {
    display: flex;
    align-items: center;
    gap: 6px;
  }
  .client-addr-ai-title {
    font-size: 16px;
    font-weight: 700;
    color: #2e1065;
    margin: 0;
  }
  .client-addr-ai-beta {
    background: #ede9fe;
    color: #7c3aed;
    font-size: 11px;
    font-weight: 600;
    padding: 2px 8px;
    border-radius: 9999px;
  }
  .client-addr-ai-subtitle {
    font-size: 12px;
    color: #6b7280;
    margin: 4px 0 0 0;
  }
  .client-addr-ai-box {
    background: #ffffff;
    border: 1px solid #e9d5ff;
    border-radius: 10px;
    padding: 14px 16px;
    margin-top: 14px;
  }
  .client-addr-ai-box-top {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 8px;
  }
  .client-addr-ai-box-lbl {
    display: flex;
    align-items: center;
    font-size: 12.5px;
    font-weight: 600;
    color: #581c87;
  }
  .client-addr-ai-apply-btn {
    background: #f5f3ff;
    border: 1px solid #ddd6fe;
    color: #7c3aed;
    font-size: 11.5px;
    font-weight: 600;
    border-radius: 6px;
    padding: 4px 10px;
    cursor: pointer;
    white-space: nowrap;
    transition: all .15s ease;
  }
  .client-addr-ai-apply-btn:hover {
    background: #ede9fe;
    color: #6d28d9;
  }
  .client-addr-ai-company {
    font-size: 14px;
    font-weight: 700;
    color: #0f172a;
    margin-top: 8px;
  }
  .client-addr-ai-loc {
    font-size: 12.5px;
    color: #64748b;
    margin-top: 2px;
  }

  /* Other suggestions */
  .client-addr-other-wrap {
    margin-top: 16px;
  }
  .client-addr-other-title {
    display: block;
    font-size: 11px;
    font-weight: 700;
    color: #6b7280;
    margin-bottom: 8px;
    letter-spacing: 0.5px;
    text-transform: uppercase;
  }
  .client-addr-suggest-item {
    background: #ffffff;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    padding: 10px 14px;
    margin-bottom: 8px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    cursor: pointer;
    font-size: 12.5px;
    font-weight: 500;
    color: #374151;
    transition: background .15s;
  }
  .client-addr-suggest-item:hover {
    background: #f9fafb;
    border-color: #d1d5db;
  }
  .client-addr-suggest-left {
    display: flex;
    align-items: center;
    gap: 8px;
  }
  .client-addr-suggest-chevron {
    transition: transform .2s ease;
  }
  .client-addr-suggest-chevron.is-open {
    transform: rotate(180deg);
  }
  .client-addr-suggest-drawer {
    background: #ffffff;
    border: 1px solid #e5e7eb;
    border-top: none;
    border-radius: 0 0 8px 8px;
    margin-top: -8px;
    margin-bottom: 8px;
    padding: 8px 10px;
  }
  .client-addr-suggest-opt {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 6px 8px;
    border-radius: 6px;
    cursor: pointer;
    font-size: 12px;
    transition: background .15s;
  }
  .client-addr-suggest-opt:hover {
    background: #f3f4f6;
  }
  .client-addr-opt-name {
    font-weight: 600;
    color: #1f2937;
  }
  .client-addr-opt-meta {
    color: #6b7280;
    font-size: 11px;
  }
  .client-addr-opt-action {
    background: #eff6ff;
    color: #2563eb;
    font-weight: 600;
    padding: 2px 7px;
    border-radius: 4px;
    font-size: 11px;
  }

  /* RIGHT SIDEBAR: QUICK TIPS (Yellow Card) */
  .client-addr-tips-card {
    background: #fefce8;
    border: 1px solid #fef08a;
    border-radius: 12px;
    padding: 20px;
    margin-top: 16px;
  }
  .client-addr-tips-head {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 14px;
  }
  .client-addr-tips-badge {
    width: 32px;
    height: 32px;
    background: #fef08a;
    border-radius: 50%;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
  }
  .client-addr-tips-title {
    font-size: 15px;
    font-weight: 700;
    color: #713f12;
    margin: 0;
  }
  .client-addr-tip-bullet {
    display: flex;
    align-items: flex-start;
    gap: 8px;
    font-size: 12.5px;
    color: #78350f;
    line-height: 1.45;
    margin-bottom: 11px;
  }
  .client-addr-tip-bullet:last-child {
    margin-bottom: 0;
  }
  .client-addr-tip-check {
    flex-shrink: 0;
    margin-top: 2px;
  }
</style>
<!--end::Address Details-->
