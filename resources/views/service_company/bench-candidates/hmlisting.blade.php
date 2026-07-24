<style>
/* ── RM Drawer — scoped styles ── */
#kt_activities_body, #kt_activities_scroll,
#kt_activities_body * { font-family: 'Poppins', sans-serif !important; }

/* Scroll area: white */
#kt_activities_scroll { background: #fff !important; }

/* ── Section wrapper ── */
.rm-section {
    background: #fff;
    border: 1px solid #e9ecef;
    border-radius: 12px;
    margin: 0 16px 14px 16px;
    overflow: hidden;
}
.rm-section-head {
    padding: 18px 20px 10px 20px;
}
.rm-section-title {
    font-size: 14px;
    font-weight: 700;
    color: #181c32;
    margin: 0 0 3px 0;
}
.rm-section-sub {
    font-size: 11.5px;
    color: #9ca3af;
}
.rm-section-body {
    padding: 4px 16px 16px 16px;
}

/* ── Selected cards grid ── */
.rm-cards-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
    gap: 12px;
}
.rm-card {
    border: 1px solid #e9ecef;
    border-radius: 10px;
    background: #fff;
    padding: 14px 16px;
    display: flex;
    flex-direction: column;
    gap: 10px;
    transition: box-shadow 0.15s;
}
.rm-card:hover { box-shadow: 0 2px 12px rgba(0,0,0,0.06); }

/* Avatar */
.rm-avatar {
    width: 38px;
    height: 38px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 14px;
    font-weight: 700;
    flex-shrink: 0;
}

/* Assigned badge */
.rm-badge-assigned {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    background: #ecfdf5;
    color: #059669;
    border: 1px solid #a7f3d0;
    border-radius: 20px;
    font-size: 10.5px;
    font-weight: 600;
    padding: 3px 9px;
    white-space: nowrap;
}

