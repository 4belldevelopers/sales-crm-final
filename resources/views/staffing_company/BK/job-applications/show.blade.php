<!-- CSS only -->
<!-- <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous"> -->
<!--<link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.7/css/bootstrap.min.css'>-->
<!--<link rel="stylesheet" href="{{ asset('assets/plugins/jquery-bar-rating-master/dist/themes/fontawesome-stars.css')}}">-->
<!--<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/fontawesome.min.css">-->
<style>

</style>
<!-- <div class="rpanel-title"><h5 class="title" style="padding: 0em .7em;"><i class="icon-plus"></i>Application Status Update<span><i class="ti-close text-white"></i></span></h5></div>   -->
<div class="rpanel-title"> <span class="pt-2"><i class="ti-close text-dark"></i></span>
  <div class="row">
    <div class="col-md-12 pl-2">
      <p class="pt-2 mb-0">
        <a onclick="createSchedule('{{$application->id}}')" href="javascript:void(0);" class="btn btn-light-danger btn-sm font-weight-500 p-1" style="">
          <i class="fa fa-calendar fa-sm"></i>&nbsp;@lang('modules.interviewSchedule.scheduleInterview')
        </a>
        <a class="d-none btn btn-light-info btn-sm font-weight-500 p-1" href="{{ route('admin.job-applications.download', $application->id) }}" target="_blank">
          <i class="fa fa-file fa-sm"></i>&nbsp;Resume
        </a>
        <a onclick="moveApplication('{{$application->id}}')" href="javascript:void(0);" class="btn btn-light-warning btn-sm font-weight-500 p-1" style="">
          <i class="fa fa-user fa-sm"></i>&nbsp;Move
        </a>
        <a onclick="copyApplication('{{$application->id}}')" href="javascript:void(0);" class="btn btn-light-dark btn-sm font-weight-500 p-1" style="">
          <i class="fa fa-files-o fa-sm"></i>&nbsp;Copy
        </a>
      </p>
    </div>
  </div>
