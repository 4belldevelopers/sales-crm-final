{{-- Phase 8 Frontend Optimization: Modular Form Styles Partial --}}
<link rel="stylesheet" href="{{ asset('assets/node_modules/select2/dist/css/select2.min.css') }}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<style>
body { overflow-x: hidden; }
.dropzone { padding: 1.8rem 1.75rem !important; border: 2px dashed #e6e7e8 !important; background: transparent !important; text-align: inherit !important; }
.font-size-icon { font-size: 1.3em; }
.dashed-border-top { border-top: 1px dashed gray; }
.form { clear: both; }
.form--disabled input, .form--disabled textarea, .form--disabled select { padding-left: 0; padding-top: 4px; border: 0 solid #efefef; background: white; height: auto; transition: all 0.2s ease-in-out; font-size: 1.05rem; font-weight: 500; color: #3f4254; text-transform: capitalize; }
.enabled_input { display: block; width: 100%; border-radius: 2px; border: 1px solid #efefef; outline: none; -webkit-appearance: none; background-color: #f5f8fa; border-color: #f5f8fa; color: #5e6278; padding: 0.5em; transition: color 0.2s ease, background-color 0.2s ease; }
#calender::-webkit-inner-spin-button,
#calender::-webkit-calendar-picker-indicator { display: none; -webkit-appearance: none; }
#dob::-webkit-inner-spin-button,
#dob::-webkit-calendar-picker-indicator { display: none; -webkit-appearance: none; }
@media only screen and (min-width: 1024px) and (max-width: 1440px) {
  #kt_activitiesRecruiter { width: 1025px; }
}
@media only screen and (min-width: 1440px) and (max-width: 2560px) {
  #kt_activitiesRecruiter { width: 1200px; }
}
.tagify .tagify__tag div .tagify__tag-text { color: #0c0c0c !important; font-family: 'Poppins', sans-serif !important; font-weight: 500 !important; font-size: 13px !important; }
.tagify { height: 38px !important; font-family: 'Poppins', sans-serif !important; }
.disablesectionOn.setOpacity { cursor: not-allowed; background: #8a878d; opacity: 0.5; }

/* 15.6" Screen Optimization Scoped to Bench Candidates Module (Rule 17) */
#talent_create_app {
    max-width: 100%;
}

/* Work Authorization & Compliance Card Polish */
#talent_create_app .addl-card--details .addl-hero-title strong {
    font-family: 'Poppins', sans-serif !important;
    font-size: 14px !important;
    font-weight: 600 !important;
    color: #181C32 !important;
}

#talent_create_app .addl-select-wrap select optgroup {
    font-family: 'Poppins', sans-serif !important;
    font-size: 12px !important;
    font-weight: 700 !important;
    color: #4F566B !important;
    background-color: #F8FAFC !important;
    padding: 6px 10px !important;
}

#talent_create_app .addl-select-wrap select option {
    font-family: 'Poppins', sans-serif !important;
    font-size: 13px !important;
    font-weight: 500 !important;
    color: #181C32 !important;
    background-color: #FFFFFF !important;
    padding: 6px 12px !important;
}

#talent_create_app .addl-info-strip i.fa-shield-halved {
    color: #2563EB !important;
}

/* Quick Hub Selection Pills */
#talent_create_app .wa-popular-chips-wrap {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 8px;
    margin-bottom: 10px;
    margin-top: 4px;
}

#talent_create_app .wa-chips-label {
    font-family: 'Poppins', sans-serif;
    font-size: 11.5px;
    font-weight: 600;
    color: #475569;
    display: inline-flex;
    align-items: center;
    white-space: nowrap;
}

#talent_create_app .wa-popular-chips {
    display: inline-flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 6px;
}

#talent_create_app .wa-chip-btn {
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

#talent_create_app .wa-chip-btn:hover {
    background: #EFF6FF;
    border-color: #93C5FD;
    color: #1D4ED8;
    transform: translateY(-1px);
}

#talent_create_app .wa-chip-btn.is-active {
    background: #2563EB !important;
    border-color: #2563EB !important;
    color: #FFFFFF !important;
    font-weight: 600 !important;
    box-shadow: 0 2px 6px rgba(37, 99, 235, 0.25) !important;
}

/* Work Authorization Select & Select2 Enhancement */
#talent_create_app .addl-select-wrap {
    position: relative !important;
    width: 100% !important;
    cursor: pointer !important;
}

/* Fallback/initial native select styled identically to 38px enterprise standard */
#talent_create_app .addl-select-wrap select.wa-select {
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

#talent_create_app .addl-select-wrap select.select2-hidden-accessible {
    position: absolute !important;
    left: -9999px !important;
    width: 1px !important;
    height: 1px !important;
    opacity: 0 !important;
    pointer-events: none !important;
    visibility: hidden !important;
}

#talent_create_app .addl-select-wrap .select2-container {
    width: 100% !important;
    display: block !important;
    cursor: pointer !important;
}

/* Enterprise standard height 38px strictly locked across default & bootstrap5 themes */
#talent_create_app .addl-select-wrap .select2-selection,
#talent_create_app .addl-select-wrap .select2-selection--single,
#talent_create_app .addl-select-wrap .select2-selection.form-select,
#talent_create_app .addl-select-wrap .select2-container--default .select2-selection--single,
#talent_create_app .addl-select-wrap .select2-container--bootstrap5 .select2-selection--single,
#talent_create_app .addl-select-wrap .select2-container .select2-selection--single {
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
    background-image: none !important; /* Strips out any duplicate Bootstrap 5 SVG chevron */
    box-sizing: border-box !important;
    cursor: pointer !important;
    transition: border-color .15s ease-in-out, box-shadow .15s ease-in-out !important;
}

#talent_create_app .addl-select-wrap .select2-container.select2-container--open .select2-selection--single,
#talent_create_app .addl-select-wrap .select2-container.select2-container--focus .select2-selection--single,
#talent_create_app .addl-select-wrap .select2-container--default.select2-container--open .select2-selection--single,
#talent_create_app .addl-select-wrap .select2-container--default.select2-container--focus .select2-selection--single,
#talent_create_app .addl-select-wrap .select2-container--bootstrap5.select2-container--open .select2-selection--single,
#talent_create_app .addl-select-wrap .select2-container--bootstrap5.select2-container--focus .select2-selection--single {
    border-color: #3B82F6 !important;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, .12) !important;
    outline: none !important;
}

/* High-contrast crisp enterprise text formatting for selected value across all themes */
#talent_create_app .addl-select-wrap .select2-selection__rendered,
#talent_create_app .addl-select-wrap .select2-container--default .select2-selection--single .select2-selection__rendered,
#talent_create_app .addl-select-wrap .select2-container--bootstrap5 .select2-selection--single .select2-selection__rendered,
#talent_create_app .addl-select-wrap .select2-container .select2-selection--single .select2-selection__rendered {
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

/* Muted placeholder text strictly when no option is chosen */
#talent_create_app .addl-select-wrap .select2-selection__placeholder,
#talent_create_app .addl-select-wrap .select2-container--default .select2-selection--single .select2-selection__placeholder,
#talent_create_app .addl-select-wrap .select2-container--bootstrap5 .select2-selection--single .select2-selection__placeholder,
#talent_create_app .addl-select-wrap .select2-selection__rendered .select2-selection__placeholder {
    color: #8A94B4 !important;
    font-size: 13px !important;
    font-weight: 400 !important;
    line-height: 36px !important;
}

/* Clear button (x) */
#talent_create_app .addl-select-wrap .select2-selection__clear,
#talent_create_app .addl-select-wrap .select2-container--default .select2-selection--single .select2-selection__clear,
#talent_create_app .addl-select-wrap .select2-container--bootstrap5 .select2-selection--single .select2-selection__clear {
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

#talent_create_app .addl-select-wrap .select2-selection__clear:hover {
    color: #EF4444 !important;
}

/* Sleek dropdown chevron arrow centered vertically at 38px */
#talent_create_app .addl-select-wrap .select2-selection__arrow,
#talent_create_app .addl-select-wrap .select2-container--default .select2-selection--single .select2-selection__arrow,
#talent_create_app .addl-select-wrap .select2-container--bootstrap5 .select2-selection--single .select2-selection__arrow {
    height: 36px !important;
    top: 1px !important;
    right: 12px !important;
    width: 20px !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    pointer-events: none !important;
}

/* Hide default Select2 b triangle and use clean rotating chevron */
#talent_create_app .addl-select-wrap .select2-selection__arrow b {
    display: none !important;
}

#talent_create_app .addl-select-wrap .select2-selection__arrow::after {
    content: "\f078";
    font-family: "Font Awesome 6 Free", "Font Awesome 5 Free", "FontAwesome" !important;
    font-weight: 900 !important;
    font-size: 11px !important;
    color: #64748B !important;
    transition: transform 0.2s ease, color 0.2s ease !important;
}

#talent_create_app .addl-select-wrap .select2-container.select2-container--open .select2-selection__arrow::after,
#talent_create_app .addl-select-wrap .select2-container--open .select2-selection__arrow::after {
    transform: rotate(180deg) !important;
    color: #2563EB !important;
}

/* Suppress redundant icons */
#talent_create_app .addl-select-wrap > i.fa-chevron-down,
#talent_create_app .addl-select-wrap .select2-container ~ i.fa-chevron-down {
    display: none !important;
}

/* Work Authorization Select2 Dropdown Menu (Appended to Body) */
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

/* Dropdown Search Bar: ALWAYS visible, never hidden */
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
    border-color: #3B82F6 !important;
    box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.2) !important;
}

.wa-select2-dropdown .select2-results__options {
    max-height: 320px !important;
    overflow-y: auto !important;
    padding: 4px 0 !important;
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
    background-color: #3B82F6 !important;
    color: #FFFFFF !important;
}

.wa-select2-dropdown .select2-results__option[aria-selected="true"] {
    background-color: #EFF6FF !important;
    color: #1D4ED8 !important;
    font-weight: 600 !important;
}

/* Equal Height Flex Pairing for Resume Upload and Career Summary */
#talent_create_app .equal-height-row {
    display: flex;
    flex-wrap: wrap;
}

