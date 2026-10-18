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
  <div class="client-step-layout client-contact-layout">
    <div class="client-main-column">
      <!-- Contact Person Card -->
      <div class="card client-contact-main-card mb-4" style="background:#ffffff; border:1.5px solid #F6DF83; border-radius:12px; box-shadow:0 1px 3px rgba(0,0,0,0.02); overflow:hidden;">
        <!-- Header bar matching screenshots -->
        <div class="card-header border-0 py-4 px-6 d-flex align-items-center justify-content-between" style="background:#ffffff; border-bottom:1px solid #f8fafc !important;">
          <div class="d-flex align-items-center">
            <div style="width:38px; height:38px; border-radius:10px; background:#fef9c3; display:inline-flex; align-items:center; justify-content:center; color:#ca8a04; margin-right:12px; flex-shrink:0;">
              <i class="la la-users" style="font-size:20px; color:#ca8a04;"></i>
            </div>
            <div>
              <h3 style="font-size:15px; font-weight:700; color:#101A46; margin:0 0 2px 0;">Contact Person</h3>
              <p style="font-size:12px; color:#5C6F9F; margin:0;">Add key contacts for this client organization.</p>
            </div>
          </div>
          <button type="button" class="btn btn-warning client-add-contact-btn" id="client_open_contact_drawer" style="background:#FFD21F !important; border:none !important; color:#101A46 !important; font-weight:700 !important; font-size:13px !important; padding:9px 18px !important; border-radius:8px !important; box-shadow:0 1px 3px rgba(255,210,31,0.3) !important; display:inline-flex; align-items:center; gap:6px;">
            <i class="la la-plus" style="font-size:14px; font-weight:bold; color:#101A46;"></i> Add Contact Person
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
$cIsoToDial = [
    'IN' => '+91', 'US' => '+1', 'GB' => '+44', 'AE' => '+971', 'CA' => '+1',
    'AU' => '+61', 'SG' => '+65', 'SA' => '+966', 'DE' => '+49', 'FR' => '+33',
    'QA' => '+974', 'KW' => '+965', 'OM' => '+968', 'BH' => '+973', 'MY' => '+60',
    'PH' => '+63', 'ID' => '+62', 'NZ' => '+64', 'ZA' => '+27', 'IE' => '+353',
    'NL' => '+31', 'SE' => '+46', 'CH' => '+41', 'ES' => '+34', 'IT' => '+39',
    'BR' => '+55', 'MX' => '+52', 'JP' => '+81', 'KR' => '+82', 'PK' => '+92',
    'BD' => '+880', 'LK' => '+94', 'NP' => '+977', 'NG' => '+234', 'KE' => '+254',
    'EG' => '+20', 'TR' => '+90', 'PL' => '+48', 'RO' => '+40', 'UA' => '+380',
    'VN' => '+84', 'TH' => '+66', 'HK' => '+852', 'TW' => '+886', 'IL' => '+972',
    'RU' => '+7', 'MV' => '+960', 'YE' => '+967', 'SY' => '+963', 'IR' => '+98'
];
$cDialToIso = [
    '+91' => 'IN', '+1' => 'US', '+44' => 'GB', '+971' => 'AE',
    '+61' => 'AU', '+65' => 'SG', '+966' => 'SA', '+49' => 'DE',
    '+33' => 'FR', '+974' => 'QA', '+965' => 'KW', '+968' => 'OM',
    '+973' => 'BH', '+60' => 'MY', '+63' => 'PH', '+62' => 'ID',
    '+64' => 'NZ', '+27' => 'ZA', '+353' => 'IE', '+31' => 'NL',
    '+46' => 'SE', '+41' => 'CH', '+34' => 'ES', '+39' => 'IT',
    '+55' => 'BR', '+52' => 'MX', '+81' => 'JP', '+82' => 'KR',
    '+92' => 'PK', '+880' => 'BD', '+94' => 'LK', '+977' => 'NP',
    '+234' => 'NG', '+254' => 'KE', '+20' => 'EG', '+90' => 'TR',
    '+48' => 'PL', '+40' => 'RO', '+380' => 'UA', '+84' => 'VN',
    '+66' => 'TH', '+852' => 'HK', '+886' => 'TW', '+972' => 'IL',
    '+7' => 'RU', '+960' => 'MV', '+967' => 'YE', '+963' => 'SY', '+98' => 'IR'
];
$cSsrFlags = [
    'IN' => '<svg viewBox="0 0 640 480" width="20" height="15" class="rounded-1 shadow-xs" style="display:inline-block; vertical-align:middle; border-radius:2px; flex-shrink:0;"><rect width="640" height="160" fill="#FF9933"/><rect y="160" width="640" height="160" fill="#FFFFFF"/><rect y="320" width="640" height="160" fill="#138808"/><circle cx="320" cy="240" r="45" fill="none" stroke="#000080" stroke-width="4"/><circle cx="320" cy="240" r="10" fill="#000080"/><g stroke="#000080" stroke-width="2"><line x1="320" y1="195" x2="320" y2="285"/><line x1="275" y1="240" x2="365" y2="240"/><line x1="288" y1="208" x2="352" y2="272"/><line x1="288" y1="272" x2="352" y2="208"/><line x1="304" y1="197" x2="336" y2="283"/><line x1="336" y1="197" x2="304" y2="283"/><line x1="277" y1="224" x2="363" y2="256"/><line x1="277" y1="256" x2="363" y2="224"/></g></svg>',
    'US' => '<svg viewBox="0 0 640 480" width="20" height="15" class="rounded-1 shadow-xs" style="display:inline-block; vertical-align:middle; border-radius:2px; flex-shrink:0;"><rect width="640" height="480" fill="#B22234"/><path d="M0,37h640M0,111h640M0,185h640M0,258h640M0,332h640M0,406h640" stroke="#FFFFFF" stroke-width="37"/><rect width="260" height="258" fill="#3C3B6E"/><g fill="#FFFFFF"><circle cx="40" cy="35" r="7"/><circle cx="90" cy="35" r="7"/><circle cx="140" cy="35" r="7"/><circle cx="190" cy="35" r="7"/><circle cx="240" cy="35" r="7"/><circle cx="65" cy="70" r="7"/><circle cx="115" cy="70" r="7"/><circle cx="165" cy="70" r="7"/><circle cx="215" cy="70" r="7"/><circle cx="40" cy="105" r="7"/><circle cx="90" cy="105" r="7"/><circle cx="140" cy="105" r="7"/><circle cx="190" cy="105" r="7"/><circle cx="240" cy="105" r="7"/><circle cx="65" cy="140" r="7"/><circle cx="115" cy="140" r="7"/><circle cx="165" cy="140" r="7"/><circle cx="215" cy="140" r="7"/><circle cx="40" cy="175" r="7"/><circle cx="90" cy="175" r="7"/><circle cx="140" cy="175" r="7"/><circle cx="190" cy="175" r="7"/><circle cx="240" cy="175" r="7"/><circle cx="65" cy="210" r="7"/><circle cx="115" cy="210" r="7"/><circle cx="165" cy="210" r="7"/><circle cx="215" cy="210" r="7"/></g></svg>',
    'GB' => '<svg viewBox="0 0 640 480" width="20" height="15" class="rounded-1 shadow-xs" style="display:inline-block; vertical-align:middle; border-radius:2px; flex-shrink:0;"><clipPath id="js_gb_tab_ssr"><rect width="640" height="480"/></clipPath><g clip-path="url(#js_gb_tab_ssr)"><rect width="640" height="480" fill="#012169"/><path d="M0,0 L640,480 M640,0 L0,480" stroke="#FFFFFF" stroke-width="60"/><path d="M0,0 L640,480 M640,0 L0,480" stroke="#C8102E" stroke-width="36"/><path d="M320,0 V480 M0,240 H640" stroke="#FFFFFF" stroke-width="100"/><path d="M320,0 V480 M0,240 H640" stroke="#C8102E" stroke-width="60"/></g></svg>',
    'AE' => '<svg viewBox="0 0 640 480" width="20" height="15" class="rounded-1 shadow-xs" style="display:inline-block; vertical-align:middle; border-radius:2px; flex-shrink:0;"><rect width="640" height="160" fill="#00732f"/><rect y="160" width="640" height="160" fill="#ffffff"/><rect y="320" width="640" height="160" fill="#000000"/><rect width="160" height="480" fill="#ff0000"/></svg>',
    'CA' => '<svg viewBox="0 0 640 480" width="20" height="15" class="rounded-1 shadow-xs" style="display:inline-block; vertical-align:middle; border-radius:2px; flex-shrink:0;"><rect width="160" height="480" fill="#d80621"/><rect x="160" width="320" height="480" fill="#ffffff"/><rect x="480" width="160" height="480" fill="#d80621"/><path d="M320,130 L335,210 L370,185 L365,225 L405,230 L375,260 L390,290 L345,280 L350,305 L330,305 L330,350 L310,350 L310,305 L290,305 L295,280 L250,290 L265,260 L235,230 L275,225 L270,185 L305,210 Z" fill="#d80621"/></svg>',
    'AU' => '<svg viewBox="0 0 640 480" width="20" height="15" class="rounded-1 shadow-xs" style="display:inline-block; vertical-align:middle; border-radius:2px; flex-shrink:0;"><rect width="640" height="480" fill="#00008b"/><circle cx="160" cy="360" r="30" fill="#ffffff"/><circle cx="480" cy="120" r="14" fill="#ffffff"/><circle cx="430" cy="200" r="14" fill="#ffffff"/><circle cx="530" cy="220" r="14" fill="#ffffff"/><circle cx="470" cy="300" r="14" fill="#ffffff"/><circle cx="500" cy="250" r="9" fill="#ffffff"/></svg>',
    'SG' => '<svg viewBox="0 0 640 480" width="20" height="15" class="rounded-1 shadow-xs" style="display:inline-block; vertical-align:middle; border-radius:2px; flex-shrink:0;"><rect width="640" height="240" fill="#ed2939"/><rect y="240" width="640" height="240" fill="#ffffff"/><circle cx="130" cy="120" r="60" fill="#ffffff"/><circle cx="150" cy="120" r="55" fill="#ed2939"/><circle cx="170" cy="85" r="10" fill="#ffffff"/><circle cx="195" cy="105" r="10" fill="#ffffff"/><circle cx="195" cy="135" r="10" fill="#ffffff"/><circle cx="170" cy="155" r="10" fill="#ffffff"/><circle cx="155" cy="120" r="10" fill="#ffffff"/></svg>',
    'SA' => '<svg viewBox="0 0 640 480" width="20" height="15" class="rounded-1 shadow-xs" style="display:inline-block; vertical-align:middle; border-radius:2px; flex-shrink:0;"><rect width="640" height="480" fill="#006c35"/><rect x="180" y="270" width="280" height="14" rx="7" fill="#ffffff"/><polygon points="180,277 210,265 210,289" fill="#ffffff"/><text x="320" y="230" fill="#ffffff" font-size="65" font-family="sans-serif" font-weight="bold" text-anchor="middle">SA</text></svg>',
    'DE' => '<svg viewBox="0 0 640 480" width="20" height="15" class="rounded-1 shadow-xs" style="display:inline-block; vertical-align:middle; border-radius:2px; flex-shrink:0;"><rect width="640" height="160" fill="#000000"/><rect y="160" width="640" height="160" fill="#dd0000"/><rect y="320" width="640" height="160" fill="#ffce00"/></svg>',
    'FR' => '<svg viewBox="0 0 640 480" width="20" height="15" class="rounded-1 shadow-xs" style="display:inline-block; vertical-align:middle; border-radius:2px; flex-shrink:0;"><rect width="213" height="480" fill="#002395"/><rect x="213" width="214" height="480" fill="#ffffff"/><rect x="427" width="213" height="480" fill="#ed2939"/></svg>'
];
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

        // SSR Phone resolution with country flag and calling code
        $cCallingClean = trim((string)($cCalling ?: ''));
        $cIso = 'IN';
        $cDial = '+91';
        if (!empty($cCallingClean)) {
            $cUpper = strtoupper($cCallingClean);
            if (isset($cIsoToDial[$cUpper])) {
                $cIso = $cUpper;
                $cDial = $cIsoToDial[$cUpper];
            } else {
                $cCandidate = str_starts_with($cCallingClean, '+') ? $cCallingClean : ('+' . $cCallingClean);
                if (isset($cDialToIso[$cCandidate])) {
                    $cDial = $cCandidate;
                    $cIso = $cDialToIso[$cCandidate];
                }
            }
        }
        $cRawPhone = trim((string)($cNumber ?: ''));
        if (empty($cRawPhone)) {
            $cPhoneHtml = '<span style="color:#94a3b8;">—</span>';
        } else {
            $cCleanPhone = $cRawPhone;
            if (str_starts_with($cCleanPhone, $cDial)) {
                $cCleanPhone = trim(substr($cCleanPhone, strlen($cDial)));
            } elseif (str_starts_with($cCleanPhone, '+' . ltrim($cDial, '+'))) {
                $cCleanPhone = trim(substr($cCleanPhone, strlen('+' . ltrim($cDial, '+'))));
            }
            $cFlagSvg = $cSsrFlags[$cIso] ?? ('<svg viewBox="0 0 640 480" width="20" height="15" class="rounded-1 shadow-xs" style="display:inline-block; vertical-align:middle; border-radius:2px; flex-shrink:0;"><rect width="640" height="480" fill="#1e3c72" rx="60"/><text x="320" y="320" fill="#ffffff" font-size="240" font-family=\'Poppins\', sans-serif font-weight="700" text-anchor="middle">' . htmlspecialchars($cIso) . '</text></svg>');
            $cPhoneHtml = '<span class="d-inline-flex align-items-center text-nowrap" style="gap:6px;">'
                . '<span class="d-inline-flex align-items-center flex-shrink-0" style="width:20px; height:15px; border-radius:2px; overflow:hidden; box-shadow:0 0 0 1px rgba(0,0,0,0.08);">' . $cFlagSvg . '</span>'
                . '<span style="font-weight:600; color:#1e293b; font-size:13px;">' . htmlspecialchars($cDial) . '</span>'
                . '<span style="color:#334155; font-size:13px; font-weight:500;">' . htmlspecialchars($cCleanPhone) . '</span>'
                . '</span>';
        }
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
                  <td style="padding:14px 14px; color:#334155; font-size:13px; font-weight:500;"><?php echo $cPhoneHtml; ?></td>
                  <td style="padding:14px 10px;"><?php echo $typeBadge; ?></td>
                  <td style="padding:14px 18px; text-align:right;">
                    <div class="d-inline-flex align-items-center gap-1 justify-content-end">
                      <button type="button" class="btn btn-icon client-edit-contact" data-index="<?php echo $idx; ?>" title="Edit" style="width:30px; height:30px; border-radius:6px; background:#eff6ff; border:1px solid #bfdbfe; color:#2563eb; display:inline-flex; align-items:center; justify-content:center; padding:0;">
                        <i class="la la-pencil" style="font-size:14px; color:#2563eb;"></i>
                      </button>
                      <button type="button" class="btn btn-icon client-delete-contact" data-index="<?php echo $idx; ?>" title="Delete" style="width:30px; height:30px; border-radius:6px; background:#fef2f2; border:1px solid #fecaca; color:#dc2626; display:inline-flex; align-items:center; justify-content:center; padding:0;">
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
                    <button type="button" class="btn btn-warning client-open-contact-drawer-btn" style="background:#ffd428 !important; border:none !important; color:#1e293b !important; font-weight:700 !important; font-size:13px !important; padding:9px 20px !important; border-radius:8px !important; display:inline-flex; align-items:center; gap:6px;">
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

      <!-- ==============================================================
           Official Social Profiles & Accounts Card (21-Box Contract)
           ============================================================== -->
      <div class="card client-social-profiles-card mt-4 mb-4" style="background:#ffffff; border:1px solid #e2e8f0; border-radius:12px; box-shadow:0 1px 3px rgba(0,0,0,0.03); padding:20px 22px;">
        <div class="d-flex align-items-center mb-4" style="gap:12px; justify-content:flex-start; text-align:left;">
          <div style="width:36px; height:36px; border-radius:9px; background:#eff6ff; display:inline-flex; align-items:center; justify-content:center; color:#2563eb; flex-shrink:0;">
            <i class="la la-share-alt" style="font-size:18px; color:#2563eb;"></i>
          </div>
          <div style="text-align:left;">
            <h3 style="font-size:15px; font-weight:700; color:#0f172a; margin:0 0 2px 0;">Official Social Profiles &amp; Accounts</h3>
            <p style="font-size:11.5px; color:#64748b; margin:0;">Verified company presence channels and billing correspondence email.</p>
          </div>
        </div>

        <!-- 2x2 Grid for Core Social Channels & AP Email -->
        <div style="display:grid; grid-template-columns:repeat(2, minmax(0, 1fr)); gap:16px;">
          <!-- LinkedIn Company URL -->
          <div class="client-field fv-plugins-icon-container">
            <label class="form-label" for="linkedin_url" style="font-size:12.5px; font-weight:600; color:#0f172a; margin-bottom:5px;">LinkedIn Company Page</label>
            <div class="input-group input-group-solid" style="border:1px solid #cbd5e1; border-radius:8px; overflow:hidden; background:#ffffff; height:42px;">
              <span class="input-group-text" style="background:#fff; border:none; color:#0a66c2; padding-left:12px; padding-right:8px;"><i class="la la-linkedin" style="font-size:16px;"></i></span>
              <input type="url" class="form-control form-control-solid" name="linkedin_url" id="linkedin_url" value="<?php echo @$linkedin_url ?? ''; ?>" placeholder="https://linkedin.com/company/example" style="border:none; height:100%; font-size:13px; color:#1e293b; background-color:#ffffff;">
            </div>
          </div>

          <!-- Twitter / X Profile -->
          <div class="client-field fv-plugins-icon-container">
            <label class="form-label" for="twitter_url" style="font-size:12.5px; font-weight:600; color:#0f172a; margin-bottom:5px;">Twitter / X Profile</label>
            <div class="input-group input-group-solid" style="border:1px solid #cbd5e1; border-radius:8px; overflow:hidden; background:#ffffff; height:42px;">
              <span class="input-group-text" style="background:#fff; border:none; color:#0f172a; padding-left:12px; padding-right:8px;"><i class="la la-twitter" style="font-size:16px;"></i></span>
              <input type="text" class="form-control form-control-solid" name="twitter_url" id="twitter_url" value="<?php echo @$twitter_url ?? ''; ?>" placeholder="https://x.com/example" style="border:none; height:100%; font-size:13px; color:#1e293b; background-color:#ffffff;">
            </div>
          </div>

          <!-- GitHub Profile -->
          <div class="client-field fv-plugins-icon-container">
            <label class="form-label" for="github_url" style="font-size:12.5px; font-weight:600; color:#0f172a; margin-bottom:5px;">GitHub Organization</label>
            <div class="input-group input-group-solid" style="border:1px solid #cbd5e1; border-radius:8px; overflow:hidden; background:#ffffff; height:42px;">
              <span class="input-group-text" style="background:#fff; border:none; color:#24292f; padding-left:12px; padding-right:8px;"><i class="la la-github" style="font-size:16px;"></i></span>
              <input type="url" class="form-control form-control-solid" name="github_url" id="github_url" value="<?php echo @$github_url ?? ''; ?>" placeholder="https://github.com/example" style="border:none; height:100%; font-size:13px; color:#1e293b; background-color:#ffffff;">
            </div>
          </div>

          <!-- Accounts Payable Email -->
          <div class="client-field fv-plugins-icon-container">
            <label class="form-label" for="ap_email" style="font-size:12.5px; font-weight:600; color:#0f172a; margin-bottom:5px;">Accounts Payable (Billing) Email</label>
            <div class="input-group input-group-solid" style="border:1px solid #cbd5e1; border-radius:8px; overflow:hidden; background:#ffffff; height:42px;">
              <span class="input-group-text" style="background:#fff; border:none; color:#059669; padding-left:12px; padding-right:8px;"><i class="la la-envelope" style="font-size:16px;"></i></span>
              <input type="email" class="form-control form-control-solid" name="ap_email" id="ap_email" value="<?php echo @$ap_email ?? ''; ?>" placeholder="billing@example.com" style="border:none; height:100%; font-size:13px; color:#1e293b; background-color:#ffffff;">
            </div>
          </div>
        </div>

        <!-- Collapsible Toggle for Extended Social Profiles -->
        <div class="mt-3 pt-3 border-top" style="border-color:#f1f5f9 !important;">
          <a href="javascript:void(0);" onclick="$('#extended_socials_collapse').slideToggle(200);" style="font-size:11.5px; color:#2563eb; font-weight:600; text-decoration:none; display:inline-flex; align-items:center; gap:5px;">
            <i class="la la-plus-circle"></i> <span>Extended Social Profiles (YouTube, Facebook, Instagram)</span>
          </a>
          <div id="extended_socials_collapse" style="display:none; margin-top:12px;">
            <div style="display:grid; grid-template-columns:repeat(3, minmax(0, 1fr)); gap:12px;">
              <!-- YouTube -->
              <div>
                <label class="form-label" for="youtube_url" style="font-size:11.5px; font-weight:600; color:#0f172a; margin-bottom:4px;">YouTube</label>
                <div class="input-group input-group-solid" style="border:1px solid #cbd5e1; border-radius:8px; overflow:hidden; background:#ffffff; height:38px;">
                  <span class="input-group-text" style="background:#fff; border:none; color:#ef4444; padding-left:10px; padding-right:6px;"><i class="la la-youtube" style="font-size:15px;"></i></span>
                  <input type="url" class="form-control form-control-solid" name="youtube_url" id="youtube_url" value="<?php echo @$youtube_url ?? ''; ?>" placeholder="https://youtube.com/@example" style="border:none; height:100%; font-size:12px; color:#1e293b; background-color:#ffffff;">
                </div>
              </div>
              <!-- Facebook -->
              <div>
                <label class="form-label" for="facebook_url" style="font-size:11.5px; font-weight:600; color:#0f172a; margin-bottom:4px;">Facebook</label>
                <div class="input-group input-group-solid" style="border:1px solid #cbd5e1; border-radius:8px; overflow:hidden; background:#ffffff; height:38px;">
                  <span class="input-group-text" style="background:#fff; border:none; color:#1877f2; padding-left:10px; padding-right:6px;"><i class="la la-facebook" style="font-size:15px;"></i></span>
                  <input type="url" class="form-control form-control-solid" name="facebook_url" id="facebook_url" value="<?php echo @$facebook_url ?? ''; ?>" placeholder="https://facebook.com/example" style="border:none; height:100%; font-size:12px; color:#1e293b; background-color:#ffffff;">
                </div>
              </div>
              <!-- Instagram -->
              <div>
                <label class="form-label" for="instagram_url" style="font-size:11.5px; font-weight:600; color:#0f172a; margin-bottom:4px;">Instagram</label>
                <div class="input-group input-group-solid" style="border:1px solid #cbd5e1; border-radius:8px; overflow:hidden; background:#ffffff; height:38px;">
                  <span class="input-group-text" style="background:#fff; border:none; color:#e1306c; padding-left:10px; padding-right:6px;"><i class="la la-instagram" style="font-size:15px;"></i></span>
                  <input type="url" class="form-control form-control-solid" name="instagram_url" id="instagram_url" value="<?php echo @$instagram_url ?? ''; ?>" placeholder="https://instagram.com/example" style="border:none; height:100%; font-size:12px; color:#1e293b; background-color:#ffffff;">
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
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
<div class="client-contact-drawer-backdrop" id="client_contact_drawer_backdrop"></div>
<div class="client-contact-drawer" id="client_contact_drawer" aria-hidden="true">
  <!-- Header -->
  <div class="client-drawer-head">
    <div class="client-drawer-head-info">
      <h4 id="client_contact_drawer_title">Add Contact Person</h4>
      <p class="client-drawer-head-subtitle" id="client_contact_drawer_subtitle">Add the primary or secondary contact details for this client.</p>
    </div>
    <button type="button" class="client-drawer-close-btn" id="client_close_contact_drawer" title="Close" aria-label="Close drawer">
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
    <button type="button" class="cdf-cancel-btn" id="client_cancel_contact_drawer">Cancel</button>
    <button type="button" class="btn btn-warning cdf-save-btn" id="client_save_contact_drawer" style="background:#ffd428 !important; border:none !important; color:#1e293b !important; font-weight:700 !important; font-size:13px !important; padding:10px 22px !important; border-radius:8px !important;">
      <span class="cdf-spinner" style="display:none; width:14px; height:14px; border:2px solid #1e293b; border-top-color:transparent; border-radius:50%; animation:spin 0.6s linear infinite; margin-right:6px;"></span>
      <i class="la la-user-plus cdf-btn-icon" aria-hidden="true" style="color:#1e293b; font-size:15px; margin-right:4px;"></i>
      <span class="cdf-btn-text">Add Contact Person</span>
    </button>
  </div>
</div>
<!--end::Contact Person Step-->
