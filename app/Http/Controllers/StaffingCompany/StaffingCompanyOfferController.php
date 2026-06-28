<?php

namespace App\Http\Controllers\StaffingCompany;

use App\Models\Department;
use App\Models\Designation;
use App\Helper\Files;
use App\Helper\Reply;
use App\Http\Requests\Onboard\StoreRequest;
use App\Models\JobApplication;
use App\Notifications\JobOffer;
use App\Models\Onboard;
use App\Models\OnboardFiles;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;

use Illuminate\Support\Facades\DB;

use App\Exports\JobOnBoardExport;
use Maatwebsite\Excel\Excel as ExcelExcel;
use Maatwebsite\Excel\Facades\Excel;


class StaffingCompanyOfferController extends StaffingCompanyBaseController
{
    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = __('app.job') .' '. __('app.onBoard');
        $this->pageIcon = 'icon-user';
    }

    public function index(Request $request)
    	{
			//echo "call"; exit; 
			
			abort_if(!$this->user->cans('view_job_applications'), 403);
			return view('staffing_company.offer.index', $this->data);
		}
   
    public function data(Request $request)
    	{
        abort_if(!$this->user->cans('view_job_applications'), 403);

       
	    $jobApplications 	= JobApplication::select('job_applications.id', 'job_applications.full_name', 'jobs.title', 'job_applications.current_designation', 'job_applications.ctc', 'job_applications.ectc', 'master_status.name', 
		'clients.name as client_name', 
		'job_applications.proposed_ctc' , 'job_applications.any_offer' , 'job_applications.document', 'job_applications.sub_for_approval', 'job_applications.offer_approved', 'job_applications.offer_release', 'job_applications.offer_acceptance', 'job_applications.joined', 'job_applications.joined_date', 'job_applications.job_id'
		)
								->join('jobs', 'jobs.id', 'job_applications.job_id')
								->join('clients', 'clients.id', 'jobs.client_id')
								->join('master_status', 'master_status.id', 'job_applications.master_status_id')
								->where('job_applications.master_status_id', 6);
								
								
								
		 if (\request('search') != "")
		{
			$search					=	request('search');	
			$jobApplications		=	$jobApplications->where('job_applications.full_name', 'LIKE', "%$search%");
		}
		   						
						      		  
								
			 
        return DataTables::of($jobApplications)
           		   
			
			->editColumn('full_name', function ($row) {            
				$full_name				=	'';
				if(strlen($row->full_name)> 15){ 		
					$full_name			=	'<div class="tooltip">'.ucwords(substr($row->full_name, 0, 15)).'...<span class="tooltiptext"><span>'.ucwords($row->full_name).'<br></span></div>';				
				}else{
					$full_name			=	'<div class="tooltip">'.ucwords(substr($row->full_name, 0, 15)).'<span class="tooltiptext"><span>'.ucwords($row->full_name).'<br></span></div>';				
				}		
			    return '<a href="javascript:void(0);" class="show-detail" data-widget="control-sidebar" data-slide="true" data-row-id="' .  $row->id . '">' . $full_name . '</a>';
				//return '<a href="javascript:void(0);" class="show-detail-1" data-widget="control-sidebar" data-slide="true" data-row-id="' .  $row->id . '">' . ucwords($row->full_name) . '</a>';
            })    
			   
			
			
			->editColumn('title', function ($row) {
				$title				=	'';
				if(strlen($row->title)> 15){ 		
					$title			=	'<div class="tooltip">'.ucwords(substr($row->title, 0, 15)).'...<span class="tooltiptext"><span>'.ucwords($row->title).'<br></span></div>';				
				}else{
					$title			=	'<div class="tooltip">'.ucwords(substr($row->title, 0, 15)).'<span class="tooltiptext"><span>'.ucwords($row->title).'<br></span></div>';				
				}					
                return (!is_null($title)) ? $title : '&nbsp;';   
            }) 
			 
			
			->editColumn('client_name', function ($row) {
                return (!is_null($row->client_name)) ? $row->client_name : '&nbsp;';
            })
			
			->addColumn('hiring_manager', function ($row) {
						
				$contactHtml 			=	'';
				$job_id					=	$row->job_id;
				$client_contact_arr 	=	DB::table('client_contact_info')
												->join('client_contact_info_jobs', 'client_contact_info.id', '=', 'client_contact_info_jobs.client_contact_info_id')
												->select('client_contact_info.contact_person_name')
												->where('client_contact_info_jobs.job_id', $job_id)
												->get();
				
				$i						=	1;
				foreach($client_contact_arr as $contArr)
					{
							if($i==1){
								$contactHtml 		.=  '<div class="tooltip">'.ucfirst(substr($contArr->contact_person_name, 0, 6)).'<span class="tooltiptext">';
							}
								$contactHtml 		.= '<span> Name 	: '.$contArr->contact_person_name.'<br></span>';
							$i					= $i+1;								
				    }
				$contactHtml 		.=	'</span></div><br>';
                return $contactHtml;
            })
			
			->addColumn('client_location', function ($row) 
			{
				$contactHtml		= '';
				$jobid 				= $row->job_id;
				
				$client_loc_count 	= DB::table('client_location_jobs')
										->join('client_location', 'client_location.id', '=', 'client_location_jobs.client_location_id')
										->join('countries', 'countries.id', '=', 'client_location.country_id')
										->select('client_location.address', 'client_location.city',  'client_location.postcode', 'countries.country_name')
										//->where('jobs.status', 'active')
										->where('client_location_jobs.job_id', $jobid)
										->count();
				$client_loc_arr	 	= DB::table('client_location_jobs')
										->join('client_location', 'client_location.id', '=', 'client_location_jobs.client_location_id')
										->join('countries', 'countries.id', '=', 'client_location.country_id')
										->select('client_location.address', 'client_location.city',  'client_location.postcode', 'countries.country_name')
										//->where('jobs.status', 'active')
										->where('client_location_jobs.job_id', $jobid)
										->get();
				
				if($client_loc_count > 0)
				{
					foreach($client_loc_arr as $locArr)
					{
						
						$contactHtml 		.= '
						<div style="text-align: center;">
							<div class="tooltip">
								<i class="text-center fa fa-map-marker fa-sm text-custom" data-toggle="tooltip" data-placement="bottom">									
								</i>
								<span class="tooltiptext"> 
									<span class="font-weight-600">
										<i class="fa fa-map-marker"></i>&nbsp;Address&nbsp;:</span> 
										'.$locArr->address.'<br>
									<span class="font-weight-600">
										<i class="fa fa-codepen"></i>&nbsp;City&nbsp;:</span> 
										'.$locArr->city.'<br>
									<span class="font-weight-600">
										<i class="fa fa-map-marker"></i>&nbsp;Postcode&nbsp;:</span> 
										'.$locArr->postcode.'<br>
									<span class="font-weight-600">
										<i class="fa fa-globe"></i>&nbsp;Country&nbsp;:</span> '.$locArr->country_name.'<br>
								</span>
							</div>
						</div><br>';
					}
				}
				else
				{
					$contactHtml = 'Any';
				}						
                return $contactHtml;
            })
			
			
           /* ->editColumn('full_name', function ($row) {
                return '<a href="javascript:void(0);" class="show-detail" data-widget="control-sidebar" data-slide="true" data-row-id="' .  $row->id . '">' . ucwords($row->full_name) . '</a>';
            })         
		  	->editColumn('title', function ($row) {
                return (!is_null($row->title)) ? $row->title : '&nbsp;';
            })
			->editColumn('client_name', function ($row) {
                return (!is_null($row->client_name)) ? $row->client_name : '&nbsp;';
            })	
			
			
			
			->addColumn('hiring_manager', function ($row) {
						
				$contactHtml 			=	'';
				$job_id					=	$row->job_id;
				$client_contact_arr 	=	DB::table('client_contact_info')
												->join('client_contact_info_jobs', 'client_contact_info.id', '=', 'client_contact_info_jobs.client_contact_info_id')
												->select('client_contact_info.contact_person_name')
												->where('client_contact_info_jobs.job_id', $job_id)
												->get();
				
				$i						=	1;
				foreach($client_contact_arr as $contArr)
					{
							if($i==1){
								$contactHtml 		.=  '<div class="tooltip">'.ucfirst(substr($contArr->contact_person_name, 0, 6)).'<span class="tooltiptext">';
							}
								$contactHtml 		.= '<span> Name 	: '.$contArr->contact_person_name.'<br></span>';
							$i					= $i+1;								
				    }
				$contactHtml 		.=	'</span></div><br>';
                return $contactHtml;
            })

			->addColumn('client_location', function ($row) 
			{
				$contactHtml		= '';
				$jobid 				= $row->job_id;
				
				$client_loc_count 	= DB::table('client_location_jobs')
										->join('client_location', 'client_location.id', '=', 'client_location_jobs.client_location_id')
										->join('countries', 'countries.id', '=', 'client_location.country_id')
										->select('client_location.address', 'client_location.city',  'client_location.postcode', 'countries.country_name')
										//->where('jobs.status', 'active')
										->where('client_location_jobs.job_id', $jobid)
										->count();
				$client_loc_arr	 	= DB::table('client_location_jobs')
										->join('client_location', 'client_location.id', '=', 'client_location_jobs.client_location_id')
										->join('countries', 'countries.id', '=', 'client_location.country_id')
										->select('client_location.address', 'client_location.city',  'client_location.postcode', 'countries.country_name')
										//->where('jobs.status', 'active')
										->where('client_location_jobs.job_id', $jobid)
										->get();
				
				if($client_loc_count > 0)
				{
					foreach($client_loc_arr as $locArr)
					{
						
						$contactHtml 		.= '<div style="text-align: center;"><div class="tooltip"><i class="text-center fa fa-map-marker fa-sm text-custom" data-toggle="tooltip" data-placement="bottom"></i>
													<span class="tooltiptext"> 
															Address : '.$locArr->address.'<br>
															City 	: '.$locArr->city.'<br>
															Postcode : '.$locArr->postcode.'<br>
															Country : '.$locArr->country_name.'<br>
														</span>
												</div></div><br>';
					}
				}
				else
				{
					$contactHtml = 'Any';
				}						
                return $contactHtml;
            })*/
			
		/*	->editColumn('proposed_ctc', function ($row) {
                return (!is_null($row->proposed_ctc)) ? $row->proposed_ctc : '&nbsp;';
            })
			->editColumn('any_offer', function ($row) {
                return (!is_null($row->any_offer)) ? $row->any_offer : '&nbsp;';
            })*/
		/*	->editColumn('document', function ($row) {
                return (!is_null($row->document)) ? $row->document : '&nbsp;';
            })*/
			 
			
			
			/*->addColumn('any_offer', function ($row) {
				$this->customData	=	array();
				
				$chk			=	'';
				if($row->any_offer == 1){
					$chk		=	'checked="checked"';	
				}else{
					$chk		=	'';		
				}
				//$any_offer		=	'<input type="checkbox" name="check-1" value="1" class="lcs_check" autocomplete="off"  '.$chk.'  />';
				$any_offer			=	'

				<div class="checkbox-inline d-inline-block">
				  <label class="checkbox checkbox-success my-0 py-0">
				  <input type="checkbox" name="check-1" value="1" class="lcs_check" autocomplete="off" checked="checked">
				  <span class=""></span></label>
				</div>

				<!-- <input type="checkbox" name="check-1" value="1" class="lcs_check" autocomplete="off" checked="checked"> -->
				';
				$any_offer			=	'abu';
              	return $any_offer;
							
            })*/
			
			/*->editColumn('joined_date', function ($row) {   
				            	
				$textBox 			= '<input type="text"  name="joining_date" class="custom_calendar" value="'.$row->joined_date.'">'; 			   
			    return $textBox;
            })*/
			
			
			->editColumn('proposed_ctc', function ($row) {
 				$application_id		=	$row->id;					
				$any_offer			=	'<input style="width:100px;" type="number"  name="proposed_ctc" class="" value="'.$row->proposed_ctc.'"  onkeyup="update_offer('.$application_id.', this.value, 1);"  >';
              	return $any_offer;
            })
			
			->editColumn('any_offer', function ($row) {
				$application_id		=	$row->id;	
				
				$chk				=	'';
				if($row->any_offer == 1){
					$chk			=	'checked="checked"';	
				}else{
					$chk			=	'';		
				}
				$any_offer			=	'

				<div class="text-center">
					<div class="checkbox-inline d-inline-block">
					  <label class="checkbox checkbox-success my-0 py-0">
					  	<input type="checkbox" name="any_offer" id="any_offer'.$application_id.'" value="1" class="lcs_check"  autocomplete="off" '.@$chk.'  onchange="update_offer('.$application_id.', this.value, 2);"  >
					  	<span class="mx-auto my-auto"></span></label>
					</div>
				</div>	
				<!-- <input type="checkbox" name="any_offer" id="any_offer'.$application_id.'" value="1" class="lcs_check"  autocomplete="off" '.@$chk.'  onchange="update_offer('.$application_id.', this.value, 2);"  > -->
				';
              	return $any_offer;
            })
			
			->editColumn('document', function ($row) {
				$application_id		=	$row->id;
				
				$chk				=	'';
				if($row->document == 1){
					$chk			=	'checked="checked"';	 
				}else{
					$chk			=	'';		
				}
				$document			=	'

				<div class="text-center">
					<div class="checkbox-inline d-inline-block">
					  <label class="checkbox checkbox-success">
					  	<input type="checkbox" name="document" id="document'.$application_id.'"  value="1" class="lcs_check"  autocomplete="off" '.@$chk.'   onchange="update_offer('.$application_id.', this.value, 3);">
					  	<span class="mx-auto my-auto"></span></label>
					</div>
				</div>	

				<!-- <input type="checkbox" name="document" id="document'.$application_id.'"  value="1" class="lcs_check"  autocomplete="off" '.@$chk.'   onchange="update_offer('.$application_id.', this.value, 3);"> -->
				';
              	return $document;
            })
			
			->editColumn('sub_for_approval', function ($row) {
				$application_id		=	$row->id;
				$chk				=	'';
				if($row->sub_for_approval == 1){
					$chk			=	'checked="checked"';	
				}else{
					$chk			=	'';		
				}
				$sub_for_approval			=	'

				<div class="text-center">
					<div class="checkbox-inline d-inline-block">
					  <label class="checkbox checkbox-success my-0 py-0">
					  	<input type="checkbox" name="sub_for_approval" id="sub_for_approval'.$application_id.'" value="1" class="lcs_check"  autocomplete="off" '.@$chk.' onchange="update_offer('.$application_id.', this.value, 4);">
					  	<span class="mx-auto my-auto"></span></label>
					</div>
				</div>	

				<!-- <input type="checkbox" name="sub_for_approval" id="sub_for_approval'.$application_id.'" value="1" class="lcs_check"  autocomplete="off" '.@$chk.' onchange="update_offer('.$application_id.', this.value, 4);" > -->
				';
              	return $sub_for_approval;
            })
			
			->editColumn('offer_approved', function ($row) {
				$application_id		=	$row->id;
				$chk				=	'';
				if($row->offer_approved == 1){
					$chk			=	'checked="checked"';	
				}else{
					$chk			=	'';		
				}
				$offer_approved			=	'

				<div class="text-center">
					<div class="checkbox-inline d-inline-block">
					  <label class="checkbox checkbox-success my-0 py-0">
					  	<input type="checkbox" name="offer_approved" id="offer_approved'.$application_id.'" value="1" class="lcs_check"  autocomplete="off" '.@$chk.'  onchange="update_offer('.$application_id.', this.value, 5);">
					  	<span class="mx-auto my-auto"></span></label>
					</div>
				</div>	

				<!-- <input type="checkbox" name="offer_approved" id="offer_approved'.$application_id.'" value="1" class="lcs_check"  autocomplete="off" '.@$chk.'  onchange="update_offer('.$application_id.', this.value, 5);"> -->
				';
              	return $offer_approved;
            })
			
			->editColumn('offer_release', function ($row) {
				$application_id		=	$row->id;
				$chk				=	'';
				if($row->offer_release == 1){
					$chk			=	'checked="checked"';	
				}else{
					$chk			=	'';		
				}
				$offer_release			=	'

				<div class="text-center">
					<div class="checkbox-inline d-inline-block">
					  <label class="checkbox checkbox-success my-0 py-0">
					  	<input type="checkbox" name="offer_release" id="offer_release'.$application_id.'"  value="1" class="lcs_check"  autocomplete="off" '.@$chk.'   onchange="update_offer('.$application_id.', this.value, 6);">
					  	<span class="mx-auto my-auto"></span></label>
					</div>
				</div>

				<!-- <input type="checkbox" name="offer_release" id="offer_release'.$application_id.'"  value="1" class="lcs_check"  autocomplete="off" '.@$chk.'   onchange="update_offer('.$application_id.', this.value, 6);"> -->
				';
              	return $offer_release;
            })
			
			
			->editColumn('offer_acceptance', function ($row) {
				$application_id				=	$row->id;
				$chk						=	'';
				if($row->offer_acceptance == 1){
					$chk					=	'checked="checked"';	
				}else{
					$chk					=	'';		
				}
				$offer_acceptance			=	'

				<div class="text-center">
					<div class="checkbox-inline d-inline-block">
					  <label class="checkbox checkbox-success my-0 py-0">
					  	<input type="checkbox"  name="offer_acceptance" id="offer_acceptance'.$application_id.'" value="1" class="lcs_check"  autocomplete="off" '.@$chk.'  onchange="update_offer('.$application_id.', this.value, 7);">
					  	<span class="mx-auto my-auto"></span></label>
					</div>
				</div>

				<!-- <input type="checkbox" name="offer_acceptance" id="offer_acceptance'.$application_id.'"  value="1" class="lcs_check"  autocomplete="off" '.@$chk.'  onchange="update_offer('.$application_id.', this.value, 7);"> -->
				';
              	return $offer_acceptance;
            })
			
			
			->editColumn('joined', function ($row) {
				$application_id				=	$row->id;
				$chk						=	'';
				if($row->joined == 1){
					$chk					=	'checked="checked"';	
				}else{
					$chk					=	'';		
				}
				$joined						=	'

				<div class="text-center">
					<div class="checkbox-inline d-inline-block">
					  <label class="checkbox checkbox-success my-0 py-0">
					  	<input type="checkbox"  name="joined" id="joined'.$application_id.'" value="1" class="lcs_check"  autocomplete="off" '.@$chk.'  onchange="update_offer('.$application_id.', this.value, 8);">
					  	<span class="mx-auto my-auto"></span></label>
					</div>
				</div>

				<!-- <input type="checkbox" name="joined" id="joined'.$application_id.'"  value="1" class="lcs_check"  autocomplete="off" '.@$chk.'  onchange="update_offer('.$application_id.', this.value, 8);"> -->
				';
              	return $joined;
            })
			
			->editColumn('joined_date', function ($row) {   
				$application_id		=	$row->id;	
				
				//d-M-Y H:m:s
				if($row->joined_date	!=	'0000-00-00 00:00:00'){
					$row->joined_date 		= $row->joined_date;
					$row->joined_date 		= date("d-M-Y H:m:s", strtotime($row->joined_date));
				}								         	
				$textBox 			= '<input type="text"  name="joining_date" class="custom_calendar" value="'.$row->joined_date.'"    onblur="update_offer('.$application_id.', this.value, 9);" style="width: 150px;" >'; 			   
			    return $textBox;
            })
						
            ->rawColumns(['full_name', 'title', 'client_name',	'client_location','hiring_manager', 'proposed_ctc', 'any_offer', 'document', 'sub_for_approval', 'offer_approved', 'offer_release', 'offer_acceptance', 'joined', 'joined_date']) 
            ->addIndexColumn()
            ->make(true);
    }

 	
	public function update_application(Request $request)
	{
		$candidate_id	=	$process	=	$cvalue	=	'';
		
		$candidate_id	=	$request->candidate_id;
		if($candidate_id > 0)
		{
			 $jobApplication 								= JobApplication::find($candidate_id);	
			 
			 if($request->process == 1){												//		proposed_ctc
					$jobApplication->proposed_ctc 			= $request->cvalue;
			 }
			
			 if($request->process == 2){      											//		any_offer
					$jobApplication->any_offer 				= $request->cvalue;
			 }
			 
			 if($request->process == 3){      											//		document
					$jobApplication->document 				= $request->cvalue;
			 }
			 
			 if($request->process == 4){      											//		sub_for_approval
					$jobApplication->sub_for_approval 		= $request->cvalue;
			 }
			 
			 if($request->process == 5){      											//		offer_approved
					$jobApplication->offer_approved 		= $request->cvalue;
			 }
			 
			 if($request->process == 6){      											//		offer_release
					$jobApplication->offer_release 			= $request->cvalue;
			 }
			 
			 if($request->process == 7){      											//		offer_acceptance
					$jobApplication->offer_acceptance 		= $request->cvalue;
			 }
			 
			 if($request->process == 8){      											//		joined
					$jobApplication->joined 				= $request->cvalue;
			 }
			 			 	
			 if($request->process == 9){	
			 
			 			$monthArr							=	array('Jan'=>1, 'Feb'=>2, 'Mar'=>3, 'Apr'=>4, 'May'=>5, 'Jun'=>6, 'Jul'=>7, 'Aug'=>8, 'Sep'=>9, 'Oct'=>10, 'Nov'=>11, 'Dec'=>12);
			 			$dateTimeArr						=   explode(" ",$request->cvalue);
						$date								=	$dateTimeArr[0];
						$time								=	$dateTimeArr[1];
						
						$splitDate							= explode("-",$date);
						$customdate							=  $splitDate[2].'-'.$monthArr[$splitDate[1]].'-'.$splitDate[0].' '.$time;
						
						
						
			 			
			 			//$cvalue 								= strtotime($request->cvalue);
							
			 												//		joined_date
					$jobApplication->joined_date 			= $customdate;
			 }			
			$jobApplication->save();

		}
		
		
		//$view = view('staffing_company.job-applications.show', $this->data)->render();
        return Reply::dataOnly(['status' => 'success']);
	}
	
	
	public function loadVisibilityControl($id)
    	{
			/*$job_id	=	$id	;
			if($job_id > 0)
			{
				 $this->jobSkill = DB::table('job_skills')
						->join('skills', 'skills.id', 'job_skills.skill_id')
						->select('job_skills.company_id', 'job_skills.skill_id', 'job_skills.job_id', 'skills.name')
						->where('job_skills.job_id' , '=', $job_id)
						->get()->toArray();
			}*/
		
			return view('staffing_company.offer.load_visibility_control', $this->data)->render();
    }	
	
	
	

}
