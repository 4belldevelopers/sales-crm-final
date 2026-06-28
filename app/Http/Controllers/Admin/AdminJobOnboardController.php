<?php

namespace App\Http\Controllers\Admin;

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
use App\Helper\GeneralMethod;

class AdminJobOnboardController extends AdminBaseController
{
    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = __('app.job') .' '. __('app.onBoard');
        $this->pageIcon = 'icon-user';
    }

    public function index(Request $request)
    	{
			abort_if(!$this->user->cans('view_job_applications'), 403);
			return view('admin.job-onboard.index', $this->data);
		}
   
    public function data(Request $request)
    	{
		
		
        	abort_if(!$this->user->cans('view_joining'), 403);

			$currency 		= GeneralMethod::currency();
			$salaryMode 	= GeneralMethod::salaryMode();
			
        $jobApplications 	= JobApplication::select('job_applications.id', 'job_applications.full_name', 'jobs.title', 'job_applications.current_designation', 'job_applications.ctc', 'job_applications.ectc',  'job_applications.phone', 'job_applications.email',		
			'jobs.title',
			'clients.name as client_name',
			'users.name as posted_by', 'users.image as posted_by_image',			
			'job_applications.master_status_id', 'job_applications.child_status_id',
			'master_status.name', 'master_status.css_style',
			
			'offer_job_applications.joined_date',
			'offer_job_applications.offer_currency', 'offer_job_applications.offer_mode', 'offer_job_applications.offer_amount' 
					
		)
				->join('offer_job_applications', 'offer_job_applications.application_id', 'job_applications.id')				
				->join('jobs', 'jobs.id', 'job_applications.job_id')
				->join('clients', 'clients.id', 'jobs.client_id')
				->join('master_status', 'master_status.id', 'job_applications.master_status_id')
				->join('users', 'users.id', 'job_applications.submitted_by')
				->where('job_applications.master_status_id', 6);
		
		if (\request('search') != "")
		{
			$search					=	request('search');	
			$jobApplications		=	$jobApplications->where('job_applications.full_name', 'LIKE', "%$search%");
		}
			
        return DataTables::of($jobApplications)
           
		    ->addColumn('action', function ($row) {
	                $action = '';
					
					//if ($this->user->cans('edit_job_applications')) {
						$action .= '<a href="' . route('admin.job-applications.edit', [$row->id]) . '" class="btn btn-primary btn-circle"
						  data-toggle="tooltip" data-original-title="' . __('app.edit') . '"><i class="fa fa-pencil" aria-hidden="true"></i></a>';
					//}

					/*if ($this->user->cans('delete_job_applications')) {
						$action .= ' <a href="javascript:;" class="btn sa-params"
						  data-toggle="tooltip" data-row-id="' . $row->id . '" data-original-title="' . __('app.delete') . '"><i class="fa fa-times text-danger" aria-hidden="true"></i></a>';
					}*/			
                return $action;
           })		
            ->editColumn('full_name', function ($row) {
                return '<a href="javascript:void(0);" class="show-detail" data-widget="control-sidebar" data-slide="true" data-row-id="' .  $row->id . '">' . ucwords($row->full_name) . '</a>';
            })         
		  	->editColumn('title', function ($row) {
                return (!is_null($row->title)) ? $row->title : '&nbsp;';
            })			
			->editColumn('current_designation', function ($row) {
                return (!is_null($row->current_designation)) ? $row->current_designation : '&nbsp;';
            })			
			->editColumn('ctc', function ($row) {
                return (!is_null($row->ctc)) ? $row->ctc : '&nbsp;';
            })			
			->editColumn('ectc', function ($row) {
                return (!is_null($row->ectc)) ? $row->ectc : '&nbsp;';
            })			
			->editColumn('name', function ($row) {
                return (!is_null($row->name)) ? $row->name : '&nbsp;';
            })
					
					
			
			->editColumn('chkbox', function ($row) {
                return '<div class="form-check form-check-sm form-check-custom form-check-solid">
                    <input class="form-check-input" type="checkbox" value="1">
                  </div>';
            })
			
			->editColumn('Name', function ($row) {
			
			
					$bgTextDecore			=	$strVal	=	'';
					$str					=	trim(@$row->full_name);
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
                return '<div class="symbol symbol-circle symbol-40px overflow-hidden me-2 border-radius-5" style="float:left">
                    <a href="javascript:void(0);" data-row-id="'.$row->id.'" class="show-application-detail">
                      <div class="symbol-label fs-3 '.$bgTextDecore.' text-warning border-radius-5">'.ucwords($strVal).'</div>
                    </a>
                  </div>
                  <div class="d-flex flex-column">
                    <a href="javascript:void(0);" data-row-id="'.$row->id.'" class="text-dark fw-bolder text-hover-primary fs-6 show-application-detail" data-toggle="tooltip" title="'.ucwords($row->full_name).'">'.ucwords(substr(@$row->full_name, 0, 15)).'</a> 
                    <a href="javascript:void(0);" data-row-id="'.$row->id.'"  class="text-muted fw-bold text-muted d-block fs-7 text-hover-primary show-application-detail" data-toggle="tooltip" title="'.ucwords($row->current_designation).'">'.ucwords(substr(@$row->current_designation, 0, 15)).'</a>
                  </div>';
            })
			
			->editColumn('Contact', function ($row) { 
                return '<div class="d-flex flex-column">
                    <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6" data-toggle="tooltip" title="'.$row->phone.'">'.@$row->phone.'</a>   
                    <a href="javascript:void(0);" class="text-muted fw-bold text-muted d-block fs-7 text-hover-primary" data-toggle="tooltip" title="'.$row->email.'">'.@$row->email.'</a>
                  </div>';
            })
						
			->editColumn('Requisitions', function ($row) {
                    return '<div class="d-flex flex-column">
                    <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6" data-toggle="tooltip" title="'.ucwords($row->title).'">'.ucwords(substr($row->title, 0, 10)).'</a>
                    <span class="text-muted fw-bold text-muted d-block fs-7" data-toggle="tooltip" title="'.ucwords($row->client_name).'">'.ucwords(substr($row->client_name, 0, 10)).'</span>
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
                        <img alt="image" src="'.@$posted_by_image.'">
                      </div>
                  </div>
				  <span class="text-muted fw-bold d-block fs-7" data-toggle="tooltip" title="" data-bs-original-title="'.@$row->posted_by.'">'.substr(@$row->posted_by, 0, 10).'</span>
				  ';
        })
		
			
			
			 ->editColumn('Offer', function ($row)  use($currency, $salaryMode) {
			 
			 		$offer_currency	=	'';
					if(!empty($row->offer_currency)){
						$offer_currency	=	$currency[$row->offer_currency];
					}
					
					$offer_mode	=	'';
					if(!empty($row->offer_mode)){
						$offer_mode	=	$salaryMode[$row->offer_mode];
					}
					
                 return '<div class="d-flex flex-column">
                         <span class="text-dark fw-bolder fs-6">'.$offer_currency.' '.$row->offer_amount.' '.$offer_mode.'</span>
                     </div>';
             })
			->editColumn('Joining', function ($row) {
			
               		$joined_date	=	$joined_time	=	$date	=	'';
					$date			=	@$row->joined_date;
					if(!empty($date)){
						$joined_date 	= date("d-M-y", strtotime($date));
						$joined_time 	= date("H:i a", strtotime($date));
					}
                 return '<div class="d-flex flex-column">
                         <span class="text-dark fw-bolder fs-6">'.$joined_date.'</span>
                         <span class="text-muted fw-bold d-block fs-7">'.$joined_time.'</span>
                     </div>';
            })
						
			/*->editColumn('Stage', function ($row) {
                return '<div class="d-flex flex-column">
                        <span class="fw-bolder joined-bg fs-6 px-2">Joined</span>
                    </div>';
            })*/
			
			
			->editColumn('Stage', function ($row) {		
				$name 		=	'--------';
				if($row->master_status_id == 8){
					$name 	=	'';
				}
				if (!empty(@$row->child_status_id) && @$row->child_status_id > 0) {
					$childStatus = '';
					$childStatus = DB::table('candidate_status')->select('*')->where('id', $row->child_status_id)->first();
					if (!empty($childStatus)) {
						$name = @$childStatus->name;
					}
				}
				return '<div class="d-flex flex-column">
							<a href="javascript:void(0);" data-row-id="'.@$row->id.'" class="d-flex flex-column stage_sub_stage stageupdate" data-toggle="modal" data-target="#stage_sub_stage" onClick="updateApplicantStage('.@$row->id.')">
								<span class="badge fw-bolder fs-6 ' . @$row->css_style . ' px-2 py-1">' . ucwords(substr($row->name, 0, 10)) . '</span>
								<span class="text-muted fw-bold text-muted d-block fs-7">' . @$name . '</span>
							</a>
						</div>';
			})
			
						
			->rawColumns(['chkbox', 'Name', 'Contact', 'Requisitions', 'Source',  'Offer', 'Joining', 'Stage'])			
            //->rawColumns(['full_name', 'title', 'current_designation', 'ctc', 'ectc', 'name', 'action'])
            ->addIndexColumn()
            ->make(true);
    }


 	public function create(Request $request)
    	{
        abort_if(!$this->user->cans('add_job_applications'), 403);

        $this->application = JobApplication::with(['job','status'])->findOrFail($request->id);

        abort_if($this->application->status->status != 'hired', 403);

        //$this->users        = User::all();
		$this->users        = User::where('company_id', '=', $this->application->company_id)->where('type', '!=', 1)->get();
        $this->departments  = Department::all();
        $this->designations = Designation::all();

        return view('admin.job-onboard.create', $this->data);
    }

    public function store(StoreRequest $request)
    	{
        // Save On Board Details
        $onBoard = new Onboard();
        $onBoard->job_application_id = $request->candidate_id;
        $onBoard->department_id      = $request->department;
        $onBoard->designation_id     = $request->designation;
        $onBoard->salary_offered     = $request->salary;
        $onBoard->joining_date       = $request->join_date;
        $onBoard->reports_to_id      = $request->report_to;
        $onBoard->employee_status    = $request->employee_status;
        $onBoard->accept_last_date   = $request->last_date;
        $onBoard->offer_code         = Str::random(18);
        $onBoard->save();

        $names = $request->name;
        $files = $request->file;

        foreach ($names as $key => $name) :
            // Files store in directory
            $fileName = Files::upload($files[$key], 'onboard-files', null, null, false);

            // Files save
            OnboardFiles::create(
                [
                    'on_board_detail_id' => $onBoard->id,
                    'name'               => $name,
                    'file'               => $files[$key]->getClientOriginalName(),
                    'hash_name'          => $fileName
                ]
            );
        endforeach;

        // Send Offer email
        if($request->has('send_email')){
            $this->sendOffer($request->candidate_id);
        }

        return Reply::redirect(route('admin.job-onboard.index'), __('app.onBoard') . ' ' . __('messages.createdSuccessfully'));
    }
	 
	public function edit($id)
    	{
        abort_if(!$this->user->cans('add_job_applications'), 403);

        // images format with icon
        $this->imageExt = [
            'png' => 'fa-file-image-o',  'jpe' => 'fa-file-image-o', 'jpeg' => 'fa-file-image-o', 'jpg' => 'fa-file-image-o',
            'gif' => 'fa-file-image-o',  'bmp' => 'fa-file-image-o', 'ico' => 'fa-file-image-o', 'tiff' => 'fa-file-image-o',
            'tif' => 'fa-file-image-o', 'svg' => 'fa-file-image-o', 'svgz' => 'fa-file-image-o',
        ];

        // adobe and ms office files format with icon
        $this->fileExt = [
            // adobe
            'pdf' => 'fa-file-pdf-o', 'psd' => 'fa-file-image-o', 'ai' => 'fa-file-o', 'eps' => 'fa-file-o',
            'ps' => 'fa-file-o',
            // ms office
            'doc' => 'fa-file-text', 'rtf' => 'fa-file-text', 'xls' => 'fa-file-excel-o', 'ppt' => 'fa-file-powerpoint-o',
            'docx' => 'fa-file-text', 'xlsx' => 'fa-file-excel-o', 'pptx' => 'fa-file-powerpoint-o',
            // open office
            'odt' => 'fa-file-text', 'ods' => 'fa-file-text',
        ];

        $this->onboard      = Onboard::with(['reportto', 'files'])->findOrFail($id);
        $this->application  = JobApplication::with(['job','status'])->findOrFail($this->onboard->job_application_id);
        $this->users        = User::all();
        $this->departments  = Department::all();
        $this->designations = Designation::all();

        return view('admin.job-onboard.edit', $this->data);
    }
       
    public function update(StoreRequest $request, $id)
    	{
        // update On Board Details
        $onBoard = Onboard::findOrFail($id);
        $onBoard->job_application_id = $request->candidate_id;
        $onBoard->department_id      = $request->department;
        $onBoard->designation_id     = $request->designation;
        $onBoard->salary_offered     = $request->salary;
        $onBoard->joining_date       = $request->join_date;
        $onBoard->reports_to_id      = $request->report_to;
        $onBoard->employee_status    = $request->employee_status;
        $onBoard->accept_last_date   = $request->last_date;
        $onBoard->hired_status       = $request->status;

        if($onBoard->hired_status == 'rejected' || $onBoard->hired_status == 'canceled'){
            $onBoard->hired_status       = $request->reason;
        }
        $onBoard->save();

        $names 		= $request->name;
        $files 		= $request->file;
		
		
		if(!empty($names))
		{
			foreach ($names as $key => $name) :
				if(!empty($files[$key]))
				{
					$fileName = Files::upload($files[$key], 'onboard-files', null, null, false);
					OnboardFiles::create(
											[
												'on_board_detail_id' => $onBoard->id,
												'name'               => $name,
												'file'               => $files[$key]->getClientOriginalName(),
												'hash_name'          => $fileName
											]
										);
			 } 							
									
			endforeach;
		} 
        return Reply::redirect(route('admin.job-onboard.index'), __('app.onBoard') . ' ' . __('messages.updatedSuccessfully'));

    }

    public function destroy($id)
    	{
        $onBoardFiles = OnboardFiles::findOrFail($id);
        File::delete('user-uploads/onboard-files/'.$onBoardFiles->hashName);

        OnboardFiles::destroy($id);
        return Reply::success('messages.recordDeleted');
    }

    public function show($id)
    	{
        // images format with icon
        $this->imageExt = [
            'png' => 'fa-file-image-o',  'jpe' => 'fa-file-image-o', 'jpeg' => 'fa-file-image-o', 'jpg' => 'fa-file-image-o',
            'gif' => 'fa-file-image-o',  'bmp' => 'fa-file-image-o', 'ico' => 'fa-file-image-o', 'tiff' => 'fa-file-image-o',
            'tif' => 'fa-file-image-o', 'svg' => 'fa-file-image-o', 'svgz' => 'fa-file-image-o',
        ];

        // adobe and ms office files format with icon
        $this->fileExt = [
            // adobe
            'pdf' => 'fa-file-pdf-o', 'psd' => 'fa-file-image-o', 'ai' => 'fa-file-o', 'eps' => 'fa-file-o',
            'ps' => 'fa-file-o',
            // ms office
            'doc' => 'fa-file-text', 'rtf' => 'fa-file-text', 'xls' => 'fa-file-excel-o', 'ppt' => 'fa-file-powerpoint-o',
            'docx' => 'fa-file-text', 'xlsx' => 'fa-file-excel-o', 'pptx' => 'fa-file-powerpoint-o',
            // open office
            'odt' => 'fa-file-text', 'ods' => 'fa-file-text',
        ];
        $this->onboard      = Onboard::with(['reportto', 'files'])->findOrFail($id);
        $this->application  = JobApplication::with(['job','status'])->findOrFail($this->onboard->job_application_id);
        return view('admin.job-onboard.show', $this->data);
    }


    public function sendOffer($userID)
		{

        $jobApplication = JobApplication::with(['onboard', 'job'])->findOrFail($userID);
        // Send Email Or Sms to applicant.
        Notification::send($jobApplication, new JobOffer($jobApplication));
    }

    public function updateStatus($id)
		{

        $onboard = Onboard::findOrFail($id);
        $onboard->hired_status = 'canceled';
        $onboard->save();

        return Reply::success(__('messages.offer.updatedSuccessfully'));
    }
	
	public function loadVisibilityControl($id)
    	{
		return view('admin.job-onboard.load_visibility_control', $this->data)->render();
    }
		
	public function export($start, $length)
    	{
        $filters = [
						'start' 			=> $start,
						'length' 			=> $length,
						'loginUserId'		=> $this->user->id,
						'loginUserRole'		=> $this->user->role->role_id
					];

        $data = [
					'company' => $this->companyName
				];

       	return Excel::download(new JobOnBoardExport($filters, $data), 'jobOnBoard.xlsx', ExcelExcel::XLSX);
    }
		
		
	

}
