<?php

namespace App\Http\Controllers;

use Froiden\Envato\Traits\AppBoot;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Models\GlobalSetting;
use App\Models\SmsSetting;
use App\Models\ThemeSetting;
use Illuminate\Support\Arr;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests,AppBoot;

    public function __construct()
    {
        $this->showInstall();

        $this->global = GlobalSetting::first();
        $this->smsSettings = SmsSetting::first();
        $this->adminTheme = ThemeSetting::whereNull('company_id')->first();
        
        config(['app.name' => $this->global?->company_name ?? config('app.name')]);
        config(['app.url' => url('/')]);
        view()->share('smsSettings', $this->smsSettings);
        
        $this->middleware(function ($request, $next) {
            $this->user = auth()->user();
            if ($this->user && $this->user->is_superadmin) {
                config(['froiden_envato.allow_users_id' => true]);
            }

            return $next($request);
        });
        
        
    }
    public function getCallingCodes()
    {
        $codes = [];
        $callingCodes = config('calling_codes.codes');
        if (is_array($callingCodes)) {
            foreach ($callingCodes as $code) {
                $codes = Arr::add($codes, $code['code'], [
                    'name' => $code['name'],
                    'dial_code' => $code['dial_code'],
                ]);
            }
        }
        return $codes;
    }
}