</div>
<div class="p-2">
<!-- Tabs Section -->
<div class="row">
  <?php 

		//echo '<pre>'; print_r($application->master_status_id); exit; 
  //echo $created_at = GeneralMethod::ymdTodmy($application->created_at);
  
    //echo '<pre>'; print_r(@$created_at); 
  ?>
  <div  class="col-sm-12 col-md-12">
    <div class="row mb-0 pb-3 btm-bdr">
      <div class="col-md-12">
        <h4 class="mb-0 title-color">@lang('app.name') - {{ ucwords(@$application->full_name) }} <a href="<?php echo route('admin.job-applications.edit', [$application->id]); ?>" class="btn btn-light-danger btn-sm" style="padding: 0px!important;"> <span class="font-weight-400 px-1">Edit</span> <i class="fa fa-pencil fa-sm pr-1"></i> </a> </h4>
        <span class="position pull-left"> <a href="javascript:void(0);"><?php echo @$application->total_exp;?>&nbsp;Yrs&nbsp;|&nbsp;</a></span> <span class="position pull-left"> <a href="javascript:void(0);">Salary CTC&nbsp;-&nbsp;<?php echo @$application->ctc;?>/Exp. CTC-<?php echo @$application->ectc;?>&nbsp;|&nbsp; </a> </span> <span class="position pull-left"> <a href="javascript:void(0);">
        <?php 
           if($application->serving_notice_period_mode == 0){
            echo @$application->np_id.' Days'; 
           }else{
            echo @$application->np_id;  
           }
           
           
           ?>
        |</a> </span> <span class="position pull-left"> <a href="javascript:void(0);"><?php echo @$created_at; ?> |</a> </span> <span class="location pull-left"> <a href="javascript:void(0);"><?php echo @$application->current_location; ?></a> </span> </div>
    </div>
    <div class="row vh-100">
      <div class="left-section col-md-2 pr-0 px-0 bg-gray-custom pt-1">
        <ul class="nav nav-pills" role="tablist">
          <li class="nav-item nav-item1 mb-2"> <a class="nav-link nav-link1 active" data-toggle="pill" href="#candidate_information"> <i class="fa fa-server"></i>&nbsp;Details </a> </li>
          <li class="nav-item nav-item1 mb-2"> <a class="nav-link nav-link1" data-toggle="pill" href="#timeline"> <i class="fa fa-calendar"></i>&nbsp;Timeline </a> </li>
          <?php 
  			if($offer_job_applications_count > 0)
			{
  	?>
          <li class="nav-item nav-item1"> <a class="nav-link nav-link1" data-toggle="pill" href="#offertab"> <i class="fa fa-tags"></i>&nbsp;Offer Tracker </a> </li>
          <?php 
			}
	?>
        </ul>
      </div>
      <div class="right-section col-md-10" style="padding:0">
        <!-- Tab panes -->
        <div class="tab-content">
          <div class="tab-pane active pt-1" id="candidate_information">
            <div class="col-sm-12 px-0">
              <h4 class="text-dark border-bottom p-2 bg-light-secondary"> <i class="fa fa-user"></i>&nbsp;Applicant Information </h4>
            </div>
            <div class="row btm-bdr">
              <div class="col-md-6" style="padding:0">
                <ul class="list-group list-group-flush">
                  <li class="list-group-item">
                    <p class="font-weight-400 text-black" id="" style="margin:0!important;"> <i class="fa fa-briefcase text-dark"></i>&nbsp;Current Designation : <span class="text-dark"> <?php echo @$application->current_designation; ?></span> </p>
                  </li>
                  <li class="list-group-item">
                    <p class="font-weight-400 text-black" style="margin:0!important;"> <i class="fa fa-building text-dark"></i>&nbsp;Current Company : <span class="text-dark"> <?php echo @$application->current_company; ?></span> </p>
                  </li>
                  <li class="list-group-item">
                    <p class="font-weight-400 text-black" id="phone-{{ $application->id }}" style="margin:0!important;"> <i class="fa fa-phone text-dark"></i>&nbsp;Primary Contact Number : <span class="text-dark"> {{ (@$application->phone) ? @$application->phone : 'N/A' }} </span> </p>
                  </li>
                  <li class="list-group-item">
                    <p class="font-weight-400 text-black" id="email-{{ $application->id }}" style="margin:0!important;"><i class="fa fa-envelope text-dark"></i>&nbsp;Primary @lang('app.email') : <span class="text-dark"> {{ (@$application->email) ? @$application->email : 'xxxxxxxxxxxxx' }}</span> </p>
                  </li>
                  <li class="list-group-item">
                    <p class="font-weight-400 text-black" id="phone-{{ $application->id }}" style="margin:0!important;"><i class="fa fa-graduation-cap text-dark"></i>&nbsp;Education : <span class="text-dark"> {{ (@$qualification->name) ? @$qualification->name : 'N/A' }}</span> </p>
                  </li>
                  <li class="list-group-item">
                    <p class="font-weight-400 text-black" style="margin:0!important;"> <span class="ruppes">&#x20B9;</span>&nbsp;Salary CTC | Exp. CTC  : <span class="text-dark"> <?php echo @$application->ctc; ?> | <?php echo @$application->ectc; ?></span> </p>
                  </li>
                  <li class="list-group-item">
                    <p class="font-weight-400 text-black" style="margin:0!important;"> <i class="fa fa-briefcase text-dark"></i>&nbsp;Total Experience : <span class="text-dark"> <?php echo @$application->total_exp; ?> </span> </p>
                  </li>
                  <li class="list-group-item">
                    <p class="font-weight-400 text-black" style="margin:0!important;"> <i class="fa fa-briefcase text-dark"></i>&nbsp;Relevant Experience : <span class="text-dark"> <?php echo @$application->rel_exp; ?> </span> </p>
                  </li>
                 <!-- <li class="list-group-item d-none">
                    <p class="font-weight-400 text-black" style="margin:0!important;"> <i class="fa fa-credit-card text-dark"></i>&nbsp;Passpost : <span class="text-dark"> <?php echo @$application->passport; ?></span> </p>
                  </li>-->
                  <li class="list-group-item">
                    <p class="font-weight-400 text-black" style="margin:0!important;"> <i class="fa fa-cubes text-dark"></i>&nbsp;Skills : <span class="text-dark"> {{ @$application->skills ? @$application->skills : 'N/A' }} </span> </p>
                  </li>
				  
				  
                  <li class="list-group-item"> 
				  
				  	<span> 
				  		<i class="fa fa-file text-dark"></i> 
						<span class="font-weight-400 text-black">Resume :</span> 
					</span> 
					
					
					<?php 
					
                            if(!empty(@$resume))
							{
                                  $viewResume		=	'';
                                  $resume       	=	$resume;
                                  $temp         	=	explode(".", $resume); 
                                  $extension      	=	end($temp);
                                  
                                  $resumePath 		=	'';
                                  $resumePath 		=	asset('/').'public/user-uploads/resumes/'.$application->id.'/'.$resume;
                                
								 if(@$extension == 'pdf') { 
									$viewResume		=  'http://docs.google.com/gview?url='.@$resumePath.'&embedded=true';
								 }else if(@$extension == 'doc') {
									$viewResume		=  'https://docs.google.com/viewer?embedded=true&url='.@$resumePath.'&embedded=true';
								 }else if(@$extension == 'docx') { 
									$viewResume		=  'https://docs.google.com/viewer?embedded=true&url='.@$resumePath.'&embedded=true';
								 } 
                    
					?>
					<a type="button" class="btn btn-light-primary btn-sm p-0 px-0 font-weight-500" rel="" href="http://docs.google.com/gview?url=<?php echo @$resumePath;?>&embedded=true" target="_blank"> 
						&nbsp;View&nbsp;&nbsp; <i class="fa fa-arrows-alt" style="font-size: 9px;padding-right: 3px;"></i> 
					</a> 
					
					<?php 
							}
					?>
					<?php if(!empty(@$resume)){?>		
					<a type="button" class="btn btn-light-secondary btn-sm p-0 px-0 font-weight-500" href="{{ route('admin.job-applications.download', $application->id) }}"> 
						&nbsp;Download&nbsp;&nbsp; <i class="fa fa-download" style="font-size: 9px;padding-right: 3px;"></i> 
					</a>
					<?php } ?> 
					<?php if(!empty(@$resume)){?>	
					<a type="button" class="btn btn-light-danger btn-sm resume_delete p-0 px-0 font-weight-500" rel="{{$application->id}}" href="Javascript:void(0);" > 
						&nbsp;Delete&nbsp;&nbsp; <i class="fa fa-trash" style="font-size: 9px;padding-right: 3px;"></i> 
					</a> 
					<?php } ?> 
					<a type="button" class="btn btn-light-success btn-sm resume_change p-0 px-0 font-weight-500" rel="{{$application->id}}" href="Javascript:void(0);"> 
						&nbsp;Change&nbsp;&nbsp; <i class="fa fa-refresh" style="font-size: 9px;padding-right: 3px;"></i> 
					</a> 
				  
				  </li>
				  
				  
                </ul>
              </div>
              <div class="col-md-6" style="padding:0">
                <ul class="list-group list-group-flush ml-3">
                  <li class="list-group-item">
                    <p class="font-weight-400 text-black" id="" style="margin:0!important;"> <i class="fa fa-map-marker text-dark"></i>&nbsp;Current Location : <span class="text-dark"> <?php echo @$application->current_location; ?></span> </p>
                  </li>
                  <li class="list-group-item"> <span class="font-weight-400 text-black d-inline-block" style="margin:0!important;"> <i class="fa fa-random text-dark"></i>&nbsp;Current/Status :&nbsp; <span class="text-dark">
                    <!-- <?php echo @$application->
                    master_status_name; ?> | Interview Level-<?php echo @$child_status_name; ?> -->
                    <div class="tooltip font-weight-500"><?php echo @$application->master_status_name; ?> <span class="tooltiptext font-weight-500">Interview Level-<?php echo @$child_status_name; ?></span> </div>
                    </span> </span> </li>
                  <li class="list-group-item">
                    <p class="font-weight-400 text-black" id="phone-{{ $application->id }}" style="margin:0!important;"> <i class="fa fa-phone text-dark"></i>&nbsp;Secondary Contact Number : <span class="text-dark"> {{ (@$application->secondary_mobile) ? @$application->secondary_mobile : 'N/A' }} </span> </p>
                  </li>
                  <li class="list-group-item">
                    <p class="font-weight-400 text-black" id="email-{{ $application->id }}" style="margin:0!important;"> <i class="fa fa-envelope text-dark"></i>&nbsp;Secondary @lang('app.email') : <span class="text-dark">{{ (@$application->secondrary_email) ? @$application->secondrary_email : 'N/A' }} </span> </p>
                  </li>
                  <li class="list-group-item">
                    <p class="font-weight-400 text-black" style="margin:0!important;"> <i class="fa fa-hourglass-half text-dark"></i>&nbsp;Notice Period : <span class="text-dark">
                      <?php 
                                if($application->serving_notice_period_mode == 0){
                                    echo @$application->np_id.' Days'; 
                                }else{
                                    echo @$application->np_id;  
                                }
                                ?>
                      </span> </p>
                  </li>
                  <li class="list-group-item">
                    <p class="font-weight-400 text-black" style="margin:0!important;"> <i class="fa fa-th-large text-dark"></i>&nbsp;ID : <span class="text-dark"> <?php echo @$application->id; ?></span> </p>
                  </li>
                  <li class="list-group-item">
                    <p class="font-weight-400 text-black" style="margin:0!important;"> <i class="fa fa-cubes text-dark"></i>&nbsp;Reffered By : <span class="text-dark"> <span id="can_source"><?php echo @$application->posted_by; ?></span>&nbsp; <a href="Javascript:void(0);" rel="<?php echo @$application->id; ?>" class="updateSocurce">Change source&nbsp; <i class="fa fa-pencil fa-sm" style="color:#0000FF"></i> </a> </span> </p>
                  </li>
                  <li class="list-group-item">
                    <p class="font-weight-400 text-black" style="margin:0!important;"> <i class="fa fa-users text-dark"></i>&nbsp;Hiring HR : <span class="text-dark">
                      <?php 
                                $contactHtml  = '';
                                if(!empty($client_contuser_arr))
                                {
                                    foreach($client_contuser_arr as $cliArr)
                                    {
                                      if(!empty($contactHtml)){
                                        $contactHtml  .= '&nbsp;';  
                                      }
                                      $contactHtml    .= ucfirst($cliArr->contact_person_name);
                                                                          
                                     }
                                }
                                echo $contactHtml;
                                ?>
                      </span> </p>
                  </li>
                  <li class="list-group-item">
                    <p class="font-weight-400 text-black" style="margin:0!important;"> <i class="fa fa-calendar text-dark"></i>&nbsp;Applied Date : <span class="text-dark"> {{ $application->created_at->format('d M, Y H:i:s') }}</span> </p> 
                  </li>
                  <!-- remove from here -->
                </ul>
              </div>
            </div>
            <div class="row mt-0">
              <div class="col-12 d-none">
                <div class="col-sm-12">
                  <h5 class="text-black">Resume File</h5>
                </div>
                <div class="col-md-12"> 
				<?php    
						 $resumePath = '';
                         $resumePath = asset('/').'public/user-uploads/documents/'.$application->id.'/'.@$resume;
				?>
				<a type="button" class="btn btn-primary btn-sm" rel="" href="http://docs.google.com/gview?url=<?php echo @$resumePath;?>&embedded=true" target="_blank">&nbsp;View&nbsp;&nbsp; <i class="fa fa-arrows-alt"></i> </a> 
				<a type="button" class="btn btn-secondary btn-sm" href="{{ route('admin.job-applications.download', $application->id) }}">&nbsp;Download&nbsp;&nbsp; <i class="fa fa-upload"></i> </a> 
				<a type="button" class="btn btn-danger btn-sm resume_delete" rel="{{$application->id}}" href="Javascript:void(0);" >&nbsp;Delete&nbsp;&nbsp; <i class="fa fa-trash"></i> </a> 
				<a type="button" class="btn btn-success btn-sm resume_change" rel="{{$application->id}}" href="Javascript:void(0);">&nbsp;Change&nbsp;&nbsp; <i class="fa fa-refresh"></i> </a> 
				
				
				
				</div>
                <div class="col-sm-12">
                  <div class="col-md-6" style="padding:0">
                    <ul class="list-group list-group-flush ml-3">
                      <?php 
                                if(!empty(@$resume)){
                                
                                  $resume       	= $resume;
                                  $temp         	= explode(".", $resume); 
                                  $extension      	= end($temp);
                                  
                                  $resumePath = '';
                                  $resumePath = asset('/').'user-uploads/resumes/'.$application->id.'/'.$application->resume;
                                
                       ?>
                      <?php 
                                if(@$extension == 'pdf') { 
                                ?>
                      <li class="list-group-item">
                        <p class="font-weight-400 text-black" id="" style="margin:0!important;"> <i class="fa fa-user-circle text-dark"></i> <a class="" rel="" href="http://docs.google.com/gview?url=<?php echo @$resumePath;?>&embedded=true" target="_blank">&nbsp;View</a> </p>
                      </li>
                      <?php 
                                }else if(@$extension == 'doc') { 
                                ?>
                      <li class="list-group-item">
                        <p class="font-weight-400 text-black" id="" style="margin:0!important;"> <i class="fa fa-user-circle text-dark"></i> <a class="" rel="" href="https://docs.google.com/viewer?embedded=true&url=<?php echo @$resumePath;?>" target="_blank">&nbsp;View</a> </p>
                      </li>
                      <?php 
                                }else if(@$extension == 'docx') { 
                                ?>
                      <li class="list-group-item">
                        <p class="font-weight-400 text-black" id="" style="margin:0!important;"> <i class="fa fa-user-circle text-dark"></i> <a class="" rel="" href="https://docs.google.com/viewer?embedded=true&url=<?php echo @$resumePath;?>" target="_blank">&nbsp;View</a> </p>
                      </li>
                      <?php 
                                }
                                ?>
                      <li class="list-group-item">
                        <p class="font-weight-400 text-black" id="" style="margin:0!important;"> <i class="fa fa-user-circle text-dark"></i> <a class="" href="{{ route('admin.job-applications.download', $application->id) }}">&nbsp;Download</a> </p>
                      </li>
                      <li class="list-group-item">
                        <p class="font-weight-400 text-black" id="" style="margin:0!important;"> <i class="fa fa-user-circle text-dark"></i> <a class="resume_delete" rel="{{$application->id}}" href="Javascript:void(0);" >&nbsp;Delete</a> </p>
                      </li>
                      <?php 
                                }
                                ?>
                    </ul>
                  </div>
                </div>
              </div>
            </div>

