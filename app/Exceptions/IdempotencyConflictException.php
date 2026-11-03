<?php

namespace App\Exceptions;

use RuntimeException;

/**
 * IdempotencyConflictException
 *
 * Thrown when an incoming HTTP request conflicts with an existing idempotency record (INV-16),
 * such as payload hash mismatches or in-progress concurrent executions.
 */
class IdempotencyConflictException extends RuntimeException
{
    protected ?int $retryAfter;

    public function __construct(string $message = 'Idempotency conflict detected.', int $code = 409, ?int $retryAfter = null)
    {
        parent::__construct($message, $code);
        $this->retryAfter = $retryAfter;
    }

    public function getRetryAfter(): ?int
    {
        return $this->retryAfter;
    }
}
