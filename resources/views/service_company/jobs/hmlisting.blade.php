<?php
use App\Helper\GeneralMethod;
// dd("jd");
?>
<style>
.form-check.form-check-custom .form-check-label { margin-left: 2.55rem; }

@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');

#kt_activities_body, #kt_activities_scroll, .card-custom, .table, .btn,
.form-control, .form-select, .badge, .symbol-label, .card-title, .card-label,
.assigned-card, .alert, .input-group-text, .empty-state-text, .section-title {
    font-family: 'Poppins', sans-serif;
}

.card-custom { border: 0; box-shadow: 0 0 20px rgba(76,87,125,0.02); border-radius: 12px; }

/* Selected member cards */
.assigned-card { border: 1px solid #eff2f5; border-radius: 12px; background: #fff; transition: all .2s ease; padding: 18px 20px; }
.assigned-card:hover { box-shadow: 0 0 15px rgba(76,87,125,0.04); border-color: #e4e6ef; }
.assigned-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 16px; }
.assigned-card.h-100 { display: flex; flex-direction: column; }
.assigned-card.h-100 .ac-actions { margin-top: auto; padding-top: 6px; }
.ac-actions { gap: 10px !important; }

.hm-avatar { width: 40px; height: 40px; border-radius: 50%; flex-shrink: 0; color: #fff; display: flex; align-items: center; justify-content: center; font-size: 14px; font-weight: 600; }

/* Contact icons */
.btn-icon-circle { width: 30px; height: 30px; display: inline-flex; align-items: center; justify-content: center; border-radius: 7px; padding: 0; border: 1px solid #eaecf0; background: #f8fafc; color: #667085; font-size: 12px; }
.btn-icon-circle:hover { background: #eff6ff; color: #2563eb; border-color: #bfdbfe; }

/* Clean full-width search */
.hm-search { position: relative; width: 100%; }
.hm-search > i { position: absolute; left: 15px; top: 50%; transform: translateY(-50%); color: #a1a5b7; font-size: 17px; pointer-events: none; }
.hm-search input { width: 100%; height: 46px; border: 1px solid #eef2f6; background: #f5f8fa; border-radius: 10px; padding: 0 16px 0 42px; font-size: 13px; color: #181c32; outline: none; transition: all .15s ease; }
.hm-search input::placeholder { color: #a1a5b7; }
.hm-search input:focus { background: #fff; border-color: #bfdbfe; box-shadow: 0 0 0 3px rgba(37,99,235,.08); }

/* Status dot */
.status-dot { display: inline-flex; align-items: center; gap: 6px; font-size: 12px; font-weight: 500; }
.status-dot::before { content: ''; display: inline-block; width: 7px; height: 7px; border-radius: 50%; }
.status-dot.active::before { background-color: #50cd89; }
.status-dot.active { color: #50cd89; }
.status-dot.inactive::before { background-color: #f1416c; }
.status-dot.inactive { color: #f1416c; }

.badge.badge-light-primary { background-color: #f1faff; color: #009ef7; }
.jobs-badge { font-size: 12px; font-weight: 600; }

.section-title { font-weight: 600; color: #181c32; margin-bottom: 2px; font-size: 15px; }
.section-subtitle { color: #a1a5b7; display: block; font-size: 12px; }

.empty-state-container { display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 1.4rem 1.25rem; text-align: center; }
.empty-state-icon { width: 50px; height: 50px; border-radius: 50%; background-color: #f3f6f9; display: inline-flex; align-items: center; justify-content: center; margin-bottom: .65rem; color: #a1a5b7; font-size: 20px; }
.empty-state-text .empty-title { font-size: 14px; font-weight: 600; color: #181c32; margin-bottom: .15rem; }
.empty-state-text .empty-subtitle { color: #a1a5b7; margin-bottom: .85rem; font-size: 12px; }

/* Drawer header (logo + intro) */
.hm-drawer-header { display: flex; align-items: center; gap: 14px; padding: 6px 4px 2px; }
.hm-logo { width: 46px; height: 46px; border-radius: 12px; background: linear-gradient(135deg, #2f6bff, #1f9cf0); color: #fff; display: flex; align-items: center; justify-content: center; font-size: 22px; flex-shrink: 0; box-shadow: 0 6px 16px rgba(37,99,235,.25); overflow: hidden; }
.hm-logo img { width: 100%; height: 100%; object-fit: cover; }
.hm-head-title { font-size: 18px; font-weight: 700; color: #181c32; line-height: 1.2; }
.hm-head-sub { font-size: 12px; color: #a1a5b7; margin-top: 2px; }

.info-banner { background-color: #eff6ff; color: #1e40af; border: 1px solid #dbeafe; border-radius: 8px; padding: 12px 16px; font-size: 12px; display: flex; align-items: center; gap: 10px; }
.info-banner i { font-size: 16px; flex-shrink: 0; color: #2563eb; }

.card-header-compact { padding: 1.25rem 1.5rem .5rem 1.5rem; border-bottom: 0; background: transparent; }
.card-body-compact { padding: 0 1.5rem 1.5rem 1.5rem; }

#Iteam_Table111 { margin-bottom: 0; }
#Iteam_Table111 thead th { background-color: #f3f6f9; font-weight: 600; text-transform: uppercase; letter-spacing: .4px; padding: 10px 12px; border-bottom: 1px solid #e4e6ef; color: #a1a5b7; font-size: 11px; white-space: nowrap; }
#Iteam_Table111 tbody tr { border-bottom: 1px dashed #e4e6ef; }
#Iteam_Table111 tbody tr:last-child { border-bottom: 0; }
#Iteam_Table111 tbody td { padding: 12px; font-size: 13px; vertical-align: middle; }
.highlight { background-color: #fff8dd !important; transition: background-color .15s ease; }
.w-100px { width: 100px !important; text-align: center; justify-content: center; }
.member-name { font-size: 13px; font-weight: 600; color: #181c32; }
.member-role { font-size: 11px; color: #a1a5b7; }

/* Pills */
.pill { display: inline-flex; align-items: center; font-size: 10px; font-weight: 600; padding: 4px 9px; border-radius: 20px; white-space: nowrap; line-height: 1; }
.pill-captain { background: #fff8e6; color: #d99100; }
.pill-captain .la { color: #e0a400; }
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
/* The card "Make Captain" keeps the .hidden-button class (to trigger the original handler),
   but a global rule hides .hidden-button — force it visible & normally-positioned here. */
#kt_activities_body .ac-actions .hidden-button { display: inline-flex !important; position: static !important; transform: none !important; bottom: auto !important; left: auto !important; align-items: center; justify-content: center; }

/* Royal-blue palette scoped to this drawer */
#kt_activities_body .btn-light.btn-active-light-primary, #kt_activities_body .btn-light-primary { border: 1px solid #d6e4ff !important; color: #2563eb !important; background-color: #fff !important; font-weight: 500; }
#kt_activities_body .btn-light.btn-active-light-primary:hover, #kt_activities_body .btn-light-primary:hover { background-color: #eef4ff !important; border-color: #2563eb !important; color: #1d4ed8 !important; }
#kt_activities_body .btn-light-danger { border: 1px solid #fbd5de !important; color: #ef4444 !important; background-color: #fff !important; font-weight: 500; }
#kt_activities_body .btn-light-danger:hover { background-color: #fff1f4 !important; border-color: #ef4444 !important; }
#kt_activities_body .btn-primary { background-color: #2563eb !important; border-color: #2563eb !important; }
#kt_activities_body .btn-primary:hover { background-color: #1d4ed8 !important; border-color: #1d4ed8 !important; }

/* Contact popup (email / call / whatsapp) */
.cm-overlay { position: fixed; inset: 0; background: rgba(16,24,40,.45); z-index: 10050; display: none; align-items: center; justify-content: center; padding: 20px; }
.cm-overlay.show { display: flex; }
.cm-box { background: #fff; border-radius: 16px; width: 380px; max-width: 92vw; padding: 28px 26px; text-align: center; box-shadow: 0 24px 60px rgba(0,0,0,.22); font-family: 'Poppins', sans-serif; animation: cmpop .15s ease; }
@keyframes cmpop { from { transform: scale(.96); opacity: 0; } to { transform: scale(1); opacity: 1; } }
.cm-icon { width: 56px; height: 56px; border-radius: 50%; background: #eaf4ff; color: #2f9bff; display: flex; align-items: center; justify-content: center; font-size: 24px; margin: 0 auto 16px; }
.cm-title { font-size: 17px; font-weight: 700; color: #1a1a2e; margin-bottom: 4px; }
.cm-sub { font-size: 12px; color: #98a2b3; margin-bottom: 18px; }
.cm-value { background: #f5f8fb; border-radius: 10px; padding: 13px 12px; font-size: 14px; font-weight: 600; color: #1a1a2e; margin-bottom: 18px; word-break: break-all; }
.cm-actions { display: flex; gap: 12px; }
.cm-btn { flex: 1; padding: 11px 14px; border-radius: 9px; font-size: 13px; font-weight: 600; cursor: pointer; display: inline-flex; align-items: center; justify-content: center; gap: 7px; text-decoration: none; border: 1px solid transparent; }
.cm-btn.cm-cancel { background: #fff; border-color: #e4e6ef; color: #475569; }
.cm-btn.cm-cancel:hover { background: #f8fafc; }
.cm-btn.cm-go { background: #1f9cf0; border-color: #1f9cf0; color: #fff !important; }
.cm-btn.cm-go:hover { background: #1481cf; border-color: #1481cf; }

/* Drawer fit */
#kt_activitiesHM.drawer, #kt_activitiesHM { width: min(1160px, 96vw) !important; max-width: 96vw !important; }
#kt_activities_body { max-width: 1180px; margin: 0 auto; }
@media only screen and (max-width: 1199.98px){ #kt_activitiesHM { width: min(960px, 96vw) !important; } }
@media (max-width: 767.98px){ .filter-bar { flex-direction: column; width: 100%; } }
@media (max-width: 575.98px){ .assigned-grid { grid-template-columns: 1fr; } .ac-actions { flex-wrap: wrap; } }
</style>

<?php
/* ---- Permission gate (preserved) + captain id ---- */
$team_event = 'team_event';
if(in_array("add_team_on_requisition", $userPermissions)){
    $team_event = 'team_event';
}else{
    $team_event = 'team_event_not';
}

$captainId = null;
foreach($team as $teamVal){
    if(@$teamVal->id == @$teamVal->spok_user_id){ $captainId = $teamVal->id; break; }
}

/* Logged-in company logo for the header square (uses Company model's logo_url accessor) */
$company_logo = '';
try {
    $cmp = \App\Models\Company::find(auth()->user()->company_id);
    if ($cmp && !empty($cmp->logo)) { $company_logo = $cmp->logo_url; }
} catch (\Throwable $e) { $company_logo = ''; }
?>

<form>
  <div class="card-body position-relative px-0 py-0" id="kt_activities_body">
    <div id="kt_activities_scroll" class="position-relative scroll-y bg-light pt-2 h-100" data-kt-scroll="true" data-kt-scroll-height="auto" data-kt-scroll-wrappers="#kt_activities_body" data-kt-scroll-dependencies="#kt_activities_header, #kt_activities_footer" data-kt-scroll-offset="5px" style="">

    <!--begin::Drawer Header (logo + intro)-->
    <div class="hm-drawer-header mx-2 mb-3">
      <div class="hm-logo">
        <?php if(!empty(@$company_logo)): ?>
          <img src="<?php echo @$company_logo; ?>" alt="logo">
        <?php else: ?>
          <i class="la la-users"></i>
        <?php endif; ?>
      </div>
      <div>
        <div class="hm-head-title">Select Team Members</div>
        <div class="hm-head-sub">Choose the team members who will work on this position</div>
      </div>
    </div>
    <!--end::Drawer Header-->

    <!--begin::Assigned Team Members-->
    <div class="card card-custom mx-2 mb-3" id="assignedSection">
      <div class="card-header card-header-compact">
        <div class="card-title flex-column align-items-start m-0">
          <h3 class="section-title mb-0">1. Assigned Team Members <span class="text-muted" id="assignedCountDisplay" style="font-weight:400;">(0)</span></h3>
          <span class="section-subtitle mt-1">These members are working on this position</span>
        </div>
      </div>
      <div class="card-body card-body-compact">
        <div class="assigned-grid" id="assignedGrid" style="display:none;"></div>
        <div id="teamEmptyState">
          <div class="empty-state-container">
            <div class="empty-state-icon"><i class="la la-user-friends"></i></div>
            <div class="empty-state-text">
              <div class="empty-title">No team members assigned</div>
              <div class="empty-subtitle">Assign members below to start building your team for this position.</div>
            </div>
            <button type="button" class="btn btn-sm btn-light btn-active-light-primary" id="scrollToTableBtn"><i class="la la-plus me-1"></i>Add Team Members</button>
          </div>
        </div>
      </div>
    </div>
    <!--end::Assigned Team Members-->

    <!--begin::Info Alert-->
    <div class="mx-2 mb-3">
      <div class="info-banner">
        <i class="la la-info-circle"></i>
        <span>Only assigned members can be made captain. There can be only one captain for a position.</span>
      </div>
    </div>
    <!--end::Info Alert-->

    <!--begin::Add More Team Members-->
    <div class="card card-custom mx-2 mb-3" id="addMoreTeamSection">
      <div class="card-header card-header-compact">
        <div class="card-title flex-column align-items-start m-0">
          <h3 class="section-title mb-0">2. Add Team Members</h3>
          <span class="section-subtitle mt-1">Search and select members to assign to this position</span>
        </div>
      </div>
      <div class="card-body card-body-compact">

        <!--begin::Filter (full width, aligned)-->
        <div class="filter-bar mb-4">
          <div class="hm-search">
            <i class="la la-search"></i>
            <input type="text" id="searchInput" placeholder="Search for team members...">
          </div>
        </div>
        <!--end::Filter-->

        <!--begin::Table-->
        <div class="table-responsive">
          <table class="table align-middle table-row-dashed fs-6 gy-5 mb-0" id="Iteam_Table111">
            <thead>
              <tr class="fw-bolder text-muted bg-light">
                <th class="min-w-200px">Team Member</th>
                <th class="min-w-100px">Jobs</th>
                <th class="min-w-120px">Role</th>
                <th class="min-w-100px">Status</th>
                <th class="min-w-140px">Contact</th>
                <th class="min-w-120px text-end">Action</th>
              </tr>
            </thead>
            <tbody class="text-gray-600 fw-bold">
<?php
            foreach($team as $teamVal)
            {
                $activeJobs = DB::table('job_assign')->join('jobs','jobs.id','=','job_assign.job_id')->where('jobs.status','=','active')->where('job_assign.user_id', $teamVal->id)->count();

                $bgTextDecore = GeneralMethod::icoinText(@$teamVal->name);
                $strVal = strtolower(substr(trim((string) @$teamVal->name), 0, 1));

                $btn_text = $btn_class = '';
                $btn_assign = 0;
                if(@$teamVal->job_assign_id > 0 && ($currentJobId == $teamVal->job_id)){
                    $btn_text   = 'Remove';
                    $btn_class  = 'btn-light-danger';
                    $btn_assign = 1;
                }else{
                    $btn_text   = 'Add';
                    $btn_class  = 'btn-light-primary';
                    $btn_assign = 0;
                }

                $rName  = htmlspecialchars((string) @$teamVal->name, ENT_QUOTES);
                $rEmail = htmlspecialchars((string) @$teamVal->email, ENT_QUOTES);
                $rPhone = htmlspecialchars((string) (@$teamVal->phone ?: (@$teamVal->mobile ?: (@$teamVal->contact_number ?: ''))), ENT_QUOTES);
                $rCode  = htmlspecialchars((string) (@$teamVal->calling_code ?: 'in'), ENT_QUOTES);
?>
              <tr>
                <td>
                  <div class="d-flex align-items-center">
                    <div class="symbol symbol-circle symbol-40px overflow-hidden me-3 border-radius-5">
                      <div class="symbol-label <?php echo @$bgTextDecore; ?> border-radius-5"><?php echo strtoupper(@$strVal); ?></div>
                    </div>
                    <div class="d-flex flex-column {{ @$teamVal->id==@$teamVal->spok_user_id ? '' : 'show_hidden' }}" style="position:relative;">
                      <span class="member-name"><?php echo ucwords(substr(@$teamVal->name, 0, 30)); ?>
                        <?php if(@$teamVal->id == @$teamVal->spok_user_id): ?><sup style="color:#d99100;font-weight:600;">(Captain)</sup><?php endif; ?>
                      </span>
                      <span class="member-role"><?php echo ucwords(@$teamVal->designation); ?></span>
                    </div>
                  </div>
                </td>
                <td>
                  <div class="show-detail-team" data-row-id="{{ $teamVal->id }}" style="cursor:pointer;">
                    <span class="jobs-badge <?php echo ($activeJobs >= 1) ? 'text-success' : 'text-muted'; ?>"><?php echo $activeJobs; ?> Active</span>
                  </div>
                </td>
                <td><span class="text-muted" style="font-size:12px;"><?php echo ucwords(substr(@$teamVal->display_name, 0, 15)); ?></span></td>
                <td>
                  <?php if(@$teamVal->working_status == 1): ?>
                  <span class="status-dot active">Active</span>
                  <?php else: ?>
                  <span class="status-dot inactive">Inactive</span>
                  <?php endif; ?>
                </td>
                <td>
                  <div class="d-flex align-items-center gap-1">
                    <button type="button" class="btn-icon-circle contact-action" data-type="email" data-name="<?php echo $rName; ?>" data-email="<?php echo $rEmail; ?>" title="Email"><i class="la la-envelope"></i></button>
                    <button type="button" class="btn-icon-circle contact-action" data-type="call" data-name="<?php echo $rName; ?>" data-phone="<?php echo $rPhone; ?>" data-code="<?php echo $rCode; ?>" title="Call"><i class="la la-phone"></i></button>
                    <button type="button" class="btn-icon-circle contact-action" data-type="whatsapp" data-name="<?php echo $rName; ?>" data-phone="<?php echo $rPhone; ?>" data-code="<?php echo $rCode; ?>" title="WhatsApp"><i class="la la-whatsapp"></i></button>
                  </div>
                </td>
                <td class="text-end">
                  {{-- Original assign/remove button — handler, id and data attributes preserved exactly --}}
                  <a id="btn_<?php echo @$teamVal->id ?>" href="javascript:void(0);" class="<?= $team_event ?> {{ ($team_event=='team_event_not') ? '' : 'cp_select' }} btn btn-sm <?php echo $btn_class; ?> w-100px"
                     rel="<?php echo @$currentJobId ?>" data-assign="<?php echo $btn_assign; ?>" data-userid="<?php echo @$teamVal->id ?>"><?php echo ($btn_text == 'Add' ? 'Assign' : $btn_text); ?></a>
                </td>
              </tr>
<?php } ?>
            </tbody>
          </table>
        </div>
        <!--end::Table-->

      </div>
    </div>
    <!--end::Add More Team Members-->

    </div>
  </div>
</form>


<script>
	$(document).ready(function(){

		            $(document).on('click', '.team_event', function(e){
									e.preventDefault();

									    var $btn = $(this);
										if ($btn.data('processing')) return; // prevent double click
										$btn.data('processing', true);
												fetchCandidates();

														var currentJobId    		=	$(this).attr('rel');
														var currentUserId    		=	$(this).data('userid');
														var assignStatus    		=	$(this).data('assign');
														var id    					=	currentUserId;
														setTimeout(() => {


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
														var makespokesperson	=	false;
														var token 					=	"{{ csrf_token() }}";
														var url   					=	"{{ route('service_company.jobs.hmlistingupdate',':id') }}";
														url     					=	url.replace(':id', id);
														var baseurl    				=	"{{ asset('assets/images/Loading2.gif') }}";
														$.ajax({
																	type  		:	'GET',
																	url   		:	url,
																	data		:	{'_token': token, 'currentJobId' : currentJobId, 'currentUserId' : currentUserId, 'assignStatus' : assignStatus,'makespokesperson':makespokesperson },
																	success 	:	function (response) {
																						if(response.status=='success'){
																							if(assignStatus == 0){

																									$('#btn_'+currentUserId).addClass("btn-light-danger");
																									$('#btn_'+currentUserId).removeClass("btn-light-primary");
																									// $('#btn_'+currentUserId).text('Remove');
																							}else if(assignStatus == 1){

																									$('#btn_'+currentUserId).addClass("btn-light-primary");
																									$('#btn_'+currentUserId).removeClass("btn-light-danger");
																									// $('#btn_'+currentUserId).text('Add');
																							}
																						}

																						// table._fnDraw();
																						// load_jobs._fnDraw();
																						fetchCandidates();

																	},
																	complete: function() {
																		$btn.data('processing', false); // unlock after AJAX completes
																	}
															});
															}, 500);

					});


					$(document).on('click', '.hidden-button', function(e){
							e.preventDefault();
							var ele = $(this);

							 // Prevent multiple clicks
							if (ele.hasClass('processing')) {
								return false; // Exit if the button is already processing
							}
							ele.addClass('processing'); // Mark as processing


														var currentJobId    		=	$(this).attr('rel');
														var currentUserId    		=	$(this).data('userid');
														var assignStatus    		=	$(this).data('assign');


														var id    					=	currentUserId;
														var token 					=	"{{ csrf_token() }}";
														var makespokesperson    					=	true;
														ele.text('Captain Changing...')
														var url   					=	"{{ route('service_company.jobs.hmlistingupdate',':id') }}";
														url     					=	url.replace(':id', id);
														var baseurl    				=	"{{ asset('assets/images/Loading2.gif') }}";
														$.ajax({
																	type  		:	'GET',
																	url   		:	url,
																	data		:	{'_token': token, 'currentJobId' : currentJobId, 'currentUserId' : currentUserId, 'assignStatus' : assignStatus,'makespokesperson':makespokesperson },

										success 	:	function (response)
										                {
															if(response.status=='success'){
																ele.text('Captain Assigned Successfully');
																setTimeout(() => {
																	 $('#kt_activitiesHM_close').click();
																	 fetchCandidates();
																}, 2000);


														}




										},
										complete: function () {
											ele.removeClass('processing'); // Allow clicks again after the process
										}
															});


					});




});
</script>

<!--begin::New UI: real-time assigned cards + contact popups (additive, no backend logic changed)-->
<script>
var __teamData = <?php echo json_encode(array_map(function($t){ return ['id'=>@$t->id,'name'=>@$t->name,'designation'=>@$t->designation,'email'=>@$t->email,'number'=>(@$t->phone ?: (@$t->mobile ?: (@$t->contact_number ?: ''))),'code'=>(@$t->calling_code ?: 'in')]; }, (is_array($team) ? $team : (method_exists($team,'all') ? $team->all() : (array) $team)))); ?>;
var __captainId = <?php echo $captainId !== null ? json_encode((string)$captainId) : 'null'; ?>;
var __currentJobId = "<?php echo @$currentJobId; ?>";
var __assignedSet = {};

function tmColor(ch){ var c=(ch||'a').toLowerCase().charCodeAt(0); if(c>=97&&c<=101)return '#ef4444'; if(c>=102&&c<=106)return '#f59e0b'; if(c>=107&&c<=111)return '#2563eb'; if(c>=112&&c<=116)return '#10b981'; if(c>=117&&c<=121)return '#334155'; return '#64748b'; }
function tmInitials(n){ n=(n||'').trim(); var p=n.split(/\s+/); var s=(p[0]?p[0].charAt(0):'')+(p[1]?p[1].charAt(0):''); return (s||n.charAt(0)||'?').toUpperCase(); }
function tmEsc(s){ return (s||'').toString().replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;'); }
function tmTitle(s){ return (s||'').toString().replace(/\b\w/g,function(c){return c.toUpperCase();}); }
function tmFind(id){ for(var i=0;i<__teamData.length;i++){ if(String(__teamData[i].id)===String(id)) return __teamData[i]; } return null; }

function tmCardHtml(m){
    var isCaptain = (String(m.id) === String(__captainId));
    var role = m.designation ? m.designation : 'Team Member';
    var pill = isCaptain
        ? '<span class="pill pill-captain"><i class="la la-crown me-1"></i>Captain</span>'
        : '<span class="pill pill-assigned"><i class="la la-check-circle me-1"></i>Assigned</span>';
    // Captain: only Remove (no transfer). Every other selected member: Make Captain (real) + Remove.
    var actions = isCaptain
        ? '<button type="button" class="btn btn-sm btn-light-danger tm-remove" data-userid="'+m.id+'">Remove</button>'
        : '<a href="javascript:void(0);" class="btn btn-sm btn-light btn-active-light-primary hidden-button" rel="'+tmEsc(__currentJobId)+'" data-assign="3" data-userid="'+m.id+'">Make Captain</a>'
          + '<button type="button" class="btn btn-sm btn-light-danger tm-remove" data-userid="'+m.id+'">Remove</button>';
    return '<div class="assigned-card h-100" id="assigned_card_'+m.id+'">'
        + '<div class="d-flex align-items-start justify-content-between mb-3">'
        +   '<div class="d-flex align-items-center" style="min-width:0;">'
        +     '<div class="hm-avatar me-3" style="background:'+tmColor((m.name||'').charAt(0))+'">'+tmEsc(tmInitials(m.name))+'</div>'
        +     '<div style="min-width:0;">'
        +       '<div class="fw-bolder text-dark text-truncate" style="font-size:13px;">'+tmEsc(tmTitle((m.name||'').substring(0,22)))+'</div>'
        +       '<div class="text-muted text-truncate" style="font-size:11px;">'+tmEsc(tmTitle((role||'').substring(0,22)))+'</div>'
        +     '</div>'
        +   '</div>'
        +   '<div class="d-flex align-items-center gap-2 flex-shrink-0">'+pill
        +     '<div class="ac-kebab-wrap"><button type="button" class="ac-kebab-btn" title="More"><i class="la la-ellipsis-v"></i></button>'
        +       '<div class="ac-kebab-menu">'
        +         '<a href="javascript:void(0);" class="contact-action" data-type="email" data-name="'+tmEsc(m.name)+'" data-email="'+tmEsc(m.email)+'"><i class="la la-envelope"></i> Email</a>'
        +         '<a href="javascript:void(0);" class="contact-action" data-type="call" data-name="'+tmEsc(m.name)+'" data-phone="'+tmEsc(m.number)+'" data-code="'+tmEsc(m.code)+'"><i class="la la-phone"></i> Call</a>'
        +         '<a href="javascript:void(0);" class="contact-action" data-type="whatsapp" data-name="'+tmEsc(m.name)+'" data-phone="'+tmEsc(m.number)+'" data-code="'+tmEsc(m.code)+'"><i class="la la-whatsapp"></i> WhatsApp</a>'
        +       '</div>'
        +     '</div>'
        +   '</div>'
        + '</div>'
        + '<div class="d-flex gap-2 ac-actions">'+actions+'</div>'
        + '</div>';
}

// Single, debounced render (one DOM pass per frame — no flicker / double load)
var __renderQueued = false;
function renderAssignedTeam(){
    if(__renderQueued) return;
    __renderQueued = true;
    requestAnimationFrame(function(){
        __renderQueued = false;
        var assigned = Object.keys(__assignedSet).map(tmFind).filter(function(m){ return m; });
        assigned.sort(function(a,b){ return (String(b.id)===String(__captainId)?1:0) - (String(a.id)===String(__captainId)?1:0); });
        if(assigned.length === 0){
            $('#assignedGrid').hide().empty();
            $('#teamEmptyState').show();
        } else {
            var html=''; assigned.forEach(function(m){ html += tmCardHtml(m); });
            $('#assignedGrid').html(html).show();
            $('#teamEmptyState').hide();
        }
        $('#assignedCountDisplay').text('('+assigned.length+')');
    });
}

$(document).ready(function() {
    // Seed assigned set from the server-rendered button states, then render once
    __teamData.forEach(function(m){
        var $b = $('#btn_'+m.id);
        if($b.length && String($b.attr('data-assign')) === '1'){ __assignedSet[m.id] = true; }
    });
    renderAssignedTeam();

    // Real-time, instant: when a table Assign/Remove is clicked, update the cards immediately
    // (read the pre-toggle state from the same value the original handler reads)
    $(document).on('click', '#Iteam_Table111 .cp_select', function(){
        var id = $(this).data('userid');
        var willAssign = (String($(this).data('assign')) === '0');
        if(willAssign){ __assignedSet[id] = true; } else { delete __assignedSet[id]; }
        renderAssignedTeam();
    });

    // Always show "Assign" (never "Add") on the table buttons — normalize whenever they change
    function tmNormalizeLabels(){ $('#Iteam_Table111 .cp_select').each(function(){ if($(this).text().trim()==='Add'){ $(this).text('Assign'); } }); }
    tmNormalizeLabels();
    var __tbl = document.getElementById('Iteam_Table111');
    if(__tbl){ try { new MutationObserver(tmNormalizeLabels).observe(__tbl, {subtree:true, childList:true, characterData:true}); } catch(e){} }

    // Search/filter the table rows
    var $rows = $('#Iteam_Table111').find('tbody tr');
    $('#searchInput').on('keyup', function() {
        var value = $(this).val().toLowerCase();
        $rows.find('td').removeClass('highlight');
        $rows.each(function() {
            var $row = $(this), rowText = '';
            $row.find('td').each(function() { rowText += $(this).text().toLowerCase() + ' '; });
            if (rowText.indexOf(value) > -1) {
                $row.show();
                if (value.trim() !== '') {
                    $row.find('td').each(function() { if ($(this).text().toLowerCase().indexOf(value) > -1) { $(this).addClass('highlight'); } });
                }
            } else { $row.hide(); }
        });
    });

    // Empty-state scroll to table
    $(document).on('click', '#scrollToTableBtn', function(e){ e.preventDefault(); $('html,body').animate({scrollTop:$('#addMoreTeamSection').offset().top-20},400); setTimeout(function(){ $('#searchInput').focus(); },450); });

    // Card Remove → update cards instantly + fire the real table Remove (server AJAX)
    $(document).on('click', '.tm-remove', function(e){
        e.preventDefault();
        var userid = $(this).data('userid');
        delete __assignedSet[userid];
        renderAssignedTeam();
        var $tableBtn = $('#btn_' + userid);
        if ($tableBtn.length && $tableBtn.text().trim().toLowerCase() === 'remove') {
            $tableBtn.trigger('click');
        }
    });

    // Kebab dropdown
    $(document).on('click', '.ac-kebab-btn', function(e){ e.preventDefault(); e.stopPropagation(); var $m=$(this).siblings('.ac-kebab-menu'); $('.ac-kebab-menu').not($m).removeClass('show'); $m.toggleClass('show'); });
    $(document).on('click', '.ac-kebab-menu', function(e){ e.stopPropagation(); });
    $(document).on('click', function(){ $('.ac-kebab-menu').removeClass('show'); });

    // ---- Contact popup (Email / Call / WhatsApp) ----
    if(!document.getElementById('contactModalOverlay')){
        $('body').append(
            '<div class="cm-overlay" id="contactModalOverlay">'
          + '  <div class="cm-box">'
          + '    <div class="cm-icon"><i class="la" id="cmIcon"></i></div>'
          + '    <div class="cm-title" id="cmTitle"></div>'
          + '    <div class="cm-sub" id="cmSub"></div>'
          + '    <div class="cm-value" id="cmValue"></div>'
          + '    <div class="cm-actions">'
          + '      <button type="button" class="cm-btn cm-cancel" id="cmCancel">Cancel</button>'
          + '      <a class="cm-btn cm-go" id="cmGo" href="#"><i class="la" id="cmGoIcon"></i> <span id="cmGoText"></span></a>'
          + '    </div>'
          + '  </div>'
          + '</div>'
        );
    }
    function cmDigits(s){ return (s||'').toString().replace(/[^0-9]/g,''); }
    function cmDial(code){ try { if(typeof countryDialCodes !== 'undefined' && code && countryDialCodes[code]){ return countryDialCodes[code].toString(); } } catch(e){} return ''; }
    function cmPhoneDisplay(code, number){ number=(number||'').toString().trim(); if(!number) return ''; var d=cmDial(code); if(d){ if(d.charAt(0)!=='+') d='+'+d; return d+' '+number; } return number; }
    function cmPhoneE164(code, number){ return cmDigits(cmDial(code)) + cmDigits(number); }

    function openContactModal(opts){
        var type = opts.type, name = opts.name || 'this member';
        var $go = $('#cmGo'); $go.removeAttr('target');
        if(type === 'email'){
            var email = opts.email || '';
            $('#cmIcon').attr('class','la la-envelope'); $('#cmTitle').text('Send Email to '+name); $('#cmSub').text('Click below to open your email client');
            $('#cmValue').text(email || 'No email available'); $('#cmGoIcon').attr('class','la la-external-link-alt'); $('#cmGoText').text('Open Email');
            $go.attr('href', email ? ('mailto:'+email) : '#');
        } else if(type === 'call'){
            var disp = cmPhoneDisplay(opts.code, opts.phone), e164 = cmPhoneE164(opts.code, opts.phone);
            $('#cmIcon').attr('class','la la-phone'); $('#cmTitle').text('Call '+name); $('#cmSub').text('Click below to initiate the call');
            $('#cmValue').text(disp || 'No number available'); $('#cmGoIcon').attr('class','la la-phone'); $('#cmGoText').text('Call Now');
            $go.attr('href', e164 ? ('tel:+'+e164) : '#');
        } else if(type === 'whatsapp'){
            var wdisp = cmPhoneDisplay(opts.code, opts.phone), we164 = cmPhoneE164(opts.code, opts.phone);
            $('#cmIcon').attr('class','la la-whatsapp'); $('#cmTitle').text('Message '+name+' on WhatsApp'); $('#cmSub').text('Click below to open WhatsApp');
            $('#cmValue').text(wdisp || 'No number available'); $('#cmGoIcon').attr('class','la la-whatsapp'); $('#cmGoText').text('Open WhatsApp');
            $go.attr('href', we164 ? ('https://wa.me/'+we164) : '#').attr('target','_blank');
        }
        $('#contactModalOverlay').addClass('show');
    }

    $(document).on('click', '.contact-action', function(e){
        e.preventDefault(); e.stopPropagation();
        $('.ac-kebab-menu').removeClass('show');
        openContactModal({
            type:  $(this).data('type'),
            name:  $(this).data('name') ? String($(this).data('name')) : '',
            email: $(this).data('email') ? String($(this).data('email')) : '',
            phone: $(this).data('phone') !== undefined ? String($(this).data('phone')) : '',
            code:  $(this).data('code') ? String($(this).data('code')) : 'in'
        });
    });
    $(document).on('click', '#cmCancel', function(){ $('#contactModalOverlay').removeClass('show'); });
    $(document).on('click', '#contactModalOverlay', function(e){ if(e.target === this){ $(this).removeClass('show'); } });
    $(document).on('click', '#cmGo', function(){ var h=$(this).attr('href'); setTimeout(function(){ $('#contactModalOverlay').removeClass('show'); },100); if(!h || h==='#'){ return false; } });

    // Keep the table action label as "Assign" when the original handler resets it to "Add"
    $(document).on('click', '.cp_select', function(){ var $b=$(this); setTimeout(function(){ if($b.text().trim()==='Add'){ $b.text('Assign'); } },0); });
    $('#Iteam_Table111 .cp_select').each(function(){ if($(this).text().trim()==='Add'){ $(this).text('Assign'); } });
});
</script>
<!--end::New UI-->
