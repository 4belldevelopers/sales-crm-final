@php
    use App\Helper\GeneralMethod;

    $photo = url('public/avatar.png');
    if (!empty($benchApplication->photo) && file_exists(public_path('user-uploads/bench_photos/' . $benchApplication->photo))) {
        $photo = url('/public/user-uploads/bench_photos/' . $benchApplication->photo);
    }

    $country_id = @$benchApplication->country ?: 101;
    $country_name_Arr = GeneralMethod::getCountryName($country_id);
    $country_name = '';
    foreach ($country_name_Arr as $cval) {
        $country_name = ucwords($cval['name']);
    }

    // Server-Authoritative State & City Resolution
    $state_name = @$benchApplication->state_name;
    $city_name = @$benchApplication->city_name ?: @$benchApplication->city;

    $currencySymbole = GeneralMethod::currencySymbole();
    $currencySymboleforexcell = GeneralMethod::currencySymboleforexcell();
    $current_ctc_cur = $currencySymbole[@$benchApplication->pay_rate_currency ?? 0] ?? '₹';
    $current_ectc_cur = $currencySymbole[@$benchApplication->bill_rate_currency ?? 0] ?? '₹';
    $current_cur = $currencySymboleforexcell[@$benchApplication->bill_rate_currency ?? 0] ?? 'INR';
    $posted_by_company = @$benchApplication->company_name ?: (auth()->user()->company->company_name ?? '—');

    // Experience formatting
    $experience = trim((string) @$benchApplication->total_exp);
    if (strpos($experience, '.') !== false) {
        $parts = explode('.', $experience);
        $formattedExperience = (isset($parts[1]) && $parts[1] === '00') ? $parts[0] : $experience;
    } else {
        $formattedExperience = $experience;
    }
    if (empty($formattedExperience)) $formattedExperience = '0';

    $rel_experience = trim((string) @$benchApplication->rel_exp);
    if (strpos($rel_experience, '.') !== false) {
        $parts = explode('.', $rel_experience);
        $rel_formattedExperience = (isset($parts[1]) && $parts[1] === '00') ? $parts[0] : $rel_experience;
    } else {
        $rel_formattedExperience = $rel_experience;
    }
    if (empty($rel_formattedExperience)) $rel_formattedExperience = '0';

    // Notice Period / Availability
    $np = '';
    if ((@$benchApplication->serving_notice_period_mode == 0) || (@$benchApplication->serving_notice_period_mode == 1)) {
        $np = @$benchApplication->np_id;
        if (!empty($np) && strtotime($np) !== false) {
            $np = 'Available ' . date("d M Y", strtotime($np));
        }
    } else {
        $np = @$benchApplication->np_id;
    }
    if (empty($np)) $np = '—';

    // Initials for avatar
    $fullName = trim((string) @$benchApplication->full_name);
    $initials = '';
    if ($fullName !== '') {
        $parts = preg_split('/\s+/', $fullName);
        $initials = strtoupper(substr($parts[0] ?? '', 0, 1) . (isset($parts[1]) ? substr($parts[1], 0, 1) : ''));
    }
    if ($initials === '') { $initials = 'BC'; }

    // Skills array & count
    $skillsRaw = trim((string) @$benchApplication->skills);
    $skillsArr = $skillsRaw !== '' ? array_values(array_filter(array_map('trim', explode(',', $skillsRaw)), 'strlen')) : [];
    $totalSkillsCount = !empty($skillExperiencesCount) ? (int)$skillExperiencesCount : count($skillsArr);
    $skillsCountDisplay = $totalSkillsCount . ' ' . ($totalSkillsCount === 1 ? 'Skill' : 'Skills');

    // Country short-code mapping & Work Auth resolver
    $countryCodeShortMap = [
        'united states' => 'USA', 'usa' => 'USA', 'us' => 'USA',
        'india' => 'IND', 'in' => 'IND',
        'canada' => 'CAN', 'ca' => 'CAN',
        'united kingdom' => 'UK', 'uk' => 'UK', 'great britain' => 'UK', 'gb' => 'UK',
        'united arab emirates' => 'UAE', 'uae' => 'UAE', 'ae' => 'UAE',
        'australia' => 'AUS', 'au' => 'AUS',
        'singapore' => 'SG', 'sg' => 'SG',
        'new zealand' => 'NZ', 'nz' => 'NZ',
        'saudi arabia' => 'KSA', 'sa' => 'KSA',
        'germany' => 'GER', 'de' => 'GER',
        'france' => 'FRA', 'fr' => 'FRA',
        'netherlands' => 'NLD', 'nl' => 'NLD',
        'ireland' => 'IRL', 'ie' => 'IRL',
        'switzerland' => 'SUI', 'ch' => 'SUI',
        'south africa' => 'ZAF', 'za' => 'ZAF',
        'malaysia' => 'MYS', 'my' => 'MYS',
        'philippines' => 'PHL', 'ph' => 'PHL',
        'qatar' => 'QAT', 'qa' => 'QAT',
        'kuwait' => 'KWT', 'kw' => 'KWT',
        'oman' => 'OMN', 'om' => 'OMN',
        'bahrain' => 'BHR', 'bh' => 'BHR',
    ];

    $defaultCountryShort = '';
    if (!empty($country_name)) {
        $cNameLower = strtolower(trim($country_name));
        $defaultCountryShort = $countryCodeShortMap[$cNameLower] ?? strtoupper(substr($cNameLower, 0, 3));
    }

    $rawVisa = trim((string) @$benchApplication->visastatu);
    $formatWorkAuthWithCountry = function($visa, $defaultCountryShort) use ($countryCodeShortMap) {
        if (empty($visa)) {
            return !empty($defaultCountryShort) ? "{$defaultCountryShort} - Authorised" : 'Authorised';
        }
        $vLower = strtolower($visa);

        // US specific visa types
        if (preg_match('/^(h-?1b|green card|gc-ead|h-?4 ead|l-?2 ead|tn permit|tn visa|opt-?ead|cpt|e-?3|o-?1|l-?1|other us ead|us citizen|usc)/i', $visa)) {
            $cleaned = preg_replace('/^(us citizen|usc)/i', 'Citizen', $visa);
            return 'USA - ' . (strtoupper($cleaned) === 'H1B' ? 'H1B' : $cleaned);
        }
        if (preg_match('/^canad(a|ian)/i', $visa)) {
            $cleaned = preg_replace('/^canad(a|ian)\s+/i', '', $visa);
            return 'CAN - ' . $cleaned;
        }
        if (preg_match('/^(india|indian|oci)/i', $visa)) {
            $cleaned = preg_replace('/^indi(a|an)\s+/i', '', $visa);
            return 'IND - ' . $cleaned;
        }
        if (preg_match('/^(uk|united kingdom)/i', $visa)) {
            $cleaned = preg_replace('/^(uk|united kingdom)\s+/i', '', $visa);
            return 'UK - ' . $cleaned;
        }
        if (preg_match('/^(uae|saudi|gcc)/i', $visa)) {
            return (str_starts_with($vLower, 'saudi') ? 'KSA - ' : 'UAE - ') . preg_replace('/^(uae|saudi)\s+/i', '', $visa);
        }
        if (preg_match('/^singapore/i', $visa)) {
            $cleaned = preg_replace('/^singapore\s+/i', '', $visa);
            return 'SG - ' . $cleaned;
        }
        if (preg_match('/^australi(a|an)/i', $visa)) {
            $cleaned = preg_replace('/^australi(a|an)\s+/i', '', $visa);
            return 'AUS - ' . $cleaned;
        }
        if (preg_match('/^new zealand/i', $visa)) {
            $cleaned = preg_replace('/^new zealand\s+/i', '', $visa);
            return 'NZ - ' . $cleaned;
        }
        if (preg_match('/^eu/i', $visa)) {
            return 'EU - ' . preg_replace('/^eu\s+/i', '', $visa);
        }
        if (str_contains($visa, ' - ')) {
            $parts = explode(' - ', $visa, 2);
            $p0Lower = strtolower(trim($parts[0]));
            $short = $countryCodeShortMap[$p0Lower] ?? strtoupper(substr($p0Lower, 0, 3));
            return $short . ' - ' . trim($parts[1]);
        }
        if (!empty($defaultCountryShort)) {
            return "{$defaultCountryShort} - {$visa}";
        }
        return $visa;
    };

    $workAuthShortDisplay = $formatWorkAuthWithCountry($rawVisa, $defaultCountryShort);

    // Added Date with 12-hour AM/PM and IST Timezone
    $created_at = @$benchApplication->created_at;
    $addedDate = !empty($created_at) ? date("d-M-Y h:i A", strtotime($created_at)) . ' IST' : '—';

    // Phone Normalizer for server-rendered flags
    $isoToDialMap = [
        'in' => '+91', 'us' => '+1', 'gb' => '+44', 'ae' => '+971', 'ca' => '+1',
        'au' => '+61', 'sg' => '+65', 'sa' => '+966', 'de' => '+49', 'fr' => '+33',
        'qa' => '+974', 'kw' => '+965', 'om' => '+968', 'bh' => '+973', 'my' => '+60',
        'ph' => '+63', 'id' => '+62', 'nz' => '+64', 'za' => '+27', 'ie' => '+353',
        'nl' => '+31', 'se' => '+46', 'ch' => '+41', 'es' => '+34', 'it' => '+39',
    ];
    $dialToIsoMap = [
        '+91' => 'in', '+1' => 'us', '+44' => 'gb', '+971' => 'ae',
        '+61' => 'au', '+65' => 'sg', '+966' => 'sa', '+49' => 'de',
        '+33' => 'fr', '+974' => 'qa', '+965' => 'kw', '+968' => 'om',
        '+973' => 'bh', '+60' => 'my', '+63' => 'ph', '+62' => 'id',
        '+64' => 'nz', '+27' => 'za', '+353' => 'ie', '+31' => 'nl',
    ];

    $resolvePhone = function($rawCode) use ($isoToDialMap, $dialToIsoMap) {
        $raw = trim((string) $rawCode);
        $lower = strtolower($raw);
        if (isset($isoToDialMap[$lower])) {
            return ['iso' => $lower, 'dial' => $isoToDialMap[$lower]];
        }
        $withPlus = str_starts_with($raw, '+') ? $raw : ('+' . $raw);
        if (isset($dialToIsoMap[$withPlus])) {
            return ['iso' => $dialToIsoMap[$withPlus], 'dial' => $withPlus];
        }
        return ['iso' => 'in', 'dial' => ($raw !== '' ? $withPlus : '+91')];
    };

    $candidatePhoneData = $resolvePhone(@$benchApplication->phone_code);

    // Dynamic international WhatsApp phone number resolution
    $cleanPhone = preg_replace('/[^0-9]/', '', (string) @$benchApplication->phone);
    $dialDigits = preg_replace('/[^0-9]/', '', $candidatePhoneData['dial']);
    if (empty($dialDigits)) { $dialDigits = '91'; }
    $whatsAppPhone = str_starts_with($cleanPhone, $dialDigits) ? $cleanPhone : ($dialDigits . $cleanPhone);

    // Resume Download URL
    $resumeDownloadUrl = '';
    if (!empty($benchApplication->resume)) {
        if (str_starts_with($benchApplication->resume, 'http://') || str_starts_with($benchApplication->resume, 'https://')) {
            $resumeDownloadUrl = $benchApplication->resume;
        } elseif (file_exists(public_path('user-uploads/resumes/' . $benchApplication->resume))) {
            $resumeDownloadUrl = asset('user-uploads/resumes/' . $benchApplication->resume);
        } elseif (file_exists(public_path('user-uploads/documents/' . $benchApplication->id . '/' . $benchApplication->resume))) {
            $resumeDownloadUrl = asset('user-uploads/documents/' . $benchApplication->id . '/' . $benchApplication->resume);
        } else {
            $resumeDownloadUrl = asset('user-uploads/resumes/' . $benchApplication->resume);
        }
    }
    $isPdfResume = !empty($benchApplication->resume) && strtolower(pathinfo($benchApplication->resume, PATHINFO_EXTENSION)) === 'pdf';
