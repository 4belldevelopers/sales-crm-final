<?php  
/***######################################################## 
Controller    	: 	AdminClientController
Purmose			: 
Author			:	Abu
Company			:	4Bell
Date			:	05-April-2021	
########################################################***/

namespace App\Http\Controllers\Admin;
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

use App\JobCategory;


class AdminClientController extends AdminBaseController {
    public function __construct() {
        parent::__construct();
        $this->pageTitle = __('Clients Management');
        $this->pageIcon = 'icon-grid';
    }
    public function index() {
        //abort_if(! $this->user->cans('view_category'), 403);
        return view('admin.client.index', $this->data);
    }
    
	public function create() {
		
		abort_if(! $this->user->cans('add_clients'), 403);
		
		$this->page_event	=	'ADD';
        // abort_if(! $this->user->cans('add_category'), 403);
        $this->countries 		= Country::all();
		$this->categories 		= JobCategory::all();
		$this->companyType		= GeneralMethod::companyType();
		$this->department		= GeneralMethod::department();
		$this->countryArr 		= GeneralMethod::getAllCountryList();		 
	    $this->statelist 		= GeneralMethod::statelist(101);
		
		
		$statelist 				=	DB::table('state_list')
									->select('state_list.id', 'state_list.state as name', DB::raw("101 as country_id"))
									->orderBy('state_list.state')
									->get()->toArray();
		$this->statelist       = json_decode(json_encode($statelist), true);
								
		//echo '<pre>'; print_r($this->statelist); exit; 
		
		
        return view('admin.client.create', $this->data);
    }
	
    public function store(Request $request) {
	
			abort_if(! $this->user->cans('add_clients'), 403);
				
			$company_id 	= '';
			$timeStamp 		= time();
		 	$names 			= $request->name;
		  	$company_id 	= $request->user()->company_id;
			if (trim($request->name) == '') {
            	return Reply::error(__('errors.addClients'));
        	}
						
			$client 				= new Client();
			$client->company_id 	= @$company_id;
			// FIRST TAB-1
			if (!empty(@$request->name)) {
            	$client->name 		= @$request->name;
        	}
			if (!empty(@$request->website)) {
            	$client->website = @$request->website;
        	}
			if (!empty(@$request->industry)) {
            	$client->industry = @$request->industry;
        	}
			if (!empty(@$request->company_sizes)) {
            	$client->company_sizes = @$request->company_sizes;
        	}
			if (isset($request->company_type) && $request->company_type !== '') {
            	$client->company_type = $request->company_type;
        	}
			if (!empty(@$request->founded)) {
            	$client->founded = @$request->founded;
        	}
			// SECOND TAB-2
			if (!empty(@$request->description)) {
            	$client->description = @$request->description; 
				//$client->detail = @$request->detail; 
       	 	}
			if (!empty(@$request->tags)) {
            	$tags 			=	@$request->tags;
				$tags 			=	json_decode($tags, true);
				$tags 			=	array_column($tags, 'value');
				$tags 			=	implode(',', $tags);
				$client->tags 	=	$tags;
        	}
			
			
			$client->save();
			$client_id = DB::getPdo()->lastInsertId();

			// THIRD TAB-3
			//if (!empty(@$request->addressId)) {
            	$addressId = @$request->addressId;
				for($i=0; $i<=$addressId; $i++)
				{
					$address 			= @$request->address[$i];
					$country 			= @$request->country[$i];
					$state 				= @$request->state[$i];
					
					$postcode 			= @$request->postcode[$i];	
					
					$city_id 			= $city = '';
					if($country == 101){
						$city_id 			= @$request->city_id[$i];	
					}else{
						$city 				= @$request->city[$i];	
					}
					
					$values 			= array(
													'client_id' 	=> $client_id, 
													'country_id' 	=> $country, 
													'city' 			=> $city,
													'city_id' 		=> $city_id, 
													'address' 		=> $address, 
													'postcode' 		=> $postcode, 
													'state' 		=> $state
												);
                	DB::table('client_location')->insert($values);
				}
        	//}
			// FORTH TAB-4
				$contactPersonId = @$request->contactPersonId;
				for($j=0; $j<=$contactPersonId; $j++)
				{
					$cp_name 			= @$request->cp_name[$j];
					$cp_email 			= @$request->cp_email[$j];
					$cp_number 			= @$request->cp_number[$j];
					$cp_desigination 	= @$request->cp_desigination[$j];						
					
					if(!empty($cp_email))
					{
						$values = array(
											'client_id' 					=> $client_id, 
											'contact_person_name' 			=> $cp_name, 
											'contact_person_email' 			=> $cp_email, 
											'contact_person_number' 		=> $cp_number, 
											'contact_person_designation' 	=> $cp_desigination
										);
						DB::table('client_contact_info')->insert($values);
					}
				}
							
		$maxsize 			= 5242880;
        $directoryPath 		= GeneralMethod::document_manage_directory($company_id);	
		
		if ($request->hasFile('logo')) 
		{
			$name = $file_size = $file_type = $temp = $target_file = $logicalpath = '';
			$file_size 		= @$_FILES['logo']['size'];
			$file_type 		= @$_FILES['logo']['type'];
			$temp 			= @$_FILES['logo']['tmp_name'];
			
			$path_parts 	= pathinfo($_FILES['logo']['name']);
			$extension 		= $path_parts['extension'];
			$filename 		= strtolower($path_parts['filename']);
			$altfilename 	= GeneralMethod::getValidUploadFileName($filename) . "-" . $timeStamp . "." . $extension;
			$dirArr 		= explode("/", $directoryPath);
			$target_file 	= $directoryPath . '/' . $altfilename;
			$allowed 		= array('doc', 'docx', 'pdf', 'png', 'jpeg', 'jpg', 'DOC', 'DOCX', 'PDF', 'PNG', 'JPEG', 'JPG');
			$ext 			= pathinfo($_FILES['logo']['name'], PATHINFO_EXTENSION);          
		    if (in_array($ext, $allowed)) 
			{
			  $logicalpath = substr($target_file, strpos($target_file, 'user-uploads'));
			  if (move_uploaded_file($temp, $target_file)) {
                            							
							$client 					= Client::find($client_id);
							$client->logo				= @$altfilename;	
							$client->logo_physical_path	= @$target_file;	
							$client->logo_logical_path	= @$logicalpath;	
							$client->save(); 
						
				}						
			}			
			
			 
        }
		return Reply::redirect(route('admin.clients.index'), __('menu.clients') . ' ' . __('messages.createdSuccessfully'));
		//return redirect(route('admin.clients.index'))->with('status', 'messages.createdSuccessfully');
		
		
		//echo '<pre>'; print_r($_REQUEST); exit;   
		
        
             
        /*if (trim($request->email) != '') {
            if (!filter_var($request->email, FILTER_VALIDATE_EMAIL)) {
                return Reply::error(__('errors.emailAddressInvalid'));
            }
        }  */    
        /*if (!empty(@$request->email)) {
            $client->email 					= @$request->email;
            
        }*/
        /*if (!empty(@$request->contact_person_name)) {
            $client->contact_person_name = @$request->contact_person_name;
        }*/
       /* if (!empty(@$request->contact_number)) {
            $client->contact_number = @$request->contact_number;
        }*/        
        /*if (!empty(@$request->payout)) {
            $client->payout = @$request->payout;
        }*/
        /*if (!empty(@$request->commission)) {
            $client->commission = @$request->commission;
        }*/        
       /* if (!empty(@$request->feedback)) {
            $client->feedback = @$request->feedback;
        }*/
        /*$client_id = DB::getPdo()->lastInsertId();*/		
        // Contact Person
       /* $cp_name = $cp_email = $cp_number = $cp_designation = '';
        $cp_name = $request->cp_name;
        $cp_email = $request->cp_email;
        $cp_number = $request->cp_number;
        $cp_designation = $request->cp_designation;
        if (!empty($cp_name)) {
            for ($i = 0;$i < count($cp_name);$i++) {
                $name = $number = $email = $designation = '';
                $name = $cp_name[$i];
                $email = $cp_email[$i];
                $number = $cp_number[$i];
                $designation = $cp_designation[$i];
                $values = array('client_id' => $client_id, 'contact_person_name' => $name, 'contact_person_email' => $email, 'contact_person_number' => $number, 'contact_person_designation' => $designation);
                DB::table('client_contact_info')->insert($values);
            }
        }*/	   
	    // Location
        //client_location
       /* $address = $pincode = $country_id = $city = '';
        $address = $request->address;
        $postcode = $request->postcode;
        $country_id = $request->country_id;
        $city = $request->city;
        if (!empty($country_id)) {
            for ($i = 0;$i < count($country_id);$i++) {
                $cl_address = $post = $country = $cl_city = '';
                $country = $country_id[$i];
                $cl_city = $city[$i];
                $cl_address = $address[$i];
                $post = $postcode[$i];
                $values = array('client_id' => $client_id, 'country_id' => $country, 'city' => $cl_city, 'address' => $cl_address, 'postcode' => $post);
                DB::table('client_location')->insert($values);
            }
        }*/
        //echo '<pre>'; print_r($location);
        //exit;
		
		
		
        
       /* if ($request->hasFile('image')) {
            for ($i = 0;$i < count($_FILES['image']);$i++) 
			{
                $name = $file_size = $file_type = $temp = $target_file = $logicalpath = '';
                $file_size = @$_FILES['image']['size'][$i];
                $file_type = @$_FILES['image']['type'][$i];
                $temp = @$_FILES['image']['tmp_name'][$i];
                if (@$_FILES['image']['size'][$i] > 0) {
                    $path_parts = pathinfo($_FILES['image']['name'][$i]);
                    $extension = $path_parts['extension'];
                    $filename = strtolower($path_parts['filename']);
                    $altfilename = GeneralMethod::getValidUploadFileName($filename) . "-" . $timeStamp . "." . $extension;
                    $dirArr = explode("/", $directoryPath);
                    $target_file = $directoryPath . '/' . $altfilename;
                    $allowed = array('doc', 'docx', 'pdf', 'png', 'jpeg', 'jpg', 'DOC', 'DOCX', 'PDF', 'PNG', 'JPEG', 'JPG');
                    $ext = pathinfo($_FILES['image']['name'][$i], PATHINFO_EXTENSION);
                    //$hashname 		= Files::upload($_FILES['image']['name'][$i], $target_file, null, null, false);
                    
                }
                if (in_array($ext, $allowed) && ($file_size < $maxsize)) {
                    $logicalpath = substr($target_file, strpos($target_file, 'user-uploads'));
                    if (!empty($company_id) && !empty($client_id) && !empty($altfilename) && !empty($target_file) && !empty($logicalpath) && !empty($file_type) && !empty($file_size)) {
                        if (move_uploaded_file($temp, $target_file)) {
                            $values = array('company_id' => @$company_id, 'client_id' => @$client_id, 'file_name' => @$altfilename, 'file_physical_path' => @$target_file, 'file_logical_path' => @$logicalpath, 'file_type' => @$file_type, 'file_size' => @$file_size);
                            //echo '<pre>'; print_r($values); exit;
                            DB::table('client_file')->insert($values);
                        }
                    }
                }
            }
        }*/
		
		
		
        
    }
	
	
	
	
	
	
	
	
    public function show($id) {
        //
        
    }
	