#talent_create_app .equal-height-row > [class*='col-'] {
    display: flex;
    flex-direction: column;
}

#talent_create_app .equal-height-row .card {
    flex: 1 1 auto;
    margin-bottom: 1.25rem;
}

/* Dropzone Collapse: Completely Hide Upload Area When File Present */
#talent_create_app .dropzone.has-file,
.dropzone.has-file {
    display: none !important;
}

/* Single Unified Remove Action Badge */
#talent_create_app .uploaded-file-badge {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0.75rem 1rem;
    background: #f4f6fa;
    border: 1px solid #e1e3ea;
    border-radius: 0.475rem;
}

/* 10% Vertical Height Compression on Enterprise Displays (1366x768 & 1920x1080) */
#talent_create_app .form-group,
#talent_create_app .fv-row {
    margin-bottom: 0.85rem !important;
}

#talent_create_app .form-control,
#talent_create_app .form-select {
    font-size: 13px !important;
    padding-top: 0.45rem !important;
    padding-bottom: 0.45rem !important;
    height: calc(1.5em + 0.9rem + 2px);
}

#talent_create_app .card-header {
    min-height: 48px !important;
    padding: 0.5rem 1.25rem !important;
}

#talent_create_app .card-body {
    padding: 1.25rem !important;
}

/* ==========================================================================
   Resume Uploaded View (Enterprise Green Success Banner & File Details UI)
   Matches attached reference design (media_1790666173035.png)
   ========================================================================== */
#talent_create_app .up-info,
.resume-card .up-info {
    display: none;
    margin-top: 0 !important;
}

#talent_create_app .up-info.show,
.resume-card .up-info.show {
    display: flex !important;
    flex-direction: column !important;
    flex: 1 1 auto !important;
    height: 100% !important;
    gap: 8px !important;
}

/* Hide dropzone & marketplace privacy note when file is uploaded */
.resume-card:has(.up-info.show) .ua,
.resume-card:has(.up-info.show) .resume-note,
#talent_create_app .resume-card:has(.up-info.show) .marketplace-privacy {
    display: none !important;
}

/* 1. Green Success Banner */
#talent_create_app .up-info-banner,
.resume-card .up-info-banner {
    display: flex !important;
    align-items: center !important;
    justify-content: space-between !important;
    padding: 7px 12px !important;
    background: #ecfdf5 !important;
    border: 1px solid #a7f3d0 !important;
    border-radius: 8px !important;
    margin-bottom: 0 !important;
    color: #065f46 !important;
    flex-shrink: 0 !important;
}

#talent_create_app .up-info-banner-left,
.resume-card .up-info-banner-left {
    display: flex;
    align-items: center;
    gap: 10px;
}

#talent_create_app .up-info-banner-check,
.resume-card .up-info-banner-check {
    width: 22px;
    height: 22px;
    border-radius: 50%;
    background: #10b981 !important;
    color: #fff !important;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 10px;
    flex-shrink: 0;
}

#talent_create_app .up-info-banner-check i,
.resume-card .up-info-banner-check i {
    color: #fff !important;
    font-size: 10px !important;
}

#talent_create_app .up-info-banner-text,
.resume-card .up-info-banner-text {
    display: flex;
    flex-direction: column;
}

#talent_create_app .up-info-banner-text strong,
.resume-card .up-info-banner-text strong {
    font-size: 12px !important;
    font-weight: 700 !important;
    color: #047857 !important;
    line-height: 1.3;
}

#talent_create_app .up-info-banner-text small,
.resume-card .up-info-banner-text small {
    font-size: 10.5px !important;
    color: #4b5563 !important;
    line-height: 1.3;
    margin-top: 1px;
}

#talent_create_app .up-info-banner-close,
.resume-card .up-info-banner-close {
    background: transparent !important;
    border: 0 !important;
    color: #64748b !important;
    font-size: 15px !important;
    line-height: 1;
    padding: 4px;
    cursor: pointer;
    border-radius: 4px;
    transition: color .15s ease;
    display: inline-flex;
    align-items: center;
    justify-content: center;
}

#talent_create_app .up-info-banner-close:hover,
.resume-card .up-info-banner-close:hover {
    color: #071434 !important;
}

/* 2. Uploaded File Row Card */
#talent_create_app .up-file-card,
.resume-card .up-file-card {
    display: flex !important;
    align-items: center !important;
    justify-content: space-between !important;
    padding: 8px 14px !important;
    background: #fff !important;
    border: 1px solid #e2e8f0 !important;
    border-radius: 8px !important;
    margin-bottom: 0 !important;
    flex-shrink: 0 !important;
}

#talent_create_app .up-file-card-left,
.resume-card .up-file-card-left {
    display: flex;
    align-items: center;
    gap: 10px;
    min-width: 0;
}

#talent_create_app .up-file-badge,
.resume-card .up-file-badge {
    width: 36px;
    height: 36px;
    border-radius: 7px;
    background: #fee2e2 !important;
    color: #dc2626 !important;
    display: inline-flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

#talent_create_app .up-file-badge i,
.resume-card .up-file-badge i {
    font-size: 13px;
    line-height: 1;
    color: #dc2626 !important;
    margin-bottom: 1px;
}

#talent_create_app .up-file-badge-ext,
.resume-card .up-file-badge-ext {
    font-size: 8.5px;
    font-weight: 800;
    line-height: 1;
    letter-spacing: 0.5px;
    color: #dc2626 !important;
}

#talent_create_app .up-file-meta,
.resume-card .up-file-meta {
    flex: 1;
    min-width: 0;
}

#talent_create_app .up-file-name,
.resume-card .up-file-name {
    font-size: 13px !important;
    font-weight: 700 !important;
    color: #071434 !important;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    line-height: 1.3;
}

#talent_create_app .up-file-sub,
.resume-card .up-file-sub {
    font-size: 11px !important;
    color: #64748b !important;
    margin-top: 1px;
    line-height: 1.3;
}

#talent_create_app .up-file-actions,
.resume-card .up-file-actions {
    display: flex;
    align-items: center;
    gap: 10px;
    flex-shrink: 0;
    margin-left: 14px;
}

#talent_create_app .up-btn,
.resume-card .up-btn {
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    gap: 7px !important;
    font-size: 13px !important;
    font-weight: 600 !important;
    padding: 7px 16px !important;
    border-radius: 6px !important;
    cursor: pointer !important;
    transition: all .15s ease !important;
    font-family: inherit !important;
    line-height: 1.3 !important;
}

#talent_create_app .up-btn-remove,
.resume-card .up-btn-remove {
    background: #fff !important;
    border: 1px solid #fca5a5 !important;
    border-radius: 6px !important;
    color: #dc2626 !important;
    padding: 5px 12px !important;
    font-size: 12px !important;
    font-weight: 600 !important;
    cursor: pointer !important;
    display: inline-flex !important;
    align-items: center !important;
    gap: 6px !important;
    transition: all .15s ease !important;
}

#talent_create_app .up-btn-remove:hover,
.resume-card .up-btn-remove:hover {
    background: #fff1f2 !important;
    border-color: #f87171 !important;
}

#talent_create_app .up-btn-remove i,
.resume-card .up-btn-remove i {
    font-size: 12px !important;
    color: #dc2626 !important;
}

#talent_create_app .up-btn-download,
.resume-card .up-btn-download {
    background: #eff6ff !important;
    border: 1px solid #bfdbfe !important;
    border-radius: 6px !important;
    color: #1d4ed8 !important;
    padding: 5px 12px !important;
    font-size: 12px !important;
    font-weight: 600 !important;
    text-decoration: none !important;
    display: inline-flex !important;
    align-items: center !important;
    gap: 6px !important;
    transition: all .15s ease !important;
}

#talent_create_app .up-btn-download:hover,
.resume-card .up-btn-download:hover {
    background: #dbeafe !important;
    border-color: #93c5fd !important;
    color: #1e40af !important;
}

#talent_create_app .up-btn-download i,
.resume-card .up-btn-download i {
    font-size: 12px !important;
    color: #1d4ed8 !important;
}

#talent_create_app .up-btn-replace,
.resume-card .up-btn-replace {
    background: #f8fafc !important;
    border: 1px solid #cbd5e1 !important;
    border-radius: 6px !important;
    color: #334155 !important;
    padding: 5px 12px !important;
    font-size: 12px !important;
    font-weight: 600 !important;
    cursor: pointer !important;
    display: inline-flex !important;
    align-items: center !important;
    gap: 6px !important;
    transition: all .15s ease !important;
}

#talent_create_app .up-btn-replace:hover,
.resume-card .up-btn-replace:hover {
    background: #f1f5f9 !important;
    border-color: #94a3b8 !important;
    color: #0f172a !important;
}

#talent_create_app .up-btn-replace i,
.resume-card .up-btn-replace i {
    font-size: 12px !important;
    color: #475569 !important;
}

/* 3. Resume Details Card (2x2 Grid with Yellow Badges matching Image 2) */
#talent_create_app .up-details-card,
.resume-card .up-details-card {
    flex: 1 1 auto !important;
    display: flex !important;
    flex-direction: column !important;
    justify-content: center !important;
    background: #ffffff !important;
    border: 1px solid #e2e8f0 !important;
    border-radius: 8px !important;
    padding: 10px 14px !important;
    margin-bottom: 0 !important;
}

#talent_create_app .up-details-grid,
.resume-card .up-details-grid {
    display: grid !important;
    grid-template-columns: 1fr 1fr !important;
    column-gap: 0 !important;
    row-gap: 8px !important;
    padding: 0 !important;
    margin: 0 !important;
}

#talent_create_app .up-detail-tile,
.resume-card .up-detail-tile {
    display: flex !important;
    align-items: center !important;
    gap: 10px !important;
    padding: 2px 10px 2px 0 !important;
}

#talent_create_app .up-details-grid > .up-detail-tile:nth-child(even),
.resume-card .up-details-grid > .up-detail-tile:nth-child(even) {
    border-left: 1px solid #edf2f7 !important;
    padding-left: 14px !important;
    padding-right: 0 !important;
}

#talent_create_app .up-detail-tile .up-detail-icon,
.resume-card .up-detail-tile .up-detail-icon {
    width: 32px !important;
    height: 32px !important;
    border-radius: 6px !important;
    background: #fff0a8 !important;
    color: #854d0e !important;
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    font-size: 14px !important;
    flex-shrink: 0 !important;
}

#talent_create_app .up-detail-tile .up-detail-icon i,
.resume-card .up-detail-tile .up-detail-icon i {
    color: #854d0e !important;
    font-size: 14px !important;
}

#talent_create_app .up-detail-lbl,
.resume-card .up-detail-lbl {
    font-size: 10.5px !important;
    font-weight: 500 !important;
    color: #64748b !important;
    line-height: 1.2 !important;
}

#talent_create_app .up-detail-val,
.resume-card .up-detail-val {
    font-size: 12px !important;
    font-weight: 700 !important;
    color: #071434 !important;
    margin-top: 1px !important;
    line-height: 1.3 !important;
}

#talent_create_app #upDetailParserStatus,
.resume-card #upDetailParserStatus {
    color: #059669 !important;
}

