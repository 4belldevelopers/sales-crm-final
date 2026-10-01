{{-- 
  JERO Enterprise Mobile Number Box Blade Component (V7.0 Pure In-Place Architecture)
  100% Self-Contained | In-Place Scoped Dropdown | Real SVG Flags | Zero 404 Dependency
  Metronic 15.6" Screen Fit | WCAG 2.1 AA | Complete Page Isolation (Rule 17)
--}}
@props([
    'id' => 'phone',
    'name' => 'phone',
    'countryCodeName' => 'primary_country_code',
    'countryCodeId' => null,
    'value' => '',
    'countryCode' => '',
    'label' => 'Primary Contact Number',
    'required' => false,
    'placeholder' => 'Enter mobile number',
    'class' => '',
])

@php
    $resolvedCountryCodeId = $countryCodeId ?: ($id . '_country_code');
    $tenantHqIso = strtoupper(auth()->check() && auth()->user()->company ? (auth()->user()->company->headquarter_country_code ?? 'IN') : 'IN');

    // Comprehensive ISO-2 to Dial Code mapping
    $isoToDial = [
        'IN' => '+91', 'US' => '+1', 'GB' => '+44', 'AE' => '+971', 'CA' => '+1',
        'AU' => '+61', 'SG' => '+65', 'SA' => '+966', 'DE' => '+49', 'FR' => '+33',
        'QA' => '+974', 'KW' => '+965', 'OM' => '+968', 'BH' => '+973', 'MY' => '+60',
        'PH' => '+63', 'ID' => '+62', 'NZ' => '+64', 'ZA' => '+27', 'IE' => '+353',
        'NL' => '+31', 'SE' => '+46', 'CH' => '+41', 'ES' => '+34', 'IT' => '+39',
        'BR' => '+55', 'MX' => '+52', 'JP' => '+81', 'KR' => '+82', 'PK' => '+92',
        'BD' => '+880', 'LK' => '+94', 'NP' => '+977', 'NG' => '+234', 'KE' => '+254',
        'EG' => '+20', 'TR' => '+90', 'PL' => '+48', 'RO' => '+40', 'UA' => '+380',
        'VN' => '+84', 'TH' => '+66', 'HK' => '+852', 'TW' => '+886', 'IL' => '+972',
        'RU' => '+7', 'MV' => '+960', 'YE' => '+967', 'SY' => '+963', 'IR' => '+98'
    ];

    $dialToIso = [
        '+91' => 'IN', '+1' => 'US', '+44' => 'GB', '+971' => 'AE',
        '+61' => 'AU', '+65' => 'SG', '+966' => 'SA', '+49' => 'DE',
        '+33' => 'FR', '+974' => 'QA', '+965' => 'KW', '+968' => 'OM',
        '+973' => 'BH', '+60' => 'MY', '+63' => 'PH', '+62' => 'ID',
        '+64' => 'NZ', '+27' => 'ZA', '+353' => 'IE', '+31' => 'NL',
        '+46' => 'SE', '+41' => 'CH', '+34' => 'ES', '+39' => 'IT',
        '+55' => 'BR', '+52' => 'MX', '+81' => 'JP', '+82' => 'KR',
        '+92' => 'PK', '+880' => 'BD', '+94' => 'LK', '+977' => 'NP',
        '+234' => 'NG', '+254' => 'KE', '+20' => 'EG', '+90' => 'TR',
        '+48' => 'PL', '+40' => 'RO', '+380' => 'UA', '+84' => 'VN',
        '+66' => 'TH', '+852' => 'HK', '+886' => 'TW', '+972' => 'IL',
        '+7' => 'RU', '+960' => 'MV', '+967' => 'YE', '+963' => 'SY', '+98' => 'IR'
    ];

    $dialToLen = [
        '+91' => 10, '+1' => 10, '+44' => 10, '+971' => 9,
        '+61' => 9, '+65' => 8, '+966' => 9, '+49' => 11,
        '+33' => 9, '+974' => 8, '+965' => 8, '+968' => 8,
        '+973' => 8, '+60' => 10, '+63' => 10, '+62' => 11,
        '+64' => 9, '+27' => 9, '+353' => 9, '+31' => 9,
        '+46' => 9, '+41' => 9, '+34' => 9, '+39' => 10,
        '+55' => 11, '+52' => 10, '+81' => 10, '+82' => 10,
        '+92' => 10, '+880' => 10, '+94' => 9, '+977' => 10,
        '+234' => 10, '+254' => 9, '+20' => 10, '+90' => 10,
        '+48' => 9, '+40' => 9, '+380' => 9, '+84' => 9,
        '+66' => 9, '+852' => 8, '+886' => 9, '+972' => 9,
        '+7' => 10, '+960' => 7, '+967' => 9, '+963' => 9, '+98' => 10
    ];

    $aliasToIso = [
        'UAE' => 'AE', 'ARE' => 'AE', 'DUBAI' => 'AE',
        'USA' => 'US', 'AMERICA' => 'US',
        'UK' => 'GB', 'GBR' => 'GB', 'ENGLAND' => 'GB', 'BRITAIN' => 'GB',
        'KSA' => 'SA', 'SAU' => 'SA',
        'IND' => 'IN', 'BHARAT' => 'IN',
        'AUS' => 'AU', 'CAN' => 'CA', 'SGP' => 'SG', 'DEU' => 'DE', 'FRA' => 'FR',
        'NZL' => 'NZ', 'ZAF' => 'ZA', 'IRL' => 'IE', 'NLD' => 'NL', 'SWE' => 'SE',
        'CHE' => 'CH', 'JPN' => 'JP', 'KOR' => 'KR', 'CHN' => 'CN', 'HKG' => 'HK',
        'TWN' => 'TW', 'THA' => 'TH', 'VNM' => 'VN', 'ESP' => 'ES', 'ITA' => 'IT',
        'PRT' => 'PT', 'BEL' => 'BE', 'AUT' => 'AT', 'NOR' => 'NO', 'DNK' => 'DK',
        'FIN' => 'FI', 'POL' => 'PL', 'CZE' => 'CZ', 'ROU' => 'RO', 'HUN' => 'HU',
        'GRC' => 'GR', 'TUR' => 'TR', 'ISR' => 'IL', 'EGY' => 'EG', 'NGA' => 'NG',
        'KEN' => 'KE', 'GHA' => 'GH', 'PAK' => 'PK', 'BGD' => 'BD', 'LKA' => 'LK',
        'NPL' => 'NP', 'MEX' => 'MX', 'BRA' => 'BR', 'ARG' => 'AR', 'CHL' => 'CL',
        'COL' => 'CO', 'PER' => 'PER', 'RUS' => 'RU', 'QAT' => 'QA', 'KWT' => 'KW',
        'OMN' => 'OM', 'BHR' => 'BH', 'MYS' => 'MY', 'PHL' => 'PH', 'IDN' => 'ID'
    ];

    $rawCode = trim((string) old($countryCodeName, $countryCode));
    $upperRaw = strtoupper($rawCode);
    if (isset($aliasToIso[$upperRaw])) {
        $upperRaw = $aliasToIso[$upperRaw];
    }

    if (isset($isoToDial[$upperRaw])) {
        $initialCode = $isoToDial[$upperRaw];
        $initialIso = $upperRaw;
    } elseif ($rawCode && preg_match('/^\+?\d+$/', $rawCode)) {
        $initialCode = str_starts_with($rawCode, '+') ? $rawCode : ('+' . $rawCode);
        $initialIso = $dialToIso[$initialCode] ?? ($tenantHqIso ?: 'IN');
    } else {
        $initialCode = $isoToDial[$tenantHqIso] ?? '+91';
        $initialIso = $tenantHqIso ?: 'IN';
    }

    $initialLength = $dialToLen[$initialCode] ?? 10;
    $initialValue = old($name, $value);
    $initialDigitsCount = strlen(preg_replace('/[^\d]/', '', (string)$initialValue));
@endphp

<div class="jero-phone-box-wrapper w-100 {{ $class }}" 
     id="{{ $id }}_wrapper"
     data-field-id="{{ $id }}"
     data-default-iso="{{ $initialIso }}"
     style="position: relative;">
    
    @if($label)
        <label for="{{ $id }}" class="form-label fs-7 fw-semibold text-gray-700 mb-1 {{ $required ? 'required' : '' }}">
            {{ $label }}
        </label>
    @endif

    <div class="jero-phone-input-group position-relative d-flex align-items-center"
         style="position: relative !important; display: flex !important; align-items: center !important; width: 100% !important; max-width: 100% !important; height: 38px !important; min-height: 38px !important; max-height: 38px !important; background: #ffffff !important; background-color: #ffffff !important; border: 1px solid #d8deec !important; border-radius: 6px !important; box-sizing: border-box !important; overflow: hidden !important;">
        <!-- Hidden input for Country Code (e.g. +91) submitted with form -->
        <input type="hidden" 
               name="{{ $countryCodeName }}" 
               id="{{ $resolvedCountryCodeId }}" 
               class="jero-phone-country-code-input"
               value="{{ $initialCode }}">

        <!-- Country Selector Trigger Button -->
        <button type="button" 
                class="jero-country-picker-btn"
                id="{{ $id }}_btn"
                aria-haspopup="listbox"
                aria-expanded="false"
                title="Select country code"
                style="display: inline-flex !important; align-items: center !important; justify-content: center !important; height: 100% !important; min-height: 36px !important; max-height: 36px !important; background: transparent !important; background-color: transparent !important; border: 0 none transparent !important; border-radius: 5px 0 0 5px !important; padding: 0 10px 0 12px !important; margin: 0 !important; box-shadow: none !important; outline: none !important; flex-shrink: 0 !important;">
            <span class="jero-selected-flag" id="{{ $id }}_flag" data-iso="{{ $initialIso }}"></span>
            <span class="jero-selected-dial" id="{{ $id }}_dial">{{ $initialCode }}</span>
            <i class="fa fa-chevron-down jero-chevron"></i>
        </button>

        <!-- Hairline Subtle Divider -->
        <div class="jero-phone-divider"></div>

        <!-- Main Phone Digits Input -->
        <input type="tel" 
               name="{{ $name }}" 
               id="{{ $id }}" 
               value="{{ $initialValue }}"
               placeholder="{{ $placeholder }}"
               class="jero-phone-main-input"
               maxlength="{{ $initialLength }}"
               autocomplete="tel-national"
               inputmode="numeric"
               {{ $required ? 'required' : '' }}
               style="flex: 1 1 auto !important; min-width: 0 !important; width: 100% !important; height: 100% !important; min-height: 0 !important; max-height: 100% !important; background: transparent !important; background-color: transparent !important; border: 0 none transparent !important; border-width: 0 !important; border-style: none !important; border-color: transparent !important; box-shadow: none !important; outline: none !important; padding: 0 54px 0 10px !important; margin: 0 !important; box-sizing: border-box !important; color: #181c32 !important; font-size: 13px !important; font-weight: 500 !important; font-family: 'Poppins', Helvetica, sans-serif !important;">

        <!-- Real-Time Visual Digit Counter Badge -->
        <span class="badge {{ $initialDigitsCount === $initialLength && $initialLength > 0 ? 'bg-light-success text-success' : 'bg-light text-muted' }} jero-digit-counter"
              id="{{ $id }}_counter">
            {{ $initialDigitsCount }}/{{ $initialLength }}
        </span>
    </div>

    <!-- Dedicated Validation Error Feedback Container (Outside the 38px group to prevent flex compression & badge overlap) -->
    <div class="fv-plugins-message-container invalid-feedback jero-phone-error-msg" id="{{ $id }}_error" style="display: none;"></div>

    <!-- IN-PLACE POPUP DROPDOWN (Direct Child of Wrapper, Scoped & 100% Collision Safe) -->
    <div class="jero-country-dropdown card shadow-lg border"
         id="{{ $id }}_dropdown"
         style="display: none; position: absolute; top: calc(100% + 4px); left: 0; z-index: 1060; width: 340px; max-height: 360px; overflow: hidden; border-radius: 0.625rem; box-shadow: 0 10px 30px rgba(0,0,0,0.18); background: #ffffff; border: 1px solid #dce3ee;">

        <!-- Search Header -->
        <div class="p-2 border-bottom bg-light d-flex align-items-center position-relative" style="border-top-left-radius: 0.625rem; border-top-right-radius: 0.625rem;">
            <i class="fa fa-search text-gray-500 ms-2 me-2 fs-7" aria-hidden="true"></i>
            <input type="text"
                   class="form-control form-control-sm form-control-solid fs-7 border-0 bg-white jero-search-input pe-6"
                   placeholder="Search country, code, or alias (e.g. UAE, USA, +91)..."
                   id="{{ $id }}_search"
                   autocomplete="off"
                   spellcheck="false"
                   style="outline: none; box-shadow: none;">
            <span class="jero-search-clear text-gray-500 cursor-pointer position-absolute end-0 me-3 fs-7"
                  style="display: none; line-height: 1; user-select: none;"
                  title="Clear search">
                <i class="fa fa-times-circle"></i>
            </span>
        </div>

        <!-- Country List -->
        <div class="jero-country-list py-1"
             id="{{ $id }}_list"
             style="max-height: 290px; overflow-y: auto; scrollbar-width: thin;"
             role="listbox">
        </div>
    </div>

    <!-- Client-side field feedback / hint -->
    <div class="jero-phone-feedback fs-8 text-muted mt-1" id="{{ $id }}_hint"></div>
