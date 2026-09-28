<?php

namespace App\Services;

use App\Events\SecurityContextChangedEvent;
use App\Models\User;
use Illuminate\Support\Facades\Cache;

/**
 * Class CandidateDrawerSecurityService
 *
 * Intent: Enforces Tenant-Scoped permission_version and auth_context_hash generation,
 * mid-session security invalidations, and zero-MySQL-read authorization per docs/drawer_candidate.md.
 */
class CandidateDrawerSecurityService
{
    private const CACHE_PREFIX = 'tenant:permission_version:';

    /**
     * Get the current monotonic permission_version for the tenant.
     */
    public function getPermissionVersion(int $tenantId): int
    {
        if ($tenantId <= 0) {
            return 1;
        }

        return (int) Cache::get(self::CACHE_PREFIX.$tenantId, 1);
    }

    /**
     * Increment the tenant's permission_version and broadcast SECURITY_CONTEXT_CHANGED immediately.
     */
    public function incrementPermissionVersion(int $tenantId, ?int $userId = null, int|string $roleId = 1): int
    {
        $current = $this->getPermissionVersion($tenantId);
        $newVersion = $current + 1;
        Cache::put(self::CACHE_PREFIX.$tenantId, $newVersion, 86400 * 30);

        $authContextHash = $this->calculateAuthContextHash($tenantId, $userId ?? 0, $roleId, $newVersion);

        try {
            event(new SecurityContextChangedEvent($tenantId, $userId, $newVersion, $authContextHash));
        } catch (\Throwable) {
            // Non-blocking in environments without broadcast driver configured
        }

        return $newVersion;
    }

    /**
     * Calculate auth_context_hash = SHA256(tenant_id || user_id || role_id || permission_version)
     */
    public function calculateAuthContextHash(int $tenantId, int $userId, int|string $roleId, int $permissionVersion): string
    {
        return hash('sha256', "{$tenantId}|{$userId}|{$roleId}|{$permissionVersion}");
    }

    /**
     * Generate security context for user with zero MySQL reads.
     */
    public function getAuthContextForUser(?User $user): ?array
    {
        if (! $user) {
            return null;
        }

        $tenantId = (int) ($user->company_id ?? 0);
        $userId = (int) ($user->id ?? 0);
        $roleId = $user->role_id ?? ($user->role ?? 1);
        $permVersion = $this->getPermissionVersion($tenantId);
        $hash = $this->calculateAuthContextHash($tenantId, $userId, $roleId, $permVersion);

        return [
            'tenant_id' => $tenantId,
            'user_id' => $userId,
            'role_id' => $roleId,
            'permission_version' => $permVersion,
            'auth_context_hash' => $hash,
        ];
    }

    /**
     * Check if user is restricted from viewing candidates with 0 MySQL reads.
     */
    public function isUserRestricted(User $user): bool
    {
        $role = $user->role ?? ($user->getAttribute('role') ?? null);
        if ($role === 'recruiter_restricted') {
            return true;
        }

        return false;
    }

    /**
     * Check if user can view salary data with 0 MySQL reads.
     */
    public function canViewSalary(User $user): bool
    {
        $role = $user->role ?? ($user->getAttribute('role') ?? null);
        if ($role === 'recruiter_restricted') {
            return false;
        }

        if ($user->getAttribute('salary_view') === false || $user->getAttribute('salary_view') === 0) {
            return false;
        }

        return true;
    }
}