#talent_create_app #upDetailParserStatus span,
.resume-card #upDetailParserStatus span {
    font-size: 12px !important;
}

/* ==========================================================================
   Enterprise Required Fields Modal (Complete Required Fields)
   Source of Truth: media_1790668032103.png
   ========================================================================== */
.rf-modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(15, 23, 42, 0.55);
    backdrop-filter: blur(3px);
    -webkit-backdrop-filter: blur(3px);
    z-index: 10500;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 16px;
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
    background: #ffffff;
    border-radius: 16px;
    width: 100%;
    max-width: 460px;
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.15), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    padding: 32px 30px 26px;
    position: relative;
    font-family: 'Poppins', sans-serif;
    animation: rfScaleIn 0.2s cubic-bezier(0.16, 1, 0.3, 1);
    box-sizing: border-box;
}

.rf-modal-close {
    position: absolute;
    top: 16px;
    right: 16px;
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background: #f1f5f9;
    border: none;
    color: #64748b;
    font-size: 14px;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    transition: all 0.15s ease;
    padding: 0;
}

.rf-modal-close:hover {
    background: #e2e8f0;
    color: #0f172a;
}

.rf-modal-close svg {
    stroke: currentColor;
    transition: transform 0.15s ease;
}

.rf-modal-close:hover svg {
    transform: rotate(90deg);
}

/* Warning circle at top center */
.rf-warning-icon-wrap {
    width: 64px;
    height: 64px;
    border-radius: 50%;
    background: #fef9c3;
    border: 2px solid #facc15;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 16px;
}

.rf-warning-icon {
    font-size: 30px;
    font-weight: 700;
    color: #ca8a04;
    line-height: 1;
    font-family: 'Poppins', sans-serif;
}

/* Title & Description */
.rf-modal-title {
    font-size: 20px;
    font-weight: 700;
    color: #0f172a;
    text-align: center;
    margin: 0 0 8px 0;
    line-height: 1.25;
}

.rf-modal-desc {
    font-size: 13.5px;
    color: #64748b;
    text-align: center;
    line-height: 1.45;
    margin: 0 auto 22px;
    max-width: 360px;
}

/* Section Header */
.rf-section-header {
    font-size: 11px;
    font-weight: 700;
    letter-spacing: 0.6px;
    color: #64748b;
    text-transform: uppercase;
    text-align: left;
    margin-bottom: 10px;
}

/* Interactive Field Rows */
.rf-fields-list {
    display: flex;
    flex-direction: column;
    gap: 8px;
    max-height: 240px;
    overflow-y: auto;
    padding-right: 2px;
    margin-bottom: 12px;
}

.rf-field-row {
    background: #fef2f2;
    border: 1px solid #fee2e2;
    border-radius: 8px;
    padding: 10px 14px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    cursor: pointer;
    transition: all 0.15s ease;
    text-decoration: none;
    box-sizing: border-box;
}

.rf-field-row:hover {
    background: #fee2e2;
    border-color: #fca5a5;
    transform: translateY(-1px);
}

.rf-field-row-left {
    display: flex;
    align-items: center;
    gap: 12px;
    min-width: 0;
}

.rf-field-badge {
    width: 20px;
    height: 20px;
    border-radius: 50%;
    background: #ef4444;
    color: #ffffff;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 10px;
    font-weight: 800;
    flex-shrink: 0;
    line-height: 1;
}

.rf-field-name {
    font-size: 13.5px;
    font-weight: 600;
    color: #0f172a;
    line-height: 1.3;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.rf-field-chevron {
    color: #64748b;
    font-size: 12px;
    flex-shrink: 0;
    margin-left: 8px;
    stroke: currentColor;
    transition: transform 0.15s ease, color 0.15s ease;
}

.rf-field-row:hover .rf-field-chevron {
    color: #0f172a;
    transform: translateX(2px);
}

/* Helper text */
.rf-helper-text {
    font-size: 12px;
    color: #64748b;
    display: flex;
    align-items: center;
    gap: 6px;
    margin-bottom: 20px;
    text-align: left;
}

.rf-helper-text i,
.rf-helper-text svg {
    font-size: 13px;
    color: #64748b;
    stroke: currentColor;
    flex-shrink: 0;
}

/* Primary CTA button */
.rf-cta-btn {
    width: 100%;
    height: 44px;
    background: #1b84ff;
    color: #ffffff;
    border: none;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 600;
    font-family: 'Poppins', sans-serif;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    cursor: pointer;
    transition: background 0.15s ease, transform 0.1s ease;
}

.rf-cta-btn:hover {
    background: #1570ef;
}

.rf-cta-btn:active {
    transform: scale(0.99);
}

.rf-cta-btn i,
.rf-cta-btn svg {
    font-size: 13px;
    stroke: currentColor;
    transition: transform 0.15s ease;
}

.rf-cta-btn:hover i,
.rf-cta-btn:hover svg {
    transform: translateX(3px);
}

/* Temporary field attention highlight when navigated from modal */
.field-attention-highlight {
    outline: 2px solid #3b82f6 !important;
    box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.25) !important;
    border-radius: 6px !important;
    transition: all 0.2s ease-in-out !important;
    background-color: rgba(239, 246, 255, 0.4) !important;
}

/* Free Service Client Bill Rate Hover Notification & Locked State */
.br-rate-panel.br-bill-panel {
    position: relative;
}

.br-free-bill-hover-notice {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    background: #0f172a;
    color: #ffffff;
    padding: 14px 18px;
    border-radius: 12px;
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.35), 0 8px 10px -6px rgba(0, 0, 0, 0.25);
    z-index: 60;
    max-width: 320px;
    width: 88%;
    text-align: left;
    pointer-events: none;
    backdrop-filter: blur(6px);
    border: 1px solid rgba(255, 255, 255, 0.12);
}

.br-free-bill-hover-inner {
    display: flex;
    align-items: flex-start;
    gap: 12px;
}

.br-free-bill-hover-inner i {
    color: #38bdf8;
    font-size: 18px;
    margin-top: 2px;
    flex-shrink: 0;
}

.br-free-bill-hover-text strong {
    display: block;
    font-size: 13px;
    font-weight: 600;
    color: #ffffff;
    margin-bottom: 4px;
    line-height: 1.25;
}

.br-free-bill-hover-text span {
    display: block;
    font-size: 11.5px;
    color: #94a3b8;
    line-height: 1.45;
}

.br-card.is-free-service .br-bill-panel {
    cursor: not-allowed;
    background: #f8fafc;
    border: 1.5px dashed #cbd5e1;
    position: relative;
    opacity: 0.85;
}

.br-card.is-free-service .br-bill-panel * {
    cursor: not-allowed;
}

.br-card.is-free-service .br-bill-panel:hover {
    border-color: #94a3b8;
    background: #f1f5f9;
}

/* ── Restored USD Conversion label (Approx $ X.XX) floating right on label ── */
.br-rate-field label {
    display: flex !important;
    align-items: center !important;
    justify-content: space-between !important;
    width: 100% !important;
    margin-bottom: 6px !important;
    font-size: 13px !important;
    font-weight: 600 !important;
    color: #1e293b !important;
}

.br-rate-field label .rq {
    margin-left: 2px !important;
    margin-right: auto !important;
    color: #ef4444 !important;
}

.br-rate-field label span[id$="_usd"],
label span[id$="_usd"] {
    float: right !important;
    margin-left: auto !important;
    color: #EF560A !important;
    font-size: 0.95rem !important;
    font-weight: 600 !important;
    background: transparent !important;
    border: none !important;
    padding: 0 !important;
    line-height: normal !important;
    white-space: nowrap !important;
}

/* ── br-panel-icon: Fix icon box sizing and alignment ── */
.br-panel-icon {
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    flex: 0 0 auto !important;
    width: 40px !important;
    height: 40px !important;
    min-width: 40px !important;
    border-radius: 10px !important;
    font-size: 16px !important;
    line-height: 1 !important;
    overflow: hidden !important;
}
.br-panel-icon i {
    font-size: 15px !important;
    line-height: 1 !important;
    display: block !important;
}

/* ── Commercial Rate Input Group (.ig + .ig-a integrated seamless design) ── */
.br-rate-field .ig {
    display: flex !important;
    align-items: stretch !important;
    position: relative !important;
    width: 100% !important;
    gap: 0 !important;
}

.br-rate-field .ig input {
    flex: 1 1 auto !important;
    min-width: 0 !important;
    height: 38px !important;
    border-radius: 6px 0 0 6px !important;
    border: 1px solid #d1d5db !important;
    border-right: none !important;
    padding: 8px 12px !important;
    font-size: 0.95rem !important;
    color: #0f172a !important;
    background: #ffffff !important;
    box-sizing: border-box !important;
    margin: 0 !important;
    outline: none !important;
    transition: border-color 0.15s ease, box-shadow 0.15s ease !important;
}

.br-rate-field .ig input:focus {
    border-color: #3b82f6 !important;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15) !important;
    position: relative !important;
    z-index: 2 !important;
}

