<script src="{{ asset('assets/node_modules/bootstrap-datepicker/bootstrap-datepicker.min.js') }}<?php echo TM_VERSION; ?>" type="text/javascript"></script>
<link rel="stylesheet" href="{{ asset('assets/plugins/jquery-bar-rating-master/dist/themes/fontawesome-stars.css') }}<?php echo TM_VERSION; ?>">
<style>
.resume-button
{
  text-align: center;
  margin-top: 1rem;
}
/*begin::Add new CSS*/
.input-icons i
{
  position: absolute;
} 
.input-icons
{
  width: 100%;
  margin-bottom: 10px;
} 
.icon
{
  padding: 10px;
  min-width: 40px;
}
.input-field
{
  width: 100%;
  padding: 10px;
  text-align: center;
}
.pll-2
{
  padding-left: 2.2rem!important;
}
.form-control.form-control-solid
{
  padding: 0.75rem 3rem 0.75rem 1rem;
  border-radius: 5px;
}
.form-check.form-check-custom .form-check-label
{
  padding-left: 1.6em;
}
.header-fixed.toolbar-fixed .wrapper
{
  margin: 1.3em 0em 0em .7em;
  background: #f1f2f5;
}
.card .card-body
{
  padding: 1rem 1em;
}

#chart_teamwork .row .col-md-12 .card .card-body:last-child
{
  /* margin-bottom: -2em; */
}

