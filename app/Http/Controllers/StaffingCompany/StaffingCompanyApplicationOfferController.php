<?php

namespace App\Http\Controllers\StaffingCompany;

use App\Models\Department;
use App\Models\Designation;
use App\Helper\Files;
use App\Helper\Reply;
use App\Http\Requests\Onboard\StoreRequest;
use App\Models\JobApplication;
use App\Models\Notifications\JobOffer;
use App\Models\Onboard;
use App\Models\OnboardFiles;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;

use Illuminate\Support\Facades\DB;

use App\Exports\ApplicationOfferExport;
use Maatwebsite\Excel\Excel as ExcelExcel;
use Maatwebsite\Excel\Facades\Excel;
use App\Helper\GeneralMethod;
use App\OfferJobApplication;
use Carbon\Carbon;

class StaffingCompanyApplicationOfferController extends StaffingCompanyBaseController
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
			return view('staffing_company.applicationoffer.index', $this->data);
	}   
    public function data(Request $request)
    {
        	abort_if(!$this->user->cans('view_offer'), 403);

       
	   		$currency 		= GeneralMethod::currency();
			$salaryMode 	= GeneralMethod::salaryMode();
		
	    $jobApplications 	= OfferJobApplication::select('offer_job_applications.id', 'offer_job_applications.application_id', 'offer_job_applications.full_name',  'offer_job_applications.current_designation', 'offer_job_applications.ctc', 'offer_job_applications.ectc',  
		 
		'offer_job_applications.proposed_ctc' , 'offer_job_applications.any_offer' , 'offer_job_applications.document', 'offer_job_applications.sub_for_approval', 'offer_job_applications.offer_approved', 'offer_job_applications.offer_release', 'offer_job_applications.offer_acceptance', 'offer_job_applications.joined', 'offer_job_applications.joined_date', 'offer_job_applications.job_id',
		
		'offer_job_applications.phone', 'offer_job_applications.email',
		
		'jobs.title',
		'clients.name as client_name',
		
			'users.name as posted_by', 'users.image as posted_by_image',
			
			'offer_job_applications.master_status_id', 'offer_job_applications.child_status_id',
			'master_status.name', 'master_status.css_style',
			
			'offer_job_applications.offer_currency', 'offer_job_applications.offer_mode', 'offer_job_applications.offer_amount'
		)
		->join('jobs', 'jobs.id', 'offer_job_applications.job_id')
		->join('clients', 'clients.id', 'jobs.client_id')
		
		
		->join('job_applications', 'job_applications.id', 'offer_job_applications.application_id')
		->join('master_status', 'master_status.id', 'job_applications.master_status_id')
		->join('users', 'users.id', 'offer_job_applications.submitted_by')
		->where('offer_job_applications.master_status_id', 5)
		->orderBy('offer_job_applications.id', 'DESC');
																
		if (\request('search') != "")
		{
			$search					=	request('search');	
			$jobApplications		=	$jobApplications->where('offer_job_applications.full_name', 'LIKE', "%$search%");
		}
		   						
						      		  			 
        return DataTables::of($jobApplications)

			->editColumn('full_name', function ($row) {            
				$full_name				=	'';
				if(strlen($row->full_name)> 15){ 		
					$full_name			=	'<div class="tooltip">'.ucwords(substr($row->full_name, 0, 15)).'...<span class="tooltiptext"><span>'.ucwords($row->full_name).'<br></span></div>';				
				}else{
					$full_name			=	'<div class="tooltip">'.ucwords(substr($row->full_name, 0, 15)).'<span class="tooltiptext"><span>'.ucwords($row->full_name).'<br></span></div>';				
				}		
			    return '<a href="javascript:void(0);" class="show-detail" data-widget="control-sidebar" data-slide="true" data-row-id="' .  $row->application_id . '">' . $full_name . '</a>';
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
			
			->addColumn('client_location', function ($row) {
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
					$contactHtml 		.= '<div style="text-align: center;height: 2px;"><div class="tooltip"><i class="text-center fa fa-map-marker fa-sm text-custom" data-toggle="tooltip" data-placement="bottom"></i>';
					foreach($client_loc_arr as $locArr)
					{
						
						$contactHtml 		.= '<span class="tooltiptext"> 
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
													</span>';
					}
					$contactHtml 		.= '</div></div>';

				}
				else
				{
					$contactHtml = 'Any';
				}						
                return $contactHtml;
            })

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
				if(!empty($row->joined_date) && ($row->joined_date	!=	'0000-00-00 00:00:00')){
					$row->joined_date 		= $row->joined_date;
					$row->joined_date 		= date("d-M-Y H:m:s", strtotime($row->joined_date));
				}								         	
				$textBox 			= '<input type="text"  name="joining_date" class="custom_calendar" value="'.$row->joined_date.'"    onblur="update_offer('.$application_id.', this.value, 9);" style="width: 150px;" >'; 			   
			    return $textBox;
            })
			
			->editColumn('chkbox', function ($row) {
				return '<div class="form-check form-check-sm form-check-custom form-check-solid">
							<input class="form-check-input" type="checkbox" value="1" />
						</div>';
			})
			
			->editColumn('Name', function ($row) {
				$bgTextDecore = $strVal = '';
				$str = trim(@$row->full_name);
				$str = $strVal = strtolower($str[0]);
				$str = ord($str);
				if ($str >= 97 && $str <= 101) {
					$bgTextDecore = 'fs-3 bg-light-danger text-danger';
				} else if ($str >= 102 && $str <= 106) {
					$bgTextDecore = 'fs-3 bg-light-warning text-warning';
				} else if ($str >= 107 && $str <= 111) {
					$bgTextDecore = 'fs-3 bg-light-primary text-primary';
				} else if ($str >= 112 && $str <= 116) {
					$bgTextDecore = 'fs-3 bg-light-success text-success';
				} else if ($str >= 117 && $str <= 121) {
					$bgTextDecore = 'fs-3 bg-dark text-secondary';
				} else {
					$bgTextDecore = 'fs-3 bg-light-secondary text-dark';
				}
				return '<div class="symbol symbol-circle symbol-40px overflow-hidden me-2 border-radius-5" style="float:left">
						<a href="javascript:void(0);"   data-row-id="' . @$row->application_id . '" class="show-application-detail">
						<div class="symbol-label ' . @$bgTextDecore . ' border-radius-5">' . strtoupper(@$strVal) . '</div>
						</a>
					</div>                  
					<div class="d-flex flex-column">
						<a href="javascript:void(0);"  data-row-id="' . @$row->application_id . '"  class="text-dark fw-bolder text-hover-primary fs-6 show-application-detail" data-toggle="tooltip" title="' . ucwords(@$row->full_name) . '">' . ucwords(substr(@$row->full_name, 0, 10)) . '</a>
						<a href="javascript:void(0);"  data-row-id="' . @$row->application_id . '"  class="text-muted fw-bold text-muted d-block fs-7 text-hover-primary show-application-detail" data-toggle="tooltip" title="' . ucwords(@$row->current_designation) . '">' . ucwords(substr(@$row->current_designation, 0, 10)) . '</a>
					</div>';
        	})
		
			->editColumn('Contacts', function ($row) {
				return '<div class="d-flex flex-column">
						<a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6" data-toggle="tooltip" title="' . @$row->phone . '">' . @$row->phone . '</a>
						<a href="javascript:void(0);" class="text-muted fw-bold text-muted d-block fs-7 text-hover-primary" data-toggle="tooltip" title="'. @$row->email . '">' . substr(@$row->email, 0, 15) . '</a>
					</div>';
        	})	
			
			->editColumn('Requisitions', function ($row) {
							return '<div class="d-flex flex-column">
							<a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">'.ucwords(substr($row->title, 0, 10)).'</a>
							<span class="text-muted fw-bold text-muted d-block fs-7">'.ucwords(substr($row->client_name, 0, 10)).'</span>
						</div>';
					})
					
					->editColumn('Source', function ($row) {
					
					$posted_by_image			=		'';
					if(!empty($row->posted_by_image)){	
						$posted_by_image		=	url('public/user-uploads/team_profile_img/'.@$row->posted_by_image); 
					}	

					return '<div class="d-flex flex-column">
							<div class="symbol-group symbol-hover">
							<div class="symbol symbol-30 symbol-circle" data-toggle="tooltip" title="'.@$row->posted_by.'">
								<img alt="Pic" src="'.@$posted_by_image.'">
							</div>
						</div>
						<span class="text-muted fw-bold d-block fs-7" data-toggle="tooltip" title="" data-bs-original-title="'.@$row->posted_by.'">'.substr(@$row->posted_by, 0, 10).'</span>
						';
			})
			
			->editColumn('Requisitions', function ($row) {
                    return '<div class="d-flex flex-column">
                    <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">'.ucwords(substr($row->title, 0, 10)).'</a>
                    <span class="text-muted fw-bold text-muted d-block fs-7">'.ucwords(substr($row->client_name, 0, 10)).'</span>
                  </div>';
            })
			
			// ->editColumn('OfferAmount', function ($row) {
			// 		return 'OfferAmount';
            // })

			->editColumn('OfferAmount', function ($row) use($currency, $salaryMode) {
			
					$sel				=	'';
					$option_currency	=	'';
					if(!empty($currency)){
						foreach($currency as $key=>$cval){
							$sel	='';
							if( $row->offer_currency == $key){
									$sel	=	'selected="selected"';
							}
							$option_currency	.=	'<option value="'.$key.'" '.$sel.'>'.$cval.'</option>';	
						}
					}
					
					$option_salarymode	=	'';
					if(!empty($salaryMode)){
						foreach($salaryMode as $key=>$sval){
							$sel	='';
							if( $row->offer_mode == $key){
									$sel	=	'selected="selected"';
							}
							$option_salarymode	.=	'<option value="'.$key.'" '.$sel.'>'.$sval.'</option>';	 
						}
					}
					
					$raw_html	=	'<div class="" style="">
						<select class="form-select form-select-solid option_currency" arial-label="Select Currency" id="option_currency_'.$row->id.'" data-id="'.$row->id.'" name="option_currency[]" style="width: 82px;display: initial;">
							<option value="">Select</option>
							'.@$option_currency.'
						</select>
						<input type="number" class="form-control form-control-solid offer_amount" minlength="3" maxlength="7" name="offer_amount[]" id="offer_amount_'.$row->id.'" data-id="'.$row->id.'" placeholder="Amount" value="'.$row->offer_amount.'" style="width: 100px;display: initial;">
						<select class="form-select form-select-solid option_salarymode" arial-label="Select Annual Salary" name="option_salarymode[]" id="option_salarymode'.$row->id.'" data-id="'.$row->id.'" style="width: 100px;display: initial;">
							<option value="">Select</option>
							'.@$option_salarymode.'
						</select>
					</div>';
					return @$raw_html;
            	})
			
			// ->editColumn('JoiningDate', function ($row) {
            //         return 'JoiningDate';
            // })	

			->editColumn('JoiningDate', function ($row) {
				$joined_date	=	'';
				$joined_date	=	@$row->joined_date;
				$joined_date 	= date("d-M-Y H:i:s", strtotime($joined_date));
			$raw_html	=	'<div class="d-flex align-items-center justify-content-stretch">
								<input type="text" id="joining_date_'.$row->id.'" value="'.@$joined_date.'" data-id="'.$row->id.'" name="joining_date[]"  class="me-2 form-control form-control-solid cursor-pointer custom_calendar joining_date" placeholder="Joining date" style="width:185px;"/>
								<i class="fa fa-calendar-alt" style="font-size: 16px;" id="joining_date_'.$row->id.'"></i>
							</div>';
			return @$raw_html;
			})
			
			->editColumn('Stage', function ($row) {
		
				$name = '--------';
				if($row->master_status_id == 8){
					$name = '';
				}
				if (!empty(@$row->child_status_id) && @$row->child_status_id > 0) {
					$childStatus = '';
					$childStatus = DB::table('candidate_status')->select('*')->where('id', $row->child_status_id)->first();
					if (!empty($childStatus)) {
						$name = @$childStatus->name;
					}
				}
				return '<div class="d-flex flex-column">
							<a href="javascript.void(0);" data-row-id="'.@$row->application_id.'" class="d-flex flex-column stage_sub_stage stageupdate" data-toggle="modal" data-target="#stage_sub_stage" onClick="updateApplicantStage('.@$row->application_id.')">
								<span class="badge fw-bolder fs-6 ' . @$row->css_style . ' px-2 py-1" data-toggle="tooltip" title="' . $row->name . '">' . ucwords(substr($row->name, 0, 8)) . '</span>
								<span class="text-muted fw-bold text-muted d-block fs-7" data-toggle="tooltip" title="' . @$name . '">' . substr(@$name, 0, 8) . '</span>
							</a>
						</div>';
			})
						
            ->rawColumns(['chkbox', 'Name', 'Contacts', 'Requisitions', 'Source', 'OfferAmount', 'JoiningDate',  'Stage']) 
            ->addIndexColumn()
            ->make(true);
    } 	
	public function update_application(Request $request)
	{
		abort_if(!$this->user->cans('edit_offer'), 403);
		
		$candidate_id	=	$process	=	$cvalue	=	'';
		
		$candidate_id	=	$request->candidate_id;
		if($candidate_id > 0)
		{
			 $jobApplication 								= OfferJobApplication::find($candidate_id);	
			 
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
		
			return view('staffing_company.applicationoffer.load_visibility_control', $this->data)->render();
    }	
	public function export($start, $length)
    {
         	$filters = [
							'start' 			=>	$start,
							'length' 			=>	$length,
							'loginUserId'		=>	$this->user->id,
							'loginUserRole'		=>	$this->user->role->role_id,
							'company_id'		=>	auth()->user()->company_id
						];


       	 $data = [
					'company' => $this->user->name 
				 ];


        return Excel::download(new ApplicationOfferExport($filters, $data), 'ApplicationOffer.xlsx', ExcelExcel::XLSX);
    }	
	public function updateSalary(Request $request)
	{
			abort_if(!$this->user->cans('edit_offer'), 403);
			 	
			$block						=	'';
			$company_id      			=	auth()->user()->company_id;				
			
			
			$option_case 				=	@$request->option_case;
			$option_currency_id 		=	@$request->option_currency_id;
			$option_currency_val 		=	@$request->option_currency_val;
			
			if($option_case == 'OFFER_CURRENCY')
			{
					DB::table('offer_job_applications')
							->where('company_id', $company_id)
							->where('id', $option_currency_id)
							->limit(1) 
							->update(array('offer_currency' => $option_currency_val));			
			}
			if($option_case == 'OFFER_SALARY_MODE')
			{
					DB::table('offer_job_applications')
							->where('company_id', $company_id)
							->where('id', $option_currency_id)
							->limit(1) 
							->update(array('offer_mode' => $option_currency_val));			
			}
			if($option_case == 'OFFER_AMOUNT')
			{
					DB::table('offer_job_applications')
							->where('company_id', $company_id)
							->where('id', $option_currency_id)
							->limit(1) 
							->update(array('offer_amount' => $option_currency_val));			
			}
			if($option_case == 'OFFER_JOINING_DATE')
			{
					$joined_date	=	'';
					//$joined_date	=	@$row->joined_date;
					$joined_date 	= date("y-m-d H:i:s", strtotime($option_currency_val));
	
					DB::table('offer_job_applications')
							->where('company_id', $company_id)
							->where('id', $option_currency_id)
							->limit(1) 
							->update(array('joined_date' => $joined_date));			
			}	
				
			$message			=	'<div class="alert alert-success" id="success-alert"><button type="button" class="close bg-transparent border-0 fw-bold" data-dismiss="alert">x</button><strong>Success! </strong> New Process have added successfully.</div>';
		return Reply::dataOnly(['status' => 'success', 'message'=> $message]);
	
	
	}

}
