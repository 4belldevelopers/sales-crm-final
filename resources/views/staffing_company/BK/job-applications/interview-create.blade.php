<!--begin::Form-->
<script src="https://code.jquery.com/jquery-3.6.0.js" integrity="sha256-H+K7U5CnXl1h5ywQfKtSj8PCmoN9aaq30gDh27Xc0jk=" crossorigin="anonymous"></script>
<script>tinymce.init({ selector:'#editor' });</script>
<script>
   $.noConflict();
   
   	function accessEmailTemplate(template_id, application_id)
   		{
   			
   			var interviewers				=	$('#panellist').val();
   			var videoUrl					=	$('#url').val();
   			var interview_mode				=	$('#interview_mode').val();
   								
   			if(template_id > 0)
   			{
   						var id        		=	template_id;
   						var url       		=	"{{ route('admin.job-applications.accessEmailTemplate',':id') }}";
   						url         		=	url.replace(':id', id);
   						var baseurl     	=	"{{ asset('assets/images/Loading2.gif') }}"; 
   						var token       	=	"{{ csrf_token() }}";
   						$.ajax({
   												type		:	'POST',
   												url			:	url,
   												data		:	{'_token': token, '_method': 'POST', 'template_id' : template_id, 'application_id' : application_id, 'interviewers': interviewers, 'videoUrl': videoUrl, 'interview_mode': interview_mode},
   												beforeSend	:	function() {
   																				$('#childInterview').html('<img src="'+baseurl+'" height="35px" width="100%">');
   																			},
   												success: function (response) 
   												{		
   												
   													$('#subject').val(response.template_subject);
   													$('#editor').html(response.template_description);													
   													$(tinymce.get('editor').getBody()).html(response.template_description);	
   
   													     
   												},
   												complete: function (data)  
   												{
   														//$('#childStatus_'+current_application_id).html('');
   												}
   									});
   		  
   		  
   			}
   			else
   			{
   					$('#subject').val('');
   					$('#editor').html('');	
   			}
   			
   		  
   		  
   		 
   									
   		  	
   	
   		}
   		
</script>
<style>
   .tox-statusbar__branding{ display:none}
