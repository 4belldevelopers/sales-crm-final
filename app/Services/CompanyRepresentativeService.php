<?php

namespace App\Services;

use App\Helper\GeneralMethod;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;

/**
 * CompanyRepresentativeService
 *
 * Single source of truth for company communication representatives (Client & Agency),
 * default representative management, job-level agency representative mapping, and
 * tenant-scoped cache invalidation.
 *
 * Rules:
 * 1. Default Representative: Configurable per company for Client Communication and Agency Communication.
 * 2. Job-Level Hierarchy: Job + Agency Relationship -> User Who Shared/Added Agency -> Representative.
 *    The job creator does not automatically become the representative unless they shared/added the agency.
 * 3. Direct Client Visibility: Direct Client views reflect the exact representative who shared the job with the agency.
 * 4. Zero Confusion: Strictly differentiates Job Created By, Agency Shared By, Client Representative, and Agency Representative.
 */
class CompanyRepresentativeService
{
    const CACHE_TTL_SECONDS = 2592000; // 30 days

    /**
     * Get default communication representatives (Client & Agency) for a company.
     * Checks Redis first, then database (company_tags / root_user fallback).
     *
     * @param int $companyId
     * @return array
     */
    public function getDefaultRepresentatives(int $companyId): array
    {
        if ($companyId <= 0) {
            return [
                'client_rep' => null,
                'agency_rep' => null,
            ];
        }

        $cacheKey = "ats:company_rep:defaults:{$companyId}";
        try {
            $cached = Cache::get($cacheKey);
            if (!empty($cached) && is_array($cached)) {
                return $cached;
            }
        } catch (\Throwable $e) {
            Log::warning("CompanyRepresentativeService: cache read error for {$companyId}: " . $e->getMessage());
        }

        // 1. Check persistent tags in company_tags
        $tags = DB::table('company_tags')
            ->where('company_id', $companyId)
            ->where(function ($q) {
                $q->where('tag_name', 'like', 'rep:client:%')
                  ->orWhere('tag_name', 'like', 'rep:agency:%');
            })
            ->pluck('tag_name')
            ->toArray();

        $clientRepUserId = null;
        $agencyRepUserId = null;

        foreach ($tags as $tag) {
            if (str_starts_with($tag, 'rep:client:')) {
                $uid = (int) str_replace('rep:client:', '', $tag);
                if ($uid > 0) {
                    $clientRepUserId = $uid;
                }
            } elseif (str_starts_with($tag, 'rep:agency:')) {
                $uid = (int) str_replace('rep:agency:', '', $tag);
                if ($uid > 0) {
                    $agencyRepUserId = $uid;
                }
            }
        }

        // 2. Fallback to Root User / First Active Admin if not explicitly set
        if (!$clientRepUserId || !$agencyRepUserId) {
            $fallbackUser = DB::table('users')
                ->where('company_id', $companyId)
                ->where('status', 'active')
                ->orderByDesc('root_user')
                ->orderBy('id', 'asc')
                ->first(['id']);

            $defaultFallbackId = $fallbackUser ? (int) $fallbackUser->id : 0;
            if (!$clientRepUserId) {
                $clientRepUserId = $defaultFallbackId;
            }
            if (!$agencyRepUserId) {
                $agencyRepUserId = $defaultFallbackId;
            }
        }

        $clientRep = $clientRepUserId > 0 ? $this->resolveUserSummary($clientRepUserId, $companyId) : null;
        $agencyRep = $agencyRepUserId > 0 ? $this->resolveUserSummary($agencyRepUserId, $companyId) : null;

        $result = [
            'client_rep' => $clientRep,
            'agency_rep' => $agencyRep,
            'client_rep_id' => $clientRepUserId,
            'agency_rep_id' => $agencyRepUserId,
        ];

        try {
            Cache::put($cacheKey, $result, now()->addSeconds(self::CACHE_TTL_SECONDS));
        } catch (\Throwable $e) {
            Log::warning("CompanyRepresentativeService: cache write error for {$companyId}: " . $e->getMessage());
        }

        return $result;
    }

