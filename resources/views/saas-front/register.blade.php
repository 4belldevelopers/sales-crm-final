@extends('layouts.metronic_front_app')

@section('content')
<!-- Stylesheets -->
<link href="{{ asset('demo/dist/assets/plugins/global/plugins.bundle.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('demo/dist/assets/css/style.bundle.css')}}" rel="stylesheet" type="text/css" />
<link href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.17/css/intlTelInput.css" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

<style>
/* Page Loader Override - Hidden by default */ 
.page-loader {
  display: none;
  justify-content: center;
  align-items: center;
  flex-direction: column;
  position: fixed;
  top: 0;
  left: 0;
  width: 100vw;
  height: 100vh;
  z-index: 99999;
  background: rgba(15, 23, 42, 0.5);
  backdrop-filter: blur(4px);
}

/* Stepper Display Controls */
[data-kt-stepper-element="content"] {
  display: none !important;
}
[data-kt-stepper-element="content"].current {
  display: block !important;
}

:root {
  --v2-p: #1d61f2;
  --v2-ph: #154ec4;
  --v2-pl: #f0f6ff;
  --v2-pb: #d0e4ff;
  --v2-dk: #0f172a;
  --v2-gr: #64748b;
  --v2-bd: #e2e8f0;
  --v2-bg: #f5f7fa;
}

body, html {
  margin: 0 !important;
  padding: 0 !important;
  font-family: 'Poppins', sans-serif !important;
  background: var(--v2-bg) !important;
  color: var(--v2-dk);
  min-height: 100vh;
}

.flex-root, .flex-column-fluid {
  background: var(--v2-bg) !important;
}

/* Outer layout wrapper */
.v2-wrapper {
  display: flex;
  width: 100%;
  max-width: 1440px;
  margin: 0 auto;
  min-height: 100vh;
  background: var(--v2-bg);
  padding: 32px 40px;
  box-sizing: border-box;
  gap: 36px;
}

/* Left Column */
.v2-left-col {
  width: 300px;
  flex-shrink: 0;
  display: flex;
  flex-direction: column;
  gap: 18px;
  position: sticky;
  top: 0;
  align-self: flex-start;
  overflow-y: visible;
}

.v2-logo {
  display: flex;
  align-items: center;
  gap: 10px;
  text-decoration: none;
  margin-bottom: 5px;
}
.v2-logo-compact {
  margin-bottom: 6px;
  gap: 8px;
}
.v2-logo-compact .v2-logo-text {
  font-size: 17px;
  font-weight: 600;
}
.v2-logo-text {
  font-size: 24px;
  font-weight: 600;
  color: var(--v2-dk);
  letter-spacing: -0.5px;
  line-height: 1;
}
.v2-logo-sub {
  font-size: 11.5px;
  font-weight: 500;
  color: var(--v2-gr);
  margin-top: 4px;
  display: block;
}

/* Stepper Card — Timeline style */
.v2-stepper-card {
  background: #ffffff;
  border-radius: 14px;
  padding: 18px 16px;
  box-shadow: 0 1px 3px rgba(0,0,0,0.03);
  border: 1px solid #edf0f4;
  display: flex;
  flex-direction: column;
  gap: 0;
}

.v2-step-item {
  position: relative;
  display: flex;
  align-items: center;
  gap: 14px;
  padding: 10px 6px;
  border-radius: 10px;
  transition: all 0.2s ease;
  text-decoration: none !important;
}
/* vertical connector line — drawn behind the badge, anchored to badge center */
.v2-step-item:not(:last-child)::before {
  content: "";
  position: absolute;
  left: 16px;
  top: 36px;
  bottom: -10px;
  width: 2px;
  background: #e2e8f0;
  z-index: 0;
}
.v2-step-item.current:not(:last-child)::before,
.v2-step-item.completed:not(:last-child)::before {
  background: #1d61f2;
}

.v2-step-item.current {
  /* background removed per request */
  border-radius: 10px;
}

.v2-step-badge-wrap {
  position: relative;
  z-index: 1;
  flex-shrink: 0;
}

.v2-step-badge {
  width: 32px;
  height: 32px;
  border-radius: 50%;
  background: #ffffff;
  border: 2px solid #cbd5e1;
  color: #94a3b8;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 12.5px;
  font-weight: 600;
  transition: all 0.2s ease;
}
.v2-step-item.current .v2-step-badge {
  background: #1d61f2;
  border-color: #1d61f2;
  color: #ffffff;
  box-shadow: 0 0 0 4px rgba(29, 97, 242, 0.15);
}
.v2-step-item.completed .v2-step-badge {
  background: #1d61f2;
  border-color: #1d61f2;
  color: #ffffff;
}
.v2-step-item.current .v2-step-badge-check,
.v2-step-item.completed .v2-step-badge-check { display: inline-flex; }
.v2-step-item.current .v2-step-badge-num,
.v2-step-item.completed .v2-step-badge-num { display: none; }
.v2-step-badge-check { display: none; }
.v2-step-badge-num { display: inline; }

.v2-step-text {
  padding-top: 0;
  min-width: 0;
}
.v2-step-title {
  font-size: 13px;
  font-weight: 600;
  color: #94a3b8;
  line-height: 1.2;
}
.v2-step-item.current .v2-step-title {
  color: #0f172a;
  font-weight: 600;
}
.v2-step-item.completed .v2-step-title {
  color: #0f172a;
}
.v2-step-sub {
  font-size: 11.5px;
  color: #94a3b8;
  margin-top: 3px;
  line-height: 1.3;
}
.v2-step-item.current .v2-step-sub {
  color: #64748b;
}
.v2-step-item.completed .v2-step-sub {
  color: #64748b;
}

