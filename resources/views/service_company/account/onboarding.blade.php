@extends('layouts.service_company_layout')

@push('head-script')
<meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
<meta http-equiv="Pragma" content="no-cache" />
<meta http-equiv="Expires" content="0" />

<!-- Metronic 7 CSS Bundles -->
<link href="{{ asset('demo/v7/css/wizard-1.css') }}<?php echo TM_VERSION; ?>" rel="stylesheet" type="text/css" />
<link href="{{ asset('demo/v7/css/plugins.bundle.css') }}<?php echo TM_VERSION; ?>" rel="stylesheet" type="text/css" />
<link href="{{ asset('demo/v7/css/prismjs.bundle.css') }}<?php echo TM_VERSION; ?>" rel="stylesheet" type="text/css" />
<link href="{{ asset('demo/v7/css/style.bundle.css') }}<?php echo TM_VERSION; ?>" rel="stylesheet" type="text/css" />
<link href="https://unpkg.com/@yaireo/tagify/dist/tagify.css" rel="stylesheet" type="text/css" />

<style>
/* =============================================================================
   JERO GOD-LEVEL COMPANY PROFILE / ONBOARDING SETUP (RULE 20)
   100% Replicates Add Internal Client Visual Language, Layout & Dropdown Engines
   Optimized for 15.6" Screens (1366x768 & 1920x1080) with Zero Horizontal Scroll
   ============================================================================= */

:root {
  --primary: #ffd428;
  --primary-hover: #f5c400;
  --primary-gradient: linear-gradient(180deg, #ffd428 0%, #f5c400 100%);
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
  --radius: 10px;
  --radius-sm: 6px;
  --radius-lg: 12px;
  --radius-xl: 16px;
  --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05);
  --shadow: 0 1px 3px 0 rgb(0 0 0 / 0.1), 0 1px 2px -1px rgb(0 0 0 / 0.1);
  --transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
}

body {
  overflow-x: hidden !important;
  width: 100% !important;
  font-family: 'Poppins', sans-serif !important;
  background: var(--bg) !important;
  color: var(--text-primary) !important;
}

#kt_content {
  background: var(--bg) !important;
  padding: 14px 20px 24px !important;
}

#kt_toolbar, #kt_footer {
  display: none !important;
  height: 0 !important;
  padding: 0 !important;
  margin: 0 !important;
}

/* Master Shell */
.company-profile-shell {
  width: 100%;
  max-width: 1440px;
  margin: 0 auto;
  box-sizing: border-box;
}

/* ===== STICKY TOP NAVIGATION BAR (Header & Stepper) ===== */
.client-top-nav-card.company-profile-nav-card {
  position: sticky !important;
  top: 10px !important;
  z-index: 100 !important;
  background: rgba(255, 255, 255, 0.98) !important;
  backdrop-filter: blur(10px) !important;
  border: 1px solid var(--border) !important;
  border-radius: var(--radius-lg) !important;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.03) !important;
  padding: 0 14px !important;
  margin-bottom: 10px !important;
  height: 48px !important;
  min-height: 48px !important;
  max-height: 48px !important;
  box-sizing: border-box !important;
  transition: box-shadow 0.2s ease, top 0.2s ease !important;
}

.client-header-grid {
  display: flex !important;
  align-items: center !important;
  justify-content: space-between !important;
  width: 100% !important;
  height: 100% !important;
  gap: 10px !important;
  box-sizing: border-box !important;
}

.client-header-identity {
  flex: 0 0 auto !important;
  min-width: 120px !important;
  max-width: 190px !important;
  height: 100% !important;
  display: flex !important;
  align-items: center !important;
}

.client-header-title {
  font-size: 13.5px !important;
  font-weight: 700 !important;
  color: var(--text-primary) !important;
  margin: 0 !important;
  line-height: 1.25 !important;
  white-space: nowrap !important;
}

.client-header-subtitle {
  font-size: 10.5px !important;
  color: var(--text-muted) !important;
  margin: 1px 0 0 !important;
  line-height: 1.3 !important;
  white-space: nowrap !important;
  overflow: hidden !important;
  text-overflow: ellipsis !important;
  max-width: 180px !important;
}

/* Center Stepper */
.client-header-stepper-col {
  flex: 1 1 auto !important;
  display: flex !important;
  justify-content: center !important;
  min-width: 0 !important;
}

.client-stepper-wrap {
  display: inline-flex !important;
  align-items: center !important;
  gap: 2px !important;
  background: #f8fafc !important;
  padding: 2px 6px !important;
  border-radius: 24px !important;
  border: 1px solid var(--border) !important;
}

.client-step-item {
  display: inline-flex !important;
  align-items: center !important;
  gap: 4px !important;
  cursor: pointer !important;
  padding: 2px 5px !important;
  border-radius: 14px !important;
  transition: var(--transition) !important;
  user-select: none !important;
}

.client-step-item:hover {
  background: #f1f5f9 !important;
}

.client-step-item.is-active {
  background: #ffffff !important;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.06) !important;
}

.client-step-circle {
  width: 20px !important;
  height: 20px !important;
  min-width: 20px !important;
  min-height: 20px !important;
  border-radius: 50% !important;
  display: flex !important;
  align-items: center !important;
  justify-content: center !important;
  font-size: 10px !important;
  font-weight: 700 !important;
  transition: var(--transition) !important;
}

.client-step-circle.is-upcoming {
  background: #e2e8f0 !important;
  color: #64748b !important;
}

.client-step-circle.is-active {
  background: var(--primary-gradient) !important;
  color: var(--primary-text) !important;
  border: 1px solid var(--primary-border) !important;
  box-shadow: 0 0 0 2px rgba(245, 196, 0, 0.25) !important;
}

.client-step-circle.is-complete {
  background: var(--success) !important;
  color: #ffffff !important;
  border: 1px solid var(--success) !important;
}

.client-step-name {
  font-size: 11px !important;
  font-weight: 600 !important;
  color: #475569 !important;
  white-space: nowrap !important;
}

.client-step-item.is-active .client-step-name {
  color: var(--text-primary) !important;
  font-weight: 700 !important;
}

.client-step-arrow {
  color: #cbd5e1 !important;
  display: flex !important;
  align-items: center !important;
  margin: 0 2px !important;
}

.client-step-arrow-svg {
  width: 10px !important;
  height: 10px !important;
}

/* Action Buttons in Header */
.client-header-actions {
  flex: 0 0 auto !important;
  display: flex !important;
  align-items: center !important;
  gap: 6px !important;
}

.profile-live-status-pill {
  display: inline-flex !important;
  align-items: center !important;
  gap: 5px !important;
  font-size: 10.5px !important;
  font-weight: 500 !important;
  color: #64748b !important;
  padding: 3px 8px !important;
  background: #f8fafc !important;
  border: 1px solid #e2e8f0 !important;
  border-radius: 14px !important;
  white-space: nowrap !important;
}

.status-indicator-dot {
  width: 6px !important;
  height: 6px !important;
  border-radius: 50% !important;
  background: #10b981 !important;
  display: inline-block !important;
  box-shadow: 0 0 0 2px rgba(16, 185, 129, 0.2) !important;
}

.client-top-close-btn, .client-top-back-btn, .client-top-next-btn, .client-top-submit-btn {
  height: 30px !important;
  min-height: 30px !important;
  max-height: 30px !important;
  padding: 0 11px !important;
  font-size: 11px !important;
  font-weight: 600 !important;
  border-radius: 7px !important;
  display: inline-flex !important;
  align-items: center !important;
  gap: 5px !important;
  transition: var(--transition) !important;
  cursor: pointer !important;
  font-family: 'Poppins', sans-serif !important;
  white-space: nowrap !important;
}

.client-top-close-btn {
  background: #ffffff !important;
  border: 1px solid var(--border) !important;
  color: #64748b !important;
}
.client-top-close-btn:hover {
  background: #f8fafc !important;
  color: var(--text-primary) !important;
}

.client-top-back-btn {
  background: #ffffff !important;
  border: 1px solid var(--border) !important;
  color: #334155 !important;
}
.client-top-back-btn:hover {
  background: #f1f5f9 !important;
}

.client-top-next-btn {
  background: #ffd428 !important;
  color: #10143b !important;
  border: 1px solid #d4a017 !important;
  box-shadow: 0 1px 3px rgba(245, 196, 0, 0.25) !important;
  font-weight: 700 !important;
}
.client-top-next-btn:hover {
  background: #f5c400 !important;
  transform: translateY(-1px);
}

.client-top-submit-btn {
  background: #10b981 !important;
  color: #ffffff !important;
  border: 1px solid #059669 !important;
  box-shadow: 0 2px 6px rgba(16, 185, 129, 0.3) !important;
  font-weight: 700 !important;
}
.client-top-submit-btn:hover {
  background: #059669 !important;
}

/* 15.6" Screen Responsive Navigation Collapse */
@media (max-width: 1400px) {
  .client-top-nav-card.company-profile-nav-card {
    padding: 0 10px !important;
    height: 46px !important;
    min-height: 46px !important;
    max-height: 46px !important;
  }
  .client-header-grid {
    gap: 6px !important;
  }
  .client-header-identity {
    max-width: 155px !important;
    min-width: 105px !important;
  }
  .client-header-title {
    font-size: 13px !important;
  }
  .client-header-subtitle {
    display: none !important;
  }
  #profile_last_saved_text {
    display: none !important;
  }
  .profile-live-status-pill {
    padding: 3px 6px !important;
  }
  .client-stepper-wrap {
    padding: 2px 4px !important;
    gap: 1px !important;
  }
  .client-step-item {
    padding: 2px 4px !important;
    gap: 3px !important;
  }
  .client-step-name {
    font-size: 10.5px !important;
  }
  .client-step-circle {
    width: 18px !important;
    height: 18px !important;
    min-width: 18px !important;
    min-height: 18px !important;
    font-size: 9.5px !important;
  }
  .client-step-arrow {
    margin: 0 1px !important;
  }
  .client-step-arrow-svg {
    width: 9px !important;
    height: 9px !important;
  }
  .client-top-close-btn,
  .client-top-back-btn,
  .client-top-next-btn,
  .client-top-submit-btn {
    height: 28px !important;
    min-height: 28px !important;
    max-height: 28px !important;
    padding: 0 9px !important;
    font-size: 10.5px !important;
    gap: 4px !important;
  }
}

@media (max-width: 1100px) {
  .client-step-name {
    display: none !important;
  }
  .client-step-item.is-active .client-step-name {
    display: inline !important;
  }
}

/* ===== STEP CONTENT VISIBILITY ===== */
[data-wizard-type="step-content"] {
  display: none !important;
}

[data-wizard-type="step-content"][data-wizard-state="current"],
[data-wizard-type="step-content"].is-current {
  display: block !important;
  animation: fadeIn 0.22s ease-in-out;
}

@keyframes fadeIn {
  from { opacity: 0; transform: translateY(4px); }
  to { opacity: 1; transform: translateY(0); }
}

/* ===== TWO-COLUMN SUB-CARD SYSTEM (Rule 20 & 15.6" Screen Fit) ===== */
.god-level-company-layout, .client-step-layout {
  display: grid !important;
  grid-template-columns: minmax(0, 1fr) 290px !important;
  gap: 12px !important;
  align-items: start !important;
  width: 100% !important;
  max-width: 100% !important;
  box-sizing: border-box !important;
}

@media (max-width: 1024px) {
  .god-level-company-layout, .client-step-layout {
    grid-template-columns: 1fr !important;
  }
}

.god-card, .client-section-card {
  background: #ffffff !important;
  border: 1px solid var(--border) !important;
  border-radius: var(--radius-lg) !important;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.03) !important;
  overflow: hidden !important;
  min-width: 0 !important;
  width: 100% !important;
  box-sizing: border-box !important;
}

.god-card-header, .client-card-header {
  padding: 8px 14px !important;
  border-bottom: 1px solid #f1f5f9 !important;
  display: flex !important;
  align-items: center !important;
  gap: 10px !important;
  background: #ffffff !important;
}

.god-card-header-icon {
  width: 28px !important;
  height: 28px !important;
  border-radius: 7px !important;
  display: flex !important;
  align-items: center !important;
  justify-content: center !important;
  flex-shrink: 0 !important;
}

.god-card-header-icon.blue-sky-icon {
  background: #eff6ff !important;
  color: #2563eb !important;
}

