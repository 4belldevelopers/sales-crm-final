<?php

/**
 * CompanyRegistered — V2 Registration Sprint 5 (ADR-V2R-09).
 *
 * Fired AFTER DB::commit() returns, never inside the transaction closure (ADR-V2R-13).
 * Listeners (BootstrapTenantDefaultsAction, IndexNewCompany) run as queued jobs so a
 * listener failure cannot roll back the company creation.
 */

namespace App\Events;

use App\Models\Company;
use App\Models\User;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CompanyRegistered
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public readonly Company $company,
        public readonly User    $user,
        public readonly array   $requestData,
    ) {}
}
