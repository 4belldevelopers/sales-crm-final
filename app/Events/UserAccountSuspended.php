<?php

namespace App\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * V2_login Phase 3.3 — fired when a team admin suspends a user account.
 *
 * Downstream: epoch gate in EnsureOneDeviceLogin invalidates the user's active sessions
 * on their next request (no push needed). Can also be consumed by notification listeners
 * to email the affected user in future work.
 *
 * Channel: none — this is a server-side domain event, not a broadcast.
 */
class UserAccountSuspended
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public readonly int $userId,
        public readonly int $companyId,
        public readonly int $actorId,
    ) {}
}
