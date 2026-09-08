<?php

namespace App\Support\Auth;

use Illuminate\Support\Facades\Auth;

/**
 * Class PermissionMemoizer
 *
 * Provides request-scoped in-memory caching (memoization) for authenticated user roles and permissions.
 * Bypasses redundant database queries during repetitive Spatie permission checks (`auth()->user()->can(...)`)
 * across Blade templates and API middleware during a single HTTP request lifecycle.
 *
 * Tenancy Note:
 * Memoization state is bound strictly to the current request execution context.
 * In Octane / FrankenPHP, the static cache array MUST be cleared between requests.
 */
class PermissionMemoizer
{
    /**
     * In-memory cache of user permission checks for the current request context.
     *
     * @var array<string, bool>
     */
    protected static array $memoizedPermissions = [];

    /**
     * Check if the authenticated user has a specific permission, using request memoization.
     *
     * @param  int|string|null  $companyId  Tenant company context for scope verification
     */
    public static function hasPermission(string $permission, int|string|null $companyId = null): bool
    {
        $user = Auth::user();
        if (! $user) {
            return false;
        }

        $effectiveCompanyId = $companyId ?? $user->company_id ?? 0;
        $key = "u:{$user->id}:c:{$effectiveCompanyId}:p:{$permission}";

        if (array_key_exists($key, static::$memoizedPermissions)) {
            return static::$memoizedPermissions[$key];
        }

        $hasPermission = $user->can($permission);
        static::$memoizedPermissions[$key] = $hasPermission;

        return $hasPermission;
    }

    /**
     * Flush all memoized permission states.
     * Must be called in Octane listeners between worker operations to prevent state leakage.
     */
    public static function flush(): void
    {
        static::$memoizedPermissions = [];
    }
}
