<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Helper\Files;
use App\Helper\Reply;
use App\Models\LanguageSetting;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Models\Company;
use App\Models\GlobalSetting;
use App\Models\Currency;

class GlobalSettingController extends SuperAdminBaseController
{
    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = 'menu.settings';
        $this->pageIcon = 'icon-settings';
    }
    public function index(){
        $this->currencies = Currency::all();
        $this->sold_by_address = DB::table('sold_by_address')->where('id',1)->first();
        $this->countries = DB::table('countries')
        ->select('countries.*')
        ->get();
        $this->countrie_id = '';
        $this->state_id = '';
        $this->city_id = '';

        if($this->sold_by_address){
            $this->countrie_id = $this->sold_by_address->country_id;
            $this->state_id = $this->sold_by_address->state_id;
            $this->city_id = $this->sold_by_address->city;
        }
        // dd( $this->data);
        return view('super-admin.global-settings.edit', $this->data);
    }
    public function updatePr(Request $request){
        $setting = GlobalSetting::first();

        // dd($request->input('company_name'),$request->all());
        $setting->company_name = $request->input('company_name');
        $setting->company_email = $request->input('company_email');
        $setting->company_phone = $request->input('company_phone');
        $setting->website = $request->input('website');
        $setting->address = $request->input('address');
        $setting->currency_id = $request->input('currency_id');
        $setting->locale = $request->input('locale');
        $setting->google_recaptcha_key = $request->input('google_recaptcha_key');
        $setting->system_update = $request->has('system_update') && $request->input('system_update') == 'on' ? 1 : 0;;
        $setting->delete_account_hour_day = $request->has('hoursDays');
        $setting->delete_account_in = $request->has('delete_account_in');

        if ($request->hasFile('logo')) {
             // Define the upload path
                $uploadPath = base_path('user-uploads/global-logo');

                // Ensure the directory exists
                if (!file_exists($uploadPath)) {
                    mkdir($uploadPath, 0777, true);
                }

                // Delete the old file if it exists
                if ($setting->logo) {
                    $oldFilePath = base_path('user-uploads/global-logo/' . $setting->logo);
                    if (file_exists($oldFilePath)) {
                        unlink($oldFilePath);
                    }
                }
                // dd($uploadPath);

                // Upload the new file
                $file = $request->file('logo');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $file->move($uploadPath, $fileName);

                // Save the file name to the database
                $setting->logo = $fileName;
        }


        $setting->save();

        DB::table('sold_by_address')->where('id',1)->update(['country_id'=>$request->country,'first_name'=>$request->first_name,'last_name'=>$request->last_name,'phone'=>$request->company_phone,'building'=>$request->building,'street'=>$request->address,'state_id'=>$request->state_id,'city'=>$request->city,'postal_code'=>$request->postal_code,'organization'=>$request->company_name,'gstin'=>$request->gstin,'pan_no'=>$request->pan_no]);


        // dd("djd",$uploadPath, $fileName);
        

        return Reply::redirect(route('superadmin.global-settings.index'), __('menu.settings').' '.__('messages.updatedSuccessfully'));
    }
    public function update(Request $request,$id){
        $setting = GlobalSetting::first();

        // dd($request->all());
        $setting->company_name = $request->input('company_name');
        $setting->company_email = $request->input('company_email');
        $setting->company_phone = $request->input('company_phone');
        $setting->website = $request->input('website');
        $setting->address = $request->input('address');
        $setting->currency_id = $request->input('currency_id');
        $setting->locale = $request->input('locale');
        $setting->google_recaptcha_key = $request->input('google_recaptcha_key');
        $setting->system_update = $request->has('system_update') && $request->input('system_update') == 'on' ? 1 : 0;;
        $setting->delete_account_hour_day = $request->has('hoursDays');
        $setting->delete_account_in = $request->has('delete_account_in');

        if ($request->hasFile('logo')) {
            Files::deleteFile($setting->logo,'global-logo');
            $setting->logo = Files::upload($request->logo,'global-logo');
        }

        $setting->save();

        return Reply::redirect(route('superadmin.global-settings.index'), __('menu.settings').' '.__('messages.updatedSuccessfully'));
    }
    public function changeLanguage(Request $request) {
        $setting = Company::first();
        $setting->locale = $request->input('lang');
        $setting->save();

        return Reply::success('Language changed successfully.');
    }
}