/* Need Help Card (left sidebar) */
.v2-help-card {
  background: #ffffff;
  border: 1px solid #edf0f4;
  border-radius: 14px;
  padding: 16px 16px 8px;
  box-shadow: 0 1px 3px rgba(0,0,0,0.03);
}
.v2-help-head {
  display: flex;
  align-items: center;
  gap: 12px;
  margin-bottom: 10px;
}
.v2-help-icon {
  width: 36px;
  height: 36px;
  border-radius: 50%;
  background: #fff7ed;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}
.v2-help-title {
  font-size: 13px;
  font-weight: 600;
  color: #0f172a;
  line-height: 1.2;
}
.v2-help-sub {
  font-size: 11.5px;
  color: #94a3b8;
  margin-top: 2px;
}
.v2-help-rows {
  display: flex;
  flex-direction: column;
  border-top: 1px solid #f1f5f9;
}
.v2-help-row {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 10px 2px;
  border-bottom: 1px solid #f1f5f9;
  text-decoration: none !important;
  transition: background 0.15s ease;
  border-radius: 4px;
}
.v2-help-row:last-child { border-bottom: none; }
.v2-help-row:hover { background: #f8fafc; }
.v2-help-row-ico {
  width: 26px;
  height: 26px;
  border-radius: 50%;
  background: #eff6ff;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}
.v2-help-row-txt {
  flex: 1;
  font-size: 12.5px;
  font-weight: 600;
  color: #0f172a;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}
.v2-help-row-arrow {
  flex-shrink: 0;
}

/* Right Column */
.v2-right-col {
  flex: 1;
  min-width: 0;
  display: flex;
  flex-direction: column;
}

.v2-topbar {
  display: flex;
  justify-content: flex-end;
  align-items: center;
  gap: 12px;
  margin-bottom: 14px;
  font-size: 13px;
}
.v2-topbar span { color: var(--v2-gr); }
.v2-btn-signin {
  padding: 7px 20px;
  border: 1.5px solid var(--v2-p);
  border-radius: 8px;
  color: var(--v2-p);
  font-weight: 500;
  text-decoration: none !important;
  font-size: 13px;
  transition: all 0.2s ease;
  background: transparent;
}
.v2-btn-signin:hover {
  background: var(--v2-p);
  color: #ffffff !important;
}

.v2-main-card {
  background: #ffffff;
  border-radius: 20px;
  padding: 40px 44px;
  box-shadow: 0 2px 16px rgba(0,0,0,0.03);
  border: 1px solid #edf0f4;
  width: 100%;
  box-sizing: border-box;
  min-height: 550px;
}

.v2-hero {
  display: flex;
  align-items: flex-start;
  gap: 16px;
  margin-bottom: 24px;
}
.v2-hero-ico {
  width: 48px;
  height: 48px;
  border-radius: 50%;
  background: var(--v2-pl);
  display: flex;
  align-items: center;
  justify-content: center;
  color: var(--v2-p);
  flex-shrink: 0;
}
.v2-hero h1 {
  font-size: 26px;
  font-weight: 600;
  color: var(--v2-dk);
  margin: 0 0 4px;
  letter-spacing: -0.5px;
}
.v2-hero p {
  font-size: 13.5px;
  color: var(--v2-gr);
  margin: 0;
}

/* Form Controls Common */
.v2-fg { margin-bottom: 18px; }
.v2-fg > label {
  display: block;
  font-size: 13px;
  font-weight: 500;
  color: var(--v2-dk);
  margin-bottom: 6px;
}
.v2-fg > label .req { color: #dc2626; margin-left: 2px; }

.v2-iw {
  position: relative;
  display: flex;
  align-items: center;
}
.v2-il {
  position: absolute;
  left: 14px;
  color: #94a3b8;
  display: flex;
  pointer-events: none;
  z-index: 2;
}
.v2-ir {
  position: absolute;
  right: 14px;
  color: #94a3b8;
  display: flex;
  cursor: pointer;
  z-index: 2;
}
.v2-inp {
  width: 100%;
  height: 50px;
  border: 1.5px solid var(--v2-bd);
  border-radius: 10px;
  padding: 0 16px 0 46px;
  font-size: 13.5px;
  font-family: 'Poppins', sans-serif;
  color: var(--v2-dk);
  background: #ffffff;
  outline: none;
  transition: border-color 0.2s, box-shadow 0.2s;
}
.v2-inp:focus {
  border-color: var(--v2-p);
  box-shadow: 0 0 0 3px rgba(29,97,242,0.12);
}
.v2-hint {
  font-size: 12px;
  color: var(--v2-gr);
  margin-top: 5px;
}

/* Step 2 Pixel-Perfect Reference Match Styles */
.v2-step2-title {
  font-size: 26px;
  font-weight: 500;
  color: #0f172a;
  margin: 0 0 4px;
  letter-spacing: -0.5px;
}
.v2-step2-sub {
  font-size: 13.5px;
  color: #64748b;
  margin: 0;
}

/* Step 2 — Redesigned stacked card layout */
.v2-step2-head {
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
  gap: 16px;
  margin-bottom: 14px;
}
.v2-step2-title {
  font-size: 20px;
  font-weight: 600;
  color: #0f172a;
  margin: 0 0 4px;
  line-height: 1.2;
  letter-spacing: -0.2px;
}
.v2-step2-sub {
  font-size: 12.5px;
  color: #64748b;
  margin: 0;
  line-height: 1.45;
  max-width: 480px;
}
.v2-step2-section-label {
  display: block;
  font-size: 13px;
  font-weight: 500;
  color: #0f172a;
  margin-bottom: 10px;
}
.v2-step2-section-label .req {
  color: #ef4444;
  margin-left: 2px;
}

.v2-comp-grid {
  display: flex;
  flex-direction: column;
  gap: 10px;
  margin-bottom: 12px;
  align-items: stretch;
  flex: 0 0 auto;
}
.v2-comp-grid > .v2-comp-card {
  flex: 0 0 auto;
}

.v2-comp-card {
  background: #ffffff;
  border: 1.5px solid #e2e8f0;
  border-radius: 14px;
  padding: 16px 18px;
  min-height: 116px;
  cursor: pointer;
  transition: all 0.2s ease;
  position: relative;
  display: flex;
  flex-direction: column;
  flex: 0 0 auto;
}
.v2-comp-card:hover {
  border-color: #cbd5e1;
  box-shadow: 0 6px 20px rgba(0,0,0,0.04);
  transform: translateY(-1px);
}
.v2-comp-card.selected {
  border: 2px solid #1d61f2;
  background: #f0f6ff;
  box-shadow: 0 8px 24px rgba(29,97,242,0.12);
  padding: 15px 17px;
}

.v2-comp-check {
  position: absolute;
  top: 14px;
  right: 14px;
  width: 22px;
  height: 22px;
  border-radius: 50%;
  border: 1.5px solid #cbd5e1;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.2s;
  background: #ffffff;
}
.v2-comp-card.selected .v2-comp-check {
  background: #1d61f2;
  border-color: #1d61f2;
  color: #ffffff;
}

.v2-comp-card-inner {
  display: flex;
  align-items: flex-start;
  gap: 14px;
  margin-top: 2px;
}
.v2-comp-card-body {
  flex: 0 1 auto;
  min-width: 0;
  display: flex;
  flex-direction: column;
  gap: 4px;
}

.v2-type-icon-badge {
  width: 44px;
  height: 44px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}
.v2-card-title {
  font-size: 14.5px;
  font-weight: 600;
  color: #0f172a;
  line-height: 1.25;
  letter-spacing: -0.1px;
}
.v2-card-desc {
  font-size: 12px;
  color: #64748b;
  line-height: 1.4;
  margin: 2px 0 0;
}

.v2-comp-divider {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 6px;
  margin: 10px 0 10px;
  height: 6px;
}
.v2-comp-divider::before,
.v2-comp-divider::after {
  content: "";
  flex: 1;
  height: 1px;
  background: #e2e8f0;
}
.v2-comp-divider-dot {
  width: 4px;
  height: 4px;
  border-radius: 50%;
  background: #cbd5e1;
}

.v2-comp-examples {
  display: flex;
  flex-wrap: wrap;
  align-items: baseline;
  gap: 0;
  margin-top: 0;
  font-size: 12px;
  line-height: 1.5;
}
.v2-comp-ex-label {
  font-size: 12px;
  font-weight: 500;
  color: #94a3b8;
  margin-right: 6px;
}
.v2-comp-ex-tag {
  font-size: 12px;
  font-weight: 500;
  color: #475569;
  padding: 0;
  white-space: nowrap;
}
.v2-comp-ex-sep {
  display: inline-block;
  width: 4px;
  height: 4px;
  border-radius: 50%;
  background: #cbd5e1;
  margin: 0 8px;
  vertical-align: middle;
}

@media (max-width: 1200px) {
  .v2-comp-card { padding: 12px 14px; }
  .v2-comp-card.selected { padding: 11px 13px; }
  .v2-type-icon-badge { width: 40px; height: 40px; }
}

@media (max-width: 991px) {
  .v2-step2-head { flex-direction: column; gap: 10px; }
}

@media (max-width: 600px) {
  .v2-step2-title { font-size: 18px; }
  .v2-comp-card-inner { gap: 10px; }
  .v2-type-icon-badge { width: 36px; height: 36px; }
  .v2-card-title { font-size: 13.5px; }
}

/* obsolete classes from old design — no-op fallbacks to avoid breakage if any stray ref */
.v2-card-img-wrap { display: none; }
.v2-card-head { display: none; }
.v2-card-hr { display: none; }
.v2-comp-list { display: none; }
.v2-comp-list li { display: none; }
.v2-chk-icon { display: none; }
.v2-comp-banner { display: none; }
.v2-why-box { display: none; }
.v2-why-left { display: none; }
.v2-why-title { display: none; }
.v2-why-sub { display: none; }
.v2-why-items { display: none; }
.v2-why-item { display: none; }
.v2-why-icon { display: none; }
.v2-why-item-txt { display: none; }

@media (max-width: 1200px) {
  /* Removed obsolete .v2-why-* + grid overrides for Step 2 redesign */
}

/* Step 3 Section Cards - Redesigned Pixel-Perfect */
.v2-step3-box {
  background: #ffffff;
  border: 1.5px solid #e2e8f0;
  border-radius: 16px;
  padding: 24px;
  margin-bottom: 24px;
  box-shadow: 0 1px 3px rgba(0,0,0,0.02);
  transition: border-color 0.2s;
  width: 100%;
  box-sizing: border-box;
}
.v2-step3-box:focus-within {
  border-color: #cbd5e1;
}

.v2-step3-head {
  display: flex;
  align-items: center;
  gap: 14px;
  margin-bottom: 18px;
}
.v2-step3-icon {
  width: 42px;
  height: 42px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}
.v2-step3-title {
  font-size: 15px;
  font-weight: 600;
  color: #0f172a;
  margin: 0 0 3px 0;
  line-height: 1.2;
}
.v2-step3-sub {
  font-size: 12.5px;
  color: #64748b;
  line-height: 1.35;
  margin: 0;
}

/* Step 3 Website Group */
.v2-web-group {
  display: flex;
  align-items: center;
  border: 1.5px solid #e2e8f0;
  border-radius: 10px;
  overflow: hidden;
  background: #ffffff;
  height: 50px;
  transition: border-color 0.2s, box-shadow 0.2s;
}
.v2-web-group:focus-within {
  border-color: #1d61f2;
  box-shadow: 0 0 0 3px rgba(29,97,242,0.12);
}
.v2-web-prefix {
  display: flex;
  align-items: center;
  gap: 6px;
  padding: 0 14px;
  background: #f8fafc;
  border-right: 1.5px solid #e2e8f0;
  color: #64748b;
  font-size: 13.5px;
  font-weight: 500;
  height: 100%;
  flex-shrink: 0;
}
.v2-web-input {
  flex: 1;
  border: none !important;
  outline: none !important;
  padding: 0 16px;
  font-size: 13.5px;
  font-family: 'Poppins', sans-serif;
  color: #0f172a;
  background: transparent;
  height: 100%;
}

/* Step 3 Phone Input Container & intlTelInput styling */
.v2-phone-wrap {
  border: 1.5px solid #e2e8f0;
  border-radius: 10px;
  /* overflow:visible is REQUIRED — overflow:hidden clips the ITI country dropdown */
  overflow: visible;
  position: relative;
  height: 50px;
  display: flex;
  align-items: center;
  background: #ffffff;
  transition: border-color 0.2s, box-shadow 0.2s;
  width: 100%;
}
.v2-phone-wrap:focus-within {
  border-color: #1d61f2;
  box-shadow: 0 0 0 3px rgba(29,97,242,0.12);
}
.v2-phone-wrap .iti {
  width: 100% !important;
  height: 100% !important;
  display: flex !important;
  align-items: center !important;
  border-radius: 10px;
  overflow: hidden; /* clip only the iti wrapper so border-radius still applies */
}
.v2-phone-wrap .iti__selected-flag {
  background: #f8fafc !important;
  border-right: 1.5px solid #e2e8f0 !important;
  border-radius: 9px 0 0 9px;
  padding: 0 14px !important;
  height: 100% !important;
  display: flex !important;
  align-items: center !important;
}
.v2-phone-wrap input#contact_number {
  height: 100% !important;
  width: 100% !important;
  border: none !important;
  outline: none !important;
  box-shadow: none !important;
  padding-left: 100px !important;
  font-size: 13.5px !important;
  font-family: 'Poppins', sans-serif !important;
  color: #0f172a !important;
  background: transparent !important;
}
/* Ensure the ITI dropdown list renders above everything */
.iti__country-list {
  z-index: 9999 !important;
  max-height: 220px !important;
  overflow-y: auto !important;
  border-radius: 8px !important;
  box-shadow: 0 8px 24px rgba(0,0,0,0.14) !important;
  border: 1px solid #e2e8f0 !important;
  font-family: 'Poppins', sans-serif !important;
  font-size: 13px !important;
}

/* Step 3 Tagify Input Alignment */
.v2-tagify-wrap {
  position: relative;
  display: flex;
  align-items: center;
  width: 100%;
}
.v2-tagify-wrap .tagify {
  width: 100%;
  min-height: 50px;
  height: auto;
  border: 1.5px solid #e2e8f0 !important;
  border-radius: 10px !important;
  padding: 4px 44px 4px 44px !important;
  font-size: 13.5px;
  font-family: 'Poppins', sans-serif;
  color: #0f172a;
  background: #ffffff;
  display: flex;
  align-items: center;
  transition: border-color 0.2s, box-shadow 0.2s;
}
.v2-tagify-wrap .tagify:focus-within {
  border-color: #1d61f2 !important;
  box-shadow: 0 0 0 3px rgba(29,97,242,0.12) !important;
}
.v2-tagify-wrap .tagify__tag {
  margin: 2px 4px 2px 0;
  height: 30px;
}

/* FormValidation Error Messages Styling */
.fv-plugins-message-container {
  margin-top: 8px !important;
  font-size: 12px !important;
  font-weight: 500 !important;
  color: #dc2626 !important;
}
.fv-plugins-message-container .fv-help-block {
  margin-bottom: 4px;
}

/* Step 4 Review Cards */
.v2-review-grid {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 20px;
  margin-bottom: 24px;
}
.v2-review-card {
  background: #ffffff;
  border: 1.5px solid #e2e8f0;
  border-radius: 16px;
  padding: 20px 22px;
  display: flex;
  flex-direction: column;
}
.v2-review-head {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 16px;
}
.v2-review-icon-title {
  display: flex;
  align-items: center;
  gap: 10px;
  font-size: 15px;
  font-weight: 500;
  color: #0f172a;
}
.v2-review-btn-edit {
  color: #16a34a;
  font-size: 13px;
  font-weight: 500;
  background: none;
  border: none;
  cursor: pointer;
  display: flex;
  align-items: center;
  gap: 4px;
  padding: 0;
}
.v2-review-btn-edit:hover { text-decoration: underline; }

.v2-review-val-title {
  font-size: 18px;
  font-weight: 600;
  color: #0f172a;
  margin-bottom: 4px;
}
.v2-review-val-sub {
  font-size: 12.5px;
  color: #64748b;
  margin-bottom: 16px;
}
.v2-review-meta {
  display: flex;
  align-items: center;
  flex-wrap: nowrap;
  gap: 20px;
  font-size: 13px;
  color: #334155;
  padding-top: 14px;
  border-top: 1px solid #f1f5f9;
}
.v2-review-meta-item {
  display: flex;
  align-items: center;
  gap: 8px;
  min-width: 0;
  flex: 1 1 auto;
}
.v2-review-meta-item > span {
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  min-width: 0;
}

/* Step 4 Workspace Includes Container */
.v2-inc-card {
  background: #ffffff;
  border: 1.5px solid #e2e8f0;
  border-radius: 16px;
  padding: 22px 24px;
  margin-bottom: 24px;
}
.v2-inc-title {
  font-size: 15px;
  font-weight: 500;
  color: #0f172a;
  margin-bottom: 18px;
}
.v2-inc-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 18px;
}
.v2-inc-item {
  display: flex;
  align-items: flex-start;
  gap: 12px;
}
.v2-inc-icon {
  width: 36px;
  height: 36px;
  border-radius: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}
.v2-inc-txt strong {
  display: block;
  font-size: 13px;
  font-weight: 500;
  color: #0f172a;
  line-height: 1.2;
}
.v2-inc-txt span {
  font-size: 11.5px;
  color: #64748b;
}

/* Step 4 Confirmation Box */
.v2-confirm-box {
  background: #eff6ff;
  border: 1px solid #dbeafe;
  border-radius: 14px;
  padding: 16px 20px;
  margin-bottom: 20px;
}
.v2-confirm-box .form-check-label {
  font-size: 13.5px;
  font-weight: 500;
  color: #0f172a;
}
.v2-confirm-sub {
  font-size: 12px;
  color: #64748b;
  margin-top: 4px;
  padding-left: 28px;
}

/* Step 4 Launch Container */
.v2-launch-bar {
  background: #f8fafc;
  border: 1px solid #e2e8f0;
  border-radius: 16px;
  padding: 18px 24px;
  display: flex;
  align-items: center;
  justify-content: space-between;
}
.v2-launch-sec {
  display: flex;
  align-items: center;
  gap: 12px;
}
.v2-launch-sec-icon {
  width: 36px;
  height: 36px;
  border-radius: 10px;
  background: #f0fdf4;
  color: #16a34a;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

/* Buttons */
.v2-btn-primary {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  height: 48px;
  padding: 0 28px;
  background: var(--v2-p);
  color: #ffffff !important;
  font-size: 14px;
  font-weight: 500;
  font-family: 'Poppins', sans-serif;
  border: none;
  border-radius: 10px;
  cursor: pointer;
  transition: background 0.2s, box-shadow 0.2s;
  box-shadow: 0 4px 14px rgba(29,97,242,0.3);
  text-decoration: none !important;
}
.v2-btn-primary:hover {
  background: var(--v2-ph);
  box-shadow: 0 6px 18px rgba(29,97,242,0.4);
}
.v2-btn-primary:disabled {
  opacity: 0.75;
  cursor: not-allowed;
}

@keyframes v2-spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}
.spinner-border {
  display: inline-block;
  width: 1rem;
  height: 1rem;
  vertical-align: -0.125em;
  border: 0.15em solid currentColor;
  border-right-color: transparent;
  border-radius: 50%;
  animation: v2-spin 0.75s linear infinite;
}
.spinner-border-sm {
  width: 0.875rem;
  height: 0.875rem;
  border-width: 0.12em;
}

.v2-btn-secondary {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  height: 44px;
  padding: 0 18px;
  background: #ffffff;
  color: #334155;
  font-size: 13px;
  font-weight: 500;
  font-family: 'Poppins', sans-serif;
  border: 1.5px solid var(--v2-bd);
  border-radius: 9px;
  cursor: pointer;
  transition: all 0.2s;
  text-decoration: none !important;
}
.v2-btn-secondary:hover {
  background: #f1f5f9;
  color: var(--v2-dk);
}

.v2-btn-secondary-blue {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  height: 46px;
  padding: 0 22px;
  background: #eff6ff;
  color: #1d61f2;
  font-size: 13.5px;
  font-weight: 500;
  font-family: 'Poppins', sans-serif;
  border: none;
  border-radius: 10px;
  cursor: pointer;
  transition: all 0.2s;
  text-decoration: none !important;
}
.v2-btn-secondary-blue:hover {
  background: #dbeafe;
  color: #154ec4;
}

.v2-acts {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-top: 28px;
  padding-top: 20px;
  border-top: 1px solid var(--v2-bd);
}

/* Page Footer */
.v2-page-footer {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-top: 32px;
  font-size: 12.5px;
  color: #64748b;
  width: 100%;
}
.v2-page-footer a {
  color: #64748b;
  text-decoration: none;
}
.v2-page-footer a:hover { color: var(--v2-p); }

.iti--allow-dropdown { width: 100% !important; }
.iti__flag { margin-top: 2px !important; }
</style>

<!-- Loading Overlay -->
<div class="page-loader">
  <span class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status"></span>
  <span class="text-white fs-6 fw-semibold mt-3">Loading...</span>
</div>

<div class="v2-wrapper stepper stepper-pills stepper-column" id="kt_create_account_stepper">
  <!-- ═══════════ LEFT COLUMN ═══════════ -->
  <div class="v2-left-col">
    <!-- Logo -->
    <a href="https://jero.works/" class="v2-logo v2-logo-compact">
      <svg width="28" height="28" viewBox="0 0 40 44" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path d="M20 2C9.5 2 1 10.5 1 21H39C39 10.5 30.5 2 20 2Z" fill="#111827"/>
        <path d="M1 21C1 21 7.5 17.5 20 21" stroke="rgba(255,255,255,0.35)" stroke-width="1.2"/>
        <path d="M39 21C39 21 32.5 17.5 20 21" stroke="rgba(255,255,255,0.35)" stroke-width="1.2"/>
        <line x1="20" y1="21" x2="20" y2="37" stroke="#111827" stroke-width="2.6" stroke-linecap="round"/>
        <path d="M20 37C20 40.31 17.31 43 14 43" stroke="#111827" stroke-width="2.6" stroke-linecap="round" fill="none"/>
      </svg>
      <span class="v2-logo-text">jero.works</span>
    </a>

    <!-- Stepper Navigation Card (Timeline) -->
    <div class="v2-stepper-card stepper-nav">
      <!-- Step 1 -->
      <div class="v2-step-item stepper-item current" data-kt-stepper-element="nav">
        <div class="v2-step-badge-wrap">
          <div class="v2-step-badge">
            <span class="v2-step-badge-num">1</span>
            <svg class="v2-step-badge-check" width="14" height="14" viewBox="0 0 20 20" fill="none"><path d="M5 10l3.5 3.5L15 7" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"/></svg>
          </div>
        </div>
        <div class="v2-step-text">
          <div class="v2-step-title">Get Started</div>
          <div class="v2-step-sub">Set up your registration</div>
        </div>
      </div>

      <!-- Step 2 -->
      <div class="v2-step-item stepper-item" data-kt-stepper-element="nav">
        <div class="v2-step-badge-wrap">
          <div class="v2-step-badge">
            <span class="v2-step-badge-num">2</span>
            <svg class="v2-step-badge-check" width="14" height="14" viewBox="0 0 20 20" fill="none"><path d="M5 10l3.5 3.5L15 7" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"/></svg>
          </div>
        </div>
        <div class="v2-step-text">
          <div class="v2-step-title">Organization Type</div>
          <div class="v2-step-sub">Tell us about your organization</div>
        </div>
      </div>

      <!-- Step 3 -->
      <div class="v2-step-item stepper-item" data-kt-stepper-element="nav">
        <div class="v2-step-badge-wrap">
          <div class="v2-step-badge">
            <span class="v2-step-badge-num">3</span>
            <svg class="v2-step-badge-check" width="14" height="14" viewBox="0 0 20 20" fill="none"><path d="M5 10l3.5 3.5L15 7" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"/></svg>
          </div>
        </div>
        <div class="v2-step-text">
          <div class="v2-step-title">Organization Profile</div>
          <div class="v2-step-sub">Add your business and contact details</div>
        </div>
      </div>

      <!-- Step 4 -->
      <div class="v2-step-item stepper-item" data-kt-stepper-element="nav">
        <div class="v2-step-badge-wrap">
          <div class="v2-step-badge">
            <span class="v2-step-badge-num">4</span>
            <svg class="v2-step-badge-check" width="14" height="14" viewBox="0 0 20 20" fill="none"><path d="M5 10l3.5 3.5L15 7" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"/></svg>
          </div>
        </div>
        <div class="v2-step-text">
          <div class="v2-step-title">Launch</div>
          <div class="v2-step-sub">Review your information and get started</div>
        </div>
      </div>
    </div>

    <!-- Need Help Card -->
    <div class="v2-help-card">
      <div class="v2-help-head">
        <div class="v2-help-icon">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="10" fill="#fed7aa"/><path d="M9.5 9.5a2.5 2.5 0 0 1 5 0c0 1.5-1.5 2-2.5 2.5V13" stroke="#ea580c" stroke-width="2" stroke-linecap="round"/><circle cx="12" cy="16.5" r="0.9" fill="#ea580c"/></svg>
        </div>
        <div>
          <div class="v2-help-title">Need Help?</div>
          <div class="v2-help-sub">We are here to assist you</div>
        </div>
      </div>

      <div class="v2-help-rows">
        <a href="mailto:support@jero.works" class="v2-help-row">
          <span class="v2-help-row-ico">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none"><rect x="3" y="5" width="18" height="14" rx="2" stroke="#1d61f2" stroke-width="1.8"/><path d="M3 7l9 6 9-6" stroke="#1d61f2" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
          </span>
          <span class="v2-help-row-txt">support@jero.works</span>
          <svg class="v2-help-row-arrow" width="14" height="14" viewBox="0 0 20 20" fill="none"><path d="M5 10h10M10 5l5 5-5 5" stroke="#cbd5e1" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
        </a>

        <a href="tel:+918868888888" class="v2-help-row">
          <span class="v2-help-row-ico">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none"><path d="M5 4h3l2 5-2.5 1.5a11 11 0 0 0 6 6L15 14l5 2v3a2 2 0 0 1-2 2A16 16 0 0 1 3 6a2 2 0 0 1 2-2Z" stroke="#1d61f2" stroke-width="1.8" stroke-linejoin="round"/></svg>
          </span>
          <span class="v2-help-row-txt">+91 886 888 8888</span>
          <svg class="v2-help-row-arrow" width="14" height="14" viewBox="0 0 20 20" fill="none"><path d="M5 10h10M10 5l5 5-5 5" stroke="#cbd5e1" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
        </a>
      </div>
    </div>
  </div>

  <!-- ═══════════ RIGHT COLUMN ═══════════ -->
  <div class="v2-right-col" id="upgrade-plan">
    <!-- Top Right Bar -->
    <div class="v2-topbar">
      <span id="top-bar-text">Already have an account?</span>
      <a href="{{ route('login') }}" class="v2-btn-signin" id="top-bar-btn">Sign In</a>
    </div>

    <!-- Main Card Container -->
    <form class="v2-main-card" novalidate id="kt_create_account_form" method="post" name="kt_create_account_form">
      <!-- ════════════════════════════════════════════════════════════════ -->
      <!-- STEP 1: Registration Details                                    -->
      <!-- ════════════════════════════════════════════════════════════════ -->
      <div class="current" data-kt-stepper-element="content">
        @csrf
        {{-- Honeypot: invisible to humans; bots fill it. Server silently 200s on non-empty. (Sprint 2 task 2.5) --}}
        <input type="text" name="website_url" id="website_url_hp" value=""
               style="display:none;position:absolute;left:-9999px;" tabindex="-1" autocomplete="off" aria-hidden="true">
        <input type="hidden" name="team_size" id="team_size" value="">
        <input type="hidden" name="contact_code" id="contact_code" value="">

        <div class="v2-hero">
          <div class="v2-hero-ico">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="8" r="4" stroke="#1d61f2" stroke-width="2"/><path d="M4 20c0-4 4-7 8-7s8 3 8 7" stroke="#1d61f2" stroke-width="2" stroke-linecap="round"/></svg>
          </div>
          <div>
            <h1>Let’s Create Your Workspace</h1>
            <p>Tell us a few details to get started.</p>
          </div>
        </div>

        <!-- Company Name -->
        <div class="v2-fg fv-row">
          <label>Company Name <span class="req">*</span></label>
          <div class="v2-iw">
            <span class="v2-il">
              <svg width="18" height="18" viewBox="0 0 20 20" fill="none"><rect x="3" y="3" width="14" height="15" rx="2" stroke="#94a3b8" stroke-width="1.6"/><path d="M7 7h2M11 7h2M7 11h2M11 11h2M9 18v-3h2v3" stroke="#94a3b8" stroke-width="1.6" stroke-linecap="round"/></svg>
            </span>
            <input type="text" class="v2-inp" name="company_name" placeholder="Enter your company or organization name" value="" minlength="2" maxlength="191" />
          </div>
          <div class="v2-hint">This will be the name of your organization on Jero.works</div>
        </div>

        <!-- Full Name -->
        <div class="v2-fg fv-row">
          <label>Full Name <span class="req">*</span></label>
          <div class="v2-iw">
            <span class="v2-il">
              <svg width="18" height="18" viewBox="0 0 20 20" fill="none"><circle cx="10" cy="7" r="3.5" stroke="#94a3b8" stroke-width="1.6"/><path d="M3 17c0-3.3 3.1-6 7-6s7 2.7 7 6" stroke="#94a3b8" stroke-width="1.6" stroke-linecap="round"/></svg>
            </span>
            <input type="text" class="v2-inp" name="name" placeholder="Enter your full name" autocomplete="off" minlength="2" maxlength="191" />
          </div>
          <div class="v2-hint">This should be your full legal name</div>
        </div>

        <!-- Work Email -->
        <div class="v2-fg fv-row">
          <label>Work Email <span class="req">*</span></label>
          <div class="v2-iw">
            <span class="v2-il">
              <svg width="18" height="18" viewBox="0 0 20 20" fill="none"><rect x="2" y="4" width="16" height="12" rx="2" stroke="#94a3b8" stroke-width="1.6"/><path d="M2 7l8 5 8-5" stroke="#94a3b8" stroke-width="1.6" stroke-linecap="round"/></svg>
            </span>
            <input type="email" class="v2-inp" name="email" placeholder="Enter your official work email address" autocomplete="off" />
          </div>
          <div class="v2-hint">We'll use this email for all communications</div>
        </div>

        <!-- Password -->
        <div class="v2-fg fv-row" data-kt-password-meter="true">
          <label>Password <span class="req">*</span></label>
          <div class="v2-iw">
            <span class="v2-il">
              <svg width="18" height="18" viewBox="0 0 20 20" fill="none"><rect x="4" y="9" width="12" height="9" rx="2" stroke="#94a3b8" stroke-width="1.6"/><path d="M7 9V6a3 3 0 1 1 6 0v3" stroke="#94a3b8" stroke-width="1.6" stroke-linecap="round"/></svg>
            </span>
            <input type="password" class="v2-inp" style="padding-right:46px;" name="password" id="password" placeholder="Create a strong password" autocomplete="off" minlength="8" />
            <span class="v2-ir" data-kt-password-meter-control="visibility">
              <i class="bi bi-eye-slash fs-4"></i>
              <i class="bi bi-eye fs-4 d-none"></i>
            </span>
          </div>
          <div class="d-flex align-items-center mt-2 mb-1" data-kt-password-meter-control="highlight">
            <div class="flex-grow-1 bg-secondary bg-active-success rounded h-4px me-2"></div>
            <div class="flex-grow-1 bg-secondary bg-active-success rounded h-4px me-2"></div>
            <div class="flex-grow-1 bg-secondary bg-active-success rounded h-4px me-2"></div>
            <div class="flex-grow-1 bg-secondary bg-active-success rounded h-4px"></div>
          </div>
          <div class="v2-hint">At least 8 characters with a mix of letters, numbers &amp; symbols</div>
        </div>

        <!-- Confirm Password -->
        <div class="v2-fg fv-row" data-kt-password-meter="true">
          <label>Confirm Password <span class="req">*</span></label>
          <div class="v2-iw">
            <span class="v2-il">
              <svg width="18" height="18" viewBox="0 0 20 20" fill="none"><rect x="4" y="9" width="12" height="9" rx="2" stroke="#94a3b8" stroke-width="1.6"/><path d="M7 9V6a3 3 0 1 1 6 0v3" stroke="#94a3b8" stroke-width="1.6" stroke-linecap="round"/></svg>
            </span>
            <input type="password" class="v2-inp" style="padding-right:46px;" name="password_confirmation" id="password_confirmation" placeholder="Re-enter your password" autocomplete="off" minlength="8" />
            <span class="v2-ir" data-kt-password-meter-control="visibility">
              <i class="bi bi-eye-slash fs-4"></i>
              <i class="bi bi-eye fs-4 d-none"></i>
            </span>
          </div>
          <div class="v2-hint">Please enter the same password as above</div>
        </div>

        <!-- Terms Checkbox -->
        <div class="fv-row mt-4 mb-2">
          <label class="form-check form-check-custom form-check-solid cursor-pointer">
            <input class="form-check-input" type="checkbox" checked="checked" name="toc" value="1" />
            <span class="form-check-label text-gray-700 fs-7 ms-2">I Agree <a href="{{ route('termsAndConditions') }}" target="_blank" class="link-primary fw-semibold">Terms and conditions</a>.</span>
          </label>
        </div>

        <!-- Action Bar -->
        <div class="v2-acts">
          <div class="d-flex align-items-center gap-3">
            <button type="button" class="v2-btn-secondary">
              <svg width="16" height="16" viewBox="0 0 20 20" fill="none"><path d="M4 4h12v12H4V4Z" stroke="#475569" stroke-width="1.6"/><path d="M4 8h12M8 4v4" stroke="#475569" stroke-width="1.6"/></svg>
              Save &amp; Exit
            </button>
            <span class="fs-7 text-muted">You can complete setup anytime</span>
          </div>
          <button type="button" class="v2-btn-primary btn-next-step" data-kt-stepper-action="next">
            <span class="indicator-label d-flex align-items-center gap-2">
              Continue
              <svg width="16" height="16" viewBox="0 0 20 20" fill="none"><path d="M4.166 10h11.668M10.833 5l5 5-5 5" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </span>
            <span class="indicator-progress" style="display:none;">
              Please wait... <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
            </span>
          </button>
        </div>
      </div>

      <!-- ════════════════════════════════════════════════════════════════ -->
      <!-- STEP 2: Company Details / Business Model                        -->
      <!-- ════════════════════════════════════════════════════════════════ -->
      <div class="" data-kt-stepper-element="content">
        <!-- Top Header Row -->
        <div class="v2-step2-head">
          <div>
            <h1 class="v2-step2-title">Tell us about your organization</h1>
            <p class="v2-step2-sub">This helps us personalize your workspace and show you the right features, workflows and marketplace.</p>
          </div>
        </div>

        <label class="v2-step2-section-label">Select Company Type <span class="req">*</span></label>

        <div class="v2-comp-grid fv-row">
          <!-- Card 1: Product or Manufacturing -->
          <div class="v2-comp-card selected" onclick="selectCompanyType(3, this)">
            <input type="radio" class="btn-check" name="company_type" value="3" checked />
            <div class="v2-comp-check">
              <svg width="12" height="12" viewBox="0 0 20 20" fill="none"><path d="M4 10l4 4 8-8" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </div>

            <div class="v2-comp-card-inner">
              <div class="v2-type-icon-badge" style="background:#fff7ed; color:#ea580c;">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none">
                  <rect x="3" y="3" width="8" height="18" rx="1" stroke="#ea580c" stroke-width="1.8"/>
                  <rect x="13" y="8" width="8" height="13" rx="1" stroke="#ea580c" stroke-width="1.8"/>
                  <rect x="5" y="6" width="2" height="2" fill="#ea580c"/>
                  <rect x="8" y="6" width="2" height="2" fill="#ea580c"/>
                  <rect x="5" y="10" width="2" height="2" fill="#ea580c"/>
                  <rect x="8" y="10" width="2" height="2" fill="#ea580c"/>
                  <rect x="5" y="14" width="2" height="2" fill="#ea580c"/>
                  <rect x="8" y="14" width="2" height="2" fill="#ea580c"/>
                  <rect x="15" y="11" width="2" height="2" fill="#ea580c"/>
                  <rect x="18" y="11" width="2" height="2" fill="#ea580c"/>
                  <rect x="15" y="15" width="2" height="2" fill="#ea580c"/>
                  <rect x="18" y="15" width="2" height="2" fill="#ea580c"/>
                </svg>
              </div>
              <div class="v2-comp-card-body">
                <strong class="v2-card-title">Product or Manufacturing Companies</strong>
                <p class="v2-card-desc">Ideal for organizations hiring employees for their own business and managing internal recruitment.</p>
                <div class="v2-comp-divider">
                  <span class="v2-comp-divider-dot"></span>
                  <span class="v2-comp-divider-dot"></span>
                  <span class="v2-comp-divider-dot"></span>
                </div>
                <div class="v2-comp-examples">
                  <span class="v2-comp-ex-label">Examples:</span>
                  <span class="v2-comp-ex-tag">Amazon</span><span class="v2-comp-ex-sep"></span><span class="v2-comp-ex-tag">Google</span><span class="v2-comp-ex-sep"></span><span class="v2-comp-ex-tag">TCS</span><span class="v2-comp-ex-sep"></span><span class="v2-comp-ex-tag">Infosys</span><span class="v2-comp-ex-sep"></span><span class="v2-comp-ex-tag">Microsoft</span>
                </div>
              </div>
            </div>
          </div>

          <!-- Card 2: Consulting or Services -->
          <div class="v2-comp-card" onclick="selectCompanyType(2, this)">
            <input type="radio" class="btn-check" name="company_type" value="2" />
            <div class="v2-comp-check">
              <svg width="12" height="12" viewBox="0 0 20 20" fill="none"><path d="M4 10l4 4 8-8" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </div>

            <div class="v2-comp-card-inner">
              <div class="v2-type-icon-badge" style="background:#f1f5f9; color:#475569;">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none">
                  <circle cx="9" cy="8" r="3" stroke="#475569" stroke-width="1.8"/>
                  <circle cx="17" cy="9" r="2.5" stroke="#475569" stroke-width="1.8"/>
                  <path d="M3 19c0-3.3 2.7-6 6-6s6 2.7 6 6" stroke="#475569" stroke-width="1.8" stroke-linecap="round"/>
                  <path d="M14 18c0-2.5 2-4.5 4-4.5s3 2 3 4" stroke="#475569" stroke-width="1.8" stroke-linecap="round"/>
                </svg>
              </div>
              <div class="v2-comp-card-body">
                <strong class="v2-card-title">Consulting or Services Companies</strong>
                <p class="v2-card-desc">For businesses that provide professional services or consulting and hire for client-based projects.</p>
                <div class="v2-comp-divider">
                  <span class="v2-comp-divider-dot"></span>
                  <span class="v2-comp-divider-dot"></span>
                  <span class="v2-comp-divider-dot"></span>
                </div>
                <div class="v2-comp-examples">
                  <span class="v2-comp-ex-label">Examples:</span>
                  <span class="v2-comp-ex-tag">Deloitte</span><span class="v2-comp-ex-sep"></span><span class="v2-comp-ex-tag">Accenture</span><span class="v2-comp-ex-sep"></span><span class="v2-comp-ex-tag">Capgemini</span><span class="v2-comp-ex-sep"></span><span class="v2-comp-ex-tag">Wipro</span>
                </div>
              </div>
            </div>
          </div>

          <!-- Card 3: Recruitment or Staffing -->
          <div class="v2-comp-card" onclick="selectCompanyType(1, this)">
            <input type="radio" class="btn-check" name="company_type" value="1" />
            <div class="v2-comp-check">
              <svg width="12" height="12" viewBox="0 0 20 20" fill="none"><path d="M4 10l4 4 8-8" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </div>

            <div class="v2-comp-card-inner">
              <div class="v2-type-icon-badge" style="background:#eff6ff; color:#1d61f2;">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none">
                  <circle cx="12" cy="8" r="3.5" stroke="#1d61f2" stroke-width="1.8"/>
                  <path d="M5 20c0-3.5 3.1-6 7-6s7 2.5 7 6" stroke="#1d61f2" stroke-width="1.8" stroke-linecap="round"/>
                  <circle cx="12" cy="12" r="10" stroke="#1d61f2" stroke-width="1.8" stroke-dasharray="2 3"/>
                </svg>
              </div>
              <div class="v2-comp-card-body">
                <strong class="v2-card-title">Recruitment or Staffing Companies</strong>
                <p class="v2-card-desc">Perfect for recruitment &amp; staffing agencies that source and submit candidates for client organizations.</p>
                <div class="v2-comp-divider">
                  <span class="v2-comp-divider-dot"></span>
                  <span class="v2-comp-divider-dot"></span>
                  <span class="v2-comp-divider-dot"></span>
                </div>
                <div class="v2-comp-examples">
                  <span class="v2-comp-ex-label">Examples:</span>
                  <span class="v2-comp-ex-tag">Agencies</span><span class="v2-comp-ex-sep"></span><span class="v2-comp-ex-tag">Placement Consultants</span><span class="v2-comp-ex-sep"></span><span class="v2-comp-ex-tag">Recruiting Firms</span>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Action Bar -->
        <div class="v2-acts">
          <button type="button" class="v2-btn-secondary-blue btn-prev-step" data-kt-stepper-action="previous">
            <svg width="16" height="16" viewBox="0 0 20 20" fill="none"><path d="M15.833 10H4.165M9.166 15l-5-5 5-5" stroke="#1d61f2" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
            Previous
          </button>
          <button type="button" class="v2-btn-primary btn-next-step" data-kt-stepper-action="next">
            <span class="indicator-label d-flex align-items-center gap-2">
              Continue
              <svg width="16" height="16" viewBox="0 0 20 20" fill="none"><path d="M4.166 10h11.668M10.833 5l5 5-5 5" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </span>
            <span class="indicator-progress" style="display:none;">
              Please wait... <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
            </span>
          </button>
        </div>
      </div>

      <!-- ════════════════════════════════════════════════════════════════ -->
      <!-- STEP 3: Complete Your Organization Profile                      -->
      <!-- ════════════════════════════════════════════════════════════════ -->
      <div class="" data-kt-stepper-element="content">
        <!-- Top Header Row -->
        <div class="d-flex align-items-start justify-content-between mb-4">
          <div>
            <h1 class="v2-step2-title">Complete Your Organization Profile</h1>
            <p class="v2-step2-sub">Help companies, agencies and candidates connect with your organization.</p>
          </div>
        </div>

        <!-- Section 1: Website -->
        <div class="v2-step3-box fv-row">
          <div class="v2-step3-head">
            <div class="v2-step3-icon" style="background:#eff6ff; color:#1d61f2;">
              <svg width="22" height="22" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="9" stroke="#1d61f2" stroke-width="2"/><path d="M3.6 9h16.8M3.6 15h16.8M12 3a14 14 0 0 1 0 18M12 3a14 14 0 0 0 0 18" stroke="#1d61f2" stroke-width="2"/></svg>
            </div>
            <div>
              <div class="v2-step3-title">Company Website <span class="text-muted fw-normal fs-7">(Optional)</span></div>
              <div class="v2-step3-sub">Displayed on your public organization profile.</div>
            </div>
          </div>
          <div class="v2-web-group">
            <div class="v2-web-prefix">
              <svg width="16" height="16" viewBox="0 0 20 20" fill="none"><circle cx="10" cy="10" r="8" stroke="#64748b" stroke-width="1.6"/><path d="M2 10h16M10 2a12 12 0 0 1 0 16M10 2a12 12 0 0 0 0 16" stroke="#64748b" stroke-width="1.6"/></svg>
              <span>https://</span>
            </div>
            <input type="url" name="website" id="website_field" class="v2-web-input" placeholder="www.yourcompany.com" value="" maxlength="255" />
          </div>
        </div>

        <!-- Section 2: Contact Number -->
        <div class="v2-step3-box fv-row smsForm">
          <div class="v2-step3-head">
            <div class="v2-step3-icon" style="background:#f0fdf4; color:#16a34a;">
              <svg width="22" height="22" viewBox="0 0 24 24" fill="none"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92Z" stroke="#16a34a" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </div>
            <div>
              <div class="v2-step3-title">Business Contact Number <span class="text-danger">*</span></div>
              <div class="v2-step3-sub">Visible only to verified users.</div>
            </div>
          </div>
          <div class="v2-phone-wrap">
            <input name="contact_number" id="contact_number" type="tel" class="v2-inp" placeholder="Enter mobile number" value="" maxlength="15" inputmode="numeric" autocomplete="tel-national" required />
          </div>
        </div>

        <!-- Section 3: Headquarters Location -->
        <div class="v2-step3-box fv-row smsForm">
          <div class="v2-step3-head">
            <div class="v2-step3-icon" style="background:#f5f3ff; color:#7c3aed;">
              <svg width="22" height="22" viewBox="0 0 24 24" fill="none"><path d="M12 2a8 8 0 0 0-8 8c0 5.25 8 12 8 12s8-6.75 8-12a8 8 0 0 0-8-8Z" stroke="#7c3aed" stroke-width="2"/><circle cx="12" cy="10" r="3" fill="#7c3aed"/></svg>
            </div>
            <div>
              <div class="v2-step3-title">Headquarters Location <span class="text-danger">*</span></div>
              <div class="v2-step3-sub">Used for job recommendations and marketplace visibility.</div>
            </div>
          </div>
          <div class="v2-tagify-wrap">
            <span class="v2-il">
              <svg width="18" height="18" viewBox="0 0 20 20" fill="none"><circle cx="9" cy="9" r="6" stroke="#94a3b8" stroke-width="1.6"/><path d="M13.5 13.5L17 17" stroke="#94a3b8" stroke-width="1.6"/></svg>
            </span>
            <input class="v2-inp" value="" id="kt_tagify_work_location" name="kt_tagify_work_location" placeholder="Search city, state or country..." required />
            <span class="v2-ir">
              <svg width="18" height="18" viewBox="0 0 20 20" fill="none"><circle cx="10" cy="10" r="7" stroke="#94a3b8" stroke-width="1.6"/><circle cx="10" cy="10" r="2" fill="#94a3b8"/></svg>
            </span>
          </div>
        </div>

        <!-- Section 4: Platform Default Currency -->
        <div class="v2-step3-box fv-row smsForm mt-6">
          <div class="v2-step3-head">
            <div class="v2-step3-icon" style="background:#ecfdf5; color:#059669;">
              <svg width="22" height="22" viewBox="0 0 24 24" fill="none"><rect x="2" y="5" width="20" height="14" rx="2" stroke="#059669" stroke-width="2"/><line x1="2" y1="10" x2="22" y2="10" stroke="#059669" stroke-width="2"/><circle cx="7" cy="15" r="1.5" fill="#059669"/><circle cx="17" cy="15" r="1.5" fill="#059669"/></svg>
            </div>
            <div class="d-flex align-items-center justify-content-between flex-grow-1">
              <div>
                <div class="v2-step3-title d-flex align-items-center gap-2">
                  Platform Default Currency <span class="text-danger">*</span>
                  <span class="badge badge-light-success fs-8 py-1 px-2 fw-semibold" id="currency_auto_detected_badge">Auto-detected from location</span>
                </div>
                <div class="v2-step3-sub">Primary currency for your organization's jobs, candidate compensation, and talent marketplace workflows.</div>
              </div>
            </div>
          </div>
          <div class="position-relative mt-2">
            @php
              $allSystemCurrencies = \App\Services\CurrencyService::getAllCurrenciesWithMeta();
            @endphp
            <select class="form-select form-select-solid fw-bold" id="platform_currency" name="platform_currency" data-control="select2" data-placeholder="Select default platform currency" style="height: 48px; border-radius: 8px; border: 1px solid #e2e8f0; font-size: 14px;">
              @foreach($allSystemCurrencies as $curr)
                <option value="{{ $curr['code'] }}" data-key="{{ $curr['key'] }}" data-symbol="{{ $curr['symbol'] }}" data-flag="{{ $curr['flag'] }}" {{ $curr['code'] === 'USD' ? 'selected' : '' }}>
                  {{ $curr['flag'] }} {{ $curr['code'] }} - {{ $curr['name'] }} ({{ $curr['symbol'] }})
                </option>
              @endforeach
            </select>
            <input type="hidden" name="currency" id="hidden_currency_input" value="USD" />
          </div>
        </div>

        <!-- Action Bar -->
        <div class="v2-acts">
          <button type="button" class="v2-btn-secondary-blue btn-prev-step" data-kt-stepper-action="previous">
            <svg width="16" height="16" viewBox="0 0 20 20" fill="none"><path d="M15.833 10H4.165M9.166 15l-5-5 5-5" stroke="#1d61f2" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
            Previous
          </button>
          <div class="d-flex align-items-center gap-2 text-success fs-7 fw-semibold">
            <svg width="16" height="16" viewBox="0 0 20 20" fill="none"><circle cx="10" cy="10" r="8" fill="#dcfce7"/><path d="M6 10l3 3 5-5" stroke="#16a34a" stroke-width="1.8" stroke-linecap="round"/></svg>
            All changes are auto-saved
          </div>
          <button type="button" class="v2-btn-primary btn-next-step" data-kt-stepper-action="next">
            <span class="indicator-label d-flex align-items-center gap-2">
              Continue to Launch Workspace
              <svg width="16" height="16" viewBox="0 0 20 20" fill="none"><path d="M4.166 10h11.668M10.833 5l5 5-5 5" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </span>
            <span class="indicator-progress" style="display:none;">
              Please wait... <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
            </span>
          </button>
        </div>
      </div>

      <!-- ════════════════════════════════════════════════════════════════ -->
      <!-- STEP 4: Review & Launch Your Workspace                           -->
      <!-- ════════════════════════════════════════════════════════════════ -->
      <div class="" data-kt-stepper-element="content">
        <!-- Top Header Row -->
        <div class="v2-hero">
          <div>
            <h1 class="v2-step2-title">Review &amp; Launch Your Workspace 🚀</h1>
            <p class="v2-step2-sub">Please review your details below. Once confirmed, your workspace will be ready to use.</p>
          </div>
        </div>

        <div class="v2-review-grid">
          <!-- Card 1: Organization -->
          <div class="v2-review-card">
            <div class="v2-review-head">
              <div class="v2-review-icon-title">
                <div class="v2-type-icon-badge" style="background:#eff6ff; color:#1d61f2;">
                  <svg width="20" height="20" viewBox="0 0 24 24" fill="none"><path d="M3 21h18M5 21V7l8-4v18M13 21V11l6 3v7" stroke="#1d61f2" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </div>
                <span>Organization</span>
              </div>
              <button type="button" onclick="goToStep(1)" class="v2-review-btn-edit">
                <svg width="14" height="14" viewBox="0 0 20 20" fill="none"><path d="M13.586 3.586a2 2 0 1 1 2.828 2.828l-.793.793-2.828-2.828.793-.793ZM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828Z" fill="#16a34a"/></svg>
                Edit
              </button>
            </div>
            <div class="v2-review-val-title" id="preview_company_name">-</div>
            <div class="v2-review-val-sub" id="preview_company_type">-</div>
            <div class="v2-review-meta">
              <div class="v2-review-meta-item">
                <svg width="16" height="16" viewBox="0 0 20 20" fill="none"><circle cx="10" cy="10" r="8" stroke="#1d61f2" stroke-width="1.6"/><path d="M2 10h16M10 2a12 12 0 0 1 0 16M10 2a12 12 0 0 0 0 16" stroke="#1d61f2" stroke-width="1.6"/></svg>
                <strong class="text-dark fs-7">Website</strong>
                <span class="text-primary fw-semibold fs-7" id="preview_website">-</span>
              </div>
              <div class="v2-review-meta-item">
                <svg width="16" height="16" viewBox="0 0 20 20" fill="none"><path d="M10 2a6 6 0 0 0-6 6c0 4.5 6 10 6 10s6-5.5 6-10a6 6 0 0 0-6-6Z" stroke="#7c3aed" stroke-width="1.6"/><circle cx="10" cy="8" r="2" fill="#7c3aed"/></svg>
                <strong class="text-dark fs-7">Headquarters</strong>
                <span class="text-secondary fs-7" id="preview_city">-</span>
              </div>
              <div class="v2-review-meta-item">
                <svg width="16" height="16" viewBox="0 0 20 20" fill="none"><path d="M10 2a8 8 0 1 0 0 16 8 8 0 0 0 0-16Z" stroke="#059669" stroke-width="1.6"/><path d="M10 5v10M7.5 7.5h4a1.5 1.5 0 0 1 0 3h-3a1.5 1.5 0 0 0 0 3h4" stroke="#059669" stroke-width="1.6" stroke-linecap="round"/></svg>
                <strong class="text-dark fs-7">Platform Currency</strong>
                <span class="text-success fw-bold fs-7" id="preview_currency">USD</span>
              </div>
            </div>
          </div>

          <!-- Card 2: Administrator -->
          <div class="v2-review-card">
            <div class="v2-review-head">
              <div class="v2-review-icon-title">
                <div class="v2-type-icon-badge" style="background:#f0fdf4; color:#16a34a;">
                  <svg width="20" height="20" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="8" r="4" stroke="#16a34a" stroke-width="2"/><path d="M4 20c0-4 4-7 8-7s8 3 8 7" stroke="#16a34a" stroke-width="2"/></svg>
                </div>
                <span>Administrator</span>
              </div>
              <button type="button" onclick="goToStep(1)" class="v2-review-btn-edit">
                <svg width="14" height="14" viewBox="0 0 20 20" fill="none"><path d="M13.586 3.586a2 2 0 1 1 2.828 2.828l-.793.793-2.828-2.828.793-.793ZM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828Z" fill="#16a34a"/></svg>
                Edit
              </button>
            </div>
            <div class="v2-review-val-title" id="preview_name">-</div>
            <div class="v2-review-val-sub" id="preview_email">-</div>
            <div class="v2-review-meta">
              <div class="v2-review-meta-item">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92Z" stroke="#334155" stroke-width="2"/></svg>
                <div id="phoneDisplay" style="display:flex; align-items:center; gap:4px;"></div>
                <span class="text-secondary fs-7" id="preview_contact_number">-</span>
              </div>
            </div>
          </div>
        </div>

        <!-- Section: Your Workspace will include -->
        <div class="v2-inc-card">
          <div class="v2-inc-title">Your Workspace will include</div>
          <div class="v2-inc-grid">
            <div class="v2-inc-item">
              <div class="v2-inc-icon" style="background:#f5f3ff; color:#7c3aed;">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none"><path d="M12 2L15 9L22 12L15 15L12 22L9 15L2 12L9 9L12 2Z" fill="#7c3aed"/></svg>
              </div>
              <div class="v2-inc-txt">
                <strong>AI Recruitment ATS</strong>
                <span>End-to-end hiring automation</span>
              </div>
            </div>

            <div class="v2-inc-item">
              <div class="v2-inc-icon" style="background:#f0fdf4; color:#16a34a;">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none"><rect x="3" y="7" width="18" height="13" rx="2" stroke="#16a34a" stroke-width="2"/><path d="M8 7V5a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2" stroke="#16a34a" stroke-width="2"/></svg>
              </div>
              <div class="v2-inc-txt">
                <strong>Job &amp; Candidate Management</strong>
                <span>Post, track and manage easily</span>
              </div>
            </div>

            <div class="v2-inc-item">
              <div class="v2-inc-icon" style="background:#fff7ed; color:#ea580c;">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" stroke="#ea580c" stroke-width="2"/><circle cx="9" cy="7" r="4" stroke="#ea580c" stroke-width="2"/></svg>
              </div>
              <div class="v2-inc-txt">
                <strong>Marketplace Access</strong>
                <span>Connect with agencies &amp; talent</span>
              </div>
            </div>

            <div class="v2-inc-item">
              <div class="v2-inc-icon" style="background:#eff6ff; color:#1d61f2;">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8l-6-6Z" stroke="#1d61f2" stroke-width="2"/><path d="M14 2v6h6M16 13H8M16 17H8M10 9H8" stroke="#1d61f2" stroke-width="2"/></svg>
              </div>
              <div class="v2-inc-txt">
                <strong>Resume Parser</strong>
                <span>Smart extraction &amp; matching</span>
              </div>
            </div>

            <div class="v2-inc-item">
              <div class="v2-inc-icon" style="background:#fef2f2; color:#ef4444;">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2v10Z" stroke="#ef4444" stroke-width="2"/></svg>
              </div>
              <div class="v2-inc-txt">
                <strong>Interview &amp; Feedback</strong>
                <span>Structured interview process</span>
              </div>
            </div>

            <div class="v2-inc-item">
              <div class="v2-inc-icon" style="background:#eff6ff; color:#1d61f2;">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none"><rect x="3" y="12" width="4" height="8" rx="1" fill="#1d61f2"/><rect x="10" y="7" width="4" height="13" rx="1" fill="#1d61f2"/><rect x="17" y="3" width="4" height="17" rx="1" fill="#1d61f2"/></svg>
              </div>
              <div class="v2-inc-txt">
                <strong>Analytics &amp; Reports</strong>
                <span>Insights that drive results</span>
              </div>
            </div>
          </div>
        </div>

        <!-- Section: Confirmation Checkbox Box -->
        <div class="v2-confirm-box">
          <label class="form-check form-check-custom form-check-solid cursor-pointer">
            <input class="form-check-input" type="checkbox" checked="checked" />
            <span class="form-check-label">I confirm that all the above information is correct.</span>
          </label>
          <div class="v2-confirm-sub">You can update these details anytime from settings after launch.</div>
        </div>

        <!-- Section: Final Launch Bar with Previous Button -->
        <div class="v2-launch-bar mb-4">
          <div class="v2-launch-sec">
            <div class="v2-launch-sec-icon">
              <svg width="20" height="20" viewBox="0 0 24 24" fill="none"><rect x="5" y="11" width="14" height="10" rx="2" stroke="#16a34a" stroke-width="2"/><path d="M8 11V7a4 4 0 0 1 8 0v4" stroke="#16a34a" stroke-width="2"/></svg>
            </div>
            <div>
              <strong class="text-dark fs-7 d-block">Secure • Trusted • Encrypted</strong>
              <span class="fs-7 text-muted">Your information is protected and safe with us.</span>
            </div>
          </div>
        </div>

        <!-- Cloudflare Turnstile — auto-mode; token populated into hidden input before AJAX submit. (Sprint 2 task 2.4) -->
        @if(config('services.turnstile.site_key'))
        <div class="cf-turnstile mb-3 d-flex justify-content-center"
             data-sitekey="{{ config('services.turnstile.site_key') }}"
             data-theme="light"
             data-appearance="interaction-only">
        </div>
        @endif

        <!-- Action Bar for Step 4 -->
        <div class="v2-acts">
          <button type="button" class="v2-btn-secondary-blue btn-prev-step" data-kt-stepper-action="previous">
            <svg width="16" height="16" viewBox="0 0 20 20" fill="none"><path d="M15.833 10H4.165M9.166 15l-5-5 5-5" stroke="#1d61f2" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
            Previous
          </button>
          <button type="button" class="v2-btn-primary" data-kt-stepper-action="submit">
            <span class="indicator-label d-flex align-items-center gap-2">
              Create &amp; Launch Workspace
              <svg width="16" height="16" viewBox="0 0 20 20" fill="none"><path d="M4.166 10h11.668M10.833 5l5 5-5 5" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </span>
            <span class="indicator-progress" style="display:none;">
              Please wait... <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
            </span>
          </button>
        </div>
      </div>
    </form>

    <!-- Page Footer -->
    <div class="v2-page-footer">
      <span>© 2026 Jero Technologies Pvt. Ltd. All rights reserved.</span>
      <div class="d-flex align-items-center gap-4">
        <a href="https://jero.works/privacy-policy" target="_blank">Privacy Policy</a>
        <a href="{{ route('termsAndConditions') }}" target="_blank">Terms of Service</a>
        <a href="https://jero.works/support-policy" target="_blank">Support</a>
        <span>🌐 English ∨</span>
      </div>
    </div>
  </div>
</div>

@if(config('services.turnstile.site_key'))
<script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
@endif
<script src="{{ asset('demo/dist/assets/plugins/global/plugins.bundle.js') }}"></script>
<script src="{{ asset('demo/dist/assets/js/scripts.bundle.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.17/js/intlTelInput.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script src="{{ asset('front/assets/js/countryDialCodes.js') }}"></script>

<script>
function selectCompanyType(val, elem) {
  $('.v2-comp-card').removeClass('selected');
  $(elem).addClass('selected');
  $('input[name="company_type"]').prop('checked', false);
  var radio = $(elem).find('input[type="radio"]');
  radio.prop('checked', true).trigger('change');
}

function goToStep(stepNum) {
  if(window.regStepper) {
    window.regStepper.goTo(stepNum);
    scrollToTop();
  }
}

function scrollToTop() {
  window.scrollTo({ top: 0, behavior: 'instant' });
  document.body.scrollTop = 0;
  document.documentElement.scrollTop = 0;
  var container = document.querySelector('.v2-wrapper');
  if (container) container.scrollTop = 0;
}

function updatePreviewData() {
  try {
    var email          = $("input[name=email]").val() || '-'; 
    var name           = $("input[name=name]").val() || '-'; 
    var company_name   = $("input[name=company_name]").val() || '-'; 
    var company_type_val = $("input[name='company_type']:checked").val();
    var company_type   = '-';

    if (company_type_val == 1) {
      company_type = 'Staffing Partners - Recruitment Consultant, Staffing Companies, Man-Power Companies.';
    } else if (company_type_val == 2) {
      company_type = 'Consulting Companies - IT Companies, Trainings Companies, Service Based Companies.';
    } else if (company_type_val == 3) {
      company_type = 'Product & Manufacturing Company - Product Based and similar Companies.';
    }

    var website        = $("input[name=website]").val() || 'N/A';
    var contact_number = $("input[name=contact_number]").val() || '-'; 
    var kt_tagify_val  = $('#kt_tagify_work_location').val() || '';
    var city           = '-';

    if (kt_tagify_val) {
      try {
        var parsed = JSON.parse(kt_tagify_val);
        if (Array.isArray(parsed) && parsed.length > 0 && parsed[0].value) {
          city = parsed[0].value;
        } else {
          city = kt_tagify_val.replace(/\[\{"value":"/g, '').replace(/"\}\]/g, '');
        }
      } catch (err) {
        city = kt_tagify_val.replace(/\[\{"value":"/g, '').replace(/"\}\]/g, '');
      }
    }

    // Use official intlTelInput API to read the selected country's dial code
    var selectedCountryData = (window.phoneInput && window.phoneInput.getSelectedCountryData) ? window.phoneInput.getSelectedCountryData() : {};
    var itiDialCode = selectedCountryData.dialCode ? ('+' + selectedCountryData.dialCode) : '+91';
    var iti__active = selectedCountryData.iso2 || 'in';
    $('#contact_code').val(itiDialCode);

    $('#preview_email').text(email);
    $('#preview_name').text(name);
    $('#preview_company_name').text(company_name);
    $('#preview_company_type').text(company_type);	
    $('#preview_website').text(website);
    $('#preview_contact_number').text(contact_number);
    $('#preview_city').text(city || 'N/A');

    var $platformCurr = $('#platform_currency');
    if ($platformCurr.length) {
      var currText = $platformCurr.find('option:selected').text().trim() || $platformCurr.val() || 'USD';
      $('#preview_currency').text(currText);
    }

    const flagContainer = document.getElementById('phoneDisplay');
    if (flagContainer && iti__active) {
      $('#phoneDisplay').html('');
      const flagDiv = document.createElement("div");
      flagDiv.className = `iti__flag iti__${iti__active}`;
      // Prefer the live dial code from the ITI API; fall back to the lookup table
      const dialCode = itiDialCode || (typeof countryDialCodes !== 'undefined' ? countryDialCodes[iti__active] : '') || '';
      const dialCodeSpan = document.createElement("span");
      dialCodeSpan.textContent = dialCode;
      flagContainer.appendChild(flagDiv);
      flagContainer.appendChild(dialCodeSpan);
    }
  } catch(err) {
    console.error("Preview update error:", err);
  }
}

// Tagify
let inputId = `#kt_tagify_work_location`;
let inputElement = document.querySelector(inputId);
let tagifyInstance = null;

if (inputElement) {
  tagifyInstance = new Tagify(inputElement, {
    whitelist: [],
    enforceWhitelist: true,
    maxTags: 1,
    dropdown: {
      maxItems: 20,
      classname: "",
      enabled: 0,
      closeOnSelect: true
    }
  });

  tagifyInstance.on('input', function(e) {
    tagifyInstance.dropdown.show();
  });

  tagifyInstance.on('dropdown:select', function(e) {
    tagifyInstance.addTags([e.detail.data.value]);
    if (window.formValidators && window.formValidators[2]) {
      window.formValidators[2].revalidateField('kt_tagify_work_location');
    }
    if (typeof autoDetectPlatformCurrency === 'function') {
      autoDetectPlatformCurrency(e.detail.data.value, '');
    }
    updatePreviewData();
  });

  tagifyInstance.on('change', function(e) {
    if (window.formValidators && window.formValidators[2]) {
      window.formValidators[2].revalidateField('kt_tagify_work_location');
    }
    if (typeof autoDetectPlatformCurrency === 'function') {
      var val = $('#kt_tagify_work_location').val();
      autoDetectPlatformCurrency(val, '');
    }
    updatePreviewData();
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
        if (Array.isArray(data)) {
          tagifyInstance.settings.whitelist = data; 
          tagifyInstance.dropdown.show.call(tagifyInstance);
        }
      }
    });
  }, 50);

  tagifyInstance.on('input', function(e) {
    var query = e.detail.value.trim();
    if (query.length < 1) return;
    fetchSuggestions(query);
  });
}

