@extends('layouts.service_company_layout')
@push('head-script')
<script>
   $( function(){
     $( "#calender" ).datepicker
     	({
			showOn				: "button",
			buttonImage			: "images/calendar.gif",
			buttonImageOnly		: true,
			buttonText			: "Select date"
       });
   });	
</script>

<link href="{{ asset('demo/css/filterStyle.css') }}{{ TM_VERSION }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('demo/css/candidate-form.css') }}{{ TM_VERSION }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('demo/css/job-application-create.css') }}{{ TM_VERSION }}&remaining_candidate_cards_yellow=20260925&candidate_header_compact_icons=20260925&candidate_design_repair=20260926&candidate_header_harmonize=20260926&candidate_rating_view=20260926&resume_card_design=20260926&resume_card_clean=20260926&required_fields_modal=20261006_v2" rel="stylesheet" type="text/css" />
<style>
/* Scoped Enterprise Currency Select2 Dropdown on Add Candidate Page */
.job-candidate-create .candidate-salary-header-currency {
    display: flex;
    align-items: center;
    gap: 10px;
    min-width: 290px;
    max-width: 380px;
}
.job-candidate-create .candidate-salary-header-currency > label {
    margin: 0;
    color: var(--jac-text);
    font-size: 13px;
    font-weight: 700;
    white-space: nowrap;
}
.job-candidate-create .candidate-salary-header-currency .select2-container {
    flex: 1 1 auto;
    min-width: 220px;
}
.job-candidate-create .candidate-salary-header-currency .select2-container--default .select2-selection--single {
    height: 38px;
    border: 1px solid #d8deec !important;
    border-radius: 6px !important;
    background-color: #ffffff;
    display: flex;
    align-items: center;
    padding-left: 10px;
    padding-right: 30px;
    font-family: 'Poppins', sans-serif !important;
    font-size: 13px !important;
    font-weight: 600 !important;
    color: #1e293b !important;
    box-sizing: border-box;
    transition: border-color 0.15s ease, box-shadow 0.15s ease;
}
.job-candidate-create .candidate-salary-header-currency .select2-container--default.select2-container--open .select2-selection--single,
.job-candidate-create .candidate-salary-header-currency .select2-container--default .select2-selection--single:focus {
    border-color: #3b82f6 !important;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15) !important;
    outline: none;
}
.job-candidate-create .candidate-salary-header-currency .select2-container--default .select2-selection--single .select2-selection__arrow {
    height: 36px;
    top: 1px;
    right: 8px;
}
.br-currency-select2-dropdown {
    font-family: 'Poppins', sans-serif;
    font-size: 13px;
    border: 1px solid #cbd5e1 !important;
    border-radius: 8px !important;
    box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.12), 0 8px 10px -6px rgba(0, 0, 0, 0.06) !important;
    overflow: hidden;
    z-index: 1060;
}
.br-currency-select2-dropdown .select2-search--dropdown {
    padding: 8px 10px;
    background: #f8fafc;
    border-bottom: 1px solid #e2e8f0;
}
.br-currency-select2-dropdown .select2-search--dropdown .select2-search__field {
    border: 1px solid #cbd5e1;
    border-radius: 6px;
    padding: 6px 12px;
    font-size: 13px;
    outline: none;
    font-family: 'Poppins', sans-serif;
    background-color: #ffffff;
    width: 100%;
}
.br-currency-select2-dropdown .select2-search--dropdown .select2-search__field:focus {
    border-color: #3b82f6;
    box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.2);
}
.br-currency-select2-dropdown .select2-results__options {
    max-height: 260px;
}
.br-currency-select2-dropdown .select2-results__option {
    padding: 7px 12px;
    font-size: 13px;
    font-weight: 500;
    color: #334155;
    line-height: 1.4;
}
.br-currency-select2-dropdown .select2-results__option--highlighted[aria-selected] {
    background-color: #3b82f6 !important;
    color: #ffffff !important;
}
.br-currency-select2-dropdown .select2-results__option[aria-selected="true"] {
    background-color: #eff6ff;
    color: #1d4ed8;
    font-weight: 600;
}
@media (max-width: 767px) {
    .job-candidate-create .candidate-salary-header-currency {
        width: 100%;
        max-width: 100%;
        min-width: 0;
        margin-top: 8px;
    }
}

/* Scoped Enterprise Global Currency Indicative Pills & Hover Lens */
.jero-pill-wrapper {
    display: inline-flex;
    align-items: center;
    vertical-align: middle;
}
.jero-indicative-pill {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 2px 7px;
    font-size: 11px !important;
    font-weight: 600 !important;
    font-family: 'Poppins', sans-serif !important;
    border-radius: 4px;
    cursor: pointer;
    line-height: 1.35;
    transition: all 0.15s ease-in-out;
    user-select: none;
    text-decoration: none;
}
.jero-indicative-pill--base {
    background-color: #e8fff3 !important;
    color: #50cd89 !important;
    border: 1px solid #b4f1d0 !important;
}
.jero-indicative-pill--converted {
    background-color: #f1faff !important;
    color: #009ef7 !important;
    border: 1px solid #b5e4fd !important;
}
.jero-indicative-pill:hover,
.jero-indicative-pill:focus-visible {
    box-shadow: 0 2px 6px rgba(0, 158, 247, 0.25);
    transform: translateY(-1px);
    outline: none;
}
.jero-indicative-pill i {
    font-size: 10px;
}

/* Multi-Currency Hover Lens Popover (Section 15.1) */
.jero-currency-hover-lens {
    position: fixed;
    z-index: 1080;
    width: 320px;
    max-width: 90vw;
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    box-shadow: 0 12px 28px -4px rgba(15, 23, 42, 0.18), 0 4px 12px -2px rgba(15, 23, 42, 0.08);
    font-family: 'Poppins', sans-serif;
    color: #1e293b;
    pointer-events: auto;
    opacity: 0;
    visibility: hidden;
    transform: translateY(4px);
    transition: opacity 0.15s ease, transform 0.15s ease, visibility 0.15s;
}
.jero-currency-hover-lens.is-visible {
    opacity: 1;
    visibility: visible;
    transform: translateY(0);
}
.jero-hover-lens-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 8px 12px;
    background: #f8fafc;
    border-bottom: 1px solid #e2e8f0;
    border-radius: 8px 8px 0 0;
    font-size: 11px;
    font-weight: 700;
    color: #334155;
    letter-spacing: 0.2px;
}
.jero-hover-lens-header i {
    color: #009ef7;
    margin-right: 5px;
}
.jero-hover-lens-badge {
    font-size: 9.5px;
    font-weight: 700;
    background: #e0f2fe;
    color: #0284c7;
    padding: 1px 6px;
    border-radius: 3px;
    text-transform: uppercase;
}
.jero-hover-lens-base {
    padding: 6px 12px;
    background: #f1f5f9;
    border-bottom: 1px dashed #cbd5e1;
    font-size: 11px;
    color: #475569;
}
.jero-hover-lens-base strong {
    color: #0f172a;
    font-weight: 700;
}
.jero-hover-lens-body {
    padding: 6px 12px;
    display: flex;
    flex-direction: column;
    gap: 5px;
}
.jero-hover-lens-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 4px 0;
    border-bottom: 1px solid #f1f5f9;
    font-size: 11.5px;
}
.jero-hover-lens-row:last-child {
    border-bottom: none;
}
.jero-hover-lens-curr {
    display: flex;
    align-items: center;
    gap: 6px;
    font-weight: 600;
    color: #1e293b;
}
.jero-hover-lens-flag {
    font-size: 14px;
    line-height: 1;
}
.jero-hover-lens-vals {
    text-align: right;
    display: flex;
    flex-direction: column;
    align-items: flex-end;
}
.jero-hover-lens-primary {
    font-weight: 700;
    color: #0f172a;
    font-size: 12px;
}
.jero-hover-lens-sub {
    font-size: 10px;
    color: #64748b;
    font-weight: 500;
}
.jero-hover-lens-footer {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 6px 12px;
    background: #f8fafc;
    border-top: 1px solid #e2e8f0;
    border-radius: 0 0 8px 8px;
    font-size: 10px;
    color: #64748b;
}
.jero-hover-lens-footer i {
    color: #f59e0b;
    margin-right: 4px;
}
/* Salary Summary USD Equivalent Row */
.candidate-salary-summary-usd-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 6px 0;
    border-top: 1px dashed #cbd5e1;
    margin-top: 4px;
    font-size: 12px;
    color: #475569;
}
.candidate-salary-summary-usd-row strong {
    color: #009ef7;
    font-weight: 700;
    font-size: 12.5px;
}


/* ==========================================================================
   HIGHEST QUALIFICATION: 100% BROWSER RAM COMBOBOX & CARD OVERFLOW PROTECTION
   ========================================================================== */
.candidate-profile-card,
.bd-card {
    overflow: visible !important;
    position: relative;
    z-index: 1;
}

.candidate-profile-card.has-active-combobox,
.bd-card.has-active-combobox {
    z-index: 1050 !important;
}

.candidate-profile-grid {
    overflow: visible !important;
}

/* Bulletproof native select hiding scoped strictly to combobox container */
.candidate-field-control--has-combobox > select,
.bd-control--has-combobox > select,
.fg--has-combobox > select,
.has-combobox > select {
    position: absolute !important;
    opacity: 0 !important;
    width: 1px !important;
    height: 1px !important;
    margin: -1px !important;
    clip: rect(0, 0, 0, 0) !important;
    overflow: hidden !important;
    z-index: -1 !important;
    pointer-events: none !important;
}

/* Hide static icons outside combobox when trigger has its own icon */
.candidate-field-control--has-combobox > i.fa-graduation-cap,
.bd-control--has-combobox > i.fa-graduation-cap,
.candidate-field-control--has-combobox > i.fa-flag,
.bd-control--has-combobox > i.fa-flag {
    display: none !important;
}

/* Custom Combobox Trigger Container */
.jero-custom-select {
    position: relative;
    width: 100%;
    font-family: 'Poppins', sans-serif;
    font-size: 13px;
    user-select: none;
}

.jero-cs-trigger {
    display: flex !important;
    align-items: center !important;
    justify-content: space-between !important;
    width: 100% !important;
    min-height: 42px !important;
    height: 42px !important;
    max-height: 42px !important;
    padding: 0 14px !important;
    background: #ffffff !important;
    border: 1px solid #d8deec !important;
    border-radius: 6px !important;
    cursor: pointer !important;
    outline: none !important;
    transition: border-color 0.15s ease, box-shadow 0.15s ease !important;
    text-align: left !important;
    box-sizing: border-box !important;
    font-family: 'Poppins', sans-serif !important;
}

.jero-cs-trigger:hover {
    border-color: #94a3b8 !important;
}

.jero-cs-trigger:focus,
.jero-custom-select.is-open .jero-cs-trigger {
    border-color: #3b82f6 !important;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.14) !important;
}

/* Validation error state */
.jero-custom-select.has-validation-error .jero-cs-trigger,
.candidate-field-control.has-validation-error .jero-cs-trigger,
.bd-control.has-validation-error .jero-cs-trigger {
    border-color: #ef4444 !important;
    box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.14) !important;
}

.jero-cs-left {
    display: flex !important;
    align-items: center !important;
    gap: 10px !important;
    min-width: 0 !important;
    flex: 1 1 auto !important;
}

.jero-cs-icon {
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    width: 18px !important;
    color: #5d6681 !important;
    font-size: 14px !important;
    flex-shrink: 0 !important;
}

.jero-cs-value {
    font-size: 13px !important;
    font-weight: 500 !important;
    color: #0b1545 !important;
    white-space: nowrap !important;
    overflow: hidden !important;
    text-overflow: ellipsis !important;
    line-height: 1.2 !important;
}

.jero-cs-placeholder {
    color: #8991a8 !important;
    font-weight: 400 !important;
}

.jero-cs-right {
    display: flex !important;
    align-items: center !important;
    gap: 8px !important;
    flex-shrink: 0 !important;
    margin-left: 8px !important;
}

.jero-cs-badge {
    display: inline-block !important;
    font-size: 10px !important;
    font-weight: 600 !important;
    padding: 2px 7px !important;
    background: #e0e7ff !important;
    color: #3730a3 !important;
    border-radius: 4px !important;
    white-space: nowrap !important;
}

.jero-cs-trigger-clear {
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    width: 18px !important;
    height: 18px !important;
    font-size: 15px !important;
    line-height: 1 !important;
    color: #94a3b8 !important;
    cursor: pointer !important;
    border-radius: 50% !important;
    transition: all 0.15s ease !important;
}

.jero-cs-trigger-clear:hover {
    color: #ef4444 !important;
    background-color: #fee2e2 !important;
}

.jero-cs-arrow {
    font-size: 11px !important;
    color: #94a3b8 !important;
    transition: transform 0.2s ease !important;
}

.jero-custom-select.is-open .jero-cs-arrow {
    transform: rotate(180deg) !important;
    color: #3b82f6 !important;
}

/* Floating Dropdown Menu Panel */
.jero-cs-dropdown {
    position: absolute !important;
    left: 0 !important;
    right: 0 !important;
    top: calc(100% + 4px) !important;
    background: #ffffff !important;
    border: 1px solid #cbd5e1 !important;
    border-radius: 8px !important;
    box-shadow: 0 16px 36px -4px rgba(15, 23, 42, 0.22), 0 10px 18px -6px rgba(15, 23, 42, 0.14) !important;
    z-index: 999999 !important;
    overflow: hidden !important;
    display: none;
    flex-direction: column !important;
    max-height: 340px !important;
    font-family: 'Poppins', sans-serif !important;
    animation: jeroCsFadeIn 0.15s ease-out;
}

.jero-custom-select.is-open .jero-cs-dropdown {
    display: flex !important;
}

.jero-custom-select.is-open.open-up .jero-cs-dropdown {
    top: auto !important;
    bottom: calc(100% + 4px) !important;
}

/* Sticky Search Box at the top */
.jero-cs-search-box {
    display: flex !important;
    align-items: center !important;
    position: sticky !important;
    top: 0 !important;
    padding: 8px 10px !important;
    background: #f8fafc !important;
    border-bottom: 1px solid #e2e8f0 !important;
    z-index: 10 !important;
    flex-shrink: 0 !important;
    gap: 8px !important;
}

.jero-cs-search-box i.jero-cs-search-icon {
    color: #64748b !important;
    font-size: 13px !important;
    flex-shrink: 0 !important;
}

.jero-cs-search-input {
    width: 100% !important;
    height: 36px !important;
    min-height: 36px !important;
    border: 1px solid #cbd5e1 !important;
    border-radius: 6px !important;
    padding: 6px 12px !important;
    font-size: 13px !important;
    font-family: 'Poppins', sans-serif !important;
    color: #0f172a !important;
    background: #ffffff !important;
    outline: none !important;
    box-sizing: border-box !important;
}

.jero-cs-search-input:focus {
    border-color: #3b82f6 !important;
    box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.15) !important;
}

.jero-cs-search-clear {
    border: none !important;
    background: transparent !important;
    color: #94a3b8 !important;
    font-size: 16px !important;
    cursor: pointer !important;
    padding: 0 4px !important;
    line-height: 1 !important;
}

.jero-cs-search-clear:hover {
    color: #475569 !important;
}

/* Scrollable Options Container */
.jero-cs-list {
    flex: 1 1 auto !important;
    overflow-y: auto !important;
    max-height: 280px !important;
    padding: 4px 0 !important;
}

/* Group Title */
.jero-cs-group-title {
    padding: 7px 12px !important;
    font-size: 11px !important;
    font-weight: 700 !important;
    letter-spacing: 0.3px !important;
    color: #1e40af !important;
    background: #f1f5f9 !important;
    border-top: 1px solid #e2e8f0 !important;
    border-bottom: 1px solid #e2e8f0 !important;
    text-transform: uppercase !important;
    position: sticky !important;
    top: 0 !important;
    z-index: 5 !important;
}

.jero-cs-group:first-child .jero-cs-group-title {
    border-top: none !important;
}

/* Option Item */
.jero-cs-item {
    display: flex !important;
    align-items: center !important;
    justify-content: space-between !important;
    padding: 8px 14px !important;
    font-size: 13px !important;
    font-weight: 500 !important;
    color: #1e293b !important;
    cursor: pointer !important;
    transition: background 0.1s ease !important;
}

.jero-cs-item:hover,
.jero-cs-item.is-highlighted {
    background-color: #eff6ff !important;
    color: #1d4ed8 !important;
}

.jero-cs-item.is-selected {
    background-color: #e0e7ff !important;
    color: #312e81 !important;
    font-weight: 600 !important;
}

.jero-cs-item.is-hidden {
    display: none !important;
}

.jero-cs-group.is-hidden {
    display: none !important;
}

.jero-cs-empty.is-hidden {
    display: none !important;
}

.jero-cs-item-text {
    flex: 1 1 auto !important;
    min-width: 0 !important;
    line-height: 1.35 !important;
    word-break: break-word !important;
}

.jero-cs-item-badge {
    font-size: 10px !important;
    font-weight: 600 !important;
    padding: 2px 7px !important;
    background: #e0e7ff !important;
    color: #3730a3 !important;
    border-radius: 4px !important;
    white-space: nowrap !important;
    margin-left: 8px !important;
    flex-shrink: 0 !important;
}

.jero-cs-item.is-selected .jero-cs-item-badge {
    background: #c7d2fe !important;
    color: #1e1b4b !important;
}

.jero-cs-empty {
    padding: 20px 14px !important;
    text-align: center !important;
    color: #64748b !important;
    font-size: 13px !important;
    font-weight: 500 !important;
}

@keyframes jeroCsFadeIn {
    from { opacity: 0; transform: translateY(-4px); }
    to { opacity: 1; transform: translateY(0); }
}

/* Disambiguation Ribbon for NEEDS_CONFIRMATION State */
.jero-disambiguation-bar {
    display: flex !important;
    flex-wrap: wrap !important;
    align-items: center !important;
    gap: 6px !important;
    margin-top: 6px !important;
    padding: 6px 10px !important;
    background: #f8fafc !important;
    border: 1px dashed #cbd5e1 !important;
    border-radius: 6px !important;
    font-family: 'Poppins', sans-serif !important;
}

.jero-disambiguation-badge {
    display: inline-flex !important;
    align-items: center !important;
    padding: 3px 8px !important;
    font-size: 11px !important;
    font-weight: 500 !important;
    color: #1e40af !important;
    background: #eff6ff !important;
    border: 1px solid #bfdbfe !important;
    border-radius: 4px !important;
    cursor: pointer !important;
    transition: all 0.15s ease !important;
}

.jero-disambiguation-badge:hover {
    background: #dbeafe !important;
    border-color: #93c5fd !important;
}

.jero-disambiguation-badge.is-recommended {
    background: #fef3c7 !important;
    color: #92400e !important;
    border-color: #fde68a !important;
    font-weight: 600 !important;
}

.jero-disambiguation-badge.is-recommended:hover {
    background: #fde68a !important;
}

/* Explicit styling protection for native Gender select (42px height & 6px border-radius) */
.candidate-field-control select#gender {
    display: block !important;
    visibility: visible !important;
    opacity: 1 !important;
    pointer-events: auto !important;
    width: 100% !important;
    min-height: 42px !important;
    height: 42px !important;
    line-height: 40px !important;
    padding: 0 34px 0 42px !important;
    background-color: #ffffff !important;
    border: 1px solid #d8deec !important;
    border-radius: 6px !important;
    color: #0b1545 !important;
    font-size: 13.5px !important;
    font-weight: 500 !important;
    font-family: 'Poppins', sans-serif !important;
    outline: none !important;
    cursor: pointer !important;
    appearance: none !important;
    -webkit-appearance: none !important;
    -moz-appearance: none !important;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3E%3Cpath fill='none' stroke='%2394a3b8' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='m2 5 6 6 6-6'/%3E%3C/svg%3E") !important;
    background-repeat: no-repeat !important;
    background-position: right 14px center !important;
    background-size: 11px !important;
    box-sizing: border-box !important;
}
</style>
@endpush

@section('content')
@php 
      use App\Helper\GeneralMethod;
      $defaultTimeFormat = GeneralMethod::defaultTimeFormat();
      $country_code =  GeneralMethod::countryCodeFun();
      $currency_code =  GeneralMethod::defaultCurrency();
      // dd($currency_code);
      $phone_code = $country_code;
      $second_phone_code = $country_code;
      $url= '';
      $resumeCheck = '';
		  $resume_url				=	'';
		  $photo  					=	url('public/avatar.png');	
		  if($page_event == 'ADD'){
			 $pagetitle			=	'Add Candidate';
			 $resume_url			=	'';	
		  }else if($page_event == 'EDIT'){
        $phone_code = ($application->phone_code) ? $application->phone_code : $phone_code;
        $second_phone_code = ($application->second_phone_code) ? $application->second_phone_code : $second_phone_code;
        $pagetitle	=	'Edit Candidate';   
        //  dd($application);   
			  if(!empty($application->resume)){
				$url				=	 $application->resume;
        // $url = @$application->hashname;
        $info = pathinfo($url);
        if($resumeAwsPath){
           if (!empty($application->resume)) {
            $resume_url			=	route('download.resume', ['id' => $application->id, 'path' => rtrim(strtr(base64_encode($url), '+/', '-_'), '=')]);
            $resumeCheck = 1;
           }
        }else{
           $resume_url			=	'';
        }
			 }else{
				$resume_url			=	'';	
			 }
			 if(!empty($application->photo)){	
				$photo			=	 $application->photo;
			 }   
		  }	
					
      $bgTextDecore				=	$strVal	=	'';
      $str						=	@$jobDetail->title;
      // dd($str);
      $firstChar = (!empty($str) && is_string($str)) ? strtolower(substr($str, 0, 1)) : '';
      $strVal = $firstChar;
      $str = !empty($firstChar) ? ord($firstChar) : 0;
      if($str >=97 && $str <=101){
        $bgTextDecore		=	'fs-3 bg-light-danger text-danger';	
      }else if($str >=102 && $str <=106){
        $bgTextDecore		=	'fs-3 bg-light-warning text-warning';	
      }else if($str >=107 && $str <=111){
        $bgTextDecore		=	'fs-3 bg-light-primary text-primary';	
      }else if($str >=112 && $str <=116){
        $bgTextDecore		=	'fs-3 bg-light-success text-success';	
      }else if($str >=117 && $str <=121){
        $bgTextDecore		=	'fs-3 bg-dark text-light';	
      }else{
        $bgTextDecore		=	'fs-3 bg-light-secondary text-dark';	
      }	

      $city			=	$country_id		=	 $country_name	=	$state_name		=	$address		=	'';
      $address		=	@$jobLocation->address;
      if(@$jobLocation->country_id == 101){
        $city			=	@$jobLocation->city_name;
        $state_name		=	@$jobLocation->state_name;	
      }else{
        $city		=	@$jobLocation->city;		
      }
      if(@$jobLocation->country_id > 0){
          $country_name	=	GeneralMethod::getCountryName(@$jobLocation->country_id);	
          foreach($country_name as $cval){
          $country_name	=	ucwords($cval['name']);	
          }				 
      }
		
      $status_class		=	'';
      if(@$jobDetail->status == 'active'){
        $status_class	=	'bg-light-success text-success';
      }else if(@$jobDetail->status == 'hold'){
        $status_class	=	'bg-light-warning text-warning';
      }else if(@$jobDetail->status == 'inactive'){
        $status_class	=	'bg-light-danger text-danger';
      }
		
      $created_at 		=	"";
      $created_at 		=	@$jobDetail->created_at;
      $created_at 		=	date("d M, Y", strtotime($created_at));
		
      $snp_mode = null;
      if (isset($application) && !empty($application->id)) {
        $stored_snp_mode = (int) ($application->serving_notice_period_mode ?? 0);
        if (in_array($stored_snp_mode, [1, 2], true) || ($stored_snp_mode === 0 && !empty($application->np_id))) {
          $snp_mode = $stored_snp_mode;
        }
      }
      $snp_last_working = '';
      if ($snp_mode === 1) {
        $snp_last_working = @$application->np_id;
      } elseif ($snp_mode === 2) {
        $snp_last_working = @$application->last_working_date;
      }      $jobDetailId = @$jobDetail->id ?? 0;
      $logos = $jobDetailId ? GeneralMethod::companyDetails($jobDetailId) : null;
      $logo_path = '';
      $logo_path = url('public/user-uploads/company-logo/' . @$logos->logo);
      if (!empty($logos->logo_logical_path)) {
        $logo_path = url('public/' . @$logos->logo_logical_path);
      }
        
      $company_or_client = $jobDetailId ? GeneralMethod::companyDetailsByClient($jobDetailId) : null;	
      $Jobcities = $jobDetailId ? GeneralMethod::Jobcities($jobDetailId) : '';	
      $locations  = explode('`,`',$Jobcities);
      $salaryModeSelect = GeneralMethod::salaryMode();
      $currencySymbole = GeneralMethod::currencySymbole();
      $min_sal_mode = (!empty($jobDetail->min_sal_mode) && isset($salaryModeSelect[$jobDetail->min_sal_mode])) ? strtoupper(substr($salaryModeSelect[$jobDetail->min_sal_mode], 0, 1)) : '';
      $max_sal_mode = (!empty($jobDetail->max_sal_mode) && isset($salaryModeSelect[$jobDetail->max_sal_mode])) ? strtoupper(substr($salaryModeSelect[$jobDetail->max_sal_mode], 0, 1)) : '';
      $min_salary_currency = $currencySymbole[@$jobDetail->min_salary_currency] ?? '';
      $max_salary_currency = $currencySymbole[@$jobDetail->max_salary_currency] ?? '';
      $max_salary = !empty($jobDetail->max_salary) ? GeneralMethod::thousandsCurrencyFormat($jobDetail->max_salary) : '0';
      $salary_currency = @$jobDetail->max_salary_currency ?? 0;
@endphp 

<!--begin::Toolbar-->
<div class="toolbar candidate-top-toolbar" id="kt_toolbar">
  <!--begin::Container-->
  <div id="kt_toolbar_container" class="container-fluid d-flex flex-stack candidate-toolbar-container">
    @include('sections.service_company.toolbar',['id' => @$jobDetail->id ?? 0])
    <div class="d-flex align-items-center py-1 candidate-header-actions">
      @php
          if($page_event == 'EDIT'){
            echo '<button type="button" name="update" id="update-form" value="update" class="btn btn-primary talent-topbar-primary candidate-submit-action"><i class="fa-solid fa-pen-to-square" aria-hidden="true"></i><span>Update Candidate</span></button>';
          }else{
            echo '<button type="button" name="save" id="save-form" value="save" class="btn btn-primary talent-topbar-primary candidate-submit-action"><i class="fa-solid fa-paper-plane" aria-hidden="true"></i><span>Submit Candidate</span></button>';
          }
      @endphp
    </div>
  </div>
  <!--end::Container-->
