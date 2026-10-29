<?php

/**
 * SuperAdminThemeSettingsController
 *
 * Handles Super Admin theme settings, enterprise primary brand color,
 * custom administrative & front-facing CSS stylesheets, and brand background imagery.
 * Enforces global tenant isolation (company_id IS NULL) and multi-tenant atomic cache invalidation.
 *
 * @category Controller
 *
 * @author   Senior Principal Enterprise SaaS Architect
 * @license  JERO Enterprise License
 */

namespace App\Http\Controllers\SuperAdmin;

use App\Helper\Files;
use App\Helper\Reply;
use App\Models\ThemeSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class SuperAdminThemeSettingsController extends SuperAdminBaseController
{
    /**
     * Default enterprise primary branding color
     */
    public const DEFAULT_PRIMARY_COLOR = '#009ef7';

    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = 'menu.themeSettings';
        $this->pageIcon = 'ti-settings';
    }

    /**
     * Display the theme settings configuration page.
     * Guarantees safe retrieval or instantiation of the global Super Admin theme record.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function index()
    {
        // Strictly query global Super Admin scope (company_id IS NULL) bypassing any tenant scopes
        $theme = ThemeSetting::withoutGlobalScopes()
            ->whereNull('company_id')
            ->first();

        if (! $theme) {
            $theme = new ThemeSetting;
            $theme->company_id = null;
            $theme->primary_color = self::DEFAULT_PRIMARY_COLOR;
            $theme->save();
        }

        $this->adminTheme = $theme;

        return view('super-admin.theme-settings.index', $this->data);
    }

    /**
     * Store and update Super Admin theme settings, primary color, CSS, and background image.
     * Enforces strict validation, transaction safety, and atomic cache invalidation.
     *
     * @return array
     */
    public function store(Request $request)
    {
        $request->validate([
            'primary_color' => ['required', 'string', 'regex:/^#([a-fA-F0-9]{3}|[a-fA-F0-9]{6})$/i'],
            'admin_custom_css' => ['nullable', 'string', 'max:65535'],
            'front_custom_css' => ['nullable', 'string', 'max:65535'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:4096'],
            'remove_image' => ['nullable'],
        ]);

        return DB::transaction(function () use ($request) {
            $theme = ThemeSetting::withoutGlobalScopes()
                ->whereNull('company_id')
                ->first();

            if (! $theme) {
                $theme = new ThemeSetting;
                $theme->company_id = null;
                $theme->primary_color = self::DEFAULT_PRIMARY_COLOR;
            }

            $theme->primary_color = $request->input('primary_color');
            $theme->admin_custom_css = $request->input('admin_custom_css');
            $theme->front_custom_css = $request->input('front_custom_css');

            $oldImage = $theme->home_background_image;

            // Handle removal of background image if requested
            if ($request->filled('remove_image') && in_array($request->input('remove_image'), ['1', 1, 'true', true], true)) {
                if ($oldImage) {
                    $path = public_path('user-uploads/background/'.$oldImage);
                    if (File::exists($path)) {
                        Files::deleteFile($oldImage, 'background');
                    }
                    $theme->home_background_image = null;
                }
            }

            // Handle new background image upload
            if ($request->hasFile('image')) {
                if ($oldImage) {
                    $path = public_path('user-uploads/background/'.$oldImage);
                    if (File::exists($path)) {
                        Files::deleteFile($oldImage, 'background');
                    }
                }
                $theme->home_background_image = Files::upload($request->file('image'), 'background');
            }

            $theme->save();

            // Atomic Multi-Tenant Cache Invalidation (Rule #6)
            Cache::forget('superadmin_theme_setting');

            return Reply::successWithData(__('menu.themeSettings').' '.__('messages.updatedSuccessfully'), [
                'primary_color' => $theme->primary_color,
                'background_image_url' => $theme->background_image_url,
                'has_background_image' => ! is_null($theme->home_background_image),
                'action' => 'redirect',
                'url' => route('superadmin.theme-settings.index'),
            ]);
        });
    }

    /**
     * Reset the Super Admin theme to factory defaults.
     *
     * @return array
     */
    public function reset()
    {
        return DB::transaction(function () {
            $theme = ThemeSetting::withoutGlobalScopes()
                ->whereNull('company_id')
                ->first();

            if (! $theme) {
                $theme = new ThemeSetting;
                $theme->company_id = null;
            }

            $theme->primary_color = self::DEFAULT_PRIMARY_COLOR;
            $theme->admin_custom_css = null;
            $theme->front_custom_css = null;

            if ($theme->home_background_image) {
                $path = public_path('user-uploads/background/'.$theme->home_background_image);
                if (File::exists($path)) {
                    Files::deleteFile($theme->home_background_image, 'background');
                }
                $theme->home_background_image = null;
            }

            $theme->save();

            // Atomic Multi-Tenant Cache Invalidation (Rule #6)
            Cache::forget('superadmin_theme_setting');

            return Reply::successWithData(__('messages.updatedSuccessfully'), [
                'primary_color' => self::DEFAULT_PRIMARY_COLOR,
                'admin_custom_css' => '',
                'front_custom_css' => '',
                'background_image_url' => asset('front/assets/img/header-bg.jpg'),
                'has_background_image' => false,
                'action' => 'redirect',
                'url' => route('superadmin.theme-settings.index'),
            ]);
        });
    }
}