<?php 
if($application_add_detail_cnt > 0)
{
?>
            <div class="row">
              <div class="col-12">
                <div class="col-sm-12 px-0">
                  <h4 class="text-dark border-bottom p-2 bg-light-secondary"> <i class="fa fa-info-circle"></i>&nbsp;Additional Details</h4>
                </div>
                <div class="row ml-1">
                  <div class="col-12">
                    <div class="row mx-0 d-flex justify-content-right">
                      <?php 
        							foreach($application_add_detail as $jval )
        							{
        					?>
                      <div class="boxs width-32 mr-2"> <i class="fa fa-adjust"></i>&nbsp;
                        <label class=""><?php echo ucwords(@$jval->job_detail_value); ?></label>
                        <input class="d-block border p-1 font-weight-400 w-100 text-dark" placeholder="No Record" type="text" value="<?php echo ucwords(@$jval->application_detail_value); ?>" disabled>
                      </div>
                      <?php 
        							}
        					?>
                    </div>
                  </div>
                </div>
              </div>
            </div>
<?php 
}
?>
            <div class="row">
              <div class="col-12">
                <!--<div class="col-sm-12 px-0">
              <h4 class="text-dark border-bottom p-2 bg-light-secondary"> <i class="fa fa-info-circle"></i>&nbsp;Additional Details </h4>
            </div> -->
                <div class="col-12">
                  <div class="row">
                    <?php 
					if(!empty($additional_skills_arr)){
						foreach($additional_skills_arr as $addSkillData){
			?>
                    <div class="text-dark font-weight-500 pl-2" style="width:auto; float:left"> <i class="fa fa-adjust"></i> <?php echo @$addSkillData->name; ?> :<?php echo @$addSkillData->skill_value; ?> </div>
                    <?php 
					}}							  	     
			?>
                  </div>
                </div>
              </div>
            </div>
            <div class="row mt-2">
              <div class="col-12">
                <div class="col-sm-12 px-0">
                  <h4 class="text-dark border-bottom p-2 bg-light-secondary"> <i class="fa fa-edit"></i>&nbsp;@lang('modules.jobApplication.applicantNotes') </h4>
                </div>
                <div id="applicant-notes" class="col-sm-12">
                  <ul class="list-unstyled">
                    @foreach($notes as $key => $notes )
                    <li class="media mb-3" id="note-{{ $notes->id }}">
                      <div class="media-body">
                        <p class="mt-0 mb-1 add-note-para"> {{ ucwords($notes->name) }} <span class="text-black font-weight-bold text-success" style="float:right;"> <span class="mb-0 pb-0 font-weight-bolder text-black">
                         <?php	
										$originalDate      = $notes->created_at;
										$create_at         = date("d-M-Y | H:i:s", strtotime($originalDate)); 
										echo $create_at;
						?>
                          </span> @if($user->can('edit_job_applications')) <a href="javascript:void(0);" class="edit-note" data-note-id="{{ $notes->id }}"> <i class="fa fa-edit text-primary ml-2 fa-sm"></i> </a> <a href="javascript:void(0);" class="delete-note" data-note-id="{{ $notes->id }}"> <i class="fa fa-trash ml-1 text-danger fa-sm"></i> </a> @endif </span> </p>
                        <span class="comment-para note-text">{{ ucfirst($notes->note_text) }}</span>
                        <div class="note-textarea"></div>
                      </div>
                    </li>
                    @endforeach
                  </ul>
                </div>
                @if($user->cans('edit_job_applications'))
                <div class="row">
                  <div class="col-sm-10">
                    <div class="form-group mb-2">
                      <textarea name="note" id="note_text" cols="30" rows="2" class="form-control ml-2" placeholder="Write a Note.."></textarea>
                      <!-- <a class="update-note btn btn-success btn-sm mt-1" data-note-id="30" href="javascript:void(0);:;"><i class="fa fa-save"></i> Save</a> -->
                    </div>
                  </div>
                  <div class="col-md-2"> <a href="javascript:void(0);" id="add-note" class="btn btn-sm btn-light-success"> <i class="fa fa-pencil fa-sm"></i>&nbsp;@lang('modules.jobApplication.addNote') </a> </div>
                </div>
                @endif </div>
            </div>
          </div>
          <div class="tab-pane pt-2" id="timeline">
            <div class="timeline timeline-5 ml-3">
              <?php 
                    //echo '<pre>'; print_r($timeline);  
                    if(!empty($timeline)){
                      foreach($timeline as $tldata){  
                      
                              $process_style    = 'bg-light-secondary';
                              $process_text     = 'text-black';
                              $process_stick_bg   = 'timeline-desc-light-secondary';
                              $process_title_bg   = '';
                              $process_title_text_color = 'text-black';
                              
                              if(!empty($tldata->process_id) && $tldata->process_id==1){                //Sourcing
                                $process_style        = 'bg-light-secondary'; 
                                $process_text         = 'text-black';
                                $process_stick_bg       = 'timeline-desc-light-secondary';
                                $process_title_bg       = '';
                                $process_title_text_color = 'text-black';
                              }else if(!empty($tldata->process_id) && $tldata->process_id==2){      //Screening
                                $process_style        = 'bg-light-warning';
                                $process_text         = 'text-yellow';
                                $process_stick_bg       = 'timeline-desc-light-warning';  
                                $process_title_bg       = 'bg-yellow';
                                $process_title_text_color = 'text-white';
                              }else if(!empty($tldata->process_id) && $tldata->process_id==3){     //Interview
                                $process_style        = 'bg-light-primary';
                                $process_text         = 'text-primary'; 
                                $process_stick_bg       = 'timeline-desc-light-primary';    
                                $process_title_bg       = 'bg-primary';
                                $process_title_text_color = 'text-white';
                              }else if(!empty($tldata->process_id) && $tldata->process_id==4){     //Negotiation 
                                $process_style        = 'bg-light-orange';
                                $process_text         = 'text-pink pt-2'; 
                                $process_stick_bg       = 'timeline-desc-light-orange';  
                                $process_title_bg       = 'bg-pink' ; 
                                $process_title_text_color = 'text-white'; 
                              }else if(!empty($tldata->process_id) && $tldata->process_id==5){     //Offered        
                                $process_style        = 'bg-light-success';
                                $process_text         = 'text-success'; 
                                $process_stick_bg       = 'timeline-desc-light-success'; 
                                $process_title_bg       = 'bg-success'  ;
                                $process_title_text_color = 'text-white'; 
                              }else if(!empty($tldata->process_id) && $tldata->process_id==6){     //Joined
                                $process_style        = 'bg-light-info';
                                $process_text         = 'text-purple';  
                                $process_stick_bg       = 'timeline-desc-light-info'; 
                                $process_title_bg       = 'bg-purple';   
                                $process_title_text_color = 'text-white';
                              }else if(!empty($tldata->process_id) && $tldata->process_id==7){     //Rejected
                                $process_style        = 'bg-light-danger';
                                $process_text         = 'text-danger';  
                                $process_stick_bg       = 'timeline-desc-light-danger';  
                                $process_title_bg       = 'bg-danger' ; 
                                $process_title_text_color = 'text-white';
                              }
                              
                              $status_name    = '';
                              if(!empty($tldata->master_status_name)){
                                $status_name  =  substr( strtoupper($tldata->master_status_name), 0, 3) ;
                              }
                              
                              $job_id = $jobtitle = $client_name  = $master_status_name = $child_process_name = $insert_type  ='';
                              $job_id         =   $tldata->job_id;
                              $jobtitle       =   $tldata->title;
                              $client_name      =   $tldata->client_name;
                              $master_status_name   =   $tldata->master_status_name;
                              $child_process_name   =   $tldata->process_child_name;
                              $insert_type      =   $tldata->insert_type;  
                              
                              $fstatus_name     =   $fprocess_child_name = $name = $create_at = $interview_message =  $interview_mode = '...'; 
                              $fstatus_name     =   $tldata->status_name; 
                              $fprocess_child_name  =   $tldata->process_child_name; 
                              $name         =   $tldata->name;
                              $create_at        =   $tldata->create_at; 
                              
                              $interview_mode     =   $tldata->interview_mode;
                              $interviewers     =   $tldata->interviewers;
                              $interview_message    =   $tldata->interview_message;
                              
                              $originalDate       = $tldata->create_at;
                              $create_at        = date("d-M-Y | H:i:s", strtotime($originalDate)); 
                    
                              
                    /*0 : update stage, sub stage/ process
                    1 : Mean interview schedule 
                    2 : Interview Reschedule 
                    3 : Interview status update 
                    4 : Feedback status update
                    5 : Feedback update */
                              
                    ?>
              <div class="timeline-items">
                <div class="timeline-item">
                  <div class="timeline-media <?php echo @$process_style; ?>"> <span class="svg-icon-secondary svg-icon-md">
                    <h6 class="<?php echo @$process_text; ?> pt-2 font-weight-bold"><?php echo @$status_name; ?></h6>
                    </span> </div>
                  <div class="timeline-desc <?php echo @$process_stick_bg; ?>"> <span class="font-weight-normal <?php echo @$process_title_bg ; ?> bg-light-secondary p-1 rounded "> <a href="javascript:void(0);" class="<?php echo @$process_title_text_color; ?>"><?php echo @$job_id; ?> |</a> <a href="javascript:void(0);" class="<?php echo @$process_title_text_color; ?>"><?php echo @$jobtitle; ?> |</a> <a href="javascript:void(0);" class="<?php echo @$process_title_text_color; ?>"><?php echo @$client_name; ?> | </a> <a href="javascript:void(0);" class="<?php echo @$process_title_text_color; ?>"> Greater Noida |</a> <a href="javascript:void(0);" class="<?php echo @$process_title_text_color; ?>"> <?php echo @$master_status_name; ?> |</a>
                    <?php 
                     if(!empty($child_process_name)){
                     ?>
                    <a href="javascript:void(0);" class="<?php echo @$process_title_text_color; ?>"> <?php echo @$child_process_name; ?> </a>
                    <?php 
                     }
                    ?>
                    </span>
                    <?php 
                if(($insert_type ==0) ){ ?>
                    <div class="row pt-2">
                      <div class="col-md-2"> <span class="font-weight-500 text-black">Type</span><br>
                        <span class="font-weight-normal text-black"><?php echo @$fstatus_name; ?></span> </div>
                      <div class="col-md-2"> <span class="font-weight-500 text-black">Sub Stage</span><br>
                        <span class="font-weight-normal text-black"><?php echo @$fprocess_child_name; ?></span> </div>
                      <div class="col-md-2"> <span class="font-weight-500 text-black">Update By</span><br>
                        <span class="font-weight-normal text-black"><?php echo @$name; ?></span> </div>
                      <!--<div class="col-md-2">
                                <span class="font-weight-500 text-black">Mode</span><br>
                                <span class="font-weight-normal text-black">F2F</span> 
                                 </div>-->
                      <div class="col-md-4"> <span class="font-weight-500 text-black">Date</span><br>
                        <span class="font-weight-normal text-black"><?php echo @$create_at; ?></span> </div>
                    </div>
                    <p class="font-weight-normal text-dark-50 mt-2 mb-0"> <span class="d-block"> <span class="font-weight-500 text-black">Message :&nbsp;</span> <span class="text-black-one">
                      <?php
                                if(!empty($interview_message)){
                                  echo $interview_message;
                                }else{
                                  echo @$fstatus_name;  
                                } 
                                
                                ?>
                      </span> </span> </p>
                    <?php 
                             }
                             else if(($insert_type ==1) || ($insert_type ==2))
                             {  
                              $type = $interviewers = $interview_schedule_date  = $interview_mode = $intModeval =$name = $interview_from_time = $interview_to_time =  '';
                              
                              if($insert_type == 1){
                                $type = 'Interview schedule';
                              }else if($insert_type == 2){
                                $type = 'Re-scheduled'; 
                              }
                              
                              $interviewers       =   $tldata->interviewers;
                              $interview_schedule_date  =   $tldata->interview_schedule_date;
                              $interview_mode       =   $tldata->interview_mode;
                              $name           =   $tldata->name;
                              if($interview_mode >= 0){
                                $intModeval         = $intModeArr[$interview_mode];
                              }
                              
                              $originalDate           = $tldata->interview_schedule_date;
                              $interview_schedule_date    = date("d-M-Y", strtotime($originalDate)); 
                              
                              $interview_from_time      =   $tldata->interview_from_time;
                              $interview_to_time        =   $tldata->interview_to_time;
                             
                             ?>
                    <div class="row pt-2">
                      <div class="col-md-2"> <span class="font-weight-500 text-black">Type</span><br>
                        <span class="font-weight-normal text-black"><?php echo @$type; ?></span> </div>
                      <div class="col-md-2"> <span class="font-weight-500 text-black">Marked By</span><br>
                        <span class="font-weight-normal text-black"><?php echo @$name; ?></span> </div>
                      <div class="col-md-2"> <span class="font-weight-500 text-black">Interviewer</span><br>
                        <span class="font-weight-normal text-black"><?php echo @$interviewers; ?></span> </div>
                      <div class="col-md-2"> <span class="font-weight-500 text-black">Mode</span><br>
                        <span class="font-weight-normal text-black"><?php echo $intModeval; ?></span> </div>
                      <div class="col-md-4"> <span class="font-weight-500 text-black">Scheduled Date</span><br>
                        <span class="font-weight-normal text-black"><?php echo @$interview_schedule_date; ?> &nbsp;| <?php echo @$interview_from_time; ?>-<?php echo @$interview_to_time; ?> </span> </div>
                    </div>
                    <p class="font-weight-normal text-dark-50 mt-2 mb-0"> <span class="d-block"> <span class="font-weight-500 text-black">Message :&nbsp;</span> <span class="text-black-one"><?php echo @$interview_message; ?></span> </span> </p>
                    <?php 
                             } 
                             else if($insert_type ==3)
                             {  
                              $type = $interviewers = $interview_schedule_date  = $interview_mode = $intModeval =$name = $note =  '';
                              
                              
                              $type           = 'Interview Status Update';
                              $interviewers       =   $tldata->interviewers;
                              $interview_schedule_date  =   $tldata->interview_schedule_date;
                              $interview_mode       =   $tldata->interview_mode;
                              $name           =   $tldata->name;                
                              $note           =   $tldata->note;
                              //$intModeval       = $intModeArr[$interview_mode];
                             ?>
                    <div class="row pt-2">
                      <div class="col-md-2"> <span class="font-weight-500 text-black">Type</span><br>
                        <span class="font-weight-normal text-black"><?php echo @$type; ?></span> </div>
                      <div class="col-md-2"> <span class="font-weight-500 text-black">Marked By</span><br>
                        <span class="font-weight-normal text-black"><?php echo @$name; ?></span> </div>
                      <!--<div class="col-md-2"> <span class="font-weight-500 text-black">Interviewer</span><br>
                                <span class="font-weight-normal text-black"><?php echo @$interviewers; ?></span> 
                                </div>-->
                      <!-- <div class="col-md-2"> <span class="font-weight-500 text-black">Mode</span><br>
                                <span class="font-weight-normal text-black"><?php echo $intModeval; ?></span> 
                                </div>-->
                      <div class="col-md-4"> <span class="font-weight-500 text-black">Date</span><br>
                        <span class="font-weight-normal text-black"><?php echo @$create_at; ?></span> </div>
                    </div>
                    <p class="font-weight-normal text-dark-50 mt-2 mb-0"> <span class="d-block"> <span class="font-weight-500 text-black">Message :&nbsp;</span> <span class="text-black-one"><?php echo @$note; ?></span> </span> </p>
                    <?php 
                             } 
                             else if($insert_type ==4)
                             {  
                              $type = $interviewers = $interview_schedule_date  = $interview_mode = $intModeval =$name = $note =  $interview_message =  '';
                                              
                              $type           = 'Feedback';
                              $interviewers       =   $tldata->interviewers;
                              $interview_schedule_date  =   $tldata->interview_schedule_date;
                              $interview_mode       =   $tldata->interview_mode;
                              $name           =   $tldata->name;                
                              $note           =   $tldata->note;
                              $interview_message      =   $tldata->interview_message;
                              //$intModeval       = $intModeArr[$interview_mode];
                             ?>
                    <div class="row pt-2">
                      <div class="col-md-2"> <span class="font-weight-500 text-black">Type</span><br>
                        <span class="font-weight-normal text-black"><?php echo @$type; ?></span> </div>
                      <div class="col-md-2"> <span class="font-weight-500 text-black">Marked By</span><br>
                        <span class="font-weight-normal text-black"><?php echo @$name; ?></span> </div>
                      <!--<div class="col-md-2"> <span class="font-weight-500 text-black">Interviewer</span><br>
                                <span class="font-weight-normal text-black"><?php echo @$interviewers; ?></span> 
                                </div>-->
                      <!-- <div class="col-md-2"> <span class="font-weight-500 text-black">Mode</span><br>
                                <span class="font-weight-normal text-black"><?php echo $intModeval; ?></span> 
                                </div>-->
                      <div class="col-md-4"> <span class="font-weight-500 text-black">Date</span><br>
                        <span class="font-weight-normal text-black"><?php echo @$create_at; ?></span> </div>
                    </div>
                    <p class="font-weight-normal text-dark-50 mt-2 mb-0"> <span class="d-block"> <span class="font-weight-500 text-black">Message :&nbsp;</span> <span class="text-black-one"><?php echo @$interview_message; ?></span> </span> </p>
                    <?php 
                             } 
                             else if($insert_type ==5)
                             {  
                              $type = $interviewers = $interview_schedule_date  = $interview_mode = $intModeval =$name = $note =  $interview_message =  '';
                                              
                              $type           = 'Feedback';
                              $interviewers       =   $tldata->interviewers;
                              $interview_schedule_date  =   $tldata->interview_schedule_date;
                              $interview_mode       =   $tldata->interview_mode;
                              $name           =   $tldata->name;                
                              $note           =   $tldata->note;
                              $interview_message      =   $tldata->interview_message;
                              //$intModeval       = $intModeArr[$interview_mode];
                             ?>
                    <div class="row pt-2">
                      <div class="col-md-2"> <span class="font-weight-500 text-black">Type</span><br>
                        <span class="font-weight-normal text-black"><?php echo @$type; ?></span> </div>
                      <div class="col-md-2"> <span class="font-weight-500 text-black">Marked By</span><br>
                        <span class="font-weight-normal text-black"><?php echo @$name; ?></span> </div>
                      <!--<div class="col-md-2"> <span class="font-weight-500 text-black">Interviewer</span><br>
                                <span class="font-weight-normal text-black"><?php echo @$interviewers; ?></span> 
                                </div>-->
                      <div class="col-md-2"> <span class="font-weight-500 text-black">Mode</span><br>
                        <span class="font-weight-normal text-black"><?php echo $interview_mode; ?></span> </div>
                      <div class="col-md-4"> <span class="font-weight-500 text-black">Date</span><br>
                        <span class="font-weight-normal text-black"><?php echo @$create_at; ?></span> </div>
                    </div>
                    <p class="font-weight-normal text-dark-50 mt-2 mb-0"> <span class="d-block"> <span class="font-weight-500 text-black">Message :&nbsp;</span> <span class="text-black-one"><?php echo @$interview_message; ?></span> </span> </p>
                    <?php 
                             } 
                             else if(($insert_type ==6))
                             {  
                             ?>
                    <div class="row pt-2">
                      <div class="col-md-2"> <span class="font-weight-500 text-black">Stage</span><br>
                        <span class="font-weight-normal text-black"><?php echo @$fstatus_name; ?></span> </div>
                      <div class="col-md-2"> <span class="font-weight-500 text-black">Sub Stage</span><br>
                        <span class="font-weight-normal text-black"><?php echo @$fprocess_child_name; ?></span> </div>
                      <div class="col-md-2"> <span class="font-weight-500 text-black">Update By</span><br>
                        <span class="font-weight-normal text-black"><?php echo @$name; ?></span> </div>
                      <div class="col-md-2"> <span class="font-weight-500 text-black">Mode</span><br>
                        <span class="font-weight-normal text-black"><?php echo @$interview_mode; ?></span> </div>
                      <div class="col-md-4"> <span class="font-weight-500 text-black">Date</span><br>
                        <span class="font-weight-normal text-black"><?php echo @$create_at; ?></span> </div>
                    </div>
                    <p class="font-weight-normal text-dark-50 mt-2 mb-0"> <span class="d-block"> <span class="font-weight-500 text-black">Message :&nbsp;</span> <span class="text-black-one"> <?php echo @$interview_message; ?></span> </span> </p>
                    <?php 
                             }
                             ?>
                    <!-- <div class="custm-bdr-btm"></div> -->
                  </div>
                </div>
              </div>
              <?php 
                    }
                    }
                    //echo '<pre>'; print_r($timeline);   
                    ?>
            </div>
            <hr class="mb-1 mt-1">
            <div class="row">
              <div class="col-md-12">
                <!-- <h5>@lang('modules.interviewSchedule.comments')</h5> -->
                <?php               
                       $dataArr   =  DB::table('interview_schedules')
                            ->join('interview_schedule_comments', 'interview_schedule_comments.interview_schedule_id', '=', 'interview_schedules.id')
                            ->join('users', 'users.id', '=', 'interview_schedule_comments.user_id')
                            ->select('interview_schedule_comments.comment', 'users.name')
                            ->where('interview_schedules.job_application_id', '=', $application->id)
                            //->orderBy('skills.name', 'ASC')
                            ->get();
                       if(!empty($dataArr)){
                        foreach($dataArr as $dArr){     
                       ?>
                <div class="col-sm-12">
                  <p class="text-muted"><b>{{$dArr->name }}:</b> {{ $dArr->comment }}</p>
                </div>
                <?php 
                       }}
                       ?>
                <div class="col-sm-12">
                  <p class="text-muted"> @if(!is_null($application->skype_id)) <span class="skype-button rounded" data-contact-id="live:{{$application->skype_id}}" data-text="Call"></span> @endif </p>
                </div>
                <div class="col-sm-6">
                  <p class="text-muted"> </p>
                </div>
                <div class="col-sm-12"> <span class="font-weight-500"><i class="fa fa-hourglass-half text-dark fa-sm"></i>&nbsp;@lang('modules.interviewSchedule.scheduleDate')</span><br>
                  @if(!empty($schedule))
                  @forelse($schedule as $sche)
                  <p class="custom-font"><i class="fa fa-clock fa-sm"></i>&nbsp;{{ $sche->schedule_date }}</p>
                  <?php                      
                          $dataArr  =  DB::table('interview_schedules')
                                ->join('interview_schedule_employees', 'interview_schedule_employees.interview_schedule_id', '=', 'interview_schedules.id')
                                ->join('users', 'users.id', '=', 'interview_schedule_employees.user_id')
                                ->select('interview_schedule_employees.user_accept_status', 'users.name')
                                ->where('interview_schedules.job_application_id', '=', $application->id)
                                //->orderBy('skills.name', 'ASC')
                                ->get();
                          if(!empty($dataArr)){
                            foreach($dataArr as $dArr){     
                                      
                          ?>
                  <div class="col-sm-6" style="padding:0"> <span class="font-weight-500"><i class="fa fa-briefcase fa-sm text-white"></i>&nbsp;@lang('modules.interviewSchedule.assignedEmployee'):</span>&nbsp;<?php echo ucwords($dArr->name); ?><br>
                  </div>
                  <div class="col-sm-6" style="padding:0">
                    <?php if($dArr->user_accept_status == 'accept'){?>
                    <label class="badge badge-success"><?php echo ucwords($dArr->user_accept_status); ?></label>
                    <?php } elseif($dArr->user_accept_status == 'refuse'){?>
                    <label class="badge badge-danger"><?php echo ucwords($dArr->user_accept_status); ?></label>
                    <?php }else {?>
                    <label class="badge badge-warning"><?php echo ucwords($dArr->user_accept_status); ?></label>
                    <?php } ?>
                  </div>
                  <?php }} ?>
                  @empty
                  @endforelse
                  @endif </div>
              </div>
            </div>
            <!-- <hr class="mb-1 mt-1">  -->
          </div>
          <div class="tab-pane" id="offertab">
            <div class="offertab offertab-5 ml-3">
              <div class="timeline-items">
                <div class="timeline-item">
