<style>
.header-fixed.toolbar-fixed .wrapper
{
   margin: 0em;
   padding-right: 0em!important;
}
.mt-3-minus
{
   margin-top: -3em !important;
}
/*Media query for 2 screens*/
@media only screen and (min-device-width : 720px) and (max-device-width : 1280px)
{
}
/*Media query for 2 screens end*/
</style>
<?php 
//echo '<pre>'; print_r($clientJob); exit; 
?>
<div class="row">
   <div class="col-12">
      <div class="card shadow-none bg-transparent">
         <div class="card-body">
            <!--begin::Charts Row-1-->
            <div class="d-flex flex-column-fluid">
               <!--begin::Container-->
               <div class="container-fluid">
                  <div class="row">        
                  <!--begin::Card-->
                  <div class="card card-custom mb-0 px-0 shadow-none">
                     <div class="card-header">
                        <div class="card-title">
                        <h3 class="card-label fs-2">All Requisitions</h3>
                        </div>
                     </div>
                     <!--begin::Card body-->
                     <div class="card-body px-3 py-3">
                        <div class="row row-eq-height">
                           <div class="col-lg-4">
                              <!--begin::Card-->
                              <div class="card card-custom gutter-b bg-light h-100">
                                 <div class="card-header border-bottom-dark1">
                                    <div class="card-title">
                                       <h3 class="card-label">Client's All Requisitions</h3>
                                    </div>
                                 </div>
                                 <div class="card-body">                    
                                    <!--begin::Chart-->
                                    <div id="" class="d-flex justify-content-center">
                                       <?php 
                                          if($clientJobCount > 0)
                                          {
                                          
                                          ?>
                                       <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
                                       <script type="text/javascript">
                                          // Shared Colors Definition
                                          const inreview = "#CCDF30";
                                          const sourcing = "#BBBDC9";
                                          const screening = "#8950FC";
                                          const evaluation = "#6993FF";
                                          const negotation = "#FF6CB0";
                                          const offer = '#FFA800';
                                          const joined = '#1BC5BD';
                                          const rejected = '#F64E60';

                                          google.charts.load("current", {packages:["corechart"]});
                                          google.charts.setOnLoadCallback(drawChart);
                                          function drawChart() {
                                          
                                          var data = google.visualization.arrayToDataTable([
                                          ['Task', 'Hours per Day'],
                                          <?php
                                             foreach($clientJob as $cj){								
                                                //echo "['".$cj->name."("..$cj->name.")',  "..$cj->name."],";  jobCounter
                                                echo "['".$cj->name."-".$cj->jobCounter.""."',     ".$cj->jobCounter."],";
                                             }	
                                             ?>      
                                          ]);
                                          var options = {
                                          // title          :  Client's All Requisitions, 
                                          // legend			: 'none',
                                          // pieSliceText	: 'label',
                                          // pieStartAngle  : 250,                                  
                                          chart: {
                                             width : 380,
                                             type  : 'pie',
                                          },
                                          legendTextStyle:  
                                          {
                                             fontName : "Poppins,Helvetica,sans-serif!important",
                                             fontSize : 13,
                                             textTransform: "capitalize",
                                             bold  : false,
                                             is3D: true,
                                             'width':400,
                                             'height':300
                                          },
                                          chartArea   :
                                          {
                                             left:0,
                                             top:10,
                                             right:0,
                                             bottom:10,
                                             width:"100%",
                                             height:"100%"
                                          },
                                          colors: [negotation, screening, offer, evaluation, sourcing, rejected, inreview, joined],
                                          // colors: [inreview, sourcing, screening, evaluation, negotation, offer, joined, rejected],
                                          fontName: "Poppins,Helvetica,sans-serif",
                                          backgroundColor: "transparent",
                                          fontSize: 13,
                                          bold: true
                                          };
                                          var chart = new google.visualization.PieChart(document.getElementById('piechart_client_jobs1'));
                                          chart.draw(data, options);
                                          }
                                       </script>
                                       <div class="" id="piechart_client_jobs1" style=""></div>
                                       <?php 
                                          }
                                          else
                                          {                  	
                                          ?>
                                       <div class="" id="piechart_client_jobs1" style="">
                                          <img src="<?php echo asset('img/blank_pie_chart1.png'); ?>" class="img-responsive img-fluid h-100" style="    height: 171px!important;">
                                       </div>
                                       <?php 
                                          }
                                          ?>
                                    </div>
                                    <!--end::Chart-->                 
                                 </div>
                              </div>
                              <!--end::Card-->
                           </div>
                           <div class="col-lg-4">
                              <!--begin::Card-->
                              <div class="card card-custom gutter-b bg-light h-100">
                                 <div class="card-header border-bottom-dark1">
                                    <div class="card-title">
                                       <h3 class="card-label">Application On Requisitions</h3>
                                    </div>
                                 </div>
                                 <div class="card-body">                    
                                    <!--begin::Chart-->
                                    <div id="" class="d-flex justify-content-center">
                                       <?php 
                                          if($applicationOnRequisitionCount > 0)
                                          {
                                          ?>
                                       <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
                                       <script type="text/javascript">
                                          google.charts.load('current', {'packages':['corechart']});
                                          google.charts.setOnLoadCallback(drawChart);   
                                          function drawChart() {
                                          
                                                   var data = google.visualization.arrayToDataTable([
                                                      ['Task', 'Hours per Day'],
                                                      <?php 			
                                             foreach($applicationOnRequisition as $aor){								
                                                //echo "['".$cj->name."("..$cj->name.")',  "..$cj->name."],";  jobCounter
                                                echo "['".$aor->title."-".$aor->applicationCounter.""."',     ".$aor->applicationCounter."],";
                                             }	
                                             ?>                  
                                             ]);
                                          
                                          var options = {
                                          // title          :  Applicant on Requisitions, 
                                          // legend			: 'none',
                                          // pieSliceText	: 'label',
                                          // pieStartAngle  : 250,                                  
                                          chart: {
                                             width : 380,
                                             type  : 'pie',
                                          },
                                          legendTextStyle:  
                                          {
                                             fontName : "Poppins,Helvetica,sans-serif!important",
                                             fontSize : 13,
                                             textTransform: "capitalize",
                                             bold  : false,
                                             is3D: true,
                                             'width':400,
                                             'height':300
                                          },
                                          chartArea   :
                                          {
                                             left:0,
                                             top:10,
                                             right:0,
                                             bottom:10,
                                             width:"100%",
                                             height:"100%"
                                          },
                                          colors: [evaluation, joined, screening, rejected, inreview, sourcing, negotation, offer],
                                          // colors: [inreview, sourcing, screening, evaluation, negotation, offer, joined, rejected],
                                          fontName: "Poppins,Helvetica,sans-serif",
                                          backgroundColor: "transparent",
                                          fontSize: 13,
                                          bold: true
                                          };

                                          var chart = new google.visualization.PieChart(document.getElementById('applicationOnRequisition1'));
                                          chart.draw(data, options);
                                          }
                                       </script>
                                       <div id="applicationOnRequisition1" style=""></div>
                                       <?php 
                                          }
                                          else
                                          {
                                             
                                          ?>
                                       <div class="" id="piechart_client_jobs1" style="">
                                          <img src="<?php echo asset('img/blank_pie_chart1.png'); ?>" class="img-responsive img-fluid h-100" style="height: 171px!important;">
                                       </div>
                                       <?php 
                                          }
                                          ?>
                                    </div>
                                    <!--end::Chart-->                 
                                 </div>
                              </div>
                              <!--end::Card-->
                           </div>
                           <div class="col-lg-4">
                              <!--begin::Card-->
                              <div class="card card-custom gutter-b bg-light h-100">
                                 <div class="card-header border-bottom-dark1">
                                    <div class="card-title">
                                       <h3 class="card-label">Assessment Stage</h3>
                                    </div>
                                 </div>
                                 <div class="card-body">                 
                                 <!--begin::Chart-->
                                 <div id="" class="d-flex justify-content-center">
                                    <?php 
                                       if($assesmentStageCount > 0)
                                       {
                                       
                                       ?>
                                    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
                                    <script type="text/javascript">
                                       google.charts.load('current', {'packages':['corechart']});
                                       google.charts.setOnLoadCallback(drawChart);   
                                       function drawChart() {
                                       
                                       var data = google.visualization.arrayToDataTable([
                                       ['Task', 'Hours per Day'],
                                       
                                       <?php 			
                                          foreach($assesmentStage as $as){			
                                             //echo "['".$cj->name."("..$cj->name.")',  "..$cj->name."],";  jobCounter
                                             echo "['".$as->name."-".$as->applicationCounter.""."',     ".$as->applicationCounter."],";
                                          }	
                                          ?>                                    
                                       ]);                                    
                                       var options = {
                                       // title          :  Assessment Stage, 
                                       // legend			: 'none',
                                       // pieSliceText	: 'label',
                                       // pieStartAngle  : 250,                                  
                                       chart:
                                       {
                                          width : 380,
                                          type  : 'pie',
                                       },
                                       legendTextStyle:  
                                       {
                                          fontName : "Poppins,Helvetica,sans-serif!important",
                                          fontSize : 13,
                                          textTransform: "capitalize",
                                          bold  : false,
                                          is3D: true,
                                          'width':400,
                                          'height':300
                                       },
                                       chartArea   :
                                       {
                                          left:0,
                                          top:10,
                                          right:0,
                                          bottom:10,
                                          width:"100%",
                                          height:"100%"
                                       },                                    
                                       colors: [inreview, sourcing, screening, negotation, offer, joined, rejected, evaluation],
                                       // colors: [inreview, sourcing, screening, evaluation, negotation, offer, joined, rejected],
                                       fontName: "Poppins,Helvetica,sans-serif",
                                       backgroundColor: "transparent",
                                       fontSize: 13,
                                       bold: true
                                       };
                                       
                                       var chart = new google.visualization.PieChart(document.getElementById('piechart_static_22'));
                                       
                                       chart.draw(data, options);
                                       }
                                    </script> 
                                    <div id="piechart_static_22" style=""></div>
                                    <?php 
                                       }
                                       else
                                       {
                                          
                                       ?>
                                    <div class="" id="piechart_client_jobs1" style="">
                                       <img src="<?php echo asset('img/blank_pie_chart1.png'); ?>" class="img-responsive img-fluid h-100" style="    height: 171px!important;">
                                    </div>
                                    <?php 
                                       }
                                       ?>
                                 </div>
                                 <!--end::Chart-->  
                                 </div>
                              </div>
                              <!--end::Card-->
                           </div>
                        </div>            
                     </div>
                     <!--end::Card body-->
                  </div>
                  <!--end::Card-->
                  </div>  
               </div>
               <!--end::Container-->
            </div>
            <!--end::Charts Row-1-->

            <!--begin::Chart old row-1-->
            <div class="col-12 allRequisition_titleBg d-none" id="">
               <h4 class="my-0"><i class="fa fa-briefcase"></i>&nbsp;All Requisitions</h4>
            </div>
            <div class="row d-none">
               <div class="col-md-4">
                  <?php 
                     if($clientJobCount > 0)
                     {
                     
                     ?>
                  <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
                  <script type="text/javascript">
                     google.charts.load("current", {packages:["corechart"]});
                     google.charts.setOnLoadCallback(drawChart);
                     function drawChart() {
                     
                     var data = google.visualization.arrayToDataTable([
                      ['Task', 'Hours per Day'],
                     <?php 			
                        foreach($clientJob as $cj){								
                        	//echo "['".$cj->name."("..$cj->name.")',  "..$cj->name."],";  jobCounter
                        	echo "['".$cj->name."(".$cj->jobCounter.")"."',     ".$cj->jobCounter."],";
                        }	
                        ?>      
                     ]);
                     var options = {
                     title: "Client's All Requisition",
                     // legend			: 'none',
                     pieSliceText	: 'label', 
                     pieStartAngle  : 250,
                     legendTextStyle:  {fontName: "Poppins",fontSize: 12,textTransform: "capitalize",bold: false},
                     chartArea   :{left:10,top:40, right:10, bottom:10, width:"100%",height:"100%"},
                     height      : 210,
                     // width    : 385,
                     width    : "33.333333%",
                     fontName: "Poppins",
                     backgroundColor: "whitesmoke",
                     titleTextStyle: {fontName: '"Poppins"',fontSize: 18,bold: false},
                     textStyle:  {fontName: '"Poppins"',fontSize: 18,bold: false}
                     };
                     var chart = new google.visualization.PieChart(document.getElementById('piechart_client_jobs'));
                     chart.draw(data, options);
                     }
                  </script>
                  <div class="" id="piechart_client_jobs" style=""></div>
                  <?php 
                     }
                     else
                     {                  	
                     ?>
                  <!-- <p style="padding: 3px 0px 10px 10px;;" class="interview_process"> Client's All Requisition </p> -->
                  <div class="" id="piechart_client_jobs" style="">
                     <img src="<?php echo asset('img/contribution.png'); ?>" class="img-responsive img-fluid h-100" style="    height: 171px!important;">
                  </div>
                  <?php 
                     }
                     ?>
               </div>
               <div class="col-md-4">
                  <?php 
                     if($applicationOnRequisitionCount > 0)
                     {
                     ?>
                  <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
                  <script type="text/javascript">
                     google.charts.load('current', {'packages':['corechart']});
                     google.charts.setOnLoadCallback(drawChart);   
                     function drawChart() {
                     
                     			 var data = google.visualization.arrayToDataTable([
                     			   ['Task', 'Hours per Day'],
                     				<?php 			
                        foreach($applicationOnRequisition as $aor){								
                        	//echo "['".$cj->name."("..$cj->name.")',  "..$cj->name."],";  jobCounter
                        	echo "['".$aor->title."(".$aor->applicationCounter.")"."',     ".$aor->applicationCounter."],";
                        }	
                        ?>                  
                     	]);
                     
                     var options = {
                     title: 'Application On Requisition',
                     // legend			: 'none',
                     pieSliceText	: 'label', 
                     pieStartAngle  : 250,
                     legendTextStyle:  {fontName: "Poppins",fontSize: 12,textTransform: "capitalize",bold: false},
                     chartArea   :{left:10,top:40, right:10, bottom:10, width:"100%",height:"100%"},
                     height      : 210,
                     // width    : 385,
                     width    : "33.333333%",
                     fontName: "Poppins",
                     backgroundColor: "whitesmoke",
                     titleTextStyle: {fontName: '"Poppins"',fontSize: 18,bold: false},
                     textStyle:  {fontName: '"Poppins"',fontSize: 18,bold: false}
                     };
                     var chart = new google.visualization.PieChart(document.getElementById('applicationOnRequisition'));
                     chart.draw(data, options);
                     }
                  </script>
                  <div id="applicationOnRequisition" style=""></div>
                  <?php 
                     }
                     else
                     {
                     	
                     ?>
                  <!-- <p style="padding: 3px 0px 10px 10px;" class="interview_process"> Application On Requisition </p> -->
                  <div class="" id="piechart_client_jobs" style="">
                     <img src="<?php echo asset('img/contribution.png'); ?>" class="img-responsive img-fluid h-100" style="    height: 171px!important;">
                  </div>
                  <?php 
                     }
                     ?>  				  
               </div>
               <div class="col-md-4">
                  <!-- <p style="padding: 10px 0px 10px 10px;font-weight: 300;" class="contribution">Assessment Stage</p> -->
                  <?php 
                     if($assesmentStageCount > 0)
                     {
                     
                     ?>
                  <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
                  <script type="text/javascript">
                     google.charts.load('current', {'packages':['corechart']});
                     google.charts.setOnLoadCallback(drawChart);   
                     function drawChart() {
                     
                     var data = google.visualization.arrayToDataTable([
                      ['Task', 'Hours per Day'],
                      
                      <?php 			
                        foreach($assesmentStage as $as){								
                        	//echo "['".$cj->name."("..$cj->name.")',  "..$cj->name."],";  jobCounter
                        	echo "['".$as->name."(".$as->applicationCounter.")"."',     ".$as->applicationCounter."],";
                        }	
                        ?>    
                      
                     ]);
                     
                     var options = {
                     			title: 'Assessment Stage',
                     			// legend		:	'none',
                     			pieSliceText	:	'label',
                     			pieStartAngle  	:	250,
                     			legendTextStyle	:  {fontName: "Poppins",fontSize: 12,textTransform: "capitalize",bold: false},
                     			
                     			chartArea   	:	{left:10,top:40, right:0, bottom:10, width:"100%",height:"100%"},
                     			height      	:	210,
                     			// width    	:	385, 
                     			width    		:	"33.333333%",
                     			fontName		:	"Poppins",
                     			backgroundColor	:	"whitesmoke",
                     			
                     			titleTextStyle	:	{fontName: '"Poppins"',fontSize: 18,bold: false},
                     			textStyle		:	{fontName: '"Poppins"',fontSize: 18,bold: false},
                     			colors			:	[
                     								
                     	<?php ?> 
                              '#737373', '#ff4098', '#ff0202', '#f3b49f', '#f6c7b6', '#f6c7b6', '#f6c7b6'                           
                           ]       
                     	};
                     
                     var chart = new google.visualization.PieChart(document.getElementById('piechart_static_2'));
                     
                     chart.draw(data, options);
                     }
                  </script> 
                  <div id="piechart_static_2" style=""></div>
                  <?php 
                     }
                     else
                     {
                     	
                     ?>
                  <!-- <p style="padding: 3px 0px 10px 10px;" class="interview_process"> Assessment Stage </p> -->
                  <div class="" id="piechart_client_jobs" style="">
                     <img src="<?php echo asset('img/contribution.png'); ?>" class="img-responsive img-fluid h-100" style="    height: 171px!important;">
                  </div>
                  <?php 
                     }
                     ?>  
               </div>
            </div>
            <!--end::Chart old row-1-->
         </div>
      </div>
   </div>
