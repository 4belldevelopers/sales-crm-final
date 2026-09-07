@extends('layouts.staffing_company_layout')

@push('head-script')
<link rel="stylesheet" href="//cdn.datatables.net/fixedheader/3.1.5/css/fixedHeader.bootstrap.min.css">
<link rel="stylesheet" href="//cdn.datatables.net/responsive/2.2.3/css/responsive.bootstrap.min.css">
<link rel="stylesheet" href="{{ asset('assets/node_modules/html5-editor/bootstrap-wysihtml5.css') }}">
<link rel="stylesheet" href="{{ asset('assets/node_modules/bootstrap-datepicker/bootstrap-datepicker.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/node_modules/multiselect/css/multi-select.css') }}">
<link rel="stylesheet" href="{{ asset('assets/plugins/iCheck/all.css') }}">
<link href="{{ asset('assets/dist/css/adminlte.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('css/custom.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/icons/themify-icons/themify-icons.css') }}" rel="stylesheet" type="text/css" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/1.12.4/jquery.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
<script>
$(document).on('click', function (e) {
		var flag	=	0;
		if ($('.control-sidebar-slide-open').is(':visible')){
			if ($(e.target).closest("#right-sidebar-content").length === 0) {
					flag	=	1;
			}
		}			
		if ($('#scheduleDetailModal').is(':visible')){
					flag = 0;
		}
		if(flag == 1){
			$("body").removeClass("control-sidebar-slide-open");	
		}
});
</script>
<style>
.help-block{color: red;}

#myTable {
    overflow-x: scroll;
    max-width: 40%;
    display: block;
    white-space: nowrap;
}
.custm-mt-top{margin-top: -1.5em!important;}
.topbar {
    position: relative;
    right: 0%!important;
}
#myTable_length {
    position: absolute;
    left: 0%;
    margin-top: -6%;
}
</style>
@endpush



@section('content')
<?php 
		//echo '<pre>'; print_r($clients);exit; 
?>
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
              <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal">Close</button>
              <button type="button" class="btn btn-success btn-sm">Update</button>
            </div>
          </div>
          <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
      </div>
      <div class="card-body">
        <div class="row">
          <div class="col-md-6 text-right">
            <div class="input-group mr-2 mt-2" style="width: fit-content;display: inline-flex;">
              <!--<input type="text" name="inputSearch" id="inputSearch" class="form-control" placeholder="Search..">-->
            </div>
          </div>
        </div>
        <div class="row clearfix">
          <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
          <link rel="stylesheet" href="/resources/demos/style.css">
          <script>
  $( function() {
    $( "#sortable" ).sortable();
    $( "#sortable" ).disableSelection();
  } );
  </script>
          <?php 
		$candidate_field = DB::table('custom_candidate_display_field')
			->select('*')
				->where('user_id' , auth()->user()->id)
				->where('display_status' , 1)
				->orderBy('display_order', 'ASC')
				->get();
		
										
?>
        </div>
        <form action="" id="filter-form" class="mb-20">
          <table id="myTable" class="table table-bordered table-hover table responsive table-responsive display nowrap" style="overflow-x:auto;">
            <thead>
              <tr class="table-bg-custom">
                <th >#</th>
                <th >Date </th>
                <th > Applicant </th>
                <th> All Client </th>
                <th> All Jobs </th>
                <th>All Source </th>
                <th> Email </th>
                <th> Phone </th>
                <th> CTC </th>
                <th> ECTC </th>
                <th> EXP </th>
                <th> R.EXP </th>
                <th> Passport </th>
                <th>Notice Period </th>
                <th class="custm-mt-top"> Curr Title </th>
                <th class="custm-mt-top"> Curr Co </th>
                <th class="custm-mt-top"> Curr Loc </th>
                <th class="custm-mt-top">Education</th>
                <th>Interview Process </th>
                <!-- <th>Interview Status Level </th> -->
                <th>Status </th>
                <th class="text-center">CV</th>
                <th> @lang('app.action') </th>
              </tr>
            </thead>
          </table>
        </form>
      </div>
    </div>
  </div>
</div>
<div class="modal fade bs-modal-md in" id="scheduleDetailModal" role="dialog" aria-labelledby="myModalLabel"
         aria-hidden="true">
  <div class="modal-dialog modal-lg" id="modal-data-application">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
        <span class="caption-subject font-red-sunglo bold uppercase" id="modelHeading"></span> </div>
      <div class="modal-body"> Loading... </div>
      <div class="modal-footer">
        <button type="button" class="btn default" data-dismiss="modal">Close</button>
        <button type="button" class="btn blue">Save changes</button>
      </div>
    </div>
  </div>
