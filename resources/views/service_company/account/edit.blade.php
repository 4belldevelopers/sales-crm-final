@extends('layouts.service_company_layout')
@push('head-script')
<link href="{{ asset('demo/v7/css/wizard-1.css') }}<?php echo TM_VERSION; ?>" rel="stylesheet" type="text/css" />
<link href="{{ asset('demo/v7/css/plugins.bundle.css') }}<?php echo TM_VERSION; ?>" rel="stylesheet" type="text/css" />
<link href="{{ asset('demo/v7/css/prismjs.bundle.css') }}<?php echo TM_VERSION; ?>" rel="stylesheet" type="text/css" />
<link href="{{ asset('demo/v7/css/style.bundle.css') }}<?php echo TM_VERSION; ?>" rel="stylesheet" type="text/css" />
<style>
/* Purpose: account edit wizard — visual layer aligned to the Client Create/Edit design system. Form contract (names, ids, routes) is unchanged. */
:root {
  --acc-primary: #1769f5;
  --acc-primary-soft: #edf4ff;
  --acc-ink: #0d194d;
  --acc-muted: #68738d;
  --acc-border: #dde4ef;
  --acc-surface: #ffffff;
  --acc-page: #f7f9fc;
}
body { overflow-x: hidden; width: 100%; font-family: Poppins, sans-serif; }
#kt_content { background: var(--acc-page); }
#kt_toolbar { background: var(--acc-page); padding-top: 14px; }
#kt_toolbar .page-title { align-items: flex-start !important; }
#kt_toolbar .page-title h1,
#kt_toolbar .page-title h1 a { font-size: 16px !important; line-height: 1.25; color: var(--acc-ink) !important; }
#kt_subheader_total { font-size: 12px; color: var(--acc-muted) !important; border: 0 !important; margin: 2px 0 0 !important; font-weight: 400 !important; }
.form-label { margin-bottom: 0.5rem !important; font-size: 12px !important; font-weight: 600 !important; color: var(--acc-ink) !important; }
/* ── Wizard shell ── */
.acc-wizard-shell.card { background: transparent; box-shadow: none; }
/* ── Step nav ── */
#kt_contact_add .wizard-nav {
  background: var(--acc-surface);
  border: 1px solid var(--acc-border) !important;
  border-radius: 10px;
  box-shadow: 0 2px 8px rgba(17,28,68,.03);
  overflow: hidden;
}
#kt_contact_add .wizard-steps {
  display: grid !important;
  grid-template-columns: repeat(4, minmax(0,1fr));
  padding: 14px 18px !important;
  gap: 0;
}
#kt_contact_add .wizard-step {
  display: flex !important;
  align-items: center;
  min-width: 0;
  position: relative;
  padding-right: 26px;
}
#kt_contact_add .wizard-step:not(:last-child)::after {
  content: '';
  height: 1px;
  background: #dce3ee;
  position: absolute;
  right: 8px;
  width: 20px;
  top: 18px;
}
#kt_contact_add .wizard-label { display: grid !important; grid-template-columns: 36px minmax(0,1fr); column-gap: 10px; width: 100%; align-items: center; }
#kt_contact_add .wizard-icon {
  grid-row: 1 / span 2;
  width: 36px !important; height: 36px !important;
  display: grid !important; place-items: center;
  border-radius: 50%;
  background: #f2f5f9; border: 1px solid #dce3ee;
  color: #344054 !important;
}
#kt_contact_add .wizard-icon svg { display: none; }
#kt_contact_add .wizard-step:nth-child(1) .wizard-icon::before { content: '1'; font-size: 12px; font-weight: 600; }
#kt_contact_add .wizard-step:nth-child(2) .wizard-icon::before { content: '2'; font-size: 12px; font-weight: 600; }
#kt_contact_add .wizard-step:nth-child(3) .wizard-icon::before { content: '3'; font-size: 12px; font-weight: 600; }
#kt_contact_add .wizard-step:nth-child(4) .wizard-icon::before { content: '4'; font-size: 12px; font-weight: 600; }
#kt_contact_add .wizard-title { font-size: 12px !important; font-weight: 600 !important; color: var(--acc-ink) !important; margin: 0 !important; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
#kt_contact_add .wizard-title::after { display: block; margin-top: 2px; color: #7b8497; font-size: 10.5px; font-weight: 400; }
#kt_contact_add .wizard-step:nth-child(1) .wizard-title::after { content: 'Basic details'; }
#kt_contact_add .wizard-step:nth-child(2) .wizard-title::after { content: 'About the company'; }
#kt_contact_add .wizard-step:nth-child(3) .wizard-title::after { content: 'Location & address'; }
#kt_contact_add .wizard-step:nth-child(4) .wizard-title::after { content: 'Confirm & save'; }
#kt_contact_add .wizard-arrow { display: none !important; }
#kt_contact_add .wizard-step[data-wizard-state="current"] .wizard-icon,
#kt_contact_add .wizard-step[data-wizard-state="done"] .wizard-icon {
  background: var(--acc-primary); border-color: var(--acc-primary);
  color: #fff !important; box-shadow: 0 4px 10px rgba(37,99,235,.22);
}
#kt_contact_add .wizard-step[data-wizard-state="done"] .wizard-icon::before { content: '✓'; font-size: 14px; }
#kt_contact_add .wizard-step[data-wizard-state="current"] .wizard-title { color: var(--acc-primary) !important; }
#kt_contact_add, #kt_contact_add > .row, #kt_contact_add > .row > div, #kt_contact_add_form { width: 100%; max-width: 100%; min-width: 0; }
#kt_contact_add > .row { margin: 14px 0 0 !important; padding: 0 !important; }
#kt_contact_add > .row > div { padding: 0 !important; }
/* ── Step content panels ── */
#kt_contact_add_form > [data-wizard-type="step-content"] {
  background: var(--acc-surface); border: 1px solid var(--acc-border);
  border-radius: 10px; padding: 18px !important; min-height: 330px;
  box-shadow: 0 2px 8px rgba(17,28,68,.03);
}
#kt_contact_add_form > [data-wizard-type="step-content"] > h3 { font-size: 14px !important; margin-bottom: 16px !important; color: var(--acc-ink) !important; }
#kt_contact_add_form .form-control,
#kt_contact_add_form .form-select,
#kt_contact_add_form .input-group-text,
#kt_contact_add_form .tagify { min-height: 38px; font-size: 12px !important; border-color: #dce3ee !important; background-color: #fff !important; }
#kt_contact_add_form .form-control:focus,
#kt_contact_add_form .form-select:focus,
#kt_contact_add_form .tagify:focus-within { border-color: var(--acc-primary) !important; box-shadow: 0 0 0 3px rgba(37,99,235,.1) !important; }
#kt_contact_add_form .form-text { font-size: 10.5px !important; color: #7b8497 !important; margin-top: 5px; }
/* ── Step 1 two-column layout ── */
.acc-info-step { background: transparent !important; border: 0 !important; padding: 0 !important; box-shadow: none !important; min-height: auto !important; }
.acc-step-layout { display: grid; width: 100%; max-width: 100%; min-width: 0; grid-template-columns: minmax(0,1fr) 280px; gap: 14px; align-items: start; }
.acc-main-column { min-width: 0; }
.acc-side-column { min-width: 0; }
/* ── Section cards ── */
.acc-section-card { background: #fff; border: 1px solid var(--acc-border); border-radius: 10px; padding: 18px; margin-bottom: 14px; box-shadow: 0 2px 8px rgba(17,28,68,.04); }
.acc-main-column .acc-section-card:last-child { margin-bottom: 0; }
.acc-section-heading { font-size: 13px; font-weight: 600; color: var(--acc-ink); margin: 0 0 3px; }
.acc-section-copy { font-size: 10.5px; color: var(--acc-muted); margin: 0 0 15px; }
/* ── Branding grid ── */
.acc-branding-grid { display: grid; width: 100%; min-width: 0; grid-template-columns: 190px 130px minmax(0,1fr); gap: 16px; align-items: stretch; }
.acc-logo-upload { border: 1px dashed #8db5ff; border-radius: 8px; background: #fbfdff; min-height: 152px; padding: 14px; display: flex; cursor: pointer; flex-direction: column; align-items: center; justify-content: center; text-align: center; transition: border-color .2s,background .2s; }
.acc-logo-upload:hover { border-color: var(--acc-primary); background: #f5f9ff; }
.acc-upload-icon { display: grid; place-items: center; width: 42px; height: 42px; margin-bottom: 6px; border-radius: 50%; background: var(--acc-primary-soft); color: var(--acc-primary); font-size: 23px; }
.acc-logo-upload .acc-upload-title { font-size: 11.5px; color: var(--acc-primary); font-weight: 600; }
.acc-logo-upload .acc-upload-copy { font-size: 10px; color: #7b8497; margin-top: 3px; }
.acc-logo-preview { border: 1px solid var(--acc-border); border-radius: 8px; overflow: hidden; min-height: 152px; display: flex; flex-direction: column; background: #fff; }
.acc-preview-label { padding: 9px 11px 0; color: var(--acc-ink); font-size: 10.5px; font-weight: 600; }
.acc-logo-preview .image-input { margin: auto; }
.acc-logo-preview .image-input-wrapper { width: 72px !important; height: 72px !important; border-radius: 10px; background-size: cover; background-position: center; background-color: #f1efff; }
.acc-logo-preview .btn[data-kt-image-input-action="change"] { display: none; }
.acc-preview-remove { border: 0; border-top: 1px solid var(--acc-border); background: #fff; min-height: 32px; color: #ef3858; font-size: 10.5px; font-weight: 500; width: 100%; cursor: pointer; }
.acc-brand-fields { display: grid; min-width: 0; grid-template-columns: minmax(0,1fr); gap: 14px; align-content: center; }
/* ── Details 2-col grid ── */
.acc-details-grid { display: grid; grid-template-columns: repeat(2,minmax(0,1fr)); gap: 14px 18px; }
.acc-field, .acc-field .input-group, .acc-field .form-control, .acc-field .form-select { min-width: 0; }
.acc-field .form-label { display: block; }
#kt_contact_add_form .acc-field .input-group {
  display: flex; flex-wrap: nowrap; min-height: 38px;
  border: 1px solid #d5deeb !important; border-radius: 6px !important;
  background: #fff !important; overflow: hidden;
}
#kt_contact_add_form .acc-field .input-group:focus-within { border-color: var(--acc-primary) !important; box-shadow: 0 0 0 3px rgba(23,105,245,.1); }
#kt_contact_add_form .acc-field .input-group-text { flex: 0 0 38px; width: 38px; justify-content: center; color: #53607e; border: 0 !important; background: #fff !important; }
#kt_contact_add_form .acc-field .input-group .form-control,
#kt_contact_add_form .acc-field .input-group .form-select {
  flex: 1 1 auto; width: 1%; min-width: 0;
  border: 0 !important; border-radius: 0 !important;
  background-color: #fff !important; color: var(--acc-ink) !important;
  box-shadow: none !important; padding-left: 8px !important;
}
#kt_contact_add_form .acc-field .input-group .form-control::placeholder { color: #8993a8; opacity: 1; }
/* ── Sidebar checklist ── */
.acc-assistant { min-width: 0; overflow: hidden; border: 1px solid #e0e4f5; border-radius: 10px; padding: 18px; background: linear-gradient(145deg,#fbfaff 0%,#f4f6ff 100%); box-shadow: 0 2px 8px rgba(17,28,68,.04); }
.acc-assistant-head { display: flex; justify-content: space-between; gap: 12px; align-items: flex-start; margin-bottom: 15px; }
.acc-assistant-title { font-size: 13px; font-weight: 600; color: #4c38ed; margin: 0; }
.acc-assistant-spark { display: grid; flex: 0 0 48px; width: 48px; height: 48px; place-items: center; border-radius: 10px; background: #fff; color: #6246ea; font-size: 23px; box-shadow: 0 8px 18px rgba(78,56,237,.12); }
.acc-checklist { border: 1px solid #e1e6ef; border-radius: 8px; background: #fff; overflow: hidden; }
.acc-checklist-title { padding: 12px 13px 10px; border-bottom: 1px solid #edf0f5; font-size: 11px; color: var(--acc-ink); font-weight: 600; }
.acc-checklist-row { display: flex; align-items: center; gap: 8px; min-height: 36px; padding: 7px 13px; border-bottom: 1px solid #edf0f5; font-size: 10.5px; color: #53607e; }
.acc-checklist-row:last-child { border-bottom: 0; }
.acc-checklist-row i { font-size: 14px; color: #b7bfce; }
/* ── Steps 2-4 transparent wrappers ── */
.acc-desc-step, .acc-address-step, .acc-review-step { background: transparent !important; border: 0 !important; padding: 0 !important; box-shadow: none !important; min-height: auto !important; }
.acc-step-header { display: flex; align-items: flex-start; justify-content: space-between; gap: 12px; margin-bottom: 14px; }
.acc-step-header h3 { font-size: 14px !important; font-weight: 600 !important; color: var(--acc-ink) !important; margin: 0 !important; }
.acc-step-header p { font-size: 10.5px; color: var(--acc-muted); margin: 4px 0 0; }
/* ── Editor ── */
.acc-editor-wrap { border: 1px solid var(--acc-border); border-radius: 8px; overflow: hidden; background: #fff; }
.acc-editor-toolbar { display: flex; align-items: center; justify-content: space-between; padding: 8px 12px; border-bottom: 1px solid #edf0f5; background: #fafbfd; }
.acc-editor-toolbar span { font-size: 10.5px; color: var(--acc-muted); }
/* ── Address cards ── */
.client-address-container-child { background: #f9f9f940; border-radius: 14px; padding: 7px 0; margin-bottom: 15px; }
.acc-address-card { border: 1px solid var(--acc-border); border-radius: 10px; padding: 16px; margin-bottom: 12px; background: #fff; }
.acc-address-card-head { display: flex; align-items: center; justify-content: space-between; margin-bottom: 12px; }
.acc-address-card-head h4 { font-size: 12px; font-weight: 600; margin: 0; color: var(--acc-ink); }
.acc-add-address-btn { min-height: 34px; padding: 6px 14px; border-radius: 7px; border: 0; background: var(--acc-primary); color: #fff; font-size: 10.5px; font-weight: 600; cursor: pointer; }
.acc-remove-address-btn { min-height: 30px; padding: 4px 10px; border-radius: 6px; border: 1px solid #f5c2cb; background: #fff5f6; color: #e5484d; font-size: 10px; cursor: pointer; }
/* ── Review cards ── */
.acc-review-card { background: #fff; border: 1px solid var(--acc-border); border-radius: 10px; padding: 16px 18px; margin-bottom: 12px; box-shadow: 0 2px 8px rgba(17,28,68,.03); }
.acc-review-card-head { display: flex; align-items: center; justify-content: space-between; margin-bottom: 14px; }
.acc-review-card-head h4 { margin: 0; font-size: 13px; font-weight: 600; color: var(--acc-ink); }
.acc-review-grid { display: grid; grid-template-columns: repeat(2,minmax(0,1fr)); gap: 12px 18px; }
.acc-review-item label { display: block; font-size: 10px; color: #7b8497; margin-bottom: 3px; }
.acc-review-item span { font-size: 11.5px; color: var(--acc-ink); font-weight: 600; }
.acc-almost-done { margin-top: 14px; padding: 14px; border-radius: 10px; background: #eefbf3; border: 1px solid #d5f0de; text-align: center; }
.acc-almost-done i { color: #10a66a; font-size: 22px; margin-bottom: 6px; display: block; }
.acc-almost-done h5 { font-size: 12px; font-weight: 600; margin: 0 0 4px; color: var(--acc-ink); }
.acc-almost-done p { font-size: 10px; color: #53607e; margin: 0; }
/* ── Bottom action bar ── */
#kt_contact_add_form > .d-flex.border-top {
  background: #fff; border: 1px solid var(--acc-border) !important;
  border-radius: 10px; margin-top: 14px !important; padding: 10px 12px !important;
  justify-content: space-between !important; gap: 8px;
}
.acc-action-left, .acc-action-right { display: flex; align-items: center; gap: 8px; }
#kt_contact_add_form > .d-flex.border-top .btn { min-height: 36px; padding: 8px 18px !important; font-size: 11.5px !important; text-transform: none !important; border-radius: 7px; }
.acc-cancel-btn { border: 1px solid #cfd7e6; color: var(--acc-ink); background: #fff; }
.acc-submit-btn { min-width: 150px; background: linear-gradient(90deg,#315efb,#2557e7) !important; border: 0 !important; color: #fff !important; font-weight: 600; }
.acc-submit-btn:hover, .acc-submit-btn:focus, .acc-submit-btn:active { background: linear-gradient(90deg,#2557e7,#1e4fd4) !important; color: #fff !important; }
.tagify .tagify__tag div .tagify__tag-text { color: #0c0c0c !important; font-weight: 500 !important; font-size: 14px !important; }
/* ── Helper cards (Step 2 sidebar) ── */
.acc-helper-card { border-radius: 10px; padding: 16px; margin-bottom: 12px; border: 1px solid transparent; }
.acc-helper-card:last-child { margin-bottom: 0; }
.acc-helper-card h4 { font-size: 12px; font-weight: 600; margin: 0 0 10px; display: flex; align-items: center; gap: 8px; }
.acc-helper-card p, .acc-helper-card li { font-size: 10.5px; color: #53607e; line-height: 1.55; }
.acc-helper-card ul { margin: 0; padding-left: 0; list-style: none; }
.acc-helper-card li { display: flex; align-items: flex-start; gap: 7px; margin-bottom: 7px; }
.acc-helper-card li i { color: #10a66a; margin-top: 2px; }
.acc-helper-blue { background: #eef5ff; border-color: #d7e6ff; }
.acc-helper-purple { background: #f3f0ff; border-color: #e4dcff; }
.acc-helper-green { background: #eefbf3; border-color: #d5f0de; }
/* ── Live preview box ── */
.acc-preview-box { border: 1px solid #dce3ee; border-radius: 8px; padding: 12px; background: #fff; }
.acc-preview-box .preview-title { font-size: 12px; font-weight: 600; color: var(--acc-ink); margin-bottom: 6px; }
.acc-preview-box .preview-body { font-size: 10.5px; color: #53607e; min-height: 48px; }
.acc-preview-tags { margin-top: 8px; display: flex; flex-wrap: wrap; gap: 5px; }
.acc-preview-tag-chip { font-size: 10px; background: var(--acc-primary-soft); color: var(--acc-primary); border-radius: 4px; padding: 2px 7px; font-weight: 500; }
/* ── Char count ── */
.acc-char-count { font-size: 10px; color: #7b8497; }
/* ── Service Offerings tagify ── */
.acc-service-tagify { display: flex !important; flex-wrap: wrap; gap: 4px; align-items: center; border: 1px solid #dce3ee !important; border-radius: 6px !important; padding: 5px 8px !important; background: #fff !important; min-height: 38px; cursor: text; }
.acc-service-tagify:focus-within { border-color: var(--acc-primary) !important; box-shadow: 0 0 0 3px rgba(23,105,245,.1) !important; }
.acc-service-tagify .tagify__tag { background: var(--acc-primary-soft); border-radius: 4px; margin: 2px; padding: 0; height: 24px; border: 0; }
.acc-service-tagify .tagify__tag > div { padding: 0 6px; display: flex; align-items: center; gap: 4px; height: 24px; }
.acc-service-tagify .tagify__tag > div::before { box-shadow: none !important; background: transparent !important; }
.acc-service-tagify .tagify__tag-text { color: var(--acc-primary) !important; font-size: 11px !important; font-weight: 500; }
.acc-service-tagify .tagify__tag__removeBtn { display: inline-flex !important; align-items: center !important; justify-content: center !important; width: 18px !important; height: 18px !important; min-width: 18px !important; border-radius: 50% !important; background: var(--acc-primary) !important; margin: 0 5px 0 5px !important; cursor: pointer !important; flex-shrink: 0 !important; opacity: 1 !important; font-size: 0 !important; }
.acc-service-tagify .tagify__tag__removeBtn::after { content: "×" !important; color: #fff !important; font-size: 14px !important; font-weight: 700 !important; line-height: 1 !important; display: block !important; }
.acc-service-tagify .tagify__tag__removeBtn:hover { background: #1e4fd4 !important; }
.acc-service-tagify .tagify__input { line-height: 26px !important; }
.acc-service-tagify .tagify__input::before { color: #aab4c8 !important; font-size: 11.5px !important; }
/* ── Suggested tags ── */
.acc-suggested-label { font-size: 10.5px; color: var(--acc-muted); display: block; margin-bottom: 6px; }
.acc-suggested-list { display: flex; flex-wrap: wrap; gap: 7px; }
.acc-suggested-tag { display: inline-flex; align-items: center; gap: 5px; padding: 4px 9px; border-radius: 20px; border: 1px solid #d5deeb; background: #fff; font-size: 10.5px; color: #53607e; cursor: pointer; transition: border-color .15s,color .15s; user-select: none; }
.acc-suggested-tag:hover { border-color: var(--acc-primary); color: var(--acc-primary); }
.acc-suggested-tag.is-added { opacity: .38; pointer-events: none; cursor: default; }
.acc-suggest-add { display: grid; place-items: center; width: 15px; height: 15px; border-radius: 50%; background: #e7edf7; color: #53607e; font-size: 9px; border: 0; padding: 0; pointer-events: none; flex-shrink: 0; }
.acc-suggested-tag:hover .acc-suggest-add { background: var(--acc-primary); color: #fff; }
/* ── Responsive ── */
@media (max-width:1199px) {
  #kt_contact_add .wizard-title::after { display: none; }
  #kt_contact_add .wizard-step { padding-right: 16px; }
  #kt_contact_add .wizard-step:not(:last-child)::after { right: 4px; width: 10px; }
  .acc-step-layout { grid-template-columns: minmax(0,1fr) 240px; }
  .acc-branding-grid { grid-template-columns: 170px 120px minmax(0,1fr); gap: 12px; }
}
@media (max-width:991px) {
  .acc-step-layout { grid-template-columns: 1fr; }
  .acc-assistant { min-height: auto; }
  .acc-review-grid { grid-template-columns: 1fr; }
}
@media (max-width:767px) {
  #kt_contact_add .wizard-steps { display: flex !important; overflow-x: auto; padding: 10px !important; }
  #kt_contact_add .wizard-step { min-width: 155px; }
  .acc-branding-grid, .acc-brand-fields, .acc-details-grid { grid-template-columns: 1fr; }
  .acc-logo-upload { min-height: 130px; }
  #kt_contact_add_form > .d-flex.border-top { align-items: stretch; flex-direction: column-reverse; }
  .acc-action-left, .acc-action-right { justify-content: flex-end; }
}
</style>
@endpush

@section('content')
@php
  use App\Helper\GeneralMethod;

  $countAddrees = (count($client_location) > 0) ? count($client_location) : 1;

  $tags = '';
  $cnt  = 0;

  if (!empty(@$account->logo) && strlen(@$account->logo) > 10) {
    $logo_path = @$account->logo;
  } else {
    $logo_path = url('public/avatar.png');
  }
  $tags                = @$client->tags;
  $client_location_arr = $client_location;
@endphp

<!--begin::Toolbar-->
<div class="toolbar" id="kt_toolbar">
  <div id="kt_toolbar_container" class="container-fluid d-flex flex-stack">
    <div data-kt-swapper="true" data-kt-swapper-mode="prepend"
         data-kt-swapper-parent="{default: '#kt_content_container', 'lg': '#kt_toolbar_container'}"
         class="page-title d-flex align-items-center flex-wrap me-3 mb-5 mb-lg-0">
      <div class="d-flex flex-column">
        <h1 class="d-flex align-items-center text-dark fw-bolder fs-3 my-1">
          <a href="javascript:void(0);" class="text-gray-800 mb-1">Company Profile</a>
        </h1>
        <span class="text-dark-800 fw-bold" id="kt_subheader_total">Edit your company details and save changes.</span>
      </div>
    </div>
  </div>
</div>
<!--end::Toolbar-->

<!--begin::Content-->
<div class="content d-flex flex-column flex-column-fluid pt-4 pb-5" id="kt_content">
  <div class="d-flex flex-column-fluid">
    <div class="container-fluid px-3">
      <div class="card card-custom acc-wizard-shell">
        <div class="card-body p-0">

          <!--begin::Wizard-->
          <div class="wizard wizard-1" id="kt_contact_add" data-wizard-state="step-first" data-wizard-clickable="true">

            <!--begin::Wizard Nav-->
            <div class="wizard-nav border-bottom">
              <div class="wizard-steps p-8 p-lg-10">

                <!--1. Company Information-->
                <div class="wizard-step" data-wizard-type="step" data-wizard-state="current">
                  <div class="wizard-label">
                    <span class="svg-icon svg-icon-4x wizard-icon">
                      <svg xmlns="http://www.w3.org/2000/svg" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                        <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                          <rect x="0" y="0" width="24" height="24"/>
                          <path d="M12.2674799,18.2323597 L12.0084872,5.45852451 C12.0004303,5.06114792 12.1504154,4.6768183 12.4255037,4.38993949 L15.0030167,1.70195304 L17.5910752,4.40093695 C17.8599071,4.6812911 18.0095067,5.05499603 18.0083938,5.44341307 L17.9718262,18.2062508 C17.9694575,19.0329966 17.2985816,19.701953 16.4718324,19.701953 L13.7671717,19.701953 C12.9505952,19.701953 12.2840328,19.0487684 12.2674799,18.2323597 Z" fill="#000000" fill-rule="nonzero" transform="translate(14.701953, 10.701953) rotate(-135.000000) translate(-14.701953, -10.701953)"/>
                          <path d="M12.9,2 C13.4522847,2 13.9,2.44771525 13.9,3 C13.9,3.55228475 13.4522847,4 12.9,4 L6,4 C4.8954305,4 4,4.8954305 4,6 L4,18 C4,19.1045695 4.8954305,20 6,20 L18,20 C19.1045695,20 20,19.1045695 20,18 L20,13 C20,12.4477153 20.4477153,12 21,12 C21.5522847,12 22,12.4477153 22,13 L22,18 C22,20.209139 20.209139,22 18,22 L6,22 C3.790861,22 2,20.209139 2,18 L2,6 C2,3.790861 3.790861,2 6,2 L12.9,2 Z" fill="#000000" fill-rule="nonzero" opacity="0.3"/>
                        </g>
                      </svg>
                    </span>
                    <h3 class="wizard-title">1. Company Information</h3>
                  </div>
                  <span class="svg-icon svg-icon-xl wizard-arrow">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                      <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                        <polygon points="0 0 24 0 24 24 0 24"/>
                        <rect fill="#000000" opacity="0.3" transform="translate(12.000000, 12.000000) rotate(-90.000000) translate(-12.000000, -12.000000)" x="11" y="5" width="2" height="14" rx="1"/>
                        <path d="M9.70710318,15.7071045 C9.31657888,16.0976288 8.68341391,16.0976288 8.29288961,15.7071045 C7.90236532,15.3165802 7.90236532,14.6834152 8.29288961,14.2928909 L14.2928896,8.29289093 C14.6714686,7.914312 15.281055,7.90106637 15.675721,8.26284357 L21.675721,13.7628436 C22.08284,14.136036 22.1103429,14.7686034 21.7371505,15.1757223 C21.3639581,15.5828413 20.7313908,15.6103443 20.3242718,15.2371519 L15.0300721,10.3841355 L9.70710318,15.7071045 Z" fill="#000000" fill-rule="nonzero" transform="translate(14.999999, 11.999997) scale(1, -1) rotate(90.000000) translate(-14.999999, -11.999997)"/>
                      </g>
                    </svg>
                  </span>
                </div>

                <!--2. Company Description-->
                <div class="wizard-step" data-wizard-type="step">
                  <div class="wizard-label">
                    <span class="svg-icon svg-icon-4x wizard-icon">
                      <svg xmlns="http://www.w3.org/2000/svg" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                        <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                          <rect x="0" y="0" width="24" height="24"/>
                          <path d="M11,20 L11,17 C11,16.4477153 11.4477153,16 12,16 C12.5522847,16 13,16.4477153 13,17 L13,20 L15.5,20 C15.7761424,20 16,20.2238576 16,20.5 C16,20.7761424 15.7761424,21 15.5,21 L8.5,21 C8.22385763,21 8,20.7761424 8,20.5 C8,20.2238576 8.22385763,20 8.5,20 L11,20 Z" fill="#000000" opacity="0.3"/>
                          <path d="M3,5 L21,5 C21.5522847,5 22,5.44771525 22,6 L22,16 C22,16.5522847 21.5522847,17 21,17 L3,17 C2.44771525,17 2,16.5522847 2,16 L2,6 C2,5.44771525 2.44771525,5 3,5 Z" fill="#000000"/>
                        </g>
                      </svg>
                    </span>
                    <h3 class="wizard-title">2. Company Description</h3>
                  </div>
                  <span class="svg-icon svg-icon-xl wizard-arrow">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                      <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                        <polygon points="0 0 24 0 24 24 0 24"/>
                        <rect fill="#000000" opacity="0.3" transform="translate(12.000000, 12.000000) rotate(-90.000000) translate(-12.000000, -12.000000)" x="11" y="5" width="2" height="14" rx="1"/>
                        <path d="M9.70710318,15.7071045 C9.31657888,16.0976288 8.68341391,16.0976288 8.29288961,15.7071045 C7.90236532,15.3165802 7.90236532,14.6834152 8.29288961,14.2928909 L14.2928896,8.29289093 C14.6714686,7.914312 15.281055,7.90106637 15.675721,8.26284357 L21.675721,13.7628436 C22.08284,14.136036 22.1103429,14.7686034 21.7371505,15.1757223 C21.3639581,15.5828413 20.7313908,15.6103443 20.3242718,15.2371519 L15.0300721,10.3841355 L9.70710318,15.7071045 Z" fill="#000000" fill-rule="nonzero" transform="translate(14.999999, 11.999997) scale(1, -1) rotate(90.000000) translate(-14.999999, -11.999997)"/>
                      </g>
                    </svg>
                  </span>
                </div>

                <!--3. Address Details-->
                <div class="wizard-step" data-wizard-type="step">
                  <div class="wizard-label">
                    <span class="svg-icon svg-icon-4x wizard-icon">
                      <svg xmlns="http://www.w3.org/2000/svg" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                        <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                          <rect x="0" y="0" width="24" height="24"/>
                          <path d="M13,18.9450712 L13,20 L14,20 C15.1045695,20 16,20.8954305 16,22 L8,22 C8,20.8954305 8.8954305,20 10,20 L11,20 L11,18.9448245 C9.02872877,18.7261967 7.20827378,17.866394 5.79372555,16.5182701 L4.73856106,17.6741866 C4.36621808,18.0820826 3.73370941,18.110904 3.32581341,17.7385611 C2.9179174,17.3662181 2.88909597,16.7337094 3.26143894,16.3258134 L5.04940685,14.367122 C5.46150313,13.9156769 6.17860937,13.9363085 6.56406875,14.4106998 C7.88623094,16.037907 9.86320756,17 12,17 C15.8659932,17 19,13.8659932 19,10 C19,7.73468744 17.9175842,5.65198725 16.1214335,4.34123851 C15.6753081,4.01567657 15.5775721,3.39010038 15.903134,2.94397499 C16.228696,2.49784959 16.8542722,2.4001136 17.3003976,2.72567554 C19.6071362,4.40902808 21,7.08906798 21,10 C21,14.6325537 17.4999505,18.4476269 13,18.9450712 Z" fill="#000000" fill-rule="nonzero"/>
                          <circle fill="#000000" opacity="0.3" cx="12" cy="10" r="6"/>
                        </g>
                      </svg>
                    </span>
                    <h3 class="wizard-title">3. Address Details</h3>
                  </div>
                  <span class="svg-icon svg-icon-xl wizard-arrow">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                      <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                        <polygon points="0 0 24 0 24 24 0 24"/>
                        <rect fill="#000000" opacity="0.3" transform="translate(12.000000, 12.000000) rotate(-90.000000) translate(-12.000000, -12.000000)" x="11" y="5" width="2" height="14" rx="1"/>
                        <path d="M9.70710318,15.7071045 C9.31657888,16.0976288 8.68341391,16.0976288 8.29288961,15.7071045 C7.90236532,15.3165802 7.90236532,14.6834152 8.29288961,14.2928909 L14.2928896,8.29289093 C14.6714686,7.914312 15.281055,7.90106637 15.675721,8.26284357 L21.675721,13.7628436 C22.08284,14.136036 22.1103429,14.7686034 21.7371505,15.1757223 C21.3639581,15.5828413 20.7313908,15.6103443 20.3242718,15.2371519 L15.0300721,10.3841355 L9.70710318,15.7071045 Z" fill="#000000" fill-rule="nonzero" transform="translate(14.999999, 11.999997) scale(1, -1) rotate(90.000000) translate(-14.999999, -11.999997)"/>
                      </g>
                    </svg>
                  </span>
                </div>

                <!--4. Review and Submit-->
                <div class="wizard-step" data-wizard-type="step">
                  <div class="wizard-label">
                    <span class="svg-icon svg-icon-4x wizard-icon">
                      <svg xmlns="http://www.w3.org/2000/svg" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                        <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                          <rect x="0" y="0" width="24" height="24"/>
                          <path d="M13.2070325,4 C13.0721672,4.47683179 13,4.97998812 13,5.5 C13,8.53756612 15.4624339,11 18.5,11 C19.0200119,11 19.5231682,10.9278328 20,10.7929675 L20,17 C20,18.6568542 18.6568542,20 17,20 L7,20 C5.34314575,20 4,18.6568542 4,17 L4,7 C4,5.34314575 5.34314575,4 7,4 L13.2070325,4 Z" fill="#000000"/>
                          <circle fill="#000000" opacity="0.3" cx="18.5" cy="5.5" r="2.5"/>
                        </g>
                      </svg>
                    </span>
                    <h3 class="wizard-title">4. Review &amp; Save</h3>
                  </div>
                  <span class="svg-icon svg-icon-xl wizard-arrow last">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                      <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                        <polygon points="0 0 24 0 24 24 0 24"/>
                        <rect fill="#000000" opacity="0.3" transform="translate(12.000000, 12.000000) rotate(-90.000000) translate(-12.000000, -12.000000)" x="11" y="5" width="2" height="14" rx="1"/>
                        <path d="M9.70710318,15.7071045 C9.31657888,16.0976288 8.68341391,16.0976288 8.29288961,15.7071045 C7.90236532,15.3165802 7.90236532,14.6834152 8.29288961,14.2928909 L14.2928896,8.29289093 C14.6714686,7.914312 15.281055,7.90106637 15.675721,8.26284357 L21.675721,13.7628436 C22.08284,14.136036 22.1103429,14.7686034 21.7371505,15.1757223 C21.3639581,15.5828413 20.7313908,15.6103443 20.3242718,15.2371519 L15.0300721,10.3841355 L9.70710318,15.7071045 Z" fill="#000000" fill-rule="nonzero" transform="translate(14.999999, 11.999997) scale(1, -1) rotate(90.000000) translate(-14.999999, -11.999997)"/>
                      </g>
                    </svg>
                  </span>
                </div>

              </div>
            </div>
            <!--end::Wizard Nav-->

            <!--begin::Wizard Body-->
            <div class="row justify-content-center py-10 px-8 py-lg-10 px-lg-10">
              <div class="col-xl-12 col-xxl-12">
                <form class="form mx-auto" novalidate="novalidate" action="javascript:void(0);" method="post"
                      id="kt_contact_add_form" name="kt_contact_add_form" enctype="multipart/form-data">
                  <meta name="csrf-token" content="{{ csrf_token() }}" />

                  <!-- ═══════════════════════════════════════════════════
                       STEP 1 — Company Information
                  ═══════════════════════════════════════════════════ -->
                  <div class="pb-5 acc-info-step" data-wizard-type="step-content" data-wizard-state="current">
                    <div class="acc-step-layout">

                      <!-- Main column -->
                      <div class="acc-main-column">

                        <!-- Company Branding card -->
                        <section class="acc-section-card" aria-labelledby="acc-branding-title">
                          <h3 class="acc-section-heading" id="acc-branding-title">Company Branding</h3>
                          <p class="acc-section-copy">Upload your company logo for visual identification across the platform.</p>
                          <div class="acc-branding-grid">

                            <!-- Upload trigger area -->
                            <label class="acc-logo-upload" for="logo">
                              <span class="acc-upload-icon"><i class="la la-cloud-upload-alt" aria-hidden="true"></i></span>
                              <span class="acc-upload-title">Upload Company Logo</span>
                              <span class="acc-upload-copy">PNG or JPG (Max. 2 MB)</span>
                              <span class="acc-upload-copy">Recommended: square image</span>
                            </label>

                            <!-- Preview panel -->
                            <div class="acc-logo-preview">
                              <span class="acc-preview-label">Preview</span>
                              <div class="imageinput image-input image-input-outline" data-kt-image-input="true">
                                <div class="image-input-wrapper w-90px h-90px" style="background-image: url({{ @$logo_path }})"></div>
                                <label class="btn btn-icon btn-circle btn-active-color-primary w-20px h-20px bg-white shadow"
                                       data-kt-image-input-action="change" data-toggle="tooltip" title="Change logo">
                                  <i class="bi bi-pencil-fill fs-9"></i>
                                  <input type="file" accept="image/*" onchange="loadFile(event)" name="logo" id="logo">
                                  <input type="hidden" name="avatar_remove">
                                </label>
                                <span class="btn btn-icon btn-circle btn-active-color-primary w-20px h-20px bg-white shadow"
                                      data-kt-image-input-action="cancel" title="Cancel"><i class="bi bi-x fs-4"></i></span>
                                <span class="removeimg btn btn-icon btn-circle btn-active-color-primary w-20px h-20px bg-white shadow"
                                      data-kt-image-input-action="remove" title="Remove"><i class="bi bi-x fs-4"></i></span>
                              </div>
                              <button type="button" class="acc-preview-remove removeimg">
                                <i class="la la-trash" aria-hidden="true"></i> Remove
                              </button>
                            </div>

                            <!-- Website + Company Name fields -->
                            <div class="acc-brand-fields">
                              <div class="acc-field fv-plugins-icon-container">
                                <label class="form-label required" for="website">Website</label>
                                <div class="input-group input-group-solid">
                                  <span class="input-group-text"><i class="la la-globe"></i></span>
                                  <input type="text" class="form-control form-control-solid"
                                         name="{{ @$account->accountVerify ? '' : 'website' }}" id="website"
                                         value="{{ @$account->website }}" placeholder="https://www.example.com"
                                         {{ @$account->accountVerify ? 'readonly' : '' }}>
                                </div>
                                <span class="form-text">Enter your company website URL.</span>
                              </div>
                              <div class="acc-field fv-plugins-icon-container">
                                <label class="form-label required" for="company_name">Company Name</label>
                                <div class="input-group input-group-solid">
                                  <span class="input-group-text"><i class="la la-building"></i></span>
                                  <input type="text" class="form-control form-control-solid"
                                         name="{{ @$account->accountVerify ? '' : 'company_name' }}" id="company_name"
                                         value="{{ @$account->company_name }}" placeholder="Enter company name"
                                         {{ @$account->accountVerify ? 'readonly' : '' }}>
                                </div>
                              </div>
                            </div>

                          </div>
                        </section>

                        <!-- Company Details card -->
                        <section class="acc-section-card" aria-labelledby="acc-details-title">
                          <h3 class="acc-section-heading" id="acc-details-title">Company Details</h3>
                          <p class="acc-section-copy">Enter essential details about your organisation.</p>
                          <div class="acc-details-grid">

                            <!-- Industry -->
                            <div class="acc-field fv-plugins-icon-container">
                              <label class="form-label" for="industries">Industry</label>
                              <div class="input-group input-group-solid">
                                <span class="input-group-text"><i class="la la-bar-chart"></i></span>
                                <select class="form-select form-select-solid" name="industries" id="industries">
                                  <option value="">Select Industries</option>
                                  @foreach($industries as $ind)
                                    @php
                                      $category_id = $sel = '';
                                      $category_id = @$account->industries;
                                    @endphp
                                    @if($category_id == @$ind->ind_id)
                                      @php $sel = 'selected="selected"'; @endphp
                                    @endif
                                    {!! '<option value="'.@$ind->ind_id.'"  '.$sel.'>'.ucfirst($ind->ind_name).'</option>' !!}
                                  @endforeach
                                </select>
                              </div>
                            </div>

                            <!-- Company Size -->
                            <div class="acc-field fv-plugins-icon-container">
                              <label class="form-label" for="company_size">Company Size</label>
                              <div class="input-group input-group-solid">
                                <span class="input-group-text"><i class="la la-users"></i></span>
                                @php $selected_company_size = @$account->company_size_from . '-' . $account->company_size; @endphp
                                <select name="company_size" class="form-select form-select-solid" id="company_size">
                                  <option value="">Select Company Size</option>
                                  <option value="1-10"         {{ $selected_company_size == '1-10'         ? 'selected' : '' }}>1–10</option>
                                  <option value="11-50"        {{ $selected_company_size == '11-50'        ? 'selected' : '' }}>11–50</option>
                                  <option value="51-200"       {{ $selected_company_size == '51-200'       ? 'selected' : '' }}>51–200</option>
                                  <option value="201-500"      {{ $selected_company_size == '201-500'      ? 'selected' : '' }}>201–500</option>
                                  <option value="501-1000"     {{ $selected_company_size == '501-1000'     ? 'selected' : '' }}>501–1,000</option>
                                  <option value="1001-5000"    {{ $selected_company_size == '1001-5000'    ? 'selected' : '' }}>1,001–5,000</option>
                                  <option value="5001-10000"   {{ $selected_company_size == '5001-10000'   ? 'selected' : '' }}>5,001–10,000</option>
                                  <option value="10000-999999" {{ $selected_company_size == '10000-999999' ? 'selected' : '' }}>10,000+</option>
                                </select>
                              </div>
                            </div>

                            <!-- Type -->
                            <div class="acc-field fv-plugins-icon-container">
                              <label class="form-label" for="industries_type">Type</label>
                              <div class="input-group input-group-solid">
                                <span class="input-group-text"><i class="la la-codepen"></i></span>
                                <select class="form-select form-select-solid" name="industries_type" id="industries_type">
                                  <option value="">Select Type</option>
                                  @foreach($industries_type_arr as $key => $val)
                                    @php $sel = ''; @endphp
                                    @if($key == @$account->industries_type)
                                      @php $sel = 'selected="selected"'; @endphp
                                    @endif
                                    {!! '<option value="'.@$key.'"  '.@$sel.'>'.ucwords(@$val).'</option>' !!}
                                  }
                                  @endforeach
                                </select>
                              </div>
                            </div>

                            <!-- Founded -->
                            <div class="acc-field fv-plugins-icon-container">
                              <label class="form-label" for="founded">Founded Year</label>
                              <div class="input-group input-group-solid">
                                <span class="input-group-text"><i class="la la-calendar"></i></span>
                                <input type="number" class="form-control form-control-solid"
                                       name="founded" id="founded" value="{{ @$account->founded }}"
                                       placeholder="e.g. 2005">
                              </div>
                            </div>

                          </div>
                        </section>

                      </div><!-- /acc-main-column -->

                      <!-- Side column — profile checklist -->
                      <div class="acc-side-column">
                        <div class="acc-assistant">
                          <div class="acc-assistant-head">
                            <div>
                              <p class="acc-assistant-title">Profile Checklist</p>
                              <p style="font-size:10.5px;color:#53607e;margin:4px 0 0;">Complete all sections for a strong company profile.</p>
                            </div>
                            <div class="acc-assistant-spark"><i class="la la-check-circle"></i></div>
                          </div>
                          <div class="acc-checklist">
                            <div class="acc-checklist-title">Sections to complete</div>
                            <div class="acc-checklist-row"><i class="la la-circle-o"></i> Company logo uploaded</div>
                            <div class="acc-checklist-row"><i class="la la-circle-o"></i> Website &amp; company name</div>
                            <div class="acc-checklist-row"><i class="la la-circle-o"></i> Industry &amp; size selected</div>
                            <div class="acc-checklist-row"><i class="la la-circle-o"></i> Company description</div>
                            <div class="acc-checklist-row"><i class="la la-circle-o"></i> Office address(es)</div>
                          </div>
                        </div>
                      </div>

                    </div>
                  </div>
                  <!--end::Step 1-->

                  <!-- ═══════════════════════════════════════════════════
                       STEP 2 — Company Description
                  ═══════════════════════════════════════════════════ -->
                  <div class="pb-5 acc-desc-step" data-wizard-type="step-content">
                    <div class="acc-step-layout">
                      <div class="acc-main-column">

                        <section class="acc-section-card">
                          <h3 class="acc-section-heading">About the Company</h3>
                          <p class="acc-section-copy">This description appears on your public company profile and job postings.</p>
                          <div class="acc-editor-wrap">
                            <div class="acc-editor-toolbar">
                              <span><i class="la la-edit"></i> Company Description</span>
                              <span class="acc-char-count"><span id="acc_desc_count">0</span>/2000</span>
                            </div>
                            <textarea id="kt_docs_tinymce_hidden" name="kt_docs_tinymce_hidden"
                                      class="tox-target" style="min-height:260px;">{{ @$account->description }}</textarea>
                          </div>
                        </section>

                        <section class="acc-section-card">
                          <h3 class="acc-section-heading">Service Offerings</h3>
                          <p class="acc-section-copy">Add the services your company provides (press Enter or comma to add).</p>
                          <input class="form-control form-control-solid" name="tags" id="tags"
                                 placeholder="e.g. Software Development, IT Consulting..."
                                 value="{{ @$tags ?? '' }}"/>
                          <div class="mt-3">
                            <span class="acc-suggested-label">Suggested:</span>
                            <div class="acc-suggested-list" id="acc_suggested_services">
                              @php
                                $suggestedServices = ['Software Development','IT Consulting','Cloud Solutions','Digital Transformation','Data Analytics','Cybersecurity','UI/UX Design','Project Management','DevOps','Quality Assurance'];
                              @endphp
                              @foreach($suggestedServices as $svc)
                              <span class="acc-suggested-tag" data-value="{{ htmlspecialchars($svc) }}">
                                {{ htmlspecialchars($svc) }}
                                <button type="button" class="acc-suggest-add" title="Add {{ htmlspecialchars($svc) }}"><i class="la la-plus" aria-hidden="true"></i></button>
                              </span>
                              @endforeach
                            </div>
                          </div>
                        </section>

                      </div>

                      <aside class="acc-side-column">
                        <div class="acc-helper-card acc-helper-blue">
                          <h4><i class="la la-lightbulb-o" aria-hidden="true"></i> Writing Tips</h4>
                          <ul>
                            <li><i class="la la-check-circle" aria-hidden="true"></i><span>Include company mission, vision and background.</span></li>
                            <li><i class="la la-check-circle" aria-hidden="true"></i><span>Mention key services, team culture, and market focus.</span></li>
                            <li><i class="la la-check-circle" aria-hidden="true"></i><span>Keep the tone professional and concise.</span></li>
                          </ul>
                        </div>
                        <div class="acc-helper-card acc-helper-purple">
                          <h4><i class="la la-cube" aria-hidden="true"></i> Why add Services?</h4>
                          <p>Services help match your company with relevant requirements and improve search visibility across the platform.</p>
                        </div>
                        <div class="acc-helper-card acc-helper-green">
                          <h4><i class="la la-eye" aria-hidden="true"></i> Live Preview</h4>
                          <div class="acc-preview-box">
                            <div class="preview-title" id="acc_preview_company">{{ @$account->company_name ?: 'Company Name' }}</div>
                            <div class="preview-body" id="acc_preview_description">Your company description will appear here.</div>
                            <div class="acc-preview-tags" id="acc_preview_tags"></div>
                          </div>
                        </div>
                      </aside>
                    </div>
                  </div>
                  <!--end::Step 2-->

                  <!-- ═══════════════════════════════════════════════════
                       STEP 3 — Address Details
                  ═══════════════════════════════════════════════════ -->
                  <div class="pb-5 acc-address-step" data-wizard-type="step-content">
                    <div class="acc-step-header">
                      <div>
                        <h3>Address Details</h3>
                        <p>Add your office location(s). You can add multiple addresses.</p>
                      </div>
                    </div>

                    <div class="acc-section-card">

                      @php
                        $addressId = 0;
                        $countAdd  = 1;
                        $display_city_id = $stateListing = $cityListing = '';

                        if (count($client_location_arr) > 0) {
                          $clhtml = '<div class="row client-address-container">';
                          foreach ($client_location_arr as $clVal) {
                            $countryListing = $stateListing = '';
                            $countryName    = $clVal->country_name;
                            $display_city_id = '';
                            $stateListing    = '';
                            $cityListing     = '';
                            $stateName       = $clVal->state_name;
                            $cityName        = $clVal->city_name;
                            $return          = $cityName . ' | ' . $stateName . ' | ' . $countryName;

                            $clhtml .= '
                              <div class="acc-address-card client-address-container-child" id="clientAddr_' . $addressId . '">
                                <div class="acc-address-card-head">
                                  <h4><i class="la la-map-marker" style="color:var(--acc-primary);margin-right:6px;"></i>Address ' . $countAdd . '</h4>
                                </div>
                                <div class="form-group mb-3">
                                  <label class="form-label required">Street Address</label>
                                  <input type="text" class="form-control form-control-solid"
                                         name="address[]" placeholder="Enter full street address"
                                         value="' . @$clVal->address . '" id="address_' . @$addressId . '">
                                </div>
                                <div class="form-group">
                                  <label class="form-label required">City / Location</label>
                                  <input type="hidden" name="hid_country_id[]" id="hid_country_id_' . @$addressId . '"
                                         class="gethiddencountry" value="' . $clVal->country_id . '" />
                                  <input class="form-control form-control-solid"
                                         value="' . $return . '"
                                         id="kt_tagify_work_location_' . @$addressId . '"
                                         name="kt_tagify_work_location[]"
                                         placeholder="Search city..."/>
                                </div>
                              </div>';
                            $addressId++;
                            $countAdd++;
                          }
                          $clhtml .= '</div>';
                          echo $clhtml;
                          $addressId--;
                        } else {
                      @endphp

                      <div class="row client-address-container">
                        <div class="acc-address-card client-address-container-child" id="clientAddr_{{ @$addressId }}">
                          <div class="acc-address-card-head">
                            <h4><i class="la la-map-marker" style="color:var(--acc-primary);margin-right:6px;"></i>Address 1</h4>
                          </div>
                          <div class="form-group mb-3">
                            <label class="form-label required">Street Address</label>
                            <input type="text" class="form-control form-control-solid"
                                   name="address[]" placeholder="Enter full street address"
                                   value="" id="address_{{ @$addressId }}">
                          </div>
                          <div class="form-group">
                            <label class="form-label required">City / Location</label>
                            <input class="form-control form-control-solid" value=""
                                   id="kt_tagify_work_location_{{ @$addressId }}"
                                   name="kt_tagify_work_location[]" placeholder="Search city..."/>
                          </div>
                        </div>
                      </div>

                      @php } @endphp

                      <div id="addressLoader"></div>
                      <input type="hidden" name="addressId" id="addressId" value="{{ @$addressId }}" />

                      <!-- Add / Remove location buttons -->
                      <div class="d-flex gap-3 pt-4 mt-2" style="border-top:1px solid var(--acc-border);">
                        <button type="button" class="acc-add-address-btn add_address d-flex align-items-center gap-2">
                          <i class="la la-plus"></i> Add Another Location
                        </button>
                        <button type="button" class="acc-remove-address-btn remove_address d-flex align-items-center gap-2">
                          <i class="la la-minus"></i> Remove Last
                        </button>
                      </div>

                    </div>
                  </div>
                  <!--end::Step 3-->

                  <!-- ═══════════════════════════════════════════════════
                       STEP 4 — Review and Submit
                  ═══════════════════════════════════════════════════ -->
                  <div class="pb-15 acc-review-step" data-wizard-type="step-content">
                    <div class="acc-step-header">
                      <div>
                        <h3>Review &amp; Save</h3>
                        <p>Review your company details before saving. Click Back to make changes.</p>
                      </div>
                    </div>

                    <!-- Company Information -->
                    <div class="acc-review-card">
                      <div class="acc-review-card-head">
                        <h4><i class="la la-building" style="color:var(--acc-primary);margin-right:6px;"></i>Company Information</h4>
                      </div>
                      <div class="acc-review-grid">
                        <div class="acc-review-item">
                          <label>Company Name</label>
                          <span class="span_company_name">&nbsp;</span>
                        </div>
                        <div class="acc-review-item">
                          <label>Website</label>
                          <span class="span_website">&nbsp;</span>
                        </div>
                        <div class="acc-review-item">
                          <label>Industry</label>
                          <span class="span_category_id">&nbsp;</span>
                        </div>
                        <div class="acc-review-item">
                          <label>Company Size</label>
                          <span class="span_company_size">&nbsp;</span>
                        </div>
                        <div class="acc-review-item">
                          <label>Type</label>
                          <span class="span_company_type">&nbsp;</span>
                        </div>
                        <div class="acc-review-item">
                          <label>Founded</label>
                          <span class="span_founded">&nbsp;</span>
                        </div>
                      </div>
                    </div>

                    <!-- Description -->
                    <div class="acc-review-card">
                      <div class="acc-review-card-head">
                        <h4><i class="la la-file-text" style="color:var(--acc-primary);margin-right:6px;"></i>Company Description</h4>
                      </div>
                      <div class="row pt-2 text-justify span_description" style="font-size:11.5px;color:#53607e;">&nbsp;</div>
                    </div>

                    <!-- Address -->
                    <div class="acc-review-card">
                      <div class="acc-review-card-head">
                        <h4><i class="la la-map-marker" style="color:var(--acc-primary);margin-right:6px;"></i>Address Details</h4>
                      </div>
                      <div class="row pt-2 divAddressDetail" style="font-size:11.5px;color:#53607e;"></div>
                    </div>

                    <!-- Confirmation banner -->
                    <div class="acc-almost-done">
                      <i class="la la-check-circle"></i>
                      <h5>Ready to save!</h5>
                      <p>Your company profile is complete. Click <strong>Save Changes</strong> below to apply your updates.</p>
                    </div>
                  </div>
                  <!--end::Step 4-->

                  <!--begin::Bottom Action Bar-->
                  <div class="d-flex justify-content-center border-top pt-10">
                    <div class="acc-action-left">
                      <a href="{{ route('service_company.account.index') }}" class="btn acc-cancel-btn">Cancel</a>
                      <button type="button" class="btn btn-light-primary font-weight-bolder px-9 py-4" data-wizard-type="action-prev">
                        <i class="la la-arrow-left me-1"></i> Back
                      </button>
                    </div>
                    <div class="acc-action-right ms-auto">
                      <button type="button" class="btn acc-submit-btn frmupdate font-weight-bolder px-9 py-4"
                              data-wizard-type="action-submit1" style="display:none;">
                        <i class="la la-save me-1"></i> Save Changes
                      </button>
                      <button type="button" class="btn btn-primary font-weight-bolder px-9 py-4" data-wizard-type="action-next">
                        Continue <i class="la la-arrow-right ms-1"></i>
                      </button>
                    </div>
                  </div>
                  <!--end::Bottom Action Bar-->

                </form>
              </div>
            </div>
            <!--end::Wizard Body-->

          </div>
          <!--end::Wizard-->

        </div>
      </div>
    </div>
  </div>
</div>
<!--end::Content-->
@endsection

@push('footer-script')
<script src="{{ asset('assets/node_modules/select2/dist/js/select2.full.min.js') }}<?php echo TM_VERSION; ?>" type="text/javascript"></script>
<script src="{{ asset('assets/node_modules/bootstrap-select/bootstrap-select.min.js') }}<?php echo TM_VERSION; ?>" type="text/javascript"></script>
<script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/jquery.validate.min.js"></script>
<script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/additional-methods.min.js"></script>
<script src="https://unpkg.com/@yaireo/tagify"></script>
<script src="https://unpkg.com/@yaireo/tagify/dist/tagify.polyfills.min.js"></script>
<link href="https://unpkg.com/@yaireo/tagify/dist/tagify.css" rel="stylesheet" type="text/css" />
<script src="{{ asset('demo/v7/js/plugins.bundle.js') }}<?php echo TM_VERSION; ?>"></script>
<script src="{{ asset('demo/v7/js/v7scripts.bundle.js') }}<?php echo TM_VERSION; ?>"></script>
{{-- account.js intentionally excluded: it initialises KTWizard with hardcoded field names
     (communication/language/timezone/kt_contact_add_avatar) that don't exist in this form.
     Its change handler always returns false and the missing validators never resolve,
     so Continue never advances. We initialise the wizard inline below instead. --}}
<script src="{{ asset('demo/v7/js/pages/crud/forms/widgets/select2.js') }}<?php echo TM_VERSION; ?>"></script>
<script src="{{asset('demo/plugins/custom/tinymce/tinymce.bundle.js')}}"></script>
<script src="{{asset('demo/dist/assets/js/custom/documentation/editors/tinymce/hidden.js')}}"></script>

<script>
function opensidebar(){
  $('#kt_activitiesClient_toggle').trigger('click');
}
</script>

<script>
"use strict";
var KTFormsTinyMCEHidden = {
  init: function () {
    tinymce.init({
      selector: "#kt_docs_tinymce_hidden",
      menubar: false,
      toolbar: [
        "styleselect | fontselect | fontsizeselect | undo redo | cut copy paste | bold italic | link image | alignleft aligncenter alignright alignjustify | bullist numlist | outdent indent | blockquote subscript superscript | advlist | autolink | lists charmap | print preview | code",
      ],
      plugins: "advlist autolink link image lists charmap print preview code",
      setup: function(editor) {
        editor.on('keyup change SetContent', function() {
          var text = editor.getContent({format: 'text'}) || '';
          $('#acc_desc_count').text(Math.min(text.trim().length, 2000));
          $('#acc_preview_description').text(text.trim() ? text.trim().substring(0, 180) : 'Your company description will appear here.');
        });
      }
    });
  },
};
KTUtil.onDOMContentLoaded(function () {
  KTFormsTinyMCEHidden.init();
});
</script>

<script>
// Replacement for account.js — initialises KTWizard with validation appropriate for
// the account edit form fields (company_name is the only required Step-1 field).
$(document).ready(function() {
    var wizardObj = new KTWizard('kt_contact_add', {
        startStep: 1,
        clickableSteps: false
    });

    wizardObj.on('change', function(wizard) {
        // Going backwards — always allow
        if (wizard.getStep() > wizard.getNewStep()) {
            return;
        }

        // Step 1: company_name must not be empty
        if (wizard.getStep() === 1) {
            var companyName = $.trim($('#company_name').val());
            if (!companyName) {
                $('#company_name').addClass('is-invalid').css('border-color', '#ef4444');
                Swal.fire({
                    text: "Please enter your company name before continuing.",
                    icon: "warning",
                    buttonsStyling: false,
                    confirmButtonText: "OK",
                    customClass: { confirmButton: "btn btn-primary" }
                }).then(function() {
                    $('#company_name').focus();
                });
                return false;
            }
            $('#company_name').removeClass('is-invalid').css('border-color', '');
        }
        // Steps 2, 3, 4: no blocking validation — allow navigation
    });

    wizardObj.on('changed', function(wizard) {
        if (typeof KTUtil !== 'undefined') { KTUtil.scrollTop(); }
    });

    // Last step submit — trigger the hidden form save button
    wizardObj.on('submit', function(wizard) {
        $('.frmupdate').trigger('click');
    });
});
</script>

<script>
// Service Offerings tagify with suggested chips + live preview
var tagsInput = document.querySelector('#tags');
if (tagsInput) {
  window.accTagsTagify = new Tagify(tagsInput, { maxTags: 30 });
  if (window.accTagsTagify.DOM && window.accTagsTagify.DOM.scope) {
    window.accTagsTagify.DOM.scope.classList.add('acc-service-tagify');
  }

  function renderAccServicePreview(values) {
    var $cont = $('#acc_preview_tags');
    $cont.empty();
    (values || []).forEach(function(v) {
      $cont.append($('<span class="acc-preview-tag-chip"></span>').text(v));
    });
  }

  function syncAccSuggestedHighlight() {
    var added = (window.accTagsTagify.value || []).map(function(t) { return t.value.toLowerCase(); });
    $('#acc_suggested_services .acc-suggested-tag').each(function() {
      var v = $(this).data('value') || '';
      $(this).toggleClass('is-added', added.indexOf(v.toLowerCase()) > -1);
    });
  }

  window.accTagsTagify.on('add remove', function() {
    syncAccSuggestedHighlight();
    renderAccServicePreview((window.accTagsTagify.value || []).map(function(t) { return t.value; }));
  });

  syncAccSuggestedHighlight();
  renderAccServicePreview((window.accTagsTagify.value || []).map(function(t) { return t.value; }));

  $(document).off('click.accServiceChip').on('click.accServiceChip', '.acc-suggested-tag', function(e) {
    e.preventDefault();
    var $chip = $(this);
    if ($chip.hasClass('is-added')) return;
    var value = $chip.attr('data-value');
    if (!value || !window.accTagsTagify) return;
    var existing = (window.accTagsTagify.value || []).map(function(t) { return t.value.toLowerCase(); });
    if (existing.indexOf(value.toLowerCase()) !== -1) { $chip.addClass('is-added'); return; }
    window.accTagsTagify.addTags([{ value: value }]);
  });
}

function isNumberKey(evt){
  var charCode = (evt.which) ? evt.which : evt.keyCode;
  if (charCode > 31 && (charCode < 48 || charCode > 57)) return false;
  return true;
}

const cities = @json($cities);
var countAddrees = "{{ $countAddrees }}";
for (let i = 0; i < countAddrees; i++) {
  let inputElement = document.querySelector('#kt_tagify_work_location_' + i);
  if (inputElement) {
    let tagifyInstance = new Tagify(inputElement, {
      whitelist: cities,
      maxTags: 1,
      dropdown: { maxItems: 20, classname: "", enabled: 0, closeOnSelect: true }
    });
    tagifyInstance.on('input', function(e) { tagifyInstance.dropdown.show(); });
    tagifyInstance.on('dropdown:select', function(e) { tagifyInstance.addTags([e.detail.data.value]); });

    function debounce(func, delay) {
      let timer;
      return function (...args) {
        clearTimeout(timer);
        timer = setTimeout(() => func.apply(this, args), delay);
      };
    }
    const fetchSuggestions = debounce(function(query) {
      $.ajax({
        url: "{{ url('/') }}/city-job-suggestions?q=" + query,
        method: 'GET',
        success: function(data) {
          if (Array.isArray(data)) {
            tagifyInstance.settings.whitelist = data;
            tagifyInstance.dropdown.show.call(tagifyInstance);
          }
        }
      });
    }, 200);
    tagifyInstance.on('input', function(e) {
      var query = e.detail.value.trim();
      if (query.length < 3) return;
      fetchSuggestions(query);
    });
  }
}
</script>

<script>
var loadFile = function(event) {
  var output = URL.createObjectURL(event.target.files[0]);
  $('.image-input-wrapper').css('background-image', 'url(' + output + ')');
};
$(document).ready(function() {
  $(".removeimg").click(function() {
    var defaultUrl = 'https://preview.keenthemes.com/metronic8/demo1/assets/media/avatars/blank.png';
    $('.image-input-wrapper').css('background-image', 'url(' + defaultUrl + ')');
  });
});
</script>

<script>
// Review step — populate summary cards, show Save button
$(document).on('click', '.btn', function() {
  var state = $('#kt_contact_add').attr("data-wizard-state");
  if (state === 'last') {
    $('.frmupdate').show();
    $('.span_company_name').html($('#company_name').val());
    $('.span_website').html($('#website').val());
    $('.span_category_id').html($('#industries option:selected').text());
    $('.span_company_size').html($('#company_size').val());
    $('.span_company_type').html($('#industries_type option:selected').text());
    $('.span_founded').html($('#founded').val());

    var description = tinymce.get("kt_docs_tinymce_hidden") ? tinymce.get("kt_docs_tinymce_hidden").getContent() : '';
    $('.span_description').html(description || '<em style="color:#b7bfce;">No description entered.</em>');

    // Tags (hidden section)
    var tags = $('#tags').val();
    $('.span_tags').html('');
    if (tags) {
      try {
        var json_tags = $.parseJSON(tags);
        for (let len = 0; len < json_tags.length; len++) {
          $('.span_tags').append((len === 0 ? '' : ', ') + json_tags[len].value);
        }
      } catch(e) {}
    }

    // Addresses
    $('.divAddressDetail').html('');
    var addressId = $('#addressId').val();
    for (let cntAdd = 0; cntAdd <= addressId; cntAdd++) {
      var address = $('#address_' + cntAdd).val();
      if (typeof address === 'undefined') continue;
      var rawCity = $('#kt_tagify_work_location_' + cntAdd).val();
      var city = rawCity ? rawCity.replace('[{"value":"', '').replace('"}]', '') : '';
      $('.divAddressDetail').append(
        '<div class="row mb-3">' +
          '<div class="col-lg-2"><label style="font-size:10px;font-weight:600;color:#7b8497;">Address</label></div>' +
          '<div class="col-lg-10"><span style="font-size:11.5px;font-weight:600;color:#0d194d;">' + address + '</span></div>' +
        '</div>' +
        '<div class="row mb-2">' +
          '<div class="col-lg-2"><label style="font-size:10px;font-weight:600;color:#7b8497;">City</label></div>' +
          '<div class="col-lg-10"><span style="font-size:11.5px;font-weight:600;color:#0d194d;">' + city + '</span></div>' +
        '</div>'
      );
    }
    $('.frmupdate').show();
  } else {
    $('.frmupdate').hide();
  }
});

// Form AJAX submission
$(document).on('click', '.frmupdate', function() {
  var data      = new FormData();
  var form_data = $('#kt_contact_add_form').serializeArray();
  $.each(form_data, function(key, input) { data.append(input.name, input.value); });

  var file_data = $('input[name="logo"]')[0].files;
  for (var i = 0; i < file_data.length; i++) { data.append("logo", file_data[i]); }

  var description = tinymce.get("kt_docs_tinymce_hidden") ? tinymce.get("kt_docs_tinymce_hidden").getContent() : '';
  data.append("description", description);
  data.append('key', 'value');

  $('.frmupdate').prop('disabled', true).html('<i class="la la-spinner la-spin me-1"></i> Saving…');

  $.ajax({
    url         : '{{ route('service_company.account.update', @$account->id) }}',
    headers     : {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
    method      : "POST",
    processData : false,
    contentType : false,
    data        : data,
    redirect    : true,
    success     : function(data) {
      if (data.status === 'success') {
        $('.frmupdate').prop('disabled', false).html('<i class="la la-save me-1"></i> Save Changes');
        window.location = '{{ route('service_company.account.index') }}';
      }
    }
  });
});
</script>

<script>
$(document).ready(function() {

  // Add another location
  $('.add_address').click(function() {
    var addressId = $('#addressId').val();
    addressId++;
    $('#addressId').val(addressId);

    var baseurl = "{{ asset('assets/images/Loading2.gif') }}";
    var cid     = addressId;
    var url     = "{{ route('service_company.clients.addresslist', ':cid') }}";
    url         = url.replace(':cid', cid);
    $('#addressLoader').show().html('');

    $.ajax({
      type       : 'GET',
      url        : url,
      beforeSend : function() { $('#addressLoader').html('<img src="' + baseurl + '" height="35px" width="100%">'); },
      success    : function(response) {
        $('#addressLoader').hide();
        $('.client-address-container').last().append(response.data);
        $(".country").trigger("change");
      }
    });
  });

  // Remove last location
  $('.remove_address').click(function() {
    var totalChildren = $('.client-address-container').children().length;
    if (totalChildren > 1) {
      $('.client-address-container').children().last().remove();
    } else {
      Swal.fire("Notice", 'At least one address is required.', "warning");
    }
  });

  // Country change handler
  $(document).on('change', '.country', function() {
    var cid = this.value;
    var id  = parseInt($(this).attr('id').substr(8));
    if (cid == 101) {
      $('#city_id_' + id).show(); $('#city_' + id).hide();
    } else {
      $('#city_' + id).show(); $('#city_id_' + id).hide();
    }
    $('#hid_country_id_' + id).val(cid);
    var url = "{{ route('service_company.job-applications.statelist', ':cid') }}";
    $.ajax({ type: 'GET', url: url.replace(':cid', cid), success: function(r) { $('#state_' + id).html(r.data); } });
  });

  // State change handler
  $(document).on('change', '.change_state', function() {
    var state_id   = this.value;
    var ccountryid = parseInt($(this).attr('id').substr(6));
    var hid        = $('#hid_country_id_' + ccountryid).val();
    if (hid == 101) {
      $('#city_id_' + ccountryid).show(); $('#city_' + ccountryid).hide();
    } else {
      $('#city_' + ccountryid).show(); $('#city_id_' + ccountryid).hide();
    }
    var url = "{{ route('service_company.clients.citylist', ':state_id') }}";
    $.ajax({ type: 'GET', url: url.replace(':state_id', state_id), success: function(r) { $('#city_id_' + ccountryid).html(r.data); } });
  });

  // Header user menu dropdown toggle
  $('#kt_header_user_menu_toggle').click(function(event) {
    event.stopPropagation();
    var $dd = $('.menu-sub-dropdown');
    if ($dd.hasClass('menu-sub-dropdown-hidden')) {
      $dd.removeClass('menu-sub-dropdown-hidden')
         .attr('style', 'z-index:105;position:fixed;inset:0px auto auto 0px;margin:0;transform:translate(1278px,65px);display:inline-block;');
    } else {
      $dd.addClass('menu-sub-dropdown-hidden').css('display', 'none');
    }
  });
  $('body').click(function() {
    var $dd = $('.menu-sub-dropdown');
    if (!$dd.hasClass('menu-sub-dropdown-hidden')) {
      $dd.addClass('menu-sub-dropdown-hidden').css('display', 'none');
    }
  });

  // Auto-navigate to the correct step if switchSection is set
  setTimeout(function() {
    var tagSection = "{{ $switchSection }}";
    if (tagSection == 1) {
      $('[data-wizard-type="action-next"]').click();
    } else if (tagSection == 2) {
      $('[data-wizard-type="action-next"]').click();
      setTimeout(function() { $('[data-wizard-type="action-next"]').click(); }, 500);
    }
  }, 1500);

});
</script>
@endpush
