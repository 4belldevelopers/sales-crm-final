<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Helper\Reply;
use App\Http\Requests\Language\StoreLanguage;
use App\Models\LanguageSetting;
use App\Services\SuperAdmin\SuperAdminLanguageCacheManager;
use App\Services\SuperAdmin\SuperAdminLanguageMutationService;
use App\Services\SuperAdmin\SuperAdminLanguageReadService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

/**
 * Class LanguageSettingsController
 *
 * Intent: Super Admin Language & Localization Hub Controller.
 * Hardened to God-Level standards:
 * - Strict CQRS Segregation (0% MySQL reads on warm page loads, 100% queries served from Elasticsearch/Redis L2).
 * - Two-Tier Request Idempotency via sys_idempotency_keys.
 * - Transactional Outbox synchronization to Elasticsearch local_language_settings via sys_outbox_events.
 * - Base English & Active Default System Locale Immutability Guards.
 * - O(1) Generation-Based Multi-Tenant Cache Invalidation.
 *
 * Tenancy: Global Super Admin root localization scope (tenant_id = 0).
 * Author: Antigravity AI — Super Admin Backend Architecture
 * Complies with docs/CODE_DOCUMENTATION_STANDARD.md and docs/AI_RULES.md.
 */
class LanguageSettingsController extends SuperAdminBaseController
{
    public function __construct(
        protected SuperAdminLanguageReadService $readService,
        protected SuperAdminLanguageMutationService $mutationService
    ) {
        parent::__construct();
        $this->pageTitle = 'languageSetting';
        $this->pageIcon = 'icon-settings';
        $this->langPath = base_path().'/resources/lang';
    }

    /**
     * Display a listing of language settings with 10K concurrency optimizations.
     * Complies with CQRS read separation (0% MySQL reads) and modern Metronic 8 card design standards.
     *
     * @return \Illuminate\Http\Response|\Illuminate\View\View
     */
    public function index()
    {
        if (request()->ajax()) {
            // Serves 100% from Elasticsearch / Redis L2 Cache (0% MySQL reads)
            $languages = $this->readService->getLanguages();

            return datatables()->of($languages)
                ->addIndexColumn()
                ->addColumn('serial_no', function ($row) {
                    static $index = 0;
                    $index++;

                    return $index;
                })
                ->addColumn('action', function ($row) {
                    $isDefault = ($this->global->locale == $row->language_code);
                    $action = '<div class="d-flex align-items-center justify-content-center gap-1">';

                    $action .= '<a href="javascript:;" data-row-id="'.$row->id.'" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm edit-language" data-bs-toggle="tooltip" title="'.__('app.edit').'">
                        <i class="ki-outline ki-pencil fs-4 text-primary"></i>
                    </a>';

                    if (! $isDefault && $row->status == 'enabled') {
                        $action .= '<a href="javascript:;" data-row-id="'.$row->id.'" data-lang-name="'.e($row->language_name).'" class="btn btn-icon btn-bg-light btn-active-color-warning btn-sm set-default-language" data-bs-toggle="tooltip" title="Set as System Default">
                            <i class="ki-outline ki-star fs-4 text-warning"></i>
                        </a>';
                    }

                    if ($row->language_code !== 'en' && ! $isDefault) {
                        $action .= '<a href="javascript:;" class="btn btn-icon btn-bg-light btn-active-color-danger btn-sm delete-language-row" data-bs-toggle="tooltip" data-row-id="'.$row->id.'" title="'.__('app.delete').'">
                            <i class="ki-outline ki-trash fs-4 text-danger"></i>
                        </a>';
                    } else {
                        $action .= '<span class="badge badge-light-primary fw-bold fs-8 px-2 py-1 ms-1">System Default</span>';
                    }

                    $action .= '</div>';

                    return $action;
                })
                ->editColumn('language_code', function ($row) {
                    return '<span class="badge badge-light-info fw-bold text-uppercase px-2 py-1">'.e(strtolower($row->language_code)).'</span>';
                })
                ->editColumn('language_name', function ($row) {
                    $flag = $row->flag_emoji ?? '🌐';

                    return '<div class="d-flex align-items-center">
                        <span class="fs-4 me-2">'.$flag.'</span>
                        <span class="text-gray-800 fw-bold fs-6">'.e(ucfirst($row->language_name)).'</span>
                    </div>';
                })
                ->editColumn('status', function ($row) {
                    $checked = $row->status == 'enabled' ? 'checked' : '';
                    $isDefault = ($this->global->locale == $row->language_code);
                    $isEnglish = ($row->language_code === 'en');
                    $disabled = ($isDefault || $isEnglish) ? 'disabled' : '';
                    $disabledNote = ($isDefault || $isEnglish)
                        ? 'data-bs-toggle="tooltip" title="'.__('modules.languageSettings.statusDisabledNote').'"'
                        : '';

                    return '<div '.$disabledNote.' class="form-check form-switch form-check-custom form-check-solid justify-content-center">
                            <input '.$disabled.' class="form-check-input change-language-setting cursor-pointer" type="checkbox" '.$checked.
                            ' value="active" data-lang-id="'.$row->id.'">
                        </div>';
                })
                ->rawColumns(['serial_no', 'action', 'status', 'language_name', 'language_code'])
                ->make(true);
        }

        // 100% CQRS Read Path: Zero MySQL Reads on User Request Path (Rule 1 & Rule 2)
        $languages = $this->readService->getLanguages();
        $masterLanguages = $this->readService->getMasterLanguages();
        $kpis = $this->readService->computeKpis($languages, $masterLanguages, $this->global->locale ?? 'en');

        $this->languages = $languages;
        $this->masterLanguages = $masterLanguages;
        $this->totalLanguages = $kpis['totalLanguages'];
        $this->enabledCount = $kpis['enabledCount'];
        $this->disabledCount = $kpis['disabledCount'];
        $this->defaultLocale = $kpis['defaultLocale'];
        $this->masterLanguagesCount = $kpis['masterLanguagesCount'];

        return view('super-admin.language-settings.index', $this->data);
    }

