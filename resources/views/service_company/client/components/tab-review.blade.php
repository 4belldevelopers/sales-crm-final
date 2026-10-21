<!--begin::Step 6: Review and Submit (Pixel-Perfect Enterprise Design Aligned with Reference media_1792566743106_f52e3a53.png)-->
<div class="pb-5 client-review-step" data-wizard-type="step-content">
  <div class="client-review-layout">
    
    <!-- ==============================================================
         LEFT COLUMN (~68% width): Scannable Summary Review Cards
         ============================================================== -->
    <div class="client-review-main-column">

      <!-- ==========================================
           CARD 1: Company Profile Banner
           ========================================== -->
      <div class="card client-review-section-card client-review-company-card">
        <div class="card-body p-5 d-flex align-items-center justify-content-between flex-wrap gap-3">
          <div class="d-flex align-items-center gap-4 min-w-0 flex-grow-1">
            <!-- Logo / Initials Avatar Box -->
            <div id="client_review_logo_box" class="client-review-avatar-box">
              <span id="client_review_initials">4B</span>
            </div>
            <div class="min-w-0 flex-grow-1">
              <div class="d-flex align-items-center flex-wrap gap-2 mb-1">
                <h3 class="client-review-company-name span_name text-truncate">4Bell Technology</h3>
                <span class="client-badge-internal-client">Internal Client</span>
              </div>
              <div class="client-review-company-meta d-flex align-items-center flex-wrap gap-4">
                <span class="meta-item">
                  <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#64748b" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect>
                    <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path>
                  </svg>
                  <span class="span_industry">Information Technology &amp; Services</span>
                </span>
                <span class="meta-item">
                  <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#64748b" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                    <circle cx="9" cy="7" r="4"></circle>
                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                    <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                  </svg>
                  <span class="span_company_sizes">201 - 500 employees</span>
                </span>
                <span class="meta-item">
                  <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#64748b" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                    <circle cx="12" cy="10" r="3"></circle>
                  </svg>
                  <span class="span_primary_location">Pune, Maharashtra, India</span>
                </span>
              </div>
            </div>
          </div>
          <button type="button" class="btn btn-sm client-review-edit-btn" onclick="window.goToWizardStep ? window.goToWizardStep(1) : false;">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>
            <span>Edit Company</span>
          </button>
        </div>
      </div>

      <!-- ==========================================
           CARD 2: Company Description & Services
           ========================================== -->
      <div class="card client-review-section-card">
        <div class="card-header border-0 py-3 px-5 d-flex align-items-center justify-content-between">
          <div class="d-flex align-items-center gap-3">
            <div class="client-review-icon-box">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#ca8a04" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect>
                <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path>
              </svg>
            </div>
            <h4 class="client-review-card-title">Company Description</h4>
          </div>
          <button type="button" class="btn btn-sm client-review-edit-btn" onclick="window.goToWizardStep ? window.goToWizardStep(2) : false;">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>
            <span>Edit</span>
          </button>
        </div>
        <div class="card-body px-5 py-4 pt-1">
          <div class="client-review-description-text span_kt_docs_tinymce_hidden mb-3">
            <span class="text-muted">No description provided.</span>
          </div>
          <!-- Service Offerings Badges -->
          <div class="client-review-services-wrap d-flex flex-wrap gap-2" id="client_review_service_badges">
            <!-- Populated by populateReviewSummaries() -->
          </div>
        </div>
      </div>

      <!-- ==========================================
           CARD 3: Addresses
           ========================================== -->
      <div class="card client-review-section-card">
        <div class="card-header border-0 py-3 px-5 d-flex align-items-center justify-content-between">
          <div class="d-flex align-items-center gap-3">
            <div class="client-review-icon-box">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#ca8a04" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                <circle cx="12" cy="10" r="3"></circle>
              </svg>
            </div>
            <h4 class="client-review-card-title">Addresses</h4>
          </div>
          <button type="button" class="btn btn-sm client-review-edit-btn" onclick="window.goToWizardStep ? window.goToWizardStep(3) : false;">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>
            <span>Edit</span>
          </button>
        </div>
        <div class="card-body px-5 py-4 pt-1">
          <div class="client-review-addresses-split">
            <!-- Headquarters Box -->
            <div class="client-review-hq-card">
              <div class="d-flex align-items-center gap-2 mb-2 flex-wrap">
                <span class="badge-hq-pill">HQ</span>
                <span class="hq-city-title text-truncate" id="client_review_hq_city">Pune, Maharashtra, India</span>
                <span class="badge-hq-tag">Headquarters</span>
              </div>
              <div class="hq-address-line d-flex align-items-start gap-2">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#64748b" stroke-width="2" class="mt-1 flex-shrink-0"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                <span id="client_review_hq_address" class="text-break">Building name, street, area, landmark, pincode...</span>
              </div>
            </div>

            <!-- Additional Locations Box -->
            <div class="client-review-branch-card">
              <div class="branch-header mb-2" id="client_review_additional_header">
                Additional Locations (0)
              </div>
              <div class="branch-list" id="client_review_additional_list">
                <div class="text-muted" style="font-size:12px;">No additional branch locations added.</div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- ==========================================
           CARD 4: Client Contacts
           ========================================== -->
      <div class="card client-review-section-card">
        <div class="card-header border-0 py-3 px-5 d-flex align-items-center justify-content-between">
          <div class="d-flex align-items-center gap-3">
            <div class="client-review-icon-box">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#ca8a04" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                <circle cx="9" cy="7" r="4"></circle>
                <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
              </svg>
            </div>
            <h4 class="client-review-card-title">Client Contacts</h4>
          </div>
          <button type="button" class="btn btn-sm client-review-edit-btn" onclick="window.goToWizardStep ? window.goToWizardStep(4) : false;">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>
            <span>Edit</span>
          </button>
        </div>
        <div class="card-body px-5 py-4 pt-1">
          <div class="client-review-contacts-grid" id="client_review_contacts_grid">
            <!-- Dynamically populated contact cards -->
          </div>
        </div>
      </div>

      <!-- ==========================================
           CARD 5: Company Profiles
           ========================================== -->
      <div class="card client-review-section-card">
        <div class="card-header border-0 py-3 px-5 d-flex align-items-center justify-content-between">
          <div class="d-flex align-items-center gap-3">
            <div class="client-review-icon-box">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#ca8a04" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="10"></circle>
                <line x1="2" y1="12" x2="22" y2="12"></line>
                <path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"></path>
              </svg>
            </div>
            <h4 class="client-review-card-title">Company Profiles</h4>
          </div>
          <button type="button" class="btn btn-sm client-review-edit-btn" onclick="window.goToWizardStep ? window.goToWizardStep(5) : false;">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>
            <span>Edit</span>
          </button>
        </div>
        <div class="card-body px-5 py-4 pt-1">
          <div class="client-review-profiles-wrap" id="client_review_social_list">
            <!-- Dynamically populated profile items -->
          </div>
          <div id="client_review_social_empty" class="text-muted py-2" style="display:none; font-size:12px;">
            No official company profiles added.
          </div>
        </div>
      </div>

    </div>

    <!-- ==============================================================
         RIGHT SIDEBAR (~32% width): Confirmation, Checklist & Actions
         ============================================================== -->
    <aside class="client-review-side-column">
      
      <!-- Card A: Client Profile Ready -->
      <div class="card client-confirmation-card">
        <div class="d-flex align-items-center gap-3 mb-3">
          <div class="client-ready-icon-circle">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#15803d" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
              <polyline points="20 6 9 17 4 12"/>
            </svg>
          </div>
          <div>
            <h4 class="client-ready-title">Client Profile Ready</h4>
            <p class="client-ready-subtitle">All required information has been completed successfully.</p>
          </div>
        </div>

        <!-- Completion Status Meter -->
        <div class="client-completion-box mb-3">
          <div class="d-flex align-items-center justify-content-between mb-2">
            <span class="completion-label">Completion Status</span>
            <span class="completion-value" id="client_review_completion_percent">100%</span>
          </div>
          <div class="completion-progress-track">
            <div class="completion-progress-bar" id="client_review_completion_bar" style="width: 100%;"></div>
          </div>
        </div>

        <!-- Checklist -->
        <div class="client-checklist-group">
          <div class="checklist-item">
            <span class="checklist-icon"><svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg></span>
            <span class="checklist-text">Company information</span>
            <span class="checklist-status">Completed</span>
          </div>
          <div class="checklist-item">
            <span class="checklist-icon"><svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg></span>
            <span class="checklist-text">Description &amp; services</span>
            <span class="checklist-status">Completed</span>
          </div>
          <div class="checklist-item">
            <span class="checklist-icon"><svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg></span>
            <span class="checklist-text">Addresses &amp; locations</span>
            <span class="checklist-status">Completed</span>
          </div>
          <div class="checklist-item">
            <span class="checklist-icon"><svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg></span>
            <span class="checklist-text">Client contacts</span>
            <span class="checklist-status">Completed</span>
          </div>
          <div class="checklist-item">
            <span class="checklist-icon"><svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg></span>
            <span class="checklist-text">Company profiles</span>
            <span class="checklist-status">Completed</span>
          </div>
        </div>
      </div>

      <!-- Card B: Before you create this client -->
      <div class="card client-before-create-card mt-3">
        <div class="d-flex align-items-center gap-2 mb-2">
          <div class="client-tip-icon-box">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#ca8a04" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M9 18h6"/><path d="M10 22h4"/>
              <path d="M15.09 14c.18-.98.65-1.74 1.41-2.5A4.65 4.65 0 0 0 18 8 6 6 0 0 0 6 8c0 1 .23 2.23 1.5 3.5A4.61 4.61 0 0 1 8.91 14"/>
            </svg>
          </div>
          <h5 class="before-create-title">Before you create this client</h5>
        </div>
        <div class="before-create-list">
          <div class="before-item">
            <span class="before-item-check"><svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg></span>
            <span>Please review all the information for accuracy.</span>
          </div>
          <div class="before-item">
            <span class="before-item-check"><svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg></span>
            <span>You can go back and edit any section if required.</span>
          </div>
          <div class="before-item">
            <span class="before-item-check"><svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg></span>
            <span>This client will be added to your internal client list.</span>
          </div>
          <div class="before-item">
            <span class="before-item-check"><svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg></span>
            <span>You can always update this information later.</span>
          </div>
        </div>
      </div>

      <!-- Card C: What happens next? -->
      <div class="card client-next-steps-card mt-3">
        <div class="d-flex align-items-center gap-2 mb-2">
          <div class="client-next-icon-box">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/>
              <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/>
            </svg>
          </div>
          <h5 class="next-steps-title">What happens next?</h5>
        </div>
        <div class="next-steps-list">
          <div class="next-step-item">
            <span class="step-num">1</span>
            <span class="step-desc">Client will be created and added to your internal client list.</span>
          </div>
          <div class="next-step-item">
            <span class="step-num">2</span>
            <span class="step-desc">You can start creating job requests and managing hiring activities.</span>
          </div>
          <div class="next-step-item">
            <span class="step-num">3</span>
            <span class="step-desc">You can invite your team members to collaborate on this client.</span>
          </div>
        </div>
      </div>

      <!-- Action Buttons -->
      <div class="client-sidebar-actions mt-3 d-flex flex-column gap-2">
        @if (isset($page_event) && $page_event == 'EDIT')
          <button type="button" class="btn client-sidebar-primary-btn updatefrm" id="sidebar_update_client_btn">
            <span>Update Client</span>
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
          </button>
        @else
          <button type="button" class="btn client-sidebar-primary-btn submitfrm" id="sidebar_create_client_btn">
            <span>Create Client</span>
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
          </button>
        @endif
        <button type="button" class="btn client-sidebar-back-btn" onclick="window.goToWizardStep ? window.goToWizardStep(5) : false;">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M19 12H5"/><path d="m12 19-7-7 7-7"/></svg>
          <span>Back to Social Links</span>
        </button>
      </div>

    </aside>

  </div>
