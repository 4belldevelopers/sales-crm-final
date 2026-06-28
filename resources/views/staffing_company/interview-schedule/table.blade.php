@extends('layouts.staffing_company_layout')
<style>

/*begin::Media query*/
@media  only screen and (min-width: 900px) and (max-width: 2560px)
{
  .table td, .table th, .table tr
  {
    width: max-content!important;
  }
  .table > :not(caption) > * > *
  {
    padding: 0rem;
  }
  td:nth-child(n+2),
  th:nth-child(n+2)
  {
    width: revert!important;
  }
}
/*end::Media query*/

</style>
@section('content')
<!--begin::Content-->
<div class="content d-flex flex-column flex-column-fluid py-4" id="kt_content">
  <!--begin::Toolbar-->
  <div class="toolbar" id="kt_toolbar">
    <!--begin::Container-->
    <div id="kt_toolbar_container" class="container-fluid d-flex flex-stack">
      <!--begin::Page title-->
      <div data-kt-swapper="true" data-kt-swapper-mode="prepend" data-kt-swapper-parent="{default: '#kt_content_container', 'lg': '#kt_toolbar_container'}" class="page-title d-flex align-items-center flex-wrap me-3 mb-5 mb-lg-0">
        <!--begin::Title-->
        <div class="d-flex flex-column">
          <h5 class="d-flex align-items-center text-dark fw-bolder">Interview Tracker</h5>
          <span class="text-gray-600" id="total_record">0</span> </div>
        <!--end::Title-->
      </div>
      <!--end::Page title-->
      <!--begin::Actions-->
      <div class="d-flex align-items-center py-1">
        <!--begin::Search-->
        <div class="d-flex align-items-center position-relative my-1 me-3">
          <!--begin::Svg Icon | path: icons/duotune/general/gen021.svg-->
          <span class="svg-icon svg-icon-1 position-absolute ms-6">
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
            <rect opacity="0.5" x="17.0365" y="15.1223" width="8.15546" height="2" rx="1" transform="rotate(45 17.0365 15.1223)" fill="black" />
            <path d="M11 19C6.55556 19 3 15.4444 3 11C3 6.55556 6.55556 3 11 3C15.4444 3 19 6.55556 19 11C19 15.4444 15.4444 19 11 19ZM11 5C7.53333 5 5 7.53333 5 11C5 14.4667 7.53333 17 11 17C14.4667 17 17 14.4667 17 11C17 7.53333 14.4667 5 11 5Z" fill="black" />
          </svg>
          </span>
          <!--end::Svg Icon-->
          <input type="text" data-kt-user-table-filter="search" name="inputSearch" id="inputSearch" class="form-control form-control-solid w-250px ps-14" style="padding: .6rem 1rem;" placeholder="Search..." />
        </div>
        <!--end::Search-->
        <div class="card-toolbar">
          <!--begin::Toolbar-->
          <div class="d-flex justify-content-end" data-kt-user-table-toolbar="base">
            <!--begin::Filter-->
            <button type="button" class="btn btn-sm btn-flex btn-light btn-active-primary fw-bolder me-3" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end" data-kt-menu-flip="top-end">
            <!--begin::Svg Icon | path: icons/duotune/general/gen031.svg-->
            <span class="svg-icon svg-icon-2">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
              <path d="M19.0759 3H4.72777C3.95892 3 3.47768 3.83148 3.86067 4.49814L8.56967 12.6949C9.17923 13.7559 9.5 14.9582 9.5 16.1819V19.5072C9.5 20.2189 10.2223 20.7028 10.8805 20.432L13.8805 19.1977C14.2553 19.0435 14.5 18.6783 14.5 18.273V13.8372C14.5 12.8089 14.8171 11.8056 15.408 10.964L19.8943 4.57465C20.3596 3.912 19.8856 3 19.0759 3Z" fill="black" />
            </svg>
            </span>
            <!--end::Svg Icon-->
            Filter</button>
            <!--end::Filter-->
          </div>
          <!--end::Toolbar-->
          <!--begin::Group actions-->
          <div class="d-flex justify-content-end align-items-center d-none" data-kt-user-table-toolbar="selected">
            <div class="fw-bolder me-5"> <span class="me-2" data-kt-user-table-select="selected_count"></span>Selected</div>
            <button type="button" class="btn btn-danger" data-kt-user-table-select="delete_selected">Delete Selected</button>
          </div>
        </div>
      </div>
      <!--end::Actions-->
    </div>
    <!--end::Container-->
  </div>
  <!--end::Toolbar-->
  <!--begin::Post-->
  <div class="post d-flex flex-column-fluid" id="kt_post">
    <!--begin::Container-->
    <div id="kt_content_container" class="container-fluid px-3">
      <!--begin::Card-->
      <div class="card">
        <!--begin::Card body-->
        <div class="card-body pt-3 px-5"> @if(in_array("view_schedule", $userPermissions))
          <table id="myTable" class="table align-middle table-row-dashed fs-6 gy-5">
            <thead>
              <tr class="fw-bolder text-muted">
                <th class="w-10px pe-0"> <div class="form-check form-check-sm form-check-custom form-check-solid me-3">
                    <input class="form-check-input" type="checkbox" data-kt-check="true" data-kt-check-target="#kt_table_users .form-check-input" value="1" />
                  </div></th>
                <th class="">Name</th>
                <th class="">Requisitions</th>
                <th class="">Interview Mode</th>
                <th class="">Interviewers</th>
                <th class="">Source</th>
                <th class="">Interview Date</th>
                <th class="">Feedback</th>
                <th class="">Rating</th>
                <th class="">Stage</th>
              </tr>
            </thead>
          </table>
          @endif </div>
        <!--end::Card body-->
      </div>
      <!--end::Card-->
    </div>
    <!--end::Container-->
  </div>
  <!--end::Post-->
