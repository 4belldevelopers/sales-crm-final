<!--begin::Body-->
<style>
.form-check.form-check-custom .form-check-label {
    margin-left: 2.55rem;
}
</style>
@php 
					$bgTextDecore			=	$strVal	=	'';
					$str					=	trim(@$company->company_name);
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
					}else {
						$bgTextDecore		=	'fs-3 bg-light-secondary text-dark';	
					}	
					$icon	= '<div class="symbol symbol-circle symbol-40px overflow-hidden me-2 border-radius-5" style="float:left">
                    <a href="javascript:void(0);" data-row-id="'.$company->id.'" class="show-detail">
                      <div class="symbol-label '.$bgTextDecore.' border-radius-5">'.ucwords($strVal).'</div>
                    </a>
                  </div>';
				  
				  if(!empty($company->logo_logical_path)){
				  		$icon	= '<div class="symbol symbol-circle symbol-40px overflow-hidden me-2  border-radius-5">
                                        <a href="javascript:void(0);">
                                            <div class="symbol-label border-radius-5">
                                            <img src="'.url('public/'.@$company->logo_logical_path).'" alt="Company Logo" class="border-radius-5 w-100">
                                            </div>
                                        </a>
                                    </div>';
				  }	
				  
@endphp

<div class="card-header px-5 py-0" id="kt_activities_header">
  <div class="d-flex align-items-center py-1">
    <div class=" d-flex align-items-center"> {!! @$icon !!}
      <div class="d-flex flex-column"> <a href="javascript:void(0);" class="text-dark fw-bolder fs-6" id="light_recruiter_name">{{ ucwords($company->company_name) }}</a> </div>
    </div>
    <span class="h-20px border-gray-200 border-start mx-4 d-none"></span> </div>
  <div class="card-toolbar">
    <button type="button" class="btn btn-sm btn-icon btn-active-light-primary me-n5" id="kt_activitiesIntTeam_close"> <span class="svg-icon svg-icon-1">
    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
      <g transform="translate(12.000000, 12.000000) rotate(-45.000000) translate(-12.000000, -12.000000) translate(4.000000, 4.000000)" fill="#000000">
        <rect fill="#000000" x="0" y="7" width="16" height="2" rx="1" />
        <rect fill="#000000" opacity="0.5" transform="translate(8.000000, 8.000000) rotate(-270.000000) translate(-8.000000, -8.000000)" x="0" y="7" width="16" height="2" rx="1" />
      </g>
    </svg>
    </span> </button>
  </div>
