@php
    use App\Helper\GeneralMethod;
@endphp
@php
    $resume_url = '<i class="fa fa-download" aria-hidden="true" style="color:#003300"></i>';
    $photo = url('public/avatar.png');
    if (!empty($benchApplication->photo)) {
        $photo = url('/public/user-uploads/bench_photos/' . $benchApplication->photo);
    }
    $country_id = $country_name = $state_name = $state = $city_name = $posted_by_company = '';
    $country_id = @$benchApplication->country;
    $state = @$benchApplication->state;

    $country_name_Arr = GeneralMethod::getCountryName($country_id);
    foreach ($country_name_Arr as $cval) {
        $country_name = ucwords($cval['name']);
    }

    if ($country_id == 101) {
        $state_name = @$benchApplication->state_name;
        $city_name = @$benchApplication->city_name;
    } else {
        if ($state > 0) {
            $state_name_Arr = GeneralMethod::getStateName($country_id, $state);
            foreach ($state_name_Arr as $sval) {
                $state_name = ucwords($sval['name']);
            }
        }
        $city_name = @$benchApplication->city;
    }
    $currencySymbole = GeneralMethod::currencySymbole();
    $currencySymboleforexcell = GeneralMethod::currencySymboleforexcell();
    $current_ctc_cur = $current_ectc_cur = '';
    $current_ctc_cur = $currencySymbole[(@$benchApplication->pay_rate_currency) ? @$benchApplication->pay_rate_currency : 0];
    $current_ectc_cur = $currencySymbole[(@$benchApplication->bill_rate_currency) ? @$benchApplication->bill_rate_currency : 0];
    $current_cur = $currencySymboleforexcell[@$benchApplication->bill_rate_currency ?? 0] ?? ''; 
    $posted_by_company = @$benchApplication->company_name;

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

    // Skills array
    $skillsRaw = trim((string) @$benchApplication->skills);
    $skillsArr = $skillsRaw !== '' ? array_values(array_filter(array_map('trim', explode(',', $skillsRaw)), 'strlen')) : [];

    // Added Date
    $created_at = @$benchApplication->created_at;
    $addedDate = !empty($created_at) ? date("d-M-Y h:i:s", strtotime($created_at)) : '—';
@endphp

<link href="{{ asset('demo/css/candidates-details.css') }}{{ TM_VERSION }}" rel="stylesheet" type="text/css" />

<style>
.text-justify { text-align: justify !important; }
.iti__flag { margin-top: 4px !important; }

/* ===== DRAWER SCROLLER FIX ===== */
#kt_activitiesBenchApplication {
    overflow-y: auto !important;
    max-height: 100vh !important;
    height: 100vh !important;
}
#bench_application_show_right_bar {
    min-height: 100vh;
    overflow-y: visible !important;
}

/* ===== THREE-CARD LAYOUT & CURVED CARD CORNERS ===== */
#kt_activitiesBenchApplication .page-wrapper,
.page-wrapper {
    display: flex;
    flex-direction: column;
    gap: 12px;
    background: #f1f5f9;
    min-height: auto;
    padding-bottom: 24px;
    overflow-y: visible !important;
}

#kt_activitiesBenchApplication .cdr-outer-card,
#kt_activitiesBenchApplication .card,
#kt_activitiesBenchApplication .content-card { background: #fff; border: 1px solid #e2e8f0; border-radius: 8px !important; box-shadow: 0 1px 3px rgba(15,23,42,.04), 0 4px 12px rgba(15,23,42,.03); overflow: hidden; flex-shrink: 0; margin: 0 16px; }

/* ===== TOP ACTION BAR & OVERFLOW FIX FOR DROPDOWN ===== */
#kt_activitiesBenchApplication .cdr-topbar {
    display: flex !important;
    align-items: center;
    flex-wrap: nowrap;
    gap: 8px;
    padding: 12px 16px;
    background: #fff;
    border-bottom: 1px solid #e2e8f0;
    justify-content: space-between;
    position: relative;
    z-index: 1000;
    overflow: visible !important;
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
#kt_activitiesBenchApplication .cdr-action-btn { display: inline-flex !important; align-items: center; gap: 6px; padding: 7px 14px; border: 1.5px solid #d1d5db; border-radius: 8px; background: #fff; color: #374151; font-family: 'Poppins', sans-serif; font-size: 12.5px; font-weight: 500; cursor: pointer; text-decoration: none; white-space: nowrap; line-height: 1; }
#kt_activitiesBenchApplication .cdr-action-btn.cdr-btn-primary { background: #3b82f6; color: #fff; border-color: #3b82f6; }
#kt_activitiesBenchApplication .cdr-action-btn.cdr-btn-interview { background: #6366f1; color: #fff; border-color: #6366f1; }
#kt_activitiesBenchApplication .cdr-action-btn.cdr-btn-stage { background: #059669; color: #fff; border-color: #059669; }
#kt_activitiesBenchApplication .cdr-action-btn.cdr-btn-secondary { background: #fff; color: #475569; border-color: #e2e8f0; }
#kt_activitiesBenchApplication .cdr-action-btn svg { width: 14px; height: 14px; flex-shrink: 0; }
#kt_activitiesBenchApplication .cdr-topbar-split { width: 1px; height: 20px; background: #e2e8f0; flex-shrink: 0; margin: 0 2px; }
#kt_activitiesBenchApplication .cdr-topbar-close { width: 34px; height: 34px; flex-shrink: 0; display: inline-flex; align-items: center; justify-content: center; background: none; border: 1.5px solid #e2e8f0; border-radius: 8px; cursor: pointer; color: #6b7280; padding: 0; margin-left: 4px; }
#kt_activitiesBenchApplication .cdr-topbar-close:hover { background: #fee2e2; border-color: #fca5a5; color: #dc2626; }

