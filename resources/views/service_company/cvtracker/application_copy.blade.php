@php
		use App\Helper\GeneralMethod;
		use Illuminate\Support\Facades\DB;
@endphp
<style>
/* ── Copy-job drawer: Jobs-page grid layout ── */
.cj-table-card {
    background: #fff;
    border-radius: 12px;
    border: 1px solid #e4e6ef;
    overflow: hidden;
}
.cj-table-header,
.cj-job-row {
    display: grid;
    grid-template-columns: 40px 2.6fr 1fr 1.05fr 1.1fr 0.9fr 100px;
    column-gap: 10px;
    align-items: start;
}
.cj-table-header {
    padding: 9px 12px;
    background: #f7f8fc;
    border-bottom: 1.5px solid #e4e6ef;
    align-items: center;
}
.cj-th {
    font-size: 10.5px;
    font-weight: 600;
    color: #6d7284;
    text-transform: uppercase;
    letter-spacing: 0.4px;
}
.cj-job-row {
    padding: 12px 12px;
    border-bottom: 1px solid #e4e6ef;
    transition: background 0.12s;
    position: relative;
    min-height: 90px;
}
.cj-job-row:last-of-type { border-bottom: none; }
.cj-job-row:hover { background: #fafbff; }

/* Col 1: checkbox */
.cj-col-chk { display: flex; align-items: flex-start; padding-top: 2px; }

/* Col 2: Job Details */
.col-job { display: flex; gap: 10px; flex-direction: column; }
.cj-job-top { display: flex; gap: 10px; align-items: flex-start; }
.job-logo {
    width: 48px; height: 48px; border-radius: 10px; flex-shrink: 0;
    display: flex; align-items: center; justify-content: center;
    font-size: 18px; font-weight: 800; overflow: hidden; align-self: flex-start;
}
.job-info { flex: 1; min-width: 0; }
.job-title { font-size: 12.5px; font-weight: 700; color: #181c32; margin-bottom: 1px; line-height: 1.3; text-decoration: none; }
.job-title:hover { color: #3b55e6; }
.job-dept { font-size: 11px; color: #7c3aed; margin-bottom: 1px; font-weight: 500; }
.job-client { font-size: 11px; color: #6d7284; font-weight: 500; margin-bottom: 0; }
.job-meta-line {
    display: flex; align-items: center; flex-wrap: nowrap;
    gap: 5px; font-size: 10.5px; color: #a1a5b7;
    white-space: nowrap; overflow: hidden;
}
.skills-inline { font-size: 10.5px; color: #a1a5b7; }
.skills-inline b { font-weight: 600; color: #6d7284; }

/* Col 3: Experience */
.col-exp { display: flex; flex-direction: column; gap: 5px; }
.exp-row {
    display: flex; align-items: center; gap: 5px;
    font-size: 11.5px; font-weight: 500; color: #181c32;
}
.exp-row svg, .sal-row svg { width: 13px; height: 13px; color: #a1a5b7; flex-shrink: 0; }
.sal-row {
    display: flex; align-items: center; gap: 5px;
    font-size: 11.5px; font-weight: 500; color: #181c32;
}
.badge-type {
    display: inline-flex; align-items: center; gap: 3px;
    padding: 2px 9px; border-radius: 20px; font-size: 10px;
    font-weight: 600; width: fit-content; margin-top: 2px;
}
.badge-internal { background: #eef2ff; color: #3b55e6; }
.badge-client   { background: #eefff6; color: #09b760; }

/* Col 4: Hiring & Location */
.col-hl { display: flex; flex-direction: column; gap: 5px; }
.badge-hiring {
    padding: 2px 9px; border-radius: 20px; font-size: 10.5px;
    font-weight: 600; display: inline-flex; align-items: center;
    gap: 5px; width: fit-content;
}
.badge-hiring::before {
    content: ""; width: 6px; height: 6px;
    background: #c9c4c4; border-radius: 50%;
}
.loc-row {
    display: flex; align-items: center; gap: 4px;
    font-size: 11.5px; color: #6d7284; font-weight: 500;
}
.loc-row svg { width: 12px; height: 12px; color: #a1a5b7; }
.mode-row {
    display: flex; align-items: center; gap: 4px;
    font-size: 11.5px; color: #6d7284; font-weight: 500;
}
.mode-row svg { width: 12px; height: 12px; color: #a1a5b7; }

/* Col 5: Hiring Manager */
.col-hm { font-size: 11.5px; }
.hm-name  { font-size: 12px; font-weight: 600; color: #181c32; margin-bottom: 2px; }
.hm-title { font-size: 11px; color: #6d7284; margin-bottom: 1px; }
.hm-company { font-size: 11px; color: #a1a5b7; }

/* Col 6: Team */
.col-action { display: flex; flex-direction: column; gap: 5px; }
.action-count { font-size: 11px; color: #6d7284; }
.action-more  { font-size: 10.5px; color: #3b55e6; cursor: pointer; font-weight: 500; }

/* Col 7: Action */
.col-single-action { display: flex; align-items: flex-start; padding-top: 2px; }
.btn-add-single {
    display: inline-flex; align-items: center; gap: 5px;
    padding: 6px 12px; border-radius: 6px;
    background: #3b55e6; color: #fff;
    font-size: 11px; font-weight: 600;
    border: none; cursor: pointer; white-space: nowrap;
    transition: background 0.15s;
    font-family: "Poppins", sans-serif;
}
.btn-add-single:hover { background: #2a42c8; color: #fff; }
.btn-already-added {
    background: #dcfce7 !important; color: #15803d !important;
    border: 1.5px solid #bbf7d0 !important; cursor: default !important;
    font-size: 11px; font-weight: 600;
}

@media (max-width: 1366px) {
    .cj-table-header,
    .cj-job-row {
        grid-template-columns: 36px 2.3fr 0.95fr 0.95fr 1fr 0.85fr 90px;
        column-gap: 8px;
    }
}
</style>
<div class="card-body position-relative px-0 py-0" id="kt_activities_body">
  <input type="hidden" name="hid_job_id" id="hid_job_id" value="" />
  <div id="kt_activities_scroll" class="position-relative scroll-y bg-light pt-2" data-kt-scroll="true" data-kt-scroll-height="auto" data-kt-scroll-wrappers="#kt_activities_body" data-kt-scroll-dependencies="#kt_activities_header, #kt_activities_footer" data-kt-scroll-offset="5px">

<div class="post d-flex flex-column-fluid" id="kt_post">
  <div id="kt_content_container" class="container-fluid px-3">
    <div class="card">
      <div class="card-body pt-3 px-5">

        <div class="d-flex align-items-center position-relative my-1 me-3">
          <span class="svg-icon svg-icon-1 position-absolute ms-6">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
              <rect opacity="0.5" x="17.0365" y="15.1223" width="8.15546" height="2" rx="1" transform="rotate(45 17.0365 15.1223)" fill="black"></rect>
              <path d="M11 19C6.55556 19 3 15.4444 3 11C3 6.55556 6.55556 3 11 3C15.4444 3 19 6.55556 19 11C19 15.4444 15.4444 19 11 19ZM11 5C7.53333 5 5 7.53333 5 11C5 14.4667 7.53333 17 11 17C14.4667 17 17 14.4667 17 11C17 7.53333 14.4667 5 11 5Z" fill="black"></path>
            </svg>
          </span>
          <input type="text" id="inputCopySearch" name="inputCopySearch" data-kt-user-table-filter="search" class="form-control form-control-solid ps-14" style="padding:.8rem 1rem;" placeholder="Search...">
        </div>

        <div class="cj-table-card">
          <!-- Header -->
          <div class="cj-table-header">
            <div class="cj-col-chk">
              <div class="form-check form-check-sm form-check-custom form-check-solid">
                <input class="form-check-input chk_copy_list" type="checkbox" value="1" />
              </div>
            </div>
            <div class="cj-th">Job Details</div>
            <div class="cj-th">Experience</div>
            <div class="cj-th">Hiring &amp; Location</div>
            <div class="cj-th">Hiring Manager</div>
            <div class="cj-th">Team</div>
            <div class="cj-th">Action</div>
          </div>
          <!-- Rows injected via AJAX -->
          <div id="copyJobTable"></div>
        </div>

        <div id="paginationcopy" class="mt-3"></div>
      </div>
    </div>
  </div>
</div>

  </div>
</div>
<script>
    $('.chk_copy_list').click(function(){
		if($(".chk_copy_list").prop('checked') == true){
			var chk_copy_list 	=	$('.chk_copy_list').val();
			$('.chk_copy_item').prop('checked', true);
			$('.copy_current_selected_applicant').removeClass("d-none");
			 showToastr('success', 'Multiple Jobs added successfully!', 'Success');
		}else{
			 $('.chk_copy_item').prop('checked', false);
			 $('.copy_current_selected_applicant').addClass("d-none");
			 showToastr('error', 'Multiple Jobs removed successfully!', 'Removed');
		}
	});
     fetchCandidates();
	 $(document).on('click', '#paginationcopy .page-link', function(e) {
                e.preventDefault();
                const page = $(this).data('page');
                if (!page || $(this).hasClass('disabled')) return;
                $(this).addClass('disabled');
                fetchCandidates(page, $('#search').val()).then(() => {
                    $('#paginationcopy .page-link').removeClass('disabled');
                }).catch(() => {
                    $('#paginationcopy .page-link').removeClass('disabled');
                });
            });

	function fetchCandidates(page = 1, search = '') {
            var clientid = '1';
            if($('#kt_tagify_2').val()!=''){
                $('.loader_search').css({ display: 'block' });
            }else{
                $('.page-loader').css('display', 'flex');
            }
            $.ajax({
                url: `{!! route('service_company.cvtracker.copyjobtable') !!}?page=${page}`,
                type: 'GET',
                data: {
                  search : $('#inputCopySearch').val(),
				  findTalentAdd : '{{ @$findTalentAdd }}'
                },
                success: function(response) {
                    $('.loadingMessage').css({ display: 'none' });
                    $('.loader_search').css({ display: 'none' });
                    $('.page-loader').css('display', 'none');
                    $('#copyJobTable').html(response.html);
                    $('#paginationcopy').html(response.pagination);
                    markAddedButtons();
                }
            });
        }
</script>
<script>
   /* Pre-compute job IDs that already contain these candidates */
   var alreadyAddedJobIds = <?php
        $appIds = collect($applicationData)->pluck('id')->toArray();
        $addedJobIds = !empty($appIds)
            ? DB::table('job_applications')->whereIn('id', $appIds)->whereNotNull('job_id')->pluck('job_id')->toArray()
            : [];
        echo json_encode(array_values(array_unique($addedJobIds)));
   ?>;

   function markAddedButtons() {
       $('#copyJobTable .btn-add-single').each(function() {
           var jobId = parseInt($(this).data('job-id'));
           if (alreadyAddedJobIds.indexOf(jobId) !== -1) {
               $(this).addClass('btn-already-added').html(
                   '<svg width="11" height="11" fill="none" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"/></svg> Added'
               ).prop('disabled', true);
           }
       });
   }

   $(document).ready(function() {
	   $('#inputCopySearch').keyup(function() {
			 fetchCandidates();
	   });
   });

   $(document).on('click', '.chk_copy_single_add', function() {
        var jobId    = $(this).data('job-id');
        var token    = "{{ csrf_token() }}";
        var url      = "{{ route('service_company.cvtracker.copy_current_selected_applicant', ':id') }}".replace(':id', 1);

        var selectedApplicantListing = [];
        var jqueryarray = <?php echo json_encode($applicationData); ?>;
        jqueryarray.map(function(mval) { selectedApplicantListing.push(mval.id); });

        var selectedApplicantListingOld = [];
        var wiseIdOld = <?php echo json_encode($wiseId); ?>;
        wiseIdOld.map(function(mval) { selectedApplicantListingOld.push(mval.id); });

        var $btn = $(this);
        $btn.prop('disabled', true).text('Adding…');

        $.ajax({
            type: 'POST',
            url: url,
            data: {
                '_token': token,
                '_method': 'POST',
                'selectedJobListing': [jobId],
                'selectedApplicantListing': selectedApplicantListing,
                'selectedApplicantListingOld': selectedApplicantListingOld
            },
            success: function(response) {
                if (response.status == 'success') {
                    showToastr('success', 'Candidate added to job successfully!', 'Success');
                    alreadyAddedJobIds.push(parseInt(jobId));
                    $btn.addClass('btn-already-added').html(
                        '<svg width="11" height="11" fill="none" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"/></svg> Added'
                    ).prop('disabled', true);
                    return;
                } else if (response.status == 'coinCheck') {
                    Swal.fire('Failed', response.message, 'warning');
                }
                $btn.prop('disabled', false).html('<svg width="11" height="11" fill="none" viewBox="0 0 24 24"><path d="M12 5v14M5 12h14" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"/></svg> Add');
            },
            error: function() {
                showToastr('error', 'Something went wrong.', 'Error');
                $btn.prop('disabled', false).html('<svg width="11" height="11" fill="none" viewBox="0 0 24 24"><path d="M12 5v14M5 12h14" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"/></svg> Add');
            }
        });
   });
</script>
<script>
	$('.copy_current_selected_applicant').click(function(){

		const swalWithBootstrapButtons = Swal.mixin({
			  customClass: {
								confirmButton: 'btn btn-success',
								cancelButton: 'btn btn-danger'
							},
		  	 buttonsStyling: false
		})
		swalWithBootstrapButtons.fire({
										  title				: 'Are you sure?',
										  text				: "You won't be able to revert this!",
										  icon				: 'warning',
										  showCancelButton	: true,
										  confirmButtonText	: 'Yes, Save it!',
										  cancelButtonText	: 'No, cancel!',
										  reverseButtons	: true
									}).then((result) => {
		  if (result.isConfirmed) {

		  							var id        	=	1;
									var url       	=	"{{ route('service_company.cvtracker.copy_current_selected_applicant',':id') }}";
									url         	=	url.replace(':id', id);
									var baseurl     =	"{{ asset('assets/images/Loading2.gif') }}";
									var token       =	"{{ csrf_token() }}";
									var arr 		=	[];
									$('.chk_copy_item:checked').each(function () {
											arr.push($(this).val());
									});
									var selectedApplicantListing	=	[];
									var jqueryarray 				=	<?php echo json_encode($applicationData); ?>;

									jqueryarray.map((mval, mid, marr)=>{
										selectedApplicantListing.push( mval.id);
									});

									var selectedApplicantListingOld	=	[];
									var wiseIdOld 				=	<?php echo json_encode($wiseId); ?>;
									wiseIdOld.map((mval, mid, marr)=>{
										selectedApplicantListingOld.push( mval.id);
									});

									$.ajax({
													type		: 'POST',
													url			: url,
													data		: {'_token': token, '_method': 'POST', 'selectedJobListing' : arr, 'selectedApplicantListing': selectedApplicantListing,'selectedApplicantListingOld' :selectedApplicantListingOld},
													success 	: function (response) {
																	if (response.status == "success") {
																		  	fetchCandidates();
																			swalWithBootstrapButtons.fire(
																											  'Success!',
																											  'Selected Applicant Copy to Selected Requisitions.',
																											  'success'
																											);
																											$('#kt_activitiesCopy_close').click();
																	}

																	if (response.status == 'coinCheck') {

																		Swal.fire("Failed",` ${response.message}` , "warning").then(function(){
																			$(".kt_activitiesCopy_close").click();
																			});

																			return false;
																	}
														}
									});

		  }
		  else if (result.dismiss === Swal.DismissReason.cancel) {
						swalWithBootstrapButtons.fire(
														 'Cancelled',
														'Selected request is safe :)',
														'error'
														)
		  }
		})	});
</script>
