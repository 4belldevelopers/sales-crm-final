<?php

namespace App\Services;

use App\Exceptions\TenantStateCorruptedException;
use Illuminate\Support\Facades\DB;

/**
 * TenantVersioningService
 *
 * Implements INV-11 & INV-12: Universal Domain Versioning Protocol.
 *
 * Provides fail-hard, pessimistic row-locked monotonic version increments across all core domains
 * (team, jobs, clients, commercials, candidates). Silent insertions on mutations are strictly prohibited.
 */
class TenantVersioningService
{
    public const VALID_DOMAINS = [
        'team',
        'jobs',
        'clients',
        'commercials',
        'candidates',
    ];

    /**
     * Allocates the next monotonic version number within a pessimistic transaction.
     *
     * @throws TenantStateCorruptedException
     */
    public static function allocateNextVersion(int $companyId, string $domain, ?int $eventId = null): int
    {
        if (! in_array($domain, self::VALID_DOMAINS, true)) {
            throw new TenantStateCorruptedException("Invalid tenant domain [{$domain}] requested for versioning.");
        }

        $row = DB::table('tenant_state_versions')
            ->where('company_id', $companyId)
            ->where('domain', $domain)
            ->lockForUpdate()
            ->first();

        if (! $row) {
            throw new TenantStateCorruptedException("Tenant state version record missing for company {$companyId} domain {$domain}.");
        }

        $nextVersion = ((int) $row->version) + 1;

        DB::table('tenant_state_versions')
            ->where('company_id', $companyId)
            ->where('domain', $domain)
            ->update([
                'version' => $nextVersion,
                'last_event_id' => $eventId,
                'updated_at' => now(),
            ]);

        return $nextVersion;
    }

    /**
     * Reads the current version for a tenant domain.
     */
    public static function getCurrentVersion(int $companyId, string $domain): int
    {
        $version = DB::table('tenant_state_versions')
            ->where('company_id', $companyId)
            ->where('domain', $domain)
            ->value('version');

        return $version !== null ? (int) $version : 1;
    }
}
