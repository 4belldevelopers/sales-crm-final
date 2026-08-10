<?php

/**
 * SeedCoinLedgerAction — V2 Registration Sprint 5, task 5.2 (ADR-V2R-11).
 *
 * Responsibility: insert the initial coin/email/whatsapp counter rows
 * (company_coins, email_coins, whatsapp_coins) for a new tenant.
 *
 * Runs as a queued listener or inside BootstrapTenantDefaultsAction — either way
 * dispatched AFTER DB::commit() (ADR-V2R-13).
 *
 * Status: SCAFFOLD.
 */

namespace App\Actions\Registration;

use App\Models\Company;

class SeedCoinLedgerAction
{
    public function execute(Company $company): void
    {
        // TODO Sprint 5.4: extract coin-ledger inserts from companyRegister().
        throw new \LogicException('SeedCoinLedgerAction is not yet wired.');
    }
}