.br-rate-field .ig .ig-a,
.br-rate-field .ig .changeTextCurrency {
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    flex: 0 0 auto !important;
    min-width: 54px !important;
    height: 38px !important;
    padding: 0 14px !important;
    background: #f8fafc !important;
    border: 1px solid #d1d5db !important;
    border-left: 1px solid #e2e8f0 !important;
    border-radius: 0 6px 6px 0 !important;
    font-size: 13px !important;
    font-weight: 600 !important;
    color: #475569 !important;
    white-space: nowrap !important;
    user-select: none !important;
    box-sizing: border-box !important;
    margin: 0 !important;
    transition: border-color 0.15s ease, background 0.15s ease, color 0.15s ease !important;
}

.br-rate-field .ig:focus-within .ig-a,
.br-rate-field .ig:focus-within .changeTextCurrency {
    border-color: #3b82f6 !important;
    background: #eff6ff !important;
    color: #1d4ed8 !important;
}

.br-card.is-free-service .br-bill-panel .ig input {
    background-color: #f1f5f9 !important;
    cursor: not-allowed !important;
}

.br-card.is-free-service .br-bill-panel .ig-a,
.br-card.is-free-service .br-bill-panel .changeTextCurrency {
    opacity: 0.6 !important;
    background: #f1f5f9 !important;
    cursor: not-allowed !important;
}

/* Margin Summary dynamic difference amount */
#br_summary_margin_diff.is-positive {
    color: #0AA65D !important;
}

#br_summary_margin_diff.is-negative {
    color: #E11D48 !important;
}

#br_summary_margin_diff.is-free,
#br_summary_margin_diff.is-neutral {
    color: #64748b !important;
    font-size: 0.95rem !important;
}

#br_summary_model {
    font-weight: 700 !important;
    color: #2563eb !important;
}

/* ── Rates Section Currency Selector & Select2 Styling (Enterprise UX) ── */
.br-currency {
    display: flex;
    align-items: center;
    gap: 10px;
    min-width: 290px;
    max-width: 380px;
}
.br-currency label {
    color: #0b1545;
    font-size: 13px;
    font-weight: 700;
    margin: 0;
    white-space: nowrap;
}
.br-currency .select2-container {
    flex: 1 1 auto;
    min-width: 220px;
}
.br-currency .select2-container--default .select2-selection--single {
    height: 38px;
    border: 1px solid #d8deec;
    border-radius: 6px;
    background-color: #ffffff;
    display: flex;
    align-items: center;
    padding-left: 10px;
    padding-right: 30px;
    font-family: 'Poppins', sans-serif;
    font-size: 13px;
    font-weight: 600;
    color: #1e293b;
    box-sizing: border-box;
    transition: border-color 0.15s ease, box-shadow 0.15s ease;
}
.br-currency .select2-container--default.select2-container--open .select2-selection--single,
.br-currency .select2-container--default .select2-selection--single:focus {
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15);
    outline: none;
}
.br-currency .select2-container--default .select2-selection--single .select2-selection__arrow {
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
    .br-currency {
        width: 100%;
        max-width: 100%;
        min-width: 0;
        margin-top: 8px;
    }
}

/* ── Redesigned Enterprise Relationship Manager Chip Card (media_1790683314858.png) ── */
.rm-chips-container {
    display: flex;
    flex-direction: column;
    gap: 10px;
    width: 100%;
    margin-top: 10px;
    margin-bottom: 8px;
}

.rm-chip-card {
    display: flex;
    align-items: center;
    justify-content: space-between;
    background: #f8faff;
    border: 1px solid #bfdbfe;
    border-radius: 8px;
    padding: 10px 18px;
    min-height: 56px;
    transition: all 0.2s ease;
    box-shadow: 0 1px 2px rgba(15, 23, 42, 0.04);
}

.rm-chip-card:hover {
    border-color: #93c5fd;
    box-shadow: 0 4px 12px rgba(37, 99, 235, 0.06);
    background: #ffffff;
}

.rm-chip-left {
    display: flex;
    align-items: center;
    gap: 12px;
    min-width: 190px;
}

.rm-chip-avatar {
    width: 38px;
    height: 38px;
    min-width: 38px;
    border-radius: 50%;
    background: #dbeafe;
    color: #1e40af;
    font-size: 13.5px;
    font-weight: 700;
    display: flex;
    align-items: center;
    justify-content: center;
    text-transform: uppercase;
    flex-shrink: 0;
}

.rm-chip-identity {
    display: flex;
    flex-direction: column;
    min-width: 0;
}

.rm-chip-name {
    font-size: 13.5px;
    font-weight: 700;
    color: #0f172a;
    line-height: 1.25;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.rm-chip-sub {
    font-size: 11.5px;
    color: #64748b;
    line-height: 1.2;
    margin-top: 2px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.rm-chip-divider {
    width: 1px;
    height: 28px;
    background: #e2e8f0;
    margin: 0 16px;
    flex-shrink: 0;
}

.rm-chip-col {
    display: flex;
    align-items: center;
    gap: 9px;
    flex: 1 1 auto;
    min-width: 0;
}

.rm-chip-icon {
    font-size: 13px;
    color: #64748b;
    flex-shrink: 0;
}

.rm-chip-text {
    font-size: 12.5px;
    color: #334155;
    font-weight: 500;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.rm-chip-actions {
    display: flex;
    align-items: center;
    gap: 16px;
    margin-left: auto;
    flex-shrink: 0;
}

.rm-chip-badge-selected {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: #dcfce7;
    color: #15803d;
    border-radius: 6px;
    padding: 5px 12px;
    font-size: 12px;
    font-weight: 600;
    line-height: 1;
}

.rm-chip-badge-selected i {
    color: #16a34a;
    font-size: 13px;
}

.rm-chip-btn-remove {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: transparent;
    border: none;
    outline: none;
    color: #ef4444;
    font-size: 12.5px;
    font-weight: 600;
    cursor: pointer;
    padding: 6px 10px;
    border-radius: 6px;
    transition: all 0.15s ease;
    font-family: inherit;
}

.rm-chip-btn-remove:hover {
    color: #dc2626;
    background: #fef2f2;
}

.rm-chip-btn-remove i {
    font-size: 12px;
}

/* Add another RM trigger button */
.rm-add-more-wrap {
    margin-top: 8px;
    margin-bottom: 6px;
}

.rm-add-more-btn {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: #ffffff;
    border: 1px dashed #93c5fd;
    color: #2563eb;
    border-radius: 6px;
    padding: 7px 14px;
    font-size: 12px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.15s ease;
    font-family: inherit;
}

.rm-add-more-btn:hover {
    background: #eff6ff;
    border-color: #3b82f6;
    color: #1d4ed8;
}

@media (max-width: 991px) {
    .rm-chip-card {
        flex-wrap: wrap;
        gap: 10px;
        padding: 12px 14px;
    }
    .rm-chip-divider {
        display: none;
    }
    .rm-chip-left {
        width: 100%;
    }
    .rm-chip-col {
        flex: 1 1 45%;
    }
    .rm-chip-actions {
        width: 100%;
        justify-content: flex-end;
        padding-top: 8px;
        border-top: 1px dashed #e2e8f0;
    }
}

/* ── Skills & Expertise: Minimum Skills Requirement Badge ── */
.se-badge--req {
    display: inline-flex;
    align-items: center;
    gap: 7px;
    padding: 5px 13px;
    border-radius: 6px;
    background: #fffbeb;
    color: #92400e;
    border: 1px solid #fde68a;
    font-size: 11.5px;
    font-weight: 600;
    line-height: 1.4;
    letter-spacing: 0;
}
.se-badge--req i {
    color: #f59e0b;
    font-size: 12px;
}

/* ── Star Proficiency Rating: Highlight selected option in gold tones ── */
.se-card .sk select[name="ratings[]"] option {
    font-size: 13px;
    font-family: 'Poppins', sans-serif;
    color: #0B1545;
    font-weight: 500;
}
.se-card .sk select[name="ratings[]"] {
    color: #b45309;
    font-weight: 600;
}
.se-card .sk select[name="ratings[]"] option:first-child {
    color: #64748b;
    font-weight: 400;
}

/* ── Enterprise Validation Error Highlighting (media_1790669780356.png) ── */
.has-validation-error,
input.has-validation-error,
select.has-validation-error,
textarea.has-validation-error,
.form-control.has-validation-error,
.form-select.has-validation-error,
.tagify.has-validation-error,
.rm-picker.has-validation-error {
    border: 1.5px solid #ef4444 !important;
    background-color: #fef2f2 !important;
    box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.12) !important;
}

input.has-validation-error:focus,
select.has-validation-error:focus,
textarea.has-validation-error:focus,
.form-control.has-validation-error:focus,
.tagify.has-validation-error:focus-within,
.rm-picker.has-validation-error:focus-within {
    border-color: #dc2626 !important;
    background-color: #fef2f2 !important;
    box-shadow: 0 0 0 3px rgba(220, 38, 38, 0.2) !important;
    outline: none !important;
}

.rm-picker.has-validation-error {
    border-radius: 8px !important;
}

#uploadBox.has-validation-error {
    border: 1.5px dashed #ef4444 !important;
    background-color: #fef2f2 !important;
    box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.12) !important;
    border-radius: 8px !important;
}

.aw-mode-grid.has-validation-error,
.wm-option-grid.has-validation-error {
    border: 1.5px solid #ef4444 !important;
    background-color: #fef2f2 !important;
    border-radius: 8px !important;
    padding: 6px !important;
    box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.12) !important;
}

.br-rate-field.has-validation-error .ig input {
    border: 1.5px solid #ef4444 !important;
    border-right: none !important;
    background-color: #fef2f2 !important;
}

.br-rate-field.has-validation-error .ig .ig-a,
.br-rate-field.has-validation-error .ig .changeTextCurrency {
    border: 1.5px solid #ef4444 !important;
    background-color: #fee2e2 !important;
    color: #dc2626 !important;
}

#skillExperienceWrapper tr.has-validation-error input,
#skillExperienceWrapper tr.has-validation-error select {
    border-color: #ef4444 !important;
    background-color: #fef2f2 !important;
}

