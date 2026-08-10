<?php

/**
 * CreateCompanyUserAction — V2 Registration Sprint 5, task 5.2 (ADR-V2R-11).
 *
 * Responsibility: insert the root User row, users_profile, and call assignRole('admin').
 * Receives the already-committed Company so it can set company_id.
 *
 * Transaction boundary: ONE transaction, retried up to 3 times on deadlock (ADR-V2R-12).
 *
 * Status: SCAFFOLD — not yet wired. See CreateCompanyAction for wiring instructions.
 */

namespace App\Actions\Registration;

use App\Models\Company;
use App\Models\User;
use Illuminate\Http\Request;

class CreateCompanyUserAction
{
    /** Creates the root user + profile + role; returns the saved User. */
    public function execute(Request $request, Company $company): User
    {
        // TODO Sprint 5.4: extract user + profile + role block from companyRegister().
        throw new \LogicException('CreateCompanyUserAction is not yet wired.');
    }
}