<?php 
	//echo '<pre>'; print_r($application); 
	if( @$offer_job_applications_count > 0 )
	{
?>
			 
			  <div class="table-responsive">
				<!-- <caption class="visually-hidden">Offer to</caption> -->
			<?php 
					//echo '<pre>'; print_r($offer_job_applications); 
					
					if(!empty($offer_job_applications)){
						foreach($offer_job_applications as $offerval){
			?>	
				
				<table class="table table-bordered table-striped table-hover table-checkable">
				  <thead>
					<tr class="table line-height-normal">
					  <th scope="" class="table-bg-custom">Tag</th>
					  <th scope="" class="table-bg-custom">Description</th>
					</tr>
				  </thead>
				  <tbody>
					<tr class="table line-height-normal">
					  <th scope="row"><i class="fa fa-credit-card"></i>&nbsp;ID</th>
					  <td><?php echo @$offerval->id ?></td>
					</tr>
					<tr class="table line-height-normal">
					  <th scope="row"><i class="fa fa-user"></i>&nbsp;Applicant</th>
					  <td><?php echo @$offerval->full_name ?></td>
					</tr>
					<tr class="table line-height-normal">
					  <th scope="row"><i class="fa fa-briefcase"></i>&nbsp;Requisition</th>
					  <td><?php echo @$offerval->title ?></td>
					</tr>
					<tr class="table line-height-normal"> 
					  <th scope="row"><i class="fa fa-building"></i>&nbsp;Client</th>
					  <td><?php echo @$offerval->client_name ?></td>
					</tr>
					<tr class="table line-height-normal">
					  <th scope="row"><i class="fa fa-map-marker"></i>&nbsp;Location</th>
					  <td><?php echo @$offerval->city ?>, <?php echo @$offerval->state ?>, <?php echo @$offerval->country ?></td>  
					</tr>
					<tr class="table line-height-normal">
					  <th scope="row"><i class="fa fa-users"></i>&nbsp;Representative</th>
					  <td>
<?php 

				$contactHtml 			=	'';
				$job_id					=	$offerval->job_id;
				$client_contact_arr 	=	DB::table('client_contact_info')
				->join('client_contact_info_jobs', 'client_contact_info.id', '=', 'client_contact_info_jobs.client_contact_info_id')
				->select('client_contact_info.contact_person_name')
				->where('client_contact_info_jobs.job_id', $job_id)
				->get();
				$i						=	1;
				foreach($client_contact_arr as $contArr)
				{
					if($i==1)
					{
						$contactHtml 		.=  '
						<div class="tooltip">'.ucfirst(substr($contArr->contact_person_name, 0, 6)).'
							<span class="tooltiptext">';
					}
					$contactHtml 		.= '<span><span class="font-weight-600"><i class="fa fa-male"></i>&nbsp;Name&nbsp;:</span> '.$contArr->contact_person_name.'<br></span>';
					$i					= $i+1;								
			    }
				$contactHtml 		.=	'</span></div>';
				echo @$contactHtml;
				
				
?>
					  
					  </td>
					</tr>
					<tr class="table line-height-normal">
					  <th scope="row"><i class="fa fa-money"></i>&nbsp;Proposed CTC</th>
					  <td>
					  <?php 
								if((@$offerval->proposed_ctc != '') && (@$offerval->proposed_ctc > 0) ){
									echo "".@$offerval->proposed_ctc;
								}else{
									echo "NO";
								}
						?>
					  </td>
					</tr>
					<tr class="table line-height-normal">
					  <th scope="row"><i class="fa fa-file"></i>&nbsp;Any Offer</th>
					  <td><?php 
									if(@$offerval->any_offer == 1){
										echo "YES";
									}else{
										echo "NO";
									}
									?>
					  </td>
					</tr>
					<tr class="table line-height-normal">
					  <th scope="row"><i class="fa fa-files-o"></i>&nbsp;Documents</th>
					  <td><?php 
									if(@$offerval->document == 1){
										echo "YES";
									}else{
										echo "NO";
									}
									?>
					  </td>
					</tr>
					<tr class="table line-height-normal">
					  <th scope="row"><i class="fa fa-hand-pointer-o"></i>&nbsp;Submission for Approval</th>
					  <td><?php 
									if(@$offerval->sub_for_approval == 1){
										echo "YES";
									}else{
										echo "NO";
									}
									?>
					  </td>
					</tr>
					<tr class="table line-height-normal">
					  <th scope="row"><i class="fa fa-hand-pointer-o"></i>&nbsp;Offer Approved</th>
					  <td><?php 
									if(@$offerval->offer_approved == 1){
										echo "YES";
									}else{
										echo "NO";
									}
									?>
					  </td>
					</tr>
					<tr class="table line-height-normal">
					  <th scope="row"><i class="fa fa-file"></i>&nbsp;Offer Released</th>
					  <td><?php 
									if(@$offerval->offer_release == 1){
										echo "YES";
									}else{
										echo "NO";
									}
									?>
					  </td>
					</tr>
					<tr class="table line-height-normal">
					  <th scope="row"><i class="fa fa-file"></i>&nbsp;Offer Acceptance</th>
					  <td><?php 
									if(@$offerval->offer_acceptance == 1){
										echo "YES";
									}else{
										echo "NO";
									}
									?>
					  </td>
					</tr>
					<tr class="table line-height-normal">
					  <th scope="row"><i class="nav-icon icon-people"></i>&nbsp;Joined</th>
					  <td><?php 
									if(@$offerval->joined == 1){
										echo "YES";
									}else{
										echo "NO";
									}
									?>
					  </td>
					</tr>
					<tr class="table line-height-normal">
					  <th scope="row"><i class="fa fa-calendar"></i>&nbsp;Joining Date</th>
					  <td><?php 
									if(@$offerval->joined_date != ''){
										echo "".@$offerval->joined_date;
									}else{
										echo "NO";
									}
									?>
					  </td>
					</tr>
				</tbody>                      
             </table>
			<?php 
						}
					}
			?> 
			
	

<?php }else{ ?>
			<table class="table table-bordered table-striped table-hover table-checkable">				  
				<tbody>
			<tr class="table line-height-normal">
					  <th scope="row"><i class="fa fa-calendar"></i>&nbsp;Joining Date</th>
					  <td>	
           <img src="<?php echo asset('img/No-data-available.png'); ?>" height="250px" width="95%" />
                      </td>
					</tr>
				</tbody>                      
             </table>

<?php } ?>
                  </div>
                
                </div>
              </div>
            </div>
          </div>
        </div>
        <!-- Tab panes end -->
        <div class="clearfix"></div>
      </div>
    </div>
  </div>
