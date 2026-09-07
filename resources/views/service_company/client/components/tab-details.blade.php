<!--begin::Client Description-->
<div class="pb-5 client-desc-step" data-wizard-type="step-content">
  <div class="client-step-layout">
    <div class="client-main-column">
      <section class="client-section-card">
        <div class="client-step-header">
          <div>
            <h3>About the Company</h3>
            <p>Describe the client's business, mission and background.</p>
          </div>
        </div>
        <div class="client-editor-wrap">
          <div class="client-editor-toolbar">
            <span>Company Description</span>
            <span class="client-char-count"><span id="client_desc_count">0</span>/2000</span>
          </div>
          <textarea id="kt_docs_tinymce_hidden" name="kt_docs_tinymce_hidden" class="tox-target"><?php echo @$description; ?></textarea>
        </div>
      </section>

      <section class="client-section-card">
        <div class="client-step-header">
          <div>
            <h3>Service Offerings</h3>
            <p>Add the services this client provides or is known for (press Enter or comma to add).</p>
          </div>
        </div>
        <input class="form-control form-control-solid" name="tags" id="tags" placeholder="e.g. Software Development, IT Consulting..." value="<?php echo @$tags; ?>">
        <div class="mt-3">
          <span class="client-suggested-label">Suggested:</span>
          <div class="client-suggested-list" id="client_suggested_services">
            <?php
            $suggestedServices = ['Software Development','IT Consulting','Cloud Solutions','Digital Transformation','Data Analytics','Cybersecurity','UI/UX Design','Project Management','DevOps','Quality Assurance'];
            foreach ($suggestedServices as $svc): ?>
            <span class="client-suggested-tag" data-value="<?php echo htmlspecialchars($svc); ?>">
              <?php echo htmlspecialchars($svc); ?>
              <button type="button" class="client-suggest-add" title="Add <?php echo htmlspecialchars($svc); ?>"><i class="la la-plus" aria-hidden="true"></i></button>
            </span>
            <?php endforeach; ?>
          </div>
        </div>
        <!-- hidden: keeps live-preview in sidebar in sync -->
        <div id="client_services_preview" class="client-services-grid" style="display:none"></div>
      </section>
    </div>

    <aside class="client-side-column">
      <div class="client-helper-card client-helper-blue">
        <h4><i class="la la-lightbulb-o" aria-hidden="true"></i> Writing Tips</h4>
        <ul>
          <li><i class="la la-check-circle" aria-hidden="true"></i><span>Include company mission, vision and background.</span></li>
          <li><i class="la la-check-circle" aria-hidden="true"></i><span>Mention key products, services or market focus.</span></li>
          <li><i class="la la-check-circle" aria-hidden="true"></i><span>Keep the tone professional and concise.</span></li>
        </ul>
      </div>
      <div class="client-helper-card client-helper-purple">
        <h4><i class="la la-cube" aria-hidden="true"></i> Why add Services?</h4>
        <p>Services help match this client with relevant job requirements and improve search visibility across the platform.</p>
      </div>
      <div class="client-helper-card client-helper-green">
        <h4><i class="la la-eye" aria-hidden="true"></i> Live Preview</h4>
        <div class="client-preview-box">
          <div class="preview-title" id="client_preview_company"><?php echo @$name ?: 'Company Name'; ?></div>
          <div class="preview-body" id="client_preview_description">Your company description will appear here.</div>
          <div class="client-preview-tags" id="client_preview_tags"></div>
        </div>
      </div>
    </aside>
  </div>
</div>
<!--end::Client Description-->
