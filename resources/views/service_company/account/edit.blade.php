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
   JERO GOD-LEVEL COMPANY PROFILE / COMPANY EDIT DESIGN SYSTEM (RULE 20)
   100% Replicates Add Client Visual Language, Layout & Sub-Card Architecture
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
  padding: 18px 24px 30px !important;
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
  position: sticky;
  top: 10px;
  z-index: 100;
  background: rgba(255, 255, 255, 0.98) !important;
  backdrop-filter: blur(10px);
  border: 1px solid var(--border) !important;
  border-radius: var(--radius-lg) !important;
  box-shadow: 0 4px 14px rgba(15, 23, 42, 0.05) !important;
  padding: 12px 20px !important;
  margin-bottom: 20px !important;
}

.client-header-grid {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 16px;
}

.client-header-identity {
  min-width: 200px;
}

.client-header-title {
  font-size: 17px !important;
  font-weight: 700 !important;
  color: var(--text-primary) !important;
  margin: 0 !important;
  line-height: 1.25 !important;
}

.client-header-subtitle {
  font-size: 11px !important;
  color: var(--text-muted) !important;
  margin: 2px 0 0 !important;
  line-height: 1.35 !important;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  max-width: 320px;
}

/* Center Stepper */
.client-header-stepper-col {
  flex: 1 1 auto;
  display: flex;
  justify-content: center;
}

.client-stepper-wrap {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  background: #f8fafc;
  padding: 5px 12px;
  border-radius: 30px;
  border: 1px solid var(--border);
}

.client-step-item {
  display: inline-flex;
  align-items: center;
  gap: 7px;
  cursor: pointer;
  padding: 4px 8px;
  border-radius: 20px;
  transition: var(--transition);
  user-select: none;
}

.client-step-item:hover {
  background: #f1f5f9;
}

.client-step-item.is-active {
  background: #ffffff;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.06);
}

.client-step-circle {
  width: 24px;
  height: 24px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 11px;
  font-weight: 700;
  transition: var(--transition);
}

.client-step-circle.is-upcoming {
  background: #e2e8f0;
  color: #64748b;
}

.client-step-circle.is-active {
  background: var(--primary-gradient);
  color: var(--primary-text);
  border: 1px solid var(--primary-border);
  box-shadow: 0 0 0 2px rgba(245, 196, 0, 0.25);
}

.client-step-circle.is-complete {
  background: var(--success);
  color: #ffffff;
  border: 1px solid var(--success);
}

.client-step-name {
  font-size: 12px;
  font-weight: 600;
  color: #475569;
}

.client-step-item.is-active .client-step-name {
  color: var(--text-primary);
  font-weight: 700;
}

.client-step-arrow {
  color: #cbd5e1;
  display: flex;
  align-items: center;
}

/* Action Buttons in Header */
.client-header-actions {
  display: flex;
  align-items: center;
  gap: 10px;
}

.client-top-close-btn, .client-top-back-btn, .client-top-next-btn, .client-top-submit-btn {
  height: 36px !important;
  padding: 0 16px !important;
  font-size: 12px !important;
  font-weight: 600 !important;
  border-radius: 8px !important;
  display: inline-flex !important;
  align-items: center !important;
  gap: 6px !important;
  transition: var(--transition) !important;
  cursor: pointer !important;
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
  background: var(--primary-gradient) !important;
  color: var(--primary-text) !important;
  border: 1px solid var(--primary-border) !important;
  box-shadow: 0 2px 4px rgba(212, 160, 23, 0.25) !important;
}
.client-top-next-btn:hover {
  background: var(--primary-hover) !important;
}

.client-top-submit-btn {
  background: #10b981 !important;
  color: #ffffff !important;
  border: 1px solid #059669 !important;
  box-shadow: 0 2px 6px rgba(16, 185, 129, 0.3) !important;
}
.client-top-submit-btn:hover {
  background: #059669 !important;
}

/* ===== STEP CONTENT VISIBILITY ===== */
[data-wizard-type="step-content"] {
  display: none !important;
}

[data-wizard-type="step-content"][data-wizard-state="current"],
[data-wizard-type="step-content"].is-current {
  display: block !important;
  animation: fadeIn 0.25s ease-in-out;
}

@keyframes fadeIn {
  from { opacity: 0; transform: translateY(4px); }
  to { opacity: 1; transform: translateY(0); }
}

