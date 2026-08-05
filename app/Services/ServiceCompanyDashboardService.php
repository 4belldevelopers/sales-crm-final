<?php

/**
 * ServiceCompanyDashboardService — extracted KPI/leaderboard/interview data layer.
 *
 * Extracted from ServiceCompanyDashboardController (V2 Phase 3).
 * All methods return plain arrays; no view knowledge here.
 * Tenancy: every raw DB::table() call filters by company_id explicitly (Rule #3).
 *
 * Used by:
 *   - ServiceCompanyDashboardController::index()  (passes data to Blade)
 *   - ServiceCompanyDashboardApiController        (returns JSON for Phase 4 API)
 */

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class ServiceCompanyDashboardService
{
    /** TTL for compact JSON cache entries (Phase 4). */
    private const KPI_TTL_SECONDS = 60;

    /**
     * Core KPI metrics shown on the dashboard top cards.
     * Cached per company for 60 seconds under key ats:v2:dashboard:{id}:kpis (~2.5 KB).
     */
    public function getCompanyKpiMetrics(int $companyId): array
    {
        $cacheKey = "ats:v2:dashboard:{$companyId}:kpis";

        return Cache::remember($cacheKey, self::KPI_TTL_SECONDS, function () use ($companyId) {
            $activeJobsCount  = DB::table('jobs')
                ->where('company_id', $companyId)
                ->where('status', 'active')
                ->count();

            $activeUsersCount = DB::table('users')
                ->where('company_id', $companyId)
                ->where('status', 'active')
                ->count();

            $operationalCoin  = DB::table('company_coins')
                ->where('company_id', $companyId)
                ->where('event_type', 'operational')
                ->first();

            $salesCoin = DB::table('company_coins')
                ->where('company_id', $companyId)
                ->where('event_type', 'sales')
                ->first();

            $todayInterviewCount = \App\Models\InterviewSchedule::whereBetween('schedule_date', [
                Carbon::now(company()->timezone)->startOfDay(),
                Carbon::now(company()->timezone)->endOfDay(),
            ])->count();

            return [
                'active_jobs'           => $activeJobsCount,
                'active_users'          => $activeUsersCount,
                'today_interviews'      => $todayInterviewCount,
                'operational_coins'     => $operationalCoin ? (float) $operationalCoin->coins : 0.0,
                'sales_coins'           => $salesCoin ? (float) $salesCoin->coins : 0.0,
                'cached_at'             => now()->toIso8601String(),
            ];
        });
    }

    /**
     * Recruiter leaderboard: submissions per recruiter over the last 30 days.
     * Cached per company for 60 seconds under key ats:v2:dashboard:{id}:leaderboard.
     */
    public function getRecruiterLeaderboard(int $companyId): array
    {
        $cacheKey = "ats:v2:dashboard:{$companyId}:leaderboard";

        return Cache::remember($cacheKey, self::KPI_TTL_SECONDS, function () use ($companyId) {
            $startDate = Carbon::now()->subDays(30)->startOfDay();

            return DB::table('add_job_wise_condidates')
                ->join('users', 'users.id', '=', 'add_job_wise_condidates.submitted_by')
                ->where('add_job_wise_condidates.company_id', $companyId)
                ->where('add_job_wise_condidates.created_at', '>=', $startDate)
                ->select(
                    'users.id as user_id',
                    'users.name as recruiter_name',
                    'users.image as recruiter_image',
                    DB::raw('COUNT(add_job_wise_condidates.id) as submissions')
                )
                ->groupBy('users.id', 'users.name', 'users.image')
                ->orderByDesc('submissions')
                ->limit(10)
                ->get()
                ->map(fn ($r) => (array) $r)
                ->toArray();
        });
    }

    /**
     * Today's interview schedule for a specific user (or all if $userId = 0).
     * Cached per company+user for 60 seconds under key ats:v2:dashboard:{id}:interviews:{uid}.
     */
    public function getTodayInterviews(int $companyId, int $userId = 0): array
    {
        $cacheKey = "ats:v2:dashboard:{$companyId}:interviews:{$userId}";

        return Cache::remember($cacheKey, self::KPI_TTL_SECONDS, function () use ($companyId, $userId) {
            $query = \App\Models\InterviewSchedule::with([])
                ->whereBetween('schedule_date', [
                    Carbon::now(company()->timezone)->startOfDay(),
                    Carbon::now(company()->timezone)->endOfDay(),
                ])
                ->orderBy('schedule_date');

            if ($userId > 0) {
                // Filter to interviews the logged-in user is part of.
                $query->whereRaw("JSON_CONTAINS(interviewers, ?)", [json_encode((string) $userId)]);
            }

            return $query->limit(50)
                ->get()
                ->map(fn ($i) => [
                    'id'            => $i->id,
                    'schedule_date' => Carbon::parse($i->schedule_date)->toIso8601String(),
                    'stage'         => $i->stage,
                    'status'        => $i->status,
                ])
                ->toArray();
        });
    }

    /**
     * Invalidate all cached dashboard data for a company.
     * Call after events that change KPIs (new submission, interview booked, etc.).
     */
    public function invalidateCache(int $companyId): void
    {
        Cache::forget("ats:v2:dashboard:{$companyId}:kpis");
        Cache::forget("ats:v2:dashboard:{$companyId}:leaderboard");
        // Interviews are user-scoped; purge the pattern via tags if Dragonfly supports it,
        // or let them expire naturally (60-second TTL is acceptable for interview data).
    }
}