@endphp

<link href="{{ asset('demo/css/candidates-details.css') }}{{ TM_VERSION }}" rel="stylesheet" type="text/css" />

<style>
.text-justify { text-align: justify !important; }
.iti__flag { margin-top: 0 !important; }

/* ===== GLOBAL TYPOGRAPHY & DRAWER STABILITY ===== */
#kt_activitiesBenchApplication,
#kt_activitiesBenchApplication * {
    font-family: 'Poppins', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif !important;
    box-sizing: border-box !important;
}

#kt_activitiesBenchApplication {
    overflow-y: auto !important;
    overflow-x: hidden !important;
    max-height: 100vh !important;
    height: 100vh !important;
    background: #f1f5f9 !important;
    padding: 0 !important;
    margin: 0 !important;
    display: block !important;
    box-sizing: border-box !important;
}

@media only screen and (max-width: 1023px) {
    #kt_activitiesBenchApplication { width: 95% !important; max-width: 100% !important; }
}
@media only screen and (min-width: 1024px) and (max-width: 1440px) {
    #kt_activitiesBenchApplication { width: 1025px !important; max-width: 100% !important; }
}
@media only screen and (min-width: 1440px) {
    #kt_activitiesBenchApplication { width: 1200px !important; max-width: 100% !important; }
}

#bench_application_show_right_bar {
    min-height: 100vh;
    overflow-y: visible !important;
    padding: 0 !important;
    margin: 0 !important;
    width: 100% !important;
    max-width: 100% !important;
    min-width: 100% !important;
    border: none !important;
    box-shadow: none !important;
    background: #f1f5f9 !important;
    box-sizing: border-box !important;
    display: block !important;
}

/* ===== THREE-CARD LAYOUT & BALANCED SIDE GAPS ===== */
#kt_activitiesBenchApplication .page-wrapper,
#bench_application_show_right_bar_wrapper,
.page-wrapper {
    display: flex !important;
    flex-direction: column !important;
    gap: 12px !important;
    background: #f1f5f9 !important;
    min-height: 100vh !important;
    padding: 0 0 24px 0 !important;
    margin: 0 !important;
    width: 100% !important;
    max-width: 100% !important;
    min-width: 100% !important;
    box-sizing: border-box !important;
    overflow-y: visible !important;
    overflow-x: hidden !important;
}

#kt_activitiesBenchApplication .cdr-outer-card,
#kt_activitiesBenchApplication .cdr-tabs-card,
#kt_activitiesBenchApplication .content-card:not(#bench_application_show_right_bar) {
    background: #fff !important;
    border: 1px solid #e2e8f0 !important;
    border-radius: 8px !important;
    box-shadow: 0 1px 3px rgba(15,23,42,.04), 0 4px 12px rgba(15,23,42,.03) !important;
    overflow: hidden !important;
    flex-shrink: 0 !important;
    margin: 0 16px !important;
    width: calc(100% - 32px) !important;
    max-width: calc(100% - 32px) !important;
    box-sizing: border-box !important;
}

