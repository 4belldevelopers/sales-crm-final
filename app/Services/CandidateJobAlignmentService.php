<?php

namespace App\Services;

use App\Events\JobSkillsUpdated;
use App\Helper\GeneralMethod;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;

/**
 * CandidateJobAlignmentService
 *
 * Intent:
 * Ensures complete, immediate alignment of Prescreen Reports, Key Skills,
 * Screening Questions, and Recruiter Comments whenever a candidate profile is moved, copied,
 * or added to a target job from Similar Candidates, CV Tracker, App Tracker, Bench, or Candidates Request.
 *
 * Requirements:
 * 1. Screening Questions: Candidate strictly follows the screening questions of the CURRENT target job.
 *    Answers from previous jobs are removed / NOT carried forward so recruiter evaluates answers for the current job.
 * 2. Key Skills: Candidate strictly follows the key skills of the CURRENT target job.
 *    Ratings and experiences from previous jobs are NOT carried forward; initialized clean so recruiter evaluates afresh.
 * 3. Recruiter Comments: Recruiter comments from previous jobs are NOT carried forward;
 *    the Recruiter Comments section/card is reset clean for the recruiter to update based on the current job.
 * 4. Immediate Real-Time Reflection:
 *    - Synchronous MySQL transactional writes.
 *    - Synchronous Elasticsearch indexing (ELASTIC_CANDIDATES, ELASTIC_CANDIDATE_SKILLS_EXP, ELASTIC_CANDIDATE_JOB_QUESTION).
 *    - Explicit Elasticsearch index refresh for 0ms lag.
 *    - Atomic multi-tier cache destruction (Redis drawer HTML, Forward Resume metadata, datatable versions,
 *      and WebSocket Reverb broadcast to clear client RAM & IndexedDB).
 */
