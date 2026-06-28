@extends('layouts.service_company_layout')

@push('head-script')
<link rel="stylesheet" href="//cdn.datatables.net/fixedheader/3.1.5/css/fixedHeader.bootstrap.min.css">
<link rel="stylesheet" href="//cdn.datatables.net/responsive/2.2.3/css/responsive.bootstrap.min.css">
<!-- <link rel="stylesheet" href="{{ asset('assets/node_modules/html5-editor/bootstrap-wysihtml5.css') }}"> -->
<link rel="stylesheet" href="{{ asset('assets/node_modules/html5-editor/bootstrap-wysihtml5.css') }}<?php echo TM_VERSION; ?>">
<link rel="stylesheet" href="{{ asset('assets/node_modules/bootstrap-datepicker/bootstrap-datepicker.min.css') }}<?php echo TM_VERSION; ?>">
<link rel="stylesheet" href="{{ asset('assets/node_modules/multiselect/css/multi-select.css') }}<?php echo TM_VERSION; ?>">
<link rel="stylesheet" href="{{ asset('assets/plugins/iCheck/all.css') }}<?php echo TM_VERSION; ?>">
<link href="{{ asset('assets/dist/css/adminlte.min.css') }}<?php echo TM_VERSION; ?>" rel="stylesheet" type="text/css" />
<link href="{{ asset('css/custom.css') }}<?php echo TM_VERSION; ?>" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/icons/themify-icons/themify-icons.css') }}<?php echo TM_VERSION; ?>" rel="stylesheet" type="text/css" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/1.12.4/jquery.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
<script>
$(document).on('click', function (e)
{
	var flag	=	0;
	if ($('.control-sidebar-slide-open').is(':visible'))
	{
		if ($(e.target).closest("#right-sidebar-content").length === 0) {
				flag	=	1;
		}
	}			
	if ($('#scheduleDetailModal').is(':visible'))
	{
				flag = 0;
	}
	if(flag == 1){
		$("body").removeClass("control-sidebar-slide-open");	
	}
});
</script>
<style>
.dropdown-menu
{
	padding: 0em .4em;
	border-color:lightgray;
	border-radius:0px;	
}
/*input
{
    border:1px solid lightgray;
    padding:.1em .2em;
    margin: .1em;
}*/
input
{
    border: none;
    border-bottom: 1px dashed #b1adad;
    padding: .1em .2em;
    margin: .1em;
    background: transparent;
    /*cursor: pointer;*/
}
/*Indexing Dropdown positining*/
#myTable_length label
{
    font-weight: normal;
    text-align: left;
    white-space: nowrap;
    position: absolute;
    margin-top: -8%;
}
/*Media Query*/
@media only screen and (min-width: 1440px) and (max-width: 2560px)
{
	#myTable_length label
	{
	  margin-top: -4%;
	}
}
@media only screen and (min-width: 1080px) and (max-width: 1920px)
{
	#myTable_length label
	{
    	margin-top: -5.5%;
	}
}
@media only screen and (min-width: 1050px) and (max-width: 1680px)
{
	#myTable_length label
	{
		margin-top: -6.1%;
	}
}
@media only screen and (min-width: 864px) and (max-width: 1536px)
{
	#myTable_length label
	{
		margin-top: -6.9%;
	}
}
@media only screen and (min-width: 900px) and (max-width: 1440px)
{
	#myTable_length label
	{
		margin-top: -7.3%;
	}
}
@media only screen and (min-width: 900px) and (max-width: 1600px)
{
	#myTable_length label
	{
		margin-top: -6.5%;
	}
}
@media only screen and (min-width: 800px) and (max-width: 1280px)
{
	#myTable_length label
	{
		margin-top: -8.7%;
	}
}
@media only screen and (min-width: 768px) and (max-width: 1366px)
{
	#myTable_length label
	{
		margin-top: -7.9%;
	}
}
/*Indexing Dropdown positining End*/

/*Checkbox css*/
.checkbox input[type="checkbox"]
{
    position: inherit;
    display: none;
}
/*Checkbox css end*/

