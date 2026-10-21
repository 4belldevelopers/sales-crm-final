<!--begin::Client Contacts Step (Clean Enterprise Full-Width Management)-->
@php
    $contactsList = !empty($client_contact_arr) && (is_array($client_contact_arr) || $client_contact_arr instanceof \Countable)
        ? $client_contact_arr
        : (!empty($client_contact_info) && (is_array($client_contact_info) || $client_contact_info instanceof \Countable)
            ? $client_contact_info
            : []);

    $initialContacts = [];
    $seenInitialEmails = [];
    if (($page_event ?? '') !== 'ADD' && !empty($contactsList) && count($contactsList) > 0) {
        foreach ($contactsList as $idx => $cc) {
            $name = is_object($cc) ? ($cc->contact_person_name ?? '') : ($cc['contact_person_name'] ?? '');
            $email = is_object($cc) ? ($cc->contact_person_email ?? '') : ($cc['contact_person_email'] ?? '');
            $phone = is_object($cc) ? ($cc->contact_person_number ?? '') : ($cc['contact_person_number'] ?? '');
            $calling = is_object($cc) ? ($cc->calling_code ?? '+91') : ($cc['calling_code'] ?? '+91');
            $desig = is_object($cc) ? ($cc->contact_person_designation ?? '') : ($cc['contact_person_designation'] ?? '');
            
            $cleanEmail = strtolower(trim($email));
            if (trim($name) === '' && $cleanEmail === '') continue;
            
            // Prevent duplicate emails from initial dataset
            if ($cleanEmail !== '' && isset($seenInitialEmails[$cleanEmail])) {
                continue;
            }
            if ($cleanEmail !== '') {
                $seenInitialEmails[$cleanEmail] = true;
            }

            $initialContacts[] = [
                'name' => trim($name),
                'email' => trim($email),
                'phone' => trim($phone),
                'callingCode' => trim($calling) ?: '+91',
                'designation' => trim($desig),
                'isPrimary' => count($initialContacts) === 0,
            ];
        }
    }
@endphp