/* ===== TOP ACTION BAR — Spans Full Drawer Width with Zero Outer Gutters ===== */
#kt_activitiesBenchApplication .cdr-topbar {
    display: flex !important;
    align-items: center !important;
    flex-wrap: nowrap !important;
    gap: 8px !important;
    padding: 12px 16px !important;
    background: #fff !important;
    border-bottom: 1px solid #e2e8f0 !important;
    justify-content: space-between !important;
    position: relative !important;
    z-index: 1000 !important;
    overflow: visible !important;
    width: 100% !important;
    max-width: 100% !important;
    min-width: 100% !important;
    margin: 0 !important;
    box-sizing: border-box !important;
}
#kt_activitiesBenchApplication .cdr-topbar-group {
    display: flex !important;
    align-items: center;
    gap: 8px;
    flex: 1 1 auto;
    min-width: 0;
    flex-wrap: nowrap;
    overflow: visible !important;
}
#kt_activitiesBenchApplication .cdr-action-btn {
    display: inline-flex !important;
    align-items: center;
    gap: 6px;
    padding: 7px 14px;
    border: 1.5px solid #d1d5db;
    border-radius: 8px;
    background: #fff;
    color: #374151;
    font-family: 'Poppins', sans-serif;
    font-size: 12.5px;
    font-weight: 500;
    cursor: pointer;
    text-decoration: none;
    white-space: nowrap;
    line-height: 1;
    transition: all 0.15s ease;
}
#kt_activitiesBenchApplication .cdr-action-btn.cdr-btn-primary { background: #3b82f6; color: #fff; border-color: #3b82f6; }
#kt_activitiesBenchApplication .cdr-action-btn.cdr-btn-primary:hover { background: #2563eb; color: #fff; }
#kt_activitiesBenchApplication .cdr-action-btn.cdr-btn-stage { background: #059669; color: #fff; border-color: #059669; font-weight: 600; }
#kt_activitiesBenchApplication .cdr-action-btn.cdr-btn-stage:hover { background: #047857; color: #fff; }
#kt_activitiesBenchApplication .cdr-action-btn.cdr-btn-secondary { background: #fff; color: #475569; border-color: #e2e8f0; }
#kt_activitiesBenchApplication .cdr-action-btn.cdr-btn-secondary:hover { background: #f8fafc; border-color: #cbd5e1; color: #0f172a; }
#kt_activitiesBenchApplication .cdr-action-btn.cdr-btn-dl-center:hover { background: #f0fdf4; border-color: #86efac; color: #15803d; }
#kt_activitiesBenchApplication .cdr-action-btn svg { width: 14px; height: 14px; flex-shrink: 0; }
#kt_activitiesBenchApplication .cdr-topbar-split { width: 1px; height: 20px; background: #e2e8f0; flex-shrink: 0; margin: 0 2px; }
#kt_activitiesBenchApplication .cdr-topbar-close {
    width: 34px;
    height: 34px;
    flex-shrink: 0;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    background: none;
    border: 1.5px solid #e2e8f0;
    border-radius: 8px;
    cursor: pointer;
    color: #6b7280;
    padding: 0;
    margin-left: 4px;
    transition: all 0.15s ease;
}
#kt_activitiesBenchApplication .cdr-topbar-close:hover { background: #fee2e2; border-color: #fca5a5; color: #dc2626; }

/* ===== HERO CARD ===== */
#kt_activitiesBenchApplication .cdr-hero-card { display: grid !important; grid-template-columns: 1.4fr 1fr; background: #fff; border-bottom: 1px solid #f0f4f8; }
#kt_activitiesBenchApplication .cdr-subcard-id { display: flex !important; flex-direction: column !important; gap: 0 !important; align-items: stretch !important; padding: 18px 20px; border-right: 1px solid #f1f5f9; }
#kt_activitiesBenchApplication .cdr-id-top { display: flex; gap: 14px; align-items: flex-start; }
#kt_activitiesBenchApplication .cdr-avatar-wrap { position: relative; flex-shrink: 0; align-self: flex-start; }
#kt_activitiesBenchApplication .cdr-avatar-sq {
    width: 88px;
    height: 88px;
    border-radius: 16px;
    background: #eef2ff;
    color: #1e3a8a;
    font-family: 'Poppins', sans-serif;
    font-weight: 700;
    font-size: 26px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    overflow: hidden;
}
#kt_activitiesBenchApplication .cdr-avatar-sq img { width: 100%; height: 100%; object-fit: cover; }
#kt_activitiesBenchApplication .cdr-id-body { flex: 1 1 auto; min-width: 0; }
#kt_activitiesBenchApplication .cdr-name-row { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; }
#kt_activitiesBenchApplication .cdr-name { margin: 0; font-size: 18px; font-weight: 700; color: #0f172a; line-height: 1.2; font-family: 'Poppins', sans-serif; }
#kt_activitiesBenchApplication .cdr-role { margin: 3px 0 0 0; font-size: 13px; font-weight: 600; color: #2563eb; line-height: 1.3; font-family: 'Poppins', sans-serif; }
#kt_activitiesBenchApplication .cdr-meta-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 6px 10px; margin-top: 10px; }
#kt_activitiesBenchApplication .cdr-meta-grid .cdr-meta-item {
    display: flex;
    align-items: flex-start;
    gap: 6px;
    font-size: 11.5px !important;
    font-weight: 500;
    color: #475569;
    font-family: 'Poppins', sans-serif;
    white-space: normal;
    overflow: visible;
    word-break: break-word;
    line-height: 1.4;
    min-width: 0;
}
#kt_activitiesBenchApplication .cdr-meta-grid .cdr-meta-item svg { width: 13px; height: 13px; flex-shrink: 0; color: #94a3b8; margin-top: 2px; }
#kt_activitiesBenchApplication .cdr-skills-bar { display: flex; flex-wrap: wrap; gap: 5px; margin-top: 12px; padding-top: 12px; border-top: 1px solid #f1f5f9; }
#kt_activitiesBenchApplication .cdr-skill-tag { display: inline-flex; align-items: center; font-size: 11px; font-weight: 500; padding: 3px 9px; border-radius: 4px; font-family: 'Poppins', sans-serif; line-height: 1.5; white-space: nowrap; border: 1px solid #e2e8f0; background: #f8fafc; color: #64748b; letter-spacing: .01em; }

/* ===== METRICS SUBCARD ===== */
#kt_activitiesBenchApplication .cdr-subcard-metrics { padding: 0; }
#kt_activitiesBenchApplication .cdr-metric-list { display: flex; flex-direction: column; height: 100%; justify-content: space-evenly; padding: 8px 20px 8px 16px; }
#kt_activitiesBenchApplication .cdr-metric-row { display: flex; align-items: center; gap: 12px; padding: 9px 0; border-bottom: 1px solid #f0f4f8; }
#kt_activitiesBenchApplication .cdr-metric-row:last-child { border-bottom: none; }
#kt_activitiesBenchApplication .cdr-metric-icon { width: 36px; height: 36px; border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
#kt_activitiesBenchApplication .cdr-metric-icon svg { width: 16px; height: 16px; }
#kt_activitiesBenchApplication .cdr-metric-icon.ci-blue { background: #eff6ff; color: #2563eb; }
#kt_activitiesBenchApplication .cdr-metric-icon.ci-green { background: #ecfdf5; color: #16a34a; }
#kt_activitiesBenchApplication .cdr-metric-icon.ci-amber { background: #fffbeb; color: #d97706; }
#kt_activitiesBenchApplication .cdr-metric-icon.ci-purple { background: #f5f3ff; color: #7c3aed; }
#kt_activitiesBenchApplication .cdr-metric-label { flex: 1 1 auto; font-size: 12.5px; font-weight: 500; color: #475569; font-family: 'Poppins', sans-serif; min-width: 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
#kt_activitiesBenchApplication .cdr-metric-val { font-size: 13px; font-weight: 700; color: #0f172a; text-align: right; white-space: nowrap; font-family: 'Poppins', sans-serif; }

