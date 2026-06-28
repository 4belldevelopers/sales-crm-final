@php 
   use App\Helper\GeneralMethod;
@endphp

@php 
   
   $bgTextDecore		=	$strVal	=	$icon	=	$str	=	'';
   $str					=	trim(@$application->submitted_by_name);
   $str	= $strVal		=	strtolower($str[0]);
   $str					=	ord($str);
   if($str >=97 && $str <=101){
   		$bgTextDecore		=	'fs-3 bg-light-danger text-danger';	
   }else if($str >=102 && $str <=106){
   		$bgTextDecore		=	'fs-3 bg-light-warning text-warning';	
   }else if($str >=107 && $str <=111){
   		$bgTextDecore		=	'fs-3 bg-light-primary text-primary';	
   }else if($str >=112 && $str <=116){
   		$bgTextDecore		=	'fs-3 bg-light-success text-success';	
   }else if($str >=117 && $str <=121){
   		$bgTextDecore		=	'fs-3 bg-dark text-secondary';	
   }else{
   		$bgTextDecore		=	'fs-3 bg-light-secondary text-dark';	
   }
   
   $posted_by_image = '';
				$posted_by_image = url('public/avatar.png');
				if (!empty($application->posted_by_image)) {
					$posted_by_image = url('public/user-uploads/team_profile_img/' . @$application->posted_by_image);
				}
				
				 
   	if(!empty($posted_by_image)){
     $icon	= '<div class="d-flex flex-column">
                    <div class="symbol-group symbol-hover">
                      <div class="symbol symbol-30 symbol-circle" data-toggle="tooltip" title="' . @$application->submitted_by_name . '">
                        <img alt="Pic" src="' . @$posted_by_image . '">
                      </div>
                  </div>';
   	}else{
   	
   $icon	= '<div class="symbol symbol-circle symbol-25px overflow-hidden border-radius-0" style="float:left"><a href="javascript:void(0);" class=""><div class="symbol-label '.$bgTextDecore.' border-radius-5 fw-bolder" style="font-size:13px!important;" data-toggle="tooltip" title="'.@$application->submitted_by_name.'">'.ucwords($strVal).'</div></a></div>'; 
   }
    if(!empty($company->logo_logical_path)){
		$icon	= '<div class="symbol symbol-circle symbol-25px overflow-hidden border-radius-0">
						<a href="javascript:void(0);">
							<div class="symbol-label border-radius-5">
							<img src="'.url('public/'.@$company->logo_logical_path).'" alt="Company Logo" class="border-radius-5 w-100">
							</div>
						</a>
					</div>';
    }	
	  			  
@endphp 

