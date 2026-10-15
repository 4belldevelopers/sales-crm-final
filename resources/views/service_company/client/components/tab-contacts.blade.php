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
<!--begin::Contact Person Step (God-Level Design Aligned with media_1792065553406.png & media_1792065655079.png)-->
<div class="pb-5 client-contact-step" data-wizard-type="step-content">
  <div class="client-step-layout">
    <div class="client-main-column">
      <!-- Contact Person Card -->
      <div class="card client-contact-main-card mb-4" style="background:#ffffff; border:1px solid #e2e8f0; border-radius:12px; box-shadow:0 1px 3px rgba(0,0,0,0.03); overflow:hidden;">
        <!-- Header bar matching screenshots -->
        <div class="card-header border-0 py-4 px-6 d-flex align-items-center justify-content-between" style="background:#ffffff; border-bottom:1px solid #f1f5f9 !important;">
          <div class="d-flex align-items-center">
            <div style="width:40px; height:40px; border-radius:10px; background:#fef9c3; display:inline-flex; align-items:center; justify-content:center; color:#ca8a04; margin-right:12px; flex-shrink:0;">
              <i class="la la-users" style="font-size:22px; color:#ca8a04;"></i>
            </div>
            <div>
              <h3 style="font-size:15px; font-weight:700; color:#0f172a; margin:0 0 2px 0;">Contact Person</h3>
              <p style="font-size:12px; color:#64748b; margin:0;">Add key contacts for this client organization.</p>
            </div>
          </div>
          <button type="button" class="btn btn-warning client-add-contact-btn" id="client_open_contact_drawer" onclick="window.openContactDrawer ? window.openContactDrawer(null) : false;" style="background:#ffd428 !important; border:none !important; color:#1e293b !important; font-weight:700 !important; font-size:13px !important; padding:9px 18px !important; border-radius:8px !important; box-shadow:0 1px 3px rgba(255,212,40,0.3) !important; display:inline-flex; align-items:center; gap:6px;">
            <i class="la la-plus" style="font-size:14px; font-weight:bold; color:#1e293b;"></i> Add Contact Person
          </button>
        </div>

        <!-- Contact List Table / Empty State -->
        <div class="card-body p-0">
          <div class="table-responsive">
            <table class="table align-middle mb-0" id="client_contact_table" style="width:100%; border-collapse:collapse;">
              <thead>
                <tr style="background:#f8fafc; border-bottom:1px solid #e2e8f0; color:#64748b; font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:0.5px;">
                  <th style="padding:12px 18px; width:26%;">CONTACT PERSON</th>
                  <th style="padding:12px 14px; width:18%;">DESIGNATION</th>
                  <th style="padding:12px 14px; width:22%;">EMAIL</th>
                  <th style="padding:12px 14px; width:16%;">PHONE NUMBER</th>
                  <th style="padding:12px 10px; width:10%;">TYPE</th>
                  <th style="padding:12px 18px; width:8%; text-align:right;">ACTIONS</th>
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
        $cCalling = is_object($ccArr) ? ($ccArr->calling_code ?? '') : ($ccArr['calling_code'] ?? '');
        $cDesig = is_object($ccArr) ? ($ccArr->contact_person_designation ?? '') : ($ccArr['contact_person_designation'] ?? '');
        if (trim($cName) === '' && trim($cEmail) === '') continue;
        $renderedTableRows++;
        $typeBadge = ($renderedTableRows === 1)
            ? '<span class="badge" style="background:#dcfce7; color:#166534; font-weight:600; font-size:11.5px; padding:4px 10px; border-radius:6px;">Primary</span>'
            : '<span class="badge" style="background:#eff6ff; color:#1d4ed8; font-weight:600; font-size:11.5px; padding:4px 10px; border-radius:6px;">Secondary</span>';
        
        $words = preg_split('/\s+/', trim($cName));
        $initials = '';
        foreach (array_slice($words, 0, 2) as $w) { $initials .= strtoupper($w[0] ?? ''); }
        $initials = $initials ?: 'CP';

        $palettes = [
            ['bg' => '#dbeafe', 'text' => '#1e40af'],
            ['bg' => '#f3e8ff', 'text' => '#7e22ce'],
            ['bg' => '#dcfce7', 'text' => '#166534']
        ];
        $pal = $palettes[($renderedTableRows - 1) % count($palettes)];
