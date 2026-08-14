<?php

namespace App\Services;

use App\Helper\GeneralMethod;
use Illuminate\Support\Facades\Cache;

/**
 * JobCacheKeys — Centralized Redis Cache Key Manager & Invalidation Service for Jobs Module.
 *
 * Intent: Unifies Redis cache key generation, TTL policies, and atomic cache invalidation logic across Add, Edit, and Submit flows.
 * Tenancy Note: All tenant key generators require explicit $companyId or $userId scoping.
 *
 * Author: Antigravity AI
 * Date: August 14, 2026
 */
class JobCacheKeys
{
    public const GLOBAL_QUALIFICATIONS_KEY = 'global_qualification_master';

    public const GLOBAL_COUNTRIES_KEY = 'global_countries_list';

    public const GLOBAL_COIN_RATES_KEY = 'global_coin_conversion_rates';

    public const DEFAULT_TTL_SECONDS = 86400; // 24 hours for master reference tables

    public const TENANT_TTL_SECONDS = 3600;   // 1 hour for tenant-specific datasets

    public static function categoriesKey(int $companyId): string
    {
        return 'tenant:'.$companyId.':categories';
    }

    public static function clientsKey(int $companyId): string
    {
        return 'tenant:'.$companyId.':clients';
    }

    public static function activeJobsCountKey(int $companyId): string
    {
        return 'company:'.$companyId.':active_jobs_count';
    }

    public static function userProfileKey(int $userId): string
    {
        return 'user_profile:'.$userId;
    }

    public static function jobEditKey(int $companyId, int $jobId): string
    {
        return 'tenant:'.$companyId.':job_edit:'.$jobId;
    }

    public static function userJobsListVersionKey(int $userId): string
    {
        return 'alljobslist_version_user_'.$userId;
    }

    public static function userJobDetailsVersionKey(int $userId): string
    {
        return 'jobDetails_version_user_'.$userId;
    }

    /**
     * Clear all user-specific job version caches.
     */
    public static function clearUserJobsCache(int $userId): void
    {
        GeneralMethod::cacheClean(self::userJobsListVersionKey($userId));
        GeneralMethod::cacheClean(self::userJobDetailsVersionKey($userId));
        Cache::forget(self::userProfileKey($userId));
    }

    /**
     * Clear tenant-level cached categories, clients, and active job counters.
     */
    public static function clearTenantCache(int $companyId): void
    {
        Cache::forget(self::categoriesKey($companyId));
        Cache::forget(self::clientsKey($companyId));
        Cache::forget(self::activeJobsCountKey($companyId));
    }
}