/* ===== CARD 2: PIPELINE STAT SUB-CARDS ===== */
#kt_activitiesBenchApplication .cdr-pipeline-section {
    display: grid !important;
    grid-template-columns: repeat(6, minmax(0, 1fr)) !important;
    gap: 10px !important;
    flex-shrink: 0 !important;
    margin: 0 16px !important;
    width: calc(100% - 32px) !important;
    max-width: calc(100% - 32px) !important;
    box-sizing: border-box !important;
}
#kt_activitiesBenchApplication .cdr-pipeline-card { background: #fff; border-radius: 8px !important; padding: 14px 12px; border: 1px solid #e2e8f0; display: flex; align-items: center; gap: 10px; box-shadow: 0 1px 2px rgba(15,23,42,.04); transition: box-shadow .18s ease, transform .18s ease, border-color .18s ease; cursor: default; min-width: 0; }
#kt_activitiesBenchApplication .cdr-pipeline-card:hover { box-shadow: 0 4px 14px rgba(15,23,42,.08); transform: translateY(-2px); border-color: #bfdbfe; }
#kt_activitiesBenchApplication .cdr-pipeline-icon { width: 38px; height: 38px; border-radius: 10px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
#kt_activitiesBenchApplication .cdr-pipeline-icon svg { width: 16px !important; height: 16px !important; flex-shrink: 0; }
#kt_activitiesBenchApplication .cpi-blue { background: #dbeafe; color: #1d4ed8; }
#kt_activitiesBenchApplication .cpi-green { background: #d1fae5; color: #059669; }
#kt_activitiesBenchApplication .cpi-purple { background: #ede9fe; color: #7c3aed; }
#kt_activitiesBenchApplication .cpi-amber { background: #fef3c7; color: #d97706; }
#kt_activitiesBenchApplication .cpi-teal { background: #ccfbf1; color: #0d9488; }
#kt_activitiesBenchApplication .cpi-indigo { background: #e0e7ff; color: #4338ca; }
#kt_activitiesBenchApplication .cdr-pipeline-info { flex: 1; min-width: 0; }
#kt_activitiesBenchApplication .cdr-pipeline-label { font-size: 10.5px; color: #94a3b8; font-weight: 500; font-family: 'Poppins', sans-serif; margin-bottom: 4px; white-space: nowrap; }
#kt_activitiesBenchApplication .cdr-pipeline-value { font-size: 13px; font-weight: 700; color: #0f172a; font-family: 'Poppins', sans-serif; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; line-height: 1.3; }
#kt_activitiesBenchApplication .cpi-unit { font-size: 11px; font-weight: 500; color: #64748b; font-family: 'Poppins', sans-serif; }

/* ===== CARD 3: TABS CARD ===== */
#kt_activitiesBenchApplication .cdr-tabs-card .page-tabs { border-radius: 0; background: #fff; border-bottom: 1px solid #e2e8f0; display: flex; padding: 0 16px; gap: 8px; }
#kt_activitiesBenchApplication .cdr-tabs-card .page-tabs .tab {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 14px 16px;
    font-size: 13px !important;
    font-weight: 500;
    color: #64748b;
    border-bottom: 2px solid transparent;
    text-decoration: none;
    cursor: pointer;
    transition: all 0.15s ease;
}
#kt_activitiesBenchApplication .cdr-tabs-card .page-tabs .tab:hover { color: #0f172a; }
#kt_activitiesBenchApplication .cdr-tabs-card .page-tabs .tab.tab-active {
    color: #2563eb;
    border-bottom-color: #2563eb;
    font-weight: 600;
}
#kt_activitiesBenchApplication .cdr-tabs-card .page-tabs .tab-icon { width: 16px; height: 16px; }
#kt_activitiesBenchApplication .cdr-tabs-card .page-content { background: #f8fafc; border-radius: 0 0 8px 8px; padding: 16px 20px; min-height: 400px; }

/* ===== PROFILE OVERVIEW 2-COL GRID & CARDSHOW ===== */
#kt_activitiesBenchApplication #personal_details .content-split { display: grid !important; grid-template-columns: minmax(0, 1fr) 370px; gap: 16px; align-items: start; }
@media (max-width: 860px) { #kt_activitiesBenchApplication #personal_details .content-split { grid-template-columns: 1fr !important; } }

#kt_activitiesBenchApplication #personal_details .cardshow {
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 1px 3px rgba(0,0,0,.05), 0 4px 12px rgba(0,0,0,.03);
    margin-bottom: 16px;
    padding: 0 !important;
}
#kt_activitiesBenchApplication #personal_details .card-header-sidebar {
    display: flex;
    align-items: center;
    gap: 10px;
    margin: 0 !important;
    padding: 12px 16px !important;
    background: #fff;
    border-bottom: 1px solid #f0f4f8;
}
#kt_activitiesBenchApplication #personal_details .card-header-sidebar .card-icon {
    width: 28px;
    height: 28px;
    border-radius: 6px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}
#kt_activitiesBenchApplication #personal_details .card-header-sidebar .card-icon svg { width: 14px; height: 14px; }
#kt_activitiesBenchApplication #personal_details .card-title {
    font-size: 13px !important;
    font-weight: 700 !important;
    color: #0f172a;
    margin: 0;
    font-family: 'Poppins', sans-serif;
}
#kt_activitiesBenchApplication #personal_details .summary-text {
    font-size: 12.5px !important;
    line-height: 1.65 !important;
    color: #334155;
    padding: 14px 16px !important;
    margin: 0 !important;
    font-family: 'Poppins', sans-serif;
    word-break: break-word;
    text-align: left !important;
}
#kt_activitiesBenchApplication #personal_details .skills-table {
    padding: 0 16px 14px 16px !important;
    width: 100% !important;
}
#kt_activitiesBenchApplication #personal_details .skills-thead {
    font-size: 12px !important;
    display: grid;
    grid-template-columns: 1fr 100px 140px;
    gap: 16px;
    padding: 10px 0;
    border-bottom: 1px solid #e2e8f0;
    font-weight: 600;
    color: #64748b;
}
#kt_activitiesBenchApplication #personal_details .skills-row {
    display: grid;
    grid-template-columns: 1fr 100px 140px;
    gap: 16px;
    padding: 11px 0;
    border-bottom: 1px solid #f1f5f9;
    align-items: center;
}
#kt_activitiesBenchApplication #personal_details .skill-name-cell { font-size: 12px !important; }
#kt_activitiesBenchApplication #personal_details .skill-exp-cell { font-size: 12px !important; color: #475569; }
#kt_activitiesBenchApplication #personal_details .skill-rating-cell { display: flex; align-items: center; gap: 6px; }
#kt_activitiesBenchApplication #personal_details .stars { display: flex; gap: 2px; }
#kt_activitiesBenchApplication #personal_details .star-skill { width: 13px; height: 13px; color: #eab308; }
#kt_activitiesBenchApplication #personal_details .star-empty { color: #e2e8f0; }
#kt_activitiesBenchApplication #personal_details .rating-text { font-size: 12px !important; color: #64748b; font-weight: 500; }