    public function edit($id) {
	
		abort_if(! $this->user->cans('edit_clients'), 403);
		
		$this->page_event	=	'EDIT';
		
		// abort_if(! $this->user->cans('edit_category'), 403);
		/* $this->category = JobCategory::findOrFail($id);
		return view('admin.client.edit', $this->data);*/
		
        $this->client 			= Client::find($id);
        $this->client_file 		= DB::table('client_file')->select('*')->where('client_id', $id)->get();
        $this->countries 		= Country::all();
        $this->client_location 	= DB::table('client_location')
									->join('countries', 'countries.id', '=', 'client_location.country_id')
									->select(
            'client_location.city', 'client_location.city_id', 'client_location.address', 'client_location.postcode', 'countries.country_name', 'client_location.country_id', 'client_location.state'
)
									->where('client_location.client_id', $id)
									->get();
									
        $this->client_contact_info = DB::table('client_contact_info')
										->select('*')
										->where('client_id', $id)
										->get();
										
      //  return view('admin.client.edit', $this->data); 
		
		$this->countries 		= Country::all();
		$this->categories 		= JobCategory::all();
		$this->companyType		= GeneralMethod::companyType();
		$this->department		= GeneralMethod::department();
		$this->countryArr 		= GeneralMethod::getAllCountryList();		 
	    $this->statelist 		= GeneralMethod::statelist(101);
		
		
		$statelist 					=	DB::table('state_list')
										->select('state_list.id', 'state_list.state as name', DB::raw("101 as country_id"))
										->orderBy('state_list.state')
										->get()->toArray();
		$this->indianStatelist       = json_decode(json_encode($statelist), true);
		
        return view('admin.client.create', $this->data);
		
    }
	
