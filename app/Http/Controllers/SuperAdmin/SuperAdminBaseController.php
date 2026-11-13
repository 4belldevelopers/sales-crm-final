<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\LanguageSetting;
use App\Models\StickyNote;
use Carbon\Carbon;
use Illuminate\Support\Facades\App;

class SuperAdminBaseController extends Controller
{
    /**
     * @var array
     */
    public $data = [];

    public function __set($name, $value)
    {
        $this->data[$name] = $value;
    }

    /**
     * @return mixed
     */
    public function __get($name)
    {
        return $this->data[$name];
    }

    /**
     * @return bool
     */
    public function __isset($name)
    {
        return isset($this->data[$name]);
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
            $this->adminTheme = app(\App\Services\SuperAdmin\SuperAdminThemeSettingReadService::class)->getThemeSettings();
            $this->companyName = $this->global->company_name;

            $this->languageSettings = LanguageSetting::where('status', 'enabled')->orderBy('language_name')->get();

            App::setLocale($this->global->locale);
            Carbon::setLocale($this->global->locale);
            setlocale(LC_TIME, $this->global->locale.'_'.strtoupper($this->global->locale));
            $this->stickyNotes = StickyNote::where('user_id', $this->user->id)
                ->orderBy('updated_at', 'desc')
                ->get();

            view()->share('languages', $this->languageSettings);
            view()->share('global', $this->global);

            return $next($request);
        });

    }
}
