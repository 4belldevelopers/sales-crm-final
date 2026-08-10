<?php

/**
 * IssueVerificationTokenAction — V2 Registration Sprint 5, task 5.2 (ADR-V2R-11).
 *
 * Responsibility: generate the email_verification_code + email_verification_expires_at,
 * save them on the User, and dispatch the welcome-mail job with a signed verification URL.
 *
 * Idempotent: if re-run with the same User it rotates the token (same as the resend flow).
 * No transaction needed — token rotation is a single atomic DB update.
 *
 * Status: SCAFFOLD.
 */

namespace App\Actions\Registration;

use App\Models\User;
use Illuminate\Http\Request;

class IssueVerificationTokenAction
{
    public function execute(User $user, Request $request): void
    {
        // TODO Sprint 5.4/5.10: generate signed temporarySignedRoute URL, persist token +
        // expires_at on user, dispatch QueueMailService with the welcome mail template.
        throw new \LogicException('IssueVerificationTokenAction is not yet wired.');
    }
}