/* Kebab */
.rm-kebab-btn {
    width: 26px;
    height: 26px;
    border: 0;
    background: transparent;
    border-radius: 6px;
    color: #9ca3af;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 16px;
    transition: all .15s;
}
.rm-kebab-btn:hover { background: #f3f4f6; color: #374151; }
.rm-kebab-wrap { position: relative; }
.rm-kebab-menu {
    position: absolute;
    right: 0;
    top: 30px;
    z-index: 50;
    background: #fff;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    box-shadow: 0 8px 24px rgba(16,24,40,.12);
    padding: 6px;
    min-width: 140px;
    display: none;
}
.rm-kebab-menu.show { display: block; }
.rm-kebab-menu a {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 7px 10px;
    border-radius: 6px;
    font-size: 12px;
    color: #374151 !important;
    text-decoration: none;
}
.rm-kebab-menu a:hover { background: #f8fafc; color: #2563eb !important; }

/* Workload mini stats */
.rm-wl { display: grid; grid-template-columns: repeat(3, 1fr); gap: 8px; }
.rm-wl-item {}
.rm-wl-top { display: flex; align-items: center; gap: 5px; }
.rm-wl-num { font-size: 13px; font-weight: 700; color: #181c32; }
.rm-wl-lbl { font-size: 10px; color: #9ca3af; margin-top: 2px; }
.ic-blue { color: #3b82f6; font-size: 13px; }
.ic-purple { color: #7c3aed; font-size: 13px; }
.ic-green { color: #10b981; font-size: 13px; }

/* Remove button in card */
.rm-btn-remove {
    width: 100%;
    background: #fff;
    border: 1px solid #fca5a5;
    color: #ef4444;
    border-radius: 7px;
    padding: 7px 12px;
    font-size: 12px;
    font-weight: 500;
    cursor: pointer;
    transition: background .15s, border-color .15s;
}
.rm-btn-remove:hover { background: #fef2f2; border-color: #ef4444; }

/* ── Info banner ── */
.rm-info-banner {
    margin: 0 16px 14px 16px;
    background: #eff6ff;
    border: 1px solid #dbeafe;
    border-radius: 8px;
    padding: 11px 14px;
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 12px;
    color: #1e40af;
}
.rm-info-banner i { font-size: 16px; color: #3b82f6; flex-shrink: 0; }

/* ── Search ── */
.rm-search-wrap {
    position: relative;
    margin-bottom: 14px;
}
.rm-search-icon {
    position: absolute;
    left: 11px;
    top: 50%;
    transform: translateY(-50%);
    color: #9ca3af;
    font-size: 15px;
    pointer-events: none;
}
.rm-search-input {
    width: 100%;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    padding: 9px 12px 9px 34px;
    font-size: 12.5px;
    color: #374151;
    background: #f9fafb;
    outline: none;
    font-family: 'Poppins', sans-serif;
    transition: border-color .15s, background .15s;
}
.rm-search-input:focus { border-color: #93c5fd; background: #fff; }
.rm-search-input::placeholder { color: #9ca3af; }

/* ── Table ── */
#Iteam_Table_rm { width: 100%; border-collapse: collapse; }
#Iteam_Table_rm thead th {
    background: #f8fafc;
    color: #9ca3af;
    font-size: 10.5px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .5px;
    padding: 10px 12px;
    border-bottom: 1px solid #e9ecef;
    white-space: nowrap;
}
#Iteam_Table_rm tbody tr { border-bottom: 1px dashed #e9ecef; }
#Iteam_Table_rm tbody tr:last-child { border-bottom: 0; }
#Iteam_Table_rm tbody td { padding: 12px; font-size: 12.5px; vertical-align: middle; }

/* Status dot */
.rm-status { display: inline-flex; align-items: center; gap: 6px; font-size: 12px; font-weight: 500; }
.rm-status-dot { width: 7px; height: 7px; border-radius: 50%; flex-shrink: 0; }
.rm-status.active { color: #059669; }
.rm-status.active .rm-status-dot { background: #34d399; }
.rm-status.inactive { color: #ef4444; }
.rm-status.inactive .rm-status-dot { background: #f87171; }

/* Table action buttons */
.rm-tbl-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 80px;
    border-radius: 7px;
    padding: 6px 14px;
    font-size: 12px;
    font-weight: 500;
    cursor: pointer;
    border: 1px solid;
    transition: background .15s;
}
.rm-tbl-btn.add {
    background: #fff;
    border-color: #bfdbfe;
    color: #2563eb;
}
.rm-tbl-btn.add:hover { background: #eff6ff; border-color: #93c5fd; }
.rm-tbl-btn.remove {
    background: #fff;
    border-color: #fca5a5;
    color: #ef4444;
}
.rm-tbl-btn.remove:hover { background: #fef2f2; border-color: #ef4444; }

/* Contact icon */
.rm-contact-btn {
    width: 30px;
    height: 30px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border-radius: 7px;
    border: 1px solid #e5e7eb;
    background: #f8fafc;
    color: #6b7280;
    font-size: 13px;
    cursor: pointer;
    transition: all .15s;
    text-decoration: none;
}
.rm-contact-btn:hover { background: #eff6ff; color: #2563eb; border-color: #bfdbfe; }

/* Empty state */
.rm-empty {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 24px 16px;
    text-align: center;
}
.rm-empty-icon {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    background: #f3f4f6;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    color: #9ca3af;
    margin-bottom: 10px;
}
.rm-empty-title { font-size: 13px; font-weight: 600; color: #181c32; margin-bottom: 4px; }
.rm-empty-sub { font-size: 11.5px; color: #9ca3af; margin-bottom: 12px; }
.rm-btn-scroll {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    background: #fff;
    border: 1px solid #e5e7eb;
    color: #374151;
    border-radius: 7px;
    padding: 7px 14px;
    font-size: 12px;
    font-weight: 500;
    cursor: pointer;
    transition: background .15s;
}
.rm-btn-scroll:hover { background: #f3f4f6; }

@media (max-width: 575px) { .rm-cards-grid { grid-template-columns: 1fr; } }
</style>

@php
    $rmCompanyId = auth()->user()->company_id;
    $rmElastic = app(\App\Services\ElasticService::class)->getClient();

    $activeJobsByUser = [];
    try {
        $resp = $rmElastic->search([
            'index' => ELASTIC_JOBS,
            'body' => [
                'size' => 0,
                'query' => ['term' => ['status' => 'active']],
                'aggs' => ['by_user' => ['terms' => ['field' => 'job_assign.user_id', 'size' => 2000]]],
            ],
        ]);
        foreach (($resp['aggregations']['by_user']['buckets'] ?? []) as $b) {
            $activeJobsByUser[(int) $b['key']] = (int) $b['doc_count'];
        }
    } catch (\Throwable $e) { $activeJobsByUser = []; }

    $captainJobsByUser = [];
    try {
        $resp = $rmElastic->search([
            'index' => ELASTIC_JOBS,
            'body' => [
                'size' => 0,
                'query' => ['term' => ['job_assign.company_id' => $rmCompanyId]],
                'aggs' => ['by_captain' => ['terms' => ['field' => 'job_assign.spok_user_id', 'size' => 2000]]],
            ],
        ]);
        foreach (($resp['aggregations']['by_captain']['buckets'] ?? []) as $b) {
            $captainJobsByUser[(int) $b['key']] = (int) $b['doc_count'];
        }
    } catch (\Throwable $e) { $captainJobsByUser = []; }

    // TRM Talents = bench_assign rows per user (MySQL is authoritative)
    $trmTalentsByUser = DB::table('bench_assign')
        ->select('user_id', DB::raw('COUNT(*) as cnt'))
        ->groupBy('user_id')
        ->pluck('cnt', 'user_id')
        ->toArray();

    $rmRows = [];
    foreach ($team as $teamVal) {
        $name = trim(@$teamVal->name) ?: '?';
        $firstChar = strtolower($name[0]);
        $ord = ord($firstChar);
        if ($ord >= 97 && $ord <= 101)        { $avatarClass = 'bg-light-danger text-danger'; }
        elseif ($ord >= 102 && $ord <= 106)   { $avatarClass = 'bg-light-warning text-warning'; }
        elseif ($ord >= 107 && $ord <= 111)   { $avatarClass = 'bg-light-primary text-primary'; }
        elseif ($ord >= 112 && $ord <= 116)   { $avatarClass = 'bg-light-success text-success'; }
        elseif ($ord >= 117 && $ord <= 121)   { $avatarClass = 'bg-dark text-secondary'; }
        else                                   { $avatarClass = 'bg-light-secondary text-dark'; }

        $isAssigned = (@$teamVal->bench_assign_id > 0) && ($currentBenchId == $teamVal->bench_applications_id);

        $rmRows[] = [
            'id'           => $teamVal->id,
            'name'         => ucwords(substr($name, 0, 15)),
            'fullName'     => ucwords($name),
            'role'         => ucwords(substr(trim(@$teamVal->display_name) ?: 'Team Member', 0, 18)),
            'email'        => (string) @$teamVal->email,
            'initial'      => strtoupper($firstChar),
            'avatarClass'  => $avatarClass,
            'activeJobs'   => $activeJobsByUser[$teamVal->id] ?? 0,
            'captainJobs'  => $captainJobsByUser[$teamVal->id] ?? 0,
            'trmTalents'   => $trmTalentsByUser[$teamVal->id] ?? 0,
            'assigned'     => $isAssigned,
            'workingActive'=> (@$teamVal->working_status == 1),
        ];
    }
    $selectedRm = array_values(array_filter($rmRows, fn ($r) => $r['assigned']));

    $rmTeamJs = array_map(fn ($r) => [
        'id'          => $r['id'],
        'name'        => $r['fullName'],
        'role'        => $r['role'],
        'email'       => $r['email'],
        'initial'     => $r['initial'],
        'avatarClass' => $r['avatarClass'],
        'jobs'        => $r['activeJobs'],
        'captainJobs' => $r['captainJobs'],
        'trmTalents'  => $r['trmTalents'],
    ], $rmRows);
@endphp

<form>
  <div class="card-body position-relative px-0 py-0" id="kt_activities_body">
    <div id="kt_activities_scroll" class="position-relative scroll-y pt-3 h-100"
         data-kt-scroll="true" data-kt-scroll-height="auto"
         data-kt-scroll-wrappers="#kt_activities_body"
         data-kt-scroll-dependencies="#kt_activities_header, #kt_activities_footer"
         data-kt-scroll-offset="5px">

      {{-- ── 1. Selected Relationship Managers ── --}}
      <div class="rm-section" id="selectedRMSection">
        <div class="rm-section-head">
          <div class="rm-section-title">
            1. Selected Relationship Managers
            <span id="selectedRMCount" style="color:#9ca3af; font-weight:400;">({{ count($selectedRm) }})</span>
          </div>
          <div class="rm-section-sub">These team members are the relationship managers for this candidate</div>
        </div>
        <div class="rm-section-body">
          <div class="rm-cards-grid" id="selectedRMGrid" style="{{ count($selectedRm) ? '' : 'display:none;' }}">
            @foreach ($selectedRm as $r)
            <div class="rm-card">
              {{-- Top row: avatar + name + badge + kebab --}}
              <div class="d-flex align-items-start justify-content-between gap-2">
                <div class="d-flex align-items-center gap-2" style="min-width:0;">
                  <div class="rm-avatar {{ $r['avatarClass'] }}">{{ $r['initial'] }}</div>
                  <div style="min-width:0;">
                    <div style="font-size:13px; font-weight:600; color:#181c32; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">{{ $r['fullName'] }}</div>
                    <div style="font-size:11px; color:#9ca3af;">{{ $r['role'] }}</div>
                  </div>
                </div>
                <div class="d-flex align-items-center gap-1 flex-shrink-0">
                  <span class="rm-badge-assigned">
                    <i class="la la-check-circle" style="font-size:12px;"></i> Assigned
                  </span>
                  @if ($r['email'])
                  <div class="rm-kebab-wrap">
                    <button type="button" class="rm-kebab-btn" title="More"><i class="la la-ellipsis-v"></i></button>
                    <div class="rm-kebab-menu">
                      <a href="javascript:void(0);" class="contact-action-rm" data-type="email"
                         data-name="{{ $r['fullName'] }}" data-email="{{ $r['email'] }}">
                        <i class="la la-envelope"></i> Email
                      </a>
                    </div>
                  </div>
                  @endif
                </div>
              </div>
              {{-- Workload stats --}}
              <div class="rm-wl">
                <div class="rm-wl-item">
                  <div class="rm-wl-top"><i class="la la-briefcase ic-blue"></i><span class="rm-wl-num">{{ $r['activeJobs'] }}</span></div>
                  <div class="rm-wl-lbl">Active Jobs</div>
                </div>
                <div class="rm-wl-item">
                  <div class="rm-wl-top"><i class="la la-crown ic-purple"></i><span class="rm-wl-num">{{ $r['captainJobs'] }}</span></div>
                  <div class="rm-wl-lbl">Captain Jobs</div>
                </div>
                <div class="rm-wl-item">
                  <div class="rm-wl-top"><i class="la la-user-friends ic-green"></i><span class="rm-wl-num">{{ $r['trmTalents'] }}</span></div>
                  <div class="rm-wl-lbl">TRM Talents</div>
                </div>
              </div>
              {{-- Remove button --}}
              <button type="button" class="rm-btn-remove rm-remove" data-rmid="{{ $r['id'] }}">Remove</button>
            </div>
            @endforeach
          </div>

          {{-- Empty state --}}
          <div id="rmEmptyState" style="{{ count($selectedRm) ? 'display:none;' : '' }}">
            <div class="rm-empty">
              <div class="rm-empty-icon"><i class="la la-user-check"></i></div>
              <div class="rm-empty-title">No relationship manager selected</div>
              <div class="rm-empty-sub">Select a team member below to assign as relationship manager.</div>
              <button type="button" class="rm-btn-scroll" id="scrollToRmTableBtn">
                <i class="la la-plus" style="font-size:13px;"></i> Select Team Member
              </button>
            </div>
          </div>
        </div>
      </div>

      {{-- ── Info banner ── --}}
      <div class="rm-info-banner">
        <i class="la la-info-circle"></i>
        <span>Relationship managers coordinate this candidate's bench engagement and client communication.</span>
      </div>

      {{-- ── 2. Add Relationship Managers ── --}}
      <div class="rm-section" id="addMoreRMSection">
        <div class="rm-section-head">
          <div class="rm-section-title">2. Add Relationship Managers</div>
          <div class="rm-section-sub">Search and select team members from your company</div>
        </div>
        <div class="rm-section-body">

          {{-- Search --}}
          <div class="rm-search-wrap">
            <i class="la la-search rm-search-icon"></i>
            <input type="text" id="searchInputRM" class="rm-search-input" placeholder="Search for relationship manager....">
          </div>

          {{-- Table --}}
          <div class="table-responsive">
            <table id="Iteam_Table_rm">
              <thead>
                <tr>
                  <th style="min-width:180px;">Name</th>
                  <th style="min-width:80px;">Contact</th>
                  <th style="min-width:200px;">Workload</th>
                  <th style="min-width:90px;">Status</th>
                  <th style="min-width:100px; text-align:right;">Action</th>
                </tr>
              </thead>
              <tbody>
                @foreach ($rmRows as $r)
                <tr>
                  <td>
                    <div class="d-flex align-items-center gap-2">
                      <div class="rm-avatar {{ $r['avatarClass'] }}" style="width:34px;height:34px;font-size:12px;flex-shrink:0;">{{ $r['initial'] }}</div>
                      <div>
                        <div style="font-size:12.5px; font-weight:600; color:#181c32;">{{ $r['name'] }}</div>
                        <div style="font-size:11px; color:#9ca3af;">{{ $r['role'] }}</div>
                      </div>
                    </div>
                  </td>
                  <td>
                    @if ($r['email'])
                      <a href="javascript:void(0);" class="rm-contact-btn contact-action-rm"
                         data-type="email" data-name="{{ $r['fullName'] }}" data-email="{{ $r['email'] }}" title="Email">
                        <i class="la la-envelope"></i>
                      </a>
                    @else
                      <span style="color:#d1d5db;">—</span>
                    @endif
                  </td>
                  <td>
                    <div class="rm-wl">
                      <div class="rm-wl-item">
                        <div class="rm-wl-top"><i class="la la-briefcase ic-blue"></i><span class="rm-wl-num">{{ $r['activeJobs'] }}</span></div>
                        <div class="rm-wl-lbl">Active Jobs</div>
                      </div>
                      <div class="rm-wl-item">
                        <div class="rm-wl-top"><i class="la la-crown ic-purple"></i><span class="rm-wl-num">{{ $r['captainJobs'] }}</span></div>
                        <div class="rm-wl-lbl">Captain Jobs</div>
                      </div>
                      <div class="rm-wl-item">
                        <div class="rm-wl-top"><i class="la la-user-friends ic-green"></i><span class="rm-wl-num">{{ $r['trmTalents'] }}</span></div>
                        <div class="rm-wl-lbl">TRM Talents</div>
                      </div>
                    </div>
                  </td>
                  <td>
                    <span class="rm-status {{ $r['workingActive'] ? 'active' : 'inactive' }}">
                      <span class="rm-status-dot"></span>
                      {{ $r['workingActive'] ? 'Active' : 'Inactive' }}
                    </span>
                  </td>
                  <td style="text-align:right;">
                    <button type="button"
                            id="btn_{{ $r['id'] }}"
                            class="rm-tbl-btn team_event {{ $r['assigned'] ? 'remove' : 'add' }}"
                            rel="{{ $currentBenchId }}"
                            data-assign="{{ $r['assigned'] ? 1 : 0 }}"
                            data-userid="{{ $r['id'] }}">
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
</form>

<script>
var __rmTeam = @json($rmTeamJs);

function rmFind(id) {
    for (var i = 0; i < __rmTeam.length; i++) {
        if (String(__rmTeam[i].id) === String(id)) return __rmTeam[i];
    }
    return null;
}
function rmEsc(s) {
    return (s||'').toString().replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}

function rmCardHtml(u) {
    var kebab = u.email
        ? '<div class="rm-kebab-wrap">'
          + '<button type="button" class="rm-kebab-btn" title="More"><i class="la la-ellipsis-v"></i></button>'
          + '<div class="rm-kebab-menu">'
          + '<a href="javascript:void(0);" class="contact-action-rm" data-type="email" data-name="' + rmEsc(u.name) + '" data-email="' + rmEsc(u.email) + '"><i class="la la-envelope"></i> Email</a>'
          + '</div></div>'
        : '';
    return '<div class="rm-card">'
        + '<div class="d-flex align-items-start justify-content-between gap-2">'
        +   '<div class="d-flex align-items-center gap-2" style="min-width:0;">'
        +     '<div class="rm-avatar ' + u.avatarClass + '">' + rmEsc(u.initial) + '</div>'
        +     '<div style="min-width:0;">'
        +       '<div style="font-size:13px;font-weight:600;color:#181c32;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">' + rmEsc(u.name) + '</div>'
        +       '<div style="font-size:11px;color:#9ca3af;">' + rmEsc(u.role) + '</div>'
        +     '</div>'
        +   '</div>'
        +   '<div class="d-flex align-items-center gap-1 flex-shrink-0">'
        +     '<span class="rm-badge-assigned"><i class="la la-check-circle" style="font-size:12px;"></i> Assigned</span>'
        +     kebab
        +   '</div>'
        + '</div>'
        + '<div class="rm-wl">'
        +   '<div class="rm-wl-item"><div class="rm-wl-top"><i class="la la-briefcase ic-blue"></i><span class="rm-wl-num">' + u.jobs + '</span></div><div class="rm-wl-lbl">Active Jobs</div></div>'
        +   '<div class="rm-wl-item"><div class="rm-wl-top"><i class="la la-crown ic-purple"></i><span class="rm-wl-num">' + u.captainJobs + '</span></div><div class="rm-wl-lbl">Captain Jobs</div></div>'
        +   '<div class="rm-wl-item"><div class="rm-wl-top"><i class="la la-user-friends ic-green"></i><span class="rm-wl-num">' + u.trmTalents + '</span></div><div class="rm-wl-lbl">TRM Talents</div></div>'
        + '</div>'
        + '<button type="button" class="rm-btn-remove rm-remove" data-rmid="' + u.id + '">Remove</button>'
        + '</div>';
}

function renderSelectedRM() {
    var ids = [];
    $('.team_event').each(function () {
        if (String($(this).data('assign')) === '1') ids.push($(this).data('userid'));
    });
    if (ids.length === 0) {
        $('#selectedRMGrid').hide().empty();
        $('#rmEmptyState').show();
    } else {
        var html = '';
        ids.forEach(function (id) { var u = rmFind(id); if (u) html += rmCardHtml(u); });
        $('#selectedRMGrid').html(html).show();
        $('#rmEmptyState').hide();
    }
    $('#selectedRMCount').text('(' + ids.length + ')');
}

$(document).ready(function () {
    renderSelectedRM();

    $(document).on('click', '#scrollToRmTableBtn', function (e) {
        e.preventDefault();
        $('html,body').animate({ scrollTop: $('#addMoreRMSection').offset().top - 20 }, 400);
        setTimeout(function () { $('#searchInputRM').focus(); }, 450);
    });

    $(document).on('click', '.rm-remove', function (e) {
        e.preventDefault();
        $('#btn_' + $(this).data('rmid')).trigger('click');
    });

    // Kebab
    $(document).on('click', '.rm-kebab-btn', function (e) {
        e.preventDefault(); e.stopPropagation();
        var $m = $(this).siblings('.rm-kebab-menu');
        $('.rm-kebab-menu').not($m).removeClass('show');
        $m.toggleClass('show');
    });
    $(document).on('click', '.rm-kebab-menu', function (e) { e.stopPropagation(); });
    $(document).on('click', function () { $('.rm-kebab-menu').removeClass('show'); });

    // Email
    $(document).on('click', '.contact-action-rm', function (e) {
        e.preventDefault(); e.stopPropagation();
        $('.rm-kebab-menu').removeClass('show');
        var email = $(this).data('email');
        if (email) window.location.href = 'mailto:' + email;
    });

    // Search
    var $rows = $('#Iteam_Table_rm tbody tr');
    $('#searchInputRM').on('keyup', function () {
        var val = $(this).val().toLowerCase();
        $rows.each(function () {
            $(this).toggle($(this).text().toLowerCase().indexOf(val) > -1);
        });
    });
});
</script>

<script>
$(document).ready(function () {
    $('body').on('click', '.team_event', function () {
        var currentBenchId = $(this).attr('rel');
        var currentUserId  = $(this).data('userid');
        var assignStatus   = $(this).data('assign');

        if (assignStatus == 0) {
            $('#btn_' + currentUserId).removeClass('add').addClass('remove').text('Remove').data('assign', 1);
        } else {
            $('#btn_' + currentUserId).removeClass('remove').addClass('add').text('Add').data('assign', 0);
        }
        renderSelectedRM();

        var url = "{{ route('service_company.bench-candidates.hmlistingupdate',':id') }}".replace(':id', currentUserId);
        $.ajax({
            type: 'GET',
            url: url,
            data: { '_token': "{{ csrf_token() }}", currentBenchId, currentUserId, assignStatus },
            success: function (response) {
                if (response.status === 'success') {
                    if (assignStatus == 0) {
                        $('#btn_' + currentUserId).removeClass('add').addClass('remove');
                    } else {
                        $('#btn_' + currentUserId).removeClass('remove').addClass('add');
                    }
                }
                renderSelectedRM();
                fetchBenchBenchCandidatesCandidateList();
            }
        });
    });
});
</script>
