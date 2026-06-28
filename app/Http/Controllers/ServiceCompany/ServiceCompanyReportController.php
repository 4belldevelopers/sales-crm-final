<?php
/*** ########################################################
Controller    	: 	AdminReportController
Purmose			: 
Author			:	Abu
Company			:	4Bell
Date			:	05-April-2021	
########################################################** */

/*namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\JobApplication;
use App\Job;

use App\User;
use App\Client;
use Illuminate\Support\Facades\DB;*/



namespace App\Http\Controllers\ServiceCompany;

use App\Models\JobApplication;
use App\Models\Job;
use App\Models\User;
use App\Models\Client;

use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;


class ServiceCompanyReportController extends ServiceCompanyBaseController
{
    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = __('menu.reports');
        $this->pageIcon = 'icon-film';
    }
    
    public function index()
    {
			
			abort_if(!$this->user->can('view_report'), 403);
			
         $this->jobApplication 			=	JobApplication::count();
         $this->job 					=	Job::count();
         $this->candidatesHired 		=	JobApplication::where('status_id', 4)->count();
         $this->interviewScheduled 		=	JobApplication::where('status_id', 3)->count();
         $data 							=	[];
         $data['JobApplication'] 		=	$this->jobApplication;
         $data['JobPosted'] 			=	$this->job;
         $data['CandidatesHired'] 		=	$this->candidatesHired;
         $data['InterviewScheduled'] 	=	$this->interviewScheduled;
         $data1['chart_data'] 			=	json_encode($data);
         
		 
		 
		 
		 
		 
		 
		 $chkAdminRole = $this->user->hasRole('admin');
		 
		if($chkAdminRole > 0)
		{
			 $this->jobs 			= Job::where('jobs.company_id', $this->user->company_id)->get();	
		}
		else
		{
			$this->jobs 			= DB::table('jobs')
											->join('job_assign', 'job_assign.job_id', '=', 'jobs.id')
											->select('jobs.id', 'jobs.title')
											->where('jobs.status', 'active')
											->where('jobs.company_id', $this->user->company_id)
											->where('job_assign.user_id', $this->user->id)
											->get();
						
		}				
		$this->clients 				= Client::all();
		$this->users 				= User::select('users.*')->where('status', '=', 'active')->where('company_id', '=', $this->user->company_id)->get();
		 
		 $this->candidateStatus 	= DB::table('master_status')
									->select('*')
									->orderBy('master_status.priority', 'ASC')
									->get(); 
		 
		 
		    
        return view('service_company.report.index' ,$this->data,$data1);
    }
	public function data() {
        
		abort_if(!$this->user->can('view_report'), 403);
		
       	$client 	= Client::all();




        return DataTables::of($client)
		
            ->addColumn('action', function ($row) {
                	
					$action = '';
				
				if( $this->user->can('edit_client')){
						$action.= '<a href="' . route('service_company.clients.edit', [$row->id]) . '" class="btn"
						data-toggle="tooltip" data-placement="right" data-original-title="'.__('app.edit').'"><i class="flaticon2-pen text-primary" aria-hidden="true"></i></a>';
					}
				if($row->self_company != 1)
				{
				
					
					if( $this->user->can('delete_client')){
						$action.= ' <a href="javascript:;" class="btn sa-params"
						data-toggle="tooltip" data-row-id="' . $row->id . '" data-original-title="'.__('app.delete').'"><i class="fa fa-times text-danger" aria-hidden="true"></i></a>';
					}	
				}	  
            	return $action;
					
            })
            
			
			->editColumn('name', function ($row) {
                return ucfirst($row->name);
            })
			 
			

			->addColumn('contact_person_name', function ($row) {
                
				$contactHtml	= '';
				$contactInfo 	= DB::table('client_contact_info')->select('*')->where('client_id', $row->id)->get();
				
				
				$contactHtml	='<div class="text-center tooltip"><i class="fa fa-users fa-sm text-custom" aria-hidden="true"></i><span class="tooltiptext">';
				foreach($contactInfo as $info)
				{
					$action = '';
					if( $this->user->can('edit_client')){
						$action.= '<a href="' . route('service_company.clients.edit_information', [$info->id]) . '" class="btn"
						data-toggle="tooltip" data-original-title="'.__('app.edit').'"><i class="flaticon2-pen text-white" aria-hidden="true"></i></a>';
					}
					
					if( $this->user->can('delete_client')){
						$action.= ' <a href="javascript:;" class="btn client-user-delete"
						data-toggle="tooltip" data-row-id="' . $info->id . '" data-original-title="'.__('app.delete').'"><i class="fa fa-times text-danger" aria-hidden="true"></i></a>';
					}	
						
					
					$contactHtml 			.= '
													
														Name : '.$info->contact_person_name.'<br>
														Email : '.$info->contact_person_email.'<br>
														Contact Number : '.$info->contact_person_number.'<br>
														Designation : '.@$info->contact_person_designation.'<br>
														'.$action.'<br>
													';
													
			
				}
				$contactHtml	.=	'</span></div><br>';
				return $contactHtml;
				
           	 	})
			
			
			 ->editColumn('location', function ($row) 
			 {
             			
					$locationHtml	= '';
					$locInfo 		= DB::table('client_location')
										->join('countries', 'countries.id', '=', 'client_location.country_id')
										->select('client_location.id', 'client_location.city', 'client_location.address', 'client_location.postcode', 'countries.country_name')
										->where('client_id', $row->id)
										->get();
										
					
					$locationHtml 			.= '<div class="tooltip"><i class="fa fa-map-marker fa-sm text-custom" aria-hidden="true"></i><span class="tooltiptext"> ';
					foreach($locInfo as $info)
					{
						
						 $action = '';

						if( $this->user->can('edit_client')){
							$action.= '<a href="' . route('service_company.clients.edit_location', [$info->id]) . '" class="btn"
							data-toggle="tooltip" data-original-title="'.__('app.edit').'"><i class="flaticon2-pen text-white" aria-hidden="true"></i></a>';
						}
						
						if( $this->user->can('delete_client')){
							$action.= ' <a href="javascript:;" class="btn client-location-delete"
							data-toggle="tooltip" data-row-id="' . $info->id . '" data-original-title="'.__('app.delete').'"><i class="fa fa-times text-danger" aria-hidden="true"></i></a>';
						}	  
				
						$locationHtml 			.= '
														
															Address 	: '.@$info->address .'<br>
															City 		: '.@$info->city .'<br>
															Postcode 	: '.@$info->postcode.'<br>
															Country 	: '.@$info->country_name.'<br>															
															'.$action.'<br>
														
													';
					
					}
					$locationHtml 			.= '</span></div><br>';
					return $locationHtml;
				   
            })
			
			 ->editColumn('open_jobs', function ($row) {
				$client_id  	= '';
				$client_id  	= $row->id;
				$jobCounter		= DB::table('jobs')->select('*')->where('client_id', $client_id)->where('status', '=','active')->count();
				return $jobCounter;
            })
			
			 ->editColumn('closed_jobs', function ($row) {
                $client_id  	= '';
				$client_id  	= $row->id;
				$jobCounter		= DB::table('jobs')->select('*')->where('client_id', $client_id)->where('status', '=','inactive')->count();
				return $jobCounter;
            })
			 ->editColumn('hold_jobs', function ($row) {
                $client_id  	= '';
				$client_id  	= $row->id;
				$jobCounter		= DB::table('jobs')->select('*')->where('client_id', $client_id)->where('status', '=','hold')->count();
				return $jobCounter;
            })
			
			 ->editColumn('total_jobs', function ($row) {
                $client_id  	= '';
				$client_id  	= $row->id;
				$jobCounter		= DB::table('jobs')->select('*')->where('client_id', $client_id)->count();
				return $jobCounter;
            }) 	
			/* ->editColumn('commission  ', function ($row) {
                return ucfirst($row->commission  );
            })
			 ->editColumn('payout   ', function ($row) {
                return ucfirst($row->payout   );
            })*/
			
			
			-> rawColumns(['action', 'contact_person_name', 'location', 'open_jobs', 'closed_jobs', 'hold_jobs', 'total_jobs'])
			
            ->addIndexColumn()
            ->make(true);
    }	
    public function create()
    {
        //
    }
    
    public function store(Request $request)
    {
        //
    }
   
    public function show($id)
    {
        //
    }
    
    public function edit($id)
    {
        //
    }
    
    public function update(Request $request, $id)
    {
        //
    }
    
    public function destroy($id)
    {
        //
    }
	
}