    public function update1(StoreJobCategory $request, $id) {
        // abort_if(! $this->user->cans('edit_category'), 403);
        $category = JobCategory::find($id);
        $category->name = $request->name;
        $category->save();
        return Reply::redirect(route('admin.client.index'), __('menu.jobCategories') . ' ' . __('messages.updatedSuccessfully'));
    }
	
	
    public function update(Request $request, $id) 
	{
    	
		abort_if(! $this->user->cans('edit_clients'), 403);
			
	   
		$company_id 	= $request->user()->company_id;
		 $client = Client::find($id);
		// FIRST TAB-1       
        if (!empty($request->name)) {
            $client->name 	= $request->name;
        }
		if (!empty($request->website)) {
            $client->website = $request->website;
        }
		if (!empty($request->industry)) {
            $client->industry = $request->industry;
        }
		if (!empty($request->company_sizes)) {
            $client->company_sizes = $request->company_sizes;
        }
		if (isset($request->company_type) && $request->company_type !== '') {
            $client->company_type = $request->company_type;
        }
		if (!empty($request->founded)) {
            $client->founded = $request->founded;
        }
		// SECOND TAB-2
		if (!empty(@$request->description)) {
			$client->description = @$request->description; 
			//$client->detail = @$request->detail; 
		}
		if (!empty(@$request->tags)) {
			$tags 			=	@$request->tags;
			$tags 			=	json_decode($tags, true);
			$tags 			=	array_column($tags, 'value');
			$tags 			=	implode(',', $tags);
			$client->tags 	=	$tags;
		}
		 $client->save();
		
		
		 $client_id = $id;
		 
		// THIRD TAB-3
		$addressId = @$request->addressId;
		DB::table('client_location')->where('client_id', $client_id)->delete();  
		for($i=0; $i<=$addressId; $i++)
		{
			$address 			= @$request->address[$i];
			$country 			= @$request->country[$i];
			$state 				= @$request->state[$i];
			$city 				= @$request->city[$i];	
			$postcode 			= @$request->postcode[$i];	
			
			if(!empty($country) && !empty($address) && !empty($postcode) && !empty($state))
			{
					$city_id 			= $city = '';
					if($country == 101){
						$city_id 			= @$request->city_id[$i];	
					}else{
						$city 				= @$request->city[$i];	
					}
					
				$values 				= array(
													'client_id' 	=> $client_id, 
													'country_id' 	=> $country, 
													'city' 			=> $city,
													'city_id' 		=> $city_id, 
													'address' 		=> $address, 
													'postcode' 		=> $postcode, 
													'state' 		=> $state
												);
				DB::table('client_location')->insert($values);	
			}
			
		}
				
		// FORTH TAB-4
		 DB::table('client_contact_info')->where('client_id', $client_id)->delete();  
		$contactPersonId = @$request->contactPersonId;
		for($j=0; $j<=$contactPersonId; $j++)
		{
					$cp_name 			= @$request->cp_name[$j];
					$cp_email 			= @$request->cp_email[$j];
					$cp_number 			= @$request->cp_number[$j];
					$cp_desigination 	= @$request->cp_desigination[$j];						
					
					if(!empty($cp_name) && !empty($cp_email) && !empty($cp_number) && !empty($cp_desigination))
					{
						$values 			= array(
														'client_id' 					=> $client_id, 
														'contact_person_name' 			=> $cp_name, 
														'contact_person_email' 			=> $cp_email, 
														'contact_person_number' 		=> $cp_number, 
														'contact_person_designation' 	=> $cp_desigination
													);
						DB::table('client_contact_info')->insert($values);
					}
				
		}
		
			
		$maxsize 			= 5242880;
        $directoryPath 		= GeneralMethod::document_manage_directory($company_id);	
		$timeStamp 			= time();

		if ($request->hasFile('logo')) 
		{
			$name = $file_size = $file_type = $temp = $target_file = $logicalpath = '';
			$file_size 		= @$_FILES['logo']['size'];
			$file_type 		= @$_FILES['logo']['type'];
			$temp 			= @$_FILES['logo']['tmp_name'];
			
			$path_parts 	= pathinfo($_FILES['logo']['name']);
			$extension 		= $path_parts['extension'];
			$filename 		= strtolower($path_parts['filename']);
			$altfilename 	= GeneralMethod::getValidUploadFileName($filename) . "-" . $timeStamp . "." . $extension;
			$dirArr 		= explode("/", $directoryPath);
			$target_file 	= $directoryPath . '/' . $altfilename;
			$allowed 		= array('doc', 'docx', 'pdf', 'png', 'jpeg', 'jpg', 'DOC', 'DOCX', 'PDF', 'PNG', 'JPEG', 'JPG');
			$ext 			= pathinfo($_FILES['logo']['name'], PATHINFO_EXTENSION);          
		    if (in_array($ext, $allowed)) 
			{
			  $logicalpath = substr($target_file, strpos($target_file, 'user-uploads'));
			  if (move_uploaded_file($temp, $target_file)) {
                            							
							$client 					= Client::find($client_id);
							$client->logo				= @$altfilename;	
							$client->logo_physical_path	= @$target_file;	
							$client->logo_logical_path	= @$logicalpath;	
							$client->save(); 		
				}						
			}						 
        }
		
       /* if (!empty($request->email)) {
            //$client->email 		= $request->email;
            
        }
        if (!empty($request->contact_person_name)) {
            $client->contact_person_name = $request->contact_person_name;
        }
        if (!empty($request->contact_number)) {
            $client->contact_number = $request->contact_number;
        }
       
        if (!empty(@$request->payout)) {
            $client->payout = @$request->payout;
        }
        if (!empty(@$request->commission)) {
            $client->commission = @$request->commission;
        }
        if (!empty($request->detail)) {
            $client->detail = $request->detail;
        }
        if (!empty($request->feedback)) {
            $client->feedback = $request->feedback;
        }
        $client->save();*/
       /*
        $cp_name = $cp_email = $cp_number = $cp_designation = '';
        $cp_name = $request->cp_name;
        $cp_email = $request->cp_email;
        $cp_number = $request->cp_number;
        $cp_designation = $request->cp_designation;
        if (!empty($cp_name)) {
            for ($i = 0;$i < count($cp_name);$i++) {
                $name = $number = $email = $designation = '';
                $name = $cp_name[$i];
                $email = $cp_email[$i];
                $number = $cp_number[$i];
                $designation = $cp_designation[$i];
                if ($client_id != '' && $name != '' && $email != '' && $number != '' && $designation != '') {
                    $values = array('client_id' => $client_id, 'contact_person_name' => $name, 'contact_person_email' => $email, 'contact_person_number' => $number, 'contact_person_designation' => $designation);
                    DB::table('client_contact_info')->insert($values);
                }
            }
        }*/

       /* $address = $pincode = $country_id = $city = '';
        $address = $request->address;
        $postcode = $request->postcode;
        $country_id = $request->country_id;
        $city = $request->city;
        if (!empty($country_id)) {
            for ($i = 0;$i < count($country_id);$i++) {
                $cl_address = $post = $country = $cl_city = '';
                $country = $country_id[$i];
                $cl_city = $city[$i];
                $cl_address = $address[$i];
                $post = $postcode[$i];
                if ($client_id != '' && $country != '' && $cl_city != '' && $post != '') {
                    $values = array('client_id' => $client_id, 'country_id' => $country, 'city' => $cl_city, 'address' => $cl_address, 'postcode' => $post);
                    DB::table('client_location')->insert($values);
                }
            }
        }*/
       /* $timeStamp = time();
        $company_id = $request->user()->company_id;
        $maxsize = 5242880;
        $directoryPath = GeneralMethod::document_manage_directory($company_id);
        if ($request->hasFile('image')) {
            for ($i = 0;$i <= count($_FILES['image']);$i++) {
                $name = $file_size = $file_type = $temp = $target_file = $logicalpath = '';
                $file_size = @$_FILES['image']['size'][$i];
                $file_type = @$_FILES['image']['type'][$i];
                $temp = @$_FILES['image']['tmp_name'][$i];
                if (@$_FILES['image']['size'][$i] > 0) {
                    $path_parts = pathinfo($_FILES['image']['name'][$i]);
                    $extension = $path_parts['extension'];
                    $filename = strtolower($path_parts['filename']);
                    $altfilename = GeneralMethod::getValidUploadFileName($filename) . "-" . $timeStamp . "." . $extension;
                    $dirArr = explode("/", $directoryPath);
                    $target_file = $directoryPath . '/' . $altfilename;
                    $allowed = array('doc', 'docx', 'pdf', 'png', 'jpeg', 'jpg', 'DOC', 'DOCX', 'PDF', 'PNG', 'JPEG', 'JPG');
                    $ext = pathinfo($_FILES['image']['name'][$i], PATHINFO_EXTENSION);
                }
                if (in_array($ext, $allowed) && ($file_size < $maxsize)) {
                    $logicalpath = substr($target_file, strpos($target_file, 'user-uploads'));
                    if (!empty($company_id) && !empty($id) && !empty($altfilename) && !empty($target_file) && !empty($logicalpath) && !empty($file_type) && !empty($file_size)) {
                        if (move_uploaded_file($temp, $target_file)) {
                            $values = array('company_id' => $company_id, 'client_id' => $id, 'file_name' => $altfilename, 'file_physical_path' => $target_file, 'file_logical_path' => $logicalpath, 'file_type' => $file_type, 'file_size' => $file_size);
                            DB::table('client_file')->insert($values);
                        }
                    }
                }
            }
        }*/
        return Reply::redirect(route('admin.clients.index'), __('menu.clients') . ' ' . __('messages.updatedSuccessfully'));
    }
	
	
	
	
	