</div>
<!-- Tabs Section End-->
<!-- End Section -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.js"></script>
<script src="{{ asset('assets/plugins/jquery-bar-rating-master/dist/jquery.barrating.min.js') }}<?php echo TM_VERSION; ?>" type="text/javascript:void(0);"></script>
<script>
function createSchedule (id) 
{      
          var url = "{{ route('admin.job-applications.create-schedule',':id') }}";
          url = url.replace(':id', id);
          $('#modelHeading').html('Schedule');
          $.ajaxModal('#scheduleDetailModal', url);
}

function moveApplication(id) 
{
          var url = "{{ route('admin.job-applications.create-move',':id') }}";
          url = url.replace(':id', id);
          $('#moveApplicationmodelHeading').html('Schedule');
          $.ajaxModal('#moveApplicationModal', url);
}

function copyApplication(id) 
{
          var url = "{{ route('admin.job-applications.create-copy',':id') }}";
          url = url.replace(':id', id);
          $('#copyApplicationmodelHeading').html('Schedule');
          $.ajaxModal('#copyApplicationModal', url);
}   

$('#example-fontawesome').barrating({
      theme: 'fontawesome-stars',
      showSelectedRating: false,
      onSelect:function(value, text, event){
          if(event !== undefined && value !== ''){
              var url = "{{ route('admin.job-applications.rating-save',':id') }}";
              url = url.replace(':id', {{$application->id}});
              var token = '{{ csrf_token() }}';
              var id = {{$application->id}};
              $.easyAjax({
                  type: 'Post',
                  url: url,
                  container: '#example-fontawesome',
                  data: {'rating':value, '_token':token},
                  success: function (response) {
                      $('#example-fontawesome_'+id).barrating('set', value);
                  }
              });
          }

      }
  });
  
  @if($application->rating !== null)
      $('#example-fontawesome').barrating('set', {{$application->rating}});
  @endif

