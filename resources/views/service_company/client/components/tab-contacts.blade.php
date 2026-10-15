<?php
$contactPersonId = 0;
$contactPersonIdCount = 1;
$contactsList = !empty($client_contact_arr) && (is_array($client_contact_arr) || $client_contact_arr instanceof \Countable)
    ? $client_contact_arr
    : (!empty($client_contact_info) && (is_array($client_contact_info) || $client_contact_info instanceof \Countable)
        ? $client_contact_info
        : []);
$hasContacts = count($contactsList) > 0;
?>
<!--begin::Contact Person-->
<div class="pb-5 client-contact-step" data-wizard-type="step-content">
  <div class="client-step-layout">
    <div class="client-main-column">
      <div class="client-contact-toolbar">
        <div>
          <h3 class="client-section-heading mb-1">Contact Person</h3>
          <p class="client-section-copy mb-0">Add key contacts for this client organization.</p>
        </div>
        <button type="button" class="client-add-address-btn" id="client_open_contact_drawer" onclick="window.openContactDrawer ? window.openContactDrawer(null) : false;"><i class="la la-plus" aria-hidden="true"></i> Add Contact Person</button>
      </div>
      <div class="client-contact-table-wrap mb-3">
        <table class="client-contact-table">
          <thead>
            <tr>
              <th>Contact Person</th>
              <th>Designation</th>
              <th>Email</th>
              <th>Phone Number</th>
              <th>Type</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody id="client_contact_table_body">
<?php
$renderedTableRows = 0;
if ($page_event == 'EDIT' && $hasContacts) {
    foreach ($contactsList as $idx => $ccArr) {
        $cName = is_object($ccArr) ? ($ccArr->contact_person_name ?? '') : ($ccArr['contact_person_name'] ?? '');
        $cEmail = is_object($ccArr) ? ($ccArr->contact_person_email ?? '') : ($ccArr['contact_person_email'] ?? '');
        $cNumber = is_object($ccArr) ? ($ccArr->contact_person_number ?? '') : ($ccArr['contact_person_number'] ?? '');
        $cDesig = is_object($ccArr) ? ($ccArr->contact_person_designation ?? '') : ($ccArr['contact_person_designation'] ?? '');
        if (trim($cName) === '' && trim($cEmail) === '') continue;
        $renderedTableRows++;
        $typeBadge = ($renderedTableRows === 1) ? '<span class="client-badge-primary">Primary</span>' : '<span class="client-badge-secondary">Secondary</span>';
        $primaryTag = ($renderedTableRows === 1) ? '<span class="client-badge-primary">Primary</span>' : '';
        $words = preg_split('/\s+/', trim($cName));
        $initials = '';
        foreach (array_slice($words, 0, 2) as $w) { $initials .= strtoupper($w[0] ?? ''); }
        $initials = $initials ?: 'CP';
?>
            <tr data-contact-index="<?php echo $idx; ?>">
              <td>
                <div class="client-contact-person">
                  <span class="client-contact-avatar" style="background-color: #ffd428; color: #1e1e2d; display:inline-flex; align-items:center; justify-content:center; font-weight:700; width:32px; height:32px; border-radius:50%; margin-right:8px;"><?php echo htmlspecialchars($initials); ?></span>
                  <span class="client-contact-name"><?php echo htmlspecialchars($cName); ?><?php echo $primaryTag; ?></span>
                </div>
              </td>
              <td><?php echo htmlspecialchars($cDesig ?: '—'); ?></td>
              <td><?php echo htmlspecialchars($cEmail ?: '—'); ?></td>
              <td><?php echo htmlspecialchars($cNumber ?: '—'); ?></td>
              <td><?php echo $typeBadge; ?></td>
              <td>
                <div class="client-table-actions">
                  <button type="button" class="client-edit-contact" data-index="<?php echo $idx; ?>" onclick="window.openContactDrawer ? window.openContactDrawer(<?php echo $idx; ?>) : false;" title="Edit"><i class="la la-pencil"></i></button>
                  <button type="button" class="client-delete-contact is-delete" data-index="<?php echo $idx; ?>" onclick="window.deleteContactRow ? window.deleteContactRow(<?php echo $idx; ?>) : false;" title="Delete"><i class="la la-trash"></i></button>
                </div>
              </td>
            </tr>
<?php
    }
}
if ($renderedTableRows === 0) {
?>
            <tr id="client_contact_empty_row">
              <td colspan="6" class="text-center text-muted py-6">No contact person added yet. Click "Add Contact Person" to begin.</td>
            </tr>
<?php } ?>
          </tbody>
        </table>
      </div>
      <div class="client-info-banner mb-0"><i class="la la-info-circle" aria-hidden="true"></i><span>The first contact person is set as the primary contact by default.</span></div>

      <div class="client-contact-fields">
        <div class="row client-cperson-row">