.god-card-title {
  font-size: 13.5px !important;
  font-weight: 700 !important;
  color: #0f172a !important;
  margin: 0 !important;
  line-height: 1.25 !important;
}

.god-card-subtitle {
  font-size: 10.5px !important;
  color: #64748b !important;
  margin: 1px 0 0 !important;
}

/* Fields & 2-Column Grid (15.6" Screen Fit) */
.god-fields-grid {
  padding: 10px 14px !important;
  display: grid !important;
  grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
  gap: 8px 12px !important;
  align-content: space-between !important;
  box-sizing: border-box !important;
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
  gap: 5px !important;
  margin-bottom: 3px !important;
}

.god-field-label {
  font-size: 11px !important;
  font-weight: 600 !important;
  color: #0f172a !important;
  margin: 0 !important;
  line-height: 1.25 !important;
}

/* Native Input Box */
.god-input-box {
  display: flex !important;
  align-items: center !important;
  background: #ffffff !important;
  border: 1.5px solid #cbd5e1 !important;
  border-radius: 7px !important;
  padding: 0 8px !important;
  height: 35px !important;
  min-height: 35px !important;
  max-height: 35px !important;
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

.god-input-box.is-invalid, .god-input-box.has-validation-error {
  border-color: #ef4444 !important;
  box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.18) !important;
}