// Phone Input — intlTelInput v17 integration
// window.phoneInput is used throughout (updatePreviewData, countrychange seed).
// dropdownContainer:document.body lets the country list escape overflow:hidden ancestors.
// utilsScript enables full number validation (isValidNumber).
const phoneInputField = document.querySelector("#contact_number");
window.phoneInput = window.intlTelInput(phoneInputField, {
  separateDialCode: true,
  initialCountry: "in",
  preferredCountries: ["in", "us", "gb", "ca", "au"],
  autoPlaceholder: "off",
  dropdownContainer: document.body,
  utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.17/js/utils.js"
});

// Numeric-only enforcement: strip non-digit chars and apply E.164 length cap (15 digits).
(function () {
  const maxLen = 15;
  function sanitizeDigits(raw) { return (raw || '').replace(/\D+/g, '').slice(0, maxLen); }
  phoneInputField.setAttribute('inputmode', 'numeric');
  phoneInputField.setAttribute('pattern', '[0-9]+');
  phoneInputField.addEventListener('input', function () {
    const caret = phoneInputField.selectionStart || 0;
    const before = phoneInputField.value.slice(0, caret);
    const sanitizedFull = sanitizeDigits(phoneInputField.value);
    const sanitizedBefore = sanitizeDigits(before);
    if (phoneInputField.value !== sanitizedFull) {
      phoneInputField.value = sanitizedFull;
      const newCaret = sanitizedBefore.length;
      try { phoneInputField.setSelectionRange(newCaret, newCaret); } catch (_) {}
    }
    phoneInputField.setCustomValidity('');
  });
  phoneInputField.addEventListener('paste', function (e) {
    e.preventDefault();
    const text = (e.clipboardData || window.clipboardData).getData('text') || '';
    document.execCommand('insertText', false, sanitizeDigits(text));
  });
  phoneInputField.addEventListener('keypress', function (e) {
    if (e.ctrlKey || e.metaKey || e.altKey) return;
    const ch = String.fromCharCode(e.which || e.keyCode || 0);
    if (!/[0-9]/.test(ch)) { e.preventDefault(); }
  });
})();

