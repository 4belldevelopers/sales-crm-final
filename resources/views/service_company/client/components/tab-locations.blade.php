<!--begin::Address Details-->
<?php
  /**
   * JERO GOD-LEVEL CANONICAL LOCATION & ADDRESS COMPONENT (V4 ARCHITECTURE)
   * Scoped strictly to Service Company Client Onboarding.
   * Matches Job Create Location Architecture & User Reference (media_1792046189393.png & media_1792046199055.png)
   */
  $initialCities = [];
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
          if ($canonical && !in_array($canonical, $initialCities)) {
              $initialCities[] = $canonical;
          }

          if ($idx === 0) {
              $primary_address = $clVal->address ?? '';
          } else {
              $tagifyJson = json_encode([['value' => $cName . ($sName ? ' | ' . $sName : '') . ($coName ? ' | ' . $coName : '')]]);
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
  <div class="client-step-layout">
    <div class="client-main-column">
      <div class="client-step-header mb-3">
        <div>
          <h3>Address Details</h3>
          <p>Specify the work locations and physical headquarters address for this client.</p>
        </div>
      </div>

      <!-- V4 CANONICAL LOCATION ENGINE CARD (Exact match to Job Create & User Attachments) -->
      <div class="card ij-location-card client-location-picker-card mb-4">
        <div class="card-body p-0">
          <!-- Section Banner Header with yellow gradient & yellow pin icon badge -->
          <div class="ij-location-card-head">
            <div class="ij-location-head-left">
              <span class="ij-location-head-badge">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/>
                  <circle cx="12" cy="10" r="3"/>
                </svg>
              </span>
              <div>
                <h4 class="ij-location-title">Client Location or Work Location</h4>
                <p class="ij-location-subtitle">Select up to 15 cities where this client is based.</p>
              </div>
            </div>
          </div>

          <div class="ij-location-card-body">
            <!-- L1 Quick-Select Popular Hubs Bar -->
            <div class="ij-popular-hubs-wrap">
              <span class="ij-popular-label">Popular Hubs:</span>
              <div class="ij-popular-pills" id="popularHubsPills"></div>
            </div>

            <!-- Search Bar with left magnifying glass icon, Elasticsearch spinner & clear 'x' button -->
            <div class="form-group-cus full-width mb-0">
              <div class="input-wrapper ij-location-input-wrap">
                <div class="input-with-icon">
                  <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="11" cy="11" r="8"/>
                    <path d="m21 21-4.3-4.3"/>
                  </svg>
                  <input type="text" class="form-input ij-location-search-input" id="citySearch" placeholder="Search for cities (e.g. Bangalore, Mumbai, London, New York)..." autocomplete="off" spellcheck="false">
                </div>
                <span class="ij-search-spinner" id="citySearchSpinner" style="display:none;" title="Searching Elasticsearch..."></span>
                <button type="button" class="ij-search-clear" id="citySearchClear" style="display:none;" onclick="clearCitySearchInput()" title="Clear input">&times;</button>
                <div class="search-dropdown ij-city-dropdown" id="cityDropdown"></div>
              </div>
            </div>

            <!-- Selected Locations Header (Selected Locations (X/15) + Clear All) -->
            <div class="selected-header ij-selected-header">
              <span class="selected-label">Selected Locations (<span id="selectedCount">0</span>/15)</span>
              <button type="button" class="clear-all" onclick="clearAllCities()">Clear All</button>
            </div>

            <!-- Selected Chips Container -->
            <div class="chips-container ij-selected-chips" id="selectedCities"></div>

            <!-- Hidden input for form sync (Primary Headquarters location) -->
            <input type="hidden" id="kt_tagify_work_location" name="kt_tagify_work_location[]" value="">
          </div>
        </div>
      </div>

      <!-- HEADQUARTERS PHYSICAL STREET ADDRESS CARD (Styled with same design tokens) -->
      <div class="card ij-location-card client-address-card client-address-container-child mt-4" id="clientAddr_0">
        <div class="card-body p-0">
          <div class="ij-location-card-head">
            <div class="ij-location-head-left">
              <span class="ij-location-head-badge">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M3 21h18"/>
                  <path d="M5 21V7l8-4v18"/>
                  <path d="M19 21V11l-6-4"/>
                  <path d="M9 9h1"/>
                  <path d="M9 13h1"/>
                  <path d="M9 17h1"/>
                </svg>
              </span>
              <div>
                <h4 class="ij-location-title">Headquarters (Primary) Physical Address <span class="text-danger">*</span></h4>
                <p class="ij-location-subtitle">The registered street address for legal contracts, billing, and official correspondence</p>
              </div>
            </div>
          </div>
          <div class="ij-location-card-body">
            <div class="client-field fv-plugins-icon-container">
              <label class="form-label required" for="address_0">Street / Building Address</label>
              <textarea class="form-control form-control-solid client-address-textarea" name="address[]" rows="3" maxlength="500" placeholder="Building, street, suite/floor, area, landmark, pincode, etc." id="address_0">{{ $primary_address }}</textarea>
              <div class="d-flex justify-content-between mt-1">
                <span class="form-text text-muted">Provide the complete registered or headquarters address for official correspondence.</span>
                <span class="client-char-count"><span class="client-address-count" id="address_char_count_0">0</span>/500</span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- ADDITIONAL BRANCH OFFICES SECTION -->
      <section class="mt-4">
        <!-- Add Another Address Action Card -->
        <div class="card border border-dashed border-gray-300 rounded p-4 bg-white mb-3">
          <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
            <div class="d-flex align-items-center gap-3">
              <span class="ij-location-head-badge" style="background:#e0f2fe; color:#0284c7;">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <circle cx="12" cy="12" r="10"/>
                  <path d="M12 8v8"/>
                  <path d="M8 12h8"/>
                </svg>
              </span>
              <div>
                <h5 class="mb-1 fw-bold text-dark fs-6">Additional Branch Offices <span class="text-muted fw-normal">(Optional)</span></h5>
                <p class="text-muted fs-8 mb-0">Add secondary physical offices with branch city and street address details</p>
              </div>
            </div>
            <button type="button" class="btn btn-sm btn-primary fw-bold add_address d-inline-flex align-items-center gap-2 px-4 py-2">
              <i class="la la-plus"></i> Add Another Address
            </button>
          </div>
        </div>

        <!-- Container for dynamically added & edit-mode Additional Addresses -->
        <div class="client-address-container" id="additional_addresses_container">
          @if (!empty($additional_addresses))
            @foreach ($additional_addresses as $addIdx => $addrItem)
              <div class="card ij-location-card client-address-card is-additional client-address-container-child mt-3" id="clientAddr_{{ $addrItem['id'] }}">
                <div class="card-body p-0">
                  <!-- Header with yellow gradient banner matching default Address Details -->
                  <div class="ij-location-card-head">
                    <div class="ij-location-head-left">
                      <span class="ij-location-head-badge">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                          <path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/>
                          <circle cx="12" cy="10" r="3"/>
                        </svg>
                      </span>
                      <div>
                        <h4 class="ij-location-title">Additional Address ({{ $addIdx + 1 }})</h4>
                        <p class="ij-location-subtitle">Branch or regional office location and street address</p>
                      </div>
                    </div>
                    <button type="button" class="client-remove-address-btn remove_address_single" data-target="clientAddr_{{ $addrItem['id'] }}" onclick="removeAddressCard(this, 'clientAddr_{{ $addrItem['id'] }}')">
                      <i class="la la-trash"></i> Remove
                    </button>
                  </div>

                  <div class="ij-location-card-body">
                    <!-- Branch City Selection with same design as default Address Details -->
                    <div class="client-field fv-plugins-icon-container mb-3">
                      <label class="form-label required" for="branch_city_search_{{ $addrItem['id'] }}">Branch City</label>
                      <div class="input-wrapper ij-location-input-wrap">
                        <div class="input-with-icon">
                          <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="11" cy="11" r="8"/>
                            <path d="m21 21-4.3-4.3"/>
                          </svg>
                          <input type="text" class="form-input ij-location-search-input branch-city-search" id="branch_city_search_{{ $addrItem['id'] }}" data-cid="{{ $addrItem['id'] }}" value="{{ $addrItem['city'] }}" placeholder="Search branch city (e.g. Mumbai, Pune, London)..." autocomplete="off" spellcheck="false">
                        </div>
                        <span class="ij-search-spinner" id="branch_city_spinner_{{ $addrItem['id'] }}" style="display:none;" title="Searching..."></span>
                        <button type="button" class="ij-search-clear branch-city-clear" id="branch_city_clear_{{ $addrItem['id'] }}" style="{{ !empty($addrItem['city']) ? 'display:inline-flex;' : 'display:none;' }}" data-cid="{{ $addrItem['id'] }}" title="Clear city">&times;</button>
                        <div class="search-dropdown ij-city-dropdown branch-city-dropdown" id="branch_city_dropdown_{{ $addrItem['id'] }}"></div>
                        <input type="hidden" name="kt_tagify_work_location[]" id="kt_tagify_work_location_{{ $addrItem['id'] }}" value='{{ $addrItem['tagify_json'] }}'>
                      </div>
                      <span class="form-text text-muted">Select or search for the city where this branch office is located.</span>
                    </div>

                    <!-- Full Street Address Details -->
                    <div class="client-field fv-plugins-icon-container">
                      <label class="form-label required" for="address_{{ $addrItem['id'] }}">Full Address Details</label>
                      <textarea class="form-control form-control-solid client-address-textarea" name="address[]" rows="3" maxlength="500" placeholder="Building, street, suite/floor, area, landmark, pincode, etc." id="address_{{ $addrItem['id'] }}">{{ $addrItem['address'] }}</textarea>
                      <div class="d-flex justify-content-between mt-1">
                        <span class="form-text text-muted">Provide the complete and accurate branch office address.</span>
                        <span class="client-char-count"><span class="client-address-count" id="address_char_count_{{ $addrItem['id'] }}">0</span>/500</span>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            @endforeach
          @endif
        </div>

        <div class="client-address-empty p-4 text-center border border-dashed rounded bg-light mt-3" id="client_additional_empty" style="{{ !empty($additional_addresses) ? 'display:none;' : '' }}">
          <i class="la la-building fs-1 text-muted" aria-hidden="true"></i>
          <p class="text-muted mt-2 mb-0 fs-7">No additional physical branch address added yet. Click <strong>"Add Another Address"</strong> above if this client has multiple offices.</p>
        </div>
      </section>

      <input type="hidden" name="addressId" id="addressId" value="{{ $addressId }}" />
    </div>

    <!-- SIDEBAR HELPER TIPS -->
    <aside class="client-side-column">
      <div class="client-helper-card client-helper-sky">
        <h4><i class="la la-map-marked-alt" aria-hidden="true"></i> About Work Locations</h4>
        <p>Select all cities where this client conducts operations or hires talent. These locations power job requisitions and candidate search matching.</p>
      </div>
      <div class="client-helper-card client-helper-green">
        <h4><i class="la la-lightbulb-o" aria-hidden="true"></i> Tips for Accurate Address</h4>
        <ul>
          <li><i class="la la-check-circle" aria-hidden="true"></i><span>Use quick-select pills or type to search global cities.</span></li>
          <li><i class="la la-check-circle" aria-hidden="true"></i><span>Headquarters street address will appear on contracts.</span></li>
          <li><i class="la la-check-circle" aria-hidden="true"></i><span>Add secondary branch addresses with their respective city.</span></li>
          <li><i class="la la-check-circle" aria-hidden="true"></i><span>You can select up to 15 cities per client.</span></li>
        </ul>
      </div>
    </aside>
  </div>
</div>

<script>
  window.serverInitialCities = {!! json_encode($initialCities) !!};
</script>
<!--end::Address Details-->
