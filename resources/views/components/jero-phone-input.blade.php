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
        'VN' => '+84', 'TH' => '+66', 'HK' => '+852', 'TW' => '+886', 'IL' => '+972'
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
        '+66' => 'TH', '+852' => 'HK', '+886' => 'TW', '+972' => 'IL'
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
        '+66' => 9, '+852' => 8, '+886' => 9, '+972' => 9
    ];

    $rawCode = trim((string) old($countryCodeName, $countryCode));
    $upperRaw = strtoupper($rawCode);

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

    <div class="jero-phone-input-group position-relative d-flex align-items-center">
        <!-- Hidden input for Country Code (e.g. +91) submitted with form -->
        <input type="hidden" 
               name="{{ $countryCodeName }}" 
               id="{{ $resolvedCountryCodeId }}" 
               class="jero-phone-country-code-input"
               value="{{ $initialCode }}">

        <!-- Country Selector Trigger Button -->
        <button type="button" 
                class="btn btn-sm btn-icon-flex jero-country-picker-btn d-flex align-items-center bg-light-secondary border-end-0 rounded-end-0 px-3"
                id="{{ $id }}_btn"
                aria-haspopup="listbox"
                aria-expanded="false"
                style="height: 38px; border: 1px solid #e4e6ef; cursor: pointer; user-select: none;">
            <span class="jero-selected-flag me-2 d-inline-flex align-items-center" id="{{ $id }}_flag">
                <img src="https://flagcdn.com/w20/{{ strtolower($initialIso) }}.png" width="20" height="15" alt="{{ $initialIso }}" class="rounded-1 shadow-xs" style="object-fit: cover; vertical-align: middle;">
            </span>
            <span class="jero-selected-dial fs-7 fw-bold text-gray-800 me-1" id="{{ $id }}_dial">{{ $initialCode }}</span>
            <i class="fa fa-chevron-down text-gray-500 ms-1" style="font-size: 10px;"></i>
        </button>

        <!-- Main Phone Digits Input -->
        <input type="tel" 
               name="{{ $name }}" 
               id="{{ $id }}" 
               value="{{ $initialValue }}"
               placeholder="{{ $placeholder }}"
               class="form-control form-control-solid fs-7 ps-3 pe-15 rounded-start-0"
               style="height: 38px; border-left: 0;"
               maxlength="{{ $initialLength }}"
               autocomplete="tel-national"
               inputmode="numeric"
               {{ $required ? 'required' : '' }}>

        <!-- Real-Time Visual Digit Counter Badge -->
        <span class="position-absolute end-0 me-3 badge {{ $initialDigitsCount === $initialLength && $initialLength > 0 ? 'bg-light-success text-success' : 'bg-light-primary text-primary' }} fs-8 fw-semibold jero-digit-counter"
              id="{{ $id }}_counter"
              style="pointer-events: none; z-index: 5;">
            {{ $initialDigitsCount }}/{{ $initialLength }}
        </span>
    </div>

    <!-- IN-PLACE POPUP DROPDOWN (Direct Child of Wrapper, Scoped & 100% Collision Safe) -->
    <div class="jero-country-dropdown card shadow-lg border"
         id="{{ $id }}_dropdown"
         style="display: none; position: absolute; top: calc(100% + 4px); left: 0; z-index: 1060; width: 340px; max-height: 360px; overflow: hidden; border-radius: 0.625rem; box-shadow: 0 10px 30px rgba(0,0,0,0.18); background: #ffffff; border: 1px solid #dce3ee;">

        <!-- Search Header -->
        <div class="p-2 border-bottom bg-light d-flex align-items-center" style="border-top-left-radius: 0.625rem; border-top-right-radius: 0.625rem;">
            <i class="fa fa-search text-gray-500 ms-2 me-2 fs-7" aria-hidden="true"></i>
            <input type="text"
                   class="form-control form-control-sm form-control-solid fs-7 border-0 bg-white jero-search-input"
                   placeholder="Search country or code (e.g. India, +91)..."
                   id="{{ $id }}_search"
                   autocomplete="off"
                   spellcheck="false"
                   style="outline: none; box-shadow: none;">
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
}
.jero-country-picker-btn {
  background-color: #f5f8fa !important;
  border: 1px solid #e4e6ef !important;
  border-right: none !important;
  color: #181c32 !important;
  font-weight: 600 !important;
  height: 38px !important;
  transition: background-color 0.2s ease, border-color 0.2s ease;
  white-space: nowrap;
}
.jero-country-picker-btn:hover,
.jero-country-picker-btn:focus {
  background-color: #eef3f7 !important;
  border-color: #b5b5c3 !important;
  outline: none !important;
}
.jero-phone-input-group .form-control {
  height: 38px !important;
  border: 1px solid #e4e6ef !important;
  border-left: none !important;
  background-color: #f5f8fa !important;
  color: #181c32 !important;
  font-size: 13px !important;
  font-weight: 500 !important;
  letter-spacing: 0.3px;
  padding-right: 70px !important;
}
.jero-phone-input-group .form-control:focus {
  background-color: #ffffff !important;
  border-color: #009ef7 !important;
  box-shadow: none !important;
}
.jero-digit-counter {
  top: 50%;
  transform: translateY(-50%);
  font-size: 11px !important;
  padding: 3px 6px !important;
  letter-spacing: 0.2px;
  pointer-events: none;
  z-index: 4;
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
    { iso: "UZ", name: "Uzbekistan", dial: "+998", len: 9 }
  ];

  window.setupJeroPhoneBox = function(wrapper) {
    if (!wrapper || wrapper._jeroInitialized) return;
    wrapper._jeroInitialized = true;

    const btn = wrapper.querySelector('.jero-country-picker-btn');
    const dropdown = wrapper.querySelector('.jero-country-dropdown');
    const searchInput = wrapper.querySelector('.jero-search-input');
    const countryList = wrapper.querySelector('.jero-country-list');
    const flagSpan = wrapper.querySelector('.jero-selected-flag');
    const dialSpan = wrapper.querySelector('.jero-selected-dial');
    const hiddenCode = wrapper.querySelector('.jero-phone-country-code-input') || wrapper.querySelector('input[type="hidden"]');
    const input = wrapper.querySelector('input[type="tel"]');
    const counter = wrapper.querySelector('.jero-digit-counter');
    const defaultIso = (wrapper.getAttribute('data-default-iso') || 'IN').toUpperCase();

    if (!btn || !dropdown || !searchInput || !countryList || !input || !hiddenCode) return;

    let currentCountry = null;
    let selectedIndex = -1;

    // Resolve initial country
    let initialDial = (hiddenCode.value || '').trim();
    if (initialDial) {
      if (initialDial.length === 2 && /^[a-zA-Z]{2}$/.test(initialDial)) {
        currentCountry = window.__JERO_COUNTRIES_DB__.find(c => c.iso === initialDial.toUpperCase());
      } else {
        const clean = initialDial.startsWith('+') ? initialDial : ('+' + initialDial);
        currentCountry = window.__JERO_COUNTRIES_DB__.find(c => c.dial === clean && c.iso === defaultIso) ||
                         window.__JERO_COUNTRIES_DB__.find(c => c.dial === clean);
      }
    }
    if (!currentCountry) {
      currentCountry = window.__JERO_COUNTRIES_DB__.find(c => c.iso === defaultIso) || window.__JERO_COUNTRIES_DB__[0];
    }

    function updateCounter(targetLength) {
      if (!counter) return;
      const currentDigits = (input.value || '').replace(/[^\d]/g, '');
      const len = targetLength || (currentCountry ? currentCountry.len : 10);
      counter.textContent = `${currentDigits.length}/${len}`;
      if (currentDigits.length === 0) {
        counter.className = 'position-absolute end-0 me-3 badge bg-light-primary text-primary fs-8 fw-semibold jero-digit-counter';
      } else if (currentDigits.length === len) {
        counter.className = 'position-absolute end-0 me-3 badge bg-light-success text-success fs-8 fw-bold jero-digit-counter';
        counter.innerHTML = `${currentDigits.length}/${len} <i class="fa fa-check text-success ms-1" style="font-size:9px;"></i>`;
      } else {
        counter.className = 'position-absolute end-0 me-3 badge bg-light-warning text-warning fs-8 fw-semibold jero-digit-counter';
      }
    }

    function selectCountry(c) {
      if (!c) return;
      currentCountry = c;
      hiddenCode.value = c.dial;
      dialSpan.textContent = c.dial;
      flagSpan.innerHTML = `<img src="https://flagcdn.com/w20/${c.iso.toLowerCase()}.png" width="20" height="15" alt="${c.iso}" class="rounded-1 shadow-xs" style="object-fit:cover; vertical-align:middle;">`;
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
      const q = query.trim().toLowerCase();
      const qDigits = q.replace(/[^\d]/g, '');
      const filtered = window.__JERO_COUNTRIES_DB__.filter(c => {
        if (!q) return true;
        return c.name.toLowerCase().includes(q) ||
               c.iso.toLowerCase() === q ||
               c.dial.includes(q) ||
               (qDigits && c.dial.replace(/[^\d]/g, '').includes(qDigits));
      });

      if (filtered.length === 0) {
        countryList.innerHTML = `<div class="p-4 text-center text-muted fs-8">No country matching "${query}"</div>`;
        return;
      }

      let html = '';
      filtered.forEach((c, index) => {
        const isSelected = currentCountry && currentCountry.iso === c.iso;
        html += `
          <div class="d-flex align-items-center justify-content-between px-3 py-2 cursor-pointer jero-country-item ${isSelected ? 'bg-light-primary text-primary fw-bold' : 'text-gray-800'}"
               data-dial="${c.dial}"
               data-iso="${c.iso}"
               data-index="${index}"
               role="option"
               aria-selected="${isSelected ? 'true' : 'false'}"
               style="font-size: 13px; border-bottom: 1px solid #f4f4f7; transition: background-color 0.15s ease; user-select: none;">
            <div class="d-flex align-items-center text-truncate me-2">
              <img src="https://flagcdn.com/w20/${c.iso.toLowerCase()}.png" width="20" height="15" alt="${c.iso}" class="me-2 rounded-1 shadow-xs" style="object-fit:cover; vertical-align:middle;">
              <span class="text-truncate">${c.name}</span>
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

    // Search filter input
    searchInput.addEventListener('input', function(e) {
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

    // Initial counter render
    updateCounter(currentCountry ? currentCountry.len : 10);
  };

  window.JeroPhoneBox = {
    init(wrapperOrId) {
      const el = typeof wrapperOrId === 'string' ? document.getElementById(wrapperOrId) : wrapperOrId;
      if (el) window.setupJeroPhoneBox(el);
    },
    initAll() {
      document.querySelectorAll('.jero-phone-box-wrapper').forEach(w => window.setupJeroPhoneBox(w));
    }
  };

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
    }
  }
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', boot);
  } else {
    boot();
  }
})();
</script>
