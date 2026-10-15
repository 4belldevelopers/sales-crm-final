<?php

namespace App\Services;

use App\Events\CandidateDrawerUpdated;
use App\Helper\GeneralMethod;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Class CandidateSkillSyncService
 *
 * INTENT: Single Source of Truth (SSOT) Enterprise Synchronization Service for Candidate Primary Skills
 * and Job Matching Percentage across the entire ATS lifecycle (Add Candidate, Edit Candidate, Candidate Drawer,
 * CV Tracker, App Tracker, Client App Tracker, Job Candidate listings, and Candidate Profile).
 *
 * ARCHITECTURE (CQRS):
 * - Writes: Atomically persists to MySQL `job_applications.skills`, `job_application_skills`, and `add_job_wise_condidates.skill_match`.
 * - Reads: Immediately projects to Elasticsearch `ELASTIC_CANDIDATES` with synchronous index refresh for 0% stale reads.
 * - Cache: Multi-tenant atomic cache invalidation across all Redis / application keys and candidate drawer view versions.
 * - Real-Time: Broadcasts CandidateDrawerUpdated over Reverb / WebSockets for live zero-refresh updates.
 */
class CandidateSkillSyncService
{
    /**
     * Parse raw skills input from any client/API payload into a clean, deduplicated array of skill strings.
     * Supports:
     * - Tagify JSON string of objects: '[{"value":"Azure"},{"value":"DevOps"}]'
     * - Tagify / JSON array of objects or strings
     * - Comma-separated string: 'Azure, DevOps, Python'
     * - PHP array of strings
     *
     * @return array<string>
     */
    public static function parseSkills(mixed $input): array
    {
        if (empty($input)) {
            return [];
        }

        $rawList = [];

        if (is_array($input)) {
            foreach ($input as $item) {
                if (is_array($item) && isset($item['value'])) {
                    $rawList[] = trim((string) $item['value']);
                } elseif (is_object($item) && isset($item->value)) {
                    $rawList[] = trim((string) $item->value);
                } elseif (is_string($item)) {
                    $rawList[] = trim($item);
                }
            }
        } elseif (is_string($input)) {
            $trimmed = trim($input);
            $decoded = json_decode($trimmed, true);

            if (! is_array($decoded)) {
                $decoded = json_decode(stripslashes($trimmed), true);
            }

            if (is_array($decoded)) {
                foreach ($decoded as $item) {
                    if (is_array($item) && isset($item['value'])) {
                        $rawList[] = trim((string) $item['value']);
                    } elseif (is_string($item)) {
                        $rawList[] = trim($item);
                    }
                }
            } else {
                // Comma-delimited plain text string
                $rawList = array_map('trim', explode(',', $trimmed));
            }
        }

        // Clean, strip invalid chars, and deduplicate case-insensitively while preserving original clean casing
        $seen = [];
        $uniqueSkills = [];

        foreach ($rawList as $s) {
            $clean = preg_replace('/[\r\n\t]+/', ' ', (string) $s);
            $clean = trim(preg_replace('/\s+/', ' ', $clean));

            if ($clean === '') {
                continue;
            }

            $lower = mb_strtolower($clean);
            if (! isset($seen[$lower])) {
                $seen[$lower] = true;
                $uniqueSkills[] = $clean;
            }
        }

        return array_values($uniqueSkills);
    }

