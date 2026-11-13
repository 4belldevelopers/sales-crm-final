<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Requests\SuperAdmin\UpdateSuperAdminProfileRequest;
use App\Services\SuperAdmin\SuperAdminProfileMutationService;
use App\Services\SuperAdmin\SuperAdminProfileReadService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

/**
 * SuperAdminProfileController — Root Administrator Profile & Security Hub.
 *
 * Intent: Strictly manages the master administrative identity, contact channels,
 * password rotation with confirmation and current-password verification, avatar photo uploads,
 * and security state following CQRS (Rule 1 & Rule 2), Two-Tier Idempotency, and Transactional Outbox.
 *
 * 0% MySQL reads on web request paths (served via Elasticsearch & Redis/Dragonfly L2 cache).
 *
 * Tenancy: Master Super Admin singleton scope (unrestricted system clearance).
 *
 * Author: Antigravity AI — Super Admin Backend Architecture
 * Complies with docs/CODE_DOCUMENTATION_STANDARD.md and docs/AI_RULES.md.
 */
class SuperAdminProfileController extends SuperAdminBaseController
{
    /**
     * SuperAdminProfileController constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = 'menu.myProfile';
        $this->pageIcon = 'ti-user';
    }

    /**
     * Display the redesigned Super Admin Profile Hub with 0% MySQL read strain.
     *
     * @param SuperAdminProfileReadService $readService
     * @return View
     */
    public function index(?SuperAdminProfileReadService $readService = null): View
    {
        $readService = $readService ?? app(SuperAdminProfileReadService::class);
        $userId = (int) (auth()->id() ?? 1);

        $this->user = $readService->getSuperAdminProfile($userId);
        $this->smsSettings = $readService->getSmsSettings();
        $this->calling_codes = $readService->getCallingCodes();

        return view('super-admin.profile.index', $this->data);
    }

    /**
     * Defensive alias for resource show route.
     *
     * @param int|string|null $id
     * @return RedirectResponse
     */
    public function show($id = null): RedirectResponse
    {
        return redirect()->route('superadmin.profile.index');
    }

    /**
     * Defensive alias for resource edit route.
     *
     * @param int|string|null $id
     * @return RedirectResponse
     */
    public function edit($id = null): RedirectResponse
    {
        return redirect()->route('superadmin.profile.index');
    }

    /**
     * Update root Super Admin profile credentials with Two-Tier Idempotency and Transactional Outbox.
     *
     * @param UpdateSuperAdminProfileRequest $request
     * @param SuperAdminProfileMutationService|null $mutationService
     * @return array
     */
    public function update(UpdateSuperAdminProfileRequest $request, ?SuperAdminProfileMutationService $mutationService = null): array
    {
        $mutationService = $mutationService ?? app(SuperAdminProfileMutationService::class);
        $userId = (int) (auth()->id() ?? 1);
        $idempotencyKey = $request->header('X-Idempotency-Key') ?? $request->input('idempotency_key');

        return $mutationService->updateProfile($request, $userId, $idempotencyKey);
    }
}