    /**
     * Set default representative for Client or Agency communication.
     *
     * @param int $companyId
     * @param int $userId
     * @param string $type ('client' or 'agency')
     * @return array
     */
    public function setDefaultRepresentative(int $companyId, int $userId, string $type): array
    {
        if ($companyId <= 0 || $userId <= 0) {
            return ['status' => 'error', 'message' => 'Invalid company or user ID.'];
        }

        $type = strtolower(trim($type));
        if (!in_array($type, ['client', 'agency'], true)) {
            return ['status' => 'error', 'message' => 'Invalid representative type. Must be "client" or "agency".'];
        }

        // Validate user belongs to company and is active
        $user = DB::table('users')
            ->where('id', $userId)
            ->where('company_id', $companyId)
            ->where('status', 'active')
            ->first(['id', 'name', 'email']);

        if (!$user) {
            return ['status' => 'error', 'message' => 'Selected user was not found or is not active in this company.'];
        }

        $tagName = "rep:{$type}:{$userId}";
        $prefix = "rep:{$type}:%";

        DB::transaction(function () use ($companyId, $tagName, $prefix) {
            // Delete previous default for this type
            DB::table('company_tags')
                ->where('company_id', $companyId)
                ->where('tag_name', 'like', $prefix)
                ->delete();

            // Insert new default tag
            DB::table('company_tags')->insert([
                'company_id' => $companyId,
                'tag_name' => $tagName,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        });

        // Invalidate Cache
        $this->invalidateTenantRepresentativeCache($companyId);

        $typeLabel = $type === 'client' ? 'Client Representative' : 'Agency Representative';

        return [
            'status' => 'success',
            'message' => "Successfully set {$user->name} as the Default {$typeLabel}.",
            'representative' => $this->resolveUserSummary($userId, $companyId),
            'type' => $type,
        ];
    }

    /**
     * Resolves the authoritative representative for a specific Job + Agency relationship.
     * Strict Priority:
     * Job + Agency Relationship -> User Who Shared/Added Agency -> Representative
     *
     * The job creator does not automatically become the representative unless they
     * are also the user who shared/added the agency.
     *
     * @param int $jobId
     * @param int $agencyCompanyId
     * @param int $fromCompanyId
     * @return array
     */
    public function resolveJobAgencyRepresentative(int $jobId, int $agencyCompanyId, int $fromCompanyId): array
    {
        $job = DB::table('jobs')
            ->where('id', $jobId)
            ->first(['id', 'title', 'user_id', 'company_id', 'created_at']);

        $jobCreatorId = $job ? (int) $job->user_id : 0;
        $jobCreator = $jobCreatorId > 0 ? $this->resolveUserSummary($jobCreatorId, $fromCompanyId) : null;

        // 1. Look for specific sharing relation in recommendation_relation
        $rec = DB::table('recommendation_relation')
            ->where('job_id', $jobId)
            ->where('to_company_id', $agencyCompanyId)
            ->where('from_company_id', $fromCompanyId)
            ->orderBy('id', 'desc')
            ->first(['id', 'from_user_id', 'created_at', 'from_created_at', 'final_status']);

        $sharerUserId = ($rec && !empty($rec->from_user_id)) ? (int) $rec->from_user_id : 0;
        $sharedAt = $rec ? ($rec->from_created_at ?? $rec->created_at) : null;
        $relationId = $rec ? (int) $rec->id : 0;

        if ($sharerUserId > 0) {
            $repUser = $this->resolveUserSummary($sharerUserId, $fromCompanyId);
            $source = 'job_agency_share';
        } else {
            // Fallback to company default agency representative
            $defaults = $this->getDefaultRepresentatives($fromCompanyId);
            $repUser = $defaults['agency_rep'] ?? null;
            $sharerUserId = $defaults['agency_rep_id'] ?? 0;
            $source = 'company_default_agency_rep';
        }

        return [
            'job_id' => $jobId,
            'job_title' => $job ? $job->title : 'Requisition',
            'agency_company_id' => $agencyCompanyId,
            'recommendation_relation_id' => $relationId,
            'representative' => $repUser,
            'representative_user_id' => $sharerUserId,
            'representative_source' => $source,
            'job_created_by' => $jobCreator,
            'job_created_by_user_id' => $jobCreatorId,
            'is_creator_the_rep' => ($sharerUserId > 0 && $sharerUserId === $jobCreatorId),
            'shared_at' => $sharedAt ? Carbon::parse($sharedAt)->format('d M Y') : null,
        ];
    }

    /**
     * Change / Reassign the representative for a specific Job + Agency relationship.
     * Updates recommendation_relation.from_user_id atomically.
     *
     * @param int $companyId
     * @param int $jobId
     * @param int $agencyCompanyId
     * @param int $newUserId
     * @return array
     */
    public function changeJobAgencyRepresentative(int $companyId, int $jobId, int $agencyCompanyId, int $newUserId): array
    {
        if ($companyId <= 0 || $jobId <= 0 || $agencyCompanyId <= 0 || $newUserId <= 0) {
            return ['status' => 'error', 'message' => 'Invalid parameters provided.'];
        }

        // Validate target user
        $newUser = DB::table('users')
            ->where('id', $newUserId)
            ->where('company_id', $companyId)
            ->where('status', 'active')
            ->first(['id', 'name', 'email']);

        if (!$newUser) {
            return ['status' => 'error', 'message' => 'Selected representative was not found or is not active.'];
        }

        $rec = DB::table('recommendation_relation')
            ->where('job_id', $jobId)
            ->where('to_company_id', $agencyCompanyId)
            ->where('from_company_id', $companyId)
            ->first(['id']);

        if ($rec) {
            DB::table('recommendation_relation')
                ->where('id', $rec->id)
                ->update([
                    'from_user_id' => $newUserId,
                    'updated_at' => now(),
                ]);
        } else {
            // Create a pending or mapped relation
            DB::table('recommendation_relation')->insert([
                'from_company_id' => $companyId,
                'from_user_id' => $newUserId,
                'from_status' => 1,
                'from_created_at' => now(),
                'to_company_id' => $agencyCompanyId,
                'to_status' => 0,
                'final_status' => 0,
                'job_id' => $jobId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Clean cache for this job and tenant
        $this->invalidateTenantRepresentativeCache($companyId);
        Cache::forget("ats:job_proj:{$jobId}");

        return [
            'status' => 'success',
            'message' => "Representative successfully changed to {$newUser->name} for this agency relationship.",
            'new_representative' => $this->resolveUserSummary($newUserId, $companyId),
        ];
    }

    /**
     * Resolves compact user representation summary.
     *
     * @param int $userId
     * @param int $companyId
     * @return array|null
     */
    public function resolveUserSummary(int $userId, int $companyId): ?array
    {
        if ($userId <= 0) {
            return null;
        }

        $cacheKey = "ats:user_rep_summary:{$companyId}:{$userId}";
        try {
            $cached = Cache::get($cacheKey);
            if (!empty($cached) && is_array($cached)) {
                return $cached;
            }
        } catch (\Throwable $e) {}

        $user = DB::table('users')
            ->leftJoin('users_profile', 'users_profile.user_id', '=', 'users.id')
            ->leftJoin('role_user', 'role_user.user_id', '=', 'users.id')
            ->leftJoin('roles', 'roles.id', '=', 'role_user.role_id')
            ->where('users.id', $userId)
            ->where('users.company_id', $companyId)
            ->select([
                'users.id',
                'users.name',
                'users.email',
                'users.mobile',
                'users.calling_code',
                'users.image',
                'users.status',
                'users.root_user',
                'users_profile.designation',
                'roles.display_name as role_name',
            ])
            ->first();

        if (!$user) {
            return null;
        }

        // Workload & Representative Metrics
        $agencySharesCount = DB::table('recommendation_relation')
            ->where('from_company_id', $companyId)
            ->where('from_user_id', $userId)
            ->count();

        $captainJobsCount = DB::table('job_assign')
            ->where('company_id', $companyId)
            ->where('spok_user_id', $userId)
            ->distinct()
            ->count('job_id');

        $activeJobsCount = DB::table('job_assign')
            ->join('jobs', 'jobs.id', '=', 'job_assign.job_id')
            ->where('job_assign.company_id', $companyId)
            ->where('job_assign.user_id', $userId)
            ->where('jobs.status', 'active')
            ->distinct()
            ->count('jobs.id');

        $roleTitle = !empty($user->role_name) ? ucwords(strtolower($user->role_name)) : 'Member';
        $designation = !empty($user->designation) ? ucwords(strtolower($user->designation)) : $roleTitle;

        $phone = '';
        if (!empty($user->mobile)) {
            $phone = (!empty($user->calling_code) ? '+' . ltrim($user->calling_code, '+') . ' ' : '') . $user->mobile;
        }

        $summary = [
            'id' => (int) $user->id,
            'name' => ucwords(strtolower($user->name)),
            'email' => strtolower($user->email),
            'mobile' => $phone,
            'raw_mobile' => $user->mobile,
            'calling_code' => $user->calling_code,
            'image' => $user->image ?: null,
            'designation' => $designation,
            'role_name' => $roleTitle,
            'status' => $user->status,
            'is_root_user' => (bool) $user->root_user,
            'agency_shares_count' => (int) $agencySharesCount,
            'captain_jobs_count' => (int) $captainJobsCount,
            'active_jobs_count' => (int) $activeJobsCount,
        ];

        try {
            Cache::put($cacheKey, $summary, now()->addMinutes(60));
        } catch (\Throwable $e) {}

        return $summary;
    }

    /**
     * Get team members who are eligible to act as company representatives.
     * All active users in the company can be chosen.
     *
     * @param int $companyId
     * @return array
     */
    public function getEligibleRepresentatives(int $companyId): array
    {
        $users = DB::table('users')
            ->leftJoin('users_profile', 'users_profile.user_id', '=', 'users.id')
            ->leftJoin('role_user', 'role_user.user_id', '=', 'users.id')
            ->leftJoin('roles', 'roles.id', '=', 'role_user.role_id')
            ->where('users.company_id', $companyId)
            ->where('users.status', 'active')
            ->select([
                'users.id',
                'users.name',
                'users.email',
                'users.mobile',
                'users.calling_code',
                'users.image',
                'users.root_user',
                'users_profile.designation',
                'roles.display_name as role_name',
            ])
            ->orderByDesc('users.root_user')
            ->orderBy('users.name', 'asc')
            ->get();

        $defaults = $this->getDefaultRepresentatives($companyId);
        $clientRepId = $defaults['client_rep_id'] ?? 0;
        $agencyRepId = $defaults['agency_rep_id'] ?? 0;

        return $users->map(function ($u) use ($clientRepId, $agencyRepId) {
            $isClient = ($u->id == $clientRepId);
            $isAgency = ($u->id == $agencyRepId);

            $roleTitle = !empty($u->role_name) ? ucwords(strtolower($u->role_name)) : 'Member';
            $desig = !empty($u->designation) ? ucwords(strtolower($u->designation)) : $roleTitle;

            $phone = '';
            if (!empty($u->mobile)) {
                $phone = (!empty($u->calling_code) ? '+' . ltrim($u->calling_code, '+') . ' ' : '') . $u->mobile;
            }

            return [
                'id' => (int) $u->id,
                'name' => ucwords(strtolower($u->name)),
                'email' => strtolower($u->email),
                'mobile' => $phone,
                'image' => $u->image ?: null,
                'designation' => $desig,
                'role_name' => $roleTitle,
                'is_root_user' => (bool) $u->root_user,
                'is_client_rep' => $isClient,
                'is_agency_rep' => $isAgency,
            ];
        })->toArray();
    }

    /**
     * Invalidate tenant representative cache across Dragonfly/Redis.
     *
     * @param int $companyId
     * @return void
     */
    public function invalidateTenantRepresentativeCache(int $companyId): void
    {
        if ($companyId <= 0) {
            return;
        }

        try {
            Cache::forget("ats:company_rep:defaults:{$companyId}");
            Cache::forget("ats:teamcounts:{$companyId}");

            $versionKey = 'ats:teamversion:company_' . $companyId;
            if (Cache::has($versionKey)) {
                Cache::increment($versionKey);
            } else {
                Cache::put($versionKey, 2, now()->addDays(30));
            }
        } catch (\Throwable $e) {
            Log::warning("CompanyRepresentativeService: cache invalidation error: " . $e->getMessage());
        }
    }
}
