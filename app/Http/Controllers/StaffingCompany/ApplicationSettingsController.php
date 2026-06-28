<?php
/***########################################################
Controller    	: 	ApplicationSettingsController
Purmose			: 
Author			:	Abu
Company			:	4Bell
Date			:	05-April-2021	
########################################################***/

namespace App\Http\Controllers\StaffingCompany;

use App\Models\ApplicationSetting;
use App\Helper\Reply;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class ApplicationSettingsController extends StaffingCompanyBaseController
{

    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = 'menu.applicationFormSettings';
        $this->pageIcon = 'icon-settings';
        $this->setting = ApplicationSetting::first();
    }
    public function index()
    {
        abort_if(! $this->user->cans('manage_settings'), 403);

        $this->timezones = \DateTimeZone::listIdentifiers(\DateTimeZone::ALL);
        $this->settings = ApplicationSetting::where('company_id', user()->company_id)->first();
        if(!$this->setting){
            abort(404);
        }

        return view('staffing_company.application-setting.index', $this->data);
    }
    public function create()
    {
        $result = $this->setting;
        return $result;
    }
    public function store(Request $request)
    {
        //
    }
    public function show($id)
    {
        //
    }
    public function edit($id)
    {
        //
    }
    public function update(Request $request, $id)
    {
        abort_if(! $this->user->cans('manage_settings'), 403);
        $mailSetting = [];

        foreach ($this->setting->mail_setting as $id => $setting) {
            $setting['status'] = false;
            if ($request->has('checkBoardColumn') && in_array($id, $request->checkBoardColumn)) {
                $setting['status'] = true;
            }
            $mailSetting = Arr::add($mailSetting, $id, $setting);
        }

        $this->setting->legal_term = $request->input('legal_term');
        $this->setting->mail_setting = $mailSetting;

        $this->setting->save();


        return Reply::success(__('messages.applicationSetting.settingUpdated'));
    }
    public function destroy($id)
    {
        //
    }
}
