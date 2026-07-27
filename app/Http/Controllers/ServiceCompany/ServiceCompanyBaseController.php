<?php
/*** ########################################################
Controller    	: 	AdminBaseController
Purmose			: 
Author			:	Abu
Company			:	4Bell
Date			:	05-April-2021	
########################################################***/

namespace App\Http\Controllers\ServiceCompany;

use App\Models\LanguageSetting;
use App\Models\LinkedInSetting;
use App\Models\StickyNote;
use Carbon\Carbon;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\App;
use App\Models\ThemeSetting;
use App\Models\Company;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use App\Models\CompanyPackage;
use Illuminate\Support\Facades\DB;
use App\Models\GlobalSetting;
use Illuminate\Support\Facades\Route;

//use Illuminate\Foundation\Auth\AuthenticatesUsers; 

class ServiceCompanyBaseController extends Controller
{
    /**
     * @var array
     */
    public $data = [];

    /**
     * @param $name
     * @param $value
     */
	 // use AuthenticatesUsers; 
	  
    public function __set($name, $value)
    {
        $this->data[$name] = $value;
    }

    /**
     * @param $name
     * @return mixed
     */
    public function __get($name)
    {
        return $this->data[$name];
    }

    /**
     * @param $name
     * @return bool
     */
    public function __isset($name)
    {
        return isset($this->data[ $name ]);
    }

