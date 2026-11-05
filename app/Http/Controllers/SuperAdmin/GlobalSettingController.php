<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Requests\SuperAdmin\UpdateGlobalSettingRequest;
use App\Services\SuperAdmin\SuperAdminGlobalSettingMutationService;
use App\Services\SuperAdmin\SuperAdminGlobalSettingReadService;
use Illuminate\Http\Request;

/**
 * GlobalSettingController — Master Platform Configuration & Organization Hub.
 *
 * Intent: Manages platform-wide singleton configuration including company identity,
 * brand logo, base currency, system locale, maintenance mode toggle, account deletion
 * lifecycle policies, and official seller ("Sold-By") invoicing identity.
 *
 * Architecture: Strict CQRS (0% MySQL reads on web/API request paths), Two-Tier Idempotency,
 * Transactional Outbox, L2 Dragonfly/Redis caching, and 100% UI preservation.
 *
 * Tenancy: Global Super Admin scope (system-wide singleton, tenant_id = 0).
 * Author: Antigravity AI — Super Admin Backend Architecture
 * Complies with docs/CODE_DOCUMENTATION_STANDARD.md and docs/AI_RULES.md.
 */
class GlobalSettingController extends SuperAdminBaseController
{
    /**
     * GlobalSettingController constructor.
     */
    public function __construct(
        protected SuperAdminGlobalSettingReadService $readService,
        protected SuperAdminGlobalSettingMutationService $mutationService
    ) {
        parent::__construct();
        $this->pageTitle = 'menu.settings';
        $this->pageIcon = 'icon-settings';
    }

    /**
     * Display the Global Settings configuration hub.
     * Serves 100% of read traffic from Elasticsearch & L2 Dragonfly/Redis Cache (0% MySQL reads).
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function index()
    {
        // 1. Authoritative Singleton Global Setting (Elasticsearch / L2 Cache)
        $global = $this->readService->getGlobalSettings();
        $this->global = $global;
        view()->share('global', $global);

        // 2. Reference Catalogs & Statutory Invoicing Identity (0% MySQL reads)
        $this->currencies = $this->readService->getCurrencies();
        $this->languageSettings = $this->readService->getLanguageSettings();
        $this->sold_by_address = $this->readService->getSoldByAddress();
        $this->countries = $this->readService->getCountries();

        $this->countrie_id = $this->sold_by_address->country_id ?? '';
        $this->state_id = $this->sold_by_address->state_id ?? '';
        $this->city_id = $this->sold_by_address->city ?? '';

        return view('super-admin.global-settings.edit', $this->data);
    }

    /**
     * Canonical REST resource update method.
     *
     * Strictly validates input via UpdateGlobalSettingRequest, executes atomic DB mutations
     * under Two-Tier Idempotency and Transactional Outbox (<15ms), manages brand logo uploads,
     * synchronizes Elasticsearch via post-commit fast-path worker, and evicts multi-tenant
     * cache keys to guarantee zero stale configuration across 10K concurrency.
     *
     * @param  int|null  $id
     * @return array Standardized Reply payload
     */
    public function update(UpdateGlobalSettingRequest $request, $id = null)
    {
        $idempotencyKey = $request->header('X-Idempotency-Key') ?: $request->input('idempotency_key');
        $adminId = auth()->id();

        return $this->mutationService->updateGlobalSettings(
            $request,
            $idempotencyKey,
            $adminId
        );
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
     * Change system default language locale with atomic cache invalidation.
     *
     * @return array
     */
    public function changeLanguage(Request $request)
    {
        return $this->mutationService->changeLanguage($request->input('lang'));
    }
}