</div>
<!--begin::Body-->
<div class="card-body position-relative px-0 py-0" id="kt_activities_body">
  <div id="kt_activities_scroll" class="position-relative scroll-y bg-light pt-2 h-100" data-kt-scroll="true" data-kt-scroll-height="auto" data-kt-scroll-wrappers="#kt_activities_body" data-kt-scroll-dependencies="#kt_activities_header, #kt_activities_footer" data-kt-scroll-offset="5px" style="">
    <!--begin::Team card-->
    <div class="card card-custom mx-2">
      <!--begin::Team header-->
      <div class="card-header cursor-pointer">
        <!--begin::Card title-->
        <div class="card-title m-0">
          <h3 class="fw-bolder m-0">Recruiter</h3>
        </div>
        <!--end::Card title-->
        <!--begin::Action-->
        <a href="javascript:void(0);" class="btn btn-primary align-self-center d-none" data-toggle="modal" data-target="#add_team_member">Add Member</a>
        <!--end::Action-->
      </div>
      <!--end::Team header-->
      <!--begin::Team Content-->
      <div class="card-body py-7">
        <!--begin::Table-->
        <table class="table align-middle table-row-dashed fs-6 gy-5" id="Iteam_Table">
          <!--begin::Table head-->
          <thead>
            <!--begin::Table row-->
            <tr class="fw-bolder text-muted">
              <th class="w-10px pe-0"> <div class="form-check form-check-sm form-check-custom form-check-solid me-3">
                  <input class="form-check-input" type="checkbox" data-kt-check="true" data-kt-target="#Iteam_Table .form-check-input-Item" value="1">
                </div></th>
              <th class="min-w-150px">Name</th>
              <th class="min-w-150px">Designation</th>
              <th class="min-w-150px">Working Status</th>
              <th class="min-w-150px">Action</th>
            </tr>
            <!--end::Table row-->
          </thead>
          <!--end::Table head-->
          <!--begin::Table body-->
          <tbody class="text-gray-600 fw-bold">
            @php 
								foreach($team as $teamVal)
								{
									
									$bgTextDecore = $strVal = '';
									$str 			= trim(@$teamVal->name);
									$str 			= $strVal = strtolower($str[0]);
									$str = ord($str);
									if ($str >= 97 && $str <= 101) {
										$bgTextDecore = 'fs-3 bg-light-danger text-danger';
									} 
									else if ($str >= 102 && $str <= 106) {
										$bgTextDecore = 'fs-3 bg-light-warning text-warning';
									} 
									else if ($str >= 107 && $str <= 111) {
										$bgTextDecore = 'fs-3 bg-light-primary text-primary';
									} 
									else if ($str >= 112 && $str <= 116) {
										$bgTextDecore = 'fs-3 bg-light-success text-success';
									} 
									else if ($str >= 117 && $str <= 121) {
										$bgTextDecore = 'fs-3 bg-dark text-secondary';
									} else {
										$bgTextDecore = 'fs-3 bg-light-secondary text-dark';
									}

									$working_status	= $working_status_style	=	'';
									$working_status			=  @$teamVal->working_status ==1 ? 'Enable' : 'Disable';
									$working_status_style	=  @$teamVal->working_status ==1 ? 'text-success' : 'text-danger';
			@endphp
            <!--begin::Table row-->
            <tr>
              <!--begin::Checkbox-->
              <td><div class="form-check form-check-sm form-check-custom form-check-solid">
                  <input class="form-check-input form-check-input-Item" type="checkbox" value="1">
                </div></td>
              <!--end::Checkbox-->
              <!--begin::Name-->
              <td><div class="d-flex align-items-center">
                  <!--begin::Avatar-->
                  <div class="symbol symbol-circle symbol-40px overflow-hidden me-2 border-radius-5"> <a href="javascript:void(0);" class="">
                    <div class="symbol-label {{ @$bgTextDecore }} border-radius-5">{{ strtoupper(@$strVal) }}</div>
                    </a> </div>
                  <!--end::Avatar-->
                  <!--begin::Name details-->
                  <div class="d-flex flex-column"> <span class="text-dark fw-bolder fs-6">{{ ucwords(substr(@$teamVal->name, 0, 15)) }}</span> <span class="text-muted fw-bold d-block fs-7">&nbsp;</span> </div>
                  <!--end::Name details-->
                </div></td>
              <!--end::Name-->
              <!--begin::Role-->
              <td><div class="d-flex flex-column"> <span class="text-dark fw-bolder fs-6">{{ ucwords(substr(@$teamVal->display_name, 0, 15)) }}</span> </div></td>
              <!--begin::Role-->
              <!--begin::Status-->
              <td><div class="d-flex flex-column"> <span class="{{ @$working_status_style }} fw-bolder fs-6">{{ @$working_status }}</span> </div></td>
              <!--end::Status-->
              <!--begin::Action-->
              <td><div class="d-flex flex-column"> <a id="team_select_{{ @$teamVal->id }}" href="javascript:void(0);" class="{{ ($allow) ? 'cp_selectour': 'cp_select' }} btn btn-sm btn-light-primary w-130" 
											 rel="{{ @$teamVal->id }}" data-assign="0" data-title="{{ @$teamVal->name }}"  data-email="{{ @$teamVal->email }}"  >Add</a> </div></td>
              <!--end::Action-->
            </tr>
            <!--end::Table row-->
            @php }  @endphp 
          </tbody>
          <!--end::Table body-->
        </table>
        <!--end::Table-->
      </div>
      <!--end::Team Content-->
    </div>
    <!--end::Team card-->
  </div>
</div>
<!--end::Body-->
<script>

$( document ).ready(function() {
	
	
	var team 			= <?php echo json_encode($team); ?>;
	var jqueryarray 	= <?php echo json_encode($panellistArr); ?>;
	for (var i = 0; i < team.length; i++) {
			var pid		=	team[i].id;
			var pname	=	team[i].name.trim();
			// INNER ARRAY
			for (var j = 0; j < jqueryarray.length; j++) {
            		var cname		=jqueryarray[j].trim();
					if(pname == cname){
					
								$('#team_select_'+pid).addClass("btn-light-danger");
								$('#team_select_'+pid).removeClass("btn-light-primary");
								$('#team_select_'+pid).text('Remove');
								$('#team_select_'+pid).attr("data-assign", 1);
					
					}
        	};
     };
		
	
	 $('.cp_select').on('click', function(e) {
   			
			
			var rel						= 	$(this).attr('rel');
			var assign					=	$('#team_select_'+rel).attr("data-assign");
			var title					=	$('#team_select_'+rel).attr("data-title");
			var email					=	$('#team_select_'+rel).attr("data-email");
			
			if(assign ==0)
			{
				$(this).addClass("btn-light-danger");
				$(this).removeClass("btn-light-primary");
				$(this).text('Remove');
				$(this).attr("data-assign", 1);
				AddEvent(title, email);
			}
			else if(assign ==1)
			{
				$(this).addClass("btn-light-primary");
				$(this).removeClass("btn-light-danger");
				$(this).text('Add');
				$(this).attr("data-assign", 0);	
				DeleteEvent(title, email);
			}
			
	});
	
	$('.cp_selectour').on('click', function(e) {
   			
			
			   var rel						= 	$(this).attr('rel');
			   var assign					=	$('#team_select_'+rel).attr("data-assign");
			   var title					=	$('#team_select_'+rel).attr("data-title");
			   var email					=	$('#team_select_'+rel).attr("data-email");
			   
			   if(assign ==0)
			   {
				   $(this).addClass("btn-light-danger");
				   $(this).removeClass("btn-light-primary");
				   $(this).text('Remove');
				   $(this).attr("data-assign", 1);
				   AddEventOur(title, email);
			   }
			   else if(assign ==1)
			   {
				   $(this).addClass("btn-light-primary");
				   $(this).removeClass("btn-light-danger");
				   $(this).text('Add');
				   $(this).attr("data-assign", 0);	
				   DeleteEventOur(title, email);
			   }
			   
	   });

});