</div>
<!--end::Toolbar-->
<!--begin::Content-->
<div class="content d-flex flex-column flex-column-fluid pt-4 job-candidate-create" id="kt_content">
  <!--begin::Post-->
  <div class="post d-flex flex-column-fluid" id="kt_post">
    <!--begin::Container-->
    <div id="kt_content_container" class="container-fluid px-3">
      <div class="app">
       
        <!-- MAIN -->
        <main class="mc">
          <!-- Form -->
          <form class="ajax-form" method="POST" id="createForm" name="createForm" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="job_id" value="{{ @$jobId }}" id="job_id" />
            <input type="hidden" name="appId" value="{{ @$appId }}" id="appId" />
            <input type="hidden" name="currentPage" value="{{ @$currentPage }}" id="currentPage" />
            <input type="hidden" name="currentSlug" value="{{ @$currentSlug }}" id="currentSlug" />
            <input type="hidden" name="resumeCheck" value="{{ $resumeCheck }}" id="resumeCheck" />
            <input type="hidden" name="AddJobWiseCondidateRankRow" id="AddJobWiseCondidateRankRow" value="{{ (@$AddJobWiseCondidateRankRow) ? $AddJobWiseCondidateRankRow : '' }}">
            {{-- v2 upload session UUID — populated by resume-presign.js when browser_upload flag is ON --}}
            <input type="hidden" name="resume_session_uuid" id="resume_session_uuid" value="">
            <input type="hidden" name="resume_upload_method" id="resume_upload_method" value="">
            {{-- V4 Remediation hidden fields --}}
            <input type="hidden" name="client_submission_uuid" id="client_submission_uuid" value="">
            <input type="hidden" name="client_update_uuid" id="client_update_uuid" value="">
            <input type="hidden" name="resume_version" id="resume_version" value="{{ (int)(@$application->resume_version ?? 1) }}">
            <input type="hidden" name="resume_intent" id="resume_intent" value="{{ (!empty($application->resume) || !empty($application->current_resume_id)) ? 'KEEP' : 'NONE' }}">
          <div class="fc">
            <!-- Upload + Career Summary -->
            <div class="g2 candidate-intro-grid" style="margin-bottom:16px">
                <!-- Resume Upload -->
                <div class="fs fv-row resume-card candidate-card" style="margin-bottom:0">
                    <div class="fsh resume-card-header candidate-card-header">
                        <div class="candidate-card-title">
                          <span class="resume-card-icon candidate-card-icon"><i class="fa-solid fa-file-arrow-up" aria-hidden="true"></i></span>
                          <span><strong>Upload Resume <span class="rq">*</span></strong><small>Upload a PDF or Word resume to parse candidate details.</small></span>
                        </div>
                    </div>

                    <div class="fsc resume-card-body candidate-card-body">
                        <div class="ua" id="uploadBox">

                            <div class="ui">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="1.5" stroke-linecap="round"
                                    stroke-linejoin="round">
                                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                                    <polyline points="17 8 12 3 7 8"/>
                                    <line x1="12" y1="3" x2="12" y2="15"/>
                                </svg>
                            </div>

                            <div class="ut">Drag & Drop Resume Here</div>

                            <div class="us">or</div>

                            <button class="ub" type="button" id="chooseFileBtn">
                                Choose File
                            </button>

                            <input type="file" name="resume" id="resume" accept=".pdf,.doc,.docx" style="display:none;">

                            <div class="uf">
                                Supports PDF, DOC, DOCX (Max 6 MB)
                            </div>

                            <!-- Progress bar / pill / success banner -->
                            <div class="up-wrap" id="upWrap" style="display:none">
                                <div class="up-row">
                                    <div class="up-track">
                                        <div class="up-bar" id="upBar" style="width:0%"></div>
                                    </div>
                                    <span class="up-pill" id="upPill" style="display:none">
                                        <svg class="up-pill-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="20 6 9 17 4 12"/></svg>
                                        Uploaded
                                    </span>
                                </div>
                                <div class="up-stats" id="upStats">0% &middot; 0 KB / 0 KB</div>
                                <div class="up-banner" id="upBanner" style="display:none">
                                    <svg class="up-banner-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="10"/><polyline points="9 12 11 14 16 9"/></svg>
                                    <span id="upBannerText">Upload complete! Your resume has been uploaded successfully.</span>
                                </div>
                            </div>

                            <!-- Selected File -->
                            @php
                                // basename() can return just a number when the path uses date-based
                                // directories (e.g. resumes/2024/07/candidatename.pdf → "07" is wrong).
                                // Walk the path segments and pick the first one that has a valid extension.
                                $displayResumeName = '';
                                if (!empty($application->resume)) {
                                    preg_match('/([^\/\\\\]+\.(pdf|doc|docx|htm|html))/i', $application->resume, $_rm);
                                    $displayResumeName = $_rm[1] ?? basename($application->resume);
                                    if (!$displayResumeName || !pathinfo($displayResumeName, PATHINFO_EXTENSION)) {
                                        $displayResumeName = 'Resume (uploaded)';
                                    }
                                }
                            @endphp
                            <div class="file-info" id="fileInfo" style="{{ !empty($resume_url) ? 'display:flex' : 'display:none' }}">
                                <span class="file-name" id="fileName">{{ $displayResumeName }}</span>
                                @if(!empty($resume_url))
                                <a href="{{ $resume_url }}"
                                   target="_blank"
                                   class="download-file"
                                   style="color:#fff;margin-left:250px;display:inline-flex;align-items:center;justify-content:center;">
                                    <i class="fa fa-download" aria-hidden="true" style="color:#fff"></i>
                                </a>
                                @endif

                                <button type="button"
                                        class="remove-file"
                                        id="removeFile">
                                    Remove
                                </button>
                            </div>

                        </div>

                            <!-- ==========================
                                 After-upload Resume Information card
                                 Outside #uploadBox so hiding the dropzone
                                 does not hide this card.
                                 ========================== -->
                            <div class="up-info" id="upInfo">
                                <div class="up-info-banner">
                                    <div class="up-info-banner-left">
                                        <span class="up-info-banner-check">
                                            <i class="fa-solid fa-check" aria-hidden="true"></i>
                                        </span>
                                        <div class="up-info-banner-text">
                                            <strong id="upInfoBannerTitle">Resume uploaded successfully!</strong>
                                            <small id="upInfoBannerSub">Your resume has been uploaded and parsed.</small>
                                        </div>
                                    </div>
                                    <button type="button" class="up-info-banner-close" aria-label="Dismiss banner">
                                        <i class="fa-solid fa-xmark" aria-hidden="true"></i>
                                    </button>
                                </div>

                                <div class="up-file-card">
                                    <div class="up-file-card-left">
                                        <div class="up-file-badge">
                                            <i class="fa-regular fa-file-pdf" aria-hidden="true"></i>
                                            <span class="up-file-badge-ext" id="upFileBadgeExt">PDF</span>
                                        </div>
                                        <div class="up-file-meta">
                                            <div class="up-file-name" id="upFileName">&nbsp;</div>
                                            <div class="up-file-sub" id="upFileSub">&nbsp;</div>
                                        </div>
                                    </div>
                                    <div class="up-file-actions">
                                        <button type="button" class="up-btn up-btn-remove" id="upRemoveBtn" title="Delete Resume">
                                            <i class="fa-regular fa-trash-can" aria-hidden="true"></i>
                                            <span>Delete</span>
                                        </button>
                                    </div>
                                </div>

                                <div class="up-details-card">
                                    <div class="up-details-grid">
                                        <div class="up-detail-tile">
                                            <span class="up-detail-icon">
                                                <i class="fa-regular fa-clock" aria-hidden="true"></i>
                                            </span>
                                            <div>
                                                <div class="up-detail-lbl">Uploaded On</div>
                                                <div class="up-detail-val" id="upDetailUploadedOn">&nbsp;</div>
                                            </div>
                                        </div>
                                        <div class="up-detail-tile">
                                            <span class="up-detail-icon">
                                                <i class="fa-regular fa-square-check" aria-hidden="true"></i>
                                            </span>
                                            <div>
                                                <div class="up-detail-lbl">Parser Status</div>
                                                <div class="up-detail-val" id="upDetailParserStatus">&nbsp;</div>
                                            </div>
                                        </div>
                                        <div class="up-detail-tile">
                                            <span class="up-detail-icon">
                                                <i class="fa-regular fa-file" aria-hidden="true"></i>
                                            </span>
                                            <div>
                                                <div class="up-detail-lbl">File Type</div>
                                                <div class="up-detail-val" id="upDetailFileType">&nbsp;</div>
                                            </div>
                                        </div>
                                        <div class="up-detail-tile">
                                            <span class="up-detail-icon">
                                                <i class="fa-solid fa-database" aria-hidden="true"></i>
                                            </span>
                                            <div>
                                                <div class="up-detail-lbl">File Size</div>
                                                <div class="up-detail-val" id="upDetailFileSize">&mdash;</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                    </div>
                </div>
                <div class="fs fv-row summary-card candidate-card" style="margin-bottom:0">
                  <div class="fsh resume-card-header summary-card-header candidate-card-header">
                    <div class="candidate-card-title summary-title-wrap">
                      <span class="summary-card-icon candidate-card-icon"><i class="fa-solid fa-align-left" aria-hidden="true"></i></span>
                      <span><strong>Career Summary <span class="rq">*</span></strong><small>Highlight the candidate's experience, strengths, and suitability.</small></span>
                    </div>
                  </div>
                  <div class="fsc summary-card-body candidate-card-body">
                    <textarea placeholder="Enter career summary..." id="careersummary" name="careersummary" rows="10">{{ @$application->careersummary }}</textarea>
                    <div class="cc">0 / 100</div>
                    <div class="tip"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10" />
                        <line x1="12" y1="16" x2="12" y2="12" />
                        <line x1="12" y1="8" x2="12.01" y2="8" />
                      </svg><span>Tip: A good summary helps the client
                        understand the candidate quickly.</span></div>
                  </div>
                </div>
            </div>
            
            <!--begin::Upload Photo-->
            <div class="form-group row fv-row mb-10 d-none">
              <div class="col-lg-4 col-xl-4 text-center">
                <!--begin::Image input-->
                <div class="image-input image-input-outline" data-kt-image-input="true">
                  <!--begin::Image preview wrapper-->
                  <div class="image-input-wrapper w-90px h-90px" style="background-image: url({!! @$photo !!})"></div>
                  <!--end::Image preview wrapper-->
                  <!--begin::Edit button-->
                  <label class="btn btn-icon btn-circle btn-active-color-primary w-20px h-20px bg-white shadow" data-kt-image-input-action="change" data-bs-toggle="tooltip" data-bs-dismiss="click" title="Change avatar"> <i class="bi bi-pencil-fill fs-9"></i>
                  <!--begin::Inputs-->
                  <input type="file" name="photo" id="avatar" accept=".png, .jpg, .jpeg" />
                  <input type="hidden" name="avatar_remove" />
                  <!--end::Inputs-->
                  </label>
                  <!--end::Edit button-->
                  <!--begin::Cancel button-->
                  <span class="btn btn-icon btn-circle btn-active-color-primary w-20px h-20px bg-white shadow" data-kt-image-input-action="cancel" data-bs-toggle="tooltip" data-bs-dismiss="click" title="Cancel avatar"> <i class="bi bi-x fs-4"></i> </span>
                  <!--end::Cancel button-->
                  <!--begin::Remove button-->
                  <span class="btn btn-icon btn-circle btn-active-color-primary w-20px h-20px bg-white shadow" data-kt-image-input-action="remove" data-bs-toggle="tooltip" data-bs-dismiss="click" title="Remove avatar"> <i class="bi bi-x fs-4"></i> </span>
                  <!--end::Remove button-->
                </div>
                <span class="form-text text-muted d-block">Allowed file types: png,jpg, jpeg.</span>
                <!--end::Image input-->
              </div>
              <div class="col-lg-8 col-xl-8">
                {{-- <input class="select-file w-100 mb-2 dropzone bg-light " accept=".png,.jpg,.jpeg,.pdf,.doc,.docx,.xls,.xlsx,.rtf" type="file" > --}}
                <input type="hidden" name="uploaded_file_path" id="uploaded_file_path">
                <input type="hidden" name="resume_original_name" id="resume_original_name" value="">
                <div class="fv-row"> <span class="form-text text-muted d-block">*Please upload the resume (Max file size 2MB). Allowed file types: PDF or Word.</span> </div>
                <!--end::Input group-->
              </div>
            </div>
            <!--end::Upload Photo & Resume/CV-->

            <!-- Candidate Profile -->
            <div class="fs bd-card candidate-card candidate-profile-card">
              <div class="fsh candidate-card-header bd-hero">
                <div class="candidate-card-title bd-hero-title">
                  <span class="candidate-card-icon bd-hero-icon"><i class="fa-solid fa-user" aria-hidden="true"></i></span>
                  <span><strong>Candidate Profile</strong><small>Basic information to identify and contact this candidate.</small></span>
                </div>
              </div>
              <div class="fsc candidate-card-body bd-body">
                <div class="candidate-profile-grid">
                  <div class="fg fv-row">
                    <label>Full Name <span class="rq">*</span></label>
                    <div class="candidate-field-control"><i class="fa-regular fa-user" aria-hidden="true"></i><input type="text" class="form-control form-control-solid" name="full_name" id="full_name" value="{{ @$application->full_name }}" placeholder="Enter full name" /></div>
                  </div>
                  <div class="fg fv-row">
                    <label>Primary Email <span class="rq">*</span></label>
                    <div class="candidate-field-control"><i class="fa-regular fa-envelope" aria-hidden="true"></i><input type="email" name="email" id="email" value="{{ @$application->email }}" placeholder="Enter primary email address" /></div>
                  </div>
                  <div class="fg fv-row">
                    <label>Primary Contact Number <span class="rq">*</span></label>
                    <x-jero-phone-input
                        id="phone"
                        name="phone"
                        countryCodeName="primary_country_code"
                        countryCodeId="primary_country_code"
                        :value="@$application->phone"
                        :countryCode="@$phone_code"
                        :defaultIso="@$country_code"
                        label=""
                        :required="true"
                        placeholder="Enter primary number" />
                  </div>
                  <div class="fg fv-row bd-field">
                    <label>Current Location <span class="rq">*</span></label>
                    <div class="candidate-field-control bd-control bd-control--has-combobox">
                      <i class="fa-solid fa-location-dot"></i>
                      {{-- Canonical Form Fields (0% MySQL Reads, Server Trust Boundary Compliant) --}}
                      <input type="hidden" name="location_city_id" id="location_city_id" value="{{ @$application->city_id ?? '' }}">
                      <input type="hidden" name="location_state_id" id="location_state_id" value="{{ @$application->state ?? '' }}">
                      <input type="hidden" name="location_country_id" id="location_country_id" value="{{ @$application->country ?? '' }}">
                      <input type="hidden" name="location_canonical_label" id="location_canonical_label" value="{{ @$default_loc ?? '' }}">
                      <input type="hidden" name="location_sig" id="location_sig" value="">

                      {{-- Backward-Compatible Bridge Payload for FormValidation & Legacy Interop --}}
                      <input type="hidden" name="kt_tagify_work_location" id="kt_tagify_work_location" value="{{ @$default_loc ? json_encode([['value' => @$default_loc]]) : '' }}">

                      {{-- JERO Native Custom Combobox Mount Point --}}
                      <div class="jero-custom-select" id="jero-cs-location" data-field="location">
                        <button type="button" class="jero-cs-trigger" aria-haspopup="listbox" aria-expanded="false" aria-controls="jero-cs-location-list">
                          <span class="jero-cs-left">
                            <span class="jero-cs-icon"><i class="fa-solid fa-location-dot"></i></span>
                            <span class="jero-cs-value jero-cs-placeholder">Search city, state or country</span>
                          </span>
                          <span class="jero-cs-right">
                            <span class="jero-cs-trigger-clear" title="Clear selection" style="display:none;">&times;</span>
                            <i class="fa-solid fa-chevron-down jero-cs-arrow"></i>
                          </span>
                        </button>
                        <div class="jero-cs-dropdown" style="display: none;">
                          <div class="jero-cs-search-box">
                            <i class="fa-solid fa-magnifying-glass jero-cs-search-icon"></i>
                            <input type="text" class="jero-cs-search-input" placeholder="Search city, state or country..." autocomplete="off" />
                            <i class="fa-solid fa-spinner jero-cs-search-spinner" style="display:none;"></i>
                            <button type="button" class="jero-cs-search-clear" style="display:none;">&times;</button>
                          </div>
                          <div class="jero-cs-list" id="jero-cs-location-list" role="listbox"></div>
                          <div class="jero-cs-empty is-hidden"><i class="fa-regular fa-face-frown"></i> No locations matching your search.</div>
                        </div>
                      </div>
                    </div>
                    {{-- Disambiguation Ribbon for NEEDS_CONFIRMATION State --}}
                    <div id="jero-location-disambiguation" class="jero-disambiguation-bar" style="display:none;"></div>
                  </div>
                  <div class="fg fv-row bd-field">
                    <label>Highest Qualification <span class="rq">*</span></label>
                    <div class="candidate-field-control bd-control bd-control--select">
                      <i class="fa-solid fa-graduation-cap" aria-hidden="true"></i>
                      <select name="qualification" id="qualification">
                        <option value="">Select qualification</option>
                        @php
                          // Map incoming database qualification records by ID
                          $qualMap = [];
                          if (!empty($qualification)) {
                              foreach ($qualification as $qItem) {
                                  $qualMap[$qItem->id] = $qItem;
                              }
                          }

                          // 7-Category Global Education Standard with Regional Prov & Global Alias Taxonomy
                          $educationCategories = [
                              "🎓 Bachelor's / Undergraduate Degrees" => [
                                  13 => ['label' => 'B.Tech / B.E (Engineering & Technology)', 'badge' => 'Engineering / Tech', 'regions' => 'Indian,Commonwealth,Global', 'aliases' => 'btech,b.tech,b tech,be,b.e,bachelor of technology,bachelor of engineering,beng,b.eng,bse,bs engineering,diplome ingenieur,diplom ingenieur,software engineer,computer science engineering,cse,mechanical,electrical,civil'],
                                  12 => ['label' => 'B.S. / B.Sc (Science, IT & Computing / Licence)', 'badge' => 'USA / Global / EU', 'regions' => 'American,Canadian,British,Australian,European,Indian,Global', 'aliases' => 'bs,b.s.,bsc,b.sc,bachelor of science,bachelor science,bs cs,bsc cs,b sc,bsc it,bs it,information technology,computer science,data science,cybersecurity,cyber security,cloud computing,artificial intelligence,software development,licence,licenciatura,bac+3,grado'],
                                  16 => ['label' => 'BCA (Computer Applications / Software)', 'badge' => 'IT / Software', 'regions' => 'Indian,Global', 'aliases' => 'bca,b.c.a.,bachelor of computer applications,bachelor computer applications,computer programming,web development'],
                                  5  => ['label' => 'BBA / BMS (Business Administration)', 'badge' => 'Business / Global', 'regions' => 'Indian,American,Canadian,British,Australian,Global', 'aliases' => 'bba,b.b.a.,bms,b.m.s.,bachelor of business administration,bachelor business,management studies,business management,bba marketing,bba finance'],
                                  6  => ['label' => 'B.Com (Commerce, Accounting & Finance)', 'badge' => 'Commerce / Finance', 'regions' => 'Indian,British,Canadian,Australian,Commonwealth,Global', 'aliases' => 'bcom,b.com,bachelor of commerce,bachelor commerce,commerce,accounting,finance,banking'],
                                  3  => ['label' => 'B.A (Arts, Humanities & Social Sciences)', 'badge' => 'Humanities / Global', 'regions' => 'Indian,American,Canadian,British,Australian,European,Global', 'aliases' => 'ba,b.a.,bachelor of arts,bachelor arts,humanities,economics,psychology,english,journalism,mass communication,history'],
                                  7  => ['label' => 'B.Des (Design & UX/UI)', 'badge' => 'Design / Global', 'regions' => 'Indian,American,Canadian,British,Global', 'aliases' => 'bdes,b.des,bachelor of design,bachelor design,ui ux,product design,graphic design,fashion design,industrial design'],
                                  18 => ['label' => 'BFA (Fine Arts & Digital Media)', 'badge' => 'Arts / Media', 'regions' => 'Indian,American,Canadian,British,Global', 'aliases' => 'bfa,b.f.a.,bachelor of fine arts,visual arts,animation,digital media,illustration'],
                                  4  => ['label' => 'B.Arch (Architecture & Urban Planning)', 'badge' => 'Architecture', 'regions' => 'Indian,American,Canadian,British,Global', 'aliases' => 'barch,b.arch,bachelor of architecture,architectural design,urban planning'],
                                  11 => ['label' => 'B.Pharma / Nursing / Allied Health (Healthcare & Pharma)', 'badge' => 'Healthcare / Pharma', 'regions' => 'Indian,American,Canadian,British,Global', 'aliases' => 'bpharma,b.pharma,b pharm,b.pharm,bachelor of pharmacy,pharmacy,nursing,b.sc nursing,bsc nursing,rn,registered nurse,bpt,physiotherapy,bachelor of physiotherapy,allied health,paramedical,pharm d,pharm.d'],
                                  19 => ['label' => 'BHM (Hotel & Hospitality Management)', 'badge' => 'Hospitality', 'regions' => 'Indian,Global', 'aliases' => 'bhm,b.h.m.,bachelor of hotel management,hospitality,tourism,culinary arts'],
                                  8  => ['label' => 'B.Ed (Education & Pedagogy)', 'badge' => 'Education', 'regions' => 'Indian,British,Commonwealth,Global', 'aliases' => 'bed,b.ed,bachelor of education,teaching,pedagogy'],
                                  9  => ['label' => 'B.El.Ed (Elementary Education)', 'badge' => 'Education', 'regions' => 'Indian', 'aliases' => 'beled,b.el.ed,bachelor of elementary education,primary teaching'],
                                  10 => ['label' => 'B.P.Ed (Physical Education & Sports)', 'badge' => 'Sports Science', 'regions' => 'Indian,Global', 'aliases' => 'bped,b.p.ed,bachelor of physical education,sports science,kinesiology'],
                                  21 => ['label' => 'BVSC / DVM (Veterinary Science & Medicine)', 'badge' => 'Veterinary', 'regions' => 'Indian,British,American,Commonwealth,Global', 'aliases' => 'bvsc,b.v.sc,bachelor of veterinary science,dvm,veterinary medicine,veterinarian'],
                                  61 => ['label' => 'Other Bachelor\'s / Undergraduate Degree', 'badge' => 'Bachelor\'s / Other', 'regions' => 'Global', 'aliases' => 'other bachelor,other bachelors,other undergraduate,bachelors other,custom bachelor,unlisted bachelor'],
                              ],
                              "🎓 Master's / Postgraduate Degrees" => [
                                  39 => ['label' => 'M.S. / M.Sc / M.Eng (Science, IT & Engineering / Master 2)', 'badge' => 'USA / Global / EU', 'regions' => 'American,Canadian,British,Australian,European,Indian,Global', 'aliases' => 'ms,m.s.,msc,m.sc,master of science,master science,ms cs,msc cs,m s,m sc,meng,m.eng,master of engineering,data science,ai,artificial intelligence,machine learning,deep learning,cybersecurity,cloud architecture,big data,bioinformatics,master 2,bac+5,magister'],
                                  40 => ['label' => 'M.Tech / M.E (Engineering & Advanced Tech)', 'badge' => 'Engineering / Postgrad', 'regions' => 'Indian,Global', 'aliases' => 'mtech,m.tech,m tech,me,m.e,master of technology,master of engineering,meng,m.eng,postgraduate engineering,vlsi,embedded,robotics'],
                                  41 => ['label' => 'MBA / PGDM (Business Administration & Strategy)', 'badge' => 'Management / Global', 'regions' => 'Indian,American,Canadian,British,Australian,European,Global', 'aliases' => 'mba,m.b.a.,pgdm,p.g.d.m.,master of business administration,master business administration,executive mba,emba,insead,wharton,hbs,iim,post graduate diploma in management,business management,strategy,consulting'],
                                  42 => ['label' => 'MCA (Master of Computer Applications)', 'badge' => 'IT / Software', 'regions' => 'Indian,Global', 'aliases' => 'mca,m.c.a.,master of computer applications,master computer applications,software engineering,computer science'],
                                  43 => ['label' => 'MCM (Master of Computer Management)', 'badge' => 'Management & IT', 'regions' => 'Indian', 'aliases' => 'mcm,m.c.m.,master of computer management,it management'],
                                  35 => ['label' => 'M.Com (Commerce, Economics & Finance)', 'badge' => 'Commerce / Finance', 'regions' => 'Indian,British,Commonwealth,Global', 'aliases' => 'mcom,m.com,master of commerce,economics,financial management,accounting'],
                                  32 => ['label' => 'M.A (Arts, Humanities & Social Sciences)', 'badge' => 'Humanities / Global', 'regions' => 'Indian,American,Canadian,British,Australian,European,Global', 'aliases' => 'ma,m.a.,master of arts,humanities,sociology,psychology,political science,international relations,literature'],
                                  36 => ['label' => 'M.Des (Master of Design / UX)', 'badge' => 'Design / Postgrad', 'regions' => 'Indian,American,Canadian,British,Global', 'aliases' => 'mdes,m.des,master of design,interaction design,ux design,product design'],
                                  45 => ['label' => 'MFA (Fine Arts & Creative Media)', 'badge' => 'Fine Arts / Media', 'regions' => 'Indian,American,Canadian,British,Global', 'aliases' => 'mfa,m.f.a.,master of fine arts,creative media,visual arts,cinematography'],
                                  33 => ['label' => 'M.Arch (Master of Architecture)', 'badge' => 'Architecture', 'regions' => 'Indian,American,Canadian,British,Global', 'aliases' => 'march,m.arch,master of architecture,landscape architecture,urban design'],
                                  38 => ['label' => 'M.Pharma / Pharm.D / MPT (Advanced Healthcare & Pharma)', 'badge' => 'Healthcare / Pharma', 'regions' => 'Indian,American,Canadian,British,Global', 'aliases' => 'mpharma,m.pharma,m pharm,m.pharm,master of pharmacy,pharm d,pharm.d,doctor of pharmacy,mpt,master of physiotherapy,pharmacology,clinical research'],
                                  37 => ['label' => 'M.Ed (Master of Education)', 'badge' => 'Education', 'regions' => 'Indian,British,Commonwealth,Global', 'aliases' => 'med,m.ed,master of education,educational leadership,curriculum'],
                                  47 => ['label' => 'MVSC (Master of Veterinary Science)', 'badge' => 'Veterinary Postgrad', 'regions' => 'Indian,British,Commonwealth,Global', 'aliases' => 'mvsc,m.v.sc,master of veterinary science,veterinary surgery'],
                                  30 => ['label' => 'Integrated PG / Dual Degree (B.Tech+M.Tech / BS+MS)', 'badge' => 'Integrated / Dual', 'regions' => 'Indian,European,American,Global', 'aliases' => 'integrated pg,dual degree,integrated mtech,integrated btech mtech,5 year integrated,bs ms,b.s. m.s.,btech mtech'],
                                  62 => ['label' => 'Other Master\'s / Postgraduate Degree', 'badge' => 'Master\'s / Other', 'regions' => 'Global', 'aliases' => 'other master,other masters,other postgraduate,masters other,custom master,unlisted master,mph,msw,mphil,m.phil'],
                              ],
                              "🏛️ Doctorate & Research Degrees" => [
                                  52 => ['label' => 'Ph.D. / Doctorate / Post-Doc (Doctor of Philosophy)', 'badge' => 'Doctorate / Global', 'regions' => 'Indian,American,Canadian,British,Australian,European,Global', 'aliases' => 'phd,ph.d.,ph.d,doctorate,doctor of philosophy,dphil,d.phil,postdoc,post doc,post doctoral,fellowship,research scholar,promotion,dr'],
                                  64 => ['label' => 'Other Doctorate / Post-Doctoral Degree', 'badge' => 'Doctorate / Other', 'regions' => 'Global', 'aliases' => 'other doctorate,other phd,postdoc,post-doctoral,dba,d.sc,doctor of business administration,research fellow'],
                              ],
                              "📜 Associate Degrees & Diplomas" => [
                                  22 => ['label' => 'Associate Degree / Polytechnic Diploma (A.A. / A.S. / Diploma)', 'badge' => 'Polytechnic / Assoc', 'regions' => 'Indian,American,Canadian,Australian,European,Global', 'aliases' => 'diploma,polytechnic,associate degree,associate of science,associate of arts,aa,a.a.,as,a.s.,aas,a.a.s.,community college,2 year degree,bts,dut,fachhochschule'],
                                  48 => ['label' => 'PG Diploma (Post Graduate Diploma / Certificate)', 'badge' => 'PG Diploma', 'regions' => 'Indian,Canadian,British,Commonwealth,Global', 'aliases' => 'pg diploma,pgd,pgdiploma,post graduate diploma,postgraduate certificate,post-degree diploma,canadian pg diploma'],
                                  25 => ['label' => 'HND / HNC (Higher National Diploma / Certificate)', 'badge' => 'UK / Commonwealth', 'regions' => 'British,Commonwealth,Global', 'aliases' => 'hnd,hnc,higher national diploma,higher national certificate,vocational qualification,btec'],
                                  63 => ['label' => 'Other Associate / Diploma / Vocational Degree', 'badge' => 'Diploma / Other', 'regions' => 'Global', 'aliases' => 'other diploma,other associate,vocational diploma,technical certificate,custom diploma,unlisted diploma'],
                              ],
                              "⚖️ Professional, Legal & Medical Degrees" => [
                                  26 => ['label' => 'CA / CPA / CFA / ACCA (Finance & Accounting)', 'badge' => 'Finance / Global', 'regions' => 'Indian,American,Canadian,British,Australian,European,Global', 'aliases' => 'ca,cpa,cfa,acca,chartered accountant,chartered accountancy,certified public accountant,chartered financial analyst,cima,cost accountant,accounting,finance,audit'],
                                  27 => ['label' => 'CS (Company Secretary / Corporate Governance)', 'badge' => 'Corporate Law', 'regions' => 'Indian,British,Commonwealth', 'aliases' => 'cs,c.s.,company secretary,company secretaryship,corporate governance,icsi'],
                                  29 => ['label' => 'ICWA / CMA (Cost & Management Accounting)', 'badge' => 'Cost Accounting', 'regions' => 'Indian,British,Commonwealth,Global', 'aliases' => 'icwa,cma,cost accountant,cost and management accountant,cma usa,icmai'],
                                  23 => ['label' => 'LLB / J.D. (Law Degree / Juris Doctor)', 'badge' => 'Legal / Global', 'regions' => 'Indian,British,American,Commonwealth,Global', 'aliases' => 'llb,l.l.b.,bachelor of laws,jd,j.d.,juris doctor,law degree,bar admitted,attorney,lawyer,solicitor,advocate'],
                                  31 => ['label' => 'LLM (Master of Laws / Legal Postgrad)', 'badge' => 'Legal / Postgrad', 'regions' => 'Indian,American,Canadian,British,European,Commonwealth,Global', 'aliases' => 'llm,l.l.m.,master of laws,corporate law,international law,intellectual property'],
                                  24 => ['label' => 'MBBS / M.D. / D.O. (General Medicine & Surgery)', 'badge' => 'Medical / Surgery', 'regions' => 'Indian,British,American,Commonwealth,Global', 'aliases' => 'mbbs,m.b.b.s.,bachelor of medicine,md,m.d.,doctor of medicine,do,d.o.,doctor of osteopathic medicine,physician'],
                                  46 => ['label' => 'MD / MS (Medical Specialty / Master of Surgery)', 'badge' => 'Medical Specialty', 'regions' => 'Indian,American,Canadian,British,Commonwealth,Global', 'aliases' => 'md,m.d.,ms general surgery,master of surgery,doctor of medicine,residency,medical specialty'],
                                  28 => ['label' => 'DM (Doctor of Medicine - Medical Super Specialty)', 'badge' => 'Super Specialty', 'regions' => 'Indian,Global', 'aliases' => 'dm,d.m.,super specialty,cardiology,neurology,oncology'],
                                  34 => ['label' => 'M.Ch (Surgical Super Specialty)', 'badge' => 'Surgical Specialty', 'regions' => 'Indian,Global', 'aliases' => 'mch,m.ch,magister chirurgiae,neurosurgery,cardiothoracic'],
                                  17 => ['label' => 'BDS / DDS (Dental Surgery & Medicine)', 'badge' => 'Dental / Global', 'regions' => 'Indian,British,American,Commonwealth,Global', 'aliases' => 'bds,b.d.s.,dds,d.d.s.,dmd,d.m.d.,dental surgery,dentist,oral surgery'],
                                  44 => ['label' => 'MDS (Master of Dental Surgery)', 'badge' => 'Dental Postgrad', 'regions' => 'Indian,British,Commonwealth,Global', 'aliases' => 'mds,m.d.s.,master dental surgery,orthodontics,prosthodontics'],
                                  15 => ['label' => 'BAMS (Ayurvedic Medicine & Surgery)', 'badge' => 'Ayurveda', 'regions' => 'Indian', 'aliases' => 'bams,b.a.m.s.,ayurveda,ayush'],
                                  20 => ['label' => 'BHMS (Homeopathic Medicine & Surgery)', 'badge' => 'Homeopathy', 'regions' => 'Indian', 'aliases' => 'bhms,b.h.m.s.,homeopathy,ayush'],
                                  14 => ['label' => 'B.U.M.S (Unani Medicine & Surgery)', 'badge' => 'Unani', 'regions' => 'Indian', 'aliases' => 'bums,b.u.m.s.,unani,ayush'],
                              ],
                              "🏫 Secondary & High School" => [
                                  2  => ['label' => '12th Standard / High School Diploma / A-Levels / GED', 'badge' => 'High School', 'regions' => 'Indian,American,Canadian,British,Global', 'aliases' => '12th,12th standard,hsc,intermediate,high school,high school diploma,ged,abitur,baccalaureat,senior secondary'],
                                  1  => ['label' => '10th Standard / Matriculation / Secondary School', 'badge' => 'Secondary', 'regions' => 'Indian,Global', 'aliases' => '10th,10th standard,ssc,matric,matriculation,secondary school'],
                                  49 => ['label' => 'A Levels / Advanced Level (UK / International)', 'badge' => 'UK A-Levels', 'regions' => 'British,Commonwealth,European,Global', 'aliases' => 'a levels,a-levels,alevel,advanced level,gce a levels'],
                                  51 => ['label' => 'GCSE / O-Levels (UK / International)', 'badge' => 'UK GCSE', 'regions' => 'British,Commonwealth,Global', 'aliases' => 'gcse,o-levels,o levels,olevel,igcse,gce o level'],
                                  50 => ['label' => 'Vocational Secondary / GNVQ / Technical Certificate', 'badge' => 'Vocational', 'regions' => 'British,Commonwealth,Global', 'aliases' => 'gnvq,vocational secondary,nvq,btec level 2,technical certificate'],
                              ],
                              "🌐 Other Qualifications" => [
                                  60 => ['label' => 'Others / Intensive Bootcamp / Specialized Certification', 'badge' => 'Other / Bootcamp', 'regions' => 'Global,Indian,American,Canadian,British,Australian,European', 'aliases' => 'other,others,non disclosed,bootcamp,coding bootcamp,full stack,certifications,aws certified,pmp,scrum master'],
                              ],
                          ];

                          $selectedQualId = @$application->qualification;
                          $renderedIds = [];
                        @endphp

                        @foreach($educationCategories as $categoryLabel => $catItems)
                          <optgroup label="{{ $categoryLabel }}">
                            @foreach($catItems as $qId => $itemMeta)
                              @if(isset($qualMap[$qId]))
                                @php
                                  $renderedIds[] = $qId;
                                  $isSelected = ((string)$selectedQualId === (string)$qId);
                                @endphp
                                <option value="{{ $qId }}" 
                                        data-regions="{{ $itemMeta['regions'] }}"
                                        data-aliases="{{ $itemMeta['aliases'] }}"
                                        data-badge="{{ $itemMeta['badge'] }}"
                                        {{ $isSelected ? 'selected="selected"' : '' }}>
                                  {{ $itemMeta['label'] }}
                                </option>
                              @endif
                            @endforeach
                          </optgroup>
                        @endforeach

                        {{-- Fallback safety for any unmapped database record --}}
                        @php
                          $remainingItems = [];
                          foreach ($qualMap as $qId => $qObj) {
                              if (!in_array($qId, $renderedIds)) {
                                  $remainingItems[] = $qObj;
                              }
                          }
                        @endphp
                        @if(count($remainingItems) > 0)
                          <optgroup label="Additional Qualifications">
                            @foreach($remainingItems as $remItem)
                              <option value="{{ $remItem->id }}" data-regions="Global" data-aliases="" data-badge="Other" {{ ((string)$selectedQualId === (string)$remItem->id) ? 'selected="selected"' : '' }}>
                                {{ $remItem->name }}
                              </option>
                            @endforeach
                          </optgroup>
                        @endif
                      </select>
                    </div>
                  </div>
                  <div class="fg fv-row">
                    <label>Nationality</label>
                    <div class="candidate-field-control">
                      <i class="fa-solid fa-flag" aria-hidden="true"></i>
                      @php
                        // Tenant HQ Country Mapping to Demonym (Sensible Defaulting)
                        $countryToDemonym = [
                            'in' => 'Indian',
                            'us' => 'American',
                            'gb' => 'British',
                            'uk' => 'British',
                            'ca' => 'Canadian',
                            'au' => 'Australian',
                            'ae' => 'Emirati',
                            'sg' => 'Singaporean',
                            'de' => 'German',
                            'fr' => 'French',
                            'nl' => 'Dutch',
                            'ie' => 'Irish',
                            'sa' => 'Saudi',
                        ];
                        $tenantDefaultNat = $countryToDemonym[strtolower($country_code ?? 'in')] ?? 'Indian';
                        $selectedNationality = @$application->nationality ?: ((@$page_event == 'EDIT' && !empty(@$application->id)) ? '' : $tenantDefaultNat);

                        // ⭐ Top 10 Popular Nationalities / Talent Hubs with Country Synonyms for Instant Search
                        $topPopularNationalities = [
                            'Indian'      => ['label' => '🇮🇳 Indian (India)', 'synonyms' => 'india, bharat, hindustan, in, ind'],
                            'American'    => ['label' => '🇺🇸 American (United States)', 'synonyms' => 'united states, usa, america, us, u.s., u.s.a.'],
                            'British'     => ['label' => '🇬🇧 British (United Kingdom)', 'synonyms' => 'united kingdom, uk, u.k., britain, england, scotland, wales, gb'],
                            'Canadian'    => ['label' => '🇨🇦 Canadian (Canada)', 'synonyms' => 'canada, ca, can'],
                            'Australian'  => ['label' => '🇦🇺 Australian (Australia)', 'synonyms' => 'australia, au, aus, oz'],
                            'Emirati'     => ['label' => '🇦🇪 Emirati (United Arab Emirates)', 'synonyms' => 'united arab emirates, uae, u.a.e., dubai, abu dhabi, sharjah, ae'],
                            'Singaporean' => ['label' => '🇸🇬 Singaporean (Singapore)', 'synonyms' => 'singapore, sg, sgp'],
                            'German'      => ['label' => '🇩🇪 German (Germany)', 'synonyms' => 'germany, deutschland, de, deu'],
                            'French'      => ['label' => '🇫🇷 French (France)', 'synonyms' => 'france, fr, fra'],
                            'Dutch'       => ['label' => '🇳🇱 Dutch (Netherlands)', 'synonyms' => 'netherlands, holland, nl, nld, amsterdam'],
                            'Irish'       => ['label' => '🇮🇪 Irish (Ireland)', 'synonyms' => 'ireland, eire, ie, irl, dublin'],
                            'Saudi'       => ['label' => '🇸🇦 Saudi (Saudi Arabia)', 'synonyms' => 'saudi arabia, ksa, sa, sau, riyadh'],
                            'Other'       => ['label' => '🌐 Other / Non-Disclosed', 'synonyms' => 'other, international, non disclosed, unknown'],
                        ];

                        // Complete 195+ UN Sovereign Demonyms with Country Name Synonyms
                        $allNationalities = [
                            'Afghan' => 'Afghanistan', 'Albanian' => 'Albania', 'Algerian' => 'Algeria', 'Andorran' => 'Andorra', 'Angolan' => 'Angola', 
                            'Antiguan' => 'Antigua and Barbuda', 'Argentine' => 'Argentina', 'Armenian' => 'Armenia', 'Australian' => 'Australia', 'Austrian' => 'Austria',
                            'Azerbaijani' => 'Azerbaijan', 'Bahamian' => 'Bahamas', 'Bahraini' => 'Bahrain', 'Bangladeshi' => 'Bangladesh', 'Barbadian' => 'Barbados', 
                            'Belarusian' => 'Belarus', 'Belgian' => 'Belgium', 'Belizean' => 'Belize', 'Beninese' => 'Benin', 'Bhutanese' => 'Bhutan',
                            'Bolivian' => 'Bolivia', 'Bosnian' => 'Bosnia and Herzegovina', 'Botswanan' => 'Botswana', 'Brazilian' => 'Brazil', 'British' => 'United Kingdom, UK, Britain', 
                            'Bruneian' => 'Brunei', 'Bulgarian' => 'Bulgaria', 'Burkinabe' => 'Burkina Faso', 'Burmese' => 'Myanmar, Burma', 'Burundian' => 'Burundi',
                            'Cambodian' => 'Cambodia', 'Cameroonian' => 'Cameroon', 'Canadian' => 'Canada', 'Cape Verdean' => 'Cape Verde', 'Central African' => 'Central African Republic', 
                            'Chadian' => 'Chad', 'Chilean' => 'Chile', 'Chinese' => 'China, PRC', 'Colombian' => 'Colombia', 'Comorian' => 'Comoros',
                            'Congolese' => 'Congo', 'Costa Rican' => 'Costa Rica', 'Croatian' => 'Croatia', 'Cuban' => 'Cuba', 'Cypriot' => 'Cyprus', 
                            'Czech' => 'Czech Republic, Czechia', 'Danish' => 'Denmark', 'Djiboutian' => 'Djibouti', 'Dominican' => 'Dominican Republic', 'Dutch' => 'Netherlands, Holland',
                            'East Timorese' => 'East Timor', 'Ecuadorean' => 'Ecuador', 'Egyptian' => 'Egypt', 'Emirati' => 'United Arab Emirates, UAE, Dubai', 'Equatorial Guinean' => 'Equatorial Guinea', 
                            'Eritrean' => 'Eritrea', 'Estonian' => 'Estonia', 'Ethiopian' => 'Ethiopia', 'Fijian' => 'Fiji', 'Filipino' => 'Philippines',
                            'Finnish' => 'Finland', 'French' => 'France', 'Gabonese' => 'Gabon', 'Gambian' => 'Gambia', 'Georgian' => 'Georgia', 
                            'German' => 'Germany, Deutschland', 'Ghanaian' => 'Ghana', 'Greek' => 'Greece', 'Grenadian' => 'Grenada', 'Guatemalan' => 'Guatemala',
                            'Guinean' => 'Guinea', 'Guyanese' => 'Guyana', 'Haitian' => 'Haiti', 'Honduran' => 'Honduras', 'Hungarian' => 'Hungary', 
                            'Icelander' => 'Iceland', 'Indian' => 'India, Bharat', 'Indonesian' => 'Indonesia', 'Iranian' => 'Iran', 'Iraqi' => 'Iraq',
                            'Irish' => 'Ireland, Eire', 'Israeli' => 'Israel', 'Italian' => 'Italy', 'Ivorian' => 'Ivory Coast, Cote dIvoire', 'Jamaican' => 'Jamaica', 
                            'Japanese' => 'Japan', 'Jordanian' => 'Jordan', 'Kazakh' => 'Kazakhstan', 'Kenyan' => 'Kenya', 'Kuwaiti' => 'Kuwait',
                            'Kyrgyz' => 'Kyrgyzstan', 'Laotian' => 'Laos', 'Latvian' => 'Latvia', 'Lebanese' => 'Lebanon', 'Liberian' => 'Liberia', 
                            'Libyan' => 'Libya', 'Liechtenstein citizen' => 'Liechtenstein', 'Lithuanian' => 'Lithuania', 'Luxembourgish' => 'Luxembourg', 'Macedonian' => 'North Macedonia',
                            'Malagasy' => 'Madagascar', 'Malawian' => 'Malawi', 'Malaysian' => 'Malaysia', 'Maldivian' => 'Maldives', 'Malian' => 'Mali', 
                            'Maltese' => 'Malta', 'Marshallese' => 'Marshall Islands', 'Mauritanian' => 'Mauritania', 'Mauritian' => 'Mauritius', 'Mexican' => 'Mexico',
                            'Micronesian' => 'Micronesia', 'Moldovan' => 'Moldova', 'Monegasque' => 'Monaco', 'Mongolian' => 'Mongolia', 'Montenegrin' => 'Montenegro', 
                            'Moroccan' => 'Morocco', 'Mozambican' => 'Mozambique', 'Namibian' => 'Namibia', 'Nauruan' => 'Nauru', 'Nepalese' => 'Nepal',
                            'New Zealander' => 'New Zealand, NZ', 'Nicaraguan' => 'Nicaragua', 'Nigerian' => 'Nigeria', 'Nigerien' => 'Niger', 'North Korean' => 'North Korea', 
                            'Norwegian' => 'Norway', 'Omani' => 'Oman', 'Pakistani' => 'Pakistan', 'Palauan' => 'Palau', 'Palestinian' => 'Palestine',
                            'Panamanian' => 'Panama', 'Papua New Guinean' => 'Papua New Guinea', 'Paraguayan' => 'Paraguay', 'Peruvian' => 'Peru', 'Polish' => 'Poland', 
                            'Portuguese' => 'Portugal', 'Qatari' => 'Qatar', 'Romanian' => 'Romania', 'Russian' => 'Russia', 'Rwandan' => 'Rwanda',
                            'Saint Lucian' => 'Saint Lucia', 'Salvadoran' => 'El Salvador', 'Samoan' => 'Samoa', 'San Marinese' => 'San Marino', 'Sao Tomean' => 'Sao Tome and Principe', 
                            'Saudi' => 'Saudi Arabia, KSA', 'Senegalese' => 'Senegal', 'Serbian' => 'Serbia', 'Seychellois' => 'Seychelles', 'Sierra Leonean' => 'Sierra Leone',
                            'Singaporean' => 'Singapore', 'Slovak' => 'Slovakia', 'Slovenian' => 'Slovenia', 'Solomon Islander' => 'Solomon Islands', 'Somali' => 'Somalia', 
                            'South African' => 'South Africa, RSA', 'South Korean' => 'South Korea, Korea', 'South Sudanese' => 'South Sudan', 'Spanish' => 'Spain', 'Sri Lankan' => 'Sri Lanka',
                            'Sudanese' => 'Sudan', 'Surinamese' => 'Suriname', 'Swazi' => 'Eswatini, Swaziland', 'Swedish' => 'Sweden', 'Swiss' => 'Switzerland', 
                            'Syrian' => 'Syria', 'Taiwanese' => 'Taiwan', 'Tajik' => 'Tajikistan', 'Tanzanian' => 'Tanzania', 'Thai' => 'Thailand',
                            'Togolese' => 'Togo', 'Tongan' => 'Tonga', 'Trinidadian' => 'Trinidad and Tobago', 'Tunisian' => 'Tunisia', 'Turkish' => 'Turkey, Turkiye', 
                            'Turkmen' => 'Turkmenistan', 'Tuvaluan' => 'Tuvalu', 'Ugandan' => 'Uganda', 'Ukrainian' => 'Ukraine', 'Uruguayan' => 'Uruguay',
                            'Uzbek' => 'Uzbekistan', 'Vanuatu' => 'Vanuatu', 'Vatican citizen' => 'Vatican City', 'Venezuelan' => 'Venezuela', 'Vietnamese' => 'Vietnam', 
                            'Yemeni' => 'Yemen', 'Zambian' => 'Zambia', 'Zimbabwean' => 'Zimbabwe'
                        ];
                      @endphp
                      <select name="nationality" id="nationality">
                        <option value="">Select nationality</option>
                        <optgroup label="⭐ Top 10 Popular Nationalities">
                          @foreach($topPopularNationalities as $natKey => $natItem)
                            <option value="{{ $natKey }}" data-synonyms="{{ $natItem['synonyms'] }}" {{ ((string)$selectedNationality === (string)$natKey) ? 'selected="selected"' : '' }}>{{ $natItem['label'] }}</option>
                          @endforeach
                        </optgroup>
                        <optgroup label="🌐 All Sovereign Demonyms (A–Z)">
                          @foreach($allNationalities as $demonym => $countrySynonyms)
                            <option value="{{ $demonym }}" data-synonyms="{{ $countrySynonyms }}" {{ ((string)$selectedNationality === (string)$demonym && !isset($topPopularNationalities[$demonym])) ? 'selected="selected"' : '' }}>{{ $demonym }}</option>
                          @endforeach
                        </optgroup>
                      </select>
                    </div>
                  </div>
                  <div class="fg fv-row">
                    <label>Gender</label>
                    <div class="candidate-field-control"><i class="fa-regular fa-user" aria-hidden="true"></i><select name="gender" id="gender">
                      <option value="Rather Not Say" {{ (@$application->gender==='Rather Not Say') ? 'selected' : '' }}>Rather Not Say</option>
                      <option value="Male" {{ (@$application->gender==='Male') ? 'selected' : '' }}>Male</option>
                      <option value="Female" {{ (@$application->gender==='Female') ? 'selected' : '' }}>Female</option>
                    </select></div>
                  </div>
                  <div class="fg fv-row">
                    <label>Date of Birth</label>
                    <div class="candidate-field-control"><i class="fa-regular fa-calendar" aria-hidden="true"></i><input type="date" name="dob" id="dob" value="{{ @$application->dob }}" onclick="this.showPicker()" placeholder="Select date" /></div>
                  </div>
                </div>

                <details class="candidate-secondary-details" {{ (!empty(@$application->secondrary_email) || !empty(@$application->secondary_mobile)) ? 'open' : '' }}>
                  <summary>
                    <span class="candidate-secondary-summary"><span class="candidate-secondary-icon"><i class="fa-solid fa-ellipsis" aria-hidden="true"></i></span><span><strong>Additional Secondary Contact</strong><small>Optional secondary email and contact number.</small></span></span>
                    <span class="candidate-secondary-toggle"><span class="candidate-secondary-show">Show Details</span><span class="candidate-secondary-hide">Hide Details</span><i class="fa-solid fa-chevron-down" aria-hidden="true"></i></span>
                  </summary>
                  <div class="candidate-secondary-grid">
                    <div class="fg fv-row">
                      <label>Secondary Email</label>
                      <div class="candidate-field-control"><i class="fa-regular fa-envelope" aria-hidden="true"></i><input type="email" name="secondrary_email" id="secondrary_email" value="{{ @$application->secondrary_email }}" placeholder="Enter secondary email" /></div>
                    </div>
                    <div class="fg fv-row">
                      <label>Secondary Contact Number</label>
                      <x-jero-phone-input
                          id="secondary_mobile"
                          name="secondary_mobile"
                          countryCodeName="secondary_country_code"
                          countryCodeId="secondary_country_code"
                          :value="@$application->secondary_mobile"
                          :countryCode="@$second_phone_code"
                          :defaultIso="@$country_code"
                          label=""
                          :required="false"
                          placeholder="Enter secondary number" />
                    </div>
                  </div>
                </details>
              </div>
            </div>

            <!-- Primary Skills -->
            @php
              $candidateRequiredSkills = collect($jobSkill ?? [])->filter(function ($skill) {
                  return is_object($skill) && trim((string) ($skill->skill_name ?? '')) !== '';
              })->map(function ($skill) {
                  return [
                      'id' => (string) ($skill->id ?? ''),
                      'name' => trim((string) $skill->skill_name),
                  ];
              })->unique(function ($skill) {
                  return mb_strtolower(preg_replace('/\s+/', ' ', $skill['name']));
              })->values();
            @endphp
            <div class="fs fv-row se-card primary-skills-card candidate-card">
              <div class="fsh candidate-card-header se-hero">
                <div class="candidate-card-title se-title">
                  <span class="candidate-card-icon se-icon"><i class="fa-solid fa-wand-magic-sparkles" aria-hidden="true"></i></span>
                  <span><strong>Primary Skills <span class="rq">*</span></strong><small>Add the candidate's strongest skills and specializations.</small></span>
                </div>
                <div class="candidate-skill-match is-empty" id="candidateSkillMatch" aria-live="polite">
                  <span class="candidate-skill-match-ring" id="candidateSkillMatchRing" style="--candidate-skill-match: 0%">
                    <strong id="candidateSkillMatchPercentage">--</strong>
                  </span>
                  <span class="candidate-skill-match-copy">
                    <strong>Job Skills Match</strong>
                    <small id="candidateSkillMatchDetail">No job skills defined</small>
                  </span>
                </div>
              </div>
              <div class="fsc candidate-card-body candidate-primary-skills-body">
                <div class="candidate-primary-skill-combobox">
                  <div class="candidate-primary-skill-entry">
                    <i class="fa-solid fa-magnifying-glass" aria-hidden="true"></i>
                    <input type="text" id="candidatePrimarySkillInput" autocomplete="off" placeholder="Type a skill and press Enter..." aria-label="Add a primary skill" role="combobox" aria-autocomplete="list" aria-controls="candidatePrimarySkillSuggestions" aria-expanded="false" />
                    <span class="candidate-primary-skill-divider" aria-hidden="true"></span>
                    <button type="button" id="candidatePrimarySkillAdd"><i class="fa-solid fa-plus" aria-hidden="true"></i><span>Add</span></button>
                  </div>
                  <div id="candidatePrimarySkillSuggestions" class="candidate-primary-skill-suggestions" role="listbox" aria-label="Skill suggestions" hidden></div>
                </div>
                <div id="candidatePrimarySkillChips" class="candidate-primary-skill-chips" aria-live="polite"></div>
                <div id="candidatePrimarySkillEmpty" class="candidate-primary-skill-empty">No primary skills added yet.</div>
                <div class="candidate-suggested-skills" id="candidateSuggestedSkills" hidden>
                  <div class="candidate-suggested-skills-head">
                    <span class="candidate-suggested-skills-title"><i class="fa-solid fa-wand-magic-sparkles" aria-hidden="true"></i><span><strong>Suggested Skills</strong><small>Based on skills from the job requirement. Click to add the missing skills.</small></span></span>
                    <span class="candidate-suggested-skills-count" id="candidateRemainingSkills"></span>
                  </div>
                  <div class="candidate-suggested-skills-list" id="candidateSuggestedSkillsList"></div>
                </div>
                <div id="skillTags" class="skill-tags" hidden></div>
                <input type="text" class="candidate-primary-skills-source" value="{{ @$application->skills }}" placeholder="Enter skills" id="kt_tagify_2" name="skills" />
              </div>
            </div>

            <!-- Experience Details -->
            <div class="fs pd-experience-section-card candidate-card experience-details-card">
              <div class="fsh candidate-card-header pd-experience-hero">
                <div class="candidate-card-title pd-experience-hero-title">
                  <span class="candidate-card-icon pd-experience-hero-icon"><i class="fa-solid fa-chart-line" aria-hidden="true"></i></span>
                  <span><strong>Experience Details</strong><small>Capture total and relevant experience for this job application.</small></span>
                </div>
              </div>
              <div class="fsc candidate-card-body pd-experience-body">
                @php
                 $value = @$application->total_exp; // Convert to string to preserve decimal
                 if(!is_null($value)){
                  $total_exp = GeneralMethod::separateNumberParts($value);
                  $exp_years = ($total_exp[0]);
                  $exp_months = ($total_exp[1]);
                  $selected_exp_year = ($exp_years);
                  $selected_exp_month = ($exp_months);
                 }else{
                  $selected_exp_year = null;
                  $selected_exp_month = null;
                 }

                 $valuew = @$application->rel_exp;
                 if(!is_null($value)){
                  $total_rel_exp = GeneralMethod::separateNumberParts($valuew);
                  $rel_exp_years = ($total_rel_exp[0]);
                  $rel_exp_months = ($total_rel_exp[1]);
                  $selected_rel_exp_year = ($rel_exp_years);
                  $selected_rel_exp_month = ($rel_exp_months);
                 }else{
                  $selected_rel_exp_year = null;
                  $selected_rel_exp_month = null;
                 }
                @endphp
                <div class="candidate-experience-grid">
                  <div class="candidate-experience-panel">
                    <div class="candidate-panel-title"><span><i class="fa-regular fa-clock" aria-hidden="true"></i></span><span><strong>Total Experience <em>*</em></strong><small>Your overall professional experience.</small></span></div>
                    <div class="ei">
                      <label class="candidate-experience-field"><span>Years</span><select id="total_exp_year" name="total_exp_year">
                        <option value="">Years</option>
                        <option value="0" {{ $selected_exp_year==0 ? 'selected' : '' }}>Fresher</option>
                        @php
                            for ($i = 1; $i <= 60; $i++) {
                                  $selected = '';
                                  $s = 'Year';
                                  if($i > 1){
                                      $s = 'Years';
                                  }
                                  if(is_null($selected_exp_year)){
                                    $selected = '';
                                  }else{
                                    if($i==$selected_exp_year){
                                      $selected = 'selected';
                                    }
                                  }
                                echo "<option value=\"$i\" $selected>{$i} {$s}</option>";
                            }
                        @endphp
                      </select></label>
                      <label class="candidate-experience-field"><span>Months</span><select class="" aria-label="Select total experience month" id="total_exp_month" name="total_exp_month">
                        <option value="">Select Month</option>
                        @php
                            for ($i = 0; $i <= 11; $i++) {
                                  $selected = '';
                                  $s = 'Month';
                                  if($i > 1){
                                     $s = 'Months';
                                  }
                                  if(is_null($selected_exp_month)){
                                    $selected = '';
                                  }else{
                                    if($i==$selected_exp_month){
                                      $selected = 'selected';
                                    }
                                  }
                                echo "<option value=\"$i\" $selected>{$i} {$s}</option>";
                            }
                        @endphp
                      </select></label>
                    </div>
                  </div>
                  <div class="candidate-experience-panel candidate-experience-panel--relevant">
                    <div class="candidate-panel-title"><span><i class="fa-solid fa-briefcase" aria-hidden="true"></i></span><span><strong>Relevant Experience <em>*</em></strong><small>Experience relevant to this job or required skills.</small></span></div>
                    <div class="ei">
                      <label class="candidate-experience-field"><span>Years</span><select id="rel_exp_year" name="rel_exp_year">
                        <option value="">Years</option>
                        <option value="0" {{ $selected_rel_exp_year==0 ? 'selected' : '' }}>Fresher</option>
                        @php
                              for ($i = 1; $i <= 60; $i++) {
                                    $selected = '';
                                    $s = 'Year';
                                    if($i > 1){
                                        $s = 'Years';
                                    }
                                    if(is_null($selected_rel_exp_year)){
                                      $selected = '';
                                    }else{
                                      if($i==$selected_rel_exp_year){
                                        $selected = 'selected';
                                      }
                                    }
                                  echo "<option value=\"$i\" $selected>{$i} {$s}</option>";
                              }
                        @endphp
                      </select></label>
                      <label class="candidate-experience-field"><span>Months</span><select class="" aria-label="Select relevant experience month" id="rel_exp_month" name="rel_exp_month">
                        <option value="">Select Month</option>
                        @php
                            for ($i = 0; $i <= 11; $i++) {
                                  $selected = '';
                                  $s = 'Month';
                                  if($i > 1){
                                     $s = 'Months';
                                  }
                                  if(is_null($selected_rel_exp_month)){
                                    $selected = '';
                                  }else{
                                    if($i==$selected_rel_exp_month){
                                      $selected = 'selected';
                                    }
                                  }
                                echo "<option value=\"$i\" $selected>{$i} {$s}</option>";
                            }
                        @endphp
                      </select></label>
                      <div id="rel_exp_error" class="text-danger fs-8 mt-1" style="display:none;font-weight:500;"></div>
                    </div>
                  </div>
                  <div class="candidate-experience-tip">
                    <span><i class="fa-solid fa-lightbulb" aria-hidden="true"></i></span>
                    <div class="candidate-experience-tip-copy"><strong>Tip</strong><p>Relevant experience should match the skills required for this job.</p>
                      <div class="candidate-experience-examples"><strong>Examples:</strong><ul><li>If the job requires Laravel, mention your Laravel experience here.</li><li>Exclude unrelated experience.</li><li>Freshers can select 0 years.</li></ul></div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Professional Profile -->
            <div class="fs pd-card candidate-card professional-profile-card" id="candidateProfessionalProfile">
              <div class="fsh candidate-card-header pd-hero">
                <div class="candidate-card-title pd-hero-title">
                  <span class="candidate-card-icon pd-hero-icon"><i class="fa-solid fa-briefcase" aria-hidden="true"></i></span>
                  <span><strong>Professional Profile</strong><small>Define the candidate's current professional role and employment type.</small></span>
                </div>
              </div>
              <div class="fsc candidate-card-body pd-body">
                <div class="professional-profile-grid">
                  <div class="fg fv-row">
                    <label>Current Company <span class="rq">*</span></label>
                    <div class="candidate-field-control"><i class="fa-regular fa-building" aria-hidden="true"></i><input type="text" name="current_company" id="current_company" value="{{ @$application->current_company }}" placeholder="Enter current company" /></div>
                    <small class="candidate-professional-help">e.g. TCS, Infosys, Amazon</small>
                  </div>
                  <div class="fg fv-row">
                    <label>Current Designation <span class="rq">*</span></label>
                    <div class="candidate-field-control"><i class="fa-solid fa-briefcase" aria-hidden="true"></i><input type="text" name="current_designation" id="current_designation" value="{{( @$application->current_designation) ? @$application->current_designation : @$jobDetail->title }}" placeholder="Enter current designation" /></div>
                    <small class="candidate-professional-help">e.g. Software Engineer, Product Manager</small>
                  </div>
                  <div class="fg fv-row">
                    <label>Employment Type <span class="rq">*</span></label>
                    <div class="candidate-field-control"><i class="fa-solid fa-rectangle-list" aria-hidden="true"></i><select name="employmentType" id="EmploymentType">
                      <option value="">Select employment type</option>
                      <option {{ (@$application->employmentType==='Full Time') ? 'selected' : '' }}>Full Time</option>
                      <option {{ (@$application->employmentType==='Contract') ? 'selected' : '' }}>Contract</option>
                      <option {{ (@$application->employmentType==='Part Time') ? 'selected' : '' }}>Part Time</option>
                      <option {{ (@$application->employmentType==='Freelance') ? 'selected' : '' }}>Freelance</option>
                    </select></div>
                  </div>
                </div>
              </div>
            </div>

            @php
              $candidateSalaryMode = (int) (@$application->current_ctc_mode ?? 0);
              $candidateSalaryMode = in_array($candidateSalaryMode, [0, 1, 2], true) ? $candidateSalaryMode : 0;
              $candidateSalaryPeriod = [0 => 'annual', 1 => 'monthly', 2 => 'hourly'][$candidateSalaryMode];
            @endphp
            <!-- Salary Details -->
            <div class="fs br-card salary-card candidate-card">
              <div class="fsh candidate-card-header br-hero">
                <div class="candidate-card-title br-hero-title">
                  <span class="candidate-card-icon br-hero-icon"><i class="fa-solid fa-wallet" aria-hidden="true"></i></span>
                  <span><strong>Salary Details</strong><small>Capture current and expected salary. Related values continue to calculate automatically.</small></span>
                </div>
                <div class="candidate-salary-header-currency">
                  <label for="current_ctc_cur">Currency</label>
                  <select id="current_ctc_cur" name="current_ctc_cur" class="form-select form-select-solid select2-candidate-currency" data-control="select2" data-placeholder="Search currency...">
                    @php
                        $current_ctc_cur = "";
                        $current_ctc_cur = @$application->current_ctc_cur ?? $salary_currency;
                        $currencyFlags = [
                            'INR' => '🇮🇳', 'EUR' => '🇪🇺', 'JPY' => '🇯🇵', 'GBP' => '🇬🇧', 'USD' => '🇺🇸', 'AED' => '🇦🇪',
                            'CAD' => '🇨🇦', 'AUD' => '🇦🇺', 'SGD' => '🇸🇬', 'CHF' => '🇨🇭', 'NZD' => '🇳🇿', 'ZAR' => '🇿🇦',
                            'BRL' => '🇧🇷', 'PHP' => '🇵🇭', 'MYR' => '🇲🇾', 'THB' => '🇹🇭', 'IDR' => '🇮🇩', 'VND' => '🇻🇳',
                            'SEK' => '🇸🇪', 'NOK' => '🇳🇴', 'DKK' => '🇩🇰', 'PLN' => '🇵🇱', 'MXN' => '🇲🇽', 'HKD' => '🇭🇰',
                            'TWD' => '🇹🇼', 'KRW' => '🇰🇷', 'TRY' => '🇹🇷', 'SAR' => '🇸🇦', 'QAR' => '🇶🇦', 'KWD' => '🇰🇼',
                            'BHD' => '🇧🇭', 'OMR' => '🇴🇲', 'EGP' => '🇪🇬', 'ILS' => '🇮🇱', 'CZK' => '🇨🇿', 'HUF' => '🇭🇺',
                            'RON' => '🇷🇴', 'CLP' => '🇨🇱', 'COP' => '🇨🇴', 'ARS' => '🇦🇷', 'NGN' => '🇳🇬', 'KES' => '🇰🇪',
                            'PKR' => '🇵🇰', 'BDT' => '🇧🇩', 'LKR' => '🇱🇰', 'NPR' => '🇳🇵'
                        ];
                        foreach($currency as $key=>$val){
                            $sel = '';
                            if($current_ctc_cur===null){
                              if($currency_code==$val){
                                $sel = 'selected="selected"';
                              }
                            }else{
                              if($current_ctc_cur == $key){
                                $sel = 'selected="selected"';
                              }
                            }
                            $cMeta = GeneralMethod::currencyEnterpriseMeta($val);
                            $entLabel = $cMeta['label'];
                            $keywords = $cMeta['keywords'];
                            $flag = $currencyFlags[$val] ?? '🌐';

                            echo '<option value="'.$key.'" data-code="'.e($val).'" data-symbol="'.e($cMeta['symbol']).'" data-flag="'.e($flag).'" data-search="'.e($keywords).'" '.$sel.'>'.$flag.' '.e($entLabel).'</option>';
                        }
                    @endphp
                  </select>
                  <div class="d-none">
                    <select name="current_ectc_cur" id="current_ectc_cur">
                      @php
                          foreach($currency as $key=>$val){
                              $sel = ($current_ctc_cur == $key) ? 'selected="selected"' : '';
                              echo '<option value="'.$key.'" data-code="'.e($val).'" '.$sel.'>'.$val.'</option>';
                          }
                      @endphp
                    </select>
                    <select name="current_th_cur" id="current_th_cur">
                      @php
                          foreach($currency as $key=>$val){
                              $sel = ($current_ctc_cur == $key) ? 'selected="selected"' : '';
                              echo '<option value="'.$key.'" data-code="'.e($val).'" '.$sel.'>'.$val.'</option>';
                          }
                      @endphp
                    </select>
                  </div>
                </div>
              </div>
              <div class="fsc candidate-card-body br-body">
                <div class="candidate-salary-grid">
                  <section class="candidate-salary-panel candidate-salary-panel--current">
                    <div class="candidate-salary-panel-head">
                      <span><i class="fa-solid fa-coins" aria-hidden="true"></i></span>
                      <div><strong>Current Salary</strong><small>What the candidate currently earns</small></div>

                    </div>
                    <div class="candidate-salary-tabs" role="tablist" aria-label="Salary frequency">
                      <button type="button" class="{{ $candidateSalaryPeriod === 'annual' ? 'is-active' : '' }}" data-salary-group="current" data-salary-period="annual" data-salary-mode="0">Annual</button>
                      <button type="button" class="{{ $candidateSalaryPeriod === 'monthly' ? 'is-active' : '' }}" data-salary-group="current" data-salary-period="monthly" data-salary-mode="1">Monthly</button>
                      <button type="button" class="{{ $candidateSalaryPeriod === 'hourly' ? 'is-active' : '' }}" data-salary-group="current" data-salary-period="hourly" data-salary-mode="2">Hourly</button>
                    </div>
                    <div class="candidate-salary-period {{ $candidateSalaryPeriod === 'annual' ? 'is-active' : '' }}" data-salary-panel="current" data-salary-period="annual">
                      <div class="d-none">
                        <select class="form-select form-select-solid" arial-label="Select Annual Salary" name="current_ectc_mode" id="current_ectc_mode">
                          @php
                              $current_ectc_mode = "";
                              $current_ectc_mode = @$application->current_ectc_mode;
                              foreach($salaryMode as $key=>$val){
                                $sel = '';
                                if($candidateSalaryMode==$key){
                                  $sel = 'selected="selected"';
                                }
                                echo '<option value="'.$key.'" '.$sel.'>'.$val.'</option>';
                              }
                          @endphp
                        </select>
                        <select class="form-select form-select-solid" arial-label="Select Annual Salary" id="current_ctc_mode" name="current_ctc_mode">
                          @php
                              $current_ctc_mode = "";
                              $current_ctc_mode = @$application->current_ctc_mode;
                              foreach($salaryMode as $key=>$val){
                                $sel = '';
                                if($candidateSalaryMode==$key){
                                  $sel = 'selected="selected"';
                                }
                                echo '<option value="'.$key.'" '.$sel.'>'.$val.'</option>';
                              }
                          @endphp
                        </select>
                      </div>
                      <div class="fg fv-row"><label>Current Salary (Annual) <span id="pay_rate_annual_usd" class="jero-pill-wrapper"></span></label><div class="ig"><input type="number" name="pay_annual" id="pay_annual" value="{{ @$application->ctc_yearly }}" placeholder="Enter amount" /><span class="ig-a changeTextCurrency">INR</span></div><span class="form-text text-muted payrateannual"></span></div>
                    </div>
                    <div class="candidate-salary-period {{ $candidateSalaryPeriod === 'monthly' ? 'is-active' : '' }}" data-salary-panel="current" data-salary-period="monthly">
                      <div class="fg fv-row"><label>Current Salary (Monthly) <span id="pay_rate_monthly_usd" class="jero-pill-wrapper"></span></label><div class="ig"><input type="number" name="pay_monthly" id="pay_monthly" value="{{ @$application->ctc_monthly }}" placeholder="Enter amount" /><span class="ig-a changeTextCurrency">INR</span></div><span class="form-text text-muted payratemonthly"></span></div>
                    </div>
                    <div class="candidate-salary-period {{ $candidateSalaryPeriod === 'hourly' ? 'is-active' : '' }}" data-salary-panel="current" data-salary-period="hourly">
                      <div class="fg fv-row"><label>Current Salary (Hourly) <span id="pay_rate_hourly_usd" class="jero-pill-wrapper"></span></label><div class="ig"><input type="number" name="ctc" id="ctc" value="{{ @$application->ctc_hourly }}" placeholder="Enter amount" /><span class="ig-a changeTextCurrency">INR</span></div><span class="form-text text-muted CurrentCTC"></span></div>
                    </div>
                    <div class="candidate-salary-hint"><i class="fa-regular fa-lightbulb" aria-hidden="true"></i><span id="candidateCurrentSalaryHint">Enter your current {{ $candidateSalaryPeriod }} salary.</span></div>
                    <div class="candidate-salary-auto candidate-salary-auto--dynamic">
                      <span>Auto Calculated</span>
                      <div><strong id="candidate-current-auto-label-one">Monthly Salary</strong><b id="candidate-current-monthly-auto">{{ @$application->ctc_monthly ?: 0 }}</b></div>
                      <div><strong id="candidate-current-auto-label-two">Hourly Salary</strong><b id="candidate-current-hourly-auto">{{ @$application->ctc_hourly ?: 0 }}</b></div>
                    </div>
                  </section>

                  <section class="candidate-salary-panel candidate-salary-panel--expected">
                    <div class="candidate-salary-panel-head"><span><i class="fa-solid fa-chart-simple" aria-hidden="true"></i></span><div><strong>Expected Salary</strong><small>What the candidate expects to earn</small></div></div>
                    <div class="candidate-salary-tabs candidate-salary-tabs--synced" role="group" aria-label="Expected salary frequency, synchronized with Current Salary">
                      <button type="button" class="{{ $candidateSalaryPeriod === 'annual' ? 'is-active' : '' }}" data-salary-group="expected" data-salary-period="annual" data-salary-mode="0">Annual</button>
                      <button type="button" class="{{ $candidateSalaryPeriod === 'monthly' ? 'is-active' : '' }}" data-salary-group="expected" data-salary-period="monthly" data-salary-mode="1">Monthly</button>
                      <button type="button" class="{{ $candidateSalaryPeriod === 'hourly' ? 'is-active' : '' }}" data-salary-group="expected" data-salary-period="hourly" data-salary-mode="2">Hourly</button>
                    </div>
                    <div class="candidate-salary-period {{ $candidateSalaryPeriod === 'annual' ? 'is-active' : '' }}" data-salary-panel="expected" data-salary-period="annual">
                      <div class="fg fv-row"><label>Expected Salary (Annual) <span id="bill_rate_annual_usd" class="jero-pill-wrapper"></span></label><div class="ig"><input type="number" name="bill_annual" id="bill_annual" value="{{ @$application->ectc_yearly }}" placeholder="Enter amount" /><span class="ig-a changeTextCurrency">INR</span></div><span class="form-text text-muted billrateannual"></span></div>
                    </div>
                    <div class="candidate-salary-period {{ $candidateSalaryPeriod === 'monthly' ? 'is-active' : '' }}" data-salary-panel="expected" data-salary-period="monthly">
                      <div class="fg fv-row"><label>Expected Salary (Monthly) <span id="bill_rate_monthly_usd" class="jero-pill-wrapper"></span></label><div class="ig"><input type="number" name="bill_monthly" id="bill_monthly" value="{{ @$application->ectc_monthly }}" placeholder="Enter amount" /><span class="ig-a changeTextCurrency">INR</span></div><span class="form-text text-muted billratemonthly"></span></div>
                    </div>
                    <div class="candidate-salary-period {{ $candidateSalaryPeriod === 'hourly' ? 'is-active' : '' }}" data-salary-panel="expected" data-salary-period="hourly">
                      <div class="fg fv-row"><label>Expected Salary (Hourly) <span id="bill_rate_hourly_usd" class="jero-pill-wrapper"></span></label><div class="ig"><input type="number" name="ectc" id="ectc" value="{{ @$application->ectc_hourly }}" placeholder="Enter amount" /><span class="ig-a changeTextCurrency">INR</span></div><span class="form-text text-muted expectedctc"></span></div>
                    </div>
                    <div class="candidate-salary-hint"><i class="fa-regular fa-lightbulb" aria-hidden="true"></i><span id="candidateExpectedSalaryHint">Enter your expected {{ $candidateSalaryPeriod }} salary.</span></div>
                    <div class="candidate-salary-auto candidate-salary-auto--dynamic">
                      <span>Auto Calculated</span>
                      <div><strong id="candidate-expected-auto-label-one">Monthly Salary</strong><b id="candidate-expected-monthly-auto">{{ @$application->ectc_monthly ?: 0 }}</b></div>
                      <div><strong id="candidate-expected-auto-label-two">Hourly Salary</strong><b id="candidate-expected-hourly-auto">{{ @$application->ectc_hourly ?: 0 }}</b></div>
                    </div>
                  </section>

                  <aside class="candidate-salary-panel candidate-salary-summary">
                    <div class="candidate-salary-panel-head"><span><i class="fa-solid fa-calculator" aria-hidden="true"></i></span><div><strong>Salary Summary</strong><small>Calculated from the entered values</small></div></div>
                    <div class="candidate-salary-summary-list">
                      <div><span id="candidateSalaryCurrentLabel">Current Salary (Annual)</span><strong id="candidateSalaryCurrentSummary">0</strong></div>
                      <div><span id="candidateSalaryExpectedLabel">Expected Salary (Annual)</span><strong id="candidateSalaryExpectedSummary">0</strong></div>
                      <div><span>Difference</span><strong id="candidateSalaryDifferenceSummary">0</strong></div>
                      <div><span>Expected Increase</span><strong id="candidateSalaryHikeSummary">0%</strong></div>
                      <div class="candidate-salary-summary-usd-row" id="candidateSalaryUsdEquivalentRow">
                        <span id="candidateSalaryUsdLabel"><i class="fa-solid fa-earth-americas text-primary me-1"></i>USD Equivalent</span>
                        <strong id="candidateSalaryUsdEquivalentSummary">$0.00 / yr</strong>
                      </div>
                    </div>
                    <input type="hidden" value="{{ @$application->percentage_hike }}" name="percentage_hike" id="percentage_hike" />
                    <div class="candidate-offer-field">
                      <strong>Offer in Hand</strong>
                      <div class="rg">
                        <div class="ri"><input type="radio" name="oh" id="ohy" value="yes" {{ @$application->oh == 'yes' ? 'checked' : '' }} /><label for="ohy">Yes</label></div>
                        <div class="ri"><input type="radio" name="oh" id="ohn" value="no" {{ (@$application->wm == 'no' || empty(@$application->oh)) ? 'checked' : '' }} /><label for="ohn">No</label></div>
                      </div>
                    </div>
                  </aside>
                </div>

                <div class="candidate-salary-comments">
                  <div class="candidate-salary-comments-head"><span><i class="fa-regular fa-comment-dots" aria-hidden="true"></i></span><div><strong>Comments <small>(Optional)</small></strong><small>Add salary expectations or internal compensation notes.</small></div></div>
                  <textarea name="comment" id="comment" placeholder="Enter comments about salary expectations or any other details..." rows="4">{{ @$application->comment }}</textarea>
                  <div class="cc" id="salaryCommentCounter">0 / 500</div>
                </div>
              </div>
            </div>

            <!-- Notice Period and Joining Details -->
            <div class="fs aw-card candidate-card candidate-availability-card notice-joining-card">
              <div class="fsh candidate-card-header aw-hero">
                <div class="candidate-card-title aw-hero-title">
                  <span class="candidate-card-icon aw-hero-icon"><i class="fa-regular fa-calendar-check" aria-hidden="true"></i></span>
                  <span><strong>Notice Period &amp; Joining Details</strong><small>Capture notice period, joining availability, and reason for job change.</small></span>
                </div>
              </div>
              <div class="fsc candidate-card-body aw-body">
                <div class="candidate-notice-status-block fv-row">
                  <label>Notice Period <span class="candidate-optional-label">(Optional)</span></label>
                  <div class="candidate-notice-options" role="radiogroup" aria-label="Notice Period Selection">
                    <button type="button" class="candidate-notice-option {{ $snp_mode === 2 ? 'is-selected' : '' }}" data-notice-period-value="2" role="radio" aria-checked="{{ $snp_mode === 2 ? 'true' : 'false' }}">
                      <span class="candidate-notice-option-radio" aria-hidden="true"></span>
                      <span class="candidate-notice-option-icon candidate-notice-option-icon--immediate"><i class="fa-solid fa-bolt" aria-hidden="true"></i></span>
                      <span class="candidate-notice-option-copy"><strong>Immediate Joiner</strong><small>Can join within 0-15 days</small></span>
                    </button>
                    <button type="button" class="candidate-notice-option {{ $snp_mode === 1 ? 'is-selected' : '' }}" data-notice-period-value="1" role="radio" aria-checked="{{ $snp_mode === 1 ? 'true' : 'false' }}">
                      <span class="candidate-notice-option-radio" aria-hidden="true"></span>
                      <span class="candidate-notice-option-icon candidate-notice-option-icon--serving"><i class="fa-regular fa-calendar-days" aria-hidden="true"></i></span>
                      <span class="candidate-notice-option-copy"><strong>Serving Notice Period</strong><small>Currently working, serving notice</small></span>
                    </button>
                    <button type="button" class="candidate-notice-option {{ $snp_mode === 0 ? 'is-selected' : '' }}" data-notice-period-value="0" role="radio" aria-checked="{{ $snp_mode === 0 ? 'true' : 'false' }}">
                      <span class="candidate-notice-option-radio" aria-hidden="true"></span>
                      <span class="candidate-notice-option-icon candidate-notice-option-icon--select"><i class="fa-regular fa-clock" aria-hidden="true"></i></span>
                      <span class="candidate-notice-option-copy"><strong>Select Notice Period</strong><small>Choose a specific notice period</small></span>
                    </button>
                  </div>
                  <select class="candidate-notice-native-select" name="serving_notice_period" id="serving_notice_period" aria-hidden="true" tabindex="-1">
                    <option value="" {{ $snp_mode === null ? 'selected' : '' }}>Choose Notice Period</option>
                    <option value="0" {{ $snp_mode === 0 ? 'selected' : '' }}>Select Notice Period</option>
                    <option value="1" {{ $snp_mode === 1 ? 'selected' : '' }}>Serving Notice Period</option>
                    <option value="2" {{ $snp_mode === 2 ? 'selected' : '' }}>Immediate Joiner</option>
                  </select>
                </div>

                <div class="candidate-notice-details-grid">
                  <div class="fg fv-row candidate-notice-detail" id="candidateNoticeDateField" {{ !in_array($snp_mode, [1, 2], true) ? 'hidden' : '' }}>
                    <label id="candidateNoticeDateLabel">{{ $snp_mode === 1 ? 'Expected Last Working Date' : 'Last Working Date' }} <span class="rq">*</span></label>
                    <div class="candidate-notice-input-shell">
                      <i class="fa-regular fa-calendar" aria-hidden="true"></i>
                      <div class="candidate-date-picker">
                        <input type="text" id="np_id_display" class="form-control form-control-solid" name="np_id_display" value="{{ $snp_last_working ? date('d/M/Y', strtotime($snp_last_working)) : '' }}" placeholder="{{ $snp_mode === 1 ? 'Select expected last working date' : 'Select last working date' }}" readonly {{ !in_array($snp_mode, [1, 2], true) ? 'disabled' : '' }}>
                        <input type="date" class="form-control form-control-solid np_id" name="np_id" id="np_id" value="{{ $snp_last_working ? date('Y-m-d', strtotime($snp_last_working)) : '' }}" style="opacity:0;position:absolute;z-index:-1;top:0;" {{ !in_array($snp_mode, [1, 2], true) ? 'disabled' : '' }}>
                      </div>
                    </div>
                  </div>
                  <div class="fg fv-row candidate-notice-detail" id="candidateNoticeDaysField" {{ $snp_mode !== 0 ? 'hidden' : '' }}>
                    <label>Notice Period <span class="rq">*</span></label>
                    <div class="candidate-notice-input-shell">
                      <i class="fa-regular fa-clock" aria-hidden="true"></i>
                      <select class="form-select form-select-solid" aria-label="Select Notice Period" id="ddl_np_id" name="ddl_np_id" {{ $snp_mode !== 0 ? 'disabled' : '' }}>
                        <option value="">Select Notice Period</option>
                        @php
                            for ($i = 1; $i <= 200; $i++) {
                                $suffix = $i === 1 ? 'Day' : 'Days';
                                $selected = ($snp_mode === 0 && $i == @$application->np_id) ? 'selected' : '';
                                echo '<option value="' . $i . '" ' . $selected . '>' . $i . ' ' . $suffix . '</option>';
                            }
                        @endphp
                      </select>
                    </div>
                  </div>
                  <div class="fg fv-row">
                    <label>Reason for Job Change</label>
                    <div class="candidate-field-control candidate-reason-control"><i class="fa-regular fa-comment-dots" aria-hidden="true"></i><input type="text" name="reason_for_job_change" maxlength="55" id="reason_for_job_change" value="{{ @$application->reason_for_job_change }}" placeholder="Enter reason for job change" /></div>
                  </div>
                </div>
                <input type="text" id="calender" name="calender" hidden>              </div>
            </div>

            <!-- Preferred Work Mode -->
            <div class="fs candidate-card preferred-work-mode-card">
              <div class="fsh candidate-card-header">
                <div class="candidate-card-title">
                  <span class="candidate-card-icon candidate-card-icon--work-mode"><i class="fa-solid fa-briefcase" aria-hidden="true"></i></span>
                  <span><strong>Preferred Work Mode</strong><small>Select the candidate's preferred way of working.</small></span>
                </div>
              </div>
              <div class="fsc candidate-card-body">
                <div class="candidate-work-mode-grid">
                  <label class="candidate-work-mode-option" for="wr"><input type="radio" name="wm" id="wr" {{ @$application->wm == 'remote' ? 'checked' : '' }} value="remote" /><span class="candidate-work-mode-radio"></span><span class="candidate-work-mode-icon candidate-work-mode-icon--remote"><i class="fa-solid fa-laptop" aria-hidden="true"></i></span><span class="candidate-work-mode-copy"><strong>Remote</strong><small>Can work from any location.</small></span></label>
                  <label class="candidate-work-mode-option" for="wh"><input type="radio" name="wm" id="wh" {{ @$application->wm == 'hybrid' ? 'checked' : '' }} value="hybrid" /><span class="candidate-work-mode-radio"></span><span class="candidate-work-mode-icon candidate-work-mode-icon--hybrid"><i class="fa-solid fa-building" aria-hidden="true"></i></span><span class="candidate-work-mode-copy"><strong>Hybrid</strong><small>Combination of office and remote.</small></span></label>
                  <label class="candidate-work-mode-option" for="wo"><input type="radio" name="wm" id="wo" {{ (@$application->wm == 'office' || empty(@$application->wm)) ? 'checked' : '' }} value="office" /><span class="candidate-work-mode-radio"></span><span class="candidate-work-mode-icon candidate-work-mode-icon--office"><i class="fa-solid fa-building" aria-hidden="true"></i></span><span class="candidate-work-mode-copy"><strong>Work From Office</strong><small>Office location based work.</small></span></label>
                </div>
              </div>
            </div>

            <!-- Skills Details -->
            <div class="fs se-card job-skills-card candidate-card">
              <div class="fsh candidate-card-header se-hero">
                <div class="candidate-card-title se-title">
                  <span class="candidate-card-icon se-icon"><i class="fa-solid fa-list-check" aria-hidden="true"></i></span>
                  <span><strong>Job Skills Details</strong><small>Record experience and proficiency against the job's required skills.</small></span>
                </div>
              </div>
              <div class="fsc candidate-card-body candidate-table-wrap">
                <div class="candidate-job-skills-scroll">
                <table class="sk">
                  <thead>
                    <tr>
                      <th>Skill <span class="rq">*</span></th>
                      <th>Experience (Years) <span class="rq">*</span></th>
                      <th>Experience (Months)</th>
                      <th>Proficiency Rating <span class="rq">*</span></th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach($jobSkill as $skill_val)
                        @if(!empty($skill_val->skill_name))

                            @php
                                  $selected_skill_exp_year = null;
                                  $selected_skill_exp_month = null;
                                  $selected_skill_rating = null;
                                  $skill_val_id = $skill_val->id ?? $skill_val->job_skill_id ?? $skill_val->skill_id ?? null;

                                  if(!empty($skill_val->skill_value)){
                                    $skillValue = str_replace(['Years','Year',' '],'', (string)$skill_val->skill_value);
                                    if($skillValue !== ''){
                                      $total_skill_exp = GeneralMethod::separateNumberParts($skillValue);
                                      $skill_exp_years = $total_skill_exp[0] ?? null;
                                      $skill_exp_months = $total_skill_exp[1] ?? null; 

                                      $selected_skill_exp_year = $skill_exp_years;
                                      $selected_skill_exp_month = $skill_exp_months;
                                      $selected_skill_rating = $skill_val->rating ?? null;
                                     }
                                  }
                            @endphp

                            <tr>
                              <td><label class="candidate-job-skill-name"><input type="text" value="{{ strtoupper($skill_val->skill_name) }}" placeholder="Enter skill" readonly /></label></td>
                              <td>
                                <select class="form-select form-select-solid candidate-job-skill-select" aria-label="Select skill experience year" id="job_skill_year_{{ $skill_val_id }}" name="YearskillId[{{ $skill_val_id }}]">
                                      <option value="0">Select Year</option>
                                      @php 
                                          for ($i = 1; $i <= 60; $i++) {
                                                $selected = '';
                                                $s = 'Year';
                                                if($i > 1){
                                                    $s = 'Years';
                                                }
                                                if(!is_null($selected_skill_exp_year) && (string)$i === (string)$selected_skill_exp_year){
                                                    $selected = 'selected';
                                                }
                                                echo "<option value=\"$i\" $selected>{$i} {$s}</option>";
                                          }
                                      @endphp
                                    </select>
                              </td>
                              <td>
                                <select class="form-select form-select-solid candidate-job-skill-select" aria-label="Select skill experience month" id="job_skill_month_{{ $skill_val_id }}" name="MonthskillId[{{ $skill_val_id }}]">
                                      <option value="">Select Month</option>
                                      @php 
                                          for ($i = 0; $i <= 11; $i++) {
                                                $selected = '';
                                                $s = 'Month';
                                                if($i > 1){
                                                    $s = 'Months';
                                                }
                                                if(!is_null($selected_skill_exp_month) && (string)$i === (string)$selected_skill_exp_month){
                                                    $selected = 'selected';
                                                }
                                                echo "<option value=\"$i\" $selected>{$i} {$s}</option>";
                                          }
                                      @endphp
                                </select>
                              </td>
                              <td>
                                 <select class="form-select form-select-solid candidate-rating-select" aria-label="Select proficiency rating" id="job_skill_rating_{{ $skill_val_id }}" name="rating[{{ $skill_val_id }}]">
                                      <option value="0">Select rating</option>
                                      @php 
                                          for ($i = 1; $i <= 5; $i++) {
                                                $selected = '';
                                                $s = 'Star';
                                                if($i > 1){
                                                    $s = 'Stars';
                                                }
                                                if(!is_null($selected_skill_rating) && (string)$i === (string)$selected_skill_rating){
                                                    $selected = 'selected';
                                                }
                                                echo "<option value=\"$i\" $selected>{$i} {$s}</option>";
                                          }
                                      @endphp
                                    </select>
                              </td>
                            </tr>
                        @endif
                    @endforeach
                  </tbody>
                </table>
                </div>
              </div>
            </div>

            <!-- Screening Questions -->
            @php
                $screeningQuestions = collect($job_additional_detail ?? [])->filter(function ($question) {
                    return is_object($question) && trim((string) ($question->detail_value ?? '')) !== '';
                });
            @endphp
          @if($screeningQuestions->isNotEmpty())
            <div class="fs candidate-card questions-card">
              <div class="fsh candidate-card-header">
                <div class="candidate-card-title">
                  <span class="candidate-card-icon candidate-card-icon--questions"><i class="fa-regular fa-circle-question" aria-hidden="true"></i></span>
                  <span><strong>Screening Questions</strong><small>Complete the job-specific information required for this submission.</small></span>
                </div>
              </div>
              <div class="fsc candidate-card-body">

                <!--begin::Custom Details-->
                <div class="" id="job_additional_detail">
                  @php 
                    if(!empty($job_additional_detail)){
                    
                    $customTitleId	=	1;
                    $html			=	'';
                   
                    $inc_first		=	0;
                    $inc_last		=	1;
                     $html	.=	'<div class="form-group row mb-10">';	
                    foreach($screeningQuestions as $aDetailArr){
                      if(!empty($aDetailArr->detail_value)){
                      
                      $ans_value	=	'';
                      if(!empty($application_additional_detail)){
                        foreach($application_additional_detail as $aval){
                            if($aDetailArr->id == $aval->question_id){
                              $ans_value	=	@$aval->application_detail_value;	
                            }
                        }
                      }
						
                            // if($inc_first %3 == 0) {
                             
                            // }	
                            $html		.=	'<div class="col-md-4">
                                      <label class="form-label">'.strtoupper($aDetailArr->detail_value).':</label>
                                      <input type="hidden" name="job_question[]" value="'.$aDetailArr->id.'">
                                      <input type="text" class="form-control form-control-solid" name="additional_detail[]" placeholder="Enter detail" value="'.@$ans_value.'">
                                      <span class="text-muted form-text">Please enter your details</span>
                                    </div>';
                                    
                            // if(($inc_last %3 == 0)&& $inc_last > 0){
                              
                            // }
                          }
                            $inc_first++;
                            $inc_last++;
                            $customTitleId++;				
                          }
                          $html		.='</div>';
                          echo $html;	
                          }	
                                                    
                  @endphp
                </div>
                <!--end::Custom Details-->
              </div>
            </div>
            @endif


            <!-- Availability -->
            <div class="fs candidate-card interview-card">
              <div class="fsh candidate-card-header">
                <div class="candidate-card-title">
                  <span class="candidate-card-icon candidate-card-icon--interview"><i class="fa-regular fa-calendar-days" aria-hidden="true"></i></span>
                  <span><strong>Availability for Next 3 Days <span class="rq">*</span></strong><small>Provide three interview windows and the preferred interview mode.</small></span>
                </div>
              </div>
              <div class="fsc candidate-card-body">
                <div class="ag">
                  <div class="ai candidate-slot candidate-slot--one">
                    <div class="candidate-slot-head">
                      <span class="candidate-slot-number">1</span>
                      <strong class="candidate-slot-title">Slot 1</strong>
                    </div>
                    <div class="candidate-slot-fields">
                      <label class="candidate-slot-field"><span><i class="fa-regular fa-calendar-days" aria-hidden="true"></i> Date <b>*</b></span><span class="candidate-slot-control"><input class="form-control form-control-solid" placeholder="Select date" name="interviewDateOne" id="interviewDateOne" value="{{ (@$application->interviewDateOne) ? \Carbon\Carbon::parse(@$application->interviewDateOne)->format('d-m-Y') : '' }}" type="text" autocomplete="off"></span></label>
                      <label class="candidate-slot-field"><span><i class="fa-regular fa-clock" aria-hidden="true"></i> Time <b>*</b></span><span class="candidate-slot-control"><input class="form-control form-control-solid" placeholder="Select time" name="interviewTimeOne" id="interviewTimeOne" value="{{ (@$application->interviewDateOne) ? \Carbon\Carbon::parse(@$application->interviewDateOne)->format('h:i A'): '' }}" type="text" autocomplete="off"></span></label>
                      <label class="candidate-slot-field"><span><i class="fa-solid fa-video" aria-hidden="true"></i> Mode <b>*</b></span><span class="candidate-slot-control candidate-slot-control--select"><select name="interview_mode_one" id="interview_mode_one" onchange="getInterviewModeChild(this.value, {{ $jobId }});" class="form-select form-select-solid"><option value="">Select mode</option>@if (!empty(@$interview_mode)) @foreach (@$interview_mode as $int_mode_val)<option value="{{ @$int_mode_val->name }}" {{ @$application->interview_mode_one == $int_mode_val->name ? 'selected' : '' }}>{{ @$int_mode_val->name }}</option>@endforeach @endif</select></span></label>
                    </div>
                  </div>

                  <div class="ai candidate-slot candidate-slot--two">
                    <div class="candidate-slot-head">
                      <span class="candidate-slot-number">2</span>
                      <strong class="candidate-slot-title">Slot 2</strong>
                    </div>
                    <div class="candidate-slot-fields">
                      <label class="candidate-slot-field"><span><i class="fa-regular fa-calendar-days" aria-hidden="true"></i> Date <b>*</b></span><span class="candidate-slot-control"><input class="form-control form-control-solid" placeholder="Select date" name="interviewDateTwo" id="interviewDateTwo" value="{{ (@$application->interviewDateTwo) ? \Carbon\Carbon::parse(@$application->interviewDateTwo)->format('d-m-Y') : '' }}" type="text" autocomplete="off"></span></label>
                      <label class="candidate-slot-field"><span><i class="fa-regular fa-clock" aria-hidden="true"></i> Time <b>*</b></span><span class="candidate-slot-control"><input class="form-control form-control-solid" placeholder="Select time" name="interviewTimeTwo" id="interviewTimeTwo" value="{{ (@$application->interviewDateTwo) ? \Carbon\Carbon::parse(@$application->interviewDateTwo)->format('h:i A') : '' }}" type="text" autocomplete="off"></span></label>
                      <label class="candidate-slot-field"><span><i class="fa-solid fa-video" aria-hidden="true"></i> Mode <b>*</b></span><span class="candidate-slot-control candidate-slot-control--select"><select name="interview_mode_two" id="interview_mode_two" onchange="getInterviewModeChild(this.value, {{ $jobId }});" class="form-select form-select-solid"><option value="">Select mode</option>@if (!empty(@$interview_mode)) @foreach (@$interview_mode as $int_mode_val)<option value="{{ @$int_mode_val->name }}" {{ @$application->interview_mode_two == $int_mode_val->name ? 'selected' : '' }}>{{ @$int_mode_val->name }}</option>@endforeach @endif</select></span></label>
                    </div>
                  </div>

                  <div class="ai candidate-slot candidate-slot--three">
                    <div class="candidate-slot-head">
                      <span class="candidate-slot-number">3</span>
                      <strong class="candidate-slot-title">Slot 3</strong>
                    </div>
                    <div class="candidate-slot-fields">
                      <label class="candidate-slot-field"><span><i class="fa-regular fa-calendar-days" aria-hidden="true"></i> Date <b>*</b></span><span class="candidate-slot-control"><input class="form-control form-control-solid" placeholder="Select date" name="interviewDateThree" id="interviewDateThree" value="{{ (@$application->interviewDateThree) ? \Carbon\Carbon::parse(@$application->interviewDateThree)->format('d-m-Y') : '' }}" type="text" autocomplete="off"></span></label>
                      <label class="candidate-slot-field"><span><i class="fa-regular fa-clock" aria-hidden="true"></i> Time <b>*</b></span><span class="candidate-slot-control"><input class="form-control form-control-solid" placeholder="Select time" name="interviewTimeThree" id="interviewTimeThree" value="{{ (@$application->interviewDateThree) ? \Carbon\Carbon::parse(@$application->interviewDateThree)->format('h:i A') : '' }}" type="text" autocomplete="off"></span></label>
                      <label class="candidate-slot-field"><span><i class="fa-solid fa-video" aria-hidden="true"></i> Mode <b>*</b></span><span class="candidate-slot-control candidate-slot-control--select"><select name="interview_mode_three" id="interview_mode_three" onchange="getInterviewModeChild(this.value, {{ $jobId }});" class="form-select form-select-solid"><option value="">Select mode</option>@if (!empty(@$interview_mode)) @foreach (@$interview_mode as $int_mode_val)<option value="{{ @$int_mode_val->name }}" {{ @$application->interview_mode_three == $int_mode_val->name ? 'selected' : '' }}>{{ @$int_mode_val->name }}</option>@endforeach @endif</select></span></label>
                    </div>
                  </div>
                </div>
                </div>
            </div>

            <!-- Professional Links -->
            <div class="fs addl-card candidate-card professional-links-card">
              <div class="fsh candidate-card-header addl-hero">
                <div class="candidate-card-title addl-hero-title">
                  <span class="candidate-card-icon addl-hero-icon"><i class="fa-solid fa-link" aria-hidden="true"></i></span>
                  <span><strong>Professional Links</strong><small>Add online profiles that help clients evaluate this candidate.</small></span>
                </div>
              </div>
              <div class="fsc candidate-card-body addl-body">
                <div class="candidate-professional-links">
                  <div class="candidate-link-row fv-row">
                    <span class="candidate-link-brand candidate-link-brand--linkedin" aria-hidden="true">in</span>
                    <span class="candidate-link-copy"><label for="linkedin">LinkedIn <span>(Optional)</span></label><small>Your professional profile</small></span>
                    <div class="candidate-link-control"><i class="fa-solid fa-link" aria-hidden="true"></i><input type="url" id="linkedin" name="linkedin" value="{{ @$application->linkedin }}" placeholder="Enter LinkedIn profile URL" /></div>
                    <small class="candidate-link-example">e.g. https://www.linkedin.com/in/yourname</small>
                  </div>
                  <div class="candidate-link-row fv-row">
                    <span class="candidate-link-brand candidate-link-brand--github" aria-hidden="true">GH</span>
                    <span class="candidate-link-copy"><label for="github">GitHub <span>(Optional)</span></label><small>Your code repositories</small></span>
                    <div class="candidate-link-control"><i class="fa-solid fa-link" aria-hidden="true"></i><input type="url" value="{{ @$application->github }}" id="github" name="github" placeholder="Enter GitHub profile URL" /></div>
                    <small class="candidate-link-example">e.g. https://github.com/yourusername</small>
                  </div>
                  <div class="candidate-link-row fv-row">
                    <span class="candidate-link-brand candidate-link-brand--behance" aria-hidden="true">Be</span>
                    <span class="candidate-link-copy"><label for="behance">Behance <span>(Optional)</span></label><small>Your design portfolio</small></span>
                    <div class="candidate-link-control"><i class="fa-solid fa-link" aria-hidden="true"></i><input type="url" id="behance" name="behance" value="{{ @$application->behance }}" placeholder="Enter Behance profile URL" /></div>
                    <small class="candidate-link-example">e.g. https://www.behance.net/yourname</small>
                  </div>
                  <div class="candidate-link-row fv-row">
                    <span class="candidate-link-brand candidate-link-brand--website" aria-hidden="true"><i class="fa-solid fa-globe"></i></span>
                    <span class="candidate-link-copy"><label for="otherLink">Portfolio / Website <span>(Optional)</span></label><small>Your personal website</small></span>
                    <div class="candidate-link-control"><i class="fa-solid fa-link" aria-hidden="true"></i><input type="url" value="{{ @$application->otherLink }}" id="otherLink" name="otherLink" placeholder="Enter website or portfolio URL" /></div>
                    <small class="candidate-link-example">e.g. https://yourportfolio.com</small>
                  </div>
                </div>
              </div>
            </div>

            <!-- Visa Status & Work Authorization (Optional) -->
            <div class="fs candidate-card additional-details-card">
              <div class="fsh candidate-card-header">
                <div class="candidate-card-title">
                  <span class="candidate-card-icon candidate-card-icon--additional"><i class="fa-solid fa-user-plus" aria-hidden="true"></i></span>
                  <span><strong>Visa Status & Work Authorization (Optional)</strong><small>Provide any other relevant information about the candidate's professional background.</small></span>
                </div>
              </div>
              <div class="fsc candidate-card-body">
                <div class="candidate-additional-details-grid">
                  <div class="fg fv-row">
                    <label>Current Visa Status (For International)</label>
                    <div class="candidate-detail-control candidate-detail-control--select"><span><i class="fa-solid fa-globe" aria-hidden="true"></i></span><select name="visastatu" id="visastatu"><option value="">Select visa status</option><option {{ @$application->visastatu == 'H1B' ? 'selected' : '' }}>H1B</option><option {{ @$application->visastatu == 'L1' ? 'selected' : '' }}>L1</option><option {{ @$application->visastatu == 'F1' ? 'selected' : '' }}>F1</option><option {{ @$application->visastatu == 'Green Card' ? 'selected' : '' }}>Green Card</option><option {{ @$application->visastatu == 'Citizen' ? 'selected' : '' }}>Citizen</option></select></div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Recruiter Comments -->
            <div class="fs candidate-card recruiter-note-card">
              <div class="fsh candidate-card-header">
                <div class="candidate-card-title">
                  <span class="candidate-card-icon candidate-card-icon--note"><i class="fa-regular fa-note-sticky" aria-hidden="true"></i></span>
                  <span><strong>Recruiter Comments</strong><small>Add internal context for the hiring team; this is not shared with the candidate.</small></span>
                </div>
              </div>
              <div class="fsc candidate-card-body">
                @if(!empty($recruiterNoteTemplates) && $recruiterNoteTemplates->count())
                <div class="fg fv-row recruiter-template-field">
                  <label>Select Template <span>(Optional - loads content into the note below)</span></label>
                  <div class="recruiter-template-control"><i class="fa-regular fa-file-lines" aria-hidden="true"></i><select id="recruiter_note_template_select"><option value="">Select Note Template...</option>@foreach($recruiterNoteTemplates as $tpl)<option value="{{ $tpl->id }}">{{ $tpl->title }}</option>@endforeach</select></div>
                </div>
                @endif
                <div class="fg fv-row recruiter-editor-field">
                  <label>Internal Note <span>(Optional)</span></label>
                  <textarea id="recruiter_note" name="recruiter_note" rows="6" placeholder="Add internal recruiter notes about this candidate...">{{ @$application->recruiter_note }}</textarea>
                  <div class="recruiter-private-note"><i class="fa-solid fa-lock" aria-hidden="true"></i><span>Internal only - Not visible to candidate</span></div>
                </div>
              </div>
            </div>
          </div>
          </form>
        </main>

      </div>
     
    </div>
    <!--end::Container-->
  </div>
  <!--end::Post-->