$('#view-application-details').click(function () {
      var url = "{{ route('admin.job-applications.viewDetails') }}";
      var id = {{$application->id}};
      var token = '{{ csrf_token() }}';

      $.easyAjax({
          type: 'POST',
          url: url,
          data: {'_token':token, 'id':id},
          success: function (response) {
              if(response.status == 'success') {
                  $('#email-'+id).html(response.email);
                  $('#phone-'+id).html(response.phone);
                  $('#resume-'+id).html('<a target="_blank" href="'+response.resume+'" class="btn btn-sm btn-primary">@lang('app.view') @lang('modules.jobApplication.resume')</a>');
              }
          }
      });
  })

$('#add-note').click(function () {
      var url = "{{ route('admin.applicant-note.store') }}";
      var id = {{$application->id}};
      var note = $('#note_text').val();
      var token = '{{ csrf_token() }}';

      $.easyAjax({
          type: 'POST',
          url: url,
          data: {'_token':token, 'id':id, 'note': note},
          success: function (response) {
              if(response.status == 'success') {
                  $('#applicant-notes').html(response.view);
                  $('#note_text').val('');
              }
          }
      });
  });

$('.resume_delete').click(function(){

  		var application_id  	= $(this).attr('rel');
  		var token       		= '{{ csrf_token() }}';
  
		   swal({
					  title       			: "@lang('errors.areYouSure')",
					  text        			: "@lang('errors.deleteWarning')",
					  type        			: "warning",
					  showCancelButton  	: true,
					  confirmButtonColor  	: "#DD6B55",
					  confirmButtonText 	: "@lang('app.delete')",
					  cancelButtonText  	: "@lang('app.cancel')",
					  closeOnConfirm    	: true,
					  closeOnCancel   		: true
						  }, function(isConfirm){
							  if (isConfirm) {
      
      				var url     			=	"{{ route('admin.job-applications.resume_delete') }}";
                  	var token     			=	"{{ csrf_token() }}";
                  	$.easyAjax({
									  type  		: 'POST',
									  url   		: url,
									  data  		: {'_token': token, '_method': 'POST', 'application_id':application_id },
									  success 		: function (response) {
										  if (response.status == "success") {
											$.unblockUI();
											table._fnDraw();
										  }
            						 }
          					});
              
    				}
          	});
  
  
});