    /**
     * UserBaseController constructor.
     */
    public function __construct()
    {
		
         parent::__construct();
		 
		 
        // Inject currently logged in user object into every view of user dashboard
		//   $this->emailSetting = EmailNotificationSetting::all();
		
        $this->middleware(function ($request, $next) 
		{

		    $this->user = auth()->user();
			$_SESSION['profile_popup_modal_show'] = 1;
			 
			//Prevent multiple login  - START 
			$session_token	=	'';				
			$all			=	session()->all(); 
			$session_token	=	$all['_token'];
			$user_id		=	$this->user->id;

			$chkAdminRole = $this->user->hasRole('admin');
			if($chkAdminRole){
				$role_row =	DB::table('roles')->select('roles.*')->where('roles.name','=','admin')->where('roles.company_id',$this->user->company_id)->first();
				$exists = DB::table('role_user')->where('user_id', $user_id)->exists();
				if (!$exists) {
					$valuesrole = array('user_id' => $user_id, 'role_id' => $role_row->id);
					DB::table('role_user')->insertOrIgnore($valuesrole);
				}

			}

			if(count($this->user->getAllPermissions()) <= 0){
				if ($assignRole = User::find($user_id)) {
					$rolesAs = DB::table('roles')
					->join('role_user','role_user.role_id','=','roles.id')
					->select('roles.name','roles.id')
					->where('role_user.user_id','=',$user_id)
					->first();
                    
					$roleId = $rolesAs->id;
					// if($this->user->id==5879){
        			//     dd($roleId,count($this->user->getAllPermissions()));
         			// }

					
					$role_row =	DB::table('roles')->select('roles.name')->where('roles.id',$roleId)->first();
					// dd($role_row->name);
						// Assign role
					$assignRole->syncRoles([$role_row->name]); //update 
					
					$assignRole->assignRole($role_row->name); //add new 

					// Get permissions from that role
					$permissions = DB::table('role_has_permissions')
						->join('permissions', 'permissions.id', '=', 'role_has_permissions.permission_id')
						->where('role_has_permissions.role_id', $roleId)
						->pluck('permissions.name')
						->toArray();
						$exists = DB::table('role_user')->where('user_id', $user_id)->exists();
						if (!$exists) {
							$valuesrole = array('user_id' => $user_id, 'role_id' => $roleId);
							DB::table('role_user')->insertOrIgnore($valuesrole);
						}
					// Sync permissions to user (IMPORTANT)
					$assignRole->syncPermissions($permissions);
				}
			}
				

			$userCounter 	=	DB::table('user_token')->select('id')->where('user_id', $user_id)->count();
			if($userCounter > 0)
			{
				$loginData 	=	DB::table('user_token')->select('user_token.id', 'user_token.user_id', 'user_token.token')->where('user_id', $user_id)->first();	
				
				$token		=	$loginData->token;
				
				//echo '$token=::'.$token;
				//echo '<br>';
				//echo '$session_token=::'.$session_token;
				//exit; 
				//echo '<pre>'; print_r($token);
				if($session_token != $token)    
				{ 
				
							/*$user = auth()->user();
							$request->session()->invalidate();
							
							if (module_enabled('Subdomain')) {
								if ($user->is_superadmin) {
									return $this->loggedOut($request) ?: redirect(route('front.super-admin-login'));
								}
								return redirect(route('login'));
							}*/
								
				}
				
						
			}
			$this->client = Company::find($this->user->company_id);
			//  dd(auth()->user()->getRoleNames());
					
			/*if (isset($_SESSION['username'])) { 
				$result = mysqli_query($con, "SELECT token FROM user_token where username='".$_SESSION['username']."'");

				if (mysqli_num_rows($result) > 0) {
					$row = mysqli_fetch_array($result); 
					$token = $row['token']; 
				
					if($_SESSION['token'] != $token){
				
						session_destroy();
						header('Location: index.php');
					}
				}
			}*/
						
					
			//Prevent multiple login  - END 					
		
		
			// $role = Role::findByName('default'); // or use find($id)
				// $permissions = Permission::all();
				// $role->syncPermissions($permissions);
				
			/**$roles = DB::table('roles')->select('name')->distinct()->get();
				 foreach ($roles as $role) {
					Role::firstOrCreate(['name' => $role->name, 'guard_name' => 'web']);
				}
				foreach (User::all() as $user) {
					$rolesAs = DB::table('roles')
					->join('role_user','role_user.role_id','=','roles.id')
					->select('roles.name')
					->where('role_user.user_id','=',$user->id)
					->first();
					
					if($rolesAs){
					//  dd($rolesAs,$rolesAs->name);
						$user = User::find($user->id);
						$user->assignRole($rolesAs->name);
					}
					
				}
				foreach (User::all() as $user) {
						
						$user = User::find($user->id);
						$rolesCustome = $user->getRoleNames();
					if ($rolesCustome->isNotEmpty()) {
						$role_name		=	$rolesCustome[0];
						$roles = DB::table('roles')->select('id')->where('roles.name','=',$role_name)->where('roles.company_id','=',$user->company_id)->first();
						
						DB::table('role_has_permissions')->where('role_id', $roles->id)->delete();
						
						$permissions = DB::table('permissions')->pluck('id')->toArray();
						
						$data = [];
							foreach ($permissions as $permissionId) {
								$data[] = [
									'permission_id' => $permissionId,
									'role_id' => $roles->id,
								];
							}
						
							DB::table('role_has_permissions')->insert($data);
						}
					
				}*/ 
		
				//echo '<pre>'; print_r($this->user); exit; 
				// if ($admin = User::find(211)) {
				// 	$admin->assignRole('admin');
				// }
				// $user = User::find(3);
				// $user->getRoleNames(); // ["admin"]
				// $user->getAllPermissions(); // should list all permissions
				// $user->hasPermissionTo('edit users');
				// dd($user->permissions);
			
            if ($this->user && $this->user->roles->count() > 0) {
                $this->todoItems 		=	$this->user->todoItems()->select('status', 'position', DB::raw('COUNT(*) as total'))->groupBy('status', 'position')->get(); 
            }
            $this->superSettings 		=	GlobalSetting::with('currency')->first();
           
            $userPermissions = array();

			$getPermissions 		=	$this->user->getRoleNames();
			// dd($getPermissions);
			$this->getPermissions  = $getPermissions;
			$role_row_id =	DB::table('roles')->select('roles.id')->where('roles.company_id', auth()->user()->company_id)->where('roles.name', $getPermissions[0])->first();
			// dd($role_row_id);
			$Rawlist =	DB::table('role_has_permissions')->where('role_id', $role_row_id->id)->pluck('permission_id')->toArray();
			$permissions = DB::table('permissions')->whereIn('id',$Rawlist)->pluck('permissions.name')->toArray();

			// dd($userPermissions);
            $this->userPermissions 		=	$permissions;
			// dd($this->userPermissions);

            //echo '<pre>'; print_r($this->user); exit; 
            //  dd($this->user->company_id);
            $this->global 				= Company::findOrFail($this->user->company_id);
            $this->adminTheme 			= ThemeSetting::where('company_id', '=', $this->global->id)->first();
            $this->companyName 			= $this->global->company_name;
			 $this->companyId 			= $this->global->id;
			$this->global_company_type 	= $this->global->company_type;
 
			$customActivePackage 					=	array();
			if($this->user->company_id)
			{
			
					$activePackageRowCount 			=	DB::table('package_active')->select('package_active.*')->where('package_active.company_id', $this->user->company_id)->count();	
					
					if($activePackageRowCount > 0)
					{
						$activePackageRow 										=	DB::table('package_active')->select('package_active.*')->where('package_active.company_id', $this->user->company_id)->first();	
					
					$customActivePackage['package_invoice_id'] 				=	@$activePackageRow->package_invoice_id;
					$customActivePackage['company_id'] 						=	@$activePackageRow->company_id;
					$customActivePackage['company_type'] 					=	@$activePackageRow->company_type;
					// CVACCESS
					$customActivePackage['cvaccess_startdate'] 				=	@$activePackageRow->cvaccess_startdate;
					$customActivePackage['cvaccess_enddate'] 				=	@$activePackageRow->cvaccess_enddate;
					$customActivePackage['cvaccess_status'] 				=	@$activePackageRow->cvaccess_status;
					$customActivePackage['cvaccess_quantity'] 				=	@$activePackageRow->cvaccess_quantity;
					$customActivePackage['cvaccess_used'] 					=	@$activePackageRow->cvaccess_used;
					$customActivePackage['cvaccess_balance'] 				=	@$activePackageRow->cvaccess_balance;
					//USERS
					$customActivePackage['users_startdate'] 				=	@$activePackageRow->users_startdate;
					$customActivePackage['users_enddate'] 					=	@$activePackageRow->users_enddate;
					$customActivePackage['users_status'] 					=	@$activePackageRow->users_status;
					$customActivePackage['users_quantity'] 					=	@$activePackageRow->users_quantity;
					$customActivePackage['users_used'] 						=	@$activePackageRow->users_used;
					$customActivePackage['users_balance'] 					=	@$activePackageRow->users_balance;
					//NEW CLIENT REQUEST
					$customActivePackage['newclientrequest_startdate']		=	@$activePackageRow->newclientrequest_startdate;
					$customActivePackage['newclientrequest_enddate']		=	@$activePackageRow->newclientrequest_enddate;
					$customActivePackage['newclientrequest_status']			=	@$activePackageRow->newclientrequest_status;
					$customActivePackage['newclientrequest_quantity']		=	@$activePackageRow->newclientrequest_quantity;
					$customActivePackage['newclientrequest_used']			=	@$activePackageRow->newclientrequest_used;
					$customActivePackage['newclientrequest_balance']		=	@$activePackageRow->newclientrequest_balance;
					// NEW JOB REQUEST
					$customActivePackage['newjobrequest_startdate']			=	@$activePackageRow->newjobrequest_startdate;
					$customActivePackage['newjobrequest_enddate']			=	@$activePackageRow->newjobrequest_enddate;
					$customActivePackage['newjobrequest_status']			=	@$activePackageRow->newjobrequest_status;
					$customActivePackage['newjobrequest_quantity']			=	@$activePackageRow->newjobrequest_quantity;
					$customActivePackage['newjobrequest_used']				=	@$activePackageRow->newjobrequest_used;
					$customActivePackage['newjobrequest_balance']			=	@$activePackageRow->newjobrequest_balance;
					// NEW BENCH CLIENT REQUEST
					$customActivePackage['newbenchclientrequest_startdate']	=	@$activePackageRow->newbenchclientrequest_startdate;
					$customActivePackage['newbenchclientrequest_enddate']	=	@$activePackageRow->newbenchclientrequest_enddate;
					$customActivePackage['newbenchclientrequest_status']	=	@$activePackageRow->newbenchclientrequest_status;
					$customActivePackage['newbenchclientrequest_quantity'] 	=	@$activePackageRow->newbenchclientrequest_quantity;
					$customActivePackage['newbenchclientrequest_used']	 	=	@$activePackageRow->newbenchclientrequest_used;
					$customActivePackage['newbenchclientrequest_balance']	=	@$activePackageRow->newbenchclientrequest_balance;
					// EMAILS
					$customActivePackage['emails_startdate']				=	@$activePackageRow->emails_startdate;
					$customActivePackage['emails_enddate']	 				=	@$activePackageRow->emails_enddate;
					$customActivePackage['emails_status']	 				=	@$activePackageRow->emails_status;
					$customActivePackage['emails_quantity'] 				=	@$activePackageRow->emails_quantity;
					$customActivePackage['emails_used']	 					=	@$activePackageRow->emails_used;
					$customActivePackage['emails_balance']	 				=	@$activePackageRow->emails_balance;
					// OLD CVACCESS
					$customActivePackage['oldcvaccess_startdate']			=	@$activePackageRow->oldcvaccess_startdate;
					$customActivePackage['oldcvaccess_enddate']	 			=	@$activePackageRow->oldcvaccess_enddate;
					$customActivePackage['oldcvaccess_status']	 			=	@$activePackageRow->oldcvaccess_status;
					$customActivePackage['oldcvaccess_quantity'] 			=	@$activePackageRow->oldcvaccess_quantity;
					$customActivePackage['oldcvaccess_used']	 			=	@$activePackageRow->oldcvaccess_used;
					$customActivePackage['oldcvaccess_balance']	 			=	@$activePackageRow->oldcvaccess_balance;	
					}
					else
					{


// ACCESS DEFAULT PACKAGE

$companiesRow 					=	DB::table('companies')->select('companies.company_type')->where('companies.id', $this->user->company_id)->first();	


$today 							= 	date('Y-m-d');
$package_default 				=	DB:: table('package_default')->select('package_default.*')->first(); 

$cvaccess_endDate		=	'';				
if(@$package_default->cvaccess_validity > 0){
	$cvaccess_endDate	=	date('Y-m-d', strtotime(date('Y-m-d'). ' + '.@$package_default->cvaccess_validity.' days'));
}else{
	$cvaccess_endDate	=	date('Y-m-d', strtotime(date('Y-m-d'). ' + 0 days'));
}
$users_endDate		=	'';				
if(@$package_default->users_validity > 0){
	$users_endDate	=	date('Y-m-d', strtotime(date('Y-m-d'). ' + '.@$package_default->users_validity.' days'));
}else{
	$users_endDate	=	date('Y-m-d', strtotime(date('Y-m-d'). ' + 0 days'));
}
$newclientrequest_endDate		=	'';				
if(@$package_default->newclientrequest_validity > 0){
	$newclientrequest_endDate	=	date('Y-m-d', strtotime(date('Y-m-d'). ' + '.@$package_default->newclientrequest_validity.' days'));
}else{
	$newclientrequest_endDate	=	date('Y-m-d', strtotime(date('Y-m-d'). ' + 0 days'));
}
$newjobrequest_endDate		=	'';				
if(@$package_default->newjobrequest_validity > 0){
	$newjobrequest_endDate	=	date('Y-m-d', strtotime(date('Y-m-d'). ' + '.@$package_default->newjobrequest_validity.' days'));
}else{
	$newjobrequest_endDate	=	date('Y-m-d', strtotime(date('Y-m-d'). ' + 0 days'));
}
$newbenchclientrequest_endDate		=	'';				
if(@$package_default->newbenchclientrequest_validity > 0){
	$newbenchclientrequest_endDate	=	date('Y-m-d', strtotime(date('Y-m-d'). ' + '.@$package_default->newbenchclientrequest_validity.' days'));
}else{
	$newbenchclientrequest_endDate	=	date('Y-m-d', strtotime(date('Y-m-d'). ' + 0 days'));
}
$emails_endDate		=	'';				
if(@$package_default->emails_validity > 0){
	$emails_endDate	=	date('Y-m-d', strtotime(date('Y-m-d'). ' + '.@$package_default->emails_validity.' days'));
}else{
	$emails_endDate	=	date('Y-m-d', strtotime(date('Y-m-d'). ' + 0 days'));
}
$oldcvaccess_endDate		=	'';				
if(@$package_default->oldcvaccess_validity > 0){
	$oldcvaccess_endDate	=	date('Y-m-d', strtotime(date('Y-m-d'). ' + '.@$package_default->oldcvaccess_validity.' days'));
}else{
	$oldcvaccess_endDate	=	date('Y-m-d', strtotime(date('Y-m-d'). ' + 0 days'));
}
$package_arr 				=	array(
																'company_id' 						=>	$this->user->company_id,
																'company_type'						=>	@$companiesRow->company_type,	
																
																'cvaccess_startdate'				=>	date('Y-m-d'),
																'cvaccess_enddate'					=>	$cvaccess_endDate,
																'cvaccess_status'					=>	$package_default->cvaccess_status,
																'cvaccess_quantity'					=>	$package_default->cvaccess_quantity,
																'cvaccess_used'						=>	0,
																'cvaccess_balance'					=>	$package_default->cvaccess_quantity,
																
																'users_startdate'					=>	date('Y-m-d'),
																'users_enddate'						=>	$users_endDate,
																'users_status'						=>	$package_default->users_status,
																'users_quantity'					=>	$package_default->users_quantity,
																'users_used'						=>	0,
																'users_balance'						=>	$package_default->users_quantity,
																																
																'newclientrequest_startdate'		=>	date('Y-m-d'),
																'newclientrequest_enddate'			=>	$newclientrequest_endDate,
																'newclientrequest_status'			=>	$package_default->newclientrequest_status,
																'newclientrequest_quantity'			=>	$package_default->newclientrequest_quantity,
																'newclientrequest_used'				=>	0,
																'newclientrequest_balance'			=>	$package_default->newclientrequest_quantity,																
	
																'newclientrequest_startdate'		=>	date('Y-m-d'),
																'newclientrequest_enddate'			=>	$newclientrequest_endDate,
																'newclientrequest_status'			=>	$package_default->newclientrequest_status,
																'newclientrequest_quantity'			=>	$package_default->newclientrequest_quantity,
																'newclientrequest_used'				=>	0,
																'newclientrequest_balance'			=>	$package_default->newclientrequest_quantity,		
	  
																'newjobrequest_startdate'			=>	date('Y-m-d'),
																'newjobrequest_enddate'				=>	$newjobrequest_endDate,
																'newjobrequest_status'				=>	$package_default->newjobrequest_status,
																'newjobrequest_quantity'			=>	$package_default->newjobrequest_quantity,
																'newjobrequest_used'				=>	0,
																'newjobrequest_balance'				=>	$package_default->newjobrequest_quantity, 		 
																				
																'newbenchclientrequest_startdate'	=>	date('Y-m-d'),
																'newbenchclientrequest_enddate'		=>	$newbenchclientrequest_endDate,
																'newbenchclientrequest_status'		=>	$package_default->newbenchclientrequest_status,
																'newbenchclientrequest_quantity'	=>	$package_default->newbenchclientrequest_quantity,
																'newbenchclientrequest_used'		=>	0,
																'newbenchclientrequest_balance'		=>	$package_default->newbenchclientrequest_quantity, 		 
																		
																'emails_startdate'					=>	date('Y-m-d'),
																'emails_enddate'					=>	$emails_endDate,
																'emails_status'						=>	$package_default->emails_status,
																'emails_quantity'					=>	$package_default->emails_quantity,
																'emails_used'						=>	0,
																'emails_balance'					=>	$package_default->emails_quantity, 	
	
																'oldcvaccess_startdate'				=>	date('Y-m-d'),
																'oldcvaccess_enddate'				=>	$oldcvaccess_endDate,
																'oldcvaccess_status'				=>	$package_default->oldcvaccess_status,
																'oldcvaccess_quantity'				=>	$package_default->oldcvaccess_quantity,
																'oldcvaccess_used'					=>	0,
																'oldcvaccess_balance'				=>	$package_default->oldcvaccess_quantity,
																
																'create_at' 						=> date('Y-m-d'),
																'update_at' 						=> date('Y-m-d')
														 );
DB::table('package_active')->insert($package_arr);




						
					}
					
									
			}
			$this->customActivePackage 			=	$customActivePackage;

            $this->languageSettings 			=	LanguageSetting::where('status', 'enabled')->orderBy('language_name')->get();
            $this->activePackage 				=	CompanyPackage::with('package')->where('company_id', $this->user->company_id)
                ->where('status', 'active')
                ->where(function($query){
                    $query->where(DB::raw('DATE(end_date)'), '>=', DB::raw('CURDATE()'));
                    $query->orWhereNull('end_date');
                })
                ->first();
    
            App::setLocale($this->global->locale);
            Carbon::setLocale($this->global->locale);
            setlocale(LC_TIME,$this->global->locale.'_'.strtoupper($this->global->locale));
            $this->linkedinGlobal = LinkedInSetting::first();

            $this->stickyNotes = StickyNote::where('user_id', $this->user->id)
                ->orderBy('updated_at', 'desc')
                ->get();
				
                // dd($this->user);
				//dd($this->user); exit; 
				
			   // dd($request);  
				return $next($request);
			
				//return view('service_company.team.index', $this->data);
				//exit; 
		
        });
		
		
    }


    public function generateTodoView()
    {
        $pendingTodos = $this->user->todoItems()->status('pending')->orderBy('position', 'DESC')->limit(5)->get();
        $completedTodos = $this->user->todoItems()->status('completed')->orderBy('position', 'DESC')->limit(5)->get();

        $view = view('sections.service_company.todo_items_list', compact('pendingTodos', 'completedTodos'))->render();

        return $view;
    }
}
