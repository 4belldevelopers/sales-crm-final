<!--begin::Client Information-->
<div class="pb-5 client-info-step" data-wizard-type="step-content" data-wizard-state="current">
  <!-- Purpose: group identity and company metadata into compact cards that scan well on laptop screens. -->
  <div class="client-step-layout">
    <div class="client-main-column">
      <section class="client-section-card" aria-labelledby="client-branding-title">
        <h3 class="client-section-heading" id="client-branding-title">Company Branding</h3>
        <p class="client-section-copy">Upload company logo for visual identification.</p>
        <div class="client-branding-grid">
          <label class="client-logo-upload" for="client_logo_input">
            <span class="client-upload-icon"><i class="la la-cloud-upload-alt" aria-hidden="true"></i></span>
            <span class="client-upload-title">Upload Company Logo</span>
            <span class="client-upload-copy">PNG or JPG (Max. 2MB)</span>
            <span class="client-upload-copy">Recommended: square image</span>
          </label>
          <div class="client-logo-preview">
            <span class="client-preview-label">Preview</span>
            <div class="image-input image-input-outline" id="client_logo_preview">
              <div class="image-input-wrapper" style="background-image: url(<?php echo @$background_image; ?>);"></div>
              <label data-kt-image-input-action="change" title="Change company logo">
                <input type="file" name="logo" id="client_logo_input" accept=".png, .jpg, .jpeg" onchange="loadFile(event)"/>
                <input type="hidden" name="avatar_remove" />
              </label>
            </div>
            <button type="button" class="client-preview-remove" data-kt-image-input-action="cancel"><i class="la la-trash" aria-hidden="true"></i> Remove</button>
          </div>
          <div class="client-brand-fields">
            <div class="client-field fv-plugins-icon-container">
              <label class="form-label required" for="website">Website</label>
              <div class="input-group input-group-solid">
                <span class="input-group-text"><i class="la la-globe"></i></span>
                <input type="text" class="form-control form-control-solid" name="website" id="website" value="<?php echo @$website; ?>" placeholder="https://www.example.com">
                <button type="button" class="btn client-auto-fetch" id="client_auto_fetch"><i class="la la-magic" aria-hidden="true"></i> Auto Fetch Data</button>
              </div>
              <span class="form-text text-muted">Enter website to auto-fetch available company details.</span>
            </div>
            <div class="client-field fv-plugins-icon-container">
              <label class="form-label required" for="client_id">Client Name</label>
              <div class="input-group input-group-solid">
                <span class="input-group-text"><i class="la la-user"></i></span>
                <input type="text" class="form-control form-control-solid" name="name" id="client_id" value="<?php echo @$name; ?>" placeholder="Enter client / company name">
              </div>
            </div>
          </div>
        </div>
      </section>

      <section class="client-section-card" aria-labelledby="client-details-title">
        <h3 class="client-section-heading" id="client-details-title">Company Details</h3>
        <p class="client-section-copy">Enter essential details about the client organization.</p>
        <div class="client-details-grid">
          <div class="client-field fv-plugins-icon-container">
            <label class="form-label required" for="industry">Industry</label>
            <div class="input-group input-group-solid">
              <span class="input-group-text"><i class="la la-briefcase"></i></span>
              <select class="form-select form-select-solid" name="industry" id="industry">
                <option value="">Select industry</option>
                <?php foreach($department as $key => $val){
                  $sel = $val->ind_id == $industry ? 'selected="selected"' : '';
                  echo '<option value="'.@$val->ind_id.'" '.@$sel.'>'.ucwords(@$val->ind_name).'</option>';
                } ?>
              </select>
            </div>
          </div>
          <div class="client-field fv-plugins-icon-container">
            <label class="form-label required" for="company_sizes">Company Size</label>
            <div class="input-group input-group-solid">
              <span class="input-group-text"><i class="la la-users"></i></span>
              <select name="company_sizes" class="form-select form-select-solid" id="company_sizes">
                <option value="">Select Company Size</option>
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
          <div class="client-field fv-plugins-icon-container">
            <label class="form-label required" for="company_type">Type</label>
            <div class="input-group input-group-solid">
              <span class="input-group-text"><i class="la la-city"></i></span>
              <select class="form-select form-select-solid" name="company_type" id="company_type">
                <option value="">Select company type</option>
                <?php foreach($companyType as $key => $val){
                  $sel = $key == $company_type ? 'selected="selected"' : '';
                  echo '<option value="'.@$key.'" '.@$sel.'>'.ucwords(@$val).'</option>';
                } ?>
              </select>
            </div>
          </div>
          <div class="client-field fv-plugins-icon-container">
            <label class="form-label required" for="founded">Founded Year</label>
            <div class="input-group input-group-solid">
              <span class="input-group-text"><i class="la la-calendar"></i></span>
              <input type="number" class="form-control form-control-solid" name="founded" id="founded" value="<?php echo @$founded; ?>" placeholder="Select year">
            </div>
          </div>
        </div>
        <div class="client-tip"><i class="la la-info-circle" aria-hidden="true"></i><span><strong>Tip:</strong> You can update these details anytime from the client profile.</span></div>
      </section>
    </div>

    <aside class="client-assistant" aria-labelledby="client-assistant-title">
      <div class="client-assistant-head">
        <div>
          <div class="d-flex align-items-center gap-2">
            <h3 class="client-assistant-title" id="client-assistant-title">AI Assistant</h3>
            <span class="client-beta">Beta</span>
          </div>
          <p class="client-assistant-copy">We can auto-fetch available information from the company website.</p>
        </div>
        <span class="client-assistant-spark"><i class="la la-magic" aria-hidden="true"></i></span>
      </div>
      <div class="client-suggestions">
        <div class="client-suggestions-title">Profile Suggestions</div>
        <div class="client-suggestion-row">
          <span class="client-suggestion-label">Industry</span>
          <span class="client-assistant-value" data-client-field="industry">Not set</span>
          <i class="la la-check-circle client-suggestion-status is-missing" aria-hidden="true"></i>
        </div>
        <div class="client-suggestion-row">
          <span class="client-suggestion-label">Company Size</span>
          <span class="client-assistant-value" data-client-field="company_sizes">Not set</span>
          <i class="la la-check-circle client-suggestion-status is-missing" aria-hidden="true"></i>
        </div>
        <div class="client-suggestion-row">
          <span class="client-suggestion-label">Company Type</span>
          <span class="client-assistant-value" data-client-field="company_type">Not set</span>
          <i class="la la-check-circle client-suggestion-status is-missing" aria-hidden="true"></i>
        </div>
        <div class="client-suggestion-row">
          <span class="client-suggestion-label">Founded Year</span>
          <span class="client-assistant-value" data-client-field="founded">Not set</span>
          <i class="la la-check-circle client-suggestion-status is-missing" aria-hidden="true"></i>
        </div>
        <button type="button" class="client-assistant-action" id="client_review_details"><i class="la la-magic" aria-hidden="true"></i> Apply Website Data</button>
        <p class="client-assistant-note">Not accurate? You can edit every field manually.</p>
      </div>
    </aside>
  </div>
</div>
<!--end::Client Information-->