.field-error-message {
    display: flex;
    align-items: center;
    gap: 6px;
    margin-top: 6px;
    color: #ef4444;
    font-size: 12.5px;
    font-weight: 500;
    line-height: 1.4;
    animation: fieldErrorFadeIn 0.2s ease-in-out;
}

.field-error-message i,
.field-error-message svg {
    color: #ef4444;
    stroke: currentColor;
    font-size: 13px;
    flex-shrink: 0;
}

.field-error-message span {
    color: #dc2626;
    font-size: 12.5px;
    font-weight: 500;
}

@keyframes fieldErrorFadeIn {
    from {
        opacity: 0;
        transform: translateY(-3px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* ── Jero Phone Input Validation Positioning & Styling ── */
.jero-phone-box-wrapper.has-validation-error,
.jero-phone-box-wrapper input.has-validation-error {
    border: 0 none transparent !important;
    background-color: transparent !important;
    box-shadow: none !important;
}

.jero-phone-box-wrapper.has-validation-error .jero-phone-input-group,
.jero-phone-input-group.has-validation-error {
    border: 1.5px solid #ef4444 !important;
    background-color: #fef2f2 !important;
    box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.12) !important;
}

.jero-phone-box-wrapper.has-validation-error .jero-phone-input-group:focus-within,
.jero-phone-input-group.has-validation-error:focus-within {
    border-color: #dc2626 !important;
    background-color: #fef2f2 !important;
    box-shadow: 0 0 0 3px rgba(220, 38, 38, 0.2) !important;
}

/* Strictly prevent any validation message or icon from floating inside the 38px phone group */
.jero-phone-input-group > .field-error-message,
.jero-phone-input-group .field-error-message,
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


/* ── Fix ITI phone flag floating over Gender label ── */
/* The .iti__flag-container is position:absolute — contain it strictly inside the phone field */
.bd-phone-control,
.fg.fv-row .bd-phone-control {
    position: relative;
    overflow: visible; /* allow ITI dropdown to show */
}
/* The ITI flag button must be contained within its own input wrapper */
.bd-phone-control .iti {
    position: relative;
    width: 100%;
    display: block;
}
/* Force the gender/dob row label area above the ITI phone widget */
.bd-grid--two .fg:first-child label,
.bd-grid--two .fg:last-child label {
    position: relative;
    z-index: 1;
    display: block;
}
/* Ensure ITI stays in its grid cell — do NOT bleed into sibling cells */
.bd-grid .bd-field:has(.iti__flag-container) {
    overflow: clip;
}

/* ── Clean Single Relationship Manager Search Box (Removes inner box-in-box) ── */
.rm-picker {
    display: flex !important;
    align-items: center !important;
    position: relative !important;
    width: 100% !important;
    min-height: 46px !important;
    height: 46px !important;
    padding: 0 16px !important;
    border: 1px solid #d8deec !important;
    border-radius: 8px !important;
    background: #ffffff !important;
    box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04) !important;
    cursor: pointer !important;
    transition: border-color 0.15s ease, box-shadow 0.15s ease !important;
}
.rm-picker:hover,
.rm-picker:focus-within {
    border-color: #3b82f6 !important;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.12) !important;
}
.rm-picker .rm-search-icon {
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    color: #94a3b8 !important;
    font-size: 14px !important;
    margin-right: 10px !important;
    flex: 0 0 auto !important;
}
.rm-picker input#recruiter_display,
.rm-picker input[type="text"] {
    flex: 1 1 auto !important;
    min-width: 0 !important;
    border: 0 !important;
    border-radius: 0 !important;
    background: transparent !important;
    box-shadow: none !important;
    outline: none !important;
    padding: 0 !important;
    margin: 0 !important;
    height: auto !important;
    min-height: auto !important;
    font-size: 13.5px !important;
    color: #0f172a !important;
    cursor: pointer !important;
}
.rm-picker input#recruiter_display::placeholder {
    color: #94a3b8 !important;
    font-size: 13.5px !important;
    font-weight: 400 !important;
}
.rm-picker .rm-input-placeholder {
    display: none !important;
}
.rm-picker .rm-picker-divider {
    display: none !important;
}
.rm-picker .rm-picker-action {
    display: inline-flex !important;
    align-items: center !important;
    margin-left: auto !important;
    color: #2563eb !important;
    font-weight: 600 !important;
    font-size: 13px !important;
    white-space: nowrap !important;
    padding-left: 12px !important;
    cursor: pointer !important;
}
.rm-picker .rm-picker-chevron {
    display: none !important;
}

/* ── RM Drawer Sizing & Scrollability ── */
#kt_activitiesRecruiter {
    background: #ffffff !important;
}
#kt_activitiesRecruiter_body {
    height: calc(100vh - 65px) !important;
    overflow-y: auto !important;
    background: #ffffff !important;
}
#kt_activities_scroll {
    height: auto !important;
    min-height: 100% !important;
    overflow-y: visible !important;
}

/* ── Save to Bench Stage button — ensure it re-enables after error ── */
#save-form:not([data-kt-indicator="on"]) {
    pointer-events: auto !important;
    opacity: 1 !important;
}
#save-form[disabled]:not([data-kt-indicator="on"]) {
    pointer-events: auto !important;
}

/* ── Proficiency Rating Yellow Star Icons ── */
.se-enterprise-dropdown .fa-star,
.select2-results__option .fa-star,
.select2-selection__rendered .fa-star,
.se-field--select .fa-star {
    color: #f59e0b !important;
}
.se-star-icon {
    display: inline-block;
    vertical-align: -1px;
    fill: #f59e0b;
    flex-shrink: 0;
}

/* ── Categorized Highest Qualification & Nationality Dropdown Styling ── */
#nationality optgroup,
#qualification optgroup {
    font-weight: 700 !important;
    font-size: 11.5px !important;
    color: #1e40af !important;
    background: #f1f5f9 !important;
    padding: 7px 10px !important;
    letter-spacing: 0.25px;
}
#nationality option,
#qualification option {
    font-weight: 500 !important;
    font-size: 13px !important;
    color: #1e293b !important;
    background: #ffffff !important;
    padding: 6px 14px !important;
}

/* ── Ensure Candidate Profile Card & Grid Never Clip Floating Dropdowns ── */
.fs.bd-card,
.bd-card,
.bd-card .bd-body,
.bd-card .fsc,
.bd-card .bd-grid,
.bd-card .bd-field,
.bd-card .bd-control,
.bd-card .bd-control--select {
    overflow: visible !important;
}

.bd-grid .bd-field:has(#nationality),
.bd-grid .bd-field:has(#qualification),
.bd-grid .bd-field:has(.jero-custom-select) {
    overflow: visible !important;
}

/* Base stacking context: Candidate Profile sits above subsequent cards */
.bd-card {
    position: relative;
    z-index: 30;
}

/* Active Stacking Elevation when Combobox is open */
.bd-card.has-active-combobox,
.bd-card:has(.jero-custom-select.is-open) {
    position: relative !important;
    z-index: 1050 !important;
    overflow: visible !important;
}

.bd-grid:has(.jero-custom-select.is-open) {
    position: relative !important;
    z-index: 1051 !important;
}

.bd-field:has(.jero-custom-select.is-open) {
    position: relative !important;
    z-index: 1052 !important;
}

.bd-control:has(.jero-custom-select.is-open) {
    position: relative !important;
    z-index: 1053 !important;
}

.jero-custom-select.is-open {
    position: relative !important;
    z-index: 1054 !important;
}

/* Subsequent card sits lower in the stack so dropdown renders cleanly on top of it */
.fs.pd-card,
.pd-card {
    position: relative;
    z-index: 10;
}

/* ── JERO Enterprise Custom Combobox Engine (Adaptive Nationality & Education) ── */
.jero-custom-select {
    position: relative;
    width: 100%;
    font-family: 'Poppins', sans-serif;
    user-select: none;
}

.bd-control--select {
    position: relative;
}

/* Hide native select visually ONLY when a custom combobox engine is present */
.bd-control--has-combobox > select,
.bd-control--select:has(.jero-custom-select) > select {
    position: absolute !important;
    opacity: 0 !important;
    pointer-events: none !important;
    width: 1px !important;
    height: 1px !important;
    margin: -1px !important;
    clip: rect(0, 0, 0, 0) !important;
    overflow: hidden !important;
    z-index: -1 !important;
}

/* Hide static icon inside bd-control ONLY when custom select trigger includes its own */
.bd-control--has-combobox > i,
.bd-control--select:has(.jero-custom-select) > i {
    display: none !important;
}

/* Explicit styling for native Gender select (Matches Date of Birth 42px height & 6px border-radius) */
.bd-control select#gender,
.bd-control--select select#gender {
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
    transition: border-color 0.15s ease, box-shadow 0.15s ease !important;
    box-sizing: border-box !important;
}

.bd-control:has(select#gender) > i,
.bd-control--select:has(select#gender) > i {
    display: inline-flex !important;
    position: absolute !important;
    left: 14px !important;
    top: 50% !important;
    transform: translateY(-50%) !important;
    color: #53618c !important;
    font-size: 14px !important;
    z-index: 2 !important;
    pointer-events: none !important;
}

.bd-control select#gender:focus,
.bd-control--select select#gender:focus {
    border-color: #3b82f6 !important;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.14) !important;
}

.bd-control select#gender.has-validation-error,
.bd-control.has-validation-error select#gender,
.bd-control--select select#gender.has-validation-error,
.bd-control--select.has-validation-error select#gender {
    border-color: #ef4444 !important;
    box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.14) !important;
}

/* Trigger Button */
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

/* Bulletproof hiding of non-matching items and groups */
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

/* Empty message */
.jero-cs-empty {
    padding: 20px 14px !important;
    text-align: center !important;
    color: #64748b !important;
    font-size: 13px !important;
    font-weight: 500 !important;
}
.jero-cs-empty i {
    font-size: 18px !important;
    color: #94a3b8 !important;
    display: block !important;
    margin-bottom: 6px !important;
}

