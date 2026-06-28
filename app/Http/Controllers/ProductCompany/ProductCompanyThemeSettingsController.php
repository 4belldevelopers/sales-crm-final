<?php
/*** ########################################################
Controller    	: 	AdminthemeSettingsController
Purmose			: 
Author			:	Abu
Company			:	4Bell
Date			:	05-April-2021	
########################################################***/  

namespace App\Http\Controllers\ProductCompany;

use App\Helper\Files;
use App\Helper\Reply;
use App\ThemeSetting;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ProductCompanyThemeSettingsController extends ProductCompanyBaseController
{
    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = 'menu.themeSettings';
        $this->pageIcon = 'ti-settings';
    }

    public function index()
		{
        return view('product_company.theme-settings.index', $this->data);
    }

    public function store(Request $request)
		{
        $theme = ThemeSetting::where('company_id', '=', $this->global->id)->first();
        $theme->primary_color = $request->primary_color;
        $theme->front_custom_css =  $request->front_custom_css;
        $theme->admin_custom_css =  $request->admin_custom_css;

        $oldImage = $theme->home_background_image;

        if ($request->hasFile('image')) {
            $theme->home_background_image = Files::upload($request->image, 'background');

            $path = 'user-uploads/background' . '/' . $oldImage;

            if(\File::exists($path))
            {
                Files::deleteFile($oldImage, 'background');
            }
        }

        $theme->save();

        return Reply::redirect(route('product_company.theme-settings.index'), __('menu.themeSettings').' '.__('messages.updatedSuccessfully'));
    }
}
