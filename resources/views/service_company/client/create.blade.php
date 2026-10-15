@extends('layouts.service_company_layout')
@push('head-script')
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

body {overflow-x:hidden; width:100%; font-family: Poppins, sans-serif;}
#kt_content {background: var(--client-page);}
#kt_toolbar {background: var(--client-page); padding-top: 14px;}
#kt_toolbar .page-title {align-items: flex-start !important;}
#kt_toolbar .page-title h1,
#kt_toolbar .page-title h1 a {font-size: 16px !important; line-height: 1.25; color: var(--client-ink) !important;}
#kt_subheader_total {font-size: 12px; color: var(--client-muted) !important; border: 0 !important; margin: 2px 0 0 !important; font-weight: 400 !important;}
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
    grid-template-columns: repeat(5, minmax(0, 1fr));
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
  #kt_wizard .wizard-title {font-size: 12.5px !important; font-weight: 600 !important; color: #334155 !important; margin: 0 !important; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;}
  #kt_wizard .wizard-title::after {display: block; margin-top: 2px; color: #64748b; font-size: 10.5px; font-weight: 400;}
  #kt_wizard .wizard-step:nth-child(1) .wizard-title::after {content: 'Basic details';}
  #kt_wizard .wizard-step:nth-child(2) .wizard-title::after {content: 'About the client';}
  #kt_wizard .wizard-step:nth-child(3) .wizard-title::after {content: 'Location & address';}
  #kt_wizard .wizard-step:nth-child(4) .wizard-title::after {content: 'Key contacts';}
  #kt_wizard .wizard-step:nth-child(5) .wizard-title::after {content: 'Confirm & save';}
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
  #kt_wizard > .row {margin: 14px 0 0 !important; padding: 0 !important;}
  #kt_wizard > .row > div {padding: 0 !important;}
  #kt_form > [data-wizard-type="step-content"] {
    background: var(--client-surface);
    border: 1px solid var(--client-border);
    border-radius: 12px;
    padding: 20px !important;
    min-height: 330px;
    box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.05);
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
  .client-step-layout {display: grid; width: 100%; max-width: 100%; min-width: 0; grid-template-columns: minmax(0, 1fr) 280px; gap: 14px; align-items: stretch;}
  .client-main-column {min-width: 0;}
  .client-side-column {min-width: 0;}
  .client-section-card {background: #fff; border: 1px solid var(--client-border); border-radius: 12px; padding: 20px; margin-bottom: 16px; box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.05);}
  .client-main-column .client-section-card:last-child {margin-bottom: 0;}
  .client-section-heading {font-size: 13px; font-weight: 600; color: var(--client-ink); margin: 0 0 3px;}
  .client-section-copy {font-size: 10.5px; color: var(--client-muted); margin: 0 0 15px;}
  .client-branding-grid {display: grid; width: 100%; min-width: 0; grid-template-columns: 190px 130px minmax(0, 1fr); gap: 16px; align-items: stretch;}
  .client-logo-upload {border: 1px dashed #ffd428; border-radius: 8px; background: #fffdf5; min-height: 152px; padding: 14px; display: flex; cursor: pointer; flex-direction: column; align-items: center; justify-content: center; text-align: center; transition: border-color .2s ease, background .2s ease;}
  .client-logo-upload:hover {border-color: #d4a017; background: #fffbeb;}
  .client-upload-icon {display: grid; place-items: center; width: 42px; height: 42px; margin-bottom: 6px; border-radius: 50%; background: #fff8d6; color: #d4a017; font-size: 23px;}
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
  .client-auto-fetch {flex: 0 0 auto; max-width: 145px; border: 0 !important; border-left: 1px solid #e2e8f0 !important; border-radius: 0 !important; color: #10143b !important; background: #ffd428 !important; white-space: nowrap; font-size: 10.5px !important; font-weight: 600; padding: 0 10px !important; transition: background 0.2s;}
  .client-auto-fetch:hover {background: #f5c400 !important;}
  .client-assistant {min-width: 0; overflow: hidden; border: 1px solid #e0e4f5; border-radius: 10px; padding: 18px; min-height: 100%; background: linear-gradient(145deg, #fbfaff 0%, #f4f6ff 100%); box-shadow: 0 2px 8px rgba(17, 28, 68, 0.04);}
  .client-assistant-head {display: flex; justify-content: space-between; gap: 12px; align-items: flex-start; margin-bottom: 15px;}
  .client-assistant-title {font-size: 13px; font-weight: 600; color: #4c38ed; margin: 0;}
  .client-beta {padding: 3px 8px; border-radius: 12px; background: #e9e4ff; color: #6a47f5; font-size: 9.5px; font-weight: 600;}
  .client-assistant-copy {font-size: 10.5px; line-height: 1.5; color: #53607e; margin: 5px 0 0;}
  .client-assistant-spark {display: grid; flex: 0 0 48px; width: 48px; height: 48px; place-items: center; border-radius: 10px; background: #fff; color: #6246ea; font-size: 23px; box-shadow: 0 8px 18px rgba(78, 56, 237, 0.12);}
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

  @media (max-width: 1199px) {
    #kt_wizard .wizard-title::after {display: none;}
    #kt_wizard .wizard-step {padding-right: 16px;}
    #kt_wizard .wizard-step:not(:last-child)::after {right: 4px; width: 10px;}
    .client-step-layout {grid-template-columns: minmax(0, 1fr) 240px;}
    .client-branding-grid {grid-template-columns: 170px 120px minmax(0, 1fr); gap: 12px;}
  }
  @media (max-width: 991px) {
    .client-step-layout {grid-template-columns: 1fr;}
    .client-assistant {min-height: auto;}
  }
  @media (max-width: 767px) {
    #kt_wizard .wizard-steps {display: flex !important; overflow-x: auto; padding: 10px !important;}
    #kt_wizard .wizard-step {min-width: 155px;}
    .client-branding-grid, .client-brand-fields, .client-details-grid {grid-template-columns: 1fr;}
    .client-logo-upload {min-height: 130px;}
    .client-logo-preview {min-height: 140px;}
    .client-profile-completion {display: none;}
    #kt_form > .d-flex.border-top {align-items: stretch; flex-direction: column-reverse;}
    .client-action-left, .client-action-right {justify-content: flex-end;}
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
  .client-editor-wrap {border: 1px solid var(--client-border); border-radius: 8px; overflow: hidden; background: #fff;}
  .client-editor-toolbar {display: flex; align-items: center; justify-content: space-between; padding: 8px 12px; border-bottom: 1px solid #edf0f5; background: #fafbfd;}
  .client-editor-toolbar span {font-size: 10.5px; color: var(--client-muted);}
  .client-char-count {font-size: 10px; color: #7b8497;}
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
  .client-info-banner i {color: var(--client-primary); font-size: 15px; margin-top: 1px;}
  .client-address-card {border: 1px solid var(--client-border); border-radius: 10px; padding: 16px; margin-bottom: 12px; background: #fff;}
  .client-address-card.is-additional {background: #fafbfd;}
  .client-address-card-head {display: flex; align-items: center; justify-content: space-between; margin-bottom: 12px;}
  .client-address-card-head h4 {font-size: 12px; font-weight: 600; margin: 0; color: var(--client-ink);}
  .client-address-empty {text-align: center; padding: 28px 16px; color: #7b8497;}
  .client-address-empty i {font-size: 36px; color: #c5cfe0; display: block; margin-bottom: 8px;}
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
  .client-drawer-head h4 {margin: 0 0 3px; font-size: 17px; font-weight: 700; color: #0f1940; line-height: 1.3;}
  .client-drawer-head-subtitle {margin: 0; font-size: 12px; color: #7889a8; line-height: 1.5;}
  .client-drawer-close-btn {
    flex-shrink: 0; width: 34px; height: 34px; border-radius: 8px;
    border: 1px solid #e2e8f4; background: #f5f7fb;
    display: inline-flex; align-items: center; justify-content: center;
    cursor: pointer; color: #64748b; font-size: 15px; padding: 0;
    transition: background .15s, border-color .15s, color .15s; margin-top: 1px;
  }
  .client-drawer-close-btn:hover {background: #fff0f2; border-color: #fca5a5; color: #dc2626;}

  /* Body */
  .client-drawer-body {padding: 24px; overflow-y: auto; flex: 1;}

  /* Fields */
  .client-drawer-field {margin-bottom: 18px;}
  .client-drawer-field .cdf-label {
    display: block; margin-bottom: 7px;
    font-size: 13px; font-weight: 600; color: #0f1940; line-height: 1;
  }
  .client-drawer-field .cdf-label .cdf-required {color: #e5484d; margin-left: 2px;}

  /* Input wrapper */
  .cdf-input-wrap {position: relative; display: flex; align-items: center;}
  .cdf-input-icon {
    position: absolute; left: 13px; top: 50%; transform: translateY(-50%);
    font-size: 16px; color: #94a3b8; pointer-events: none; line-height: 1; z-index: 2;
  }
  .cdf-input-wrap input {
    width: 100%; height: 44px;
    padding: 0 14px 0 40px;
    border: 1px solid #d8e2f0; border-radius: 8px;
    background: #fff; color: #1e2d4f;
    font-size: 13px; font-family: inherit;
    outline: none; transition: border-color .15s, box-shadow .15s;
    -webkit-appearance: none;
  }
  .cdf-input-wrap input::placeholder {color: #94a3b8; font-size: 12.5px;}
  .cdf-input-wrap input:focus {
    border-color: #2563eb;
    box-shadow: 0 0 0 3px rgba(37,99,235,.10);
  }
  .cdf-input-wrap input.is-invalid {border-color: #ef4444;}
  .cdf-input-wrap input.is-invalid:focus {border-color: #ef4444; box-shadow: 0 0 0 3px rgba(239,68,68,.10);}
  .cdf-error {display: none; margin-top: 5px; font-size: 11.5px; color: #dc2626; line-height: 1.4;}
  .cdf-error.visible {display: block;}

  /* Footer */
  .client-drawer-foot {
    padding: 16px 24px; border-top: 1px solid #edf0f7;
    display: flex; align-items: center; justify-content: space-between; gap: 10px; flex-shrink: 0;
    background: #fff;
  }
  .client-drawer-foot .cdf-cancel-btn {
    height: 42px; padding: 0 20px; border-radius: 8px;
    border: 1px solid #d0d8e8; background: #fff;
    color: #0f1940; font-size: 13px; font-weight: 600; cursor: pointer;
    transition: border-color .15s, background .15s;
  }
  .client-drawer-foot .cdf-cancel-btn:hover {background: #f5f7fb; border-color: #b6c2d6;}
  .client-drawer-foot .cdf-save-btn {
    height: 42px; padding: 0 22px; border-radius: 8px;
    background: var(--client-primary); border: none;
    color: #fff; font-size: 13px; font-weight: 600; cursor: pointer;
    display: inline-flex; align-items: center; gap: 7px;
    transition: background .15s, opacity .15s;
  }
  .client-drawer-foot .cdf-save-btn:hover {background: #1e4fd4;}
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
  .client-almost-done i {color: #10a66a; font-size: 22px; margin-bottom: 6px;}
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
  /* Strict Button Visibility: Update Client / Submit Client MUST ONLY show on Step 5 (Review & Submit) */
  #submitBtn, .submitfrm, .updatefrm {
    display: none !important;
  }
  body.on-review-step #submitBtn,
  body.on-review-step .submitfrm,
  body.on-review-step .updatefrm,
  [data-wizard-state="last"] #submitBtn,
  [data-wizard-state="last"] .submitfrm,
  [data-wizard-state="last"] .updatefrm {
    display: inline-flex !important;
  }
  body.on-review-step #continueButton,
  body.on-review-step [data-wizard-type="action-next"],
  [data-wizard-state="last"] #continueButton,
  [data-wizard-state="last"] [data-wizard-type="action-next"] {
    display: none !important;
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
    font-size: 16px;
    flex-shrink: 0;
  }
</style>
@endpush
@section('content')
<?php 
     use App\Helper\GeneralMethod;
     $country_code =  GeneralMethod::countryCodeFun();
   
     $background_image		=	url('img/client-default.png'); 
     $name	=	$website	=	$industry	=	$company_sizes	=	$company_type	=	$founded	= $description = $tags	= $client_location_arr = $client_contact_arr =  '';
		 if($page_event == 'ADD'){
			$pagetitle	=	'Add Client';
      $countAddrees= 1;
		 }else if($page_event == 'EDIT'){
			$pagetitle	=	'Edit Client';

     	$name					= @$client->name;
     	$website				= @$client->website;
     	$industry				= @$client->industry;
     	$company_sizes			= @$client->company_size_from . '-' . @$client->company_sizes;
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
     	
     }
     
?>
<!--begin::Toolbar-->
<div class="toolbar" id="kt_toolbar">
  <div id="kt_toolbar_container" class="container-fluid d-flex flex-stack">
    <div data-kt-swapper="true" data-kt-swapper-mode="prepend" data-kt-swapper-parent="{default: '#kt_content_container', 'lg': '#kt_toolbar_container'}" class="page-title d-flex align-items-center flex-wrap me-3 mb-5 mb-lg-0">
      <div class="d-flex flex-column">
        <h1 class="d-flex align-items-center text-dark fw-bolder fs-3 my-1"><a href="javascript:void(0);" class="text-gray-800 mb-1"><?php echo @$pagetitle; ?></a></h1>
        <span class="text-dark-800 fw-bold" id="kt_subheader_total"><?php echo $page_event === 'EDIT' ? 'Update the client profile, contacts and business details.' : 'Create a new internal client profile to manage jobs, contacts and interactions.'; ?></span>
      </div>
    </div>
    <div class="client-profile-completion" aria-live="polite">
      <div class="client-completion-copy">
        <span class="client-completion-label">Profile Completion</span>
        <span class="client-completion-status"><i class="la la-check-circle" aria-hidden="true"></i> Details saved as you continue</span>
      </div>
      <div class="client-completion-ring" id="client_completion_ring" role="img" aria-label="Profile completion 0 percent">
        <span id="client_completion_percent">0%</span>
      </div>
    </div>
  </div>
</div>
<!--end::Toolbar-->
<!--begin::Content-->
<div class="content d-flex flex-column flex-column-fluid pt-4 pb-5" id="kt_content">
  <!--begin::Entry-->
  <div class="d-flex flex-column-fluid">
    <!--begin::Container-->
    <div class="container-fluid px-3">
      <!--Card-->
      <div class="card card-custom client-wizard-shell">
        <div class="card-body p-0">
          <!--begin::Wizard-->
          <div class="wizard wizard-1" id="kt_wizard" data-wizard-state="first" data-wizard-clickable="false">
            <!--begin::Wizard Nav-->
            <div class="wizard-nav border-bottom">
              <div class="wizard-steps p-8 p-lg-10">
                <!--begin::Client Information-->
                <div class="wizard-step" data-wizard-type="step" data-wizard-state="current" data-wizard-process="1">
                  <div class="wizard-label">
                    <!-- <i class="wizard-icon flaticon-bus-stop"></i> --><!--begin::Svg Icon-->
                    <span class="svg-icon svg-icon-4x wizard-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                      <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                        <rect x="0" y="0" width="24" height="24"/>
                        <path d="M12.2674799,18.2323597 L12.0084872,5.45852451 C12.0004303,5.06114792 12.1504154,4.6768183 12.4255037,4.38993949 L15.0030167,1.70195304 L17.5910752,4.40093695 C17.8599071,4.6812911 18.0095067,5.05499603 18.0083938,5.44341307 L17.9718262,18.2062508 C17.9694575,19.0329966 17.2985816,19.701953 16.4718324,19.701953 L13.7671717,19.701953 C12.9505952,19.701953 12.2840328,19.0487684 12.2674799,18.2323597 Z" fill="#000000" fill-rule="nonzero" transform="translate(14.701953, 10.701953) rotate(-135.000000) translate(-14.701953, -10.701953) "/>
                        <path d="M12.9,2 C13.4522847,2 13.9,2.44771525 13.9,3 C13.9,3.55228475 13.4522847,4 12.9,4 L6,4 C4.8954305,4 4,4.8954305 4,6 L4,18 C4,19.1045695 4.8954305,20 6,20 L18,20 C19.1045695,20 20,19.1045695 20,18 L20,13 C20,12.4477153 20.4477153,12 21,12 C21.5522847,12 22,12.4477153 22,13 L22,18 C22,20.209139 20.209139,22 18,22 L6,22 C3.790861,22 2,20.209139 2,18 L2,6 C2,3.790861 3.790861,2 6,2 L12.9,2 Z" fill="#000000" fill-rule="nonzero" opacity="0.3"/>
                      </g>
                    </svg>
                    </span>
                    <!--end::Svg Icon-->
                    <h3 class="wizard-title">1. Client Information</h3>
                  </div>
                  <span class="svg-icon svg-icon-xl wizard-arrow">
                  <!--begin::Svg Icon | path:/metronic/theme/html/demo1/dist/assets/media/svg/icons/Navigation/Arrow-right.svg-->
                  <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                      <polygon points="0 0 24 0 24 24 0 24"></polygon>
                      <rect fill="#000000" opacity="0.3" transform="translate(12.000000, 12.000000) rotate(-90.000000) translate(-12.000000, -12.000000)" x="11" y="5" width="2" height="14" rx="1"></rect>
                      <path d="M9.70710318,15.7071045 C9.31657888,16.0976288 8.68341391,16.0976288 8.29288961,15.7071045 C7.90236532,15.3165802 7.90236532,14.6834152 8.29288961,14.2928909 L14.2928896,8.29289093 C14.6714686,7.914312 15.281055,7.90106637 15.675721,8.26284357 L21.675721,13.7628436 C22.08284,14.136036 22.1103429,14.7686034 21.7371505,15.1757223 C21.3639581,15.5828413 20.7313908,15.6103443 20.3242718,15.2371519 L15.0300721,10.3841355 L9.70710318,15.7071045 Z" fill="#000000" fill-rule="nonzero" transform="translate(14.999999, 11.999997) scale(1, -1) rotate(90.000000) translate(-14.999999, -11.999997)"></path>
                    </g>
                  </svg>
                  <!--end::Svg Icon-->
                  </span> </div>
                <!--end::Client Information-->
                <!--begin::Client Description-->
                <div class="wizard-step" data-wizard-type="step" data-wizard-state="pending" data-wizard-process="2">
                  <div class="wizard-label">
                    <!-- <i class="wizard-icon flaticon-list"></i> --><!--begin::Svg Icon-->
                    <span class="svg-icon svg-icon-4x wizard-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                      <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                        <rect x="0" y="0" width="24" height="24" />
                        <path d="M11,20 L11,17 C11,16.4477153 11.4477153,16 12,16 C12.5522847,16 13,16.4477153 13,17 L13,20 L15.5,20 C15.7761424,20 16,20.2238576 16,20.5 C16,20.7761424 15.7761424,21 15.5,21 L8.5,21 C8.22385763,21 8,20.7761424 8,20.5 C8,20.2238576 8.22385763,20 8.5,20 L11,20 Z" fill="#000000" opacity="0.3" />
                        <path d="M3,5 L21,5 C21.5522847,5 22,5.44771525 22,6 L22,16 C22,16.5522847 21.5522847,17 21,17 L3,17 C2.44771525,17 2,16.5522847 2,16 L2,6 C2,5.44771525 2.44771525,5 3,5 Z M4.5,8 C4.22385763,8 4,8.22385763 4,8.5 C4,8.77614237 4.22385763,9 4.5,9 L13.5,9 C13.7761424,9 14,8.77614237 14,8.5 C14,8.22385763 13.7761424,8 13.5,8 L4.5,8 Z M4.5,10 C4.22385763,10 4,10.2238576 4,10.5 C4,10.7761424 4.22385763,11 4.5,11 L7.5,11 C7.77614237,11 8,10.7761424 8,10.5 C8,10.2238576 7.77614237,10 7.5,10 L4.5,10 Z" fill="#000000" />
                      </g>
                    </svg>
                    </span>
                    <!--end::Svg Icon-->
                    <h3 class="wizard-title">2. Client Description</h3>
                  </div>
                  <span class="svg-icon svg-icon-xl wizard-arrow">
                  <!--begin::Svg Icon | path:/metronic/theme/html/demo1/dist/assets/media/svg/icons/Navigation/Arrow-right.svg-->
                  <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                      <polygon points="0 0 24 0 24 24 0 24"></polygon>
                      <rect fill="#000000" opacity="0.3" transform="translate(12.000000, 12.000000) rotate(-90.000000) translate(-12.000000, -12.000000)" x="11" y="5" width="2" height="14" rx="1"></rect>
                      <path d="M9.70710318,15.7071045 C9.31657888,16.0976288 8.68341391,16.0976288 8.29288961,15.7071045 C7.90236532,15.3165802 7.90236532,14.6834152 8.29288961,14.2928909 L14.2928896,8.29289093 C14.6714686,7.914312 15.281055,7.90106637 15.675721,8.26284357 L21.675721,13.7628436 C22.08284,14.136036 22.1103429,14.7686034 21.7371505,15.1757223 C21.3639581,15.5828413 20.7313908,15.6103443 20.3242718,15.2371519 L15.0300721,10.3841355 L9.70710318,15.7071045 Z" fill="#000000" fill-rule="nonzero" transform="translate(14.999999, 11.999997) scale(1, -1) rotate(90.000000) translate(-14.999999, -11.999997)"></path>
                    </g>
                  </svg>
                  <!--end::Svg Icon-->
                  </span> </div>
                <!--end::Client Description-->
                <!--begin::Address Details-->
                <div class="wizard-step" data-wizard-type="step" data-wizard-state="pending" data-wizard-process="3">
                  <div class="wizard-label">
                    <!-- <i class="wizard-icon flaticon-responsive"></i> -->
                    <!--begin::Svg Icon | path:assets/media/svg/icons/Home/Globe.svg-->
                    <span class="svg-icon svg-icon-4x wizard-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                      <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                        <rect x="0" y="0" width="24" height="24" />
                        <path d="M13,18.9450712 L13,20 L14,20 C15.1045695,20 16,20.8954305 16,22 L8,22 C8,20.8954305 8.8954305,20 10,20 L11,20 L11,18.9448245 C9.02872877,18.7261967 7.20827378,17.866394 5.79372555,16.5182701 L4.73856106,17.6741866 C4.36621808,18.0820826 3.73370941,18.110904 3.32581341,17.7385611 C2.9179174,17.3662181 2.88909597,16.7337094 3.26143894,16.3258134 L5.04940685,14.367122 C5.46150313,13.9156769 6.17860937,13.9363085 6.56406875,14.4106998 C7.88623094,16.037907 9.86320756,17 12,17 C15.8659932,17 19,13.8659932 19,10 C19,7.73468744 17.9175842,5.65198725 16.1214335,4.34123851 C15.6753081,4.01567657 15.5775721,3.39010038 15.903134,2.94397499 C16.228696,2.49784959 16.8542722,2.4001136 17.3003976,2.72567554 C19.6071362,4.40902808 21,7.08906798 21,10 C21,14.6325537 17.4999505,18.4476269 13,18.9450712 Z" fill="#000000" fill-rule="nonzero" />
                        <circle fill="#000000" opacity="0.3" cx="12" cy="10" r="6" />
                      </g>
                    </svg>
                    </span>
                    <!--end::Svg Icon-->
                    <h3 class="wizard-title">3. Address Details</h3>
                  </div>
                  <span class="svg-icon svg-icon-xl wizard-arrow">
                  <!--begin::Svg Icon | path:/metronic/theme/html/demo1/dist/assets/media/svg/icons/Navigation/Arrow-right.svg-->
                  <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                      <polygon points="0 0 24 0 24 24 0 24"></polygon>
                      <rect fill="#000000" opacity="0.3" transform="translate(12.000000, 12.000000) rotate(-90.000000) translate(-12.000000, -12.000000)" x="11" y="5" width="2" height="14" rx="1"></rect>
                      <path d="M9.70710318,15.7071045 C9.31657888,16.0976288 8.68341391,16.0976288 8.29288961,15.7071045 C7.90236532,15.3165802 7.90236532,14.6834152 8.29288961,14.2928909 L14.2928896,8.29289093 C14.6714686,7.914312 15.281055,7.90106637 15.675721,8.26284357 L21.675721,13.7628436 C22.08284,14.136036 22.1103429,14.7686034 21.7371505,15.1757223 C21.3639581,15.5828413 20.7313908,15.6103443 20.3242718,15.2371519 L15.0300721,10.3841355 L9.70710318,15.7071045 Z" fill="#000000" fill-rule="nonzero" transform="translate(14.999999, 11.999997) scale(1, -1) rotate(90.000000) translate(-14.999999, -11.999997)"></path>
                    </g>
                  </svg>
                  <!--end::Svg Icon-->
                  </span> </div>
                <!--end::Address Details-->
                <!--begin::Contact Person-->
                <div class="wizard-step" data-wizard-type="step" data-wizard-state="pending" data-wizard-process="4">
                  <div class="wizard-label">
                    <!-- <i class="wizard-icon flaticon-truck"></i> --><!--begin::Svg Icon-->
                    <span class="svg-icon svg-icon-4x wizard-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                      <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                        <polygon points="0 0 24 0 24 24 0 24"/>
                        <path d="M18,14 C16.3431458,14 15,12.6568542 15,11 C15,9.34314575 16.3431458,8 18,8 C19.6568542,8 21,9.34314575 21,11 C21,12.6568542 19.6568542,14 18,14 Z M9,11 C6.790861,11 5,9.209139 5,7 C5,4.790861 6.790861,3 9,3 C11.209139,3 13,4.790861 13,7 C13,9.209139 11.209139,11 9,11 Z" fill="#000000" fill-rule="nonzero" opacity="0.3"/>
                        <path d="M17.6011961,15.0006174 C21.0077043,15.0378534 23.7891749,16.7601418 23.9984937,20.4 C24.0069246,20.5466056 23.9984937,21 23.4559499,21 L19.6,21 C19.6,18.7490654 18.8562935,16.6718327 17.6011961,15.0006174 Z M0.00065168429,20.1992055 C0.388258525,15.4265159 4.26191235,13 8.98334134,13 C13.7712164,13 17.7048837,15.2931929 17.9979143,20.2 C18.0095879,20.3954741 17.9979143,21 17.2466999,21 C13.541124,21 8.03472472,21 0.727502227,21 C0.476712155,21 -0.0204617505,20.45918 0.00065168429,20.1992055 Z" fill="#000000" fill-rule="nonzero"/>
                      </g>
                    </svg>
                    </span>
                    <!--end::Svg Icon-->
                    <h3 class="wizard-title">4. Contact Person</h3>
                  </div>
                  <span class="svg-icon svg-icon-xl wizard-arrow">
                  <!--begin::Svg Icon | path:/metronic/theme/html/demo1/dist/assets/media/svg/icons/Navigation/Arrow-right.svg-->
                  <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                      <polygon points="0 0 24 0 24 24 0 24"></polygon>
                      <rect fill="#000000" opacity="0.3" transform="translate(12.000000, 12.000000) rotate(-90.000000) translate(-12.000000, -12.000000)" x="11" y="5" width="2" height="14" rx="1"></rect>
                      <path d="M9.70710318,15.7071045 C9.31657888,16.0976288 8.68341391,16.0976288 8.29288961,15.7071045 C7.90236532,15.3165802 7.90236532,14.6834152 8.29288961,14.2928909 L14.2928896,8.29289093 C14.6714686,7.914312 15.281055,7.90106637 15.675721,8.26284357 L21.675721,13.7628436 C22.08284,14.136036 22.1103429,14.7686034 21.7371505,15.1757223 C21.3639581,15.5828413 20.7313908,15.6103443 20.3242718,15.2371519 L15.0300721,10.3841355 L9.70710318,15.7071045 Z" fill="#000000" fill-rule="nonzero" transform="translate(14.999999, 11.999997) scale(1, -1) rotate(90.000000) translate(-14.999999, -11.999997)"></path>
                    </g>
                  </svg>
                  <!--end::Svg Icon-->
                  </span> </div>
                <!--end::Contact Person-->
                <!--begin::Review and Submit-->
                <div class="wizard-step" data-wizard-type="step" data-wizard-state="pending" data-wizard-process="5">
                  <div class="wizard-label">
                    <!-- <i class="wizard-icon flaticon-globe"></i> -->
                    <!--begin::Svg Icon | path:assets/media/svg/icons/General/Notification2.svg-->
                    <span class="svg-icon svg-icon-4x wizard-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                      <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                        <rect x="0" y="0" width="24" height="24" />
                        <path d="M13.2070325,4 C13.0721672,4.47683179 13,4.97998812 13,5.5 C13,8.53756612 15.4624339,11 18.5,11 C19.0200119,11 19.5231682,10.9278328 20,10.7929675 L20,17 C20,18.6568542 18.6568542,20 17,20 L7,20 C5.34314575,20 4,18.6568542 4,17 L4,7 C4,5.34314575 5.34314575,4 7,4 L13.2070325,4 Z" fill="#000000" />
                        <circle fill="#000000" opacity="0.3" cx="18.5" cy="5.5" r="2.5" />
                      </g>
                    </svg>
                    </span>
                    <!--end::Svg Icon-->
                    <h3 class="wizard-title">5. Review &amp; Submit</h3>
                  </div>
                </div>
                <!--end::Review and Submit-->
              </div>
            </div>
            <!--end::Wizard Nav-->
            <!--begin::Wizard Body-->
            <div class="row justify-content-center my-10 px-8 my-lg-15 px-lg-10">
              <div class="col-xl-12 col-xxl-12">
                <!--begin::Wizard Form-->
                <form class="form mx-auto" novalidate="novalidate" action="javascript:void(0);" method="post" id="kt_form" name="kt_form">
                  <meta name="csrf-token" content="{{ csrf_token() }}" />
                  <input type="hidden" name="idempotency_key" id="kt_form_idempotency_key" value="" />
                  <!--begin::Client Information-->
                  @include('service_company.client.components.tab-basic-info')

                  <!--begin::Client Description-->
                  @include('service_company.client.components.tab-details')

                  <!--begin::Address Details-->
                  @include('service_company.client.components.tab-locations')

                  <!--begin::Contact Person-->
                  @include('service_company.client.components.tab-contacts', [
    'client_contact_info' => $client_contact_info ?? [],
    'client_contact_arr'  => $client_contact_arr ?? ($client_contact_info ?? []),
    'page_event'          => $page_event,
    'country_code'        => $country_code ?? 'in'
])

                  <!--begin::Review and Submit-->
                  @include('service_company.client.components.tab-review')
                  <!--begin::Wizard Actions (Docked Sticky Footer Bar)-->
                  <div class="client-wizard-sticky-footer d-flex align-items-center justify-content-end">
                    <div class="client-action-right d-flex align-items-center gap-3">
                      <a href="{{ route('service_company.clients.index') }}" class="btn btn-secondary client-nav-cancel">
                        <i class="la la-times" style="margin-right: 4px;"></i> Cancel
                      </a>
                      <button type="button" class="btn btn-secondary client-nav-back" id="client_btn_back" style="display: none;">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width: 15px; height: 15px; margin-right: 6px; display: inline-block; vertical-align: -2px;">
                          <path d="M5 12h14"/><path d="m12 19-7-7 7-7"/>
                        </svg>Back
                      </button>
<?php
if ($page_event == 'EDIT') {
    echo '<button type="button" id="submitBtn" class="btn btn-primary client-submit-btn updatefrm" data-wizard-type="action-submit1" style="display:none;"><i class="la la-paper-plane" style="margin-right: 6px;"></i> Update Client</button>';
} else {
    echo '<button type="button" id="submitBtn" class="btn btn-primary client-submit-btn submitfrm" data-wizard-type="action-submit1" style="display:none;"><i class="la la-paper-plane" style="margin-right: 6px;"></i> Submit Client</button>';
}
?>
                      <button type="button" id="continueButton" class="btn btn-primary client-nav-next">
                        Continue
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width: 15px; height: 15px; margin-left: 6px; display: inline-block; vertical-align: -2px;">
                          <path d="M5 12h14"/><path d="m12 5 7 7-7 7"/>
                        </svg>
                      </button>
                    </div>
                  </div>
                  <!--end::Wizard Actions-->
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

<script src="{{asset('demo/plugins/custom/tinymce/tinymce.bundle.js')}}"></script>
<script src="{{asset('demo/dist/assets/js/custom/documentation/editors/tinymce/hidden.js')}}"></script>

<script src="https://unpkg.com/@yaireo/tagify"></script>
<script src="https://unpkg.com/@yaireo/tagify/dist/tagify.polyfills.min.js"></script>
<link href="https://unpkg.com/@yaireo/tagify/dist/tagify.css" rel="stylesheet" type="text/css" />

<script>

  $(document).on('input', 'input[name="cp_number[]"]', function () {

      // Allow only numbers
      this.value = this.value.replace(/\D/g, '');

      // Restrict to 15 digits (ITU-T E.164 standard)
      if (this.value.length > 15) {
          this.value = this.value.slice(0, 15);
      }

  });

  $(document).on("change", 'input[name="cp_email[]"]', function () {

      let input  = $(this);                 // current input
      let email  = input.val();

      if (email !== '') {

          $('.page-loader').css('display', 'flex');

          $.ajax({
              url: "{{ route('valid-email-chk') }}",
              type: "POST",
              data: {
                  _token: "{{ csrf_token() }}",
                  email: email
              },

              success: function (response) {

                  $('.page-loader').hide();

                  if (response.status === 'fail') {

                      input.val('');

                      Swal.fire({
                          text: "Sorry, Email [" + email + "] is already registered, Please use another email.",
                          icon: "error",
                          confirmButtonText: "Ok, got it!",
                          customClass: { confirmButton: "btn btn-light" }
                      });

                  } else if (response.status === 'emailchecker') {

                      input.val('');

                      Swal.fire({
                          text: response.message,
                          icon: "error",
                          confirmButtonText: "Ok, got it!",
                          customClass: { confirmButton: "btn btn-light" }
                      });

                  } else {

                      // ✅ valid email
                      $('.verification_email').val(email);
                  }
              },

              error: function (xhr) {
                  $('.page-loader').hide();

                  if (xhr.status === 0 || !navigator.onLine) {
                      Swal.fire({ title: "No Internet", text: "You are currently offline or the server is unreachable.", icon: "warning", confirmButtonText: "Ok, got it!", customClass: { confirmButton: "btn btn-warning" } });
                  } else {
                      Swal.fire({ text: "Server error, please try again", icon: "error" });
                  }
              }
          });
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
   			
   			$('.client-cperson-row').last().append('<div class="row client-cperson-container" data-contact-index="'+contactPersonId+'"><span class="form-text text-muted form-label fw-bolder countContact" style="position: absolute; top: 0;"><b>Contact Person Info: <span class="count_class">'+countAddressFinal+'</span> </b></span><div class="col-xl-3"><div class="form-group mb-0 mt-7 client-cperson-container-child" id="remove"><div class="input-group"><input type="text" name="cp_name[]" id="cp_name_'+contactPersonId+'" class="form-control form-control-lg form-control-solid rounded w-100" placeholder="Enter person name"><span class="form-text text-muted">Please enter contact person name</span></div></div></div><div class="col-xl-3"><div class="form-group mb-0 mt-7 client-cperson-container-child" id="remove"><div class="input-group"><input type="email" name="cp_email[]" id="cp_email_'+contactPersonId+'" class="form-control form-control-lg form-control-solid rounded w-100" placeholder="Enter email"><span class="form-text text-muted">Please enter contact email</span></div></div></div><div class="col-xl-3"><div class="form-group mb-0 mt-7 client-cperson-container-child" id="remove"><div class="input-group"><input type="number" name="cp_number[]" id="cp_number_'+contactPersonId+'" class="form-control form-control-lg form-control-solid rounded w-100" placeholder="Enter contact number"><input type="hidden" value="{{ $country_code }}" class="calling_code" name="calling_code[]" id="calling_code_'+contactPersonId+'"><span class="form-text text-muted">Please enter contact number</span></div></div></div><div class="col-xl-3"><div class="form-group mb-0 mt-7 client-cperson-container-child" id="remove"><div class="input-group" style="width: 95%;"><input type="text" name="cp_desigination[]" id="cp_desigination_'+contactPersonId+'" class="form-control form-control-lg form-control-solid rounded cp_desigination" placeholder="Enter desigination"><span class="form-text text-muted">Please enter contact desigination</span></div><i class="fa fa-close" style="position: absolute;font-size: 33px;color:red;top: 26px;right: 0;cursor: pointer;"></i></div></div></div></div>');
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



   // Service Offerings Tagify — initialised inside document.ready so Tagify lib is guaranteed loaded
   $(document).ready(function () {
     var tagsEl = document.querySelector('input[name="tags"]');
     if (!tagsEl) return;

     // Prevent double-init if something else already ran Tagify on this element
     if (!tagsEl._tagify) {
       window.clientTagsTagify = new Tagify(tagsEl, {
         delimiters: ',',
         editTags: false,
         maxTags: 30
       });
     } else {
       window.clientTagsTagify = tagsEl._tagify;
     }

     // Add a stable CSS hook to the Tagify container element
     if (window.clientTagsTagify && window.clientTagsTagify.DOM && window.clientTagsTagify.DOM.scope) {
       window.clientTagsTagify.DOM.scope.classList.add('client-service-tagify');
     }

     // Reflect currently-added tags onto the suggested chips (e.g. on EDIT load)
     function syncSuggestedHighlight() {
       var added = (window.clientTagsTagify.value || []).map(function (t) {
         return t.value.toLowerCase();
       });
       $('#client_suggested_services .client-suggested-tag').each(function () {
         var v = $(this).data('value') || '';
         $(this).toggleClass('is-added', added.indexOf(v.toLowerCase()) > -1);
       });
     }

     // When a tag is added or removed manually, keep chips in sync
     window.clientTagsTagify.on('add', function () {
       syncSuggestedHighlight();
       if (window.clientWizardUi) window.clientWizardUi.updateCompletion();
     });
     window.clientTagsTagify.on('remove', function () {
       syncSuggestedHighlight();
       if (window.clientWizardUi) window.clientWizardUi.updateCompletion();
     });

     syncSuggestedHighlight();

     // Suggested chip click — delegate from document so dynamic chips also work
     // pointer-events on inner <i> are off; the whole <span.client-suggested-tag> is the click target
     $(document).off('click.serviceChip').on('click.serviceChip', '.client-suggested-tag', function (e) {
       e.preventDefault();
       e.stopPropagation();
       var $chip = $(this);
       if ($chip.hasClass('is-added')) return;
       var value = $chip.data('value');
       if (!value || !window.clientTagsTagify) return;

       // Duplicate guard
       var existing = (window.clientTagsTagify.value || []).map(function (t) {
         return t.value.toLowerCase();
       });
       if (existing.indexOf(value.toLowerCase()) !== -1) {
         $chip.addClass('is-added');
         return;
       }

       window.clientTagsTagify.addTags([{ value: value }]);
       // syncSuggestedHighlight is called by the 'add' event listener above
     });
   });
   
   // ---------- GLOBAL INLINE ERROR UTILITIES ----------
   function clearFieldError($field) {
     if (!$field) return;
     var $f = ($field instanceof jQuery) ? $field : $($field);
     if (!$f.length) return;
     $f.removeClass('is-invalid');
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
                var card = elBtn.closest('.client-address-card, .client-address-container-child, .is-additional, .ij-location-card');
                if (card) card.remove();
            }
        }
        if (window.clientWizardUi && typeof window.clientWizardUi.syncAdditionalAddressEmpty === 'function') {
            window.clientWizardUi.syncAdditionalAddressEmpty();
        } else if (typeof syncAdditionalAddressEmpty === 'function') {
            syncAdditionalAddressEmpty();
        }
    }
    window.removeAddressCard = removeAddressCard;

    /* ===== BRANCH CITY AUTOCOMPLETE ENGINE (FOR ADDITIONAL ADDRESSES) ===== */
    var branchCitySearchAbortCtrl = {};

    function renderBranchCuratedDropdown(cid) {
        var dd = document.getElementById('branch_city_dropdown_' + cid);
        if (!dd) return;
        var currentVal = ($('#branch_city_search_' + cid).val() || '').toLowerCase().trim();
        var html = '';
        curatedLocationGroups.forEach(function(group) {
            html += '<div class="ij-cd-group">';
            html += '<div class="ij-cd-group-title">' + escapeHtml(group.region) + '</div>';
            group.items.forEach(function(item) {
                var isSelected = (currentVal && (item.city.toLowerCase() === currentVal || item.canonical.toLowerCase() === currentVal));
                var itemClass = 'ij-cd-item' + (isSelected ? ' is-selected' : '');
                var clickAttr = 'onclick="selectBranchCity(' + cid + ', \'' + escapeJsStr(item.canonical) + '\', \'' + escapeJsStr(item.city) + '\')"';
                var statusBadge = isSelected ? '<span class="ij-cd-status">✓ Selected</span>' : '';
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

    function renderBranchSearchResults(cid, results, query) {
        var dd = document.getElementById('branch_city_dropdown_' + cid);
        if (!dd) return;
        if (!results || results.length === 0) {
            var safeQ = escapeHtml(query);
            var safeJsQ = escapeJsStr(query);
            dd.innerHTML = '<div class="ij-cd-empty">' +
                '<div>No matching cities found for &ldquo;' + safeQ + '&rdquo;</div>' +
                '<button type="button" class="ij-btn-custom-add" onclick="selectBranchCity(' + cid + ', \'' + safeJsQ + '\', \'' + safeJsQ + '\')">' +
                '+ Set &ldquo;' + safeQ + '&rdquo; as branch city</button>' +
                '</div>';
            dd.classList.add('active');
            return;
        }
        var html = '<div class="ij-cd-group">';
        html += '<div class="ij-cd-group-title">Search Results (' + results.length + ')</div>';
        results.forEach(function(item) {
            var canonical = item.canonical || item.label || (item.city + (item.state ? ', ' + item.state : '') + (item.country ? ', ' + item.country : ''));
            var clickAttr = 'onclick="selectBranchCity(' + cid + ', \'' + escapeJsStr(canonical) + '\', \'' + escapeJsStr(item.city) + '\')"';
            var subText = item.state ? (item.state + ', ' + item.country) : (item.country || '');
            html += '<div class="ij-cd-item" ' + clickAttr + ' data-canonical="' + escapeHtml(canonical) + '">';
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
            html += '  </div>';
            html += '</div>';
        });
        html += '</div>';
        dd.innerHTML = html;
        dd.classList.add('active');
    }

    function performBranchCitySearch(cid, query) {
        var trimmed = (query || '').trim();
        var spinner = document.getElementById('branch_city_spinner_' + cid);
        var clearBtn = document.getElementById('branch_city_clear_' + cid);
        if (clearBtn) clearBtn.style.display = trimmed ? 'inline-flex' : 'none';

        if (!trimmed) {
            if (spinner) spinner.style.display = 'none';
            renderBranchCuratedDropdown(cid);
            return;
        }

        var lookupQuery = trimmed.toLowerCase();
        var normalizedQ = citySynonymAliases[lookupQuery] || trimmed;

        var cacheKey = normalizedQ.toLowerCase().trim();
        var cached = getCachedCities(cacheKey);
        if (cached) {
            if (spinner) spinner.style.display = 'none';
            renderBranchSearchResults(cid, cached, trimmed);
            return;
        }

        if (branchCitySearchAbortCtrl[cid]) {
            branchCitySearchAbortCtrl[cid].abort();
        }
        branchCitySearchAbortCtrl[cid] = new AbortController();

        if (spinner) spinner.style.display = 'inline-block';

        var targetUrl = "{{ url('/') }}" + "/city-job-suggestions?q=" + encodeURIComponent(normalizedQ) + "&format=canonical&limit=25";

        fetch(targetUrl, {
            method: 'GET',
            signal: branchCitySearchAbortCtrl[cid].signal,
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
            renderBranchSearchResults(cid, parsed, trimmed);
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
            renderBranchSearchResults(cid, filtered, trimmed);
        });
    }

    var debounceBranchCitySearch = debounce(function(cid, val) {
        performBranchCitySearch(cid, val);
    }, 180);

    function selectBranchCity(cid, canonical, label) {
        var clean = String(canonical || '').trim();
        var searchInput = document.getElementById('branch_city_search_' + cid);
        var hiddenInput = document.getElementById('kt_tagify_work_location_' + cid);
        var clearBtn = document.getElementById('branch_city_clear_' + cid);
        var dd = document.getElementById('branch_city_dropdown_' + cid);

        if (searchInput) searchInput.value = clean;
        if (hiddenInput) {
            var parts = clean.split(/[,|]/).map(function(s) { return s.trim(); });
            var formattedValue = parts.join(' | ');
            hiddenInput.value = JSON.stringify([{ value: formattedValue }]);
        }
        if (clearBtn) clearBtn.style.display = clean ? 'inline-flex' : 'none';
        if (dd) dd.classList.remove('active');
        clearFieldError($('#branch_city_search_' + cid));
    }

    function clearBranchCity(cid) {
        var searchInput = document.getElementById('branch_city_search_' + cid);
        var hiddenInput = document.getElementById('kt_tagify_work_location_' + cid);
        var clearBtn = document.getElementById('branch_city_clear_' + cid);
        var dd = document.getElementById('branch_city_dropdown_' + cid);

        if (searchInput) {
            searchInput.value = '';
            searchInput.focus();
        }
        if (hiddenInput) hiddenInput.value = '';
        if (clearBtn) clearBtn.style.display = 'none';
        renderBranchCuratedDropdown(cid);
    }

    window.selectBranchCity = selectBranchCity;
    window.clearBranchCity = clearBranchCity;
    window.performBranchCitySearch = performBranchCitySearch;
    window.renderBranchCuratedDropdown = renderBranchCuratedDropdown;
   	
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
   
   $.noConflict();
   
   var loadFile 	=	function(event) {
   		var output 		=	URL.createObjectURL(event.target.files[0]);
   		$('.image-input-wrapper').css('background-image', 'url(' + output + ')');
   };
   
</script>
<script>

        
    
   $( document ).ready(function() { 
       
       /* Delegated Chip Removal and Address Removal Listeners */
       $(document).on('click', '.ij-chip-del, .js-remove-city', function (e) {
           e.preventDefault();
           e.stopPropagation();
           var city = $(this).attr('data-city') || $(this).data('city');
           if (city && typeof removeCity === 'function') {
               removeCity(city, true);
           }
       });

       $(document).on('click', '.remove_address_single, .client-remove-address-btn', function (e) {
           e.preventDefault();
           e.stopPropagation();
           var target = $(this).attr('data-target') || $(this).data('target');
           if (typeof removeAddressCard === 'function') {
               removeAddressCard(this, target);
           }
       });

       /* Delegated Branch City Listeners (For dynamic additional addresses) */
       $(document).on('focus', '.branch-city-search', function () {
           var cid = $(this).data('cid');
           var val = $(this).val();
           if (!val || !val.trim()) {
               renderBranchCuratedDropdown(cid);
           } else {
               performBranchCitySearch(cid, val);
           }
       });

       $(document).on('input', '.branch-city-search', function () {
           var cid = $(this).data('cid');
           var val = $(this).val();
           var clearBtn = document.getElementById('branch_city_clear_' + cid);
           if (clearBtn) clearBtn.style.display = val.trim() ? 'inline-flex' : 'none';
           var hiddenInput = document.getElementById('kt_tagify_work_location_' + cid);
           if (hiddenInput) {
               var parts = val.split(/[,|]/).map(function(s) { return s.trim(); });
               hiddenInput.value = val.trim() ? JSON.stringify([{ value: parts.join(' | ') }]) : '';
           }
           debounceBranchCitySearch(cid, val);
       });

       $(document).on('click', '.branch-city-clear', function (e) {
           e.stopPropagation();
           var cid = $(this).data('cid');
           clearBranchCity(cid);
       });

       $(document).on('click', function (e) {
           if (!$(e.target).closest('.ij-location-input-wrap').length) {
               $('.branch-city-dropdown').removeClass('active');
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

    $('.add_address').click(function () {
        var addressId = parseInt($('#addressId').val(), 10) || 0;
        addressId++;
        $('#addressId').val(addressId);

        var cid = addressId;
        var additionalIndex = $('.client-address-container .client-address-card.is-additional').length + 1;

        // Instant Browser-Execution in User RAM (< 1ms, 0 server network roundtrip)
        // Uniform Design: Matches Default Address Details Card (Yellow Gradient Banner, Yellow Pin Badge, Magnifying Glass City Search, Textarea with Counter)
        var cardHtml = '<div class="card ij-location-card client-address-card is-additional client-address-container-child mt-3" id="clientAddr_' + cid + '">' +
            '<div class="card-body p-0">' +
            '<div class="ij-location-card-head">' +
            '<div class="ij-location-head-left">' +
            '<span class="ij-location-head-badge">' +
            '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">' +
            '<path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/>' +
            '<circle cx="12" cy="10" r="3"/>' +
            '</svg>' +
            '</span>' +
            '<div>' +
            '<h4 class="ij-location-title">Additional Address (' + additionalIndex + ')</h4>' +
            '<p class="ij-location-subtitle">Branch or regional office location and street address</p>' +
            '</div>' +
            '</div>' +
            '<button type="button" class="client-remove-address-btn remove_address_single" data-target="clientAddr_' + cid + '" onclick="removeAddressCard(this, \'clientAddr_' + cid + '\')">' +
            '<i class="la la-trash"></i> Remove' +
            '</button>' +
            '</div>' +
            '<div class="ij-location-card-body">' +
            '<div class="client-field fv-plugins-icon-container mb-3">' +
            '<label class="form-label required" for="branch_city_search_' + cid + '">Branch City</label>' +
            '<div class="input-wrapper ij-location-input-wrap">' +
            '<div class="input-with-icon">' +
            '<svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">' +
            '<circle cx="11" cy="11" r="8"/>' +
            '<path d="m21 21-4.3-4.3"/>' +
            '</svg>' +
            '<input type="text" class="form-input ij-location-search-input branch-city-search" id="branch_city_search_' + cid + '" data-cid="' + cid + '" value="" placeholder="Search branch city (e.g. Mumbai, Pune, London)..." autocomplete="off" spellcheck="false">' +
            '</div>' +
            '<span class="ij-search-spinner" id="branch_city_spinner_' + cid + '" style="display:none;" title="Searching..."></span>' +
            '<button type="button" class="ij-search-clear branch-city-clear" id="branch_city_clear_' + cid + '" style="display:none;" data-cid="' + cid + '" title="Clear city">&times;</button>' +
            '<div class="search-dropdown ij-city-dropdown branch-city-dropdown" id="branch_city_dropdown_' + cid + '"></div>' +
            '<input type="hidden" name="kt_tagify_work_location[]" id="kt_tagify_work_location_' + cid + '" value="">' +
            '</div>' +
            '<span class="form-text text-muted">Select or search for the city where this branch office is located.</span>' +
            '</div>' +
            '<div class="client-field fv-plugins-icon-container">' +
            '<label class="form-label required" for="address_' + cid + '">Full Address Details</label>' +
            '<textarea class="form-control form-control-solid client-address-textarea" name="address[]" rows="3" maxlength="500" placeholder="Building, street, suite/floor, area, landmark, pincode, etc." id="address_' + cid + '"></textarea>' +
            '<div class="d-flex justify-content-between mt-1">' +
            '<span class="form-text text-muted">Provide the complete and accurate branch office address.</span>' +
            '<span class="client-char-count"><span class="client-address-count" id="address_char_count_' + cid + '">0</span>/500</span>' +
            '</div>' +
            '</div>' +
            '</div>' +
            '</div>' +
            '</div>';

        $('#additional_addresses_container').append(cardHtml);

        if (window.clientWizardUi) {
            window.clientWizardUi.syncAdditionalAddressEmpty();
            window.clientWizardUi.bindAddressCounters(document.getElementById('clientAddr_' + cid));
        }
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
   			
   			var kt_docs_tinymce_hidden = tinymce.get("kt_docs_tinymce_hidden").getContent();
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
               								website: {
   														required: true,
   														minlength: 5
   													 }
           							}
       					});
   
   });   
   
   $(document).on('click', '.submitfrm', function() {
        var $btn = $(this);

   	var data 			=	new FormData();
   	var form_data 		=	$('#kt_form').serializeArray();

      $btn.prop("disabled", true);
      $btn.html(
        `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Waiting...`
      );


   	$.each(form_data, function (key, input) {
       		data.append(input.name, input.value);
   	});

   	var file_data 		= $('input[name="logo"]')[0].files;
   	for (var i = 0; i < file_data.length; i++) {
       		data.append("logo", file_data[i]);
   	}

	var kt_docs_tinymce_hidden = tinymce.get("kt_docs_tinymce_hidden").getContent();//CKEDITOR.instances.kt_docs_tinymce_hidden.getData();
    data.append("description", kt_docs_tinymce_hidden);

   	data.append('key', 'value');

    if (!navigator.onLine) {
        Swal.fire({ title: "No Internet", text: "You are currently offline or the server is unreachable.", icon: "warning", confirmButtonText: "Ok, got it!", customClass: { confirmButton: "btn btn-warning" } });
        $btn.prop("disabled", false).html('<i class="la la-paper-plane"></i> Submit Client');
        return;
    }

   	$.ajax({
       			url					: '{{route('service_company.clients.store')}}',
   				headers				: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
       			method				: "POST",
       			processData			: false,
       			contentType			: false,
      		 	data				: data,
   				  redirect			: true,
       			success				: function (data)
                {
                  if(data.status == 'success')
                  {
                     window.location = '{{ route('service_company.clients.index') }}'
                  }
       			},
       			error				: function (xhr) {
                    $btn.prop("disabled", false).html('<i class="la la-paper-plane"></i> Submit Client');
                    if (xhr.status === 0 || !navigator.onLine) {
                        Swal.fire({ title: "No Internet", text: "You are currently offline or the server is unreachable.", icon: "warning", confirmButtonText: "Ok, got it!", customClass: { confirmButton: "btn btn-warning" } });
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

   $(document).on('click', '.updatefrm', function() {
        var $btn = $(this);

    $btn.prop("disabled", true);
    $btn.html(
        `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Waiting...`
      );

   	var data 			=	new FormData();
   	var form_data 		=	$('#kt_form').serializeArray();
   	$.each(form_data, function (key, input) {
       		data.append(input.name, input.value);
   	});
   	var file_data 		= $('input[name="logo"]')[0].files;
   	for (var i = 0; i < file_data.length; i++) {
       		data.append("logo", file_data[i]);
   	}

	var kt_docs_tinymce_hidden = tinymce.get("kt_docs_tinymce_hidden").getContent();// CKEDITOR.instances.kt_docs_tinymce_hidden.getData();
	data.append("description", kt_docs_tinymce_hidden);

	data.append('key', 'value');
  // data.append('_method', 'PUT');

    if (!navigator.onLine) {
        Swal.fire({ title: "No Internet", text: "You are currently offline or the server is unreachable.", icon: "warning", confirmButtonText: "Ok, got it!", customClass: { confirmButton: "btn btn-warning" } });
        $btn.prop("disabled", false).html('<i class="la la-paper-plane"></i> Update Client');
        return;
    }

   	$.ajax({
   				url					: '{{route('service_company.clients.update', @$client->id)}}',
   				headers				: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
       			method				: "POST",
       			processData			: false,
       			contentType			: false,
      		 	data				: data,
   				redirect			: true,
       			success				: function (data)
               {
                  if(data.status == 'success')
                  {
                     window.location = '{{ route('service_company.clients.index') }}'
                  }
       			},
       			error				: function (xhr) {
                    $btn.prop("disabled", false).html('<i class="la la-paper-plane"></i> Update Client');
                    if (xhr.status === 0 || !navigator.onLine) {
                        Swal.fire({ title: "No Internet", text: "You are currently offline or the server is unreachable.", icon: "warning", confirmButtonText: "Ok, got it!", customClass: { confirmButton: "btn btn-warning" } });
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


  $(document).on('change', '#website', function() { 
      clientDomainRegisterChk();
  });


 
  // In-memory RAM cache for domain verification (prevents repeat network requests)
  window._clientDomainCheckCache = window._clientDomainCheckCache || {};
  window._lastCheckedWebsite = '';

  function clientDomainRegisterChk(){
    var website = ($('#website').val() || '').trim();
    if (!website) return false;

    // Skip redundant check if website hasn't changed and is already verified
    if (website === window._lastCheckedWebsite && window._clientDomainCheckCache[website]) {
        return false;
    }

    // Fast-path: Instant User RAM cache lookup (0ms latency, 0 server requests)
    if (window._clientDomainCheckCache[website]) {
        var response = window._clientDomainCheckCache[website];
        if (response.status === 'fail') {
            Swal.fire("Warning", response.message, "warning");
            $('.updatefrm').prop("disabled", true);
            $('#website').val('');
            if (formValidationInstance && typeof formValidationInstance.revalidateField === 'function') {
                formValidationInstance.revalidateField('website');
            }
        } else {
            $("#client_id").val(response.clientName);
            if (response.logo_url) {
                $('.image-input-wrapper').css('background-image', 'url(' + response.logo_url + ')');
            }
            if (window.clientWizardUi) {
                window.clientWizardUi.updateAssistantPanel();
                window.clientWizardUi.updateCompletion();
            }
        }
        return false;
    }

    $('.page-loader').css('display', 'flex');

    // Lean AJAX payload: sends only website and check_client_id (zero form/file serialization overhead)
    $.ajax({
        url: "{{route('client-domain-register-chk')}}",
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        type: "POST",
        data: {
            website: website,
            check_client_id: "{{ @$client->id }}"
        },
        success: function(response) {
            $('.page-loader').css('display', 'none');
            window._lastCheckedWebsite = website;
            window._clientDomainCheckCache[website] = response;

            if (response.status === 'fail') {
                Swal.fire("Warning", response.message, "warning");
                $('.updatefrm').prop("disabled", true);
                $('#website').val('');
                if (formValidationInstance && typeof formValidationInstance.revalidateField === 'function') {
                    formValidationInstance.revalidateField('website');
                }
            } else {
                $("#client_id").val(response.clientName);
                if (response.logo_url) {
                    $('.image-input-wrapper').css('background-image', 'url(' + response.logo_url + ')');
                }
                if (window.clientWizardUi) {
                    window.clientWizardUi.updateAssistantPanel();
                    window.clientWizardUi.updateCompletion();
                }
            }
        },
        error: function() {
            $('.page-loader').css('display', 'none');
        }
    });

    return false;
  }

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
    var checks = [
      $('#client_id').val(),
      $('#website').val(),
      $('#industry').val(),
      $('#company_sizes').val(),
      $('#company_type').val(),
      $('#founded').val(),
      (typeof tinymce !== 'undefined' && tinymce.get('kt_docs_tinymce_hidden')) ? tinymce.get('kt_docs_tinymce_hidden').getContent({ format: 'text' }).trim() : '',
      $('#tags').val(),
      $('textarea[name="address[]"]').first().val(),
      ((typeof selectedCities !== 'undefined' && selectedCities.length) ? 'loc_set' : $('input[name="kt_tagify_work_location[]"]').first().val()),
      $('input[name="cp_name[]"]').first().val()
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
    $(scope || document).find('.client-address-textarea').each(function () {
      var $textarea = $(this);
      var $counter = $textarea.closest('.client-field, .client-address-card').find('.client-address-count');
      var sync = function () {
        $counter.text(($textarea.val() || '').length);
      };
      $textarea.off('input.clientAddressCount').on('input.clientAddressCount', sync);
      sync();
    });
  }

  function syncAdditionalAddressEmpty() {
    var $cards = $('.client-address-container .client-address-container-child.is-additional, .client-address-container .client-address-card.is-additional');
    $('#client_additional_empty').toggle($cards.length === 0);
    $cards.each(function(idx) {
      $(this).find('.ij-location-title').text('Additional Address (' + (idx + 1) + ')');
    });
  }

  function getContactRowsData() {
    var rows = [];
    $('.client-cperson-container').each(function (idx) {
      var $container = $(this);
      var name = ($container.find('input[name="cp_name[]"]').val() || '').trim();
      var email = ($container.find('input[name="cp_email[]"]').val() || '').trim();
      var phone = ($container.find('input[name="cp_number[]"]').val() || '').trim();
      var designation = ($container.find('input[name="cp_desigination[]"]').val() || '').trim();
      var callingCode = ($container.find('input[name="calling_code[]"]').val() || 'in').trim();
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
    var rows = getContactRowsData();
    var $tbody = $('#client_contact_table_body');
    $tbody.empty();
    if (!rows.length) {
      $tbody.append('<tr id="client_contact_empty_row"><td colspan="6" class="text-center text-muted py-6">No contact person added yet. Click "Add Contact Person" to begin.</td></tr>');
      return;
    }
    rows.forEach(function (row, i) {
      var typeBadge = i === 0
        ? '<span class="client-badge-primary">Primary</span>'
        : '<span class="client-badge-secondary">Secondary</span>';
      var primaryTag = i === 0 ? '<span class="client-badge-primary">Primary</span>' : '';
      $tbody.append(
        '<tr data-contact-index="' + row.index + '">' +
          '<td><div class="client-contact-person"><span class="client-contact-avatar" style="background-color:' + getAvatarColor(row.name) + '; color:#fff; display:inline-flex; align-items:center; justify-content:center; font-weight:600; width:32px; height:32px; border-radius:50%; margin-right:8px;">' + escapeHtml(initials(row.name)) + '</span><span class="client-contact-name">' + escapeHtml(row.name) + primaryTag + '</span></div></td>' +
          '<td>' + escapeHtml(row.designation || '—') + '</td>' +
          '<td>' + escapeHtml(row.email || '—') + '</td>' +
          '<td>' + escapeHtml(row.phone || '—') + '</td>' +
          '<td>' + typeBadge + '</td>' +
          '<td><div class="client-table-actions">' +
            '<button type="button" class="client-edit-contact" data-index="' + row.index + '" onclick="window.openContactDrawer ? window.openContactDrawer(' + row.index + ') : false;" title="Edit"><i class="la la-pencil"></i></button>' +
            '<button type="button" class="client-delete-contact is-delete" data-index="' + row.index + '" onclick="window.deleteContactRow ? window.deleteContactRow(' + row.index + ') : false;" title="Delete"><i class="la la-trash"></i></button>' +
          '</div></td>' +
        '</tr>'
      );
    });
  }

  function deleteContactRow(index) {
    var idx = parseInt(index, 10);
    var $rows = $('.client-cperson-container');
    if ($rows.length > 1) {
      $rows.eq(idx).remove();
    } else {
      $rows.eq(0).find('input').val('');
    }
    $('#contactPersonId').val(Math.max(0, $('.client-cperson-container').length - 1));
    renderContactTable();
    updateCompletion();
    if (typeof populateReviewSummaries === 'function') {
      populateReviewSummaries();
    }
  }

  function _cdfClearErrors() {
    $('#client_drawer_name, #client_drawer_designation, #client_drawer_email, #client_drawer_phone').removeClass('is-invalid');
    $('.cdf-error').removeClass('visible');
  }

  function openContactDrawer(index) {
    _cdfClearErrors();
    var isEdit = (typeof index === 'number' && !isNaN(index) && index >= 0) || (typeof index === 'string' && index !== '' && !isNaN(parseInt(index, 10)));
    var editIdx = isEdit ? parseInt(index, 10) : null;
    $('#client_drawer_edit_index').val(editIdx !== null ? editIdx : '');
    $('#client_contact_drawer_title').text(isEdit ? 'Edit Contact Person' : 'Add Contact Person');
    $('#client_contact_drawer_subtitle').text(isEdit ? 'Update the contact details below.' : 'Add the primary or secondary contact details for this client.');
    $('#client_save_contact_drawer .cdf-btn-text').text(isEdit ? 'Save Changes' : 'Add Contact Person');
    $('#client_save_contact_drawer .cdf-btn-icon').toggleClass('la-user-plus', !isEdit).toggleClass('la-save', isEdit);

    if (isEdit) {
      var $container = $('.client-cperson-container').eq(editIdx);
      if ($container.length) {
        $('#client_drawer_name').val($container.find('input[name="cp_name[]"]').val() || '');
        $('#client_drawer_designation').val($container.find('input[name="cp_desigination[]"]').val() || '');
        $('#client_drawer_email').val($container.find('input[name="cp_email[]"]').val() || '');
        $('#client_drawer_phone').val($container.find('input[name="cp_number[]"]').val() || '');
      }
    } else {
      $('#client_drawer_name, #client_drawer_designation, #client_drawer_email, #client_drawer_phone').val('');
    }

    // Ensure drawer is direct child of body to prevent any overflow:hidden clipping
    if ($('#client_contact_drawer').length && $('#client_contact_drawer').parent()[0] !== document.body) {
      $('body').append($('#client_contact_drawer_backdrop'), $('#client_contact_drawer'));
    }

    $('#client_contact_drawer, #client_contact_drawer_backdrop').addClass('is-open').attr('aria-hidden', 'false');
    setTimeout(function () { $('#client_drawer_name').focus(); }, 120);
  }

  function closeContactDrawer() {
    $('#client_contact_drawer, #client_contact_drawer_backdrop').removeClass('is-open').attr('aria-hidden', 'true');
    _cdfClearErrors();
  }

  window.openContactDrawer = openContactDrawer;
  window.closeContactDrawer = closeContactDrawer;
  window.saveContactDrawer = saveContactDrawer;
  window.deleteContactRow = deleteContactRow;

  function saveContactDrawer() {
    _cdfClearErrors();
    var name        = $('#client_drawer_name').val().trim();
    var designation = $('#client_drawer_designation').val().trim();
    var email       = $('#client_drawer_email').val().trim();
    var phone       = $('#client_drawer_phone').val().trim();
    var emailRe     = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    var valid       = true;
    var $firstBad   = null;

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
    if (!email || !emailRe.test(email)) {
      $('#client_drawer_email').addClass('is-invalid');
      $('#cdf_err_email').text(!email ? 'Email address is required.' : 'Please enter a valid email address.').addClass('visible');
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
      if ($firstBad) { $firstBad.focus(); }
      return;
    }

    var editIndexStr = $('#client_drawer_edit_index').val();
    var isEdit = (editIndexStr !== '' && !isNaN(parseInt(editIndexStr, 10)));

    if (isEdit) {
      var editIdx = parseInt(editIndexStr, 10);
      var $container = $('.client-cperson-container').eq(editIdx);
      if ($container.length) {
        $container.find('input[name="cp_name[]"]').val(name);
        $container.find('input[name="cp_desigination[]"]').val(designation);
        $container.find('input[name="cp_email[]"]').val(email);
        $container.find('input[name="cp_number[]"]').val(phone);
      }
    } else {
      // Check if an empty container exists to reuse
      var $emptyContainer = null;
      $('.client-cperson-container').each(function () {
        var n = ($(this).find('input[name="cp_name[]"]').val() || '').trim();
        var e = ($(this).find('input[name="cp_email[]"]').val() || '').trim();
        if (!n && !e) {
          $emptyContainer = $(this);
          return false;
        }
      });

      if ($emptyContainer && $emptyContainer.length) {
        $emptyContainer.find('input[name="cp_name[]"]').val(name);
        $emptyContainer.find('input[name="cp_desigination[]"]').val(designation);
        $emptyContainer.find('input[name="cp_email[]"]').val(email);
        $emptyContainer.find('input[name="cp_number[]"]').val(phone);
      } else {
        var newIdx = $('.client-cperson-container').length;
        var newHtml = '<div class="row client-cperson-container" data-contact-index="' + newIdx + '">' +
          '<div class="col-xl-3"><div class="form-group mb-0 client-cperson-container-child"><input type="text" name="cp_name[]" id="cp_name_' + newIdx + '" class="form-control form-control-solid" value="' + escapeHtml(name) + '"></div></div>' +
          '<div class="col-xl-3"><div class="form-group mb-0 client-cperson-container-child"><input type="email" name="cp_email[]" id="cp_email_' + newIdx + '" class="form-control form-control-solid" value="' + escapeHtml(email) + '"></div></div>' +
          '<div class="col-xl-3"><div class="form-group mb-0 client-cperson-container-child"><input type="number" name="cp_number[]" id="cp_number_' + newIdx + '" class="form-control form-control-solid" value="' + escapeHtml(phone) + '"><input type="hidden" value="in" class="calling_code" name="calling_code[]" id="calling_code_' + newIdx + '"></div></div>' +
          '<div class="col-xl-3"><div class="form-group mb-0 client-cperson-container-child"><input type="text" name="cp_desigination[]" id="cp_desigination_' + newIdx + '" class="form-control form-control-solid cp_desigination" value="' + escapeHtml(designation) + '"></div></div>' +
          '</div>';
        $('.client-cperson-row').first().append(newHtml);
      }
    }

    var totalContainers = $('.client-cperson-container').length;
    $('#contactPersonId').val(Math.max(0, totalContainers - 1));

    renderContactTable();
    updateCompletion();
    if (typeof populateReviewSummaries === 'function') {
      populateReviewSummaries();
    }
    closeContactDrawer();
  }

    /* Clear individual field error on input */
  $(document).on('input', '#client_drawer_name', function () { $(this).removeClass('is-invalid'); $('#cdf_err_name').removeClass('visible'); });
  $(document).on('input', '#client_drawer_designation', function () { $(this).removeClass('is-invalid'); $('#cdf_err_designation').removeClass('visible'); });
  $(document).on('input', '#client_drawer_email', function () { $(this).removeClass('is-invalid'); $('#cdf_err_email').removeClass('visible'); });
  $(document).on('input', '#client_drawer_phone', function () { $(this).removeClass('is-invalid'); $('#cdf_err_phone').removeClass('visible'); });

  function populateReviewSummaries() {
    var clientName = ($('#client_id').val() || '').trim();
    var website = ($('#website').val() || '').trim();
    var industry = $('#industry option:selected').text() || '—';
    var compSizes = $('#company_sizes option:selected').text() || '';
    var compType = $('#company_type option:selected').text() || '—';
    var founded = ($('#founded').val() || '').trim();

    // 1. Client Information Card
    $('.span_name').text(clientName || '—');
    $('.span_website').text(website || '—').attr('href', website || 'javascript:void(0);');
    $('.span_industry').text(industry !== 'Select Industry' ? industry : '—');
    $('.span_company_sizes').text(compSizes && compSizes !== 'Select Company Size' ? compSizes + ' employees' : '—');
    $('.span_company_type').text(compType !== 'Select Company Type' ? compType : '—');
    $('.span_founded').text(founded || '—');

    // 2. Client Description Card
    var descHtml = (typeof tinymce !== 'undefined' && tinymce.get('kt_docs_tinymce_hidden'))
      ? tinymce.get('kt_docs_tinymce_hidden').getContent()
      : ($('#kt_docs_tinymce_hidden').val() || '');
    var descText = (typeof tinymce !== 'undefined' && tinymce.get('kt_docs_tinymce_hidden'))
      ? tinymce.get('kt_docs_tinymce_hidden').getContent({ format: 'text' }).trim()
      : (descHtml ? $('<div>').html(descHtml).text().trim() : '');
    $('.span_kt_docs_tinymce_hidden').html(descHtml || '<span class="text-muted">No description provided.</span>');

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

    // 3. Address Details Card
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
    $('.divAddressDetail').html(addressBlocks.length ? addressBlocks.join('') : '<span class="text-muted">No address details entered.</span>');

    // 4. Contact Persons Card
    var contactRows = [];
    var contacts = getContactRowsData();
    contacts.forEach(function (row, idx) {
      var typeBadge = idx === 0 ? '<span class="client-badge-primary">Primary</span>' : '<span class="client-badge-secondary">Secondary</span>';
      var primaryTag = idx === 0 ? '<span class="client-badge-primary">Primary</span>' : '';
      var avatarLetters = initials(row.name);
      var avatarBg = getAvatarColor(row.name);
      contactRows.push('<tr><td><div class="client-contact-person"><span class="client-contact-avatar" style="background-color:'+avatarBg+'; color:#fff; display:inline-flex; align-items:center; justify-content:center; font-weight:600; width:32px; height:32px; border-radius:50%; margin-right:8px;">'+escapeHtml(avatarLetters)+'</span><span class="client-contact-name">'+escapeHtml(row.name)+primaryTag+'</span></div></td><td>'+escapeHtml(row.designation || '—')+'</td><td>'+escapeHtml(row.email || '—')+'</td><td>'+escapeHtml(row.phone || '—')+'</td><td>'+typeBadge+'</td></tr>');
    });
    if (!contactRows.length) {
      contactRows.push('<tr><td colspan="5" class="text-center text-muted py-4">No contact persons added.</td></tr>');
    }
    $('.div-contact-person').html(contactRows.join(''));

    // 5. Right Sidebar Summary Cards
    $('.client_summary_name').text(clientName || '—');
    $('.client_summary_desc').text(descText ? descText.substring(0, 80) + (descText.length > 80 ? '…' : '') : '—');
    var cityLabel = (typeof selectedCities !== 'undefined' && selectedCities.length)
      ? (selectedCities[0] + (selectedCities.length > 1 ? ' (+' + (selectedCities.length - 1) + ' more)' : ''))
      : '—';
    $('.client_summary_address').text(cityLabel);
    $('.client_summary_contacts').text(contacts.length ? contacts.length + ' contact(s)' : '—');
    $('#client_review_logo').css('background-image', $('.image-input-wrapper').css('background-image'));
    $('#client_preview_company').text(clientName || 'Company Name');
  }
  /* ===== PRE-LOADED CLIENT VALIDATION ENGINE (RAM-RESIDENT) ===== */
  var ClientValidationEngine = (function () {
    var patterns = {
      email: /^[^\s@]+@[^\s@]+\.[^\s@]+$/,
      phone10: /^\d{10}$/,
      year4: /^(18|19|20)\d{2}$/,
      url: /^(https?:\/\/)?([a-zA-Z0-9-]+\.)+[a-zA-Z]{2,}(\/[^\s]*)?$/
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
          if (!value) {
            result = { valid: false, message: 'Website URL is required.' };
          } else if (!patterns.url.test(value)) {
            result = { valid: false, message: 'Please enter a valid website URL (e.g. https://example.com).' };
          }
          break;

        case 'industry':
          if (!value) {
            result = { valid: false, message: 'Please select an Industry.' };
          }
          break;

        case 'company_sizes':
          if (!value) {
            result = { valid: false, message: 'Please select Company Size.' };
          }
          break;

        case 'company_type':
          if (!value) {
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
          if (!value) {
            result = { valid: false, message: 'Company Description is required.' };
          } else if (value.length < 5) {
            result = { valid: false, message: 'Description must be at least 5 characters.' };
          }
          break;

        case 'tags':
          if (!value || value === '[]') {
            result = { valid: false, message: 'Please add at least 1 Service Offering.' };
          }
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
          } else if (!patterns.email.test(value)) {
            result = { valid: false, message: 'Please enter a valid email address (e.g. contact@example.com).' };
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
  }

  function clearFieldError($field) {
    if (!$field || !$field.length) return;
    $field.removeClass('is-invalid');
    var $container = $field.closest('.client-field, .fv-plugins-icon-container');
    if ($container.length) {
      $container.find('.client-inline-error').remove();
    }
    $field.next('.client-inline-error').remove();
    if ($field.parent().length) {
      $field.parent().next('.client-inline-error').remove();
    }
  }

  function showInlineError($field, message) {
    if (!$field || !$field.length) return;
    clearFieldError($field);
    $field.addClass('is-invalid');
    var $container = $field.closest('.client-field, .fv-plugins-icon-container');
    if ($container.length) {
      $container.append('<span class="client-inline-error text-danger mt-1 d-block" style="font-size: 11px; font-weight: 600; color: #e11d48;"><i class="la la-exclamation-circle"></i> ' + message + '</span>');
    } else {
      var $target = $field.closest('.input-group').length ? $field.closest('.input-group') : $field;
      $target.after('<span class="client-inline-error text-danger mt-1 d-block" style="font-size: 11px; font-weight: 600; color: #e11d48;"><i class="la la-exclamation-circle"></i> ' + message + '</span>');
    }
  }

  function validateWizardStep(step) {
    clearInlineErrors();
    var isValid = true;
    var $firstInvalid = null;

    // Section 1: Client Information
    if (step === 1) {
      var clientNameRes = ClientValidationEngine.validateField('name', $('#client_id').val(), $('#client_id'));
      if (!clientNameRes.valid) {
        showInlineError($('#client_id'), clientNameRes.message);
        $firstInvalid = $firstInvalid || $('#client_id');
        isValid = false;
      }

      var websiteRes = ClientValidationEngine.validateField('website', $('#website').val(), $('#website'));
      if (!websiteRes.valid) {
        showInlineError($('#website'), websiteRes.message);
        $firstInvalid = $firstInvalid || $('#website');
        isValid = false;
      }

      var industryRes = ClientValidationEngine.validateField('industry', $('#industry').val(), $('#industry'));
      if (!industryRes.valid) {
        showInlineError($('#industry'), industryRes.message);
        $firstInvalid = $firstInvalid || $('#industry');
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

    // Section 2: Client Description & Offerings
    if (step === 2) {
      var descText = '';
      if (typeof tinymce !== 'undefined' && tinymce.get('kt_docs_tinymce_hidden')) {
        descText = tinymce.get('kt_docs_tinymce_hidden').getContent({ format: 'text' }).trim();
      } else {
        descText = ($('#kt_docs_tinymce_hidden').val() || '').trim();
      }
      var descRes = ClientValidationEngine.validateField('description', descText, $('#kt_docs_tinymce_hidden'));
      if (!descRes.valid) {
        showInlineError($('#kt_docs_tinymce_hidden'), descRes.message);
        $firstInvalid = $firstInvalid || $('#kt_docs_tinymce_hidden');
        isValid = false;
      }

      var tagsVal = ($('#tags').val() || '').trim();
      var tagsRes = ClientValidationEngine.validateField('tags', tagsVal, $('#tags'));
      if (!tagsRes.valid) {
        showInlineError($('#tags'), tagsRes.message);
        $firstInvalid = $firstInvalid || $('#tags');
        isValid = false;
      }
    }

    // Section 3: Address Details
    if (step === 3) {
      if (!selectedCities || selectedCities.length === 0) {
        var $searchWrap = $('.ij-location-input-wrap').first();
        showInlineError($searchWrap, 'Please select at least 1 work location city.');
        $firstInvalid = $firstInvalid || $('#citySearch');
        isValid = false;
      }

      var $firstAddr = $('textarea[name="address[]"]').first();
      var addrRes = ClientValidationEngine.validateField('address', ($firstAddr.val() || '').trim(), $firstAddr);
      if (!addrRes.valid) {
        showInlineError($firstAddr, addrRes.message);
        $firstInvalid = $firstInvalid || $firstAddr;
        isValid = false;
      }

      // Check any additional branch addresses
      $('.client-address-container .client-address-card.is-additional').each(function(idx) {
        var $card = $(this);
        var $cityInput = $card.find('.branch-city-search');
        var $textarea = $card.find('textarea[name="address[]"]');
        var cityVal = ($cityInput.val() || '').trim();
        var textVal = ($textarea.val() || '').trim();

        if (cityVal || textVal) {
          if (!cityVal) {
            showInlineError($cityInput, 'Branch city is required for Additional Address (' + (idx + 1) + ').');
            $firstInvalid = $firstInvalid || $cityInput;
            isValid = false;
          }
          if (!textVal) {
            showInlineError($textarea, 'Full Address is required for Additional Address (' + (idx + 1) + ').');
            $firstInvalid = $firstInvalid || $textarea;
            isValid = false;
          } else if (textVal.length < 5) {
            showInlineError($textarea, 'Address must be at least 5 characters for Additional Address (' + (idx + 1) + ').');
            $firstInvalid = $firstInvalid || $textarea;
            isValid = false;
          }
        }
      });
    }

    // Section 4: Contact Person
    if (step === 4) {
      var contacts = getContactRowsData();
      if (!contacts.length) {
        var $contactSection = $('.client-contact-section, #client_contact_table_body').first();
        showInlineError($contactSection, 'Minimum 1 Contact Person is required. Please click "Add Contact Person" to add details.');
        $firstInvalid = $firstInvalid || $contactSection;
        isValid = false;
      } else {
        for (var i = 0; i < contacts.length; i++) {
          var c = contacts[i];
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
    }

    if (!isValid && $firstInvalid) {
      $firstInvalid.focus();
      try {
        $('html, body').animate({ scrollTop: $firstInvalid.offset().top - 120 }, 200);
      } catch (e) {}
    }

    return isValid;
  }

  /* Synchronize wizard action buttons and stepper visual tokens */
  function syncWizardNavigationState(activeStep) {
    if (!activeStep) {
      activeStep = 1;
      $('#kt_wizard .wizard-step').each(function (idx) {
        if ($(this).attr('data-wizard-state') === 'current' || $(this).hasClass('active') || $(this).hasClass('current')) {
          activeStep = idx + 1;
        }
      });
    }

    // Step 1: Hide Back button; Step >= 2: Show Back button
    if (activeStep <= 1) {
      $('#client_btn_back, [data-wizard-type="action-prev"]').attr('style', 'display: none !important;');
    } else {
      $('#client_btn_back, [data-wizard-type="action-prev"]').attr('style', 'display: inline-flex !important;');
    }

    // Step 5: Switch Continue button to Submit / Update Client (ONLY on step 5 Review & Submit!)
    if (activeStep >= 5) {
      $('body').addClass('on-review-step');
      $('#continueButton, [data-wizard-type="action-next"]').attr('style', 'display: none !important;');
      $('#submitBtn, .submitfrm, .updatefrm').attr('style', 'display: inline-flex !important;');
      if (window.clientWizardUi && typeof window.clientWizardUi.populateReviewSummaries === 'function') {
        window.clientWizardUi.populateReviewSummaries();
      }
    } else {
      $('body').removeClass('on-review-step');
      $('#continueButton, [data-wizard-type="action-next"]').attr('style', 'display: inline-flex !important;');
      $('#submitBtn, .submitfrm, .updatefrm').attr('style', 'display: none !important;');
    }

        // Synchronize Stepper Tokens (Emerald Green for done, JERO Yellow gradient for current)
    $('#kt_wizard .wizard-step').each(function (idx) {
      var stepNum = idx + 1;
      var $step = $(this);
      if (stepNum < activeStep) {
        $step.attr('data-wizard-state', 'done').addClass('completed').removeClass('current');
      } else if (stepNum === activeStep) {
        $step.attr('data-wizard-state', 'current').addClass('current').removeClass('completed');
      } else {
        $step.attr('data-wizard-state', 'pending').removeClass('completed current');
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
    syncWizardNavigationState(1);

    // Navigation Mutex lock to strictly prevent duplicate transitions and double clicks
    var isNavigating = false;

    function getCurrentStepNumber() {
      var activeStep = 1;
      $('#kt_wizard .wizard-step').each(function (idx) {
        if ($(this).attr('data-wizard-state') === 'current' || $(this).hasClass('active') || $(this).hasClass('current')) {
          activeStep = idx + 1;
        }
      });
      return activeStep;
    }

    function goToWizardStep(targetStep) {
      targetStep = parseInt(targetStep, 10);
      if (!targetStep || targetStep < 1 || targetStep > 5) return;

      // Update Metronic outer wizard container state for CSS rules
      var stepState = (targetStep === 1 ? 'first' : (targetStep === 5 ? 'last' : 'between'));
      $('#kt_wizard').attr('data-wizard-state', stepState);

      // Synchronize Step Header indicators
      $('#kt_wizard .wizard-step').each(function (idx) {
        var s = idx + 1;
        if (s === targetStep) {
          $(this).attr('data-wizard-state', 'current').addClass('current').removeClass('completed pending');
        } else if (s < targetStep) {
          $(this).attr('data-wizard-state', 'done').addClass('completed').removeClass('current pending');
        } else {
          $(this).attr('data-wizard-state', 'pending').addClass('pending').removeClass('current completed');
        }
      });

      // Synchronize Step Tab Content panels
      $('#kt_wizard [data-wizard-type="step-content"]').each(function (idx) {
        var s = idx + 1;
        if (s === targetStep) {
          $(this).attr('data-wizard-state', 'current').show();
        } else {
          $(this).removeAttr('data-wizard-state').hide();
        }
      });

      // Synchronize Sticky Footer Buttons
      syncWizardNavigationState(targetStep);

      // On Step 5 (Review & Submit), populate real-time review summaries
      if (targetStep === 5 && typeof populateReviewSummaries === 'function') {
        populateReviewSummaries();
      }

      // Smooth scroll to top
      if (typeof KTUtil !== 'undefined' && typeof KTUtil.scrollTop === 'function') {
        KTUtil.scrollTop();
      } else {
        window.scrollTo({ top: 0, behavior: 'smooth' });
      }
    }

    // Intercept Continue button clicks — validate current step before advancing (Debounced & Mutex-locked)
    $(document).on('click', '#continueButton, .client-nav-next', function (e) {
      e.preventDefault();
      e.stopPropagation();

      if (isNavigating) return false;

      var currentStep = getCurrentStepNumber();
      if (!validateWizardStep(currentStep)) {
        return false;
      }

      isNavigating = true;
      var $btn = $(this);
      $btn.prop('disabled', true).addClass('disabled');

      goToWizardStep(currentStep + 1);

      setTimeout(function () {
        isNavigating = false;
        $btn.prop('disabled', false).removeClass('disabled');
      }, 300);
    });

    // Intercept Back button clicks — navigate back and sync navigation state (Debounced & Mutex-locked)
    $(document).on('click', '#client_btn_back, .client-nav-back', function (e) {
      e.preventDefault();
      e.stopPropagation();

      if (isNavigating) return false;

      var currentStep = getCurrentStepNumber();
      if (currentStep > 1) {
        isNavigating = true;
        var $btn = $(this);
        $btn.prop('disabled', true).addClass('disabled');

        goToWizardStep(currentStep - 1);

        setTimeout(function () {
          isNavigating = false;
          $btn.prop('disabled', false).removeClass('disabled');
        }, 300);
      }
    });

    // Intercept Step Header clicks — validate preceding steps when moving forward (Debounced & Mutex-locked)
    $(document).on('click', '#kt_wizard .wizard-step', function (e) {
      e.preventDefault();
      e.stopPropagation();

      if (isNavigating) return false;

      var targetStep = $(this).index() + 1;
      var currentStep = getCurrentStepNumber();
      if (targetStep === currentStep) return;

      if (targetStep > currentStep) {
        for (var s = currentStep; s < targetStep; s++) {
          if (!validateWizardStep(s)) {
            return false;
          }
        }
      }

      isNavigating = true;
      goToWizardStep(targetStep);

      setTimeout(function () {
        isNavigating = false;
      }, 300);
    });

    // Section Edit buttons in Review & Submit and Sidebar Summary Cards
    $(document).on('click', '[data-client-goto-step], .client-review-edit, .client-summary-edit', function (e) {
      e.preventDefault();
      e.stopPropagation();

      if (isNavigating) return false;

      var step = $(this).attr('data-client-goto-step') || $(this).data('client-goto-step');
      if (step) {
        isNavigating = true;
        goToWizardStep(step);
        setTimeout(function () {
          isNavigating = false;
        }, 300);
      }
    });

    $(document).on('input change', '#kt_form input, #kt_form select, #kt_form textarea', function () {
      updateAssistantPanel();
      updateCompletion();
    });

    $('#client_auto_fetch, #client_review_details').on('click', function () {
      if ($('#website').val()) {
        clientDomainRegisterChk();
      } else {
        Swal.fire({ text: 'Enter a website URL first.', icon: 'info', confirmButtonText: 'Ok' });
      }
    });

    $('#client_focus_tags').on('click', function () {
      $('#tags').focus();
    });

    $(document).on('click', '#client_open_contact_drawer', function (e) {
      e.preventDefault();
      e.stopPropagation();
      openContactDrawer(null);
    });
    $(document).on('click', '#client_close_contact_drawer, #client_cancel_contact_drawer, #client_contact_drawer_backdrop', function (e) {
      e.preventDefault();
      closeContactDrawer();
    });
    $(document).on('click', '#client_save_contact_drawer', function (e) {
      e.preventDefault();
      saveContactDrawer();
    });
    $(document).on('click', '.client-edit-contact', function (e) {
      e.preventDefault();
      e.stopPropagation();
      var index = $(this).attr('data-index');
      openContactDrawer(index);
    });

    $(document).on('click', '.client-delete-contact', function (e) {
      e.preventDefault();
      e.stopPropagation();
      var index = parseInt($(this).attr('data-index'), 10);
      deleteContactRow(index);
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

// Goto-step listener unified in goToWizardStep above

    // Draft save and restore removed per user request

    if (window.clientTagsTagify) {
      var refreshTags = function () {
        renderServicePreview((window.clientTagsTagify.value || []).map(function (t) { return t.value; }));
      };
      window.clientTagsTagify.on('add remove', refreshTags);
      refreshTags();
    }

    if (typeof tinymce !== 'undefined') {
      var attachEditorHooks = function () {
        var editor = tinymce.get('kt_docs_tinymce_hidden');
        if (!editor) return;
        var syncDesc = function () {
          var text = editor.getContent({ format: 'text' }) || '';
          $('#client_desc_count').text(Math.min(text.trim().length, 2000));
          $('#client_preview_description').text(text.trim() ? text.trim().substring(0, 180) : 'Your company description will appear here.');
          updateCompletion();
        };
        editor.on('keyup change SetContent', syncDesc);
        syncDesc();
      };
      setTimeout(attachEditorHooks, 1200);
    }

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
<script src="{{ asset('demo/v7/js/pages/crud/file-upload/image-input.js') }}"></script> 
<script src="{{ asset('demo/v7/js/plugins.bundle.js') }}<?php echo TM_VERSION; ?>"></script>
<script src="{{ asset('demo/v7/js/scripts.bundle.js') }}<?php echo TM_VERSION; ?>"></script>




@endpush