$('.resume_change').click(function(){

	//alert("call");
	
  var application_id  = $(this).attr('rel');
        
  var url       = "{{ route('admin.job-applications.resume_change',':id') }}";
  url         	=  url.replace(':id', application_id);
  
           $('#modelHeadingCS').html('Schedule');
           $.ajaxModal('#scheduleDetailModalSC', url);            
          
});


$('.resume_view').click(function(){
	 

  var application_id  = $(this).attr('rel');
        
  var url       = "{{ route('admin.job-applications.resume_view',':id') }}";
  url         =  url.replace(':id', application_id);
  
           $('#modelHeading_rv').html('Schedule');
           $.ajaxModal('#scheduleDetailModal_rv', url);           
          
});

$('.updateSocurce').click(function(){
	

  var application_id  = $(this).attr('rel');
        
  var url       = "{{ route('admin.job-applications.updateSocurce',':id') }}";
  url         =  url.replace(':id', application_id);
  
           $('#modelHeadingCS').html('Schedule');
           $.ajaxModal('#scheduleDetailModalSC', url);            
          
});

$('body').on('click', '.edit-note', function() {
	
      $(this).hide();
      let noteId = $(this).data('note-id');
      $('body').find('#note-'+noteId+' .note-text').hide();

      let noteText = $('body').find('#note-'+noteId+' .note-text').html();
      let textArea = '<textarea id="edit-note-text-'+noteId+'" class="form-control" row="4">'+noteText+'</textarea><a class="update-note" data-note-id="'+noteId+'" href="javascript:void(0);"><i class="fa fa-save text-white"></i> @lang("app.save")</a>';
      $('body').find('#note-'+noteId+' .note-textarea').html(textArea);
  
});

