<?php

namespace App\Exceptions;

use RuntimeException;

/**
 * DomainProtectionException
 *
 * Thrown when an invitation target email violates cross-tenant domain protection (INV-15)
 * or attempts to use disposable temporary burner domains.
 */
class DomainProtectionException extends RuntimeException
{
    public function __construct(string $message = 'Target email domain violates domain protection policies.', int $code = 403)
    {
        parent::__construct($message, $code);
    }
}