    public function destroy($id) {
        // abort_if(! $this->user->cans('delete_category'), 403);
        /*  JobCategory::destroy($id);
         return Reply::success(__('messages.recordDeleted'));*/
        Client::destroy($id);
        return Reply::success(__('messages.recordDeleted'));
    }
    public function deleterecord($id) {
        $client_file = DB::table('client_file')->select('*')->where('id', $id)->first();
        if (file_exists($client_file->file_logical_path)) {
            unlink(@$client_file->file_logical_path);
        }
        DB::table('client_file')->where('id', $id)->delete();
        return Reply::success(__('messages.recordDeleted'));
    }
    public function getSkills111($categoryId) {
        $jobSkills = '';
        $skills = Skill::where('category_id', $categoryId)->get();
        foreach ($skills as $skill) {
            $jobSkills.= '<option selected value="' . $skill->id . '">' . ucwords($skill->name) . '</option>';
        }
        return Reply::dataOnly(['status' => 'success', 'data' => $jobSkills]);
    }
    public function data1111() {
        // abort_if(! $this->user->cans('view_category'), 403);
        $categories = JobCategory::all();
        return DataTables::of($categories)->addColumn('action', function ($row) {
            $action = '';
            if ($this->user->cans('edit_category')) {
                $action.= '<a href="' . route('admin.job-categories.edit', [$row->id]) . '" class="btn btn-sm"
                      data-toggle="tooltip" data-original-title="' . __('app.edit') . '"><i class="fa fa-pencil text-primary" aria-hidden="true"></i></a>';
            }
            if ($this->user->cans('delete_category')) {
                $action.= ' <a href="javascript:;" class="btn btn-sm sa-params"
                      data-toggle="tooltip" data-row-id="' . $row->id . '" data-original-title="' . __('app.delete') . '"><i class="fa fa-times text-danger" aria-hidden="true"></i></a>';
            }
            return $action;
        })->editColumn('name', function ($row) {
            return ucfirst($row->name);
        })->addIndexColumn()->make(true);
    }
    
	
	public function data() {
        
		abort_if(! $this->user->cans('view_clients'), 403);
		
		$department		= GeneralMethod::department();
		
		
		
        $client = Client::select('*');
        if (\request('search') != "") {
            $search = request('search');
            $client->where('clients.name', 'LIKE', "%$search%");
        }
        $client->where('clients.company_id', '=', $this->user->company_id);
        $client->where('clients.self_company', '!=', 1);
        return DataTables::of($client)
		->addColumn('action', function ($row) {
            $action = '';
            //if( $this->user->cans('edit_client')){
            $action.= '<a href="' . route('admin.clients.edit', [$row->id]) . '" class="btn"
						data-toggle="tooltip" data-placement="right" data-original-title="' . __('app.edit') . '"><i class="fa fa-pencil text-primary" aria-hidden="true"></i></a>';
            //}
            if ($row->self_company != 1) {
                //if( $this->user->cans('delete_client')){
                $action.= ' <a href="javascript:;" class="btn sa-params"
						data-toggle="tooltip" data-row-id="' . $row->id . '" data-original-title="' . __('app.delete') . '"><i class="fa fa-times text-danger" aria-hidden="true"></i></a>';
                //}
                
            }
            return $action;
        })
		->editColumn('name', function ($row) {
            return ucfirst($row->name);
        })
		->addColumn('contact_person_name', function ($row) {
            $contactHtml = '';
            $contactInfo = DB::table('client_contact_info')->select('*')->where('client_id', $row->id)->get();
            $contactHtml = '<div class="text-center tooltip"><i class="fa fa-users fa-sm text-custom" aria-hidden="true"></i><span class="tooltiptext">';
            foreach ($contactInfo as $info) {
                $action = '';
                if ($this->user->can('edit_client')) {
                    $action.= '<a href="' . route('admin.clients.edit_information', [$info->id]) . '" class="btn"
						data-toggle="tooltip" data-original-title="' . __('app.edit') . '"><i class="flaticon2-pen text-white" aria-hidden="true"></i></a>';
                }
                if ($this->user->can('delete_client')) {
                    $action.= ' <a href="javascript:;" class="btn client-user-delete"
						data-toggle="tooltip" data-row-id="' . $info->id . '" data-original-title="' . __('app.delete') . '"><i class="fa fa-times text-danger" aria-hidden="true"></i></a>';
                }
                $contactHtml.= '
												<span class="font-weight-600"><i class="fa fa-user"></i>&nbsp;</span>&nbsp;' . $info->contact_person_name . '<br>
												<span class="font-weight-600"><i class="fa fa-envelope"></i>&nbsp;</span>&nbsp;' . $info->contact_person_email . '<br>
												<span class="font-weight-600"><i class="fa fa-phone"></i>&nbsp;</span>&nbsp;' . $info->contact_person_number . '<br>
												<span class="font-weight-600"><i class="fa fa-briefcase"></i>&nbsp;</span>&nbsp;' . @$info->contact_person_designation . '<br> 
												' . $action . '<br>
												';
            }
            $contactHtml.= '</span></div><br>';
            return $contactHtml;
        })
		->editColumn('location', function ($row) {
            $locationHtml 	= '';
            $locInfo 		= DB::table('client_location')
								->join('countries', 'countries.id', '=', 'client_location.country_id')
								->leftJoin('city_list', 'city_list.city_id', '=', 'client_location.city_id')
								->select(
                                'client_location.id', 'client_location.city', 'client_location.address', 'client_location.postcode', 'countries.country_name',
                                'client_location.country_id', 'city_list.city as city_name'
                                )
								->where('client_id', $row->id)
								->get();
								
            $locationHtml.= '<div class="tooltip"><i class="fa fa-map-marker fa-sm text-custom" aria-hidden="true"></i><span class="tooltiptext"> ';
            foreach ($locInfo as $info) {
                $action = '';
                if ($this->user->can('edit_client')) {
                    $action.= '<a href="' . route('admin.clients.edit_location', [$info->id]) . '" class="btn"
							data-toggle="tooltip" data-original-title="' . __('app.edit') . '"><i class="flaticon2-pen text-white" aria-hidden="true"></i></a>';
                }
                if ($this->user->can('delete_client')) {
                    $action.= ' <a href="javascript:;" class="btn client-location-delete"
							data-toggle="tooltip" data-row-id="' . $info->id . '" data-original-title="' . __('app.delete') . '"><i class="fa fa-times text-danger" aria-hidden="true"></i></a>';
                }
				
				$city			= @$info->city;
				if(@$info->country_id == 101){
					$city			= @$info->city_name;		
				}
				
                $locationHtml.= '														
													<span class="font-weight-600"><i class="fa fa-map-marker"></i>&nbsp;Address :</span>&nbsp;' . @$info->address . '<br>
													<span class="font-weight-600"><i class="fa fa-codepen"></i>&nbsp;City :</span>&nbsp;' . @$city .@$info->country_id. '<br>
													<span class="font-weight-600"><i class="fa fa-map-pin"></i>&nbsp;Postcode :</span>&nbsp;' . @$info->postcode . '<br>
													<span class="font-weight-600"><i class="fa fa-globe"></i>&nbsp;Country :</span>&nbsp;' . @$info->country_name . 'aaaa<br>									
													' . $action . '<br>														
													';
            }
            $locationHtml.= '</span></div><br>';
            return $locationHtml;
        })
		->editColumn('open_jobs', function ($row) {
            $client_id = '';
            $client_id = $row->id;
            $jobCounter = DB::table('jobs')->select('*')->where('client_id', $client_id)->where('status', '=', 'active')->count();
            return $jobCounter;
        })
		->editColumn('closed_jobs', function ($row) {
            $client_id = '';
            $client_id = $row->id;
            $jobCounter = DB::table('jobs')->select('*')->where('client_id', $client_id)->where('status', '=', 'inactive')->count();
            return $jobCounter;
        })
		->editColumn('hold_jobs', function ($row) {
            $client_id = '';
            $client_id = $row->id;
            $jobCounter = DB::table('jobs')->select('*')->where('client_id', $client_id)->where('status', '=', 'hold')->count();
            return $jobCounter;
        })
		->editColumn('total_jobs', function ($row) {
            $client_id = '';
            $client_id = $row->id;
            $jobCounter = DB::table('jobs')->select('*')->where('client_id', $client_id)->count();
            return $jobCounter;
        })
		->editColumn('commission  ', function ($row) {
            return ucfirst($row->commission);
        })
		->editColumn('payout   ', function ($row) {
            return ucfirst($row->payout);
        })
		->editColumn('chkbox', function ($row) {
            return '<div class="form-check form-check-sm form-check-custom form-check-solid">
                    <input class="form-check-input" type="checkbox" value="1">
                  </div>';
        })		
		->editColumn('Name', function ($row) use($department) {      
			//function ($row) use($department) {
            $bgTextDecore = $strVal = '';
            $str = trim(@$row->name);
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
			
			$image					=	'<a href="javascript:void(0);" data-row-id="' . $row->id . '" class="show-detail border-radius-5" id="kt_activities3_toggle" >
                      				<div class="symbol-label fs-3 ' . @$bgTextDecore . '  border-radius-5">' . ucwords(@$strVal) . '</div>
                    			</a>';
			$background_image		=	"";
			if(!empty(@$row->logo_logical_path) && strlen(@$row->logo_logical_path)>10){
				$background_image		=  url('public/'.@$row->logo_logical_path); 	
				$image					=	'<img src="'.@$background_image.'" height="100px" width="100px" class="show-detail border-radius-5"  />';
			} 
					

				  
			$departmentArr		=	'------';	
			if($row->industry >= 0)
			{							
				$departmentArr		=	$department[@$row->industry];
			}	
			
			//$departmentArr		=	@$row->industry;						  
            return '<div class="symbol symbol-circle symbol-40px overflow-hidden me-2 border-radius-5" style="float:left" >
                   		'.$image.'		 
                  		</div>                 
                  <div class="d-flex flex-column">
                    <a href="javascript:void(0);" data-row-id="' . $row->id . '" class="text-dark fw-bolder text-hover-primary fs-6 show-detail"  data-toggle="tooltip1" data-html="true" title="' . ucwords(@$row->name) . '">' . ucwords(substr(@$row->name, 0, 15)) . '</a>
                    <a href="javascript:void(0);" data-row-id="' . $row->id . '" class="text-muted fw-bold text-muted d-block fs-7 text-hover-primary show-detail">'.@$departmentArr.'</a>
                  </div>
                  ';
        })
		->editColumn('Location', function ($row) {
            $locationHtml 	= '';
            $locInfo 		= DB::table('client_location')
								//->join('countries', 'countries.id', '=', 'client_location.country_id')
								->select(
'client_location.id', 'client_location.city', 'client_location.address', 'client_location.postcode', 'client_location.country_id',
'city_list.city as city_name'
)
->leftJoin('city_list', 'city_list.city_id', '=', 'client_location.city_id')
								->where('client_id', $row->id)
								->get();
            $locationHtml.= '<div class="d-flex flex-column">';
            foreach ($locInfo as $info) {
				
				$country_name	=	"";
				if(@$info->country_id > 0){
									 $country_name	= GeneralMethod::getCountryName( $info->country_id );	
									 foreach($country_name as $cval){
									 	$country_name	= ucwords($cval['name']);	
									 }
									 
								}
				
				$city			= @$info->city;
				if(@$info->country_id == 101){
					$city			= @$info->city_name;		
				}			
                $locationHtml = '<a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">' . ucwords(substr(@$info->address, 0, 10)) . '</a>
														<div class="d-flex align-items-center"> 
															<span class="fw-bold d-block fs-7">' . ucwords(substr(@$city, 0, 10)) . '</span> 
																&nbsp;|&nbsp; 
															<span class="fw-bold d-block fs-7">' . @$country_name . '</span>
															<div></div>
														</div>';
            }
            $locationHtml.= '</div>';
            return $locationHtml;
            /* return '<div class="d-flex flex-column">
            <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">Gurgaon</a>
            <div class="d-flex align-items-center">
            <span class="fw-bold d-block fs-7">Haryana</span>
            &nbsp;|&nbsp;
            <span class="fw-bold d-block fs-7">INDIA</span>
            <div></div>
            </div>
            </div>
            ';*/
        })
		->editColumn('Joining', function ($row) {
            $client_id = '';
            $client_id = $row->id;
            $applicationCounter = DB::table('jobs')->join('job_applications', 'job_applications.job_id', '=', 'jobs.id')->select(DB::raw("count(job_applications.id) as allCandidate"), DB::raw("SUM(CASE WHEN job_applications.master_status_id = 6  THEN 1 ELSE 0 END) AS appJoin"))->where('jobs.client_id', $client_id)
            //->where('status', '=','active')
            ->first();
            return '<div class="d-flex flex-column">
                    <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">' . number_format(@$applicationCounter->appJoin) . '</a>                    
                    <span class="fw-bold d-block fs-7">' . number_format(@$applicationCounter->allCandidate) . '</span>
                  </div>';
        })
		->editColumn('Requisitions', function ($row) {
            $client_id = '';
            $client_id = $row->id;
            $jobCounter = DB::table('jobs')->select(DB::raw("SUM(CASE WHEN jobs.status = 'active' THEN 1 ELSE 0 END) AS active"), DB::raw("SUM(CASE WHEN jobs.status = 'inactive' THEN 1 ELSE 0 END) AS inactive"), DB::raw("SUM(CASE WHEN jobs.status = 'hold' THEN 1 ELSE 0 END) AS hold"))->where('client_id', $client_id)
            //->where('status', '=','active')
            ->first();
            $active_counter = 0;
            if ($jobCounter->active > 0) {
                $active_counter = $jobCounter->active;
            }
            $inactive_counter = 0;
            if ($jobCounter->active > 0) {
                $inactive_counter = $jobCounter->active;
            }
            $hold_counter = 0;
            if ($jobCounter->active > 0) {
                $hold_counter = $jobCounter->active;
            }
            return '<div class="d-flex flex-column">
                      <span class="fw-bolder text-success fs-6">' . @$active_counter . ' Active</span>
                      <div class="d-flex align-items-center">
                        <span class="fw-bold d-block fs-7 text-warning">' . @$hold_counter . ' Hold</span>
                          &nbsp;|&nbsp;
                        <span class="fw-bold d-block fs-7 text-danger">' . @$inactive_counter . ' Closed</span>
                      <div>
                    </div>
                </div></div>';
        })
		->editColumn('Connected', function ($row) {
            $daysAgo = '';
            $created_at = $row->created_at->format('d M, Y');
            //$daysAgo		=	GeneralMethod::time_elapsed_string($row->created_at);
            $daysAgo = GeneralMethod::timeAgo($row->created_at);
            //echo '<pre>'; print_r($daysAgo); exit;
            return '<div class="d-flex flex-column">
                    <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">' . @$created_at . '</a>
                    <a href="javascript:void(0);" class="text-muted fw-bold text-muted d-block fs-7 text-hover-primary">' . @$daysAgo . '</a>
                  </div>';
        })
		->editColumn('Client', function ($row) {
            return '<div class="d-flex flex-column">
                    <a href="javascript:void(0);" class="text-dark fw-bolder fs-6">Client</a>
                    <span class="text-muted fw-bold text-muted d-block fs-7">Product Based</span>
                  </div>';
        })
		->editColumn('Contact', function ($row) {
            $contactHtml = '';
            $contactInfo = DB::table('client_contact_info')->select('*')->where('client_id', $row->id)->get();
            $contactHtml = '<div class="d-flex flex-column">
                    					<div class="symbol-group symbol-hover">';
            foreach ($contactInfo as $info) {
                $contactHtml.= '<div class="symbol symbol-30 symbol-circle" data-bs-placement="top" data-toggle="tooltip2" data-html="true" title="' . ucwords(@$info->contact_person_name) . '" >
                            <img alt="Pic" src="https://preview.keenthemes.com//metronic/theme/html/demo1/dist/assets/media/users/300_25.jpg">
                          </div>';
            }
            $contactHtml.= '</div>
                    			</div>';
            return $contactHtml;
        })
		->rawColumns(['chkbox', 'Name', 'Location', 'Joining', 'Requisitions', 'Connected', 'Client', 'Contact'])
        /*-> rawColumns(['action', 'contact_person_name', 'location', 'open_jobs', 'closed_jobs', 'hold_jobs', 'total_jobs'])*/->addIndexColumn()->make(true);
    }
   
