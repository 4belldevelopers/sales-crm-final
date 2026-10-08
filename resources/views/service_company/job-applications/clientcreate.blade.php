{{--
 * Add Candidate UI/UX — Direct Client Candidate Flow (clientcreate.blade.php)
 * ==========================================================================
 * Enterprise Light Green Theme (Emerald / Mint) Edition
 * Pixel-Level UI/UX Replica of create.blade.php with complete page isolation.
 *
 * Specification: docs/ADD_CANDIDATE_UI_UX_DESIGN.md
 * Architectural Invariants Followed:
 * - Rule 1 & 2: CQRS Architecture (MySQL writes only, 0% reads on request path)
 * - Rule 3: Client-First Execution (User RAM First, WASM Parser, 0% Server Strain)
 * - Rule 4: 10K Concurrency Guard (Sub-15ms transaction locks, R2 presigned uploads)
 * - Rule 7: Zero Business Logic Mutations (Exact same fields, inputs, validations)
 * - Rule 14: 15.6" Laptop Display Optimization (1366x768 & 1920x1080) with zero horizontal scroll
 * - Rule 17: Complete Page/Blade Isolation (Standalone scripts, no shared mutable state)
 * - Flow Identity: Enterprise Light Green Theme (#059669 / #10b981 / #ecfdf5)
--}}
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
<link href="{{ asset('demo/css/job-application-create.css') }}{{ TM_VERSION }}&candidate_header_compact_icons=20260925&candidate_design_repair=20260926&candidate_header_harmonize=20260926&candidate_rating_view=20260926&resume_card_design=20260926&resume_card_clean=20260926&required_fields_modal=20261006_v2" rel="stylesheet" type="text/css" />
<style>
/* ==========================================================================
   DIRECT CLIENT FLOW — ENTERPRISE LIGHT GREEN THEME (EMERALD / MINT)
   Scoped Theme Tokens: Primary (#059669), Hover (#047857), Soft (#f0fdf4 / #ecfdf5)
   ========================================================================== */
:root,
.candidate-top-toolbar,
.job-candidate-create {
    --jac-primary: #059669 !important; /* Enterprise Emerald Green */
    --jac-primary-hover: #047857 !important; /* Deep Forest Emerald */
    --jac-soft: #f0fdf4 !important; /* Soft Mint Canvas */
    --jac-success: #059669 !important;
}

.candidate-top-toolbar {
    background: #f0fdf4 !important;
    border-bottom: 1px solid #d1fae5 !important;
}

.candidate-top-toolbar .candidate-toolbar-container {
    background: transparent !important;
}

/* Primary Topbar Action Button & Buttons with .btn-primary */
.talent-topbar-primary,
.candidate-topbar .talent-topbar-primary,
.candidate-top-toolbar .talent-topbar-primary {
    border-color: #059669 !important;
    background: #059669 !important;
    box-shadow: 0 6px 14px rgba(5, 150, 105, 0.22) !important;
}

.talent-topbar-primary:hover,
.talent-topbar-primary:focus,
.candidate-topbar .talent-topbar-primary:hover,
.candidate-topbar .talent-topbar-primary:focus,
.candidate-top-toolbar .talent-topbar-primary:hover,
.candidate-top-toolbar .talent-topbar-primary:focus {
    border-color: #047857 !important;
    background: #047857 !important;
    box-shadow: 0 8px 18px rgba(5, 150, 105, 0.28) !important;
}

.job-candidate-create .btn-primary,
.candidate-top-toolbar .btn-primary {
    background-color: #059669 !important;
    border-color: #059669 !important;
}

.job-candidate-create .btn-primary:hover,
.job-candidate-create .btn-primary:focus,
.candidate-top-toolbar .btn-primary:hover,
.candidate-top-toolbar .btn-primary:focus {
    background-color: #047857 !important;
    border-color: #047857 !important;
}

/* Direct Client Flow Indicator Pill */
.client-flow-pill {
    display: inline-flex !important;
    align-items: center !important;
    gap: 6px !important;
    letter-spacing: 0.2px !important;
    font-weight: 600 !important;
    box-shadow: 0 1px 2px rgba(5, 150, 105, 0.08) !important;
}

/* Badges & Theme Tints */
.job-candidate-create .badge-light-primary {
    background-color: #ecfdf5 !important;
    color: #059669 !important;
}
.job-candidate-create .bg-light-primary {
    background-color: #ecfdf5 !important;
    color: #059669 !important;
}


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
    border-color: #10b981 !important;
    box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.15) !important;
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
    border-color: #10b981;
    box-shadow: 0 0 0 2px rgba(16, 185, 129, 0.2);
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
    background-color: #10b981 !important;
    color: #ffffff !important;
}
.br-currency-select2-dropdown .select2-results__option[aria-selected="true"] {
    background-color: #ecfdf5;
    color: #047857;
    font-weight: 600;
}

/* ==========================================================================
   WORK AUTHORIZATION & COMPLIANCE ENGINE (ENTERPRISE 38PX SPECIFICATION)
   ========================================================================== */
.wa-popular-chips-wrap {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 8px;
    margin-bottom: 10px;
    margin-top: 4px;
}

.wa-chips-label {
    font-family: 'Poppins', sans-serif;
    font-size: 11.5px;
    font-weight: 600;
    color: #475569;
    display: inline-flex;
    align-items: center;
    white-space: nowrap;
}

.wa-popular-chips {
    display: inline-flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 6px;
}

.wa-chip-btn {
    appearance: none;
    background: #F8FAFC;
    border: 1px solid #E2E8F0;
    border-radius: 20px;
    padding: 3px 11px;
    font-family: 'Poppins', sans-serif;
    font-size: 11.5px;
    font-weight: 500;
    color: #334155;
    cursor: pointer;
    line-height: 1.4;
    display: inline-flex;
    align-items: center;
    gap: 5px;
    transition: all 0.15s ease-in-out;
    outline: none;
    user-select: none;
}

.wa-chip-btn:hover {
    background: #ecfdf5;
    border-color: #93C5FD;
    color: #047857;
    transform: translateY(-1px);
}

.wa-chip-btn.is-active {
    background: #059669 !important;
    border-color: #059669 !important;
    color: #FFFFFF !important;
    font-weight: 600 !important;
    box-shadow: 0 2px 6px rgba(37, 99, 235, 0.25) !important;
}

.addl-select-wrap {
    position: relative !important;
    width: 100% !important;
    cursor: pointer !important;
}

.addl-select-wrap select.wa-select {
    width: 100% !important;
    height: 38px !important;
    min-height: 38px !important;
    max-height: 38px !important;
    border: 1px solid #CDD7EB !important;
    border-radius: 6px !important;
    padding: 0 38px 0 14px !important;
    font-family: 'Poppins', sans-serif !important;
    font-size: 13px !important;
    font-weight: 500 !important;
    color: #0f172a !important;
    background-color: #FFFFFF !important;
    outline: none !important;
    box-sizing: border-box !important;
    line-height: 36px !important;
}

.addl-select-wrap select.select2-hidden-accessible {
    position: absolute !important;
    left: -9999px !important;
    width: 1px !important;
    height: 1px !important;
    opacity: 0 !important;
    pointer-events: none !important;
    visibility: hidden !important;
}

.addl-select-wrap .select2-container {
    width: 100% !important;
    display: block !important;
    cursor: pointer !important;
}

.addl-select-wrap .select2-selection,
.addl-select-wrap .select2-selection--single,
.addl-select-wrap .select2-selection.form-select,
.addl-select-wrap .select2-container--default .select2-selection--single,
.addl-select-wrap .select2-container--bootstrap5 .select2-selection--single,
.addl-select-wrap .select2-container .select2-selection--single {
    min-height: 38px !important;
    height: 38px !important;
    max-height: 38px !important;
    border: 1px solid #CDD7EB !important;
    border-radius: 6px !important;
    padding: 0 38px 0 14px !important;
    display: flex !important;
    align-items: center !important;
    justify-content: flex-start !important;
    background-color: #FFFFFF !important;
    background-image: none !important;
    box-sizing: border-box !important;
    cursor: pointer !important;
    transition: border-color .15s ease-in-out, box-shadow .15s ease-in-out !important;
}

.addl-select-wrap .select2-container.select2-container--open .select2-selection--single,
.addl-select-wrap .select2-container.select2-container--focus .select2-selection--single,
.addl-select-wrap .select2-container--default.select2-container--open .select2-selection--single,
.addl-select-wrap .select2-container--default.select2-container--focus .select2-selection--single,
.addl-select-wrap .select2-container--bootstrap5.select2-container--open .select2-selection--single,
.addl-select-wrap .select2-container--bootstrap5.select2-container--focus .select2-selection--single {
    border-color: #10b981 !important;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, .12) !important;
    outline: none !important;
}

.addl-select-wrap .select2-selection__rendered,
.addl-select-wrap .select2-container--default .select2-selection--single .select2-selection__rendered,
.addl-select-wrap .select2-container--bootstrap5 .select2-selection--single .select2-selection__rendered,
.addl-select-wrap .select2-container .select2-selection--single .select2-selection__rendered {
    font-family: 'Poppins', sans-serif !important;
    font-size: 13px !important;
    font-weight: 500 !important;
    color: #0f172a !important;
    line-height: 36px !important;
    height: 36px !important;
    padding: 0 !important;
    margin: 0 !important;
    display: block !important;
    width: 100% !important;
    text-align: left !important;
    overflow: hidden !important;
    text-overflow: ellipsis !important;
    white-space: nowrap !important;
    box-sizing: border-box !important;
}

.addl-select-wrap .select2-selection__placeholder,
.addl-select-wrap .select2-container--default .select2-selection--single .select2-selection__placeholder,
.addl-select-wrap .select2-container--bootstrap5 .select2-selection--single .select2-selection__placeholder,
.addl-select-wrap .select2-selection__rendered .select2-selection__placeholder {
    color: #8A94B4 !important;
    font-size: 13px !important;
    font-weight: 400 !important;
    line-height: 36px !important;
}

.addl-select-wrap .select2-selection__clear,
.addl-select-wrap .select2-container--default .select2-selection--single .select2-selection__clear,
.addl-select-wrap .select2-container--bootstrap5 .select2-selection--single .select2-selection__clear {
    position: absolute !important;
    right: 32px !important;
    top: 50% !important;
    transform: translateY(-50%) !important;
    color: #94A3B8 !important;
    font-size: 16px !important;
    font-weight: 700 !important;
    line-height: 1 !important;
    cursor: pointer !important;
    z-index: 3 !important;
    padding: 2px !important;
    transition: color 0.15s ease !important;
}

.addl-select-wrap .select2-selection__clear:hover {
    color: #EF4444 !important;
}

.addl-select-wrap .select2-selection__arrow,
.addl-select-wrap .select2-container--default .select2-selection--single .select2-selection__arrow,
.addl-select-wrap .select2-container--bootstrap5 .select2-selection--single .select2-selection__arrow {
    height: 36px !important;
    top: 1px !important;
    right: 12px !important;
    width: 20px !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    pointer-events: none !important;
}

.addl-select-wrap .select2-selection__arrow b {
    display: none !important;
}

.addl-select-wrap .select2-selection__arrow::after {
    content: "\f078";
    font-family: "Font Awesome 6 Free", "Font Awesome 5 Free", "FontAwesome" !important;
    font-weight: 900 !important;
    font-size: 11px !important;
    color: #64748B !important;
    transition: transform 0.2s ease, color 0.2s ease !important;
}

.addl-select-wrap .select2-container.select2-container--open .select2-selection__arrow::after,
.addl-select-wrap .select2-container--open .select2-selection__arrow::after {
    transform: rotate(180deg) !important;
    color: #059669 !important;
}

.addl-select-wrap > i.fa-chevron-down,
.addl-select-wrap .select2-container ~ i.fa-chevron-down {
    display: none !important;
}

.wa-select2-dropdown,
.select2-dropdown.wa-select2-dropdown {
    font-family: 'Poppins', sans-serif !important;
    font-size: 13px !important;
    border: 1px solid #CBD5E1 !important;
    border-radius: 8px !important;
    box-shadow: 0 10px 25px -5px rgba(15, 23, 42, 0.15), 0 8px 10px -6px rgba(15, 23, 42, 0.08) !important;
    overflow: hidden !important;
    z-index: 99999 !important;
    background: #FFFFFF !important;
}

.wa-select2-dropdown .select2-search,
.wa-select2-dropdown .select2-search--dropdown,
.wa-select2-dropdown .select2-search.select2-search--dropdown,
.wa-select2-dropdown .select2-search--hide,
.wa-select2-dropdown .select2-search--dropdown.select2-search--hide,
.select2-dropdown.wa-select2-dropdown .select2-search,
.select2-dropdown.wa-select2-dropdown .select2-search--dropdown,
.select2-dropdown.wa-select2-dropdown .select2-search--dropdown.select2-search--hide {
    display: block !important;
    visibility: visible !important;
    opacity: 1 !important;
    padding: 8px 10px !important;
    background: #F8FAFC !important;
    border-bottom: 1px solid #E2E8F0 !important;
    box-sizing: border-box !important;
    height: auto !important;
}

.wa-select2-dropdown .select2-search__field,
.select2-dropdown.wa-select2-dropdown .select2-search__field {
    display: block !important;
    visibility: visible !important;
    opacity: 1 !important;
    border: 1px solid #CBD5E1 !important;
    border-radius: 6px !important;
    padding: 7px 12px !important;
    font-size: 13px !important;
    outline: none !important;
    font-family: 'Poppins', sans-serif !important;
    background-color: #FFFFFF !important;
    width: 100% !important;
    box-sizing: border-box !important;
    height: 36px !important;
    min-height: 36px !important;
    line-height: normal !important;
    color: #0f172a !important;
}

.wa-select2-dropdown .select2-search__field:focus,
.select2-dropdown.wa-select2-dropdown .select2-search__field:focus {
    border-color: #10b981 !important;
    box-shadow: 0 0 0 2px rgba(16, 185, 129, 0.2) !important;
}

/* ONLY top-level options list scrolls — single unified scrollbar */
.wa-select2-dropdown .select2-results > .select2-results__options {
    max-height: 340px !important;
    overflow-y: auto !important;
    overflow-x: hidden !important;
    padding: 4px 0 !important;
    margin: 0 !important;
    scrollbar-width: thin !important;
    scrollbar-color: #cbd5e1 #f8fafc !important;
}

/* Strictly remove inner nested scrollbar on optgroup lists (e.g. United States) */
.wa-select2-dropdown .select2-results__options--nested,
.wa-select2-dropdown .select2-results__options .select2-results__options,
.wa-select2-dropdown ul.select2-results__options--nested {
    max-height: none !important;
    height: auto !important;
    overflow: visible !important;
    overflow-y: visible !important;
    overflow-x: visible !important;
    padding: 0 !important;
    margin: 0 !important;
}

/* Sleek God-Level Scrollbar for Visa Status Dropdown */
.wa-select2-dropdown .select2-results > .select2-results__options::-webkit-scrollbar {
    width: 6px !important;
}
.wa-select2-dropdown .select2-results > .select2-results__options::-webkit-scrollbar-track {
    background: #f8fafc !important;
    border-radius: 4px !important;
}
.wa-select2-dropdown .select2-results > .select2-results__options::-webkit-scrollbar-thumb {
    background: #cbd5e1 !important;
    border-radius: 4px !important;
}
.wa-select2-dropdown .select2-results > .select2-results__options::-webkit-scrollbar-thumb:hover {
    background: #94a3b8 !important;
}

.wa-select2-dropdown .select2-results__group {
    font-family: 'Poppins', sans-serif !important;
    font-size: 11px !important;
    font-weight: 700 !important;
    text-transform: uppercase !important;
    letter-spacing: 0.5px !important;
    color: #475569 !important;
    background-color: #F1F5F9 !important;
    padding: 6px 12px !important;
    border-top: 1px solid #E2E8F0 !important;
    border-bottom: 1px solid #E2E8F0 !important;
    line-height: 1.4 !important;
}

.wa-select2-dropdown .select2-results__group:first-child {
    border-top: none !important;
}

.wa-select2-dropdown .select2-results__option {
    padding: 8px 14px !important;
    font-size: 13px !important;
    font-weight: 500 !important;
    color: #1E293B !important;
    line-height: 1.4 !important;
    transition: background-color 0.12s ease !important;
}

.wa-select2-dropdown .select2-results__option--highlighted[aria-selected] {
    background-color: #10b981 !important;
    color: #FFFFFF !important;
}

.wa-select2-dropdown .select2-results__option[aria-selected="true"] {
    background-color: #ecfdf5 !important;
    color: #047857 !important;
    font-weight: 600 !important;
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
    gap: 3px;
    padding: 0 4px;
    font-size: 8.8px !important; /* 20% reduction from 11px */
    font-weight: 500 !important;
    font-family: 'Poppins', sans-serif !important;
    border-radius: 3px;
    cursor: pointer;
    line-height: 1.35;
    transition: all 0.15s ease-in-out;
    user-select: none;
    text-decoration: none;
    background: transparent !important;
    border: none !important;
    box-shadow: none !important;
}
.jero-indicative-pill--base {
    background: transparent !important;
    color: #10b981 !important;
    border: none !important;
}
.jero-indicative-pill--converted {
    background: transparent !important;
    color: #059669 !important;
    border: none !important;
}
.jero-indicative-pill:hover,
.jero-indicative-pill:focus-visible {
    box-shadow: none !important;
    transform: none !important;
    color: #0077b5 !important;
    text-decoration: underline dotted;
    outline: none;
}
.jero-indicative-pill i {
    font-size: 8px !important;
}

/* Seamless Unified Amount & Currency Input Box */
.job-candidate-create .salary-card .candidate-salary-period .ig {
    display: flex !important;
    align-items: stretch !important;
    width: 100% !important;
    height: 38px !important;
    border: 1px solid #d1d5db !important;
    border-radius: 6px !important;
    background: #ffffff !important;
    overflow: hidden !important;
    box-sizing: border-box !important;
    transition: border-color 0.15s ease, box-shadow 0.15s ease !important;
}
.job-candidate-create .salary-card .candidate-salary-period .ig:focus-within {
    border-color: #059669 !important;
    box-shadow: 0 0 0 3px rgba(0, 158, 247, 0.15) !important;
}
.job-candidate-create .salary-card .candidate-salary-period .ig input {
    flex: 1 1 auto !important;
    min-width: 0 !important;
    height: 100% !important;
    border: none !important;
    outline: none !important;
    box-shadow: none !important;
    padding: 0 12px !important;
    font-size: 13px !important;
    font-weight: 500 !important;
    color: #0f172a !important;
    background: transparent !important;
    margin: 0 !important;
}
.job-candidate-create .salary-card .candidate-salary-period .ig input:focus {
    border: none !important;
    outline: none !important;
    box-shadow: none !important;
}
.job-candidate-create .salary-card .candidate-salary-period .ig .ig-a,
.job-candidate-create .salary-card .candidate-salary-period .ig .changeTextCurrency {
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    flex: 0 0 auto !important;
    min-width: 52px !important;
    height: 100% !important;
    padding: 0 12px !important;
    background: #f8fafc !important;
    border: none !important;
    border-left: 1px solid #e2e8f0 !important;
    border-radius: 0 !important;
    font-size: 12px !important;
    font-weight: 700 !important;
    color: #475569 !important;
    white-space: nowrap !important;
    user-select: none !important;
    margin: 0 !important;
    transition: background-color 0.15s ease, color 0.15s ease, border-color 0.15s ease !important;
}
.job-candidate-create .salary-card .candidate-salary-period .ig:focus-within .ig-a,
.job-candidate-create .salary-card .candidate-salary-period .ig:focus-within .changeTextCurrency {
    background: #ecfdf5 !important;
    color: #059669 !important;
    border-left-color: #d0e3fc !important;
}

/* Unified Warm Theme for Expected Salary (Matching Current Salary) */
.job-candidate-create .salary-card .candidate-salary-panel--expected {
    border: 1px solid #efd36b !important;
    background: #fffef8 !important;
}
.job-candidate-create .salary-card .candidate-salary-panel--expected .candidate-salary-panel-head {
    background: #fff8dc !important;
}
.job-candidate-create .salary-card .candidate-salary-panel--expected .candidate-salary-panel-head > span {
    color: #946b00 !important;
    background: #ffefad !important;
}
.job-candidate-create .salary-card .candidate-salary-panel--expected .candidate-salary-auto {
    background: #fffaf0 !important;
}

/* ============================================================================
   JERO ATS - GOD-LEVEL ENTERPRISE TIME CLOCK POPOVER (15.6" & SMALL SCREENS)
   ============================================================================ */
.jgtp-popover {
    position: fixed !important;
    z-index: 100050 !important;
    width: 310px !important;
    max-width: calc(100vw - 16px) !important;
    max-height: calc(100vh - 16px) !important;
    background: #ffffff !important;
    border-radius: 12px !important;
    border: 1px solid #e2e8f0 !important;
    box-shadow: 0 16px 40px -8px rgba(15, 23, 42, 0.28), 0 0 0 1px rgba(15, 23, 42, 0.08) !important;
    font-family: 'Poppins', sans-serif !important;
    color: #1e293b !important;
    opacity: 0;
    visibility: hidden;
    transform: translateY(4px) scale(0.98);
    transition: opacity 0.15s ease, transform 0.15s cubic-bezier(0.16, 1, 0.3, 1), visibility 0.15s !important;
    overflow: hidden !important;
    display: flex !important;
    flex-direction: column !important;
    box-sizing: border-box !important;
}
.jgtp-popover.is-open {
    opacity: 1 !important;
    visibility: visible !important;
    transform: translateY(0) scale(1) !important;
}
.jgtp-header {
    flex: 0 0 auto !important;
    background: linear-gradient(135deg, #071434 0%, #172346 100%) !important;
    color: #ffffff !important;
    padding: 8px 12px 7px !important;
    box-sizing: border-box !important;
}
.jgtp-header-top {
    display: flex !important;
    align-items: center !important;
    justify-content: space-between !important;
    margin-bottom: 5px !important;
}
.jgtp-slot-badge {
    display: inline-flex !important;
    align-items: center !important;
    background: rgba(255, 255, 255, 0.14) !important;
    color: #ffd233 !important;
    font-size: 10px !important;
    font-weight: 600 !important;
    padding: 2px 7px !important;
    border-radius: 4px !important;
    letter-spacing: 0.2px !important;
    line-height: 1.3 !important;
}
.jgtp-close-btn {
    background: transparent !important;
    border: none !important;
    color: rgba(255, 255, 255, 0.7) !important;
    font-size: 13px !important;
    cursor: pointer !important;
    padding: 2px 5px !important;
    border-radius: 4px !important;
    line-height: 1 !important;
    transition: color 0.15s ease, background 0.15s ease !important;
}
.jgtp-close-btn:hover {
    color: #ffffff !important;
    background: rgba(255, 255, 255, 0.15) !important;
}
.jgtp-display-row {
    display: flex !important;
    align-items: center !important;
    justify-content: space-between !important;
    gap: 8px !important;
}
.jgtp-digits {
    display: flex !important;
    align-items: center !important;
    gap: 2px !important;
}
.jgtp-digit {
    font-size: 22px !important;
    font-weight: 700 !important;
    color: #ffffff !important;
    letter-spacing: 0.5px !important;
    line-height: 1 !important;
    min-width: 30px !important;
    text-align: center !important;
}
.jgtp-colon {
    font-size: 18px !important;
    font-weight: 700 !important;
    color: #ffd233 !important;
    line-height: 1 !important;
    animation: jgtpBlink 1.5s infinite ease-in-out;
}
@keyframes jgtpBlink {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.4; }
}
.jgtp-ampm-switch {
    display: inline-flex !important;
    background: rgba(255, 255, 255, 0.12) !important;
    border-radius: 5px !important;
    padding: 2px !important;
    gap: 2px !important;
}
.jgtp-ampm-btn {
    background: transparent !important;
    border: none !important;
    color: rgba(255, 255, 255, 0.8) !important;
    font-size: 10px !important;
    font-weight: 600 !important;
    padding: 4px 8px !important;
    border-radius: 3px !important;
    cursor: pointer !important;
    transition: all 0.15s ease !important;
    line-height: 1 !important;
}
.jgtp-ampm-btn.is-active {
    background: #059669 !important;
    color: #ffffff !important;
    font-weight: 700 !important;
    box-shadow: 0 1px 3px rgba(0, 158, 247, 0.4) !important;
}
.jgtp-body {
    flex: 1 1 auto !important;
    min-height: 0 !important;
    padding: 8px 10px !important;
    overflow-y: auto !important;
    overflow-x: hidden !important;
    scrollbar-width: thin !important;
    scrollbar-color: #cbd5e1 #f8fafc !important;
    box-sizing: border-box !important;
}
.jgtp-body::-webkit-scrollbar {
    width: 5px !important;
}
.jgtp-body::-webkit-scrollbar-track {
    background: #f8fafc !important;
}
.jgtp-body::-webkit-scrollbar-thumb {
    background: #cbd5e1 !important;
    border-radius: 4px !important;
}
.jgtp-body::-webkit-scrollbar-thumb:hover {
    background: #94a3b8 !important;
}
.jgtp-section {
    margin-bottom: 7px !important;
}
.jgtp-section:last-child {
    margin-bottom: 0 !important;
}
.jgtp-section-label {
    font-size: 9px !important;
    font-weight: 700 !important;
    text-transform: uppercase !important;
    letter-spacing: 0.5px !important;
    color: #64748b !important;
    margin-bottom: 4px !important;
    display: flex !important;
    align-items: center !important;
    line-height: 1.2 !important;
}
.jgtp-presets-grid {
    display: grid !important;
    grid-template-columns: repeat(5, minmax(0, 1fr)) !important;
    gap: 3px !important;
}
.jgtp-preset-chip {
    padding: 4px 2px !important;
    font-size: 9px !important;
    font-weight: 600 !important;
    border: 1px solid #e2e8f0 !important;
    border-radius: 4px !important;
    background: #f8fafc !important;
    color: #334155 !important;
    cursor: pointer !important;
    text-align: center !important;
    white-space: nowrap !important;
    transition: all 0.12s ease !important;
    line-height: 1 !important;
    min-height: 22px !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
}
.jgtp-preset-chip:hover {
    border-color: #059669 !important;
    background: #f0fdf4 !important;
    color: #059669 !important;
}
.jgtp-preset-chip.is-active {
    border-color: #059669 !important;
    background: #059669 !important;
    color: #ffffff !important;
    font-weight: 700 !important;
}
.jgtp-hours-grid {
    display: grid !important;
    grid-template-columns: repeat(6, minmax(0, 1fr)) !important;
    gap: 3px !important;
}
.jgtp-hour-btn {
    height: 26px !important;
    font-size: 10.5px !important;
    font-weight: 600 !important;
    border: 1px solid #e2e8f0 !important;
    border-radius: 4px !important;
    background: #f8fafc !important;
    color: #1e293b !important;
    cursor: pointer !important;
    transition: all 0.12s ease !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
}
.jgtp-hour-btn:hover {
    border-color: #059669 !important;
    background: #f0fdf4 !important;
    color: #059669 !important;
}
.jgtp-hour-btn.is-active {
    background: #059669 !important;
    color: #ffffff !important;
    border-color: #059669 !important;
    font-weight: 700 !important;
    box-shadow: 0 1px 4px rgba(0, 158, 247, 0.3) !important;
}
.jgtp-mins-grid {
    display: grid !important;
    grid-template-columns: repeat(4, minmax(0, 1fr)) !important;
    gap: 3px !important;
    margin-bottom: 3px !important;
}
.jgtp-min-btn {
    height: 26px !important;
    font-size: 10.5px !important;
    font-weight: 700 !important;
    border: 1px solid #e2e8f0 !important;
    border-radius: 4px !important;
    background: #f8fafc !important;
    color: #1e293b !important;
    cursor: pointer !important;
    transition: all 0.12s ease !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
}
.jgtp-min-btn:hover {
    border-color: #059669 !important;
    background: #f0fdf4 !important;
    color: #059669 !important;
}
.jgtp-min-btn.is-active {
    background: #059669 !important;
    color: #ffffff !important;
    border-color: #059669 !important;
    box-shadow: 0 1px 4px rgba(0, 158, 247, 0.3) !important;
}
.jgtp-mins-subgrid {
    display: grid !important;
    grid-template-columns: repeat(8, minmax(0, 1fr)) !important;
    gap: 2px !important;
}
.jgtp-min-subchip {
    padding: 2px 0 !important;
    height: 20px !important;
    font-size: 8.5px !important;
    font-weight: 500 !important;
    border: 1px solid #edf2f7 !important;
    border-radius: 3px !important;
    background: #ffffff !important;
    color: #64748b !important;
    cursor: pointer !important;
    text-align: center !important;
    transition: all 0.12s ease !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
}
.jgtp-min-subchip:hover {
    border-color: #059669 !important;
    color: #059669 !important;
    background: #f0fdf4 !important;
}
.jgtp-min-subchip.is-active {
    background: #059669 !important;
    color: #ffffff !important;
    border-color: #059669 !important;
    font-weight: 700 !important;
}
.jgtp-footer {
    flex: 0 0 auto !important;
    padding: 6px 10px !important;
    background: #f8fafc !important;
    border-top: 1px solid #e2e8f0 !important;
    display: flex !important;
    align-items: center !important;
    justify-content: space-between !important;
    gap: 6px !important;
    box-sizing: border-box !important;
}
.jgtp-btn {
    border: none !important;
    border-radius: 5px !important;
    cursor: pointer !important;
    font-family: 'Poppins', sans-serif !important;
    transition: all 0.12s ease !important;
}
.jgtp-btn-clear {
    background: transparent !important;
    color: #ef4444 !important;
    font-size: 10.5px !important;
    font-weight: 600 !important;
    padding: 4px 6px !important;
}
.jgtp-btn-clear:hover {
    background: #fef2f2 !important;
}
.jgtp-btn-now {
    background: #edf2f7 !important;
    color: #334155 !important;
    font-size: 10px !important;
    font-weight: 600 !important;
    padding: 4px 8px !important;
}
.jgtp-btn-now:hover {
    background: #e2e8f0 !important;
    color: #0f172a !important;
}
.jgtp-btn-apply {
    background: #059669 !important;
    color: #ffffff !important;
    font-size: 10.5px !important;
    font-weight: 700 !important;
    padding: 4px 12px !important;
}
.jgtp-btn-apply:hover {
    background: #0088d4 !important;
}
.candidate-slot-control input.is-picker-active {
    border-color: #059669 !important;
    box-shadow: 0 0 0 3px rgba(0, 158, 247, 0.15) !important;
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
    color: #059669;
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
    color: #059669;
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
    border-color: #10b981 !important;
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
    background: #a7f3d0 !important;
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
    color: #10b981 !important;
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
    border-color: #10b981 !important;
    box-shadow: 0 0 0 2px rgba(16, 185, 129, 0.15) !important;
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
    background-color: #ecfdf5 !important;
    color: #047857 !important;
}

