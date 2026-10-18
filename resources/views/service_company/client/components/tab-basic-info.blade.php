<!--begin::Client Information (God-Level Design Aligned with Metronic Enterprise Standard)-->
<div class="pb-5 client-info-step" data-wizard-type="step-content" data-wizard-state="current">
  <div class="client-step-layout client-information-layout">
    <div class="client-main-column client-information-main">

      <!-- ==========================================
           SECTION 1: Company Identity & Intelligence
           ========================================== -->
      <section class="card client-section-card client-identity-card company-identity-card mb-4" style="background:#ffffff; border:1px solid #e2e8f0; border-radius:12px; box-shadow:0 1px 3px rgba(0,0,0,0.03); padding:20px 22px !important;" aria-labelledby="client-identity-title">
        
        <!-- Section Title Row: Blue Icon + Heading locked together at left -->
        <div class="d-flex align-items-center justify-content-between mb-4">
          <div class="d-flex align-items-center" style="gap:12px; justify-content:flex-start !important; text-align:left !important;">
            <div style="width:38px; height:38px; border-radius:10px; background:#eff6ff; display:inline-flex; align-items:center; justify-content:center; color:#2563eb; flex-shrink:0;">
              <i class="la la-building" style="font-size:20px; color:#2563eb;"></i>
            </div>
            <div style="text-align:left !important;">
              <h3 class="client-section-heading" id="client-identity-title" style="font-size:15px; font-weight:700; color:#0f172a; margin:0 0 2px 0; text-align:left !important;">Company Identity & Auto-Intelligence</h3>
              <p class="client-section-copy" style="font-size:11.5px; color:#64748b; margin:0; text-align:left !important;">Enter company website to auto-fetch 20+ verified enterprise intelligence attributes in 150ms.</p>
            </div>
          </div>
          <div id="client_intelligence_status_badge" style="display:none;">
            <span class="badge" style="background:#f0fdf4; border:1px solid #bbf7d0; color:#166534; font-weight:600; font-size:11px; padding:6px 12px; border-radius:20px;">
              <i class="la la-check-circle" style="color:#16a34a; margin-right:4px;"></i> Intelligence Verified
            </span>
          </div>
        </div>

        <!-- 1. Hero Magic Fetch Bar (Top Focus of the Page) -->
        <div class="p-3 mb-4" style="background:linear-gradient(135deg, #f8faff 0%, #f1f5f9 100%); border:1.5px solid #cbd5e1; border-radius:10px;">
          <label class="form-label required mb-1" for="website" style="font-size:12.5px; font-weight:700; color:#0f172a;">Company Website / Domain <span class="text-danger">*</span></label>
          <div class="company-website-input-group" style="display:flex; align-items:stretch; gap:10px;">
            <div class="input-group-solid website-input-wrapper" style="display:flex; align-items:center; flex:1 1 auto; min-width:0; border:1px solid #cbd5e1; border-radius:8px; overflow:hidden; background:#ffffff; height:44px; box-shadow:0 1px 2px rgba(0,0,0,0.03);">
              <span class="input-group-text" style="background:#fff; border:none; color:#2563eb; padding-left:14px; padding-right:8px; flex:0 0 auto;">
                <i class="la la-globe" style="font-size:18px;"></i>
              </span>
              <input type="text" class="form-control form-control-solid" name="website" id="website" value="<?php echo @$website; ?>" placeholder="e.g. stripe.com or https://company.com" style="border:none; height:100%; font-size:13.5px; font-weight:500; color:#101A46; background-color:#ffffff; padding-left:4px;">
            </div>
            <button type="button" class="btn client-auto-fetch fetch-company-details-btn" id="client_auto_fetch" style="background:linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%) !important; border:none !important; color:#ffffff !important; font-weight:600 !important; font-size:12.5px !important; padding:0 18px !important; height:44px !important; border-radius:8px !important; white-space:nowrap !important; display:inline-flex; align-items:center; justify-content:center; gap:8px; flex:0 0 auto !important; box-shadow:0 2px 8px rgba(37, 99, 235, 0.28) !important; cursor:pointer;" title="Auto-populate company profile details from website">
              <i class="la la-magic" style="font-size:16px; color:#ffffff;"></i> <span>Auto-Fetch Details</span>
            </button>
          </div>
          <div class="d-flex align-items-center justify-content-between mt-2" style="font-size:11px; color:#64748b;">
            <span id="domain_mx_indicator" style="display:inline-flex; align-items:center; gap:4px;">
              <i class="la la-shield" style="color:#2563eb; font-size:14px;"></i> Client-First 150ms Stream • Zero Server Read Strain • 100% Free
            </span>
            <span id="domain_mx_badge" style="display:none; color:#16a34a; font-weight:600;">
              <i class="la la-check-circle" style="color:#16a34a;"></i> Active Mail Server (MX Verified)
            </span>
          </div>
        </div>

        <!-- 2. Interactive Unified Logo & Brand Palette Studio -->
        <div class="client-identity-logo-row mb-4 p-3" style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:10px; display:flex; align-items:center; gap:16px;">
          <!-- Hidden Native File & Avatar Remove Inputs (Direct DOM Siblings for Standard Form Submit) -->
          <input type="file" name="logo" id="client_logo_input" accept=".png, .jpg, .jpeg, .webp" onchange="loadFile(event)" style="position:absolute; width:1px; height:1px; padding:0; margin:-1px; overflow:hidden; clip:rect(0,0,0,0); border:0; opacity:0; pointer-events:none;" />
          <input type="hidden" name="avatar_remove" id="client_avatar_remove" value="0" />

          <!-- Interactive Logo Avatar / Dropzone (Auto-Injected with 512px Canvas PNG) -->
          <div class="client-logo-preview" style="position:relative; flex-shrink:0;">
            <label for="client_logo_input" class="image-input image-input-outline client-logo-avatar-box" id="client_logo_preview" style="width:84px; height:84px; border-radius:12px; border:1.5px dashed #cbd5e1; overflow:hidden; display:flex; align-items:center; justify-content:center; background:#ffffff; cursor:pointer; transition:all 0.2s ease; margin:0;" title="Click to upload or switch logo">
              <div class="image-input-wrapper" style="width:100%; height:100%; background-size:contain; background-repeat:no-repeat; background-position:center; <?php echo !empty(@$background_image) ? 'background-image: url('.@$background_image.');' : ''; ?> display:flex; align-items:center; justify-content:center;">
                <?php if(empty(@$background_image)): ?>
                  <div style="width:100%; height:100%; display:flex; flex-direction:column; align-items:center; justify-content:center; background:#f8fafc; color:#94a3b8; gap:3px;">
                    <i class="la la-building" style="font-size:26px; color:#94a3b8;"></i>
                    <span style="font-size:9.5px; font-weight:600; color:#64748b; text-transform:uppercase; letter-spacing:0.3px;">Logo</span>
                  </div>
                <?php endif; ?>
              </div>
            </label>
          </div>

          <!-- Action & Info Block beside logo with Brand Theme Accent -->
          <div class="client-logo-info d-flex flex-column justify-content-center flex-grow-1" style="min-width:0;">
            <div class="d-flex align-items-center justify-content-between mb-1">
              <div class="d-flex align-items-center gap-2">
                <h4 style="font-size:13px; font-weight:700; color:#0f172a; margin:0;">Company Logo</h4>
                <span class="badge" id="logo_auto_badge" style="display:none; background:#dbeafe; color:#1d4ed8; font-weight:600; font-size:10px; padding:2px 7px; border-radius:4px;">Auto-Rasterized 512px</span>
              </div>
              
              <!-- Brand Theme Color Swatch -->
              <div class="d-flex align-items-center gap-2" title="Primary Brand Accent Color">
                <span style="font-size:11px; font-weight:600; color:#64748b;">Brand Color:</span>
                <div class="d-flex align-items-center gap-1" style="background:#ffffff; border:1px solid #cbd5e1; border-radius:6px; padding:2px 8px;">
                  <span id="brand_color_preview" style="width:14px; height:14px; border-radius:3px; background:#2563eb; display:inline-block; border:1px solid rgba(0,0,0,0.1);"></span>
                  <input type="text" name="brand_color" id="brand_color" value="<?php echo @$detail_arr['brand_color'] ?? '#2563eb'; ?>" style="border:none; width:65px; font-size:11.5px; font-weight:600; color:#334155; padding:0; outline:none;" placeholder="#2563eb">
                </div>
              </div>
            </div>
            <p style="font-size:11px; color:#64748b; margin:0 0 8px 0; line-height:1.35;">Auto-converted to 512×512 PNG in User RAM for 100% PDF-LIB and Prescreen Report compatibility without CORS errors.</p>
            <div class="d-flex align-items-center gap-2">
              <label for="client_logo_input" class="btn btn-sm client-upload-logo-btn" id="client_upload_logo_btn" style="background:#eff6ff; color:#2563eb; border:1px solid #bfdbfe; font-size:11.5px; font-weight:600; padding:4px 12px; border-radius:6px; display:inline-flex; align-items:center; gap:5px; cursor:pointer; user-select:none; margin:0;">
                <i class="la la-upload" style="font-size:14px; color:#2563eb;"></i>
                <span>Choose Custom Logo</span>
              </label>
              <button type="button" class="btn btn-sm client-preview-remove" id="client_preview_remove_btn" data-kt-image-input-action="cancel" style="border:1px solid #fecdd3; background:#fff1f2; color:#e11d48; border-radius:6px; font-size:11.5px; font-weight:600; padding:4px 10px; display:inline-flex; align-items:center; gap:4px; margin:0; <?php echo empty(@$background_image) ? 'display:none;' : ''; ?>">
                <i class="la la-trash" style="font-size:13px;"></i>
                <span>Remove</span>
              </button>
            </div>
          </div>
        </div>

        <!-- 3. Dual Identity Fields Row: Brand Name & Legal Registered Name -->
        <div class="client-brand-fields mb-3" style="display:grid; grid-template-columns:repeat(2, minmax(0, 1fr)); gap:16px;">
          <!-- Brand / Trading Name -->
          <div class="client-field fv-plugins-icon-container">
            <label class="form-label required" for="client_id" style="font-size:12.5px; font-weight:600; color:#0f172a; margin-bottom:5px;">Brand / Trading Name <span class="text-danger">*</span></label>
            <div class="input-group input-group-solid" style="border:1px solid #cbd5e1; border-radius:8px; overflow:hidden; background:#ffffff; height:42px;">
              <span class="input-group-text" style="background:#fff; border:none; color:#94a3b8; padding-left:12px; padding-right:8px;"><i class="la la-building" style="font-size:16px;"></i></span>
              <input type="text" class="form-control form-control-solid" name="name" id="client_id" value="<?php echo @$name; ?>" placeholder="e.g. Stripe" style="border:none; height:100%; font-size:13px; font-weight:600; color:#101A46; background-color:#ffffff;">
            </div>
          </div>

          <!-- Legal Registered Entity Name (For MSAs, Contracts, Invoices) -->
          <div class="client-field fv-plugins-icon-container">
            <label class="form-label" for="legal_name" style="font-size:12.5px; font-weight:600; color:#0f172a; margin-bottom:5px;">Legal Registered Entity Name</label>
            <div class="input-group input-group-solid" style="border:1px solid #cbd5e1; border-radius:8px; overflow:hidden; background:#ffffff; height:42px;">
              <span class="input-group-text" style="background:#fff; border:none; color:#94a3b8; padding-left:12px; padding-right:8px;"><i class="la la-certificate" style="font-size:16px;"></i></span>
              <input type="text" class="form-control form-control-solid" name="legal_name" id="legal_name" value="<?php echo @$detail_arr['legal_name'] ?? ''; ?>" placeholder="e.g. Stripe, Inc. / Stripe Payments India Pvt Ltd" style="border:none; height:100%; font-size:13px; color:#101A46; background-color:#ffffff;">
            </div>
          </div>
        </div>

        <!-- 4. Hiring Tech Footprint (Detected ATS & Career Portal) -->
        <div class="client-ats-card p-3 mb-2" id="client_ats_container" style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:8px;">
          <div class="d-flex align-items-center justify-content-between mb-2">
            <div class="d-flex align-items-center gap-2">
              <span style="width:24px; height:24px; border-radius:6px; background:#eff6ff; display:inline-flex; align-items:center; justify-content:center; color:#2563eb;">
                <i class="la la-bolt" style="font-size:14px;"></i>
              </span>
              <span style="font-size:12px; font-weight:700; color:#1e293b;">Detected ATS & Career Portal</span>
            </div>
            <span class="badge" id="ats_detected_status_badge" style="background:#f1f5f9; color:#64748b; font-size:10.5px; font-weight:600; padding:3px 8px; border-radius:4px;">Auto-Scanned</span>
          </div>
          
          <div style="display:grid; grid-template-columns: 1fr 1.5fr; gap:12px;">
            <!-- ATS Provider -->
            <div>
              <label class="form-label mb-1" style="font-size:11.5px; font-weight:600; color:#475569;">ATS Provider</label>
              <div class="input-group input-group-solid" style="border:1px solid #cbd5e1; border-radius:6px; overflow:hidden; background:#ffffff; height:36px;">
                <span class="input-group-text" style="background:#fff; border:none; color:#94a3b8; padding-left:10px; padding-right:6px;"><i class="la la-cogs" style="font-size:14px;"></i></span>
                <input type="text" class="form-control form-control-solid" name="ats_provider" id="ats_provider" value="<?php echo @$detail_arr['ats_provider'] ?? ''; ?>" placeholder="e.g. Greenhouse, Workday, Lever" style="border:none; height:100%; font-size:12px; color:#101A46; background-color:#ffffff;">
              </div>
            </div>

            <!-- Career Portal URL -->
            <div>
              <label class="form-label mb-1" style="font-size:11.5px; font-weight:600; color:#475569;">Career Portal / Job Board URL</label>
              <div class="input-group input-group-solid" style="border:1px solid #cbd5e1; border-radius:6px; overflow:hidden; background:#ffffff; height:36px;">
                <span class="input-group-text" style="background:#fff; border:none; color:#94a3b8; padding-left:10px; padding-right:6px;"><i class="la la-external-link" style="font-size:14px;"></i></span>
                <input type="text" class="form-control form-control-solid" name="career_page_url" id="career_page_url" value="<?php echo @$detail_arr['career_page_url'] ?? ''; ?>" placeholder="https://boards.greenhouse.io/company" style="border:none; height:100%; font-size:12px; color:#101A46; background-color:#ffffff;">
              </div>
            </div>
          </div>
        </div>

      </section>

      <!-- ==========================================
           SECTION 2: Organization Profile
           ========================================== -->
      <section class="card client-section-card client-profile-card organization-profile-card" style="background:#ffffff; border:1px solid #e2e8f0; border-radius:12px; box-shadow:0 1px 3px rgba(0,0,0,0.03); padding:20px 22px !important;" aria-labelledby="client-details-title">
        
        <!-- Section Title Row: Violet Icon + Heading locked together at left -->
        <div class="d-flex align-items-center mb-4" style="gap:12px; justify-content:flex-start !important; text-align:left !important;">
          <div style="width:38px; height:38px; border-radius:10px; background:#f5f3ff; display:inline-flex; align-items:center; justify-content:center; color:#7c3aed; flex-shrink:0;">
            <i class="la la-briefcase" style="font-size:18px; color:#7c3aed;"></i>
          </div>
          <div style="text-align:left !important;">
            <h3 class="client-section-heading" id="client-details-title" style="font-size:15px; font-weight:700; color:#0f172a; margin:0 0 2px 0; text-align:left !important;">Organization Profile</h3>
            <p class="client-section-copy" style="font-size:11.5px; color:#64748b; margin:0; text-align:left !important;">Key organizational attributes used for intelligent job matching and ATS routing.</p>
          </div>
        </div>

        <div class="client-details-grid" style="display:grid; grid-template-columns:repeat(2, minmax(0, 1fr)); gap:16px;">
            
            <!-- Industry Field (God-Level Combobox) -->
            <div class="client-field fv-plugins-icon-container">
              <label class="form-label required" for="industry_display" style="font-size:12.5px; font-weight:600; color:#0f172a; margin-bottom:5px;">Industry <span class="text-danger">*</span></label>
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
                <div class="jero-ind-input-box <?php echo $selectedIndName ? 'has-value' : ''; ?>" id="jero_ind_input_box" style="height:42px;">
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
                         style="font-size:13px; color:#1e293b; height:42px;">
                  <kbd class="jero-ind-kbd" id="jero_ind_kbd" title="Press / to search" style="<?php echo $selectedIndName ? 'display:none;' : ''; ?>">/</kbd>
                  <button type="button" class="jero-ind-clear-btn" id="jero_ind_clear_btn" aria-label="Clear industry" title="Clear selection" style="<?php echo $selectedIndName ? 'display:flex;' : 'display:none;'; ?>">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M18 6 6 18M6 6l12 12"/></svg>
                  </button>
                  <div class="jero-ind-chevron" id="jero_ind_chevron" title="Toggle industry list" role="button" aria-label="Toggle industry list" tabindex="-1">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m6 9 6 6 6-6"/></svg>
                  </div>
                </div>

                <!-- Hierarchical Dropdown Menu -->
                <div class="jero-ind-dropdown" id="jero_ind_dropdown" style="display:none;" role="listbox" aria-label="Client Industries">
                  <div class="jero-ind-tabs" id="jero_ind_tabs">
                    <button type="button" class="jero-ind-tab-btn active" data-sector="all">All <span class="tab-count" id="jero_ind_count_all">0</span></button>
                  </div>
                  <div class="jero-ind-recent-bar" id="jero_ind_recent_bar" style="display:none;"></div>
                  <div class="jero-ind-list-container" id="jero_ind_list_container"></div>
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
              <label class="form-label required" for="company_sizes" style="font-size:12.5px; font-weight:600; color:#0f172a; margin-bottom:5px;">Company Size <span class="text-danger">*</span></label>
              <div class="input-group input-group-solid" style="border:1px solid #cbd5e1; border-radius:8px; overflow:hidden; background:#ffffff; height:42px;">
                <span class="input-group-text" style="background:#fff; border:none; color:#94a3b8; padding-left:12px; padding-right:8px;"><i class="la la-users" style="font-size:16px;"></i></span>
                <select name="company_sizes" class="form-select form-select-solid" id="company_sizes" style="border:none; height:100%; font-size:13px; color:#1e293b; background-color:#ffffff;">
                  <option value="">Select company size</option>
                  <option value="1-10"         <?php echo (@$company_sizes == '1-10'         ? 'selected' : ''); ?>>1–10 employees</option>
                  <option value="11-50"        <?php echo (@$company_sizes == '11-50'        ? 'selected' : ''); ?>>11–50 employees</option>
                  <option value="51-200"       <?php echo (@$company_sizes == '51-200'       ? 'selected' : ''); ?>>51–200 employees</option>
                  <option value="201-500"      <?php echo (@$company_sizes == '201-500'      ? 'selected' : ''); ?>>201–500 employees</option>
                  <option value="501-1000"     <?php echo (@$company_sizes == '501-1000'     ? 'selected' : ''); ?>>501–1,000 employees</option>
                  <option value="1001-5000"    <?php echo (@$company_sizes == '1001-5000'    ? 'selected' : ''); ?>>1,001–5,000 employees</option>
                  <option value="501-10000"    <?php echo (@$company_sizes == '5001-10000'   ? 'selected' : ''); ?>>5,001–10,000 employees</option>
                  <option value="10000-999999" <?php echo (@$company_sizes == '10000-999999' ? 'selected' : ''); ?>>10,000+ employees</option>
                </select>
              </div>
            </div>

            <!-- Company Type & Stock Ticker Field -->
            <div class="client-field fv-plugins-icon-container">
              <label class="form-label required" for="company_type" style="font-size:12.5px; font-weight:600; color:#0f172a; margin-bottom:5px;">Company Type <span class="text-danger">*</span></label>
              <div style="display:flex; gap:8px;">
                <div class="input-group input-group-solid" style="border:1px solid #cbd5e1; border-radius:8px; overflow:hidden; background:#ffffff; height:42px; flex:1;">
                  <span class="input-group-text" style="background:#fff; border:none; color:#94a3b8; padding-left:12px; padding-right:8px;"><i class="la la-city" style="font-size:16px;"></i></span>
                  <select class="form-select form-select-solid" name="company_type" id="company_type" style="border:none; height:100%; font-size:13px; color:#1e293b; background-color:#ffffff;">
                    <option value="">Select company type</option>
                    <?php foreach($companyType as $key => $val){
                      $sel = $key == $company_type ? 'selected="selected"' : '';
                      echo '<option value="'.@$key.'" '.@$sel.'>'.ucwords(@$val).'</option>';
                    } ?>
                  </select>
                </div>
                <div class="input-group input-group-solid" style="border:1px solid #cbd5e1; border-radius:8px; overflow:hidden; background:#ffffff; height:42px; width:130px;" title="Stock Ticker (e.g. NASDAQ: AAPL)">
                  <span class="input-group-text" style="background:#fff; border:none; color:#94a3b8; padding:0 6px;"><i class="la la-line-chart" style="font-size:14px;"></i></span>
                  <input type="text" class="form-control form-control-solid" name="stock_ticker" id="stock_ticker" value="<?php echo @$detail_arr['stock_ticker'] ?? ''; ?>" placeholder="Ticker" style="border:none; height:100%; font-size:11.5px; color:#1e293b; background-color:#ffffff; padding:0 6px;">
                </div>
              </div>
            </div>

            <!-- Founded Year Field -->
            <div class="client-field fv-plugins-icon-container">
              <label class="form-label required" for="founded" style="font-size:12.5px; font-weight:600; color:#0f172a; margin-bottom:5px;">Founded Year <span class="text-danger">*</span></label>
              <div class="input-group input-group-solid" style="border:1px solid #cbd5e1; border-radius:8px; overflow:hidden; background:#ffffff; height:42px;">
                <span class="input-group-text" style="background:#fff; border:none; color:#94a3b8; padding-left:12px; padding-right:8px;"><i class="la la-calendar" style="font-size:16px;"></i></span>
                <input type="number" class="form-control form-control-solid" name="founded" id="founded" value="<?php echo @$founded; ?>" placeholder="e.g. 2015" min="1800" max="2099" style="border:none; height:100%; font-size:13px; color:#1e293b; background-color:#ffffff;">
              </div>
            </div>

          </div>

          <div class="client-tip mt-4" style="background:#f0fdf4; border:1px solid #bbf7d0; border-radius:8px; padding:10px 14px; display:flex; align-items:center; gap:8px; color:#166534; font-size:11.5px;">
            <i class="la la-check-circle" style="color:#16a34a; font-size:15px; flex-shrink:0;"></i>
            <span><strong>Pro Tip:</strong> Industry, size, and ATS platform are synchronized to JERO's Elasticsearch index for instant cross-organization candidate routing.</span>
          </div>
      </section>

    </div>

    <!-- ==========================================
         RIGHT SIDEBAR: AI Assist & Quick Tips
         ========================================== -->
    <aside class="client-side-column client-information-sidebar" aria-labelledby="client-assistant-title">
      <!-- AI Company Assist Card -->
      <div class="client-helper-card client-ai-card ai-company-assist-card mb-4" style="background:linear-gradient(180deg, #faf5ff 0%, #ffffff 100%); border:1px solid #ede9fe; border-radius:12px; padding:18px; box-shadow:0 1px 3px rgba(124, 58, 237, 0.04);">
        <div class="d-flex align-items-center justify-content-between mb-2">
          <div class="d-flex align-items-center gap-2">
            <div style="width:32px; height:32px; border-radius:8px; background:#f3e8ff; display:inline-flex; align-items:center; justify-content:center; color:#7c3aed; flex-shrink:0;">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#7c3aed" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                <path d="m12 3-1.9 5.8a2 2 0 0 1-1.3 1.3L3 12l5.8 1.9a2 2 0 0 1 1.3 1.3L12 21l1.9-5.8a2 2 0 0 1 1.3-1.3L21 12l-5.8-1.9a2 2 0 0 1-1.3-1.3L12 3z"/>
              </svg>
            </div>
            <div>
              <h4 style="font-size:13.5px; font-weight:700; color:#101A46; margin:0;" id="client-assistant-title">Company Intelligence</h4>
              <span style="font-size:10.5px; color:#6b7280;">Live 150ms Stream Engine</span>
            </div>
          </div>
          <span class="badge" style="background:#ede9fe; color:#7c3aed; font-weight:600; font-size:10px; padding:3px 8px; border-radius:6px;">Active</span>
        </div>
        <p style="font-size:11.5px; color:#64748b; line-height:1.45; margin-bottom:12px;">Auto-extracts identity, logos, ATS, and company profile with 0% server read strain.</p>

        <!-- Live Suggestion Rows -->
        <div class="client-suggestions mb-3" style="background:#ffffff; border:1px solid #ede9fe; border-radius:8px; padding:10px 12px;">
          <div style="font-size:10.5px; font-weight:700; color:#6b7280; text-transform:uppercase; letter-spacing:0.5px; margin-bottom:8px;">Detected Attributes</div>
          
          <div class="client-suggestion-row d-flex align-items-center justify-content-between py-1 border-bottom" style="border-color:#f3f4f6 !important;">
            <span class="client-suggestion-label" style="font-size:11.5px; color:#475569; font-weight:500;">Industry</span>
            <div class="d-flex align-items-center gap-1">
              <span class="client-assistant-value" data-client-field="industry" style="font-size:11px; font-weight:600; color:#1e293b; max-width:110px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">Not set</span>
              <i class="la la-check-circle client-suggestion-status is-missing" style="font-size:14px;" aria-hidden="true"></i>
            </div>
          </div>

          <div class="client-suggestion-row d-flex align-items-center justify-content-between py-1 border-bottom" style="border-color:#f3f4f6 !important;">
            <span class="client-suggestion-label" style="font-size:11.5px; color:#475569; font-weight:500;">Company Size</span>
            <div class="d-flex align-items-center gap-1">
              <span class="client-assistant-value" data-client-field="company_sizes" style="font-size:11px; font-weight:600; color:#1e293b; max-width:110px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">Not set</span>
              <i class="la la-check-circle client-suggestion-status is-missing" style="font-size:14px;" aria-hidden="true"></i>
            </div>
          </div>

          <div class="client-suggestion-row d-flex align-items-center justify-content-between py-1 border-bottom" style="border-color:#f3f4f6 !important;">
            <span class="client-suggestion-label" style="font-size:11.5px; color:#475569; font-weight:500;">ATS Platform</span>
            <div class="d-flex align-items-center gap-1">
              <span class="client-assistant-value" data-client-field="ats_provider" style="font-size:11px; font-weight:600; color:#1e293b; max-width:110px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">Not detected</span>
              <i class="la la-check-circle client-suggestion-status is-missing" style="font-size:14px;" aria-hidden="true"></i>
            </div>
          </div>

          <div class="client-suggestion-row d-flex align-items-center justify-content-between py-1">
            <span class="client-suggestion-label" style="font-size:11.5px; color:#475569; font-weight:500;">Founded Year</span>
            <div class="d-flex align-items-center gap-1">
              <span class="client-assistant-value" data-client-field="founded" style="font-size:11px; font-weight:600; color:#1e293b; max-width:110px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">Not set</span>
              <i class="la la-check-circle client-suggestion-status is-missing" style="font-size:14px;" aria-hidden="true"></i>
            </div>
          </div>
        </div>

        <button type="button" class="btn w-100" id="client_fetch_side_btn" onclick="$('#client_auto_fetch').click();" style="background:#2563eb; color:#ffffff; font-weight:600; font-size:12px; border-radius:8px; padding:10px 14px; border:none; display:flex; align-items:center; justify-content:center; gap:6px; box-shadow:0 2px 6px rgba(37, 99, 235, 0.25); cursor:pointer;">
          <i class="la la-magic"></i> Auto-Fetch from Website
        </button>
        <div style="font-size:10.5px; color:#64748b; margin-top:8px; display:flex; align-items:center; gap:5px;">
          <i class="la la-shield" style="color:#2563eb; font-size:13px;"></i>
          <span>Zero external paid subscriptions required.</span>
        </div>
      </div>

      <!-- Quick Tips Card -->
      <div class="client-helper-card client-tips-card quick-tips-card" style="background:#ffffff; border:1px solid #e2e8f0; border-radius:12px; padding:18px; box-shadow:0 1px 3px rgba(0,0,0,0.03);">
        <div class="d-flex align-items-center gap-2 mb-3">
          <div style="width:30px; height:30px; border-radius:8px; background:#fffbeb; display:inline-flex; align-items:center; justify-content:center; color:#d97706; flex-shrink:0;">
            <i class="la la-lightbulb-o" style="font-size:18px; color:#d97706;"></i>
          </div>
          <div>
            <h4 style="font-size:13.5px; font-weight:700; color:#101A46; margin:0;">Enterprise Quick Tips</h4>
            <span style="font-size:10.5px; color:#6b7280;">Profile Setup Best Practices</span>
          </div>
        </div>
        <ul style="list-style:none; padding:0; margin:0;">
          <li style="display:flex; align-items:flex-start; gap:8px; font-size:11.5px; color:#475569; margin-bottom:10px;">
            <i class="la la-check-circle" style="font-size:15px; color:#10b981; margin-top:1px; flex-shrink:0;"></i>
            <span>Entering the domain auto-populates 20+ fields across all 5 wizard steps.</span>
          </li>
          <li style="display:flex; align-items:flex-start; gap:8px; font-size:11.5px; color:#475569; margin-bottom:10px;">
            <i class="la la-check-circle" style="font-size:15px; color:#10b981; margin-top:1px; flex-shrink:0;"></i>
            <span>Logos are rasterized into 512px PNGs in memory for flawless Prescreen Report generation.</span>
          </li>
          <li style="display:flex; align-items:flex-start; gap:8px; font-size:11.5px; color:#475569; margin-bottom:10px;">
            <i class="la la-check-circle" style="font-size:15px; color:#10b981; margin-top:1px; flex-shrink:0;"></i>
            <span>Detected ATS allows recruiters to tailor candidate dossier submission formats.</span>
          </li>
          <li style="display:flex; align-items:flex-start; gap:8px; font-size:11.5px; color:#475569;">
            <i class="la la-check-circle" style="font-size:15px; color:#10b981; margin-top:1px; flex-shrink:0;"></i>
            <span>All values remain 100% editable prior to submitting.</span>
          </li>
        </ul>
      </div>
    </aside>

  </div>
</div>
<!--end::Client Information-->