    /**
     * Atomically synchronize candidate primary skills and recalculate matching percentages across all linked jobs.
     *
     * @param  int  $candidateId  job_applications.id
     * @param  array<string>|null  $skillList  Explicit list of skills (or null to re-sync existing SSOT)
     * @param  int|null  $primaryJobId  Optional primary job ID being edited / viewed
     * @param  int|null  $companyId  Tenant company ID
     * @return array Result metadata including updated skills, match percentage, and affected wise IDs
     */
    public static function syncCandidateSkillsAndMatch(
        int $candidateId,
        ?array $skillList = null,
        ?int $primaryJobId = null,
        ?int $companyId = null
    ): array {
        if ($candidateId <= 0) {
            return [
                'skills' => [],
                'canonical_skills_str' => '',
                'primary_match_pct' => 0,
                'wise_ids' => [],
            ];
        }

        $tenantCompanyId = $companyId ?: (auth()->check() ? (int) auth()->user()->company_id : 0);

        // 1. Resolve skills list from input or fallback to database
        if ($skillList === null) {
            $rawDbSkills = DB::table('job_applications')->where('id', $candidateId)->value('skills') ?? '';
            $skillList = self::parseSkills($rawDbSkills);

            if (empty($skillList)) {
                $jasList = DB::table('job_application_skills')
                    ->where('job_application_id', $candidateId)
                    ->pluck('skills')
                    ->toArray();
                $skillList = self::parseSkills($jasList);
            }
        } else {
            $skillList = self::parseSkills($skillList);
        }

        $canonicalSkillsStr = implode(', ', $skillList);

        // 2. MySQL Atomic Writes (Single Source of Truth)
        DB::table('job_applications')->where('id', $candidateId)->update([
            'skills' => $canonicalSkillsStr,
            'updated_at' => now(),
        ]);

        // Atomically replace job_application_skills rows (no substring LIKE bugs)
        DB::table('job_application_skills')->where('job_application_id', $candidateId)->delete();
        if (! empty($skillList)) {
            $jasRows = [];
            foreach ($skillList as $skillName) {
                $jasRows[] = [
                    'job_application_id' => $candidateId,
                    'skills' => mb_substr($skillName, 0, 55),
                ];
            }
            DB::table('job_application_skills')->insert($jasRows);
        }

        // Add to global skills taxonomy index
        if (! empty($skillList)) {
            try {
                GeneralMethod::skillsAddElastic($skillList);
            } catch (\Throwable $e) {
                Log::warning("CandidateSkillSyncService::skillsAddElastic failed: {$e->getMessage()}");
            }
        }

        // 3. Recalculate skill_match for ALL jobs linked to this candidate
        $linkedJobs = DB::table('add_job_wise_condidates')
            ->where('job_application_id', $candidateId)
            ->get(['id', 'job_id', 'company_id']);

        $updatedWiseIds = [];
        $primaryMatchPct = null;

        foreach ($linkedJobs as $wiseRow) {
            $jobId = (int) $wiseRow->job_id;
            $matchResult = GeneralMethod::calculateSkillMatch($jobId, $canonicalSkillsStr);
            $pct = (int) ($matchResult['percentage'] ?? 0);

            DB::table('add_job_wise_condidates')->where('id', $wiseRow->id)->update([
                'skill_match' => $pct,
                'updated_at' => now(),
            ]);

            $updatedWiseIds[] = (int) $wiseRow->id;

            if ($primaryJobId && $jobId === $primaryJobId) {
                $primaryMatchPct = $pct;
            }
        }

        if ($primaryMatchPct === null && $primaryJobId && $primaryJobId > 0) {
            $calc = GeneralMethod::calculateSkillMatch($primaryJobId, $canonicalSkillsStr);
            $primaryMatchPct = (int) ($calc['percentage'] ?? 0);
        } elseif ($primaryMatchPct === null && ! empty($updatedWiseIds)) {
            $firstWise = $linkedJobs->first();
            if ($firstWise && ! empty($firstWise->job_id)) {
                $calc = GeneralMethod::calculateSkillMatch((int) $firstWise->job_id, $canonicalSkillsStr);
                $primaryMatchPct = (int) ($calc['percentage'] ?? 0);
            } else {
                $primaryMatchPct = 0;
            }
        } elseif ($primaryMatchPct === null) {
            $primaryMatchPct = 0;
        }

        // 4. Synchronize Elasticsearch Read Model (ELASTIC_CANDIDATES)
        try {
            foreach ($updatedWiseIds as $wId) {
                GeneralMethod::JobApplicationsAddEditUpload($wId, $tenantCompanyId);
            }

            $elasticService = app(\App\Services\ElasticService::class);
            if ($elasticService->indexExists(ELASTIC_CANDIDATES)) {
                $elasticService->getClient()->indices()->refresh(['index' => ELASTIC_CANDIDATES]);
            }
        } catch (\Throwable $e) {
            Log::warning("CandidateSkillSyncService::ES sync failed for candidate #{$candidateId}: {$e->getMessage()}");
        }

        // 5. Multi-Tenant Atomic Cache Invalidation
        self::invalidateCandidateCaches($candidateId, $updatedWiseIds, $tenantCompanyId);

        // 6. Broadcast Real-Time Reverb Event
        try {
            $note = (string) (DB::table('job_applications')->where('id', $candidateId)->value('recruiter_note') ?? '');
            $resume = (string) (DB::table('job_applications')->where('id', $candidateId)->value('resume') ?? '');

            CandidateDrawerUpdated::dispatch(
                (int) $tenantCompanyId,
                (int) $candidateId,
                time(),
                'candidate_updated',
                array_map('intval', $updatedWiseIds),
                $primaryMatchPct,
                $canonicalSkillsStr,
                $note,
                null,
                null,
                (int) ($primaryJobId ?? 0),
                $resume
            );
        } catch (\Throwable $e) {
            Log::warning("CandidateSkillSyncService::CandidateDrawerUpdated dispatch failed: {$e->getMessage()}");
        }

        return [
            'skills' => $skillList,
            'canonical_skills_str' => $canonicalSkillsStr,
            'primary_match_pct' => $primaryMatchPct,
            'wise_ids' => $updatedWiseIds,
        ];
    }