.jero-cs-item.is-selected {
    background-color: #a7f3d0 !important;
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
    background: #a7f3d0 !important;
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
    display: none !important;
    border: none !important;
    outline: none !important;
    box-shadow: none !important;
    padding: 0 !important;
    margin: 0 !important;
}

.jero-disambiguation-bar.is-visible,
.jero-disambiguation-bar:not(:empty) {
    display: flex !important;
    flex-wrap: wrap !important;
    align-items: center !important;
    gap: 6px !important;
    margin-top: 6px !important;
    padding: 6px 10px !important;
    background: #f8fafc !important;
    border: none !important;
    border-radius: 6px !important;
    font-family: 'Poppins', sans-serif !important;
}

.jero-disambiguation-bar:empty {
    display: none !important;
    border: none !important;
    padding: 0 !important;
    margin: 0 !important;
}

.jero-disambiguation-badge {
    display: inline-flex !important;
    align-items: center !important;
    padding: 3px 8px !important;
    font-size: 11px !important;
    font-weight: 500 !important;
    color: #1e40af !important;
    background: #ecfdf5 !important;
    border: 1px solid #bfdbfe !important;
    border-radius: 4px !important;
    cursor: pointer !important;
    transition: all 0.15s ease !important;
}

.jero-disambiguation-badge:hover {
    background: #d1fae5 !important;
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

/* Metronic Full Name & Email Input Polish */
.candidate-field-control--interactive {
    position: relative;
    width: 100%;
}
.candidate-field-control--interactive:focus-within > i {
    color: #059669 !important;
    transition: color 0.15s ease;
}
.candidate-field-control--interactive > input {
    padding-right: 34px !important;
}
.candidate-field-clear {
    position: absolute;
    right: 10px;
    top: 50%;
    transform: translateY(-50%);
    width: 20px;
    height: 20px;
    padding: 0;
    margin: 0;
    border: none;
    border-radius: 50%;
    background: #e4e6ef;
    color: #7e8299;
    font-size: 14px;
    line-height: 1;
    cursor: pointer;
    z-index: 4;
    transition: all 0.15s ease;
    display: inline-flex;
    align-items: center;
    justify-content: center;
}
.candidate-field-clear:hover {
    background: #f1416c;
    color: #ffffff;
}
.candidate-email-typo-hint {
    margin-top: 5px;
    font-size: 11.5px;
    font-family: 'Poppins', sans-serif;
    color: #b45309;
    background: #fef3c7;
    border: 1px solid #fde68a;
    border-radius: 6px;
    padding: 5px 10px;
    display: none;
    align-items: center;
    gap: 6px;
}
.candidate-email-typo-hint a {
    color: #047857;
    font-weight: 600;
    text-decoration: underline;
    cursor: pointer;
}

/* ============================================================================
   JERO ATS - RECRUITER COMMENTS CATEGORIZED TEMPLATE DROPDOWN & EDITOR
   Client-First • 0% Server Strain • 100% In-Memory Browser RAM
   ============================================================================ */
.fs.rm-note-card,
.rm-note-card,
#recruiter_comments_section {
    overflow: visible !important;
    position: relative;
    z-index: 20;
    border: 1px solid #efd36b !important;
    border-radius: 8px !important;
    background: #ffffff !important;
    box-shadow: 0 4px 14px rgba(214, 158, 0, 0.06) !important;
    margin-bottom: 20px;
}
.rm-note-card.has-open-dropdown,
.rm-note-card:has(.custom-template-dropdown.is-open),
#recruiter_comments_section.has-open-dropdown,
#recruiter_comments_section:has(.custom-template-dropdown.is-open) {
    position: relative !important;
    z-index: 1060 !important;
}
.rm-note-card .candidate-card-header,
#recruiter_comments_section .candidate-card-header {
    min-height: 58px !important;
    padding: 12px 18px !important;
    border-bottom: 1px solid #f5e3a0 !important;
    border-top-left-radius: 7px !important;
    border-top-right-radius: 7px !important;
    background: linear-gradient(90deg, #fffbeb 0%, #ffffff 82%) !important;
    display: flex;
    align-items: center;
}
.rm-note-card .candidate-card-title,
#recruiter_comments_section .candidate-card-title {
    display: flex;
    align-items: center;
    gap: 12px;
}
.rm-note-card .candidate-card-icon--note,
#recruiter_comments_section .candidate-card-icon--note {
    width: 38px !important;
    height: 38px !important;
    min-width: 38px !important;
    flex-basis: 38px !important;
    border-radius: 8px !important;
    border: 0 !important;
    color: #071434 !important;
    background: #ffca0a !important;
    box-shadow: 0 4px 10px rgba(214, 158, 0, 0.22) !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
}
.rm-note-card .candidate-card-icon--note i,
#recruiter_comments_section .candidate-card-icon--note i {
    color: #071434 !important;
    opacity: 1 !important;
    font-size: 18px !important;
    line-height: 1 !important;
    display: inline-block !important;
}
.rm-note-card .candidate-card-title strong,
#recruiter_comments_section .candidate-card-title strong {
    color: #071434 !important;
    font-size: 14px !important;
    font-weight: 700 !important;
    display: block;
}
.rm-note-card .candidate-card-title small,
#recruiter_comments_section .candidate-card-title small {
    display: block !important;
    color: #65708e !important;
    font-size: 11px !important;
    line-height: 1.4;
}
.rm-note-card .candidate-card-body,
.rm-note-card .fsc,
#recruiter_comments_section .candidate-card-body,
#recruiter_comments_section .fsc {
    overflow: visible !important;
    position: relative;
    padding: 16px 20px !important;
    display: grid;
    gap: 14px;
}
.rm-note-card .recruiter-template-field,
#recruiter_comments_section .recruiter-template-field {
    overflow: visible !important;
    position: relative;
    z-index: 1062;
}
.custom-template-dropdown {
    position: relative !important;
    width: 100%;
    z-index: 1064;
}
.custom-template-dropdown.is-open {
    z-index: 1068 !important;
}
.custom-template-trigger {
    display: flex;
    align-items: center;
    justify-content: space-between;
    height: 44px;
    padding: 0 14px;
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.18s ease;
    user-select: none;
}
.custom-template-trigger:hover {
    border-color: #f59e0b;
    background: #fffdf5;
}
.custom-template-trigger:focus,
.custom-template-dropdown.is-open .custom-template-trigger {
    border-color: #f59e0b !important;
    box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.15) !important;
    outline: none;
    background: #ffffff;
}
.custom-template-trigger .trigger-left {
    display: flex;
    align-items: center;
    gap: 10px;
    min-width: 0;
    flex: 1;
}
.custom-template-trigger .trigger-icon {
    font-size: 15px;
    color: #d97706;
    flex-shrink: 0;
}
.custom-template-trigger .trigger-label {
    font-size: 13px;
    font-weight: 500;
    color: #1e293b;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    font-family: 'Poppins', sans-serif;
}
.custom-template-trigger .trigger-label.is-placeholder {
    color: #94a3b8;
}
.custom-template-trigger .trigger-arrow {
    font-size: 11px;
    color: #64748b;
    transition: transform 0.2s ease;
    flex-shrink: 0;
    margin-left: 8px;
}
.custom-template-trigger * {
    pointer-events: none;
}
.custom-template-dropdown.is-open .custom-template-trigger .trigger-arrow {
    transform: rotate(180deg);
    color: #d97706;
}
.custom-template-menu {
    display: none;
    position: absolute;
    top: calc(100% + 6px);
    left: 0;
    right: 0;
    z-index: 1080 !important;
    background: #ffffff !important;
    border: 1px solid #cbd5e1 !important;
    border-radius: 12px !important;
    box-shadow: 0 16px 36px rgba(15, 23, 42, 0.22), 0 4px 12px rgba(15, 23, 42, 0.1) !important;
    padding: 10px 12px;
    max-height: 400px;
    overflow-y: auto !important;
    scrollbar-width: thin;
    scrollbar-color: #cbd5e1 #f8fafc;
}
#custom_template_dropdown.is-open #custom_template_menu,
.custom-template-dropdown.is-open .custom-template-menu {
    display: block !important;
}
.custom-template-menu::-webkit-scrollbar {
    width: 6px;
}
.custom-template-menu::-webkit-scrollbar-track {
    background: #f8fafc;
    border-radius: 4px;
}
.custom-template-menu::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 4px;
}
.tpl-category-section {
    margin-bottom: 10px;
}
.tpl-category-section:last-child {
    margin-bottom: 2px;
}
.tpl-category-heading {
    display: flex;
    align-items: center;
    padding: 6px 12px;
    border-radius: 6px;
    font-size: 12px;
    font-weight: 700;
    letter-spacing: 0.2px;
    margin-bottom: 4px;
    font-family: 'Poppins', sans-serif;
}
.tpl-cat--amber {
    background: #fffbeb;
    color: #92400e;
}
.tpl-cat--blue {
    background: #ecfdf5;
    color: #047857;
}
.tpl-cat--green {
    background: #f0fdf4;
    color: #166534;
}
.tpl-cat--purple {
    background: #f5f3ff;
    color: #6d28d9;
}
.tpl-card-item * {
    pointer-events: none;
}
.tpl-card-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 8px 12px;
    margin-bottom: 3px;
    border: 1px solid transparent;
    border-radius: 8px;
    background: #ffffff;
    cursor: pointer;
    transition: all 0.15s ease-in-out;
}
.tpl-card-item:hover {
    background: #fffdf5;
    border-color: #fde68a;
}
.tpl-card-item.is-selected {
    background: #fefce8 !important;
    border-color: #fde68a !important;
}
.tpl-card-left {
    display: flex;
    align-items: center;
    gap: 12px;
    flex: 1;
    min-width: 0;
}
.tpl-card-icon {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 26px;
    height: 26px;
    color: #d97706;
    font-size: 15px;
    flex-shrink: 0;
}
.tpl-card-text {
    display: flex;
    flex-direction: column;
    min-width: 0;
}
.tpl-card-title {
    font-size: 13px;
    font-weight: 600;
    color: #0f172a;
    line-height: 1.35;
    font-family: 'Poppins', sans-serif;
}
.tpl-card-subtitle {
    font-size: 11.5px;
    color: #64748b;
    line-height: 1.35;
    margin-top: 1px;
    font-family: 'Poppins', sans-serif;
}
.tpl-card-check {
    display: none !important;
    align-items: center;
    justify-content: center;
    width: 22px;
    height: 22px;
    flex-shrink: 0;
    margin-left: 10px;
}
.tpl-card-check i {
    font-size: 16px;
    color: #d97706;
}
.tpl-card-item.is-selected .tpl-card-check {
    display: inline-flex !important;
}