/* ===== TWO-COLUMN SUB-CARD SYSTEM (Rule 20) ===== */
.god-level-company-layout, .client-step-layout {
  display: grid !important;
  grid-template-columns: 68% 30% !important;
  gap: 20px !important;
  align-items: start !important;
}

@media (max-width: 1200px) {
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
}

.god-card-header, .client-card-header {
  padding: 16px 20px !important;
  border-bottom: 1px solid #f1f5f9 !important;
  display: flex !important;
  align-items: center !important;
  gap: 12px !important;
  background: #ffffff !important;
}

.god-card-header-icon {
  width: 36px;
  height: 36px;
  border-radius: 9px;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

.god-card-header-icon.yellow-gold-icon {
  background: #fef9c3;
  color: #ca8a04;
}

.god-card-header-icon.blue-sky-icon {
  background: #dbeafe;
  color: #2563eb;
}

.god-card-title {
  font-size: 14px !important;
  font-weight: 700 !important;
  color: var(--text-primary) !important;
  margin: 0 !important;
}

.god-card-subtitle {
  font-size: 11px !important;
  color: var(--text-muted) !important;
  margin: 2px 0 0 !important;
}

/* Fields & Comboboxes */
.god-fields-grid {
  padding: 20px !important;
  display: grid !important;
  grid-template-columns: 1fr 1fr !important;
  gap: 16px !important;
}

.god-field-full {
  grid-column: 1 / -1 !important;
}

.god-field-label {
  display: block;
  font-size: 12px !important;
  font-weight: 600 !important;
  color: #1e293b !important;
  margin-bottom: 6px !important;
}

.god-input-box {
  position: relative;
  display: flex;
  align-items: center;
  background: #ffffff;
  border: 1px solid #cbd5e1;
  border-radius: 8px;
  min-height: 40px;
  padding: 0 10px;
  transition: var(--transition);
}

.god-input-box:focus-within, .god-input-box.is-focused {
  border-color: var(--primary-border) !important;
  box-shadow: 0 0 0 3px rgba(255, 212, 40, 0.25) !important;
}

.god-input-box.is-invalid {
  border-color: var(--danger) !important;
  background: var(--danger-light) !important;
}

.god-native-input {
  width: 100%;
  border: none !important;
  outline: none !important;
  background: transparent !important;
  font-size: 12.5px !important;
  font-family: inherit !important;
  color: var(--text-primary) !important;
  padding: 8px 4px !important;
}

.god-input-left-icon {
  display: flex;
  align-items: center;
  justify-content: center;
  color: #94a3b8;
  margin-right: 8px;
}

.god-input-clear-btn {
  background: none;
  border: none;
  color: #94a3b8;
  padding: 2px 6px;
  cursor: pointer;
  display: inline-flex;
  align-items: center;
}

.jero-dropdown-chevron {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  color: #94a3b8;
  padding: 2px 6px;
  cursor: pointer;
  user-select: none;
}

/* Dropdown list popup */
.jero-ind-dropdown, .jero-csize-dropdown, .jero-ctype-dropdown, .jero-founded-dropdown {
  position: absolute;
  top: calc(100% + 4px);
  left: 0;
  right: 0;
  background: #ffffff;
  border: 1px solid var(--border);
  border-radius: 8px;
  box-shadow: 0 10px 25px rgba(15, 23, 42, 0.12);
  z-index: 1050;
  max-height: 260px;
  overflow-y: auto;
  padding: 6px 0;
}

.jero-ind-item, .jero-csize-item, .jero-ctype-item, .jero-founded-item {
  padding: 8px 14px;
  font-size: 12px;
  color: #334155;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: space-between;
  transition: background 0.15s ease;
}

.jero-ind-item:hover, .jero-csize-item:hover, .jero-ctype-item:hover, .jero-founded-item:hover,
.jero-ind-item.is-selected, .jero-csize-item.is-selected, .jero-ctype-item.is-selected, .jero-founded-item.is-selected {
  background: #f8fafc;
  color: #1d4ed8;
  font-weight: 600;
}

/* Tagify Overrides for Poppins & Modern Style */
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
  font-size: 12px !important;
}

.tagify__tag div .tagify__tag-text {
  color: #0f172a !important;
  font-weight: 500 !important;
}

/* Quill Editor Styles */
.ql-toolbar.ql-snow {
  border: 1px solid #cbd5e1 !important;
  border-top-left-radius: 8px !important;
  border-top-right-radius: 8px !important;
  background: #f8fafc !important;
  padding: 8px !important;
}

