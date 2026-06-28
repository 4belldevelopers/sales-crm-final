@php 
		use App\Helper\GeneralMethod;
@endphp
<style>
.text-justify{text-align: justify!important;}
.symbol1 > img,
.symbol1 .symbol-label1{width: 32px!important; height: 32px!important;}
.table > :not(caption) > * > *{padding: 0rem 0.4rem;}

</style>
<div class="card-body position-relative px-0 py-0" id="kt_activities_body">
  <input type="hidden" name="hid_job_id" id="hid_job_id" value="" />
  <div id="kt_activities_scroll" class="position-relative scroll-y bg-light pt-2" data-kt-scroll="true" data-kt-scroll-height="auto" data-kt-scroll-wrappers="#kt_activities_body" data-kt-scroll-dependencies="#kt_activities_header, #kt_activities_footer" data-kt-scroll-offset="5px">
 
	
<div class="post d-flex flex-column-fluid" id="kt_post">
  <div id="kt_content_container" class="container-fluid px-3">
    <div class="card">
      <div class="card-body pt-3 px-5">

		 <div class="d-flex align-items-center position-relative my-1 me-3"> <span class="svg-icon svg-icon-1 position-absolute ms-6">
                  <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                    <rect opacity="0.5" x="17.0365" y="15.1223" width="8.15546" height="2" rx="1" transform="rotate(45 17.0365 15.1223)" fill="black"></rect>
                    <path d="M11 19C6.55556 19 3 15.4444 3 11C3 6.55556 6.55556 3 11 3C15.4444 3 19 6.55556 19 11C19 15.4444 15.4444 19 11 19ZM11 5C7.53333 5 5 7.53333 5 11C5 14.4667 7.53333 17 11 17C14.4667 17 17 14.4667 17 11C17 7.53333 14.4667 5 11 5Z" fill="black"></path>
                  </svg>
                  </span>
                  <input type="text" id="inputCopySearch" name="inputCopySearch" data-kt-user-table-filter="search" class="form-control form-control-solid ps-14" style="padding: .8rem 1rem;" placeholder="Search...">
        </div>

        
        <table class="table align-middle table-row-dashed fs-6 gy-5">
          <thead>
            <tr class="fw-bolder text-muted">
              <th class="w-10px pe-0"> <div class="form-check form-check-sm form-check-custom form-check-solid me-3">
                  <input class="form-check-input chk_copy_list" type="checkbox" value="1" />
                </div></th>
              <th class="">Jobs</th>
              <th class="">Exp.</th>
              <th class="">Salary</th>
              <th class="">Location</th>
              <th class="">Status</th>
              <th class="">Date</th>
              <th class=""><i class="fa fa-users" aria-hidden="true" style="font-size:17px"></i></th>
              <th class=""><i class="fa fa-handshake" aria-hidden="true" style="font-size:17px"></i></th>
              <th class="">TOT</th>
              <th class="" >SOU</th>
              <th class="" >SCR</th>
              <th class="" >EVA</th>
              <th class="" >NEO</th>
              <th class="" >OFF</th>
              <th class="">JOI</th>
              <th class="" >REJ</th>
            </tr>
          </thead>
		   <tbody class="text-gray-600 fw-bold" id="copyJobTable">
            </tbody>
        </table>
		 <div id="paginationcopy"></div>
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
			// $('.chk_copy_item').attr('checked','checked');
			$('.chk_copy_item').prop('checked', true);
			$('.copy_current_selected_applicant').removeClass("d-none");
			 showToastr('success', 'Multiple Jobs added successfully!', 'Success');
		}else{
			// $('.chk_copy_item').removeAttr('checked');	
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

                // Disable the clicked pagination link
                $(this).addClass('disabled');

                // Fetch data
                fetchCandidates(page, $('#search').val()).then(() => {
                    // Re-enable the links after successful data fetch
                    $('#paginationcopy .page-link').removeClass('disabled');
                }).catch(() => {
                    // Re-enable the links even if there's an error
                    $('#paginationcopy .page-link').removeClass('disabled');
                });
            });

	function fetchCandidates(page = 1, search = '') {
            var clientid = '1';
            if($('#kt_tagify_2').val()!=''){
                // $('.loadingMessage').css({ display: 'block' });
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
                }
            });
        }

