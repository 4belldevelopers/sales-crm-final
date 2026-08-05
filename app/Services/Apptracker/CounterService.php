<?php

namespace App\Services\Apptracker;

use App\Events\StageMoved;
use App\Services\ElasticService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * CounterService — Phase B of the Step-16 Apptracker controller slice.
 *
 * Owns all Redis / DB counter orchestration for the pipeline stage badges.
 * Extracted from ServiceCompanyAppTrackerController to reduce that file from
 * ~11,374 lines to ~2,000 lines (see 18_STEP_IMPLEMENTATION_PLAN.md Step 16).
 *
 * Responsibilities:
 *   - Load per-stage counts from Redis flexible cache → pipeline_counters → ES.
 *   - Bust the Redis cache key on stage move or manual invalidation.
 *   - Dispatch StageMoved Reverb event after a confirmed stage change.
 */
class CounterService
{
    public function __construct(private readonly ElasticService $elasticService) {}

    /**
     * Load per-stage pipeline counts for a job.
     * Returns the canonical 8-key counter array used by loadcounter().
     *
     * Cache hierarchy:
     *   1. Redis flexible cache ("pipeline:{$jobId}") — served stale for up to 120s.
     *   2. pipeline_counters DB row — populated every 5 min by counters:reconcile.
     *   3. ES aggregation — cold-start fallback only.
     *
     * @param  int   $jobId
     * @param  int   $companyId
     * @param  string|null  $inputSearch  When set, bypasses all caches (live counts).
     */
    public function load(int $jobId, int $companyId, ?string $inputSearch = null): array
    {
        if (!empty($inputSearch)) {
            return $this->fromES($jobId, $companyId);
        }

        $cacheKey = "pipeline:{$jobId}";

        return Cache::flexible($cacheKey, [30, 120], function () use ($jobId, $companyId) {
            try {
                return Cache::lock("lock:pipeline:{$jobId}", 5)->block(6, function () use ($jobId, $companyId) {
                    // Use mysql_replica (sticky:false) so counter reads never hit the
                    // primary even when a stage move happened earlier in this request.
                    $row = DB::connection('mysql_replica')
                        ->table('pipeline_counters')
                        ->where('job_id', $jobId)
                        ->where('company_id', $companyId)
                        ->whereNotNull('computed_at')
                        ->first();
                    if ($row) {
                        return $this->rowToArray($row);
                    }
                    return $this->fromES($jobId, $companyId);
                });
            } catch (\Throwable $e) {
                Log::warning("CounterService::load lock failed for job {$jobId}: " . $e->getMessage());
                return $this->fromES($jobId, $companyId);
            }
        });
    }

    /**
     * Bust the Redis flexible cache and, optionally, broadcast a StageMoved event.
     *
     * @param  int   $jobId
     * @param  int   $applicationId
     * @param  int   $newStatus
     * @param  int   $companyId
     */
    public function onStageMoved(int $jobId, int $applicationId, int $newStatus, int $companyId): void
    {
        Cache::forget("pipeline:{$jobId}");
        broadcast(new StageMoved($jobId, $applicationId, $newStatus, $companyId))->toOthers();
    }

    /**
     * Returns an empty counter array. Used on complete ES / lock failure.
     */
    public function emptyCounters(): array
    {
        return ['allCandidate' => 0, 'Sourcing' => 0, 'Screening' => 0, 'Interview' => 0,
                'Negotiation' => 0, 'Offered' => 0, 'Joined' => 0, 'Rejected' => 0];
    }

    /* ── Private helpers ──────────────────────────────────────────────── */

    private function rowToArray(object $row): array
    {
        return [
            'allCandidate' => (int) $row->all_candidate,
            'Sourcing'     => (int) $row->sourcing,
            'Screening'    => (int) $row->screening,
            'Interview'    => (int) $row->interview,
            'Negotiation'  => (int) $row->negotiation,
            'Offered'      => (int) $row->offered,
            'Joined'       => (int) $row->joined,
            'Rejected'     => (int) $row->rejected,
        ];
    }

    private function fromES(int $jobId, int $companyId): array
    {
        try {
            $response = $this->elasticService->getClient()->search([
                'index'         => ELASTIC_CANDIDATES,
                'request_cache' => true,
                'body'          => [
                    'size'  => 0,
                    'query' => [
                        'bool' => [
                            'should' => [
                                ['bool' => [
                                    'must'     => [
                                        ['term' => ['jobId'      => $jobId]],
                                        ['term' => ['company_id' => $companyId]],
                                    ],
                                    'must_not' => [['range' => ['ClientComID' => ['gt' => 0]]]],
                                ]],
                                ['bool' => [
                                    'must'     => [
                                        ['term'  => ['jobId'      => $jobId]],
                                        ['term'  => ['company_id' => $companyId]],
                                        ['range' => ['ClientComID' => ['gt' => 0]]],
                                    ],
                                    'must_not' => [['terms' => ['job_master_status_id' => [8, 9]]]],
                                ]],
                            ],
                            'minimum_should_match' => 1,
                        ],
                    ],
                    'aggs' => [
                        'allCandidate' => ['value_count' => ['field' => 'id']],
                        'Sourcing'     => ['filter' => ['term' => ['job_master_status_id' => 1]]],
                        'Screening'    => ['filter' => ['term' => ['job_master_status_id' => 2]]],
                        'Interview'    => ['filter' => ['term' => ['job_master_status_id' => 3]]],
                        'Negotiation'  => ['filter' => ['term' => ['job_master_status_id' => 4]]],
                        'Offered'      => ['filter' => ['term' => ['job_master_status_id' => 5]]],
                        'Joined'       => ['filter' => ['term' => ['job_master_status_id' => 6]]],
                        'Rejected'     => ['filter' => ['term' => ['job_master_status_id' => 7]]],
                    ],
                ],
            ]);
            return [
                'allCandidate' => (int)($response['aggregations']['allCandidate']['value']     ?? 0),
                'Sourcing'     => (int)($response['aggregations']['Sourcing']['doc_count']     ?? 0),
                'Screening'    => (int)($response['aggregations']['Screening']['doc_count']    ?? 0),
                'Interview'    => (int)($response['aggregations']['Interview']['doc_count']    ?? 0),
                'Negotiation'  => (int)($response['aggregations']['Negotiation']['doc_count'] ?? 0),
                'Offered'      => (int)($response['aggregations']['Offered']['doc_count']     ?? 0),
                'Joined'       => (int)($response['aggregations']['Joined']['doc_count']      ?? 0),
                'Rejected'     => (int)($response['aggregations']['Rejected']['doc_count']    ?? 0),
            ];
        } catch (\Throwable) {
            return $this->emptyCounters();
        }
    }
}