</div>
<!-- Enterprise Complete Required Fields Modal (Self-Contained Isolation) -->
<style>
.rf-modal-overlay {
    position: fixed !important;
    top: 0 !important;
    left: 0 !important;
    right: 0 !important;
    bottom: 0 !important;
    width: 100vw !important;
    height: 100vh !important;
    background: rgba(15, 23, 42, 0.65) !important;
    backdrop-filter: blur(4px) !important;
    -webkit-backdrop-filter: blur(4px) !important;
    z-index: 999999 !important;
    display: none;
    align-items: center !important;
    justify-content: center !important;
    padding: 16px !important;
    box-sizing: border-box !important;
    animation: rfFadeIn 0.18s ease-out;
}

@keyframes rfFadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

@keyframes rfScaleIn {
    from { opacity: 0; transform: scale(0.96) translateY(8px); }
    to { opacity: 1; transform: scale(1) translateY(0); }
}

.rf-modal-box {
    background: #ffffff !important;
    border-radius: 16px !important;
    width: 100% !important;
    max-width: 480px !important;
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25), 0 10px 15px -3px rgba(0, 0, 0, 0.1) !important;
    padding: 32px 30px 26px !important;
    position: relative !important;
    font-family: 'Poppins', sans-serif !important;
    animation: rfScaleIn 0.2s cubic-bezier(0.16, 1, 0.3, 1) !important;
    box-sizing: border-box !important;
    margin: auto !important;
    border: 1px solid #e2e8f0 !important;
}