</div>

@once
<style id="jero_phone_box_master_styles">
.jero-phone-box-wrapper {
  position: relative;
  font-family: 'Poppins', Helvetica, sans-serif;
  width: 100%;
}

/* Enterprise unified pure white container */
.jero-phone-input-group,
.jero-phone-box-wrapper .jero-phone-input-group,
.candidate-card .jero-phone-box-wrapper .jero-phone-input-group,
.job-candidate-create .jero-phone-input-group,
.candidate-profile-grid .jero-phone-input-group,
.candidate-secondary-grid .jero-phone-input-group {
  position: relative !important;
  display: flex !important;
  align-items: center !important;
  width: 100% !important;
  max-width: 100% !important;
  height: 38px !important;
  min-height: 38px !important;
  max-height: 38px !important;
  background: #ffffff !important;
  background-color: #ffffff !important;
  border: 1px solid #d8deec !important;
  border-radius: 6px !important;
  box-sizing: border-box !important;
  transition: border-color 0.2s ease, box-shadow 0.2s ease;
  overflow: hidden !important;
}

.jero-phone-input-group:hover:not(:focus-within),
.jero-phone-box-wrapper .jero-phone-input-group:hover:not(:focus-within),
.candidate-card .jero-phone-box-wrapper .jero-phone-input-group:hover:not(:focus-within) {
  border-color: #b5b5c3 !important;
}

.jero-phone-input-group:focus-within,
.jero-phone-box-wrapper .jero-phone-input-group:focus-within,
.candidate-card .jero-phone-box-wrapper .jero-phone-input-group:focus-within,
.job-candidate-create .jero-phone-input-group:focus-within {
  border-color: #009ef7 !important;
  box-shadow: 0 0 0 3px rgba(0, 158, 247, 0.12) !important;
}

.jero-phone-input-group.is-invalid,
.jero-phone-box-wrapper .jero-phone-input-group.is-invalid,
.jero-phone-box-wrapper.is-invalid .jero-phone-input-group,
.jero-phone-box-wrapper:has(.is-invalid) .jero-phone-input-group,
.jero-phone-box-wrapper .jero-phone-input-group:has(.is-invalid) {
  border-color: #f1416c !important;
  box-shadow: 0 0 0 3px rgba(241, 65, 108, 0.12) !important;
}

/* Country picker button - centered vertically and horizontally */
.jero-country-picker-btn,
.jero-phone-box-wrapper .jero-phone-input-group .jero-country-picker-btn,
.candidate-card .jero-phone-box-wrapper .jero-country-picker-btn {
  display: inline-flex !important;
  align-items: center !important;
  justify-content: center !important;
  height: 100% !important;
  min-height: 36px !important;
  max-height: 36px !important;
  background: transparent !important;
  background-color: transparent !important;
  border: 0 none transparent !important;
  border-width: 0 !important;
  border-style: none !important;
  border-color: transparent !important;
  padding: 0 10px 0 12px !important;
  margin: 0 !important;
  cursor: pointer !important;
  user-select: none !important;
  outline: none !important;
  box-shadow: none !important;
  gap: 6px !important;
  flex-shrink: 0 !important;
  line-height: 1 !important;
  border-top-left-radius: 5px !important;
  border-bottom-left-radius: 5px !important;
  border-top-right-radius: 0 !important;
  border-bottom-right-radius: 0 !important;
  transition: background-color 0.15s ease !important;
}

.jero-country-picker-btn:hover {
  background-color: #f8fafc !important;
}

.jero-selected-flag {
  display: inline-flex !important;
  align-items: center !important;
  justify-content: center !important;
  width: 20px !important;
  min-width: 20px !important;
  max-width: 20px !important;
  height: 15px !important;
  min-height: 15px !important;
  max-height: 15px !important;
  flex-shrink: 0 !important;
  line-height: 1 !important;
  overflow: visible !important;
}

.jero-selected-flag svg {
  width: 20px !important;
  height: 15px !important;
  min-width: 20px !important;
  min-height: 15px !important;
  max-width: 20px !important;
  max-height: 15px !important;
  display: block !important;
  vertical-align: middle !important;
  border-radius: 2px !important;
  box-shadow: 0 0 1px rgba(0,0,0,0.35) !important;
  flex-shrink: 0 !important;
}

.jero-selected-flag img {
  width: 20px !important;
  height: 14px !important;
  object-fit: cover !important;
  border-radius: 2px !important;
  box-shadow: 0 0 1px rgba(0,0,0,0.35) !important;
  display: block !important;
  vertical-align: middle !important;
}

.jero-selected-dial {
  display: inline-flex !important;
  align-items: center !important;
  justify-content: center !important;
  font-size: 13px !important;
  font-weight: 600 !important;
  color: #181c32 !important;
  line-height: 1 !important;
  letter-spacing: 0.2px !important;
}

.jero-country-picker-btn .jero-chevron {
  display: inline-flex !important;
  align-items: center !important;
  justify-content: center !important;
  font-size: 9px !important;
  color: #7e8299 !important;
  line-height: 1 !important;
  transition: transform 0.2s ease !important;
}

.jero-country-picker-btn[aria-expanded="true"] .jero-chevron {
  transform: rotate(180deg) !important;
}

/* Hairline divider between country selector and input */
.jero-phone-divider {
  width: 1px !important;
  height: 20px !important;
  background-color: #e4e6ef !important;
  flex-shrink: 0 !important;
  margin: 0 2px !important;
}

/* Phone digits input - completely impervious to external card/input styles */
.jero-phone-main-input,
.jero-phone-box-wrapper .jero-phone-input-group input.jero-phone-main-input,
.candidate-card .jero-phone-box-wrapper input.jero-phone-main-input,
.job-candidate-create .candidate-card .jero-phone-input-group input,
.job-candidate-create .bd-card .jero-phone-input-group #phone,
.job-candidate-create .bd-card .jero-phone-input-group #secondary_mobile,
.candidate-profile-grid .jero-phone-input-group input,
.candidate-secondary-grid .jero-phone-input-group input,
#phone.jero-phone-main-input,
#secondary_mobile.jero-phone-main-input {
  flex: 1 1 auto !important;
  min-width: 0 !important;
  width: 100% !important;
  height: 100% !important;
  min-height: 0 !important;
  max-height: 100% !important;
  border: 0 none transparent !important;
  border-width: 0 !important;
  border-style: none !important;
  border-color: transparent !important;
  outline: none !important;
  box-shadow: none !important;
  background: transparent !important;
  background-color: transparent !important;
  color: #181c32 !important;
  font-size: 13px !important;
  font-weight: 500 !important;
  font-family: 'Poppins', Helvetica, sans-serif !important;
  padding: 0 54px 0 10px !important;
  margin: 0 !important;
  letter-spacing: 0.3px !important;
  border-radius: 0 5px 5px 0 !important;
  box-sizing: border-box !important;
}

.jero-phone-main-input::placeholder {
  color: #8A94B4 !important;
  font-size: 13px !important;
  font-weight: 500 !important;
  opacity: 1 !important;
}

.jero-phone-main-input:focus,
.jero-phone-box-wrapper .jero-phone-input-group input.jero-phone-main-input:focus,
.candidate-card .jero-phone-box-wrapper input.jero-phone-main-input:focus,
.job-candidate-create .candidate-card .jero-phone-input-group input:focus,
#phone.jero-phone-main-input:focus,
#secondary_mobile.jero-phone-main-input:focus {
  border: 0 none transparent !important;
  border-color: transparent !important;
  box-shadow: none !important;
  outline: none !important;
  background: transparent !important;
  background-color: transparent !important;
}

/* Neutralize Bootstrap / FormValidation invalid state on input (suppress red exclamation icon, maintain padding) */
.jero-phone-main-input.is-invalid,
.jero-phone-main-input:invalid,
.jero-phone-input-group input.is-invalid,
.jero-phone-input-group input:invalid {
  border: 0 none transparent !important;
  box-shadow: none !important;
  background-image: none !important;
  background-repeat: no-repeat !important;
  padding: 0 54px 0 10px !important;
}

/* Hide any rogue validation message container or plugin icon injected inside the 38px flex group */
.jero-phone-input-group > .fv-plugins-message-container,
.jero-phone-input-group > .invalid-feedback,
.jero-phone-input-group > label.error,
.jero-phone-input-group .fv-plugins-icon,
.jero-phone-input-group i.fv-plugins-icon {
  display: none !important;
  visibility: hidden !important;
  width: 0 !important;
  height: 0 !important;
  overflow: hidden !important;
  position: absolute !important;
  pointer-events: none !important;
  margin: 0 !important;
  padding: 0 !important;
}

/* Dedicated validation error message outside the 38px input group */
.jero-phone-box-wrapper .invalid-feedback.jero-phone-error-msg,
.jero-phone-box-wrapper > .fv-plugins-message-container {
  display: block;
  font-size: 11px !important;
  font-weight: 500 !important;
  color: #f1416c !important;
  margin-top: 4px !important;
  padding-left: 2px !important;
  line-height: 1.3 !important;
  width: 100% !important;
  clear: both !important;
}

.jero-phone-box-wrapper .invalid-feedback.jero-phone-error-msg:empty,
.jero-phone-box-wrapper > .fv-plugins-message-container:empty {
  display: none !important;
}

/* Real-time digit counter badge */
.jero-digit-counter {
  position: absolute !important;
  right: 10px !important;
  top: 50% !important;
  transform: translateY(-50%) !important;
  font-size: 11px !important;
  font-weight: 600 !important;
  padding: 2px 7px !important;
  letter-spacing: 0.2px !important;
  pointer-events: none !important;
  border-radius: 4px !important;
  z-index: 4 !important;
  line-height: 1.2 !important;
}

/* Searchable dropdown styling */
.jero-country-dropdown {
  position: absolute;
  top: calc(100% + 4px);
  left: 0;
  z-index: 1060;
  width: 340px;
  max-height: 360px;
  overflow: hidden;
  border-radius: 8px !important;
  box-shadow: 0 10px 30px rgba(0,0,0,0.15) !important;
  background: #ffffff !important;
  border: 1px solid #d8deec !important;
}

.jero-country-item:hover {
  background-color: #f1faff !important;
  color: #009ef7 !important;
}

.jero-country-list::-webkit-scrollbar {
  width: 6px;
}
.jero-country-list::-webkit-scrollbar-track {
  background: #f1f1f4;
}
.jero-country-list::-webkit-scrollbar-thumb {
  background: #c4c4d0;
  border-radius: 3px;
}
</style>