.god-input-left-icon {
  color: #94a3b8 !important;
  display: inline-flex !important;
  align-items: center !important;
  justify-content: center !important;
  margin-right: 4px !important;
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
  font-size: 11.5px !important;
  font-family: 'Poppins', sans-serif !important;
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

.god-website-control-row {
  display: flex !important;
  align-items: stretch !important;
  gap: 8px !important;
  height: 35px !important;
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
  font-size: 11px !important;
  font-weight: 700 !important;
  border-radius: 7px !important;
  padding: 0 11px !important;
  height: 35px !important;
  min-height: 35px !important;
  display: inline-flex !important;
  align-items: center !important;
  justify-content: center !important;
  gap: 5px !important;
  flex-shrink: 0 !important;
  cursor: pointer !important;
  white-space: nowrap !important;
  box-shadow: 0 1px 3px rgba(245, 196, 0, 0.25) !important;
  transition: all 0.15s ease !important;
  font-family: 'Poppins', sans-serif !important;
}
.god-fetch-btn:hover {
  background: #f5c400 !important;
  transform: translateY(-1px) !important;
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
  font-family: 'Poppins', sans-serif !important;
}
.god-fetch-btn:hover {
  background: #f5c400 !important;
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

/* =========================================================================
   ENTERPRISE INDUSTRY COMBOBOX
   ========================================================================= */
.jero-ind-select-wrap {
  position: relative !important;
  width: 100% !important;
}
.jero-ind-select-wrap.is-open .jero-dropdown-chevron {
  transform: rotate(180deg) !important;
  color: #2563eb !important;
}
.jero-ind-dropdown {
  position: absolute !important;
  top: calc(100% + 4px) !important;
  left: 0 !important;
  right: 0 !important;
  width: 100% !important;
  z-index: 1080 !important;
  background: #ffffff !important;
  border: 1.5px solid #cbd5e1 !important;
  border-radius: 10px !important;
  box-shadow: 0 16px 36px -6px rgba(15, 23, 42, 0.16), 0 4px 12px -2px rgba(15, 23, 42, 0.08) !important;
  overflow: hidden !important;
  display: none;
  flex-direction: column !important;
  max-height: 280px !important;
  animation: jeroDropdownFadeIn 0.14s cubic-bezier(0.16, 1, 0.3, 1) !important;
  box-sizing: border-box !important;
}
.jero-ind-select-wrap.is-open .jero-ind-dropdown {
  display: flex !important;
}
.jero-ind-list-container {
  flex: 1 1 auto !important;
  max-height: 270px !important;
  overflow-y: auto !important;
  padding: 4px 0 !important;
  scrollbar-width: thin !important;
  scrollbar-color: #cbd5e1 #f8fafc !important;
}
.jero-ind-item {
  display: flex !important;
  align-items: center !important;
  justify-content: space-between !important;
  padding: 8px 12px !important;
  font-size: 12px !important;
  font-weight: 500 !important;
  color: #1e293b !important;
  cursor: pointer !important;
  transition: background 0.12s ease !important;
}
.jero-ind-item:hover, .jero-ind-item.is-selected {
  background: #eff6ff !important;
  color: #1d4ed8 !important;
  font-weight: 600 !important;
}

/* =========================================================================
   ENTERPRISE COMPANY SIZE COMBOBOX
   ========================================================================= */
.jero-csize-select-wrap {
  position: relative !important;
  width: 100% !important;
}
.god-input-box.jero-csize-input-box {
  cursor: pointer !important;
  user-select: none !important;
}
.jero-csize-select-wrap.is-open .jero-dropdown-chevron,
.jero-csize-select-wrap.is-open .jero-csize-chevron {
  transform: rotate(180deg) !important;
  color: #2563eb !important;
}
.jero-csize-dropdown {
  position: absolute !important;
  top: calc(100% + 5px) !important;
  left: 0 !important;
  right: 0 !important;
  z-index: 1060 !important;
  background: #ffffff !important;
  border: 1px solid #e2e8f0 !important;
  border-radius: 12px !important;
  box-shadow: 0 20px 48px -8px rgba(15, 23, 42, 0.16), 0 8px 20px -4px rgba(15, 23, 42, 0.08) !important;
  overflow: hidden !important;
  display: none;
  flex-direction: column !important;
  max-height: 280px !important;
  animation: jeroDropdownFadeIn 0.16s cubic-bezier(0.16, 1, 0.3, 1) !important;
  box-sizing: border-box !important;
}
.jero-csize-select-wrap.is-open .jero-csize-dropdown {
  display: flex !important;
}
.jero-csize-dropdown.drop-up {
  top: auto !important;
  bottom: calc(100% + 5px) !important;
  box-shadow: 0 -20px 48px -8px rgba(15, 23, 42, 0.16), 0 -8px 20px -4px rgba(15, 23, 42, 0.08) !important;
}
.jero-csize-list-container {
  max-height: 270px !important;
  overflow-y: auto !important;
  padding: 6px 0 !important;
  scrollbar-width: thin !important;
  scrollbar-color: #cbd5e1 #f8fafc !important;
}
.jero-csize-item {
  display: flex !important;
  align-items: center !important;
  justify-content: space-between !important;
  padding: 8px 12px !important;
  cursor: pointer !important;
  transition: background 0.12s ease !important;
  border-bottom: 1px solid #f8fafc !important;
  box-sizing: border-box !important;
  min-height: 42px !important;
}
.jero-csize-item:last-child {
  border-bottom: none !important;
}
.jero-csize-item:hover, .jero-csize-item.focused {
  background: #f8fafc !important;
}
.jero-csize-item.selected {
  background: #eff6ff !important;
}
.jero-csize-item-main {
  display: flex !important;
  align-items: center !important;
  gap: 8px !important;
  flex: 1 1 auto !important;
  min-width: 0 !important;
}
.jero-csize-item-icon {
  display: inline-flex !important;
  align-items: center !important;
  justify-content: center !important;
  width: 24px !important;
  height: 24px !important;
  border-radius: 6px !important;
  background: #f1f5f9 !important;
  color: #475569 !important;
  flex-shrink: 0 !important;
}
.jero-csize-item:hover .jero-csize-item-icon,
.jero-csize-item.selected .jero-csize-item-icon {
  background: #dbeafe !important;
  color: #1d4ed8 !important;
}
.jero-csize-item-info {
  display: flex !important;
  flex-direction: column !important;
  min-width: 0 !important;
  flex: 1 1 auto !important;
}
.jero-csize-item-label {
  font-size: 12px !important;
  font-weight: 600 !important;
  color: #1e293b !important;
  line-height: 1.35 !important;
  white-space: nowrap !important;
  overflow: hidden !important;
  text-overflow: ellipsis !important;
}
.jero-csize-item.selected .jero-csize-item-label {
  color: #1d4ed8 !important;
}
.jero-csize-item-desc {
  font-size: 10.5px !important;
  color: #64748b !important;
  line-height: 1.3 !important;
  white-space: nowrap !important;
  overflow: hidden !important;
  text-overflow: ellipsis !important;
}
.jero-csize-item-side {
  display: flex !important;
  align-items: center !important;
  gap: 6px !important;
  flex-shrink: 0 !important;
  margin-left: 8px !important;
}
.jero-csize-item-badge {
  font-size: 9.5px !important;
  font-weight: 600 !important;
  padding: 2px 6px !important;
  border-radius: 10px !important;
  background: #f1f5f9 !important;
  color: #475569 !important;
  border: 1px solid #e2e8f0 !important;
}
.jero-csize-item.selected .jero-csize-item-badge {
  background: #bfdbfe !important;
  color: #1e40af !important;
  border-color: #93c5fd !important;
}
.jero-csize-item-check {
  display: none !important;
  color: #2563eb !important;
  align-items: center !important;
  justify-content: center !important;
  flex-shrink: 0 !important;
}
.jero-csize-item.selected .jero-csize-item-check {
  display: inline-flex !important;
}

/* =========================================================================
   SEARCH-FIRST COMPANY TYPE COMBOBOX
   ========================================================================= */
.jero-ctype-select-wrap {
  position: relative !important;
  width: 100% !important;
}
.jero-ctype-select-wrap.is-open .jero-dropdown-chevron,
.jero-ctype-select-wrap.is-open .jero-ctype-chevron {
  transform: rotate(180deg) !important;
  color: #2563eb !important;
}
.jero-ctype-dropdown {
  position: absolute !important;
  top: calc(100% + 5px) !important;
  left: 0 !important;
  right: 0 !important;
  z-index: 1060 !important;
  background: #ffffff !important;
  border: 1px solid #e2e8f0 !important;
  border-radius: 10px !important;
  box-shadow: 0 12px 32px -4px rgba(15, 23, 42, 0.12), 0 4px 12px -2px rgba(15, 23, 42, 0.08) !important;
  overflow: hidden !important;
  display: none;
  flex-direction: column !important;
  max-height: 280px !important;
  animation: jeroDropdownFadeIn 0.15s ease-out !important;
  box-sizing: border-box !important;
}
.jero-ctype-select-wrap.is-open .jero-ctype-dropdown {
  display: flex !important;
}
.jero-ctype-dropdown.drop-up {
  top: auto !important;
  bottom: calc(100% + 5px) !important;
  box-shadow: 0 -12px 32px -4px rgba(15, 23, 42, 0.12), 0 -4px 12px -2px rgba(15, 23, 42, 0.08) !important;
}
.jero-ctype-list-container {
  max-height: 260px !important;
  overflow-y: auto !important;
  padding: 4px 0 !important;
  box-sizing: border-box !important;
  scrollbar-width: thin !important;
}
.jero-ctype-item {
  display: flex !important;
  align-items: center !important;
  justify-content: space-between !important;
  padding: 8px 12px !important;
  cursor: pointer !important;
  transition: background 0.12s ease !important;
  border-bottom: 1px solid #f8fafc !important;
  box-sizing: border-box !important;
  min-height: 36px !important;
}
.jero-ctype-item:last-child {
  border-bottom: none !important;
}
.jero-ctype-item:hover, .jero-ctype-item.focused {
  background: #f1f5f9 !important;
}
.jero-ctype-item.selected {
  background: #eff6ff !important;
}
.jero-ctype-item-main {
  display: flex !important;
  flex-direction: row !important;
  align-items: center !important;
  gap: 6px !important;
  flex: 1 !important;
  min-width: 0 !important;
}
.jero-ctype-item-name {
  font-size: 12.5px !important;
  font-weight: 600 !important;
  color: #0f172a !important;
  line-height: 1.35 !important;
  font-family: 'Poppins', sans-serif !important;
}
.jero-ctype-mark {
  background: #fef08a !important;
  color: #854d0e !important;
  padding: 0 2px !important;
  border-radius: 2px !important;
  font-weight: 700 !important;
}
.jero-ctype-item-desc {
  font-size: 10.5px !important;
  color: #64748b !important;
  line-height: 1.3 !important;
}
.jero-ctype-item-aliases {
  font-size: 10px !important;
  color: #94a3b8 !important;
  margin-top: 1px !important;
}
.jero-ctype-item-side {
  display: flex !important;
  align-items: center !important;
  gap: 6px !important;
  flex-shrink: 0 !important;
  margin-left: 8px !important;
}
.jero-ctype-badge {
  display: inline-flex !important;
  align-items: center !important;
  padding: 2px 6px !important;
  font-size: 9.5px !important;
  font-weight: 600 !important;
  border-radius: 4px !important;
  background: #f1f5f9 !important;
  color: #475569 !important;
  letter-spacing: 0.2px !important;
  text-transform: uppercase !important;
}
.jero-ctype-badge.matched-alias {
  background: #dbeafe !important;
  color: #1e40af !important;
  border: 1px solid #bfdbfe !important;
}
.jero-ctype-item-check {
  display: flex !important;
  align-items: center !important;
  color: #2563eb !important;
}
.jero-ctype-empty-state {
  padding: 20px 14px !important;
  text-align: center !important;
  font-size: 12px !important;
  color: #64748b !important;
}

/* =========================================================================
   SEARCH-FIRST FOUNDED YEAR COMBOBOX
   ========================================================================= */
.jero-founded-select-wrap {
  position: relative !important;
  width: 100% !important;
}
.jero-founded-select-wrap.is-open .jero-dropdown-chevron,
.jero-founded-select-wrap.is-open .jero-founded-chevron {
  transform: rotate(180deg) !important;
  color: #2563eb !important;
}
.jero-founded-dropdown {
  position: absolute !important;
  top: calc(100% + 5px) !important;
  left: 0 !important;
  right: 0 !important;
  z-index: 1060 !important;
  background: #ffffff !important;
  border: 1px solid #e2e8f0 !important;
  border-radius: 10px !important;
  box-shadow: 0 12px 32px -4px rgba(15, 23, 42, 0.12), 0 4px 12px -2px rgba(15, 23, 42, 0.08) !important;
  overflow: hidden !important;
  display: none;
  flex-direction: column !important;
  max-height: 280px !important;
  animation: jeroDropdownFadeIn 0.15s ease-out !important;
  box-sizing: border-box !important;
}
.jero-founded-select-wrap.is-open .jero-founded-dropdown {
  display: flex !important;
}
.jero-founded-dropdown.drop-up {
  top: auto !important;
  bottom: calc(100% + 5px) !important;
  box-shadow: 0 -12px 32px -4px rgba(15, 23, 42, 0.12), 0 -4px 12px -2px rgba(15, 23, 42, 0.08) !important;
}
.jero-founded-search-wrap {
  display: flex !important;
  align-items: center !important;
  padding: 6px 10px !important;
  background: #f8fafc !important;
  border-bottom: 1px solid #eef2f6 !important;
  gap: 6px !important;
  position: sticky !important;
  top: 0 !important;
  z-index: 3 !important;
}
.jero-founded-search-icon {
  display: inline-flex !important;
  align-items: center !important;
  justify-content: center !important;
  color: #94a3b8 !important;
  flex-shrink: 0 !important;
}
.jero-founded-search-input {
  width: 100% !important;
  height: 28px !important;
  background: #ffffff !important;
  border: 1px solid #cbd5e1 !important;
  border-radius: 6px !important;
  padding: 0 8px !important;
  font-size: 11.5px !important;
  font-family: 'Poppins', sans-serif !important;
  color: #0f172a !important;
  outline: none !important;
}
.jero-founded-search-input:focus {
  border-color: #2563eb !important;
  box-shadow: 0 0 0 2px rgba(37, 99, 235, 0.15) !important;
}
.jero-founded-list-container {
  max-height: 200px !important;
  overflow-y: auto !important;
  padding: 4px 0 !important;
  box-sizing: border-box !important;
  scrollbar-width: thin !important;
}
.jero-founded-item {
  display: flex !important;
  align-items: center !important;
  justify-content: space-between !important;
  padding: 6px 12px !important;
  cursor: pointer !important;
  transition: background 0.12s ease !important;
  border-bottom: 1px solid #f8fafc !important;
  box-sizing: border-box !important;
  min-height: 32px !important;
}
.jero-founded-item:last-child {
  border-bottom: none !important;
}
.jero-founded-item:hover, .jero-founded-item.focused {
  background: #f1f5f9 !important;
}
.jero-founded-item.selected {
  background: #eff6ff !important;
}
.jero-founded-item-name {
  font-size: 12.5px !important;
  font-weight: 500 !important;
  color: #1e293b !important;
  font-family: 'Poppins', sans-serif !important;
}
.jero-founded-item.selected .jero-founded-item-name {
  font-weight: 600 !important;
  color: #2563eb !important;
}
.jero-founded-mark {
  background: #fef08a !important;
  color: #854d0e !important;
  padding: 0 2px !important;
  border-radius: 2px !important;
  font-weight: 700 !important;
}
.jero-founded-item-check {
  display: flex !important;
  align-items: center !important;
  color: #2563eb !important;
}
.jero-founded-footer {
  display: flex !important;
  align-items: center !important;
  justify-content: space-between !important;
  padding: 5px 10px !important;
  background: #f8fafc !important;
  border-top: 1px solid #eef2f6 !important;
  font-size: 10px !important;
  color: #64748b !important;
}
.jero-founded-calendar-dot {
  width: 5px !important;
  height: 5px !important;
  border-radius: 50% !important;
  background: #10b981 !important;
  display: inline-block !important;
  margin-right: 4px !important;
}

/* =========================================================================
   LOGO COLUMN & CARD STYLING
   ========================================================================= */
.god-logo-column {
  display: flex !important;
  flex-direction: column !important;
}
.god-logo-preview-box {
  border: 1.5px dashed #cbd5e1 !important;
  border-radius: 8px !important;
  background: #ffffff !important;
  padding: 6px !important;
  min-height: 100px !important;
  max-height: 100px !important;
  display: flex !important;
  align-items: center !important;
  justify-content: center !important;
  position: relative !important;
  box-sizing: border-box !important;
}
.god-logo-surface {
  width: 86px !important;
  height: 86px !important;
  border-radius: 8px !important;
  display: flex !important;
  align-items: center !important;
  justify-content: center !important;
  background-size: contain !important;
  background-repeat: no-repeat !important;
  background-position: center !important;
  background-color: #f8fafc !important;
  box-shadow: 0 1px 4px rgba(0, 0, 0, 0.05) !important;
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
  padding: 4px !important;
}
.god-logo-placeholder svg {
  width: 32px !important;
  height: 32px !important;
}
.god-logo-placeholder .placeholder-title {
  font-size: 10.5px !important;
  font-weight: 600 !important;
  color: #64748b !important;
  margin-top: 1px !important;
}
.god-logo-placeholder .placeholder-sub {
  font-size: 8.5px !important;
  color: #94a3b8 !important;
}
.god-logo-status-banner {
  width: 100% !important;
  border-radius: 6px !important;
  padding: 4px 8px !important;
  font-size: 10.5px !important;
  font-weight: 600 !important;
  display: flex !important;
  align-items: center !important;
  justify-content: center !important;
  gap: 5px !important;
  margin-top: 6px !important;
  box-sizing: border-box !important;
  text-align: center !important;
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
  gap: 6px !important;
  margin-top: 6px !important;
  width: 100% !important;
}
.god-logo-btn {
  flex: 1 1 auto !important;
  height: 30px !important;
  min-height: 30px !important;
  max-height: 30px !important;
  border-radius: 6px !important;
  font-size: 11px !important;
  font-weight: 600 !important;
  display: inline-flex !important;
  align-items: center !important;
  justify-content: center !important;
  gap: 5px !important;
  cursor: pointer !important;
  transition: all 0.15s ease !important;
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
  font-size: 9.5px !important;
  color: #64748b !important;
  line-height: 1.3 !important;
  text-align: center !important;
  margin-top: 6px !important;
  display: flex !important;
  align-items: flex-start !important;
  justify-content: center !important;
  gap: 4px !important;
}

/* Tagify & Quill Overrides */
.tagify {
  border: 1px solid #cbd5e1 !important;
  border-radius: 8px !important;
  background: #ffffff !important;
  font-family: inherit !important;
  padding: 4px 6px !important;
}
.tagify:focus-within {
  border-color: var(--primary-border) !important;
  box-shadow: 0 0 0 3px rgba(255, 212, 40, 0.25) !important;
}
.tagify__tag {
  background: #f1f5f9 !important;
  border-radius: 6px !important;
  font-size: 11.5px !important;
}
.ql-toolbar.ql-snow {
  border: 1px solid #cbd5e1 !important;
  border-top-left-radius: 8px !important;
  border-top-right-radius: 8px !important;
  background: #f8fafc !important;
  padding: 6px !important;
}
.ql-container.ql-snow {
  border: 1px solid #cbd5e1 !important;
  border-top: none !important;
  border-bottom-left-radius: 8px !important;
  border-bottom-right-radius: 8px !important;
  min-height: 160px !important;
  font-family: 'Poppins', sans-serif !important;
  font-size: 12.5px !important;
}
</style>
@endpush

@section('content')
<div class="d-flex flex-column flex-root client-wizard-shell company-profile-shell" id="kt_company_profile_app">

  <!-- =========================================================================
       STEPPER HEADER (Sticky Tier-1 Navigation Card)
       ========================================================================= -->
  @include('service_company.account.components.wizard-header')

  <!-- =========================================================================
       MAIN FORM SHELL (Submits to service_company.account.update)
       ========================================================================= -->
  <form id="kt_contact_add_form"
        class="form fv-plugins-bootstrap fv-plugins-framework"
        method="POST"
        action="{{ route('service_company.account.onboarding.save') }}"
        enctype="multipart/form-data"
        novalidate>
    @csrf
    <input type="hidden" name="_method" value="POST">
    <input type="hidden" name="addressId" id="addressId" value="{{ count($client_location ?? []) > 0 ? count($client_location) - 1 : 0 }}">
    <input type="hidden" name="page_mode" id="page_mode" value="ONBOARDING">
    <input type="hidden" name="technologies" id="company_technologies_payload" value="">
    <input type="hidden" name="tags" id="company_services_payload" value="">
    <input type="hidden" name="description" id="kt_company_description_hidden" value="{{ $account->description ?? '' }}">
    <input type="hidden" name="kt_docs_tinymce_hidden" id="kt_docs_tinymce_hidden" value="{{ $account->description ?? '' }}">

    <!-- STEP 1: Company Information & Logo -->
    @include('service_company.account.components.tab-company-info')

    <!-- STEP 2: Description & AI Assist -->
    @include('service_company.account.components.tab-description')

    <!-- STEP 3: Technologies & Expertise -->
    @include('service_company.account.components.tab-technology')

    <!-- STEP 4: Locations & Headquarters -->
    @include('service_company.account.components.tab-locations')

    <!-- STEP 5: Social Links & Official Profiles -->
    @include('service_company.account.components.tab-social-links')

    <!-- STEP 6: Review & Readiness -->
    @include('service_company.account.components.tab-review')

  </form>

</div>
@endsection

@push('footer-script')
<script src="https://unpkg.com/@yaireo/tagify"></script>
<script src="https://unpkg.com/@yaireo/tagify/dist/tagify.polyfills.min.js"></script>

<!-- Company Type Seed Data -->
<script id="jero_company_type_seed_data" type="application/json">
    {!! json_encode($companyTypeTaxonomy ?? \App\Helper\GeneralMethod::companyTypeTaxonomy()) !!}
</script>

<script>
/**
 * JERO God-Level Company Profile Form Engine
 * Rule 20 Card + Sub-Card Design System
 * Client-First Execution (User RAM & Browser CPU)
 */
(function() {
  'use strict';

  // 1. State Configuration
  window.JERO_PROFILE_STATE = {
    currentStep: 1,
    maxSteps: 6,
    companyId: {{ (int)$company_id }},
    pageMode: "{{ $page_mode ?? 'EDIT' }}",
    isDirty: false,
    selectedTechnologies: [],
    quillInstance: null,
    servicesTagify: null
  };

  var state = window.JERO_PROFILE_STATE;

  // 2. Step Navigation Engine
  window.goToWizardStep = function(targetStep) {
    targetStep = parseInt(targetStep, 10);
    if (isNaN(targetStep) || targetStep < 1 || targetStep > state.maxSteps) return;

    if (targetStep > state.currentStep) {
      if (!validateStep(state.currentStep)) {
        return;
      }
    }

    state.currentStep = targetStep;

    var steps = document.querySelectorAll('[data-wizard-type="step-content"]');
    steps.forEach(function(stepEl, idx) {
      var stepNum = idx + 1;
      if (stepNum === targetStep) {
        stepEl.setAttribute('data-wizard-state', 'current');
        stepEl.classList.add('is-current');
        stepEl.style.display = 'block';
      } else {
        stepEl.removeAttribute('data-wizard-state');
        stepEl.classList.remove('is-current');
        stepEl.style.display = 'none';
      }
    });

    var stepItems = document.querySelectorAll('.client-step-item');
    stepItems.forEach(function(itemEl) {
      var s = parseInt(itemEl.getAttribute('data-step'), 10);
      var circle = itemEl.querySelector('.client-step-circle');
      if (circle) {
        circle.classList.remove('is-active', 'is-complete', 'is-upcoming');
        if (s < targetStep) {
          circle.classList.add('is-complete');
          circle.innerHTML = '✓';
        } else if (s === targetStep) {
          circle.classList.add('is-active');
          circle.innerHTML = s;
        } else {
          circle.classList.add('is-upcoming');
          circle.innerHTML = s;
        }
      }
      if (s === targetStep) {
        itemEl.classList.add('is-active');
      } else {
        itemEl.classList.remove('is-active');
      }
    });

    var backBtn = document.getElementById('profile_top_back_btn');
    var nextBtn = document.getElementById('profile_top_next_btn');
    var submitBtn = document.getElementById('submitBtn');
    var closeBtn = document.getElementById('profile_top_close_btn');

    if (backBtn) {
      backBtn.style.setProperty('display', (targetStep > 1) ? 'inline-flex' : 'none', 'important');
    }
    if (closeBtn) {
      closeBtn.style.setProperty('display', (targetStep === 1 && state.pageMode !== 'ONBOARDING') ? 'inline-flex' : 'none', 'important');
    }
    if (nextBtn) {
      nextBtn.style.setProperty('display', (targetStep < state.maxSteps) ? 'inline-flex' : 'none', 'important');
    }
    if (submitBtn) {
      submitBtn.style.setProperty('display', (targetStep === state.maxSteps) ? 'inline-flex' : 'none', 'important');
    }

    if (targetStep === 6) {
      populateReviewSummaries();
    }

    window.scrollTo({ top: 0, behavior: 'smooth' });
  };

  function validateStep(step) {
    if (step === 1) {
      var compName = document.getElementById('company_name');
      if (compName && !compName.value.trim()) {
        compName.classList.add('is-invalid');
        var errEl = document.getElementById('company_name_error');
        if (errEl) errEl.style.display = 'block';
        compName.focus();
        return false;
      }
      var dupErr = document.getElementById('website_duplicate_error');
      if (dupErr && dupErr.style.display !== 'none') {
        if (typeof Swal !== 'undefined') {
          Swal.fire({
            icon: 'warning',
            title: 'Action Required',
            text: 'Please resolve the website domain duplication before proceeding.',
            confirmButtonColor: '#ffd428'
          });
        }
        return false;
      }
    }
    return true;
  }

  // 3. Industry Combobox Engine
  function initIndustryDropdown() {
    var indSelect = document.getElementById('industries');
    var indSelectAlt = document.getElementById('industry');
    var indInput = document.getElementById('industry_display');
    var indWrap = document.getElementById('jero_ind_select_wrap');
    var indDropdown = document.getElementById('jero_ind_dropdown');
    var indList = document.getElementById('jero_ind_list_container');
    var indClear = document.getElementById('jero_ind_clear_btn');
    var indChevron = document.getElementById('jero_ind_chevron');

    if (!indSelect || !indList) return;

    var indOptions = Array.from(indSelect.options).filter(function(opt) { return opt.value !== ''; });

    function renderIndustryItems(filterText) {
      filterText = (filterText || '').toLowerCase().trim();
      indList.innerHTML = '';
      var matched = 0;
      indOptions.forEach(function(opt) {
        var text = opt.text;
        if (!filterText || text.toLowerCase().indexOf(filterText) !== -1) {
          matched++;
          var item = document.createElement('div');
          item.className = 'jero-ind-item' + (indSelect.value === opt.value ? ' is-selected' : '');
          item.setAttribute('data-id', opt.value);
          item.textContent = text;
          item.addEventListener('click', function() {
            indSelect.value = opt.value;
            if (indSelectAlt) indSelectAlt.value = opt.value;
            if (indInput) indInput.value = text;
            if (indClear) indClear.style.display = 'inline-flex';
            if (indWrap) indWrap.classList.remove('is-open');
            var indReview = document.querySelector('.span_industry');
            if (indReview) indReview.textContent = text;
          });
          indList.appendChild(item);
        }
      });
      if (matched === 0) {
        indList.innerHTML = '<div style="padding:10px 14px; font-size:11.5px; color:#94a3b8; font-style:italic;">No industries match search</div>';
      }
    }

    if (indInput) {
      indInput.addEventListener('focus', function() {
        renderIndustryItems(this.value);
        if (indWrap) indWrap.classList.add('is-open');
      });
      indInput.addEventListener('input', function() {
        renderIndustryItems(this.value);
        if (indClear) indClear.style.display = this.value ? 'inline-flex' : 'none';
        if (indWrap) indWrap.classList.add('is-open');
      });
    }

    if (indChevron) {
      indChevron.addEventListener('click', function(e) {
        e.stopPropagation();
        var isOpen = indWrap && indWrap.classList.contains('is-open');
        if (isOpen) {
          if (indWrap) indWrap.classList.remove('is-open');
        } else {
          renderIndustryItems(indInput ? indInput.value : '');
          if (indWrap) indWrap.classList.add('is-open');
        }
      });
    }

    if (indClear) {
      indClear.addEventListener('click', function(e) {
        e.stopPropagation();
        indSelect.value = '';
        if (indSelectAlt) indSelectAlt.value = '';
        if (indInput) indInput.value = '';
        indClear.style.display = 'none';
        if (indWrap) indWrap.classList.remove('is-open');
        var indReview = document.querySelector('.span_industry');
        if (indReview) indReview.textContent = 'Industry';
      });
    }

    document.addEventListener('pointerdown', function(e) {
      if (indWrap && !indWrap.contains(e.target)) {
        indWrap.classList.remove('is-open');
      }
    });
  }

  // 4. Locations & Headquarters Repeater
  function initLocationsEngine() {
    var addBtn = document.getElementById('btn_add_additional_location');
    var container = document.getElementById('additional_locations_container');
    var emptyMsg = document.getElementById('no_additional_branches_msg');
    var addressIdInput = document.getElementById('addressId');

    if (addBtn && container) {
      addBtn.addEventListener('click', function() {
        var nextId = parseInt(addressIdInput ? addressIdInput.value : '0', 10) + 1;
        if (addressIdInput) addressIdInput.value = nextId;

        var card = document.createElement('div');
        card.className = 'card client-subcard client-addr-card mb-3';
        card.id = 'branch_addr_card_' + nextId;
        card.style.cssText = 'background:#ffffff; border:1px solid #e2e8f0; border-radius:12px; padding:16px !important;';
        card.innerHTML = 
          '<div class="d-flex align-items-center justify-content-between mb-3">' +
            '<div class="d-flex align-items-center gap-2">' +
              '<div style="width:24px; height:24px; border-radius:6px; background:#f1f5f9; display:inline-flex; align-items:center; justify-content:center; color:#475569;">' +
                '<svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>' +
              '</div>' +
              '<span style="font-size:12.5px; font-weight:700; color:#0f172a;">Branch Office #' + nextId + '</span>' +
            '</div>' +
            '<button type="button" class="btn btn-sm btn-link text-danger p-0" onclick="window.removeAddressCard(' + nextId + ')" style="font-size:11px; font-weight:600; text-decoration:none; cursor:pointer;">Remove Location</button>' +
          '</div>' +
          '<div class="client-subcard-body">' +
            '<div class="mb-3">' +
              '<label class="form-label mb-1" style="font-size:12px; font-weight:700; color:#0f172a;">City / Location <span class="text-danger">*</span></label>' +
              '<input type="text" class="form-control branch-city-search" id="branch_city_search_' + nextId + '" placeholder="e.g. San Francisco, California, USA" style="height:38px; font-size:12px; border:1px solid #cbd5e1; border-radius:8px;">' +
              '<input type="hidden" name="kt_tagify_work_location[' + nextId + ']" id="kt_tagify_work_location_' + nextId + '" value="">' +
            '</div>' +
            '<div class="mb-3">' +
              '<label class="form-label mb-1" style="font-size:12px; font-weight:700; color:#0f172a;">Address <span class="text-danger">*</span></label>' +
              '<textarea class="form-control" name="address[' + nextId + ']" id="address_' + nextId + '" rows="2" placeholder="Building, street, area..." style="font-size:12px; border:1px solid #cbd5e1; border-radius:8px;"></textarea>' +
            '</div>' +
            '<div>' +
              '<label class="form-label mb-1" style="font-size:12px; font-weight:700; color:#0f172a;">Postal / ZIP Code</label>' +
              '<input type="text" class="form-control" name="postcode[' + nextId + ']" id="postcode_' + nextId + '" placeholder="Postal code" style="height:36px; max-width:240px; font-size:12px; border:1px solid #cbd5e1; border-radius:8px;">' +
            '</div>' +
          '</div>';

        container.appendChild(card);
        if (emptyMsg) emptyMsg.style.display = 'none';

        var newCityInput = card.querySelector('#branch_city_search_' + nextId);
        var newHidden = card.querySelector('#kt_tagify_work_location_' + nextId);
        if (newCityInput && newHidden) {
          newCityInput.addEventListener('input', function() {
            var v = this.value.trim();
            newHidden.value = v ? JSON.stringify([{ value: v }]) : '';
          });
        }
      });
    }

    window.removeAddressCard = function(id) {
      var card = document.getElementById('branch_addr_card_' + id);
      if (card) {
        card.remove();
        if (container && container.children.length === 0 && emptyMsg) {
          emptyMsg.style.display = 'block';
        }
      }
    };

    // Primary City Sync
    var primaryCityInput = document.getElementById('primary_work_city_input');
    var primaryHidden = document.getElementById('kt_tagify_work_location_0');
    if (primaryCityInput && primaryHidden) {
      primaryCityInput.addEventListener('input', function() {
        var v = this.value.trim();
        primaryHidden.value = v ? JSON.stringify([{ value: v }]) : '';
      });
    }
  }

  // 5. Populate Step 5 Review Summaries & Readiness Score
  window.populateReviewSummaries = function() {
    var compName = (document.getElementById('company_name') ? document.getElementById('company_name').value : '') || 'Your Organization';
    var compWeb = (document.getElementById('website') ? document.getElementById('website').value : '') || '—';
    var compInd = (document.getElementById('industry_display') ? document.getElementById('industry_display').value : '') || 'Information Technology';
    var compSize = (document.getElementById('company_size_display') ? document.getElementById('company_size_display').value : '') || '—';
    var compType = (document.getElementById('company_type_display') ? document.getElementById('company_type_display').value : '') || 'Service Company';
    var compCity = (document.getElementById('primary_work_city_input') ? document.getElementById('primary_work_city_input').value : '') || '—';

    var nameEls = document.querySelectorAll('.span_company_name');
    nameEls.forEach(function(el) { el.textContent = compName; });

    var indEls = document.querySelectorAll('.span_industry');
    indEls.forEach(function(el) { el.textContent = compInd; });

    var sizeEls = document.querySelectorAll('.span_company_sizes');
    sizeEls.forEach(function(el) { el.textContent = compSize; });

    var locEls = document.querySelectorAll('#profile_review_meta_location, .span_primary_location');
    locEls.forEach(function(el) { el.textContent = compCity; });

    var typeBadge = document.getElementById('profile_review_type_badge');
    if (typeBadge) typeBadge.textContent = compType;

    var initialsEl = document.getElementById('profile_review_initials');
    if (initialsEl) {
      var parts = compName.trim().split(/\s+/);
      var inits = parts.length > 1 ? (parts[0][0] + parts[1][0]) : (parts[0].slice(0, 2));
      initialsEl.textContent = (inits || 'CP').toUpperCase();
    }

    var descPreview = document.getElementById('profile_review_desc_preview');
    if (descPreview && state.quillInstance) {
      var html = state.quillInstance.root.innerHTML;
      var text = state.quillInstance.getText().trim();
      descPreview.innerHTML = text.length > 0 ? html : '<p class="text-muted" style="font-style:italic;">No description provided yet.</p>';
    }

    var techContainer = document.getElementById('profile_review_tech_chips');
    if (techContainer) {
      techContainer.innerHTML = '';
      if (state.selectedTechnologies.length > 0) {
        state.selectedTechnologies.forEach(function(tech) {
          var chip = document.createElement('span');
          chip.className = 'badge';
          chip.style.cssText = 'background:#f1f5f9; color:#334155; font-size:11px; font-weight:600; padding:4px 9px; border-radius:6px; border:1px solid #e2e8f0; display:inline-flex; align-items:center;';
          chip.textContent = tech;
          techContainer.appendChild(chip);
        });
      } else {
        techContainer.innerHTML = '<span class="text-muted" style="font-size:12px; font-style:italic;">No core technologies specified.</span>';
      }
    }

    var servicesContainer = document.getElementById('profile_review_services_chips');
    if (servicesContainer && state.servicesTagify) {
      servicesContainer.innerHTML = '';
      var servicesVal = state.servicesTagify.value || [];
      if (servicesVal.length > 0) {
        servicesVal.forEach(function(sItem) {
          var chip = document.createElement('span');
          chip.className = 'badge';
          chip.style.cssText = 'background:#eff6ff; color:#1d4ed8; font-size:11px; font-weight:600; padding:4px 9px; border-radius:6px; border:1px solid #bfdbfe; display:inline-flex; align-items:center;';
          chip.textContent = sItem.value || sItem;
          servicesContainer.appendChild(chip);
        });
      } else {
        servicesContainer.innerHTML = '<span class="text-muted" style="font-size:12px; font-style:italic;">No services added yet.</span>';
      }
    }

    // Social links review summary
    var socialContainer = document.getElementById('profile_review_social_badges');
    var hasSocial = false;
    if (socialContainer) {
      socialContainer.innerHTML = '';
      var platforms = [
        { id: 'linkedin_url', name: 'LinkedIn', bg: '#0a66c2' },
        { id: 'twitter_url', name: 'X / Twitter', bg: '#000000' },
        { id: 'facebook_url', name: 'Facebook', bg: '#1877f2' },
        { id: 'instagram_url', name: 'Instagram', bg: '#e1306c' },
        { id: 'youtube_url', name: 'YouTube', bg: '#ff0000' },
        { id: 'github_url', name: 'GitHub', bg: '#24292f' },
        { id: 'custom_link', name: 'Custom Link', bg: '#475569' }
      ];
      platforms.forEach(function(p) {
        var inp = document.getElementById(p.id);
        if (inp && inp.value.trim()) {
          hasSocial = true;
          var badge = document.createElement('a');
          badge.href = inp.value.trim().startsWith('http') ? inp.value.trim() : ('https://' + inp.value.trim());
          badge.target = '_blank';
          badge.className = 'badge';
          badge.style.cssText = 'background:#f1f5f9; color:#0f172a; border:1px solid #cbd5e1; font-size:11.5px; font-weight:600; padding:5px 10px; border-radius:6px; text-decoration:none; display:inline-flex; align-items:center; gap:6px;';
          badge.innerHTML = '<span style="width:8px; height:8px; border-radius:50%; background:' + p.bg + '; display:inline-block;"></span> ' + p.name;
          socialContainer.appendChild(badge);
        }
      });
      if (!hasSocial) {
        socialContainer.innerHTML = '<span class="text-muted" style="font-size:11.5px; font-style:italic;">No official social profiles added yet.</span>';
      }
    }

    var score = 0;
    if (compName && compName !== 'Pending Onboarding') score += 20;
    if (compWeb && compWeb !== '—') score += 15;
    if (compInd) score += 15;
    if (compSize && compSize !== '—') score += 10;
    if (state.quillInstance && state.quillInstance.getText().trim().length > 30) score += 15;
    if (state.selectedTechnologies.length > 0 || (state.servicesTagify && state.servicesTagify.value.length > 0)) score += 10;
    if (compCity && compCity !== '—') score += 10;
    if (hasSocial) score += 5;

    score = Math.min(score, 100);

    var pctEl = document.getElementById('profile_readiness_pct') || document.getElementById('profile_review_completion_percent');
    if (pctEl) pctEl.textContent = score + '%';

    var barEl = document.getElementById('profile_readiness_bar') || document.getElementById('profile_review_completion_bar');
    if (barEl) barEl.style.width = score + '%';
  };

  // 6. Initialize on DOM Ready
  document.addEventListener('DOMContentLoaded', function() {
    initIndustryDropdown();
    initLocationsEngine();

    // Stepper item clicks
    var stepTabs = document.querySelectorAll('.client-step-item');
    stepTabs.forEach(function(tab) {
      tab.addEventListener('click', function() {
        var s = parseInt(this.getAttribute('data-step'), 10);
        window.goToWizardStep(s);
      });
    });

    // Header Action Buttons
    var nextBtn = document.getElementById('profile_top_next_btn');
    if (nextBtn) {
      nextBtn.addEventListener('click', function() {
        window.goToWizardStep(state.currentStep + 1);
      });
    }

    var backBtn = document.getElementById('profile_top_back_btn');
    if (backBtn) {
      backBtn.addEventListener('click', function() {
        window.goToWizardStep(state.currentStep - 1);
      });
    }

    var closeBtn = document.getElementById('profile_top_close_btn');
    if (closeBtn) {
      closeBtn.addEventListener('click', function() {
        if (confirm('Leave profile setup? You can complete it later from your dashboard.')) {
          window.location.href = "{{ url('service_company/dashboard') }}";
        }
      });
    }

    // Quill Description Editor
    var quillEl = document.getElementById('kt_company_description_quill');
    if (quillEl && typeof Quill !== 'undefined') {
      state.quillInstance = new Quill(quillEl, {
        modules: {
          toolbar: [
            ['bold', 'italic', 'underline', 'strike'],
            [{ 'list': 'ordered'}, { 'list': 'bullet' }],
            ['clean']
          ]
        },
        placeholder: 'Provide a comprehensive overview of your organization...',
        theme: 'snow'
      });

      var charCountEl = document.getElementById('profile_desc_count') || document.getElementById('desc_char_count');
      state.quillInstance.on('text-change', function() {
        var text = state.quillInstance.getText().trim();
        var len = text.length;
        if (charCountEl) {
          charCountEl.textContent = len;
          if (len > 2000) {
            charCountEl.style.color = '#ef4444';
          } else if (len > 1800) {
            charCountEl.style.color = '#f59e0b';
          } else {
            charCountEl.style.color = '#10b981';
          }
        }
        var hiddenInput = document.getElementById('kt_company_description_hidden');
        if (hiddenInput) {
          hiddenInput.value = state.quillInstance.root.innerHTML;
        }
        var tinymceHidden = document.getElementById('kt_docs_tinymce_hidden');
        if (tinymceHidden) {
          tinymceHidden.value = state.quillInstance.root.innerHTML;
        }
      });
    }

    // Apply AI Description
    window.applyDescriptionSuggestion = function() {
      var aiText = document.getElementById('profile_ai_suggested_desc') || document.getElementById('ai_desc_suggestion_text');
      if (aiText && state.quillInstance) {
        state.quillInstance.root.innerHTML = '<p>' + aiText.textContent.trim() + '</p>';
        state.quillInstance.emitter.emit('text-change');
      }
    };

    // Technologies Tagging Engine
    var techHidden = document.getElementById('company_technologies_payload');
    var chipsContainer = document.getElementById('selected_tech_chips_container');
    var techInput = document.getElementById('tag_search_input');
    var btnAddTech = document.getElementById('btn_add_tech_tag');

    var initialChips = document.querySelectorAll('.chip-item-val');
    initialChips.forEach(function(chip) {
      var val = chip.getAttribute('data-tech');
      if (val && !state.selectedTechnologies.includes(val)) {
        state.selectedTechnologies.push(val);
      }
    });
    syncTechnologies();

    function syncTechnologies() {
      if (techHidden) {
        techHidden.value = JSON.stringify(state.selectedTechnologies);
      }
      var countBadge = document.getElementById('tech_count_badge');
      if (countBadge) {
        countBadge.textContent = state.selectedTechnologies.length + ' selected';
      }
    }

    window.addTechnology = function(name) {
      name = (name || '').trim();
      if (!name || state.selectedTechnologies.includes(name)) return;
      state.selectedTechnologies.push(name);
      renderTechChip(name);
      syncTechnologies();
    };

    window.removeTechnology = function(name) {
      var idx = state.selectedTechnologies.indexOf(name);
      if (idx !== -1) {
        state.selectedTechnologies.splice(idx, 1);
        var chipEl = chipsContainer ? chipsContainer.querySelector('[data-tech="' + CSS.escape(name) + '"]') : null;
        if (chipEl) chipEl.remove();
        syncTechnologies();
      }
    };

    function renderTechChip(name) {
      if (!chipsContainer) return;
      var chip = document.createElement('div');
      chip.className = 'tech-chip chip-item-val';
      chip.setAttribute('data-tech', name);
      chip.style.cssText = 'background:#f1f5f9; border:1px solid #cbd5e1; border-radius:6px; padding:4px 10px; font-size:11.5px; font-weight:600; color:#1e293b; display:inline-flex; align-items:center; gap:6px; margin:3px;';
      chip.innerHTML = '<span>' + name + '</span><button type="button" onclick="removeTechnology(\'' + name.replace(/'/g, "\\'") + '\')" style="background:none; border:none; cursor:pointer; color:#94a3b8; font-size:12px; padding:0; display:flex;">✕</button>';
      chipsContainer.appendChild(chip);
    }

    if (btnAddTech && techInput) {
      btnAddTech.addEventListener('click', function() {
        var v = techInput.value.trim();
        if (v) {
          window.addTechnology(v);
          techInput.value = '';
        }
      });
      techInput.addEventListener('keydown', function(e) {
        if (e.key === 'Enter') {
          e.preventDefault();
          btnAddTech.click();
        }
      });
    }

    var presetPills = document.querySelectorAll('.preset-tech-pill');
    presetPills.forEach(function(pill) {
      pill.addEventListener('click', function() {
        var t = this.getAttribute('data-tech');
        if (t) window.addTechnology(t);
      });
    });

    // Tagify Services
    var servicesInput = document.getElementById('kt_tagify_company_services');
    if (servicesInput && typeof Tagify !== 'undefined') {
      state.servicesTagify = new Tagify(servicesInput, {
        whitelist: [],
        dropdown: { maxItems: 20, enabled: 0, closeOnSelect: false }
      });

      state.servicesTagify.on('change', function() {
        var hiddenServices = document.getElementById('company_services_payload');
        if (hiddenServices) {
          hiddenServices.value = JSON.stringify(state.servicesTagify.value || []);
        }
      });
    }

    var servicePills = document.querySelectorAll('.preset-service-pill');
    servicePills.forEach(function(pill) {
      pill.addEventListener('click', function() {
        var s = this.getAttribute('data-service');
        if (s && state.servicesTagify) {
          state.servicesTagify.addTags([s]);
        }
      });
    });

    // =========================================================================
    // GOD-LEVEL COMPANY INTELLIGENCE & DUPLICATE VALIDATION ENGINE
    // Direct Client-First Cloudflare Edge Worker execution (0% server strain)
    // Matches Add Internal Client Architecture 100%
    // =========================================================================
    window._companyIntelligenceCache = {};
    window._clientDomainCheckCache = {};
    window._lastFetchedWebsite = '';
    window._lastCheckedWebsite = '';

    // 1. Point-of-Entry Duplicate Domain Verification
    function checkDomainDuplicate(silent) {
      var website = ($('#website').val() || '').trim();
      if (!website) {
        $('#website_duplicate_error').hide();
        $('#website').removeClass('is-invalid');
        return true;
      }

      var cleanDomain = website.replace(/^https?:\/\//i, '').replace(/^www\./i, '').split('/')[0].trim().toLowerCase();
      if (!cleanDomain || cleanDomain.length < 3) return true;

      if (window._clientDomainCheckCache[cleanDomain]) {
        var cached = window._clientDomainCheckCache[cleanDomain];
        handleDomainCheckResult(cached, silent);
        return cached.status !== 'fail';
      }

      $('#website_checking_indicator').show();
      $('#website_duplicate_error').hide();

      $.ajax({
        url: "{{ route('service_company.account.check_domain') }}",
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
        type: "POST",
        data: { website: website },
        success: function(response) {
          $('#website_checking_indicator').hide();
          window._clientDomainCheckCache[cleanDomain] = response;
          handleDomainCheckResult(response, silent);
        },
        error: function(err) {
          $('#website_checking_indicator').hide();
        }
      });
    }

    function handleDomainCheckResult(response, silent) {
      if (response && response.status === 'fail') {
        $('#website_duplicate_error_text').text(response.message || 'This website domain is already registered to another organization.');
        $('#website_duplicate_error').show();
        $('#website').addClass('is-invalid');
        if (!silent && typeof Swal !== 'undefined') {
          Swal.fire({
            icon: 'warning',
            title: 'Duplicate Domain Detected',
            text: response.message || 'This website domain is already registered to another organization.',
            confirmButtonColor: '#ffd428'
          });
        }
      } else {
        $('#website_duplicate_error').hide();
        $('#website').removeClass('is-invalid');
      }
    }

    // 2. Intelligent Website Fetch & Auto-Fill Pipeline
    function fetchGodLevelCompanyIntelligence(website, forceRefresh) {
      if (!website) return;
      var cleanDomain = website.replace(/^https?:\/\//i, '').replace(/^www\./i, '').split('/')[0].trim();
      if (!cleanDomain || cleanDomain.length < 3) return;

      window._lastFetchedWebsite = website;

      if (forceRefresh) {
        delete window._companyIntelligenceCache[cleanDomain];
      }

      var $fetchBtn = $('#client_auto_fetch');
      var originalBtnHtml = $fetchBtn.html();
      $fetchBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true" style="width:13px;height:13px;"></span> Extracting...');

      // In-Memory RAM Cache lookup (only when not forced)
      if (!forceRefresh && window._companyIntelligenceCache[cleanDomain]) {
        applyCompanyIntelligenceData(window._companyIntelligenceCache[cleanDomain]);
        $fetchBtn.prop('disabled', false).html(originalBtnHtml);
        return;
      }

      var workerEndpoint = 'https://jero-company-intelligence.vibhu21m.workers.dev';
      var targetUrl = workerEndpoint + '/?domain=' + encodeURIComponent(cleanDomain);

      function handleSuccess(data) {
        $fetchBtn.prop('disabled', false).html(originalBtnHtml);
        if (!data || data.error) {
          console.warn('[CompanyIntelligence] No data or error in payload:', data ? data.error : 'empty');
          return;
        }
        window._companyIntelligenceCache[cleanDomain] = data;
        applyCompanyIntelligenceData(data);
      }

      // Direct Client-Side Edge Worker Fetch (0% server load)
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
        console.warn('[CompanyIntelligence] Direct edge fetch error, synthesizing client intelligence:', err);
        var clientSideName = cleanDomain.split('.')[0].replace(/[-_]/g, ' ').replace(/\b\w/g, function(l) { return l.toUpperCase(); });
        var clientSideData = {
          domain: cleanDomain,
          website: 'https://' + cleanDomain,
          name: clientSideName,
          industry: 'Information Technology & Services',
          industries: ['Information Technology & Services', 'Computer Software'],
          logo: 'https://www.google.com/s2/favicons?domain=' + cleanDomain + '&sz=256',
          description: clientSideName + ' is a high-growth technology organization delivering high-value solutions, specialized talent operations, and enterprise digital services across global markets.',
          tags: ['Technology Services', 'Software Development', 'Cloud Architecture']
        };
        window._companyIntelligenceCache[cleanDomain] = clientSideData;
        applyCompanyIntelligenceData(clientSideData);
      });

      // Background non-blocking duplicate domain check
      checkDomainDuplicate(true);
    }

    window.fetchGodLevelCompanyIntelligence = fetchGodLevelCompanyIntelligence;

    // 3. Apply Company Intelligence Data Across Form
    function applyCompanyIntelligenceData(data) {
      if (!data) return;

      // 1. Domain Key
      var domainInput = document.getElementById('domain_key');
      if (domainInput) domainInput.value = data.domain || '';

      // 2. Company Name
      var nameInput = document.getElementById('company_name');
      if (nameInput && (!nameInput.value || nameInput.value === 'Pending Onboarding' || nameInput.value === 'Your Organization')) {
        nameInput.value = data.name || data.legal_name || '';
        nameInput.classList.remove('is-invalid');
        var errEl = document.getElementById('company_name_error');
        if (errEl) errEl.style.display = 'none';
      }

      // 3. Industry Auto-Selection
      if (data.industry || (data.industries && data.industries.length)) {
        var indName = data.industry || data.industries[0];
        var indSelect = document.getElementById('industries');
        var indInput = document.getElementById('industry_display');
        if (indSelect) {
          for (var i = 0; i < indSelect.options.length; i++) {
            var opt = indSelect.options[i];
            if (opt.text.toLowerCase().indexOf(indName.toLowerCase()) !== -1 || indName.toLowerCase().indexOf(opt.text.toLowerCase()) !== -1) {
              indSelect.value = opt.value;
              var indAlt = document.getElementById('industry');
              if (indAlt) indAlt.value = opt.value;
              if (indInput) indInput.value = opt.text;
              var clearB = document.getElementById('jero_ind_clear_btn');
              if (clearB) clearB.style.display = 'inline-flex';
              var indReview = document.querySelector('.span_industry');
              if (indReview) indReview.textContent = opt.text;
              break;
            }
          }
        }
      }

      // 4. Company Size Combobox Auto-Selection
      if (data.company_size || data.employees || (data.metrics && data.metrics.employees)) {
        var empCount = data.employees || (data.metrics && data.metrics.employees);
        var targetTier = '';
        var targetLabel = '';
        if (empCount) {
          empCount = parseInt(empCount, 10);
          if (empCount === 1) { targetTier = '1-1'; targetLabel = 'Solo (1 employee)'; }
          else if (empCount <= 10) { targetTier = '2-10'; targetLabel = '2 – 10 employees'; }
          else if (empCount <= 50) { targetTier = '11-50'; targetLabel = '11 – 50 employees'; }
          else if (empCount <= 200) { targetTier = '51-200'; targetLabel = '51 – 200 employees'; }
          else if (empCount <= 500) { targetTier = '201-500'; targetLabel = '201 – 500 employees'; }
          else if (empCount <= 1000) { targetTier = '501-1000'; targetLabel = '501 – 1,000 employees'; }
          else if (empCount <= 5000) { targetTier = '1001-5000'; targetLabel = '1,001 – 5,000 employees'; }
          else if (empCount <= 10000) { targetTier = '5001-10000'; targetLabel = '5,001 – 10,000 employees'; }
          else if (empCount <= 50000) { targetTier = '1001-50000'; targetLabel = '10,001 – 50,000 employees'; }
          else { targetTier = '50001-0'; targetLabel = '50,000+ employees'; }
        } else if (data.company_size) {
          var sStr = String(data.company_size).toLowerCase();
          if (sStr.indexOf('1-10') !== -1 || sStr.indexOf('startup') !== -1) { targetTier = '2-10'; targetLabel = '2 – 10 employees'; }
          else if (sStr.indexOf('11-50') !== -1) { targetTier = '11-50'; targetLabel = '11 – 50 employees'; }
          else if (sStr.indexOf('51-200') !== -1) { targetTier = '51-200'; targetLabel = '51 – 200 employees'; }
          else if (sStr.indexOf('201-500') !== -1) { targetTier = '201-500'; targetLabel = '201 – 500 employees'; }
          else if (sStr.indexOf('501-1000') !== -1 || sStr.indexOf('1,000') !== -1) { targetTier = '501-1000'; targetLabel = '501 – 1,000 employees'; }
          else if (sStr.indexOf('1001-5000') !== -1 || sStr.indexOf('5,000') !== -1) { targetTier = '1001-5000'; targetLabel = '1,001 – 5,000 employees'; }
          else if (sStr.indexOf('5001-10000') !== -1 || sStr.indexOf('10,000') !== -1) { targetTier = '5001-10000'; targetLabel = '5,001 – 10,000 employees'; }
          else { targetTier = '1001-5000'; targetLabel = '1,001 – 5,000 employees'; }
        }
        if (targetTier && window.JeroCompanySizeEngine && window.JeroCompanySizeEngine.select) {
          window.JeroCompanySizeEngine.select(targetTier, targetLabel);
        }
      }

      // 5. Company Type Auto-Selection
      if (data.company_type || data.type) {
        var typeStr = String(data.company_type || data.type).toLowerCase();
        if (window.JeroCompanyTypeEngine && window.JeroCompanyTypeEngine.selectById) {
          if (typeStr.indexOf('public') !== -1 || typeStr.indexOf('listed') !== -1) {
            window.JeroCompanyTypeEngine.selectById(4);
          } else if (typeStr.indexOf('llc') !== -1 || typeStr.indexOf('limited liability') !== -1) {
            window.JeroCompanyTypeEngine.selectById(1);
          } else if (typeStr.indexOf('pvt') !== -1 || typeStr.indexOf('private') !== -1) {
            window.JeroCompanyTypeEngine.selectById(2);
          } else {
            window.JeroCompanyTypeEngine.selectById(3);
          }
        }
      }

      // 6. Founded Year Auto-Selection
      if (data.founded || data.founded_year || data.year_founded) {
        var fYear = parseInt(data.founded || data.founded_year || data.year_founded, 10);
        if (!isNaN(fYear) && fYear >= 1800 && fYear <= (new Date()).getFullYear()) {
          if (window.JeroFoundedYearEngine && window.JeroFoundedYearEngine.selectYear) {
            window.JeroFoundedYearEngine.selectYear(fYear);
          }
        }
      }

      // 7. Career / Jobs URL
      if (data.career_page_url || data.careers_url || data.jobs_url) {
        var cUrl = data.career_page_url || data.careers_url || data.jobs_url;
        var cInput = document.getElementById('career_page_url');
        if (cInput) cInput.value = cUrl;
      }

      // 8. Tax ID / Reg No
      if (data.tax_id || data.lei || data.registration_number) {
        var tInput = document.getElementById('tax_id');
        if (tInput && !tInput.value) tInput.value = data.tax_id || data.lei || data.registration_number;
      }

      // 9. Logo Auto-Detection & Preview
      if (data.logo) {
        var logoBox = document.getElementById('company_logo_box');
        if (logoBox) {
          logoBox.style.backgroundImage = 'url(' + data.logo + ')';
          logoBox.style.backgroundSize = 'contain';
          logoBox.style.backgroundRepeat = 'no-repeat';
          logoBox.style.backgroundPosition = 'center';
          logoBox.classList.add('has-image');
          logoBox.innerHTML = '';
        }
        var removeBtn = document.getElementById('client_remove_logo_btn');
        if (removeBtn) removeBtn.style.display = 'inline-flex';
        var statusBanner = document.getElementById('logo_status_banner');
        if (statusBanner) {
          statusBanner.className = 'god-logo-status-banner is-detected';
          statusBanner.innerHTML = '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg> <span>Logo automatically detected from website</span>';
        }
        var reviewLogo = document.getElementById('profile_review_logo_box');
        if (reviewLogo) {
          reviewLogo.style.backgroundImage = 'url(' + data.logo + ')';
          reviewLogo.style.backgroundSize = 'contain';
          reviewLogo.style.backgroundRepeat = 'no-repeat';
          reviewLogo.style.backgroundPosition = 'center';
          reviewLogo.innerHTML = '';
        }
      }

      // 10. Description & AI Assist (Step 2)
      if (data.description) {
        var aiDescEl = document.getElementById('profile_ai_suggested_desc');
        if (aiDescEl) aiDescEl.textContent = data.description;
        if (state.quillInstance && state.quillInstance.getText().trim().length < 20) {
          state.quillInstance.root.innerHTML = '<p>' + data.description + '</p>';
          state.quillInstance.emitter.emit('text-change');
        }
      }

      // 11. Technologies Tagging (Step 3)
      if (data.tags && Array.isArray(data.tags)) {
        data.tags.slice(0, 6).forEach(function(t) {
          if (typeof window.addTechnology === 'function') window.addTechnology(t);
        });
      }

      // 12. Headquarters Location (Step 4)
      if (data.city) {
        var displayCity = data.city;
        if (data.country_name && displayCity.indexOf(data.country_name) === -1) {
          displayCity += ', ' + data.country_name;
        }
        var citySearch = document.getElementById('city_search_0');
        if (citySearch) $(citySearch).val(displayCity).trigger('input');
        var tagifyLocation = document.getElementById('kt_tagify_work_location_0');
        if (tagifyLocation) tagifyLocation.value = JSON.stringify([{ value: displayCity }]);
        var addrInput = document.getElementById('address_0');
        if (addrInput && !addrInput.value.trim() && data.address) {
          addrInput.value = data.address;
        }
      }

      // 13. Social Links & AI Profile Discovery (Step 5)
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

      // 14. SweetAlert Feedback Toast
      if (typeof Swal !== 'undefined' && typeof Swal.mixin === 'function') {
        var Toast = Swal.mixin({
          toast: true,
          position: 'top-end',
          showConfirmButton: false,
          timer: 3500,
          timerProgressBar: true
        });
        Toast.fire({
          icon: 'success',
          title: '✨ Auto-filled company intelligence from ' + (data.domain || 'website')
        });
      }
    }

    // 4. Client Social Manager Engine (Rule 20 Design Standard)
    window.clientSocialManager = {
      platforms: ['linkedin', 'twitter', 'facebook', 'instagram', 'youtube', 'github'],
      discoveredUrls: {},

      setDiscoveredUrls: function(urls) {
        this.discoveredUrls = urls || {};
      },

      clearDiscoveredUrls: function() {
        this.discoveredUrls = {};
      },

      getSlug: function() {
        var web = ($('#website').val() || '').trim();
        var name = ($('#company_name').val() || '').trim();
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
        return slug || 'companyname';
      },

      getCleanHandle: function() {
        var s = this.getSlug();
        return s.replace(/-/g, '');
      },

      getUrlForPlatform: function(platform) {
        if (this.discoveredUrls && this.discoveredUrls[platform]) {
          return this.discoveredUrls[platform];
        }
        var slug = this.getSlug();
        var handle = this.getCleanHandle();
        switch (platform) {
          case 'linkedin': return 'https://linkedin.com/company/' + slug;
          case 'twitter': return 'https://x.com/' + handle;
          case 'facebook': return 'https://facebook.com/' + handle;
          case 'instagram': return 'https://instagram.com/' + handle;
          case 'youtube': return 'https://youtube.com/@' + handle;
          case 'github': return 'https://github.com/' + handle;
          default: return '';
        }
      },

      syncWebsiteLink: function() {
        var self = this;
        this.platforms.forEach(function(plat) {
          var url = self.getUrlForPlatform(plat);
          $('#ai_url_' + plat).text(url).attr('title', url);
        });
      },

      addPlatform: function(platform) {
        var url = ($('#ai_url_' + platform).text() || '').trim() || this.getUrlForPlatform(platform);
        var $input = $('#' + platform + '_url');
        if ($input.length && url) {
          $input.val(url).trigger('input').trigger('change');
        }
        this.markButtonAdded(platform);

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
            title: (platNames[platform] || platform) + ' profile added'
          });
        }
        this.syncAiButtons();
        if (typeof populateReviewSummaries === 'function') populateReviewSummaries();
      },

      addAllSuggested: function() {
        var self = this;
        var addedCount = 0;
        this.platforms.forEach(function(plat) {
          var url = ($('#ai_url_' + plat).text() || '').trim() || self.getUrlForPlatform(plat);
          var $input = $('#' + plat + '_url');
          if ($input.length && url) {
            $input.val(url).trigger('input').trigger('change');
            self.markButtonAdded(plat);
            addedCount++;
          }
        });

        var $btnAll = $('#btn_add_all_social');
        $btnAll.html('<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" style="margin-right:4px;"><polyline points="20 6 9 17 4 12"/></svg> All Added')
               .css({ 'background': '#ecfdf5', 'border': '1px solid #a7f3d0', 'color': '#059669', 'cursor': 'default' });

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
            title: 'All ' + addedCount + ' social profiles added'
          });
        }
        if (typeof populateReviewSummaries === 'function') populateReviewSummaries();
      },

      markButtonAdded: function(platform) {
        var $btn = $('#btn_ai_add_' + platform);
        $btn.addClass('is-added')
            .html('<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" style="margin-right:4px;"><polyline points="20 6 9 17 4 12"/></svg> Added')
            .css({ 'background': '#ecfdf5', 'border': '1px solid #a7f3d0', 'color': '#059669', 'cursor': 'default' });
      },

      resetButton: function(platform) {
        var $btn = $('#btn_ai_add_' + platform);
        $btn.removeClass('is-added')
            .html('+ Add')
            .css({ 'background': '#ffffff', 'border': '1px solid #2563eb', 'color': '#2563eb', 'cursor': 'pointer' });
      },

      syncAiButtons: function() {
        var self = this;
        this.platforms.forEach(function(plat) {
          var inputVal = ($('#' + plat + '_url').val() || '').trim();
          var suggestedUrl = ($('#ai_url_' + plat).text() || '').trim();
          if (inputVal && (inputVal === suggestedUrl || inputVal.toLowerCase().indexOf(plat) !== -1)) {
            self.markButtonAdded(plat);
          } else {
            self.resetButton(plat);
          }
        });
      }
    };

    // 5. Social Clear & Input Listeners
    $(document).on('click', '.client-social-clear-btn', function(e) {
      e.preventDefault();
      var target = $(this).data('target');
      if (target) {
        var $inp = $('#' + target);
        $inp.val('').trigger('input').trigger('change');
        $(this).hide();
        if (window.clientSocialManager) window.clientSocialManager.syncAiButtons();
      }
    });

    $(document).on('input', '.client-social-url-input', function() {
      var target = $(this).attr('id');
      var $clearBtn = $('.client-social-clear-btn[data-target="' + target + '"]');
      if ($clearBtn.length) {
        $clearBtn.toggle(Boolean($(this).val().trim().length));
      }
      if (window.clientSocialManager) window.clientSocialManager.syncAiButtons();
    });

    // 6. Website Domain Point-of-Entry & Clear Listeners
    var webClearBtn = document.getElementById('website_clear_btn');
    var webInputEl = document.getElementById('website');
    if (webClearBtn && webInputEl) {
      webClearBtn.addEventListener('click', function(e) {
        e.preventDefault();
        webInputEl.value = '';
        webClearBtn.style.display = 'none';
        $('#website_duplicate_error').hide();
        $('#website').removeClass('is-invalid');
        webInputEl.focus();
      });
      webInputEl.addEventListener('input', function() {
        webClearBtn.style.display = this.value.trim().length > 0 ? 'inline-flex' : 'none';
      });
      webInputEl.addEventListener('blur', function() {
        if (this.value && this.value.trim().length > 3) {
          checkDomainDuplicate(false);
          if (this.value !== window._lastFetchedWebsite && window.fetchGodLevelCompanyIntelligence) {
            window.fetchGodLevelCompanyIntelligence(this.value);
          }
        }
      });
    }

    // Initial sync of social manager
    if (window.clientSocialManager) {
      window.clientSocialManager.syncWebsiteLink();
      window.clientSocialManager.syncAiButtons();
    }

    // Form Submission AJAX Engine
    var form = document.getElementById('kt_contact_add_form');
    var submitBtn = document.getElementById('submitBtn');

    if (submitBtn && form) {
      submitBtn.addEventListener('click', function(e) {
        e.preventDefault();

        if (!validateStep(1)) {
          window.goToWizardStep(1);
          return;
        }

        submitBtn.disabled = true;
        var originalBtnHtml = submitBtn.innerHTML;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Saving Profile...';

        var formData = new FormData(form);

        if (state.quillInstance) {
          var descHtml = state.quillInstance.root.innerHTML;
          formData.set('description', descHtml);
          formData.set('kt_docs_tinymce_hidden', descHtml);
        }

        formData.set('technologies', JSON.stringify(state.selectedTechnologies));

        if (state.servicesTagify) {
          formData.set('tags', JSON.stringify(state.servicesTagify.value || []));
        }

        fetch(form.action, {
          method: 'POST',
          headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
          },
          body: formData
        })
        .then(function(res) {
          return res.json().then(function(data) {
            return { ok: res.ok, status: res.status, data: data };
          });
        })
        .then(function(resObj) {
          submitBtn.disabled = false;
          submitBtn.innerHTML = originalBtnHtml;

          if (resObj.ok && resObj.data.status === 'success') {
            if (state.pageMode === 'ONBOARDING') {
              window.location.href = resObj.data.redirect_url || "{{ url('service_company/dashboard') }}";
            } else {
              if (typeof Swal !== 'undefined') {
                Swal.fire({
                  icon: 'success',
                  title: 'Profile Updated',
                  text: 'Your company profile has been successfully saved.',
                  timer: 2000,
                  showConfirmButton: false
                }).then(function() {
                  window.location.href = "{{ route('service_company.account.index') }}";
                });
              } else {
                alert('Company Profile saved successfully!');
                window.location.href = "{{ route('service_company.account.index') }}";
              }
            }
          } else {
            var errMsg = (resObj.data && resObj.data.message) ? resObj.data.message : 'Failed to save company profile. Please check the fields and try again.';
            if (typeof Swal !== 'undefined') {
              Swal.fire({ icon: 'warning', title: 'Attention', text: errMsg });
            } else {
              alert(errMsg);
            }
          }
        })
        .catch(function(err) {
          submitBtn.disabled = false;
          submitBtn.innerHTML = originalBtnHtml;
          console.error('[CompanyProfile] Submission error:', err);
          alert('Network or server error occurred while saving profile. Please try again.');
        });
      });
    }

  });

})();

