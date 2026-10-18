<!--begin::Client Description (God-Level Design Aligned with Reference Design System)-->
<div class="pb-5 client-desc-step" data-wizard-type="step-content">
  <div class="client-step-layout client-description-layout">
    
    <!-- ==========================================
         MAIN COLUMN: About Company & Work Culture
         ========================================== -->
    <div class="client-main-column">
      
      <!-- Card 1: Company Description -->
      <section class="card client-section-card client-desc-card mb-4" style="background:#ffffff; border:1px solid #e2e8f0; border-radius:12px; box-shadow:0 1px 3px rgba(0,0,0,0.02); overflow:hidden;">
        <!-- Header bar -->
        <div class="card-header border-0 d-flex align-items-center" style="background:#ffffff; border-bottom:1px solid #f8fafc !important; padding:16px 20px !important; justify-content:flex-start !important;">
          <div class="d-flex align-items-center" style="gap:12px; text-align:left !important;">
            <div style="width:38px; height:38px; border-radius:10px; background:#eff6ff; display:inline-flex; align-items:center; justify-content:center; color:#2563eb; flex-shrink:0;">
              <i class="la la-file-text-o" style="font-size:20px; color:#2563eb;"></i>
            </div>
            <div style="text-align:left !important;">
              <h3 class="client-section-heading" style="font-size:15px; font-weight:700; color:#101A46; margin:0 0 2px 0; text-align:left !important;">About the Company</h3>
              <p class="client-section-copy" style="font-size:12px; color:#5C6F9F; margin:0; text-align:left !important;">Describe the client's business, mission, and core value proposition.</p>
            </div>
          </div>
        </div>

        <div class="card-body" style="padding:20px !important;">
          <div class="client-editor-wrap">
            <div class="d-flex align-items-center justify-content-between mb-2 client-desc-label-row">
              <label class="form-label mb-0" style="font-size:12.5px; font-weight:600; color:#101A46;">Company Description <span class="text-danger">*</span></label>
              <span class="client-char-count text-muted" style="font-size:12px; font-weight:600;"><span id="client_desc_count">0</span>/2000</span>
            </div>
            <div class="client-editor-box" id="client_editor_box" style="border:1px solid #cbd5e1; border-radius:8px; overflow:hidden; background:#ffffff;">
              <div id="kt_client_description_quill" style="min-height:160px; font-size:13px; color:#101A46;">{!! @$description !!}</div>
            </div>
            <textarea id="kt_docs_tinymce_hidden" name="kt_docs_tinymce_hidden" style="display:none !important;" aria-hidden="true"><?php echo @$description; ?></textarea>
          </div>

          <!-- Tips banner -->
          <div class="client-info-banner" style="background:#eff6ff; border:1px solid #dbeafe; border-radius:8px; padding:12px 14px !important; color:#1e40af; margin-top:16px !important; margin-bottom:0 !important;">
            <div class="d-flex align-items-center gap-2 mb-1 font-weight-bold" style="font-size:12px; font-weight:700; color:#1e40af;">
              <i class="la la-info-circle" style="font-size:15px; color:#3b82f6;"></i>
              <span>Auto-Populated via Website OpenGraph & Schema.org</span>
            </div>
            <p style="margin:0; font-size:11px; color:#1e3a8a; line-height:1.4;">This overview is automatically synchronized with JERO's Prescreen Dossier and candidate job pitches.</p>
          </div>
        </div>
      </section>

      <!-- Card 2: Workplace Culture, Notice Period & Compensation Policy -->
      <section class="card client-section-card mb-4" style="background:#ffffff; border:1px solid #e2e8f0; border-radius:12px; box-shadow:0 1px 3px rgba(0,0,0,0.02); overflow:hidden;">
        <div class="card-header border-0 d-flex align-items-center" style="background:#ffffff; border-bottom:1px solid #f8fafc !important; padding:16px 20px !important;">
          <div class="d-flex align-items-center" style="gap:12px;">
            <div style="width:38px; height:38px; border-radius:10px; background:#f0fdf4; display:inline-flex; align-items:center; justify-content:center; color:#16a34a; flex-shrink:0;">
              <i class="la la-users" style="font-size:20px; color:#16a34a;"></i>
            </div>
            <div>
              <h3 style="font-size:15px; font-weight:700; color:#101A46; margin:0 0 2px 0;">Workplace Culture & Hiring Preferences</h3>
              <p style="font-size:11.5px; color:#64748b; margin:0;">Define work model, notice period appetite, and compensation philosophy.</p>
            </div>
          </div>
        </div>

        <div class="card-body" style="padding:20px !important;">
          <div style="display:grid; grid-template-columns:repeat(2, minmax(0, 1fr)); gap:16px;">
            
            <!-- Workplace Model -->
            <div>
              <label class="form-label mb-1" style="font-size:12px; font-weight:600; color:#1e293b;">Workplace Model</label>
              <select name="work_model" id="work_model" class="form-select" style="border:1px solid #cbd5e1; border-radius:8px; height:40px; font-size:12.5px; color:#1e293b;">
                <option value="Hybrid" <?php echo (@$detail_arr['work_model'] == 'Hybrid' ? 'selected' : ''); ?>>Hybrid (2–3 Days in Office)</option>
                <option value="Remote" <?php echo (@$detail_arr['work_model'] == 'Remote' ? 'selected' : ''); ?>>100% Remote / Distributed</option>
                <option value="Onsite" <?php echo (@$detail_arr['work_model'] == 'Onsite' ? 'selected' : ''); ?>>100% Onsite (Office Based)</option>
              </select>
            </div>

            <!-- Notice Period Tolerance -->
            <div>
              <label class="form-label mb-1" style="font-size:12px; font-weight:600; color:#1e293b;">Notice Period Appetite</label>
              <select name="notice_period_tolerance" id="notice_period_tolerance" class="form-select" style="border:1px solid #cbd5e1; border-radius:8px; height:40px; font-size:12.5px; color:#1e293b;">
                <option value="Immediate to 15 Days" <?php echo (@$detail_arr['notice_period_tolerance'] == 'Immediate to 15 Days' ? 'selected' : ''); ?>>Immediate to 15 Days (Urgent)</option>
                <option value="Up to 30 Days" <?php echo (@$detail_arr['notice_period_tolerance'] == 'Up to 30 Days' ? 'selected' : ''); ?>>Up to 30 Days (Standard)</option>
                <option value="Up to 60 Days" <?php echo (@$detail_arr['notice_period_tolerance'] == 'Up to 60 Days' ? 'selected' : ''); ?>>Up to 60 Days</option>
                <option value="90 Days Allowed" <?php echo (@$detail_arr['notice_period_tolerance'] == '90 Days Allowed' ? 'selected' : ''); ?>>90 Days Allowed (Enterprise)</option>
              </select>
            </div>

            <!-- Compensation Philosophy -->
            <div>
              <label class="form-label mb-1" style="font-size:12px; font-weight:600; color:#1e293b;">Compensation Philosophy</label>
              <select name="compensation_philosophy" id="compensation_philosophy" class="form-select" style="border:1px solid #cbd5e1; border-radius:8px; height:40px; font-size:12.5px; color:#1e293b;">
                <option value="Top of Market (90th Percentile)" <?php echo (@$detail_arr['compensation_philosophy'] == 'Top of Market (90th Percentile)' ? 'selected' : ''); ?>>Top of Market (90th Percentile)</option>
                <option value="Competitive (50th-75th Percentile)" <?php echo (@$detail_arr['compensation_philosophy'] == 'Competitive (50th-75th Percentile)' || empty(@$detail_arr['compensation_philosophy']) ? 'selected' : ''); ?>>Competitive (50th–75th Percentile)</option>
                <option value="Budget-Conscious / High Growth" <?php echo (@$detail_arr['compensation_philosophy'] == 'Budget-Conscious / High Growth' ? 'selected' : ''); ?>>Budget-Conscious / Equity Heavy</option>
              </select>
            </div>

            <!-- Notice Buyout Support Toggle -->
            <div>
              <label class="form-label mb-1" style="font-size:12px; font-weight:600; color:#1e293b;">Notice Buyout Support</label>
              <div class="d-flex align-items-center" style="height:40px; background:#f8fafc; border:1px solid #cbd5e1; border-radius:8px; padding:0 12px; gap:10px;">
                <input class="form-check-input" type="checkbox" name="notice_buyout_support" id="notice_buyout_support" value="1" <?php echo (!empty(@$detail_arr['notice_buyout_support']) ? 'checked' : ''); ?> style="cursor:pointer; width:18px; height:18px;">
                <label for="notice_buyout_support" class="form-check-label mb-0" style="font-size:12px; color:#334155; cursor:pointer; font-weight:500;">
                  Client willing to buyout candidate notice period
                </label>
              </div>
            </div>

          </div>
        </div>
      </section>

    </div>

    <!-- ==========================================
         RIGHT SIDEBAR: Detected Stack & Services
         ========================================== -->
    <aside class="client-side-column">
      
      <!-- Card: Service Offerings & Detected Tech Stack -->
      <div class="card client-helper-card client-service-offerings-card mb-4" style="background:#ffffff; border:1px solid #e2e8f0; border-radius:12px; box-shadow:0 1px 3px rgba(0,0,0,0.02); overflow:hidden;">
        <div class="card-header border-0 d-flex align-items-center" style="background:#ffffff; border-bottom:1px solid #f8fafc !important; padding:14px 18px !important; justify-content:flex-start !important;">
          <div class="d-flex align-items-center" style="gap:10px; text-align:left !important;">
            <div style="width:34px; height:34px; border-radius:8px; background:#fef9c3; display:inline-flex; align-items:center; justify-content:center; color:#ca8a04; flex-shrink:0;">
              <i class="la la-layer-group" style="font-size:18px; color:#ca8a04;"></i>
            </div>
            <div style="text-align:left !important;">
              <h4 style="font-size:13.5px; font-weight:700; color:#101A46; margin:0 0 1px 0; text-align:left !important;">Tech Stack & Services</h4>
              <p style="font-size:11px; color:#5C6F9F; margin:0; text-align:left !important;">Auto-detected from website headers & assets.</p>
            </div>
          </div>
        </div>

        <div class="card-body" style="padding:18px !important;">
          <!-- Tagify Input Box -->
          <div class="mb-3">
            <label class="form-label mb-1" style="font-size:11.5px; font-weight:600; color:#1e293b;">Primary Capabilities / Skills <span class="text-danger">*</span></label>
            <input class="form-control form-control-solid client-service-tagify" name="tags" id="tags" placeholder="Type and press Enter..." value="<?php echo @$tags; ?>" style="min-height:42px; border:1px solid #cbd5e1; border-radius:8px;">
          </div>

          <!-- Error message if empty -->
          <div class="text-danger mb-3" id="client_tags_error" style="display:none; font-size:11.5px; font-weight:600;">
            <i class="la la-exclamation-circle"></i> Please add at least 1 skill or service offering.
          </div>

          <!-- Auto-Detected Tech Stack Badges -->
          <div class="mb-3">
            <span class="client-suggested-label" style="font-size:11.5px; font-weight:700; color:#101A46; display:block; margin-bottom:8px;">
              <i class="la la-bolt text-warning"></i> Detected Technologies (1-Click Add)
            </span>
            <div class="client-suggested-list d-flex flex-wrap gap-2" id="client_detected_tech_list">
              <?php
              $detectedTech = [
                'React',
                'AWS',
                'Node.js',
                'Cloudflare',
                'Salesforce',
                'Python',
                'Docker',
                'Kubernetes'
              ];
              foreach ($detectedTech as $tech): ?>
              <button type="button" class="btn btn-sm client-suggested-tag" data-value="<?php echo htmlspecialchars($tech); ?>" onclick="window.addSuggestedService ? window.addSuggestedService('<?php echo htmlspecialchars($tech, ENT_QUOTES); ?>') : false;" style="background:#f8fafc; border:1px solid #cbd5e1; border-radius:20px; padding:3px 10px; font-size:11px; font-weight:600; color:#334155; display:inline-flex; align-items:center; gap:5px; cursor:pointer;">
                <i class="la la-plus-circle" style="color:#2563eb; font-size:13px;"></i>
                <span><?php echo htmlspecialchars($tech); ?></span>
              </button>
              <?php endforeach; ?>
            </div>
          </div>

          <!-- Suggested Industry Services -->
          <div>
            <span class="client-suggested-label" style="font-size:11.5px; font-weight:700; color:#101A46; display:block; margin-bottom:8px;">Standard Offerings</span>
            <div class="client-suggested-list d-flex flex-wrap gap-2" id="client_suggested_services">
              <?php
              $suggestedServices = [
                'Software Engineering',
                'Cloud Architecture',
                'Data Analytics',
                'DevOps & CI/CD',
                'Product Management',
                'UI/UX Design'
              ];
              foreach ($suggestedServices as $svc): ?>
              <button type="button" class="btn btn-sm client-suggested-tag" data-value="<?php echo htmlspecialchars($svc); ?>" onclick="window.addSuggestedService ? window.addSuggestedService('<?php echo htmlspecialchars($svc, ENT_QUOTES); ?>') : false;" style="background:#ffffff; border:1px solid #cbd5e1; border-radius:20px; padding:3px 10px; font-size:10.5px; font-weight:500; color:#475569; display:inline-flex; align-items:center; gap:4px; cursor:pointer;">
                <i class="la la-plus" style="color:#64748b; font-size:12px;"></i>
                <span><?php echo htmlspecialchars($svc); ?></span>
              </button>
              <?php endforeach; ?>
            </div>
          </div>

          <div id="client_services_preview" class="client-services-grid" style="display:none"></div>
        </div>
      </div>

      <!-- Card: AI Description Intelligence -->
      <div class="card client-helper-card client-ai-assist-card" style="background:linear-gradient(180deg, #f5f3ff 0%, #ffffff 100%); border:1px solid #ede9fe; border-radius:12px; padding:18px !important;">
        <div class="d-flex align-items-center justify-content-between mb-2">
          <div class="d-flex align-items-center gap-2">
            <div style="width:30px; height:30px; border-radius:8px; background:#ede9fe; display:inline-flex; align-items:center; justify-content:center; color:#7c3aed;">
              <i class="la la-magic" style="font-size:16px; color:#7c3aed;"></i>
            </div>
            <h4 style="font-size:13.5px; font-weight:700; color:#101A46; margin:0;">AI Overview Assist</h4>
          </div>
          <span class="badge" style="background:#ede9fe; color:#7c3aed; font-weight:600; font-size:10px; padding:3px 8px; border-radius:6px;">150ms Stream</span>
        </div>
        <p style="font-size:11px; color:#5C6F9F; line-height:1.45; margin-bottom:10px;">Extracted metadata from the client website `<head>` stream ready to apply.</p>

        <div style="font-size:11px; font-weight:700; color:#101A46; margin-bottom:6px;">Suggested Description</div>
        <div id="client_ai_suggested_desc" class="client-ai-suggested-desc" style="background:#ffffff; border:1px solid #ede9fe; border-radius:8px; padding:12px !important; font-size:11.5px; color:#334155; line-height:1.55; margin-bottom:12px !important; max-height:120px; overflow-y:auto;">
          Paste a company website in Step 1 to extract the verified corporate overview.
        </div>

        <div class="d-flex justify-content-end">
          <button type="button" class="btn btn-sm" id="client_apply_desc_suggestion" onclick="window.applyDescriptionSuggestion ? window.applyDescriptionSuggestion() : false;" style="background:#7c3aed; border:none; color:#ffffff; font-weight:600; font-size:11.5px; border-radius:6px; padding:6px 14px; display:inline-flex; align-items:center; gap:6px; cursor:pointer;">
            <i class="la la-check"></i> Apply to Editor
          </button>
        </div>
      </div>

    </aside>

  </div>
</div>
<!--end::Client Description-->
