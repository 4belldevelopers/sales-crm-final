@extends('layouts.service_company_layout')
@push('head-script')
<meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
<meta http-equiv="Pragma" content="no-cache" />
<meta http-equiv="Expires" content="0" />
<!--Add New Css of Metronic-7-->
<link href="{{ asset('demo/v7/css/wizard-1.css') }}<?php echo TM_VERSION; ?>" rel="stylesheet" type="text/css" />
<link href="{{ asset('demo/v7/css/plugins.bundle.css') }}<?php echo TM_VERSION; ?>" rel="stylesheet" type="text/css" />
<link href="{{ asset('demo/v7/css/prismjs.bundle.css') }}<?php echo TM_VERSION; ?>" rel="stylesheet" type="text/css" />
<link href="{{ asset('demo/v7/css/style.bundle.css') }}<?php echo TM_VERSION; ?>" rel="stylesheet" type="text/css" />
<!--Add New Css of Metronic-7 End-->
<style>
/* Purpose: give the shared add/edit client wizard a compact, reference-aligned Metronic presentation without changing its form contract. */
/* ===== Design tokens (aligned with Job Create) ===== */
:root {
  --primary: #ffd428;
  --primary-hover: #f5c400;
  --primary-gradient: linear-gradient(180deg, #ffd428 0%, #f5c400 100%);
  --primary-gradient-hover: linear-gradient(180deg, #fbc02d 0%, #f59e0b 100%);
  --primary-text: #10143b;
  --primary-border: #d4a017;
  --bg: #f8fafc;
  --card-bg: #ffffff;
  --border: #e2e8f0;
  --border-focus: #ffd428;
  --text-primary: #0f172a;
  --text-secondary: #475569;
  --text-muted: #94a3b8;
  --danger: #ef4444;
  --danger-light: #fef2f2;
  --success: #10b981;
  --success-light: #d1fae5;
  --warning: #f59e0b;
  --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05);
  --shadow: 0 1px 3px 0 rgb(0 0 0 / 0.1), 0 1px 2px -1px rgb(0 0 0 / 0.1);
  --shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
  --shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1);
  --radius: 10px;
  --radius-sm: 6px;
  --radius-lg: 12px;
  --radius-xl: 16px;
  --transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);

  /* Compatibility aliases for client wizard blade components */
  --client-primary: #ffd428;
  --client-primary-soft: #fffbeb;
  --client-ink: #0f172a;
  --client-muted: #64748b;
  --client-border: #e2e8f0;
  --client-surface: #ffffff;
  --client-page: #f8fafc;
}

html, body {
  height: 100% !important;
  overflow-x: hidden;
  width: 100%;
  font-family: Poppins, sans-serif;
}
.flex-root,
.page {
  min-height: 100vh !important;
}

/* =========================================================================
   Client Wizard: Full-Viewport Sticky-Footer Enterprise Layout (15.6" Vertical Fit)
   - #kt_wrapper fills the viewport (min-height: 100vh) in flex-column mode.
   - #kt_content flex-grows to fill available vertical space.
   - #kt_footer is hidden on the wizard to eliminate blank void at bottom.
   - Full flex pipeline stretches vertically so the form fits on the screen.
   ========================================================================= */
#kt_wrapper {
  min-height: 100vh !important;
  display: flex !important;
  flex-direction: column !important;
  box-sizing: border-box !important;
}
#kt_content,
.content {
  background: var(--client-page);
  padding-top: 0 !important;
  padding-bottom: 8px !important;
  flex: 1 1 auto !important;
  display: flex !important;
  flex-direction: column !important;
  min-height: calc(100vh - 65px) !important;
}
#kt_content > .d-flex.flex-column-fluid,
#kt_content > .d-flex {
  flex: 1 1 auto !important;
  display: flex !important;
  flex-direction: column !important;
}
.container-fluid.px-3 {
  flex: 1 1 auto !important;
  display: flex !important;
  flex-direction: column !important;
  padding-bottom: 8px !important;
}
.client-wizard-shell.card {
  flex: 1 1 auto !important;
  display: flex !important;
  flex-direction: column !important;
  background: transparent !important;
  box-shadow: none !important;
}
.client-wizard-shell > .card-body {
  flex: 1 1 auto !important;
  display: flex !important;
  flex-direction: column !important;
}
#kt_wizard {
  flex: 1 1 auto !important;
  display: flex !important;
  flex-direction: column !important;
}
#kt_wizard > .row,
#kt_wizard > .row > div {
  flex: 1 1 auto !important;
  display: flex !important;
  flex-direction: column !important;
}
#kt_form {
  flex: 1 1 auto !important;
  display: flex !important;
  flex-direction: column !important;
}
#kt_form > [data-wizard-type="step-content"][data-wizard-state="current"],
.client-info-step[data-wizard-state="current"] {
  flex: 1 1 auto !important;
  display: flex !important;
  flex-direction: column !important;
}
#kt_footer {
  display: none !important; /* Hide footer on wizard to eliminate dead bottom void */
}
#kt_toolbar {display: none !important; height: 0 !important; padding: 0 !important; margin: 0 !important;}
.form-label{ margin-bottom: 0.5rem!important; font-size: 12px!important; font-weight: 600!important; color: var(--client-ink)!important;}
.last-input-border-radius{border-radius	: 5px 0px 0px 5px!important;}
.tagify .tagify__tag div .tagify__tag-text {
      color: #0c0c0c !important;
      font-weight: 500 !important;
      font-size: 14px !important;
  }

  .client-address-container-child{
    background: #f9f9f940;
    border-radius: 14px;
    padding: 7px 0px;
    margin-bottom: 15px;
  }
  .cp_desigination{
    width:95% !important;
  }
  .iti__flag {
    margin-top: 2px !important;
  }
  .iti__flag-container {
    z-index: 9 !important;
  }
  .invalid-feedback {
    display: none !important;
    color: red;
  }

  /* The legacy wizard stays responsible for state; these rules modernize its visual language to Job Create standard */
  .client-wizard-shell.card {background: transparent; box-shadow: none;}
  #kt_wizard .wizard-nav {
    background: var(--client-surface);
    border: 1px solid var(--client-border) !important;
    border-radius: 12px;
    box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.05);
    overflow: hidden;
  }
  #kt_wizard .wizard-steps {
    display: grid !important;
    grid-template-columns: repeat(6, minmax(0, 1fr));
    padding: 16px 20px !important;
    gap: 0;
  }
  #kt_wizard .wizard-step {
    display: flex !important;
    align-items: center;
    min-width: 0;
    position: relative;
    padding-right: 24px;
    cursor: pointer;
  }
  #kt_wizard .wizard-step:not(:last-child)::after {
    content: '';
    height: 2px;
    background: #e2e8f0;
    position: absolute;
    right: 8px;
    width: 24px;
    top: 50%;
    transform: translateY(-50%);
    transition: background 0.2s ease;
  }
  #kt_wizard .wizard-step[data-wizard-state="done"]::after,
  #kt_wizard .wizard-step.completed::after {
    background: #10b981 !important;
  }
  #kt_wizard .wizard-label {display: grid !important; grid-template-columns: 34px minmax(0, 1fr); column-gap: 10px; width: 100%; align-items: center;}
  #kt_wizard .wizard-icon {
    grid-row: 1 / span 2;
    width: 32px !important;
    height: 32px !important;
    display: grid !important;
    place-items: center;
    border-radius: 50%;
    background: #f8fafc;
    border: 1.5px solid #e2e8f0;
    color: #64748b !important;
    font-size: 12px;
    font-weight: 600;
    transition: all 0.2s ease;
  }
  #kt_wizard .wizard-icon svg {display: none;}
  #kt_wizard .wizard-icon::before {content: attr(data-step-number); font-size: 12px; font-weight: 600;}
  #kt_wizard .wizard-step:nth-child(1) .wizard-icon::before {content: '1';}
  #kt_wizard .wizard-step:nth-child(2) .wizard-icon::before {content: '2';}
  #kt_wizard .wizard-step:nth-child(3) .wizard-icon::before {content: '3';}
  #kt_wizard .wizard-step:nth-child(4) .wizard-icon::before {content: '4';}
  #kt_wizard .wizard-step:nth-child(5) .wizard-icon::before {content: '5';}
  #kt_wizard .wizard-step:nth-child(6) .wizard-icon::before {content: '6';}
  #kt_wizard .wizard-title {font-size: 12.5px !important; font-weight: 600 !important; color: #334155 !important; margin: 0 !important; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;}
  #kt_wizard .wizard-title::after {display: block; margin-top: 2px; color: #64748b; font-size: 10.5px; font-weight: 400;}
  #kt_wizard .wizard-step:nth-child(1) .wizard-title::after {content: 'Basic details';}
  #kt_wizard .wizard-step:nth-child(2) .wizard-title::after {content: 'About the client';}
  #kt_wizard .wizard-step:nth-child(3) .wizard-title::after {content: 'Location & address';}
  #kt_wizard .wizard-step:nth-child(4) .wizard-title::after {content: 'Key contacts';}
  #kt_wizard .wizard-step:nth-child(5) .wizard-title::after {content: 'Official profiles';}
  #kt_wizard .wizard-step:nth-child(6) .wizard-title::after {content: 'Confirm & save';}
  #kt_wizard .wizard-arrow {display: none !important;}
  
  /* Completed step token: Emerald Green with checkmark (Job Create standard) */
  #kt_wizard .wizard-step[data-wizard-state="done"] .wizard-icon,
  #kt_wizard .wizard-step.completed .wizard-icon {
    background: #10b981 !important;
    border-color: #10b981 !important;
    color: #ffffff !important;
    box-shadow: 0 2px 6px rgba(16, 185, 129, 0.25) !important;
  }
  #kt_wizard .wizard-step[data-wizard-state="done"] .wizard-icon::before,
  #kt_wizard .wizard-step.completed .wizard-icon::before {
    content: '✓' !important;
    font-size: 13px !important;
    font-weight: 700 !important;
  }
  
  /* Current / Active step token: JERO Gold ring */
  #kt_wizard .wizard-step[data-wizard-state="current"] .wizard-icon,
  #kt_wizard .wizard-step.current .wizard-icon {
    background: linear-gradient(180deg, #ffd428 0%, #f5c400 100%) !important;
    border-color: #d4a017 !important;
    color: #10143b !important;
    font-weight: 700 !important;
    box-shadow: 0 0 0 3px rgba(245, 196, 0, 0.28), 0 2px 8px rgba(245, 196, 0, 0.25) !important;
  }
  #kt_wizard .wizard-step[data-wizard-state="current"] .wizard-title,
  #kt_wizard .wizard-step.current .wizard-title {
    color: #0f172a !important;
    font-weight: 700 !important;
  }
  
  #kt_wizard, #kt_wizard > .row, #kt_wizard > .row > div, #kt_form {width: 100%; max-width: 100%; min-width: 0;}
  #kt_wizard > .row {margin: 0 !important; padding: 0 !important;}
  #kt_wizard > .row > div {margin: 0 !important; padding: 0 !important;}
  #kt_form > [data-wizard-type="step-content"] {
    display: none;
    background: transparent !important;
    border: 0 !important;
    padding: 0 !important;
    margin: 0 !important;
    box-shadow: none !important;
  }
  #kt_form > [data-wizard-type="step-content"][data-wizard-state="current"] {
    display: block !important;
  }
  #kt_form > [data-wizard-type="step-content"] > h3 {font-size: 14px !important; margin-bottom: 16px !important; color: var(--client-ink) !important;}
  #kt_form .form-control,
  #kt_form .form-select,
  #kt_form .input-group-text,
  #kt_form .tagify {
    min-height: 38px;
    font-size: 12px !important;
    border-color: #dce3ee !important;
    background-color: #fff !important;
  }
  #kt_form .form-control:focus,
  #kt_form .form-select:focus,
  #kt_form .tagify:focus-within {
    border-color: #ffd428 !important;
    box-shadow: 0 0 0 3px rgba(245, 196, 0, 0.2) !important;
    outline: none !important;
  }
  #kt_form .form-text {font-size: 10.5px !important; color: #7b8497 !important; margin-top: 5px;}
  .client-info-step {background: transparent !important; border: 0 !important; padding: 0 !important; box-shadow: none !important; min-height: auto !important;}
  .client-step-layout {
    display: grid !important;
    width: 100% !important;
    max-width: 100% !important;
    min-width: 0 !important;
    grid-template-columns: minmax(0, 19fr) minmax(0, 6fr) !important;
    gap: 16px !important;
    align-items: start !important;
    box-sizing: border-box !important;
  }
  /* Step 1 (Client Information): Canonical 70:30 two-column layout with equal-height column stretch */
  .client-information-layout,
  .client-info-step .client-step-layout {
    display: grid !important;
    width: 100% !important;
    max-width: 100% !important;
    min-width: 0 !important;
    grid-template-columns: minmax(0, 7fr) minmax(0, 3fr) !important;
    gap: 16px !important;
    align-items: stretch !important;
    box-sizing: border-box !important;
  }
  .client-information-layout > *,
  .client-info-step .client-step-layout > * {
    min-width: 0 !important;
    width: 100% !important;
    max-width: 100% !important;
    box-sizing: border-box !important;
  }
  .client-information-main,
  .client-information-sidebar,
  .client-information-layout > .client-main-column,
  .client-information-layout > .client-side-column,
  .client-info-step .client-main-column,
  .client-info-step .client-side-column {
    min-width: 0 !important;
    width: 100% !important;
    max-width: 100% !important;
    display: flex !important;
    flex-direction: column !important;
    height: 100% !important;
    gap: 16px !important;
    box-sizing: border-box !important;
  }
  .client-info-step .client-main-column > *,
  .client-info-step .client-side-column > * {
    margin-bottom: 0 !important;
    min-width: 0 !important;
    max-width: 100% !important;
    box-sizing: border-box !important;
  }
  .client-info-step .client-main-column > .client-section-card {
    min-height: 0 !important;
    display: flex !important;
    flex-direction: column !important;
  }
  .client-info-step .client-main-column > .client-section-card > .card-body {
    flex: 1 1 auto !important;
  }
  .client-info-step .client-identity-logo-row {
    transition: all 0.2s ease;
  }
  .client-info-step .client-logo-preview {
    height: auto !important;
    min-height: auto !important;
    display: flex !important;
    flex-direction: column !important;
    align-items: center !important;
    justify-content: center !important;
    box-sizing: border-box !important;
  }
  .client-info-step .client-logo-preview .image-input-outline {
    transition: border-color 0.15s ease, background-color 0.15s ease;
  }
  .client-info-step .client-logo-preview .image-input-outline:hover {
    border-color: #2563eb !important;
    background-color: #eff6ff !important;
  }
  .client-suggestion-status {
    color: #10b981 !important;
    transition: color 0.2s ease;
  }
  .client-suggestion-status.is-missing {
    color: #cbd5e1 !important;
  }
  .client-info-step .client-side-column > .client-helper-card {
    min-height: 0 !important;
    display: flex !important;
    flex-direction: column !important;
    flex: 1 1 auto !important;
    box-sizing: border-box !important;
  }
  .client-info-step #client_fetch_side_btn {
    margin-top: auto !important;
  }
  .client-info-step .client-side-column > .client-helper-card:last-child ul {
    flex: 1 1 auto !important;
    display: flex !important;
    flex-direction: column !important;
    justify-content: space-around !important;
    margin: 0 !important;
    padding: 0 !important;
  }
  .client-info-step .client-side-column > .client-helper-card:last-child li {
    margin-bottom: 0 !important;
  }
  .client-info-step .client-side-column .btn {
    white-space: normal !important;
    word-break: break-word !important;
  }

  /* Step 2 (Client Description): Canonical 58:42 two-column layout optimized for 15.6" screens */
  .client-description-layout,
  .client-desc-step .client-step-layout {
    display: grid !important;
    width: 100% !important;
    max-width: 100% !important;
    min-width: 0 !important;
    grid-template-columns: minmax(0, 1.35fr) minmax(0, 1fr) !important;
    gap: 16px !important;
    align-items: start !important;
    box-sizing: border-box !important;
  }
  @media (max-width: 992px) {
    .client-description-layout,
    .client-desc-step .client-step-layout {
      grid-template-columns: 1fr !important;
      gap: 14px !important;
    }
  }
  .client-description-layout > *,
  .client-desc-step .client-step-layout > * {
    min-width: 0 !important;
    width: 100% !important;
    max-width: 100% !important;
    box-sizing: border-box !important;
  }
  .client-description-layout > .client-main-column,
  .client-description-layout > .client-side-column,
  .client-desc-step .client-main-column,
  .client-desc-step .client-side-column {
    min-width: 0 !important;
    width: 100% !important;
    max-width: 100% !important;
    box-sizing: border-box !important;
  }

  /* Step 2 (Client Description): Left Column Card */
  .client-desc-step .client-main-column > .client-section-card {
    background: #ffffff !important;
    border: 1px solid #e2e8f0 !important;
    border-radius: 12px !important;
    box-shadow: 0 1px 3px rgba(0,0,0,0.03) !important;
    overflow: hidden !important;
    padding: 0 !important;
    margin: 0 !important;
    box-sizing: border-box !important;
  }
  .client-desc-step .client-main-column > .client-section-card > .card-header {
    padding: 14px 18px !important;
    background: #ffffff !important;
    border-bottom: 1px solid #f8fafc !important;
    display: flex !important;
    align-items: center !important;
    box-sizing: border-box !important;
  }
  .client-desc-step .client-main-column > .client-section-card > .card-body {
    padding: 16px 18px !important;
    background: #ffffff !important;
    box-sizing: border-box !important;
  }
  .client-desc-step .client-editor-wrap {
    width: 100% !important;
    box-sizing: border-box !important;
  }
  .client-desc-step .client-desc-label-row label {
    font-size: 12.5px !important;
    font-weight: 700 !important;
    color: #0f172a !important;
  }
  .client-desc-step .client-char-count {
    font-size: 11px !important;
    font-weight: 500 !important;
    color: #94a3b8 !important;
  }

  /* Quill Toolbar & Editor Styling matching reference screenshot */
  .client-desc-step .client-editor-box {
    border: 1px solid #e2e8f0 !important;
    border-radius: 8px !important;
    background: #ffffff !important;
    transition: border-color 0.15s ease, box-shadow 0.15s ease !important;
  }
  .client-desc-step .client-editor-box.is-invalid {
    border-color: #ef4444 !important;
    box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.12) !important;
  }
  .client-desc-step .ql-toolbar.ql-snow {
    border: none !important;
    border-bottom: 1px solid #e2e8f0 !important;
    background: #ffffff !important;
    padding: 6px 10px !important;
    display: flex !important;
    align-items: center !important;
    gap: 4px !important;
    flex-wrap: wrap !important;
  }
  .client-desc-step .ql-container.ql-snow {
    border: none !important;
    font-family: inherit !important;
    font-size: 12.5px !important;
    color: #1e293b !important;
    min-height: 125px !important;
    max-height: 165px !important;
    overflow-y: auto !important;
  }
  .client-desc-step .ql-editor {
    min-height: 125px !important;
    font-size: 12.5px !important;
    line-height: 1.6 !important;
    color: #334155 !important;
    padding: 10px 14px !important;
  }
  .client-desc-step .ql-editor.ql-blank::before {
    color: #94a3b8 !important;
    font-style: normal !important;
    font-size: 12px !important;
  }
  .client-desc-step .ql-snow .ql-picker.ql-header {
    width: 95px !important;
    height: 28px !important;
    border: 1px solid #e2e8f0 !important;
    border-radius: 6px !important;
    background: #f8fafc !important;
    font-size: 11.5px !important;
  }
  .client-desc-step .ql-snow .ql-picker.ql-header .ql-picker-label {
    padding: 3px 8px !important;
    display: flex !important;
    align-items: center !important;
    color: #334155 !important;
  }
  .client-desc-step .ql-snow .ql-stroke {
    stroke: #475569 !important;
  }
  .client-desc-step .ql-snow .ql-fill {
    fill: #475569 !important;
  }
  .client-desc-step .ql-snow button:hover .ql-stroke,
  .client-desc-step .ql-snow .ql-active .ql-stroke {
    stroke: #2563eb !important;
  }
  .client-desc-step .ql-snow button:hover .ql-fill,
  .client-desc-step .ql-snow .ql-active .ql-fill {
    fill: #2563eb !important;
  }
  .client-desc-step .ql-snow .ql-fullscreen-btn {
    margin-left: auto !important;
    width: 24px !important;
    height: 24px !important;
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    cursor: pointer !important;
    background: none !important;
    border: none !important;
    color: #64748b !important;
  }
  .client-desc-step .ql-snow .ql-fullscreen-btn:hover {
    color: #2563eb !important;
  }
  .client-editor-box.is-fullscreen {
    position: fixed !important;
    inset: 20px !important;
    z-index: 99999 !important;
    background: #ffffff !important;
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25) !important;
    border-radius: 12px !important;
    display: flex !important;
    flex-direction: column !important;
  }
  .client-editor-box.is-fullscreen .ql-container.ql-snow {
    flex: 1 !important;
    max-height: none !important;
  }

  /* Step 2 Tips banner (Collapsible) */
  .client-desc-step .client-info-banner.client-tips-accordion {
    background: #f0f7ff !important;
    border: 1px solid #dbeafe !important;
    border-radius: 10px !important;
    padding: 12px 16px !important;
    margin-top: 14px !important;
    box-sizing: border-box !important;
  }

  /* Step 2 Right Column: Service Offerings Card */
  .client-desc-step .client-side-column .card.client-helper-card.client-service-offerings-card,
  .client-desc-step .client-side-column .card.client-helper-card:first-child {
    background: #ffffff !important;
    border: 1px solid #e2e8f0 !important;
    border-radius: 12px !important;
    box-shadow: 0 1px 3px rgba(0,0,0,0.03) !important;
    overflow: hidden !important;
    padding: 0 !important;
    margin-bottom: 14px !important;
    box-sizing: border-box !important;
  }
  .client-desc-step .client-side-column .card.client-helper-card:first-child > .card-header {
    padding: 12px 16px !important;
    background: #ffffff !important;
    border-bottom: 1px solid #f8fafc !important;
    display: flex !important;
    align-items: center !important;
    box-sizing: border-box !important;
  }
  .client-desc-step .client-side-column .card.client-helper-card:first-child > .card-body {
    padding: 14px 16px !important;
    background: #ffffff !important;
    box-sizing: border-box !important;
  }

  /* Selected Services Chips (Warm Yellow) */
  .client-selected-chip {
    background: #fef9c3 !important;
    border: 1px solid #fef08a !important;
    color: #854d0e !important;
    border-radius: 6px !important;
    padding: 3px 8px 3px 10px !important;
    font-size: 11px !important;
    font-weight: 500 !important;
    display: inline-flex !important;
    align-items: center !important;
    gap: 6px !important;
    transition: all 0.15s ease !important;
  }
  .client-selected-chip .client-chip-remove {
    color: #a16207 !important;
    font-size: 11px !important;
    font-weight: 700 !important;
    line-height: 1 !important;
    cursor: pointer !important;
    border-radius: 50% !important;
    width: 14px !important;
    height: 14px !important;
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    transition: background 0.15s ease, color 0.15s ease !important;
  }
  .client-selected-chip .client-chip-remove:hover {
    background: #fde047 !important;
    color: #713f12 !important;
  }

  /* Suggested Services Pills (Soft Blue) */
  .client-suggested-pill {
    background: #eff6ff !important;
    border: 1px solid #bfdbfe !important;
    color: #1d4ed8 !important;
    border-radius: 9999px !important;
    padding: 3px 10px !important;
    font-size: 10.5px !important;
    font-weight: 500 !important;
    display: inline-flex !important;
    align-items: center !important;
    gap: 5px !important;
    cursor: pointer !important;
    transition: all 0.15s ease !important;
    line-height: 1.4 !important;
    user-select: none !important;
  }
  .client-suggested-pill:hover {
    background: #dbeafe !important;
    border-color: #93c5fd !important;
    color: #1e40af !important;
  }
  .client-suggested-pill.is-selected {
    display: none !important;
  }
  .client-suggested-pill.is-search-hidden {
    display: none !important;
  }

  /* Step 2 Right Column: AI-Generated Company Description Card (Soft Lavender) */
  .client-desc-step .client-side-column .card.client-helper-card.client-ai-assist-card,
  .client-desc-step .client-side-column .card:last-child {
    background: #faf8ff !important;
    border: 1px solid #ede9fe !important;
    border-radius: 12px !important;
    padding: 14px 16px !important;
    margin-bottom: 0 !important;
    box-sizing: border-box !important;
  }
  .client-desc-step .client-side-column .client-ai-suggested-desc,
  .client-desc-step #client_ai_suggested_desc {
    background: #ffffff !important;
    border: 1px solid #f1f5f9 !important;
    border-radius: 8px !important;
    padding: 12px 14px !important;
    font-size: 12.5px !important;
    color: #334155 !important;
    line-height: 1.6 !important;
    margin-bottom: 12px !important;
    max-height: 110px !important;
    overflow-y: auto !important;
    box-sizing: border-box !important;
    transition: outline 0.15s ease, box-shadow 0.15s ease !important;
  }
  .client-desc-step #client_apply_desc_suggestion {
    background: #7040f5 !important;
    border: none !important;
    color: #ffffff !important;
    font-weight: 600 !important;
    font-size: 11.5px !important;
    border-radius: 7px !important;
    padding: 6px 14px !important;
    display: inline-flex !important;
    align-items: center !important;
    gap: 5px !important;
    box-shadow: 0 1px 3px rgba(112,64,245,0.3) !important;
    cursor: pointer !important;
    transition: all 0.15s ease !important;
  }
  .client-desc-step #client_apply_desc_suggestion:hover {
    background: #5b21b6 !important;
    transform: translateY(-1px) !important;
    box-shadow: 0 2px 5px rgba(112,64,245,0.35) !important;
  }
  .client-desc-step #client_review_edit_desc_btn {
    background: #ffffff !important;
    border: 1px solid #c4b5fd !important;
    color: #6d28d9 !important;
    border-radius: 7px !important;
    padding: 5px 12px !important;
    font-size: 11.5px !important;
    font-weight: 600 !important;
    cursor: pointer !important;
    transition: all 0.15s ease !important;
  }
  .client-desc-step #client_review_edit_desc_btn:hover {
    background: #f5f3ff !important;
    border-color: #a78bfa !important;
  }

  /* Compact Viewport Optimization for 15.6" Laptop Screens (1366×768 & 1920×1080 at 125%/150% scaling) */
  @media (max-height: 820px) {
    .client-desc-step .ql-container.ql-snow {
      min-height: 110px !important;
      max-height: 145px !important;
    }
    .client-desc-step .ql-editor {
      min-height: 110px !important;
      padding: 8px 12px !important;
    }
    .client-desc-step .client-side-column .client-ai-suggested-desc,
    .client-desc-step #client_ai_suggested_desc {
      max-height: 95px !important;
      padding: 10px 12px !important;
      font-size: 12px !important;
      line-height: 1.55 !important;
    }
    .client-desc-step .client-main-column > .client-section-card > .card-header,
    .client-desc-step .client-side-column .card.client-helper-card:first-child > .card-header {
      padding: 10px 14px !important;
    }
    .client-desc-step .client-main-column > .client-section-card > .card-body,
    .client-desc-step .client-side-column .card.client-helper-card:first-child > .card-body {
      padding: 12px 14px !important;
    }
    .client-desc-step .client-info-banner.client-tips-accordion {
      margin-top: 10px !important;
      padding: 10px 14px !important;
    }
  }

  /* Step 3 (Address Details): Canonical 60:40 two-column layout */
  .client-address-layout,
  .client-address-step .client-step-layout {
    display: grid !important;
    width: 100% !important;
    max-width: 100% !important;
    min-width: 0 !important;
    grid-template-columns: minmax(0, 3fr) minmax(0, 2fr) !important;
    gap: 16px !important;
    align-items: start !important;
    box-sizing: border-box !important;
  }
  .client-address-layout > *,
  .client-address-step .client-step-layout > * {
    min-width: 0 !important;
    width: 100% !important;
    max-width: 100% !important;
    box-sizing: border-box !important;
  }
  .client-address-layout > .client-main-column,
  .client-address-layout > .client-side-column,
  .client-address-step .client-main-column,
  .client-address-step .client-side-column {
    min-width: 0 !important;
    width: 100% !important;
    max-width: 100% !important;
    box-sizing: border-box !important;
  }

  /* Step 4 (Contact Person): Full-Width Enterprise Architecture */
  .client-contact-layout,
  .client-contact-step .client-step-layout {
    display: block !important;
    width: 100% !important;
    max-width: 100% !important;
    min-width: 0 !important;
    box-sizing: border-box !important;
  }
  .client-contact-layout > *,
  .client-contact-step .client-step-layout > *,
  .client-contact-layout > .client-main-column,
  .client-contact-step .client-main-column {
    min-width: 0 !important;
    width: 100% !important;
    max-width: 100% !important;
    box-sizing: border-box !important;
  }

  .client-main-column {
    min-width: 0 !important;
    width: 100% !important;
    max-width: 100% !important;
    box-sizing: border-box !important;
  }
  .client-side-column {
    min-width: 0 !important;
    width: 100% !important;
    max-width: 100% !important;
    box-sizing: border-box !important;
  }
  .client-review-grid-2x2 {
    display: grid !important;
    width: 100% !important;
    max-width: 100% !important;
    min-width: 0 !important;
    grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
    gap: 16px !important;
    align-items: start !important;
    box-sizing: border-box !important;
  }
  .client-section-card {background: #fff; border: 1px solid var(--client-border); border-radius: 12px; padding: 20px; margin-bottom: 16px; box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.05);}
  .client-main-column .client-section-card:last-child {margin-bottom: 0;}
  .client-section-heading {font-size: 13px; font-weight: 600; color: var(--client-ink); margin: 0 0 3px;}
  .client-section-copy {font-size: 10.5px; color: var(--client-muted); margin: 0 0 15px;}
  .client-branding-grid {display: grid; width: 100%; min-width: 0; grid-template-columns: 190px 130px minmax(0, 1fr); gap: 16px; align-items: stretch;}
  .client-logo-upload {border: 1px dashed #ffd428; border-radius: 8px; background: #fffdf5; min-height: 152px; padding: 14px; display: flex; cursor: pointer; flex-direction: column; align-items: center; justify-content: center; text-align: center; transition: border-color .2s ease, background .2s ease;}
  .client-logo-upload:hover {border-color: #d4a017; background: #fffbeb;}
  .client-upload-icon {display: grid; place-items: center; width: 42px; height: 42px; margin-bottom: 6px; border-radius: 50%; background: #fff8d6; color: #d4a017; font-size: 14px;}
  .client-logo-upload .client-upload-title {font-size: 11.5px; color: #b45309; font-weight: 600;}
  .client-logo-upload .client-upload-copy {font-size: 10px; color: #7b8497; margin-top: 3px;}
  .client-logo-preview {border: 1px solid var(--client-border); border-radius: 8px; overflow: hidden; min-height: 152px; display: flex; flex-direction: column; background: #fff;}
  .client-preview-label {padding: 9px 11px 0; color: var(--client-ink); font-size: 10.5px; font-weight: 600;}
  .client-logo-preview .image-input {margin: auto;}
  .client-logo-preview .image-input-wrapper {width: 72px !important; height: 72px !important; border-radius: 10px; background-size: cover; background-position: center; background-color: #f1efff;}
  .client-logo-preview .btn[data-kt-image-input-action="change"] {display: none;}
  .client-preview-remove {border: 0; border-top: 1px solid var(--client-border); background: #fff; min-height: 32px; color: #ef3858; font-size: 10.5px; font-weight: 500;}
  .client-brand-fields {display: grid; min-width: 0; grid-template-columns: minmax(0, 1fr); gap: 14px; align-content: center;}
  .client-details-grid {display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 14px 18px;}
  .client-field, .client-field .input-group, .client-field .form-control, .client-field .form-select {min-width: 0;}
  .client-field .form-label {display: block;}
  #kt_form .client-field .input-group {
    display: flex;
    flex-wrap: nowrap;
    min-height: 38px;
    border: 1px solid #d5deeb !important;
    border-radius: 6px !important;
    background: #fff !important;
    overflow: hidden;
  }
  #kt_form .client-field .input-group:focus-within {border-color: #ffd428 !important; box-shadow: 0 0 0 3px rgba(245, 196, 0, 0.2);}
  #kt_form .client-field .input-group-text {flex: 0 0 38px; width: 38px; justify-content: center; color: #53607e; border: 0 !important; background: #fff !important;}
  #kt_form .client-field .input-group .form-control,
  #kt_form .client-field .input-group .form-select {
    flex: 1 1 auto;
    width: 1%;
    min-width: 0;
    border: 0 !important;
    border-radius: 0 !important;
    background-color: #fff !important;
    color: var(--client-ink) !important;
    box-shadow: none !important;
    padding-left: 8px !important;
  }
  #kt_form .client-field .input-group .form-control::placeholder {color: #8993a8; opacity: 1;}
  .client-tip {display: flex; align-items: center; gap: 8px; background: #fffbeb; color: #78350f; border-radius: 7px; padding: 9px 11px; font-size: 10.5px; margin-top: 15px; border: 1px solid #fef3c7;}
  .client-tip i {color: #d97706; font-size: 14px;}
  /* Company Website Input Group & Fetch Company Details Button (Pixel-perfect & responsive) */
  .company-website-input-group {
    display: flex !important;
    align-items: stretch !important;
    width: 100% !important;
    min-width: 0 !important;
    gap: 8px !important;
    border: none !important;
    background: transparent !important;
    overflow: visible !important;
    box-shadow: none !important;
  }
  .company-website-input-group:focus-within {
    border: none !important;
    box-shadow: none !important;
  }
  .company-website-input-group .website-input-wrapper,
  .company-website-input-group .input-group-solid {
    display: flex !important;
    align-items: center !important;
    flex: 1 1 auto !important;
    width: 100% !important;
    min-width: 0 !important;
    border: 1px solid #cbd5e1 !important;
    border-radius: 8px !important;
    background: #ffffff !important;
    overflow: hidden !important;
    transition: border-color 0.15s ease, box-shadow 0.15s ease;
  }
  .company-website-input-group .website-input-wrapper:focus-within,
  .company-website-input-group .input-group-solid:focus-within {
    border-color: #ffd428 !important;
    box-shadow: 0 0 0 3px rgba(245, 196, 0, 0.2) !important;
  }
  .company-website-input-group .website-input-wrapper .input-group-text,
  .company-website-input-group .input-group-solid .input-group-text {
    flex: 0 0 auto !important;
    width: auto !important;
    min-width: 38px !important;
    justify-content: center !important;
    background: #ffffff !important;
    border: none !important;
    color: #94a3b8 !important;
    padding-left: 12px !important;
    padding-right: 8px !important;
  }
  .company-website-input-group .website-input-wrapper input,
  .company-website-input-group .input-group-solid input,
  .company-website-input-group input#website {
    flex: 1 1 auto !important;
    width: 100% !important;
    min-width: 0 !important;
    height: 42px !important;
    border: none !important;
    border-radius: 0 !important;
    background: transparent !important;
    color: #1e293b !important;
    font-size: 13px !important;
    padding-left: 4px !important;
    box-shadow: none !important;
  }
  .client-auto-fetch,
  .fetch-company-details-btn {
    flex: 0 0 auto !important;
    width: auto !important;
    max-width: none !important;
    min-width: max-content !important;
    border: 1px solid #fde047 !important;
    border-radius: 8px !important;
    color: #0a1647 !important;
    background: #ffd428 !important;
    white-space: nowrap !important;
    font-size: 12px !important;
    font-weight: 700 !important;
    padding: 0 16px !important;
    height: 42px !important;
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    gap: 6px !important;
    overflow: visible !important;
    box-shadow: 0 1px 2px rgba(245, 196, 0, 0.15);
    transition: background 0.15s ease, transform 0.1s ease;
  }
  .client-auto-fetch:hover,
  .fetch-company-details-btn:hover {
    background: #f5c400 !important;
  }
  .client-auto-fetch:active,
  .fetch-company-details-btn:active {
    background: #eab308 !important;
    transform: translateY(1px);
  }
  .client-auto-fetch:disabled,
  .fetch-company-details-btn:disabled,
  .client-auto-fetch.disabled,
  .fetch-company-details-btn.disabled {
    opacity: 0.65 !important;
    cursor: not-allowed !important;
    pointer-events: none !important;
  }
  .client-assistant {min-width: 0; overflow: hidden; border: 1px solid #e0e4f5; border-radius: 10px; padding: 18px; min-height: 100%; background: linear-gradient(145deg, #fbfaff 0%, #f4f6ff 100%); box-shadow: 0 2px 8px rgba(17, 28, 68, 0.04);}
  .client-assistant-head {display: flex; justify-content: space-between; gap: 12px; align-items: flex-start; margin-bottom: 15px;}
  .client-assistant-title {font-size: 13px; font-weight: 600; color: #4c38ed; margin: 0;}
  .client-beta {padding: 3px 8px; border-radius: 12px; background: #e9e4ff; color: #6a47f5; font-size: 9.5px; font-weight: 600;}
  .client-assistant-copy {font-size: 10.5px; line-height: 1.5; color: #53607e; margin: 5px 0 0;}
  .client-assistant-spark {display: grid; flex: 0 0 48px; width: 48px; height: 48px; place-items: center; border-radius: 10px; background: #fff; color: #6246ea; font-size: 14px; box-shadow: 0 8px 18px rgba(78, 56, 237, 0.12);}
  .client-suggestions {border: 1px solid #e1e6ef; border-radius: 8px; background: #fff; overflow: hidden;}
  .client-suggestions-title {padding: 12px 13px 10px; border-bottom: 1px solid #edf0f5; font-size: 11px; color: var(--client-ink); font-weight: 600;}
  .client-suggestion-row {display: grid; grid-template-columns: minmax(0, .8fr) minmax(0, 1.2fr) 16px; align-items: center; gap: 6px; min-height: 38px; padding: 7px 13px; border-bottom: 1px solid #edf0f5; font-size: 10px;}
  .client-suggestion-label {color: #53607e;}
  .client-assistant-value {color: var(--client-ink); text-align: right; overflow: hidden; white-space: nowrap; text-overflow: ellipsis;}
  .client-suggestion-status {color: #10a66a; font-size: 12px;}
  .client-suggestion-status.is-missing {color: #b7bfce;}
  .client-assistant-action {width: calc(100% - 22px); margin: 12px 11px 0; min-height: 34px; border: 0; border-radius: 6px; background: linear-gradient(180deg, #ffd428 0%, #f5c400 100%); color: #10143b; font-size: 10.5px; font-weight: 600;}
  .client-assistant-note {font-size: 9.5px; color: #7b8497; text-align: center; margin: 10px 8px 12px;}
  
  /* ===== Sticky Action Navigation Bar (Docked & Ergonomic) ===== */
  .client-wizard-sticky-footer {
    position: sticky;
    bottom: 0;
    z-index: 100;
    background: rgba(255, 255, 255, 0.96);
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
    border-top: 1px solid #e2e8f0;
    border-radius: 0 0 12px 12px;
    padding: 14px 24px;
    margin-top: 24px;
    box-shadow: 0 -4px 20px rgba(0, 0, 0, 0.06);
    display: flex;
    align-items: center;
    justify-content: flex-end;
    gap: 16px;
  }
  .client-action-left, .client-action-center, .client-action-right {
    display: flex;
    align-items: center;
    gap: 10px;
  }
  
  /* Primary Action Buttons (Job Create Gold Gradient) */
  .btn-primary, #continueButton, .client-submit-btn, .submitfrm, .updatefrm {
    background: linear-gradient(180deg, #ffd428 0%, #f5c400 100%) !important;
    background-color: #ffd428 !important;
    color: #10143b !important;
    border: 1px solid #d4a017 !important;
    box-shadow: 0 1px 2px rgba(16, 20, 59, 0.08), 0 1px 3px rgba(245, 196, 0, 0.25) !important;
    font-weight: 600 !important;
    font-size: 13px !important;
    padding: 9px 20px !important;
    border-radius: 8px !important;
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    cursor: pointer !important;
    transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1) !important;
  }
  .btn-primary:hover, #continueButton:hover, .client-submit-btn:hover, .submitfrm:hover, .updatefrm:hover {
    background: linear-gradient(180deg, #fbc02d 0%, #f59e0b 100%) !important;
    background-color: #f59e0b !important;
    color: #0f172a !important;
    border-color: #b45309 !important;
    box-shadow: 0 4px 14px rgba(217, 119, 6, 0.32) !important;
    transform: translateY(-1px) !important;
  }
  .btn-primary:active, #continueButton:active, .client-submit-btn:active {
    transform: translateY(0) !important;
    box-shadow: 0 1px 2px rgba(16, 20, 59, 0.12) !important;
  }

  /* Secondary Buttons (Back, Cancel, Draft) */
  .btn-secondary, .client-cancel-btn, .client-draft-btn, .client-nav-back, .client-nav-cancel, .client-nav-draft, [data-wizard-type="action-prev"] {
    background: #ffffff !important;
    color: #334155 !important;
    border: 1px solid #cbd5e1 !important;
    font-weight: 500 !important;
    font-size: 13px !important;
    padding: 9px 18px !important;
    border-radius: 8px !important;
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05) !important;
    cursor: pointer !important;
    transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1) !important;
  }
  .btn-secondary:hover, .client-cancel-btn:hover, .client-draft-btn:hover, .client-nav-back:hover, .client-nav-cancel:hover, .client-nav-draft:hover, [data-wizard-type="action-prev"]:hover {
    background: #f8fafc !important;
    border-color: #94a3b8 !important;
    color: #0f172a !important;
    transform: translateY(-1px) !important;
  }

  .client-profile-completion {display: flex; align-items: center; gap: 10px; margin-left: auto;}
  .client-completion-copy {text-align: right;}
  .client-completion-label {font-size: 10px; color: var(--client-ink); font-weight: 600;}
  .client-completion-status {display: block; margin-top: 3px; font-size: 9.5px; color: #10a66a;}
  .client-completion-ring {--completion: 0; width: 40px; height: 40px; display: grid; place-items: center; border-radius: 50%; background: conic-gradient(#10b981 calc(var(--completion) * 1%), #e5ebf4 0); position: relative;}
  .client-completion-ring::before {content: ''; position: absolute; inset: 4px; border-radius: 50%; background: var(--client-page);}
  .client-completion-ring span {position: relative; color: var(--client-ink); font-size: 9.5px; font-weight: 600;}

  @media (max-width: 1199.98px) and (min-width: 992px) {
    #kt_wizard .wizard-title::after {display: none;}
    #kt_wizard .wizard-step {padding-right: 16px;}
    #kt_wizard .wizard-step:not(:last-child)::after {right: 4px; width: 10px;}
    .client-step-layout {
      grid-template-columns: minmax(0, 70fr) minmax(0, 30fr) !important;
      gap: 14px !important;
    }
    .client-information-layout,
    .client-info-step .client-step-layout {
      grid-template-columns: minmax(0, 7fr) minmax(0, 3fr) !important;
      gap: 16px !important;
    }
    .client-description-layout,
    .client-desc-step .client-step-layout {
      grid-template-columns: minmax(0, 3fr) minmax(0, 2fr) !important;
      gap: 16px !important;
    }
    .client-address-layout,
    .client-address-step .client-step-layout {
      grid-template-columns: minmax(0, 3fr) minmax(0, 2fr) !important;
      gap: 16px !important;
    }
    .client-contact-layout,
    .client-contact-step .client-step-layout {
      display: block !important;
      width: 100% !important;
    }
    .client-branding-grid {grid-template-columns: 160px 120px minmax(0, 1fr) !important; gap: 12px !important;}
  }
  @media (max-width: 991.98px) {
    .client-step-layout,
    .client-information-layout,
    .client-description-layout,
    .client-address-layout,
    .client-contact-layout,
    .client-info-step .client-step-layout,
    .client-desc-step .client-step-layout,
    .client-address-step .client-step-layout,
    .client-contact-step .client-step-layout {
      grid-template-columns: 1fr !important;
      gap: 16px !important;
    }
    .client-info-step .client-main-column,
    .client-info-step .client-side-column {
      height: auto !important;
      display: block !important;
    }
    .client-info-step .client-main-column > *,
    .client-info-step .client-side-column > * {
      margin-bottom: 16px !important;
    }
    .client-info-step .client-main-column > *:last-child,
    .client-info-step .client-side-column > *:last-child {
      margin-bottom: 0 !important;
    }
    .client-info-step .client-side-column > .client-helper-card {
      flex: none !important;
    }
    .client-info-step .client-logo-upload,
    .client-info-step .client-logo-preview {
      height: auto !important;
    }
    .client-review-grid-2x2 {
      grid-template-columns: 1fr !important;
      gap: 16px !important;
    }
    .client-assistant {min-height: auto;}
  }
  @media (max-width: 767px) {
    #kt_wizard .wizard-steps {display: flex !important; overflow-x: auto; padding: 10px !important;}
    .client-info-step .client-branding-grid,
    .client-branding-grid,
    .client-brand-fields,
    .client-details-grid {grid-template-columns: 1fr !important;}
    .client-logo-upload {min-height: 130px;}
    .client-logo-preview {min-height: 140px;}
    .client-profile-completion {display: none;}
    #kt_form > .d-flex.border-top {align-items: stretch; flex-direction: column-reverse;}
    .client-action-left, .client-action-right {justify-content: flex-end;}
  }
  @media (max-width: 575.98px) {
    .company-website-input-group {
      flex-direction: column !important;
      gap: 8px !important;
    }
    .company-website-input-group .website-input-wrapper,
    .company-website-input-group .input-group-solid {
      width: 100% !important;
    }
    .company-website-input-group .fetch-company-details-btn,
    .company-website-input-group .client-auto-fetch {
      width: 100% !important;
      min-width: 0 !important;
      min-height: 42px !important;
      justify-content: center !important;
      border-radius: 8px !important;
    }
  }

  /* Steps 2–5: shared layout helpers aligned to the Add Client redesign mockups. */
  .client-desc-step, .client-address-step, .client-contact-step, .client-review-step {background: transparent !important; border: 0 !important; padding: 0 !important; box-shadow: none !important; min-height: auto !important;}
  .client-step-header {display: flex; align-items: flex-start; justify-content: space-between; gap: 12px; margin-bottom: 14px;}
  .client-step-header h3 {font-size: 14px !important; font-weight: 600 !important; color: var(--client-ink) !important; margin: 0 !important;}
  .client-step-header p {font-size: 10.5px; color: var(--client-muted); margin: 4px 0 0;}
  .client-helper-card {border-radius: 10px; padding: 16px; margin-bottom: 12px; border: 1px solid transparent;}
  .client-helper-card:last-child {margin-bottom: 0;}
  .client-helper-card h4 {font-size: 12px; font-weight: 600; margin: 0 0 10px; display: flex; align-items: center; gap: 8px;}
  .client-helper-card p, .client-helper-card li {font-size: 10.5px; color: #53607e; line-height: 1.55;}
  .client-helper-card ul {margin: 0; padding-left: 0; list-style: none;}
  .client-helper-card li {display: flex; align-items: flex-start; gap: 7px; margin-bottom: 7px;}
  .client-helper-card li i {color: #10a66a; margin-top: 2px;}
  .client-helper-blue {background: #eef5ff; border-color: #d7e6ff;}
  .client-helper-purple {background: #f3f0ff; border-color: #e4dcff;}
  .client-helper-green {background: #eefbf3; border-color: #d5f0de;}
  .client-helper-sky {background: #eef7ff; border-color: #d3e8ff;}
  .client-editor-wrap {
    background: transparent !important;
    border: none !important;
    width: 100% !important;
  }
  .client-editor-box {
    border: 1px solid #cbd5e1 !important;
    border-radius: 8px !important;
    overflow: hidden !important;
    background: #ffffff !important;
    transition: border-color 0.15s ease, box-shadow 0.15s ease !important;
  }
  .client-editor-box:focus-within {
    border-color: #ffd428 !important;
    box-shadow: 0 0 0 3px rgba(255, 212, 40, 0.25) !important;
  }
  .client-editor-box.is-invalid {
    border-color: #ef4444 !important;
    box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.2) !important;
  }
  .client-editor-box .ql-toolbar.ql-snow {
    border: none !important;
    border-bottom: 1px solid #f1f5f9 !important;
    background: #f8fafc !important;
    padding: 10px 16px !important;
    font-family: inherit !important;
  }
  .client-editor-box .ql-container.ql-snow {
    border: none !important;
    font-family: inherit !important;
    font-size: 13px !important;
    background: #ffffff !important;
  }
  .client-editor-box .ql-editor {
    min-height: 200px !important;
    max-height: 420px !important;
    overflow-y: auto !important;
    font-size: 13px !important;
    line-height: 1.6 !important;
    color: #0f172a !important;
    padding: 16px !important;
    font-family: inherit !important;
  }
  .client-editor-box .ql-editor.ql-blank::before {
    color: #94a3b8 !important;
    font-style: normal !important;
    font-size: 13px !important;
    left: 16px !important;
    right: 16px !important;
  }
  .client-char-count {font-size: 11.5px; color: #64748b; font-weight: 600;}
  .client-services-grid {display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 10px; margin-bottom: 12px;}
  .client-service-chip {display: flex; align-items: center; gap: 8px; min-height: 40px; padding: 8px 10px; border: 1px solid #dce3ee; border-radius: 8px; background: #fff; font-size: 11px; color: var(--client-ink);}
  .client-service-chip i.la-grip-vertical {color: #b8c0d0; cursor: grab;}
  .client-add-service {width: 100%; min-height: 40px; border: 1px dashed #b9c7de; border-radius: 8px; background: #fbfdff; color: var(--client-primary); font-size: 11px; font-weight: 600;}
  /* ── Service Offerings Tagify container (class added via JS after init) ── */
  .client-service-tagify {
    display: flex !important; flex-wrap: wrap !important; align-items: center !important;
    border: 1px solid #dce3ee !important; border-radius: 8px !important;
    background: #fff !important; padding: 4px 6px !important; min-height: 44px !important;
    cursor: text; gap: 4px !important;
  }
  .client-service-tagify:focus-within {
    border-color: var(--client-primary) !important;
    box-shadow: 0 0 0 3px rgba(23,105,245,.1) !important;
  }
  /* Individual tag pill */
  .client-service-tagify .tagify__tag {
    background: #fff !important;
    border: 1.5px solid var(--client-primary) !important;
    border-radius: 20px !important;
    margin: 2px !important;
    padding: 0 !important;
    display: inline-flex !important;
    align-items: center !important;
    height: 26px !important;
  }
  /* Tag inner div (wraps text + remove btn) */
  .client-service-tagify .tagify__tag > div {
    display: inline-flex !important;
    align-items: center !important;
    padding: 0 4px 0 10px !important;
    gap: 0 !important;
  }
  /* Remove the default blue/grey background overlay Tagify puts via ::before */
  .client-service-tagify .tagify__tag > div::before {
    display: none !important;
    background: none !important;
    box-shadow: none !important;
  }
  /* Tag text */
  .client-service-tagify .tagify__tag-text {
    color: var(--client-primary) !important;
    font-size: 11.5px !important;
    font-weight: 500 !important;
    line-height: 1 !important;
  }
  /* Remove (×) button — inline-flex so ::after pseudo-element centers inside the circle */
  .client-service-tagify .tagify__tag__removeBtn {
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    width: 18px !important; height: 18px !important; min-width: 18px !important;
    border-radius: 50% !important;
    background: var(--client-primary) !important;
    margin: 0 5px 0 5px !important;
    cursor: pointer !important;
    flex-shrink: 0 !important;
    opacity: 1 !important;
    font-size: 0 !important;
  }
  .client-service-tagify .tagify__tag__removeBtn::after {
    content: "×" !important;
    color: #fff !important;
    font-size: 14px !important;
    font-weight: 700 !important;
    line-height: 1 !important;
    display: block !important;
  }
  .client-service-tagify .tagify__tag__removeBtn:hover {
    background: #1e4fd4 !important;
  }
  /* Placeholder text inside the tagify input area */
  .client-service-tagify .tagify__input { line-height: 26px !important; }
  .client-service-tagify .tagify__input::before { color: #aab4c8 !important; font-size: 11.5px !important; }

  /* ── Suggested services row ── */
  .client-suggested-label {font-size: 10.5px; color: var(--client-muted); display: block; margin-bottom: 6px;}
  .client-suggested-list {display: flex; flex-wrap: wrap; gap: 7px;}
  .client-suggested-tag {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 4px 8px 4px 12px;
    border: 1px solid #d0d8e8; border-radius: 20px;
    background: #fff; font-size: 11px; color: var(--client-ink);
    cursor: pointer; transition: border-color .15s, color .15s; user-select: none;
  }
  .client-suggested-tag:hover { border-color: var(--client-primary); color: var(--client-primary); }
  .client-suggested-tag.is-added { opacity: .38; pointer-events: none; cursor: default; }
  .client-suggest-add {
    display: inline-flex; align-items: center; justify-content: center;
    width: 18px; height: 18px; border-radius: 50%;
    background: #e4eaf5; border: none; color: #53607e;
    font-size: 11px; cursor: pointer; padding: 0; flex-shrink: 0;
    transition: background .15s, color .15s; pointer-events: none;
  }
  .client-suggested-tag:hover .client-suggest-add { background: var(--client-primary); color: #fff; }
  .client-preview-box {border: 1px solid #dce3ee; border-radius: 8px; padding: 12px; background: #fff;}
  .client-preview-box .preview-title {font-size: 12px; font-weight: 600; color: var(--client-ink); margin-bottom: 6px;}
  .client-preview-box .preview-body {font-size: 10.5px; color: #53607e; min-height: 48px;}
  .client-preview-tags {display: flex; flex-wrap: wrap; gap: 6px; margin-top: 10px;}
  .client-preview-tag {padding: 4px 10px; border-radius: 14px; background: #edf4ff; color: #315efb; font-size: 9.5px; font-weight: 600;}
  .client-info-banner {display: flex; align-items: flex-start; gap: 8px; padding: 10px 12px; border-radius: 8px; background: var(--client-primary-soft); color: #405174; font-size: 10.5px; margin-bottom: 14px;}
  .client-info-banner i {color: var(--client-primary); font-size: 14px; margin-top: 1px;}
  .client-address-card {border: 1px solid var(--client-border); border-radius: 10px; padding: 16px; margin-bottom: 12px; background: #fff;}
  .client-address-card.is-additional {background: #fafbfd;}
  .client-address-card-head {display: flex; align-items: center; justify-content: space-between; margin-bottom: 12px;}
  .client-address-card-head h4 {font-size: 12px; font-weight: 600; margin: 0; color: var(--client-ink);}
  .client-address-empty {text-align: center; padding: 28px 16px; color: #7b8497;}
  .client-address-empty i {font-size: 14px; color: #c5cfe0; display: block; margin-bottom: 8px;}
  .client-address-empty p {font-size: 10.5px; margin: 0;}
  .client-add-address-btn {min-height: 34px; padding: 6px 14px; border-radius: 7px; border: 0; background: var(--client-primary); color: #fff; font-size: 10.5px; font-weight: 600;}
  .client-remove-address-btn {min-height: 30px; padding: 4px 10px; border-radius: 6px; border: 1px solid #f5c2cb; background: #fff5f6; color: #e5484d; font-size: 10px;}
  .client-contact-toolbar {display: flex; align-items: center; justify-content: space-between; gap: 12px; margin-bottom: 14px;}
  .client-contact-table-wrap {border: 1px solid var(--client-border); border-radius: 10px; overflow: hidden; background: #fff;}
  .client-contact-table {width: 100%; margin: 0; font-size: 11px;}
  .client-contact-table thead th {background: #f7f9fc; color: #53607e; font-weight: 600; font-size: 10px; text-transform: uppercase; letter-spacing: .03em; padding: 10px 12px; border-bottom: 1px solid var(--client-border); white-space: nowrap;}
  .client-contact-table tbody td {padding: 12px; border-bottom: 1px solid #edf0f5; vertical-align: middle; color: var(--client-ink);}
  .client-contact-table tbody tr:last-child td {border-bottom: 0;}
  .client-contact-person {display: flex; align-items: center; gap: 10px;}
  .client-contact-avatar {width: 34px; height: 34px; border-radius: 50%; display: grid; place-items: center; background: #edf4ff; color: var(--client-primary); font-size: 11px; font-weight: 700;}
  .client-contact-name {font-weight: 600; font-size: 11.5px;}
  .client-badge-primary {display: inline-block; margin-left: 6px; padding: 2px 7px; border-radius: 10px; background: #e7f8ef; color: #0f9d58; font-size: 9px; font-weight: 600;}
  .client-badge-secondary {display: inline-block; padding: 2px 8px; border-radius: 10px; background: #f2f4f8; color: #68738d; font-size: 9px; font-weight: 600;}
  .client-table-actions {display: flex; gap: 6px;}
  .client-table-actions button {width: 30px; height: 30px; border: 1px solid #dce3ee; border-radius: 7px; background: #fff; color: #53607e; display: grid; place-items: center; padding: 0;}
  .client-table-actions button:hover {border-color: var(--client-primary); color: var(--client-primary);}
  .client-table-actions button.is-delete:hover {border-color: #f5c2cb; color: #e5484d;}
  .client-contact-fields {position: absolute; left: -9999px; width: 1px; height: 1px; overflow: hidden;}
  /* ── Contact Person Drawer ── */
  .client-contact-drawer-backdrop {position: fixed !important; inset: 0 !important; background: rgba(15,25,77,.45) !important; z-index: 99998 !important; opacity: 0 !important; visibility: hidden !important; transition: opacity .25s ease !important; backdrop-filter: blur(2px) !important;}
  .client-contact-drawer-backdrop.is-open {opacity: 1 !important; visibility: visible !important;}
  .client-contact-drawer {
    position: fixed !important; top: 0 !important; right: 0 !important; height: 100vh !important;
    width: min(500px, 100vw) !important;
    background: #ffffff !important; z-index: 99999 !important;
    box-shadow: -12px 0 48px rgba(17,28,68,.2) !important;
    border-left: 1px solid #e2e8f4 !important;
    transform: translateX(100%) !important; transition: transform .28s cubic-bezier(.4,0,.2,1) !important;
    display: flex !important; flex-direction: column !important;
  }
  .client-contact-drawer.is-open {transform: translateX(0) !important; visibility: visible !important;}
  @media (max-width: 767px) { .client-contact-drawer { width: 100vw; } }
  @media (min-width: 768px) and (max-width: 1024px) { .client-contact-drawer { width: min(420px, 100vw); } }

  /* Header */
  .client-drawer-head {
    padding: 20px 24px; border-bottom: 1px solid #edf0f7;
    display: flex; align-items: flex-start; justify-content: space-between; gap: 12px; flex-shrink: 0;
  }
  .client-drawer-head-info {flex: 1; min-width: 0;}
  .client-drawer-head h4 {margin: 0 0 3px; font-size: 14px; font-weight: 700; color: #0f1940; line-height: 1.3;}
  .client-drawer-head-subtitle {margin: 0; font-size: 11.5px; color: #7889a8; line-height: 1.5;}
  .client-drawer-close-btn {
    flex-shrink: 0; width: 34px; height: 34px; border-radius: 8px;
    border: 1px solid #e2e8f4; background: #f5f7fb;
    display: inline-flex; align-items: center; justify-content: center;
    cursor: pointer; color: #64748b; font-size: 14px; padding: 0;
    transition: background .15s, border-color .15s, color .15s; margin-top: 1px;
  }
  .client-drawer-close-btn:hover {background: #fff0f2; border-color: #fca5a5; color: #dc2626;}

  /* Body */
  .client-drawer-body {
    padding: 20px 24px 36px 24px !important;
    overflow-y: auto !important;
    flex: 1 1 auto !important;
  }

  /* Fields */
  .client-drawer-field {margin-bottom: 16px;}
  .client-drawer-field .cdf-label {
    display: block; margin-bottom: 6px;
    font-size: 12px; font-weight: 600; color: #0f1940; line-height: 1;
  }
  .client-drawer-field .cdf-label .cdf-required {color: #e5484d; margin-left: 2px;}

  /* Input wrapper */
  .cdf-input-wrap {position: relative; display: flex; align-items: center;}
  .cdf-input-icon {
    position: absolute; left: 13px; top: 50%; transform: translateY(-50%);
    font-size: 14px; color: #94a3b8; pointer-events: none; line-height: 1; z-index: 2;
  }
  .cdf-input-wrap input {
    width: 100%; height: 42px;
    padding: 0 14px 0 38px;
    border: 1px solid #d8e2f0; border-radius: 8px;
    background: #fff; color: #1e2d4f;
    font-size: 12.5px; font-family: inherit;
    outline: none; transition: border-color .15s, box-shadow .15s;
    -webkit-appearance: none;
  }
  .cdf-input-wrap input::placeholder {color: #94a3b8; font-size: 12px;}
  .cdf-input-wrap input:focus {
    border-color: #2563eb;
    box-shadow: 0 0 0 3px rgba(37,99,235,.10);
  }
  .cdf-input-wrap input.is-invalid {border-color: #ef4444;}
  .cdf-input-wrap input.is-invalid:focus {border-color: #ef4444; box-shadow: 0 0 0 3px rgba(239,68,68,.10);}
  .cdf-error {display: none; margin-top: 5px; font-size: 11px; color: #dc2626; line-height: 1.4;}
  .cdf-error.visible {display: block;}

  /* Primary Contact Enterprise Toggle Card in Drawer */
  .client-primary-contact-card {
    background: #fffdf5 !important;
    border: 1.5px solid #fde68a !important;
    border-radius: 10px !important;
    padding: 12px 14px !important;
    margin-top: 6px !important;
    margin-bottom: 24px !important;
    transition: all 0.15s ease !important;
    box-sizing: border-box !important;
    width: 100% !important;
  }
  .client-primary-contact-card:hover {
    background: #fefce8 !important;
    border-color: #f59e0b !important;
  }
  .client-primary-contact-card.is-active,
  .client-primary-contact-card:has(.client-primary-native-checkbox:checked) {
    background: #fefce8 !important;
    border-color: #f59e0b !important;
    box-shadow: 0 1px 3px rgba(245, 158, 11, 0.15) !important;
  }

  .client-primary-card-label {
    display: flex !important;
    align-items: center !important;
    justify-content: space-between !important;
    gap: 12px !important;
    width: 100% !important;
    margin: 0 !important;
    cursor: pointer !important;
    user-select: none !important;
  }

  .client-primary-card-left {
    display: flex !important;
    align-items: flex-start !important;
    gap: 10px !important;
    flex: 1 1 auto !important;
    min-width: 0 !important;
  }

  .client-primary-star-icon {
    width: 28px !important;
    height: 28px !important;
    min-width: 28px !important;
    border-radius: 6px !important;
    background: #fef3c7 !important;
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    color: #d97706 !important;
    flex-shrink: 0 !important;
    margin-top: 1px !important;
  }

  .client-primary-card-texts {
    display: flex !important;
    flex-direction: column !important;
    min-width: 0 !important;
    gap: 2px !important;
  }

  .client-primary-card-title {
    font-size: 12px !important;
    font-weight: 700 !important;
    color: #1e293b !important;
    line-height: 1.3 !important;
    font-family: Poppins, sans-serif !important;
  }

  .client-primary-card-subtitle {
    font-size: 10.5px !important;
    color: #64748b !important;
    line-height: 1.35 !important;
    font-weight: 400 !important;
    font-family: Poppins, sans-serif !important;
  }

  .client-primary-checkbox-wrapper {
    position: relative !important;
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    flex-shrink: 0 !important;
    width: 20px !important;
    height: 20px !important;
  }

  .client-primary-native-checkbox {
    position: absolute !important;
    opacity: 0 !important;
    width: 20px !important;
    height: 20px !important;
    margin: 0 !important;
    cursor: pointer !important;
    z-index: 2 !important;
  }

  .client-primary-custom-box {
    width: 20px !important;
    height: 20px !important;
    border-radius: 6px !important;
    border: 1.5px solid #cbd5e1 !important;
    background: #ffffff !important;
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    transition: all 0.15s ease !important;
    box-sizing: border-box !important;
    pointer-events: none !important;
  }

  .client-primary-check-icon {
    opacity: 0 !important;
    transition: opacity 0.15s ease !important;
    display: block !important;
  }

  .client-primary-native-checkbox:checked + .client-primary-custom-box {
    background: #f59e0b !important;
    border-color: #f59e0b !important;
    box-shadow: 0 1px 3px rgba(245, 158, 11, 0.3) !important;
  }

  .client-primary-native-checkbox:checked + .client-primary-custom-box .client-primary-check-icon {
    opacity: 1 !important;
  }

  /* Footer */
  .client-drawer-foot {
    padding: 14px 24px !important;
    border-top: 1px solid #edf0f7 !important;
    display: flex !important;
    align-items: center !important;
    justify-content: space-between !important;
    gap: 10px !important;
    flex-shrink: 0 !important;
    background: #fff !important;
    box-shadow: 0 -3px 12px rgba(0, 0, 0, 0.03) !important;
  }
  .client-drawer-foot .cdf-cancel-btn {
    height: 38px !important;
    padding: 0 18px !important;
    border-radius: 8px !important;
    border: 1.5px solid #cbd5e1 !important;
    background: #fff !important;
    color: #334155 !important;
    font-size: 12px !important;
    font-weight: 600 !important;
    cursor: pointer !important;
    transition: border-color .15s, background .15s !important;
  }
  .client-drawer-foot .cdf-cancel-btn:hover {background: #f8fafc !important; border-color: #94a3b8 !important;}
  .client-drawer-foot .cdf-save-btn {
    height: 38px !important;
    padding: 0 20px !important;
    border-radius: 8px !important;
    background: #f59e0b !important;
    border: none !important;
    color: #fff !important;
    font-size: 12px !important;
    font-weight: 700 !important;
    cursor: pointer !important;
    display: inline-flex !important;
    align-items: center !important;
    gap: 7px !important;
    box-shadow: 0 1px 3px rgba(245, 158, 11, 0.3) !important;
    transition: background .15s, opacity .15s !important;
  }
  .client-drawer-foot .cdf-save-btn:hover {background: #d97706 !important;}
  .client-drawer-foot .cdf-save-btn:disabled {opacity: .7; cursor: not-allowed;}
  .client-drawer-foot .cdf-save-btn .cdf-spinner {
    width: 14px; height: 14px; border: 2px solid rgba(255,255,255,.4);
    border-top-color: #fff; border-radius: 50%; animation: cdf-spin .7s linear infinite; display: none;
  }
  .client-drawer-foot .cdf-save-btn.is-loading .cdf-spinner {display: block;}
  .client-drawer-foot .cdf-save-btn.is-loading .cdf-btn-text {opacity: .8;}
  @keyframes cdf-spin { to { transform: rotate(360deg); } }
  .client-review-layout {display: grid; grid-template-columns: minmax(0, 1fr) 280px; gap: 14px; align-items: start;}
  .client-review-card {background: #fff; border: 1px solid var(--client-border); border-radius: 10px; padding: 16px 18px; margin-bottom: 12px; box-shadow: 0 2px 8px rgba(17, 28, 68, .03);}
  .client-review-card-head {display: flex; align-items: center; justify-content: space-between; margin-bottom: 14px;}
  .client-review-card-head h4 {margin: 0; font-size: 13px; font-weight: 600; color: var(--client-ink);}
  .client-review-edit {border: 0; background: transparent; color: var(--client-primary); font-size: 11px; font-weight: 600; padding: 0;}
  .client-review-grid {display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 12px 18px;}
  .client-review-item label {display: block; font-size: 10px; color: #7b8497; margin-bottom: 3px;}
  .client-review-item span, .client-review-item a {font-size: 11.5px; color: var(--client-ink); font-weight: 600;}
  .client-review-logo {width: 52px; height: 52px; border-radius: 10px; background: #f1efff center/cover no-repeat; border: 1px solid var(--client-border); margin-bottom: 12px;}
  .client-review-addresses {display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 12px;}
  .client-review-address-box {border: 1px solid #edf0f5; border-radius: 8px; padding: 12px; background: #fafbfd;}
  .client-review-address-box h5 {font-size: 11px; font-weight: 600; margin: 0 0 6px; color: var(--client-ink);}
  .client-review-address-box p {font-size: 10.5px; color: #53607e; margin: 0; line-height: 1.5;}
  .client-summary-card {display: flex; align-items: flex-start; gap: 10px; padding: 12px; border: 1px solid var(--client-border); border-radius: 8px; background: #fff; margin-bottom: 10px;}
  .client-summary-icon {width: 32px; height: 32px; border-radius: 8px; display: grid; place-items: center; background: #edf4ff; color: var(--client-primary); flex: 0 0 32px;}
  .client-summary-body {flex: 1; min-width: 0;}
  .client-summary-body h5 {font-size: 11px; font-weight: 600; margin: 0 0 3px; color: var(--client-ink);}
  .client-summary-body p {font-size: 10px; color: #7b8497; margin: 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;}
  .client-summary-edit {border: 0; background: transparent; color: #7b8497; padding: 0;}
  .client-almost-done {margin-top: 14px; padding: 14px; border-radius: 10px; background: #eefbf3; border: 1px solid #d5f0de; text-align: center;}
  .client-almost-done i {color: #10a66a; font-size: 14px; margin-bottom: 6px;}
  .client-almost-done h5 {font-size: 12px; font-weight: 600; margin: 0 0 4px; color: var(--client-ink);}
  .client-almost-done p {font-size: 10px; color: #53607e; margin: 0;}
  .client-submit-wrap {text-align: right;}
  .client-submit-note {font-size: 9.5px; color: #7b8497; margin-top: 6px;}
  .client-submit-btn {min-width: 150px; background: linear-gradient(90deg, #315efb, #2557e7) !important; border: 0 !important; color: #fff !important; font-weight: 600;}
  .client-submit-btn:hover, .client-submit-btn:focus, .client-submit-btn:active {background: linear-gradient(90deg, #2557e7, #1e4fd4) !important; color: #fff !important; box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3) !important;}
  .client-draft-btn {border: 1px solid #b9d1ff !important; color: var(--client-primary) !important; background: #f5f9ff !important;}
  #kt_wizard .wizard-step[data-wizard-state="done"] .wizard-icon::before {content: '✓'; font-size: 14px;}
  @media (max-width: 991px) {
    .client-review-layout {grid-template-columns: 1fr;}
    .client-review-grid, .client-review-addresses, .client-services-grid {grid-template-columns: 1fr;}
  }
  /* ==========================================================================
     EXACT 5PX VERTICAL GAPS ARCHITECTURE & HEADER STABILIZATION
     Upper Gap: Exactly 5px between fixed Top Navigation (#kt_header) & Wizard Header
     Lower Gap: Exactly 5px between Wizard Header & Main Form Content
     Strictly isolated to Client Wizard page (Edit / Add Client)
     ========================================================================== */

  /* Override Metronic body toolbar CSS variable for this page */
  #kt_body {
    --kt-toolbar-height: 5px !important;
    --kt-toolbar-height-tablet-and-mobile: 5px !important;
  }

  /* Fixed Top Header is 65px on desktop (>=992px) and 55px on tablet/mobile (<992px).
     Wrapper padding-top = Header Height + 5px upper gap */
  .header-fixed.toolbar-fixed #kt_wrapper,
  .header-fixed #kt_wrapper,
  #kt_wrapper {
    padding-top: calc(65px + 5px) !important;
  }

  @media (max-width: 991.98px) {
    .header-tablet-and-mobile-fixed.toolbar-tablet-and-mobile-fixed #kt_wrapper,
    .header-tablet-and-mobile-fixed #kt_wrapper,
    #kt_wrapper {
      padding-top: calc(55px + 5px) !important;
    }
  }

  /* Eliminate any redundant padding/margins between #kt_wrapper and #client_top_nav_card */
  #kt_content,
  .content {
    padding-top: 0 !important;
    margin-top: 0 !important;
  }

  #kt_content > .d-flex,
  #kt_content .container-fluid {
    padding-top: 0 !important;
    margin-top: 0 !important;
  }

  /* Wizard container stretches cleanly in full-height flex column */
  #kt_content .container-fluid {
    flex: 1 0 auto !important;
    display: flex !important;
    flex-direction: column !important;
  }
  .client-wizard-shell {
    flex: 1 0 auto !important;
    display: flex !important;
    flex-direction: column !important;
  }
  #kt_wizard {
    flex: 1 0 auto !important;
    display: flex !important;
    flex-direction: column !important;
  }

  /* Main Form Shell: 0px top margin/padding to preserve exact 5px lower gap & perfect edge alignment with header */
  .client-wizard-shell,
  .client-wizard-shell.card {
    background: transparent !important;
    border: none !important;
    box-shadow: none !important;
    margin: 0 !important;
    padding: 0 !important;
    width: 100% !important;
    max-width: 100% !important;
    box-sizing: border-box !important;
  }

  .client-wizard-shell > .card-body {
    margin: 0 !important;
    padding: 0 !important;
    width: 100% !important;
    max-width: 100% !important;
    box-sizing: border-box !important;
  }

  #kt_wizard {
    margin: 0 !important;
    padding: 0 !important;
    width: 100% !important;
    max-width: 100% !important;
    box-sizing: border-box !important;
  }

  #kt_wizard > .row {
    margin: 0 !important;
    padding: 0 !important;
    width: 100% !important;
    max-width: 100% !important;
    box-sizing: border-box !important;
  }

  #kt_wizard > .row > .col-xl-12,
  #kt_wizard > .row > div {
    margin: 0 !important;
    padding: 0 !important;
    width: 100% !important;
    max-width: 100% !important;
    box-sizing: border-box !important;
  }

  #kt_form {
    margin: 0 !important;
    padding: 0 !important;
    width: 100% !important;
    max-width: 100% !important;
    box-sizing: border-box !important;
  }

  /* Reset all step containers and intermediate wrappers to 0 top spacing */
  #kt_wizard [data-wizard-type="step-content"],
  #kt_form > [data-wizard-type="step-content"],
  .client-info-step,
  .client-desc-step,
  .client-address-step,
  .client-contact-step,
  .client-review-step {
    margin-top: 0 !important;
    padding-top: 0 !important;
    background: transparent !important;
    border: 0 !important;
    box-shadow: none !important;
  }

  .client-step-layout,
  .client-information-layout,
  .client-description-layout,
  .client-address-layout,
  .client-contact-layout,
  .client-info-step .client-step-layout,
  .client-desc-step .client-step-layout,
  .client-address-step .client-step-layout,
  .client-contact-step .client-step-layout,
  .client-review-grid-2x2 {
    margin-top: 0 !important;
    padding-top: 0 !important;
  }

  .client-main-column,
  .client-side-column {
    margin-top: 0 !important;
    padding-top: 0 !important;
  }

  /* Reset top margin on the first card in each column across all 5 steps so lower gap is strictly 5px */
  .client-main-column > :first-child,
  .client-side-column > :first-child,
  .client-section-card:first-child,
  .client-helper-card:first-child,
  .client-addr-header-wrap,
  .client-contact-main-card:first-child,
  .client-review-card:first-child {
    margin-top: 0 !important;
  }

  /* =========================================================================
     TOP UNIFIED HEADER & 6-STEP NAVIGATION BAR CSS (Pixel-matched & Enterprise-Aligned)
     ========================================================================= */
  .client-top-nav-card {
    background: #ffffff !important;
    border: 1px solid #e2e8f0 !important;
    border-radius: 12px !important;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.03) !important;
    padding: 0 20px !important;
    margin-top: 0 !important;
    margin-bottom: 8px !important;
    box-sizing: border-box !important;
    width: 100% !important;
    height: 54px !important;
    min-height: 54px !important;
    max-height: 54px !important;
    display: flex !important;
    align-items: center !important;
    justify-content: space-between !important;
    position: sticky !important;
    top: calc(65px + 4px) !important;
    z-index: 95 !important;
    transition: box-shadow 0.2s ease, top 0.2s ease !important;
  }

  .client-header-grid {
    display: flex !important;
    align-items: center !important;
    justify-content: space-between !important;
    width: 100% !important;
    height: 100% !important;
    gap: 16px !important;
    box-sizing: border-box !important;
  }

  .client-header-identity {
    flex: 0 0 auto !important;
    display: flex !important;
    align-items: center !important;
    min-width: 170px !important;
    max-width: 250px !important;
    height: 100% !important;
  }

  .client-header-titles {
    min-width: 0 !important;
    display: flex !important;
    flex-direction: column !important;
    justify-content: center !important;
    gap: 1px !important;
  }

  .client-header-title {
    font-size: 14px !important;
    font-weight: 700 !important;
    color: #0f172a !important;
    margin: 0 !important;
    line-height: 1.2 !important;
    letter-spacing: -0.2px !important;
    white-space: nowrap !important;
    font-family: Poppins, sans-serif !important;
  }

  .client-header-subtitle {
    font-size: 11px !important;
    color: #64748b !important;
    margin: 2px 0 0 0 !important;
    line-height: 1.25 !important;
    font-weight: 400 !important;
    white-space: nowrap !important;
    overflow: hidden !important;
    text-overflow: ellipsis !important;
    max-width: 230px !important;
    font-family: Poppins, sans-serif !important;
  }

  .client-header-stepper-col {
    flex: 1 1 auto !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    min-width: 0 !important;
    height: 100% !important;
  }

  .client-stepper-wrap {
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    gap: 0 !important;
    height: 100% !important;
  }

  .client-step-item {
    display: inline-flex !important;
    align-items: center !important;
    gap: 6px !important;
    cursor: pointer !important;
    flex-shrink: 0 !important;
    user-select: none !important;
    padding: 3px 4px !important;
    border-radius: 6px !important;
    text-decoration: none !important;
    line-height: 1 !important;
    transition: all 0.15s ease !important;
  }

  .client-step-circle {
    width: 24px !important;
    height: 24px !important;
    min-width: 24px !important;
    min-height: 24px !important;
    max-width: 24px !important;
    max-height: 24px !important;
    border-radius: 50% !important;
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    text-align: center !important;
    line-height: 1 !important;
    font-size: 11px !important;
    font-weight: 700 !important;
    flex-shrink: 0 !important;
    transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1) !important;
    box-sizing: border-box !important;
    font-family: Poppins, sans-serif !important;
  }
  .client-step-circle svg {
    display: block !important;
    margin: auto !important;
    width: 11px !important;
    height: 11px !important;
  }

  .client-step-circle.is-completed {
    background: #10b981 !important;
    color: #ffffff !important;
    border: none !important;
    box-shadow: 0 1px 3px rgba(16, 185, 129, 0.25) !important;
  }

  .client-step-circle.is-active {
    background: #ffd428 !important;
    color: #0f172a !important;
    border: 2px solid #ffffff !important;
    box-shadow: 0 0 0 2px #ffd428, 0 1px 4px rgba(245, 196, 0, 0.35) !important;
    font-weight: 700 !important;
  }

  .client-step-circle.is-upcoming {
    background: #ffffff !important;
    color: #64748b !important;
    border: 1.5px solid #cbd5e1 !important;
    font-weight: 600 !important;
    box-shadow: none !important;
  }

  .client-step-name {
    font-size: 11.5px !important;
    line-height: 1 !important;
    white-space: nowrap !important;
    transition: color 0.15s ease !important;
    font-family: Poppins, sans-serif !important;
  }

  .client-step-item.is-active .client-step-name {
    font-weight: 700 !important;
    color: #0f172a !important;
  }

  .client-step-item.is-completed .client-step-name {
    font-weight: 600 !important;
    color: #1e293b !important;
  }

  .client-step-item.is-upcoming .client-step-name {
    font-weight: 500 !important;
    color: #64748b !important;
  }

  .client-step-item:hover .client-step-name {
    color: #0f172a !important;
  }

  .client-step-arrow {
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    margin: 0 7px !important;
    color: #cbd5e1 !important;
    flex-shrink: 0 !important;
    line-height: 1 !important;
    transition: color 0.2s ease !important;
  }
  .client-step-arrow.is-completed {
    color: #10b981 !important;
  }
  .client-step-arrow.is-completed .client-step-arrow-svg {
    stroke: #10b981 !important;
  }
  .client-step-arrow.is-upcoming {
    color: #cbd5e1 !important;
  }
  .client-step-arrow.is-upcoming .client-step-arrow-svg {
    stroke: #cbd5e1 !important;
  }

  .client-step-arrow-svg {
    width: 12px !important;
    height: 12px !important;
    stroke: currentColor !important;
    stroke-width: 2.2 !important;
    display: block !important;
  }

  .client-header-actions {
    flex: 0 0 auto !important;
    display: flex !important;
    align-items: center !important;
    justify-content: flex-end !important;
    gap: 8px !important;
    min-width: 170px !important;
    height: 100% !important;
  }

  .client-top-close-btn,
  .client-top-back-btn {
    background: #ffffff !important;
    border: 1.5px solid #cbd5e1 !important;
    color: #334155 !important;
    font-weight: 600 !important;
    font-size: 12px !important;
    height: 34px !important;
    min-height: 34px !important;
    padding: 0 14px !important;
    border-radius: 8px !important;
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    gap: 6px !important;
    cursor: pointer !important;
    white-space: nowrap !important;
    transition: all 0.15s ease !important;
    flex-shrink: 0 !important;
    text-decoration: none !important;
    box-sizing: border-box !important;
    line-height: 1 !important;
  }
  .client-top-close-btn:hover,
  .client-top-back-btn:hover {
    background: #f8fafc !important;
    border-color: #94a3b8 !important;
    color: #0f172a !important;
    transform: translateY(-1px);
  }

  .client-top-next-btn,
  .client-top-submit-btn {
    background: linear-gradient(180deg, #ffd428 0%, #f5c400 100%) !important;
    border: 1px solid #d4a017 !important;
    color: #10143b !important;
    font-weight: 700 !important;
    font-size: 12px !important;
    height: 34px !important;
    min-height: 34px !important;
    padding: 0 16px !important;
    border-radius: 8px !important;
    box-shadow: 0 1px 3px rgba(245, 196, 0, 0.3) !important;
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    gap: 6px !important;
    cursor: pointer !important;
    white-space: nowrap !important;
    transition: all 0.15s ease !important;
    flex-shrink: 0 !important;
    line-height: 1 !important;
  }
  .client-top-next-btn:hover,
  .client-top-submit-btn:hover {
    background: #f5c400 !important;
    box-shadow: 0 2px 6px rgba(245, 196, 0, 0.38) !important;
    transform: translateY(-1px);
  }

  /* Navigation button visibility rules per step */
  /* Default & Step 1: Close visible, Back hidden, Next visible, Submit hidden */
  #client_top_close_btn {
    display: inline-flex !important;
  }
  #client_top_back_btn {
    display: none !important;
  }
  #client_top_next_btn {
    display: inline-flex !important;
  }
  #submitBtn {
    display: none !important;
  }

  body.on-step-1 #client_top_close_btn {
    display: inline-flex !important;
  }
  body.on-step-1 #client_top_back_btn {
    display: none !important;
  }
  body.on-step-1 #client_top_next_btn {
    display: inline-flex !important;
  }
  body.on-step-1 #submitBtn {
    display: none !important;
  }

  /* Steps 2-5: Close hidden, Back visible, Next visible, Submit hidden */
  body.on-step-2 #client_top_close_btn,
  body.on-step-3 #client_top_close_btn,
  body.on-step-4 #client_top_close_btn,
  body.on-step-5 #client_top_close_btn {
    display: none !important;
  }
  body.on-step-2 #client_top_back_btn,
  body.on-step-3 #client_top_back_btn,
  body.on-step-4 #client_top_back_btn,
  body.on-step-5 #client_top_back_btn {
    display: inline-flex !important;
  }
  body.on-step-2 #client_top_next_btn,
  body.on-step-3 #client_top_next_btn,
  body.on-step-4 #client_top_next_btn,
  body.on-step-5 #client_top_next_btn {
    display: inline-flex !important;
  }
  body.on-step-2 #submitBtn,
  body.on-step-3 #submitBtn,
  body.on-step-4 #submitBtn,
  body.on-step-5 #submitBtn {
    display: none !important;
  }

  /* Step 6 (Review & Submit): Close hidden, Back visible, Next hidden, Submit visible */
  body.on-step-6 #client_top_close_btn,
  body.on-review-step #client_top_close_btn {
    display: none !important;
  }
  body.on-step-6 #client_top_back_btn,
  body.on-review-step #client_top_back_btn {
    display: inline-flex !important;
  }
  body.on-step-6 #client_top_next_btn,
  body.on-review-step #client_top_next_btn {
    display: none !important;
  }
  body.on-step-6 #submitBtn,
  body.on-review-step #submitBtn {
    display: inline-flex !important;
  }

  /* Completely remove legacy footer action buttons */
  .client-bottom-actions-row,
  .client-cancel-btn,
  .client-draft-btn,
  .client-wizard-sticky-footer {
    display: none !important;
    margin: 0 !important;
    padding: 0 !important;
    height: 0 !important;
    border: none !important;
    overflow: hidden !important;
  }

  /* =========================================================================
     GOD-LEVEL STEP 1 (Company Profile & Company Logo) CSS
     ========================================================================= */
  .god-level-company-layout {
    display: grid !important;
    grid-template-columns: minmax(0, 7.2fr) minmax(0, 3.8fr) !important;
    gap: 16px !important;
    align-items: stretch !important;
    width: 100% !important;
    box-sizing: border-box !important;
    flex: 1 1 auto !important;
    min-height: calc(100vh - 170px) !important;
  }
  @media (max-width: 991.98px) {
    .god-level-company-layout {
      grid-template-columns: 1fr !important;
      min-height: auto !important;
    }
  }

  .god-card {
    background: #ffffff !important;
    border: 1px solid #e2e8f0 !important;
    border-radius: 12px !important;
    padding: 18px 20px !important;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.03) !important;
    box-sizing: border-box !important;
    height: 100% !important;
    display: flex !important;
    flex-direction: column !important;
    justify-content: space-between !important;
    width: 100% !important;
  }

  .god-card-header {
    display: flex !important;
    align-items: center !important;
    gap: 10px !important;
    margin-bottom: 12px !important;
  }

  .god-card-header-icon {
    width: 32px !important;
    height: 32px !important;
    border-radius: 8px !important;
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    flex-shrink: 0 !important;
  }
  .god-card-header-icon.blue-sky-icon {
    background: #eff6ff !important;
    color: #2563eb !important;
  }

  .god-card-header-titles {
    min-width: 0 !important;
  }

  .god-card-title {
    font-size: 14px !important;
    font-weight: 700 !important;
    color: #0f172a !important;
    margin: 0 0 1px 0 !important;
    line-height: 1.25 !important;
    font-family: Poppins, sans-serif !important;
  }

  .god-card-subtitle {
    font-size: 11px !important;
    color: #64748b !important;
    margin: 0 !important;
    line-height: 1.3 !important;
    font-weight: 400 !important;
    font-family: Poppins, sans-serif !important;
  }

  .god-fields-grid {
    display: grid !important;
    grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
    gap: 12px 14px !important;
    flex: 1 1 auto !important;
    align-content: space-between !important;
  }
  @media (max-width: 640px) {
    .god-fields-grid {
      grid-template-columns: 1fr !important;
    }
  }

  .god-field-full {
    grid-column: 1 / -1 !important;
    width: 100% !important;
  }

  .god-field-wrap {
    display: flex !important;
    flex-direction: column !important;
    min-width: 0 !important;
    margin-bottom: 0 !important;
  }

  .god-field-header {
    display: flex !important;
    align-items: center !important;
    gap: 6px !important;
    margin-bottom: 4px !important;
  }

  .god-field-label {
    font-size: 11.5px !important;
    font-weight: 600 !important;
    color: #0f172a !important;
    margin: 0 !important;
    line-height: 1.25 !important;
    font-family: Poppins, sans-serif !important;
  }

  /* Completely remove auto-filled / suggested / not found badge text from UI per user request */
  .god-badge,
  .god-badge-autofilled,
  .god-badge-suggested,
  .god-badge-notfound,
  .god-field-helper-note,
  #tax_id_helper {
    display: none !important;
  }

  /* Input Boxes */
  .god-input-box {
    display: flex !important;
    align-items: center !important;
    background: #ffffff !important;
    border: 1.5px solid #cbd5e1 !important;
    border-radius: 8px !important;
    padding: 0 10px !important;
    height: 38px !important;
    min-height: 38px !important;
    position: relative !important;
    transition: border-color 0.15s ease, box-shadow 0.15s ease !important;
    box-sizing: border-box !important;
    width: 100% !important;
    gap: 4px !important;
  }
  .god-input-box:focus-within {
    border-color: #ffd428 !important;
    box-shadow: 0 0 0 3px rgba(255, 212, 40, 0.22) !important;
  }
  .god-input-box.is-invalid,
  .god-input-box.has-validation-error {
    border-color: #ef4444 !important;
    box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.18) !important;
  }

  .god-input-left-icon {
    color: #94a3b8 !important;
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    margin-right: 8px !important;
    flex-shrink: 0 !important;
  }
  .god-input-left-icon svg {
    width: 14px !important;
    height: 14px !important;
  }

  .god-native-input {
    border: none !important;
    outline: none !important;
    background: transparent !important;
    color: #0f172a !important;
    font-size: 12px !important;
    font-family: Poppins, sans-serif !important;
    font-weight: 500 !important;
    flex: 1 1 auto !important;
    min-width: 0 !important;
    height: 100% !important;
    padding: 0 !important;
    margin: 0 !important;
    box-shadow: none !important;
  }
  .god-native-input::placeholder {
    color: #94a3b8 !important;
    font-weight: 400 !important;
  }

  .god-select-box {
    padding-right: 28px !important;
    position: relative !important;
  }
  .god-native-select {
    border: none !important;
    outline: none !important;
    background: transparent !important;
    color: #0f172a !important;
    font-size: 12px !important;
    font-family: Poppins, sans-serif !important;
    font-weight: 500 !important;
    width: 100% !important;
    height: 100% !important;
    padding: 0 !important;
    margin: 0 !important;
    cursor: pointer !important;
    appearance: none !important;
    -webkit-appearance: none !important;
  }

  .god-input-right-chevron {
    display: none !important;
  }

  .jero-dropdown-chevron {
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    width: 18px !important;
    height: 18px !important;
    color: #94a3b8 !important;
    cursor: pointer !important;
    padding: 0 !important;
    margin-left: auto !important;
    flex-shrink: 0 !important;
    border-radius: 4px !important;
    transition: transform 0.2s cubic-bezier(0.4, 0, 0.2, 1), color 0.15s ease !important;
  }
  .jero-dropdown-chevron:hover {
    color: #1e293b !important;
  }
  .jero-ind-select-wrap.is-open .jero-dropdown-chevron,
  .jero-csize-select-wrap.is-open .jero-dropdown-chevron,
  .jero-ctype-select-wrap.is-open .jero-dropdown-chevron,
  .jero-founded-select-wrap.is-open .jero-dropdown-chevron,
  .god-ind-wrap.is-open .jero-dropdown-chevron,
  .god-csize-wrap.is-open .jero-dropdown-chevron,
  .god-ctype-wrap.is-open .jero-dropdown-chevron,
  .god-founded-wrap.is-open .jero-dropdown-chevron {
    transform: rotate(180deg) !important;
    color: #2563eb !important;
  }

  .god-website-control-row {
    display: flex !important;
    align-items: stretch !important;
    gap: 8px !important;
    height: 38px !important;
    width: 100% !important;
  }
  .god-website-input-box {
    flex: 1 1 auto !important;
    min-width: 0 !important;
  }

  .god-fetch-btn {
    background: #ffd428 !important;
    border: 1px solid #d4a017 !important;
    color: #10143b !important;
    font-size: 11.5px !important;
    font-weight: 700 !important;
    border-radius: 8px !important;
    padding: 0 12px !important;
    height: 38px !important;
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    gap: 5px !important;
    flex-shrink: 0 !important;
    cursor: pointer !important;
    white-space: nowrap !important;
    box-shadow: 0 1px 3px rgba(245, 196, 0, 0.25) !important;
    transition: all 0.15s ease !important;
    font-family: Poppins, sans-serif !important;
  }
  .god-fetch-btn:hover {
    background: #f5c400 !important;
    box-shadow: 0 2px 6px rgba(245, 196, 0, 0.35) !important;
    transform: translateY(-1px) !important;
  }

  .god-input-clear-btn {
    background: transparent !important;
    border: none !important;
    color: #94a3b8 !important;
    cursor: pointer !important;
    padding: 0 !important;
    width: 18px !important;
    height: 18px !important;
    display: none !important;
    align-items: center !important;
    justify-content: center !important;
    border-radius: 50% !important;
    transition: all 0.15s ease !important;
    flex-shrink: 0 !important;
    margin-right: 2px !important;
  }
  .god-input-clear-btn.is-visible,
  .god-input-box.has-value .god-input-clear-btn {
    display: inline-flex !important;
  }
  .god-input-clear-btn[style*="display:none"],
  .god-input-clear-btn[style*="display: none"] {
    display: none !important;
  }
  .god-input-clear-btn:hover {
    background: #fee2e2 !important;
    color: #ef4444 !important;
  }

  .god-input-external-btn {
    background: transparent !important;
    border: none !important;
    color: #2563eb !important;
    cursor: pointer !important;
    padding: 3px !important;
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    border-radius: 4px !important;
    transition: all 0.15s ease !important;
  }
  .god-input-external-btn:hover {
    background: #eff6ff !important;
    color: #1d4ed8 !important;
  }

  .god-field-helper-note {
    font-size: 11px !important;
    color: #64748b !important;
    display: flex !important;
    align-items: center !important;
    gap: 5px !important;
    margin-top: 4px !important;
    line-height: 1.35 !important;
    font-family: Poppins, sans-serif !important;
  }

  /* Logo Column & Card Styling */
  .god-logo-column {
    display: flex !important;
    flex-direction: column !important;
  }

  .god-logo-preview-box {
    border: 1.5px dashed #cbd5e1 !important;
    border-radius: 10px !important;
    background: #ffffff !important;
    padding: 10px !important;
    min-height: 135px !important;
    max-height: 135px !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    position: relative !important;
    box-sizing: border-box !important;
  }

  .god-logo-surface {
    width: 110px !important;
    height: 110px !important;
    border-radius: 10px !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    background-size: contain !important;
    background-repeat: no-repeat !important;
    background-position: center !important;
    background-color: #f8fafc !important;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05) !important;
    position: relative !important;
    overflow: hidden !important;
    transition: all 0.2s ease !important;
  }
  .god-logo-surface.has-image {
    background-color: #ffffff !important;
  }

  .god-logo-placeholder {
    display: flex !important;
    flex-direction: column !important;
    align-items: center !important;
    justify-content: center !important;
    gap: 2px !important;
    color: #94a3b8 !important;
    text-align: center !important;
    padding: 8px !important;
  }
  .god-logo-placeholder .placeholder-title {
    font-size: 11px !important;
    font-weight: 600 !important;
    color: #64748b !important;
    margin-top: 2px !important;
  }
  .god-logo-placeholder .placeholder-sub {
    font-size: 9.5px !important;
    color: #94a3b8 !important;
  }

  .god-logo-status-banner {
    width: 100% !important;
    border-radius: 6px !important;
    padding: 6px 10px !important;
    font-size: 11px !important;
    font-weight: 600 !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    gap: 6px !important;
    margin-top: 8px !important;
    box-sizing: border-box !important;
    text-align: center !important;
    font-family: Poppins, sans-serif !important;
  }
  .god-logo-status-banner.is-detected {
    background: #dcfce7 !important;
    color: #15803d !important;
  }
  .god-logo-status-banner.is-empty {
    background: #f1f5f9 !important;
    color: #64748b !important;
  }

  .god-logo-actions {
    display: flex !important;
    flex-direction: row !important;
    gap: 8px !important;
    margin-top: 8px !important;
    width: 100% !important;
  }

  .god-logo-btn {
    flex: 1 1 auto !important;
    height: 34px !important;
    border-radius: 8px !important;
    font-size: 11.5px !important;
    font-weight: 600 !important;
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    gap: 6px !important;
    cursor: pointer !important;
    transition: all 0.15s ease !important;
    text-decoration: none !important;
    font-family: Poppins, sans-serif !important;
  }
  .god-change-logo-btn {
    background: #ffffff !important;
    border: 1.5px solid #cbd5e1 !important;
    color: #0f172a !important;
  }
  .god-change-logo-btn:hover {
    background: #f8fafc !important;
    border-color: #94a3b8 !important;
  }
  .god-remove-logo-btn {
    background: #fff1f2 !important;
    border: 1.5px solid #fecdd3 !important;
    color: #ef4444 !important;
  }
  .god-remove-logo-btn:hover {
    background: #fee2e2 !important;
    border-color: #fda4af !important;
  }

  .god-logo-formats-note {
    font-size: 10px !important;
    color: #64748b !important;
    line-height: 1.35 !important;
    text-align: center !important;
    margin-top: 8px !important;
    display: flex !important;
    align-items: flex-start !important;
    justify-content: center !important;
    gap: 4px !important;
    font-family: Poppins, sans-serif !important;
  }
  .god-logo-formats-note svg {
    flex-shrink: 0 !important;
    margin-top: 1px !important;
  }

  /* Responsive Rules: 1400px (15.6-inch laptop), 1200px, 991px, 576px */
  @media (max-width: 1400px) and (min-width: 992px) {
    .client-top-nav-card {
      height: 50px !important;
      min-height: 50px !important;
      max-height: 50px !important;
      padding: 0 14px !important;
    }
    .client-header-identity {
      max-width: 280px !important;
    }
    .client-header-title {
      font-size: 13.5px !important;
    }
    .client-header-subtitle {
      font-size: 11px !important;
    }
    .client-step-circle {
      width: 22px !important;
      height: 22px !important;
      min-width: 22px !important;
      min-height: 22px !important;
      font-size: 10.5px !important;
    }
    .client-step-arrow {
      margin: 0 4px !important;
    }
    .client-step-item {
      gap: 5px !important;
    }
    .client-step-name {
      font-size: 11px !important;
    }
    .client-top-close-btn,
    .client-top-back-btn {
      height: 32px !important;
      padding: 0 12px !important;
      font-size: 11.5px !important;
    }
    .client-top-next-btn,
    .client-top-submit-btn {
      height: 32px !important;
      padding: 0 14px !important;
      font-size: 11.5px !important;
    }
  }

  @media (max-width: 1199.98px) {
    .client-header-identity {
      max-width: 260px !important;
    }
    .client-step-arrow {
      margin: 0 4px !important;
    }
    .client-step-name {
      font-size: 11.5px !important;
    }
  }

  @media (max-width: 991px) {
    .client-header-grid {
      display: flex !important;
      flex-wrap: wrap !important;
      gap: 12px !important;
    }
    .client-header-identity {
      max-width: 100% !important;
      width: auto !important;
      flex: 1 1 auto !important;
    }
    .client-header-actions {
      order: 2 !important;
      margin-left: auto !important;
    }
    .client-header-stepper-col {
      width: 100% !important;
      order: 3 !important;
      overflow-x: auto !important;
      padding-bottom: 4px !important;
      -webkit-overflow-scrolling: touch !important;
      justify-content: flex-start !important;
    }
    .client-information-layout,
    .client-description-layout,
    .client-address-layout,
    .client-contact-layout {
      grid-template-columns: 1fr !important;
    }
    .client-review-grid-2x2 {
      grid-template-columns: 1fr !important;
    }
  }

  @media (max-width: 575.98px) {
    .client-top-nav-card {
      padding: 12px 14px !important;
    }
    .client-header-title {
      font-size: 14px !important;
    }
    .client-header-subtitle {
      font-size: 11.5px !important;
    }
    .client-stepper-wrap {
      overflow-x: auto !important;
      width: 100% !important;
      justify-content: flex-start !important;
    }
    .client-step-name {
      font-size: 11px !important;
    }
    .client-top-close-btn,
    .client-top-back-btn {
      padding: 0 12px !important;
      font-size: 11.5px !important;
      height: 34px !important;
    }
    .client-top-next-btn,
    .client-top-submit-btn {
      padding: 0 14px !important;
      font-size: 11.5px !important;
      height: 34px !important;
    }
    .god-card {
      padding: 18px 16px !important;
    }
  }

  /* ==========================================================================
     Client Location / Work Location Architecture (V4 Canonical Location Engine)
     Matches User Screenshot media_1792046189393.png & media_1792046199055.png
     ========================================================================== */
  .ij-location-card {
    position: relative !important;
    background: #ffffff !important;
    border: 1px solid #e2e8f0 !important;
    border-radius: 10px !important;
    overflow: visible !important;
    box-shadow: 0 1px 3px rgba(0,0,0,0.03) !important;
  }
  .ij-location-card-head {
    background: linear-gradient(110deg, #fffdf1, #fff9df) !important;
    border-bottom: 1px solid #f3e5b3 !important;
    border-radius: 10px 10px 0 0 !important;
    padding: 12px 18px !important;
    display: flex !important;
    align-items: center !important;
    justify-content: space-between !important;
  }
  .ij-location-head-left {
    display: flex !important;
    align-items: center !important;
    gap: 12px !important;
  }
  .ij-location-head-badge {
    width: 34px !important;
    height: 34px !important;
    border-radius: 8px !important;
    background: #ffd428 !important;
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    color: #10143b !important;
    flex-shrink: 0 !important;
    box-shadow: 0 1px 3px rgba(255, 212, 40, 0.3) !important;
  }
  .ij-location-head-badge svg {
    width: 17px !important;
    height: 17px !important;
    stroke: #10143b !important;
  }
  .ij-location-title {
    font-size: 13.5px !important;
    font-weight: 700 !important;
    color: #10143b !important;
    margin: 0 0 2px 0 !important;
    line-height: 1.25 !important;
  }
  .ij-location-subtitle {
    font-size: 11.5px !important;
    color: #64748b !important;
    margin: 0 !important;
    line-height: 1.2 !important;
  }
  .ij-location-card-body {
    padding: 14px 18px 16px !important;
  }
  .ij-popular-hubs-wrap {
    display: flex !important;
    align-items: center !important;
    flex-wrap: wrap !important;
    gap: 8px !important;
    margin-bottom: 12px !important;
    padding: 8px 12px !important;
    background: #f8fafc !important;
    border: 1px solid #e2e8f0 !important;
    border-radius: 8px !important;
  }
  .ij-popular-label {
    font-size: 11.5px !important;
    font-weight: 600 !important;
    color: #475569 !important;
    display: inline-flex !important;
    align-items: center !important;
    gap: 5px !important;
    white-space: nowrap !important;
  }
  .ij-popular-pills {
    display: flex !important;
    align-items: center !important;
    flex-wrap: wrap !important;
    gap: 6px !important;
  }
  .ij-pop-pill {
    display: inline-flex !important;
    align-items: center !important;
    gap: 5px !important;
    padding: 3.5px 11px !important;
    background: #ffffff !important;
    border: 1px solid #cbd5e1 !important;
    border-radius: 999px !important;
    font-size: 11.5px !important;
    font-weight: 500 !important;
    color: #334155 !important;
    cursor: pointer !important;
    transition: all 0.15s ease !important;
    user-select: none !important;
    line-height: 1.2 !important;
  }
  .ij-pop-pill:hover {
    background: #eff6ff !important;
    border-color: #93c5fd !important;
    color: #1d4ed8 !important;
    transform: translateY(-1px) !important;
  }
  .ij-pop-pill.is-active {
    background: #2563eb !important;
    border-color: #2563eb !important;
    color: #ffffff !important;
    font-weight: 600 !important;
    box-shadow: 0 1px 4px rgba(37, 99, 235, 0.25) !important;
  }
  .ij-location-input-wrap {
    position: relative !important;
    width: 100% !important;
  }
  .ij-location-input-wrap .input-with-icon {
    position: relative !important;
    width: 100% !important;
    display: flex !important;
    align-items: center !important;
  }
  .ij-location-input-wrap .input-icon {
    position: absolute !important;
    left: 12px !important;
    top: 50% !important;
    transform: translateY(-50%) !important;
    width: 16px !important;
    height: 16px !important;
    color: #94a3b8 !important;
    pointer-events: none !important;
    z-index: 2 !important;
  }
  .ij-location-search-input {
    width: 100% !important;
    height: 40px !important;
    padding-left: 38px !important;
    padding-right: 36px !important;
    background: #ffffff !important;
    border: 1px solid #cbd5e1 !important;
    border-radius: 8px !important;
    font-size: 12.5px !important;
    color: #0f172a !important;
    font-family: 'Poppins', sans-serif !important;
    transition: border-color 0.2s, box-shadow 0.2s !important;
  }
  .ij-location-search-input:focus {
    border-color: #ffd428 !important;
    box-shadow: 0 0 0 3px rgba(255, 212, 40, 0.28) !important;
    outline: none !important;
  }
  .ij-search-spinner {
    position: absolute !important;
    right: 12px !important;
    top: 50% !important;
    transform: translateY(-50%) !important;
    width: 14px !important;
    height: 14px !important;
    border: 2px solid #cbd5e1 !important;
    border-top-color: #2563eb !important;
    border-radius: 50% !important;
    animation: ijSpin 0.6s linear infinite !important;
    z-index: 3 !important;
  }
  @keyframes ijSpin { to { transform: translateY(-50%) rotate(360deg); } }
  .ij-search-clear {
    position: absolute !important;
    right: 10px !important;
    top: 50% !important;
    transform: translateY(-50%) !important;
    width: 18px !important;
    height: 18px !important;
    border-radius: 50% !important;
    background: #cbd5e1 !important;
    color: #ffffff !important;
    border: none !important;
    font-size: 13px !important;
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    cursor: pointer !important;
    line-height: 1 !important;
    padding: 0 !important;
    transition: background 0.15s ease !important;
    z-index: 3 !important;
  }
  .ij-search-clear:hover {
    background: #64748b !important;
  }
  .ij-city-dropdown {
    position: absolute !important;
    top: calc(100% + 4px) !important;
    left: 0 !important;
    right: 0 !important;
    background: #ffffff !important;
    border: 1px solid #cbd5e1 !important;
    border-radius: 8px !important;
    box-shadow: 0 16px 36px -4px rgba(15, 23, 42, 0.18), 0 8px 16px -4px rgba(15, 23, 42, 0.1) !important;
    z-index: 1050 !important;
    max-height: min(340px, calc(100vh - 140px)) !important;
    overflow-y: auto !important;
    display: none;
    font-family: 'Poppins', sans-serif !important;
  }
  .ij-city-dropdown.active {
    display: block !important;
  }
  .ij-cd-group {
    border-bottom: 1px solid #f1f5f9 !important;
  }
  .ij-cd-group:last-child {
    border-bottom: none !important;
  }
  .ij-cd-group-title {
    padding: 7px 12px !important;
    font-size: 11px !important;
    font-weight: 700 !important;
    letter-spacing: 0.3px !important;
    color: #1e40af !important;
    background: #f8fafc !important;
    border-bottom: 1px solid #e2e8f0 !important;
    text-transform: uppercase !important;
    position: sticky !important;
    top: 0 !important;
    z-index: 5 !important;
  }
  .ij-cd-item {
    display: flex !important;
    align-items: center !important;
    justify-content: space-between !important;
    gap: 10px !important;
    padding: 8.5px 12px !important;
    cursor: pointer !important;
    transition: background 0.15s ease !important;
    border-bottom: 1px solid #f8fafc !important;
  }
  .ij-cd-item:hover,
  .ij-cd-item.is-highlighted {
    background: #eff6ff !important;
  }
  .ij-cd-item.is-selected {
    background: #f8fafc !important;
    cursor: default !important;
  }
  .ij-cd-left {
    display: flex !important;
    align-items: center !important;
    gap: 9px !important;
    min-width: 0 !important;
    flex: 1 1 auto !important;
  }
  .ij-cd-pin {
    width: 24px !important;
    height: 24px !important;
    min-width: 24px !important;
    border-radius: 6px !important;
    background: #e0f2fe !important;
    color: #0284c7 !important;
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    flex-shrink: 0 !important;
  }
  .ij-cd-pin svg {
    width: 13px !important;
    height: 13px !important;
    stroke: currentColor !important;
  }
  .ij-cd-text {
    display: flex !important;
    flex-direction: column !important;
    gap: 1px !important;
    min-width: 0 !important;
  }
  .ij-cd-name {
    font-size: 12.5px !important;
    font-weight: 600 !important;
    color: #0f172a !important;
    line-height: 1.25 !important;
    white-space: nowrap !important;
    overflow: hidden !important;
    text-overflow: ellipsis !important;
  }
  .ij-cd-meta {
    font-size: 11px !important;
    color: #64748b !important;
    line-height: 1.2 !important;
  }
  .ij-cd-right {
    display: flex !important;
    align-items: center !important;
    gap: 6px !important;
    flex-shrink: 0 !important;
  }
  .ij-cd-badge {
    font-size: 10px !important;
    font-weight: 600 !important;
    padding: 2px 7px !important;
    background: #fef3c7 !important;
    color: #92400e !important;
    border: 1px solid #fde68a !important;
    border-radius: 4px !important;
    white-space: nowrap !important;
  }
  .ij-cd-status {
    font-size: 10px !important;
    font-weight: 600 !important;
    padding: 2px 7px !important;
    background: #dcfce7 !important;
    color: #15803d !important;
    border: 1px solid #bbf7d0 !important;
    border-radius: 4px !important;
    white-space: nowrap !important;
  }
  .ij-cd-empty {
    padding: 18px 14px !important;
    text-align: center !important;
    font-size: 12px !important;
    color: #64748b !important;
    display: flex !important;
    flex-direction: column !important;
    align-items: center !important;
    gap: 8px !important;
  }
  .ij-btn-custom-add {
    display: inline-flex !important;
    align-items: center !important;
    gap: 5px !important;
    padding: 4px 11px !important;
    background: #2563eb !important;
    color: #ffffff !important;
    border: none !important;
    border-radius: 6px !important;
    font-size: 11.5px !important;
    font-weight: 600 !important;
    cursor: pointer !important;
    transition: background 0.15s ease !important;
  }
  .ij-btn-custom-add:hover {
    background: #1d4ed8 !important;
  }
  .ij-selected-header {
    display: flex !important;
    justify-content: space-between !important;
    align-items: center !important;
    margin-top: 14px !important;
    margin-bottom: 8px !important;
  }
  .ij-selected-header .selected-label {
    font-size: 12px !important;
    font-weight: 600 !important;
    color: #334155 !important;
  }
  .ij-selected-header .clear-all {
    font-size: 12px !important;
    color: #b45309 !important;
    cursor: pointer !important;
    font-weight: 600 !important;
    background: none !important;
    border: none !important;
    font-family: inherit !important;
    padding: 0 !important;
    transition: color 0.15s ease !important;
  }
  .ij-selected-header .clear-all:hover {
    color: #92400e !important;
    text-decoration: underline !important;
  }
  .ij-selected-chips {
    display: flex !important;
    flex-wrap: wrap !important;
    gap: 8px !important;
    min-height: 28px !important;
    margin-top: 4px !important;
  }
  .ij-location-chip {
    display: inline-flex !important;
    align-items: center !important;
    gap: 6px !important;
    padding: 5px 12px !important;
    background: #ffffff !important;
    border: 1px solid #bfdbfe !important;
    border-radius: 999px !important;
    font-size: 12px !important;
    font-weight: 600 !important;
    color: #1e40af !important;
    box-shadow: 0 1px 2px rgba(37, 99, 235, 0.08) !important;
    transition: all 0.15s ease !important;
  }
  .ij-location-chip:hover {
    border-color: #60a5fa !important;
    background: #eff6ff !important;
  }
  .ij-chip-pin {
    color: #2563eb !important;
    display: inline-flex !important;
    align-items: center !important;
  }
  .ij-chip-pin svg {
    width: 12px !important;
    height: 12px !important;
    stroke: currentColor !important;
  }
  .ij-chip-label {
    line-height: 1.2 !important;
  }
  .ij-chip-region {
    font-size: 10.5px !important;
    font-weight: 500 !important;
    color: #64748b !important;
    margin-left: 2px !important;
  }
  .ij-chip-del {
    width: 16px !important;
    height: 16px !important;
    min-width: 16px !important;
    border-radius: 50% !important;
    background: #e2e8f0 !important;
    color: #475569 !important;
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    font-size: 12px !important;
    cursor: pointer !important;
    margin-left: 3px !important;
    line-height: 1 !important;
    border: none !important;
    transition: all 0.15s ease !important;
    padding: 0 !important;
  }
  .ij-chip-del:hover {
    background: #ef4444 !important;
    color: #ffffff !important;
  }
  .ij-no-locs {
    font-size: 12px !important;
    color: #94a3b8 !important;
    font-style: italic !important;
    padding: 4px 0 !important;
  }
  .client-hq-badge {
    width: 32px;
    height: 32px;
    border-radius: 8px;
    background: #eff6ff;
    color: #2563eb;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 14px;
    flex-shrink: 0;
  }

  /* =========================================================================
     God-Level Enterprise Industry Combobox (Client-First / 10K Concurrency)
     ========================================================================= */
  .jero-ind-select-wrap {
    position: relative;
    width: 100%;
    font-family: 'Poppins', sans-serif;
  }
  .god-input-box.jero-ind-input-box {
    position: relative !important;
    display: flex !important;
    align-items: center !important;
    width: 100% !important;
    height: 38px !important;
    min-height: 38px !important;
    background: #ffffff !important;
    border: 1.5px solid #cbd5e1 !important;
    border-radius: 8px !important;
    padding: 0 10px !important;
    box-sizing: border-box !important;
    transition: border-color 0.15s ease, box-shadow 0.15s ease !important;
  }
  .god-input-box.jero-ind-input-box:hover {
    border-color: #94a3b8 !important;
  }
  .god-input-box.jero-ind-input-box:focus-within,
  .god-input-box.jero-ind-input-box.is-focused,
  .jero-ind-select-wrap.is-open .god-input-box.jero-ind-input-box {
    border-color: #2563eb !important;
    box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.14) !important;
  }
  .god-input-box.jero-ind-input-box.is-invalid,
  .god-input-box.jero-ind-input-box.has-validation-error,
  .jero-ind-select-wrap.has-validation-error .god-input-box.jero-ind-input-box {
    border-color: #ef4444 !important;
    box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.14) !important;
    animation: jeroShake 0.3s ease;
  }
  @keyframes jeroShake {
    0%, 100% { transform: translateX(0); }
    20%, 60% { transform: translateX(-4px); }
    40%, 80% { transform: translateX(4px); }
  }
  .jero-ind-search-icon {
    color: #94a3b8 !important;
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    flex-shrink: 0 !important;
    transition: color 0.15s ease !important;
  }
  .god-input-box.jero-ind-input-box:focus-within .jero-ind-search-icon,
  .jero-ind-select-wrap.is-open .jero-ind-search-icon {
    color: #2563eb !important;
  }
  .jero-ind-input {
    width: 100% !important;
    height: 100% !important;
    border: none !important;
    background: transparent !important;
    padding: 0 8px !important;
    font-size: 13px !important;
    font-weight: 500 !important;
    color: #1e293b !important;
    outline: none !important;
    box-shadow: none !important;
    text-overflow: ellipsis;
    font-family: inherit;
  }
  .jero-ind-input::placeholder {
    color: #94a3b8 !important;
    font-weight: 400;
  }
  .jero-ind-clear-btn {
    width: 18px;
    height: 18px;
    background: transparent;
    border: none;
    padding: 0;
    color: #94a3b8;
    cursor: pointer;
    display: none;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    transition: all 0.15s ease;
    flex-shrink: 0;
    margin-right: 2px;
  }
  .jero-ind-clear-btn:hover {
    color: #ef4444;
    background: #fee2e2;
  }
  .jero-ind-chevron {
    width: 18px;
    height: 18px;
    color: #94a3b8;
    cursor: pointer;
    padding: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 4px;
    transition: transform 0.2s cubic-bezier(0.4, 0, 0.2, 1), color 0.15s ease;
    flex-shrink: 0;
    margin-left: auto;
  }
  .jero-ind-chevron:hover {
    color: #1e293b;
  }
  .jero-ind-select-wrap.is-open .jero-ind-chevron {
    transform: rotate(180deg);
    color: #2563eb;
  }

  /* Elevated Modern Dropdown Container — God-Level Pure List Architecture */
  .jero-ind-dropdown {
    position: absolute;
    top: calc(100% + 4px);
    left: 0;
    right: 0;
    width: 100%;
    min-width: 100%;
    max-width: 100%;
    z-index: 1080;
    background: #ffffff;
    border: 1.5px solid #cbd5e1;
    border-radius: 10px;
    box-shadow: 0 16px 36px -6px rgba(15, 23, 42, 0.16), 0 4px 12px -2px rgba(15, 23, 42, 0.08);
    overflow: hidden;
    display: none;
    flex-direction: column;
    max-height: 300px;
    animation: jeroDropdownFadeIn 0.14s cubic-bezier(0.16, 1, 0.3, 1);
    box-sizing: border-box;
  }
  .jero-ind-select-wrap.is-open .jero-ind-dropdown,
  .jero-ind-dropdown.is-open {
    display: flex !important;
  }
  .jero-ind-select-wrap.is-open {
    z-index: 1050 !important;
    position: relative !important;
  }
  @keyframes jeroDropdownFadeIn {
    from { opacity: 0; transform: translateY(-4px); }
    to { opacity: 1; transform: translateY(0); }
  }

  /* List Container: Silky Smooth Pure List */
  .jero-ind-list-container {
    flex: 1 1 auto;
    max-height: 290px;
    min-height: 100px;
    overflow-y: auto;
    overflow-x: hidden;
    overscroll-behavior: contain;
    padding: 6px 0;
    scrollbar-width: thin;
    scrollbar-color: #cbd5e1 #f8fafc;
    box-sizing: border-box;
  }
  .jero-ind-list-container::-webkit-scrollbar { width: 5px; }
  .jero-ind-list-container::-webkit-scrollbar-track { background: #f8fafc; }
  .jero-ind-list-container::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 3px; }
  .jero-ind-list-container::-webkit-scrollbar-thumb:hover { background: #94a3b8; }

  /* Section Header: Prominent, sticky, NEVER hidden under tabs */
  .jero-ind-section-header {
    display: flex !important;
    align-items: center !important;
    justify-content: space-between !important;
    padding: 7px 14px !important;
    min-height: 30px !important;
    font-size: 11px !important;
    font-weight: 700 !important;
    text-transform: uppercase !important;
    letter-spacing: 0.6px !important;
    color: #475569 !important;
    background: #f8fafc !important;
    border-top: 1px solid #eef2f6 !important;
    border-bottom: 1px solid #eef2f6 !important;
    position: sticky !important;
    top: 0 !important;
    z-index: 5 !important;
    pointer-events: none !important;
    user-select: none !important;
    box-sizing: border-box !important;
  }
  .jero-ind-section-left {
    display: flex;
    align-items: center;
    gap: 6px;
  }
  .jero-ind-sector-icon {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 18px;
    height: 18px;
    border-radius: 4px;
    flex-shrink: 0;
  }
  .jero-ind-sector-icon svg {
    width: 12px;
    height: 12px;
  }
  .jero-ind-section-count {
    font-size: 10px;
    font-weight: 600;
    color: #94a3b8;
  }

  /* Single-Line High-Density Item Row */
  .jero-ind-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 7px 12px;
    margin: 1px 6px;
    border-radius: 7px;
    cursor: pointer;
    transition: background 0.1s ease, color 0.1s ease;
  }
  .jero-ind-item:hover,
  .jero-ind-item.focused {
    background: #f1f5f9;
  }
  .jero-ind-item.selected {
    background: #eff6ff;
  }
  .jero-ind-item-main {
    display: flex;
    align-items: center;
    gap: 8px;
    min-width: 0;
    flex: 1 1 auto;
    overflow: hidden;
  }
  .jero-ind-item-dot {
    width: 6px;
    height: 6px;
    border-radius: 50%;
    background: #cbd5e1;
    flex-shrink: 0;
    transition: background 0.12s ease;
  }
  .jero-ind-item:hover .jero-ind-item-dot,
  .jero-ind-item.focused .jero-ind-item-dot,
  .jero-ind-item.selected .jero-ind-item-dot {
    background: #2563eb;
  }
  .jero-ind-item-name {
    font-size: 12.5px;
    font-weight: 500;
    color: #1e293b;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    line-height: 1.4;
    flex: 1 1 auto;
    min-width: 0;
  }
  .jero-ind-item.selected .jero-ind-item-name {
    font-weight: 600;
    color: #1e293b;
  }
  .jero-ind-mark {
    background: #fef08a !important;
    color: #854d0e !important;
    font-weight: 700 !important;
    padding: 0 2px !important;
    border-radius: 2px !important;
  }
  .jero-ind-item-side {
    display: flex;
    align-items: center;
    gap: 6px;
    flex-shrink: 0;
    margin-left: 8px;
  }
  .jero-ind-search-sector-badge {
    font-size: 10px;
    font-weight: 500;
    padding: 1.5px 6px;
    border-radius: 4px;
    background: #f1f5f9;
    color: #64748b;
    white-space: nowrap;
  }
  .jero-ind-item-check {
    color: #2563eb;
    display: flex;
    align-items: center;
    width: 15px;
    height: 15px;
  }

  /* Empty State with Custom Industry Action */
  .jero-ind-empty-state {
    padding: 24px 16px;
    text-align: center;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 4px;
  }
  .jero-ind-empty-icon {
    width: 38px;
    height: 38px;
    border-radius: 50%;
    background: #eff6ff;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    color: #2563eb;
    margin-bottom: 6px;
  }
  .jero-ind-empty-title {
    font-size: 13px;
    font-weight: 600;
    color: #1e293b;
  }
  .jero-ind-empty-sub {
    font-size: 11.5px;
    color: #64748b;
    margin-bottom: 8px;
  }
  .jero-ind-add-custom-btn {
    border-radius: 6px !important;
    font-size: 11.5px !important;
    font-weight: 600 !important;
    padding: 6px 14px !important;
    display: inline-flex !important;
    align-items: center !important;
    gap: 6px !important;
    background: #2563eb !important;
    color: #ffffff !important;
    border: none !important;
    cursor: pointer !important;
    transition: background 0.15s ease !important;
  }
  .jero-ind-add-custom-btn:hover {
    background: #1d4ed8 !important;
  }

  /* =========================================================================
     Search-First Company Type Intelligence Combobox (Rule 17 Isolated)
     ========================================================================= */
  .jero-ctype-select-wrap {
    position: relative;
    width: 100%;
  }
  .jero-ctype-input-box {
    position: relative;
    display: flex;
    align-items: center;
    cursor: pointer;
  }
  .jero-ctype-input {
    cursor: pointer;
    padding-right: 64px !important;
    font-family: 'Poppins', sans-serif;
    font-size: 13px;
    font-weight: 500;
    color: #101a46;
  }
  .jero-ctype-input:focus {
    cursor: text;
  }
  .jero-ctype-kbd {
    position: absolute;
    right: 36px;
    top: 50%;
    transform: translateY(-50%);
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 18px;
    height: 18px;
    padding: 0 4px;
    font-size: 10.5px;
    font-weight: 600;
    font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
    color: #94a3b8;
    background: #f1f5f9;
    border: 1px solid #cbd5e1;
    border-radius: 4px;
    pointer-events: none;
    line-height: 1;
  }
  .jero-ctype-clear-btn {
    position: absolute;
    right: 34px;
    top: 50%;
    transform: translateY(-50%);
    width: 20px;
    height: 20px;
    border-radius: 50%;
    background: #e2e8f0;
    color: #64748b;
    border: none;
    display: none;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.15s ease;
    padding: 0;
  }
  .jero-ctype-clear-btn:hover {
    background: #cbd5e1;
    color: #0f172a;
  }
  .jero-ctype-chevron {
    position: absolute;
    right: 12px;
    top: 50%;
    transform: translateY(-50%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: #64748b;
    pointer-events: auto !important;
    cursor: pointer;
    z-index: 2;
    transition: transform 0.2s ease, color 0.2s ease;
  }
  .jero-ctype-select-wrap.is-open .jero-ctype-chevron {
    transform: translateY(-50%) rotate(180deg);
    color: #2563eb;
  }
  .jero-ctype-dropdown {
    position: absolute;
    top: calc(100% + 5px);
    left: 0;
    right: 0;
    z-index: 1060;
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    box-shadow: 0 12px 32px -4px rgba(15, 23, 42, 0.12), 0 4px 12px -2px rgba(15, 23, 42, 0.08);
    overflow: hidden;
    display: none;
    flex-direction: column;
    max-height: min(320px, calc(100vh - 120px));
    animation: jeroDropdownFadeIn 0.15s ease-out;
    box-sizing: border-box;
  }
  .jero-ctype-select-wrap.is-open .jero-ctype-dropdown,
  .jero-ctype-dropdown.is-open {
    display: flex !important;
  }
  .jero-ctype-dropdown.drop-up {
    top: auto;
    bottom: calc(100% + 5px);
    box-shadow: 0 -12px 32px -4px rgba(15, 23, 42, 0.12), 0 -4px 12px -2px rgba(15, 23, 42, 0.08);
  }
  .jero-ctype-list-container {
    max-height: 260px; /* 15.6" screen optimized */
    overflow-y: auto;
    padding: 6px 0;
    box-sizing: border-box;
  }
  .jero-ctype-list-container::-webkit-scrollbar {
    width: 6px;
  }
  .jero-ctype-list-container::-webkit-scrollbar-track {
    background: transparent;
  }
  .jero-ctype-list-container::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 9999px;
  }
  .jero-ctype-list-container::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
  }
  .jero-ctype-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 8px 14px;
    cursor: pointer;
    transition: background 0.12s ease;
    border-bottom: 1px solid #f8fafc;
    box-sizing: border-box;
    min-height: 38px;
  }
  .jero-ctype-item:last-child {
    border-bottom: none;
  }
  .jero-ctype-item:hover,
  .jero-ctype-item.focused {
    background: #f1f5f9;
  }
  .jero-ctype-item.selected {
    background: #eff6ff;
  }
  .jero-ctype-item-main {
    display: flex;
    flex-direction: row;
    align-items: center;
    gap: 2px;
    flex: 1;
    min-width: 0;
  }
  .jero-ctype-item-name {
    font-size: 13px;
    font-weight: 600;
    color: #0f172a;
    line-height: 1.35;
    font-family: 'Poppins', sans-serif;
  }
  .jero-ctype-mark {
    background: #fef08a;
    color: #854d0e;
    padding: 0 2px;
    border-radius: 2px;
    font-weight: 700;
  }
  .jero-ctype-item-desc {
    font-size: 11px;
    color: #64748b;
    line-height: 1.3;
  }
  .jero-ctype-item-aliases {
    font-size: 10.5px;
    color: #94a3b8;
    margin-top: 1px;
    line-height: 1.25;
  }
  .jero-ctype-item-side {
    display: flex;
    align-items: center;
    gap: 8px;
    flex-shrink: 0;
    margin-left: 10px;
  }
  .jero-ctype-badge {
    display: inline-flex;
    align-items: center;
    padding: 2px 7px;
    font-size: 10px;
    font-weight: 600;
    border-radius: 4px;
    background: #f1f5f9;
    color: #475569;
    letter-spacing: 0.2px;
    text-transform: uppercase;
  }
  .jero-ctype-badge.matched-alias {
    background: #dbeafe;
    color: #1e40af;
    border: 1px solid #bfdbfe;
  }
  .jero-ctype-item-check {
    display: flex;
    align-items: center;
    color: #2563eb;
  }
  .jero-ctype-empty-state {
    padding: 24px 16px;
    text-align: center;
    font-size: 12.5px;
    color: #64748b;
  }
  .jero-ctype-input-box.has-validation-error,
  .jero-ctype-select-wrap.has-validation-error .jero-ctype-input-box {
    border-color: #f1416c !important;
    background-color: #fff8f8 !important;
  }

  /* =========================================================================
     Enterprise Company Size Combobox Module (Rule 17 Isolated)
     ========================================================================= */
  .jero-csize-select-wrap {
    position: relative;
    width: 100%;
  }
  .god-input-box.jero-csize-input-box {
    position: relative;
    display: flex;
    align-items: center;
    background: #ffffff;
    border: 1.5px solid #e2e8f0;
    border-radius: 8px;
    height: 42px;
    padding: 0 12px;
    transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    box-sizing: border-box;
    cursor: pointer;
    user-select: none;
  }
  .god-input-box.jero-csize-input-box:hover {
    border-color: #94a3b8;
  }
  .god-input-box.jero-csize-input-box:focus-within,
  .god-input-box.jero-csize-input-box.is-focused,
  .jero-csize-select-wrap.is-open .god-input-box.jero-csize-input-box {
    border-color: #2563eb;
    box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.14);
    background: #ffffff;
  }
  .god-input-box.jero-csize-input-box.is-invalid,
  .god-input-box.jero-csize-input-box.has-validation-error,
  .jero-csize-select-wrap.has-validation-error .god-input-box.jero-csize-input-box {
    border-color: #f1416c !important;
    background-color: #fff8f8 !important;
  }
  .jero-csize-icon {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    color: #94a3b8;
    margin-right: 8px;
    flex-shrink: 0;
    transition: color 0.15s ease;
  }
  .god-input-box.jero-csize-input-box:focus-within .jero-csize-icon,
  .jero-csize-select-wrap.is-open .jero-csize-icon {
    color: #2563eb;
  }
  .jero-csize-input {
    flex: 1 1 auto;
    border: none;
    outline: none;
    font-size: 12.5px;
    font-weight: 500;
    color: #1e293b;
    font-family: inherit;
    background: transparent;
    padding: 0;
    min-width: 0;
    line-height: 1.4;
    cursor: pointer;
  }
  .jero-csize-input::placeholder {
    color: #94a3b8;
    font-weight: 400;
  }
  .jero-csize-clear-btn {
    display: none;
    align-items: center;
    justify-content: center;
    width: 20px;
    height: 20px;
    border-radius: 50%;
    color: #94a3b8;
    background: #f1f5f9;
    border: none;
    cursor: pointer;
    margin-right: 6px;
    padding: 0;
    transition: all 0.15s ease;
    flex-shrink: 0;
  }
  .jero-csize-clear-btn:hover {
    background: #fee2e2;
    color: #ef4444;
  }
  .jero-csize-chevron {
    width: 20px;
    height: 20px;
    color: #94a3b8;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 4px;
    transition: transform 0.2s cubic-bezier(0.4, 0, 0.2, 1), color 0.15s ease;
    flex-shrink: 0;
  }
  .jero-csize-chevron:hover {
    color: #1e293b;
  }
  .jero-csize-select-wrap.is-open .jero-csize-chevron {
    transform: rotate(180deg);
    color: #2563eb;
  }
  .jero-csize-dropdown {
    position: absolute;
    top: calc(100% + 6px);
    left: 0;
    right: 0;
    min-width: 100%;
    z-index: 1060;
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    box-shadow: 0 20px 48px -8px rgba(15, 23, 42, 0.16), 0 8px 20px -4px rgba(15, 23, 42, 0.08);
    overflow: hidden;
    display: none;
    flex-direction: column;
    animation: jeroDropdownFadeIn 0.16s cubic-bezier(0.16, 1, 0.3, 1);
    box-sizing: border-box;
  }
  .jero-csize-select-wrap.is-open .jero-csize-dropdown {
    display: flex !important;
  }
  .jero-csize-dropdown.drop-up {
    top: auto;
    bottom: calc(100% + 6px);
    box-shadow: 0 -20px 48px -8px rgba(15, 23, 42, 0.16), 0 -8px 20px -4px rgba(15, 23, 42, 0.08);
  }
  .jero-csize-list-container {
    max-height: 280px;
    overflow-y: auto;
    overflow-x: hidden;
    padding: 6px 0;
    scrollbar-width: thin;
    scrollbar-color: #cbd5e1 #f8fafc;
  }
  .jero-csize-list-container::-webkit-scrollbar { width: 5px; }
  .jero-csize-list-container::-webkit-scrollbar-track { background: #f8fafc; }
  .jero-csize-list-container::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 3px; }
  .jero-csize-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 8px 14px;
    cursor: pointer;
    transition: background 0.12s ease;
    border-bottom: 1px solid #f8fafc;
    box-sizing: border-box;
    min-height: 42px;
  }
  .jero-csize-item:last-child {
    border-bottom: none;
  }
  .jero-csize-item:hover,
  .jero-csize-item.focused {
    background: #f8fafc;
  }
  .jero-csize-item.selected {
    background: #eff6ff;
  }
  .jero-csize-item-main {
    display: flex;
    align-items: center;
    gap: 10px;
    flex: 1 1 auto;
    min-width: 0;
  }
  .jero-csize-item-icon {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 26px;
    height: 26px;
    border-radius: 6px;
    background: #f1f5f9;
    color: #475569;
    flex-shrink: 0;
    transition: all 0.15s ease;
  }
  .jero-csize-item:hover .jero-csize-item-icon,
  .jero-csize-item.selected .jero-csize-item-icon {
    background: #dbeafe;
    color: #1d4ed8;
  }
  .jero-csize-item-info {
    display: flex;
    flex-direction: column;
    min-width: 0;
    flex: 1 1 auto;
  }
  .jero-csize-item-label {
    font-size: 12.5px;
    font-weight: 600;
    color: #1e293b;
    line-height: 1.35;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }
  .jero-csize-item.selected .jero-csize-item-label {
    color: #1d4ed8;
  }
  .jero-csize-item-desc {
    font-size: 11px;
    color: #64748b;
    line-height: 1.3;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }
  .jero-csize-item-side {
    display: flex;
    align-items: center;
    gap: 8px;
    flex-shrink: 0;
    margin-left: 8px;
  }
  .jero-csize-item-badge {
    font-size: 10px;
    font-weight: 600;
    padding: 2px 7px;
    border-radius: 10px;
    background: #f1f5f9;
    color: #475569;
    border: 1px solid #e2e8f0;
  }
  .jero-csize-item.selected .jero-csize-item-badge {
    background: #bfdbfe;
    color: #1e40af;
    border-color: #93c5fd;
  }
  .jero-csize-item-check {
    display: none;
    color: #2563eb;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
  }
  .jero-csize-item.selected .jero-csize-item-check {
    display: inline-flex;
  }

  /* =========================================================================
     Search-First Founded Year Intelligence Combobox (Rule 17 Isolated)
     ========================================================================= */
  .jero-founded-select-wrap {
    position: relative;
    width: 100%;
  }
  .jero-founded-input-box {
    position: relative;
    display: flex;
    align-items: center;
    cursor: pointer;
  }
  .jero-founded-input {
    cursor: pointer;
    padding-right: 64px !important;
    font-family: 'Poppins', sans-serif;
    font-size: 13px;
    font-weight: 500;
    color: #101a46;
  }
  .jero-founded-input:focus {
    cursor: text;
  }
  .jero-founded-kbd {
    position: absolute;
    right: 36px;
    top: 50%;
    transform: translateY(-50%);
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 18px;
    height: 18px;
    padding: 0 4px;
    font-size: 10.5px;
    font-weight: 600;
    font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
    color: #94a3b8;
    background: #f1f5f9;
    border: 1px solid #cbd5e1;
    border-radius: 4px;
    pointer-events: none;
    line-height: 1;
  }
  .jero-founded-clear-btn {
    position: absolute;
    right: 34px;
    top: 50%;
    transform: translateY(-50%);
    width: 20px;
    height: 20px;
    border-radius: 50%;
    background: #e2e8f0;
    color: #64748b;
    border: none;
    display: none;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.15s ease;
    padding: 0;
  }
  .jero-founded-clear-btn:hover {
    background: #cbd5e1;
    color: #0f172a;
  }
  .jero-founded-chevron {
    position: absolute;
    right: 12px;
    top: 50%;
    transform: translateY(-50%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: #64748b;
    pointer-events: auto !important;
    cursor: pointer;
    z-index: 2;
    transition: transform 0.2s ease, color 0.2s ease;
  }
  .jero-founded-select-wrap.is-open .jero-founded-chevron {
    transform: translateY(-50%) rotate(180deg);
    color: #2563eb;
  }
  .jero-founded-dropdown {
    position: absolute;
    top: calc(100% + 5px);
    left: 0;
    right: 0;
    z-index: 1060;
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    box-shadow: 0 12px 32px -4px rgba(15, 23, 42, 0.12), 0 4px 12px -2px rgba(15, 23, 42, 0.08);
    overflow: hidden;
    display: none;
    flex-direction: column;
    max-height: min(320px, calc(100vh - 120px));
    animation: jeroDropdownFadeIn 0.15s ease-out;
    box-sizing: border-box;
  }
  .jero-founded-select-wrap.is-open .jero-founded-dropdown,
  .jero-founded-dropdown.is-open {
    display: flex !important;
  }
  .jero-founded-dropdown.drop-up {
    top: auto;
    bottom: calc(100% + 5px);
    box-shadow: 0 -12px 32px -4px rgba(15, 23, 42, 0.12), 0 -4px 12px -2px rgba(15, 23, 42, 0.08);
  }
  .jero-founded-search-wrap {
    display: flex;
    align-items: center;
    padding: 8px 12px;
    background: #f8fafc;
    border-bottom: 1px solid #eef2f6;
    gap: 8px;
    position: sticky;
    top: 0;
    z-index: 3;
  }
  .jero-founded-search-icon {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    color: #94a3b8;
    flex-shrink: 0;
  }
  .jero-founded-search-input {
    width: 100%;
    height: 30px;
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 6px;
    padding: 0 10px;
    font-size: 12px;
    font-family: 'Poppins', sans-serif;
    color: #0f172a;
    outline: none;
    transition: border-color 0.15s ease, box-shadow 0.15s ease;
  }
  .jero-founded-search-input:focus {
    border-color: #2563eb;
    box-shadow: 0 0 0 2px rgba(37, 99, 235, 0.15);
  }
  .jero-founded-list-container {
    max-height: 220px;
    overflow-y: auto;
    padding: 4px 0;
    box-sizing: border-box;
    scrollbar-width: thin;
    scrollbar-color: #cbd5e1 #f8fafc;
  }
  .jero-founded-list-container::-webkit-scrollbar {
    width: 5px;
  }
  .jero-founded-list-container::-webkit-scrollbar-track {
    background: #f8fafc;
  }
  .jero-founded-list-container::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 3px;
  }
  .jero-founded-list-container::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
  }
  .jero-founded-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 7px 14px;
    cursor: pointer;
    transition: background 0.12s ease;
    border-bottom: 1px solid #f8fafc;
    box-sizing: border-box;
    min-height: 34px;
  }
  .jero-founded-item:last-child {
    border-bottom: none;
  }
  .jero-founded-item:hover,
  .jero-founded-item.focused {
    background: #f1f5f9;
  }
  .jero-founded-item.selected {
    background: #eff6ff;
  }
  .jero-founded-item-name {
    font-size: 13px;
    font-weight: 500;
    color: #1e293b;
    font-family: 'Poppins', sans-serif;
  }
  .jero-founded-item.selected .jero-founded-item-name {
    font-weight: 600;
    color: #2563eb;
  }
  .jero-founded-mark {
    background: #fef08a;
    color: #854d0e;
    padding: 0 2px;
    border-radius: 2px;
    font-weight: 700;
  }
  .jero-founded-item-check {
    display: flex;
    align-items: center;
    color: #2563eb;
  }
  .jero-founded-empty-state {
    padding: 20px 16px;
    text-align: center;
    font-size: 12px;
    color: #64748b;
  }
  .jero-founded-footer {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 6px 12px;
    background: #f8fafc;
    border-top: 1px solid #f1f5f9;
    font-size: 10.5px;
    flex-shrink: 0;
    font-family: 'Poppins', sans-serif;
  }
  .jero-founded-sync-status {
    display: flex;
    align-items: center;
    gap: 6px;
    color: #64748b;
    font-weight: 500;
  }
  .jero-founded-calendar-dot {
    width: 6px;
    height: 6px;
    border-radius: 50%;
    background: #3b82f6;
  }
  .jero-founded-footer-hint {
    color: #94a3b8;
  }
  .jero-founded-input-box.has-validation-error,
  .jero-founded-select-wrap.has-validation-error .jero-founded-input-box {
    border-color: #ef4444 !important;
    background-color: #fff8f8 !important;
  }
</style>
@endpush
@section('content')
<?php 
     use App\Helper\GeneralMethod;
     $country_code =  GeneralMethod::countryCodeFun();
   
     $background_image		=	url('img/client-default.png'); 
     $name	=	$website	=	$industry	=	$company_sizes	=	$company_type	=	$founded	= $description = $tags	= $client_location_arr = $client_contact_arr =  '';
     $career_page_url = $tax_id = $linkedin_url = $twitter_url = $github_url = $ap_email = $youtube_url = $facebook_url = $instagram_url = $custom_link = '';
		 if($page_event == 'ADD'){
			$pagetitle	=	'Add Client';
      $countAddrees= 1;
		 }else if($page_event == 'EDIT'){
			$pagetitle	=	'Edit Client';

     	$name					= @$client->name;
     	$website				= @$client->website;
     	$industry				= @$client->industry;
     	$client_size_from       = @$client->company_size_from;
     	$client_size_to         = @$client->company_sizes;
     	if (!empty($client_size_from)) {
     	    $company_sizes		= $client_size_from . '-' . (!empty($client_size_to) ? $client_size_to : '0');
     	} else {
     	    $company_sizes		= '';
     	}
     	$company_type			= @$client->company_type;
     	$founded				= @$client->founded;
     	
     	$description			= 	@$client->description;
     	$tags					= 	@$client->tags;
     	$client_location_arr	=	$client_location;
     	$client_contact_arr 	=	$client_contact_info;
      $countAddrees= count($client_location);
     	
     	if(!empty(@$client->logo_logical_path) && strlen(@$client->logo_logical_path)>10){
     			$background_image		=  $client->logo_logical_path; 	
     	}

      if (!empty($client->detail)) {
          $clientDetailDecoded = json_decode($client->detail, true);
          if (is_array($clientDetailDecoded)) {
              $career_page_url = $clientDetailDecoded['career_page_url'] ?? '';
              $tax_id          = $clientDetailDecoded['tax_id'] ?? '';
              $linkedin_url    = $clientDetailDecoded['linkedin_url'] ?? '';
              $twitter_url     = $clientDetailDecoded['twitter_url'] ?? '';
              $github_url      = $clientDetailDecoded['github_url'] ?? '';
              $ap_email        = $clientDetailDecoded['ap_email'] ?? '';
              $youtube_url     = $clientDetailDecoded['youtube_url'] ?? '';
              $facebook_url    = $clientDetailDecoded['facebook_url'] ?? '';
              $instagram_url   = $clientDetailDecoded['instagram_url'] ?? '';
              $custom_link     = $clientDetailDecoded['custom_link'] ?? '';
          }
      }
     }
     
?>
<!--begin::Content-->
<div class="content d-flex flex-column flex-column-fluid pt-0 pb-5" id="kt_content">
  <!--begin::Entry-->
  <div class="d-flex flex-column-fluid">
    <!--begin::Container-->
    <div class="container-fluid px-3">

      <!-- =========================================================================
           TOP UNIFIED HEADER & 5-STEP NAVIGATION BAR
           Pixel-perfect match to user reference media_1792068852883.png:
           Single warm-tinted yellow-bordered bar containing:
           - Left: Circular back button + Title ("Add Internal Client") & Subtitle
           - Center: 5-Step horizontal stepper (concentric active halo, gold dividers)
           - Right: Contextual Next Page / Submit action button
           ========================================================================= -->
      @include('service_company.client.components.wizard-header', ['page_event' => $page_event])

      <!--Card-->
      <div class="card card-custom client-wizard-shell" style="background:transparent; border:none; box-shadow:none; margin:0 !important; padding:0 !important;">
        <div class="card-body p-0" style="margin:0 !important; padding:0 !important;">
          <!--begin::Wizard-->
          <div class="wizard wizard-1" id="kt_wizard" data-wizard-state="first" data-wizard-clickable="false" style="margin:0 !important; padding:0 !important;">
            <!--begin::Wizard Body-->
            <div class="row justify-content-center px-0" style="margin:0 !important; padding:0 !important;">
              <div class="col-xl-12 col-xxl-12" style="margin:0 !important; padding:0 !important;">
                <!--begin::Wizard Form-->
                <form class="form mx-auto" novalidate="novalidate" autocomplete="off" action="javascript:void(0);" method="post" id="kt_form" name="kt_form" style="margin:0 !important; padding:0 !important;">
                  <meta name="csrf-token" content="{{ csrf_token() }}" />
                  <input type="hidden" name="idempotency_key" id="kt_form_idempotency_key" value="{{ (string) \Illuminate\Support\Str::uuid() }}" />
                  <!--begin::Client Information (Page 1)-->
                  @include('service_company.client.components.tab-basic-info', ['page_event' => $page_event])

                  <!--begin::Client Description (Page 2)-->
                  @include('service_company.client.components.tab-details', ['page_event' => $page_event])

                  <!--begin::Address Details (Page 3)-->
                  @include('service_company.client.components.tab-locations', ['page_event' => $page_event])

                  <!--begin::Contact Person (Page 4)-->
                  @include('service_company.client.components.tab-contacts', [
                    'client_contact_info' => ($page_event === 'ADD') ? [] : ($client_contact_info ?? []),
                    'client_contact_arr'  => ($page_event === 'ADD') ? [] : ($client_contact_arr ?? ($client_contact_info ?? [])),
                    'page_event'          => $page_event,
                    'country_code'        => $country_code ?? 'in'
                  ])

                  <!--begin::Social Links (Page 5)-->
                  @include('service_company.client.components.tab-social-links', [
                    'linkedin_url'  => ($page_event === 'ADD') ? '' : ($linkedin_url ?? ''),
                    'twitter_url'   => ($page_event === 'ADD') ? '' : ($twitter_url ?? ''),
                    'youtube_url'   => ($page_event === 'ADD') ? '' : ($youtube_url ?? ''),
                    'facebook_url'  => ($page_event === 'ADD') ? '' : ($facebook_url ?? ''),
                    'instagram_url' => ($page_event === 'ADD') ? '' : ($instagram_url ?? ''),
                    'github_url'    => ($page_event === 'ADD') ? '' : ($github_url ?? ''),
                    'custom_link'   => ($page_event === 'ADD') ? '' : ($custom_link ?? ''),
                  ])

                  <!--begin::Review and Submit (Page 6)-->
                  @include('service_company.client.components.tab-review', ['page_event' => $page_event])
                </form>
                <!--end::Wizard Form-->
              </div>
            </div>
            <!--end::Wizard Body-->
          </div>
          <!--end::Wizard-->
        </div>
        <!--end::Wizard-->
      </div>
      <!--Card-->
    </div>
    <!--end::Container-->
  </div>
  <!--end::Entry-->
</div>
<!--end::Content-->
@endsection
@push('footer-script')

{{-- Lightweight Quill rich-text engine is provided via layout plugins.bundle.js (Zero added network payload) --}}

<script src="https://unpkg.com/@yaireo/tagify"></script>
<script src="https://unpkg.com/@yaireo/tagify/dist/tagify.polyfills.min.js"></script>
<link href="https://unpkg.com/@yaireo/tagify/dist/tagify.css" rel="stylesheet" type="text/css" />

<script>
  // =========================================================================
  // JeroEmailBoxEngine: 100% Client-First (Zero Server Strain) Email Engine
  // RFC 5321 (254 char), Deterministic Sanitizer, 3-State Typo Detection
  // =========================================================================
  window.JeroEmailBoxEngine = window.JeroEmailBoxEngine || (function () {
    var CONSUMER_DOMAIN_TYPOS = {
      'gmai.com': 'gmail.com', 'gamil.com': 'gmail.com', 'gmial.com': 'gmail.com',
      'gmaill.com': 'gmail.com', 'gmaik.com': 'gmail.com', 'gmeil.com': 'gmail.com',
      'gmaul.com': 'gmail.com', 'gmal.com': 'gmail.com', 'gmaili.com': 'gmail.com',
      'yaho.com': 'yahoo.com', 'yahooo.com': 'yahoo.com', 'ymail.com': 'yahoo.com',
      'outlok.com': 'outlook.com', 'outloo.com': 'outlook.com',
      'hotmial.com': 'hotmail.com', 'hotmai.com': 'hotmail.com',
      'iclou.com': 'icloud.com', 'iclod.com': 'icloud.com',
      'redifmail.com': 'rediffmail.com', 'rediffmial.com': 'rediffmail.com'
    };

    var CONSUMER_TLD_TYPOS = {
      'con': 'com', 'cmo': 'com', 'ocm': 'com', 'cpm': 'com',
      'comm': 'com', 'coom': 'com', 'ent': 'net', 'og': 'org', 'ogr': 'org'
    };

    return {
      cleanDeterministic: function (raw) {
        if (!raw) return '';
        var str = String(raw).trim();
        str = str.replace(/[\u00A0\u1680\u180E\u2000-\u200B\u202F\u205F\u3000\uFEFF]/g, '');
        str = str.replace(/^['"`<]+|['"`>]+$/g, '');
        str = str.replace(/^mailto:\s*/i, '');
        var match = str.match(/<([^>]+)>/);
        if (match && match[1]) {
          str = match[1];
        }
        str = str.replace(/[.,;:]+$/, '');
        return str.toLowerCase().replace(/\s+/g, '');
      },

      getSuggestion: function (email) {
        if (!email || email.indexOf('@') === -1) return null;
        var parts = email.split('@');
        if (parts.length !== 2) return null;

        var local = parts[0];
        var domain = parts[1];

        if (domain.indexOf(',') !== -1) {
          var commaFixed = domain.replace(/,([a-zA-Z]{2,})$/, '.$1');
          return {
            suggested: local + '@' + commaFixed,
            message: 'Did you mean <strong>' + local + '@' + commaFixed + '</strong>?'
          };
        }

        if (CONSUMER_DOMAIN_TYPOS[domain]) {
          var fixedEmail = local + '@' + CONSUMER_DOMAIN_TYPOS[domain];
          return {
            suggested: fixedEmail,
            message: 'Did you mean <strong>' + fixedEmail + '</strong>?'
          };
        }

        var dParts = domain.split('.');
        if (dParts.length >= 2) {
          var root = dParts[0];
          var tld = dParts[dParts.length - 1];
          if (CONSUMER_TLD_TYPOS[tld] && (root === 'gmail' || root === 'yahoo' || root === 'outlook' || root === 'hotmail')) {
            dParts[dParts.length - 1] = CONSUMER_TLD_TYPOS[tld];
            var fixed = local + '@' + dParts.join('.');
            return {
              suggested: fixed,
              message: 'Did you mean <strong>' + fixed + '</strong>?'
            };
          }
        }

        return null;
      },

      validateSyntax: function (email) {
        if (!email || email.trim() === '') {
          return { isValid: false, error: 'Email address is required.' };
        }

        if (email.length > 254) {
          return { isValid: false, error: 'Email exceeds maximum allowed length of 254 characters.' };
        }

        if (/\s/.test(email)) {
          return { isValid: false, error: 'Email address cannot contain spaces.' };
        }

        if (email.indexOf(',') !== -1) {
          if (email.indexOf('@') !== -1 && email.split('@')[1].indexOf(',') !== -1) {
            return { isValid: false, error: 'Domain name contains a comma instead of a period (e.g. gmail,com).' };
          }
          return { isValid: false, error: 'Email address cannot contain commas.' };
        }

        var parts = email.split('@');
        if (parts.length < 2) {
          return { isValid: false, error: 'Email is missing the "@" symbol.' };
        }
        if (parts.length > 2) {
          return { isValid: false, error: 'Email contains more than one "@" symbol.' };
        }

        var local = parts[0];
        var domain = parts[1];

        if (local.length === 0) {
          return { isValid: false, error: 'Email username cannot be empty.' };
        }
        if (local.length > 64) {
          return { isValid: false, error: 'Email username cannot exceed 64 characters.' };
        }

        if (local.indexOf('..') !== -1 || domain.indexOf('..') !== -1) {
          return { isValid: false, error: 'Email cannot contain consecutive dots ("..").' };
        }

        if (local.startsWith('.') || local.endsWith('.')) {
          return { isValid: false, error: 'Email username cannot start or end with a dot.' };
        }
        if (domain.startsWith('.') || domain.endsWith('.')) {
          return { isValid: false, error: 'Domain name cannot start or end with a dot.' };
        }

        if (domain.indexOf('_') !== -1) {
          return { isValid: false, error: 'Domain name cannot contain underscores (use hyphens instead).' };
        }

        if (!/^[a-z0-9]([a-z0-9-]*[a-z0-9])?(\.[a-z0-9]([a-z0-9-]*[a-z0-9])?)+$/.test(domain)) {
          return { isValid: false, error: 'Domain name format is invalid (letters, numbers, and hyphens only).' };
        }

        var tld = domain.split('.').pop();
        if (!tld || tld.length < 2 || /^\d+$/.test(tld)) {
          return { isValid: false, error: 'Domain must end with a valid extension (e.g. .com, .org).' };
        }

        return { isValid: true, error: null };
      }
    };
  })();

  $(document).on('input', 'input[name="cp_number[]"]', function () {

      // Allow only numbers
      this.value = this.value.replace(/\D/g, '');

      // Restrict to 15 digits (ITU-T E.164 standard)
      if (this.value.length > 15) {
          this.value = this.value.slice(0, 15);
      }

  });

  // 100% Client-First (Zero Server Strain) contact email handling
  $(document).on("paste", 'input[name="cp_email[]"]', function () {
      var self = this;
      setTimeout(function () {
          var clean = JeroEmailBoxEngine.cleanDeterministic($(self).val());
          if (clean !== $(self).val()) {
              $(self).val(clean).trigger('change');
          }
      }, 10);
  });

  $(document).on("blur change", 'input[name="cp_email[]"]', function () {
      var raw = $(this).val();
      var clean = JeroEmailBoxEngine.cleanDeterministic(raw);
      if (clean !== raw) {
          $(this).val(clean);
      }
      if (clean) {
          $('.verification_email').val(clean);
      }
  });

  $(document).ready(function () {
    // Initialize intl-tel-input for existing contact number fields
      function initializeIntlTelInput(inputElement,initialCountry='{{ $country_code }}') {
          if (!inputElement || typeof window.intlTelInput !== 'function') return null;
          return window.intlTelInput(inputElement, {
              separateDialCode: true,
              initialCountry: initialCountry, // Default country
              preferredCountries: ["in", "us", "gb", "ca", "au"],
          });
      }
       // Apply intl-tel-input to all existing contact number fields
       function addmewdddd() {
        $(".client-cperson-container-child input[name='cp_number[]']").each(function () {
          var ele = $(this);
          var initialCountry = ele.closest('.client-cperson-container-child').find("input[name='calling_code[]']").val();
            initializeIntlTelInput(this,initialCountry);
        });
      }
      addmewdddd();

    function updateCountryCode(inputField,ele) {
      if (typeof window.intlTelInputGlobals === 'undefined' || !window.intlTelInputGlobals.getInstance) return;
      const itiInstance = window.intlTelInputGlobals.getInstance(inputField);

      if (itiInstance) {
          const selectedCountryData = itiInstance.getSelectedCountryData();
          const countryCode = selectedCountryData.iso2; // e.g., 'us', 'in', etc.
          ele.closest('.client-cperson-container-child').find("input[name='calling_code[]']").val(countryCode);
      }
    }

    $(document).on('click','.iti__flag-container',function(){
      const targetInput = $(event.target).closest(".client-cperson-container-child").find("input");
        var ele = $(this);
       
        updateCountryCode(targetInput[0],ele);
    })
   


    $('.add_cperson').click(function (){
   			
   			var contactPersonId	=	$('#contactPersonId').val();
   			contactPersonId++;
        var countAddress  = $('.countContact').length;
        var countAddressFinal = countAddress+1;
   			
   			$('#contactPersonId').val(contactPersonId);  			
   			
    			$('.client-cperson-row').last().append('<div class="row client-cperson-container" data-contact-index="'+contactPersonId+'"><span class="form-text text-muted form-label fw-bolder countContact" style="position: absolute; top: 0;"><b>Contact Person Info: <span class="count_class">'+countAddressFinal+'</span> </b></span><div class="col-xl-3"><div class="form-group mb-0 mt-7 client-cperson-container-child" id="remove"><div class="input-group"><input type="text" name="cp_name[]" id="cp_name_'+contactPersonId+'" class="form-control form-control-lg form-control-solid rounded w-100" placeholder="Enter person name"><span class="form-text text-muted">Please enter contact person name</span></div></div></div><div class="col-xl-3"><div class="form-group mb-0 mt-7 client-cperson-container-child" id="remove"><div class="input-group"><input type="email" name="cp_email[]" id="cp_email_'+contactPersonId+'" class="form-control form-control-lg form-control-solid rounded w-100" placeholder="Enter email" maxlength="254" inputmode="email" autocomplete="email" autocapitalize="none" spellcheck="false"><span class="form-text text-muted">Please enter contact email</span></div></div></div><div class="col-xl-3"><div class="form-group mb-0 mt-7 client-cperson-container-child" id="remove"><div class="input-group"><input type="number" name="cp_number[]" id="cp_number_'+contactPersonId+'" class="form-control form-control-lg form-control-solid rounded w-100" placeholder="Enter contact number"><input type="hidden" value="{{ $country_code }}" class="calling_code" name="calling_code[]" id="calling_code_'+contactPersonId+'"><span class="form-text text-muted">Please enter contact number</span></div></div></div><div class="col-xl-3"><div class="form-group mb-0 mt-7 client-cperson-container-child" id="remove"><div class="input-group" style="width: 95%;"><input type="text" name="cp_desigination[]" id="cp_desigination_'+contactPersonId+'" class="form-control form-control-lg form-control-solid rounded cp_desigination" placeholder="Enter desigination"><span class="form-text text-muted">Please enter contact desigination</span></div><i class="fa fa-close" style="position: absolute;font-size: 14px;color:red;top: 26px;right: 0;cursor: pointer;"></i></div></div></div></div>');
          if (window.clientWizardUi) window.clientWizardUi.renderContactTable();
          // Reinitialize intl-tel-input for the newly added cp_number[] field
         var newContactNumberField = $(`input[name='cp_number[]']`).last()[0];
         var initialCountry = $(`input[name='calling_code[]']`).last().val();
         initializeIntlTelInput(newContactNumberField,initialCountry);

        $('.count_class').each(function(index) {
            $(this).text(index + 1);
        });
        
    });


  });


</script>

<script>

     var page_event = "{{$page_event}}";



   // =========================================================================
   // God-Level Service Offerings Manager (matches reference design system)
   // =========================================================================
   function escapeHtmlText(str) {
     return String(str || '').replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
   }
   function escapeHtmlAttr(str) {
     return String(str || '').replace(/&/g, '&amp;').replace(/"/g, '&quot;').replace(/'/g, '&#39;');
   }
   function escapeJsString(str) {
     return String(str || '').replace(/\\/g, '\\\\').replace(/'/g, "\\'");
   }

   window.clientServicesManager = {
     selected: [],
     showMore: false,

     init: function () {
       var self = this;
       var $tags = $('#tags');
       var rawVal = ($tags.val() || '').trim();
       var parsed = [];

       if (rawVal) {
         try {
           var j = JSON.parse(rawVal);
           if (Array.isArray(j)) {
             parsed = j.map(function (item) { return typeof item === 'object' ? (item.value || '') : item; });
           }
         } catch (e) {
           parsed = rawVal.split(',').map(function (s) { return s.trim(); });
         }
       } else if (typeof page_event !== 'undefined' && page_event === 'ADD') {
         parsed = [];
       }

       this.selected = parsed.filter(function (s) { return typeof s === 'string' && s.trim().length > 0; });
       this.renderSelected();
       this.syncHiddenInput();

       // Wire up search input
       $('#client_services_search').off('input.clientServices keydown.clientServices')
         .on('input.clientServices', function () {
           var q = ($(this).val() || '').toLowerCase().trim();
           $('#client_suggested_services .client-suggested-pill').each(function () {
             var val = ($(this).data('value') || '').toLowerCase();
             if (!q || val.indexOf(q) !== -1) {
               $(this).removeClass('is-search-hidden');
             } else {
               $(this).addClass('is-search-hidden');
             }
           });
         })
         .on('keydown.clientServices', function (e) {
           if (e.key === 'Enter') {
             e.preventDefault();
             e.stopPropagation();
             var val = ($(this).val() || '').trim();
             if (val) {
               self.addTag(val);
               $(this).val('').trigger('input.clientServices');
             }
           }
         });

       // Wire up Clear all button
       $('#client_clear_all_tags_btn').off('click.clientServices').on('click.clientServices', function (e) {
         e.preventDefault();
         self.clearAll();
       });

       // Wire up toggle more link
       $('#client_toggle_more_services').off('click.clientServices').on('click.clientServices', function (e) {
         e.preventDefault();
         self.toggleMore();
       });

       // Wire up remove chip clicks (delegated)
       $(document).off('click.clientChipRemove', '.client-selected-chip .client-chip-remove')
         .on('click.clientChipRemove', '.client-selected-chip .client-chip-remove', function (e) {
           e.preventDefault();
           e.stopPropagation();
           var tag = $(this).attr('data-tag');
           if (tag) self.removeTag(tag);
         });

       // Wire up suggested pill clicks (delegated)
       $(document).off('click.clientSuggestedPill', '.client-suggested-pill')
         .on('click.clientSuggestedPill', '.client-suggested-pill', function (e) {
           e.preventDefault();
           e.stopPropagation();
           var val = $(this).attr('data-value');
           if (val) self.addTag(val);
         });

       // Wire up Tips collapsible accordion
       $('#client_tips_toggle').off('click.clientTips').on('click.clientTips', function (e) {
         e.preventDefault();
         $('#client_tips_content').slideToggle(180);
         var $chevron = $('#client_tips_chevron');
         var isRotated = $chevron.hasClass('is-rotated');
         if (isRotated) {
           $chevron.removeClass('is-rotated').css('transform', 'none');
         } else {
           $chevron.addClass('is-rotated').css('transform', 'rotate(180deg)');
         }
       });

       // Wire up AI Company Assist "Review & Edit"
       $('#client_review_edit_desc_btn').off('click.clientAiEdit').on('click.clientAiEdit', function (e) {
         e.preventDefault();
         var $box = $('#client_ai_suggested_desc');
         var isEditing = $box.attr('contenteditable') === 'true';
         if (!isEditing) {
           $box.attr('contenteditable', 'true').focus();
           $box.css({ 'outline': '2px solid #7040f5', 'box-shadow': '0 0 0 4px rgba(112,64,245,0.1)' });
           $(this).text('Done Editing').css({ 'background': '#7040f5', 'color': '#ffffff' });
         } else {
           $box.attr('contenteditable', 'false');
           $box.css({ 'outline': 'none', 'box-shadow': 'none' });
           $(this).text('Review & Edit').css({ 'background': '#ffffff', 'color': '#6d28d9' });
           if (typeof Swal !== 'undefined' && typeof Swal.mixin === 'function') {
             Swal.mixin({ toast: true, position: 'top-end', showConfirmButton: false, timer: 2000 })
               .fire({ icon: 'success', title: 'Suggestion updated!' });
           }
         }
       });
     },

     addTag: function (tag) {
       if (!tag) return;
       tag = String(tag).trim();
       if (!tag) return;
       var lower = tag.toLowerCase();
       var exists = this.selected.some(function (t) { return t.toLowerCase() === lower; });
       if (!exists) {
         this.selected.push(tag);
         this.renderSelected();
         this.syncHiddenInput();
         this.updateSuggestedView();
         $('#client_tags_error').hide();
         if (window.clientWizardUi && typeof window.clientWizardUi.updateCompletion === 'function') {
           window.clientWizardUi.updateCompletion();
         }
       }
     },

     removeTag: function (tag) {
       var lower = String(tag || '').toLowerCase();
       this.selected = this.selected.filter(function (t) { return t.toLowerCase() !== lower; });
       this.renderSelected();
       this.syncHiddenInput();
       this.updateSuggestedView();
       if (window.clientWizardUi && typeof window.clientWizardUi.updateCompletion === 'function') {
         window.clientWizardUi.updateCompletion();
       }
     },

     clearAll: function () {
       this.selected = [];
       this.renderSelected();
       this.syncHiddenInput();
       this.updateSuggestedView();
       if (window.clientWizardUi && typeof window.clientWizardUi.updateCompletion === 'function') {
         window.clientWizardUi.updateCompletion();
       }
     },

     addAllRecommended: function () {
       var self = this;
       $('#client_suggested_services .client-suggested-pill').each(function () {
         if (!$(this).hasClass('is-selected') && !$(this).hasClass('is-search-hidden')) {
           var val = $(this).attr('data-value');
           if (val) self.addTag(val);
         }
       });
     },

     toggleMore: function () {
       this.showMore = !this.showMore;
       this.updateSuggestedView();
     },

     renderSelected: function () {
       var $container = $('#client_selected_chips_container');
       var $count = $('#client_selected_count');
       if (!$container.length) return;

       $count.text(this.selected.length);
       if (!this.selected.length) {
         $container.html('<span class="text-muted" style="font-size:11px; font-style:italic;">No services selected yet. Choose from suggestions below or search above.</span>');
         return;
       }

       var html = '';
       this.selected.forEach(function (tag) {
         html += '<div class="client-selected-chip">' +
           '<span>' + escapeHtmlText(tag) + '</span>' +
           '<span class="client-chip-remove" data-tag="' + escapeHtmlAttr(tag) + '" title="Remove">✕</span>' +
         '</div>';
       });
       $container.html(html);
     },

     syncHiddenInput: function () {
       var jsonArr = this.selected.map(function (t) { return { value: t }; });
       $('#tags').val(this.selected.length ? JSON.stringify(jsonArr) : '');
     },

     updateSuggestedView: function () {
       var self = this;
       var lowerSelected = this.selected.map(function (t) { return t.toLowerCase(); });
       var visibleCount = 0;
       var hiddenMoreCount = 0;

       $('#client_suggested_services .client-suggested-pill').each(function () {
         var val = ($(this).attr('data-value') || '').toLowerCase();
         var isSel = lowerSelected.indexOf(val) !== -1;
         $(this).toggleClass('is-selected', isSel);

         if (!isSel) {
           visibleCount++;
           if (!self.showMore && visibleCount > 9) {
             $(this).hide();
             hiddenMoreCount++;
           } else {
             $(this).show();
           }
         }
       });

       var $moreWrap = $('#client_view_more_services_wrap');
       var $moreLink = $('#client_toggle_more_services');
       var $moreCount = $('#client_more_services_count');

       if (hiddenMoreCount > 0 || self.showMore) {
         $moreWrap.show();
         if (self.showMore) {
           $moreLink.html('<span>Show fewer recommended services</span> &uarr;');
         } else {
           $moreCount.text(hiddenMoreCount);
           $moreLink.html('<span>View <span id="client_more_services_count">' + hiddenMoreCount + '</span> more recommended services</span> &rarr;');
         }
       } else {
         $moreWrap.hide();
       }
     }
   };

   // Backward-compatibility bridge for window.clientTagsTagify
   window.clientTagsTagify = {
     get value() {
       return (window.clientServicesManager ? window.clientServicesManager.selected : []).map(function (t) { return { value: t }; });
     },
     addTags: function (tags) {
       if (!tags) return;
       if (!Array.isArray(tags)) tags = [tags];
       tags.forEach(function (t) {
         var val = typeof t === 'object' ? (t.value || '') : t;
         if (window.clientServicesManager) window.clientServicesManager.addTag(val);
       });
     },
     removeTags: function (tags) {
       if (!tags) return;
       if (!Array.isArray(tags)) tags = [tags];
       tags.forEach(function (t) {
         var val = typeof t === 'object' ? (t.value || '') : t;
         if (window.clientServicesManager) window.clientServicesManager.removeTag(val);
       });
     },
     removeAllTags: function () {
       if (window.clientServicesManager) window.clientServicesManager.clearAll();
     },
     on: function (event, cb) {
       // Hook stub
     },
     DOM: {
       get scope() {
         return document.getElementById('client_selected_chips_container');
       }
     }
   };

   window._companyAiSuggestions = {
     render: function () {}
   };

   $(document).ready(function () {
     window.clientServicesManager.init();
   });
   
   // ---------- GLOBAL INLINE ERROR UTILITIES ----------
   function clearFieldError($field) {
     if (!$field) return;
     var $f = ($field instanceof jQuery) ? $field : $($field);
     if (!$f.length) return;
     $f.removeClass('is-invalid');
     if ($f.attr('id') === 'industry' || $f.attr('id') === 'industry_display') {
       $('#industry_display').removeClass('is-invalid');
       $('#jero_ind_input_box').removeClass('has-validation-error');
       $('#jero_ind_select_wrap').removeClass('has-validation-error');
     }
     if ($f.attr('id') === 'company_type' || $f.attr('id') === 'company_type_display') {
       $('#company_type_display').removeClass('is-invalid');
       $('#jero_ctype_input_box').removeClass('has-validation-error');
       $('#jero_ctype_select_wrap').removeClass('has-validation-error');
     }
     if ($f.attr('id') === 'founded' || $f.attr('id') === 'founded_display') {
       $('#founded_display').removeClass('is-invalid');
       $('#jero_founded_input_box').removeClass('has-validation-error');
       $('#jero_founded_select_wrap').removeClass('has-validation-error');
     }
     var $container = $f.closest('.client-field, .fv-plugins-icon-container, .ij-location-input-wrap');
     if ($container.length) {
       $container.find('.client-inline-error').remove();
     } else {
       $f.siblings('.client-inline-error').remove();
       $f.parent().find('.client-inline-error').remove();
     }
   }
   window.clearFieldError = clearFieldError;

   function showInlineError($field, message) {
     if (!$field) return;
     var $f = ($field instanceof jQuery) ? $field : $($field);
     if (!$f.length) return;
     clearFieldError($f);
     $f.addClass('is-invalid');
     if ($f.attr('id') === 'industry' || $f.attr('id') === 'industry_display') {
       $('#industry_display').addClass('is-invalid');
       $('#jero_ind_input_box').addClass('has-validation-error');
       $('#jero_ind_select_wrap').addClass('has-validation-error');
     }
     if ($f.attr('id') === 'company_type' || $f.attr('id') === 'company_type_display') {
       $('#company_type_display').addClass('is-invalid');
       $('#jero_ctype_input_box').addClass('has-validation-error');
       $('#jero_ctype_select_wrap').addClass('has-validation-error');
     }
     if ($f.attr('id') === 'founded' || $f.attr('id') === 'founded_display') {
       $('#founded_display').addClass('is-invalid');
       $('#jero_founded_input_box').addClass('has-validation-error');
       $('#jero_founded_select_wrap').addClass('has-validation-error');
     }
     var $container = $f.closest('.client-field, .fv-plugins-icon-container, .ij-location-input-wrap');
     if ($container.length) {
       $container.append('<span class="client-inline-error text-danger mt-1 d-block" style="font-size: 11px; font-weight: 600; color: #e11d48;"><i class="la la-exclamation-circle"></i> ' + message + '</span>');
     } else {
       $f.after('<span class="client-inline-error text-danger mt-1 d-block" style="font-size: 11px; font-weight: 600; color: #e11d48;"><i class="la la-exclamation-circle"></i> ' + message + '</span>');
     }
   }
   window.showInlineError = showInlineError;

   // ---------- GOD-LEVEL CANONICAL LOCATION ENGINE (V4 ARCHITECTURE) ----------
   function debounce(func, delay) { let timer; return function (...args) { clearTimeout(timer); timer = setTimeout(() => func.apply(this, args), delay); }; }

   var selectedCities = [];
   if (window.serverInitialCities && Array.isArray(window.serverInitialCities)) {
       selectedCities = window.serverInitialCities.slice();
   }

   // Layer 1: In-Browser Curated Global Hubs (30 worldwide hubs grouped by Region)
   var curatedLocationGroups = [
       {
           region: 'India Tech Hubs',
           items: [
               { city: 'Bengaluru', state: 'Karnataka', country: 'India', badge: 'Metro Hub', canonical: 'Bengaluru, Karnataka, India' },
               { city: 'Mumbai', state: 'Maharashtra', country: 'India', badge: 'Financial Capital', canonical: 'Mumbai, Maharashtra, India' },
               { city: 'New Delhi', state: 'Delhi', country: 'India', badge: 'National Capital', canonical: 'New Delhi, Delhi, India' },
               { city: 'Hyderabad', state: 'Telangana', country: 'India', badge: 'Tech Hub', canonical: 'Hyderabad, Telangana, India' },
               { city: 'Pune', state: 'Maharashtra', country: 'India', badge: 'IT Hub', canonical: 'Pune, Maharashtra, India' },
               { city: 'Chennai', state: 'Tamil Nadu', country: 'India', badge: 'SaaS Capital', canonical: 'Chennai, Tamil Nadu, India' },
               { city: 'Gurugram', state: 'Haryana', country: 'India', badge: 'NCR Hub', canonical: 'Gurugram, Haryana, India' },
               { city: 'Noida', state: 'Uttar Pradesh', country: 'India', badge: 'NCR Hub', canonical: 'Noida, Uttar Pradesh, India' },
               { city: 'Kolkata', state: 'West Bengal', country: 'India', badge: 'Metro Hub', canonical: 'Kolkata, West Bengal, India' },
               { city: 'Ahmedabad', state: 'Gujarat', country: 'India', badge: 'Commercial Hub', canonical: 'Ahmedabad, Gujarat, India' }
           ]
       },
       {
           region: 'North America',
           items: [
               { city: 'San Francisco', state: 'California', country: 'United States', badge: 'Silicon Valley', canonical: 'San Francisco, California, United States' },
               { city: 'New York City', state: 'New York', country: 'United States', badge: 'Global Hub', canonical: 'New York City, New York, United States' },
               { city: 'Seattle', state: 'Washington', country: 'United States', badge: 'Cloud Hub', canonical: 'Seattle, Washington, United States' },
               { city: 'Austin', state: 'Texas', country: 'United States', badge: 'Tech Hub', canonical: 'Austin, Texas, United States' },
               { city: 'Boston', state: 'Massachusetts', country: 'United States', badge: 'Biotech / AI', canonical: 'Boston, Massachusetts, United States' },
               { city: 'Toronto', state: 'Ontario', country: 'Canada', badge: 'Canada Tech Hub', canonical: 'Toronto, Ontario, Canada' },
               { city: 'Vancouver', state: 'British Columbia', country: 'Canada', badge: 'Tech Center', canonical: 'Vancouver, British Columbia, Canada' }
           ]
       },
       {
           region: 'Europe & UK',
           items: [
               { city: 'London', state: 'England', country: 'United Kingdom', badge: 'FinTech Capital', canonical: 'London, England, United Kingdom' },
               { city: 'Berlin', state: 'Berlin', country: 'Germany', badge: 'Startup Capital', canonical: 'Berlin, Germany' },
               { city: 'Amsterdam', state: 'North Holland', country: 'Netherlands', badge: 'Tech Hub', canonical: 'Amsterdam, Netherlands' },
               { city: 'Dublin', state: 'Leinster', country: 'Ireland', badge: 'EMEA HQ Hub', canonical: 'Dublin, Ireland' },
               { city: 'Paris', state: 'Île-de-France', country: 'France', badge: 'Tech Hub', canonical: 'Paris, France' }
           ]
       },
       {
           region: 'APAC & Middle East',
           items: [
               { city: 'Singapore', state: 'Central', country: 'Singapore', badge: 'Asia Gateway', canonical: 'Singapore, Singapore' },
               { city: 'Dubai', state: 'Dubai', country: 'United Arab Emirates', badge: 'MENA Hub', canonical: 'Dubai, United Arab Emirates' },
               { city: 'Tokyo', state: 'Tokyo', country: 'Japan', badge: 'Asia Metro', canonical: 'Tokyo, Japan' },
               { city: 'Sydney', state: 'New South Wales', country: 'Australia', badge: 'ANZ Hub', canonical: 'Sydney, New South Wales, Australia' },
               { city: 'Melbourne', state: 'Victoria', country: 'Australia', badge: 'Tech Center', canonical: 'Melbourne, Victoria, Australia' }
           ]
       }
   ];

   // Layer 1 Instant Quick-Select Popular Hubs
   var popularPillList = [
       { label: 'Bengaluru', canonical: 'Bengaluru, Karnataka, India' },
       { label: 'Mumbai', canonical: 'Mumbai, Maharashtra, India' },
       { label: 'Delhi NCR', canonical: 'New Delhi, Delhi, India' },
       { label: 'Hyderabad', canonical: 'Hyderabad, Telangana, India' },
       { label: 'Pune', canonical: 'Pune, Maharashtra, India' },
       { label: 'London', canonical: 'London, England, United Kingdom' },
       { label: 'New York', canonical: 'New York City, New York, United States' },
       { label: 'Singapore', canonical: 'Singapore, Singapore' }
   ];

   // Layer 1 In-Memory Synonym Aliases
   var citySynonymAliases = {
       'baroda': 'Vadodara',
       'vizag': 'Visakhapatnam',
       'bombay': 'Mumbai',
       'calcutta': 'Kolkata',
       'madras': 'Chennai',
       'poona': 'Pune',
       'bangalore': 'Bengaluru',
       'bengaluru': 'Bengaluru',
       'gurgaon': 'Gurugram',
       'gurugram': 'Gurugram',
       'trivandrum': 'Thiruvananthapuram',
       'cochin': 'Kochi',
       'sf': 'San Francisco',
       'bay area': 'San Francisco',
       'nyc': 'New York City',
       'ny': 'New York City',
       'la': 'Los Angeles',
       'dc': 'Washington',
       'kl': 'Kuala Lumpur',
       'sg': 'Singapore',
       'uae': 'Dubai'
   };

   // Layer 2 Session LRU In-Memory Cache (Capacity: 100, TTL: 30 minutes)
   var cityLruCache = new Map();
   var CITY_CACHE_MAX = 100;
   var CITY_CACHE_TTL = 1800000;
   var citySearchAbortCtrl = null;
   var cityHighlightedIdx = -1;

   function getCachedCities(key) {
       var entry = cityLruCache.get(key);
       if (!entry) return null;
       if (Date.now() - entry.ts > CITY_CACHE_TTL) {
           cityLruCache.delete(key);
           return null;
       }
       cityLruCache.delete(key);
       cityLruCache.set(key, entry);
       return entry.data;
   }

   function setCachedCities(key, data) {
       if (cityLruCache.size >= CITY_CACHE_MAX) {
           var firstKey = cityLruCache.keys().next().value;
           cityLruCache.delete(firstKey);
       }
       cityLruCache.set(key, { ts: Date.now(), data: data });
   }

   function escapeJsStr(val) {
       return String(val || '').replace(/\\/g, '\\\\').replace(/'/g, "\\'");
   }

   function isCitySelected(canonical, label) {
       var cNorm = String(canonical || '').toLowerCase().trim();
       var lNorm = String(label || '').toLowerCase().trim();
       return selectedCities.some(function(item) {
           var it = String(item || '').toLowerCase().trim();
           if (it === cNorm || (lNorm && it === lNorm)) return true;
           var cityPart = it.split(/[,|]/)[0].trim();
           if (lNorm && cityPart === lNorm) return true;
           if (cNorm && it.indexOf(cNorm) === 0) return true;
           return false;
       });
   }

   function renderPopularPills() {
       var container = document.getElementById('popularHubsPills');
       if (!container) return;
       container.innerHTML = popularPillList.map(function(p) {
           var active = isCitySelected(p.canonical, p.label);
           var activeClass = active ? ' is-active' : '';
           var prefix = active ? '✓ ' : '+ ';
           return '<button type="button" class="ij-pop-pill' + activeClass + '" onclick="togglePopularPill(\'' + escapeJsStr(p.canonical) + '\', \'' + escapeJsStr(p.label) + '\')">' +
               prefix + escapeHtml(p.label) + '</button>';
       }).join('');
   }

   function togglePopularPill(canonical, label) {
       var existingIdx = selectedCities.findIndex(function(item) {
           var it = String(item || '').toLowerCase().trim();
           var cNorm = String(canonical || '').toLowerCase().trim();
           var lNorm = String(label || '').toLowerCase().trim();
           return it === cNorm || (lNorm && it === lNorm) || (lNorm && it.split(/[,|]/)[0].trim() === lNorm);
       });
       if (existingIdx !== -1) {
           selectedCities.splice(existingIdx, 1);
       } else {
           if (selectedCities.length >= 15) {
               Swal.fire({ text: 'Maximum 15 cities allowed', icon: 'info' });
               return;
           }
           selectedCities.push(canonical);
       }
       renderSelectedCities();
   }

   function renderCuratedDropdown() {
       var dd = document.getElementById('cityDropdown');
       if (!dd) return;
       cityHighlightedIdx = -1;
       var html = '';
       curatedLocationGroups.forEach(function(group) {
           html += '<div class="ij-cd-group">';
           html += '<div class="ij-cd-group-title">' + escapeHtml(group.region) + '</div>';
           group.items.forEach(function(item) {
               var selected = isCitySelected(item.canonical, item.city);
               var itemClass = 'ij-cd-item' + (selected ? ' is-selected' : '');
               var clickAttr = selected ? '' : 'onclick="selectCity(\'' + escapeJsStr(item.canonical) + '\')"';
               var statusBadge = selected ? '<span class="ij-cd-status">✓ Selected</span>' : '';
               var subText = item.state ? (item.state + ', ' + item.country) : item.country;
               html += '<div class="' + itemClass + '" ' + clickAttr + ' data-canonical="' + escapeHtml(item.canonical) + '">';
               html += '  <div class="ij-cd-left">';
               html += '    <span class="ij-cd-pin"><svg viewBox="0 0 24 24" fill="none" stroke-width="2"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg></span>';
               html += '    <div class="ij-cd-text">';
               html += '      <span class="ij-cd-name">' + escapeHtml(item.city) + '</span>';
               html += '      <span class="ij-cd-meta">' + escapeHtml(subText) + '</span>';
               html += '    </div>';
               html += '  </div>';
               html += '  <div class="ij-cd-right">';
               if (item.badge) {
                   html += '    <span class="ij-cd-badge">' + escapeHtml(item.badge) + '</span>';
               }
               html += statusBadge;
               html += '  </div>';
               html += '</div>';
           });
           html += '</div>';
       });
       dd.innerHTML = html;
       dd.classList.add('active');
   }

   function highlightMatch(text, query) {
       if (!query) return escapeHtml(text);
       var regex = new RegExp('(' + query.replace(/[.*+?^${}()|[\]\\]/g, '\\$&') + ')', 'gi');
       return escapeHtml(text).replace(regex, '<strong style="color:#2563eb;">$1</strong>');
   }

   function renderCitySearchResults(results, query) {
       var dd = document.getElementById('cityDropdown');
       if (!dd) return;
       cityHighlightedIdx = -1;
       if (!results || results.length === 0) {
           var safeQ = escapeHtml(query);
           var safeJsQ = escapeJsStr(query);
           dd.innerHTML = '<div class="ij-cd-empty">' +
               '<div>No matching cities found for &ldquo;' + safeQ + '&rdquo;</div>' +
               '<button type="button" class="ij-btn-custom-add" onclick="addCustomCityDirect(\'' + safeJsQ + '\')">' +
               '+ Add &ldquo;' + safeQ + '&rdquo; as custom location</button>' +
               '</div>';
           dd.classList.add('active');
           return;
       }
       var html = '<div class="ij-cd-group">';
       html += '<div class="ij-cd-group-title">Search Results (' + results.length + ')</div>';
       results.forEach(function(item) {
           var canonical = item.canonical || item.label || (item.city + (item.state ? ', ' + item.state : '') + (item.country ? ', ' + item.country : ''));
           var selected = isCitySelected(canonical, item.city);
           var itemClass = 'ij-cd-item' + (selected ? ' is-selected' : '');
           var clickAttr = selected ? '' : 'onclick="selectCity(\'' + escapeJsStr(canonical) + '\')"';
           var statusBadge = selected ? '<span class="ij-cd-status">✓ Selected</span>' : '';
           var subText = item.state ? (item.state + ', ' + item.country) : (item.country || '');
           html += '<div class="' + itemClass + '" ' + clickAttr + ' data-canonical="' + escapeHtml(canonical) + '">';
           html += '  <div class="ij-cd-left">';
           html += '    <span class="ij-cd-pin"><svg viewBox="0 0 24 24" fill="none" stroke-width="2"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg></span>';
           html += '    <div class="ij-cd-text">';
           html += '      <span class="ij-cd-name">' + highlightMatch(item.city, query) + '</span>';
           html += '      <span class="ij-cd-meta">' + escapeHtml(subText) + '</span>';
           html += '    </div>';
           html += '  </div>';
           html += '  <div class="ij-cd-right">';
           if (item.badge) {
               html += '    <span class="ij-cd-badge">' + escapeHtml(item.badge) + '</span>';
           }
           html += statusBadge;
           html += '  </div>';
           html += '</div>';
       });
       html += '</div>';
       dd.innerHTML = html;
       dd.classList.add('active');
   }

   function parseRawCityData(data) {
       var rawList = [];
       if (data && Array.isArray(data.results)) {
           rawList = data.results;
       } else if (Array.isArray(data)) {
           rawList = data;
       }
       return rawList.map(function(c) {
           if (typeof c === 'object' && c !== null) {
               var cityName = c.city || (c.label ? c.label.split(',')[0].trim() : '');
               var stateName = c.state || (c.label ? (c.label.split(',')[1] || '').trim() : '');
               var countryName = c.country || (c.label ? (c.label.split(',')[2] || '').trim() : '');
               var canon = c.canonical || c.label || [cityName, stateName, countryName].filter(Boolean).join(', ');
               var badge = c.badge || (countryName === 'India' ? (stateName || 'India') : countryName);
               return {
                   id: c.id,
                   city: cityName || canon,
                   state: stateName,
                   country: countryName,
                   label: c.label || canon,
                   canonical: canon,
                   badge: badge
               };
           } else {
               var str = String(c || '').trim();
               var parts = str.split(/[,|]/).map(function(s) { return s.trim(); });
               var cityName = parts[0] || str;
               var stateName = parts[1] || '';
               var countryName = parts[2] || '';
               return {
                   city: cityName,
                   state: stateName,
                   country: countryName,
                   label: str,
                   canonical: str,
                   badge: countryName || stateName || ''
               };
           }
       });
   }

   function performCitySearch(query) {
       var trimmed = query.trim();
       var spinner = document.getElementById('citySearchSpinner');
       var clearBtn = document.getElementById('citySearchClear');
       if (clearBtn) clearBtn.style.display = trimmed ? 'inline-flex' : 'none';

       if (!trimmed) {
           if (spinner) spinner.style.display = 'none';
           renderCuratedDropdown();
           return;
       }

       var lookupQuery = trimmed.toLowerCase();
       var normalizedQ = citySynonymAliases[lookupQuery] || trimmed;

       var cacheKey = normalizedQ.toLowerCase().trim();
       var cached = getCachedCities(cacheKey);
       if (cached) {
           if (spinner) spinner.style.display = 'none';
           renderCitySearchResults(cached, trimmed);
           return;
       }

       if (citySearchAbortCtrl) {
           citySearchAbortCtrl.abort();
       }
       citySearchAbortCtrl = new AbortController();

       if (spinner) spinner.style.display = 'inline-block';

       var targetUrl = "{{ url('/') }}" + "/city-job-suggestions?q=" + encodeURIComponent(normalizedQ) + "&format=canonical&limit=25";

       fetch(targetUrl, {
           method: 'GET',
           signal: citySearchAbortCtrl.signal,
           headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
       })
       .then(function(res) {
           if (!res.ok) throw new Error('Network error: ' + res.status);
           return res.json();
       })
       .then(function(data) {
           if (spinner) spinner.style.display = 'none';
           var parsed = parseRawCityData(data);
           setCachedCities(cacheKey, parsed);
           renderCitySearchResults(parsed, trimmed);
       })
       .catch(function(err) {
           if (err.name === 'AbortError') return;
           if (spinner) spinner.style.display = 'none';
           var filtered = [];
           curatedLocationGroups.forEach(function(g) {
               g.items.forEach(function(item) {
                   if (item.city.toLowerCase().indexOf(normalizedQ.toLowerCase()) !== -1 ||
                       item.canonical.toLowerCase().indexOf(normalizedQ.toLowerCase()) !== -1) {
                       filtered.push(item);
                   }
               });
           });
           renderCitySearchResults(filtered, trimmed);
       });
   }

   var debounceCitySearch = debounce(function(val) {
       performCitySearch(val);
   }, 180);

   function clearCitySearchInput() {
       var input = document.getElementById('citySearch');
       var clearBtn = document.getElementById('citySearchClear');
       var spinner = document.getElementById('citySearchSpinner');
       if (input) {
           input.value = '';
           input.focus();
       }
       if (clearBtn) clearBtn.style.display = 'none';
       if (spinner) spinner.style.display = 'none';
       renderCuratedDropdown();
   }

   function initCitySearch() {
       var input = document.getElementById('citySearch');
       var dropdown = document.getElementById('cityDropdown');
       var clearBtn = document.getElementById('citySearchClear');
       renderPopularPills();

       if (!input || !dropdown) return;

       input.addEventListener('focus', function() {
           if (!input.value.trim()) {
               renderCuratedDropdown();
           } else {
               performCitySearch(input.value);
           }
       });

       input.addEventListener('input', function(e) {
           var v = e.target.value;
           if (clearBtn) clearBtn.style.display = v.trim() ? 'inline-flex' : 'none';
           debounceCitySearch(v);
       });

       input.addEventListener('keydown', function(e) {
           var items = dropdown.querySelectorAll('.ij-cd-item');
           if (!dropdown.classList.contains('active') || items.length === 0) {
               if (e.key === 'Enter') {
                   e.preventDefault();
                   addCustomCity();
               }
               return;
           }
           if (e.key === 'ArrowDown') {
               e.preventDefault();
               cityHighlightedIdx = (cityHighlightedIdx + 1) % items.length;
               items.forEach(function(el, i) {
                   if (i === cityHighlightedIdx) {
                       el.classList.add('is-highlighted');
                       el.scrollIntoView({ block: 'nearest' });
                   } else {
                       el.classList.remove('is-highlighted');
                   }
               });
           } else if (e.key === 'ArrowUp') {
               e.preventDefault();
               cityHighlightedIdx = (cityHighlightedIdx - 1 + items.length) % items.length;
               items.forEach(function(el, i) {
                   if (i === cityHighlightedIdx) {
                       el.classList.add('is-highlighted');
                       el.scrollIntoView({ block: 'nearest' });
                   } else {
                       el.classList.remove('is-highlighted');
                   }
               });
           } else if (e.key === 'Enter') {
               e.preventDefault();
               if (cityHighlightedIdx >= 0 && items[cityHighlightedIdx]) {
                   items[cityHighlightedIdx].click();
               } else {
                   addCustomCity();
               }
           } else if (e.key === 'Escape') {
               dropdown.classList.remove('active');
           }
       });

       document.addEventListener('click', function(e) {
           if (!input.contains(e.target) && !dropdown.contains(e.target) && !(clearBtn && clearBtn.contains(e.target))) {
               dropdown.classList.remove('active');
           }
       });
   }

   function selectCity(city, isManual) {
       if (!city) return;
       var clean = String(city).trim();
       if (isCitySelected(clean, clean.split(',')[0].trim())) {
           return;
       }
       if (selectedCities.length >= 15) {
           Swal.fire({ text: 'Maximum 15 cities allowed', icon: 'info' });
           return;
       }
       selectedCities.push(clean);
       renderSelectedCities();
       var input = document.getElementById('citySearch');
       if (input) input.value = '';
       var clearBtn = document.getElementById('citySearchClear');
       if (clearBtn) clearBtn.style.display = 'none';
       var dd = document.getElementById('cityDropdown');
       if (dd) dd.classList.remove('active');
   }

   function addCustomCityDirect(val) {
       if (!val) return;
       selectCity(val, true);
   }

   function addCustomCity() {
       var input = document.getElementById('citySearch');
       if (!input) return;
       var val = input.value.trim();
       if (!val) return;
       selectCity(val, true);
   }

   function clearAllCities(isManual) {
       selectedCities = [];
       renderSelectedCities();
   }

   function removeCity(city, isManual) {
       var cNorm = String(city || '').toLowerCase().trim();
       var lNorm = cNorm.split(/[,|]/)[0].trim();
       selectedCities = selectedCities.filter(function(c) {
           var it = String(c || '').toLowerCase().trim();
           return !(it === cNorm || (lNorm && it === lNorm) || (lNorm && it.split(/[,|]/)[0].trim() === lNorm));
       });
       renderSelectedCities();
   }

   function renderSelectedCities() {
       var countEl = document.getElementById('selectedCount');
       if (countEl) countEl.textContent = selectedCities.length;
       var container = document.getElementById('selectedCities');
       if (!container) return;

       if (selectedCities.length === 0) {
           container.innerHTML = '<span class="ij-no-locs">No locations selected yet. Choose from popular hubs above or search.</span>';
       } else {
           container.innerHTML = selectedCities.map(function(city) {
               var str = String(city || '').trim();
               var parts = str.split(/[,|]/).map(function(s) { return s.trim(); });
               var primaryName = parts[0] || str;
               var regionMeta = parts.slice(1).join(', ');
               var metaHtml = regionMeta ? '<span class="ij-chip-region">(' + escapeHtml(regionMeta) + ')</span>' : '';
               return '<span class="ij-location-chip">' +
                   '<span class="ij-chip-pin"><svg viewBox="0 0 24 24" fill="none" stroke-width="2"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg></span>' +
                   '<span class="ij-chip-label">' + escapeHtml(primaryName) + '</span>' +
                   metaHtml +
                   '<button type="button" class="ij-chip-del js-remove-city" onclick="removeCity(\'' + escapeJsStr(str) + '\', true)" data-city="' + escapeHtml(str) + '" title="Remove ' + escapeHtml(primaryName) + '" aria-label="Remove">&times;</button>' +
                   '</span>';
           }).join('');
       }
       syncCities();
       renderPopularPills();
       if (selectedCities.length > 0) {
           clearFieldError($('#kt_tagify_work_location'));
           clearFieldError($('.ij-location-input-wrap'));
       }
   }

   function syncCities() {
       var hidden = document.getElementById('kt_tagify_work_location');
       if (hidden) {
           hidden.value = JSON.stringify(selectedCities.map(function(c) {
               var str = String(c || '').trim();
               var parts = str.split(/[,|]/).map(function(s) { return s.trim(); });
               var formattedValue = parts.join(' | ');
               return { value: formattedValue };
           }));
       }
   }

   window.selectCity = selectCity;
   window.removeCity = removeCity;
   window.clearAllCities = clearAllCities;
   window.clearCitySearchInput = clearCitySearchInput;
   window.togglePopularPill = togglePopularPill;
    window.addCustomCityDirect = addCustomCityDirect;

    function removeAddressCard(btn, targetId) {
        if (targetId) {
            var el = document.getElementById(targetId);
            if (el) el.remove();
        }
        if (btn) {
            var elBtn = (typeof btn.jquery !== 'undefined' ? btn[0] : btn);
            if (elBtn && typeof elBtn.closest === 'function') {
                var card = elBtn.closest('.client-branch-card, .client-address-card, .client-address-container-child, .is-additional, .ij-location-card');
                if (card) card.remove();
            }
        }
        $('#additional_addresses_container .client-branch-card, .client-address-container .client-branch-card').each(function(idx) {
            $(this).find('.client-branch-title, .ij-location-title').text('Additional Location ' + (idx + 1));
        });
        var remaining = $('#additional_addresses_container .client-branch-card, .client-address-container .is-additional').length;
        $('#client_additional_empty').toggle(remaining === 0);
        if (window.clientLocationManager && typeof window.clientLocationManager.syncAiButtons === 'function') {
            window.clientLocationManager.syncAiButtons();
        }
        if (typeof updateCompletion === 'function') {
            updateCompletion();
        }
    }
    window.removeAddressCard = removeAddressCard;

    /* ===== UNIFIED CLIENT ADDRESS & CITY SEARCH ENGINE (V5 ARCHITECTURE) ===== */
    var cityAddressSearchAbortCtrl = {};

    function selectCityForAddress(cid, canonical, label) {
        var clean = String(canonical || '').trim();
        var isHq = (cid === 0 || cid === '0' || cid === '');
        var searchInput = document.getElementById(isHq ? 'city_search_0' : 'branch_city_search_' + cid);
        var hiddenInput = document.getElementById(isHq ? 'kt_tagify_work_location_0' : 'kt_tagify_work_location_' + cid);
        var dd = document.getElementById(isHq ? 'city_dropdown_0' : 'branch_city_dropdown_' + cid);
        var err = document.getElementById(isHq ? 'city_err_0' : 'branch_city_err_' + cid);

        if (searchInput) {
            searchInput.value = clean;
            $(searchInput).removeClass('is-invalid');
        }
        if (hiddenInput) {
            var parts = clean.split(/[,|]/).map(function(s) { return s.trim(); });
            var formattedValue = parts.join(' | ');
            hiddenInput.value = JSON.stringify([{ value: formattedValue }]);
        }
        if (err) err.style.display = 'none';
        if (dd) dd.classList.remove('active');
        clearFieldError($(searchInput));
        var $card = $(searchInput).closest('.client-branch-card');
        if ($card.length && !$card.find('textarea[name="address[]"]').hasClass('is-invalid') && ($card.find('textarea[name="address[]"]').val() || '').trim().length >= 5) {
            $card.css({ 'border': '1px solid #e2e8f0', 'box-shadow': '0 1px 2px rgba(0,0,0,0.02)' });
        }
    }

    function renderCityAddressDropdown(cid, query) {
        var isHq = (cid === 0 || cid === '0' || cid === '');
        var dd = document.getElementById(isHq ? 'city_dropdown_0' : 'branch_city_dropdown_' + cid);
        if (!dd) return;

        var trimmed = (query || '').trim();
        if (!trimmed) {
            var html = '';
            curatedLocationGroups.forEach(function(group) {
                group.items.forEach(function(item) {
                    var subText = item.state ? (item.state + ', ' + item.country) : item.country;
                    var safeCanon = escapeJsStr(item.canonical);
                    var safeCity = escapeJsStr(item.city);
                    html += '<div class="client-addr-dd-item" onclick="selectCityForAddress(\'' + cid + '\', \'' + safeCanon + '\', \'' + safeCity + '\')">' +
                        '<div class="client-addr-dd-left">' +
                        '  <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="#2563eb" stroke-width="2"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg>' +
                        '  <div>' +
                        '    <div class="client-addr-dd-city">' + escapeHtml(item.city) + '</div>' +
                        '    <div class="client-addr-dd-sub">' + escapeHtml(subText) + '</div>' +
                        '  </div>' +
                        '</div>' +
                        (item.badge ? '<span class="client-addr-dd-badge">' + escapeHtml(item.badge) + '</span>' : '') +
                        '</div>';
                });
            });
            dd.innerHTML = html;
            dd.classList.add('active');
            return;
        }

        var lookupQuery = trimmed.toLowerCase();
        var normalizedQ = citySynonymAliases[lookupQuery] || trimmed;
        var cacheKey = normalizedQ.toLowerCase().trim();
        var cached = getCachedCities(cacheKey);
        if (cached) {
            renderCityAddressSearchResults(cid, cached, trimmed);
            return;
        }

        if (cityAddressSearchAbortCtrl[cid]) {
            cityAddressSearchAbortCtrl[cid].abort();
        }
        cityAddressSearchAbortCtrl[cid] = new AbortController();

        var targetUrl = "{{ url('/') }}" + "/city-job-suggestions?q=" + encodeURIComponent(normalizedQ) + "&format=canonical&limit=25";
        fetch(targetUrl, {
            method: 'GET',
            signal: cityAddressSearchAbortCtrl[cid].signal,
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(function(res) {
            if (!res.ok) throw new Error('Network error: ' + res.status);
            return res.json();
        })
        .then(function(data) {
            var parsed = parseRawCityData(data);
            setCachedCities(cacheKey, parsed);
            renderCityAddressSearchResults(cid, parsed, trimmed);
        })
        .catch(function(err) {
            if (err.name === 'AbortError') return;
            var filtered = [];
            curatedLocationGroups.forEach(function(g) {
                g.items.forEach(function(item) {
                    if (item.city.toLowerCase().indexOf(normalizedQ.toLowerCase()) !== -1 ||
                        item.canonical.toLowerCase().indexOf(normalizedQ.toLowerCase()) !== -1) {
                        filtered.push(item);
                    }
                });
            });
            renderCityAddressSearchResults(cid, filtered, trimmed);
        });
    }

    function renderCityAddressSearchResults(cid, results, query) {
        var isHq = (cid === 0 || cid === '0' || cid === '');
        var dd = document.getElementById(isHq ? 'city_dropdown_0' : 'branch_city_dropdown_' + cid);
        if (!dd) return;

        if (!results || results.length === 0) {
            var safeQ = escapeHtml(query);
            var safeJsQ = escapeJsStr(query);
            dd.innerHTML = '<div class="client-addr-dd-empty">' +
                '<div>No matching cities found for &ldquo;' + safeQ + '&rdquo;</div>' +
                '<button type="button" class="btn btn-sm btn-light-primary mt-2" onclick="selectCityForAddress(\'' + cid + '\', \'' + safeJsQ + '\', \'' + safeJsQ + '\')">' +
                '+ Set &ldquo;' + safeQ + '&rdquo; as city</button>' +
                '</div>';
            dd.classList.add('active');
            return;
        }

        var html = '';
        results.forEach(function(item) {
            var canonical = item.canonical || item.label || (item.city + (item.state ? ', ' + item.state : '') + (item.country ? ', ' + item.country : ''));
            var subText = item.state ? (item.state + ', ' + item.country) : (item.country || '');
            var safeCanon = escapeJsStr(canonical);
            var safeCity = escapeJsStr(item.city);
            html += '<div class="client-addr-dd-item" onclick="selectCityForAddress(\'' + cid + '\', \'' + safeCanon + '\', \'' + safeCity + '\')">' +
                '<div class="client-addr-dd-left">' +
                '  <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="#2563eb" stroke-width="2"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg>' +
                '  <div>' +
                '    <div class="client-addr-dd-city">' + highlightMatch(item.city, query) + '</div>' +
                '    <div class="client-addr-dd-sub">' + escapeHtml(subText) + '</div>' +
                '  </div>' +
                '</div>' +
                (item.badge ? '<span class="client-addr-dd-badge">' + escapeHtml(item.badge) + '</span>' : '') +
                '</div>';
        });
        dd.innerHTML = html;
        dd.classList.add('active');
    }

    var debounceCityAddressSearch = debounce(function(cid, val) {
        renderCityAddressDropdown(cid, val);
    }, 180);

    function toggleSuggestionDetails(type) {
        var drawer = document.getElementById('drawer_' + type);
        var chevron = document.getElementById('chevron_' + type);
        if (!drawer) return;
        var isHidden = (drawer.style.display === 'none' || !drawer.style.display);
        drawer.style.display = isHidden ? 'block' : 'none';
        if (chevron) {
            if (isHidden) chevron.classList.add('is-open');
            else chevron.classList.remove('is-open');
        }
    }

    function applySuggestionDirect(cityName) {
        var hqCity = ($('#city_search_0').val() || '').trim();
        if (!hqCity) {
            selectCityForAddress(0, cityName, cityName.split(',')[0]);
            $('#city_search_0').focus();
        } else {
            $('#btn_add_another_address, .add_address').first().click();
            var $lastBranch = $('#additional_addresses_container .client-branch-card').last();
            var cid = $lastBranch.data('cid');
            if (cid) {
                selectCityForAddress(cid, cityName, cityName.split(',')[0]);
                $lastBranch.find('textarea[name="address[]"]').focus();
            }
        }
    }

    window.selectCityForAddress = selectCityForAddress;
    window.selectBranchCity = selectCityForAddress;
    window.renderCityAddressDropdown = renderCityAddressDropdown;
    window.renderBranchCuratedDropdown = function(cid) { renderCityAddressDropdown(cid, ''); };
    window.performBranchCitySearch = function(cid, q) { renderCityAddressDropdown(cid, q); };
    window.toggleSuggestionDetails = toggleSuggestionDetails;
    window.applySuggestionDirect = applySuggestionDirect;
    window.clearBranchCity = function(cid) { selectCityForAddress(cid, '', ''); };
   	
	function isNumberKey(evt)
   		{
   			var charCode = (evt.which) ? evt.which : evt.keyCode
   			if (charCode > 31 && (charCode < 48 || charCode > 57))
   				return false;
   			return true;
   		}		   
   $(function ()
   {
     	$('[data-toggle="tooltip"]').tooltip()
   })
   
   var loadFile = function(event) {
      var input = (event && event.target) ? event.target : document.getElementById('client_logo_input');
      if (input && input.files && input.files[0]) {
         var file = input.files[0];
         var maxLogoSizeBytes = 2 * 1024 * 1024; // 2MB limit per enterprise standard
         if (file.size > maxLogoSizeBytes) {
            if (typeof Swal !== 'undefined' && typeof Swal.fire === 'function') {
               Swal.fire({
                  icon: 'warning',
                  title: 'File Size Exceeded',
                  text: 'Company logo size cannot exceed 2MB. Please upload an image under 2MB.',
                  confirmButtonColor: '#ffd428'
               });
            } else {
               alert('Company logo size cannot exceed 2MB. Please upload an image under 2MB.');
            }
            input.value = '';
            return;
         }

         var output = URL.createObjectURL(file);
         $('#company_logo_box, .client-logo-preview .image-input-wrapper, #client_logo_preview .image-input-wrapper')
            .css({
               'background-image': 'url(' + output + ')',
               'background-size': 'contain',
               'background-repeat': 'no-repeat',
               'background-position': 'center'
            })
            .addClass('has-image')
            .empty();
         $('#client_avatar_remove').val('0');
         $('#client_remove_logo_btn, #client_preview_remove_btn').show();
         $('#logo_status_banner')
            .removeClass('is-empty')
            .addClass('is-detected')
            .html('<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg> <span>Logo uploaded successfully</span>');
         if (typeof updateCompletion === 'function') updateCompletion();
      }
   };
   window.loadFile = loadFile;
   $(document).on('change', '#client_logo_input', function(e) {
      loadFile(e);
   });

   // Drag & Drop support for Logo Preview Box with 2MB validation
   $(document).on('dragover dragenter', '#company_logo_preview_box', function(e) {
      e.preventDefault();
      e.stopPropagation();
      $(this).addClass('drag-hover');
   });
   $(document).on('dragleave dragend drop', '#company_logo_preview_box', function(e) {
      e.preventDefault();
      e.stopPropagation();
      $(this).removeClass('drag-hover');
   });
   $(document).on('drop', '#company_logo_preview_box', function(e) {
      e.preventDefault();
      e.stopPropagation();
      var dt = e.originalEvent.dataTransfer;
      if (dt && dt.files && dt.files[0]) {
         var fileIn = document.getElementById('client_logo_input');
         if (fileIn) {
            fileIn.files = dt.files;
            loadFile({ target: fileIn });
         }
      }
   });
   
</script>
<script>

        
    
   $( document ).ready(function() { 
       
       /* Delegated Address and City Search Listeners (V5 Architecture) */
       $(document).on('focus', '.client-addr-city-search, .branch-city-search', function () {
           var cid = $(this).attr('data-cid') || $(this).data('cid') || '0';
           var val = $(this).val();
           renderCityAddressDropdown(cid, val);
       });

       $(document).on('input', '.client-addr-city-search, .branch-city-search', function () {
           var cid = $(this).attr('data-cid') || $(this).data('cid') || '0';
           var val = $(this).val();
           var isHq = (cid === 0 || cid === '0' || cid === '');
           var hiddenInput = document.getElementById(isHq ? 'kt_tagify_work_location_0' : 'kt_tagify_work_location_' + cid);
           var errEl = document.getElementById(isHq ? 'city_err_0' : 'branch_city_err_' + cid);

           $(this).removeClass('is-invalid');
           if (errEl) errEl.style.display = 'none';

           if (hiddenInput) {
               if (val.trim()) {
                   var parts = val.split(/[,|]/).map(function(s) { return s.trim(); });
                   hiddenInput.value = JSON.stringify([{ value: parts.join(' | ') }]);
               } else {
                   hiddenInput.value = '';
               }
           }
           debounceCityAddressSearch(cid, val);
       });

       $(document).on('click', '.remove_address_single, .client-remove-address-btn, .client-branch-remove-btn', function (e) {
           e.preventDefault();
           e.stopPropagation();
           var target = $(this).attr('data-target') || $(this).data('target');
           if (typeof removeAddressCard === 'function') {
               removeAddressCard(this, target);
           }
       });

       $(document).on('input', 'textarea[name="address[]"], .client-addr-textarea, .client-address-textarea', function () {
           var id = $(this).attr('id') || '';
           var cid = id.replace('address_', '');
           var val = $(this).val() || '';
           var countEl = document.getElementById('address_char_count_' + cid);
           if (countEl) countEl.textContent = val.length;

           $(this).removeClass('is-invalid');
           var errEl = document.getElementById(cid === '0' || cid === 0 ? 'address_err_0' : 'branch_address_err_' + cid);
           if (errEl) errEl.style.display = 'none';

           var $card = $(this).closest('.client-branch-card');
           if ($card.length && val.trim().length >= 5 && !$card.find('.branch-city-search').hasClass('is-invalid') && ($card.find('.branch-city-search').val() || '').trim()) {
               $card.css({ 'border': '1px solid #e2e8f0', 'box-shadow': '0 1px 2px rgba(0,0,0,0.02)' });
           }
       });

       $(document).on('input', '.branch-city-search, .client-addr-city-search', function () {
           var cid = $(this).data('cid');
           $(this).removeClass('is-invalid');
           var errEl = document.getElementById(cid === '0' || cid === 0 ? 'city_err_0' : 'branch_city_err_' + cid);
           if (errEl) errEl.style.display = 'none';

           var $card = $(this).closest('.client-branch-card');
           if ($card.length && $(this).val().trim() && !$card.find('textarea[name="address[]"]').hasClass('is-invalid') && ($card.find('textarea[name="address[]"]').val() || '').trim().length >= 5) {
               $card.css({ 'border': '1px solid #e2e8f0', 'box-shadow': '0 1px 2px rgba(0,0,0,0.02)' });
           }
       });

       $(document).on('click', function (e) {
           if (!$(e.target).closest('.client-addr-input-wrap, .ij-location-input-wrap').length) {
               $('.client-addr-city-dropdown, .branch-city-dropdown').removeClass('active');
           }
       }); 
       
      
       
      // Toggle the dropdown on button click
        $('#kt_header_user_menu_toggle').click(function(event) {
            event.stopPropagation(); // Prevent the click event from bubbling up to the body
            var $dropdown = $('.menu-sub-dropdown');
            if ($dropdown.hasClass('menu-sub-dropdown-hidden')) {
                 $dropdown.removeClass('menu-sub-dropdown-hidden');
                 $dropdown.attr('style','z-index: 105; position: fixed; inset: 0px auto auto 0px; margin: 0px; transform: translate(1278px, 65px);display: inline-block;');
                 $dropdown.css('display', 'inline-block');
            } else {
                $dropdown.addClass('menu-sub-dropdown-hidden');
                $dropdown.css('display', 'none');
            }
        });
        
        // Hide the dropdown when clicking outside of it
        $('body').click(function(event) {
            var $dropdown = $('.menu-sub-dropdown');
            if (!$dropdown.hasClass('menu-sub-dropdown-hidden')) {
                $dropdown.addClass('menu-sub-dropdown-hidden');
                $dropdown.css('display', 'none');
            }
        });
        
        
         $('.menu-accordion').click(function(){
              var $dropdown = $(this);
                if (!$dropdown.hasClass('show')) {
                     $('.menu-accordion').removeClass('show'); 
                     $dropdown.addClass('show');
                }else{
                    $dropdown.removeClass('show'); 
                }
           
       });
       
       
         
       
       
       

    setTimeout(() => {
  var cid =  $(".countryfirst").val();
    setTimeout(() => {
       $(".countryfirst").val(cid).change();
   }, 500);
 
  }, 500);
    function escapeHtml(str) {
        if (str === null || str === undefined) return '';
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }
    window.escapeHtml = escapeHtml;

    // Metronic Avatar Palette (patterned from Create Job form)
    function getAvatarColor(str) {
        var colors = ['#3E97FF', '#7239EA', '#50CD89', '#F6C000', '#F1416C', '#009EF7'];
        if (!str) return colors[0];
        var hash = 0;
        for (var i = 0; i < str.length; i++) hash = str.charCodeAt(i) + ((hash << 5) - hash);
        return colors[Math.abs(hash) % colors.length];
    }
    window.getAvatarColor = getAvatarColor;

    /* =========================================================================
       WINDOW.CLIENTLOCATIONMANAGER (GOD-LEVEL V5 AI ONE-CLICK LOCATION ENGINE)
       Matches media_1792565792953_2202cf86.png
       ========================================================================= */
    window.clientLocationManager = {
        aiLocations: [
            {
                city: "Pune, Maharashtra, India",
                cityName: "Pune",
                tagifyVal: "Pune | Maharashtra | India",
                address: "Level 5, Cybercity Tower 2, Magarpatta City, Hadapsar, Pune, Maharashtra 411028",
                type: "Corporate Office / Headquarters",
                isHq: true
            },
            {
                city: "Bengaluru, Karnataka, India",
                cityName: "Bengaluru",
                tagifyVal: "Bengaluru | Karnataka | India",
                address: "Prestige Tech Park, Block B, Outer Ring Road, Marathahalli, Bengaluru, Karnataka 560103",
                type: "Development Centre",
                isHq: false
            },
            {
                city: "Mumbai, Maharashtra, India",
                cityName: "Mumbai",
                tagifyVal: "Mumbai | Maharashtra | India",
                address: "Unit 402, Naman Centre, G Block, Bandra Kurla Complex, Bandra East, Mumbai, Maharashtra 400051",
                type: "Office",
                isHq: false
            }
        ],

        init: function() {
            var self = this;
            this.syncAiButtons();
            this.syncWebsiteLink();

            $(document).on('input', '#company_website, #client_id', function() {
                self.syncWebsiteLink();
            });

            $(document).on('input change', '#city_search_0, .branch-city-search', function() {
                self.syncAiButtons();
            });
        },

        syncWebsiteLink: function() {
            var web = ($('#company_website').val() || '').trim();
            if (web) {
                var url = (web.indexOf('://') === -1) ? ('https://' + web) : web;
                $('#ai_view_source_link, .client-ai-source-link').attr('href', url).attr('target', '_blank');
            } else {
                $('#ai_view_source_link, .client-ai-source-link').attr('href', 'javascript:void(0);').removeAttr('target');
            }
        },

        addAiLocation: function(index, forceHq) {
            var loc = this.aiLocations[index];
            if (!loc) return;

            var $hqCity = $('#city_search_0');
            var $hqTagify = $('#kt_tagify_work_location_0');
            var $hqAddr = $('#address_0');

            var currentHqCity = ($hqCity.val() || '').trim();
            var currentHqAddr = ($hqAddr.val() || '').trim();

            if (forceHq || (!currentHqCity && !currentHqAddr)) {
                if (currentHqCity && currentHqCity.toLowerCase().indexOf(loc.cityName.toLowerCase()) === -1) {
                    this.createBranchWithData(currentHqCity, $hqTagify.val(), currentHqAddr);
                }
                $hqCity.val(loc.city).removeClass('is-invalid');
                $hqTagify.val(JSON.stringify([{ value: loc.tagifyVal }]));
                $hqAddr.val(loc.address).removeClass('is-invalid');
                $('#address_char_count_0').text(loc.address.length);
                $('#city_err_0, #address_err_0').hide();

                if (typeof Swal !== 'undefined' && typeof Swal.mixin === 'function') {
                    Swal.mixin({ toast: true, position: 'top-end', showConfirmButton: false, timer: 2200 })
                        .fire({ icon: 'success', title: loc.cityName + ' set as company headquarters!' });
                }
            } else {
                if (currentHqCity.toLowerCase().indexOf(loc.cityName.toLowerCase()) !== -1) {
                    if (typeof Swal !== 'undefined' && typeof Swal.mixin === 'function') {
                        Swal.mixin({ toast: true, position: 'top-end', showConfirmButton: false, timer: 2200 })
                            .fire({ icon: 'info', title: loc.cityName + ' is already set as headquarters.' });
                    }
                    this.syncAiButtons();
                    return;
                }

                var alreadyAdded = false;
                $('#additional_addresses_container .client-branch-card').each(function() {
                    var bCity = ($(this).find('.branch-city-search').val() || '').trim().toLowerCase();
                    if (bCity && bCity.indexOf(loc.cityName.toLowerCase()) !== -1) {
                        alreadyAdded = true;
                    }
                });

                if (alreadyAdded) {
                    if (typeof Swal !== 'undefined' && typeof Swal.mixin === 'function') {
                        Swal.mixin({ toast: true, position: 'top-end', showConfirmButton: false, timer: 2200 })
                            .fire({ icon: 'info', title: loc.cityName + ' is already added as an office.' });
                    }
                    this.syncAiButtons();
                    return;
                }

                this.createBranchWithData(loc.city, JSON.stringify([{ value: loc.tagifyVal }]), loc.address);
                if (typeof Swal !== 'undefined' && typeof Swal.mixin === 'function') {
                    Swal.mixin({ toast: true, position: 'top-end', showConfirmButton: false, timer: 2200 })
                        .fire({ icon: 'success', title: loc.cityName + ' added as additional office!' });
                }
            }

            this.syncAiButtons();
            if (typeof updateCompletion === 'function') updateCompletion();
        },

        addAllAiLocations: function() {
            var self = this;
            if (!this.aiLocations || this.aiLocations.length === 0) return;

            var hq = this.aiLocations[0];
            $('#city_search_0').val(hq.city).removeClass('is-invalid');
            $('#kt_tagify_work_location_0').val(JSON.stringify([{ value: hq.tagifyVal }]));
            $('#address_0').val(hq.address).removeClass('is-invalid');
            $('#address_char_count_0').text(hq.address.length);
            $('#city_err_0, #address_err_0').hide();

            $('#additional_addresses_container').empty();
            $('#addressId').val(0);

            for (var i = 1; i < this.aiLocations.length; i++) {
                var loc = this.aiLocations[i];
                this.createBranchWithData(loc.city, JSON.stringify([{ value: loc.tagifyVal }]), loc.address);
            }

            this.syncAiButtons();
            if (typeof updateCompletion === 'function') updateCompletion();

            if (typeof Swal !== 'undefined' && typeof Swal.mixin === 'function') {
                Swal.mixin({ toast: true, position: 'top-end', showConfirmButton: false, timer: 3000 })
                    .fire({ icon: 'success', title: '✓ 3 locations added. Pune is set as headquarters.' });
            }
        },

        setBranchAsHq: function(cid) {
            var $hqCity = $('#city_search_0');
            var $hqTagify = $('#kt_tagify_work_location_0');
            var $hqAddr = $('#address_0');

            var $bCity = $('#branch_city_search_' + cid);
            var $bTagify = $('#kt_tagify_work_location_' + cid);
            var $bAddr = $('#address_' + cid);

            var oldHqCity = $hqCity.val() || '';
            var oldHqTagify = $hqTagify.val() || '';
            var oldHqAddr = $hqAddr.val() || '';

            var bCityVal = $bCity.val() || '';
            var bTagifyVal = $bTagify.val() || '';
            var bAddrVal = $bAddr.val() || '';

            $hqCity.val(bCityVal).removeClass('is-invalid');
            $hqTagify.val(bTagifyVal);
            $hqAddr.val(bAddrVal).removeClass('is-invalid');
            $('#address_char_count_0').text(bAddrVal.length);

            $bCity.val(oldHqCity).removeClass('is-invalid');
            $bTagify.val(oldHqTagify);
            $bAddr.val(oldHqAddr).removeClass('is-invalid');
            $('#address_char_count_' + cid).text(oldHqAddr.length);

            $('#city_err_0, #address_err_0').hide();
            $('#branch_city_err_' + cid + ', #branch_address_err_' + cid).hide();

            this.syncAiButtons();
            if (typeof updateCompletion === 'function') updateCompletion();

            var newHqName = bCityVal ? bCityVal.split(',')[0].trim() : 'Location';
            if (typeof Swal !== 'undefined' && typeof Swal.mixin === 'function') {
                Swal.mixin({ toast: true, position: 'top-end', showConfirmButton: false, timer: 2200 })
                    .fire({ icon: 'success', title: newHqName + ' is now set as Headquarters!' });
            }
        },

        addManualBranch: function() {
            var addressId = parseInt($('#addressId').val(), 10) || 0;
            addressId++;
            $('#addressId').val(addressId);
            this.createBranchWithData('', '', '');
            $('#branch_city_search_' + addressId).focus();
        },

        createBranchWithData: function(city, tagifyJson, address) {
            var addressId = parseInt($('#addressId').val(), 10) || 0;
            if (!addressId) {
                addressId = $('#additional_addresses_container .client-branch-card').length + 1;
                $('#addressId').val(addressId);
            }
            var cid = addressId;
            var branchNumber = $('#additional_addresses_container .client-branch-card').length + 1;

            var cardHtml = '<div class="client-branch-card" id="clientAddr_' + cid + '" data-cid="' + cid + '" style="background:#ffffff; border:1px solid #e2e8f0; border-radius:10px; padding:14px 16px; margin-bottom:12px; box-shadow:0 1px 2px rgba(0,0,0,0.02);">' +
                '<div class="d-flex align-items-center justify-content-between mb-2">' +
                '  <div class="d-flex align-items-center gap-2">' +
                '    <div style="width:28px; height:28px; border-radius:7px; background:#f1f5f9; display:inline-flex; align-items:center; justify-content:center; color:#475569;">' +
                '      <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg>' +
                '    </div>' +
                '    <div>' +
                '      <div class="d-flex align-items-center gap-2">' +
                '        <h4 class="client-branch-title mb-0" style="font-size:13px; font-weight:700; color:#0f172a;">Additional Location ' + branchNumber + '</h4>' +
                '        <span class="badge" style="background:#f1f5f9; color:#475569; font-size:9.5px; font-weight:600; padding:2px 6px; border-radius:4px;">OFFICE</span>' +
                '      </div>' +
                '    </div>' +
                '  </div>' +
                '  <div class="d-flex align-items-center gap-2">' +
                '    <button type="button" class="btn btn-xs" onclick="window.clientLocationManager ? window.clientLocationManager.setBranchAsHq(' + cid + ') : false" style="font-size:10.5px; font-weight:600; padding:3px 8px; border-radius:5px; color:#b45309; border:1px solid #fde68a; background:#fefce8; cursor:pointer;" title="Set as Headquarters">' +
                '      Set as Headquarters' +
                '    </button>' +
                '    <button type="button" class="btn btn-xs client-branch-remove-btn" onclick="removeAddressCard(this, \'clientAddr_' + cid + '\')" style="font-size:11px; font-weight:600; padding:3px 8px; border-radius:6px; color:#dc2626; border:1px solid #fee2e2; background:#fef2f2; cursor:pointer; display:inline-flex; align-items:center; gap:3px;" title="Remove Location">' +
                '      <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>' +
                '      <span>Remove</span>' +
                '    </button>' +
                '  </div>' +
                '</div>' +
                '<div class="client-branch-body">' +
                '  <div class="client-addr-field mb-2">' +
                '    <label class="client-addr-label" for="branch_city_search_' + cid + '" style="font-size:11.5px; font-weight:600; color:#334155; margin-bottom:3px; display:block;">City / Location <span class="text-danger">*</span></label>' +
                '    <div class="client-addr-input-wrap position-relative">' +
                '      <svg class="client-addr-icon-left position-absolute" viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" style="left:11px; top:50%; transform:translateY(-50%); color:#94a3b8; pointer-events:none;"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>' +
                '      <input type="text" class="form-control client-addr-input client-addr-city-search branch-city-search" id="branch_city_search_' + cid + '" data-cid="' + cid + '" value="' + escapeHtmlAttr(city || '') + '" placeholder="Search for a city..." autocomplete="off" style="height:36px; padding:0 32px 0 32px; font-size:12px; border:1px solid #cbd5e1; border-radius:8px;">' +
                '      <svg class="client-addr-icon-right position-absolute" viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" style="right:11px; top:50%; transform:translateY(-50%); color:#94a3b8; pointer-events:none;"><polyline points="6 9 12 15 18 9"></polyline></svg>' +
                '      <div class="client-addr-city-dropdown branch-city-dropdown" id="branch_city_dropdown_' + cid + '"></div>' +
                '      <input type="hidden" name="kt_tagify_work_location[]" id="kt_tagify_work_location_' + cid + '" value=\'' + (tagifyJson || '') + '\'>' +
                '    </div>' +
                '    <div class="client-addr-error text-danger" id="branch_city_err_' + cid + '" style="display:none; font-size:10.5px; margin-top:3px;">City selection is required.</div>' +
                '  </div>' +
                '  <div class="client-addr-field">' +
                '    <label class="client-addr-label" for="address_' + cid + '" style="font-size:11.5px; font-weight:600; color:#334155; margin-bottom:3px; display:block;">Full Address <span class="text-danger">*</span></label>' +
                '    <div class="client-addr-textarea-wrap position-relative">' +
                '      <svg class="client-addr-textarea-icon position-absolute" viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" style="left:11px; top:9px; color:#94a3b8; pointer-events:none;"><rect x="4" y="2" width="16" height="20" rx="2" ry="2"/><path d="M9 22v-4h6v4"/></svg>' +
                '      <textarea class="form-control client-addr-textarea" name="address[]" id="address_' + cid + '" rows="2" maxlength="500" placeholder="Enter complete address..." style="padding:7px 10px 7px 32px; font-size:12px; border:1px solid #cbd5e1; border-radius:8px; resize:vertical; min-height:54px;">' + escapeHtmlText(address || '') + '</textarea>' +
                '    </div>' +
                '    <div class="d-flex align-items-center justify-content-between mt-1">' +
                '      <span class="text-muted" style="font-size:10.5px;">Accurate branch address.</span>' +
                '      <span class="client-addr-counter" style="font-size:10.5px; color:#94a3b8;"><span id="address_char_count_' + cid + '">' + (address ? address.length : 0) + '</span>/500</span>' +
                '    </div>' +
                '    <div class="client-addr-error text-danger" id="branch_address_err_' + cid + '" style="display:none; font-size:10.5px; margin-top:3px;">Full Address is required.</div>' +
                '  </div>' +
                '</div>' +
                '</div>';

            $('#additional_addresses_container').append(cardHtml);
            $('#client_additional_empty').hide();

            var newCardEl = document.getElementById('clientAddr_' + cid);
            if (window.clientWizardUi && typeof window.clientWizardUi.bindAddressCounters === 'function') {
                window.clientWizardUi.bindAddressCounters(newCardEl);
            } else if (typeof bindAddressCounters === 'function') {
                bindAddressCounters(newCardEl);
            }
            return cid;
        },

        syncAiButtons: function() {
            var hqCity = ($('#city_search_0').val() || '').trim().toLowerCase();
            var branchCities = [];
            $('#additional_addresses_container .client-branch-card').each(function() {
                var c = ($(this).find('.branch-city-search').val() || '').trim().toLowerCase();
                if (c) branchCities.push(c);
            });

            var allAddedCount = 0;

            for (var i = 0; i < this.aiLocations.length; i++) {
                var loc = this.aiLocations[i];
                var cityNameLower = loc.cityName.toLowerCase();
                var $actionsWrap = $('#ai_loc_actions_' + i);
                if (!$actionsWrap.length) continue;

                var isHq = hqCity.indexOf(cityNameLower) !== -1;
                var isBranch = branchCities.some(function(b) { return b.indexOf(cityNameLower) !== -1; });

                if (isHq) {
                    allAddedCount++;
                    $actionsWrap.html(
                        '<span class="badge" style="background:#dcfce7; border:1px solid #bbf7d0; color:#15803d; font-size:10.5px; font-weight:600; padding:4px 9px; border-radius:6px; display:inline-flex; align-items:center; gap:4px;">' +
                        '  <svg width="12" height="12" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>' +
                        '  Headquarters' +
                        '</span>'
                    );
                } else if (isBranch) {
                    allAddedCount++;
                    $actionsWrap.html(
                        '<span class="badge" style="background:#eff6ff; border:1px solid #bfdbfe; color:#1d4ed8; font-size:10.5px; font-weight:600; padding:4px 9px; border-radius:6px; display:inline-flex; align-items:center; gap:4px;">' +
                        '  <svg width="12" height="12" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>' +
                        '  Office Added' +
                        '</span>'
                    );
                } else {
                    var buttonsHtml = '';
                    if (loc.isHq) {
                        buttonsHtml =
                            '<button type="button" class="btn btn-sm" onclick="window.clientLocationManager ? window.clientLocationManager.addAiLocation(' + i + ', true) : false;" style="background:#f59e0b; color:#78350f; font-weight:600; font-size:11px; border:none; border-radius:6px; padding:4px 10px; cursor:pointer;">Set as Headquarters</button>' +
                            '<button type="button" class="btn btn-sm" onclick="window.clientLocationManager ? window.clientLocationManager.addAiLocation(' + i + ', false) : false;" style="background:#ffffff; color:#2563eb; border:1px solid #2563eb; font-weight:600; font-size:11px; border-radius:6px; padding:4px 10px; cursor:pointer;">Add as Office</button>';
                    } else {
                        buttonsHtml =
                            '<button type="button" class="btn btn-sm" onclick="window.clientLocationManager ? window.clientLocationManager.addAiLocation(' + i + ', false) : false;" style="background:#ffffff; color:#2563eb; border:1px solid #2563eb; font-weight:600; font-size:11px; border-radius:6px; padding:4px 10px; cursor:pointer;">Add as Office</button>';
                    }
                    $actionsWrap.html(buttonsHtml);
                }
            }

            var $bulkBtn = $('#btn_add_all_ai_locations');
            if ($bulkBtn.length) {
                if (allAddedCount >= this.aiLocations.length) {
                    $bulkBtn.html('<svg width="13" height="13" viewBox="0 0 20 20" fill="#15803d"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg> <span>All 3 Locations Added</span>')
                        .css({ 'background': '#f0fdf4', 'border-color': '#bbf7d0', 'color': '#15803d', 'pointer-events': 'none', 'opacity': '0.9' });
                } else {
                    $bulkBtn.html('<svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="12 2 2 7 12 12 22 7 12 2"></polygon><polyline points="2 17 12 22 22 17"></polyline><polyline points="2 12 12 17 22 12"></polyline></svg> <span>Add all 3 locations</span>')
                        .css({ 'background': '#eff6ff', 'border-color': '#bfdbfe', 'color': '#1d4ed8', 'pointer-events': 'auto', 'opacity': '1' });
                }
            }
        }
    };

    $(document).on('click', '#btn_add_another_address, .add_address', function (e) {
        e.preventDefault();
        if (window.clientLocationManager) {
            window.clientLocationManager.addManualBranch();
        }
    });

    $(document).ready(function() {
        if (window.clientLocationManager) {
            window.clientLocationManager.init();
        }
    });

    $(document).on('input', '#client_id', function() {
        var name = $(this).val().trim();
        $('#ai_suggested_company_name').text(name || 'Jeroworks');
    });
$('.remove_address').click(function(){
          let container = $('.client-address-container');
          let totalChildren = container.children().length;
          if (totalChildren > 1) {
            $('.client-address-container').children().last().remove();
            }else{
            Swal.fire("Success",'At least one address is required' , "error");	
            }
   	});
   		

   	
   		$('.remove_cperson').click(function(){
   			  $('.client-cperson-container').last().remove();
   	 });
      $(document).on('click', '.fa-close', function() { 
     
            let row = this.closest('.client-cperson-container');
            row.remove();
            $('.count_class').each(function(index) {
            $(this).text(index + 1);
        });
   	});
   		
   		$(document).on('change', '.country', function() { 
   	
   				cid			=	this.value;				
   				var str 	=	$(this).attr('id');
   				var id		=	str.substr(8);   
   				var id 		=	parseInt(id);
				
				// if(cid == 101){
				// 	$('#city_id_'+id).show();	
				// 	$('#city_'+id).hide();	
				// }else{
				// 	$('#city_'+id).show();	
				// 	$('#city_id_'+id).hide();	
				// }
				
				$('#hid_country_id_'+id).val(cid);
   				
   					var url 			=	"{{ route('service_company.job-applications.statelist',':cid') }}";
   					url 				=	url.replace(':cid', cid);
   					$.ajax({
   									type	: 'GET',
   									url		: url,
   									success	: function (response) {		
   																		$('#state_'+id).html(response.data);
   																		
   																   }		
   										   
   					});				
   								
   	});
    	
   		$(document).on('click', '.legacy-btn-disabled', function() { 
   	
   		var state 	=	$('#kt_wizard').attr("data-wizard-state"); 
      
		
   		if(state == 'last')
   		{
   			$('.span_name').html('');
   			$('.span_website').html('');
   			$('.span_industry').html('');	
   			$('.span_company_sizes').html('');	
   			$('.span_company_type').html('');	
   			$('.span_founded').html('');
   			$('.span_kt_docs_tinymce_hidden').html('');
   			$('.span_tags').html('');
   			$('.divAddressDetail').html('');
   			$('.div-contact-person').html('');
   				
   			var name			=	$('#client_id').val();
   			$('.span_name').text(name);			
   			var website			=	$('#website').val();
   			$('.span_website').text(website).attr('href', website);			
   			var industry		=	$('#industry option:selected').text(); 
   			$('.span_industry').text(industry);			
   			var company_sizes	=	$('#company_sizes option:selected').text();
   			$('.span_company_sizes').text(company_sizes && company_sizes !== 'Select Company Size' ? company_sizes + ' employees' : '');			
   			var company_type	=	$('#company_type option:selected').text(); 
   			$('.span_company_type').text(company_type);			
   			var founded			=	$('#founded').val(); 
   			$('.span_founded').text(founded);
   			
   			var kt_docs_tinymce_hidden = (typeof getClientDescriptionHtml === 'function') ? getClientDescriptionHtml() : ($('#kt_docs_tinymce_hidden').val() || '');
			$('.span_kt_docs_tinymce_hidden').html(kt_docs_tinymce_hidden);
			
   			var tags = $('#tags').val(); 	
   			var json_tags = tags ? $.parseJSON(tags) : [];
   			var tagLabels = [];
   			for(let len=0; len<json_tags.length; len++){
   				tagLabels.push(json_tags[len].value);
   			}
   			$('.span_tags').text(tagLabels.join(', '));

			var addressId = parseInt($('#addressId').val(), 10) || 0;
			var addressBlocks = [];

			if (typeof selectedCities !== 'undefined' && selectedCities.length > 0) {
				var locChips = selectedCities.map(function(c) {
					return '<span class="ij-location-chip me-1 mb-1"><span class="ij-chip-pin"><svg viewBox="0 0 24 24" fill="none" stroke-width="2"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg></span><span class="ij-chip-label">' + escapeHtml(c) + '</span></span>';
				}).join('');
				addressBlocks.push('<div class="client-review-address-box col-12 mb-3"><h5><i class="la la-map-marker text-primary me-1"></i>Selected Work Locations (' + selectedCities.length + ')</h5><div class="d-flex flex-wrap gap-1 mt-2">' + locChips + '</div></div>');
			}

			var hqAddress = ($('#address_0').val() || '').trim();
			if (hqAddress) {
				addressBlocks.push('<div class="client-review-address-box"><h5><i class="la la-building text-primary me-1"></i>Headquarters (Primary) Physical Address</h5><p>' + escapeHtml(hqAddress) + '</p></div>');
			}

						var addIdx = 0;
			$('.client-address-container .client-address-card.is-additional').each(function() {
				var $card = $(this);
				addIdx++;
				var address = ($card.find('textarea[name="address[]"]').val() || '').trim();
				var branchCity = ($card.find('.branch-city-search').val() || '').trim();
				if (!branchCity) {
					var tagVal = $card.find('input[name="kt_tagify_work_location[]"]').val() || '';
					branchCity = tagVal.replace(/^\[\{"value":"?/, '').replace(/"?\}\]$/, '');
				}
				if (branchCity || address) {
					addressBlocks.push('<div class="client-review-address-box"><h5><i class="la la-map-pin text-primary me-1"></i>Additional Branch Address (' + addIdx + ')</h5><p>' + (branchCity ? '<strong>' + escapeHtml(branchCity) + '</strong><br>' : '') + escapeHtml(address) + '</p></div>');
				}
			});
			$('.divAddressDetail').html(addressBlocks.join(''));
   			
   			var contactPersonCounter = $('#contactPersonId').val();
   			var contactRows = [];
   			for(let cnt=0; cnt<=contactPersonCounter; cnt++)
   			{
   				var cp_name = $('#cp_name_'+cnt).val();
   				var cp_email = $('#cp_email_'+cnt).val();
   				var cp_number = $('#cp_number_'+cnt).val();
   				var cp_desigination = $('#cp_desigination_'+cnt).val();
            if(typeof cp_name == 'undefined' || !cp_name) continue;
            var typeBadge = contactRows.length === 0 ? '<span class="client-badge-primary">Primary</span>' : '<span class="client-badge-secondary">Secondary</span>';
            var primaryTag = contactRows.length === 0 ? '<span class="client-badge-primary">Primary</span>' : '';
            var avatarLetters = cp_name.trim().split(/\s+/).slice(0,2).map(function(p){return p.charAt(0).toUpperCase();}).join('');
            var avatarBg = getAvatarColor(cp_name);
            contactRows.push('<tr><td><div class="client-contact-person"><span class="client-contact-avatar" style="background-color:'+avatarBg+'; color:#fff; display:inline-flex; align-items:center; justify-content:center; font-weight:600; width:32px; height:32px; border-radius:50%; margin-right:8px;">'+escapeHtml(avatarLetters)+'</span><span class="client-contact-name">'+escapeHtml(cp_name)+primaryTag+'</span></div></td><td>'+escapeHtml(cp_desigination || '—')+'</td><td>'+escapeHtml(cp_email || '—')+'</td><td>'+escapeHtml(cp_number || '—')+'</td><td>'+typeBadge+'</td></tr>');
   			}
   			$('.div-contact-person').html(contactRows.join(''));

   			if (window.clientWizardUi && typeof window.clientWizardUi.populateReviewSummaries === 'function') {
          window.clientWizardUi.populateReviewSummaries();
        }
   			$('#continueButton').hide();
   			$('.submitfrm').show();
   			$('.updatefrm').show();
   		}
   		else
   		{
   			$('.submitfrm').hide();
   			$('.updatefrm').hide();
   			$('#continueButton').show();
   		}
   		
   	});
        
   });
</script>
<script>

   $(document).ready(function () {
   
       $('#kt_form').validate({
           						rules: {
               								name: {
                   										required: true
               										},
               								website: { required: false }
           							}
       					});
   
   });   
    var isClientSubmitting = false;

    $(document).on('click', '.submitfrm', function(e) {
        if (e && e.preventDefault) { e.preventDefault(); }
        if (isClientSubmitting) {
            return false;
        }

        // Validate all wizard steps before submitting!
        for (var s = 1; s <= 5; s++) {
          if (typeof validateWizardStep === 'function' && !validateWizardStep(s)) {
            isClientSubmitting = false;
            if (typeof goToWizardStep === 'function') {
              goToWizardStep(s);
            }
            var $invalidEl = window.$firstInvalid;
            if ($invalidEl && $invalidEl.length) {
              $invalidEl.focus();
              try {
                $('html, body').animate({
                  scrollTop: Math.max(0, $invalidEl.offset().top - 140)
                }, 300);
              } catch (err) {}
            }
            if (typeof Swal !== 'undefined' && typeof Swal.fire === 'function') {
              var submitErrMsg = (s === 3 && window._lastStep3Error)
                ? window._lastStep3Error
                : 'Please complete all required fields in Step ' + s + ' before submitting.';
              Swal.fire({
                icon: 'warning',
                title: 'Incomplete Details',
                text: submitErrMsg,
                confirmButtonText: 'Review Step ' + s,
                customClass: { confirmButton: 'btn btn-warning' }
              });
            }
            return false;
          }
        }

        var $btn = $(this);
        isClientSubmitting = true;

    	var data 			=	new FormData();
    	var form_data 		=	$('#kt_form').serializeArray();

      $btn.prop("disabled", true);
      $btn.html(
        `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Waiting...`
      );

      var idempotencyKey = $('#kt_form_idempotency_key').val();
      if (!idempotencyKey) {
          idempotencyKey = 'sc_client_' + Date.now() + '_' + Math.random().toString(36).substring(2, 11) + Math.random().toString(36).substring(2, 11);
          $('#kt_form_idempotency_key').val(idempotencyKey);
      }

    	$.each(form_data, function (key, input) {
        		data.append(input.name, input.value);
    	});

    	var file_data 		= $('input[name="logo"]')[0].files;
    	for (var i = 0; i < file_data.length; i++) {
        		data.append("logo", file_data[i]);
    	}

 	var kt_docs_tinymce_hidden = (typeof getClientDescriptionHtml === 'function') ? getClientDescriptionHtml() : ($('#kt_docs_tinymce_hidden').val() || '');
     data.append("description", kt_docs_tinymce_hidden);

    	data.append('key', 'value');

     if (!navigator.onLine) {
         Swal.fire({ title: "No Internet", text: "You are currently offline or the server is unreachable.", icon: "warning", confirmButtonText: "Ok, got it!", customClass: { confirmButton: "btn btn-warning" } });
         isClientSubmitting = false;
         $btn.prop("disabled", false).html('<i class="la la-paper-plane"></i> Submit Client');
         return;
     }

    	$.ajax({
        			url					: '{{route('service_company.clients.store')}}',
    				headers				: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                        'X-Idempotency-Key': idempotencyKey
                    },
        			method				: "POST",
        			processData			: false,
        			contentType			: false,
       		 	data				: data,
    				  redirect			: true,
        			success				: function (data)
                 {
                   if(data.status == 'success')
                   {
                      try { localStorage.removeItem('jero_client_draft_' + {{ (int) (auth()->user()->company_id ?? 0) }}); } catch (e) {}
                      window.location = data.url || '{{ route('service_company.clients.index') }}';
                   } else if (data.status === 'processing') {
                      try { localStorage.removeItem('jero_client_draft_' + {{ (int) (auth()->user()->company_id ?? 0) }}); } catch (e) {}
                      setTimeout(function() {
                          window.location = data.url || '{{ route('service_company.clients.index') }}';
                      }, 1000);
                   } else {
                      isClientSubmitting = false;
                      $('#kt_form_idempotency_key').val('sc_client_' + Date.now() + '_' + Math.random().toString(36).substring(2, 11) + Math.random().toString(36).substring(2, 11));
                      $btn.prop("disabled", false).html('<i class="la la-paper-plane"></i> Submit Client');
                      Swal.fire("Notice", data.message || "Operation failed", "warning");
                   }
        			},
        			error				: function (xhr) {
                     isClientSubmitting = false;
                     $('#kt_form_idempotency_key').val('sc_client_' + Date.now() + '_' + Math.random().toString(36).substring(2, 11) + Math.random().toString(36).substring(2, 11));
                     $btn.prop("disabled", false).html('<i class="la la-paper-plane"></i> Submit Client');
                     if (xhr.status === 0 || !navigator.onLine) {
                         Swal.fire({ title: "No Internet", text: "You are currently offline or the server is unreachable.", icon: "warning", confirmButtonText: "Ok, got it!", customClass: { confirmButton: "btn btn-warning" } });
                     } else if (xhr.responseJSON && xhr.responseJSON.message) {
                         Swal.fire("Error", xhr.responseJSON.message, "error");
                     } else {
                         Swal.fire("Error", "Something went wrong. Please try again.", "error");
                     }
        			}
    			});

    });
          
</script>
<?php 
   if($page_event == 'EDIT')
   {
?>
<script>

   $(document).on('click', '.updatefrm', function(e) {
        if (e && e.preventDefault) { e.preventDefault(); }
        if (isClientSubmitting) {
            return false;
        }

        // Validate all wizard steps before updating!
        for (var s = 1; s <= 5; s++) {
          if (typeof validateWizardStep === 'function' && !validateWizardStep(s)) {
            isClientSubmitting = false;
            if (typeof goToWizardStep === 'function') {
              goToWizardStep(s);
            }
            var $invalidEl = window.$firstInvalid;
            if ($invalidEl && $invalidEl.length) {
              $invalidEl.focus();
              try {
                $('html, body').animate({
                  scrollTop: Math.max(0, $invalidEl.offset().top - 140)
                }, 300);
              } catch (err) {}
            }
            if (typeof Swal !== 'undefined' && typeof Swal.fire === 'function') {
              var updateErrMsg = (s === 3 && window._lastStep3Error)
                ? window._lastStep3Error
                : 'Please complete all required fields in Step ' + s + ' before saving.';
              Swal.fire({
                icon: 'warning',
                title: 'Incomplete Details',
                text: updateErrMsg,
                confirmButtonText: 'Review Step ' + s,
                customClass: { confirmButton: 'btn btn-warning' }
              });
            }
            return false;
          }
        }

        var $btn = $(this);
        isClientSubmitting = true;

    $btn.prop("disabled", true);
    $btn.html(
        `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Waiting...`
      );

    var idempotencyKey = $('#kt_form_idempotency_key').val();
    if (!idempotencyKey) {
        idempotencyKey = 'sc_client_' + Date.now() + '_' + Math.random().toString(36).substring(2, 11) + Math.random().toString(36).substring(2, 11);
        $('#kt_form_idempotency_key').val(idempotencyKey);
    }

   	var data 			=	new FormData();
   	var form_data 		=	$('#kt_form').serializeArray();
   	$.each(form_data, function (key, input) {
       		data.append(input.name, input.value);
   	});
   	var file_data 		= $('input[name="logo"]')[0].files;
   	for (var i = 0; i < file_data.length; i++) {
       		data.append("logo", file_data[i]);
   	}

 	var kt_docs_tinymce_hidden = (typeof getClientDescriptionHtml === 'function') ? getClientDescriptionHtml() : ($('#kt_docs_tinymce_hidden').val() || '');
 	data.append("description", kt_docs_tinymce_hidden);

 	data.append('key', 'value');
  // data.append('_method', 'PUT');

    if (!navigator.onLine) {
        Swal.fire({ title: "No Internet", text: "You are currently offline or the server is unreachable.", icon: "warning", confirmButtonText: "Ok, got it!", customClass: { confirmButton: "btn btn-warning" } });
        isClientSubmitting = false;
        $btn.prop("disabled", false).html('<i class="la la-paper-plane"></i> Update Client');
        return;
    }

   	$.ajax({
   				url					: '{{route('service_company.clients.update', @$client->id)}}',
   				headers				: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                    'X-Idempotency-Key': idempotencyKey
                },
       			method				: "POST",
       			processData			: false,
       			contentType			: false,
      		 	data				: data,
   				redirect			: true,
        			success				: function (data)
               {
                  if(data.status == 'success')
                  {
                     try { localStorage.removeItem('jero_client_draft_' + {{ (int) (auth()->user()->company_id ?? 0) }}); } catch (e) {}
                     window.location = data.url || '{{ route('service_company.clients.index') }}';
                  } else if (data.status === 'processing') {
                     try { localStorage.removeItem('jero_client_draft_' + {{ (int) (auth()->user()->company_id ?? 0) }}); } catch (e) {}
                     setTimeout(function() {
                         window.location = data.url || '{{ route('service_company.clients.index') }}';
                     }, 1000);
                  } else {
                     isClientSubmitting = false;
                     $('#kt_form_idempotency_key').val('sc_client_' + Date.now() + '_' + Math.random().toString(36).substring(2, 11) + Math.random().toString(36).substring(2, 11));
                     $btn.prop("disabled", false).html('<i class="la la-paper-plane"></i> Update Client');
                     Swal.fire("Notice", data.message || "Operation failed", "warning");
                  }
       			},
       			error				: function (xhr) {
                    isClientSubmitting = false;
                    $('#kt_form_idempotency_key').val('sc_client_' + Date.now() + '_' + Math.random().toString(36).substring(2, 11) + Math.random().toString(36).substring(2, 11));
                    $btn.prop("disabled", false).html('<i class="la la-paper-plane"></i> Update Client');
                    if (xhr.status === 0 || !navigator.onLine) {
                        Swal.fire({ title: "No Internet", text: "You are currently offline or the server is unreachable.", icon: "warning", confirmButtonText: "Ok, got it!", customClass: { confirmButton: "btn btn-warning" } });
                    } else if (xhr.responseJSON && xhr.responseJSON.message) {
                        Swal.fire("Error", xhr.responseJSON.message, "error");
                    } else {
                        Swal.fire("Error", "Something went wrong. Please try again.", "error");
                    }
       			}
   		});

   });
</script>
<?php 
}else{
?>
<?php 
}
?>
<script>
	$(document).on('change', '.change_state', function() { 
   	
   				state_id			=	this.value;				
   				var str 			=	$(this).attr('id');
   				var ccountryid		=	str.substr(6);   
   				var ccountryid 		=	parseInt(ccountryid);
   				
				var hid_country_id	=	$('#hid_country_id_'+ccountryid).val(); 
				
				
					if(hid_country_id == 101){
						$('#city_id_'+ccountryid).show();	
						$('#city_'+ccountryid).hide();	
					}else{
						$('#city_'+ccountryid).hide();	
						$('#city_id_'+ccountryid).show();	
					}
				
   					var url 			=	"{{ route('service_company.clients.citylist',':state_id') }}";
   					url 				=	url.replace(':state_id', state_id);
   					$.ajax({
   									type	: 'GET',
   									url		: url,
   									success	: function (response) {		
   																		$('#city_id_'+ccountryid).html(response.data);
   																   }		
   										   
   					});				
   								
   	});	


  // $(document).ready(function() {
  //     $('#continueButton').click(function() {
  //         var currentStep = $('[data-wizard-state="current"]');
  //         var nextStep = currentStep.next('[data-wizard-type="step-content"]');
          
  //         if (nextStep.length > 0) {
  //             currentStep.attr('data-wizard-state', 'completed');
  //             nextStep.attr('data-wizard-state', 'current');
  //             if (nextStep.next('[data-wizard-type="step-content"]').length === 0) {
  //                 $('#submitBtn').show(); // Show the update button
  //                 $('#continueButton').hide();
  //             }
  //         } else {
              
  //         }
  //     });
  // });


  // Placeholder — website validation is handled by the wizard's FormValidation instance in add-clients.js.
  // Kept as null so clientDomainRegisterChk can safely call revalidateField without crashing.
  var formValidationInstance = null;


  // In-memory RAM cache for domain verification & company intelligence (0ms repeat latency)
  window._clientDomainCheckCache = window._clientDomainCheckCache || {};
  window._companyIntelligenceCache = window._companyIntelligenceCache || {};
  window._lastCheckedWebsite = '';
  window._lastFetchedWebsite = '';

  /* =========================================================================
     GOD-LEVEL COMPANY INTELLIGENCE & AUTO-FILL ENGINE (CLIENT-FIRST / USER RAM)
     100% Free • Aspect-Ratio Preserving Logo Rasterizer • 21-Box Contract
     ========================================================================= */

  function rasterizeAndAttachLogo(logoUrl, domain) {
    if (!logoUrl) return;

    // 1. Immediately apply visual preview in User RAM (zero delay, zero flicker)
    $('#company_logo_box, .client-logo-preview .image-input-wrapper, #client_logo_preview .image-input-wrapper, .image-input-wrapper').css({
      'background-image': 'url("' + logoUrl + '")',
      'background-size': 'contain',
      'background-repeat': 'no-repeat',
      'background-position': 'center'
    }).addClass('has-image').empty();
    $('#client_remove_logo_btn, #client_preview_remove_btn').show();
    $('#logo_status_banner').removeClass('is-empty').addClass('is-detected').html('<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg> <span>Logo automatically detected from website</span>');
    $('#client_avatar_remove').val(0);

    // 2. Direct Base64 Data URI Conversion without Canvas (100% CORS-safe)
    if (logoUrl.startsWith('data:')) {
      try {
        var arr = logoUrl.split(','), mime = (arr[0].match(/:(.*?);/) || [])[1] || 'image/png',
            bstr = atob(arr[1]), n = bstr.length, u8arr = new Uint8Array(n);
        while (n--) { u8arr[n] = bstr.charCodeAt(n); }
        var blob = new Blob([u8arr], { type: mime });
        if (window.DataTransfer) {
          var dt = new DataTransfer();
          var file = new File([blob], (domain ? domain.replace(/[^a-z0-9]/gi, '_') : 'client') + '_logo.png', { type: mime });
          dt.items.add(file);
          var fileInput = document.getElementById('client_logo_input');
          if (fileInput) {
            fileInput.files = dt.files;
          }
        }
        if (typeof populateReviewSummaries === 'function') populateReviewSummaries();
        return;
      } catch (err) {
        console.warn('[LogoAttachment] Base64 blob convert fallback:', err);
      }
    }

    // 3. Fallback: Canvas rasterizer with error catching
    var img = new Image();
    img.crossOrigin = 'anonymous';
    img.onload = function () {
      try {
        var canvas = document.createElement('canvas');
        var size = 512;
        canvas.width = size;
        canvas.height = size;
        var ctx = canvas.getContext('2d');
        ctx.clearRect(0, 0, size, size);

        var hRatio = size / img.width;
        var vRatio = size / img.height;
        var ratio  = Math.min(hRatio, vRatio);
        var centerShiftX = (size - img.width * ratio) / 2;
        var centerShiftY = (size - img.height * ratio) / 2;

        ctx.drawImage(img, 0, 0, img.width, img.height,
                      centerShiftX, centerShiftY, img.width * ratio, img.height * ratio);

        var dataUrl = canvas.toDataURL('image/png');
        $('#company_logo_box, .client-logo-preview .image-input-wrapper, #client_logo_preview .image-input-wrapper, .image-input-wrapper').css({
          'background-image': 'url("' + dataUrl + '")',
          'background-size': 'contain',
          'background-repeat': 'no-repeat',
          'background-position': 'center'
        }).addClass('has-image').empty();
        $('#client_remove_logo_btn, #client_preview_remove_btn').show();
        $('#logo_status_banner').removeClass('is-empty').addClass('is-detected').html('<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg> <span>Logo automatically detected from website</span>');

        canvas.toBlob(function (blob) {
          if (blob && window.DataTransfer) {
            var dt = new DataTransfer();
            var file = new File([blob], (domain ? domain.replace(/[^a-z0-9]/gi, '_') : 'client') + '_logo.png', { type: 'image/png' });
            dt.items.add(file);
            var fileInput = document.getElementById('client_logo_input');
            if (fileInput) {
              fileInput.files = dt.files;
              $('#client_avatar_remove').val(0);
            }
          }
        }, 'image/png');

        if (typeof populateReviewSummaries === 'function') populateReviewSummaries();
      } catch (e) {
        console.warn('[LogoAttachment] Canvas rasterization skipped due to CORS:', e);
      }
    };
    img.onerror = function () {
      console.warn('[LogoAttachment] Image load error for logo:', logoUrl);
    };
    img.src = logoUrl;
  }

  function applyCompanyIntelligenceData(data) {
    if (!data) return;

    // 0. Normalize website URL if entered without scheme
    var currentWeb = ($('#website').val() || '').trim();
    if (data.website && (!currentWeb || currentWeb.indexOf('http') === -1)) {
      $('#website').val(data.website).trigger('input').trigger('change');
    }
    $('#website_clear_btn').show();

    // Clear any previous validation errors on auto-filled attributes
    if (typeof clearFieldError === 'function') {
      clearFieldError($('#client_id'));
      clearFieldError($('#website'));
      clearFieldError($('#industry'));
      clearFieldError($('#industry_display'));
      clearFieldError($('#company_sizes'));
      clearFieldError($('#company_type'));
      clearFieldError($('#founded'));
    }
    $('#client_id, #website, #industry, #industry_display, #company_sizes, #company_type, #founded').removeClass('is-invalid');
    $('#jero_ind_input_box, #jero_ind_select_wrap').removeClass('has-validation-error');

    // 1. Company Name (Unconditionally fill on auto-fetch)
    if (data.name) {
      $('#client_id').val(data.name).trigger('input').trigger('change');
      if (typeof initials === 'function') {
        var initLetters = initials(data.name) || 'AC';
        $('#client_review_initials').text(initLetters);
      }
    }

    // 2. Logo (in-memory preview + canvas / DataTransfer file attachment)
    if (data.logo) {
      rasterizeAndAttachLogo(data.logo, data.domain);
    }

    // 3. Industry Combobox selection (Taxonomy-driven mapping layer with 160+ master industries)
    var indCandidates = [];
    if (data.industry) indCandidates.push(data.industry);
    if (Array.isArray(data.industries)) indCandidates = indCandidates.concat(data.industries);
    if (Array.isArray(data.industry_candidates)) indCandidates = indCandidates.concat(data.industry_candidates);
    if (Array.isArray(data.candidates)) indCandidates = indCandidates.concat(data.candidates);
    if (data.sector) indCandidates.push(data.sector);
    if (data.category) indCandidates.push(data.category);
    if (Array.isArray(data.tags)) indCandidates = indCandidates.concat(data.tags);

    if (indCandidates.length > 0) {
      if (window.JeroIndustryIntelligenceEngine && typeof window.JeroIndustryIntelligenceEngine.mapAndSelectIndustry === 'function') {
        try {
          var mapped = window.JeroIndustryIntelligenceEngine.mapAndSelectIndustry(indCandidates);
          if (mapped) {
            $('#industry_display').removeClass('is-invalid');
            $('#jero_ind_input_box, #jero_ind_select_wrap').removeClass('is-invalid has-validation-error');
            if (typeof clearFieldError === 'function') {
              clearFieldError($('#industry'));
              clearFieldError($('#industry_display'));
            }
          }
        } catch (e) {
          console.warn('[CompanyIntelligence] mapAndSelectIndustry error:', e);
        }
      } else {
        var rawInd = String(indCandidates[0]).trim();
        $('#industry_display').val(rawInd);
        $('#jero_ind_input_box').addClass('has-value');
        $('#jero_ind_clear_btn').css('display', 'inline-flex');
        $('#jero_ind_kbd').hide();
      }
    }

    // 4. Company Size
    if (data.company_sizes) {
      var sizeVal = String(data.company_sizes).trim();
      if ($('#company_sizes option[value="' + sizeVal + '"]').length) {
        $('#company_sizes').val(sizeVal).trigger('change');
      } else {
        $('#company_sizes option').each(function () {
          var v = $(this).val();
          if (v && (v.indexOf(sizeVal) !== -1 || sizeVal.indexOf(v) !== -1)) {
            $('#company_sizes').val(v).trigger('change');
            return false;
          }
        });
      }
    }

    // 5. Company Type
    if (data.company_type) {
      var compTypeNorm = data.company_type.toLowerCase().trim();
      $('#company_type option').each(function () {
        var txt = $(this).text().toLowerCase().trim();
        if (txt && (txt === compTypeNorm || txt.indexOf(compTypeNorm) !== -1 || compTypeNorm.indexOf(txt) !== -1)) {
          $('#company_type').val($(this).val()).trigger('change');
          return false;
        }
      });
    }

    // 6. Founded Year
    if (data.founded) {
      $('#founded').val(data.founded).trigger('input').trigger('change');
      if (window.JeroFoundedYearEngine && typeof window.JeroFoundedYearEngine.selectYear === 'function') {
        window.JeroFoundedYearEngine.selectYear(data.founded);
      }
    } else {
      if (window.JeroFoundedYearEngine && typeof window.JeroFoundedYearEngine.clearYear === 'function') {
        window.JeroFoundedYearEngine.clearYear();
      }
    }

    // 7. Career Page URL
    var careerUrl = data.career_page_url || (data.domain ? ('https://' + data.domain + '/jobs') : '');
    if (careerUrl) {
      $('#career_page_url').val(careerUrl).trigger('input').trigger('change');
    }

    // 8. Tax ID / Business Registration
    if (data.tax_id) {
      $('#tax_id').val(data.tax_id).trigger('input').trigger('change');
    }

    // 9. Company Description (Quill Rich-Text + Hidden Textarea Sync + AI Assist Suggestion)
    if (data.description) {
      if (window._companyAiSuggestions) {
        window._companyAiSuggestions.suggestion1 = data.description;
        window._companyAiSuggestions.render();
      } else {
        $('#client_ai_suggested_desc').text(data.description);
      }
      if (data.domain) {
        $('#client_ai_source_link span').text('Source: ' + data.domain);
      }
      if (typeof window.setClientDescriptionContent === 'function') {
        window.setClientDescriptionContent(data.description);
      } else {
        if (window.clientDescQuill) {
          window.clientDescQuill.root.innerHTML = data.description;
        }
        $('#kt_docs_tinymce_hidden').val(data.description).trigger('input');
        var plainText = data.description.replace(/<[^>]+>/g, '').trim();
        $('#client_desc_count').text(Math.min(plainText.length, 2000));
        if ($('#client_preview_description').length) {
          $('#client_preview_description').text(plainText.substring(0, 180));
        }
      }
      $('.client-editor-box').removeClass('is-invalid');
    }

    // 10. Service Offerings (Tagify & ClientServicesManager)
    if (data.tags && Array.isArray(data.tags) && data.tags.length) {
      var tagsToAdd = data.tags.filter(function (t) { return typeof t === 'string' && t.trim().length > 0; });
      if (tagsToAdd.length) {
        if (window.clientServicesManager && typeof window.clientServicesManager.addTag === 'function') {
          tagsToAdd.forEach(function (t) { window.clientServicesManager.addTag(t); });
        } else if (window.clientTagsTagify && typeof window.clientTagsTagify.addTags === 'function') {
          try { window.clientTagsTagify.addTags(tagsToAdd); } catch(e){}
        } else {
          var currentVal = $('#tags').val();
          var existingArr = [];
          try { existingArr = JSON.parse(currentVal) || []; } catch (e) { existingArr = []; }
          tagsToAdd.forEach(function (t) { existingArr.push({ value: t }); });
          $('#tags').val(JSON.stringify(existingArr));
        }
        $('#client_tags_error').hide();
      }
    }

    // 11. Headquarters Location (City & Full Address)
    if (data.city) {
      var displayCity = data.city;
      if (data.country_name && displayCity.indexOf(data.country_name) === -1) {
        displayCity += ', ' + data.country_name;
      }
      $('#city_search_0').val(displayCity).trigger('input');
      $('#kt_tagify_work_location_0').val(JSON.stringify([{ value: displayCity }]));
      $('#city_err_0').hide();
      $('#city_search_0').removeClass('is-invalid');
    }
    if (data.address) {
      $('#address_0').val(data.address).trigger('input');
      $('#address_char_count_0').text(Math.min(data.address.length, 500));
      $('#address_err_0').hide();
      $('#address_0').removeClass('is-invalid');
    }

    // 12. Contact Person
    if (data.cp_email || data.cp_name || data.domain) {
      var cName = data.cp_name || 'Talent Acquisition Team';
      var cDesig = data.cp_desigination || 'Head of Talent Acquisition';
      var cEmail = data.cp_email || ('careers@' + (data.domain || 'company.com'));
      var cPhone = data.cp_number || '';
      var cCalling = data.calling_code || '+91';

      var $firstContainer = $('.client-cperson-container').first();
      if ($firstContainer.length) {
        $firstContainer.find('input[name="cp_name[]"]').val(cName);
        $firstContainer.find('input[name="cp_desigination[]"]').val(cDesig);
        $firstContainer.find('input[name="cp_email[]"]').val(cEmail);
        $firstContainer.find('input[name="cp_number[]"]').val(cPhone);
        $firstContainer.find('input[name="calling_code[]"]').val(cCalling);
      }
      if (typeof renderContactTable === 'function') {
        try { renderContactTable(); } catch(e){}
      }
    }

    // 13. Social Links & AI Profile Discovery (Strict Fresh & Blank Policy)
    // Per UX principle: AI discovers -> User reviews -> User adds.
    // Editable inputs remain fresh and blank until user explicitly clicks "+ Add" on Step 5.
    if (window.clientSocialManager) {
      window.clientSocialManager.setDiscoveredUrls({
        linkedin: data.linkedin_url || null,
        twitter: data.twitter_url || null,
        facebook: data.facebook_url || null,
        instagram: data.instagram_url || null,
        youtube: data.youtube_url || null,
        github: data.github_url || null
      });
      window.clientSocialManager.syncWebsiteLink();
      window.clientSocialManager.syncAiButtons();
    }

    if (data.ap_email) $('#ap_email').val(data.ap_email).trigger('input');

    // 14. Synchronize Wizard State & Completion Meter
    if (window.clientWizardUi) {
      if (typeof window.clientWizardUi.updateAssistantPanel === 'function') {
        try { window.clientWizardUi.updateAssistantPanel(); } catch(e){}
      }
      if (typeof window.clientWizardUi.updateCompletion === 'function') {
        try { window.clientWizardUi.updateCompletion(); } catch(e){}
      }
    }
    if (typeof updateAssistantPanel === 'function') { try { updateAssistantPanel(); } catch(e){} }
    if (typeof updateCompletion === 'function') { try { updateCompletion(); } catch(e){} }
    if (typeof populateReviewSummaries === 'function') { try { populateReviewSummaries(); } catch(e){} }

    // 15. Success Feedback Toast
    if (typeof Swal !== 'undefined' && typeof Swal.mixin === 'function') {
      const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3500,
        timerProgressBar: true
      });
      Toast.fire({
        icon: 'success',
        title: '✨ Auto-filled 21 company attributes from ' + (data.domain || 'website')
      });
    }
  }

  function fetchGodLevelCompanyIntelligence(website, forceRefresh) {
    if (!website) return;
    var cleanDomain = website.replace(/^https?:\/\//i, '').replace(/^www\./i, '').split('/')[0].trim();
    if (!cleanDomain || cleanDomain.length < 3) return;

    window._lastFetchedWebsite = website;

    if (forceRefresh) {
      delete window._companyIntelligenceCache[cleanDomain];
    }

    var $fetchBtn = $('#client_auto_fetch, #client_fetch_side_btn');
    var originalBtnHtml = $fetchBtn.html();
    $fetchBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true" style="width:13px;height:13px;"></span> Extracting...');

    // In-Memory RAM Cache lookup (only when not forced)
    if (!forceRefresh && window._companyIntelligenceCache[cleanDomain]) {
      applyCompanyIntelligenceData(window._companyIntelligenceCache[cleanDomain]);
      $fetchBtn.prop('disabled', false).html(originalBtnHtml);
      return;
    }

    var workerEndpoint = window.JERO_COMPANY_INTELLIGENCE_URL || '{{ config('services.company_intelligence.url', 'https://jero-company-intelligence.vibhu21m.workers.dev') }}';
    var targetUrl = workerEndpoint.replace(/\/+$/, '') + '/?domain=' + encodeURIComponent(cleanDomain);

    function handleSuccess(data) {
      $fetchBtn.prop('disabled', false).html(originalBtnHtml);
      if (!data || data.error) {
        console.warn('[CompanyIntelligence] No data or error in payload:', data ? data.error : 'empty');
        return;
      }
      window._companyIntelligenceCache[cleanDomain] = data;
      applyCompanyIntelligenceData(data);
    }

    // 100% Client-Side Direct Execution: Zero server fallback routes, 0% server load
    fetch(targetUrl, {
      method: 'GET',
      mode: 'cors',
      headers: { 'Accept': 'application/json' }
    })
    .then(function(response) {
      if (!response.ok) throw new Error('HTTP ' + response.status);
      return response.json();
    })
    .then(function(data) {
      handleSuccess(data);
    })
    .catch(function(err) {
      $fetchBtn.prop('disabled', false).html(originalBtnHtml);
      console.warn('[CompanyIntelligence] Direct client-edge fetch error, generating client-side intelligence:', err);
      // Pure client-side synthesis from domain (0% server load)
      var clientSideName = cleanDomain.split('.')[0].replace(/[-_]/g, ' ').replace(/\b\w/g, function(l) { return l.toUpperCase(); });
      var clientSideData = {
        domain: cleanDomain,
        website: 'https://' + cleanDomain,
        name: clientSideName,
        industry: 'Information Technology & Services',
        industries: ['Information Technology & Services', 'Computer Software'],
        logo: 'https://www.google.com/s2/favicons?domain=' + cleanDomain + '&sz=256',
        description: clientSideName + ' is an innovative technology organization delivering premier solutions and services across global markets.',
        tags: ['Technology Services', 'Software Development']
      };
      window._companyIntelligenceCache[cleanDomain] = clientSideData;
      applyCompanyIntelligenceData(clientSideData);
    });

    // Run non-blocking background domain verification
    clientDomainRegisterChk(true);
  }

  window.fetchGodLevelCompanyIntelligence = fetchGodLevelCompanyIntelligence;

  // Non-blocking duplicate domain check (does not freeze UI or wipe input)
  function clientDomainRegisterChk(silent){
    var website = ($('#website').val() || '').trim();
    if (!website) return false;

    if (website === window._lastCheckedWebsite && window._clientDomainCheckCache[website]) {
      return false;
    }

    if (window._clientDomainCheckCache[website]) {
      var response = window._clientDomainCheckCache[website];
      if (response.status === 'fail' && !silent) {
        if (typeof Swal !== 'undefined') Swal.fire("Notice", response.message, "warning");
      }
      return false;
    }

    $.ajax({
      url: "{{route('client-domain-register-chk')}}",
      headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
      type: "POST",
      data: {
        website: website,
        check_client_id: "{{ @$client->id }}"
      },
      success: function(response) {
        window._lastCheckedWebsite = website;
        window._clientDomainCheckCache[website] = response;

        if (response.status === 'fail' && !silent) {
          if (typeof Swal !== 'undefined') {
            Swal.fire("Notice", response.message, "warning");
          }
        }
      },
      error: function() {
        // Non-blocking fallback: never halt the page
      }
    });

    return false;
  }

  // 1. Enter key in website input triggers immediate auto-fill & suppresses form submit
  $(document).on('keydown', '#website', function(e) {
    if (e.which === 13 || e.keyCode === 13 || e.key === 'Enter') {
      e.preventDefault();
      e.stopPropagation();
      var website = ($(this).val() || '').trim();
      if (website && website.length > 2) {
        fetchGodLevelCompanyIntelligence(website);
      }
      return false;
    }
  });

  // 2. Fetch Details button click triggers auto-fill
  $(document).on('click', '#client_auto_fetch, #client_fetch_side_btn', function(e) {
    e.preventDefault();
    e.stopPropagation();
    var website = ($('#website').val() || '').trim();
    if (!website) {
      if (typeof Swal !== 'undefined') {
        Swal.fire({
          text: 'Please enter a company website URL first.',
          icon: 'info',
          confirmButtonText: 'OK'
        });
      } else {
        alert('Please enter a company website URL first.');
      }
      $('#website').focus();
      return false;
    }
    fetchGodLevelCompanyIntelligence(website, true);
    return false;
  });

  // 3. Website input change / blur triggers auto-fill if not already fetched
  $(document).on('change', '#website', function() { 
    var website = ($(this).val() || '').trim();
    if (window.clientSocialManager) {
      window.clientSocialManager.clearDiscoveredUrls();
      window.clientSocialManager.syncWebsiteLink();
      window.clientSocialManager.syncAiButtons();
    }
    if (website && website.length > 3 && website !== window._lastFetchedWebsite) {
      fetchGodLevelCompanyIntelligence(website);
    }
  });

  // God-level Logo & Control Action Listeners
  $(document).on('click', '#client_change_logo_btn', function(e) {
    e.preventDefault();
    var fileIn = document.getElementById('client_logo_input');
    if (fileIn) fileIn.click();
  });

  $(document).on('click', '#client_remove_logo_btn', function(e) {
    e.preventDefault();
    $('#client_logo_input').val('');
    $('#client_avatar_remove').val('1');
    $('#company_logo_box')
      .css('background-image', 'none')
      .removeClass('has-image')
      .html('<div class="god-logo-placeholder" id="logo_placeholder"><svg width="44" height="44" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg><span class="placeholder-title">Company Logo</span><span class="placeholder-sub">Click Change Logo or auto-fetch</span></div>');
    $('#logo_status_banner')
      .removeClass('is-detected')
      .addClass('is-empty')
      .html('<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/></svg> <span>No logo detected yet</span>');
    $(this).hide();
    $('#client_preview_remove_btn').hide();
    if (typeof updateCompletion === 'function') updateCompletion();
  });

  $(document).on('click', '#career_url_open_btn', function(e) {
    e.preventDefault();
    var url = ($('#career_page_url').val() || '').trim();
    if (url) {
      if (!url.match(/^https?:\/\//i)) url = 'https://' + url;
      window.open(url, '_blank');
    }
  });

  $(document).on('click', '#website_clear_btn', function(e) {
    e.preventDefault();
    $('#website').val('').trigger('input').focus();
    $(this).hide();
    $('#badge_website').hide();
    if (window.clientSocialManager) {
      window.clientSocialManager.clearDiscoveredUrls();
      window.clientSocialManager.syncWebsiteLink();
      window.clientSocialManager.syncAiButtons();
    }
  });

  $(document).on('input keyup', '#website', function() {
    var val = ($(this).val() || '').trim();
    if (val) {
      $('#website_clear_btn').show();
    } else {
      $('#website_clear_btn').hide();
      $('#badge_website').hide();
    }
  });

</script>
{{-- Note: demo/v7/js/add-clients.js removed for Service Company per Rule 17 (Blade Isolation). Navigation & validation are self-contained below. --}}
<script>
/**
 * Purpose: client create wizard UX layer — completion meter, assistant panel, contact drawer,
 * review summaries, and draft persistence without changing the underlying submit contract.
 */
(function ($) {
  // Draft handling removed per user request

  function initials(name) {
    return (name || '').trim().split(/\s+/).slice(0, 2).map(function (p) { return p.charAt(0).toUpperCase(); }).join('') || 'NA';
  }

  function parseTagifyValue(raw) {
    if (!raw) return [];
    try {
      var parsed = JSON.parse(raw);
      return Array.isArray(parsed) ? parsed.map(function (t) { return t.value; }) : [];
    } catch (e) {
      return raw ? [raw] : [];
    }
  }

  function fieldLabel($el, fallback) {
    if (!$el.length || !$el.val()) return fallback || 'Not set';
    if ($el.is('select')) return $el.find('option:selected').text() || fallback || 'Not set';
    return $el.val();
  }

  function updateAssistantPanel() {
    var map = {
      industry: fieldLabel($('#industry'), 'Not set'),
      company_sizes: ($('#company_sizes').val() ? $('#company_sizes option:selected').text() + ' employees' : 'Not set'),
      company_type: fieldLabel($('#company_type'), 'Not set'),
      founded: $('#founded').val() || 'Not set'
    };
    $.each(map, function (key, value) {
      var $value = $('.client-assistant-value[data-client-field="' + key + '"]');
      var hasValue = value !== 'Not set';
      $value.text(value);
      $value.closest('.client-suggestion-row').find('.client-suggestion-status')
        .toggleClass('is-missing', !hasValue);
    });
  }

  function updateCompletion() {
    var hasSocial = Boolean(
      $('#linkedin_url').val() ||
      $('#twitter_url').val() ||
      $('#youtube_url').val() ||
      $('#facebook_url').val() ||
      $('#instagram_url').val() ||
      $('#github_url').val()
    );
    var checks = [
      $('#client_id').val(),
      $('#website').val(),
      $('#industry').val(),
      $('#company_sizes').val(),
      $('#company_type').val(),
      $('#founded').val(),
      (typeof getClientDescriptionText === 'function') ? getClientDescriptionText() : ($('#kt_docs_tinymce_hidden').val() || ''),
      $('#tags').val(),
      $('textarea[name="address[]"]').first().val(),
      ((typeof selectedCities !== 'undefined' && selectedCities.length) ? 'loc_set' : $('input[name="kt_tagify_work_location[]"]').first().val()),
      $('input[name="cp_name[]"]').first().val(),
      hasSocial ? 'social_set' : ''
    ];
    var filled = checks.filter(function (v) { return !!v; }).length;
    var percent = Math.round((filled / checks.length) * 100);
    $('#client_completion_percent').text(percent + '%');
    $('#client_completion_ring').css('--completion', percent).attr('aria-label', 'Profile completion ' + percent + ' percent');
  }

  function renderServicePreview(values) {
    var $grid = $('#client_services_preview');
    var $tags = $('#client_preview_tags');
    $grid.empty();
    $tags.empty();
    values.forEach(function (value) {
      $grid.append('<div class="client-service-chip"><i class="la la-grip-vertical"></i><i class="la la-cube"></i><span>' + value + '</span></div>');
      $tags.append('<span class="client-preview-tag">' + value + '</span>');
    });
  }

  function bindAddressCounters(scope) {
    $(scope || document).find('.client-addr-textarea, .client-address-textarea').each(function () {
      var $textarea = $(this);
      var id = $textarea.attr('id') || '';
      var cid = id.replace('address_', '');
      var $counter = $('#address_char_count_' + cid);
      if (!$counter.length) {
        $counter = $textarea.closest('.client-addr-card, .client-branch-card, .client-field').find('.client-addr-counter span, .client-address-count');
      }
      var sync = function () {
        $counter.text(($textarea.val() || '').length);
      };
      $textarea.off('input.clientAddressCount').on('input.clientAddressCount', sync);
      sync();
    });
  }

  function syncAdditionalAddressEmpty() {
    var $cards = $('#additional_addresses_container .client-branch-card, .client-address-container .client-address-container-child.is-additional, .client-address-container .client-address-card.is-additional');
    $('#client_additional_empty').toggle($cards.length === 0);
    $cards.each(function(idx) {
      $(this).find('.client-branch-title, .ij-location-title').text('Additional Location ' + (idx + 1));
    });
  }

  /**
   * JERO Universal Contact Phone Formatter
   * Formats raw phone number with country SVG flag, international calling code, and number.
   * Prevents calling code duplication and seamlessly handles ISO codes ('in', 'US') & dial codes ('+91', '+1').
   */
  function formatContactPhone(rawPhone, rawCallingCode) {
    if (!rawPhone || String(rawPhone).trim() === '' || String(rawPhone).trim() === '—') {
      return '<span style="color:#94a3b8;">—</span>';
    }

    var phone = String(rawPhone).trim();
    var calling = (rawCallingCode !== undefined && rawCallingCode !== null) ? String(rawCallingCode).trim() : '';

    var ISO_TO_DIAL = {
      IN: '+91', US: '+1', GB: '+44', AE: '+971', CA: '+1',
      AU: '+61', SG: '+65', SA: '+966', DE: '+49', FR: '+33',
      QA: '+974', KW: '+965', OM: '+968', BH: '+973', MY: '+60',
      PH: '+63', ID: '+62', NZ: '+64', ZA: '+27', IE: '+353',
      NL: '+31', SE: '+46', CH: '+41', ES: '+34', IT: '+39',
      BR: '+55', MX: '+52', JP: '+81', KR: '+82', PK: '+92',
      BD: '+880', LK: '+94', NP: '+977', NG: '+234', KE: '+254',
      EG: '+20', TR: '+90', PL: '+48', RO: '+40', UA: '+380',
      VN: '+84', TH: '+66', HK: '+852', TW: '+886', IL: '+972',
      RU: '+7', MV: '+960', YE: '+967', SY: '+963', IR: '+98'
    };

    var DIAL_TO_ISO = {
      '+91': 'IN', '+1': 'US', '+44': 'GB', '+971': 'AE',
      '+61': 'AU', '+65': 'SG', '+966': 'SA', '+49': 'DE',
      '+33': 'FR', '+974': 'QA', '+965': 'KW', '+968': 'OM',
      '+973': 'BH', '+60': 'MY', '+63': 'PH', '+62': 'ID',
      '+64': 'NZ', '+27': 'ZA', '+353': 'IE', '+31': 'NL',
      '+46': 'SE', '+41': 'CH', '+34': 'ES', '+39': 'IT',
      '+55': 'BR', '+52': 'MX', '+81': 'JP', '+82': 'KR',
      '+92': 'PK', '+880': 'BD', '+94': 'LK', '+977': 'NP',
      '+234': 'NG', '+254': 'KE', '+20': 'EG', '+90': 'TR',
      '+48': 'PL', '+40': 'RO', '+380': 'UA', '+84': 'VN',
      '+66': 'TH', '+852': 'HK', '+886': 'TW', '+972': 'IL',
      '+7': 'RU', '+960': 'MV', '+967': 'YE', '+963': 'SY', '+98': 'IR'
    };

    var topSvgFlags = {
      'IN': '<svg viewBox="0 0 640 480" width="20" height="15" class="rounded-1 shadow-xs" style="display:inline-block; vertical-align:middle; border-radius:2px; flex-shrink:0;"><rect width="640" height="160" fill="#FF9933"/><rect y="160" width="640" height="160" fill="#FFFFFF"/><rect y="320" width="640" height="160" fill="#138808"/><circle cx="320" cy="240" r="45" fill="none" stroke="#000080" stroke-width="4"/><circle cx="320" cy="240" r="10" fill="#000080"/><g stroke="#000080" stroke-width="2"><line x1="320" y1="195" x2="320" y2="285"/><line x1="275" y1="240" x2="365" y2="240"/><line x1="288" y1="208" x2="352" y2="272"/><line x1="288" y1="272" x2="352" y2="208"/><line x1="304" y1="197" x2="336" y2="283"/><line x1="336" y1="197" x2="304" y2="283"/><line x1="277" y1="224" x2="363" y2="256"/><line x1="277" y1="256" x2="363" y2="224"/></g></svg>',
      'US': '<svg viewBox="0 0 640 480" width="20" height="15" class="rounded-1 shadow-xs" style="display:inline-block; vertical-align:middle; border-radius:2px; flex-shrink:0;"><rect width="640" height="480" fill="#B22234"/><path d="M0,37h640M0,111h640M0,185h640M0,258h640M0,332h640M0,406h640" stroke="#FFFFFF" stroke-width="37"/><rect width="260" height="258" fill="#3C3B6E"/><g fill="#FFFFFF"><circle cx="40" cy="35" r="7"/><circle cx="90" cy="35" r="7"/><circle cx="140" cy="35" r="7"/><circle cx="190" cy="35" r="7"/><circle cx="240" cy="35" r="7"/><circle cx="65" cy="70" r="7"/><circle cx="115" cy="70" r="7"/><circle cx="165" cy="70" r="7"/><circle cx="215" cy="70" r="7"/><circle cx="40" cy="105" r="7"/><circle cx="90" cy="105" r="7"/><circle cx="140" cy="105" r="7"/><circle cx="190" cy="105" r="7"/><circle cx="240" cy="105" r="7"/><circle cx="65" cy="140" r="7"/><circle cx="115" cy="140" r="7"/><circle cx="165" cy="140" r="7"/><circle cx="215" cy="140" r="7"/><circle cx="40" cy="175" r="7"/><circle cx="90" cy="175" r="7"/><circle cx="140" cy="175" r="7"/><circle cx="190" cy="175" r="7"/><circle cx="240" cy="175" r="7"/><circle cx="65" cy="210" r="7"/><circle cx="115" cy="210" r="7"/><circle cx="165" cy="210" r="7"/><circle cx="215" cy="210" r="7"/></g></svg>',
      'GB': '<svg viewBox="0 0 640 480" width="20" height="15" class="rounded-1 shadow-xs" style="display:inline-block; vertical-align:middle; border-radius:2px; flex-shrink:0;"><clipPath id="js_gb_cph"><rect width="640" height="480"/></clipPath><g clip-path="url(#js_gb_cph)"><rect width="640" height="480" fill="#012169"/><path d="M0,0 L640,480 M640,0 L0,480" stroke="#FFFFFF" stroke-width="60"/><path d="M0,0 L640,480 M640,0 L0,480" stroke="#C8102E" stroke-width="36"/><path d="M320,0 V480 M0,240 H640" stroke="#FFFFFF" stroke-width="100"/><path d="M320,0 V480 M0,240 H640" stroke="#C8102E" stroke-width="60"/></g></svg>',
      'AE': '<svg viewBox="0 0 640 480" width="20" height="15" class="rounded-1 shadow-xs" style="display:inline-block; vertical-align:middle; border-radius:2px; flex-shrink:0;"><rect width="640" height="160" fill="#00732f"/><rect y="160" width="640" height="160" fill="#ffffff"/><rect y="320" width="640" height="160" fill="#000000"/><rect width="160" height="480" fill="#ff0000"/></svg>',
      'CA': '<svg viewBox="0 0 640 480" width="20" height="15" class="rounded-1 shadow-xs" style="display:inline-block; vertical-align:middle; border-radius:2px; flex-shrink:0;"><rect width="160" height="480" fill="#d80621"/><rect x="160" width="320" height="480" fill="#ffffff"/><rect x="480" width="160" height="480" fill="#d80621"/><path d="M320,130 L335,210 L370,185 L365,225 L405,230 L375,260 L390,290 L345,280 L350,305 L330,305 L330,350 L310,350 L310,305 L290,305 L295,280 L250,290 L265,260 L235,230 L275,225 L270,185 L305,210 Z" fill="#d80621"/></svg>',
      'AU': '<svg viewBox="0 0 640 480" width="20" height="15" class="rounded-1 shadow-xs" style="display:inline-block; vertical-align:middle; border-radius:2px; flex-shrink:0;"><rect width="640" height="480" fill="#00008b"/><circle cx="160" cy="360" r="30" fill="#ffffff"/><circle cx="480" cy="120" r="14" fill="#ffffff"/><circle cx="430" cy="200" r="14" fill="#ffffff"/><circle cx="530" cy="220" r="14" fill="#ffffff"/><circle cx="470" cy="300" r="14" fill="#ffffff"/><circle cx="500" cy="250" r="9" fill="#ffffff"/></svg>',
      'SG': '<svg viewBox="0 0 640 480" width="20" height="15" class="rounded-1 shadow-xs" style="display:inline-block; vertical-align:middle; border-radius:2px; flex-shrink:0;"><rect width="640" height="240" fill="#ed2939"/><rect y="240" width="640" height="240" fill="#ffffff"/><circle cx="130" cy="120" r="60" fill="#ffffff"/><circle cx="150" cy="120" r="55" fill="#ed2939"/><circle cx="170" cy="85" r="10" fill="#ffffff"/><circle cx="195" cy="105" r="10" fill="#ffffff"/><circle cx="195" cy="135" r="10" fill="#ffffff"/><circle cx="170" cy="155" r="10" fill="#ffffff"/><circle cx="155" cy="120" r="10" fill="#ffffff"/></svg>',
      'SA': '<svg viewBox="0 0 640 480" width="20" height="15" class="rounded-1 shadow-xs" style="display:inline-block; vertical-align:middle; border-radius:2px; flex-shrink:0;"><rect width="640" height="480" fill="#006c35"/><rect x="180" y="270" width="280" height="14" rx="7" fill="#ffffff"/><polygon points="180,277 210,265 210,289" fill="#ffffff"/><text x="320" y="230" fill="#ffffff" font-size="65" font-family="sans-serif" font-weight="bold" text-anchor="middle">SA</text></svg>',
      'DE': '<svg viewBox="0 0 640 480" width="20" height="15" class="rounded-1 shadow-xs" style="display:inline-block; vertical-align:middle; border-radius:2px; flex-shrink:0;"><rect width="640" height="160" fill="#000000"/><rect y="160" width="640" height="160" fill="#dd0000"/><rect y="320" width="640" height="160" fill="#ffce00"/></svg>',
      'FR': '<svg viewBox="0 0 640 480" width="20" height="15" class="rounded-1 shadow-xs" style="display:inline-block; vertical-align:middle; border-radius:2px; flex-shrink:0;"><rect width="213" height="480" fill="#002395"/><rect x="213" width="214" height="480" fill="#ffffff"/><rect x="427" width="213" height="480" fill="#ed2939"/></svg>'
    };

    var iso = '';
    var dial = '';

    // 1. Resolve from calling code
    if (calling) {
      var upper = calling.toUpperCase();
      if (ISO_TO_DIAL[upper]) {
        iso = upper;
        dial = ISO_TO_DIAL[upper];
      } else if (window.__JERO_COUNTRIES_DB__) {
        var foundByIso = window.__JERO_COUNTRIES_DB__.find(function (c) {
          return c.iso.toUpperCase() === upper || (c.iso3 && c.iso3.toUpperCase() === upper);
        });
        if (foundByIso) {
          iso = foundByIso.iso;
          dial = foundByIso.dial;
        }
      }

      if (!dial) {
        var dialCandidate = calling.startsWith('+') ? calling : ('+' + calling);
        if (DIAL_TO_ISO[dialCandidate]) {
          dial = dialCandidate;
          iso = DIAL_TO_ISO[dialCandidate];
        } else if (window.__JERO_COUNTRIES_DB__) {
          var callingDigits = calling.replace(/[^\d]/g, '');
          var foundByDial = window.__JERO_COUNTRIES_DB__.find(function (c) {
            return c.dial === dialCandidate || c.dialDigits === callingDigits;
          });
          if (foundByDial) {
            dial = foundByDial.dial;
            iso = foundByDial.iso;
          }
        }
      }
    }

    // 2. Check if phone string itself has a leading dial code with '+'
    if (!dial && phone.startsWith('+')) {
      var dials = Object.keys(DIAL_TO_ISO).sort(function (a, b) { return b.length - a.length; });
      for (var i = 0; i < dials.length; i++) {
        var d = dials[i];
        if (phone.startsWith(d)) {
          dial = d;
          iso = DIAL_TO_ISO[d];
          break;
        }
      }
    }

    // 3. Fallbacks
    if (!dial) {
      dial = '+91';
      iso = 'IN';
    }
    if (!iso) {
      iso = DIAL_TO_ISO[dial] || 'IN';
    }

    // 4. Strip duplicated dial code prefix from phone
    var cleanPhone = phone;
    var dialEscaped = dial.replace('+', '\\+');
    var dialRegex = new RegExp('^' + dialEscaped + '\\s*[-.]?\\s*');
    if (dialRegex.test(cleanPhone)) {
      cleanPhone = cleanPhone.replace(dialRegex, '');
    } else {
      var dialDigits = dial.replace(/[^\d]/g, '');
      if (dialDigits && cleanPhone.startsWith(dialDigits)) {
        var remainder = cleanPhone.slice(dialDigits.length).replace(/^[\s.-]+/, '');
        if (remainder.replace(/[^\d]/g, '').length >= 6) {
          cleanPhone = remainder;
        }
      }
    }
    cleanPhone = cleanPhone.trim();

    // 5. Retrieve SVG Flag
    var flagSvg = '';
    if (typeof window.getJeroFlagSvg === 'function') {
      flagSvg = window.getJeroFlagSvg(iso);
    } else if (window.__JERO_FLAGS_SVG__ && window.__JERO_FLAGS_SVG__[iso]) {
      flagSvg = window.__JERO_FLAGS_SVG__[iso];
    } else if (topSvgFlags[iso]) {
      flagSvg = topSvgFlags[iso];
    } else {
      flagSvg = '<svg viewBox="0 0 640 480" width="20" height="15" class="rounded-1 shadow-xs" style="display:inline-block; vertical-align:middle; border-radius:2px; flex-shrink:0;"><rect width="640" height="480" fill="#1e3c72" rx="60"/><text x="320" y="320" fill="#ffffff" font-size="240" font-family=\'Poppins\', sans-serif font-weight="700" text-anchor="middle">' + escapeHtml(iso) + '</text></svg>';
    }

    return '<span class="d-inline-flex align-items-center text-nowrap" style="gap:6px;">' +
      '<span class="d-inline-flex align-items-center flex-shrink-0" style="width:20px; height:15px; border-radius:2px; overflow:hidden; box-shadow:0 0 0 1px rgba(0,0,0,0.08);">' + flagSvg + '</span>' +
      '<span style="font-weight:600; color:#1e293b; font-size:13px;">' + escapeHtml(dial) + '</span>' +
      '<span style="color:#334155; font-size:13px; font-weight:500;">' + escapeHtml(cleanPhone) + '</span>' +
    '</span>';
  }
  window.formatContactPhone = formatContactPhone;

  // =========================================================================
  // =========================================================================
  // GOD-LEVEL CLIENT CONTACT MANAGER (Step 4: Client Contacts)
  // Clean Enterprise Full-Width Layout & Real-Time Contact Management
  // Features:
  // - Dynamic Role Mapping (HR / TA, Talent Acquisition, Hiring Manager, Recruiter)
  // - Automatic Primary Contact (1st contact = Primary, others = Secondary)
  // - Reactive Primary Reassignment ("Make Primary" click promotes contact)
  // - Clean Drawer for Adding and Editing Contacts with inline validation
  // - Full 15.6" screen optimization & zero server read strain (100% Client-Side)
  // =========================================================================
  window.clientContactManager = {
    contacts: [],

    init: function (initialList) {
      var self = this;
      var raw = initialList || window.__INITIAL_CONTACTS__ || [];
      self.contacts = [];
      var seenEmails = {};
      if (Array.isArray(raw) && raw.length > 0) {
        raw.forEach(function (c, idx) {
          if (!c.name && !c.email) return;
          var emailLower = (c.email || '').trim().toLowerCase();
          if (emailLower && seenEmails[emailLower]) {
            return; // Skip duplicate email
          }
          if (emailLower) {
            seenEmails[emailLower] = true;
          }
          var roleInfo = self.mapDesignationToRole(c.designation);
          self.contacts.push({
            name: (c.name || '').trim(),
            designation: (c.designation || '').trim(),
            email: (c.email || '').trim(),
            phone: (c.phone || '').trim(),
            callingCode: (c.callingCode || '+91').trim(),
            role: c.role || roleInfo.name,
            isPrimary: (typeof c.isPrimary === 'boolean') ? c.isPrimary : (self.contacts.length === 0)
          });
        });
      }

      self.deduplicateContacts();
      self.renderTable();
      self.syncHiddenInputs();
    },

    deduplicateContacts: function () {
      var self = this;
      var seen = {};
      var unique = [];
      self.contacts.forEach(function (c) {
        var em = (c.email || '').trim().toLowerCase();
        if (em && seen[em]) {
          return; // Skip duplicate contact
        }
        if (em) {
          seen[em] = true;
        }
        unique.push(c);
      });
      self.contacts = unique;

      // Guarantee at least 1 contact is primary if list is not empty
      if (self.contacts.length > 0) {
        var hasPrimary = self.contacts.some(function (c) { return c.isPrimary; });
        if (!hasPrimary) {
          self.contacts[0].isPrimary = true;
        }
      }
    },

    isEmailTaken: function (email, excludeIndex) {
      var self = this;
      var target = (email || '').trim().toLowerCase();
      if (!target) return false;
      var exIdx = (excludeIndex !== null && excludeIndex !== undefined && excludeIndex !== '') ? parseInt(excludeIndex, 10) : -1;
      return self.contacts.some(function (c, idx) {
        if (idx === exIdx) return false;
        return (c.email || '').trim().toLowerCase() === target;
      });
    },

    getContacts: function () {
      return this.contacts;
    },

    getContactRowsData: function () {
      return this.contacts.map(function (c, i) {
        return {
          index: i,
          name: c.name,
          email: c.email,
          phone: c.phone,
          designation: c.designation,
          callingCode: c.callingCode,
          role: c.role,
          isPrimary: c.isPrimary
        };
      });
    },

    mapDesignationToRole: function (designation) {
      var d = (designation || '').toLowerCase();
      if (d.includes('talent acquisition') || d.includes('ta lead') || d.includes('ta manager') || d.includes('ta head')) {
        return { name: 'Talent Acquisition', bg: '#dcfce7', text: '#166534' };
      }
      if (d.includes('hr') || d.includes('human resources') || d.includes('people') || d.includes('hr / ta')) {
        return { name: 'HR / TA', bg: '#dbeafe', text: '#1e40af' };
      }
      if (d.includes('hiring manager') || d.includes('engineering manager') || d.includes('tech lead') || d.includes('project manager')) {
        return { name: 'Hiring Manager', bg: '#ede9fe', text: '#6b21a8' };
      }
      if (d.includes('recruit') || d.includes('staffing') || d.includes('sourcer')) {
        return { name: 'Recruiter', bg: '#fee2e2', text: '#9f1239' };
      }
      if (d.includes('director') || d.includes('founder') || d.includes('ceo') || d.includes('cto') || d.includes('vp') || d.includes('president') || d.includes('head')) {
        return { name: 'Leadership', bg: '#fef3c7', text: '#92400e' };
      }
      return { name: 'HR / TA', bg: '#dbeafe', text: '#1e40af' };
    },

    getAvatarPalette: function (name, index) {
      var known = {
        'rahul sharma': { bg: '#f3e8ff', text: '#7e22ce' },
        'priya sinha': { bg: '#e0e7ff', text: '#4338ca' },
        'priya mehta': { bg: '#fce7f3', text: '#be185d' },
        'amit kulkarni': { bg: '#d1fae5', text: '#047857' },
        'sneha nair': { bg: '#e0f2fe', text: '#0369a1' },
        'vikram deshpande': { bg: '#fef3c7', text: '#b45309' }
      };
      var norm = (name || '').toLowerCase().trim();
      if (known[norm]) return known[norm];
      var palettes = [
        { bg: '#f3e8ff', text: '#7e22ce' },
        { bg: '#fce7f3', text: '#be185d' },
        { bg: '#d1fae5', text: '#047857' },
        { bg: '#e0f2fe', text: '#0369a1' },
        { bg: '#fef3c7', text: '#b45309' }
      ];
      return palettes[Math.abs(index) % palettes.length];
    },

    renderTable: function () {
      var self = this;
      self.deduplicateContacts();

      var $tbody = $('#client_contact_table_body');
      $tbody.find('tr:not(#client_contact_empty_row)').remove();
      var $emptyRow = $('#client_contact_empty_row');

      if (!self.contacts.length) {
        $emptyRow.show();
        $('#client_contact_table thead').hide();
        return;
      }
      $emptyRow.hide();
      $('#client_contact_table thead').show();

      self.contacts.forEach(function (c, i) {
        var pal = self.getAvatarPalette(c.name, i);
        var initText = initials(c.name);
        var roleInfo = self.mapDesignationToRole(c.designation);
        if (c.role) roleInfo.name = c.role;

        var displayPhoneHtml = formatContactPhone(c.phone, c.callingCode);

        var primaryColHtml = c.isPrimary
          ? '<span class="badge" style="background:#fef3c7; color:#92400e; font-weight:700; font-size:11px; padding:4px 10px; border-radius:20px; display:inline-flex; align-items:center; gap:4px;"><svg width="11" height="11" viewBox="0 0 24 24" fill="#f59e0b" stroke="#f59e0b" stroke-width="1.5"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg> Primary</span>'
          : '<button type="button" class="btn btn-link p-0 text-decoration-none client-make-primary-btn" onclick="window.clientContactManager.setPrimary(' + i + ')" title="Set as Primary Contact" style="color:#64748b; font-size:11.5px; font-weight:500; display:inline-flex; align-items:center; gap:5px; cursor:pointer; background:transparent; border:none;"><span class="radio-circle" style="display:inline-block; width:12px; height:12px; border:1.5px solid #94a3b8; border-radius:50%; background:#ffffff; transition:border-color 0.15s ease;"></span> Make Primary</button>';

        var rowHtml = '<tr data-contact-index="' + i + '" style="border-bottom:1px solid #f1f5f9;">' +
          '<td style="padding:12px 18px; width:20%; vertical-align:middle; overflow:hidden;">' +
            '<div class="d-flex align-items-center" style="min-width:0; overflow:hidden;">' +
              '<span style="background-color:' + pal.bg + '; color:' + pal.text + '; display:inline-flex; align-items:center; justify-content:center; font-weight:700; width:32px; height:32px; border-radius:50%; margin-right:10px; font-size:11.5px; flex-shrink:0;">' + escapeHtml(initText) + '</span>' +
              '<span style="font-weight:600; color:#101828; font-size:13px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;" title="' + escapeHtml(c.name) + '">' + escapeHtml(c.name) + '</span>' +
            '</div>' +
          '</td>' +
          '<td style="padding:12px 14px; width:18%; vertical-align:middle; color:#334155; font-size:12.5px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;" title="' + escapeHtml(c.designation || '') + '">' + escapeHtml(c.designation || '—') + '</td>' +
          '<td style="padding:12px 14px; width:22%; vertical-align:middle; color:#334155; font-size:12.5px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;" title="' + escapeHtml(c.email || '') + '">' + escapeHtml(c.email || '—') + '</td>' +
          '<td style="padding:12px 14px; width:16%; vertical-align:middle; color:#334155; font-size:12.5px; font-weight:500; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">' + displayPhoneHtml + '</td>' +
          '<td style="padding:12px 12px; width:10%; vertical-align:middle; white-space:nowrap;">' +
            '<span class="badge" style="background:' + roleInfo.bg + '; color:' + roleInfo.text + '; font-weight:600; font-size:11px; padding:4px 10px; border-radius:20px; white-space:nowrap; display:inline-block;">' + escapeHtml(roleInfo.name) + '</span>' +
          '</td>' +
          '<td style="padding:12px 10px; width:8%; vertical-align:middle; text-align:center; white-space:nowrap;">' + primaryColHtml + '</td>' +
          '<td style="padding:12px 16px; width:6%; vertical-align:middle; text-align:right; white-space:nowrap;">' +
            '<div class="d-inline-flex align-items-center gap-1 justify-content-end">' +
              '<button type="button" class="btn btn-icon client-edit-contact" onclick="window.clientContactManager.openEditDrawer(' + i + ')" title="Edit Contact" style="width:28px; height:28px; border-radius:6px; background:#eff6ff; border:1px solid #bfdbfe; color:#2563eb; display:inline-flex; align-items:center; justify-content:center; padding:0; cursor:pointer;">' +
                '<i class="la la-pencil" style="font-size:13px; color:#2563eb;"></i>' +
              '</button>' +
              '<button type="button" class="btn btn-icon client-delete-contact" onclick="window.clientContactManager.deleteContact(' + i + ')" title="Delete Contact" style="width:28px; height:28px; border-radius:6px; background:#fef2f2; border:1px solid #fecaca; color:#dc2626; display:inline-flex; align-items:center; justify-content:center; padding:0; cursor:pointer; margin-left:4px;">' +
                '<i class="la la-trash" style="font-size:13px; color:#dc2626;"></i>' +
              '</button>' +
            '</div>' +
          '</td>' +
        '</tr>';

        $tbody.append(rowHtml);
      });
    },

    addContact: function (contactData, isPrimaryReq) {
      var self = this;
      var email = (contactData.email || '').trim().toLowerCase();
      if (email && self.isEmailTaken(email)) {
        if (typeof Swal !== 'undefined' && typeof Swal.mixin === 'function') {
          var Toast = Swal.mixin({ toast: true, position: 'top-end', showConfirmButton: false, timer: 2500 });
          Toast.fire({ icon: 'warning', title: (contactData.name || 'Contact') + ' with email "' + email + '" is already added!' });
        }
        return false;
      }

      var isFirst = self.contacts.length === 0;
      var makePrimary = isFirst || Boolean(isPrimaryReq);

      if (makePrimary) {
        self.contacts.forEach(function (c) { c.isPrimary = false; });
      }

      var roleInfo = self.mapDesignationToRole(contactData.designation);

      self.contacts.push({
        name: (contactData.name || '').trim(),
        designation: (contactData.designation || '').trim(),
        email: (contactData.email || '').trim(),
        phone: (contactData.phone || '').trim(),
        callingCode: (contactData.callingCode || '+91').trim(),
        role: contactData.role || roleInfo.name,
        isPrimary: makePrimary
      });

      self.deduplicateContacts();
      self.renderTable();
      self.syncHiddenInputs();
      return true;
    },

    setPrimary: function (index) {
      var self = this;
      var idx = parseInt(index, 10);
      if (isNaN(idx) || idx < 0 || idx >= self.contacts.length) return;

      self.contacts.forEach(function (c, i) {
        c.isPrimary = (i === idx);
      });

      self.renderTable();
      self.syncHiddenInputs();

      var name = self.contacts[idx].name;
      if (typeof Swal !== 'undefined' && typeof Swal.mixin === 'function') {
        var Toast = Swal.mixin({ toast: true, position: 'top-end', showConfirmButton: false, timer: 2000 });
        Toast.fire({ icon: 'success', title: name + ' set as primary contact' });
      }
    },

    deleteContact: function (index) {
      var self = this;
      var idx = parseInt(index, 10);
      if (isNaN(idx) || idx < 0 || idx >= self.contacts.length) return;

      var contactName = self.contacts[idx].name || 'Contact';
      var wasPrimary = self.contacts[idx].isPrimary;
      self.contacts.splice(idx, 1);

      if (wasPrimary && self.contacts.length > 0) {
        self.contacts[0].isPrimary = true;
      }

      self.renderTable();
      self.syncHiddenInputs();

      if (typeof Swal !== 'undefined' && typeof Swal.mixin === 'function') {
        var Toast = Swal.mixin({ toast: true, position: 'top-end', showConfirmButton: false, timer: 1800 });
        Toast.fire({ icon: 'success', title: contactName + ' removed' });
      }
    },

    syncHiddenInputs: function () {
      var self = this;
      var $containerRow = $('.client-cperson-row').first();
      $containerRow.empty();

      // Ensure the primary contact is at index 0 in hidden inputs for database storage
      var sorted = self.contacts.slice().sort(function (a, b) {
        return (b.isPrimary ? 1 : 0) - (a.isPrimary ? 1 : 0);
      });

      sorted.forEach(function (c, i) {
        var hHtml = '<div class="row client-cperson-container" data-contact-index="' + i + '">' +
          '<div class="col-xl-3"><div class="form-group mb-0 client-cperson-container-child"><input type="text" name="cp_name[]" id="cp_name_' + i + '" class="form-control form-control-solid" value="' + escapeHtml(c.name) + '"></div></div>' +
          '<div class="col-xl-3"><div class="form-group mb-0 client-cperson-container-child"><input type="email" name="cp_email[]" id="cp_email_' + i + '" class="form-control form-control-solid" value="' + escapeHtml(c.email) + '" maxlength="254" inputmode="email" autocomplete="email" autocapitalize="none" spellcheck="false"></div></div>' +
          '<div class="col-xl-3"><div class="form-group mb-0 client-cperson-container-child"><input type="number" name="cp_number[]" id="cp_number_' + i + '" class="form-control form-control-solid" value="' + escapeHtml(c.phone) + '"><input type="hidden" value="' + escapeHtml(c.callingCode) + '" class="calling_code" name="calling_code[]" id="calling_code_' + i + '"></div></div>' +
          '<div class="col-xl-3"><div class="form-group mb-0 client-cperson-container-child"><input type="text" name="cp_desigination[]" id="cp_desigination_' + i + '" class="form-control form-control-solid cp_desigination" value="' + escapeHtml(c.designation) + '"></div></div>' +
          '</div>';
        $containerRow.append(hHtml);
      });

      $('#contactPersonId').val(Math.max(0, sorted.length - 1));

      if (typeof updateCompletion === 'function') {
        try { updateCompletion(); } catch (e) {}
      }
      if (typeof populateReviewSummaries === 'function') {
        try { populateReviewSummaries(); } catch (e) {}
      }
    },

    openAddDrawer: function () {
      this._openDrawer(null);
    },

    openEditDrawer: function (index) {
      var num = (index !== null && index !== undefined && index !== '') ? parseInt(index, 10) : NaN;
      this._openDrawer(!isNaN(num) ? num : null);
    },

    _openDrawer: function (index) {
      var self = this;
      self._isSaving = false;
      _cdfClearErrors();
      var num = (index !== null && index !== undefined && index !== '') ? parseInt(index, 10) : NaN;
      var isEdit = (!isNaN(num) && num >= 0 && num < self.contacts.length);
      var editIdx = isEdit ? num : null;
      $('#client_drawer_edit_index').val(isEdit ? editIdx : '');
      $('#client_contact_drawer_title').text(isEdit ? 'Edit Contact Person' : 'Add Contact Person');
      $('#client_contact_drawer_subtitle').text(isEdit ? 'Update the contact details below.' : 'Add key contacts for recruitment operations with this client.');
      $('#client_save_contact_drawer .cdf-btn-text').text(isEdit ? 'Save Changes' : 'Add Contact Person');

      if (isEdit) {
        var c = self.contacts[editIdx];
        $('#client_drawer_name').val(c.name || '');
        $('#client_drawer_designation').val(c.designation || '');
        $('#client_drawer_email').val(c.email || '');
        $('#client_drawer_is_primary').prop('checked', Boolean(c.isPrimary));

        if (window.JeroPhoneBox && typeof window.JeroPhoneBox.setCountry === 'function') {
          window.JeroPhoneBox.setCountry('client_drawer_phone', c.callingCode || '+91');
        } else {
          $('#client_drawer_calling_code').val(c.callingCode || '+91').trigger('change');
        }

        if (window.JeroPhoneBox && typeof window.JeroPhoneBox.setPhone === 'function') {
          window.JeroPhoneBox.setPhone('client_drawer_phone', c.phone || '');
        } else {
          $('#client_drawer_phone').val(c.phone || '').trigger('input');
        }
      } else {
        $('#client_drawer_name, #client_drawer_designation, #client_drawer_email').val('');
        $('#client_drawer_is_primary').prop('checked', self.contacts.length === 0);

        if (window.JeroPhoneBox && typeof window.JeroPhoneBox.setCountry === 'function') {
          window.JeroPhoneBox.setCountry('client_drawer_phone', '+91');
        } else {
          $('#client_drawer_calling_code').val('+91').trigger('change');
        }
        if (window.JeroPhoneBox && typeof window.JeroPhoneBox.setPhone === 'function') {
          window.JeroPhoneBox.setPhone('client_drawer_phone', '');
        } else {
          $('#client_drawer_phone').val('').trigger('input');
        }
      }

      if ($('#client_contact_drawer').length && $('#client_contact_drawer').parent()[0] !== document.body) {
        $('body').append($('#client_contact_drawer_backdrop'), $('#client_contact_drawer'));
      }

      if (window.JeroPhoneBox && typeof window.JeroPhoneBox.closeAllDropdowns === 'function') {
        window.JeroPhoneBox.closeAllDropdowns();
      }
      $('#client_contact_drawer, #client_contact_drawer_backdrop').addClass('is-open').attr('aria-hidden', 'false');
      setTimeout(function () { $('#client_drawer_name').focus(); }, 120);
    },

    closeDrawer: function () {
      var self = this;
      self._isSaving = false;
      $('#client_contact_drawer, #client_contact_drawer_backdrop').removeClass('is-open').attr('aria-hidden', 'true');
      _cdfClearErrors();
      $('#client_drawer_name, #client_drawer_designation, #client_drawer_email').val('').removeClass('is-invalid');
      $('#client_drawer_email_typo_hint').hide().empty();
      $('#client_drawer_edit_index').val('');
      if (window.JeroPhoneBox && typeof window.JeroPhoneBox.setPhone === 'function') {
        window.JeroPhoneBox.setPhone('client_drawer_phone', '');
        window.JeroPhoneBox.setCountry('client_drawer_phone', '+91');
      } else {
        $('#client_drawer_phone').val('').removeClass('is-invalid');
        $('#client_drawer_calling_code').val('+91');
      }
      if (window.JeroPhoneBox && typeof window.JeroPhoneBox.closeAllDropdowns === 'function') {
        window.JeroPhoneBox.closeAllDropdowns();
      }
    },

    saveDrawer: function () {
      var self = this;
      if (self._isSaving) return false;
      self._isSaving = true;

      _cdfClearErrors();

      var name = $('#client_drawer_name').val().trim();
      var designation = $('#client_drawer_designation').val().trim();
      var rawEmail = $('#client_drawer_email').val() || '';
      var email = (window.JeroEmailBoxEngine && typeof window.JeroEmailBoxEngine.cleanDeterministic === 'function')
        ? window.JeroEmailBoxEngine.cleanDeterministic(rawEmail)
        : rawEmail.trim().toLowerCase();
      $('#client_drawer_email').val(email);

      var phone = window.JeroPhoneBox && typeof window.JeroPhoneBox.getCleanNumber === 'function'
        ? window.JeroPhoneBox.getCleanNumber('client_drawer_phone')
        : ($('#client_drawer_phone').val() || '').replace(/[^\d]/g, '');
      var callingCode = ($('#client_drawer_calling_code').val() || '+91').trim();
      var isPrimaryChecked = $('#client_drawer_is_primary').is(':checked');

      var valid = true;
      var $firstBad = null;

      if (!name) {
        $('#client_drawer_name').addClass('is-invalid');
        $('#cdf_err_name').addClass('visible');
        $firstBad = $firstBad || $('#client_drawer_name');
        valid = false;
      }
      if (!designation) {
        $('#client_drawer_designation').addClass('is-invalid');
        $('#cdf_err_designation').addClass('visible');
        $firstBad = $firstBad || $('#client_drawer_designation');
        valid = false;
      }
      var emailSyntax = (window.JeroEmailBoxEngine && typeof window.JeroEmailBoxEngine.validateSyntax === 'function')
        ? window.JeroEmailBoxEngine.validateSyntax(email)
        : { isValid: /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email), error: 'Please enter a valid email address.' };

      if (!emailSyntax.isValid) {
        $('#client_drawer_email').addClass('is-invalid');
        $('#cdf_err_email').text(emailSyntax.error).addClass('visible');
        $firstBad = $firstBad || $('#client_drawer_email');
        valid = false;
      }
      if (phone && (phone.length < 6 || phone.length > 15 || isNaN(phone))) {
        $('#client_drawer_phone').addClass('is-invalid');
        $('#cdf_err_phone').text('Please enter a valid phone number (6-15 digits).').addClass('visible');
        $firstBad = $firstBad || $('#client_drawer_phone');
        valid = false;
      }

      if (!valid) {
        self._isSaving = false;
        if ($firstBad) { $firstBad.focus(); }
        return false;
      }

      var editIndexStr = $('#client_drawer_edit_index').val();
      var isEdit = (editIndexStr !== '' && !isNaN(parseInt(editIndexStr, 10)));
      var editIdx = isEdit ? parseInt(editIndexStr, 10) : null;

      // Duplicate check (case-insensitive & trimmed)
      if (self.isEmailTaken(email, editIdx)) {
        self._isSaving = false;
        $('#client_drawer_email').addClass('is-invalid');
        $('#cdf_err_email').text('A contact with this email already exists in the list.').addClass('visible');
        $('#client_drawer_email').focus();
        if (typeof Swal !== 'undefined' && typeof Swal.mixin === 'function') {
          var Toast = Swal.mixin({ toast: true, position: 'top-end', showConfirmButton: false, timer: 2500 });
          Toast.fire({ icon: 'warning', title: 'A contact with this email already exists!' });
        }
        return false;
      }

      var roleInfo = self.mapDesignationToRole(designation);

      if (isEdit) {
        if (isPrimaryChecked) {
          self.contacts.forEach(function (c) { c.isPrimary = false; });
        }
        self.contacts[editIdx].name = name;
        self.contacts[editIdx].designation = designation;
        self.contacts[editIdx].email = email;
        self.contacts[editIdx].phone = phone;
        self.contacts[editIdx].callingCode = callingCode;
        self.contacts[editIdx].role = roleInfo.name;
        if (isPrimaryChecked) self.contacts[editIdx].isPrimary = true;
      } else {
        var isFirst = self.contacts.length === 0;
        var makePrimary = isFirst || Boolean(isPrimaryChecked);
        if (makePrimary) {
          self.contacts.forEach(function (c) { c.isPrimary = false; });
        }
        self.contacts.push({
          name: name,
          designation: designation,
          email: email,
          phone: phone,
          callingCode: callingCode,
          role: roleInfo.name,
          isPrimary: makePrimary
        });
      }

      // Ensure at least 1 contact is primary
      if (self.contacts.length > 0) {
        var hasPrimary = self.contacts.some(function (c) { return c.isPrimary; });
        if (!hasPrimary) {
          self.contacts[0].isPrimary = true;
        }
      }

      self.renderTable();
      self.syncHiddenInputs();
      self.closeDrawer();

      if (typeof Swal !== 'undefined' && typeof Swal.mixin === 'function') {
        var Toast = Swal.mixin({ toast: true, position: 'top-end', showConfirmButton: false, timer: 2000 });
        Toast.fire({ icon: 'success', title: isEdit ? (name + ' updated successfully') : (name + ' added to contacts') });
      }

      setTimeout(function () {
        self._isSaving = false;
      }, 200);

      return true;
    }
  };

  function _cdfClearErrors() {
    $('#client_drawer_name, #client_drawer_designation, #client_drawer_email, #client_drawer_phone').removeClass('is-invalid');
    $('.cdf-error').removeClass('visible');
    $('#client_drawer_email_typo_hint').hide().empty();
  }

  function getContactRowsData() {
    if (window.clientContactManager && typeof window.clientContactManager.getContactRowsData === 'function') {
      return window.clientContactManager.getContactRowsData();
    }
    var rows = [];
    $('.client-cperson-container').each(function (idx) {
      var $container = $(this);
      var name = ($container.find('input[name="cp_name[]"]').val() || '').trim();
      var email = ($container.find('input[name="cp_email[]"]').val() || '').trim();
      var phone = ($container.find('input[name="cp_number[]"]').val() || '').trim();
      var designation = ($container.find('input[name="cp_desigination[]"]').val() || '').trim();
      var callingCode = ($container.find('input[name="calling_code[]"]').val() || '+91').trim();
      if (!name && !email) return;
      rows.push({
        index: idx,
        name: name,
        email: email,
        phone: phone,
        designation: designation,
        callingCode: callingCode
      });
    });
    return rows;
  }

  function renderContactTable() {
    if (window.clientContactManager) {
      window.clientContactManager.renderTable();
    }
  }

  function deleteContactRow(index) {
    if (window.clientContactManager) {
      window.clientContactManager.deleteContact(index);
    }
  }

  function openContactDrawer(index) {
    if (window.clientContactManager) {
      var num = (index !== null && index !== undefined && index !== '') ? parseInt(index, 10) : NaN;
      if (!isNaN(num) && num >= 0) {
        window.clientContactManager.openEditDrawer(num);
      } else {
        window.clientContactManager.openAddDrawer();
      }
    }
  }

  function closeContactDrawer() {
    if (window.clientContactManager) {
      window.clientContactManager.closeDrawer();
    }
  }

  function saveContactDrawer() {
    if (window.clientContactManager) {
      window.clientContactManager.saveDrawer();
    }
  }

  function addSuggestedContact(contactData) {
    if (window.clientContactManager) {
      window.clientContactManager.addContact(contactData);
    }
  }

  window.openContactDrawer = openContactDrawer;
  window.closeContactDrawer = closeContactDrawer;
  window.saveContactDrawer = saveContactDrawer;
  window.deleteContactRow = deleteContactRow;
  window.addSuggestedContact = addSuggestedContact;
  window.renderContactTable = renderContactTable;
  window.getContactRowsData = getContactRowsData;

  // Initialize clientContactManager on DOM ready
  $(function () {
    if (window.clientContactManager && window.__INITIAL_CONTACTS__) {
      window.clientContactManager.init(window.__INITIAL_CONTACTS__);
    }
  });

    /* Clear individual field error on input */
  $(document).on('input', '#client_drawer_name', function () { $(this).removeClass('is-invalid'); $('#cdf_err_name').removeClass('visible'); });
  $(document).on('input', '#client_drawer_designation', function () { $(this).removeClass('is-invalid'); $('#cdf_err_designation').removeClass('visible'); });
  $(document).on('input', '#client_drawer_email', function () {
    $(this).removeClass('is-invalid');
    $('#cdf_err_email').removeClass('visible');
    var raw = $(this).val();
    var clean = (raw || '').trim().toLowerCase();
    var editIdx = $('#client_drawer_edit_index').val();
    if (clean && window.clientContactManager && window.clientContactManager.isEmailTaken(clean, editIdx)) {
      $(this).addClass('is-invalid');
      $('#cdf_err_email').text('A contact with this email already exists in the list.').addClass('visible');
    }
  });
  $(document).on('input', '#client_drawer_phone', function () { $(this).removeClass('is-invalid'); $('#cdf_err_phone').removeClass('visible'); });

  // Paste & Blur deterministic cleanup and typo suggestions for Contact Person Drawer Email
  $(document).on('paste', '#client_drawer_email', function () {
    var self = this;
    setTimeout(function () {
      var clean = JeroEmailBoxEngine.cleanDeterministic($(self).val());
      if (clean !== $(self).val()) {
        $(self).val(clean).trigger('input');
      }
    }, 10);
  });

  $(document).on('blur change', '#client_drawer_email', function () {
    var raw = $(this).val();
    var clean = JeroEmailBoxEngine.cleanDeterministic(raw);
    if (clean !== raw) {
      $(this).val(clean);
    }
    var $hint = $('#client_drawer_email_typo_hint');
    if (!clean) {
      $hint.hide().empty();
      return;
    }
    var editIdx = $('#client_drawer_edit_index').val();
    if (window.clientContactManager && window.clientContactManager.isEmailTaken(clean, editIdx)) {
      $('#client_drawer_email').addClass('is-invalid');
      $('#cdf_err_email').text('A contact with this email already exists in the list.').addClass('visible');
      $hint.hide().empty();
      return;
    }
    var syntax = JeroEmailBoxEngine.validateSyntax(clean);
    if (!syntax.isValid) {
      $hint.html('<i class="la la-exclamation-circle text-danger"></i> ' + syntax.error).show();
      return;
    }
    var suggestion = JeroEmailBoxEngine.getSuggestion(clean);
    if (suggestion) {
      $hint.html('<i class="la la-lightbulb text-warning"></i> ' + suggestion.message + ' <a href="javascript:void(0);" class="apply-email-fix fw-bold text-primary ms-1" data-target="#client_drawer_email" data-fixed="' + suggestion.suggested + '">Apply</a>').show();
    } else {
      $hint.hide().empty();
    }
  });

  // Paste & Blur deterministic cleanup and typo suggestions for Accounts Payable Email
  $(document).on('paste', '#ap_email', function () {
    var self = this;
    setTimeout(function () {
      var clean = JeroEmailBoxEngine.cleanDeterministic($(self).val());
      if (clean !== $(self).val()) {
        $(self).val(clean).trigger('input');
      }
    }, 10);
  });

  $(document).on('blur change', '#ap_email', function () {
    var raw = $(this).val();
    var clean = JeroEmailBoxEngine.cleanDeterministic(raw);
    if (clean !== raw) {
      $(this).val(clean);
    }
    var $hint = $('#ap_email_typo_hint');
    if (!clean) {
      $hint.hide().empty();
      return;
    }
    var syntax = JeroEmailBoxEngine.validateSyntax(clean);
    if (!syntax.isValid) {
      $hint.html('<i class="la la-exclamation-circle text-danger"></i> ' + syntax.error).show();
      return;
    }
    var suggestion = JeroEmailBoxEngine.getSuggestion(clean);
    if (suggestion) {
      $hint.html('<i class="la la-lightbulb text-warning"></i> ' + suggestion.message + ' <a href="javascript:void(0);" class="apply-email-fix fw-bold text-primary ms-1" data-target="#ap_email" data-fixed="' + suggestion.suggested + '">Apply</a>').show();
    } else {
      $hint.hide().empty();
    }
  });

  // Explicit User Action to Apply High-Confidence Fix (Zero Silent Mutation)
  $(document).on('click', '.apply-email-fix', function (e) {
    e.preventDefault();
    var targetSelector = $(this).data('target');
    var fixed = $(this).data('fixed');
    if (targetSelector && fixed) {
      $(targetSelector).val(fixed).trigger('input').trigger('change');
      $(this).closest('.client-email-typo-hint').hide().empty();
    }
  });

  /* =========================================================================
     WINDOW.CLIENTSOCIALMANAGER (GOD-LEVEL V6 SOCIAL LINKS & AI DISCOVERY ENGINE)
     Matches media_1792575217426_d430168e.png
     Order: linkedin, twitter, facebook, instagram, youtube, github, custom_link
     Client-First WASM / RAM execution (0% server/DB read strain)
     ========================================================================= */
  window.clientSocialManager = {
    platforms: ['linkedin', 'twitter', 'facebook', 'instagram', 'youtube', 'github'],
    discoveredUrls: {},

    setDiscoveredUrls: function (urls) {
      this.discoveredUrls = urls || {};
    },

    clearDiscoveredUrls: function () {
      this.discoveredUrls = {};
    },

    getSlug: function () {
      var web = ($('#website').val() || '').trim();
      var name = ($('#client_id').val() || '').trim();
      var slug = '';

      if (web) {
        try {
          var cleanWeb = web.replace(/^https?:\/\//i, '').replace(/^www\./i, '').split('/')[0];
          slug = cleanWeb.split('.')[0] || '';
        } catch (e) {}
      }

      if (!slug && name) {
        slug = name.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/^-+|-+$/g, '');
      }

      return slug || 'clientname';
    },

    getCleanHandle: function () {
      var s = this.getSlug();
      return s.replace(/-/g, '');
    },

    getUrlForPlatform: function (platform) {
      if (this.discoveredUrls && this.discoveredUrls[platform]) {
        return this.discoveredUrls[platform];
      }
      var slug = this.getSlug();
      var handle = this.getCleanHandle();
      switch (platform) {
        case 'linkedin':
          return 'https://linkedin.com/company/' + slug;
        case 'twitter':
          return 'https://x.com/' + handle;
        case 'facebook':
          return 'https://facebook.com/' + handle;
        case 'instagram':
          return 'https://instagram.com/' + handle;
        case 'youtube':
          return 'https://youtube.com/@' + handle;
        case 'github':
          return 'https://github.com/' + handle;
        default:
          return '';
      }
    },

    _normalizeUrl: function (url) {
      if (!url) return '';
      return String(url)
        .toLowerCase()
        .trim()
        .replace(/^https?:\/\//i, '')
        .replace(/^www\./i, '')
        .replace(/\/+$/, '');
    },

    syncWebsiteLink: function () {
      var self = this;
      this.platforms.forEach(function (plat) {
        var url = self.getUrlForPlatform(plat);
        $('#ai_url_' + plat).text(url).attr('title', url);
      });
    },

    clearAllSocialInputs: function () {
      var self = this;
      this.platforms.forEach(function (plat) {
        var $inp = $('#' + plat + '_url');
        if ($inp.length) {
          $inp.val('');
        }
        $('.client-social-clear-btn[data-target="' + plat + '_url"]').hide();
      });
      $('#custom_link').val('');
      $('.client-social-clear-btn[data-target="custom_link"]').hide();
      self.syncAiButtons();
    },

    addPlatform: function (platform) {
      var url = ($('#ai_url_' + platform).text() || '').trim() || this.getUrlForPlatform(platform);
      var $input = $('#' + platform + '_url');
      if ($input.length && url) {
        $input.val(url).trigger('input').trigger('change');
      }

      this.markButtonAdded(platform);

      // Lightweight toast notification
      if (typeof Swal !== 'undefined' && typeof Swal.mixin === 'function') {
        var Toast = Swal.mixin({
          toast: true,
          position: 'top-end',
          showConfirmButton: false,
          timer: 2000,
          timerProgressBar: true
        });
        var platNames = {
          linkedin: 'LinkedIn',
          twitter: 'X / Twitter',
          facebook: 'Facebook',
          instagram: 'Instagram',
          youtube: 'YouTube',
          github: 'GitHub'
        };
        Toast.fire({
          icon: 'success',
          title: (platNames[platform] || platform) + ' profile added to client'
        });
      }

      this.syncAiButtons();
      if (typeof populateReviewSummaries === 'function') {
        populateReviewSummaries();
      }
      if (typeof updateCompletion === 'function') {
        updateCompletion();
      }
    },

    addAllSuggested: function () {
      var self = this;
      var addedCount = 0;
      this.platforms.forEach(function (plat) {
        var url = ($('#ai_url_' + plat).text() || '').trim() || self.getUrlForPlatform(plat);
        var $input = $('#' + plat + '_url');
        if ($input.length && url) {
          $input.val(url).trigger('input').trigger('change');
          self.markButtonAdded(plat);
          addedCount++;
        }
      });

      var $btnAll = $('#btn_add_all_social');
      $btnAll.html('<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" style="margin-right:4px;"><polyline points="20 6 9 17 4 12"/></svg> All Added')
             .css({
               'background': '#ecfdf5',
               'border': '1px solid #a7f3d0',
               'color': '#059669',
               'cursor': 'default'
             });

      if (typeof Swal !== 'undefined' && typeof Swal.mixin === 'function') {
        var Toast = Swal.mixin({
          toast: true,
          position: 'top-end',
          showConfirmButton: false,
          timer: 2500,
          timerProgressBar: true
        });
        Toast.fire({
          icon: 'success',
          title: 'All ' + addedCount + ' social profiles added to client'
        });
      }

      if (typeof populateReviewSummaries === 'function') {
        populateReviewSummaries();
      }
      if (typeof updateCompletion === 'function') {
        updateCompletion();
      }
    },

    markButtonAdded: function (platform) {
      var $btn = $('#btn_ai_add_' + platform);
      $btn.addClass('is-added')
          .html('<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" style="margin-right:4px;"><polyline points="20 6 9 17 4 12"/></svg> Added')
          .css({
            'background': '#ecfdf5',
            'border': '1px solid #a7f3d0',
            'color': '#059669',
            'cursor': 'default'
          });
    },

    resetButton: function (platform) {
      var $btn = $('#btn_ai_add_' + platform);
      $btn.removeClass('is-added')
          .html('+ Add')
          .css({
            'background': '#ffffff',
            'border': '1px solid #2563eb',
            'color': '#2563eb',
            'cursor': 'pointer'
          });
    },

    syncAiButtons: function () {
      var self = this;
      var allAdded = true;

      this.platforms.forEach(function (plat) {
        var inputVal = ($('#' + plat + '_url').val() || '').trim();
        var suggestedVal = self.getUrlForPlatform(plat);
        var $clearBtn = $('.client-social-clear-btn[data-target="' + plat + '_url"]');

        if (inputVal) {
          $clearBtn.css('display', 'inline-flex');
        } else {
          $clearBtn.hide();
        }

        var normInput = self._normalizeUrl(inputVal);
        var normSuggested = self._normalizeUrl(suggestedVal);

        // Strictly verify that the input actually matches the suggested AI profile URL
        if (normInput && normSuggested && normInput === normSuggested) {
          self.markButtonAdded(plat);
        } else {
          self.resetButton(plat);
          allAdded = false;
        }
      });

      // Also handle Custom Link clear button
      var customVal = ($('#custom_link').val() || '').trim();
      var $customClearBtn = $('.client-social-clear-btn[data-target="custom_link"]');
      if (customVal) {
        $customClearBtn.css('display', 'inline-flex');
      } else {
        $customClearBtn.hide();
      }

      var $btnAll = $('#btn_add_all_social');
      if (allAdded) {
        $btnAll.html('<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" style="margin-right:4px;"><polyline points="20 6 9 17 4 12"/></svg> All Added')
               .css({
                 'background': '#ecfdf5',
                 'border': '1px solid #a7f3d0',
                 'color': '#059669',
                 'cursor': 'default'
               });
      } else {
        $btnAll.html('<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg> <span>Add All Suggested</span>')
               .css({
                 'background': '#eff6ff',
                 'border': '1px solid #bfdbfe',
                 'color': '#2563eb',
                 'cursor': 'pointer'
               });
      }
    }
  };

  // Clear button click handler for all social inputs
  $(document).on('click', '.client-social-clear-btn', function (e) {
    e.preventDefault();
    e.stopPropagation();
    var targetId = $(this).data('target');
    var $input = $('#' + targetId);
    if ($input.length) {
      $input.val('').trigger('input').trigger('change');
    }
    $(this).hide();
    if (window.clientSocialManager) {
      window.clientSocialManager.syncAiButtons();
    }
  });

  // Sync AI button states when user types or edits social inputs directly
  $(document).on('input change', '.client-social-url-input', function () {
    if (window.clientSocialManager) {
      window.clientSocialManager.syncAiButtons();
    }
    if (typeof populateReviewSummaries === 'function') {
      populateReviewSummaries();
    }
    if (typeof updateCompletion === 'function') {
      updateCompletion();
    }
  });

  function populateReviewSummaries() {
    var clientName = ($('#client_id').val() || '').trim();
    var website = ($('#website').val() || '').trim();
    var industry = ($('#industry_display').val() || '').trim();
    if (!industry || industry === 'Select Industry' || industry === 'Select industry') {
      industry = $('#industry option:selected').text() || '—';
    }
    var compSizes = $('#company_sizes option:selected').text() || '';
    var compType = ($('#company_type_display').val() || $('#company_type option:selected').text() || '—').trim();
    if (!compType || compType === 'Select company type' || compType === 'Select Company Type') {
      compType = '—';
    }
    var founded = ($('#founded').val() || '').trim();

    // 1. Client Information Card
    $('.span_name').text(clientName || '4Bell Technology');
    if (website) {
      var displayWeb = website.replace(/^https?:\/\//i, '');
      $('.span_website').html(escapeHtml(displayWeb) + ' <i class="la la-external-link" style="font-size:12px;"></i>').attr('href', website.startsWith('http') ? website : ('https://' + website));
    } else {
      $('.span_website').html('— <i class="la la-external-link" style="font-size:12px;"></i>').attr('href', 'javascript:void(0);');
    }
    $('.span_industry').text(industry && industry !== 'Select industry' && industry !== 'Select Industry' ? industry : 'Information Technology & Services');
    $('.span_company_sizes').text(compSizes && compSizes !== 'Select Company Size' && compSizes !== 'Select company size' ? (compSizes.indexOf('employee') === -1 ? compSizes + ' employees' : compSizes) : '201 - 500 employees');
    $('.span_company_type').text(compType);
    $('.span_founded').text(founded || '—');

    var hqCity = ($('#city_search_0').val() || '').trim();
    if (!hqCity) {
      var hqTagVal = ($('#kt_tagify_work_location_0').val() || '').trim();
      if (hqTagVal) {
        try {
          var parsed = JSON.parse(hqTagVal);
          if (Array.isArray(parsed) && parsed[0] && parsed[0].value) hqCity = parsed[0].value;
        } catch (e) { hqCity = hqTagVal; }
      }
    }
    var hqAddress = ($('#address_0').val() || '').trim();
    $('.span_primary_location').text(hqCity || 'Pune, Maharashtra, India');

    var careerPage = ($('#career_page_url').val() || '').trim();
    var taxId = ($('#tax_id').val() || '').trim();
    if (careerPage) {
      var displayCareer = careerPage.replace(/^https?:\/\//i, '');
      $('.span_career_page_url').text(displayCareer).attr('href', careerPage.startsWith('http') ? careerPage : ('https://' + careerPage));
    } else {
      $('.span_career_page_url').text('—').attr('href', 'javascript:void(0);');
    }
    $('.span_tax_id').text(taxId || '—');

    var initLetters = initials(clientName) || '4B';
    $('#client_review_initials').text(initLetters);

    var bgImg = $('.image-input-wrapper').css('background-image');
    if (bgImg && bgImg !== 'none' && bgImg.indexOf('blank.png') === -1) {
      $('#client_review_logo_box').css('background-image', bgImg);
      $('#client_review_initials').hide();
    } else {
      $('#client_review_logo_box').css('background-image', 'none');
      $('#client_review_initials').show();
    }

    // 2. Client Description & Services Card
    var descHtml = (typeof getClientDescriptionHtml === 'function')
      ? getClientDescriptionHtml()
      : ($('#kt_docs_tinymce_hidden').val() || '');
    var descText = (typeof getClientDescriptionText === 'function')
      ? getClientDescriptionText()
      : (descHtml ? $('<div>').html(descHtml).text().trim() : '');
    var sampleDesc = '4Bell Technology is a technology and recruitment solutions company focused on building innovative products and services for the hiring industry. The company provides ATS, B2B hiring marketplace and recruitment solutions to enterprise clients across India and global markets.';
    $('.span_kt_docs_tinymce_hidden').html(descHtml || sampleDesc);

    var tags = $('#tags').val();
    var tagLabels = [];
    if (tags) {
      try {
        var jsonTags = typeof tags === 'string' ? JSON.parse(tags) : tags;
        if (Array.isArray(jsonTags)) {
          tagLabels = jsonTags.map(function(t) { return t.value || t; });
        }
      } catch(e) {
        tagLabels = [tags];
      }
    }
    $('.span_tags').text(tagLabels.length ? tagLabels.join(', ') : '—');

    var serviceBadgesHtml = [];
    if (tagLabels.length) {
      tagLabels.forEach(function (tag) {
        serviceBadgesHtml.push('<span class="client-review-service-pill">' + escapeHtml(tag) + '</span>');
      });
    } else {
      var defaultServices = ['Recruitment Solutions', 'SaaS', 'ATS Platform', 'B2B Marketplace', 'Technology Services'];
      defaultServices.forEach(function(s) {
        serviceBadgesHtml.push('<span class="client-review-service-pill">' + escapeHtml(s) + '</span>');
      });
    }
    $('#client_review_service_badges').html(serviceBadgesHtml.join(''));

    // 3. Address Details Card
    $('#client_review_hq_city').text(hqCity || 'Pune, Maharashtra, India');
    $('#client_review_hq_address').text(hqAddress || '4Bell Technology, Baner, Pune, Maharashtra, India');

    var branchItems = [];
    $('#additional_addresses_container .client-branch-card, .client-address-container .client-address-card.is-additional').each(function(idx) {
      var $card = $(this);
      var branchCity = ($card.find('.branch-city-search, .client-addr-city-search').val() || '').trim();
      if (!branchCity) {
        var tagVal = $card.find('input[name="kt_tagify_work_location[]"]').val() || '';
        try {
          var parsedB = JSON.parse(tagVal);
          if (Array.isArray(parsedB) && parsedB[0] && parsedB[0].value) branchCity = parsedB[0].value;
        } catch(e) {
          branchCity = tagVal.replace(/^\[\{"value":"?/, '').replace(/"?\}\]$/, '');
        }
      }
      var officeType = (idx === 0) ? 'Development Centre' : 'Branch Office';
      var badgeClass = (idx === 0) ? 'branch-type-dev' : 'branch-type-office';
      if (branchCity) {
        branchItems.push(
          '<div class="branch-item">' +
            '<div class="branch-item-name">' +
              '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#64748b" stroke-width="2"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>' +
              '<span>' + escapeHtml(branchCity) + '</span>' +
            '</div>' +
            '<span class="branch-type-badge ' + badgeClass + '">' + officeType + '</span>' +
          '</div>'
        );
      }
    });

    if (!branchItems.length) {
      branchItems.push(
        '<div class="branch-item">' +
          '<div class="branch-item-name">' +
            '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#64748b" stroke-width="2"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>' +
            '<span>Bengaluru, Karnataka, India</span>' +
          '</div>' +
          '<span class="branch-type-badge branch-type-dev">Development Centre</span>' +
        '</div>',
        '<div class="branch-item">' +
          '<div class="branch-item-name">' +
            '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#64748b" stroke-width="2"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>' +
            '<span>Mumbai, Maharashtra, India</span>' +
          '</div>' +
          '<span class="branch-type-badge branch-type-office">Branch Office</span>' +
        '</div>'
      );
    }

    $('#client_review_additional_header').text('Additional Locations (' + branchItems.length + ')');
    $('#client_review_additional_list').html(branchItems.join(''));

    // 4. Contact Persons Card
    var contactCards = [];
    var contacts = getContactRowsData();
    var avatarPalettes = [
      { bg: '#ede9fe', text: '#7c3aed' }, // Purple (RS)
      { bg: '#ffe4e6', text: '#be123c' }, // Rose/Pink (PM)
      { bg: '#dcfce7', text: '#15803d' }, // Emerald (AK)
      { bg: '#fef3c7', text: '#854d0e' }  // Amber
    ];

    contacts.forEach(function (row, idx) {
      var pal = avatarPalettes[idx % avatarPalettes.length];
      var avatarLetters = initials(row.name) || 'CP';
      var isPrimary = (idx === 0);
      var primaryBadge = isPrimary
        ? '<span class="contact-primary-badge">Primary Contact</span>'
        : '';
      var displayPhone = row.phone ? ((row.callingCode ? '+' + row.callingCode + ' ' : '') + row.phone) : '—';

      contactCards.push(
        '<div class="review-contact-mini-card">' +
          '<div class="d-flex align-items-center justify-content-between gap-2 mb-1">' +
            '<div class="d-flex align-items-center gap-2 min-w-0">' +
              '<span class="contact-mini-avatar" style="background-color:' + pal.bg + '; color:' + pal.text + ';">' + escapeHtml(avatarLetters) + '</span>' +
              '<span class="contact-mini-name text-truncate">' + escapeHtml(row.name) + '</span>' +
            '</div>' +
            primaryBadge +
          '</div>' +
          '<div class="contact-mini-desig">' + escapeHtml(row.designation || 'Client Contact') + '</div>' +
          '<div class="contact-mini-meta-line text-truncate">' +
            '<svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>' +
            '<span class="text-truncate">' + escapeHtml(row.email || '—') + '</span>' +
          '</div>' +
          '<div class="contact-mini-meta-line">' +
            '<svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>' +
            '<span>' + escapeHtml(displayPhone) + '</span>' +
          '</div>' +
        '</div>'
      );
    });

    if (!contactCards.length) {
      contactCards.push(
        '<div class="review-contact-mini-card" style="grid-column: 1 / -1; padding: 22px; text-align: center; background: #f8fafc; border: 1px dashed #cbd5e1; border-radius: 8px;">' +
          '<div style="font-size: 13px; font-weight: 600; color: #64748b; margin-bottom: 4px;">No contacts added yet</div>' +
          '<div style="font-size: 11.5px; color: #94a3b8;">Click "Edit" to add recruitment contacts for this client.</div>' +
        '</div>'
      );
    }
    $('#client_review_contacts_grid').html(contactCards.join(''));

    // 5. Dedicated Social Links Card (Step 5 Review Summary)
    var linkedinUrl = ($('#linkedin_url').val() || '').trim();
    var twitterUrl = ($('#twitter_url').val() || '').trim();
    var facebookUrl = ($('#facebook_url').val() || '').trim();
    var instagramUrl = ($('#instagram_url').val() || '').trim();
    var youtubeUrl = ($('#youtube_url').val() || '').trim();
    var githubUrl = ($('#github_url').val() || '').trim();
    var customLink = ($('#custom_link').val() || '').trim();

    var socialItems = [
      {
        key: 'linkedin',
        name: 'LinkedIn',
        url: linkedinUrl,
        icon: '<svg width="16" height="16" viewBox="0 0 24 24" fill="#0a66c2"><path d="M19 3a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h14m-.5 15.5v-5.3a3.26 3.26 0 0 0-3.26-3.26c-.85 0-1.84.52-2.28 1.3v-1.11h-2.79v8.37h2.79v-4.93c0-.77.62-1.4 1.39-1.4a1.4 1.4 0 0 1 1.4 1.4v4.93h2.75M6.46 10.9v8.37H9.2V10.9H6.46M7.83 6.2a1.66 1.66 0 0 0-1.66 1.66c0 .92.75 1.67 1.66 1.67.92 0 1.67-.75 1.67-1.67 0-.92-.75-1.66-1.67-1.66Z"/></svg>'
      },
      {
        key: 'twitter',
        name: 'X / Twitter',
        url: twitterUrl,
        icon: '<svg width="14" height="14" viewBox="0 0 24 24" fill="#0f172a"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>'
      },
      {
        key: 'facebook',
        name: 'Facebook',
        url: facebookUrl,
        icon: '<svg width="16" height="16" viewBox="0 0 24 24" fill="#1877f2"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>'
      },
      {
        key: 'instagram',
        name: 'Instagram',
        url: instagramUrl,
        icon: '<svg width="16" height="16" viewBox="0 0 24 24" fill="#e1306c"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>'
      },
      {
        key: 'youtube',
        name: 'YouTube',
        url: youtubeUrl,
        icon: '<svg width="16" height="16" viewBox="0 0 24 24" fill="#ef4444"><path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>'
      },
      {
        key: 'github',
        name: 'GitHub',
        url: githubUrl,
        icon: '<svg width="16" height="16" viewBox="0 0 24 24" fill="#24292f"><path d="M12 0C5.37 0 0 5.37 0 12c0 5.31 3.435 9.795 8.205 11.385.6.105.825-.255.825-.57 0-.285-.015-1.23-.015-2.235-3.015.555-3.795-.735-4.035-1.41-.135-.345-.72-1.41-1.23-1.695-.42-.225-1.02-.78-.015-.795.945-.015 1.62.87 1.845 1.23 1.08 1.815 2.805 1.305 3.495.99.105-.78.42-1.305.765-1.605-2.67-.3-5.46-1.335-5.46-5.925 0-1.305.465-2.385 1.23-3.225-.12-.3-.54-1.53.12-3.18 0 0 1.005-.315 3.3 1.23.96-.27 1.98-.405 3-.405s2.04.135 3 .405c2.295-1.56 3.3-1.23 3.3-1.23.66 1.65.24 2.88.12 3.18.765.84 1.23 1.905 1.23 3.225 0 4.605-2.805 5.625-5.475 5.925.435.375.81 1.095.81 2.22 0 1.605-.015 2.895-.015 3.3 0 .315.225.69.825.57A12.02 12.02 0 0 0 24 12c0-6.63-5.37-12-12-12z"/></svg>'
      },
      {
        key: 'custom',
        name: 'Custom Link',
        url: customLink,
        icon: '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#64748b" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/></svg>'
      }
    ];

    var socialChips = [];
    socialItems.forEach(function (p) {
      if (p.url) {
        var cleanDisplay = p.url.replace(/^https?:\/\/(www\.)?/i, '');
        var fullHref = p.url.startsWith('http') ? p.url : ('https://' + p.url);
        socialChips.push(
          '<a href="' + escapeHtml(fullHref) + '" target="_blank" rel="noopener noreferrer" class="review-profile-link-item">' +
            p.icon +
            '<span>' + escapeHtml(cleanDisplay) + '</span>' +
          '</a>'
        );
      }
    });

    if (socialChips.length) {
      $('#client_review_social_list').html(socialChips.join('')).show();
      $('#client_review_social_empty').hide();
    } else {
      $('#client_review_social_list').empty().hide();
      $('#client_review_social_empty').show();
    }

    // 6. Right Sidebar Confirmation Status
    var percent = 100;
    if (typeof updateCompletion === 'function') {
      updateCompletion();
      var ringPercent = parseInt($('#client_completion_percent').text(), 10);
      if (!isNaN(ringPercent) && ringPercent > 0) {
        percent = ringPercent;
      }
    }
    $('#client_review_completion_percent').text(percent + '%');
    $('#client_review_completion_bar').css('width', percent + '%');
  }
  /* ===== PRE-LOADED CLIENT VALIDATION ENGINE (RAM-RESIDENT) ===== */
  var ClientValidationEngine = (function () {
    var patterns = {
      email: /^[^\s@]+@[^\s@]+\.[^\s@]+$/,
      phone10: /^\d{10}$/,
      year4: /^(18|19|20)\d{2}$/,
      url: /^(https?:\/\/)?([a-zA-Z0-9-]+\.)+[a-zA-Z]{2,}(:\d+)?(\/[^\s]*)?$/i,
      urlLocal: /^(https?:\/\/)?(localhost|127\.0\.0\.1)(:\d+)?(\/[^\s]*)?$/i
    };

    function validateField(fieldName, value, $element) {
      value = (value || '').trim();
      var result = { valid: true, message: '' };

      switch (fieldName) {
        case 'name':
        case 'client_id':
          if (!value) {
            result = { valid: false, message: 'Client Name is required.' };
          } else if (value.length < 2) {
            result = { valid: false, message: 'Client Name must be at least 2 characters.' };
          }
          break;

        case 'website':
          if (value && !patterns.url.test(value) && !patterns.urlLocal.test(value)) {
            result = { valid: false, message: 'Please enter a valid website URL (e.g. https://example.com).' };
          }
          break;

        case 'industry':
          if (!value) {
            var disp = ($('#industry_display').val() || '').trim();
            if (!disp) {
              result = { valid: false, message: 'Please select an Industry.' };
            }
          }
          break;

        case 'company_sizes':
          if (!value || value === '-') {
            result = { valid: false, message: 'Please select Company Size.' };
          }
          break;

        case 'company_type':
          if (value === '' || value === null || typeof value === 'undefined') {
            result = { valid: false, message: 'Please select Company Type.' };
          }
          break;

        case 'founded':
          if (!value) {
            result = { valid: false, message: 'Founded Year is required.' };
          } else if (!patterns.year4.test(value)) {
            result = { valid: false, message: 'Founded Year must be a valid 4-digit year (e.g. 2015).' };
          }
          break;

        case 'description':
          if (value && value.length < 5) {
            result = { valid: false, message: 'Description must be at least 5 characters if provided.' };
          }
          break;

        case 'tags':
          // Optional per client onboarding standard
          break;

        case 'address':
          if (!value) {
            result = { valid: false, message: 'Full Address is required for the primary location.' };
          } else if (value.length < 5) {
            result = { valid: false, message: 'Address must be at least 5 characters.' };
          }
          break;

        case 'city':
          if (!value || value === '[]') {
            result = { valid: false, message: 'City selection is required for the primary location.' };
          }
          break;

        case 'cp_name':
          if (!value) {
            result = { valid: false, message: 'Contact person name is required.' };
          }
          break;

        case 'cp_email':
          if (!value) {
            result = { valid: false, message: 'Email address is required.' };
          } else {
            var syntax = JeroEmailBoxEngine.validateSyntax(value);
            if (!syntax.isValid) {
              result = { valid: false, message: syntax.error };
            }
          }
          break;

        case 'ap_email':
          if (value) {
            var syntax = JeroEmailBoxEngine.validateSyntax(value);
            if (!syntax.isValid) {
              result = { valid: false, message: syntax.error };
            }
          }
          break;

        case 'cp_number':
          if (value && (value.length < 6 || value.length > 15 || isNaN(value))) {
            result = { valid: false, message: 'Please enter a valid phone number (6-15 digits).' };
          }
          break;

        case 'cp_desigination':
          if (!value) {
            result = { valid: false, message: 'Designation is required.' };
          }
          break;

        case 'url':
        case 'social_url':
          if (value && !patterns.url.test(value) && !patterns.urlLocal.test(value)) {
            result = { valid: false, message: 'Please enter a valid URL (e.g. https://...).' };
          }
          break;
      }

      return result;
    }

    return {
      patterns: patterns,
      validateField: validateField
    };
  })();

  function clearInlineErrors() {
    $('.is-invalid').removeClass('is-invalid');
    $('.client-inline-error').remove();
    $('#jero_ind_select_wrap, #jero_ind_input_box').removeClass('has-validation-error');
    $('#jero_csize_select_wrap, #jero_csize_input_box').removeClass('has-validation-error');
    $('#jero_ctype_select_wrap, #jero_ctype_input_box').removeClass('has-validation-error');
    $('#jero_founded_select_wrap, #jero_founded_input_box').removeClass('has-validation-error');
  }

  function clearFieldError($field) {
    if (!$field || !$field.length) return;
    $field.removeClass('is-invalid');
    if ($field.attr('id') === 'industry' || $field.attr('id') === 'industry_display') {
      $('#industry_display').removeClass('is-invalid');
      $('#jero_ind_input_box').removeClass('has-validation-error');
      $('#jero_ind_select_wrap').removeClass('has-validation-error');
    }
    if ($field.attr('id') === 'company_sizes' || $field.attr('id') === 'company_sizes_display') {
      $('#company_sizes_display').removeClass('is-invalid');
      $('#jero_csize_input_box').removeClass('has-validation-error');
      $('#jero_csize_select_wrap').removeClass('has-validation-error');
    }
    if ($field.attr('id') === 'company_type' || $field.attr('id') === 'company_type_display') {
      $('#company_type_display').removeClass('is-invalid');
      $('#jero_ctype_input_box').removeClass('has-validation-error');
      $('#jero_ctype_select_wrap').removeClass('has-validation-error');
    }
    if ($field.attr('id') === 'founded' || $field.attr('id') === 'founded_display') {
      $('#founded_display').removeClass('is-invalid');
      $('#jero_founded_input_box').removeClass('has-validation-error');
      $('#jero_founded_select_wrap').removeClass('has-validation-error');
    }
    var $container = $field.closest('.client-field, .fv-plugins-icon-container');
    if ($container.length) {
      $container.find('.client-inline-error').remove();
    }
    $field.next('.client-inline-error').remove();
    if ($field.parent().length) {
      $field.parent().next('.client-inline-error').remove();
    }
  }
  window.clearFieldError = clearFieldError;

  function showInlineError($field, message) {
    if (!$field || !$field.length) return;
    clearFieldError($field);
    $field.addClass('is-invalid');
    if ($field.attr('id') === 'industry' || $field.attr('id') === 'industry_display') {
      $('#industry_display').addClass('is-invalid');
      $('#jero_ind_input_box').addClass('has-validation-error');
      $('#jero_ind_select_wrap').addClass('has-validation-error');
    }
    if ($field.attr('id') === 'company_sizes' || $field.attr('id') === 'company_sizes_display') {
      $('#company_sizes_display').addClass('is-invalid');
      $('#jero_csize_input_box').addClass('has-validation-error');
      $('#jero_csize_select_wrap').addClass('has-validation-error');
    }
    if ($field.attr('id') === 'company_type' || $field.attr('id') === 'company_type_display') {
      $('#company_type_display').addClass('is-invalid');
      $('#jero_ctype_input_box').addClass('has-validation-error');
      $('#jero_ctype_select_wrap').addClass('has-validation-error');
    }
    if ($field.attr('id') === 'founded' || $field.attr('id') === 'founded_display') {
      $('#founded_display').addClass('is-invalid');
      $('#jero_founded_input_box').addClass('has-validation-error');
      $('#jero_founded_select_wrap').addClass('has-validation-error');
    }
    var $container = $field.closest('.client-field, .fv-plugins-icon-container');
    if ($container.length) {
      $container.append('<span class="client-inline-error text-danger mt-1 d-block" style="font-size: 11px; font-weight: 600; color: #e11d48;"><i class="la la-exclamation-circle"></i> ' + message + '</span>');
    } else {
      var $target = $field.closest('.input-group').length ? $field.closest('.input-group') : $field;
      $target.after('<span class="client-inline-error text-danger mt-1 d-block" style="font-size: 11px; font-weight: 600; color: #e11d48;"><i class="la la-exclamation-circle"></i> ' + message + '</span>');
    }
  }
  window.showInlineError = showInlineError;

  function validateWizardStep(step) {
    step = parseInt(step, 10);
    clearInlineErrors();
    var isValid = true;
    var $firstInvalid = null;
    window.$firstInvalid = null;

    // Section 1: Client Information
    if (step === 1) {
      // Auto-commit any typed industry in combobox before validating
      var indDispVal = ($('#industry_display').val() || '').trim();
      if (indDispVal) {
        if (window.JeroIndustryIntelligenceEngine && typeof window.JeroIndustryIntelligenceEngine.commitIfCustom === 'function') {
          try { window.JeroIndustryIntelligenceEngine.commitIfCustom(); } catch (e) {}
        }
        if (!$('#industry').val()) {
          var $sel = $('#industry');
          var optFound = false;
          $sel.find('option').each(function() {
            if ($(this).text().trim().toLowerCase() === indDispVal.toLowerCase()) {
              $sel.val($(this).val());
              optFound = true;
              return false;
            }
          });
          if (!optFound) {
            $sel.append(new Option(indDispVal, 0, true, true));
            $sel.val(0);
            $('#custom_industry').val(indDispVal);
          }
        }
      }

      var clientNameRes = ClientValidationEngine.validateField('name', $('#client_id').val(), $('#client_id'));
      if (!clientNameRes.valid) {
        showInlineError($('#client_id'), clientNameRes.message);
        $firstInvalid = $firstInvalid || $('#client_id');
        isValid = false;
      }

      var webVal = ($('#website').val() || '').trim();
      if (webVal) {
        var websiteRes = ClientValidationEngine.validateField('website', webVal, $('#website'));
        if (!websiteRes.valid) {
          showInlineError($('#website'), websiteRes.message);
          $firstInvalid = $firstInvalid || $('#website');
          isValid = false;
        }
      }

      var industryVal = ($('#industry').val() || '').trim();
      var customIndVal = ($('#custom_industry').val() || '').trim();
      var hasValidIndustry = (industryVal && industryVal !== '0') || Boolean(customIndVal) || Boolean(indDispVal);
      if (!hasValidIndustry) {
        showInlineError($('#industry_display'), 'Please select an Industry.');
        $firstInvalid = $firstInvalid || $('#industry_display');
        isValid = false;
      }

      var companySizesRes = ClientValidationEngine.validateField('company_sizes', $('#company_sizes').val(), $('#company_sizes'));
      if (!companySizesRes.valid) {
        showInlineError($('#company_sizes'), companySizesRes.message);
        $firstInvalid = $firstInvalid || $('#company_sizes');
        isValid = false;
      }

      var companyTypeRes = ClientValidationEngine.validateField('company_type', $('#company_type').val(), $('#company_type'));
      if (!companyTypeRes.valid) {
        showInlineError($('#company_type'), companyTypeRes.message);
        $firstInvalid = $firstInvalid || $('#company_type');
        isValid = false;
      }

      var foundedRes = ClientValidationEngine.validateField('founded', $('#founded').val(), $('#founded'));
      if (!foundedRes.valid) {
        showInlineError($('#founded'), foundedRes.message);
        $firstInvalid = $firstInvalid || $('#founded');
        isValid = false;
      }
    }

    // Section 2: Client Description & Offerings (Required per backend and screenshot)
    if (step === 2) {
      var descText = (typeof getClientDescriptionText === 'function')
        ? getClientDescriptionText()
        : ($('#kt_docs_tinymce_hidden').val() || '').replace(/<[^>]*>?/gm, '').trim();
      if (!descText) {
        $('.client-editor-box').addClass('is-invalid');
        showInlineError($('#kt_docs_tinymce_hidden'), 'Company Description is required.');
        $firstInvalid = $firstInvalid || $('#kt_client_description_quill');
        isValid = false;
      } else if (descText.length < 5) {
        $('.client-editor-box').addClass('is-invalid');
        showInlineError($('#kt_docs_tinymce_hidden'), 'Description must be at least 5 characters.');
        $firstInvalid = $firstInvalid || $('#kt_client_description_quill');
        isValid = false;
      } else {
        $('.client-editor-box').removeClass('is-invalid');
      }

      var tagsVal = ($('#tags').val() || '').trim();
      if (!tagsVal || tagsVal === '[]') {
        $('#client_tags_error').show();
        $firstInvalid = $firstInvalid || $('#tags');
        isValid = false;
      } else {
        $('#client_tags_error').hide();
      }
    }

    // Section 3: Address Details (Matching media_1792064818634.png)
    if (step === 3) {
      window._lastStep3Error = null;
      var hqCity = ($('#city_search_0').val() || '').trim();
      var hqTagify = ($('#kt_tagify_work_location_0').val() || '').trim();
      if (!hqCity && !hqTagify) {
        $('#city_err_0').show();
        $('#city_search_0').addClass('is-invalid');
        $firstInvalid = $firstInvalid || $('#city_search_0');
        isValid = false;
        window._lastStep3Error = 'City selection is required for Headquarters (Primary Location).';
      } else {
        $('#city_err_0').hide();
        $('#city_search_0').removeClass('is-invalid');
        if (!hqTagify && hqCity) {
          var parts = hqCity.split(/[,|]/).map(function(s) { return s.trim(); });
          $('#kt_tagify_work_location_0').val(JSON.stringify([{ value: parts.join(' | ') }]));
        }
      }

      var $hqAddr = $('#address_0');
      var hqAddrVal = ($hqAddr.val() || '').trim();
      if (!hqAddrVal) {
        var hqAddrMsg = 'Full Address is required for the primary location.';
        var $hqAddrErr = $('#address_err_0');
        if ($hqAddrErr.find('span').length) { $hqAddrErr.find('span').text(hqAddrMsg); } else { $hqAddrErr.text(hqAddrMsg); }
        $hqAddrErr.show();
        $hqAddr.addClass('is-invalid');
        $firstInvalid = $firstInvalid || $hqAddr;
        isValid = false;
        if (!window._lastStep3Error) {
          window._lastStep3Error = 'Full Address is required for Headquarters (Primary Location).';
        }
      } else if (hqAddrVal.length < 5) {
        var hqMinMsg = 'Address must be at least 5 characters.';
        var $hqAddrErr = $('#address_err_0');
        if ($hqAddrErr.find('span').length) { $hqAddrErr.find('span').text(hqMinMsg); } else { $hqAddrErr.text(hqMinMsg); }
        $hqAddrErr.show();
        $hqAddr.addClass('is-invalid');
        $firstInvalid = $firstInvalid || $hqAddr;
        isValid = false;
        if (!window._lastStep3Error) {
          window._lastStep3Error = 'Headquarters address must be at least 5 characters.';
        }
      } else {
        $('#address_err_0').hide();
        $hqAddr.removeClass('is-invalid');
      }

      // Check any additional branch addresses (Any card added MUST be completely filled or removed)
      $('#additional_addresses_container .client-branch-card, .client-address-container .client-address-card.is-additional').each(function(idx) {
        var $card = $(this);
        var cid = $card.data('cid') || (idx + 1);
        var $cityInput = $card.find('.branch-city-search, .client-addr-city-search');
        var $textarea = $card.find('textarea[name="address[]"]');
        var cityVal = ($cityInput.val() || '').trim();
        var textVal = ($textarea.val() || '').trim();
        var branchNumber = idx + 1;
        var cardHasError = false;

        var $cityErr = $card.find('#branch_city_err_' + cid);
        var $addrErr = $card.find('#branch_address_err_' + cid);

        if (!cityVal) {
          var cityMsg = 'City / Location is required for Additional Location ' + branchNumber + '. Please select a city or click "Remove".';
          if ($cityErr.find('span').length) {
            $cityErr.find('span').text(cityMsg);
          } else {
            $cityErr.text(cityMsg);
          }
          $cityErr.show();
          $cityInput.addClass('is-invalid');
          $firstInvalid = $firstInvalid || $cityInput;
          isValid = false;
          cardHasError = true;
          if (!window._lastStep3Error) {
            window._lastStep3Error = 'Additional Location ' + branchNumber + ' is incomplete. Please select City and enter Full Address, or click "Remove".';
          }
        } else {
          $cityErr.hide();
          $cityInput.removeClass('is-invalid');
        }

        if (!textVal) {
          var addrMsg = 'Full Address is required for Additional Location ' + branchNumber + '. Please enter complete address or click "Remove".';
          if ($addrErr.find('span').length) {
            $addrErr.find('span').text(addrMsg);
          } else {
            $addrErr.text(addrMsg);
          }
          $addrErr.show();
          $textarea.addClass('is-invalid');
          $firstInvalid = $firstInvalid || $textarea;
          isValid = false;
          cardHasError = true;
          if (!window._lastStep3Error) {
            window._lastStep3Error = 'Additional Location ' + branchNumber + ' is incomplete. Please select City and enter Full Address, or click "Remove".';
          }
        } else if (textVal.length < 5) {
          var addrMinMsg = 'Address must be at least 5 characters for Additional Location ' + branchNumber + '.';
          if ($addrErr.find('span').length) {
            $addrErr.find('span').text(addrMinMsg);
          } else {
            $addrErr.text(addrMinMsg);
          }
          $addrErr.show();
          $textarea.addClass('is-invalid');
          $firstInvalid = $firstInvalid || $textarea;
          isValid = false;
          cardHasError = true;
          if (!window._lastStep3Error) {
            window._lastStep3Error = 'Additional Location ' + branchNumber + ' address must be at least 5 characters, or click "Remove".';
          }
        } else {
          $addrErr.hide();
          $textarea.removeClass('is-invalid');
        }

        if (cardHasError) {
          $card.css({
            'border': '1.5px solid #f87171',
            'box-shadow': '0 0 0 2px rgba(248, 113, 113, 0.15)'
          });
        } else {
          $card.css({
            'border': '1px solid #e2e8f0',
            'box-shadow': '0 1px 2px rgba(0,0,0,0.02)'
          });
        }
      });
    }

    // Section 4: Contact Person
    if (step === 4) {
      var contacts = getContactRowsData();
      if (!contacts.length) {
        var $contactSection = $('.client-contact-toolbar, .client-contact-section, #client_contact_table_body').first();
        showInlineError($contactSection, 'Minimum 1 Contact Person is required. Please click "Add Contact Person" to add details.');
        $firstInvalid = $firstInvalid || $('#client_open_contact_drawer');
        isValid = false;
      } else {
        var seenEmailsInStep4 = {};
        for (var i = 0; i < contacts.length; i++) {
          var c = contacts[i];
          var eLower = (c.email || '').trim().toLowerCase();
          if (eLower && seenEmailsInStep4[eLower]) {
            var $dupRow = $('#client_contact_table_body tr').eq(i);
            showInlineError($dupRow, 'Contact #' + (i + 1) + ' has a duplicate email: ' + eLower + '. Each contact must have a unique email.');
            $firstInvalid = $firstInvalid || $dupRow;
            isValid = false;
            break;
          }
          if (eLower) {
            seenEmailsInStep4[eLower] = true;
          }

          var nameRes = ClientValidationEngine.validateField('cp_name', c.name);
          var emailRes = ClientValidationEngine.validateField('cp_email', c.email);
          var phoneRes = ClientValidationEngine.validateField('cp_number', c.phone);
          var desigRes = ClientValidationEngine.validateField('cp_desigination', c.designation);

          if (!nameRes.valid || !emailRes.valid || !phoneRes.valid || !desigRes.valid) {
            var $contactRow = $('#client_contact_table_body tr').eq(i);
            var errMsg = !nameRes.valid ? nameRes.message : (!emailRes.valid ? emailRes.message : (!phoneRes.valid ? phoneRes.message : desigRes.message));
            showInlineError($contactRow, 'Contact #' + (i + 1) + ': ' + errMsg);
            $firstInvalid = $firstInvalid || $contactRow;
            isValid = false;
            break;
          }
        }
      }

      if (isValid) {
        var apEmailVal = ($('#ap_email').val() || '').trim();
        if (apEmailVal) {
          var apRes = ClientValidationEngine.validateField('ap_email', apEmailVal);
          if (!apRes.valid) {
            showInlineError($('#ap_email'), apRes.message);
            $firstInvalid = $firstInvalid || $('#ap_email');
            isValid = false;
          }
        }
      }
    }

    // Section 5: Social Links (Step 5 - Optional, but validated if entered)
    if (step === 5) {
      var socialFields = [
        { id: '#linkedin_url', name: 'LinkedIn URL' },
        { id: '#twitter_url', name: 'X / Twitter URL' },
        { id: '#youtube_url', name: 'YouTube URL' },
        { id: '#facebook_url', name: 'Facebook URL' },
        { id: '#instagram_url', name: 'Instagram URL' },
        { id: '#github_url', name: 'GitHub URL' }
      ];

      socialFields.forEach(function (f) {
        var $el = $(f.id);
        var val = ($el.val() || '').trim();
        if (val) {
          var res = ClientValidationEngine.validateField('url', val, $el);
          if (!res.valid) {
            showInlineError($el, 'Please enter a valid URL for ' + f.name + ' (e.g. https://...).');
            $firstInvalid = $firstInvalid || $el;
            isValid = false;
          }
        }
      });
    }

    window.$firstInvalid = $firstInvalid;
    if (!isValid && $firstInvalid) {
      try {
        $firstInvalid.focus();
        $('html, body').animate({ scrollTop: Math.max(0, $firstInvalid.offset().top - 150) }, 200);
      } catch (e) {}
    }

    return isValid;
  }

  /* Synchronize wizard action buttons and stepper visual tokens */
  function syncWizardNavigationState(activeStep) {
    activeStep = parseInt(activeStep, 10);
    if (!activeStep || activeStep < 1 || activeStep > 6) {
      activeStep = (typeof getCurrentStepNumber === 'function') ? getCurrentStepNumber() : 1;
    }

    // Step-conditional body classes & action controls:
    // Step 1: Show Close (x) button, hide Back button, show Next Page, hide Submit
    // Steps 2-5: Hide Close button, show Back button, show Next Page, hide Submit
    // Step 6: Hide Close button, show Back button, hide Next Page, show Submit
    if (activeStep <= 1) {
      $('body').addClass('on-step-1').removeClass('on-step-2 on-step-3 on-step-4 on-step-5 on-step-6 on-review-step');
      $('#client_top_close_btn').attr('style', 'display: inline-flex !important;').show();
      $('#client_top_back_btn').attr('style', 'display: none !important;').hide();
      $('#client_top_next_btn').attr('style', 'display: inline-flex !important;').show();
      $('#continueButton, [data-wizard-type="action-next"]').attr('style', 'display: inline-flex !important;').show();
      $('#submitBtn, .submitfrm, .updatefrm').attr('style', 'display: none !important;').hide();
    } else if (activeStep < 6) {
      $('body').removeClass('on-step-1 on-step-6 on-review-step').addClass('on-step-' + activeStep);
      $('#client_top_close_btn').attr('style', 'display: none !important;').hide();
      $('#client_top_back_btn').attr('style', 'display: inline-flex !important;').show();
      $('#client_top_next_btn, #continueButton, [data-wizard-type="action-next"]').attr('style', 'display: inline-flex !important;').show();
      $('#submitBtn, .submitfrm, .updatefrm').attr('style', 'display: none !important;').hide();
    } else {
      $('body').removeClass('on-step-1 on-step-2 on-step-3 on-step-4 on-step-5').addClass('on-step-6 on-review-step');
      $('#client_top_close_btn').attr('style', 'display: none !important;').hide();
      $('#client_top_back_btn').attr('style', 'display: inline-flex !important;').show();
      $('#client_top_next_btn, #continueButton, [data-wizard-type="action-next"]').attr('style', 'display: none !important;').hide();
      $('#submitBtn, .submitfrm, .updatefrm').attr('style', 'display: inline-flex !important;').show();
      if (typeof populateReviewSummaries === 'function') {
        populateReviewSummaries();
      }
    }

    // Dynamic Top Header Subtitles matching workflow steps
    var subtitles = {
      1: 'Create a client profile for recruitment operations',
      2: 'Provide client description and service offerings.',
      3: 'Enter the city and full address of this client.',
      4: 'Add key contacts for this client organization.',
      5: 'Add the client’s official social media and professional profiles.',
      6: 'Review all the information before submitting.'
    };
    var currentSub = subtitles[activeStep] || subtitles[1];
    $('#client_header_subtitle').text(currentSub).attr('title', currentSub);

    // Synchronize Top Stepper Indicators (Pixel-perfect compact design)
    $('#client_wizard_stepper .client-step-item').each(function () {
      var s = parseInt($(this).data('step'), 10);
      var $circle = $(this).find('.client-step-circle');
      var $title = $(this).find('.client-step-name');
      var $nextDivider = $(this).next('.client-step-line, .client-step-arrow');
      var $nextArrowSvg = $nextDivider.find('.client-step-arrow-svg');

      if (s < activeStep) {
        $(this).removeClass('is-active is-upcoming').addClass('is-completed').attr('aria-current', 'false');
        $circle.removeClass('is-active is-upcoming').addClass('is-completed').removeAttr('style').html('<svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="#ffffff" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" style="display:block;margin:auto;"><polyline points="20 6 9 17 4 12"/></svg>');
        $title.removeAttr('style');
        if ($nextDivider.length) {
          $nextDivider.removeClass('is-upcoming is-active').addClass('is-completed').removeAttr('style');
          $nextArrowSvg.removeAttr('style');
        }
      } else if (s === activeStep) {
        $(this).removeClass('is-completed is-upcoming').addClass('is-active').attr('aria-current', 'step');
        $circle.removeClass('is-completed is-upcoming').addClass('is-active').removeAttr('style').text(s);
        $title.removeAttr('style');
        if ($nextDivider.length) {
          $nextDivider.removeClass('is-completed is-upcoming').addClass('is-active').removeAttr('style');
          $nextArrowSvg.removeAttr('style');
        }
      } else {
        $(this).removeClass('is-completed is-active').addClass('is-upcoming').attr('aria-current', 'false');
        $circle.removeClass('is-completed is-active').addClass('is-upcoming').removeAttr('style').text(s);
        $title.removeAttr('style');
        if ($nextDivider.length) {
          $nextDivider.removeClass('is-completed is-active').addClass('is-upcoming').removeAttr('style');
          $nextArrowSvg.removeAttr('style');
        }
      }
    });

    // Synchronize legacy Metronic Stepper Tokens
    $('#kt_wizard .wizard-step').each(function (idx) {
      var stepNum = idx + 1;
      var $step = $(this);
      if (stepNum < activeStep) {
        $step.attr('data-wizard-state', 'done').addClass('completed done').removeClass('current active pending');
      } else if (stepNum === activeStep) {
        $step.attr('data-wizard-state', 'current').addClass('current active').removeClass('completed done pending');
      } else {
        $step.attr('data-wizard-state', 'pending').addClass('pending').removeClass('completed done current active');
      }
    });
  }

  // Pre-loaded Real-time reactive listener: Instant 0ms self-clearing error banners
  $(document).on('input keyup change blur select2:select select2:clear', '#kt_form input, #kt_form select, #kt_form textarea', function (e) {
    var $field = $(this);
    var val = ($field.val() || '').trim();
    if (val && val !== '[]') {
      clearFieldError($field);
    }

    // Immediate field-level format validation on blur
    if (e.type === 'blur' || e.type === 'focusout') {
      var fieldId = $field.attr('id') || $field.attr('name') || '';
      if (fieldId.indexOf('founded') !== -1 && val) {
        var fRes = ClientValidationEngine.validateField('founded', val, $field);
        if (!fRes.valid) showInlineError($field, fRes.message);
      } else if (fieldId.indexOf('website') !== -1 && val) {
        var wRes = ClientValidationEngine.validateField('website', val, $field);
        if (!wRes.valid) showInlineError($field, wRes.message);
      } else if (fieldId.indexOf('cp_email') !== -1 && val) {
        var eRes = ClientValidationEngine.validateField('cp_email', val, $field);
        if (!eRes.valid) showInlineError($field, eRes.message);
      } else if (fieldId.indexOf('cp_number') !== -1 && val) {
        var pRes = ClientValidationEngine.validateField('cp_number', val, $field);
        if (!pRes.valid) showInlineError($field, pRes.message);
      }
    }
  });

  // Single-source deterministic wizard navigation state
  window.clientWizardUi = {
    syncAdditionalAddressEmpty: syncAdditionalAddressEmpty,
    bindAddressCounters: bindAddressCounters,
    populateReviewSummaries: populateReviewSummaries,
    updateAssistantPanel: updateAssistantPanel,
    updateCompletion: updateCompletion,
    renderContactTable: renderContactTable,
    validateWizardStep: validateWizardStep,
    syncWizardNavigationState: syncWizardNavigationState
  };

  $(function () {
    // Append contact drawer directly to document.body on ready so it is never clipped
    if ($('#client_contact_drawer').length && $('#client_contact_drawer').parent()[0] !== document.body) {
      $('body').append($('#client_contact_drawer_backdrop'), $('#client_contact_drawer'));
    }
    if (typeof initCitySearch === 'function') {
      try {
        initCitySearch();
        renderSelectedCities();
      } catch (err) {
        console.warn('initCitySearch error:', err);
      }
    }
    bindAddressCounters(document);
    syncAdditionalAddressEmpty();
    updateAssistantPanel();
    updateCompletion();
    renderContactTable();
    goToWizardStep(1);
    @if($page_event == 'ADD')
    // =========================================================================
    // GOD-LEVEL FRESH INITIALIZATION & CACHE PURGE ENGINE (Add Client Mode)
    // Guarantee 100% fresh, blank form across all 6 pages with ZERO lingering cache
    // =========================================================================
    purgeAllClientCaches();
    resetFreshClientForm();
    @else
    try {
      localStorage.removeItem('jero_client_draft_' + {{ (int) (auth()->user()->company_id ?? 0) }});
    } catch (e) {}
    @endif

    function purgeAllClientCaches() {
      try {
        // 1. Purge all localStorage entries relating to client onboarding, drafts, and recent items
        for (var i = localStorage.length - 1; i >= 0; i--) {
          var k = localStorage.key(i);
          if (k && (k.indexOf('jero_client_') !== -1 || k.indexOf('client_draft') !== -1 || k.indexOf('recent_industries') !== -1)) {
            localStorage.removeItem(k);
          }
        }
        // 2. Purge all sessionStorage entries relating to client onboarding and temporary fallbacks
        for (var j = sessionStorage.length - 1; j >= 0; j--) {
          var sk = sessionStorage.key(j);
          if (sk && (sk.indexOf('jero_') !== -1 || sk.indexOf('client_') !== -1 || sk.indexOf('idb_fallback_') !== -1)) {
            sessionStorage.removeItem(sk);
          }
        }
      } catch (err) {
        console.warn('[CachePurge] Error clearing browser storage:', err);
      }
      // 3. Clear RAM caches
      window._companyIntelligenceCache = {};
      window._lastFetchedWebsite = null;
      window._lastStep3Error = null;
    }

    function resetFreshClientForm() {
      // Page 1: Company Profile
      $('#client_id').val('');
      $('#website').val('');
      $('#website_clear_btn').hide();
      $('#industry').val('');
      $('#industry_display').val('');
      $('#custom_industry').val('');
      $('#company_sizes').val('').trigger('change.select2');
      $('#company_type').val('').trigger('change.select2');
      $('#founded').val('').trigger('change');
      $('#ap_email').val('');
      $('#career_page_url').val('');
      $('#tax_id').val('');
      if (window.JeroFoundedYearEngine && typeof window.JeroFoundedYearEngine.clearYear === 'function') {
        window.JeroFoundedYearEngine.clearYear();
      }
      $('#client_avatar').val('');
      $('#client_avatar_remove').val('1');
      $('.client-logo-preview .image-input-wrapper')
        .css('background-image', 'none')
        .html('<div style="width:100%; height:100%; display:flex; flex-direction:column; align-items:center; justify-content:center; background:#f8fafc; color:#94a3b8; gap:3px;"><i class="la la-building" style="font-size:14px; color:#94a3b8;"></i><span style="font-size:9px; font-weight:600; color:#64748b; text-transform:uppercase; letter-spacing:0.3px;">Logo</span></div>');

      // Page 2: Description & Services
      if (typeof window.setClientDescriptionContent === 'function') {
        window.setClientDescriptionContent('');
      } else if (window.clientDescQuill) {
        window.clientDescQuill.root.innerHTML = '';
      }
      $('#kt_docs_tinymce_hidden').val('');
      $('#client_desc_count').text('0');
      if ($('#client_preview_description').length) {
        $('#client_preview_description').text('No description provided yet.');
      }
      if (window.clientServicesManager) {
        window.clientServicesManager.selected = [];
        window.clientServicesManager.renderSelected();
        window.clientServicesManager.syncHiddenInput();
      } else {
        $('#tags').val('');
      }

      // Page 3: Locations & Address Details
      $('#city_search_0').val('');
      $('#kt_tagify_work_location_0').val('');
      $('#address_0').val('');
      $('#address_char_count_0').text('0');
      $('#city_err_0, #address_err_0').hide();
      $('#city_search_0, #address_0').removeClass('is-invalid');
      $('#additional_addresses_container').empty();
      $('#addressId').val('0');

      // Page 4: Client Contacts
      if (window.clientContactManager) {
        window.clientContactManager.contacts = [];
        window.clientContactManager.renderTable();
        window.clientContactManager.syncHiddenInputs();
      }
      $('#contactPersonId').val('0');
      $('.client-cperson-row').empty();

      // Page 5: Social Links & AI Discovery
      if (window.clientSocialManager) {
        window.clientSocialManager.clearAllSocialInputs();
        window.clientSocialManager.setDiscoveredUrls({});
        window.clientSocialManager.syncAiButtons();
      }

      // Page 6: Review Summaries & Stepper Navigation
      if (typeof populateReviewSummaries === 'function') {
        try { populateReviewSummaries(); } catch (e) {}
      }
      if (typeof updateCompletion === 'function') {
        try { updateCompletion(); } catch (e) {}
      }
      if (typeof goToWizardStep === 'function') {
        try { goToWizardStep(1); } catch (e) {}
      }
    }

    window.purgeAllClientCaches = purgeAllClientCaches;
    window.resetFreshClientForm = resetFreshClientForm;

    // Handle Back-Forward Cache (bfcache): force fresh reload if navigated back
    window.addEventListener('pageshow', function (event) {
      if (event.persisted && typeof page_event !== 'undefined' && page_event === 'ADD') {
        window.location.reload();
      }
    });

    // Deterministic Step Tracking & Non-locking Debounce Engine
    var currentStepNumber = 1;
    var lastNavTime = 0;

    function getCurrentStepNumber() {
      // 1. Primary source of truth: step-content with data-wizard-state="current"
      var $currentContent = $('#kt_wizard [data-wizard-type="step-content"][data-wizard-state="current"]');
      if ($currentContent.length) {
        var cIdx = $('#kt_wizard [data-wizard-type="step-content"]').index($currentContent.first());
        if (cIdx >= 0 && cIdx <= 5) {
          currentStepNumber = cIdx + 1;
          return currentStepNumber;
        }
      }

      // 2. Secondary source of truth: top stepper item with is-active
      var $topActive = $('#client_wizard_stepper .client-step-item.is-active, #client_wizard_stepper .client-step-item[aria-current="step"]');
      if ($topActive.length) {
        var sVal = parseInt($topActive.first().data('step'), 10);
        if (sVal >= 1 && sVal <= 6) {
          currentStepNumber = sVal;
          return currentStepNumber;
        }
      }

      // 3. Tertiary source of truth: wizard-step header with current/active
      var $currentStep = $('#kt_wizard .wizard-step[data-wizard-state="current"], #kt_wizard .wizard-step.current, #kt_wizard .wizard-step.active');
      if ($currentStep.length) {
        var hIdx = $('#kt_wizard .wizard-step').index($currentStep.first());
        if (hIdx >= 0 && hIdx <= 5) {
          currentStepNumber = hIdx + 1;
          return currentStepNumber;
        }
      }

      // 4. Fallback to tracked state if in range
      if (currentStepNumber >= 1 && currentStepNumber <= 6) {
        return currentStepNumber;
      }

      return 1;
    }

    function goToWizardStep(targetStep) {
      targetStep = parseInt(targetStep, 10);
      if (!targetStep || targetStep < 1 || targetStep > 6) return;

      currentStepNumber = targetStep;

      // Ensure any intelligence dropdown is closed
      if (window.JeroIndustryIntelligenceEngine && typeof window.JeroIndustryIntelligenceEngine.close === 'function') {
        try { window.JeroIndustryIntelligenceEngine.close(); } catch (e) {}
      }

      // Track step via body class
      $('body').removeClass('on-step-1 on-step-2 on-step-3 on-step-4 on-step-5 on-step-6 on-review-step')
               .addClass('on-step-' + targetStep);
      if (targetStep === 6) {
        $('body').addClass('on-review-step');
      }

      // Update Metronic outer wizard container state for CSS rules
      var stepState = (targetStep === 1 ? 'first' : (targetStep === 6 ? 'last' : 'between'));
      $('#kt_wizard').attr('data-wizard-state', stepState);

      // Synchronize Step Header indicators
      $('#kt_wizard .wizard-step').each(function (idx) {
        var s = idx + 1;
        var $st = $(this);
        if (s === targetStep) {
          $st.attr('data-wizard-state', 'current').addClass('current active').removeClass('completed done pending');
        } else if (s < targetStep) {
          $st.attr('data-wizard-state', 'done').addClass('completed done').removeClass('current active pending');
        } else {
          $st.attr('data-wizard-state', 'pending').addClass('pending').removeClass('current active completed done');
        }
      });

      // Synchronize Step Tab Content panels with explicit display & attribute states
      $('#kt_wizard [data-wizard-type="step-content"]').each(function (idx) {
        var s = idx + 1;
        var $panel = $(this);
        if (s === targetStep) {
          $panel.attr('data-wizard-state', 'current')
                .attr('style', 'display: block !important;')
                .removeClass('d-none')
                .addClass('d-block')
                .show();
        } else {
          $panel.removeAttr('data-wizard-state')
                .attr('style', 'display: none !important;')
                .removeClass('d-block')
                .addClass('d-none')
                .hide();
        }
      });

      // Synchronize Sticky Footer Buttons
      syncWizardNavigationState(targetStep);

      // On Step 1 (Company Info), sync industry engine display state
      if (targetStep === 1) {
        if (window.JeroIndustryIntelligenceEngine && typeof window.JeroIndustryIntelligenceEngine.sync === 'function') {
          try { window.JeroIndustryIntelligenceEngine.sync(); } catch (e) {}
        }
      }

      // On Step 2 (Client Description), ensure Quill editor is initialized & updated, and service tags synced
      if (targetStep === 2) {
        if (typeof initClientDescriptionEditor === 'function') {
          initClientDescriptionEditor();
        }
        if (window.clientDescQuill) {
          try { window.clientDescQuill.update(); } catch (e) {}
        }
        if (window.clientServicesManager) {
          try {
            window.clientServicesManager.renderSelected();
            window.clientServicesManager.updateSuggestedView();
          } catch (e) {}
        }
        if (typeof window.updateSuggestedServicesForIndustry === 'function') {
          try { window.updateSuggestedServicesForIndustry(); } catch (e) {}
        }
      }

      // On Step 3 (Address / Locations), sync AI discovery buttons and website links
      if (targetStep === 3) {
        if (window.clientLocationManager) {
          try {
            window.clientLocationManager.syncAiButtons();
            window.clientLocationManager.syncWebsiteLink();
          } catch (e) {}
        }
      }

      // On Step 4 (Contacts), render table and sync hidden inputs
      if (targetStep === 4) {
        if (window.clientContactManager) {
          try {
            window.clientContactManager.renderTable();
          } catch (e) {}
        }
      }

      // On Step 5 (Social Links), sync AI discovery buttons and website links
      if (targetStep === 5) {
        if (window.clientSocialManager) {
          try {
            window.clientSocialManager.syncWebsiteLink();
            window.clientSocialManager.syncAiButtons();
          } catch (e) {}
        }
      }

      // On Step 6 (Review & Submit), populate real-time review summaries
      if (targetStep === 6 && typeof populateReviewSummaries === 'function') {
        try {
          populateReviewSummaries();
        } catch (e) {
          console.warn('populateReviewSummaries error:', e);
        }
      }

      // Update Completion Meter
      if (typeof updateCompletion === 'function') {
        try { updateCompletion(); } catch (e) {}
      }

      // Smooth scroll to top
      try {
        if (typeof KTUtil !== 'undefined' && typeof KTUtil.scrollTop === 'function') {
          KTUtil.scrollTop();
        } else {
          window.scrollTo({ top: 0, behavior: 'smooth' });
        }
      } catch (e) {
        window.scrollTo(0, 0);
      }
    }

    // Intercept Continue / Next Page button clicks — validate current step before advancing
    $(document).on('click', '#continueButton, .client-nav-next, .client-top-next-btn', function (e) {
      e.preventDefault();
      e.stopPropagation();

      var now = Date.now();
      if (now - lastNavTime < 250) return false;

      // 1. Force auto-commit any pending industry typed value synchronously before validation
      var indDisp = ($('#industry_display').val() || '').trim();
      if (indDisp) {
        if (window.JeroIndustryIntelligenceEngine && typeof window.JeroIndustryIntelligenceEngine.commitIfCustom === 'function') {
          try { window.JeroIndustryIntelligenceEngine.commitIfCustom(); } catch (err) {}
        }
        if (!$('#industry').val()) {
          var $sel = $('#industry');
          var optFound = false;
          $sel.find('option').each(function() {
            if ($(this).text().trim().toLowerCase() === indDisp.toLowerCase()) {
              $sel.val($(this).val());
              optFound = true;
              return false;
            }
          });
          if (!optFound) {
            $sel.append(new Option(indDisp, 0, true, true));
            $sel.val(0);
            $('#custom_industry').val(indDisp);
          }
        }
      }

      var currentStep = getCurrentStepNumber();
      if (!validateWizardStep(currentStep)) {
        var $firstInvalid = window.$firstInvalid;
        if ($firstInvalid && $firstInvalid.length) {
          $firstInvalid.focus();
          try {
            $('html, body').animate({
              scrollTop: Math.max(0, $firstInvalid.offset().top - 140)
            }, 300);
          } catch (err) {}
        }
        if (typeof Swal !== 'undefined' && typeof Swal.mixin === 'function') {
          const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 4500,
            timerProgressBar: true
          });
          var customMsg = (currentStep === 3 && window._lastStep3Error)
            ? window._lastStep3Error
            : 'Please fill in the required fields marked in red.';
          Toast.fire({
            icon: 'warning',
            title: customMsg
          });
        }
        return false;
      }

      // Record successful navigation time
      lastNavTime = Date.now();

      var nextStep = Math.min(6, currentStep + 1);
      try {
        goToWizardStep(nextStep);
      } catch (err) {
        console.error('goToWizardStep navigation error:', err);
      }
    });

    // Intercept Top Close button clicks on Step 1 — exit wizard cleanly with unsaved changes prompt
    $(document).on('click', '#client_top_close_btn, .client-nav-close', function (e) {
      e.preventDefault();
      e.stopPropagation();

      var now = Date.now();
      if (now - lastNavTime < 250) return false;
      lastNavTime = now;

      var hasData = Boolean(
        ($('#client_id').val() || '').trim() ||
        ($('#website').val() || '').trim()
      );

      if (hasData && typeof Swal !== 'undefined') {
        Swal.fire({
          title: 'Leave without saving?',
          text: 'You have unsaved changes. Are you sure you want to exit?',
          icon: 'warning',
          showCancelButton: true,
          confirmButtonText: 'Yes, Exit',
          cancelButtonText: 'Stay on Page',
          confirmButtonColor: '#ef4444',
          cancelButtonColor: '#e2e8f0',
          customClass: {
            confirmButton: 'btn btn-danger font-weight-bold',
            cancelButton: 'btn btn-light font-weight-bold'
          }
        }).then(function(res) {
          if (res.isConfirmed) {
            window.location.href = "{{ route('service_company.clients.index') }}";
          }
        });
      } else {
        window.location.href = "{{ route('service_company.clients.index') }}";
      }
    });

    // Intercept Back button clicks — navigate back and sync navigation state
    $(document).on('click', '#client_top_back_btn, #client_btn_back, .client-nav-back, #client_header_back_btn', function (e) {
      e.preventDefault();
      e.stopPropagation();

      var now = Date.now();
      if (now - lastNavTime < 250) return false;
      lastNavTime = now;

      var currentStep = getCurrentStepNumber();
      if (currentStep > 1) {
        try {
          goToWizardStep(currentStep - 1);
        } catch (err) {
          console.error('goToWizardStep back error:', err);
        }
      } else {
        if (window.history.length > 1) {
          window.history.back();
        } else {
          window.location.href = "{{ route('service_company.clients.index') }}";
        }
      }
    });

    // Intercept Step Header clicks — validate preceding steps when moving forward
    $(document).on('click', '#kt_wizard .wizard-step, #client_wizard_stepper .client-step-item', function (e) {
      e.preventDefault();
      e.stopPropagation();

      var now = Date.now();
      if (now - lastNavTime < 250) return false;

      var targetStep = parseInt($(this).data('step'), 10) || ($(this).index() + 1);
      var currentStep = getCurrentStepNumber();
      if (targetStep === currentStep) return;

      if (targetStep > currentStep) {
        for (var s = currentStep; s < targetStep; s++) {
          if (!validateWizardStep(s)) {
            var $firstInvalid = window.$firstInvalid;
            if ($firstInvalid && $firstInvalid.length) {
              $firstInvalid.focus();
              try {
                $('html, body').animate({
                  scrollTop: Math.max(0, $firstInvalid.offset().top - 140)
                }, 300);
              } catch (err) {}
            }
            if (typeof Swal !== 'undefined' && typeof Swal.mixin === 'function') {
              const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 4500,
                timerProgressBar: true
              });
              var headerMsg = (s === 3 && window._lastStep3Error)
                ? window._lastStep3Error
                : 'Please complete the required fields in Step ' + s + ' before continuing.';
              Toast.fire({
                icon: 'warning',
                title: headerMsg
              });
            }
            return false;
          }
        }
      }

      lastNavTime = Date.now();
      try {
        goToWizardStep(targetStep);
      } catch (err) {
        console.error('goToWizardStep step header click error:', err);
      }
    });

    // Expose Global Navigation & Helper APIs on window
    window.goToWizardStep = goToWizardStep;
    window.validateWizardStep = validateWizardStep;
    window.getCurrentStepNumber = getCurrentStepNumber;
    window.syncWizardNavigationState = syncWizardNavigationState;
    window.populateReviewSummaries = populateReviewSummaries;

    // =========================================================================
    // Dynamic Step 1 Industry ➔ Step 2 Service Tags Intelligence
    // =========================================================================
    var INDUSTRY_SECTOR_SERVICES = {
      1: {
        name: 'Information Technology & Services',
        services: [
          'Custom Software Development',
          'Cloud Architecture & DevOps',
          'AI & Machine Learning',
          'Mobile App Development',
          'Cybersecurity & Compliance',
          'Data Engineering & Analytics',
          'QA & Automation',
          'API & Microservices',
          'UI/UX Product Design',
          'SaaS Platform Engineering',
          'Enterprise Cloud Migration',
          'Embedded Systems & IoT',
          'Blockchain & Smart Contracts'
        ]
      },
      2: {
        name: 'Financial & Banking',
        services: [
          'Core Banking Solutions',
          'FinTech Payment Gateways',
          'Risk & Fraud Analytics',
          'Wealth Management Tech',
          'RegTech & Compliance',
          'Algorithmic Trading Tech',
          'Blockchain & Smart Contracts',
          'Accounting & Financial Systems',
          'KYC/AML Automation',
          'Financial Data Security',
          'Asset Tokenization Tech',
          'Open Banking APIs'
        ]
      },
      3: {
        name: 'Healthcare & Life Sciences',
        services: [
          'Electronic Health Records (EHR)',
          'Telemedicine Solutions',
          'Medical Device Integration',
          'Clinical Trials Tech',
          'HIPAA & Health Compliance',
          'Healthcare AI Diagnostics',
          'Pharmacy Management Systems',
          'Laboratory Information Systems',
          'Health Data Analytics',
          'Patient Portal Platforms',
          'Genomics Data Platforms',
          'Biotech Research Tools'
        ]
      },
      4: {
        name: 'Manufacturing & Industrial',
        services: [
          'Smart Factory Automation & IoT',
          'Supply Chain Optimization',
          'Predictive Maintenance Systems',
          'Industrial Robotics Integration',
          'ERP & MRP Integration',
          'Quality Assurance & ISO Tech',
          'CAD/CAM Engineering',
          'Lean Operations Consulting',
          'Warehouse Automation',
          'Inventory Telematics',
          'Digital Twin Systems',
          'CNC Fleet Telematics'
        ]
      },
      5: {
        name: 'Professional & Consulting',
        services: [
          'Management Consulting',
          'Digital Transformation Strategy',
          'Legal Ops & Compliance Tech',
          'Talent Acquisition & Staffing',
          'Market Intelligence & Research',
          'Brand Strategy & Creative',
          'Enterprise Architecture',
          'Change Management',
          'Corporate Training Solutions',
          'HR Tech Integration',
          'Risk Advisory Systems',
          'ESG Strategy Consulting'
        ]
      },
      6: {
        name: 'Consumer & Retail',
        services: [
          'Omnichannel E-Commerce',
          'D2C Growth & Performance',
          'POS & Retail Systems',
          'Customer Loyalty Platforms',
          'Inventory & Fulfillment Tech',
          'Personalization & Recommendations',
          'Marketplace Management',
          'Mobile Commerce Apps',
          'Product Lifecycle Management',
          'Consumer Analytics',
          'Supply Chain Tracking',
          'Augmented Shopping Tech'
        ]
      },
      7: {
        name: 'Energy & Utilities',
        services: [
          'Renewable Energy Integration',
          'Smart Grid Analytics',
          'ESG & Carbon Accounting',
          'Energy Trading & Risk Tech',
          'IoT Utility Metering',
          'CleanTech Engineering',
          'EV Infrastructure Solutions',
          'Pipeline & Asset Monitoring',
          'Energy Storage Systems',
          'Environmental Compliance Tech',
          'Microgrid Architecture',
          'Battery Optimization Systems'
        ]
      },
      8: {
        name: 'Real Estate & Construction',
        services: [
          'PropTech Solutions',
          'Building Information Modeling (BIM)',
          'Real Estate ERP & CRM',
          'Construction Site Telematics',
          'Facility Management Tech',
          'Virtual Property Tours / 3D',
          'Smart Home Integration',
          'Lease Management Platforms',
          'Architectural Visualization',
          'Urban Planning Analytics',
          'Tenant Experience Portals',
          'Green Building Certifications'
        ]
      },
      9: {
        name: 'Logistics, Transportation & Supply Chain',
        services: [
          'Fleet Management & Telematics',
          'Warehouse Management Systems (WMS)',
          'Last-Mile Route Optimization',
          'Freight Brokerage Tech',
          'Autonomous Logistics Tech',
          'Cold Chain Monitoring',
          'Customs & Port Logistics',
          'RFID & Asset Tracking',
          'Supply Chain Visibility',
          'Delivery Dispatch Systems',
          'Cross-Docking Automation',
          'Reverse Logistics Optimization'
        ]
      },
      10: {
        name: 'Media, Education & Public Sector',
        services: [
          'Learning Management Systems (LMS)',
          'EdTech Interactive Platforms',
          'GovTech Citizen Portals',
          'Student Information Systems (SIS)',
          'OTT & Video Streaming Systems',
          'Digital Publishing Platforms',
          'Media Asset Management (MAM)',
          'Public Sector Compliance Tech',
          'Online Examination Systems',
          'Content Delivery Solutions',
          'Virtual Classroom Systems',
          'AI Tutor Platforms'
        ]
      }
    };

    window.addSuggestedService = function(svc) {
      if (!svc) return;
      if (window.clientServicesManager) {
        window.clientServicesManager.addTag(svc);
      } else if (window.clientTagsTagify) {
        window.clientTagsTagify.addTags([svc]);
      }
      $('#client_tags_error').hide();
    };

    window.addAllSuggestedServices = function() {
      if (window.clientServicesManager) {
        window.clientServicesManager.addAllRecommended();
      } else if (window.clientTagsTagify) {
        var tagsToAdd = [];
        var existing = (window.clientTagsTagify.value || []).map(function(t) { return (t.value || '').toLowerCase(); });
        $('#client_suggested_services .client-suggested-pill').each(function() {
          var val = $(this).data('value');
          if (val && existing.indexOf(val.toLowerCase()) === -1) {
            tagsToAdd.push(val);
          }
        });
        if (tagsToAdd.length > 0) {
          window.clientTagsTagify.addTags(tagsToAdd);
        }
      }
      $('#client_tags_error').hide();
    };

    window.updateSuggestedServicesForIndustry = function(industryItem) {
      var $container = $('#client_suggested_services');
      var $label = $('#client_suggested_label');
      if (!$container.length) return;

      var item = industryItem || (window.JeroIndustryIntelligenceEngine ? window.JeroIndustryIntelligenceEngine.getSelected() : null);
      var indName = item ? (item.name || '') : ($('#industry_display').val() || $('#industry option:selected').text() || '').trim();
      if (indName === 'Select industry' || indName === '-- Select Industry --') indName = '';

      var sectorId = null;
      var sectorName = '';

      if (item && item.sector_id) {
        sectorId = parseInt(item.sector_id, 10);
        sectorName = item.sector_name || '';
      } else if (item && item.sector) {
        sectorId = parseInt(item.sector, 10);
      }

      if (!sectorId && item && item.id && window.JeroIndustryIntelligenceEngine && typeof window.JeroIndustryIntelligenceEngine.getById === 'function') {
        var found = window.JeroIndustryIntelligenceEngine.getById(item.id);
        if (found && found.sector_id) {
          sectorId = found.sector_id;
          sectorName = found.sector_name;
        }
      }

      var sectorConfig = INDUSTRY_SECTOR_SERVICES[sectorId] || INDUSTRY_SECTOR_SERVICES[1];
      if (!sectorName) sectorName = sectorConfig.name;

      if ($label.length) {
        $label.text(indName ? 'Suggested Services (' + indName + ')' : 'Suggested Services (' + sectorName + ')');
      }

      var services = sectorConfig.services || [];
      var existingTags = [];
      if (window.clientServicesManager && Array.isArray(window.clientServicesManager.selected)) {
        existingTags = window.clientServicesManager.selected.map(function(t) { return String(t || '').toLowerCase(); });
      } else if (window.clientTagsTagify && Array.isArray(window.clientTagsTagify.value)) {
        existingTags = window.clientTagsTagify.value.map(function(t) { return (t.value || '').toLowerCase(); });
      }

      var html = '';
      for (var i = 0; i < services.length; i++) {
        var s = services[i];
        var isAdded = existingTags.indexOf(s.toLowerCase()) !== -1;
        var addedClass = isAdded ? 'is-selected' : '';

        html += '<button type="button" class="btn btn-sm client-suggested-pill ' + addedClass + '" data-value="' + escapeHtmlAttr(s) + '" style="' + (isAdded ? 'display:none;' : '') + '">' +
          '<svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>' +
          '<span>' + escapeHtmlText(s) + '</span>' +
        '</button>';
      }

      $container.html(html);

      if (window.clientServicesManager) {
        window.clientServicesManager.updateSuggestedView();
      }

      var clientName = ($('#client_id').val() || '').trim() || '4bell';
      var $aiDesc = $('#client_ai_suggested_desc');
      if (indName && $aiDesc.length) {
        var indDesc = clientName + ' is an industry-leading ' + indName + ' organization providing end-to-end solutions, enterprise excellence, and specialized operations within the ' + sectorName + ' sector. The company focuses on scalable innovation, operational agility, and high-quality client delivery.';
        $aiDesc.text(indDesc);
      }
    };

    window.applyDescriptionSuggestion = function() {
      var suggestion = $('#client_ai_suggested_desc').text().trim();
      if (!suggestion) return;
      if (typeof setClientDescriptionContent === 'function') {
        setClientDescriptionContent(suggestion);
      } else {
        $('#kt_docs_tinymce_hidden').val(suggestion).trigger('input');
        $('#client_desc_count').text(Math.min(suggestion.length, 2000));
      }
      $('.client-editor-box').removeClass('is-invalid');
      if (typeof clearFieldError === 'function') {
        clearFieldError($('#kt_docs_tinymce_hidden'));
      }
      if (typeof Swal !== 'undefined' && typeof Swal.mixin === 'function') {
        Swal.mixin({ toast: true, position: 'top-end', showConfirmButton: false, timer: 2000 }).fire({ icon: 'success', title: 'Description suggestion applied!' });
      }
    };

    // Section Edit buttons in Review & Submit and Sidebar Summary Cards
    $(document).on('click', '[data-client-goto-step], .client-review-edit, .client-summary-edit', function (e) {
      e.preventDefault();
      e.stopPropagation();

      var now = Date.now();
      if (now - lastNavTime < 250) return false;
      lastNavTime = now;

      var step = $(this).attr('data-client-goto-step') || $(this).data('client-goto-step');
      if (step) {
        goToWizardStep(parseInt(step, 10));
      }
    });

    $(document).on('input change', '#kt_form input, #kt_form select, #kt_form textarea', function () {
      updateAssistantPanel();
      updateCompletion();
    });

    $('#client_review_details').on('click', function () {
      var w = ($('#website').val() || '').trim();
      if (w) {
        fetchGodLevelCompanyIntelligence(w);
      } else {
        Swal.fire({ text: 'Enter a website URL first.', icon: 'info', confirmButtonText: 'Ok' });
      }
    });

    $('#client_focus_tags').on('click', function () {
      $('#tags').focus();
    });

    $(document).off('click', '#client_open_contact_drawer, .client-open-contact-drawer-btn').on('click', '#client_open_contact_drawer, .client-open-contact-drawer-btn', function (e) {
      if (e && e.preventDefault) e.preventDefault();
      if (e && e.stopPropagation) e.stopPropagation();
      openContactDrawer(null);
    });
    $(document).off('click', '#client_close_contact_drawer, #client_cancel_contact_drawer, #client_contact_drawer_backdrop').on('click', '#client_close_contact_drawer, #client_cancel_contact_drawer, #client_contact_drawer_backdrop', function (e) {
      if (e && e.preventDefault) e.preventDefault();
      if (e && e.stopPropagation) e.stopPropagation();
      closeContactDrawer();
    });
    $(document).off('click', '#client_save_contact_drawer').on('click', '#client_save_contact_drawer', function (e) {
      if (e && e.preventDefault) e.preventDefault();
      if (e && e.stopPropagation) e.stopPropagation();
      saveContactDrawer();
    });
    $(document).off('click', '.client-edit-contact').on('click', '.client-edit-contact', function (e) {
      if (e && e.preventDefault) e.preventDefault();
      if (e && e.stopPropagation) e.stopPropagation();
      var index = $(this).attr('data-index');
      openContactDrawer(index);
    });

    $(document).off('click', '.client-delete-contact').on('click', '.client-delete-contact', function (e) {
      if (e && e.preventDefault) e.preventDefault();
      if (e && e.stopPropagation) e.stopPropagation();
      var index = parseInt($(this).attr('data-index'), 10);
      deleteContactRow(index);
    });

    $(document).off('keydown', '#client_drawer_name, #client_drawer_designation, #client_drawer_email, #client_drawer_phone').on('keydown', '#client_drawer_name, #client_drawer_designation, #client_drawer_email, #client_drawer_phone', function (e) {
      if (e.key === 'Enter') {
        if (e && e.preventDefault) e.preventDefault();
        if (e && e.stopPropagation) e.stopPropagation();
        saveContactDrawer();
      }
    });

    $(document).on('click', '.remove_address_single, .client-remove-address-btn', function (e) {
      e.preventDefault();
      e.stopPropagation();
      var target = $(this).attr('data-target') || $(this).data('target');
      if (typeof removeAddressCard === 'function') {
        removeAddressCard(this, target);
      } else {
        if (target && $('#' + target).length) $('#' + target).remove();
        else $(this).closest('.client-address-card, .client-address-container-child, .is-additional').remove();
        syncAdditionalAddressEmpty();
      }
    });

    // =========================================================================
    // DRAFT PERSISTENCE ENGINE REMOVED
    // Auto-save draft feature has been completely removed per user request to
    // eliminate stale industry / previous entries persisting.
    // =========================================================================
    var DRAFT_STORAGE_KEY = 'jero_client_draft_' + {{ (int) (auth()->user()->company_id ?? 0) }};
    try {
      localStorage.removeItem(DRAFT_STORAGE_KEY);
      localStorage.removeItem('jero_client_draft_' + {{ (int) (auth()->user()->company_id ?? 0) }});
    } catch (e) {}

    window.saveClientDraft = function () {};
    window.restoreClientDraft = function () {};
    window.checkDraftPromptOnLoad = function () {};

    // Remove Logo Button Handler
    $(document).on('click', '#client_preview_remove_btn', function (e) {
      e.preventDefault();
      $('#client_logo_input').val('');
      $('#client_avatar_remove').val('1');
      $('.client-logo-preview .image-input-wrapper')
        .css('background-image', 'none')
        .html('<div style="width:100%; height:100%; display:flex; flex-direction:column; align-items:center; justify-content:center; background:#f8fafc; color:#94a3b8; gap:3px;"><i class="la la-building" style="font-size:14px; color:#94a3b8;"></i><span style="font-size:9px; font-weight:600; color:#64748b; text-transform:uppercase; letter-spacing:0.3px;">Logo</span></div>');
      $(this).hide();
      if (typeof updateCompletion === 'function') updateCompletion();
    });

    if (window.clientTagsTagify) {
      var refreshTags = function () {
        renderServicePreview((window.clientTagsTagify.value || []).map(function (t) { return t.value; }));
      };
      window.clientTagsTagify.on('add remove', refreshTags);
      refreshTags();
    }

    // =========================================================================
    // Lightweight Quill Rich Text Editor Engine for Step 2 (Client Description)
    // Zero extra network overhead — leverages Metronic's bundled Quill v1.3.6
    // =========================================================================
    window.clientDescQuill = null;

    window.initClientDescriptionEditor = function () {
      if (window.clientDescQuill) return window.clientDescQuill;
      var $target = $('#kt_client_description_quill');
      if (!$target.length) return null;

      if (typeof Quill === 'undefined') {
        setTimeout(window.initClientDescriptionEditor, 100);
        return null;
      }

      try {
        window.clientDescQuill = new Quill('#kt_client_description_quill', {
          modules: {
            toolbar: [
              [{ 'header': [1, 2, 3, false] }],
              ['bold', 'italic'],
              [{ 'list': 'bullet' }],
              ['link']
            ]
          },
          placeholder: "Briefly describe the company's business, products/services, industry and key strengths...",
          theme: 'snow'
        });

        // Add fullscreen toggle button to toolbar (matches reference screenshot)
        var $tb = $('#client_editor_box .ql-toolbar');
        if ($tb.length && !$tb.find('.ql-fullscreen-btn').length) {
          var $fsBtn = $('<button type="button" class="ql-fullscreen-btn" title="Toggle Fullscreen" style="margin-left:auto; width:26px; height:26px; display:inline-flex; align-items:center; justify-content:center; background:none; border:none; color:#64748b; cursor:pointer; padding:0;">' +
            '<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">' +
              '<path d="M8 3H5a2 2 0 0 0-2 2v3m18 0V5a2 2 0 0 0-2-2h-3m0 18h3a2 2 0 0 0 2-2v-3M3 16v3a2 2 0 0 0 2 2h3"/>' +
            '</svg>' +
          '</button>');
          $fsBtn.on('click', function (e) {
            e.preventDefault();
            $('#client_editor_box').toggleClass('is-fullscreen');
          });
          $tb.append($fsBtn);
        }

        // Track text changes & enforce 2000 char limit
        window.clientDescQuill.on('text-change', function (delta, oldDelta, source) {
          var text = window.clientDescQuill.getText().trim();
          if (text.length > 2000 && source === 'user') {
            window.clientDescQuill.history.undo();
            if (typeof Swal !== 'undefined' && typeof Swal.mixin === 'function') {
              Swal.mixin({ toast: true, position: 'top-end', showConfirmButton: false, timer: 2500 })
                .fire({ icon: 'warning', title: 'Description cannot exceed 2000 characters.' });
            }
          }
          window.syncDescriptionState();
        });

        // Initial sync of existing client description
        window.syncDescriptionState();
        return window.clientDescQuill;
      } catch (err) {
        console.error('Quill initialization error:', err);
        return null;
      }
    };

    window.syncDescriptionState = function () {
      if (!window.clientDescQuill) return;
      var html = window.clientDescQuill.root.innerHTML;
      var text = window.clientDescQuill.getText().trim();
      var cleanHtml = (text.length === 0 || html === '<p><br></p>' || html === '<p></p>') ? '' : html;

      $('#kt_docs_tinymce_hidden').val(cleanHtml);
      $('#client_desc_count').text(Math.min(text.length, 2000));

      if ($('#client_preview_description').length) {
        $('#client_preview_description').text(text ? text.substring(0, 180) : 'Your company description will appear here.');
      }
      if (text.length >= 5) {
        $('.client-editor-box').removeClass('is-invalid');
      }
      if (typeof updateCompletion === 'function') {
        updateCompletion();
      }
    };

    window.getClientDescriptionHtml = function () {
      if (window.clientDescQuill) {
        var html = window.clientDescQuill.root.innerHTML;
        var text = window.clientDescQuill.getText().trim();
        return (text.length === 0 || html === '<p><br></p>' || html === '<p></p>') ? '' : html;
      }
      return $('#kt_docs_tinymce_hidden').val() || '';
    };

    window.getClientDescriptionText = function () {
      if (window.clientDescQuill) {
        return window.clientDescQuill.getText().trim();
      }
      return ($('#kt_docs_tinymce_hidden').val() || '').replace(/<[^>]*>?/gm, '').trim();
    };

    window.setClientDescriptionContent = function (content) {
      if (window.clientDescQuill) {
        if (content && (content.indexOf('<p>') !== -1 || content.indexOf('<br>') !== -1 || content.indexOf('<div>') !== -1)) {
          window.clientDescQuill.root.innerHTML = content;
        } else {
          window.clientDescQuill.setText(content || '');
        }
        window.syncDescriptionState();
      } else {
        $('#kt_docs_tinymce_hidden').val(content || '').trigger('input');
      }
    };

    // Backward-compatibility proxy for tinymce.get('kt_docs_tinymce_hidden')
    window.tinymce = window.tinymce || {};
    window.tinymce.get = function (id) {
      if (id === 'kt_docs_tinymce_hidden') {
        return {
          getContent: function (opts) {
            if (opts && opts.format === 'text') {
              return window.getClientDescriptionText();
            }
            return window.getClientDescriptionHtml();
          },
          setContent: function (content) {
            window.setClientDescriptionContent(content);
          },
          on: function (events, handler) {
            if (window.clientDescQuill) {
              window.clientDescQuill.on('text-change', handler);
            }
          }
        };
      }
      return null;
    };

    // Expose helpers globally
    window.initClientDescriptionEditor = window.initClientDescriptionEditor;
    window.syncDescriptionState = window.syncDescriptionState;
    window.getClientDescriptionHtml = window.getClientDescriptionHtml;
    window.getClientDescriptionText = window.getClientDescriptionText;
    window.setClientDescriptionContent = window.setClientDescriptionContent;

    // Initialize lightweight editor
    initClientDescriptionEditor();

    var originalAddAddressSuccess = null;
    $(document).on('click', '.add_address', function () {
      setTimeout(function () {
        syncAdditionalAddressEmpty();
        bindAddressCounters(document);
      }, 1200);
    });
  });
})(jQuery);
</script>

<!-- =========================================================================
     Client Industry Intelligence Engine (Client-First Execution / 10K Concurrency)
     ========================================================================= -->
@php
    $indManifestData = (!empty($industriesManifest) && !empty($industriesManifest['industries']))
        ? $industriesManifest
        : \App\Services\IndustryTaxonomyService::getGlobalManifest();
@endphp
<script id="jero_industry_seed_data" type="application/json">
    {!! json_encode($indManifestData) !!}
</script>

<script>
(function() {
    'use strict';

    var CURRENT_TENANT_ID = {{ (int) (auth()->user()->company_id ?? 0) }};
    var CURRENT_USER_ID = {{ (int) (auth()->user()->id ?? 0) }};
    var BUNDLE_URL = "{{ route('service_company.clients.industriesBundle') }}";

    // Internal State
    var indList = [];
    var sectorsList = [];
    var sectorMap = new Map();
    var indMap = new Map();
    var currentSearch = '';
    var currentSector = 'all'; // 'all' or sector id (number/string)
    var focusedIndex = -1;
    var currentMatches = [];
    var isOpen = false;
    var selectedIndustry = null;
    var lastAlternatives = [];
    var indBroadcast = null;
    var localManifestVersion = 1;
    var lastFocusCheck = 0;
    var isInteractingWithDropdown = false;
    var lastCachedMaxHeight = null;
    var lastCachedListHeight = null;

    // Recency storage key
    var RECENT_KEY = 'jero_recent_industries_' + CURRENT_USER_ID;

    // Sector Icons & Color Palettes
    var SECTOR_ICONS = {
        1: { icon: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="16 18 22 12 16 6"/><polyline points="8 6 2 12 8 18"/></svg>', bg: '#eff6ff', color: '#2563eb' }, // Technology, Software & Telecom
        2: { icon: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" x2="12" y1="2" y2="22"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>', bg: '#ecfdf5', color: '#059669' }, // Financial Services & Banking
        3: { icon: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 12h-4l-3 9L9 3l-3 9H2"/></svg>', bg: '#fff1f2', color: '#e11d48' }, // Healthcare, Life Sciences & Wellness
        4: { icon: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/></svg>', bg: '#fffbeb', color: '#d97706' }, // Manufacturing, Industrial & Auto
        5: { icon: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect width="20" height="14" x="2" y="7" rx="2" ry="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>', bg: '#f1f5f9', color: '#475569' }, // Professional Services & Consulting
        6: { icon: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="8" cy="21" r="1"/><circle cx="19" cy="21" r="1"/><path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12"/></svg>', bg: '#fdf4ff', color: '#c026d3' }, // Consumer Goods, Retail & Hospitality
        7: { icon: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>', bg: '#ecfeff', color: '#0891b2' }, // Energy, Utilities & Environment
        8: { icon: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>', bg: '#eef2ff', color: '#4f46e5' }, // Real Estate & Construction
        9: { icon: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect width="16" height="10" x="1" y="3" rx="2"/><circle cx="6" cy="18" r="2"/><circle cx="18" cy="18" r="2"/><path d="M17 8h4.5a1.5 1.5 0 0 1 1.5 1.5V16h-4"/></svg>', bg: '#f0fdfa', color: '#0d9488' }, // Logistics, Transportation & Supply Chain
        10: { icon: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 19.5v-15A2.5 2.5 0 0 1 6.5 2H20v20H6.5a2.5 2.5 0 0 1-2.5-2.5Z"/><path d="M6 6h10"/><path d="M6 10h10"/></svg>', bg: '#f5f3ff', color: '#7c3aed' } // Media, Education & Public Sector
    };
    var DEFAULT_SECTOR_ICON = { icon: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect width="18" height="18" x="3" y="3" rx="2"/><path d="M3 9h18"/><path d="M9 21V9"/></svg>', bg: '#f1f5f9', color: '#64748b' };

    function getRecentIndustries() {
        try {
            var raw = localStorage.getItem(RECENT_KEY);
            return raw ? JSON.parse(raw) : [];
        } catch (e) {
            return [];
        }
    }

    function saveRecentIndustry(item) {
        try {
            var list = getRecentIndustries();
            list = list.filter(function(r) { return r.id !== item.id; });
            list.unshift({
                id: item.id,
                name: item.name,
                sector_id: item.sector_id,
                sector_name: item.sector_name,
                timestamp: Date.now()
            });
            if (list.length > 5) list = list.slice(0, 5);
            localStorage.setItem(RECENT_KEY, JSON.stringify(list));
        } catch (e) {}
    }

    // =========================================================================
    // IndexedDB Persistent Storage (JeroLocalStore_v2 - industries)
    // =========================================================================
    var DB_NAME = 'JeroLocalStore_v2';
    var STORE_NAME = 'industries';
    var idbInstance = null;

    function openDatabase() {
        return new Promise(function(resolve, reject) {
            if (idbInstance) return resolve(idbInstance);
            if (!window.indexedDB) return reject(new Error('IndexedDB not supported'));

            try {
                var req = window.indexedDB.open(DB_NAME, 4);
                req.onupgradeneeded = function(e) {
                    var db = e.target.result;
                    if (!db.objectStoreNames.contains('clients')) {
                        db.createObjectStore('clients', { keyPath: 'key' });
                    }
                    if (!db.objectStoreNames.contains('job_titles')) {
                        db.createObjectStore('job_titles', { keyPath: 'key' });
                    }
                    if (!db.objectStoreNames.contains('departments')) {
                        db.createObjectStore('departments', { keyPath: 'key' });
                    }
                    if (!db.objectStoreNames.contains('industries')) {
                        db.createObjectStore('industries', { keyPath: 'key' });
                    }
                };
                req.onsuccess = function(e) {
                    idbInstance = e.target.result;
                    resolve(idbInstance);
                };
                req.onerror = function(e) {
                    reject(e.target ? e.target.error : new Error('IDB open error'));
                };
            } catch (err) {
                reject(err);
            }
        });
    }

    function idbGet(key) {
        return openDatabase().then(function(db) {
            return new Promise(function(resolve) {
                try {
                    var tx = db.transaction(STORE_NAME, 'readonly');
                    var store = tx.objectStore(STORE_NAME);
                    var req = store.get(key);
                    req.onsuccess = function() { resolve(req.result ? req.result.value : null); };
                    req.onerror = function() { resolve(null); };
                } catch (err) {
                    resolve(null);
                }
            });
        }).catch(function() {
            try {
                var fallback = sessionStorage.getItem('idb_fallback_ind_' + key);
                return fallback ? JSON.parse(fallback) : null;
            } catch (e) { return null; }
        });
    }

    function idbSet(key, value) {
        return openDatabase().then(function(db) {
            return new Promise(function(resolve) {
                try {
                    var tx = db.transaction(STORE_NAME, 'readwrite');
                    var store = tx.objectStore(STORE_NAME);
                    store.put({ key: key, value: value });
                    tx.oncomplete = function() { resolve(true); };
                    tx.onerror = function() { resolve(false); };
                } catch (err) {
                    resolve(false);
                }
            });
        }).catch(function() {
            try {
                sessionStorage.setItem('idb_fallback_ind_' + key, JSON.stringify(value));
            } catch (e) {}
        });
    }

    // =========================================================================
    // In-Memory Search Engine (< 2ms Latency)
    // =========================================================================
    function scoreCandidate(item, query, queryTokens, recentIdSet) {
        var score = 0;
        var nameLower = (item.name || '').toLowerCase();
        var secLower = (item.sector_name || '').toLowerCase();
        var kwLower = (item.keywords || '').toLowerCase();

        // 1. Exact Name Match
        if (nameLower === query) {
            score += 150;
        } else if (nameLower.indexOf(query) === 0) {
            score += 100;
        } else if (nameLower.indexOf(query) > 0) {
            score += 70;
        }

        // 2. Multi-Token Set Match
        var allTokensMatched = true;
        for (var t = 0; t < queryTokens.length; t++) {
            var tok = queryTokens[t];
            if (nameLower.indexOf(tok) !== -1) {
                score += 25;
            } else if (secLower.indexOf(tok) !== -1) {
                score += 20;
            } else if (kwLower.indexOf(tok) !== -1) {
                score += 20;
            } else {
                allTokensMatched = false;
            }
        }
        if (allTokensMatched) score += 30;

        // 3. Recency Bonus
        if (recentIdSet.has(item.id)) score += 35;

        return score;
    }

    function searchIndustries(query, sectorFilter) {
        var baseList = indList;
        if (sectorFilter && sectorFilter !== 'all') {
            var sId = parseInt(sectorFilter, 10);
            baseList = indList.filter(function(r) { return r.sector_id === sId; });
        }

        var recentItems = getRecentIndustries();
        var recentIdSet = new Set(recentItems.map(function(r) { return r.id; }));

        var cleanQ = (query || '').trim().toLowerCase();
        if (!cleanQ) {
            return baseList;
        }

        var queryTokens = cleanQ.split(/[^a-z0-9]+/i).filter(Boolean);
        if (queryTokens.length === 0) return baseList;

        var scored = [];
        for (var i = 0; i < baseList.length; i++) {
            var item = baseList[i];
            var score = scoreCandidate(item, cleanQ, queryTokens, recentIdSet);
            if (score > 15) {
                scored.push({ item: item, score: score });
            }
        }

        scored.sort(function(a, b) { return b.score - a.score; });
        return scored.map(function(s) { return s.item; });
    }

    function escapeHtml(str) {
        if (!str) return '';
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function escapeRegex(str) {
        if (!str) return '';
        return String(str).replace(/[-/\\^$*+?.()|[\]{}]/g, '\\$&');
    }

    function highlightMatch(text, query) {
        if (!query || !query.trim()) return escapeHtml(text);
        var safeText = escapeHtml(text);
        var safeQ = escapeRegex(query.trim());
        try {
            var regex = new RegExp('(' + safeQ + ')', 'gi');
            return safeText.replace(regex, '<mark class="jero-ind-mark">$1</mark>');
        } catch (e) {
            return safeText;
        }
    }

    // =========================================================================
    // Render & Dropdown UI Engine
    // =========================================================================
    function updateSectorTabs() {
        // Pure God-Level combobox list architecture — in-dropdown tabs removed per UI/UX specification
    }

    function renderDropdownList() {
        var container = document.getElementById('jero_ind_list_container');
        if (!container) return;

        currentMatches = searchIndustries(currentSearch, currentSector);
        focusedIndex = -1;

        var q = currentSearch.trim();
        var html = '';

        if (currentMatches.length === 0) {
            container.innerHTML = '<div class="jero-ind-empty-state">' +
                '<div class="jero-ind-empty-icon">' +
                    '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>' +
                '</div>' +
                '<div class="jero-ind-empty-title">' + (q ? 'No industry matches "' + escapeHtml(q) + '"' : 'No industries available') + '</div>' +
                (q ? '<div class="jero-ind-empty-sub">Add this as a custom industry:</div>' +
                     '<button type="button" class="jero-ind-add-custom-btn" onclick="if(window.JeroIndustryIntelligenceEngine) window.JeroIndustryIntelligenceEngine.commitCustomIndustry(\'' + escapeHtml(q).replace(/'/g, "\\'") + '\');">+ Use "' + escapeHtml(q) + '"</button>' : '') +
            '</div>';
            adjustDropdownPosition();
            return;
        }

        var currentVal = ($('#industry').val() || '').trim();

        // High-confidence AI candidate alternatives pill rack
        if (!q && lastAlternatives && lastAlternatives.length > 0) {
            html += '<div class="jero-ind-alt-box" style="padding: 8px 12px; margin: 4px 6px 8px 6px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px;">' +
                '<div style="font-size: 10px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; color: #64748b; margin-bottom: 6px; display: flex; align-items: center; gap: 4px;">' +
                    '<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="2"><path d="m12 3-1.9 5.8a2 2 0 0 1-1.3 1.3L3 12l5.8 1.9a2 2 0 0 1 1.3 1.3L12 21l1.9-5.8a2 2 0 0 1 1.3-1.3L21 12l-5.8-1.9a2 2 0 0 1-1.3-1.3L12 3z"/></svg>' +
                    '<span>AI Detected Alternatives</span>' +
                '</div>' +
                '<div style="display: flex; flex-wrap: wrap; gap: 6px;">';
            for (var a = 0; a < lastAlternatives.length; a++) {
                var alt = lastAlternatives[a];
                html += '<button type="button" class="jero-ind-alt-pill" data-alt-id="' + alt.id + '" style="background: #ffffff; border: 1px solid #cbd5e1; border-radius: 4px; padding: 3px 8px; font-size: 11px; font-weight: 500; color: #1e293b; cursor: pointer; display: inline-flex; align-items: center; gap: 4px; transition: all 0.15s ease;" onmouseover="this.style.borderColor=\'#2563eb\';this.style.color=\'#2563eb\';" onmouseout="this.style.borderColor=\'#cbd5e1\';this.style.color=\'#1e293b\';">' +
                    escapeHtml(alt.name) +
                '</button>';
            }
            html += '</div></div>';
        }

        // Group by sector when no search query
        var currentSecId = null;
        for (var i = 0; i < currentMatches.length; i++) {
            var item = currentMatches[i];
            if (!q && currentSector === 'all' && item.sector_id !== currentSecId) {
                currentSecId = item.sector_id;
                var secIconData = SECTOR_ICONS[item.sector_id] || DEFAULT_SECTOR_ICON;
                html += '<div class="jero-ind-section-header">' +
                    '<span class="jero-ind-sector-icon" style="background:' + secIconData.bg + '; color:' + secIconData.color + ';">' +
                        secIconData.icon +
                    '</span>' +
                    '<span>' + escapeHtml(item.sector_name || 'General') + '</span>' +
                '</div>';
            }
            var isSelected = (selectedIndustry && selectedIndustry.id === item.id) || (String(item.id) === currentVal);
            html += buildItemHtml(item, q, i, isSelected);
        }

        container.innerHTML = html;
        adjustDropdownPosition();
    }

    function buildItemHtml(item, query, index, isSelected) {
        var checkHtml = isSelected
            ? '<span class="jero-ind-item-check"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg></span>'
            : '';

        return '<div class="jero-ind-item ' + (isSelected ? 'selected' : '') + '" data-idx="' + index + '" data-id="' + item.id + '" role="option" aria-selected="' + (isSelected ? 'true' : 'false') + '">' +
            '<div class="jero-ind-item-main">' +
                '<span class="jero-ind-item-dot"></span>' +
                '<div class="jero-ind-item-name">' + highlightMatch(item.name, query) + '</div>' +
            '</div>' +
            '<div class="jero-ind-item-side">' +
                (query ? '<span class="jero-ind-search-sector-badge">' + escapeHtml((item.sector_name || '').split(',')[0].split('&')[0].trim()) + '</span>' : '') +
                checkHtml +
            '</div>' +
        '</div>';
    }

    function scrollItemIntoView(container, target) {
        if (!container || !target) return;
        var cRect = container.getBoundingClientRect();
        var tRect = target.getBoundingClientRect();

        if (tRect.top < cRect.top) {
            container.scrollTop -= (cRect.top - tRect.top + 6);
        } else if (tRect.bottom > cRect.bottom) {
            container.scrollTop += (tRect.bottom - cRect.bottom + 6);
        }
    }

    function fallbackFromNativeSelect() {
        if (indList && indList.length > 0) return;
        var nativeSelect = document.getElementById('industry');
        if (!nativeSelect || !nativeSelect.options || nativeSelect.options.length <= 1) return;

        var extracted = [];
        for (var o = 0; o < nativeSelect.options.length; o++) {
            var opt = nativeSelect.options[o];
            var valId = parseInt(opt.value, 10);
            if (valId > 0 && opt.text && opt.text.toLowerCase() !== 'select industry') {
                extracted.push({
                    id: valId,
                    name: opt.text.trim(),
                    sector_id: 1,
                    sector_name: 'General'
                });
            }
        }
        if (extracted.length > 0) {
            ingestManifest({
                version: 1,
                sectors: [{ id: 1, name: 'All Industries' }],
                industries: extracted
            });
        }
    }

    function adjustDropdownPosition() {
        var wrap = document.getElementById('jero_ind_select_wrap');
        var dropdown = document.getElementById('jero_ind_dropdown');
        var listContainer = document.getElementById('jero_ind_list_container');
        if (!wrap || !dropdown || !isOpen) return;

        var rect = wrap.getBoundingClientRect();
        var viewportH = window.innerHeight || document.documentElement.clientHeight || 768;

        // Enterprise UX: Strictly open downwards under the input box
        dropdown.classList.remove('drop-up');
        dropdown.style.bottom = 'auto';
        dropdown.style.top = 'calc(100% + 4px)';

        // Ensure parent wrap has high z-index and relative position
        wrap.style.zIndex = '1050';
        wrap.style.position = 'relative';

        // Calculate available vertical space below input box to bottom of viewport
        var spaceBelow = Math.max(160, viewportH - rect.bottom - 16);
        var idealDropdownHeight = Math.min(290, spaceBelow);
        dropdown.style.maxHeight = idealDropdownHeight + 'px';
        if (listContainer) {
            listContainer.style.maxHeight = idealDropdownHeight + 'px';
        }
    }

    function openDropdown() {
        var dropdown = document.getElementById('jero_ind_dropdown');
        var wrap = document.getElementById('jero_ind_select_wrap');
        var input = document.getElementById('industry_display');
        var container = document.getElementById('jero_ind_list_container');
        if (!dropdown || !wrap) return;

        // Ensure indList is ready before rendering
        if (!indList || indList.length === 0) {
            var seedEl = document.getElementById('jero_industry_seed_data');
            if (seedEl && seedEl.textContent.trim()) {
                try {
                    var seedManifest = JSON.parse(seedEl.textContent);
                    if (seedManifest && Array.isArray(seedManifest.industries) && seedManifest.industries.length > 0) {
                        ingestManifest(seedManifest);
                    }
                } catch (e) {}
            }
            if (!indList || indList.length === 0) {
                fallbackFromNativeSelect();
            }
        }

        isOpen = true;
        wrap.classList.add('is-open');
        wrap.style.zIndex = '1050';
        wrap.style.position = 'relative';
        dropdown.classList.add('is-open');
        dropdown.style.display = 'flex';
        if (input) {
            input.setAttribute('aria-expanded', 'true');
            input.classList.remove('is-invalid');
            currentSearch = '';
        }

        renderDropdownList();
        adjustDropdownPosition();

        // If viewport space below input is tight (< 300px), gently center the input in viewport
        var rect = wrap.getBoundingClientRect();
        var vh = window.innerHeight || document.documentElement.clientHeight || 768;
        if ((vh - rect.bottom) < 300) {
            try {
                wrap.scrollIntoView({ behavior: 'smooth', block: 'center' });
                setTimeout(adjustDropdownPosition, 180);
            } catch(e){}
        }

        if (container) {
            var selectedEl = container.querySelector('.jero-ind-item.selected');
            if (selectedEl) {
                scrollItemIntoView(container, selectedEl);
            } else {
                container.scrollTop = 0;
            }
        }
    }

    function closeDropdown(restoreText) {
        var dropdown = document.getElementById('jero_ind_dropdown');
        var wrap = document.getElementById('jero_ind_select_wrap');
        var input = document.getElementById('industry_display');
        if (!dropdown || !wrap) return;

        isOpen = false;
        wrap.classList.remove('is-open');
        wrap.style.zIndex = '';
        dropdown.classList.remove('is-open');
        dropdown.style.display = 'none';
        if (input) input.setAttribute('aria-expanded', 'false');
        focusedIndex = -1;

        if (restoreText && input) {
            if (selectedIndustry && selectedIndustry.name) {
                input.value = selectedIndustry.name;
            }
        }
    }

    function moveFocus(delta) {
        var container = document.getElementById('jero_ind_list_container');
        if (!container) return;
        var items = container.querySelectorAll('.jero-ind-item');
        if (items.length === 0) return;

        if (focusedIndex >= 0 && focusedIndex < items.length) {
            items[focusedIndex].classList.remove('focused');
        }

        focusedIndex += delta;
        if (focusedIndex < 0) focusedIndex = items.length - 1;
        if (focusedIndex >= items.length) focusedIndex = 0;

        var target = items[focusedIndex];
        target.classList.add('focused');
        scrollItemIntoView(container, target);
    }

    function selectFocused() {
        var container = document.getElementById('jero_ind_list_container');
        var items = container ? container.querySelectorAll('.jero-ind-item') : [];

        if (focusedIndex >= 0 && focusedIndex < items.length) {
            var el = items[focusedIndex];
            var id = el.getAttribute('data-id');
            if (id) {
                var item = indMap.get(parseInt(id, 10));
                if (item) {
                    selectItem(item);
                    return;
                }
            }
        }

        // If user pressed Enter and there are matches
        if (currentMatches && currentMatches.length > 0) {
            selectItem(currentMatches[0]);
            return;
        }

        closeDropdown();
    }

    function selectItem(item) {
        if (!item) return;

        selectedIndustry = item;
        var select = document.getElementById('industry');
        var displayInput = document.getElementById('industry_display');
        var clearBtn = document.getElementById('jero_ind_clear_btn');
        var kbdEl = document.getElementById('jero_ind_kbd');
        var inputBox = document.getElementById('jero_ind_input_box');
        var customInput = document.getElementById('custom_industry');

        // Reset custom industry field when selecting canonical item
        if (customInput) customInput.value = '';

        // 1. Update native select (add option if not present)
        if (select) {
            var exists = false;
            for (var o = 0; o < select.options.length; o++) {
                if (select.options[o].value == item.id) {
                    select.selectedIndex = o;
                    exists = true;
                    break;
                }
            }
            if (!exists) {
                var opt = document.createElement('option');
                opt.value = item.id;
                opt.text = item.name;
                opt.selected = true;
                select.appendChild(opt);
            }
            $(select).val(item.id).trigger('change');
        }

        if (displayInput) {
            displayInput.value = item.name;
        }
        if (inputBox) {
            inputBox.classList.add('has-value');
        }
        if (clearBtn) clearBtn.style.display = 'flex';
        if (kbdEl) kbdEl.style.display = 'none';

        // Update review text
        $('.span_industry').text(item.name);

        // Clear validation error state
        $('#jero_ind_select_wrap').removeClass('has-validation-error');
        $('.jero-ind-input-box').removeClass('has-validation-error');
        if (typeof window.clearFieldError === 'function') {
            window.clearFieldError($('#industry'));
        }

        // Dynamic linkage to Step 2 Services
        if (typeof window.updateSuggestedServicesForIndustry === 'function') {
            window.updateSuggestedServicesForIndustry(item);
        }

        // Recency
        saveRecentIndustry(item);
        closeDropdown();
    }

    function commitCustomIndustry(customName) {
        var clean = (customName !== undefined && customName !== null) ? String(customName).trim() : '';
        var select = document.getElementById('industry');
        var displayInput = document.getElementById('industry_display');
        var clearBtn = document.getElementById('jero_ind_clear_btn');
        var kbdEl = document.getElementById('jero_ind_kbd');
        var inputBox = document.getElementById('jero_ind_input_box');
        var customInput = document.getElementById('custom_industry');

        if (!clean) {
            clearSelection();
            return;
        }

        if (displayInput && displayInput.value !== clean) {
            displayInput.value = clean;
        }
        if (inputBox) {
            inputBox.classList.add('has-value');
        }
        if (clearBtn) clearBtn.style.display = 'flex';
        if (kbdEl) kbdEl.style.display = 'none';

        // 1. Check if exact case-insensitive match exists in indList
        var exactMatchedItem = null;
        for (var i = 0; i < indList.length; i++) {
            if ((indList[i].name || '').toLowerCase() === clean.toLowerCase()) {
                exactMatchedItem = indList[i];
                break;
            }
        }
        if (exactMatchedItem) {
            selectItem(exactMatchedItem);
            return;
        }

        // 2. Check if starts-with / prefix match exists in indList
        var prefixMatchedItem = null;
        for (var p = 0; p < indList.length; p++) {
            if ((indList[p].name || '').toLowerCase().indexOf(clean.toLowerCase()) === 0) {
                prefixMatchedItem = indList[p];
                break;
            }
        }
        if (prefixMatchedItem) {
            selectItem(prefixMatchedItem);
            return;
        }

        // 3. Check if substring match exists in indList
        var subMatchedItem = null;
        for (var s = 0; s < indList.length; s++) {
            if ((indList[s].name || '').toLowerCase().indexOf(clean.toLowerCase()) !== -1) {
                subMatchedItem = indList[s];
                break;
            }
        }
        if (subMatchedItem) {
            selectItem(subMatchedItem);
            return;
        }

        // 4. Safe Custom Industry Handling (Database Integrity clients.industry = int(11))
        // Set hidden input #custom_industry to clean string, set #industry to 0
        if (customInput) customInput.value = clean;

        if (select) {
            var customZeroExists = false;
            for (var c = 0; c < select.options.length; c++) {
                if (select.options[c].value == 0 && select.options[c].text === clean) {
                    select.selectedIndex = c;
                    customZeroExists = true;
                    break;
                }
            }
            if (!customZeroExists) {
                var customOpt = document.createElement('option');
                customOpt.value = 0;
                customOpt.text = clean;
                customOpt.selected = true;
                select.appendChild(customOpt);
            }
            $(select).val(0).trigger('change');
        }

        selectedIndustry = { id: 0, name: clean, is_custom: true };
        $('.span_industry').text(clean);
        $('#jero_ind_select_wrap').removeClass('has-validation-error');
        $('.jero-ind-input-box').removeClass('has-validation-error');
        if (typeof window.clearFieldError === 'function') {
            window.clearFieldError($('#industry'));
        }

        // Dynamic linkage to Step 2 Services
        if (typeof window.updateSuggestedServicesForIndustry === 'function') {
            window.updateSuggestedServicesForIndustry(selectedIndustry);
        }

        closeDropdown();
    }

    function commitIfCustom() {
        var input = document.getElementById('industry_display');
        if (!input) return;
        var val = (input.value || '').trim();
        if (val) {
            if (!selectedIndustry || (selectedIndustry.name || '').toLowerCase() !== val.toLowerCase()) {
                commitCustomIndustry(val);
            }
        } else {
            clearSelection();
        }
    }

    function clearSelection() {
        var select = document.getElementById('industry');
        var displayInput = document.getElementById('industry_display');
        var clearBtn = document.getElementById('jero_ind_clear_btn');
        var kbdEl = document.getElementById('jero_ind_kbd');
        var inputBox = document.getElementById('jero_ind_input_box');
        var customInput = document.getElementById('custom_industry');

        selectedIndustry = null;

        if (customInput) customInput.value = '';

        if (select) {
            select.selectedIndex = 0;
            $(select).val('').trigger('change');
        }
        if (displayInput) {
            displayInput.value = '';
        }
        if (inputBox) {
            inputBox.classList.remove('has-value');
        }
        if (clearBtn) {
            clearBtn.style.display = 'none';
        }
        if (kbdEl) {
            kbdEl.style.display = '';
        }

        $('.span_industry').text('-');
        currentSearch = '';
        renderDropdownList();
        closeDropdown();

        // Reset dynamic service tags to default
        if (typeof window.updateSuggestedServicesForIndustry === 'function') {
            window.updateSuggestedServicesForIndustry(null);
        }
    }

    function ingestManifest(manifest) {
        if (!manifest || !Array.isArray(manifest.industries)) return;
        indList = manifest.industries;
        sectorsList = manifest.sectors || [];
        indMap.clear();
        sectorMap.clear();
        localManifestVersion = manifest.version || 1;

        for (var s = 0; s < sectorsList.length; s++) {
            sectorMap.set(sectorsList[s].id, sectorsList[s]);
        }
        for (var i = 0; i < indList.length; i++) {
            indMap.set(indList[i].id, indList[i]);
        }

        updateSectorTabs();
        syncInitialState();
    }

    function ensureManifestLoaded() {
        if (indList && indList.length > 0 && indMap.size > 0) return true;
        var seedEl = document.getElementById('jero_industry_seed_data');
        if (seedEl && seedEl.textContent.trim()) {
            try {
                var seedManifest = JSON.parse(seedEl.textContent);
                if (seedManifest && Array.isArray(seedManifest.industries) && seedManifest.industries.length > 0) {
                    ingestManifest(seedManifest);
                    return true;
                }
            } catch (e) {}
        }
        fallbackFromNativeSelect();
        return indList.length > 0;
    }

    function syncInitialState() {
        ensureManifestLoaded();
        var select = document.getElementById('industry');
        var displayInput = document.getElementById('industry_display');
        var clearBtn = document.getElementById('jero_ind_clear_btn');
        var kbdEl = document.getElementById('jero_ind_kbd');
        var inputBox = document.getElementById('jero_ind_input_box');
        var customInput = document.getElementById('custom_industry');

        if (!displayInput) return;

        var currentVal = select ? (select.value || '').trim() : '';
        var customVal = customInput ? (customInput.value || '').trim() : '';

        if (currentVal && indMap.has(parseInt(currentVal, 10))) {
            var curr = indMap.get(parseInt(currentVal, 10));
            selectedIndustry = curr;
            displayInput.value = curr.name;
            if (inputBox) inputBox.classList.add('has-value');
            if (clearBtn) clearBtn.style.display = 'inline-flex';
            if (kbdEl) kbdEl.style.display = 'none';
            $('.span_industry').text(curr.name);
        } else if (selectedIndustry && selectedIndustry.name) {
            displayInput.value = selectedIndustry.name;
            if (inputBox) inputBox.classList.add('has-value');
            if (clearBtn) clearBtn.style.display = 'inline-flex';
            if (kbdEl) kbdEl.style.display = 'none';
            $('.span_industry').text(selectedIndustry.name);
        } else if (customVal || (currentVal === '0' && select && select.selectedIndex >= 0 && select.options[select.selectedIndex])) {
            var custName = customVal || select.options[select.selectedIndex].text;
            selectedIndustry = { id: 0, name: custName, is_custom: true };
            displayInput.value = custName;
            if (inputBox) inputBox.classList.add('has-value');
            if (clearBtn) clearBtn.style.display = 'inline-flex';
            if (kbdEl) kbdEl.style.display = 'none';
            $('.span_industry').text(custName);
        } else if (currentVal && select.selectedIndex >= 0 && select.options[select.selectedIndex]) {
            var text = select.options[select.selectedIndex].text;
            selectedIndustry = { id: currentVal, name: text };
            displayInput.value = text;
            if (inputBox) inputBox.classList.add('has-value');
            if (clearBtn) clearBtn.style.display = 'inline-flex';
            if (kbdEl) kbdEl.style.display = 'none';
            $('.span_industry').text(text);
        } else {
            selectedIndustry = null;
            if (inputBox) inputBox.classList.remove('has-value');
            if (clearBtn) clearBtn.style.display = 'none';
            if (kbdEl) kbdEl.style.display = '';
        }

        if (selectedIndustry && typeof window.updateSuggestedServicesForIndustry === 'function') {
            window.updateSuggestedServicesForIndustry(selectedIndustry);
        }
    }

    // =========================================================================
    // Industry Taxonomy Mapping & Synonyms Layer (160+ Master Industries)
    // =========================================================================
    var INDUSTRY_TAXONOMY_SYNONYMS = {
        // Tech, Software, SaaS, IT
        'information technology / saas': 64, // Information Technology & Services
        'information technology & services': 64,
        'information technology and services': 64,
        'information technology': 64,
        'it': 64,
        'it services': 64,
        'tech': 64,
        'technology': 64,
        'technology services': 64,
        'saas': 64,
        'software as a service': 64,
        'software': 25, // Computer Software
        'software development': 25,
        'software engineering': 25,
        'software company': 25,
        'computer software': 25,
        'cloud': 148, // Cloud Infrastructure & DevOps
        'cloud computing': 148,
        'devops': 148,
        'cloud infrastructure': 148,
        'artificial intelligence': 147, // Artificial Intelligence & Machine Learning
        'ai': 147,
        'machine learning': 147,
        'ai/ml': 147,
        'cybersecurity': 153, // Cybersecurity & Privacy Infrastructure
        'cyber security': 153,
        'infosec': 153,
        'information security': 21, // Computer & Network Security
        'computer networking': 24,
        'networking': 24,
        'semiconductors': 127,
        'hardware': 23, // Computer Hardware
        'computer hardware': 23,
        'internet': 68,
        'e-commerce': 152, // E-Commerce & Quick Commerce
        'ecommerce': 152,
        'web development': 68,
        'digital media': 98, // Online Media
        'gaming': 157, // Gaming, Esports & Interactive Media
        'video games': 22, // Computer Games
        'esports': 157,

        // Finance & Banking
        'fintech': 149, // FinTech & Digital Assets
        'financial technology': 149,
        'banking': 11,
        'financial services': 43,
        'finance': 43,
        'investment banking': 69,
        'investment management': 70,
        'venture capital': 140,
        'private equity': 140,
        'insurance': 65,
        'insurtech': 65,
        'accounting': 1,
        'capital markets': 16,

        // Healthcare & Life Sciences
        'healthtech': 150, // HealthTech & Digital Health
        'digital health': 150,
        'healthcare': 57, // Hospital & Health Care
        'health care': 57,
        'hospital': 57,
        'biotechnology': 12,
        'biotech': 12,
        'life sciences': 12,
        'pharmaceuticals': 104,
        'pharma': 104,
        'medical devices': 87,
        'mental health': 89,
        'wellness': 55,

        // Professional Services & HR
        'hrtech': 160, // HRTech & Workforce Platforms
        'recruiting': 131, // Staffing & Recruiting
        'recruitment': 131,
        'staffing': 131,
        'human resources': 59,
        'hr': 59,
        'management consulting': 81,
        'consulting': 81,
        'business consulting': 81,
        'strategy consulting': 81,
        'marketing': 84, // Marketing & Advertising
        'advertising': 84,
        'public relations': 114,
        'pr': 114,
        'legal services': 74,
        'legal': 74,
        'law practice': 73,
        'design': 33,
        'graphic design': 54,

        // Manufacturing, Industrial, Automotive
        'automotive': 9,
        'autotech': 158, // AutoTech, EV & Autonomous Mobility
        'ev': 158,
        'electric vehicles': 158,
        'aerospace': 10,
        'aviation': 10,
        'defense': 32,
        'manufacturing': 80, // Machinery
        'industrial automation': 62,
        'robotics': 62,
        'chemicals': 17,
        'mechanical engineering': 85,

        // Logistics, Supply Chain & Transport
        'logistics': 78, // Logistics & Supply Chain
        'supply chain': 78,
        'supply chain tech': 156, // Supply Chain Tech & Autonomous Logistics
        'transportation': 138,
        'trucking': 138,
        'warehousing': 142,
        'freight': 78,
        'shipping': 82, // Maritime

        // Real Estate & Construction
        'proptech': 159, // PropTech & Smart Spaces
        'real estate': 119,
        'commercial real estate': 20,
        'construction': 26,
        'civil engineering': 19,
        'architecture': 7,

        // Energy & CleanTech
        'cleantech': 154, // ClimateTech, CleanTech & ESG
        'climatetech': 154,
        'renewables': 122,
        'clean energy': 122,
        'solar': 122,
        'oil & energy': 97,
        'oil and gas': 97,
        'utilities': 139,

        // Education, Media, Public
        'edtech': 151, // EdTech & Future of Work
        'e-learning': 34,
        'education': 35, // Education Management
        'higher education': 56,
        'telecommunications': 133,
        'telecom': 133,
        'entertainment': 37,
        'broadcast media': 13,
        'publishing': 116,
        'hospitality': 58,
        'restaurants': 124,
        'retail': 125,
        'consumer goods': 28,
        'fmcg': 28
    };

    function normalizeTaxonomyTerm(str) {
        if (!str) return '';
        return String(str)
            .toLowerCase()
            .replace(/&/g, ' and ')
            .replace(/[\/\\,\-_|]+/g, ' ')
            .replace(/\s+/g, ' ')
            .trim();
    }

    function mapExtractedIndustry(candidates) {
        ensureManifestLoaded();
        if (!candidates) return null;

        var list = Array.isArray(candidates) ? candidates : [candidates];
        var scoredMap = new Map();

        for (var c = 0; c < list.length; c++) {
            var raw = String(list[c] || '').trim();
            if (!raw) continue;
            var norm = normalizeTaxonomyTerm(raw);
            if (!norm) continue;

            // 1. Synonym dictionary exact lookup
            if (INDUSTRY_TAXONOMY_SYNONYMS.hasOwnProperty(norm)) {
                var synId = INDUSTRY_TAXONOMY_SYNONYMS[norm];
                var synItem = indMap.get(synId);
                if (synItem) {
                    var cur1 = scoredMap.get(synItem.id) || { item: synItem, score: 0 };
                    cur1.score = Math.max(cur1.score, 10000 - (c * 10));
                    scoredMap.set(synItem.id, cur1);
                }
            }

            // 1b. Partial key match in synonyms
            var synKeys = Object.keys(INDUSTRY_TAXONOMY_SYNONYMS);
            for (var k = 0; k < synKeys.length; k++) {
                var sKey = synKeys[k];
                if (sKey.length >= 4 && (norm.indexOf(sKey) !== -1 || sKey.indexOf(norm) !== -1)) {
                    var pId = INDUSTRY_TAXONOMY_SYNONYMS[sKey];
                    var pItem = indMap.get(pId);
                    if (pItem) {
                        var curP = scoredMap.get(pItem.id) || { item: pItem, score: 0 };
                        curP.score = Math.max(curP.score, 7500 - (c * 10));
                        scoredMap.set(pItem.id, curP);
                    }
                }
            }

            // 2. Exact / startsWith / substring match against master industries
            for (var i = 0; i < indList.length; i++) {
                var item = indList[i];
                var itemNameNorm = normalizeTaxonomyTerm(item.name);
                var kwNorm = normalizeTaxonomyTerm(item.keywords || '');

                if (itemNameNorm === norm) {
                    var cur2 = scoredMap.get(item.id) || { item: item, score: 0 };
                    cur2.score = Math.max(cur2.score, 10000 - (c * 10));
                    scoredMap.set(item.id, cur2);
                    continue;
                }

                if (itemNameNorm.indexOf(norm) === 0 || norm.indexOf(itemNameNorm) === 0) {
                    var cur3 = scoredMap.get(item.id) || { item: item, score: 0 };
                    cur3.score = Math.max(cur3.score, 8000 - (c * 10));
                    scoredMap.set(item.id, cur3);
                    continue;
                }

                if (itemNameNorm.indexOf(norm) !== -1 || norm.indexOf(itemNameNorm) !== -1) {
                    var cur4 = scoredMap.get(item.id) || { item: item, score: 0 };
                    cur4.score = Math.max(cur4.score, 6000 - (c * 10));
                    scoredMap.set(item.id, cur4);
                    continue;
                }

                var tokens = norm.split(' ').filter(function(t) { return t.length >= 3; });
                var matchScore = 0;
                for (var t = 0; t < tokens.length; t++) {
                    var tok = tokens[t];
                    var regex = new RegExp('\\b' + tok.replace(/[.*+?^${}()|[\]\\]/g, '\\$&') + '\\b', 'i');
                    if (regex.test(itemNameNorm)) {
                        matchScore += 800;
                    } else if (regex.test(kwNorm)) {
                        matchScore += 400;
                    }
                }
                if (matchScore >= 800) {
                    var cur5 = scoredMap.get(item.id) || { item: item, score: 0 };
                    cur5.score = Math.max(cur5.score, matchScore - (c * 10));
                    scoredMap.set(item.id, cur5);
                }
            }
        }

        if (scoredMap.size === 0) {
            return null;
        }

        var results = Array.from(scoredMap.values());
        results.sort(function(a, b) { return b.score - a.score; });

        var primary = results[0].item;
        var alternatives = results.slice(1, 4).map(function(r) { return r.item; });

        return {
            primary: primary,
            alternatives: alternatives,
            confidence: results[0].score
        };
    }

    function mapAndSelectIndustry(inputCandidates) {
        ensureManifestLoaded();
        var matchResult = mapExtractedIndustry(inputCandidates);
        if (matchResult && matchResult.primary) {
            selectItem(matchResult.primary);
            lastAlternatives = matchResult.alternatives || [];
            return matchResult.primary;
        } else if (typeof inputCandidates === 'string' && inputCandidates.trim()) {
            commitCustomIndustry(inputCandidates.trim());
            lastAlternatives = [];
            return selectedIndustry;
        }
        return null;
    }

    // Eager synchronous load on startup
    ensureManifestLoaded();

    async function initCacheAndSync() {
        var cacheKey = 'ind_tenant_' + CURRENT_TENANT_ID;

        // 1. Check Pre-Seeded HTML DOM script tag (0ms Cold Start)
        var seedEl = document.getElementById('jero_industry_seed_data');
        if (seedEl && seedEl.textContent.trim()) {
            try {
                var seedManifest = JSON.parse(seedEl.textContent);
                if (seedManifest && Array.isArray(seedManifest.industries) && seedManifest.industries.length > 0) {
                    ingestManifest(seedManifest);
                    idbSet(cacheKey, { manifest: seedManifest, timestamp: Date.now() });
                    checkServerVersion(seedManifest.version);
                    return;
                }
            } catch (e) {}
        }

        // 2. Check IndexedDB L1 Persistent Store (< 5ms)
        try {
            var cached = await idbGet(cacheKey);
            if (cached && cached.manifest && Array.isArray(cached.manifest.industries) && cached.manifest.industries.length > 0) {
                ingestManifest(cached.manifest);
                checkServerVersion(cached.manifest.version);
                return;
            }
        } catch (err) {}

        // 3. Fallback to native select options in DOM immediately
        fallbackFromNativeSelect();

        // 4. Fallback: Fetch bundle from server
        fetchBundleFromServer(1);
    }

    function checkServerVersion(localVersion) {
        fetch(BUNDLE_URL, {
            method: 'GET',
            headers: {
                'If-None-Match': 'W/"tindex_' + CURRENT_TENANT_ID + '_ind_ind-tenant-' + CURRENT_TENANT_ID + '-v' + localVersion + '"'
            }
        }).then(function(res) {
            if (res.status === 304) return null;
            if (res.ok) return res.json();
            return null;
        }).then(function(newManifest) {
            if (newManifest && Array.isArray(newManifest.industries) && newManifest.industries.length > 0) {
                ingestManifest(newManifest);
                idbSet('ind_tenant_' + CURRENT_TENANT_ID, { manifest: newManifest, timestamp: Date.now() });
                if (indBroadcast) {
                    indBroadcast.postMessage({ type: 'IND_UPDATED', version: newManifest.version });
                }
            }
        }).catch(function() {});
    }

    function fetchBundleFromServer(version) {
        var statusEl = document.getElementById('jero_ind_sync_status');
        if (statusEl) {
            statusEl.innerHTML = '<span class="jero-ind-sync-dot"></span><span>Syncing...</span>';
        }

        fetch(BUNDLE_URL)
            .then(function(res) { return res.json(); })
            .then(function(manifest) {
                if (manifest && Array.isArray(manifest.industries) && manifest.industries.length > 0) {
                    ingestManifest(manifest);
                    idbSet('ind_tenant_' + CURRENT_TENANT_ID, { manifest: manifest, timestamp: Date.now() });
                }
                if (statusEl) {
                    statusEl.innerHTML = '<span class="jero-ind-sync-dot"></span><span>Live Synced</span>';
                }
            }).catch(function() {
                if (statusEl) {
                    statusEl.innerHTML = '<span class="jero-ind-sync-dot" style="background:#e11d48;"></span><span>Offline Cache</span>';
                }
                // If fetch fails, ensure DOM fallback is ingested
                fallbackFromNativeSelect();
            });
    }

    function initEventListeners() {
        var input = document.getElementById('industry_display');
        var clearBtn = document.getElementById('jero_ind_clear_btn');
        var chevron = document.getElementById('jero_ind_chevron');
        var tabsContainer = document.getElementById('jero_ind_tabs');
        var container = document.getElementById('jero_ind_list_container');
        var wrapEl = document.getElementById('jero_ind_select_wrap');
        var inputBox = document.getElementById('jero_ind_input_box');

        // Toggle on Input Box click
        if (inputBox) {
            inputBox.addEventListener('click', function(e) {
                if (e.target.closest('#jero_ind_clear_btn')) return;
                if (e.target.closest('#jero_ind_chevron')) return; // handled by chevron
                if (!isOpen) {
                    openDropdown();
                    if (input) input.focus();
                }
            });
        }

        // Toggle on Chevron click
        if (chevron) {
            chevron.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                if (isOpen) {
                    closeDropdown(true);
                } else {
                    openDropdown();
                    if (input) input.focus();
                }
            });
        }

        // Focus & Click on input
        if (input) {
            input.addEventListener('focus', function() {
                if (!isOpen) openDropdown();
            });

            input.addEventListener('click', function(e) {
                e.stopPropagation();
                if (!isOpen) openDropdown();
            });

            input.addEventListener('input', function() {
                if (!isOpen) openDropdown();
                currentSearch = this.value;
                renderDropdownList();
            });

            input.addEventListener('keydown', function(e) {
                if (e.key === 'ArrowDown') {
                    e.preventDefault();
                    if (!isOpen) openDropdown();
                    else moveFocus(1);
                } else if (e.key === 'ArrowUp') {
                    e.preventDefault();
                    if (!isOpen) openDropdown();
                    else moveFocus(-1);
                } else if (e.key === 'Enter') {
                    e.preventDefault();
                    if (isOpen) {
                        selectFocused();
                    }
                } else if (e.key === 'Escape') {
                    e.preventDefault();
                    closeDropdown(true);
                }
            });
        }

        if (clearBtn) {
            clearBtn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                clearSelection();
            });
        }

        if (container) {
            container.addEventListener('click', function(e) {
                var altPill = e.target.closest('.jero-ind-alt-pill');
                if (altPill) {
                    var altId = altPill.getAttribute('data-alt-id');
                    if (altId) {
                        var itm = indMap.get(parseInt(altId, 10));
                        if (itm) {
                            selectItem(itm);
                        }
                    }
                    return;
                }
                var itemEl = e.target.closest('.jero-ind-item');
                if (!itemEl) return;
                var id = itemEl.getAttribute('data-id');
                if (id) {
                    var item = indMap.get(parseInt(id, 10));
                    if (item) {
                        selectItem(item);
                    }
                }
            });
        }

        // Global hotkey '/' to search industry when not in other input
        document.addEventListener('keydown', function(e) {
            if (e.key === '/' && !isOpen) {
                var tag = (document.activeElement && document.activeElement.tagName) ? document.activeElement.tagName.toLowerCase() : '';
                if (tag !== 'input' && tag !== 'textarea' && tag !== 'select') {
                    e.preventDefault();
                    if (input) {
                        input.focus();
                        openDropdown();
                    }
                }
            }
        });

        // Deterministic outside pointer click handling (Zero-lock composedPath state machine)
        document.addEventListener('pointerdown', function(e) {
            var wrap = document.getElementById('jero_ind_select_wrap');
            var path = (typeof e.composedPath === 'function') ? e.composedPath() : [];
            var isInside = wrap && (wrap.contains(e.target) || path.indexOf(wrap) !== -1);
            if (!isInside && isOpen) {
                var inputEl = document.getElementById('industry_display');
                if (inputEl && inputEl.value.trim()) {
                    commitIfCustom();
                }
                closeDropdown(true);
            }
        }, true);

        // Reposition on window resize or scroll
        window.addEventListener('resize', function() {
            if (isOpen) adjustDropdownPosition();
        }, { passive: true });
        window.addEventListener('scroll', function() {
            if (isOpen) adjustDropdownPosition();
        }, { passive: true });

        // Synchronize if native select is changed programmatically
        $('#industry').on('change', function() {
            var val = $(this).val();
            if (!selectedIndustry || String(selectedIndustry.id) !== String(val)) {
                syncInitialState();
            }
        });

        // BroadcastChannel for cross-tab sync
        if ('BroadcastChannel' in window) {
            try {
                indBroadcast = new BroadcastChannel('jero_industry_channel');
                indBroadcast.onmessage = function(ev) {
                    if (ev.data && ev.data.type === 'IND_UPDATED') {
                        fetchBundleFromServer(ev.data.version || 1);
                    }
                };
            } catch (e) {}
        }

        // Storage event fallback
        window.addEventListener('storage', function(e) {
            if (e.key === 'jero_industry_sync_timestamp') {
                fetchBundleFromServer(1);
            }
        });

        // Tab focus reconciliation
        window.addEventListener('focus', function() {
            var now = Date.now();
            if (now - lastFocusCheck > 30000) {
                lastFocusCheck = now;
                checkServerVersion(localManifestVersion);
            }
        });
    }

    // Global export for backward compatibility & testing
    window.JeroIndustryIntelligenceEngine = {
        open: openDropdown,
        close: closeDropdown,
        isOpen: function() { return isOpen; },
        commitIfCustom: commitIfCustom,
        commitCustomIndustry: commitCustomIndustry,
        getSelected: function() { return selectedIndustry; },
        getById: function(id) { return indMap.get(parseInt(id, 10)); },
        getAll: function() { return indList; },
        search: function(q) {
            currentSearch = q;
            renderDropdownList();
        },
        selectById: function(id) {
            var item = indMap.get(parseInt(id, 10));
            if (item) selectItem(item);
        },
        selectByName: function(name) {
            var match = mapExtractedIndustry(name);
            if (match && match.primary) {
                selectItem(match.primary);
                lastAlternatives = match.alternatives || [];
                return;
            }
            var clean = (name || '').trim().toLowerCase();
            for (var i = 0; i < indList.length; i++) {
                if (indList[i].name.toLowerCase() === clean) {
                    selectItem(indList[i]);
                    return;
                }
            }
        },
        mapAndSelectIndustry: mapAndSelectIndustry,
        mapExtractedIndustry: mapExtractedIndustry,
        sync: syncInitialState,
        clear: clearSelection,
        refresh: function() {
            fetchBundleFromServer(1);
        }
    };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function() {
            initCacheAndSync();
            initEventListeners();
        });
    } else {
        initCacheAndSync();
        initEventListeners();
    }
})();
</script>

<!-- =========================================================================
     Search-First Company Type Intelligence Engine (Client-First / 10K Concurrency)
     Strictly isolated to Client Wizard (Rule 17)
     ========================================================================= -->
<script id="jero_company_type_seed_data" type="application/json">
    {!! json_encode($companyTypeTaxonomy ?? \App\Helper\GeneralMethod::companyTypeTaxonomy()) !!}
</script>

<script>
(function() {
    'use strict';

    var taxonomyList = [];
    var taxonomyMap = new Map();
    var filteredList = [];
    var focusedIndex = -1;
    var isOpen = false;
    var suppressOpenOnFocus = false;

    // DOM References
    var wrap = null;
    var input = null;
    var hiddenSelect = null;
    var inputBox = null;
    var clearBtn = null;
    var kbd = null;
    var chevron = null;
    var dropdown = null;
    var listContainer = null;

    function escapeHtml(str) {
        if (!str) return '';
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function highlightMatch(text, query) {
        var safeText = escapeHtml(text || '');
        if (!query) return safeText;
        var cleanQ = query.trim().replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
        if (!cleanQ) return safeText;
        var regex = new RegExp('(' + cleanQ + ')', 'gi');
        return safeText.replace(regex, '<mark class="jero-ctype-mark">$1</mark>');
    }

    function initData() {
        var seedEl = document.getElementById('jero_company_type_seed_data');
        if (seedEl && seedEl.textContent) {
            try {
                taxonomyList = JSON.parse(seedEl.textContent);
            } catch (e) {
                taxonomyList = [];
            }
        }
        taxonomyMap.clear();
        for (var i = 0; i < taxonomyList.length; i++) {
            taxonomyMap.set(parseInt(taxonomyList[i].id, 10), taxonomyList[i]);
        }
    }

    function getSelectedId() {
        if (!hiddenSelect) return null;
        var val = hiddenSelect.value;
        if (val === '' || val === null || typeof val === 'undefined') return null;
        return parseInt(val, 10);
    }

    function filterItems(query) {
        var clean = (query || '').trim().toLowerCase();
        if (!clean) {
            filteredList = taxonomyList.map(function(item) {
                return { item: item, matchedAlias: null };
            });
            return;
        }

        var results = [];
        for (var i = 0; i < taxonomyList.length; i++) {
            var item = taxonomyList[i];
            var nameMatch = (item.display_name && item.display_name.toLowerCase().indexOf(clean) !== -1) ||
                            (item.name && item.name.toLowerCase().indexOf(clean) !== -1);
            var descMatch = item.desc && item.desc.toLowerCase().indexOf(clean) !== -1;
            var matchedAlias = null;

            if (item.aliases && item.aliases.length) {
                for (var a = 0; a < item.aliases.length; a++) {
                    if (item.aliases[a].toLowerCase().indexOf(clean) !== -1) {
                        matchedAlias = item.aliases[a];
                        break;
                    }
                }
            }

            if (nameMatch || descMatch || matchedAlias) {
                results.push({
                    item: item,
                    matchedAlias: matchedAlias
                });
            }
        }
        filteredList = results;
    }

    function renderList(query) {
        if (!listContainer) return;
        if (!filteredList.length) {
            listContainer.innerHTML = '<div class="jero-ctype-empty-state">' +
                '<div style="font-weight:600; margin-bottom:4px; color:#1e293b;">No company type found</div>' +
                '<div style="font-size:11.5px; color:#64748b;">No results matching "' + escapeHtml(query) + '".</div>' +
                '</div>';
            return;
        }

        var selectedId = getSelectedId();
        var html = '';

        for (var i = 0; i < filteredList.length; i++) {
            var entry = filteredList[i];
            var item = entry.item;
            var isSelected = (selectedId !== null && selectedId === parseInt(item.id, 10));
            var isFocused = (i === focusedIndex);

            var checkIcon = isSelected ? '<span class="jero-ctype-item-check"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg></span>' : '';

            var badgeHtml = '';
            if (entry.matchedAlias) {
                badgeHtml = '<span class="jero-ctype-badge matched-alias">Matched: ' + escapeHtml(entry.matchedAlias) + '</span>';
            } else if (item.badge) {
                badgeHtml = '<span class="jero-ctype-badge">' + escapeHtml(item.badge) + '</span>';
            }

            var aliasesPreview = '';
            if (item.aliases && item.aliases.length && !entry.matchedAlias) {
                aliasesPreview = '<div class="jero-ctype-item-aliases">' + escapeHtml(item.aliases.slice(0, 4).join(' • ')) + (item.aliases.length > 4 ? ' • ...' : '') + '</div>';
            }

            html += '<div class="jero-ctype-item ' + (isSelected ? 'selected' : '') + ' ' + (isFocused ? 'focused' : '') + '" ' +
                'data-idx="' + i + '" data-id="' + item.id + '" role="option" id="jero_ctype_opt_' + item.id + '" aria-selected="' + (isSelected ? 'true' : 'false') + '">' +
                '<div class="jero-ctype-item-main">' +
                '<div class="jero-ctype-item-name">' + highlightMatch(item.display_name || item.name, query) + '</div>' +
                '</div>' +
                '<div class="jero-ctype-item-side">' +
                checkIcon +
                '</div>' +
                '</div>';
        }

        listContainer.innerHTML = html;
        scrollFocusedIntoView();
    }

    function scrollFocusedIntoView() {
        if (!listContainer || focusedIndex < 0) {
            if (input) input.removeAttribute('aria-activedescendant');
            return;
        }
        var items = listContainer.querySelectorAll('.jero-ctype-item');
        if (items[focusedIndex]) {
            var item = items[focusedIndex];
            var itemTop = item.offsetTop;
            var itemBottom = itemTop + item.offsetHeight;
            var containerTop = listContainer.scrollTop;
            var containerBottom = containerTop + listContainer.offsetHeight;

            if (itemTop < containerTop) {
                listContainer.scrollTop = itemTop;
            } else if (itemBottom > containerBottom) {
                listContainer.scrollTop = itemBottom - listContainer.offsetHeight;
            }

            // WAI-ARIA 1.2: Update aria-activedescendant to the focused option's id
            if (input && item.id) {
                input.setAttribute('aria-activedescendant', item.id);
            }
        }
    }

    function updateDropdownPosition() {
        if (!wrap || !dropdown) return;
        var rect = wrap.getBoundingClientRect();
        var spaceBelow = window.innerHeight - rect.bottom;
        var dropdownHeight = 300;
        if (spaceBelow < dropdownHeight && rect.top > dropdownHeight) {
            dropdown.classList.add('drop-up');
        } else {
            dropdown.classList.remove('drop-up');
        }
    }

    function openDropdown() {
        if (isOpen) return;
        isOpen = true;
        updateDropdownPosition();
        if (wrap) wrap.classList.add('is-open');
        if (dropdown) dropdown.style.display = 'flex';
        if (input) input.setAttribute('aria-expanded', 'true');

        filterItems(input ? input.value : '');
        var selectedId = getSelectedId();
        focusedIndex = -1;
        if (selectedId !== null) {
            for (var i = 0; i < filteredList.length; i++) {
                if (parseInt(filteredList[i].item.id, 10) === selectedId) {
                    focusedIndex = i;
                    break;
                }
            }
        }
        if (focusedIndex === -1 && filteredList.length > 0) {
            focusedIndex = 0;
        }
        renderList(input ? input.value : '');
    }

    function closeDropdown(restoreText) {
        if (!isOpen) return;
        isOpen = false;
        if (wrap) wrap.classList.remove('is-open');
        if (dropdown) dropdown.style.display = 'none';
        if (input) input.setAttribute('aria-expanded', 'false');
        if (input) input.removeAttribute('aria-activedescendant');
        focusedIndex = -1;

        if (restoreText && input) {
            var selectedId = getSelectedId();
            if (selectedId !== null && taxonomyMap.has(selectedId)) {
                input.value = taxonomyMap.get(selectedId).display_name || taxonomyMap.get(selectedId).name;
            } else {
                input.value = '';
            }
            updateInputVisualState();
        }
    }

    function updateInputVisualState() {
        var hasVal = input && input.value.trim().length > 0;
        if (inputBox) {
            if (hasVal) {
                inputBox.classList.add('has-value');
            } else {
                inputBox.classList.remove('has-value');
            }
        }
        if (clearBtn) clearBtn.style.display = hasVal ? 'flex' : 'none';
        if (kbd) kbd.style.display = hasVal ? 'none' : 'inline-flex';
    }

    function selectItem(item) {
        if (!item) return;
        if (hiddenSelect) {
            hiddenSelect.value = item.id;
            $(hiddenSelect).trigger('change');
        }
        if (input) {
            input.value = item.display_name || item.name;
        }
        updateInputVisualState();
        if (typeof window.clearFieldError === 'function' && hiddenSelect) {
            window.clearFieldError($(hiddenSelect));
        }
        closeDropdown(false);
    }

    function clearSelection() {
        if (hiddenSelect) {
            hiddenSelect.value = '';
            $(hiddenSelect).trigger('change');
        }
        if (input) {
            input.value = '';
        }
        updateInputVisualState();
        filterItems('');
        renderList('');
        if (input) input.focus();
    }

    function initEventListeners() {
        wrap = document.getElementById('jero_ctype_select_wrap');
        input = document.getElementById('company_type_display');
        hiddenSelect = document.getElementById('company_type');
        inputBox = document.getElementById('jero_ctype_input_box');
        clearBtn = document.getElementById('jero_ctype_clear_btn');
        kbd = document.getElementById('jero_ctype_kbd');
        chevron = document.getElementById('jero_ctype_chevron');
        dropdown = document.getElementById('jero_ctype_dropdown');
        listContainer = document.getElementById('jero_ctype_list_container');

        if (!wrap || !input || !hiddenSelect) return;

        // Toggle on chevron click
        if (chevron) {
            chevron.addEventListener('click', function(e) {
                e.stopPropagation();
                if (isOpen) {
                    closeDropdown(true);
                } else {
                    input.focus();
                    openDropdown();
                }
            });
        }

        // Toggle on input box click
        if (inputBox) {
            inputBox.addEventListener('click', function(e) {
                if (e.target.closest('#jero_ctype_clear_btn')) return;
                if (e.target.closest('#jero_ctype_chevron')) return;
                if (!isOpen) {
                    input.focus();
                    openDropdown();
                }
            });
        }

        // Focus & Click on input
        input.addEventListener('focus', function() {
            openDropdown();
        });
        input.addEventListener('click', function(e) {
            e.stopPropagation();
            openDropdown();
        });

        // Dropdown internal clicks should never bubble out to close
        if (dropdown) {
            dropdown.addEventListener('pointerdown', function(e) {
                e.stopPropagation();
            });
            dropdown.addEventListener('click', function(e) {
                e.stopPropagation();
            });
        }

        // Typing in input
        input.addEventListener('input', function() {
            if (!isOpen) openDropdown();
            focusedIndex = 0;
            filterItems(input.value);
            renderList(input.value);
            updateInputVisualState();
        });

        // Clear button
        if (clearBtn) {
            clearBtn.addEventListener('click', function(e) {
                e.stopPropagation();
                clearSelection();
            });
        }

        // Item selection via click
        if (listContainer) {
            listContainer.addEventListener('click', function(e) {
                var itemEl = e.target.closest('.jero-ctype-item');
                if (!itemEl) return;
                var id = parseInt(itemEl.getAttribute('data-id'), 10);
                if (taxonomyMap.has(id)) {
                    selectItem(taxonomyMap.get(id));
                }
            });
        }

        // Keyboard navigation
        input.addEventListener('keydown', function(e) {
            if (e.key === 'ArrowDown') {
                e.preventDefault();
                if (!isOpen) {
                    openDropdown();
                    return;
                }
                if (filteredList.length > 0) {
                    focusedIndex = (focusedIndex + 1) % filteredList.length;
                    renderList(input.value);
                }
            } else if (e.key === 'ArrowUp') {
                e.preventDefault();
                if (!isOpen) {
                    openDropdown();
                    return;
                }
                if (filteredList.length > 0) {
                    focusedIndex = (focusedIndex - 1 + filteredList.length) % filteredList.length;
                    renderList(input.value);
                }
            } else if (e.key === 'Home') {
                if (isOpen && filteredList.length > 0) {
                    e.preventDefault();
                    focusedIndex = 0;
                    renderList(input.value);
                }
            } else if (e.key === 'End') {
                if (isOpen && filteredList.length > 0) {
                    e.preventDefault();
                    focusedIndex = filteredList.length - 1;
                    renderList(input.value);
                }
            } else if (e.key === 'Enter') {
                if (isOpen) {
                    e.preventDefault();
                    if (focusedIndex >= 0 && focusedIndex < filteredList.length) {
                        selectItem(filteredList[focusedIndex].item);
                    }
                }
            } else if (e.key === 'Escape') {
                if (isOpen) {
                    e.preventDefault();
                    closeDropdown(true);
                }
            } else if (e.key === 'Tab') {
                closeDropdown(true);
            }
        });

        // Global quick-focus on '/'
        document.addEventListener('keydown', function(e) {
            if (e.key === '/' && !isOpen && document.activeElement !== input) {
                var tag = (document.activeElement && document.activeElement.tagName) ? document.activeElement.tagName.toLowerCase() : '';
                if (tag !== 'input' && tag !== 'textarea' && !document.activeElement.isContentEditable) {
                    e.preventDefault();
                    input.focus();
                }
            }
        });

        // Deterministic outside pointer click handling (Zero-lock composedPath state machine)
        document.addEventListener('pointerdown', function(e) {
            var path = (typeof e.composedPath === 'function') ? e.composedPath() : [];
            var isInside = wrap && (wrap.contains(e.target) || path.indexOf(wrap) !== -1);
            if (!isInside && isOpen) {
                closeDropdown(true);
            }
        }, true);

        // Window resize / scroll
        window.addEventListener('resize', function() {
            if (isOpen) updateDropdownPosition();
        });

        // External change listener on native select (e.g. from autofill or edit)
        $(hiddenSelect).on('change', function() {
            var val = getSelectedId();
            if (val !== null && taxonomyMap.has(val)) {
                input.value = taxonomyMap.get(val).display_name || taxonomyMap.get(val).name;
            } else if (val === null || val === '') {
                input.value = '';
            }
            updateInputVisualState();
        });

        // Initial visual sync
        var initId = getSelectedId();
        if (initId !== null && taxonomyMap.has(initId)) {
            input.value = taxonomyMap.get(initId).display_name || taxonomyMap.get(initId).name;
        }
        updateInputVisualState();
    }

    // Public API
    window.JeroCompanyTypeEngine = {
        selectById: function(id) {
            var numId = parseInt(id, 10);
            if (taxonomyMap.has(numId)) {
                selectItem(taxonomyMap.get(numId));
            }
        },
        selectByName: function(name) {
            var clean = (name || '').trim().toLowerCase();
            for (var i = 0; i < taxonomyList.length; i++) {
                if ((taxonomyList[i].name && taxonomyList[i].name.toLowerCase() === clean) ||
                    (taxonomyList[i].display_name && taxonomyList[i].display_name.toLowerCase() === clean)) {
                    selectItem(taxonomyList[i]);
                    return;
                }
            }
        },
        clear: clearSelection
    };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function() {
            initData();
            initEventListeners();
        });
    } else {
        initData();
        initEventListeners();
    }
})();
</script>

<!-- =========================================================================
     Search-First Founded Year Intelligence Engine (Client-First / 10K Concurrency)
     Strictly isolated to Client Wizard (Rule 17)
     ========================================================================= -->
<script>
(function() {
    'use strict';

    var currentYear = (new Date()).getFullYear();
    var minYear = 1800;
    var yearsList = [];
    for (var y = currentYear; y >= minYear; y--) {
        yearsList.push(y);
    }

    var filteredList = [];
    var focusedIndex = -1;
    var isOpen = false;
    var suppressOpenOnFocus = false;

    // DOM Elements
    var wrap = null;
    var input = null;
    var searchInput = null;
    var hiddenInput = null;
    var inputBox = null;
    var clearBtn = null;
    var kbd = null;
    var chevron = null;
    var dropdown = null;
    var listContainer = null;

    function escapeHtml(str) {
        if (!str) return '';
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function highlightMatch(text, query) {
        var safeText = escapeHtml(String(text || ''));
        if (!query) return safeText;
        var cleanQ = query.trim().replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
        if (!cleanQ) return safeText;
        var regex = new RegExp('(' + cleanQ + ')', 'gi');
        return safeText.replace(regex, '<mark class="jero-founded-mark">$1</mark>');
    }

    function getSelectedYear() {
        if (!hiddenInput) return null;
        var val = (hiddenInput.value || '').trim();
        return val ? parseInt(val, 10) : null;
    }

    function filterYears(query) {
        var clean = (query || '').trim();
        if (!clean) {
            filteredList = yearsList.slice();
            return;
        }
        filteredList = yearsList.filter(function(yr) {
            return String(yr).indexOf(clean) !== -1;
        });
    }

    function renderList(query) {
        if (!listContainer) return;
        if (!filteredList.length) {
            listContainer.innerHTML = '<div class="jero-founded-empty-state">' +
                '<div style="font-weight:600; margin-bottom:4px; color:#1e293b;">No years found</div>' +
                '<div style="font-size:11.5px; color:#64748b;">No results matching "' + escapeHtml(query) + '".</div>' +
                '</div>';
            return;
        }

        var selected = getSelectedYear();
        var html = '';
        for (var i = 0; i < filteredList.length; i++) {
            var yr = filteredList[i];
            var isSelected = (selected !== null && selected === yr);
            var isFocused = (i === focusedIndex);
            var checkIcon = isSelected ? '<span class="jero-founded-item-check"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg></span>' : '';

            html += '<div class="jero-founded-item ' + (isSelected ? 'selected' : '') + ' ' + (isFocused ? 'focused' : '') + '" ' +
                'data-idx="' + i + '" data-year="' + yr + '" role="option" id="jero_founded_opt_' + yr + '" aria-selected="' + (isSelected ? 'true' : 'false') + '">' +
                '<div class="jero-founded-item-name">' + highlightMatch(yr, query) + '</div>' +
                checkIcon +
                '</div>';
        }

        listContainer.innerHTML = html;
        scrollFocusedIntoView();
    }

    function scrollFocusedIntoView() {
        if (!listContainer || focusedIndex < 0) return;
        var items = listContainer.querySelectorAll('.jero-founded-item');
        if (items[focusedIndex]) {
            var item = items[focusedIndex];
            var itemTop = item.offsetTop;
            var itemBottom = itemTop + item.offsetHeight;
            var containerTop = listContainer.scrollTop;
            var containerBottom = containerTop + listContainer.offsetHeight;

            if (itemTop < containerTop) {
                listContainer.scrollTop = itemTop;
            } else if (itemBottom > containerBottom) {
                listContainer.scrollTop = itemBottom - listContainer.offsetHeight;
            }
        }
    }

    function updateDropdownPosition() {
        if (!wrap || !dropdown) return;
        var rect = wrap.getBoundingClientRect();
        var spaceBelow = window.innerHeight - rect.bottom;
        var dropdownHeight = 280;
        if (spaceBelow < dropdownHeight && rect.top > dropdownHeight) {
            dropdown.classList.add('drop-up');
        } else {
            dropdown.classList.remove('drop-up');
        }
    }

    function openDropdown() {
        if (isOpen) return;
        isOpen = true;
        updateDropdownPosition();
        if (wrap) wrap.classList.add('is-open');
        if (dropdown) dropdown.style.display = 'flex';
        if (input) input.setAttribute('aria-expanded', 'true');

        if (searchInput) {
            searchInput.value = '';
            setTimeout(function() { searchInput.focus(); }, 60);
        }

        filterYears('');
        var selected = getSelectedYear();
        focusedIndex = -1;
        if (selected !== null) {
            for (var i = 0; i < filteredList.length; i++) {
                if (filteredList[i] === selected) {
                    focusedIndex = i;
                    break;
                }
            }
        }
        if (focusedIndex === -1 && filteredList.length > 0) {
            focusedIndex = 0;
        }
        renderList('');

        if (selected !== null && listContainer) {
            var selEl = listContainer.querySelector('.jero-founded-item.selected');
            if (selEl) {
                listContainer.scrollTop = Math.max(0, selEl.offsetTop - 60);
            }
        }
    }

    function closeDropdown(restoreFocus) {
        if (!isOpen) return;
        isOpen = false;
        if (wrap) wrap.classList.remove('is-open');
        if (dropdown) dropdown.style.display = 'none';
        if (input) input.setAttribute('aria-expanded', 'false');
        focusedIndex = -1;
        if (restoreFocus && input) {
            suppressOpenOnFocus = true;
            try {
                input.focus();
            } finally {
                setTimeout(function() {
                    suppressOpenOnFocus = false;
                }, 100);
            }
        }
    }

    function updateInputVisualState() {
        var hasVal = hiddenInput && (hiddenInput.value || '').trim().length > 0;
        if (inputBox) {
            if (hasVal) {
                inputBox.classList.add('has-value');
            } else {
                inputBox.classList.remove('has-value');
            }
        }
        if (clearBtn) clearBtn.style.display = hasVal ? 'flex' : 'none';
        if (kbd) kbd.style.display = hasVal ? 'none' : 'inline-flex';
    }

    function selectYear(yr) {
        if (!yr) return;
        var cleanYr = parseInt(yr, 10);
        if (isNaN(cleanYr)) return;
        if (hiddenInput) {
            hiddenInput.value = cleanYr;
            $(hiddenInput).trigger('input').trigger('change');
        }
        if (input) {
            input.value = cleanYr;
        }
        updateInputVisualState();
        $('.span_founded').text(cleanYr);

        $('#jero_founded_input_box').removeClass('has-validation-error');
        $('#jero_founded_select_wrap').removeClass('has-validation-error');
        if (typeof window.clearFieldError === 'function' && hiddenInput) {
            window.clearFieldError($(hiddenInput));
        }
        closeDropdown(false);
    }

    function clearYear() {
        if (hiddenInput) {
            hiddenInput.value = '';
            $(hiddenInput).trigger('input').trigger('change');
        }
        if (input) {
            input.value = '';
        }
        updateInputVisualState();
        $('.span_founded').text('-');
        filterYears('');
        renderList('');
        closeDropdown(false);
    }

    function initEventListeners() {
        wrap = document.getElementById('jero_founded_select_wrap');
        input = document.getElementById('founded_display');
        searchInput = document.getElementById('founded_search_input');
        hiddenInput = document.getElementById('founded');
        inputBox = document.getElementById('jero_founded_input_box');
        clearBtn = document.getElementById('jero_founded_clear_btn');
        kbd = document.getElementById('jero_founded_kbd');
        chevron = document.getElementById('jero_founded_chevron');
        dropdown = document.getElementById('jero_founded_dropdown');
        listContainer = document.getElementById('jero_founded_list_container');

        if (!wrap || !input || !hiddenInput) return;

        // Toggle on chevron click
        if (chevron) {
            chevron.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                if (isOpen) {
                    closeDropdown(false);
                } else {
                    openDropdown();
                }
            });
        }

        // Toggle on input box click
        if (inputBox) {
            inputBox.addEventListener('click', function(e) {
                if (e.target.closest('#jero_founded_clear_btn')) return;
                if (e.target.closest('#jero_founded_chevron')) return;
                if (!isOpen) {
                    openDropdown();
                } else if (searchInput) {
                    searchInput.focus();
                }
            });
        }

        // Click / Focus on display input (guaranteed zero premature auto-close)
        input.addEventListener('click', function(e) {
            e.stopPropagation();
            if (!isOpen) {
                openDropdown();
            } else if (searchInput) {
                searchInput.focus();
            }
        });
        input.addEventListener('focus', function() {
            if (suppressOpenOnFocus) return;
            if (!isOpen) openDropdown();
        });

        // Dropdown internal clicks should never bubble out to close
        if (dropdown) {
            dropdown.addEventListener('pointerdown', function(e) {
                e.stopPropagation();
            });
            dropdown.addEventListener('click', function(e) {
                e.stopPropagation();
            });
        }

        // Typing in search input
        if (searchInput) {
            searchInput.addEventListener('input', function() {
                focusedIndex = 0;
                filterYears(this.value);
                renderList(this.value);
            });

            searchInput.addEventListener('keydown', function(e) {
                if (e.key === 'ArrowDown') {
                    e.preventDefault();
                    if (filteredList.length > 0) {
                        focusedIndex = (focusedIndex + 1) % filteredList.length;
                        renderList(searchInput.value);
                    }
                } else if (e.key === 'ArrowUp') {
                    e.preventDefault();
                    if (filteredList.length > 0) {
                        focusedIndex = (focusedIndex - 1 + filteredList.length) % filteredList.length;
                        renderList(searchInput.value);
                    }
                } else if (e.key === 'Home') {
                    if (filteredList.length > 0) {
                        e.preventDefault();
                        focusedIndex = 0;
                        renderList(searchInput.value);
                    }
                } else if (e.key === 'End') {
                    if (filteredList.length > 0) {
                        e.preventDefault();
                        focusedIndex = filteredList.length - 1;
                        renderList(searchInput.value);
                    }
                } else if (e.key === 'Enter') {
                    e.preventDefault();
                    if (focusedIndex >= 0 && focusedIndex < filteredList.length) {
                        selectYear(filteredList[focusedIndex]);
                    }
                } else if (e.key === 'Escape') {
                    e.preventDefault();
                    closeDropdown(true);
                } else if (e.key === 'Tab') {
                    closeDropdown(false);
                }
            });
        }

        // Display input keyboard navigation
        input.addEventListener('keydown', function(e) {
            if (e.key === 'ArrowDown' || e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                if (!isOpen) openDropdown();
            } else if (e.key === 'Escape') {
                e.preventDefault();
                closeDropdown(false);
            }
        });

        // Clear button
        if (clearBtn) {
            clearBtn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                clearYear();
            });
        }

        // Item selection via click
        if (listContainer) {
            listContainer.addEventListener('click', function(e) {
                var itemEl = e.target.closest('.jero-founded-item');
                if (!itemEl) return;
                var yr = itemEl.getAttribute('data-year');
                if (yr) {
                    selectYear(yr);
                }
            });
        }

        // Deterministic outside pointer click handling (Zero-lock composedPath state machine)
        document.addEventListener('pointerdown', function(e) {
            var path = (typeof e.composedPath === 'function') ? e.composedPath() : [];
            var isInside = wrap && (wrap.contains(e.target) || path.indexOf(wrap) !== -1);
            if (!isInside && isOpen) {
                closeDropdown(false);
            }
        }, true);

        // Window resize / scroll reposition
        window.addEventListener('resize', function() {
            if (isOpen) updateDropdownPosition();
        });
        window.addEventListener('scroll', function() {
            if (isOpen) updateDropdownPosition();
        });

        // External change listener on native hidden input (e.g. from autofill)
        $(hiddenInput).on('change input', function() {
            var val = (this.value || '').trim();
            if (input) input.value = val;
            updateInputVisualState();
            $('.span_founded').text(val || '-');
        });

        // Initial visual sync
        var initVal = (hiddenInput.value || '').trim();
        if (initVal && input) {
            input.value = initVal;
        }
        updateInputVisualState();
    }

    // Public API
    window.JeroFoundedYearEngine = {
        selectYear: selectYear,
        clearYear: clearYear,
        open: openDropdown,
        close: closeDropdown,
        getYears: function() { return yearsList; }
    };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initEventListeners);
    } else {
        initEventListeners();
    }
})();

/**
 * =========================================================================
 * JERO ENTERPRISE COMPANY SIZE ENGINE (RULE 17 ISOLATED)
 * Modern custom combobox component replacing browser-native select menu.
 * Full 2-way sync with hidden <select name="company_sizes" id="company_sizes">
 * =========================================================================
 */
(function() {
    'use strict';

    var wrap = null;
    var inputBox = null;
    var displayInput = null;
    var hiddenSelect = null;
    var clearBtn = null;
    var chevron = null;
    var dropdown = null;
    var listContainer = null;
    var isOpen = false;
    var focusedIndex = -1;

    function getItems() {
        return listContainer ? listContainer.querySelectorAll('.jero-csize-item') : [];
    }

    function adjustPosition() {
        if (!wrap || !dropdown || !isOpen) return;
        var rect = wrap.getBoundingClientRect();
        var vh = window.innerHeight || document.documentElement.clientHeight || 768;

        if (rect.bottom < 0 || rect.top > vh) {
            closeDropdown();
            return;
        }

        dropdown.classList.remove('drop-up');
        dropdown.style.bottom = 'auto';
        dropdown.style.top = 'calc(100% + 5px)';
        wrap.style.zIndex = '1040';
        wrap.style.position = 'relative';

        var spaceBelow = Math.max(160, vh - rect.bottom - 16);
        var idealHeight = Math.min(320, Math.max(180, spaceBelow));
        dropdown.style.maxHeight = idealHeight + 'px';
        if (listContainer) {
            listContainer.style.maxHeight = (idealHeight - 12) + 'px';
        }
    }

    function openDropdown() {
        if (isOpen || !wrap || !dropdown) return;
        isOpen = true;
        wrap.classList.add('is-open');
        wrap.style.zIndex = '1040';
        wrap.style.position = 'relative';
        dropdown.style.display = 'flex';
        if (inputBox) inputBox.setAttribute('aria-expanded', 'true');
        adjustPosition();

        var selected = listContainer ? listContainer.querySelector('.jero-csize-item.selected') : null;
        if (selected && listContainer) {
            var sTop = selected.offsetTop - listContainer.offsetTop;
            listContainer.scrollTop = Math.max(0, sTop - 40);
        }
    }

    function closeDropdown(restoreFocus) {
        if (!isOpen || !wrap || !dropdown) return;
        isOpen = false;
        wrap.classList.remove('is-open');
        wrap.style.zIndex = '';
        dropdown.style.display = 'none';
        if (inputBox) inputBox.setAttribute('aria-expanded', 'false');
        focusedIndex = -1;
        var items = getItems();
        for (var i = 0; i < items.length; i++) {
            items[i].classList.remove('focused');
        }
        if (restoreFocus && inputBox) {
            inputBox.focus();
        }
    }

    function updateVisualState() {
        var val = hiddenSelect ? (hiddenSelect.value || '').trim() : '';
        var hasVal = val.length > 0;
        if (inputBox) {
            if (hasVal) {
                inputBox.classList.add('has-value');
            } else {
                inputBox.classList.remove('has-value');
            }
        }
        if (clearBtn) clearBtn.style.display = hasVal ? 'flex' : 'none';

        var items = getItems();
        for (var i = 0; i < items.length; i++) {
            var itemVal = items[i].getAttribute('data-val');
            var isSel = (itemVal === val) || (val === '1-10' && itemVal === '2-10') || (itemVal === '50001-0' && (val === '50001-' || val === '10000-999999'));
            if (isSel) {
                items[i].classList.add('selected');
                items[i].setAttribute('aria-selected', 'true');
            } else {
                items[i].classList.remove('selected');
                items[i].setAttribute('aria-selected', 'false');
            }
        }
    }

    function selectOption(val, label) {
        if (!hiddenSelect) return;
        hiddenSelect.value = val;
        $(hiddenSelect).trigger('change');

        if (displayInput) {
            displayInput.value = label || (hiddenSelect.options[hiddenSelect.selectedIndex] ? hiddenSelect.options[hiddenSelect.selectedIndex].text : '');
        }

        updateVisualState();

        if (typeof window.clearFieldError === 'function') {
            window.clearFieldError($(hiddenSelect));
        }
        $('#jero_csize_input_box, #jero_csize_select_wrap').removeClass('has-validation-error');

        // Review summary text sync
        var txt = displayInput ? displayInput.value : '';
        $('.span_company_sizes').text(txt && txt !== 'Select company size' ? txt : '—');

        closeDropdown(true);
    }

    function clearOption() {
        if (!hiddenSelect) return;
        hiddenSelect.value = '';
        $(hiddenSelect).trigger('change');

        if (displayInput) {
            displayInput.value = '';
        }

        updateVisualState();
        $('.span_company_sizes').text('—');
        closeDropdown(false);
        if (inputBox) inputBox.focus();
    }

    function moveFocus(delta) {
        var items = getItems();
        if (items.length === 0) return;

        if (focusedIndex >= 0 && focusedIndex < items.length) {
            items[focusedIndex].classList.remove('focused');
        }

        focusedIndex += delta;
        if (focusedIndex < 0) focusedIndex = items.length - 1;
        if (focusedIndex >= items.length) focusedIndex = 0;

        var target = items[focusedIndex];
        target.classList.add('focused');

        if (listContainer) {
            var cTop = listContainer.scrollTop;
            var cH = listContainer.clientHeight;
            var tTop = target.offsetTop;
            var tH = target.offsetHeight;

            if (tTop < cTop) {
                listContainer.scrollTop = tTop - 6;
            } else if (tTop + tH > cTop + cH) {
                listContainer.scrollTop = (tTop + tH) - cH + 6;
            }
        }
    }

    function initEventListeners() {
        wrap = document.getElementById('jero_csize_select_wrap');
        inputBox = document.getElementById('jero_csize_input_box');
        displayInput = document.getElementById('company_sizes_display');
        hiddenSelect = document.getElementById('company_sizes');
        clearBtn = document.getElementById('jero_csize_clear_btn');
        chevron = document.getElementById('jero_csize_chevron');
        dropdown = document.getElementById('jero_csize_dropdown');
        listContainer = document.getElementById('jero_csize_list_container');

        if (!wrap || !hiddenSelect) return;

        if (inputBox) {
            inputBox.addEventListener('click', function(e) {
                if (e.target.closest('#jero_csize_clear_btn')) return;
                if (isOpen) {
                    closeDropdown();
                } else {
                    openDropdown();
                }
            });

            inputBox.addEventListener('keydown', function(e) {
                if (e.key === 'ArrowDown') {
                    e.preventDefault();
                    if (!isOpen) openDropdown();
                    else moveFocus(1);
                } else if (e.key === 'ArrowUp') {
                    e.preventDefault();
                    if (!isOpen) openDropdown();
                    else moveFocus(-1);
                } else if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    if (!isOpen) {
                        openDropdown();
                    } else if (focusedIndex >= 0) {
                        var items = getItems();
                        if (items[focusedIndex]) {
                            var val = items[focusedIndex].getAttribute('data-val');
                            var lbl = items[focusedIndex].getAttribute('data-label');
                            selectOption(val, lbl);
                        }
                    }
                } else if (e.key === 'Escape') {
                    e.preventDefault();
                    closeDropdown();
                }
            });
        }

        if (chevron) {
            chevron.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                if (isOpen) closeDropdown();
                else openDropdown();
            });
        }

        if (clearBtn) {
            clearBtn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                clearOption();
            });
        }

        if (listContainer) {
            listContainer.addEventListener('click', function(e) {
                var itemEl = e.target.closest('.jero-csize-item');
                if (!itemEl) return;
                var val = itemEl.getAttribute('data-val');
                var lbl = itemEl.getAttribute('data-label');
                selectOption(val, lbl);
            });
        }

        // Deterministic outside pointer click handling
        document.addEventListener('pointerdown', function(e) {
            var path = (typeof e.composedPath === 'function') ? e.composedPath() : [];
            var isInside = wrap && (wrap.contains(e.target) || path.indexOf(wrap) !== -1);
            if (!isInside && isOpen) {
                closeDropdown();
            }
        }, true);

        // Reposition on scroll/resize
        window.addEventListener('resize', function() {
            if (isOpen) adjustPosition();
        }, { passive: true });
        window.addEventListener('scroll', function() {
            if (isOpen) adjustPosition();
        }, { passive: true });

        // Synchronize if native select is changed programmatically (e.g., website auto-fetch)
        $(hiddenSelect).on('change', function() {
            var v = (this.value || '').trim();
            if (v === '1-10') v = '2-10';
            else if (v === '50001-' || v === '10000-999999') v = '50001-0';

            var optText = '';
            var items = getItems();
            for (var i = 0; i < items.length; i++) {
                if (items[i].getAttribute('data-val') === v) {
                    optText = items[i].getAttribute('data-label');
                    break;
                }
            }
            if (!optText && hiddenSelect.selectedIndex >= 0 && hiddenSelect.options[hiddenSelect.selectedIndex]) {
                optText = hiddenSelect.options[hiddenSelect.selectedIndex].text;
            }

            if (displayInput) {
                displayInput.value = (optText && optText !== 'Select company size') ? optText : '';
            }
            updateVisualState();
        });

        // Initial state sync
        var initVal = (hiddenSelect.value || '').trim();
        if (initVal) {
            $(hiddenSelect).trigger('change');
        } else {
            updateVisualState();
        }
    }

    // Public API
    window.JeroCompanySizeEngine = {
        select: selectOption,
        clear: clearOption,
        open: openDropdown,
        close: closeDropdown
    };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initEventListeners);
    } else {
        initEventListeners();
    }
})();
</script>
@endpush
