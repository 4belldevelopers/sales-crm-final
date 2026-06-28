<style>
.form-check.form-check-custom .form-check-label{margin-left: 2.55rem;}
@media only screen and (min-width: 1024px) and (max-width: 1440px){
  #kt_activitiesRecruiter{width: 1025px;}  
  #kt_activitiesClient{width: 1025px;}
}
@media only screen and (min-width: 1440px) and (max-width: 2560px){
  #kt_activitiesRecruiter{width: 1200px;}  
  #kt_activitiesClient{width: 1200px;}
}
</style>
<div class="card-body position-relative px-0 py-0" id="kt_activities_body">
  <div id="kt_activities_scroll" class="position-relative scroll-y bg-light pt-2 h-100" data-kt-scroll="true" data-kt-scroll-height="auto" data-kt-scroll-wrappers="#kt_activities_body" data-kt-scroll-dependencies="#kt_activities_header, #kt_activities_footer" data-kt-scroll-offset="5px" style="">
    <div class="card card-custom mx-2">
      <div class="card-header cursor-pointer">
        <div class="card-title m-0" style="width: 80%;">

			<div class="search-container input-group input-group-lg input-group-solid">
				<div class="input-group-prepend"> <span
					class="input-group-text"> <i class="la la-search"></i>
				</span>
			  </div>
				<input class="form-control form-control-lg form-control-solid" type="text" id="searchInputContactHire" placeholder="Search for contacts person/hiring manager....">
			</div>

          {{-- <h3 class="fw-bolder m-0">Contacts Person/Hiring Manager</h3> --}}
        </div>

       @if(@$client_types->self_company !=1 )
        <a href="javascript:void(0);" class="btn btn-primary align-self-center" data-toggle="modal" data-target="#add_team_member">Add Contact Person</a> 
	   @endif

	</div>
      <div class="card-body py-7">
        <table class="table align-middle table-row-dashed fs-6 gy-5" id="Iteam_Table_hire">
          <thead>
            <tr class="fw-bolder text-muted">
              <th class="min-w-150px">Name</th>
              <th class="min-w-150px">Contact</th>
              <th class="min-w-150px">Status</th>
              <th class="min-w-150px">Action</th>
            </tr>
          </thead>
          <tbody class="text-gray-600 fw-bold" id="Iteam_Table">
<?php 
		foreach($assigned as $assignVal)
		{
					
					$bgTextDecore = $strVal = '';
					$str 			= trim(@$assignVal->contact_person_name);
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

				
  ?>
            <tr>
				<td>
					<div class="d-flex align-items-center">
						<div class="symbol symbol-circle symbol-40px overflow-hidden me-2 border-radius-5"> 
									<a href="javascript:void(0);" class="">
									<div class="symbol-label <?php echo @$bgTextDecore; ?> border-radius-5"><?php echo strtoupper(@$strVal); ?></div>
									</a>
						</div>
						<div class="d-flex flex-column"> 
							<span class="text-dark fw-bolder fs-6"><?php echo ucwords(substr(@$assignVal->contact_person_name, 0, 15));  ?></span> 
							<span class="text-muted fw-bold d-block fs-7"><?php echo @$assignVal->contact_person_designation; ?></span>
						</div>
					</div>
				</td>
				<td>
					<div class="d-flex flex-column">
						<span class="text-dark fw-bolder fs-6"><?php echo @$assignVal->contact_person_email; ?></span>
						 <div style="position: relative;display: flex;">
								    <div class="iti__flag-container" data-code="{{ @$assignVal->calling_code ?? 'in' }}" data-row="{{ @$assignVal->id }}" id="phoneDisplay_{{ @$assignVal->id }}" style="display: flex;position: relative;"> </div>
										<div style="margin-left: 6px;margin-top: 2px;">
											<a href="javascript:void(0);" class="text-muted fw-bold d-block fs-7 text-hover-primary">{{ @$assignVal->contact_person_number }}</a>
										</div>
						</div>
					</div>
				</td>
				<td>
					<div class="d-flex flex-column"> 
						<span class="text-success fw-bolder fs-6">Active</span> 
					</div>
				</td>
				<td>
					<div class="d-flex flex-column"> 
						<a id="hiring_manager_select_<?php echo @$assignVal->id ?>" href="javascript:void(0);" class="cp_select btn btn-sm btn-light-primary w-130" 
								rel="<?php echo @$assignVal->id ?>" data-assign="0" data-title="<?php echo @$assignVal->contact_person_name; ?>" data-email="<?php echo @$assignVal->contact_person_email; ?>">Add</a>
						</div>
				</td>
			</tr>
<?php }?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
<!--end::Body-->
<script>
		function phoneDisplay() {
			$('.iti__flag-container').each(function () {

				var cnt = $(this).attr('data-row');
				var calling_code = $(this).attr('data-code');

				const flagContainer = document.getElementById(`phoneDisplay_${cnt}`);

				if (!flagContainer) return;

				// Remove old content first
				flagContainer.innerHTML = '';

				const flagDiv = document.createElement("div");
				flagDiv.className = `iti__flag iti__${calling_code.toLowerCase()}`;

				const dialCode = countryDialCodes[calling_code] || "";

				const dialCodeSpan = document.createElement("span");
				// dialCodeSpan.textContent = " +" + dialCode;
				dialCodeSpan.textContent = dialCode.startsWith('+') ? dialCode : '+' + dialCode;

				flagContainer.appendChild(flagDiv);
				flagContainer.appendChild(dialCodeSpan);
			});
		}

    setTimeout(() => {
        phoneDisplay()
    }, 2500);