$('body').on('click', '.update-note', function () {
	
      let noteId = $(this).data('note-id');

      var url = "{{ route('admin.applicant-note.update', ':id') }}";
      url = url.replace(':id', noteId);

      var note = $('#edit-note-text-'+noteId).val();
      var token = '{{ csrf_token() }}';

      $.easyAjax({
          type: 'POST',
          url: url,
          data: {'_token':token, 'noteId':noteId, 'note': note, '_method': 'PUT'},
          success: function (response) {
              if(response.status == 'success') {
                  $('#applicant-notes').html(response.view);
              }
          }
      });
  
});

$('body').on('click', '.delete-note', function(){
	


      let noteId = $(this).data('note-id');



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

              var url = "{{ route('admin.applicant-note.destroy', ':id') }}";
              url = url.replace(':id', noteId);

              var token = '{{ csrf_token() }}';

              $.easyAjax({
                  type: 'POST',
                  url: url,
                  data: {'_token':token, '_method': 'DELETE'},
                  success: function (response) {
                      if(response.status == 'success') {
                          $('#applicant-notes').html(response.view);
                      }
                  }
              });
          }
      });
  
});

</script>
@if(!is_null($application->skype_id))
<script src="https://swc.cdn.skype.com/sdk/v1/sdk.min.js"></script>
@endif