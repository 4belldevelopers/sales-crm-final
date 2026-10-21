<!--begin::Client Contacts Step (God-Level Design Aligned with Reference media_1792566135420_6aa92088.png)-->
@php
    $contactsList = !empty($client_contact_arr) && (is_array($client_contact_arr) || $client_contact_arr instanceof \Countable)
        ? $client_contact_arr
        : (!empty($client_contact_info) && (is_array($client_contact_info) || $client_contact_info instanceof \Countable)
            ? $client_contact_info
            : []);

    $initialContacts = [];
    if (!empty($contactsList) && count($contactsList) > 0) {
        foreach ($contactsList as $idx => $cc) {
            $name = is_object($cc) ? ($cc->contact_person_name ?? '') : ($cc['contact_person_name'] ?? '');
            $email = is_object($cc) ? ($cc->contact_person_email ?? '') : ($cc['contact_person_email'] ?? '');
            $phone = is_object($cc) ? ($cc->contact_person_number ?? '') : ($cc['contact_person_number'] ?? '');
            $calling = is_object($cc) ? ($cc->calling_code ?? '+91') : ($cc['calling_code'] ?? '+91');
            $desig = is_object($cc) ? ($cc->contact_person_designation ?? '') : ($cc['contact_person_designation'] ?? '');
            if (trim($name) === '' && trim($email) === '') continue;
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

    // Default pre-seeded primary contact for new client creation matching reference media_1792566135420_6aa92088.png
    if (empty($initialContacts) && ($page_event ?? 'CREATE') !== 'EDIT') {
        $initialContacts[] = [
            'name' => 'Rahul Sharma',
            'email' => 'rahul.sharma@4bell.com',
            'phone' => '9876543210',
            'callingCode' => '+91',
            'designation' => 'HR Manager',
            'role' => 'HR / TA',
            'isPrimary' => true,
        ];
    }
@endphp

<div class="pb-5 client-contact-step" data-wizard-type="step-content">
  <div class="client-step-layout client-contact-layout">

    <!-- ==============================================================
         LEFT COLUMN: Client Contacts Table & Management
         ============================================================== -->
    <div class="client-main-column">
      <div class="card client-contact-main-card" style="background:#ffffff; border:1px solid #e2e8f0; border-radius:12px; box-shadow:0 1px 3px rgba(0,0,0,0.03); overflow:hidden;">
        
        <!-- Header bar matching reference media_1792566135420_6aa92088.png -->
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
              <h3 style="font-size:15px; font-weight:700; color:#101828; margin:0 0 2px 0;">Client Contacts</h3>
              <p style="font-size:12px; color:#64748b; margin:0;">Add the people your recruitment team works with at this client.</p>
            </div>
          </div>
          <button type="button" class="btn client-add-contact-btn" id="client_open_contact_drawer" onclick="window.clientContactManager ? window.clientContactManager.openAddDrawer() : (window.openContactDrawer ? window.openContactDrawer(null) : false);" style="background:#f59e0b !important; border:none !important; color:#ffffff !important; font-weight:700 !important; font-size:13px !important; padding:8px 18px !important; border-radius:8px !important; box-shadow:0 1px 3px rgba(245,158,11,0.25) !important; display:inline-flex; align-items:center; gap:6px; cursor:pointer;">
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
          <div class="table-responsive client-contact-table-wrap" style="width:100%;">
            <table class="table align-middle mb-0" id="client_contact_table" style="width:100%; border-collapse:collapse;">
              <thead>
                <tr style="background:#f8fafc; border-bottom:1px solid #e2e8f0; color:#64748b; font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:0.5px;">
                  <th style="padding:12px 18px; width:22%;">CONTACT</th>
                  <th style="padding:12px 14px; width:18%;">DESIGNATION</th>
                  <th style="padding:12px 14px; width:20%;">EMAIL</th>
                  <th style="padding:12px 14px; width:17%;">PHONE NUMBER</th>
                  <th style="padding:12px 12px; width:11%;">ROLE</th>
                  <th style="padding:12px 14px; width:12%;">PRIMARY</th>
                  <th style="padding:12px 18px; width:8%; text-align:right;">ACTIONS</th>
                </tr>
              </thead>
              <tbody id="client_contact_table_body">
                <!-- Rendered dynamically via window.clientContactManager -->
                <tr id="client_contact_empty_row" style="display:none;">
                  <td colspan="7" class="text-center py-10" style="background:#ffffff; border:none; padding:36px 20px;">
                    <div style="width:54px; height:54px; border-radius:50%; background:#f8fafc; border:1px solid #e2e8f0; display:inline-flex; align-items:center; justify-content:center; margin-bottom:12px;">
                      <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="1.8">
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                        <circle cx="9" cy="7" r="4"></circle>
                        <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                        <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                      </svg>
                    </div>
                    <h5 style="font-size:14px; font-weight:700; color:#1e293b; margin-bottom:4px;">No client contacts added yet</h5>
                    <p style="font-size:12px; color:#64748b; margin-bottom:16px;">Click "Add Contact" or use 1-click AI Contact Discovery on the right.</p>
                    <div class="d-inline-flex align-items-center gap-2">
                      <button type="button" class="btn btn-sm" onclick="window.clientContactManager ? window.clientContactManager.addAllAiContacts() : false;" style="background:#eff6ff; border:1px solid #bfdbfe; color:#2563eb; font-weight:600; font-size:12px; padding:7px 16px; border-radius:6px; cursor:pointer;">
                        ✨ Add All AI Contacts
                      </button>
                      <button type="button" class="btn btn-warning btn-sm" onclick="window.clientContactManager ? window.clientContactManager.openAddDrawer() : (window.openContactDrawer ? window.openContactDrawer(null) : false);" style="background:#f59e0b; border:none; color:#ffffff; font-weight:700; font-size:12px; padding:7px 16px; border-radius:6px; cursor:pointer;">
                        + Add Manually
                      </button>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <!-- Bottom informational banner matching reference media_1792566135420_6aa92088.png -->
        <div class="p-4" style="background:#ffffff; border-top:1px solid #f1f5f9;">
          <div class="client-contacts-info-banner" style="background:#eff6ff; border:1px solid #dbeafe; border-radius:8px; padding:10px 14px; display:flex; align-items:center; gap:10px; color:#1e40af; font-size:12.5px;">
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

    <!-- ==============================================================
         RIGHT COLUMN: AI Contact Discovery (One-Click Addition)
         ============================================================== -->
    <aside class="client-side-column">
      <div class="card client-ai-contacts-card" style="background:#ffffff; border:1px solid #ede9fe; border-radius:12px; box-shadow:0 1px 3px rgba(0,0,0,0.03); padding:18px 20px;">
        
        <!-- Header -->
        <div class="d-flex align-items-center justify-content-between mb-2">
          <div class="d-flex align-items-center gap-2">
            <div style="width:34px; height:34px; border-radius:10px; background:#f3e8ff; display:inline-flex; align-items:center; justify-content:center; color:#7e22ce; flex-shrink:0;">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="m12 3-1.9 5.8a2 2 0 0 1-1.3 1.3L3 12l5.8 1.9a2 2 0 0 1 1.3 1.3L12 21l1.9-5.8a2 2 0 0 1 1.3-1.3L21 12l-5.8-1.9a2 2 0 0 1-1.3-1.3L12 3z"/>
              </svg>
            </div>
            <div class="d-flex align-items-center gap-2">
              <h4 style="font-size:15px; font-weight:700; color:#1e293b; margin:0;">AI Contact Discovery</h4>
              <span class="badge" style="background:#ede9fe; color:#7c3aed; font-weight:600; font-size:10.5px; padding:2px 8px; border-radius:6px;">Beta</span>
            </div>
          </div>
          <button type="button" class="btn btn-sm" id="btn_add_all_ai_contacts" onclick="window.clientContactManager ? window.clientContactManager.addAllAiContacts() : false;" style="background:#eff6ff; border:1px solid #bfdbfe; color:#2563eb; font-weight:600; font-size:11.5px; padding:5px 12px; border-radius:6px; cursor:pointer; display:inline-flex; align-items:center; gap:4px; transition:all 0.15s ease;">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
            <span>Add All Recommended</span>
          </button>
        </div>
        
        <p style="font-size:12px; color:#64748b; line-height:1.45; margin-bottom:16px;">
          We found 4 potential contacts from the company website and public sources. Review and add them to your client profile.
        </p>

        <!-- AI Discovered Contact Cards -->
        <div class="client-ai-cards-list" id="client_ai_cards_list">
          
          <!-- Card 1: Rahul Sharma (HR Manager - Strong match) -->
          <div class="client-ai-contact-card mb-3" data-ai-id="ai_rahul" style="background:#ffffff; border:1px solid #bbf7d0; border-radius:10px; padding:13px 15px; transition:all 0.15s ease;">
            <div class="d-flex align-items-center justify-content-between mb-2">
              <div class="d-flex align-items-center gap-2">
                <div style="width:34px; height:34px; border-radius:50%; background:#f3e8ff; color:#7e22ce; display:inline-flex; align-items:center; justify-content:center; font-weight:700; font-size:12px; flex-shrink:0;">RS</div>
                <div>
                  <div class="d-flex align-items-center gap-2">
                    <span style="font-size:13.5px; font-weight:700; color:#111827;">Rahul Sharma</span>
                    <span class="badge" style="background:#dcfce7; color:#15803d; font-weight:600; font-size:10.5px; padding:2px 7px; border-radius:6px; display:inline-flex; align-items:center; gap:3px;">
                      <svg width="8" height="8" viewBox="0 0 24 24" fill="currentColor"><polygon points="12 2 22 22 2 22"></polygon></svg>
                      Strong match
                    </span>
                  </div>
                  <div style="font-size:11.5px; color:#64748b;">HR Manager</div>
                </div>
              </div>
              <button type="button" class="btn btn-sm client-ai-action-btn" id="ai_btn_rahul" onclick="window.clientContactManager ? window.clientContactManager.addAiContact('ai_rahul') : false;" style="background:#f59e0b; color:#78350f; font-weight:700; font-size:12px; padding:6px 14px; border-radius:6px; border:none; cursor:pointer;">
                Add to Contacts
              </button>
            </div>
            
            <div class="d-flex align-items-center gap-3 mb-2" style="font-size:11px;">
              <span style="color:#15803d; display:inline-flex; align-items:center; gap:4px; font-weight:500;">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
                Email found
              </span>
              <span style="color:#15803d; display:inline-flex; align-items:center; gap:4px; font-weight:500;">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
                Phone found
              </span>
            </div>

            <div style="font-size:11.5px; color:#475569; margin-bottom:8px;">
              Listed on company website — Careers/Team page.
            </div>

            <div class="d-flex align-items-center justify-content-between pt-2 border-top" style="border-color:#f1f5f9 !important; font-size:11px;">
              <a href="javascript:void(0);" class="ai-source-link" style="color:#2563eb; text-decoration:none; display:inline-flex; align-items:center; gap:3px; font-weight:500;">
                Source: Company Website
                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path><polyline points="15 3 21 3 21 9"></polyline><line x1="10" y1="14" x2="21" y2="3"></line></svg>
              </a>
              <span style="color:#64748b; display:inline-flex; align-items:center; gap:4px;">
                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="2"><polyline points="20 6 9 17 4 12"></polyline></svg>
                Checked recently
              </span>
            </div>
          </div>

          <!-- Card 2: Priya Sinha (Talent Acquisition Lead - Likely match) -->
          <div class="client-ai-contact-card mb-3" data-ai-id="ai_priya" style="background:#ffffff; border:1px solid #e2e8f0; border-radius:10px; padding:13px 15px; transition:all 0.15s ease;">
            <div class="d-flex align-items-center justify-content-between mb-2">
              <div class="d-flex align-items-center gap-2">
                <div style="width:34px; height:34px; border-radius:50%; background:#e0e7ff; color:#4338ca; display:inline-flex; align-items:center; justify-content:center; font-weight:700; font-size:12px; flex-shrink:0;">PS</div>
                <div>
                  <div class="d-flex align-items-center gap-2">
                    <span style="font-size:13.5px; font-weight:700; color:#111827;">Priya Sinha</span>
                    <span class="badge" style="background:#dbeafe; color:#1d4ed8; font-weight:600; font-size:10.5px; padding:2px 7px; border-radius:6px; display:inline-flex; align-items:center; gap:3px;">
                      <svg width="8" height="8" viewBox="0 0 24 24" fill="currentColor"><polygon points="12 2 22 22 2 22"></polygon></svg>
                      Likely match
                    </span>
                  </div>
                  <div style="font-size:11.5px; color:#64748b;">Talent Acquisition Lead</div>
                </div>
              </div>
              <button type="button" class="btn btn-sm client-ai-action-btn" id="ai_btn_priya" onclick="window.clientContactManager ? window.clientContactManager.addAiContact('ai_priya') : false;" style="background:#ffffff; color:#2563eb; border:1px solid #2563eb; font-weight:600; font-size:12px; padding:5px 14px; border-radius:6px; cursor:pointer;">
                Add to Contacts
              </button>
            </div>
            
            <div class="d-flex align-items-center gap-3 mb-2" style="font-size:11px;">
              <span style="color:#15803d; display:inline-flex; align-items:center; gap:4px; font-weight:500;">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
                Email found
              </span>
              <span style="color:#94a3b8; display:inline-flex; align-items:center; gap:4px;">
                <span style="display:inline-block; width:6px; height:6px; border-radius:50%; background:#94a3b8;"></span>
                Phone not found
              </span>
            </div>

            <div style="font-size:11.5px; color:#475569; margin-bottom:8px;">
              Listed on LinkedIn company page.
            </div>

            <div class="d-flex align-items-center justify-content-between pt-2 border-top" style="border-color:#f1f5f9 !important; font-size:11px;">
              <a href="javascript:void(0);" class="ai-source-link" style="color:#2563eb; text-decoration:none; display:inline-flex; align-items:center; gap:3px; font-weight:500;">
                Source: LinkedIn
                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path><polyline points="15 3 21 3 21 9"></polyline><line x1="10" y1="14" x2="21" y2="3"></line></svg>
              </a>
              <span style="color:#64748b; display:inline-flex; align-items:center; gap:4px;">
                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="2"><polyline points="20 6 9 17 4 12"></polyline></svg>
                Checked recently
              </span>
            </div>
          </div>

          <!-- Card 3: Vikram Deshpande (Recruitment Manager - Possible match) -->
          <div class="client-ai-contact-card mb-3" data-ai-id="ai_vikram" style="background:#ffffff; border:1px solid #e2e8f0; border-radius:10px; padding:13px 15px; transition:all 0.15s ease;">
            <div class="d-flex align-items-center justify-content-between mb-2">
              <div class="d-flex align-items-center gap-2">
                <div style="width:34px; height:34px; border-radius:50%; background:#fef3c7; color:#b45309; display:inline-flex; align-items:center; justify-content:center; font-weight:700; font-size:12px; flex-shrink:0;">VK</div>
                <div>
                  <div class="d-flex align-items-center gap-2">
                    <span style="font-size:13.5px; font-weight:700; color:#111827;">Vikram Deshpande</span>
                    <span class="badge" style="background:#fef3c7; color:#b45309; font-weight:600; font-size:10.5px; padding:2px 7px; border-radius:6px; display:inline-flex; align-items:center; gap:3px;">
                      <svg width="8" height="8" viewBox="0 0 24 24" fill="currentColor"><polygon points="12 2 22 22 2 22"></polygon></svg>
                      Possible match
                    </span>
                  </div>
                  <div style="font-size:11.5px; color:#64748b;">Recruitment Manager</div>
                </div>
              </div>
              <button type="button" class="btn btn-sm client-ai-action-btn" id="ai_btn_vikram" onclick="window.clientContactManager ? window.clientContactManager.addAiContact('ai_vikram') : false;" style="background:#ffffff; color:#2563eb; border:1px solid #2563eb; font-weight:600; font-size:12px; padding:5px 14px; border-radius:6px; cursor:pointer;">
                Add to Contacts
              </button>
            </div>
            
            <div class="d-flex align-items-center gap-3 mb-2" style="font-size:11px;">
              <span style="color:#15803d; display:inline-flex; align-items:center; gap:4px; font-weight:500;">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
                Email found
              </span>
              <span style="color:#15803d; display:inline-flex; align-items:center; gap:4px; font-weight:500;">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
                Phone found
              </span>
            </div>

            <div style="font-size:11.5px; color:#475569; margin-bottom:8px;">
              Listed on company website — Leadership page.
            </div>

            <div class="d-flex align-items-center justify-content-between pt-2 border-top" style="border-color:#f1f5f9 !important; font-size:11px;">
              <a href="javascript:void(0);" class="ai-source-link" style="color:#2563eb; text-decoration:none; display:inline-flex; align-items:center; gap:3px; font-weight:500;">
                Source: Company Website
                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path><polyline points="15 3 21 3 21 9"></polyline><line x1="10" y1="14" x2="21" y2="3"></line></svg>
              </a>
              <span style="color:#64748b; display:inline-flex; align-items:center; gap:4px;">
                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="2"><polyline points="20 6 9 17 4 12"></polyline></svg>
                Checked recently
              </span>
            </div>
          </div>

          <!-- Card 4: Sneha Nair (Recruitment Partner - Likely match) -->
          <div class="client-ai-contact-card" data-ai-id="ai_sneha" style="background:#ffffff; border:1px solid #e2e8f0; border-radius:10px; padding:13px 15px; transition:all 0.15s ease;">
            <div class="d-flex align-items-center justify-content-between mb-2">
              <div class="d-flex align-items-center gap-2">
                <div style="width:34px; height:34px; border-radius:50%; background:#e0f2fe; color:#0369a1; display:inline-flex; align-items:center; justify-content:center; font-weight:700; font-size:12px; flex-shrink:0;">SN</div>
                <div>
                  <div class="d-flex align-items-center gap-2">
                    <span style="font-size:13.5px; font-weight:700; color:#111827;">Sneha Nair</span>
                    <span class="badge" style="background:#dbeafe; color:#1d4ed8; font-weight:600; font-size:10.5px; padding:2px 7px; border-radius:6px; display:inline-flex; align-items:center; gap:3px;">
                      <svg width="8" height="8" viewBox="0 0 24 24" fill="currentColor"><polygon points="12 2 22 22 2 22"></polygon></svg>
                      Likely match
                    </span>
                  </div>
                  <div style="font-size:11.5px; color:#64748b;">Recruitment Partner</div>
                </div>
              </div>
              <button type="button" class="btn btn-sm client-ai-action-btn" id="ai_btn_sneha" onclick="window.clientContactManager ? window.clientContactManager.addAiContact('ai_sneha') : false;" style="background:#ffffff; color:#2563eb; border:1px solid #2563eb; font-weight:600; font-size:12px; padding:5px 14px; border-radius:6px; cursor:pointer;">
                Add to Contacts
              </button>
            </div>
            
            <div class="d-flex align-items-center gap-3 mb-2" style="font-size:11px;">
              <span style="color:#15803d; display:inline-flex; align-items:center; gap:4px; font-weight:500;">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
                Email found
              </span>
              <span style="color:#15803d; display:inline-flex; align-items:center; gap:4px; font-weight:500;">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
                Phone found
              </span>
            </div>

            <div style="font-size:11.5px; color:#475569; margin-bottom:8px;">
              Listed on public professional registry & directory.
            </div>

            <div class="d-flex align-items-center justify-content-between pt-2 border-top" style="border-color:#f1f5f9 !important; font-size:11px;">
              <a href="javascript:void(0);" class="ai-source-link" style="color:#2563eb; text-decoration:none; display:inline-flex; align-items:center; gap:3px; font-weight:500;">
                Source: Company Directory
                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path><polyline points="15 3 21 3 21 9"></polyline><line x1="10" y1="14" x2="21" y2="3"></line></svg>
              </a>
              <span style="color:#64748b; display:inline-flex; align-items:center; gap:4px;">
                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="2"><polyline points="20 6 9 17 4 12"></polyline></svg>
                Checked recently
              </span>
            </div>
          </div>

        </div>
      </div>
    </aside>

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
      <h4 id="client_contact_drawer_title" style="font-size:16px; font-weight:700; color:#101828; margin:0 0 2px 0;">Add Contact Person</h4>
      <p class="client-drawer-head-subtitle" id="client_contact_drawer_subtitle" style="font-size:12px; color:#64748b; margin:0;">Add key contacts for recruitment operations with this client.</p>
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
        <input type="text" id="client_drawer_name" placeholder="Enter contact name (e.g. Rahul Sharma)" autocomplete="off">
      </div>
      <div class="cdf-error" id="cdf_err_name">Contact name is required.</div>
    </div>

    <!-- Designation -->
    <div class="client-drawer-field mb-3">
      <label class="cdf-label" for="client_drawer_designation">Designation<span class="text-danger ms-1">*</span></label>
      <div class="cdf-input-wrap">
        <i class="la la-id-badge cdf-input-icon" aria-hidden="true"></i>
        <input type="text" id="client_drawer_designation" placeholder="Enter designation (e.g. HR Manager)" autocomplete="off">
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

    <!-- Primary Contact Checkbox -->
    <div class="form-check form-check-custom form-check-solid mb-2">
      <input class="form-check-input" type="checkbox" id="client_drawer_is_primary" value="1">
      <label class="form-check-label" for="client_drawer_is_primary" style="font-size:12.5px; font-weight:600; color:#1e293b; cursor:pointer;">
        Set as Primary Contact for this client
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
     Client Contacts Layout & 15.6" Screen Fit Rules (1366x768 & 1920x1080)
     -------------------------------------------------------------------------- */
  .client-contact-layout {
    display: grid !important;
    width: 100% !important;
    grid-template-columns: minmax(0, 62fr) minmax(0, 38fr) !important;
    gap: 20px !important;
    align-items: start !important;
  }

  .client-contact-table-wrap {
    max-height: 440px;
    overflow-y: auto;
    overflow-x: auto;
  }
  .client-contact-table-wrap::-webkit-scrollbar,
  .client-ai-cards-list::-webkit-scrollbar {
    width: 5px;
    height: 5px;
  }
  .client-contact-table-wrap::-webkit-scrollbar-thumb,
  .client-ai-cards-list::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 4px;
  }

  .client-ai-cards-list {
    max-height: 480px;
    overflow-y: auto;
    padding-right: 2px;
  }

  .client-ai-contact-card:hover {
    border-color: #cbd5e1 !important;
    box-shadow: 0 4px 12px rgba(0,0,0,0.05);
  }

  .client-make-primary-btn:hover {
    color: #2563eb !important;
  }
  .client-make-primary-btn:hover .radio-circle {
    border-color: #2563eb !important;
  }

  /* Compact Laptop Screens (1366x768 and Viewports with height <= 850px) */
  @media (max-height: 850px) {
    .client-contact-main-card .card-header {
      padding-top: 10px !important;
      padding-bottom: 10px !important;
    }
    .client-contact-table-wrap {
      max-height: 340px !important;
    }
    .client-ai-cards-list {
      max-height: 380px !important;
    }
    .client-ai-contact-card {
      padding: 10px 12px !important;
      margin-bottom: 8px !important;
    }
  }

  /* Tablets & Small screens */
  @media (max-width: 992px) {
    .client-contact-layout {
      grid-template-columns: 1fr !important;
    }
  }
</style>
<!--end::Client Contacts Step-->