</style>
<form class="form fv-plugins-bootstrap fv-plugins-framework" id="app_interview_create_frm" name="app_interview_create_frm" method="post" enctype="multipart/form-data">
   <meta name="csrf-token" content="{{ csrf_token() }}" />
   <input type="hidden" name="_token" value="{{ csrf_token() }}">
   <input type="hidden" name="application_id" id="application_id" value="<?php echo @$application->id; ?>" />
   <input type="hidden" name="master_status_id" id="master_status_id" value="<?php echo @$application->master_status_id; ?>" />
   <input type="hidden" name="child_status_id" id="child_status_id" value="<?php echo @$application->child_status_id; ?>" />
   <div id="div_interview_schedule_first">
      <div class="modal-header flex-stack">
         <h2>Interview Schedule</h2>
         <div class="btn btn-sm btn-icon btn-active-color-primary" data-dismiss="modal">
            <span class="svg-icon svg-icon-1">
               <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                  <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1" transform="rotate(-45 6 17.3137)" fill="black" />
                  <rect x="7.41422" y="6" width="16" height="2" rx="1" transform="rotate(45 7.41422 6)" fill="black" />
               </svg>
            </span>
         </div>
      </div>
      <div class="modal-body scroll-y py-0 px-lg-7">
         <div data-kt-element="options">
            <div class="py-5">
               <table class="table align-middle table-row-dashed fs-6" id="">
				<!--begin::Table Head-->
				<thead>
		   		<!--begin::Table row-->
				   <tr class="fw-bolder text-muted" role="row">
		   				<th class="">Name</th>
						<th class="">Contacts</th>
						<th class="">Exp.</th>
						<th class="">Salary</th>
						<th class="">Notice Period</th>
						<th class="">Qualifaction</th>
						<th class="">Location</th>
						<th class="">Source</th>
						<th class="">Requisition</th>
						<th class="">Stage</th>
				   </tr>
				   <!--end::Table row-->
				</thead>
				<!--end::Table Head-->
                  <tbody class="text-gray-600 fw-bold">
                     <tr>
                        <?php 
                           $bgTextDecore = $strVal = '';
                                    $str = trim(@$application->full_name);
                                    $str = $strVal = strtolower($str[0]);
                                    $str = ord($str);
                                    if ($str >= 97 && $str <= 101)
									{
                                        $bgTextDecore = 'fs-3 bg-light-danger text-danger';
                                    }
									else if ($str >= 102 && $str <= 106)
									{
                                        $bgTextDecore = 'fs-3 bg-light-warning text-warning';
                                    }
									else if ($str >= 107 && $str <= 111)
									{
                                        $bgTextDecore = 'fs-3 bg-light-primary text-primary';
                                    }
									else if ($str >= 112 && $str <= 116)
									{
                                        $bgTextDecore = 'fs-3 bg-light-success text-success';
                                    }
									else if ($str >= 117 && $str <= 121)
									{
                                        $bgTextDecore = 'fs-3 bg-dark text-secondary';
                                    }
									else
									{
                                        $bgTextDecore = 'fs-3 bg-light-secondary text-dark';
                                    }
                           ?>
                        <td class="d-flex align-items-center border-0">
                           <div class="symbol symbol-circle symbol-40px overflow-hidden me-2 border-radius-5">
                              <a href="javascript:void(0);">
                                 <div class="symbol-label fs-3 <?php echo @$bgTextDecore; ?> border-radius-5"><?php echo strtoupper(@$strVal); ?></div>
                              </a>
                           </div>
                           <div class="d-flex flex-column"> 
                              <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">
                              <?php echo ucwords(@$application->full_name); ?>
                              </a> 
                              <a href="javascript:void(0);" class="text-muted fw-bold d-block fs-7 text-hover-primary">
                              <?php echo ucwords(@$application->current_designation); ?></a> 
                           </div>
                        </td>
                        <td>
                           <div class="d-flex flex-column"> 
                              <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">
                              <?php echo @$application->phone; ?>
                              </a> 
                              <a href="javascript:void(0);" class="text-muted fw-bold d-block fs-7 text-hover-primary" data-toggle="tooltip" title="<?php echo @$application->email; ?>">
                                 <?php echo substr(ucwords(@$application->email), 0, 10); ?>
                              </a> 
                           </div>
                        </td>
                        <td>
                           <div class="d-flex flex-column"> 
                              <span class="text-dark fw-bolder fs-6"><?php echo @$application->total_exp; ?>+</span> 
                              <span class="text-muted fw-bold d-block fs-7"><?php echo @$application->rel_exp; ?>+</span> 
                           </div>
                        </td>
                        <td>
                           <div class="d-flex flex-column"> 
                              <span class="text-dark fw-bolder fs-6"><?php echo @$current_ctc; ?></span> 
                              <span class="text-muted fw-bold d-block fs-7"><?php echo @$expected_ctc; ?></span> 
                           </div>
                        </td>
                        <td>
                           <div class="d-flex flex-column"> 
                              <span class="text-dark fw-bolder fs-6"><?php echo @$application->np_id; ?></span> 
                              <span class="text-muted fw-bold d-block fs-7"><?php echo @$application->current_company; ?></span> 
                           </div>
                        </td>
                        <td>
                           <div class="d-flex flex-column"> 
                              <span class="text-dark fw-bolder fs-6"><?php echo @$application->qualification_name; ?></span> 
                              <span class="text-muted fw-bold d-block fs-7"><?php echo  substr(ucwords(@$application->skills), 0, 15); ?></span> 
                           </div>
                        </td>
                        <td>
                           <div class="d-flex flex-column"> 
                              <span class="text-dark fw-bolder fs-6"><?php echo @$row_city_name; ?></span> 
                              <span class="text-muted fw-bold d-block fs-7"><?php echo substr(@$row_state_name, 0, 6) ?> | <?php echo @$row_country_name; ?></span> 
                           </div>
                        </td>
                        <td>
                           <div class="d-flex flex-column">
                              <div class="symbol-group symbol-hover">
                                 <div class="symbol symbol-30 symbol-circle" data-toggle="tooltip" data-placement="top" title="<?php echo substr(@$application->submitted_by_name, 0, 10) ?>"> 
                                    <img alt="Pic" src="https://preview.keenthemes.com//metronic/theme/html/demo1/dist/assets/media/users/300_25.jpg"> 
                                 </div>
                              </div>
                           </div>
                           <span class="text-muted fw-bold d-block fs-7"><?php echo substr(@$submitted_by_company, 0, 10) ?></span>
                        </td>
                        <td>
                           <div class="d-flex flex-column">
							   <span class="text-dark fw-bolder fs-6"><?php echo substr(@$application->job_title, 0, 10) ?></span>
							   <span class="text-muted fw-bold d-block fs-7"><?php echo substr(@$application->client_name, 0, 10) ?></span>
							</div>
                        </td>
                        <td>
							<a href="javascript:void(0);" class="d-flex flex-column">
								<span class="badge fw-bolder fs-6 <?php echo @$application->master_status_style; ?> px-2 py-1"><?php echo substr(trim(@$application->master_status_name), 0, 10) ?></span>
								<span class="text-muted fw-bold text-muted d-block fs-7"><?php echo substr(@$child_status_name, 0, 10) ?></span>
							</a>
						</td>
                     </tr>
                  </tbody>
               </table>
               <?php 
                  $fresh_check		=	'';	
                  $reschedule_check	=	'';	
                  if($reschedule == 1){
                  	$reschedule_check	=	'checked="checked"';	
                  }else{
                  	$fresh_check		=	'checked="checked"';	
                  }		
                  ?>	
               <div class="row">
                  <div class="col-lg-3">
                     <label class="text-dark mb-0 mt-3 fw-bolder fs-5">Interview Type</label>
                     <div class="row py-3 mb-3">
                        <div class="col-lg-6 px-2">
                           <input type="radio" class="btn-check" name="interview_type" value="1"  id="fresh" <?php echo @$fresh_check; ?> >
                           <label class="btn btn-outline btn-outline-dashed btn-outline-default p-3 d-flex align-items-center justify-content-center" for="fresh">
                              <span class="d-block fw-bold text-start">
                                 <span class="text-dark-800 fw-bolder d-block fs-5">Fresh</span>
                              </span>
                           </label>
                        </div>
                        <div class="col-lg-6 px-2">
                           <input type="radio" class="btn-check" name="interview_type" value="2" id="reschedule" <?php echo @$reschedule_check; ?>>
                           <label class="btn btn-outline btn-outline-dashed btn-outline-default p-3 d-flex align-items-center justify-content-center" for="reschedule">
                              <span class="d-block fw-bold text-start">
                                 <span class="text-dark-800 fw-bolder d-block fs-5">Reschedule</span>
                              </span>
                           </label>
                        </div>
                        <span class="form-text text-muted">Please select type</span> 
                     </div>
                  </div>
                  <div class="col-lg-3">
                     <label class="text-dark mb-0 mt-3 fw-bolder fs-5 px-2">Date and Time</label>
                     <div class="row my-3 px-3">
                        <input class="form-control form-control-solid" placeholder="Select a date & time" name="interviewDate" id="interviewDate" type="datetime-local">
                     </div>
                     <span class="form-text text-muted">Please select date & time</span> 
                  </div>
                  <div class="col-lg-3">
                     <label class="text-dark mb-0 mt-3 fw-bolder fs-5 px-2">Interview Mode</label>
                     <div class="row py-3 mb-3 px-3">
                        <select name="interview_mode" id="interview_mode" onchange="getInterviewModeChild(this.value, <?php echo $job_id; ?>);" class="form-select form-select-solid">
                           <option value="">Select Interview Mode</option>
                           <?php 
                              if(!empty($interview_mode)){
                              foreach($interview_mode as $int_mode_val){
                              	echo	'<option value="'.@$int_mode_val->id.'">'.@$int_mode_val->name.'</option>';
                              }
                              }		 
                              ?>
                        </select>
                        <span class="form-text text-muted">Please select mode</span> 
                     </div>
                  </div>
                  <div class="col-lg-3">
                     <label class="text-dark mb-0 mt-3 fw-bolder fs-5 px-2">Interview Sub Mode</label>
                     <div class="row py-3 mb-3 px-3">
                        <span id="childInterview">
                           <select class="form-select form-select-solid" name="interview_mode_lavel" id="interview_mode_lavels" data-style="">
                              <option value="">Select mode</option>
                           </select>
                        </span>
                        <span class="form-text text-muted">Please select mode</span> 
                     </div>
                  </div>
               </div>
               <div class="row" style="display:none" id="url_div">
                  <div class="col-lg-12">
                     <label class="text-dark my-3 fw-bolder fs-5 d-flex justify-content-between align-items-center"><span id="lbl_call_mode">&nbsp;</span></label>
                     <input type="text" name="url" id="url" class="form-control form-control-solid">					
                  </div>
               </div>
               <div class="row">
                  <div class="col-lg-12">
                     <label class="text-dark my-3 fw-bolder fs-5 d-flex justify-content-between align-items-center"> Panel List </label>
                     <input class="form-control form-control-solid add_panel" name="panellist" id="panellist" placeholder="Enter panellist" value="">
                  </div>
                  <span class="form-text text-muted">Please select panel list</span> 
               </div>
            </div>
         </div>
      </div>
      <div class="modal-footer flex-center">
         <div class="div1">
            <button type="close" id="" class="btn btn-secondary me-3" data-dismiss="modal">Cancel</button>
            <button type="button" id="" class="btn btn-light-primary me-3" onclick="submitForm();">
				<span class="indicator-label">Save</span>
				<span class="indicator-progress">Please wait...<span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
			</button>
         </div>
         <div class="div1 position-absolute end-0 me-9">
            <!-- <button type="button" id="" class="btn btn-secondary">Rescheduled</button> -->
            <button type="button" class="btn btn-light-dark btn-hover-dark" id="send_email_invies" >Send Email Invites</button>
         </div>
      </div>
   </div>
   <div id="div_interview_schedule_second" style="display:none">
      <!--begin::Modal header-->
      <div class="modal-header flex-stack">
         <!--begin::Title-->
         <h2>Availability</h2>
         <!--end::Title-->
         <!--begin::Close-->
         <div class="btn btn-sm btn-icon btn-active-color-primary" data-dismiss="modal">
            <!--begin::Svg Icon | path: icons/duotune/arrows/arr061.svg-->
            <span class="svg-icon svg-icon-1">
               <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                  <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1" transform="rotate(-45 6 17.3137)" fill="black" />
                  <rect x="7.41422" y="6" width="16" height="2" rx="1" transform="rotate(45 7.41422 6)" fill="black" />
               </svg>
            </span>
            <!--end::Svg Icon-->
         </div>
         <!--end::Close-->
      </div>
      <!--end::Modal header-->
      <!--begin::Modal body-->
      <div class="modal-body scroll-y py-0 px-lg-7">
         <div data-kt-element="options">
            <div class="py-5">
               <!--begin::Template-->       
               <div class="form-group row mb-4">
                  <label class="col-xl-2 col-lg-2 col-form-label text-dark fw-bolder fs-5">Template</label>
                  <div class="col-lg-10 col-xl-10">
                     <select name="email_template" id="email_template" class="form-control form-control-solid" onchange="accessEmailTemplate(this.value, <?php echo @$application->id ?> );">
                        <option value="">Select Pre Define Email Template </option>
                        <?php 
                           if(!empty($email_template))
                           {
                           	foreach($email_template as $email_temp)
                           	{
                           		echo '<option value="'.$email_temp->e_temp_id.'" >'.$email_temp->display_name.'</option>';	
                           	}
                           }
                           ?>
                     </select>
                     <span class="form-text text-muted">Please select email template</span>
                  </div>
               </div>
               <!--end::Template-->
               <!--begin::From email-->       
               <div class="form-group row mb-4">
                  <label class="col-xl-2 col-lg-2 col-form-label text-dark fw-bolder fs-5">From</label>
                  <div class="col-lg-10 col-xl-10">
                     <input type="text" name="from_email" id="from_email" placeholder="Ex: example@test.com" class="form-control form-control-solid" value="<?php echo @$from_email; ?>">
                     <span class="form-text text-muted">Please enter email</span>
                  </div>
               </div>
               <!--end::From email-->
               <!--begin::To email-->       
               <div class="form-group row mb-4">
                  <label class="col-xl-2 col-lg-2 col-form-label text-dark fw-bolder fs-5">To</label>
                  <div class="col-lg-10 col-xl-10">
                     <input type="text" name="to_email" id="to_email" placeholder="Ex: demo@test.com" class="form-control form-control-solid" value="<?php echo @$application->email ?>">				
                     <span class="form-text text-muted">Please enter email</span>
                  </div>
               </div>
               <!--end::To email-->
               <!--begin::CC email-->       
               <div class="form-group row mb-4">
                  <label class="col-xl-2 col-lg-2 col-form-label text-dark fw-bolder fs-5">CC</label>
                  <div class="col-lg-10 col-xl-10">
                     <input type="text" name="cc_email" id="cc_email" placeholder="Ex: example2@test.com" class="form-control form-control-solid" value="<?php //echo @$cc_email; ?>"> 				
                     <span class="form-text text-muted">Please enter email</span>
                  </div>
               </div>
               <!--end::CC email-->
               <!--begin::Subject-->
               <div class="form-group row mb-4">
                  <label class="col-xl-2 col-lg-2 col-form-label text-dark fw-bolder fs-5">Subject</label>
                  <div class="col-lg-10 col-xl-10">
                     <input type="text" name="subject" id="subject" placeholder="Ex: Re-Schedule Interview" class="form-control form-control-solid">				
                     <span class="form-text text-muted">Please enter subject</span>
                  </div>
               </div>
               <!--end::Subject-->
               <!--begin::Body-->       
               <div class="form-group row mb-4">
                  <label class="col-xl-2 col-lg-2 col-form-label text-dark fw-bolder fs-5">Body</label>
                  <div class="col-lg-10 col-xl-10">            
                     <textarea id="editor" name="editor"  class="form-control form-control-solid" row="10" placeholder="">&nbsp;</textarea>				 
                     <span class="form-text text-muted">Please enter email content</span> 
                  </div>
               </div>
               <!--end::Body-->
               <!--begin::Upload Files-->
               <div class="form-group row mb-4">
                  <label class="col-xl-2 col-lg-2 col-form-label text-dark fw-bolder fs-5">Upload Files</label>
                  <div class="col-xl-10 col-lg-10">
                     <input type="file" name="attachment" id="attachment" class="dropzone-select btn btn-sm btn-primary me-2" />                 
                  </div>
               </div>
               <!--end::Upload Files-->
            </div>
         </div>
      </div>
      <!--begin::Modal body-->
      <!--begin::Modal footer-->
      <div class="modal-footer flex-center">
         <div class="div1">
            <!--begin::Cancel-->
            <button type="close" id="" class="btn btn-light-dark me-3" data-dismiss="modal">Cancel</button>
            <!--end::Cancel-->
            <!--begin::Back-->
            <button type="button" id="" class="btn btn-light-info me-3 divback">Back</button>
            <!--end::Back-->
            <!--begin::Send Email-->
            <button type="button" id="sendEmailBtn" class="btn btn-light-primary sendEmailBtn">
            <span class="indicator-label">Send Email</span>
            <span class="indicator-progress">Please wait... 
            <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
            </span>
            </button>
            <!--end::Send Email-->
         </div>
         <!--<span style="position: absolute;right: 2%;"></span>-->
         <div class="div2 position-absolute end-0 me-5 d-none">
         </div>
      </div>
      <!--end::Modal footer-->
   </div>
