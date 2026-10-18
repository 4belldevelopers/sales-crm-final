<!--begin::Client Information (God-Level Design Aligned with media_1792065526584.png)-->
<div class="pb-5 client-info-step" data-wizard-type="step-content" data-wizard-state="current">
  <div class="client-step-layout client-information-layout">
    <div class="client-main-column client-information-main">

      <!-- ==========================================
           SECTION 1: Company Identity
           ========================================== -->
      <section class="card client-section-card client-identity-card company-identity-card mb-4" style="background:#ffffff; border:1.5px solid #F6DF83; border-radius:12px; box-shadow:0 1px 3px rgba(0,0,0,0.02); overflow:hidden;" aria-labelledby="client-identity-title">
        <div class="card-header border-0 py-4 px-6 d-flex align-items-center" style="background:#ffffff; border-bottom:1px solid #f8fafc !important;">
          <div style="width:38px; height:38px; border-radius:10px; background:#fef9c3; display:inline-flex; align-items:center; justify-content:center; color:#ca8a04; margin-right:12px; flex-shrink:0;">
            <i class="la la-user" style="font-size:20px; color:#ca8a04;"></i>
          </div>
          <div>
            <h3 class="client-section-heading" id="client-identity-title" style="font-size:15px; font-weight:700; color:#101A46; margin:0 0 2px 0;">Company Identity</h3>
            <p class="client-section-copy" style="font-size:12px; color:#5C6F9F; margin:0;">Upload logo and enter basic details for this client.</p>
          </div>
        </div>

        <div class="card-body p-6">
          <div class="client-branding-grid" style="display:grid; grid-template-columns:180px 140px minmax(0, 1fr); gap:16px; align-items:stretch;">
            
            <!-- Logo Upload Dashed Area -->
            <label class="client-logo-upload" for="client_logo_input" style="border:1.5px dashed #93c5fd; border-radius:10px; padding:16px 12px; text-align:center; background:#f8fafc; cursor:pointer; display:flex; flex-direction:column; align-items:center; justify-content:center; min-height:148px; height:100%; box-sizing:border-box; transition:border-color 0.15s ease;">
              <div style="width:44px; height:44px; border-radius:50%; background:#eff6ff; display:inline-flex; align-items:center; justify-content:center; position:relative; margin-bottom:8px;">
                <i class="la la-building" style="font-size:22px; color:#3b82f6;"></i>
                <span style="position:absolute; bottom:-1px; right:-1px; width:15px; height:15px; border-radius:50%; background:#2563eb; color:#fff; display:inline-flex; align-items:center; justify-content:center; font-size:10px; font-weight:bold; line-height:1;">+</span>
              </div>
              <span class="client-upload-title" style="font-size:12.5px; font-weight:600; color:#1769FF; margin-bottom:2px;">Upload Company Logo</span>
              <span class="client-upload-copy" style="font-size:10.5px; color:#64748b; line-height:1.3;">PNG or JPG (Max. 2MB)</span>
              <span class="client-upload-copy" style="font-size:10px; color:#94a3b8; margin-top:2px;">Recommended: square image</span>
            </label>

            <!-- Logo Preview -->
            <div class="client-logo-preview" style="border:1px solid #e2e8f0; border-radius:10px; padding:12px; text-align:center; background:#ffffff; min-height:148px; height:100%; box-sizing:border-box; display:flex; flex-direction:column; align-items:center; justify-content:center;">
              <span class="client-preview-label" style="font-size:11px; font-weight:600; color:#101A46; margin-bottom:8px;">Preview</span>
              <div class="image-input image-input-outline" id="client_logo_preview" style="width:68px; height:68px; border-radius:8px; border:1px solid #e2e8f0; overflow:hidden; margin-bottom:8px; display:inline-flex; align-items:center; justify-content:center; background:#64748b;">
                <div class="image-input-wrapper" style="width:100%; height:100%; background-size:cover; background-position:center; background-image: url(<?php echo @$background_image; ?>);">
                  <?php if(empty(@$background_image)): ?>
                    <div style="width:100%; height:100%; display:flex; align-items:center; justify-content:center; background:#64748b; color:#ffffff;">
                      <i class="la la-building" style="font-size:30px; color:#ffffff;"></i>
                    </div>
                  <?php endif; ?>
                </div>
                <label data-kt-image-input-action="change" title="Change company logo" style="display:none;">
                  <input type="file" name="logo" id="client_logo_input" accept=".png, .jpg, .jpeg" onchange="loadFile(event)"/>
                  <input type="hidden" name="avatar_remove" id="client_avatar_remove" value="0" />
                </label>
              </div>
              <button type="button" class="btn btn-sm client-preview-remove" id="client_preview_remove_btn" data-kt-image-input-action="cancel" style="border:1px solid #fecdd3; background:#fff1f2; color:#e11d48; border-radius:6px; font-size:11px; font-weight:600; padding:3px 10px; display:inline-flex; align-items:center; gap:4px;">
                <i class="la la-trash" style="font-size:12px;"></i> Remove
              </button>
            </div>

            <!-- Client Name & Website Fields (Matches Screenshot: Client Name first, Company Website second) -->
            <div class="client-brand-fields d-flex flex-column gap-3 justify-content-center">

              <!-- Client Name Field -->
              <div class="client-field fv-plugins-icon-container">
                <label class="form-label required" for="client_id" style="font-size:12.5px; font-weight:600; color:#101A46; margin-bottom:4px;">Client Name <span class="text-danger">*</span></label>
                <div class="input-group input-group-solid" style="border:1px solid #cbd5e1; border-radius:8px; overflow:hidden; background:#ffffff;">
                  <span class="input-group-text" style="background:#fff; border:none; color:#94a3b8; padding-left:12px; padding-right:8px;"><i class="la la-user" style="font-size:16px;"></i></span>
                  <input type="text" class="form-control form-control-solid" name="name" id="client_id" value="<?php echo @$name; ?>" placeholder="Enter client / company name" style="border:none; height:42px; font-size:13px; color:#101A46;">
                </div>
              </div>

              <!-- Company Website Field with Fetch Button -->
              <div class="client-field fv-plugins-icon-container">
                <label class="form-label" for="website" style="font-size:12.5px; font-weight:600; color:#101A46; margin-bottom:4px;">Company Website</label>
                <div class="company-website-input-group input-group" style="display:flex; align-items:stretch; gap:8px; border:none !important; background:transparent !important; overflow:visible !important; box-shadow:none !important;">
                  <div class="input-group-solid website-input-wrapper" style="display:flex; align-items:center; flex:1 1 auto; min-width:0; border:1px solid #cbd5e1; border-radius:8px; overflow:hidden; background:#ffffff;">
                    <span class="input-group-text" style="background:#fff; border:none; color:#94a3b8; padding-left:12px; padding-right:8px; flex:0 0 auto; width:auto;"><i class="la la-globe" style="font-size:16px;"></i></span>
                    <input type="text" class="form-control form-control-solid" name="website" id="website" value="<?php echo @$website; ?>" placeholder="https://www.example.com" style="border:none; height:42px; font-size:13px; color:#101A46; flex:1 1 auto; width:100%; min-width:0; padding-left:4px;">
                  </div>
                  <button type="button" class="btn client-auto-fetch fetch-company-details-btn" id="client_auto_fetch" style="background:#fffdf0 !important; border:1.5px solid #F6DF83 !important; color:#78350F !important; font-weight:700 !important; font-size:12px !important; padding:0 16px !important; height:42px !important; border-radius:8px !important; white-space:nowrap !important; display:inline-flex; align-items:center; justify-content:center; gap:6px; flex:0 0 auto !important; width:auto !important; min-width:max-content !important; max-width:none !important; overflow:visible !important; cursor:pointer;">
                    <i class="la la-magic" style="font-size:14px; color:#b45309;"></i> Fetch Company Details
                  </button>
                </div>
                <div class="client-info-banner mt-2 mb-0" style="background:#eff7ff; border:1px solid #dbeafe; border-radius:6px; padding:7px 12px; display:flex; align-items:center; gap:8px; color:#1e40af; font-size:11.5px;">
                  <i class="la la-info-circle" style="color:#3b82f6; font-size:14px;"></i>
                  <span>We will fetch and suggest company information from the website.</span>
                </div>
              </div>

            </div>

          </div>
        </div>
      </section>

      <!-- ==========================================
           SECTION 2: Organization Profile
           ========================================== -->
      <section class="card client-section-card client-profile-card organization-profile-card" style="background:#ffffff; border:1.5px solid #F6DF83; border-radius:12px; box-shadow:0 1px 3px rgba(0,0,0,0.02); overflow:hidden;" aria-labelledby="client-details-title">
        <div class="card-header border-0 py-4 px-6 d-flex align-items-center" style="background:#ffffff; border-bottom:1px solid #f8fafc !important;">
          <div style="width:38px; height:38px; border-radius:10px; background:#fef9c3; display:inline-flex; align-items:center; justify-content:center; color:#ca8a04; margin-right:12px; flex-shrink:0;">
            <i class="la la-building" style="font-size:20px; color:#ca8a04;"></i>
          </div>
          <div>
            <h3 class="client-section-heading" id="client-details-title" style="font-size:15px; font-weight:700; color:#101A46; margin:0 0 2px 0;">Organization Profile</h3>
            <p class="client-section-copy" style="font-size:12px; color:#5C6F9F; margin:0;">Tell us a little about the organization.</p>
          </div>
        </div>

        <div class="card-body p-6">
          <div class="client-details-grid" style="display:grid; grid-template-columns:repeat(2, minmax(0, 1fr)); gap:16px;">
            
            <!-- Industry Field (God-Level Combobox) -->
            <div class="client-field fv-plugins-icon-container">
              <label class="form-label required" for="industry_display" style="font-size:12.5px; font-weight:600; color:#0f172a; margin-bottom:4px;">Industry</label>
              <!-- Native select kept synchronized for form POST & validation -->
              <select name="industry" id="industry" style="position:absolute; width:1px; height:1px; opacity:0; pointer-events:none;" tabindex="-1" aria-hidden="true">
                <option value="">Select industry</option>
                <?php foreach($department as $key => $val){
                  $sel = $val->ind_id == $industry ? 'selected="selected"' : '';
                  echo '<option value="'.@$val->ind_id.'" '.@$sel.'>'.ucwords(@$val->ind_name).'</option>';
                } ?>
              </select>

              <?php
                $selectedIndName = '';
                if (!empty($industry) && !empty($department)) {
                    foreach ($department as $val) {
                        if ($val->ind_id == $industry) {
                            $selectedIndName = ucwords($val->ind_name);
                            break;
                        }
                    }
                }
              ?>

              <!-- God-Level Enterprise Industry Combobox Component -->
              <div class="jero-ind-select-wrap" id="jero_ind_select_wrap">
                <div class="jero-ind-input-box <?php echo $selectedIndName ? 'has-value' : ''; ?>" id="jero_ind_input_box" style="height:42px; border-radius:8px; border:1px solid #cbd5e1;">
                  <span class="jero-ind-search-icon">
                    <i class="la la-building" style="font-size:16px; color:#94a3b8;"></i>
                  </span>
                  <input type="text"
                         class="jero-ind-input"
                         id="industry_display"
                         placeholder="Select industry"
                         autocomplete="off"
                         spellcheck="false"
                         role="combobox"
                         aria-expanded="false"
                         aria-haspopup="listbox"
                         aria-autocomplete="list"
                         value="<?php echo htmlspecialchars($selectedIndName); ?>"
                         style="font-size:13px; color:#1e293b;">
                  <kbd class="jero-ind-kbd" id="jero_ind_kbd" title="Press / to search" style="<?php echo $selectedIndName ? 'display:none;' : ''; ?>">/</kbd>
                  <button type="button" class="jero-ind-clear-btn" id="jero_ind_clear_btn" aria-label="Clear industry" title="Clear selection" style="<?php echo $selectedIndName ? 'display:flex;' : 'display:none;'; ?>">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M18 6 6 18M6 6l12 12"/></svg>
                  </button>
                  <div class="jero-ind-chevron" id="jero_ind_chevron" title="Toggle industry list" role="button" aria-label="Toggle industry list" tabindex="-1">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m6 9 6 6 6-6"/></svg>
                  </div>
                </div>

                <!-- Decision-Centric Hierarchical Dropdown Menu -->
                <div class="jero-ind-dropdown" id="jero_ind_dropdown" style="display:none;" role="listbox" aria-label="Client Industries">
                  <!-- Sector Filter Tabs -->
                  <div class="jero-ind-tabs" id="jero_ind_tabs">
                    <button type="button" class="jero-ind-tab-btn active" data-sector="all">All <span class="tab-count" id="jero_ind_count_all">0</span></button>
                  </div>

                  <!-- Recently Used Bar (1-Click Quick Picks) -->
                  <div class="jero-ind-recent-bar" id="jero_ind_recent_bar" style="display:none;"></div>

                  <!-- Scrollable Options List Container -->
                  <div class="jero-ind-list-container" id="jero_ind_list_container"></div>

                  <!-- Sticky Footer Bar -->
                  <div class="jero-ind-footer">
                    <div class="jero-ind-sync-status" id="jero_ind_sync_status">
                      <span class="jero-ind-sync-dot"></span>
                      <span>Live Synced</span>
                    </div>
                    <span class="jero-ind-footer-hint">↑↓ Navigate • Enter Select • Esc Close</span>
                  </div>
                </div>
              </div>
            </div>

            <!-- Company Size Field -->
            <div class="client-field fv-plugins-icon-container">
              <label class="form-label required" for="company_sizes" style="font-size:12.5px; font-weight:600; color:#0f172a; margin-bottom:4px;">Company Size </label>
              <div class="input-group input-group-solid" style="border:1px solid #cbd5e1; border-radius:8px; overflow:hidden;">
                <span class="input-group-text" style="background:#fff; border:none; color:#94a3b8; padding-left:12px; padding-right:8px;"><i class="la la-users" style="font-size:16px;"></i></span>
                <select name="company_sizes" class="form-select form-select-solid" id="company_sizes" style="border:none; height:42px; font-size:13px; color:#1e293b;">
                  <option value="">Select company size</option>
                  <option value="1-10"         <?php echo (@$company_sizes == '1-10'         ? 'selected' : ''); ?>>1–10</option>
                  <option value="11-50"        <?php echo (@$company_sizes == '11-50'        ? 'selected' : ''); ?>>11–50</option>
                  <option value="51-200"       <?php echo (@$company_sizes == '51-200'       ? 'selected' : ''); ?>>51–200</option>
                  <option value="201-500"      <?php echo (@$company_sizes == '201-500'      ? 'selected' : ''); ?>>201–500</option>
                  <option value="501-1000"     <?php echo (@$company_sizes == '501-1000'     ? 'selected' : ''); ?>>501–1,000</option>
                  <option value="1001-5000"    <?php echo (@$company_sizes == '1001-5000'    ? 'selected' : ''); ?>>1,001–5,000</option>
                  <option value="5001-10000"   <?php echo (@$company_sizes == '5001-10000'   ? 'selected' : ''); ?>>5,001–10,000</option>
                  <option value="10000-999999" <?php echo (@$company_sizes == '10000-999999' ? 'selected' : ''); ?>>10,000+</option>
                </select>
              </div>
            </div>

            <!-- Company Type Field -->
            <div class="client-field fv-plugins-icon-container">
              <label class="form-label required" for="company_type" style="font-size:12.5px; font-weight:600; color:#0f172a; margin-bottom:4px;">Company Type</label>
              <div class="input-group input-group-solid" style="border:1px solid #cbd5e1; border-radius:8px; overflow:hidden;">
                <span class="input-group-text" style="background:#fff; border:none; color:#94a3b8; padding-left:12px; padding-right:8px;"><i class="la la-city" style="font-size:16px;"></i></span>
                <select class="form-select form-select-solid" name="company_type" id="company_type" style="border:none; height:42px; font-size:13px; color:#1e293b;">
                  <option value="">Select company type</option>
                  <?php foreach($companyType as $key => $val){
                    $sel = $key == $company_type ? 'selected="selected"' : '';
                    echo '<option value="'.@$key.'" '.@$sel.'>'.ucwords(@$val).'</option>';
                  } ?>
                </select>
              </div>
            </div>

            <!-- Founded Year Field -->
            <div class="client-field fv-plugins-icon-container">
              <label class="form-label required" for="founded" style="font-size:12.5px; font-weight:600; color:#0f172a; margin-bottom:4px;">Founded Year </label>
              <div class="input-group input-group-solid" style="border:1px solid #cbd5e1; border-radius:8px; overflow:hidden;">
                <span class="input-group-text" style="background:#fff; border:none; color:#94a3b8; padding-left:12px; padding-right:8px;"><i class="la la-calendar" style="font-size:16px;"></i></span>
                <input type="number" class="form-control form-control-solid" name="founded" id="founded" value="<?php echo @$founded; ?>" placeholder="Select year" style="border:none; height:42px; font-size:13px; color:#1e293b;">
              </div>
            </div>

          </div>

          <div class="client-tip mt-4" style="background:#eff6ff; border:1px solid #dbeafe; border-radius:6px; padding:10px 14px; display:flex; align-items:center; gap:8px; color:#1e40af; font-size:12px;">
            <i class="la la-info-circle" style="color:#3b82f6; font-size:16px;"></i>
            <span><strong>Tip:</strong> You can update these details anytime from the client profile.</span>
          </div>
        </div>
      </section>

    </div>

    <!-- ==========================================
         RIGHT SIDEBAR: AI Assist & Quick Tips
         ========================================== -->
    <aside class="client-side-column client-information-sidebar" aria-labelledby="client-assistant-title">
      <!-- AI Company Assist Card -->
      <div class="client-helper-card client-ai-card ai-company-assist-card mb-4" style="background:#f5f3ff; border:1px solid #ede9fe; border-radius:12px; padding:18px;">
        <div class="d-flex align-items-center justify-content-between mb-2">
          <div class="d-flex align-items-center gap-2">
            <div style="width:30px; height:30px; border-radius:8px; background:#ede9fe; display:inline-flex; align-items:center; justify-content:center; color:#7040f5;">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#7040f5" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                <path d="m12 3-1.9 5.8a2 2 0 0 1-1.3 1.3L3 12l5.8 1.9a2 2 0 0 1 1.3 1.3L12 21l1.9-5.8a2 2 0 0 1 1.3-1.3L21 12l-5.8-1.9a2 2 0 0 1-1.3-1.3L12 3z"/>
              </svg>
            </div>
            <h4 style="font-size:14px; font-weight:700; color:#101A46; margin:0;" id="client-assistant-title">AI Company Assist</h4>
          </div>
          <span class="badge" style="background:#ede9fe; color:#7040f5; font-weight:600; font-size:10.5px; padding:3px 8px; border-radius:6px;">Beta</span>
        </div>
        <p style="font-size:11.5px; color:#5C6F9F; line-height:1.45; margin-bottom:14px;">We can auto-fetch and suggest company information from the website.</p>

        <!-- Inner Checklist Box Matching Screenshot -->
        <div class="client-ai-checklist-box" style="background:#ffffff; border:1px solid #ede9fe; border-radius:8px; padding:14px 16px; margin-bottom:14px;">
          <div style="font-size:11.5px; font-weight:700; color:#101A46; margin-bottom:10px;">Information we can find</div>
          <ul style="list-style:none; padding:0; margin:0;">
            <li style="display:flex; align-items:center; gap:8px; font-size:12px; color:#374151; margin-bottom:8px;">
              <i class="la la-check-circle" style="font-size:16px; color:#3b82f6;"></i>
              <span>Industry</span>
            </li>
            <li style="display:flex; align-items:center; gap:8px; font-size:12px; color:#374151; margin-bottom:8px;">
              <i class="la la-check-circle" style="font-size:16px; color:#3b82f6;"></i>
              <span>Company Size</span>
            </li>
            <li style="display:flex; align-items:center; gap:8px; font-size:12px; color:#374151; margin-bottom:8px;">
              <i class="la la-check-circle" style="font-size:16px; color:#3b82f6;"></i>
              <span>Company Type</span>
            </li>
            <li style="display:flex; align-items:center; gap:8px; font-size:12px; color:#374151; margin-bottom:8px;">
              <i class="la la-check-circle" style="font-size:16px; color:#3b82f6;"></i>
              <span>Founded Year</span>
            </li>
            <li style="display:flex; align-items:center; gap:8px; font-size:12px; color:#374151;">
              <i class="la la-check-circle" style="font-size:16px; color:#3b82f6;"></i>
              <span>Company Description</span>
            </li>
          </ul>
        </div>

        <button type="button" class="btn w-100" id="client_fetch_side_btn" onclick="$('#client_auto_fetch').click();" style="background:#ede9fe; color:#7040f5; font-weight:600; font-size:12px; border-radius:8px; padding:10px 14px; border:none; display:flex; align-items:center; justify-content:center; gap:6px;">
          <i class="la la-globe"></i> Enter website and fetch details &rarr;
        </button>
        <div style="font-size:11px; color:#5C6F9F; margin-top:10px; display:flex; align-items:center; gap:6px;">
          <i class="la la-check-circle" style="color:#3b82f6; font-size:13px;"></i> Review the suggested information before applying.
        </div>
      </div>

      <!-- Quick Tips Card (Warm golden surface matching screenshot) -->
      <div class="client-helper-card client-tips-card quick-tips-card" style="background:#FFFCF0; border:1.5px solid #F6DF83; border-radius:12px; padding:18px;">
        <div class="d-flex align-items-center gap-2 mb-3">
          <div style="width:30px; height:30px; border-radius:8px; background:#fef9c3; display:inline-flex; align-items:center; justify-content:center; color:#ca8a04;">
            <i class="la la-lightbulb-o" style="font-size:18px; color:#ca8a04;"></i>
          </div>
          <h4 style="font-size:14px; font-weight:700; color:#101A46; margin:0;">Quick Tips</h4>
        </div>
        <ul style="list-style:none; padding:0; margin:0;">
          <li style="display:flex; align-items:flex-start; gap:8px; font-size:11.5px; color:#475569; margin-bottom:8px;">
            <i class="la la-check-circle" style="font-size:16px; color:#eab308; margin-top:1px;"></i>
            <span>Enter company name and website to fetch details.</span>
          </li>
          <li style="display:flex; align-items:flex-start; gap:8px; font-size:11.5px; color:#475569; margin-bottom:8px;">
            <i class="la la-check-circle" style="font-size:16px; color:#eab308; margin-top:1px;"></i>
            <span>Verify and edit the suggested information.</span>
          </li>
          <li style="display:flex; align-items:flex-start; gap:8px; font-size:11.5px; color:#475569; margin-bottom:8px;">
            <i class="la la-check-circle" style="font-size:16px; color:#eab308; margin-top:1px;"></i>
            <span>You can update all details later from the client profile.</span>
          </li>
          <li style="display:flex; align-items:flex-start; gap:8px; font-size:11.5px; color:#475569;">
            <i class="la la-check-circle" style="font-size:16px; color:#eab308; margin-top:1px;"></i>
            <span>Complete all 5 sections to create the client.</span>
          </li>
        </ul>
      </div>
    </aside>

  </div>
</div>
<!--end::Client Information-->
