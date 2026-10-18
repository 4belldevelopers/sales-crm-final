<!--begin::Review and Submit (God-Level Design Aligned with Master Design System)-->
<div class="pb-5 client-review-step" data-wizard-type="step-content">
  <!-- 2x2 Grid Layout exactly matching reference specifications -->
  <div class="client-review-grid-2x2" style="display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 20px; align-items: start;">
    
    <!-- ==========================================
         CARD 1: Client Information (Top Left)
         ========================================== -->
    <div class="card client-review-card" style="background:#ffffff; border:1.5px solid #F6DF83; border-radius:12px; box-shadow:0 1px 3px rgba(0,0,0,0.02); overflow:hidden;">
      <div class="card-header border-0 py-4 px-6 d-flex align-items-center justify-content-between" style="background:#fffdf0 !important; border-bottom:1px solid #fef3c7 !important;">
        <div class="d-flex align-items-center">
          <div style="width:38px; height:38px; border-radius:10px; background:#fef9c3; display:inline-flex; align-items:center; justify-content:center; color:#ca8a04; margin-right:12px; flex-shrink:0;">
            <i class="la la-building" style="font-size:20px; color:#ca8a04;"></i>
          </div>
          <div>
            <h4 style="font-size:15px; font-weight:700; color:#101A46; margin:0 0 2px 0;">Client Information</h4>
            <p style="font-size:12px; color:#5C6F9F; margin:0;">Basic details about the client.</p>
          </div>
        </div>
        <button type="button" class="btn client-review-edit" onclick="window.goToWizardStep ? window.goToWizardStep(1) : false;" style="border:1px solid #93c5fd; color:#2563eb; background:#ffffff; font-weight:600; font-size:12.5px; border-radius:6px; padding:5px 12px; display:inline-flex; align-items:center; gap:5px; cursor:pointer;">
          <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/></svg> Edit
        </button>
      </div>

      <div class="card-body p-6">
        <div class="d-flex align-items-start gap-4">
          <!-- Logo / Initials box -->
          <div id="client_review_logo_box" style="width:58px; height:58px; border-radius:8px; background:#ede9fe; color:#4338ca; display:inline-flex; align-items:center; justify-content:center; font-weight:700; font-size:20px; flex-shrink:0; background-size:cover; background-position:center; background-repeat:no-repeat;">
            <span id="client_review_initials">AC</span>
          </div>

          <!-- Two columns of metadata with vertical divider -->
          <div class="row flex-grow-1 align-items-start m-0">
            <div class="col-6" style="padding-left:10px; padding-right:18px; border-right:1px solid #f1f5f9;">
              <div style="font-size:11px; color:#5C6F9F; font-weight:500; margin-bottom:2px;">Client Name</div>
              <div class="span_name" style="font-size:15px; font-weight:700; color:#101A46; margin-bottom:14px;">—</div>

              <div style="font-size:11px; color:#5C6F9F; font-weight:500; margin-bottom:2px;">Website</div>
              <div style="margin-bottom:14px;">
                <a href="javascript:void(0);" target="_blank" class="span_website" style="font-size:13px; font-weight:600; color:#2563eb; text-decoration:none; display:inline-flex; align-items:center; gap:5px; word-break:break-all;">
                  <span>—</span>
                  <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
                </a>
              </div>

              <div style="font-size:11px; color:#5C6F9F; font-weight:500; margin-bottom:2px;">Company Type</div>
              <div class="span_company_type" style="font-size:13.5px; font-weight:700; color:#101A46;">—</div>
            </div>

            <div class="col-6" style="padding-left:18px;">
              <div style="font-size:11px; color:#5C6F9F; font-weight:500; margin-bottom:2px;">Industry</div>
              <div style="font-size:13.5px; font-weight:700; color:#101A46; margin-bottom:14px; display:flex; align-items:center; gap:8px;">
                <i class="la la-building" style="font-size:17px; color:#64748b;"></i>
                <span class="span_industry">—</span>
              </div>

              <div style="font-size:11px; color:#5C6F9F; font-weight:500; margin-bottom:2px;">Company Size</div>
              <div style="font-size:13.5px; font-weight:700; color:#101A46; margin-bottom:14px; display:flex; align-items:center; gap:8px;">
                <i class="la la-users" style="font-size:17px; color:#64748b;"></i>
                <span class="span_company_sizes">—</span>
              </div>

              <div style="font-size:11px; color:#5C6F9F; font-weight:500; margin-bottom:2px;">Founded Year</div>
              <div style="font-size:13.5px; font-weight:700; color:#101A46; display:flex; align-items:center; gap:8px;">
                <i class="la la-calendar" style="font-size:17px; color:#64748b;"></i>
                <span class="span_founded">—</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- ==========================================
         CARD 2: Client Description (Top Right)
         ========================================== -->
    <div class="card client-review-card" style="background:#ffffff; border:1.5px solid #F6DF83; border-radius:12px; box-shadow:0 1px 3px rgba(0,0,0,0.02); overflow:hidden;">
      <div class="card-header border-0 py-4 px-6 d-flex align-items-center justify-content-between" style="background:#fffdf0 !important; border-bottom:1px solid #fef3c7 !important;">
        <div class="d-flex align-items-center">
          <div style="width:38px; height:38px; border-radius:10px; background:#fef9c3; display:inline-flex; align-items:center; justify-content:center; color:#ca8a04; margin-right:12px; flex-shrink:0;">
            <i class="la la-file-text-o" style="font-size:20px; color:#ca8a04;"></i>
          </div>
          <div>
            <h4 style="font-size:15px; font-weight:700; color:#101A46; margin:0 0 2px 0;">Client Description</h4>
            <p style="font-size:12px; color:#5C6F9F; margin:0;">About the client's business, mission and background.</p>
          </div>
        </div>
        <button type="button" class="btn client-review-edit" onclick="window.goToWizardStep ? window.goToWizardStep(2) : false;" style="border:1px solid #93c5fd; color:#2563eb; background:#ffffff; font-weight:600; font-size:12.5px; border-radius:6px; padding:5px 12px; display:inline-flex; align-items:center; gap:5px; cursor:pointer;">
          <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/></svg> Edit
        </button>
      </div>

      <div class="card-body p-6">
        <div class="span_kt_docs_tinymce_hidden" style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:8px; padding:18px 20px; min-height:160px; color:#334155; font-size:13px; line-height:1.6; overflow-y:auto; max-height:240px;">
          <span class="text-muted">No description provided.</span>
        </div>
      </div>
    </div>

    <!-- ==========================================
         CARD 3: Address Details (Bottom Left)
         ========================================== -->
    <div class="card client-review-card" style="background:#ffffff; border:1.5px solid #F6DF83; border-radius:12px; box-shadow:0 1px 3px rgba(0,0,0,0.02); overflow:hidden;">
      <div class="card-header border-0 py-4 px-6 d-flex align-items-center justify-content-between" style="background:#fffdf0 !important; border-bottom:1px solid #fef3c7 !important;">
        <div class="d-flex align-items-center">
          <div style="width:38px; height:38px; border-radius:10px; background:#fef9c3; display:inline-flex; align-items:center; justify-content:center; color:#ca8a04; margin-right:12px; flex-shrink:0;">
            <i class="la la-map-marker" style="font-size:20px; color:#ca8a04;"></i>
          </div>
          <div>
            <h4 style="font-size:15px; font-weight:700; color:#101A46; margin:0 0 2px 0;">Address Details</h4>
            <p style="font-size:12px; color:#5C6F9F; margin:0;">Client's primary and additional addresses.</p>
          </div>
        </div>
        <button type="button" class="btn client-review-edit" onclick="window.goToWizardStep ? window.goToWizardStep(3) : false;" style="border:1px solid #93c5fd; color:#2563eb; background:#ffffff; font-weight:600; font-size:12.5px; border-radius:6px; padding:5px 12px; display:inline-flex; align-items:center; gap:5px; cursor:pointer;">
          <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/></svg> Edit
        </button>
      </div>

      <div class="card-body p-6">
        <!-- Headquarters (Primary) Physical Address -->
        <div class="client-review-hq-box" style="background:#fffdf0; border:1px solid #fef08a; border-radius:8px; padding:16px; margin-bottom:18px; display:flex; align-items:flex-start; gap:14px;">
          <div style="width:40px; height:40px; border-radius:8px; background:#fef9c3; display:inline-flex; align-items:center; justify-content:center; color:#ca8a04; flex-shrink:0;">
            <i class="la la-building" style="font-size:22px; color:#ca8a04;"></i>
          </div>
          <div>
            <div style="font-size:14px; font-weight:700; color:#101A46; margin-bottom:3px;">Headquarters (Primary)</div>
            <div id="client_review_hq_city" style="font-size:13px; font-weight:600; color:#334155; margin-bottom:3px;">—</div>
            <div id="client_review_hq_address" style="font-size:13px; color:#5C6F9F; line-height:1.45;">—</div>
          </div>
        </div>

        <!-- Additional Addresses -->
        <div id="client_review_additional_header" style="font-size:13px; font-weight:600; color:#101A46; margin-bottom:10px;">Additional Addresses (0)</div>
        <div id="client_review_additional_list">
          <div id="client_review_additional_empty" style="border:1.5px dashed #cbd5e1; border-radius:8px; padding:28px 20px; text-align:center; background:#ffffff;">
            <i class="la la-map-marker" style="font-size:26px; color:#94a3b8; display:block; margin-bottom:8px;"></i>
            <div style="font-size:13px; color:#64748b; margin-bottom:14px; font-weight:500;">No additional addresses added yet.</div>
            <button type="button" class="btn btn-sm" onclick="window.goToWizardStep ? window.goToWizardStep(3) : false;" style="background:#ffffff; border:1px solid #93c5fd; color:#2563eb; font-weight:600; font-size:12.5px; border-radius:6px; padding:6px 16px; display:inline-flex; align-items:center; gap:5px; cursor:pointer;">
              <i class="la la-plus"></i> <span>Add Another Address</span>
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- ==========================================
         CARD 4: Contact Persons (Bottom Right)
         ========================================== -->
    <div class="card client-review-card" style="background:#ffffff; border:1.5px solid #F6DF83; border-radius:12px; box-shadow:0 1px 3px rgba(0,0,0,0.02); overflow:hidden;">
      <div class="card-header border-0 py-4 px-6 d-flex align-items-center justify-content-between" style="background:#fffdf0 !important; border-bottom:1px solid #fef3c7 !important;">
        <div class="d-flex align-items-center">
          <div style="width:38px; height:38px; border-radius:10px; background:#fef9c3; display:inline-flex; align-items:center; justify-content:center; color:#ca8a04; margin-right:12px; flex-shrink:0;">
            <i class="la la-users" style="font-size:20px; color:#ca8a04;"></i>
          </div>
          <div>
            <h4 style="font-size:15px; font-weight:700; color:#101A46; margin:0 0 2px 0;">Contact Persons</h4>
            <p style="font-size:12px; color:#5C6F9F; margin:0;">Key contacts for this client organization.</p>
          </div>
        </div>
        <button type="button" class="btn btn-warning btn-sm" onclick="window.openContactDrawer ? window.openContactDrawer(null) : false;" style="background:#FFD21F !important; border:none !important; color:#101A46 !important; font-weight:700; font-size:12.5px; border-radius:6px; padding:7px 15px; display:inline-flex; align-items:center; gap:5px; box-shadow:0 1px 3px rgba(255,210,31,0.3); cursor:pointer;">
          <i class="la la-plus" style="font-weight:bold; font-size:13px;"></i> <span>Add Contact Person</span>
        </button>
      </div>

      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table align-middle mb-0" style="width:100%; border-collapse:collapse;">
            <thead>
              <tr style="background:#f8fafc; border-bottom:1px solid #e2e8f0; color:#64748b; font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:0.5px;">
                <th style="padding:12px 16px;">CONTACT PERSON</th>
                <th style="padding:12px 12px;">DESIGNATION</th>
                <th style="padding:12px 12px;">EMAIL</th>
                <th style="padding:12px 12px;">PHONE NUMBER</th>
                <th style="padding:12px 12px;">TYPE</th>
                <th style="padding:12px 16px; text-align:right;">ACTIONS</th>
              </tr>
            </thead>
            <tbody id="client_review_contact_tbody">
              <tr>
                <td colspan="6" class="text-center py-6 text-muted" style="font-size:12.5px;">No contact persons added yet.</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

  </div>
</div>
<!--end::Review and Submit-->