?>
                <tr data-contact-index="<?php echo $idx; ?>" style="border-bottom:1px solid #f1f5f9;">
                  <td style="padding:14px 18px;">
                    <div class="d-flex align-items-center">
                      <span style="background-color:<?php echo $pal['bg']; ?>; color:<?php echo $pal['text']; ?>; display:inline-flex; align-items:center; justify-content:center; font-weight:700; width:34px; height:34px; border-radius:50%; margin-right:10px; font-size:12.5px; flex-shrink:0;"><?php echo htmlspecialchars($initials); ?></span>
                      <span style="font-weight:600; color:#1e293b; font-size:13.5px;"><?php echo htmlspecialchars($cName); ?></span>
                    </div>
                  </td>
                  <td style="padding:14px 14px; color:#334155; font-size:13px;"><?php echo htmlspecialchars($cDesig ?: '—'); ?></td>
                  <td style="padding:14px 14px; color:#334155; font-size:13px;"><?php echo htmlspecialchars($cEmail ?: '—'); ?></td>
                  <td style="padding:14px 14px; color:#334155; font-size:13px; font-weight:500;"><?php echo htmlspecialchars($cNumber ?: '—'); ?></td>
                  <td style="padding:14px 10px;"><?php echo $typeBadge; ?></td>
                  <td style="padding:14px 18px; text-align:right;">
                    <div class="d-inline-flex align-items-center gap-1 justify-content-end">
                      <button type="button" class="btn btn-icon client-edit-contact" data-index="<?php echo $idx; ?>" onclick="window.openContactDrawer ? window.openContactDrawer(<?php echo $idx; ?>) : false;" title="Edit" style="width:30px; height:30px; border-radius:6px; background:#eff6ff; border:1px solid #bfdbfe; color:#2563eb; display:inline-flex; align-items:center; justify-content:center; padding:0;">
                        <i class="la la-pencil" style="font-size:14px; color:#2563eb;"></i>
                      </button>
                      <button type="button" class="btn btn-icon client-delete-contact" data-index="<?php echo $idx; ?>" onclick="window.deleteContactRow ? window.deleteContactRow(<?php echo $idx; ?>) : false;" title="Delete" style="width:30px; height:30px; border-radius:6px; background:#fef2f2; border:1px solid #fecaca; color:#dc2626; display:inline-flex; align-items:center; justify-content:center; padding:0;">
                        <i class="la la-trash" style="font-size:14px; color:#dc2626;"></i>
                      </button>
                    </div>
                  </td>
                </tr>