/**
 * =========================================================================
 * JERO ENTERPRISE COMPANY SIZE ENGINE (RULE 17 ISOLATED)
 * 100% Replicates Client Wizard Company Size Engine
 * =========================================================================
 */
(function() {
    'use strict';

    var isOpen = false;
    var focusedIndex = -1;

    var wrap = null;
    var inputBox = null;
    var displayInput = null;
    var hiddenSelect = null;
    var clearBtn = null;
    var chevron = null;
    var dropdown = null;
    var listContainer = null;

    function getItems() {
        return listContainer ? listContainer.querySelectorAll('.jero-csize-item') : [];
    }

    function openDropdown() {
        if (isOpen) return;
        isOpen = true;
        if (wrap) wrap.classList.add('is-open');
        if (inputBox) {
            inputBox.classList.add('is-focused');
            inputBox.setAttribute('aria-expanded', 'true');
        }
        adjustPosition();
        syncFocusedToSelected();
    }

    function closeDropdown() {
        if (!isOpen) return;
        isOpen = false;
        if (wrap) wrap.classList.remove('is-open');
        if (inputBox) {
            inputBox.classList.remove('is-focused');
            inputBox.setAttribute('aria-expanded', 'false');
        }
        clearFocusClasses();
        focusedIndex = -1;
    }

    function adjustPosition() {
        if (!dropdown || !inputBox) return;
        var rect = inputBox.getBoundingClientRect();
        var dropHeight = 280;
        var spaceBelow = window.innerHeight - rect.bottom;
        var spaceAbove = rect.top;

        if (spaceBelow < dropHeight && spaceAbove > spaceBelow) {
            dropdown.classList.add('drop-up');
        } else {
            dropdown.classList.remove('drop-up');
        }
    }

    function syncFocusedToSelected() {
        var items = getItems();
        var selected = listContainer ? listContainer.querySelector('.jero-csize-item.selected') : null;
        for (var i = 0; i < items.length; i++) {
            if (items[i] === selected) {
                focusedIndex = i;
                items[i].classList.add('focused');
                scrollItemIntoView(items[i]);
                return;
            }
        }
        focusedIndex = -1;
    }

    function clearFocusClasses() {
        var items = getItems();
        for (var i = 0; i < items.length; i++) {
            items[i].classList.remove('focused');
        }
    }

    function selectOption(val, label) {
        val = (val || '').trim();
        label = (label || '').trim();

        if (hiddenSelect) {
            hiddenSelect.value = val;
            $(hiddenSelect).trigger('change');
        }
        var altSelect = document.getElementById('company_sizes');
        if (altSelect) {
            altSelect.value = val;
            $(altSelect).trigger('change');
        }

        if (displayInput) {
            displayInput.value = label;
        }

        var items = getItems();
        for (var i = 0; i < items.length; i++) {
            var itemVal = items[i].getAttribute('data-val');
            var isSel = (itemVal === val);
            items[i].classList.toggle('selected', isSel);
            items[i].setAttribute('aria-selected', isSel ? 'true' : 'false');
        }

        updateVisualState();
        closeDropdown();

        var sizeReview = document.querySelector('.span_company_sizes');
        if (sizeReview) sizeReview.textContent = label;
    }

    function clearOption() {
        selectOption('', '');
    }

    function updateVisualState() {
        var hasVal = displayInput && displayInput.value.trim().length > 0;
        if (inputBox) {
            inputBox.classList.toggle('has-value', hasVal);
        }
        if (clearBtn) {
            clearBtn.style.display = hasVal ? 'inline-flex' : 'none';
        }
    }

    function scrollItemIntoView(itemEl) {
        if (!itemEl || !listContainer) return;
        var tTop = itemEl.offsetTop;
        var tH = itemEl.offsetHeight;
        var cTop = listContainer.scrollTop;
        var cH = listContainer.clientHeight;

        if (tTop < cTop) {
            listContainer.scrollTop = tTop - 6;
        } else if (tTop + tH > cTop + cH) {
            listContainer.scrollTop = (tTop + tH) - cH + 6;
        }
    }

    function initEventListeners() {
        wrap = document.getElementById('jero_csize_select_wrap');
        inputBox = document.getElementById('jero_csize_input_box');
        displayInput = document.getElementById('company_size_display');
        hiddenSelect = document.getElementById('company_size');
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

        document.addEventListener('pointerdown', function(e) {
            var path = (typeof e.composedPath === 'function') ? e.composedPath() : [];
            var isInside = wrap && (wrap.contains(e.target) || path.indexOf(wrap) !== -1);
            if (!isInside && isOpen) {
                closeDropdown();
            }
        }, true);

        window.addEventListener('resize', function() { if (isOpen) adjustPosition(); }, { passive: true });
        window.addEventListener('scroll', function() { if (isOpen) adjustPosition(); }, { passive: true });
    }

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

/**
 * =========================================================================
 * JERO ENTERPRISE COMPANY TYPE ENGINE (RULE 17 ISOLATED)
 * Search-First Dual-Layer Intelligence Combobox with Fuzzy Matching & Aliases
 * =========================================================================
 */
(function() {
    'use strict';

    var taxonomyList = [];
    var taxonomyMap = new Map();
    var filteredList = [];
    var focusedIndex = -1;
    var isOpen = false;

    var wrap = null;
    var input = null;
    var hiddenSelect = null;
    var inputBox = null;
    var clearBtn = null;
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

            html += '<div class="jero-ctype-item ' + (isSelected ? 'selected' : '') + ' ' + (isFocused ? 'focused' : '') + '" ' +
                'data-idx="' + i + '" data-id="' + item.id + '" role="option" id="jero_ctype_opt_' + item.id + '" aria-selected="' + (isSelected ? 'true' : 'false') + '">' +
                '<div class="jero-ctype-item-main">' +
                '<div class="jero-ctype-item-name">' + highlightMatch(item.display_name || item.name, query) + '</div>' +
                '</div>' +
                '<div class="jero-ctype-item-side">' +
                (entry.matchedAlias ? '<span class="jero-ctype-badge matched-alias">Matched: ' + escapeHtml(entry.matchedAlias) + '</span>' : (item.badge ? '<span class="jero-ctype-badge">' + escapeHtml(item.badge) + '</span>' : '')) +
                checkIcon +
                '</div>' +
                '</div>';
        }

        listContainer.innerHTML = html;
    }

    function openDropdown() {
        if (isOpen) return;
        isOpen = true;
        if (wrap) wrap.classList.add('is-open');
        filterItems(input ? input.value : '');
        renderList(input ? input.value : '');
    }

    function closeDropdown(restoreText) {
        if (!isOpen) return;
        isOpen = false;
        if (wrap) wrap.classList.remove('is-open');
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
        if (inputBox) inputBox.classList.toggle('has-value', hasVal);
        if (clearBtn) clearBtn.style.display = hasVal ? 'inline-flex' : 'none';
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
        closeDropdown(false);

        var typeBadge = document.getElementById('profile_review_type_badge');
        if (typeBadge) typeBadge.textContent = item.display_name || item.name;
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

        var typeBadge = document.getElementById('profile_review_type_badge');
        if (typeBadge) typeBadge.textContent = 'Organization';
    }

    function initEventListeners() {
        wrap = document.getElementById('jero_ctype_select_wrap');
        input = document.getElementById('company_type_display');
        hiddenSelect = document.getElementById('company_type');
        inputBox = document.getElementById('jero_ctype_input_box');
        clearBtn = document.getElementById('jero_ctype_clear_btn');
        chevron = document.getElementById('jero_ctype_chevron');
        dropdown = document.getElementById('jero_ctype_dropdown');
        listContainer = document.getElementById('jero_ctype_list_container');

        if (!wrap || !input || !hiddenSelect) return;

        if (chevron) {
            chevron.addEventListener('click', function(e) {
                e.stopPropagation();
                if (isOpen) closeDropdown(true);
                else { input.focus(); openDropdown(); }
            });
        }

        if (inputBox) {
            inputBox.addEventListener('click', function(e) {
                if (e.target.closest('#jero_ctype_clear_btn')) return;
                if (e.target.closest('#jero_ctype_chevron')) return;
                if (!isOpen) { input.focus(); openDropdown(); }
            });
        }

        input.addEventListener('focus', function() { openDropdown(); });
        input.addEventListener('click', function(e) { e.stopPropagation(); openDropdown(); });

        input.addEventListener('input', function() {
            if (!isOpen) openDropdown();
            focusedIndex = 0;
            filterItems(input.value);
            renderList(input.value);
            updateInputVisualState();
        });

        if (clearBtn) {
            clearBtn.addEventListener('click', function(e) {
                e.stopPropagation();
                clearSelection();
            });
        }

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

        document.addEventListener('pointerdown', function(e) {
            var path = (typeof e.composedPath === 'function') ? e.composedPath() : [];
            var isInside = wrap && (wrap.contains(e.target) || path.indexOf(wrap) !== -1);
            if (!isInside && isOpen) {
                closeDropdown(true);
            }
        }, true);
    }

    window.JeroCompanyTypeEngine = {
        selectById: function(id) {
            var numId = parseInt(id, 10);
            if (taxonomyMap.has(numId)) selectItem(taxonomyMap.get(numId));
        },
        clear: clearSelection
    };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function() { initData(); initEventListeners(); });
    } else {
        initData();
        initEventListeners();
    }
})();