// Sync the hidden contact_code field whenever the user picks a different flag.
// Uses the official ITI API — fires AFTER the library updates its internal state.
phoneInputField.addEventListener("countrychange", function () {
  var data = window.phoneInput.getSelectedCountryData();
  var dialCode = data.dialCode ? ('+' + data.dialCode) : '';
  $('#contact_code').val(dialCode);
  if (typeof autoDetectPlatformCurrency === 'function') {
    autoDetectPlatformCurrency('', data.iso2 || '');
  }
  updatePreviewData();
});

// Seed the hidden field immediately on page load with the default country (+91).
$(document).ready(function () {
  var data = window.phoneInput.getSelectedCountryData();
  var dialCode = data.dialCode ? ('+' + data.dialCode) : '+91';
  $('#contact_code').val(dialCode);
  if (typeof autoDetectPlatformCurrency === 'function') {
    autoDetectPlatformCurrency('', data.iso2 || 'in');
  }
});

// Currency Auto-Detection & Platform Currency Synchronization
window.userManuallySelectedCurrency = false;
var countryIsoToCurrencyMap = {
  'in': 'INR', 'us': 'USD', 'gb': 'GBP', 'uk': 'GBP', 'ae': 'AED',
  'ca': 'CAD', 'au': 'AUD', 'sg': 'SGD', 'ch': 'CHF', 'jp': 'JPY',
  'cn': 'CNY', 'nz': 'NZD', 'za': 'ZAR', 'br': 'BRL', 'ph': 'PHP',
  'my': 'MYR', 'th': 'THB', 'id': 'IDR', 'vn': 'VND', 'se': 'SEK',
  'no': 'NOK', 'dk': 'DKK', 'pl': 'PLN', 'mx': 'MXN', 'hk': 'HKD',
  'tw': 'TWD', 'kr': 'KRW', 'tr': 'TRY', 'sa': 'SAR', 'qa': 'QAR',
  'kw': 'KWD', 'bh': 'BHD', 'om': 'OMR', 'eg': 'EGP', 'il': 'ILS',
  'cz': 'CZK', 'hu': 'HUF', 'ro': 'RON', 'cl': 'CLP', 'co': 'COP',
  'ar': 'ARS', 'ng': 'NGN', 'ke': 'KES', 'pk': 'PKR', 'bd': 'BDT',
  'lk': 'LKR', 'np': 'NPR',
  // Eurozone
  'de': 'EUR', 'fr': 'EUR', 'it': 'EUR', 'es': 'EUR', 'nl': 'EUR',
  'be': 'EUR', 'ie': 'EUR', 'at': 'EUR', 'pt': 'EUR', 'fi': 'EUR',
  'gr': 'EUR', 'sk': 'EUR', 'si': 'EUR', 'lu': 'EUR', 'cy': 'EUR',
  'ee': 'EUR', 'lv': 'EUR', 'lt': 'EUR', 'mt': 'EUR'
};

