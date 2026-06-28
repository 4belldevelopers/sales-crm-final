<?php
/*** ########################################################
Controller    	: 	AdminBaseController
Purmose			: 
Author			:	Abu
Company			:	4Bell
Date			:	05-April-2021	
########################################################** */

namespace App\Http\Controllers\Admin;

use App\LanguageSetting;
use App\LinkedInSetting;
use App\StickyNote;
use Carbon\Carbon;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\App;
use App\Models\ThemeSetting;
use App\Models\Company;
use App\Models\User;
use App\Models\CompanyPackage;
use Illuminate\Support\Facades\DB;
use App\GlobalSetting;
use Illuminate\Support\Facades\Route;

//use Illuminate\Foundation\Auth\AuthenticatesUsers; 

class AdminBaseController extends Controller
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
//        $this->emailSetting = EmailNotificationSetting::all();
        $this->middleware(function ($request, $next) 
		{
        
		    $this->user = auth()->user();
			 
			
//Prevent multiple login  - START 
$session_token	=	'';				
$all			=	session()->all(); 
$session_token	=	$all['_token'];
$user_id		=	$this->user->id;	
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
		
		
		
		
		
		
		
		
		
		
		
		
		
				//echo '<pre>'; print_r($this->user); exit; 
				
				
            if ($this->user && $this->user->roles->count() > 0) {
                $this->todoItems = $this->user->todoItems()->groupBy('status', 'position')->get();
            }
            $this->superSettings = GlobalSetting::with('currency')->first();
           
            $this->getPermissions = User::with('roles.permissions.permission')->find($this->user->id);
            $userPermissions = array();
            foreach ($this->getPermissions->roles[0]->permissions as $key => $value) {
                $userPermissions[] = $value->permission->name;
            }
            $this->userPermissions = $userPermissions;

            $this->global 				= Company::findOrFail($this->user->company_id);
            $this->adminTheme 			= ThemeSetting::where('company_id', '=', $this->global->id)->first();
            $this->companyName 			= $this->global->company_name;
			$this->global_company_type 	= $this->global->company_type;





            $this->languageSettings = LanguageSetting::where('status', 'enabled')->orderBy('language_name')->get();
            $this->activePackage = CompanyPackage::with('package')->where('company_id', $this->user->company_id)
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
            return $next($request);
			
		
		
        });
		
	
	
		
    }













    public function generateTodoView()
    {
        $pendingTodos = $this->user->todoItems()->status('pending')->orderBy('position', 'DESC')->limit(5)->get();
        $completedTodos = $this->user->todoItems()->status('completed')->orderBy('position', 'DESC')->limit(5)->get();

        $view = view('sections.todo_items_list', compact('pendingTodos', 'completedTodos'))->render();

        return $view;
    }
}