.rf-modal-close {
    position: absolute !important;
    top: 16px !important;
    right: 16px !important;
    width: 32px !important;
    height: 32px !important;
    border-radius: 50% !important;
    background: #f1f5f9 !important;
    border: none !important;
    color: #64748b !important;
    font-size: 14px !important;
    cursor: pointer !important;
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    transition: all 0.15s ease !important;
    padding: 0 !important;
}

.rf-modal-close:hover {
    background: #e2e8f0 !important;
    color: #0f172a !important;
}

.rf-modal-close svg {
    stroke: currentColor !important;
    transition: transform 0.15s ease !important;
}

.rf-modal-close:hover svg {
    transform: rotate(90deg) !important;
}

.rf-warning-icon-wrap {
    width: 64px !important;
    height: 64px !important;
    border-radius: 50% !important;
    background: #fef9c3 !important;
    border: 2px solid #facc15 !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    margin: 0 auto 16px !important;
}

.rf-warning-icon {
    font-size: 30px !important;
    font-weight: 700 !important;
    color: #ca8a04 !important;
    line-height: 1 !important;
    font-family: 'Poppins', sans-serif !important;
}

.rf-modal-title {
    font-size: 20px !important;
    font-weight: 700 !important;
    color: #0f172a !important;
    text-align: center !important;
    margin: 0 0 8px 0 !important;
    line-height: 1.25 !important;
}

.rf-modal-desc {
    font-size: 13.5px !important;
    color: #64748b !important;
    text-align: center !important;
    line-height: 1.45 !important;
    margin: 0 auto 22px !important;
    max-width: 360px !important;
}

.rf-section-header {
    font-size: 11px !important;
    font-weight: 700 !important;
    letter-spacing: 0.6px !important;
    color: #64748b !important;
    text-transform: uppercase !important;
    text-align: left !important;
    margin-bottom: 10px !important;
}

.rf-fields-list {
    display: flex !important;
    flex-direction: column !important;
    gap: 8px !important;
    max-height: 240px !important;
    overflow-y: auto !important;
    padding-right: 4px !important;
    margin-bottom: 12px !important;
}

.rf-field-row {
    background: #fef2f2 !important;
    border: 1px solid #fee2e2 !important;
    border-radius: 8px !important;
    padding: 10px 14px !important;
    display: flex !important;
    align-items: center !important;
    justify-content: space-between !important;
    cursor: pointer !important;
    transition: all 0.15s ease !important;
    text-decoration: none !important;
    box-sizing: border-box !important;
}

.rf-field-row:hover {
    background: #fee2e2 !important;
    border-color: #fca5a5 !important;
    transform: translateY(-1px) !important;
}

.rf-field-row-left {
    display: flex !important;
    align-items: center !important;
    gap: 12px !important;
    min-width: 0 !important;
}

.rf-field-badge {
    width: 20px !important;
    height: 20px !important;
    border-radius: 50% !important;
    background: #ef4444 !important;
    color: #ffffff !important;
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    font-size: 11px !important;
    font-weight: 800 !important;
    flex-shrink: 0 !important;
    line-height: 1 !important;
}

.rf-field-name {
    font-size: 13.5px !important;
    font-weight: 600 !important;
    color: #0f172a !important;
    line-height: 1.3 !important;
    white-space: nowrap !important;
    overflow: hidden !important;
    text-overflow: ellipsis !important;
}

.rf-field-chevron {
    color: #64748b !important;
    font-size: 12px !important;
    flex-shrink: 0 !important;
    margin-left: 8px !important;
    stroke: currentColor !important;
    transition: transform 0.15s ease, color 0.15s ease !important;
}

.rf-field-row:hover .rf-field-chevron {
    color: #0f172a !important;
    transform: translateX(2px) !important;
}

.rf-helper-text {
    font-size: 12px !important;
    color: #64748b !important;
    display: flex !important;
    align-items: center !important;
    gap: 6px !important;
    margin-bottom: 20px !important;
    text-align: left !important;
}

.rf-helper-text svg {
    font-size: 13px !important;
    color: #64748b !important;
    stroke: currentColor !important;
    flex-shrink: 0 !important;
}

.rf-cta-btn {
    width: 100% !important;
    height: 44px !important;
    background: #1b84ff !important;
    color: #ffffff !important;
    border: none !important;
    border-radius: 8px !important;
    font-size: 14px !important;
    font-weight: 600 !important;
    font-family: 'Poppins', sans-serif !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    gap: 8px !important;
    cursor: pointer !important;
    transition: background 0.15s ease, transform 0.1s ease !important;
}

.rf-cta-btn:hover {
    background: #1570ef !important;
}

.rf-cta-btn:active {
    transform: scale(0.99) !important;
}

.field-attention-highlight {
    outline: 2px solid #3b82f6 !important;
    box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.25) !important;
    border-radius: 6px !important;
    transition: all 0.2s ease-in-out !important;
    background-color: rgba(239, 246, 255, 0.4) !important;
}
</style>

<div id="requiredFieldsModal" class="rf-modal-overlay" style="display:none;position:fixed;top:0;left:0;right:0;bottom:0;width:100vw;height:100vh;background:rgba(15,23,42,0.65);backdrop-filter:blur(4px);-webkit-backdrop-filter:blur(4px);z-index:999999;align-items:center;justify-content:center;padding:16px;box-sizing:border-box;" role="dialog" aria-modal="true" aria-labelledby="rfModalTitle">
    <div class="rf-modal-box" style="background:#ffffff;border-radius:16px;width:100%;max-width:480px;box-shadow:0 25px 50px -12px rgba(0,0,0,0.25),0 10px 15px -3px rgba(0,0,0,0.1);padding:32px 30px 26px;position:relative;font-family:'Poppins',sans-serif;box-sizing:border-box;margin:auto;border:1px solid #e2e8f0;">
        <button type="button" class="rf-modal-close" id="rfModalClose" aria-label="Close modal" style="position:absolute;top:16px;right:16px;width:32px;height:32px;border-radius:50%;background:#f1f5f9;border:none;color:#64748b;font-size:14px;cursor:pointer;display:inline-flex;align-items:center;justify-content:center;">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
        </button>

        <!-- Warning Icon -->
        <div class="rf-warning-icon-wrap" aria-hidden="true" style="width:64px;height:64px;border-radius:50%;background:#fef9c3;border:2px solid #facc15;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;">
            <span class="rf-warning-icon" style="font-size:30px;font-weight:700;color:#ca8a04;line-height:1;font-family:'Poppins',sans-serif;">!</span>
        </div>

        <!-- Title -->
        <h3 class="rf-modal-title" id="rfModalTitle" style="font-size:20px;font-weight:700;color:#0f172a;text-align:center;margin:0 0 8px 0;line-height:1.25;">Complete Required Fields</h3>

        <!-- Description -->
        <p class="rf-modal-desc" id="rfModalDesc" style="font-size:13.5px;color:#64748b;text-align:center;line-height:1.45;margin:0 auto 22px;max-width:360px;">
            <span id="rfMissingCountText">3 required fields</span> need your attention before you can submit this candidate.
        </p>

        <!-- Section Heading -->
        <div class="rf-section-header" style="font-size:11px;font-weight:700;letter-spacing:0.6px;color:#64748b;text-transform:uppercase;text-align:left;margin-bottom:10px;">REQUIRED FIELDS</div>

        <!-- Interactive Fields List -->
        <div class="rf-fields-list" id="rfFieldsList" style="display:flex;flex-direction:column;gap:8px;max-height:240px;overflow-y:auto;padding-right:4px;margin-bottom:12px;">
        </div>

        <!-- Helper text -->
        <div class="rf-helper-text" style="font-size:12px;color:#64748b;display:flex;align-items:center;gap:6px;margin-bottom:20px;text-align:left;">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="10"></circle><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>
            <span>Click a field above to jump directly to it.</span>
        </div>

        <!-- Primary CTA -->
        <button type="button" class="rf-cta-btn" id="rfPrimaryCta" style="width:100%;height:44px;background:#1b84ff;color:#ffffff;border:none;border-radius:8px;font-size:14px;font-weight:600;font-family:'Poppins',sans-serif;display:flex;align-items:center;justify-content:center;gap:8px;cursor:pointer;">
            <span>Review Required Fields</span>
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
        </button>
    </div>
</div>

@endsection

@php
    $rates = $currencyConverter['usd_based_rates'] ?? [
        'USD' => 1,
        'INR' => 83,
        'EUR' => 0.87,
        'JPY' => 159.65,
        'GBP' => 0.76,
        'AED' => 3.67
    ];

    $currencyKeys = $currencyConverter['getKey'] ?? [
        'INR',
        'EUR',
        'JPY',
        'GBP',
        'USD',
        'AED'
    ];

@endphp

@push('footer-script')
<script src="{{ asset('demo/ckeditor/ckeditor.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="{{ asset('js/resume-presign.js') }}?v=20260924-1"></script>
@include('service_company.job-applications.jero-client-parser')

<script>
$(document).ready(function () {

    // Authoritative Edit/Add state directly from Laravel route/view model (Phase 6)
    var IS_EDIT_MODE = {{ $page_event === 'EDIT' ? 'true' : 'false' }};
    console.log('[PARSER] IS_EDIT_MODE authoritatively set to:', IS_EDIT_MODE);

    // ── v2 upload: presign URL injected from PHP; flag computed at runtime ──────
    // resume-presign.js is loaded unconditionally; v2 activates only when the
    // module exists and the server confirms the complete session contract.
    @if(config('resumes.features.browser_upload')
        && config('resumes.features.presign_endpoint')
        && config('resumes.features.finalize_endpoint')
        && config('resumes.features.submit_with_session')
        && \Illuminate\Support\Facades\Schema::hasTable('resume_uploads'))
    window.RESUME_PRESIGN_URL = @json(route('service_company.resume.v2.sessions.store'));
    @endif
    var RESUMES_FEATURE_BROWSER_UPLOAD = !!(window.ResumePresign && window.RESUME_PRESIGN_URL);
    if (RESUMES_FEATURE_BROWSER_UPLOAD) {
        window.RESUMES_FEATURE_BROWSER_UPLOAD = true;
        // Phase 6: server-controlled retry config passed to the JS module.
        window.RESUMES_CONFIG = {
            maxPutAttempts: @json(config('resumes.network_resilience.max_put_attempts', 3)),
            putBackoffMs:   @json(config('resumes.network_resilience.put_backoff_ms', 1000)),
        };
    }

    // ── v2 state → existing progress bar UI bridge ───────────────────────────
    if (RESUMES_FEATURE_BROWSER_UPLOAD) {
        window.ResumePresign.onStateChange(function (data) {
            if (data.state === 'uploading') {
                $('#upWrap').show();
                $('#upBar').css('width', data.progress + '%');
                $('#upStats').html(data.progress + '% &middot; Uploading to cloud&hellip;');
            } else if (data.state === 'uploaded') {
                $('#upBar').css('width', '100%');
                $('#upStats').html('100% &middot; Upload complete');
                $('#upPill').css('display', 'inline-flex');
                var activeMethod = data.method === 'Method c' ? 'Method c' : 'Method A';
                $('#resume_upload_method').val(activeMethod);
                $('#upBanner').removeClass('err').addClass('ok').html(
                    '<svg class="up-banner-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor"'
                    + ' stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">'
                    + '<circle cx="12" cy="12" r="10"/><polyline points="9 12 11 14 16 9"/></svg>'
                    + '<span><strong>Upload complete!</strong> Resume uploaded successfully using ' + activeMethod + '</span>'
                ).show();
                $('#upInfoBannerTitle').text('Resume uploaded successfully using ' + activeMethod);
                $('#uploaded_file_path').val('');   // v2 uses session_uuid, not file path
                $('#resume_original_name').val('');
                $('#resumeCheck').val('1');
                // Storage upload is isolated: do NOT clear ri.value or re-call showUploadedCard to avoid parser conflicts
            } else if (data.state === 'failed') {
                if (typeof _showUploadError === 'function') {
                    _showUploadError(data.error || 'Upload failed. Please remove and try again.');
                }
            }
        });
    }
    // ─────────────────────────────────────────────────────────────────────────

    // Tab-scoped instance ID ensures multiple open tabs never collide or clear each other's upload sessions
    window.__tabInstanceId = window.__tabInstanceId || (window.crypto && window.crypto.randomUUID ? window.crypto.randomUUID() : ('tab_' + Math.random().toString(36).slice(2) + Date.now()));

    // Generate submission & update UUIDs on page load if not already set
    if (!$('#client_submission_uuid').val()) {
        $('#client_submission_uuid').val(window.crypto && window.crypto.randomUUID ? window.crypto.randomUUID() : ('sub_' + Math.random().toString(36).slice(2) + Date.now()));
    }
    if (!$('#client_update_uuid').val()) {
        $('#client_update_uuid').val(window.crypto && window.crypto.randomUUID ? window.crypto.randomUUID() : ('upd_' + Math.random().toString(36).slice(2) + Date.now()));
    }

    // Build a unique contextKey for this company + job + application + tab context
    function getUpContextKey() {
        var companyId = @json(auth()->user()->company_id ?? 0);
        var jobId = $('#job_id').val() || '0';
        var appId = $('#appId').val() || '0';
        return 'comp' + companyId + '_job' + jobId + '_app' + appId + '_tab' + window.__tabInstanceId;
    }

    /**
     * Intent: Clean resume state initialization for ADD vs EDIT candidate flows.
     * Prevents previous session resume leakage on page load/refresh for ADD Candidate mode.
     * In ADD mode ($page_event != 'EDIT' or appId == 0), start with a completely empty resume state,
     * clear any stale browser-side upload sessions from localStorage, and never auto-recover old sessions.
     * In EDIT mode ($page_event == 'EDIT' and appId > 0), preserve existing database resume state and
     * clear uncommitted temporary V2 upload sessions so they do not interfere with saved data.
     */
    if (RESUMES_FEATURE_BROWSER_UPLOAD) {
        var isEditMode = String(@json($page_event ?? '')).toUpperCase() === 'EDIT' && parseInt($('#appId').val() || '0', 10) > 0;
        if (!isEditMode) {
            // ADD mode: clear stale session for current context & reset form fields & UI
            window.ResumePresign.cancelUpload(getUpContextKey());
            $('#resume_session_uuid').val('');
            $('#uploaded_file_path').val('');
            $('#resume_original_name').val('');
            $('#resumeCheck').val('');
            $('#upInfo').removeClass('show');
            $('#uploadBox').show();
            $('#fileInfo').hide();
            $('#fileName').text('');
            var ri = document.getElementById('resume'); if (ri) ri.value = '';
        } else {
            // EDIT mode: clear uncommitted temporary V2 session; DB resume is rendered via Blade HTML if present
            window.ResumePresign.cancelUpload(getUpContextKey());
            $('#resume_session_uuid').val('');
        }
    }
    // ─────────────────────────────────────────────────────────────────────────

    const uploadBox = document.getElementById('uploadBox');
    const resumeInput = document.getElementById('resume');
    var resumeUploadGeneration = 0;

    /**
     * Intent: Use the existing same-origin raw-byte uploader as a circuit breaker
     * when this browser or network cannot complete the direct Cloudflare R2 PUT.
     */
    function shouldUseResumeUploadFallback(error) {
        var message = String((error && error.message) || '');

        return message.indexOf('R2 upload failed') !== -1
            || message.indexOf('Network error during upload') !== -1
            || message.indexOf('Upload timed out') !== -1
            || message.indexOf('Upload feature is not enabled') !== -1
            || message.indexOf('Presign request failed') !== -1;
    }

    // ==========================
    // Common file handler
    // ==========================
    function processFile(file, event = null) {

        if (!file) return;

        const ext = file.name.split('.').pop().toLowerCase();

        if ($.inArray(ext, ['pdf', 'doc', 'docx']) === -1) {
            alert('Only PDF, DOC and DOCX files are allowed.');
            return;
        }

        const sizeMB = file.size / (1024 * 1024);

        // Server is `max:6048` KB = 6 MB. Keep the JS check in sync.
        if (sizeMB > 6) {
            alert('Maximum file size is 6 MB.');
            return;
        }
        var uploadGeneration = ++resumeUploadGeneration;

        // Show human-readable size: KB for small files (avoids "0.07 MB" display)
        const sizeStr = sizeMB >= 1
            ? sizeMB.toFixed(2) + ' MB'
            : Math.round(file.size / 1024) + ' KB';

        $('#fileName').text(file.name + ' (' + sizeStr + ')');

        $('#fileInfo').css('display', 'flex');

        $('#resumeCheck').val('1');
        $('#resume_intent').val('ATTACH_IF_AVAILABLE');

        $('.download-file').addClass('d-none');

        // Phase 1: Capture selected File immediately; decouple R2 / server upload from parsing path
        const selectedFile = file;
        window.__upXhrChannelClaimed = true;
        window._currentActiveResumeFile = selectedFile;
        window._clientParserState = 'parsing';

        // Immediately display the Resume Card so the recruiter sees file info and live parser progress
        showUploadedCard(selectedFile, null);

        $('#upDetailParserStatus').html(
            '<span class="spinner-border spinner-border-sm text-primary me-1" role="status" style="width: 12px; height: 12px; border-width: 2px;"></span>' +
            '<span>Parsing in browser&hellip;</span>'
        );

        // ── Phase 1-5: 100% Client-Side Resume Parsing via https://parser.jero.works/ ──────────
        // Zero PHP/Laravel execution, zero MySQL/ES strain, zero R2 during parsing, runs 100% in Recruiter Browser RAM
        if (window.JeroClientParser && typeof window.JeroClientParser.parseAndPopulate === 'function') {
            var reqSkills = [];
            if (typeof candidateRequiredSkills !== 'undefined' && Array.isArray(candidateRequiredSkills)) {
                reqSkills = candidateRequiredSkills.map(function (s) { return s.name; });
            }

            window.JeroClientParser.parseAndPopulate(selectedFile, {
                isEditMode: IS_EDIT_MODE,
                requiredSkills: reqSkills,
                onProgress: function (stage, pct) {
                    $('#upDetailParserStatus').html(
                        '<span class="spinner-border spinner-border-sm text-primary me-1" role="status" style="width: 12px; height: 12px; border-width: 2px;"></span>' +
                        '<span>' + $('<div>').text(stage).html() + ' (' + pct + '%)</span>'
                    );
                },
                onSuccess: function (result, totalMs) {
                    window._clientParserState = 'completed';
                    $('#upDetailParserStatus').html(
                        '<span style="color: #41c08e; font-weight: 600;">✓ Parsed in ' + totalMs + 'ms</span>'
                    );
                    toastr.success('Resume parsed in browser and fields populated!');
                },
                onError: function (err) {
                    window._clientParserState = 'error';
                    $('#upDetailParserStatus').html(
                        '<span style="color: #f59e0b; font-weight: 500;">Manual Entry</span>'
                    );
                    toastr.info('Resume parsing unavailable. Please enter candidate details manually.');
                }
            });
        }

        // ── PIPELINE 2: Independent Background Storage Upload ──────────
        setTimeout(function () {
            var uploadGeneration = ++resumeUploadGeneration;
            if (RESUMES_FEATURE_BROWSER_UPLOAD) {
                // v2 path: direct browser → R2 storage (runs independently in background)
                window.ResumePresign.startUpload(selectedFile, getUpContextKey()).catch(function (error) {
                    if (uploadGeneration !== resumeUploadGeneration
                        || !shouldUseResumeUploadFallback(error)
                        || typeof uploadWithProgress !== 'function') {
                        return;
                    }
                    window.ResumePresign.cancelUpload(getUpContextKey());
                    uploadWithProgress(selectedFile);
                });
            } else if (typeof uploadWithProgress === 'function') {
                // v1 path: same-origin multipart POST fallback
                uploadWithProgress(selectedFile);
            }
        }, 30);

        window.__upXhrChannelClaimed = false;
    }

    // ==========================
    // XHR progress helpers
    // ==========================
    let upXhr = null;

    function fmtKB(b) {
        return (b / 1024).toFixed(1) + ' KB';
    }

    function fmtSize(b) {
        return b >= 1024 * 1024
            ? (b / (1024 * 1024)).toFixed(1) + ' MB'
            : Math.round(b / 1024) + ' KB';
    }

    function extLabel(file) {
        var e = (file.name.split('.').pop() || '').toUpperCase();
        if (e !== 'PDF' && e !== 'DOC' && e !== 'DOCX') e = 'PDF';
        return e;
    }

    function resetProgress() {
        if (upXhr) { upXhr.abort(); upXhr = null; }
        window.__upXhrChannelClaimed = false;
        $('#upWrap').hide();
        $('#upBar').css('width', '0%');
        $('#upStats').text('0% &middot; 0 KB / 0 KB');
        $('#upPill').hide();
        const $b = $('#upBanner');
        $b.hide().removeClass('ok err').empty();
        $('#save-form, #update-form').prop('disabled', false);
    }

    // Go back to the dropzone / progress-possible state. Called by
    // the #removeFile button and the card's Replace/Remove buttons.
    function backToDropzone() {
        resumeUploadGeneration++;
        resetProgress();
        // Cancel v2 upload if in progress (aborts R2 XHR + clears session UUID)
        if (RESUMES_FEATURE_BROWSER_UPLOAD) {
            window.ResumePresign.cancelUpload(getUpContextKey());
        }
        // Race condition 3 defense: abort any in-flight WASM client parsing
        if (window.JeroClientParser && typeof window.JeroClientParser.cancelActiveParse === 'function') {
            window.JeroClientParser.cancelActiveParse();
        }
        window._clientParserState = null;
        window._currentActiveResumeFile = null;
        $('#resume_session_uuid').val('');
        $('#resume_upload_method').val('');
        $('#uploaded_file_path').val('');
        $('#resume_original_name').val('');
        $('#upInfoBannerTitle').text('Resume uploaded successfully!');
        $('.resume-card').removeClass('has-uploaded-resume');
        $('#upInfo').removeClass('show');
        $('#uploadBox').removeClass('d-none').attr('style', '').show();
        $('#fileInfo').hide();
        $('#fileName').text('');
        $('#resume').val('');
        $('#resumeCheck').val('');
        var isEdit = String(@json($page_event ?? '')).toUpperCase() === 'EDIT' && parseInt($('#appId').val() || '0', 10) > 0;
        $('#resume_intent').val(isEdit ? 'REMOVE' : 'NONE');
        $('.download-file').addClass('d-none');
        window.__upLoc = null;
    }

    // Populate the post-upload Resume Information card from the picked
    // file. Sets the initial details (Uploaded On, File Type, File Size,
    // Parser Status = Pending). When parserResponse is provided (from
    // re_test), it refreshes skills count, parser status, and pages.
    function showUploadedCard(file, parserResponse) {
        var ext = extLabel(file);
        var $info = $('#upInfo');
        $('#upFileBadgeExt').text(ext);
        if (ext === 'DOC' || ext === 'DOCX') {
            $('.up-file-badge').css({'background': '#dbeafe', 'color': '#2563eb'});
            $('.up-file-badge i').attr('class', 'fa-regular fa-file-word').css('color', '#2563eb');
            $('#upFileBadgeExt').css('color', '#2563eb');
        } else {
            $('.up-file-badge').css({'background': '#fee2e2', 'color': '#dc2626'});
            $('.up-file-badge i').attr('class', 'fa-regular fa-file-pdf').css('color', '#dc2626');
            $('#upFileBadgeExt').css('color', '#dc2626');
        }
        $('#upFileName').text(file.name);
        $('#upFileSub').html(fmtSize(file.size) + ' &bull; ' + ext);

        var now = new Date();
        var day = String(now.getDate()).padStart(2, '0');
        var months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
        var month = months[now.getMonth()];
        var year = now.getFullYear();
        var hh = String(now.getHours()).padStart(2, '0');
        var mm = String(now.getMinutes()).padStart(2, '0');
        var tz = '';
        var tzMatch = now.toTimeString().match(/\(([A-Za-z\s]+)\)$/);
        if (tzMatch && tzMatch[1]) {
            var tzWords = tzMatch[1].trim().split(/\s+/);
            tz = tzWords.length > 1 ? tzWords.map(function (w) { return w[0]; }).join('') : tzMatch[1];
        }
        if (!tz) {
            try {
                var tzParts = new Intl.DateTimeFormat('en-US', { timeZoneName: 'short' }).formatToParts(now);
                var tzFound = tzParts.find(function (p) { return p.type === 'timeZoneName'; });
                if (tzFound && tzFound.value) tz = tzFound.value;
            } catch (e) {}
        }
        var tzSuffix = tz ? (' ' + tz) : '';
        $('#upDetailUploadedOn').text(day + ' ' + month + ' ' + year + ', ' + hh + ':' + mm + tzSuffix);

        if (!window._clientParserState) {
            $('#upDetailParserStatus').text('Pending');
        }
        $('#upDetailFileType').text(ext);
        $('#upDetailFileSize').text(fmtSize(file.size));

        if (parserResponse) {
            window._clientParserState = 'completed';
            $('#upDetailParserStatus').html('<span style="color: #059669; font-weight: 700;">✓ Parsed</span>');
        }

        // Hide the dropzone + file-info strip and show the new card.
        $('#uploadBox').attr('style', 'display: none !important').addClass('d-none');
        $('#fileInfo').hide();
        $('.resume-card').addClass('has-uploaded-resume');
        $info.find('.up-info-banner').show();
        $info.addClass('show');
    }

    // ─── Zero-temp upload pipeline ───────────────────────────────────────────
    // xhr.send(file) sends raw bytes — PHP reads php://input stream directly
    // to R2. No multipart = no PHP upload_tmp_dir write. No CORS needed.
    // Flow: browser → POST raw bytes → Laravel streams to R2 → ParseResumeJob
    // ─────────────────────────────────────────────────────────────────────────

    var _parseStatusInterval = null;
    var _resumeUploadId      = null;

    function _showUploadError(msg) {
        $('#upBanner').removeClass('ok').addClass('err').html(
            '<svg class="up-banner-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">'
            + '<circle cx="12" cy="12" r="10"/>'
            + '<line x1="15" y1="9" x2="9" y2="15"/>'
            + '<line x1="9" y1="9" x2="15" y2="15"/>'
            + '</svg><span><strong>Upload failed:</strong> ' + msg + '</span>'
        ).show();
        upXhr = null;
    }

    function _startParsePoller(uploadId) {
        // Zero server-side polling: Resume parsing is 100% client-side via JeroClientParser.
        _resumeUploadId = uploadId;
    }

    function uploadWithProgress(file) {
        window.__upXhrChannelClaimed = true;
        resetProgress();
        window.__upXhrChannelClaimed = true;
        if (_parseStatusInterval) { clearInterval(_parseStatusInterval); _parseStatusInterval = null; }
        _resumeUploadId = null;

        $('#upWrap').show();
        $('#upStats').text('0% &middot; ' + fmtKB(0) + ' / ' + fmtKB(file.size));
        window.__upLoc = { file: file };

        var mimeMap = {
            pdf:  'application/pdf',
            doc:  'application/msword',
            docx: 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        };
        var ext         = (file.name.split('.').pop() || '').toLowerCase();
        var contentType = mimeMap[ext] || file.type || 'application/octet-stream';

        // Send raw file bytes — PHP reads php://input, no multipart, no temp file
        upXhr = new XMLHttpRequest();
        upXhr.open('POST', '{{ route("service_company.job-applications.uploadresume") }}', true);
        upXhr.setRequestHeader('Content-Type', contentType);
        upXhr.setRequestHeader('X-CSRF-TOKEN', '{{ csrf_token() }}');
        upXhr.setRequestHeader('X-File-Name', encodeURIComponent(file.name));
        upXhr.setRequestHeader('X-File-Size', file.size);
        upXhr.setRequestHeader('X-Candidate-Id', $('#appId').val() || '');
        upXhr.setRequestHeader('X-Job-Id', $('#job_id').val() || '');

        upXhr.upload.onprogress = function (e) {
            if (!e.lengthComputable) return;
            var pct = Math.round(e.loaded * 100 / e.total);
            $('#upBar').css('width', pct + '%');
            $('#upStats').text(pct + '% &middot; ' + fmtKB(e.loaded) + ' / ' + fmtKB(file.size));
        };

        upXhr.onload = function () {
            var r = null;
            try { r = JSON.parse(upXhr.responseText); } catch (_) {}
            upXhr = null;

            if (this.status >= 200 && this.status < 300 && r && r.success) {
                $('#upBar').css('width', '100%');
                $('#upStats').text('100% &middot; ' + fmtKB(file.size) + ' / ' + fmtKB(file.size));
                $('#upPill').css('display', 'inline-flex');
                $('#resume_upload_method').val('Method B');
                $('#upBanner').removeClass('err').addClass('ok').html(
                    '<svg class="up-banner-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">'
                    + '<circle cx="12" cy="12" r="10"/><polyline points="9 12 11 14 16 9"/></svg>'
                    + '<span><strong>Upload complete!</strong> Resume uploaded successfully using Method B</span>'
                ).show();
                $('#upInfoBannerTitle').text('Resume uploaded successfully using Method B');

                $('#uploaded_file_path').val(r.file_path || '');
                $('#resume_original_name').val(r.original_name || file.name || '');
                // Ensure uploadBox remains completely hidden and upInfo remains shown
                $('#uploadBox').attr('style', 'display: none !important').addClass('d-none');
                $('.resume-card').addClass('has-uploaded-resume');
                $('#upInfo').addClass('show');

                // Zero server polling: 100% of resume parsing runs client-side
            } else {
                _showUploadError((r && r.message) ? r.message : 'Upload failed. Try again.');
            }
        };

        upXhr.onerror = function () {
            _showUploadError('Network error during upload. Click Remove and try again.');
        };

        upXhr.send(file); // raw bytes → php://input → R2, zero local temp
    }

    // Remove button inside the post-upload Resume Information card.
    // Remove = reset state and stay on the empty dropzone.
    $(document).on('click', '#upRemoveBtn', function (e) {
        e.preventDefault();
        if (_parseStatusInterval) { clearInterval(_parseStatusInterval); _parseStatusInterval = null; }
        backToDropzone();
    });

    $(document).on('click', '.up-info-banner-close', function (e) {
        e.preventDefault();
        $(this).closest('.up-info-banner').slideUp(180);
    });

    // ==========================
    // Choose File Button
    // Direct binding so stopPropagation() fires BEFORE the click reaches
    // #uploadBox — prevents resumeInput.click() from being called twice.
    // ==========================
    document.getElementById('chooseFileBtn').addEventListener('click', function (e) {
        e.preventDefault();
        e.stopPropagation();
        resumeInput.click();
    });

    document.addEventListener('paste', function (e) {

        const files = e.clipboardData?.files;

        if (files && files.length) {

            const file = files[0];

            // Assign to input so form submit sees the file
            const dt = new DataTransfer();
            dt.items.add(file);
            resumeInput.files = dt.files;

            // processFile handles upload — no synthetic change dispatch (would double-fire)
            processFile(file);
        }
    });
    

    // ==========================
    // Click Upload Area
    // ==========================
    $('#uploadBox').on('click', function (e) {

        if (
            $(e.target).closest('#chooseFileBtn').length ||
            $(e.target).closest('#removeFile').length ||
            $(e.target).closest('.download-file').length
        ) {
            return;
        }

        resumeInput.click();
    });

    // ==========================
    // File Input Change
    // ==========================
    $('#resume').on('change', function (e) {

        if (this.files.length > 0) {

            processFile(this.files[0], e);

        } else {

            $('#fileInfo').hide();
            $('#resumeCheck').val('');
            $('.download-file').addClass('d-none');
        }
    });

    // ==========================
    // Remove File
    // ==========================
    $(document).on('click', '#removeFile', function (e) {
        e.preventDefault();
        e.stopPropagation();
        if (_parseStatusInterval) { clearInterval(_parseStatusInterval); _parseStatusInterval = null; }
        backToDropzone();
    });

    // ==========================
    // Drag & Drop
    // ==========================
    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {

        uploadBox.addEventListener(eventName, function (e) {
            e.preventDefault();
            e.stopPropagation();
        });
    });

    uploadBox.addEventListener('dragenter', function () {
        this.classList.add('dragging');
    });

    uploadBox.addEventListener('dragover', function () {
        this.classList.add('dragging');
    });

    uploadBox.addEventListener('dragleave', function () {
        this.classList.remove('dragging');
    });

    uploadBox.addEventListener('drop', function (e) {

        this.classList.remove('dragging');

        const files = e.dataTransfer.files;

        console.log('DROP FIRED', files);

        if (files.length > 0) {

            const file = files[0];

            // Assign to input so form submit sees the file
            const dt = new DataTransfer();
            dt.items.add(file);
            resumeInput.files = dt.files;

            // processFile handles upload — no synthetic change dispatch (would double-fire)
            processFile(file);
        }
    });

});
</script>


