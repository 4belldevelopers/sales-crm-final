<?php
/*** ########################################################
Controller    	: 	AdminLinkedInSettingsController
Purmose			: 
Author			:	Abu
Company			:	4Bell
Date			:	05-April-2021	
########################################################***/

namespace App\Http\Controllers\StaffingCompany;

use App\Models\Company;
use App\Helper\Reply;
use App\Models\LinkedInSetting;
use Illuminate\Http\Request;

class StaffingCompanyLinkedInSettingsController extends StaffingCompanyBaseController
{
    public function __construct()
    {
        parent::__construct();
        $this->pageTitle ='menu.linkedInSettings';
        $this->pageIcon = 'icon-settings';
    }

    public function index()
		{
        $linkedinGlobal = LinkedInSetting::first();
        if($linkedinGlobal->status == 'disable')
        {
            abort(404);
        }
        $this->linkedInSetting = Company::select('id', 'linkedin')->where('id', $this->user->company_id)->first();
        return view('staffing_company.linked-in-settings.edit', $this->data);
    }

    public function update(Request $request, $id)
		{
        /*dd($request->all());*/
        $company = Company::findOrFail($id);
        $company->linkedin = $request->status == "on" ? 'enable' : 'disable';

        $company->save();

        return Reply::redirect(route('staffing_company.linkedin-settings.index'), __('menu.settings').' '.__('messages.updatedSuccessfully'));
    }
}
