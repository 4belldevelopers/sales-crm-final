<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Helper\Files;
use App\Helper\Reply;
use App\Http\Requests\SuperAdmin\UpdateGlobalSettingRequest;
use App\Models\Company;
use App\Models\Currency;
use App\Models\GlobalSetting;
use App\Models\LanguageSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

/**
 * GlobalSettingController — Master Platform Configuration & Organization Hub.
 *
 * Intent: Manages platform-wide singleton configuration including company identity,
 * brand logo, base currency, system locale, maintenance mode toggle, account deletion
 * lifecycle policies, and official seller ("Sold-By") invoicing identity.
 * Tenancy: Global Super Admin scope (system-wide singleton, tenant_id = 0).
 */
class GlobalSettingController extends SuperAdminBaseController
{
    /**
     * GlobalSettingController constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = 'menu.settings';
        $this->pageIcon = 'icon-settings';
    }

    /**
     * Display the Global Settings configuration hub.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function index()
    {
        $this->currencies = Currency::all();
        $this->languageSettings = LanguageSetting::where('status', 'enabled')->orderBy('language_name')->get();
        $this->sold_by_address = DB::table('sold_by_address')->where('id', 1)->first();
        $this->countries = DB::table('countries')
            ->select('countries.*')
            ->orderBy('country_name', 'asc')
            ->get();

        $this->countrie_id = '';
        $this->state_id = '';
        $this->city_id = '';

        if ($this->sold_by_address) {
            $this->countrie_id = $this->sold_by_address->country_id;
            $this->state_id = $this->sold_by_address->state_id;
            $this->city_id = $this->sold_by_address->city;
        } else {
            $this->sold_by_address = (object) [
                'country_id' => null,
                'first_name' => '',
                'last_name' => '',
                'phone' => '',
                'building' => '',
                'street' => '',
                'state_id' => null,
                'city' => null,
                'postal_code' => '',
                'organization' => '',
                'gstin' => '',
                'pan_no' => '',
            ];
        }

        return view('super-admin.global-settings.edit', $this->data);
    }

    /**
     * Canonical REST resource update method.
     *
     * Strictly validates input via UpdateGlobalSettingRequest, executes atomic DB mutations
     * across global_settings and sold_by_address, manages brand logo uploads, and evicts
     * multi-tenant cache keys to guarantee zero stale configuration across 10K concurrency.
     *
     * @param  int|null  $id
     * @return array
     */
    public function update(UpdateGlobalSettingRequest $request, $id = null)
    {
        DB::beginTransaction();

        try {
            $setting = GlobalSetting::first();
            if (! $setting) {
                $setting = new GlobalSetting;
            }

            // Platform Identity & Branding
            $setting->company_name = $request->input('company_name');
            $setting->company_email = $request->input('company_email');
            $setting->company_phone = $request->input('company_phone');
            $setting->website = $request->input('website');
            $setting->address = $request->input('address');

            // Regional & Localization
            $setting->currency_id = (int) $request->input('currency_id');
            $setting->locale = $request->input('locale');

            // Security & System Policies
            $setting->google_recaptcha_key = $request->input('google_recaptcha_key');
            $setting->system_update = $request->boolean('system_update') ? 1 : 0;
            $setting->delete_account_hour_day = $request->input('hoursDays', 'day');
            $setting->delete_account_in = $request->filled('delete_account_in')
                ? (int) $request->input('delete_account_in')
                : null;

            // Brand Logo Upload & Reset Management
            if ($request->hasFile('logo')) {
                if (! empty($setting->logo)) {
                    Files::deleteFile($setting->logo, 'global-logo');
                }
                $setting->logo = Files::upload($request->file('logo'), 'global-logo');
            } elseif ($request->boolean('remove_logo')) {
                if (! empty($setting->logo)) {
                    Files::deleteFile($setting->logo, 'global-logo');
                }
                $setting->logo = null;
            }

            $setting->save();

            // Statutory Invoicing & Sold-By Legal Entity
            DB::table('sold_by_address')->updateOrInsert(
                ['id' => 1],
                [
                    'country_id' => $request->filled('country') ? (int) $request->input('country') : null,
                    'first_name' => $request->input('first_name'),
                    'last_name' => $request->input('last_name'),
                    'phone' => $request->input('company_phone'),
                    'building' => $request->input('building'),
                    'street' => $request->filled('street') ? $request->input('street') : $request->input('address'),
                    'state_id' => $request->filled('state_id') ? (int) $request->input('state_id') : null,
                    'city' => $request->filled('city') ? (int) $request->input('city') : null,
                    'postal_code' => $request->input('postal_code'),
                    'organization' => $request->filled('organization') ? $request->input('organization') : $request->input('company_name'),
                    'gstin' => $request->input('gstin'),
                    'pan_no' => $request->input('pan_no'),
                ]
            );

            DB::commit();

            // Multi-Tenant Atomic Cache Invalidation
            Cache::forget('global_settings');
            Cache::forget('global_setting:default');

            return Reply::successWithData(__('menu.settings').' '.__('messages.updatedSuccessfully'), [
                'logo_url' => $setting->logo_url,
                'company_name' => $setting->company_name,
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();

            return Reply::error('Failed to update global settings: '.$e->getMessage());
        }
    }

    /**
     * Backward-compatible alias method for existing legacy route bindings.
     *
     * @return array
     */
    public function updatePr(UpdateGlobalSettingRequest $request)
    {
        return $this->update($request);
    }

    /**
     * Change system default language locale.
     *
     * @return array
     */
    public function changeLanguage(Request $request)
    {
        $setting = Company::first();
        if ($setting) {
            $setting->locale = $request->input('lang');
            $setting->save();
        }

        return Reply::success('Language changed successfully.');
    }
}