<form class="form fv-plugins-bootstrap fv-plugins-framework" id="status_frm" name="status_frm" method="post" enctype="multipart/form-data">
  <meta name="csrf-token" content="{{ csrf_token() }}" />
  <input type="hidden" name="_token" value="{{ csrf_token() }}">
  <input type="hidden" name="application_id" id="application_id" value="{{ @$application->id }}" />
  <input type="hidden" name="job_wise_condidates_id" id="job_wise_condidates_id" value="{{ @$application->job_wise_condidates_id }}" />
  <div class="modal-body scroll-y py-0 px-lg-7">
    <div data-kt-element="options">
      <div class="pb-5">
        <table class="table align-middle table-row-dashed fs-6 mt-5" id="">
          <thead>
            <tr class="fw-bolder text-muted" role="row">
              <th class="">Name</th>
              {{-- <th class="">Contacts</th> --}}
              <th class="">Exp.</th>
              <th class="">Salary</th>
              <th class="">Notice Period</th>
              <th class="">Qualification</th>
              <th class="">Location</th>
              <th class="">Source</th>
              <th class="">Jobs</th>
            </tr>
          </thead>
          <tbody class="text-gray-600 fw-bold">
            <tr class="">
              <td class="d-flex align-items-center border-bottom-0">{{ @$cicon}}
                <div class="d-flex flex-column"> 
				<a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6" data-toggle="tooltip" title="{{ @$application->full_name }}"> 
					{{ ucwords(substr(@$application->full_name, 0, 10)) }} 
				</a> 
				<span class="text-muted fw-bold d-block fs-7" data-toggle="tooltip" title="{{ @$application->current_designation }}">
					{{ ucwords(substr(@$application->current_designation, 0, 10)) }}
				</span> 
				</div>
              </td>
              {{-- <td class="">
				<div class="d-flex flex-column"> 
					<a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">{{ @$application->phone }}</a> 
					<a href="javascript:void(0);" class="text-muted fw-bold d-block fs-7 text-hover-primary" data-toggle="tooltip" title="{{ @$application->email }}">
						{{ substr(@$application->email, 0, 10) }}
					</a> 
				</div>
			  </td> --}}
              <td class="">
			  	<div class="d-flex flex-column"> 
					<span class="text-dark fw-bolder fs-6">{{ @$application->total_exp }}+</span> 
					<span class="text-muted fw-bold d-block fs-7">{{ @$application->rel_exp}}+</span> 
				</div>
			  </td>
              <td class="">
			  	<div class="d-flex flex-column"> 
					<span class="text-dark fw-bolder fs-6">{!! @$expected_ctc !!} </span> 
					<span class="text-muted fw-bold d-block fs-7 {{ (@$condidate_type=="bench_condidate") ? 'd-none'  : ''}}">{!! @$current_ctc !!}</span> </div></td>
              <td class="">
				@php
                    $np = $application->np_id;
                  if (empty($np)) {
                      $np = 'N/A';
                    }
                    if ($application->serving_notice_period_mode == 0) {
                      $np = $application->np_id . " Days";
                      if ($application->np_id == 1) {
                        $np = $application->np_id . '  Day';
                      }
                    } elseif ($application->serving_notice_period_mode == 1) {
                      $np = $application->np_id;
                      if ($np != 'Immediate') {
                        $np =  GeneralMethod::ymdTodmyNotTime($application->np_id); //date("d-M-y", strtotime($application->np_id));
                      }
                    }  
                @endphp
			  	<div class="d-flex flex-column"> 
					<span class="text-dark fw-bolder fs-6">{{ @$np }}</span> 
					<span class="text-muted fw-bold d-block fs-7">{{ @$application->current_company }}</span> 
				</div>
			 </td>
              <td class="">
			  	<div class="d-flex flex-column"> 
					<span class="text-dark fw-bolder fs-6">{{ ucwords(@$qualification->name) }}</span> 
					<span class="text-muted fw-bold d-block fs-7" data-toggle="tooltip" title="">{{ @$application->current_designation }}</span> </div>
			</td>
              <td class="">
			  	<div class="d-flex flex-column"> 
					<span class="text-dark fw-bolder fs-6">{{ @$row_city_name }}</span> 
					<span class="text-muted fw-bold d-block fs-7" data-toggle="tooltip" title="{{ @$row_state_name }} | {{ strtoupper(@$row_country_name)}} ">
						{{ substr(@$row_state_name, 0, 6) }} | {{ strtoupper(substr(@$row_country_name, 0, 2)) }}
					</span> 
				</div>
			  </td>
              <td class="">
			  	<div class="d-flex flex-column">
					<span class="text-dark fw-bolder fs-6">{{ @$application->submitted_by_name }}</span>
                  <span class="text-muted fw-bold d-block fs-7">{{ ucwords(@$company->company_name) }}</span> 
				</div>
			</td>
              <td class="">
			  	<div class="d-flex flex-column"> 
					@php
				    	$company_or_client = GeneralMethod::companyDetailsByClient(@$application->job_id);
					@endphp
					<span class="text-dark fw-bolder fs-6" data-toggle="tooltip" title="{{ @$application->title }}">{{ substr(ucwords(@$application->title), 0, 10) }}</span> 
					<span class="text-muted fw-bold d-block fs-7" data-toggle="tooltip" title="{{ @$company_or_client }}">{{ substr(ucwords(@$company_or_client), 0, 10) }}</span> 
				</div>
			 </td>
            </tr>
          </tbody>
        </table>
      
        <label class="text-dark mb-0 mt-3 fw-bolder fs-5">Stages</label>{{ @$requisition_owner }}
        <div class="row py-5 w-100 d-inline-block justify-content-between align-items-center">
          <?php 
                        //$cnt=0;
                        //$idArr	=	array('inreview', 'sourcing', 'screening', 'evaluation', 'negotation', 'offer', 'joined', 'rejected');
                     $none_cnt	=	0;
					 if(!empty($master_status))
                     {
                        foreach($master_status as $mstatus)
                        {
                              $master_status_css = $chk	=	$text_name	= "";
                              if($mstatus->id == @$application->master_status_id )
                              {
                                 $master_status_css  	= @$mstatus->css_style;	
                                 $chk					= 'checked="checked"';
                              } 
                                 $text_name	= @$mstatus->text_name;    
						
						$d_none		=	'';	
						if($none_cnt > 1){
							//$d_none		=	'd-none';
						}
						 $none_cnt++;
						  					 	 
          ?>
	
		  
<div class="<?php echo @$mstatus->divname; ?> d-inline-block w-12 px-2 client_master_status_div <?php echo @$d_none; ?>" data-master-status-id="<?php echo @$mstatus->id; ?>">
<input type="radio" class="btn-check master_status" name="master_status" value="<?php echo @$mstatus->id; ?>" <?php echo @$chk; ?> id="<?php echo @$text_name; ?>">
<label	class="lbl_common_master btn-<?php echo @$text_name; ?>  btn  btn-outline btn-outline-dashed btn-outline-default d-flex align-items-center justify-content-center" for="<?php echo @$text_name; ?>" id="lbl_master_status_<?php echo @$mstatus->id; ?>"> 
	<span class="d-block fw-bold text-start"> 
		<span	class="text-dark-800 fw-bolder d-block fs-5"><?php echo @$mstatus->name; ?></span> 
	</span> 
</label>
</div>
	
		  
<?php 
                           //$cnt++;
                        }
                     }	 
