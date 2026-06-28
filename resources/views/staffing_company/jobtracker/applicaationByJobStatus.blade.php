@extends('layouts.staffing_company_layout')

@push('head-script')
<link rel="stylesheet" href="//cdn.datatables.net/fixedheader/3.1.5/css/fixedHeader.bootstrap.min.css">
<link rel="stylesheet" href="//cdn.datatables.net/responsive/2.2.3/css/responsive.bootstrap.min.css">
<link rel="stylesheet" href="{{ asset('assets/node_modules/html5-editor/bootstrap-wysihtml5.css') }}<?php echo TM_VERSION; ?>">
<link rel="stylesheet" href="{{ asset('assets/node_modules/bootstrap-datepicker/bootstrap-datepicker.min.css') }}<?php echo TM_VERSION; ?>">
<link rel="stylesheet" href="{{ asset('assets/node_modules/multiselect/css/multi-select.css') }}<?php echo TM_VERSION; ?>">
<link rel="stylesheet" href="{{ asset('assets/plugins/iCheck/all.css') }}<?php echo TM_VERSION; ?>">
<style>
        .mb-20{
            margin-bottom: 20px
        }
        .datepicker{
            z-index: 9999 !important;
        }
        .select2-container--default .select2-selection--single, .select2-selection .select2-selection--single {
            width: 250px !important;
        }
        .select2-container--default .select2-selection--single, .select2-selection .select2-selection--single, .select2-search__field {
            width: 250px !important;
        }
        .select2-search__field {
            width: 150px !important;
        }
		
table.dataTable {
clear: both;
margin-top: 0px !important;
margin-bottom: 0px !important;
max-width: none !important;
border-collapse: separate !important;
border-spacing: 0;
}

body {
    margin: 0;
    font-family: "Poppins";
    font-style: normal;
    font-size: 11px;
    /* font-weight: 300; */
    line-height: 1.5;
    color: #000000;
    text-align: left;
    background-color: #fff;
}
.select2-container{ width:60% !important;}
.select2-selection--multiple{ width:80% !important;}

    </style>
<style>
.content{ width:100% !important; overflow:auto !important;}
.row{ width:100% !important; overflow:auto !important;}
.col-12{ width:100% !important; overflow:auto !important;}
.card{ width:100% !important; overflow:auto !important;}
.card-body{ width:100% !important; overflow:auto; overflow:auto}
#footer-sticky-notes{ display:none}
</style>
@endpush

@if(in_array("add_job_applications", $userPermissions))
@section('create-button') <a href="{{ route('staffing_company.jobs.create') }}" class="btn btn-dark btn-sm m-l-15"><i class="fa fa-plus-circle"></i> Create New Job</a> <a href="{{ route('staffing_company.job-applications.create') }}" class="btn btn-dark btn-sm m-l-15" style="margin-left:10px; margin-right:10px"> <i class="fa fa-plus-circle"></i> Upload Single Profile </a> <a href="{{ route('staffing_company.bulkupload.index') }}" class="pull-right"  style="margin-left:10px; margin-right:10px">
<button class="btn btn-sm btn-primary" type="button"><i class="fa fa-upload"></i> Upload Multiple Profile </button>
</a> @endsection
@endif

@section('content')
<?php 
		//echo '<pre>'; print_r($applicationStatus);exit; 
