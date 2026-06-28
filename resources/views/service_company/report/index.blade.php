@extends('layouts.service_company_layout')
 
@push('head-script')
<link rel="stylesheet" href="//cdn.datatables.net/fixedheader/3.1.5/css/fixedHeader.bootstrap.min.css">
<link rel="stylesheet" href="//cdn.datatables.net/responsive/2.2.3/css/responsive.bootstrap.min.css">
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
<link rel="stylesheet" href="//cdn.datatables.net/fixedheader/3.1.5/css/fixedHeader.bootstrap.min.css">
<link rel="stylesheet" href="//cdn.datatables.net/responsive/2.2.3/css/responsive.bootstrap.min.css">
<style>
    .mb-20 {
        margin-bottom: 20px
    }
    .badge-color{
        color: aliceblue;
    }
</style>
<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script>
  $( function() {
    	$( "#start_date" ).datepicker();
		$( "#end_date" ).datepicker();
  } );
</script>
@endpush 
@section('content')
<div class="row"> </div>
<form action="" id="report_frm" name="report_frm" class="row" style="width: 100%;">
  <div class="row w-100 py-2">
    <input type="text" class="form-control w-100" id="custom_text" name="custom_text" placeholder="Enter Client/Requisitions/Team" value="" />
  </div>
  <div class="row w-100 py-2">
    <div class="col-xs-4 col-md-3">
      <div class="form-group">
        <label class="d-block text-dark text-capitalize mt-0" style=""> <span class="font-weight-bold">Start Date</span> </label>
        <input type="text" class="form-control w-100" id="start_date" name="start_date" placeholder="Start Date:-dd/mm/yyyy" value="" />
      </div>
    </div>
    <div class="col-xs-4 col-md-3">
      <div class="form-group">
        <label class="d-block text-dark text-capitalize mt-0" style=""> <span class="font-weight-bold">End Date</span> </label>
        <input type="text" class="form-control w-100" id="end_date" name="end_date" placeholder="End Date:- dd/mm/yyyy" value="" />
      </div>
    </div>
    <div class="col-xs-4 col-md-3">
      <div class="form-group">
        <label class="d-block text-dark text-capitalize mt-0" style=""> <span class="font-weight-bold">Clients</span> </label>
        <select name="clients" id="clients" data-style="form-control" onchange="getClientJob(this.value);" class="w-100">
          <option value="all">All Client</option>
          
          			
				@forelse($clients as $clientsArr)                          
					
          
          <option value="{{$clientsArr->id}}">{{ucfirst($clientsArr->name)}}</option>
          
          			
				@empty
				@endforelse                        	
		
        
        </select>
        <span class="clientStatusLoader" style="height:25px; width:25px"></span> </div>
    </div>
    <div class="col-xs-4 col-md-3">
      <div class="form-group">
        <label class="d-block text-dark text-capitalize mt-0" style=""> <span class="font-weight-bold">Requisitions</span> </label>
        <select name="jobs" id="jobs" data-style="form-control" class="w-100">
          <option value="all">All Jobs</option>
          
          
				@forelse($jobs as $job)
					
          
          <option title="{{ucfirst($job->title)}}" value="{{$job->id}}">{{ucfirst($job->title)}}</option>
          
          
				@empty
				@endforelse                        
		
        
        </select>
      </div>
    </div>
  </div>
  <div class="row w-100 py-2">
    <div class="col-xs-4 col-md-3">
      <div class="form-group">
        <label class="d-block text-dark text-capitalize mt-0" style=""> <span class="font-weight-bold">Team</span> </label>
        <select class="select22 w-100" name="source" id="source" data-style="form-control">
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
    </div>
    <div class="col-xs-4 col-md-3">
      <div class="form-group">
        <label class="d-block text-dark text-capitalize mt-0" style=""> <span class="font-weight-bold">Interview Process</span> </label>
        <select class=" w-100" name="master_status_id" id="master_status_id" data-style="form-control">
          <option value="all">All Process</option>
          
          
					@forelse($candidateStatus as $status)
						
          
          <option value="{{$status->id}}">{{ucfirst($status->name)}}</option>
          
          
					@empty
					@endforelse
			
        
        </select>
      </div>
    </div>
    <div class="col-xs-4 col-md-3">
      <div class="form-group">
        <label class="d-block text-dark text-capitalize mt-0" style=""> <span class="font-weight-bold">Interview Schedule</span> </label>
        <input name="interview_process" type="radio" value="0" checked="checked" />
        &nbsp;No
        <input name="interview_process" type="radio" value="1" />
        &nbsp;Yes </div>
    </div>
    <div class="col-xs-4 col-md-3">
      <div class="form-group">
        <label class="d-block text-dark text-capitalize mt-0" style="">
        <span class="font-weight-bold">
        <div class="form-group" style="margin-top: 20px">
          <label class="control-label col-xs-12">&nbsp;</label>
          <button type="button" id="apply-filters" class="btn btn-primary btn-sm col-md-6 p-2"> @lang('app.apply') <i class="fa fa-paper-plane fa-sm"></i></button>
          <button type="button" id="reset-filters" class="btn btn-warning btn-sm col-md-5 col-md-offset-1 p-2">@lang('app.reset') <i class="fa fa-spinner fa-sm"></i></button>
        </div>
        </span>
        </label>
      </div>
    </div>
  </div>
