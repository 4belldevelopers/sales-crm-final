<!--begin::Review and Submit (God-Level Design Aligned with Master Design System)-->
<div class="pb-5 client-review-step" data-wizard-type="step-content">
  <!-- 2x2 Grid Layout exactly matching reference specifications -->
  <div class="client-review-grid-2x2 mb-4" style="display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 20px; align-items: start;">
    
    <!-- ==========================================
         CARD 1: Client Information & Tech (Top Left)
         ========================================== -->
    <div class="card client-review-card" style="background:#ffffff; border:1px solid #e2e8f0; border-radius:12px; box-shadow:0 1px 3px rgba(0,0,0,0.02); overflow:hidden;">
      <div class="card-header border-0 py-4 px-6 d-flex align-items-center justify-content-between" style="background:#fffdf0 !important; border-bottom:1px solid #fef3c7 !important;">
        <div class="d-flex align-items-center">
          <div style="width:38px; height:38px; border-radius:10px; background:#fef9c3; display:inline-flex; align-items:center; justify-content:center; color:#ca8a04; margin-right:12px; flex-shrink:0;">
            <i class="la la-building" style="font-size:20px; color:#ca8a04;"></i>
          </div>
          <div>
            <h4 style="font-size:15px; font-weight:700; color:#101A46; margin:0 0 2px 0;">Client Information</h4>
            <p style="font-size:12px; color:#5C6F9F; margin:0;">Verified identity, ATS platform, and firmographics.</p>
          </div>
        </div>
        <button type="button" class="btn client-review-edit" onclick="window.goToWizardStep ? window.goToWizardStep(1) : false;" style="border:1px solid #93c5fd; color:#2563eb; background:#ffffff; font-weight:600; font-size:12.5px; border-radius:6px; padding:5px 12px; display:inline-flex; align-items:center; gap:5px; cursor:pointer;">
          <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/></svg> Edit
        </button>
      </div>

      <div class="card-body p-6">
        <div class="d-flex align-items-start gap-4">
          <!-- Logo / Initials box (Updated live with 512px rasterized PNG) -->
          <div id="client_review_logo_box" style="width:64px; height:64px; border-radius:10px; background:#ede9fe; color:#4338ca; display:inline-flex; align-items:center; justify-content:center; font-weight:700; font-size:22px; flex-shrink:0; background-size:contain; background-position:center; background-repeat:no-repeat; border:1px solid #cbd5e1;">
            <span id="client_review_initials">CL</span>
          </div>

          <!-- Two columns of metadata with vertical divider -->
          <div class="row flex-grow-1 align-items-start m-0">
            <div class="col-6" style="padding-left:10px; padding-right:18px; border-right:1px solid #f1f5f9;">
              <div style="font-size:11px; color:#5C6F9F; font-weight:500; margin-bottom:2px;">Brand Name</div>
              <div class="span_name" style="font-size:15px; font-weight:700; color:#101A46; margin-bottom:10px;">—</div>

              <div style="font-size:11px; color:#5C6F9F; font-weight:500; margin-bottom:2px;">Legal Registered Entity</div>
              <div id="client_review_legal_name" style="font-size:12.5px; font-weight:600; color:#334155; margin-bottom:10px;">—</div>

              <div style="font-size:11px; color:#5C6F9F; font-weight:500; margin-bottom:2px;">Website & Domain</div>
              <div style="margin-bottom:10px;">
                <a href="javascript:void(0);" target="_blank" class="span_website" style="font-size:12.5px; font-weight:600; color:#2563eb; text-decoration:none; display:inline-flex; align-items:center; gap:5px; word-break:break-all;">
                  <span>—</span>
                  <i class="la la-external-link" style="font-size:12px;"></i>
                </a>
              </div>

              <div style="font-size:11px; color:#5C6F9F; font-weight:500; margin-bottom:2px;">Company Type</div>
              <div class="span_company_type" style="font-size:13px; font-weight:700; color:#101A46;">—</div>
            </div>

            <div class="col-6" style="padding-left:18px;">
              <div style="font-size:11px; color:#5C6F9F; font-weight:500; margin-bottom:2px;">Detected ATS Platform</div>
              <div style="margin-bottom:10px;">
                <span class="badge" id="client_review_ats_badge" style="background:#f0fdf4; border:1px solid #bbf7d0; color:#166534; font-weight:700; font-size:11.5px; padding:4px 8px; border-radius:6px;">
                  <i class="la la-bolt text-success"></i> <span id="client_review_ats_name">Not Detected</span>
                </span>
              </div>

              <div style="font-size:11px; color:#5C6F9F; font-weight:500; margin-bottom:2px;">Industry</div>
              <div style="font-size:13px; font-weight:700; color:#101A46; margin-bottom:10px; display:flex; align-items:center; gap:6px;">
                <i class="la la-building" style="font-size:15px; color:#64748b;"></i>
                <span class="span_industry">—</span>
              </div>

              <div style="font-size:11px; color:#5C6F9F; font-weight:500; margin-bottom:2px;">Company Size</div>
              <div style="font-size:13px; font-weight:700; color:#101A46; margin-bottom:10px; display:flex; align-items:center; gap:6px;">
                <i class="la la-users" style="font-size:15px; color:#64748b;"></i>
                <span class="span_company_sizes">—</span>
              </div>

              <div style="font-size:11px; color:#5C6F9F; font-weight:500; margin-bottom:2px;">Founded Year</div>
              <div style="font-size:13px; font-weight:700; color:#101A46; display:flex; align-items:center; gap:6px;">
                <i class="la la-calendar" style="font-size:15px; color:#64748b;"></i>
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
    <div class="card client-review-card" style="background:#ffffff; border:1px solid #e2e8f0; border-radius:12px; box-shadow:0 1px 3px rgba(0,0,0,0.02); overflow:hidden;">
      <div class="card-header border-0 py-4 px-6 d-flex align-items-center justify-content-between" style="background:#fffdf0 !important; border-bottom:1px solid #fef3c7 !important;">
        <div class="d-flex align-items-center">
          <div style="width:38px; height:38px; border-radius:10px; background:#fef9c3; display:inline-flex; align-items:center; justify-content:center; color:#ca8a04; margin-right:12px; flex-shrink:0;">
            <i class="la la-file-text-o" style="font-size:20px; color:#ca8a04;"></i>
          </div>
          <div>
            <h4 style="font-size:15px; font-weight:700; color:#101A46; margin:0 0 2px 0;">About & Culture Preferences</h4>
            <p style="font-size:12px; color:#5C6F9F; margin:0;">Mission, workplace model, and notice period appetite.</p>
          </div>
        </div>
        <button type="button" class="btn client-review-edit" onclick="window.goToWizardStep ? window.goToWizardStep(2) : false;" style="border:1px solid #93c5fd; color:#2563eb; background:#ffffff; font-weight:600; font-size:12.5px; border-radius:6px; padding:5px 12px; display:inline-flex; align-items:center; gap:5px; cursor:pointer;">
          <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/></svg> Edit
        </button>
      </div>

      <div class="card-body p-6">
        <div class="span_kt_docs_tinymce_hidden mb-3" style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:8px; padding:14px 16px; min-height:100px; color:#334155; font-size:12.5px; line-height:1.55; overflow-y:auto; max-height:140px;">
          <span class="text-muted">No description provided.</span>
        </div>

        <div style="display:grid; grid-template-columns: 1fr 1fr; gap:12px; background:#f8fafc; border:1px solid #e2e8f0; border-radius:8px; padding:12px;">
          <div>
            <div style="font-size:11px; color:#64748b; font-weight:600;">Work Model:</div>
            <div id="client_review_work_model" style="font-size:12.5px; font-weight:700; color:#1e293b;">Hybrid</div>
          </div>
          <div>
            <div style="font-size:11px; color:#64748b; font-weight:600;">Notice Appetite:</div>
            <div id="client_review_notice_tolerance" style="font-size:12.5px; font-weight:700; color:#1e293b;">Up to 30 Days</div>
          </div>
        </div>
      </div>
    </div>

    <!-- ==========================================
         CARD 3: Address & Timezone (Bottom Left)
         ========================================== -->
    <div class="card client-review-card" style="background:#ffffff; border:1px solid #e2e8f0; border-radius:12px; box-shadow:0 1px 3px rgba(0,0,0,0.02); overflow:hidden;">
      <div class="card-header border-0 py-4 px-6 d-flex align-items-center justify-content-between" style="background:#fffdf0 !important; border-bottom:1px solid #fef3c7 !important;">
        <div class="d-flex align-items-center">
          <div style="width:38px; height:38px; border-radius:10px; background:#fef9c3; display:inline-flex; align-items:center; justify-content:center; color:#ca8a04; margin-right:12px; flex-shrink:0;">
            <i class="la la-map-marker" style="font-size:20px; color:#ca8a04;"></i>
          </div>
          <div>
            <h4 style="font-size:15px; font-weight:700; color:#101A46; margin:0 0 2px 0;">Address & Operating Timezone</h4>
            <p style="font-size:12px; color:#5C6F9F; margin:0;">Headquarters location and core working hours.</p>
          </div>
        </div>
        <button type="button" class="btn client-review-edit" onclick="window.goToWizardStep ? window.goToWizardStep(3) : false;" style="border:1px solid #93c5fd; color:#2563eb; background:#ffffff; font-weight:600; font-size:12.5px; border-radius:6px; padding:5px 12px; display:inline-flex; align-items:center; gap:5px; cursor:pointer;">
          <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/></svg> Edit
        </button>
      </div>

      <div class="card-body p-6">
        <!-- Headquarters Physical Address -->
        <div class="client-review-hq-box" style="background:#fffdf0; border:1px solid #fef08a; border-radius:8px; padding:14px; margin-bottom:14px; display:flex; align-items:flex-start; gap:12px;">
          <div style="width:36px; height:36px; border-radius:8px; background:#fef9c3; display:inline-flex; align-items:center; justify-content:center; color:#ca8a04; flex-shrink:0;">
            <i class="la la-building" style="font-size:20px; color:#ca8a04;"></i>
          </div>
          <div>
            <div style="font-size:13.5px; font-weight:700; color:#101A46; margin-bottom:2px;">Headquarters (Primary)</div>
            <div id="client_review_hq_city" style="font-size:12.5px; font-weight:600; color:#334155; margin-bottom:2px;">—</div>
            <div id="client_review_hq_address" style="font-size:12px; color:#5C6F9F; line-height:1.4;">—</div>
          </div>
        </div>

        <div style="display:flex; align-items:center; gap:10px; background:#f8fafc; border:1px solid #e2e8f0; border-radius:8px; padding:10px 14px;">
          <i class="la la-clock-o text-primary" style="font-size:18px;"></i>
          <div>
            <span style="font-size:10.5px; color:#64748b; font-weight:600; display:block;">Primary Timezone:</span>
            <span id="client_review_timezone" style="font-size:12.5px; font-weight:700; color:#1e293b;">Auto-calculated from HQ</span>
          </div>
        </div>
      </div>
    </div>

    <!-- ==========================================
         CARD 4: Contact Persons (Bottom Right)
         ========================================== -->
    <div class="card client-review-card" style="background:#ffffff; border:1px solid #e2e8f0; border-radius:12px; box-shadow:0 1px 3px rgba(0,0,0,0.02); overflow:hidden;">
      <div class="card-header border-0 py-4 px-6 d-flex align-items-center justify-content-between" style="background:#fffdf0 !important; border-bottom:1px solid #fef3c7 !important;">
        <div class="d-flex align-items-center">
          <div style="width:38px; height:38px; border-radius:10px; background:#fef9c3; display:inline-flex; align-items:center; justify-content:center; color:#ca8a04; margin-right:12px; flex-shrink:0;">
            <i class="la la-users" style="font-size:20px; color:#ca8a04;"></i>
          </div>
          <div>
            <h4 style="font-size:15px; font-weight:700; color:#101A46; margin:0 0 2px 0;">Contact Persons</h4>
            <p style="font-size:12px; color:#5C6F9F; margin:0;">Key hiring and management contacts.</p>
          </div>
        </div>
        <button type="button" class="btn btn-warning btn-sm" onclick="window.goToWizardStep ? window.goToWizardStep(4) : false;" style="background:#ffd428 !important; border:none !important; color:#101A46 !important; font-weight:700; font-size:12.5px; border-radius:6px; padding:5px 12px; display:inline-flex; align-items:center; gap:5px; cursor:pointer;">
          <i class="la la-edit" style="font-weight:bold; font-size:13px;"></i> <span>Edit</span>
        </button>
      </div>

      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table align-middle mb-0" style="width:100%; border-collapse:collapse;">
            <thead>
              <tr style="background:#f8fafc; border-bottom:1px solid #e2e8f0; color:#64748b; font-size:10.5px; font-weight:700; text-transform:uppercase; letter-spacing:0.5px;">
                <th style="padding:10px 14px;">NAME</th>
                <th style="padding:10px 10px;">DESIGNATION</th>
                <th style="padding:10px 10px;">EMAIL</th>
                <th style="padding:10px 10px;">PHONE</th>
              </tr>
            </thead>
            <tbody id="client_review_contact_tbody">
              <tr>
                <td colspan="4" class="text-center py-6 text-muted" style="font-size:12.5px;">No contact persons added yet.</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

  </div>

  <!-- =========================================================================
       CARD 5: Commercial Placement Terms, Legal Compliance & JERO Marketplace
       ========================================================================= -->
  <section class="card client-review-card mb-4" style="background:#ffffff; border:1px solid #e2e8f0; border-radius:12px; box-shadow:0 1px 3px rgba(0,0,0,0.02); overflow:hidden;">
    <div class="card-header border-0 py-4 px-6 d-flex align-items-center justify-content-between" style="background:linear-gradient(135deg, #f8fafc 0%, #eff6ff 100%) !important; border-bottom:1px solid #dbeafe !important;">
      <div class="d-flex align-items-center">
        <div style="width:38px; height:38px; border-radius:10px; background:#dbeafe; display:inline-flex; align-items:center; justify-content:center; color:#1d4ed8; margin-right:12px; flex-shrink:0;">
          <i class="la la-briefcase" style="font-size:20px; color:#1d4ed8;"></i>
        </div>
        <div>
          <h4 style="font-size:15px; font-weight:700; color:#0f172a; margin:0 0 2px 0;">Commercial Terms, Compliance & JERO Marketplace Protocol</h4>
          <p style="font-size:11.5px; color:#64748b; margin:0;">Configure recruitment placement fee, replacement guarantee SLA, and talent co-brokering rules.</p>
        </div>
      </div>
      <span class="badge" style="background:#dbeafe; color:#1e40af; font-size:11px; font-weight:700; padding:6px 12px; border-radius:20px;">
        <i class="la la-shield text-primary"></i> Contract Guard
      </span>
    </div>

    <div class="card-body p-6">
      
      <!-- Row 1: Commercial Placement & SLA Terms -->
      <div class="mb-4">
        <h5 style="font-size:12.5px; font-weight:700; color:#1e293b; text-transform:uppercase; letter-spacing:0.5px; margin-bottom:12px; border-bottom:1px solid #f1f5f9; padding-bottom:6px;">
          1. Placement Fee & Billing Terms
        </h5>
        <div style="display:grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap:16px;">
          <!-- Fee Structure Model -->
          <div>
            <label class="form-label mb-1" style="font-size:12px; font-weight:600; color:#1e293b;">Commission Model</label>
            <select name="commission_model" id="commission_model" class="form-select" style="border:1px solid #cbd5e1; border-radius:8px; height:38px; font-size:12px;">
              <option value="8.33% CTC (1 Month Gross)" <?php echo (@$detail_arr['commission_model'] == '8.33% CTC (1 Month Gross)' || empty(@$detail_arr['commission_model']) ? 'selected' : ''); ?>>8.33% of CTC (1 Mo Gross)</option>
              <option value="10.0% of Annual CTC" <?php echo (@$detail_arr['commission_model'] == '10.0% of Annual CTC' ? 'selected' : ''); ?>>10.0% of Annual CTC</option>
              <option value="12.5% of Annual CTC" <?php echo (@$detail_arr['commission_model'] == '12.5% of Annual CTC' ? 'selected' : ''); ?>>12.5% of Annual CTC</option>
              <option value="15.0% of Annual CTC" <?php echo (@$detail_arr['commission_model'] == '15.0% of Annual CTC' ? 'selected' : ''); ?>>15.0% of Annual CTC</option>
              <option value="20.0% of Annual CTC (Executive)" <?php echo (@$detail_arr['commission_model'] == '20.0% of Annual CTC (Executive)' ? 'selected' : ''); ?>>20.0% of CTC (Executive)</option>
              <option value="Fixed Flat Fee Per Hire" <?php echo (@$detail_arr['commission_model'] == 'Fixed Flat Fee Per Hire' ? 'selected' : ''); ?>>Fixed Flat Fee Per Hire</option>
            </select>
          </div>

          <!-- Replacement Guarantee SLA -->
          <div>
            <label class="form-label mb-1" style="font-size:12px; font-weight:600; color:#1e293b;">Replacement Guarantee SLA</label>
            <select name="replacement_guarantee_days" id="replacement_guarantee_days" class="form-select" style="border:1px solid #cbd5e1; border-radius:8px; height:38px; font-size:12px;">
              <option value="90 Days Free Replacement" <?php echo (@$detail_arr['replacement_guarantee_days'] == '90 Days Free Replacement' || empty(@$detail_arr['replacement_guarantee_days']) ? 'selected' : ''); ?>>90 Days Free Replacement (Standard)</option>
              <option value="60 Days Free Replacement" <?php echo (@$detail_arr['replacement_guarantee_days'] == '60 Days Free Replacement' ? 'selected' : ''); ?>>60 Days Free Replacement</option>
              <option value="30 Days Free Replacement" <?php echo (@$detail_arr['replacement_guarantee_days'] == '30 Days Free Replacement' ? 'selected' : ''); ?>>30 Days Free Replacement</option>
              <option value="180 Days (Executive Search)" <?php echo (@$detail_arr['replacement_guarantee_days'] == '180 Days (Executive Search)' ? 'selected' : ''); ?>>180 Days (Executive Search)</option>
            </select>
          </div>

          <!-- Payment Terms -->
          <div>
            <label class="form-label mb-1" style="font-size:12px; font-weight:600; color:#1e293b;">Payment Terms</label>
            <select name="payment_terms" id="payment_terms" class="form-select" style="border:1px solid #cbd5e1; border-radius:8px; height:38px; font-size:12px;">
              <option value="Net 30 Days" <?php echo (@$detail_arr['payment_terms'] == 'Net 30 Days' || empty(@$detail_arr['payment_terms']) ? 'selected' : ''); ?>>Net 30 Days</option>
              <option value="Net 15 Days" <?php echo (@$detail_arr['payment_terms'] == 'Net 15 Days' ? 'selected' : ''); ?>>Net 15 Days</option>
              <option value="Net 45 Days" <?php echo (@$detail_arr['payment_terms'] == 'Net 45 Days' ? 'selected' : ''); ?>>Net 45 Days</option>
              <option value="Net 60 Days" <?php echo (@$detail_arr['payment_terms'] == 'Net 60 Days' ? 'selected' : ''); ?>>Net 60 Days</option>
              <option value="Immediate Upon Joining" <?php echo (@$detail_arr['payment_terms'] == 'Immediate Upon Joining' ? 'selected' : ''); ?>>Immediate Upon Joining</option>
            </select>
          </div>

          <!-- Payment Trigger Event -->
          <div>
            <label class="form-label mb-1" style="font-size:12px; font-weight:600; color:#1e293b;">Invoice Billing Trigger</label>
            <select name="payment_trigger" id="payment_trigger" class="form-select" style="border:1px solid #cbd5e1; border-radius:8px; height:38px; font-size:12px;">
              <option value="Candidate Date of Joining (DOJ)" <?php echo (@$detail_arr['payment_trigger'] == 'Candidate Date of Joining (DOJ)' || empty(@$detail_arr['payment_trigger']) ? 'selected' : ''); ?>>Candidate Date of Joining (DOJ)</option>
              <option value="Candidate Offer Acceptance" <?php echo (@$detail_arr['payment_trigger'] == 'Candidate Offer Acceptance' ? 'selected' : ''); ?>>Candidate Offer Acceptance</option>
              <option value="30 Days Post-Joining" <?php echo (@$detail_arr['payment_trigger'] == '30 Days Post-Joining' ? 'selected' : ''); ?>>30 Days Post-Joining</option>
            </select>
          </div>
        </div>
      </div>

      <!-- Row 2: Legal Compliance & PO Requirements -->
      <div class="mb-4">
        <h5 style="font-size:12.5px; font-weight:700; color:#1e293b; text-transform:uppercase; letter-spacing:0.5px; margin-bottom:12px; border-bottom:1px solid #f1f5f9; padding-bottom:6px;">
          2. Legal Safeguards, Tax ID & Candidate Protection
        </h5>
        <div style="display:grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap:16px;">
          <!-- Corporate Tax ID -->
          <div>
            <label class="form-label mb-1" style="font-size:12px; font-weight:600; color:#1e293b;">Corporate Tax ID / GSTIN / EIN</label>
            <input type="text" class="form-control" name="tax_id" id="tax_id" value="<?php echo @$detail_arr['tax_id'] ?? ''; ?>" placeholder="e.g. 27AAACW1234F1Z5 or US EIN" style="border:1px solid #cbd5e1; border-radius:8px; height:38px; font-size:12px;">
          </div>

          <!-- Candidate Ownership Window -->
          <div>
            <label class="form-label mb-1" style="font-size:12px; font-weight:600; color:#1e293b;">Candidate Ownership Protection</label>
            <select name="candidate_ownership_months" id="candidate_ownership_months" class="form-select" style="border:1px solid #cbd5e1; border-radius:8px; height:38px; font-size:12px;">
              <option value="12 Months Protection" <?php echo (@$detail_arr['candidate_ownership_months'] == '12 Months Protection' || empty(@$detail_arr['candidate_ownership_months']) ? 'selected' : ''); ?>>12 Months Active Lock-in</option>
              <option value="6 Months Protection" <?php echo (@$detail_arr['candidate_ownership_months'] == '6 Months Protection' ? 'selected' : ''); ?>>6 Months Active Lock-in</option>
              <option value="18 Months (Exclusive)" <?php echo (@$detail_arr['candidate_ownership_months'] == '18 Months (Exclusive)' ? 'selected' : ''); ?>>18 Months (Exclusive)</option>
            </select>
          </div>

          <!-- Purchase Order Mandatory Checkbox -->
          <div>
            <label class="form-label mb-1" style="font-size:12px; font-weight:600; color:#1e293b;">Procurement Policy</label>
            <div class="d-flex align-items-center" style="height:38px; background:#f8fafc; border:1px solid #cbd5e1; border-radius:8px; padding:0 12px; gap:8px;">
              <input class="form-check-input" type="checkbox" name="po_required" id="po_required" value="1" <?php echo (!empty(@$detail_arr['po_required']) ? 'checked' : ''); ?> style="cursor:pointer; width:16px; height:16px;">
              <label for="po_required" class="form-check-label mb-0" style="font-size:11.5px; color:#334155; cursor:pointer; font-weight:500;">
                Purchase Order (PO) required before invoice
              </label>
            </div>
          </div>
        </div>
      </div>

      <!-- Row 3: JERO Talent Marketplace Co-Brokering -->
      <div>
        <h5 style="font-size:12.5px; font-weight:700; color:#1e293b; text-transform:uppercase; letter-spacing:0.5px; margin-bottom:12px; border-bottom:1px solid #f1f5f9; padding-bottom:6px;">
          3. JERO Talent Marketplace & Co-Brokering Permissions
        </h5>
        <div style="display:grid; grid-template-columns: 1.5fr 1fr; gap:16px;">
          <!-- Marketplace Sharing -->
          <div>
            <label class="form-label mb-1" style="font-size:12px; font-weight:600; color:#1e293b;">Marketplace Visibility & Sharing</label>
            <select name="marketplace_sharing" id="marketplace_sharing" class="form-select" style="border:1px solid #cbd5e1; border-radius:8px; height:38px; font-size:12px;">
              <option value="Private & Exclusive" <?php echo (@$detail_arr['marketplace_sharing'] == 'Private & Exclusive' || empty(@$detail_arr['marketplace_sharing']) ? 'selected' : ''); ?>>Private & Exclusive (Internal Recruiters Only)</option>
              <option value="Public Marketplace Co-Brokering" <?php echo (@$detail_arr['marketplace_sharing'] == 'Public Marketplace Co-Brokering' ? 'selected' : ''); ?>>Public Marketplace Co-Brokering (Verified JERO Agencies)</option>
            </select>
            <span style="font-size:10.5px; color:#64748b;">Allow verified partner agencies on JERO to co-source candidates for this client.</span>
          </div>

          <!-- Bounty Split -->
          <div>
            <label class="form-label mb-1" style="font-size:12px; font-weight:600; color:#1e293b;">Default Bounty Split Ratio</label>
            <select name="bounty_split_percentage" id="bounty_split_percentage" class="form-select" style="border:1px solid #cbd5e1; border-radius:8px; height:38px; font-size:12px;">
              <option value="60% Sourcing / 40% Account Owner" <?php echo (@$detail_arr['bounty_split_percentage'] == '60% Sourcing / 40% Account Owner' || empty(@$detail_arr['bounty_split_percentage']) ? 'selected' : ''); ?>>60% Sourcing / 40% Account Owner</option>
              <option value="50% Sourcing / 50% Account Owner" <?php echo (@$detail_arr['bounty_split_percentage'] == '50% Sourcing / 50% Account Owner' ? 'selected' : ''); ?>>50% Sourcing / 50% Account Owner</option>
              <option value="70% Sourcing / 30% Account Owner" <?php echo (@$detail_arr['bounty_split_percentage'] == '70% Sourcing / 30% Account Owner' ? 'selected' : ''); ?>>70% Sourcing / 30% Account Owner</option>
            </select>
            <span style="font-size:10.5px; color:#64748b;">Automated payout split executed via JERO Coin Escrow.</span>
          </div>
        </div>
      </div>

    </div>
  </section>

</div>
<!--end::Review and Submit-->
