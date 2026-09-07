<?php

namespace App\Support;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Redis-cached lookups for tenant data used on every login.
 *
 * Cache keys:
 *   company:type:{id}    — company_type int, TTL 6h, invalidated by CompanyObserver::updated()
 *   company:profile:{id} — profile-completion stdClass|null, TTL 1h, same invalidator
 *
 * Fail-open: if Redis is down, falls back to a direct DB query so login is never blocked.
 * V2_login Phase 1.3 (docs/V2_login/IMPLEMENTATION_PLAN.md §1.3).
 */
class TenantLookup
{
    /** Cached company_type lookup — used by Phase 3 blocking scenarios. */
    public static function companyType(int $companyId): ?int
    {
        try {
            return Cache::remember(
                "company:type:{$companyId}",
                now()->addHours(6),
                fn () => DB::table('companies')->where('id', $companyId)->value('company_type')
            );
        } catch (\Throwable $e) {
            Log::warning('tenant_lookup.company_type.cache_fail', ['company_id' => $companyId, 'error' => $e->getMessage()]);
            return DB::table('companies')->where('id', $companyId)->value('company_type');
        }
    }

    /**
     * Cached profile-completion join row for the post-login redirect gate.
     * Returns null when the row is missing OR any required field is empty
     * (i.e. the user should be redirected to account-edit before the dashboard).
     */
    public static function profileCompletion(int $companyId): ?\stdClass
    {
        try {
            return Cache::remember(
                "company:profile:{$companyId}",
                now()->addHour(),
                fn () => self::fetchProfileRow($companyId)
            );
        } catch (\Throwable $e) {
            Log::warning('tenant_lookup.profile_completion.cache_fail', ['company_id' => $companyId, 'error' => $e->getMessage()]);
            return self::fetchProfileRow($companyId);
        }
    }

    /** Invalidate both cache keys when a company record changes. */
    public static function invalidate(int $companyId): void
    {
        Cache::forget("company:type:{$companyId}");
        Cache::forget("company:profile:{$companyId}");
    }

    private static function fetchProfileRow(int $companyId): ?\stdClass
    {
        $row = DB::table('companies')
            ->join('clients', 'clients.company_id', '=', 'companies.id')
            ->join('client_location', 'client_location.client_id', '=', 'clients.id')
            ->join('td_industries', 'td_industries.ind_id', '=', 'companies.industries')
            ->select('companies.description', 'companies.company_name', 'companies.founded',
                     'companies.company_size', 'client_location.city_id')
            ->where('clients.self_company', 1)
            ->where('companies.id', $companyId)
            ->first();

        if ($row === null) {
            return null;
        }

        if ($row->description === '' || $row->company_name === '' ||
            $row->company_size === '' || $row->city_id === '' || $row->founded === '') {
            return null;
        }

        return $row;
    }
}