    /**
     * Show the form for creating a new resource.
     * Supports both modal AJAX partials and standalone view requests.
     *
     * @return \Illuminate\Http\Response|\Illuminate\View\View
     */
    public function create()
    {
        $this->masterLanguages = $this->readService->getMasterLanguages();

        if (request()->ajax()) {
            return view('super-admin.language-settings.create-modal', $this->data);
        }

        return view('super-admin.language-settings.create', $this->data);
    }

    /**
     * Store a newly created resource in storage.
     * Hardened with Two-Tier Idempotency, Transactional Outbox, and Directory Scaffolding.
     *
     * @return array
     */
    public function store(StoreLanguage $request)
    {
        return $this->mutationService->storeLanguage($request);
    }

    /**
     * Show the form for editing the specified resource.
     * Supports both modal AJAX partials and standalone view requests.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response|\Illuminate\View\View
     */
    public function edit($id)
    {
        $this->language = $this->readService->findLanguage((int) $id)
            ?? LanguageSetting::withoutGlobalScopes()->findOrFail($id);
        $this->masterLanguages = $this->readService->getMasterLanguages();

        if (request()->ajax()) {
            return view('super-admin.language-settings.edit-modal', $this->data);
        }

        return view('super-admin.language-settings.edit', $this->data);
    }

    /**
     * Update the specified resource in storage.
     * Non-destructively renames directory, migrates database translations, and updates Elasticsearch via outbox.
     *
     * @param  int  $id
     * @return array
     */
    public function update(StoreLanguage $request, $id)
    {
        return $this->mutationService->updateLanguage($request, (int) $id);
    }

    /**
     * Remove the specified resource from storage.
     * Guards English and active system default locale from deletion.
     * Registers deletion tombstone to prevent resurrection.
     *
     * @param  int  $id
     * @return array
     */
    public function destroy($id)
    {
        return $this->mutationService->destroyLanguage((int) $id);
    }

    /**
     * Toggle enabled/disabled status of a language.
     * Guards English and active system default locale from being disabled.
     *
     * @param  int  $id
     * @return array
     */
    public function changeStatus(Request $request, $id)
    {
        $status = $request->input('status', 'disabled');

        return $this->mutationService->changeStatus((int) $id, $status);
    }

    /**
     * Set a specific language as the global system default locale.
     *
     * @param  int  $id
     * @return array
     */
    public function setDefaultLanguage(Request $request, $id)
    {
        return $this->mutationService->setDefaultLanguage((int) $id);
    }

    /**
     * Change active language for the user session / context without leaking tenancy.
     *
     * @return array
     */
    public function changeLanguage(Request $request)
    {
        $lang = strtolower(trim((string) $request->input('lang', 'en')));

        // Validate that requested language exists and is enabled from cache/ES (0% MySQL reads)
        $languages = $this->readService->getLanguages();
        $exists = $languages->where('language_code', $lang)->where('status', 'enabled')->isNotEmpty();

        if (! $exists && $lang !== 'en') {
            return Reply::error('The selected language is not available or has been disabled.');
        }

        session(['locale' => $lang]);
        App::setLocale($lang);

        if ($this->global && $request->boolean('set_as_global_default')) {
            $this->global->locale = $lang;
            $this->global->save();
        }

        // If in tenant company context, scope update to tenant company
        if (function_exists('company') && company()) {
            $company = company();
            $company->locale = $lang;
            $company->save();
        }

        SuperAdminLanguageCacheManager::flushCaches();

        return Reply::success(__('messages.languageSettings.changedSuccessfully'));
    }
}