</script>
<script>	
   $(document).ready(function() {	
	   $('#inputCopySearch').keyup(function() {
			// table._fnDraw();
			 fetchCandidates();
	   });
   });

</script>
<script>
    var table = $('#copyJobTabless').dataTable({
   			responsive  	: true,
   			processing  	: true,
   			serverSide  	: true,
   			destroy   		: true,
   			ordering		: false, //disable ordering
   			"bLengthChange" : false, //thought this line could hide the LengthMenu   
   			sDom			: 'lrtip',
   			oLanguage 		: { sLengthMenu: "_MENU_",},
      		ajax			: {'url' : '{!! route('service_company.cvtracker.copyjobtable') !!}', "data": function ( d ){d.search = $('#inputCopySearch').val()}},
   
   			"drawCallback"	: function( settings ) {
										$('[data-toggle="tooltip1"]').tooltip();
										$('[data-toggle="tooltip2"]').tooltip();
										$('[data-toggle="tooltip3"]').tooltip();
										$('[data-toggle="tooltip4"]').tooltip();		
								},
   			"columnDefs"	:
							  [ 
										{ "width": "50px", "targets": 0, "width": "4%", "class":""},
										{  "width": "200px", "targets": 1, "class":"d-flex align-items-center" },																				
										{  "width": "50px"  , "targets": 2},		
										{  "width": "100px", "targets": 3 },		
										{  "width": "200px", "targets": 4 }, 
										
										{  "width": "50px" ,"targets": 5 },
										{  "width": "200px" , "targets": 6 },              
										{  "width": "150px" , "targets": 7 },	
										{  "width": "150px", "targets": 8 },	
										{ "width": "20px", "targets": 9 },
											
										{ "width": "20px", "targets": 10 }, 
										{ "width": "20px", "targets": 11 },    
										{ "width": "20px", "targets": 12 },             
										{ "width": "20px", "targets": 13 },
										{ "width": "20px", "targets": 14 }, 
										  
										{ "width": "20px", "targets": 15 },
										{ "width": "20px", "targets": 16 }  
										
								  ], 
   				columns			:
									[
										{ data: 'chkbox', name: 'chkbox',  orderable: false, searchable: false},  
										{ data: 'Requisition', name: 'Requisition' , orderable: false, searchable: false},
										{ data: 'Exp', name: 'Exp' , orderable: false, searchable: false},
										{ data: 'Salary', name: 'Salary' , orderable: false, searchable: false},
										{ data: 'Location', name: 'Location' , orderable: false, searchable: false},
										
										{ data: 'Status', name: 'Status' , orderable: false, searchable: false},
										{ data: 'Date', name: 'Date' , orderable: false, searchable: false},
										{ data: 'HM', name: 'HM' , orderable: false, searchable: false},
										{ data: 'Partners', name: 'Partners' , orderable: false, searchable: false},
										{ data: 'TOT', name: 'TOT' , orderable: false, searchable: false},
										
										{ data: 'SOU', name: 'SOU' , orderable: false, searchable: false},					
										{ data: 'SCR', name: 'SCR' , orderable: false, searchable: false},
										{ data: 'EVA', name: 'EVA' , orderable: false, searchable: false},
										{ data: 'NEO', name: 'NEO' , orderable: false, searchable: false},
										{ data: 'OFF', name: 'OFF' , orderable: false, searchable: false},
										
										{ data: 'JOI', name: 'JOI' , orderable: false, searchable: false},
										{ data: 'REJ', name: 'REJ' , orderable: false, searchable: false}	   
									],
				//timeout		: 500000,
           		async		: false
   				
   				});   
   	new $.fn.dataTable.FixedHeader( table );	
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
																		  	// table._fnDraw();
																			 fetchCandidates();
																			swalWithBootstrapButtons.fire(
																											  'Success!',
																											  'Selected Applicant Copy to Selected Requisitions.',
																											  'success'
																											);
																											$('#kt_activitiesCopy_close').click();
																	}
																	// if (response.status == "success") {
																	// 	  	table._fnDraw();
																	// 		swalWithBootstrapButtons.fire(
																	// 										  'Success!',
																	// 										  'Selected Applicant Copy to Selected Requisitions.',
																	// 										  'success'
																	// 										)
																	// }
																	
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