</div>

<style>
  /* --------------------------------------------------------------------------
     Step 6: Review & Create Client (Matches Reference media_1792566743106_f52e3a53.png)
     -------------------------------------------------------------------------- */
  .client-review-layout {
    display: grid !important;
    width: 100% !important;
    grid-template-columns: minmax(0, 68fr) minmax(0, 32fr) !important;
    gap: 20px !important;
    align-items: start !important;
  }

  .client-review-main-column {
    display: flex !important;
    flex-direction: column !important;
    gap: 16px !important;
    min-width: 0 !important;
  }

  .client-review-side-column {
    display: flex !important;
    flex-direction: column !important;
    min-width: 0 !important;
  }

  .client-review-section-card {
    background: #ffffff !important;
    border: 1px solid #e2e8f0 !important;
    border-radius: 12px !important;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02) !important;
    overflow: hidden !important;
  }

  .client-review-avatar-box {
    width: 54px !important;
    height: 54px !important;
    border-radius: 12px !important;
    background: #ede9fe !important;
    color: #7c3aed !important;
    font-weight: 700 !important;
    font-size: 19px !important;
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    flex-shrink: 0 !important;
    background-size: cover !important;
    background-position: center !important;
    background-repeat: no-repeat !important;
  }

  .client-review-company-name {
    font-size: 17px !important;
    font-weight: 700 !important;
    color: #0f172a !important;
    margin: 0 !important;
  }

  .client-badge-internal-client {
    background: #fef9c3 !important;
    color: #854d0e !important;
    border: 1px solid #fef08a !important;
    font-size: 11px !important;
    font-weight: 600 !important;
    padding: 2px 10px !important;
    border-radius: 9999px !important;
    display: inline-flex !important;
    align-items: center !important;
  }

  .client-review-company-meta .meta-item {
    font-size: 12px !important;
    color: #64748b !important;
    display: inline-flex !important;
    align-items: center !important;
    gap: 6px !important;
  }
  .client-review-company-meta .meta-item svg,
  .client-review-company-meta .meta-item i {
    color: #94a3b8 !important;
    font-size: 14px !important;
  }

  .client-review-edit-btn {
    background: #eff6ff !important;
    border: 1px solid #bfdbfe !important;
    color: #2563eb !important;
    font-weight: 600 !important;
    font-size: 12px !important;
    border-radius: 6px !important;
    padding: 5px 12px !important;
    display: inline-flex !important;
    align-items: center !important;
    gap: 5px !important;
    cursor: pointer !important;
    transition: all 0.15s ease !important;
  }
  .client-review-edit-btn:hover {
    background: #dbeafe !important;
    border-color: #93c5fd !important;
    color: #1d4ed8 !important;
  }

  .client-review-icon-box {
    width: 32px !important;
    height: 32px !important;
    border-radius: 8px !important;
    background: #fffbeb !important;
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    flex-shrink: 0 !important;
  }

  .client-review-card-title {
    font-size: 14.5px !important;
    font-weight: 700 !important;
    color: #0f172a !important;
    margin: 0 !important;
  }

  .client-review-description-text {
    font-size: 12.5px !important;
    line-height: 1.55 !important;
    color: #334155 !important;
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

  /* Addresses split */
  .client-review-addresses-split {
    display: grid !important;
    grid-template-columns: minmax(0, 1fr) minmax(0, 1fr) !important;
    gap: 14px !important;
  }

  .client-review-hq-card {
    background: #fffdf0 !important;
    border: 1px solid #fef08a !important;
    border-radius: 10px !important;
    padding: 14px 16px !important;
  }

  .badge-hq-pill {
    background: #fef08a !important;
    color: #854d0e !important;
    font-size: 10.5px !important;
    font-weight: 700 !important;
    padding: 2px 6px !important;
    border-radius: 4px !important;
  }

  .hq-city-title {
    font-size: 13px !important;
    font-weight: 700 !important;
    color: #0f172a !important;
  }

  .badge-hq-tag {
    background: #dcfce7 !important;
    color: #15803d !important;
    font-size: 10px !important;
    font-weight: 600 !important;
    padding: 2px 7px !important;
    border-radius: 4px !important;
  }

  .hq-address-line {
    font-size: 11.5px !important;
    color: #64748b !important;
    line-height: 1.45 !important;
  }

  .client-review-branch-card {
    background: #ffffff !important;
    border: 1px solid #f1f5f9 !important;
    border-radius: 10px !important;
    padding: 12px 14px !important;
  }

  .branch-header {
    font-size: 12px !important;
    font-weight: 700 !important;
    color: #2563eb !important;
  }

  .branch-item {
    display: flex !important;
    align-items: center !important;
    justify-content: space-between !important;
    padding: 6px 0 !important;
    border-bottom: 1px solid #f8fafc !important;
  }
  .branch-item:last-child {
    border-bottom: none !important;
  }

  .branch-item-name {
    font-size: 12px !important;
    font-weight: 600 !important;
    color: #1e293b !important;
    display: inline-flex !important;
    align-items: center !important;
    gap: 6px !important;
  }

  .branch-type-badge {
    font-size: 10px !important;
    font-weight: 600 !important;
    padding: 2px 7px !important;
    border-radius: 4px !important;
  }
  .branch-type-dev {
    background: #f3e8ff !important;
    color: #7e22ce !important;
  }
  .branch-type-office {
    background: #dbeafe !important;
    color: #1e40af !important;
  }

  /* Contact cards grid */
  .client-review-contacts-grid {
    display: grid !important;
    grid-template-columns: repeat(3, minmax(0, 1fr)) !important;
    gap: 12px !important;
  }

  .review-contact-mini-card {
    border: 1px solid #e2e8f0 !important;
    border-radius: 10px !important;
    padding: 12px 14px !important;
    background: #ffffff !important;
    display: flex !important;
    flex-direction: column !important;
    gap: 6px !important;
  }

  .contact-mini-avatar {
    width: 28px !important;
    height: 28px !important;
    border-radius: 50% !important;
    font-weight: 700 !important;
    font-size: 11px !important;
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    flex-shrink: 0 !important;
  }

  .contact-mini-name {
    font-size: 12.5px !important;
    font-weight: 700 !important;
    color: #0f172a !important;
  }

  .contact-primary-badge {
    background: #fef3c7 !important;
    color: #92400e !important;
    font-size: 9.5px !important;
    font-weight: 600 !important;
    padding: 1px 6px !important;
    border-radius: 4px !important;
  }

  .contact-mini-desig {
    font-size: 11px !important;
    color: #64748b !important;
    font-weight: 500 !important;
    margin-bottom: 2px !important;
  }

  .contact-mini-meta-line {
    font-size: 11px !important;
    color: #475569 !important;
    display: flex !important;
    align-items: center !important;
    gap: 6px !important;
  }
  .contact-mini-meta-line svg {
    color: #94a3b8 !important;
    flex-shrink: 0 !important;
  }

  /* Company Profiles flex */
  .client-review-profiles-wrap {
    display: flex !important;
    flex-wrap: wrap !important;
    gap: 12px 20px !important;
  }

  .review-profile-link-item {
    display: inline-flex !important;
    align-items: center !important;
    gap: 8px !important;
    text-decoration: none !important;
    color: #1e293b !important;
    font-size: 12px !important;
    font-weight: 500 !important;
    transition: all 0.15s ease !important;
  }
  .review-profile-link-item:hover {
    color: #2563eb !important;
  }
  .review-profile-link-item svg {
    flex-shrink: 0 !important;
  }

  /* Right Sidebar Confirmation Card */
  .client-confirmation-card {
    background: #ffffff !important;
    border: 1px solid #e2e8f0 !important;
    border-radius: 12px !important;
    padding: 18px 20px !important;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02) !important;
  }

  .client-ready-icon-circle {
    width: 36px !important;
    height: 36px !important;
    border-radius: 50% !important;
    background: #dcfce7 !important;
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    flex-shrink: 0 !important;
  }

  .client-ready-title {
    font-size: 14.5px !important;
    font-weight: 700 !important;
    color: #0f172a !important;
    margin: 0 0 2px 0 !important;
  }

  .client-ready-subtitle {
    font-size: 11.5px !important;
    color: #64748b !important;
    margin: 0 !important;
    line-height: 1.4 !important;
  }

  .client-completion-box {
    background: #f8fafc !important;
    border-radius: 8px !important;
    padding: 10px 14px !important;
  }

  .completion-label {
    font-size: 11.5px !important;
    font-weight: 600 !important;
    color: #1e293b !important;
  }

  .completion-value {
    font-size: 12px !important;
    font-weight: 700 !important;
    color: #15803d !important;
  }

  .completion-progress-track {
    width: 100% !important;
    height: 6px !important;
    background: #e2e8f0 !important;
    border-radius: 9999px !important;
    overflow: hidden !important;
  }

  .completion-progress-bar {
    height: 100% !important;
    background: #10b981 !important;
    border-radius: 9999px !important;
    transition: width 0.3s ease !important;
  }

  .client-checklist-group {
    display: flex !important;
    flex-direction: column !important;
    gap: 8px !important;
  }

  .checklist-item {
    display: flex !important;
    align-items: center !important;
    justify-content: space-between !important;
    font-size: 12px !important;
  }

  .checklist-icon {
    width: 18px !important;
    height: 18px !important;
    border-radius: 50% !important;
    background: #dcfce7 !important;
    color: #15803d !important;
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    margin-right: 8px !important;
    flex-shrink: 0 !important;
  }

  .checklist-text {
    flex: 1 !important;
    color: #1e293b !important;
    font-weight: 500 !important;
  }

  .checklist-status {
    color: #64748b !important;
    font-size: 11px !important;
    font-weight: 500 !important;
  }

  /* Before you create card */
  .client-before-create-card {
    background: #fffdf0 !important;
    border: 1px solid #fef08a !important;
    border-radius: 12px !important;
    padding: 16px 18px !important;
  }

  .client-tip-icon-box {
    width: 26px !important;
    height: 26px !important;
    border-radius: 6px !important;
    background: #fef9c3 !important;
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    flex-shrink: 0 !important;
  }

  .before-create-title {
    font-size: 13px !important;
    font-weight: 700 !important;
    color: #854d0e !important;
    margin: 0 !important;
  }

  .before-create-list {
    display: flex !important;
    flex-direction: column !important;
    gap: 6px !important;
  }

  .before-item {
    display: flex !important;
    align-items: flex-start !important;
    gap: 8px !important;
    font-size: 11.5px !important;
    color: #78350f !important;
    line-height: 1.4 !important;
  }

  .before-item-check {
    width: 14px !important;
    height: 14px !important;
    border-radius: 50% !important;
    background: #fef08a !important;
    color: #854d0e !important;
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    flex-shrink: 0 !important;
    margin-top: 1px !important;
  }

  /* What happens next card */
  .client-next-steps-card {
    background: #ffffff !important;
    border: 1px solid #e2e8f0 !important;
    border-radius: 12px !important;
    padding: 16px 18px !important;
  }

  .client-next-icon-box {
    width: 26px !important;
    height: 26px !important;
    border-radius: 6px !important;
    background: #eff6ff !important;
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    flex-shrink: 0 !important;
  }

  .next-steps-title {
    font-size: 13px !important;
    font-weight: 700 !important;
    color: #1e293b !important;
    margin: 0 !important;
  }

  .next-steps-list {
    display: flex !important;
    flex-direction: column !important;
    gap: 8px !important;
  }

  .next-step-item {
    display: flex !important;
    align-items: flex-start !important;
    gap: 8px !important;
    font-size: 11.5px !important;
    color: #475569 !important;
    line-height: 1.4 !important;
  }

  .step-num {
    width: 18px !important;
    height: 18px !important;
    border-radius: 50% !important;
    background: #eff6ff !important;
    color: #2563eb !important;
    font-size: 10.5px !important;
    font-weight: 700 !important;
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    flex-shrink: 0 !important;
    margin-top: 1px !important;
  }

  /* Sidebar Action Buttons */
  .client-sidebar-primary-btn {
    background: linear-gradient(180deg, #ffd428 0%, #f5c400 100%) !important;
    border: none !important;
    color: #10143b !important;
    font-size: 13.5px !important;
    font-weight: 700 !important;
    height: 44px !important;
    border-radius: 8px !important;
    width: 100% !important;
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    gap: 8px !important;
    box-shadow: 0 1px 3px rgba(245, 196, 0, 0.35) !important;
    cursor: pointer !important;
    transition: all 0.15s ease !important;
  }
  .client-sidebar-primary-btn:hover {
    background: linear-gradient(180deg, #fbc02d 0%, #f59e0b 100%) !important;
    transform: translateY(-1px) !important;
  }
  .client-sidebar-primary-btn:active {
    transform: translateY(0) !important;
  }

  .client-sidebar-back-btn {
    background: #ffffff !important;
    border: 1px solid #cbd5e1 !important;
    color: #334155 !important;
    font-size: 12.5px !important;
    font-weight: 600 !important;
    height: 40px !important;
    border-radius: 8px !important;
    width: 100% !important;
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    gap: 6px !important;
    cursor: pointer !important;
    transition: all 0.15s ease !important;
  }
  .client-sidebar-back-btn:hover {
    background: #f8fafc !important;
    border-color: #94a3b8 !important;
    color: #0f172a !important;
  }

  /* --------------------------------------------------------------------------
     15.6" Laptop Viewport Responsiveness (1366x768 & 1920x1080)
     -------------------------------------------------------------------------- */
  @media (max-height: 850px) {
    .client-review-main-column {
      gap: 12px !important;
    }
    .client-review-section-card {
      margin-bottom: 0 !important;
    }
    .client-review-section-card .card-header {
      padding-top: 8px !important;
      padding-bottom: 8px !important;
    }
    .client-review-section-card .card-body {
      padding-top: 8px !important;
      padding-bottom: 12px !important;
    }
    .client-confirmation-card,
    .client-before-create-card,
    .client-next-steps-card {
      padding: 12px 14px !important;
      margin-top: 10px !important;
    }
    .client-sidebar-primary-btn {
      height: 38px !important;
      font-size: 12.5px !important;
    }
    .client-sidebar-back-btn {
      height: 34px !important;
      font-size: 12px !important;
    }
    .client-review-avatar-box {
      width: 46px !important;
      height: 46px !important;
      font-size: 16px !important;
    }
    .client-review-contacts-grid {
      gap: 8px !important;
    }
    .review-contact-mini-card {
      padding: 10px 12px !important;
    }
  }

  @media (max-width: 1100px) {
    .client-review-layout {
      grid-template-columns: 1fr !important;
    }
    .client-review-contacts-grid {
      grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
    }
  }

  @media (max-width: 768px) {
    .client-review-addresses-split {
      grid-template-columns: 1fr !important;
    }
    .client-review-contacts-grid {
      grid-template-columns: 1fr !important;
    }
  }
</style>
<!--end::Step 6: Review and Submit-->