/* Recruiter Note Editor & Counter */
.recruiter-editor-field textarea#recruiter_note {
    width: 100% !important;
    min-height: 160px !important;
    padding: 12px 14px !important;
    border: 1px solid #d1d5db !important;
    border-radius: 8px !important;
    background: #ffffff !important;
    font-family: 'Poppins', sans-serif !important;
    font-size: 13px !important;
    line-height: 1.6 !important;
    color: #1e293b !important;
    resize: vertical !important;
    box-sizing: border-box !important;
    transition: border-color 0.15s ease, box-shadow 0.15s ease !important;
}
.recruiter-editor-field textarea#recruiter_note:focus {
    border-color: #f59e0b !important;
    box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.15) !important;
    outline: none !important;
}
.recruiter-private-note {
    display: inline-flex !important;
    align-items: center !important;
    gap: 6px !important;
    background: #fffbeb !important;
    color: #92400e !important;
    border: 1px solid #fde68a !important;
    border-radius: 9999px !important;
    padding: 4px 10px !important;
    font-size: 11px !important;
    font-weight: 500 !important;
    font-family: 'Poppins', sans-serif !important;
}
.rm-comments-counter {
    color: #8790AC;
    font-size: 11.5px;
    font-weight: 500;
    font-family: 'Poppins', sans-serif;
}
#btn_sync_rm_template {
    background: #fffbeb !important;
    color: #b45309 !important;
    border: 1px solid #fde68a !important;
    border-radius: 6px !important;
    font-weight: 600 !important;
    transition: all 0.15s ease !important;
}
#btn_sync_rm_template:hover {
    background: #fef3c7 !important;
    color: #92400e !important;
    border-color: #fcd34d !important;
}
.field-attention-highlight {
    animation: rmFieldPulse 0.7s ease !important;
}
@keyframes rmFieldPulse {
    0% { background-color: #fff9db !important; }
    50% { background-color: #fef08a !important; }
    100% { background-color: transparent !important; }
}</style>
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
        if (isset($application->serving_notice_period_mode) && $application->serving_notice_period_mode !== null && $application->serving_notice_period_mode !== '') {
          $stored_snp_mode = (int) $application->serving_notice_period_mode;
          if (in_array($stored_snp_mode, [0, 1, 2], true)) {
            $snp_mode = $stored_snp_mode;
          }
        } elseif (!empty($application->np_id)) {
          if (is_numeric($application->np_id)) {
            $snp_mode = 0;
          } elseif (strtolower((string) $application->np_id) === 'immediate') {
            $snp_mode = 2;
          } else {
            $snp_mode = 1;
          }
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
    @php
        $currentAppMasterStatus = (int)(@$application->master_status_id ?? 8);
    @endphp
    @include('sections.service_company.toolbar', [
        'id' => @$jobDetail->id ?? 0,
        'hideLocationDate' => true,
        'showStage' => true,
        'stageId' => $currentAppMasterStatus,
    ])
    <div class="d-flex align-items-center py-1 candidate-header-actions">
      <span class="badge badge-light-success fw-bold fs-8 px-3 py-2 me-3 client-flow-pill" style="background:#ecfdf5;color:#059669;border:1px solid #a7f3d0;"><i class="fa-solid fa-briefcase me-1" style="color:#059669;"></i> Direct Client Flow</span>
      @if($page_event == 'EDIT')
        <button type="button" name="update" id="update-form" value="update" class="btn btn-primary talent-topbar-primary candidate-submit-action"><i class="fa-solid fa-pen-to-square" aria-hidden="true"></i><span>Update Candidate</span></button>
      @else
        <button type="button" name="save" id="save-form" value="save" class="btn btn-primary talent-topbar-primary candidate-submit-action"><i class="fa-solid fa-paper-plane" aria-hidden="true"></i><span>Submit Candidate</span></button>
      @endif
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
            <input type="hidden" name="current_master_status_id" id="current_master_status_id" value="{{ @$application->master_status_id ?? 8 }}" />
            <input type="hidden" name="master_status_id" id="master_status_id" value="{{ $currentAppMasterStatus }}" />
            <input type="hidden" name="AddJobWiseCondidateRankRow" id="AddJobWiseCondidateRankRow" value="{{ (@$AddJobWiseCondidateRankRow) ? $AddJobWiseCondidateRankRow : '' }}">
            <input type="hidden" name="percentage_hike" id="percentage_hike" value="{{ @$application->percentage_hike }}">
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
                @php
                    $displayResumeName = '';
                    $hasExistingResume = false;
                    if (!empty($application->resume)) {
                        preg_match('/([^\/\\\\]+\.(pdf|doc|docx|htm|html))/i', $application->resume, $_rm);
                        $displayResumeName = $_rm[1] ?? basename($application->resume);
                        if (!$displayResumeName || !pathinfo($displayResumeName, PATHINFO_EXTENSION)) {
                            $displayResumeName = 'Resume (uploaded)';
                        }
                        $hasExistingResume = true;
                    } elseif (!empty($resume_url)) {
                        $hasExistingResume = true;
                        $displayResumeName = 'Resume (uploaded)';
                    }
                    $displayResumeExt = strtoupper(pathinfo($displayResumeName, PATHINFO_EXTENSION) ?: 'PDF');
                    $isWordDoc = in_array($displayResumeExt, ['DOC', 'DOCX']);
                @endphp
                <div class="fs fv-row resume-card candidate-card {{ $hasExistingResume ? 'has-uploaded-resume' : '' }}" style="margin-bottom:0">
                    <div class="fsh resume-card-header candidate-card-header">
                        <div class="candidate-card-title">
                          <span class="resume-card-icon candidate-card-icon"><i class="fa-solid fa-file-arrow-up" aria-hidden="true"></i></span>
                          <span><strong>Upload Resume <span class="rq">*</span></strong><small>Upload a PDF or Word resume to parse candidate details.</small></span>
                        </div>
                    </div>

                    <div class="fsc resume-card-body candidate-card-body">
                        <div class="ua {{ $hasExistingResume ? 'd-none' : '' }}" id="uploadBox" style="{{ $hasExistingResume ? 'display:none !important;' : '' }}">

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

                            <!-- Hidden legacy fileInfo strip to avoid DOM errors in older scripts -->
                            <div class="file-info" id="fileInfo" style="display:none !important;">
                                <span class="file-name" id="fileName">{{ $displayResumeName }}</span>
                                <button type="button" class="remove-file" id="removeFile" style="display:none;">Remove</button>
                            </div>

                        </div>

                            <!-- ==========================
                                 After-upload Resume Information card
                                 Outside #uploadBox so hiding the dropzone
                                 does not hide this card.
                                 ========================== -->
                            <div class="up-info {{ $hasExistingResume ? 'show' : '' }}" id="upInfo" style="{{ $hasExistingResume ? 'display:flex !important;' : '' }}">
                                <div class="up-info-banner">
                                    <div class="up-info-banner-left">
                                        <span class="up-info-banner-check">
                                            <i class="fa-solid fa-check" aria-hidden="true"></i>
                                        </span>
                                        <div class="up-info-banner-text">
                                            <strong id="upInfoBannerTitle">{{ $page_event == 'EDIT' ? 'Resume attached successfully' : 'Resume uploaded successfully!' }}</strong>
                                            <small id="upInfoBannerSub">{{ $page_event == 'EDIT' ? 'Uploaded resume is attached and parsed.' : 'Your resume has been uploaded and parsed.' }}</small>
                                        </div>
                                    </div>
                                    <button type="button" class="up-info-banner-close" aria-label="Dismiss banner">
                                        <i class="fa-solid fa-xmark" aria-hidden="true"></i>
                                    </button>
                                </div>

                                <div class="up-file-card">
                                    <div class="up-file-card-left">
                                        <div class="up-file-badge" style="{{ $isWordDoc ? 'background:#d1fae5 !important; color:#059669 !important;' : 'background:#fee2e2 !important; color:#dc2626 !important;' }}">
                                            <i class="{{ $isWordDoc ? 'fa-regular fa-file-word' : 'fa-regular fa-file-pdf' }}" aria-hidden="true" style="{{ $isWordDoc ? 'color:#059669 !important;' : 'color:#dc2626 !important;' }}"></i>
                                            <span class="up-file-badge-ext" id="upFileBadgeExt" style="{{ $isWordDoc ? 'color:#059669 !important;' : 'color:#dc2626 !important;' }}">{{ $displayResumeExt }}</span>
                                        </div>
                                        <div class="up-file-meta">
                                            <div class="up-file-name" id="upFileName" title="{{ $displayResumeName }}">{{ $displayResumeName ?: '&nbsp;' }}</div>
                                            <div class="up-file-sub" id="upFileSub">{{ $hasExistingResume ? ('Uploaded Resume • ' . $displayResumeExt) : '&nbsp;' }}</div>
                                        </div>
                                    </div>
                                    <div class="up-file-actions">
                                        @if(!empty($resume_url))
                                        <a href="{{ $resume_url }}"
                                           target="_blank"
                                           class="up-btn up-btn-replace download-file"
                                           id="upDownloadBtn"
                                           title="Download Resume">
                                            <i class="fa-solid fa-download" aria-hidden="true"></i>
                                            <span>Download</span>
                                        </a>
                                        @endif
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
                                                <div class="up-detail-val" id="upDetailUploadedOn">{{ !empty($application->created_at) ? \Carbon\Carbon::parse($application->created_at)->format('d M Y, H:i') : ($hasExistingResume ? now()->format('d M Y, H:i') : '&nbsp;') }}</div>
                                            </div>
                                        </div>
                                        <div class="up-detail-tile">
                                            <span class="up-detail-icon">
                                                <i class="fa-regular fa-square-check" aria-hidden="true"></i>
                                            </span>
                                            <div>
                                                <div class="up-detail-lbl">Parser Status</div>
                                                <div class="up-detail-val" id="upDetailParserStatus">
                                                    @if($hasExistingResume)
                                                        <span style="color: #059669; font-weight: 700;">✓ Parsed</span>
                                                    @else
                                                        &nbsp;
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        <div class="up-detail-tile">
                                            <span class="up-detail-icon">
                                                <i class="fa-regular fa-file" aria-hidden="true"></i>
                                            </span>
                                            <div>
                                                <div class="up-detail-lbl">File Type</div>
                                                <div class="up-detail-val" id="upDetailFileType">{{ $hasExistingResume ? $displayResumeExt : '&nbsp;' }}</div>
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
                    <label for="full_name">Full Name <span class="rq">*</span></label>
                    <div class="candidate-field-control candidate-field-control--interactive"><i class="fa-regular fa-user" aria-hidden="true"></i><input type="text" class="form-control form-control-solid" name="full_name" id="full_name" value="{{ @$application->full_name }}" placeholder="Enter full name" maxlength="191" autocomplete="name" autocapitalize="words" spellcheck="false" aria-required="true" /><button type="button" class="candidate-field-clear" title="Clear name" tabindex="-1" style="display: none;">&times;</button></div>
                  </div>
                  <div class="fg fv-row">
                    <label for="email">Primary Email <span class="rq">*</span></label>
                    <div class="candidate-field-control candidate-field-control--interactive"><i class="fa-regular fa-envelope" aria-hidden="true"></i><input type="email" class="form-control form-control-solid" name="email" id="email" value="{{ @$application->email }}" placeholder="Enter primary email address" maxlength="191" inputmode="email" autocomplete="email" autocapitalize="none" spellcheck="false" aria-required="true" /><button type="button" class="candidate-field-clear" title="Clear email" tabindex="-1" style="display: none;">&times;</button></div>
                    <div id="email-typo-hint" class="candidate-email-typo-hint" style="display: none;"></div>
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
                    <label>Date of Birth <span class="text-muted fw-normal" style="font-size:11px;font-weight:400;">(Optional)</span></label>
                    <div class="candidate-field-control"><i class="fa-regular fa-calendar" aria-hidden="true"></i><input type="date" name="dob" id="dob" value="{{ @$application->dob }}" onclick="this.showPicker()" placeholder="Select date" /></div>
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
                    {{-- Disambiguation Ribbon for NEEDS_CONFIRMATION State (Border Strictly Removed) --}}
                    <div id="jero-location-disambiguation" class="jero-disambiguation-bar" style="display:none !important; border:none !important;"></div>
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
                </div>

                <details class="candidate-secondary-details" {{ (!empty(@$application->secondrary_email) || !empty(@$application->secondary_mobile)) ? 'open' : '' }}>
                  <summary>
                    <span class="candidate-secondary-summary"><span class="candidate-secondary-icon"><i class="fa-solid fa-ellipsis" aria-hidden="true"></i></span><span><strong>Additional Secondary Contact</strong><small>Optional secondary email and contact number.</small></span></span>
                    <span class="candidate-secondary-toggle"><span class="candidate-secondary-show">Show Details</span><span class="candidate-secondary-hide">Hide Details</span><i class="fa-solid fa-chevron-down" aria-hidden="true"></i></span>
                  </summary>
                  <div class="candidate-secondary-grid">
                    <div class="fg fv-row">
                      <label for="secondrary_email">Secondary Email</label>
                      <div class="candidate-field-control candidate-field-control--interactive"><i class="fa-regular fa-envelope" aria-hidden="true"></i><input type="email" class="form-control form-control-solid" name="secondrary_email" id="secondrary_email" value="{{ @$application->secondrary_email }}" placeholder="Enter secondary email" maxlength="55" inputmode="email" autocomplete="email" autocapitalize="none" spellcheck="false" /><button type="button" class="candidate-field-clear" title="Clear email" tabindex="-1" style="display: none;">&times;</button></div>
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
                        $companyId = $company_id ?? (auth()->check() ? auth()->user()->company_id : null);
                        if (empty($companyId) && isset($jobDetail) && !empty($jobDetail->company_id)) {
                            $companyId = $jobDetail->company_id;
                        }
                        $tenantDefaultCode = $tenant_currency ?? \App\Helper\GeneralMethod::defaultCurrency($companyId);
                        $tenantDefaultKey  = $tenant_currency_key ?? \App\Helper\GeneralMethod::defaultCurrencyKey($companyId);

                        // If editing an existing candidate with saved currency, honor it (RULE-TENANT-001).
                        // Otherwise, default strictly to tenant's configured currency from settings page.
                        $selectedCurKey = null;
                        if (isset($application) && isset($application->current_ctc_cur) && $application->current_ctc_cur !== null && $application->current_ctc_cur !== '') {
                            $selectedCurKey = (int) $application->current_ctc_cur;
                        } elseif (isset($current_ctc_cur) && $current_ctc_cur !== null && $current_ctc_cur !== '') {
                            $selectedCurKey = (int) $current_ctc_cur;
                        } else {
                            $selectedCurKey = (int) $tenantDefaultKey;
                        }

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
                            $sel = ((int)$key === (int)$selectedCurKey) ? 'selected="selected"' : '';
                            $cMeta = \App\Helper\GeneralMethod::currencyEnterpriseMeta($val);
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
                              $sel = ((int)$key === (int)$selectedCurKey) ? 'selected="selected"' : '';
                              echo '<option value="'.$key.'" data-code="'.e($val).'" '.$sel.'>'.$val.'</option>';
                          }
                      @endphp
                    </select>
                    <select name="current_th_cur" id="current_th_cur">
                      @php
                          foreach($currency as $key=>$val){
                              $sel = ((int)$key === (int)$selectedCurKey) ? 'selected="selected"' : '';
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
                      <span class="candidate-notice-option-copy"><strong>Serving Notice Period (SNP)</strong><small>Currently working, serving notice</small></span>
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
                    <option value="1" {{ $snp_mode === 1 ? 'selected' : '' }}>Serving Notice Period (SNP)</option>
                    <option value="2" {{ $snp_mode === 2 ? 'selected' : '' }}>Immediate Joiner</option>
                  </select>
                </div>

                <div class="candidate-notice-details-grid">
                  <div class="fg fv-row candidate-notice-detail" id="candidateNoticeDateField" {{ !in_array($snp_mode, [1, 2], true) ? 'hidden' : '' }}>
                    <label id="candidateNoticeDateLabel">{{ $snp_mode === 1 ? 'Expected Last Working Date (LWD)' : 'Last Working Date (LWD)' }} <span class="rq">*</span></label>
                    <div class="candidate-notice-input-shell">
                      <i class="fa-regular fa-calendar" aria-hidden="true"></i>
                      <div class="candidate-date-picker">
                        <input type="text" id="np_id_display" class="form-control form-control-solid" name="np_id_display" value="{{ $snp_last_working ? date('d/M/Y', strtotime($snp_last_working)) : '' }}" placeholder="{{ $snp_mode === 1 ? 'Select expected last working date (LWD)' : 'Select last working date (LWD)' }}" readonly {{ !in_array($snp_mode, [1, 2], true) ? 'disabled' : '' }}>
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
                    <label>Reason for Job Change <span class="candidate-optional-label">(Optional)</span></label>
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
                  <label class="candidate-work-mode-option" for="wo"><input type="radio" name="wm" id="wo" {{ (@$application->wm == 'office' || empty(@$application->wm)) ? 'checked' : '' }} value="office" /><span class="candidate-work-mode-radio"></span><span class="candidate-work-mode-icon candidate-work-mode-icon--office"><i class="fa-solid fa-building" aria-hidden="true"></i></span><span class="candidate-work-mode-copy"><strong>Work From Office (WFO)</strong><small>Office location based work.</small></span></label>
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
                      <label class="candidate-slot-field"><span><i class="fa-regular fa-clock" aria-hidden="true"></i> Time <b>*</b></span><span class="candidate-slot-control"><input class="form-control form-control-solid jero-god-time-input" placeholder="Select time" name="interviewTimeOne" id="interviewTimeOne" value="{{ (@$application->interviewDateOne) ? \Carbon\Carbon::parse(@$application->interviewDateOne)->format('h:i A'): '' }}" type="text" autocomplete="off" readonly style="cursor: pointer;"></span></label>
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
                      <label class="candidate-slot-field"><span><i class="fa-regular fa-clock" aria-hidden="true"></i> Time <b>*</b></span><span class="candidate-slot-control"><input class="form-control form-control-solid jero-god-time-input" placeholder="Select time" name="interviewTimeTwo" id="interviewTimeTwo" value="{{ (@$application->interviewDateTwo) ? \Carbon\Carbon::parse(@$application->interviewDateTwo)->format('h:i A') : '' }}" type="text" autocomplete="off" readonly style="cursor: pointer;"></span></label>
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
                      <label class="candidate-slot-field"><span><i class="fa-regular fa-clock" aria-hidden="true"></i> Time <b>*</b></span><span class="candidate-slot-control"><input class="form-control form-control-solid jero-god-time-input" placeholder="Select time" name="interviewTimeThree" id="interviewTimeThree" value="{{ (@$application->interviewDateThree) ? \Carbon\Carbon::parse(@$application->interviewDateThree)->format('h:i A') : '' }}" type="text" autocomplete="off" readonly style="cursor: pointer;"></span></label>
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
            <div class="fs candidate-card additional-details-card addl-card addl-card--details">
              <div class="fsh candidate-card-header addl-hero">
                <div class="candidate-card-title addl-hero-title">
                  <span class="candidate-card-icon addl-hero-icon"><i class="fa-solid fa-passport" aria-hidden="true"></i></span>
                  <span><strong>Visa Status & Work Authorization (Optional)</strong><small>Specify legal right-to-work status and visa category for client submissions.</small></span>
                </div>
              </div>
              <div class="fsc candidate-card-body addl-body">
                <div class="addl-wide-field fv-row" style="width: 100%;">
                  {{-- Quick Select Hub Chips --}}
                  <div class="wa-popular-chips-wrap">
                    <span class="wa-chips-label"><i class="fa-solid fa-bolt-lightning text-warning me-1"></i> Popular Hubs:</span>
                    <div class="wa-popular-chips" role="group" aria-label="Quick Select Work Authorization Hubs">
                      <button type="button" class="wa-chip-btn" data-region="United States" data-filter="United States" title="Filter United States Visas">🇺🇸 US</button>
                      <button type="button" class="wa-chip-btn" data-region="Canada" data-filter="Canada" title="Filter Canada Visas">🇨🇦 Canada</button>
                      <button type="button" class="wa-chip-btn" data-region="India" data-filter="India" title="Filter India Work Status">🇮🇳 India</button>
                      <button type="button" class="wa-chip-btn" data-region="United Kingdom" data-filter="United Kingdom" title="Filter UK Visas">🇬🇧 UK</button>
                      <button type="button" class="wa-chip-btn" data-region="Dubai" data-filter="UAE" title="Filter Dubai & UAE Visas">🇦🇪 Dubai / UAE</button>
                      <button type="button" class="wa-chip-btn" data-region="Singapore" data-filter="Singapore" title="Filter Singapore Visas">🇸🇬 Singapore</button>
                      <button type="button" class="wa-chip-btn" data-region="Australia" data-filter="Australia" title="Filter Australia & NZ Visas">🇦🇺 Australia</button>
                      <button type="button" class="wa-chip-btn" data-region="Germany" data-filter="Germany" title="Filter Germany Visas">🇩🇪 Germany</button>
                      <button type="button" class="wa-chip-btn" data-region="Global" data-filter="Global" title="Filter EU & Global Visas">🌐 Global</button>
                    </div>
                  </div>

                  {{-- Select Wrapper --}}
                  <div class="addl-select-wrap">
                    @php
                      $currentVisa  = trim((string) @$application->visastatu);
                      $isUsCitizen  = in_array($currentVisa, ['Citizen', 'US Citizen', 'US Citizen (USC)']);
                      $isGreenCard  = in_array($currentVisa, ['Green Card', 'Permanent Resident (Green Card)']);
                      $isGcEad      = in_array($currentVisa, ['GC-EAD', 'GC EAD', 'Green Card EAD']);
                      $isH1b        = in_array($currentVisa, ['H1B', 'H-1B', 'H1-B']);
                      $isH4Ead      = in_array($currentVisa, ['H4-EAD', 'H4 EAD', 'H-4 EAD']);
                      $isL2Ead      = in_array($currentVisa, ['L2-EAD', 'L2 EAD', 'L-2 EAD']);
                      $isTn         = in_array($currentVisa, ['TN Permit', 'TN Visa', 'TN-1', 'TN-2']);
                      $isOpt        = in_array($currentVisa, ['F1', 'OPT', 'OPT-EAD', 'STEM-OPT', 'OPT / STEM-OPT']);
                      $isCpt        = in_array($currentVisa, ['CPT']);
                      $isE3         = in_array($currentVisa, ['E-3', 'E3']);
                      $isO1         = in_array($currentVisa, ['O-1', 'O1']);
                      $isL1         = in_array($currentVisa, ['L1', 'L-1']);
                      $isOtherUsEad = in_array($currentVisa, ['Other US EAD', 'TPS EAD', 'Asylee EAD']);

                      $isCaCitizen  = in_array($currentVisa, ['Canadian Citizen']);
                      $isCaPr       = in_array($currentVisa, ['Canada PR', 'Canadian Permanent Resident']);
                      $isCaOpenWp   = in_array($currentVisa, ['Canada Open Work Permit', 'PGWP']);
                      $isCaClosedWp = in_array($currentVisa, ['Canada Closed Work Permit', 'LMIA Work Permit']);

                      $isInCitizen  = in_array($currentVisa, ['Indian Citizen']);
                      $isInOci      = in_array($currentVisa, ['OCI / PIO', 'OCI', 'PIO']);
                      $isInVisa     = in_array($currentVisa, ['India Work Visa', 'Employment Visa (India)']);

                      $isUkCitizen  = in_array($currentVisa, ['UK Citizen', 'British Citizen']);
                      $isUkIlr      = in_array($currentVisa, ['UK ILR', 'Indefinite Leave to Remain']);
                      $isUkSkilled  = in_array($currentVisa, ['UK Skilled Worker', 'Tier 2']);
                      $isUkGrad     = in_array($currentVisa, ['UK Graduate Visa']);

                      $isUaeGolden    = in_array($currentVisa, ['UAE Golden Visa', 'UAE Golden / Green Visa', 'Golden Visa']);
                      $isUaeGreen     = in_array($currentVisa, ['UAE Green Visa', 'Green Visa']);
                      $isUaeEmpVisa   = in_array($currentVisa, ['UAE Employment Visa', 'UAE Work Visa', 'Dubai Employment Visa']);
                      $isUaeFreelance = in_array($currentVisa, ['UAE Freelance Visa', 'Dubai Freelance Visa', 'Remote Work Visa (UAE)']);
                      $isUaeNational  = in_array($currentVisa, ['UAE National', 'UAE Citizen', 'Emirati Citizen']);
                      $isGccIqama     = in_array($currentVisa, ['GCC Iqama', 'Saudi Iqama', 'GCC Work Permit']);

                      $isSgCitizen    = in_array($currentVisa, ['Singapore Citizen']);
                      $isSgPr         = in_array($currentVisa, ['Singapore PR', 'Singapore Permanent Resident']);
                      $isSgEp         = in_array($currentVisa, ['Singapore EP', 'Employment Pass', 'Singapore Employment Pass']);
                      $isSgSpass      = in_array($currentVisa, ['Singapore S Pass', 'S Pass', 'S-Pass']);
                      $isSgOnePass    = in_array($currentVisa, ['Singapore ONE Pass', 'ONE Pass', 'Tech.Pass', 'PEP']);
                      $isSgLocDp      = in_array($currentVisa, ['Singapore LOC / DP', 'LOC', 'Dependant Pass']);

                      $isAuCitizenPr  = in_array($currentVisa, ['Australia Citizen / PR', 'Australian Citizen', 'Australian PR']);
                      $isAuTss482     = in_array($currentVisa, ['Australia TSS 482', 'TSS 482', 'Subclass 482']);
                      $isAuSkilled    = in_array($currentVisa, ['Australia Skilled 189/190', 'Subclass 189', 'Subclass 190']);
                      $isAuGrad485    = in_array($currentVisa, ['Australia Graduate 485', 'Subclass 485']);
                      $isNzCitizen    = in_array($currentVisa, ['New Zealand Citizen / PR', 'NZ Citizen', 'NZ PR']);

                      $isEuCitizen    = in_array($currentVisa, ['EU Citizen', 'EU / EEA Citizen']);
                      $isEuBlue       = in_array($currentVisa, ['EU Blue Card']);
                      $isAuthWp       = in_array($currentVisa, ['Authorized Work Permit']);
                      $isReqSpons     = in_array($currentVisa, ['Requires Sponsorship', 'Requires International Visa Sponsorship']);

                      $hasMatchedOption = $isUsCitizen || $isGreenCard || $isGcEad || $isH1b || $isH4Ead || $isL2Ead || $isTn || $isOpt || $isCpt || $isE3 || $isO1 || $isL1 || $isOtherUsEad
                                       || $isCaCitizen || $isCaPr || $isCaOpenWp || $isCaClosedWp
                                       || $isInCitizen || $isInOci || $isInVisa
                                       || $isUkCitizen || $isUkIlr || $isUkSkilled || $isUkGrad
                                       || $isUaeGolden || $isUaeGreen || $isUaeEmpVisa || $isUaeFreelance || $isUaeNational || $isGccIqama
                                       || $isSgCitizen || $isSgPr || $isSgEp || $isSgSpass || $isSgOnePass || $isSgLocDp
                                       || $isAuCitizenPr || $isAuTss482 || $isAuSkilled || $isAuGrad485 || $isNzCitizen
                                       || $isEuCitizen || $isEuBlue || $isAuthWp || $isReqSpons;

                      // Load comprehensive 240+ countries taxonomy
                      $allGlobalCountries = !empty($countries) ? (is_array($countries) ? $countries : $countries->toArray()) : [];
                      if (empty($allGlobalCountries) && file_exists(base_path('country-state-city/countries.json'))) {
                          $allGlobalCountries = json_decode(file_get_contents(base_path('country-state-city/countries.json')), true)['countries'] ?? [];
                      }
                      if (!empty($allGlobalCountries)) {
                          usort($allGlobalCountries, function($a, $b) {
                              return strcmp($a['name'] ?? '', $b['name'] ?? '');
                          });
                      }
                    @endphp
                    {{-- CRITICAL: Use class="wa-select" ONLY. NEVER include class="form-select" --}}
                    <select name="visastatu" id="visastatu" class="wa-select" data-placeholder="Search visa / right-to-work or country (e.g. H-1B, UAE Golden Visa, PR, Citizen)...">
                      <option value="">Select work authorization or visa status</option>

                      {{-- POPULAR HUBS (TIER 1 RECRUITING DESTINATIONS) --}}
                      <optgroup label="United States (US)">
                        <option value="US Citizen" {{ $isUsCitizen ? 'selected' : '' }}>US Citizen (USC)</option>
                        <option value="Green Card" {{ $isGreenCard ? 'selected' : '' }}>Permanent Resident (Green Card)</option>
                        <option value="GC-EAD" {{ $isGcEad ? 'selected' : '' }}>GC-EAD (Adjustment of Status)</option>
                        <option value="H1B" {{ $isH1b ? 'selected' : '' }}>H-1B (Specialty Occupation)</option>
                        <option value="H-4 EAD" {{ $isH4Ead ? 'selected' : '' }}>H-4 EAD (Spouse Dependent)</option>
                        <option value="L-2 EAD" {{ $isL2Ead ? 'selected' : '' }}>L-2 EAD (Intracompany Dependent)</option>
                        <option value="TN Permit" {{ $isTn ? 'selected' : '' }}>TN Visa (USMCA - Canada / Mexico)</option>
                        <option value="OPT-EAD" {{ $isOpt ? 'selected' : '' }}>OPT / STEM-OPT EAD</option>
                        <option value="CPT" {{ $isCpt ? 'selected' : '' }}>CPT (Curricular Practical Training)</option>
                        <option value="E-3" {{ $isE3 ? 'selected' : '' }}>E-3 (Australian Specialty Occupation)</option>
                        <option value="O-1" {{ $isO1 ? 'selected' : '' }}>O-1 (Extraordinary Ability)</option>
                        <option value="L1" {{ $isL1 ? 'selected' : '' }}>L-1 (Intracompany Transferee)</option>
                        <option value="Other US EAD" {{ $isOtherUsEad ? 'selected' : '' }}>Other US EAD (TPS / Asylee / DACA)</option>
                      </optgroup>

                      <optgroup label="Canada (CA)">
                        <option value="Canadian Citizen" {{ $isCaCitizen ? 'selected' : '' }}>Canadian Citizen</option>
                        <option value="Canada PR" {{ $isCaPr ? 'selected' : '' }}>Canada Permanent Resident (PR)</option>
                        <option value="Canada Open Work Permit" {{ $isCaOpenWp ? 'selected' : '' }}>Canada Open Work Permit / PGWP</option>
                        <option value="Canada Closed Work Permit" {{ $isCaClosedWp ? 'selected' : '' }}>Canada LMIA / Closed Work Permit</option>
                      </optgroup>

                      <optgroup label="India (IN)">
                        <option value="Indian Citizen" {{ $isInCitizen ? 'selected' : '' }}>Indian Citizen / Domestic Right to Work</option>
                        <option value="OCI / PIO" {{ $isInOci ? 'selected' : '' }}>OCI / PIO Cardholder</option>
                        <option value="India Work Visa" {{ $isInVisa ? 'selected' : '' }}>India Employment Visa</option>
                      </optgroup>

                      <optgroup label="United Kingdom (UK)">
                        <option value="UK Citizen" {{ $isUkCitizen ? 'selected' : '' }}>British Citizen</option>
                        <option value="UK ILR" {{ $isUkIlr ? 'selected' : '' }}>UK Indefinite Leave to Remain (ILR)</option>
                        <option value="UK Skilled Worker" {{ $isUkSkilled ? 'selected' : '' }}>UK Skilled Worker / Tier 2</option>
                        <option value="UK Graduate Visa" {{ $isUkGrad ? 'selected' : '' }}>UK Graduate / Youth Mobility</option>
                      </optgroup>

                      <optgroup label="United Arab Emirates & GCC (Dubai / UAE)">
                        <option value="UAE Golden Visa" {{ $isUaeGolden ? 'selected' : '' }}>UAE Golden Visa (10-Year Self-Sponsored)</option>
                        <option value="UAE Green Visa" {{ $isUaeGreen ? 'selected' : '' }}>UAE Green Visa (5-Year Skilled / Freelance)</option>
                        <option value="UAE Employment Visa" {{ $isUaeEmpVisa ? 'selected' : '' }}>UAE Employment Residence Visa (Company / Freezone)</option>
                        <option value="UAE Freelance Visa" {{ $isUaeFreelance ? 'selected' : '' }}>UAE Freelance / Remote Work Visa</option>
                        <option value="UAE National" {{ $isUaeNational ? 'selected' : '' }}>UAE / GCC National</option>
                        <option value="GCC Iqama" {{ $isGccIqama ? 'selected' : '' }}>GCC Resident Work Permit / Iqama</option>
                      </optgroup>

                      <optgroup label="Singapore (SG)">
                        <option value="Singapore Citizen" {{ $isSgCitizen ? 'selected' : '' }}>Singapore Citizen (SC)</option>
                        <option value="Singapore PR" {{ $isSgPr ? 'selected' : '' }}>Singapore Permanent Resident (PR)</option>
                        <option value="Singapore EP" {{ $isSgEp ? 'selected' : '' }}>Singapore Employment Pass (EP)</option>
                        <option value="Singapore S Pass" {{ $isSgSpass ? 'selected' : '' }}>Singapore S Pass (Skilled Staff)</option>
                        <option value="Singapore ONE Pass" {{ $isSgOnePass ? 'selected' : '' }}>Singapore ONE Pass / Tech.Pass / PEP</option>
                        <option value="Singapore LOC / DP" {{ $isSgLocDp ? 'selected' : '' }}>Singapore Letter of Consent (LOC / Dependant Pass)</option>
                      </optgroup>

                      <optgroup label="Australia & New Zealand (AU / NZ)">
                        <option value="Australia Citizen / PR" {{ $isAuCitizenPr ? 'selected' : '' }}>Australian Citizen / Permanent Resident</option>
                        <option value="Australia TSS 482" {{ $isAuTss482 ? 'selected' : '' }}>Australia TSS Visa (Subclass 482 - Temporary Skill)</option>
                        <option value="Australia Skilled 189/190" {{ $isAuSkilled ? 'selected' : '' }}>Australia Skilled Work Visa (Subclass 189 / 190)</option>
                        <option value="Australia Graduate 485" {{ $isAuGrad485 ? 'selected' : '' }}>Australia Temporary Graduate Visa (Subclass 485)</option>
                        <option value="New Zealand Citizen / PR" {{ $isNzCitizen ? 'selected' : '' }}>New Zealand Citizen / Resident</option>
                      </optgroup>

                      <optgroup label="European Union (EU)">
                        <option value="EU Citizen" {{ $isEuCitizen ? 'selected' : '' }}>EU / EEA Citizen</option>
                        <option value="EU Blue Card" {{ $isEuBlue ? 'selected' : '' }}>EU Blue Card</option>
                        <option value="Germany - Opportunity Card / Work Visa" {{ ($currentVisa === 'Germany - Opportunity Card / Work Visa') ? 'selected' : '' }}>Germany - Opportunity Card / Work Visa</option>
                        <option value="Netherlands - Highly Skilled Migrant" {{ ($currentVisa === 'Netherlands - Highly Skilled Migrant') ? 'selected' : '' }}>Netherlands - Highly Skilled Migrant</option>
                        <option value="Ireland - Critical Skills Employment Permit" {{ ($currentVisa === 'Ireland - Critical Skills Employment Permit') ? 'selected' : '' }}>Ireland - Critical Skills Employment Permit</option>
                        <option value="Authorized Work Permit" {{ $isAuthWp ? 'selected' : '' }}>Authorized Work Permit (Country Specific)</option>
                        <option value="Requires Sponsorship" {{ $isReqSpons ? 'selected' : '' }}>Requires International Visa Sponsorship</option>
                      </optgroup>

                      {{-- 240+ COUNTRIES & TERRITORIES WORLDWIDE --}}
                      @if(!empty($allGlobalCountries))
                        @foreach($allGlobalCountries as $countryItem)
                          @php
                            $cName = trim($countryItem['name'] ?? '');
                            $cCode = trim($countryItem['sortname'] ?? '');
                            if (empty($cName)) continue;
                            if (in_array($cCode, ['US', 'CA', 'IN', 'GB', 'AE', 'SG', 'AU', 'NZ'])) continue;

                            $optCitizen = $cName . ' - Citizen / Domestic Right to Work';
                            $optPr      = $cName . ' - Permanent Resident (PR)';
                            $optVisa    = $cName . ' - Work Visa / Employment Permit';

                            $isOptCitizenSelected = ($currentVisa === $optCitizen);
                            $isOptPrSelected      = ($currentVisa === $optPr);
                            $isOptVisaSelected    = ($currentVisa === $optVisa);

                            if ($isOptCitizenSelected || $isOptPrSelected || $isOptVisaSelected) {
                                $hasMatchedOption = true;
                            }
                          @endphp
                          <optgroup label="{{ $cName }} ({{ $cCode }})">
                            <option value="{{ $optCitizen }}" {{ $isOptCitizenSelected ? 'selected' : '' }}>{{ $optCitizen }}</option>
                            <option value="{{ $optPr }}" {{ $isOptPrSelected ? 'selected' : '' }}>{{ $optPr }}</option>
                            <option value="{{ $optVisa }}" {{ $isOptVisaSelected ? 'selected' : '' }}>{{ $optVisa }}</option>
                          </optgroup>
                        @endforeach
                      @endif

                      <optgroup label="Global & International">
                        <option value="Requires International Visa Sponsorship" {{ ($currentVisa === 'Requires International Visa Sponsorship') ? 'selected' : '' }}>Requires International Visa Sponsorship</option>
                        <option value="Global Remote / Freelance Contract" {{ ($currentVisa === 'Global Remote / Freelance Contract') ? 'selected' : '' }}>Global Remote / Freelance Contract Authorization</option>
                      </optgroup>

                      @if(!empty($currentVisa) && !$hasMatchedOption)
                        <option value="{{ $currentVisa }}" selected>{{ $currentVisa }} (Current Status)</option>
                      @endif
                    </select>
                  </div>
                  <small class="text-muted mt-1 d-block" style="font-size:11px;color:#65708e;">Search by country name (e.g. Mexico, Japan, Germany), visa code (H1B, TN, EAD, Golden Visa, EP, TSS 482) or right-to-work status.</small>
                </div>
              </div>
            </div>

            <!-- Recruiter Comments -->
            <div class="fs candidate-card recruiter-note-card rm-note-card" id="recruiter_comments_section">
              <div class="fsh candidate-card-header">
                <div class="candidate-card-title">
                  <span class="candidate-card-icon candidate-card-icon--note"><i class="fa-solid fa-note-sticky" aria-hidden="true"></i></span>
                  <span><strong>Recruiter Comments</strong><small>Add internal context and evaluation notes for the hiring team; this is not shared with the candidate.</small></span>
                </div>
              </div>
              <div class="fsc candidate-card-body">
                @php
                  // Categorize Add Candidate master templates into Enterprise categories:
                  // 1. Candidate Fit & Experience
                  // 2. Availability & Notice Period
                  $catFit          = [];
                  $catAvailability = [];

                  $masterTemplatesList = [
                      [
                          'id'       => 101,
                          'cat'      => 'fit',
                          'icon'     => 'fa-solid fa-award',
                          'title'    => 'Strong Fit',
                          'subtitle' => 'Strong match with 5+ years relevant experience & product alignment.',
                          'order'    => 1,
                          'content'  => "Strong match for the requirement with [relevant experience] of relevant experience in [all skills in job]. Currently working on a similar product environment and appears technically aligned with the role.",
                      ],
                      [
                          'id'       => 104,
                          'cat'      => 'fit',
                          'icon'     => 'fa-solid fa-building-user',
                          'title'    => 'Similar Company / Product Experience',
                          'subtitle' => 'Product-based background with Agile and cross-functional delivery.',
                          'order'    => 2,
                          'content'  => "Currently working[current company context] in a product-based environment with responsibilities very similar to this position. Good understanding of Agile development and cross-functional collaboration.",
                      ],
                      [
                          'id'       => 102,
                          'cat'      => 'availability',
                          'icon'     => 'fa-solid fa-bolt-lightning',
                          'title'    => '🟢 Immediate Joiner',
                          'subtitle' => 'Immediate joiner available within 2–3 days with matching relevant experience.',
                          'order'    => 1,
                          'content'  => "Immediate joiner and available to start within [joining timeline]. Relevant experience matches the requirement closely. Candidate is actively looking due to [reason for job change].",
                      ],
                      [
                          'id'       => 103,
                          'cat'      => 'availability',
                          'icon'     => 'fa-regular fa-calendar-check',
                          'title'    => '🟢 Serving Notice Period',
                          'subtitle' => 'Currently serving notice with confirmed last working day & fast onboarding.',
                          'order'    => 2,
                          'content'  => "Currently serving notice period with last working day on [last working day]. Candidate is open to completing the process immediately and can join from the following week.",
                      ],
                  ];

                  if (!empty($recruiterNoteTemplates)) {
                      foreach ($recruiterNoteTemplates as $dbTpl) {
                          $found = false;
                          $dbTitle = $dbTpl->title ?? 'Custom Note';
                          foreach ($masterTemplatesList as $m) {
                              if ($m['id'] === $dbTpl->id || $m['title'] === $dbTitle) { $found = true; break; }
                          }
                          if (!$found) {
                              $legacyTitles = [
                                  'Bench Availability & Deployment Readiness',
                                  'Bench Availability & Immediate Deployment',
                                  'Notice Period & Availability',
                                  'Remote Only',
                                  'Remote Opportunity Only',
                                  'Contract Engagement',
                                  'Contract/C2C/C2H Engagement Only',
                                  'Direct Hire / Permanent',
                                  'Direct Hire/Permanent Hiring Engagement Only',
                                  'Contract or Direct Hire'
                              ];
                              if (!in_array($dbTitle, $legacyTitles, true)) {
                                  $cleanContent = strip_tags(str_replace(['<br>', '<br/>', '<br />', '</p>', '</li>'], ["\n", "\n", "\n", "\n\n", "\n"], $dbTpl->content ?? $dbTpl->note_text ?? ''));
                                  $masterTemplatesList[] = [
                                      'id'       => $dbTpl->id,
                                      'cat'      => 'fit',
                                      'icon'     => 'fa-regular fa-file-lines',
                                      'title'    => $dbTitle,
                                      'subtitle' => 'Organization candidate evaluation note template.',
                                      'order'    => 99,
                                      'content'  => trim($cleanContent),
                                  ];
                              }
                          }
                      }
                  }

                  foreach ($masterTemplatesList as $item) {
                      if ($item['cat'] === 'availability') $catAvailability[] = $item;
                      else $catFit[] = $item;
                  }
                @endphp

                <div class="fg fv-row recruiter-template-field mb-4">
                  <label class="form-label fw-semibold text-gray-800 fs-7 mb-1">
                    Select Template <span class="text-muted fw-normal fs-8">(Click dropdown to select categorized template)</span>
                  </label>
                  <div class="custom-template-dropdown" id="custom_template_dropdown">
                    <div class="custom-template-trigger" id="custom_template_trigger" tabindex="0" role="combobox" aria-expanded="false" aria-haspopup="listbox" onclick="window.toggleRecruiterTemplateDropdown(event);">
                      <div class="trigger-left">
                        <i class="fa-solid fa-award trigger-icon"></i>
                        <span class="trigger-label" id="selected_template_label">Strong Fit</span>
                      </div>
                      <i class="fa-solid fa-chevron-down trigger-arrow"></i>
                    </div>

                    <div class="custom-template-menu" id="custom_template_menu" role="listbox">
                      {{-- 1. Candidate Fit & Experience --}}
                      @if(count($catFit) > 0)
                      <div class="tpl-category-section">
                        <div class="tpl-category-heading tpl-cat--amber">
                          <span>Candidate Fit &amp; Experience</span>
                        </div>
                        @foreach($catFit as $item)
                        <div class="tpl-card-item {{ $item['id'] === 101 ? 'is-selected' : '' }}" data-template-id="{{ $item['id'] }}" data-template-title="{{ $item['title'] }}" role="option" tabindex="0" onclick="window.selectRecruiterTemplate(this, event);">
                          <div class="tpl-card-left">
                            <span class="tpl-card-icon"><i class="{{ $item['icon'] }}"></i></span>
                            <div class="tpl-card-text">
                              <strong class="tpl-card-title">{{ $item['title'] }}</strong>
                              <small class="tpl-card-subtitle">{{ $item['subtitle'] }}</small>
                            </div>
                          </div>
                          <span class="tpl-card-check">
                            <i class="fa-solid fa-circle-check"></i>
                          </span>
                        </div>
                        @endforeach
                      </div>
                      @endif

                      {{-- 2. Availability & Notice Period --}}
                      @if(count($catAvailability) > 0)
                      <div class="tpl-category-section">
                        <div class="tpl-category-heading tpl-cat--green">
                          <span>Availability &amp; Notice Period</span>
                        </div>
                        @foreach($catAvailability as $item)
                        <div class="tpl-card-item {{ $item['id'] === 101 ? 'is-selected' : '' }}" data-template-id="{{ $item['id'] }}" data-template-title="{{ $item['title'] }}" role="option" tabindex="0" onclick="window.selectRecruiterTemplate(this, event);">
                          <div class="tpl-card-left">
                            <span class="tpl-card-icon"><i class="{{ $item['icon'] }}"></i></span>
                            <div class="tpl-card-text">
                              <strong class="tpl-card-title">{{ $item['title'] }}</strong>
                              <small class="tpl-card-subtitle">{{ $item['subtitle'] }}</small>
                            </div>
                          </div>
                          <span class="tpl-card-check">
                            <i class="fa-solid fa-circle-check"></i>
                          </span>
                        </div>
                        @endforeach
                      </div>
                      @endif
                    </div>

                    {{-- Hidden input for backward compatibility and JS synchronization --}}
                    <input type="hidden" id="recruiter_note_template_select" name="recruiter_note_template_select" value="101" />
                  </div>
                </div>

                <div class="fg fv-row recruiter-editor-field">
                  <div class="d-flex align-items-center justify-content-between mb-2">
                    <label class="mb-0 fw-semibold text-gray-800 fs-7">Recruiter Comments <span class="rq" style="color: #f1416c;">*</span></label>
                    <button type="button" class="btn btn-sm btn-light-warning py-1 px-3 fs-8" id="btn_sync_rm_template" title="Automatically prefill and sync comments with current Designation, Skills, and Availability from form">
                      <i class="fa-solid fa-arrows-rotate me-1 fs-9"></i> Sync Details from Form
                    </button>
                  </div>
                  <textarea id="recruiter_note" name="recruiter_note" rows="6" placeholder="Add recruiter comments about this candidate (strictly up to 100 words)...">{{ @$application->recruiter_note }}</textarea>
                  <div class="d-flex align-items-center justify-content-between mt-2">
                    <div class="recruiter-private-note"><i class="fa-solid fa-lock me-1" aria-hidden="true"></i><span>Internal only - Visible to organization hiring & placement team</span></div>
                    <div class="rm-comments-counter" id="rm_comments_counter">0 / 100 words</div>
                  </div>
                </div>
              </div>
            </div>

<script id="jero_recruiter_comments_isolated_engine">
(function () {
    'use strict';

    var fallbackMasterTemplates = {
        101: {
            id: 101,
            cat: 'fit',
            icon: 'fa-solid fa-award',
            title: 'Strong Fit',
            subtitle: 'Strong match with 5+ years relevant experience & product alignment.',
            order: 1,
            content: "Strong match for the requirement with [relevant experience] of relevant experience in [all skills in job]. Currently working on a similar product environment and appears technically aligned with the role."
        },
        104: {
            id: 104,
            cat: 'fit',
            icon: 'fa-solid fa-building-user',
            title: 'Similar Company / Product Experience',
            subtitle: 'Product-based background with Agile and cross-functional delivery.',
            order: 2,
            content: "Currently working[current company context] in a product-based environment with responsibilities very similar to this position. Good understanding of Agile development and cross-functional collaboration."
        },
        102: {
            id: 102,
            cat: 'availability',
            icon: 'fa-solid fa-bolt-lightning',
            title: '🟢 Immediate Joiner',
            subtitle: 'Immediate joiner available within 2–3 days with matching relevant experience.',
            order: 1,
            content: "Immediate joiner and available to start within [joining timeline]. Relevant experience matches the requirement closely. Candidate is actively looking due to [reason for job change]."
        },
        103: {
            id: 103,
            cat: 'availability',
            icon: 'fa-regular fa-calendar-check',
            title: '🟢 Serving Notice Period',
            subtitle: 'Currently serving notice with confirmed last working day & fast onboarding.',
            order: 2,
            content: "Currently serving notice period with last working day on [last working day]. Candidate is open to completing the process immediately and can join from the following week."
        }
    };

    var serverTemplates = {};
    try {
        serverTemplates = @json(collect($masterTemplatesList)->keyBy('id')) || {};
    } catch (e) {
        serverTemplates = {};
    }

    var masterTemplatesData = Object.assign({}, fallbackMasterTemplates, serverTemplates);

    function formatTemplateToPlainText(html) {
        if (!html) return '';
        var temp = document.createElement('div');
        temp.innerHTML = html;
        var brs = temp.querySelectorAll('br');
        for (var b = 0; b < brs.length; b++) { brs[b].replaceWith('\n'); }
        var lis = temp.querySelectorAll('li');
        for (var l = 0; l < lis.length; l++) {
            var bullet = document.createTextNode('• ');
            lis[l].insertBefore(bullet, lis[l].firstChild);
            lis[l].after('\n');
        }
        var ps = temp.querySelectorAll('p, div, tr');
        for (var p = 0; p < ps.length; p++) {
            ps[p].after('\n');
        }
        var text = temp.textContent || temp.innerText || '';
        return text.replace(/\n\s*\n\s*\n+/g, '\n\n').trim();
    }

    function countWords(str) {
        if (!str) return 0;
        var clean = String(str).trim().replace(/\s+/g, ' ');
        if (!clean) return 0;
        return clean.split(' ').filter(function (w) { return w.length > 0; }).length;
    }

    function truncateToMaxWords(str, maxWords) {
        if (!str) return '';
        var words = String(str).trim().replace(/\s+/g, ' ').split(' ').filter(function (w) { return w.length > 0; });
        if (words.length <= maxWords) return str;
        return words.slice(0, maxWords).join(' ');
    }

    function updateRmCommentsCounter(enforceLimit) {
        var rn = document.getElementById('recruiter_note');
        var counter = document.getElementById('rm_comments_counter');
        if (!rn || !counter) return;

        var val = rn.value || '';
        var wordsCount = countWords(val);

        if (enforceLimit && wordsCount > 100) {
            rn.value = truncateToMaxWords(val, 100);
            wordsCount = 100;
        }

        counter.textContent = wordsCount + ' / 100 words';
        if (wordsCount >= 100) {
            counter.classList.add('is-limit-reached');
            counter.classList.remove('is-warning');
        } else if (wordsCount >= 90) {
            counter.classList.add('is-warning');
            counter.classList.remove('is-limit-reached');
        } else {
            counter.classList.remove('is-warning', 'is-limit-reached');
        }
    }

    function getCandidateFormVariables() {
        var vars = {};

        // 1. Relevant experience: search experience inputs
        var expVal = '';
        var expEl = document.getElementById('total_experience') || document.querySelector('[name="total_experience"]') || document.querySelector('[name="experience"]') || document.getElementById('experience');
        if (expEl && expEl.value && String(expEl.value).trim()) {
            var rawExp = String(expEl.value).trim();
            if (!isNaN(rawExp) && parseFloat(rawExp) > 0) {
                expVal = rawExp + '+ years';
            } else {
                expVal = rawExp;
                if (!/year/i.test(expVal)) expVal += ' years';
            }
        }
        vars.relevantExperience = expVal || '5+ years';

        // 2. Job skills / Candidate skills
        var skillsArr = [];
        var skillInputs = document.querySelectorAll('#skills, [name="skills"], [name="skills[]"], [name="skill[]"], input[name*="skill"]');
        skillInputs.forEach(function (el) {
            if (el.value) {
                try {
                    var parsed = JSON.parse(el.value);
                    if (Array.isArray(parsed)) {
                        parsed.forEach(function (p) {
                            if (p && p.value) skillsArr.push(p.value);
                            else if (typeof p === 'string') skillsArr.push(p);
                        });
                    }
                } catch (e) {
                    var splitted = el.value.split(',');
                    splitted.forEach(function (s) {
                        var trimmed = s.trim();
                        if (trimmed && trimmed.length > 1) skillsArr.push(trimmed);
                    });
                }
            }
        });
        if (skillsArr.length === 0) {
            var tagifyTags = document.querySelectorAll('.tagify__tag-text, .badge-skill, .skill-badge');
            tagifyTags.forEach(function (t) {
                var txt = t.textContent.trim();
                if (txt && skillsArr.indexOf(txt) === -1) skillsArr.push(txt);
            });
        }
        vars.allSkills = skillsArr.length > 0 ? skillsArr.slice(0, 5).join(', ') : '(Mentioned all the skills in job)';

        // 3. Current company
        var currCompanyEl = document.getElementById('current_company') || document.querySelector('[name="current_company"]') || document.getElementById('company_name');
        vars.currentCompany = (currCompanyEl && currCompanyEl.value && currCompanyEl.value.trim()) ? currCompanyEl.value.trim() : '';

        // 4. Joining timeline
        var noticeEl = document.getElementById('notice_period') || document.querySelector('[name="notice_period"]') || document.querySelector('[name="notice_period_days"]') || document.getElementById('notice_period_days');
        var noticeVal = noticeEl ? String(noticeEl.value).trim() : '';
        if (noticeVal === '0' || /immediate/i.test(noticeVal)) {
            vars.joiningTimeline = '2–3 days';
        } else if (noticeVal && !isNaN(noticeVal) && parseInt(noticeVal) > 0) {
            vars.joiningTimeline = parseInt(noticeVal) + ' days';
        } else if (noticeVal) {
            vars.joiningTimeline = noticeVal;
        } else {
            vars.joiningTimeline = '2–3 days';
        }

        // 5. Reason for job change
        var reasonEl = document.getElementById('reason_for_change') || document.querySelector('[name="reason_for_change"]') || document.querySelector('[name="job_change_reason"]');
        vars.jobChangeReason = (reasonEl && reasonEl.value && reasonEl.value.trim()) ? reasonEl.value.trim() : 'project closure';

        // 6. Last working day
        var lwdEl = document.getElementById('last_working_day') || document.querySelector('[name="last_working_day"]') || document.getElementById('lwd');
        var lwdVal = lwdEl ? String(lwdEl.value).trim() : '';
        if (lwdVal) {
            vars.lastWorkingDay = lwdVal;
        } else {
            vars.lastWorkingDay = '25 Oct 2026';
        }

        return vars;
    }

    function interpolateTemplateVariables(str) {
        if (!str) return '';
        var v = getCandidateFormVariables();
        var out = str;

        out = out.replace(/\[relevant experience\]/gi, v.relevantExperience);
        out = out.replace(/\[all skills in job\]/gi, v.allSkills);
        out = out.replace(/\(Mentioned all the skills in job\)/gi, v.allSkills);

        if (v.currentCompany) {
            out = out.replace(/\[current company context\]/gi, ' at ' + v.currentCompany);
        } else {
            out = out.replace(/\[current company context\]/gi, '');
        }

        out = out.replace(/\[joining timeline\]/gi, v.joiningTimeline);
        out = out.replace(/\[reason for job change\]/gi, v.jobChangeReason);
        out = out.replace(/\[last working day\]/gi, v.lastWorkingDay);

        return out.replace(/[ \t]{2,}/g, ' ').trim();
    }

    // Authoritative global dropdown toggle
    window.toggleRecruiterTemplateDropdown = function (e) {
        if (e) {
            if (typeof e.preventDefault === 'function') e.preventDefault();
            if (typeof e.stopPropagation === 'function') e.stopPropagation();
            if (typeof e.stopImmediatePropagation === 'function') e.stopImmediatePropagation();
        }
        var dd = document.getElementById('custom_template_dropdown');
        var menu = document.getElementById('custom_template_menu');
        var trigger = document.getElementById('custom_template_trigger');
        var card = document.getElementById('recruiter_comments_section') || (dd && dd.closest ? dd.closest('.rm-note-card') : null);
        if (!dd || !menu) return;

        var isOpen = dd.classList.contains('is-open') || menu.style.display === 'block';
        if (isOpen) {
            dd.classList.remove('is-open');
            if (card) card.classList.remove('has-open-dropdown');
            menu.style.setProperty('display', 'none', 'important');
            if (trigger) trigger.setAttribute('aria-expanded', 'false');
        } else {
            dd.classList.add('is-open');
            if (card) card.classList.add('has-open-dropdown');
            menu.style.setProperty('display', 'block', 'important');
            menu.style.setProperty('visibility', 'visible', 'important');
            menu.style.setProperty('opacity', '1', 'important');
            if (trigger) trigger.setAttribute('aria-expanded', 'true');
        }
    };

    // Authoritative global template selection
    window.selectRecruiterTemplate = function (el, e) {
        if (e) {
            if (typeof e.preventDefault === 'function') e.preventDefault();
            if (typeof e.stopPropagation === 'function') e.stopPropagation();
            if (typeof e.stopImmediatePropagation === 'function') e.stopImmediatePropagation();
        }
        var item = el || (e && (e.currentTarget || (e.target && e.target.closest && e.target.closest('.tpl-card-item'))));
        if (!item) return;

        var id = item.getAttribute('data-template-id');
        var title = item.getAttribute('data-template-title') || (item.querySelector('.tpl-card-title') ? item.querySelector('.tpl-card-title').textContent.trim() : '');
        if (!id || !masterTemplatesData[id]) return;

        var allItems = document.querySelectorAll('.tpl-card-item');
        for (var i = 0; i < allItems.length; i++) {
            allItems[i].classList.remove('is-selected');
        }
        item.classList.add('is-selected');

        var hiddenInput = document.getElementById('recruiter_note_template_select');
        if (hiddenInput) hiddenInput.value = id;

        var labelEl = document.getElementById('selected_template_label');
        if (labelEl) {
            labelEl.textContent = title;
            labelEl.classList.remove('is-placeholder');
        }

        var dd = document.getElementById('custom_template_dropdown');
        var menu = document.getElementById('custom_template_menu');
        var trigger = document.getElementById('custom_template_trigger');
        var card = document.getElementById('recruiter_comments_section') || (dd && dd.closest ? dd.closest('.rm-note-card') : null);
        if (dd) dd.classList.remove('is-open');
        if (menu) menu.style.setProperty('display', 'none', 'important');
        if (card) card.classList.remove('has-open-dropdown');
        if (trigger) trigger.setAttribute('aria-expanded', 'false');

        var raw = (masterTemplatesData[id] && masterTemplatesData[id].content) || '';
        var formatted = formatTemplateToPlainText(raw);
        var interpolated = interpolateTemplateVariables(formatted);
        var rn = document.getElementById('recruiter_note');
        if (rn) {
            rn.value = truncateToMaxWords(interpolated, 100);
            updateRmCommentsCounter(false);
        }
    };

    // Outside click closes dropdown safely without triggering when clicking inside
    document.addEventListener('click', function (e) {
        var dd = document.getElementById('custom_template_dropdown');
        var menu = document.getElementById('custom_template_menu');
        var trigger = document.getElementById('custom_template_trigger');
        var card = document.getElementById('recruiter_comments_section') || (dd && dd.closest ? dd.closest('.rm-note-card') : null);
        if (!dd || !menu) return;

        if (dd.contains(e.target)) return;

        if (dd.classList.contains('is-open') || menu.style.display === 'block') {
            dd.classList.remove('is-open');
            if (card) card.classList.remove('has-open-dropdown');
            menu.style.setProperty('display', 'none', 'important');
            if (trigger) trigger.setAttribute('aria-expanded', 'false');
        }
    }, false);

    // Keyboard support on trigger
    var triggerEl = document.getElementById('custom_template_trigger');
    if (triggerEl) {
        triggerEl.addEventListener('keydown', function (e) {
            if (e.key === 'Enter' || e.key === ' ' || e.key === 'ArrowDown') {
                e.preventDefault();
                window.toggleRecruiterTemplateDropdown(e);
            } else if (e.key === 'Escape') {
                var dd = document.getElementById('custom_template_dropdown');
                var menu = document.getElementById('custom_template_menu');
                var card = document.getElementById('recruiter_comments_section');
                if (dd) dd.classList.remove('is-open');
                if (menu) menu.style.setProperty('display', 'none', 'important');
                if (card) card.classList.remove('has-open-dropdown');
                this.setAttribute('aria-expanded', 'false');
            }
        });
    }

    // Sync button click
    var syncBtn = document.getElementById('btn_sync_rm_template');
    if (syncBtn) {
        syncBtn.addEventListener('click', function (e) {
            e.preventDefault();
            var hiddenInput = document.getElementById('recruiter_note_template_select');
            var selectedId = (hiddenInput && hiddenInput.value) || 101;
            var rn = document.getElementById('recruiter_note');
            var baseText = (masterTemplatesData[selectedId] && masterTemplatesData[selectedId].content) ? formatTemplateToPlainText(masterTemplatesData[selectedId].content) : (rn ? rn.value : '');
            var interpolated = interpolateTemplateVariables(baseText);
            if (rn) {
                rn.value = truncateToMaxWords(interpolated, 100);
                rn.classList.add('field-attention-highlight');
                setTimeout(function () { rn.classList.remove('field-attention-highlight'); }, 700);
            }
            updateRmCommentsCounter(false);
            if (typeof toastr !== 'undefined') {
                toastr.success('Recruiter comments synchronized with Candidate Skills, Experience, and Availability!');
            }
        });
    }

    // Real-time word counter on textarea input
    var rnEl = document.getElementById('recruiter_note');
    if (rnEl) {
        ['input', 'paste', 'keyup', 'change'].forEach(function (evt) {
            rnEl.addEventListener(evt, function () {
                updateRmCommentsCounter(true);
            });
        });

        // Intercept clipboard paste if user pastes a file (e.g. resume)
        rnEl.addEventListener('paste', function (e) {
            var clipboardData = e.clipboardData || (window.clipboardData);
            if (clipboardData && clipboardData.files && clipboardData.files.length > 0) {
                var file = clipboardData.files[0];
                var resumeInput = document.getElementById('resume') || document.querySelector('.resumeupload');
                if (resumeInput && typeof processFile === 'function') {
                    e.preventDefault();
                    e.stopPropagation();
                    var dt = new DataTransfer();
                    dt.items.add(file);
                    resumeInput.files = dt.files;
                    processFile(file);
                }
            }
        });
    }

    // Form inputs auto-synchronization (debounced)
    var syncTimeout = null;
    function triggerDebouncedTemplateSync() {
        clearTimeout(syncTimeout);
        syncTimeout = setTimeout(function () {
            var rn = document.getElementById('recruiter_note');
            if (!rn) return;
            var curText = rn.value.trim();
            var hiddenInput = document.getElementById('recruiter_note_template_select');
            var selectedId = (hiddenInput && hiddenInput.value) || 101;
            var tpl = masterTemplatesData[selectedId];
            if (!tpl) return;

            var rawTpl = formatTemplateToPlainText(tpl.content);
            var isRecognizedTemplate = (curText.length === 0)
                || curText.indexOf('Strong match for the requirement') !== -1
                || curText.indexOf('Immediate joiner and available to start') !== -1
                || curText.indexOf('Currently serving notice period with last working day') !== -1
                || curText.indexOf('product-based environment with responsibilities very similar') !== -1;

            if (isRecognizedTemplate) {
                var interpolated = interpolateTemplateVariables(rawTpl);
                rn.value = truncateToMaxWords(interpolated, 100);
                updateRmCommentsCounter(false);
            }
        }, 350);
    }

    var watchedFields = [
        '#total_experience', '[name="total_experience"]', '[name="experience"]',
        '#current_company', '[name="current_company"]',
        '#notice_period', '[name="notice_period"]', '[name="notice_period_days"]',
        '#last_working_day', '[name="last_working_day"]',
        '#skills', '[name="skills"]', '[name="skills[]"]'
    ];
    watchedFields.forEach(function (sel) {
        var el = document.querySelector(sel);
        if (el) {
            el.addEventListener('change', triggerDebouncedTemplateSync);
            el.addEventListener('input', triggerDebouncedTemplateSync);
        }
    });

    // Default prefill: Strong Fit (ID 101)
    function applyDefaultRecruiterNotePrefill() {
        var rn = document.getElementById('recruiter_note');
        if (!rn) return;
        var existingVal = (rn.value || '').trim();
        if (!existingVal) {
            var defaultTpl = masterTemplatesData[101] || fallbackMasterTemplates[101];
            if (defaultTpl && defaultTpl.content) {
                var formatted = formatTemplateToPlainText(defaultTpl.content);
                var interpolated = interpolateTemplateVariables(formatted);
                rn.value = truncateToMaxWords(interpolated, 100);
            }
        }
        updateRmCommentsCounter(false);
    }

    // Compatibility dummies so external callers work without errors
    window.compileEvaluationMatrix = function () {};
    window.syncRecruiterEvaluationMatrix = function () {};

    // Run prefill immediately upon script parsing
    applyDefaultRecruiterNotePrefill();

    // Also run on DOM ready and window load to guarantee execution
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function () {
            applyDefaultRecruiterNotePrefill();
            updateRmCommentsCounter(false);
        });
    } else {
        applyDefaultRecruiterNotePrefill();
        updateRmCommentsCounter(false);
    }

    window.addEventListener('load', function () {
        applyDefaultRecruiterNotePrefill();
    });

    setTimeout(applyDefaultRecruiterNotePrefill, 100);
    setTimeout(applyDefaultRecruiterNotePrefill, 400);
    setTimeout(applyDefaultRecruiterNotePrefill, 1200);
})();
</script>

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
    background: #059669 !important;
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
    background: #047857 !important;
}

