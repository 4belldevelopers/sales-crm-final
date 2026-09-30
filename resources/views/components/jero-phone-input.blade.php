{{-- 
  JERO Enterprise Mobile Number Box Blade Component (V6.0)
  Zero Keystroke DB Queries | Metronic 15.6" Screen Fit | Anti-Clipping Portal
  Self-Contained & Isolated: Safely auto-imports its assets once per page.
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

    $rawCode = trim((string) old($countryCodeName, $countryCode));
    $upperRaw = strtoupper($rawCode);

    if (isset($isoToDial[$upperRaw])) {
        $initialCode = $isoToDial[$upperRaw];
    } elseif ($rawCode && preg_match('/^\+?\d+$/', $rawCode)) {
        $initialCode = str_starts_with($rawCode, '+') ? $rawCode : ('+' . $rawCode);
    } else {
        $initialCode = $isoToDial[$tenantHqIso] ?? '+91';
    }

    $initialValue = old($name, $value);
    
    // Quick SSR Flag Map for immediate render without JS flash
    $flagMap = [
        '+91' => '🇮🇳', '+1' => '🇺🇸', '+971' => '🇦🇪', '+44' => '🇬🇧', 
        '+65' => '🇸🇬', '+61' => '🇦🇺', '+86' => '🇨🇳', '+49' => '🇩🇪',
        '+33' => '🇫🇷', '+966' => '🇸🇦', '+974' => '🇶🇦', '+965' => '🇰🇼',
        '+968' => '🇴🇲', '+973' => '🇧🇭', '+60' => '🇲🇾', '+63' => '🇵🇭',
        '+62' => '🇮🇩', '+64' => '🇳🇿', '+27' => '🇿🇦', '+353' => '🇮🇪',
        '+31' => '🇳🇱', '+46' => '🇸🇪', '+41' => '🇨🇭', '+34' => '🇪🇸',
        '+39' => '🇮🇹', '+55' => '🇧🇷', '+52' => '🇲🇽', '+81' => '🇯🇵',
        '+82' => '🇰🇷', '+92' => '🇵🇰', '+880' => '🇧🇩', '+94' => '🇱🇰',
        '+977' => '🇳🇵', '+234' => '🇳🇬', '+254' => '🇰🇪', '+20' => '🇪🇬',
        '+90' => '🇹🇷', '+48' => '🇵🇱', '+40' => '🇷🇴', '+380' => '🇺🇦',
        '+84' => '🇻🇳', '+66' => '🇹🇭', '+852' => '🇭🇰', '+886' => '🇹🇼'
    ];
    $initialFlag = $flagMap[$initialCode] ?? '🌐';
@endphp

<div class="jero-phone-box-wrapper w-100 {{ $class }}" 
     id="{{ $id }}_wrapper"
     data-field-id="{{ $id }}"
     data-default-iso="{{ $tenantHqIso }}">
    
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
                onclick="event.preventDefault(); event.stopPropagation(); if (window.JeroPhoneBox && window.JeroPhoneBox.toggleDropdown) { window.JeroPhoneBox.toggleDropdown('{{ $id }}'); }"
                style="height: 38px; border: 1px solid #e4e6ef;">
            <span class="jero-selected-flag me-1 fs-5" id="{{ $id }}_flag">{{ $initialFlag }}</span>
            <span class="jero-selected-dial fs-7 fw-bold text-gray-800 me-1" id="{{ $id }}_dial">{{ $initialCode }}</span>
            <i class="ki-duotone ki-down fs-8 text-gray-500"></i>
        </button>

        <!-- Main Phone Digits Input -->
        <input type="tel" 
               name="{{ $name }}" 
               id="{{ $id }}" 
               value="{{ $initialValue }}"
               placeholder="{{ $placeholder }}"
               class="form-control form-control-solid fs-7 ps-3 pe-15 rounded-start-0"
               style="height: 38px; border-left: 0;"
               autocomplete="tel-national"
               inputmode="numeric"
               {{ $required ? 'required' : '' }}>

        <!-- Real-Time Visual Digit Counter Badge -->
        <span class="position-absolute end-0 me-3 badge bg-light-primary text-primary fs-8 fw-semibold jero-digit-counter" 
              id="{{ $id }}_counter"
              style="pointer-events: none; z-index: 5;">
            0/10
        </span>
    </div>

    <!-- Client-side field feedback / hint -->
    <div class="jero-phone-feedback fs-8 text-muted mt-1" id="{{ $id }}_hint"></div>
</div>

@once
    <link rel="stylesheet" href="{{ asset('assets/css/telephony/jero-phone-box.css') }}">
    <script src="{{ asset('assets/js/telephony/countries-data.js') }}"></script>
    <script src="{{ asset('assets/js/telephony/jero-phone-box.js') }}"></script>
@endonce

<script>
(function() {
    function initPhoneBox() {
        if (typeof window.JeroPhoneBox !== 'undefined') {
            window.JeroPhoneBox.init('{{ $id }}_wrapper');
        }
    }
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initPhoneBox);
    } else {
        initPhoneBox();
    }
})();
</script>
