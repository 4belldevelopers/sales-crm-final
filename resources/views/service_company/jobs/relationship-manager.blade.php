<!--begin::Body-->
<style>
.form-check.form-check-custom .form-check-label
{
    margin-left: 2.55rem;
}
</style>
<!--begin::Body-->
<div class="card-body position-relative px-0 py-0" id="kt_activities_body">
  <div id="kt_activities_scroll" class="position-relative scroll-y bg-light pt-2 h-100" data-kt-scroll="true" data-kt-scroll-height="auto" data-kt-scroll-wrappers="#kt_activities_body" data-kt-scroll-dependencies="#kt_activities_header, #kt_activities_footer" data-kt-scroll-offset="5px" style="">
    <!--begin::Team card-->
    <div class="card card-custom mx-2">
      <!--begin::Team header-->
      <div class="card-header cursor-pointer">
        <!--begin::Card title-->
        <div class="card-title m-0" style="width: 95%;">
			<div class="search-container input-group input-group-lg input-group-solid">
				<div class="input-group-prepend"> <span
					class="input-group-text"> <i class="la la-search"></i>
				</span>
			  </div>
				<input class="form-control form-control-lg form-control-solid" type="text" id="searchInput" placeholder="Search for team members....">
			</div>
          {{-- <h3 class="fw-bolder m-0">Team Member</h3> --}}
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
		

        <table class="table align-middle table-row-dashed fs-6 gy-5" id="Iteam_Table_id">
          <!--begin::Table head-->
          <thead>
            <!--begin::Table row-->
            <tr class="fw-bolder text-muted">
              <th class="min-w-150px">Name</th>
              <th class="min-w-150px">Jobs</th>
			  <th class="min-w-150px">Relationship Manager</th>
              <th class="min-w-150px">Action</th>
            </tr>
            <!--end::Table row-->
          </thead>
          <!--end::Table head-->
          <!--begin::Table body-->
          <tbody class="text-gray-600 fw-bold">
            <?php 
								foreach($team as $teamVal)
								{
									$activeJobs = DB::table('job_assign')->join('jobs','jobs.id','=','job_assign.job_id')->where('jobs.status','=','active')->where('job_assign.user_id', $teamVal->id)->count();
                  $candidateCount = DB::table('bench_assign')->where('bench_assign.user_id', $teamVal->id)->count();
									
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
										$bgTextDecore = 'fs-3 bg-dark text-white';
									} else {
										$bgTextDecore = 'fs-3 bg-light-secondary text-dark';
									}

									$working_status	= $working_status_style	=	'';
									$working_status			=  @$teamVal->working_status ==1 ? 'Enable' : 'Disable';
									$working_status_style	=  @$teamVal->working_status ==1 ? 'text-success' : 'text-danger';
									
									
						?>
            <!--begin::Table row-->
            <tr>
              <!--begin::Name-->
              <td>
                    <div class="d-flex align-items-center">
                    <!--begin::Avatar-->
                    <div class="symbol symbol-circle symbol-40px overflow-hidden me-2 border-radius-5"> <a href="javascript:void(0);" class="">
                        <div class="symbol-label <?php echo @$bgTextDecore; ?> border-radius-5"><?php echo strtoupper(@$strVal); ?></div>
                        </a> </div>
                    <!--end::Avatar-->
                    <!--begin::Name details-->
                    <div class="d-flex flex-column"> <span class="text-dark fw-bolder fs-6"><?php echo ucwords(substr(@$teamVal->name, 0, 15));  ?></span> <span class="text-muted fw-bold d-block fs-7"><?php echo ucwords(substr(@$teamVal->designation, 0, 15))  ?></span> </div>
                    <!--end::Name details-->
                    </div>
              </td>
              <!--end::Name-->
              <!--begin::Role-->
              <td>
                    <div class="d-flex flex-column"> <span class="text-dark fw-bolder fs-6">
                    <div class="d-flex flex-column">
                        <span class="{{ ($activeJobs >=1) ? 'fw-bolder text-success' : 'text-muted fw-bold d-block fs-7' }} fs-6">{{ $activeJobs }} Active</span>
                    </div>
                    </div></div></span> </div>
              </td>
			  <td>
                 <div class="d-flex flex-column"> <span class="text-muted fw-bold d-block fs-7"><?php echo @$candidateCount;  ?> Inhouse Talents</span> </div>
              </td>
              <!--begin::Role-->
              <!--begin::Action-->
              <td><div class="d-flex flex-column"> <a id="team_select_<?php echo @$teamVal->id ?>" href="javascript:void(0);" class="cp_select btn btn-sm btn-light-primary w-130" 
								rel="<?php echo @$teamVal->id ?>" data-assign="0" data-title="<?php echo @$teamVal->name; ?>" data-email="<?php echo @$teamVal->email; ?>">Add</a> </div></td>
              <!--end::Action-->
            </tr>
            <!--end::Table row-->
            <?php 
						}
						?>
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