.error
{
  color: red;
  font-weight: 500!important;
}
table.dataTable>thead .sorting_desc:after
{
  display:none;
}
/*end::Add new CSS*/
</style>
<!--begin::Filter Popup-->
<div class="r-panel-body p-2">
  <!--begin::Modal header-->
  <div class="modal-header modal-htcp pb-0 border-0 justify-content-end">
    <!--begin::Close-->
    <div class="btn btn-sm btn-icon btn-active-color-primary" data-dismiss="modal">
      <!--begin::Svg Icon-->
      <span class="svg-icon svg-icon-1">
      <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
        <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1" transform="rotate(-45 6 17.3137)" fill="black"></rect>
        <rect x="7.41422" y="6" width="16" height="2" rx="1" transform="rotate(45 7.41422 6)" fill="black"></rect>
      </svg>
      </span>
      <!--end::Svg Icon-->
    </div>
    <!--end::Close-->
  </div>
  <!--begin::Modal header-->
  <!--begin::Modal body-->
  <div class="modal-body scroll-y px-3 px-lg-3 pt-0 pb-10">
    <form action="" id="report_frm" name="report_frm" class="form fv-plugins-bootstrap5 fv-plugins-framework" action="#" data-select2-id="select2-data-kt_modal_new_target_form">
    <!--begin::Heading-->
    <div class="mb-10 text-center">
      <!--begin::Title-->
      <h2 class="mb-3">Proceed to Report</h2>
      <!--end::Title-->
    </div>
    <!--end::Heading-->
    <input type="hidden" class="form-control w-100" id="custom_text" name="custom_text" placeholder="Enter Client/Requisitions/Team">
    <!--begin::Row-1-->
    <div class="row g-9 mb-8">
      <!--begin::>Start Date-->
      <div class="col-md-4 fv-row text-left">
        <label class="required fs-6 fw-bold mb-2">Start Date</label>
        <div class="input-icons"> <i class="fa fa-calendar-alt icon"></i>
          <input type="text" class="form-control form-control-solid w-100 pll-2 cursor-pointer" id="start_date" name="start_date" placeholder="Select start date" value="" />
        </div>
      </div>
      <!--end::>Start Date-->
      <!--begin::>End Date-->
      <div class="col-md-4 fv-row text-left">
        <label class="required fs-6 fw-bold mb-2">End Date</label>
        <div class="input-icons"> <i class="fa fa-calendar-alt icon"></i>
          <input type="text" class="form-control form-control-solid w-100 pll-2 cursor-pointer" id="end_date" name="end_date" placeholder="Select end date" value="" />
        </div>
      </div>
      <!--end::>End Date-->
      <!--begin::Clients-->
      <div class="col-md-4 fv-row text-left">
        <label class="fs-6 fw-bold mb-2">Client</label>
        <select name="clients" id="clients" class="form-select form-select-solid w-100" onchange="getClientJob(this.value);">
          <option value="all">All Clients</option>
             
                  @forelse($clients as $clientsArr)                          
                  
          <option value="{{$clientsArr->id}}">{{ucfirst($clientsArr->name)}}</option>
          
                  @empty
                  @endforelse            
                
        </select>
        <span class="clientStatusLoader" style="height:25px; width:25px"></span> </div>
      <!--end::Clients-->
    </div>
    <!--end::Row-1-->
    <!--begin::Row-2-->
    <div class="row g-9 mb-8">
      <!--begin::Requisition-->
      <div class="col-md-4 fv-row text-left">
        <label class="fs-6 fw-bold mb-2">Requisitions</label>
        <select class="form-select form-select-solid" name="jobs" id="jobs">
          <option value="all">All Jobs</option>
                      
                  @forelse($jobs as $job)
                  
          <option title="{{ucfirst($job->title)}}" value="{{$job->id}}">{{$job->id}} - {{ucfirst($job->title)}}</option>
          
                  @empty
                  @endforelse            
                
        </select>
      </div>
      <!--end::Requisition-->
      <!--begin::Interview Stage-->
      <div class="col-md-4 fv-row text-left">
        <label class="fs-6 fw-bold mb-2">Interview Stage</label>
        <select class="form-select form-select-solid" name="master_status_id" id="master_status_id">
          <option value="all">All Process</option>
          
                  @forelse($candidateStatus as $status)              
                
          <option value="{{$status->id}}">{{ucfirst($status->name)}}</option>
          
                @empty
                @endforelse           
              
        </select>
      </div>
      <!--end::Interview Stage-->
      <!--begin::Team-->
      <div class="col-md-4 fv-row text-left">
        <label class="fs-6 fw-bold mb-2">Team</label>
        <select class="select22 form-select form-select-solid" name="source" id="source">
          <option value="all">All Source</option>
          <?php 
                  $type_zero = ''; 
                  $type_one  = '';
                    foreach($users as $nameArr)
                    {
                      if($nameArr->type == 1)
                      {
                        $type_one   .= '<option value="'.$nameArr->id.'">'.ucfirst($nameArr->name).'</option>';	
                      }
                      else
                      {
                        $type_zero  .= '<option value="'.$nameArr->id.'">'.ucfirst($nameArr->name).'</option>';		
                      }
                    }
                ?>
          <optgroup label="Internal Team"><?php echo $type_zero; ?></optgroup>
          <optgroup label="Consultancy"><?php echo $type_one; ?></optgroup>
        </select>
      </div>
      <!--end::Team-->
    </div>
    <!--end::Row-2-->
    <!--begin::Row-03-->
    <div class="fv-row mb-8">
      <div class="d-flex flex-stack">
        <div class="fw-bold me-5">
          <label class="fs-6 fw-bold mb-2">Show Interview Details</label>
          <div class="fs-7 text-muted">Allow Interview Details</div>
        </div>
        <div class="d-flex align-items-center me-5">
          <label class="form-check form-check-custom form-check-solid me-20">
          <input class="form-check-input h-20px w-20px" type="radio" name="interview_process" id="interview_schedule_yes" value="1">
          <span class="form-check-label fw-bold pl-7">Yes</span> </label>
          <label class="form-check form-check-custom form-check-solid">
          <input class="form-check-input h-20px w-20px" type="radio" name="interview_process" id="interview_schedule_no" value="2" checked="checked">
          <span class="form-check-label fw-bold pl-7">No</span> </label>
        </div>
      </div>
      <!-- New Dropdown -->
      <div class="col-xs-4 col-md-3 d-none">
        <div class="form-group">
          <label class="d-block text-dark text-capitalize mt-0" style="">
          <label class="custom-label-color mb-0 pl-1">Show Interview Details</label>
          </label>
          <select class="select22 w-100 form-control">
            <option value="select">Select</option>
            <option name="interview_process" value="1" id="interview_schedule_yes">Yes</option>
            <option name="interview_process" value="2"  id="interview_schedule_no">No</option>
          </select>
        </div>
      </div>
      <!-- New Dropdown End -->
    </div>
    <!--end::Row-03-->
    <!--begin::Actions-->
    <div class="text-center">
      <button type="button" id="apply-filters" class="btn btn-light-primary me-3" data-dismiss="modal">@lang('app.apply')</button>
      <button type="button" id="reset-filters" class="btn btn-light-info me-3">@lang('app.reset')</button>
      <button type="button" id="" class="btn btn-light-dark" data-dismiss="modal">Cancel</button>
    </div>
    <!--end::Actions-->
    </form>
  </div>
  <!--end::Modal body-->