<div class="pb-5 client-contact-step" data-wizard-type="step-content">
  <div class="client-step-layout client-contact-layout">

    <!-- Client Contacts Table & Management (Full Width Card) -->
    <div class="client-main-column w-100">
      <div class="card client-contact-main-card" style="background:#ffffff; border:1px solid #e2e8f0; border-radius:12px; box-shadow:0 1px 3px rgba(0,0,0,0.03); overflow:hidden;">
        
        <!-- Header bar -->
        <div class="card-header border-0 py-4 px-6 d-flex align-items-center justify-content-between" style="background:#ffffff; border-bottom:1px solid #f1f5f9 !important;">
          <div class="d-flex align-items-center">
            <div style="width:38px; height:38px; border-radius:10px; background:#fef9c3; display:inline-flex; align-items:center; justify-content:center; color:#ca8a04; margin-right:12px; flex-shrink:0;">
              <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#ca8a04" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                <circle cx="9" cy="7" r="4"></circle>
                <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
              </svg>
            </div>
            <div>
              <h3 style="font-size:14px; font-weight:700; color:#101828; margin:0 0 2px 0;">Client Contacts</h3>
              <p style="font-size:11.5px; color:#64748b; margin:0;">Add key stakeholders and recruitment team contacts you work with at this client organization.</p>
            </div>
          </div>
          <button type="button" class="btn client-add-contact-btn" id="client_open_contact_drawer" onclick="window.clientContactManager ? window.clientContactManager.openAddDrawer() : (window.openContactDrawer ? window.openContactDrawer(null) : false);" style="background:#f59e0b !important; border:none !important; color:#ffffff !important; font-weight:700 !important; font-size:12.5px !important; padding:9px 20px !important; border-radius:8px !important; box-shadow:0 1px 3px rgba(245,158,11,0.25) !important; display:inline-flex; align-items:center; gap:6px; cursor:pointer;">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
              <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path>
              <circle cx="9" cy="7" r="4"></circle>
              <line x1="19" y1="8" x2="19" y2="14"></line>
              <line x1="22" y1="11" x2="16" y2="11"></line>
            </svg>
            <span>Add Contact</span>
          </button>
        </div>

        <!-- Contacts Table -->
        <div class="card-body p-0">
          <div class="table-responsive client-contact-table-wrap" style="width:100%; overflow-x:auto;">
            <table class="table align-middle mb-0" id="client_contact_table" style="width:100%; border-collapse:collapse; table-layout:fixed;">
              <thead>
                <tr style="background:#f8fafc; border-bottom:1px solid #e2e8f0; color:#64748b; font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:0.5px;">
                  <th style="padding:12px 20px; width:24%; white-space:nowrap;">CONTACT</th>
                  <th style="padding:12px 16px; width:18%; white-space:nowrap;">DESIGNATION</th>
                  <th style="padding:12px 16px; width:24%; white-space:nowrap;">EMAIL</th>
                  <th style="padding:12px 16px; width:16%; white-space:nowrap;">PHONE NUMBER</th>
                  <th style="padding:12px 14px; width:10%; text-align:center; white-space:nowrap;">PRIMARY</th>
                  <th style="padding:12px 20px; width:8%; text-align:right; white-space:nowrap;">ACTIONS</th>
                </tr>
              </thead>
              <tbody id="client_contact_table_body">
                <!-- Rendered dynamically via window.clientContactManager -->
                <tr id="client_contact_empty_row" style="display:none;">
                  <td colspan="6" class="text-center" style="background:#ffffff; border:none; padding:48px 20px;">
                    <div style="width:56px; height:56px; border-radius:50%; background:#f8fafc; border:1px solid #e2e8f0; display:inline-flex; align-items:center; justify-content:center; margin-bottom:14px; color:#94a3b8;">
                      <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                        <circle cx="9" cy="7" r="4"></circle>
                        <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                        <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                      </svg>
                    </div>
                    <h5 style="font-size:13.5px; font-weight:700; color:#1e293b; margin-bottom:6px;">No client contacts added yet</h5>
                    <p style="font-size:11.5px; color:#64748b; margin-bottom:18px; max-width:440px; margin-left:auto; margin-right:auto;">Add key stakeholders and recruitment team contacts you work with at this client organization.</p>
                    <button type="button" class="btn btn-warning" onclick="window.clientContactManager ? window.clientContactManager.openAddDrawer() : (window.openContactDrawer ? window.openContactDrawer(null) : false);" style="background:#f59e0b; border:none; color:#ffffff; font-weight:700; font-size:12px; padding:9px 22px; border-radius:8px; cursor:pointer; display:inline-flex; align-items:center; gap:6px; box-shadow:0 1px 3px rgba(245,158,11,0.25);">
                      <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                      <span>Add First Contact</span>
                    </button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <!-- Bottom informational banner -->
        <div class="p-4" style="background:#ffffff; border-top:1px solid #f1f5f9;">
          <div class="client-contacts-info-banner" style="background:#eff6ff; border:1px solid #dbeafe; border-radius:8px; padding:10px 14px; display:flex; align-items:center; gap:10px; color:#1e40af; font-size:12px;">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0;">
              <circle cx="12" cy="12" r="10"></circle>
              <line x1="12" y1="16" x2="12" y2="12"></line>
              <line x1="12" y1="8" x2="12.01" y2="8"></line>
            </svg>
            <span>The first contact is set as the primary contact by default. You can change the primary contact anytime.</span>
          </div>
        </div>
      </div>

      <!-- Hidden standard form inputs synchronized for submission -->
      <div class="client-contact-fields d-none" id="client_contact_hidden_fields">
        <div class="row client-cperson-row">
          <!-- Populated dynamically by window.clientContactManager.syncHiddenInputs() -->
        </div>
      </div>
      <input type="hidden" name="contactPersonId" id="contactPersonId" value="0" />
    </div>

  </div>
</div>

<!-- =====================================================================
     Add / Edit Contact Person Quick Drawer
     ===================================================================== -->
