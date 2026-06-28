<?php 

use App\Helper\GeneralMethod;
	
	
	//$jobRange 	= GeneralMethod::job_range_arr();
	//echo '<pre>'; print_r($jobRange); exit; 
	
	
	if(!empty($boostkeywordsDataList)){
	
	
		$employment_type		=	'';
		$employment_type		=	@$currentRowJob->employment_type;
		
		foreach($boostkeywordsDataList as $boost){
		
			$id						=	'';
			$id						=	@$boost->id;
			$listed_company_id		=	@$boost->id;		
				
					
			$logo_path				=	url('img/client-default.png');
			if(!empty(@$boost->logo) && strlen(@$boost->logo)>10){
				$logo_path			=  url('public/user-uploads/company-logo/'.@$boost->logo); 	
			}
			$company_name			=	substr(ucwords(@$boost->company_name), 0, 15);
			$ind_name				=	substr(ucwords(@$boost->ind_name), 0, 15);
			
			
			$boostkeywords_text		= 	$boostkeywords_text_arr		=	'';
			$boostkeywords_text		=	@$boost->boostkeywords_text;
			$boostkeywords_text_arr	=	explode(",",$boostkeywords_text); 
			
			
$customSimilarQuesyCount  	=	0;
$customSimilarQuesy  		= 	DB :: table('companies')->select('jobs.id');
$customSimilarQuesy			=	$customSimilarQuesy->leftJoin('recommendation_relation', function($join){
												$join->on('recommendation_relation.to_company_id', '=', 'companies.id');
												$join->on('recommendation_relation.final_status', '!=', DB::raw("'-1'"));
								});	
$customSimilarQuesy			=	$customSimilarQuesy->leftJoin('jobs', function($join){
												$join->on('jobs.company_id', '=', 'companies.id');
												$join->orOn('jobs.id', '=', 'recommendation_relation.job_id');
								});	
$customSimilarQuesy 		=	$customSimilarQuesy->leftJoin('job_metronics_skills', 'job_metronics_skills.job_id', '=', 'jobs.id');								

//##################################################################### ------------- TITLE MATCH 
foreach ($boostkeywords_text_arr as $value) { 
	$customSimilarQuesy 		=	$customSimilarQuesy->orWhere('jobs.title', 'like', "%{$value}%")->whereIn('jobs.employment_type', array($employment_type) )->where('companies.id', '=', $listed_company_id);
}
if(!empty($boostkeywords_text_arr)){
	foreach($boostkeywords_text_arr as $jskill){
		$customSimilarQuesy 	=	$customSimilarQuesy->orWhere('jobs.title', 'like', "%{$jskill}%")->whereIn('jobs.employment_type', array($employment_type) )->where('companies.id', '=', $listed_company_id);
		$customSimilarQuesy 	=	$customSimilarQuesy->orWhere('job_metronics_skills.skill_name', 'like', "%{$jskill}%")->whereIn('jobs.employment_type', array($employment_type) )->where('companies.id', '=', $listed_company_id);
	}
}
$customSimilarQuesy 		=	$customSimilarQuesy->where('jobs.employment_type', '=', $employment_type);
$customSimilarQuesy			=	$customSimilarQuesy->where('companies.id', '=', $listed_company_id);
$customSimilarQuesy 		=	$customSimilarQuesy->groupBy('job_metronics_skills.job_id');
$customSimilarQuesy 		=	$customSimilarQuesy->groupBy('companies.id');
$customSimilarQuesy 		=	$customSimilarQuesy->get(); 
$customSimilarQuesyCount  	=	count($customSimilarQuesy);




//$customProfileQuesy  		= 	DB :: table('job_applications')->select('job_applications.id');


$customProfileQuesyCount	=	0;
$customProfileQuesy  		= 	DB :: table('companies')->select('jobs.id');
$customProfileQuesy			=	$customProfileQuesy->leftJoin('job_applications', function($join) {
												$join->on('job_applications.company_id', '=', 'companies.id');
												$join->orOn('job_applications.thirdparty_company_id', '!=', DB::raw("'-1'"));    
								});	


$customProfileQuesy			=	$customProfileQuesy->leftJoin('recommendation_relation', function($join){
												$join->on('recommendation_relation.to_company_id', '=', 'companies.id');
												$join->on('recommendation_relation.final_status', '!=', DB::raw("'-1'"));
								});	
$customProfileQuesy			=	$customProfileQuesy->leftJoin('jobs', function($join){
												$join->on('jobs.company_id', '=', 'companies.id');
												$join->orOn('jobs.id', '=', 'recommendation_relation.job_id');
								});	
$customProfileQuesy 		=	$customProfileQuesy->leftJoin('job_metronics_skills', 'job_metronics_skills.job_id', '=', 'jobs.id');								

//##################################################################### ------------- TITLE MATCH 
foreach ($boostkeywords_text_arr as $value) { 
	$customProfileQuesy 		=	$customProfileQuesy->orWhere('jobs.title', 'like', "%{$value}%")->whereIn('jobs.employment_type', array($employment_type) )->where('companies.id', '=', $listed_company_id);
}
if(!empty($boostkeywords_text_arr)){
	foreach($boostkeywords_text_arr as $jskill){
		$customProfileQuesy 	=	$customProfileQuesy->orWhere('jobs.title', 'like', "%{$jskill}%")->whereIn('jobs.employment_type', array($employment_type) )->where('companies.id', '=', $listed_company_id);
		$customProfileQuesy 	=	$customProfileQuesy->orWhere('job_metronics_skills.skill_name', 'like', "%{$jskill}%")->whereIn('jobs.employment_type', array($employment_type) )->where('companies.id', '=', $listed_company_id);
	}
}
$customProfileQuesy 		=	$customProfileQuesy->where('jobs.employment_type', '=', $employment_type);
$customProfileQuesy			=	$customProfileQuesy->where('companies.id', '=', $listed_company_id);
$customProfileQuesy 		=	$customProfileQuesy->groupBy('job_metronics_skills.job_id');
$customProfileQuesy 		=	$customProfileQuesy->groupBy('companies.id');
$customProfileQuesy 		=	$customProfileQuesy->get(); 
$customProfileQuesyCount  	=	count($customProfileQuesy);

 

//echo '<pre>'; print_r($customProfileQuesyCount); 

//echo $listed_company_id;   
    
    
		
?>

<tr role="row" class="odd">

  	<td tabindex="0">
  		<div class="form-check form-check-sm form-check-custom form-check-solid">
      		<input class="form-check-input form-check-input-reco" type="checkbox" value="1">
    	</div>
	</td>
  
  	<td>
		<div class="symbol symbol-circle symbol-40px overflow-hidden me-2  border-radius-5" style="float:left"> 
			<a href="javascript:void(0);">
      			<div class="symbol-label border-radius-5"> 
					<img src="<?php echo @$logo_path; ?>" alt="Company Logo" class="border-radius-5 w-100"> 
				</div>
      		</a> 
		</div>
    	<div class="d-flex flex-column"> 
			<a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6"><?php echo @$company_name; ?></a> 
			<a href="javascript:void(0);" class="fw-bold text-muted d-block fs-7 text-hover-primary"><?php echo @$ind_name; ?></a> 
		</div>
	</td>
  	
	<td>
		<div class="d-flex flex-column"> 
			<span class="text-dark fw-bolder fs-6"> 
			
<svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 111.81 122.88" style="enable-background:new 0 0 111.81 122.88; height:30px; width:30px;" xml:space="preserve"><style type="text/css"><![CDATA[
	.st0{fill-rule:evenodd;clip-rule:evenodd;fill:#51B53C;}
	.st1{fill:#FFFFFF;}
	.st2{fill-rule:evenodd;clip-rule:evenodd;fill:#FFFFFF;}
]]></style><g>
<path class="st0" d="M55.71,0c20.85,13.21,39.68,19.47,55.85,17.99c2.82,57.1-18.26,90.82-55.63,104.89 C19.84,109.71-1.5,77.44,0.08,17.12C19.06,18.12,37.67,14.01,55.71,0L55.71,0L55.71,0z"/>
<path class="st2" d="M55.73,7.05c18.45,11.7,35.13,17.23,49.43,15.92c2.5,50.54-16.17,80.39-49.24,92.85 C23.98,104.16,5.09,75.6,6.49,22.21C23.29,23.09,39.77,19.46,55.73,7.05L55.73,7.05L55.73,7.05z"/>
<path class="st0" d="M56.24,19.54c14.22,9.01,27.06,13.27,38.08,12.27c1.92,38.94-12.45,61.93-37.94,71.53 c-0.16-0.06-0.32-0.12-0.48-0.18c-0.16,0.06-0.32,0.12-0.48,0.18c-25.48-9.6-39.86-32.59-37.94-71.53 c11.02,1.01,23.87-3.26,38.08-12.27l0,0l0.33,0.25L56.24,19.54L56.24,19.54L56.24,19.54z"/>
<path class="st1" d="M35.44,58.28l7.47-0.1l0.56,0.14c1.51,0.87,2.93,1.86,4.26,2.99c0.96,0.81,1.87,1.69,2.74,2.65 c2.68-4.31,5.54-8.28,8.56-11.92c3.31-3.99,5.38-6.18,9.06-9.49l0.73-0.28h8.16l-1.65,1.82c-5.05,5.61-8.21,9.99-12.35,15.97 c-4.15,6-7.85,12.18-11.15,18.54l-1.03,1.98l-0.94-2.02c-1.74-3.73-3.82-7.15-6.3-10.21c-2.48-3.06-5.37-5.78-8.74-8.09 L35.44,58.28L35.44,58.28L35.44,58.28L35.44,58.28z"/></g></svg>

			
			</span> 
		</div>
	</td>
  	
	<td>
		<div class="d-flex flex-column"> 
			<span class="text-dark fw-bolder fs-6"><?php echo @$customSimilarQuesyCount; ?></span> 
		</div>
	</td>
  	
	<td>
		<div class="d-flex flex-column"> 
			<span class="text-dark fw-bolder fs-6"><?php echo @$customProfileQuesyCount; ?></span> 
		</div>
	</td>
  	
	<td>
		<div class="d-flex flex-column"> 
			<span class="text-dark fw-bolder fs-6">
<?php 

$empType = '';
if( ($boost->newjobrequest_status == 1 && $boost->newjobrequest_balance > 0) && ($boost->newclientrequest_status == 1 && $boost->newclientrequest_balance > 0) )
{
	$empType = 'Full-time/Contract';	
}else if($boost->newjobrequest_status == 1 && $boost->newjobrequest_balance > 0){
	$empType = 'Full-time';	
}else if($boost->newclientrequest_status == 1 && $boost->newclientrequest_balance > 0){
	$empType = 'Contract';	
}						
echo $empType;
?>
			</span> 
		</div>
	</td>
					
  	<td>

<?php 
	
	$regStatus				=	'UnRegistered';
	$regStatusCss			=	'text';
	if(!empty($boost->from_company_id)){
		if(($boost->from_company_id == $company_id) &&  ($boost->to_company_id == $boost->id) &&  ($boost->final_status== 1) )
		{ 
			$regStatus		=	'Registered';	
			$regStatusCss	=	'text-success';
		}
	}
	echo '<div class="d-flex flex-column"> <span class=" '.@$regStatusCss.' fw-bolder fs-6">'.@$regStatus.'</div>';	
						
?>		
	</td>
  	
	<td>
	
<?php 

$from_company = $company_id	=	auth()->user()->company_id;
					
	
					
					
					
$html  						=	'';	
$recommendateCounter 		= 	DB::table('recommendation_relation')
								->select('recommendation_relation.id')
								->where('recommendation_relation.from_company_id', $from_company)
								->where('recommendation_relation.to_company_id', $id)
								->where('recommendation_relation.job_id', $jobId)
								//->where('recommendation_relation.final_status', '=', 1)
								->count();					
					
if($recommendateCounter == 1)
{

$recommendateRow 		= 	DB::table('recommendation_relation')
->select('recommendation_relation.id', 'recommendation_relation.from_company_id', 'recommendation_relation.from_status', 'recommendation_relation.to_status', 'recommendation_relation.final_status')
->where('recommendation_relation.from_company_id', $from_company)
->where('recommendation_relation.to_company_id', $id)
->where('recommendation_relation.job_id', $jobId)
//->where('recommendation_relation.final_status', '=', 1)
->first();	
		

if(($recommendateRow->final_status == 0) || ($recommendateRow->final_status == '') ){
	$html	=	'<div class="d-flex flex-column"> <a href="javascript:void(0);" class="btn btn-sm btn-light-success w-130">Request Sent</a> </div>';
}else if ($recommendateRow->final_status == -1){
	$html	=	'<a href="javascript:void(0);" data-row-id="'.$id.'" data-row-jobid="'.@$jobId.'" data-row-ename="ignore" class="btn btn-sm btn-light-success w-130 resend_job_recommanded_request">Assign Job</a>'; 	
}else if ($recommendateRow->final_status == 2){
	$html	=	'<a href="javascript:void(0);" data-row-id="'.$id.'" data-row-jobid="'.@$jobId.'" data-row-ename="remove" class="btn btn-sm btn-light-success w-130 resend_job_recommanded_request">Assign Job</a>'; 	
}else{
$html	=	'<div class="d-flex flex-column"> 
<a href="javascript:void(0);" data-row-tabname="Registered" data-row-jobid="'.$jobId.'" data-row-fromcompany="'.$company_id.'" data-row-tocompany="'.$boost->to_company_id.'" class="btn btn-sm btn-light-danger w-130 remove_job">
Remove
</a>
</div>';
}	

		
													

}
else
{

				if($boost->to_company_id == $id)     //###############    						   REGISTER NOT ASSIGN 
				{
$html  					=	'<div class="d-flex flex-column"> 
<a href="javascript:void(0);" data-row-jobid="'.$jobId.'" data-row-fromcompany="'.$from_company.'" data-row-tocompany="'.$id.'" class="btn btn-sm btn-secondary  w-130 send_only_job_invitation">
Assign Job
</a> 
</div>';	
				}
				else     //##########################################    							UNREGISTER   
				{
$html	=	'<div class="flex-column">
<a href="javascript:void(0);" data-row-id="'.$id.'"  data-row-jobid="'.$jobId.'" class="btn btn-sm btn-light-primary w-130 send_job_recommanded_request">
Request Hire
</a> 
</div>';
				}
				
}					
					
																
echo  $html;	
	
	
?>	
		<!--<div class="flex-column"> 
			<a href="javascript:void(0);" data-row-id="299" data-row-jobid="274" class="btn btn-sm btn-light-primary w-130 send_job_recommanded_request"> 
				Request Hire 
			</a> 
		</div>-->
	</td>
	
</tr>

<?php 
		}
	}	
?>
