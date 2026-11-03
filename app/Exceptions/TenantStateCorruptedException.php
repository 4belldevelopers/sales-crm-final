<?php

namespace App\Exceptions;

use RuntimeException;

/**
 * TenantStateCorruptedException
 *
 * Thrown when a tenant state version record is missing or corrupted during a mutation.
 * Enforces fail-hard monotonic versioning (INV-11). Silent insertions are strictly prohibited.
 */
class TenantStateCorruptedException extends RuntimeException
{
    public function __construct(string $message = 'Tenant state version record is missing or corrupted.', int $code = 500)
    {
        parent::__construct($message, $code);
    }
}
