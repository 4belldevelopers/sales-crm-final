<?php
/*** ########################################################
Controller    	: 	AdmincandidateStatusController
Purmose			: 
Author			:	Abu
Company			:	4Bell
Date			:	05-April-2021	
########################################################** */

namespace App\Http\Controllers\Admin;


use App\Helper\Reply;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;
use App\CandidateStatus;

/*use App\Helper\Files;
use App\Helper\GeneralMethod;
use App\Helper\Reply;
use App\Http\Requests\StoreSkill;
use App\JobCategory;
use App\Client;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;

use App\Country;
use App\CandidateStatus;*/


class AdminCandidateStatusController extends AdminBaseController
{

    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = __('Interview Process');
        $this->pageIcon = 'icon-grid';
    }

    
    public function index(Request $request)
    {
        //abort_if(! $this->user->can('view_skills'), 403);
		
		
		$this->statuslist 		= CandidateStatus::with('children')->orderBy('priority', 'ASC')->get();		
		$this->master_status 	= DB::table('master_status')->select('*')->orderBy('priority', 'ASC')->get();
	
        return view('admin.candidateStatus.index', $this->data);
    }
  
    public function create()
    {
		//echo user()->company_id; exit; 
       // abort_if(! $this->user->can('add_skills'), 403);
		
		$this->master_status 	= DB::table('master_status')->select('*')->orderBy('priority', 'ASC')->get();
		
		$this->status 			= DB::table('candidate_status')->select('*')->where('company_id', user()->company_id)->where('parent', 0)->orderBy('priority', 'ASC')->get();
       
	    return view('admin.candidateStatus.create', $this->data);
    }	 
	
    public function store(Request $request)
    {
        //abort_if(! $this->user->can('add_skills'), 403);
		
		$Sourcing				=	$Sourcing_priority = $Sourcinghidden = '';
		$Sourcing      			=	@$request->Sourcing;
		$Sourcing_priority  	=	@$request->Sourcing_priority;
		$Sourcinghidden  		=	@$request->Sourcinghidden;
		
		$Offered				=	$Offered_priority = $Offeredhidden = '';
		$Offered      			=	$request->Offered;
		$Offered_priority 		=	$request->Offered_priority;
		$Offeredhidden 			=	$request->Offeredhidden;
		
		$Joined					=	$Joined_priority = $Joinedhidden	='';
		$Joined      			=	$request->Joined;
		$Joined_priority 		=	$request->Joined_priority;
		$Joinedhidden 			=	$request->Joinedhidden;
		
		$Rejected				=	$Rejected_priority = $Rejectedhidden = '';
		$Rejected      			=	$request->Rejected;
		$Rejected_priority 		=	$request->Rejected_priority;
		$Rejectedhidden 		=	$request->Rejectedhidden;
		
		// SOURCING 
		$first			=	0;
		 if(!empty($Sourcing)){
		 	foreach($Sourcing as $Sourcing_val){
				
				$sourcing_value =	$sourcing_p = $sourcing_h = '';
				
				$sourcing_value = 	@$Sourcing_val;
				$sourcing_p 	=	@$Sourcing_priority[$first];
				$sourcing_h 	=	@$Sourcinghidden[$first];
				
				if(!empty($sourcing_h) && $sourcing_h > 0){
					DB::table('candidate_status')->where('company_id', user()->company_id)->where('parent', 1)->where('id', @$sourcing_h)
					->update(array('name' => @$sourcing_value, 'priority' => @$sourcing_p )); 
					
				}else{
						$values = array('company_id' => user()->company_id, 'parent' => 1, 'name' => $sourcing_value, 'priority' => $sourcing_p);
						DB::table('candidate_status')->insert($values);
				}
				$first = $first+1;
			}
		 }
		 
		 // SCREENING 
		$Screening				=	$Screening_priority = $Screeninghidden = '';
		$Screening      		=	@$request->Screening;
		$Screening_priority 	=	@$request->Screening_priority;
		$Screeninghidden 		=	@$request->Screeninghidden;
		$second			=	0;
		if(!empty($Screening)){
		 	foreach($Screening as $Screening_val){
				
				$Screening_value 	=	$Screening_p = $Screeningh = '';
				
				$Screening_value 	= 	@$Screening_val;
				$Screening_p 		=	@$Screening_priority[$second];
				$Screeningh 		=	@$Screeninghidden[$second];
				
				if(!empty($Screeningh) && $Screeningh > 0){
					DB::table('candidate_status')->where('company_id', user()->company_id)->where('parent', 2)->where('id', @$Screeningh)
					->update(array('name' => @$Screening_value, 'priority' => @$Screening_p )); 
					
				}else{
						$values = array('company_id' => user()->company_id, 'parent' => 2, 'name' => $Screening_value, 'priority' => $Screening_p);
						DB::table('candidate_status')->insert($values);
				}
				$second = $second+1;
			}
		 }
		 
		// Interview 
		$third					=	0;
		$Interview				=	$Interview_priority = $Interviewhidden = '';
		$Interview      		=	@$request->Interview;
		$Interview_priority 	=	@$request->Interview_priority;
		$Interviewhidden 		=	@$request->Interviewhidden;
		if(!empty($Interview)){
		 	foreach($Interview as $Interview_val){
				
				$Interview_value 	=	$Interview_p = $Interview_h = '';
				
				$Interview_value 	= 	@$Interview_val;
				$Interview_p 		=	@$Interview_priority[$third];
				$Interview_h 		=	@$Interviewhidden[$third];
				
				if(!empty($Interview_h) && $Interview_h > 0){
					DB::table('candidate_status')->where('company_id', user()->company_id)->where('parent', 3)->where('id', @$Interview_h)
					->update(array('name' => @$Interview_value, 'priority' => @$Interview_p )); 
					
				}else{
						$values = array('company_id' => user()->company_id, 'parent' => 3, 'name' => $Interview_value, 'priority' => $Interview_p);
						DB::table('candidate_status')->insert($values);
				}
				$third = $third+1;
			}
		 }



		// Negotation
		$forth					=	0;
		$Negotiation			=	$Negotiation_priority = $Negotiationhidden	='';
		$Negotiation      		=	@$request->Negotiation;
		$Negotiation_priority 	=	@$request->Negotiation_priority;
		$Negotiationhidden 		=	@$request->Negotiationhidden;
		if(!empty($Negotiation)){
		
		 	foreach($Negotiation as $Negotiation_val){
				
				$Negotation_value 	=	$Negotiation_p = $Negotation_h = '';
				
				$Negotation_value 	= 	@$Negotiation_val;
				$Negotiation_p 		=	@$Negotiation_priority[$forth];
				$Negotation_h 		=	@$Negotiationhidden[$forth];
				
				if(!empty($Negotation_h) && $Negotation_h > 0){
						DB::table('candidate_status')->where('company_id', user()->company_id)->where('parent', 4)->where('id', @$Negotation_h)
						->update(array('name' => @$Negotation_value, 'priority' => @$Negotiation_p )); 
					
				}else{
						$values = array('company_id' => user()->company_id, 'parent' => 4, 'name' => $Negotation_value, 'priority' => $Negotiation_p);
						DB::table('candidate_status')->insert($values);
				}
				$forth = $forth+1;
			}
		 }
		 
		// OFFERED
		$five					=	0;
		$Offered				=	$Offered_priority = $Offeredhidden	='';
		$Offered      			=	@$request->Offered;
		$Offered_priority 		=	@$request->Offered_priority;
		$Offeredhidden 			=	@$request->Offeredhidden;
		if(!empty($Offered)){
		
		 	foreach($Offered as $Offered_val){
				
				$Offered_value 	=	$Offered_p = $Offered_h = '';
				
				$Offered_value 		= 	@$Offered_val;
				$Offered_p 			=	@$Offered_priority[$five];
				$Offered_h 			=	@$Offeredhidden[$five];
				
				if(!empty($Offered_h) && $Offered_h > 0){
						DB::table('candidate_status')->where('company_id', user()->company_id)->where('parent', 5)->where('id', @$Offered_h)
						->update(array('name' => @$Offered_value, 'priority' => @$Offered_p )); 
					
				}else{
						$values = array('company_id' => user()->company_id, 'parent' => 5, 'name' => $Offered_value, 'priority' => $Offered_p);
						DB::table('candidate_status')->insert($values);
				}
				$five = $five+1;
			}
		 }
		 
		
		
		// JOINED
		$six					=	0;
		$Joined					=	$Joined_priority = $Joinedhidden	='';
		$Joined      			=	@$request->Joined;
		$Joined_priority 		=	@$request->Joined_priority;
		$Joinedhidden 			=	@$request->Joinedhidden;
		if(!empty($Joined)){
		
		 	foreach($Joined as $Joined_val){
				
				$Joined_value 	=	$Joined_p = $Joined_h = '';
				
				$Joined_value 		= 	@$Joined_val;
				$Joined_p 			=	@$Joined_priority[$six];
				$Joinedh 			=	@$Joinedhidden[$six];
				
				if(!empty($Joinedh) && $Joinedh > 0){
						DB::table('candidate_status')->where('company_id', user()->company_id)->where('parent', 6)->where('id', @$Joinedh)
						->update(array('name' => @$Joined_value, 'priority' => @$Joined_p )); 
					
				}else{
						$values = array('company_id' => user()->company_id, 'parent' => 6, 'name' => $Joined_value, 'priority' => $Joined_p);
						DB::table('candidate_status')->insert($values);
				}
				
				$six 				= $six+1;
			}
		 } 
		 
		// echo '<pre>'; print_r($_REQUEST); exit;
		 // Rejected
		$seven					=	0;
		$Rejected				=	$Rejected_priority = $Rejectedhidden	='';
		$Rejected      			=	@$request->Rejected;
		$Rejected_priority 		=	@$request->Rejected_priority;
		$Rejectedhidden 		=	@$request->Rejectedhidden;
		if(!empty($Rejected)){
		
		 	foreach($Rejected as $Rejected_val){
				
				$Rejected_value 	=	$Rejected_p = $Rejected_h = '';
				
				$Rejected_value 	= 	@$Rejected_val;
				$Rejected_p 		=	@$Rejected_priority[$seven];
				$Rejectedh 			=	@$Rejectedhidden[$seven];
				
				if(!empty($Rejectedh) && $Rejectedh > 0){
						DB::table('candidate_status')->where('company_id', user()->company_id)->where('parent', 7)->where('id', @$Rejectedh)
						->update(array('name' => @$Rejected_value, 'priority' => @$Rejected_p, 'delete_status'=> 1 )); 
					
				}else{
						$values = array('company_id' => user()->company_id, 'parent' => 7, 'name' => $Rejected_value, 'priority' => $Rejected_p, 'delete_status'=> 1);
						DB::table('candidate_status')->insert($values);
				}
				
				$seven 				= $seven+1;
			}
		 } 
		
 		
		//echo '<pre>'; print_r($_REQUEST); exit; 
		
        return Reply::redirect(route('admin.candidateStatus.index'), __('menu.jobCategories').' '.__('messages.createdSuccessfully'));
		
    }
   
    public function show($id)
    {
        //
    }
  
    public function edit($id)
    {
        //abort_if(! $this->user->can('edit_skills'), 403);
		$this->candidate_status 	= DB::table('candidate_status')->select('*')->where('id', $id)->get();
		$this->status 				= DB::table('candidate_status')->select('*')->where('company_id', user()->company_id)->where('parent', 0)->get();
        return view('admin.candidateStatus.edit', $this->data);

    }
    
    public function update(Request $request, $id)
    {
       // abort_if(! $this->user->can('edit_skills'), 403);

       $CandidateStatus 			= CandidateStatus::find($id);
		
		if(!empty($request->parent)){
			$CandidateStatus->parent = $request->parent;
		}
		if(!empty($request->title)){
			$CandidateStatus->name = $request->title;
		}
		if(!empty($request->priority)){
			$CandidateStatus->priority = $request->priority;
		}
		
        $CandidateStatus->save();
        return Reply::redirect(route('admin.candidateStatus.index'), __('menu.clients').' '.__('messages.updatedSuccessfully'));
    }
    
    public function destroy($id)
    {
        //abort_if(! $this->user->can('delete_skills'), 403);

        CandidateStatus::destroy($id);
        return Reply::success(__('messages.recordDeleted'));
    }
	
	public function deleterecord($id)
	{
		$client_file 	= DB::table('client_file')->select('*')->where('id', $id)->first();
        if(file_exists($client_file->file_logical_path))
        {
            unlink(@$client_file->file_logical_path);
        }
		
		DB::table('client_file')->where('id', $id)->delete();
		return Reply::success(__('messages.recordDeleted'));
	}
		
    public function data() 
	{
       
	   // abort_if(! $this->user->can('view_skills'), 403);

       	//$client 	= CandidateStatus::all();
		$client 	= CandidateStatus::with('children')->orderBy('id', 'ASC')->orderBy('parent', 'ASC')->get();

		//$client 	= CandidateStatus:: group->with('children', 'parent')->first(1);
		//$client 	= $client->children()->orderBy('name')->get();
		//->orderBy('candidate_status.parent', 'ASC')

		//echo '<pre>'; print_r($client); exit; 
        return DataTables::of($client)
		
            ->addColumn('action', function ($row) {
                	
					$action = '';
				
				
					if( $this->user->can('edit_client')){
						$action.= '<a href="' . route('admin.candidateStatus.edit', [$row->id]) . '" class="btn btn-primary btn-circle"
						data-toggle="tooltip" data-original-title="'.__('app.edit').'"><i class="fa fa-pencil" aria-hidden="true"></i></a>';
					}
					if( $this->user->can('delete_client')){
						$action.= ' <a href="javascript:;" class="btn btn-danger btn-circle sa-params"
						data-toggle="tooltip" data-row-id="' . $row->id . '" data-original-title="'.__('app.delete').'"><i class="fa fa-times" aria-hidden="true"></i></a>';
					}	
					  
            	return $action;
					
            })
            
			
			->editColumn('name', function ($row) {
				if($row->parent ==0){
                	return ucfirst($row->name);
				}else{
					return '...';
				}
            })
			 
			

			->addColumn('status_child', function ($row) {
                
				$childName		= '';
				if($row->parent ==0){
					$childName		= '...';	
				}else{
					$childName		= ucfirst($row->name);	
				}
				
				return $childName;
				
           	 	})
				
			->addColumn('status_child', function ($row) {
                
				$childName		= '';
				if($row->parent ==0){
					$childName		= '...';	
				}else{
					$childName		= ucfirst($row->name);	
				}
				
				return $childName;
				
           	 	})
			
			
			-> rawColumns(['action', 'status_child'])
            ->addIndexColumn()
            ->make(true);
    }
	
	public function download($id)
    {
		
	    $client_file 	= DB::table('client_file')->select('*')->where('id', $id)->first();
		//$client_file->file_logical_path
		
	   // $jobApplication = JobApplication::findOrFail($id);
        if(!file_exists($client_file->file_logical_path))
        {
            abort(404);
        }
        return response()->download($client_file->file_logical_path);
    }
	
	public function deleteUser(Request $request)
	{
		$id	=	'';
		$id	=	$request->id;
		if($id > 0)
		{
			DB::table('client_contact_info')->where('id', $id)->delete();
			return Reply::success(__('messages.recordDeleted'));
		}
		
	}
	
	public function deleteAddress(Request $request)
	{
		$id	=	'';
		$id	=	$request->id;
		if($id > 0)
		{
			DB::table('client_location')->where('id', $id)->delete();
			return Reply::success(__('messages.recordDeleted'));
		}
		
	}	
	
	public function edit_information($id)
    {

		 $this->cc_info 		= DB::table('client_contact_info')->select('*')->where('id', $id)->first();		
         return view('admin.client.edit_information', $this->data);
    }
	
	public function edit_location($id)
    {
      
		$this->cl_info 	= DB::table('client_location')
								->join('clients', 'clients.id', '=', 'client_location.client_id')
								->select('clients.name as client_name', 'client_location.id', 'client_location.address', 'client_location.city', 'client_location.postcode', 'client_location.country_id')
								->where('client_location.id', $id)
								->first();
		 
		//echo '<pre>'; print_r($this->cl_info); 
		$this->countries 			= Country::all();
		
       return view('admin.client.edit_location', $this->data);
    }	
	
	 public function update_info(Request $request, $id)
    {

       		$updateArr  = array(
									'contact_person_name' 			=> @$request->contact_person_name,
									'contact_person_email' 			=> @$request->contact_person_email,
									'contact_person_number' 		=> @$request->contact_person_number,
									'contact_person_designation' 	=> @$request->contact_person_designation
								); 
			DB::table('client_contact_info')
			->where('id', @$id)
			->limit(1) 
			->update( $updateArr ); 
		

        return Reply::redirect(route('admin.clients.index'), __('menu.clients').' '.__('messages.updatedSuccessfully'));
    }	
	
	 public function update_loc(Request $request, $id)
    {

       		$updateArr  = array(
									'address' 					=> @$request->address ,
									'city' 						=> @$request->city ,
									'postcode' 					=> @$request->postcode,
									'country_id' 				=> @$request->country_id 
								); 
			DB::table('client_location')
			->where('id', @$id)
			->limit(1) 
			->update( $updateArr ); 
		

        return Reply::redirect(route('admin.clients.index'), __('menu.clients').' '.__('messages.updatedSuccessfully'));
    }
	
	public function usermore(Request $request)
	{
		$client_id = '';
		$client_id = $request->client_id;
		
		$contact_person_name = '';
		$contact_person_name = $request->contact_person_name;
		
		$contact_person_email = '';
		$contact_person_email = $request->contact_person_email;
		
		
		$contact_person_number = '';
		$contact_person_number = $request->contact_person_number;
		
		$contact_person_designation = '';
		$contact_person_designation = $request->contact_person_designation;
		
		if($client_id !='' && $contact_person_name !='' &&  $contact_person_email !='' && $contact_person_number !='' && $contact_person_designation != '')
		{
				if (!filter_var($contact_person_email, FILTER_VALIDATE_EMAIL)) 
				{
					return Reply::dataOnly(['status' => 'error', 'message' => "Please enter proper information."]);
					exit;
				}
				
			$insertArr = array(
									'client_id' 				 => $client_id,
									'contact_person_name' 		 => $contact_person_name,
									'contact_person_email' 		 => $contact_person_email,
									'contact_person_number' 	 => $contact_person_number,
									'contact_person_designation' => $contact_person_designation
								);
			DB::table('client_contact_info')->insert($insertArr); 
			$insertedId	= DB::getPdo()->lastInsertId();
			
			if($insertedId > 0)
			{
				$option 	= array('id' => $insertedId , 'value' => ucwords($contact_person_name) );
				return Reply::dataOnly(['status' => 'success', 'message' => "Contact person information added successfully.", 'option'=> $option]);		
			}
			else
			{
				return Reply::dataOnly(['status' => 'error', 'message' => "Please enter proper information."]);
			}
		
		}
		else
		{
			return Reply::dataOnly(['status' => 'error', 'message' => "Please enter proper information."]);	
		}
		
		
		
	}	
	
	public function addressmore(Request $request)
	{
		//echo '<pre>'; print_r($_REQUEST); exit; 
		$client_id 		= '';
		$client_id 		= $request->client_id;
		
		$country_id 	= '';
		$country_id 	= $request->country_id;
		
		$city 			= '';
		$city 			= $request->city;
		
		
		$address 		= '';
		$address 		= $request->address;
		
		$postcode 		= '';
		$postcode 		= $request->postcode;
		
		
		if($client_id !='' && $country_id !='' && $city !='' && $address !='' && $postcode !='')
		{
				$insertArr = array(
									'client_id' 	=> $client_id,
									'company_id'	=> $this->user->company_id,
									'city' 		 	=> $city,
									'address' 		=> $address,
									'postcode' 	 	=> $postcode,
									'country_id' 	=> $country_id
								);
				DB::table('client_location')->insert($insertArr); 
				$insertedId	= DB::getPdo()->lastInsertId();
				if($insertedId > 0){
					
				$country_row 	= DB::table('countries')->select('*')->where('id', $country_id)->first(); 
				/*$option 		= '<option   value="'.$insertedId.'">'.ucwords($address).'&nbsp;-&nbsp;'.ucwords($city).'&nbsp;-&nbsp;'.ucwords($postcode).'&nbsp;-&nbsp;'.ucwords($country_row->country_name).'</option>';*/
				
				$option 	= array(
										'id' 			=> $insertedId , 
										'address' 		=> ucwords($address),
										'city' 			=> $city, 
										'postcode' 		=> $postcode, 
										'country_name' 	=> ucwords($country_row->country_name)
								  );
				
				return Reply::dataOnly(['status' => 'success', 'message' => "Office address added successfully.", 'option'=> $option ]);		
		
				}
				else
				{
					return Reply::dataOnly(['status' => 'error', 'message' => "Please enter proper information."]);
				}	
		}
		else
		{
			return Reply::dataOnly(['status' => 'error', 'message' => "Please enter proper information."]);
		}
		
		
	}
	
	public function delete()
	{
		$id		=	'';
		$id		= $_REQUEST['id'];
		
		DB::table('candidate_status')->where('id', $id)->delete();
		echo 1; exit; 
	}
	

}