$(document).ready(function() {
    // Cache the table and rows for better performance
    const $table = $('#Iteam_Table_hire');
    const $rows = $table.find('tbody tr');    
    // Search function
    $('#searchInputContactHire').on('keyup', function() {
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
    $('#searchInputContactHire').on('click', function() {
        if ($(this).val() === '') {
            $rows.show();
            $rows.find('td').removeClass('highlight');
        }
    });

});


$( document ).ready(function() {
	
	var assigned 			= <?php echo json_encode($assigned); ?>;
	var jqueryarray 		= <?php echo json_encode($hiring_namagerArr); ?>;
	for (var i = 0; i < assigned.length; i++) {
			var pid		=	assigned[i].id;
			var pname	=	assigned[i].contact_person_name.trim();
			for (var j = 0; j < jqueryarray.length; j++) {
            		var cname		=jqueryarray[j].trim();
					if(pname == cname){
					
								$('#hiring_manager_select_'+pid).addClass("btn-light-danger");
								$('#hiring_manager_select_'+pid).removeClass("btn-light-primary");
								$('#hiring_manager_select_'+pid).text('Remove');
								$('#hiring_manager_select_'+pid).attr("data-assign", 1); 
					
					}
        	};
     };

});	 
</script>
<script>

$( document ).ready(function() {

	 $('.cp_select').on('click', function(e) {
   			
			var rel						= 	$(this).attr('rel');
			var js_array 				=	<?php echo json_encode($assigned); ?>;
			var assign					=	$('#hiring_manager_select_'+rel).attr("data-assign");
			var title					=	$('#hiring_manager_select_'+rel).attr("data-title");
			
			if(assign ==0){
				$(this).addClass("btn-light-danger");
				$(this).removeClass("btn-light-primary");
				$(this).text('Remove');
				$(this).attr("data-assign", 1);
				addHiringManager(title);
			}else if(assign ==1){
				$(this).addClass("btn-light-primary");
				$(this).removeClass("btn-light-danger");
				$(this).text('Add');
				$(this).attr("data-assign", 0);	
				DeleteEvent(title);
			}		
	});
	
});
	
</script>




<script>

var js_array 				= <?php echo json_encode($assigned); ?>;
	
$( document ).ready(function() {
  
   $('.chk_child_assign').on('click', function() {
   			
		var cp_id					=	$(this).val();
		if($(this).is(':checked')){
			
			var js_array 				= <?php echo json_encode($assigned); ?>;
			var select_contact_person	=	js_array[cp_id];
			
			for(var i=0; i<js_array.length; i++){
				 
				if(js_array[i].id == cp_id){
					var strVal			=	'';		var bgTextDecore	=	'';		var str				=	'';	
					
					str					=	js_array[i].contact_person_name;
					str					=	str.substring(0, 1);
					str	= strVal		=	str.toLowerCase();
					var str_bg			=	str.charCodeAt(0);
					
					if(str_bg >=97 && str_bg <=101){
						bgTextDecore		=	'fs-3 bg-light-danger text-danger';	
					}else if(str_bg >=102 && str_bg <=106){
						bgTextDecore		=	'fs-3 bg-light-warning text-warning';	
					}else if(str_bg >=107 && str_bg <=111){
						bgTextDecore		=	'fs-3 bg-light-primary text-primary';	
					}else if(str_bg >=112 && str_bg <=116){
						bgTextDecore		=	'fs-3 bg-light-success text-success';	
					}else if(str_bg >=117 && str_bg <=121){
						bgTextDecore		=	'fs-3 bg-dark text-secondary';	
					}else {
						bgTextDecore		=	'fs-3 bg-light-secondary text-dark';	
					}
					
					var html	='<tr class="" data-group-name="'+js_array[i].contact_person_designation+'" id="assigned_row_'+cp_id+'"><td class=""><div class="form-check form-check-sm form-check-custom form-check-solid"><input class="form-check-input form-check-input-team1" type="checkbox" value="1"></div></td><td class="d-flex align-items-center border-0"><div class="symbol symbol-circle symbol-40px overflow-hidden me-2  border-radius-5"><a href="javascript:void(0);"><div class="symbol symbol-circle symbol-40px overflow-hidden me-2 border-radius-5" style="float:left"> <a href="javascript:void(0);" data-row-id="146" class="show-detail"><div class="symbol-label fs-3 '+bgTextDecore+' border-radius-5">'+strVal.toUpperCase()+'</div></a> </div></a> </div><div class="d-flex flex-column"> <span class="text-dark fw-bolder fs-6">'+js_array[i].contact_person_name+'</span> <span class="fw-bold text-muted d-block fs-7">'+js_array[i].contact_person_email+'</span> </div></td><td class=""><div class="d-flex flex-column"> <span class="text-dark fw-bolder fs-6">'+js_array[i].contact_person_designation+'</span> </div></td><td class=""><div class="d-flex flex-column"> <span class="text-dark fw-bolder fs-6">187</span> </div></td><td class=""><div class="d-flex flex-column"> <span class="text-dark fw-bolder fs-6">12</span> </div></td><td class=""><div class="d-flex flex-column"> <span class="text-dark fw-bolder fs-6">45</span> </div></td><td><div class=""> <a href="javascript:void(0);" class="btn btn-sm btn-light-danger remove_assign_data" rel="'+cp_id+'" onclick="DeleteEvent('+cp_id+')">Remove</a> </div></td></tr>';
									 
					$("#assign_append_table tbody").last().append(html);	
					addHiringManager(js_array[i].contact_person_name);	
					
				}
					
			}

							
		}
		else{
				$('#assigned_row_'+cp_id).remove();
				$('#'+cp_id).prop('checked', false); // Unchecks it
		}
      		  
				
	});
		
	$('.parentname').on('click', function(){
	
			var js_array 		=	<?php echo json_encode($assigned); ?>;
			var parentname		=	"";
			parentname			=	$(this).attr('data-parent-title');
			
			if($(this).is(':checked')){
				for(var i=0; i<js_array.length; i++){		
						 		
						if(parentname.trim() == js_array[i].contact_person_designation.trim()){
									
									var cp_id			=	js_array[i].id;
									var strVal			=	'';		var bgTextDecore	=	'';		var str				=	'';	
									
									str					=	js_array[i].contact_person_name;
									str					=	str.substring(0, 1);
									str	= strVal		=	str.toLowerCase();
									var str_bg			=	str.charCodeAt(0);
									
									if(str_bg >=97 && str_bg <=101){
										bgTextDecore		=	'fs-3 bg-light-danger text-danger';	
									}else if(str_bg >=102 && str_bg <=106){
										bgTextDecore		=	'fs-3 bg-light-warning text-warning';	
									}else if(str_bg >=107 && str_bg <=111){
										bgTextDecore		=	'fs-3 bg-light-primary text-primary';	
									}else if(str_bg >=112 && str_bg <=116){
										bgTextDecore		=	'fs-3 bg-light-success text-success';	
									}else if(str_bg >=117 && str_bg <=121){
										bgTextDecore		=	'fs-3 bg-dark text-secondary';	
									}else {
										bgTextDecore		=	'fs-3 bg-light-secondary text-dark';	
									}
					
									var assign_row_id			=	"assigned_row_"+cp_id;
										
									if(document.getElementById(assign_row_id) == null) {
										var html					='<tr class="" data-group-name="'+js_array[i].contact_person_designation+'" id="assigned_row_'+cp_id+'"><td class=""><div class="form-check form-check-sm form-check-custom form-check-solid"><input class="form-check-input form-check-input-team1" type="checkbox" value="1"></div></td><td class="d-flex align-items-center border-0"><div class="symbol symbol-circle symbol-40px overflow-hidden me-2  border-radius-5"><a href="javascript:void(0);"><div class="symbol symbol-circle symbol-40px overflow-hidden me-2 border-radius-5" style="float:left"> <a href="javascript:void(0);" data-row-id="146" class="show-detail"><div class="symbol-label fs-3 '+bgTextDecore+' border-radius-5">'+strVal.toUpperCase()+'</div></a> </div></a> </div><div class="d-flex flex-column"> <span class="text-dark fw-bolder fs-6">'+js_array[i].contact_person_name+'</span> <span class="fw-bold text-muted d-block fs-7">'+js_array[i].contact_person_email+'</span> </div></td><td class=""><div class="d-flex flex-column"> <span class="text-dark fw-bolder fs-6">'+js_array[i].contact_person_designation+'</span> </div></td><td class=""><div class="d-flex flex-column"> <span class="text-dark fw-bolder fs-6">187</span> </div></td><td class=""><div class="d-flex flex-column"> <span class="text-dark fw-bolder fs-6">12</span> </div></td><td class=""><div class="d-flex flex-column"> <span class="text-dark fw-bolder fs-6">45</span> </div></td><td><div class=""><a href="javascript:void(0);" class="btn btn-sm btn-light-danger remove_assign_data" rel="'+cp_id+'" onclick="DeleteEvent('+cp_id+')">Remove</a> </div></td></tr>';
														 
										$("#assign_append_table tbody").last().append(html);
									}	
					
				addHiringManager(js_array[i].contact_person_name);		
			
			
				
				}
			}	
			}
			else
			{
				for(var i=0; i<js_array.length; i++)
				{
					if(parentname == js_array[i].contact_person_designation)
					{
						$('#assigned_row_'+js_array[i].id).remove();	
					}	
				}	
			}
			
	});
	
	
});
     
function DeleteEvent(contact_person_name){
		 
	var tem_hiring_namager	=	'';  
	var temp				=	[]; 
	var hiring_namager		=	$('#hiring_namager').val(); 
	if(hiring_namager !=''){
		var hiring_namagerArr = hiring_namager.split(',');
		for(var cnt=0; cnt<hiring_namagerArr.length; cnt++){
			if(contact_person_name.trim() != hiring_namagerArr[cnt].trim()){
				temp.push(hiring_namagerArr[cnt]);
			}
		}
		tem_hiring_namager	=	temp.toString();	 
	}
	$('#hiring_namager').val(tem_hiring_namager);				
}  

function addHiringManager(name){
		
		var currentValue		=	$('#hiring_namager').val();
		if(currentValue !=''){
			var includeStr			=	currentValue.includes(name);
			if(includeStr  === false){
				var assignNewValue	=	currentValue +' , '+name;	
				$('#hiring_namager').val(assignNewValue);	
			}							
		}else{
			$('#hiring_namager').val(name);	
		}
}	 
	
</script>