</form>
<!--<div class="row">
  <div class="col-md-3 col-sm-6 col-12">
    <div class="info-box"> <span class="info-box-icon" style="background-color: #95a5a6;"><i class="icon-badge badge-color"></i></span>
      <div class="info-box-content"> <span class="info-box-text">@lang('modules.report.jobapplication')</span> <span class="info-box-number">{{$jobApplication}}</span> </div>
    </div>
  </div>
  <div class="col-md-3 col-sm-6 col-12">
    <div class="info-box"> <span class="info-box-icon" style="background-color: #9b59b6;"><i class="icon-badge badge-color"></i></span>
      <div class="info-box-content"> <span class="info-box-text">@lang('modules.report.job')</span> <span class="info-box-number">{{$job}}</span> </div>
    </div>
  </div>
  <div class="col-md-3 col-sm-6 col-12">
    <div class="info-box"> <span class="info-box-icon" style="background-color: #28a745;"><i class="icon-badge badge-color"></i></span>
      <div class="info-box-content"> <span class="info-box-text">@lang('modules.report.candidatehired')</span> <span class="info-box-number">{{$candidatesHired}}</span> </div>
    </div>
  </div>
  <div class="col-md-3 col-sm-6 col-12">
    <div class="info-box"> <span class="info-box-icon" style="background-color: #3D8EE8;"><i class="icon-badge badge-color"></i></span>
      <div class="info-box-content"> <span class="info-box-text">@lang('modules.report.interviewschedule')</span> <span class="info-box-number">{{$interviewScheduled}}</span> </div>
    </div>
  </div>
</div>-->
<div class="row">
  <div class="col-md-3">
    <div class="form-group"> </div>
  </div>
</div>
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-body">
        <div class="row clearfix">
          <div class="col-md-12 mb-20" id="">
            <h3>Report</h3>
            <div id="client_piechart" style="width: 45%; height: 300px; float:left"></div>
            <div id="application_on_jobs_piechart" style="width: 45%; height: 300px; float:left"></div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-body">
        <div class="row clearfix">
          <div class="col-md-12 mb-20" id="">
            <div id="application_on_jobs_barchart" style="width: 49%; height: 300px; float:left"></div>
            <div id="team_work" style="width: 49%; height: 300px; float:right"></div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-body">
        <table id="myTable" class="table table-bordered table-hover table-checkable table-responsive">
          <thead>
            <tr class="table-bg-custom">
              <th>SNo</th>
              <th>Client</th>
              <th>Requisitions</th>
              <th>Source</th>
              <th>Total Application</th>
              <th>Sourcing</th>
              <th>Screening</th>
              <th>Interview</th>
              <th>Negotiation</th>
              <th>Offered</th>
              <th>Joined</th>
              <th>Rejected</th>
            </tr>
          </thead>
        </table>
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
<script src="//cdn.datatables.net/responsive/2.2.3/js/responsive.bootstrap.min.js"></script>
<script src="{{ asset('assets/node_modules/moment/moment.js') }}<?php echo TM_VERSION; ?>" type="text/javascript"></script>
<script src="{{ asset('assets/node_modules/bootstrap-datepicker/bootstrap-datepicker.min.js') }}<?php echo TM_VERSION; ?>" type="text/javascript"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.css" rel="stylesheet" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.9.0/jquery.validate.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.9.0/additional-methods.min.js"></script>
<script type="text/javascript">

	$().ready(function() {
	
			$("#report_frm").validate({
											rules: {
														start_date: "required",
														end_date: "required"
													},
											messages: {
															start_date: "Please enter Start date",
															end_date: "Please enter End date"
													}
			});
	});