/* Location Combobox Polish Styles */
.jero-cs-trigger-clear {
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    width: 20px !important;
    height: 20px !important;
    font-size: 16px !important;
    line-height: 1 !important;
    color: #94a3b8 !important;
    border-radius: 50% !important;
    transition: background-color 0.15s ease, color 0.15s ease !important;
    cursor: pointer !important;
    margin-right: 2px !important;
}
.jero-cs-trigger-clear:hover {
    background-color: #fee2e2 !important;
    color: #ef4444 !important;
}

.jero-cs-search-spinner {
    font-size: 13px !important;
    color: #3b82f6 !important;
    animation: jeroSpin 0.75s linear infinite !important;
}

@keyframes jeroSpin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}

/* Needs-Confirmation Disambiguation Bar */
.jero-disambiguation-bar {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 6px;
    margin-top: 6px;
    padding: 6px 10px;
    background: #f1f5f9;
    border: 1px dashed #cbd5e1;
    border-radius: 6px;
}
.jero-disambiguation-badge {
    display: inline-flex;
    align-items: center;
    padding: 3px 8px;
    font-size: 11.5px;
    font-weight: 500;
    color: #1e40af;
    background: #dbeafe;
    border: 1px solid #bfdbfe;
    border-radius: 4px;
    cursor: pointer;
    transition: all 0.15s ease;
}
.jero-disambiguation-badge:hover {
    background: #bfdbfe;
    color: #1e3a8a;
    border-color: #93c5fd;
}
.jero-disambiguation-badge.is-recommended {
    background: #e0e7ff;
    color: #3730a3;
    border-color: #c7d2fe;
    font-weight: 600;
}

/* ==========================================================================
   Candidate Profile Card: Strict 42px Equal Height & Alignment Engine
   Ensures 100% pixel-perfect equal height (42px) across all 8 controls:
   1. Full Name (text input)
   2. Email (email input)
   3. Mobile Number (phone input + iti flag wrapper)
   4. Nationality (custom combobox)
   5. Current Location (custom combobox)
   6. Date of Birth (date input)
   7. Gender (select dropdown)
   8. Highest Qualification (custom combobox)
   ========================================================================== */
#talent_create_app #bd-candidate-profile-card .bd-control,
#talent_create_app #bd-candidate-profile-card .bd-phone-control,
#talent_create_app #bd-candidate-profile-card .bd-control--select,
#talent_create_app #bd-candidate-profile-card .bd-control--has-combobox,
#talent_create_app .bd-card:has(#full_name) .bd-control,
#talent_create_app .bd-card:has(#full_name) .bd-phone-control,
#talent_create_app .bd-card:has(#full_name) .bd-control--select,
#talent_create_app .bd-card:has(#full_name) .bd-control--has-combobox {
    min-height: 42px !important;
    height: 42px !important;
    max-height: 42px !important;
}

#talent_create_app #bd-candidate-profile-card .bd-control input,
#talent_create_app #bd-candidate-profile-card .bd-phone-control input,
#talent_create_app #bd-candidate-profile-card .bd-control select,
#talent_create_app #bd-candidate-profile-card .bd-control input[type="date"],
#talent_create_app #bd-candidate-profile-card .jero-custom-select .jero-cs-trigger,
#talent_create_app .bd-card:has(#full_name) .bd-control input,
#talent_create_app .bd-card:has(#full_name) .bd-phone-control input,
#talent_create_app .bd-card:has(#full_name) .bd-control select,
#talent_create_app .bd-card:has(#full_name) .bd-control input[type="date"],
#talent_create_app .bd-card:has(#full_name) .jero-custom-select .jero-cs-trigger {
    min-height: 42px !important;
    height: 42px !important;
    max-height: 42px !important;
    line-height: 40px !important;
    box-sizing: border-box !important;
    border: 1px solid #d8deec !important;
    border-radius: 6px !important;
    font-size: 13.5px !important;
    font-weight: 500 !important;
    font-family: 'Poppins', sans-serif !important;
    padding-top: 0 !important;
    padding-bottom: 0 !important;
}

#talent_create_app #bd-candidate-profile-card .bd-control input:not([type="date"]):not([type="checkbox"]):not([type="radio"]),
#talent_create_app #bd-candidate-profile-card .bd-control input[type="date"],
#talent_create_app .bd-card:has(#full_name) .bd-control input:not([type="date"]):not([type="checkbox"]):not([type="radio"]),
#talent_create_app .bd-card:has(#full_name) .bd-control input[type="date"] {
    padding-left: 42px !important;
}

#talent_create_app #bd-candidate-profile-card .bd-phone-control .iti,
#talent_create_app .bd-card:has(#full_name) .bd-phone-control .iti {
    height: 42px !important;
    min-height: 42px !important;
    max-height: 42px !important;
    box-sizing: border-box !important;
}

#talent_create_app #bd-candidate-profile-card .bd-phone-control .iti__selected-flag,
#talent_create_app .bd-card:has(#full_name) .bd-phone-control .iti__selected-flag {
    height: 40px !important;
    max-height: 40px !important;
}

#talent_create_app #bd-candidate-profile-card .bd-field label,
#talent_create_app .bd-card:has(#full_name) .bd-field label {
    min-height: 22px !important;
    display: flex !important;
    align-items: center !important;
    gap: 4px !important;
    margin-bottom: 7px !important;
    font-size: 13px !important;
    font-weight: 600 !important;
    line-height: 1.3 !important;
}

#talent_create_app #full_name {
    text-transform: capitalize !important;
    font-weight: 500 !important;
    letter-spacing: 0.15px !important;
}

#talent_create_app #bd-candidate-profile-card .bd-control input:focus,
#talent_create_app .bd-card:has(#full_name) .bd-control input:focus {
    border-color: #3b82f6 !important;
    background-color: #ffffff !important;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.12) !important;
    outline: none !important;
}

/* ==========================================================================
   Enterprise Available From Date Hover Notification Card
   ========================================================================== */
.aw-date-info-trigger {
    position: relative !important;
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    width: 20px !important;
    height: 20px !important;
    margin-left: 6px !important;
    border-radius: 50% !important;
    color: #4f46e5 !important;
    background: #eef2ff !important;
    cursor: pointer !important;
    transition: all 0.2s ease !important;
    vertical-align: middle !important;
    outline: none !important;
}

.aw-date-info-trigger:hover,
.aw-date-info-trigger:focus,
.aw-date-info-trigger:focus-within {
    background: #e0e7ff !important;
    color: #3730a3 !important;
    box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.15) !important;
}

.aw-date-info-trigger i.fa-circle-info {
    font-size: 13px !important;
    line-height: 1 !important;
}

.aw-date-hover-card {
    position: absolute !important;
    bottom: calc(100% + 12px) !important;
    right: -20px !important;
    width: 350px !important;
    max-width: 90vw !important;
    background: #ffffff !important;
    border: 1px solid #d8deec !important;
    border-radius: 10px !important;
    box-shadow: 0 16px 36px -4px rgba(15, 23, 42, 0.18), 0 8px 16px -6px rgba(15, 23, 42, 0.10) !important;
    padding: 14px 16px !important;
    z-index: 1060 !important;
    opacity: 0 !important;
    visibility: hidden !important;
    pointer-events: none !important;
    transform: translateY(6px) !important;
    transition: opacity 0.2s cubic-bezier(0.16, 1, 0.3, 1), transform 0.2s cubic-bezier(0.16, 1, 0.3, 1), visibility 0.2s !important;
    text-align: left !important;
    white-space: normal !important;
    font-family: 'Poppins', sans-serif !important;
}

.aw-date-hover-card::after {
    content: '' !important;
    position: absolute !important;
    top: 100% !important;
    right: 23px !important;
    border-width: 7px !important;
    border-style: solid !important;
    border-color: #ffffff transparent transparent transparent !important;
}

.aw-date-hover-card::before {
    content: '' !important;
    position: absolute !important;
    top: 100% !important;
    right: 22px !important;
    border-width: 8px !important;
    border-style: solid !important;
    border-color: #d8deec transparent transparent transparent !important;
}

.aw-date-info-trigger:hover .aw-date-hover-card,
.aw-date-info-trigger:focus .aw-date-hover-card,
.aw-date-info-trigger:focus-within .aw-date-hover-card {
    opacity: 1 !important;
    visibility: visible !important;
    pointer-events: auto !important;
    transform: translateY(0) !important;
}

.aw-hover-card-header {
    display: flex !important;
    align-items: center !important;
    justify-content: space-between !important;
    padding-bottom: 8px !important;
    margin-bottom: 10px !important;
    border-bottom: 1px solid #f1f5f9 !important;
}

.aw-hover-card-title {
    display: flex !important;
    align-items: center !important;
    gap: 7px !important;
    font-size: 12.5px !important;
    font-weight: 700 !important;
    color: #0f172a !important;
}

.aw-hover-card-title i {
    font-size: 13px !important;
    color: #3b82f6 !important;
}

.aw-hover-card-pill {
    font-size: 9.5px !important;
    font-weight: 700 !important;
    letter-spacing: 0.3px !important;
    text-transform: uppercase !important;
    color: #059669 !important;
    background: #ecfdf5 !important;
    border: 1px solid #a7f3d0 !important;
    border-radius: 20px !important;
    padding: 2px 7px !important;
}

.aw-hover-card-content {
    display: flex !important;
    flex-direction: column !important;
    gap: 9px !important;
}

.aw-hover-item {
    display: flex !important;
    align-items: flex-start !important;
    gap: 10px !important;
}

.aw-hover-icon {
    width: 22px !important;
    height: 22px !important;
    border-radius: 6px !important;
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    font-size: 10px !important;
    flex-shrink: 0 !important;
    margin-top: 1px !important;
}

.aw-hover-icon--blue {
    background: #eff6ff !important;
    color: #2563eb !important;
}

.aw-hover-icon--amber {
    background: #fffbeb !important;
    color: #d97706 !important;
}

.aw-hover-icon--emerald {
    background: #ecfdf5 !important;
    color: #059669 !important;
}

.aw-hover-text {
    flex: 1 1 auto !important;
}

.aw-hover-text strong {
    display: block !important;
    font-size: 11.5px !important;
    font-weight: 600 !important;
    color: #1e293b !important;
    line-height: 1.3 !important;
}

