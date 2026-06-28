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
<style>
.help-block{color: red;}
/*#scheduleDetailModal{z-index: 2147483647;}*/
/*#scheduleDetailModal{opacity: 1;}
#scheduleDetailModal > #modal-data-application{opacity: 0;}*/
/*#modal-data-application{z-index: 2147483647!important;}*/
</style>
@endpush

@if(in_array("add_job_applications", $userPermissions))
@section('create-button') <a href="{{ route('staffing_company.job-applications.create') }}" class="btn btn-dark btn-sm m-l-15"><i class="fa fa-plus-circle"></i> @lang('app.createNew')</a> @endsection
@endif

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
          <div class="col-md-6">
            <ol class="breadcrumb float-sm-left mt-xs-2 small bg-white border-bottom border-top border-secondary">
              <li class="breadcrumb-item"><a href="{{ $user->is_superadmin?route('superadmin.dashboard.index'): route('staffing_company.dashboard')}}"><i class="fa fa-home"></i></a></li>
              <li class="breadcrumb-item active">{{ $pageTitle }}</li>
            </ol>
          </div>
          <div class="col-md-6 text-right"> <a href="javascript:void(0);" id="column_visibility_control_btn" class="btn light-primary btn-primary btn-sm"><i class="fa fa-bars fa-sm"></i></a> <a href="{{ route('staffing_company.job-applications.create') }}" class="btn btn-light-primary font-weight-bold btn-sm"><i class="fa fa-edit fa-sm"></i> @lang('app.createNew')</a> <a href="javascript:void(0);" id="reset-filters" class="btn btn-light-primary font-weight-bold btn-sm"><i class="fa fa-spinner fa-sm"></i> @lang('app.reset')</a> </div>
        </div>
        <div class="row clearfix">
          <script src="//cdn.datatables.net/fixedheader/3.1.5/js/dataTables.fixedHeader.min.js"></script>
          <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
          <link rel="stylesheet" href="/resources/demos/style.css">
          <style>
 /* #sortable { list-style-type: none; margin: 0; padding: 0; width: 60%; }
  #sortable li { margin: 0 3px 3px 3px; padding: 0.4em; padding-left: 1.5em; font-size: 1.4em; height: 18px; }
  #sortable li span { position: absolute; margin-left: -1.3em; }*/
  </style>
          <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/1.12.4/jquery.js"></script>
          <script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
          <script>
  $( function() {
    $( "#sortable" ).sortable();
    $( "#sortable" ).disableSelection();
  } );
  </script>
          <!-- <div class="col-md-12 mb-20">
            <button type="button" id="reset-filters" class="btn btn-sm btn-dark "><i class="fa fa-refresh"></i> @lang('app.reset')</button>
			
			<a href="javascript:;" id="column_visibility_control_btn" class="btn btn-outline btn-success btn-sm ">
				<i class="fa fa-sliders"></i> Column Visibility Control 
			</a> 
			
          </div>
          <div class="col-md-12 mb-20"> <a class="pull-right" onclick="exportJobApplication()" >
            <button class="btn btn-sm btn-primary" type="button"><i class="fa fa-upload"></i> @lang('menu.export') </button>
            </a> 
        </div> -->
          <?php 
		$candidate_field = DB::table('custom_candidate_display_field')
							->select('*')
							->where('user_id' , auth()->user()->id)
							->where('display_status' , 1)
							->orderBy('display_order', 'ASC')
							->get();
		
		//echo '<pre>'; print_r($candidate_field); 					
													
