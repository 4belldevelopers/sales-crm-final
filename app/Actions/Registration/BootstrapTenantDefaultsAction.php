<?php

/**
 * BootstrapTenantDefaultsAction — V2 Registration Sprint 5, task 5.2 (ADR-V2R-11).
 *
 * Responsibility: seed roles, permissions, application_status, ThemeSetting,
 * ApplicationSetting, and the trial CompanyPackage for a new tenant.
 *
 * Runs as a QUEUED LISTENER on CompanyRegistered (ADR-V2R-13 — after commit).
 * This prevents a permission-attach failure from rolling back company creation.
 *
 * Must be idempotent: if re-queued after partial failure it must not create duplicates.
 * Guarded by scoped binding (ADR-V2R-11) — fresh instance per Octane request.
 *
 * Status: SCAFFOLD — CompanyObserver::created() registration deferred here from Sprint 3.6.
 */

namespace App\Actions\Registration;

use App\Models\Company;

class BootstrapTenantDefaultsAction
{
    /** Seeds all tenant defaults for a newly created Company. */
    public function execute(Company $company): void
    {
        // TODO Sprint 5.4: extract roles/statuses/ThemeSetting/ApplicationSetting/package seeding
        // from companyRegister() into this method. Register CompanyObserver AFTER this is wired
        // to avoid double-seeding (see Sprint 3.6 deferral in session memory).
        throw new \LogicException('BootstrapTenantDefaultsAction is not yet wired.');
    }
}
