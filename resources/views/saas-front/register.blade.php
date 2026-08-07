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
  width: 320px;
  flex-shrink: 0;
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.v2-logo {
  display: flex;
  align-items: center;
  gap: 10px;
  text-decoration: none;
  margin-bottom: 8px;
}
.v2-logo-text {
  font-size: 24px;
  font-weight: 800;
  color: var(--v2-dk);
  letter-spacing: -0.5px;
  line-height: 1;
}
.v2-logo-sub {
  font-size: 11.5px;
  font-weight: 600;
  color: var(--v2-gr);
  margin-top: 4px;
  display: block;
}

.v2-aside-hero h2 {
  font-size: 22px;
  font-weight: 800;
  color: var(--v2-dk);
  margin: 0 0 6px;
  letter-spacing: -0.4px;
}
.v2-aside-hero p {
  font-size: 13px;
  color: var(--v2-gr);
  margin: 0;
  line-height: 1.5;
}

/* Progress Card */
.v2-prog-card {
  background: #ffffff;
  border-radius: 14px;
  padding: 16px;
  box-shadow: 0 1px 3px rgba(0,0,0,0.03);
  border: 1px solid #edf0f4;
}
.v2-prog-head {
  display: flex;
  justify-content: space-between;
  align-items: center;
  font-size: 12px;
  font-weight: 600;
}
.v2-prog-bg {
  width: 100%;
  height: 7px;
  background: #f1f5f9;
  border-radius: 999px;
  margin: 10px 0;
  overflow: hidden;
}
.v2-prog-fill {
  height: 100%;
  background: linear-gradient(90deg, #1d61f2, #3b82f6);
  border-radius: 999px;
  transition: width 0.3s ease;
}

/* Stepper Card */
.v2-stepper-card {
  background: #ffffff;
  border-radius: 14px;
  padding: 12px;
  box-shadow: 0 1px 3px rgba(0,0,0,0.03);
  border: 1px solid #edf0f4;
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.v2-step-item {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 10px 12px;
  border-radius: 12px;
  border: 1px solid transparent;
  transition: all 0.2s ease;
  text-decoration: none !important;
}
.v2-step-item.current {
  background: var(--v2-pl);
  border-color: var(--v2-pb);
}
.v2-step-badge {
  width: 30px;
  height: 30px;
  border-radius: 50%;
  background: #f1f5f9;
  border: 1.5px solid var(--v2-bd);
  color: var(--v2-gr);
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 12.5px;
  font-weight: 700;
  flex-shrink: 0;
  transition: all 0.2s ease;
}
.v2-step-item.current .v2-step-badge {
  background: var(--v2-p);
  border-color: var(--v2-p);
  color: #ffffff;
}
.v2-step-item.completed .v2-step-badge {
  background: #16a34a;
  border-color: #16a34a;
  color: #ffffff;
}
.v2-step-title {
  font-size: 13px;
  font-weight: 700;
  color: var(--v2-dk);
  line-height: 1.2;
}
.v2-step-item.current .v2-step-title { color: var(--v2-p); }
.v2-step-sub {
  font-size: 11px;
  color: var(--v2-gr);
  margin-top: 2px;
}

/* Value Props Card */
.v2-value-card {
  background: #ffffff;
  border-radius: 14px;
  padding: 16px;
  box-shadow: 0 1px 3px rgba(0,0,0,0.03);
  border: 1px solid #edf0f4;
}
.v2-value-title {
  font-size: 12.5px;
  font-weight: 700;
  color: var(--v2-dk);
  margin-bottom: 12px;
}
.v2-value-row {
  display: flex;
  align-items: flex-start;
  gap: 10px;
  margin-bottom: 10px;
}
.v2-value-row:last-child { margin-bottom: 0; }
.v2-value-icon {
  width: 26px;
  height: 26px;
  border-radius: 6px;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}
.v2-value-txt strong {
  display: block;
  font-size: 11.5px;
  font-weight: 700;
  color: var(--v2-dk);
  line-height: 1.2;
}
.v2-value-txt span {
  font-size: 10.5px;
  color: var(--v2-gr);
}

/* Security Box */
.v2-sec-card {
  background: #ffffff;
  border-radius: 14px;
  padding: 14px 16px;
  box-shadow: 0 1px 3px rgba(0,0,0,0.03);
  border: 1px solid #edf0f4;
  display: flex;
  align-items: center;
  gap: 12px;
}
.v2-sec-card strong { display: block; font-size: 12px; font-weight: 700; color: var(--v2-dk); }
.v2-sec-card span { font-size: 11px; color: var(--v2-gr); }

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
  margin-bottom: 20px;
  font-size: 13px;
}
.v2-topbar span { color: var(--v2-gr); }
.v2-btn-signin {
  padding: 7px 20px;
  border: 1.5px solid var(--v2-p);
  border-radius: 8px;
  color: var(--v2-p);
  font-weight: 700;
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
  font-weight: 800;
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
  font-weight: 600;
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
  font-weight: 800;
  color: #0f172a;
  margin: 0 0 4px;
  letter-spacing: -0.5px;
}
.v2-step2-sub {
  font-size: 13.5px;
  color: #64748b;
  margin: 0;
}

.v2-comp-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 20px;
  margin-bottom: 24px;
}