function autoDetectPlatformCurrency(locText, countryIso) {
  if (window.userManuallySelectedCurrency) return;
  var detected = null;

  if (locText && typeof locText === 'string') {
    var lower = locText.toLowerCase();
    if (lower.indexOf('india') !== -1 || lower.indexOf('bharat') !== -1) detected = 'INR';
    else if (lower.indexOf('united states') !== -1 || lower.indexOf('usa') !== -1 || lower.indexOf('america') !== -1) detected = 'USD';
    else if (lower.indexOf('united kingdom') !== -1 || lower.indexOf('london') !== -1 || lower.indexOf('england') !== -1 || lower.indexOf('britain') !== -1) detected = 'GBP';
    else if (lower.indexOf('emirates') !== -1 || lower.indexOf('dubai') !== -1 || lower.indexOf('uae') !== -1 || lower.indexOf('abu dhabi') !== -1) detected = 'AED';
    else if (lower.indexOf('germany') !== -1 || lower.indexOf('france') !== -1 || lower.indexOf('spain') !== -1 || lower.indexOf('italy') !== -1 || lower.indexOf('netherlands') !== -1 || lower.indexOf('ireland') !== -1) detected = 'EUR';
    else if (lower.indexOf('canada') !== -1) detected = 'CAD';
    else if (lower.indexOf('australia') !== -1 || lower.indexOf('sydney') !== -1 || lower.indexOf('melbourne') !== -1) detected = 'AUD';
    else if (lower.indexOf('singapore') !== -1) detected = 'SGD';
    else if (lower.indexOf('japan') !== -1 || lower.indexOf('tokyo') !== -1) detected = 'JPY';
  }

  if (!detected && countryIso) {
    var isoLower = countryIso.toLowerCase();
    if (countryIsoToCurrencyMap[isoLower]) {
      detected = countryIsoToCurrencyMap[isoLower];
    }
  }

  if (detected) {
    var $sel = $('#platform_currency');
    if ($sel.length && $sel.val() !== detected) {
      $sel.val(detected).trigger('change.select2');
      $('#hidden_currency_input').val(detected);
      $('#currency_auto_detected_badge')
        .text('Auto-detected: ' + detected)
        .removeClass('badge-light-primary')
        .addClass('badge-light-success');
      updatePreviewData();
    }
  }
}

