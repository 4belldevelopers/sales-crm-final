<!--begin::Review and Submit-->
<div class="pb-5 client-review-step" data-wizard-type="step-content">
  <div class="client-step-header mb-3">
    <div>
      <h3>Review &amp; Submit</h3>
      <p>Review all the information before submitting. You can edit any section if needed.</p>
    </div>
  </div>
  <div class="client-review-layout">
    <div class="client-review-main">
      <section class="client-review-card">
        <div class="client-review-card-head">
          <h4>Client Information</h4>
          <button type="button" class="client-review-edit" data-client-goto-step="1"><i class="la la-pencil"></i> Edit</button>
        </div>
        <div class="client-review-logo" id="client_review_logo" style="background-image: url(<?php echo @$background_image; ?>);"></div>
        <div class="client-review-grid">
          <div class="client-review-item"><label>Client Name</label><span class="span_name">&nbsp;</span></div>
          <div class="client-review-item"><label>Industry</label><span class="span_industry">&nbsp;</span></div>
          <div class="client-review-item"><label>Website</label><a href="javascript:void(0);" class="span_website">&nbsp;</a></div>
          <div class="client-review-item"><label>Company Size</label><span class="span_company_sizes">&nbsp;</span></div>
          <div class="client-review-item"><label>Company Type</label><span class="span_company_type">&nbsp;</span></div>
          <div class="client-review-item"><label>Founded Year</label><span class="span_founded">&nbsp;</span></div>
        </div>
      </section>

      <section class="client-review-card">
        <div class="client-review-card-head">
          <h4>Client Description</h4>
          <button type="button" class="client-review-edit" data-client-goto-step="2"><i class="la la-pencil"></i> Edit</button>
        </div>
        <div class="client-review-item mb-3"><label>About the Client</label></div>
        <p class="span_kt_docs_tinymce_hidden text-justify mb-3" style="font-size:11px;color:#53607e;">&nbsp;</p>
        <div class="client-review-item"><label>Services</label><span class="span_tags">&nbsp;</span></div>
      </section>

      <section class="client-review-card">
        <div class="client-review-card-head">
          <h4>Address Details</h4>
          <button type="button" class="client-review-edit" data-client-goto-step="3"><i class="la la-pencil"></i> Edit</button>
        </div>
        <div class="client-review-addresses divAddressDetail">&nbsp;</div>
      </section>

      <section class="client-review-card">
        <div class="client-review-card-head">
          <h4>Contact Persons</h4>
          <button type="button" class="client-review-edit" data-client-goto-step="4"><i class="la la-pencil"></i> Edit</button>
        </div>
        <div class="client-contact-table-wrap">
          <table class="client-contact-table">
            <thead>
              <tr>
                <th>Contact Person</th>
                <th>Designation</th>
                <th>Email</th>
                <th>Phone Number</th>
                <th>Type</th>
              </tr>
            </thead>
            <tbody class="div-contact-person">&nbsp;</tbody>
          </table>
        </div>
      </section>
    </div>

    <aside class="client-side-column">
      <div class="client-summary-card">
        <span class="client-summary-icon"><i class="la la-building"></i></span>
        <div class="client-summary-body"><h5>Client Information</h5><p class="client_summary_name">—</p></div>
        <button type="button" class="client-summary-edit" data-client-goto-step="1"><i class="la la-pencil"></i></button>
      </div>
      <div class="client-summary-card">
        <span class="client-summary-icon"><i class="la la-file-alt"></i></span>
        <div class="client-summary-body"><h5>Description</h5><p class="client_summary_desc">—</p></div>
        <button type="button" class="client-summary-edit" data-client-goto-step="2"><i class="la la-pencil"></i></button>
      </div>
      <div class="client-summary-card">
        <span class="client-summary-icon"><i class="la la-map-marker"></i></span>
        <div class="client-summary-body"><h5>Address</h5><p class="client_summary_address">—</p></div>
        <button type="button" class="client-summary-edit" data-client-goto-step="3"><i class="la la-pencil"></i></button>
      </div>
      <div class="client-summary-card">
        <span class="client-summary-icon"><i class="la la-user"></i></span>
        <div class="client-summary-body"><h5>Contacts</h5><p class="client_summary_contacts">—</p></div>
        <button type="button" class="client-summary-edit" data-client-goto-step="4"><i class="la la-pencil"></i></button>
      </div>
      <div class="client-almost-done">
        <i class="la la-check-circle" aria-hidden="true"></i>
        <h5>You're Almost Done!</h5>
        <p>Review the details above and submit to create the client profile.</p>
      </div>
    </aside>
  </div>
</div>
<!--end::Review and Submit-->