</script>
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script type="text/javascript">
      google.charts.load('current', {'packages':['corechart']});
      google.charts.setOnLoadCallback(drawChart);
      function drawChart() {
        var data = google.visualization.arrayToDataTable([
          ['Clients', 'Requisitions'],
          ['TCS',     11],
          ['HCL',      2],
          ['IBM',  2],
          ['Wipro', 2],
          ['ABC',    7]
        ]);		
        var options = {
          title: "Client's Requisitions",
		  pieSliceText: 'value'
        };
        var chart = new google.visualization.PieChart(document.getElementById('client_piechart'));
        chart.draw(data, options);
      }	  
</script>
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script type="text/javascript">
      google.charts.load('current', {'packages':['corechart']});
      google.charts.setOnLoadCallback(drawChart);
      function drawChart() {
        var data = google.visualization.arrayToDataTable([
          ['Requisitions', 'Application'],
          ['Sr PHP Developer',     35],
          ['Automation Testing',      12],
          ['Java Developer',  5],
          ['SAP Programmer', 51],
          ['ABC',    41]
        ]);		
        var options = {
          title: "Application's on Requisitions",
		  pieSliceText: 'value'
        };
        var chart = new google.visualization.PieChart(document.getElementById('application_on_jobs_piechart'));
        chart.draw(data, options);
      }	  
</script>
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script type="text/javascript">
      google.charts.load('current', {'packages':['corechart']});
      google.charts.setOnLoadCallback(drawVisualization);
      function drawVisualization() {
        // Some raw data (not necessarily accurate)
        var data = google.visualization.arrayToDataTable([
          ['Month', 'Testing', 'Sr PHP Developer', 'Automation Testing', 'Java Developer', 'SAP Programmer', 'ABC'],
          ['2021/01',  165,      938,         522,             998,           450,      614.6],
          ['2021/02',  135,      120,        599,             268,          288,      682],
          ['2021/03',  157,      167,        587,             407,           397,      623],
          ['2021/04',  139,      110,        615,             368,           215,      609.4],
          ['2021/05',  136,      691,         629,             626,          366,      569.6]
        ]);

        var options = {
          title : "Application's on Requisitions",
          vAxis: {title: 'Application'},
          hAxis: {title: 'Date'}, 
          seriesType: 'bars',
          series: {5: {type: 'line'}}
        };

        var chart = new google.visualization.ComboChart(document.getElementById('application_on_jobs_barchart'));
        chart.draw(data, options);
      }
</script>
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script type="text/javascript">
      google.charts.load('current', {'packages':['corechart']});
      google.charts.setOnLoadCallback(drawChart);
      function drawChart() {
        var data = google.visualization.arrayToDataTable([
          ['Date', 'Abu', 'Amit', 'Sonika', 'Vibhanshu'],
          ['jan-2021',  1000,      400, 150, 340],
          ['feb-2021',  1170,      460 , 245, 125],
          ['march-2021',  660,       1120, 850, 374],
          ['april-2021',  1030,      540, 893, 1170]
        ]);

        var options = {
          title: 'Team Work(Application submited by team member)',
          hAxis: {title: 'Year',  titleTextStyle: {color: '#333'}},
          vAxis: {minValue: 0}
        };

        var chart = new google.visualization.AreaChart(document.getElementById('team_work'));
        chart.draw(data, options);
      }
