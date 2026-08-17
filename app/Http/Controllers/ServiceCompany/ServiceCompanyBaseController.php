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
use Illuminate\Support\Facades\Cache;
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
        return $this->data[$name] ?? null;
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
            $this->superSettings        = Cache::remember('global_settings', 86400, fn() => GlobalSetting::with('currency')->first());
           
            $userPermissions = array();

			$getPermissions 		=	$this->user->getRoleNames();
			$this->getPermissions  = $getPermissions;

			// V3 Phase 1.1: Cache 3 permission SQL queries → 0 per request for 1 hour.
			// Key = user_id + company_id so a user switching companies never gets stale permissions.
			// Bust "user_permissions:{userId}:{companyId}" when an admin reassigns this user's role.
			$this->userPermissions = Cache::remember(
				"user_permissions:{$this->user->id}:{$this->user->company_id}",
				3600,
				function () use ($getPermissions) {
					if (empty($getPermissions[0])) return [];

					$role_row_id = DB::table('roles')
						->select('roles.id')
						->where('roles.company_id', auth()->user()->company_id)
						->where('roles.name', $getPermissions[0])
						->first();

					if (!$role_row_id) return [];

					$Rawlist = DB::table('role_has_permissions')
						->where('role_id', $role_row_id->id)
						->pluck('permission_id')
						->toArray();

					return DB::table('permissions')
						->whereIn('id', $Rawlist)
						->pluck('permissions.name')
						->toArray();
				}
			);
			// dd($this->userPermissions);

            //echo '<pre>'; print_r($this->user); exit; 
            //  dd($this->user->company_id);
            $this->global               = $this->client ?? Company::findOrFail($this->user->company_id);
            $this->adminTheme 			= ThemeSetting::where('company_id', '=', $this->global->id)->first();
            $this->companyName 			= $this->global->company_name;
			 $this->companyId 			= $this->global->id;
			$this->global_company_type 	= $this->global->company_type;
 
			// V3 Phase 1.2: Cache package_active lookup per company (1h TTL) — was 2 SQL queries per request.
			// On a company's very first login (no package_active row), the default package is provisioned
			// and the result cached so subsequent requests cost 0 SQL queries.
			// Bust "company_package_active:{companyId}" when a package is purchased or updated.
			$customActivePackage = [];
			if ($this->user->company_id) {
				$_companyId = $this->user->company_id;
				$customActivePackage = Cache::remember(
					"company_package_active:{$_companyId}",
					3600,
					function () use ($_companyId) {
						$activePackageRow = DB::table('package_active')
							->where('company_id', $_companyId)
							->first();

						if (!$activePackageRow) {
							// First login — provision from package_default.
							$companiesRow    = DB::table('companies')->select('company_type')->where('id', $_companyId)->first();
							$package_default = DB::table('package_default')->first();
							$today           = date('Y-m-d');
							$endDate         = fn($v) => $v > 0
								? date('Y-m-d', strtotime($today . ' + ' . (int)$v . ' days'))
								: $today;

							DB::table('package_active')->insert([
								'company_id'                        => $_companyId,
								'company_type'                      => @$companiesRow->company_type,
								'cvaccess_startdate'                => $today,
								'cvaccess_enddate'                  => $endDate(@$package_default->cvaccess_validity),
								'cvaccess_status'                   => @$package_default->cvaccess_status,
								'cvaccess_quantity'                 => @$package_default->cvaccess_quantity,
								'cvaccess_used'                     => 0,
								'cvaccess_balance'                  => @$package_default->cvaccess_quantity,
								'users_startdate'                   => $today,
								'users_enddate'                     => $endDate(@$package_default->users_validity),
								'users_status'                      => @$package_default->users_status,
								'users_quantity'                    => @$package_default->users_quantity,
								'users_used'                        => 0,
								'users_balance'                     => @$package_default->users_quantity,
								'newclientrequest_startdate'        => $today,
								'newclientrequest_enddate'          => $endDate(@$package_default->newclientrequest_validity),
								'newclientrequest_status'           => @$package_default->newclientrequest_status,
								'newclientrequest_quantity'         => @$package_default->newclientrequest_quantity,
								'newclientrequest_used'             => 0,
								'newclientrequest_balance'          => @$package_default->newclientrequest_quantity,
								'newjobrequest_startdate'           => $today,
								'newjobrequest_enddate'             => $endDate(@$package_default->newjobrequest_validity),
								'newjobrequest_status'              => @$package_default->newjobrequest_status,
								'newjobrequest_quantity'            => @$package_default->newjobrequest_quantity,
								'newjobrequest_used'                => 0,
								'newjobrequest_balance'             => @$package_default->newjobrequest_quantity,
								'newbenchclientrequest_startdate'   => $today,
								'newbenchclientrequest_enddate'     => $endDate(@$package_default->newbenchclientrequest_validity),
								'newbenchclientrequest_status'      => @$package_default->newbenchclientrequest_status,
								'newbenchclientrequest_quantity'    => @$package_default->newbenchclientrequest_quantity,
								'newbenchclientrequest_used'        => 0,
								'newbenchclientrequest_balance'     => @$package_default->newbenchclientrequest_quantity,
								'emails_startdate'                  => $today,
								'emails_enddate'                    => $endDate(@$package_default->emails_validity),
								'emails_status'                     => @$package_default->emails_status,
								'emails_quantity'                   => @$package_default->emails_quantity,
								'emails_used'                       => 0,
								'emails_balance'                    => @$package_default->emails_quantity,
								'oldcvaccess_startdate'             => $today,
								'oldcvaccess_enddate'               => $endDate(@$package_default->oldcvaccess_validity),
								'oldcvaccess_status'                => @$package_default->oldcvaccess_status,
								'oldcvaccess_quantity'              => @$package_default->oldcvaccess_quantity,
								'oldcvaccess_used'                  => 0,
								'oldcvaccess_balance'               => @$package_default->oldcvaccess_quantity,
								'create_at'                         => $today,
								'update_at'                         => $today,
							]);

							$activePackageRow = DB::table('package_active')->where('company_id', $_companyId)->first();
							if (!$activePackageRow) return [];
						}

						return [
							'package_invoice_id'              => @$activePackageRow->package_invoice_id,
							'company_id'                      => @$activePackageRow->company_id,
							'company_type'                    => @$activePackageRow->company_type,
							'cvaccess_startdate'              => @$activePackageRow->cvaccess_startdate,
							'cvaccess_enddate'                => @$activePackageRow->cvaccess_enddate,
							'cvaccess_status'                 => @$activePackageRow->cvaccess_status,
							'cvaccess_quantity'               => @$activePackageRow->cvaccess_quantity,
							'cvaccess_used'                   => @$activePackageRow->cvaccess_used,
							'cvaccess_balance'                => @$activePackageRow->cvaccess_balance,
							'users_startdate'                 => @$activePackageRow->users_startdate,
							'users_enddate'                   => @$activePackageRow->users_enddate,
							'users_status'                    => @$activePackageRow->users_status,
							'users_quantity'                  => @$activePackageRow->users_quantity,
							'users_used'                      => @$activePackageRow->users_used,
							'users_balance'                   => @$activePackageRow->users_balance,
							'newclientrequest_startdate'      => @$activePackageRow->newclientrequest_startdate,
							'newclientrequest_enddate'        => @$activePackageRow->newclientrequest_enddate,
							'newclientrequest_status'         => @$activePackageRow->newclientrequest_status,
							'newclientrequest_quantity'       => @$activePackageRow->newclientrequest_quantity,
							'newclientrequest_used'           => @$activePackageRow->newclientrequest_used,
							'newclientrequest_balance'        => @$activePackageRow->newclientrequest_balance,
							'newjobrequest_startdate'         => @$activePackageRow->newjobrequest_startdate,
							'newjobrequest_enddate'           => @$activePackageRow->newjobrequest_enddate,
							'newjobrequest_status'            => @$activePackageRow->newjobrequest_status,
							'newjobrequest_quantity'          => @$activePackageRow->newjobrequest_quantity,
							'newjobrequest_used'              => @$activePackageRow->newjobrequest_used,
							'newjobrequest_balance'           => @$activePackageRow->newjobrequest_balance,
							'newbenchclientrequest_startdate' => @$activePackageRow->newbenchclientrequest_startdate,
							'newbenchclientrequest_enddate'   => @$activePackageRow->newbenchclientrequest_enddate,
							'newbenchclientrequest_status'    => @$activePackageRow->newbenchclientrequest_status,
							'newbenchclientrequest_quantity'  => @$activePackageRow->newbenchclientrequest_quantity,
							'newbenchclientrequest_used'      => @$activePackageRow->newbenchclientrequest_used,
							'newbenchclientrequest_balance'   => @$activePackageRow->newbenchclientrequest_balance,
							'emails_startdate'                => @$activePackageRow->emails_startdate,
							'emails_enddate'                  => @$activePackageRow->emails_enddate,
							'emails_status'                   => @$activePackageRow->emails_status,
							'emails_quantity'                 => @$activePackageRow->emails_quantity,
							'emails_used'                     => @$activePackageRow->emails_used,
							'emails_balance'                  => @$activePackageRow->emails_balance,
							'oldcvaccess_startdate'           => @$activePackageRow->oldcvaccess_startdate,
							'oldcvaccess_enddate'             => @$activePackageRow->oldcvaccess_enddate,
							'oldcvaccess_status'              => @$activePackageRow->oldcvaccess_status,
							'oldcvaccess_quantity'            => @$activePackageRow->oldcvaccess_quantity,
							'oldcvaccess_used'                => @$activePackageRow->oldcvaccess_used,
							'oldcvaccess_balance'             => @$activePackageRow->oldcvaccess_balance,
						];
					}
				);
			}
			$this->customActivePackage = $customActivePackage;

            $this->languageSettings         = Cache::remember('language_settings_enabled', 86400, fn() => LanguageSetting::where('status', 'enabled')->orderBy('language_name')->get());
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