</div>
<!--end::Content-->
<!--begin::Modal - Interview Feedback-->
<div href="javascript:void(0);" id="intervied_feedback_toggle" class="text-dark fw-bolder text-hover-primary fs-6" data-bs-toggle="modal" data-bs-target="#interview_feedback"></div>
<div class="modal fade" id="interview_feedback" tabindex="-1" aria-hidden="true" >
  <div class="modal-dialog modal-dialog-centered modal-xl">
    <div class="modal-content">
      <div class="modal-header flex-stack">
        <h2>Interview Feedback</h2>
        <div class="btn btn-sm btn-icon btn-active-color-primary" data-bs-dismiss="modal"> <span class="svg-icon svg-icon-1">
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
            <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1" transform="rotate(-45 6 17.3137)" fill="black" />
            <rect x="7.41422" y="6" width="16" height="2" rx="1" transform="rotate(45 7.41422 6)" fill="black" />
          </svg>
          </span> </div>
      </div>
      <div id="load_interview_feedback">&nbsp;</div>
    </div>
  </div>
</div>
@endsection

@push('footer-script')
<script src="{{ asset('assets/node_modules/select2/dist/js/select2.full.min.js') }}<?php echo TM_VERSION; ?>" type="text/javascript"></script>
<script src="{{ asset('assets/node_modules/bootstrap-select/bootstrap-select.min.js') }}<?php echo TM_VERSION; ?>" type="text/javascript"></script>
<script src="//cdn.datatables.net/fixedheader/3.1.5/js/dataTables.fixedHeader.min.js"></script>
<script src="//cdn.datatables.net/responsive/2.2.3/js/dataTables.responsive.min.js"></script>
<script src="//cdn.datatables.net/responsive/2.2.3/js/responsive.bootstrap.min.js"></script>
<script src="{{ asset('assets/node_modules/moment/moment.js') }}<?php echo TM_VERSION; ?>" type="text/javascript"></script>
<script src="{{ asset('assets/node_modules/bootstrap-datepicker/bootstrap-datepicker.min.js') }}<?php echo TM_VERSION; ?>" type="text/javascript"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.css" rel="stylesheet" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.js"></script>
<script>    
    
	$(document).ready(function() { 
      $('#inputSearch').keyup(function() {
        
          tableLoad('filter');   
          //alert("call");   
      });
   });
   

        loadTable();
        $(".select2").select2({
            formatNoMatches: function () {
                return "{{ __('messages.noRecordFound') }}";
            }
        });
        $('#start-date').datepicker({
            // format: 'yyyy-mm-dd'
           // format: 'd-M-Y: H:I:s'
      format: 'yyyy-m-d'
        })
        $('#end-date').datepicker({
            // format: 'yyyy-mm-dd'
            //format: 'd-M-Y: H:I:s'
      format: 'yyyy-m-d'
        })

        var table;
        function loadTable(){
            
								var startDate = $('#start-date').val();
								if (startDate == '') {
									startDate = null;
								}
					
								var endDate = $('#end-date').val();
								if (endDate == '') {
									endDate = null;
								}
					
								startDate 			= null;
								endDate 			= null;
								 
								var status 			= $('#status').val();
								var applicationID 	= $('#applicationID').val();
								var inputSearch 		= $('#inputSearch').val();


								table   = $('#myTable').dataTable({
								
											responsive  	: true,
											processing  	: true,
											serverSide  	: true,
											destroy   		: true,
											ordering		: false, //disable ordering
											 
											"bLengthChange" : false, //thought this line could hide the LengthMenu
											sDom			: 'lrtip',
                     
											oLanguage : {
															sLengthMenu: "_MENU_",
														},

ajax    		: '{!! route('staffing_company.interview-schedule.data') !!}?startDate=' + startDate + '&endDate=' + endDate + '&status=' + status + '&applicationID=' + applicationID+ '&inputSearch=' + inputSearch,
   
              
                "fnDrawCallback": function( settings, start, end, max, total, pre ) {
				
					var total_record	=	this.fnSettings().fnRecordsTotal();
					$('#total_record').html(total_record);			
					
					$("body").tooltip({
                        selector: '[data-toggle="tooltip"]'
                    });
                },
             
            "columnDefs": [
  
							{"targets": 0 },
							{"targets": 1, "class":"align-items-center" }, 
							{"targets": 2 },
							{ "targets": 3 },
							{ "targets": 4 },
							{ "targets": 5 },            
							{ "targets": 6 },
							{ "targets": 7 },
							{ "targets": 8 },
							{ "targets": 9 }
					
						  ],             
      columns: [
				  { data: 'chkbox', name:'chkbox',width: '1%', searchable : false, orderable: false},  
				  { data: 'Name', name: 'Name',width: '15%', searchable : false, orderable: false},
				  { data: 'Requisitions', name: 'Requisitions',width: '10%', searchable : false, orderable: false},
				  { data: 'InterviewMode', name: 'InterviewMode',width: '10%', searchable : false, orderable: false},
				  { data: 'Interviewers', name: 'Interviewers',width: '10%', searchable : false, orderable: false},
				  { data: 'Source', name: 'Source',width: '10%', searchable : false, orderable: false},
				  { data: 'InterviewDate', name: 'InterviewDate',width: '10%', searchable : false, orderable: false},
				  { data: 'Feedback', name: 'Feedback',width: '10%', searchable : false, orderable: false},
				  { data: 'Rating', name: 'Rating',width: '10%', searchable : false, orderable: false},		  
				  { data: 'Stage', name: 'Stage',width: '10%', searchable : false, orderable: false}
			  ],
			  
			  timeout: 500000,
           async: false
			  
            });
            new $.fn.dataTable.FixedHeader( table );

        }
  
    table.on('click', '.show-detail', function () 
    {   
            $(".right-sidebar").slideDown(50).addClass("shw-rside");

            var id      = $(this).data('row-id');
            var url     = "{{ route('staffing_company.job-applications.show',':id') }}";
            url       = url.replace(':id', id);

            $.easyAjax({
                type: 'GET',
                url: url,
                success: function (response) {
                    if (response.status == "success") {
                        $('#right-sidebar-content').html(response.view);
                    }
                }
            });
        }); 
    
	table.on('click', '.show-feedback', function () 
    {   
		$('#intervied_feedback_toggle').trigger('click');
    	var id      	=	$(this).data('row-id');
        var url     	=	"{{ route('staffing_company.interview-schedule.showfeedback',':id') }}";
        url       		=	url.replace(':id', id);
		var baseurl    	= "{{ asset('assets/images/Loading2.gif') }}"; 
        $.ajax({
                	type	: 'GET',
                	url		: url,
					beforeSend: function() {
                                  $('#load_interview_feedback').html('<img src="'+baseurl+'" height="35px" width="100%">');
                    },
                	success	: function (response) {
                    if (response.status == "success") {
                        $('#load_interview_feedback').html(response.view);
                    }
                }
            });
		 
	  	  
	     
    }); 
		
	
    $('body').on('click', '.ti-close', function () {
      $("body").removeClass("control-sidebar-slide-open");
    })    

        // Handle click on "Select all" control
        $('#example-select-all').on('click', function(){
            // Check/uncheck all checkboxes in the table
            $('input[type="checkbox"]').prop('checked', this.checked);
        });

        // Employee Response on schedule
        $('#changeMultipleStatus').on('click', function(){
            var msg;
            var status = $('#statusMultiple').val();
            var selectedArray = [];
            $('.checkboxes:checked').each(function(){
                selectedArray.push($(this).val());
            });
            if(selectedArray.length > 0){
            swal({
          title       : "@lang('errors.askForCandidateEmail')",
          text        : msg,
          type        : "info",
          showCancelButton  : true,
          confirmButtonColor  : "#0c19dd",
          confirmButtonText : "@lang('app.yes')",
          cancelButtonText  : "@lang('app.no')",
          closeOnConfirm    : true,
          closeOnCancel   : true
            }, function (isConfirm) {
                if (isConfirm) {
                    changeMultipleStatus(status , 'yes')
                }
                else{
                    changeMultipleStatus(status , 'no')
                }
            });
            }else{
                $.showToastr('Please check atleast one checkbox', "error" );
                // alert('Please check atleast one checkbox');
            }
        });

        function changeMultipleStatus(status, mailToCandidate){
            var selectedArray = [];
            $('.checkboxes:checked').each(function(){
                selectedArray.push($(this).val());
            });
            var token = "{{ csrf_token() }}";
            var url = "{{ route('staffing_company.interview-schedule.change-status-multiple') }}";
            $.easyAjax({
              url     : url,
              type    : "POST",
              data    : {'_token': token, "id": selectedArray, "status": status,'mailToCandidate': mailToCandidate},
              container : '#myTable',
              success   : function (response) {
                                  $.unblockUI();
                                  table._fnDraw();
                                }
                  });
        }

        // Edit Data
        $('body').on('click', '.edit-data', function(){
            var id    = $(this).data('row-id');
            var url   = "{{ route('staffing_company.interview-schedule.edit',':id') }}";
            url     = url.replace(':id', id);
            $('#modelHeading').html('Schedule');
            $('#scheduleDetailModal').modal('hide');
            $.ajaxModal('#scheduleDetailModal', url);
        });
        // View Data
        $('body').on('click', '.view-data', function(){
            var id    = $(this).data('row-id');
            var url   = "{{ route('staffing_company.interview-schedule.show',':id') }}?table=yes";
            url     = url.replace(':id', id);
            $('#modelHeading').html('Schedule');
            $('#scheduleDetailModal').modal('hide');
            $.ajaxModal('#scheduleDetailModal', url);
        });

        // Delete Data
    
        table.on('click', '.sa-params', function () 
    	{
    
      
           var id = $(this).data('row-id');
        swal({
          title       : "@lang('errors.areYouSure')",
          text        : "@lang('errors.deleteWarning')",
          type        : "warning",
          showCancelButton  : true,
          confirmButtonColor  : "#DD6B55",
          confirmButtonText : "@lang('app.delete')",
          cancelButtonText  : "@lang('app.cancel')", 
          closeOnConfirm    : true,
          closeOnCancel   : true
            }, function(isConfirm){
                if (isConfirm) {
                    var url     = "{{ route('staffing_company.interview-schedule.destroy',':id') }}";
                    url       = url.replace(':id', id);
                    var token = "{{ csrf_token() }}";
                    $.easyAjax({
                        type: 'POST',
                        url: url,
                        data: {'_token': token, '_method': 'DELETE'},
                        success: function (response) {
                            if (response.status == "success") {
                                $.unblockUI();
                                table._fnDraw();
                            }
                        }
                    });
                }
            });
      
      
        });
    
     
        // Filte Toggle
        $('.toggle-filter').click(function () {
            $('#ticket-filters').toggle('slide');
        })

        // Apply Filter
        $('#apply-filters').click(function () {
            loadTable();
        });

        // Reset Filters
        $('#reset-filters').click(function () {
            $('#filter-form')[0].reset();
            $('#status').val('all');
            $('#status').select2();
            $('#applicationID').val('all');
            $('#applicationID').select2();
            loadTable();
        })

    
    
    

    function updateFeedbackStatus(feedback_id, interview_id, application_id)
    {
      let set_feedback_id     = feedback_id;
      let set_interview_id    = interview_id;
      let set_application_id    = application_id;
      
      if(set_feedback_id == 3)
      {
          if (typeof scheduleDate === "undefined") {
            scheduleDate = '';
          }
          var url = '{{ route('staffing_company.interview-schedule.create_status')}}?application_id=' + set_application_id+'&interview_id='+set_interview_id+'&feedback_id='+set_feedback_id ;
          $('#modelHeading_feedback').html('Schedule');
          $.ajaxModal('#scheduleDetailModal_feedback', url);
        
          //$('#feebdack').modal('show'); 
      }
      
      if(set_feedback_id == 4)
      {
      
        if (typeof scheduleDate === "undefined") {
          scheduleDate = '';
        }
        var url = '{{ route('staffing_company.interview-schedule.create_status')}}?application_id=' + set_application_id+'&interview_id='+set_interview_id+'&feedback_id='+set_feedback_id ;
        $('#modelHeading').html('Schedule');
        $.ajaxModal('#scheduleDetailModal', url);
      
      }
      
      if((set_feedback_id == 1) || (set_feedback_id == 2))
      {
          var clientId    = 0;
          var url       = "{{ route('staffing_company.interview-schedule.updateFeedbackStatus',':clientId') }}";
                    url         = url.replace(':clientId', clientId);
          var baseurl     = "{{ asset('assets/images/AjaxLoader.gif') }}"; 
                    var token       = "{{ csrf_token() }}";
          
          
             $.easyAjax({
                                type: 'POST',
                                url: url,
                                data: {'_token': token, '_method': 'POST', 'status_id' : status_id, 'interview_id':interview_id, 'job_application_id': job_application_id},
                    beforeSend: function() {
                                  $('.clientStatusLoader').html('<img src="'+baseurl+'" height="30px" width="30px">');
                    },
                                success: function (response) {                                              
                      //update
                    },
                    complete: function (data) {
                        //$('.clientStatusLoader').html('');
                     }
                          });
          
          
          
          
     }      
  }
    
    function updateAvailableStatus(status_id, interview_id, job_application_id, className)
    {         
      var clientId    = 0;
      var url       = "{{ route('staffing_company.interview-schedule.updateAvailableStatus',':clientId') }}";
      url         = url.replace(':clientId', clientId);
      var baseurl     = "{{ asset('assets/images/AjaxLoader.gif') }}";        
      var token       = "{{ csrf_token() }}";
      
    
         $.easyAjax({
                type: 'POST',
                url: url,
                data: {'_token': token, '_method': 'POST', 'status_id' : status_id, 'interview_id':interview_id, 'job_application_id': job_application_id},
                beforeSend: function() {
                              $('.clientStatusLoader').html('<img src="'+baseurl+'" height="30px" width="30px">');
                },
                success: function (response) {
         
                  
                },
                complete: function (data) {
                    //$('.clientStatusLoader').html('');
                 }
            });       
              
    }
    
     table.on('change', '.updateAvailableStatus', function () 
    {
    
      //this.value
      var status_id         = this.value;
      var interview_id      = $(this).attr("data-interview_id");
      var job_application_id    = $(this).attr("data-application");
      var currentClass      = $(this).attr('class');
            
      var clientId        = 0;
      var url           = "{{ route('staffing_company.interview-schedule.updateAvailableStatus',':clientId') }}";
      url             = url.replace(':clientId', clientId);     
      var baseurl         = "{{ asset('assets/images/AjaxLoader.gif') }}";      
      var token           = "{{ csrf_token() }}";
          
        
             $.easyAjax({
                                type: 'POST',
                                url: url,
                                data: {'_token': token, '_method': 'POST', 'status_id' : status_id, 'interview_id':interview_id, 'job_application_id': job_application_id},
                    beforeSend: function() {
                                  $('.clientStatusLoader').html('<img src="'+baseurl+'" height="30px" width="30px">');
                    },
                                success: function (response) {
                         
                      
                    },
                    complete: function (data) {
                        //$('.clientStatusLoader').html('');
                     }
                          });
        
        $(this).removeClass('available-bg'); 

        $(this).removeClass('not-available-bg'); 
        
        $(this).removeClass('cancel-bg'); 
        
        $(this).removeClass('not-intrested'); 
        
        $(this).removeClass('bg-whites'); 
        
        if(status_id == 1)
        {     
          $(this).addClass('available-bg');         
        }

        else if(status_id == 2)
        { 
         $(this).addClass('not-available-bg'); 
        }

        else if(status_id == 3)
        { 
          $(this).addClass('cancel-bg'); 
        }

        else if(status_id == 4)
        { 
          $(this).addClass('not-intrested'); 
        }

        else
        {
          $(this).addClass('bg-whites');  
        } 
    });
        
     table.on('change', '.updateFeedbackStatus', function () 
    {
    
      //this.value
      var status_id         = this.value;
      var interview_id      = $(this).attr("data-interview_id");
      var job_application_id    = $(this).attr("data-application");
      var currentClass      = $(this).attr('class');
      
      let set_feedback_id     = status_id;
      let set_interview_id    = interview_id;
      let set_application_id    = job_application_id;
      
      if(set_feedback_id == 3)
      {
          if (typeof scheduleDate === "undefined") {
            scheduleDate = '';
          }
          var url = '{{ route('staffing_company.interview-schedule.create_feedbackfrm')}}?application_id=' + set_application_id+'&interview_id='+set_interview_id+'&feedback_id='+set_feedback_id ;
          $('#modelHeading_feedback').html('Schedule');
          $.ajaxModal('#scheduleDetailModal_feedback', url);
          
        //$('#feebdack').modal('show'); 
      }
      
      if(set_feedback_id == 4)
      {
      
        
        if (typeof scheduleDate === "undefined") {
          scheduleDate = '';
        }
        var url = '{{ route('staffing_company.interview-schedule.create_status')}}?application_id=' + set_application_id+'&interview_id='+set_interview_id+'&feedback_id='+set_feedback_id ;
        $('#modelHeading').html('Schedule');
        $.ajaxModal('#scheduleDetailModal', url);
      
      }
      
          var clientId    = 0;
          var url       = "{{ route('staffing_company.interview-schedule.updateFeedbackStatus',':clientId') }}";
                    url         = url.replace(':clientId', clientId);

          var baseurl     = "{{ asset('assets/images/AjaxLoader.gif') }}"; 
        
                    var token       = "{{ csrf_token() }}";
          
        
            
        
        if((set_feedback_id== 0) || (set_feedback_id== 1) || (set_feedback_id== 2) )
        {
           
          
           $.easyAjax({
                                type: 'POST',
                                url: url,
                                data: {'_token': token, '_method': 'POST', 'status_id' : status_id, 'interview_id':interview_id, 'job_application_id': job_application_id},
                    beforeSend: function() {
                                  $('.clientStatusLoader').html('<img src="'+baseurl+'" height="30px" width="30px">');
                    },
                                success: function (response) {
                         
                      
                    },
                    complete: function (data) {
                        //$('.clientStatusLoader').html('');
                     }
                          });         
        }
        
        
        
        $(this).removeClass('pending-bg'); 
        $(this).removeClass('shortlisted-bg'); 
        $(this).removeClass('rejected-bg'); 
        $(this).removeClass('feedback-bg'); 
        $(this).removeClass('rescheduled-bg');        
        if(status_id == 1){     
          $(this).addClass('shortlisted-bg');         
        }else if(status_id == 2){ 
          $(this).addClass('rejected-bg'); 
        }else if(status_id == 3){ 
          $(this).addClass('feedback-bg'); 
        }else if(status_id == 4){ 
          $(this).addClass('rescheduled-bg'); 
        }else{
          $(this).addClass('pending-bg');   
        } 
    });
            
     function exportExcel()
     {
    
        var table         = $('#myTable').dataTable();
        var length        = table.fnGetData().length;
        
        var oSettings       = table.fnSettings();
        var start           = oSettings._iDisplayStart;
        
        var url         = '{{ route('staffing_company.interview-schedule.export', [':start' , ':length']) }}';
        url           = url.replace(':start', start);
        url           = url.replace(':length', length);
        window.location.href  = url;
      
           }
        

	
		$('body').on('click', '#app_interview_schedule_reschedule', function(){
				
				 
				
				$("#interview_feedback .close").click()	   
	   	
				var id    		=	$(this).attr('rel');
				var url 		=	"{{ route('staffing_company.job-applications.create-schedule',':id') }}?reschedule=1";
				url     		=	url.replace(':id', id);		   
				var baseurl    	=	"{{ asset('assets/images/Loading2.gif') }}"; 
				$.ajax({
								type  		: 'GET',
								url   		: url,
								beforeSend	: function() {
															$('#app_interview_schedule_content').html('<img src="'+baseurl+'" height="35px" width="100%">');
								},
								success : function (response) 
								{
									$('#app_interview_schedule_content').html(response.view);		
								}
					});
	    });	
	
    </script>
@endpush 