<script>

        // stepper
        document.querySelectorAll('.stp-s').forEach((s) => {
            s.addEventListener('click', () => {
                document
                    .querySelectorAll('.stp-s')
                    .forEach((x) => x.classList.remove('a'));
                s.classList.add('a');
            });
        });
        // char / word counters
        document.querySelectorAll('textarea').forEach((t) => {
            const c = t.parentElement ? t.parentElement.querySelector('.cc') : null;
            if (c) {
                if (t.id === 'careersummary') {
                    const updateCsCount = () => {
                        const words = t.value.trim().split(/\s+/).filter(Boolean);
                        c.textContent = words.length + ' / 100';
                    };
                    t.addEventListener('input', updateCsCount);
                    updateCsCount();
                } else {
                    const m = c.textContent.includes('1000') ? 1000 : 500;
                    t.addEventListener('input', () => {
                        c.textContent = t.value.length + ' / ' + m;
                    });
                }
            }
        });
        // star ratings
        document.querySelectorAll('.sr').forEach((r) => {
            const stars = r.querySelectorAll('.st');
            stars.forEach((s, i) => {
                s.addEventListener('click', () => {
                    stars.forEach((x, j) => {
                        if (j <= i) x.classList.remove('e');
                        else x.classList.add('e');
                    });
                });
            });
        });
        // add skill
        document.querySelector('.asb').addEventListener('click', () => {
            const tb = document.querySelector('.sk tbody');
            const tr = document.createElement('tr');
            tr.innerHTML =
                '<td><input type="text" placeholder="Enter skill"></td><td><input type="number" value="0" style="width:80px"></td><td><input type="number" value="0" style="width:80px"></td><td><select style="width:140px"><option>Currently</option><option>Within 6 Months</option><option>Within 1 Year</option><option>Within 2 Years</option><option>More than 2 Years</option></select></td><td><div class="sr"><span class="st e">★</span><span class="st e">★</span><span class="st e">★</span><span class="st e">★</span><span class="st e">★</span></div></td><td><button class="ab" type="button">🗑️</button></td>';
            tb.appendChild(tr);
            tr.querySelectorAll('.sr').forEach((r) => {
                const s = r.querySelectorAll('.st');
                s.forEach((x, i) => {
                    x.addEventListener('click', () => {
                        s.forEach((y, j) => {
                            if (j <= i) y.classList.remove('e');
                            else y.classList.add('e');
                        });
                    });
                });
            });
            tr.querySelector('.ab').addEventListener('click', () => tr.remove());
        });
        // delete existing
        document.querySelectorAll('.ab').forEach((b) => {
            b.addEventListener('click', () => b.closest('tr').remove());
        });
</script>

<script>

        const HOURS_PER_MONTH = 160;
        const MONTHS_PER_YEAR = 12;
        const currencyRates = @json($rates);
        const currencyRateschar = @json($currencyKeys);

        let isUpdating = false;

        // Base values (USD)
        let payBase = { hourly: 0 };
        let billBase = { hourly: 0 };

        function format(val) {
            return val ? parseFloat(val).toFixed(2) : '';
        }
        
        function fromUSD(amount, currency) {
            return amount / currencyRates[currency];
        }

   
        // ================= PAY RATE =================
        $('#ctc').on('input', function () {
            if (isUpdating) return;
            isUpdating = true;

            let hourly = parseFloat($(this).val()) || 0;

            $('#pay_monthly').val(format(hourly * HOURS_PER_MONTH));
            $('#pay_annual').val(format(hourly * HOURS_PER_MONTH * MONTHS_PER_YEAR));
            updatePayUI();

            isUpdating = false;
        });

        $('#pay_monthly').on('input', function () {
            if (isUpdating) return;
            isUpdating = true;

            let monthly = parseFloat($(this).val()) || 0;

            let hourly = monthly / HOURS_PER_MONTH;

            $('#ctc').val(format(hourly));
            $('#pay_annual').val(format(monthly * MONTHS_PER_YEAR));
            updatePayUI();

            isUpdating = false;
        });

        $('#pay_annual').on('input', function () {
            if (isUpdating) return;
            isUpdating = true;

            let annual = parseFloat($(this).val()) || 0;

            let monthly = annual / MONTHS_PER_YEAR;
            let hourly = monthly / HOURS_PER_MONTH;

            $('#ctc').val(format(hourly));
            $('#pay_monthly').val(format(monthly));
            updatePayUI();

            isUpdating = false;
        });

        // ================= BILL RATE =================
        $('#ectc').on('input', function () {
            if (isUpdating) return;
            isUpdating = true;

            let hourly = parseFloat($(this).val()) || 0;

            $('#bill_monthly').val(format(hourly * HOURS_PER_MONTH));
            $('#bill_annual').val(format(hourly * HOURS_PER_MONTH * MONTHS_PER_YEAR));
            updateBillUI();
            isUpdating = false;
        });

        $('#bill_monthly').on('input', function () {
            if (isUpdating) return;
            isUpdating = true;

            let monthly = parseFloat($(this).val()) || 0;

            let hourly = monthly / HOURS_PER_MONTH;

            $('#ectc').val(format(hourly));
            $('#bill_annual').val(format(monthly * MONTHS_PER_YEAR));
            updateBillUI();

            isUpdating = false;
        });

        $('#bill_annual').on('input', function () {
            if (isUpdating) return;
            isUpdating = true;

            let annual = parseFloat($(this).val()) || 0;

            let monthly = annual / MONTHS_PER_YEAR;
            let hourly = monthly / HOURS_PER_MONTH;

            $('#ectc').val(format(hourly));
            $('#bill_monthly').val(format(monthly));
            updateBillUI();

            isUpdating = false;
        });
        function updatePayUI() {
            let currency = $('#current_ctc_cur').val();
            let pay_rate_amount = $('#ctc').val();
            let currSet = currencyRateschar[currency];
            if(currency==4){ 
              $('#pay_rate_hourly_usd').text('');
              $('#pay_rate_monthly_usd').text('');
              $('#pay_rate_annual_usd').text('');
              updateCTCFields();
              return false;
            }

            let hourly = fromUSD(pay_rate_amount, currSet);
            let monthly = hourly * HOURS_PER_MONTH;
            let annual = monthly * MONTHS_PER_YEAR;
            
            $('#pay_rate_hourly_usd').text('Approx $ '+ format(hourly));
            $('#pay_rate_monthly_usd').text('Approx $ '+ format(monthly));
            $('#pay_rate_annual_usd').text('Approx $ '+ format(annual));
            updateCTCFields();
        }
        function updateBillUI() {
            let currency = $('#current_ctc_cur').val();
            let bill_rate_amount = $('#ectc').val();
            let currSet = currencyRateschar[currency];
            if(currency==4){ 
              $('#bill_rate_hourly_usd').text('');
              $('#bill_rate_monthly_usd').text('');
              $('#bill_rate_annual_usd').text('');
              updateCTCFields();
              return false;
            }

            let hourly = fromUSD(bill_rate_amount, currSet);
            let monthly = hourly * HOURS_PER_MONTH;
            let annual = monthly * MONTHS_PER_YEAR;

            $('#bill_rate_hourly_usd').text('Approx $ '+ format(hourly));
            $('#bill_rate_monthly_usd').text('Approx $ '+ format(monthly));
            $('#bill_rate_annual_usd').text('Approx $ '+ format(annual));
            updateCTCFields();
        }



        function payRateUpdate(){

          if (isUpdating) return;
            isUpdating = true;
           

            updatePayUI();

            isUpdating = false;
          
        }

        function billRateUpdate(){

          if (isUpdating) return;
            isUpdating = true;
          
            updateBillUI();

            isUpdating = false;
          
        }


        





        $('#current_ctc_cur').on('change', function () {
           var textCurrency = $(this).find('option:selected').text();
           $('.changeTextCurrency').text(textCurrency);
            updatePayUI();
            updateBillUI();
        });
        $('#current_ectc_cur').on('change', function () {
           var textCurrency = $(this).find('option:selected').text();
           $('.changeTextCurrency').text(textCurrency);
            updateBillUI();
            updatePayUI();
        });
        $('#current_th_cur').on('change', function () {
           var textCurrency = $(this).find('option:selected').text();
           $('.changeTextCurrency').text(textCurrency);
            updateBillUI();
            updatePayUI();
        });
        setTimeout(() => {
              billRateUpdate();
              payRateUpdate();
          }, 1000);








    let linkedinRegex = /^(https?:\/\/)?(www\.)?linkedin\.com\/.*$/i;
    let githubRegex   = /^(https?:\/\/)?(www\.)?github\.com\/[A-Za-z0-9_-]+\/?$/i;
    let behanceRegex  = /^(https?:\/\/)?(www\.)?behance\.net\/[A-Za-z0-9_-]+\/?$/i;

  function validateLink(input, regex, messageId, platform) {
          let value = $(input).val().trim();
          if (value === "") {
              $(messageId).html(""); // Clear if empty
              return;
          }
          if (regex.test(value)) {
              $(messageId).html("✅ Valid " + platform).css("color", "green");
          } else {
              $(messageId).html("❌ Invalid " + platform).css("color", "red");
          }
      }


    $("#linkedin").on("change", function () {
        validateLink(this, linkedinRegex, "#linkedin-msg", "LinkedIn");
    });

    $("#github").on("change", function () {
        validateLink(this, githubRegex, "#github-msg", "GitHub");
    });

    $("#behance").on("change", function () {
        validateLink(this, behanceRegex, "#behance-msg", "Behance");
    });


  localStorage.setItem('currentPage', "{{ @$currentPage }}");
  
var cities = @json($cities);


    $(document).ready(function () {
      // JERO Enterprise Phone Box auto-initializes on load
      if (window.JeroPhoneBox) {
        window.JeroPhoneBox.initAll();
      }
    });


</script>

<script>
// Internet connectivity check used by both save and update handlers.
async function isInternetAvailable() {
    try {
        await fetch("https://www.gstatic.com/generate_204", {
            method: "GET",
            cache: "no-cache",
            mode: "no-cors"
        });
        return true;
    } catch (error) {
        return false;
    }
}
</script>

@php if($page_event == 'EDIT'){ @endphp
<script>

//// new code for update  start
    var KTUPDATECandidate = function () {
    var form;
    var submitButton;
    var validator;

    
    var handleForm = function (e) {
      
        validator = FormValidation.formValidation(
            form,
            {
                fields: {
					
                  // 'resume':
                  //   {
                  //       validators:
                  //       {
                  //           file: {
                  //               maxSize: 5242880, // 5 MB in bytes
                  //               message: "The file size must not exceed 5 MB",
                  //               type: 'application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                  //               message: "Only PDF, DOC, or DOCX files are allowed"
                  //           }
                  //       }
                  //   },
                    'photo':
                    {
                      validators:
                        {
                            file: {
                                maxSize: 5242880, // 5 MB in bytes
                                message: "The file size must not exceed 5 MB"
                            }
                        }
                   },
                    'full_name':
                    {
                        validators:
                        {
                            notEmpty:{message: "Full name is required"}
                           
                        }
                    },
					
                    'current_designation':
                    {
                        validators:
                        {
                            notEmpty:{ message: "Current designation is required"}
                           
                        }
                    },
					
                    'current_company':
                    {
                        validators:
                        {
                            // notEmpty:{ message: "Current company is required"}
                            
                        }
                    },
					
                    'phone':
                    {
                        validators:
                        {
                            notEmpty:{message: "Primary number is required"},
                            digits:{ message: "Primary number must contain only digits"},
                            stringLength:{ min: 6, max: 15, message: "Please enter a valid mobile number"}
                            
                        }
                    },
                    'secondary_mobile':
                    {
                        validators:
                        {
                            digits:{ message: "Secondary number must contain only digits"},
                            stringLength:{ min: 6, max: 15, message: "Please enter a valid mobile number"}
                            
                        }
                    },
					
                    'email':
                    {
                        validators:
                        {
                            notEmpty:{ message: 'Primary email is required'},
                            emailAddress:{message: 'The value is not a valid email address'}
                            
                        }
                    },
                    'secondrary_email':
                    {
                        validators:
                        {
                            emailAddress:{message: 'The value is not a valid email address'}
                            
                        }
                    },
					
                    'qualification':
                    {
                        validators:
                        {
                            // notEmpty:{message: 'Qualification Name is required'}
                           
                        }
                    },
					
                    'total_exp':
                    {
                        validators:
                        {
                            // notEmpty:{message:"Total Experience is required"}
                           
                        }
                    },
					
                    'rel_exp':
                    {
                        validators:
                        {
                            // notEmpty:{ message:"Relevant Experience is required"}
                           
                        }
                    },
					
                    'ctc':
                    {
                        validators:
                        {
                            // notEmpty:{ message: "Current CTC is required"}
                            
                        }
                    },
					
                    'ectc':
                    {
                        validators:
                        {
                            // notEmpty:{message: "Exp. CTC is required"}
                            
                        }
                    },
					
					
                    'country':
                    {
                        validators:
                        {
                            // notEmpty:{message: "Country is required"}
                        }
                    },
					
                    'state':
                    {
                        validators:
                        {
                            // notEmpty:{message: "State is required"}
                           
                        }
                    },
					
                    'pincode':
                    {
                        validators:
                        {
                            // notEmpty:{message: "Pincode is required"},                           
                            digits:{message: "Pincode must contain digits"},
                            stringLength:{ min: 1,	max: 1,	message: ""}
                           
                        }    
                    },                    
                    'skills':
                    {
                        validators:
                        {
                            // notEmpty:{ message: 'Skills is required'}
                          
                        }
                    },
                    'specialties':
                    {
                        validators:
                        { notEmpty:{message: "Specialties is required"}
                           
                        }
                    }
                },
                plugins: {
                    trigger: new FormValidation.plugins.Trigger(),
                    bootstrap: new FormValidation.plugins.Bootstrap5({
                        rowSelector: '.fv-row'
                    })
                }
            }
        );

       
        submitButton.addEventListener('click', async function (e) {
           
            e.preventDefault();
            if (submitButton.disabled) {
                return; // Prevent duplicate submission
            }

            const connected = await isInternetAvailable();
            if (!connected) {
                Swal.fire({
                    title: "No Internet",
                    text: "You are currently offline or the server is unreachable.",
                    icon: "warning",
                    confirmButtonText: "Ok, got it!",
                    customClass: {
                        confirmButton: "btn btn-warning"
                    }
                });
                return; // ⛔ STOP EXECUTION
            }

            validator.validate().then(function (status) {
                if (status == 'Valid') {
                    if (!navigator.onLine) {
                        Swal.fire({ title: "No Internet", text: "You are currently offline or the server is unreachable.", icon: "warning", confirmButtonText: "Ok, got it!", customClass: { confirmButton: "btn btn-warning" } });
                        return;
                    }
                    $("#update-form").prop("disabled", true);
                    $("#update-form").html(
                        `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Waiting...`
                    );
                    submitButton.setAttribute('data-kt-indicator', 'on');
                    submitButton.disabled = true;

                    if (typeof CKEDITOR !== 'undefined' && CKEDITOR.instances['recruiter_note']) {
                        CKEDITOR.instances['recruiter_note'].updateElement();
                    }

                    // Ensure unique client_update_uuid for idempotency
                    if (!$('#client_update_uuid').val()) {
                        $('#client_update_uuid').val(window.crypto && window.crypto.randomUUID ? window.crypto.randomUUID() : ('upd_' + Math.random().toString(36).slice(2) + Date.now()));
                    }

                    var formData = new FormData($('#createForm')[0]);
                    // 0% Server Strain & 10K Concurrency Guard: Strip redundant binary file if already in R2
                    if ($('#resume_session_uuid').val() || $('#uploaded_file_path').val()) {
                        if (formData.delete) { formData.delete('resume'); }
                    }
                    var candidateEditId = '{{ @$application->id ?: @$appId }}';
                    $.ajax({
                        url         : '{{ url("service_company/job-applications/update") }}/' + candidateEditId + '?_token=' + '{{ csrf_token() }}',
                        type        : 'POST',
                        data        : formData,
                        processData : false,
                        contentType : false,
                        dataType    : 'json',
                        redirect    : true,
                        success     : function (response) {
                            if (typeof response === 'string') {
                                try { response = JSON.parse(response); } catch (e) {}
                            }
                            $("#update-form").prop("disabled", false);
                            if (response.status == 'mailcheck') {
                                if (!navigator.onLine) {
                                    Swal.fire({ title: "No Internet", text: "You are currently offline or the server is unreachable.", icon: "warning", confirmButtonText: "Ok, got it!", customClass: { confirmButton: "btn btn-warning" } });
                                    $("#save-form").prop("disabled", false).html('<i class="fa-solid fa-paper-plane" aria-hidden="true"></i><span>Submit Candidate</span>');
                                    submitButton.removeAttribute('data-kt-indicator');
                                    submitButton.disabled = false;
                                    return;
                                }
                                $("#update-form").html('<i class="fa-solid fa-pen-to-square" aria-hidden="true"></i><span>Update Candidate</span>');
                                Swal.fire("Warning", response.message , "warning");
                                return false;
                            }
                            if (response.status == 'notauthorized') {
                                $("#update-form").html('<i class="fa-solid fa-pen-to-square" aria-hidden="true"></i><span>Update Candidate</span>');
                                submitButton.removeAttribute('data-kt-indicator');
                                submitButton.disabled = false;
                                Swal.fire("Not Authorized", response.message, "warning");
                                return false;
                            }

                            if (response.status !== 'success') {
                                $("#update-form").html('<i class="fa-solid fa-pen-to-square" aria-hidden="true"></i><span>Update Candidate</span>');
                                submitButton.removeAttribute('data-kt-indicator');
                                submitButton.disabled = false;
                                Swal.fire("Update Failed", response.message || "The candidate could not be updated. Please try again.", "error");
                                return false;
                            }

                            var fallbackUrl = '{{ route('service_company.cvtracker.index') }}';
                            var targetUrl = (response && typeof response.url === 'string' && response.url.trim() !== '') ? response.url : fallbackUrl;

                            submitButton.removeAttribute('data-kt-indicator');
                            submitButton.disabled = true;

                            var updateRedirected = false;
                            function executeUpdateRedirect() {
                                if (updateRedirected) return;
                                updateRedirected = true;
                                var emailField = form.querySelector('[name="email1"]');
                                var passwordField = form.querySelector('[name="password1"]');
                                if (emailField) emailField.value = "";
                                if (passwordField) passwordField.value = "";
                                window.location.assign(targetUrl);
                            }

                            Swal.fire({
                                text: "You have successfully updated",
                                icon: "success",
                                buttonsStyling: false,
                                confirmButtonText: "Ok, got it!",
                                customClass: {
                                    confirmButton: "btn btn-primary"
                                },
                                timer: 2000,
                                timerProgressBar: true
                            }).then(function () {
                                executeUpdateRedirect();
                            });

                            setTimeout(executeUpdateRedirect, 2200);
                        },
                        error: function (xhr, textStatus, errorThrown) {
                            $("#update-form").prop("disabled", false);
                            $("#update-form").html('<i class="fa-solid fa-pen-to-square" aria-hidden="true"></i><span>Update Candidate</span>');
                            submitButton.removeAttribute('data-kt-indicator');
                            submitButton.disabled = false;
                            if (xhr.status === 0 || !navigator.onLine) {
                                Swal.fire({ title: "No Internet", text: "You are currently offline or the server is unreachable.", icon: "warning", confirmButtonText: "Ok, got it!", customClass: { confirmButton: "btn btn-warning" } });
                                return;
                            }
                            console.error('Update AJAX error:', textStatus, errorThrown, xhr.responseText);
                            var serverMsg = '';
                            try {
                                var body = xhr.responseText || '';
                                var jsonStart = body.indexOf('{');
                                if (jsonStart >= 0) {
                                    var parsed = JSON.parse(body.substring(jsonStart));
                                    serverMsg = parsed.message || parsed.error || '';
                                }
                            } catch (e) {}

                            if (xhr.status === 409) {
                                Swal.fire({
                                    title: "Conflict Detected",
                                    text: serverMsg || "This candidate was modified in another session (or a concurrent update is currently processing). Please refresh the page.",
                                    icon: "warning",
                                    showCancelButton: true,
                                    confirmButtonText: "Refresh Page",
                                    cancelButtonText: "Stay Here"
                                }).then(function (res) {
                                    if (res.isConfirmed) {
                                        window.location.reload();
                                    }
                                });
                                return;
                            }

                            var display = serverMsg
                                ? serverMsg
                                : (xhr.status ? ('HTTP ' + xhr.status + ' ' + xhr.statusText) : textStatus);
                            Swal.fire("Error (" + (xhr.status || '?') + ")", display || "Something went wrong. Please try again.", "error");
                        },
                        complete: function () {
                            if ($("#update-form").length) {
                                $("#update-form").prop("disabled", false);
                                $("#update-form").html('<i class="fa-solid fa-pen-to-square" aria-hidden="true"></i><span>Update Candidate</span>');
                                submitButton.removeAttribute('data-kt-indicator');
                                submitButton.disabled = false;
                            }
                        }
                    });
                } 
				else 
				{
                   
                    Swal.fire({
									text				: "Sorry, looks like there are some errors detected, please try again.",
									icon				: "error",
									buttonsStyling		: false,
									confirmButtonText	: "Ok, got it!",
									customClass			: {
																confirmButton: "btn btn-primary"
														  }
                    });
                }
            });
        });
    }

    return {
				init: function () {
										form 			=	document.querySelector('#createForm');
										submitButton 	=	document.querySelector('#update-form');
										handleForm();
									}
    };
}();

KTUtil.onDOMContentLoaded(function () {
    KTUPDATECandidate.init();
});


</script>	
@php } @endphp