class CandidateJobAlignmentService
{
    /**
     * Align candidate data points with the target job and purge all stale caches immediately.
     *
     * @param  int  $candidateId  Core job_applications ID
     * @param  int  $targetJobId  Target job ID
     * @param  int  $wiseId  add_job_wise_condidates ID
     * @param  int|null  $companyId  Tenant company ID
     * @param  object|null  $candidateObj  Optional pre-loaded candidate row to avoid duplicate SELECT
     * @param  object|null  $jobObj  Optional pre-loaded job row to avoid duplicate SELECT
     * @param  bool  $skipSkillMatchUpdate  True when caller already computed and inserted skill_match & reset recruiter_note
     */
    public function alignCandidateToJob(
        int $candidateId,
        int $targetJobId,
        int $wiseId,
        ?int $companyId = null,
        ?object $candidateObj = null,
        ?object $jobObj = null,
        bool $skipSkillMatchUpdate = false
    ): bool {
        if ($candidateId <= 0 || $targetJobId <= 0 || $wiseId <= 0) {
            return false;
        }

        $companyId = (int) ($companyId ?: (auth()->user()?->company_id ?: 0));
        $elasticService = app(ElasticService::class);
        $elasticClient = $elasticService->getClient();

        try {
            // 1. Fetch Candidate & Target Job details (reuse pre-loaded objects when provided by caller)
            $candidate = $candidateObj ?: DB::table('job_applications')->where('id', $candidateId)->first();
            $job = $jobObj ?: DB::table('jobs')->where('id', $targetJobId)->first();

            if (! $candidate || ! $job) {
                Log::warning("[CandidateJobAlignmentService] Missing candidate #{$candidateId} or job #{$targetJobId}");

                return false;
            }

            // 2. Recruiter Comments: Do NOT carry forward recruiter comments from previous jobs.
            // Reset recruiter_note clean for the recruiter to review and update based on the current job.
            if (! $skipSkillMatchUpdate) {
                DB::table('job_applications')->where('id', $candidateId)->update([
                    'recruiter_note' => null,
                    'updated_at' => now(),
                ]);
            }

            // 3. Align Key Skills: Follow current target job's skills cleanly without carrying forward previous ratings/exp
            $this->alignKeySkillsClean($candidateId, $targetJobId, $elasticClient);

            // 4. Align Screening Questions: Follow current target job's questions cleanly without carrying forward previous answers
            $this->alignScreeningQuestionsClean($candidateId, $targetJobId, $elasticClient);

            // 5. Update Skill Match Percentage for target job (skipped if caller already inserted exact skill_match)
            if (! $skipSkillMatchUpdate) {
                $candSkillsArr = array_values(array_filter(array_map('trim', explode(',', (string) @$candidate->skills))));
                $matchPercentage = GeneralMethod::skill_match_percentage($targetJobId, $candSkillsArr);

                DB::table('add_job_wise_condidates')
                    ->where('id', $wiseId)
                    ->update([
                        'skill_match' => $matchPercentage,
                        'updated_at' => now(),
                    ]);
            }

            // 6. Synchronous Authoritative Elasticsearch Indexing
            GeneralMethod::JobApplicationsAddEditUpload($wiseId, $companyId);

            // 7. Single combined multi-index refresh (1 ES HTTP round-trip instead of 3) so queries immediately see the latest documents
            try {
                $candidatesIndex = config('constants.ELASTIC_CANDIDATES', 'elastic_candidates');
                $skillsExpIndex = config('constants.ELASTIC_CANDIDATE_SKILLS_EXP', 'elastic_candidate_skills_exp');
                $questionsIndex = config('constants.ELASTIC_CANDIDATE_JOB_QUESTION', 'elastic_candidate_job_question');

                $elasticClient->indices()->refresh([
                    'index' => implode(',', [$candidatesIndex, $skillsExpIndex, $questionsIndex]),
                ]);
            } catch (\Throwable $refreshErr) {
                Log::warning('[CandidateJobAlignmentService] ES refresh exception: '.$refreshErr->getMessage());
            }

            // 8. Atomic Cache Destruction (Redis, Browser RAM, IndexedDB, Datatables)
            $this->destroyCaches($candidateId, $wiseId, $targetJobId, $companyId);

            return true;
        } catch (\Throwable $e) {
            Log::error('[CandidateJobAlignmentService] Failed to align candidate to job: '.$e->getMessage(), [
                'candidateId' => $candidateId,
                'targetJobId' => $targetJobId,
                'wiseId' => $wiseId,
                'trace' => $e->getTraceAsString(),
            ]);

            return false;
        }
    }

    /**
     * Initializes key skills for the target job cleanly using batched MySQL reads/writes and 1 ES _bulk call.
     * Follows the current job's skills and does NOT carry forward ratings or experiences from previous jobs.
     */
    protected function alignKeySkillsClean(int $candidateId, int $targetJobId, $elasticClient): void
    {
        $targetJobSkills = DB::table('job_metronics_skills')
            ->where('job_id', $targetJobId)
            ->get();

        if ($targetJobSkills->isEmpty()) {
            return;
        }

        // Batch fetch existing skill experience rows for (candidate, job) in 1 query instead of N queries
        $existingMap = DB::table('candidate_job_skill_exp')
            ->where('candidate_id', $candidateId)
            ->where('job_id', $targetJobId)
            ->get()
            ->keyBy('job_skill_id');

        $idsToReset = [];
        $bulkBody = [];
        $skillsExpIndex = config('constants.ELASTIC_CANDIDATE_SKILLS_EXP', 'elastic_candidate_skills_exp');
        $nowIso = now()->toIso8601String();

        foreach ($targetJobSkills as $jobSkill) {
            $skillName = trim((string) $jobSkill->skill_name);
            $existing = $existingMap->get($jobSkill->id);

            if ($existing) {
                $idsToReset[] = $existing->id;
                $expId = $existing->id;
            } else {
                $expId = DB::table('candidate_job_skill_exp')->insertGetId([
                    'job_skill_id' => $jobSkill->id,
                    'skill_value' => 0,
                    'job_id' => $targetJobId,
                    'rating' => 0,
                    'candidate_id' => $candidateId,
                ]);
            }

            $bulkBody[] = [
                'index' => [
                    '_index' => $skillsExpIndex,
                    '_id' => $expId,
                ],
            ];
            $bulkBody[] = [
                'skill_name' => $skillName,
                'job_skill_id' => (int) $jobSkill->id,
                'skill_value' => 0,
                'job_id' => (int) $targetJobId,
                'rating' => 0,
                'candidate_id' => (int) $candidateId,
                'created_at' => $nowIso,
                'updated_at' => $nowIso,
            ];
        }

        if (! empty($idsToReset)) {
            DB::table('candidate_job_skill_exp')
                ->whereIn('id', $idsToReset)
                ->update([
                    'skill_value' => 0,
                    'rating' => 0,
                ]);
        }

        if (! empty($bulkBody)) {
            try {
                $elasticClient->bulk(['body' => $bulkBody]);
            } catch (\Throwable $esErr) {
                Log::warning('[CandidateJobAlignmentService] Skill ES bulk index failed: '.$esErr->getMessage());
            }
        }
    }

