<style>
/* ── RM Drawer (bench-candidates/create) — scoped styles ── */
#kt_activitiesRecruiter_body,
#kt_activitiesRecruiter_body * { font-family: 'Poppins', sans-serif !important; }
#kt_activitiesRecruiter_body i[class*="fa-"] {
    font-family: "Jero Font Awesome 6 Free", "Font Awesome 6 Free", "FontAwesome" !important;
    font-style: normal;
}
#kt_activitiesRecruiter_body i.fa-regular {
    font-family: "Font Awesome 6 Free", "FontAwesome" !important;
    font-weight: 400;
}
#kt_activitiesRecruiter_body i.fa-solid {
    font-weight: 900;
}
#kt_activities_scroll { background: #fff !important; }

/* Section wrapper */
.crm-section {
    background: #fff;
    border: 1px solid #e9ecef;
    border-radius: 12px;
    margin: 0 16px 14px 16px;
    overflow: hidden;
}
.crm-section-head { padding: 18px 20px 10px 20px; }
.crm-section-title { font-size: 14px; font-weight: 700; color: #181c32; margin: 0 0 3px 0; }
.crm-section-sub   { font-size: 11.5px; color: #9ca3af; }
.crm-section-body  { padding: 4px 16px 16px 16px; }

/* Selected cards grid */
.crm-cards-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
    gap: 12px;
}
.crm-card {
    border: 1px solid #e9ecef;
    border-radius: 10px;
    background: #fff;
    padding: 14px 16px;
    display: flex;
    flex-direction: column;
    gap: 10px;
    transition: box-shadow .15s;
}
.crm-card:hover { box-shadow: 0 2px 12px rgba(0,0,0,.06); }

/* Avatar */
.crm-avatar {
    width: 38px; height: 38px; border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-size: 14px; font-weight: 700; flex-shrink: 0;
}

/* Assigned badge */
.crm-badge-assigned {
    display: inline-flex; align-items: center; gap: 4px;
    background: #ecfdf5; color: #059669;
    border: 1px solid #a7f3d0; border-radius: 20px;
    font-size: 10.5px; font-weight: 600; padding: 3px 9px; white-space: nowrap;
}

/* Kebab */
.crm-kebab-btn {
    width: 26px; height: 26px; border: 0; background: transparent;
    border-radius: 6px; color: #9ca3af; cursor: pointer;
    display: inline-flex; align-items: center; justify-content: center;
    font-size: 16px; transition: all .15s;
}
.crm-kebab-btn:hover { background: #f3f4f6; color: #374151; }
.crm-kebab-wrap { position: relative; }
.crm-kebab-menu {
    position: absolute; right: 0; top: 30px; z-index: 50;
    background: #fff; border: 1px solid #e5e7eb; border-radius: 8px;
    box-shadow: 0 8px 24px rgba(16,24,40,.12); padding: 6px;
    min-width: 140px; display: none;
}
.crm-kebab-menu.show { display: block; }
.crm-kebab-menu a {
    display: flex; align-items: center; gap: 8px;
    padding: 7px 10px; border-radius: 6px;
    font-size: 12px; color: #374151 !important; text-decoration: none;
}
.crm-kebab-menu a:hover { background: #f8fafc; color: #2563eb !important; }

/* Workload stats */
.crm-wl { display: grid; grid-template-columns: repeat(3, 1fr); gap: 8px; }
.crm-wl-top { display: flex; align-items: center; gap: 5px; }
.crm-wl-num { font-size: 13px; font-weight: 700; color: #181c32; }
.crm-wl-lbl { font-size: 10px; color: #9ca3af; margin-top: 2px; }
.crm-ic-blue   { color: #3b82f6; font-size: 13px; }
.crm-ic-purple { color: #7c3aed; font-size: 13px; }
.crm-ic-green  { color: #10b981; font-size: 13px; }

/* Remove button in card */
.crm-btn-remove {
    width: 100%; background: #fff; border: 1px solid #fca5a5;
    color: #ef4444; border-radius: 7px; padding: 7px 12px;
    font-size: 12px; font-weight: 500; cursor: pointer;
    transition: background .15s, border-color .15s;
}
.crm-btn-remove:hover { background: #fef2f2; border-color: #ef4444; }

/* Info banner */
.crm-info-banner {
    margin: 0 16px 14px 16px;
    background: #eff6ff; border: 1px solid #dbeafe; border-radius: 8px;
    padding: 11px 14px; display: flex; align-items: center; gap: 10px;
    font-size: 12px; color: #1e40af;
}
.crm-info-banner i { font-size: 16px; color: #3b82f6; flex-shrink: 0; }

/* Search */
.crm-search-wrap { position: relative; margin-bottom: 14px; }
.crm-search-icon {
    position: absolute; left: 11px; top: 50%; transform: translateY(-50%);
    color: #9ca3af; font-size: 15px; pointer-events: none;
}
.crm-search-input {
    width: 100%; border: 1px solid #e5e7eb; border-radius: 8px;
    padding: 9px 12px 9px 34px; font-size: 12.5px; color: #374151;
    background: #f9fafb; outline: none; font-family: 'Poppins', sans-serif;
    transition: border-color .15s, background .15s;
}
.crm-search-input:focus { border-color: #93c5fd; background: #fff; }
.crm-search-input::placeholder { color: #9ca3af; }

/* Table */
#crm_team_table { width: 100%; border-collapse: collapse; }
#crm_team_table thead th {
    background: #f8fafc; color: #9ca3af; font-size: 10.5px;
    font-weight: 700; text-transform: uppercase; letter-spacing: .5px;
    padding: 10px 12px; border-bottom: 1px solid #e9ecef; white-space: nowrap;
}
#crm_team_table tbody tr { border-bottom: 1px dashed #e9ecef; transition: background .1s; }
#crm_team_table tbody tr:last-child { border-bottom: 0; }
#crm_team_table tbody tr:hover { background: #f9fafb; }
#crm_team_table tbody td { padding: 11px 12px; font-size: 12.5px; vertical-align: middle; }

/* Status dot */
.crm-status { display: inline-flex; align-items: center; gap: 6px; font-size: 12px; font-weight: 500; }
.crm-status-dot { width: 7px; height: 7px; border-radius: 50%; flex-shrink: 0; }
.crm-status.active { color: #059669; }
.crm-status.active .crm-status-dot { background: #34d399; }
.crm-status.inactive { color: #ef4444; }
.crm-status.inactive .crm-status-dot { background: #f87171; }

/* Contact icon */
.crm-contact-btn {
    width: 30px; height: 30px; display: inline-flex; align-items: center;
    justify-content: center; border-radius: 7px; border: 1px solid #e5e7eb;
    background: #f8fafc; color: #6b7280; font-size: 13px; cursor: pointer;
    text-decoration: none; transition: all .15s;
}
.crm-contact-btn:hover { background: #eff6ff; color: #2563eb; border-color: #bfdbfe; }

/* Table action buttons */
.crm-tbl-btn {
    display: inline-flex; align-items: center; justify-content: center;
    min-width: 80px; border-radius: 7px; padding: 6px 14px;
    font-size: 12px; font-weight: 500; cursor: pointer; border: 1px solid;
    font-family: 'Poppins', sans-serif; transition: background .15s;
}
.crm-tbl-btn.crm-add    { background:#fff; border-color:#bfdbfe; color:#2563eb; }
.crm-tbl-btn.crm-add:hover { background:#eff6ff; }
.crm-tbl-btn.crm-remove { background:#fff; border-color:#fca5a5; color:#ef4444; }
.crm-tbl-btn.crm-remove:hover { background:#fef2f2; }

/* Empty state */
.crm-empty {
    display: flex; flex-direction: column; align-items: center;
    justify-content: center; padding: 24px 16px; text-align: center;
}
.crm-empty-icon {
    width: 46px; height: 46px; border-radius: 50%; background: #f3f4f6;
    display: flex; align-items: center; justify-content: center;
    font-size: 20px; color: #9ca3af; margin-bottom: 10px;
}
.crm-empty-title { font-size: 13px; font-weight: 600; color: #181c32; margin-bottom: 4px; }
.crm-empty-sub   { font-size: 11.5px; color: #9ca3af; margin-bottom: 12px; }
.crm-btn-scroll {
    display: inline-flex; align-items: center; gap: 5px;
    background: #fff; border: 1px solid #e5e7eb; color: #374151;
    border-radius: 7px; padding: 7px 14px; font-size: 12px; font-weight: 500;
    cursor: pointer; transition: background .15s;
}
.crm-btn-scroll:hover { background: #f3f4f6; }

@media (max-width:575px) { .crm-cards-grid { grid-template-columns: 1fr; } }
</style>

@php
/* ── Build per-user workload stats via ES (same pattern as hmlisting.blade.php) ── */
$crmCompanyId = auth()->user()->company_id;
$crmElastic   = app(\App\Services\ElasticService::class)->getClient();

$crmActiveJobs = [];
try {
    $r = $crmElastic->search(['index' => ELASTIC_JOBS, 'body' => ['size' => 0,
        'query' => ['term' => ['status' => 'active']],
        'aggs'  => ['by_user' => ['terms' => ['field' => 'job_assign.user_id', 'size' => 2000]]],
    ]]);
    foreach ($r['aggregations']['by_user']['buckets'] ?? [] as $b) {
        $crmActiveJobs[(int)$b['key']] = (int)$b['doc_count'];
    }
} catch (\Throwable $e) { $crmActiveJobs = []; }

$crmCaptainJobs = [];
try {
    $r = $crmElastic->search(['index' => ELASTIC_JOBS, 'body' => ['size' => 0,
        'query' => ['term' => ['job_assign.company_id' => $crmCompanyId]],
        'aggs'  => ['by_captain' => ['terms' => ['field' => 'job_assign.spok_user_id', 'size' => 2000]]],
    ]]);
    foreach ($r['aggregations']['by_captain']['buckets'] ?? [] as $b) {
        $crmCaptainJobs[(int)$b['key']] = (int)$b['doc_count'];
    }
} catch (\Throwable $e) { $crmCaptainJobs = []; }

// TRM Talents = bench_assign rows per user (MySQL is authoritative)
$crmTrmTalents = DB::table('bench_assign')
    ->select('user_id', DB::raw('COUNT(*) as cnt'))
    ->groupBy('user_id')
    ->pluck('cnt', 'user_id')
    ->toArray();

/* Build row objects */
$crmRows     = [];
$crmSelected = [];

// $recruiter is the array of currently-assigned names passed by controller
$assignedNames = array_map('trim', (array)($recruiter ?? []));

foreach ($team as $tv) {
    $tName  = trim(@$tv->name) ?: '?';
    $tFirst = strtolower($tName[0]);
    $tOrd   = ord($tFirst);
    if ($tOrd >= 97  && $tOrd <= 101) { $tAv = 'bg-light-danger text-danger'; }
    elseif ($tOrd >= 102 && $tOrd <= 106) { $tAv = 'bg-light-warning text-warning'; }
    elseif ($tOrd >= 107 && $tOrd <= 111) { $tAv = 'bg-light-primary text-primary'; }
    elseif ($tOrd >= 112 && $tOrd <= 116) { $tAv = 'bg-light-success text-success'; }
    elseif ($tOrd >= 117 && $tOrd <= 121) { $tAv = 'bg-dark text-white'; }
    else { $tAv = 'bg-light-secondary text-dark'; }

    $isAssigned = in_array(trim($tName), $assignedNames);

    $row = [
        'id'          => $tv->id,
        'name'        => ucwords(substr($tName, 0, 15)),
        'fullName'    => ucwords($tName),
        'role'        => ucwords(substr(trim(@$tv->designation ?: @$tv->display_name) ?: 'Team Member', 0, 18)),
        'email'       => (string)(@$tv->email ?? ''),
        'initial'     => strtoupper($tFirst),
        'avatarClass' => $tAv,
        'activeJobs'  => $crmActiveJobs[$tv->id] ?? 0,
        'captainJobs' => $crmCaptainJobs[$tv->id] ?? 0,
        'trmTalents'  => $crmTrmTalents[$tv->id] ?? 0,
        'assigned'    => $isAssigned,
        'active'      => (@$tv->working_status == 1),
    ];
    $crmRows[] = $row;
    if ($isAssigned) $crmSelected[] = $row;
}

$crmTeamJs = array_map(fn($r) => [
    'id' => $r['id'], 'name' => $r['fullName'], 'role' => $r['role'],
    'email' => $r['email'], 'initial' => $r['initial'],
    'avatarClass' => $r['avatarClass'],
    'jobs' => $r['activeJobs'], 'captainJobs' => $r['captainJobs'], 'trmTalents' => $r['trmTalents'],
], $crmRows);
@endphp

<div class="card-body position-relative px-0 py-0" id="kt_activities_body">
  <div id="kt_activities_scroll" class="position-relative scroll-y h-100 pt-3"
       data-kt-scroll="true" data-kt-scroll-height="auto"
       data-kt-scroll-wrappers="#kt_activities_body"
       data-kt-scroll-dependencies="#kt_activities_header, #kt_activities_footer"
       data-kt-scroll-offset="5px">

    {{-- ── 1. Selected Relationship Managers ── --}}
    <div class="crm-section" id="crmSelectedSection">
      <div class="crm-section-head">
        <div class="crm-section-title">
          1. Selected Relationship Managers
          <span id="crmSelectedCount" style="color:#9ca3af;font-weight:400;">({{ count($crmSelected) }})</span>
        </div>
        <div class="crm-section-sub">These team members are the relationship managers for this candidate</div>
      </div>
      <div class="crm-section-body">

        <div class="crm-cards-grid" id="crmSelectedGrid" style="{{ count($crmSelected) ? '' : 'display:none;' }}">
          @foreach ($crmSelected as $r)
          <div class="crm-card">
            <div class="d-flex align-items-start justify-content-between gap-2">
              <div class="d-flex align-items-center gap-2" style="min-width:0;">
                <div class="crm-avatar {{ $r['avatarClass'] }}">{{ $r['initial'] }}</div>
                <div style="min-width:0;">
                  <div style="font-size:13px;font-weight:600;color:#181c32;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $r['fullName'] }}</div>
                  <div style="font-size:11px;color:#9ca3af;">{{ $r['role'] }}</div>
                </div>
              </div>
              <div class="d-flex align-items-center gap-1 flex-shrink-0">
                <span class="crm-badge-assigned"><i class="fa-solid fa-circle-check" style="font-size:12px;"></i> Assigned</span>
                @if($r['email'])
                <div class="crm-kebab-wrap">
                  <button type="button" class="crm-kebab-btn"><i class="fa-solid fa-ellipsis-vertical"></i></button>
                  <div class="crm-kebab-menu">
                    <a href="javascript:void(0);" class="crm-email-action" data-email="{{ $r['email'] }}"><i class="fa-regular fa-envelope"></i> Email</a>
                  </div>
                </div>
                @endif
              </div>
            </div>
            <div class="crm-wl">
              <div><div class="crm-wl-top"><i class="fa-solid fa-briefcase crm-ic-blue"></i><span class="crm-wl-num">{{ $r['activeJobs'] }}</span></div><div class="crm-wl-lbl">Active Jobs</div></div>
              <div><div class="crm-wl-top"><i class="fa-solid fa-crown crm-ic-purple"></i><span class="crm-wl-num">{{ $r['captainJobs'] }}</span></div><div class="crm-wl-lbl">Captain Jobs</div></div>
              <div><div class="crm-wl-top"><i class="fa-solid fa-users crm-ic-green"></i><span class="crm-wl-num">{{ $r['trmTalents'] }}</span></div><div class="crm-wl-lbl">TRM Talents</div></div>
            </div>
            <button type="button" class="crm-btn-remove crm-card-remove" data-rmid="{{ $r['id'] }}">Remove</button>
          </div>
          @endforeach
        </div>

        <div id="crmEmptyState" style="{{ count($crmSelected) ? 'display:none;' : '' }}">
          <div class="crm-empty">
            <div class="crm-empty-icon"><i class="fa-solid fa-user-check"></i></div>
            <div class="crm-empty-title">No relationship manager selected</div>
            <div class="crm-empty-sub">Select a team member below to assign as relationship manager.</div>
            <button type="button" class="crm-btn-scroll" id="crmScrollToTable">
              <i class="fa-solid fa-plus" style="font-size:13px;"></i> Select Team Member
            </button>
          </div>
        </div>

      </div>
    </div>

    {{-- ── Info banner ── --}}
    <div class="crm-info-banner">
      <i class="fa-solid fa-circle-info"></i>
      <span>Relationship managers coordinate this candidate's bench engagement and client communication.</span>
    </div>

    {{-- ── 2. Add Relationship Managers ── --}}
    <div class="crm-section" id="crmAddSection">
      <div class="crm-section-head">
        <div class="crm-section-title">2. Add Relationship Managers</div>
        <div class="crm-section-sub">Search and select team members from your company</div>
      </div>
      <div class="crm-section-body">

        <div class="crm-search-wrap">
          <i class="fa-solid fa-magnifying-glass crm-search-icon"></i>
          <input type="text" id="searchInput" class="crm-search-input" placeholder="Search for relationship manager....">
        </div>

        <div class="table-responsive">
          <table id="crm_team_table">
            <thead>
              <tr>
                <th style="min-width:170px;">Name</th>
                <th style="min-width:70px;">Contact</th>
                <th style="min-width:200px;">Workload</th>
                <th style="min-width:90px;">Status</th>
                <th style="min-width:100px;text-align:right;">Action</th>
              </tr>
            </thead>
            <tbody>
              @foreach ($crmRows as $r)
              <tr>
                <td>
                  <div class="d-flex align-items-center gap-2">
                    <div class="crm-avatar {{ $r['avatarClass'] }}" style="width:34px;height:34px;font-size:12px;flex-shrink:0;">{{ $r['initial'] }}</div>
                    <div>
                      <div style="font-size:12.5px;font-weight:600;color:#181c32;">{{ $r['name'] }}</div>
                      <div style="font-size:11px;color:#9ca3af;">{{ $r['role'] }}</div>
                    </div>
                  </div>
                </td>
                <td>
                  @if($r['email'])
                    <a href="javascript:void(0);" class="crm-contact-btn crm-email-action" data-email="{{ $r['email'] }}" title="Email">
                      <i class="fa-regular fa-envelope"></i>
                    </a>
                  @else
                    <span style="color:#d1d5db;">—</span>
                  @endif
                </td>
                <td>
                  <div class="crm-wl">
                    <div><div class="crm-wl-top"><i class="fa-solid fa-briefcase crm-ic-blue"></i><span class="crm-wl-num">{{ $r['activeJobs'] }}</span></div><div class="crm-wl-lbl">Active Jobs</div></div>
                    <div><div class="crm-wl-top"><i class="fa-solid fa-crown crm-ic-purple"></i><span class="crm-wl-num">{{ $r['captainJobs'] }}</span></div><div class="crm-wl-lbl">Captain Jobs</div></div>
                    <div><div class="crm-wl-top"><i class="fa-solid fa-users crm-ic-green"></i><span class="crm-wl-num">{{ $r['trmTalents'] }}</span></div><div class="crm-wl-lbl">TRM Talents</div></div>
                  </div>
                </td>
                <td>
                  <span class="crm-status {{ $r['active'] ? 'active' : 'inactive' }}">
                    <span class="crm-status-dot"></span>
                    {{ $r['active'] ? 'Active' : 'Inactive' }}
                  </span>
                </td>
                <td style="text-align:right;">
                  <button type="button"
                          id="team_select_{{ $r['id'] }}"
                          class="crm-tbl-btn {{ $r['assigned'] ? 'crm-remove' : 'crm-add' }} cp_select"
                          rel="{{ $r['id'] }}"
                          data-assign="{{ $r['assigned'] ? 1 : 0 }}"
                          data-title="{{ $r['fullName'] }}"
                          data-email="{{ $r['email'] }}">
                    {{ $r['assigned'] ? 'Remove' : 'Add' }}
                  </button>
                </td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>

      </div>
    </div>

  </div>
</div>

<script>
var __crmTeam = @json($crmTeamJs);

function crmEsc(s) {
    return (s||'').toString().replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}
function crmFind(id) {
    for (var i = 0; i < __crmTeam.length; i++) {
        if (String(__crmTeam[i].id) === String(id)) return __crmTeam[i];
    }
    return null;
}

function crmCardHtml(u) {
    var kebab = u.email
        ? '<div class="crm-kebab-wrap"><button type="button" class="crm-kebab-btn"><i class="fa-solid fa-ellipsis-vertical"></i></button>'
          + '<div class="crm-kebab-menu"><a href="javascript:void(0);" class="crm-email-action" data-email="' + crmEsc(u.email) + '"><i class="fa-regular fa-envelope"></i> Email</a></div></div>'
        : '';
    return '<div class="crm-card">'
        + '<div class="d-flex align-items-start justify-content-between gap-2">'
        +   '<div class="d-flex align-items-center gap-2" style="min-width:0;">'
        +     '<div class="crm-avatar ' + u.avatarClass + '">' + crmEsc(u.initial) + '</div>'
        +     '<div style="min-width:0;">'
        +       '<div style="font-size:13px;font-weight:600;color:#181c32;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">' + crmEsc(u.name) + '</div>'
        +       '<div style="font-size:11px;color:#9ca3af;">' + crmEsc(u.role) + '</div>'
        +     '</div>'
        +   '</div>'
        +   '<div class="d-flex align-items-center gap-1 flex-shrink-0">'
        +     '<span class="crm-badge-assigned"><i class="fa-solid fa-circle-check" style="font-size:12px;"></i> Assigned</span>'
        +     kebab
        +   '</div>'
        + '</div>'
        + '<div class="crm-wl">'
        +   '<div><div class="crm-wl-top"><i class="fa-solid fa-briefcase crm-ic-blue"></i><span class="crm-wl-num">' + u.jobs + '</span></div><div class="crm-wl-lbl">Active Jobs</div></div>'
        +   '<div><div class="crm-wl-top"><i class="fa-solid fa-crown crm-ic-purple"></i><span class="crm-wl-num">' + u.captainJobs + '</span></div><div class="crm-wl-lbl">Captain Jobs</div></div>'
        +   '<div><div class="crm-wl-top"><i class="fa-solid fa-users crm-ic-green"></i><span class="crm-wl-num">' + u.trmTalents + '</span></div><div class="crm-wl-lbl">TRM Talents</div></div>'
        + '</div>'
        + '<button type="button" class="crm-btn-remove crm-card-remove" data-rmid="' + u.id + '">Remove</button>'
        + '</div>';
}

function crmRenderSelected() {
    var ids = [];
    $('.cp_select').each(function () {
        if (String($(this).data('assign')) === '1') ids.push($(this).attr('rel'));
    });
    if (ids.length === 0) {
        $('#crmSelectedGrid').hide().empty();
        $('#crmEmptyState').show();
    } else {
        var html = '';
        ids.forEach(function (id) { var u = crmFind(id); if (u) html += crmCardHtml(u); });
        $('#crmSelectedGrid').html(html).show();
        $('#crmEmptyState').hide();
    }
    $('#crmSelectedCount').text('(' + ids.length + ')');
}

$(document).ready(function () {

    // Search
    var $rows = $('#crm_team_table tbody tr');
    $('#searchInput').on('keyup', function () {
        var val = $(this).val().toLowerCase();
        $rows.each(function () { $(this).toggle($(this).text().toLowerCase().indexOf(val) > -1); });
    });

    // Scroll to table button
    $(document).on('click', '#crmScrollToTable', function (e) {
        e.preventDefault();
        $('html,body').animate({ scrollTop: $('#crmAddSection').offset().top - 20 }, 400);
        setTimeout(function () { $('#searchInput').focus(); }, 450);
    });

    // Card remove -> trigger table button
    $(document).on('click', '.crm-card-remove', function (e) {
        e.preventDefault();
        $('#team_select_' + $(this).data('rmid')).trigger('click');
    });

    // Kebab
    $(document).on('click', '.crm-kebab-btn', function (e) {
        e.preventDefault(); e.stopPropagation();
        var $m = $(this).siblings('.crm-kebab-menu');
        $('.crm-kebab-menu').not($m).removeClass('show');
        $m.toggleClass('show');
    });
    $(document).on('click', '.crm-kebab-menu', function (e) { e.stopPropagation(); });
    $(document).on('click', function () { $('.crm-kebab-menu').removeClass('show'); });

    // Email
    $(document).on('click', '.crm-email-action', function (e) {
        e.preventDefault(); e.stopPropagation();
        $('.crm-kebab-menu').removeClass('show');
        var email = $(this).data('email');
        if (email) window.location.href = 'mailto:' + email;
    });

    // Add / Remove in table
    $(document).on('click', '.cp_select', function () {
        var rel    = $(this).attr('rel');
        var assign = $(this).attr('data-assign');
        var title  = $(this).attr('data-title');
        if (assign == 0) {
            $(this).removeClass('crm-add').addClass('crm-remove').text('Remove').attr('data-assign', 1);
            addRecruiterManager(title, rel);
        } else {
            $(this).removeClass('crm-remove').addClass('crm-add').text('Add').attr('data-assign', 0);
            DeleteEvent(title, rel);
        }
        crmRenderSelected();
    });

});

function addRecruiterManager(name, rel) {
    var cur   = $('#recruiter').val();
    var curId = $('#recruiter_id').val();
    if (cur !== '') {
        if (!cur.includes(name))  $('#recruiter').val(cur + ' , ' + name);
        if (!curId.includes(rel)) $('#recruiter_id').val(curId + ' , ' + rel);
    } else {
        $('#recruiter').val(name);
        $('#recruiter_id').val(rel);
    }
}

function DeleteEvent(name, rel) {
    var cur   = $('#recruiter').val();
    var curId = $('#recruiter_id').val();
    if (cur !== '') {
        var names = cur.split(',').filter(function (n) { return n.trim() !== name.trim(); });
        var ids   = curId.split(',').filter(function (i) { return i.trim() !== String(rel).trim(); });
        $('#recruiter').val(names.join(' , '));
        $('#recruiter_id').val(ids.join(' , '));
    }
}
</script>
