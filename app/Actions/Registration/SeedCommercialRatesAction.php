<?php

/**
 * SeedCommercialRatesAction — V2 Registration Sprint 5, task 5.2 (ADR-V2R-11).
 *
 * Responsibility: insert commericials, commericial_details, and the matching
 * client_location rows for each work city supplied at registration.
 *
 * Note: "commericials" / "commericial" are baked-in typos that map to real table names.
 * Do NOT correct them — see CLAUDE.md rule 7 (NAMING_CONVENTIONS safe-rename procedure).
 *
 * Status: SCAFFOLD.
 */

namespace App\Actions\Registration;

use App\Models\Company;
use Illuminate\Http\Request;

class SeedCommercialRatesAction
{
    public function execute(Request $request, Company $company): void
    {
        // TODO Sprint 5.4: extract commericials + commericial_details + city-loop block.
        throw new \LogicException('SeedCommercialRatesAction is not yet wired.');
    }
}