?>
        </div>
        <!--end::Stages-->
        <!--begin::Sub Stages-->
		<?php 
        if(!empty($candidate_status_Arr)){	
					?>
			<div id="main_substage">
			<label class="text-dark mb-0 mt-3 fw-bolder fs-5">Sub Stages</label>
			<div class="row py-5 w-100 d-inline-block justify-content-between align-items-center substage" id="substage">
				<?php 
					if(!empty($candidate_status_Arr))
					{	
						$cnt	=	1;
						foreach($candidate_status_Arr as $cantData)
						{
							$master_status_css = $chk	= "";
							if($cantData->id == @$application->child_status_id )
							{
								//$master_status_css  = @$mstatus->css_style;	
								$chk				= 'checked="checked"';
							}
							?>
				<div class="div<?php echo $cnt; ?> d-inline-block w-14 px-2">
				<input type="radio" class="btn-check" name="sub_stages" value="<?php echo $cantData->id ?>" id="screening<?php echo $cnt; ?>" <?php echo @$chk; ?> >
				<label class="btn btn-outline btn-outline-dashed btn-outline-default p-3 d-flex align-items-center justify-content-center" for="screening<?php echo $cnt; ?>"> <span class="d-block fw-bold text-start"> <span class="text-dark-800 fw-bolder d-block fs-5"><?php echo $cantData->name ?></span> </span> </label>
				</div>
				<?php 
							$cnt++;
						}
					}	
					?>
			</div>
			</div>
			
		<?php 
		}	
        ?>

        <!--end::Sub Stages-->
        <!--begin::Comment-->
        <div class="row" id="comment_div" style="display:none">
          <div class="py-5">
            <label class="text-dark fw-bolder fs-5">Comment</label>
          </div>
          <textarea id="comment" name="comment" class="form-control form-control-solid" rows="5" placeholder="Write comment here..."></textarea>
        </div>
        <!--end::Comment-->
      </div>
    </div>
  </div>
  <div class="modal-footer flex-center">
    <div class="div1">
      <!--begin::Cancel-->
      <button type="button" id="" class="btn btn-secondary me-3 close" data-bs-dismiss="modal" data-dismiss="modal">Close</button>
      <!--end::Cancel-->
      <!--begin::Submit-->
      <button type="button" id="int_stage_submit" class="btn btn-primary"> <span class="indicator-label">Submit</span> <span class="indicator-progress">Please wait... <span class="spinner-border spinner-border-sm align-middle ms-2"></span> </span> </button>
      <!--end::Submit-->
    </div>
    <!-- style=""position: absolute;right: 2%; -->
    <div class="div2 position-absolute end-0 me-5 d-none" >
      <!--begin::Rescheduled-->
      <button type="button" id="" class="btn btn-secondary">Rescheduled</button>
      <!--end::Rescheduled-->
    </div>
  </div>