$(document).on('change', '#platform_currency', function () {
  var val = $(this).val();
  $('#hidden_currency_input').val(val);
  if ($(this).is(':focus') || $(this).data('select2-open')) {
    window.userManuallySelectedCurrency = true;
    $('#currency_auto_detected_badge')
      .text('Custom selection: ' + val)
      .removeClass('badge-light-success')
      .addClass('badge-light-primary');
  }
  updatePreviewData();
});

// Original Stepper & FormValidation Integration
$(document).ready(function() {  
  var KTCreateAccount = (function () {
    var e, t, i, o, r, s, a = [];

    function updateProgressBar(stepIndex) {
      const stepTexts = ["Step 1 of 4", "Step 2 of 4", "Step 3 of 4", "Step 4 of 4"];
      const pctTexts  = ["25% Complete", "50% Complete", "75% Complete", "100% Complete"];
      const pcts      = ["25%", "50%", "75%", "100%"];
      const times     = ["Estimated Time: 2 Minutes", "Est. Time: 2 Min", "Est. Time: 2-3 Min", "All steps done"];

      const idx = Math.max(0, Math.min(stepIndex - 1, 3));
      $('#progress-step-text').text(stepTexts[idx]);
      $('#progress-pct-text').text(pctTexts[idx]);
      $('#progress-bar-fill').css('width', pcts[idx]);
      $('#progress-time-text').text(times[idx]);
    }

    return {
      init: function () {
        (i = document.querySelector("#kt_create_account_stepper")) && ( 
          (o = i.querySelector("#kt_create_account_form")), 
          (r = i.querySelector('[data-kt-stepper-action="submit"]')),
          (s = new KTStepper(i)),
          (window.regStepper = s),

          s.on("kt.stepper.changed", function(e) {
            updateProgressBar(e.getCurrentStepIndex());
            updatePreviewData();
            scrollToTop();
          }),

          // Delegated Next Step Click Handler across ALL step buttons
          $(document).on('click', '.btn-next-step, [data-kt-stepper-action="next"]', function(ev) {
            ev.preventDefault();
            ev.stopPropagation();
            if (!s) return;

            var btn = $(this);
            var label = btn.find('.indicator-label');
            var progress = btn.find('.indicator-progress');

            function showLoading() {
              btn.prop('disabled', true);
              if (label.length && progress.length) {
                label.hide();
                progress.show();
              }
            }

            function hideLoading() {
              btn.prop('disabled', false);
              if (label.length && progress.length) {
                label.show();
                progress.hide();
              }
            }

            showLoading();

            var currentStep = s.getCurrentStepIndex();

            if (currentStep === 1) {
              var t1 = a[0];
              if (t1) {
                t1.validate().then(function (status) {
                  if (status === "Valid") {
                    setTimeout(function() {
                      hideLoading();
                      s.goNext();
                      updatePreviewData();
                      scrollToTop();
                    }, 150);
                  } else {
                    hideLoading();
                    Swal.fire({
                      text: "Sorry, looks like there are some mandatory fields missing on Step 1.",
                      icon: "error",
                      buttonsStyling: false,
                      confirmButtonText: "Ok, got it!",
                      customClass: { confirmButton: "btn btn-light" },
                    }).then(function () { scrollToTop(); });
                  }
                });
              } else {
                setTimeout(function() {
                  hideLoading();
                  s.goNext();
                  updatePreviewData();
                  scrollToTop();
                }, 150);
              }
            } else if (currentStep === 2) {
              var companyType = $("input[name='company_type']:checked").val();
              if (!companyType) {
                hideLoading();
                Swal.fire({
                  text: "Please select how your organization operates.",
                  icon: "error",
                  buttonsStyling: false,
                  confirmButtonText: "Ok, got it!",
                  customClass: { confirmButton: "btn btn-light" },
                });
                return;
              }
              setTimeout(function() {
                hideLoading();
                s.goNext();
                updatePreviewData();
                scrollToTop();
              }, 150);
            } else if (currentStep === 3) {
              var t3 = a[2];
              if (t3) {
                t3.validate().then(function (status) {
                  if (status === "Valid") {
                    setTimeout(function() {
                      hideLoading();
                      s.goNext();
                      updatePreviewData();
                      scrollToTop();
                    }, 150);
                  } else {
                    hideLoading();
                    Swal.fire({
                      text: "Please fill in all mandatory fields on Step 3.",
                      icon: "error",
                      buttonsStyling: false,
                      confirmButtonText: "Ok, got it!",
                      customClass: { confirmButton: "btn btn-light" },
                    }).then(function () { scrollToTop(); });
                  }
                });
              } else {
                setTimeout(function() {
                  hideLoading();
                  s.goNext();
                  updatePreviewData();
                  scrollToTop();
                }, 150);
              }
            } else {
              setTimeout(function() {
                hideLoading();
                s.goNext();
                updatePreviewData();
                scrollToTop();
              }, 150);
            }
          }),

          // Delegated Previous Step Click Handler
          $(document).on('click', '.btn-prev-step, [data-kt-stepper-action="previous"]', function(ev) {
            ev.preventDefault();
            ev.stopPropagation();
            if (!s) return;
            s.goPrevious();
            scrollToTop();
          }),

          // Step 1 Validation
          a.push(
            FormValidation.formValidation(o, {
              fields: {
                company_name: { validators: { notEmpty: { message: "Company name is required" } } },
                name: { validators: { notEmpty: { message: "Name is required" } } },
                email: { validators: { notEmpty: { message: "Email is required" }, emailAddress: { message: "The value is not a valid email address" } } },
                password: { validators: { notEmpty: { message: "Please enter password" } } },
                password_confirmation: {
                  validators: {
                    notEmpty: { message: "Please enter confirm password" },
                    identical: {
                      compare: function() {
                        return o.querySelector('[name="password"]').value;
                      },
                      message: "The password and its confirm are not the same"
                    }
                  }
                },
                toc: { validators: { notEmpty: { message: "You must accept the terms and conditions" } } }
              },
              plugins: { trigger: new FormValidation.plugins.Trigger(), bootstrap: new FormValidation.plugins.Bootstrap5({ rowSelector: '.fv-row', eleInvalidClass: '', eleValidClass: '' }) }
            })
          ),

          // Step 2 Validation
          a.push(
            FormValidation.formValidation(o, {
              fields: {
                company_type: { validators: { notEmpty: { message: "Company type is required" } } }
              },
              plugins: { trigger: new FormValidation.plugins.Trigger(), bootstrap: new FormValidation.plugins.Bootstrap5({ rowSelector: ".fv-row", eleInvalidClass: "", eleValidClass: "" }) }
            })
          ),

          // Step 3 Validation
          a.push(
            FormValidation.formValidation(o, {
              fields: {
                contact_number: {
                  validators: {
                    notEmpty: { message: "Contact Number is required" },
                    digits: { message: "Contact Number must contain only digits" },
                    stringLength: { min: 4, max: 15, message: "Please enter a valid mobile number (4–15 digits)" }
                  }
                },
                kt_tagify_work_location: { validators: { notEmpty: { message: "City is required" } } }
              },
              plugins: { trigger: new FormValidation.plugins.Trigger(), bootstrap: new FormValidation.plugins.Bootstrap5({ rowSelector: ".fv-row", eleInvalidClass: "", eleValidClass: "" }) }
            })
          ),

          (window.formValidators = a),

          // Trigger FormValidation check without calling jQuery change or modifying input value
          $('#contact_number').on('input', function() {
            if (a[2]) {
              a[2].revalidateField('contact_number');
            }
            updatePreviewData();
          }),

          $('#website_field').on('input', function() {
            updatePreviewData();
          }),

          r.addEventListener("click", function (t) {
            t.preventDefault();
            var btn = $(r);
            btn.prop("disabled", true).attr("data-kt-indicator", "on");

            btn.find('.indicator-label').hide();
            btn.find('.indicator-progress').css('display', 'inline-flex').show();

            if (!navigator.onLine) {
              Swal.fire({ title: 'No Internet', text: 'You are currently offline or the server is unreachable.', icon: 'warning', confirmButtonText: 'Ok, got it!', customClass: { confirmButton: 'btn btn-warning' } });
              btn.find('.indicator-label').show();
              btn.find('.indicator-progress').hide();
              btn.removeAttr('data-kt-indicator').prop("disabled", false);
              return;
            }

            $.ajax({
              url: "{{route('company-register')}}",
              container: '#kt_create_account_form',
              type: "POST",
              data: $('#kt_create_account_form').serialize(),
              success: function(response) {
                if(response.status == 'success') {	
                  $('#kt_create_account_form').trigger("reset");
                  Swal.fire("Sent!", "Your account has been created successfully.\n Check your email to activate your account.", "success");
                  $('#kt_create_account_stepper').html(response.view);
                } else {
                  btn.find('.indicator-label').show();
                  btn.find('.indicator-progress').hide();
                  btn.removeAttr('data-kt-indicator').prop("disabled", false);
                  Swal.fire("Error!", response.message || "You need to fill all the form fields!", "error");
                }
              },
              error: function(xhr) {
                btn.find('.indicator-label').show();
                btn.find('.indicator-progress').hide();
                btn.removeAttr('data-kt-indicator').prop("disabled", false);
                if (xhr.status === 0 || !navigator.onLine) {
                  Swal.fire({ title: 'No Internet', text: 'You are currently offline or the server is unreachable. Please check your connection and try again.', icon: 'warning', confirmButtonText: 'Ok, got it!', customClass: { confirmButton: 'btn btn-warning' } });
                } else {
                  Swal.fire('Error!', 'Something went wrong (HTTP ' + xhr.status + '). Please try again.', 'error');
                }
              }
            });
          }),

          // Email check handler
          $(o.querySelector('[name="email"]')).on("change", function () {            
            var email = $("input[name=email]").val();
            if(email != '') {
              $('.page-loader').css('display', 'flex');
              $.ajax({
                url: "{{route('company-register-chk')}}",
                container: '#kt_create_account_form',
                type: "POST",
                data: $('#kt_create_account_form').serialize(),
                success: function(response) {
                  $('.page-loader').css('display', 'none');
                  if(response.status == 'fail') {
                    $("input[name=email]").val('');
                    Swal.fire({
                      text: "Sorry, Email ["+email+"] is already registered, Please use another email.",
                      icon: "error",
                      buttonsStyling: !1,
                      confirmButtonText: "Ok, got it!",
                      customClass: { confirmButton: "btn btn-light" },
                    }).then(function () {
                      scrollToTop();
                    });
                  } else if(response.status == 'emailchecker') {
                    $("input[name=email]").val('');
                    Swal.fire({
                      text: response.message,
                      icon: "error",
                      buttonsStyling: !1,
                      confirmButtonText: "Ok, got it!",
                      customClass: { confirmButton: "btn btn-light" },
                    }).then(function () {
                      scrollToTop();
                    });
                  } else {
                    $('.verification_email').val(email);
                  }
                },
                error: function(xhr) {
                  $('.page-loader').css('display', 'none');
                  if (xhr.status === 0 || !navigator.onLine) {
                    Swal.fire({ title: 'No Internet', text: 'You are currently offline or the server is unreachable.', icon: 'warning', confirmButtonText: 'Ok, got it!', customClass: { confirmButton: 'btn btn-warning' } });
                  } else {
                    Swal.fire('Error!', 'Something went wrong. Please try again.', 'error');
                  }
                }
              });
            }  	
          }),

          // Contact Number AJAX check handler
          $(o.querySelector('[name="contact_number"]')).on("change", function () {
            var contact_number = $("input[name=contact_number]").val();
            if(contact_number != '' && contact_number.length >= 7) {
              $('.page-loader').css('display', 'flex');
              $.ajax({
                url: "{{route('contact-company-register-chk')}}",
                container: '#kt_create_account_form',
                type: "POST",
                data: $('#kt_create_account_form').serialize(),
                success: function(response) {
                  $('.page-loader').css('display', 'none');
                  if(response.status == 'fail') {
                    $("input[name=contact_number]").val('');
                    if (a[2]) { a[2].revalidateField('contact_number'); }
                    Swal.fire({
                      text: "Sorry, Contact Number ["+contact_number+"] is already registered, Please use another Contact Number.",
                      icon: "error",
                      buttonsStyling: !1,
                      confirmButtonText: "Ok, got it!",
                      customClass: { confirmButton: "btn btn-light" },
                    }).then(function () {
                      scrollToTop();
                    });
                  }
                },
                error: function(xhr) {
                  $('.page-loader').css('display', 'none');
                  if (xhr.status === 0 || !navigator.onLine) {
                    Swal.fire({ title: 'No Internet', text: 'You are currently offline or the server is unreachable.', icon: 'warning', confirmButtonText: 'Ok, got it!', customClass: { confirmButton: 'btn btn-warning' } });
                  } else {
                    Swal.fire('Error!', 'Something went wrong. Please try again.', 'error');
                  }
                }
              });
            }  	
          }),

          // Website check handler
          $(o.querySelector('[name="website"]')).on("change", function () {            
            var website = $("input[name=website]").val();
            if(website != '') {
              $('.page-loader').css('display', 'flex');
              $.ajax({
                url: "{{route('domain-company-register-chk')}}",
                container: '#kt_create_account_form',
                type: "POST",
                data: $('#kt_create_account_form').serialize(),
                success: function(response) {
                  $('.page-loader').css('display', 'none');
                  if(response.status == 'fail') {
                    $("input[name=website]").val('');
                    Swal.fire({
                      title: 'Account Already Exists',
                      html: 'An account is already registered on <strong>Jero.Works</strong> using <strong><a href="javascript:void();">'+response.company_email+'</a></strong> <br><br>If you’ve forgotten your password, please use the <strong>“Forgot Password”</strong> option to log in.<br><br>If this email doesn’t belong to your organization, contact us at <a href="mailto:support@jero.works"> support@jero.works</a>.<br><br>Please avoid creating multiple accounts for the same organization. Duplicate accounts may lead to suspension.',
                      icon: "warning",
                      width: '800px',
                      buttonsStyling: !1,
                      confirmButtonText: "Ok, got it!",
                      customClass: { confirmButton: "btn btn-light" },
                    }).then(function () {
                      scrollToTop();
                    });
                  }
                },
                error: function(xhr) {
                  $('.page-loader').css('display', 'none');
                  if (xhr.status === 0 || !navigator.onLine) {
                    Swal.fire({ title: 'No Internet', text: 'You are currently offline or the server is unreachable.', icon: 'warning', confirmButtonText: 'Ok, got it!', customClass: { confirmButton: 'btn btn-warning' } });
                  } else {
                    Swal.fire('Error!', 'Something went wrong. Please try again.', 'error');
                  }
                }
              });
            }  	
          })
        );
      }
    };
  })();

  KTUtil.onDOMContentLoaded(function () {
    KTCreateAccount.init();
  });         
});         
</script>
@endsection
