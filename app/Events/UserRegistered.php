<?php

/**
 * UserRegistered — V2 Registration Sprint 5 (ADR-V2R-09).
 *
 * Fired alongside CompanyRegistered, AFTER DB::commit() (ADR-V2R-13).
 * The IssueVerificationTokenAction listener uses rawPassword to embed it in the
 * welcome mail. rawPassword is NOT persisted anywhere by these classes.
 */

namespace App\Events;

use App\Models\User;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserRegistered
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public readonly User   $user,
        public readonly string $rawPassword, // welcome-mail only; never stored by this class
    ) {}
}