<?php
    }
}
?>
                <!-- Empty state row matching media_1792065553406.png -->
                <tr id="client_contact_empty_row" style="<?php echo $renderedTableRows > 0 ? 'display:none;' : ''; ?>">
                  <td colspan="6" class="text-center py-10" style="background:#ffffff; border:none;">
                    <div style="width:64px; height:64px; border-radius:50%; background:#f1f5f9; display:inline-flex; align-items:center; justify-content:center; margin-bottom:12px;">
                      <i class="la la-users" style="font-size:28px; color:#64748b;"></i>
                    </div>
                    <h5 style="font-size:15px; font-weight:700; color:#1e293b; margin-bottom:4px;">No contact person added yet.</h5>
                    <p style="font-size:12.5px; color:#64748b; margin-bottom:16px;">Click "Add Contact Person" to begin adding key contacts for this client.</p>
                    <button type="button" class="btn btn-warning" onclick="window.openContactDrawer ? window.openContactDrawer(null) : false;" style="background:#ffd428 !important; border:none !important; color:#1e293b !important; font-weight:700 !important; font-size:13px !important; padding:9px 20px !important; border-radius:8px !important; display:inline-flex; align-items:center; gap:6px;">
                      <i class="la la-plus" style="font-size:14px; font-weight:bold; color:#1e293b;"></i> Add Contact Person
                    </button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <!-- Blue info banner matching screenshots -->
      <div class="client-info-banner mb-3" style="background:#eff6ff; border:1px solid #dbeafe; border-radius:8px; padding:12px 16px; display:flex; align-items:center; gap:10px; color:#1e40af; font-size:12.5px;">
        <i class="la la-info-circle" style="font-size:18px; color:#3b82f6; flex-shrink:0;"></i>
        <span>The first contact person is set as the primary contact by default.</span>
      </div>

      <!-- Yellow minimum 1 warning banner matching screenshots (shown only when empty) -->
      <div class="client-warning-banner mb-4" id="client_contact_min_warning" style="background:#fefce8; border:1px solid #fef08a; border-radius:8px; padding:12px 16px; display:<?php echo $renderedTableRows > 0 ? 'none' : 'flex'; ?>; align-items:center; gap:10px; color:#854d0e; font-size:12.5px;">
        <i class="la la-star-o" style="font-size:18px; color:#ca8a04; flex-shrink:0;"></i>
        <span>Minimum 1 Contact Person is required. Please click "Add Contact Person" to add details.</span>
      </div>

      <!-- Hidden standard form inputs synchronized for submission -->
      <div class="client-contact-fields">
        <div class="row client-cperson-row">
