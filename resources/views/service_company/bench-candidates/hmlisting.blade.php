<style>
.form-check.form-check-custom .form-check-label{ margin-left: 2.55rem; }

@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');

#kt_activities_body, #kt_activities_scroll, .card-custom, .table, .btn,
.form-control, .form-select, .badge, .symbol-label, .card-title, .card-label,
.assigned-card, .alert, .input-group-text, .empty-state-text, .section-title {
    font-family: 'Poppins', sans-serif;
}

.card-custom { border: 0; box-shadow: 0 0 20px rgba(76,87,125,0.02); border-radius: 12px; }

/* Selected (assigned) member cards */
.assigned-card { border: 1px solid #eff2f5; border-radius: 12px; background: #fff; transition: all .2s ease; padding: 18px 20px; }
.assigned-card:hover { box-shadow: 0 0 15px rgba(76,87,125,0.04); border-color: #e4e6ef; }
.assigned-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 16px; }
.assigned-card.h-100 { display: flex; flex-direction: column; }
.assigned-card.h-100 .ac-actions { margin-top: auto; padding-top: 6px; }
.ac-actions { gap: 10px !important; }

.hm-avatar { width: 40px; height: 40px; border-radius: 50%; flex-shrink: 0; display: flex; align-items: center; justify-content: center; font-size: 14px; font-weight: 700; }

/* Contact icon */
.btn-icon-circle { width: 30px; height: 30px; display: inline-flex; align-items: center; justify-content: center; border-radius: 7px; padding: 0; border: 1px solid #eaecf0; background: #f8fafc; color: #667085; font-size: 12px; }
.btn-icon-circle:hover { background: #eff6ff; color: #2563eb; border-color: #bfdbfe; }

/* Search */
.search-container .input-group-text { background-color: #f5f8fa; border: 1px solid #f5f8fa; color: #a1a5b7; border-right: 0; font-size: 13px; }
.search-container .form-control { background-color: #f5f8fa; border: 1px solid #f5f8fa; font-size: 13px; padding: 0.55rem 1rem; }
.search-container .form-control:focus { background-color: #eef3f7; border-color: #eef3f7; }

/* Status dot */
.status-dot { display: inline-flex; align-items: center; gap: 6px; font-size: 12px; font-weight: 500; }
.status-dot::before { content: ''; display: inline-block; width: 7px; height: 7px; border-radius: 50%; }
.status-dot.active::before { background-color: #50cd89; }
.status-dot.active { color: #50cd89; }
.status-dot.inactive::before { background-color: #f1416c; }
.status-dot.inactive { color: #f1416c; }

.section-title { font-weight: 600; color: #181c32; margin-bottom: 2px; font-size: 15px; }
.section-subtitle { color: #a1a5b7; display: block; font-size: 12px; }

.empty-state-container { display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 1.4rem 1.25rem; text-align: center; }
.empty-state-icon { width: 50px; height: 50px; border-radius: 50%; background-color: #f3f6f9; display: inline-flex; align-items: center; justify-content: center; margin-bottom: .65rem; color: #a1a5b7; font-size: 20px; }
.empty-state-text .empty-title { font-size: 14px; font-weight: 600; color: #181c32; margin-bottom: .15rem; }
.empty-state-text .empty-subtitle { color: #a1a5b7; margin-bottom: .85rem; font-size: 12px; }

.info-banner { background-color: #eff6ff; color: #1e40af; border: 1px solid #dbeafe; border-radius: 8px; padding: 12px 16px; font-size: 12px; display: flex; align-items: center; gap: 10px; }
.info-banner i { font-size: 16px; flex-shrink: 0; color: #2563eb; }

.card-header-compact { padding: 1.25rem 1.5rem .5rem 1.5rem; border-bottom: 0; background: transparent; }
.card-body-compact { padding: 0 1.5rem 1.5rem 1.5rem; }

#Iteam_Table_rm { margin-bottom: 0; }
#Iteam_Table_rm thead th { background-color: #f3f6f9; font-weight: 600; text-transform: uppercase; letter-spacing: .4px; padding: 10px 12px; border-bottom: 1px solid #e4e6ef; color: #a1a5b7; font-size: 11px; white-space: nowrap; }
#Iteam_Table_rm tbody tr { border-bottom: 1px dashed #e4e6ef; }
#Iteam_Table_rm tbody tr:last-child { border-bottom: 0; }
#Iteam_Table_rm tbody td { padding: 12px; font-size: 13px; vertical-align: middle; }
.highlight { background-color: #fff8dd !important; transition: background-color .15s ease; }
.w-100px { width: 100px !important; text-align: center; justify-content: center; }

/* Pills */
.pill { display: inline-flex; align-items: center; font-size: 10px; font-weight: 600; padding: 4px 9px; border-radius: 20px; white-space: nowrap; line-height: 1; }
.pill-assigned { background: #e8fff3; color: #17b26a; }
.pill-assigned .la { color: #17b26a; }

/* Kebab */
.ac-kebab-wrap { position: relative; }
.ac-kebab-btn { width: 26px; height: 26px; border-radius: 6px; border: 0; background: transparent; color: #98a2b3; cursor: pointer; display: inline-flex; align-items: center; justify-content: center; font-size: 15px; transition: all .15s ease; }
.ac-kebab-btn:hover { background: #f2f4f7; color: #475569; }
.ac-kebab-menu { position: absolute; right: 0; top: 30px; z-index: 30; background: #fff; border: 1px solid #eaecf0; border-radius: 8px; box-shadow: 0 8px 24px rgba(16,24,40,.12); padding: 6px; min-width: 152px; display: none; }
.ac-kebab-menu.show { display: block; }
.ac-kebab-menu a { display: flex; align-items: center; gap: 8px; padding: 7px 10px; border-radius: 6px; font-size: 12px; color: #475569 !important; text-decoration: none; }
.ac-kebab-menu a:hover { background: #f8fafc; color: #2563eb !important; }
.ac-kebab-menu a .la { font-size: 14px; width: 16px; text-align: center; }

.ac-actions .btn { flex: 1 1 0; padding: 7px 10px !important; font-size: 11px !important; }

/* Royal-blue palette scoped to this drawer */
#kt_activities_body .btn-light.btn-active-light-primary, #kt_activities_body .btn-light-primary { border: 1px solid #d6e4ff !important; color: #2563eb !important; background-color: #fff !important; font-weight: 500; }
#kt_activities_body .btn-light.btn-active-light-primary:hover, #kt_activities_body .btn-light-primary:hover { background-color: #eef4ff !important; border-color: #2563eb !important; color: #1d4ed8 !important; }
#kt_activities_body .btn-light-danger { border: 1px solid #fbd5de !important; color: #ef4444 !important; background-color: #fff !important; font-weight: 500; }
#kt_activities_body .btn-light-danger:hover { background-color: #fff1f4 !important; border-color: #ef4444 !important; }

@media (max-width: 575.98px){ .assigned-grid { grid-template-columns: 1fr; } .ac-actions { flex-wrap: wrap; } }
</style>

@php
    // Build a view-model per team member once, reusing the exact same queries/logic as before (no business-logic change).
    $rmRows = [];
    foreach ($team as $teamVal) {
        $activeJobs = DB::table('job_assign')->join('jobs', 'jobs.id', '=', 'job_assign.job_id')->where('jobs.status', '=', 'active')->where('job_assign.user_id', $teamVal->id)->count();
        $candidateCount = DB::table('bench_assign')->where('bench_assign.user_id', $teamVal->id)->count();

        $name = trim(@$teamVal->name) ?: '?';
        $firstChar = strtolower($name[0]);
        $ord = ord($firstChar);
        if ($ord >= 97 && $ord <= 101) {
            $avatarClass = 'bg-light-danger text-danger';
        } elseif ($ord >= 102 && $ord <= 106) {
            $avatarClass = 'bg-light-warning text-warning';
        } elseif ($ord >= 107 && $ord <= 111) {
            $avatarClass = 'bg-light-primary text-primary';
        } elseif ($ord >= 112 && $ord <= 116) {
            $avatarClass = 'bg-light-success text-success';
        } elseif ($ord >= 117 && $ord <= 121) {
            $avatarClass = 'bg-dark text-secondary';
        } else {
            $avatarClass = 'bg-light-secondary text-dark';
        }

        $isAssigned = (@$teamVal->bench_assign_id > 0) && ($currentBenchId == $teamVal->bench_applications_id);

        $rmRows[] = [
            'id' => $teamVal->id,
            'name' => ucwords(substr($name, 0, 15)),
            'fullName' => ucwords($name),
            'role' => ucwords(substr(trim(@$teamVal->display_name) ?: 'Team Member', 0, 18)),
            'email' => (string) @$teamVal->email,
            'initial' => strtoupper($firstChar),
            'avatarClass' => $avatarClass,
            'activeJobs' => $activeJobs,
            'candidateCount' => $candidateCount,
            'assigned' => $isAssigned,
            'workingActive' => (@$teamVal->working_status == 1),
        ];
    }
    $selectedRm = array_values(array_filter($rmRows, fn ($r) => $r['assigned']));

    $rmTeamJs = array_map(function ($r) {
        return [
            'id' => $r['id'],
            'name' => $r['fullName'],
            'role' => $r['role'],
            'email' => $r['email'],
            'initial' => $r['initial'],
            'avatarClass' => $r['avatarClass'],
            'jobs' => $r['activeJobs'],
            'talents' => $r['candidateCount'],
        ];
    }, $rmRows);
@endphp

<form>
  <div class="card-body position-relative px-0 py-0" id="kt_activities_body">
    <div id="kt_activities_scroll" class="position-relative scroll-y bg-light pt-2 h-100" data-kt-scroll="true" data-kt-scroll-height="auto" data-kt-scroll-wrappers="#kt_activities_body" data-kt-scroll-dependencies="#kt_activities_header, #kt_activities_footer" data-kt-scroll-offset="5px">

      <!--begin::Selected Relationship Managers-->
      <div class="card card-custom mx-2 mb-3" id="selectedRMSection">
        <div class="card-header card-header-compact">
          <div class="card-title flex-column align-items-start m-0 mt-5">
            <h3 class="section-title mb-0">1. Selected Relationship Managers <span class="text-muted" id="selectedRMCount" style="font-weight:400;">({{ count($selectedRm) }})</span></h3>
            <span class="section-subtitle mt-1">These team members are the relationship managers for this candidate</span>
          </div>
        </div>
        <div class="card-body card-body-compact">
          <div class="assigned-grid" id="selectedRMGrid" style="{{ count($selectedRm) ? '' : 'display:none;' }}">
            @foreach ($selectedRm as $r)
              <div class="assigned-card h-100">
                <div class="d-flex align-items-start justify-content-between mb-3">
                  <div class="d-flex align-items-center" style="min-width:0;">
                    <div class="hm-avatar me-3 {{ $r['avatarClass'] }}">{{ $r['initial'] }}</div>
                    <div style="min-width:0;">
                      <div class="fw-bolder text-dark text-truncate" style="font-size:13px;">{{ $r['fullName'] }}</div>
                      <div class="text-muted text-truncate" style="font-size:11px;">{{ $r['role'] }}</div>
                    </div>
                  </div>
                  <div class="d-flex align-items-center gap-2 flex-shrink-0">
                    <span class="pill pill-assigned"><i class="la la-check-circle me-1"></i>Assigned</span>
                    @if ($r['email'])
                      <div class="ac-kebab-wrap">
                        <button type="button" class="ac-kebab-btn" title="More"><i class="la la-ellipsis-v"></i></button>
                        <div class="ac-kebab-menu">
                          <a href="javascript:void(0);" class="contact-action-rm" data-type="email" data-name="{{ $r['fullName'] }}" data-email="{{ $r['email'] }}"><i class="la la-envelope"></i> Email</a>
                        </div>
                      </div>
                    @endif
                  </div>
                </div>
                <div class="d-flex align-items-center gap-3 text-muted mb-3" style="font-size:11px;">
                  <span><i class="la la-briefcase me-1"></i>{{ $r['activeJobs'] }} Active Jobs</span>
                  <span><i class="la la-users me-1"></i>{{ $r['candidateCount'] }} Talents</span>
                </div>
                <div class="d-flex gap-2 ac-actions">
                  <button type="button" class="btn btn-sm btn-light-danger rm-remove" data-rmid="{{ $r['id'] }}">Remove</button>
                </div>
              </div>
            @endforeach
          </div>
          <div id="rmEmptyState" style="{{ count($selectedRm) ? 'display:none;' : '' }}">
            <div class="empty-state-container">
              <div class="empty-state-icon"><i class="la la-user-check"></i></div>
              <div class="empty-state-text">
                <div class="empty-title">No relationship manager selected</div>
                <div class="empty-subtitle">Select a team member below to assign as the relationship manager for this candidate.</div>
              </div>
              <button type="button" class="btn btn-sm btn-light btn-active-light-primary" id="scrollToRmTableBtn"><i class="la la-plus me-1"></i>Select Team Member</button>
            </div>
          </div>
        </div>
      </div>
      <!--end::Selected Relationship Managers-->

      <!--begin::Info Alert-->
      <div class="mx-2 mb-3">
        <div class="info-banner">
          <i class="la la-info-circle"></i>
          <span>Relationship managers coordinate this candidate's bench engagement and client communication.</span>
        </div>
      </div>
      <!--end::Info Alert-->

      <!--begin::Add Relationship Managers-->
      <div class="card card-custom mx-2 mb-3" id="addMoreRMSection">
        <div class="card-header card-header-compact">
          <div class="card-title flex-column align-items-start m-0">
            <h3 class="section-title mb-0">2. Add Relationship Managers</h3>
            <span class="section-subtitle mt-1">Search and select team members from your company</span>
          </div>
        </div>
        <div class="card-body card-body-compact">

          <!--begin::Filter-->
          <div class="d-flex flex-wrap gap-3 mb-4 align-items-center filter-bar">
            <div class="search-container input-group input-group-lg input-group-solid w-100">
              <div class="input-group-prepend"><span class="input-group-text"><i class="la la-search"></i></span></div>
              <input class="form-control form-control-lg form-control-solid" type="text" id="searchInputRM" placeholder="Search for relationship manager....">
            </div>
          </div>
          <!--end::Filter-->

          <!--begin::Table-->
          <div class="table-responsive">
            <table class="table align-middle table-row-dashed fs-6 gy-5 mb-0" id="Iteam_Table_rm">
              <thead>
                <tr class="fw-bolder text-muted bg-light">
                  <th class="min-w-200px">Name</th>
                  <th class="min-w-100px">Contact</th>
                  <th class="min-w-150px">Workload</th>
                  <th class="min-w-100px">Status</th>
                  <th class="min-w-120px text-end">Action</th>
                </tr>
              </thead>
              <tbody class="text-gray-600 fw-bold">
                @foreach ($rmRows as $r)
                  <tr>
                    <td>
                      <div class="d-flex align-items-center">
                        <div class="symbol symbol-circle symbol-40px overflow-hidden me-3 border-radius-5">
                          <div class="symbol-label fs-3 {{ $r['avatarClass'] }} border-radius-5">{{ $r['initial'] }}</div>
                        </div>
                        <div class="d-flex flex-column">
                          <span class="text-dark fw-bolder fs-6">{{ $r['name'] }}</span>
                          <span class="text-muted fw-bold d-block fs-7">{{ $r['role'] }}</span>
                        </div>
                      </div>
                    </td>
                    <td>
                      @if ($r['email'])
                        <button type="button" class="btn-icon-circle contact-action-rm" data-type="email" data-name="{{ $r['fullName'] }}" data-email="{{ $r['email'] }}" title="Email"><i class="la la-envelope"></i></button>
                      @else
                        <span class="text-muted fs-7">&mdash;</span>
                      @endif
                    </td>
                    <td>
                      <div class="d-flex align-items-center gap-3 text-muted" style="font-size:11px;">
                        <span><i class="la la-briefcase me-1"></i>{{ $r['activeJobs'] }}</span>
                        <span><i class="la la-users me-1"></i>{{ $r['candidateCount'] }}</span>
                      </div>
                    </td>
                    <td><span class="status-dot {{ $r['workingActive'] ? 'active' : 'inactive' }}">{{ $r['workingActive'] ? 'Active' : 'Inactive' }}</span></td>
                    <td class="text-end">
                      <a id="btn_{{ $r['id'] }}" href="javascript:void(0);" class="team_event cp_select btn btn-sm {{ $r['assigned'] ? 'btn-light-danger' : 'btn-light-primary' }} w-100px"
                         rel="{{ $currentBenchId }}" data-assign="{{ $r['assigned'] ? 1 : 0 }}" data-userid="{{ $r['id'] }}">{{ $r['assigned'] ? 'Remove' : 'Add' }}</a>
                    </td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
          <!--end::Table-->

        </div>
      </div>
      <!--end::Add Relationship Managers-->

    </div>
  </div>
</form>

<script>
var __rmTeam = @json($rmTeamJs);

function rmFind(id) { for (var i = 0; i < __rmTeam.length; i++) { if (String(__rmTeam[i].id) === String(id)) return __rmTeam[i]; } return null; }
function rmEsc(s) { return (s || '').toString().replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;'); }

function rmCardHtml(u) {
    return '<div class="assigned-card h-100">'
        + '<div class="d-flex align-items-start justify-content-between mb-3">'
        +   '<div class="d-flex align-items-center" style="min-width:0;">'
        +     '<div class="hm-avatar me-3 ' + u.avatarClass + '">' + rmEsc(u.initial) + '</div>'
        +     '<div style="min-width:0;">'
        +       '<div class="fw-bolder text-dark text-truncate" style="font-size:13px;">' + rmEsc(u.name) + '</div>'
        +       '<div class="text-muted text-truncate" style="font-size:11px;">' + rmEsc(u.role) + '</div>'
        +     '</div>'
        +   '</div>'
        +   '<div class="d-flex align-items-center gap-2 flex-shrink-0">'
        +     '<span class="pill pill-assigned"><i class="la la-check-circle me-1"></i>Assigned</span>'
        +     (u.email ? '<div class="ac-kebab-wrap"><button type="button" class="ac-kebab-btn" title="More"><i class="la la-ellipsis-v"></i></button>'
        +       '<div class="ac-kebab-menu">'
        +         '<a href="javascript:void(0);" class="contact-action-rm" data-type="email" data-name="' + rmEsc(u.name) + '" data-email="' + rmEsc(u.email) + '"><i class="la la-envelope"></i> Email</a>'
        +       '</div></div>' : '')
        +   '</div>'
        + '</div>'
        + '<div class="d-flex align-items-center gap-3 text-muted mb-3" style="font-size:11px;">'
        +   '<span><i class="la la-briefcase me-1"></i>' + u.jobs + ' Active Jobs</span>'
        +   '<span><i class="la la-users me-1"></i>' + u.talents + ' Talents</span>'
        + '</div>'
        + '<div class="d-flex gap-2 ac-actions">'
        +   '<button type="button" class="btn btn-sm btn-light-danger rm-remove" data-rmid="' + u.id + '">Remove</button>'
        + '</div>'
        + '</div>';
}

function renderSelectedRM() {
    var ids = [];
    $('.team_event').each(function () { if (String($(this).data('assign')) === '1') { ids.push($(this).data('userid')); } });

    if (ids.length === 0) {
        $('#selectedRMGrid').hide().empty();
        $('#rmEmptyState').show();
    } else {
        var html = '';
        ids.forEach(function (id) { var u = rmFind(id); if (u) { html += rmCardHtml(u); } });
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

    // Card Remove -> run the real table toggle (fires .team_event -> ajax add/remove)
    $(document).on('click', '.rm-remove', function (e) {
        e.preventDefault();
        var id = $(this).data('rmid');
        $('#btn_' + id).trigger('click');
    });

    // Kebab dropdown
    $(document).on('click', '.ac-kebab-btn', function (e) { e.preventDefault(); e.stopPropagation(); var $m = $(this).siblings('.ac-kebab-menu'); $('.ac-kebab-menu').not($m).removeClass('show'); $m.toggleClass('show'); });
    $(document).on('click', '.ac-kebab-menu', function (e) { e.stopPropagation(); });
    $(document).on('click', function () { $('.ac-kebab-menu').removeClass('show'); });

    // Email quick-action
    $(document).on('click', '.contact-action-rm', function (e) {
        e.preventDefault();
        e.stopPropagation();
        $('.ac-kebab-menu').removeClass('show');
        var email = $(this).data('email');
        if (email) { window.location.href = 'mailto:' + email; }
    });

    // Search
    var $rmRowsEl = $('#Iteam_Table_rm').find('tbody tr');
    $('#searchInputRM').on('keyup', function () {
        var value = $(this).val().toLowerCase();
        $rmRowsEl.find('td').removeClass('highlight');
        $rmRowsEl.each(function () {
            var $row = $(this);
            var rowText = $row.text().toLowerCase();
            $row.toggle(rowText.indexOf(value) > -1);
        });
    });
});
</script>

<script>

$(document).ready(function(){

						$('body').on('click', '.team_event', function(){

														var currentBenchId    		=	$(this).attr('rel');
														var currentUserId    		=	$(this).data('userid');
														var assignStatus    		=	$(this).data('assign');

														var id    					=	currentUserId;
														if(assignStatus == 0){
																$('#btn_'+currentUserId).addClass("btn-light-danger");
																$('#btn_'+currentUserId).removeClass("btn-light-primary");
																$('#btn_'+currentUserId).text('Remove');
																$(this).data('assign',1);
														}else if(assignStatus == 1){
																$('#btn_'+currentUserId).addClass("btn-light-primary");
																$('#btn_'+currentUserId).removeClass("btn-light-danger");
																$('#btn_'+currentUserId).text('Add');
																$(this).data('assign',0);
														}
														renderSelectedRM();
														var token 					=	"{{ csrf_token() }}";

														var url   					=	"{{ route('service_company.bench-candidates.hmlistingupdate',':id') }}";
														url     					=	url.replace(':id', id);
														var baseurl    				=	"{{ asset('assets/images/Loading2.gif') }}";
														$.ajax({
																	type  		:	'GET',
																	url   		:	url,
																	data		:	{'_token': token, 'currentBenchId' : currentBenchId, 'currentUserId' : currentUserId, 'assignStatus' : assignStatus },
																	success 	:	function (response)
																	{
																						if(response.status=='success'){

																							if(assignStatus == 0){
																									$('#btn_'+currentUserId).addClass("btn-light-danger");
																									$('#btn_'+currentUserId).removeClass("btn-light-primary");
																							}else if(assignStatus == 1){
																									$('#btn_'+currentUserId).addClass("btn-light-primary");
																									$('#btn_'+currentUserId).removeClass("btn-light-danger");
																							}
																						}
																						renderSelectedRM();
																						fetchBenchBenchCandidatesCandidateList();
																	}
															});

					});

});

</script>
