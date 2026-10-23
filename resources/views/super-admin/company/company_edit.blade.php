@extends('layouts.superadmin_layout')

@section('header_title')
<div class="page-title sa-page-title-stack my-0">
    <h1 class="d-flex align-items-center text-gray-900 fw-bold fs-3 my-0">Edit Company</h1>
    <span class="sa-header-sub" id="sa_top_header_sub">Edit company details, locations, and verification</span>
</div>
@endsection

@push('head-script')
<meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
<meta http-equiv="Pragma" content="no-cache" />
<meta http-equiv="Expires" content="0" />

<!-- Add Metronic-7 CSS Bundle matching service_company/client/create reference -->
<link href="{{ asset('demo/v7/css/wizard-1.css') }}<?php echo TM_VERSION; ?>" rel="stylesheet" type="text/css" />
<link href="{{ asset('demo/v7/css/plugins.bundle.css') }}<?php echo TM_VERSION; ?>" rel="stylesheet" type="text/css" />
<link href="{{ asset('demo/v7/css/prismjs.bundle.css') }}<?php echo TM_VERSION; ?>" rel="stylesheet" type="text/css" />
<link href="{{ asset('demo/v7/css/style.bundle.css') }}<?php echo TM_VERSION; ?>" rel="stylesheet" type="text/css" />
<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
<link href="https://unpkg.com/@yaireo/tagify/dist/tagify.css" rel="stylesheet" type="text/css" />

<style>
/* =========================================================================
   SUPER ADMIN COMPANY EDIT — 100% EXACT DESIGN SYSTEM PARITY WITH CLIENT CREATE
   Reference: service_company/clients/create (media_1792068852883 & media_1792566743106)
   Enterprise Typography, Design Tokens, Poppins Font, 15.6" Vertical Fit
   ========================================================================= */
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
  --radius: 10px;
  --radius-sm: 6px;
  --radius-lg: 12px;
  --transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);

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
  font-family: Poppins, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif !important;
  background-color: var(--client-page) !important;
}

/* Top Navigation Header Title Stack & Subtitle (Isolated) */
#kt_header_nav .page-title.sa-page-title-stack {
  display: flex !important;
  flex-direction: column !important;
  align-items: flex-start !important;
  justify-content: center !important;
}

#kt_header_nav .page-title.sa-page-title-stack h1 {
  font-family: 'Poppins', sans-serif !important;
  font-size: 1.35rem !important;
  font-weight: 600 !important;
  color: #181c32 !important;
  line-height: 1.2 !important;
  margin: 0 !important;
}

#kt_header_nav .page-title.sa-page-title-stack .sa-header-sub {
  font-size: 0.785rem !important;
  color: #7E8299 !important;
  font-weight: 500 !important;
  line-height: 1.2 !important;
  margin-top: 2px !important;
}

@media (max-width: 991.98px) {
  #kt_header_nav .page-title.sa-page-title-stack h1 {
    font-size: 1.1rem !important;
  }
  #kt_header_nav .page-title.sa-page-title-stack .sa-header-sub {
    font-size: 0.7rem !important;
  }
}

#kt_header {
  border-bottom: 1px solid #eff2f5 !important;
}

/* =========================================================================
   HEADER TOOLBAR ICON CLEANUP (Isolated to Super Admin Edit Company)
   Remove redundant header icons (5 red-arrowed icons):
   1. Left search toggle circle next to page title (#kt_header_nav search)
   2. Right search toggle circle (#kt_header topbar search)
   3. Quick links launcher (4-square grid icon)
   4. Chat drawer toggle (with blinking green notification dot)
   5. Notification drawer toggle (4-diamond icon)
   Strictly preserve User Profile dropdown avatar (#kt_header_user_menu_toggle)
   ========================================================================= */
#kt_header_nav .header-menu,
#kt_header_nav #kt_header_search,
#kt_header_nav #kt_header_search_toggle,
#kt_header .d-none,
#kt_header #kt_header_search,
#kt_header #kt_header_search_toggle,
#kt_header #kt_activities_toggle,
#kt_header #kt_drawer_chat_toggle,
#kt_header .d-flex.align-items-stretch.flex-shrink-0 > .d-flex.align-items-stretch.flex-shrink-0 > div:not(#kt_header_user_menu_toggle):not(#kt_header_menu_mobile_toggle) {
  display: none !important;
}

/* =========================================================================
   TOP GAP COLLAPSE & VIEWPORT SPACING MANAGEMENT (Eliminate Metronic 55px whitespace)
   ========================================================================= */
#kt_body {
  --kt-toolbar-height: 0px !important;
  --kt-toolbar-height-tablet-and-mobile: 0px !important;
}

.header-fixed.toolbar-fixed #kt_wrapper,
.header-fixed #kt_wrapper,
.toolbar-fixed #kt_wrapper,
#kt_wrapper {
  padding-top: calc(65px + 5px) !important;
  min-height: 100vh !important;
  display: flex !important;
  flex-direction: column !important;
  box-sizing: border-box !important;
}

@media (max-width: 991.98px) {
  .header-tablet-and-mobile-fixed.toolbar-tablet-and-mobile-fixed #kt_wrapper,
  .header-tablet-and-mobile-fixed #kt_wrapper,
  #kt_wrapper {
    padding-top: calc(55px + 5px) !important;
  }
}

#kt_toolbar, #kt_footer {
  display: none !important;
  height: 0 !important;
  padding: 0 !important;
  margin: 0 !important;
}

#kt_content,
.content {
  background: var(--client-page) !important;
  padding-top: 0 !important;
  padding-bottom: 12px !important;
  margin-top: 0 !important;
  flex: 1 1 auto !important;
  display: flex !important;
  flex-direction: column !important;
  min-height: calc(100vh - 65px) !important;
}

#kt_content > .d-flex,
#kt_content .container-fluid {
  padding-top: 0 !important;
  margin-top: 0 !important;
}

/* =========================================================================
   TOP UNIFIED STICKY HEADER & 4-STEP STEPPER BAR (Pixel-Matched to Reference)
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
  top: calc(65px + 5px) !important;
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
  display: none !important;
  flex: 0 0 auto !important;
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
  justify-content: flex-start !important;
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

/* Dynamic button visibility by body class (Exact match to Client Create) */
#client_top_close_btn { display: inline-flex !important; }
#client_top_back_btn { display: none !important; }
#client_top_next_btn { display: inline-flex !important; }
#submitBtn { display: none !important; }

body.on-step-1 #client_top_close_btn { display: inline-flex !important; }
body.on-step-1 #client_top_back_btn { display: none !important; }
body.on-step-1 #client_top_next_btn { display: inline-flex !important; }
body.on-step-1 #submitBtn { display: none !important; }

body.on-step-2 #client_top_close_btn,
body.on-step-3 #client_top_close_btn { display: none !important; }
body.on-step-2 #client_top_back_btn,
body.on-step-3 #client_top_back_btn { display: inline-flex !important; }
body.on-step-2 #client_top_next_btn,
body.on-step-3 #client_top_next_btn { display: inline-flex !important; }
body.on-step-2 #submitBtn,
body.on-step-3 #submitBtn { display: none !important; }

body.on-step-4 #client_top_close_btn { display: none !important; }
body.on-step-4 #client_top_back_btn { display: inline-flex !important; }
body.on-step-4 #client_top_next_btn { display: none !important; }
body.on-step-4 #submitBtn { display: inline-flex !important; }

/* =========================================================================
   TWO-COLUMN FORM GRIDS (70:30, 58:42, 60:40 Screen Fit)
   ========================================================================= */
.client-step-pane {
  display: none;
}
.client-step-pane.is-active {
  display: flex !important;
  flex-direction: column !important;
  flex: 1 1 auto !important;
  min-height: calc(100vh - 170px) !important;
}

#kt_company_form {
  display: flex !important;
  flex-direction: column !important;
  flex: 1 1 auto !important;
  width: 100% !important;
}

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

.client-information-layout {
  display: grid !important;
  width: 100% !important;
  grid-template-columns: minmax(0, 7.2fr) minmax(0, 3.8fr) !important;
  gap: 16px !important;
  align-items: stretch !important;
  box-sizing: border-box !important;
}

.client-description-layout {
  display: grid !important;
  width: 100% !important;
  grid-template-columns: minmax(0, 1.35fr) minmax(0, 1fr) !important;
  gap: 16px !important;
  align-items: stretch !important;
  box-sizing: border-box !important;
  flex: 1 1 auto !important;
  min-height: calc(100vh - 170px) !important;
}

.client-address-layout {
  display: grid !important;
  width: 100% !important;
  grid-template-columns: minmax(0, 3fr) minmax(0, 2fr) !important;
  gap: 16px !important;
  align-items: stretch !important;
  box-sizing: border-box !important;
  flex: 1 1 auto !important;
  min-height: calc(100vh - 170px) !important;
}