function AddEventOur(name, email)
{
		var currentValue			=	$('#ourpanellist').val();
		var cc_email				=	$('#cc_email').val();
		if(currentValue !='')
		{
			var includeStr			=	currentValue.includes(name);
			if(includeStr  === false)
			{
				var assignNewValue	=	currentValue +' , '+name;	
				$('#ourpanellist').val(assignNewValue);	
				
				var assignNewEmail	=	cc_email +' , '+email;	
				$('#cc_email').val(assignNewEmail);
			}							
		}
		else   
		{
			$('#ourpanellist').val(name);	
			$('#cc_email').val(email);
		}
}

function DeleteEventOur(contact_person_name, email)
{	 
	var tem_hiring_namager		=	'';  
	var temp					=	[]; 
	var hiring_namager			=	$('#ourpanellist').val(); 
	
	
	var team_hiring_manager_email		=	''; 
	var tempEmail						=	[]; 
	var cc_email						=	$('#cc_email').val();
	
	if(hiring_namager !='')
	{
		var hiring_namagerArr = hiring_namager.split(',');
		for(var cnt=0; cnt<hiring_namagerArr.length; cnt++)
		{
			if(contact_person_name.trim() != hiring_namagerArr[cnt].trim()){
				temp.push(hiring_namagerArr[cnt]);
			
			}			
		}
		tem_hiring_namager	=	temp.toString();
				
		var emailArr 	= cc_email.split(',');
		for(var inc=0; inc<emailArr.length; inc++)
		{
			if(email.trim() != emailArr[inc].trim()){
				tempEmail.push(emailArr[inc]);
			
			}			
		}
		team_hiring_manager_email	=	tempEmail.toString();
		
				 
	}	
	
	$('#ourpanellist').val(tem_hiring_namager);		
	$('#cc_email').val(team_hiring_manager_email);			
}

function AddEvent(name, email)
{
		var currentValue			=	$('#panellist').val();
		var cc_email				=	$('#cc_email').val();
		if(currentValue !='')
		{
			var includeStr			=	currentValue.includes(name);
			if(includeStr  === false)
			{
				var assignNewValue	=	currentValue +' , '+name;	
				$('#panellist').val(assignNewValue);	
				
				var assignNewEmail	=	cc_email +' , '+email;	
				$('#cc_email').val(assignNewEmail);
			}							
		}
		else   
		{
			$('#panellist').val(name);	
			$('#cc_email').val(email);
		}
}
	 
function DeleteEvent(contact_person_name, email)
{	 
	var tem_hiring_namager		=	'';  
	var temp					=	[]; 
	var hiring_namager			=	$('#panellist').val(); 
	
	
	var team_hiring_manager_email		=	''; 
	var tempEmail						=	[]; 
	var cc_email						=	$('#cc_email').val();
	
	if(hiring_namager !='')
	{
		var hiring_namagerArr = hiring_namager.split(',');
		for(var cnt=0; cnt<hiring_namagerArr.length; cnt++)
		{
			if(contact_person_name.trim() != hiring_namagerArr[cnt].trim()){
				temp.push(hiring_namagerArr[cnt]);
			
			}			
		}
		tem_hiring_namager	=	temp.toString();
				
		var emailArr 	= cc_email.split(',');
		for(var inc=0; inc<emailArr.length; inc++)
		{
			if(email.trim() != emailArr[inc].trim()){
				tempEmail.push(emailArr[inc]);
			
			}			
		}
		team_hiring_manager_email	=	tempEmail.toString();
		
				 
	}	
	
	$('#panellist').val(tem_hiring_namager);		
	$('#cc_email').val(team_hiring_manager_email);			
}  	
</script>
