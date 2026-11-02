<?php

namespace App\Services;

use App\DTO\Commercials\CommercialStateDTO;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

/**
 * CommercialReadModelService — CQRS Read Model for Commercials & Hiring Services.
 *
 * Governing Standards:
 * - Rule 1: 0% MySQL reads on web/API request paths (served 100% via Redis projection).
 * - Rule 4: 10,000 concurrency / 0% server & database strain.
 * - Rule 5: Zero stale data in ATS.
 * - Rule 6: Atomic multi-tenant cache invalidation scoped by company_id.
 *
 * Tenancy: All queries are strictly scoped by company_id.
 */
class CommercialReadModelService
{
    private const TTL_PROJECTION = 86400; // 24 hours

    private const TTL_MASTER_DATA = 86400; // 24 hours

    /** Company-commercial policy options shared across drawer rendering and DTOs. */
    public const COMMERCIAL_REPLACEMENT_PERIODS = [30 => '30 Days', 60 => '60 Days', 90 => '90 Days', 180 => '180 Days'];

    public const COMMERCIAL_REPLACEMENT_TYPES = ['free_replacement' => 'Free Replacement', 'credit_note' => 'Credit Note', 'none' => 'No Replacement'];

    public const COMMERCIAL_REPLACEMENT_ELIGIBILITY = ['early_exit' => 'Candidate Resignation / Early Exit', 'employer_termination' => 'Employer Termination', 'any_separation' => 'Any Early Separation', 'any_conditions' => 'Any conditions'];

    public const COMMERCIAL_BILLING_FREQUENCIES = ['one_time' => 'One Time', 'weekly' => 'Weekly', 'monthly' => 'Monthly', 'quarterly' => 'Quarterly'];

    public const COMMERCIAL_BILLING_TRIGGERS = ['candidate_joined' => 'Candidate Joined', 'approved_timesheet' => 'Approved Timesheet', 'milestone_completed' => 'Milestone Completed'];

    public const COMMERCIAL_PAYMENT_TERMS = [15 => 'Net 15 Days', 30 => 'Net 30 Days', 45 => 'Net 45 Days', 60 => 'Net 60 Days'];

    /**
     * Get the tenant's canonical commercial state with ≤ 1 logical Redis fetch.
     * Enforces 0% MySQL reads when cached.
     */
    public static function getTenantCommercialsState(int $companyId): CommercialStateDTO
    {
        $version = (int) Cache::get("company:{$companyId}:commercials:version", 1);
        $cacheKey = "company:{$companyId}:commercials:projection:v{$version}";

        $cachedData = Cache::get($cacheKey);
        if ($cachedData && is_array($cachedData)) {
            return CommercialStateDTO::fromProjections(
                $companyId,
                $version,
                $cachedData['fulltime'] ?? null,
                $cachedData['contract'] ?? null
            );
        }

        // Cache Warm-up (Strictly tenant-scoped)
        $projection = self::warmupTenantProjection($companyId, $version);
        Cache::put($cacheKey, $projection, self::TTL_PROJECTION);

        return CommercialStateDTO::fromProjections(
            $companyId,
            $version,
            $projection['fulltime'] ?? null,
            $projection['contract'] ?? null
        );
    }