/* ===== DOWNLOAD RESUME DROPDOWN ===== */
#kt_activitiesBenchApplication .cdr-dl-wrap {
    position: relative !important;
    display: inline-block !important;
    z-index: 1001 !important;
    overflow: visible !important;
}
#kt_activitiesBenchApplication .cdr-dl-menu {
    display: none;
    position: absolute !important;
    right: 0 !important;
    top: calc(100% + 6px) !important;
    background: #ffffff !important;
    border: 1px solid #cbd5e1 !important;
    border-radius: 8px !important;
    box-shadow: 0 10px 30px rgba(15, 23, 42, 0.2) !important;
    min-width: 230px !important;
    z-index: 999999 !important;
    overflow: hidden !important;
}
#kt_activitiesBenchApplication .cdr-dl-menu.open {
    display: block !important;
    visibility: visible !important;
    opacity: 1 !important;
}
#kt_activitiesBenchApplication .cdr-dl-item { display: block; padding: 9px 13px; font-size: 12px; font-weight: 500; color: #334155; text-decoration: none; border-bottom: 1px solid #f1f5f9; width: 100%; text-align: left; background: none; border-left: 0; border-right: 0; border-top: 0; }
#kt_activitiesBenchApplication .cdr-dl-item:hover { background: #f8fafc; color: #0f172a; }
#kt_activitiesBenchApplication .cdr-dl-item-logo { display: flex; align-items: center; gap: 6px; width: 100%; padding: 9px 13px; font-size: 12px; font-weight: 600; color: #15803d; background: #f0fdf4; border: 0; border-left: 3px solid #22c55e; cursor: pointer; text-align: left; }
#kt_activitiesBenchApplication .cdr-dl-badge { font-size: 10px; background: #22c55e; color: #fff; border-radius: 4px; padding: 1px 5px; margin-left: auto; font-weight: 700; letter-spacing: .3px; }

/* ===== HERO CARD ===== */
#kt_activitiesBenchApplication .cdr-hero-card { display: grid !important; grid-template-columns: 1.4fr 1fr; background: #fff; border-bottom: 1px solid #f0f4f8; }
#kt_activitiesBenchApplication .cdr-subcard-id { display: flex !important; flex-direction: column !important; gap: 0 !important; align-items: stretch !important; padding: 18px 20px; border-right: 1px solid #f1f5f9; }
#kt_activitiesBenchApplication .cdr-id-top { display: flex; gap: 14px; align-items: flex-start; }
#kt_activitiesBenchApplication .cdr-avatar-wrap { position: relative; flex-shrink: 0; align-self: flex-start; }
#kt_activitiesBenchApplication .cdr-avatar-sq { width: 88px; height: 88px; border-radius: 16px; background: #eef2ff; color: #1e3a8a; font-family: 'Poppins', sans-serif; font-weight: 700; font-size: 26px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; overflow: hidden; }
#kt_activitiesBenchApplication .cdr-avatar-sq img { width: 100%; height: 100%; object-fit: cover; }
#kt_activitiesBenchApplication .cdr-id-body { flex: 1 1 auto; min-width: 0; }
#kt_activitiesBenchApplication .cdr-name-row { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; }
#kt_activitiesBenchApplication .cdr-name { margin: 0; font-size: 18px; font-weight: 700; color: #0f172a; line-height: 1.2; font-family: 'Poppins', sans-serif; }
#kt_activitiesBenchApplication .cdr-role { margin: 3px 0 0 0; font-size: 13px; font-weight: 600; color: #2563eb; line-height: 1.3; font-family: 'Poppins', sans-serif; }
#kt_activitiesBenchApplication .cdr-meta-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 7px 12px; margin-top: 10px; }
#kt_activitiesBenchApplication .cdr-meta-grid .cdr-meta-item { display: flex; align-items: center; gap: 6px; font-size: 12px; font-weight: 500; color: #475569; font-family: 'Poppins', sans-serif; white-space: nowrap; overflow: hidden; min-width: 0; }
#kt_activitiesBenchApplication .cdr-meta-grid .cdr-meta-item svg { width: 13px; height: 13px; flex-shrink: 0; color: #94a3b8; }
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
#kt_activitiesBenchApplication .cdr-pipeline-section { display: grid; grid-template-columns: repeat(6, minmax(0, 1fr)); gap: 10px; flex-shrink: 0; margin: 0 16px; }
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
#kt_activitiesBenchApplication .cdr-tabs-card .page-tabs { border-radius: 0; background: #fff; }
#kt_activitiesBenchApplication .cdr-tabs-card .page-content { background: #f8fafc; border-radius: 0 0 12px 12px; padding: 16px 20px; }