    public function download($id) {
        $client_file = DB::table('client_file')->select('*')->where('id', $id)->first();
        //echo '<pre>'; print_r($client_file); exit;
        //$client_file->file_logical_path
        // $jobApplication = JobApplication::findOrFail($id);
        if (!file_exists($client_file->file_physical_path)) {
            abort(404);
        }
        return response()->download($client_file->file_physical_path);
    }
    
	public function deleteUser(Request $request) {
        $id = '';
        $id = $request->id;
        if ($id > 0) {
            DB::table('client_contact_info')->where('id', $id)->delete();
            return Reply::success(__('messages.recordDeleted'));
        }
    }
   
    public function deleteAddress(Request $request) {
        $id = '';
        $id = $request->id;
        if ($id > 0) {
            DB::table('client_location')->where('id', $id)->delete();
            return Reply::success(__('messages.recordDeleted'));
        }
    }
   
    public function edit_information($id) {
        //abort_if(! $this->user->can('edit_skills'), 403);
        // $this->client 				= Client::find($id);
        $this->cc_info = DB::table('client_contact_info')->select('*')->where('id', $id)->first();
        // echo '<pre>'; print_r($this->client_contact_info);
        // $this->countries 			= Country::all();
        /*$this->client_location 		= DB::table('client_location')
        		->join('countries', 'countries.id', '=', 'client_location.country_id')
        		->select('client_location.city', 'client_location.address', 'client_location.postcode', 'countries.country_name')
        		->where('client_location.client_id', $id)
        		->get();
        $this->client_contact_info 	= DB::table('client_contact_info')->select('*')->where('client_id', $id)->get();*/
        return view('admin.client.edit_information', $this->data);
    }
	