</form>
<!--begin::Form-->
<script>
   $( document ).ready(function() {
       	
   	$('body').on('click', '#send_email_invies', function(){		
   				$('#div_interview_schedule_first').hide();
   				$('#div_interview_schedule_second').show();	
   		});	
   	$('body').on('click', '.divback', function(){		
   				$('#div_interview_schedule_first').show();
   				$('#div_interview_schedule_second').hide();
   		});	
   });
   	function submitForm()
   		{
   			
   				//if($("#createSchedule").valid())
   				$.ajax({
   						url				: '{{route('admin.job-applications.store-schedule')}}',
   						container		: '#app_interview_create_frm',
   						type			: "POST",
   						data			: $('#app_interview_create_frm').serialize(),
   						success			: function (response) {
   																if(response.status == 'success'){
   																	Swal.fire("Success", "Interview schedule successfully!", "success");	
   																}
   						}
   				})  
   		
   		} 
   
   
   $(document).ready(function(){
   
   	$('body').on('click', '#sendEmailBtn', function(){
   		
   	
   				$.ajax({
   						url				: '{{route('admin.job-applications.emailschedule')}}',
   						container		: '#app_interview_create_frm',
   						type			: "POST",
   						data			: $('#app_interview_create_frm').serialize(),					
   						success			: function (response) {
   																if(response.status == 'success'){
   																	Swal.fire("Success", "Interview schedule successfully!", "success");	
   																}
   						}
   				})
   				
   	/*var data 			=	new FormData();
      	var form_data 		=	$('#app_interview_create_frm').serializeArray();
      	$.each(form_data, function (key, input) {
          		data.append(input.name, input.value);
      	});  	
      	var file_data 		= $('input[name="attachment"]')[0].files;
      	for (var i = 0; i < file_data.length; i++) {
          		data.append("attachment", file_data[i]);
      	}  
      	var editor	=	tinymce.get("editor").getContent();
       data.append("editor", editor);
      	//data.append('key', 'value');
   	
      	$.ajax({
      				
   				url					: '{{route('admin.job-applications.emailschedule')}}',
      				headers				: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
          			method				: "POST",
          			processData			: false,
          			contentType			: false,
         		 		data				: data,
      				redirect			: true,
          			success				: function (data) {
      										if(data.status == 'success'){
      												// window.location = '{{ route('admin.clients.index') }}'	
      										}						
          			}
      			});*/
   	});
   });
   function getInterviewModeChild(id, job_id)
         {  		
   	  	 $('#url_div').hide();
   		 $('#lbl_call_mode').html('');
   		 $('#url').hide('');
   		 
   		 
   		 		  
   		  var url       	= "{{ route('admin.job-applications.getinterviewchild',':id') }}";
             url         		= url.replace(':id', id);
             var baseurl     	= "{{ asset('assets/images/Loading2.gif') }}"; 
             var token       	= "{{ csrf_token() }}";
             
              $.ajax({
                       	type	:	'POST',
                       	url		:	url,
                       	data	:	{'_token': token, '_method': 'POST', 'id' : id, 'job_id' : job_id},
                       	beforeSend	: function() {
                                     					$('#childInterview').html('<img src="'+baseurl+'" height="35px" width="100%">');
                       	},
                       	success: function (response) 
                       	{
                         			$('#childInterview').html('');
                           		$('#childInterview').html(response.data);                        
                            		
                          },
                       	complete: function (data) {
                           //$('#childStatus_'+current_application_id).html('');
                        	}
                  });
          
   	  }
   function getVideoMeetingUrl(currentId, ParentId, job_id )
   	{
   		$("#url").prop("readonly", false);
   		
   		var baseurl     	= "{{ asset('assets/images/Loading2.gif') }}"; 
   		
   		$('#url_div').hide(); 
   		$('#url').val('');	
   		
   		
   		if((ParentId == 2) && (currentId ==2 ))		// FOR GOOGLE
   		{
   					
   					$('#url_div').show();
   					$('#url').show();
   					$('#lbl_call_mode').show();
   					$('#lbl_call_mode').html('Google Meet Url');
   																			
   					var interviewDate	=	$('#interviewDate').val();
   					if(interviewDate == '')
   					{
   						alert("Please select interview date");
   						return false;
   					}
   						
   					var url 			= "{{ route('admin.job-applications.getGoogleVideoUrl',':currentId') }}";
   					url 				= url.replace(':currentId', currentId);
   					var baseurl 		= "{{ asset('assets/images/Loading2.gif') }}"; 
   					var token 			= "{{ csrf_token() }}";
   
   					$.ajax({
   									type	: 'POST',
   									url		: url,
   									data	: { '_token': token, '_method': 'POST', 'currentId' : currentId, 'ParentId': ParentId, 'job_id': job_id , 'interviewDate': interviewDate },
   									beforeSend: function() 
   									{			
   															//	
   																
   									},
   									success: function (response) 
   									{
   										if(response.status == "success")
   										{
   											
   											$('#url_div').show();
   											$('#url').val(response.output.hangoutLink);	
   												
   										}
   										else
   										{
   											
   $('.sub_status').hide();
   //$('#childStatus').html(response.data);
   										}
   									},
   								complete: function (data) 
   								{
   											//$('#childStatus_'+current_application_id).html('');
   								}
   							});
   					
   		}
   		if((ParentId == 2) && (currentId ==3 ))		// FOR ZOOM
   		{
   					
   					var interviewDate	=	$('#interviewDate').val();
   					if(interviewDate == '')
   					{
   						alert("Please select interview date");
   						return false;
   					}
   						
   					var url 			= "{{ route('admin.job-applications.getVideoUrl',':currentId') }}";
   					url 				= url.replace(':currentId', currentId);
   					var baseurl 		= "{{ asset('assets/images/Loading2.gif') }}"; 
   					var token 			= "{{ csrf_token() }}";
   
   					$.ajax({
   									type	: 'POST',
   									url		: url,
   									data	: { '_token': token, '_method': 'POST', 'currentId' : currentId, 'ParentId': ParentId, 'job_id': job_id , 'interviewDate': interviewDate },
   									beforeSend: function() 
   									{
   																//$('#url_div').show();
   																//$('#url_div').html('<img src="'+baseurl+'" height="35px" width="100%">');
   									},
   									success: function (response) 
   									{
   										if(response.status == "success")
   										{
   												$('#url_div').show();
   												$('#url').show();
   												$('#lbl_call_mode').show();
   												$('#lbl_call_mode').html('Zoom Meet Url');			
   												if(response.mode == 'zoom' ){
   													$('#url').val(response.output.join_url);	
   												}else{
   													$('#url').val('');	
   												}
   											
   										}
   										else
   										{	
   												$('.sub_status').hide();
   												//$('#childStatus').html(response.data);
   										}
   									},
   								complete: function (data) 
   								{
   											//$('#childStatus_'+current_application_id).html('');
   								}
   							});
   		}
   		
   		if((ParentId == 2) && currentId ==6 )		// FOR OTHER
   		{
   				$('#url_div').show();
   				$('#url').show();
   				$('#lbl_call_mode').show();
   				$('#lbl_call_mode').html('Enter Custom Url');
   		}
   		else if((ParentId ==1 ) && ((currentId == 1) || (currentId == 4) ) )   // PHONE CALL
   		{
   			$('#url_div').show();
   			$('#url').show();
   			$('#lbl_call_mode').show();
   			$('#lbl_call_mode').html('');
   			if(currentId == 1){
   				$('#lbl_call_mode').html('Phone Call');
   				$('#url').val('Phone Call');
   				$("#url").prop("readonly", true);
   			}
   			if(currentId == 4){
   				$('#lbl_call_mode').html('Skype Call');
   				$('#url').val('Skype Call');
   				$("#url").prop("readonly", true);
   			}
   		}
   		else if((ParentId ==3 ) && (currentId > 0) )
   		{
   			$('#url_div').show();
   			$('#url').show();
   			$('#lbl_call_mode').show();
   			$('#lbl_call_mode').html('Interview Address/Location');
   			
   		 	var location 	=	$('#interview_mode_lavels :selected').text();
   		 	$('#url').val(location);	
   		}	
   	}		   
</script>
<?php 
   //echo '<pre>'; print_r($application);
?>