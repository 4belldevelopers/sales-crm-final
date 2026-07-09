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
:root {
  --client-primary: #1769f5;
  --client-primary-soft: #edf4ff;
  --client-ink: #0d194d;
  --client-muted: #68738d;
  --client-border: #dde4ef;
  --client-surface: #ffffff;
  --client-page: #f7f9fc;
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

  /* The legacy wizard stays responsible for state; these rules only modernize its visual language. */
  .client-wizard-shell.card {background: transparent; box-shadow: none;}
  #kt_wizard .wizard-nav {
    background: var(--client-surface);
    border: 1px solid var(--client-border) !important;
    border-radius: 10px;
    box-shadow: 0 2px 8px rgba(17, 28, 68, 0.03);
    overflow: hidden;
  }
  #kt_wizard .wizard-steps {
    display: grid !important;
    grid-template-columns: repeat(5, minmax(0, 1fr));
    padding: 14px 18px !important;
    gap: 0;
  }
  #kt_wizard .wizard-step {
    display: flex !important;
    align-items: center;
    min-width: 0;
    position: relative;
    padding-right: 26px;
  }
  #kt_wizard .wizard-step:not(:last-child)::after {
    content: '';
    height: 1px;
    background: #dce3ee;
    position: absolute;
    right: 8px;
    width: 20px;
    top: 18px;
  }
  #kt_wizard .wizard-label {display: grid !important; grid-template-columns: 36px minmax(0, 1fr); column-gap: 10px; width: 100%; align-items: center;}
  #kt_wizard .wizard-icon {
    grid-row: 1 / span 2;
    width: 36px !important;
    height: 36px !important;
    display: grid !important;
    place-items: center;
    border-radius: 50%;
    background: #f2f5f9;
    border: 1px solid #dce3ee;
    color: #344054 !important;
  }
  #kt_wizard .wizard-icon svg {display: none;}
  #kt_wizard .wizard-icon::before {content: attr(data-step-number); font-size: 12px; font-weight: 600;}
  #kt_wizard .wizard-step:nth-child(1) .wizard-icon::before {content: '1';}
  #kt_wizard .wizard-step:nth-child(2) .wizard-icon::before {content: '2';}
  #kt_wizard .wizard-step:nth-child(3) .wizard-icon::before {content: '3';}
  #kt_wizard .wizard-step:nth-child(4) .wizard-icon::before {content: '4';}
  #kt_wizard .wizard-step:nth-child(5) .wizard-icon::before {content: '5';}
  #kt_wizard .wizard-title {font-size: 12px !important; font-weight: 600 !important; color: var(--client-ink) !important; margin: 0 !important; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;}
  #kt_wizard .wizard-title::after {display: block; margin-top: 2px; color: #7b8497; font-size: 10.5px; font-weight: 400;}
  #kt_wizard .wizard-step:nth-child(1) .wizard-title::after {content: 'Basic details';}
  #kt_wizard .wizard-step:nth-child(2) .wizard-title::after {content: 'About the client';}
  #kt_wizard .wizard-step:nth-child(3) .wizard-title::after {content: 'Location & address';}
  #kt_wizard .wizard-step:nth-child(4) .wizard-title::after {content: 'Key contacts';}
  #kt_wizard .wizard-step:nth-child(5) .wizard-title::after {content: 'Confirm & save';}
  #kt_wizard .wizard-arrow {display: none !important;}
  #kt_wizard .wizard-step[data-wizard-state="current"] .wizard-icon,
  #kt_wizard .wizard-step[data-wizard-state="done"] .wizard-icon {
    background: var(--client-primary);
    border-color: var(--client-primary);
    color: #fff !important;
    box-shadow: 0 4px 10px rgba(37, 99, 235, 0.22);
  }
  #kt_wizard .wizard-step[data-wizard-state="current"] .wizard-title {color: var(--client-primary) !important;}
  #kt_wizard, #kt_wizard > .row, #kt_wizard > .row > div, #kt_form {width: 100%; max-width: 100%; min-width: 0;}
  #kt_wizard > .row {margin: 14px 0 0 !important; padding: 0 !important;}
  #kt_wizard > .row > div {padding: 0 !important;}
  #kt_form > [data-wizard-type="step-content"] {
    background: var(--client-surface);
    border: 1px solid var(--client-border);
    border-radius: 10px;
    padding: 18px !important;
    min-height: 330px;
    box-shadow: 0 2px 8px rgba(17, 28, 68, 0.03);
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
  #kt_form .tagify:focus-within {border-color: var(--client-primary) !important; box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1) !important;}
  #kt_form .form-text {font-size: 10.5px !important; color: #7b8497 !important; margin-top: 5px;}
  .client-info-step {background: transparent !important; border: 0 !important; padding: 0 !important; box-shadow: none !important; min-height: auto !important;}
  .client-step-layout {display: grid; width: 100%; max-width: 100%; min-width: 0; grid-template-columns: minmax(0, 1fr) 280px; gap: 14px; align-items: stretch;}
  .client-main-column {min-width: 0;}
  .client-side-column {min-width: 0;}
  .client-section-card {background: #fff; border: 1px solid var(--client-border); border-radius: 10px; padding: 18px; margin-bottom: 14px; box-shadow: 0 2px 8px rgba(17, 28, 68, 0.04);}
  .client-main-column .client-section-card:last-child {margin-bottom: 0;}
  .client-section-heading {font-size: 13px; font-weight: 600; color: var(--client-ink); margin: 0 0 3px;}
  .client-section-copy {font-size: 10.5px; color: var(--client-muted); margin: 0 0 15px;}
  .client-branding-grid {display: grid; width: 100%; min-width: 0; grid-template-columns: 190px 130px minmax(0, 1fr); gap: 16px; align-items: stretch;}
  .client-logo-upload {border: 1px dashed #8db5ff; border-radius: 8px; background: #fbfdff; min-height: 152px; padding: 14px; display: flex; cursor: pointer; flex-direction: column; align-items: center; justify-content: center; text-align: center; transition: border-color .2s ease, background .2s ease;}
  .client-logo-upload:hover {border-color: var(--client-primary); background: #f5f9ff;}
  .client-upload-icon {display: grid; place-items: center; width: 42px; height: 42px; margin-bottom: 6px; border-radius: 50%; background: var(--client-primary-soft); color: var(--client-primary); font-size: 23px;}
  .client-logo-upload .client-upload-title {font-size: 11.5px; color: var(--client-primary); font-weight: 600;}
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
  #kt_form .client-field .input-group:focus-within {border-color: var(--client-primary) !important; box-shadow: 0 0 0 3px rgba(23, 105, 245, 0.1);}
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
  .client-tip {display: flex; align-items: center; gap: 8px; background: var(--client-primary-soft); color: #405174; border-radius: 7px; padding: 9px 11px; font-size: 10.5px; margin-top: 15px;}
  .client-tip i {color: var(--client-primary); font-size: 14px;}
  .client-auto-fetch {flex: 0 0 auto; max-width: 145px; border: 0 !important; border-left: 1px solid #b9d1ff !important; border-radius: 0 !important; color: var(--client-primary) !important; background: #f5f9ff !important; white-space: nowrap; font-size: 10.5px !important; font-weight: 600; padding: 0 10px !important;}
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
  .client-assistant-action {width: calc(100% - 22px); margin: 12px 11px 0; min-height: 34px; border: 0; border-radius: 6px; background: linear-gradient(90deg, #3f46f5, #116cf5); color: #fff; font-size: 10.5px; font-weight: 600;}
  .client-assistant-note {font-size: 9.5px; color: #7b8497; text-align: center; margin: 10px 8px 12px;}
  #kt_form > .d-flex.border-top {background: #fff; border: 1px solid var(--client-border) !important; border-radius: 10px; margin-top: 14px !important; padding: 10px 12px !important; justify-content: space-between !important; gap: 8px;}
  .client-action-left, .client-action-right {display: flex; align-items: center; gap: 8px;}
  #kt_form > .d-flex.border-top .btn {min-height: 36px; padding: 8px 18px !important; font-size: 11.5px !important; text-transform: none !important; border-radius: 7px;}
  #continueButton {min-width: 128px; background: linear-gradient(90deg, #315efb, #2557e7);}
  .client-cancel-btn {border: 1px solid #cfd7e6; color: var(--client-ink); background: #fff;}
  .client-profile-completion {display: flex; align-items: center; gap: 10px; margin-left: auto;}
  .client-completion-copy {text-align: right;}
  .client-completion-label {font-size: 10px; color: var(--client-ink); font-weight: 600;}
  .client-completion-status {display: block; margin-top: 3px; font-size: 9.5px; color: #10a66a;}
  .client-completion-ring {--completion: 0; width: 40px; height: 40px; display: grid; place-items: center; border-radius: 50%; background: conic-gradient(var(--client-primary) calc(var(--completion) * 1%), #e5ebf4 0); position: relative;}
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
  .client-contact-drawer-backdrop {position: fixed; inset: 0; background: rgba(15,25,77,.38); z-index: 1050; opacity: 0; visibility: hidden; transition: opacity .25s ease; backdrop-filter: blur(2px);}
  .client-contact-drawer-backdrop.is-open {opacity: 1; visibility: visible;}
  .client-contact-drawer {
    position: fixed; top: 0; right: 0; height: 100vh;
    width: min(500px, 100vw);
    background: #fff; z-index: 1051;
    box-shadow: -12px 0 48px rgba(17,28,68,.14);
    border-left: 1px solid #e2e8f4;
    transform: translateX(100%); transition: transform .28s cubic-bezier(.4,0,.2,1);
    display: flex; flex-direction: column;
  }
  .client-contact-drawer.is-open {transform: translateX(0);}
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
     	$company_sizes			= @$client->company_sizes; 
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
                  <!--begin::Client Information-->
                  <div class="pb-5 client-info-step" data-wizard-type="step-content" data-wizard-state="current">
                    <!-- Purpose: group identity and company metadata into compact cards that scan well on laptop screens. -->
                    <div class="client-step-layout">
                      <div class="client-main-column">
                        <section class="client-section-card" aria-labelledby="client-branding-title">
                          <h3 class="client-section-heading" id="client-branding-title">Company Branding</h3>
                          <p class="client-section-copy">Upload company logo for visual identification.</p>
                          <div class="client-branding-grid">
                            <label class="client-logo-upload" for="client_logo_input">
                              <span class="client-upload-icon"><i class="la la-cloud-upload-alt" aria-hidden="true"></i></span>
                              <span class="client-upload-title">Upload Company Logo</span>
                              <span class="client-upload-copy">PNG or JPG (Max. 2MB)</span>
                              <span class="client-upload-copy">Recommended: square image</span>
                            </label>
                            <div class="client-logo-preview">
                              <span class="client-preview-label">Preview</span>
                              <div class="image-input image-input-outline" id="client_logo_preview">
                                <div class="image-input-wrapper" style="background-image: url(<?php echo @$background_image; ?>);"></div>
                                <label data-kt-image-input-action="change" title="Change company logo">
                                  <input type="file" name="logo" id="client_logo_input" accept=".png, .jpg, .jpeg" onchange="loadFile(event)"/>
                                  <input type="hidden" name="avatar_remove" />
                                </label>
                              </div>
                              <button type="button" class="client-preview-remove" data-kt-image-input-action="cancel"><i class="la la-trash" aria-hidden="true"></i> Remove</button>
                            </div>
                            <div class="client-brand-fields">
                              <div class="client-field fv-plugins-icon-container">
                                <label class="form-label required" for="website">Website</label>
                                <div class="input-group input-group-solid">
                                  <span class="input-group-text"><i class="la la-globe"></i></span>
                                  <input type="text" class="form-control form-control-solid" name="website" id="website" value="<?php echo @$website; ?>" placeholder="https://www.example.com">
                                  <button type="button" class="btn client-auto-fetch" id="client_auto_fetch"><i class="la la-magic" aria-hidden="true"></i> Auto Fetch Data</button>
                                </div>
                                <span class="form-text text-muted">Enter website to auto-fetch available company details.</span>
                              </div>
                              <div class="client-field fv-plugins-icon-container">
                                <label class="form-label required" for="client_id">Client Name</label>
                                <div class="input-group input-group-solid">
                                  <span class="input-group-text"><i class="la la-user"></i></span>
                                  <input type="text" class="form-control form-control-solid" name="name" id="client_id" value="<?php echo @$name; ?>" placeholder="Enter client / company name">
                                </div>
                              </div>
                            </div>
                          </div>
                        </section>

                        <section class="client-section-card" aria-labelledby="client-details-title">
                          <h3 class="client-section-heading" id="client-details-title">Company Details</h3>
                          <p class="client-section-copy">Enter essential details about the client organization.</p>
                          <div class="client-details-grid">
                            <div class="client-field fv-plugins-icon-container">
                              <label class="form-label required" for="industry">Industry</label>
                              <div class="input-group input-group-solid">
                                <span class="input-group-text"><i class="la la-briefcase"></i></span>
                                <select class="form-select form-select-solid" name="industry" id="industry">
                                  <option value="">Select industry</option>
                                  <?php foreach($department as $key => $val){
                                    $sel = $val->ind_id == $industry ? 'selected="selected"' : '';
                                    echo '<option value="'.@$val->ind_id.'" '.@$sel.'>'.ucwords(@$val->ind_name).'</option>';
                                  } ?>
                                </select>
                              </div>
                            </div>
                            <div class="client-field fv-plugins-icon-container">
                              <label class="form-label required" for="company_sizes">Company Size</label>
                              <div class="input-group input-group-solid">
                                <span class="input-group-text"><i class="la la-users"></i></span>
                                <input type="number" class="form-control form-control-solid" name="company_sizes" id="company_sizes" value="<?php echo @$company_sizes; ?>" placeholder="Enter employee count">
                              </div>
                            </div>
                            <div class="client-field fv-plugins-icon-container">
                              <label class="form-label required" for="company_type">Type</label>
                              <div class="input-group input-group-solid">
                                <span class="input-group-text"><i class="la la-city"></i></span>
                                <select class="form-select form-select-solid" name="company_type" id="company_type">
                                  <option value="">Select company type</option>
                                  <?php foreach($companyType as $key => $val){
                                    $sel = $key == $company_type ? 'selected="selected"' : '';
                                    echo '<option value="'.@$key.'" '.@$sel.'>'.ucwords(@$val).'</option>';
                                  } ?>
                                </select>
                              </div>
                            </div>
                            <div class="client-field fv-plugins-icon-container">
                              <label class="form-label required" for="founded">Founded Year</label>
                              <div class="input-group input-group-solid">
                                <span class="input-group-text"><i class="la la-calendar"></i></span>
                                <input type="number" class="form-control form-control-solid" name="founded" id="founded" value="<?php echo @$founded; ?>" placeholder="Select year">
                              </div>
                            </div>
                          </div>
                          <div class="client-tip"><i class="la la-info-circle" aria-hidden="true"></i><span><strong>Tip:</strong> You can update these details anytime from the client profile.</span></div>
                        </section>
                      </div>

                      <aside class="client-assistant" aria-labelledby="client-assistant-title">
                        <div class="client-assistant-head">
                          <div>
                            <div class="d-flex align-items-center gap-2">
                              <h3 class="client-assistant-title" id="client-assistant-title">AI Assistant</h3>
                              <span class="client-beta">Beta</span>
                            </div>
                            <p class="client-assistant-copy">We can auto-fetch available information from the company website.</p>
                          </div>
                          <span class="client-assistant-spark"><i class="la la-magic" aria-hidden="true"></i></span>
                        </div>
                        <div class="client-suggestions">
                          <div class="client-suggestions-title">Profile Suggestions</div>
                          <div class="client-suggestion-row">
                            <span class="client-suggestion-label">Industry</span>
                            <span class="client-assistant-value" data-client-field="industry">Not set</span>
                            <i class="la la-check-circle client-suggestion-status is-missing" aria-hidden="true"></i>
                          </div>
                          <div class="client-suggestion-row">
                            <span class="client-suggestion-label">Company Size</span>
                            <span class="client-assistant-value" data-client-field="company_sizes">Not set</span>
                            <i class="la la-check-circle client-suggestion-status is-missing" aria-hidden="true"></i>
                          </div>
                          <div class="client-suggestion-row">
                            <span class="client-suggestion-label">Company Type</span>
                            <span class="client-assistant-value" data-client-field="company_type">Not set</span>
                            <i class="la la-check-circle client-suggestion-status is-missing" aria-hidden="true"></i>
                          </div>
                          <div class="client-suggestion-row">
                            <span class="client-suggestion-label">Founded Year</span>
                            <span class="client-assistant-value" data-client-field="founded">Not set</span>
                            <i class="la la-check-circle client-suggestion-status is-missing" aria-hidden="true"></i>
                          </div>
                          <button type="button" class="client-assistant-action" id="client_review_details"><i class="la la-magic" aria-hidden="true"></i> Apply Website Data</button>
                          <p class="client-assistant-note">Not accurate? You can edit every field manually.</p>
                        </div>
                      </aside>
                    </div>
                  </div>
                  <!--end::Client Information-->
                  <!--begin::Client Description-->
                  <div class="pb-5 client-desc-step" data-wizard-type="step-content">
                    <div class="client-step-layout">
                      <div class="client-main-column">
                        <section class="client-section-card">
                          <div class="client-step-header">
                            <div>
                              <h3>About the Company</h3>
                              <p>Describe the client's business, mission and background.</p>
                            </div>
                          </div>
                          <div class="client-editor-wrap">
                            <div class="client-editor-toolbar">
                              <span>Company Description</span>
                              <span class="client-char-count"><span id="client_desc_count">0</span>/2000</span>
                            </div>
                            <textarea id="kt_docs_tinymce_hidden" name="kt_docs_tinymce_hidden" class="tox-target"><?php echo @$description; ?></textarea>
                          </div>
                        </section>

                        <section class="client-section-card">
                          <div class="client-step-header">
                            <div>
                              <h3>Service Offerings</h3>
                              <p>Add the services this client provides or is known for (press Enter or comma to add).</p>
                            </div>
                          </div>
                          <input class="form-control form-control-solid" name="tags" id="tags" placeholder="e.g. Software Development, IT Consulting..." value="<?php echo @$tags; ?>">
                          <div class="mt-3">
                            <span class="client-suggested-label">Suggested:</span>
                            <div class="client-suggested-list" id="client_suggested_services">
                              <?php
                              $suggestedServices = ['Software Development','IT Consulting','Cloud Solutions','Digital Transformation','Data Analytics','Cybersecurity','UI/UX Design','Project Management','DevOps','Quality Assurance'];
                              foreach ($suggestedServices as $svc): ?>
                              <span class="client-suggested-tag" data-value="<?php echo htmlspecialchars($svc); ?>">
                                <?php echo htmlspecialchars($svc); ?>
                                <button type="button" class="client-suggest-add" title="Add <?php echo htmlspecialchars($svc); ?>"><i class="la la-plus" aria-hidden="true"></i></button>
                              </span>
                              <?php endforeach; ?>
                            </div>
                          </div>
                          <!-- hidden: keeps live-preview in sidebar in sync -->
                          <div id="client_services_preview" class="client-services-grid" style="display:none"></div>
                        </section>
                      </div>

                      <aside class="client-side-column">
                        <div class="client-helper-card client-helper-blue">
                          <h4><i class="la la-lightbulb-o" aria-hidden="true"></i> Writing Tips</h4>
                          <ul>
                            <li><i class="la la-check-circle" aria-hidden="true"></i><span>Include company mission, vision and background.</span></li>
                            <li><i class="la la-check-circle" aria-hidden="true"></i><span>Mention key products, services or market focus.</span></li>
                            <li><i class="la la-check-circle" aria-hidden="true"></i><span>Keep the tone professional and concise.</span></li>
                          </ul>
                        </div>
                        <div class="client-helper-card client-helper-purple">
                          <h4><i class="la la-cube" aria-hidden="true"></i> Why add Services?</h4>
                          <p>Services help match this client with relevant job requirements and improve search visibility across the platform.</p>
                        </div>
                        <div class="client-helper-card client-helper-green">
                          <h4><i class="la la-eye" aria-hidden="true"></i> Live Preview</h4>
                          <div class="client-preview-box">
                            <div class="preview-title" id="client_preview_company"><?php echo @$name ?: 'Company Name'; ?></div>
                            <div class="preview-body" id="client_preview_description">Your company description will appear here.</div>
                            <div class="client-preview-tags" id="client_preview_tags"></div>
                          </div>
                        </div>
                      </aside>
                    </div>
                  </div>
                  <!--end::Client Description-->
                  <!--begin::Address Details-->
                  <div class="pb-5 client-address-step" data-wizard-type="step-content">
                    <div class="client-step-layout">
                      <div class="client-main-column">
                        <div class="client-step-header">
                          <div>
                            <h3>Address Details</h3>
                            <p>Enter the city and full address of this client.</p>
                          </div>
                        </div>
                        <div class="client-info-banner"><i class="la la-info-circle" aria-hidden="true"></i><span>The first address you enter will be considered as the headquarters.</span></div>
                        <?php
                          $countryListing = $stateListing = '';
                          foreach ($countryArr as $cont) {
                            $sel = ($cont['id'] == 101) ? 'selected="selected"' : '';
                            $countryListing .= '<option value="'.$cont['id'].'" '.$sel.'>'.$cont['name'].'</option>';
                          }
                          foreach ($statelist as $state) {
                            $stateListing .= '<option value="'.$state['id'].'">'.$state['name'].'</option>';
                          }

                          $addressId = 0;
                          $addressIdCount = 1;
                          if ($page_event == 'EDIT') {
                            $clhtml = '<div class="row client-address-container">';
                            foreach ($client_location_arr as $clVal) {
                              $countryListing = $stateListing = '';
                              $countryName = '';
                              foreach ($countryArr as $cont) {
                                $sel = ($cont['id'] == $clVal->country_id) ? 'selected="selected"' : '';
                                if ($cont['id'] == $clVal->country_id) {
                                  $countryName = $cont['name'];
                                }
                                $countryListing .= '<option value="'.$cont['id'].'" '.$sel.'>'.$cont['name'].'</option>';
                              }
                              $statelist = GeneralMethod::statelist($clVal->country_id);
                              foreach ($statelist as $state) {
                                $sel = ($state['id'] == $clVal->state) ? 'selected="selected"' : '';
                                $stateListing .= '<option value="'.$state['id'].'" '.$sel.'>'.$state['name'].'</option>';
                              }
                              $statelist = DB::table('state_list')
                                ->select('state_list.id', 'state_list.state as name', DB::raw('101 as country_id'))
                                ->where('state_list.country_id', $clVal->country_id)
                                ->orderBy('state_list.state')
                                ->get()->toArray();
                              $indianStatelist = json_decode(json_encode($statelist), true);
                              $stateListing = '';
                              $cityListing = '';
                              $stateName = '';
                              $cityName = '';
                              foreach ($indianStatelist as $sRow) {
                                $sel = ($sRow['id'] == $clVal->state) ? 'selected="selected"' : '';
                                if ($sRow['id'] == $clVal->state) {
                                  $stateName = $sRow['name'];
                                }
                                $stateListing .= '<option value="'.$sRow['id'].'" '.$sel.'>'.$sRow['name'].'</option>';
                              }
                              $Rawcitylist = DB::table('city_list')->select('city_list.city_id as id', 'city_list.city as name')->where('city_list.state_id', $clVal->state)->orderBy('city_list.city')->get();
                              foreach ($Rawcitylist as $cRow) {
                                $sel = ($cRow->id == $clVal->city_id) ? 'selected="selected"' : '';
                                if ($cRow->id == $clVal->city_id) {
                                  $cityName = $cRow->name;
                                }
                                $cityListing .= '<option value="'.$cRow->id.'" '.$sel.'>'.$cRow->name.'</option>';
                              }
                              $return = $cityName.' | '.$stateName.' | '.$countryName;
                              $cardClass = ($addressIdCount === 1) ? '' : ' is-additional';
                              $cardTitle = ($addressIdCount === 1) ? 'Headquarters (Primary)' : 'Additional Address ('.($addressIdCount - 1).')';
                              $clhtml .= '
                                <div class="client-address-card'.$cardClass.' client-address-container-child" id="clientAddr_'.$addressId.'">
                                  <div class="client-address-card-head">
                                    <h4>'.$cardTitle.'</h4>
                                    '.($addressIdCount > 1 ? '<button type="button" class="client-remove-address-btn remove_address_single" data-target="clientAddr_'.$addressId.'"><i class="la la-trash"></i> Remove</button>' : '').'
                                  </div>
                                  <div class="client-field fv-plugins-icon-container mb-3">
                                    <label class="form-label required" for="kt_tagify_work_location_'.$addressId.'">City</label>
                                    <div class="input-group input-group-solid">
                                      <span class="input-group-text"><i class="la la-map-marker"></i></span>
                                      <input type="hidden" name="hid_country_id[]" id="hid_country_id_'.$addressId.'" class="gethiddencountry" value="'.$clVal->country_id.'" />
                                      <input class="form-control form-control-solid" value="'.$return.'" id="kt_tagify_work_location_'.$addressId.'" name="kt_tagify_work_location[]" placeholder="Start typing to search for a city"/>
                                    </div>
                                    <span class="form-text text-muted">Start typing to search for a city.</span>
                                  </div>
                                  <div class="client-field fv-plugins-icon-container">
                                    <label class="form-label required" for="address_'.$addressId.'">Full Address</label>
                                    <textarea class="form-control form-control-solid client-address-textarea" name="address[]" rows="4" maxlength="500" placeholder="Building, street, area, landmark, pincode, etc." id="address_'.$addressId.'">'.@$clVal->address.'</textarea>
                                    <div class="d-flex justify-content-between mt-1">
                                      <span class="form-text text-muted">Provide the complete and accurate address for better identification.</span>
                                      <span class="client-char-count"><span class="client-address-count">0</span>/500</span>
                                    </div>
                                  </div>
                                </div>';
                              $addressId++;
                              $addressIdCount++;
                            }
                            $clhtml .= '</div>';
                            echo $clhtml;
                            $addressId--;
                          } else {
                        ?>
                        <div class="row client-address-container">
                          <div class="client-address-card client-address-container-child" id="clientAddr_<?php echo @$addressId; ?>">
                            <div class="client-address-card-head">
                              <h4>Headquarters (Primary)</h4>
                            </div>
                            <div class="client-field fv-plugins-icon-container mb-3">
                              <label class="form-label required" for="kt_tagify_work_location_<?php echo @$addressId; ?>">City</label>
                              <div class="input-group input-group-solid">
                                <span class="input-group-text"><i class="la la-map-marker"></i></span>
                                <input class="form-control form-control-solid" value="" id="kt_tagify_work_location_<?php echo @$addressId; ?>" name="kt_tagify_work_location[]" placeholder="Start typing to search for a city"/>
                              </div>
                              <span class="form-text text-muted">Start typing to search for a city.</span>
                            </div>
                            <div class="client-field fv-plugins-icon-container">
                              <label class="form-label required" for="address_<?php echo @$addressId; ?>">Full Address</label>
                              <textarea class="form-control form-control-solid client-address-textarea" name="address[]" rows="4" maxlength="500" placeholder="Building, street, area, landmark, pincode, etc." id="address_<?php echo @$addressId; ?>"></textarea>
                              <div class="d-flex justify-content-between mt-1">
                                <span class="form-text text-muted">Provide the complete and accurate address for better identification.</span>
                                <span class="client-char-count"><span class="client-address-count">0</span>/500</span>
                              </div>
                            </div>
                          </div>
                        </div>
                        <?php } ?>
                        <section class="client-section-card mt-3">
                          <div class="client-address-card-head">
                            <div>
                              <h4>Additional Address <span class="text-muted fw-normal">(Optional)</span></h4>
                            </div>
                            <button type="button" class="client-add-address-btn add_address"><i class="la la-plus" aria-hidden="true"></i> Add Another Address</button>
                          </div>
                          <div id="addressLoader"></div>
                          <div class="client-address-empty" id="client_additional_empty">
                            <i class="la la-building" aria-hidden="true"></i>
                            <p>No additional address added yet.</p>
                          </div>
                        </section>
                        <input type="hidden" name="addressId" id="addressId" value="<?php echo @$addressId; ?>" />
                      </div>
                      <aside class="client-side-column">
                        <div class="client-helper-card client-helper-sky">
                          <h4><i class="la la-map-marked-alt" aria-hidden="true"></i> About Headquarters</h4>
                          <p>The first address is treated as the client headquarters and appears prominently on the client profile and job listings.</p>
                        </div>
                        <div class="client-helper-card client-helper-green">
                          <h4><i class="la la-lightbulb-o" aria-hidden="true"></i> Tips for Accurate Address</h4>
                          <ul>
                            <li><i class="la la-check-circle" aria-hidden="true"></i><span>Search and select the correct city.</span></li>
                            <li><i class="la la-check-circle" aria-hidden="true"></i><span>Include landmark and pincode in the full address.</span></li>
                            <li><i class="la la-check-circle" aria-hidden="true"></i><span>Add branch offices as additional addresses if needed.</span></li>
                          </ul>
                        </div>
                      </aside>
                    </div>
                  </div>
                  <!--end::Address Details-->
                  <!--begin::Contact Person-->
                  <div class="pb-5 client-contact-step" data-wizard-type="step-content">
                    <div class="client-step-layout">
                      <div class="client-main-column">
                        <div class="client-contact-toolbar">
                          <div>
                            <h3 class="client-section-heading mb-1">Contact Person</h3>
                            <p class="client-section-copy mb-0">Add key contacts for this client organization.</p>
                          </div>
                          <button type="button" class="client-add-address-btn" id="client_open_contact_drawer"><i class="la la-plus" aria-hidden="true"></i> Add Contact Person</button>
                        </div>
                        <div class="client-contact-table-wrap mb-3">
                          <table class="client-contact-table">
                            <thead>
                              <tr>
                                <th>Contact Person</th>
                                <th>Designation</th>
                                <th>Email</th>
                                <th>Phone Number</th>
                                <th>Type</th>
                                <th>Actions</th>
                              </tr>
                            </thead>
                            <tbody id="client_contact_table_body">
                              <tr id="client_contact_empty_row">
                                <td colspan="6" class="text-center text-muted py-6">No contact person added yet. Click "Add Contact Person" to begin.</td>
                              </tr>
                            </tbody>
                          </table>
                        </div>
                        <div class="client-info-banner mb-0"><i class="la la-info-circle" aria-hidden="true"></i><span>The first contact person is set as the primary contact by default.</span></div>

                        <div class="client-contact-fields">
                          <div class="row client-cperson-row">
<?php
$contactPersonId = 0;
$contactPersonIdCount = 1;
if ($page_event == 'EDIT') {
    if (count($client_contact_arr) > 0) {
        foreach ($client_contact_arr as $ccArr) {
            echo '<div class="row client-cperson-container" data-contact-index="'.$contactPersonId.'">
              <div class="col-xl-3"><div class="form-group mb-0 client-cperson-container-child"><input type="text" name="cp_name[]" id="cp_name_'.$contactPersonId.'" class="form-control form-control-solid" value="'.$ccArr->contact_person_name.'" placeholder="Enter person name"></div></div>
              <div class="col-xl-3"><div class="form-group mb-0 client-cperson-container-child"><input type="email" name="cp_email[]" id="cp_email_'.$contactPersonId.'" class="form-control form-control-solid" value="'.$ccArr->contact_person_email.'" placeholder="Enter email"></div></div>
              <div class="col-xl-3"><div class="form-group mb-0 client-cperson-container-child"><input type="number" name="cp_number[]" id="cp_number_'.$contactPersonId.'" class="form-control form-control-solid" value="'.$ccArr->contact_person_number.'" placeholder="Enter contact number"><input type="hidden" value="'.($ccArr->calling_code ? $ccArr->calling_code : $country_code).'" class="calling_code" name="calling_code[]" id="calling_code_'.$contactPersonId.'"></div></div>
              <div class="col-xl-3"><div class="form-group mb-0 client-cperson-container-child"><input type="text" name="cp_desigination[]" id="cp_desigination_'.$contactPersonId.'" class="form-control form-control-solid cp_desigination" value="'.$ccArr->contact_person_designation.'" placeholder="Enter desigination"></div></div>
            </div>';
            $contactPersonId++;
            $contactPersonIdCount++;
        }
    } else {
?>
                            <div class="row client-cperson-container" data-contact-index="<?php echo $contactPersonId; ?>">
                              <div class="col-xl-3"><div class="form-group mb-0 client-cperson-container-child"><input type="text" name="cp_name[]" id="cp_name_<?php echo $contactPersonId; ?>" class="form-control form-control-solid" placeholder="Enter person name"></div></div>
                              <div class="col-xl-3"><div class="form-group mb-0 client-cperson-container-child"><input type="email" name="cp_email[]" id="cp_email_<?php echo $contactPersonId; ?>" class="form-control form-control-solid" placeholder="Enter email"></div></div>
                              <div class="col-xl-3"><div class="form-group mb-0 client-cperson-container-child"><input type="number" name="cp_number[]" id="cp_number_<?php echo $contactPersonId; ?>" class="form-control form-control-solid" placeholder="Enter contact number"><input type="hidden" value="{{ $country_code }}" class="calling_code" name="calling_code[]" id="calling_code_<?php echo $contactPersonId; ?>"></div></div>
                              <div class="col-xl-3"><div class="form-group mb-0 client-cperson-container-child"><input type="text" name="cp_desigination[]" id="cp_desigination_<?php echo $contactPersonId; ?>" class="form-control form-control-solid cp_desigination" placeholder="Enter desigination"></div></div>
                            </div>
<?php
    }
    $contactPersonId--;
} else {
?>
                            <div class="row client-cperson-container" data-contact-index="<?php echo $contactPersonId; ?>">
                              <div class="col-xl-3"><div class="form-group mb-0 client-cperson-container-child"><input type="text" name="cp_name[]" id="cp_name_<?php echo $contactPersonId; ?>" class="form-control form-control-solid" placeholder="Enter person name"></div></div>
                              <div class="col-xl-3"><div class="form-group mb-0 client-cperson-container-child"><input type="email" name="cp_email[]" id="cp_email_<?php echo $contactPersonId; ?>" class="form-control form-control-solid" placeholder="Enter email"></div></div>
                              <div class="col-xl-3"><div class="form-group mb-0 client-cperson-container-child"><input type="number" name="cp_number[]" id="cp_number_<?php echo $contactPersonId; ?>" class="form-control form-control-solid" placeholder="Enter contact number"><input type="hidden" value="{{ $country_code }}" class="calling_code" name="calling_code[]" id="calling_code_<?php echo $contactPersonId; ?>"></div></div>
                              <div class="col-xl-3"><div class="form-group mb-0 client-cperson-container-child"><input type="text" name="cp_desigination[]" id="cp_desigination_<?php echo $contactPersonId; ?>" class="form-control form-control-solid cp_desigination" placeholder="Enter desigination"></div></div>
                            </div>
<?php } ?>
                          </div>
                        </div>
                        <input type="hidden" name="contactPersonId" id="contactPersonId" value="<?php echo @$contactPersonId; ?>" />
                        <button type="button" class="add_cperson d-none" aria-hidden="true"></button>
                        <button type="button" class="remove_cperson d-none" aria-hidden="true"></button>
                      </div>
                      <aside class="client-side-column">
                        <div class="client-helper-card client-helper-purple">
                          <h4><i class="la la-users" aria-hidden="true"></i> Why add Contact Person?</h4>
                          <ul>
                            <li><i class="la la-check-circle" aria-hidden="true"></i><span>Quick communication for job requirements.</span></li>
                            <li><i class="la la-check-circle" aria-hidden="true"></i><span>Track interactions and follow-ups easily.</span></li>
                            <li><i class="la la-check-circle" aria-hidden="true"></i><span>Assign primary and secondary contacts.</span></li>
                          </ul>
                        </div>
                        <div class="client-helper-card client-helper-green">
                          <h4><i class="la la-lightbulb-o" aria-hidden="true"></i> Best Practices</h4>
                          <ul>
                            <li><i class="la la-check-circle" aria-hidden="true"></i><span>Ensure email and phone number are valid.</span></li>
                            <li><i class="la la-check-circle" aria-hidden="true"></i><span>Add HR or hiring manager as primary contact.</span></li>
                            <li><i class="la la-check-circle" aria-hidden="true"></i><span>Include designation for context.</span></li>
                          </ul>
                        </div>
                      </aside>
                    </div>
                  </div>
                  <div class="client-contact-drawer-backdrop" id="client_contact_drawer_backdrop"></div>
                  <div class="client-contact-drawer" id="client_contact_drawer" aria-hidden="true">
                    <!-- Header -->
                    <div class="client-drawer-head">
                      <div class="client-drawer-head-info">
                        <h4 id="client_contact_drawer_title">Add Contact Person</h4>
                        <p class="client-drawer-head-subtitle" id="client_contact_drawer_subtitle">Add the primary or secondary contact details for this client.</p>
                      </div>
                      <button type="button" class="client-drawer-close-btn" id="client_close_contact_drawer" title="Close" aria-label="Close drawer">
                        <i class="la la-times" aria-hidden="true"></i>
                      </button>
                    </div>
                    <!-- Body -->
                    <div class="client-drawer-body">
                      <input type="hidden" id="client_drawer_edit_index" value="">

                      <div class="client-drawer-field">
                        <label class="cdf-label" for="client_drawer_name">Contact Name<span class="cdf-required">*</span></label>
                        <div class="cdf-input-wrap">
                          <i class="la la-user cdf-input-icon" aria-hidden="true"></i>
                          <input type="text" id="client_drawer_name" placeholder="Enter contact name" autocomplete="off">
                        </div>
                        <div class="cdf-error" id="cdf_err_name">Contact name is required.</div>
                      </div>

                      <div class="client-drawer-field">
                        <label class="cdf-label" for="client_drawer_designation">Designation<span class="cdf-required">*</span></label>
                        <div class="cdf-input-wrap">
                          <i class="la la-id-badge cdf-input-icon" aria-hidden="true"></i>
                          <input type="text" id="client_drawer_designation" placeholder="Enter designation" autocomplete="off">
                        </div>
                        <div class="cdf-error" id="cdf_err_designation">Designation is required.</div>
                      </div>

                      <div class="client-drawer-field">
                        <label class="cdf-label" for="client_drawer_email">Email Address<span class="cdf-required">*</span></label>
                        <div class="cdf-input-wrap">
                          <i class="la la-envelope cdf-input-icon" aria-hidden="true"></i>
                          <input type="email" id="client_drawer_email" placeholder="name@company.com" autocomplete="off">
                        </div>
                        <div class="cdf-error" id="cdf_err_email">Please enter a valid email address.</div>
                      </div>

                      <div class="client-drawer-field">
                        <label class="cdf-label" for="client_drawer_phone">Phone Number<span class="cdf-required">*</span></label>
                        <div class="cdf-input-wrap">
                          <i class="la la-phone cdf-input-icon" aria-hidden="true"></i>
                          <input type="number" id="client_drawer_phone" placeholder="Enter 10-digit phone number" autocomplete="off">
                        </div>
                        <div class="cdf-error" id="cdf_err_phone">Please enter a valid 10-digit phone number.</div>
                      </div>
                    </div>
                    <!-- Footer -->
                    <div class="client-drawer-foot">
                      <button type="button" class="cdf-cancel-btn" id="client_cancel_contact_drawer">Cancel</button>
                      <button type="button" class="cdf-save-btn" id="client_save_contact_drawer">
                        <span class="cdf-spinner"></span>
                        <i class="la la-user-plus cdf-btn-icon" aria-hidden="true"></i>
                        <span class="cdf-btn-text">Add Contact Person</span>
                      </button>
                    </div>
                  </div>
                  <!--end::Contact Person-->
                  <!--begin::Review and Submit-->
                  <div class="pb-5 client-review-step" data-wizard-type="step-content">
                    <div class="client-step-header mb-3">
                      <div>
                        <h3>Review &amp; Submit</h3>
                        <p>Review all the information before submitting. You can edit any section if needed.</p>
                      </div>
                    </div>
                    <div class="client-review-layout">
                      <div class="client-review-main">
                        <section class="client-review-card">
                          <div class="client-review-card-head">
                            <h4>Client Information</h4>
                            <button type="button" class="client-review-edit" data-client-goto-step="1"><i class="la la-pencil"></i> Edit</button>
                          </div>
                          <div class="client-review-logo" id="client_review_logo" style="background-image: url(<?php echo @$background_image; ?>);"></div>
                          <div class="client-review-grid">
                            <div class="client-review-item"><label>Client Name</label><span class="span_name">&nbsp;</span></div>
                            <div class="client-review-item"><label>Industry</label><span class="span_industry">&nbsp;</span></div>
                            <div class="client-review-item"><label>Website</label><a href="javascript:void(0);" class="span_website">&nbsp;</a></div>
                            <div class="client-review-item"><label>Company Size</label><span class="span_company_sizes">&nbsp;</span></div>
                            <div class="client-review-item"><label>Company Type</label><span class="span_company_type">&nbsp;</span></div>
                            <div class="client-review-item"><label>Founded Year</label><span class="span_founded">&nbsp;</span></div>
                          </div>
                        </section>

                        <section class="client-review-card">
                          <div class="client-review-card-head">
                            <h4>Client Description</h4>
                            <button type="button" class="client-review-edit" data-client-goto-step="2"><i class="la la-pencil"></i> Edit</button>
                          </div>
                          <div class="client-review-item mb-3"><label>About the Client</label></div>
                          <p class="span_kt_docs_tinymce_hidden text-justify mb-3" style="font-size:11px;color:#53607e;">&nbsp;</p>
                          <div class="client-review-item"><label>Services</label><span class="span_tags">&nbsp;</span></div>
                        </section>

                        <section class="client-review-card">
                          <div class="client-review-card-head">
                            <h4>Address Details</h4>
                            <button type="button" class="client-review-edit" data-client-goto-step="3"><i class="la la-pencil"></i> Edit</button>
                          </div>
                          <div class="client-review-addresses divAddressDetail">&nbsp;</div>
                        </section>

                        <section class="client-review-card">
                          <div class="client-review-card-head">
                            <h4>Contact Persons</h4>
                            <button type="button" class="client-review-edit" data-client-goto-step="4"><i class="la la-pencil"></i> Edit</button>
                          </div>
                          <div class="client-contact-table-wrap">
                            <table class="client-contact-table">
                              <thead>
                                <tr>
                                  <th>Contact Person</th>
                                  <th>Designation</th>
                                  <th>Email</th>
                                  <th>Phone Number</th>
                                  <th>Type</th>
                                </tr>
                              </thead>
                              <tbody class="div-contact-person">&nbsp;</tbody>
                            </table>
                          </div>
                        </section>
                      </div>

                      <aside class="client-side-column">
                        <div class="client-summary-card">
                          <span class="client-summary-icon"><i class="la la-building"></i></span>
                          <div class="client-summary-body"><h5>Client Information</h5><p class="client_summary_name">—</p></div>
                          <button type="button" class="client-summary-edit" data-client-goto-step="1"><i class="la la-pencil"></i></button>
                        </div>
                        <div class="client-summary-card">
                          <span class="client-summary-icon"><i class="la la-file-alt"></i></span>
                          <div class="client-summary-body"><h5>Description</h5><p class="client_summary_desc">—</p></div>
                          <button type="button" class="client-summary-edit" data-client-goto-step="2"><i class="la la-pencil"></i></button>
                        </div>
                        <div class="client-summary-card">
                          <span class="client-summary-icon"><i class="la la-map-marker"></i></span>
                          <div class="client-summary-body"><h5>Address</h5><p class="client_summary_address">—</p></div>
                          <button type="button" class="client-summary-edit" data-client-goto-step="3"><i class="la la-pencil"></i></button>
                        </div>
                        <div class="client-summary-card">
                          <span class="client-summary-icon"><i class="la la-user"></i></span>
                          <div class="client-summary-body"><h5>Contacts</h5><p class="client_summary_contacts">—</p></div>
                          <button type="button" class="client-summary-edit" data-client-goto-step="4"><i class="la la-pencil"></i></button>
                        </div>
                        <div class="client-almost-done">
                          <i class="la la-check-circle" aria-hidden="true"></i>
                          <h5>You're Almost Done!</h5>
                          <p>Review the details above and submit to create the client profile.</p>
                        </div>
                      </aside>
                    </div>
                  </div>
                  <!--end::Review and Submit-->
                  <!--begin::Wizard Actions-->
                  <div class="d-flex border-top mt-5 pt-10">
                    <div class="client-action-left">
                      <a href="{{ route('service_company.clients.index') }}" class="btn client-cancel-btn"><i class="la la-times"></i> Cancel</a>
                      <button type="button" class="btn client-draft-btn" id="client_save_draft"><i class="la la-bookmark"></i> Save as Draft</button>
                    </div>
                    <div class="client-action-right ms-auto">
                      <button type="button" class="btn client-cancel-btn" data-wizard-type="action-prev"><i class="la la-arrow-left"></i> Back</button>
                      <div class="client-submit-wrap">
<?php
if ($page_event == 'EDIT') {
    echo '<button type="button" id="submitBtn" class="btn client-submit-btn updatefrm" data-wizard-type="action-submit1" style="display:none"><i class="la la-paper-plane"></i> Update Client</button>';
} else {
    echo '<button type="button" id="submitBtn" class="btn client-submit-btn submitfrm" data-wizard-type="action-submit1" style="display:none"><i class="la la-paper-plane"></i> Submit Client</button>';
}
?>
                        <button type="button" id="continueButton" class="btn btn-primary" data-wizard-type="action-next">Continue <i class="la la-arrow-right"></i></button>
                        <p class="client-submit-note">After submission, you can't edit basic details.</p>
                      </div>
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

      // Restrict to 10 digits
      if (this.value.length > 10) {
          this.value = this.value.slice(0, 10);
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

              error: function () {
                  $('.page-loader').hide();

                  Swal.fire({
                      text: "Server error, please try again",
                      icon: "error"
                  });
              }
          });
      }
  });

  $(document).ready(function () {
    // Initialize intl-tel-input for existing contact number fields
      function initializeIntlTelInput(inputElement,initialCountry='in') {
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
      const itiInstance = window.intlTelInputGlobals.getInstance(inputField);

      console.log(inputField);
      console.log($(inputField).val());
         console.log($(inputField).closest(".iti").html());
          if (itiInstance) {
              const selectedCountryData = itiInstance.getSelectedCountryData();
              const countryCode = selectedCountryData.iso2; // e.g., 'us', 'in', etc.
              // alert(countryCode);
              // Assuming you have a hidden input to store the country code
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
   
   const cities = @json($cities);
   var countAddrees  = "{{ $countAddrees }}";
for (let i = 0; i < countAddrees; i++) {
    let inputId = `#kt_tagify_work_location_${i}`;
    let inputElement = document.querySelector(inputId);
    var cityDataGet = $(inputId).val();
    var enforceWhitelist = true;
    if(page_event=='EDIT'){
      enforceWhitelist = false;
    }
    

    if (inputElement) {

      
        let tagifyInstance = new Tagify(inputElement, {
            whitelist: cities,
            enforceWhitelist: enforceWhitelist, // Prevents users from adding custom values
            maxTags: 1,
            dropdown: {
                maxItems: 20,           // maximum allowed rendered suggestions
                classname: "", // custom classname for this dropdown
                enabled: 0,             // show suggestions on focus
                closeOnSelect: true    // do not hide the suggestions dropdown once an item has been selected
            }
        });


        // Prevent creating tags on blur or enter key
        tagifyInstance.on('input', function(e) {
            tagifyInstance.dropdown.show(); // show the dropdown immediately
        });

        // Create tags only on select
        tagifyInstance.on('dropdown:select', function(e) {
            tagifyInstance.addTags([e.detail.data.value]);
        });

        tagifyInstance.on('add', function(e) {
          // Remove the tag if it wasn't selected from the dropdown
          // if (!cities.includes(e.detail.data.value)) {
          //   tagifyInstance.removeTag(e.detail.data.value);
          // }
        });


         function debounce(func, delay) {     
            let timer;
            return function (...args) {
                clearTimeout(timer);
                timer = setTimeout(() => func.apply(this, args), delay);
            };
        }

        const fetchSuggestions = debounce(function(query) {
            $.ajax({
                url: "{{ url('/') }}" + "/city-job-suggestions?q=" + query,
                method: 'GET',
                success: function(data) {
                    console.warn("Fetched Cities: ", data);

                    if (Array.isArray(data)) {
                      tagifyInstance.settings.whitelist = data; 
                      // tagifyInstance.dropdown.show(query); 
                      tagifyInstance.dropdown.show.call(tagifyInstance);
                    } else {
                        console.error("Invalid API response format: Expected array.");
                    }
                },
                error: function(err) {
                    console.error("Error fetching cities:", err);
                }
            });
        }, 200); // 300ms delay
        tagifyInstance.on('input', function(e) {
            var query = e.detail.value.trim();
            if (query.length < 2) return;
            fetchSuggestions(query);
        });

        // setTimeout(() => {
        //   var city = cityDataGet;
        //   if(city !=''){
        //     var cityArray = city.split(","); // Split multiple locations
        //     var cityNames = cityArray.map(entry => entry.split(" | ")[0]); // Extract city names
            
        //     fetchSuggestions(cityNames.join(", ")); 
        //   }
        // }, 100);
        // setTimeout(() => {
        //      var city = cityDataGet;
        //     //  alert(cityDataGet);
        //     //  var defaultLocation = JSON.stringify(city);// Ensure proper Blade syntax for JavaScript
        //     //  alert(defaultLocation);
        //     if (cityDataGet) {
        //       tagifyInstance.addTags([cityDataGet]); // Add the default location as a tag
        //     }
        // }, 3000); 


    }
}
   	
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
   
   		$('.add_address').click(function (){	
   	
   	
					var addressId			=	$('#addressId').val();
					addressId++;
					$('#addressId').val(addressId);
				   
					var clientAddr_id		=	"clientAddr_"+addressId;
					var address_id			=	"address_"+addressId;
					var country_id			=	"country_"+addressId;	
					var state_id			=	"state_"+addressId;
					var city_id				=	"city_"+addressId;
					var postcode_id			=	"postcode_"+addressId;
						
					var countryListing		=	'';
				   
				   $('#addressLoader').show();
				   $('#addressLoader').html('');
					
				   var baseurl    		=	"{{ asset('assets/images/Loading2.gif') }}"; 	
				   var cid				=	addressId ;
				   var url 				=	"{{ route('service_company.clients.addresslist',':cid') }}";
				   url 					=	url.replace(':cid', cid);
				   $.ajax({
									type		: 'GET',
									url			: url,
									 beforeSend	: function() {
																
																	$('#addressLoader').html('<img src="'+baseurl+'" height="35px" width="100%">');
															},
									success		: function (response) {																		
																		$('#addressLoader').hide();
																		$('.client-address-container').last().append(response.data);
                                    if (window.clientWizardUi) {
                                      window.clientWizardUi.syncAdditionalAddressEmpty();
                                      window.clientWizardUi.bindAddressCounters(document);
                                    }
																  }		
						});		
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
    	
   		$(document).on('click', '.btn', function() { 
   	
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
   			$('.span_name').html(name);			
   			var website			=	$('#website').val();
   			$('.span_website').html(website).attr('href', website);			
   			var industry		=	$('#industry option:selected').text(); 
   			$('.span_industry').html(industry);			
   			var company_sizes	=	$('#company_sizes').val(); 
   			$('.span_company_sizes').html(company_sizes ? company_sizes + ' employees' : '');			
   			var company_type	=	$('#company_type option:selected').text(); 
   			$('.span_company_type').html(company_type);			
   			var founded			=	$('#founded').val(); 
   			$('.span_founded').html(founded);
   			
   			var kt_docs_tinymce_hidden = tinymce.get("kt_docs_tinymce_hidden").getContent();
			$('.span_kt_docs_tinymce_hidden').html(kt_docs_tinymce_hidden);
			
   			var tags = $('#tags').val(); 	
   			var json_tags = tags ? $.parseJSON(tags) : [];
   			var tagLabels = [];
   			for(let len=0; len<json_tags.length; len++){
   				tagLabels.push(json_tags[len].value);
   			}
   			$('.span_tags').html(tagLabels.join(', '));

   			var addressId = $('#addressId').val();
   			var addressBlocks = [];
   			for(let cntAdd=0; cntAdd<=addressId; cntAdd++)
   			{
   				var address = $('#address_'+cntAdd).val();
   				var cextractedValueity = $('#kt_tagify_work_location_'+cntAdd).val();
   				if (typeof address === 'undefined') continue;
            var city = cextractedValueity
                    .replace('[{"value":"', '')
                    .replace('"}]', '');
            var title = cntAdd === 0 ? 'Headquarters (Primary)' : 'Additional Address ('+cntAdd+')';
            addressBlocks.push('<div class="client-review-address-box"><h5>'+title+'</h5><p><strong>'+city+'</strong><br>'+address+'</p></div>');
   			}
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
            contactRows.push('<tr><td><div class="client-contact-person"><span class="client-contact-avatar">'+cp_name.trim().split(/\s+/).slice(0,2).map(function(p){return p.charAt(0).toUpperCase();}).join('')+'</span><span class="client-contact-name">'+cp_name+primaryTag+'</span></div></td><td>'+(cp_desigination || '—')+'</td><td>'+(cp_email || '—')+'</td><td>'+(cp_number || '—')+'</td><td>'+typeBadge+'</td></tr>');
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
   
   	var data 			=	new FormData();
   	var form_data 		=	$('#kt_form').serializeArray();

      $(this).prop("disabled", true);
      $(this).html(
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

    // $(this).prop("disabled", true);
    $(this).html(
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


 
  function clientDomainRegisterChk(){

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
  data.append("check_client_id", "{{ @$client->id }}");

  var website = $('#website').val();

    
    if(website != '')
      {
        $('.page-loader').css('display', 'flex');
          $.ajax({
              url: "{{route('client-domain-register-chk')}}",
              headers				: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
              type: "POST",
              processData			: false,
              contentType			: false,
              data: data,
              success: function(response) {
                $('.page-loader').css('display', 'none');                            
                                  if(response.status == 'fail')
                                  {
                                      Swal.fire("Warning", response.message, "warning");
                                      $('.updatefrm').prop("disabled", true);
                                      $('#website').val('');
                                    if (formValidationInstance && typeof formValidationInstance.revalidateField === 'function') {
                                        formValidationInstance.revalidateField('website');
                                    }
                                  }else{
                                    $("#client_id").val(response.clientName);
                                      $('.image-input-wrapper').css('background-image', 'url(' + response.logo_url + ')');
                                      if (window.clientWizardUi) {
                                        window.clientWizardUi.updateAssistantPanel();
                                        window.clientWizardUi.updateCompletion();
                                      }
                                  }
                            }
            });             		
      }


    return false;

  }

</script>
<script src="{{ asset('demo/v7/js/add-clients.js') }}<?php echo TM_VERSION; ?>"></script>
<script>
/**
 * Purpose: client create wizard UX layer — completion meter, assistant panel, contact drawer,
 * review summaries, and draft persistence without changing the underlying submit contract.
 */
(function ($) {
  var draftKey = 'jero_client_create_draft';

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
      company_sizes: $('#company_sizes').val() ? $('#company_sizes').val() + ' employees' : 'Not set',
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
      $('input[name="kt_tagify_work_location[]"]').first().val(),
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
    var extraCount = $('.client-address-container .client-address-container-child.is-additional, .client-address-container .client-address-card.is-additional').length;
    $('#client_additional_empty').toggle(extraCount === 0);
  }

  function getContactRowsData() {
    var rows = [];
    $('.client-cperson-container').each(function (idx) {
      var index = $(this).data('contact-index');
      if (typeof index === 'undefined') index = idx;
      var name = $('#cp_name_' + index).val() || '';
      if (!name.trim()) return;
      rows.push({
        index: index,
        name: name.trim(),
        email: ($('#cp_email_' + index).val() || '').trim(),
        phone: ($('#cp_number_' + index).val() || '').trim(),
        designation: ($('#cp_desigination_' + index).val() || '').trim(),
        callingCode: ($('#calling_code_' + index).val() || 'in')
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
          '<td><div class="client-contact-person"><span class="client-contact-avatar">' + initials(row.name) + '</span><span class="client-contact-name">' + row.name + primaryTag + '</span></div></td>' +
          '<td>' + (row.designation || '—') + '</td>' +
          '<td>' + (row.email || '—') + '</td>' +
          '<td>' + (row.phone || '—') + '</td>' +
          '<td>' + typeBadge + '</td>' +
          '<td><div class="client-table-actions">' +
            '<button type="button" class="client-edit-contact" data-index="' + row.index + '" title="Edit"><i class="la la-pencil"></i></button>' +
            '<button type="button" class="client-delete-contact is-delete" data-index="' + row.index + '" title="Delete"><i class="la la-trash"></i></button>' +
          '</div></td>' +
        '</tr>'
      );
    });
  }

  function _cdfClearErrors() {
    $('#client_drawer_name, #client_drawer_designation, #client_drawer_email, #client_drawer_phone').removeClass('is-invalid');
    $('.cdf-error').removeClass('visible');
  }

  function openContactDrawer(index) {
    _cdfClearErrors();
    $('#client_drawer_edit_index').val(index === null ? '' : index);
    var isEdit = index !== null;
    $('#client_contact_drawer_title').text(isEdit ? 'Edit Contact Person' : 'Add Contact Person');
    $('#client_contact_drawer_subtitle').text(isEdit ? 'Update the contact details below.' : 'Add the primary or secondary contact details for this client.');
    $('#client_save_contact_drawer .cdf-btn-text').text(isEdit ? 'Save Changes' : 'Add Contact Person');
    $('#client_save_contact_drawer .cdf-btn-icon').toggleClass('la-user-plus', !isEdit).toggleClass('la-save', isEdit);
    if (!isEdit) {
      $('#client_drawer_name, #client_drawer_designation, #client_drawer_email, #client_drawer_phone').val('');
    } else {
      $('#client_drawer_name').val($('#cp_name_' + index).val());
      $('#client_drawer_designation').val($('#cp_desigination_' + index).val());
      $('#client_drawer_email').val($('#cp_email_' + index).val());
      $('#client_drawer_phone').val($('#cp_number_' + index).val());
    }
    $('#client_contact_drawer, #client_contact_drawer_backdrop').addClass('is-open').attr('aria-hidden', 'false');
    setTimeout(function () { $('#client_drawer_name').focus(); }, 280);
  }

  function closeContactDrawer() {
    $('#client_contact_drawer, #client_contact_drawer_backdrop').removeClass('is-open').attr('aria-hidden', 'true');
    _cdfClearErrors();
  }

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
    if (!phone || phone.length !== 10 || isNaN(phone)) {
      $('#client_drawer_phone').addClass('is-invalid');
      $('#cdf_err_phone').text(!phone ? 'Phone number is required.' : 'Please enter a valid 10-digit phone number.').addClass('visible');
      $firstBad = $firstBad || $('#client_drawer_phone');
      valid = false;
    }
    if (!valid) {
      if ($firstBad) { $firstBad.focus(); $('.client-drawer-body').animate({ scrollTop: $firstBad.closest('.client-drawer-field').position().top - 10 }, 200); }
      return;
    }

    /* Loading state */
    var $btn = $('#client_save_contact_drawer');
    $btn.prop('disabled', true).addClass('is-loading');
    var origText = $btn.find('.cdf-btn-text').text();
    $btn.find('.cdf-btn-text').text('Saving…');

    /* Simulate brief async so UI updates (save is synchronous/local — restore immediately) */
    setTimeout(function () {
      var editIndex = $('#client_drawer_edit_index').val();
      if (editIndex === '') {
        var targetIndex = null;
        $('.client-cperson-container').each(function () {
          var idx = $(this).data('contact-index');
          if (!$('#cp_name_' + idx).val().trim()) { targetIndex = idx; return false; }
        });
        if (targetIndex === null) {
          $('.add_cperson').trigger('click');
          targetIndex = parseInt($('#contactPersonId').val(), 10);
          $('.client-cperson-container').last().attr('data-contact-index', targetIndex);
        }
        $('#cp_name_' + targetIndex).val(name);
        $('#cp_desigination_' + targetIndex).val(designation);
        $('#cp_email_' + targetIndex).val(email);
        $('#cp_number_' + targetIndex).val(phone);
      } else {
        $('#cp_name_' + editIndex).val(name);
        $('#cp_desigination_' + editIndex).val(designation);
        $('#cp_email_' + editIndex).val(email);
        $('#cp_number_' + editIndex).val(phone);
      }
      renderContactTable();
      updateCompletion();
      closeContactDrawer();
      $btn.prop('disabled', false).removeClass('is-loading');
      $btn.find('.cdf-btn-text').text(origText);
    }, 180);
  }

  /* Clear individual field error on input */
  $(document).on('input', '#client_drawer_name', function () { $(this).removeClass('is-invalid'); $('#cdf_err_name').removeClass('visible'); });
  $(document).on('input', '#client_drawer_designation', function () { $(this).removeClass('is-invalid'); $('#cdf_err_designation').removeClass('visible'); });
  $(document).on('input', '#client_drawer_email', function () { $(this).removeClass('is-invalid'); $('#cdf_err_email').removeClass('visible'); });
  $(document).on('input', '#client_drawer_phone', function () { $(this).removeClass('is-invalid'); $('#cdf_err_phone').removeClass('visible'); });

  function populateReviewSummaries() {
    $('.client_summary_name').text($('#client_id').val() || '—');
    var descText = (typeof tinymce !== 'undefined' && tinymce.get('kt_docs_tinymce_hidden'))
      ? tinymce.get('kt_docs_tinymce_hidden').getContent({ format: 'text' }).trim()
      : '';
    $('.client_summary_desc').text(descText ? descText.substring(0, 80) + (descText.length > 80 ? '…' : '') : '—');
    var firstCity = $('input[name="kt_tagify_work_location[]"]').first().val();
    var cityLabel = parseTagifyValue(firstCity)[0] || firstCity || '—';
    $('.client_summary_address').text(cityLabel);
    var contacts = getContactRowsData();
    $('.client_summary_contacts').text(contacts.length ? contacts.length + ' contact(s)' : '—');
    $('#client_review_logo').css('background-image', $('.image-input-wrapper').css('background-image'));
    $('#client_preview_company').text($('#client_id').val() || 'Company Name');
  }

  window.clientWizardUi = {
    syncAdditionalAddressEmpty: syncAdditionalAddressEmpty,
    bindAddressCounters: bindAddressCounters,
    populateReviewSummaries: populateReviewSummaries,
    updateAssistantPanel: updateAssistantPanel,
    updateCompletion: updateCompletion,
    renderContactTable: renderContactTable
  };

  $(function () {
    bindAddressCounters(document);
    syncAdditionalAddressEmpty();
    updateAssistantPanel();
    updateCompletion();
    renderContactTable();

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

    $('#client_open_contact_drawer').on('click', function () { openContactDrawer(null); });
    $('#client_close_contact_drawer, #client_cancel_contact_drawer, #client_contact_drawer_backdrop').on('click', closeContactDrawer);
    $('#client_save_contact_drawer').on('click', saveContactDrawer);
    $(document).on('click', '.client-edit-contact', function () { openContactDrawer($(this).data('index')); });
    $(document).on('click', '.client-delete-contact', function () {
      var index = $(this).data('index');
      $('.client-cperson-container[data-contact-index="' + index + '"]').remove();
      renderContactTable();
      updateCompletion();
    });

    $(document).on('click', '.remove_address_single', function () {
      var target = $(this).data('target');
      $('#' + target).remove();
      syncAdditionalAddressEmpty();
    });

    $(document).on('click', '[data-client-goto-step]', function () {
      var step = parseInt($(this).data('client-goto-step'), 10);
      if (window.KTWizard && window.KTWizard.getInstance && $('#kt_wizard').length) {
        var wizard = KTWizard.getInstance($('#kt_wizard')[0]);
        if (wizard) wizard.goTo(step);
      }
    });

    $('#client_save_draft').on('click', function () {
      var draft = $('#kt_form').serializeArray();
      if (typeof tinymce !== 'undefined' && tinymce.get('kt_docs_tinymce_hidden')) {
        draft.push({ name: 'description_draft', value: tinymce.get('kt_docs_tinymce_hidden').getContent() });
      }
      localStorage.setItem(draftKey, JSON.stringify(draft));
      $('.client-completion-status').html('<i class="la la-check-circle"></i> Draft saved locally');
    });

    var existingDraft = localStorage.getItem(draftKey);
    if (existingDraft && page_event === 'ADD') {
      try {
        var draftData = JSON.parse(existingDraft);
        draftData.forEach(function (item) {
          var $field = $('[name="' + item.name + '"]');
          if ($field.length) $field.val(item.value).trigger('change');
        });
      } catch (e) {}
    }

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
