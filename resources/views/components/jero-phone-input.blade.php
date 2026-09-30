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
    $initialCode = old($countryCodeName, $countryCode ?: ($tenantHqIso === 'IN' ? '+91' : '+1'));
    if ($initialCode && !str_starts_with($initialCode, '+')) {
        $initialCode = '+' . $initialCode;
    }
    $initialValue = old($name, $value);
    
    // Quick SSR Flag Map for immediate render without JS flash
    $flagMap = [
        '+91' => '🇮🇳', '+1' => '🇺🇸', '+971' => '🇦🇪', '+44' => '🇬🇧', 
        '+65' => '🇸🇬', '+61' => '🇦🇺', '+86' => '🇨🇳', '+49' => '🇩🇪',
        '+33' => '🇫🇷', '+966' => '🇸🇦', '+974' => '🇶🇦', '+965' => '🇰🇼'
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
    if (typeof window.JeroPhoneBox !== 'undefined') {
        window.JeroPhoneBox.init('{{ $id }}_wrapper');
    }
</script>