<?php
if ($page_event == 'EDIT' && $hasContacts) {
    foreach ($contactsList as $ccArr) {
        $cName = is_object($ccArr) ? ($ccArr->contact_person_name ?? '') : ($ccArr['contact_person_name'] ?? '');
        $cEmail = is_object($ccArr) ? ($ccArr->contact_person_email ?? '') : ($ccArr['contact_person_email'] ?? '');
        $cNumber = is_object($ccArr) ? ($ccArr->contact_person_number ?? '') : ($ccArr['contact_person_number'] ?? '');
        $cCalling = is_object($ccArr) ? ($ccArr->calling_code ?? '') : ($ccArr['calling_code'] ?? '');
        $cCalling = !empty($cCalling) ? $cCalling : ($country_code ?? 'in');
        $cDesig = is_object($ccArr) ? ($ccArr->contact_person_designation ?? '') : ($ccArr['contact_person_designation'] ?? '');
        echo '<div class="row client-cperson-container" data-contact-index="'.$contactPersonId.'">
          <div class="col-xl-3"><div class="form-group mb-0 client-cperson-container-child"><input type="text" name="cp_name[]" id="cp_name_'.$contactPersonId.'" class="form-control form-control-solid" value="'.htmlspecialchars($cName, ENT_QUOTES).'" placeholder="Enter person name"></div></div>
          <div class="col-xl-3"><div class="form-group mb-0 client-cperson-container-child"><input type="email" name="cp_email[]" id="cp_email_'.$contactPersonId.'" class="form-control form-control-solid" value="'.htmlspecialchars($cEmail, ENT_QUOTES).'" placeholder="Enter email"></div></div>
          <div class="col-xl-3"><div class="form-group mb-0 client-cperson-container-child"><input type="number" name="cp_number[]" id="cp_number_'.$contactPersonId.'" class="form-control form-control-solid" value="'.htmlspecialchars($cNumber, ENT_QUOTES).'" placeholder="Enter contact number"><input type="hidden" value="'.htmlspecialchars($cCalling, ENT_QUOTES).'" class="calling_code" name="calling_code[]" id="calling_code_'.$contactPersonId.'"></div></div>
          <div class="col-xl-3"><div class="form-group mb-0 client-cperson-container-child"><input type="text" name="cp_desigination[]" id="cp_desigination_'.$contactPersonId.'" class="form-control form-control-solid cp_desigination" value="'.htmlspecialchars($cDesig, ENT_QUOTES).'" placeholder="Enter desigination"></div></div>
        </div>';
        $contactPersonId++;
        $contactPersonIdCount++;
    }
    $contactPersonId--;
} else {
?>
          <div class="row client-cperson-container" data-contact-index="0">
            <div class="col-xl-3"><div class="form-group mb-0 client-cperson-container-child"><input type="text" name="cp_name[]" id="cp_name_0" class="form-control form-control-solid" placeholder="Enter person name"></div></div>
            <div class="col-xl-3"><div class="form-group mb-0 client-cperson-container-child"><input type="email" name="cp_email[]" id="cp_email_0" class="form-control form-control-solid" placeholder="Enter email"></div></div>
            <div class="col-xl-3"><div class="form-group mb-0 client-cperson-container-child"><input type="number" name="cp_number[]" id="cp_number_0" class="form-control form-control-solid" placeholder="Enter contact number"><input type="hidden" value="{{ $country_code }}" class="calling_code" name="calling_code[]" id="calling_code_0"></div></div>
            <div class="col-xl-3"><div class="form-group mb-0 client-cperson-container-child"><input type="text" name="cp_desigination[]" id="cp_desigination_0" class="form-control form-control-solid cp_desigination" placeholder="Enter desigination"></div></div>
          </div>
<?php } ?>
        </div>
      </div>
      <input type="hidden" name="contactPersonId" id="contactPersonId" value="<?php echo @$contactPersonId; ?>" />
      <button type="button" class="add_cperson d-none" aria-hidden="true"></button>
      <button type="button" class="remove_cperson d-none" aria-hidden="true"></button>
    </div>
    <aside class="client-side-column">
      <div class="client-helper-card client-helper-purple">
        <h4><i class="la la-users" aria-hidden="true"></i> Why add Contact Person?</h4>
        <ul>
          <li><i class="la la-check-circle" aria-hidden="true"></i><span>Quick communication for job requirements.</span></li>
          <li><i class="la la-check-circle" aria-hidden="true"></i><span>Track interactions and follow-ups easily.</span></li>
          <li><i class="la la-check-circle" aria-hidden="true"></i><span>Assign primary and secondary contacts.</span></li>
        </ul>
      </div>
      <div class="client-helper-card client-helper-green">
        <h4><i class="la la-lightbulb-o" aria-hidden="true"></i> Best Practices</h4>
        <ul>
          <li><i class="la la-check-circle" aria-hidden="true"></i><span>Ensure email and phone number are valid.</span></li>
          <li><i class="la la-check-circle" aria-hidden="true"></i><span>Add HR or hiring manager as primary contact.</span></li>
          <li><i class="la la-check-circle" aria-hidden="true"></i><span>Include designation for context.</span></li>
        </ul>
      </div>
    </aside>
  </div>