</script>
<script>

	  loadTable();
		
       $('#start_date').datepicker({
            // format: 'yyyy-mm-dd'
           // format: 'd-M-Y: H:I:s'
			format: 'd-m-yyyy'
        })
        $('#end_date').datepicker({
            // format: 'yyyy-mm-dd'
            //format: 'd-M-Y: H:I:s'
			format: 'd-m-yyyy'
        })
		
		function getClientJob(clientId)
			{
				
					var clientId 		= clientId;
					var url 			= "{{ route('service_company.cvtracker.get_client_job',':clientId') }}";
                    url 				= url.replace(':clientId', clientId);
					var baseurl 		= "{{ asset('assets/images/AjaxLoader.gif') }}"; 				
                    var token 			= "{{ csrf_token() }}";
					
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
											//tableLoad('filter');
																						
										},
										complete: function (data) {
												$('.clientStatusLoader').html('');
										 }
                    });
					
					
            	
		
			}

			
// Apply Filter
$('#apply-filters').click(function () {
	
	loadTable();
	

});
		

function loadTable()
{


	var custom_text			=	$('#custom_text').val();
	var start_date			=	$('#start_date').val();
	var end_date			=	$('#end_date').val();
	var clients				=	$('#clients').val();             // Clients Default value = all else other number
	var jobs				=	$('#jobs').val();				// Jobs Default value = all else other number
	var source				=	$('#source').val();				// Team Default value = all else other number
	var master_status_id	=	$('#master_status_id').val();	// Jobs Default value = all else other number
	var interview_process 	= $('input[name="interview_process"]:checked').val();  // 0 For Not intrested interview schedule , 1 For intrested interview schedule 
	
	if( (start_date !='') && (end_date !='') )
	{
		
		
			    var table = $('#myTable').dataTable({
														responsive		: true,
														processing		: true,
														serverSide		: true,
														"bFilter"		: false,
														stateSave		: true,"bInfo": false,
													   "bLengthChange"	: true,
													   oLanguage: {
														   sLengthMenu	: "_MENU_",
														},
														lengthMenu: [[10, 25, 50, 100,150,200,250,300], [10, 25, 50, 100,150, 200, 250, 300]],
												
														/*dom		: 'Bfrtip',
														colReorder: true,
														buttons: [{
																	text			:"Visibility Control",
																	extend			: 'colvis',
																	collectionLayout: 'two-column',
																	colReorder		: true,
																	columnText		: function ( dt, idx, title ) {
																						return (idx+1)+': '+title;
																					},
																	dropup			: true
																}],*/
																	
				//ajax: '{!! route('service_company.cvtracker.data') !!}?status='+status+'&jobs='+jobs+'&clients='+clients+'&source='+source+'&search_passport='+search_passport+'&search_np_id='+search_np_id,			
													
													
														ajax: '{!! route('service_company.report.data') !!}',
														language: {
															"url": "<?php echo __("app.datatable") ?>"
														},
														"fnDrawCallback": function( oSettings ) {
															 $("body").tooltip({
																selector: '[data-toggle="tooltip"]'
															});
														},
														columns: [
																	{ data: 'DT_Row_Index'},
																	{ data: 'name', name: 'name'},
																	{ data: 'contact_person_name', name: 'contact_person_name' , searchable : false, orderable : false},
																	{ data: 'location', name: 'location', searchable : false, orderable : false },
																	
																	{ data: 'total_jobs', name: 'total_jobs', searchable : false, orderable : false },
																	{ data: 'open_jobs', name: 'open_jobs', searchable : false, orderable : false },
																	{ data: 'closed_jobs', name: 'closed_jobs', searchable : false, orderable : false },
																	{ data: 'hold_jobs', name: 'hold_jobs', searchable : false, orderable : false },
																	
																	//{ data: 'email', name: 'email' },
																	//{ data: 'commission', name: 'commission' },
																	//{ data: 'payout', name: 'payout' },
															
																	{ data: 'action', name: 'action', searchable : false, orderable : false }
														]
													});
    					new $.fn.dataTable.FixedHeader( table );
			
				
				
			
	}
	
	

}







		
 //for pie chart
  var chart = document.getElementById("myChart").getContext('2d');
  var cData = JSON.parse(`<?php echo $chart_data; ?>`);
  var keys = [];
  var value = [];
  $.each(cData, function(k, v) {
    value.push(v)
    keys.push(k)
});
  var myChart = new Chart(chart, {
  type: 'pie',
  data: {
    labels: keys,
    datasets: [{
      backgroundColor: [
       
        "#95a5a6",
        "#9b59b6",
        "#28a745",
        "#3D8EE8",
      ],
      data:value,
    }]
  }
});
</script>
@endpush 