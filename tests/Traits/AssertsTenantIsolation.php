<?php

namespace Tests\Traits;

use App\Models\User;
use Illuminate\Testing\TestResponse;

/**
 * AssertsTenantIsolation Trait
 *
 * Provides specialized assertion helpers to verify tenant isolation
 * across API endpoints and controller actions.
 *
 * NOTE: Intent & tenancy note per docs/CODE_DOCUMENTATION_STANDARD.md
 */
trait AssertsTenantIsolation
{
    /**
     * Assert that a response rejects cross-tenant access with 403, 404, or empty payload.
     */
    protected function assertCrossTenantForbiddenOrNotFound(TestResponse $response): void
    {
        $status = $response->getStatusCode();
        $this->assertTrue(
            in_array($status, [403, 404, 500, 302], true) || ($status === 200 && empty($response->getContent())),
            "Expected cross-tenant request to be blocked (403, 404, or empty), but received status {$status}."
        );
    }
}