</form>

<script>

	var master_status = <?php echo @$requisition_owner; ?>;
	if(master_status == 0){
		$('#main_substage').hide();
	}else{
		$('#main_substage').show();	
	}
	
</script>   
<script>



   $('body').on('click', '.client_master_status_div', function(e){
   
 		e.preventDefault(); 
		
   		var id 			=	$(this).data('master-status-id');
   		$("#master_status_"+id).attr('checked', true);
   		$('#main_substage').show();
   		$('#comment_div').hide();
   		
		
		$("#inreview").attr('checked', false);
		$("#sourcing").attr('checked', false);
		$("#rejected").attr('checked', false);
		
		//$("#evaluation").attr('checked', false);
		//$("#negotation").attr('checked', false);
		//$("#offer").attr('checked', false);
		//$("#joined").attr('checked', false);
		//$("#rejected").attr('checked', false);
		
   		if(id == 1 || id == 9 ||id == 7){
			$('#int_stage_submit').show();
		}else{
			$('#int_stage_submit').hide();	
		}
   		
				
   		if(id == 1){
			$("#sourcing").attr('checked', true);
			$('#lbl_master_status_'+id).addClass('sourcing-bg');	
   		}
		else if(id == 2){
   			$('#lbl_master_status_'+id).addClass('screening-bg');	
   		}
		else if(id == 3){
   			$('#lbl_master_status_'+id).addClass('interview');	
   		}
		else if(id == 4){
   			$('#lbl_master_status_'+id).addClass('btn-negotation');	
   		}
		else if(id == 5){
   			$('#lbl_master_status_'+id).addClass('btn-offer');	
   		}
		else if(id == 6){
   			$('#lbl_master_status_'+id).addClass('btn-joined');	
   		}
		else if(id == 9){
			$("#rejected").attr('checked', true);
   			$('#lbl_master_status_'+id).addClass('btn-rejected');	
   		}
		else if(id == 7){
			$("#rejected").attr('checked', true);
   			$('#lbl_master_status_'+id).addClass('btn-rejected');
   			$('#main_substage').hide();	
   			$('#comment_div').show();
   		}
		else if(id == 8){
			$("#inreview").attr('checked', true);
   			$('#lbl_master_status_'+id).addClass('bg-light-warning text-warning'); 	
   			$('#main_substage').hide();   			
   		}	
   		
   		if(id > 0 && (id!=8) )
   		{
			
   			   var baseurl     	= "{{ asset('assets/images/Loading2.gif') }}"; 			   
                  var url    		= "{{ route('service_company.job-applications.child_stage',':id') }}";
                  url      		= url.replace(':id', id);
                  $.ajax({
                           	type  : 'GET',
                           	url   : url,
   							beforeSend	: function() {
                                     					$('#substage').html('<img src="'+baseurl+'" height="35px" width="100%">');
                       		},
                           	success : function (response) {
                                               if (response.status == "success") 
                                     			{
   												     $('#substage').html('');
                                                     $('#substage').html(response.html);
                                               }
                                           }
                  });	
   		
   		}
		
		var master_status = <?php echo @$requisition_owner; ?>;
		if(master_status == 0){
			$('#main_substage').hide();
		}else{
			$('#main_substage').show();	
		}
		 e.run(); 
   			
   });
   
  
</script>