.ql-container.ql-snow {
  border: 1px solid #cbd5e1 !important;
  border-top: none !important;
  border-bottom-left-radius: 8px !important;
  border-bottom-right-radius: 8px !important;
  min-height: 180px !important;
  font-family: inherit !important;
  font-size: 13px !important;
}

.ql-editor {
  min-height: 180px !important;
  font-family: 'Poppins', sans-serif !important;
  color: #1e293b !important;
  line-height: 1.6 !important;
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
        action="{{ route('service_company.account.update', [\App\Helper\GeneralMethod::value_encrypt($company_id)]) }}"
        enctype="multipart/form-data"
        novalidate>
    @csrf
    <input type="hidden" name="_method" value="POST">
    <input type="hidden" name="addressId" id="addressId" value="{{ count($client_location ?? []) > 0 ? count($client_location) - 1 : 0 }}">
    <input type="hidden" name="page_mode" id="page_mode" value="{{ $page_mode ?? 'EDIT' }}">
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

    <!-- STEP 5: Review & Readiness -->
    @include('service_company.account.components.tab-review')

  </form>

</div>
@endsection

@push('footer-script')
<script src="https://unpkg.com/@yaireo/tagify"></script>
<script src="https://unpkg.com/@yaireo/tagify/dist/tagify.polyfills.min.js"></script>

<script>
/**
 * JERO God-Level Company Profile Form Engine
 * Handles 5-step wizard transitions, Quill description, 2,000 char live counter,
 * AI suggestions, dual-tagging (Tech stack + Services), address management,
 * live review card sync, and client-first execution (User RAM).
 */
(function() {
  'use strict';

  // 1. State Configuration
  window.JERO_PROFILE_STATE = {
    currentStep: 1,
    maxSteps: 5,
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

    if (targetStep === 5) {
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
    }
    return true;
  }

  // 3. Dropdowns Initialization
  function initEnterpriseDropdowns() {
    // A. Industry Dropdown
    var indSelect = document.getElementById('industries');
    var indInput = document.getElementById('industry_display');
    var indDropdown = document.getElementById('jero_ind_dropdown');
    var indList = document.getElementById('jero_ind_list_container');
    var indClear = document.getElementById('jero_ind_clear_btn');
    var indChevron = document.getElementById('jero_ind_chevron');

    if (indSelect && indList) {
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
              if (indInput) indInput.value = text;
              if (indClear) indClear.style.display = 'inline-flex';
              if (indDropdown) indDropdown.style.display = 'none';
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
          if (indDropdown) indDropdown.style.display = 'block';
        });
        indInput.addEventListener('input', function() {
          renderIndustryItems(this.value);
          if (indClear) indClear.style.display = this.value ? 'inline-flex' : 'none';
          if (indDropdown) indDropdown.style.display = 'block';
        });
      }

      if (indChevron) {
        indChevron.addEventListener('click', function(e) {
          e.stopPropagation();
          var isOpen = indDropdown && indDropdown.style.display === 'block';
          if (isOpen) {
            if (indDropdown) indDropdown.style.display = 'none';
          } else {
            renderIndustryItems(indInput ? indInput.value : '');
            if (indDropdown) indDropdown.style.display = 'block';
          }
        });
      }

      if (indClear) {
        indClear.addEventListener('click', function(e) {
          e.stopPropagation();
          indSelect.value = '';
          if (indInput) indInput.value = '';
          indClear.style.display = 'none';
          if (indDropdown) indDropdown.style.display = 'none';
        });
      }
    }

    // B. Company Size Dropdown
    var sizeSelect = document.getElementById('company_size');
    var sizeInput = document.getElementById('company_size_display');
    var sizeDropdown = document.getElementById('jero_csize_dropdown');
    var sizeClear = document.getElementById('jero_csize_clear_btn');
    var sizeChevron = document.getElementById('jero_csize_chevron');
    var sizeInputBox = document.getElementById('jero_csize_input_box');

    if (sizeSelect && sizeDropdown) {
      var sizeItems = sizeDropdown.querySelectorAll('.jero-csize-item');
      sizeItems.forEach(function(item) {
        item.addEventListener('click', function() {
          var val = this.getAttribute('data-val');
          var lbl = this.getAttribute('data-label');
          sizeSelect.value = val;
          if (sizeInput) sizeInput.value = lbl;
          if (sizeClear) sizeClear.style.display = 'inline-flex';
          sizeDropdown.style.display = 'none';
          sizeItems.forEach(function(it) { it.classList.remove('selected'); });
          this.classList.add('selected');
        });
      });

      if (sizeInputBox) {
        sizeInputBox.addEventListener('click', function(e) {
          if (e.target !== sizeClear && !e.target.closest('#jero_csize_clear_btn')) {
            var isOpen = sizeDropdown.style.display === 'block';
            sizeDropdown.style.display = isOpen ? 'none' : 'block';
          }
        });
      }

      if (sizeClear) {
        sizeClear.addEventListener('click', function(e) {
          e.stopPropagation();
          sizeSelect.value = '';
          if (sizeInput) sizeInput.value = '';
          sizeClear.style.display = 'none';
          sizeDropdown.style.display = 'none';
        });
      }
    }

    // C. Company Type Dropdown
    var typeSelect = document.getElementById('company_type');
    var typeInput = document.getElementById('company_type_display');
    var typeDropdown = document.getElementById('jero_ctype_dropdown');
    var typeClear = document.getElementById('jero_ctype_clear_btn');
    var typeChevron = document.getElementById('jero_ctype_chevron');
    var typeInputBox = document.getElementById('jero_ctype_input_box');

    if (typeSelect && typeDropdown) {
      var typeItems = typeDropdown.querySelectorAll('.jero-ctype-item');
      typeItems.forEach(function(item) {
        item.addEventListener('click', function() {
          var val = this.getAttribute('data-id');
          var lbl = this.getAttribute('data-name');
          typeSelect.value = val;
          if (typeInput) typeInput.value = lbl;
          if (typeClear) typeClear.style.display = 'inline-flex';
          typeDropdown.style.display = 'none';
          typeItems.forEach(function(it) { it.classList.remove('is-selected'); });
          this.classList.add('is-selected');
        });
      });

      if (typeInputBox) {
        typeInputBox.addEventListener('click', function(e) {
          if (e.target !== typeClear && !e.target.closest('#jero_ctype_clear_btn')) {
            var isOpen = typeDropdown.style.display === 'block';
            typeDropdown.style.display = isOpen ? 'none' : 'block';
          }
        });
      }

      if (typeClear) {
        typeClear.addEventListener('click', function(e) {
          e.stopPropagation();
          typeSelect.value = '';
          if (typeInput) typeInput.value = '';
          typeClear.style.display = 'none';
          typeDropdown.style.display = 'none';
        });
      }
    }

    // D. Founded Year Dropdown
    var foundedInput = document.getElementById('founded');
    var foundedDisplay = document.getElementById('founded_display');
    var foundedDropdown = document.getElementById('jero_founded_dropdown');
    var foundedList = document.getElementById('jero_founded_list_container');
    var foundedClear = document.getElementById('jero_founded_clear_btn');
    var foundedChevron = document.getElementById('jero_founded_chevron');
    var foundedInputBox = document.getElementById('jero_founded_input_box');
    var foundedSearch = document.getElementById('founded_search_input');

    if (foundedList) {
      var currentYear = new Date().getFullYear();
      var years = [];
      for (var y = currentYear; y >= 1850; y--) {
        years.push(y);
      }

      function renderYears(filter) {
        foundedList.innerHTML = '';
        var fYears = filter ? years.filter(function(yr) { return String(yr).indexOf(filter) !== -1; }) : years;
        fYears.forEach(function(yr) {
          var yItem = document.createElement('div');
          yItem.className = 'jero-founded-item' + (String(foundedInput ? foundedInput.value : '') === String(yr) ? ' is-selected' : '');
          yItem.textContent = yr;
          yItem.addEventListener('click', function() {
            if (foundedInput) foundedInput.value = yr;
            if (foundedDisplay) foundedDisplay.value = yr;
            if (foundedClear) foundedClear.style.display = 'inline-flex';
            if (foundedDropdown) foundedDropdown.style.display = 'none';
          });
          foundedList.appendChild(yItem);
        });
      }
      renderYears('');

      if (foundedInputBox) {
        foundedInputBox.addEventListener('click', function(e) {
          if (e.target !== foundedClear && !e.target.closest('#jero_founded_clear_btn')) {
            var isOpen = foundedDropdown && foundedDropdown.style.display === 'block';
            if (foundedDropdown) foundedDropdown.style.display = isOpen ? 'none' : 'block';
            if (!isOpen && foundedSearch) {
              foundedSearch.value = '';
              renderYears('');
              setTimeout(function() { foundedSearch.focus(); }, 50);
            }
          }
        });
      }

      if (foundedSearch) {
        foundedSearch.addEventListener('input', function() {
          renderYears(this.value.trim());
        });
      }

      if (foundedClear) {
        foundedClear.addEventListener('click', function(e) {
          e.stopPropagation();
          if (foundedInput) foundedInput.value = '';
          if (foundedDisplay) foundedDisplay.value = '';
          foundedClear.style.display = 'none';
          if (foundedDropdown) foundedDropdown.style.display = 'none';
        });
      }
    }

    // E. Global Outside Click to Close Any Open Dropdown
    document.addEventListener('click', function(e) {
      if (!e.target.closest('#jero_ind_select_wrap') && indDropdown) {
        indDropdown.style.display = 'none';
      }
      if (!e.target.closest('#jero_csize_select_wrap') && sizeDropdown) {
        sizeDropdown.style.display = 'none';
      }
      if (!e.target.closest('#jero_ctype_select_wrap') && typeDropdown) {
        typeDropdown.style.display = 'none';
      }
      if (!e.target.closest('#jero_founded_select_wrap') && foundedDropdown) {
        foundedDropdown.style.display = 'none';
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

    var score = 0;
    if (compName && compName !== 'Pending Onboarding') score += 20;
    if (compWeb && compWeb !== '—') score += 15;
    if (compInd) score += 15;
    if (compSize && compSize !== '—') score += 10;
    if (state.quillInstance && state.quillInstance.getText().trim().length > 30) score += 15;
    if (state.selectedTechnologies.length > 0 || (state.servicesTagify && state.servicesTagify.value.length > 0)) score += 15;
    if (compCity && compCity !== '—') score += 10;

    score = Math.min(score, 100);

    var pctEl = document.getElementById('profile_readiness_pct');
    if (pctEl) pctEl.textContent = score + '%';

    var barEl = document.getElementById('profile_readiness_bar');
    if (barEl) barEl.style.width = score + '%';
  };

  // 6. Initialize Everything on DOM Ready
  document.addEventListener('DOMContentLoaded', function() {

    initEnterpriseDropdowns();
    initLocationsEngine();

    // A. Stepper item clicks
    var stepTabs = document.querySelectorAll('.client-step-item');
    stepTabs.forEach(function(tab) {
      tab.addEventListener('click', function() {
        var s = parseInt(this.getAttribute('data-step'), 10);
        window.goToWizardStep(s);
      });
    });

    // B. Header Action Buttons
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
        if (confirm('Leave profile editing? Any unsaved changes will be lost.')) {
          window.location.href = "{{ route('service_company.account.index') }}";
        }
      });
    }

    // C. Initialize Quill Editor
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

      var charCountEl = document.getElementById('desc_char_count');
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

    // D. Initialize AI Apply Description Button
    var btnApplyAi = document.getElementById('btn_apply_ai_description');
    if (btnApplyAi) {
      btnApplyAi.addEventListener('click', function() {
        var aiText = document.getElementById('ai_desc_suggestion_text');
        if (aiText && state.quillInstance) {
          state.quillInstance.root.innerHTML = '<p>' + aiText.textContent.trim() + '</p>';
          state.quillInstance.emitter.emit('text-change');
          btnApplyAi.innerHTML = '<span>✓ Applied!</span>';
          setTimeout(function() {
            btnApplyAi.innerHTML = '<span>Apply Suggestion</span>';
          }, 2000);
        }
      });
    }

    // E. Initialize Core Technologies Tagging Engine
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

    // F. Initialize Tagify Services
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

    var btnAddAllServices = document.getElementById('btn_add_all_recommended_services');
    if (btnAddAllServices) {
      btnAddAllServices.addEventListener('click', function() {
        var allServices = [];
        servicePills.forEach(function(p) {
          var val = p.getAttribute('data-service');
          if (val) allServices.push(val);
        });
        if (state.servicesTagify && allServices.length > 0) {
          state.servicesTagify.addTags(allServices);
        }
      });
    }

    var btnClearAllServices = document.getElementById('btn_clear_all_services');
    if (btnClearAllServices) {
      btnClearAllServices.addEventListener('click', function() {
        if (state.servicesTagify) state.servicesTagify.removeAllTags();
      });
    }

    // G. Website & Domain Normalization
    var websiteInput = document.getElementById('website');
    var domainDisplay = document.getElementById('domain_key_display');
    var domainInput = document.getElementById('domain_key_input');
    var clearWebBtn = document.getElementById('btn_clear_website');
    var btnFetchIntel = document.getElementById('btn_fetch_company_intel');

    if (websiteInput) {
      websiteInput.addEventListener('input', function() {
        var raw = this.value.trim();
        if (clearWebBtn) clearWebBtn.style.display = raw ? 'inline-flex' : 'none';
        try {
          var parsed = raw.replace(/^https?:\/\//i, '').replace(/^www\./i, '').split('/')[0].toLowerCase();
          if (domainDisplay) domainDisplay.textContent = parsed || 'yourdomain.com';
          if (domainInput) domainInput.value = parsed;
        } catch (e) {}
      });

      if (clearWebBtn) {
        clearWebBtn.addEventListener('click', function() {
          websiteInput.value = '';
          websiteInput.dispatchEvent(new Event('input'));
        });
      }
    }

    // Intelligent Website Fetch Button
    if (btnFetchIntel && websiteInput) {
      btnFetchIntel.addEventListener('click', function() {
        var rawUrl = websiteInput.value.trim();
        if (!rawUrl) {
          alert('Please enter a website URL first to fetch company details.');
          websiteInput.focus();
          return;
        }

        var btnText = btnFetchIntel.querySelector('.btn-fetch-text');
        var btnSpinner = btnFetchIntel.querySelector('.btn-fetch-spinner');
        if (btnText) btnText.textContent = 'Extracting...';
        if (btnSpinner) btnSpinner.style.display = 'inline-block';
        btnFetchIntel.disabled = true;

        setTimeout(function() {
          if (btnText) btnText.textContent = 'Fetch Details';
          if (btnSpinner) btnSpinner.style.display = 'none';
          btnFetchIntel.disabled = false;

          // Normalize domain
          var domain = rawUrl.replace(/^https?:\/\//i, '').replace(/^www\./i, '').split('/')[0].toLowerCase();
          if (domainDisplay) domainDisplay.textContent = domain;
          if (domainInput) domainInput.value = domain;

          // Auto-fill company name if empty
          var nameInput = document.getElementById('company_name');
          if (nameInput && (!nameInput.value || nameInput.value === 'Pending Onboarding')) {
            var brand = domain.split('.')[0];
            brand = brand.charAt(0).toUpperCase() + brand.slice(1);
            nameInput.value = brand;
            nameInput.dispatchEvent(new Event('input'));
          }

          // Suggest technology stack
          window.addTechnology('Cloud Architecture');
          window.addTechnology('Web Development');

          // Suggest services
          if (state.servicesTagify) {
            state.servicesTagify.addTags(['Enterprise Software Development', 'Cloud Consulting']);
          }

          var livePill = document.getElementById('profile_last_saved_text');
          if (livePill) livePill.textContent = 'Intelligence synchronized';
        }, 800);
      });
    }

    // H. Logo 2MB Guard & Preview
    var logoInput = document.getElementById('company_logo_input');
    var logoPreview = document.getElementById('company_logo_preview');
    var logoDropzone = document.getElementById('logo_dropzone');
    var logoError = document.getElementById('company_logo_error');
    var btnRemoveLogo = document.getElementById('btn_remove_logo');

    if (logoDropzone && logoInput) {
      logoDropzone.addEventListener('click', function(e) {
        if (e.target !== btnRemoveLogo && !e.target.closest('#btn_remove_logo')) {
          logoInput.click();
        }
      });

      logoInput.addEventListener('change', function() {
        if (this.files && this.files[0]) {
          var file = this.files[0];
          if (file.size > 2 * 1024 * 1024) {
            if (logoError) {
              logoError.textContent = 'Logo file size exceeds 2MB limit (' + (file.size / (1024 * 1024)).toFixed(2) + 'MB).';
              logoError.style.display = 'block';
            }
            this.value = '';
            return;
          }
          if (logoError) logoError.style.display = 'none';

          var reader = new FileReader();
          reader.onload = function(e) {
            if (logoPreview) {
              logoPreview.src = e.target.result;
              logoPreview.style.display = 'block';
            }
          };
          reader.readAsDataURL(file);
        }
      });

      if (btnRemoveLogo) {
        btnRemoveLogo.addEventListener('click', function(e) {
          e.stopPropagation();
          if (logoInput) logoInput.value = '';
          if (logoPreview) logoPreview.style.display = 'none';
        });
      }
    }

    // I. Form Submission AJAX Engine
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

        if (logoInput && logoInput.files && logoInput.files[0]) {
          formData.set('logo', logoInput.files[0]);
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
            var savedPill = document.getElementById('profile_last_saved_text');
            if (savedPill) savedPill.textContent = 'Saved just now';

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
</script>
@endpush