</div>

<?php
/*STEP FILTER 
1.	CURRENT USER 		//$user_id			=	$user_data->id;
2.	CLIENT 				//	clients
3.	JOBS				   //	jobs
4.	INTERVIEW STAGE	//	master_status_id
*/

$i=0;
if(!empty($teamWorkArr))
{
	foreach($teamWorkArr as $user_data)
	{
		$user_id			=	'';
		$user_id			=	$user_data->id;			
	
	// FILTER	
	if($clients == '' || $clients == 'all'){
		$clients	=	'all';
	}
	
   // INDIVIDUAL CONTRIBUTION 
	$contributionArr 		   = 	array();
	$contributionArrCount 	=	DB::table('job_applications');
	$contributionArrCount 	=	$contributionArrCount->join('jobs', 'jobs.id', '=', 'job_applications.job_id');	
	$contributionArrCount 	=	$contributionArrCount->join('clients', 'clients.id', '=', 'jobs.client_id');
	$contributionArrCount 	=	$contributionArrCount->join('users', 'users.id', '=', 'job_applications.submitted_by');	
	$contributionArrCount 	=	$contributionArrCount->select('clients.name as client_name', 'jobs.title as job_title', 'jobs.id as job_id', DB::raw("COUNT(job_applications.id) as application_counter") );
	$contributionArrCount 	=	$contributionArrCount->where('job_applications.submitted_by', $user_id);
	
	// FILTER
	if($clients !='all' && $clients > 0)
   {
      // CLIENTS
		$contributionArrCount 	=	$contributionArrCount->where('clients.id', '=',  $clients);	
	}
	if($jobs !='all' && $jobs > 0)
   {
      // JOBS
		$contributionArrCount 	=	$contributionArrCount->where('jobs.id', '=',  $jobs);	
	}
	if($master_status_id !='all' && $master_status_id > 0)
   {
      // INTERVIEW STAGE
		$contributionArrCount 	=	$contributionArrCount->where('job_applications.master_status_id', '=',  $master_status_id);	
	}
	  // DATE START , END DATE
	  $contributionArrCount 	=	$contributionArrCount->whereBetween('job_applications.created_at',  [$start_date, $end_date]);	
	    
	// CUSTOM TEXT  
	if(!empty($custom_text) && $custom_text != 'none' ){
		$contributionArrCount 	=	$contributionArrCount->where('clients.name', 'like', '%' . $custom_text . '%')
									->orWhere('jobs.title', 'like', '%' . $custom_text . '%')                       
									->orWhere('users.name', 'like', '%' . $custom_text . '%');
	} 
	
	$contributionArrCount 	=	$contributionArrCount->groupBy('job_applications.job_id');			
	$contributionArrCount 	=	$contributionArrCount->count();	
				
	if($contributionArrCount  > 0)
	{											
		$contributionArr 	=	DB::table('job_applications');
		$contributionArr 	=	$contributionArr->join('jobs', 'jobs.id', '=', 'job_applications.job_id');		
		$contributionArr 	=	$contributionArr->join('clients', 'clients.id', '=', 'jobs.client_id');	
		$contributionArr 	=	$contributionArr->join('users', 'users.id', '=', 'job_applications.submitted_by');	
		$contributionArr 	=	$contributionArr->select('clients.name as client_name', 'jobs.title as job_title', 'jobs.id as job_id', DB::raw("COUNT(job_applications.id) as application_counter") );
		$contributionArr 	=	$contributionArr->where('job_applications.submitted_by', $user_id);
		
		// FILTER
		if($clients !='all' && $clients > 0)
      {
         // CLIENT
			$contributionArr 	=	$contributionArr->where('clients.id', '=', $clients);	
		}
		if($jobs !='all' && $jobs > 0){
         // JOBS
			$contributionArr 	=	$contributionArr->where('jobs.id', '=', $jobs);	
		}
		if($master_status_id !='all' && $master_status_id > 0)
      {
         // INTERVIEW STAGE
			$contributionArr 	=	$contributionArr->where('job_applications.master_status_id', '=', $master_status_id);	
		}
	   // DATE START , END DATE
		$contributionArr 	=	$contributionArr->whereBetween('job_applications.created_at',  [$start_date, $end_date]);	
		
		// CUSTOM TEXT  
		if(!empty($custom_text) && $custom_text != 'none' )
      {
			$contributionArr 		=	$contributionArr->where('clients.name', 'like', '%' . $custom_text . '%')
			->orWhere('jobs.title', 'like', '%' . $custom_text . '%')                       
			->orWhere('users.name', 'like', '%' . $custom_text . '%');
		} 
	
		$contributionArr 	=	$contributionArr->groupBy('job_applications.job_id');			
		$contributionArr 	=	$contributionArr->get();	
	}
   // INTERVIEW SCHEDULE
	$interviewScheduleArr 		   = 	array();
	$interviewScheduleArrCount 	=	DB::table('interview_schedules');
	$interviewScheduleArrCount 	=	$interviewScheduleArrCount->join('job_applications', 'job_applications.id', '=', 'interview_schedules.job_application_id');		
	$interviewScheduleArrCount 	=	$interviewScheduleArrCount->join('jobs', 'jobs.id', '=', 'job_applications.job_id');
	$interviewScheduleArrCount 	=	$interviewScheduleArrCount->join('clients', 'clients.id', '=', 'jobs.client_id');	
	$interviewScheduleArrCount 	=	$interviewScheduleArrCount->join('users', 'users.id', '=', 'job_applications.submitted_by');	
   $interviewScheduleArrCount 	=	$interviewScheduleArrCount->select( 'clients.name as client_name', 'jobs.title as job_title', 'jobs.id as job_id', DB::raw("COUNT(interview_schedules.id) as interview_count") );
	$interviewScheduleArrCount	=	$interviewScheduleArrCount->where('job_applications.submitted_by', $user_id);
	// FILTER
	if($clients !='all' && $clients > 0)
   {
      // CLIENT
		$interviewScheduleArrCount 	=	$interviewScheduleArrCount->where('clients.id', '=', $clients);
	}
	if($jobs !='all' && $jobs > 0)
   {
      // JOBS
		$interviewScheduleArrCount 	=	$interviewScheduleArrCount->where('jobs.id', '=', $jobs);
	}
	if($master_status_id !='all' && $master_status_id > 0)
   {
      // INTERVIEW STAGE
		$interviewScheduleArrCount 	=	$interviewScheduleArrCount->where('job_applications.master_status_id', '=', $master_status_id);
	}																		
	// DATE START , END DATE
	$interviewScheduleArrCount 		=	$interviewScheduleArrCount->whereBetween('interview_schedules.created_at',  [$start_date, $end_date]); 
	// CUSTOM TEXT  
	if(!empty($custom_text) && $custom_text != 'none' )
   {
		$interviewScheduleArrCount 		=	$interviewScheduleArrCount->where('clients.name', 'like', '%' . $custom_text . '%')
		->orWhere('jobs.title', 'like', '%' . $custom_text . '%')                       
		->orWhere('users.name', 'like', '%' . $custom_text . '%');
	}    		 
	$interviewScheduleArrCount 		=	$interviewScheduleArrCount->groupBy('job_applications.job_id');			
	$interviewScheduleArrCount 		=	$interviewScheduleArrCount->count();	
	if($interviewScheduleArrCount  > 0)
	{											
		$interviewScheduleArr 	=	DB::table('interview_schedules');
		$interviewScheduleArr 	=	$interviewScheduleArr->join('job_applications', 'job_applications.id', '=', 'interview_schedules.job_application_id');		
		$interviewScheduleArr 	=	$interviewScheduleArr->join('jobs', 'jobs.id', '=', 'job_applications.job_id');
		$interviewScheduleArr 	=	$interviewScheduleArr->join('clients', 'clients.id', '=', 'jobs.client_id');	
		$interviewScheduleArr 	=	$interviewScheduleArr->join('users', 'users.id', '=', 'job_applications.submitted_by');
	   $interviewScheduleArr 	=	$interviewScheduleArr->select( 'clients.name as client_name', 'jobs.title as job_title', 'jobs.id as job_id', DB::raw("COUNT(interview_schedules.id) as interview_count") );				
		$interviewScheduleArr	=	$interviewScheduleArr->where('job_applications.submitted_by', $user_id);
		// FILTER
		if($clients !='all' && $clients > 0)
      {
         // CLIENT
			$interviewScheduleArr 	=	$interviewScheduleArr->where('clients.id', '=',  $clients);		
		}
		if($jobs !='all' && $jobs > 0)
      {
         // JOBS
			$interviewScheduleArr 	=	$interviewScheduleArr->where('jobs.id', '=',  $jobs);		
		}
		if($master_status_id !='all' && $master_status_id > 0){																								// INTERVIEW STAGE
			$interviewScheduleArr 	=	$interviewScheduleArr->where('job_applications.master_status_id', '=',  $master_status_id);
		}
      // DATE START , END DATE
		$interviewScheduleArr 		=	$interviewScheduleArr->whereBetween('interview_schedules.created_at',  [$start_date, $end_date]);   		
		// CUSTOM TEXT  
		if(!empty($custom_text) && $custom_text != 'none' ){
			$interviewScheduleArr 	=	$interviewScheduleArr->where('clients.name', 'like', '%' . $custom_text . '%')
			->orWhere('jobs.title', 'like', '%' . $custom_text . '%')                       
			->orWhere('users.name', 'like', '%' . $custom_text . '%'); 
		}    	
		$interviewScheduleArr 		=	$interviewScheduleArr->groupBy('job_applications.job_id');		
		$interviewScheduleArr 		=	$interviewScheduleArr->get();	   
	}

   // INTERVIEW PROCESS
	$interviewProcessArr 		= 	array();
	$interviewProcessArrCount 	=	DB::table('job_applications');
	$interviewProcessArrCount	=	$interviewProcessArrCount->join('master_status', 'master_status.id', '=', 'job_applications.master_status_id'); 
	$interviewProcessArrCount	=	$interviewProcessArrCount->join('jobs', 'jobs.id', '=', 'job_applications.job_id'); 
	$interviewProcessArrCount	=	$interviewProcessArrCount->join('clients', 'clients.id', '=', 'jobs.client_id');
	$interviewProcessArrCount 	=	$interviewProcessArrCount->join('users', 'users.id', '=', 'job_applications.submitted_by');	
	$interviewProcessArrCount	=	$interviewProcessArrCount->select( 'master_status.name as process_name', DB::raw("COUNT(job_applications.id) as interview_process_count") );
	$interviewProcessArrCount	=	$interviewProcessArrCount->where('job_applications.submitted_by', $user_id);
	
	// FILTER
	if($clients !='all' && $clients > 0)
   {
      // CLIENT
		$interviewProcessArrCount	=	$interviewProcessArrCount->where('clients.id', '=', $clients);	
	}
	if($jobs !='all' && $jobs > 0)
   {
      //	JOBS
		$interviewProcessArrCount	=	$interviewProcessArrCount->where('jobs.id', '=', $jobs);	
	}
	if($master_status_id !='all' && $master_status_id > 0)
   {	
      //	INTERVIEW STAGE
		$interviewProcessArrCount	=	$interviewProcessArrCount->where('job_applications.master_status_id', '=', $master_status_id);
   }
	// START DATE , END DATE
	$interviewProcessArrCount 	=	$interviewProcessArrCount->whereBetween('job_applications.created_at',  [$start_date, $end_date]);
	// CUSTOM TEXT  
	if(!empty($custom_text) && $custom_text != 'none' )
   {
		$interviewProcessArrCount 	=	$interviewProcessArrCount->where('clients.name', 'like', '%' . $custom_text . '%')
		->orWhere('jobs.title', 'like', '%' . $custom_text . '%')                       
		->orWhere('users.name', 'like', '%' . $custom_text . '%'); 
   }
	$interviewProcessArrCount	=	$interviewProcessArrCount->groupBy('job_applications.master_status_id')	;		
	$interviewProcessArrCount	=	$interviewProcessArrCount->count();
	if($interviewProcessArrCount  > 0)
	{											
		$interviewProcessArr 	=	DB::table('job_applications');
		$interviewProcessArr 	=	$interviewProcessArr->join('master_status', 'master_status.id', '=', 'job_applications.master_status_id'); 
		$interviewProcessArr	=	$interviewProcessArr->join('jobs', 'jobs.id', '=', 'job_applications.job_id'); 
		$interviewProcessArr	=	$interviewProcessArr->join('clients', 'clients.id', '=', 'jobs.client_id');	
		$interviewProcessArr 	=	$interviewProcessArr->join('users', 'users.id', '=', 'job_applications.submitted_by');				
		$interviewProcessArr 	=	$interviewProcessArr->select( 'master_status.name as process_name', DB::raw("COUNT(job_applications.id) as interview_process_count") );
		$interviewProcessArr 	=	$interviewProcessArr->where('job_applications.submitted_by', $user_id);
		
		// FILTER
		if($clients !='all' && $clients > 0)
      {
         // CLIENT
			$interviewProcessArr =	$interviewProcessArr->where('clients.id', '=', $clients);	
		}
		if($jobs !='all' && $jobs > 0)
      {
         // JOBS
			$interviewProcessArr =	$interviewProcessArr->where('jobs.id', '=', $jobs);	
		}
		if($master_status_id !='all' && $master_status_id > 0)
      {
         // INTERVIEW STAGE
			$interviewProcessArr =	$interviewProcessArr->where('job_applications.master_status_id', '=', $master_status_id);	
		}
		// START DATE , END DATE
		$interviewProcessArr 	=	$interviewProcessArr->whereBetween('job_applications.created_at',  [$start_date, $end_date]);
		// CUSTOM TEXT  
		if(!empty($custom_text) && $custom_text != 'none' )
      {
			$interviewProcessArr 	=	$interviewProcessArr->where('clients.name', 'like', '%' . $custom_text . '%')
			->orWhere('jobs.title', 'like', '%' . $custom_text . '%')                       
			->orWhere('users.name', 'like', '%' . $custom_text . '%'); 
		} 
		$interviewProcessArr 	=	$interviewProcessArr->groupBy('job_applications.master_status_id');			
		$interviewProcessArr 	=	$interviewProcessArr->get();	   
	}
?>
<div class="row mt-3-minus">
   <div class="col-md-12">
      <div class="card shadow-none bg-transparent">
         <div class="card-body">   
            <!--begin::Charts Row-2-->
            <div class="d-flex flex-column-fluid">
               <!--begin::Container-->
               <div class="container-fluid">
                  <div class="row">        
                  <!--begin::Card-->
                  <div class="card card-custom mb-0 px-0 shadow-none">
                     <div class="card-header">
                        <div class="card-title">
                        <h3 class="card-label fs-2">Recruiter</h3>
                        </div>
                     </div>
                     <!--begin::Card body-->
                     <div class="card-body px-3 py-3">
                        <div class="row row-eq-height">
                           <div class="col-lg-4">
                              <!--begin::Card-->
                              <div class="card card-custom gutter-b bg-light h-100">
                                 <div class="card-header border-bottom-dark1">
                                    <div class="card-title">
                                       <h3 class="card-label"><?php echo ucwords(substr(@$user_data->user_name, 0, 10));?>-Contribution</h3>
                                    </div>
                                 </div>
                                 <div class="card-body">                                    
                                    <!--begin::Chart-->
                                    <div id="" class="d-flex justify-content-center">
                                       <?php 
                                          $contribute_image	=	'';
                                          if($contributionArrCount  > 0)
                                          { 	
                                       ?>  
                                       <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
                                       <script type="text/javascript">
                                          google.charts.load("current", {packages:["corechart"]});
                                          google.charts.setOnLoadCallback(drawChart);
                                          function drawChart() {
                                          var data = google.visualization.arrayToDataTable([
                                             ['Language', 'Speakers (in millions)'],		  
                                          <?php 
                                             foreach($contributionArr as $ic){
                                                
                                                $custom_string	=	'';  //substr() strtoupper( ucfirst( ucwords(  
                                                $custom_string	=	strtoupper(substr($ic->client_name, 0, 2)).' | '.ucfirst(substr($ic->job_title, 0, 2)).'-'.$ic->job_id.' | '.''.$ic->application_counter.' Cv'; 
                                                echo "['".$custom_string."',".$ic->application_counter."],";
                                             }	
                                             ?> 
                                          ]);
                                          
                                          var options =
                                          {
                                             // title: '<?php echo ucwords(substr(@$user_data->user_name, 0, 8));?> - Contribution',
                                             // legend         : 'none',
                                             // pieSliceText   : 'label',
                                             // pieStartAngle  : 250,
                                             chart: {
                                                width : 380,
                                                type  : 'pie',
                                             },
                                             legendTextStyle:  
                                             {
                                                fontName : "Poppins,Helvetica,sans-serif!important",
                                                fontSize : 13,
                                                textTransform: "capitalize",
                                                bold  : false,
                                                is3D: true,
                                                'width':400,
                                                'height':300
                                             },
                                             chartArea   :
                                             {
                                                left:0,
                                                top:10,
                                                right:0,
                                                bottom:10,
                                                width:"100%",
                                                height:"100%"
                                             },
                                             colors: [negotation, screening, offer, evaluation, sourcing, rejected, inreview, joined],
                                             // colors: [inreview, sourcing, screening, evaluation, negotation, offer, joined, rejected],
                                             fontName: "Poppins,Helvetica,sans-serif",
                                             backgroundColor: "transparent",
                                             fontSize: 13,
                                             bold: true
                                          };
                                          
                                          var chart = new google.visualization.PieChart(document.getElementById('piechart_contribution_to_teamwork_<?php echo $i;?>'));
                                          chart.draw(data, options);
                                          }
                                       </script>
                                       <?php  
                                          }
                                          else
                                          {
                                             $contribute_image	=	'
                                             <img src="'.asset('img/blank_pie_chart1.png').'" class="img-responsive img-fluid h-100" style="">
                                          ';	
                                          }
                                       ?>
                                       <div id="piechart_contribution_to_teamwork_<?php echo $i;?>" style=""><?php echo @$contribute_image;  ?></div>
                                    </div>
                                    <!--end::Chart-->
                                 </div>
                              </div>
                              <!--end::Card-->                           
                           </div>
                           <div class="col-lg-4">
                              <!--begin::Card-->
                              <div class="card card-custom gutter-b bg-light h-100">
                                 <div class="card-header border-bottom-dark1">
                                    <div class="card-title">
                                       <h3 class="card-label"><?php echo ucwords(substr(@$user_data->user_name, 0, 10));?>-Interview Schedule</h3>
                                    </div>
                                 </div>
                                 <div class="card-body">                                 
                                    <!--begin::Chart-->
                                    <div id="" class="d-flex justify-content-center">
                                       <?php 
                                          $interview_schedule_image	=	'';
                                          if($interviewScheduleArrCount  > 0)
                                          {
                                       ?>  
                                       <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
                                       <script type="text/javascript">
                                          google.charts.load("current", {packages:["corechart"]});
                                          google.charts.setOnLoadCallback(drawChart);
                                          function drawChart() {
                                          var data = google.visualization.arrayToDataTable([
                                             ['Language', 'Speakers (in millions)'],
                                                <?php 
                                             foreach($interviewScheduleArr as $is)
                                             {                     	
                                                $custom_string	=	'';
                                                $custom_string	=	strtoupper(substr($is->client_name, 0, 2)).' | '.ucfirst(substr($is->job_title, 0, 2)).'-'.$is->job_id.''.' | '.$is->interview_count.' INT'; 
                                                echo "['".$custom_string."',  ".$is->interview_count."],";
                                             }	
                                             ?>
                                          ]);
                                          
                                          var options =
                                          {                  
                                             // legend         : 'none',
                                             // pieSliceText   : 'label',
                                             // pieStartAngle  : 250,                           
                                             chart:
                                             {
                                                width : 380,
                                                type  : 'pie',
                                             },
                                             legendTextStyle: 
                                             {
                                                fontName: "Poppins,Helvetica,sans-serif",
                                                fontSize: 13,
                                                textTransform: "capitalize",
                                                bold: false
                                             },
                                             chartArea   :
                                             {
                                                left:0,
                                                top:10,
                                                right:0,
                                                bottom:10,
                                                width:"100%",
                                                height:"100%"
                                             },
                                             colors: [evaluation, joined, screening, rejected, inreview, sourcing, negotation, offer],
                                             // colors: [inreview, sourcing, screening, evaluation, negotation, offer, joined, rejected],
                                             fontName: "Poppins,Helvetica,sans-serif",
                                             backgroundColor: "transparent",
                                             fontSize: 13,
                                             bold: true
                                          };                  
                                          var chart = new google.visualization.PieChart(document.getElementById('piechart_interview_schedule1_<?php echo $i;?>'));
                                          chart.draw(data, options);
                                          }
                                       </script>
                                       <?php
                                          }
                                          else
                                          {
                                             $interview_schedule_image	=	'<img src="'.asset('img/blank_pie_chart1.png').'" class="img-responsive img-fluid h-100"  style="">';	
                                          }
                                          ?>		 			 
                                       <div id="piechart_interview_schedule1_<?php echo $i;?>" style=""><?php echo @$interview_schedule_image;  ?></div>
                                    </div>
                                    <!--end::Chart-->
                                 </div>
                              </div>
                              <!--end::Card-->
                           </div>
                           <div class="col-lg-4">
                              <!--begin::Card-->
                              <div class="card card-custom gutter-b bg-light h-100">
                                 <div class="card-header border-bottom-dark1">
                                    <div class="card-title">
                                       <h3 class="card-label"><?php echo ucwords(substr(@$user_data->user_name, 0, 10));?>-Interview Process</h3>
                                    </div>
                                 </div>
                                 <div class="card-body">                                    
                                    <!--begin::Chart-->
                                    <div id="" class="d-flex justify-content-center">
                                       <?php 
                                          $interview_process_image	=	'';
                                          if($interviewProcessArrCount  > 0)
                                          {
                                       ?> 
                                       <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
                                       <script type="text/javascript">
                                          google.charts.load("current", {packages:["corechart"]});
                                          google.charts.setOnLoadCallback(drawChart);
                                          function drawChart() {
                                          var data = google.visualization.arrayToDataTable([
                                             ['Language', 'Speakers (in millions)'],
                                          <?php 
                                             foreach($interviewProcessArr as $ip){							
                                                $custom_string		=	'';
                                                $custom_string		=	strtoupper(substr($ip->process_name, 0, 3)).''.'-'.$ip->interview_process_count.'' ; 
                                                echo "['".$custom_string."', ".$ip->interview_process_count."],";
                                             }	
                                             ?>
                                          ]);
                                          
                                          var options = {
                                             // title			: 'Interview Process',
                                             // legend			: 'none',
                                             // pieSliceText	: 'label',
                                             // pieStartAngle	: 250,                                 
                                             chart:
                                             {
                                                width : 380,
                                                type  : 'pie',
                                             },
                                             legendTextStyle: 
                                             {
                                                fontName: "Poppins,Helvetica,sans-serif!important",
                                                fontSize: 13,
                                                textTransform: "capitalize",
                                                bold: false
                                             },              
                                             chartArea   :
                                             {
                                                left:0,
                                                top:10,
                                                right:10,
                                                bottom:10,
                                                width:"100%",
                                                height:"100%"
                                             },
                                             colors: [sourcing, screening, negotation, offer, joined, evaluation],
                                             // colors: [inreview, sourcing, screening, evaluation, negotation, offer, joined, rejected],
                                             fontName: "Poppins,Helvetica,sans-serif!important",
                                             backgroundColor: "transparent",
                                             fontSize: 13,
                                             bold: true
                                          };
                                          
                                          var chart = new google.visualization.PieChart(document.getElementById('piechart_interview_process_<?php echo $i;?>'));
                                          chart.draw(data, options);
                                          }
                                       </script>	
                                       <?php  
                                          }else
                                          {
                                             $interview_process_image	=	'<img src="'.asset('img/blank_pie_chart1.png').'" class="img-responsive img-fluid h-100" style="">';	
                                          }
                                          ?>               
                                       <div id="piechart_interview_process_<?php echo $i;?>" style=""><?php echo @$interview_process_image;  ?></div>
                                    </div>   
                                    <!--end::New chart-->
                                 </div>
                              </div>
                              <!--end::Card-->
                           </div>
                        </div>            
                     </div>
                     <!--end::Card body-->
                  </div>
                  <!--end::Card-->
                  </div>  
               </div>
               <!--end::Container-->
            </div>
            <!--end::Charts Row-2-->

            <!--begin::Old chart row-2-->
            <div class="col-12 d-none">
               <div class="col-12 allRecruiter_titleBg" id="">
                  <h4 class="my-0"><i class="fa fa-users"></i>&nbsp;Recruiter</h4>
               </div>
               <div class="row">
                  <div class="col-md-4" id="">
                     <p style="padding: 10px 0px 10px 10px;font-weight: 300;" class="contribution">
                        <?php echo ucwords(substr(@$user_data->user_name, 0, 8));?> - Contribution
                     </p>
                     <?php 
                        $contribute_image	=	'';
                        if($contributionArrCount  > 0)
                        { 	
                     ?>  
                     <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
                     <script type="text/javascript">
                        google.charts.load("current", {packages:["corechart"]});
                        google.charts.setOnLoadCallback(drawChart);
                        function drawChart() {
                        var data = google.visualization.arrayToDataTable([
                           ['Language', 'Speakers (in millions)'],		  
                        <?php 
                           foreach($contributionArr as $ic){
                              
                              $custom_string	=	'';  //substr() strtoupper( ucfirst( ucwords(  
                              $custom_string	=	strtoupper(substr($ic->client_name, 0, 3)).' - '.ucfirst(substr($ic->job_title, 0, 3)).'('.$ic->job_id.')'.'  '.$ic->application_counter.'CV'; 
                              echo "['".$custom_string."',  ".$ic->application_counter."],";
                           }	
                           ?> 
                        ]);
                        
                        var options =
                        {
                        // title: '<?php echo ucwords(substr(@$user_data->user_name, 0, 8));?> - Contribution',
                           // legend         : 'none',
                           pieSliceText   : 'label', 
                           pieStartAngle  : 250,
                           legendTextStyle:  {fontName: "Poppins",fontSize: 12,textTransform: "capitalize",bold: false},           
                           chartArea   :{left:10,top:6, right:10, bottom:10, width:"100%",height:"100%"},
                           height      : 171,
                           // width    : 385,
                           width    : "33.333333%",
                           fontName: "Poppins",
                           backgroundColor: "whitesmoke",
                           titleTextStyle: {fontName: '"Poppins"',fontSize: 18,bold: false},
                           textStyle:  {fontName: '"Poppins"',fontSize: 18,bold: false}
                        };
                        
                        var chart = new google.visualization.PieChart(document.getElementById('piechart_individual_contribution_to_teamwork_<?php echo $i;?>'));
                        chart.draw(data, options);
                        }
                     </script>
                     <?php  
                        }
                        else
                        {
                           $contribute_image	=	'
                           <img src="'.asset('img/contribution.png').'" class="img-responsive img-fluid h-100" style="height: 171px!important;">
                        ';	
                        }
                     ?>
                     <div id="piechart_individual_contribution_to_teamwork_<?php echo $i;?>" style=""><?php echo @$contribute_image;  ?></div>
                  </div>
                  <div class="col-md-4" id="" style="">
                     <p style="" class="interview_process">
                        <?php echo ucwords(substr(@$user_data->user_name, 0, 8));?> - Interview Schedule
                     </p>
                     <?php 
                        $interview_schedule_image	=	'';
                        if($interviewScheduleArrCount  > 0)
                        {
                     ?>  
                     <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
                     <script type="text/javascript">
                        google.charts.load("current", {packages:["corechart"]});
                        google.charts.setOnLoadCallback(drawChart);
                        function drawChart() {
                        var data = google.visualization.arrayToDataTable([
                           ['Language', 'Speakers (in millions)'],
                              <?php 
                           foreach($interviewScheduleArr as $is)
                           {                     	
                              $custom_string	=	'';
                              $custom_string	=	strtoupper(substr($is->client_name, 0, 3)).' - '.ucfirst(substr($is->job_title, 0, 3)).'('.$is->job_id.')'.'  '.$is->interview_count.' INTV'; 
                              echo "['".$custom_string."',  ".$is->interview_count."],";
                           }	
                           ?>
                        ]);
                        
                        var options =
                        {                  
                           // legend         : 'none',
                           pieSliceText   : 'label', 
                           pieStartAngle  : 250,
                           legendTextStyle:  {fontName: "Poppins",fontSize: 12,textTransform: "capitalize",bold: false},
                           chartArea   :{left:10,top:40, right:10, bottom:10, width:"100%",height:"100%"},
                           height      : 210,
                           // width    : 385,
                           width    : "33.333333%",
                           fontName: "Poppins",
                           backgroundColor: "whitesmoke",                  
                           titleTextStyle: {fontName: '"Poppins"',fontSize: 18,bold: false},
                           textStyle:  {fontName: '"Poppins"',fontSize: 18,bold: false}
                        };                  
                        var chart = new google.visualization.PieChart(document.getElementById('piechart_individual_interview_schedule1_<?php echo $i;?>'));
                        chart.draw(data, options);
                        }
                     </script>	
                     <?php
                        }
                        else
                        {
                           $interview_schedule_image	=	'<img src="'.asset('img/contribution.png').'" class="img-responsive img-fluid h-100"  style="height: 171px!important;">';	
                        }
                        ?>		 			 
                     <div id="piechart_individual_interview_schedule1_<?php echo $i;?>" style=""><?php echo @$interview_schedule_image;  ?></div>
                  </div>
                  <div class="col-md-4" id="">
                     <p style="" class="interview_process">
                        <?php echo ucwords(substr(@$user_data->user_name, 0, 8));?> - Interview Process
                     </p>
                     <?php 
                        $interview_process_image	=	'';
                        if($interviewProcessArrCount  > 0)
                        {
                     ?> 
                     <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
                     <script type="text/javascript">
                        google.charts.load("current", {packages:["corechart"]});
                        google.charts.setOnLoadCallback(drawChart);
                        function drawChart() {
                        var data = google.visualization.arrayToDataTable([
                           ['Language', 'Speakers (in millions)'],
                        <?php 
                           foreach($interviewProcessArr as $ip){							
                              $custom_string		=	'';
                              $custom_string		=	strtoupper(substr($ip->process_name, 0, 3)).' - '.'('.$ip->interview_process_count.')' ; 
                              echo "['".$custom_string."',  ".$ip->interview_process_count."],";
                           }	
                           ?>
                        ]);
                        
                        var options = {
                        title			: '',
                        // legend			: 'none',
                        pieSliceText	: 'label',
                        pieStartAngle	: 250,    
                        legendTextStyle:  {fontName: "Poppins",fontSize: 12,textTransform: "capitalize",bold: false},                
                        chartArea   :{left:10,top:6, right:10, bottom:10, width:"100%",height:"100%"},
                        height      : 171,
                        // width    : 385,
                        width    : "33.333333%",
                        fontName: "Poppins",
                        backgroundColor: "whitesmoke",
                        titleTextStyle: {fontName: '"Poppins"',fontSize: 18,bold: false},
                        textStyle:  {fontName: '"Poppins"',fontSize: 18,bold: false}
                        };
                        
                        var chart = new google.visualization.PieChart(document.getElementById('piechart_individual_interview_process_<?php echo $i;?>'));
                        chart.draw(data, options);
                        }
                     </script>	
                     <?php  
                        }else{
                              $interview_process_image	=	'<img src="'.asset('img/contribution.png').'" class="img-responsive img-fluid h-100" style="height: 171px!important">';	
                        }
                        ?>               
                     <div id="piechart_individual_interview_process_<?php echo $i;?>" style=""><?php echo @$interview_process_image;  ?></div>
                  </div>
               </div>
            </div>
            <!--end::Old chart row-2-->
         </div>
      </div>
   </div>
</div>
<?php 
$i++;
}
} 
?>

<!--begin::Metronic theme charts-->
<!-- <script src="{{ asset('demo/v7/js/pages/features/charts/apexcharts.js')}}"></script> -->
<!--end::Metronic theme charts-->