.aw-hover-text small {
    display: block !important;
    font-size: 10.5px !important;
    font-weight: 400 !important;
    color: #64748b !important;
    line-height: 1.35 !important;
    margin-top: 2px !important;
}

.aw-hover-card-footer {
    display: flex !important;
    align-items: center !important;
    gap: 7px !important;
    padding-top: 9px !important;
    margin-top: 10px !important;
    border-top: 1px solid #f1f5f9 !important;
    font-size: 10px !important;
    color: #475569 !important;
    line-height: 1.3 !important;
}

.aw-hover-card-footer i {
    font-size: 12px !important;
    color: #10b981 !important;
    flex-shrink: 0 !important;
}

@media (max-width: 560px) {
    .aw-date-hover-card {
        right: -60px !important;
        width: 300px !important;
    }
    .aw-date-hover-card::after {
        right: 63px !important;
    }
    .aw-date-hover-card::before {
        right: 62px !important;
    }
}

/* ==========================================================================
   Preferred Work Mode Card Styling (Isolated Card)
   ========================================================================== */
#talent_create_app .wm-card {
    position: relative;
}
#talent_create_app .wm-card .wm-hero {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
    padding: 14px 20px;
    border-bottom: 1px solid #E1E8F5;
    background: linear-gradient(90deg, #F5FBFD 0%, #FFFFFF 82%) !important;
}
#talent_create_app .wm-card .wm-hero-title {
    display: flex;
    align-items: center;
    gap: 12px;
}
#talent_create_app .wm-card .wm-hero-icon {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 40px !important;
    height: 40px !important;
    min-width: 40px !important;
    border-radius: 8px !important;
    background: linear-gradient(135deg, #0284C7, #38BDF8) !important;
    color: #FFFFFF !important;
    box-shadow: 0 8px 18px rgba(2, 132, 199, .18) !important;
}
#talent_create_app .wm-card .wm-hero-icon i {
    font-size: 16px !important;
    color: #FFFFFF !important;
}
#talent_create_app .wm-card .wm-hero-title strong {
    display: block;
    font-family: 'Poppins', sans-serif !important;
    font-size: 14px !important;
    font-weight: 600 !important;
    color: #181C32 !important;
    line-height: 1.25 !important;
}
#talent_create_app .wm-card .wm-hero-title small {
    display: block;
    font-family: 'Poppins', sans-serif !important;
    font-size: 11px !important;
    color: #58658F !important;
    line-height: 1.35 !important;
}
#talent_create_app .wm-card .wm-talent-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 4px 10px;
    border-radius: 7px !important;
    font-family: 'Poppins', sans-serif !important;
    font-size: 11px !important;
    font-weight: 600 !important;
    color: #0284C7 !important;
    background: #E0F2FE !important;
}
#talent_create_app .wm-card .wm-body {
    padding: 16px 18px 18px !important;
}
#talent_create_app .wm-card .wm-field > label {
    margin-bottom: 2px;
    color: #0B1545 !important;
    font-size: 13px !important;
    font-weight: 600 !important;
}
#talent_create_app .wm-card .aw-field-help {
    margin-bottom: 10px;
    color: #58658F !important;
    font-size: 11px !important;
}
#talent_create_app .wm-card .aw-work-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 12px;
}
#talent_create_app .wm-card .aw-work-option--remote {
    --aw-accent: #079B59;
    --aw-soft: #E9FAF1;
    --aw-light: #F5FCF8;
}
#talent_create_app .wm-card .aw-work-option--hybrid {
    --aw-accent: #2563EB;
    --aw-soft: #EAF2FF;
    --aw-light: #F5F9FF;
}
#talent_create_app .wm-card .aw-work-option--office {
    --aw-accent: #E46B16;
    --aw-soft: #FFF2E8;
    --aw-light: #FFFAF5;
}
#talent_create_app .wm-card .aw-work-option {
    position: relative;
    display: flex !important;
    align-items: center;
    gap: 10px;
    min-width: 0;
    min-height: 76px;
    padding: 12px;
    border: 1px solid #D8E1F0 !important;
    border-radius: 8px !important;
    background: #FFFFFF !important;
    cursor: pointer;
    transition: all 0.2s ease-in-out;
}
#talent_create_app .wm-card .aw-work-option:hover,
#talent_create_app .wm-card .aw-work-option:focus-within {
    border-color: var(--aw-accent) !important;
}
#talent_create_app .wm-card .aw-work-option:has(input:checked) {
    border-color: var(--aw-accent) !important;
    background: linear-gradient(105deg, var(--aw-light), #FFFFFF) !important;
    box-shadow: inset 0 0 0 1px var(--aw-accent) !important;
}
#talent_create_app .wm-card .aw-work-option input[type="radio"] {
    position: absolute;
    opacity: 0;
    pointer-events: none;
}
#talent_create_app .wm-card .aw-work-option .aw-radio {
    width: 18px;
    height: 18px;
    flex-basis: 18px;
    flex-shrink: 0;
    border: 2px solid #CBD5E1;
    border-radius: 50%;
    transition: all 0.2s ease;
}
#talent_create_app .wm-card .aw-work-option:has(input:checked) .aw-radio {
    border-color: var(--aw-accent) !important;
    background: radial-gradient(circle, var(--aw-accent) 0 36%, transparent 40%) !important;
}
#talent_create_app .wm-card .aw-work-option > i {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    flex: 0 0 44px;
    width: 44px !important;
    font-size: 17px;
    color: #64748B;
    transition: color 0.2s ease;
}
#talent_create_app .wm-card .aw-work-option:has(input:checked) > i {
    color: var(--aw-accent) !important;
}
#talent_create_app .wm-card .aw-work-copy {
    display: block;
    min-width: 0;
    flex: 1 1 auto;
}
#talent_create_app .wm-card .aw-work-copy strong {
    display: block;
    color: #0B1545;
    font-size: 12px;
    line-height: 1.3;
    font-weight: 700;
}
#talent_create_app .wm-card .aw-work-copy small {
    display: block;
    margin-top: 3px;
    color: #58658F;
    font-size: 10px;
    line-height: 1.35;
}
#talent_create_app .wm-card .wm-info-strip {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-top: 14px;
    padding: 10px 14px;
    background: #F0F9FF;
    border: 1px solid #BAE6FD;
    border-radius: 6px;
    font-size: 12px;
    color: #0369A1;
}
#talent_create_app .wm-card .wm-info-strip i {
    font-size: 14px;
    color: #0284C7;
    flex-shrink: 0;
}
#talent_create_app .wm-card .wm-info-strip span strong {
    font-weight: 600;
    color: #0369A1;
    margin-right: 4px;
}
#talent_create_app .wm-card .wm-info-strip span small {
    color: #0C4A6E;
    font-size: 11.5px;
}
@media (max-width: 768px) {
    #talent_create_app .wm-card .aw-work-grid {
        grid-template-columns: 1fr;
    }
}

/* Topbar Edit & Add Mode Single-Row Flex Cluster Polish */
.talent-topbar {
    display: flex !important;
    align-items: center !important;
    justify-content: space-between !important;
    flex-wrap: nowrap !important;
    gap: 16px !important;
    min-height: 52px !important;
    padding: 8px 0 !important;
}
.talent-topbar .talent-topbar-copy {
    flex: 0 1 auto !important;
    min-width: 0 !important;
}
.talent-topbar .talent-topbar-title-row {
    display: flex !important;
    align-items: center !important;
    flex-wrap: wrap !important;
    gap: 6px 12px !important;
}
.talent-topbar .talent-topbar-copy h1 {
    font-size: 16px !important;
    line-height: 1.25 !important;
    margin: 0 !important;
    white-space: nowrap !important;
}
.talent-topbar .talent-topbar-copy p {
    font-size: 12px !important;
    color: #58658f !important;
    margin: 2px 0 0 0 !important;
    white-space: nowrap !important;
}
.talent-topbar-right {
    display: inline-flex !important;
    align-items: center !important;
    gap: 10px !important;
    flex: 0 0 auto !important;
    margin-left: auto !important;
}
.talent-topbar .talent-topbar-marketplace {
    display: inline-flex !important;
    align-items: center !important;
    gap: 8px !important;
    min-height: 38px !important;
    height: 38px !important;
    padding: 4px 10px !important;
    border: 1px solid #e1e5ff !important;
    border-radius: 6px !important;
    background: #f7f8ff !important;
    white-space: nowrap !important;
    flex: 0 0 auto !important;
}
.talent-topbar-marketplace-icon {
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    width: 26px !important;
    height: 26px !important;
    flex: 0 0 26px !important;
    border-radius: 50% !important;
    font-size: 13px !important;
}
.talent-topbar-marketplace-icon i {
    font-size: 13px !important;
}
.talent-topbar-marketplace-copy strong {
    font-size: 12px !important;
}
.talent-topbar-marketplace-copy small {
    font-size: 11px !important;
}
.talent-topbar .talent-topbar-actions {
    display: inline-flex !important;
    flex-direction: row !important;
    align-items: flex-start !important;
    gap: 8px !important;
    flex: 0 0 auto !important;
}
.talent-topbar .talent-topbar-actions .talent-topbar-close-btn {
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    height: 38px !important;
    min-height: 38px !important;
    padding: 0 14px !important;
    font-size: 13px !important;
    font-weight: 600 !important;
    color: #4b5675 !important;
    background: #f1f2f6 !important;
    border: 1px solid #d8dbe0 !important;
    border-radius: 6px !important;
    text-decoration: none !important;
    transition: all 0.2s ease !important;
    white-space: nowrap !important;
}
.talent-topbar .talent-topbar-actions .talent-topbar-close-btn:hover {
    background: #e4e6ef !important;
    color: #1b2559 !important;
    border-color: #c4c8d0 !important;
}
.talent-topbar-btn-group {
    display: inline-flex !important;
    flex-direction: column !important;
    align-items: stretch !important;
    gap: 2px !important;
    flex: 0 0 auto !important;
}
.talent-topbar .talent-topbar-actions .talent-topbar-primary {
    width: auto !important;
    min-height: 38px !important;
    height: 38px !important;
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    padding: 0 16px !important;
    font-size: 13px !important;
    font-weight: 600 !important;
    white-space: nowrap !important;
    border-radius: 6px !important;
    border: 1px solid transparent !important;
    color: #ffffff !important;
}
.talent-topbar-primary.talent-stage-action--pool {
    background-color: #7239ea !important;
    border-color: #7239ea !important;
}
.talent-topbar-primary.talent-stage-action--bench {
    background-color: #155eef !important;
    border-color: #155eef !important;
}
.talent-topbar-primary.talent-stage-action--hold {
    background-color: #e9a900 !important;
    border-color: #e9a900 !important;
}
.talent-topbar-primary.talent-stage-action--exited {
    background-color: #e43e57 !important;
    border-color: #e43e57 !important;
}
.talent-topbar--edit .talent-topbar-actions .talent-topbar-primary {
    background-color: #2563EB !important;
    border-color: #2563EB !important;
}
.talent-topbar--edit .talent-topbar-actions .talent-topbar-primary:hover {
    background-color: #1D4ED8 !important;
    border-color: #1D4ED8 !important;
}
.talent-topbar-action-hint {
    display: block !important;
    color: #65718e !important;
    font-size: 10.5px !important;
    line-height: 1.2 !important;
    text-align: center !important;
    white-space: nowrap !important;
    margin: 0 !important;
}
@media (max-width: 991px) {
    .talent-topbar {
        flex-wrap: wrap !important;
        gap: 12px !important;
    }
    .talent-topbar-right {
        width: 100% !important;
        justify-content: space-between !important;
        gap: 10px !important;
    }
}