#kt_activitiesBenchApplication #personal_details .comp-list { padding: 6px 16px 14px 16px !important; }
#kt_activitiesBenchApplication #personal_details .comp-row { display: flex; align-items: center; justify-content: space-between; padding: 7px 0; border-bottom: 1px solid #f1f5f9; }
#kt_activitiesBenchApplication #personal_details .comp-row:last-child { border-bottom: none; }
#kt_activitiesBenchApplication #personal_details .comp-label { font-size: 12px !important; color: #64748b; }
#kt_activitiesBenchApplication #personal_details .comp-value { font-size: 12px !important; font-weight: 600 !important; color: #0f172a; }
#kt_activitiesBenchApplication #personal_details .comp-badge { font-size: 11px !important; padding: 2px 7px; border-radius: 4px; background: #eef2ff; color: #2563eb; font-weight: 600; }

#kt_activitiesBenchApplication #personal_details .contact-list { padding: 8px 16px 10px 16px !important; }
#kt_activitiesBenchApplication #personal_details .contact-row { display: flex; align-items: center; justify-content: space-between; padding: 7px 0; border-bottom: 1px solid #f1f5f9; }
#kt_activitiesBenchApplication #personal_details .contact-row:last-child { border-bottom: none; }
#kt_activitiesBenchApplication #personal_details .contact-label { display: flex; align-items: center; gap: 6px; font-size: 12px !important; color: #64748b; }
#kt_activitiesBenchApplication #personal_details .contact-label svg { width: 13px; height: 13px; color: #94a3b8; }
#kt_activitiesBenchApplication #personal_details .contact-value { font-size: 12px !important; font-weight: 600 !important; color: #0f172a; text-decoration: none; }

#kt_activitiesBenchApplication #personal_details .contact-actions {
    display: flex !important;
    flex-direction: column !important;
    gap: 8px !important;
    padding: 0 16px 14px 16px !important;
}
#kt_activitiesBenchApplication #personal_details .contact-actions .btn-whatsapp,
#kt_activitiesBenchApplication #personal_details .contact-actions .btn-linkedin {
    width: 100% !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    gap: 6px !important;
    font-size: 12px !important;
    font-weight: 600 !important;
    padding: 8px 12px !important;
    border-radius: 8px !important;
    white-space: nowrap !important;
    text-align: center !important;
    box-sizing: border-box !important;
    border: none;
    cursor: pointer;
    text-decoration: none !important;
}
#kt_activitiesBenchApplication #personal_details .contact-actions .btn-whatsapp {
    background: #25d366 !important;
    color: #fff !important;
}
#kt_activitiesBenchApplication #personal_details .contact-actions .btn-whatsapp:hover { background: #1ebd5a !important; color: #fff; }
#kt_activitiesBenchApplication #personal_details .contact-actions .btn-linkedin {
    background: #0a66c2 !important;
    color: #fff !important;
}
#kt_activitiesBenchApplication #personal_details .contact-actions .btn-linkedin:hover { background: #004182 !important; color: #fff; }
#kt_activitiesBenchApplication #personal_details .contact-actions .btn-icon { width: 14px !important; height: 14px !important; flex-shrink: 0 !important; }

#kt_activitiesBenchApplication #personal_details .rm-comments-body {
    padding: 14px 16px !important;
    font-size: 12.5px !important;
    line-height: 1.6 !important;
    color: #334155;
    font-family: 'Poppins', sans-serif;
    word-break: break-word;
}
#kt_activitiesBenchApplication #personal_details .rm-details-body { padding: 14px 16px !important; }
#kt_activitiesBenchApplication #personal_details .rm-card-content h4 { font-size: 13px !important; font-weight: 700 !important; color: #0f172a; margin: 0 0 6px 0 !important; }
#kt_activitiesBenchApplication #personal_details .rm-contact-list { display: flex; flex-direction: column; gap: 6px !important; }
#kt_activitiesBenchApplication #personal_details .rm-contact-item { display: flex; align-items: center; gap: 8px !important; font-size: 12px !important; color: #334155 !important; }
#kt_activitiesBenchApplication #personal_details .rm-contact-item svg { width: 13px !important; height: 13px !important; color: #64748b !important; }

#kt_activitiesBenchApplication .confidentiality-banner {
    display: flex;
    align-items: center;
    gap: 8px;
    background: #fffbeb;
    border: 1px solid #fef3c7;
    border-radius: 8px;
    padding: 10px 14px;
    margin-top: 16px;
    font-size: 12px;
    color: #92400e;
}
#kt_activitiesBenchApplication .confidentiality-banner svg { width: 16px; height: 16px; flex-shrink: 0; color: #d97706; }
#kt_activitiesBenchApplication .confidentiality-banner p { margin: 0; }