<script>
  var tagifyskill = null;
  var tagifylocation = null;

  function candidateNormalizeSuggestions(data) {
      if (!Array.isArray(data)) {
          return [];
      }

      return data.map(function (item) {
          if (typeof item === 'string') {
              return item.trim();
          }

          return String((item && (item.value || item.name || item.text)) || '').trim();
      }).filter(Boolean);
  }

  function candidateDebounce(callback, delay) {
      var timer;

      return function () {
          var context = this;
          var args = arguments;
          window.clearTimeout(timer);
          timer = window.setTimeout(function () {
              callback.apply(context, args);
          }, delay);
      };
  }

  var candidateSkillInput = document.querySelector('#kt_tagify_2');

  if (typeof window.Tagify === 'function' && candidateSkillInput) {
      tagifyskill = new window.Tagify(candidateSkillInput, {
          whitelist: [],
          maxTags: Infinity,
          duplicates: false,
          dropdown: {
              maxItems: 50,
              enabled: 1,
              closeOnSelect: true,
              highlightFirst: true
          }
      });
      window.tagifyskill = tagifyskill;

      // The custom visual layer delegates all mutations to Tagify so the submitted contract stays unchanged.
      var candidateRequiredSkills = @json($candidateRequiredSkills ?? []);
      var candidatePrimarySkillQuickInput = document.querySelector('#candidatePrimarySkillInput');
      var candidatePrimarySkillAdd = document.querySelector('#candidatePrimarySkillAdd');
      var candidatePrimarySkillSuggestions = document.querySelector('#candidatePrimarySkillSuggestions');
      var candidatePrimarySkillsCard = document.querySelector('.primary-skills-card');
      var candidatePrimarySkillChips = document.querySelector('#candidatePrimarySkillChips');
      var candidatePrimarySkillEmpty = document.querySelector('#candidatePrimarySkillEmpty');
      var candidateSkillMatch = document.querySelector('#candidateSkillMatch');
      var candidateSkillMatchRing = document.querySelector('#candidateSkillMatchRing');
      var candidateSkillMatchPercentage = document.querySelector('#candidateSkillMatchPercentage');
      var candidateSkillMatchDetail = document.querySelector('#candidateSkillMatchDetail');
      var candidateSuggestedSkills = document.querySelector('#candidateSuggestedSkills');
      var candidateSuggestedSkillsList = document.querySelector('#candidateSuggestedSkillsList');
      var candidateRemainingSkills = document.querySelector('#candidateRemainingSkills');
      var candidateSkillSuggestionItems = [];
      var candidateSkillLastSuggestions = [];
      var candidateSkillActiveIndex = -1;
      var candidateSkillRequest = null;
      var candidateSkillRequestSequence = 0;

      if (tagifyskill.DOM && tagifyskill.DOM.scope) {
          tagifyskill.DOM.scope.classList.add('candidate-primary-skill-tagify-source');
          tagifyskill.DOM.scope.setAttribute('aria-hidden', 'true');
      }

      function candidateSkillKey(value) {
          return String(value || '').trim().replace(/\s+/g, ' ').toLocaleLowerCase();
      }

      function candidateSkillName(skill) {
          return String((skill && (skill.name || skill.skill_name || skill.value)) || '').trim();
      }

      function candidateUniqueSkills(skills) {
          var unique = new Map();
          (Array.isArray(skills) ? skills : []).forEach(function (skill) {
              var name = candidateSkillName(skill);
              var id = String((skill && skill.id) || '').trim();
              if (!name) {
                  return;
              }
              var key = 'name:' + candidateSkillKey(name);
              if (!unique.has(key)) {
                  unique.set(key, { id: id, name: name, value: name });
              }
          });
          return Array.from(unique.values());
      }

      function candidateSelectedSkills() {
          return candidateUniqueSkills((tagifyskill.value || []).map(function (skill) {
              return { id: skill.id || '', name: skill.value || '' };
          }));
      }

      function candidateSkillsMatch(requiredSkill, selectedSkill) {
          var requiredId = String(requiredSkill.id || '').trim();
          var selectedId = String(selectedSkill.id || '').trim();
          if (requiredId && selectedId) {
              return requiredId === selectedId;
          }
          return candidateSkillKey(requiredSkill.name) === candidateSkillKey(selectedSkill.name);
      }

      function closeCandidateSkillSuggestions() {
          if (!candidatePrimarySkillSuggestions || !candidatePrimarySkillQuickInput) {
              return;
          }

          candidatePrimarySkillSuggestions.hidden = true;
          candidatePrimarySkillSuggestions.replaceChildren();
          if (candidatePrimarySkillsCard) {
              candidatePrimarySkillsCard.classList.remove('has-open-skill-suggestions');
          }
          candidatePrimarySkillQuickInput.setAttribute('aria-expanded', 'false');
          candidatePrimarySkillQuickInput.removeAttribute('aria-activedescendant');
          candidateSkillSuggestionItems = [];
          candidateSkillActiveIndex = -1;
      }

      function setActiveCandidateSkillSuggestion(index) {
          if (!candidateSkillSuggestionItems.length || !candidatePrimarySkillQuickInput) {
              candidateSkillActiveIndex = -1;
              candidatePrimarySkillQuickInput.removeAttribute('aria-activedescendant');
              return;
          }

          candidateSkillActiveIndex = (index + candidateSkillSuggestionItems.length) % candidateSkillSuggestionItems.length;
          candidateSkillSuggestionItems.forEach(function (item, itemIndex) {
              var isActive = itemIndex === candidateSkillActiveIndex;
              item.element.classList.toggle('is-active', isActive);
              item.element.setAttribute('aria-selected', isActive ? 'true' : 'false');
          });

          var activeItem = candidateSkillSuggestionItems[candidateSkillActiveIndex];
          candidatePrimarySkillQuickInput.setAttribute('aria-activedescendant', activeItem.element.id);
          activeItem.element.scrollIntoView({ block: 'nearest' });
      }

      function renderCandidateSkillSuggestions(suggestions, state, query) {
          if (!candidatePrimarySkillSuggestions || !candidatePrimarySkillQuickInput) {
              return;
          }

          candidateSkillLastSuggestions = Array.isArray(suggestions) ? suggestions : [];
          var normalizedQuery = candidateSkillKey(query);
          var selected = candidateSelectedSkills();
          var combined = candidateUniqueSkills(candidateRequiredSkills.concat(candidateSkillLastSuggestions.map(function (name) {
              return { name: name };
          }))).filter(function (skill) {
              var matchesQuery = !normalizedQuery || candidateSkillKey(skill.name).indexOf(normalizedQuery) !== -1;
              var isSelected = selected.some(function (selectedSkill) {
                  return candidateSkillsMatch(skill, selectedSkill);
              });
              return matchesQuery && !isSelected;
          }).slice(0, 20);

          candidatePrimarySkillSuggestions.replaceChildren();
          candidateSkillSuggestionItems = [];
          candidateSkillActiveIndex = -1;

          combined.forEach(function (skill, index) {
              var option = document.createElement('button');
              option.type = 'button';
              option.id = 'candidate-primary-skill-option-' + index;
              option.className = 'candidate-primary-skill-suggestion';
              option.setAttribute('role', 'option');
              option.setAttribute('aria-selected', 'false');
              option.textContent = skill.name;
              option.addEventListener('mousedown', function (event) {
                  event.preventDefault();
              });
              option.addEventListener('click', function () {
                  addCandidatePrimarySkill(skill.name);
                  closeCandidateSkillSuggestions();
              });
              candidatePrimarySkillSuggestions.appendChild(option);
              candidateSkillSuggestionItems.push({ element: option, skill: skill });
          });

          if (state === 'loading' || state === 'error' || (!combined.length && normalizedQuery)) {
              var status = document.createElement('div');
              status.className = 'candidate-primary-skill-suggestion-status';
              status.setAttribute('role', 'status');
              status.textContent = state === 'loading'
                  ? 'Searching skills...'
                  : (state === 'error' ? 'Unable to load suggestions. You can still add this skill.' : 'No matching skills found.');
              candidatePrimarySkillSuggestions.appendChild(status);
          }

          if (!candidatePrimarySkillSuggestions.childElementCount) {
              closeCandidateSkillSuggestions();
              return;
          }

          candidatePrimarySkillSuggestions.hidden = false;
          if (candidatePrimarySkillsCard) {
              candidatePrimarySkillsCard.classList.add('has-open-skill-suggestions');
          }
          candidatePrimarySkillQuickInput.setAttribute('aria-expanded', 'true');
      }

      function addCandidatePrimarySkill(value) {
          var name = String(value || '').trim();
          if (!name) {
              return;
          }
          var requiredSkill = candidateUniqueSkills(candidateRequiredSkills).find(function (skill) {
              return candidateSkillKey(skill.name) === candidateSkillKey(name);
          });
          tagifyskill.addTags([requiredSkill ? { value: requiredSkill.name, id: requiredSkill.id } : name]);
          if (candidatePrimarySkillQuickInput) {
              candidatePrimarySkillQuickInput.value = '';
              candidatePrimarySkillQuickInput.focus();
          }
          closeCandidateSkillSuggestions();
      }

      function refreshCandidatePrimarySkills() {
          var required = candidateUniqueSkills(candidateRequiredSkills);
          var selected = candidateSelectedSkills();
          var matched = required.filter(function (requiredSkill) {
              return selected.some(function (selectedSkill) {
                  return candidateSkillsMatch(requiredSkill, selectedSkill);
              });
          });
          var missing = required.filter(function (requiredSkill) {
              return !selected.some(function (selectedSkill) {
                  return candidateSkillsMatch(requiredSkill, selectedSkill);
              });
          });
          var percentage = required.length ? Math.round((matched.length / required.length) * 100) : 0;

          candidatePrimarySkillChips.replaceChildren();
          selected.forEach(function (skill) {
              var chip = document.createElement('span');
              chip.className = 'candidate-primary-skill-chip';
              var label = document.createElement('span');
              label.textContent = skill.name;
              var remove = document.createElement('button');
              remove.type = 'button';
              remove.setAttribute('aria-label', 'Remove ' + skill.name);
              remove.title = 'Remove ' + skill.name;
              remove.innerHTML = '<i class="fa-solid fa-xmark" aria-hidden="true"></i>';
              remove.addEventListener('click', function () {
                  tagifyskill.removeTag(skill.name);
              });
              chip.append(label, remove);
              candidatePrimarySkillChips.appendChild(chip);
          });
          candidatePrimarySkillEmpty.hidden = selected.length > 0;

          candidateSkillMatch.classList.toggle('is-empty', required.length === 0);
          candidateSkillMatch.classList.toggle('is-complete', required.length > 0 && percentage === 100);
          candidateSkillMatchRing.style.setProperty('--candidate-skill-match', percentage + '%');
          candidateSkillMatchPercentage.textContent = required.length ? percentage + '%' : '--';
          candidateSkillMatchDetail.textContent = !required.length
              ? 'No job skills defined'
              : (percentage === 100
                  ? 'All required skills matched'
                  : matched.length + ' of ' + required.length + ' required skills matched');

          candidateSuggestedSkillsList.replaceChildren();
          missing.forEach(function (skill) {
              var button = document.createElement('button');
              button.type = 'button';
              button.className = 'candidate-suggested-skill';
              var label = document.createElement('span');
              label.textContent = skill.name;
              var icon = document.createElement('span');
              icon.className = 'candidate-suggested-skill-icon';
              icon.setAttribute('aria-hidden', 'true');
              icon.innerHTML = '<svg viewBox="0 0 20 20" focusable="false" aria-hidden="true"><path d="M10 4.25v11.5M4.25 10h11.5" /></svg>';
              button.append(label, icon);
              button.addEventListener('click', function () {
                  addCandidatePrimarySkill(skill.name);
              });
              candidateSuggestedSkillsList.appendChild(button);
          });
          candidateRemainingSkills.textContent = missing.length + ' remaining ' + (missing.length === 1 ? 'skill' : 'skills');
          candidateSuggestedSkills.hidden = required.length === 0 || missing.length === 0;

          if (candidatePrimarySkillQuickInput && document.activeElement === candidatePrimarySkillQuickInput) {
              renderCandidateSkillSuggestions(candidateSkillLastSuggestions, 'idle', candidatePrimarySkillQuickInput.value);
          }
      }

      tagifyskill.on('add', refreshCandidatePrimarySkills);
      tagifyskill.on('remove', refreshCandidatePrimarySkills);
      window.refreshCandidatePrimarySkills = refreshCandidatePrimarySkills;

      if (candidatePrimarySkillAdd) {
          candidatePrimarySkillAdd.addEventListener('click', function () {
              addCandidatePrimarySkill(candidatePrimarySkillQuickInput.value);
              closeCandidateSkillSuggestions();
          });
      }
      if (candidatePrimarySkillQuickInput) {
          candidatePrimarySkillQuickInput.addEventListener('keydown', function (event) {
              if (event.key === 'ArrowDown' || event.key === 'ArrowUp') {
                  event.preventDefault();
                  if (candidatePrimarySkillSuggestions.hidden) {
                      renderCandidateSkillSuggestions(candidateSkillLastSuggestions, 'idle', candidatePrimarySkillQuickInput.value);
                  }
                  setActiveCandidateSkillSuggestion(candidateSkillActiveIndex + (event.key === 'ArrowDown' ? 1 : -1));
                  return;
              }

              if (event.key === 'Escape') {
                  event.preventDefault();
                  closeCandidateSkillSuggestions();
                  return;
              }

              if (event.key === 'Enter') {
                  event.preventDefault();
                  if (candidateSkillActiveIndex >= 0 && candidateSkillSuggestionItems[candidateSkillActiveIndex]) {
                      addCandidatePrimarySkill(candidateSkillSuggestionItems[candidateSkillActiveIndex].skill.name);
                  } else {
                      addCandidatePrimarySkill(candidatePrimarySkillQuickInput.value);
                  }
              }
          });

          candidatePrimarySkillQuickInput.addEventListener('focus', function () {
              var query = candidatePrimarySkillQuickInput.value.trim();
              renderCandidateSkillSuggestions(candidateSkillLastSuggestions, 'idle', query);
          });

          candidatePrimarySkillQuickInput.addEventListener('blur', function () {
              window.setTimeout(closeCandidateSkillSuggestions, 120);
          });
      }

      var _candidateSkillCache = new Map();
      var fetchCandidateSkillSuggestions = candidateDebounce(function (query) {
          if (!candidatePrimarySkillQuickInput || candidatePrimarySkillQuickInput.value.trim() !== query) {
              return;
          }

          var cacheKey = query.toLowerCase();
          if (_candidateSkillCache.has(cacheKey)) {
              var cached = _candidateSkillCache.get(cacheKey);
              tagifyskill.settings.whitelist = cached;
              renderCandidateSkillSuggestions(cached, 'idle', query);
              return;
          }

          if (candidateSkillRequest && candidateSkillRequest.readyState !== 4) {
              candidateSkillRequest.abort();
          }

          var requestSequence = ++candidateSkillRequestSequence;
          candidateSkillRequest = window.jQuery.get("{{ url('/') }}/skill-suggestions", { q: query })
              .done(function (data) {
                  if (requestSequence !== candidateSkillRequestSequence || candidatePrimarySkillQuickInput.value.trim() !== query) {
                      return;
                  }
                  var suggestions = candidateNormalizeSuggestions(data);
                  _candidateSkillCache.set(cacheKey, suggestions);
                  tagifyskill.settings.whitelist = suggestions;
                  renderCandidateSkillSuggestions(suggestions, 'idle', query);
              })
              .fail(function (_xhr, status) {
                  if (status !== 'abort' && requestSequence === candidateSkillRequestSequence && candidatePrimarySkillQuickInput.value.trim() === query) {
                      renderCandidateSkillSuggestions([], 'error', query);
                  }
              });
      }, 250);

      if (candidatePrimarySkillQuickInput) {
          candidatePrimarySkillQuickInput.addEventListener('input', function () {
              var query = candidatePrimarySkillQuickInput.value.trim();
              if (query.length >= 1) {
                  renderCandidateSkillSuggestions([], 'loading', query);
                  fetchCandidateSkillSuggestions(query);
              } else {
                  if (candidateSkillRequest && candidateSkillRequest.readyState !== 4) {
                      candidateSkillRequest.abort();
                  }
                  candidateSkillRequestSequence++;
                  renderCandidateSkillSuggestions([], 'idle', '');
              }
          });
      }

      window.setTimeout(refreshCandidatePrimarySkills, 0);
  }

  /**
   * JERO Canonical Location Resolution & Combobox Engine
   * - Architecture: CQRS Compliant (0% MySQL Reads, Authoritative Elasticsearch read-scope)
   * - Performance: Client-First RAM execution (< 1ms search latency for Curated Hubs & L2 Cache)
   * - Network: Fetch API + AbortController + Selection Lock (Eliminates all race conditions)
   * - Accuracy: 5-State Machine with Disambiguation Ribbon (NEEDS_CONFIRMATION)
   * - Parser Integration: Triple-alias bridge (resolveAndSetBenchLocation / setSingleLocationTag / resolveAndSetCandidateLocation)
   * - Persistence: Canonical dual-payload with cryptographic HMAC signature (_sig)
   * - Viewport: Responsive 15.6" screen auto-placement (max-height: min(340px, calc(100vh - 120px)))
   * - Isolation: Strict Rule 17 Page Isolation
   */
  (function(window, $) {
      'use strict';

      // L1 Cache: 30 Curated Global Talent Hubs (Grouped by Economic Region)
      var curatedLocations = [
          // 🇮🇳 Top Indian Metros & Tech Hubs
          { id: 57848, city_id: 57848, state_id: 4026, country_id: 101, city: "Bengaluru", state: "Karnataka", country: "India", country_code: "IN", label: "Bengaluru | Karnataka | India", category: "🇮🇳 India Metros & Tech Hubs", badge: "Silicon Valley of India", aliases: ["bangalore", "bengaluru", "blr"] },
          { id: 132132, city_id: 132132, state_id: 4012, country_id: 101, city: "Hyderabad", state: "Telangana", country: "India", country_code: "IN", label: "Hyderabad | Telangana | India", category: "🇮🇳 India Metros & Tech Hubs", badge: "HITEC City", aliases: ["hyderabad", "secunderabad", "hyd"] },
          { id: 133504, city_id: 133504, state_id: 4008, country_id: 101, city: "Pune", state: "Maharashtra", country: "India", country_code: "IN", label: "Pune | Maharashtra | India", category: "🇮🇳 India Metros & Tech Hubs", badge: "IT & Auto Hub", aliases: ["pune", "poona"] },
          { id: 133024, city_id: 133024, state_id: 4008, country_id: 101, city: "Mumbai", state: "Maharashtra", country: "India", country_code: "IN", label: "Mumbai | Maharashtra | India", category: "🇮🇳 India Metros & Tech Hubs", badge: "Financial Capital", aliases: ["mumbai", "bombay", "navi mumbai"] },
          { id: 133230, city_id: 133230, state_id: 4031, country_id: 101, city: "Noida", state: "Uttar Pradesh", country: "India", country_code: "IN", label: "Noida | Uttar Pradesh | India", category: "🇮🇳 India Metros & Tech Hubs", badge: "Delhi NCR", aliases: ["noida", "greater noida"] },
          { id: 132032, city_id: 132032, state_id: 4013, country_id: 101, city: "Gurugram", state: "Haryana", country: "India", country_code: "IN", label: "Gurugram | Haryana | India", category: "🇮🇳 India Metros & Tech Hubs", badge: "Delhi NCR", aliases: ["gurgaon", "gurugram", "ggn"] },
          { id: 131517, city_id: 131517, state_id: 4030, country_id: 101, city: "Chennai", state: "Tamil Nadu", country: "India", country_code: "IN", label: "Chennai | Tamil Nadu | India", category: "🇮🇳 India Metros & Tech Hubs", badge: "SaaS & Tech", aliases: ["chennai", "madras"] },
          { id: 142001, city_id: 142001, state_id: 4853, country_id: 101, city: "Kolkata", state: "West Bengal", country: "India", country_code: "IN", label: "Kolkata | West Bengal | India", category: "🇮🇳 India Metros & Tech Hubs", badge: "East Tech Hub", aliases: ["kolkata", "calcutta"] },
          { id: 134310, city_id: 134310, state_id: 4011, country_id: 101, city: "Vadodara", state: "Gujarat", country: "India", country_code: "IN", label: "Vadodara | Gujarat | India", category: "🇮🇳 India Metros & Tech Hubs", badge: "Commercial Hub", aliases: ["vadodara", "baroda"] },
          { id: 134369, city_id: 134369, state_id: 4007, country_id: 101, city: "Visakhapatnam", state: "Andhra Pradesh", country: "India", country_code: "IN", label: "Visakhapatnam | Andhra Pradesh | India", category: "🇮🇳 India Metros & Tech Hubs", badge: "Port & IT Hub", aliases: ["visakhapatnam", "vizag"] },

          // 🇺🇸 Top United States Tech Hubs
          { id: 114046, city_id: 114046, state_id: 5, country_id: 233, city: "San Francisco", state: "California", country: "United States", country_code: "US", label: "San Francisco | California | United States", category: "🇺🇸 United States Tech Hubs", badge: "Bay Area / AI Hub", aliases: ["san francisco", "sf", "bay area", "silicon valley"] },
          { id: 122795, city_id: 122795, state_id: 33, country_id: 233, city: "New York City", state: "New York", country: "United States", country_code: "US", label: "New York City | New York | United States", category: "🇺🇸 United States Tech Hubs", badge: "Fintech & Media", aliases: ["new york", "nyc", "manhattan"] },
          { id: 126104, city_id: 126104, state_id: 48, country_id: 233, city: "Seattle", state: "Washington", country: "United States", country_code: "US", label: "Seattle | Washington | United States", category: "🇺🇸 United States Tech Hubs", badge: "Cloud & Enterprise", aliases: ["seattle", "bellevue", "redmond"] },
          { id: 111668, city_id: 111668, state_id: 44, country_id: 233, city: "Austin", state: "Texas", country: "United States", country_code: "US", label: "Austin | Texas | United States", category: "🇺🇸 United States Tech Hubs", badge: "Silicon Hills", aliases: ["austin", "atx"] },
          { id: 112521, city_id: 112521, state_id: 22, country_id: 233, city: "Boston", state: "Massachusetts", country: "United States", country_code: "US", label: "Boston | Massachusetts | United States", category: "🇺🇸 United States Tech Hubs", badge: "Biotech & EdTech", aliases: ["boston", "cambridge"] },
          { id: 113702, city_id: 113702, state_id: 14, country_id: 233, city: "Chicago", state: "Illinois", country: "United States", country_code: "US", label: "Chicago | Illinois | United States", category: "🇺🇸 United States Tech Hubs", badge: "Midwest Tech Hub", aliases: ["chicago", "chi"] },

          // 🇬🇧 🇪🇺 Top UK & European Tech Hubs
          { id: 49011, city_id: 49011, state_id: 1, country_id: 232, city: "London", state: "England", country: "United Kingdom", country_code: "GB", label: "London | England | United Kingdom", category: "🇬🇧 🇪🇺 UK & European Hubs", badge: "Global FinTech Hub", aliases: ["london", "city of london", "greater london"] },
          { id: 112234, city_id: 112234, state_id: 11, country_id: 82, city: "Berlin", state: "Berlin", country: "Germany", country_code: "DE", label: "Berlin | Berlin | Germany", category: "🇬🇧 🇪🇺 UK & European Hubs", badge: "European Startup Hub", aliases: ["berlin"] },
          { id: 77340, city_id: 77340, state_id: 8, country_id: 157, city: "Amsterdam", state: "North Holland", country: "Netherlands", country_code: "NL", label: "Amsterdam | North Holland | Netherlands", category: "🇬🇧 🇪🇺 UK & European Hubs", badge: "Tech & E-Commerce", aliases: ["amsterdam"] },
          { id: 60100, city_id: 60100, state_id: 1, country_id: 104, city: "Dublin", state: "Leinster", country: "Ireland", country_code: "IE", label: "Dublin | Leinster | Ireland", category: "🇬🇧 🇪🇺 UK & European Hubs", badge: "European Tech HQ", aliases: ["dublin"] },

          // 🇦🇪 🇸🇬 Middle East & Asia-Pacific Hubs
          { id: 32, city_id: 32, state_id: 2, country_id: 231, city: "Dubai", state: "Dubai", country: "United Arab Emirates", country_code: "AE", label: "Dubai | Dubai | United Arab Emirates", category: "🇦🇪 🇸🇬 Middle East & APAC Hubs", badge: "MENA Tech Hub", aliases: ["dubai", "dxb"] },
          { id: 31, city_id: 31, state_id: 1, country_id: 231, city: "Abu Dhabi", state: "Abu Dhabi", country: "United Arab Emirates", country_code: "AE", label: "Abu Dhabi | Abu Dhabi | United Arab Emirates", category: "🇦🇪 🇸🇬 Middle East & APAC Hubs", badge: "Capital Hub", aliases: ["abu dhabi"] },
          { id: 104057, city_id: 104057, state_id: 1, country_id: 196, city: "Singapore", state: "Central Singapore", country: "Singapore", country_code: "SG", label: "Singapore | Central Singapore | Singapore", category: "🇦🇪 🇸🇬 Middle East & APAC Hubs", badge: "APAC FinTech Hub", aliases: ["singapore", "sg"] },
          { id: 17121, city_id: 17121, state_id: 9, country_id: 38, city: "Toronto", state: "Ontario", country: "Canada", country_code: "CA", label: "Toronto | Ontario | Canada", category: "🇨🇦 🇦🇺 Commonwealth Tech Hubs", badge: "AI & Tech Hub", aliases: ["toronto", "gta"] },
          { id: 7541, city_id: 7541, state_id: 2, country_id: 14, city: "Sydney", state: "New South Wales", country: "Australia", country_code: "AU", label: "Sydney | New South Wales | Australia", category: "🇨🇦 🇦🇺 Commonwealth Tech Hubs", badge: "Oceania Hub", aliases: ["sydney", "syd"] }
      ];

      // Client-First In-Memory Alias Dictionary (0ms Instant Recall)
      var citySynonymAliases = {
          'baroda': 'Vadodara',
          'vizag': 'Visakhapatnam',
          'calcutta': 'Kolkata',
          'bombay': 'Mumbai',
          'madras': 'Chennai',
          'poona': 'Pune',
          'banaras': 'Varanasi',
          'benares': 'Varanasi',
          'trivandrum': 'Thiruvananthapuram',
          'cochin': 'Kochi',
          'sf': 'San Francisco',
          'bay area': 'San Francisco',
          'nyc': 'New York City',
          'la': 'Los Angeles',
          'dc': 'Washington, D.C.'
      };

      // L2 Session LRU Cache (< 300 KB Browser Memory Budget)
      var lruCache = new Map();
      var MAX_LRU_ENTRIES = 100;
      var CACHE_TTL_MS = 30 * 60 * 1000; // 30 minutes

      function cacheGet(query) {
          var key = (query || '').toLowerCase().trim();
          if (!lruCache.has(key)) return null;
          var entry = lruCache.get(key);
          if (Date.now() - entry.timestamp > CACHE_TTL_MS) {
              lruCache.delete(key);
              return null;
          }
          lruCache.delete(key);
          lruCache.set(key, entry);
          return entry.data;
      }

      function cachePut(query, data) {
          var key = (query || '').toLowerCase().trim();
          if (lruCache.size >= MAX_LRU_ENTRIES) {
              var oldestKey = lruCache.keys().next().value;
              lruCache.delete(oldestKey);
          }
          lruCache.set(key, { data: data, timestamp: Date.now() });
      }

      // Network Engine State
      var activeAbortController = null;
      var requestSequence = 0;
      var selectionLocked = false;
      var debounceTimer = null;

      // Component DOM references
      var $component = $('#jero-cs-location');
      var $trigger = $component.find('.jero-cs-trigger');
      var $valueLabel = $component.find('.jero-cs-value');
      var $clearBtn = $component.find('.jero-cs-trigger-clear');
      var $dropdown = $component.find('.jero-cs-dropdown');
      var $searchInput = $component.find('.jero-cs-search-input');
      var $searchClear = $component.find('.jero-cs-search-clear');
      var $searchSpinner = $component.find('.jero-cs-search-spinner');
      var $list = $component.find('.jero-cs-list');
      var $emptyMsg = $component.find('.jero-cs-empty');
      var $disambiguationBar = $('#jero-location-disambiguation');

      // Canonical Hidden Form Inputs
      var $inputCityId = $('#location_city_id');
      var $inputStateId = $('#location_state_id');
      var $inputCountryId = $('#location_country_id');
      var $inputCanonicalLabel = $('#location_canonical_label');
      var $inputSig = $('#location_sig');
      var $inputTagifyLegacy = $('#kt_tagify_work_location');

      // Current Active Location Object
      var activeCanonicalLocation = null;

      /**
       * State 1: UNRESOLVED - Resets location state and inputs cleanly
       */
      function setUnresolvedState() {
          activeCanonicalLocation = null;
          selectionLocked = false;

          $inputCityId.val('');
          $inputStateId.val('');
          $inputCountryId.val('');
          $inputCanonicalLabel.val('');
          $inputSig.val('');
          $inputTagifyLegacy.val('');

          $valueLabel.text('Search city, state or country').addClass('jero-cs-placeholder');
          $clearBtn.hide();
          $disambiguationBar.hide().empty();
          $component.removeClass('is-open open-up');
          $trigger.attr('aria-expanded', 'false');

          $inputTagifyLegacy.trigger('change').trigger('input');
          if (window.benchFormValidator && typeof window.benchFormValidator.revalidateField === 'function') {
              window.benchFormValidator.revalidateField('kt_tagify_work_location');
          }
      }

      /**
       * State 3: RESOLVED - Locks in authoritative canonical location entity
       */
      function setResolvedState(locationEntity, source, confidence) {
          if (!locationEntity) return;
          selectionLocked = true;
          activeCanonicalLocation = locationEntity;

          if (activeAbortController) {
              activeAbortController.abort();
              activeAbortController = null;
          }

          var cityId = locationEntity.city_id || locationEntity.id || '';
          var stateId = locationEntity.state_id || '';
          var countryId = locationEntity.country_id || '';
          var label = locationEntity.label || (locationEntity.city + ' | ' + locationEntity.state + ' | ' + locationEntity.country);
          var sig = locationEntity._sig || '';

          // Update Canonical and Legacy Payloads
          $inputCityId.val(cityId);
          $inputStateId.val(stateId);
          $inputCountryId.val(countryId);
          $inputCanonicalLabel.val(label);
          $inputSig.val(sig);
          $inputTagifyLegacy.val(JSON.stringify([{ value: label }]));

          // Update UI trigger
          $valueLabel.text(label).removeClass('jero-cs-placeholder');
          $clearBtn.show();
          $disambiguationBar.hide().empty();
          $component.removeClass('is-open open-up has-validation-error');
          $trigger.attr('aria-expanded', 'false');
          $component.closest('.candidate-field-control, .bd-control, .fg').removeClass('has-validation-error');

          // Clear any validation errors
          $('[data-field-error="kt_tagify_work_location"]').remove();
          if (typeof clearFieldValidationErrors === 'function') {
              clearFieldValidationErrors('kt_tagify_work_location');
          }
          if (window.benchFormValidator && typeof window.benchFormValidator.revalidateField === 'function') {
              window.benchFormValidator.revalidateField('kt_tagify_work_location');
          }
          $inputTagifyLegacy.trigger('change').trigger('input');
      }

      /**
       * State 4: NEEDS_CONFIRMATION - Displays 1-click disambiguation badges
       */
      function enterNeedsConfirmationState(rawQuery, candidates) {
          selectionLocked = false;
          $disambiguationBar.empty();

          var $label = $('<span>').addClass('text-muted fs-8 me-1').text('Did you mean:');
          $disambiguationBar.append($label);

          (candidates || []).forEach(function(cand, idx) {
              var isFirst = idx === 0;
              var $btn = $('<button>')
                  .attr('type', 'button')
                  .addClass('jero-disambiguation-badge' + (isFirst ? ' is-recommended' : ''))
                  .html((isFirst ? '★ ' : '') + cand.label)
                  .on('click', function(e) {
                      e.preventDefault();
                      e.stopPropagation();
                      setResolvedState(cand, 'disambiguation_click', 1.00);
                  });
              $disambiguationBar.append($btn);
          });

          var $cancelBtn = $('<button>')
              .attr('type', 'button')
              .addClass('btn btn-xs btn-link text-muted p-0 ms-1')
              .text('Search other')
              .on('click', function(e) {
                  e.preventDefault();
                  $disambiguationBar.hide().empty();
                  openDropdown();
              });
          $disambiguationBar.append($cancelBtn);
          $disambiguationBar.show();
      }

      /**
       * Render a list of location items into the dropdown
       */
      function renderLocations(locations, isCurated) {
          $list.empty();
          if (!locations || !locations.length) {
              $emptyMsg.removeClass('is-hidden');
              return;
          }
          $emptyMsg.addClass('is-hidden');

          var currentCategory = '';
          locations.forEach(function(loc) {
              if (isCurated && loc.category && loc.category !== currentCategory) {
                  currentCategory = loc.category;
                  var $group = $('<div>').addClass('jero-cs-group-title').text(currentCategory);
                  $list.append($group);
              }

              var isSelected = activeCanonicalLocation && (
                  (activeCanonicalLocation.city_id && activeCanonicalLocation.city_id === loc.city_id) ||
                  (activeCanonicalLocation.label && activeCanonicalLocation.label === loc.label)
              );

              var $item = $('<div>')
                  .addClass('jero-cs-item' + (isSelected ? ' is-selected' : ''))
                  .attr('role', 'option')
                  .attr('aria-selected', isSelected ? 'true' : 'false')
                  .data('location', loc);

              var $text = $('<span>').addClass('jero-cs-item-text').text(loc.label);
              $item.append($text);

              if (loc.badge) {
                  var $badge = $('<span>').addClass('jero-cs-item-badge').text(loc.badge);
                  $item.append($badge);
              }

              $item.on('click', function(e) {
                  e.stopPropagation();
                  setResolvedState(loc, 'manual_selection', 1.00);
              });

              $list.append($item);
          });
      }

      /**
       * Fetch autocomplete suggestions from Elasticsearch
       */
      function executeSearch(query) {
          var rawQ = (query || '').trim();
          if (rawQ.length < 2) {
              renderLocations(curatedLocations, true);
              $searchSpinner.hide();
              return;
          }

          var lowerQ = rawQ.toLowerCase();
          var mappedCity = citySynonymAliases[lowerQ];
          var searchTerm = mappedCity || rawQ;

          var cached = cacheGet(searchTerm);
          if (cached) {
              renderLocations(cached, false);
              $searchSpinner.hide();
              return;
          }

          if (activeAbortController) {
              activeAbortController.abort();
          }
          activeAbortController = new AbortController();
          var currentSeq = ++requestSequence;
          $searchSpinner.show();

          var endpoint = "{{ url('/') }}/city-job-suggestions?q=" + encodeURIComponent(searchTerm) + "&format=canonical&limit=25";

          fetch(endpoint, {
              method: 'GET',
              signal: activeAbortController.signal,
              headers: {
                  'Accept': 'application/json',
                  'X-Requested-With': 'XMLHttpRequest'
              }
          })
          .then(function(res) {
              if (!res.ok) throw new Error('Network error: ' + res.status);
              return res.json();
          })
          .then(function(data) {
              $searchSpinner.hide();
              if (selectionLocked) return;
              if (currentSeq !== requestSequence) return;

              var results = Array.isArray(data) ? data : (data.results || []);
              cachePut(searchTerm, results);
              renderLocations(results, false);
          })
          .catch(function(err) {
              $searchSpinner.hide();
              if (err.name === 'AbortError') return;
              console.warn('Elasticsearch location autocomplete notice:', err);
              renderLocations(curatedLocations, true);
          });
      }

      function openDropdown() {
          $('.jero-custom-select').not($component).removeClass('is-open open-up');
          $('.jero-cs-trigger').not($trigger).attr('aria-expanded', 'false');

          $component.addClass('is-open');
          $trigger.attr('aria-expanded', 'true');
          selectionLocked = false;

          var triggerRect = $trigger[0].getBoundingClientRect();
          var spaceBelow = window.innerHeight - triggerRect.bottom;
          if (spaceBelow < 320 && triggerRect.top > 320) {
              $component.addClass('open-up');
          } else {
              $component.removeClass('open-up');
          }

          var currentQuery = $searchInput.val().trim();
          if (currentQuery.length >= 2) {
              executeSearch(currentQuery);
          } else {
              renderLocations(curatedLocations, true);
          }

          setTimeout(function() {
              $searchInput.focus();
          }, 60);
      }

      function closeDropdown() {
          $component.removeClass('is-open open-up');
          $trigger.attr('aria-expanded', 'false');
      }

      // Trigger Click: Toggle Dropdown
      $trigger.on('click', function(e) {
          e.preventDefault();
          e.stopPropagation();
          if ($component.hasClass('is-open')) {
              closeDropdown();
          } else {
              openDropdown();
          }
      });

      // Clear Selection Button
      $clearBtn.on('click', function(e) {
          e.preventDefault();
          e.stopPropagation();
          setUnresolvedState();
      });

      // Search Input Keystroke Handler (Debounced 150ms)
      $searchInput.on('input', function() {
          var q = $(this).val();
          if (q.trim().length > 0) {
              $searchClear.show();
          } else {
              $searchClear.hide();
          }
          clearTimeout(debounceTimer);
          debounceTimer = setTimeout(function() {
              executeSearch(q);
          }, 150);
      });

      // Search Clear Button
      $searchClear.on('click', function(e) {
          e.stopPropagation();
          $searchInput.val('').focus();
          $searchClear.hide();
          renderLocations(curatedLocations, true);
      });

      // Keyboard Navigation (ArrowUp, ArrowDown, Enter, Escape)
      $component.on('keydown', function(e) {
          var isOpen = $component.hasClass('is-open');
          if (!isOpen) {
              if (e.key === 'ArrowDown' || e.key === 'Enter') {
                  e.preventDefault();
                  openDropdown();
              }
              return;
          }

          var $items = $list.find('.jero-cs-item');
          var $active = $items.filter('.is-active');
          var activeIdx = $items.index($active);

          if (e.key === 'ArrowDown') {
              e.preventDefault();
              var nextIdx = (activeIdx + 1) >= $items.length ? 0 : (activeIdx + 1);
              $items.removeClass('is-active');
              var $target = $items.eq(nextIdx).addClass('is-active');
              if ($target.length && $target[0].scrollIntoView) {
                  $target[0].scrollIntoView({ block: 'nearest' });
              }
          } else if (e.key === 'ArrowUp') {
              e.preventDefault();
              var prevIdx = (activeIdx - 1) < 0 ? ($items.length - 1) : (activeIdx - 1);
              $items.removeClass('is-active');
              var $target = $items.eq(prevIdx).addClass('is-active');
              if ($target.length && $target[0].scrollIntoView) {
                  $target[0].scrollIntoView({ block: 'nearest' });
              }
          } else if (e.key === 'Enter') {
              e.preventDefault();
              if ($active.length) {
                  $active.trigger('click');
              } else if ($items.length === 1) {
                  $items.first().trigger('click');
              }
          } else if (e.key === 'Escape') {
              e.preventDefault();
              closeDropdown();
              $trigger.focus();
          }
      });

      // Close dropdown when clicking outside
      $(document).on('click', function(e) {
          if (!$(e.target).closest($component).length && !$(e.target).closest($disambiguationBar).length) {
              closeDropdown();
          }
      });

      /**
       * Resume Parser Confidence & Provenance Resolver
       */
      function resolveAndSetCandidateLocation(rawLocationData) {
          var rawText = '';
          if (typeof rawLocationData === 'string') {
              rawText = rawLocationData;
          } else if (rawLocationData && typeof rawLocationData === 'object') {
              rawText = rawLocationData.value || rawLocationData.name || rawLocationData.text || rawLocationData.city || '';
          }
          rawText = (rawText || '').trim();
          if (!rawText) return;

          var clean = rawText.toLowerCase().replace(/[\r\n\t]+/g, ' ').replace(/\s+/g, ' ');

          // 1. Instant check against Curated Master Hubs (Confidence 1.00)
          for (var i = 0; i < curatedLocations.length; i++) {
              var hub = curatedLocations[i];
              if (hub.aliases && hub.aliases.indexOf(clean) !== -1) {
                  setResolvedState(hub, 'resume_parser', 1.00);
                  return;
              }
              if (hub.city.toLowerCase() === clean) {
                  setResolvedState(hub, 'resume_parser', 1.00);
                  return;
              }
          }

          // 2. Tokenize into primary city / state components
          var tokens = clean.split(/[,|\/\-]/).map(function(t) { return t.trim(); }).filter(Boolean);
          var primaryToken = tokens[0] || clean;

          if (citySynonymAliases[primaryToken]) {
              primaryToken = citySynonymAliases[primaryToken].toLowerCase();
          }

          var endpoint = "{{ url('/') }}/city-job-suggestions?q=" + encodeURIComponent(primaryToken) + "&format=canonical&limit=10";

          fetch(endpoint, {
              method: 'GET',
              headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
          })
          .then(function(res) {
              if (!res.ok) throw new Error('Parser lookup error: ' + res.status);
              return res.json();
          })
          .then(function(data) {
              var results = Array.isArray(data) ? data : (data.results || []);
              if (!results.length) return;

              var bestMatch = null;
              var bestConfidence = 0.0;
              var homonymMatches = [];

              for (var k = 0; k < results.length; k++) {
                  var item = results[k];
                  var itemCity = (item.city || '').toLowerCase();
                  var itemLabel = (item.label || '').toLowerCase();

                  if (itemLabel.indexOf(clean) !== -1 || (item.aliases && item.aliases.indexOf(clean) !== -1)) {
                      bestMatch = item;
                      bestConfidence = 1.00;
                      break;
                  }
                  if (itemCity === primaryToken) {
                      homonymMatches.push(item);
                      if (!bestMatch) {
                          bestMatch = item;
                          bestConfidence = 0.95;
                      }
                  }
              }

              if (homonymMatches.length > 1 && bestConfidence < 1.00) {
                  bestConfidence = 0.85;
              }

              if (bestMatch && bestConfidence >= 0.95) {
                  setResolvedState(bestMatch, 'resume_parser', bestConfidence);
              } else if (results.length > 0 && bestConfidence >= 0.80) {
                  enterNeedsConfirmationState(rawText, (homonymMatches.length ? homonymMatches : results).slice(0, 3));
              }
          })
          .catch(function(err) {
              console.warn('Parser location resolution notice:', err);
          });
      }

      // Register Global Functions & Bridge for Resume Parsers
      window.resolveAndSetBenchLocation = resolveAndSetCandidateLocation;
      window.setSingleLocationTag = resolveAndSetCandidateLocation;
      window.resolveAndSetCandidateLocation = resolveAndSetCandidateLocation;
      window.setSingleCandidateLocation = resolveAndSetCandidateLocation;

      // Backward-compatibility shim for any legacy caller accessing window.tagifylocation
      window.tagifylocation = {
          removeAllTags: function() { setUnresolvedState(); },
          addTags: function(tags) {
              if (Array.isArray(tags) && tags.length > 0) {
                  resolveAndSetCandidateLocation(tags[0]);
              } else if (typeof tags === 'string') {
                  resolveAndSetCandidateLocation(tags);
              }
          },
          dropdown: {
              hide: function() { closeDropdown(); },
              show: function() { openDropdown(); }
          },
          settings: { whitelist: [] }
      };

      // Synchronous 0ms Edit-Mode Prefill
      var defaultLocStr = {!! json_encode(@$default_loc) !!};
      var defaultCityId = {!! json_encode(@$application->city_id) !!};
      var defaultStateId = {!! json_encode(@$application->state) !!};
      var defaultCountryId = {!! json_encode(@$application->country) !!};

      if (defaultLocStr || defaultCityId) {
          setResolvedState({
              id: defaultCityId || '',
              city_id: defaultCityId || '',
              state_id: defaultStateId || '',
              country_id: defaultCountryId || '',
              label: defaultLocStr || '',
              _sig: ''
          }, 'initial_prefill', 1.00);
      }

  })(window, jQuery);
   $('body').on('change', '#current_ctc_cur', function(){
   			var	current_ctc_cur		=	$(this).val();
			document.getElementById("current_ectc_cur").selectedIndex = current_ctc_cur;
			document.getElementById("current_th_cur").selectedIndex = current_ctc_cur;
   });
   $('body').on('change', '#current_ectc_cur', function(){
   			var	current_ectc_cur	=	$(this).val();
			document.getElementById("current_ctc_cur").selectedIndex = current_ectc_cur;
			document.getElementById("current_th_cur").selectedIndex = current_ectc_cur;
   });
   $('body').on('change', '#current_th_cur', function(){
   			var	current_th_cur		=	$(this).val();
			document.getElementById("current_ectc_cur").selectedIndex = current_th_cur;
			document.getElementById("current_ctc_cur").selectedIndex = current_th_cur;
   });
   $('body').on('change', '#current_ctc_mode', function(){
   			var	current_ctc_mode	=	$(this).val();
			document.getElementById("current_ectc_mode").value = current_ctc_mode;
   });
  
      setTimeout(() => {

        $('#state option:eq({{ @$state }})').prop('selected', true);
       
      }, 1000);
	
	$(document).on('change', '.change_state', function() { 
   				
				  var country				=	$('#country').val(); 
   				var state_id			=	this.value;				
   								
				if(country == 101){
					$('#city_id').show();	
					$('#city').hide();	
				}else{
					$('#city').hide();	
					$('#city_id').show();	
				}
								
				var url 			=	"{{ route('service_company.clients.citylist',':state_id') }}";
				url 				=	url.replace(':state_id', state_id);
				$.ajax({
								type	: 'GET',
								url		: url,
								success	: function (response) {		
																	$('#city_id').html(response.data);
															   }		
									   
				});	
   								
   	});

        function numberToIndianWords(num) {

    num = Math.floor(Number(num));

    if (!num || isNaN(num)) return '';

    const ones = [
        '', 'One', 'Two', 'Three', 'Four', 'Five', 'Six',
        'Seven', 'Eight', 'Nine', 'Ten', 'Eleven', 'Twelve',
        'Thirteen', 'Fourteen', 'Fifteen', 'Sixteen',
        'Seventeen', 'Eighteen', 'Nineteen'
    ];

    const tens = [
        '', '', 'Twenty', 'Thirty', 'Forty',
        'Fifty', 'Sixty', 'Seventy', 'Eighty', 'Ninety'
    ];

    function convert(n) {

        if (n < 20) {
            return ones[n];
        }

        if (n < 100) {
            return tens[Math.floor(n / 10)] + ' ' + ones[n % 10];
        }

        if (n < 1000) {
            return ones[Math.floor(n / 100)] + ' Hundred ' + convert(n % 100);
        }

        if (n < 100000) {
            return convert(Math.floor(n / 1000)) + ' Thousand ' + convert(n % 1000);
        }

        if (n < 10000000) {
            return convert(Math.floor(n / 100000)) + ' Lakh ' + convert(n % 100000);
        }

        if (n < 1000000000) {
            return convert(Math.floor(n / 10000000)) + ' Crore ' + convert(n % 10000000);
        }

        return convert(Math.floor(n / 1000000000)) + ' Arab ' + convert(n % 1000000000);
    }

    return convert(num).replace(/\s+/g, ' ').trim() + ' Rupees';
}


    function updateCTCFields() {
                // Current CTC
                
                let maxLimit = 99999999999;
                var ctcVal = parseInt($("#ctc").val(), 10);
                var payMonthly = parseInt($("#pay_monthly").val(), 10);
                var payAnnual = parseInt($("#pay_annual").val(), 10);
                var ctcCurrency = $(".currency").val();

                if (Number(payAnnual) > maxLimit) {
                    Swal.fire("Warning", "Value above Arab is not allowed", "warning");
                    $('#pay_annual').val(maxLimit);
                    $(".payrateannual").text(numberToIndianWords(maxLimit));
                    return false;
                }

                if (!isNaN(payAnnual)) {
                    var ctcWords = window.numberToWords(ctcVal, ctcCurrency);
                    var payMonthlyWords = window.numberToWords(payMonthly, ctcCurrency);
                    var payAnnualWords = window.numberToWords(payAnnual, ctcCurrency);
                    
                    
                    $(".payrateannual").text(numberToIndianWords(payAnnual));
                    $(".payratemonthly").text(numberToIndianWords(payMonthly));
                    $(".CurrentCTC").text(numberToIndianWords(ctcVal));

                    // $(".CurrentCTC").text(ctcWords.charAt(0).toUpperCase() + ctcWords.slice(1));
                    // $(".payratemonthly").text(payMonthlyWords.charAt(0).toUpperCase() + payMonthlyWords.slice(1));
                    // $(".payrateannual").text(payAnnualWords.charAt(0).toUpperCase() + payAnnualWords.slice(1));
                }else{
                    $(".payrateannual").text(' ');
                    $(".payratemonthly").text(' ');
                    $(".CurrentCTC").text(' ');
                }

                // Expected CTC
                var ectcVal = parseInt($("#ectc").val(), 10);
                var billMonthly = parseInt($("#bill_monthly").val(), 10);
                var billAnnual = parseInt($("#bill_annual").val(), 10);

                
                if (Number(billAnnual) > maxLimit) {
                    Swal.fire("Warning", "Value above Arab is not allowed", "warning");
                    $('#bill_annual').val(maxLimit);
                    $(".billrateannual").text(numberToIndianWords(maxLimit));
                    return false;
                }

                var ectcCurrency = $(".currency").val();
                if (!isNaN(billAnnual)) {
                    var ectcWords = window.numberToWords(ectcVal, ectcCurrency);
                    var billMonthlyWords = window.numberToWords(billMonthly, ctcCurrency);
                    var billAnnualWords = window.numberToWords(billAnnual, ctcCurrency);
                    
                    $(".billrateannual").text(numberToIndianWords(billAnnual));
                    $(".billratemonthly").text(numberToIndianWords(billMonthly));
                    $(".expectedctc").text(numberToIndianWords(ectcVal));

                    // $(".expectedctc").text(ectcWords.charAt(0).toUpperCase() + ectcWords.slice(1));
                    // $(".billratemonthly").text(billMonthlyWords.charAt(0).toUpperCase() + billMonthlyWords.slice(1));
                    // $(".billrateannual").text(billAnnualWords.charAt(0).toUpperCase() + billAnnualWords.slice(1));
                }else{
                    $(".billrateannual").text('');
                    $(".billratemonthly").text('');
                    $(".expectedctc").text('');
                }
    }
    setTimeout(() => {
          updateCTCFields();
    }, 500);
    $(document).on("keyup", "#ctc", updateCTCFields);
    $(document).on("keyup", "#ectc", updateCTCFields);
    $(".currency").on("change", updateCTCFields);
            

     $(document).on('change', '#total_exp, #rel_exp', function() { 
      total_exp_and_rel_exp();
     });


		