@media (max-width: 992px) {
  .god-level-company-layout,
  .client-information-layout,
  .client-description-layout,
  .client-address-layout {
    grid-template-columns: 1fr !important;
    gap: 14px !important;
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

.god-logo-column {
  height: 100% !important;
  display: flex !important;
  flex-direction: column !important;
  justify-content: space-between !important;
  gap: 14px !important;
  box-sizing: border-box !important;
}
.god-logo-column .client-helper-card {
  margin-top: auto !important;
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

.god-card-header {
  display: flex !important;
  align-items: center !important;
  gap: 12px !important;
  margin-bottom: 16px !important;
  padding-bottom: 12px !important;
  border-bottom: 1px solid #f1f5f9 !important;
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
.god-card-header-icon.yellow-icon {
  background: #fef9c3 !important;
  color: #ca8a04 !important;
}
.god-card-header-icon.purple-icon {
  background: #faf5ff !important;
  color: #9333ea !important;
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
  margin-bottom: 5px !important;
}

.god-field-label {
  font-size: 12px !important;
  font-weight: 600 !important;
  color: #0f172a !important;
  margin: 0 !important;
  line-height: 1.25 !important;
  font-family: Poppins, sans-serif !important;
}

/* =========================================================================
   INPUT CONTROLS & GOD-LEVEL FETCH ROW
   ========================================================================= */
.god-input-box {
  display: flex !important;
  align-items: center !important;
  background: #ffffff !important;
  border: 1px solid #cbd5e1 !important;
  border-radius: 8px !important;
  padding: 0 12px !important;
  height: 40px !important;
  min-height: 40px !important;
  position: relative !important;
  transition: border-color 0.15s ease, box-shadow 0.15s ease !important;
  box-sizing: border-box !important;
  width: 100% !important;
  gap: 8px !important;
}
.god-input-box:focus-within {
  border-color: #ffd428 !important;
  box-shadow: 0 0 0 3px rgba(255, 212, 40, 0.22) !important;
}
.god-input-box.is-invalid {
  border-color: #ef4444 !important;
  box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.18) !important;
}
.god-input-box.is-readonly {
  background: #f8fafc !important;
  border-color: #e2e8f0 !important;
  cursor: default !important;
}
.god-input-box.is-readonly:focus-within {
  border-color: #cbd5e1 !important;
  box-shadow: none !important;
}
.god-input-box.is-readonly .god-native-input {
  color: #475569 !important;
  cursor: default !important;
  font-weight: 500 !important;
  user-select: all !important;
}
.god-input-right-lock {
  display: inline-flex !important;
  align-items: center !important;
  justify-content: center !important;
  flex-shrink: 0 !important;
  color: #94a3b8 !important;
  cursor: default !important;
  margin-left: auto !important;
}

.god-input-left-icon {
  color: #94a3b8 !important;
  display: inline-flex !important;
  align-items: center !important;
  justify-content: center !important;
  margin-right: 0 !important;
  flex-shrink: 0 !important;
  width: 16px !important;
  height: 16px !important;
}
.god-input-left-icon svg {
  width: 15px !important;
  height: 15px !important;
}

.god-native-input {
  border: none !important;
  outline: none !important;
  background: transparent !important;
  color: #0f172a !important;
  font-size: 12.5px !important;
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

.god-website-control-row {
  display: flex !important;
  align-items: stretch !important;
  gap: 8px !important;
  height: 40px !important;
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
  font-size: 12px !important;
  font-weight: 700 !important;
  border-radius: 8px !important;
  padding: 0 14px !important;
  height: 40px !important;
  display: inline-flex !important;
  align-items: center !important;
  justify-content: center !important;
  gap: 6px !important;
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
  display: none;
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
.is-open .jero-dropdown-chevron {
  transform: rotate(180deg) !important;
  color: #2563eb !important;
}

/* =========================================================================
   SEARCHABLE COMBOBOX & DROPDOWN CONTAINERS
   ========================================================================= */
.jero-ind-select-wrap,
.jero-csize-select-wrap,
.jero-ctype-select-wrap,
.jero-founded-select-wrap {
  position: relative !important;
  width: 100% !important;
}

.jero-dropdown-menu-custom {
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
  max-height: 290px !important;
  box-sizing: border-box !important;
  animation: jeroDropdownFadeIn 0.14s cubic-bezier(0.16, 1, 0.3, 1) !important;
}
.is-open .jero-dropdown-menu-custom {
  display: flex !important;
}

@keyframes jeroDropdownFadeIn {
  from { opacity: 0; transform: translateY(-4px); }
  to { opacity: 1; transform: translateY(0); }
}

.jero-dropdown-list-container {
  max-height: 280px !important;
  overflow-y: auto !important;
  padding: 6px 0 !important;
  scrollbar-width: thin !important;
  scrollbar-color: #cbd5e1 #f8fafc !important;
}
.jero-dropdown-list-container::-webkit-scrollbar { width: 5px; }
.jero-dropdown-list-container::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 3px; }

.jero-dropdown-item {
  display: flex !important;
  align-items: center !important;
  justify-content: space-between !important;
  padding: 7px 12px !important;
  margin: 1px 6px !important;
  border-radius: 7px !important;
  cursor: pointer !important;
  transition: background 0.1s ease, color 0.1s ease !important;
  font-size: 12.5px !important;
  font-weight: 500 !important;
  color: #1e293b !important;
}
.jero-dropdown-item:hover {
  background: #f1f5f9 !important;
}
.jero-dropdown-item.is-selected {
  background: #eff6ff !important;
  font-weight: 600 !important;
  color: #1d4ed8 !important;
}

/* =========================================================================
   Search-First Company Type Intelligence Combobox (Rule 17 Isolated)
   ========================================================================= */
.jero-ctype-select-wrap {
  position: relative !important;
  width: 100% !important;
}
.jero-ctype-input-box {
  position: relative !important;
  display: flex !important;
  align-items: center !important;
  cursor: pointer !important;
}
.jero-ctype-input {
  cursor: pointer !important;
  padding-right: 64px !important;
  font-family: 'Poppins', sans-serif !important;
  font-size: 13px !important;
  font-weight: 500 !important;
  color: #101a46 !important;
}
.jero-ctype-input:focus {
  cursor: text !important;
}
.jero-ctype-clear-btn {
  position: absolute !important;
  right: 34px !important;
  top: 50% !important;
  transform: translateY(-50%) !important;
  width: 20px !important;
  height: 20px !important;
  border-radius: 50% !important;
  background: #e2e8f0 !important;
  color: #64748b !important;
  border: none !important;
  display: none;
  align-items: center !important;
  justify-content: center !important;
  cursor: pointer !important;
  transition: all 0.15s ease !important;
  padding: 0 !important;
  z-index: 3 !important;
}
.jero-ctype-clear-btn:hover {
  background: #cbd5e1 !important;
  color: #0f172a !important;
}
.jero-ctype-chevron {
  position: absolute !important;
  right: 12px !important;
  top: 50% !important;
  transform: translateY(-50%) !important;
  display: flex !important;
  align-items: center !important;
  justify-content: center !important;
  color: #64748b !important;
  pointer-events: auto !important;
  cursor: pointer !important;
  z-index: 2 !important;
  transition: transform 0.2s ease, color 0.2s ease !important;
}
.jero-ctype-select-wrap.is-open .jero-ctype-chevron {
  transform: translateY(-50%) rotate(180deg) !important;
  color: #2563eb !important;
}
.jero-ctype-dropdown {
  position: absolute !important;
  top: calc(100% + 5px) !important;
  left: 0 !important;
  right: 0 !important;
  z-index: 1080 !important;
  background: #ffffff !important;
  border: 1px solid #cbd5e1 !important;
  border-radius: 10px !important;
  box-shadow: 0 12px 32px -4px rgba(15, 23, 42, 0.12), 0 4px 12px -2px rgba(15, 23, 42, 0.08) !important;
  overflow: hidden !important;
  display: none;
  flex-direction: column !important;
  max-height: min(320px, calc(100vh - 120px)) !important;
  animation: jeroDropdownFadeIn 0.15s ease-out !important;
  box-sizing: border-box !important;
}
.jero-ctype-select-wrap.is-open .jero-ctype-dropdown,
.jero-ctype-dropdown.is-open {
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
  padding: 6px 0 !important;
  box-sizing: border-box !important;
}
.jero-ctype-list-container::-webkit-scrollbar {
  width: 6px !important;
}
.jero-ctype-list-container::-webkit-scrollbar-thumb {
  background: #cbd5e1 !important;
  border-radius: 9999px !important;
}
.jero-ctype-item {
  display: flex !important;
  align-items: center !important;
  justify-content: flex-start !important;
  padding: 8px 16px !important;
  cursor: pointer !important;
  transition: background 0.12s ease, color 0.12s ease !important;
  border-bottom: 1px solid #f8fafc !important;
  box-sizing: border-box !important;
  min-height: 36px !important;
}
.jero-ctype-item:last-child {
  border-bottom: none !important;
}
.jero-ctype-item:hover,
.jero-ctype-item.focused {
  background: #0d6efd !important;
  color: #ffffff !important;
}
.jero-ctype-item:hover .jero-ctype-item-name,
.jero-ctype-item.focused .jero-ctype-item-name {
  color: #ffffff !important;
}
.jero-ctype-item.selected {
  background: #f1f5f9 !important;
  font-weight: 600 !important;
}
.jero-ctype-item.selected:hover {
  background: #0d6efd !important;
  color: #ffffff !important;
}
.jero-ctype-item-main {
  display: flex !important;
  align-items: center !important;
  flex: 1 !important;
  min-width: 0 !important;
}
.jero-ctype-item-name {
  font-size: 13px !important;
  font-weight: 500 !important;
  color: #181c32 !important;
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
  font-size: 11px !important;
  color: #64748b !important;
  line-height: 1.3 !important;
}
.jero-ctype-item-aliases {
  font-size: 10.5px !important;
  color: #94a3b8 !important;
  margin-top: 1px !important;
  line-height: 1.25 !important;
}
.jero-ctype-item-side {
  display: flex !important;
  align-items: center !important;
  gap: 8px !important;
  flex-shrink: 0 !important;
  margin-left: 10px !important;
}
.jero-ctype-badge {
  display: inline-flex !important;
  align-items: center !important;
  padding: 2px 7px !important;
  font-size: 10px !important;
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
  padding: 24px 16px !important;
  text-align: center !important;
  font-size: 12.5px !important;
  color: #64748b !important;
}

/* =========================================================================
   LOGO COLUMN & DROPZONE (Step 1 Right Column)
   ========================================================================= */
.god-logo-preview-box {
  border: 1.5px dashed #cbd5e1 !important;
  border-radius: 10px !important;
  background: #ffffff !important;
  padding: 10px !important;
  min-height: 130px !important;
  display: flex !important;
  align-items: center !important;
  justify-content: center !important;
  position: relative !important;
  box-sizing: border-box !important;
}

.god-logo-surface {
  width: 105px !important;
  height: 105px !important;
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
.god-logo-surface.has-image .god-logo-placeholder {
  display: none !important;
}

.god-logo-placeholder {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  gap: 2px;
  color: #94a3b8;
  text-align: center;
  padding: 8px;
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
  background: #fef2f2 !important;
  border: 1px solid #fecaca !important;
  color: #ef4444 !important;
}
.god-remove-logo-btn:hover {
  background: #fee2e2 !important;
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
}
.god-logo-status-banner.is-detected {
  background: #dcfce7 !important;
  color: #15803d !important;
}
.god-logo-status-banner.is-empty {
  background: #f1f5f9 !important;
  color: #64748b !important;
}

/* =========================================================================
   STEP 2: ABOUT THE COMPANY (Quill Editor & Service Offerings)
   ========================================================================= */
.client-description-layout {
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
  .client-description-layout {
    grid-template-columns: 1fr !important;
    gap: 14px !important;
  }
}
.client-description-layout > *,
.client-description-layout > .client-main-column,
.client-description-layout > .client-side-column {
  min-width: 0 !important;
  width: 100% !important;
  max-width: 100% !important;
  box-sizing: border-box !important;
}

.client-desc-card {
  background: #ffffff !important;
  border: 1px solid #e2e8f0 !important;
  border-radius: 12px !important;
  box-shadow: 0 1px 3px rgba(0,0,0,0.03) !important;
  overflow: hidden !important;
}
.client-desc-card > .card-header {
  padding: 12px 18px !important;
  background: #ffffff !important;
  border-bottom: 1px solid #f8fafc !important;
  display: flex !important;
  align-items: center !important;
}
.client-desc-card > .card-body {
  padding: 14px 18px !important;
  background: #ffffff !important;
}

.client-editor-wrap {
  width: 100% !important;
  box-sizing: border-box !important;
}
.client-editor-box {
  border: 1px solid #e2e8f0 !important;
  border-radius: 8px !important;
  background: #ffffff !important;
  overflow: hidden !important;
  transition: border-color 0.15s ease, box-shadow 0.15s ease !important;
}
.client-editor-box:focus-within {
  border-color: #ffd428 !important;
  box-shadow: 0 0 0 3px rgba(255, 212, 40, 0.25) !important;
}
.client-editor-box.is-invalid {
  border-color: #ef4444 !important;
  box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.12) !important;
}

.ql-toolbar.ql-snow {
  border: none !important;
  border-bottom: 1px solid #e2e8f0 !important;
  background: #ffffff !important;
  padding: 6px 10px !important;
  display: flex !important;
  align-items: center !important;
  gap: 4px !important;
  flex-wrap: wrap !important;
}
.ql-container.ql-snow {
  border: none !important;
  font-family: inherit !important;
  font-size: 12.5px !important;
  color: #1e293b !important;
  min-height: 125px !important;
  max-height: 165px !important;
  overflow-y: auto !important;
}
.ql-editor {
  min-height: 125px !important;
  font-size: 12.5px !important;
  line-height: 1.6 !important;
  color: #334155 !important;
  padding: 10px 14px !important;
}
.ql-editor.ql-blank::before {
  color: #94a3b8 !important;
  font-style: normal !important;
  font-size: 12px !important;
}
.ql-snow .ql-stroke { stroke: #475569 !important; }
.ql-snow .ql-fill { fill: #475569 !important; }
.ql-snow button:hover .ql-stroke, .ql-snow .ql-active .ql-stroke { stroke: #2563eb !important; }
.ql-snow button:hover .ql-fill, .ql-snow .ql-active .ql-fill { fill: #2563eb !important; }

.client-char-count {
  font-size: 11px !important;
  font-weight: 500 !important;
  color: #94a3b8 !important;
}

.client-tips-accordion {
  background: #f0f7ff !important;
  border: 1px solid #dbeafe !important;
  border-radius: 10px !important;
  padding: 12px 16px !important;
  margin-top: 12px !important;
  margin-bottom: 0 !important;
}

/* Service Offerings Card */
.client-service-offerings-card {
  background: #ffffff !important;
  border: 1px solid #e2e8f0 !important;
  border-radius: 12px !important;
  box-shadow: 0 1px 3px rgba(0,0,0,0.03) !important;
  overflow: hidden !important;
  margin-bottom: 12px !important;
}
.client-service-offerings-card > .card-header {
  padding: 12px 16px !important;
  background: #ffffff !important;
  border-bottom: 1px solid #f8fafc !important;
}
.client-service-offerings-card > .card-body {
  padding: 14px 16px !important;
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

/* AI-Generated Company Description Card (Lavender) */
.client-ai-assist-card {
  background: #faf8ff !important;
  border: 1px solid #ede9fe !important;
  border-radius: 12px !important;
  padding: 14px 16px !important;
  margin-bottom: 0 !important;
}
.client-ai-suggested-desc {
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
}
#client_apply_desc_suggestion {
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
#client_apply_desc_suggestion:hover {
  background: #5b21b6 !important;
  transform: translateY(-1px) !important;
}
#client_review_edit_desc_btn {
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
#client_review_edit_desc_btn:hover {
  background: #f5f3ff !important;
}

/* =========================================================================
   STEP 3: ADDRESS & LOCATIONS (Exact Enterprise SaaS Standard)
   ========================================================================= */
.client-address-layout {
  display: grid !important;
  width: 100% !important;
  max-width: 100% !important;
  min-width: 0 !important;
  grid-template-columns: minmax(0, 1.45fr) minmax(0, 1fr) !important;
  gap: 16px !important;
  align-items: start !important;
  box-sizing: border-box !important;
}
@media (max-width: 1024px) {
  .client-address-layout {
    grid-template-columns: 1fr !important;
    gap: 14px !important;
  }
}
.client-address-layout > *,
.client-address-layout > .client-main-column,
.client-address-layout > .client-side-column {
  min-width: 0 !important;
  width: 100% !important;
  max-width: 100% !important;
  box-sizing: border-box !important;
}

/* Top Banner */
.client-addr-top-banner {
  background: #f0f7ff !important;
  border: 1px solid #dbeafe !important;
  border-radius: 8px !important;
  padding: 9px 14px !important;
  display: flex !important;
  align-items: center !important;
  gap: 10px !important;
  font-size: 12px !important;
  color: #1e40af !important;
  font-weight: 500 !important;
  line-height: 1.4 !important;
}

/* Primary & Dedicated Section Cards */
.client-section-card {
  background: #ffffff !important;
  border: 1px solid #e2e8f0 !important;
  border-radius: 12px !important;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02) !important;
  overflow: hidden !important;
}
.client-section-card > .card-header {
  background: #ffffff !important;
  border-bottom: 1px solid #f1f5f9 !important;
  padding: 12px 18px !important;
}
.client-section-card > .card-body {
  padding: 14px 18px !important;
}

.client-addr-card-icon-box {
  width: 36px !important;
  height: 36px !important;
  border-radius: 8px !important;
  background: #fef9c3 !important;
  display: inline-flex !important;
  align-items: center !important;
  justify-content: center !important;
  color: #ca8a04 !important;
  flex-shrink: 0 !important;
}

.client-section-heading {
  font-size: 14px !important;
  font-weight: 700 !important;
  color: #0f172a !important;
}
.client-section-copy {
  font-size: 11.5px !important;
  color: #64748b !important;
}

.client-hq-badge {
  background: #fef3c7 !important;
  border: 1px solid #fde68a !important;
  color: #92400e !important;
  font-size: 10px !important;
  font-weight: 700 !important;
  padding: 3px 10px !important;
  border-radius: 9999px !important;
  letter-spacing: 0.3px !important;
}

.client-branch-count-badge {
  background: #f1f5f9 !important;
  color: #475569 !important;
  font-size: 11px !important;
  font-weight: 600 !important;
  padding: 3px 10px !important;
  border-radius: 9999px !important;
  border: 1px solid #e2e8f0 !important;
}

/* Notice Alert */
.client-addr-notice {
  background: #fffdf0 !important;
  border: 1px solid #fef08a !important;
  border-radius: 8px !important;
  padding: 8px 12px !important;
  display: flex !important;
  align-items: center !important;
  gap: 8px !important;
  font-size: 11.5px !important;
  color: #854d0e !important;
  line-height: 1.4 !important;
}

/* Sub-card Architecture */
.client-subcard {
  background: #ffffff !important;
  border: 1px solid #e2e8f0 !important;
  border-radius: 10px !important;
  padding: 13px 15px !important;
  margin-bottom: 12px !important;
  box-shadow: 0 1px 2px rgba(0, 0, 0, 0.02) !important;
  transition: border-color 0.15s ease, box-shadow 0.15s ease !important;
}
.client-subcard:hover {
  border-color: #cbd5e1 !important;
}
.client-subcard:last-child {
  margin-bottom: 0 !important;
}
.client-additional-subcards-wrap .client-subcard {
  margin-bottom: 10px !important;
}

.client-subcard-header {
  border-color: #f1f5f9 !important;
}

.client-subcard-icon-box {
  width: 26px !important;
  height: 26px !important;
  border-radius: 6px !important;
  background: #fef9c3 !important;
  color: #ca8a04 !important;
  display: inline-flex !important;
  align-items: center !important;
  justify-content: center !important;
  flex-shrink: 0 !important;
}
.client-subcard-icon-box.office-icon {
  background: #f1f5f9 !important;
  color: #475569 !important;
}

.client-subcard-title {
  font-size: 13px !important;
  font-weight: 700 !important;
  color: #0f172a !important;
}
.client-subcard-subtitle {
  font-size: 11px !important;
  color: #64748b !important;
  display: block !important;
}

.badge-hq-pill {
  background: #fef08a !important;
  color: #854d0e !important;
  font-size: 10px !important;
  font-weight: 700 !important;
  padding: 2px 7px !important;
  border-radius: 4px !important;
}
.badge-office-tag,
.badge-branch-tag {
  background: #f1f5f9 !important;
  color: #475569 !important;
  font-size: 10px !important;
  font-weight: 700 !important;
  padding: 2px 8px !important;
  border-radius: 9999px !important;
  letter-spacing: 0.3px !important;
}

/* Sub-card Actions */
.btn-set-primary,
.btn-set-hq {
  font-size: 11px !important;
  font-weight: 600 !important;
  padding: 4px 10px !important;
  border-radius: 6px !important;
  color: #b45309 !important;
  border: 1px solid #fde68a !important;
  background: #fffdf0 !important;
  cursor: pointer !important;
  display: inline-flex !important;
  align-items: center !important;
  gap: 4px !important;
  transition: all 0.15s ease !important;
}
.btn-set-primary:hover,
.btn-set-hq:hover {
  background: #fef3c7 !important;
  border-color: #f59e0b !important;
  color: #92400e !important;
}

.btn-remove-subcard {
  font-size: 11px !important;
  font-weight: 600 !important;
  padding: 4px 10px !important;
  border-radius: 6px !important;
  color: #ef4444 !important;
  border: 1px solid #fee2e2 !important;
  background: #ffffff !important;
  cursor: pointer !important;
  display: inline-flex !important;
  align-items: center !important;
  gap: 4px !important;
  transition: all 0.15s ease !important;
}
.btn-remove-subcard:hover {
  background: #fef2f2 !important;
  border-color: #fca5a5 !important;
  color: #dc2626 !important;
}

/* Form Controls */
.client-addr-field-icon-left {
  left: 11px !important;
  top: 50% !important;
  transform: translateY(-50%) !important;
  color: #94a3b8 !important;
  pointer-events: none !important;
  z-index: 2 !important;
}
.client-addr-field-icon-right {
  right: 11px !important;
  top: 50% !important;
  transform: translateY(-50%) !important;
  color: #94a3b8 !important;
  pointer-events: none !important;
  z-index: 2 !important;
}
.client-addr-field-icon-top {
  left: 11px !important;
  top: 10px !important;
  color: #94a3b8 !important;
  pointer-events: none !important;
  z-index: 2 !important;
}

.client-addr-input {
  width: 100% !important;
  height: 36px !important;
  padding: 0 32px !important;
  border: 1px solid #cbd5e1 !important;
  border-radius: 8px !important;
  font-size: 12px !important;
  color: #1e293b !important;
  background: #ffffff !important;
  transition: border-color .15s ease, box-shadow .15s ease !important;
}
.client-addr-input:focus {
  outline: none !important;
  border-color: #2563eb !important;
  box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.12) !important;
}
.client-addr-input.is-invalid {
  border-color: #ef4444 !important;
  box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.12) !important;
}

.client-addr-textarea {
  width: 100% !important;
  min-height: 52px !important;
  padding: 7px 10px 7px 32px !important;
  border: 1px solid #cbd5e1 !important;
  border-radius: 8px !important;
  font-size: 12px !important;
  color: #1e293b !important;
  line-height: 1.45 !important;
  background: #ffffff !important;
  transition: border-color .15s ease, box-shadow .15s ease !important;
  resize: vertical !important;
}
.client-addr-textarea:focus {
  outline: none !important;
  border-color: #2563eb !important;
  box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.12) !important;
}
.client-addr-textarea.is-invalid {
  border-color: #ef4444 !important;
  box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.12) !important;
}

.client-field-hint {
  font-size: 10.5px !important;
  color: #64748b !important;
}
.client-addr-counter {
  font-size: 10.5px !important;
  color: #94a3b8 !important;
}
.client-addr-error {
  font-size: 11px !important;
  font-weight: 600 !important;
}

/* Empty State */
.client-addr-empty-state {
  background: #fffdf0 !important;
  border: 1px solid #fde68a !important;
  border-radius: 10px !important;
  padding: 24px 20px !important;
  display: flex;
  flex-direction: column !important;
  align-items: center !important;
  justify-content: center !important;
  text-align: center !important;
  margin-bottom: 8px !important;
  transition: all 0.2s ease !important;
}
.client-addr-empty-state.d-none,
.client-addr-empty-state[style*="display: none"],
.client-addr-empty-state[style*="display:none"] {
  display: none !important;
}
.client-addr-empty-state:hover {
  border-color: #f59e0b !important;
}
.empty-state-icon-circle {
  width: 44px !important;
  height: 44px !important;
  border-radius: 50% !important;
  background: #eff6ff !important;
  border: 1px solid #dbeafe !important;
  display: flex !important;
  align-items: center !important;
  justify-content: center !important;
  margin-bottom: 10px !important;
  color: #2563eb !important;
}
.empty-state-title {
  font-size: 13.5px !important;
  font-weight: 700 !important;
  color: #0f172a !important;
  margin: 0 0 4px 0 !important;
}
.empty-state-subtitle {
  font-size: 11.5px !important;
  color: #64748b !important;
  margin: 0 0 14px 0 !important;
  max-width: 480px !important;
}

/* CTA Buttons */
.btn-add-first-location,
.btn-add-additional-location {
  background: linear-gradient(180deg, #f59e0b 0%, #d97706 100%) !important;
  border: none !important;
  color: #ffffff !important;
  border-radius: 6px !important;
  padding: 8px 20px !important;
  font-size: 12px !important;
  font-weight: 700 !important;
  cursor: pointer !important;
  display: inline-flex !important;
  align-items: center !important;
  justify-content: center !important;
  gap: 6px !important;
  box-shadow: 0 1px 3px rgba(245, 158, 11, 0.3) !important;
  transition: all 0.15s ease !important;
}
.btn-add-first-location:hover,
.btn-add-additional-location:hover {
  background: linear-gradient(180deg, #d97706 0%, #b45309 100%) !important;
  transform: translateY(-1px) !important;
  box-shadow: 0 2px 5px rgba(245, 158, 11, 0.4) !important;
  color: #ffffff !important;
}
.btn-add-first-location:active,
.btn-add-additional-location:active {
  transform: translateY(0) !important;
}

.client-add-location-cta-wrap {
  display: flex;
  justify-content: center !important;
  align-items: center !important;
  width: 100% !important;
  padding: 6px 0 2px 0 !important;
}
.client-add-location-cta-wrap.d-none,
.client-add-location-cta-wrap[style*="display: none"],
.client-add-location-cta-wrap[style*="display:none"] {
  display: none !important;
}

/* City Autocomplete Dropdown */
.client-addr-city-dropdown {
  display: none;
  position: absolute;
  top: calc(100% + 4px);
  left: 0;
  right: 0;
  background: #ffffff;
  border: 1px solid #cbd5e1;
  border-radius: 8px;
  box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.05);
  max-height: 220px;
  overflow-y: auto;
  z-index: 1050;
}
.client-addr-city-dropdown.active {
  display: block;
}
.client-addr-dd-item {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 8px 12px;
  cursor: pointer;
  border-bottom: 1px solid #f1f5f9;
  transition: background .15s;
}
.client-addr-dd-item:hover, .client-addr-dd-item.is-highlighted {
  background: #f8fafc;
}
.client-addr-dd-left {
  display: flex;
  align-items: center;
  gap: 8px;
}
.client-addr-dd-city {
  font-size: 12px;
  font-weight: 600;
  color: #1e293b;
}
.client-addr-dd-sub {
  font-size: 11px;
  color: #64748b;
}
.client-addr-dd-badge {
  font-size: 10px;
  background: #f1f5f9;
  color: #475569;
  padding: 2px 6px;
  border-radius: 4px;
}
.client-addr-dd-empty {
  padding: 12px;
  text-align: center;
  font-size: 12px;
  color: #64748b;
}

/* RIGHT SIDEBAR: AI Location Discovery */
.client-ai-location-card {
  background: #faf8ff !important;
  border: 1px solid #ede9fe !important;
  border-radius: 12px !important;
  padding: 14px 16px !important;
  margin-bottom: 12px !important;
}
.client-ai-icon-box {
  width: 26px !important;
  height: 26px !important;
  border-radius: 7px !important;
  background: #ede9fe !important;
  display: inline-flex !important;
  align-items: center !important;
  justify-content: center !important;
  color: #7040f5 !important;
}
.client-ai-title {
  font-size: 13.5px !important;
  font-weight: 700 !important;
  color: #101828 !important;
  margin: 0 !important;
}
.badge-ai-beta {
  background: #ede9fe !important;
  color: #7040f5 !important;
  font-weight: 600 !important;
  font-size: 10px !important;
  padding: 2px 7px !important;
  border-radius: 6px !important;
}
.client-ai-desc {
  font-size: 11px !important;
  color: #64748b !important;
  line-height: 1.45 !important;
  margin-bottom: 12px !important;
}

.client-ai-loc-item {
  background: #ffffff !important;
  border: 1px solid #e2e8f0 !important;
  border-radius: 10px !important;
  padding: 12px 14px !important;
}
#ai_loc_card_0 {
  background: #f0fdf4 !important;
  border-color: #bbf7d0 !important;
}
.ai-loc-symbol-box {
  width: 28px !important;
  height: 28px !important;
  border-radius: 7px !important;
  display: inline-flex !important;
  align-items: center !important;
  justify-content: center !important;
  flex-shrink: 0 !important;
  margin-top: 2px !important;
}
.symbol-hq {
  background: #dcfce7 !important;
  color: #16a34a !important;
}
.symbol-office {
  background: #f1f5f9 !important;
  color: #475569 !important;
}

.ai-loc-city-name {
  font-size: 13px !important;
  font-weight: 700 !important;
  color: #0f172a !important;
}
.badge-ai-recommended {
  background: #dcfce7 !important;
  color: #15803d !important;
  border-radius: 9999px !important;
  font-size: 9.5px !important;
  font-weight: 600 !important;
  padding: 2px 8px !important;
}
.ai-loc-type-label {
  font-size: 11px !important;
  font-weight: 600 !important;
  color: #334155 !important;
  margin-top: 1px !important;
}
.ai-loc-sub-note {
  font-size: 10.5px !important;
  color: #64748b !important;
  margin: 2px 0 0 0 !important;
}
.ai-loc-footer-border {
  border-color: rgba(226, 232, 240, 0.7) !important;
}
#ai_loc_card_0 .ai-loc-footer-border {
  border-color: rgba(187, 247, 208, 0.6) !important;
}
.ai-loc-meta-text {
  font-size: 10.5px !important;
  color: #64748b !important;
}
.client-ai-source-link {
  color: #2563eb !important;
  text-decoration: none !important;
  display: inline-flex !important;
  align-items: center !important;
  gap: 2px !important;
}
.ai-loc-verified {
  color: #16a34a !important;
  font-weight: 500 !important;
}
.verified-dot {
  width: 5px !important;
  height: 5px !important;
  border-radius: 50% !important;
  background: #16a34a !important;
  display: inline-block !important;
}
.ai-loc-likely {
  color: #64748b !important;
  font-weight: 500 !important;
}
.likely-dot {
  width: 5px !important;
  height: 5px !important;
  border-radius: 50% !important;
  background: #64748b !important;
  display: inline-block !important;
}

.btn-ai-set-hq {
  background: #f59e0b !important;
  color: #78350f !important;
  font-weight: 600 !important;
  font-size: 11px !important;
  border: none !important;
  border-radius: 6px !important;
  padding: 4px 10px !important;
  cursor: pointer !important;
}
.btn-ai-add-office {
  background: #ffffff !important;
  color: #2563eb !important;
  border: 1px solid #2563eb !important;
  font-weight: 600 !important;
  font-size: 11px !important;
  border-radius: 6px !important;
  padding: 4px 10px !important;
  cursor: pointer !important;
}
.btn-ai-add-all {
  background: #eff6ff !important;
  border: 1px solid #bfdbfe !important;
  color: #1d4ed8 !important;
  font-weight: 600 !important;
  font-size: 11.5px !important;
  border-radius: 6px !important;
  padding: 6px 14px !important;
  display: inline-flex !important;
  align-items: center !important;
  gap: 6px !important;
  cursor: pointer !important;
}
.ai-source-view-link {
  color: #2563eb !important;
  text-decoration: none !important;
  font-size: 11px !important;
  font-weight: 500 !important;
  display: inline-flex !important;
  align-items: center !important;
  gap: 3px !important;
}

/* Quick Tips Card */
.client-addr-tips-card {
  background: #fefce8 !important;
  border: 1px solid #fef08a !important;
  border-radius: 12px !important;
  padding: 14px 16px !important;
  margin-bottom: 0 !important;
}
.client-tips-icon-box {
  width: 26px !important;
  height: 26px !important;
  border-radius: 50% !important;
  background: #fef08a !important;
  display: inline-flex !important;
  align-items: center !important;
  justify-content: center !important;
  color: #854d0e !important;
  flex-shrink: 0 !important;
}
.client-tips-heading {
  font-size: 13px !important;
  font-weight: 700 !important;
  color: #713f12 !important;
  margin: 0 !important;
}
.client-tips-list {
  font-size: 11px !important;
  color: #78350f !important;
  line-height: 1.55 !important;
}

/* 15.6" Laptop Viewport Responsiveness */
@media (max-height: 850px) {
  .client-addr-top-banner {
    padding: 7px 12px !important;
    margin-bottom: 10px !important;
  }
  .client-section-card > .card-header {
    padding: 10px 14px !important;
  }
  .client-section-card > .card-body {
    padding: 11px 14px !important;
  }
  .client-subcard {
    padding: 10px 12px !important;
    margin-bottom: 9px !important;
  }
  .client-subcard-header {
    margin-bottom: 8px !important;
    padding-bottom: 6px !important;
  }
  .client-addr-input {
    height: 34px !important;
    font-size: 11.5px !important;
  }
  .client-addr-textarea {
    min-height: 48px !important;
    font-size: 11.5px !important;
    padding: 6px 10px 6px 30px !important;
  }
  .client-ai-location-card {
    padding: 11px 13px !important;
  }
  .client-ai-loc-item {
    padding: 9px 11px !important;
    margin-bottom: 7px !important;
  }
  .client-addr-tips-card {
    padding: 11px 13px !important;
  }
}

/* =========================================================================
   STEP 4: REVIEW AND SUBMIT
   ========================================================================= */
.client-review-layout {
  display: grid !important;
  width: 100% !important;
  grid-template-columns: minmax(0, 68fr) minmax(0, 32fr) !important;
  gap: 16px !important;
  align-items: start !important;
}
@media (max-width: 992px) {
  .client-review-layout {
    grid-template-columns: 1fr !important;
  }
}

.client-review-section-card {
  background: #ffffff !important;
  border: 1px solid #e2e8f0 !important;
  border-radius: 12px !important;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02) !important;
  overflow: hidden !important;
}

.client-review-avatar-box {
  width: 52px !important;
  height: 52px !important;
  border-radius: 12px !important;
  background: #ede9fe !important;
  color: #7c3aed !important;
  font-weight: 700 !important;
  font-size: 14px !important;
  display: inline-flex !important;
  align-items: center !important;
  justify-content: center !important;
  flex-shrink: 0 !important;
  background-size: cover !important;
  background-position: center !important;
}

