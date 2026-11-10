<?php

namespace App\Services\Zoom\Exceptions;

use RuntimeException;

/**
 * Class ZoomRateLimitException
 *
 * Intent: Thrown when Zoom API returns HTTP 429 (Too Many Requests).
 * Encapsulates the Retry-After interval for controlled backoff and telemetry.
 *
 * Author: Antigravity AI — Enterprise Zoom Integration Architecture
 * Complies with docs/CODE_DOCUMENTATION_STANDARD.md and docs/AI_RULES.md.
 */
class ZoomRateLimitException extends RuntimeException
{
    protected int $retryAfterSeconds;

    public function __construct(string $message = 'Zoom API rate limit exceeded.', int $retryAfterSeconds = 60, ?\Throwable $previous = null)
    {
        $this->retryAfterSeconds = $retryAfterSeconds;
        parent::__construct("{$message} (Retry after: {$retryAfterSeconds}s)", 429, $previous);
    }

    public function getRetryAfterSeconds(): int
    {
        return $this->retryAfterSeconds;
    }
}