</div>
<div class="modal fade bs-modal-lg in" id="CandidateDetailModalStatus" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" id="modal-data-application">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
        <span class="caption-subject font-red-sunglo bold uppercase" id="CandidateDetailModalStatusData"></span> </div>
    </div>
  </div>
</div>
<div class="modal fade bs-modal-exl in" id="moveApplicationModal" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" id="modal-data-application">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
        <span class="caption-subject font-red-sunglo bold uppercase" id="moveApplicationmodelHeading"></span> </div>
      <div class="modal-body"> Loading... </div>
      <div class="modal-footer"> </div>
    </div>
  </div>
</div>
<div class="modal fade bs-modal-exl in" id="copyApplicationModal" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" id="modal-data-application">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
        <span class="caption-subject font-red-sunglo bold uppercase" id="copyApplicationmodelHeading"></span> </div>
      <div class="modal-body"> Loading... </div>
      <div class="modal-footer"> </div>
    </div>
  </div>
</div>
@endsection

@push('footer-script')
<script src="{{ asset('js/dataTables.fixedHeader.min.js') }}"></script>
<!--<script src="{{ asset('js/dataTables.responsive.min.js') }}"></script>-->
<script src="{{ asset('js/responsive.bootstrap.min.js') }}"></script>
<script src="{{ asset('js/dataTables.colReorder.min.js') }}"></script>
<!--<script src="{{ asset('js/dataTables.buttons.min.js') }}"></script>-->
<script src="{{ asset('js/buttons.colVis.min.js') }}"></script>
<!--<script src="{{ asset('assets/node_modules/select2/dist/js/select2.full.min.js') }}" type="text/javascript"></script>-->
<!--<script src="{{ asset('assets/node_modules/bootstrap-select/bootstrap-select.min.js') }}" type="text/javascript"></script>-->
<script src="//cdn.datatables.net/buttons/1.6.1/js/dataTables.buttons.min.js"></script>
<!--<script src="{{ asset('assets/node_modules/moment/moment.js') }}" type="text/javascript"></script>-->
<script src="{{ asset('assets/node_modules/multiselect/js/jquery.multi-select.js') }}"></script>
<script src="{{ asset('assets/plugins/iCheck/icheck.min.js') }}"></script>
<script src="{{ asset('assets/node_modules/bootstrap-datepicker/bootstrap-datepicker.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('assets/node_modules/select2/dist/js/select2.full.min.js') }}"type="text/javascript"></script>
<script src="{{ asset('assets/node_modules/bootstrap-select/bootstrap-select.min.js') }}"type="text/javascript"></script>
<script src="{{ asset('assets/node_modules/moment/moment.js') }}" type="text/javascript"></script>
<script src="{{ asset('assets/plugins/calendar/dist/fullcalendar.min.js') }}"></script>
<script src="{{ asset('assets/plugins/calendar/dist/jquery.fullcalendar.js') }}"></script>
<script src="{{ asset('assets/plugins/calendar/dist/locale-all.js') }}"></script>
<script src="{{ asset('js/schedule-calendar.js') }}"></script>
<script>
		
        $('#start-date').datepicker({
            format: 'yyyy-mm-dd'
        })
        $('#end-date').datepicker({
            format: 'yyyy-mm-dd'
        })

        var table;
        tableLoad('load');
        // For select 2
        $(".select2").select2();
        $('#reset-filters').click(function () {
		
			//$('#myTable').DataTable().column('').search('').draw();
			var table = $('#myTable').DataTable();
			
			table.search( '' ).columns().search( '' ).draw();
 
			
            $('#filter-form')[0].reset();
           // $('#filter-form').find('select').val('all').trigger('change');
            tableLoad('load');
        })
        $('#apply-filters').click(function () {
            tableLoad('filter');
        });
		
		
		
		
        function tableLoad(type) 
		{		
			
			//var   clients 			= $('#clients').val();
			var		clients				=	'all';			
			//var jobs 					= $('#jobs').val();
			var		jobs				=	'all';
			//var   source 				= $('#source').val();
			var		source				=	'all';
			//var   search_passport 		= $('#search_passport').val();
			var		search_passport		=	'all';
			//var   search_np_id 		= $('#search_np_id').val();
			var		search_np_id		=	'all';
			//var status 					= $('#status').val();
			var		status				=	'all';
			
			var common_search 			= $('#common_search').val();
			if(common_search == ''){
					 common_search		=	'all';	
			}
						
			
			
            table = $('#myTable').dataTable({
               			   			   			 	
				responsive		: true,
				processing		: true,
				destroy			: true,
				stateSave		: true,
				//colReorder		: true,
				//sScrollXInner	: "110%",
				bJQueryUI		: true,
				
				"scrollX"		: true,
				
				"bFilter"		: false,
				"bInfo"			: false,
				"bLengthChange"	: false,

				oLanguage		: {
									sLengthMenu: "_MENU_",
								   },
							   							   
				buttons: [{
								
                        	//collectionTitle	: 'Visibility Control',
							text			:	"Visibility Control",
                        	extend			:	'colvis',
                        	collectionLayout:	'two-column',
							colReorder		:	true,
							
							columnText		: function ( dt, idx, title ) {
												return (idx+1)+': '+title;
											},
							dropup			: true							
                    	}],
										
                ajax: '{!! route('staffing_company.advsearch.data') !!}?status='+status+'&jobs='+jobs+'&clients='+clients+'&source='+source+'&search_passport='+search_passport+'&search_np_id='+search_np_id
				+'&common_search='+common_search,
							
				
                language: {
                    "url": "<?php echo __("app.datatable") ?>"
                },
                "fnDrawCallback": function (oSettings) {
                    $("body").tooltip({
                        selector: '[data-toggle="tooltip"]'
                    });
                }, 
				"fnRowCallback" : function(nRow, aData, iDisplayIndex){
                	$("td:first", nRow).html(iDisplayIndex +1);
               		return nRow;
            	},
				"initComplete":function( settings, json){
				
					$('.clientStatusLoader').html('');
					$('.jobLoader').html('');
					$('.sourceLoader').html('');
					
					$('.search_np_idLoader').html('');
					$('.search_passportLoader').html('');
					$('.statusLoader').html('');
					
					
				}, 
				 
                //columns: generateDynamicColumn()
				//columns:  $('#hiddenCustFld').val()	
				
				
						
					columns: [
									{data: 'id', name:'id',width: '2%', searchable : false},
									{data: 'created_at', name: 'created_at', width: '0%', orderable: true},
									{data: 'full_name', name: 'full_name', width: '5%', orderable: true},
									{data: 'client_name', name: 'clients.client_name', width: '5%', orderable: false },
									{data: 'title', name: 'jobs.title', width: '15%', orderable: false},
									
									{data: 'posted_by', name: 'users.name', width: '5%', orderable: false},
									{data: 'email', name: 'email', width: '5%', orderable: true},
									{data: 'phone', name: 'phone', width: '5%'},
									{data: 'ctc', name: 'ctc', width: '5%'},
									{data: 'ectc', name: 'ectc', width: '5%'},
									{data: 'total_exp', name: 'total_exp', width: '5%'},
									{data: 'rel_exp', name: 'rel_exp', width: '5%'},
									{data: 'passport', name: 'passport', width: '5%', orderable: false},
									{data: 'np_id', name: 'notice_periods.np_id', width: '5%', orderable: false},
									
									{data: 'current_designation', name: 'current_designation', width: '5%',  orderable : true},
									{data: 'current_company', name: 'current_company', width: '5%', orderable : true},
									{data: 'current_location', name: 'current_location', width: '5%', orderable : true},
									//{data: 'location', name: 'job_locations.location', width: '5%'},
									
									{data: 'qualification_name', name: 'qualification_name', width: '5%', orderable : false},
									
									{data: 'status', name: 'application_status.status', width: '5%', orderable : false},
									{data: 'interview_status_level', name: 'interview_status_level', width: '5%', searchable : false, orderable : false},
									//{data: 'interview_process', name: 'interview_process', width: '5%', orderable : false, searchable : false},
									
									{data: 'resume', name: 'resume', width: '5%', orderable : false, searchable : false},
									{data: 'action', name: 'action', width: '10%', orderable : false}
							 ]
						
            });
			
			
            new $.fn.dataTable.FixedHeader(table);
			
			


        }

        $('body').on('click', '.right-side-toggle', function () {
		$("body").removeClass("control-sidebar-slide-open");
		})
		
		 table.on('click', '.show-detail', function () 
			{
			
            $(".right-sidebar").slideDown(50).addClass("shw-rside");

            var id 			= $(this).data('row-id');
            var url 		= "{{ route('staffing_company.job-applications.show',':id') }}";
            url 			= url.replace(':id', id);
			
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

		$('body').on('click', '.ti-close', function () {
			$("body").removeClass("control-sidebar-slide-open");
		})
		
		$('#column_visibility_control_btn').click(function(){
		
				//alert("call");
				var id 				= 12;			
				if(typeof scheduleDate === "undefined") {
					scheduleDate 	= '';
				}
				var url 			= "{{ route('staffing_company.advsearch.load_visibility_control',':id') }}";
				url 				= url.replace(':id', id);
				$('#modelHeading_vis').html('Schedule');
				$.ajaxModal('#scheduleDetailModal_vis', url);
		});
		
		
		
        $('body').on('click', '.sa-params', function(){
            var id = $(this).data('row-id');
            swal({
					title				: "@lang('errors.areYouSure')",
					text				: "@lang('errors.deleteWarning')",
					type				: "warning",
					showCancelButton	: true,
					confirmButtonColor	: "#DD6B55",
					confirmButtonText	: "@lang('app.delete')",
					cancelButtonText	: "@lang('app.cancel')",
					closeOnConfirm		: true,
					closeOnCancel		: true
				}, function(isConfirm){
                if (isConfirm) {

                    var url 			= "{{ route('staffing_company.advsearch.destroy',':id') }}";
                    url 				= url.replace(':id', id);
                    var token 			= "{{ csrf_token() }}";

                    $.easyAjax({
									type		: 'POST',
									url			: url,
									data		: {'_token': token, '_method': 'DELETE'},
									success		: function (response) {
										if (response.status == "success") {
											$.unblockUI();
			//                              	swal("Deleted!", response.message, "success");
												table._fnDraw();
										}d
                        }
                    });
                }
            });
        });

        $('.toggle-filter').click(function () {
            $('#ticket-filters').toggle('slide');
        });

       

		function updateCandidateStatus(scheduleDate) {
            if (typeof scheduleDate === "undefined") {
                scheduleDate = '';
            }
            var url = '{{ route('staffing_company.advsearch.create_status')}}?date=' + scheduleDate;
            $('#modelHeading').html('Schedule');
            $.ajaxModal('#scheduleDetailModal', url);
        }
		
		
		
		
		
		
		$( document ).ready(function() {
		
		 	var baseurl 		= "{{ asset('assets/images/AjaxLoader.gif') }}"; 
												
			 table.on('click', '.show-resume-detail', function () {
																		$(".right-sidebar").slideDown(50).addClass("shw-rside");
															
																		var id = $(this).data('row-id');
																		var url = "{{ route('staffing_company.advsearch.resume',':id') }}";
																		url = url.replace(':id', id);
															
																				$.easyAjax({
																								type	: 'GET',
																								url		: url,
																								success	: function (response) {
																																if (response.status == "success") {
																																	$('#right-sidebar-content').html(response.view);
																																}
																															}
																				});
				 });
		
		
		});
		
		function openCanadidateLightbox()
			{
				$(".right-sidebar").slideDown(50).addClass("shw-rside");

				//alert("call"); 
				//return false; 			
				//var id 		= $(this).data('row-id');
				var id 		= "candidate_id";
				var url 	= "{{ route('staffing_company.advsearch.candidateRightSideBar',':id') }}";
				url 		= url.replace(':id', id);
	
				$.easyAjax({
					type: 'GET',
					url: url,
					success: function (response) {
						if (response.status == "success") {
							
							$('#right-sidebar-content').html(response.view);
						}
					}
				});
		}
		
		function updateCandidateProcess(processId, applicationId)
			{
			var processId 		= processId;
			var applicationId 	= applicationId;
          			

            		var url 	= "{{ route('staffing_company.advsearch.candidate_process_update') }}";
                   // url 		= url.replace(':processId', processId);
					var baseurl = "{{ asset('assets/images/AjaxLoader.gif') }}"; 				
                    var token = "{{ csrf_token() }}";					
                    $.easyAjax({
                        type: 'POST',
                        url: url,
                        data: {'_token': token,  'processId' : processId, 'applicationId': applicationId },
						beforeSend: function() {
								$('.statusLoader_'+applicationId).html('<img src="'+baseurl+'" height="50px" width="50px">');
						},
                        success: function (response) {
                         
							/*  if (response.status == "success") {
							$.unblockUI();
							// swal("Deleted!", response.message, "success");
							table._fnDraw();
							}*/
							
                        },
						complete: function (data) {
						  		$('.statusLoader_'+applicationId).html('');
						 }
                    });				
		}
		
		
		
		function getNoticePeriodListing(notice_period)
		{
			//alert('call me');
			tableLoad('filter');	
		}
		
		
		
		
		
	
    </script>
@endpush 

@push('footer-script')

@endpush 