<?php

namespace App\Observers;

use App\Models\ApplicationSetting;
use App\Models\Company;
use App\Helper\Files;
use App\Models\Role;
use Illuminate\Support\Facades\DB;
use App\Models\Package;
use App\Models\CompanyPackage;
use Carbon\Carbon;
use App\Models\ThemeSetting;
use App\Models\Permission;
use App\Support\TenantLookup;
use App\Jobs\ElasticCompanyIndex;

class CompanyObserver
{
    /** Defer observer execution until database transaction commit success. */
    public bool $afterCommit = true;

    /** Invalidate TenantLookup caches whenever a company record is saved. */
    public function updated(Company $company): void
    {
        TenantLookup::invalidate($company->id);
    }

    public function updating(Company $company)
    {
        $original = $company->getOriginal();
        if ($company->isDirty('logo')) {
            Files::deleteFile($original['logo'], 'company-logo');
        }
    }

    public function created(Company $company)
    {
        if ($company->id > 1) {

            $this->createCommercialDefaults($company);

            // add company default role
            $roleUser = new Role();
            $roleUser->name = 'admin';
            $roleUser->display_name = 'Administrator';
            $roleUser->description = 'Admin is allowed to manage everything of the app.';
            $roleUser->company_id = $company->id;
            $roleUser->save();

            // Spatie equivalent of the legacy entrust attachPermission() loop.
            // syncPermissions() attaches all permissions in a single bulk operation.
            $permissions = Permission::all();
            $roleUser->syncPermissions($permissions);

            //add company default job application status
            $data = [
                ['status' => 'applied', 'company_id' => $company->id, 'slug' => 'applied', 'position' => '1', 'color' => '#2b2b2b'],
                ['status' => 'phone screen', 'company_id' => $company->id, 'slug' => 'phone screen', 'position' => '2', 'color' => '#f1e52e'],
                ['status' => 'interview', 'company_id' => $company->id, 'slug' => 'interview', 'position' => '3', 'color' => '#3d8ee8'],
                ['status' => 'hired', 'company_id' => $company->id, 'slug' => 'hired', 'position' => '4', 'color' => '#32ac16'],
                ['status' => 'rejected', 'company_id' => $company->id, 'slug' => 'rejected', 'position' => '5', 'color' => '#ee1127']
            ];
            DB::table('application_status')->insert($data);

            //add default theme settings
            $theme = new ThemeSetting();
            $theme->primary_color = '#1579d0';
            $theme->company_id = $company->id;
            $theme->admin_custom_css = '/*Enter your custom css after this line*/ 
            .sidebar-dark-primary {
            background-image: linear-gradient(to top, #00c6fb 0%, #005bea 100%);
            }';
            $theme->save();

            // add default application settings
            $application_setting_data = [
                'company_id' => $company->id,
                'legal_term' => "If any provision of these Terms and Conditions is held to be invalid or unenforceable, the provision shall be removed (or interpreted, if possible, in a manner as to be enforceable), and the remaining provisions shall be enforced. Headings are for reference purposes only and in no way define, limit, construe or describe the scope or extent of such section. Our failure to act with respect to a breach by you or others does not waive our right to act with respect to subsequent or similar breaches. These Terms and Conditions set forth the entire understanding and agreement between us with respect to the subject matter contained herein and supersede any other agreement, proposals and communications, written or oral, between our representatives and you with respect to the subject matter hereof, including any terms and conditions on any of customer's documents or purchase orders.<br>No Joint Venture, No Derogation of Rights. You agree that no joint venture, partnership, employment, or agency relationship exists between you and us as a result of these Terms and Conditions or your use of the Site. Our performance of these Terms and Conditions is subject to existing laws and legal process, and nothing contained herein is in derogation of our right to comply with governmental, court and law enforcement requests or requirements relating to your use of the Site or information provided to or gathered by us with respect to such use.",
                'mail_setting' => [
                    '1' => [
                        'name' => 'applied',
                        'status' => true
                    ],
                    '2' => [
                        'name' => 'phone screen',
                        'status' => true
                    ],
                    '3' => [
                        'name' => 'interview',
                        'status' => true
                    ],
                    '4' => [
                        'name' => 'hired',
                        'status' => true
                    ],
                    '5' => [
                        'name' => 'rejected',
                        'status' => true
                    ]
                ]
            ];

            ApplicationSetting::create($application_setting_data);

            //assign trial package
            $trialPackage = Package::where('is_trial', 1)->first();
            $checkTrialPackage = CompanyPackage::where('company_id', $company->id)
                ->where('package_id', $trialPackage->id)->first();

            if (!is_null($trialPackage) && is_null($checkTrialPackage)) {
                $companyPackage = new CompanyPackage();
                $companyPackage->company_id = $company->id;
                $companyPackage->package_id = $trialPackage->id;
                $companyPackage->status = 'active';
                $companyPackage->start_date = Carbon::today()->format('Y-m-d');
                $companyPackage->end_date = Carbon::today()->addDays($trialPackage->trial_duration)->format('Y-m-d');
                $companyPackage->save();

                Company::where('id', $company->id)->update(['package_id' => $trialPackage->id]);
            }
        }
    }

    /**
     * Create the explicitly approved commercial defaults for a new tenant only.
     *
     * This uses raw queries intentionally; every inserted row is explicitly scoped to
     * the newly created company and no existing tenant configuration is modified.
     */
    private function createCommercialDefaults(Company $company): void
    {
        $usdCurrencyKey = DB::table('currencies')->where('currency_code', 'USD')->value('getKey');
        $experienceIndexes = DB::table('commercial_experience_bands')
            ->where('is_active', 1)
            ->orderBy('sort_order')
            ->get(['min_experience', 'max_experience'])
            ->flatMap(function ($band) {
                return range((int) $band->min_experience, (int) $band->max_experience);
            })
            ->unique()
            ->values()
            ->all();

        if ($usdCurrencyKey === null || $experienceIndexes === []) {
            return;
        }

        foreach ([
            ['commtype' => 1, 'rateType' => 1, 'sal_mode' => 2, 'rate' => 5.00],
            ['commtype' => 2, 'rateType' => 2, 'sal_mode' => 0, 'rate' => 8.33],
        ] as $default) {
            $commercialId = DB::table('commericials')->insertGetId([
                'company_id' => $company->id,
                'rateType' => $default['rateType'],
                'commtype' => $default['commtype'],
                'status' => in_array((int) $company->company_type, [1, 2], true) ? 1 : 0,
                'sal_mode' => $default['sal_mode'],
                'salary_currency' => (int) $usdCurrencyKey,
                'replacement_period_days' => 90,
                'replacement_type' => 'free_replacement',
                'replacement_eligibility' => 'early_exit',
                'billing_frequency' => 'one_time',
                'billing_trigger' => 'candidate_joined',
                'payment_terms_days' => 30,
            ]);

            $detailRows = array_map(function (int $experienceIndex) use ($company, $commercialId, $default) {
                return [
                    'commericial_id' => $commercialId,
                    'exp' => $experienceIndex,
                    'rate' => $default['rate'],
                    'company_id' => $company->id,
                    'commtype' => $default['commtype'],
                ];
            }, $experienceIndexes);
            DB::table('commericial_details')->insert($detailRows);
        }

        // Intent: Refresh ES read-model inline and via queue so new company is immediately visible in agency search.
        try {
            app(\App\Services\ElasticCaompanyIndex::class)->companyIndexElastic($company->id);
            \App\Helper\GeneralMethod::cacheClean('allfindagencyversion_global');
        } catch (\Throwable $e) {
            Log::warning('[CompanyObserver] Inline ES company indexing failed: ' . $e->getMessage());
        }
        ElasticCompanyIndex::dispatch($company->id)->afterCommit();
    }

}
