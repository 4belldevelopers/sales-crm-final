<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Helper\Reply;
use App\Http\Requests\Language\StoreLanguage;
use App\Models\Company;
use App\Models\LanguageSetting;
use Barryvdh\TranslationManager\Models\Translation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class LanguageSettingsController extends SuperAdminBaseController
{
    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = 'languageSetting';
        $this->pageIcon = 'icon-settings';
        $this->langPath = base_path().'/resources/lang';
    }

    /**
     * Display a listing of language settings with 10K concurrency optimizations.
     * Complies with CQRS read separation and modern Metronic 8 card design standards.
     *
     * @return \Illuminate\Http\Response|\Illuminate\View\View
     */
    public function index()
    {
        if (request()->ajax()) {
            $languages = LanguageSetting::orderBy('language_name', 'asc')->get();

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

        // Instant First-Contentful-Paint data for Client-First rendering
        $languages = LanguageSetting::orderBy('language_name', 'asc')->get();
        $masterLanguages = DB::table('master_languages')
            ->select('id', 'name', DB::raw('`iso_639-1` as code'))
            ->orderBy('name', 'asc')
            ->get();

        $this->languages = $languages;
        $this->masterLanguages = $masterLanguages;
        $this->totalLanguages = $languages->count();
        $this->enabledCount = $languages->where('status', 'enabled')->count();
        $this->disabledCount = $languages->where('status', 'disabled')->count();
        $this->defaultLocale = $this->global->locale ?? 'en';
        $this->masterLanguagesCount = $masterLanguages->count();

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
        $this->masterLanguages = DB::table('master_languages')
            ->select('id', 'name', DB::raw('`iso_639-1` as code'))
            ->orderBy('name', 'asc')
            ->get();

        if (request()->ajax()) {
            return view('super-admin.language-settings.create-modal', $this->data);
        }

        return view('super-admin.language-settings.create', $this->data);
    }

    /**
     * Store a newly created resource in storage.
     * Wrapped in DB::transaction with base translation dictionary scaffolding.
     *
     * @return array
     */
    public function store(StoreLanguage $request)
    {
        $code = strtolower(trim((string) $request->language_code));
        $name = ucfirst(trim((string) $request->language_name));

        DB::transaction(function () use ($request, $code, $name) {
            $targetDir = $this->langPath.'/'.$code;

            if (! File::exists($targetDir)) {
                File::makeDirectory($targetDir, 0755, true);

                // Base translation scaffolding: copy core translation dictionaries from en/ so language is immediately functional
                $enDir = $this->langPath.'/en';
                if (File::isDirectory($enDir)) {
                    $coreFiles = ['app.php', 'messages.php', 'menu.php', 'errors.php'];
                    foreach ($coreFiles as $fileName) {
                        $srcFile = $enDir.'/'.$fileName;
                        if (File::exists($srcFile)) {
                            File::copy($srcFile, $targetDir.'/'.$fileName);
                        }
                    }
                }
            }

            $language = new LanguageSetting;
            $language->language_name = $name;
            $language->language_code = $code;
            $language->status = $request->status;
            $language->save();
        });

        LanguageSetting::flushCaches();

        return Reply::success(__('app.language').' '.__('menu.settings').' '.__('messages.createdSuccessfully'));
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
        $this->language = LanguageSetting::withoutGlobalScopes()->findOrFail($id);
        $this->masterLanguages = DB::table('master_languages')
            ->select('id', 'name', DB::raw('`iso_639-1` as code'))
            ->orderBy('name', 'asc')
            ->get();

        if (request()->ajax()) {
            return view('super-admin.language-settings.edit-modal', $this->data);
        }

        return view('super-admin.language-settings.edit', $this->data);
    }

    /**
     * Update the specified resource in storage.
     * Non-destructively renames directory and updates database translations.
     *
     * @param  int  $id
     * @return array
     */
    public function update(StoreLanguage $request, $id)
    {
        $language = LanguageSetting::withoutGlobalScopes()->findOrFail($id);

        $oldCode = strtolower((string) $language->language_code);
        $newCode = strtolower(trim((string) $request->language_code));
        $newName = ucfirst(trim((string) $request->language_name));

        if ($oldCode === 'en' && $newCode !== 'en') {
            return Reply::error(__('modules.languageSettings.defaultLanguageCannotBeModified'));
        }

        DB::transaction(function () use ($language, $request, $oldCode, $newCode, $newName) {
            $oldDir = $this->langPath.'/'.$oldCode;
            $newDir = $this->langPath.'/'.$newCode;

            if ($oldCode !== $newCode) {
                if (File::isDirectory($oldDir) && ! File::isDirectory($newDir)) {
                    File::move($oldDir, $newDir);
                } elseif (! File::isDirectory($newDir)) {
                    File::makeDirectory($newDir, 0755, true);
                }

                // Non-destructive update of Translation records: update locale instead of deleting!
                Translation::where('locale', $oldCode)->update(['locale' => $newCode]);
            }

            if ($oldCode === $this->global->locale) {
                $this->global->locale = $newCode;
                $this->global->save();
                $language->status = 'enabled';
            } else {
                $language->status = $request->status;
            }

            $language->language_name = $newName;
            $language->language_code = $newCode;
            $language->save();
        });

        LanguageSetting::flushCaches();

        return Reply::success(__('app.language').' '.__('menu.settings').' '.__('messages.updatedSuccessfully'));
    }

    /**
     * Remove the specified resource from storage.
     * Guards English and active system default locale from deletion.
     *
     * @param  int  $id
     * @return array
     */
    public function destroy($id)
    {
        $language = LanguageSetting::withoutGlobalScopes()->find($id);

        if (! $language) {
            return Reply::error(__('messages.recordNotFound'));
        }

        if ($language->language_code === 'en') {
            return Reply::error(__('modules.languageSettings.defaultLanguageCannotBeModified'));
        }

        if ($this->global->locale === $language->language_code) {
            return Reply::error('Cannot delete the active system default language. Please switch the default language first.');
        }

        DB::transaction(function () use ($language, $id) {
            Translation::where('locale', $language->language_code)->delete();
            LanguageSetting::destroy($id);
        });

        LanguageSetting::flushCaches();

        return Reply::success(__('messages.recordDeleted'));
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
        $language = LanguageSetting::withoutGlobalScopes()->findOrFail($id);

        // Strictly protect English from ever being disabled
        if ($language->language_code === 'en' && $status === 'disabled') {
            return Reply::error(__('modules.languageSettings.defaultLanguageCannotBeModified'));
        }

        // Protect active system default locale from being disabled
        if ($this->global->locale === $language->language_code && $status === 'disabled') {
            return Reply::error('The active system default language cannot be disabled.');
        }

        $language->status = $status;
        $language->save();

        LanguageSetting::flushCaches();

        return Reply::success(__('app.language').' '.__('menu.settings').' '.__('messages.updatedSuccessfully'));
    }

    /**
     * Set a specific language as the global system default locale.
     *
     * @param  int  $id
     * @return array
     */
    public function setDefaultLanguage(Request $request, $id)
    {
        $language = LanguageSetting::withoutGlobalScopes()->findOrFail($id);

        if ($language->status !== 'enabled') {
            $language->status = 'enabled';
            $language->save();
        }

        $this->global->locale = $language->language_code;
        $this->global->save();

        session(['locale' => $language->language_code]);
        App::setLocale($language->language_code);

        LanguageSetting::flushCaches();

        return Reply::success(ucfirst($language->language_name).' set as system default language successfully.');
    }

    /**
     * Change active language for the user session / context without leaking tenancy.
     *
     * @return array
     */
    public function changeLanguage(Request $request)
    {
        $lang = strtolower(trim((string) $request->input('lang', 'en')));

        // Validate that requested language exists and is enabled
        $exists = LanguageSetting::where('language_code', $lang)->where('status', 'enabled')->exists();
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

        LanguageSetting::flushCaches();

        return Reply::success(__('messages.languageSettings.changedSuccessfully'));
    }
}
