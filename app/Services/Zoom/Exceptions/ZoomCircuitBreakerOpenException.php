<?php

namespace App\Services\Zoom\Exceptions;

use RuntimeException;

/**
 * Class ZoomCircuitBreakerOpenException
 *
 * Intent: Thrown when the Zoom Integration Circuit Breaker is in the OPEN state,
 * preventing outbound requests to Zoom API during suspected third-party outages
 * and preserving application worker threads.
 *
 * Author: Antigravity AI — Enterprise Zoom Integration Architecture
 * Complies with docs/CODE_DOCUMENTATION_STANDARD.md and docs/AI_RULES.md.
 */
class ZoomCircuitBreakerOpenException extends RuntimeException
{
    protected int $cooldownRemainingSeconds;

    public function __construct(string $message = 'Zoom API Circuit Breaker is OPEN. Outbound requests are fast-failed to prevent system degradation.', int $cooldownRemainingSeconds = 30, ?\Throwable $previous = null)
    {
        $this->cooldownRemainingSeconds = $cooldownRemainingSeconds;
        parent::__construct("{$message} (Cooldown remaining: {$cooldownRemainingSeconds}s)", 503, $previous);
    }

    public function getCooldownRemainingSeconds(): int
    {
        return $this->cooldownRemainingSeconds;
    }
}