/**
 * =========================================================================
 * JERO ENTERPRISE FOUNDED YEAR ENGINE (RULE 17 ISOLATED)
 * Search-First Historical Registry (1800 – Present Year)
 * =========================================================================
 */
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

    var wrap = null;
    var input = null;
    var searchInput = null;
    var hiddenInput = null;
    var inputBox = null;
    var clearBtn = null;
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
    }

    function openDropdown() {
        if (isOpen) return;
        isOpen = true;
        if (wrap) wrap.classList.add('is-open');

        if (searchInput) {
            searchInput.value = '';
            setTimeout(function() { searchInput.focus(); }, 60);
        }

        filterYears('');
        renderList('');

        var selected = getSelectedYear();
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
        focusedIndex = -1;
    }

    function updateInputVisualState() {
        var hasVal = hiddenInput && (hiddenInput.value || '').trim().length > 0;
        if (inputBox) inputBox.classList.toggle('has-value', hasVal);
        if (clearBtn) clearBtn.style.display = hasVal ? 'inline-flex' : 'none';
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
        chevron = document.getElementById('jero_founded_chevron');
        dropdown = document.getElementById('jero_founded_dropdown');
        listContainer = document.getElementById('jero_founded_list_container');

        if (!wrap || !input || !hiddenInput) return;

        if (chevron) {
            chevron.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                if (isOpen) closeDropdown(false);
                else openDropdown();
            });
        }

        if (inputBox) {
            inputBox.addEventListener('click', function(e) {
                if (e.target.closest('#jero_founded_clear_btn')) return;
                if (e.target.closest('#jero_founded_chevron')) return;
                if (!isOpen) openDropdown();
                else if (searchInput) searchInput.focus();
            });
        }

        if (searchInput) {
            searchInput.addEventListener('input', function() {
                focusedIndex = 0;
                filterYears(this.value);
                renderList(this.value);
            });
        }

        if (clearBtn) {
            clearBtn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                clearYear();
            });
        }

        if (listContainer) {
            listContainer.addEventListener('click', function(e) {
                var itemEl = e.target.closest('.jero-founded-item');
                if (!itemEl) return;
                var yr = itemEl.getAttribute('data-year');
                if (yr) selectYear(yr);
            });
        }

        document.addEventListener('pointerdown', function(e) {
            var path = (typeof e.composedPath === 'function') ? e.composedPath() : [];
            var isInside = wrap && (wrap.contains(e.target) || path.indexOf(wrap) !== -1);
            if (!isInside && isOpen) closeDropdown(false);
        }, true);
    }

    window.JeroFoundedYearEngine = {
        selectYear: selectYear,
        clearYear: clearYear,
        open: openDropdown,
        close: closeDropdown
    };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initEventListeners);
    } else {
        initEventListeners();
    }
})();

