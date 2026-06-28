<style>
.form-check.form-check-custom .form-check-label{margin-left: 2.55rem;}

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

.hm-avatar { width: 40px; height: 40px; border-radius: 50%; flex-shrink: 0; color: #fff; display: flex; align-items: center; justify-content: center; font-size: 14px; font-weight: 600; }

/* Contact icons */
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

.badge.badge-light-primary { background-color: #f1faff; color: #009ef7; }

.position-header { border-bottom: 1px solid #eff2f5; }
.captain-box { background-color: #fff; border: 1px solid #eaecf0; border-radius: 10px; padding: 14px 18px; }

.section-title { font-weight: 600; color: #181c32; margin-bottom: 2px; font-size: 15px; }
.section-subtitle { color: #a1a5b7; display: block; font-size: 12px; }

.empty-state-container { display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 1.4rem 1.25rem; text-align: center; }
.empty-state-icon { width: 50px; height: 50px; border-radius: 50%; background-color: #f3f6f9; display: inline-flex; align-items: center; justify-content: center; margin-bottom: .65rem; color: #a1a5b7; font-size: 20px; }
.empty-state-icon.captain-icon { width: 48px; height: 48px; font-size: 20px; margin-bottom: 0; margin-right: .875rem; flex-shrink: 0; }
.empty-state-text .empty-title { font-size: 14px; font-weight: 600; color: #181c32; margin-bottom: .15rem; }
.empty-state-text .empty-subtitle { color: #a1a5b7; margin-bottom: .85rem; font-size: 12px; }

/* Drawer header (logo + intro text) */
.hm-drawer-header { display: flex; align-items: center; gap: 14px; padding: 6px 4px 2px; }
.hm-logo { width: 44px; height: 44px; border-radius: 11px; background: linear-gradient(135deg, #2f6bff, #1f9cf0); color: #fff; display: flex; align-items: center; justify-content: center; font-size: 21px; flex-shrink: 0; box-shadow: 0 6px 16px rgba(37,99,235,.25); }
.hm-head-title { font-size: 18px; font-weight: 700; color: #181c32; line-height: 1.2; }
.hm-head-sub { font-size: 12px; color: #a1a5b7; margin-top: 2px; }
.captain-empty-state { display: flex; align-items: center; background-color: #fff; border: 1px solid #eff2f5; border-radius: 10px; padding: 1rem 1.25rem; }

.info-banner { background-color: #eff6ff; color: #1e40af; border: 1px solid #dbeafe; border-radius: 8px; padding: 12px 16px; font-size: 12px; display: flex; align-items: center; gap: 10px; }
.info-banner i { font-size: 16px; flex-shrink: 0; color: #2563eb; }

.card-header-compact { padding: 1.25rem 1.5rem .5rem 1.5rem; border-bottom: 0; background: transparent; }
.card-body-compact { padding: 0 1.5rem 1.5rem 1.5rem; }

#Iteam_Table_hire { margin-bottom: 0; }
#Iteam_Table_hire thead th { background-color: #f3f6f9; font-weight: 600; text-transform: uppercase; letter-spacing: .4px; padding: 10px 12px; border-bottom: 1px solid #e4e6ef; color: #a1a5b7; font-size: 11px; white-space: nowrap; }
#Iteam_Table_hire tbody tr { border-bottom: 1px dashed #e4e6ef; }
#Iteam_Table_hire tbody tr:last-child { border-bottom: 0; }
#Iteam_Table_hire tbody td { padding: 12px; font-size: 13px; vertical-align: middle; }
.highlight { background-color: #fff8dd !important; transition: background-color .15s ease; }
.w-100px { width: 100px !important; text-align: center; justify-content: center; }

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

/* Royal-blue palette scoped to this drawer */
#kt_activities_body .btn-light.btn-active-light-primary, #kt_activities_body .btn-light-primary { border: 1px solid #d6e4ff !important; color: #2563eb !important; background-color: #fff !important; font-weight: 500; }
#kt_activities_body .btn-light.btn-active-light-primary:hover, #kt_activities_body .btn-light-primary:hover { background-color: #eef4ff !important; border-color: #2563eb !important; color: #1d4ed8 !important; }
#kt_activities_body .btn-light-danger { border: 1px solid #fbd5de !important; color: #ef4444 !important; background-color: #fff !important; font-weight: 500; }
#kt_activities_body .btn-light-danger:hover { background-color: #fff1f4 !important; border-color: #ef4444 !important; }
#kt_activities_body .btn-primary { background-color: #2563eb !important; border-color: #2563eb !important; }
#kt_activities_body .btn-primary:hover { background-color: #1d4ed8 !important; border-color: #1d4ed8 !important; }

/* Drawer fit */
#kt_activitiesClient.drawer, #kt_activitiesClient { width: min(1160px, 96vw) !important; max-width: 96vw !important; }
#kt_activities_body { max-width: 1180px; margin: 0 auto; }
@media only screen and (max-width: 1199.98px){ #kt_activitiesRecruiter, #kt_activitiesClient { width: min(960px, 96vw) !important; } }
@media (max-width: 767.98px){ .filter-bar { flex-direction: column; width: 100%; } .captain-empty-state { flex-direction: column; text-align: center; gap: .75rem; } .captain-empty-state .captain-icon { margin-right: 0; } }
@media (max-width: 575.98px){ .assigned-grid { grid-template-columns: 1fr; } .ac-actions { flex-wrap: wrap; } }
</style>

<div class="card-body position-relative px-0 py-0" id="kt_activities_body">
  <div id="kt_activities_scroll" class="position-relative scroll-y bg-light pt-2 h-100" data-kt-scroll="true" data-kt-scroll-height="auto" data-kt-scroll-wrappers="#kt_activities_body" data-kt-scroll-dependencies="#kt_activities_header, #kt_activities_footer" data-kt-scroll-offset="5px" style="">

    <!--begin::Drawer Header (logo + intro)-->
    {{-- <div class="hm-drawer-header mx-2 mb-3">
      <div class="hm-logo"><i class="la la-user-tie"></i></div>
      <div>
        <div class="hm-head-title">Select Hiring Manager</div>
        <div class="hm-head-sub">Choose the contact person / hiring manager who will own this position</div>
      </div>
    </div> --}}
    <!--end::Drawer Header-->

    <!--begin::Position Header-->
    {{-- NOTE: position/client labels are static placeholders to mirror the reference design.
         Wire to real job/client variables when available. --}}
    {{-- <div class="card card-custom mx-2 mb-3">
      <div class="card-body p-5 position-header">
        <div class="row align-items-start">
          <div class="col-lg-7">
            <div class="text-muted text-uppercase mb-1" style="letter-spacing:.5px;font-size:11px;font-weight:500;">Position</div>
            <div class="d-flex flex-wrap align-items-center gap-2 mb-1">
              <h2 class="fw-bolder text-dark mb-0" style="font-size:15px;">Senior Java Developer</h2>
              <span class="badge badge-light-primary" style="font-size:10px;padding:4px 10px;">DIRECT CLIENT</span>
            </div>
            <div class="d-flex align-items-center text-muted mb-1" style="font-size:12px;">
              <i class="la la-building me-2" style="font-size:14px;"></i><span>Acme Technologies Pvt. Ltd.</span>
            </div>
            <div class="d-flex align-items-center gap-2 text-muted" style="font-size:12px;">
              <span class="fw-semibold">JD-2438</span><span>•</span><span>Created on 12 May 2024</span>
            </div>
          </div>
          <div class="col-lg-5 mt-3 mt-lg-0">
            <div class="d-flex flex-column align-items-start align-items-lg-end">
              <div class="text-muted text-uppercase mb-2" style="letter-spacing:.5px;font-size:11px;font-weight:500;">
                Primary Contact <i class="la la-info-circle ms-1"></i>
              </div>
              <div class="w-100" id="primaryWrap"><!-- filled by JS --></div>
              <div class="text-muted mt-2 text-lg-end" style="font-size:11px;">
                The primary contact is the main hiring manager and receives all primary notifications.
              </div>
            </div>
          </div>
        </div>
      </div>
    </div> --}}
    <!--end::Position Header-->

    <!--begin::Selected Hiring Managers-->
    <div class="card card-custom mx-2 mb-3" id="selectedHMSection">
      <div class="card-header card-header-compact">
        <div class="card-title flex-column align-items-start m-0 mt-5">
          <h3 class="section-title mb-0">1. Selected Hiring Managers <span class="text-muted" id="selectedHMCount" style="font-weight:400;">(0)</span></h3>
          <span class="section-subtitle mt-1">These contacts are the hiring managers for this position</span>
        </div>
      </div>
      <div class="card-body card-body-compact">
        <div class="assigned-grid" id="selectedHMGrid" style="display:none;"></div>
        <div id="hmEmptyState">
          <div class="empty-state-container">
            <div class="empty-state-icon"><i class="la la-user-tie"></i></div>
            <div class="empty-state-text">
              <div class="empty-title">No hiring manager selected</div>
              <div class="empty-subtitle">Select a contact person below to set the hiring manager for this position.</div>
            </div>
            <button type="button" class="btn btn-sm btn-light btn-active-light-primary" id="scrollToTableBtn"><i class="la la-plus me-1"></i>Select Contact</button>
          </div>
        </div>
      </div>
    </div>
    <!--end::Selected Hiring Managers-->

    <!--begin::Info Alert-->
    <div class="mx-2 mb-3">
      <div class="info-banner">
        <i class="la la-info-circle"></i>
        <span>The first selected hiring manager is the primary point of contact for this position.</span>
      </div>
    </div>
    <!--end::Info Alert-->

    <!--begin::Add Hiring Managers-->
    <div class="card card-custom mx-2 mb-3" id="addMoreHMSection">
      <div class="card-header card-header-compact d-flex justify-content-between align-items-start flex-wrap gap-2">
        <div class="card-title flex-column align-items-start m-0">
          <h3 class="section-title mb-0">2. Add Hiring Managers</h3>
          <span class="section-subtitle mt-1">Search and select contact persons from the client</span>
        </div>
        @if(@$client_types->self_company != 1)
        <a href="javascript:void(0);" class="btn btn-primary btn-sm align-self-center" style="font-size:12px;padding:8px 14px;" data-toggle="modal" data-target="#add_team_member">
          <i class="la la-plus me-1"></i>Add Contact Person
        </a>
        @endif
      </div>
      <div class="card-body card-body-compact">

        <!--begin::Filter-->
        <div class="d-flex flex-wrap gap-3 mb-4 align-items-center filter-bar">
          <div class="search-container input-group input-group-lg input-group-solid w-100">
            <div class="input-group-prepend"><span class="input-group-text"><i class="la la-search"></i></span></div>
            <input class="form-control form-control-lg form-control-solid" type="text" id="searchInputContactHire" placeholder="Search for contacts person/hiring manager....">
          </div>
        </div>
        <!--end::Filter-->

        <!--begin::Table-->
        <div class="table-responsive">
          <table class="table align-middle table-row-dashed fs-6 gy-5 mb-0" id="Iteam_Table_hire">
            <thead>
              <tr class="fw-bolder text-muted bg-light">
                <th class="min-w-200px">Name</th>
                <th class="min-w-200px">Contact</th>
                <th class="min-w-100px">Status</th>
                <th class="min-w-120px text-end">Action</th>
              </tr>
            </thead>
            <tbody class="text-gray-600 fw-bold" id="Iteam_Table">
<?php
        foreach($assigned as $assignVal)
        {
                    $bgTextDecore = $strVal = '';
                    $str = trim(@$assignVal->contact_person_name);
                    $str = $strVal = strtolower($str[0]);
                    $str = ord($str);
                    if ($str >= 97 && $str <= 101) { $bgTextDecore = 'fs-3 bg-light-danger text-danger'; }
                    else if ($str >= 102 && $str <= 106) { $bgTextDecore = 'fs-3 bg-light-warning text-warning'; }
                    else if ($str >= 107 && $str <= 111) { $bgTextDecore = 'fs-3 bg-light-primary text-primary'; }
                    else if ($str >= 112 && $str <= 116) { $bgTextDecore = 'fs-3 bg-light-success text-success'; }
                    else if ($str >= 117 && $str <= 121) { $bgTextDecore = 'fs-3 bg-dark text-secondary'; }
                    else { $bgTextDecore = 'fs-3 bg-light-secondary text-dark'; }
?>
              <tr>
                <td>
                  <div class="d-flex align-items-center">
                    <div class="symbol symbol-circle symbol-40px overflow-hidden me-3 border-radius-5">
                      <a href="javascript:void(0);" class="">
                        <div class="symbol-label <?php echo @$bgTextDecore; ?> border-radius-5"><?php echo strtoupper(@$strVal); ?></div>
                      </a>
                    </div>
                    <div class="d-flex flex-column">
                      <span class="text-dark fw-bolder fs-6"><?php echo ucwords(substr(@$assignVal->contact_person_name, 0, 15)); ?></span>
                      <span class="text-muted fw-bold d-block fs-7"><?php echo @$assignVal->contact_person_designation; ?></span>
                    </div>
                  </div>
                </td>
                <td>
                  <?php
                    $cName  = htmlspecialchars((string) @$assignVal->contact_person_name, ENT_QUOTES);
                    $cEmail = htmlspecialchars((string) @$assignVal->contact_person_email, ENT_QUOTES);
                    $cPhone = htmlspecialchars((string) @$assignVal->contact_person_number, ENT_QUOTES);
                    $cCode  = htmlspecialchars((string) (@$assignVal->calling_code ?? 'in'), ENT_QUOTES);
                  ?>
                  <div class="d-flex align-items-center gap-1">
                    <button type="button" class="btn-icon-circle contact-action" data-type="email" data-name="<?php echo $cName; ?>" data-email="<?php echo $cEmail; ?>" title="Email"><i class="la la-envelope"></i></button>
                    <button type="button" class="btn-icon-circle contact-action" data-type="call" data-name="<?php echo $cName; ?>" data-phone="<?php echo $cPhone; ?>" data-code="<?php echo $cCode; ?>" title="Call"><i class="la la-phone"></i></button>
                    <button type="button" class="btn-icon-circle contact-action" data-type="whatsapp" data-name="<?php echo $cName; ?>" data-phone="<?php echo $cPhone; ?>" data-code="<?php echo $cCode; ?>" title="WhatsApp"><i class="la la-whatsapp"></i></button>
                  </div>
                </td>
                <td><span class="status-dot active">Active</span></td>
                <td class="text-end">
                  {{-- Original Add/Remove button — handler, id and data attributes preserved exactly --}}
                  <a id="hiring_manager_select_<?php echo @$assignVal->id ?>" href="javascript:void(0);" class="cp_select btn btn-sm btn-light-primary w-100px"
                     rel="<?php echo @$assignVal->id ?>" data-assign="0" data-title="<?php echo @$assignVal->contact_person_name; ?>" data-email="<?php echo @$assignVal->contact_person_email; ?>">Add</a>
                </td>
              </tr>
<?php }?>
            </tbody>
          </table>
        </div>
        <!--end::Table-->

      </div>
    </div>
    <!--end::Add Hiring Managers-->

  </div>
</div>
<!--end::Body-->
<script>
		function phoneDisplay() {
			$('.iti__flag-container').each(function () {

				var cnt = $(this).attr('data-row');
				var calling_code = $(this).attr('data-code');

				const flagContainer = document.getElementById(`phoneDisplay_${cnt}`);

				if (!flagContainer) return;

				// Remove old content first
				flagContainer.innerHTML = '';

				const flagDiv = document.createElement("div");
				flagDiv.className = `iti__flag iti__${calling_code.toLowerCase()}`;

				const dialCode = countryDialCodes[calling_code] || "";

				const dialCodeSpan = document.createElement("span");
				// dialCodeSpan.textContent = " +" + dialCode;
				dialCodeSpan.textContent = dialCode.startsWith('+') ? dialCode : '+' + dialCode;

				flagContainer.appendChild(flagDiv);
				flagContainer.appendChild(dialCodeSpan);
			});
		}

    setTimeout(() => {
        phoneDisplay()
    }, 2500);

$(document).ready(function() {
    // Cache the table and rows for better performance
    const $table = $('#Iteam_Table_hire');
    const $rows = $table.find('tbody tr');
    // Search function
    $('#searchInputContactHire').on('keyup', function() {
        const value = $(this).val().toLowerCase();
        let hasResults = false;

        // Remove previous highlights
        $rows.find('td').removeClass('highlight');

        $rows.each(function() {
            const $row = $(this);
            let rowText = '';

            // Concatenate all cell text in the row
            $row.find('td').each(function() {
                rowText += $(this).text().toLowerCase() + ' ';
            });

            // Show/hide row based on search term
            if (rowText.indexOf(value) > -1) {
                $row.show();
                hasResults = true;

                // Highlight matching text if search term isn't empty
                if (value.trim() !== '') {
                    $row.find('td').each(function() {
                        const cellText = $(this).text().toLowerCase();
                        if (cellText.indexOf(value) > -1) {
                            $(this).addClass('highlight');
                        }
                    });
                }
            } else {
                $row.hide();
            }
        });

    });

    // Clear search when clicking the input (optional)
    $('#searchInputContactHire').on('click', function() {
        if ($(this).val() === '') {
            $rows.show();
            $rows.find('td').removeClass('highlight');
        }
    });

});


$( document ).ready(function() {

	var assigned 			= <?php echo json_encode($assigned); ?>;
	var jqueryarray 		= <?php echo json_encode($hiring_namagerArr); ?>;
	for (var i = 0; i < assigned.length; i++) {
			var pid		=	assigned[i].id;
			var pname	=	assigned[i].contact_person_name.trim();
			for (var j = 0; j < jqueryarray.length; j++) {
        		var cname		=jqueryarray[j].trim();
					if(pname == cname){

								$('#hiring_manager_select_'+pid).addClass("btn-light-danger");
								$('#hiring_manager_select_'+pid).removeClass("btn-light-primary");
								$('#hiring_manager_select_'+pid).text('Remove');
								$('#hiring_manager_select_'+pid).attr("data-assign", 1);

					}
        	};
     };

});
</script>
<script>

$( document ).ready(function() {

	 $('.cp_select').on('click', function(e) {

			var rel						= 	$(this).attr('rel');
			var js_array 				=	<?php echo json_encode($assigned); ?>;
			var assign					=	$('#hiring_manager_select_'+rel).attr("data-assign");
			var title					=	$('#hiring_manager_select_'+rel).attr("data-title");

			if(assign ==0){
				$(this).addClass("btn-light-danger");
				$(this).removeClass("btn-light-primary");
				$(this).text('Remove');
				$(this).attr("data-assign", 1);
				addHiringManager(title);
			}else if(assign ==1){
				$(this).addClass("btn-light-primary");
				$(this).removeClass("btn-light-danger");
				$(this).text('Add');
				$(this).attr("data-assign", 0);
				DeleteEvent(title);
			}
	});

});

</script>




<script>

var js_array 				= <?php echo json_encode($assigned); ?>;

$( document ).ready(function() {

   $('.chk_child_assign').on('click', function() {

		var cp_id					=	$(this).val();
		if($(this).is(':checked')){

			var js_array 				= <?php echo json_encode($assigned); ?>;
			var select_contact_person	=	js_array[cp_id];

			for(var i=0; i<js_array.length; i++){

				if(js_array[i].id == cp_id){
					var strVal			=	'';		var bgTextDecore	=	'';		var str				=	'';

					str					=	js_array[i].contact_person_name;
					str					=	str.substring(0, 1);
					str	= strVal		=	str.toLowerCase();
					var str_bg			=	str.charCodeAt(0);

					if(str_bg >=97 && str_bg <=101){
						bgTextDecore		=	'fs-3 bg-light-danger text-danger';
					}else if(str_bg >=102 && str_bg <=106){
						bgTextDecore		=	'fs-3 bg-light-warning text-warning';
					}else if(str_bg >=107 && str_bg <=111){
						bgTextDecore		=	'fs-3 bg-light-primary text-primary';
					}else if(str_bg >=112 && str_bg <=116){
						bgTextDecore		=	'fs-3 bg-light-success text-success';
					}else if(str_bg >=117 && str_bg <=121){
						bgTextDecore		=	'fs-3 bg-dark text-secondary';
					}else {
						bgTextDecore		=	'fs-3 bg-light-secondary text-dark';
					}

					var html	='<tr class="" data-group-name="'+js_array[i].contact_person_designation+'" id="assigned_row_'+cp_id+'"><td class=""><div class="form-check form-check-sm form-check-custom form-check-solid"><input class="form-check-input form-check-input-team1" type="checkbox" value="1"></div></td><td class="d-flex align-items-center border-0"><div class="symbol symbol-circle symbol-40px overflow-hidden me-2  border-radius-5"><a href="javascript:void(0);"><div class="symbol symbol-circle symbol-40px overflow-hidden me-2 border-radius-5" style="float:left"> <a href="javascript:void(0);" data-row-id="146" class="show-detail"><div class="symbol-label fs-3 '+bgTextDecore+' border-radius-5">'+strVal.toUpperCase()+'</div></a> </div></a> </div><div class="d-flex flex-column"> <span class="text-dark fw-bolder fs-6">'+js_array[i].contact_person_name+'</span> <span class="fw-bold text-muted d-block fs-7">'+js_array[i].contact_person_email+'</span> </div></td><td class=""><div class="d-flex flex-column"> <span class="text-dark fw-bolder fs-6">'+js_array[i].contact_person_designation+'</span> </div></td><td class=""><div class="d-flex flex-column"> <span class="text-dark fw-bolder fs-6">187</span> </div></td><td class=""><div class="d-flex flex-column"> <span class="text-dark fw-bolder fs-6">12</span> </div></td><td class=""><div class="d-flex flex-column"> <span class="text-dark fw-bolder fs-6">45</span> </div></td><td><div class=""> <a href="javascript:void(0);" class="btn btn-sm btn-light-danger remove_assign_data" rel="'+cp_id+'" onclick="DeleteEvent('+cp_id+')">Remove</a> </div></td></tr>';

					$("#assign_append_table tbody").last().append(html);
					addHiringManager(js_array[i].contact_person_name);

				}

			}


		}
		else{
				$('#assigned_row_'+cp_id).remove();
				$('#'+cp_id).prop('checked', false); // Unchecks it
		}


	});

	$('.parentname').on('click', function(){

			var js_array 		=	<?php echo json_encode($assigned); ?>;
			var parentname		=	"";
			parentname			=	$(this).attr('data-parent-title');

			if($(this).is(':checked')){
				for(var i=0; i<js_array.length; i++){

						if(parentname.trim() == js_array[i].contact_person_designation.trim()){

									var cp_id			=	js_array[i].id;
									var strVal			=	'';		var bgTextDecore	=	'';		var str				=	'';

									str					=	js_array[i].contact_person_name;
									str					=	str.substring(0, 1);
									str	= strVal		=	str.toLowerCase();
									var str_bg			=	str.charCodeAt(0);

									if(str_bg >=97 && str_bg <=101){
										bgTextDecore		=	'fs-3 bg-light-danger text-danger';
									}else if(str_bg >=102 && str_bg <=106){
										bgTextDecore		=	'fs-3 bg-light-warning text-warning';
									}else if(str_bg >=107 && str_bg <=111){
										bgTextDecore		=	'fs-3 bg-light-primary text-primary';
									}else if(str_bg >=112 && str_bg <=116){
										bgTextDecore		=	'fs-3 bg-light-success text-success';
									}else if(str_bg >=117 && str_bg <=121){
										bgTextDecore		=	'fs-3 bg-dark text-secondary';
									}else {
										bgTextDecore		=	'fs-3 bg-light-secondary text-dark';
									}

									var assign_row_id			=	"assigned_row_"+cp_id;

									if(document.getElementById(assign_row_id) == null) {
										var html					='<tr class="" data-group-name="'+js_array[i].contact_person_designation+'" id="assigned_row_'+cp_id+'"><td class=""><div class="form-check form-check-sm form-check-custom form-check-solid"><input class="form-check-input form-check-input-team1" type="checkbox" value="1"></div></td><td class="d-flex align-items-center border-0"><div class="symbol symbol-circle symbol-40px overflow-hidden me-2  border-radius-5"><a href="javascript:void(0);"><div class="symbol symbol-circle symbol-40px overflow-hidden me-2 border-radius-5" style="float:left"> <a href="javascript:void(0);" data-row-id="146" class="show-detail"><div class="symbol-label fs-3 '+bgTextDecore+' border-radius-5">'+strVal.toUpperCase()+'</div></a> </div></a> </div><div class="d-flex flex-column"> <span class="text-dark fw-bolder fs-6">'+js_array[i].contact_person_name+'</span> <span class="fw-bold text-muted d-block fs-7">'+js_array[i].contact_person_email+'</span> </div></td><td class=""><div class="d-flex flex-column"> <span class="text-dark fw-bolder fs-6">'+js_array[i].contact_person_designation+'</span> </div></td><td class=""><div class="d-flex flex-column"> <span class="text-dark fw-bolder fs-6">187</span> </div></td><td class=""><div class="d-flex flex-column"> <span class="text-dark fw-bolder fs-6">12</span> </div></td><td class=""><div class="d-flex flex-column"> <span class="text-dark fw-bolder fs-6">45</span> </div></td><td><div class=""><a href="javascript:void(0);" class="btn btn-sm btn-light-danger remove_assign_data" rel="'+cp_id+'" onclick="DeleteEvent('+cp_id+')">Remove</a> </div></td></tr>';

										$("#assign_append_table tbody").last().append(html);
									}

				addHiringManager(js_array[i].contact_person_name);



				}
			}
			}
			else
			{
				for(var i=0; i<js_array.length; i++)
				{
					if(parentname == js_array[i].contact_person_designation)
					{
						$('#assigned_row_'+js_array[i].id).remove();
					}
				}
			}

	});


});

function DeleteEvent(contact_person_name){

	var tem_hiring_namager	=	'';
	var temp				=	[];
	var hiring_namager		=	$('#hiring_namager').val();
	if(hiring_namager !=''){
		var hiring_namagerArr = hiring_namager.split(',');
		for(var cnt=0; cnt<hiring_namagerArr.length; cnt++){
			if(contact_person_name.trim() != hiring_namagerArr[cnt].trim()){
				temp.push(hiring_namagerArr[cnt]);
			}
		}
		tem_hiring_namager	=	temp.toString();
	}
	$('#hiring_namager').val(tem_hiring_namager);
}

function addHiringManager(name){

		var currentValue		=	$('#hiring_namager').val();
		if(currentValue !=''){
			var includeStr			=	currentValue.includes(name);
			if(includeStr  === false){
				var assignNewValue	=	currentValue +' , '+name;
				$('#hiring_namager').val(assignNewValue);
			}
		}else{
			$('#hiring_namager').val(name);
		}
}

</script>

<!--begin::New UI: Selected Hiring Manager cards (synced to #hiring_namager) — additive, no logic change-->
<script>
var __hmContacts = <?php echo json_encode(array_map(function($a){ return ['id'=>$a->id,'name'=>$a->contact_person_name,'designation'=>$a->contact_person_designation,'email'=>$a->contact_person_email,'number'=>@$a->contact_person_number,'code'=>(@$a->calling_code ?: 'in')]; }, is_array($assigned) ? $assigned : $assigned->all())); ?>;
var __lastHMVal = null;

function hmColor(ch){ var c=(ch||'a').toLowerCase().charCodeAt(0); if(c>=97&&c<=101)return '#ef4444'; if(c>=102&&c<=106)return '#f59e0b'; if(c>=107&&c<=111)return '#2563eb'; if(c>=112&&c<=116)return '#10b981'; if(c>=117&&c<=121)return '#334155'; return '#64748b'; }
function hmInitials(n){ var p=n.trim().split(/\s+/); var s=(p[0]?p[0].charAt(0):'')+(p[1]?p[1].charAt(0):''); return (s||n.charAt(0)).toUpperCase(); }
function hmEsc(s){ return (s||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;'); }
function hmTitle(s){ return (s||'').replace(/\b\w/g,function(c){return c.toUpperCase();}); }
function hmFind(name){ for(var i=0;i<__hmContacts.length;i++){ if((__hmContacts[i].name||'').trim().toLowerCase()===name.trim().toLowerCase()) return __hmContacts[i]; } return null; }

function hmCardHtml(name, isPrimary){
    var c = hmFind(name) || {};
    var role = c.designation ? c.designation : 'Hiring Manager';
    var email = c.email || '';
    var phone = c.number || '';
    var code = c.code || 'in';
    var id = (c.id !== undefined) ? c.id : '';
    var pill = isPrimary
        ? '<span class="pill pill-captain"><i class="la la-star me-1"></i>Primary</span>'
        : '<span class="pill pill-assigned"><i class="la la-check-circle me-1"></i>Selected</span>';
    var firstBtn = isPrimary
        ? '<button type="button" class="btn btn-sm btn-light btn-active-light-primary hm-change-primary">Change Primary</button>'
        : '<button type="button" class="btn btn-sm btn-light btn-active-light-primary hm-make-primary" data-hmname="'+hmEsc(name)+'">Make Primary</button>';
    return '<div class="assigned-card h-100">'
        + '<div class="d-flex align-items-start justify-content-between mb-3">'
        +   '<div class="d-flex align-items-center" style="min-width:0;">'
        +     '<div class="hm-avatar me-3" style="background:'+hmColor(name.charAt(0))+'">'+hmEsc(hmInitials(name))+'</div>'
        +     '<div style="min-width:0;">'
        +       '<div class="fw-bolder text-dark text-truncate" style="font-size:13px;">'+hmEsc(hmTitle(name).substring(0,22))+'</div>'
        +       '<div class="text-muted text-truncate" style="font-size:11px;">'+hmEsc(hmTitle(role).substring(0,22))+'</div>'
        +     '</div>'
        +   '</div>'
        +   '<div class="d-flex align-items-center gap-2 flex-shrink-0">'+pill
        +     '<div class="ac-kebab-wrap"><button type="button" class="ac-kebab-btn" title="More"><i class="la la-ellipsis-v"></i></button>'
        +       '<div class="ac-kebab-menu">'
        +         '<a href="javascript:void(0);" class="contact-action" data-type="email" data-name="'+hmEsc(name)+'" data-email="'+hmEsc(email)+'"><i class="la la-envelope"></i> Email</a>'
        +         '<a href="javascript:void(0);" class="contact-action" data-type="call" data-name="'+hmEsc(name)+'" data-phone="'+hmEsc(phone)+'" data-code="'+hmEsc(code)+'"><i class="la la-phone"></i> Call</a>'
        +         '<a href="javascript:void(0);" class="contact-action" data-type="whatsapp" data-name="'+hmEsc(name)+'" data-phone="'+hmEsc(phone)+'" data-code="'+hmEsc(code)+'"><i class="la la-whatsapp"></i> WhatsApp</a>'
        +       '</div>'
        +     '</div>'
        +   '</div>'
        + '</div>'
        + '<div class="d-flex gap-2 ac-actions">'+firstBtn
        +   '<button type="button" class="btn btn-sm btn-light-danger hm-remove" data-hmid="'+id+'" data-hmname="'+hmEsc(name)+'">Remove</button>'
        + '</div>'
        + '</div>';
}

function hmPrimaryBoxHtml(name){
    if(!name){
        return '<div class="captain-empty-state w-100">'
            + '<div class="empty-state-icon captain-icon"><i class="la la-star"></i></div>'
            + '<div class="flex-grow-1 text-start"><div class="empty-state-text"><div class="empty-title">No primary contact</div><div class="empty-subtitle">Select a hiring manager to set the primary point of contact.</div></div></div>'
            + '</div>';
    }
    var c = hmFind(name) || {};
    var role = c.designation ? c.designation : 'Hiring Manager';
    return '<div class="captain-box w-100 d-flex align-items-center">'
        + '<div class="hm-avatar me-3" style="background:'+hmColor(name.charAt(0))+'">'+hmEsc(hmInitials(name))+'</div>'
        + '<div class="flex-grow-1" style="min-width:0;"><div class="fw-bolder text-dark text-truncate" style="font-size:13px;">'+hmEsc(hmTitle(name).substring(0,22))+'</div><div class="text-muted text-truncate" style="font-size:11px;">'+hmEsc(hmTitle(role).substring(0,22))+'</div></div>'
        + '<div class="d-flex align-items-center gap-2 ms-2"><span class="pill pill-captain"><i class="la la-star me-1"></i>Primary</span></div>'
        + '</div>';
}

function hmNames(){ var v=$('#hiring_namager').val()||''; return v.split(',').map(function(s){return s.trim();}).filter(function(s){return s.length;}); }

function renderSelectedHM(){
    var v = $('#hiring_namager').val()||'';
    __lastHMVal = v;
    var names = hmNames();
    if(names.length === 0){
        $('#selectedHMGrid').hide().empty();
        $('#hmEmptyState').show();
    } else {
        var html='';
        names.forEach(function(nm, i){ html += hmCardHtml(nm, i===0); });
        $('#selectedHMGrid').html(html).show();
        $('#hmEmptyState').hide();
    }
    $('#selectedHMCount').text('('+names.length+')');
    $('#primaryWrap').html(hmPrimaryBoxHtml(names[0] || ''));
}

$(document).ready(function(){
    renderSelectedHM();
    // #hiring_namager is updated client-side by addHiringManager/DeleteEvent (no change event) — poll to stay in sync
    setInterval(function(){ if(($('#hiring_namager').val()||'') !== __lastHMVal){ renderSelectedHM(); } }, 400);

    // Scroll from empty state to the table
    $(document).on('click', '#scrollToTableBtn', function(e){ e.preventDefault(); $('html,body').animate({scrollTop:$('#addMoreHMSection').offset().top-20},400); setTimeout(function(){ $('#searchInputContactHire').focus(); },450); });

    // Card Remove → run the real table toggle (fires .cp_select → DeleteEvent + updates #hiring_namager)
    $(document).on('click', '.hm-remove', function(e){
        e.preventDefault();
        var id = $(this).data('hmid');
        var name = $(this).data('hmname');
        var $tableBtn = id !== '' ? $('#hiring_manager_select_'+id) : $();
        if($tableBtn.length && String($tableBtn.attr('data-assign')) === '1'){
            $tableBtn.trigger('click');
        } else {
            // Fallback: contact not in current table (e.g. just-added) — remove the name directly
            if(typeof DeleteEvent === 'function'){ DeleteEvent(String(name)); }
        }
        renderSelectedHM();
    });

    // Make Primary → move this name to the front of #hiring_namager (first = primary)
    $(document).on('click', '.hm-make-primary', function(e){
        e.preventDefault();
        var name = String($(this).data('hmname')).trim();
        var arr = hmNames().filter(function(n){ return n.toLowerCase() !== name.toLowerCase(); });
        arr.unshift(name);
        $('#hiring_namager').val(arr.join(' , '));
        renderSelectedHM();
    });

    // Change Primary helper
    $(document).on('click', '.hm-change-primary', function(e){
        e.preventDefault();
        if(typeof Swal !== 'undefined'){
            Swal.fire({ icon:'info', title:'Primary Contact', text:'To change the primary contact, choose "Make Primary" on another selected hiring manager.', confirmButtonText:'OK', customClass:{ confirmButton:'btn btn-primary btn-sm' } });
        }
        if($('#selectedHMSection').length){ $('html,body').animate({scrollTop:$('#selectedHMSection').offset().top-20},400); }
    });

    // Kebab dropdown (contact actions)
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
    function cmPhoneDisplay(code, number){
        number = (number||'').toString().trim();
        if(!number) return '';
        var d = cmDial(code);
        if(d){ if(d.charAt(0) !== '+') d = '+' + d; return d + ' ' + number; }
        return number;
    }
    function cmPhoneE164(code, number){
        var d = cmDigits(cmDial(code));
        return d + cmDigits(number);
    }

    function openContactModal(opts){
        var type = opts.type, name = opts.name || 'this contact';
        var $go = $('#cmGo');
        $go.removeAttr('target');
        if(type === 'email'){
            var email = opts.email || '';
            $('#cmIcon').attr('class', 'la la-envelope');
            $('#cmTitle').text('Send Email to ' + name);
            $('#cmSub').text('Click below to open your email client');
            $('#cmValue').text(email || 'No email available');
            $('#cmGoIcon').attr('class', 'la la-external-link-alt');
            $('#cmGoText').text('Open Email');
            $go.attr('href', email ? ('mailto:' + email) : '#');
        } else if(type === 'call'){
            var disp = cmPhoneDisplay(opts.code, opts.phone);
            var e164 = cmPhoneE164(opts.code, opts.phone);
            $('#cmIcon').attr('class', 'la la-phone');
            $('#cmTitle').text('Call ' + name);
            $('#cmSub').text('Click below to initiate the call');
            $('#cmValue').text(disp || 'No number available');
            $('#cmGoIcon').attr('class', 'la la-phone');
            $('#cmGoText').text('Call Now');
            $go.attr('href', e164 ? ('tel:+' + e164) : '#');
        } else if(type === 'whatsapp'){
            var wdisp = cmPhoneDisplay(opts.code, opts.phone);
            var we164 = cmPhoneE164(opts.code, opts.phone);
            $('#cmIcon').attr('class', 'la la-whatsapp');
            $('#cmTitle').text('Message ' + name + ' on WhatsApp');
            $('#cmSub').text('Click below to open WhatsApp');
            $('#cmValue').text(wdisp || 'No number available');
            $('#cmGoIcon').attr('class', 'la la-whatsapp');
            $('#cmGoText').text('Open WhatsApp');
            $go.attr('href', we164 ? ('https://wa.me/' + we164) : '#').attr('target', '_blank');
        }
        $('#contactModalOverlay').addClass('show');
    }

    // Open popup from any contact icon (table rows + card kebab)
    $(document).on('click', '.contact-action', function(e){
        e.preventDefault();
        e.stopPropagation();
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
    // Close after launching the action
    $(document).on('click', '#cmGo', function(){ var h = $(this).attr('href'); setTimeout(function(){ $('#contactModalOverlay').removeClass('show'); }, 100); if(!h || h === '#'){ return false; } });

    // Keep the table action label as "Assign" when the original handler resets it to "Add"
    $(document).on('click', '.cp_select', function(){ var $b=$(this); setTimeout(function(){ if($b.text().trim()==='Add'){ $b.text('Assign'); } },0); });
    // Initial label polish: show "Assign" instead of "Add"
    $('.cp_select').each(function(){ if($(this).text().trim()==='Add'){ $(this).text('Assign'); } });
});
</script>