</style>
@endpush

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="modal fade bs-modal-md in" id="scheduleDetailModal_vis" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" id="modal-data-application">
          <div class="modal-content">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
              <span class="caption-subject font-red-sunglo bold uppercase" id="modelHeading_vis"></span> </div>
            <div class="modal-body"> Loading... </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-light-danger btn-sm" data-dismiss="modal"> <i class="fa fa-circle"></i>&nbsp;Close</button>
              <button type="button" class="btn btn-light-primary btn-sm"> <i class="fa fa-refresh"></i>&nbsp;Update</button>
            </div>
          </div>
          <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
      </div>
      <div class="card-body"> @section('create-button')
        <div class="row align-items-center">
          <div class="col-md-6 text-left">
            <div class="dropdown dropdown-inline invisible">
              <button class="dropbtn bg-light dropdown-toggle dropbtn-border"> 10 </button>
              <div class="dropdown-content">
                <ul class="navi">
                  <li class="navi-item"> <a href="javascript:void(0);" class="navi-link custom-width"> <span class="navi-text">20</span> </a> </li>
                  <li class="navi-item"> <a href="javascript:void(0);" class="navi-link custom-width"> <span class="navi-text">30</span> </a> </li>
                </ul>
              </div>
            </div>
            <!-- Page Title -->
            <span class="card-title pt-2 pb-2">Offer Tracker</span>
            <!-- Page Title End -->
          </div>
          <div class="col-md-6 text-right searchbar-parent">
            <div class="input-group mr-1 searchbar-child" style="">
              <input type="text" name="inputSearch" id="inputSearch" class="form-control" placeholder="Search..." style="padding: 0.3em .5em;">
            </div>
            <a onclick="exportExcel()">
            <button class="btn btn-light-primary btn-sm px-2 py-1 mr-1" type="button"> <i class="fa fa-download fa-sm"></i> </button>
            </a> <a href="javascript:void(0);" id="column_visibility_control_btn" class="btn btn-light-dark btn-sm px-2 py-1"> <i class="fa fa-filter fa-sm"></i> </a>
            <!-- <a href="javascript:void(0);">
					<label class="checkbox checkbox-primary">
						<input type="checkbox" name="Checkboxes" checked="checked" />
						<span></span>
					</label>
				</a> -->
          </div>
        </div>
        @endsection
        <div class="table-responsive">
          <table id="myTable" class="table table-bordered table-striped table-hover table-checkable">
            <thead>
              <tr class="table-bg-custom">
                <th class="table_id">ID</th>
                <th>Applicants</th>
                <!-- <th>Position</th> -->
                <th>Requisition</th>
                <th>Client</th>
                <th> <div class="tooltip font-weight-700">Loc<span class="tooltiptext font-weight-500">Location</span></div></th>
                <!-- <th class="text-center"><i class="text-center fa fa-map-marker"></i></th> -->
                <th> <div class="tooltip font-weight-700">REP<span class="tooltiptext font-weight-500">Representative</span></div></th>
                <th class="custom-bdr-right"> <div class="tooltip font-weight-700">P.CTC<span class="tooltiptext font-weight-500">Proposed CTC</span></div></th>
                <th> <div class="tooltip font-weight-700">AOF<span class="tooltiptext font-weight-500">Any Offer</span></div></th>
                <th> <div class="tooltip font-weight-700">DOC<span class="tooltiptext font-weight-500">Document</span></div></th>
                <th><div class="tooltip font-weight-700">SFA<span class="tooltiptext font-weight-500">Submission For Approval</span></div></th>
                <th> <div class="tooltip font-weight-700">OAP<span class="tooltiptext font-weight-500">Offer Approved</span></div></th>
                <th> <div class="tooltip font-weight-700">ORE<span class="tooltiptext font-weight-500">Offer Release</span></div></th>
                <th> <div class="tooltip font-weight-700">OAC<span class="tooltiptext font-weight-500">Offer Acceptance</span></div></th>
                <th> <div class="tooltip font-weight-700">JOI<span class="tooltiptext font-weight-500">Joined</span></div></th>
                <th> <div class="tooltip font-weight-700">J.Date<span class="tooltiptext font-weight-500">Joined Date</span></div></th>
              </tr>
            </thead>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
<div class="modal fade bs-modal-lg in" id="scheduleDetailModal_cvc" role="dialog" aria-labelledby="myModalLabel"
   aria-hidden="true">
  <div class="modal-dialog modal-lg" id="modal-data-application">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
        <span class="caption-subject font-red-sunglo bold uppercase" id="modelHeading_cvc"></span> </div>
      <div class="modal-body"> Loading... </div>
      <div class="modal-footer"> </div>
    </div>
  </div>
</div>
@endsection

@push('footer-script')
<link rel="stylesheet" type="text/css" href="{{ asset('css/jquery.datetimepicker.min.css') }}<?php echo TM_VERSION; ?>"/>
<script src="{{ asset('js/jquery.datetimepicker.js') }}<?php echo TM_VERSION; ?>"></script>
<script type="text/javascript">
	$(document).ready(function () {
		$('#datetimepicker').datetimepicker({
			format:'d-M-Y H:m:s'
				 
		});
	});