.rf-cta-btn:active {
    transform: scale(0.99) !important;
}

.field-attention-highlight {
    outline: 2px solid #10b981 !important;
    box-shadow: 0 0 0 4px rgba(16, 185, 129, 0.25) !important;
    border-radius: 6px !important;
    transition: all 0.2s ease-in-out !important;
    background-color: rgba(236, 253, 245, 0.5) !important;
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
        <button type="button" class="rf-cta-btn" id="rfPrimaryCta" style="width:100%;height:44px;background:#059669;color:#ffffff;border:none;border-radius:8px;font-size:14px;font-weight:600;font-family:'Poppins',sans-serif;display:flex;align-items:center;justify-content:center;gap:8px;cursor:pointer;">
            <span>Review Required Fields</span>
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
        </button>
    </div>
</div>

@endsection

@php
    $fxSnapshot = GeneralMethod::getLatestFxSnapshot();
    $rates = $fxSnapshot['rates_json'] ?? ($currencyConverter['usd_based_rates'] ?? [
        'USD' => 1,
        'INR' => 83,
        'EUR' => 0.87,
        'JPY' => 159.65,
        'GBP' => 0.76,
        'AED' => 3.67
    ]);
    $fxVersion = $fxSnapshot['version'] ?? '202610-001';
    $currencyKeys = array_keys($rates);
@endphp

@push('footer-script')
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
                    var display = (stage === 'Completed' || (stage && stage.indexOf('Building ATS JSON') !== -1) || pct >= 100)
                        ? 'Completed'
                        : ($('<div>').text(stage).html() + ' (' + pct + '%)');
                    $('#upDetailParserStatus').html(
                        '<span>' + display + '</span>'
                    );
                },
                onSuccess: function (result, totalMs) {
                    window._clientParserState = 'completed';
                    $('#upDetailParserStatus').html(
                        '<span style="color: #41c08e; font-weight: 600;">Completed</span>'
                    );
                    toastr.success('Resume parsed in browser and fields populated!');
                    if (typeof syncRecruiterEvaluationMatrix === 'function') {
                        syncRecruiterEvaluationMatrix(false);
                    }
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
        $('#upInfo').removeClass('show').removeAttr('style').hide();
        $('#uploadBox').removeClass('d-none').attr('style', '').show();
        $('#fileInfo').hide();
        $('#fileName').text('');
        $('#resume').val('');
        $('#resumeCheck').val('');
        var isEdit = String(@json($page_event ?? '')).toUpperCase() === 'EDIT' && parseInt($('#appId').val() || '0', 10) > 0;
        $('#resume_intent').val(isEdit ? 'REMOVE' : 'NONE');
        $('.download-file, #upDownloadBtn').addClass('d-none').hide();
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
            $('.up-file-badge').css({'background': '#d1fae5', 'color': '#059669'});
            $('.up-file-badge i').attr('class', 'fa-regular fa-file-word').css('color', '#059669');
            $('#upFileBadgeExt').css('color', '#059669');
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
        $info.addClass('show').attr('style', 'display: flex !important;');
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

        // Legacy Pay Rate listeners superseded by JeroCandidateCurrencyEngine
        function fromUSD(amount, currency) {
            return (currencyRates && currencyRates[currency]) ? (amount / currencyRates[currency]) : amount;
        }

        function updatePayUI() { /* delegated to JeroCandidateCurrencyEngine */ }
        function updateBillUI() { /* delegated to JeroCandidateCurrencyEngine */ }



        function payRateUpdate() { /* delegated to JeroCandidateCurrencyEngine */ }
        function billRateUpdate() { /* delegated to JeroCandidateCurrencyEngine */ }


        





        // Legacy currency change events superseded by JeroCandidateCurrencyEngine








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
// Reliable in-browser network connectivity check without external 3rd-party latency/blocking
async function isInternetAvailable() {
    if (typeof navigator !== 'undefined' && 'onLine' in navigator) {
        return Boolean(navigator.onLine);
    }
    return true;
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
                            notEmpty:{message: "Full name is required"},
                            stringLength:{min: 2, max: 191, message: "Full name must be between 2 and 191 characters"},
                            regexp:{
                                regexp: /^[\p{L}\s\-\'.]+$/u,
                                message: "Full name should only contain letters, spaces, hyphens, and apostrophes"
                            }
                        }
                    },
					
                    'current_designation':
                    {
                        validators:
                        {
                            // notEmpty:{ message: "Current designation is required"}
                            
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
                            callback: {
                                message: 'The value is not a valid email address',
                                callback: function(input) {
                                    var val = (input.value || '').trim();
                                    if (!val) return true; // Optional in In Review stage
                                    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(val);
                                }
                            },
                            stringLength:{max: 191, message: "Email must not exceed 191 characters"}
                        }
                    },
                    'secondrary_email':
                    {
                        validators:
                        {
                            emailAddress:{message: 'The value is not a valid email address'},
                            stringLength:{max: 55, message: "Secondary email must not exceed 55 characters"},
                            different:{
                                compare: function() {
                                    return form.querySelector('[name="email"]') ? form.querySelector('[name="email"]').value : '';
                                },
                                message: 'Secondary email cannot be the same as primary email'
                            }
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

            // 1. RAM Validation for Missing Required Fields FIRST (Stage-aware)
            if (typeof detectCandidateMissingFields === 'function') {
                var missing = detectCandidateMissingFields(form, validator);
                if (missing && missing.length > 0) {
                    if (typeof showCandidateRequiredFieldsModal === 'function') {
                        showCandidateRequiredFieldsModal(missing);
                    } else {
                        Swal.fire({
                            text: "Please fill all required fields before updating.",
                            icon: "warning",
                            confirmButtonText: "Ok, got it!"
                        });
                    }
                    return false;
                }
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

                    if (typeof compileEvaluationMatrix === 'function') {
                        compileEvaluationMatrix();
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
                        url         : ('{{ $page_event }}' === 'EDIT' && parseInt(candidateEditId, 10) > 0) ? '{{ route("service_company.job-applications.update", @$application->id ?: (@$appId ?: 0)) }}' + '?_token=' + '{{ csrf_token() }}' : '{{ url("service_company/job-applications/update") }}/' + candidateEditId + '?_token=' + '{{ csrf_token() }}',
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
                                $(document).trigger('jero-candidate-saved');
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
                    if (typeof detectCandidateMissingFields === 'function') {
                        var missingNow = detectCandidateMissingFields(form, validator);
                        if (missingNow && missingNow.length > 0) {
                            showCandidateRequiredFieldsModal(missingNow);
                            return;
                        }
                    }
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
          return String(value || '').trim().replace(/[\.\-\_\s\/]/g, '').toLocaleLowerCase();
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

      // JERO Autonomous Client-First Skills Taxonomy Engine (0% MySQL / 0% Elasticsearch Read Load)
      var _skillsTaxonomy = window._jeroSkillsTaxonomy || null;
      var _skillsAliases = (_skillsTaxonomy && _skillsTaxonomy.aliases) || {};
      var _skillsRelations = (_skillsTaxonomy && _skillsTaxonomy.relations) || {};
      var _skillsCategoryMap = (_skillsTaxonomy && _skillsTaxonomy.skill_category_map) || {};
      var _skillsList = (_skillsTaxonomy && _skillsTaxonomy.skills) || [];

      function resolveSkillAlias(str) {
          var clean = String(str || '').trim();
          var lower = clean.toLowerCase();
          if (_skillsAliases && _skillsAliases[lower]) {
              return _skillsAliases[lower];
          }
          return clean;
      }

      var JERO_SKILLS_VERSION = '2026-10-06.enterprise-god-level-v3';

      function getSkillDomainIcon(category) {
          switch (category) {
              case 'Programming Languages': return 'fa-solid fa-code';
              case 'Frontend Development': return 'fa-solid fa-desktop';
              case 'Backend & APIs': return 'fa-solid fa-server';
              case 'Databases & Storage': return 'fa-solid fa-database';
              case 'Cloud Platforms': return 'fa-solid fa-cloud';
              case 'DevOps, CI/CD & Infra': return 'fa-solid fa-gears';
              case 'Data Engineering & Big Data': return 'fa-solid fa-diagram-project';
              case 'AI, Machine Learning & Data Science': return 'fa-solid fa-brain';
              case 'Mobile Development': return 'fa-solid fa-mobile-screen';
              case 'Testing, QA & Automation': return 'fa-solid fa-vial-circle-check';
              case 'Cybersecurity & Networking': return 'fa-solid fa-shield-halved';
              case 'Enterprise Software, ERP & CRM': return 'fa-solid fa-building-columns';
              case 'Business Intelligence & Product': return 'fa-solid fa-chart-pie';
              case 'Architecture & Methodologies': return 'fa-solid fa-cubes-stacked';
              default: return 'fa-solid fa-layer-group';
          }
      }

      function highlightSkillQuery(text, query) {
          if (!query || !text) return document.createTextNode(text);
          var cleanQ = String(query).trim().toLowerCase();
          var idx = text.toLowerCase().indexOf(cleanQ);
          if (idx === -1) return document.createTextNode(text);

          var frag = document.createDocumentFragment();
          if (idx > 0) {
              frag.appendChild(document.createTextNode(text.substring(0, idx)));
          }
          var mark = document.createElement('mark');
          mark.className = 'candidate-skill-highlight';
          mark.textContent = text.substring(idx, idx + cleanQ.length);
          frag.appendChild(mark);
          if (idx + cleanQ.length < text.length) {
              frag.appendChild(document.createTextNode(text.substring(idx + cleanQ.length)));
          }
          return frag;
      }

      function loadLocalSkillsTaxonomy() {
          if (_skillsTaxonomy && _skillsList.length && _skillsTaxonomy.version === JERO_SKILLS_VERSION) {
              return;
          }
          try {
              var cached = localStorage.getItem('jero_skills_taxonomy_' + JERO_SKILLS_VERSION);
              if (cached) {
                  var parsed = JSON.parse(cached);
                  if (parsed && parsed.version === JERO_SKILLS_VERSION && Array.isArray(parsed.skills)) {
                      _skillsTaxonomy = parsed;
                      _skillsAliases = parsed.aliases || {};
                      _skillsRelations = parsed.relations || {};
                      _skillsCategoryMap = parsed.skill_category_map || {};
                      _skillsList = parsed.skills;
                      window._jeroSkillsTaxonomy = parsed;
                      return;
                  }
              }
              // Purge stale legacy caches
              localStorage.removeItem('jero_skills_taxonomy_v1');
              localStorage.removeItem('jero_skills_taxonomy_2026-10-06.enterprise-pristine-v2');
          } catch (e) {}

          if (window.fetch) {
              fetch("{{ asset('skills/jero-skills-taxonomy.json') }}?v=" + JERO_SKILLS_VERSION, { cache: 'no-cache' })
                  .then(function (res) { return res.json(); })
                  .then(function (data) {
                      if (data && Array.isArray(data.skills)) {
                          _skillsTaxonomy = data;
                          _skillsAliases = data.aliases || {};
                          _skillsRelations = data.relations || {};
                          _skillsCategoryMap = data.skill_category_map || {};
                          _skillsList = data.skills;
                          window._jeroSkillsTaxonomy = data;
                          try {
                              localStorage.setItem('jero_skills_taxonomy_' + JERO_SKILLS_VERSION, JSON.stringify(data));
                          } catch (e) {}
                          if (typeof refreshCandidatePrimarySkills === 'function') {
                              refreshCandidatePrimarySkills();
                          }
                      }
                  })
                  .catch(function () {});
          }
      }
      loadLocalSkillsTaxonomy();

      function searchSkillsLocal(query) {
          var rawTrimmed = String(query || '').trim();
          var normalizedQuery = candidateSkillKey(rawTrimmed);

          var selected = candidateSelectedSkills();
          var isAlreadySelected = function (name) {
              var n = candidateSkillKey(resolveSkillAlias(name));
              return selected.some(function (s) { return candidateSkillKey(resolveSkillAlias(s.name)) === n; });
          };

          var results = [];
          var seen = new Set();

          if (!normalizedQuery) {
              // 0-Keystroke Combobox State: Show Unselected Job Requirements + Synergy Recommendations + Top Essentials
              candidateUniqueSkills(candidateRequiredSkills).forEach(function (skill) {
                  var skillNorm = candidateSkillKey(resolveSkillAlias(skill.name));
                  if (!seen.has(skillNorm) && !isAlreadySelected(skill.name)) {
                      results.push({
                          id: skill.id,
                          name: skill.name,
                          type: 'required',
                          category: (_skillsCategoryMap && _skillsCategoryMap[skill.name]) || ''
                      });
                      seen.add(skillNorm);
                  }
              });

              selected.forEach(function (sel) {
                  var canonName = resolveSkillAlias(sel.name);
                  var rels = (_skillsRelations && _skillsRelations[canonName]) || [];
                  rels.forEach(function (rel) {
                      var relNorm = candidateSkillKey(resolveSkillAlias(rel));
                      if (!seen.has(relNorm) && !isAlreadySelected(rel) && results.length < 10) {
                          results.push({
                              name: rel,
                              type: 'suggested',
                              category: (_skillsCategoryMap && _skillsCategoryMap[rel]) || ''
                          });
                          seen.add(relNorm);
                      }
                  });
              });

              for (var k = 0; k < _skillsList.length && results.length < 10; k++) {
                  var topSkill = _skillsList[k];
                  var topNorm = candidateSkillKey(resolveSkillAlias(topSkill));
                  if (!seen.has(topNorm) && !isAlreadySelected(topSkill)) {
                      results.push({
                          name: topSkill,
                          type: 'global',
                          category: (_skillsCategoryMap && _skillsCategoryMap[topSkill]) || ''
                      });
                      seen.add(topNorm);
                  }
              }

              return results.slice(0, 10);
          }

          // 1. Direct Alias Match (e.g. k8s -> Kubernetes, js -> JavaScript)
          var resolvedAlias = _skillsAliases && _skillsAliases[rawTrimmed.toLowerCase()];
          if (resolvedAlias && !isAlreadySelected(resolvedAlias)) {
              var aliasNorm = candidateSkillKey(resolvedAlias);
              results.push({
                  name: resolvedAlias,
                  type: 'alias',
                  category: (_skillsCategoryMap && _skillsCategoryMap[resolvedAlias]) || ''
              });
              seen.add(aliasNorm);
          }

          // 2. Job Required Skills matching query (Rank #1 Priority)
          candidateUniqueSkills(candidateRequiredSkills).forEach(function (skill) {
              var skillNorm = candidateSkillKey(skill.name);
              if (!seen.has(skillNorm) && !isAlreadySelected(skill.name)) {
                  if (skillNorm.indexOf(normalizedQuery) !== -1) {
                      results.push({
                          id: skill.id,
                          name: skill.name,
                          type: 'required',
                          category: (_skillsCategoryMap && _skillsCategoryMap[skill.name]) || ''
                      });
                      seen.add(skillNorm);
                  }
              }
          });

          // 3. Co-occurrence synergy skills based on currently selected stack
          selected.forEach(function (sel) {
              var canonName = resolveSkillAlias(sel.name);
              var rels = (_skillsRelations && _skillsRelations[canonName]) || [];
              rels.forEach(function (rel) {
                  var relNorm = candidateSkillKey(rel);
                  if (!seen.has(relNorm) && !isAlreadySelected(rel)) {
                      if (relNorm.indexOf(normalizedQuery) !== -1) {
                          results.push({
                              name: rel,
                              type: 'suggested',
                              category: (_skillsCategoryMap && _skillsCategoryMap[rel]) || ''
                          });
                          seen.add(relNorm);
                      }
                  }
              });
          });

          // 4. Prefix Matches from canonical dictionary (Instant in-memory lookup)
          for (var i = 0; i < _skillsList.length && results.length < 15; i++) {
              var item = _skillsList[i];
              var itemNorm = candidateSkillKey(item);
              if (!seen.has(itemNorm) && !isAlreadySelected(item)) {
                  if (itemNorm.startsWith(normalizedQuery)) {
                      results.push({
                          name: item,
                          type: 'global',
                          category: (_skillsCategoryMap && _skillsCategoryMap[item]) || ''
                      });
                      seen.add(itemNorm);
                  }
              }
          }

          // 5. Substring / Word-Boundary Matches
          if (results.length < 12) {
              for (var j = 0; j < _skillsList.length && results.length < 15; j++) {
                  var item2 = _skillsList[j];
                  var itemNorm2 = candidateSkillKey(item2);
                  if (!seen.has(itemNorm2) && !isAlreadySelected(item2)) {
                      if (itemNorm2.indexOf(normalizedQuery) !== -1) {
                          results.push({
                              name: item2,
                              type: 'global',
                              category: (_skillsCategoryMap && _skillsCategoryMap[item2]) || ''
                          });
                          seen.add(itemNorm2);
                      }
                  }
              }
          }

          return results.slice(0, 12);
      }

      function flashDuplicateChip(norm) {
          if (!candidatePrimarySkillChips) return;
          var chips = candidatePrimarySkillChips.querySelectorAll('.candidate-primary-skill-chip');
          chips.forEach(function (chip) {
              var name = chip.getAttribute('data-skill-name') || chip.querySelector('span:first-child')?.textContent || '';
              if (candidateSkillKey(name) === norm) {
                  chip.classList.remove('pulse-duplicate');
                  void chip.offsetWidth; // Trigger reflow
                  chip.classList.add('pulse-duplicate');
                  chip.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
              }
          });
      }

      function renderCandidateSkillSuggestions(suggestions, query) {
          if (!candidatePrimarySkillSuggestions || !candidatePrimarySkillQuickInput) {
              return;
          }

          candidateSkillLastSuggestions = Array.isArray(suggestions) ? suggestions : [];
          candidatePrimarySkillSuggestions.replaceChildren();
          candidateSkillSuggestionItems = [];
          candidateSkillActiveIndex = -1;

          var trimmedQ = String(query || '').trim();

          // Header Bar
          var header = document.createElement('div');
          header.className = 'candidate-primary-skill-suggestions-header';
          header.innerHTML = '<div class="candidate-primary-skill-suggestions-header-left"><i class="fa-solid fa-wand-magic-sparkles"></i><span>Skill Suggestions</span></div>' +
              '<div class="candidate-primary-skill-suggestions-header-right">' +
              '<span class="candidate-primary-skill-count-badge">' + candidateSkillLastSuggestions.length + ' matches</span>' +
              '<span class="candidate-primary-skill-perf-badge"><i class="fa-solid fa-bolt"></i>0% Server Load</span>' +
              '</div>';
          candidatePrimarySkillSuggestions.appendChild(header);

          // Scrollable List Container
          var listContainer = document.createElement('div');
          listContainer.className = 'candidate-primary-skill-suggestions-list';
          listContainer.setAttribute('role', 'listbox');

          candidateSkillLastSuggestions.forEach(function (item, index) {
              var option = document.createElement('button');
              option.type = 'button';
              option.id = 'candidate-primary-skill-option-' + index;
              option.className = 'candidate-primary-skill-suggestion';
              option.setAttribute('role', 'option');
              option.setAttribute('aria-selected', 'false');

              // Left: Icon + Highlighted Name
              var main = document.createElement('div');
              main.className = 'candidate-primary-skill-suggestion-main';

              var iconSpan = document.createElement('span');
              iconSpan.className = 'candidate-primary-skill-suggestion-icon';
              var iconEl = document.createElement('i');
              iconEl.className = getSkillDomainIcon(item.category);
              iconSpan.appendChild(iconEl);

              var nameSpan = document.createElement('span');
              nameSpan.className = 'candidate-primary-skill-suggestion-name';
              nameSpan.appendChild(highlightSkillQuery(item.name, trimmedQ));

              main.append(iconSpan, nameSpan);
              option.appendChild(main);

              // Right: Meta Badges + Enter Hint
              var meta = document.createElement('div');
              meta.className = 'candidate-primary-skill-suggestion-meta';

              if (item.type === 'required') {
                  var badge = document.createElement('span');
                  badge.className = 'candidate-primary-skill-suggestion-badge is-required';
                  badge.innerHTML = '<i class="fa-solid fa-star"></i><span>Required</span>';
                  meta.appendChild(badge);
              } else if (item.type === 'suggested') {
                  var badge = document.createElement('span');
                  badge.className = 'candidate-primary-skill-suggestion-badge is-suggested';
                  badge.innerHTML = '<i class="fa-solid fa-wand-magic-sparkles"></i><span>Suggested</span>';
                  meta.appendChild(badge);
              } else if (item.type === 'alias') {
                  var badge = document.createElement('span');
                  badge.className = 'candidate-primary-skill-suggestion-badge is-alias';
                  badge.textContent = 'Alias';
                  meta.appendChild(badge);
              }

              if (item.category) {
                  var catBadge = document.createElement('span');
                  catBadge.className = 'candidate-primary-skill-suggestion-badge is-category';
                  catBadge.textContent = item.category;
                  meta.appendChild(catBadge);
              }

              var kbdSpan = document.createElement('span');
              kbdSpan.className = 'candidate-primary-skill-suggestion-kbd';
              kbdSpan.innerHTML = '<kbd>↵</kbd>';
              meta.appendChild(kbdSpan);

              option.appendChild(meta);

              option.addEventListener('mousedown', function (event) {
                  event.preventDefault();
              });
              option.addEventListener('click', function () {
                  addCandidatePrimarySkill(item.name);
              });
              listContainer.appendChild(option);
              candidateSkillSuggestionItems.push({ element: option, skill: item });
          });

          candidatePrimarySkillSuggestions.appendChild(listContainer);

          // Custom Skill Option if user typed something not matching exactly
          if (trimmedQ) {
              var exactFound = candidateSkillLastSuggestions.some(function (it) {
                  return it.name.toLowerCase() === trimmedQ.toLowerCase();
              });
              if (!exactFound) {
                  var customOption = document.createElement('div');
                  customOption.className = 'candidate-primary-skill-custom-option';

                  var customBtn = document.createElement('button');
                  customBtn.type = 'button';
                  customBtn.className = 'candidate-primary-skill-custom-btn';

                  var cMain = document.createElement('div');
                  cMain.className = 'candidate-primary-skill-suggestion-main';
                  cMain.innerHTML = '<span class="candidate-primary-skill-suggestion-icon custom-add-icon"><i class="fa-solid fa-plus"></i></span>' +
                      '<span class="candidate-primary-skill-suggestion-name">Add custom skill: <strong class="text-primary">"' + trimmedQ.replace(/</g, '&lt;').replace(/>/g, '&gt;') + '"</strong></span>';

                  var cMeta = document.createElement('div');
                  cMeta.className = 'candidate-primary-skill-suggestion-meta';
                  cMeta.innerHTML = '<span class="candidate-primary-skill-suggestion-badge is-required">Custom</span><span class="candidate-primary-skill-suggestion-kbd"><kbd>↵</kbd></span>';

                  customBtn.append(cMain, cMeta);
                  customBtn.addEventListener('mousedown', function (e) { e.preventDefault(); });
                  customBtn.addEventListener('click', function () {
                      addCandidatePrimarySkill(trimmedQ);
                  });
                  customOption.appendChild(customBtn);
                  candidatePrimarySkillSuggestions.appendChild(customOption);

                  if (!candidateSkillSuggestionItems.length) {
                      candidateSkillSuggestionItems.push({ element: customBtn, skill: { name: trimmedQ } });
                  }
              }
          }

          // Footer Bar with Shortcut Hints
          var footer = document.createElement('div');
          footer.className = 'candidate-primary-skill-suggestions-footer';
          footer.innerHTML = '<div class="candidate-primary-skill-shortcuts">' +
              '<span class="candidate-primary-skill-shortcut-item"><kbd>↑</kbd><kbd>↓</kbd> Navigate</span>' +
              '<span class="candidate-primary-skill-shortcut-item"><kbd>↵</kbd> Select</span>' +
              '<span class="candidate-primary-skill-shortcut-item"><kbd>Esc</kbd> Dismiss</span>' +
              '</div>' +
              '<div class="candidate-primary-skill-footer-note">' +
              '<span>Client RAM (0% Server Load)</span>' +
              '</div>';
          candidatePrimarySkillSuggestions.appendChild(footer);

          candidatePrimarySkillSuggestions.hidden = false;
          if (candidatePrimarySkillsCard) {
              candidatePrimarySkillsCard.classList.add('has-open-skill-suggestions');
          }
          candidatePrimarySkillQuickInput.setAttribute('aria-expanded', 'true');
      }

      function addCandidatePrimarySkill(value) {
          var rawName = String(value || '').trim();
          if (!rawName) {
              return;
          }

          var resolvedName = resolveSkillAlias(rawName);
          var norm = candidateSkillKey(resolvedName);
          var selected = candidateSelectedSkills();

          // Strict Deduplication Check with Pulse Feedback
          var isDuplicate = selected.some(function (s) {
              return candidateSkillKey(s.name) === norm;
          });

          if (isDuplicate) {
              flashDuplicateChip(norm);
              if (candidatePrimarySkillQuickInput) {
                  candidatePrimarySkillQuickInput.value = '';
              }
              closeCandidateSkillSuggestions();
              return;
          }

          var requiredSkill = candidateUniqueSkills(candidateRequiredSkills).find(function (skill) {
              return candidateSkillKey(skill.name) === norm;
          });

          var tagPayload = requiredSkill ? { value: requiredSkill.name, id: requiredSkill.id } : resolvedName;
          tagifyskill.addTags([tagPayload]);

          if (candidatePrimarySkillQuickInput) {
              candidatePrimarySkillQuickInput.value = '';
              candidatePrimarySkillQuickInput.focus();
          }
          closeCandidateSkillSuggestions();
      }

      // Resume Parser Batch Bridge: Deduplicates & adds skills in bulk
      window.addCandidatePrimarySkillsBatch = function (skillsList) {
          if (!Array.isArray(skillsList) || !skillsList.length) return;
          var selected = candidateSelectedSkills();
          var selectedSet = new Set(selected.map(function (s) { return candidateSkillKey(s.name); }));
          var toAdd = [];

          skillsList.forEach(function (raw) {
              var clean = resolveSkillAlias(String(raw || '').trim());
              var norm = candidateSkillKey(clean);
              if (clean && !selectedSet.has(norm)) {
                  selectedSet.add(norm);
                  var req = candidateUniqueSkills(candidateRequiredSkills).find(function (r) {
                      return candidateSkillKey(r.name) === norm;
                  });
                  toAdd.push(req ? { value: req.name, id: req.id } : clean);
              }
          });

          if (toAdd.length && tagifyskill && typeof tagifyskill.addTags === 'function') {
              tagifyskill.addTags(toAdd);
              refreshCandidatePrimarySkills();
          }
      };

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
              chip.setAttribute('data-skill-name', skill.name);

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

          // Render Suggestions: Missing job requirements or synergy relations
          candidateSuggestedSkillsList.replaceChildren();
          var suggestionsToShow = [];
          var isMissingRequired = missing.length > 0;

          if (isMissingRequired) {
              suggestionsToShow = missing;
              candidateRemainingSkills.textContent = missing.length + ' remaining ' + (missing.length === 1 ? 'skill' : 'skills');
          } else {
              // Synergy suggestions based on selected stack
              var seenSyn = new Set();
              selected.forEach(function (sel) {
                  var rels = (_skillsRelations && _skillsRelations[sel.name]) || [];
                  rels.forEach(function (rel) {
                      var relNorm = candidateSkillKey(rel);
                      var isAlreadySel = selected.some(function (s) { return candidateSkillKey(s.name) === relNorm; });
                      if (!isAlreadySel && !seenSyn.has(relNorm) && suggestionsToShow.length < 8) {
                          seenSyn.add(relNorm);
                          suggestionsToShow.push({ name: rel });
                      }
                  });
              });
              candidateRemainingSkills.textContent = suggestionsToShow.length ? suggestionsToShow.length + ' suggested' : '';
          }

          suggestionsToShow.forEach(function (skill) {
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

          candidateSuggestedSkills.hidden = suggestionsToShow.length === 0;

          if (candidatePrimarySkillQuickInput && document.activeElement === candidatePrimarySkillQuickInput) {
              var q = candidatePrimarySkillQuickInput.value.trim();
              if (q.length >= 1) {
                  renderCandidateSkillSuggestions(searchSkillsLocal(q), q);
              }
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
                      var q = candidatePrimarySkillQuickInput.value.trim();
                      renderCandidateSkillSuggestions(searchSkillsLocal(q), q);
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
              renderCandidateSkillSuggestions(searchSkillsLocal(query), query);
          });

          candidatePrimarySkillQuickInput.addEventListener('click', function () {
              if (candidatePrimarySkillSuggestions && candidatePrimarySkillSuggestions.hidden) {
                  var query = candidatePrimarySkillQuickInput.value.trim();
                  renderCandidateSkillSuggestions(searchSkillsLocal(query), query);
              }
          });

          candidatePrimarySkillQuickInput.addEventListener('blur', function () {
              window.setTimeout(closeCandidateSkillSuggestions, 160);
          });

          // 100% In-Browser Search Execution — ZERO network requests
          candidatePrimarySkillQuickInput.addEventListener('input', function () {
              var query = candidatePrimarySkillQuickInput.value.trim();
              renderCandidateSkillSuggestions(searchSkillsLocal(query), query);
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
      var _candidateSkillCache = window._candidateSkillCache || {};
      var _candidateLocationCache = window._candidateLocationCache || {};
      function getCandidateLocationSuggestions() { return _candidateLocationCache; }

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
			var ectcCurEl = document.getElementById("current_ectc_cur");
			var thCurEl = document.getElementById("current_th_cur");
			if (ectcCurEl) { ectcCurEl.value = current_ctc_cur; }
			if (thCurEl) { thCurEl.value = current_ctc_cur; }
   });
   $('body').on('change', '#current_ectc_cur', function(){
   			var	current_ectc_cur	=	$(this).val();
			var ctcCurEl = document.getElementById("current_ctc_cur");
			var thCurEl = document.getElementById("current_th_cur");
			if (ctcCurEl) { ctcCurEl.value = current_ectc_cur; }
			if (thCurEl) { thCurEl.value = current_ectc_cur; }
   });
   $('body').on('change', '#current_th_cur', function(){
   			var	current_th_cur		=	$(this).val();
			var ectcCurEl = document.getElementById("current_ectc_cur");
			var ctcCurEl = document.getElementById("current_ctc_cur");
			if (ectcCurEl) { ectcCurEl.value = current_th_cur; }
			if (ctcCurEl) { ctcCurEl.value = current_th_cur; }
   });
   $('body').on('change', '#current_ctc_mode', function(){
   			var	current_ctc_mode	=	$(this).val();
			document.getElementById("current_ectc_mode").value = current_ctc_mode;
   });
  


        function numberToIndianWords(num, currCode) {
            num = Math.floor(Number(num));
            if (!num || isNaN(num) || num <= 0) return '';
            var code = (currCode || $('#current_ctc_cur option:selected').data('code') || 'INR').toString().toUpperCase();

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

            function convertIndian(n) {
                if (n < 20) return ones[n];
                if (n < 100) return tens[Math.floor(n / 10)] + ' ' + ones[n % 10];
                if (n < 1000) return ones[Math.floor(n / 100)] + ' Hundred ' + convertIndian(n % 100);
                if (n < 100000) return convertIndian(Math.floor(n / 1000)) + ' Thousand ' + convertIndian(n % 1000);
                if (n < 10000000) return convertIndian(Math.floor(n / 100000)) + ' Lakh ' + convertIndian(n % 100000);
                if (n < 1000000000) return convertIndian(Math.floor(n / 10000000)) + ' Crore ' + convertIndian(n % 10000000);
                return convertIndian(Math.floor(n / 1000000000)) + ' Arab ' + convertIndian(n % 1000000000);
            }

            function convertInternational(n) {
                if (n < 20) return ones[n];
                if (n < 100) return tens[Math.floor(n / 10)] + ' ' + ones[n % 10];
                if (n < 1000) return ones[Math.floor(n / 100)] + ' Hundred ' + convertInternational(n % 100);
                if (n < 1000000) return convertInternational(Math.floor(n / 1000)) + ' Thousand ' + convertInternational(n % 1000);
                if (n < 1000000000) return convertInternational(Math.floor(n / 1000000)) + ' Million ' + convertInternational(n % 1000000);
                return convertInternational(Math.floor(n / 1000000000)) + ' Billion ' + convertInternational(n % 1000000000);
            }

            if (code === 'INR') {
                return convertIndian(num).replace(/\s+/g, ' ').trim() + ' Rupees';
            }
            return convertInternational(num).replace(/\s+/g, ' ').trim() + ' ' + code;
        }

        function updateCTCFields() {
            var ctcVal = parseFloat($("#ctc").val()) || 0;
            var payMonthly = parseFloat($("#pay_monthly").val()) || 0;
            var payAnnual = parseFloat($("#pay_annual").val()) || 0;
            var ectcVal = parseFloat($("#ectc").val()) || 0;
            var billMonthly = parseFloat($("#bill_monthly").val()) || 0;
            var billAnnual = parseFloat($("#bill_annual").val()) || 0;
            var code = ($('#current_ctc_cur option:selected').data('code') || 'INR').toString().toUpperCase();

            if (payAnnual > 0) {
                $(".payrateannual").text(numberToIndianWords(payAnnual, code));
                $(".payratemonthly").text(numberToIndianWords(payMonthly, code));
                $(".CurrentCTC").text(numberToIndianWords(ctcVal, code));
            } else {
                $(".payrateannual, .payratemonthly, .CurrentCTC").text('');
            }

            if (billAnnual > 0) {
                $(".billrateannual").text(numberToIndianWords(billAnnual, code));
                $(".billratemonthly").text(numberToIndianWords(billMonthly, code));
                $(".expectedctc").text(numberToIndianWords(ectcVal, code));
            } else {
                $(".billrateannual, .billratemonthly, .expectedctc").text('');
            }
        }
        setTimeout(() => {
            updateCTCFields();
        }, 500);
        $(document).on("input change", "#ctc, #ectc, #pay_annual, #bill_annual, #pay_monthly, #bill_monthly", updateCTCFields);
            

     $(document).on('change', '#total_exp, #rel_exp', function() { 
      total_exp_and_rel_exp();
     });


		
</script>


<script>
  

// Candidate Required Fields Config and Modal Navigation (Global Scope for Add & Edit)
var candidateFieldConfigMap = {
    'resume': {
        label: 'Upload Resume',
        selector: '#uploadBox',
        message: 'Please upload a resume file (PDF or Word, max 2MB).'
    },
    'careersummary': {
        label: 'Career Summary',
        selector: '#careersummary',
        message: 'Career summary is required.'
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
        selector: '#jero-cs-location',
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
    'skills': {
        label: 'Primary Skills',
        selector: '#kt_tagify_2',
        message: 'Primary skills are required (minimum 1 skill).'
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
    'ctc': {
        label: 'Current CTC (Salary Details)',
        selector: '#ctc',
        message: 'Current salary details are required.'
    },
    'ectc': {
        label: 'Expected CTC (Salary Details)',
        selector: '#ectc',
        message: 'Expected salary details are required.'
    },
    'np_id': {
        label: 'Notice Period & Joining Details',
        selector: '#np_id',
        message: 'Notice period is required.'
    },
    'wm': {
        label: 'Preferred Work Mode',
        selector: 'input[name="wm"]',
        message: 'Preferred work mode is required.'
    },
    'job_skills': {
        label: 'Job Skills Details',
        selector: '.candidate-job-skills-scroll table',
        message: 'Job skills experience and ratings are required.'
    },
    'screening_questions': {
        label: 'Screening Questions',
        selector: '.candidate-card-icon--questions',
        message: 'Please answer all required screening questions.'
    },
    'recruiter_note': {
        label: 'Recruiter Comments',
        selector: '#recruiter_note',
        message: 'Recruiter comments are required.'
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

    // Determine target stage:
    // If user changed stage selector on page (#master_status_id), use that value.
    // Otherwise check hidden #current_master_status_id or window.__candidateMasterStatusId.
    // On Add Candidate, default is 8 (In Review).
    var targetStageEl = document.getElementById('master_status_id') || document.getElementById('current_master_status_id');
    var activeStageId = targetStageEl ? parseInt(targetStageEl.value, 10) : (window.__candidateMasterStatusId || 8);

    // =========================================================================
    // MANDATORY FOR IN REVIEW STAGE (master_status_id == 8):
    // Only Full Name * and Primary Contact Number * are mandatory.
    // All other fields remain optional and do NOT block submission.
    // =========================================================================
    var fn = ($('#full_name').val() || '').trim();
    if (!fn || fn.length < 2) addMissing('full_name');

    var ph = ($('#phone').val() || '').trim();
    if (!ph || ph.length < 6) addMissing('phone');

    // If candidate is in "In Review" stage (8), stop here — all other fields are optional!
    if (activeStageId === 8) {
        return missing;
    }

    // =========================================================================
    // 100% VALIDATION WHEN CHANGING STAGE FROM In Review -> Sourcing (or Stage 1+):
    // =========================================================================

    // 1. Primary Email *
    var em = ($('#email').val() || '').trim();
    if (!em || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(em)) addMissing('email');

    // 2. Current Location *
    var loc = ($('#kt_tagify_work_location').val() || '').trim();
    var canonLoc = ($('#location_canonical_label').val() || '').trim();
    var cityId = ($('#location_city_id').val() || '').trim();
    if (!loc && !canonLoc && !cityId) {
        addMissing('kt_tagify_work_location');
        $('#jero-cs-location').addClass('has-validation-error');
        $('#jero-cs-location').closest('.candidate-field-control, .bd-control, .fg').addClass('has-validation-error');
    }

    // 3. Highest Qualification *
    var qual = ($('#qualification').val() || '').trim();
    if (!qual) addMissing('qualification');

    // 4. Nationality
    var nat = ($('#nationality').val() || '').trim();
    if (!nat) addMissing('nationality');

    // 5. Gender
    var gen = ($('#gender').val() || '').trim();
    if (!gen) addMissing('gender');

    // 6. Primary Skills — minimum 1 skill required
    var sk = ($('#kt_tagify_2').val() || '').trim();
    if (!sk) {
        addMissing('skills');
    } else {
        try {
            var parsedSkills = JSON.parse(sk);
            if (Array.isArray(parsedSkills) && parsedSkills.length === 0) {
                addMissing('skills');
            }
        } catch (e) {
            if (sk.length < 1) addMissing('skills');
        }
    }

    // 7. Experience Details
    var te = $('#total_exp_year').val();
    if (te === '' || te === null || te === undefined) addMissing('total_exp');

    var re = $('#rel_exp_year').val();
    if (re === '' || re === null || re === undefined) addMissing('rel_exp');

    // Check if Fresher (Total Experience is 0)
    var isFresher = (te === '0' || te === 0);

    // Fields NOT mandatory for Freshers:
    if (!isFresher) {
        // Professional Profile: Current Designation, Current Company, Employment Type
        var cd = ($('#current_designation').val() || '').trim();
        if (!cd) addMissing('current_designation');

        var cc = ($('#current_company').val() || '').trim();
        if (!cc) addMissing('current_company');

        var et = ($('#EmploymentType').val() || '').trim();
        if (!et) addMissing('employmentType');

        // Salary Details: Current CTC, Expected CTC
        var ctc = ($('#ctc').val() || '').trim();
        if (!ctc) addMissing('ctc');

        var ectc = ($('#ectc').val() || '').trim();
        if (!ectc) addMissing('ectc');

        // Notice Period & Joining Details
        var snpMode = ($('#serving_notice_period').val() || '').trim();
        if (!snpMode) {
            addMissing('np_id');
        } else if (snpMode === '0') {
            var npDays = ($('#ddl_np_id').val() || '').trim();
            if (!npDays) addMissing('np_id');
        } else if (snpMode === '1' || snpMode === '2') {
            var npDate = ($('#np_id').val() || '').trim();
            if (!npDate) addMissing('np_id');
        }
    }

    // 8. Preferred Work Mode
    var wm = $('input[name="wm"]:checked').val();
    if (!wm) addMissing('wm');

    // 9. Job Skills Details (if job has skills)
    var $jobSkillYears = $('select[name^="YearskillId"]');
    if ($jobSkillYears.length > 0) {
        var missingJobSkills = false;
        $jobSkillYears.each(function() {
            var val = $(this).val();
            if (!val || val === '0') {
                missingJobSkills = true;
            }
        });
        if (missingJobSkills) addMissing('job_skills');
    }

    // 10. Screening Questions (if job has questions)
    var $questions = $('.screening-question-input, [name^="answer["]');
    if ($questions.length > 0) {
        var missingQuestions = false;
        $questions.each(function() {
            var qVal = $(this).val();
            if (!qVal || !String(qVal).trim()) {
                missingQuestions = true;
            }
        });
        if (missingQuestions) addMissing('screening_questions');
    }

    // 11. Recruiter Comments
    var rn = ($('#recruiter_note').val() || '').trim();
    if (!rn) addMissing('recruiter_note');

    // 12. Upload Resume
    var hasResume = false;
    if (($('#uploaded_file_path').val() && String($('#uploaded_file_path').val()).trim()) ||
        ($('#resume_original_name').val() && String($('#resume_original_name').val()).trim()) ||
        ($('#resume_intent').val() === 'KEEP' || $('#existing_resume_path').val())) {
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
    if (!hasResume) addMissing('resume');

    // 13. Career Summary *
    var cs = ($('#careersummary').val() || '').trim();
    if (!cs) addMissing('careersummary');

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
                var btn = document.querySelector('#chooseFileBtn') || targetEl;
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
$(document).off('click', '#rfModalClose').on('click', '#rfModalClose', function (e) {
    e.preventDefault();
    closeCandidateRequiredFieldsModal();
});

$(document).off('click', '#requiredFieldsModal').on('click', '#requiredFieldsModal', function (e) {
    if (e.target === this) {
        closeCandidateRequiredFieldsModal();
    }
});

$(document).off('keydown.rfModal').on('keydown.rfModal', function (e) {
    if (e.key === 'Escape' && $('#requiredFieldsModal').is(':visible')) {
        closeCandidateRequiredFieldsModal();
    }
});

$(document).off('click', '.rf-field-row').on('click', '.rf-field-row', function (e) {
    e.preventDefault();
    var key = $(this).data('field-key');
    if (key) {
        jumpToCandidateField(key);
    }
});

$(document).off('keydown', '.rf-field-row').on('keydown', '.rf-field-row', function (e) {
    if (e.key === 'Enter' || e.key === ' ') {
        e.preventDefault();
        var key = $(this).data('field-key');
        if (key) {
            jumpToCandidateField(key);
        }
    }
});

$(document).off('click', '#rfPrimaryCta').on('click', '#rfPrimaryCta', function (e) {
    e.preventDefault();
    if (window.__currentMissingFields && window.__currentMissingFields.length > 0) {
        jumpToCandidateField(window.__currentMissingFields[0].key);
    } else {
        closeCandidateRequiredFieldsModal();
    }
});

@php if($page_event != 'EDIT'){ @endphp
var KTAddCandidate = function () {
    var form;
    var submitButton;
    var validator;
    var isInitialized = false;

    var handleForm = function () {
        validator = FormValidation.formValidation(
            form,
            {
                fields: {
                    'full_name': {
                        validators: {
                            notEmpty: { message: "Full name is required" },
                            stringLength: { min: 2, max: 191, message: "Full name must be between 2 and 191 characters" }
                        }
                    },
                    'phone': {
                        validators: {
                            notEmpty: { message: "Primary number is required" },
                            digits: { message: "Primary number must contain only digits" },
                            stringLength: { min: 6, max: 15, message: "Please enter a valid mobile number" }
                        }
                    },
                    'secondary_mobile': {
                        validators: {
                            digits: { message: "Secondary number must contain only digits" },
                            stringLength: { min: 6, max: 15, message: "Please enter a valid mobile number" }
                        }
                    },
                    'email': {
                        validators: {
                            callback: {
                                message: "The value is not a valid email address",
                                callback: function(input) {
                                    var val = (input.value || '').trim();
                                    if (!val) return true; // Optional in In Review stage
                                    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(val);
                                }
                            },
                            stringLength: { max: 191, message: "Email must not exceed 191 characters" }
                        }
                    },
                    'secondrary_email': {
                        validators: {
                            emailAddress: { message: "The value is not a valid email address" },
                            stringLength: { max: 55, message: "Secondary email must not exceed 55 characters" },
                            different: {
                                compare: function() {
                                    return form.querySelector('[name="email"]') ? form.querySelector('[name="email"]').value : '';
                                },
                                message: "Secondary email cannot be the same as primary email"
                            }
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

        window.benchFormValidator = validator;

        var executeCandidateSubmit = function () {
            window.__candidateIsSubmitting = true;
            closeCandidateRequiredFieldsModal();
            var $btn = $('#save-form');
            $btn.prop('disabled', true).addClass('disabled');
            $btn.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Saving Candidate...');
            if (submitButton) {
                submitButton.setAttribute('data-kt-indicator', 'on');
                submitButton.disabled = true;
            }

            if (typeof compileEvaluationMatrix === 'function') {
                compileEvaluationMatrix();
            }

            // Ensure unique client_submission_uuid for idempotency
            if (!$('#client_submission_uuid').val()) {
                $('#client_submission_uuid').val(window.crypto && window.crypto.randomUUID ? window.crypto.randomUUID() : ('sub_' + Math.random().toString(36).slice(2) + Date.now()));
            }

            var formData = new FormData($('#createForm')[0]);
            // Strip binary resume file if already uploaded to R2
            if ($('#resume_session_uuid').val() || $('#uploaded_file_path').val()) {
                if (formData.delete) { formData.delete('resume'); }
            }

            $.ajax({
                url: '{{route('service_company.job-applications.clientappstore')}}'+'?_token=' + '{{ csrf_token() }}',
                type: 'POST',
                headers: {
                    'X-Correlation-ID': window.crypto && window.crypto.randomUUID ? window.crypto.randomUUID() : ('cid-' + Date.now() + '-' + Math.random().toString(36).slice(2))
                },
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json',
                redirect: true,
                success: function (response) {
                    if (typeof response === 'string') {
                        try { response = JSON.parse(response); } catch (e) {}
                    }
                    if (response.status == 'coinCheck') {
                        window.__candidateIsSubmitting = false;
                        $btn.prop('disabled', false).removeClass('disabled').html('<i class="fa-solid fa-paper-plane" aria-hidden="true"></i><span>Submit Candidate</span>');
                        if (submitButton) { submitButton.removeAttribute('data-kt-indicator'); submitButton.disabled = false; }
                        Swal.fire("Notice", response.message , "warning");
                        return false;
                    }
                    if (response.status == 'mailcheck') {
                        window.__candidateIsSubmitting = false;
                        $btn.prop('disabled', false).removeClass('disabled').html('<i class="fa-solid fa-paper-plane" aria-hidden="true"></i><span>Submit Candidate</span>');
                        if (submitButton) { submitButton.removeAttribute('data-kt-indicator'); submitButton.disabled = false; }
                        Swal.fire("Warning", response.message , "warning");
                        return false;
                    }
                    if (response.status !== 'success') {
                        window.__candidateIsSubmitting = false;
                        $btn.prop('disabled', false).removeClass('disabled').html('<i class="fa-solid fa-paper-plane" aria-hidden="true"></i><span>Submit Candidate</span>');
                        if (submitButton) { submitButton.removeAttribute('data-kt-indicator'); submitButton.disabled = false; }
                        Swal.fire("Submission Failed", response.message || "The candidate could not be saved. Please try again.", "error");
                        return false;
                    }

                    submitButton && submitButton.removeAttribute('data-kt-indicator');

                    // Definitive success: rotate client_submission_uuid for subsequent submissions
                    $('#client_submission_uuid').val(window.crypto && window.crypto.randomUUID ? window.crypto.randomUUID() : ('sub_' + Math.random().toString(36).slice(2) + Date.now()));

                    // Clear resume upload session & form state
                    if (window.ResumePresign && typeof window.ResumePresign.cancelUpload === 'function') {
                        try {
                            var ctxKey = typeof getUpContextKey === 'function' ? getUpContextKey() : undefined;
                            window.ResumePresign.cancelUpload(ctxKey);
                        } catch (e) {}
                    }
                    $('#resume_session_uuid').val('');
                    $('#uploaded_file_path').val('');
                    $('#resume_original_name').val('');

                    var fallbackUrl = '{{ route('service_company.cvtracker.index') }}';
                    var targetUrl = (response && typeof response.url === 'string' && response.url.trim() !== '') ? response.url : fallbackUrl;
                    var candidateId = (response && (response.candidate_id || (response.candidate && response.candidate.id))) || null;

                    var saveRedirected = false;
                    function executeSaveRedirect() {
                        if (saveRedirected) return;
                        saveRedirected = true;
                        $(document).trigger('jero-candidate-saved');
                        var e1 = form.querySelector('[name="email1"]'); if (e1) e1.value = "";
                        var p1 = form.querySelector('[name="password1"]'); if (p1) p1.value = "";
                        var finalRedirectUrl = targetUrl;
                        if (candidateId) {
                            try {
                                var parsedUrl = new URL(finalRedirectUrl, window.location.origin);
                                parsedUrl.searchParams.set('new_candidate_id', candidateId);
                                finalRedirectUrl = parsedUrl.toString();
                            } catch (e) {
                                finalRedirectUrl += (finalRedirectUrl.indexOf('?') !== -1 ? '&' : '?') + 'new_candidate_id=' + encodeURIComponent(candidateId);
                            }
                        }
                        window.location.assign(finalRedirectUrl);
                    }

                    if (!candidateId) {
                        Swal.fire({
                            text: "Candidate successfully saved.",
                            icon: "success",
                            buttonsStyling: false,
                            confirmButtonText: "Ok, got it!",
                            customClass: { confirmButton: "btn btn-primary" },
                            timer: 2000,
                            timerProgressBar: true
                        }).then(function () {
                            executeSaveRedirect();
                        });
                        setTimeout(executeSaveRedirect, 2200);
                        return;
                    }

                    var checkIndexUrlTemplate = '{{ route("service_company.job-applications.check-index-status", ["id" => ":id"]) }}';
                    var checkIndexUrl = checkIndexUrlTemplate.replace(':id', candidateId);
                    var pollInterval = null;
                    var stopTimerTimeout = null;
                    var secondsRemaining = 10;
                    var pollAttempt = 0;

                    function cleanupTimers() {
                        if (pollInterval) { clearInterval(pollInterval); pollInterval = null; }
                        if (stopTimerTimeout) { clearTimeout(stopTimerTimeout); stopTimerTimeout = null; }
                    }

                    Swal.fire({
                        title: "Candidate Saved!",
                        html: '<div class="d-flex flex-column align-items-center py-2">' +
                              '  <div class="spinner-border text-primary mb-3" role="status" style="width: 2.2rem; height: 2.2rem;">' +
                              '    <span class="visually-hidden">Syncing...</span>' +
                              '  </div>' +
                              '  <div class="fs-6 fw-semibold text-gray-700 mb-2">Syncing candidate to search index for instant listing...</div>' +
                              '  <div class="badge badge-light-primary fs-7 fw-bold" id="candidate-index-countdown">Preparing index (max 10s)...</div>' +
                              '</div>',
                        icon: "info",
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        showConfirmButton: true,
                        confirmButtonText: "Preparing index...",
                        customClass: { confirmButton: "btn btn-sm btn-light-primary mt-2" },
                        didOpen: function () {
                            var btn = Swal.getConfirmButton();
                            if (btn) {
                                btn.disabled = true;
                                btn.classList.add('disabled');
                            }
                        }
                    }).then(function () {
                        cleanupTimers();
                        executeSaveRedirect();
                    });

                    // Unlock button after 3 seconds so recruiter is never blocked permanently
                    setTimeout(function () {
                        if (!saveRedirected) {
                            var btn = Swal.getConfirmButton();
                            if (btn) {
                                btn.disabled = false;
                                btn.classList.remove('disabled');
                                btn.innerText = "Go to Tracker Now";
                            }
                        }
                    }, 3000);

                    var countdownInterval = setInterval(function () {
                        secondsRemaining--;
                        var badge = document.getElementById('candidate-index-countdown');
                        if (badge) {
                            badge.innerText = 'Syncing search index (' + Math.max(0, secondsRemaining) + 's)...';
                        }
                        if (secondsRemaining <= 0) {
                            clearInterval(countdownInterval);
                        }
                    }, 1000);

                    // 10s Stop Timer fallback
                    stopTimerTimeout = setTimeout(function () {
                        cleanupTimers();
                        clearInterval(countdownInterval);
                        var btn = Swal.getConfirmButton();
                        if (btn) {
                            btn.disabled = false;
                            btn.classList.remove('disabled');
                            btn.innerText = "Go to Tracker Now";
                        }
                        Swal.update({
                            title: "Candidate Saved!",
                            html: '<div class="fs-6 fw-semibold text-gray-700 py-2"><i class="fa-solid fa-clock-rotate-left text-warning fs-3 me-2"></i>Candidate saved! Search index is updating in the background.</div>',
                            icon: "info",
                            showConfirmButton: true
                        });
                        setTimeout(executeSaveRedirect, 1500);
                    }, 10000);

                    function pollSearchIndex() {
                        if (saveRedirected) return;
                        pollAttempt++;
                        $.ajax({
                            url: checkIndexUrl,
                            type: 'GET',
                            data: { attempt: pollAttempt },
                            dataType: 'json',
                            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                            success: function (res) {
                                if (res && res.indexed === true) {
                                    cleanupTimers();
                                    clearInterval(countdownInterval);
                                    Swal.update({
                                        title: "Candidate Ready!",
                                        html: '<div class="fs-6 fw-bold text-success py-2"><i class="fa-solid fa-circle-check text-success fs-2 me-2"></i>Candidate indexed & verified live in listing!</div>',
                                        icon: "success",
                                        showConfirmButton: false
                                    });
                                    setTimeout(executeSaveRedirect, 600);
                                }
                            },
                            error: function () {
                                // Silently continue polling; 10s stop timer will handle graceful redirect
                            }
                        });
                    }

                    // Initial poll at 600ms, followed by 800ms intervals
                    setTimeout(function () {
                        pollSearchIndex();
                        if (!saveRedirected) {
                            pollInterval = setInterval(pollSearchIndex, 800);
                        }
                    }, 600);
                },
                error: function (xhr, textStatus, errorThrown) {
                    window.__candidateIsSubmitting = false;
                    $btn.prop('disabled', false).removeClass('disabled').html('<i class="fa-solid fa-paper-plane" aria-hidden="true"></i><span>Submit Candidate</span>');
                    if (submitButton) { submitButton.removeAttribute('data-kt-indicator'); submitButton.disabled = false; }

                    if (xhr.status === 0 || !navigator.onLine) {
                        Swal.fire({ title: "No Internet", text: "You are currently offline or the server is unreachable.", icon: "warning", confirmButtonText: "Ok, got it!", customClass: { confirmButton: "btn btn-warning" } });
                        return;
                    }
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

                    var display = serverMsg || (xhr.status ? ('HTTP ' + xhr.status + ' ' + xhr.statusText) : textStatus);
                    Swal.fire("Error (" + (xhr.status || '?') + ")", display || "Something went wrong. Please try again.", "error");
                }
            });
        };

        var handleCandidateSubmit = async function (e) {
            if (e) {
                e.preventDefault();
                if (typeof e.stopPropagation === 'function') e.stopPropagation();
            }

            if (window.__candidateIsSubmitting) {
                return false;
            }

            var $btn = $('#save-form');
            if ($btn.length && $btn.prop('disabled')) {
                return false;
            }

            var connected = true;
            try {
                if (typeof isInternetAvailable === 'function') {
                    connected = await isInternetAvailable();
                } else if (typeof navigator !== 'undefined' && 'onLine' in navigator) {
                    connected = Boolean(navigator.onLine);
                }
            } catch (netErr) {
                connected = (typeof navigator !== 'undefined') ? navigator.onLine : true;
            }

            if (!connected) {
                Swal.fire({
                    title: "No Internet",
                    text: "You are currently offline or the server is unreachable.",
                    icon: "warning",
                    confirmButtonText: "Ok, got it!",
                    customClass: { confirmButton: "btn btn-warning" }
                });
                return false;
            }

            // 1. RAM Validation for Missing Required Fields FIRST!
            var missing = detectCandidateMissingFields(form, validator);
            if (missing && missing.length > 0) {
                showCandidateRequiredFieldsModal(missing);
                return false;
            }

            // 2. Format / Pattern Validation (FormValidation)
            if (validator && typeof validator.validate === 'function') {
                validator.validate().then(function (status) {
                    if (status === 'Valid') {
                        executeCandidateSubmit();
                    } else {
                        var missingNow = detectCandidateMissingFields(form, validator);
                        if (missingNow && missingNow.length > 0) {
                            showCandidateRequiredFieldsModal(missingNow);
                        } else {
                            Swal.fire({
                                text: "Please check the highlighted fields with formatting errors.",
                                icon: "error",
                                confirmButtonText: "Ok, got it!",
                                customClass: { confirmButton: "btn btn-primary" }
                            });
                        }
                    }
                }).catch(function (valErr) {
                    console.warn('[Add Candidate] FormValidation error, falling back:', valErr);
                    var fallbackMissing = detectCandidateMissingFields(form, null);
                    if (fallbackMissing && fallbackMissing.length > 0) {
                        showCandidateRequiredFieldsModal(fallbackMissing);
                    } else {
                        executeCandidateSubmit();
                    }
                });
            } else {
                executeCandidateSubmit();
            }
        };

        // Delegated click listener
        $(document).off('click', '#save-form').on('click', '#save-form', function (e) {
            handleCandidateSubmit(e);
        });

        // Form submit interception
        $(document).off('submit', '#createForm').on('submit', '#createForm', function (e) {
            if (e) {
                e.preventDefault();
                if (typeof e.stopPropagation === 'function') e.stopPropagation();
            }
            handleCandidateSubmit(e);
            return false;
        });
    };

    return {
        init: function () {
            if (isInitialized) return;
            isInitialized = true;
            form = document.querySelector('#createForm');
            submitButton = document.querySelector('#save-form');
            handleForm();
        }
    };
}();

var ktAddCandidateInitialized = false;
function initKTAddCandidate() {
    if (ktAddCandidateInitialized) return;
    if (typeof KTAddCandidate !== 'undefined' && KTAddCandidate.init) {
        ktAddCandidateInitialized = true;
        KTAddCandidate.init();
    }
}
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initKTAddCandidate);
} else {
    initKTAddCandidate();
}
$(document).ready(initKTAddCandidate);

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

			
   
   });






       


// ==========================================================================
// Compatibility dummies
    window.compileEvaluationMatrix = function () {};
    window.syncRecruiterEvaluationMatrix = function () {};

// ============================================================================
// ENTERPRISE CANDIDATE PROFILE: FULL NAME & EMAIL POLISH (CLIENT-FIRST)
// ============================================================================
(function () {
    'use strict';

    // 1. Cultural-Preserving Title-Caser (Preserves O'Connor, Jean-Luc, Al-Mansoor)
    window.formatCandidateTitleCase = function (str) {
        if (!str) return '';
        return String(str)
            .trim()
            .toLowerCase()
            .replace(/(^|[\s\-\'])([a-z])/g, function (_, sep, chr) {
                return sep + chr.toUpperCase();
            });
    };

    // 2. Client-Side Fast Domain Typo Detector (Zero Server Overhead)
    window.detectDomainTypo = function (email) {
        var commonDomains = {
            'gmial.com': 'gmail.com', 'gmai.com': 'gmail.com', 'gamil.com': 'gmail.com',
            'yaho.com': 'yahoo.com', 'yahooo.com': 'yahoo.com',
            'hotmial.com': 'hotmail.com', 'hotmai.com': 'hotmail.com',
            'outlok.com': 'outlook.com', 'outloo.com': 'outlook.com'
        };
        var parts = String(email || '').split('@');
        if (parts.length === 2 && commonDomains[parts[1].toLowerCase()]) {
            return parts[0] + '@' + commonDomains[parts[1].toLowerCase()];
        }
        return null;
    };

    // 3. Clear Button State Synchronization
    function syncClearButton($input) {
        var $clearBtn = $input.siblings('.candidate-field-clear');
        if (($input.val() || '').trim().length > 0) {
            $clearBtn.show();
        } else {
            $clearBtn.hide();
        }
    }

    $(document).on('input paste keyup change', '#full_name, #email, #secondrary_email', function () {
        syncClearButton($(this));
    });

    $(document).on('click', '.candidate-field-clear', function (e) {
        e.preventDefault();
        var $input = $(this).siblings('input');
        $input.val('').trigger('input').trigger('change').focus();
        $(this).hide();
        if ($input.attr('id') === 'email') {
            $('#email-typo-hint').hide().empty();
        }
    });

    // 4. Name Field Normalization
    $(document).on('blur change', '#full_name', function () {
        var val = $(this).val();
        if (val) {
            var formatted = window.formatCandidateTitleCase(val);
            if (formatted !== val) {
                $(this).val(formatted);
            }
        }
    });

    $(document).on('paste', '#full_name', function () {
        var self = this;
        setTimeout(function () {
            var val = $(self).val();
            if (val) {
                $(self).val(window.formatCandidateTitleCase(val));
            }
        }, 15);
    });

    // 5. Email Normalization & Inline Typo Assistance
    $(document).on('blur change', '#email', function () {
        var raw = ($(this).val() || '').trim().toLowerCase();
        $(this).val(raw);

        var fix = window.detectDomainTypo(raw);
        var $hint = $('#email-typo-hint');
        if (fix && fix !== raw) {
            $hint.html('<i class="fa-solid fa-lightbulb text-warning" aria-hidden="true"></i> Did you mean <strong><a href="javascript:void(0);" class="apply-email-fix">' + fix + '</a></strong>?').show();
        } else {
            $hint.hide().empty();
        }
    });

    $(document).on('click', '.apply-email-fix', function (e) {
        e.preventDefault();
        var fixed = $(this).text();
        $('#email').val(fixed).trigger('change');
        $('#email-typo-hint').hide().empty();
        if (window.benchFormValidator && typeof window.benchFormValidator.revalidateField === 'function') {
            window.benchFormValidator.revalidateField('email');
        }
    });

    $(document).on('blur change', '#secondrary_email', function () {
        var raw = ($(this).val() || '').trim().toLowerCase();
        $(this).val(raw);
    });

    // Initial check on page load
    $(document).ready(function () {
        $('#full_name, #email, #secondrary_email').each(function () {
            syncClearButton($(this));
        });
    });
})();

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
    var previousNoticeMode = noticePeriodSelect ? String(noticePeriodSelect.value) : '';

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
            var selected = Boolean(mode && option.getAttribute('data-notice-period-value') === mode);
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
                ? 'Select expected last working date (LWD)'
                : 'Select last working date (LWD)';
        }
        if (noticeDaysSelect) {
            noticeDaysSelect.disabled = mode !== '0';
            noticeDaysSelect.required = mode === '0';
        }
        if (noticeDateLabel) {
            noticeDateLabel.innerHTML = (mode === '1' ? 'Expected Last Working Date (LWD)' : 'Last Working Date (LWD)') + ' <span class="rq">*</span>';
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
})();

// ============================================================================
// JERO ATS - GLOBAL CURRENCY CANDIDATE ENGINE (RULES 7, 14, 17)
// ============================================================================
(function initializeJeroCandidateCurrencyEngine() {
    'use strict';

    var root = document.querySelector('.job-candidate-create') || document;
    var salaryCurrencySelect = root.querySelector('#current_ctc_cur');
    if (!salaryCurrencySelect) return;

    // 1. BOUNDED LRU CACHE (Max 200 items, <50KB footprint)
    class BoundedLRU {
        constructor(max = 200) {
            this.max = max;
            this.map = new Map();
        }
        get(key) {
            if (!this.map.has(key)) return null;
            var val = this.map.get(key);
            this.map.delete(key);
            this.map.set(key, val);
            return val;
        }
        set(key, val) {
            if (this.map.has(key)) {
                this.map.delete(key);
            } else if (this.map.size >= this.max) {
                var oldestKey = this.map.keys().next().value;
                this.map.delete(oldestKey);
            }
            this.map.set(key, val);
        }
        clear() { this.map.clear(); }
    }

    var _lruCache = new BoundedLRU(200);
    var _isUpdating = false;

    // FX Data injected from backend
    var fxRates = @json($rates);
    var fxVersion = @json($fxVersion);

    var CURRENCY_SYMBOLS = {
        USD: '$', INR: '₹', EUR: '€', GBP: '£', AED: 'AED ', CAD: 'CA$',
        AUD: 'A$', SGD: 'S$', JPY: '¥', CHF: 'CHF ', NZD: 'NZ$', ZAR: 'R ',
        BRL: 'R$', PHP: '₱', MYR: 'RM ', THB: '฿', IDR: 'Rp ', VND: '₫',
        SEK: 'kr ', NOK: 'kr ', DKK: 'kr ', PLN: 'zł ', MXN: 'Mex$', HKD: 'HK$',
        TWD: 'NT$', KRW: '₩', TRY: '₺', SAR: 'SAR ', QAR: 'QAR ', KWD: 'KD ',
        BHD: 'BD ', OMR: 'OMR ', EGP: 'E£ ', ILS: '₪', CZK: 'Kč ', HUF: 'Ft ',
        RON: 'lei ', CLP: 'CLP$', COP: 'COL$', ARS: 'AR$', NGN: '₦', KES: 'KSh ',
        PKR: 'PKR ', BDT: '৳', LKR: 'Rs ', NPR: 'Rs '
    };

    var CURRENCY_FLAGS = {
        INR: '🇮🇳', EUR: '🇪🇺', JPY: '🇯🇵', GBP: '🇬🇧', USD: '🇺🇸', AED: '🇦🇪',
        CAD: '🇨🇦', AUD: '🇦🇺', SGD: '🇸🇬', CHF: '🇨🇭', NZD: '🇳🇿', ZAR: '🇿🇦',
        BRL: '🇧🇷', PHP: '🇵🇭', MYR: '🇲🇾', THB: '🇹🇭', IDR: '🇮🇩', VND: '🇻🇳',
        SEK: '🇸🇪', NOK: '🇳🇴', DKK: '🇩🇰', PLN: '🇵🇱', MXN: '🇲🇽', HKD: '🇭🇰',
        TWD: '🇹🇼', KRW: '🇰🇷', TRY: '🇹🇷', SAR: '🇸🇦', QAR: '🇶🇦', KWD: '🇰🇼',
        BHD: '🇧🇭', OMR: '🇴🇲', EGP: '🇪🇬', ILS: '🇮🇱', CZK: '🇨🇿', HUF: '🇭🇺',
        RON: '🇷🇴', CLP: '🇨🇱', COP: '🇨🇴', ARS: '🇦🇷', NGN: '🇳🇬', KES: '🇰🇪',
        PKR: '🇵🇰', BDT: '🇧🇩', LKR: '🇱🇰', NPR: '🇳🇵'
    };

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

    var salaryCurrencyMirrors = [
        root.querySelector('#current_ectc_cur'),
        root.querySelector('#current_th_cur')
    ];

    function safeConvert(amount, fromCurr, toCurr) {
        try {
            if (amount === null || amount === undefined || isNaN(amount)) return 0;
            var num = parseFloat(amount);
            if (!isFinite(num) || num <= 0) return 0;
            if (fromCurr === toCurr) return num;

            var cacheKey = fromCurr + '_' + toCurr + '_' + num + '_' + fxVersion;
            var cached = _lruCache.get(cacheKey);
            if (cached !== null) return cached;

            var rateFrom = fxRates[fromCurr] || 1;
            var rateTo = fxRates[toCurr] || 1;
            if (rateFrom <= 0 || rateTo <= 0) return num;

            var result = (num / rateFrom) * rateTo;
            _lruCache.set(cacheKey, result);
            return result;
        } catch (e) {
            return typeof amount === 'number' ? amount : 0;
        }
    }

    function safeFormat(amount, currencyCode, options) {
        options = options || {};
        try {
            var curr = (currencyCode || getActiveCurrencyCode()).toUpperCase();
            var symbol = CURRENCY_SYMBOLS[curr] || curr + ' ';
            var noDecimals = ['JPY', 'KRW', 'VND'].indexOf(curr) !== -1;
            var decimals = options.decimals !== undefined ? options.decimals : (noDecimals ? 0 : 2);
            var num = parseFloat(amount) || 0;

            var formattedNum = num.toLocaleString('en-US', {
                minimumFractionDigits: decimals,
                maximumFractionDigits: decimals
            });

            return symbol + formattedNum;
        } catch (e) {
            return (currencyCode || '') + ' ' + (amount || 0);
        }
    }

    function getActiveCurrencyCode() {
        if (!salaryCurrencySelect || salaryCurrencySelect.selectedIndex < 0) return 'INR';
        var opt = salaryCurrencySelect.options[salaryCurrencySelect.selectedIndex];
        var code = opt.getAttribute('data-code');
        if (!code) {
            code = opt.text.trim().split('—')[0].split('-')[0].replace(/[^\w]/g, '').trim();
        }
        return (code || 'INR').toUpperCase();
    }

    function fieldValue(id) {
        var field = root.querySelector('#' + id);
        var value = field ? parseFloat(field.value) : 0;
        return Number.isFinite(value) ? value : 0;
    }

    function activeSalaryPeriod() {
        var activeButton = root.querySelector('.candidate-salary-tabs button.is-active[data-salary-group="current"]');
        return activeButton ? activeButton.getAttribute('data-salary-period') : 'annual';
    }

    function salaryValuesFromPeriod(period, amount) {
        var values = { annual: 0, monthly: 0, hourly: 0 };
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

    // 2-Way Input Synchronizer with Mutex Guard
    function syncInputsForGroup(group, sourcePeriod) {
        if (_isUpdating) return;
        _isUpdating = true;
        try {
            var sourceId = salaryFieldMap[group][sourcePeriod];
            var sourceVal = fieldValue(sourceId);
            var values = salaryValuesFromPeriod(sourcePeriod, sourceVal);

            ['annual', 'monthly', 'hourly'].forEach(function (period) {
                if (period !== sourcePeriod) {
                    var targetField = root.querySelector('#' + salaryFieldMap[group][period]);
                    if (targetField) {
                        targetField.value = values[period] > 0 ? (values[period] % 1 === 0 ? values[period] : values[period].toFixed(2)) : '';
                    }
                }
            });
        } finally {
            _isUpdating = false;
        }
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
        var curr = getActiveCurrencyCode();

        if (labelOne) labelOne.textContent = salaryPeriodLabels[otherPeriods[0]] + ' Salary';
        if (labelTwo) labelTwo.textContent = salaryPeriodLabels[otherPeriods[1]] + ' Salary';
        if (outputOne) outputOne.textContent = safeFormat(values[otherPeriods[0]], curr);
        if (outputTwo) outputTwo.textContent = safeFormat(values[otherPeriods[1]], curr);
    }

    // Live Indicative USD Preview Pills (RULE-COMMERCIAL-005)
    function updateIndicativePills() {
        var curr = getActiveCurrencyCode();
        var isUSD = (curr === 'USD');

        var configs = [
            { id: 'pay_rate_annual_usd',  field: 'pay_annual',  freq: 'yr' },
            { id: 'pay_rate_monthly_usd', field: 'pay_monthly', freq: 'mo' },
            { id: 'pay_rate_hourly_usd',  field: 'ctc',         freq: 'hr' },
            { id: 'bill_rate_annual_usd',  field: 'bill_annual',  freq: 'yr' },
            { id: 'bill_rate_monthly_usd', field: 'bill_monthly', freq: 'mo' },
            { id: 'bill_rate_hourly_usd',  field: 'ectc',         freq: 'hr' }
        ];

        configs.forEach(function (cfg) {
            var el = root.querySelector('#' + cfg.id);
            if (!el) return;

            var amount = fieldValue(cfg.field);

            if (isUSD) {
                el.innerHTML = '<span class="jero-indicative-pill jero-indicative-pill--base" title="Base currency USD"><i class="fa-solid fa-check-circle fs-9 me-1"></i>Base USD</span>';
            } else {
                var usdAmount = safeConvert(amount, curr, 'USD');
                var formatted = (usdAmount > 0)
                    ? '$ ' + usdAmount.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })
                    : '$ 0.00';

                el.innerHTML = '<span class="jero-indicative-pill jero-indicative-pill--converted" data-amount="' + amount + '" data-curr="' + curr + '" data-freq="' + cfg.freq + '" title="Click to view 5 global benchmarks or switch currency"><i class="fa-solid fa-dollar-sign fs-9 me-1"></i>≈ ' + formatted + ' / ' + cfg.freq + ' USD <i class="fa-solid fa-circle-info fs-9 ms-1"></i></span>';
            }
        });
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
        var usdSummaryTarget = root.querySelector('#candidateSalaryUsdEquivalentSummary');
        var currency = getActiveCurrencyCode();
        var difference = expectedAmount - currentAmount;
        var calculatedIncrease = currentAmount > 0 && expectedAmount > 0
            ? (difference / currentAmount) * 100
            : 0;

        if (currentTarget) currentTarget.textContent = safeFormat(currentAmount, currency);
        if (expectedTarget) expectedTarget.textContent = safeFormat(expectedAmount, currency);
        if (currentLabel) currentLabel.textContent = 'Current Salary (' + salaryPeriodLabels[period] + ')';
        if (expectedLabel) expectedLabel.textContent = 'Expected Salary (' + salaryPeriodLabels[period] + ')';
        if (differenceOutput) differenceOutput.textContent = safeFormat(difference, currency);
        if (increaseOutput) increaseOutput.textContent = calculatedIncrease.toFixed(2) + '%';
        if (hikeInput) hikeInput.value = calculatedIncrease ? String(Math.round(calculatedIncrease * 100) / 100) : '0';

        // USD Equivalent (Annual) in Summary
        if (usdSummaryTarget) {
            var baseSummaryAnnual = expectedAmount > 0 ? salaryValuesFromPeriod(period, expectedAmount).annual : salaryValuesFromPeriod(period, currentAmount).annual;
            var summaryUsd = safeConvert(baseSummaryAnnual, currency, 'USD');
            usdSummaryTarget.textContent = safeFormat(summaryUsd, 'USD', { decimals: 2 }) + ' / yr';
        }

        updateSalaryAutoValues('current', period);
        updateSalaryAutoValues('expected', period);
        updateIndicativePills();
    }

    function syncSalaryPeriod(period) {
        var modeByPeriod = { annual: '0', monthly: '1', hourly: '2' };
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
        if (currentMode) currentMode.value = modeByPeriod[normalizedPeriod];
        if (expectedMode) expectedMode.value = modeByPeriod[normalizedPeriod];

        var currentHint = root.querySelector('#candidateCurrentSalaryHint');
        var expectedHint = root.querySelector('#candidateExpectedSalaryHint');
        if (currentHint) currentHint.textContent = 'Enter your current ' + normalizedPeriod + ' salary.';
        if (expectedHint) expectedHint.textContent = 'Enter your expected ' + normalizedPeriod + ' salary.';

        updateSalarySummary();
    }

    function syncSalaryCurrency() {
        if (!salaryCurrencySelect) return;
        var code = getActiveCurrencyCode();

        salaryCurrencyMirrors.forEach(function (select) {
            if (select) select.value = salaryCurrencySelect.value;
        });

        root.querySelectorAll('.changeTextCurrency').forEach(function (label) {
            label.textContent = code;
        });

        updateSalarySummary();
        if (typeof updateCTCFields === 'function') {
            updateCTCFields();
        }
    }

    // Multi-Currency Hover Lens Popover (Section 15.1)
    var _hoverLensEl = null;
    var _hoverLensTimeout = null;

    function getOrCreateHoverLens() {
        if (!_hoverLensEl) {
            _hoverLensEl = document.createElement('div');
            _hoverLensEl.className = 'jero-currency-hover-lens';
            _hoverLensEl.id = 'candidateSalaryHoverLens';
            _hoverLensEl.setAttribute('role', 'tooltip');
            _hoverLensEl.innerHTML =
                '<div class="jero-hover-lens-header">' +
                    '<span><i class="fa-solid fa-globe"></i> GLOBAL BENCHMARKS</span>' +
                    '<span class="jero-hover-lens-badge">User RAM</span>' +
                '</div>' +
                '<div class="jero-hover-lens-base" id="jeroHoverLensBase"></div>' +
                '<div class="jero-hover-lens-body" id="jeroHoverLensBody"></div>' +
                '<div class="jero-hover-lens-footer">' +
                    '<span><i class="fa-solid fa-bolt"></i> Live 60 FPS • FX: ' + fxVersion + '</span>' +
                    '<span style="color:#059669;font-weight:600;cursor:pointer;" id="jeroHoverLensSwitchCta">Change Currency ▾</span>' +
                '</div>';
            document.body.appendChild(_hoverLensEl);

            _hoverLensEl.addEventListener('mouseenter', function () {
                clearTimeout(_hoverLensTimeout);
            });
            _hoverLensEl.addEventListener('mouseleave', function () {
                hideHoverLens();
            });

            var switchCta = _hoverLensEl.querySelector('#jeroHoverLensSwitchCta');
            if (switchCta) {
                switchCta.addEventListener('click', function () {
                    hideHoverLens();
                    if (window.jQuery && $.fn.select2 && $(salaryCurrencySelect).hasClass('select2-hidden-accessible')) {
                        $(salaryCurrencySelect).select2('open');
                    } else {
                        salaryCurrencySelect.focus();
                    }
                });
            }
        }
        return _hoverLensEl;
    }

    function showHoverLens(targetEl) {
        clearTimeout(_hoverLensTimeout);
        var lens = getOrCreateHoverLens();
        var baseCurr = getActiveCurrencyCode();

        var period = activeSalaryPeriod();
        var rawAmount = 0;
        var freq = 'yr';

        var pill = targetEl.closest('.jero-indicative-pill');
        if (pill) {
            rawAmount = parseFloat(pill.getAttribute('data-amount')) || 0;
            freq = pill.getAttribute('data-freq') || 'yr';
        } else {
            rawAmount = fieldValue(salaryFieldMap.expected[period]) || fieldValue(salaryFieldMap.current[period]);
            freq = period === 'hourly' ? 'hr' : (period === 'monthly' ? 'mo' : 'yr');
        }

        var annualAmount = 0;
        if (freq === 'yr') annualAmount = rawAmount;
        else if (freq === 'mo') annualAmount = rawAmount * 12;
        else annualAmount = rawAmount * 160 * 12;

        var hourlyAmount = annualAmount / 1920;

        var baseContainer = lens.querySelector('#jeroHoverLensBase');
        if (baseContainer) {
            baseContainer.innerHTML = 'Base Rate: <strong>' + safeFormat(rawAmount, baseCurr) + ' / ' + freq + '</strong> <small>(' + safeFormat(hourlyAmount, baseCurr) + '/hr)</small>';
        }

        var defaultBenchmarks = ['USD', 'EUR', 'GBP', 'AED', 'SGD'];
        var alternates = ['CAD', 'AUD', 'JPY', 'CHF'];
        var benchmarks = [];

        defaultBenchmarks.forEach(function (b) {
            if (b !== baseCurr && benchmarks.length < 5) {
                benchmarks.push(b);
            }
        });
        alternates.forEach(function (a) {
            if (a !== baseCurr && benchmarks.length < 5) {
                benchmarks.push(a);
            }
        });

        var bodyContainer = lens.querySelector('#jeroHoverLensBody');
        var rowsHtml = '';
        benchmarks.forEach(function (bCurr) {
            var bAnnual = safeConvert(annualAmount, baseCurr, bCurr);
            var bHourly = bAnnual / 1920;
            var flag = CURRENCY_FLAGS[bCurr] || '🌐';

            rowsHtml +=
                '<div class="jero-hover-lens-row">' +
                    '<div class="jero-hover-lens-curr">' +
                        '<span class="jero-hover-lens-flag">' + flag + '</span>' +
                        '<strong>' + bCurr + '</strong>' +
                    '</div>' +
                    '<div class="jero-hover-lens-vals">' +
                        '<span class="jero-hover-lens-primary">' + safeFormat(bAnnual, bCurr, { decimals: 0 }) + ' / yr</span>' +
                        '<span class="jero-hover-lens-sub">(' + safeFormat(bHourly, bCurr, { decimals: 2 }) + ' / hr)</span>' +
                    '</div>' +
                '</div>';
        });
        if (bodyContainer) bodyContainer.innerHTML = rowsHtml;

        var rect = targetEl.getBoundingClientRect();
        var lensWidth = 320;
        var top = rect.bottom + window.scrollY + 6;
        var left = rect.left + window.scrollX - 20;

        if (left + lensWidth > window.innerWidth - 10) {
            left = window.innerWidth - lensWidth - 15;
        }
        if (left < 10) left = 10;

        lens.style.top = top + 'px';
        lens.style.left = left + 'px';
        lens.classList.add('is-visible');
    }

    function hideHoverLens() {
        _hoverLensTimeout = setTimeout(function () {
            if (_hoverLensEl) {
                _hoverLensEl.classList.remove('is-visible');
            }
        }, 150);
    }

    $(document).on('mouseenter focusin', '.jero-indicative-pill, .changeTextCurrency, #candidateSalaryUsdEquivalentRow', function () {
        showHoverLens(this);
    });
    $(document).on('mouseleave focusout', '.jero-indicative-pill, .changeTextCurrency, #candidateSalaryUsdEquivalentRow', function () {
        hideHoverLens();
    });

    root.querySelectorAll('.candidate-salary-tabs button[data-salary-period]').forEach(function (button) {
        button.addEventListener('click', function () {
            syncSalaryPeriod(button.getAttribute('data-salary-period'));
        });
    });

    var debounceTimers = {};
    function debounceInput(id, fn, delay) {
        clearTimeout(debounceTimers[id]);
        debounceTimers[id] = setTimeout(fn, delay || 120);
    }

    [
        { id: 'pay_annual',  group: 'current',  period: 'annual' },
        { id: 'pay_monthly', group: 'current',  period: 'monthly' },
        { id: 'ctc',         group: 'current',  period: 'hourly' },
        { id: 'bill_annual',  group: 'expected', period: 'annual' },
        { id: 'bill_monthly', group: 'expected', period: 'monthly' },
        { id: 'ectc',         group: 'expected', period: 'hourly' }
    ].forEach(function (item) {
        var field = root.querySelector('#' + item.id);
        if (field) {
            field.addEventListener('input', function () {
                debounceInput(item.id, function () {
                    syncInputsForGroup(item.group, item.period);
                    updateSalarySummary();
                    if (typeof updateCTCFields === 'function') {
                        updateCTCFields();
                    }
                }, 120);
            });
            field.addEventListener('change', function () {
                syncInputsForGroup(item.group, item.period);
                updateSalarySummary();
                if (typeof updateCTCFields === 'function') {
                    updateCTCFields();
                }
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
                    var symbol = ($el.data('symbol') || '').toString().toLowerCase();
                    var searchKw = ($el.data('search') || '').toString().toLowerCase();

                    if (text.indexOf(term) > -1 || code.indexOf(term) > -1 || symbol.indexOf(term) > -1 || searchKw.indexOf(term) > -1) {
                        return data;
                    }
                    return null;
                }
            }).on('change', function () {
                syncSalaryCurrency();
            });
        }
    }

    var commentField = root.querySelector('#comment');
    var commentCounter = root.querySelector('#salaryCommentCounter');
    if (commentField && commentCounter) {
        function updateCommentCount() {
            commentCounter.textContent = (commentField.value || '').length + ' / 500';
        }
        commentField.addEventListener('input', updateCommentCount);
        updateCommentCount();
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

    // ── GOD-LEVEL ENTERPRISE TIME CLOCK PICKER ──
    function initJeroGodLevelTimePicker(fieldIds, formatStr) {
        var is12Hour = (formatStr !== 'H:i');
        var activeInput = null;
        var currentHour = 10;
        var currentMin = 0;
        var currentAmPm = 'AM';

        var popover = document.getElementById('jero-god-time-picker');
        if (!popover) {
            popover = document.createElement('div');
            popover.id = 'jero-god-time-picker';
            popover.className = 'jgtp-popover';
            popover.innerHTML = [
                '<div class="jgtp-header">',
                '  <div class="jgtp-header-top">',
                '    <span class="jgtp-slot-badge"><i class="fa-regular fa-clock me-1"></i> <span id="jgtp-slot-label">Slot 1 Interview Time</span></span>',
                '    <button type="button" class="jgtp-close-btn" id="jgtp-close" aria-label="Close"><i class="fa-solid fa-xmark"></i></button>',
                '  </div>',
                '  <div class="jgtp-display-row">',
                '    <div class="jgtp-digits">',
                '      <span class="jgtp-digit jgtp-hour-display" id="jgtp-hour-display">10</span>',
                '      <span class="jgtp-colon">:</span>',
                '      <span class="jgtp-digit jgtp-min-display" id="jgtp-min-display">00</span>',
                '    </div>',
                '    <div class="jgtp-ampm-switch" id="jgtp-ampm-switch">',
                '      <button type="button" class="jgtp-ampm-btn is-active" data-ampm="AM">AM</button>',
                '      <button type="button" class="jgtp-ampm-btn" data-ampm="PM">PM</button>',
                '    </div>',
                '  </div>',
                '</div>',
                '<div class="jgtp-body">',
                '  <div class="jgtp-section">',
                '    <div class="jgtp-section-label"><i class="fa-solid fa-bolt me-1 text-warning"></i> Quick Interview Slots</div>',
                '    <div class="jgtp-presets-grid" id="jgtp-presets-grid">',
                '      <button type="button" class="jgtp-preset-chip" data-time="09:00 AM">09:00 AM</button>',
                '      <button type="button" class="jgtp-preset-chip" data-time="10:00 AM">10:00 AM</button>',
                '      <button type="button" class="jgtp-preset-chip" data-time="11:00 AM">11:00 AM</button>',
                '      <button type="button" class="jgtp-preset-chip" data-time="11:30 AM">11:30 AM</button>',
                '      <button type="button" class="jgtp-preset-chip" data-time="12:00 PM">12:00 PM</button>',
                '      <button type="button" class="jgtp-preset-chip" data-time="02:00 PM">02:00 PM</button>',
                '      <button type="button" class="jgtp-preset-chip" data-time="03:00 PM">03:00 PM</button>',
                '      <button type="button" class="jgtp-preset-chip" data-time="04:00 PM">04:00 PM</button>',
                '      <button type="button" class="jgtp-preset-chip" data-time="05:00 PM">05:00 PM</button>',
                '      <button type="button" class="jgtp-preset-chip" data-time="06:00 PM">06:00 PM</button>',
                '    </div>',
                '  </div>',
                '  <div class="jgtp-section">',
                '    <div class="jgtp-section-label"><i class="fa-regular fa-clock me-1 text-primary"></i> Select Hour</div>',
                '    <div class="jgtp-hours-grid" id="jgtp-hours-grid"></div>',
                '  </div>',
                '  <div class="jgtp-section">',
                '    <div class="jgtp-section-label"><i class="fa-solid fa-stopwatch me-1 text-info"></i> Select Minute</div>',
                '    <div class="jgtp-mins-grid" id="jgtp-mins-grid">',
                '      <button type="button" class="jgtp-min-btn is-active" data-min="00">:00</button>',
                '      <button type="button" class="jgtp-min-btn" data-min="15">:15</button>',
                '      <button type="button" class="jgtp-min-btn" data-min="30">:30</button>',
                '      <button type="button" class="jgtp-min-btn" data-min="45">:45</button>',
                '    </div>',
                '    <div class="jgtp-mins-subgrid" id="jgtp-mins-subgrid">',
                '      <button type="button" class="jgtp-min-subchip" data-min="05">:05</button>',
                '      <button type="button" class="jgtp-min-subchip" data-min="10">:10</button>',
                '      <button type="button" class="jgtp-min-subchip" data-min="20">:20</button>',
                '      <button type="button" class="jgtp-min-subchip" data-min="25">:25</button>',
                '      <button type="button" class="jgtp-min-subchip" data-min="35">:35</button>',
                '      <button type="button" class="jgtp-min-subchip" data-min="40">:40</button>',
                '      <button type="button" class="jgtp-min-subchip" data-min="50">:50</button>',
                '      <button type="button" class="jgtp-min-subchip" data-min="55">:55</button>',
                '    </div>',
                '  </div>',
                '</div>',
                '<div class="jgtp-footer">',
                '  <button type="button" class="jgtp-btn jgtp-btn-clear" id="jgtp-btn-clear">Clear</button>',
                '  <button type="button" class="jgtp-btn jgtp-btn-now" id="jgtp-btn-now">Current Time</button>',
                '  <button type="button" class="jgtp-btn jgtp-btn-apply" id="jgtp-btn-apply"><i class="fa-solid fa-check me-1"></i> Done</button>',
                '</div>'
            ].join('');
            document.body.appendChild(popover);

            var hoursGrid = popover.querySelector('#jgtp-hours-grid');
            for (var h = 1; h <= 12; h++) {
                var btn = document.createElement('button');
                btn.type = 'button';
                btn.className = 'jgtp-hour-btn';
                var hStr = (h < 10 ? '0' : '') + h;
                btn.setAttribute('data-hour', h);
                btn.textContent = hStr;
                hoursGrid.appendChild(btn);
            }
        }

        var hourDisplay = popover.querySelector('#jgtp-hour-display');
        var minDisplay = popover.querySelector('#jgtp-min-display');
        var ampmSwitch = popover.querySelector('#jgtp-ampm-switch');
        var slotLabel = popover.querySelector('#jgtp-slot-label');

        function formatFormattedTime(h, m, ap) {
            var hStr = (h < 10 ? '0' : '') + h;
            var mStr = (m < 10 ? '0' : '') + m;
            if (is12Hour) {
                return hStr + ':' + mStr + ' ' + ap;
            } else {
                var h24 = h;
                if (ap === 'PM' && h24 < 12) h24 += 12;
                if (ap === 'AM' && h24 === 12) h24 = 0;
                return ((h24 < 10 ? '0' : '') + h24) + ':' + mStr;
            }
        }

        function updateDisplay() {
            var hStr = (currentHour < 10 ? '0' : '') + currentHour;
            var mStr = (currentMin < 10 ? '0' : '') + currentMin;
            hourDisplay.textContent = hStr;
            minDisplay.textContent = mStr;

            popover.querySelectorAll('.jgtp-ampm-btn').forEach(function (btn) {
                btn.classList.toggle('is-active', btn.getAttribute('data-ampm') === currentAmPm);
            });

            popover.querySelectorAll('.jgtp-hour-btn').forEach(function (btn) {
                btn.classList.toggle('is-active', parseInt(btn.getAttribute('data-hour'), 10) === currentHour);
            });

            var mPadded = (currentMin < 10 ? '0' : '') + currentMin;
            popover.querySelectorAll('.jgtp-min-btn, .jgtp-min-subchip').forEach(function (btn) {
                btn.classList.toggle('is-active', btn.getAttribute('data-min') === mPadded);
            });

            var currentTimeStr = formatFormattedTime(currentHour, currentMin, currentAmPm);
            popover.querySelectorAll('.jgtp-preset-chip').forEach(function (btn) {
                btn.classList.toggle('is-active', btn.getAttribute('data-time') === currentTimeStr);
            });
        }

        function parseTimeValue(val) {
            if (!val) return null;
            var m = String(val).trim().match(/^(\d{1,2}):(\d{2})(?:\s*(AM|PM))?$/i);
            if (!m) return null;
            var h = parseInt(m[1], 10);
            var min = parseInt(m[2], 10);
            var ap = m[3] ? m[3].toUpperCase() : null;
            if (!ap) {
                if (h >= 12) {
                    ap = 'PM';
                    if (h > 12) h -= 12;
                } else {
                    ap = 'AM';
                    if (h === 0) h = 12;
                }
            }
            return { hour: h, min: min, ampm: ap };
        }

        function positionPicker(input) {
            if (!input) return;
            var vh = window.innerHeight;
            var vw = window.innerWidth;
            var rect = input.getBoundingClientRect();
            var popoverWidth = 310;
            var popoverHeight = popover.offsetHeight || 295;

            var spaceBelow = vh - rect.bottom - 8;
            var spaceAbove = rect.top - 8;

            var top;

            if (spaceBelow >= popoverHeight) {
                // Comfortable room below input
                top = rect.bottom + 4;
            } else if (spaceAbove >= popoverHeight) {
                // Comfortable room above input
                top = rect.top - popoverHeight - 4;
            } else {
                // Viewport is constrained (e.g. 15.6" screen / small viewport)
                // Pick whichever side has more room and clamp
                if (spaceBelow >= spaceAbove) {
                    top = rect.bottom + 4;
                    var availBelow = Math.max(180, spaceBelow);
                    popover.style.maxHeight = availBelow + 'px';
                } else {
                    var availAbove = Math.max(180, spaceAbove);
                    top = Math.max(8, rect.top - availAbove - 4);
                    popover.style.maxHeight = availAbove + 'px';
                }
            }

            // Boundary clamping to ensure popover never spills offscreen
            if (top < 8) {
                top = 8;
            }
            if (top + popoverHeight > vh - 8) {
                top = Math.max(8, vh - popoverHeight - 8);
            }

            // If still overflows viewport bottom, enforce maxHeight
            if (top + (popover.offsetHeight || popoverHeight) > vh - 8) {
                popover.style.maxHeight = Math.max(180, vh - top - 8) + 'px';
            }

            // Horizontal alignment with boundary protection
            var left = rect.left;
            if (left + popoverWidth > vw - 10) {
                left = Math.max(10, vw - popoverWidth - 10);
            }
            if (left < 10) {
                left = 10;
            }

            popover.style.top = Math.round(top) + 'px';
            popover.style.left = Math.round(left) + 'px';
            popover.style.width = popoverWidth + 'px';
        }

        function openPicker(input) {
            activeInput = input;
            var val = input.value;
            var parsed = parseTimeValue(val);
            if (parsed) {
                currentHour = parsed.hour;
                currentMin = parsed.min;
                currentAmPm = parsed.ampm;
            } else {
                currentHour = 10;
                currentMin = 0;
                currentAmPm = 'AM';
            }

            var slotName = 'Slot 1';
            if (input.id === 'interviewTimeTwo') slotName = 'Slot 2';
            else if (input.id === 'interviewTimeThree') slotName = 'Slot 3';
            slotLabel.textContent = slotName + ' Interview Time';

            updateDisplay();

            // Clear previous max-height constraint before recalculation
            popover.style.maxHeight = '';

            popover.classList.add('is-open');
            input.classList.add('is-picker-active');

            positionPicker(input);
        }

        function closePicker() {
            if (!popover.classList.contains('is-open')) return;
            popover.classList.remove('is-open');
            popover.style.maxHeight = '';
            if (activeInput) {
                activeInput.classList.remove('is-picker-active');
                activeInput = null;
            }
        }

        function applyTime() {
            if (!activeInput) return;
            var formatted = formatFormattedTime(currentHour, currentMin, currentAmPm);
            activeInput.value = formatted;
            activeInput.dispatchEvent(new Event('input', { bubbles: true }));
            activeInput.dispatchEvent(new Event('change', { bubbles: true }));
            closePicker();
        }

        popover.querySelector('#jgtp-close').onclick = closePicker;
        popover.querySelector('#jgtp-btn-apply').onclick = applyTime;
        popover.querySelector('#jgtp-btn-clear').onclick = function () {
            if (activeInput) {
                activeInput.value = '';
                activeInput.dispatchEvent(new Event('input', { bubbles: true }));
                activeInput.dispatchEvent(new Event('change', { bubbles: true }));
            }
            closePicker();
        };
        popover.querySelector('#jgtp-btn-now').onclick = function () {
            var now = new Date();
            var h = now.getHours();
            var m = Math.round(now.getMinutes() / 15) * 15;
            if (m === 60) {
                m = 0;
                h += 1;
            }
            var ap = h >= 12 ? 'PM' : 'AM';
            h = h % 12;
            if (h === 0) h = 12;
            currentHour = h;
            currentMin = m;
            currentAmPm = ap;
            updateDisplay();
        };

        popover.querySelectorAll('.jgtp-ampm-btn').forEach(function (btn) {
            btn.onclick = function () {
                currentAmPm = btn.getAttribute('data-ampm');
                updateDisplay();
            };
        });

        popover.querySelectorAll('.jgtp-hour-btn').forEach(function (btn) {
            btn.onclick = function () {
                currentHour = parseInt(btn.getAttribute('data-hour'), 10);
                updateDisplay();
            };
        });

        popover.querySelectorAll('.jgtp-min-btn, .jgtp-min-subchip').forEach(function (btn) {
            btn.onclick = function () {
                currentMin = parseInt(btn.getAttribute('data-min'), 10);
                updateDisplay();
            };
        });

        popover.querySelectorAll('.jgtp-preset-chip').forEach(function (btn) {
            btn.onclick = function () {
                var tVal = btn.getAttribute('data-time');
                var parsed = parseTimeValue(tVal);
                if (parsed) {
                    currentHour = parsed.hour;
                    currentMin = parsed.min;
                    currentAmPm = parsed.ampm;
                    updateDisplay();
                }
                if (activeInput) {
                    activeInput.value = formatFormattedTime(currentHour, currentMin, currentAmPm);
                    activeInput.dispatchEvent(new Event('input', { bubbles: true }));
                    activeInput.dispatchEvent(new Event('change', { bubbles: true }));
                }
                closePicker();
            };
        });

        fieldIds.forEach(function (id) {
            var input = document.getElementById(id);
            if (!input) return;
            input.readOnly = true;
            input.style.cursor = 'pointer';
            input.onclick = function (e) {
                e.stopPropagation();
                if (activeInput === input && popover.classList.contains('is-open')) {
                    closePicker();
                } else {
                    openPicker(input);
                }
            };
            input.onfocus = function () {
                if (activeInput !== input) {
                    openPicker(input);
                }
            };
        });

        document.addEventListener('click', function (e) {
            if (!popover.classList.contains('is-open')) return;
            if (!popover.contains(e.target) && (!activeInput || !activeInput.contains(e.target))) {
                closePicker();
            }
        });

        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape' && popover.classList.contains('is-open')) {
                closePicker();
            }
        });

        window.addEventListener('scroll', function () {
            if (activeInput && popover.classList.contains('is-open')) {
                positionPicker(activeInput);
            }
        }, true);

        window.addEventListener('resize', function () {
            if (activeInput && popover.classList.contains('is-open')) {
                positionPicker(activeInput);
            }
        });
    }

    // Initialize Flatpickr for Date fields ONLY (Date calendar)
    if (typeof window.flatpickr === 'function') {
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
    } else {
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
    }

    // Initialize God-Level Time Clock Picker on Availability time inputs
    initJeroGodLevelTimePicker(timeFieldIds, timeFormat);
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

<script>
/**
 * Enterprise Work Authorization & 240+ Country Compliance Engine
 * Client-First Execution (User RAM & Browser CPU First - Rule 3, Rule 17)
 */
$(document).ready(function () {
    function initWorkAuthorizationEngine() {
        if (!$.fn || !$.fn.select2) {
            setTimeout(initWorkAuthorizationEngine, 50);
            return;
        }

        var $select = $('#visastatu');
        if (!$select.length) return;

        // Idempotency check: if already active, sync chips and return
        if ($select.hasClass('select2-hidden-accessible')) {
            syncWorkAuthActiveChip();
            return;
        }

        $select.select2({
            theme: 'default',
            width: '100%',
            dropdownParent: $(document.body),
            dropdownCssClass: 'wa-select2-dropdown',
            placeholder: 'Search visa / work authorization or country...',
            allowClear: true,
            minimumResultsForSearch: 0,
            templateSelection: function (item) {
                if (!item || !item.id || item.id === '') {
                    return item ? (item.text || 'Select work authorization or visa status') : 'Select work authorization or visa status';
                }
                return item.text;
            },
            matcher: function (params, data) {
                if (!params.term || $.trim(params.term) === '') {
                    return data;
                }
                var term = params.term.toLowerCase().trim();

                // If optgroup: check if optgroup label matches search term
                if (data.children && data.children.length > 0) {
                    var groupLabel = (data.text || '').toLowerCase();
                    if (groupLabel.indexOf(term) > -1) {
                        return data;
                    }

                    // Filter matched child options
                    var matchedChildren = [];
                    for (var i = 0; i < data.children.length; i++) {
                        var child = data.children[i];
                        var childText = (child.text || '').toLowerCase();
                        var childVal = (child.id || '').toLowerCase();
                        if (childText.indexOf(term) > -1 || childVal.indexOf(term) > -1) {
                            matchedChildren.push(child);
                        }
                    }
                    if (matchedChildren.length > 0) {
                        var cloned = $.extend({}, data, true);
                        cloned.children = matchedChildren;
                        return cloned;
                    }
                    return null;
                }

                // Leaf option check
                var text = (data.text || '').toLowerCase();
                var val = (data.id || '').toLowerCase();
                if (text.indexOf(term) > -1 || val.indexOf(term) > -1) {
                    return data;
                }
                return null;
            }
        });

        syncWorkAuthActiveChip();
    }

    function syncWorkAuthActiveChip() {
        var val = $('#visastatu').val();
        $('.wa-chip-btn').removeClass('is-active');
        if (!val) return;

        var $selectedOpt = $('#visastatu').find('option:selected');
        var $optgroup = $selectedOpt.closest('optgroup');
        var groupLabel = ($optgroup.attr('label') || '').toLowerCase();
        var optVal = (val || '').toLowerCase();

        $('.wa-chip-btn').each(function () {
            var filter = ($(this).data('filter') || '').toLowerCase();
            var region = ($(this).data('region') || '').toLowerCase();
            if ((groupLabel && (groupLabel.indexOf(filter) > -1 || groupLabel.indexOf(region) > -1)) ||
                optVal.indexOf(filter) > -1 || optVal.indexOf(region) > -1) {
                $(this).addClass('is-active');
                return false;
            }
        });
    }

    // Force search field visibility, placeholder and focus upon opening
    $(document).on('select2:open select2:opening', '#visastatu', function () {
        var $dropdown = $('.wa-select2-dropdown');
        $dropdown.find('.select2-search, .select2-search--dropdown').removeClass('select2-search--hide').css('display', 'block');
        setTimeout(function () {
            var searchField = document.querySelector('.wa-select2-dropdown .select2-search__field');
            if (searchField) {
                searchField.placeholder = 'Search visa status, permit or country (e.g. H-1B, Green Card)...';
                searchField.focus();
            }
        }, 30);
    });

    // Handle Quick Hub Chip click
    $(document).on('click', '.wa-chip-btn', function (e) {
        e.preventDefault();
        var $btn = $(this);
        var filterTerm = $btn.data('filter') || '';
        var wasActive = $btn.hasClass('is-active');

        $('.wa-chip-btn').removeClass('is-active');

        var $visaSelect = $('#visastatu');
        if (!$.fn.select2 || !$visaSelect.hasClass('select2-hidden-accessible')) {
            initWorkAuthorizationEngine();
        }

        if (wasActive) {
            if ($visaSelect.data('select2') && $visaSelect.data('select2').isOpen()) {
                var $sf = $('.wa-select2-dropdown .select2-search__field');
                if ($sf.length) {
                    $sf.val('').trigger('input').trigger('keyup');
                }
            }
            return;
        }

        $btn.addClass('is-active');

        if ($.fn.select2 && $visaSelect.hasClass('select2-hidden-accessible')) {
            $visaSelect.select2('open');
            setTimeout(function () {
                var $searchField = $('.wa-select2-dropdown .select2-search__field');
                if ($searchField.length) {
                    $searchField.val(filterTerm).trigger('input').trigger('keyup');
                }
            }, 50);
        } else {
            $visaSelect.focus();
        }
    });

    $(document).on('change', '#visastatu', function () {
        syncWorkAuthActiveChip();
    });

    // Make dropdown container click trigger Select2 open reliably without interfering with internal toggle
    $(document).on('click', '.addl-select-wrap', function (e) {
        if ($(e.target).closest('.select2-container').length) return;
        if ($(e.target).closest('.select2-selection__clear').length) return;
        var $visaSelect = $('#visastatu');
        if ($.fn.select2 && $visaSelect.hasClass('select2-hidden-accessible')) {
            if (!$visaSelect.data('select2') || !$visaSelect.data('select2').isOpen()) {
                $visaSelect.select2('open');
            }
        } else {
            initWorkAuthorizationEngine();
            if ($visaSelect.hasClass('select2-hidden-accessible')) {
                $visaSelect.select2('open');
            }
        }
    });

    // Initial boot
    initWorkAuthorizationEngine();
});
</script>
@endpush