?>
        </div>
        <form action="" id="filter-form" class="mb-20">
          <table id="myTable" class="table table-bordered table-hover table responsive table-responsive">
            <thead>
              <tr class="table-bg-custom">
                <th>#</th>
                <th>Date </th>
                <th> Applicant </th>
                <th> <div class="col-md-2">
                    <div class="form-group margin-0">
                      <!-- <label for="address"></label> -->
                      <select name="clients" id="clients" data-style="form-control" onchange="getClientJob(this.value);">
                        <option value="all">All Client</option>
                                                  
							@forelse($clients as $clientsArr)                          
                          
                        <option value="{{$clientsArr->id}}">{{ucfirst($clientsArr->name)}}</option>
                          @empty
							@endforelse                        
                        
                      </select>
                      <span class="clientStatusLoader" style="height:35px; width:35px"></span> </div>
                  </div></th>
                <th> <div class="col-md-2">
                    <div class="form-group margin-0">
                      <!-- <label for="address"></label> -->
                      <select name="jobs" id="jobs" data-style="form-control">
                        <option value="all">All Jobs</option>
                        
                          
						@forelse($jobs as $job)
                          
                          
                        <option title="{{ucfirst($job->title)}}" value="{{$job->id}}">{{ucfirst($job->title)}}</option>
                                                  
						@empty
						@endforelse                        
                        
                      </select>
                      <span class="jobLoader" style="height:35px; width:35px!important"></span> </div>
                  </div></th>
                <th> <div class="col-md-2">
                    <div class="form-group margin-0">
                      <!-- <label for="address"></label> -->
                      <select class="select22" name="source" id="source" data-style="form-control">
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
                      <span class="sourceLoader" style="height:35px; width:35px"></span> </div>
                  </div></th>
                <th> Email </th>
                <th> Phone </th>
                <th> CTC </th>
                <th> ECTC </th>
                <th> EXP </th>
                <th> REL EXP </th>
                <th> <div class="col-md-3">
                    <div class="form-group margin-0">
                      <!-- <label for="address"></label> -->
                      <select name="search_passport" id="search_passport">
                        <option value="all">Passport</option>
                        <option value="Y">Yes</option>
                        <option value="N">No</option>
                      </select>
                      <span class="search_passportLoader" style="height:35px; width:35px"></span> </div>
                  </div></th>
                <th> <div class="col-md-3">
                    <div class="form-group margin-0">
                      <!-- <label for="address"></label> -->
                      <select class="select2" name="search_np_id" id="search_np_id" data-style="form-control">
                        <option value="all">Notice Period</option>
                        
                          		@forelse($noticeperiod as $noticeArr)								
                          		
                        <option value="{{$noticeArr->np_id}}">{{ucfirst($noticeArr->np_title)}}</option>
                        
                          			@empty
									@endforelse
						
                      </select>
                      <span class="search_np_idLoader" style="height:35px; width:35px"></span> </div>
                  </div></th>
                <th> Curr Title </th>
                <th> Curr Co </th>
                <th> Curr Loc </th>
                <?php 
				 			foreach($candidate_field as $custLavel)
							{
								
				 ?>
                <?php 
				 			
				 		/*	if($custLavel->display_name == 'Date'){
									echo '<th id="'.$custLavel->custom_field_id.'">Date </th>'; 				
							}*/
							
							/*if($custLavel->display_name == 'Applicant'){
									echo '<th id="'.$custLavel->custom_field_id.'"> Applicant </th>'; 				
							}*/
							
							/*if($custLavel->display_name == 'Client'){
									$html =  '<th id="'.$custLavel->custom_field_id.'"> <div class="col-md-3">
												<div class="form-group">
												<label for="address">Client</label>
												<select name="clients" id="clients" data-style="form-control" onchange="getClientJob(this.value);">
												<option value="all">ALL</option>';
												
												foreach($clients as $clientsArr){
													$html .=  '<option value="'.$clientsArr->id.'">'.ucfirst($clientsArr->name).'</option>';
												}
												
									$html .=  '</select>
												<span class="clientStatusLoader" style="height:35px; width:35px"></span> </div>
												</div></th>'; 
									echo $html;			
									
							}*/
							
							
							/*if($custLavel->display_name == 'Jobs'){
									$html =  '<th id="'.$custLavel->custom_field_id.'"> <div class="col-md-3">
												<div class="form-group">
												<label for="address">Jobs</label>
												<select class="select21" name="jobs" id="jobs" data-style="form-control">
												<option value="all">ALL</option>';
												
												foreach($jobs as $jobLavel){
													$html .=  '<option value="'.$jobLavel->id.'">'.ucfirst($jobLavel->title).'</option>';
												}
												
										$html .=  '</select>
										<span class="jobLoader" style="height:35px; width:35px"></span> </div>
										</div></th>'; 
										echo $html;			
									
							}*/
							
							
							/*if($custLavel->display_name == 'Source'){
									$html =  '<th id="'.$custLavel->custom_field_id.'"> <div class="col-md-3">
												<div class="form-group">
												<label for="address">Source</label>
												<select class="select22" name="source" id="source" data-style="form-control">
												<option value="all">ALL</option>';
												
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
												
										$html .=  ' <optgroup label="Internal Team">'.$type_zero.'</optgroup>
										<optgroup label="Consultancy">'.$type_one.'</optgroup>
										</select>
										<span class="sourceLoader" style="height:35px; width:35px"></span> </div>
										</div></th>'; 
										echo $html;			
									
							}*/
							
							/*
							if($custLavel->display_name == 'Email'){
									echo '<th id="'.$custLavel->custom_field_id.'"> Email </th>'; 				
							}
							
							if($custLavel->display_name == 'Phone'){
									echo '<th id="'.$custLavel->custom_field_id.'"> Phone </th>'; 				
							}
							
							if($custLavel->display_name == 'CTC'){
									echo '<th id="'.$custLavel->custom_field_id.'" style="width:2%"> CTC </th>'; 				
							}
							
							if($custLavel->display_name == 'ECTC'){
									echo '<th id="'.$custLavel->custom_field_id.'" style="width:2%"> ECTC </th>'; 				
							}
							
							if($custLavel->display_name == 'EXP'){
									echo '<th id="'.$custLavel->custom_field_id.'" > EXP </th>'; 				
							}
							
							if($custLavel->display_name == 'REL EXP'){
									echo ' <th id="'.$custLavel->custom_field_id.'"> REL EXP </th>'; 				
							}
							
							if($custLavel->display_name == 'REL EXP'){
									echo ' <th id="'.$custLavel->custom_field_id.'"> REL EXP </th>'; 				
							}*/
							
							/*if($custLavel->display_name == 'Passport'){
									echo '<th id="'.$custLavel->custom_field_id.'" style="width:2%"> <div class="col-md-3">
											<div class="form-group">
											<label for="address">Passport</label>
											<select name="search_passport" id="search_passport">
											<option value="all">ALL </option>
											<option value="Y">Yes</option>
											<option value="N">No</option>
											</select>
											<span class="search_passportLoader" style="height:35px; width:35px"></span> </div>
											</div></th>'; 				
							}
							
							
							if($custLavel->display_name == 'Notice Period'){
									$html =  ' <th id="'.$custLavel->custom_field_id.'"> <div class="col-md-3">
												<div class="form-group">
												<label for="address">Notice Period</label>
												<select class="select2" name="search_np_id" id="search_np_id" data-style="form-control">
												<option value="all">ALL</option>';
												
												foreach($noticeperiod as $noticeArr){
													$html .=  '<option value="'.$noticeArr->np_id.'">'.ucfirst($noticeArr->np_title).'</option>';
												}
												
										$html .=  '</select>
													<span class="search_np_idLoader" style="height:35px; width:35px"></span> </div>
													</div></th>'; 
										echo $html;			
									
							}
							
							if($custLavel->display_name == 'Curr DSGN'){
									echo '<th id="'.$custLavel->custom_field_id.'"> Curr DSGN </th>'; 				
							}
							
							if($custLavel->display_name == 'Curr Co'){
									echo '<th id="'.$custLavel->custom_field_id.'"> Curr Co </th>'; 				
							}
							
							if($custLavel->display_name == 'Curr Loc'){
									echo '<th id="'.$custLavel->custom_field_id.'"> Curr Loc </th>'; 				
							}*/
							
							/*if($custLavel->display_name == 'Status'){
									$html =  '<th id="'.$custLavel->custom_field_id.'"> <div class="col-md-3">
												<div class="form-group">
												<label for="address">Status</label>
												<select class="select2" name="status" id="status" data-style="form-control">
												<option value="all">ALL</option>';
												
												foreach($boardColumns as $status){
													$html .=  '<option value="'.$status->id.'">'.ucfirst($status->status).'</option>';
												}
												
										$html .=  '</select>
													<span class="statusLoader" style="height:35px; width:35px"></span> </div>
													</div></th>'; 
										echo $html;			
									
							}
							 
							if($custLavel->display_name == 'Interview Process'){
									echo '<th id="'.$custLavel->custom_field_id.'">Interview Process</th>'; 				
							}*/
							
				 ?>
                <?php 
				 			}
				 ?>
                <th>Education</th>
                <th> <div class="col-md-3">
                    <div class="form-group">
                      <!-- <label for="address"></label> -->
                      <select class="select2" name="status" id="status" data-style="form-control">
                        <option value="all">Interview Process</option>
                        
							@forelse($candidateStatus as $status)
                          
                        <option value="{{$status->id}}">{{ucfirst($status->name)}}</option>
                        
							@empty
							@endforelse
                        
                      </select>
                      <span class="statusLoader" style="height:35px; width:35px"></span> </div>
                  </div></th>
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
@endsection

