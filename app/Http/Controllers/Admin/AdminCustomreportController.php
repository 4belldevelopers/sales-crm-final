<?php

namespace App\Http\Controllers\Admin;
use App\Helper\Files;
use App\Helper\GeneralMethod;
use App\Helper\Reply;
use App\Http\Requests\StoreSkill;
use App\JobCategory;
use App\Models\Client;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;
use App\Models\Country;
use App\Models\JobApplication;
use App\Models\Job;
use App\Models\User;
//use App\Client;
use App\Exports\InterviewExcelExport;
use App\Exports\ApplicationListExport;
use Maatwebsite\Excel\Excel as ExcelExcel;
use Maatwebsite\Excel\Facades\Excel;


class AdminCustomreportController extends AdminBaseController
{


    public function __construct()
    {

        parent::__construct();
        $this->pageTitle 	= __('Report');
        $this->pageIcon 	= 'icon-grid';

    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
   /* public function index(Request $request)
    {
        return view('admin.customreport.index', $this->data);
    }*/

	  public function index()
    	{

			abort_if(!$this->user->cans('view_report'), 403);

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

		//  $chkAdminRole = DB::table('roles')
		// 				->select('*')
		// 				->where('id', $this->user->role->role_id)
		// 				->where('name', 'admin')
		// 				->count(); 

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
	    $this->candidateStatus 	    = DB::table('master_status')
									->select('*')
									->orderBy('master_status.priority', 'ASC')
									->get(); 

        return view('admin.customreport.index' ,$this->data, $data1);

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
	 
    public function create()
    {

        abort_if(! $this->user->can('add_skills'), 403);
		$this->countries = Country::all();
       // $this->categories = JobCategory::all();
        return view('admin.client.create', $this->data);

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function store(Request $request)
    {

        abort_if(! $this->user->cans('add_skills'), 403);

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
        return Reply::redirect(route('admin.clients.index'), __('menu.clients').' '.__('messages.createdSuccessfully'));
    }
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        abort_if(! $this->user->can('edit_skills'), 403);
        $this->client 				= Client::find($id);
		$this->client_file 			= DB::table('client_file')->select('*')->where('client_id', $id)->get();
		$this->countries 			= Country::all();
		$this->client_location 		= DB::table('client_location')
										->join('countries', 'countries.id', '=', 'client_location.country_id')
										->select('client_location.city', 'client_location.address', 'client_location.postcode', 'countries.country_name')
										->where('client_location.client_id', $id)
										->get();
		$this->client_contact_info 	= DB::table('client_contact_info')->select('*')->where('client_id', $id)->get();
        return view('admin.client.edit', $this->data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
	 
    public function update(Request $request, $id)
    	{

        abort_if(! $this->user->can('edit_skills'), 403);



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







		/*if(!empty($request->commission)){

			$client->commission 	= $request->commission;

		}*/

		/*if(!empty($request->payout)){

			$client->payout 	   = $request->payout;

		}*/





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







        return Reply::redirect(route('admin.clients.index'), __('menu.clients').' '.__('messages.updatedSuccessfully'));



    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function destroy($id)
    	{



        abort_if(! $this->user->can('delete_skills'), 403);



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

	public function download($id)
    	{





	    $client_file 	= DB::table('client_file')->select('*')->where('id', $id)->first();





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
        abort_if(! $this->user->can('edit_skills'), 403);
		$this->cc_info 	= DB::table('client_contact_info')->select('*')->where('id', $id)->first();
       return view('admin.client.edit_information', $this->data);
    }
	public function edit_location($id)
    	{
        abort_if(! $this->user->can('edit_skills'), 403);
		$this->cl_info 	= DB::table('client_location')
								->join('clients', 'clients.id', '=', 'client_location.client_id')
								->select('clients.name as client_name', 'client_location.id', 'client_location.address', 'client_location.city', 'client_location.postcode', 'client_location.country_id')
								->where('client_location.id', $id)
								->first();
		$this->countries 			= Country::all();
       return view('admin.client.edit_location', $this->data);
    }
	public function update_info(Request $request, $id)
    {
        abort_if(! $this->user->can('edit_skills'), 403);
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
        abort_if(! $this->user->can('edit_skills'), 403);
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
	public function data(Request $request)
	  	{

			
       		$custom_text			=	'';
			$custom_text			=	$request->custom_text;

			$start_date				=	'';
			$start_date				=	$request->start_date;
			
			$end_date				=	'';
			$end_date				=	$request->end_date;

			$clients				=	'';
			$clients				=	$request->clients;

			$jobs					=	'';
			$jobs					=	$request->jobs;			

			$source					=	'';
			$source					=	$request->source;

			$master_status_id		=	'';									//  Application status 
			$master_status_id		=	$request->master_status_id;

			$interview_process		=	'';                                  // Interview schedule status
			$interview_process		=	$request->interview_process;  

			$application_data 		=  DB::table('clients')
										->select(
										'clients.id', 'clients.company_id', 'clients.self_company', 'clients.name as client_name', 'clients.email', 'clients.contact_person_name',
										'jobs.title as job_title' , 'jobs.id as job_id', 
										'job_applications.id as application_id', 'job_applications.master_status_id', 
										DB::raw('count(job_applications.id) as total_application'),
										DB::raw("SUM(CASE WHEN job_applications.master_status_id = 1 THEN 1 ELSE 0 END) AS sourcing"),
										DB::raw("SUM(CASE WHEN job_applications.master_status_id = 2 THEN 1 ELSE 0 END) AS screening"),
										DB::raw("SUM(CASE WHEN job_applications.master_status_id = 3 THEN 1 ELSE 0 END) AS interview"),
										DB::raw("SUM(CASE WHEN job_applications.master_status_id = 4 THEN 1 ELSE 0 END) AS negotiation"),
										DB::raw("SUM(CASE WHEN job_applications.master_status_id = 5 THEN 1 ELSE 0 END) AS offered"),
										DB::raw("SUM(CASE WHEN job_applications.master_status_id = 6 THEN 1 ELSE 0 END) AS joined"),
										DB::raw("SUM(CASE WHEN job_applications.master_status_id = 7 THEN 1 ELSE 0 END) AS rejected")
									);

			$application_data 		=	$application_data->join('jobs', 'jobs.client_id', '=', 'clients.id');
			$application_data 		=	$application_data->join('job_applications', 'job_applications.job_id', '=', 'jobs.id');
			$application_data 		=	$application_data->join('users', 'job_applications.submitted_by', '=', 'users.id');
			$application_data 		=	$application_data->where('clients.company_id', $this->user->company_id);	

			// START DATE  AND END DATE	// d-m-y to y-m-d
			if(!empty($start_date) && !empty($end_date)){				
				$start_date 		=	date("Y-m-d", strtotime($start_date) );
				$end_date 			=	date("Y-m-d", strtotime($end_date) );
				$application_data 	=	$application_data->whereBetween('job_applications.created_at', [$start_date, $end_date] );
			}
			// SERACH BY CLIENT 
			if($clients > 0){
				$application_data 	=	$application_data->where('clients.id', $clients );		
			}	
			//	SEARCH BY JOBS 
			if($jobs > 0){
				$application_data 	=	$application_data->where('jobs.id', $jobs );	 	
			}
			//	MASTER STATUS 
			if($master_status_id > 0){
				$application_data 	=	$application_data->where('job_applications.master_status_id', $master_status_id );	 	
			}							
			// SERACH BY SOURCE / TEAM  
			if($source > 0){
				$application_data 	=	$application_data->where('job_applications.submitted_by', $source );		
			}
		// CUSTOM TEXT  

		if(!empty($custom_text) )
		{
			$application_data 	=	$application_data->where('clients.name', 'like', '%' . $custom_text . '%')
			->orWhere('jobs.title', 'like', '%' . $custom_text . '%')                       
			->orWhere('users.name', 'like', '%' . $custom_text . '%');
		} 

		$application_data 		=	$application_data->groupBy('jobs.id');

		$application_data 		=	$application_data->orderBy('clients.name', 'ASC');

        return DataTables::of($application_data)

		
		->editColumn('chkbox', function ($row)
		{
            return '<div class="form-check form-check-sm form-check-custom form-check-solid">
                    <input class="form-check-input" type="checkbox" value="1">
                  </div>';
        })

		// ->editColumn('client_name', function ($row)
		// {
		// 	return ucfirst($row->client_name);
		// })

		->editColumn('client_name', function ($row)
		{					  
            return '                
			<div class="d-flex flex-column">
			<a href="javascript:void(0);" data-row-id="' . $row->id . '" class="text-dark fw-bolder text-hover-primary fs-6 show-detail"  data-toggle="tooltip" data-placement="top" data-html="true" title="' . ucwords(@$row->client_name) . '">' . ucwords(substr(@$row->client_name, 0, 15)) . '</a>
			<a href="javascript:void(0);" data-row-id="' . $row->id . '" class="text-muted fw-bold text-muted d-block fs-7 text-hover-primary show-detail">'.@$departmentArr.'</a>
			</div>
			';
        })
			
		// ->editColumn('job_title', function ($row)
		// {
		// 	return ucfirst($row->job_title);
		// })

		->editColumn('job_title', function ($row)
		{					  
            return '                
			<div class="d-flex flex-column">
			<a href="javascript:void(0);" data-row-id="' . $row->id . '" class="text-dark fw-bolder text-hover-primary fs-6 show-detail"  data-toggle="tooltip" data-placement="top" data-html="true" title="' . ucwords(@$row->job_title) . '">' . ucwords(substr(@$row->job_title, 0, 15)) . '</a>
			<a href="javascript:void(0);" data-row-id="' . $row->id . '" class="text-muted fw-bold text-muted d-block fs-7 text-hover-primary show-detail">'.@$departmentArr.'</a>
			</div>
			';
        })		

		->editColumn('total_application', function ($row)
		{
			$html		=	'
			<div class="text-left pl-3"><a  style="color:#0e0606;font-weight:700" href="javascript:void(0);" class="joblisting" data-widget="control-sidebar1" data-slide="true" data-row-id="'.$row->job_id.'" data-row-status="0" >'.$row->total_application.'</a></div>';

			return $html;

		})

		->editColumn('sourcing', function ($row)
		{
			$html		=	'
			<div class="text-left pl-3"><a href="javascript:void(0);" style=" color:#737373; font-weight:700"  class="joblisting" data-widget="control-sidebar1" data-slide="true" data-row-id="'.$row->job_id.'" data-row-status="1" >'.$row->sourcing.'</a></div>';

			return $html;

		})

		->editColumn('screening', function ($row)
		{
			$html		=	'
			<div class="text-left pl-3"><a href="javascript:void(0);" style=" color:#c000ff; font-weight:700"  class="joblisting" data-widget="control-sidebar1" data-slide="true" data-row-id="'.$row->job_id.'" data-row-status="2" >'.$row->screening.'</a></div>';

			//$html		=	'<a href="Javascript:void(0);" style=" color:#ffab00; font-weight:bold">'.$row->screening.'</a>';

			return $html;

		})

		->editColumn('interview', function ($row)
		{
			$html		=	'
			<div class="text-left pl-3"><a href="javascript:void(0);" style=" color:#2896ff; font-weight:700"  class="joblisting" data-widget="control-sidebar1" data-slide="true" data-row-id="'.$row->job_id.'" data-row-status="3" >'.$row->interview.'</a></div>';
			//$html		=	'<a href="Javascript:void(0);" style=" color:#2896ff; font-weight:bold">'.$row->interview.'</a>';

			return $html;

		})

		->editColumn('negotiation', function ($row)
		{

			$html		=	'
			<div class="text-left pl-3"><a href="javascript:void(0);" style=" color:#ff4098; font-weight:700"  class="joblisting" data-widget="control-sidebar1" data-slide="true" data-row-id="'.$row->job_id.'" data-row-status="4" >'.$row->negotiation.'</a></div>';

			//$html		=	'<a href="Javascript:void(0);" style=" color:#ff4098; font-weight:bold">'.$row->negotiation.'</a>';

			return $html;

		})

		->editColumn('offered', function ($row)
		{
			$html		=	'
			<div class="text-left pl-3"><a href="javascript:void(0);" style=" color:#ffab00; font-weight:700"  class="joblisting" data-widget="control-sidebar1" data-slide="true" data-row-id="'.$row->job_id.'" data-row-status="5" >'.$row->offered.'</a></div>';

			//$html		=	'<a href="Javascript:void(0);" style=" color:#06c732; font-weight:bold">'.$row->offered.'</a>';

			return $html;				

		})

		->editColumn('joined', function ($row)
		{
			$html		=	'
			<div class="text-left pl-3"><a href="javascript:void(0);" style=" color:#06c732; font-weight:700"  class="joblisting" data-widget="control-sidebar1" data-slide="true" data-row-id="'.$row->job_id.'" data-row-status="6" >'.$row->joined.'</a></div>';

			//$html		=	'<a href="Javascript:void(0);" style=" color:#c000ff; font-weight:bold">'.$row->joined.'</a>';

			return $html;				

		})

		->editColumn('rejected', function ($row)
		{
			$html		=	'
			<div class="text-left pl-3"><a href="javascript:void(0);" style=" color:#ff0202; font-weight:700"  class="joblisting" data-widget="control-sidebar1" data-slide="true" data-row-id="'.$row->job_id.'" data-row-status="7" >'.$row->rejected.'</a></div>';

			//$html		=	'<a href="Javascript:void(0);" style=" color:#ff0202; font-weight:bold">'.$row->rejected.'</a>';

			return $html;

		})

		->editColumn('source', function ($row) use($source)
		{
			if($source == 'all')
			{
				$html		=	'
							<div class="d-flex flex-column">
								<div class="symbol-group symbol-hover">
									<div class="symbol symbol-30 symbol-circle" data-toggle="tooltip" data-placement="top" title="Thor">
										<img alt="img" src="../metronic_assets/media/avatars/150-1.jpg">
									</div>
								</div>
							</div>'; 
				return $html;
			}
			else if($source > 0)
			{
				$userRow    = DB::table('users')->select('users.name')->where('id', $source)->first();
				$html		=	'<a href="Javascript:void(0);" style=" color:#adb5bd; font-weight:bold">'.$userRow->name.'</a>';
				return $html;
			}
		})


		-> rawColumns(['chkbox', 'client_name', 'job_title', 'total_application', 'sourcing', 'screening', 'interview', 'negotiation', 'offered', 'joined',  'rejected', 'source'])

		->addIndexColumn()

		->make(true);







	  }	

 		public function show(Request $request)
    	{

		

		$application_status		=	$request->application_status;

		$job_id					=	$request->job_id;

		$this->jobs 			=	Job::select('*')

									->where('jobs.id', '=', $job_id)

									->first(); 

									

		$myApplicationList		=	DB::table('job_applications')

										->select(

												'job_applications.id',
												'job_applications.full_name',
												'job_applications.email',
												'job_applications.phone' ,
												'job_applications.ctc',
												'job_applications.ectc',
												'job_applications.total_exp', 
												'job_applications.rel_exp',
												'jobs.title as job_title',
												'clients.name as client_name',
												'users.name as source',
												'master_status.name as master_status_name'
												);				


			$myApplicationList 		=	$myApplicationList->join('jobs', 'jobs.id', '=', 'job_applications.job_id' );
			$myApplicationList 		=	$myApplicationList->join('clients', 'clients.id', '=', 'jobs.client_id' );
			$myApplicationList 		=	$myApplicationList->leftJoin('master_status', 'master_status.id', '=', 'job_applications.master_status_id' );
			$myApplicationList 		=	$myApplicationList->join('users', 'job_applications.submitted_by', '=', 'users.id');
			$myApplicationList 		=	$myApplicationList->where('job_applications.job_id', $job_id );	

			if($application_status > 0)
			{
				$myApplicationList 		=	$myApplicationList->where('job_applications.master_status_id', $application_status );
			}

			$this->applicantlist  	= $myApplicationList->get();
			return view('admin.customreport.show', $this->data)->render();


        	return DataTables::of($application_status)

		
		
		->editColumn('chkbox', function ($row)
		{
            return '<div class="form-check form-check-sm form-check-custom form-check-solid">
                    <input class="form-check-input" type="checkbox" value="1">
                  </div>';
        })

		// ->editColumn('client_name', function ($row)
		// {
		// 	return ucfirst($row->client_name);
		// })

		// ->editColumn('client_name', function ($row)
		// {					  
        //     return '                
		// 	<div class="d-flex flex-column">
		// 	<a href="javascript:void(0);" data-row-id="' . $row->id . '" class="text-dark fw-bolder text-hover-primary fs-6 show-detail"  data-toggle="tooltip" data-placement="top" data-html="true" title="' . ucwords(@$row->client_name) . '">' . ucwords(substr(@$row->client_name, 0, 15)) . '</a>
		// 	<a href="javascript:void(0);" data-row-id="' . $row->id . '" class="text-muted fw-bold text-muted d-block fs-7 text-hover-primary show-detail">'.@$departmentArr.'</a>
		// 	</div>
		// 	';
        // })


		-> rawColumns(['chkbox'])

		->addIndexColumn()

		->make(true);

	}

	public function myInterviewSchedule(Request $request) 
	  	{

			$custom_text				=	'';
			$custom_text				=	$request->custom_text;

			$start_date					=	'';
			$start_date					=	$request->start_date;

			$end_date					=	'';
			$end_date					=	$request->end_date;

			$clients					=	'';
			$clients					=	$request->clients;

			$jobs						=	'';
			$jobs						=	$request->jobs;			

			$source						=	'';
			$source						=	$request->source;

			$master_status_id			=	'';									//  Application status 
			$master_status_id			=	$request->master_status_id;

			$interview_process			=	'';                                  // Interview schedule status
			$interview_process			=	$request->interview_process;  

			$interviewScheduleList		=	DB::table('interview_schedules')
											->select(
														'interview_schedules.id',
														'interview_schedules.schedule_date',
														'interview_schedules.interviewers',
														'job_applications.full_name', 
														'jobs.title as requisitions_title',
														'clients.name as client_name',
														'users.name as source_user',
														'interview_schedules.status as interview_status_name',
														'master_status.name as status_name',
														'candidate_status.name as child_status_name',													
														DB::raw(
																	'(CASE 
																			WHEN interview_schedules.interview_mode = 1 THEN "TELECON"     
																			WHEN interview_schedules.interview_mode = 2 THEN "VIDEO" 
																			WHEN interview_schedules.interview_mode = 3 THEN "F2F"
																			WHEN interview_schedules.interview_mode = 4 THEN "OTHER"
																	  END) AS interview_mode'
																),
														DB::raw(
																	'(CASE 
																			WHEN interview_schedules.feedback = 0 THEN "PENDING" 
																			WHEN interview_schedules.feedback = 1 THEN "SHORTLISTED"      
																			WHEN interview_schedules.feedback = 2 THEN "REJECTED" 
																			WHEN interview_schedules.feedback = 3 THEN "FEEDBACK"    
																			WHEN interview_schedules.feedback = 4 THEN "RESCHEDULE"
																	  END) AS feedback'
																)
													)

											->join('job_applications', 'job_applications.id', '=', 'interview_schedules.job_application_id' )
											->join('jobs', 'jobs.id', '=', 'job_applications.job_id' )	
											->join('clients', 'clients.id', '=', 'jobs.client_id' )		
											->join('users', 'users.id', '=', 'job_applications.submitted_by' )
											->join('master_status', 'master_status.id', '=', 'job_applications.master_status_id' )		
											->leftjoin('candidate_status', 'candidate_status.id', '=', 'job_applications.child_status_id' )	;							

			$interviewScheduleList 		=	$interviewScheduleList->where('interview_schedules.company_id', $this->user->company_id);	
			// START DATE  AND END DATE	// d-m-y to y-m-d
			if(!empty($start_date) && !empty($end_date)){				
				$start_date 			=	date("Y-m-d", strtotime($start_date) );
				$end_date 				=	date("Y-m-d", strtotime($end_date) );
				$interviewScheduleList 	=	$interviewScheduleList->whereBetween('interview_schedules.schedule_date', [$start_date, $end_date] );
			}
           // SERACH BY CLIENT 
			if($clients > 0){
				$interviewScheduleList 	=	$interviewScheduleList->where('clients.id', $clients );		
			}
		   //	SEARCH BY JOBS 
			if($jobs > 0){
				$interviewScheduleList 	=	$interviewScheduleList->where('jobs.id', $jobs );	      	
			}
		   // SERACH BY SOURCE / TEAM  
			if($source > 0){
				$interviewScheduleList 	=	$interviewScheduleList->where('job_applications.submitted_by', $source );		
			}	
			//	MASTER STATUS 
			if($master_status_id > 0){
				$interviewScheduleList 	=	$interviewScheduleList->where('job_applications.master_status_id', $master_status_id );	 	
			}
			// CUSTOM TEXT  

			if(!empty($custom_text) ){
				$interviewScheduleList 	=	$interviewScheduleList->where('clients.name', 'like', '%' . $custom_text . '%')
										->orWhere('jobs.title', 'like', '%' . $custom_text . '%')                       
										->orWhere('users.name', 'like', '%' . $custom_text . '%');
			}  

			return DataTables::of($interviewScheduleList) 

			->editColumn('schedule_date', function ($row) {
				$date 			= $row->schedule_date;
				$date 			= date("d-M-Y H:m:s", strtotime($date)); 
                return $date;

            }) 
			 ->editColumn('full_name', function ($row) {
                return $row->full_name;
            })

			->editColumn('client_name', function ($row) {
                return $row->client_name;
            })

			->editColumn('requisitions_title', function ($row) {
                return $row->requisitions_title;
            })			

			->editColumn('source_user', function ($row) {
                return ucfirst($row->source_user);
            })

			->editColumn('interview_status_name', function ($row) {
                return ucfirst($row->interview_status_name);
            })

			->editColumn('interview_mode', function ($row) {
                return ucfirst($row->interview_mode);
            })

			->editColumn('status_name', function ($row) {
                return ucfirst($row->status_name);
            })

			->editColumn('child_status_name', function ($row) {
                return ucfirst($row->child_status_name);
           })


			->editColumn('interviewers', function ($row) {
				$intName				=	'';
				$interviewers			=	$row->interviewers;
				if(!empty($interviewers))
				{
						$interviewers	=	explode(",",$interviewers);
						foreach($interviewers as $val)
						{					
							if($val > 0)
							{

								$userRow 				= DB::table('users')
															->select('users.id', 'users.name')
															->where('users.id', $val)
															->first(); 
								@$intName 	.=	$userRow->name.'<br>';	 						
							}
						}	
				}
                return @$intName;
            })

			->editColumn('feedback', function ($row) {
                return ucfirst($row->feedback);
            })

			-> rawColumns(['schedule_date', 'full_name', 'title', 'interviewers'])
            ->addIndexColumn()
            ->make(true);

    }	

 	public function myapplicationlist() 
	 	{	 	

				// abort_if(! $this->user->can('view_skills'), 403);

       			$client 	= Client::all();

        	return DataTables::of($client)

		

		

            ->addColumn('action', function ($row) {                	

					$action = '';		

				//if( $this->user->cans('edit_client')){

						$action.= '<a href="' . route('admin.clients.edit', [$row->id]) . '" class=""

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

						$action.= '<a href="' . route('admin.clients.edit_information', [$info->id]) . '" class="btn"

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

							$action.= '<a href="' . route('admin.clients.edit_location', [$info->id]) . '" class="btn"

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

	//all/all/all/all/none/04-01-2021/04-30-2021/0/5

	 public function export($clients, $jobs, $master_status_id, $source, $custom_text, $start_date, $end_date,  $start, $length)
    	{
				$filters = [
								'clients' 			=> $clients,
								'jobs' 				=> $jobs,
								'master_status_id' 	=> $master_status_id,
								'source' 			=> $source,
								'custom_text' 		=> $custom_text,
								'start_date' 		=> $start_date,
								'end_date'			=> $end_date,
								'start' 			=> $start,
								'length' 			=> $length,
								'loginUserId'		=> $this->user->id,
								'loginUserRole'		=> $this->user->role->role_id
							];
				$data = [

							'company' => $this->companyName

						];

		

				return Excel::download(new ApplicationListExport($filters, $data), 'application_list.xlsx', ExcelExcel::XLSX);

    }

	 public function interviewexport($clients, $jobs, $master_status_id, $source, $custom_text, $start_date, $end_date,  $start, $length)
    	{				

				$filters = [
								'clients' 			=> $clients,
								'jobs' 				=> $jobs,
								'master_status_id' 	=> $master_status_id,
								'source' 			=> $source,
								'custom_text' 		=> $custom_text,
								'start_date' 		=> $start_date,								
								'end_date'			=> $end_date,
								'start' 			=> $start,
								'length' 			=> $length,
								'loginUserId'		=> $this->user->id,
								'loginUserRole'		=> $this->user->role->role_i
							];		

				$data   = [
							'company' => $this->companyName
						 ];		

				return Excel::download(new InterviewExcelExport($filters, $data), 'interviews_list.xlsx', ExcelExcel::XLSX);

    }


	

	public function report_filter(Request $request)
    	{
			echo "call me"; exit;
			return view('admin.customreport.report_filter', $this->data)->render();
    	}
	    public function custom_report_filter(Request $request)
    	{
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
    		//  $chkAdminRole = DB::table('roles')
    		// 				->select('*')
    		// 				->where('id', $this->user->role->role_id)
    		// 				->where('name', 'admin')
    		// 				->count(); 
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







    		$this->clients 				= Client::select('clients.*')->where('clients.company_id', '=', $this->user->company_id)->get();


			//echo '<pre>'; print_r($this->clients); exit; 
			

    		$this->users 				= User::select('users.*')->where('status', '=', 'active')->where('company_id', '=', $this->user->company_id)->get();

    	    $this->candidateStatus 	    = DB::table('master_status')
    									->select('*')
    									->orderBy('master_status.priority', 'ASC')
    									->get(); 

    									
		
			
												

									

		    return view('admin.customreport.custom_report_filter', $this->data)->render();  

			

    }

     

	 public function chart_teamwork(Request $request)
	 	{
		
	   		$clients				=	$jobs	=  $master_status_id =	$source		=	$start_date	=	$originalStartDate 	=	$end_date =	$originalEndDate =	$custom_text =	$interview_process		=	'';
			$clients				=	$request->clients;
			$this->clients			=	$clients;							// all
						
			$jobs					=	$request->jobs;
			$this->jobs				=	$jobs;								// all

			$master_status_id		=	$request->master_status_id;
			$this->master_status_id	=	$master_status_id;					// all
		
			$source					=	$request->source; 	
			$this->source			=	$source;							// all
			
			$start_date				=	$request->start_date;
			$oldDate 				=	$start_date;
			$arr 					=	explode('-', $oldDate);
			$start_date 			=	$arr[2].'-'.$arr[0].'-'.$arr[1];
			$this->start_date		=	$start_date;				
			$originalStartDate 		=	$request->start_date;
			$this->start_date 		=	date("Y-m-d", strtotime($originalStartDate));
			
			$end_date				=	$request->end_date;
			$oldDate 				=	$end_date;
			$arr 					=	explode('-', $oldDate);
			$end_date 				=	$arr[2].'-'.$arr[0].'-'.$arr[1];
			$this->end_date			=	$end_date;						
			$originalEndDate 		=	$request->end_date;
			$this->end_date 		=	date("Y-m-d", strtotime($originalEndDate));
						
			$custom_text			=	$request->custom_text;	
			$this->custom_text		=	$custom_text;			
			
			$interview_process		=	$request->interview_process;			

			$company_id				=	$this->user->company_id; 
			
			if($this->clients !='all' && $this->clients > 0)
			{
				$this->allJobs 		= DB::table('jobs')->select('id')->where('jobs.company_id', $this->user->company_id)->where('jobs.client_id', $this->clients)->whereBetween('jobs.created_at', [$start_date, $end_date] )->count(); 				

			}
			else
			{
				$this->allJobs 		= DB::table('jobs')->select('id')->where('jobs.company_id', $this->user->company_id)->whereBetween('jobs.created_at', [$start_date, $end_date] )->count(); 	
			}				

			$this->teamWorkArr 		=	DB::table('users');
			$this->teamWorkArr 		=	$this->teamWorkArr->join('role_user', 'role_user.user_id', '=', 'users.id');
			$this->teamWorkArr 		=	$this->teamWorkArr->join('roles', 'roles.id', '=', 'role_user.role_id');
			$this->teamWorkArr 		=	$this->teamWorkArr->leftJoin('job_assign', 'job_assign.user_id', '=', 'users.id');

			if($this->clients !='all' && $this->clients > 0){
				$this->teamWorkArr 	=	$this->teamWorkArr->leftJoin('jobs', 'jobs.id', '=', 'job_assign.job_id');	
			}						

			$this->teamWorkArr 		=	$this->teamWorkArr->select('users.id', 'users.name as user_name', 'roles.name as rolename', DB::raw("COUNT(job_assign.user_id) as assign")  ) ;																							

			if($this->clients !='all' && $this->clients > 0){			// CLIENT
				$this->teamWorkArr 	=	$this->teamWorkArr->where('jobs.client_id', $this->clients);
			}
			if($this->jobs !='all' && $this->jobs > 0){					//	JOBS
				$this->teamWorkArr 	=	$this->teamWorkArr->where('jobs.id', $this->jobs);
			}
			if($this->source !='all' && $this->source > 0){				// SOURCE / TEAM 
				$this->teamWorkArr 	=	$this->teamWorkArr->where('users.id', $this->source);
			}

			//$teamWorkArr 			=	$this->teamWorkArr->whereBetween('jobs.created_at', [$start_date, $end_date] );   
			//$this->start_date	$this->end_date	 start_date	04-01-2021 m-d-y     end_date	05-31-2021  m-d-y
			//start_date	04/01/2021  end_date	05/31/2021

			$this->teamWorkArr 		=	$this->teamWorkArr->where('users.company_id', $this->user->company_id);
			$this->teamWorkArr 		=	$this->teamWorkArr->groupBy('job_assign.user_id');		
			$this->teamWorkArr 		=	$this->teamWorkArr->get();	 						




			
			
				


//###########   START FOR ALL CLIENT
				$this->clientJobCount 					= 	DB::table('clients');
				$this->clientJobCount 					=	$this->clientJobCount->join('jobs', 'jobs.client_id', '=', 'clients.id');
				$this->clientJobCount 					=	$this->clientJobCount->select('clients.id', 'clients.name', DB::raw("COUNT(jobs.id) as jobCounter") );
				$this->clientJobCount 					=	$this->clientJobCount->where('clients.company_id', $this->user->company_id);
				if(($this->clients != 'all') && ($this->clients > 0)){
					$this->clientJobCount				= $this->clientJobCount->where('jobs.client_id', $this->clients);												//	CLIENT		
				}																										
				$this->clientJobCount					=	$this->clientJobCount->groupBy('clients.id');
				$this->clientJobCount					=	$this->clientJobCount->count();
				 
				$this->clientJob 						= 	DB::table('clients');
				$this->clientJob						=	$this->clientJob->join('jobs', 'jobs.client_id', '=', 'clients.id');
				$this->clientJob						=	$this->clientJob->select('clients.id', 'clients.name', DB::raw("COUNT(jobs.id) as jobCounter") );
				$this->clientJob						=	$this->clientJob->where('clients.company_id', $this->user->company_id);
				if(($this->clients != 'all') && ($this->clients > 0)){
					$this->clientJob					= $this->clientJob->where('jobs.client_id', $this->clients);												//	CLIENT		
				}											
				$this->clientJob						=	$this->clientJob->groupBy('clients.id');
				$this->clientJob						=	$this->clientJob->get();
															
//###########   END START FOR ALL CLIENT







//#############   START APPLICATION ON REQUISITION 

				$this->applicationOnRequisitionCount 	= DB::table('clients');
				$this->applicationOnRequisitionCount	= $this->applicationOnRequisitionCount->join('jobs', 'jobs.client_id', '=', 'clients.id');
				$this->applicationOnRequisitionCount	= $this->applicationOnRequisitionCount->join('job_applications', 'job_applications.job_id', '=', 'jobs.id');
				$this->applicationOnRequisitionCount	= $this->applicationOnRequisitionCount->select('jobs.id', 'jobs.title', DB::raw("COUNT(job_applications.id) as applicationCounter") );
				$this->applicationOnRequisitionCount	= $this->applicationOnRequisitionCount->where('clients.company_id', $this->user->company_id);
				$this->applicationOnRequisitionCount	= $this->applicationOnRequisitionCount->whereBetween('job_applications.created_at', [$this->start_date, $this->end_date] );		//	DATE
				if(($this->clients != 'all') && ($this->clients > 0)){
					$this->applicationOnRequisitionCount= $this->applicationOnRequisitionCount->where('jobs.client_id', $this->clients);												//	CLIENT		
				}
				if(($this->jobs != 'all') && ($this->jobs > 0)){
					$this->applicationOnRequisitionCount= $this->applicationOnRequisitionCount->where('jobs.id', $this->jobs);															//	JOBS	
				}
				if(($this->master_status_id != 'all') && ($this->master_status_id > 0)){
					$this->applicationOnRequisitionCount = $this->applicationOnRequisitionCount->where('job_applications.master_status_id', $this->master_status_id);					//	MASTER STATUS	
				}
				if(($this->source != 'all') && ($this->source > 0)){
					$this->applicationOnRequisitionCount = $this->applicationOnRequisitionCount->where('job_applications.submitted_by', $this->source);										//	TEAM	
				}
																
				$this->applicationOnRequisitionCount	= $this->applicationOnRequisitionCount->groupBy('jobs.id');
				$this->applicationOnRequisitionCount	= $this->applicationOnRequisitionCount->count(); 
				
				
				
				
				
				$this->applicationOnRequisition 	= DB::table('clients');
				$this->applicationOnRequisition		= $this->applicationOnRequisition->join('jobs', 'jobs.client_id', '=', 'clients.id');
				$this->applicationOnRequisition		= $this->applicationOnRequisition->join('job_applications', 'job_applications.job_id', '=', 'jobs.id');
				$this->applicationOnRequisition		= $this->applicationOnRequisition->select('jobs.id', 'jobs.title', DB::raw("COUNT(job_applications.id) as applicationCounter") );
				$this->applicationOnRequisition		= $this->applicationOnRequisition->where('clients.company_id', $this->user->company_id);
				$this->applicationOnRequisition		= $this->applicationOnRequisition->whereBetween('job_applications.created_at', [$this->start_date, $this->end_date] );			//	DATE
				if(($this->clients != 'all') && ($this->clients > 0)){
					$this->applicationOnRequisition			= $this->applicationOnRequisition->where('jobs.client_id', $this->clients);												//	CLIENT		
				}
				if(($this->jobs != 'all') && ($this->jobs > 0)){
					$this->applicationOnRequisition= $this->applicationOnRequisition->where('jobs.id', $this->jobs);																//	JOBS	
				}
				if(($this->master_status_id != 'all') && ($this->master_status_id > 0)){
					$this->applicationOnRequisition = $this->applicationOnRequisition->where('job_applications.master_status_id', $this->master_status_id);							//	MASTER STATUS	
				}
				if(($this->source != 'all') && ($this->source > 0)){
					$this->applicationOnRequisition = $this->applicationOnRequisition->where('job_applications.submitted_by', $this->source);										//	TEAM	
				}									
				$this->applicationOnRequisition	= $this->applicationOnRequisition->groupBy('jobs.id');
				$this->applicationOnRequisition	= $this->applicationOnRequisition->get();
				
//#############   END APPLICATION ON REQUISITION 				
				
				
				
				
				
				
																						
//#############   ASSIGNMENT STAGE	
				//ASSIGNMENT STAGE COUNT			
				$this->assesmentStageCount 				= DB::table('master_status');
				$this->assesmentStageCount				= $this->assesmentStageCount->leftJoin('job_applications', 'job_applications.master_status_id', '=', 'master_status.id');
				$this->assesmentStageCount				= $this->assesmentStageCount->join('jobs', 'jobs.id', '=', 'job_applications.job_id');
				$this->assesmentStageCount				= $this->assesmentStageCount->select('master_status.name', DB::raw("COUNT(job_applications.id) as applicationCounter"));
				//$this->assesmentStageCount			= $this->assesmentStageCount->where('master_status.id', '!=', 7);
				$this->assesmentStageCount				= $this->assesmentStageCount->where('job_applications.company_id', $this->user->company_id);								
				$this->assesmentStageCount				= $this->assesmentStageCount->whereBetween('job_applications.created_at', [$this->start_date, $this->end_date] );			//	DATE	
				if(($this->clients != 'all') && ($this->clients > 0)){
					$this->assesmentStageCount			= $this->assesmentStageCount->where('jobs.client_id', $this->clients);														//	CLIENT		
				}
				if(($this->jobs != 'all') && ($this->jobs > 0)){
					$this->assesmentStageCount			= $this->assesmentStageCount->where('jobs.id', $this->jobs);																//	JOBS	
				}
				if(($this->master_status_id != 'all') && ($this->master_status_id > 0)){
					$this->assesmentStageCount			= $this->assesmentStageCount->where('job_applications.master_status_id', $this->master_status_id);						    //	MASTER STATUS	
				}
				if(($this->source != 'all') && ($this->source > 0)){
					$this->assesmentStageCount			= $this->assesmentStageCount->where('job_applications.submitted_by', $this->source);										//	TEAM	
				}										
				$this->assesmentStageCount				= $this->assesmentStageCount->groupBy('master_status.id');
				$this->assesmentStageCount				= $this->assesmentStageCount->orderBy('master_status.priority', 'ASC'); 
				$this->assesmentStageCount				= $this->assesmentStageCount->count();
				
				//ASSIGNMENT STAGE DATA
				$this->assesmentStage 					= DB::table('master_status');
				$this->assesmentStage					= $this->assesmentStage->leftJoin('job_applications', 'job_applications.master_status_id', '=', 'master_status.id');
				$this->assesmentStage					= $this->assesmentStage->join('jobs', 'jobs.id', '=', 'job_applications.job_id');
				$this->assesmentStage					= $this->assesmentStage->select('master_status.name', DB::raw("COUNT(job_applications.id) as applicationCounter"));
				//$this->assesmentStage					= $this->assesmentStage->where('master_status.id', '!=', 7);
				$this->assesmentStage					= $this->assesmentStage->where('job_applications.company_id', $this->user->company_id);				
				$this->assesmentStage					= $this->assesmentStage->whereBetween('job_applications.created_at', [$this->start_date, $this->end_date] );    			//	DATE 
				if(($this->clients != 'all') && ($this->clients > 0)){
					$this->assesmentStage				= $this->assesmentStage->where('jobs.client_id', $this->clients);															//	CLIENT	
				}
				if(($this->jobs != 'all') && ($this->jobs > 0)){
					$this->assesmentStage				= $this->assesmentStage->where('jobs.id', $this->jobs);																		//	JOBS	
				}
				if(($this->master_status_id != 'all') && ($this->master_status_id > 0)){
					$this->assesmentStage				= $this->assesmentStage->where('job_applications.master_status_id', $this->master_status_id);								//	MASTER STATUS	
				}	
				if(($this->source != 'all') && ($this->source > 0)){
					$this->assesmentStage				= $this->assesmentStage->where('job_applications.submitted_by', $this->source);												//	TEAM	
				}												
				$this->assesmentStage					= $this->assesmentStage->groupBy('master_status.id');
				$this->assesmentStage					= $this->assesmentStage->orderBy('master_status.priority', 'ASC'); 
				$this->assesmentStage					= $this->assesmentStage->get();
//##############################################################


				

			 $view = view('admin.customreport.chart_teamwork', $this->data)->render();   
       		 return Reply::dataOnly(['status' => 'success', 'view' => $view]);	   		
    
		
		
		}
}