</script>
<script src="{{ asset('assets/node_modules/select2/dist/js/select2.full.min.js') }}<?php echo TM_VERSION; ?>" type="text/javascript"></script>
<script src="{{ asset('assets/node_modules/bootstrap-select/bootstrap-select.min.js') }}<?php echo TM_VERSION; ?>" type="text/javascript"></script>
<script src="//cdn.datatables.net/fixedheader/3.1.5/js/dataTables.fixedHeader.min.js"></script>
<script src="//cdn.datatables.net/responsive/2.2.3/js/dataTables.responsive.min.js"></script>
<script src="//cdn.datatables.net/responsive/2.2.3/js/responsive.bootstrap.min.js"></script>
<script src="{{ asset('assets/node_modules/moment/moment.js') }}<?php echo TM_VERSION; ?>" type="text/javascript"></script>
<script src="{{ asset('assets/node_modules/bootstrap-datepicker/bootstrap-datepicker.min.js') }}<?php echo TM_VERSION; ?>" type="text/javascript"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.css" rel="stylesheet" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.js"></script>
<script type="text/javascript">

$(document).ready(function() {

	
	$('#inputSearch').keyup(function() {
		//alert("call");
		//table._fnDraw();
		tableLoad('filter');     
	}); 
});


$( document ).ready(function() {
   
    $('body').on('click', '.ti-close', function () {
			$("body").removeClass("control-sidebar-slide-open");
		})
		
});
		
		
		$('#column_visibility_control_btn').click(function(){
			//var id    	= $('#jobid').val();
			var id        = 12;
			if(typeof scheduleDate === "undefined") {
				scheduleDate  = '';
			}
			var url      	 = "{{ route('service_company.offer.load_visibility_control',':id') }}";
			url         	 = url.replace(':id', id);
			$('#modelHeading_cvc').html('Schedule');
			$.ajaxModal('#scheduleDetailModal_cvc', url);
		});
		
	  
	  
        var table;
        tableLoad('load');

        function tableLoad() 
		{
            table = $('#myTable').dataTable({
                responsive: true,
                processing: true,
                serverSide: true,
                destroy: true,
                stateSave: true, 
                "bInfo": false,
               "bLengthChange": true,
               oLanguage: {
                   sLengthMenu: "_MENU_",
                },
				"bFilter": false,
				//dom			: 'Bfrtip',
				colReorder	: true,     
				"autoWidth": false,

				buttons		: [{
									//collectionTitle	: 'Visibility Control',
									text			:"Visibility Control",
									extend			: 'colvis',
									collectionLayout: 'two-column',
									colReorder		: true,
									columnText		: function ( dt, idx, title ) {
														return (idx+1)+': '+title;
													},
									dropup			: true
                    		}],
								
createdRow: function ( row, data, index ) {
		$('td', row).eq(6).addClass('custom-bdr-right');
},  								
						
                //ajax: '{!! route('service_company.offer.data') !!}',
				
				ajax: {'url' : '{!! route('service_company.offer.data') !!}',
				"data": function ( d )
					{
						d.search = $('#inputSearch').val()
					}
				},
		
				
				
                language: {
                    "url": "<?php echo __("app.datatable") ?>"
                },
                "fnDrawCallback": function (oSettings) {
                    $("body").tooltip({
                        selector: '[data-toggle="tooltip"]'
                    });
					$('.custom_calendar').datetimepicker({	format	:	'd-M-Y H:m:s' });
					


/*const checkboxes 	=	'input[type=checkbox]',
radios 				=	'input[type=radio]';    
lc_switch(checkboxes);
lc_switch(radios, {
						compact_mode : true,
						on_color : 'teal',
				}); */
				
				
	
					
                },
                columns: [
        			{data: 'id', name:'id', width:"5", orderable: true, searchable: false},
        			{data: 'full_name', name: 'job_applications.full_name', width: '20%', orderable: true, searchable: false},
					{data: 'title', name: 'jobs.title', width: '25%'},
					{data: 'client_name', name: 'client_name', width: '5%'},					
					{data: 'client_location', name: 'client_location', width: '5%'},
					{data: 'hiring_manager', name: 'hiring_manager', width: '5%'},				
					{data: 'proposed_ctc', name: 'jproposed_ctc', width: '5%', orderable: false},
					{data: 'any_offer', name: 'any_offer', width: '3%', orderable: false},
					{data: 'document', name: 'document', width: '3%', orderable: false},
					{data: 'sub_for_approval', name: 'sub_for_approval', width: '3%', orderable: false},
					{data: 'offer_approved', name: 'offer_approved', width: '3%', orderable: false},
					{data: 'offer_release', name: 'offer_release', width: '3%', orderable: false},
					{data: 'offer_acceptance', name: 'offer_acceptance', width: '3%', orderable: false},
					{data: 'joined', name: 'joined', width: '3%', orderable: false},         
					{data: 'joined_date', name: 'joined_date', width: '9%', orderable: false} 
					
                ]
            });
            new $.fn.dataTable.FixedHeader(table);
        }
		
		
		table.on('click', '.show-detail', function () 
		{			
			
				$(".right-sidebar").slideDown(50).addClass("shw-rside");
				var id 			= $(this).data('row-id');
				
				//alert(id);
				//return false;
				
				var url 		= "{{ route('service_company.job-applications.show',':id') }}";
				url 			= url.replace(':id', id);
				$.easyAjax({
								type	: 'GET',
								url		: url,
								success	: function (response) {
																	if (response.status == "success") 
																	{
																			$('#right-sidebar-content').html(response.view);
																	}
															  }
				});
									
        }); 
		
		
		
    </script>