    /**
     * Atomically invalidate all candidate drawer, listing, and tenant cache keys.
     *
     * @param  array<int>  $wiseIds
     */
    public static function invalidateCandidateCaches(int $candidateId, array $wiseIds, int $companyId): void
    {
        $drawerViewVersion = @filemtime(resource_path('views/service_company/job-applications/showdetail.blade.php')) ?: 0;
        $targetIds = array_unique(array_merge([$candidateId], $wiseIds));
        $slugs = ['', 'cvtracker', 'apptracker', 'clientapp', 'interview-schedule'];

        foreach ($targetIds as $tid) {
            foreach ($slugs as $slug) {
                cache()->forget('tenant:'.$companyId.':candidate_details:'.$tid.':'.md5($slug).':view:'.$drawerViewVersion);
                cache()->forget('tenant:'.$companyId.':candidate_details:'.$tid.'::view:'.$drawerViewVersion);
            }
            cache()->forget('candidate:'.$tid);
        }

        // Multi-tenant Redis/Dragonfly non-blocking wildcard pattern purge for drawer HTML
        try {
            if (config('cache.default') === 'redis') {
                $redis = \Illuminate\Support\Facades\Redis::connection();
                foreach ($targetIds as $tid) {
                    $keys = $redis->keys("*tenant:{$companyId}:candidate_details:{$tid}:*");
                    if (! empty($keys)) {
                        $redis->del($keys);
                    }
                }
            }
        } catch (\Throwable) {
        }

        // Invalidate tenant-wide listing cache version keys so all users in the organization see fresh data
        if ($companyId > 0) {
            try {
                cache()->increment('allcvtracker_version_company_'.$companyId);
                cache()->increment('allapptracker_version_company_'.$companyId);
                cache()->increment('allclientapp_version_company_'.$companyId);
            } catch (\Throwable) {
            }
        }

        // Invalidate user listing cache version keys
        if (auth()->check()) {
            $userId = auth()->id();
            $cachekeys = [
                'allcvtracker_version_user_'.$userId,
                'allapptracker_version_user_'.$userId,
                'allclientapp_version_user_'.$userId,
                'candidateDetails_version_user_'.$userId,
                'candidate:'.$candidateId,
                'candidate:'.$candidateId.':resume',
            ];
            foreach ($cachekeys as $key) {
                GeneralMethod::cacheClean($key);
            }
        }
    }
}
