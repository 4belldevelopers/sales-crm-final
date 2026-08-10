<?php

/**
 * CreateCompanyAction — V2 Registration Sprint 5, task 5.2 (ADR-V2R-11).
 *
 * Responsibility: validate and insert the Company row, set domain_key, seed the trial
 * CompanyPackage + subscriptions rows, and return the saved Company model.
 *
 * Transaction boundary: ONE transaction, retried up to 3 times on deadlock (ADR-V2R-12).
 * Must be idempotent inside the closure — re-derives all state from $request.
 *
 * Status: SCAFFOLD — not yet wired. companyRegister() still runs the inline logic.
 * Wire as part of the companyRegister() rewrite (Sprint 5 task 5.4/5.5).
 * Every field set here must exactly match what the current companyRegister() does.
 */

namespace App\Actions\Registration;

use App\Models\Company;
use Illuminate\Http\Request;

class CreateCompanyAction
{
    /** Creates company + package rows; returns the saved Company. */
    public function execute(Request $request): Company
    {
        // TODO Sprint 5.4: extract the company + package seeding block from
        // SaasFrontController::companyRegister() lines 397–900 into this method.
        // Every field assignment and DB::table() insert must be reproduced here
        // BEFORE removing from the controller. Run feature tests after each extraction.
        throw new \LogicException('CreateCompanyAction is not yet wired — companyRegister() still runs inline.');
    }
}