.client-review-edit-btn {
  background: #eff6ff !important;
  border: 1px solid #bfdbfe !important;
  color: #2563eb !important;
  font-weight: 600 !important;
  font-size: 11.5px !important;
  border-radius: 6px !important;
  padding: 4px 10px !important;
  display: inline-flex !important;
  align-items: center !important;
  gap: 4px !important;
  cursor: pointer !important;
}
.client-review-edit-btn:hover {
  background: #dbeafe !important;
  color: #1d4ed8 !important;
}

.client-review-service-pill {
  background: #eff6ff !important;
  color: #1d4ed8 !important;
  border: 1px solid #bfdbfe !important;
  font-size: 11px !important;
  font-weight: 600 !important;
  padding: 3px 11px !important;
  border-radius: 9999px !important;
  display: inline-block !important;
}

.verify-toggle-card {
  border: 1.5px solid #cbd5e1 !important;
  border-radius: 12px !important;
  padding: 16px 20px !important;
  background: #ffffff !important;
  transition: all 0.2s ease !important;
}
.verify-toggle-card.is-verified {
  border-color: #10b981 !important;
  background: #f0fdf4 !important;
}

.switch-custom {
  position: relative !important;
  display: inline-block !important;
  width: 44px !important;
  height: 24px !important;
}
.switch-custom input {
  opacity: 0;
  width: 0;
  height: 0;
}
.slider-custom {
  position: absolute !important;
  cursor: pointer !important;
  top: 0; left: 0; right: 0; bottom: 0;
  background-color: #cbd5e1 !important;
  transition: .3s !important;
  border-radius: 24px !important;
}
.slider-custom:before {
  position: absolute !important;
  content: "" !important;
  height: 18px !important;
  width: 18px !important;
  left: 3px !important;
  bottom: 3px !important;
  background-color: white !important;
  transition: .3s !important;
  border-radius: 50% !important;
  box-shadow: 0 1px 3px rgba(0,0,0,0.2) !important;
}
input:checked + .slider-custom {
  background-color: #10b981 !important;
}
input:checked + .slider-custom:before {
  transform: translateX(20px) !important;
}

/* Helper Card */
.client-helper-card {
  background: #ffffff !important;
  border: 1px solid #e2e8f0 !important;
  border-radius: 12px !important;
  padding: 16px !important;
  box-shadow: 0 1px 3px rgba(0,0,0,0.02) !important;
}
.client-helper-title {
  display: flex !important;
  align-items: center !important;
  gap: 8px !important;
  font-size: 13px !important;
  font-weight: 700 !important;
  color: #0f172a !important;
  margin-bottom: 8px !important;
}
.client-helper-card ul {
  padding-left: 18px !important;
  margin: 0 !important;
  font-size: 11px !important;
  color: #64748b !important;
  line-height: 1.6 !important;
}
</style>
@endpush

@section('content')
@php
    $companyId = $account->id ?? 0;
    // --- ROBUST MULTI-SOURCE LOGO RESOLVER ---
    $logoUrl = '';
    $hasRealLogo = false;
    $rawLogo = trim((string)(@$account->logo ?? ''));
    $website = trim((string)(@$account->website ?? ''));

    if (!empty($rawLogo) && strlen($rawLogo) > 2) {
        if (str_starts_with($rawLogo, 'http://') || str_starts_with($rawLogo, 'https://')) {
            $logoUrl = $rawLogo;
            $hasRealLogo = true;
        } elseif (file_exists(public_path('user-uploads/company-logo/' . $rawLogo))) {
            $logoUrl = asset('user-uploads/company-logo/' . $rawLogo);
            $hasRealLogo = true;
        } elseif (file_exists(base_path('user-uploads/company-logo/' . $rawLogo))) {
            $logoUrl = url('user-uploads/company-logo/' . $rawLogo);
            $hasRealLogo = true;
        } else {
            $logoUrl = url('public/user-uploads/company-logo/' . $rawLogo);
            $hasRealLogo = true;
        }
    } elseif (!empty($website)) {
        $cleanDomain = preg_replace('#^https?://#i', '', preg_replace('#^www\.#i', '', explode('/', $website)[0]));
        if (!empty($cleanDomain) && strlen($cleanDomain) > 2) {
            $logoUrl = 'https://www.google.com/s2/favicons?domain=' . urlencode($cleanDomain) . '&sz=256';
            $hasRealLogo = true;
        }
    }

    if (!$hasRealLogo) {
        $logoUrl = asset('assets/logo-not-found.png');
    }

    // Resolve pre-selected industry name
    $selectedIndName = '';
    if (!empty($account->industries) && !empty($industries)) {
        foreach ($industries as $ind) {
            if ((string)$ind->ind_id === (string)$account->industries) {
                $selectedIndName = ucfirst($ind->ind_name);
                break;
            }
        }
    }

    // Company Size Tiers matching Client Create standard
    $companySizeTiers = [
        '1-1'         => ['label' => 'Solo (1 employee)',        'badge' => '1',           'desc' => 'Individual practitioner / Freelancer'],
        '2-10'        => ['label' => '2 – 10 employees',          'badge' => '2 – 10',      'desc' => 'Seed / Startup stage team'],
        '11-50'       => ['label' => '11 – 50 employees',         'badge' => '11 – 50',     'desc' => 'Early growth stage business'],
        '51-200'      => ['label' => '51 – 200 employees',        'badge' => '51 – 200',    'desc' => 'Mid-sized established business'],
        '201-500'     => ['label' => '201 – 500 employees',       'badge' => '201 – 500',   'desc' => 'Upper mid-market company'],
        '501-1000'    => ['label' => '501 – 1,000 employees',     'badge' => '501 – 1K',    'desc' => 'Commercial scale organization'],
        '1001-5000'   => ['label' => '1,001 – 5,000 employees',   'badge' => '1K – 5K',     'desc' => 'Enterprise tier organization'],
        '5001-10000'  => ['label' => '5,001 – 10,000 employees',  'badge' => '5K – 10K',    'desc' => 'Large enterprise corporation'],
        '10001-50000' => ['label' => '10,001 – 50,000 employees', 'badge' => '10K – 50K',   'desc' => 'Global enterprise scale'],
        '50001-0'     => ['label' => '50,000+ employees',        'badge' => '50,000+',     'desc' => 'Multinational conglomerate']
    ];

    // Intelligent Company Size Resolver: parses range, client size, number, or defaults to 201-500
    $currSizeKey = '';
    $candidates = [
        (!empty($account->company_size_from) && !empty($account->company_size)) ? ($account->company_size_from . '-' . $account->company_size) : null,
        (!empty($client->company_size_from) && !empty($client->company_sizes)) ? ($client->company_size_from . '-' . $client->company_sizes) : null,
        @$account->company_size,
        @$client->company_sizes,
        @$account->company_size_from,
    ];

    foreach ($candidates as $cand) {
        if (empty($cand)) continue;
        $c = trim((string)$cand);
        if (isset($companySizeTiers[$c])) {
            $currSizeKey = $c;
            break;
        }
        if ($c === '1-10') {
            $currSizeKey = '2-10';
            break;
        }
        if (in_array($c, ['50001-', '10000-999999', '50000+'])) {
            $currSizeKey = '50001-0';
            break;
        }
        if (is_numeric($c)) {
            $num = (int)$c;
            if ($num <= 1) $currSizeKey = '1-1';
            elseif ($num <= 10) $currSizeKey = '2-10';
            elseif ($num <= 50) $currSizeKey = '11-50';
            elseif ($num <= 200) $currSizeKey = '51-200';
            elseif ($num <= 500) $currSizeKey = '201-500';
            elseif ($num <= 1000) $currSizeKey = '501-1000';
            elseif ($num <= 5000) $currSizeKey = '1001-5000';
            elseif ($num <= 10000) $currSizeKey = '5001-10000';
            elseif ($num <= 50000) $currSizeKey = '10001-50000';
            else $currSizeKey = '50001-0';
            break;
        }
    }
    if (empty($currSizeKey)) {
        $currSizeKey = '201-500'; // Default enterprise SaaS standard matching client create
    }
    $selectedSizeLabel = isset($companySizeTiers[$currSizeKey]) ? $companySizeTiers[$currSizeKey]['label'] : '201 – 500 employees';

    // Company Type Data from GeneralMethod::industries_type_arr() (Exact match with user requirement)
    $industries_type_arr = $industries_type_arr ?? \App\Helper\GeneralMethod::industries_type_arr();

    $currCompanyTypeId = null;
    if (isset($account->industries_type) && $account->industries_type !== '' && $account->industries_type !== null) {
        $currCompanyTypeId = (int)$account->industries_type;
    } elseif (isset($client->company_type) && $client->company_type !== '' && $client->company_type !== null) {
        $currCompanyTypeId = (int)$client->company_type;
    } else {
        $currCompanyTypeId = 0; // Default: Public (ID 0)
    }

    $selectedTypeName = isset($industries_type_arr[$currCompanyTypeId]) ? $industries_type_arr[$currCompanyTypeId] : 'Public';

    $actualCompanyTypes = [
        ['id' => '', 'name' => 'Select Type', 'display_name' => 'Select Type']
    ];
    foreach ($industries_type_arr as $tId => $tName) {
        $actualCompanyTypes[] = [
            'id' => (int)$tId,
            'name' => $tName,
            'display_name' => $tName
        ];
    }

    // Description & Tags (Aligned with tab-details.blade.php)
    $initialDesc = !empty($account->description) ? $account->description : '';
    $initialDescCount = mb_strlen(strip_tags($initialDesc));

    $existingTags = @$client->tags ?: '';
    $parsedTagsArray = [];
    if (!empty($existingTags)) {
        $decoded = json_decode($existingTags, true);
        if (is_array($decoded)) {
            $parsedTagsArray = array_column($decoded, 'value');
        } else {
            $parsedTagsArray = array_map('trim', explode(',', $existingTags));
        }
    }
    $parsedTagsArray = array_values(array_filter($parsedTagsArray));
    $encodedTags = !empty($parsedTagsArray) ? json_encode(array_map(fn($t) => ['value' => $t], $parsedTagsArray)) : '';
    $tagsJson = $encodedTags;

    $defaultSuggestedServices = [
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
    ];

    // Locations & Addresses (Aligned with tab-locations.blade.php)
    $locationsList = $client_location ?? collect([]);
    $firstLoc = $locationsList->first();
    $additionalLocs = $locationsList->slice(1);

    $primary_city = '';
    $primary_tagify_json = '';
    $primary_address = '';
    $additional_addresses = [];
    $addressId = 0;

    if (isset($locationsList) && is_iterable($locationsList) && count($locationsList) > 0) {
        foreach ($locationsList as $idx => $clVal) {
            $cName = $clVal->resolved_city ?? ($clVal->city ?? '');
            $sName = $clVal->state_name ?? '';
            $coName = $clVal->country_name ?? '';
            $canonical = !empty($clVal->canonical_location) 
                ? str_replace(' | ', ', ', $clVal->canonical_location) 
                : trim($cName . ($sName ? ', ' . $sName : '') . ($coName ? ', ' . $coName : ''), ', ');
            $tagifyVal = !empty($clVal->canonical_location) 
                ? $clVal->canonical_location 
                : trim($cName . ($sName ? ' | ' . $sName : '') . ($coName ? ' | ' . $coName : ''), ' |');
            $tagifyJson = json_encode([['value' => $tagifyVal]]);

            if ($idx === 0) {
                $primary_city = $canonical ?: 'Pune, Maharashtra, India';
                $primary_tagify_json = $tagifyJson ?: json_encode([['value' => 'Pune | Maharashtra | India']]);
                $primary_address = $clVal->address ?? '';
            } else {
                $additional_addresses[] = [
                    'id' => $idx,
                    'address' => $clVal->address ?? '',
                    'city' => $canonical,
                    'tagify_json' => $tagifyJson,
                ];
                $addressId = $idx;
            }
        }
    } else {
        $primary_city = 'Pune, Maharashtra, India';
        $primary_tagify_json = json_encode([['value' => 'Pune | Maharashtra | India']]);
        $primary_address = '';
    }

    $firstCityStr = $primary_city ?: 'Pune, Maharashtra, India';
@endphp