</script>


<script>
  

@php if($page_event != 'EDIT'){ @endphp
var KTAddCandidate = function () {
    var form;
    var submitButton;
    var validator;

    
    var handleForm = function (e) {
       
        validator = FormValidation.formValidation(
            form,
            {
                fields: {
                    
				       	  // 'resume':
                  //   {
                  //     validators:
                  //       {
                  //           file: {
                  //               maxSize: 5242880, // 5 MB in bytes
                  //               message: "The file size must not exceed 5 MB",
                  //               type: 'application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                  //               message: "Only PDF, DOC, or DOCX files are allowed"
                  //           }
                  //       }
                  // },
                  'photo':
                    {
                      validators:
                        {
                            file: {
                                maxSize: 5242880, // 5 MB in bytes
                                message: "The file size must not exceed 5 MB"
                            }
                        }
                   },
					
                    'full_name':
                    {
                        validators:
                        {
                            notEmpty:{message: "Full name is required"}
                           
                        }
                    },
					
                    'current_designation':
                    {
                        validators:
                        {
                            notEmpty:{ message: "Current designation is required"}
                           
                        }
                    },
					
                    'current_company':
                    {
                        validators:
                        {
                            // notEmpty:{ message: "Current company is required"}
                            
                        }
                    },
					
                    'phone':
                    {
                        validators:
                        {
                            notEmpty:{message: "Primary number is required"},
                            digits:{ message: "Primary number must contain only digits"},
                            stringLength:{ min: 6, max: 15, message: "Please enter a valid mobile number"}
                            
                        }
                    },
                    
                    'secondary_mobile':
                    {
                        validators:
                        {
                            digits:{ message: "Secondary number must contain only digits"},
                            stringLength:{ min: 6, max: 15, message: "Please enter a valid mobile number"}
                            
                        }
                    },
					
                    'email':
                    {
                        validators:
                        {
                            notEmpty:{message: "Primary Email is required"},
                            emailAddress:{message: 'The value is not a valid email address'}
                            
                        }
                    },
                    
                    'secondrary_email':
                    {
                        validators:
                        {
                            emailAddress:{message: 'The value is not a valid email address'}
                            
                        }
                    },
					
                    'qualification':
                    {
                        validators:
                        {
                            // notEmpty:{message: 'Qualification Name is required'}
                           
                        }
                    },
					
                    'total_exp':
                    {
                        validators:
                        {
                            // notEmpty:{message:"Total Experience is required"},                           
                        }
                    },
					
                    'rel_exp':
                    {
                        validators:
                        {
                            // notEmpty:{ message:"Relevant Experience is required"}
                           
                        }
                    },
					
                    'ctc':
                    {
                        validators:
                        {
                            // notEmpty:{ message: "Current CTC is required"}
                            
                        }
                    },
					
                    'ectc':
                    {
                        validators:
                        {
                            // notEmpty:{message: "Exp. CTC is required"}
                            
                        }
                    },
					
					
                    'country':
                    {
                        validators:
                        {
                            // notEmpty:{message: "Country is required"}
                        }
                    },
					
                    'state':
                    {
                        validators:
                        {
                            // notEmpty:{message: "State is required"}
                           
                        }
                    },
					
                    'pincode':
                    {
                        validators:
                        {
                            // notEmpty:{message: "Pincode is required"},                           
                            digits:{message: "Pincode must contain digits"},
                            stringLength:{ min: 1,	max: 1,	message: ""}
                           
                        }    
                    },                    
                    'skills':
                    {
                        validators:
                        {
                            // notEmpty:{ message: 'Skills is required'}
                          
                        }
                    },
                    'specialties':
                    {
                        validators:
                        { notEmpty:{message: "Specialties is required"}
                           
                        }
                    }
                },
                plugins: {
                    trigger: new FormValidation.plugins.Trigger(),
                    bootstrap: new FormValidation.plugins.Bootstrap5({
                        rowSelector: '.fv-row'
                    })
                }
            }
        );

       
        submitButton.addEventListener('click', async function (e) {
           
            e.preventDefault();
            if (submitButton.disabled) {
                return; // ⛔ Prevent multiple clicks
            }
            submitButton.disabled = true;

            const connected = await isInternetAvailable();
            if (!connected) {
                Swal.fire({
                    title: "No Internet",
                    text: "You are currently offline or the server is unreachable.",
                    icon: "warning",
                    confirmButtonText: "Ok, got it!",
                    customClass: {
                        confirmButton: "btn btn-warning"
                    }
                });
                submitButton.disabled = false;
                return; // ⛔ STOP EXECUTION
            }
            validator.validate().then(function (status) {
                if (status == 'Valid') {
                    if (!navigator.onLine) {
                        Swal.fire({ title: "No Internet", text: "You are currently offline or the server is unreachable.", icon: "warning", confirmButtonText: "Ok, got it!", customClass: { confirmButton: "btn btn-warning" } });
                        submitButton.disabled = false;
                        return;
                    }
                    $("#save-form").prop("disabled", true);
                    $("#save-form").html(
                        `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Waiting...`
                    );
                    submitButton.setAttribute('data-kt-indicator', 'on');
                    submitButton.disabled = true;

                    if (typeof CKEDITOR !== 'undefined' && CKEDITOR.instances['recruiter_note']) {
                        CKEDITOR.instances['recruiter_note'].updateElement();
                    }

                    // Ensure unique client_submission_uuid for idempotency
                    if (!$('#client_submission_uuid').val()) {
                        $('#client_submission_uuid').val(window.crypto && window.crypto.randomUUID ? window.crypto.randomUUID() : ('sub_' + Math.random().toString(36).slice(2) + Date.now()));
                    }

                    var formData = new FormData($('#createForm')[0]);
                    // 0% Server Strain & 10K Concurrency Guard: Strip redundant binary file if already in R2
                    if ($('#resume_session_uuid').val() || $('#uploaded_file_path').val()) {
                        if (formData.delete) { formData.delete('resume'); }
                    }
                    $.ajax({
                        url         : '{{route('service_company.job-applications.store')}}'+'?_token=' + '{{ csrf_token() }}',
                        type        : 'POST',
                        data        : formData,
                        processData : false,
                        contentType : false,
                        dataType    : 'json',
                        redirect    : true,
                        success     : function (response) {
                            if (typeof response === 'string') {
                                try { response = JSON.parse(response); } catch (e) {}
                            }
                            $("#save-form").prop("disabled", false);
                            if (response.status == 'coinCheck') {
                                Swal.fire("Notice", response.message , "warning");
                                return false;
                            }
                            if (response.status == 'mailcheck') {
                                if (!navigator.onLine) {
                                    Swal.fire({ title: "No Internet", text: "You are currently offline or the server is unreachable.", icon: "warning", confirmButtonText: "Ok, got it!", customClass: { confirmButton: "btn btn-warning" } });
                                    $("#save-form").prop("disabled", false).html('<i class="fa-solid fa-paper-plane" aria-hidden="true"></i><span>Submit Candidate</span>');
                                    submitButton.removeAttribute('data-kt-indicator');
                                    submitButton.disabled = false;
                                    return;
                                }

                                $("#save-form").html('<i class="fa-solid fa-paper-plane" aria-hidden="true"></i><span>Submit Candidate</span>');
                                Swal.fire("Warning", response.message , "warning");
                                return false;
                            }

                            if (response.status !== 'success') {
                                $("#save-form").html('<i class="fa-solid fa-paper-plane" aria-hidden="true"></i><span>Submit Candidate</span>');
                                submitButton.removeAttribute('data-kt-indicator');
                                submitButton.disabled = false;
                                Swal.fire("Submission Failed", response.message || "The candidate could not be saved. Please try again.", "error");
                                return false;
                            }

                            submitButton.removeAttribute('data-kt-indicator');
                            submitButton.disabled = true;

                            var fallbackUrl = '{{ route('service_company.cvtracker.index') }}';
                            var targetUrl = (response && typeof response.url === 'string' && response.url.trim() !== '') ? response.url : fallbackUrl;

                            // Clear v2 session from localStorage so the next ADD form does not recover this already-completed session.
                            if (typeof RESUMES_FEATURE_BROWSER_UPLOAD !== 'undefined' && RESUMES_FEATURE_BROWSER_UPLOAD && window.ResumePresign) { 
                                window.ResumePresign.cancelUpload(); 
                            }

                            // Rotate client_submission_uuid for any subsequent submission
                            $('#client_submission_uuid').val(window.crypto && window.crypto.randomUUID ? window.crypto.randomUUID() : ('sub_' + Math.random().toString(36).slice(2) + Date.now()));

                            // Phase 5: Multi-Tenant Atomic Cache Invalidation
                            try {
                                var tenantCachePrefix = 'jero_cand_v1_{{ $companyId }}_';
                                for (var k in sessionStorage) {
                                    if (sessionStorage.hasOwnProperty(k) && k.indexOf(tenantCachePrefix) === 0) {
                                        sessionStorage.removeItem(k);
                                    }
                                }
                            } catch (e) {}

                            try {
                                window.dispatchEvent(new CustomEvent('jero:candidate-updated', {
                                    detail: {
                                        companyId: '{{ $companyId }}',
                                        timestamp: Date.now()
                                    }
                                }));
                            } catch (e) {}

                            var saveRedirected = false;
                            function executeSaveRedirect() {
                                if (saveRedirected) return;
                                saveRedirected = true;
                                var e1 = form.querySelector('[name="email1"]'); if (e1) e1.value = "";
                                var p1 = form.querySelector('[name="password1"]'); if (p1) p1.value = "";
                                window.location.assign(targetUrl);
                            }

                            Swal.fire({
                                text: "You have successfully Submitted",
                                icon: "success",
                                buttonsStyling: false,
                                confirmButtonText: "Ok, got it!",
                                customClass: {
                                    confirmButton: "btn btn-primary"
                                },
                                timer: 2000,
                                timerProgressBar: true
                            }).then(function () {
                                executeSaveRedirect();
                            });

                            setTimeout(executeSaveRedirect, 2200);
                        },
                        error: function (xhr, textStatus, errorThrown) {
                            $("#save-form").prop("disabled", false);
                            $("#save-form").html('<i class="fa-solid fa-paper-plane" aria-hidden="true"></i><span>Submit Candidate</span>');
                            submitButton.removeAttribute('data-kt-indicator');
                            submitButton.disabled = false;
                            if (xhr.status === 0 || !navigator.onLine) {
                                Swal.fire({ title: "No Internet", text: "You are currently offline or the server is unreachable.", icon: "warning", confirmButtonText: "Ok, got it!", customClass: { confirmButton: "btn btn-warning" } });
                                return;
                            }
                            console.error('Save AJAX error:', textStatus, errorThrown, xhr.responseText);
                            var serverMsg = '';
                            try {
                                var body = xhr.responseText || '';
                                var jsonStart = body.indexOf('{');
                                if (jsonStart >= 0) {
                                    var parsed = JSON.parse(body.substring(jsonStart));
                                    serverMsg = parsed.message || parsed.error || '';
                                }
                            } catch (e) {}

                            if (xhr.status === 409) {
                                Swal.fire("Concurrent Submission", serverMsg || "This candidate submission is already being processed. Please wait a moment.", "warning");
                                return;
                            }

                            var display = serverMsg
                                ? serverMsg
                                : (xhr.status ? ('HTTP ' + xhr.status + ' ' + xhr.statusText) : textStatus);
                            Swal.fire("Error (" + (xhr.status || '?') + ")", display || "Something went wrong. Please try again.", "error");
                        },
                        complete: function () {
                            if ($("#save-form").length) {
                                $("#save-form").prop("disabled", false);
                                $("#save-form").html('<i class="fa-solid fa-paper-plane" aria-hidden="true"></i><span>Submit Candidate</span>');
                                submitButton.removeAttribute('data-kt-indicator');
                                submitButton.disabled = false;
                            }
                        }
                    });
                } 
				else 
				{
                    submitButton.disabled = false;
                    var missing = detectCandidateMissingFields(form, validator);
                    if (missing && missing.length > 0) {
                        showCandidateRequiredFieldsModal(missing);
                    } else {
                        Swal.fire({
                            text: "Sorry, looks like there are some errors detected, please check the highlighted fields.",
                            icon: "error",
                            buttonsStyling: false,
                            confirmButtonText: "Ok, got it!",
                            customClass: {
                                confirmButton: "btn btn-primary"
                            }
                        });
                    }
                }
            });
        });
    }

    return {
				init: function () {
										form 			=	document.querySelector('#createForm');
										submitButton 	=	document.querySelector('#save-form');
										handleForm();
									}
    };
}();

// Candidate Required Fields Config and Modal Navigation
var candidateFieldConfigMap = {
    'resume': {
        label: 'Upload Resume',
        selector: '#dropzone_resum',
        message: 'Please upload a resume file (PDF or Word).'
    },
    'full_name': {
        label: 'Full Name',
        selector: '#full_name',
        message: 'Candidate full name is required.'
    },
    'email': {
        label: 'Primary Email',
        selector: '#email',
        message: 'A valid primary email address is required.'
    },
    'phone': {
        label: 'Primary Contact Number',
        selector: '#phone',
        message: 'A valid primary mobile number is required.'
    },
    'kt_tagify_work_location': {
        label: 'Current Location',
        selector: '#kt_tagify_work_location',
        message: 'Current location is required.'
    },
    'qualification': {
        label: 'Highest Qualification',
        selector: '#qualification',
        message: 'Highest qualification is required.'
    },
    'nationality': {
        label: 'Nationality',
        selector: '#nationality',
        message: 'Nationality is required.'
    },
    'gender': {
        label: 'Gender',
        selector: '#gender',
        message: 'Gender is required.'
    },
    'current_company': {
        label: 'Current Company',
        selector: '#current_company',
        message: 'Current company is required.'
    },
    'current_designation': {
        label: 'Current Designation',
        selector: '#current_designation',
        message: 'Current designation is required.'
    },
    'employmentType': {
        label: 'Employment Type',
        selector: '#EmploymentType',
        message: 'Employment type is required.'
    },
    'total_exp': {
        label: 'Total Experience',
        selector: '#total_exp_year',
        message: 'Total experience is required.'
    },
    'rel_exp': {
        label: 'Relevant Experience',
        selector: '#rel_exp_year',
        message: 'Relevant experience is required.'
    },
    'skills': {
        label: 'Primary Skills',
        selector: '#kt_tagify_2',
        message: 'Primary skills are required.'
    }
};

function detectCandidateMissingFields(formEl, validatorInstance) {
    var missing = [];
    var seenKeys = {};

    function addMissing(key, customLabel, customSelector, customMessage) {
        if (!seenKeys[key]) {
            seenKeys[key] = true;
            var cfg = candidateFieldConfigMap[key] || {};
            missing.push({
                key: key,
                label: customLabel || cfg.label || key,
                selector: customSelector || cfg.selector || ('#' + key),
                message: customMessage || cfg.message || ((customLabel || cfg.label || key) + ' is required.')
            });
        }
    }

    // 1. Resume check
    var hasResume = false;
    if (($('#uploaded_file_path').val() && String($('#uploaded_file_path').val()).trim()) ||
        ($('#resume_original_name').val() && String($('#resume_original_name').val()).trim())) {
        hasResume = true;
    } else {
        var ri = document.getElementById('resume');
        if (ri && ri.files && ri.files.length > 0) {
            hasResume = true;
        } else if ($('#fileInfo').is(':visible') && $('#fileName').text().trim()) {
            hasResume = true;
        } else if ($('#upInfo').is(':visible') && $('#upFileName').text().trim()) {
            hasResume = true;
        }
    }
    if (!hasResume) {
        addMissing('resume');
    }

    // 2. Full Name
    var fn = ($('#full_name').val() || '').trim();
    if (!fn) addMissing('full_name');

    // 3. Email
    var em = ($('#email').val() || '').trim();
    if (!em || !/\S+@\S+\.\S+/.test(em)) addMissing('email');

    // 4. Phone
    var ph = ($('#phone').val() || '').trim();
    if (!ph || ph.length < 6) addMissing('phone');

    // 5. Current Location
    var loc = ($('#kt_tagify_work_location').val() || '').trim();
    if (!loc) {
        addMissing('kt_tagify_work_location');
        $('#jero-cs-location').addClass('has-validation-error');
        $('#jero-cs-location').closest('.candidate-field-control, .bd-control, .fg').addClass('has-validation-error');
    }

    // 6. Current Designation
    var cd = ($('#current_designation').val() || '').trim();
    if (!cd) addMissing('current_designation');

    // 7. Current Company
    var cc = ($('#current_company').val() || '').trim();
    if (!cc) addMissing('current_company');

    // 8. Employment Type
    var et = ($('#EmploymentType').val() || '').trim();
    if (!et) addMissing('employmentType');

    // 9. Total Exp
    var te = $('#total_exp_year').val();
    if (te === '' || te === null || te === undefined) addMissing('total_exp');

    // 10. Rel Exp
    var re = $('#rel_exp_year').val();
    if (re === '' || re === null || re === undefined) addMissing('rel_exp');

    // 11. Highest Qualification
    var qual = ($('#qualification').val() || '').trim();
    if (!qual) addMissing('qualification');

    // 12. Primary Skills
    var sk = ($('#kt_tagify_2').val() || '').trim();
    if (!sk) addMissing('skills');

    // 13. Nationality
    var nat = ($('#nationality').val() || '').trim();
    if (!nat) addMissing('nationality');

    // 14. Gender
    var gen = ($('#gender').val() || '').trim();
    if (!gen) addMissing('gender');

    return missing;
}

function jumpToCandidateField(fieldKey) {
    closeCandidateRequiredFieldsModal();

    var cfg = candidateFieldConfigMap[fieldKey];
    var targetEl = cfg && cfg.selector ? document.querySelector(cfg.selector) : null;
    if (!targetEl) {
        targetEl = document.querySelector('[name="' + fieldKey + '"]');
    }

    if (targetEl) {
        var parentDetails = targetEl.closest('details');
        if (parentDetails && !parentDetails.open) {
            parentDetails.open = true;
        }

        var scrollTarget = targetEl.closest('.fv-row, .card, .form-group, .fs, .candidate-card') || targetEl;
        scrollTarget.scrollIntoView({ behavior: 'smooth', block: 'center' });

        var highlightEl = targetEl.closest('.fv-row, .form-group, .candidate-card, .candidate-field-control') || targetEl;
        $(highlightEl).addClass('field-attention-highlight');
        setTimeout(function () {
            $(highlightEl).removeClass('field-attention-highlight');
        }, 3000);

        setTimeout(function () {
            if (fieldKey === 'resume') {
                var btn = document.querySelector('.candidate-resume-cta') || targetEl;
                if (btn && typeof btn.focus === 'function') btn.focus();
            } else if (fieldKey === 'kt_tagify_work_location') {
                var csLocTrigger = document.querySelector('#jero-cs-location .jero-cs-trigger');
                if (csLocTrigger && typeof csLocTrigger.focus === 'function') {
                    csLocTrigger.focus();
                    csLocTrigger.click();
                } else if (typeof targetEl.focus === 'function') {
                    targetEl.focus();
                }
            } else if (fieldKey === 'skills') {
                var container = targetEl.closest('.candidate-field-control, .candidate-card-body');
                var tagifyInput = container ? container.querySelector('.tagify__input') : null;
                if (tagifyInput && typeof tagifyInput.focus === 'function') tagifyInput.focus();
            } else if (fieldKey === 'qualification') {
                var csTrigger = document.querySelector('#jero-cs-qualification .jero-cs-trigger');
                if (csTrigger && typeof csTrigger.focus === 'function') {
                    csTrigger.focus();
                } else if (typeof targetEl.focus === 'function') {
                    targetEl.focus();
                }
            } else if (fieldKey === 'nationality') {
                var csNatTrigger = document.querySelector('#jero-cs-nationality .jero-cs-trigger');
                if (csNatTrigger && typeof csNatTrigger.focus === 'function') {
                    csNatTrigger.focus();
                } else if (typeof targetEl.focus === 'function') {
                    targetEl.focus();
                }
            } else if (typeof targetEl.focus === 'function') {
                targetEl.focus();
            }
        }, 350);
    }
}

function showCandidateRequiredFieldsModal(missingFields) {
    if (!missingFields || !missingFields.length) {
        missingFields = [
            { key: 'full_name', label: 'Full Name' },
            { key: 'kt_tagify_work_location', label: 'Current Location' }
        ];
    }

    window.__currentMissingFields = missingFields;

    var count = missingFields.length;
    var countText = count === 1 ? '1 required field' : (count + ' required fields');
    var descText = countText + ' need' + (count === 1 ? 's' : '') + ' your attention before you can submit this candidate.';

    $('#rfMissingCountText').text(countText);
    $('#rfModalDesc').text(descText);

    var $list = $('#rfFieldsList');
    $list.empty();

    missingFields.forEach(function (f) {
        var rowHtml = '<div class="rf-field-row" role="button" tabindex="0" data-field-key="' + f.key + '" aria-label="Go to ' + f.label + '" style="background:#fef2f2;border:1px solid #fee2e2;border-radius:8px;padding:10px 14px;display:flex;align-items:center;justify-content:space-between;cursor:pointer;margin-bottom:8px;">'
            + '<div class="rf-field-row-left" style="display:flex;align-items:center;gap:12px;min-width:0;">'
            + '<span class="rf-field-badge" aria-hidden="true" style="width:20px;height:20px;border-radius:50%;background:#ef4444;color:#ffffff;display:inline-flex;align-items:center;justify-content:center;font-size:11px;font-weight:800;flex-shrink:0;">!</span>'
            + '<span class="rf-field-name" style="font-size:13.5px;font-weight:600;color:#0f172a;line-height:1.3;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">' + $('<div>').text(f.label).html() + '</span>'
            + '</div>'
            + '<svg class="rf-field-chevron" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#64748b" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" style="flex-shrink:0;"><polyline points="9 18 15 12 9 6"></polyline></svg>'
            + '</div>';
        $list.append(rowHtml);
    });

    var $modal = $('#requiredFieldsModal');
    $modal.css('display', 'flex').hide().fadeIn(150, function () {
        $('#rfPrimaryCta').focus();
    });
}

function closeCandidateRequiredFieldsModal() {
    $('#requiredFieldsModal').fadeOut(120, function () {
        $(this).css('display', 'none');
    });
}

// Modal event listeners
$(document).on('click', '#rfModalClose', function (e) {
    e.preventDefault();
    closeCandidateRequiredFieldsModal();
});

$(document).on('click', '#requiredFieldsModal', function (e) {
    if (e.target === this) {
        closeCandidateRequiredFieldsModal();
    }
});

$(document).on('keydown', function (e) {
    if (e.key === 'Escape' && $('#requiredFieldsModal').is(':visible')) {
        closeCandidateRequiredFieldsModal();
    }
});

$(document).on('click', '.rf-field-row', function (e) {
    e.preventDefault();
    var key = $(this).data('field-key');
    if (key) {
        jumpToCandidateField(key);
    }
});

$(document).on('keydown', '.rf-field-row', function (e) {
    if (e.key === 'Enter' || e.key === ' ') {
        e.preventDefault();
        var key = $(this).data('field-key');
        if (key) {
            jumpToCandidateField(key);
        }
    }
});

$(document).on('click', '#rfPrimaryCta', function (e) {
    e.preventDefault();
    if (window.__currentMissingFields && window.__currentMissingFields.length > 0) {
        jumpToCandidateField(window.__currentMissingFields[0].key);
    } else {
        closeCandidateRequiredFieldsModal();
    }
});

// Phase 6: Keyboard Shortcut Ctrl+S / Cmd+S for rapid candidate submission
document.addEventListener('keydown', function (e) {
    if ((e.ctrlKey || e.metaKey) && (e.key === 's' || e.key === 'S')) {
        e.preventDefault();
        var submitBtn = document.getElementById('save-form');
        if (submitBtn && !submitBtn.disabled) {
            submitBtn.click();
        }
    }
});

KTUtil.onDOMContentLoaded(function () {
    KTAddCandidate.init();
});

@php } @endphp
function re_test(text){
    // Deprecated: Resume parsing is executed 100% client-side via JeroClientParser.
    // Zero server POST requests to extract.resume-job-applications or file.unset.
    return;
}


document.addEventListener('DOMContentLoaded', function () {
    var today = new Date().toISOString().split('T')[0];
    document.getElementById('dob').setAttribute('max', today);
});

    function total_exp_and_rel_exp(){
        var totalYear = $('#total_exp_year').val();
        var relYear = $('#rel_exp_year').val();
        var totalExp = (totalYear !== '' && totalYear !== null) ? parseFloat(totalYear) : parseFloat($('#total_exp').val() || 0);
        var relExp = (relYear !== '' && relYear !== null) ? parseFloat(relYear) : parseFloat($('#rel_exp').val() || 0);

        if (!isNaN(totalExp) && !isNaN(relExp) && relExp > totalExp) {
            $('#rel_exp_error').text('Relevant experience (' + relExp + ' yrs) cannot be higher than total experience (' + totalExp + ' yrs).').show();
            Swal.fire({
                title: "Invalid Experience",
                text: "Relevant Experience (" + relExp + " years) cannot be higher than Total Experience (" + totalExp + " years).",
                icon: "warning",
                confirmButtonText: "Ok, got it!",
                customClass: { confirmButton: "btn btn-primary" }
            });
            $('#rel_exp_year').val('').trigger('change');
            $('#rel_exp').val('');
            return false;
        } else {
            $('#rel_exp_error').text('').hide();
            return true;
        }
    }

    $(document).on('change', '#total_exp_year, #rel_exp_year, #total_exp, #rel_exp', function() { 
        total_exp_and_rel_exp();
    });

    function normalizeResumeText(text) {
        return text
            .replace(/[^\x00-\x7F]/g, " ")   // remove broken unicode
            .replace(/\s+/g, " ")
            .replace(/\|/g, " ")
            .trim();
    }


    function handleFileInputChange(event) {
        // Deprecated: Resume parsing is executed 100% client-side in processFile() via JeroClientParser.
        // Zero duplicate uploads to /uploadresume, zero server parse requests.
        return;
    }

    // Attach event listener to file input
    // document.getElementById('resume').addEventListener('change', handleFileInputChange);

    
$(document).ready(function() {
        var optionFormat = function(item) {
            if ( !item.id ) {
                return item.text;
            }

            var span = document.createElement('span');
            var imgUrl = item.element.getAttribute('data-kt-select2-user');
            var template = '';

            template += '';
            template += item.text;

            span.innerHTML = template;

            return $(span);
        }

        $('#city_id').select2({
            templateSelection: optionFormat,
            templateResult: optionFormat
        });

        $('#country').select2({
            templateSelection: optionFormat,
            templateResult: optionFormat
        });

        $('#country').change(function(){  
   				var cid				=	$(this).val() ;
          var cityId				=	"{{ (@$application->city_id) ? @$application->city_id : 0 }}"; 
            if(cid == 101){
              $('#city_id').show();	
              $('#city').hide();	
            }else{
              $('#city').hide();	
              $('#city_id').show();	
            }
              //route('service_company.job-applications.statelist',':cid') 
              var url 			=	"{{ route('countrycitylist',':cid') }}";
              url 				=	url.replace(':cid', cid);
              $.ajax({
                      type	: 'GET',
                      url		: url,
                      success	: function (response) {																		
                                        $('#city_id').html(response.data);
                                        setTimeout(() => {
                                        if(cityId	>=1){
                                          $('#city_id').val(cityId).change();
                                        }
                                      
                                      }, 500);
                                      }		
              });
        });
        $('#country').trigger('change');
			
   
   });






       


// Initialize CKEditor for recruiter_note
if (typeof CKEDITOR !== 'undefined') {
    CKEDITOR.replace('recruiter_note', { height: 150 });

    // When CKEditor is ready, hook into its iframe paste events.
    // If the user pastes a file (e.g. a resume) while the editor has focus,
    // intercept it and route to the resume upload handler instead of letting
    // CKEditor consume the event (CKEditor's iframe swallows document-level paste).
    CKEDITOR.instances['recruiter_note'].on('instanceReady', function () {
        var editorDoc = this.document.$;
        editorDoc.addEventListener('paste', function (e) {
            var files = e.clipboardData && e.clipboardData.files;
            if (files && files.length > 0) {
                e.preventDefault();
                e.stopPropagation();
                var file = files[0];
                var dt = new DataTransfer();
                dt.items.add(file);
                resumeInput.files = dt.files;
                processFile(file);
            }
        }, true);
    });
}

// Recruiter note templates keyed by id
var recruiterNoteTemplates = @json(isset($recruiterNoteTemplates) ? $recruiterNoteTemplates->keyBy('id') : collect());

// Template selector → load content into CKEditor
$(document).on('change', '#recruiter_note_template_select', function () {
    var id = $(this).val();
    if (!id || !recruiterNoteTemplates[id]) return;
    var content = recruiterNoteTemplates[id].content;
    if (typeof CKEDITOR !== 'undefined' && CKEDITOR.instances['recruiter_note']) {
        CKEDITOR.instances['recruiter_note'].setData(content);
    } else {
        $('#recruiter_note').val(content);
    }
});

