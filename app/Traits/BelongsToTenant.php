<?php

namespace App\Traits;

use App\Scopes\TenantScope;

/**
 * BelongsToTenant Trait
 *
 * Automatically registers the TenantScope global scope on models representing tenant entities.
 *
 * NOTE: Intent & tenancy note per docs/CODE_DOCUMENTATION_STANDARD.md
 */
trait BelongsToTenant
{
    /**
     * Boot the BelongsToTenant trait for a model.
     */
    public static function bootBelongsToTenant(): void
    {
        static::addGlobalScope(new TenantScope());
    }
}