<!--begin::Content-->
<div class="content d-flex flex-column flex-column-fluid pt-0 pb-5" id="kt_content">
  <div class="container-fluid px-3">

    <!-- =========================================================================
         TOP UNIFIED STICKY HEADER & 4-STEP STEPPER BAR
         ========================================================================= -->
    <div class="card client-top-nav-card" id="client_top_nav_card">
      <div class="client-header-grid">
        
        <!-- LEFT: Title Block -->
        <div class="client-header-identity">
          <div class="client-header-titles">
            <h2 class="client-header-title" id="client_header_title">Edit Company</h2>
            <p class="client-header-subtitle" id="client_header_subtitle" title="Edit company details, locations, and verification">Edit company details, locations, and verification</p>
          </div>
        </div>

        <!-- CENTER: Stepper (1 Company → 2 Description → 3 Address → 4 Review) -->
        <div class="client-header-stepper-col">
          <div class="client-stepper-wrap" id="client_wizard_stepper" role="tablist">
            
            <!-- Step 1: Company -->
            <div class="client-step-item is-active" data-step="1" role="tab" onclick="goToWizardStep(1)">
              <div class="client-step-circle client-step-circle-1 is-active">1</div>
              <span class="client-step-name">Company</span>
            </div>

            <div class="client-step-arrow" data-arrow-after="1" aria-hidden="true">
              <svg class="client-step-arrow-svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M5 12h14"/><path d="m12 5 7 7-7 7"/>
              </svg>
            </div>

            <!-- Step 2: Description -->
            <div class="client-step-item" data-step="2" role="tab" onclick="goToWizardStep(2)">
              <div class="client-step-circle client-step-circle-2 is-upcoming">2</div>
              <span class="client-step-name">Description</span>
            </div>

            <div class="client-step-arrow" data-arrow-after="2" aria-hidden="true">
              <svg class="client-step-arrow-svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M5 12h14"/><path d="m12 5 7 7-7 7"/>
              </svg>
            </div>

            <!-- Step 3: Address -->
            <div class="client-step-item" data-step="3" role="tab" onclick="goToWizardStep(3)">
              <div class="client-step-circle client-step-circle-3 is-upcoming">3</div>
              <span class="client-step-name">Address</span>
            </div>

            <div class="client-step-arrow" data-arrow-after="3" aria-hidden="true">
              <svg class="client-step-arrow-svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M5 12h14"/><path d="m12 5 7 7-7 7"/>
              </svg>
            </div>

            <!-- Step 4: Review -->
            <div class="client-step-item" data-step="4" role="tab" onclick="goToWizardStep(4)">
              <div class="client-step-circle client-step-circle-4 is-upcoming">4</div>
              <span class="client-step-name">Review</span>
            </div>

          </div>
        </div>

        <!-- RIGHT: Contextual Actions (Pixel-Matched to Reference) -->
        <div class="client-header-actions" id="client_header_actions">
          <!-- Step 1: Close (✕) Button -->
          <a href="{{ route('superadmin.company.index') }}" class="btn client-top-close-btn" id="client_top_close_btn" title="Close and return">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
              <line x1="18" y1="6" x2="6" y2="18"></line>
              <line x1="6" y1="6" x2="18" y2="18"></line>
            </svg>
            <span>Close</span>
          </a>

          <!-- Steps 2-4: Back Button -->
          <button type="button" class="btn client-top-back-btn" id="client_top_back_btn" title="Back to previous step" onclick="handleWizardBack()">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
              <path d="M19 12H5"/><path d="m12 19-7-7 7-7"/>
            </svg>
            <span>Back</span>
          </button>

          <!-- Steps 1-3: Next Page Button -->
          <button type="button" class="btn client-top-next-btn" id="client_top_next_btn" onclick="handleWizardNext()">
            <span>Next Page</span>
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
              <path d="M5 12h14"/><path d="m12 5 7 7-7 7"/>
            </svg>
          </button>

          <!-- Step 4: Save / Update Button -->
          <button type="button" class="btn client-top-submit-btn" id="submitBtn" onclick="submitCompanyForm()">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
              <polyline points="20 6 9 17 4 12"/>
            </svg>
            <span id="submitBtnText">Update Company</span>
          </button>
        </div>

      </div>
    </div>

    <!-- =========================================================================
         MAIN WIZARD FORM
         ========================================================================= -->
    <form class="form mx-auto" novalidate="novalidate" autocomplete="off" action="javascript:void(0);" method="post" id="kt_company_form" name="kt_company_form" enctype="multipart/form-data">
      <meta name="csrf-token" content="{{ csrf_token() }}" />
      <input type="hidden" name="idempotency_key" id="kt_form_idempotency_key" value="{{ (string) \Illuminate\Support\Str::uuid() }}" />
      <input type="hidden" name="avatar_remove" id="client_avatar_remove" value="0" />
      <input type="hidden" name="addressId" id="addressId" value="{{ max(0, count($locationsList) - 1) }}" />

      <!-- =====================================================================
           PAGE 1: COMPANY PROFILE & LOGO
           ===================================================================== -->
      <div class="pb-5 client-step-pane is-active" id="step_pane_1" data-step="1">
        <div class="god-level-company-layout">
          
          <!-- LEFT CARD: Company Profile -->
          <div class="god-card god-company-profile-card">
            <!-- Header -->
            <div class="god-card-header">
              <div class="god-card-header-icon blue-sky-icon">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <rect x="4" y="2" width="16" height="20" rx="2" ry="2"/>
                  <line x1="9" y1="6" x2="9" y2="6.01"/><line x1="15" y1="6" x2="15" y2="6.01"/>
                  <line x1="9" y1="10" x2="9" y2="10.01"/><line x1="15" y1="10" x2="15" y2="10.01"/>
                  <line x1="9" y1="14" x2="9" y2="14.01"/><line x1="15" y1="14" x2="15" y2="14.01"/>
                  <path d="M10 22v-4h4v4"/>
                </svg>
              </div>
              <div class="god-card-header-titles">
                <h3 class="god-card-title">Company Profile</h3>
                <p class="god-card-subtitle">Review and update organization identity and operational data.</p>
              </div>
            </div>

            <!-- Fields Grid -->
            <div class="god-fields-grid">
              
              <!-- ROW 1 (Full Width): Website -->
              <div class="god-field-wrap god-field-full">
                <div class="god-field-header">
                  <label class="god-field-label" for="website">Company Website (Optional)</label>
                </div>
                <div class="god-website-control-row">
                  <div class="god-input-box god-website-input-box" id="website_input_box">
                    <span class="god-input-left-icon">
                      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/>
                        <path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/>
                      </svg>
                    </span>
                    <input type="text"
                           class="god-native-input"
                           name="website"
                           id="website"
                           value="{{ $account->website ?? '' }}"
                           placeholder="https://example.com"
                           autocomplete="off"
                           spellcheck="false"
                           onkeydown="if(event.key==='Enter'||event.keyCode===13){event.preventDefault();if(window.fetchGodLevelCompanyIntelligence)window.fetchGodLevelCompanyIntelligence(this.value);return false;}" />
                    <button type="button"
                            class="god-input-clear-btn"
                            id="website_clear_btn"
                            title="Clear website"
                            style="{{ !empty($account->website) ? 'display:inline-flex;' : 'display:none;' }}">
                      <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line>
                      </svg>
                    </button>
                  </div>

                  <!-- Yellow Fetch Details Button -->
                  <button type="button"
                          class="btn god-fetch-btn"
                          id="client_auto_fetch"
                          title="Fetch and auto-fill company intelligence"
                          onclick="if(window.fetchGodLevelCompanyIntelligence)window.fetchGodLevelCompanyIntelligence(document.getElementById('website').value); return false;">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="currentColor">
                      <path d="m12 3-1.9 5.8a2 2 0 0 1-1.3 1.3L3 12l5.8 1.9a2 2 0 0 1 1.3 1.3L12 21l1.9-5.8a2 2 0 0 1 1.3-1.3L21 12l-5.8-1.9a2 2 0 0 1-1.3-1.3L12 3z"/>
                    </svg>
                    <span>Fetch Details</span>
                  </button>
                </div>
              </div>

              <!-- ROW 2 (Full Width): Company Name -->
              <div class="god-field-wrap god-field-full">
                <div class="god-field-header">
                  <label class="god-field-label" for="company_name">Company Name <span class="text-danger">*</span></label>
                </div>
                <div class="god-input-box" id="company_name_input_box">
                  <span class="god-input-left-icon">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                      <rect x="4" y="2" width="16" height="20" rx="2" ry="2"/>
                      <line x1="9" y1="6" x2="9" y2="6.01"/><line x1="15" y1="6" x2="15" y2="6.01"/>
                      <line x1="9" y1="10" x2="9" y2="10.01"/><line x1="15" y1="10" x2="15" y2="10.01"/>
                      <line x1="9" y1="14" x2="9" y2="14.01"/><line x1="15" y1="14" x2="15" y2="14.01"/>
                      <path d="M10 22v-4h4v4"/>
                    </svg>
                  </span>
                  <input type="text"
                         class="god-native-input"
                         name="company_name"
                         id="company_name"
                         value="{{ $account->company_name ?? '' }}"
                         placeholder="e.g. Stripe"
                         required />
                </div>
              </div>

              <!-- ROW 3, COL 1: Industry (Searchable Combobox) -->
              <div class="god-field-wrap">
                <div class="god-field-header">
                  <label class="god-field-label" for="industry_display">Industry</label>
                </div>
                <select name="industries" id="industries" style="position:absolute; width:1px; height:1px; opacity:0; pointer-events:none;" tabindex="-1">
                  <option value="">Select industry</option>
                  @foreach($industries as $ind)
                    <option value="{{ $ind->ind_id }}" {{ (string)$account->industries === (string)$ind->ind_id ? 'selected' : '' }}>
                      {{ ucfirst($ind->ind_name) }}
                    </option>
                  @endforeach
                </select>

                <div class="jero-ind-select-wrap" id="jero_ind_select_wrap">
                  <div class="god-input-box" id="jero_ind_input_box" onclick="toggleDropdown('jero_ind_select_wrap')">
                    <span class="god-input-left-icon">
                      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M3 21h18"/><path d="M5 21V7l8-4v18"/><path d="M19 21V11l-6-4"/>
                      </svg>
                    </span>
                    <input type="text"
                           class="god-native-input"
                           id="industry_display"
                           placeholder="Select industry"
                           autocomplete="off"
                           value="{{ $selectedIndName }}"
                           oninput="filterIndustryList(this.value)" />
                    <button type="button" class="god-input-clear-btn" id="industry_clear_btn" onclick="clearIndustrySelect(event)" style="{{ $selectedIndName ? 'display:inline-flex;' : '' }}">
                      <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M18 6 6 18M6 6l12 12"/></svg>
                    </button>
                    <div class="jero-dropdown-chevron">
                      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m6 9 6 6 6-6"/></svg>
                    </div>
                  </div>

                  <div class="jero-dropdown-menu-custom" id="jero_ind_dropdown">
                    <div class="jero-dropdown-list-container" id="industry_list_container">
                      @foreach($industries as $ind)
                        <div class="jero-dropdown-item {{ (string)$account->industries === (string)$ind->ind_id ? 'is-selected' : '' }}"
                             data-id="{{ $ind->ind_id }}"
                             data-name="{{ ucfirst($ind->ind_name) }}"
                             onclick="selectIndustry('{{ $ind->ind_id }}', '{{ addslashes(ucfirst($ind->ind_name)) }}')">
                          <span>{{ ucfirst($ind->ind_name) }}</span>
                        </div>
                      @endforeach
                    </div>
                  </div>
                </div>
              </div>

              <!-- ROW 3, COL 2: Company Size -->
              <div class="god-field-wrap">
                <div class="god-field-header">
                  <label class="god-field-label" for="company_size_display">Company Size</label>
                </div>
                <select name="company_size" id="company_size" style="position:absolute; width:1px; height:1px; opacity:0; pointer-events:none;" tabindex="-1">
                  <option value="">Select size</option>
                  @foreach($companySizeTiers as $cVal => $cData)
                    <option value="{{ $cVal }}" {{ (string)$currSizeKey === (string)$cVal ? 'selected' : '' }}>{{ $cData['label'] }}</option>
                  @endforeach
                </select>

                <div class="jero-csize-select-wrap" id="jero_csize_select_wrap">
                  <div class="god-input-box" id="jero_csize_input_box" onclick="toggleDropdown('jero_csize_select_wrap')">
                    <span class="god-input-left-icon">
                      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                      </svg>
                    </span>
                    <input type="text"
                           class="god-native-input"
                           id="company_size_display"
                           placeholder="Select company size"
                           readonly
                           value="{{ $selectedSizeLabel }}" />
                    <div class="jero-dropdown-chevron">
                      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m6 9 6 6 6-6"/></svg>
                    </div>
                  </div>

                  <div class="jero-dropdown-menu-custom" id="jero_csize_dropdown">
                    <div class="jero-dropdown-list-container">
                      @foreach($companySizeTiers as $cVal => $cData)
                        <div class="jero-dropdown-item {{ (string)$currSizeKey === (string)$cVal ? 'is-selected' : '' }}"
                             onclick="selectCompanySize('{{ $cVal }}', '{{ addslashes($cData['label']) }}')">
                          <div>
                            <div style="font-weight:600;">{{ $cData['label'] }}</div>
                            <div style="font-size:10.5px; color:#64748b;">{{ $cData['desc'] }}</div>
                          </div>
                          <span class="badge bg-light text-dark">{{ $cData['badge'] }}</span>
                        </div>
                      @endforeach
                    </div>
                  </div>
                </div>
              </div>

              <!-- ROW 4, COL 1: Company Type (Search-First Dual-Layer Intelligence Combobox) -->
              <div class="god-field-wrap">
                <div class="god-field-header">
                  <label class="god-field-label" for="company_type_display">Company Type</label>
                </div>

                <!-- Native select synchronized for 100% Form POST & Validation compatibility -->
                <select name="company_type" id="company_type" style="position:absolute; width:1px; height:1px; opacity:0; pointer-events:none;" tabindex="-1" aria-hidden="true">
                  <option value="">Select Type</option>
                  @if(!empty($industries_type_arr))
                    @foreach($industries_type_arr as $tId => $tName)
                      <option value="{{ $tId }}" {{ (string)$tId === (string)$currCompanyTypeId ? 'selected="selected"' : '' }}>{{ $tName }}</option>
                    @endforeach
                  @endif
                </select>
                <input type="hidden" name="industries_type" id="industries_type" value="{{ $currCompanyTypeId }}">

                <!-- God-Level Enterprise Company Type Combobox -->
                <div class="jero-ctype-select-wrap god-ctype-wrap" id="jero_ctype_select_wrap">
                  <div class="god-input-box jero-ctype-input-box {{ $selectedTypeName ? 'has-value' : '' }}" id="jero_ctype_input_box">
                    <span class="god-input-left-icon jero-ctype-search-icon">
                      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M6 22V4a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v18Z"/>
                        <path d="M6 12H4a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2h2"/>
                        <path d="M18 9h2a2 2 0 0 1 2 2v9a2 2 0 0 1-2 2h-2"/>
                        <path d="M10 6h4"/>
                        <path d="M10 10h4"/>
                        <path d="M10 14h4"/>
                        <path d="M10 18h4"/>
                      </svg>
                    </span>
                    <input type="text"
                           class="god-native-input jero-ctype-input"
                           id="company_type_display"
                           placeholder="Select Type"
                           autocomplete="off"
                           spellcheck="false"
                           role="combobox"
                           aria-expanded="false"
                           aria-haspopup="listbox"
                           aria-autocomplete="list"
                           aria-controls="jero_ctype_dropdown"
                           value="{{ htmlspecialchars($selectedTypeName) }}">
                    <button type="button" class="god-input-clear-btn jero-ctype-clear-btn" id="jero_ctype_clear_btn" aria-label="Clear company type" title="Clear selection" style="{{ $selectedTypeName ? 'display:inline-flex;' : 'display:none;' }}">
                      <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M18 6 6 18M6 6l12 12"/></svg>
                    </button>
                    <div class="jero-dropdown-chevron jero-ctype-chevron" id="jero_ctype_chevron" title="Toggle company type list" role="button" aria-label="Toggle company type list" tabindex="-1">
                      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m6 9 6 6 6-6"/></svg>
                    </div>
                  </div>

                  <!-- Dropdown Menu Container (Search-First) -->
                  <div class="jero-ctype-dropdown" id="jero_ctype_dropdown" style="display:none;" role="listbox" aria-label="Company Types">
                    <div class="jero-ctype-list-container" id="jero_ctype_list_container"></div>
                  </div>
                </div>
              </div>

              <!-- ROW 4, COL 2: Founded Year -->
              <div class="god-field-wrap">
                <div class="god-field-header">
                  <label class="god-field-label" for="founded">Founded Year</label>
                </div>
                <div class="god-input-box">
                  <span class="god-input-left-icon">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                      <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>
                    </svg>
                  </span>
                  <input type="number"
                         class="god-native-input"
                         name="founded"
                         id="founded"
                         value="{{ $account->founded ?? '' }}"
                         placeholder="e.g. 2018"
                         min="1800"
                         max="{{ date('Y') }}" />
                </div>
              </div>

              <!-- ROW 5, COL 1: Career / Jobs Page URL with External Link Button -->
              <div class="god-field-wrap">
                <div class="god-field-header">
                  <label class="god-field-label" for="career_page_link">Career / Jobs Page URL</label>
                </div>
                <div class="god-input-box">
                  <span class="god-input-left-icon">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                      <path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/>
                      <path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/>
                    </svg>
                  </span>
                  <input type="url"
                         class="god-native-input"
                         name="career_page_link"
                         id="career_page_link"
                         value="{{ $account->career_page_link ?? '' }}"
                         placeholder="https://stripe.com/jobs" />
                  <button type="button"
                          class="god-input-external-btn"
                          id="career_url_open_btn"
                          title="Open career page in new tab"
                          aria-label="Open career page"
                          onclick="var u=document.getElementById('career_page_link').value; if(u){window.open(u.startsWith('http')?u:'https://'+u, '_blank');}">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                      <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/>
                      <polyline points="15 3 21 3 21 9"/>
                      <line x1="10" y1="14" x2="21" y2="3"/>
                    </svg>
                  </button>
                </div>
              </div>

              <!-- ROW 5, COL 2: Tax ID / Business Reg No / GST / Service Number -->
              <div class="god-field-wrap">
                <div class="god-field-header">
                  <label class="god-field-label" for="tax_id">Tax ID / Business Reg No / GST / Service Number (Optional)</label>
                </div>
                <div class="god-input-box">
                  <span class="god-input-left-icon">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                      <rect x="3" y="4" width="18" height="16" rx="2"/>
                      <line x1="7" y1="8" x2="17" y2="8"/>
                      <line x1="7" y1="12" x2="13" y2="12"/>
                      <line x1="7" y1="16" x2="11" y2="16"/>
                    </svg>
                  </span>
                  <input type="text"
                         class="god-native-input"
                         name="tax_id"
                         id="tax_id"
                         value="{{ $account->tax_id ?? $client->tax_id ?? '' }}"
                         placeholder="e.g. EIN, CIN, VAT, GSTIN" />
                </div>
              </div>

              <!-- ROW 6, COL 1: Official Company Email (View Only) -->
              <div class="god-field-wrap">
                <div class="god-field-header">
                  <label class="god-field-label" for="company_email">Official Company Email <span class="text-muted ms-1" style="font-size:10.5px; font-weight:500;">(View Only)</span></label>
                </div>
                <div class="god-input-box is-readonly" id="company_email_input_box" title="Official company email is view-only">
                  <span class="god-input-left-icon">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                      <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
                      <polyline points="22,6 12,13 2,6"/>
                    </svg>
                  </span>
                  <input type="email"
                         class="god-native-input"
                         name="company_email"
                         id="company_email"
                         value="{{ $account->company_email ?? '' }}"
                         placeholder="No email provided"
                         readonly
                         tabindex="-1" />
                  <span class="god-input-right-lock" title="View only">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                      <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                      <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                    </svg>
                  </span>
                </div>
              </div>

              <!-- ROW 6, COL 2: Official Phone Number (View Only) -->
              <div class="god-field-wrap">
                <div class="god-field-header">
                  <label class="god-field-label" for="company_phone">Official Phone Number <span class="text-muted ms-1" style="font-size:10.5px; font-weight:500;">(View Only)</span></label>
                </div>
                <div class="god-input-box is-readonly" id="company_phone_input_box" title="Official company phone is view-only">
                  <span class="god-input-left-icon">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                      <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/>
                    </svg>
                  </span>
                  <input type="tel"
                         class="god-native-input"
                         name="company_phone"
                         id="company_phone"
                         value="{{ $account->company_phone ?? '' }}"
                         placeholder="No phone provided"
                         readonly
                         tabindex="-1" />
                  <span class="god-input-right-lock" title="View only">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                      <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                      <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                    </svg>
                  </span>
                </div>
              </div>

            </div>
          </div>

          <!-- RIGHT COLUMN: Company Logo & Super Admin Quick Tips -->
          <div class="god-logo-column d-flex flex-column gap-3">
            
            <!-- Card 1: Company Logo -->
            <div class="god-card client-logo-preview-box">
              <!-- Header -->
              <div class="god-card-header">
                <div class="god-card-header-icon yellow-icon">
                  <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#ca8a04" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/>
                  </svg>
                </div>
                <div class="god-card-header-titles">
                  <h3 class="god-card-title">Company Logo</h3>
                  <p class="god-card-subtitle">Official organization brand avatar</p>
                </div>
              </div>

              <!-- Logo Surface with overlay pencil button -->
              <div class="god-logo-preview-box position-relative">
                <div class="god-logo-surface {{ $hasRealLogo ? 'has-image' : '' }}" id="company_logo_box" style="{{ $hasRealLogo ? "background-image: url('{$logoUrl}');" : '' }}">
                  @if(!$hasRealLogo)
                    <div class="god-logo-placeholder" id="logo_placeholder">
                      <svg width="44" height="44" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/>
                      </svg>
                      <span class="placeholder-title">Company Logo</span>
                      <span class="placeholder-sub">Click Change Logo or auto-fetch</span>
                    </div>
                  @endif
                </div>

                <!-- Pencil edit overlay button -->
                <label for="company_logo_input" class="position-absolute cursor-pointer" style="top:12px; right:12px; width:28px; height:28px; border-radius:50%; background:#ffffff; border:1px solid #cbd5e1; display:flex; align-items:center; justify-content:center; box-shadow:0 1px 3px rgba(0,0,0,0.1); color:#475569;" title="Change Logo">
                  <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z"></path>
                  </svg>
                </label>
              </div>

              <div class="text-center text-muted mt-2" style="font-size:10.5px; color:#94a3b8 !important;">Allowed file types: PNG, JPG, JPEG (Max 2MB)</div>

              <!-- Logo Actions -->
              <div class="god-logo-actions mt-2">
                <label class="god-logo-btn god-change-logo-btn" for="company_logo_input">
                  <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/>
                  </svg>
                  <span>Change Logo</span>
                  <input type="file" accept="image/png, image/jpeg, image/jpg, image/svg+xml" name="logo" id="company_logo_input" style="display:none;" onchange="handleLogoChange(event)">
                </label>
                <button type="button" class="god-logo-btn god-remove-logo-btn" id="client_remove_logo_btn" onclick="removeLogo()" style="{{ $hasRealLogo ? 'display:inline-flex;' : 'display:none;' }}">
                  <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                  <span>Remove</span>
                </button>
              </div>

              <div class="god-logo-status-banner {{ $hasRealLogo ? 'is-detected' : 'is-empty' }} mt-2" id="logo_status_banner">
                @if($hasRealLogo)
                  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
                  <span>Official logo detected</span>
                @else
                  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/></svg>
                  <span>No logo detected yet</span>
                @endif
              </div>
            </div>

            <!-- Card 2: Super Admin Quick Tips (Pixel-matched to media_1792740844508.png) -->
            <div class="god-card client-helper-card" style="padding:14px 16px !important; background:#ffffff; border:1px solid #e2e8f0; border-radius:12px;">
              <div class="d-flex align-items-center gap-2 mb-2">
                <span style="font-size:14px;">💡</span>
                <h4 style="font-size:12.5px; font-weight:700; color:#1e293b; margin:0;">Super Admin Quick Tips</h4>
              </div>
              <ul style="list-style:none; padding-left:0; margin:0; font-size:11px; color:#475569; line-height:1.55;">
                <li class="d-flex align-items-start gap-2 mb-2">
                  <span style="color:#f59e0b; font-size:12px; line-height:1;">•</span>
                  <span>Maintaining correct official websites enables automated talent exchange matching.</span>
                </li>
                <li class="d-flex align-items-start gap-2 mb-2">
                  <span style="color:#f59e0b; font-size:12px; line-height:1;">•</span>
                  <span>Assigning the appropriate industry categorizes requisition flows.</span>
                </li>
                <li class="d-flex align-items-start gap-2">
                  <span style="color:#f59e0b; font-size:12px; line-height:1;">•</span>
                  <span>Logo changes update globally across all tenant portals and job postings.</span>
                </li>
              </ul>
            </div>

          </div>

        </div>
      </div>

      <!-- =====================================================================
           PAGE 2: ABOUT THE COMPANY (Exact Design System Parity with tab-details)
           ===================================================================== -->
      <div class="pb-5 client-step-pane client-desc-step" id="step_pane_2" data-step="2">
        <div class="client-step-layout client-description-layout">
          
          <!-- MAIN COLUMN: About the Company Editor -->
          <div class="client-main-column">
            <section class="card client-section-card client-desc-card">
              <!-- Header bar matching reference design -->
              <div class="card-header border-0 d-flex align-items-center">
                <div class="d-flex align-items-center" style="gap:12px; text-align:left !important;">
                  <div style="width:36px; height:36px; border-radius:9px; background:#fef9c3; display:inline-flex; align-items:center; justify-content:center; color:#ca8a04; flex-shrink:0;">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#ca8a04" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                      <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                      <polyline points="14 2 14 8 20 8"></polyline>
                      <line x1="16" y1="13" x2="8" y2="13"></line>
                      <line x1="16" y1="17" x2="8" y2="17"></line>
                      <line x1="10" y1="9" x2="8" y2="9"></line>
                    </svg>
                  </div>
                  <div style="text-align:left !important;">
                    <h3 class="client-section-heading" style="font-size:14px; font-weight:700; color:#101828; margin:0 0 2px 0; text-align:left !important;">About the Company</h3>
                    <p class="client-section-copy" style="font-size:11px; color:#64748b; margin:0; text-align:left !important;">Give recruiters a clear understanding of what this company does.</p>
                  </div>
                </div>
              </div>

              <div class="card-body" style="padding:14px 18px !important;">
                <div class="client-editor-wrap">
                  <div class="d-flex align-items-center justify-content-between mb-1 client-desc-label-row">
                    <label class="form-label mb-0" style="font-size:12px; font-weight:700; color:#0f172a;">Company Description <span class="text-danger">*</span></label>
                  </div>
                  <p style="font-size:11.5px; color:#64748b; margin:0 0 8px 0;">Briefly describe the company's business, products/services, industry and key strengths.</p>

                  <div class="client-editor-box" id="client_editor_box">
                    <div id="kt_client_description_quill" style="min-height:120px; font-size:12.5px; color:#1e293b;">{!! $initialDesc !!}</div>
                  </div>
                  <textarea id="kt_docs_tinymce_hidden" name="kt_docs_tinymce_hidden" style="display:none !important;" aria-hidden="true">{{ $initialDesc }}</textarea>
                  <textarea id="hidden_description" name="description" style="display:none !important;" aria-hidden="true">{{ $initialDesc }}</textarea>
                  
                  <div class="d-flex justify-content-end mt-1">
                    <span class="client-char-count" style="font-size:11px; font-weight:500; color:#94a3b8;"><span id="client_desc_count">{{ $initialDescCount }}</span> / 2,000</span>
                  </div>
                </div>

                <!-- Tips banner at bottom matching reference design (Collapsible) -->
                <div class="client-info-banner client-tips-accordion" id="client_tips_banner" style="background:#f0f7ff; border:1px solid #dbeafe; border-radius:10px; padding:12px 16px !important; margin-top:12px !important; margin-bottom:0 !important;">
                  <div class="d-flex align-items-center justify-content-between cursor-pointer" id="client_tips_toggle" style="cursor:pointer; user-select:none;">
                    <div class="d-flex align-items-center gap-2" style="font-size:12px; font-weight:600; color:#1d4ed8;">
                      <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" class="flex-shrink-0">
                        <path d="M9 18h6"></path>
                        <path d="M10 22h4"></path>
                        <path d="M12 2a7 7 0 0 0-7 7c0 2.38 1.19 4.47 3 5.74V17a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1v-2.26c1.81-1.27 3-3.36 3-5.74a7 7 0 0 0-7-7z"></path>
                      </svg>
                      <span>Tips for a great company description</span>
                    </div>
                    <span id="client_tips_chevron" style="color:#2563eb; font-size:12px; font-weight:700; transition:transform 0.2s ease;">
                      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="18 15 12 9 6 15"></polyline></svg>
                    </span>
                  </div>
                  <div id="client_tips_content" style="margin-top:10px;">
                    <ul style="list-style:none; padding-left:0; margin:0; font-size:11.5px; color:#334155; line-height:1.7;">
                      <li class="d-flex align-items-center gap-2 mb-1">
                        <svg width="14" height="14" viewBox="0 0 20 20" fill="#10b981" class="flex-shrink-0"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                        <span>Include a brief overview of the company and what they do.</span>
                      </li>
                      <li class="d-flex align-items-center gap-2 mb-1">
                        <svg width="14" height="14" viewBox="0 0 20 20" fill="#10b981" class="flex-shrink-0"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                        <span>Mention their mission, vision and key strengths.</span>
                      </li>
                      <li class="d-flex align-items-center gap-2">
                        <svg width="14" height="14" viewBox="0 0 20 20" fill="#10b981" class="flex-shrink-0"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                        <span>This information helps recruiters understand the client better.</span>
                      </li>
                    </ul>
                  </div>
                </div>
              </div>
            </section>
          </div>

          <!-- RIGHT SIDEBAR: Service Offerings & AI Assist -->
          <aside class="client-side-column">
            
            <!-- Card 1: Service Offerings -->
            <div class="card client-helper-card client-service-offerings-card mb-3" style="background:#ffffff; border:1px solid #e2e8f0; border-radius:12px; box-shadow:0 1px 3px rgba(0,0,0,0.03); overflow:hidden; margin-bottom:12px !important;">
              <div class="card-header border-0 d-flex align-items-center" style="background:#ffffff; border-bottom:1px solid #f8fafc !important; padding:12px 16px !important; justify-content:flex-start !important;">
                <div class="d-flex align-items-center" style="gap:10px; text-align:left !important;">
                  <div style="width:34px; height:34px; border-radius:8px; background:#fef9c3; display:inline-flex; align-items:center; justify-content:center; color:#ca8a04; flex-shrink:0;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#ca8a04" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                      <polygon points="12 2 2 7 12 12 22 7 12 2"></polygon>
                      <polyline points="2 17 12 22 22 17"></polyline>
                      <polyline points="2 12 12 17 22 12"></polyline>
                    </svg>
                  </div>
                  <div style="text-align:left !important;">
                    <h4 style="font-size:14px; font-weight:700; color:#101828; margin:0 0 1px 0; text-align:left !important;">Service Offerings</h4>
                    <p style="font-size:11px; color:#64748b; margin:0; text-align:left !important;">Select the services that this company provides or is known for.</p>
                  </div>
                </div>
              </div>

              <div class="card-body" style="padding:14px 16px !important;">
                <input type="hidden" name="tags" id="tags" value="{{ $encodedTags }}">

                <!-- Search services input bar -->
                <div class="position-relative mb-3">
                  <span class="position-absolute" style="top:50%; left:11px; transform:translateY(-50%); pointer-events:none; color:#94a3b8;">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                      <circle cx="11" cy="11" r="8"></circle>
                      <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                    </svg>
                  </span>
                  <input type="text" id="client_services_search" class="form-control" placeholder="Search services (e.g. Software Development, Cloud, QA...)" style="height:35px; padding-left:32px; font-size:11.5px; border:1px solid #e2e8f0; border-radius:8px; background:#ffffff; color:#1e293b;" autocomplete="off">
                </div>

                <!-- Error message if empty -->
                <div class="text-danger mb-2" id="client_tags_error" style="display:none; font-size:11px; font-weight:600;">
                  <i class="la la-exclamation-circle"></i> Please select or add at least 1 Service Offering.
                </div>

                <!-- Section: Selected Services (count) + Clear all -->
                <div class="mb-3" id="client_selected_services_section">
                  <div class="d-flex align-items-center justify-content-between mb-2">
                    <span style="font-size:11.5px; font-weight:700; color:#1e293b;">Selected Services (<span id="client_selected_count">{{ count($parsedTagsArray) }}</span>)</span>
                    <button type="button" class="btn btn-link p-0" id="client_clear_all_tags_btn" style="font-size:11px; font-weight:500; color:#2563eb; text-decoration:none; cursor:pointer; background:none; border:none;">Clear all</button>
                  </div>
                  <div id="client_selected_chips_container" class="d-flex flex-wrap gap-2" style="min-height:24px;">
                    @forelse($parsedTagsArray as $tag)
                      <div class="client-selected-chip">
                        <span>{{ $tag }}</span>
                        <span class="client-chip-remove" data-tag="{{ $tag }}" title="Remove">✕</span>
                      </div>
                    @empty
                      <span class="text-muted" style="font-size:11px; font-style:italic;">No services selected yet. Choose from suggestions below or search above.</span>
                    @endforelse
                  </div>
                </div>

                <!-- Section: Suggested Services (Industry) + Add All Recommended -->
                <div>
                  <div class="d-flex align-items-center justify-content-between mb-2">
                    <span id="client_suggested_label" style="font-size:11.5px; font-weight:700; color:#1e293b;">Suggested Services ({{ $selectedIndName ?: 'Information Technology & Services' }})</span>
                    <button type="button" class="btn btn-link p-0" id="client_add_all_suggested_btn" onclick="window.addAllSuggestedServices ? window.addAllSuggestedServices() : false;" style="font-size:11px; font-weight:500; color:#2563eb; text-decoration:none; display:inline-flex; align-items:center; gap:4px; cursor:pointer; background:none; border:none;">
                      <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                      <span>Add All Recommended</span>
                    </button>
                  </div>
                  
                  <div class="client-suggested-list d-flex flex-wrap gap-2" id="client_suggested_services">
                    @foreach($defaultSuggestedServices as $idx => $service)
                      @php
                        $isSel = in_array(strtolower($service), array_map('strtolower', $parsedTagsArray));
                        $isExtra = $idx >= 9;
                      @endphp
                      <button type="button" class="btn btn-sm client-suggested-pill {{ $isSel ? 'is-selected' : '' }}" data-value="{{ $service }}" style="{{ ($isSel || $isExtra) ? 'display:none;' : '' }}">
                        <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                        <span>{{ $service }}</span>
                      </button>
                    @endforeach
                  </div>

                  <!-- View more recommended services link -->
                  <div class="mt-2" id="client_view_more_services_wrap" style="display:block;">
                    <a href="javascript:void(0);" id="client_toggle_more_services" style="font-size:11px; font-weight:600; color:#2563eb; text-decoration:none; display:inline-flex; align-items:center; gap:4px;">
                      <span>View <span id="client_more_services_count">4</span> more recommended services</span> &rarr;
                    </a>
                  </div>
                </div>
              </div>
            </div>

            <!-- Card 2: AI-Generated Company Description -->
            <div class="card client-helper-card client-ai-assist-card" style="background:#faf8ff; border:1px solid #ede9fe; border-radius:12px; padding:14px 16px !important; margin-bottom:0 !important;">
              <div class="d-flex align-items-center justify-content-between mb-1">
                <div class="d-flex align-items-center gap-2">
                  <div style="width:26px; height:26px; border-radius:7px; background:#ede9fe; display:inline-flex; align-items:center; justify-content:center; color:#7040f5; flex-shrink:0;">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#7040f5" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                      <path d="m12 3-1.9 5.8a2 2 0 0 1-1.3 1.3L3 12l5.8 1.9a2 2 0 0 1 1.3 1.3L12 21l1.9-5.8a2 2 0 0 1 1.3-1.3L21 12l-5.8-1.9a2 2 0 0 1-1.3-1.3L12 3z"/>
                    </svg>
                  </div>
                  <h4 style="font-size:13.5px; font-weight:700; color:#101828; margin:0; letter-spacing:-0.2px;">AI-Generated Company Description</h4>
                </div>
                <span class="badge" style="background:#ede9fe; color:#7040f5; font-weight:600; font-size:10px; padding:2px 7px; border-radius:6px;">Beta</span>
              </div>
              <p style="font-size:11.5px; color:#64748b; line-height:1.45; margin-bottom:10px;">AI-powered suggestion based on publicly available company information.</p>

              <!-- Description Box -->
              <div id="client_ai_suggested_desc" class="client-ai-suggested-desc" style="background:#ffffff; border:1px solid #f1f5f9; border-radius:8px; padding:12px 14px !important; font-size:12.5px; color:#334155; line-height:1.6; margin-bottom:12px !important; max-height:110px; overflow-y:auto;">{{ $account->company_name ?? 'This organization' }} is an established industry leader providing comprehensive enterprise solutions, operational excellence, and specialized technical expertise. The company focuses on scalable innovation, agile delivery, and verified client outcomes.</div>

              <!-- Footer Row -->
              <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                <div class="d-flex align-items-center gap-2" style="font-size:10.5px; color:#64748b;">
                  <a href="{{ $account->website ? (str_starts_with($account->website, 'http') ? $account->website : 'https://' . $account->website) : 'javascript:void(0);' }}" target="{{ $account->website ? '_blank' : '_self' }}" id="client_ai_source_link" style="color:#2563eb; text-decoration:none; display:inline-flex; align-items:center; gap:2px;">
                    <span>Source: Company Website</span>
                    <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path><polyline points="15 3 21 3 21 9"></polyline><line x1="10" y1="14" x2="21" y2="3"></line></svg>
                  </a>
                  <span class="d-inline-flex align-items-center gap-1" style="color:#475569; font-weight:500;">
                    <span style="width:6px; height:6px; border-radius:50%; background:#10b981; display:inline-block;"></span> Verified
                  </span>
                  <span>Updated recently</span>
                </div>

                <div class="d-flex align-items-center gap-2">
                  <button type="button" class="btn btn-sm" id="client_review_edit_desc_btn" style="background:#ffffff; border:1px solid #c4b5fd; color:#6d28d9; border-radius:7px; padding:5px 12px; font-size:11.5px; font-weight:600; cursor:pointer; transition:all 0.15s ease;" onmouseover="this.style.background='#f5f3ff';" onmouseout="this.style.background='#ffffff';">
                    Review & Edit
                  </button>
                  <button type="button" class="btn btn-sm" id="client_apply_desc_suggestion" onclick="window.applyDescriptionSuggestion ? window.applyDescriptionSuggestion() : false;" style="background:#7040f5; border:none; color:#ffffff; font-weight:600; font-size:11.5px; border-radius:7px; padding:6px 14px; display:inline-flex; align-items:center; gap:5px; box-shadow:0 1px 3px rgba(112,64,245,0.3); cursor:pointer; transition:all 0.15s ease;" onmouseover="this.style.background='#5b21b6';" onmouseout="this.style.background='#7040f5';">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
                    <span>Apply Suggestion</span>
                  </button>
                </div>
              </div>
            </div>

          </aside>

        </div>
      </div>

      <!-- =====================================================================
           PAGE 3: ADDRESS & LOCATIONS (Exact Design System Parity with tab-locations)
           ===================================================================== -->
      <div class="pb-5 client-step-pane client-address-step" id="step_pane_3" data-step="3">
        
        <!-- TOP INFORMATIONAL BANNER -->
        <div class="client-addr-top-banner mb-3">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="flex-shrink-0">
            <circle cx="12" cy="12" r="10"></circle>
            <line x1="12" y1="16" x2="12" y2="12"></line>
            <line x1="12" y1="8" x2="12.01" y2="8"></line>
          </svg>
          <span>The first location entered is designated as the primary headquarters. You can adjust headquarters or add branch offices at any time.</span>
        </div>

        <div class="client-step-layout client-address-layout">
          
          <!-- LEFT MAIN COLUMN: Company Locations -->
          <div class="client-main-column">
            
            <!-- CARD 1: PRIMARY ADDRESS INFORMATION CARD -->
            <section class="card client-section-card client-addr-primary-card mb-3" id="client_addr_card_primary">
              <div class="card-header border-0 d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-3">
                  <div class="client-addr-card-icon-box">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#ca8a04" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                      <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                      <polyline points="9 22 9 12 15 12 15 22"></polyline>
                    </svg>
                  </div>
                  <div>
                    <h3 class="client-section-heading mb-0">Address Information</h3>
                    <p class="client-section-copy mb-0">Primary corporate headquarters and registered office location.</p>
                  </div>
                </div>
                <span class="badge client-hq-badge">
                  HEADQUARTERS
                </span>
              </div>

              <div class="card-body">
                <!-- SUB-CARD: Headquarters & Registered Office -->
                <div class="client-subcard client-hq-subcard" id="client_addr_card_0">
                  <div class="client-subcard-header d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom">
                    <div class="d-flex align-items-center gap-2">
                      <div class="client-subcard-icon-box">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#ca8a04" stroke-width="2">
                          <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                          <polyline points="9 22 9 12 15 12 15 22"></polyline>
                        </svg>
                      </div>
                      <div>
                        <h4 class="client-subcard-title mb-0">Headquarters (Main Office)</h4>
                        <span class="client-subcard-subtitle">Primary operational and official billing location</span>
                      </div>
                    </div>
                    <span class="badge badge-hq-pill">PRIMARY</span>
                  </div>

                  <div class="client-subcard-body">
                    <!-- Field: City -->
                    <div class="client-addr-field mb-3">
                      <label class="form-label mb-1" for="city_search_0">City / Location <span class="text-danger">*</span></label>
                      <div class="position-relative client-addr-input-wrap">
                        <span class="position-absolute client-addr-field-icon-left">
                          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="11" cy="11" r="8"></circle>
                            <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                          </svg>
                        </span>
                        <input type="text" class="form-control client-addr-input client-addr-city-search" id="city_search_0" data-cid="0" value="{{ $primary_city }}" placeholder="Search for a city..." autocomplete="off" spellcheck="false">
                        <span class="position-absolute client-addr-field-icon-right">
                          <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="6 9 12 15 18 9"></polyline>
                          </svg>
                        </span>
                        <div class="client-addr-city-dropdown" id="city_dropdown_0"></div>
                        <input type="hidden" name="kt_tagify_work_location[]" id="kt_tagify_work_location_0" value="{{ $primary_tagify_json }}">
                      </div>
                      <div class="client-addr-error text-danger mt-1" id="city_err_0" style="display:none;">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/></svg>
                        <span>City selection is required for headquarters.</span>
                      </div>
                    </div>

                    <!-- Field: Full Address -->
                    <div class="client-addr-field">
                      <label class="form-label mb-1" for="address_0">Full Address <span class="text-danger">*</span></label>
                      <div class="position-relative client-addr-textarea-wrap">
                        <span class="position-absolute client-addr-field-icon-top">
                          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"></path>
                            <circle cx="12" cy="10" r="3"></circle>
                          </svg>
                        </span>
                        <textarea class="form-control client-addr-textarea" name="address[]" id="address_0" rows="2" maxlength="500" placeholder="Building name, street, area, landmark, pincode, etc.">{{ $primary_address }}</textarea>
                      </div>
                      <div class="d-flex align-items-center justify-content-between mt-1">
                        <span class="text-muted client-field-hint">Provide complete registered headquarters address.</span>
                        <span class="client-addr-counter"><span id="address_char_count_0">{{ strlen($primary_address) }}</span>/500</span>
                      </div>
                      <div class="client-addr-error text-danger mt-1" id="address_err_0" style="display:none;">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/></svg>
                        <span>Full Address is required for headquarters.</span>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </section>

            <!-- CARD 2: DEDICATED ADDITIONAL LOCATIONS CARD -->
            <section class="card client-section-card client-addr-additional-card" id="client_addr_additional_card">
              <div class="card-header border-0 d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-3">
                  <div class="client-addr-card-icon-box">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#ca8a04" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                      <path d="M3 21h18"></path>
                      <path d="M5 21V7l8-4v18"></path>
                      <path d="M19 21V11l-6-4"></path>
                    </svg>
                  </div>
                  <div>
                    <h3 class="client-section-heading mb-0">Additional Locations (Optional)</h3>
                    <p class="client-section-copy mb-0">Add branch offices, development centres or regional hubs.</p>
                  </div>
                </div>
                <span class="badge client-branch-count-badge" id="additional_locations_count_badge">
                  {{ count($additional_addresses) }} {{ count($additional_addresses) === 1 ? 'Location' : 'Locations' }}
                </span>
              </div>

              <div class="card-body">
                <!-- Subtle Info Notice -->
                <div class="client-addr-notice mb-3">
                  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#ca8a04" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="flex-shrink-0">
                    <circle cx="12" cy="12" r="10"></circle>
                    <line x1="12" y1="16" x2="12" y2="12"></line>
                    <line x1="12" y1="8" x2="12.01" y2="8"></line>
                  </svg>
                  <span>Additional locations are optional. If added, both <strong>City</strong> and <strong>Full Address</strong> are required (or click <strong>Remove</strong> if not needed).</span>
                </div>

                <!-- Centered Empty State -->
                <div class="client-addr-empty-state {{ empty($additional_addresses) ? '' : 'd-none' }}" id="client_additional_empty_state" style="{{ empty($additional_addresses) ? 'display:flex;' : 'display:none;' }}">
                  <div class="empty-state-icon-circle">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                      <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                      <circle cx="12" cy="10" r="3"></circle>
                    </svg>
                  </div>
                  <h5 class="empty-state-title">No additional locations added yet</h5>
                  <p class="empty-state-subtitle">Add branch offices, development centres or regional hubs for this organization.</p>
                  <button type="button" class="btn btn-add-additional-location btn-add-first-location" id="btn_add_first_location">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                    <span>Add Additional Location</span>
                  </button>
                </div>

                <!-- Dynamic Sub-Cards Container -->
                <div class="client-additional-subcards-wrap" id="additional_addresses_container">
                  @if (!empty($additional_addresses))
                    @foreach ($additional_addresses as $addIdx => $addrItem)
                      <div class="client-subcard client-branch-subcard client-branch-card" id="clientAddr_{{ $addrItem['id'] }}" data-cid="{{ $addrItem['id'] }}">
                        <div class="client-subcard-header d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom">
                          <div class="d-flex align-items-center gap-2">
                            <div class="client-subcard-icon-box office-icon">
                              <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="4" y="2" width="16" height="20" rx="2" ry="2"/>
                                <path d="M9 22v-4h6v4"/>
                                <line x1="8" y1="6" x2="8.01" y2="6"/>
                                <line x1="16" y1="6" x2="16.01" y2="6"/>
                                <line x1="12" y1="6" x2="12.01" y2="6"/>
                                <line x1="8" y1="10" x2="8.01" y2="10"/>
                                <line x1="12" y1="10" x2="12.01" y2="10"/>
                                <line x1="16" y1="10" x2="16.01" y2="10"/>
                                <line x1="8" y1="14" x2="8.01" y2="14"/>
                                <line x1="12" y1="14" x2="12.01" y2="14"/>
                                <line x1="16" y1="14" x2="16.01" y2="14"/>
                              </svg>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                              <h4 class="client-subcard-title client-branch-title mb-0">Additional Location {{ $addIdx + 1 }}</h4>
                              <span class="badge badge-branch-tag">BRANCH</span>
                            </div>
                          </div>
                          <div class="d-flex align-items-center gap-2">
                            <button type="button" class="btn btn-xs btn-set-primary" onclick="window.clientLocationManager ? window.clientLocationManager.setBranchAsHq({{ $addrItem['id'] }}) : false" title="Set as Primary">
                              <svg width="12" height="12" viewBox="0 0 24 24" fill="#f59e0b" stroke="#d97706" stroke-width="1.2" class="me-1"><path d="M5 16L3 5l5.5 5L12 4l3.5 6L21 5l-2 11H5m14 3c0 .6-.4 1-1 1H6c-.6 0-1-.4-1-1v-1h14v1z"/></svg>
                              <span>Set as Primary</span>
                            </button>
                            <button type="button" class="btn btn-xs btn-remove-subcard client-branch-remove-btn" onclick="removeAddressCard(this, 'clientAddr_{{ $addrItem['id'] }}')" title="Remove Location">
                              <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="me-1"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                              <span>Remove</span>
                            </button>
                          </div>
                        </div>

                        <div class="client-subcard-body">
                          <!-- Row 1: City / Location -->
                          <div class="client-addr-field mb-2">
                            <label class="form-label mb-1" for="branch_city_search_{{ $addrItem['id'] }}">City / Location <span class="text-danger">*</span></label>
                            <div class="position-relative client-addr-input-wrap">
                              <span class="position-absolute client-addr-field-icon-left">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                              </span>
                              <input type="text" class="form-control client-addr-input client-addr-city-search branch-city-search" id="branch_city_search_{{ $addrItem['id'] }}" data-cid="{{ $addrItem['id'] }}" value="{{ $addrItem['city'] }}" placeholder="Search for a city.." autocomplete="off">
                              <span class="position-absolute client-addr-field-icon-right">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"></polyline></svg>
                              </span>
                              <div class="client-addr-city-dropdown branch-city-dropdown" id="branch_city_dropdown_{{ $addrItem['id'] }}"></div>
                              <input type="hidden" name="kt_tagify_work_location[]" id="kt_tagify_work_location_{{ $addrItem['id'] }}" value='{{ $addrItem['tagify_json'] }}'>
                            </div>
                            <div class="client-addr-error text-danger mt-1" id="branch_city_err_{{ $addrItem['id'] }}" style="display:none;">City selection is required.</div>
                          </div>

                          <!-- Row 2: Full Address -->
                          <div class="client-addr-field">
                            <div class="position-relative client-addr-textarea-wrap">
                              <span class="position-absolute client-addr-field-icon-top">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg>
                              </span>
                              <textarea class="form-control client-addr-textarea" name="address[]" id="address_{{ $addrItem['id'] }}" rows="2" maxlength="500" placeholder="Enter complete branch address..">{{ $addrItem['address'] }}</textarea>
                            </div>
                            <div class="d-flex align-items-center justify-content-between mt-1">
                              <span class="text-muted client-field-hint">Include building, street, area, city and postal code.</span>
                              <span class="client-addr-counter"><span id="address_char_count_{{ $addrItem['id'] }}">{{ strlen($addrItem['address']) }}</span> / 500</span>
                            </div>
                            <div class="client-addr-error text-danger mt-1" id="branch_address_err_{{ $addrItem['id'] }}" style="display:none;">Full Address is required.</div>
                          </div>
                        </div>
                      </div>
                    @endforeach
                  @endif
                </div>

                <!-- Additional Locations Bottom Action CTA -->
                <div class="client-add-location-cta-wrap text-center mt-3 {{ empty($additional_addresses) ? 'd-none' : '' }}" id="additional_location_cta_wrap" style="{{ empty($additional_addresses) ? 'display:none;' : 'display:flex;' }}">
                  <button type="button" class="btn btn-add-additional-location" id="btn_add_another_address">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                    <span>Add Additional Location</span>
                  </button>
                </div>
              </div>
            </section>

          </div>

          <!-- RIGHT SIDEBAR: AI Location Discovery & Tips -->
          <aside class="client-side-column">
            
            <!-- AI Location Discovery Card -->
            <div class="card client-helper-card client-ai-location-card">
              <div class="d-flex align-items-center justify-content-between mb-1">
                <div class="d-flex align-items-center gap-2">
                  <div class="client-ai-icon-box">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#7040f5" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                      <path d="m12 3-1.9 5.8a2 2 0 0 1-1.3 1.3L3 12l5.8 1.9a2 2 0 0 1 1.3 1.3L12 21l1.9-5.8a2 2 0 0 1 1.3-1.3L21 12l-5.8-1.9a2 2 0 0 1-1.3-1.3L12 3z"/>
                    </svg>
                  </div>
                  <h4 class="client-ai-title">AI Location Discovery</h4>
                </div>
                <span class="badge badge-ai-beta">Beta</span>
              </div>
              <p class="client-ai-desc">We found 3 locations from the company website. Review and add them to your client profile.</p>

              <!-- Discovered Location 1: Pune (Recommended HQ) -->
              <div class="client-ai-loc-item mb-2" id="ai_loc_card_0">
                <div class="d-flex align-items-start justify-content-between mb-1">
                  <div class="d-flex align-items-start gap-2">
                    <div class="ai-loc-symbol-box symbol-hq">
                      <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="2"><path d="M3 21h18"/><path d="M5 21V7l8-4v18"/><path d="M19 21V11l-6-4"/></svg>
                    </div>
                    <div>
                      <div class="d-flex align-items-center gap-2">
                        <span class="ai-loc-city-name">Pune, Maharashtra, India</span>
                        <span class="badge badge-ai-recommended">Recommended</span>
                      </div>
                      <div class="ai-loc-type-label">Corporate Office / Headquarters</div>
                      <p class="ai-loc-sub-note">Listed as primary office on the company website.</p>
                    </div>
                  </div>
                </div>
                <div class="d-flex align-items-center justify-content-between mt-2 pt-1 border-top flex-wrap gap-2 ai-loc-footer-border">
                  <div class="d-flex align-items-center gap-2 ai-loc-meta-text">
                    <a href="{{ $account->website ? (str_starts_with($account->website, 'http') ? $account->website : 'https://' . $account->website) : 'javascript:void(0);' }}" target="{{ $account->website ? '_blank' : '_self' }}" class="client-ai-source-link">
                      <span>Source: Website</span>
                      <svg width="9" height="9" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path><polyline points="15 3 21 3 21 9"></polyline><line x1="10" y1="14" x2="21" y2="3"></line></svg>
                    </a>
                    <span class="d-inline-flex align-items-center gap-1 ai-loc-verified">
                      <span class="verified-dot"></span> Verified
                    </span>
                  </div>
                  <div class="d-flex align-items-center gap-2" id="ai_loc_actions_0">
                    <button type="button" class="btn btn-sm btn-ai-set-hq" onclick="window.clientLocationManager ? window.clientLocationManager.addAiLocation(0, true) : false;">
                      Set as Headquarters
                    </button>
                    <button type="button" class="btn btn-sm btn-ai-add-office" onclick="window.clientLocationManager ? window.clientLocationManager.addAiLocation(0, false) : false;">
                      Add as Office
                    </button>
                  </div>
                </div>
              </div>

              <!-- Discovered Location 2: Bengaluru (Development Centre) -->
              <div class="client-ai-loc-item mb-2" id="ai_loc_card_1">
                <div class="d-flex align-items-start justify-content-between mb-1">
                  <div class="d-flex align-items-start gap-2">
                    <div class="ai-loc-symbol-box symbol-office">
                      <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#475569" stroke-width="2"><path d="M3 21h18"/><path d="M5 21V7l8-4v18"/><path d="M19 21V11l-6-4"/></svg>
                    </div>
                    <div>
                      <span class="ai-loc-city-name">Bengaluru, Karnataka, India</span>
                      <div class="ai-loc-type-label">Development Centre</div>
                      <p class="ai-loc-sub-note">Listed under company locations page.</p>
                    </div>
                  </div>
                </div>
                <div class="d-flex align-items-center justify-content-between mt-2 pt-1 border-top flex-wrap gap-2 ai-loc-footer-border">
                  <div class="d-flex align-items-center gap-2 ai-loc-meta-text">
                    <a href="{{ $account->website ? (str_starts_with($account->website, 'http') ? $account->website : 'https://' . $account->website) : 'javascript:void(0);' }}" target="{{ $account->website ? '_blank' : '_self' }}" class="client-ai-source-link">
                      <span>Source: Website</span>
                      <svg width="9" height="9" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path><polyline points="15 3 21 3 21 9"></polyline><line x1="10" y1="14" x2="21" y2="3"></line></svg>
                    </a>
                    <span class="d-inline-flex align-items-center gap-1 ai-loc-likely">
                      <span class="likely-dot"></span> Likely location
                    </span>
                  </div>
                  <div class="d-flex align-items-center gap-2" id="ai_loc_actions_1">
                    <button type="button" class="btn btn-sm btn-ai-add-office" onclick="window.clientLocationManager ? window.clientLocationManager.addAiLocation(1, false) : false;">
                      Add as Office
                    </button>
                  </div>
                </div>
              </div>

              <!-- Discovered Location 3: Mumbai (Office) -->
              <div class="client-ai-loc-item mb-3" id="ai_loc_card_2">
                <div class="d-flex align-items-start justify-content-between mb-1">
                  <div class="d-flex align-items-start gap-2">
                    <div class="ai-loc-symbol-box symbol-office">
                      <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#475569" stroke-width="2"><path d="M3 21h18"/><path d="M5 21V7l8-4v18"/><path d="M19 21V11l-6-4"/></svg>
                    </div>
                    <div>
                      <span class="ai-loc-city-name">Mumbai, Maharashtra, India</span>
                      <div class="ai-loc-type-label">Office</div>
                      <p class="ai-loc-sub-note">Listed as regional business office.</p>
                    </div>
                  </div>
                </div>
                <div class="d-flex align-items-center justify-content-between mt-2 pt-1 border-top flex-wrap gap-2 ai-loc-footer-border">
                  <div class="d-flex align-items-center gap-2 ai-loc-meta-text">
                    <a href="{{ $account->website ? (str_starts_with($account->website, 'http') ? $account->website : 'https://' . $account->website) : 'javascript:void(0);' }}" target="{{ $account->website ? '_blank' : '_self' }}" class="client-ai-source-link">
                      <span>Source: Website</span>
                      <svg width="9" height="9" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path><polyline points="15 3 21 3 21 9"></polyline><line x1="10" y1="14" x2="21" y2="3"></line></svg>
                    </a>
                    <span class="d-inline-flex align-items-center gap-1 ai-loc-likely">
                      <span class="likely-dot"></span> Likely location
                    </span>
                  </div>
                  <div class="d-flex align-items-center gap-2" id="ai_loc_actions_2">
                    <button type="button" class="btn btn-sm btn-ai-add-office" onclick="window.clientLocationManager ? window.clientLocationManager.addAiLocation(2, false) : false;">
                      Add as Office
                    </button>
                  </div>
                </div>
              </div>

              <!-- Bulk Action Row -->
              <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 pt-1">
                <button type="button" class="btn btn-sm btn-ai-add-all" id="btn_add_all_ai_locations" onclick="window.clientLocationManager ? window.clientLocationManager.addAllAiLocations() : false;">
                  <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="12 2 2 7 12 12 22 7 12 2"></polygon><polyline points="2 17 12 22 22 17"></polyline><polyline points="2 12 12 17 22 12"></polyline></svg>
                  <span>Add all 3 locations</span>
                </button>
                <a href="{{ $account->website ? (str_starts_with($account->website, 'http') ? $account->website : 'https://' . $account->website) : 'javascript:void(0);' }}" target="{{ $account->website ? '_blank' : '_self' }}" id="ai_view_source_link" class="ai-source-view-link">
                  <span>View source</span>
                  <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path><polyline points="15 3 21 3 21 9"></polyline><line x1="10" y1="14" x2="21" y2="3"></line></svg>
                </a>
              </div>
            </div>

            <!-- Quick Tips Card -->
            <div class="card client-helper-card client-addr-tips-card">
              <div class="d-flex align-items-center gap-2 mb-2">
                <div class="client-tips-icon-box">
                  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#854d0e" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M9 18h6"></path>
                    <path d="M10 22h4"></path>
                    <path d="M12 2a7 7 0 0 0-7 7c0 2.38 1.19 4.47 3 5.74V17a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1v-2.26c1.81-1.27 3-3.36 3-5.74a7 7 0 0 0-7-7z"></path>
                  </svg>
                </div>
                <h4 class="client-tips-heading">Quick Tips</h4>
              </div>
              <div class="client-tips-list">
                <div class="d-flex align-items-start gap-2 mb-1">
                  <svg width="13" height="13" viewBox="0 0 20 20" fill="#eab308" class="flex-shrink-0 mt-1"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                  <span>Start typing a city name to search our global database.</span>
                </div>
                <div class="d-flex align-items-start gap-2 mb-1">
                  <svg width="13" height="13" viewBox="0 0 20 20" fill="#eab308" class="flex-shrink-0 mt-1"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                  <span>The first location is marked as primary headquarters.</span>
                </div>
                <div class="d-flex align-items-start gap-2 mb-1">
                  <svg width="13" height="13" viewBox="0 0 20 20" fill="#eab308" class="flex-shrink-0 mt-1"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                  <span>Add branch offices or regional centres as additional locations.</span>
                </div>
                <div class="d-flex align-items-start gap-2">
                  <svg width="13" height="13" viewBox="0 0 20 20" fill="#eab308" class="flex-shrink-0 mt-1"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                  <span>Click &ldquo;Add as Office&rdquo; to quickly populate AI discovered locations.</span>
                </div>
              </div>
            </div>

          </aside>

        </div>
      </div>

      <!-- =====================================================================
           PAGE 4: REVIEW & CONFIRM
           ===================================================================== -->
      <div class="pb-5 client-step-pane" id="step_pane_4" data-step="4">
        <div class="client-review-layout">
          
          <!-- LEFT COLUMN: Scannable Cards -->
          <div class="d-flex flex-column gap-3">
            
            <!-- Card 1: Company Profile Banner -->
            <div class="card client-review-section-card">
              <div class="card-body p-4 d-flex align-items-center justify-content-between flex-wrap gap-3">
                <div class="d-flex align-items-center gap-3">
                  <div id="client_review_logo_box" class="client-review-avatar-box" style="{{ $hasRealLogo ? "background-image: url('{$logoUrl}'); background-size: contain; background-repeat: no-repeat; background-position: center;" : '' }}">
                    <span id="client_review_initials" style="{{ $hasRealLogo ? 'display:none;' : '' }}">
                      {{ strtoupper(substr($account->company_name ?? 'CO', 0, 2)) }}
                    </span>
                  </div>
                  <div>
                    <div class="d-flex align-items-center gap-2 mb-1">
                      <h3 class="client-review-company-name mb-0" id="rev_company_name">{{ $account->company_name ?? 'Company Name' }}</h3>
                      <span class="badge bg-light-warning text-warning border px-2 py-1" style="font-size:10.5px; font-weight:600;">SUPER ADMIN</span>
                    </div>
                    <div class="d-flex align-items-center flex-wrap gap-3 text-muted" style="font-size:11.5px;">
                      <span id="rev_website"><i class="fa fa-globe me-1"></i> {{ $account->website ?? 'No website' }}</span>
                      <span id="rev_email"><i class="fa fa-envelope me-1"></i> {{ $account->company_email ?? 'No email' }}</span>
                      <span id="rev_phone"><i class="fa fa-phone me-1"></i> {{ $account->company_phone ?? 'No phone' }}</span>
                      <span id="rev_career"><i class="fa fa-briefcase me-1"></i> {{ $account->career_page_link ?? 'No career page' }}</span>
                      <span id="rev_industry"><i class="fa fa-building me-1"></i> {{ $selectedIndName ?: 'Industry' }}</span>
                      <span id="rev_size"><i class="fa fa-users me-1"></i> {{ $selectedSizeLabel ?: '201 – 500 employees' }}</span>
                      <span id="rev_type"><i class="fa fa-tag me-1"></i> {{ $selectedTypeName ?: 'Company Type' }}</span>
                      <span id="rev_primary_location"><i class="fa fa-map-marker-alt me-1"></i> {{ $firstCityStr ?? ($primary_city ?? 'No location') }}</span>
                    </div>
                  </div>
                </div>
                <button type="button" class="btn btn-sm client-review-edit-btn" onclick="goToWizardStep(1)">
                  <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>
                  <span>Edit</span>
                </button>
              </div>
            </div>

            <!-- Card 2: Description & Services -->
            <div class="card client-review-section-card">
              <div class="card-header border-0 py-3 px-4 d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-2">
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#ca8a04" stroke-width="2">
                    <rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path>
                  </svg>
                  <strong style="font-size:13px; color:#0f172a;">Company Description &amp; Services</strong>
                </div>
                <button type="button" class="btn btn-sm client-review-edit-btn" onclick="goToWizardStep(2)">
                  <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>
                  <span>Edit</span>
                </button>
              </div>
              <div class="card-body px-4 py-3 pt-0">
                <div class="text-muted mb-3" id="rev_description_box" style="font-size:12px; line-height:1.6; max-height:120px; overflow-y:auto;">
                  {!! @$account->description ?: 'No description provided.' !!}
                </div>
                <div class="d-flex flex-wrap gap-2" id="client_review_service_badges">
                  <!-- Populated by populateReviewSummaries() -->
                </div>
              </div>
            </div>

            <!-- Card 3: Locations -->
            <div class="card client-review-section-card">
              <div class="card-header border-0 py-3 px-4 d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-2">
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#ca8a04" stroke-width="2">
                    <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle>
                  </svg>
                  <strong style="font-size:13px; color:#0f172a;">Locations &amp; Addresses</strong>
                </div>
                <button type="button" class="btn btn-sm client-review-edit-btn" onclick="goToWizardStep(3)">
                  <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>
                  <span>Edit</span>
                </button>
              </div>
              <div class="card-body px-4 py-3 pt-0" id="rev_locations_summary_box">
                <!-- Populated dynamically -->
              </div>
            </div>

          </div>

          <!-- RIGHT COLUMN: Verification & Status -->
          <div class="d-flex flex-column gap-3">
            
            <!-- Super Admin Verification Toggle Card -->
            <div class="verify-toggle-card {{ @$account->accountVerify ? 'is-verified' : '' }}" id="verify_toggle_box">
              <div class="d-flex align-items-center justify-content-between mb-2">
                <div class="d-flex align-items-center gap-2">
                  <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#10b981" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                  </svg>
                  <strong style="font-size:13px; color:#0f172a;">Account Verification</strong>
                </div>
                <label class="switch-custom">
                  <input type="checkbox" name="accountVerify" id="accountVerify" value="1" {{ @$account->accountVerify ? 'checked' : '' }} onchange="toggleVerifyVisual(this.checked)" />
                  <span class="slider-custom"></span>
                </label>
              </div>
              <p style="font-size:11px; color:#64748b; line-height:1.5; margin-bottom:8px;">
                Verified organizations enjoy trusted status, higher visibility in talent exchanges, and instant requisition indexing.
              </p>
              <div id="verify_status_badge">
                @if(@$account->accountVerify)
                  <span class="badge bg-light-success text-success fw-bold px-3 py-2">✓ VERIFIED BY SUPER ADMIN</span>
                @else
                  <span class="badge bg-light-warning text-warning fw-bold px-3 py-2">⏳ PENDING VERIFICATION</span>
                @endif
              </div>
            </div>

            <!-- Ready Checklist -->
            <div class="card client-helper-card">
              <div class="client-helper-title">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#10b981" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                <span>Ready to Save</span>
              </div>
              <ul style="list-style:none; padding-left:0; margin:0; font-size:11.5px; color:#334155; line-height:1.7;">
                <li class="d-flex align-items-center gap-2 mb-1">
                  <span style="color:#10b981;">✓</span> Company Profile Complete
                </li>
                <li class="d-flex align-items-center gap-2 mb-1">
                  <span style="color:#10b981;">✓</span> Description &amp; Services Tagged
                </li>
                <li class="d-flex align-items-center gap-2 mb-1">
                  <span style="color:#10b981;">✓</span> Headquarters &amp; Locations Confirmed
                </li>
                <li class="d-flex align-items-center gap-2 mb-1">
                  <span style="color:#10b981;">✓</span> Outbox Transaction Idempotency Active
                </li>
              </ul>
            </div>

          </div>

        </div>
      </div>

    </form>

  </div>