</div>
<!--end::Filter Popup-->
<script src="{{ asset('assets/node_modules/select2/dist/js/select2.full.min.js') }}<?php echo TM_VERSION; ?>" type="text/javascript"></script>
<script src="{{ asset('assets/node_modules/bootstrap-select/bootstrap-select.min.js') }}<?php echo TM_VERSION; ?>" type="text/javascript"></script>
<script src="//cdn.datatables.net/fixedheader/3.1.5/js/dataTables.fixedHeader.min.js"></script>
<script src="//cdn.datatables.net/responsive/2.2.3/js/dataTables.responsive.min.js"></script>
<script src="//cdn.datatables.net/responsive/2.2.3/js/responsive.bootstrap.min.js"></script>
<script src="{{ asset('assets/node_modules/moment/moment.js') }}<?php echo TM_VERSION; ?>" type="text/javascript"></script>
<script src="{{ asset('assets/node_modules/bootstrap-datepicker/bootstrap-datepicker.min.js') }}<?php echo TM_VERSION; ?>" type="text/javascript"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.css" rel="stylesheet" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.9.0/jquery.validate.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.9.0/additional-methods.min.js"></script>
<script type="text/javascript">
  $().ready(function()
  {  
    $("#report_frm").validate(
    {   
      rules:
      {
        start_date: "required",
        end_date: "required"
      },
      messages:
      {
        start_date: "Please select start date",
        end_date: "Please select end date"
      }
    });   
  });   