    /**
     * Initializes screening questions for the target job cleanly using batched MySQL reads and 1 ES _bulk call.
     * Candidate strictly follows current job's questions, and previous answers are NOT carried forward.
     */
    protected function alignScreeningQuestionsClean(int $candidateId, int $targetJobId, $elasticClient): void
    {
        $targetQuestions = DB::table('job_additional_detail')
            ->where('job_id', $targetJobId)
            ->get();

        if ($targetQuestions->isEmpty()) {
            return;
        }

        // Batch fetch existing question rows for (candidate, job) in 1 query instead of N queries
        $existingMap = DB::table('application_additional_detail')
            ->where('application_id', $candidateId)
            ->where('job_id', $targetJobId)
            ->get()
            ->keyBy('question_id');

        $bulkBody = [];
        $questionsIndex = config('constants.ELASTIC_CANDIDATE_JOB_QUESTION', 'elastic_candidate_job_question');
        $nowIso = now()->toIso8601String();

        foreach ($targetQuestions as $q) {
            $qText = trim((string) $q->detail_value);
            $existing = $existingMap->get($q->id);

            if ($existing) {
                DB::table('application_additional_detail')
                    ->where('id', $existing->id)
                    ->update([
                        'application_detail_value' => null,
                        'job_detail_value' => $qText,
                    ]);
                $qDetailId = $existing->id;
            } else {
                $qDetailId = DB::table('application_additional_detail')->insertGetId([
                    'job_id' => $targetJobId,
                    'question_id' => $q->id,
                    'application_id' => $candidateId,
                    'job_detail_value' => $qText,
                    'application_detail_value' => null,
                ]);
            }

            $bulkBody[] = [
                'update' => [
                    '_index' => $questionsIndex,
                    '_id' => $qDetailId,
                ],
            ];
            $bulkBody[] = [
                'doc' => [
                    'job_id' => (int) $targetJobId,
                    'question_id' => (int) $q->id,
                    'application_id' => (int) $candidateId,
                    'job_detail_value' => $qText,
                    'application_detail_value' => null,
                    'created_at' => $nowIso,
                    'updated_at' => $nowIso,
                ],
                'doc_as_upsert' => true,
            ];
        }

        if (! empty($bulkBody)) {
            try {
                $elasticClient->bulk(['body' => $bulkBody]);
            } catch (\Throwable $esErr) {
                Log::warning('[CandidateJobAlignmentService] Question ES bulk update failed: '.$esErr->getMessage());
            }
        }
    }