?>
<div class="row">
  <div class="col-24">
    <div class="card">
      <div class="modal fade bs-modal-md in" id="scheduleDetailModal" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" id="modal-data-application">
          <div class="modal-content">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
              <span class="caption-subject font-red-sunglo bold uppercase" id="modelHeading"></span> </div>
            <div class="modal-body"> Loading... </div>
            <div class="modal-footer">
              <button type="button" class="btn default" data-dismiss="modal">Close</button>
              <button type="button" class="btn blue">Update</button>
            </div>
          </div>
        </div>
      </div>
      <div class="card-body">
        <div class="row clearfix">
        <div class="col-md-12 mb-20" style="padding:10px; font-weight:bold; font-size:16px">Job Title&nbsp;&nbsp;::&nbsp;&nbsp; {{ @$job->title }} </div>
        <form action="" id="filter-form" >
          <div class="col-md-12 mb-20"> <a href="javascript:;" id="column_visibility_control_btn" class="btn btn-outline btn-success btn-sm "> <i class="fa fa-sliders"></i> Column Visibility Control </a> @csrf
            <input type="hidden" name="jobid" id="jobid" value="{{ @$job->id }}" />
            <input type="hidden" name="applicationStatus" id="applicationStatus" value="{{ @$applicationStatus }}" />
            Skills
            <input name="skill_name" id="skill_name" type="text" />
            <input type="button" name="submit" id="submit" value="Submit" />
            <div class="skill_posting_loader"></div>
          </div>
          <div class="col-md-12 mb-20"> <a class="pull-right" onclick="exportJobApplication()" >
            <button class="btn btn-sm btn-primary" type="button"><i class="fa fa-upload"></i> @lang('menu.export') </button>
            </a> </div>
          </div>
          <div class="table-responsive m-t-40">
            <table id="myTable" class="table table-bordered table-striped">
              <thead>
                <tr>
                  <th>#</th>
                  <th>Date </th>
                  <th> Applicant </th>
                  <th>Client </th>
                  <!-- <th>Jobs </th>-->
                  <th>Source</th>
                  @forelse($skillData as $sdata)
                  <th>{{ @$sdata->name}}</th>
                  @empty
                  @endforelse
                  <th> Email </th>
                  <th> Phone </th>
                  <th style="width:2%"> CTC </th>
                  <th style="width:2%"> ECTC </th>
                  <th> EXP </th>
                  <th> REL EXP </th>
                  <th style="width:2%">Passport </th>
                  <th>Notice Period </th>
                  <th> Curr DSGN </th>
                  <th> Curr Co </th>
                  <th> Curr Loc </th>
                  <th>Qualification </th>
                  <th>Status </th>
                  <th>Interview Status Level </th>
                  <th>Resume </th>
                  <th> @lang('app.action') </th>
                </tr>
              </thead>
            </table>
          </div>
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
@endsection

@push('footer-script')
<script src="{{ asset('assets/node_modules/select2/dist/js/select2.full.min.js') }}<?php echo TM_VERSION; ?>" type="text/javascript"></script>
<script src="{{ asset('assets/node_modules/bootstrap-select/bootstrap-select.min.js') }}<?php echo TM_VERSION; ?>" type="text/javascript"></script>
<script src="//cdn.datatables.net/fixedheader/3.1.5/js/dataTables.fixedHeader.min.js"></script>
<script src="//cdn.datatables.net/responsive/2.2.3/js/dataTables.responsive.min.js"></script>
<script src="//cdn.datatables.net/colreorder/1.5.2/js/dataTables.colReorder.min.js"></script>
<script src="//cdn.datatables.net/buttons/1.6.1/js/dataTables.buttons.min.js"></script>
<script src="//cdn.datatables.net/buttons/1.6.1/js/buttons.colVis.min.js"></script>
<script src="//cdn.datatables.net/responsive/2.2.3/js/responsive.bootstrap.min.js"></script>
<script src="{{ asset('assets/node_modules/moment/moment.js') }}<?php echo TM_VERSION; ?>" type="text/javascript"></script>
<script src="{{ asset('assets/node_modules/multiselect/js/jquery.multi-select.js') }}<?php echo TM_VERSION; ?>"></script>
<script src="{{ asset('assets/plugins/iCheck/icheck.min.js') }}<?php echo TM_VERSION; ?>"></script>
<script src="{{ asset('assets/node_modules/bootstrap-datepicker/bootstrap-datepicker.min.js') }}<?php echo TM_VERSION; ?>" type="text/javascript"></script>
<script>


		$('#column_visibility_control_btn').click(function(){
				  var id 		= $('#jobid').val();
				//var id 				= 12;
				if(typeof scheduleDate === "undefined") {
					scheduleDate 	= '';
				}
				var url 			= "{{ route('staffing_company.jobtracker.load_visibility_control',':id') }}";
				url 				= url.replace(':id', id);
				$('#modelHeading').html('Schedule');
				$.ajaxModal('#scheduleDetailModal', url);
		});
		
		
