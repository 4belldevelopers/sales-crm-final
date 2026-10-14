<?php

namespace App\Enums;

/**
 * MarketplaceState
 *
 * Master utility and projection aggregator for the 6-dimensional Marketplace state machine.
 * Computes legacy public_market_status projection dynamically and validates state transitions.
 */
class MarketplaceState
{
    /**
     * Compute legacy public_market_status integer projection from 6-dimensional state.
     */
    public static function computeLegacyStatus(
        ?string $pubState,
        ?string $modState,
        ?string $claimCap,
        ?string $jobStatus = 'active',
        ?string $commState = 'configured'
    ): int {
        // Suspended moderation takes precedence
        if ($modState === MarketModState::SUSPENDED->value) {
            return LegacyMarketStatus::SUSPENDED->value; // 5
        }

        // Withdrawn, inactive or un-published by recruiter
        if ($pubState === MarketPubState::UNPUBLISHED->value || $pubState === MarketPubState::WITHDRAWN->value || $jobStatus !== 'active') {
            return LegacyMarketStatus::UNPUBLISHED->value; // 6
        }

        // Private / draft
        if ($pubState === MarketPubState::PRIVATE->value || empty($pubState)) {
            return LegacyMarketStatus::PRIVATE->value; // 0
        }

        // Pending review or pending moderation
        if ($pubState === MarketPubState::PENDING_REVIEW->value || $modState === MarketModState::PENDING->value) {
            return LegacyMarketStatus::PENDING->value; // 1
        }

        // Moderation rejected
        if ($modState === MarketModState::REJECTED->value) {
            return LegacyMarketStatus::REJECTED->value; // 3
        }

        // Approved / Auto-approved and published
        if (
            $pubState === MarketPubState::PUBLISHED->value &&
            in_array($modState, [MarketModState::APPROVED->value, MarketModState::AUTO_APPROVED->value, MarketModState::NOT_REQUIRED->value])
        ) {
            if ($claimCap === ClaimCapacity::LIMIT_REACHED->value) {
                return LegacyMarketStatus::VENDOR_LIMIT->value; // 4
            }

            return LegacyMarketStatus::APPROVED->value; // 2
        }

        return LegacyMarketStatus::PRIVATE->value; // 0
    }
}