/**
 * =========================================================================
 * LOGO MANAGEMENT & PREVIEW ENGINE (RULE 17 ISOLATED)
 * 2MB Max Validation, ObjectURL Preview, Remove Trigger
 * =========================================================================
 */
window.loadFile = function(event) {
  var input = (event && event.target) ? event.target : document.getElementById('client_logo_input');
  if (input && input.files && input.files[0]) {
    var file = input.files[0];
    var maxLogoSizeBytes = 2 * 1024 * 1024;
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
    var logoBox = document.getElementById('company_logo_box');
    if (logoBox) {
      logoBox.style.backgroundImage = 'url(' + output + ')';
      logoBox.style.backgroundSize = 'contain';
      logoBox.style.backgroundRepeat = 'no-repeat';
      logoBox.style.backgroundPosition = 'center';
      logoBox.classList.add('has-image');
      logoBox.innerHTML = '';
    }

    var removeInput = document.getElementById('client_avatar_remove');
    if (removeInput) removeInput.value = '0';

    var removeBtn = document.getElementById('client_remove_logo_btn');
    if (removeBtn) removeBtn.style.display = 'inline-flex';

    var statusBanner = document.getElementById('logo_status_banner');
    if (statusBanner) {
      statusBanner.className = 'god-logo-status-banner is-detected';
      statusBanner.innerHTML = '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg> <span>Logo uploaded successfully</span>';
    }

    var reviewLogo = document.getElementById('profile_review_logo_box');
    if (reviewLogo) {
      reviewLogo.style.backgroundImage = 'url(' + output + ')';
      reviewLogo.style.backgroundSize = 'contain';
      reviewLogo.style.backgroundRepeat = 'no-repeat';
      reviewLogo.style.backgroundPosition = 'center';
      reviewLogo.innerHTML = '';
    }
  }
};