<div class="client-contact-drawer-backdrop" id="client_contact_drawer_backdrop" onclick="window.closeContactDrawer ? window.closeContactDrawer() : false;"></div>
<div class="client-contact-drawer" id="client_contact_drawer" aria-hidden="true" role="dialog" aria-modal="true">
  <!-- Header -->
  <div class="client-drawer-head">
    <div class="client-drawer-head-info">
      <h4 id="client_contact_drawer_title" style="font-size:14px; font-weight:700; color:#101828; margin:0 0 2px 0;">Add Contact Person</h4>
      <p class="client-drawer-head-subtitle" id="client_contact_drawer_subtitle" style="font-size:11.5px; color:#64748b; margin:0;">Add key contacts for recruitment operations with this client.</p>
    </div>
    <button type="button" class="client-drawer-close-btn" id="client_close_contact_drawer" onclick="window.closeContactDrawer ? window.closeContactDrawer() : false;" title="Close" aria-label="Close drawer">
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
    </button>
  </div>

  <!-- Body -->
  <div class="client-drawer-body">
    <input type="hidden" id="client_drawer_edit_index" value="">

    <!-- Contact Name -->
    <div class="client-drawer-field mb-3">
      <label class="cdf-label" for="client_drawer_name">Contact Name<span class="text-danger ms-1">*</span></label>
      <div class="cdf-input-wrap">
        <i class="la la-user cdf-input-icon" aria-hidden="true"></i>
        <input type="text" id="client_drawer_name" placeholder="Enter contact name (e.g. John Smith)" autocomplete="off">
      </div>
      <div class="cdf-error" id="cdf_err_name">Contact name is required.</div>
    </div>

    <!-- Designation -->
    <div class="client-drawer-field mb-3">
      <label class="cdf-label" for="client_drawer_designation">Designation<span class="text-danger ms-1">*</span></label>
      <div class="cdf-input-wrap">
        <i class="la la-id-badge cdf-input-icon" aria-hidden="true"></i>
        <input type="text" id="client_drawer_designation" placeholder="Enter designation (e.g. HR Manager, Talent Lead)" autocomplete="off">
      </div>
      <div class="cdf-error" id="cdf_err_designation">Designation is required.</div>
    </div>

    <!-- Email Address -->
    <div class="client-drawer-field mb-3">
      <label class="cdf-label" for="client_drawer_email">Email Address<span class="text-danger ms-1">*</span></label>
      <div class="cdf-input-wrap">
        <i class="la la-envelope cdf-input-icon" aria-hidden="true"></i>
        <input type="email" id="client_drawer_email" placeholder="name@company.com" maxlength="254" inputmode="email" autocomplete="email" autocapitalize="none" spellcheck="false">
      </div>
      <div id="client_drawer_email_typo_hint" class="client-email-typo-hint" style="display: none; margin-top: 5px; font-size: 11.5px; color: #b45309; background: #fef3c7; border: 1px solid #fde68a; border-radius: 6px; padding: 4px 8px;"></div>
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

    <!-- Primary Contact Selection Card (Enterprise Styled & 100% Visible) -->
    <div class="client-primary-contact-card" id="client_drawer_primary_box">
      <label class="client-primary-card-label" for="client_drawer_is_primary">
        <div class="client-primary-card-left">
          <div class="client-primary-star-icon">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#d97706" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
              <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
            </svg>
          </div>
          <div class="client-primary-card-texts">
            <span class="client-primary-card-title">Set as Primary Contact for this client</span>
            <span class="client-primary-card-subtitle">Primary contact receives official notifications and candidate submissions</span>
          </div>
        </div>
        <div class="client-primary-checkbox-wrapper">
          <input type="checkbox" id="client_drawer_is_primary" name="client_drawer_is_primary" value="1" class="client-primary-native-checkbox">
          <span class="client-primary-custom-box">
            <svg class="client-primary-check-icon" width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="#ffffff" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
              <polyline points="20 6 9 17 4 12"/>
            </svg>
          </span>
        </div>
      </label>
    </div>
  </div>

  <!-- Footer -->
  <div class="client-drawer-foot">
    <button type="button" class="cdf-cancel-btn" id="client_cancel_contact_drawer" onclick="window.closeContactDrawer ? window.closeContactDrawer() : false;">Cancel</button>
    <button type="button" class="btn btn-warning cdf-save-btn" id="client_save_contact_drawer" onclick="window.saveContactDrawer ? window.saveContactDrawer() : false;" style="background:#f59e0b !important; border:none !important; color:#ffffff !important; font-weight:700 !important; font-size:13px !important; padding:10px 22px !important; border-radius:8px !important;">
      <span class="cdf-spinner" style="display:none; width:14px; height:14px; border:2px solid #ffffff; border-top-color:transparent; border-radius:50%; animation:spin 0.6s linear infinite; margin-right:6px;"></span>
      <span class="cdf-btn-text">Save Contact</span>
    </button>
  </div>
</div>

<script>
  window.__INITIAL_CONTACTS__ = @json($initialContacts);
</script>

<style>
  /* --------------------------------------------------------------------------
     Client Contacts Full-Width Layout & 15.6" Screen Fit Rules (1366x768 & 1920x1080)
     -------------------------------------------------------------------------- */
  .client-contact-layout {
    display: block !important;
    width: 100% !important;
    max-width: 100% !important;
    margin: 0 !important;
  }

  .client-contact-main-card {
    width: 100% !important;
    max-width: 100% !important;
  }

  .client-contact-table-wrap {
    width: 100% !important;
    max-height: 520px;
    overflow-y: auto;
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
  }
  .client-contact-table-wrap::-webkit-scrollbar {
    width: 5px;
    height: 5px;
  }
  .client-contact-table-wrap::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 4px;
  }

  #client_contact_table th {
    white-space: nowrap !important;
  }

  .client-make-primary-btn:hover {
    color: #2563eb !important;
  }
  .client-make-primary-btn:hover .radio-circle {
    border-color: #2563eb !important;
  }

  /* Compact Laptop Screens (1366x768 & 1920x1080 with height <= 850px) */
  @media (max-height: 850px) {
    .client-contact-main-card .card-header {
      padding-top: 10px !important;
      padding-bottom: 10px !important;
    }
    .client-contact-table-wrap {
      max-height: 380px !important;
    }
    #client_contact_table th,
    #client_contact_table td {
      padding-top: 10px !important;
      padding-bottom: 10px !important;
    }
  }

  /* Responsive boundary for narrow viewports */
  @media (max-width: 1199.98px) {
    #client_contact_table {
      min-width: 720px !important;
    }
  }
</style>
<!--end::Client Contacts Step-->