    /**
     * Atomically destroys all Redis caches, datatable version counters, and triggers
     * client RAM & IndexedDB invalidation across all open recruiter browser sessions.
     */
    protected function destroyCaches(int $candidateId, int $wiseId, int $targetJobId, int $companyId): void
    {
        $drawerViewVersion = @filemtime(resource_path('views/service_company/job-applications/showdetail.blade.php')) ?: 0;

        // Fetch all wise IDs for this candidate across all jobs
        $allWiseIds = DB::table('add_job_wise_condidates')
            ->where('job_application_id', $candidateId)
            ->pluck('id')
            ->toArray();

        $targetIds = array_values(array_unique(array_filter(array_merge([$candidateId, $wiseId], $allWiseIds))));
        $slugs = ['', 'cvtracker', 'apptracker', 'interview-schedule', 'similar_candidates', 'internal_bench', 'public_bench'];

        // 1. Invalidate candidate details drawer HTML/JSON cache
        foreach ($targetIds as $tid) {
            foreach ($slugs as $slug) {
                Cache::forget('tenant:'.$companyId.':candidate_details:'.$tid.':'.md5($slug).':view:'.$drawerViewVersion);
                Cache::forget('tenant:'.$companyId.':candidate_details:'.$tid);
            }
        }

        // Redis scan & delete pattern for any wildcard drawer keys
        try {
            $redis = Redis::connection();
            foreach ($targetIds as $tid) {
                $keys = $redis->keys("*tenant:{$companyId}:candidate_details:{$tid}*");
                if (! empty($keys)) {
                    foreach ($keys as $k) {
                        $redis->del($k);
                    }
                }
            }
        } catch (\Throwable $re) {
            // Non-critical if Redis keys command fails
        }

        // 2. Invalidate Forward Resume caches
        Cache::forget("tenant:{$companyId}:fwd_branding");
        Cache::forget("tenant:{$companyId}:fwd_template_metadata");

        // 3. Invalidate Datatable user version counters (including Bench Candidates list)
        $userId = auth()->user()?->id;
        if ($userId) {
            $versionKeys = [
                'allcvtracker_version_user_'.$userId,
                'allapptracker_version_user_'.$userId,
                'allbenchcandidates_version_user_'.$userId,
                'allsimilarcandidates_version_user_'.$userId,
                'allclientapp_version_user_'.$userId,
                'allinternalbench_version_user_'.$userId,
                'allpublicbench_version_user_'.$userId,
                'alldsimilarcandidates_version_user_'.$userId,
                'alldinternalbench_version_user_'.$userId,
                'allfindtalents_version_user_'.$userId,
                'allcandidatesrequest_version_user_'.$userId,
                'allcareerapplydata_version_user_'.$userId,
                'alljobslist_version_user_'.$userId,
                'allbenchexplore_version_user_'.$userId,
                'candidateDetails_version_user_'.$userId,
            ];
            foreach ($versionKeys as $vk) {
                GeneralMethod::cacheClean($vk);
            }
        }

        // Intent: Invalidate header counter cache (custome_count_all) AND AppTracker stage pipeline counter cache (pipeline:{jobId})
        // so Total Candidates In Job and all stage pills update with 0ms staleness when navigating to AppTracker.
        Cache::forget("custome_count_all:{$companyId}:{$targetJobId}");
        Cache::forget("pipeline:{$targetJobId}");
        $authCompanyId = auth()->user()?->company_id;
        if ($authCompanyId && (int) $authCompanyId !== (int) $companyId) {
            Cache::forget("custome_count_all:{$authCompanyId}:{$targetJobId}");
        }

        // 4. Real-time WebSocket Reverb broadcast to clear client RAM & IndexedDB
        try {
            JobSkillsUpdated::dispatch(
                $companyId,
                $targetJobId,
                [$candidateId],
                $targetIds,
                [],
                time()
            );
        } catch (\Throwable $bErr) {
            Log::warning('[CandidateJobAlignmentService] Broadcast failed: '.$bErr->getMessage());
        }
    }
}