//        $("#skill").select2({
//            multiple: true,
//            placeholder: 'Select Skills'
//        })
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
        function tableLoad(type) {

           // var status 		= $('#status').val();
            var jobid 					= $('#jobid').val();
            var applicationStatus 		= $('#applicationStatus').val();
			
			
			
            table = $('#myTable').dataTable({
                responsive: true,
                processing: true,
                serverSide: true,
                destroy: true,
                stateSave: true,
				//autoWidth : false,
				
				//dom		: 'Bfrtip',
				colReorder: true,
				//buttons: ['colvis'],
				
				buttons: [{
				
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
						
               // ajax: '{!! route('staffing_company.jobtracker.applicationdata') !!}?jobid='+jobid+'&applicationStatus='+applicationStatus,
				
				 ajax: '{!! route('staffing_company.jobtracker.applicationdata') !!}/'+jobid+'/'+applicationStatus,
				 
				
				/* ajax: '{!! route('staffing_company.cvtracker.data') !!}?status='+status+'&location='+location+'&startDate='+startDate+'&endDate='+endDate+'&jobs='+jobs+'&skill='+skill+'&clients='+clients+'&source='+source+'&search_passport='+search_passport+'&search_np_id='+search_np_id+'&search_status='+search_status,*/
				
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
				 
                columns: [
                    		{data: 'id', name:'id',width: '2%', searchable : false},
							{data: 'created_at', name: 'created_at', width: '0%', orderable: true},
							{data: 'full_name', name: 'full_name', width: '5%', orderable: false},
							{data: 'client_name', name: 'clients.name', width: '5%', orderable: false },
							/*{data: 'title', name: 'jobs.title', width: '15%', orderable: false},*/
							
							{data: 'posted_by', name: 'users.name', width: '5%', orderable: false},
							
							<?php 
									foreach($skillData as $sdata)
									{
										echo "{data: 'skill_".$sdata->id."', name: 'skill_".$sdata->id."', width: '5%', orderable: false, searchable: false},";	
									}
							?>
				  
							{data: 'email', name: 'email', width: '5%', orderable: false},
							{data: 'phone', name: 'phone', width: '5%', orderable: false},
							{data: 'ctc', name: 'ctc', width: '5%', orderable: false},
							{data: 'ectc', name: 'ectc', width: '5%', orderable: false},
							{data: 'total_exp', name: 'total_exp', width: '5%', orderable: false},
							{data: 'rel_exp', name: 'rel_exp', width: '5%', orderable: false},
							{data: 'passport', name: 'passport', width: '5%', orderable: false},
							{data: 'np_title', name: 'notice_periods.np_title', width: '5%', orderable: false},
							
							{data: 'current_designation', name: 'current_designation', width: '5%',  orderable : false},
							{data: 'current_company', name: 'current_company', width: '5%', orderable : false},
							{data: 'current_location', name: 'current_location', width: '5%', orderable : false},
							//{data: 'location', name: 'job_locations.location', width: '5%'},
							
							{data: 'qualification_name', name: 'qualification_name', width: '5%', orderable : false},
							
							{data: 'status', name: 'application_status.status', width: '5%', orderable : false},
							{data: 'interview_status_level', name: 'interview_status_level', width: '5%', searchable : false, orderable : false},
							
							{data: 'resume', name: 'resume', width: '5%', searchable : false, orderable : false },
							
							{data: 'action', name: 'action', width: '10%', orderable : false}
							
                    		
                ]
            });
            new $.fn.dataTable.FixedHeader(table);
        }

        $('body').on('click', '.sa-params', function(){
            var id = $(this).data('row-id');
            swal({
                title: "@lang('errors.areYouSure')",
                text: "@lang('errors.deleteWarning')",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "@lang('app.delete')",
                cancelButtonText: "@lang('app.cancel')",
                closeOnConfirm: true,
                closeOnCancel: true
            }, function(isConfirm){
                if (isConfirm) {

                    var url = "{{ route('staffing_company.cvtracker.destroy',':id') }}";
                    url = url.replace(':id', id);

                    var token = "{{ csrf_token() }}";

                    $.easyAjax({
                        type: 'POST',
                        url: url,
                        data: {'_token': token, '_method': 'DELETE'},
                        success: function (response) {
                            if (response.status == "success") {
                                $.unblockUI();
//                                    swal("Deleted!", response.message, "success");
                                table._fnDraw();
                            }
                        }
                    });
                }
            });
        });

        table.on('click', '.show-detail', function () {
            $(".right-sidebar").slideDown(50).addClass("shw-rside");

            var id = $(this).data('row-id');
            var url = "{{ route('staffing_company.cvtracker.show',':id') }}";
            url = url.replace(':id', id);

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

        $('.toggle-filter').click(function () {
            $('#ticket-filters').toggle('slide');
        });

        function exportJobApplication()
		{
			
			var jobs 			= '';
			jobs     			= $('#jobid').val();
			
			var table 			= $('#myTable').dataTable();
			var length 			= table.fnGetData().length;
			
			
			var oSettings 	= table.fnSettings();
			var start     	= oSettings._iDisplayStart;
			
			var url 			= '{{ route('staffing_company.jobtracker.export', [':jobs', ':start', ':length' ]) }}';
			url 				= url.replace(':jobs', jobs);
			url 				= url.replace(':start', start);
			url 				= url.replace(':length', length);
			
			
            window.location.href = url;
			
        }
		
		

		
		
		
		
		
		
		$( document ).ready(function() {
		 	var baseurl 		= "{{ asset('assets/images/AjaxLoader.gif') }}"; 
			
			
			$('#jobs').on('change', function() {
				//alert("call"); 
				$('.jobLoader').html('<img src="'+baseurl+'" height="30px" width="30px">');
				tableLoad('filter');
			});
			
			$('#source').on('change', function() {
				$('.sourceLoader').html('<img src="'+baseurl+'" height="30px" width="30px">');
				tableLoad('filter');
			});
			
			
			
			
			
			$('#search_passport').on('change', function() {
				$('.search_passportLoader').html('<img src="'+baseurl+'" height="30px" width="30px">');
				tableLoad('filter');
			});
			
			$('#search_np_id').on('change', function() {
				$('.search_np_idLoader').html('<img src="'+baseurl+'" height="30px" width="30px">');
				tableLoad('filter');
			});
			
			$('#status').on('change', function() {
			
				
				$('.statusLoader').html('<img src="'+baseurl+'" height="30px" width="30px">');
				tableLoad('filter');
			});
			
			
			
			$('#submit').click(function () {

							$.easyAjax({
								url: '{{route('staffing_company.jobtracker.store')}}',
								container: '#filter-form',
								type: "POST",
								redirect: true,
								file: true,
								data: $('#filter-form').serialize(),
								beforeSend: function() {
									$('.skill_posting_loader').html('<img src="'+baseurl+'" height="30px" width="30px">');
								},
                        		success: function (response) {
						 						$('.skill_posting_loader').html('');
												  location.reload();
								},
										
								error: function (response) {
									handleFails(response);
								}
							})
						});
				
		
		});
		
		
		
		
		$('body').on('keyup, change', '.userinfo', function()
		{
            var jobid 		= $(this).data('jobid');
			var candidate 	= $(this).data('candidate');
			var schematype 	= $(this).data('schematype');
			var schemaname  = $(this).data('schemaname');
			var schemavalue = $(this).val();
			
			//alert(schemavalue);
		//	url: '{{route('staffing_company.jobtracker.store')}}',
           var url = '{{ route('staffing_company.jobtracker.updateinformation') }}';
          // url = url.replace(':candidate', candidate);

                    var token = "{{ csrf_token() }}";

                    $.easyAjax({
                        type: 'POST',
                        url: url,
                        data: {'_token': token, 'jobid': jobid, 'candidate': candidate, 'schematype': schematype, 'schemaname': schemaname, 'schemavalue': schemavalue},
						// data: { 'jobid': jobid},
                        success: function (response) {
						
							//alert(response); return false;
                            if (response.status == "success") 
							{
                                $.unblockUI();
								
                                table._fnDraw();
                            }
                        }
                    });
               
            
        });
		
		function updateCandidatePassport(currentvalue, candidate, jobid)
		{
			//updateCandidateStatus(currentvalue, candidate, jobid, 'noskill', 'passport');
			customApplicationUpdate(currentvalue, candidate, jobid, 'noskill', 'passport');
		}
		function updateCandidateNoticePeriod(currentvalue, candidate, jobid)
		{
			//updateCandidateStatus(currentvalue, candidate, jobid, 'noskill', 'np_id');
			customApplicationUpdate(currentvalue, candidate, jobid, 'noskill', 'np_id');
		}
		function updateApplicationStaus(currentvalue, candidate, jobid)
		{
			updateCandidateStatus(currentvalue, candidate, jobid, 'noskill', 'status_id');
		}
		
		function updateApplicationEducation(currentvalue, candidate, jobid)
		{
			//updateCandidateStatus(currentvalue, candidate, jobid, 'noskill', 'education');
			customApplicationUpdate(currentvalue, candidate, jobid, 'noskill', 'education');
		}
		
		
		function customApplicationUpdate(currentvalue,  candidate, jobid, schematype, schemaname )
		{
		    var jobid 		= jobid;
			var candidate 	= candidate;
			var schematype 	= schematype;
			var schemaname  = schemaname;
			var schemavalue = currentvalue;
			
			//alert(schemavalue);
		//	url: '{{route('staffing_company.jobtracker.store')}}',
           var url = '{{ route('staffing_company.jobtracker.updateinformation') }}';
          // url = url.replace(':candidate', candidate);

                    var token = "{{ csrf_token() }}";

                    $.easyAjax({
                        type: 'POST',
                        url: url,
                        data: {'_token': token, 'jobid': jobid, 'candidate': candidate, 'schematype': schematype, 'schemaname': schemaname, 'schemavalue': schemavalue},
						// data: { 'jobid': jobid},
                        success: function (response) {
						
							//alert(response); return false;
                            if (response.status == "success") 
							{
                                $.unblockUI();
								
                                table._fnDraw();
                            }
                        }
                    });
			
		}
		
		
		
		
		
		
		
		
		
		
		
		function updateApplicationStatus121(statusId, applicationId)
		{
			var statusId 		= statusId;
			var applicationId 	= applicationId;
          			

            		var url = "{{ route('staffing_company.job-applications.statusupdate',':statusId') }}";
                    url = url.replace(':statusId', statusId);

					var baseurl = "{{ asset('assets/images/AjaxLoader.gif') }}"; 
				
                    var token = "{{ csrf_token() }}";
					
                    $.easyAjax({
                        type: 'POST',
                        url: url,
                        data: {'_token': token, '_method': 'POST', 'statusId' : statusId, 'applicationId': applicationId },
						beforeSend: function() {
								$('.statusLoader_'+applicationId).html('<img src="'+baseurl+'" height="50px" width="50px">');
						},
                        success: function (response) {
                         
						  /*  if (response.status == "success") {
                                $.unblockUI();
//                                    swal("Deleted!", response.message, "success");
                                table._fnDraw();
                            }*/
							
                        },
						complete: function (data) {
						  		$('.statusLoader_'+applicationId).html('');
						 }
                    });
				
		}
		
		
		function updateCandidateStatus(scheduleDate) {
            if (typeof scheduleDate === "undefined") {
                scheduleDate = '';
            }
            var url = '{{ route('staffing_company.cvtracker.create_status')}}?date=' + scheduleDate;
            $('#modelHeading').html('Schedule');
            $.ajaxModal('#scheduleDetailModal', url);
        }
		
		
    </script>
@endpush

@push('footer-script')
<script src="{{ asset('assets/node_modules/select2/dist/js/select2.full.min.js') }}<?php echo TM_VERSION; ?>"type="text/javascript"></script>
<script src="{{ asset('assets/node_modules/bootstrap-select/bootstrap-select.min.js') }}<?php echo TM_VERSION; ?>"type="text/javascript"></script>
<script src="//cdn.datatables.net/fixedheader/3.1.5/js/dataTables.fixedHeader.min.js"></script>
<script src="//cdn.datatables.net/responsive/2.2.3/js/dataTables.responsive.min.js"></script>
<script src="//cdn.datatables.net/responsive/2.2.3/js/responsive.bootstrap.min.js"></script>
<script src="{{ asset('assets/node_modules/moment/moment.js') }}<?php echo TM_VERSION; ?>" type="text/javascript"></script>
<script src="{{ asset('assets/plugins/calendar/dist/fullcalendar.min.js') }}<?php echo TM_VERSION; ?>"></script>
<script src="{{ asset('assets/plugins/calendar/dist/jquery.fullcalendar.js') }}<?php echo TM_VERSION; ?>"></script>
<script src="{{ asset('assets/plugins/calendar/dist/locale-all.js') }}<?php echo TM_VERSION; ?>"></script>
<script src="{{ asset('js/schedule-calendar.js') }}<?php echo TM_VERSION; ?>"></script>
@endpush 