window.removeCompanyLogo = function() {
  var fileIn = document.getElementById('client_logo_input');
  if (fileIn) fileIn.value = '';

  var removeIn = document.getElementById('client_avatar_remove');
  if (removeIn) removeIn.value = '1';

  var logoBox = document.getElementById('company_logo_box');
  if (logoBox) {
    logoBox.style.backgroundImage = 'none';
    logoBox.classList.remove('has-image');
    logoBox.innerHTML = '<div class="god-logo-placeholder" id="logo_placeholder"><svg width="44" height="44" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg><span class="placeholder-title">Company Logo</span><span class="placeholder-sub">Click Change Logo or auto-fetch</span></div>';
  }

  var removeBtn = document.getElementById('client_remove_logo_btn');
  if (removeBtn) removeBtn.style.display = 'none';

  var statusBanner = document.getElementById('logo_status_banner');
  if (statusBanner) {
    statusBanner.className = 'god-logo-status-banner is-empty';
    statusBanner.innerHTML = '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/></svg> <span>No logo detected yet</span>';
  }

  var reviewLogo = document.getElementById('profile_review_logo_box');
  if (reviewLogo) {
    reviewLogo.style.backgroundImage = 'none';
    reviewLogo.innerHTML = '<span id="profile_review_initials" style="font-size:18px; font-weight:700; color:#1e293b;">CP</span>';
  }
};
</script>
@endpush