/* Relationship Manager Comments Card - Metronic Blue Theme */
.fs.rm-note-card,
.rm-note-card {
    overflow: visible !important;
    position: relative;
    z-index: 20;
    border: 1px solid #dbeafe !important;
    border-radius: 8px !important;
    background: #ffffff !important;
    box-shadow: 0 4px 14px rgba(37, 99, 235, 0.05) !important;
    margin-bottom: 20px;
}
.rm-note-card.has-open-dropdown,
.rm-note-card:has(.custom-template-dropdown.is-open) {
    position: relative !important;
    z-index: 1060 !important;
}
.rm-note-card .candidate-card-header {
    min-height: 58px !important;
    padding: 12px 18px !important;
    border-bottom: 1px solid #dbeafe !important;
    border-top-left-radius: 7px !important;
    border-top-right-radius: 7px !important;
    background: linear-gradient(90deg, #f0f6ff 0%, #ffffff 82%) !important;
    display: flex;
    align-items: center;
}
.rm-note-card .candidate-card-title {
    display: flex;
    align-items: center;
    gap: 12px;
}
.rm-note-card .candidate-card-icon--note {
    width: 38px !important;
    height: 38px !important;
    min-width: 38px !important;
    flex-basis: 38px !important;
    border-radius: 8px !important;
    border: 0 !important;
    color: #ffffff !important;
    background: linear-gradient(135deg, #2563eb, #38bdf8) !important;
    box-shadow: 0 4px 10px rgba(37, 99, 235, 0.22) !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
}
.rm-note-card .candidate-card-icon--note i {
    color: #ffffff !important;
    opacity: 1 !important;
    font-size: 18px !important;
    line-height: 1 !important;
    display: inline-block !important;
}
.rm-note-card .candidate-card-title strong {
    color: #071434 !important;
    font-size: 14px !important;
    font-weight: 700 !important;
    display: block;
}
.rm-note-card .candidate-card-title small {
    display: block !important;
    color: #65708e !important;
    font-size: 11px !important;
    line-height: 1.4;
}
.rm-note-card .candidate-card-body,
.rm-note-card .fsc {
    overflow: visible !important;
    position: relative;
    padding: 16px 20px !important;
    display: grid;
    gap: 14px;
}
.rm-note-card .recruiter-template-field {
    overflow: visible !important;
    position: relative;
    z-index: 1062;
}
.rm-note-card .recruiter-template-field label,
.rm-note-card .recruiter-editor-field label {
    display: block;
    margin-bottom: 6px;
    color: #071434 !important;
    font-size: 12px !important;
    font-weight: 700 !important;
}
.rm-note-card .recruiter-template-field label span,
.rm-note-card .recruiter-editor-field label span {
    color: #65708e;
    font-weight: 400;
}
.rm-note-card #btn_sync_rm_template {
    font-size: 11px !important;
    font-weight: 600 !important;
    color: #2563eb !important;
    background: #eff6ff !important;
    border: 1px solid #bfdbfe !important;
    border-radius: 6px !important;
    padding: 3px 10px !important;
    cursor: pointer;
    transition: all 0.15s ease;
}
.rm-note-card #btn_sync_rm_template:hover {
    background: #2563eb !important;
    color: #ffffff !important;
    border-color: #2563eb !important;
}
.rm-note-card .recruiter-template-control {
    position: relative;
    display: flex;
    align-items: center;
}
.rm-note-card .recruiter-template-control > i {
    position: absolute;
    z-index: 2;
    left: 13px;
    color: #2563eb !important;
    font-size: 13px !important;
    pointer-events: none;
}
.rm-note-card #recruiter_note_template_select {
    width: 100%;
    height: 40px;
    padding: 0 36px 0 36px !important;
    border: 1px solid #dbeafe !important;
    border-radius: 6px !important;
    color: #071434 !important;
    background: #ffffff !important;
    font-size: 12px !important;
    font-weight: 500 !important;
    font-family: 'Poppins', sans-serif !important;
    cursor: pointer;
    transition: border-color 0.15s ease, box-shadow 0.15s ease;
}
.rm-note-card #recruiter_note_template_select:focus {
    border-color: #2563eb !important;
    box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.12) !important;
    outline: none;
}
.rm-note-card textarea#recruiter_note {
    width: 100%;
    min-height: 140px !important;
    padding: 12px !important;
    border: 1px solid #dbeafe !important;
    border-radius: 6px !important;
    font-size: 12px !important;
    line-height: 1.6;
    font-family: 'Poppins', sans-serif !important;
    color: #1e293b;
    background-color: #ffffff;
    resize: vertical;
    transition: border-color 0.15s ease, box-shadow 0.15s ease;
}
.rm-note-card textarea#recruiter_note:focus {
    border-color: #2563eb !important;
    box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.12) !important;
    outline: none;
}
.rm-note-card textarea#recruiter_note::placeholder {
    color: #94a3b8;
    font-style: normal;
}
.rm-note-card .recruiter-private-note {
    display: inline-flex;
    align-items: center;
    gap: 7px;
    width: fit-content;
    margin-top: 10px;
    padding: 6px 14px;
    border: 1px solid #bfdbfe !important;
    border-radius: 18px;
    color: #1e40af !important;
    background: #eff6ff !important;
    font-size: 11px;
    font-weight: 500;
}
.rm-note-card .recruiter-private-note i {
    font-size: 11px;
    color: #2563eb !important;
}
.rm-note-card.has-validation-error,
.rm-note-card.field-attention-highlight,
.rm-note-card .has-validation-error {
    border-color: #f1416c !important;
}

/* Custom Template Dropdown Selection (media_1790970788987.png) */
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
    border: 1px solid #dbeafe;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.18s ease;
    user-select: none;
}

.custom-template-trigger:hover {
    border-color: #2563eb;
    background: #f8fafc;
}

.custom-template-trigger:focus,
.custom-template-dropdown.is-open .custom-template-trigger {
    border-color: #2563eb !important;
    box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.12) !important;
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
    color: #2563eb;
    flex-shrink: 0;
}

.custom-template-trigger .trigger-label {
    font-size: 13px;
    font-weight: 500;
    color: #1e293b;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
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

.custom-template-dropdown.is-open .custom-template-trigger .trigger-arrow {
    transform: rotate(180deg);
    color: #2563eb;
}

.custom-template-menu {
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
    font-size: 12.5px;
    font-weight: 700;
    letter-spacing: 0.2px;
    margin-bottom: 4px;
}

.tpl-cat--blue {
    background: #eff6ff;
    color: #1d4ed8;
}

.tpl-cat--green {
    background: #f0fdf4;
    color: #166534;
}

.tpl-cat--amber {
    background: #fffbeb;
    color: #92400e;
}

.tpl-cat--purple {
    background: #f5f3ff;
    color: #6d28d9;
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
    background: #f8fafc;
    border-color: #e2e8f0;
}

.tpl-card-item.is-selected {
    background: #eff6ff !important;
    border-color: #bfdbfe !important;
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
    color: #2563eb;
    font-size: 16px;
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
}

.tpl-card-subtitle {
    font-size: 11.5px;
    color: #64748b;
    line-height: 1.35;
    margin-top: 1px;
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
    font-size: 18px;
    color: #2563eb;
}

.tpl-card-item.is-selected .tpl-card-check {
    display: inline-flex !important;
}
</style>
<link href="{{ asset('demo/css/filterStyle.css') }}{{ TM_VERSION }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('demo/css/candidate-form.css') }}{{ TM_VERSION }}&bench_commercials=20260914&professional_profile=20260914&basic_availability=20260914&location_dropdown=20260914&location_typography=20260914&availability_date=20260914&fa6_fix=20260914&skills_header=20260914&location_enterprise=20260914&availability_picker=20260914&resume_summary_cards=20260914&relationship_manager_card=20260914&top_header=20260914&additional_details_card=20260914&enterprise_polish=20260914&professional_row_alignment=20260914&final_enterprise_polish=20260914&rm_summary_fix=20260914&availability_additional_fix=20260914&resume_note_visibility=20260915&create_card_order_icons=20260915&create_field_polish=20260915&create_global_phone_experience=20260915&commercial_state_colors=20260921&talent_stage_privacy=20260921&commercial_blue_green=20260921&availability_work_colors=20260921&skills_expertise=20260921&talent_stage_header=20260921&alignment_icon_comments=20260921&font_aliases=20260921&page_header_flow=20260921&talent_stage_skill_selects=20260921&sticky_compact_header=20260921&create_defaults_icons=20260921&bench_header_lavender=20260928" rel="stylesheet" type="text/css" />