    public function edit_location($id) {
        //abort_if(! $this->user->can('edit_skills'), 403);
        // $this->client 				= Client::find($id);
        $this->cl_info = DB::table('client_location')->join('clients', 'clients.id', '=', 'client_location.client_id')->select('clients.name as client_name', 'client_location.id', 'client_location.address', 'client_location.city', 'client_location.postcode', 'client_location.country_id')->where('client_location.id', $id)->first();
        //echo '<pre>'; print_r($this->cl_info);
        $this->countries = Country::all();
        /*$this->client_location 		= DB::table('client_location')
        		->join('countries', 'countries.id', '=', 'client_location.country_id')
        		->select('client_location.city', 'client_location.address', 'client_location.postcode', 'countries.country_name')
        		->where('client_location.client_id', $id)
        		->get();
        $this->client_contact_info 	= DB::table('client_contact_info')->select('*')->where('client_id', $id)->get();*/
        return view('admin.client.edit_location', $this->data);
    }
	
    public function update_info(Request $request, $id) {
        //abort_if(! $this->user->can('edit_skills'), 403);
        $updateArr = array('contact_person_name' => @$request->contact_person_name, 'contact_person_email' => @$request->contact_person_email, 'contact_person_number' => @$request->contact_person_number, 'contact_person_designation' => @$request->contact_person_designation);
        DB::table('client_contact_info')->where('id', @$id)->limit(1)->update($updateArr);
        return Reply::redirect(route('admin.clients.index'), __('menu.clients') . ' ' . __('messages.updatedSuccessfully'));
    }
	