</script>
<script src="{{ asset('js/jquery.validate.js') }}<?php echo TM_VERSION; ?>"></script>
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/additional-methods.min.js"></script>
<script>
$("#report_frm").validate({
			onfocusout: function(e) {
        		this.element(e);
    		},	
			rules: {						
						start_date: "required",
						end_date: "required"										
					},
			messages: {
						start_date: "Please select start date",
						end_date: "Please select end date"															
					 }					 
});		
</script>
<script>
   loadTable(); 
   
   $('#start_date').datepicker({
   // format: 'yyyy-mm-dd'
   // format: 'd-M-Y: H:I:s'
   format: 'd-M-yyyy'
   })
   
   $('#end_date').datepicker({
   // format: 'yyyy-mm-dd'
   //format: 'd-M-Y: H:I:s'
   format: 'd-M-yyyy'
   })		
    
   function getClientJob(clientId)
   {
   
   	var clientId 		= clientId;					
   	var url 			= "{{ route('service_company.cvtracker.get_client_job',':clientId') }}";					
   	url 				= url.replace(':clientId', clientId);					
   	var baseurl 		= "{{ asset('assets/images/Loading2.gif') }}"; 									
   	var token 			= "{{ csrf_token() }}";
   	$.easyAjax({   
                type: 'POST',
                url: url,
                data: {'_token': token, '_method': 'POST', 'clientId' : clientId},
                beforeSend: function() {
                $('.clientStatusLoader').html('<img src="'+baseurl+'" height="30px" width="150px">');
                },
                success: function (response)
                {   
                  $('#jobs').html(response.data);
                  $('.clientStatusLoader').html('');
                  //tableLoad('filter');
                },   
                complete: function (data)
                {
                    $('.clientStatusLoader').html('');
                }
              });   
   }   
   // Apply Filter   
   $('#apply-filters').click(function ()
   {
    loadTable();
   });
   
   function loadTable()
   {   	
	   var custom_text			=	$('#custom_text').val();
	   var start_date			=	$('#start_date').val();
	   var end_date				=	$('#end_date').val();
	   var clients				=	$('#clients').val(); // Clients Default value = all else other number
	   var jobs					=	$('#jobs').val();			// Jobs Default value = all else other number
	   var source				=	$('#source').val();	  // Team Default value = all else other number
	   var master_status_id		=	$('#master_status_id').val();	// Jobs Default value = all else other number
	   var interview_process 	= $('input[name="interview_process"]:checked').val(); // 0 For Not intrested interview schedule , 1 For intrested interview schedule 
     
		  if($("#report_frm").valid())
		  { 
   					$('#lbl_start_date').html(start_date);
						$('#lbl_end_date').html(end_date);
   						teamwork();
   
   						$("body").mLoading();
						var table = $('#myTable').dataTable({
												responsive			: true,
												processing			: true,
												serverSide			: true,
												"bFilter"			: false,
												"bDestroy"			: true,
												stateSave			: true,"bInfo": false,
											   "bLengthChange"		: false,
												oLanguage			: {sLengthMenu: "_MENU_",},
												lengthMenu			: [[10, 25, 50, 100,150,200,250,300], [10, 25, 50, 100,150, 200, 250, 300]],					
			   
			   
			   ajax: '{!! route('service_company.customreport.data') !!}?custom_text='+custom_text+'&start_date='+start_date+'&end_date='+end_date+'&clients='+clients+'&jobs='+jobs+'&source='+source+'&master_status_id='+master_status_id+'&interview_process='+interview_process,
				   language: {
					   "url": "<?php echo __("app.datatable") ?>"
				   },
			   
				   "fnDrawCallback": function( oSettings ) {
						$("body").tooltip({  
						   selector: '[data-bs-toggle="tooltip"]' 
					   });  
				   },  
			   
					"initComplete":function( settings, json){
							setTimeout(function(){$("body").mLoading('hide');}, 1500);
					},
					
			   "columnDefs": [   
								  { "width": "10px", "targets": 0 },
								  { "width": "250px", "targets": 1 },
								  { "width": "400px", "targets": 2 },
								  { "width": "70px", "targets": 3 },
								  { "width": "70px", "targets": 4 },
								  { "width": "70px", "targets": 5 },
								  { "width": "70px", "targets": 6 },
								  { "width": "70px", "targets": 7 },
								  { "width": "70px", "targets": 8 },
								  { "width": "70px", "targets": 9 },
								  { "width": "70px", "targets": 10 },
								  { "width": "70px", "targets": 11 } 
					],
			 columns: [
						{ data: 'id'},
						{ data: 'client_name', name: 'client_name', searchable : false, orderable : false},
						{ data: 'job_title', name: 'job_title' , searchable : false, orderable : false},
						{ data: 'source', name: 'source', searchable : false, orderable : false },
						{ data: 'total_application', name: 'total_application', searchable : false, orderable : false },	
						{ data: 'sourcing', name: 'sourcing', searchable : false, orderable : false },
						{ data: 'screening', name: 'screening', searchable : false, orderable : false },
						{ data: 'interview', name: 'interview', searchable : false, orderable : false },
						{ data: 'negotiation', name: 'negotiation', searchable : false, orderable : false },
						{ data: 'offered', name: 'offered', searchable : false, orderable : false },
						{ data: 'joined', name: 'joined', searchable : false, orderable : false },
						{ data: 'rejected', name: 'rejected', searchable : false, orderable : false }
			   
					]
			   
				});
			   
						new $.fn.dataTable.FixedHeader( table );					
			   
						if(interview_process == 1)
							{
			   
				 var table = $('#myInterviewSchedule').dataTable({
			   
			   
			   
												 responsive		: true,
												 processing		: true,
												 serverSide		: true,
												 "bFilter"		: false,
												 stateSave		: true,"bInfo": false,
												"bLengthChange"	: false,
												 oLanguage		: {sLengthMenu: "_MENU_",},
												 lengthMenu		: [[10, 25, 50, 100,150,200,250,300], [10, 25, 50, 100,150, 200, 250, 300]],
			   
			   
			   destroy: true,
				//aaData: response.data,			   
			   //ajax: '{!! route('service_company.customreport.myInterviewSchedule') !!}',
			   ajax: '{!! route('service_company.customreport.myInterviewSchedule') !!}?custom_text='+custom_text+'&start_date='+start_date+'&end_date='+end_date+'&clients='+clients+'&jobs='+jobs+'&source='+source+'&master_status_id='+master_status_id+'&interview_process='+interview_process,
			   	   
				   language		: {"url": "<?php echo __("app.datatable") ?>"},
				   "fnDrawCallback": function( oSettings ) {
						$("body").tooltip({
								selector: '[data-bs-toggle="tooltip"]'
							});
				   },
			   
			   "columnDefs": [
					  { "width": "10px", "targets": 0 },
					  { "width": "150px", "targets": 1 },
					  { "width": "200px", "targets": 2 },
					  { "width": "200px", "targets": 3 },
					  { "width": "300px", "targets": 4 },
					  { "width": "70px", "targets": 5 },
					  { "width": "70px", "targets": 6 },
					  { "width": "70px", "targets": 7 },
					  { "width": "70px", "targets": 8 },
					  { "width": "70px", "targets": 9 },
					  { "width": "70px", "targets": 10 },
					  { "width": "70px", "targets": 11 }
					],
			   			   
	columns: [
				{ data: 'id'},
				{ data: 'schedule_date', name: 'schedule_date', searchable : false, orderable : false},
				{ data: 'full_name', name: 'full_name', searchable : false, orderable : false },
				{ data: 'client_name', name: 'client_name' , searchable : false, orderable : false},		
				{ data: 'requisitions_title', name: 'requisitions_title', searchable : false, orderable : false },	
				{ data: 'source_user', name: 'source_user', searchable : false, orderable : false },
			   
				{ data: 'interview_status_name', name: 'interview_status_name', searchable : false, orderable : false },
				{ data: 'interview_mode', name: 'interview_mode', searchable : false, orderable : false },
				{ data: 'status_name', name: 'status_name', searchable : false, orderable : false }, 
				{ data: 'child_status_name', name: 'child_status_name', searchable : false, orderable : false },
				{ data: 'interviewers', name: 'interviewers', searchable : false, orderable : false },											
				{ data: 'feedback', name: 'feedback', searchable : false, orderable : false }
						]
			   
			   
			   
			   });
			   
							 new $.fn.dataTable.FixedHeader( table );
			   
				
			   
			   }
			   
 
  			} 
   
   
   }
    
   
   $('#myTable').on('click', '.joblisting', function() {
   
   var id 				= $(this).data('row-id');
   var row_status 		= $(this).data('row-status');			
  //  url 				=  '{!! route('service_company.customreport.show') !!}?id='+id+'&row_status='+row_status;	
   url 				= '{!! route('service_company.customreport.show') !!}?job_id='+id+'&application_status='+row_status;
   	
          	$('#modelHeading').html('Schedule');
          	$.ajaxModal('#scheduleDetailModal', url);					
   });
     
  
   $('.report_filter').click(function () {				
			url 				= '{!! route('service_company.customreport.report_filter') !!}';				
			$('#modelHeadingReportFilter').html('Schedule');
			$.ajaxModal('#scheduleDetailModalReportFilter', url);		
   });  
   
 
</script>
<script>

		
// Reset Filters
$('#reset-filters').click(function () {
           
		   $('#custom_text').val('');
		   $('#start_date').val('');
		   $('#end_date').val('');
		   $('#clients').val('all');
		   $('#jobs').val('all');
		   $('#master_status_id').val('all');
		   $('#source').val('all');
          	//loadTable();
			
			//var table = $('#myTable').DataTable(); 
 
			$( "#myTable tbody tr" ).each( function(){
				this.parentNode.removeChild( this ); 
			});
			$( "#myInterviewSchedule tbody tr" ).each( function(){
				this.parentNode.removeChild( this ); 
			});
			
			 
	
			
        })
					
</script>
<!--begin::Tooltip-->
<script>
  $(function () {
  $('[data-toggle="tooltip"]').tooltip()
})
</script>
<!--end::Tooltip-->