</div>

<div class="client-contact-drawer-backdrop" id="client_contact_drawer_backdrop" onclick="window.closeContactDrawer ? window.closeContactDrawer() : false;"></div>
<div class="client-contact-drawer" id="client_contact_drawer" aria-hidden="true">
  <!-- Header -->
  <div class="client-drawer-head">
    <div class="client-drawer-head-info">
      <h4 id="client_contact_drawer_title">Add Contact Person</h4>
      <p class="client-drawer-head-subtitle" id="client_contact_drawer_subtitle">Add the primary or secondary contact details for this client.</p>
    </div>
    <button type="button" class="client-drawer-close-btn" id="client_close_contact_drawer" title="Close" aria-label="Close drawer" onclick="window.closeContactDrawer ? window.closeContactDrawer() : false;">
      <i class="la la-times" aria-hidden="true"></i>
    </button>
  </div>
  <!-- Body -->
  <div class="client-drawer-body">
    <input type="hidden" id="client_drawer_edit_index" value="">

    <div class="client-drawer-field">
      <label class="cdf-label" for="client_drawer_name">Contact Name<span class="cdf-required">*</span></label>
      <div class="cdf-input-wrap">
        <i class="la la-user cdf-input-icon" aria-hidden="true"></i>
        <input type="text" id="client_drawer_name" placeholder="Enter contact name" autocomplete="off">
      </div>
      <div class="cdf-error" id="cdf_err_name">Contact name is required.</div>
    </div>

    <div class="client-drawer-field">
      <label class="cdf-label" for="client_drawer_designation">Designation<span class="cdf-required">*</span></label>
      <div class="cdf-input-wrap">
        <i class="la la-id-badge cdf-input-icon" aria-hidden="true"></i>
        <input type="text" id="client_drawer_designation" placeholder="Enter designation" autocomplete="off">
      </div>
      <div class="cdf-error" id="cdf_err_designation">Designation is required.</div>
    </div>

    <div class="client-drawer-field">
      <label class="cdf-label" for="client_drawer_email">Email Address<span class="cdf-required">*</span></label>
      <div class="cdf-input-wrap">
        <i class="la la-envelope cdf-input-icon" aria-hidden="true"></i>
        <input type="email" id="client_drawer_email" placeholder="name@company.com" autocomplete="off">
      </div>
      <div class="cdf-error" id="cdf_err_email">Please enter a valid email address.</div>
    </div>

    <div class="client-drawer-field">
      <label class="cdf-label" for="client_drawer_phone">Phone Number</label>
      <div class="cdf-input-wrap">
        <i class="la la-phone cdf-input-icon" aria-hidden="true"></i>
        <input type="number" id="client_drawer_phone" placeholder="Enter phone number" autocomplete="off">
      </div>
      <div class="cdf-error" id="cdf_err_phone">Please enter a valid phone number (6-15 digits).</div>
    </div>
  </div>
  <!-- Footer -->
  <div class="client-drawer-foot">
    <button type="button" class="cdf-cancel-btn" id="client_cancel_contact_drawer" onclick="window.closeContactDrawer ? window.closeContactDrawer() : false;">Cancel</button>
    <button type="button" class="cdf-save-btn" id="client_save_contact_drawer" onclick="window.saveContactDrawer ? window.saveContactDrawer() : false;">
      <span class="cdf-spinner"></span>
      <i class="la la-user-plus cdf-btn-icon" aria-hidden="true"></i>
      <span class="cdf-btn-text">Add Contact Person</span>
    </button>
  </div>
</div>
<!--end::Contact Person-->