<script src="{{ asset('js/lc_switch.js') }}<?php echo TM_VERSION; ?>" type="text/javascript"></script>
<script type="text/javascript">
    const checkboxes 	=	'input[type=checkbox]',
          radios 		=	'input[type=radio]';    
    // initialization    
    lc_switch(checkboxes);
    lc_switch(radios, {
        					compact_mode : true,
        					on_color : 'teal',
    }); 
      	     
</script>
<script src="{{ asset('js/jquery.mloading.js') }}<?php echo TM_VERSION; ?>"></script>
<link rel="stylesheet" href="{{ asset('css/jquery.mloading.css') }}<?php echo TM_VERSION; ?>">
<script>    
    
        $('.custom_calendar').datepicker({
				format			: 'dd-mm-yyyy',
				showOn			: "button",
				buttonImage		: "{{ asset('img/calendar.png') }}", 
				buttonImageOnly	: true,
				buttonText		: "Select date",
				 onSelect: function(dateText, inst) {
														var date = $(this).val();
														var time = $('#time').val();
														alert('on select triggered');
														//$("#start").val(date + time.toString(' HH:mm').toString());
			
				}
	 
        })
       
	   function updatecalendar()
	   {
	   		alert("call"); 
	   }
	   
	   
	   function update_offer(application_id, current_value, event_name)
	   {
				
	   			if( (event_name ==2) || (event_name ==3) || (event_name ==4) || (event_name ==5) || (event_name ==6) || (event_name ==7) || (event_name ==8)  )
				{
						$("body").mLoading();
						if(event_name == 2){
												if($("#any_offer"+application_id).prop('checked') == true){
														current_value = 1;
												}else{
														current_value = 0;	
												}
						}
						
						if(event_name == 3){
												if($("#document"+application_id).prop('checked') == true){
														current_value = 1;
												}else{
														current_value = 0;	
												}
						}
						
						if(event_name == 4){
												if($("#sub_for_approval"+application_id).prop('checked') == true){
														current_value = 1;
												}else{
														current_value = 0;	
												}
						}
						
						if(event_name == 5){
												if($("#offer_approved"+application_id).prop('checked') == true){
														current_value = 1;
												}else{
														current_value = 0;	
												}
						}
						
						if(event_name == 6){
												if($("#offer_release"+application_id).prop('checked') == true){
														current_value = 1;
												}else{
														current_value = 0;	
												}
						}
						
						if(event_name == 7){
												if($("#offer_acceptance"+application_id).prop('checked') == true){
														current_value = 1;
												}else{
														current_value = 0;	
												}
						}
						
						if(event_name == 8){
												if($("#joined"+application_id).prop('checked') == true){
														current_value = 1;
												}else{
														current_value = 0;	
												}
						}
						
						
				}	
	   			update_application(application_id, current_value, event_name);
	   }
	   
	  function update_application(application_id, current_value, event_name)
	   {
	   						
				var candidate_id 		= application_id;
				var process 			= event_name;
				var cvalue 				= current_value;
									
				var url 				= '{!! route('service_company.offer.update_application') !!}';					
				//url 					= url.replace(':clientId', clientId);					
				var baseurl 			= '{{ asset('assets/images/AjaxLoader.gif') }}'; 									
				var token 				= '{{ csrf_token() }}';
				$.easyAjax({
   
                       				type	: 'POST',
                       				url		: url,
                       				data	: {'_token': token, '_method': 'POST', 'candidate_id' : candidate_id, 'process' : process, 'cvalue' : cvalue},
   									beforeSend: function() {
   													//$('.clientStatusLoader').html('<img src="'+baseurl+'" height="30px" width="30px">');
   									},
                       				success: function (response) {   
										//$('#jobs').html(response.data);
										//$('.clientStatusLoader').html('');
									},   
   									complete: function (data) {
																	if( (process ==2) || (process ==3) || (process ==4) || (process ==5) || (process ==6) || (process ==7) || (process ==8)  )
																	{
   																		setTimeout(function(){$("body").mLoading('hide');}, 500);
																	}
   						 			}
                		});
				  				   	
	   }
	   
</script>
@endpush