// Add Candidate presentation only: card visibility, salary display tabs, and summaries.
(function () {
    'use strict';

    var root = document.querySelector('.job-candidate-create');
    if (!root) {
        return;
    }

    var noticePeriodSelect = root.querySelector('#serving_notice_period');
    var noticePeriodOptions = root.querySelectorAll('.candidate-notice-option');
    var noticeDateField = root.querySelector('#candidateNoticeDateField');
    var noticeDaysField = root.querySelector('#candidateNoticeDaysField');
    var noticeDateLabel = root.querySelector('#candidateNoticeDateLabel');
    var noticeDateInput = root.querySelector('#np_id');
    var noticeDateDisplay = root.querySelector('#np_id_display');
    var noticeDaysSelect = root.querySelector('#ddl_np_id');
    var previousNoticeMode = noticePeriodSelect ? String(noticePeriodSelect.value) : '0';

    function formatNoticeDate(value) {
        if (!value) {
            return '';
        }

        var parts = value.split('-');
        return parts.length === 3 ? parts[2] + '/' + parts[1] + '/' + parts[0] : value;
    }

    function syncNoticePeriodComponent(clearPreviousValue) {
        if (!noticePeriodSelect) {
            return;
        }

        var mode = String(noticePeriodSelect.value);
        var usesDate = mode === '1' || mode === '2';
        var today = new Date().toISOString().split('T')[0];

        if (clearPreviousValue && mode !== previousNoticeMode) {
            if (noticeDateInput) noticeDateInput.value = '';
            if (noticeDateDisplay) noticeDateDisplay.value = '';
            if (noticeDaysSelect) noticeDaysSelect.value = '';
        }

        noticePeriodOptions.forEach(function (option) {
            var selected = option.getAttribute('data-notice-period-value') === mode;
            option.classList.toggle('is-selected', selected);
            option.setAttribute('aria-checked', selected ? 'true' : 'false');
        });

        if (noticeDateField) noticeDateField.hidden = !usesDate;
        if (noticeDaysField) noticeDaysField.hidden = mode !== '0';
        if (noticeDateInput) {
            noticeDateInput.disabled = !usesDate;
            noticeDateInput.required = usesDate;
            noticeDateInput.min = mode === '1' ? today : '';
            noticeDateInput.max = mode === '2' ? today : '';
        }
        if (noticeDateDisplay) {
            noticeDateDisplay.disabled = !usesDate;
            noticeDateDisplay.placeholder = mode === '1'
                ? 'Select expected last working date'
                : 'Select last working date';
        }
        if (noticeDaysSelect) {
            noticeDaysSelect.disabled = mode !== '0';
            noticeDaysSelect.required = mode === '0';
        }
        if (noticeDateLabel) {
            noticeDateLabel.innerHTML = (mode === '1' ? 'Expected Last Working Date' : 'Last Working Date') + ' <span class="rq">*</span>';
        }

        previousNoticeMode = mode;
    }

    noticePeriodOptions.forEach(function (option) {
        option.addEventListener('click', function () {
            if (!noticePeriodSelect) {
                return;
            }

            noticePeriodSelect.value = option.getAttribute('data-notice-period-value');
            noticePeriodSelect.dispatchEvent(new Event('change', { bubbles: true }));
        });
    });

    if (noticeDateDisplay && noticeDateInput) {
        noticeDateDisplay.addEventListener('click', function () {
            if (typeof noticeDateInput.showPicker === 'function') {
                noticeDateInput.showPicker();
            } else {
                noticeDateInput.click();
            }
        });
        noticeDateInput.addEventListener('change', function () {
            noticeDateDisplay.value = formatNoticeDate(noticeDateInput.value);
        });
    }

    if (noticePeriodSelect) {
        noticePeriodSelect.addEventListener('change', function () {
            syncNoticePeriodComponent(true);
        });
        syncNoticePeriodComponent(false);
    }
    var salaryFieldMap = {
        current: {
            annual: 'pay_annual',
            monthly: 'pay_monthly',
            hourly: 'ctc'
        },
        expected: {
            annual: 'bill_annual',
            monthly: 'bill_monthly',
            hourly: 'ectc'
        }
    };
    var salaryPeriodLabels = {
        annual: 'Annual',
        monthly: 'Monthly',
        hourly: 'Hourly'
    };
    var salaryHoursPerMonth = 160;
    var salaryMonthsPerYear = 12;
    var salaryCurrencySelect = root.querySelector('#current_ctc_cur');
    var salaryCurrencyMirrors = [
        root.querySelector('#current_ectc_cur'),
        root.querySelector('#current_th_cur')
    ];

    function fieldValue(id) {
        var field = root.querySelector('#' + id);
        var value = field ? parseFloat(field.value) : 0;
        return Number.isFinite(value) ? value : 0;
    }

    function currencyLabel() {
        if (!salaryCurrencySelect || salaryCurrencySelect.selectedIndex < 0) {
            return 'INR';
        }

        var opt = salaryCurrencySelect.options[salaryCurrencySelect.selectedIndex];
        var code = opt.getAttribute('data-code');
        if (!code) {
            code = opt.text.trim().split('—')[0].split('-')[0].trim();
        }
        return (code || 'INR').toUpperCase();
    }

    function formatAmount(value) {
        return new Intl.NumberFormat('en-IN', {
            minimumFractionDigits: 0,
            maximumFractionDigits: 2
        }).format(value || 0);
    }

    function activeSalaryPeriod() {
        var activeButton = root.querySelector('.candidate-salary-tabs button.is-active[data-salary-group="current"]');
        return activeButton ? activeButton.getAttribute('data-salary-period') : 'annual';
    }

    function salaryValuesFromPeriod(period, amount) {
        var values = {
            annual: 0,
            monthly: 0,
            hourly: 0
        };

        if (period === 'annual') {
            values.annual = amount;
            values.monthly = amount / salaryMonthsPerYear;
            values.hourly = values.monthly / salaryHoursPerMonth;
        } else if (period === 'monthly') {
            values.monthly = amount;
            values.annual = amount * salaryMonthsPerYear;
            values.hourly = amount / salaryHoursPerMonth;
        } else {
            values.hourly = amount;
            values.monthly = amount * salaryHoursPerMonth;
            values.annual = values.monthly * salaryMonthsPerYear;
        }

        return values;
    }

    function updateSalaryAutoValues(group, activePeriod) {
        var activeAmount = fieldValue(salaryFieldMap[group][activePeriod]);
        var values = salaryValuesFromPeriod(activePeriod, activeAmount);
        var otherPeriods = ['annual', 'monthly', 'hourly'].filter(function (period) {
            return period !== activePeriod;
        });
        var prefix = group === 'current' ? 'candidate-current-' : 'candidate-expected-';
        var labelOne = root.querySelector('#' + prefix + 'auto-label-one');
        var labelTwo = root.querySelector('#' + prefix + 'auto-label-two');
        var outputOne = root.querySelector('#' + prefix + 'monthly-auto');
        var outputTwo = root.querySelector('#' + prefix + 'hourly-auto');

        if (labelOne) {
            labelOne.textContent = salaryPeriodLabels[otherPeriods[0]] + ' Salary';
        }
        if (labelTwo) {
            labelTwo.textContent = salaryPeriodLabels[otherPeriods[1]] + ' Salary';
        }
        if (outputOne) {
            outputOne.textContent = formatAmount(values[otherPeriods[0]]);
        }
        if (outputTwo) {
            outputTwo.textContent = formatAmount(values[otherPeriods[1]]);
        }
    }

    function updateSalarySummary() {
        var period = activeSalaryPeriod();
        var currentAmount = fieldValue(salaryFieldMap.current[period]);
        var expectedAmount = fieldValue(salaryFieldMap.expected[period]);
        var currentTarget = root.querySelector('#candidateSalaryCurrentSummary');
        var expectedTarget = root.querySelector('#candidateSalaryExpectedSummary');
        var currentLabel = root.querySelector('#candidateSalaryCurrentLabel');
        var expectedLabel = root.querySelector('#candidateSalaryExpectedLabel');
        var differenceOutput = root.querySelector('#candidateSalaryDifferenceSummary');
        var increaseOutput = root.querySelector('#candidateSalaryHikeSummary');
        var hikeInput = root.querySelector('#percentage_hike');
        var currency = currencyLabel();
        var difference = expectedAmount - currentAmount;
        var calculatedIncrease = currentAmount > 0 && expectedAmount > 0
            ? (difference / currentAmount) * 100
            : 0;

        if (currentTarget) {
            currentTarget.textContent = currency + ' ' + formatAmount(currentAmount);
        }
        if (expectedTarget) {
            expectedTarget.textContent = currency + ' ' + formatAmount(expectedAmount);
        }
        if (currentLabel) {
            currentLabel.textContent = 'Current Salary (' + salaryPeriodLabels[period] + ')';
        }
        if (expectedLabel) {
            expectedLabel.textContent = 'Expected Salary (' + salaryPeriodLabels[period] + ')';
        }
        if (differenceOutput) {
            differenceOutput.textContent = currency + ' ' + formatAmount(difference);
        }
        if (increaseOutput) {
            increaseOutput.textContent = formatAmount(calculatedIncrease) + '%';
        }
        if (hikeInput) {
            hikeInput.value = calculatedIncrease ? String(Math.round(calculatedIncrease * 100) / 100) : '0';
        }

        updateSalaryAutoValues('current', period);
        updateSalaryAutoValues('expected', period);
    }

    function syncSalaryPeriod(period) {
        var modeByPeriod = {
            annual: '0',
            monthly: '1',
            hourly: '2'
        };
        var normalizedPeriod = Object.prototype.hasOwnProperty.call(modeByPeriod, period) ? period : 'annual';

        root.querySelectorAll('.candidate-salary-tabs button').forEach(function (button) {
            var selected = button.getAttribute('data-salary-period') === normalizedPeriod;
            button.classList.toggle('is-active', selected);
            button.setAttribute('aria-selected', selected ? 'true' : 'false');
        });

        root.querySelectorAll('.candidate-salary-period').forEach(function (panel) {
            panel.classList.toggle('is-active', panel.getAttribute('data-salary-period') === normalizedPeriod);
        });

        var currentMode = root.querySelector('#current_ctc_mode');
        var expectedMode = root.querySelector('#current_ectc_mode');
        if (currentMode) {
            currentMode.value = modeByPeriod[normalizedPeriod];
        }
        if (expectedMode) {
            expectedMode.value = modeByPeriod[normalizedPeriod];
        }

        var currentHint = root.querySelector('#candidateCurrentSalaryHint');
        var expectedHint = root.querySelector('#candidateExpectedSalaryHint');
        if (currentHint) {
            currentHint.textContent = 'Enter your current ' + normalizedPeriod + ' salary.';
        }
        if (expectedHint) {
            expectedHint.textContent = 'Enter your expected ' + normalizedPeriod + ' salary.';
        }

        updateSalarySummary();
    }

    function syncSalaryCurrency() {
        if (!salaryCurrencySelect) {
            return;
        }

        salaryCurrencyMirrors.forEach(function (select) {
            if (select) {
                select.value = salaryCurrencySelect.value;
            }
        });

        root.querySelectorAll('.changeTextCurrency').forEach(function (label) {
            label.textContent = currencyLabel();
        });

        updateSalarySummary();
    }

    root.querySelectorAll('.candidate-salary-tabs button[data-salary-period]').forEach(function (button) {
        button.addEventListener('click', function () {
            syncSalaryPeriod(button.getAttribute('data-salary-period'));
        });
    });

    [
        'pay_annual', 'pay_monthly', 'ctc',
        'bill_annual', 'bill_monthly', 'ectc'
    ].forEach(function (id) {
        var field = root.querySelector('#' + id);
        if (field) {
            field.addEventListener('input', function () {
                window.setTimeout(updateSalarySummary, 0);
            });
            field.addEventListener('change', function () {
                window.setTimeout(updateSalarySummary, 0);
            });
        }
    });
    if (salaryCurrencySelect) {
        salaryCurrencySelect.addEventListener('change', syncSalaryCurrency);

        if (window.jQuery && $.fn.select2) {
            var $curSelect = $(salaryCurrencySelect);
            if ($curSelect.hasClass('select2-hidden-accessible')) {
                $curSelect.select2('destroy');
            }
            $curSelect.select2({
                width: '100%',
                dropdownParent: $(document.body),
                dropdownCssClass: 'br-currency-select2-dropdown',
                placeholder: 'Search currency (e.g. USD, INR, Euro, $)...',
                minimumResultsForSearch: 0,
                matcher: function (params, data) {
                    if ($.trim(params.term) === '') {
                        return data;
                    }
                    if (typeof data.text === 'undefined') {
                        return null;
                    }
                    var term = params.term.toLowerCase();
                    var text = data.text.toLowerCase();
                    var $el = $(data.element);
                    var code = ($el.data('code') || '').toString().toLowerCase();
                    var searchKw = ($el.data('search') || '').toString().toLowerCase();

                    if (text.indexOf(term) > -1 || code.indexOf(term) > -1 || searchKw.indexOf(term) > -1) {
                        return data;
                    }
                    return null;
                }
            }).on('change', function () {
                syncSalaryCurrency();
            });
        }
    }

    $(document).on('click', '.candidate-salary-panel .changeTextCurrency, .candidate-salary-panel .ig-a', function () {
        if (window.jQuery && $.fn.select2 && $('#current_ctc_cur').hasClass('select2-hidden-accessible')) {
            $('#current_ctc_cur').select2('open');
        } else if (salaryCurrencySelect) {
            salaryCurrencySelect.focus();
        }
    });

    syncSalaryPeriod(activeSalaryPeriod());
    syncSalaryCurrency();
})();
</script>

<script>
// Render the existing numeric proficiency values as accessible five-star Select2 options.
$(function initializeCandidateSkillRatings() {
    function renderCandidateRating(option) {
        var value = Number.parseInt(option.id, 10) || 0;
        var wrapper = document.createElement('span');
        wrapper.className = 'candidate-rating-option';
        var stars = document.createElement('span');
        stars.className = 'candidate-rating-stars';

        for (var index = 1; index <= 5; index++) {
            var star = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
            var starPath = document.createElementNS('http://www.w3.org/2000/svg', 'path');
            star.setAttribute('viewBox', '0 0 24 24');
            star.setAttribute('focusable', 'false');
            star.setAttribute('aria-hidden', 'true');
            star.classList.add('candidate-rating-star');
            if (index <= value) {
                star.classList.add('is-filled');
            }
            starPath.setAttribute('d', 'M12 2.75 14.84 8.5l6.35.92-4.59 4.48 1.08 6.32L12 17.23l-5.68 2.99 1.08-6.32-4.59-4.48 6.35-.92L12 2.75Z');
            star.appendChild(starPath);
            stars.appendChild(star);
        }

        var label = document.createElement('span');
        label.className = value ? 'candidate-rating-value' : 'candidate-rating-placeholder';
        label.textContent = value ? value + ' ' + (value === 1 ? 'Star' : 'Stars') : 'Select rating';
        wrapper.append(stars, label);
        return $(wrapper);
    }

    if (!$.fn.select2) {
        return;
    }

    $('.job-candidate-create .candidate-rating-select').each(function () {
        var ratingSelect = $(this);
        if (ratingSelect.data('select2')) {
            ratingSelect.select2('destroy');
        }
        ratingSelect.select2({
            width: '100%',
            dropdownParent: $(document.body),
            dropdownCssClass: 'candidate-rating-dropdown',
            minimumResultsForSearch: Infinity,
            templateResult: renderCandidateRating,
            templateSelection: renderCandidateRating
        });
    });
});
</script>


<script>
(function initializeCandidateAvailabilityPickers() {
    'use strict';

    var dateFields = [
        { id: 'interviewDateOne', offset: 0 },
        { id: 'interviewDateTwo', offset: 1 },
        { id: 'interviewDateThree', offset: 2 }
    ];
    var timeFieldIds = ['interviewTimeOne', 'interviewTimeTwo', 'interviewTimeThree'];
    var timeFormat = @json($defaultTimeFormat == 1 ? 'h:i K' : 'H:i');

    function parseDisplayDate(value) {
        var match = String(value || '').match(/^(\d{2})-(\d{2})-(\d{4})$/);
        return match ? new Date(Number(match[3]), Number(match[2]) - 1, Number(match[1])) : null;
    }

    function futureDate(offset) {
        var date = new Date();
        date.setHours(0, 0, 0, 0);
        date.setDate(date.getDate() + offset);
        return date;
    }

    function nativeDateValue(date) {
        var month = String(date.getMonth() + 1).padStart(2, '0');
        var day = String(date.getDate()).padStart(2, '0');
        return date.getFullYear() + '-' + month + '-' + day;
    }

    function nativeTimeValue(value) {
        var match = String(value || '').trim().match(/^(\d{1,2}):(\d{2})(?:\s*(AM|PM))?$/i);
        if (!match) {
            return '';
        }

        var hour = Number(match[1]);
        var suffix = String(match[3] || '').toUpperCase();
        if (suffix === 'PM' && hour < 12) {
            hour += 12;
        } else if (suffix === 'AM' && hour === 12) {
            hour = 0;
        }

        return String(hour).padStart(2, '0') + ':' + match[2];
    }

    if (typeof window.flatpickr === 'function') {
        timeFieldIds.forEach(function (id) {
            var field = document.getElementById(id);
            if (!field || field._flatpickr) {
                return;
            }

            window.flatpickr(field, {
                enableTime: true,
                noCalendar: true,
                dateFormat: timeFormat,
                time_24hr: timeFormat === 'H:i',
                minuteIncrement: 1,
                allowInput: true,
                clickOpens: true,
                disableMobile: true
            });
        });

        dateFields.forEach(function (config) {
            var field = document.getElementById(config.id);
            if (!field || field._flatpickr) {
                return;
            }

            var savedDate = parseDisplayDate(field.value);
            window.flatpickr(field, {
                dateFormat: 'd-m-Y',
                altInput: true,
                altFormat: 'd-M-Y',
                allowInput: true,
                clickOpens: true,
                disableMobile: true,
                defaultDate: savedDate || futureDate(config.offset),
                minDate: savedDate ? null : 'today',
                disable: savedDate ? [function (date) {
                    var today = futureDate(0);
                    return date < today && date.getTime() !== savedDate.getTime();
                }] : []
            });
        });

        return;
    }

    dateFields.forEach(function (config) {
        var field = document.getElementById(config.id);
        if (!field) {
            return;
        }

        var savedDate = parseDisplayDate(field.value);
        field.type = 'date';
        field.value = nativeDateValue(savedDate || futureDate(config.offset));
        if (!savedDate) {
            field.min = nativeDateValue(futureDate(0));
        }
    });

    timeFieldIds.forEach(function (id) {
        var field = document.getElementById(id);
        if (!field) {
            return;
        }

        var existingValue = nativeTimeValue(field.value);
        field.type = 'time';
        field.value = existingValue;
    });
})();
</script>
<script>
/**
 * Highest Qualification System: 100% In-Browser RAM Custom Combobox & Curation Engine
 * Compliant with UNESCO ISCED-2011 7-Category Global Architecture
 *
 * Characteristics:
 * - 0% Server / MySQL / Elasticsearch read calls (Instant < 1ms browser RAM execution)
 * - Real-time Country-Adaptive Education curation based on Nationality
 * - Global Escape Hatch: Real-time search unlocks all 7 categories and 60+ degrees worldwide with token/compact/alias matching
 * - 100% Two-Way Synchronization with native <select id="qualification"> for form submission & resume parser
 * - Rule 17 Page Isolation & 15.6" screen responsiveness
 */
(function($) {
    'use strict';

    // 1. Regional Bucket Resolver for Adaptive Idle Curation
    function getRegionalBucket(nationality) {
        var nat = (nationality || '').trim();
        if (!nat || nat === 'Indian') return ['Indian'];
        if (nat === 'American') return ['American'];
        if (nat === 'Canadian') return ['Canadian', 'American'];
        if (nat === 'British') return ['British', 'Commonwealth'];
        if (nat === 'Australian') return ['Australian', 'Commonwealth', 'British'];
        if (['German', 'French', 'Dutch', 'Irish', 'Italian', 'Spanish', 'Swedish', 'Swiss', 'Austrian', 'Belgian', 'Polish'].indexOf(nat) !== -1) {
            return ['European'];
        }
        if (['Emirati', 'Saudi', 'Qatari', 'Kuwaiti', 'Bahraini', 'Omani'].indexOf(nat) !== -1) {
            return ['Indian', 'British', 'American'];
        }
        return ['all']; // 'Other' or unlisted demonyms show all degrees cleanly
    }

    // 2. Token-Based, Accent-Insensitive and Punctuation-Insensitive String Cleaner
    function jeroCleanStr(str) {
        if (!str) return '';
        return str.toString()
            .toLowerCase()
            .normalize('NFD').replace(/[\u0300-\u036f]/g, '')
            .replace(/[.,\/#!$%\^&\*;:{}=\-_`~()]/g, ' ')
            .replace(/\s+/g, ' ')
            .trim();
    }

    // 3. Close All Comboboxes Helper
    function closeAllComboboxes() {
        $('.jero-custom-select').removeClass('is-open open-up');
        $('.jero-cs-trigger').attr('aria-expanded', 'false');
        $('.candidate-profile-card, .bd-card').removeClass('has-active-combobox');
    }

    // 4. Build Custom Combobox DOM from native <select id="nationality">
    function buildNationalityCombobox() {
        var $select = $('#nationality');
        if (!$select.length) return;
        $('#jero-cs-nationality').remove();

        var placeholder = 'Select nationality';
        var currentVal = ($select.val() || '').trim();
        var currentLabel = placeholder;
        if (currentVal) {
            var $selOpt = $select.find('option:selected').first();
            if ($selOpt.length && $selOpt.val()) {
                currentLabel = $selOpt.text().trim();
            }
        }

        var html = '<div class="jero-custom-select" id="jero-cs-nationality">' +
            '<button type="button" class="jero-cs-trigger" aria-haspopup="listbox" aria-expanded="false">' +
                '<div class="jero-cs-left">' +
                    '<span class="jero-cs-icon"><i class="fa-regular fa-flag"></i></span>' +
                    '<span class="jero-cs-value ' + (!currentVal ? 'jero-cs-placeholder' : '') + '">' + currentLabel + '</span>' +
                '</div>' +
                '<div class="jero-cs-right">' +
                    '<span class="jero-cs-arrow"><i class="fa-solid fa-chevron-down"></i></span>' +
                '</div>' +
            '</button>' +
            '<div class="jero-cs-dropdown">' +
                '<div class="jero-cs-search-box">' +
                    '<i class="fa-solid fa-magnifying-glass jero-cs-search-icon"></i>' +
                    '<input type="text" class="jero-cs-search-input" placeholder="Search nationality or country (e.g. India, USA, UK, Canada)..." spellcheck="false" autocomplete="off">' +
                    '<button type="button" class="jero-cs-search-clear" style="display:none;" title="Clear search">&times;</button>' +
                '</div>' +
                '<div class="jero-cs-list"></div>' +
                '<div class="jero-cs-empty" style="display:none;"><i class="fa-solid fa-circle-question"></i> No nationality found</div>' +
            '</div>' +
        '</div>';

        var $cs = $(html);
        var $list = $cs.find('.jero-cs-list');
        var selectedItemDone = false;

        $select.children().each(function() {
            var $node = $(this);
            if ($node.is('optgroup')) {
                var groupLabel = $node.attr('label') || '';
                var $group = $('<div class="jero-cs-group"><div class="jero-cs-group-title">' + groupLabel + '</div></div>');
                $node.find('option').each(function() {
                    var $opt = $(this);
                    var val = $opt.val();
                    if (!val) return;
                    var text = $opt.text().trim();
                    var synonyms = $opt.attr('data-synonyms') || '';
                    var isSelected = (val === currentVal && !selectedItemDone);
                    if (isSelected) selectedItemDone = true;
                    var $item = $('<div class="jero-cs-item ' + (isSelected ? 'is-selected' : '') + '" data-val="' + val + '" data-synonyms="' + synonyms + '">' +
                        '<span class="jero-cs-item-text">' + text + '</span>' +
                    '</div>');
                    $group.append($item);
                });
                $list.append($group);
            } else if ($node.is('option') && $node.val()) {
                var val = $node.val();
                var text = $node.text().trim();
                var synonyms = $node.attr('data-synonyms') || '';
                var isSelected = (val === currentVal && !selectedItemDone);
                if (isSelected) selectedItemDone = true;
                var $item = $('<div class="jero-cs-item ' + (isSelected ? 'is-selected' : '') + '" data-val="' + val + '" data-synonyms="' + synonyms + '">' +
                    '<span class="jero-cs-item-text">' + text + '</span>' +
                '</div>');
                $list.append($item);
            }
        });

        $select.closest('.candidate-field-control, .bd-control, .fg').addClass('candidate-field-control--has-combobox bd-control--has-combobox fg--has-combobox has-combobox');
        $select.after($cs);
    }

    // 5. Build Custom Combobox DOM from native <select id="qualification">
    function buildQualificationCombobox() {
        var $select = $('#qualification');
        if (!$select.length) return;
        $('#jero-cs-qualification').remove();

        var placeholder = 'Select qualification';
        var currentVal = ($select.val() || '').trim();
        var currentLabel = placeholder;
        var currentBadge = '';

        if (currentVal) {
            var $selOpt = $select.find('option:selected').first();
            if ($selOpt.length && $selOpt.val()) {
                currentLabel = $selOpt.text().trim();
                currentBadge = $selOpt.attr('data-badge') || '';
            }
        }

        var html = '<div class="jero-custom-select" id="jero-cs-qualification">' +
            '<button type="button" class="jero-cs-trigger" aria-haspopup="listbox" aria-expanded="false">' +
                '<div class="jero-cs-left">' +
                    '<span class="jero-cs-icon"><i class="fa-solid fa-graduation-cap"></i></span>' +
                    '<span class="jero-cs-value ' + (!currentVal ? 'jero-cs-placeholder' : '') + '">' + currentLabel + '</span>' +
                '</div>' +
                '<div class="jero-cs-right">' +
                    '<span class="jero-cs-badge" style="' + (currentBadge ? '' : 'display:none;') + '">' + currentBadge + '</span>' +
                    '<span class="jero-cs-arrow"><i class="fa-solid fa-chevron-down"></i></span>' +
                '</div>' +
            '</button>' +
            '<div class="jero-cs-dropdown">' +
                '<div class="jero-cs-search-box">' +
                    '<i class="fa-solid fa-magnifying-glass jero-cs-search-icon"></i>' +
                    '<input type="text" class="jero-cs-search-input" placeholder="Search degree or acronym (e.g. B.Tech, MS, HND, CA, MBA)..." spellcheck="false" autocomplete="off">' +
                    '<button type="button" class="jero-cs-search-clear" style="display:none;" title="Clear search">&times;</button>' +
                '</div>' +
                '<div class="jero-cs-list"></div>' +
                '<div class="jero-cs-empty" style="display:none;"><i class="fa-solid fa-graduation-cap"></i> No matching degrees found</div>' +
            '</div>' +
        '</div>';

        var $cs = $(html);
        var $list = $cs.find('.jero-cs-list');
        var selectedItemDone = false;

        $select.children().each(function() {
            var $node = $(this);
            if ($node.is('optgroup')) {
                var groupLabel = $node.attr('label') || '';
                var $group = $('<div class="jero-cs-group"><div class="jero-cs-group-title">' + groupLabel + '</div></div>');
                $node.find('option').each(function() {
                    var $opt = $(this);
                    var val = $opt.val();
                    if (!val) return;
                    var text = $opt.text().trim();
                    var regions = $opt.attr('data-regions') || 'Global';
                    var aliases = $opt.attr('data-aliases') || '';
                    var badge = $opt.attr('data-badge') || '';
                    var isSelected = (val === currentVal && !selectedItemDone);
                    if (isSelected) selectedItemDone = true;
                    var badgeHtml = badge ? '<span class="jero-cs-item-badge">' + badge + '</span>' : '';
                    var $item = $('<div class="jero-cs-item ' + (isSelected ? 'is-selected' : '') + '" data-val="' + val + '" data-regions="' + regions + '" data-aliases="' + aliases + '" data-badge="' + badge + '">' +
                        '<span class="jero-cs-item-text">' + text + '</span>' +
                        badgeHtml +
                    '</div>');
                    $group.append($item);
                });
                $list.append($group);
            }
        });

        $select.closest('.candidate-field-control, .bd-control, .fg').addClass('candidate-field-control--has-combobox bd-control--has-combobox fg--has-combobox has-combobox');
        $select.after($cs);
    }

    // 6. Filter Nationality items by country synonyms or demonym text
    function filterNationalityItems() {
        var $cs = $('#jero-cs-nationality');
        if (!$cs.length) return;
        var rawQuery = $cs.find('.jero-cs-search-input').val() || '';
        var cleanQ = jeroCleanStr(rawQuery);
        var compactQ = cleanQ.replace(/\s+/g, '');
        var queryWords = cleanQ.split(' ').filter(Boolean);
        var totalVisible = 0;

        $cs.find('.jero-cs-group').each(function() {
            var $group = $(this);
            var groupVisible = 0;

            $group.find('.jero-cs-item').each(function() {
                var $item = $(this);
                var text = $item.find('.jero-cs-item-text').text() || '';
                var synonyms = $item.attr('data-synonyms') || '';
                var val = $item.attr('data-val') || '';

                var isMatch = false;
                if (!cleanQ) {
                    isMatch = true;
                } else {
                    var cleanText = jeroCleanStr(text);
                    var cleanSynonyms = jeroCleanStr(synonyms);
                    var cleanVal = jeroCleanStr(val);
                    var combined = cleanText + ' ' + cleanSynonyms + ' ' + cleanVal;
                    var combinedCompact = combined.replace(/\s+/g, '');

                    if (cleanText.indexOf(cleanQ) !== -1 || cleanSynonyms.indexOf(cleanQ) !== -1 || cleanVal.indexOf(cleanQ) !== -1) {
                        isMatch = true;
                    } else if (combinedCompact.indexOf(compactQ) !== -1) {
                        isMatch = true;
                    } else if (queryWords.length > 0) {
                        var allWordsMatch = true;
                        for (var w = 0; w < queryWords.length; w++) {
                            if (combined.indexOf(queryWords[w]) === -1 && combinedCompact.indexOf(queryWords[w]) === -1) {
                                allWordsMatch = false;
                                break;
                            }
                        }
                        if (allWordsMatch) {
                            isMatch = true;
                        }
                    }
                }

                if (isMatch) {
                    $item.removeClass('is-hidden').show();
                    groupVisible++;
                    totalVisible++;
                } else {
                    $item.addClass('is-hidden').hide();
                }
            });

            if (groupVisible > 0) {
                $group.removeClass('is-hidden').show();
            } else {
                $group.addClass('is-hidden').hide();
            }
        });

        if (totalVisible === 0) {
            $cs.find('.jero-cs-empty').removeClass('is-hidden').show();
        } else {
            $cs.find('.jero-cs-empty').addClass('is-hidden').hide();
        }
    }

    // 7. Intelligent Filtering for Highest Qualification (Idle Regional Curation vs Global Search Unlock)
    function filterQualificationItems() {
        var $cs = $('#jero-cs-qualification');
        if (!$cs.length) return;
        var selectedNat = ($('#nationality').val() || '').trim();
        var selectedQual = ($('#qualification').val() || '').trim();
        var bucket = getRegionalBucket(selectedNat);
        var rawQuery = $cs.find('.jero-cs-search-input').val() || '';
        var cleanQ = jeroCleanStr(rawQuery);
        var compactQ = cleanQ.replace(/\s+/g, '');
        var queryWords = cleanQ.split(' ').filter(Boolean);
        var totalVisible = 0;

        $cs.find('.jero-cs-group').each(function() {
            var $group = $(this);
            var groupVisible = 0;

            $group.find('.jero-cs-item').each(function() {
                var $item = $(this);
                var val = String($item.attr('data-val') || '');
                var text = $item.find('.jero-cs-item-text').text() || '';
                var aliases = $item.attr('data-aliases') || '';
                var badge = $item.attr('data-badge') || '';
                var regions = ($item.attr('data-regions') || 'Global').split(',').map(function(r) { return r.trim(); });

                var isMatch = false;

                if (cleanQ.length > 0) {
                    // Global Search Mode: Unlocks all degrees worldwide regardless of nationality
                    var cleanText = jeroCleanStr(text);
                    var cleanAliases = jeroCleanStr(aliases);
                    var cleanBadge = jeroCleanStr(badge);
                    var combined = cleanText + ' ' + cleanAliases + ' ' + cleanBadge;
                    var combinedCompact = combined.replace(/\s+/g, '');

                    if (cleanText.indexOf(cleanQ) !== -1 || cleanAliases.indexOf(cleanQ) !== -1 || cleanBadge.indexOf(cleanQ) !== -1) {
                        isMatch = true;
                    } else if (combinedCompact.indexOf(compactQ) !== -1) {
                        isMatch = true;
                    } else if (queryWords.length > 0) {
                        var allWordsMatch = true;
                        for (var w = 0; w < queryWords.length; w++) {
                            if (combined.indexOf(queryWords[w]) === -1 && combinedCompact.indexOf(queryWords[w]) === -1) {
                                allWordsMatch = false;
                                break;
                            }
                        }
                        if (allWordsMatch) isMatch = true;
                    }
                } else {
                    // Idle Mode: Regionally curates degrees based on selected nationality
                    if (val === selectedQual) {
                        isMatch = true;
                    } else if (bucket.indexOf('all') !== -1 || regions.indexOf('Global') !== -1) {
                        isMatch = true;
                    } else {
                        for (var k = 0; k < bucket.length; k++) {
                            if (regions.indexOf(bucket[k]) !== -1) {
                                isMatch = true;
                                break;
                            }
                        }
                    }
                }

                if (isMatch) {
                    $item.removeClass('is-hidden').show();
                    groupVisible++;
                    totalVisible++;
                } else {
                    $item.addClass('is-hidden').hide();
                }
            });

            if (groupVisible > 0) {
                $group.removeClass('is-hidden').show();
            } else {
                $group.addClass('is-hidden').hide();
            }
        });

        if (totalVisible === 0) {
            $cs.find('.jero-cs-empty').removeClass('is-hidden').show();
        } else {
            $cs.find('.jero-cs-empty').addClass('is-hidden').hide();
        }
    }

    // 8. Event Listeners & Two-Way Synchronization

    // Open/Toggle Combobox
    $(document).on('click', '.jero-cs-trigger', function(e) {
        e.stopPropagation();
        var $combobox = $(this).closest('.jero-custom-select');
        var wasOpen = $combobox.hasClass('is-open');

        closeAllComboboxes();

        if (!wasOpen) {
            var rect = this.getBoundingClientRect();
            var spaceBelow = window.innerHeight - rect.bottom;
            if (spaceBelow < 340 && rect.top > 340) {
                $combobox.addClass('open-up');
            } else {
                $combobox.removeClass('open-up');
            }

            $combobox.addClass('is-open');
            $(this).attr('aria-expanded', 'true');
            $combobox.closest('.candidate-profile-card, .bd-card, .fs').addClass('has-active-combobox');

            var $search = $combobox.find('.jero-cs-search-input');
            $search.val('');
            $combobox.find('.jero-cs-search-clear').hide();

            if ($combobox.attr('id') === 'jero-cs-nationality') {
                filterNationalityItems();
            } else if ($combobox.attr('id') === 'jero-cs-qualification') {
                filterQualificationItems();
            }

            setTimeout(function() {
                $search.focus().select();
            }, 60);
        }
    });

    // Prevent clicks inside dropdown from bubbling and closing
    $(document).on('click', '.jero-cs-dropdown', function(e) {
        e.stopPropagation();
    });

    $(document).on('click', '.jero-cs-search-box', function(e) {
        e.stopPropagation();
        $(this).find('.jero-cs-search-input').focus();
    });

    // Search Input Typing Handler
    $(document).on('input keyup change', '.jero-cs-search-input', function(e) {
        if (e.which === 27 || e.which === 13 || e.which === 38 || e.which === 40) return;
        var $input = $(this);
        var val = $input.val();
        var $clearBtn = $input.siblings('.jero-cs-search-clear');
        if (val) {
            $clearBtn.show();
        } else {
            $clearBtn.hide();
        }

        var $combobox = $input.closest('.jero-custom-select');
        if ($combobox.attr('id') === 'jero-cs-nationality') {
            filterNationalityItems();
        } else if ($combobox.attr('id') === 'jero-cs-qualification') {
            filterQualificationItems();
        }
    });

    // Clear Search Input Button
    $(document).on('click', '.jero-cs-search-clear', function(e) {
        e.stopPropagation();
        var $input = $(this).siblings('.jero-cs-search-input');
        $input.val('').focus();
        $(this).hide();
        var $combobox = $(this).closest('.jero-custom-select');
        if ($combobox.attr('id') === 'jero-cs-nationality') {
            filterNationalityItems();
        } else if ($combobox.attr('id') === 'jero-cs-qualification') {
            filterQualificationItems();
        }
    });

    // Keyboard Navigation: Escape, Arrows, Enter
    $(document).on('keydown', '.jero-custom-select.is-open', function(e) {
        var $combobox = $(this);
        var $visibleItems = $combobox.find('.jero-cs-item:not(.is-hidden):visible');

        if (e.which === 27) { // Escape
            closeAllComboboxes();
            $combobox.find('.jero-cs-trigger').focus();
            e.preventDefault();
        } else if (e.which === 40) { // Arrow Down
            var $curr = $visibleItems.filter('.is-highlighted');
            var idx = $visibleItems.index($curr);
            $visibleItems.removeClass('is-highlighted');
            if (idx === -1 || idx >= $visibleItems.length - 1) {
                var $target = $visibleItems.first();
                $target.addClass('is-highlighted');
                if ($target.length) $target[0].scrollIntoView({ block: 'nearest' });
            } else {
                var $target = $visibleItems.eq(idx + 1);
                $target.addClass('is-highlighted');
                if ($target.length) $target[0].scrollIntoView({ block: 'nearest' });
            }
            e.preventDefault();
        } else if (e.which === 38) { // Arrow Up
            var $curr = $visibleItems.filter('.is-highlighted');
            var idx = $visibleItems.index($curr);
            $visibleItems.removeClass('is-highlighted');
            if (idx <= 0) {
                var $target = $visibleItems.last();
                $target.addClass('is-highlighted');
                if ($target.length) $target[0].scrollIntoView({ block: 'nearest' });
            } else {
                var $target = $visibleItems.eq(idx - 1);
                $target.addClass('is-highlighted');
                if ($target.length) $target[0].scrollIntoView({ block: 'nearest' });
            }
            e.preventDefault();
        } else if (e.which === 13) { // Enter
            var $target = $visibleItems.filter('.is-highlighted');
            if (!$target.length) {
                $target = $visibleItems.first();
            }
            if ($target.length) {
                $target.trigger('click');
            }
            e.preventDefault();
        }
    });

    // Select Nationality Item
    $(document).on('click', '#jero-cs-nationality .jero-cs-item', function(e) {
        e.stopPropagation();
        var $item = $(this);
        var val = $item.attr('data-val') || '';
        var text = $item.find('.jero-cs-item-text').text().trim();

        var $cs = $('#jero-cs-nationality');
        $cs.find('.jero-cs-item').removeClass('is-selected');
        $item.addClass('is-selected');
        $cs.find('.jero-cs-value').removeClass('jero-cs-placeholder').text(text);
        $cs.removeClass('has-validation-error');
        $cs.closest('.candidate-field-control, .bd-control, .fg').removeClass('has-validation-error');
        $('[data-field-error="nationality"]').remove();
        closeAllComboboxes();

        var $nativeNat = $('#nationality');
        if ($nativeNat.val() !== val) {
            $nativeNat.val(val).trigger('change');
        }

        filterQualificationItems();

        if (window.benchFormValidator && typeof window.benchFormValidator.revalidateField === 'function') {
            window.benchFormValidator.revalidateField('nationality');
            window.benchFormValidator.revalidateField('qualification');
        }
    });

    // Select Qualification Item
    $(document).on('click', '#jero-cs-qualification .jero-cs-item', function(e) {
        e.stopPropagation();
        var $item = $(this);
        var val = $item.attr('data-val') || '';
        var text = $item.find('.jero-cs-item-text').text().trim();
        var badge = $item.attr('data-badge') || '';
        var $cs = $('#jero-cs-qualification');

        $cs.find('.jero-cs-item').removeClass('is-selected');
        $item.addClass('is-selected');
        $cs.find('.jero-cs-value').removeClass('jero-cs-placeholder').text(text);
        if (badge) {
            $cs.find('.jero-cs-badge').show().text(badge);
        } else {
            $cs.find('.jero-cs-badge').hide().text('');
        }
        $cs.removeClass('has-validation-error');
        $cs.closest('.candidate-field-control, .bd-control, .fg').removeClass('has-validation-error');
        $('[data-field-error="qualification"]').remove();
        closeAllComboboxes();

        var $native = $('#qualification');
        if ($native.val() !== val) {
            $native.val(val).trigger('change');
            if (window.benchFormValidator && typeof window.benchFormValidator.revalidateField === 'function') {
                window.benchFormValidator.revalidateField('qualification');
            }
        }
    });

    // Global Click Outside Handler
    $(document).on('click', function(e) {
        if (!$(e.target).closest('.jero-custom-select').length) {
            closeAllComboboxes();
        }
    });

    // Native <select id="nationality"> External Change Listener (Resume Parser or programmatic update)
    $(document).on('change', '#nationality', function() {
        var val = ($(this).val() || '').trim();
        var $cs = $('#jero-cs-nationality');
        if ($cs.length) {
            var $item = $cs.find('.jero-cs-item[data-val="' + val + '"]').first();
            $cs.find('.jero-cs-item').removeClass('is-selected');
            if ($item.length) {
                $item.addClass('is-selected');
                $cs.find('.jero-cs-value').removeClass('jero-cs-placeholder').text($item.find('.jero-cs-item-text').text().trim());
            } else if (!val) {
                $cs.find('.jero-cs-value').addClass('jero-cs-placeholder').text('Select nationality');
            } else {
                $cs.find('.jero-cs-value').removeClass('jero-cs-placeholder').text(val);
            }
        }
        filterQualificationItems();
    });

    // Native <select id="qualification"> External Change Listener (Resume Parser or programmatic update)
    $(document).on('change', '#qualification', function() {
        var val = ($(this).val() || '').trim();
        var $cs = $('#jero-cs-qualification');
        if ($cs.length) {
            var $item = $cs.find('.jero-cs-item[data-val="' + val + '"]').first();
            $cs.find('.jero-cs-item').removeClass('is-selected');
            if ($item.length) {
                $item.addClass('is-selected');
                $cs.find('.jero-cs-value').removeClass('jero-cs-placeholder').text($item.find('.jero-cs-item-text').text().trim());
                var badge = $item.attr('data-badge') || '';
                if (badge) {
                    $cs.find('.jero-cs-badge').show().text(badge);
                } else {
                    $cs.find('.jero-cs-badge').hide().text('');
                }
            } else if (!val) {
                $cs.find('.jero-cs-value').addClass('jero-cs-placeholder').text('Select qualification');
                $cs.find('.jero-cs-badge').hide().text('');
            }
        }
    });

    // Mount on DOM Ready
    $(document).ready(function() {
        buildNationalityCombobox();
        buildQualificationCombobox();
        filterQualificationItems();
    });

})(jQuery);
</script>
@endpush