/* ===== PROFILE OVERVIEW 2-COL GRID & CARDSHOW ===== */
#kt_activitiesBenchApplication #personal_details .content-split { display: grid !important; grid-template-columns: minmax(0, 1fr) 370px; gap: 16px; align-items: start; }
@media (max-width: 860px) { #kt_activitiesBenchApplication #personal_details .content-split { grid-template-columns: 1fr !important; } }

#kt_activitiesBenchApplication #personal_details .cardshow { background: #fff; border: 1px solid #e2e8f0; border-radius: 12px; overflow: hidden; box-shadow: 0 1px 3px rgba(0,0,0,.05), 0 4px 12px rgba(0,0,0,.03); margin-bottom: 16px; }
#kt_activitiesBenchApplication #personal_details .card-header-sidebar { display: flex; align-items: center; gap: 10px; padding: 14px 20px; background: #fff; border-bottom: 1px solid #f0f4f8; }
#kt_activitiesBenchApplication #personal_details .card-title { font-size: 13px !important; font-weight: 700 !important; color: #0f172a; margin: 0; font-family: 'Poppins', sans-serif; }
#kt_activitiesBenchApplication #personal_details .summary-text { font-size: 12px !important; line-height: 1.7 !important; color: #334155; padding: 16px 20px; margin: 0; font-family: 'Poppins', sans-serif; }
#kt_activitiesBenchApplication #personal_details .skills-thead { font-size: 12px !important; }
#kt_activitiesBenchApplication #personal_details .skill-name-cell { font-size: 12px !important; }
#kt_activitiesBenchApplication #personal_details .skill-exp-cell { font-size: 12px !important; }
#kt_activitiesBenchApplication #personal_details .rating-text { font-size: 12px !important; }
#kt_activitiesBenchApplication #personal_details .comp-label { font-size: 12px !important; }
#kt_activitiesBenchApplication #personal_details .comp-value { font-size: 12px !important; font-weight: 600 !important; }
#kt_activitiesBenchApplication #personal_details .comp-badge { font-size: 12px !important; }

/* ===== RESPONSIVE ===== */
@media (max-width: 1100px) { #kt_activitiesBenchApplication .cdr-pipeline-section { grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 8px; } }
@media (max-width: 640px) { #kt_activitiesBenchApplication .cdr-pipeline-section { grid-template-columns: repeat(2, minmax(0, 1fr)); } #kt_activitiesBenchApplication .cdr-hero-card { grid-template-columns: 1fr !important; } }
</style>

<div class="page-wrapper">
  {{-- TOP ACTION BAR --}}
  <div class="cdr-topbar" aria-label="Bench candidate actions">
    <div class="cdr-topbar-group">
      @if (in_array('edit_job_applications', $userPermissions) && $benchApplication->company_id == auth()->user()->company_id)
        <a href="{{ route('service_company.bench-candidates.edit', [GeneralMethod::value_encrypt($benchApplication->id)]) }}" class="cdr-action-btn cdr-btn-primary" title="Edit candidate profile">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4 12.5-12.5z"/></svg>
          Edit Profile
        </a>
      @endif

      <a href="javascript:void(0);" data-row-id="{{ $benchApplication->id }}" class="cdr-action-btn cdr-btn-interview stage_sub_stage" data-toggle="modal" data-target="#stage_sub_stage" title="Change Status">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M23 4v6h-6"/><path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"/></svg>
        Change Status
      </a>

      <a href="javascript:void(0);" data-row-id="{{ $benchApplication->id }}" class="cdr-action-btn cdr-btn-stage hmb-listing" title="Add Relationship Manager">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
        Add Relationship Manager
      </a>

      <div class="cdr-topbar-split" role="separator" aria-hidden="true"></div>

      @if(!empty($benchApplication->resume) && in_array(strtolower(pathinfo($benchApplication->resume, PATHINFO_EXTENSION)),['pdf', 'doc', 'docx']))
        @php
            $url = @$benchApplication->resume;
            $hdrMaskContact = ((int) @$benchApplication->company_id !== (int) (auth()->user()->company_id ?? 0));
        @endphp
        <div class="cdr-dl-wrap" id="hdrDlWrapBench">
          <button type="button" class="cdr-action-btn cdr-btn-secondary" id="hdrDlToggleBench" aria-haspopup="true" aria-expanded="false">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
            Download Resume
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="cdr-caret" aria-hidden="true"><polyline points="6 9 12 15 18 9"/></svg>
          </button>
          <div id="hdrDlMenuBench" class="cdr-dl-menu">
            @if($hdrMaskContact)
              <button type="button" disabled title="Original resume is protected for masked profiles" class="cdr-dl-item text-muted" style="cursor:not-allowed;background:#f8fafc;">
                Download Resume
              </button>
            @else
              <a target="_blank" data-id="{{ $benchApplication->id }}" class="cdr-dl-item downloadCount" href="{{ route('download.resume', ['id' => $benchApplication->id,'path' => rtrim(strtr(base64_encode($url), '+/', '-_'), '=')]) }}">
                Download Resume
              </a>
            @endif
            <button type="button" onclick="hdrDownloadWithLogoBench()" class="cdr-dl-item cdr-dl-item-logo">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18"/><path d="M9 21V9"/></svg>
              Resume with Company Logo
              <span class="cdr-dl-badge">LOGO</span>
            </button>
          </div>
        </div>
      @else
        <a href="{{ route('service_company.bench-candidates.edit', [GeneralMethod::value_encrypt($benchApplication->id)]) }}" class="cdr-action-btn cdr-btn-secondary" title="Upload resume">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
          Upload Resume
        </a>
      @endif
    </div>

    <button type="button" class="cdr-topbar-close" id="kt_activitiesBenchApplication_close" aria-label="Close">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
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
              <span class="badge badge-light-primary fw-bolder">{{ @$benchApplication->master_status_name ?: 'Active' }}</span>
            </div>

            <p class="cdr-role">{{ ucwords(@$benchApplication->current_designation ?: '—') }}</p>

            <div class="cdr-meta-grid">
              <span class="cdr-meta-item">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
                Bench ID: BJ-{{ @$benchApplication->id }}
              </span>

              <span class="cdr-meta-item">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                Upload: {{ @$addedDate }}
              </span>

              @if(!empty($posted_by_company))
              <span class="cdr-meta-item">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 21h18"/><path d="M5 21V7l7-4 7 4v14"/></svg>
                {{ ucwords($posted_by_company) }}
              </span>
              @endif

              <span class="cdr-meta-item">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                {{ @$city_name ? $city_name . ', ' : '' }}{{ @$state_name ? ucwords($state_name) . ', ' : '' }}{{ ucwords(@$country_name) }}
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

      {{-- Sub-Card B: Highlights & Metrics --}}
      <div class="cdr-subcard cdr-subcard-metrics">
        <div class="cdr-metric-list" role="list" aria-label="Candidate metrics">
          <div class="cdr-metric-row" role="listitem">
            <div class="cdr-metric-icon ci-blue" aria-hidden="true">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
            </div>
            <span class="cdr-metric-label">Total Experience</span>
            <span class="cdr-metric-val">{{ @$formattedExperience }} Yrs</span>
          </div>

          <div class="cdr-metric-row" role="listitem">
            <div class="cdr-metric-icon ci-green" aria-hidden="true">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="20" x2="12" y2="10"/><line x1="18" y1="20" x2="18" y2="4"/><line x1="6" y1="20" x2="6" y2="16"/></svg>
            </div>
            <span class="cdr-metric-label">Relevant Experience</span>
            <span class="cdr-metric-val">{{ @$rel_formattedExperience }} Yrs</span>
          </div>

          <div class="cdr-metric-row" role="listitem">
            <div class="cdr-metric-icon ci-amber" aria-hidden="true">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
            </div>
            <span class="cdr-metric-label">Notice Period</span>
            <span class="cdr-metric-val">{{ @$np }}</span>
          </div>

          <div class="cdr-metric-row" role="listitem">
            <div class="cdr-metric-icon ci-purple" aria-hidden="true">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="3" width="20" height="14" rx="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>
            </div>
            <span class="cdr-metric-label">Work Mode</span>
            <span class="cdr-metric-val">{{ ucwords(@$benchApplication->wm ?: '—') }}</span>
          </div>
        </div>
      </div>

    </div>
  </div>

  {{-- ===== CARD 2: 6 PIPELINE STAT CARDS ===== --}}
  <div class="cdr-pipeline-section" aria-label="Candidate at-a-glance">
    <div class="cdr-pipeline-card">
      <div class="cdr-pipeline-icon cpi-blue">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
      </div>
      <div class="cdr-pipeline-info">
        <div class="cdr-pipeline-label">Total Experience</div>
        <div class="cdr-pipeline-value">{{ @$formattedExperience }}<span class="cpi-unit"> Yrs</span></div>
      </div>
    </div>

    <div class="cdr-pipeline-card">
      <div class="cdr-pipeline-icon cpi-green">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><line x1="12" y1="20" x2="12" y2="10"/><line x1="18" y1="20" x2="18" y2="4"/><line x1="6" y1="20" x2="6" y2="16"/></svg>
      </div>
      <div class="cdr-pipeline-info">
        <div class="cdr-pipeline-label">Relevant Exp</div>
        <div class="cdr-pipeline-value">{{ @$rel_formattedExperience }}<span class="cpi-unit"> Yrs</span></div>
      </div>
    </div>

    <div class="cdr-pipeline-card">
      <div class="cdr-pipeline-icon cpi-purple">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
      </div>
      <div class="cdr-pipeline-info">
        <div class="cdr-pipeline-label">Gender</div>
        <div class="cdr-pipeline-value">{{ ucwords(@$benchApplication->gender ?: '—') }}</div>
      </div>
    </div>

    <div class="cdr-pipeline-card">
      <div class="cdr-pipeline-icon cpi-amber">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>
      </div>
      <div class="cdr-pipeline-info">
        <div class="cdr-pipeline-label">Nationality</div>
        <div class="cdr-pipeline-value" title="{{ ucwords(@$benchApplication->nationality) }}">{{ \Illuminate\Support\Str::limit(ucwords(@$benchApplication->nationality), 10, '…') ?: '—' }}</div>
      </div>
    </div>

    <div class="cdr-pipeline-card">
      <div class="cdr-pipeline-icon cpi-teal">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="2" y="3" width="20" height="14" rx="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>
      </div>
      <div class="cdr-pipeline-info">
        <div class="cdr-pipeline-label">Work Mode</div>
        <div class="cdr-pipeline-value">{{ ucwords(@$benchApplication->wm ?: '—') }}</div>
      </div>
    </div>

    <div class="cdr-pipeline-card">
      <div class="cdr-pipeline-icon cpi-indigo">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c0 1.66 1.79 3 4 3s4-1.34 4-3v-5"/></svg>
      </div>
      <div class="cdr-pipeline-info">
        <div class="cdr-pipeline-label">Education</div>
        <div class="cdr-pipeline-value" title="{{ ucwords(@$benchApplication->qualification_name) }}">{{ \Illuminate\Support\Str::limit(ucwords(@$benchApplication->qualification_name), 12, '…') ?: '—' }}</div>
      </div>
    </div>
  </div>

  {{-- ===== CARD 3: TABS & CONTENT CARD ===== --}}
  <div class="cdr-outer-card cdr-tabs-card">
    <nav class="page-tabs tab-content nav" aria-label="Profile sections" role="tablist">
      <a type="button" class="tab tab-active" data-toggle="tab" href="#personal_details" aria-selected="true">
        <svg class="tab-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
          <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
          <circle cx="12" cy="7" r="4"/>
        </svg>
        Profile Overview
      </a>
      @if(@$benchApplication->status_type != 'DIRECT')
        <a type="button" class="tab" data-toggle="tab" href="#boostkeywords" aria-selected="false">
          <svg class="tab-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <circle cx="12" cy="12" r="3"></circle>
            <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 1 1-4 0v-.09 a1.65 1.65 0 0 0-1-1.51 1.65 1.65 0 0 0-1.82.33 l-.06.06a2 2 0 1 1-2.83-2.83l.06-.06 a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3 a2 2 0 1 1 0-4h.09a1.65 1.65 0 0 0 1.51-1 1.65 1.65 0 0 0-.33-1.82l-.06-.06 a2 2 0 1 1 2.83-2.83l.06.06 a1.65 1.65 0 0 0 1.82.33h.01 a1.65 1.65 0 0 0 1-1.51V3 a2 2 0 1 1 4 0v.09 a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06 a2 2 0 1 1 2.83 2.83l-.06.06 a1.65 1.65 0 0 0-.33 1.82v.01 a1.65 1.65 0 0 0 1.51 1H21 a2 2 0 1 1 0 4h-.09 a1.65 1.65 0 0 0-1.51 1z"></path>
          </svg>
          Ads Setting
        </a>
      @endif
      @if(!empty($benchApplication->resume) && in_array(strtolower(pathinfo($benchApplication->resume, PATHINFO_EXTENSION)),['pdf','doc','docx']))
        <a type="button" class="tab" data-toggle="tab" href="#resume_cv" aria-selected="false">
          <svg class="tab-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
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
      @if(@$benchApplication->status_type != 'DIRECT')
        <div class="tab-pane fade" id="boostkeywords" role="tabpanel" data-tab-loaded="0" data-tab-url="{{ route('service_company.bench-candidates.tab.ads', $benchApplication->id) }}">
          <div class="text-center py-10 ads-tab-loader" style="padding: 40px 0;">
            <div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading Ads Setting...</span></div>
            <div class="mt-2 text-gray-600 fs-7 fw-bold">Loading Ads Setting...</div>
          </div>
        </div>
      @endif

      <div class="tab-pane fade show active" id="personal_details" role="tabpanel">
        <h2 class="overview-title" style="font-size:14px;font-weight:700;color:#0f172a;margin-bottom:14px;">Profile Overview</h2>

        <div class="content-split">
          {{-- Main Content Column --}}
          <div class="content-main">
            <section class="cardshow">
              <div class="card-header-sidebar">
                <div class="card-icon" style="background: var(--color-purple-50, #f3e8ff); color: var(--color-purple-500, #a855f7);">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                    <polyline points="14 2 14 8 20 8"/>
                    <line x1="16" y1="13" x2="8" y2="13"/>
                    <line x1="16" y1="17" x2="8" y2="17"/>
                    <polyline points="10 9 9 9 8 9"/>
                  </svg>
                </div>
                <h3 class="card-title">Professional Summary</h3>
              </div>
              <p class="summary-text">{{ @$benchApplication->careersummary ?: 'No summary provided.' }}</p>
            </section>

            <section class="cardshow">
              <div class="card-header-sidebar">
                <div class="card-icon" style="background: var(--color-success-50, #f0fdf4); color: var(--color-success-600, #16a34a);">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                    <circle cx="9" cy="7" r="4"/>
                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                    <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                  </svg>
                </div>
                <h3 class="card-title">Key Skills & Experience</h3>
              </div>
              <div class="skills-table">
                <div class="skills-thead">
                  <div>Skill</div>
                  <div>Experience</div>
                  <div>Self Rating (Out of 5)</div>
                </div>
                @if(@$benchApplication->status_type != 'DIRECT')
                  @php
                    if(!empty($skillExperiencesCount)){
                      foreach($skillExperiences as $exp) {
                        echo '<div class="skills-row">
                          <div class="skill-name-cell">
                            <div class="skill-logo" style="color: #f59e0b;">
                              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path d="M12 2L2 7l10 5 10-5-10-5z"/>
                                <path d="M2 17l10 5 10-5"/>
                                <path d="M2 12l10 5 10-5"/>
                              </svg>
                            </div>
                            '.e($exp->skill_key).'
                          </div>
                          <div class="skill-exp-cell">'.e($exp->year ?? '0').'.'.e($exp->month ?? '0').' Years</div>
                          <div class="skill-rating-cell">
                            <div class="stars">
                              <svg class="star-skill '.(@$exp->rating >= 1 ? '' : 'star-empty').'" viewBox="0 0 24 24" fill="currentColor" stroke="none" aria-hidden="true"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                              <svg class="star-skill '.(@$exp->rating >= 2 ? '' : 'star-empty').'" viewBox="0 0 24 24" fill="currentColor" stroke="none" aria-hidden="true"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                              <svg class="star-skill '.(@$exp->rating >= 3 ? '' : 'star-empty').'" viewBox="0 0 24 24" fill="currentColor" stroke="none" aria-hidden="true"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                              <svg class="star-skill '.(@$exp->rating >= 4 ? '' : 'star-empty').'" viewBox="0 0 24 24" fill="currentColor" stroke="none" aria-hidden="true"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                              <svg class="star-skill '.(@$exp->rating >= 5 ? '' : 'star-empty').'" viewBox="0 0 24 24" fill="currentColor" stroke="none" aria-hidden="true"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                            </div>
                            <span class="rating-text">'.e(@$exp->rating ?? 0).' / 5</span>
                          </div>
                        </div>';
                      }
                    }
                  @endphp
                @endif
              </div>
            </section>
          </div>

          {{-- Sidebar Column --}}
          <aside class="content-sidebar">
            <section class="cardshow">
              <div class="card-header-sidebar">
                <div class="card-icon" style="background: var(--color-warning-50, #fffbeb); color: var(--color-warning-600, #d97706);">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
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
                  <span class="comp-value">{!! $current_ctc_cur !!} {{ @$benchApplication->pay_rate_amount }} {{ $salaryMode[$pay_rate_mode] ?? '' }}</span>
                </div>
                <div class="comp-row">
                  <span class="comp-label">Bill Rate</span>
                  <span class="comp-value">{!! @$current_ectc_cur !!} {{ @$benchApplication->bill_rate_amount }} {{ $salaryMode[$bill_rate_mode] ?? '' }}</span>
                </div>
                <div class="comp-row">
                  <span class="comp-label">Compensation Type</span>
                  <span class="comp-badge">{{ $salaryMode[$bill_rate_mode] ?? 'Hourly' }}</span>
                </div>
                <div class="comp-row">
                  <span class="comp-label">Salary Type</span>
                  <span class="comp-value">Fixed</span>
                </div>
                <div class="comp-row">
                  <span class="comp-label">Currency</span>
                  <span class="comp-value">{{ $current_cur }}</span>
                </div>
                <div class="comp-row">
                  <span class="comp-label">Service Type</span>
                  <span class="comp-value">{{ @$comission_type_name }} Service</span>
                </div>
                <div class="comp-row">
                  <span class="comp-label">Job Type</span>
                  <span class="comp-value">{{ @$benchApplication->employmentType ?: 'Fulltime' }}</span>
                </div>
              </div>
            </section>

            <section class="cardshow">
              <div class="card-header-sidebar">
                <div class="card-icon" style="background: var(--color-primary-50, #eff6ff); color: var(--color-primary-600, #2563eb);">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                    <circle cx="12" cy="7" r="4"/>
                  </svg>
                </div>
                <h3 class="card-title">Contact Details</h3>
              </div>
              <div class="contact-list">
                <div class="contact-row">
                  <span class="contact-label">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                    Email ID
                  </span>
                  <a href="mailto:{{ @$benchApplication->email }}" class="contact-value">{{ @$benchApplication->email ?: '—' }}</a>
                </div>
                <div class="contact-row">
                  <span class="contact-label">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                    Contact Number
                  </span>
                  <a href="javascript:;" class="contact-value" style="display: flex;">
                    <div id="firstPhoneDisplaybench" class="fw-bolder text-gray-800 fs-6" style="display: flex;"></div>
                    <span class="fw-bolder text-gray-800 fs-6" style="margin-left: 4px;">{{ @$benchApplication->phone }}</span>
                  </a>
                </div>
              </div>
              <div class="contact-actions">
                <button type="button" class="btn btn-sm btn-whatsapp">
                  <a href="https://api.whatsapp.com/send?phone=91{{@$benchApplication->phone}}" target="_blank" class="contact-value" style="display: flex; text-decoration: none; color: inherit;">
                    <svg class="btn-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                      <path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"/>
                    </svg>
                    Chat on WhatsApp
                  </a>
                </button>
                @php
                  $linkedin = @$benchApplication->linkedin;
                  if (!empty($linkedin) && !preg_match('/^(https?:\/\/|www\.)/i', $linkedin)) {
                      $linkedin = 'https://' . $linkedin;
                  }
                @endphp
                @if(!empty(@$benchApplication->linkedin))
                  <button type="button" class="btn btn-sm btn-linkedin">
                    <a href="{{ $linkedin ?? 'javascript:;' }}" target="{{ $linkedin ? '_blank' : '_self' }}" class="contact-value" style="display: flex; color: #fff; text-decoration: none;">
                      <svg class="btn-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-2-2 2 2 0 0 0-2 2v7h-4v-7a6 6 0 0 1 6-6z"/>
                        <rect x="2" y="9" width="4" height="12"/>
                        <circle cx="4" cy="4" r="2"/>
                      </svg>
                      Connect on LinkedIn
                    </a>
                  </button>
                @endif
              </div>
            </section>

            <section class="cardshow">
              <div class="card-header-sidebar">
                <div class="card-icon" style="background: var(--color-purple-50, #f3e8ff); color: var(--color-purple-500, #a855f7);">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                    <circle cx="9" cy="7" r="4"/>
                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                    <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                  </svg>
                </div>
                <h3 class="card-title">Relationship Manager Details</h3>
              </div>
              @if(!empty($bench_assign_row) && count($bench_assign_row) > 0)
                @foreach($bench_assign_row as $key => $assignVal)
                  <div class="rm-card">
                    <figure class="rm-avatar">
                      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                        <circle cx="12" cy="7" r="4"/>
                      </svg>
                    </figure>
                    <div class="rm-details">
                      <h4>{{ ucwords(@$assignVal->name) }}</h4>
                      <div class="rm-contact-list">
                        <div class="rm-contact-item" style="position: relative; display: flex;">
                          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/>
                          </svg>
                          <div class="iti__flag-container" data-code="{{ $assignVal->calling_code ?? 'in' }}" data-row="{{ $assignVal->id }}" id="phoneDisplayReal_{{ $assignVal->id }}" style="display: flex; position: relative;"></div>
                          {{ ucwords(@$assignVal->mobile) }}
                        </div>
                        <div class="rm-contact-item">
                          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
                            <polyline points="22,6 12,13 2,6"/>
                          </svg>
                          {{ @$assignVal->email }}
                        </div>
                      </div>
                    </div>
                  </div>
                  @if(!$loop->last)<hr>@endif
                @endforeach
              @else
                <div style="padding: 16px 20px; font-size: 12px; color: #94a3b8; font-style: italic;">No Relationship Manager assigned.</div>
              @endif
            </section>
          </aside>
        </div>
      </div>

      {{-- Confidentiality Banner --}}
      <div class="confidentiality-banner" role="note">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
          <circle cx="12" cy="12" r="10"/>
          <line x1="12" y1="16" x2="12" y2="12"/>
          <line x1="12" y1="8" x2="12.01" y2="8"/>
        </svg>
        <p>All candidate information is confidential and intended for internal use only.</p>
      </div>

      {{-- Resume / CV Lazy Load Container --}}
      @if(!empty($benchApplication->resume) && in_array(strtolower(pathinfo($benchApplication->resume, PATHINFO_EXTENSION)),['pdf','doc','docx']))
        <div class="tab-pane fade" id="resume_cv" role="tabpanel" data-tab-loaded="0" data-tab-url="{{ route('service_company.bench-candidates.tab.resume', $benchApplication->id) }}">
          <div class="text-center py-10 rcv-tab-loader" style="padding: 50px 0;">
            <div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading Resume...</span></div>
            <div class="mt-2 text-gray-600 fs-7 fw-bold">Loading candidate résumé & viewer...</div>
          </div>
        </div>
      @endif
    </main>
  </div>
</div>

<script>
    // Global function for Logo Download
    window.hdrDownloadWithLogoBench = function() {
        $('#hdrDlMenuBench').removeClass('open').css({'display': 'none', 'visibility': 'hidden'});
        var rcvTab = document.querySelector('a[href="#resume_cv"].tab, button[data-target="resume_cv"], button[data-rcv-tab]');
        if (rcvTab) rcvTab.click();

        var tries = 0;
        var poll = setInterval(function() {
            if (document.querySelectorAll('#rcvPages .rcv-page').length) {
                clearInterval(poll);
                var b = document.querySelector('[data-dl="logo"]');
                if (b) b.click();
            } else if (++tries > 80) {
                clearInterval(poll);
                alert('Resume is taking too long to load. Please open the Resume / CV tab and try again.');
            }
        }, 100);
    };

    (function() {
        // Robust Document Delegation for Download Resume Dropdown
        $(document).off('click', '#hdrDlToggleBench').on('click', '#hdrDlToggleBench', function(e) {
            e.preventDefault();
            e.stopPropagation();
            var $menu = $('#hdrDlMenuBench');
            if ($menu.length) {
                var isVisible = $menu.hasClass('open') || $menu.is(':visible');
                if (isVisible) {
                    $menu.removeClass('open').css({'display': 'none', 'visibility': 'hidden'});
                    $(this).attr('aria-expanded', 'false');
                } else {
                    $('.cdr-dl-menu').removeClass('open').css({'display': 'none', 'visibility': 'hidden'});
                    $menu.addClass('open').css({
                        'display': 'block',
                        'visibility': 'visible',
                        'opacity': '1',
                        'z-index': '999999'
                    });
                    $(this).attr('aria-expanded', 'true');
                }
            }
        });

        $(document).off('click.hdrDlBenchClose').on('click.hdrDlBenchClose', function(e) {
            if (!$(e.target).closest('#hdrDlWrapBench').length) {
                $('#hdrDlMenuBench').removeClass('open').css({'display': 'none', 'visibility': 'hidden'});
                $('#hdrDlToggleBench').attr('aria-expanded', 'false');
            }
        });

        var activeAbortController = null;

        $('.page-tabs .tab').on('click', function (e) {
            e.preventDefault();
            $('.page-tabs .tab').removeClass('tab-active').attr('aria-selected', 'false');
            $(this).addClass('tab-active').attr('aria-selected', 'true');

            var targetPaneId = $(this).attr('href');
            $('.tab-pane').removeClass('show active');
            var $targetPane = $(targetPaneId);
            $targetPane.addClass('show active');

            if ($targetPane.length && parseInt($targetPane.attr('data-tab-loaded') || '0', 10) === 0) {
                var url = $targetPane.attr('data-tab-url');
                if (url) {
                    if (activeAbortController) {
                        activeAbortController.abort();
                    }
                    activeAbortController = new AbortController();

                    fetch(url, {
                        signal: activeAbortController.signal,
                        headers: { 'X-Requested-With': 'XMLHttpRequest' }
                    })
                    .then(function(r) { return r.json(); })
                    .then(function(data) {
                        if (data.status === 'success' && data.view) {
                            if (targetPaneId === '#resume_cv') {
                                var $newPane = $(data.view);
                                $newPane.addClass('show active').attr('data-tab-loaded', '1');
                                $('#resume_cv').replaceWith($newPane);
                                if (typeof window.rcvInitResumeViewer === 'function') {
                                    window.rcvInitResumeViewer();
                                }
                            } else {
                                $targetPane.html(data.view).attr('data-tab-loaded', '1');
                            }
                        } else if (data.message) {
                            $targetPane.html('<div class="text-center py-10 text-danger fw-bold">' + data.message + '</div>');
                        }
                    })
                    .catch(function(err) {
                        if (err.name !== 'AbortError') {
                            $targetPane.html('<div class="text-center py-10 text-danger fw-bold">Failed to load tab content. Please try again.</div>');
                        }
                    });
                }
            } else if (targetPaneId === '#resume_cv' && typeof window.rcvInitResumeViewer === 'function') {
                window.rcvInitResumeViewer();
            }
        });

        var input = document.querySelector('input[name=tags]'); 
        if (input && !input.__tagify) {
            input.__tagify = new Tagify(input);
        }

        var input_show = document.querySelector('input[name=tags_show]'); 
        if (input_show && !input_show.__tagify) {
            input_show.__tagify = new Tagify(input_show);
        }

        $(document).ready(function() {
            const firstSavedCountryCode = "{{ @$benchApplication->phone_code }}";
            const firstPhoneDisplaybenchDiv = document.getElementById("firstPhoneDisplaybench");
            if (firstPhoneDisplaybenchDiv && firstSavedCountryCode) {
                const flagDiv = document.createElement("div");
                flagDiv.className = `iti__flag iti__${firstSavedCountryCode}`;
                const dialCode = (typeof countryDialCodes !== 'undefined' ? countryDialCodes[firstSavedCountryCode] : "") || "";
                const dialCodeSpan = document.createElement("span");
                dialCodeSpan.textContent = dialCode;
                firstPhoneDisplaybenchDiv.appendChild(flagDiv);
                firstPhoneDisplaybenchDiv.appendChild(dialCodeSpan);
            }

            setTimeout(() => {
                $('.iti__flag-container').each(function(index) {
                    var cnt = $(this).attr('data-row');
                    const flagContainer = document.getElementById(`phoneDisplayReal_${cnt}`);
                    var calling_code = $(this).attr('data-code');
                    if (flagContainer && calling_code && !flagContainer.children.length) {
                        const flagDiv = document.createElement("div");
                        flagDiv.className = `iti__flag iti__${calling_code}`;
                        const dialCode = (typeof countryDialCodes !== 'undefined' ? countryDialCodes[calling_code] : "") || "";
                        const dialCodeSpan = document.createElement("span");
                        dialCodeSpan.textContent = dialCode;
                        flagContainer.appendChild(flagDiv);
                        flagContainer.appendChild(dialCodeSpan);
                    }
                });
            }, 1000);
        });
    })();
</script>
