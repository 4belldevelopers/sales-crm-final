<?php

/**
 * SuperAdminThemeSettingsController
 *
 * Handles Super Admin theme settings, enterprise primary brand color,
 * custom administrative & front-facing CSS stylesheets, and brand background imagery.
 * Strictly adheres to CQRS (0% MySQL reads via Elasticsearch local_theme_settings),
 * Two-Tier Idempotency (RFC 4122 v4 UUID & sys_idempotency_keys leases), Transactional Outbox
 * (sys_outbox_events row-locked <15ms duration), and O(1) Redis/Dragonfly L2 cache invalidation (Rule 6).
 *
 * @category Controller
 *
 * @author   Senior Principal Enterprise SaaS Architect
 * @license  JERO Enterprise License
 */

namespace App\Http\Controllers\SuperAdmin;

use App\Services\SuperAdmin\SuperAdminThemeSettingMutationService;
use App\Services\SuperAdmin\SuperAdminThemeSettingReadService;
use Illuminate\Http\Request;

class SuperAdminThemeSettingsController extends SuperAdminBaseController
{
    /**
     * Default enterprise primary branding color
     */
    public const DEFAULT_PRIMARY_COLOR = SuperAdminThemeSettingReadService::DEFAULT_PRIMARY_COLOR;

    public function __construct(
        protected SuperAdminThemeSettingReadService $readService,
        protected SuperAdminThemeSettingMutationService $mutationService
    ) {
        parent::__construct();
        $this->pageTitle = 'menu.themeSettings';
        $this->pageIcon = 'ti-settings';
    }

    /**
     * Display the theme settings configuration page.
     * CQRS Read Path: 100% offloaded to Elasticsearch / Dragonfly L2 Cache (0% MySQL reads).
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function index()
    {
        $theme = $this->readService->getThemeSettings();
        $this->adminTheme = $theme;

        return view('super-admin.theme-settings.index', $this->data);
    }

    /**
     * Store and update Super Admin theme settings, primary color, CSS, and background image.
     * Enforces two-tier idempotency, transactional outbox (<15ms MySQL lock), and post-commit fast-path sync.
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

        $idempotencyKey = $request->header('X-Idempotency-Key', $request->input('idempotency_key'));
        $adminId = auth()->id();

        return $this->mutationService->updateThemeSettings($request, $idempotencyKey, $adminId);
    }

    /**
     * Reset the Super Admin theme to factory defaults.
     * Enforces two-tier idempotency, transactional outbox (<15ms MySQL lock), and post-commit fast-path sync.
     *
     * @return array
     */
    public function reset(Request $request)
    {
        $idempotencyKey = $request->header('X-Idempotency-Key', $request->input('idempotency_key'));
        $adminId = auth()->id();

        return $this->mutationService->resetThemeSettings($idempotencyKey, $adminId);
    }
}
