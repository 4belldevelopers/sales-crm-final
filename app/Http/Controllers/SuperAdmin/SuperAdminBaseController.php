<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Models\EmailNotificationSetting;
use App\Models\LanguageSetting;
use App\Models\Notification;
use App\Models\ProjectActivity;
use App\Models\Setting;
use App\Models\StickyNote;
use App\Traits\FileSystemSettingTrait;
use App\Models\UniversalSearch;
use App\Models\UserActivity;
use App\Models\UserChat;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Validator;
use SebastianBergmann\CodeCoverage\Report\Xml\Project;
use App\Models\ThemeSetting;
use App\Models\Company;
use App\GlobalSetting;

class SuperAdminBaseController extends Controller
{
    /**
     * @var array
     */
    public $data = [];

    /**
     * @param $name
     * @param $value
     */
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


        $this->recruitPlugins = recruit_plugins();

        $this->middleware(function ($request, $next) {
            $this->user = auth()->user();
            $this->adminTheme = ThemeSetting::whereNull('company_id')->first();
            $this->companyName = $this->global->company_name;

            $this->languageSettings = LanguageSetting::where('status', 'enabled')->orderBy('language_name')->get();
    
            App::setLocale($this->global->locale);
            Carbon::setLocale($this->global->locale);
            setlocale(LC_TIME,$this->global->locale.'_'.strtoupper($this->global->locale));
            $this->stickyNotes = StickyNote::where('user_id', $this->user->id)
                ->orderBy('updated_at', 'desc')
                ->get();

            view()->share('languages', $this->languageSettings);
            view()->share('global', $this->global);
        
            return $next($request);
        });


    }
    
}