<script id="jero_phone_box_master_script">
(function() {
  window.__JERO_COUNTRIES_DB__ = [
    { iso: "IN", name: "India", dial: "+91", len: 10 },
    { iso: "US", name: "United States", dial: "+1", len: 10 },
    { iso: "GB", name: "United Kingdom", dial: "+44", len: 10 },
    { iso: "AE", name: "United Arab Emirates", dial: "+971", len: 9 },
    { iso: "CA", name: "Canada", dial: "+1", len: 10 },
    { iso: "AU", name: "Australia", dial: "+61", len: 9 },
    { iso: "SG", name: "Singapore", dial: "+65", len: 8 },
    { iso: "SA", name: "Saudi Arabia", dial: "+966", len: 9 },
    { iso: "DE", name: "Germany", dial: "+49", len: 11 },
    { iso: "FR", name: "France", dial: "+33", len: 9 },
    { iso: "QA", name: "Qatar", dial: "+974", len: 8 },
    { iso: "KW", name: "Kuwait", dial: "+965", len: 8 },
    { iso: "OM", name: "Oman", dial: "+968", len: 8 },
    { iso: "BH", name: "Bahrain", dial: "+973", len: 8 },
    { iso: "MY", name: "Malaysia", dial: "+60", len: 10 },
    { iso: "PH", name: "Philippines", dial: "+63", len: 10 },
    { iso: "ID", name: "Indonesia", dial: "+62", len: 11 },
    { iso: "NZ", name: "New Zealand", dial: "+64", len: 9 },
    { iso: "ZA", name: "South Africa", dial: "+27", len: 9 },
    { iso: "IE", name: "Ireland", dial: "+353", len: 9 },
    { iso: "NL", name: "Netherlands", dial: "+31", len: 9 },
    { iso: "SE", name: "Sweden", dial: "+46", len: 9 },
    { iso: "CH", name: "Switzerland", dial: "+41", len: 9 },
    { iso: "JP", name: "Japan", dial: "+81", len: 10 },
    { iso: "KR", name: "South Korea", dial: "+82", len: 10 },
    { iso: "CN", name: "China", dial: "+86", len: 11 },
    { iso: "HK", name: "Hong Kong", dial: "+852", len: 8 },
    { iso: "TW", name: "Taiwan", dial: "+886", len: 9 },
    { iso: "TH", name: "Thailand", dial: "+66", len: 9 },
    { iso: "VN", name: "Vietnam", dial: "+84", len: 9 },
    { iso: "ES", name: "Spain", dial: "+34", len: 9 },
    { iso: "IT", name: "Italy", dial: "+39", len: 10 },
    { iso: "PT", name: "Portugal", dial: "+351", len: 9 },
    { iso: "BE", name: "Belgium", dial: "+32", len: 9 },
    { iso: "AT", name: "Austria", dial: "+43", len: 10 },
    { iso: "NO", name: "Norway", dial: "+47", len: 8 },
    { iso: "DK", name: "Denmark", dial: "+45", len: 8 },
    { iso: "FI", name: "Finland", dial: "+358", len: 9 },
    { iso: "PL", name: "Poland", dial: "+48", len: 9 },
    { iso: "CZ", name: "Czech Republic", dial: "+420", len: 9 },
    { iso: "RO", name: "Romania", dial: "+40", len: 9 },
    { iso: "HU", name: "Hungary", dial: "+36", len: 9 },
    { iso: "GR", name: "Greece", dial: "+30", len: 10 },
    { iso: "TR", name: "Turkey", dial: "+90", len: 10 },
    { iso: "IL", name: "Israel", dial: "+972", len: 9 },
    { iso: "EG", name: "Egypt", dial: "+20", len: 10 },
    { iso: "NG", name: "Nigeria", dial: "+234", len: 10 },
    { iso: "KE", name: "Kenya", dial: "+254", len: 9 },
    { iso: "GH", name: "Ghana", dial: "+233", len: 9 },
    { iso: "PK", name: "Pakistan", dial: "+92", len: 10 },
    { iso: "BD", name: "Bangladesh", dial: "+880", len: 10 },
    { iso: "LK", name: "Sri Lanka", dial: "+94", len: 9 },
    { iso: "NP", name: "Nepal", dial: "+977", len: 10 },
    { iso: "MX", name: "Mexico", dial: "+52", len: 10 },
    { iso: "BR", name: "Brazil", dial: "+55", len: 11 },
    { iso: "AR", name: "Argentina", dial: "+54", len: 10 },
    { iso: "CL", name: "Chile", dial: "+56", len: 9 },
    { iso: "CO", name: "Colombia", dial: "+57", len: 10 },
    { iso: "PE", name: "Peru", dial: "+51", len: 9 },
    { iso: "AF", name: "Afghanistan", dial: "+93", len: 9 },
    { iso: "AL", name: "Albania", dial: "+355", len: 9 },
    { iso: "DZ", name: "Algeria", dial: "+213", len: 9 },
    { iso: "AD", name: "Andorra", dial: "+376", len: 6 },
    { iso: "AO", name: "Angola", dial: "+244", len: 9 },
    { iso: "AM", name: "Armenia", dial: "+374", len: 8 },
    { iso: "AZ", name: "Azerbaijan", dial: "+994", len: 9 },
    { iso: "BS", name: "Bahamas", dial: "+1242", len: 7 },
    { iso: "BB", name: "Barbados", dial: "+1246", len: 7 },
    { iso: "BY", name: "Belarus", dial: "+375", len: 9 },
    { iso: "BZ", name: "Belize", dial: "+501", len: 7 },
    { iso: "BJ", name: "Benin", dial: "+229", len: 8 },
    { iso: "BM", name: "Bermuda", dial: "+1441", len: 7 },
    { iso: "BT", name: "Bhutan", dial: "+975", len: 8 },
    { iso: "BO", name: "Bolivia", dial: "+591", len: 8 },
    { iso: "BA", name: "Bosnia and Herzegovina", dial: "+387", len: 8 },
    { iso: "BW", name: "Botswana", dial: "+267", len: 8 },
    { iso: "BN", name: "Brunei", dial: "+673", len: 7 },
    { iso: "BG", name: "Bulgaria", dial: "+359", len: 9 },
    { iso: "BF", name: "Burkina Faso", dial: "+226", len: 8 },
    { iso: "KH", name: "Cambodia", dial: "+855", len: 9 },
    { iso: "CM", name: "Cameroon", dial: "+237", len: 9 },
    { iso: "CR", name: "Costa Rica", dial: "+506", len: 8 },
    { iso: "HR", name: "Croatia", dial: "+385", len: 9 },
    { iso: "CU", name: "Cuba", dial: "+53", len: 8 },
    { iso: "CY", name: "Cyprus", dial: "+357", len: 8 },
    { iso: "EC", name: "Ecuador", dial: "+593", len: 9 },
    { iso: "EE", name: "Estonia", dial: "+372", len: 8 },
    { iso: "ET", name: "Ethiopia", dial: "+251", len: 9 },
    { iso: "FJ", name: "Fiji", dial: "+679", len: 7 },
    { iso: "GE", name: "Georgia", dial: "+995", len: 9 },
    { iso: "GI", name: "Gibraltar", dial: "+350", len: 8 },
    { iso: "GT", name: "Guatemala", dial: "+502", len: 8 },
    { iso: "IS", name: "Iceland", dial: "+354", len: 7 },
    { iso: "IQ", name: "Iraq", dial: "+964", len: 10 },
    { iso: "JM", name: "Jamaica", dial: "+1876", len: 7 },
    { iso: "JO", name: "Jordan", dial: "+962", len: 9 },
    { iso: "KZ", name: "Kazakhstan", dial: "+7", len: 10 },
    { iso: "KG", name: "Kyrgyzstan", dial: "+996", len: 9 },
    { iso: "LA", name: "Laos", dial: "+856", len: 10 },
    { iso: "LV", name: "Latvia", dial: "+371", len: 8 },
    { iso: "LB", name: "Lebanon", dial: "+961", len: 8 },
    { iso: "LT", name: "Lithuania", dial: "+370", len: 8 },
    { iso: "LU", name: "Luxembourg", dial: "+352", len: 9 },
    { iso: "MT", name: "Malta", dial: "+356", len: 8 },
    { iso: "MU", name: "Mauritius", dial: "+230", len: 8 },
    { iso: "MD", name: "Moldova", dial: "+373", len: 8 },
    { iso: "MC", name: "Monaco", dial: "+377", len: 8 },
    { iso: "MA", name: "Morocco", dial: "+212", len: 9 },
    { iso: "MM", name: "Myanmar", dial: "+95", len: 9 },
    { iso: "PA", name: "Panama", dial: "+507", len: 8 },
    { iso: "RS", name: "Serbia", dial: "+381", len: 9 },
    { iso: "SK", name: "Slovakia", dial: "+421", len: 9 },
    { iso: "SI", name: "Slovenia", dial: "+386", len: 8 },
    { iso: "TZ", name: "Tanzania", dial: "+255", len: 9 },
    { iso: "UG", name: "Uganda", dial: "+256", len: 9 },
    { iso: "UA", name: "Ukraine", dial: "+380", len: 9 },
    { iso: "UY", name: "Uruguay", dial: "+598", len: 8 },
    { iso: "UZ", name: "Uzbekistan", dial: "+998", len: 9 },
    { iso: "RU", name: "Russia", dial: "+7", len: 10 },
    { iso: "MV", name: "Maldives", dial: "+960", len: 7 },
    { iso: "YE", name: "Yemen", dial: "+967", len: 9 },
    { iso: "SY", name: "Syria", dial: "+963", len: 9 },
    { iso: "IR", name: "Iran", dial: "+98", len: 10 },
    { iso: "ZW", name: "Zimbabwe", dial: "+263", len: 9 },
    { iso: "ZM", name: "Zambia", dial: "+260", len: 9 },
    { iso: "RW", name: "Rwanda", dial: "+250", len: 9 },
    { iso: "SN", name: "Senegal", dial: "+221", len: 9 },
    { iso: "CI", name: "Ivory Coast", dial: "+225", len: 10 },
    { iso: "TT", name: "Trinidad and Tobago", dial: "+1868", len: 7 },
    { iso: "DO", name: "Dominican Republic", dial: "+1809", len: 7 },
    { iso: "PR", name: "Puerto Rico", dial: "+1787", len: 7 }
  ];

  window.__JERO_FLAGS_SVG__ = {
    'IN': '<svg viewBox="0 0 640 480" width="20" height="15" class="rounded-1 shadow-xs" style="display:inline-block; vertical-align:middle; border-radius:2px; flex-shrink:0;"><rect width="640" height="160" fill="#FF9933"/><rect y="160" width="640" height="160" fill="#FFFFFF"/><rect y="320" width="640" height="160" fill="#138808"/><circle cx="320" cy="240" r="45" fill="none" stroke="#000080" stroke-width="4"/><circle cx="320" cy="240" r="10" fill="#000080"/><g stroke="#000080" stroke-width="2"><line x1="320" y1="195" x2="320" y2="285"/><line x1="275" y1="240" x2="365" y2="240"/><line x1="288" y1="208" x2="352" y2="272"/><line x1="288" y1="272" x2="352" y2="208"/><line x1="304" y1="197" x2="336" y2="283"/><line x1="336" y1="197" x2="304" y2="283"/><line x1="277" y1="224" x2="363" y2="256"/><line x1="277" y1="256" x2="363" y2="224"/></g></svg>',
    'US': '<svg viewBox="0 0 640 480" width="20" height="15" class="rounded-1 shadow-xs" style="display:inline-block; vertical-align:middle; border-radius:2px; flex-shrink:0;"><rect width="640" height="480" fill="#B22234"/><path d="M0,37h640M0,111h640M0,185h640M0,258h640M0,332h640M0,406h640" stroke="#FFFFFF" stroke-width="37"/><rect width="260" height="258" fill="#3C3B6E"/><g fill="#FFFFFF"><circle cx="40" cy="35" r="7"/><circle cx="90" cy="35" r="7"/><circle cx="140" cy="35" r="7"/><circle cx="190" cy="35" r="7"/><circle cx="240" cy="35" r="7"/><circle cx="65" cy="70" r="7"/><circle cx="115" cy="70" r="7"/><circle cx="165" cy="70" r="7"/><circle cx="215" cy="70" r="7"/><circle cx="40" cy="105" r="7"/><circle cx="90" cy="105" r="7"/><circle cx="140" cy="105" r="7"/><circle cx="190" cy="105" r="7"/><circle cx="240" cy="105" r="7"/><circle cx="65" cy="140" r="7"/><circle cx="115" cy="140" r="7"/><circle cx="165" cy="140" r="7"/><circle cx="215" cy="140" r="7"/><circle cx="40" cy="175" r="7"/><circle cx="90" cy="175" r="7"/><circle cx="140" cy="175" r="7"/><circle cx="190" cy="175" r="7"/><circle cx="240" cy="175" r="7"/><circle cx="65" cy="210" r="7"/><circle cx="115" cy="210" r="7"/><circle cx="165" cy="210" r="7"/><circle cx="215" cy="210" r="7"/></g></svg>',
    'GB': '<svg viewBox="0 0 640 480" width="20" height="15" class="rounded-1 shadow-xs" style="display:inline-block; vertical-align:middle; border-radius:2px; flex-shrink:0;"><clipPath id="js_gb"><rect width="640" height="480"/></clipPath><g clip-path="url(#js_gb)"><rect width="640" height="480" fill="#012169"/><path d="M0,0 L640,480 M640,0 L0,480" stroke="#FFFFFF" stroke-width="60"/><path d="M0,0 L640,480 M640,0 L0,480" stroke="#C8102E" stroke-width="36"/><path d="M320,0 V480 M0,240 H640" stroke="#FFFFFF" stroke-width="100"/><path d="M320,0 V480 M0,240 H640" stroke="#C8102E" stroke-width="60"/></g></svg>',
    'AE': '<svg viewBox="0 0 640 480" width="20" height="15" class="rounded-1 shadow-xs" style="display:inline-block; vertical-align:middle; border-radius:2px; flex-shrink:0;"><rect width="640" height="160" fill="#00732f"/><rect y="160" width="640" height="160" fill="#ffffff"/><rect y="320" width="640" height="160" fill="#000000"/><rect width="160" height="480" fill="#ff0000"/></svg>',
    'CA': '<svg viewBox="0 0 640 480" width="20" height="15" class="rounded-1 shadow-xs" style="display:inline-block; vertical-align:middle; border-radius:2px; flex-shrink:0;"><rect width="160" height="480" fill="#d80621"/><rect x="160" width="320" height="480" fill="#ffffff"/><rect x="480" width="160" height="480" fill="#d80621"/><path d="M320,130 L335,210 L370,185 L365,225 L405,230 L375,260 L390,290 L345,280 L350,305 L330,305 L330,350 L310,350 L310,305 L290,305 L295,280 L250,290 L265,260 L235,230 L275,225 L270,185 L305,210 Z" fill="#d80621"/></svg>',
    'AU': '<svg viewBox="0 0 640 480" width="20" height="15" class="rounded-1 shadow-xs" style="display:inline-block; vertical-align:middle; border-radius:2px; flex-shrink:0;"><rect width="640" height="480" fill="#00008b"/><g transform="scale(0.5)"><rect width="640" height="480" fill="#012169"/><path d="M0,0 L640,480 M640,0 L0,480" stroke="#FFFFFF" stroke-width="60"/><path d="M0,0 L640,480 M640,0 L0,480" stroke="#C8102E" stroke-width="36"/><path d="M320,0 V480 M0,240 H640" stroke="#FFFFFF" stroke-width="100"/><path d="M320,0 V480 M0,240 H640" stroke="#C8102E" stroke-width="60"/></g><circle cx="160" cy="360" r="30" fill="#ffffff"/><circle cx="480" cy="120" r="14" fill="#ffffff"/><circle cx="430" cy="200" r="14" fill="#ffffff"/><circle cx="530" cy="220" r="14" fill="#ffffff"/><circle cx="470" cy="300" r="14" fill="#ffffff"/><circle cx="500" cy="250" r="9" fill="#ffffff"/></svg>',
    'SG': '<svg viewBox="0 0 640 480" width="20" height="15" class="rounded-1 shadow-xs" style="display:inline-block; vertical-align:middle; border-radius:2px; flex-shrink:0;"><rect width="640" height="240" fill="#ed2939"/><rect y="240" width="640" height="240" fill="#ffffff"/><circle cx="130" cy="120" r="60" fill="#ffffff"/><circle cx="150" cy="120" r="55" fill="#ed2939"/><circle cx="170" cy="85" r="10" fill="#ffffff"/><circle cx="195" cy="105" r="10" fill="#ffffff"/><circle cx="195" cy="135" r="10" fill="#ffffff"/><circle cx="170" cy="155" r="10" fill="#ffffff"/><circle cx="155" cy="120" r="10" fill="#ffffff"/></svg>',
    'SA': '<svg viewBox="0 0 640 480" width="20" height="15" class="rounded-1 shadow-xs" style="display:inline-block; vertical-align:middle; border-radius:2px; flex-shrink:0;"><rect width="640" height="480" fill="#006c35"/><rect x="180" y="270" width="280" height="14" rx="7" fill="#ffffff"/><polygon points="180,277 210,265 210,289" fill="#ffffff"/><text x="320" y="230" fill="#ffffff" font-size="65" font-family="sans-serif" font-weight="bold" text-anchor="middle">SA</text></svg>',
    'DE': '<svg viewBox="0 0 640 480" width="20" height="15" class="rounded-1 shadow-xs" style="display:inline-block; vertical-align:middle; border-radius:2px; flex-shrink:0;"><rect width="640" height="160" fill="#000000"/><rect y="160" width="640" height="160" fill="#dd0000"/><rect y="320" width="640" height="160" fill="#ffce00"/></svg>',
    'FR': '<svg viewBox="0 0 640 480" width="20" height="15" class="rounded-1 shadow-xs" style="display:inline-block; vertical-align:middle; border-radius:2px; flex-shrink:0;"><rect width="213" height="480" fill="#002395"/><rect x="213" width="214" height="480" fill="#ffffff"/><rect x="427" width="213" height="480" fill="#ed2939"/></svg>',
    'QA': '<svg viewBox="0 0 640 480" width="20" height="15" class="rounded-1 shadow-xs" style="display:inline-block; vertical-align:middle; border-radius:2px; flex-shrink:0;"><rect width="640" height="480" fill="#8d1b3d"/><polygon points="0,0 200,0 260,26 200,53 260,80 200,106 260,133 200,160 260,186 200,213 260,240 200,266 260,293 200,320 260,346 200,373 260,400 200,426 260,453 200,480 0,480" fill="#ffffff"/></svg>',
    'KW': '<svg viewBox="0 0 640 480" width="20" height="15" class="rounded-1 shadow-xs" style="display:inline-block; vertical-align:middle; border-radius:2px; flex-shrink:0;"><rect width="640" height="160" fill="#007a3d"/><rect y="160" width="640" height="160" fill="#ffffff"/><rect y="320" width="640" height="160" fill="#ce1126"/><polygon points="0,0 180,140 180,340 0,480" fill="#000000"/></svg>',
    'OM': '<svg viewBox="0 0 640 480" width="20" height="15" class="rounded-1 shadow-xs" style="display:inline-block; vertical-align:middle; border-radius:2px; flex-shrink:0;"><rect width="640" height="160" fill="#ffffff"/><rect y="160" width="640" height="160" fill="#db161b"/><rect y="320" width="640" height="160" fill="#008000"/><rect width="180" height="480" fill="#db161b"/><circle cx="90" cy="90" r="30" fill="#ffffff"/></svg>',
    'BH': '<svg viewBox="0 0 640 480" width="20" height="15" class="rounded-1 shadow-xs" style="display:inline-block; vertical-align:middle; border-radius:2px; flex-shrink:0;"><rect width="640" height="480" fill="#ce1126"/><polygon points="0,0 180,0 250,48 180,96 250,144 180,192 250,240 180,288 250,336 180,384 250,432 180,480 0,480" fill="#ffffff"/></svg>',
    'MY': '<svg viewBox="0 0 640 480" width="20" height="15" class="rounded-1 shadow-xs" style="display:inline-block; vertical-align:middle; border-radius:2px; flex-shrink:0;"><rect width="640" height="480" fill="#cc0000"/><path d="M0,34h640M0,102h640M0,170h640M0,238h640M0,306h640M0,374h640M0,442h640" stroke="#ffffff" stroke-width="34"/><rect width="320" height="272" fill="#000066"/><circle cx="150" cy="136" r="60" fill="#ffcc00"/><circle cx="170" cy="136" r="50" fill="#000066"/><circle cx="205" cy="136" r="30" fill="#ffcc00"/></svg>',
    'PH': '<svg viewBox="0 0 640 480" width="20" height="15" class="rounded-1 shadow-xs" style="display:inline-block; vertical-align:middle; border-radius:2px; flex-shrink:0;"><rect width="640" height="240" fill="#0038a8"/><rect y="240" width="640" height="240" fill="#ce1126"/><polygon points="0,0 280,240 0,480" fill="#ffffff"/><circle cx="90" cy="240" r="28" fill="#fcd116"/></svg>',
    'ID': '<svg viewBox="0 0 640 480" width="20" height="15" class="rounded-1 shadow-xs" style="display:inline-block; vertical-align:middle; border-radius:2px; flex-shrink:0;"><rect width="640" height="240" fill="#ce1126"/><rect y="240" width="640" height="240" fill="#ffffff"/></svg>',
    'NZ': '<svg viewBox="0 0 640 480" width="20" height="15" class="rounded-1 shadow-xs" style="display:inline-block; vertical-align:middle; border-radius:2px; flex-shrink:0;"><rect width="640" height="480" fill="#00247d"/><g transform="scale(0.5)"><rect width="640" height="480" fill="#012169"/><path d="M0,0 L640,480 M640,0 L0,480" stroke="#FFFFFF" stroke-width="60"/><path d="M0,0 L640,480 M640,0 L0,480" stroke="#C8102E" stroke-width="36"/><path d="M320,0 V480 M0,240 H640" stroke="#FFFFFF" stroke-width="100"/><path d="M320,0 V480 M0,240 H640" stroke="#C8102E" stroke-width="60"/></g><circle cx="480" cy="120" r="14" fill="#cc142b" stroke="#ffffff" stroke-width="4"/><circle cx="430" cy="200" r="14" fill="#cc142b" stroke="#ffffff" stroke-width="4"/><circle cx="530" cy="220" r="14" fill="#cc142b" stroke="#ffffff" stroke-width="4"/><circle cx="470" cy="300" r="14" fill="#cc142b" stroke="#ffffff" stroke-width="4"/></svg>',
    'ZA': '<svg viewBox="0 0 640 480" width="20" height="15" class="rounded-1 shadow-xs" style="display:inline-block; vertical-align:middle; border-radius:2px; flex-shrink:0;"><rect width="640" height="240" fill="#e03c31"/><rect y="240" width="640" height="240" fill="#001489"/><polygon points="0,0 260,240 0,480" fill="#000000"/><path d="M0,0 L260,240 L640,240 M0,480 L260,240" stroke="#007749" stroke-width="80" fill="none"/><path d="M0,0 L260,240 L640,240 M0,480 L260,240" stroke="#ffb81c" stroke-width="25" fill="none"/></svg>',
    'IE': '<svg viewBox="0 0 640 480" width="20" height="15" class="rounded-1 shadow-xs" style="display:inline-block; vertical-align:middle; border-radius:2px; flex-shrink:0;"><rect width="213" height="480" fill="#169b62"/><rect x="213" width="214" height="480" fill="#ffffff"/><rect x="427" width="213" height="480" fill="#ff883e"/></svg>',
    'NL': '<svg viewBox="0 0 640 480" width="20" height="15" class="rounded-1 shadow-xs" style="display:inline-block; vertical-align:middle; border-radius:2px; flex-shrink:0;"><rect width="640" height="160" fill="#ae1c28"/><rect y="160" width="640" height="160" fill="#ffffff"/><rect y="320" width="640" height="160" fill="#21468b"/></svg>',
    'SE': '<svg viewBox="0 0 640 480" width="20" height="15" class="rounded-1 shadow-xs" style="display:inline-block; vertical-align:middle; border-radius:2px; flex-shrink:0;"><rect width="640" height="480" fill="#006aa7"/><rect x="200" width="80" height="480" fill="#fecc00"/><rect y="200" width="640" height="80" fill="#fecc00"/></svg>',
    'CH': '<svg viewBox="0 0 640 480" width="20" height="15" class="rounded-1 shadow-xs" style="display:inline-block; vertical-align:middle; border-radius:2px; flex-shrink:0;"><rect width="640" height="480" fill="#d52b1e"/><rect x="280" y="120" width="80" height="240" fill="#ffffff"/><rect x="200" y="200" width="240" height="80" fill="#ffffff"/></svg>',
    'ES': '<svg viewBox="0 0 640 480" width="20" height="15" class="rounded-1 shadow-xs" style="display:inline-block; vertical-align:middle; border-radius:2px; flex-shrink:0;"><rect width="640" height="120" fill="#aa151b"/><rect y="120" width="640" height="240" fill="#f1bf00"/><rect y="360" width="640" height="120" fill="#aa151b"/><circle cx="200" cy="240" r="40" fill="#aa151b"/></svg>',
    'IT': '<svg viewBox="0 0 640 480" width="20" height="15" class="rounded-1 shadow-xs" style="display:inline-block; vertical-align:middle; border-radius:2px; flex-shrink:0;"><rect width="213" height="480" fill="#009246"/><rect x="213" width="214" height="480" fill="#ffffff"/><rect x="427" width="213" height="480" fill="#ce2b37"/></svg>',
    'BR': '<svg viewBox="0 0 640 480" width="20" height="15" class="rounded-1 shadow-xs" style="display:inline-block; vertical-align:middle; border-radius:2px; flex-shrink:0;"><rect width="640" height="480" fill="#009c3b"/><polygon points="320,60 580,240 320,420 60,240" fill="#ffdf00"/><circle cx="320" cy="240" r="85" fill="#002776"/><path d="M236,240 Q320,210 404,240" stroke="#ffffff" stroke-width="12" fill="none"/></svg>',
    'MX': '<svg viewBox="0 0 640 480" width="20" height="15" class="rounded-1 shadow-xs" style="display:inline-block; vertical-align:middle; border-radius:2px; flex-shrink:0;"><rect width="213" height="480" fill="#006847"/><rect x="213" width="214" height="480" fill="#ffffff"/><rect x="427" width="213" height="480" fill="#ce1126"/><circle cx="320" cy="240" r="30" fill="#9c7a40"/></svg>',
    'JP': '<svg viewBox="0 0 640 480" width="20" height="15" class="rounded-1 shadow-xs" style="display:inline-block; vertical-align:middle; border-radius:2px; flex-shrink:0;"><rect width="640" height="480" fill="#ffffff"/><circle cx="320" cy="240" r="120" fill="#bc002d"/></svg>',
    'KR': '<svg viewBox="0 0 640 480" width="20" height="15" class="rounded-1 shadow-xs" style="display:inline-block; vertical-align:middle; border-radius:2px; flex-shrink:0;"><rect width="640" height="480" fill="#ffffff"/><circle cx="320" cy="240" r="90" fill="#cd2e3a"/><path d="M230,240 A90,90 0 0,0 410,240 A45,45 0 0,0 320,240 A45,45 0 0,1 230,240 Z" fill="#0047a0"/></svg>',
    'PK': '<svg viewBox="0 0 640 480" width="20" height="15" class="rounded-1 shadow-xs" style="display:inline-block; vertical-align:middle; border-radius:2px; flex-shrink:0;"><rect width="160" height="480" fill="#ffffff"/><rect x="160" width="480" height="480" fill="#01411c"/><circle cx="400" cy="240" r="90" fill="#ffffff"/><circle cx="430" cy="220" r="80" fill="#01411c"/><circle cx="450" cy="190" r="22" fill="#ffffff"/></svg>',
    'BD': '<svg viewBox="0 0 640 480" width="20" height="15" class="rounded-1 shadow-xs" style="display:inline-block; vertical-align:middle; border-radius:2px; flex-shrink:0;"><rect width="640" height="480" fill="#006a4e"/><circle cx="280" cy="240" r="120" fill="#f42a41"/></svg>',
    'LK': '<svg viewBox="0 0 640 480" width="20" height="15" class="rounded-1 shadow-xs" style="display:inline-block; vertical-align:middle; border-radius:2px; flex-shrink:0;"><rect width="640" height="480" fill="#ffbe29"/><rect x="40" y="40" width="60" height="400" fill="#00534e"/><rect x="110" y="40" width="60" height="400" fill="#eb7400"/><rect x="190" y="40" width="410" height="400" fill="#8d153a"/><circle cx="400" cy="240" r="70" fill="#ffbe29"/></svg>',
    'NP': '<svg viewBox="0 0 640 480" width="20" height="15" class="rounded-1 shadow-xs" style="display:inline-block; vertical-align:middle; border-radius:2px; flex-shrink:0;"><rect width="640" height="480" fill="#ffffff"/><polygon points="40,20 400,220 180,220 420,440 40,440" fill="#dc143c" stroke="#003893" stroke-width="20"/><circle cx="160" cy="140" r="30" fill="#ffffff"/><circle cx="180" cy="340" r="40" fill="#ffffff"/></svg>',
    'NG': '<svg viewBox="0 0 640 480" width="20" height="15" class="rounded-1 shadow-xs" style="display:inline-block; vertical-align:middle; border-radius:2px; flex-shrink:0;"><rect width="213" height="480" fill="#008751"/><rect x="213" width="214" height="480" fill="#ffffff"/><rect x="427" width="213" height="480" fill="#008751"/></svg>',
    'KE': '<svg viewBox="0 0 640 480" width="20" height="15" class="rounded-1 shadow-xs" style="display:inline-block; vertical-align:middle; border-radius:2px; flex-shrink:0;"><rect width="640" height="140" fill="#000000"/><rect y="140" width="640" height="20" fill="#ffffff"/><rect y="160" width="640" height="160" fill="#bb0000"/><rect y="320" width="640" height="20" fill="#ffffff"/><rect y="340" width="640" height="140" fill="#006600"/><ellipse cx="320" cy="240" rx="45" ry="90" fill="#bb0000" stroke="#ffffff" stroke-width="8"/></svg>',
    'EG': '<svg viewBox="0 0 640 480" width="20" height="15" class="rounded-1 shadow-xs" style="display:inline-block; vertical-align:middle; border-radius:2px; flex-shrink:0;"><rect width="640" height="160" fill="#ce1126"/><rect y="160" width="640" height="160" fill="#ffffff"/><rect y="320" width="640" height="160" fill="#000000"/><circle cx="320" cy="240" r="28" fill="#c09338"/></svg>',
    'TR': '<svg viewBox="0 0 640 480" width="20" height="15" class="rounded-1 shadow-xs" style="display:inline-block; vertical-align:middle; border-radius:2px; flex-shrink:0;"><rect width="640" height="480" fill="#e30a17"/><circle cx="280" cy="240" r="90" fill="#ffffff"/><circle cx="305" cy="240" r="72" fill="#e30a17"/><polygon points="370,240 395,248 375,263 382,288 360,272 338,288 345,263 325,248 350,240 360,215" fill="#ffffff"/></svg>',
    'PL': '<svg viewBox="0 0 640 480" width="20" height="15" class="rounded-1 shadow-xs" style="display:inline-block; vertical-align:middle; border-radius:2px; flex-shrink:0;"><rect width="640" height="240" fill="#ffffff"/><rect y="240" width="640" height="240" fill="#dc143c"/></svg>',
    'RO': '<svg viewBox="0 0 640 480" width="20" height="15" class="rounded-1 shadow-xs" style="display:inline-block; vertical-align:middle; border-radius:2px; flex-shrink:0;"><rect width="213" height="480" fill="#002b7f"/><rect x="213" width="214" height="480" fill="#fcd116"/><rect x="427" width="213" height="480" fill="#ce1126"/></svg>',
    'UA': '<svg viewBox="0 0 640 480" width="20" height="15" class="rounded-1 shadow-xs" style="display:inline-block; vertical-align:middle; border-radius:2px; flex-shrink:0;"><rect width="640" height="240" fill="#0057b7"/><rect y="240" width="640" height="240" fill="#ffd700"/></svg>',
    'VN': '<svg viewBox="0 0 640 480" width="20" height="15" class="rounded-1 shadow-xs" style="display:inline-block; vertical-align:middle; border-radius:2px; flex-shrink:0;"><rect width="640" height="480" fill="#da251d"/><polygon points="320,130 348,217 439,217 365,271 393,358 320,304 247,358 275,271 201,217 292,217" fill="#ffff00"/></svg>',
    'TH': '<svg viewBox="0 0 640 480" width="20" height="15" class="rounded-1 shadow-xs" style="display:inline-block; vertical-align:middle; border-radius:2px; flex-shrink:0;"><rect width="640" height="80" fill="#a51931"/><rect y="80" width="640" height="80" fill="#f4f5f8"/><rect y="160" width="640" height="160" fill="#2d2a4a"/><rect y="320" width="640" height="80" fill="#f4f5f8"/><rect y="400" width="640" height="80" fill="#a51931"/></svg>',
    'HK': '<svg viewBox="0 0 640 480" width="20" height="15" class="rounded-1 shadow-xs" style="display:inline-block; vertical-align:middle; border-radius:2px; flex-shrink:0;"><rect width="640" height="480" fill="#de2910"/><circle cx="320" cy="240" r="90" fill="#ffffff"/><circle cx="320" cy="240" r="70" fill="#de2910"/><circle cx="320" cy="240" r="30" fill="#ffffff"/></svg>',
    'TW': '<svg viewBox="0 0 640 480" width="20" height="15" class="rounded-1 shadow-xs" style="display:inline-block; vertical-align:middle; border-radius:2px; flex-shrink:0;"><rect width="640" height="480" fill="#fe0000"/><rect width="320" height="240" fill="#000095"/><circle cx="160" cy="120" r="60" fill="#ffffff"/><circle cx="160" cy="120" r="45" fill="#000095"/><circle cx="160" cy="120" r="30" fill="#ffffff"/></svg>',
    'IL': '<svg viewBox="0 0 640 480" width="20" height="15" class="rounded-1 shadow-xs" style="display:inline-block; vertical-align:middle; border-radius:2px; flex-shrink:0;"><rect width="640" height="480" fill="#ffffff"/><rect y="50" width="640" height="60" fill="#0038b8"/><rect y="370" width="640" height="60" fill="#0038b8"/><polygon points="320,180 370,270 270,270" stroke="#0038b8" stroke-width="12" fill="none"/><polygon points="320,300 370,210 270,210" stroke="#0038b8" stroke-width="12" fill="none"/></svg>',
    'RU': '<svg viewBox="0 0 640 480" width="20" height="15" class="rounded-1 shadow-xs" style="display:inline-block; vertical-align:middle; border-radius:2px; flex-shrink:0;"><rect width="640" height="160" fill="#ffffff"/><rect y="160" width="640" height="160" fill="#0039a6"/><rect y="320" width="640" height="160" fill="#d52b1e"/></svg>',
    'MV': '<svg viewBox="0 0 640 480" width="20" height="15" class="rounded-1 shadow-xs" style="display:inline-block; vertical-align:middle; border-radius:2px; flex-shrink:0;"><rect width="640" height="480" fill="#d21034"/><rect x="120" y="90" width="400" height="300" fill="#007e3a"/><circle cx="340" cy="240" r="75" fill="#ffffff"/><circle cx="365" cy="240" r="75" fill="#007e3a"/></svg>',
    'YE': '<svg viewBox="0 0 640 480" width="20" height="15" class="rounded-1 shadow-xs" style="display:inline-block; vertical-align:middle; border-radius:2px; flex-shrink:0;"><rect width="640" height="160" fill="#ce1126"/><rect y="160" width="640" height="160" fill="#ffffff"/><rect y="320" width="640" height="160" fill="#000000"/></svg>',
    'SY': '<svg viewBox="0 0 640 480" width="20" height="15" class="rounded-1 shadow-xs" style="display:inline-block; vertical-align:middle; border-radius:2px; flex-shrink:0;"><rect width="640" height="160" fill="#ce1126"/><rect y="160" width="640" height="160" fill="#ffffff"/><rect y="320" width="640" height="160" fill="#000000"/><circle cx="240" cy="240" r="24" fill="#007a3d"/><circle cx="400" cy="240" r="24" fill="#007a3d"/></svg>',
    'IR': '<svg viewBox="0 0 640 480" width="20" height="15" class="rounded-1 shadow-xs" style="display:inline-block; vertical-align:middle; border-radius:2px; flex-shrink:0;"><rect width="640" height="160" fill="#239f40"/><rect y="160" width="640" height="160" fill="#ffffff"/><rect y="320" width="640" height="160" fill="#da0000"/><circle cx="320" cy="240" r="30" fill="#da0000"/></svg>'
  };

  // Master ISO-3 and Recruiter Aliases / Synonyms / Regional Names Dictionary
  const COUNTRY_EXTRAS = {
    IN: { iso3: "IND", aliases: ["bharat", "hindustan", "ind", "india", "delhi", "mumbai", "bangalore"] },
    US: { iso3: "USA", aliases: ["usa", "america", "united states of america", "us of a", "states", "american", "us", "u.s.a", "u.s."] },
    GB: { iso3: "GBR", aliases: ["uk", "britain", "great britain", "england", "scotland", "wales", "northern ireland", "gbr", "british", "london", "u.k."] },
    AE: { iso3: "ARE", aliases: ["uae", "emirates", "dubai", "dxb", "abu dhabi", "sharjah", "ajman", "ras al khaimah", "fujairah", "umm al quwain", "are", "united arab emirates", "u.a.e."] },
    CA: { iso3: "CAN", aliases: ["can", "canada", "toronto", "vancouver", "montreal"] },
    AU: { iso3: "AUS", aliases: ["aus", "aussie", "oz", "australia", "sydney", "melbourne"] },
    SG: { iso3: "SGP", aliases: ["sgp", "sng", "singa", "singapore", "sing"] },
    SA: { iso3: "SAU", aliases: ["ksa", "kingdom of saudi arabia", "saudi", "riyadh", "jeddah", "sau", "k.s.a."] },
    DE: { iso3: "DEU", aliases: ["deutschland", "ger", "deu", "germany", "aleman", "allemagne", "berlin", "munich", "frankfurt"] },
    FR: { iso3: "FRA", aliases: ["fra", "france", "french", "paris", "republique francaise"] },
    QA: { iso3: "QAT", aliases: ["qat", "doha", "qatar"] },
    KW: { iso3: "KWT", aliases: ["kwt", "kuwait city", "kuwait"] },
    OM: { iso3: "OMN", aliases: ["omn", "muscat", "oman"] },
    BH: { iso3: "BHR", aliases: ["bhr", "manama", "bahrain"] },
    MY: { iso3: "MYS", aliases: ["mys", "mas", "malay", "malaysia", "kuala lumpur", "kl"] },
    PH: { iso3: "PHL", aliases: ["phl", "phi", "phils", "phil", "philippines", "filipino", "pinoy", "manila", "rp"] },
    ID: { iso3: "IDN", aliases: ["idn", "ina", "indo", "indonesia", "jakarta"] },
    NZ: { iso3: "NZL", aliases: ["nzl", "aotearoa", "kiwi", "new zealand", "auckland", "wellington", "nz"] },
    ZA: { iso3: "ZAF", aliases: ["rsa", "zaf", "south africa", "mzansi", "johannesburg", "cape town"] },
    IE: { iso3: "IRL", aliases: ["irl", "eire", "ireland", "republic of ireland", "dublin"] },
    NL: { iso3: "NLD", aliases: ["nld", "ned", "holland", "nederland", "dutch", "the netherlands", "amsterdam", "rotterdam"] },
    SE: { iso3: "SWE", aliases: ["swe", "sverige", "sweden", "stockholm"] },
    CH: { iso3: "CHE", aliases: ["che", "sui", "swiss", "schweiz", "suisse", "svizzera", "switzerland", "zurich", "geneva"] },
    JP: { iso3: "JPN", aliases: ["jpn", "nippon", "nihon", "japan", "tokyo"] },
    KR: { iso3: "KOR", aliases: ["kor", "rok", "korea", "south korea", "republic of korea", "seoul"] },
    CN: { iso3: "CHN", aliases: ["chn", "prc", "china", "people's republic of china", "mainland china", "beijing", "shanghai"] },
    HK: { iso3: "HKG", aliases: ["hkg", "hk", "hong kong", "hongkong"] },
    TW: { iso3: "TWN", aliases: ["twn", "roc", "taiwan", "chinese taipei", "taipei"] },
    TH: { iso3: "THA", aliases: ["tha", "siam", "thailand", "bangkok"] },
    VN: { iso3: "VNM", aliases: ["vnm", "vie", "viet nam", "vietnam", "saigon", "hanoi"] },
    ES: { iso3: "ESP", aliases: ["esp", "espana", "spain", "madrid", "barcelona"] },
    IT: { iso3: "ITA", aliases: ["ita", "italia", "italy", "rome", "milan"] },
    PT: { iso3: "PRT", aliases: ["prt", "por", "portugal", "lisbon"] },
    BE: { iso3: "BEL", aliases: ["bel", "belgique", "belgie", "belgium", "brussels"] },
    AT: { iso3: "AUT", aliases: ["aut", "osterreich", "austria", "vienna"] },
    NO: { iso3: "NOR", aliases: ["nor", "norge", "norway", "oslo"] },
    DK: { iso3: "DNK", aliases: ["dnk", "den", "danmark", "denmark", "copenhagen"] },
    FI: { iso3: "FIN", aliases: ["fin", "suomi", "finland", "helsinki"] },
    PL: { iso3: "POL", aliases: ["pol", "polska", "poland", "warsaw"] },
    CZ: { iso3: "CZE", aliases: ["cze", "czechia", "czech republic", "prague"] },
    RO: { iso3: "ROU", aliases: ["rou", "rom", "romania", "bucharest"] },
    HU: { iso3: "HUN", aliases: ["hun", "magyar", "hungary", "budapest"] },
    GR: { iso3: "GRC", aliases: ["grc", "gre", "hellas", "greece", "athens"] },
    TR: { iso3: "TUR", aliases: ["tur", "turkiye", "turkey", "istanbul", "ankara"] },
    IL: { iso3: "ISR", aliases: ["isr", "israel", "tel aviv", "jerusalem"] },
    EG: { iso3: "EGY", aliases: ["egy", "misr", "egypt", "cairo"] },
    NG: { iso3: "NGA", aliases: ["nga", "naija", "nigeria", "lagos", "abuja"] },
    KE: { iso3: "KEN", aliases: ["ken", "kenya", "nairobi"] },
    GH: { iso3: "GHA", aliases: ["gha", "ghana", "accra"] },
    PK: { iso3: "PAK", aliases: ["pak", "pakistan", "islamabad", "karachi", "lahore"] },
    BD: { iso3: "BGD", aliases: ["bgd", "bangla", "bangladesh", "dhaka"] },
    LK: { iso3: "LKA", aliases: ["lka", "ceylon", "sri lanka", "colombo"] },
    NP: { iso3: "NPL", aliases: ["npl", "nepal", "kathmandu"] },
    MX: { iso3: "MEX", aliases: ["mex", "mexico", "mexico city"] },
    BR: { iso3: "BRA", aliases: ["bra", "brasil", "brazil", "sao paulo", "rio"] },
    AR: { iso3: "ARG", aliases: ["arg", "argentina", "buenos aires"] },
    CL: { iso3: "CHL", aliases: ["chl", "chile", "santiago"] },
    CO: { iso3: "COL", aliases: ["col", "colombia", "bogota"] },
    PE: { iso3: "PER", aliases: ["per", "peru", "lima"] },
    RU: { iso3: "RUS", aliases: ["rus", "russia", "russian federation", "rossiya", "moscow"] },
    AF: { iso3: "AFG", aliases: ["afg", "afghanistan", "kabul"] },
    AL: { iso3: "ALB", aliases: ["alb", "albania", "tirana"] },
    DZ: { iso3: "DZA", aliases: ["dza", "algeria", "algiers"] },
    AD: { iso3: "AND", aliases: ["and", "andorra"] },
    AO: { iso3: "AGO", aliases: ["ago", "angola", "luanda"] },
    AM: { iso3: "ARM", aliases: ["arm", "armenia", "yerevan"] },
    AZ: { iso3: "AZE", aliases: ["aze", "azerbaijan", "baku"] },
    BS: { iso3: "BHS", aliases: ["bhs", "bahamas", "nassau"] },
    BB: { iso3: "BRB", aliases: ["brb", "barbados", "bridgetown"] },
    BY: { iso3: "BLR", aliases: ["blr", "belarus", "minsk"] },
    BZ: { iso3: "BLZ", aliases: ["blz", "belize"] },
    BJ: { iso3: "BEN", aliases: ["ben", "benin", "cotonou"] },
    BM: { iso3: "BMU", aliases: ["bmu", "bermuda"] },
    BT: { iso3: "BTN", aliases: ["btn", "bhutan", "thimphu"] },
    BO: { iso3: "BOL", aliases: ["bol", "bolivia", "la paz"] },
    BA: { iso3: "BIH", aliases: ["bih", "bosnia", "herzegovina", "bosnia and herzegovina", "sarajevo"] },
    BW: { iso3: "BWA", aliases: ["bwa", "botswana", "gaborone"] },
    BN: { iso3: "BRN", aliases: ["brn", "brunei"] },
    BG: { iso3: "BGR", aliases: ["bgr", "bulgaria", "sofia"] },
    BF: { iso3: "BFA", aliases: ["bfa", "burkina faso", "ouagadougou"] },
    KH: { iso3: "KHM", aliases: ["khm", "cambodia", "phnom penh"] },
    CM: { iso3: "CMR", aliases: ["cmr", "cameroon", "yaounde"] },
    CR: { iso3: "CRI", aliases: ["cri", "costa rica", "san jose"] },
    HR: { iso3: "HRV", aliases: ["hrv", "croatia", "hrvatska", "zagreb"] },
    CU: { iso3: "CUB", aliases: ["cub", "cuba", "havana"] },
    CY: { iso3: "CYP", aliases: ["cyp", "cyprus", "nicosia"] },
    EC: { iso3: "ECU", aliases: ["ecu", "ecuador", "quito"] },
    EE: { iso3: "EST", aliases: ["est", "estonia", "tallinn"] },
    ET: { iso3: "ETH", aliases: ["eth", "ethiopia", "addis ababa"] },
    FJ: { iso3: "FJI", aliases: ["fji", "fiji", "suva"] },
    GE: { iso3: "GEO", aliases: ["geo", "georgia", "tbilisi"] },
    GI: { iso3: "GIB", aliases: ["gib", "gibraltar"] },
    GT: { iso3: "GTM", aliases: ["gtm", "guatemala", "guatemala city"] },
    IS: { iso3: "ISL", aliases: ["isl", "iceland", "reykjavik"] },
    IQ: { iso3: "IRQ", aliases: ["irq", "iraq", "baghdad"] },
    JM: { iso3: "JAM", aliases: ["jam", "jamaica", "kingston"] },
    JO: { iso3: "JOR", aliases: ["jor", "jordan", "amman"] },
    KZ: { iso3: "KAZ", aliases: ["kaz", "kazakhstan", "almaty", "astana"] },
    KG: { iso3: "KGZ", aliases: ["kgz", "kyrgyzstan", "bishkek"] },
    LA: { iso3: "LAO", aliases: ["lao", "laos", "vientiane"] },
    LV: { iso3: "LVA", aliases: ["lva", "latvia", "riga"] },
    LB: { iso3: "LBN", aliases: ["lbn", "lebanon", "beirut"] },
    LT: { iso3: "LTU", aliases: ["ltu", "lithuania", "vilnius"] },
    LU: { iso3: "LUX", aliases: ["lux", "luxembourg"] },
    MT: { iso3: "MLT", aliases: ["mlt", "malta", "valletta"] },
    MU: { iso3: "MUS", aliases: ["mus", "mauritius", "port louis"] },
    MD: { iso3: "MDA", aliases: ["mda", "moldova", "chisinau"] },
    MC: { iso3: "MCO", aliases: ["mco", "monaco"] },
    MA: { iso3: "MAR", aliases: ["mar", "morocco", "maroc", "casablanca", "rabat"] },
    MM: { iso3: "MMR", aliases: ["mmr", "myanmar", "burma", "yangon"] },
    PA: { iso3: "PAN", aliases: ["pan", "panama", "panama city"] },
    RS: { iso3: "SRB", aliases: ["srb", "serbia", "belgrade"] },
    SK: { iso3: "SVK", aliases: ["svk", "slovakia", "bratislava"] },
    SI: { iso3: "SVN", aliases: ["svn", "slovenia", "ljubljana"] },
    TZ: { iso3: "TZA", aliases: ["tza", "tanzania", "zanzibar", "dar es salaam"] },
    UG: { iso3: "UGA", aliases: ["uga", "uganda", "kampala"] },
    UA: { iso3: "UKR", aliases: ["ukr", "ukraine", "kyiv", "kiev"] },
    UY: { iso3: "URY", aliases: ["ury", "uruguay", "montevideo"] },
    UZ: { iso3: "UZB", aliases: ["uzb", "uzbekistan", "tashkent"] },
    MV: { iso3: "MDV", aliases: ["mdv", "maldives", "male"] },
    YE: { iso3: "YEM", aliases: ["yem", "yemen", "sanaa"] },
    SY: { iso3: "SYR", aliases: ["syr", "syria", "damascus"] },
    IR: { iso3: "IRN", aliases: ["irn", "iran", "persia", "tehran"] },
    ZW: { iso3: "ZWE", aliases: ["zwe", "zimbabwe", "harare"] },
    ZM: { iso3: "ZMB", aliases: ["zmb", "zambia", "lusaka"] },
    RW: { iso3: "RWA", aliases: ["rwa", "rwanda", "kigali"] },
    SN: { iso3: "SEN", aliases: ["sen", "senegal", "dakar"] },
    CI: { iso3: "CIV", aliases: ["civ", "ivory coast", "cote d'ivoire", "abidjan"] },
    TT: { iso3: "TTO", aliases: ["tto", "trinidad", "tobago", "trinidad and tobago"] },
    DO: { iso3: "DOM", aliases: ["dom", "dominican republic", "santo domingo"] },
    PR: { iso3: "PRI", aliases: ["pri", "puerto rico", "san juan"] }
  };

  // Enrich each country object with iso3, auto-computed acronym, normalized aliases, and digits
  window.__JERO_COUNTRIES_DB__.forEach(function(c) {
    var extra = COUNTRY_EXTRAS[c.iso] || {};
    c.iso3 = extra.iso3 || '';

    // Auto-generate acronym for multi-word country names (e.g. "United Arab Emirates" -> "uae", "New Zealand" -> "nz")
    var words = c.name.replace(/[^a-zA-Z\s]/g, '').split(/\s+/).filter(function(w) {
      return !['and', 'of', 'the', 'da', 'de', 'la'].includes(w.toLowerCase());
    });
    c.autoAcronym = words.length > 1 ? words.map(function(w) { return w[0]; }).join('').toLowerCase() : '';

    var aliasList = extra.aliases ? extra.aliases.slice() : [];
    if (c.iso3) aliasList.push(c.iso3.toLowerCase());
    if (c.autoAcronym) aliasList.push(c.autoAcronym);
    aliasList.push(c.iso.toLowerCase());
    
    // Deduplicate aliases
    var uniqueAliases = [];
    aliasList.forEach(function(a) {
      if (a && uniqueAliases.indexOf(a) === -1) uniqueAliases.push(a);
    });
    c.aliases = uniqueAliases;
    c.dialDigits = c.dial.replace(/[^\d]/g, '');
  });

  window.getJeroFlagSvg = function(iso) {
    const code = (iso || 'IN').toUpperCase();
    if (window.__JERO_FLAGS_SVG__ && window.__JERO_FLAGS_SVG__[code]) {
      return window.__JERO_FLAGS_SVG__[code];
    }
    const colors = ['#1e3c72', '#2a5298', '#134e5e', '#71b280', '#0f2027', '#203a43', '#2c5364', '#373b44'];
    const hash = (code.charCodeAt(0) + (code.charCodeAt(1) || 0)) % colors.length;
    const bg = colors[hash];
    return `<svg viewBox="0 0 640 480" width="20" height="15" class="rounded-1 shadow-xs" style="display:inline-block; vertical-align:middle; border-radius:2px; flex-shrink:0;"><rect width="640" height="480" fill="${bg}" rx="60"/><text x="320" y="320" fill="#ffffff" font-size="240" font-family="'Poppins', sans-serif" font-weight="700" text-anchor="middle">${code}</text></svg>`;
  };

  window.setupJeroPhoneBox = function(wrapper) {
    if (!wrapper || wrapper._jeroInitialized) return;
    wrapper._jeroInitialized = true;
    wrapper._jeroPhoneBoxInstance = true;

    const btn = wrapper.querySelector('.jero-country-picker-btn');
    const dropdown = wrapper.querySelector('.jero-country-dropdown');
    const searchInput = wrapper.querySelector('.jero-search-input');
    const searchClear = wrapper.querySelector('.jero-search-clear');
    const countryList = wrapper.querySelector('.jero-country-list');
    const flagSpan = wrapper.querySelector('.jero-selected-flag');
    const dialSpan = wrapper.querySelector('.jero-selected-dial');
    const hiddenCode = wrapper.querySelector('.jero-phone-country-code-input') || wrapper.querySelector('input[type="hidden"]');
    const input = wrapper.querySelector('input[type="tel"]');
    const counter = wrapper.querySelector('.jero-digit-counter');
    const inputGroup = wrapper.querySelector('.jero-phone-input-group');
    const errorContainer = wrapper.querySelector(`#${input ? input.id : ''}_error`) || wrapper.querySelector('.jero-phone-error-msg');
    const defaultIso = (wrapper.getAttribute('data-default-iso') || 'IN').toUpperCase();

    if (!btn || !dropdown || !searchInput || !countryList || !input || !hiddenCode) return;

    let currentCountry = null;
    let selectedIndex = -1;

    // Resolve initial country
    let initialDial = (hiddenCode.value || '').trim();
    if (initialDial) {
      if (/^[a-zA-Z]{2,4}$/.test(initialDial)) {
        const codeUpper = initialDial.toUpperCase();
        const codeLower = initialDial.toLowerCase();
        currentCountry = window.__JERO_COUNTRIES_DB__.find(c => 
          c.iso === codeUpper || 
          (c.iso3 && c.iso3 === codeUpper) ||
          (c.aliases && c.aliases.includes(codeLower))
        );
      } else {
        const clean = initialDial.startsWith('+') ? initialDial : ('+' + initialDial);
        currentCountry = window.__JERO_COUNTRIES_DB__.find(c => c.dial === clean && c.iso === defaultIso) ||
                         window.__JERO_COUNTRIES_DB__.find(c => c.dial === clean);
      }
    }
    if (!currentCountry) {
      currentCountry = window.__JERO_COUNTRIES_DB__.find(c => c.iso === defaultIso) || window.__JERO_COUNTRIES_DB__[0];
    }

    // Ensure initial flag is present and accurate
    if (currentCountry && flagSpan) {
      if (!flagSpan.querySelector('svg') || flagSpan.getAttribute('data-iso') !== currentCountry.iso) {
        flagSpan.innerHTML = window.getJeroFlagSvg(currentCountry.iso);
        flagSpan.setAttribute('data-iso', currentCountry.iso);
      }

      // God-level Flag Protection: Prevent ANY rogue external script from wiping flagSpan or replacing with blank/emoji
      if (window.MutationObserver && !flagSpan._hasObserver) {
        flagSpan._hasObserver = true;
        const mo = new MutationObserver(() => {
          if (!flagSpan.querySelector('svg') && currentCountry) {
            flagSpan.innerHTML = window.getJeroFlagSvg(currentCountry.iso);
            flagSpan.setAttribute('data-iso', currentCountry.iso);
          }
        });
        mo.observe(flagSpan, { childList: true, characterData: true, subtree: true });
      }
    }

    function updateCounter(targetLength) {
      if (!counter) return;
      const currentDigits = (input.value || '').replace(/[^\d]/g, '');
      const len = targetLength || (currentCountry ? currentCountry.len : 10);
      counter.textContent = `${currentDigits.length}/${len}`;
      if (currentDigits.length === 0) {
        counter.className = 'badge bg-light text-muted jero-digit-counter';
      } else if (currentDigits.length === len) {
        counter.className = 'badge bg-light-success text-success fw-bold jero-digit-counter';
        counter.innerHTML = `${currentDigits.length}/${len} <i class="fa fa-check text-success ms-1" style="font-size:9px;"></i>`;
      } else {
        counter.className = 'badge bg-light-primary text-primary fw-semibold jero-digit-counter';
      }
    }

    function selectCountry(c) {
      if (!c) return;
      currentCountry = c;
      hiddenCode.value = c.dial;
      dialSpan.textContent = c.dial;
      flagSpan.innerHTML = window.getJeroFlagSvg(c.iso);
      flagSpan.setAttribute('data-iso', c.iso);
      input.setAttribute('maxlength', c.len);

      const digits = (input.value || '').replace(/[^\d]/g, '');
      if (digits.length > c.len) {
        input.value = digits.substring(0, c.len);
      }
      updateCounter(c.len);
      closeDropdown();
      hiddenCode.dispatchEvent(new Event('change', { bubbles: true }));
      input.focus();
    }

    function renderList(query = '') {
      const qRaw = (query || '').trim().toLowerCase();
      // Clean query of punctuation, periods, hyphens for acronym matching (e.g., "u.a.e." -> "uae", "u.s.a." -> "usa")
      const qClean = qRaw.replace(/[\.\-_\s,]/g, '');
      const qDigits = qRaw.replace(/[^\d]/g, '');

      let scoredResults = [];

      if (!qRaw) {
        scoredResults = window.__JERO_COUNTRIES_DB__.map(c => ({ country: c, score: 0 }));
      } else {
        window.__JERO_COUNTRIES_DB__.forEach(c => {
          let score = 0;
          let matchedAlias = null;

          const isoLower = c.iso.toLowerCase();
          const iso3Lower = (c.iso3 || '').toLowerCase();
          const nameLower = c.name.toLowerCase();
          const dialLower = c.dial.toLowerCase();
          const autoAcronym = (c.autoAcronym || '').toLowerCase();

          // 1. EXACT MATCHES (Top Tier: 2500 - 3000)
          if (isoLower === qRaw || isoLower === qClean) {
            score = 3000;
          } else if (iso3Lower && (iso3Lower === qRaw || iso3Lower === qClean)) {
            score = 2900;
            matchedAlias = c.iso3;
          } else if (autoAcronym && (autoAcronym === qRaw || autoAcronym === qClean)) {
            score = 2800;
            matchedAlias = autoAcronym.toUpperCase();
          } else if (nameLower === qRaw) {
            score = 2700;
          } else if (qDigits && (dialLower === qRaw || c.dialDigits === qDigits)) {
            score = 2600;
          } else if (c.aliases && c.aliases.length) {
            for (let i = 0; i < c.aliases.length; i++) {
              const a = c.aliases[i];
              if (a === qRaw || a === qClean) {
                score = 2500;
                matchedAlias = a;
                break;
              }
            }
          }

          // 2. PREFIX MATCHES (High Tier: 1100 - 1500)
          if (score === 0) {
            if (nameLower.startsWith(qRaw)) {
              score = 1500;
            } else {
              const words = nameLower.split(/\s+/);
              for (let i = 0; i < words.length; i++) {
                if (words[i].startsWith(qRaw)) {
                  score = 1400;
                  break;
                }
              }
            }

            if (score === 0 && iso3Lower && iso3Lower.startsWith(qClean)) {
              score = 1350;
              matchedAlias = c.iso3;
            }

            if (score === 0 && autoAcronym && autoAcronym.startsWith(qClean)) {
              score = 1300;
              matchedAlias = autoAcronym.toUpperCase();
            }

            if (score === 0 && qDigits && (dialLower.startsWith(qRaw) || c.dialDigits.startsWith(qDigits))) {
              score = 1250;
            }

            if (score === 0 && c.aliases && c.aliases.length) {
              for (let i = 0; i < c.aliases.length; i++) {
                const a = c.aliases[i];
                if (a.startsWith(qRaw) || (qClean.length >= 2 && a.startsWith(qClean))) {
                  score = 1100;
                  matchedAlias = a;
                  break;
                }
              }
            }
          }

          // 3. SUBSTRING / CONTAINS MATCHES (Standard Tier: 300 - 600)
          if (score === 0) {
            if (nameLower.includes(qRaw)) {
              score = 600;
            } else if (c.aliases && c.aliases.length) {
              for (let i = 0; i < c.aliases.length; i++) {
                const a = c.aliases[i];
                if (a.includes(qRaw) || (qClean.length >= 2 && a.includes(qClean))) {
                  score = 500;
                  matchedAlias = a;
                  break;
                }
              }
            } else if (qDigits && (dialLower.includes(qRaw) || c.dialDigits.includes(qDigits))) {
              score = 300;
            }
          }

          if (score > 0) {
            scoredResults.push({ country: c, score, matchedAlias });
          }
        });

        // Sort descending by match score; ties keep standard priority
        scoredResults.sort((a, b) => b.score - a.score);
      }

      if (scoredResults.length === 0) {
        countryList.innerHTML = `<div class="p-4 text-center text-muted fs-8">No country matching "${query}"</div>`;
        return;
      }

      let html = '';
      scoredResults.forEach((res, index) => {
        const c = res.country;
        const isSelected = currentCountry && currentCountry.iso === c.iso;
        const flagSvg = window.getJeroFlagSvg(c.iso);

        let aliasBadge = '';
        if (res.matchedAlias && !c.name.toLowerCase().includes(res.matchedAlias.toLowerCase())) {
          aliasBadge = `<span class="badge bg-light-primary text-primary ms-1 px-1 py-0 fs-9 fw-semibold text-uppercase" style="font-size:9.5px !important; line-height:1.2;">${res.matchedAlias}</span>`;
        }

        html += `
          <div class="d-flex align-items-center justify-content-between px-3 py-2 cursor-pointer jero-country-item ${isSelected ? 'bg-light-primary text-primary fw-bold' : 'text-gray-800'}"
               data-dial="${c.dial}"
               data-iso="${c.iso}"
               data-index="${index}"
               role="option"
               aria-selected="${isSelected ? 'true' : 'false'}"
               style="font-size: 13px; border-bottom: 1px solid #f4f4f7; transition: background-color 0.15s ease; user-select: none;">
            <div class="d-flex align-items-center text-truncate me-2">
              <span class="me-2 d-inline-flex align-items-center flex-shrink-0" style="width: 20px; height: 15px;">${flagSvg}</span>
              <span class="text-truncate">${c.name}</span>
              <span class="text-muted fs-8 ms-1 flex-shrink-0">(${c.iso3 || c.iso})</span>
              ${aliasBadge}
            </div>
            <div class="d-flex align-items-center flex-shrink-0">
              <span class="text-muted fs-8 me-2">(${c.len} digits)</span>
              <span class="badge bg-light text-dark fs-8 fw-bold px-2 py-1">${c.dial}</span>
            </div>
          </div>
        `;
      });
      countryList.innerHTML = html;

      countryList.querySelectorAll('.jero-country-item').forEach(item => {
        item.addEventListener('click', (e) => {
          e.preventDefault();
          e.stopPropagation();
          const iso = item.getAttribute('data-iso');
          const found = window.__JERO_COUNTRIES_DB__.find(c => c.iso === iso);
          if (found) selectCountry(found);
        });
      });

      // Auto-highlight first item on search filter so Enter key directly confirms
      if (qRaw && scoredResults.length > 0) {
        selectedIndex = 0;
        const allItems = countryList.querySelectorAll('.jero-country-item');
        if (allItems.length > 0) highlightItem(allItems);
      }
    }

    function openDropdown() {
      // Close other dropdowns
      document.querySelectorAll('.jero-country-dropdown').forEach(d => {
        if (d !== dropdown) d.style.display = 'none';
      });

      // Viewport collision flip check
      const rect = wrapper.getBoundingClientRect();
      const spaceBelow = window.innerHeight - rect.bottom;
      if (spaceBelow < 340 && rect.top > 340) {
        dropdown.style.top = 'auto';
        dropdown.style.bottom = 'calc(100% + 4px)';
      } else {
        dropdown.style.bottom = 'auto';
        dropdown.style.top = 'calc(100% + 4px)';
      }

      wrapper.style.zIndex = '1060';
      dropdown.style.display = 'block';
      btn.setAttribute('aria-expanded', 'true');
      searchInput.value = '';
      if (searchClear) searchClear.style.display = 'none';
      selectedIndex = -1;
      renderList('');
      setTimeout(() => searchInput.focus(), 30);
    }

    function closeDropdown() {
      dropdown.style.display = 'none';
      btn.setAttribute('aria-expanded', 'false');
      wrapper.style.zIndex = '1';
    }

    // Toggle click on button
    btn.addEventListener('click', function(e) {
      e.preventDefault();
      e.stopPropagation();
      if (dropdown.style.display === 'block') {
        closeDropdown();
      } else {
        openDropdown();
      }
    });

    // Close on click outside
    document.addEventListener('click', function(e) {
      if (!wrapper.contains(e.target)) {
        closeDropdown();
      }
    });

    // Search clear click
    if (searchClear) {
      searchClear.addEventListener('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        searchInput.value = '';
        this.style.display = 'none';
        selectedIndex = -1;
        renderList('');
        searchInput.focus();
      });
    }

    // Search filter input
    searchInput.addEventListener('input', function(e) {
      if (searchClear) {
        searchClear.style.display = this.value.trim() ? 'block' : 'none';
      }
      selectedIndex = -1;
      renderList(this.value);
    });

    // Keyboard navigation in search
    searchInput.addEventListener('keydown', function(e) {
      const items = countryList.querySelectorAll('.jero-country-item');
      if (!items.length) return;

      if (e.key === 'ArrowDown') {
        e.preventDefault();
        selectedIndex = (selectedIndex + 1) % items.length;
        highlightItem(items);
      } else if (e.key === 'ArrowUp') {
        e.preventDefault();
        selectedIndex = (selectedIndex - 1 + items.length) % items.length;
        highlightItem(items);
      } else if (e.key === 'Enter') {
        e.preventDefault();
        if (selectedIndex >= 0 && items[selectedIndex]) {
          items[selectedIndex].click();
        } else if (items.length > 0) {
          items[0].click();
        }
      } else if (e.key === 'Escape') {
        e.preventDefault();
        closeDropdown();
      }
    });

    function highlightItem(items) {
      items.forEach((item, idx) => {
        if (idx === selectedIndex) {
          item.classList.add('bg-light-primary');
          item.scrollIntoView({ block: 'nearest' });
        } else {
          item.classList.remove('bg-light-primary');
        }
      });
    }

    // Digits input handling
    input.addEventListener('input', function() {
      const clean = this.value.replace(/[^\d]/g, '');
      if (this.value !== clean) this.value = clean;
      updateCounter(currentCountry ? currentCountry.len : 10);
      input.classList.remove('is-invalid');
      if (inputGroup) inputGroup.classList.remove('is-invalid');
      if (errorContainer) {
        errorContainer.style.display = 'none';
        errorContainer.innerHTML = '';
      }
      const rogueInWrapper = wrapper.querySelectorAll('.fv-plugins-message-container:not(.jero-phone-error-msg)');
      rogueInWrapper.forEach(function(r) {
        r.innerHTML = '';
        r.style.display = 'none';
      });
    });

    // Smart paste handling
    input.addEventListener('paste', function(e) {
      e.preventDefault();
      const raw = (e.clipboardData || window.clipboardData).getData('text') || '';
      const trimmed = raw.trim();

      for (const c of window.__JERO_COUNTRIES_DB__) {
        if (trimmed.startsWith(c.dial)) {
          selectCountry(c);
          const digits = trimmed.substring(c.dial.length).replace(/[^\d]/g, '');
          input.value = digits.substring(0, c.len);
          updateCounter(c.len);
          input.dispatchEvent(new Event('input', { bubbles: true }));
          return;
        }
      }

      let digits = trimmed.replace(/[^\d]/g, '');
      if (digits.startsWith('0') && currentCountry && digits.length > currentCountry.len) {
        digits = digits.substring(1);
      }
      const len = currentCountry ? currentCountry.len : 10;
      input.value = digits.substring(0, len);
      updateCounter(len);
      input.dispatchEvent(new Event('input', { bubbles: true }));
    });

    // Validation Conflict Resolution & Interception
    // 1. Intercept rogue FormValidation / jQuery Validate feedback nodes and icons injected inside 38px group
    if (inputGroup && errorContainer && window.MutationObserver && !inputGroup._hasValidationObserver) {
      inputGroup._hasValidationObserver = true;
      const groupObserver = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
          mutation.addedNodes.forEach(function(node) {
            if (node.nodeType === 1) {
              const isFeedback = node.classList && (
                node.classList.contains('fv-plugins-message-container') ||
                node.classList.contains('invalid-feedback') ||
                (node.tagName === 'LABEL' && node.classList.contains('error'))
              );
              const isIcon = node.classList && (
                node.classList.contains('fv-plugins-icon') ||
                node.classList.contains('fv-plugins-bootstrap5-icon')
              );

              if (isFeedback) {
                const text = (node.innerText || node.textContent || '').trim();
                if (text && errorContainer) {
                  errorContainer.innerHTML = node.innerHTML || `<div class="fv-help-block">${text}</div>`;
                  errorContainer.style.display = 'block';
                  inputGroup.classList.add('is-invalid');
                }
                try { node.remove(); } catch (e) {}
              } else if (isIcon) {
                try { node.remove(); } catch (e) {}
              }
            }
          });
        });
      });
      groupObserver.observe(inputGroup, { childList: true });
    }

    // 2. Observe input for is-invalid class added by FormValidation
    if (input && inputGroup && window.MutationObserver && !input._hasValidationClassObserver) {
      input._hasValidationClassObserver = true;
      const inputClassObserver = new MutationObserver(function() {
        if (input.classList.contains('is-invalid')) {
          inputGroup.classList.add('is-invalid');
          if (errorContainer && errorContainer.textContent.trim()) {
            errorContainer.style.display = 'block';
          }
        } else {
          inputGroup.classList.remove('is-invalid');
          if (errorContainer) {
            errorContainer.style.display = 'none';
            errorContainer.innerHTML = '';
          }
        }
      });
      inputClassObserver.observe(input, { attributes: true, attributeFilter: ['class'] });
    }

    // Initial counter render
    updateCounter(currentCountry ? currentCountry.len : 10);
  };

  const jeroPhoneBoxEngine = {
    _isV7: true,
    init(wrapperOrId) {
      const el = typeof wrapperOrId === 'string' ? document.getElementById(wrapperOrId) : wrapperOrId;
      if (el) window.setupJeroPhoneBox(el);
    },
    initAll() {
      document.querySelectorAll('.jero-phone-box-wrapper').forEach(w => window.setupJeroPhoneBox(w));
    }
  };

  try {
    Object.defineProperty(window, 'JeroPhoneBox', {
      value: jeroPhoneBoxEngine,
      writable: false,
      configurable: true
    });
  } catch (e) {
    window.JeroPhoneBox = jeroPhoneBoxEngine;
  }

  // Run on DOM ready
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => window.JeroPhoneBox.initAll());
  } else {
    window.JeroPhoneBox.initAll();
  }
})();
</script>
@endonce

<script>
(function() {
  function boot() {
    if (window.JeroPhoneBox && window.JeroPhoneBox.init) {
      window.JeroPhoneBox.init('{{ $id }}_wrapper');
    } else if (window.getJeroFlagSvg) {
      var f = document.getElementById('{{ $id }}_flag');
      if (f && !f.innerHTML) f.innerHTML = window.getJeroFlagSvg('{{ $initialIso }}');
    }
  }
  boot();
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', boot);
  }
})();
</script>
