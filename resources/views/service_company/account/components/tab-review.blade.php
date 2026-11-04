{{--
  God-Level Company Profile — Step 5: Review & Save
  100% Replicates Add Client Step 6 Review Design System (Rule 20 Design Standard)
  68% Left Summary Cards with Direct Inline Edit Actions
  32% Right Confirmation, Dynamic Completion Status Meter & Checklist
  Optimized for 15.6" Screens (1366x768 & 1920x1080)
--}}
<div class="pb-5 client-review-step profile-review-step" data-wizard-type="step-content">
  <div class="client-review-layout">
    
    <!-- ==============================================================
         LEFT COLUMN (~68% width): Scannable Summary Review Cards
         ============================================================== -->
    <div class="client-review-main-column">

      <!-- CARD 1: Company Profile Banner -->
      <div class="card client-review-section-card client-review-company-card mb-3" style="background:#ffffff; border:1px solid #e2e8f0; border-radius:12px; box-shadow:0 1px 3px rgba(0,0,0,0.03); overflow:hidden;">
        <div class="card-body p-4 d-flex align-items-center justify-content-between flex-wrap gap-3">
          <div class="d-flex align-items-center gap-4 min-w-0 flex-grow-1">
            <!-- Logo / Initials Avatar Box -->
            <div id="profile_review_logo_box" class="client-review-avatar-box" style="width:54px; height:54px; border-radius:10px; background:#f1f5f9; border:1px solid #e2e8f0; display:flex; align-items:center; justify-content:center; flex-shrink:0; overflow:hidden;">
              <span id="profile_review_initials" style="font-size:18px; font-weight:700; color:#1e293b;">CP</span>
            </div>
            <div class="min-w-0 flex-grow-1">
              <div class="d-flex align-items-center flex-wrap gap-2 mb-1">
                <h3 class="client-review-company-name span_company_name text-truncate mb-0" style="font-size:15px; font-weight:700; color:#0f172a;">{{ $account->company_name ?? 'Company Name' }}</h3>
                <span class="badge" id="profile_review_type_badge" style="background:#fef9c3; color:#ca8a04; font-size:10.5px; font-weight:700; padding:2px 8px; border-radius:6px;">{{ ($selectedCompTypeName ?? '') ?: 'Organization' }}</span>
              </div>
              <div class="client-review-company-meta d-flex align-items-center flex-wrap gap-4" style="font-size:11.5px; color:#64748b;">
                <span class="meta-item d-inline-flex align-items-center gap-1">
                  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#64748b" stroke-width="2"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
                  <span class="span_industry">{{ ($selectedIndName ?? '') ?: 'Industry' }}</span>
                </span>
                <span class="meta-item d-inline-flex align-items-center gap-1">
                  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#64748b" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/></svg>
                  <span class="span_company_sizes">{{ ($selectedSizeLabel ?? '') ?: 'Company Size' }}</span>
                </span>
                <span class="meta-item d-inline-flex align-items-center gap-1">
                  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#64748b" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                  <span class="span_primary_location" id="profile_review_meta_location">{{ ($primary_city ?? '') ?: 'Location' }}</span>
                </span>
              </div>
            </div>
          </div>
          <button type="button" class="btn btn-sm client-review-edit-btn" onclick="window.goToWizardStep ? window.goToWizardStep(1) : false;" style="background:#ffffff; border:1px solid #cbd5e1; border-radius:7px; padding:5px 12px; font-size:11.5px; font-weight:600; color:#334155; display:inline-flex; align-items:center; gap:5px; cursor:pointer;">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>
            <span>Edit</span>
          </button>
        </div>
      </div>

      <!-- CARD 2: Company Description -->
      <div class="card client-review-section-card mb-3" style="background:#ffffff; border:1px solid #e2e8f0; border-radius:12px; box-shadow:0 1px 3px rgba(0,0,0,0.03); overflow:hidden;">
        <div class="card-header border-0 py-3 px-4 d-flex align-items-center justify-content-between" style="background:#ffffff; border-bottom:1px solid #f8fafc !important;">
          <div class="d-flex align-items-center gap-3">
            <div class="client-review-icon-box" style="width:32px; height:32px; border-radius:8px; background:#fef9c3; display:inline-flex; align-items:center; justify-content:center; color:#ca8a04;">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#ca8a04" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
            </div>
            <h4 class="client-review-card-title mb-0" style="font-size:13.5px; font-weight:700; color:#0f172a;">Company Description</h4>
          </div>
          <button type="button" class="btn btn-sm client-review-edit-btn" onclick="window.goToWizardStep ? window.goToWizardStep(2) : false;" style="background:#ffffff; border:1px solid #cbd5e1; border-radius:7px; padding:5px 12px; font-size:11.5px; font-weight:600; color:#334155; display:inline-flex; align-items:center; gap:5px; cursor:pointer;">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>
            <span>Edit</span>
          </button>
        </div>
        <div class="card-body px-4 py-3">
          <div class="client-review-description-text span_description" id="profile_review_desc_content" style="font-size:12px; line-height:1.6; color:#334155;">
            {!! $account->description ?? '<span class="text-muted">No description provided yet.</span>' !!}
          </div>
        </div>
      </div>

      <!-- CARD 3: Technologies & Expertise -->
      <div class="card client-review-section-card mb-3" style="background:#ffffff; border:1px solid #e2e8f0; border-radius:12px; box-shadow:0 1px 3px rgba(0,0,0,0.03); overflow:hidden;">
        <div class="card-header border-0 py-3 px-4 d-flex align-items-center justify-content-between" style="background:#ffffff; border-bottom:1px solid #f8fafc !important;">
          <div class="d-flex align-items-center gap-3">
            <div class="client-review-icon-box" style="width:32px; height:32px; border-radius:8px; background:#fef9c3; display:inline-flex; align-items:center; justify-content:center; color:#ca8a04;">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#ca8a04" stroke-width="2"><polyline points="16 18 22 12 16 6"/><polyline points="8 6 2 12 8 18"/></svg>
            </div>
            <h4 class="client-review-card-title mb-0" style="font-size:13.5px; font-weight:700; color:#0f172a;">Technologies & Capabilities</h4>
          </div>
          <button type="button" class="btn btn-sm client-review-edit-btn" onclick="window.goToWizardStep ? window.goToWizardStep(3) : false;" style="background:#ffffff; border:1px solid #cbd5e1; border-radius:7px; padding:5px 12px; font-size:11.5px; font-weight:600; color:#334155; display:inline-flex; align-items:center; gap:5px; cursor:pointer;">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>
            <span>Edit</span>
          </button>
        </div>
        <div class="card-body px-4 py-3">
          <!-- Technologies -->
          <div class="mb-3">
            <div class="fw-bold mb-2" style="font-size:11px; color:#64748b; text-transform:uppercase; letter-spacing:0.5px;">Core Technologies</div>
            <div class="d-flex flex-wrap gap-2" id="profile_review_tech_badges">
              <!-- Populated by populateReviewSummaries() -->
            </div>
          </div>
          <!-- Services -->
          <div>
            <div class="fw-bold mb-2" style="font-size:11px; color:#64748b; text-transform:uppercase; letter-spacing:0.5px;">Service Offerings</div>
            <div class="d-flex flex-wrap gap-2" id="profile_review_service_badges">
              <!-- Populated by populateReviewSummaries() -->
            </div>
          </div>
        </div>
      </div>

      <!-- CARD 4: Addresses & Locations -->
      <div class="card client-review-section-card mb-3" style="background:#ffffff; border:1px solid #e2e8f0; border-radius:12px; box-shadow:0 1px 3px rgba(0,0,0,0.03); overflow:hidden;">
        <div class="card-header border-0 py-3 px-4 d-flex align-items-center justify-content-between" style="background:#ffffff; border-bottom:1px solid #f8fafc !important;">
          <div class="d-flex align-items-center gap-3">
            <div class="client-review-icon-box" style="width:32px; height:32px; border-radius:8px; background:#fef9c3; display:inline-flex; align-items:center; justify-content:center; color:#ca8a04;">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#ca8a04" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
            </div>
            <h4 class="client-review-card-title mb-0" style="font-size:13.5px; font-weight:700; color:#0f172a;">Locations & Facilities</h4>
          </div>
          <button type="button" class="btn btn-sm client-review-edit-btn" onclick="window.goToWizardStep ? window.goToWizardStep(4) : false;" style="background:#ffffff; border:1px solid #cbd5e1; border-radius:7px; padding:5px 12px; font-size:11.5px; font-weight:600; color:#334155; display:inline-flex; align-items:center; gap:5px; cursor:pointer;">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>
            <span>Edit</span>
          </button>
        </div>
        <div class="card-body px-4 py-3">
          <div class="client-review-addresses-split d-flex flex-column gap-3">
            <!-- Headquarters Box -->
            <div class="client-review-hq-card" id="profile_review_hq_card" style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:10px; padding:12px 14px;">
              <div class="d-flex align-items-center gap-2 mb-2 flex-wrap">
                <span class="badge" style="background:#fef9c3; color:#ca8a04; font-size:10px; font-weight:700; padding:2px 8px; border-radius:6px;">HQ</span>
                <span class="hq-city-title text-truncate fw-bold" id="profile_review_hq_city" style="font-size:12.5px; color:#0f172a;">{{ ($primary_city ?? '') ?: 'City' }}</span>
                <span class="badge" style="background:#eef5ff; color:#1d4ed8; font-size:10px; font-weight:600; padding:2px 8px; border-radius:6px;">Headquarters</span>
              </div>
              <div class="hq-address-line d-flex align-items-start gap-2" style="font-size:11.5px; color:#475569;">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#64748b" stroke-width="2" class="mt-1 flex-shrink-0"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                <span id="profile_review_hq_address" class="text-break">{{ ($primary_address ?? '') ?: 'Address' }}</span>
              </div>
            </div>

            <!-- Additional Locations Box -->
            <div class="client-review-branch-card" id="profile_review_branch_card" style="background:#ffffff; border:1px solid #e2e8f0; border-radius:10px; padding:12px 14px;">
              <div class="branch-header mb-2 fw-bold" id="profile_review_additional_header" style="font-size:12px; color:#0f172a;">
                Additional Branch Offices (<span id="profile_review_branch_count">{{ count($additional_addresses ?? []) }}</span>)
              </div>
              <div class="branch-list" id="profile_review_additional_list" style="font-size:11.5px; color:#475569;">
                <!-- Populated dynamically -->
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- CARD 5: Social Links & Official Profiles -->
      <div class="card client-review-section-card mb-3" style="background:#ffffff; border:1px solid #e2e8f0; border-radius:12px; box-shadow:0 1px 3px rgba(0,0,0,0.03); overflow:hidden;">
        <div class="card-header border-0 py-3 px-4 d-flex align-items-center justify-content-between" style="background:#ffffff; border-bottom:1px solid #f8fafc !important;">
          <div class="d-flex align-items-center gap-3">
            <div class="client-review-icon-box" style="width:32px; height:32px; border-radius:8px; background:#fffbeb; display:inline-flex; align-items:center; justify-content:center; color:#d97706;">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#d97706" stroke-width="2"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/></svg>
            </div>
            <h4 class="client-review-card-title mb-0" style="font-size:13.5px; font-weight:700; color:#0f172a;">Official Social Profiles</h4>
          </div>
          <button type="button" class="btn btn-sm client-review-edit-btn" onclick="window.goToWizardStep ? window.goToWizardStep(5) : false;" style="background:#ffffff; border:1px solid #cbd5e1; border-radius:7px; padding:5px 12px; font-size:11.5px; font-weight:600; color:#334155; display:inline-flex; align-items:center; gap:5px; cursor:pointer;">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>
            <span>Edit</span>
          </button>
        </div>
        <div class="card-body px-4 py-3">
          <div class="d-flex flex-wrap gap-2" id="profile_review_social_badges">
            <!-- Populated dynamically by populateReviewSummaries() -->
          </div>
        </div>
      </div>

    </div>

    <!-- ==============================================================
         RIGHT SIDEBAR (~32% width): Readiness Meter & Checklist
         ============================================================== -->
    <aside class="client-review-side-column">
      
      <!-- Card: Company Profile Ready -->
      <div class="card client-confirmation-card" style="background:#ffffff; border:1px solid #e2e8f0; border-radius:12px; box-shadow:0 1px 3px rgba(0,0,0,0.03); padding:20px !important;">
        <div class="d-flex align-items-center gap-3 mb-3">
          <div class="client-ready-icon-circle" style="width:40px; height:40px; border-radius:50%; background:#dcfce7; display:flex; align-items:center; justify-content:center; color:#15803d; flex-shrink:0;">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#15803d" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
              <polyline points="20 6 9 17 4 12"/>
            </svg>
          </div>
          <div>
            <h4 class="client-ready-title mb-0" style="font-size:14px; font-weight:700; color:#0f172a;">Company Profile Ready</h4>
            <p class="client-ready-subtitle mb-0" style="font-size:11px; color:#64748b;">Review and finalize your organization details.</p>
          </div>
        </div>

        <!-- Completion Status Meter -->
        <div class="client-completion-box mb-4" style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:8px; padding:12px 14px;">
          <div class="d-flex align-items-center justify-content-between mb-2">
            <span class="completion-label" style="font-size:11.5px; font-weight:600; color:#334155;">Profile Completion</span>
            <span class="completion-value" id="profile_review_completion_percent" style="font-size:13px; font-weight:700; color:#10b981;">100%</span>
          </div>
          <div class="completion-progress-track" style="height:7px; background:#e2e8f0; border-radius:10px; overflow:hidden;">
            <div class="completion-progress-bar" id="profile_review_completion_bar" style="height:100%; width:100%; background:linear-gradient(90deg, #10b981, #059669); transition:width 0.3s ease;"></div>
          </div>
        </div>

        <!-- Checklist -->
        <div class="client-checklist-group d-flex flex-column gap-2 mb-4">
          <div class="checklist-item d-flex align-items-center justify-content-between" id="chk_company">
            <div class="d-flex align-items-center gap-2">
              <span class="checklist-icon" style="width:18px; height:18px; border-radius:50%; background:#dcfce7; display:flex; align-items:center; justify-content:center; color:#15803d;"><svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg></span>
              <span class="checklist-text" style="font-size:11.5px; font-weight:500; color:#334155;">Company Information</span>
            </div>
            <span class="checklist-status" style="font-size:10.5px; font-weight:600; color:#10b981;">Completed</span>
          </div>
          <div class="checklist-item d-flex align-items-center justify-content-between" id="chk_desc">
            <div class="d-flex align-items-center gap-2">
              <span class="checklist-icon" style="width:18px; height:18px; border-radius:50%; background:#dcfce7; display:flex; align-items:center; justify-content:center; color:#15803d;"><svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg></span>
              <span class="checklist-text" style="font-size:11.5px; font-weight:500; color:#334155;">About Organization</span>
            </div>
            <span class="checklist-status" style="font-size:10.5px; font-weight:600; color:#10b981;">Completed</span>
          </div>
          <div class="checklist-item d-flex align-items-center justify-content-between" id="chk_tech">
            <div class="d-flex align-items-center gap-2">
              <span class="checklist-icon" style="width:18px; height:18px; border-radius:50%; background:#dcfce7; display:flex; align-items:center; justify-content:center; color:#15803d;"><svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg></span>
              <span class="checklist-text" style="font-size:11.5px; font-weight:500; color:#334155;">Technologies &amp; Services</span>
            </div>
            <span class="checklist-status" style="font-size:10.5px; font-weight:600; color:#10b981;">Completed</span>
          </div>
          <div class="checklist-item d-flex align-items-center justify-content-between" id="chk_hq">
            <div class="d-flex align-items-center gap-2">
              <span class="checklist-icon" style="width:18px; height:18px; border-radius:50%; background:#dcfce7; display:flex; align-items:center; justify-content:center; color:#15803d;"><svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg></span>
              <span class="checklist-text" style="font-size:11.5px; font-weight:500; color:#334155;">Headquarters &amp; Locations</span>
            </div>
            <span class="checklist-status" style="font-size:10.5px; font-weight:600; color:#10b981;">Completed</span>
          </div>
          <div class="checklist-item d-flex align-items-center justify-content-between" id="chk_social">
            <div class="d-flex align-items-center gap-2">
              <span class="checklist-icon" style="width:18px; height:18px; border-radius:50%; background:#dcfce7; display:flex; align-items:center; justify-content:center; color:#15803d;"><svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg></span>
              <span class="checklist-text" style="font-size:11.5px; font-weight:500; color:#334155;">Social Profiles</span>
            </div>
            <span class="checklist-status" style="font-size:10.5px; font-weight:600; color:#10b981;">Completed</span>
          </div>
        </div>

        <!-- Primary Submit CTA in Review Column -->
        <button type="button" class="btn w-100 frmupdate profile-sidebar-submit-btn" id="review_submit_btn" style="background:#ffd428; border:1px solid #d4a017; color:#10143b; font-weight:700; font-size:12.5px; border-radius:8px; padding:11px 16px; display:inline-flex; align-items:center; justify-content:center; gap:6px; box-shadow:0 1px 3px rgba(0,0,0,0.06); cursor:pointer;">
          <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
          <span>{{ ($page_mode ?? '') === 'ONBOARDING' ? 'Complete Profile & Continue' : 'Save Profile Changes' }}</span>
        </button>
      </div>

    </aside>

  </div>
</div>