<?php
if ($page_event == 'EDIT' && $hasContacts) {
    foreach ($contactsList as $ccArr) {
        $cName = is_object($ccArr) ? ($ccArr->contact_person_name ?? '') : ($ccArr['contact_person_name'] ?? '');
        $cEmail = is_object($ccArr) ? ($ccArr->contact_person_email ?? '') : ($ccArr['contact_person_email'] ?? '');
        $cNumber = is_object($ccArr) ? ($ccArr->contact_person_number ?? '') : ($ccArr['contact_person_number'] ?? '');
        $cCalling = is_object($ccArr) ? ($ccArr->calling_code ?? '') : ($ccArr['calling_code'] ?? '');
        $cCalling = !empty($cCalling) ? $cCalling : ($country_code ?? '+91');
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
            <div class="col-xl-3"><div class="form-group mb-0 client-cperson-container-child"><input type="number" name="cp_number[]" id="cp_number_0" class="form-control form-control-solid" placeholder="Enter contact number"><input type="hidden" value="{{ $country_code ?? '+91' }}" class="calling_code" name="calling_code[]" id="calling_code_0"></div></div>
            <div class="col-xl-3"><div class="form-group mb-0 client-cperson-container-child"><input type="text" name="cp_desigination[]" id="cp_desigination_0" class="form-control form-control-solid cp_desigination" placeholder="Enter desigination"></div></div>
          </div>
<?php } ?>
        </div>
      </div>
      <input type="hidden" name="contactPersonId" id="contactPersonId" value="<?php echo @$contactPersonId; ?>" />
      <button type="button" class="add_cperson d-none" aria-hidden="true"></button>
      <button type="button" class="remove_cperson d-none" aria-hidden="true"></button>
    </div>

    <!-- Right Sidebar matching screenshots (switches dynamically between empty & populated states) -->
    <aside class="client-side-column">
      <!-- Empty State Sidebar (Matches media_1792065553406.png) -->
      <div id="client_contact_empty_sidebar" style="<?php echo $renderedTableRows > 0 ? 'display:none;' : 'display:block;'; ?>">
        <!-- AI Contact Assistant Card -->
        <div class="client-helper-card mb-4" style="background:#f5f3ff; border:1px solid #ede9fe; border-radius:12px; padding:16px;">
          <div class="d-flex align-items-center justify-content-between mb-2">
            <div class="d-flex align-items-center gap-2">
              <div style="width:30px; height:30px; border-radius:8px; background:#ede9fe; display:inline-flex; align-items:center; justify-content:center; color:#7e22ce;">
                <i class="la la-magic" style="font-size:16px; color:#7e22ce;"></i>
              </div>
              <h4 style="font-size:13.5px; font-weight:700; color:#1e1b4b; margin:0;">AI Contact Assistant</h4>
            </div>
            <span class="badge" style="background:#ede9fe; color:#7e22ce; font-weight:600; font-size:10.5px; padding:3px 8px; border-radius:6px;">Beta</span>
          </div>
          <p style="font-size:11.5px; color:#6b7280; line-height:1.45; margin-bottom:14px;">We can suggest key contacts based on the company website and public data.</p>
          
          <div style="font-size:11.5px; font-weight:700; color:#374151; margin-bottom:8px;">Suggested Contacts</div>
          
          <!-- Suggested Contact Card (Rahul Sharma) -->
          <div style="background:#ffffff; border:1px solid #ede9fe; border-radius:10px; padding:12px; margin-bottom:12px;">
            <div class="d-flex align-items-start justify-content-between">
              <div class="d-flex align-items-center gap-2">
                <div style="width:34px; height:34px; border-radius:50%; background:#ede9fe; color:#7e22ce; display:inline-flex; align-items:center; justify-content:center; font-weight:700; font-size:12px; flex-shrink:0;">RS</div>
                <div>
                  <div style="font-size:12.5px; font-weight:700; color:#111827;">Rahul Sharma</div>
                  <div style="font-size:11px; color:#6b7280;">HR Manager</div>
                  <div style="font-size:11px; color:#6b7280;">rahul.sharma@4bell.com</div>
                  <div style="font-size:11px; color:#6b7280;">+91 98765 43210</div>
                </div>
              </div>
              <button type="button" class="btn btn-sm" onclick="window.addSuggestedContact ? window.addSuggestedContact() : false;" style="background:#faf5ff; border:1px solid #d8b4fe; color:#7e22ce; font-weight:600; font-size:11.5px; padding:5px 10px; border-radius:6px; white-space:nowrap; display:inline-flex; align-items:center; gap:4px;">
                <i class="la la-plus-circle" style="font-size:14px;"></i> Add to List
              </button>
            </div>
          </div>
          
          <a href="javascript:void(0);" onclick="window.fetchCompanyContacts ? window.fetchCompanyContacts() : false;" style="font-size:11.5px; color:#2563eb; font-weight:600; text-decoration:none; display:inline-flex; align-items:center; gap:4px;">
            <i class="la la-globe"></i> Find more contacts from website &rarr;
          </a>
        </div>

        <!-- Best Practices Card (Empty state) -->
        <div class="client-helper-card" style="background:#f0fdf4; border:1px solid #bbf7d0; border-radius:12px; padding:16px;">
          <div class="d-flex align-items-center gap-2 mb-3">
            <div style="width:30px; height:30px; border-radius:8px; background:#dcfce7; display:inline-flex; align-items:center; justify-content:center; color:#166534;">
              <i class="la la-lightbulb-o" style="font-size:16px; color:#166534;"></i>
            </div>
            <h4 style="font-size:13.5px; font-weight:700; color:#14532d; margin:0;">Best Practices</h4>
          </div>
          <ul style="list-style:none; padding:0; margin:0;">
            <li style="display:flex; align-items:flex-start; gap:8px; font-size:11.5px; color:#166534; margin-bottom:8px;">
              <i class="la la-check-circle" style="font-size:16px; color:#16a34a; margin-top:1px;"></i>
              <span>Ensure email and phone number are valid.</span>
            </li>
            <li style="display:flex; align-items:flex-start; gap:8px; font-size:11.5px; color:#166534; margin-bottom:8px;">
              <i class="la la-check-circle" style="font-size:16px; color:#16a34a; margin-top:1px;"></i>
              <span>Add HR or hiring manager as primary contact.</span>
            </li>
            <li style="display:flex; align-items:flex-start; gap:8px; font-size:11.5px; color:#166534; margin-bottom:8px;">
              <i class="la la-check-circle" style="font-size:16px; color:#16a34a; margin-top:1px;"></i>
              <span>Include designation for better context.</span>
            </li>
            <li style="display:flex; align-items:flex-start; gap:8px; font-size:11.5px; color:#166534;">
              <i class="la la-check-circle" style="font-size:16px; color:#16a34a; margin-top:1px;"></i>
              <span>You can add multiple contact persons.</span>
            </li>
          </ul>
        </div>
      </div>

      <!-- Populated State Sidebar (Matches media_1792065655079.png) -->
      <div id="client_contact_populated_sidebar" style="<?php echo $renderedTableRows > 0 ? 'display:block;' : 'display:none;'; ?>">
        <!-- Why add Contact Person? Card -->
        <div class="client-helper-card mb-4" style="background:#f5f3ff; border:1px solid #ede9fe; border-radius:12px; padding:16px;">
          <div class="d-flex align-items-center gap-2 mb-3">
            <div style="width:30px; height:30px; border-radius:8px; background:#ede9fe; display:inline-flex; align-items:center; justify-content:center; color:#7e22ce;">
              <i class="la la-lightbulb-o" style="font-size:16px; color:#7e22ce;"></i>
            </div>
            <h4 style="font-size:13.5px; font-weight:700; color:#1e1b4b; margin:0;">Why add Contact Person?</h4>
          </div>
          <ul style="list-style:none; padding:0; margin:0;">
            <li style="display:flex; align-items:flex-start; gap:8px; font-size:11.5px; color:#4338ca; margin-bottom:10px;">
              <i class="la la-check-circle" style="font-size:16px; color:#16a34a; margin-top:1px;"></i>
              <span>Quick communication for job requirements.</span>
            </li>
            <li style="display:flex; align-items:flex-start; gap:8px; font-size:11.5px; color:#4338ca; margin-bottom:10px;">
              <i class="la la-check-circle" style="font-size:16px; color:#16a34a; margin-top:1px;"></i>
              <span>Track interactions and follow-ups easily.</span>
            </li>
            <li style="display:flex; align-items:flex-start; gap:8px; font-size:11.5px; color:#4338ca;">
              <i class="la la-check-circle" style="font-size:16px; color:#16a34a; margin-top:1px;"></i>
              <span>Assign primary and secondary contacts.</span>
            </li>
          </ul>
        </div>

        <!-- Best Practices Card (Populated state) -->
        <div class="client-helper-card" style="background:#f0fdf4; border:1px solid #bbf7d0; border-radius:12px; padding:16px;">
          <div class="d-flex align-items-center gap-2 mb-3">
            <div style="width:30px; height:30px; border-radius:8px; background:#dcfce7; display:inline-flex; align-items:center; justify-content:center; color:#166534;">
              <i class="la la-cog" style="font-size:16px; color:#166534;"></i>
            </div>
            <h4 style="font-size:13.5px; font-weight:700; color:#14532d; margin:0;">Best Practices</h4>
          </div>
          <ul style="list-style:none; padding:0; margin:0;">
            <li style="display:flex; align-items:flex-start; gap:8px; font-size:11.5px; color:#166534; margin-bottom:8px;">
              <i class="la la-check-circle" style="font-size:16px; color:#16a34a; margin-top:1px;"></i>
              <span>Ensure email and phone number are valid.</span>
            </li>
            <li style="display:flex; align-items:flex-start; gap:8px; font-size:11.5px; color:#166534; margin-bottom:8px;">
              <i class="la la-check-circle" style="font-size:16px; color:#16a34a; margin-top:1px;"></i>
              <span>Add HR or hiring manager as primary contact.</span>
            </li>
            <li style="display:flex; align-items:flex-start; gap:8px; font-size:11.5px; color:#166534; margin-bottom:8px;">
              <i class="la la-check-circle" style="font-size:16px; color:#16a34a; margin-top:1px;"></i>
              <span>Include designation for context.</span>
            </li>
            <li style="display:flex; align-items:flex-start; gap:8px; font-size:11.5px; color:#166534;">
              <i class="la la-check-circle" style="font-size:16px; color:#16a34a; margin-top:1px;"></i>
              <span>You can add multiple contact persons.</span>
            </li>
          </ul>
        </div>
      </div>
    </aside>
  </div>
</div>

<!-- =====================================================================
     Add / Edit Contact Person Drawer with Global Mobile Number Component
     ===================================================================== -->
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

    <!-- Contact Name -->
    <div class="client-drawer-field">
      <label class="cdf-label" for="client_drawer_name">Contact Name<span class="cdf-required">*</span></label>
      <div class="cdf-input-wrap">
        <i class="la la-user cdf-input-icon" aria-hidden="true"></i>
        <input type="text" id="client_drawer_name" placeholder="Enter contact name" autocomplete="off">
      </div>
      <div class="cdf-error" id="cdf_err_name">Contact name is required.</div>
    </div>

    <!-- Designation -->
    <div class="client-drawer-field">
      <label class="cdf-label" for="client_drawer_designation">Designation<span class="cdf-required">*</span></label>
      <div class="cdf-input-wrap">
        <i class="la la-id-badge cdf-input-icon" aria-hidden="true"></i>
        <input type="text" id="client_drawer_designation" placeholder="Enter designation (e.g. HR Manager)" autocomplete="off">
      </div>
      <div class="cdf-error" id="cdf_err_designation">Designation is required.</div>
    </div>

    <!-- Email Address -->
    <div class="client-drawer-field">
      <label class="cdf-label" for="client_drawer_email">Email Address<span class="cdf-required">*</span></label>
      <div class="cdf-input-wrap">
        <i class="la la-envelope cdf-input-icon" aria-hidden="true"></i>
        <input type="email" id="client_drawer_email" placeholder="name@company.com" autocomplete="off">
      </div>
      <div class="cdf-error" id="cdf_err_email">Please enter a valid email address.</div>
    </div>

    <!-- Global Mobile Number Component Integration -->
    <div class="client-drawer-field mb-4">
      <x-jero-phone-input
        id="client_drawer_phone"
        name="client_drawer_phone"
        countryCodeName="client_drawer_calling_code"
        countryCodeId="client_drawer_calling_code"
        label="Phone Number"
        placeholder="Enter mobile number"
      />
      <div class="cdf-error" id="cdf_err_phone">Please enter a valid phone number.</div>
    </div>
  </div>

  <!-- Footer -->
  <div class="client-drawer-foot">
    <button type="button" class="cdf-cancel-btn" id="client_cancel_contact_drawer" onclick="window.closeContactDrawer ? window.closeContactDrawer() : false;">Cancel</button>
    <button type="button" class="btn btn-warning cdf-save-btn" id="client_save_contact_drawer" onclick="window.saveContactDrawer ? window.saveContactDrawer() : false;" style="background:#ffd428 !important; border:none !important; color:#1e293b !important; font-weight:700 !important; font-size:13px !important; padding:10px 22px !important; border-radius:8px !important;">
      <span class="cdf-spinner"></span>
      <i class="la la-user-plus cdf-btn-icon" aria-hidden="true" style="color:#1e293b; font-size:15px; margin-right:4px;"></i>
      <span class="cdf-btn-text">Add Contact Person</span>
    </button>
  </div>
</div>
<!--end::Contact Person Step-->
