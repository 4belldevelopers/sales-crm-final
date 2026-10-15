<?php

namespace App\Services\Marketplace;

use App\Exceptions\IllegalStateTransitionException;

/**
 * Class MarketplaceChannelStateMachine
 *
 * Intent:
 * Deterministic finite state machine governing legal vs. forbidden state transitions
 * for marketplace channels (Agency Hiring and Bench Talent).
 * Guarantees that direct jumps from REJECTED or QUARANTINED to LIVE are strictly blocked.
 *
 * Adheres to Section 6.8 of docs/SUPERADMIN_JOB_MARKETPLACE_REDESIGN_PLAN.md.
 */
class MarketplaceChannelStateMachine
{
    /**
     * Complete lookup of legal state transitions.
     */
    private const ALLOWED_TRANSITIONS = [
        'DRAFT' => ['PENDING_REVIEW', 'LIVE'],
        'PENDING_REVIEW' => ['APPROVED', 'REJECTED', 'QUARANTINED'],
        'APPROVED' => ['LIVE', 'PAUSED'],
        'LIVE' => ['PAUSED', 'CLAIM_LIMIT_REACHED', 'DELISTED'],
        'PAUSED' => ['LIVE', 'DELISTED'],
        'CLAIM_LIMIT_REACHED' => ['LIVE', 'DELISTED'],
        'REJECTED' => ['PENDING_REVIEW'],
        'QUARANTINED' => ['PENDING_REVIEW'],
        'DELISTED' => ['DRAFT'],
    ];

    /**
     * Asserts whether a transition from $fromState to $toState is legally permitted.
     *
     * @throws IllegalStateTransitionException
     */
    public static function assertTransitionAllowed(string $fromState, string $toState): void
    {
        $allowed = self::ALLOWED_TRANSITIONS[$fromState] ?? [];
        if (! in_array($toState, $allowed, true)) {
            throw new IllegalStateTransitionException(
                "Illegal marketplace state transition from [{$fromState}] to [{$toState}]. Direct publication without moderation is strictly prohibited."
            );
        }
    }

    /**
     * Checks whether a transition is permitted without throwing an exception.
     */
    public static function isTransitionAllowed(string $fromState, string $toState): bool
    {
        $allowed = self::ALLOWED_TRANSITIONS[$fromState] ?? [];

        return in_array($toState, $allowed, true);
    }
}
