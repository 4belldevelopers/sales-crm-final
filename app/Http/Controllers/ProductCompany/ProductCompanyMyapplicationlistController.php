<?php

/***########################################################

Controller    	: 	AdminClientController
Purmose			: 
Author			:	Abu
Company			:	4Bell
Date			:	05-April-2021	

########################################################***/

namespace App\Http\Controllers\ProductCompany;

use App\Helper\Reply;
use App\Http\Requests\StoreJobCategory;
//use App\JobCategory;
//use App\Skill;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;

use App\Helper\Files;
use App\Helper\GeneralMethod;

//use App\JobCategory;
use App\Models\Client;

use Illuminate\Support\Facades\DB;
use App\Models\Country;
use App\Exports\ClientExport;
use Maatwebsite\Excel\Excel as ExcelExcel;
use Maatwebsite\Excel\Facades\Excel;

class ProductCompanyMyapplicationlistController extends ProductCompanyBaseController    
{

    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = __('Clients Management');
        $this->pageIcon = 'icon-grid';
    }
    
    public function index()
    	{
        //abort_if(! $this->user->cans('view_category'), 403);

        return view('product_company.client.index', $this->data);
		
		
    }
   
    public function create()
    	{
       // abort_if(! $this->user->cans('add_category'), 403);
		$this->countries = Country::all();
        return view('product_company.client.create', $this->data);
		
    }
 
    public function store(Request $request)
    	{
       
		
		
		$timeStamp 		= time();
		$company_id 	= '';
        
		$names      	= $request->name;
        $company_id 	= $request->user()->company_id;
		
		if (trim($request->name) == '') {
            return Reply::error(__('errors.addClients'));
        }
		
		if (trim($request->email) != '') {
			if (!filter_var($request->email, FILTER_VALIDATE_EMAIL)) {
            	return Reply::error(__('errors.emailAddressInvalid'));
			}
        }
		
		
		
		$client 							= new Client();
		$client->company_id 				= @$company_id;
		if(!empty(@$request->name)){
			$client->name 					= @$request->name;	
		}
		if(!empty(@$request->email)){
			//$client->email 					= @$request->email;	
		}
		if(!empty(@$request->contact_person_name)){
			$client->contact_person_name 	= @$request->contact_person_name;	
		}
		if(!empty(@$request->contact_number)){
			$client->contact_number 		= @$request->contact_number;	
		}
		if(!empty(@$request->website)){
			$client->website 			  = @$request->website;	
		}
		
		
		
		if(!empty(@$request->payout)){
			$client->payout 			  = @$request->payout;	
		}
		if(!empty(@$request->commission)){
			$client->commission 		  = @$request->commission;	
		}
		if(!empty(@$request->detail)){
			$client->detail 			  = @$request->detail;	
		}
		if(!empty(@$request->feedback)){
			$client->feedback 			  = @$request->feedback;	
		}
		
	    $client->save();
		$client_id 	= DB::getPdo()->lastInsertId();
		
		
		// Contact Person
		$cp_name 	= $cp_email = $cp_number = $cp_designation = '';
		
		$cp_name 			= $request->cp_name;
		$cp_email 			= $request->cp_email;
		$cp_number 			= $request->cp_number;
		$cp_designation 	= $request->cp_designation;
		
		if(!empty($cp_name))
		{
			for($i=0; $i<count($cp_name); $i++)
			{
				$name 	= $number = $email = $designation = '';
				
				$name 			= $cp_name[$i];
				$email 			= $cp_email[$i];
				$number 		= $cp_number[$i];
				$designation 	= $cp_designation[$i];
				
				$values = array(
									'client_id' 				=> $client_id, 
									'contact_person_name' 		=> $name, 
									'contact_person_email'		=> $email,  
									'contact_person_number' 	=> $number, 
									'contact_person_designation'=> $designation 
								);
				DB::table('client_contact_info')->insert($values);
			}
		}
		 // Location
		 //client_location
		 
		$address 	=  $pincode 	= $country_id = $city = '';
		$address 	= $request->address;
		$postcode 	= $request->postcode;
		$country_id = $request->country_id;
		$city 		= $request->city;
		
		if(!empty($country_id))
		{
			for($i=0; $i<count($country_id); $i++)
			{
				$cl_address 	= $post = $country = $cl_city = '';
				
				$country 		= $country_id[$i];
				$cl_city 		= $city[$i];
				$cl_address 	= $address[$i];
				$post 			= $postcode[$i];

				$values = array(
									'client_id' 	=> $client_id, 
									'country_id' 	=> $country, 
									'city' 			=> $cl_city, 
									'address' 		=> $cl_address, 
									'postcode' 		=> $post
								);
				DB::table('client_location')->insert($values);
			}
		}
		
		//echo '<pre>'; print_r($location); 
		//exit;
		
		
		
		$maxsize    	= 5242880;
		$directoryPath  = GeneralMethod :: document_manage_directory($company_id);
		if ($request->hasFile('image')) 
		{
			for($i=0; $i < count($_FILES['image']); $i++ )
			{
				$name		 = $file_size = $file_type = $temp = $target_file = $logicalpath    = '';
				
				$file_size	 	= @$_FILES['image']['size'][$i];
				$file_type	 	= @$_FILES['image']['type'][$i];
				$temp		 	= @$_FILES['image']['tmp_name'][$i];
				
				if(@$_FILES['image']['size'][$i] > 0)
				{
				
					$path_parts 	= pathinfo($_FILES['image']['name'][$i]);
					$extension 		= $path_parts['extension'];
					$filename 		= strtolower($path_parts['filename']);
					$altfilename 	= GeneralMethod :: getValidUploadFileName($filename) . "-" . $timeStamp . "." . $extension;
				
					$dirArr 		= explode("/",$directoryPath);
					$target_file 	=  $directoryPath.'/'.$altfilename; 
					
					$allowed 		= array('doc', 'docx', 'pdf', 'png', 'jpeg', 'jpg', 'DOC', 'DOCX', 'PDF', 'PNG', 'JPEG', 'JPG' );
					$ext 			= pathinfo($_FILES['image']['name'][$i], PATHINFO_EXTENSION); 
				}
				
				if ( in_array($ext , $allowed)  && ($file_size < $maxsize) ) 
				{
				    $logicalpath = substr($target_file, strpos($target_file, 'user-uploads')); 
					
					if(!empty($company_id) && !empty($client_id) && !empty($altfilename) && !empty($target_file) && !empty($logicalpath) && !empty($file_type) && !empty($file_size) )
					{ 
					
							if (move_uploaded_file($temp, $target_file)) 
							{
										$values = array(
															'company_id' 		 => @$company_id,
															'client_id' 		 => @$client_id, 
															'file_name' 		 => @$altfilename,
															'file_physical_path' => @$target_file,
															'file_logical_path'  => @$logicalpath,
															'file_type'			 => @$file_type,
															'file_size'			 => @$file_size
														);
														
										//echo '<pre>'; print_r($values); exit; 				
										DB::table('client_file')->insert($values);
							}
					} 
					
				}	
						
				 
			}
        }
		
        return Reply::redirect(route('product_company.clients.index'), __('menu.clients').' '.__('messages.createdSuccessfully'));
			
		
    }
  
    public function show($id)
    	{
        //
    }
    
    public function edit($id)
    	{
       // abort_if(! $this->user->cans('edit_category'), 403);

       /* $this->category = JobCategory::findOrFail($id);
        return view('product_company.client.edit', $this->data);*/
		
		$this->client 		= Client::find($id);
		$this->client_file 	= DB::table('client_file')->select('*')->where('client_id', $id)->get();
		 
		 $this->countries = Country::all();
		
		$this->client_location 		= DB::table('client_location')
										->join('countries', 'countries.id', '=', 'client_location.country_id')
										->select('client_location.city', 'client_location.address', 'client_location.postcode', 'countries.country_name')
										->where('client_location.client_id', $id)
										->get();
		$this->client_contact_info 	= DB::table('client_contact_info')->select('*')->where('client_id', $id)->get();
		
        return view('product_company.client.edit', $this->data);
		
		
    }
  
    public function update1(StoreJobCategory $request, $id)
    	{
		   // abort_if(! $this->user->cans('edit_category'), 403);	
			$category 			= JobCategory::find($id);
			$category->name 	= $request->name;
			$category->save();
			return Reply::redirect(route('product_company.client.index'), __('menu.jobCategories').' '.__('messages.updatedSuccessfully'));
    	}
	
	public function update(Request $request, $id)
    	{
        //abort_if(! $this->user->can('edit_skills'), 403);

        $client 				= Client::find($id);
		
		if(!empty($request->name)){
			$client->name 		= $request->name;
		}
		if(!empty($request->email)){
			//$client->email 		= $request->email;
		}
		if(!empty($request->contact_person_name)){
			$client->contact_person_name = $request->contact_person_name;
		}
		if(!empty($request->contact_number)){
			$client->contact_number = $request->contact_number;
		}
		if(!empty($request->website)){
			$client->website 		= $request->website;
		}
		
		if(!empty(@$request->payout)){
			$client->payout 			  = @$request->payout;	
		}
		if(!empty(@$request->commission)){
			$client->commission 		  = @$request->commission;	
		}
		
		if(!empty($request->detail)){
			$client->detail 	   = $request->detail;
		}
		if(!empty($request->feedback)){
			$client->feedback 	   = $request->feedback;
		}
			
        $client->save();
		
		
		$client_id = $id;
		
		// Contact Person
		$cp_name 	= $cp_email = $cp_number = $cp_designation = '';
		
		$cp_name 			= $request->cp_name;
		$cp_email 			= $request->cp_email;
		$cp_number 			= $request->cp_number;
		$cp_designation 	= $request->cp_designation;
		
		if(!empty($cp_name))
		{
			for($i=0; $i<count($cp_name); $i++)
			{
				$name 	= $number = $email = $designation = '';
				
				$name 			= $cp_name[$i];
				$email 			= $cp_email[$i];
				$number 		= $cp_number[$i];
				$designation 	= $cp_designation[$i];
				
				if($client_id != '' && $name != '' && $email !='' && $number != '' && $designation != '' )
				{
					$values = array(
										'client_id' 				=> $client_id, 
										'contact_person_name' 		=> $name, 
										'contact_person_email'		=> $email,  
										'contact_person_number' 	=> $number, 
										'contact_person_designation'=> $designation 
									);
					DB::table('client_contact_info')->insert($values);
				}
			}
		}
		 // Location
		 //client_location
		 
		$address 	=  $pincode 	= $country_id = $city = '';
		$address 	= $request->address;
		$postcode 	= $request->postcode;
		$country_id = $request->country_id;
		$city 		= $request->city;
		
		if(!empty($country_id))
		{
			for($i=0; $i<count($country_id); $i++)
			{
				$cl_address 	= $post = $country = $cl_city = '';
				
				$country 		= $country_id[$i];
				$cl_city 		= $city[$i];
				$cl_address 	= $address[$i];
				$post 			= $postcode[$i];

				if($client_id != '' && $country != '' && $cl_city !='' && $post != '')
				{
					$values = array(
										'client_id' 	=> $client_id, 
										'country_id' 	=> $country, 
										'city' 			=> $cl_city, 
										'address' 		=> $cl_address, 
										'postcode' 		=> $post
									);
					DB::table('client_location')->insert($values);
				} 
			}
		}
		
		
		//echo '<pre>'; print_r($_FILES['image']); exit; 
		$timeStamp 		= time();
		$company_id 	= $request->user()->company_id;
		$maxsize    	= 5242880;
		$directoryPath  = GeneralMethod :: document_manage_directory($company_id);
		if ($request->hasFile('image')) 
		{
			for($i=0; $i <= count($_FILES['image']); $i++ )
			{
				$name		 	= $file_size 	= $file_type 	= $temp 	= $target_file 	= $logicalpath = '';
				
				$file_size	 	= @$_FILES['image']['size'][$i];
				$file_type	 	= @$_FILES['image']['type'][$i];
				$temp		 	= @$_FILES['image']['tmp_name'][$i];
				if(@$_FILES['image']['size'][$i] > 0)
				{
					$path_parts 	= pathinfo($_FILES['image']['name'][$i]);
					$extension 		= $path_parts['extension'];
					$filename 		= strtolower($path_parts['filename']);
					$altfilename 	= GeneralMethod :: getValidUploadFileName($filename) . "-" . $timeStamp . "." . $extension;
					 
					$dirArr 		= explode("/",$directoryPath);
					$target_file 	=  $directoryPath.'/'.$altfilename; 
					
					$allowed 		= array('doc', 'docx', 'pdf', 'png', 'jpeg', 'jpg', 'DOC', 'DOCX', 'PDF', 'PNG', 'JPEG', 'JPG' );
					$ext 			= pathinfo($_FILES['image']['name'][$i], PATHINFO_EXTENSION); 
				}
				
				if ( in_array($ext , $allowed)  && ($file_size < $maxsize) ) 
				{
				    $logicalpath = substr($target_file, strpos($target_file, 'user-uploads'));  
					if(!empty($company_id) && !empty($id) && !empty($altfilename) && !empty($target_file) && !empty($logicalpath) && !empty($file_type) && !empty($file_size) )
					{
						if (move_uploaded_file($temp, $target_file)) 
						{
									$values = array(
														'company_id' 		 => $company_id,
														'client_id' 		 => $id, 
														'file_name' 		 => $altfilename,
														'file_physical_path' => $target_file,
														'file_logical_path'  => $logicalpath,
														'file_type'			 => $file_type,
														'file_size'			 => $file_size
													);
									DB::table('client_file')->insert($values);
						}
					}	
					
				}	
						
				 
			}
        }
		
        return Reply::redirect(route('product_company.clients.index'), __('menu.clients').' '.__('messages.updatedSuccessfully'));
    }
	  
    public function destroy($id)
    	{
       // abort_if(! $this->user->cans('delete_category'), 403);

      /*  JobCategory::destroy($id);
        return Reply::success(__('messages.recordDeleted'));*/
		
		Client::destroy($id);
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
	
    public function getSkills111($categoryId)
		{
        $jobSkills = '';

        $skills = Skill::where('category_id', $categoryId)->get();

        foreach($skills as $skill){
            $jobSkills.= '<option selected value="'.$skill->id.'">'.ucwords($skill->name).'</option>';
        }

        return Reply::dataOnly(['status' => 'success', 'data' => $jobSkills]);
    }
	
	 public function data1111() 
	 	{
       // abort_if(! $this->user->cans('view_category'), 403);

        $categories = JobCategory::all();

        return DataTables::of($categories)
            ->addColumn('action', function ($row) {
                $action = '';

                if( $this->user->cans('edit_category')){
                    $action.= '<a href="' . route('product_company.job-categories.edit', [$row->id]) . '" class=""
                      data-toggle="tooltip" data-original-title="'.__('app.edit').'"><i class="fa fa-pencil text-primary" aria-hidden="true"></i></a>';
                }

                if( $this->user->cans('delete_category')){
                    $action.= ' <a href="javascript:;" class="sa-params"
                      data-toggle="tooltip" data-row-id="' . $row->id . '" data-original-title="'.__('app.delete').'"><i class="fa fa-times text-danger" aria-hidden="true"></i></a>';
                }
                return $action;
            })
            ->editColumn('name', function ($row) {
                return ucfirst($row->name);
            })
            ->addIndexColumn()
            ->make(true);
    }	
	
	 public function data() 
	 	{
	
	 	
       // abort_if(! $this->user->can('view_skills'), 403);

       	$client 	= Client::all();




        return DataTables::of($client)
		
            ->addColumn('action', function ($row) {
                	
					$action = '';
				
				//if( $this->user->cans('edit_client')){
						$action.= '<a href="' . route('product_company.clients.edit', [$row->id]) . '" class=""
						data-toggle="tooltip" data-placement="right" data-original-title="'.__('app.edit').'"><i class="fa fa-pencil text-primary" aria-hidden="true"></i></a>';
					//}
				if($row->self_company != 1)
				{
				
					
					//if( $this->user->cans('delete_client')){
						$action.= ' <a href="javascript:;" class="sa-params"
						data-toggle="tooltip" data-row-id="' . $row->id . '" data-original-title="'.__('app.delete').'"><i class="fa fa-times text-danger" aria-hidden="true"></i></a>';
					//}	
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
						$action.= '<a href="' . route('product_company.clients.edit_information', [$info->id]) . '" class="btn"
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
							$action.= '<a href="' . route('product_company.clients.edit_location', [$info->id]) . '" class="btn"
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
			
			->editColumn('commission  ', function ($row) {
                return ucfirst($row->commission  );
            })
			
			->editColumn('payout   ', function ($row) {
                return ucfirst($row->payout   );
            })
			
			
			-> rawColumns(['action', 'contact_person_name', 'location', 'open_jobs', 'closed_jobs', 'hold_jobs', 'total_jobs'])
			
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
        //abort_if(! $this->user->can('edit_skills'), 403);

       // $this->client 				= Client::find($id);
		$this->cc_info 	= DB::table('client_contact_info')->select('*')->where('id', $id)->first();
		 
		// echo '<pre>'; print_r($this->client_contact_info); 
		// $this->countries 			= Country::all();
		
		/*$this->client_location 		= DB::table('client_location')
										->join('countries', 'countries.id', '=', 'client_location.country_id')
										->select('client_location.city', 'client_location.address', 'client_location.postcode', 'countries.country_name')
										->where('client_location.client_id', $id)
										->get();
		$this->client_contact_info 	= DB::table('client_contact_info')->select('*')->where('client_id', $id)->get();*/
		
       return view('product_company.client.edit_information', $this->data);
    }
	
	public function edit_location($id)
    	{
        //abort_if(! $this->user->can('edit_skills'), 403);

       // $this->client 				= Client::find($id);
		$this->cl_info 	= DB::table('client_location')
								->join('clients', 'clients.id', '=', 'client_location.client_id')
								->select('clients.name as client_name', 'client_location.id', 'client_location.address', 'client_location.city', 'client_location.postcode', 'client_location.country_id')
								->where('client_location.id', $id)
								->first();
		 
		//echo '<pre>'; print_r($this->cl_info); 
		$this->countries 			= Country::all();
		
		/*$this->client_location 		= DB::table('client_location')
										->join('countries', 'countries.id', '=', 'client_location.country_id')
										->select('client_location.city', 'client_location.address', 'client_location.postcode', 'countries.country_name')
										->where('client_location.client_id', $id)
										->get();
		$this->client_contact_info 	= DB::table('client_contact_info')->select('*')->where('client_id', $id)->get();*/
		
       return view('product_company.client.edit_location', $this->data);
    }
		
	public function update_info(Request $request, $id)
    	{
        //abort_if(! $this->user->can('edit_skills'), 403);

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
		

        return Reply::redirect(route('product_company.clients.index'), __('menu.clients').' '.__('messages.updatedSuccessfully'));
    }
	
	 public function update_loc(Request $request, $id)
    	{
       // abort_if(! $this->user->can('edit_skills'), 403);

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
		

        return Reply::redirect(route('product_company.clients.index'), __('menu.clients').' '.__('messages.updatedSuccessfully'));
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
	
    public function export($start, $length)
    	{
        $filters = [
						'start' 	=> $start,
						'length' 	=> $length
				   ];

        $data 	= [
					'company' => $this->companyName
				  ];

        return Excel::download(new ClientExport($filters, $data), 'client_list.xlsx', ExcelExcel::XLSX);
    }
		
		

}