</div>
<!--end::Content-->
@endsection

@push('footer-script')
<script src="https://cdn.quilljs.com/1.3.6/quill.min.js"></script>
<script src="https://unpkg.com/@yaireo/tagify"></script>
<script id="jero_company_type_seed_data" type="application/json">
    {!! json_encode($actualCompanyTypes) !!}
</script>

<script>
/**
 * Super Admin Company Edit Script Engine
 * 100% Client-First Architecture, 0% Server Strain, Outbox Idempotency
 */
(function() {
  var currentStep = 1;
  var quillEditor = null;
  window._companyIntelligenceCache = {};

  // Curated City Clusters matching Client Create
  var curatedLocationGroups = [
    {
      group: 'Popular Tech Hubs (India)',
      items: [
        { city: 'Pune', state: 'Maharashtra', country: 'India', canonical: 'Pune | Maharashtra | India' },
        { city: 'Bengaluru', state: 'Karnataka', country: 'India', canonical: 'Bengaluru | Karnataka | India' },
        { city: 'Hyderabad', state: 'Telangana', country: 'India', canonical: 'Hyderabad | Telangana | India' },
        { city: 'Mumbai', state: 'Maharashtra', country: 'India', canonical: 'Mumbai | Maharashtra | India' },
        { city: 'Gurugram', state: 'Haryana', country: 'India', canonical: 'Gurugram | Haryana | India' },
        { city: 'Noida', state: 'Uttar Pradesh', country: 'India', canonical: 'Noida | Uttar Pradesh | India' },
        { city: 'Chennai', state: 'Tamil Nadu', country: 'India', canonical: 'Chennai | Tamil Nadu | India' },
        { city: 'Ahmedabad', state: 'Gujarat', country: 'India', canonical: 'Ahmedabad | Gujarat | India' }
      ]
    },
    {
      group: 'Global Tech Hubs',
      items: [
        { city: 'San Francisco', state: 'California', country: 'United States', canonical: 'San Francisco | California | United States' },
        { city: 'New York', state: 'New York', country: 'United States', canonical: 'New York | New York | United States' },
        { city: 'London', state: 'Greater London', country: 'United Kingdom', canonical: 'London | Greater London | United Kingdom' },
        { city: 'Singapore', state: '', country: 'Singapore', canonical: 'Singapore | Singapore' },
        { city: 'Dubai', state: 'Dubai', country: 'United Arab Emirates', canonical: 'Dubai | United Arab Emirates' }
      ]
    }
  ];

  // 1. STEP NAVIGATION
  window.goToWizardStep = function(targetStep) {
    targetStep = parseInt(targetStep, 10);
    if (!targetStep || targetStep < 1 || targetStep > 4) return;

    currentStep = targetStep;

    // Body class toggle
    $('body').removeClass('on-step-1 on-step-2 on-step-3 on-step-4').addClass('on-step-' + targetStep);

    // Show/hide step panes
    $('.client-step-pane').removeClass('is-active');
    $('#step_pane_' + targetStep).addClass('is-active');

    // Update Header Stepper Indicators
    var subtitles = {
      1: 'Review and update organization identity, website, and branding.',
      2: 'Provide company overview, services, and technology taxonomy.',
      3: 'Manage corporate headquarters and secondary branch locations.',
      4: 'Review all information and authorize verification before saving.'
    };
    $('#client_header_subtitle, #sa_top_header_sub, .sa-header-sub').text(subtitles[targetStep] || subtitles[1]);

    $('#client_wizard_stepper .client-step-item').each(function() {
      var s = parseInt($(this).data('step'), 10);
      var $circle = $(this).find('.client-step-circle');
      var $nextArrow = $(this).next('.client-step-arrow');

      if (s < targetStep) {
        $(this).removeClass('is-active is-upcoming').addClass('is-completed');
        $circle.removeClass('is-active is-upcoming').addClass('is-completed').html('<svg viewBox="0 0 24 24" fill="none" stroke="#ffffff" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>');
        if ($nextArrow.length) $nextArrow.removeClass('is-upcoming').addClass('is-completed');
      } else if (s === targetStep) {
        $(this).removeClass('is-completed is-upcoming').addClass('is-active');
        $circle.removeClass('is-completed is-upcoming').addClass('is-active').text(s);
        if ($nextArrow.length) $nextArrow.removeClass('is-completed').addClass('is-upcoming');
      } else {
        $(this).removeClass('is-completed is-active').addClass('is-upcoming');
        $circle.removeClass('is-completed is-active').addClass('is-upcoming').text(s);
        if ($nextArrow.length) $nextArrow.removeClass('is-completed').addClass('is-upcoming');
      }
    });

    // Step-specific initialization
    if (targetStep === 2 && quillEditor) {
      quillEditor.update();
    }
    if (targetStep === 4) {
      populateReviewSummaries();
    }

    window.scrollTo({ top: 0, behavior: 'smooth' });
  };

  window.handleWizardNext = function() {
    if (currentStep === 1) {
      var name = ($('#company_name').val() || '').trim();
      if (!name) {
        $('#company_name_input_box').addClass('is-invalid');
        $('#company_name').focus();
        if (typeof Swal !== 'undefined') Swal.fire('Company Name Required', 'Please enter official company name to continue.', 'warning');
        return;
      }
      $('#company_name_input_box').removeClass('is-invalid');
    }
    if (currentStep === 2) {
      var descHtml = quillEditor ? quillEditor.root.innerHTML : ($('#kt_docs_tinymce_hidden').val() || '');
      var descText = quillEditor ? quillEditor.getText().trim() : '';
      $('#kt_docs_tinymce_hidden').val(descHtml);
      $('#hidden_description').val(descHtml);
      if (descText.length < 5) {
        $('#client_editor_box').addClass('is-invalid');
        if (typeof Swal !== 'undefined') Swal.fire('Description Required', 'Please enter a brief company description before proceeding.', 'warning');
        return;
      }
      $('#client_editor_box').removeClass('is-invalid');
      if (window.clientServicesManager) {
        window.clientServicesManager.syncHiddenInput();
      }
    }
    if (currentStep === 3) {
      var hqCity = ($('#city_search_0').val() || '').trim();
      var hqAddr = ($('#address_0').val() || '').trim();
      if (!hqCity) {
        $('#city_search_0').addClass('is-invalid').focus();
        $('#city_err_0').show();
        if (typeof Swal !== 'undefined') Swal.fire('Headquarters City Required', 'Please select or enter city for corporate headquarters.', 'warning');
        return;
      }
      $('#city_search_0').removeClass('is-invalid');
      $('#city_err_0').hide();

      if (!hqAddr) {
        $('#address_0').addClass('is-invalid').focus();
        $('#address_err_0').show();
        if (typeof Swal !== 'undefined') Swal.fire('Headquarters Address Required', 'Please provide complete registered headquarters address.', 'warning');
        return;
      }
      $('#address_0').removeClass('is-invalid');
      $('#address_err_0').hide();
    }

    goToWizardStep(currentStep + 1);
  };

  window.handleWizardBack = function() {
    if (currentStep > 1) {
      goToWizardStep(currentStep - 1);
    }
  };

  // 2. QUILL EDITOR INITIALIZATION
  function initQuill() {
    if (document.getElementById('kt_client_description_quill')) {
      quillEditor = new Quill('#kt_client_description_quill', {
        theme: 'snow',
        placeholder: 'Describe your company, strengths, products/services, and culture...',
        modules: {
          toolbar: [
            [{ 'header': [1, 2, 3, false] }],
            ['bold', 'italic', 'underline'],
            [{ 'list': 'ordered'}, { 'list': 'bullet' }],
            ['clean']
          ]
        }
      });

      quillEditor.on('text-change', function() {
        var text = quillEditor.getText().trim();
        var count = text.length;
        $('#client_desc_count').text(count);
        var html = quillEditor.root.innerHTML;
        $('#kt_docs_tinymce_hidden').val(html);
        $('#hidden_description').val(html);
      });
    }
  }

  // 3. DROPDOWN TOGGLES
  window.toggleDropdown = function(wrapId) {
    var $wrap = $('#' + wrapId);
    var isOpen = $wrap.hasClass('is-open');
    $('.jero-ind-select-wrap, .jero-csize-select-wrap, .jero-ctype-select-wrap').removeClass('is-open');
    if (!isOpen) $wrap.addClass('is-open');
  };

  $(document).on('click', function(e) {
    if (!$(e.target).closest('.jero-ind-select-wrap, .jero-csize-select-wrap, .jero-ctype-select-wrap').length) {
      $('.jero-ind-select-wrap, .jero-csize-select-wrap, .jero-ctype-select-wrap').removeClass('is-open');
    }
    if (!$(e.target).closest('.client-addr-city-search, .client-addr-city-dropdown').length) {
      $('.client-addr-city-dropdown').removeClass('active');
    }
  });

  window.selectIndustry = function(id, name) {
    $('#industries').val(id);
    $('#industry_display').val(name);
    $('#industry_clear_btn').show();
    $('#jero_ind_select_wrap').removeClass('is-open');
  };

  window.clearIndustrySelect = function(e) {
    e.stopPropagation();
    $('#industries').val('');
    $('#industry_display').val('');
    $('#industry_clear_btn').hide();
  };

  window.filterIndustryList = function(val) {
    val = (val || '').toLowerCase();
    $('#industry_list_container .jero-dropdown-item').each(function() {
      var itemText = $(this).text().toLowerCase();
      $(this).toggle(itemText.indexOf(val) !== -1);
    });
  };

  window.selectCompanySize = function(val, label) {
    $('#company_size').val(val);
    $('#company_size_display').val(label);
    $('#jero_csize_select_wrap').removeClass('is-open');
  };

  // =========================================================================
  // Search-First Company Type Intelligence Engine (Client-First / 10K Concurrency)
  // Strictly isolated to this page (Rule 17)
  // =========================================================================
  var ctypeTaxonomyList = [];
  var ctypeTaxonomyMap = new Map();
  var ctypeFilteredList = [];
  var ctypeFocusedIndex = -1;
  var ctypeIsOpen = false;

  var ctypeWrap = null;
  var ctypeInput = null;
  var ctypeHiddenSelect = null;
  var ctypeInputBox = null;
  var ctypeClearBtn = null;
  var ctypeChevron = null;
  var ctypeDropdown = null;
  var ctypeListContainer = null;

  function initCompanyTypeData() {
    var seedEl = document.getElementById('jero_company_type_seed_data');
    if (seedEl && seedEl.textContent) {
      try {
        ctypeTaxonomyList = JSON.parse(seedEl.textContent);
      } catch (e) {
        ctypeTaxonomyList = [];
      }
    }
    ctypeTaxonomyMap.clear();
    for (var i = 0; i < ctypeTaxonomyList.length; i++) {
      var item = ctypeTaxonomyList[i];
      ctypeTaxonomyMap.set(String(item.id), item);
    }
  }

  function getSelectedCompanyTypeId() {
    if (!ctypeHiddenSelect) return '';
    var val = ctypeHiddenSelect.value;
    if (val === '' || val === null || typeof val === 'undefined') return '';
    return String(val);
  }

  function filterCompanyTypeItems(query) {
    var clean = (query || '').trim().toLowerCase();
    if (!clean) {
      ctypeFilteredList = ctypeTaxonomyList.slice(0);
      return;
    }

    var results = [];
    for (var i = 0; i < ctypeTaxonomyList.length; i++) {
      var item = ctypeTaxonomyList[i];
      var name = (item.display_name || item.name || '').toLowerCase();
      if (name.indexOf(clean) !== -1) {
        results.push(item);
      }
    }
    ctypeFilteredList = results;
  }

  function renderCompanyTypeList(query) {
    if (!ctypeListContainer) return;
    if (!ctypeFilteredList.length) {
      ctypeListContainer.innerHTML = '<div class="jero-ctype-empty-state">' +
        '<div style="font-weight:600; margin-bottom:4px; color:#1e293b;">No company type found</div>' +
        '<div style="font-size:11.5px; color:#64748b;">No results matching "' + escapeHtmlText(query) + '".</div>' +
        '</div>';
      return;
    }

    var selectedId = getSelectedCompanyTypeId();
    var html = '';

    for (var i = 0; i < ctypeFilteredList.length; i++) {
      var item = ctypeFilteredList[i];
      var itemIdStr = String(item.id);
      var isSelected = (selectedId !== '' && selectedId === itemIdStr);
      var isFocused = (i === ctypeFocusedIndex);

      html += '<div class="jero-ctype-item ' + (isSelected ? 'selected' : '') + ' ' + (isFocused ? 'focused' : '') + '" ' +
        'data-idx="' + i + '" data-id="' + escapeHtmlText(itemIdStr) + '" role="option" id="jero_ctype_opt_' + escapeHtmlText(itemIdStr) + '" aria-selected="' + (isSelected ? 'true' : 'false') + '">' +
        '<div class="jero-ctype-item-main">' +
        '<div class="jero-ctype-item-name">' + highlightCtypeMatch(item.display_name || item.name, query) + '</div>' +
        '</div>' +
        '</div>';
    }

    ctypeListContainer.innerHTML = html;
    scrollCtypeFocusedIntoView();
  }

  function highlightCtypeMatch(text, query) {
    var safeText = escapeHtmlText(text || '');
    if (!query) return safeText;
    var cleanQ = query.trim().replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
    if (!cleanQ) return safeText;
    var regex = new RegExp('(' + cleanQ + ')', 'gi');
    return safeText.replace(regex, '<mark class="jero-ctype-mark">$1</mark>');
  }

  function scrollCtypeFocusedIntoView() {
    if (!ctypeListContainer || ctypeFocusedIndex < 0) {
      if (ctypeInput) ctypeInput.removeAttribute('aria-activedescendant');
      return;
    }
    var items = ctypeListContainer.querySelectorAll('.jero-ctype-item');
    if (items[ctypeFocusedIndex]) {
      var item = items[ctypeFocusedIndex];
      var itemTop = item.offsetTop;
      var itemBottom = itemTop + item.offsetHeight;
      var containerTop = ctypeListContainer.scrollTop;
      var containerBottom = containerTop + ctypeListContainer.offsetHeight;

      if (itemTop < containerTop) {
        ctypeListContainer.scrollTop = itemTop;
      } else if (itemBottom > containerBottom) {
        ctypeListContainer.scrollTop = itemBottom - ctypeListContainer.offsetHeight;
      }

      if (ctypeInput && item.id) {
        ctypeInput.setAttribute('aria-activedescendant', item.id);
      }
    }
  }

  function updateCtypeDropdownPosition() {
    if (!ctypeWrap || !ctypeDropdown) return;
    var rect = ctypeWrap.getBoundingClientRect();
    var spaceBelow = window.innerHeight - rect.bottom;
    var dropdownHeight = 300;
    if (spaceBelow < dropdownHeight && rect.top > dropdownHeight) {
      ctypeDropdown.classList.add('drop-up');
    } else {
      ctypeDropdown.classList.remove('drop-up');
    }
  }

  function openCtypeDropdown() {
    if (ctypeIsOpen) return;
    ctypeIsOpen = true;
    updateCtypeDropdownPosition();
    if (ctypeWrap) ctypeWrap.classList.add('is-open');
    if (ctypeDropdown) ctypeDropdown.style.display = 'flex';
    if (ctypeInput) ctypeInput.setAttribute('aria-expanded', 'true');

    filterCompanyTypeItems(ctypeInput ? ctypeInput.value : '');
    var selectedId = getSelectedCompanyTypeId();
    ctypeFocusedIndex = -1;
    if (selectedId !== '') {
      for (var i = 0; i < ctypeFilteredList.length; i++) {
        if (String(ctypeFilteredList[i].id) === selectedId) {
          ctypeFocusedIndex = i;
          break;
        }
      }
    }
    if (ctypeFocusedIndex === -1 && ctypeFilteredList.length > 0) {
      ctypeFocusedIndex = 0;
    }
    renderCompanyTypeList(ctypeInput ? ctypeInput.value : '');
  }

  function closeCtypeDropdown(restoreText) {
    if (!ctypeIsOpen) return;
    ctypeIsOpen = false;
    if (ctypeWrap) ctypeWrap.classList.remove('is-open');
    if (ctypeDropdown) ctypeDropdown.style.display = 'none';
    if (ctypeInput) ctypeInput.setAttribute('aria-expanded', 'false');
    if (ctypeInput) ctypeInput.removeAttribute('aria-activedescendant');
    ctypeFocusedIndex = -1;

    if (restoreText && ctypeInput) {
      var selectedId = getSelectedCompanyTypeId();
      if (selectedId !== '' && ctypeTaxonomyMap.has(selectedId)) {
        var itm = ctypeTaxonomyMap.get(selectedId);
        ctypeInput.value = itm.display_name || itm.name;
      } else {
        ctypeInput.value = '';
      }
      updateCtypeVisualState();
    }
  }

  function updateCtypeVisualState() {
    var hasVal = ctypeInput && ctypeInput.value.trim().length > 0;
    if (ctypeInputBox) {
      if (hasVal) {
        ctypeInputBox.classList.add('has-value');
      } else {
        ctypeInputBox.classList.remove('has-value');
      }
    }
    if (ctypeClearBtn) ctypeClearBtn.style.display = hasVal ? 'flex' : 'none';
  }

  function selectCtypeItem(item) {
    if (!item) return;
    var strId = String(item.id);
    if (strId === '' || strId === 'null' || strId === 'undefined') {
      clearCtypeSelection();
      return;
    }
    if (ctypeHiddenSelect) {
      ctypeHiddenSelect.value = strId;
      $(ctypeHiddenSelect).trigger('change');
    }
    var indType = document.getElementById('industries_type');
    if (indType) {
      indType.value = strId;
    }
    if (ctypeInput) {
      ctypeInput.value = item.display_name || item.name;
    }
    updateCtypeVisualState();
    closeCtypeDropdown(false);
  }

  function clearCtypeSelection() {
    if (ctypeHiddenSelect) {
      ctypeHiddenSelect.value = '';
      $(ctypeHiddenSelect).trigger('change');
    }
    var indType = document.getElementById('industries_type');
    if (indType) {
      indType.value = '';
    }
    if (ctypeInput) {
      ctypeInput.value = '';
    }
    updateCtypeVisualState();
    filterCompanyTypeItems('');
    renderCompanyTypeList('');
    if (ctypeInput) ctypeInput.focus();
  }

  function initCompanyTypeEventListeners() {
    ctypeWrap = document.getElementById('jero_ctype_select_wrap');
    ctypeInput = document.getElementById('company_type_display');
    ctypeHiddenSelect = document.getElementById('company_type');
    ctypeInputBox = document.getElementById('jero_ctype_input_box');
    ctypeClearBtn = document.getElementById('jero_ctype_clear_btn');
    ctypeChevron = document.getElementById('jero_ctype_chevron');
    ctypeDropdown = document.getElementById('jero_ctype_dropdown');
    ctypeListContainer = document.getElementById('jero_ctype_list_container');

    if (!ctypeWrap || !ctypeInput || !ctypeHiddenSelect) return;

    if (ctypeChevron) {
      ctypeChevron.addEventListener('click', function(e) {
        e.stopPropagation();
        if (ctypeIsOpen) {
          closeCtypeDropdown(true);
        } else {
          ctypeInput.focus();
          openCtypeDropdown();
        }
      });
    }

    if (ctypeInputBox) {
      ctypeInputBox.addEventListener('click', function(e) {
        if (e.target.closest('#jero_ctype_clear_btn')) return;
        if (e.target.closest('#jero_ctype_chevron')) return;
        if (!ctypeIsOpen) {
          ctypeInput.focus();
          openCtypeDropdown();
        }
      });
    }

    ctypeInput.addEventListener('focus', function() {
      openCtypeDropdown();
    });
    ctypeInput.addEventListener('click', function(e) {
      e.stopPropagation();
      openCtypeDropdown();
    });

    if (ctypeDropdown) {
      ctypeDropdown.addEventListener('pointerdown', function(e) {
        e.stopPropagation();
      });
      ctypeDropdown.addEventListener('click', function(e) {
        e.stopPropagation();
      });
    }

    ctypeInput.addEventListener('input', function() {
      if (!ctypeIsOpen) openCtypeDropdown();
      ctypeFocusedIndex = 0;
      filterCompanyTypeItems(ctypeInput.value);
      renderCompanyTypeList(ctypeInput.value);
      updateCtypeVisualState();
    });

    if (ctypeClearBtn) {
      ctypeClearBtn.addEventListener('click', function(e) {
        e.stopPropagation();
        clearCtypeSelection();
      });
    }

    if (ctypeListContainer) {
      ctypeListContainer.addEventListener('click', function(e) {
        var itemEl = e.target.closest('.jero-ctype-item');
        if (!itemEl) return;
        var id = itemEl.getAttribute('data-id');
        if (id !== null && ctypeTaxonomyMap.has(String(id))) {
          selectCtypeItem(ctypeTaxonomyMap.get(String(id)));
        }
      });
    }

    ctypeInput.addEventListener('keydown', function(e) {
      if (e.key === 'ArrowDown') {
        e.preventDefault();
        if (!ctypeIsOpen) {
          openCtypeDropdown();
          return;
        }
        if (ctypeFilteredList.length > 0) {
          ctypeFocusedIndex = (ctypeFocusedIndex + 1) % ctypeFilteredList.length;
          renderCompanyTypeList(ctypeInput.value);
        }
      } else if (e.key === 'ArrowUp') {
        e.preventDefault();
        if (!ctypeIsOpen) {
          openCtypeDropdown();
          return;
        }
        if (ctypeFilteredList.length > 0) {
          ctypeFocusedIndex = (ctypeFocusedIndex - 1 + ctypeFilteredList.length) % ctypeFilteredList.length;
          renderCompanyTypeList(ctypeInput.value);
        }
      } else if (e.key === 'Enter') {
        if (ctypeIsOpen) {
          e.preventDefault();
          if (ctypeFocusedIndex >= 0 && ctypeFocusedIndex < ctypeFilteredList.length) {
            selectCtypeItem(ctypeFilteredList[ctypeFocusedIndex]);
          }
        }
      } else if (e.key === 'Escape') {
        if (ctypeIsOpen) {
          e.preventDefault();
          closeCtypeDropdown(true);
        }
      } else if (e.key === 'Tab') {
        closeCtypeDropdown(true);
      }
    });

    document.addEventListener('pointerdown', function(e) {
      var path = (typeof e.composedPath === 'function') ? e.composedPath() : [];
      var isInside = ctypeWrap && (ctypeWrap.contains(e.target) || path.indexOf(ctypeWrap) !== -1);
      if (!isInside && ctypeIsOpen) {
        closeCtypeDropdown(true);
      }
    }, true);

    window.addEventListener('resize', function() {
      if (ctypeIsOpen) updateCtypeDropdownPosition();
    });

    $(ctypeHiddenSelect).on('change', function() {
      var val = getSelectedCompanyTypeId();
      if (val !== '' && ctypeTaxonomyMap.has(val)) {
        ctypeInput.value = ctypeTaxonomyMap.get(val).display_name || ctypeTaxonomyMap.get(val).name;
      } else if (val === '') {
        ctypeInput.value = '';
      }
      updateCtypeVisualState();
    });

    var initId = getSelectedCompanyTypeId();
    if (initId !== '' && ctypeTaxonomyMap.has(initId)) {
      if (ctypeInput && !ctypeInput.value) {
        ctypeInput.value = ctypeTaxonomyMap.get(initId).display_name || ctypeTaxonomyMap.get(initId).name;
      }
    }
    updateCtypeVisualState();
  }

  window.JeroCompanyTypeEngine = {
    selectById: function(id) {
      var strId = String(id);
      if (ctypeTaxonomyMap.has(strId)) {
        selectCtypeItem(ctypeTaxonomyMap.get(strId));
      }
    },
    selectByName: function(name) {
      var clean = (name || '').trim().toLowerCase();
      for (var i = 0; i < ctypeTaxonomyList.length; i++) {
        if ((ctypeTaxonomyList[i].name && ctypeTaxonomyList[i].name.toLowerCase() === clean) ||
            (ctypeTaxonomyList[i].display_name && ctypeTaxonomyList[i].display_name.toLowerCase() === clean)) {
          selectCtypeItem(ctypeTaxonomyList[i]);
          return;
        }
      }
    },
    clear: clearCtypeSelection
  };

  window.selectCompanyType = function(val, label) {
    if (window.JeroCompanyTypeEngine && typeof window.JeroCompanyTypeEngine.selectById === 'function') {
      window.JeroCompanyTypeEngine.selectById(val);
    } else {
      $('#company_type').val(val).trigger('change');
      $('#industries_type').val(val);
      $('#company_type_display').val(label);
      $('#jero_ctype_select_wrap').removeClass('is-open');
    }
  };

  // 4. LOGO UPLOAD & REMOVE
  window.handleLogoChange = function(e) {
    var file = e.target.files[0];
    if (!file) return;

    if (file.size > 2 * 1024 * 1024) {
      if (typeof Swal !== 'undefined') Swal.fire('File Too Large', 'Please upload a logo image smaller than 2MB.', 'error');
      return;
    }

    var reader = new FileReader();
    reader.onload = function(evt) {
      $('#company_logo_box')
        .css({
          'background-image': 'url("' + evt.target.result + '")',
          'background-size': 'contain',
          'background-repeat': 'no-repeat',
          'background-position': 'center'
        })
        .addClass('has-image')
        .empty();

      $('#client_remove_logo_btn').show();
      $('#client_avatar_remove').val('0');
      $('#logo_status_banner').removeClass('is-empty').addClass('is-detected').html('<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg> <span>Official logo uploaded</span>');
      $('#client_review_logo_box').css({
        'background-image': 'url("' + evt.target.result + '")',
        'background-size': 'contain',
        'background-repeat': 'no-repeat',
        'background-position': 'center'
      });
      $('#client_review_initials').hide();
    };
    reader.readAsDataURL(file);
  };

  window.removeLogo = function() {
    $('#company_logo_input').val('');
    $('#company_logo_box')
      .css('background-image', 'none')
      .removeClass('has-image')
      .html('<div class="god-logo-placeholder" id="logo_placeholder"><svg width="44" height="44" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg><span class="placeholder-title">Company Logo</span><span class="placeholder-sub">Click Change Logo or auto-fetch</span></div>');

    $('#client_remove_logo_btn').hide();
    $('#client_avatar_remove').val('1');
    $('#logo_status_banner').removeClass('is-detected').addClass('is-empty').html('<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/></svg> <span>No logo detected yet</span>');
    $('#client_review_logo_box').css('background-image', 'none');
    $('#client_review_initials').show();
  };

  // 5. LIVE PREVIEW BINDINGS
  $('#company_name').on('input', function() {
    var val = $(this).val().trim();
    $('#live_prev_name').text(val || 'Organization');
    var inits = (val ? val.substring(0, 2) : 'CO').toUpperCase();
    $('#client_review_initials').text(inits);
  });

  $('#website').on('input', function() {
    var val = $(this).val().trim();
    $('#live_prev_web').text(val || 'https://example.com');
    if (val) $('#website_clear_btn').show();
    else $('#website_clear_btn').hide();
  });

  $('#website_clear_btn').on('click', function() {
    $('#website').val('').trigger('input').focus();
  });

  // 6. CLIENT INTELLIGENCE AUTO-FETCH
  window.fetchGodLevelCompanyIntelligence = function(website, force) {
    if (!website) return;
    var cleanDomain = website.replace(/^https?:\/\//i, '').replace(/^www\./i, '').split('/')[0].trim();
    if (!cleanDomain || cleanDomain.length < 3) return;

    var $btn = $('#client_auto_fetch');
    var origHtml = $btn.html();
    $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1" style="width:12px;height:12px;"></span> Extracting...');

    if (!force && window._companyIntelligenceCache[cleanDomain]) {
      applyIntelligence(window._companyIntelligenceCache[cleanDomain]);
      $btn.prop('disabled', false).html(origHtml);
      return;
    }

    var targetUrl = 'https://jero-company-intelligence.vibhu21m.workers.dev/?domain=' + encodeURIComponent(cleanDomain);
    fetch(targetUrl, { method: 'GET', headers: { 'Accept': 'application/json' } })
      .then(function(res) { return res.json(); })
      .then(function(data) {
        $btn.prop('disabled', false).html(origHtml);
        if (data && !data.error) {
          window._companyIntelligenceCache[cleanDomain] = data;
          applyIntelligence(data);
        }
      })
      .catch(function() {
        $btn.prop('disabled', false).html(origHtml);
        var synthName = cleanDomain.split('.')[0].replace(/[-_]/g, ' ').replace(/\b\w/g, function(l){ return l.toUpperCase(); });
        var synthData = {
          name: synthName,
          website: 'https://' + cleanDomain,
          description: synthName + ' is a leading organization specializing in enterprise services, technology development, and recruitment solutions.'
        };
        applyIntelligence(synthData);
      });
  };

  function applyIntelligence(data) {
    if (data.name && !$('#company_name').val()) {
      $('#company_name').val(data.name).trigger('input');
    }
    if ((data.career_page || data.career_page_url || data.jobs_url) && !$('#career_page_link').val()) {
      $('#career_page_link').val(data.career_page || data.career_page_url || data.jobs_url).trigger('input');
    }
    // Official Email & Phone are strictly view-only; do not mutate via auto-fetch
    if ((data.founded || data.founded_year) && !$('#founded').val()) {
      $('#founded').val(data.founded || data.founded_year).trigger('input');
    }
    if (data.description && quillEditor && !quillEditor.getText().trim()) {
      quillEditor.root.innerHTML = data.description;
      $('#client_ai_suggested_desc').text(data.description);
    }
    if (data.logo) {
      $('#company_logo_box')
        .css({
          'background-image': 'url("' + data.logo + '")',
          'background-size': 'contain',
          'background-repeat': 'no-repeat',
          'background-position': 'center'
        })
        .addClass('has-image')
        .empty();

      $('#client_remove_logo_btn').show();
      $('#logo_status_banner').removeClass('is-empty').addClass('is-detected').html('<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg> <span>Official logo detected</span>');
      $('#client_review_logo_box').css({
        'background-image': 'url("' + data.logo + '")',
        'background-size': 'contain',
        'background-repeat': 'no-repeat',
        'background-position': 'center'
      });
      $('#client_review_initials').hide();
    }
  }

  // 7. GOD-LEVEL SERVICE OFFERINGS ENGINE (Exact Client Create Parity)
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
      }
    },

    removeTag: function (tag) {
      var lower = String(tag || '').toLowerCase();
      this.selected = this.selected.filter(function (t) { return t.toLowerCase() !== lower; });
      this.renderSelected();
      this.syncHiddenInput();
      this.updateSuggestedView();
    },

    clearAll: function () {
      this.selected = [];
      this.renderSelected();
      this.syncHiddenInput();
      this.updateSuggestedView();
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

  window.addAllSuggestedServices = function() {
    if (window.clientServicesManager) {
      window.clientServicesManager.addAllRecommended();
    }
  };

  window.applyDescriptionSuggestion = function() {
    var text = ($('#client_ai_suggested_desc').text() || '').trim();
    if (quillEditor && text) {
      quillEditor.root.innerHTML = text;
      $('#kt_docs_tinymce_hidden').val(text);
      $('#hidden_description').val(text);
      $('#client_desc_count').text(quillEditor.getText().trim().length);
      if (typeof Swal !== 'undefined' && typeof Swal.mixin === 'function') {
        Swal.mixin({ toast: true, position: 'top-end', showConfirmButton: false, timer: 2000 })
          .fire({ icon: 'success', title: 'AI description applied!' });
      }
    }
  };

  // 8. UNIFIED LOCATION & CITY AUTOCOMPLETE ENGINE (Client Create Standard)
  var cityAddressSearchAbortCtrl = {};
  var cityLruCache = new Map();
  var CITY_CACHE_MAX = 100;
  var CITY_CACHE_TTL = 1800000;

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

  function highlightMatch(text, query) {
    if (!query) return escapeHtmlText(text);
    var regex = new RegExp('(' + query.replace(/[.*+?^${}()|[\]\\]/g, '\\$&') + ')', 'gi');
    return escapeHtmlText(text).replace(regex, '<strong style="color:#2563eb;">$1</strong>');
  }

  function debounce(func, delay) {
    var timer;
    return function() {
      var context = this;
      var args = arguments;
      clearTimeout(timer);
      timer = setTimeout(function() {
        func.apply(context, args);
      }, delay);
    };
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

    if (window.clientLocationManager) {
      window.clientLocationManager.syncAiButtons();
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
          var safeCanon = escapeJs(item.canonical);
          var safeCity = escapeJs(item.city);
          html += '<div class="client-addr-dd-item" onclick="selectCityForAddress(\'' + cid + '\', \'' + safeCanon + '\', \'' + safeCity + '\')">' +
            '<div class="client-addr-dd-left">' +
            '  <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="#2563eb" stroke-width="2"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg>' +
            '  <div>' +
            '    <div class="client-addr-dd-city">' + escapeHtmlText(item.city) + '</div>' +
            '    <div class="client-addr-dd-sub">' + escapeHtmlText(subText) + '</div>' +
            '  </div>' +
            '</div>' +
            (item.badge ? '<span class="client-addr-dd-badge">' + escapeHtmlText(item.badge) + '</span>' : '') +
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
      var safeQ = escapeHtmlText(query);
      var safeJsQ = escapeJs(query);
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
      var safeCanon = escapeJs(canonical);
      var safeCity = escapeJs(item.city);
      html += '<div class="client-addr-dd-item" onclick="selectCityForAddress(\'' + cid + '\', \'' + safeCanon + '\', \'' + safeCity + '\')">' +
        '<div class="client-addr-dd-left">' +
        '  <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="#2563eb" stroke-width="2"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg>' +
        '  <div>' +
        '    <div class="client-addr-dd-city">' + highlightMatch(item.city, query) + '</div>' +
        '    <div class="client-addr-dd-sub">' + escapeHtmlText(subText) + '</div>' +
        '  </div>' +
        '</div>' +
        (item.badge ? '<span class="client-addr-dd-badge">' + escapeHtmlText(item.badge) + '</span>' : '') +
        '</div>';
    });
    dd.innerHTML = html;
    dd.classList.add('active');
  }

  var debounceCityAddressSearch = debounce(function(cid, val) {
    renderCityAddressDropdown(cid, val);
  }, 180);

  window.selectCityForAddress = selectCityForAddress;
  window.renderCityAddressDropdown = renderCityAddressDropdown;

  // 9. GOD-LEVEL LOCATION & BRANCH MANAGER (Exact Client Create Parity)
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

      $(document).on('input', '#website', function() {
        self.syncWebsiteLink();
      });

      $(document).on('input change', '#city_search_0, .branch-city-search, .client-addr-city-search', function() {
        self.syncAiButtons();
      });
    },

    syncWebsiteLink: function() {
      var web = ($('#website').val() || '').trim();
      if (web) {
        var url = (web.indexOf('://') === -1) ? ('https://' + web) : web;
        $('#ai_view_source_link, .client-ai-source-link, #client_ai_source_link').attr('href', url).attr('target', '_blank');
      } else {
        $('#ai_view_source_link, .client-ai-source-link, #client_ai_source_link').attr('href', 'javascript:void(0);').removeAttr('target');
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
          var bCity = ($(this).find('.branch-city-search, .client-addr-city-search').val() || '').trim().toLowerCase();
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
      if (!addressId || document.getElementById('clientAddr_' + addressId)) {
        addressId = Math.max(addressId, $('#additional_addresses_container .client-branch-card').length) + 1;
        while (document.getElementById('clientAddr_' + addressId)) {
          addressId++;
        }
        $('#addressId').val(addressId);
      }
      var cid = addressId;
      var branchNumber = $('#additional_addresses_container .client-branch-card').length + 1;

      var cardHtml = '<div class="client-subcard client-branch-subcard client-branch-card" id="clientAddr_' + cid + '" data-cid="' + cid + '">' +
        '<div class="client-subcard-header d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom">' +
        '  <div class="d-flex align-items-center gap-2">' +
        '    <div class="client-subcard-icon-box office-icon">' +
        '      <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">' +
        '        <rect x="4" y="2" width="16" height="20" rx="2" ry="2"/>' +
        '        <path d="M9 22v-4h6v4"/>' +
        '        <line x1="8" y1="6" x2="8.01" y2="6"/><line x1="16" y1="6" x2="16.01" y2="6"/><line x1="12" y1="6" x2="12.01" y2="6"/>' +
        '        <line x1="8" y1="10" x2="8.01" y2="10"/><line x1="12" y1="10" x2="12.01" y2="10"/><line x1="16" y1="10" x2="16.01" y2="10"/>' +
        '        <line x1="8" y1="14" x2="8.01" y2="14"/><line x1="12" y1="14" x2="12.01" y2="14"/><line x1="16" y1="14" x2="16.01" y2="14"/>' +
        '      </svg>' +
        '    </div>' +
        '    <div class="d-flex align-items-center gap-2">' +
        '      <h4 class="client-subcard-title client-branch-title mb-0">Additional Location ' + branchNumber + '</h4>' +
        '      <span class="badge badge-branch-tag">BRANCH</span>' +
        '    </div>' +
        '  </div>' +
        '  <div class="d-flex align-items-center gap-2">' +
        '    <button type="button" class="btn btn-xs btn-set-primary" onclick="window.clientLocationManager ? window.clientLocationManager.setBranchAsHq(' + cid + ') : false" title="Set as Primary">' +
        '      <svg width="12" height="12" viewBox="0 0 24 24" fill="#f59e0b" stroke="#d97706" stroke-width="1.2" class="me-1"><path d="M5 16L3 5l5.5 5L12 4l3.5 6L21 5l-2 11H5m14 3c0 .6-.4 1-1 1H6c-.6 0-1-.4-1-1v-1h14v1z"/></svg>' +
        '      <span>Set as Primary</span>' +
        '    </button>' +
        '    <button type="button" class="btn btn-xs btn-remove-subcard client-branch-remove-btn" onclick="removeAddressCard(this, \'clientAddr_' + cid + '\')" title="Remove Location">' +
        '      <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="me-1"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>' +
        '      <span>Remove</span>' +
        '    </button>' +
        '  </div>' +
        '</div>' +
        '<div class="client-subcard-body">' +
        '  <div class="client-addr-field mb-2">' +
        '    <label class="form-label mb-1" for="branch_city_search_' + cid + '">City / Location <span class="text-danger">*</span></label>' +
        '    <div class="position-relative client-addr-input-wrap">' +
        '      <span class="position-absolute client-addr-field-icon-left">' +
        '        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>' +
        '      </span>' +
        '      <input type="text" class="form-control client-addr-input client-addr-city-search branch-city-search" id="branch_city_search_' + cid + '" data-cid="' + cid + '" value="' + escapeHtmlAttr(city || '') + '" placeholder="Search for a city.." autocomplete="off">' +
        '      <span class="position-absolute client-addr-field-icon-right">' +
        '        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"></polyline></svg>' +
        '      </span>' +
        '      <div class="client-addr-city-dropdown branch-city-dropdown" id="branch_city_dropdown_' + cid + '"></div>' +
        '      <input type="hidden" name="kt_tagify_work_location[]" id="kt_tagify_work_location_' + cid + '" value=\'' + (tagifyJson || '') + '\'>' +
        '    </div>' +
        '    <div class="client-addr-error text-danger mt-1" id="branch_city_err_' + cid + '" style="display:none;">City selection is required.</div>' +
        '  </div>' +
        '  <div class="client-addr-field">' +
        '    <div class="position-relative client-addr-textarea-wrap">' +
        '      <span class="position-absolute client-addr-field-icon-top">' +
        '        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg>' +
        '      </span>' +
        '      <textarea class="form-control client-addr-textarea" name="address[]" id="address_' + cid + '" rows="2" maxlength="500" placeholder="Enter complete branch address..">' + escapeHtmlText(address || '') + '</textarea>' +
        '    </div>' +
        '    <div class="d-flex align-items-center justify-content-between mt-1">' +
        '      <span class="text-muted client-field-hint">Include building, street, area, city and postal code.</span>' +
        '      <span class="client-addr-counter"><span id="address_char_count_' + cid + '">' + (address ? address.length : 0) + '</span> / 500</span>' +
        '    </div>' +
        '    <div class="client-addr-error text-danger mt-1" id="branch_address_err_' + cid + '" style="display:none;">Full Address is required.</div>' +
        '  </div>' +
        '</div>' +
        '</div>';

      $('#additional_addresses_container').append(cardHtml);
      $('#client_additional_empty_state').addClass('d-none').css('display', 'none').hide();
      $('#additional_location_cta_wrap').removeClass('d-none').css('display', 'flex').show();
      var bCount = $('#additional_addresses_container .client-branch-card').length;
      $('#additional_locations_count_badge').text(bCount + (bCount === 1 ? ' Location' : ' Locations'));
      return cid;
    },

    syncAiButtons: function() {
      var hqCity = ($('#city_search_0').val() || '').trim().toLowerCase();
      var branchCities = [];
      $('#additional_addresses_container .client-branch-card').each(function() {
        var c = ($(this).find('.branch-city-search, .client-addr-city-search').val() || '').trim().toLowerCase();
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
              '<button type="button" class="btn btn-sm btn-ai-set-hq" onclick="window.clientLocationManager ? window.clientLocationManager.addAiLocation(' + i + ', true) : false;" style="background:#f59e0b; color:#78350f; font-weight:600; font-size:11px; border:none; border-radius:6px; padding:4px 10px; cursor:pointer;">Set as Headquarters</button>' +
              '<button type="button" class="btn btn-sm btn-ai-add-office" onclick="window.clientLocationManager ? window.clientLocationManager.addAiLocation(' + i + ', false) : false;" style="background:#ffffff; color:#2563eb; border:1px solid #2563eb; font-weight:600; font-size:11px; border-radius:6px; padding:4px 10px; cursor:pointer;">Add as Office</button>';
          } else {
            buttonsHtml =
              '<button type="button" class="btn btn-sm btn-ai-add-office" onclick="window.clientLocationManager ? window.clientLocationManager.addAiLocation(' + i + ', false) : false;" style="background:#ffffff; color:#2563eb; border:1px solid #2563eb; font-weight:600; font-size:11px; border-radius:6px; padding:4px 10px; cursor:pointer;">Add as Office</button>';
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

  window.removeAddressCard = function(btn, targetId) {
    if (targetId) {
      var el = document.getElementById(targetId);
      if (el) el.remove();
    }
    if (btn) {
      var elBtn = (typeof btn.jquery !== 'undefined' ? btn[0] : btn);
      if (elBtn && typeof elBtn.closest === 'function') {
        var card = elBtn.closest('.client-branch-card, .client-subcard');
        if (card) card.remove();
      }
    }
    $('#additional_addresses_container .client-branch-card').each(function(idx) {
      $(this).find('.client-branch-title').text('Additional Location ' + (idx + 1));
    });
    var bCount = $('#additional_addresses_container .client-branch-card').length;
    if (bCount === 0) {
      $('#client_additional_empty_state').removeClass('d-none').css('display', 'flex').show();
      $('#additional_location_cta_wrap').addClass('d-none').css('display', 'none').hide();
      $('#additional_locations_count_badge').text('0 Locations');
    } else {
      $('#client_additional_empty_state').addClass('d-none').css('display', 'none').hide();
      $('#additional_location_cta_wrap').removeClass('d-none').css('display', 'flex').show();
      $('#additional_locations_count_badge').text(bCount + (bCount === 1 ? ' Location' : ' Locations'));
    }
    if (window.clientLocationManager) {
      window.clientLocationManager.syncAiButtons();
    }
  };

  // 10. REVIEW SUMMARIES POPULATOR (0ms Client-Side Synthesis)
  function populateReviewSummaries() {
    var name = ($('#company_name').val() || '').trim() || 'Organization Name';
    var web = ($('#website').val() || '').trim() || 'No website provided';
    var ind = ($('#industry_display').val() || '').trim() || 'General Industry';
    var size = ($('#company_size_display').val() || '').trim() || 'Size not specified';
    var hqCity = ($('#city_search_0').val() || '').trim() || 'Location not specified';
    var hqAddr = ($('#address_0').val() || '').trim() || 'Address not specified';

    $('#rev_company_name').text(name);
    $('#rev_website').html('<i class="fa fa-globe me-1"></i> ' + escapeHtmlText(web));
    var email = ($('#company_email').val() || '').trim() || 'No email provided';
    var phone = ($('#company_phone').val() || '').trim() || 'No phone provided';
    var career = ($('#career_page_link').val() || '').trim() || 'No career page';
    $('#rev_email').html('<i class="fa fa-envelope me-1"></i> ' + escapeHtmlText(email));
    $('#rev_phone').html('<i class="fa fa-phone me-1"></i> ' + escapeHtmlText(phone));
    $('#rev_career').html('<i class="fa fa-briefcase me-1"></i> ' + escapeHtmlText(career));
    $('#rev_industry').html('<i class="fa fa-building me-1"></i> ' + escapeHtmlText(ind));
    $('#rev_size').html('<i class="fa fa-users me-1"></i> ' + escapeHtmlText(size));
    var ctype = ($('#company_type_display').val() || '').trim() || ($('#company_type option:selected').text() || 'Company Type');
    $('#rev_type').html('<i class="fa fa-tag me-1"></i> ' + escapeHtmlText(ctype));
    $('#rev_primary_location').html('<i class="fa fa-map-marker-alt me-1"></i> ' + escapeHtmlText(hqCity));

    // Description & Badges
    var descHtml = quillEditor ? quillEditor.root.innerHTML : $('#kt_docs_tinymce_hidden').val();
    $('#rev_description_box').html(descHtml || 'No description provided.');

    var tagsArr = (window.clientServicesManager && window.clientServicesManager.selected) ? window.clientServicesManager.selected : [];
    var badgesHtml = '';
    tagsArr.forEach(function(t) {
      badgesHtml += '<span class="client-review-service-pill">' + escapeHtmlText(t) + '</span>';
    });
    $('#client_review_service_badges').html(badgesHtml || '<span class="text-muted" style="font-size:11px;">No services selected.</span>');

    // Locations Box
    var locsHtml = 
      '<div class="p-3 mb-2" style="background:#fffdf0; border:1px solid #fef08a; border-radius:8px;">' +
      '  <div class="d-flex align-items-center justify-content-between mb-1">' +
      '    <strong style="font-size:12px; color:#854d0e;">Headquarters (Main Office)</strong>' +
      '    <span class="badge-hq-pill">HEADQUARTERS</span>' +
      '  </div>' +
      '  <div style="font-size:12px; font-weight:600; color:#0f172a;">' + escapeHtmlText(hqCity) + '</div>' +
      '  <div style="font-size:11.5px; color:#64748b;">' + escapeHtmlText(hqAddr) + '</div>' +
      '</div>';

    $('#additional_addresses_container .client-branch-card').each(function(i) {
      var bCity = ($(this).find('.client-addr-city-search, .branch-city-search').val() || '').trim();
      var bAddr = ($(this).find('textarea').val() || '').trim();
      if (bCity || bAddr) {
        locsHtml += 
          '<div class="p-2 px-3 mb-1" style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:8px;">' +
          '  <div class="d-flex align-items-center justify-content-between">' +
          '    <strong style="font-size:11.5px; color:#334155;">Branch Office #' + (i + 1) + '</strong>' +
          '    <span class="badge badge-branch-tag">BRANCH</span>' +
          '  </div>' +
          '  <div style="font-size:11.5px; font-weight:500; color:#0f172a;">' + escapeHtmlText(bCity) + '</div>' +
          '  <div style="font-size:11px; color:#64748b;">' + escapeHtmlText(bAddr) + '</div>' +
          '</div>';
      }
    });

    $('#rev_locations_summary_box').html(locsHtml);
  }

  window.toggleVerifyVisual = function(isVerified) {
    if (isVerified) {
      $('#verify_toggle_box').addClass('is-verified');
      $('#verify_status_badge').html('<span class="badge bg-light-success text-success fw-bold px-3 py-2">✓ VERIFIED BY SUPER ADMIN</span>');
    } else {
      $('#verify_toggle_box').removeClass('is-verified');
      $('#verify_status_badge').html('<span class="badge bg-light-warning text-warning fw-bold px-3 py-2">⏳ PENDING VERIFICATION</span>');
    }
  };

  // 11. SUBMIT FORM WITH OUTBOX IDEMPOTENCY
  window.submitCompanyForm = function() {
    var $btn = $('#submitBtn');
    var origHtml = $btn.html();
    $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1" style="width:13px;height:13px;"></span> Updating...');

    var formData = new FormData($('#kt_company_form')[0]);

    // Ensure description & tags are up to date
    if (quillEditor) {
      formData.set('description', quillEditor.root.innerHTML);
      formData.set('kt_docs_tinymce_hidden', quillEditor.root.innerHTML);
    }
    if (window.clientServicesManager) {
      window.clientServicesManager.syncHiddenInput();
      formData.set('tags', $('#tags').val());
    }

    var idempotencyKey = $('#kt_form_idempotency_key').val();

    $.ajax({
      url: '{{ route("superadmin.company.update_company", $account->id) }}',
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
        'X-Idempotency-Key': idempotencyKey
      },
      method: 'POST',
      processData: false,
      contentType: false,
      data: formData,
      success: function(res) {
        $btn.prop('disabled', false).html(origHtml);
        if (res.status === 'success') {
          if (typeof Swal !== 'undefined') {
            Swal.fire({
              text: 'Company profile updated successfully.',
              icon: 'success',
              buttonsStyling: false,
              confirmButtonText: 'OK',
              customClass: { confirmButton: 'btn btn-primary' }
            }).then(function() {
              window.location = '{{ route("superadmin.company.index") }}';
            });
          } else {
            window.location = '{{ route("superadmin.company.index") }}';
          }
        } else {
          if (typeof Swal !== 'undefined') Swal.fire('Error', res.message || 'Update failed.', 'error');
        }
      },
      error: function(err) {
        $btn.prop('disabled', false).html(origHtml);
        var msg = 'Failed to update company. Please try again.';
        if (err.responseJSON && err.responseJSON.message) msg = err.responseJSON.message;
        if (typeof Swal !== 'undefined') Swal.fire('Error', msg, 'error');
      }
    });
  };

  // Helpers
  function escapeHtml(str) {
    if (str === null || str === undefined) return '';
    return String(str)
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;')
      .replace(/'/g, '&#039;');
  }

  function escapeHtmlText(str) {
    return String(str || '').replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
  }

  function escapeHtmlAttr(str) {
    return String(str || '').replace(/&/g, '&amp;').replace(/"/g, '&quot;').replace(/'/g, '&#39;');
  }

  function escapeJs(str) {
    if (!str) return '';
    return String(str).replace(/['"\\]/g, '\\$&');
  }

  // Initialization
  $(document).ready(function() {
    initCompanyTypeData();
    initCompanyTypeEventListeners();
    initQuill();
    if (window.clientServicesManager) {
      window.clientServicesManager.init();
    }
    if (window.clientLocationManager) {
      window.clientLocationManager.init();
    }

    // Delegated Address and City Search Listeners
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

    $(document).on('click', '.client-branch-remove-btn, .btn-remove-subcard', function (e) {
      e.preventDefault();
      e.stopPropagation();
      var target = $(this).attr('data-target') || $(this).data('target');
      if (typeof removeAddressCard === 'function') {
        removeAddressCard(this, target);
      }
    });

    $(document).on('input', 'textarea[name="address[]"], .client-addr-textarea', function () {
      var id = $(this).attr('id') || '';
      var cid = id.replace('address_', '');
      var val = $(this).val() || '';
      var countEl = document.getElementById('address_char_count_' + cid);
      if (countEl) countEl.textContent = val.length;

      $(this).removeClass('is-invalid');
      var errEl = document.getElementById(cid === '0' || cid === 0 ? 'address_err_0' : 'branch_address_err_' + cid);
      if (errEl) errEl.style.display = 'none';
    });

    $(document).on('click', '#btn_add_another_address, #btn_add_first_location', function (e) {
      e.preventDefault();
      if (window.clientLocationManager) {
        window.clientLocationManager.addManualBranch();
      }
    });

    $(document).on('click', function (e) {
      if (!$(e.target).closest('.client-addr-input-wrap, .position-relative').length) {
        $('.client-addr-city-dropdown, .branch-city-dropdown').removeClass('active');
      }
    });
  });

})();
</script>
@endpush