    public function update_loc(Request $request, $id) {
        // abort_if(! $this->user->can('edit_skills'), 403);
        $updateArr = array('address' => @$request->address, 'city' => @$request->city, 'postcode' => @$request->postcode, 'country_id' => @$request->country_id);
        DB::table('client_location')->where('id', @$id)->limit(1)->update($updateArr);
        return Reply::redirect(route('admin.clients.index'), __('menu.clients') . ' ' . __('messages.updatedSuccessfully'));
    }
	
    public function usermore(Request $request) {
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
        if ($client_id != '' && $contact_person_name != '' && $contact_person_email != '' && $contact_person_number != '' && $contact_person_designation != '') {
            if (!filter_var($contact_person_email, FILTER_VALIDATE_EMAIL)) {
                return Reply::dataOnly(['status' => 'error', 'message' => "Please enter proper information."]);
                exit;
            }
            $insertArr = array('client_id' => $client_id, 'contact_person_name' => $contact_person_name, 'contact_person_email' => $contact_person_email, 'contact_person_number' => $contact_person_number, 'contact_person_designation' => $contact_person_designation);
            DB::table('client_contact_info')->insert($insertArr);
            $insertedId = DB::getPdo()->lastInsertId();
            if ($insertedId > 0) {
                $option = array('id' => $insertedId, 'value' => ucwords($contact_person_name));
                return Reply::dataOnly(['status' => 'success', 'message' => "Contact person information added successfully.", 'option' => $option]);
            } else {
                return Reply::dataOnly(['status' => 'error', 'message' => "Please enter proper information."]);
            }
        } else {
            return Reply::dataOnly(['status' => 'error', 'message' => "Please enter proper information."]);
        }
    }
	
    public function addressmore(Request $request) {
        //echo '<pre>'; print_r($_REQUEST); exit;
        $client_id = '';
        $client_id = $request->client_id;
        $country_id = '';
        $country_id = $request->country_id;
        $city = '';
        $city = $request->city;
        $address = '';
        $address = $request->address;
        $postcode = '';
        $postcode = $request->postcode;
        if ($client_id != '' && $country_id != '' && $city != '' && $address != '' && $postcode != '') {
            $insertArr = array('client_id' => $client_id, 'company_id' => $this->user->company_id, 'city' => $city, 'address' => $address, 'postcode' => $postcode, 'country_id' => $country_id);
            DB::table('client_location')->insert($insertArr);
            $insertedId = DB::getPdo()->lastInsertId();
            if ($insertedId > 0) {
                $country_row = DB::table('countries')->select('*')->where('id', $country_id)->first();
                /*$option 		= '<option   value="'.$insertedId.'">'.ucwords($address).'&nbsp;-&nbsp;'.ucwords($city).'&nbsp;-&nbsp;'.ucwords($postcode).'&nbsp;-&nbsp;'.ucwords($country_row->country_name).'</option>';*/
                $option = array('id' => $insertedId, 'address' => ucwords($address), 'city' => $city, 'postcode' => $postcode, 'country_name' => ucwords($country_row->country_name));
                return Reply::dataOnly(['status' => 'success', 'message' => "Office address added successfully.", 'option' => $option]);
            } else {
                return Reply::dataOnly(['status' => 'error', 'message' => "Please enter proper information."]);
            }
        } else {
            return Reply::dataOnly(['status' => 'error', 'message' => "Please enter proper information."]);
        }
    }
	
	
    public function export($start, $length) {
        $filters = ['start' => $start, 'length' => $length + 1];
        $data = ['company' => $this->companyName, 'company_id' => $this->user->company_id];
        // echo '<pre>'; print_r($filters ); exit;
        return Excel::download(new ClientExport($filters, $data), 'client_list.xlsx', ExcelExcel::XLSX);
    }
	
	
	
    public function viewMore($id) {
	
		abort_if(! $this->user->cans('view_clients'), 403);
	
        /*$this->job 					=	Job::findOrFail($id);
        // Company Data
        $company_arr				=	DB::table('companies')->select('*')->where('id', $this->job->company_id)->first();
        $this->company_name			=	@$company_arr->company_name;
        $this->category_name		=	'N/A';
        
        if($this->job->category_id > 0)
        {
        $cat_arr				=	DB::table('job_categories')->select('*')->where('id', $this->job->category_id)->first();
        $this->category_name	=	@$cat_arr->name;
        }
        
        $this->client_name			=	'N/A';
        
        if($this->job->client_id > 0)
        {
        $client_arr				=	DB::table('clients')->select('*')->where('id', $this->job->client_id)->first();
        $this->client_name		=	@$client_arr->name;
        }
        
        $this->qualification		=	'N/A';
        
        if($this->job->qualification > 0)
        {
        $qualification_Arr		=	DB::table('qualification_master')->select('*')->where('id', $this->job->qualification)->first();
        $this->qualification	=	@$qualification_Arr->name;
        }
        
        $this->location	=	'';
        
        if($this->job->id > 0)
        {
        
        $this->jobLocation	=	DB:: table('client_location_jobs')
        			->join('client_location', 'client_location.id', '=', 'client_location_jobs.client_location_id')
        			->join('countries', 'countries.id', '=', 'client_location.country_id')
        			->select('client_location.city', 'client_location.address', 'client_location.postcode', 'countries.country_name')
        			->where('client_location_jobs.job_id', $this->job->id)
        			->orderBy('client_location_jobs.id', 'DESC')
        			->get();
        
        $this->jobLocationCount	=	DB:: table('client_location_jobs')
        			->join('client_location', 'client_location.id', '=', 'client_location_jobs.client_location_id')
        			->join('countries', 'countries.id', '=', 'client_location.country_id')
        			->select('client_location.city', 'client_location.address', 'client_location.postcode', 'countries.country_name')
        			->where('client_location_jobs.job_id', $this->job->id)
        			->orderBy('client_location_jobs.id', 'DESC')
        			->count();							
        
        }		*/
        /*$this->location	=	DB:: table('client_location_jobs')
        	->join('client_location', 'client_location.id', '=', 'client_location_jobs.client_location_id')
        	->join('countries', 'countries.id', '=', 'client_location.country_id')
        	->select('client_location.city', 'client_location.address', 'client_location.postcode', 'countries.country_name')
        	->where('client_location_jobs.job_id', $this->job->id)
        	->orderBy('client_location_jobs.id', 'DESC')
        	->toSql();
        
        
        $this->client_loc_arr = DB::table('client_location_jobs')
        ->join('client_location', 'client_location.id', '=', 'client_location_jobs.client_location_id')
        ->join('countries', 'countries.id', '=', 'client_location.country_id')
        ->select('client_location.address', 'client_location.city',  'client_location.postcode', 'countries.country_name')
        ->where('client_location_jobs.job_id', $this->job->id)
        ->get();*/
		
		
		$this->countries 		= Country::all();
		$this->categories 		= JobCategory::all();
		$this->companyType		= GeneralMethod::companyType();
		$this->department		= GeneralMethod::department();
		$this->countryArr 		= GeneralMethod::getAllCountryList();		 
	    $this->statelist 		= GeneralMethod::statelist(101);
			
		
        $this->client 					= Client::find($id);
        $this->client_file 				= DB::table('client_file')->select('*')->where('client_id', $id)->get();
       // $this->countries 		= Country::all();
       
	    $this->client_location 			= DB::table('client_location')
											//->join('countries', 'countries.id', '=', 'client_location.country_id')
											->select('client_location.city', 'client_location.city_id', 'client_location.address', 'client_location.postcode', 'client_location.country_id')
											->where('client_location.client_id', $id)
											->orderBy('client_location.id', 'DESC')
											->first();
		
		$this->city_name	=	'';
		if($this->client_location->country_id == 101)
		{
			$this->city_obj 				=	DB::table('city_list')->select('city_list.city')->where('city_list.city_id', $this->client_location->city_id)->first();
			$this->city_name				=	ucwords($this->city_obj->city);
		}
		else
		{
			$city							=	$this->client_location->city;
			$this->city_name				=	ucwords($city);	
		}
				
		$this->country_name	=	'';
		if($this->client_location->country_id > 0){
			$country_name	= GeneralMethod::getCountryName($this->client_location->country_id);	
			foreach($country_name as $cval){
				$this->country_name	= ucwords($cval['name']);	
			}
		}
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
        $this->client_contact_info 		= DB::table('client_contact_info')->select('*')->where('client_id', $id)->get();
		
		$this->active_requisition 		= DB::table('jobs')->select('jobs.id')->where('jobs.client_id', $id)->where('jobs.status', 'active')->count();
		
		//getCountryName
		
		//echo '<pre>'; print_r($this->client_contact_info); exit; 
							
        $view = view('admin.client.show', $this->data)->render();
        return Reply::dataOnly(['status' => 'success', 'view' => $view]);
    }
	