@media (max-width: 1100px) { #kt_activitiesBenchApplication .cdr-pipeline-section { grid-template-columns: repeat(3, minmax(0, 1fr)) !important; gap: 8px !important; } }
@media (max-width: 640px) { #kt_activitiesBenchApplication .cdr-pipeline-section { grid-template-columns: repeat(2, minmax(0, 1fr)) !important; } #kt_activitiesBenchApplication .cdr-hero-card { grid-template-columns: 1fr !important; } }
</style>

<div class="page-wrapper" id="bench_application_show_right_bar_wrapper">
  {{-- ===== TOP ACTION BAR ===== --}}
  <div class="cdr-topbar" aria-label="Bench candidate approval actions">
    <div class="cdr-topbar-group">
      {{-- Super Admin Marketplace Clearance Governance Action --}}
      @if((int)@$benchApplication->superadminstatus === 1)
        <span class="badge badge-light-success fw-bolder px-3 py-2 fs-7 me-1" style="background:#e8fff3; color:#17c653; border:1px solid #b7f5d0;">
          <i class="fa fa-check-circle me-1 text-success"></i> Market Approved (Live)
        </span>
        <button type="button" onclick="rejectCandidateFromDrawer({{ $benchApplication->id }})" class="cdr-action-btn cdr-btn-secondary text-danger border-danger" title="Reject candidate from marketplace">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-danger"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
          Reject Clearance
        </button>
      @else
        <button type="button" onclick="approveCandidateFromDrawer({{ $benchApplication->id }})" class="cdr-action-btn cdr-btn-stage" id="btnDrawerApproveCandidate" title="Approve candidate for global marketplace clearance">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
          Approve Clearance
        </button>
        @if((int)@$benchApplication->superadminstatus !== 2)
          <button type="button" onclick="rejectCandidateFromDrawer({{ $benchApplication->id }})" class="cdr-action-btn cdr-btn-secondary text-danger" title="Reject candidate from marketplace">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            Reject Clearance
          </button>
        @endif
      @endif

      @if(!empty($resumeDownloadUrl))
        <a href="{{ $resumeDownloadUrl }}" target="_blank" download class="cdr-action-btn cdr-btn-secondary cdr-btn-dl-center" title="Download candidate résumé">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
          Download Resume
        </a>
      @endif
    </div>

    <button type="button" class="cdr-topbar-close" id="kt_activitiesBenchApplication_close" aria-label="Close">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
      </svg>
    </button>
  </div>

  {{-- ===== CARD 1: HERO IDENTITY + KEY HIGHLIGHTS ===== --}}
  <div class="cdr-outer-card">
    <div class="cdr-hero-card">

      {{-- Sub-Card A: Candidate Identity --}}
      <div class="cdr-subcard cdr-subcard-id">
        <div class="cdr-id-top">
          <div class="cdr-avatar-wrap">
            <div class="cdr-avatar-sq" aria-hidden="true">
              @if(!empty($benchApplication->photo) && file_exists(public_path('user-uploads/bench_photos/' . $benchApplication->photo)))
                <img src="{{ $photo }}" alt="{{ $benchApplication->full_name }}" />
              @else
                {{ $initials }}
              @endif
            </div>
          </div>

          <div class="cdr-id-body">
            <div class="cdr-name-row">
              <h2 class="cdr-name">{{ ucwords(@$benchApplication->full_name ?: '—') }}</h2>
              @if((int)@$benchApplication->superadminstatus === 1)
                <span class="badge badge-light-success fw-bolder">Approved (Live)</span>
              @elseif((int)@$benchApplication->superadminstatus === 2)
                <span class="badge badge-light-danger fw-bolder">Rejected</span>
              @else
                <span class="badge badge-light-warning fw-bolder">Pending Clearance</span>
              @endif
            </div>

            <p class="cdr-role">{{ ucwords(@$benchApplication->current_designation ?: '—') }}</p>

            <div class="cdr-meta-grid">
              {{-- Row 1: Sponsoring Company | Location --}}
              <span class="cdr-meta-item" title="{{ ucwords($posted_by_company) }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 21h18"/><path d="M5 21V7l7-4 7 4v14"/></svg>
                {{ ucwords($posted_by_company) }}
              </span>

              <span class="cdr-meta-item" title="{{ @$city_name ? $city_name . ', ' : '' }}{{ @$state_name ? ucwords($state_name) . ', ' : '' }}{{ ucwords(@$country_name) }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                {{ @$city_name ? $city_name . ', ' : '' }}{{ @$state_name ? ucwords($state_name) . ', ' : '' }}{{ ucwords(@$country_name) ?: '—' }}
              </span>

              {{-- Row 2: Bench ID | Upload Date --}}
              <span class="cdr-meta-item">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
                Bench ID: BJ-{{ @$benchApplication->id }}
              </span>

              <span class="cdr-meta-item">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                Upload: {{ @$addedDate }}
              </span>
            </div>
          </div>
        </div>

        @if(!empty($skillsArr))
          <div class="cdr-skills-bar">
            @foreach($skillsArr as $sval)
              <span class="cdr-skill-tag">{{ ucwords(strtolower(trim($sval))) }}</span>
            @endforeach
          </div>
        @endif
      </div>

      {{-- Sub-Card B: Availability, Skills & Work Authorisation --}}
      <div class="cdr-subcard cdr-subcard-metrics">
        <div class="cdr-metric-list" role="list" aria-label="Candidate availability metrics">
          <div class="cdr-metric-row" role="listitem">
            <div class="cdr-metric-icon ci-purple">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2L2 7l10 5 10-5-10-5z"/><path d="M2 17l10 5 10-5"/><path d="M2 12l10 5 10-5"/></svg>
            </div>
            <span class="cdr-metric-label">Skills & Expertise</span>
            <span class="cdr-metric-val">{{ $skillsCountDisplay }}</span>
          </div>

          <div class="cdr-metric-row" role="listitem">
            <div class="cdr-metric-icon ci-amber">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
            </div>
            <span class="cdr-metric-label">Availability</span>
            <span class="cdr-metric-val">{{ @$np }}</span>
          </div>

          <div class="cdr-metric-row" role="listitem">
            <div class="cdr-metric-icon ci-green">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
            </div>
            <span class="cdr-metric-label">Engagement Type</span>
            <span class="cdr-metric-val">{{ ucwords(@$benchApplication->employmentType ?: 'Full Time') }}</span>
          </div>

          <div class="cdr-metric-row" role="listitem">
            <div class="cdr-metric-icon ci-blue">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
            </div>
            <span class="cdr-metric-label">Work Auth / Visa</span>
            <span class="cdr-metric-val" title="{{ $workAuthShortDisplay }}">{{ $workAuthShortDisplay }}</span>
          </div>
        </div>
      </div>

    </div>
  </div>

  {{-- ===== CARD 2: 6 PIPELINE STAT CARDS ===== --}}
  <div class="cdr-pipeline-section" aria-label="Candidate at-a-glance">
    <div class="cdr-pipeline-card">
      <div class="cdr-pipeline-icon cpi-blue">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
      </div>
      <div class="cdr-pipeline-info">
        <div class="cdr-pipeline-label">Total Experience</div>
        <div class="cdr-pipeline-value">{{ @$formattedExperience }}<span class="cpi-unit"> Yrs</span></div>
      </div>
    </div>

    <div class="cdr-pipeline-card">
      <div class="cdr-pipeline-icon cpi-green">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="20" x2="12" y2="10"/><line x1="18" y1="20" x2="18" y2="4"/><line x1="6" y1="20" x2="6" y2="16"/></svg>
      </div>
      <div class="cdr-pipeline-info">
        <div class="cdr-pipeline-label">Relevant Exp</div>
        <div class="cdr-pipeline-value">{{ @$rel_formattedExperience }}<span class="cpi-unit"> Yrs</span></div>
      </div>
    </div>

    <div class="cdr-pipeline-card">
      <div class="cdr-pipeline-icon cpi-purple">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
      </div>
      <div class="cdr-pipeline-info">
        <div class="cdr-pipeline-label">Gender</div>
        <div class="cdr-pipeline-value">{{ ucwords(@$benchApplication->gender ?: '—') }}</div>
      </div>
    </div>

    <div class="cdr-pipeline-card">
      <div class="cdr-pipeline-icon cpi-amber">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>
      </div>
      <div class="cdr-pipeline-info">
        <div class="cdr-pipeline-label">Nationality</div>
        <div class="cdr-pipeline-value" title="{{ ucwords(@$benchApplication->nationality) }}">{{ \Illuminate\Support\Str::limit(ucwords(@$benchApplication->nationality), 10, '…') ?: '—' }}</div>
      </div>
    </div>

    <div class="cdr-pipeline-card">
      <div class="cdr-pipeline-icon cpi-teal">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="3" width="20" height="14" rx="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>
      </div>
      <div class="cdr-pipeline-info">
        <div class="cdr-pipeline-label">Work Mode</div>
        <div class="cdr-pipeline-value">{{ ucwords(@$benchApplication->wm ?: '—') }}</div>
      </div>
    </div>

    <div class="cdr-pipeline-card">
      <div class="cdr-pipeline-icon cpi-indigo">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c0 1.66 1.79 3 4 3s4-1.34 4-3v-5"/></svg>
      </div>
      <div class="cdr-pipeline-info">
        <div class="cdr-pipeline-label">Education</div>
        <div class="cdr-pipeline-value" title="{{ ucwords(@$benchApplication->qualification_name) }}">{{ \Illuminate\Support\Str::limit(ucwords(@$benchApplication->qualification_name), 12, '…') ?: '—' }}</div>
      </div>
    </div>
  </div>

  {{-- ===== CARD 3: TABS & CONTENT CARD ===== --}}
  <div class="cdr-outer-card cdr-tabs-card">
    <nav class="page-tabs" aria-label="Profile sections" role="tablist">
      <a href="#personal_details" class="tab tab-active" aria-selected="true" data-tab-target="#personal_details">
        <svg class="tab-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
          <circle cx="12" cy="7" r="4"/>
        </svg>
        Profile Overview
      </a>
      @if(!empty($resumeDownloadUrl))
        <a href="#resume_cv" class="tab" aria-selected="false" data-tab-target="#resume_cv">
          <svg class="tab-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
            <polyline points="14 2 14 8 20 8"/>
            <line x1="16" y1="13" x2="8" y2="13"/>
            <line x1="16" y1="17" x2="8" y2="17"/>
          </svg>
          Resume / CV
        </a>
      @endif
    </nav>

    <main class="page-content">
      {{-- Tab 1: Profile Overview --}}
      <div class="tab-pane show active" id="personal_details">
        <h2 class="overview-title" style="font-size:14px;font-weight:700;color:#0f172a;margin-bottom:14px;">Profile Overview</h2>

        <div class="content-split">
          {{-- Main Column --}}
          <div class="content-main">
            {{-- Professional Summary --}}
            <section class="cardshow">
              <div class="card-header-sidebar">
                <div class="card-icon" style="background: #f3e8ff; color: #a855f7;">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                    <polyline points="14 2 14 8 20 8"/>
                    <line x1="16" y1="13" x2="8" y2="13"/>
                    <line x1="16" y1="17" x2="8" y2="17"/>
                  </svg>
                </div>
                <h3 class="card-title">Professional Summary</h3>
              </div>
              <p class="summary-text">{{ @$benchApplication->careersummary ?: 'No summary provided.' }}</p>
            </section>

            {{-- Key Skills & Experiences Table --}}
            <section class="cardshow">
              <div class="card-header-sidebar">
                <div class="card-icon" style="background: #f0fdf4; color: #16a34a;">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                    <circle cx="9" cy="7" r="4"/>
                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                    <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                  </svg>
                </div>
                <h3 class="card-title">Talent Key Skills & Experiences</h3>
              </div>
              <div class="skills-table">
                <div class="skills-thead">
                  <div>Skill</div>
                  <div>Experience</div>
                  <div>Ratings</div>
                </div>
                @if(!empty($skillExperiencesCount) && $skillExperiencesCount > 0)
                  @foreach($skillExperiences as $exp)
                    <div class="skills-row">
                      <div class="skill-name-cell">
                        <span class="skill-name-text" style="font-weight: 600; color: #1e293b;">{{ e($exp->skill_key) }}</span>
                      </div>
                      <div class="skill-exp-cell">{{ e($exp->year ?? '0') }}.{{ e($exp->month ?? '0') }} Years</div>
                      <div class="skill-rating-cell">
                        <div class="stars">
                          @for($i = 1; $i <= 5; $i++)
                            <svg class="star-skill {{ (@$exp->rating >= $i ? '' : 'star-empty') }}" viewBox="0 0 24 24" fill="currentColor" stroke="none"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                          @endfor
                        </div>
                        <span class="rating-text">{{ e(@$exp->rating ?? 0) }} / 5</span>
                      </div>
                    </div>
                  @endforeach
                @elseif(!empty($skillsArr))
                  @foreach($skillsArr as $skillItem)
                    <div class="skills-row">
                      <div class="skill-name-cell">
                        <span class="skill-name-text" style="font-weight: 600; color: #1e293b;">{{ ucwords(strtolower(trim($skillItem))) }}</span>
                      </div>
                      <div class="skill-exp-cell">{{ $formattedExperience }} Yrs</div>
                      <div class="skill-rating-cell">
                        <div class="stars">
                          <svg class="star-skill" viewBox="0 0 24 24" fill="currentColor" stroke="none"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                          <svg class="star-skill" viewBox="0 0 24 24" fill="currentColor" stroke="none"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                          <svg class="star-skill" viewBox="0 0 24 24" fill="currentColor" stroke="none"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                          <svg class="star-skill star-empty" viewBox="0 0 24 24" fill="currentColor" stroke="none"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                          <svg class="star-skill star-empty" viewBox="0 0 24 24" fill="currentColor" stroke="none"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                        </div>
                        <span class="rating-text">3 / 5</span>
                      </div>
                    </div>
                  @endforeach
                @else
                  <div class="text-muted p-4 text-center" style="font-size:12px;">No skills mapped.</div>
                @endif
              </div>
            </section>

            {{-- Additional Details / Work History --}}
            @if(!empty($job_additional_detail) && count($job_additional_detail) > 0)
              <section class="cardshow">
                <div class="card-header-sidebar">
                  <div class="card-icon" style="background: #eef2ff; color: #4338ca;">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                      <rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/>
                    </svg>
                  </div>
                  <h3 class="card-title">Additional Profile Details</h3>
                </div>
                <div style="padding: 10px 16px 14px 16px;">
                  @foreach($job_additional_detail as $addVal)
                    <div style="display: flex; justify-content: space-between; padding: 6px 0; border-bottom: 1px solid #f1f5f9; font-size: 12px;">
                      <span style="color: #64748b; font-weight: 500;">{{ ucwords(str_replace('_', ' ', $addVal->custom_keys ?? '')) }}</span>
                      <span style="color: #0f172a; font-weight: 600;">{{ $addVal->custom_keys_value ?? '—' }}</span>
                    </div>
                  @endforeach
                </div>
              </section>
            @endif
          </div>

          {{-- Sidebar Column --}}
          <aside class="content-sidebar">
            {{-- Relationship Manager Comments --}}
            <section class="cardshow">
              <div class="card-header-sidebar">
                <div class="card-icon" style="background: #eff6ff; color: #2563eb;">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                  </svg>
                </div>
                <h3 class="card-title">Relationship Manager Comments</h3>
              </div>
              <div class="rm-comments-body">
                @php
                  $displayRmComment = !empty($benchApplication->rm_comments) ? $benchApplication->rm_comments : @$benchApplication->recruiter_note;
                @endphp
                @if(!empty($displayRmComment))
                  <div class="rm-comments-content">
                    @if(strip_tags($displayRmComment) !== $displayRmComment)
                      {!! clean($displayRmComment) !!}
                    @else
                      {!! nl2br(e($displayRmComment)) !!}
                    @endif
                  </div>
                @else
                  <p class="text-muted font-italic mb-0" style="font-size: 12px; color: #94a3b8; font-style: italic; margin: 0;">No Relationship Manager comments added yet.</p>
                @endif
              </div>
            </section>

            {{-- Compensation & Financial Details --}}
            <section class="cardshow">
              <div class="card-header-sidebar">
                <div class="card-icon" style="background: #fffbeb; color: #d97706;">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="1" y="4" width="22" height="16" rx="2" ry="2"/>
                    <line x1="1" y1="10" x2="23" y2="10"/>
                  </svg>
                </div>
                <h3 class="card-title">Compensation Details</h3>
              </div>
              <div class="comp-list">
                @php
                  $pay_rate_mode = (@$benchApplication->pay_rate_mode) ? @$benchApplication->pay_rate_mode : 0;
                  $bill_rate_mode = (@$benchApplication->bill_rate_mode) ? @$benchApplication->bill_rate_mode : 0;
                  $comission_type = @$benchApplication->comission_type;
                  $comission_type_name = ($comission_type == 1) ? 'Free' : (($comission_type == 2) ? 'Paid' : 'Free');
                @endphp
                <div class="comp-row">
                  <span class="comp-label">Pay Rate</span>
                  <span class="comp-value">{!! $current_ctc_cur !!} {{ @$benchApplication->pay_rate_amount }} / {{ $salaryMode[$pay_rate_mode] ?? 'Annual' }}</span>
                </div>
                <div class="comp-row">
                  <span class="comp-label">Bill Rate</span>
                  <span class="comp-value">{!! @$current_ectc_cur !!} {{ @$benchApplication->bill_rate_amount }} / {{ $salaryMode[$bill_rate_mode] ?? 'Annual' }}</span>
                </div>
                <div class="comp-row">
                  <span class="comp-label">Payment Model</span>
                  <span class="comp-badge">{{ $salaryMode[$bill_rate_mode] ?? 'Annual' }}</span>
                </div>
                <div class="comp-row">
                  <span class="comp-label">Currency</span>
                  <span class="comp-value">{{ $current_cur ?: 'INR' }}</span>
                </div>
                <div class="comp-row">
                  <span class="comp-label">Commercial Model</span>
                  <span class="comp-value">{{ @$comission_type_name }} Service</span>
                </div>
                <div class="comp-row">
                  <span class="comp-label">Engagement Type</span>
                  <span class="comp-value">{{ @$benchApplication->employmentType ?: 'Full Time' }}</span>
                </div>
              </div>
            </section>

            {{-- Contact Details --}}
            <section class="cardshow">
              <div class="card-header-sidebar">
                <div class="card-icon" style="background: #eff6ff; color: #2563eb;">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                    <circle cx="12" cy="7" r="4"/>
                  </svg>
                </div>
                <h3 class="card-title">Contact Details</h3>
              </div>
              <div class="contact-list">
                <div class="contact-row">
                  <span class="contact-label">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                    Email ID
                  </span>
                  <a href="mailto:{{ @$benchApplication->email }}" class="contact-value">{{ @$benchApplication->email ?: '—' }}</a>
                </div>
                <div class="contact-row">
                  <span class="contact-label">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                    Contact Number
                  </span>
                  <a href="tel:{{ $candidatePhoneData['dial'] }}{{ $cleanPhone }}" class="contact-value" style="display: flex; align-items: center;">
                    <div class="fw-bolder text-gray-800 fs-6" style="display: flex; align-items: center; gap: 4px;">
                      <div class="iti__flag iti__{{ $candidatePhoneData['iso'] }}"></div>
                      <span>{{ $candidatePhoneData['dial'] }}</span>
                    </div>
                    <span class="fw-bolder text-gray-800 fs-6" style="margin-left: 6px;">{{ @$benchApplication->phone }}</span>
                  </a>
                </div>
              </div>
              <div class="contact-actions">
                <a href="https://api.whatsapp.com/send?phone={{ $whatsAppPhone }}" target="_blank" class="btn btn-sm btn-whatsapp">
                  <svg class="btn-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"/>
                  </svg>
                  Chat on WhatsApp
                </a>
                @php
                  $linkedin = @$benchApplication->linkedin;
                  if (!empty($linkedin) && !preg_match('/^(https?:\/\/|www\.)/i', $linkedin)) {
                      $linkedin = 'https://' . $linkedin;
                  }
                @endphp
                @if(!empty(@$benchApplication->linkedin))
                  <a href="{{ $linkedin ?? 'javascript:;' }}" target="{{ $linkedin ? '_blank' : '_self' }}" class="btn btn-sm btn-linkedin">
                    <svg class="btn-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                      <path d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-2-2 2 2 0 0 0-2 2v7h-4v-7a6 6 0 0 1 6-6z"/>
                      <rect x="2" y="9" width="4" height="12"/>
                      <circle cx="4" cy="4" r="2"/>
                    </svg>
                    Connect on LinkedIn
                  </a>
                @endif
              </div>
            </section>

            {{-- Relationship Manager Details --}}
            <section class="cardshow">
              <div class="card-header-sidebar">
                <div class="card-icon" style="background: #f3e8ff; color: #a855f7;">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                    <circle cx="9" cy="7" r="4"/>
                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                    <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                  </svg>
                </div>
                <h3 class="card-title">Relationship Manager Details</h3>
              </div>
              <div class="rm-details-body">
                @if(!empty($bench_assign_row) && count($bench_assign_row) > 0)
                  @foreach($bench_assign_row as $key => $assignVal)
                    @php
                      $rmPhoneData = $resolvePhone($assignVal->calling_code ?? 'in');
                    @endphp
                    <div class="rm-card-content" style="{{ !$loop->first ? 'margin-top: 10px; padding-top: 10px; border-top: 1px solid #f1f5f9;' : '' }}">
                      <h4>{{ ucwords(@$assignVal->name ?: '—') }}</h4>
                      <div class="rm-contact-list">
                        @if(!empty(@$assignVal->mobile))
                          <div class="rm-contact-item">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                            <span style="display: inline-flex; align-items: center; gap: 4px;">
                              <span class="iti__flag iti__{{ $rmPhoneData['iso'] }}" style="display: inline-block;"></span>
                              <span style="font-weight: 600; color: #0f172a;">{{ $rmPhoneData['dial'] }}</span>
                            </span>
                            <span style="font-weight: 500; color: #334155;">{{ @$assignVal->mobile }}</span>
                          </div>
                        @endif
                        @if(!empty(@$assignVal->email))
                          <div class="rm-contact-item">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                            <a href="mailto:{{ @$assignVal->email }}">{{ @$assignVal->email }}</a>
                          </div>
                        @endif
                      </div>
                    </div>
                  @endforeach
                @else
                  <div style="padding: 10px 0; font-size: 12px; color: #94a3b8; font-style: italic;">No Relationship Manager assigned yet.</div>
                @endif
              </div>
            </section>
          </aside>
        </div>
      </div>

      {{-- Tab 2: Resume / CV Preview --}}
      @if(!empty($resumeDownloadUrl))
        <div class="tab-pane" id="resume_cv" style="display:none;">
          <div class="cardshow" style="padding: 16px;">
            <div class="d-flex align-items-center justify-content-between mb-4">
              <h3 class="card-title" style="font-size:14px; font-weight:700; color:#0f172a; margin:0;">
                Candidate Résumé Preview
              </h3>
              <a href="{{ $resumeDownloadUrl }}" target="_blank" download class="btn btn-sm btn-primary">
                <i class="fa fa-download me-1"></i> Download Résumé
              </a>
            </div>

            @if($isPdfResume)
              <div style="width:100%; height:750px; border-radius:8px; overflow:hidden; border:1px solid #e2e8f0; background:#fff;">
                <iframe src="{{ $resumeDownloadUrl }}" style="width:100%; height:100%; border:none;"></iframe>
              </div>
            @else
              <div class="text-center p-12" style="background:#f8fafc; border:1px dashed #cbd5e1; border-radius:8px;">
                <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#64748b" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="mb-3">
                  <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/>
                </svg>
                <h4 style="font-size:14px; font-weight:600; color:#1e293b; margin-bottom:6px;">Office Document Résumé</h4>
                <p style="font-size:12px; color:#64748b; margin-bottom:14px;">This candidate has uploaded a DOC/DOCX document résumé.</p>
                <a href="{{ $resumeDownloadUrl }}" target="_blank" download class="btn btn-primary btn-sm">
                  <i class="fa fa-download me-1"></i> Download Document ({{ strtoupper(pathinfo($benchApplication->resume, PATHINFO_EXTENSION)) }})
                </a>
              </div>
            @endif
          </div>
        </div>
      @endif

      {{-- Confidentiality Banner --}}
      <div class="confidentiality-banner" role="note">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/>
        </svg>
        <p>All candidate information is confidential and intended for Super Administrator governance & clearance only.</p>
      </div>
    </main>
  </div>
</div>

<script>
(function() {
    // Tab switching inside drawer
    $('.page-tabs .tab').on('click', function(e) {
        e.preventDefault();
        $('.page-tabs .tab').removeClass('tab-active').attr('aria-selected', 'false');
        $(this).addClass('tab-active').attr('aria-selected', 'true');

        var targetSelector = $(this).attr('data-tab-target') || $(this).attr('href');
        $('.page-content .tab-pane').hide().removeClass('show active');
        $(targetSelector).show().addClass('show active');
    });
})();
</script>