@push('footer-script')
<script src="{{ asset('assets/node_modules/select2/dist/js/select2.full.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('assets/node_modules/bootstrap-select/bootstrap-select.min.js') }}" type="text/javascript"></script>
<script src="//cdn.datatables.net/fixedheader/3.1.5/js/dataTables.fixedHeader.min.js"></script>
<script src="//cdn.datatables.net/responsive/2.2.3/js/dataTables.responsive.min.js"></script>
<script src="//cdn.datatables.net/colreorder/1.5.2/js/dataTables.colReorder.min.js"></script>
<script src="//cdn.datatables.net/buttons/1.6.1/js/dataTables.buttons.min.js"></script>
<script src="//cdn.datatables.net/buttons/1.6.1/js/buttons.colVis.min.js"></script>
<script src="//cdn.datatables.net/responsive/2.2.3/js/responsive.bootstrap.min.js"></script>
<script src="{{ asset('assets/node_modules/moment/moment.js') }}" type="text/javascript"></script>
<script src="{{ asset('assets/node_modules/multiselect/js/jquery.multi-select.js') }}"></script>
<script src="{{ asset('assets/plugins/iCheck/icheck.min.js') }}"></script>
<script src="{{ asset('assets/node_modules/bootstrap-datepicker/bootstrap-datepicker.min.js') }}" type="text/javascript"></script>
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

            var status 				= $('#status').val();
            var jobs 				= $('#jobs').val();
          
			var   clients 			= $('#clients').val();
			var   source 			= $('#source').val();
			
			
			var   search_passport 	= $('#search_passport').val();
			var   search_np_id 		= $('#search_np_id').val();
			
			
            table = $('#myTable').dataTable({
                responsive	: true,
                processing	: true,
                serverSide	: true,
                destroy		: true,
                stateSave	: true,
				
				//"sDom": 'C<"clear">lfrtip'
				
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
					
				
		/*	Dom: 'C<"clear">lfrtip',
		ColVis: {
			buttonText: "Change columns"
		},*/
				
				
				
				/*buttons: [
				{
				text: 'Reload',
				action: function ( e, dt, node, config ) {
				dt.ajax.reload();
				}
				},*/
		
		
				
				//autoWidth : false,
				
                ajax: '{!! route('staffing_company.cvtracker.data') !!}?status='+status+'&jobs='+jobs+'&clients='+clients+'&source='+source+'&search_passport='+search_passport+'&search_np_id='+search_np_id,
				
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
				 
                //columns: generateDynamicColumn()
				//columns:  $('#hiddenCustFld').val()	
				
				
						
				columns: [
                    		{data: 'id', name:'id',width: '2%', searchable : false},
							{data: 'created_at', name: 'created_at', width: '0%', orderable: true},
							{data: 'full_name', name: 'full_name', width: '5%', orderable: true},
							{data: 'client_name', name: 'clients.name', width: '5%', orderable: false },
							{data: 'title', name: 'jobs.title', width: '15%', orderable: false},
							
							{data: 'posted_by', name: 'users.name', width: '5%', orderable: false},
							{data: 'email', name: 'email', width: '5%', orderable: true},
							{data: 'phone', name: 'phone', width: '5%'},
							{data: 'ctc', name: 'ctc', width: '5%'},
							{data: 'ectc', name: 'ectc', width: '5%'},
							{data: 'total_exp', name: 'total_exp', width: '5%'},
							{data: 'rel_exp', name: 'rel_exp', width: '5%'},
							{data: 'passport', name: 'passport', width: '5%', orderable: false},
							{data: 'np_title', name: 'notice_periods.np_title', width: '5%', orderable: false},
							
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
				var url 			= "{{ route('staffing_company.cvtracker.load_visibility_control',':id') }}";
				url 				= url.replace(':id', id);
				$('#modelHeading_vis').html('Schedule');
				$.ajaxModal('#scheduleDetailModal_vis', url);
		});
		
		
		
		/*$('.column_visibility_control').click(function()  modelHeading_vis
		{
				var checked_arr 	= [];
				var unchecked_arr 	= [];
				
				$.each($('input[type="checkbox"]:checked'), function (key, value) {
						var dt = $('#myTable').DataTable();
						dt.column(this.value).visible(true);
				});
				
				$.each($('input[type="checkbox"]:not(:checked)'), function (key, value) {
						var dt = $('#myTable').DataTable();
						dt.column(this.value).visible(false);
				});
				
		});*/
		
		
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

        $('.toggle-filter').click(function () {
            $('#ticket-filters').toggle('slide');
        });

        function exportJobApplication()
		{
		
			var clients 		= '';
			var jobs 			= '';
			var source 			= '';
			var search_passport = '';
			var search_np_id 	= '';
			var status 			= '';
			
			var start           = '';
			var length          = '';
				
			 clients			=	$('#clients').val();
			 jobs				=	$('#jobs').val();
			 source				=	$('#source').val();
			 search_passport	=	$('#search_passport').val();
			 search_np_id		=	$('#search_np_id').val();
			 status				=	$('#status').val();
			
			

			var table 			= $('#myTable').dataTable();
			var length 			= table.fnGetData().length;
			
			
			var oSettings 	= table.fnSettings();
			var start     	= oSettings._iDisplayStart;
			
			//alert(start);
			//alert(oSettings._iDisplayStart); return false; $('#myTable').dataTable().fnGetData()
			
			var url 			= '{{ route('staffing_company.cvtracker.export', [':clients', ':jobs', ':source', ':search_passport', ':search_np_id', ':status' , ':start' , ':length']) }}';
			url 				= url.replace(':clients', clients);
			url 				= url.replace(':jobs', jobs);
			url 				= url.replace(':source', source);
			url 				= url.replace(':search_passport', search_passport);
			url 				= url.replace(':search_np_id', search_np_id);
			url 				= url.replace(':status', status);
			
			url 				= url.replace(':start', start);
			url 				= url.replace(':length', length);
			//alert(url); 
			//return false;
            window.location.href = url;
			
			
			
        }

		function updateCandidateStatus(scheduleDate) {
            if (typeof scheduleDate === "undefined") {
                scheduleDate = '';
            }
            var url = '{{ route('staffing_company.cvtracker.create_status')}}?date=' + scheduleDate;
            $('#modelHeading').html('Schedule');
            $.ajaxModal('#scheduleDetailModal', url);
        }
		
		
		
		function updateCandidateStatus_old(statusId, applicationId)
		{
			var statusId 		= statusId;
			var applicationId 	= applicationId;
          			

			var url 	= "{{ route('staffing_company.job-applications.statusupdate',':statusId') }}";
			url 		= url.replace(':statusId', statusId);

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
		
		function getClientJob(clientId)
		{
					var clientId 		= clientId;
					var url 			= "{{ route('staffing_company.cvtracker.get_client_job',':clientId') }}";
                    url 				= url.replace(':clientId', clientId);

					var baseurl 		= "{{ asset('assets/images/AjaxLoader.gif') }}"; 
				
                    var token 			= "{{ csrf_token() }}";
					//alert(clientId); 
					
					 $.easyAjax({
                        				type: 'POST',
                        				url: url,
                        				data: {'_token': token, '_method': 'POST', 'clientId' : clientId},
										beforeSend: function() {
																	$('.clientStatusLoader').html('<img src="'+baseurl+'" height="30px" width="30px">');
										},
                        				success: function (response) {
                         
						 					$('#jobs').html(response.data);
											$('.clientStatusLoader').html('');
											tableLoad('filter');
											
											//alert(response.data );
										  /*  if (response.status == "success") {
												$.unblockUI();
										//      swal("Deleted!", response.message, "success");
												table._fnDraw();
											}*/
											
										},
										complete: function (data) {
												$('.clientStatusLoader').html('');
										 }
                    });
					
					
            	
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
				
			
			 table.on('click', '.show-resume-detail', function () {
            $(".right-sidebar").slideDown(50).addClass("shw-rside");

            var id = $(this).data('row-id');
            var url = "{{ route('staffing_company.cvtracker.resume',':id') }}";
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
				var url 	= "{{ route('staffing_company.cvtracker.candidateRightSideBar',':id') }}";
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
          			

            		var url 	= "{{ route('staffing_company.cvtracker.candidate_process_update') }}";
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
//                                    swal("Deleted!", response.message, "success");
                                table._fnDraw();
                            }*/
							
                        },
						complete: function (data) {
						  		$('.statusLoader_'+applicationId).html('');
						 }
                    });
				
		}
		
		
		
		
		
		
		
	
    </script>
@endpush 

@push('footer-script')
<script src="{{ asset('assets/node_modules/select2/dist/js/select2.full.min.js') }}"type="text/javascript"></script>
<script src="{{ asset('assets/node_modules/bootstrap-select/bootstrap-select.min.js') }}"type="text/javascript"></script>
<script src="//cdn.datatables.net/fixedheader/3.1.5/js/dataTables.fixedHeader.min.js"></script>
<script src="//cdn.datatables.net/responsive/2.2.3/js/dataTables.responsive.min.js"></script>
<script src="//cdn.datatables.net/responsive/2.2.3/js/responsive.bootstrap.min.js"></script>
<script src="{{ asset('assets/node_modules/moment/moment.js') }}" type="text/javascript"></script>
<script src="{{ asset('assets/plugins/calendar/dist/fullcalendar.min.js') }}"></script>
<script src="{{ asset('assets/plugins/calendar/dist/jquery.fullcalendar.js') }}"></script>
<script src="{{ asset('assets/plugins/calendar/dist/locale-all.js') }}"></script>
<script src="{{ asset('js/schedule-calendar.js') }}"></script>
@endpush 