.v2-comp-card {
  background: #ffffff;
  border: 1.5px solid #e2e8f0;
  border-radius: 16px;
  padding: 18px;
  cursor: pointer;
  transition: all 0.2s ease;
  position: relative;
  display: flex;
  flex-direction: column;
}
.v2-comp-card:hover {
  border-color: #cbd5e1;
  box-shadow: 0 6px 20px rgba(0,0,0,0.04);
}
.v2-comp-card.selected {
  border: 2px solid #1d61f2;
  background: #ffffff;
  box-shadow: 0 8px 24px rgba(29,97,242,0.08);
}

.v2-comp-check {
  position: absolute;
  top: 14px;
  right: 14px;
  width: 24px;
  height: 24px;
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

.v2-card-img-wrap {
  border-radius: 12px;
  overflow: hidden;
  margin-bottom: 16px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.v2-card-head {
  display: flex;
  align-items: flex-start;
  gap: 10px;
  margin-bottom: 8px;
}
.v2-type-icon-badge {
  width: 34px;
  height: 34px;
  border-radius: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}
.v2-card-title {
  font-size: 16px;
  font-weight: 800;
  color: #0f172a;
  line-height: 1.25;
}
.v2-card-desc {
  font-size: 12.5px;
  color: #64748b;
  line-height: 1.45;
  margin-bottom: 14px;
}

.v2-card-hr {
  border: 0;
  border-top: 1px solid #f1f5f9;
  margin: 14px 0;
}

.v2-comp-list {
  list-style: none;
  padding: 0;
  margin: 0 0 18px;
  display: flex;
  flex-direction: column;
  gap: 9px;
}
.v2-comp-list li {
  display: flex;
  align-items: center;
  gap: 10px;
  font-size: 12.5px;
  color: #334155;
  font-weight: 500;
}
.v2-chk-icon {
  width: 18px;
  height: 18px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

.v2-comp-banner {
  border-radius: 10px;
  padding: 10px 12px;
  font-size: 11.5px;
  line-height: 1.4;
  margin-top: auto;
  display: flex;
  align-items: flex-start;
  gap: 8px;
}

/* Why selection matters container - Redesigned & Responsive */
.v2-why-box {
  background: #f8fafc;
  border: 1px solid #e2e8f0;
  border-radius: 16px;
  padding: 20px 24px;
  margin-bottom: 24px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 24px;
  box-sizing: border-box;
  width: 100%;
}
.v2-why-left {
  flex-shrink: 0;
  width: 220px;
}
.v2-why-title {
  display: block;
  font-size: 14px;
  font-weight: 800;
  color: #0f172a;
  margin: 0 0 4px 0;
  line-height: 1.3;
}
.v2-why-sub {
  font-size: 12px;
  color: #64748b;
  margin: 0;
  line-height: 1.4;
  display: block;
}
.v2-why-items {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 12px 14px;
  flex: 1;
  min-width: 0;
}
.v2-why-item {
  display: flex;
  align-items: center;
  gap: 10px;
  background: #ffffff;
  border: 1.5px solid #e2e8f0;
  border-radius: 12px;
  padding: 10px 14px;
  box-shadow: 0 1px 3px rgba(0,0,0,0.02);
  min-width: 0;
}
.v2-why-icon {
  width: 32px;
  height: 32px;
  border-radius: 9px;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}
.v2-why-item-txt {
  font-size: 12.5px;
  font-weight: 700;
  color: #0f172a;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

@media (max-width: 1200px) {
  .v2-why-box {
    flex-direction: column;
    align-items: flex-start;
    gap: 16px;
  }
  .v2-why-left {
    width: 100%;
  }
  .v2-why-items {
    width: 100%;
    grid-template-columns: repeat(2, 1fr);
  }
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
  font-weight: 800;
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
  font-weight: 600;
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
  overflow: hidden;
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

/* Step 3 Safe Box */
.v2-safe-box {
  background: #eff6ff;
  border: 1px solid #dbeafe;
  border-radius: 12px;
  padding: 16px 20px;
  display: flex;
  align-items: center;
  gap: 14px;
  margin-bottom: 24px;
}
.v2-safe-icon {
  width: 38px;
  height: 38px;
  border-radius: 10px;
  background: #ffffff;
  color: #1d61f2;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
  box-shadow: 0 1px 3px rgba(0,0,0,0.05);
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
  font-weight: 800;
  color: #0f172a;
}
.v2-review-btn-edit {
  color: #16a34a;
  font-size: 13px;
  font-weight: 700;
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
  font-weight: 800;
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
  font-weight: 800;
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
  font-weight: 800;
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
  font-weight: 700;
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
  font-weight: 700;
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
  font-weight: 600;
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
  font-weight: 700;
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
    <a href="https://jero.works/" class="v2-logo">
      <svg width="36" height="36" viewBox="0 0 40 44" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path d="M20 2C9.5 2 1 10.5 1 21H39C39 10.5 30.5 2 20 2Z" fill="#111827"/>
        <path d="M1 21C1 21 7.5 17.5 20 21" stroke="rgba(255,255,255,0.35)" stroke-width="1.2"/>
        <path d="M39 21C39 21 32.5 17.5 20 21" stroke="rgba(255,255,255,0.35)" stroke-width="1.2"/>
        <line x1="20" y1="21" x2="20" y2="37" stroke="#111827" stroke-width="2.6" stroke-linecap="round"/>
        <path d="M20 37C20 40.31 17.31 43 14 43" stroke="#111827" stroke-width="2.6" stroke-linecap="round" fill="none"/>
      </svg>
      <div>
        <span class="v2-logo-text">jero.works</span>
        <span class="v2-logo-sub">AI Recruitment Platform</span>
      </div>
    </a>

    <!-- Sidebar Hero Header -->
    <div class="v2-aside-hero">
      <h2>Create Your Workspace</h2>
      <p>Set up your organization once and start hiring in minutes.</p>
    </div>

    <!-- Progress Card -->
    <div class="v2-prog-card">
      <div class="v2-prog-head">
        <span id="progress-step-text" style="color:#475569;">Step 1 of 4</span>
        <span id="progress-pct-text" style="color:#1d61f2; font-weight:700;">25% Complete</span>
      </div>
      <div class="v2-prog-bg">
        <div id="progress-bar-fill" class="v2-prog-fill" style="width: 25%;"></div>
      </div>
      <div class="d-flex align-items-center gap-2 text-muted fs-8">
        <svg width="14" height="14" viewBox="0 0 20 20" fill="none"><circle cx="10" cy="10" r="9" stroke="#64748b" stroke-width="1.6"/><path d="M10 5v5l3 3" stroke="#64748b" stroke-width="1.6" stroke-linecap="round"/></svg>
        <span id="progress-time-text">Estimated Time: 2 Minutes</span>
      </div>
    </div>

    <!-- Stepper Navigation Card -->
    <div class="v2-stepper-card stepper-nav">
      <!-- Step 1 -->
      <div class="v2-step-item stepper-item current" data-kt-stepper-element="nav">
        <div class="v2-step-badge">1</div>
        <div>
          <div class="v2-step-title">Registration Details</div>
          <div class="v2-step-sub">Set up your account</div>
        </div>
      </div>

      <!-- Step 2 -->
      <div class="v2-step-item stepper-item" data-kt-stepper-element="nav">
        <div class="v2-step-badge">2</div>
        <div>
          <div class="v2-step-title">Company Details</div>
          <div class="v2-step-sub">Tell us about your company</div>
        </div>
      </div>

      <!-- Step 3 -->
      <div class="v2-step-item stepper-item" data-kt-stepper-element="nav">
        <div class="v2-step-badge">3</div>
        <div>
          <div class="v2-step-title">Account Details</div>
          <div class="v2-step-sub">Set up your workspace</div>
        </div>
      </div>

      <!-- Step 4 -->
      <div class="v2-step-item stepper-item" data-kt-stepper-element="nav">
        <div class="v2-step-badge">4</div>
        <div>
          <div class="v2-step-title">Review &amp; Launch</div>
          <div class="v2-step-sub">Review and finish</div>
        </div>
      </div>
    </div>

    <!-- Value Props Card -->
    <div class="v2-value-card">
      <div class="v2-value-title">Why complete your profile?</div>

      <div class="v2-value-row">
        <div class="v2-value-icon" style="background:#f0fdf4; color:#16a34a;">
          <svg width="18" height="18" viewBox="0 0 20 20" fill="none"><path d="M10 2L3 6v5.5C3 15.25 6.8 19.6 10 21c3.2-1.4 7-5.75 7-11.5V6L10 2Z" fill="#dcfce7" stroke="#16a34a" stroke-width="1.6"/><path d="M7 10.5l2 2 4-4" stroke="#16a34a" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
        </div>
        <div class="v2-value-txt">
          <strong>Get Verified Company Badge</strong>
          <span>Build trust with clients and candidates</span>
        </div>
      </div>

      <div class="v2-value-row">
        <div class="v2-value-icon" style="background:#f5f3ff; color:#7c3aed;">
          <svg width="18" height="18" viewBox="0 0 20 20" fill="none"><rect x="2" y="12" width="4" height="6" rx="1" fill="#ddd6fe" stroke="#7c3aed" stroke-width="1.4"/><rect x="8" y="8" width="4" height="10" rx="1" fill="#ddd6fe" stroke="#7c3aed" stroke-width="1.4"/><rect x="14" y="4" width="4" height="14" rx="1" fill="#ddd6fe" stroke="#7c3aed" stroke-width="1.4"/><path d="M3 9l5-4 4 3 5-6" stroke="#7c3aed" stroke-width="1.6" stroke-linecap="round"/></svg>
        </div>
        <div class="v2-value-txt">
          <strong>Better Marketplace Ranking</strong>
          <span>Increase your visibility and opportunities</span>
        </div>
      </div>

      <div class="v2-value-row">
        <div class="v2-value-icon" style="background:#eff6ff; color:#1d61f2;">
          <svg width="18" height="18" viewBox="0 0 20 20" fill="none"><path d="M10 2L12.5 7.5L18 10L12.5 12.5L10 18L7.5 12.5L2 10L7.5 7.5L10 2Z" fill="#bfdbfe" stroke="#1d61f2" stroke-width="1.5"/></svg>
        </div>
        <div class="v2-value-txt">
          <strong>Smart AI Recommendations</strong>
          <span>Get better matches and suggestions</span>
        </div>
      </div>

      <div class="v2-value-row">
        <div class="v2-value-icon" style="background:#fff7ed; color:#ea580c;">
          <svg width="18" height="18" viewBox="0 0 20 20" fill="none"><circle cx="7" cy="7" r="3" fill="#ffedd5" stroke="#ea580c" stroke-width="1.4"/><circle cx="14" cy="7" r="3" fill="#ffedd5" stroke="#ea580c" stroke-width="1.4"/><path d="M2 17c0-2.8 2.2-5 5-5h6c2.8 0 5 2.2 5 5" stroke="#ea580c" stroke-width="1.4" stroke-linecap="round"/></svg>
        </div>
        <div class="v2-value-txt">
          <strong>Attract Top Talent &amp; Agencies</strong>
          <span>Stand out and grow your network</span>
        </div>
      </div>
    </div>

    <!-- Security Card -->
    <div class="v2-sec-card">
      <svg width="20" height="20" viewBox="0 0 20 20" fill="none"><path d="M10 2L3 6v5.5C3 15.25 6.8 19.6 10 21c3.2-1.4 7-5.75 7-11.5V6L10 2Z" fill="#bfdbfe" stroke="#1d61f2" stroke-width="1.6"/><path d="M7 10.5l2 2 4-4" stroke="#1d61f2" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
      <div>
        <strong>Your information is secure</strong>
        <span>We never share your data with anyone.</span>
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

        <div style="font-size:16px; font-weight:800; color:#0f172a; margin-bottom:4px;">Registration Details</div>
        <div style="font-size:13px; color:#64748b; margin-bottom:24px;">Enter the basic information to create your account.</div>

        <!-- Company Name -->
        <div class="v2-fg fv-row">
          <label>Company Name <span class="req">*</span></label>
          <div class="v2-iw">
            <span class="v2-il">
              <svg width="18" height="18" viewBox="0 0 20 20" fill="none"><rect x="3" y="3" width="14" height="15" rx="2" stroke="#94a3b8" stroke-width="1.6"/><path d="M7 7h2M11 7h2M7 11h2M11 11h2M9 18v-3h2v3" stroke="#94a3b8" stroke-width="1.6" stroke-linecap="round"/></svg>
            </span>
            <input type="text" class="v2-inp" name="company_name" placeholder="Enter your company or organization name" value="" />
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
            <input type="text" class="v2-inp" name="name" placeholder="Enter your full name" autocomplete="off" />
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
            <input type="password" class="v2-inp" style="padding-right:46px;" name="password" id="password" placeholder="Create a strong password" autocomplete="off" />
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
            <input type="password" class="v2-inp" style="padding-right:46px;" name="password_confirmation" id="password_confirmation" placeholder="Re-enter your password" autocomplete="off" />
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
        <div class="d-flex align-items-start justify-content-between mb-4">
          <div>
            <h1 class="v2-step2-title">How does your organization operate?</h1>
            <p class="v2-step2-sub">Choose the option that best describes how your organization works. You can customize more later.</p>
          </div>
          <div class="d-flex align-items-center gap-2 fs-7 text-muted">
            <svg width="18" height="18" viewBox="0 0 20 20" fill="none"><circle cx="10" cy="10" r="8" stroke="#64748b" stroke-width="1.6"/><path d="M7.5 7.5a2.5 2.5 0 0 1 5 0c0 1.5-1.5 2-2.5 2.5V11" stroke="#64748b" stroke-width="1.6" stroke-linecap="round"/><circle cx="10" cy="14" r="0.75" fill="#64748b"/></svg>
            <span>Need help choosing?</span>
            <a href="https://jero.works/support-policy" target="_blank" class="fw-bold text-primary text-decoration-none ms-1">View Help Center &rarr;</a>
          </div>
        </div>

        <div class="v2-comp-grid fv-row">
          <!-- Card 1: Product & Manufacturing -->
          <div class="v2-comp-card selected" onclick="selectCompanyType(3, this)">
            <input type="radio" class="btn-check" name="company_type" value="3" checked />
            <div class="v2-comp-check">
              <svg width="12" height="12" viewBox="0 0 20 20" fill="none"><path d="M4 10l4 4 8-8" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </div>

            <!-- Top Vector Illustration -->
            <div class="v2-card-img-wrap" style="background:#eef6ff;">
              <svg width="100%" height="110" viewBox="0 0 260 110" fill="none" xmlns="http://www.w3.org/2000/svg">
                <rect width="260" height="110" fill="#f0f7ff"/>
                <path d="M10 95 C 30 80, 70 80, 90 95" stroke="#86efac" stroke-width="6" fill="none"/>
                <circle cx="20" cy="85" r="12" fill="#4ade80"/>
                <circle cx="240" cy="88" r="10" fill="#4ade80"/>
                
                <rect x="35" y="45" width="45" height="55" rx="3" fill="#60a5fa"/>
                <rect x="42" y="52" width="8" height="8" rx="1.5" fill="#ffffff"/>
                <rect x="54" y="52" width="8" height="8" rx="1.5" fill="#ffffff"/>
                <rect x="66" y="52" width="8" height="8" rx="1.5" fill="#ffffff"/>
                <rect x="42" y="66" width="8" height="8" rx="1.5" fill="#ffffff"/>
                <rect x="54" y="66" width="8" height="8" rx="1.5" fill="#ffffff"/>
                <rect x="66" y="66" width="8" height="8" rx="1.5" fill="#ffffff"/>

                <rect x="85" y="25" width="85" height="75" rx="4" fill="#2563eb"/>
                <rect x="95" y="35" width="12" height="12" rx="2" fill="#93c5fd"/>
                <rect x="113" y="35" width="12" height="12" rx="2" fill="#93c5fd"/>
                <rect x="131" y="35" width="12" height="12" rx="2" fill="#93c5fd"/>
                <rect x="149" y="35" width="12" height="12" rx="2" fill="#93c5fd"/>
                <rect x="95" y="53" width="12" height="12" rx="2" fill="#93c5fd"/>
                <rect x="113" y="53" width="12" height="12" rx="2" fill="#93c5fd"/>
                <rect x="131" y="53" width="12" height="12" rx="2" fill="#93c5fd"/>
                <rect x="149" y="53" width="12" height="12" rx="2" fill="#93c5fd"/>

                <rect x="175" y="40" width="55" height="60" rx="3" fill="#3b82f6"/>
                <rect x="183" y="48" width="10" height="10" rx="1.5" fill="#ffffff"/>
                <rect x="198" y="48" width="10" height="10" rx="1.5" fill="#ffffff"/>
                <rect x="213" y="48" width="10" height="10" rx="1.5" fill="#ffffff"/>

                <rect x="0" y="98" width="260" height="12" fill="#94a3b8"/>
                <rect x="120" y="88" width="28" height="12" rx="2" fill="#38bdf8"/>
                <circle cx="126" cy="100" r="3.5" fill="#1e293b"/>
                <circle cx="140" cy="100" r="3.5" fill="#1e293b"/>
              </svg>
            </div>

            <!-- Icon Badge + Title -->
            <div class="v2-card-head">
              <div class="v2-type-icon-badge" style="background:#eff6ff; color:#2563eb;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none"><path d="M3 21h18M5 21V7l8-4v18M13 21V11l6 3v7" stroke="#2563eb" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
              </div>
              <strong class="v2-card-title">Product &amp; Manufacturing Company</strong>
            </div>
            <p class="v2-card-desc">You build, manufacture or sell your own products and hire employees directly for your organization.</p>

            <hr class="v2-card-hr" />

            <ul class="v2-comp-list">
              <li><div class="v2-chk-icon" style="background:#eff6ff; color:#2563eb;"><svg width="12" height="12" viewBox="0 0 20 20" fill="none"><path d="M4 10l4 4 8-8" stroke="#2563eb" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg></div> Internal hiring &amp; recruitment</li>
              <li><div class="v2-chk-icon" style="background:#eff6ff; color:#2563eb;"><svg width="12" height="12" viewBox="0 0 20 20" fill="none"><path d="M4 10l4 4 8-8" stroke="#2563eb" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg></div> Employee &amp; team management</li>
              <li><div class="v2-chk-icon" style="background:#eff6ff; color:#2563eb;"><svg width="12" height="12" viewBox="0 0 20 20" fill="none"><path d="M4 10l4 4 8-8" stroke="#2563eb" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg></div> Agency collaboration</li>
              <li><div class="v2-chk-icon" style="background:#eff6ff; color:#2563eb;"><svg width="12" height="12" viewBox="0 0 20 20" fill="none"><path d="M4 10l4 4 8-8" stroke="#2563eb" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg></div> Campus &amp; bulk hiring</li>
              <li><div class="v2-chk-icon" style="background:#eff6ff; color:#2563eb;"><svg width="12" height="12" viewBox="0 0 20 20" fill="none"><path d="M4 10l4 4 8-8" stroke="#2563eb" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg></div> Vendor management</li>
            </ul>

            <div class="v2-comp-banner" style="background:#eff6ff; color:#1e40af;">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" style="flex-shrink:0;"><path d="M12 2a7 7 0 0 1 7 7c0 2.38-1.19 4.47-3 5.74V17a1 1 0 0 1-1 1h-6a1 1 0 0 1-1-1v-2.26C6.19 13.47 5 11.38 5 9a7 7 0 0 1 7-7Z" stroke="#2563eb" stroke-width="2"/><path d="M9 21h6" stroke="#2563eb" stroke-width="2" stroke-linecap="round"/></svg>
              <span>Best for organizations that don't serve external clients and focus on their own products or operations.</span>
            </div>
          </div>

          <!-- Card 2: Enterprise Services & Consulting -->
          <div class="v2-comp-card" onclick="selectCompanyType(2, this)">
            <input type="radio" class="btn-check" name="company_type" value="2" />
            <div class="v2-comp-check">
              <svg width="12" height="12" viewBox="0 0 20 20" fill="none"><path d="M4 10l4 4 8-8" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </div>

            <!-- Top Vector Illustration -->
            <div class="v2-card-img-wrap" style="background:#faf5ff;">
              <svg width="100%" height="110" viewBox="0 0 260 110" fill="none" xmlns="http://www.w3.org/2000/svg">
                <rect width="260" height="110" fill="#faf5ff"/>
                <rect x="110" y="15" width="70" height="45" rx="4" fill="#ffffff" stroke="#e9d5ff" stroke-width="1.5"/>
                <path d="M120 45 L135 32 L150 38 L165 24" stroke="#2563eb" stroke-width="2.5" stroke-linecap="round"/>
                <circle cx="165" cy="24" r="3" fill="#2563eb"/>
                <rect x="120" y="22" width="10" height="10" rx="5" fill="#60a5fa"/>

                <circle cx="70" cy="38" r="14" fill="#fdba74"/>
                <path d="M58 30 C 58 20, 82 20, 82 30" fill="#1e293b"/>
                <path d="M52 75 C 52 55, 88 55, 88 75" fill="#1d4ed8"/>

                <rect x="40" y="70" width="180" height="6" fill="#cbd5e1"/>
                <rect x="120" y="55" width="40" height="16" rx="2" fill="#334155"/>

                <circle cx="190" cy="38" r="14" fill="#fdba74"/>
                <path d="M175 32 C 175 18, 205 18, 205 32 L 205 45" fill="#0f172a"/>
                <path d="M172 75 C 172 55, 208 55, 208 75" fill="#16a34a"/>
              </svg>
            </div>

            <!-- Icon Badge + Title -->
            <div class="v2-card-head">
              <div class="v2-type-icon-badge" style="background:#f5f3ff; color:#7c3aed;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none"><rect x="3" y="7" width="18" height="13" rx="2" stroke="#7c3aed" stroke-width="2"/><path d="M8 7V5a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2" stroke="#7c3aed" stroke-width="2"/></svg>
              </div>
              <strong class="v2-card-title">Enterprise Services &amp; Consulting Company</strong>
            </div>
            <p class="v2-card-desc">You deliver professional or technology services to clients and manage consultants, projects and billable resources.</p>

            <hr class="v2-card-hr" />

            <ul class="v2-comp-list">
              <li><div class="v2-chk-icon" style="background:#f5f3ff; color:#7c3aed;"><svg width="12" height="12" viewBox="0 0 20 20" fill="none"><path d="M4 10l4 4 8-8" stroke="#7c3aed" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg></div> Client projects &amp; engagements</li>
              <li><div class="v2-chk-icon" style="background:#f5f3ff; color:#7c3aed;"><svg width="12" height="12" viewBox="0 0 20 20" fill="none"><path d="M4 10l4 4 8-8" stroke="#7c3aed" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg></div> Consultant &amp; resource management</li>
              <li><div class="v2-chk-icon" style="background:#f5f3ff; color:#7c3aed;"><svg width="12" height="12" viewBox="0 0 20 20" fill="none"><path d="M4 10l4 4 8-8" stroke="#7c3aed" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg></div> Contract &amp; project-based hiring</li>
              <li><div class="v2-chk-icon" style="background:#f5f3ff; color:#7c3aed;"><svg width="12" height="12" viewBox="0 0 20 20" fill="none"><path d="M4 10l4 4 8-8" stroke="#7c3aed" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg></div> Timesheets &amp; utilization tracking</li>
              <li><div class="v2-chk-icon" style="background:#f5f3ff; color:#7c3aed;"><svg width="12" height="12" viewBox="0 0 20 20" fill="none"><path d="M4 10l4 4 8-8" stroke="#7c3aed" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg></div> Billing &amp; resource allocation</li>
            </ul>

            <div class="v2-comp-banner" style="background:#f5f3ff; color:#5b21b6;">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" style="flex-shrink:0;"><path d="M12 2a7 7 0 0 1 7 7c0 2.38-1.19 4.47-3 5.74V17a1 1 0 0 1-1 1h-6a1 1 0 0 1-1-1v-2.26C6.19 13.47 5 11.38 5 9a7 7 0 0 1 7-7Z" stroke="#7c3aed" stroke-width="2"/><path d="M9 21h6" stroke="#7c3aed" stroke-width="2" stroke-linecap="round"/></svg>
              <span>Best for service-based companies that work with external clients.</span>
            </div>
          </div>

          <!-- Card 3: Recruitment & Staffing Agency -->
          <div class="v2-comp-card" onclick="selectCompanyType(1, this)">
            <input type="radio" class="btn-check" name="company_type" value="1" />
            <div class="v2-comp-check">
              <svg width="12" height="12" viewBox="0 0 20 20" fill="none"><path d="M4 10l4 4 8-8" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </div>

            <!-- Top Vector Illustration -->
            <div class="v2-card-img-wrap" style="background:#fff7ed;">
              <svg width="100%" height="110" viewBox="0 0 260 110" fill="none" xmlns="http://www.w3.org/2000/svg">
                <rect width="260" height="110" fill="#fff7ed"/>
                <rect x="140" y="15" width="90" height="60" rx="4" fill="#ffffff" stroke="#fed7aa" stroke-width="1.5"/>
                <rect x="150" y="24" width="16" height="16" rx="8" fill="#16a34a"/>
                <rect x="172" y="26" width="30" height="5" rx="2" fill="#cbd5e1"/>
                <rect x="150" y="46" width="16" height="16" rx="8" fill="#2563eb"/>
                <rect x="172" y="48" width="40" height="5" rx="2" fill="#cbd5e1"/>

                <circle cx="210" cy="45" r="14" fill="#ffffff" stroke="#1e293b" stroke-width="3"/>
                <line x1="220" y1="55" x2="235" y2="70" stroke="#1e293b" stroke-width="4" stroke-linecap="round"/>

                <circle cx="70" cy="38" r="14" fill="#fdba74"/>
                <path d="M55 30 C 55 16, 85 16, 85 30 L 85 45" fill="#451a03"/>
                <path d="M52 75 C 52 55, 88 55, 88 75" fill="#ea580c"/>
                <rect x="75" y="50" width="22" height="26" rx="2" fill="#334155"/>
              </svg>
            </div>

            <!-- Icon Badge + Title -->
            <div class="v2-card-head">
              <div class="v2-type-icon-badge" style="background:#fff7ed; color:#ea580c;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" stroke="#ea580c" stroke-width="2"/><circle cx="9" cy="7" r="4" stroke="#ea580c" stroke-width="2"/><path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75" stroke="#ea580c" stroke-width="2"/></svg>
              </div>
              <strong class="v2-card-title">Recruitment &amp; Staffing Agency</strong>
            </div>
            <p class="v2-card-desc">You source, recruit and submit candidates to client organizations.</p>

            <hr class="v2-card-hr" />

            <ul class="v2-comp-list">
              <li><div class="v2-chk-icon" style="background:#fff7ed; color:#ea580c;"><svg width="12" height="12" viewBox="0 0 20 20" fill="none"><path d="M4 10l4 4 8-8" stroke="#ea580c" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg></div> Bench talent marketplace</li>
              <li><div class="v2-chk-icon" style="background:#fff7ed; color:#ea580c;"><svg width="12" height="12" viewBox="0 0 20 20" fill="none"><path d="M4 10l4 4 8-8" stroke="#ea580c" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg></div> Candidate submissions</li>
              <li><div class="v2-chk-icon" style="background:#fff7ed; color:#ea580c;"><svg width="12" height="12" viewBox="0 0 20 20" fill="none"><path d="M4 10l4 4 8-8" stroke="#ea580c" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg></div> Client &amp; requirement management</li>
              <li><div class="v2-chk-icon" style="background:#fff7ed; color:#ea580c;"><svg width="12" height="12" viewBox="0 0 20 20" fill="none"><path d="M4 10l4 4 8-8" stroke="#ea580c" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg></div> Vendor marketplace</li>
              <li><div class="v2-chk-icon" style="background:#fff7ed; color:#ea580c;"><svg width="12" height="12" viewBox="0 0 20 20" fill="none"><path d="M4 10l4 4 8-8" stroke="#ea580c" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg></div> Agency performance analytics</li>
            </ul>

            <div class="v2-comp-banner" style="background:#fff7ed; color:#9a3412;">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" style="flex-shrink:0;"><path d="M12 2a7 7 0 0 1 7 7c0 2.38-1.19 4.47-3 5.74V17a1 1 0 0 1-1 1h-6a1 1 0 0 1-1-1v-2.26C6.19 13.47 5 11.38 5 9a7 7 0 0 1 7-7Z" stroke="#ea580c" stroke-width="2"/><path d="M9 21h6" stroke="#ea580c" stroke-width="2" stroke-linecap="round"/></svg>
              <span>Best for agencies that connect candidates with hiring companies.</span>
            </div>
          </div>
        </div>

        <!-- Bottom "Why selection matters" Box - Redesigned & Responsive -->
        <div class="v2-why-box">
          <div class="v2-why-left">
            <strong class="v2-why-title">Why your selection matters</strong>
            <span class="v2-why-sub">We'll personalize your workspace based on your choice.</span>
          </div>

          <div class="v2-why-items">
            <div class="v2-why-item">
              <div class="v2-why-icon" style="background:#eff6ff; color:#2563eb;">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none"><rect x="3" y="3" width="7" height="7" rx="1.5" stroke="#2563eb" stroke-width="2"/><rect x="14" y="3" width="7" height="7" rx="1.5" stroke="#2563eb" stroke-width="2"/><rect x="14" y="14" width="7" height="7" rx="1.5" stroke="#2563eb" stroke-width="2"/><rect x="3" y="14" width="7" height="7" rx="1.5" stroke="#2563eb" stroke-width="2"/></svg>
              </div>
              <strong class="v2-why-item-txt">Personalized Dashboard</strong>
            </div>

            <div class="v2-why-item">
              <div class="v2-why-icon" style="background:#f5f3ff; color:#7c3aed;">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="3" stroke="#7c3aed" stroke-width="2"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1 0-2.83 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1Z" stroke="#7c3aed" stroke-width="2"/></svg>
              </div>
              <strong class="v2-why-item-txt">Relevant Features</strong>
            </div>

            <div class="v2-why-item">
              <div class="v2-why-icon" style="background:#f0fdf4; color:#16a34a;">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10Z" stroke="#16a34a" stroke-width="2"/><path d="M9 12l2 2 4-4" stroke="#16a34a" stroke-width="2" stroke-linecap="round"/></svg>
              </div>
              <strong class="v2-why-item-txt">Smart Permissions</strong>
            </div>

            <div class="v2-why-item">
              <div class="v2-why-icon" style="background:#fff7ed; color:#ea580c;">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none"><path d="M12 2l2.4 7.4H22l-6 4.6 2.3 7.4-6.3-4.6-6.3 4.6 2.3-7.4-6-4.6h7.6L12 2Z" stroke="#ea580c" stroke-width="2"/></svg>
              </div>
              <strong class="v2-why-item-txt">Better AI Recommendations</strong>
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
              Continue to Workspace Setup
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
          <div class="border rounded-3 px-3 py-2 bg-white d-flex align-items-center gap-2 fs-7">
            <svg width="18" height="18" viewBox="0 0 20 20" fill="none"><circle cx="10" cy="10" r="8" stroke="#64748b" stroke-width="1.6"/><path d="M7.5 7.5a2.5 2.5 0 0 1 5 0c0 1.5-1.5 2-2.5 2.5V11" stroke="#64748b" stroke-width="1.6" stroke-linecap="round"/><circle cx="10" cy="14" r="0.75" fill="#64748b"/></svg>
            <strong class="text-dark">Need assistance?</strong>
            <span class="text-muted">|</span>
            <a href="https://jero.works/support-policy" target="_blank" class="text-secondary text-decoration-none">Live Chat</a>
            <span class="text-muted">|</span>
            <a href="https://jero.works/support-policy" target="_blank" class="fw-bold text-primary text-decoration-none">Help Center &rarr;</a>
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
            <input type="text" name="website" id="website_field" class="v2-web-input" placeholder="www.yourcompany.com" value="" />
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
            <input name="contact_number" id="contact_number" type="tel" class="v2-inp" placeholder="Enter 10 digit mobile number" value="" maxlength="10" inputmode="numeric" pattern="[0-9]{10}" autocomplete="tel-national" required />
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

        <!-- Safety Info Banner -->
        <div class="v2-safe-box">
          <div class="v2-safe-icon">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none"><rect x="4" y="10" width="16" height="11" rx="2" stroke="#1d61f2" stroke-width="2"/><path d="M7 10V7a5 5 0 0 1 10 0v3" stroke="#1d61f2" stroke-width="2"/></svg>
          </div>
          <div>
            <strong class="text-dark fs-7 d-block">Your information is safe with us.</strong>
            <span class="fs-7 text-muted">We never share your contact details with anyone without your permission.</span>
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

    var iti__active = $(".iti__active").attr('data-country-code') || "in"; 
    $('#contact_code').val(iti__active);

    $('#preview_email').text(email);
    $('#preview_name').text(name);
    $('#preview_company_name').text(company_name);
    $('#preview_company_type').text(company_type);	
    $('#preview_website').text(website);
    $('#preview_contact_number').text(contact_number);
    $('#preview_city').text(city || 'N/A');

    const flagContainer = document.getElementById('phoneDisplay');
    if (flagContainer && iti__active) {
      $('#phoneDisplay').html('');
      const flagDiv = document.createElement("div");
      flagDiv.className = `iti__flag iti__${iti__active}`;
      const dialCode = (typeof countryDialCodes !== 'undefined' ? countryDialCodes[iti__active] : '') || "";
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
    updatePreviewData();
  });

  tagifyInstance.on('change', function(e) {
    if (window.formValidators && window.formValidators[2]) {
      window.formValidators[2].revalidateField('kt_tagify_work_location');
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

// Phone Input
const phoneInputField = document.querySelector("#contact_number");
const phoneInput = window.intlTelInput(phoneInputField, { 
  separateDialCode: true,
  dropdown: true,
  initialCountry: "in",
  preferredCountries: ["in", "us", "gb", "ca", "au"],
  autoPlaceholder: "off"
});

$(document).ready(function() {
  $('.smsForm .iti__flag-container').click(function() { 
    var countryCode = $('.smsForm .iti__selected-flag').attr('title');
    if (countryCode) {
      var dialCode = countryCode.replace(/[^0-9]/g,'');
      $('#contact_code').val(dialCode);
    }
  });

  $(".iti__flag-container").on("click", function (event) {
    var iti__active = $(".iti__active").attr('data-country-code'); 
    $('#contact_code').val(iti__active);
  });
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

      if (stepIndex > 1) {
        $('#top-bar-text').text('Need assistance?');
        $('#top-bar-btn').attr('href', 'https://jero.works/support-policy').text('Help Center');
      } else {
        $('#top-bar-text').text('Already have an account?');
        $('#top-bar-btn').attr('href', "{{ route('login') }}").text('Sign In');
      }
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
                    stringLength: { min: 10, max: 10, message: "Contact Number must contain 10 digits only" }
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
            if(contact_number != '' && contact_number.length === 10) {
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