	public function addresslist($cid)
	{
		$this->countryArr 		=	GeneralMethod::getAllCountryList();		   
	    //$this->statelist 		=	GeneralMethod::statelist(101);
		$this->cid				=	$cid;
		$view = view('admin.client.addresslist', $this->data)->render();
        return Reply::dataOnly(['status' => 'success', 'data' => $view]);
	}
	
	
	
	
	
	
	
	
	
	public function contactdata(Request $request)
	{
	
		
		$clientId				=	'';
		$clientId				=	$_REQUEST['clientId'];
		//exit; 
       // abort_if(! $this->user->cans('view_schedule'), 403);

		$available_status		=	'';
		$available_status		=	GeneralMethod::available_status();
		$feedback_status		=	'';
		$feedback_status		=	GeneralMethod::feedback_status();
		$interview_mode_arr		=	'';
		$interview_mode_arr		=	GeneralMethod::interview_mode();

        $shedule = DB::table('client_contact_info')
		->select('client_contact_info.id', 'client_contact_info.contact_person_name', 'client_contact_info.contact_person_email', 'client_contact_info.contact_person_number', 'client_contact_info.contact_person_designation' )
		 ->where('client_contact_info.client_id', $request->clientId)
		 ->get();
		 
		 if ($request->jobs != 'all' && $request->jobs != '') {
            $jobApplications = $jobApplications->where('job_applications.job_id', $request->jobs);
        }
		
        return DataTables::of($shedule)		
			 ->editColumn('chkbox', function ($row) {
                    return '<div class="form-check form-check-sm form-check-custom form-check-solid">
                              <input class="form-check-input form-check-input2" type="checkbox" value="1" />
                            </div>';
            })
			->editColumn('Name', function ($row) {
				$bgTextDecore 	=	$strVal = '';
				$str 			=	trim(@$row->contact_person_name);
				$str 			=	$strVal = strtolower($str[0]);
				$str 			=	ord($str);
				if ($str >= 97 && $str <= 101) {
					$bgTextDecore = 'fs-3 bg-light-danger text-danger';
				} else if ($str >= 102 && $str <= 106) {
					$bgTextDecore = 'fs-3 bg-light-warning text-warning';
				} else if ($str >= 107 && $str <= 111) {
					$bgTextDecore = 'fs-3 bg-light-primary text-primary';
				} else if ($str >= 112 && $str <= 116) {
					$bgTextDecore = 'fs-3 bg-light-success text-success';
				} else if ($str >= 117 && $str <= 121) {
					$bgTextDecore = 'fs-3 bg-light-danger text-danger';
				} else {
					$bgTextDecore = 'fs-3 bg-light-secondary';
				}
							  
				  return '<div class="symbol symbol-circle symbol-40px overflow-hidden me-2 border-radius-5"  style="float:left"  >
                              <a href="javascript:void(0);">
                                <div class="symbol-label fs-3 '.$bgTextDecore.' border-radius-5">'.ucwords($strVal).'</div>
                              </a>
                            </div>
                            <div class="d-flex flex-column">
                              <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">'.ucwords(@$row->contact_person_name).'</a> 
                            </div>';
				  
            })
			->editColumn('Role', function ($row) {   
				  return '<div class="d-flex flex-column">
                              <span class="text-dark fw-bolder fs-6">'.@$row->contact_person_designation.'</span> 
                            </div>';
            })
			->editColumn('Contacts', function ($row) {                   
					return '<div class="d-flex flex-column">
                              <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">'.@$row->contact_person_number.'</a>
                              <a href="javascript:void(0);" class="text-muted fw-bold d-block fs-7 text-hover-primary">'.@$row->contact_person_email.'</a>
                            </div>';		
            })
			->editColumn('Status', function ($row) {                
					return '<span class="bg-light-success text-success fw-bolder fs-6">Active</span>';		
            })			
			
            ->rawColumns(['chkbox', 'Name', 'Role', 'Contacts', 'Status'])

            ->make(true);

    
	
	}
	
	
	public function citylist($state_id)
		{
								
					$Rawcitylist 				=	DB::table('city_list')
													->select('city_list.city_id as id', 'city_list.city as name')
													->where('city_list.state_id', $state_id)
													->orderBy('city_list.city')
													->get()->toArray();
					$citylist       			=	json_decode(json_encode($Rawcitylist), true);
				
				
				
				//echo '<pre>'; print_r($Rawcitylist);  exit; 
				
				$custom	=	'<option>Select City</option>';
				foreach($citylist as $sval){
					$custom	.=	'<option value="'.$sval['id'].'">'.$sval['name'].'</option>';	
				}
				
				return Reply::dataOnly(['status' => 'success', 'data' => $custom]);
				//echo '<pre>'; print_r($statelist); 	
		}
	
	
	public function client_request($id) {
	
        return view('admin.client.client_request', $this->data);
    }
	
	
	
	
	}