$(document).ready(function() {
    // Cache the table and rows for better performance
    const $table = $('#Iteam_Table_id');
    const $rows = $table.find('tbody tr');    
    // Search function
    $('#searchInput').on('keyup', function() {
        const value = $(this).val().toLowerCase();
        let hasResults = false;
        
        // Remove previous highlights
        $rows.find('td').removeClass('highlight');
        
        $rows.each(function() {
            const $row = $(this);
            let rowText = '';
            
            // Concatenate all cell text in the row
            $row.find('td').each(function() {
                rowText += $(this).text().toLowerCase() + ' ';
            });
            
            // Show/hide row based on search term
            if (rowText.indexOf(value) > -1) {
                $row.show();
                hasResults = true;
                
                // Highlight matching text if search term isn't empty
                if (value.trim() !== '') {
                    $row.find('td').each(function() {
                        const cellText = $(this).text().toLowerCase();
                        if (cellText.indexOf(value) > -1) {
                            $(this).addClass('highlight');
                        }
                    });
                }
            } else {
                $row.hide();
            }
        });
    
    });
    
    // Clear search when clicking the input (optional)
    $('#searchInput').on('click', function() {
        if ($(this).val() === '') {
            $rows.show();
            $rows.find('td').removeClass('highlight');
        }
    });

});


$( document ).ready(function() {
	
	
	
	var team 			= <?php echo json_encode($team); ?>;
	var jqueryarray 	= <?php echo json_encode($recruiter); ?>;
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
});		
		
</script>
<script>

$( document ).ready(function() {
	
	
	 $('.cp_select').on('click', function(e) {
   			
			
			var rel						= 	$(this).attr('rel');
			var assign					=	$('#team_select_'+rel).attr("data-assign");
			var title					=	$('#team_select_'+rel).attr("data-title");
			
			if(assign==0)
			{
				$(this).addClass("btn-light-danger");
				$(this).removeClass("btn-light-primary");
				$(this).text('Remove');
				$(this).attr("data-assign", 1);
				addRecruiterManager(title,rel);
			}
			else if(assign ==1)
			{
				$(this).addClass("btn-light-primary");
				$(this).removeClass("btn-light-danger");
				$(this).text('Add');
				$(this).attr("data-assign", 0);	
				DeleteEvent(title,rel);
			}
			
	});
	
});

function addRecruiterManager(name,rel)
{
		var currentValue			=	$('#recruiter').val();
    var currentValueId			=	$('#recruiter_id').val();
		if(currentValue !='')
		{
			var includeStr			=	currentValue.includes(name);
      var includeStrId			=	currentValueId.includes(rel);
			if(includeStr  === false)
			{
				var assignNewValue	=	currentValue +' , '+name;	
        var assignNewValueId	=	currentValueId +' , '+rel;	
				$('#recruiter').val(assignNewValue);	
        $('#recruiter_id').val(assignNewValueId);	

			}							
		}
		else   
		{
			$('#recruiter').val(name);
      $('#recruiter_id').val(rel);	
		}
}
	 
function DeleteEvent(contact_person_name,rel)
{	 
  
	var tem_hiring_namager		=	''; 
  var tem_hiring_namagerId		=	'';  
	var temp					=	[]; 
  var tempId					=	[]; 
	var hiring_namager			=	$('#recruiter').val(); 
  var hiring_namager_id			=	$('#recruiter_id').val(); 
	if(hiring_namager !='')
	{
    
		var hiring_namagerArr = hiring_namager.split(',');
    var hiring_namagerArrId = hiring_namager_id.split(',');
		for(var cnt=0; cnt < hiring_namagerArr.length; cnt++)
		{
			if(contact_person_name.trim() != hiring_namagerArr[cnt].trim()){
				temp.push(hiring_namagerArr[cnt]);
			
			}	
      if(rel.trim() != hiring_namagerArrId[cnt].trim()){
				tempId.push(hiring_namagerArrId[cnt]);
			
			}			
		}
		tem_hiring_namager	=	temp.toString();	
    tem_hiring_namagerId	=	tempId.toString();		 
	}	
	$('#recruiter').val(tem_hiring_namager);	
  $('#recruiter_id').val(tem_hiring_namagerId);		
}  	
</script>