    /**
     * Warm up tenant projection from database with optimized eager batching (no N+1 queries).
     */
    private static function warmupTenantProjection(int $companyId, int $version): array
    {
        $commercialRows = DB::table('commericials')
            ->where('company_id', $companyId)
            ->get()
            ->keyBy('commtype');

        $commercialIds = $commercialRows->pluck('id')->all();

        // Batch fetch details
        $allDetails = ! empty($commercialIds)
            ? DB::table('commericial_details')
                ->whereIn('commericial_id', $commercialIds)
                ->where('company_id', $companyId)
                ->get()
                ->groupBy('commericial_id')
            : collect();

        // Batch fetch country IDs
        $allCountries = ! empty($commercialIds)
            ? DB::table('commericial_countries')
                ->whereIn('commericial_id', $commercialIds)
                ->where('company_id', $companyId)
                ->get()
                ->groupBy('commericial_id')
            : collect();

        // Cached country map for ID -> Name lookup
        $countryNameMap = self::getCountryIdToNameMap();

        $defaultCurrencyKey = CurrencyService::getTenantCurrencyKey($companyId);

        $buildServiceData = function ($commtype) use ($commercialRows, $allDetails, $allCountries, $countryNameMap, $defaultCurrencyKey, $companyId) {
            $row = $commercialRows->get($commtype);
            if (! $row) {
                return [
                    'id' => 0,
                    'company_id' => $companyId,
                    'commtype' => $commtype,
                    'status' => 0,
                    'rateType' => ($commtype === 2) ? 2 : 1,
                    'sal_mode' => 0,
                    'salary_currency' => $defaultCurrencyKey,
                    'currency_code' => CurrencyService::resolveCurrencyCode($defaultCurrencyKey),
                    'replacement_period_days' => ($commtype === 2) ? 90 : 30,
                    'replacement_type' => 'free_replacement',
                    'replacement_eligibility' => 'early_exit',
                    'billing_frequency' => ($commtype === 2) ? 'one_time' : 'monthly',
                    'billing_trigger' => ($commtype === 2) ? 'candidate_joined' : 'approved_timesheet',
                    'payment_terms_days' => 30,
                    'commericial_details' => [],
                    'commericial_countries' => [],
                    'country_names' => [],
                ];
            }

            $details = ($allDetails->get($row->id) ?? collect())->map(function ($d) {
                return [
                    'id' => $d->id,
                    'commericial_id' => $d->commericial_id,
                    'exp' => $d->exp,
                    'rate' => $d->rate,
                    'company_id' => $d->company_id,
                    'commtype' => $d->commtype,
                ];
            })->all();

            $cIds = ($allCountries->get($row->id) ?? collect())->pluck('country_id')->all();
            $cNames = array_values(array_filter(array_map(fn ($cid) => $countryNameMap[$cid] ?? null, $cIds)));

            $salaryCurrency = $row->salary_currency;
            if ($salaryCurrency === null || ($salaryCurrency == 4 && $defaultCurrencyKey !== 4)) {
                $salaryCurrency = $defaultCurrencyKey;
            }

            return [
                'id' => (int) $row->id,
                'company_id' => (int) $row->company_id,
                'commtype' => (int) $row->commtype,
                'status' => (int) $row->status,
                'rateType' => (int) $row->rateType,
                'sal_mode' => (int) $row->sal_mode,
                'salary_currency' => (int) $salaryCurrency,
                'currency_code' => CurrencyService::resolveCurrencyCode($salaryCurrency),
                'replacement_period_days' => (int) ($row->replacement_period_days ?? ($commtype === 2 ? 90 : 30)),
                'replacement_type' => (string) ($row->replacement_type ?? 'free_replacement'),
                'replacement_eligibility' => (string) ($row->replacement_eligibility ?? 'early_exit'),
                'billing_frequency' => (string) ($row->billing_frequency ?? ($commtype === 2 ? 'one_time' : 'monthly')),
                'billing_trigger' => (string) ($row->billing_trigger ?? ($commtype === 2 ? 'candidate_joined' : 'approved_timesheet')),
                'payment_terms_days' => (int) ($row->payment_terms_days ?? 30),
                'commericial_details' => $details,
                'commericial_countries' => $cIds,
                'country_names' => $cNames,
            ];
        };

        return [
            'fulltime' => $buildServiceData(2),
            'contract' => $buildServiceData(1),
        ];
    }

    /**
     * Get cached master dataset: Countries ID to Name Map.
     */
    public static function getCountryIdToNameMap(): array
    {
        return Cache::remember('master_country_id_map:v1', self::TTL_MASTER_DATA, function () {
            return DB::table('countries')->pluck('country_name', 'id')->toArray();
        });
    }

    /**
     * Get cached master dataset: All Active Countries.
     */
    public static function getMasterCountries()
    {
        return Cache::remember('master_countries_list:v1', self::TTL_MASTER_DATA, function () {
            return DB::table('countries')->orderBy('country_name')->get();
        });
    }

    /**
     * Get cached master dataset: All Industries.
     */
    public static function getMasterIndustries()
    {
        return Cache::remember('master_industries_list:v1', self::TTL_MASTER_DATA, function () {
            return DB::table('td_industries')->get();
        });
    }

    /**
     * Get cached master dataset: Default Rates.
     */
    public static function getMasterDefaultRates()
    {
        return Cache::remember('master_default_rates:v1', self::TTL_MASTER_DATA, function () {
            return DB::table('default_rates')->first();
        });
    }

    /**
     * Get cached master dataset: Experience Bands.
     */
    public static function getCommercialExperienceBands()
    {
        return Cache::remember('commercial_experience_bands:v1', self::TTL_MASTER_DATA, function () {
            return DB::table('commercial_experience_bands')
                ->select(['id', 'code', 'label', 'min_experience', 'max_experience', 'sort_order'])
                ->where('is_active', 1)
                ->orderBy('sort_order')
                ->get();
        });
    }

    /**
     * Atomically invalidate all tenant commercial cache keys per Rule 6.
     */
    public static function invalidateTenantCache(int $companyId): void
    {
        $currentVersion = (int) Cache::get("company:{$companyId}:commercials:version", 1);
        $nextVersion = $currentVersion + 1;

        Cache::forever("company:{$companyId}:commercials:version", $nextVersion);
        Cache::forget("company:{$companyId}:commercials:projection:v{$currentVersion}");
        Cache::forget("company:{$companyId}:commercials:projection:v{$nextVersion}");
        Cache::forget("account_settings:company:{$companyId}");
        Cache::forever("agency_drawer_version_company_{$companyId}", ((int) Cache::get("agency_drawer_version_company_{$companyId}", 1)) + 1);
    }
}
