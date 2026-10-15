<!--begin::Client Description (God-Level Design Aligned with media_1792065531568.png)-->
<div class="pb-5 client-desc-step" data-wizard-type="step-content">
  <div class="client-step-layout">
    
    <!-- ==========================================
         MAIN COLUMN: About the Company Editor
         ========================================== -->
    <div class="client-main-column">
      <section class="card client-section-card" style="background:#ffffff; border:1px solid #e2e8f0; border-radius:12px; box-shadow:0 1px 3px rgba(0,0,0,0.03); overflow:hidden;">
        <!-- Header bar matching screenshots -->
        <div class="card-header border-0 py-4 px-6 d-flex align-items-center" style="background:#ffffff; border-bottom:1px solid #f1f5f9 !important;">
          <div style="width:38px; height:38px; border-radius:10px; background:#fef9c3; display:inline-flex; align-items:center; justify-content:center; color:#ca8a04; margin-right:12px; flex-shrink:0;">
            <i class="la la-file-text-o" style="font-size:20px; color:#ca8a04;"></i>
          </div>
          <div>
            <h3 class="client-section-heading" style="font-size:15px; font-weight:700; color:#0f172a; margin:0 0 2px 0;">About the Company</h3>
            <p class="client-section-copy" style="font-size:12px; color:#64748b; margin:0;">Describe the client's business, mission and background.</p>
          </div>
        </div>

        <div class="card-body p-6">
          <div class="client-editor-wrap">
            <div class="d-flex align-items-center justify-content-between mb-2">
              <label class="form-label mb-0" style="font-size:12.5px; font-weight:600; color:#0f172a;">Company Description <span class="text-danger">*</span></label>
              <span class="client-char-count text-muted" style="font-size:12px; font-weight:600;"><span id="client_desc_count">0</span>/2000</span>
            </div>
            <textarea id="kt_docs_tinymce_hidden" name="kt_docs_tinymce_hidden" class="tox-target"><?php echo @$description; ?></textarea>
          </div>

          <!-- Tips banner at bottom matching screenshot -->
          <div class="client-info-banner mt-4 mb-0" style="background:#eff6ff; border:1px solid #dbeafe; border-radius:8px; padding:12px 16px; color:#1e40af;">
            <div class="d-flex align-items-center gap-2 mb-2 font-weight-bold" style="font-size:12.5px; font-weight:700; color:#1e40af;">
              <i class="la la-info-circle" style="font-size:16px; color:#3b82f6;"></i>
              <span>Tips for a great company description</span>
            </div>
            <ul style="list-style:disc; padding-left:24px; margin:0; font-size:11.5px; color:#1e3a8a; line-height:1.6;">
              <li>Include a brief overview of the company and what they do.</li>
              <li>Mention their mission, vision and key strengths.</li>
              <li>This information helps recruiters understand the client better.</li>
            </ul>
          </div>
        </div>
      </section>
    </div>

    <!-- ==========================================
         RIGHT SIDEBAR: Service Offerings & AI Assist
         ========================================== -->
    <aside class="client-side-column">
      
      <!-- Card 1: Service Offerings -->
      <div class="card client-helper-card mb-4" style="background:#ffffff; border:1px solid #e2e8f0; border-radius:12px; box-shadow:0 1px 3px rgba(0,0,0,0.03); overflow:hidden;">
        <div class="card-header border-0 py-3 px-5 d-flex align-items-center" style="background:#ffffff; border-bottom:1px solid #f1f5f9 !important;">
          <div style="width:32px; height:32px; border-radius:8px; background:#fef9c3; display:inline-flex; align-items:center; justify-content:center; color:#ca8a04; margin-right:10px; flex-shrink:0;">
            <i class="la la-layer-group" style="font-size:18px; color:#ca8a04;"></i>
          </div>
          <div>
            <h4 style="font-size:14px; font-weight:700; color:#0f172a; margin:0 0 1px 0;">Service Offerings</h4>
            <p style="font-size:11.5px; color:#64748b; margin:0;">Add the services that this client provides or is known for.</p>
          </div>
        </div>

        <div class="card-body p-5">
          <!-- Tagify Input Box -->
          <div class="mb-2">
            <input class="form-control form-control-solid client-service-tagify" name="tags" id="tags" placeholder="Type and press Enter..." value="<?php echo @$tags; ?>" style="min-height:42px; border-radius:8px;">
          </div>

          <!-- Error message if empty -->
          <div class="text-danger mb-3" id="client_tags_error" style="display:none; font-size:11.5px; font-weight:600;">
            <i class="la la-exclamation-circle"></i> Please add at least 1 Service Offering.
          </div>

          <div class="mb-2">
            <span class="client-suggested-label" style="font-size:11.5px; font-weight:700; color:#334155; display:block; margin-bottom:8px;">Suggested Services</span>
            <div class="client-suggested-list d-flex flex-wrap gap-2" id="client_suggested_services">
              <?php
              $suggestedServices = [
                'Software Development',
                'IT Consulting',
                'Cloud Solutions',
                'Digital Transformation',
                'Data Analytics',
                'Cybersecurity',
                'UI/UX Design',
                'Project Management',
                'DevOps',
                'Quality Assurance'
              ];
              foreach ($suggestedServices as $svc): ?>
              <button type="button" class="btn btn-sm client-suggested-tag" data-value="<?php echo htmlspecialchars($svc); ?>" onclick="window.addSuggestedService ? window.addSuggestedService('<?php echo htmlspecialchars($svc, ENT_QUOTES); ?>') : false;" style="background:#ffffff; border:1px solid #cbd5e1; border-radius:20px; padding:4px 12px; font-size:11px; font-weight:500; color:#334155; display:inline-flex; align-items:center; gap:5px; cursor:pointer; transition:all 0.15s ease;">
                <i class="la la-plus-circle" style="color:#2563eb; font-size:13px;"></i>
                <span><?php echo htmlspecialchars($svc); ?></span>
              </button>
              <?php endforeach; ?>
            </div>
          </div>

          <!-- Live preview hidden tracker -->
          <div id="client_services_preview" class="client-services-grid" style="display:none"></div>
        </div>
      </div>

      <!-- Card 2: AI Company Assist -->
      <div class="card client-helper-card" style="background:#f5f3ff; border:1px solid #ede9fe; border-radius:12px; padding:16px;">
        <div class="d-flex align-items-center justify-content-between mb-2">
          <div class="d-flex align-items-center gap-2">
            <div style="width:30px; height:30px; border-radius:8px; background:#ede9fe; display:inline-flex; align-items:center; justify-content:center; color:#7e22ce;">
              <i class="la la-magic" style="font-size:16px; color:#7e22ce;"></i>
            </div>
            <h4 style="font-size:13.5px; font-weight:700; color:#1e1b4b; margin:0;">AI Company Assist</h4>
          </div>
          <span class="badge" style="background:#ede9fe; color:#7e22ce; font-weight:600; font-size:10.5px; padding:3px 8px; border-radius:6px;">Beta</span>
        </div>
        <p style="font-size:11.5px; color:#6b7280; line-height:1.45; margin-bottom:12px;">We found information from the company website to help you fill this section.</p>

        <div style="font-size:11.5px; font-weight:700; color:#374151; margin-bottom:6px;">Suggested Company Description</div>
        <div id="client_ai_suggested_desc" style="background:#ffffff; border:1px solid #ede9fe; border-radius:8px; padding:12px; font-size:11.5px; color:#4b5563; line-height:1.55; margin-bottom:12px;">
          Accord BuildTech is a leading building materials company involving in manufacturing and supply of high-quality construction materials for residential, commercial and infrastructure projects. The company focuses on sustainable construction solutions and innovation in building products.
        </div>

        <div class="d-flex justify-content-end">
          <button type="button" class="btn btn-sm" id="client_apply_desc_suggestion" onclick="window.applyDescriptionSuggestion ? window.applyDescriptionSuggestion() : false;" style="background:#ede9fe; border:none; color:#7e22ce; font-weight:700; font-size:12px; border-radius:8px; padding:8px 16px; display:inline-flex; align-items:center; gap:6px;">
            <i class="la la-magic" style="color:#7e22ce;"></i> Apply Suggestion
          </button>
        </div>
      </div>

    </aside>

  </div>
</div>
<!--end::Client Description-->
