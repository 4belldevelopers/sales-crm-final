<?php

namespace App\Jobs;

use App\Helper\GeneralMethod;
use App\Models\CandidateOutboxEvent;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * Intent: Asynchronous Outbox Worker with two-step FOR UPDATE SKIP LOCKED,
 * lease token fencing, exponential backoff, and idempotent Elasticsearch indexing.
 */
class ProcessOutboxEventJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 5;
    public int $backoff = 30;
    public int $timeout = 120;

    public function __construct(
        private readonly ?int $specificEventId = null,
        private readonly ?string $workerId = null,
    ) {
        $this->onQueue('candidate-index');
    }

    public function handle(): void
    {
        $worker = $this->workerId ?: ('worker_' . getmypid() . '_' . Str::random(8));

        if ($this->specificEventId !== null) {
            $this->processSingleEventById($this->specificEventId, $worker);
            return;
        }

        // Two-step SKIP LOCKED claim protocol for batch processing
        $claimedEvents = $this->claimBatch($worker, 50);
        foreach ($claimedEvents as $eventInfo) {
            $this->executeEvent($eventInfo['id'], $worker, $eventInfo['lock_token']);
        }
    }

    /**
     * Step 1: Claim batch inside short transaction using FOR UPDATE SKIP LOCKED.
     *
     * @return array<int, array{id: int, lock_token: string}>
     */
    private function claimBatch(string $worker, int $limit = 50): array
    {
        $claimed = [];
        $lockToken = (string) Str::uuid();

        DB::beginTransaction();
        try {
            $events = DB::table('candidate_outbox_events')
                ->where('status', 'pending')
                ->where('available_at', '<=', now())
                ->where(function ($query) {
                    $query->whereNull('locked_at')
                        ->orWhere('locked_at', '<', now()->subMinutes(5));
                })
                ->orderBy('id', 'asc')
                ->limit($limit)
                ->lockForUpdate()
                ->pluck('id')
                ->toArray();

            if (! empty($events)) {
                DB::table('candidate_outbox_events')
                    ->whereIn('id', $events)
                    ->update([
                        'status'     => 'processing',
                        'locked_at'  => now(),
                        'locked_by'  => $worker,
                        'lock_token' => $lockToken,
                        'updated_at' => now(),
                    ]);

                foreach ($events as $id) {
                    $claimed[] = ['id' => (int) $id, 'lock_token' => $lockToken];
                }
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('ProcessOutboxEventJob: claimBatch failed', ['error' => $e->getMessage()]);
        }

        return $claimed;
    }

    /**
     * Process a single event directly by ID (with optimistic lease token locking).
     */
    private function processSingleEventById(int $eventId, string $worker): void
    {
        $lockToken = (string) Str::uuid();

        $claimed = DB::table('candidate_outbox_events')
            ->where('id', $eventId)
            ->where(function ($query) {
                $query->where('status', 'pending')
                    ->orWhere(function ($sub) {
                        $sub->where('status', 'processing')
                            ->where('locked_at', '<', now()->subMinutes(5));
                    });
            })
            ->update([
                'status'     => 'processing',
                'locked_at'  => now(),
                'locked_by'  => $worker,
                'lock_token' => $lockToken,
                'updated_at' => now(),
            ]);

        if ($claimed > 0) {
            $this->executeEvent($eventId, $worker, $lockToken);
        }
    }

    /**
     * Step 2: Execute domain work and complete event using fencing token check.
     */
    private function executeEvent(int $eventId, string $worker, string $lockToken): void
    {
        $event = DB::table('candidate_outbox_events')->where('id', $eventId)->first();
        if (! $event) {
            return;
        }

        $payload = is_string($event->payload) ? json_decode($event->payload, true) : (array) $event->payload;
        $candidateId = (int) ($payload['candidate_id'] ?? $event->aggregate_id);

        try {
            switch ($event->event_type) {
                case 'candidate.created':
                case 'candidate.updated':
                    // Idempotent Elasticsearch indexing
                    if ($candidateId > 0) {
                        // Check if candidate exists before indexing
                        $wiseCandidateId = DB::table('add_job_wise_condidates')
                            ->where('job_application_id', $candidateId)
                            ->value('id');

                        if ($wiseCandidateId) {
                            GeneralMethod::JobApplicationsAddEditUpload((int) $wiseCandidateId);
                        }

                        // Tenancy-safe cache invalidation
                        $this->invalidateCandidateCaches($candidateId, (int) $event->company_id, $payload);
                    }
                    break;

                case 'resume.removed':
                    if ($candidateId > 0) {
                        $wiseCandidateId = DB::table('add_job_wise_condidates')
                            ->where('job_application_id', $candidateId)
                            ->value('id');

                        if ($wiseCandidateId) {
                            GeneralMethod::JobApplicationsAddEditUpload((int) $wiseCandidateId);
                        }

                        $this->invalidateCandidateCaches($candidateId, (int) $event->company_id, $payload);

                        // Async purge dispatch for removed asset (with retention grace period)
                        $oldResumeId = (int) ($payload['old_resume_id'] ?? 0);
                        if ($oldResumeId > 0) {
                            PurgeRemovedResumeAssetsJob::dispatch($oldResumeId)->onQueue('candidate-index');
                        }
                    }
                    break;

                case 'talent.bench.created':
                case 'talent.bench.updated':
                case 'talent.bench.stage_changed':
                    if ($candidateId > 0) {
                        try {
                            app(\App\Services\ElasticBenchCandidatesIndex::class)->benchCandidateIndexElastic($candidateId);
                            $companyId = (int) ($event->company_id ?: ($payload['company_id'] ?? 0));
                            if ($companyId > 0) {
                                GeneralMethod::addCompanyBenchCandidate($companyId, $candidateId);
                            }
                        } catch (\Throwable $esEx) {
                            Log::warning('ProcessOutboxEventJob: ElasticBenchCandidatesIndex warning', [
                                'candidate_id' => $candidateId,
                                'error' => $esEx->getMessage(),
                            ]);
                        }

                        $companyId = (int) ($event->company_id ?: ($payload['company_id'] ?? 0));
                        if ($companyId > 0) {
                            $this->invalidateBenchCaches($candidateId, $companyId);
                        }
                    }
                    break;

                default:
                    if ($event->aggregate_type === 'bench_candidate' && $candidateId > 0) {
                        try {
                            app(\App\Services\ElasticBenchCandidatesIndex::class)->benchCandidateIndexElastic($candidateId);
                            $companyId = (int) ($event->company_id ?: ($payload['company_id'] ?? 0));
                            if ($companyId > 0) {
                                GeneralMethod::addCompanyBenchCandidate($companyId, $candidateId);
                            }
                        } catch (\Throwable $esEx) {
                            Log::warning('ProcessOutboxEventJob: bench_candidate fallback index error', [
                                'candidate_id' => $candidateId,
                                'error' => $esEx->getMessage(),
                            ]);
                        }
                        $companyId = (int) ($event->company_id ?: ($payload['company_id'] ?? 0));
                        if ($companyId > 0) {
                            $this->invalidateBenchCaches($candidateId, $companyId);
                        }
                    } else {
                        Log::info('ProcessOutboxEventJob: Unknown event_type skipped', [
                            'event_id' => $eventId,
                            'event_type' => $event->event_type,
                        ]);
                    }
                    break;
            }

            // Step 2 completion using lease fencing token
            $completed = DB::table('candidate_outbox_events')
                ->where('id', $eventId)
                ->where('locked_by', $worker)
                ->where('lock_token', $lockToken)
                ->update([
                    'status'     => 'completed',
                    'updated_at' => now(),
                ]);

            if ($completed === 0) {
                Log::warning('ProcessOutboxEventJob: Fencing token mismatch on complete', [
                    'event_id' => $eventId,
                    'worker' => $worker,
                ]);
            }
        } catch (\Throwable $e) {
            Log::error('ProcessOutboxEventJob: Execution failed for event', [
                'event_id' => $eventId,
                'error' => $e->getMessage(),
            ]);

            $retryCount = (int) $event->retry_count + 1;
            $newStatus = $retryCount >= 5 ? 'failed' : 'pending';
            $jitterSchedule = [
                1 => 5 + rand(0, 2),
                2 => 15 + rand(0, 5),
                3 => 60 + rand(0, 15),
                4 => 300 + rand(0, 30),
                5 => 900 + rand(0, 60),
            ];
            $backoffSeconds = $jitterSchedule[$retryCount] ?? 900;

            DB::table('candidate_outbox_events')
                ->where('id', $eventId)
                ->where('locked_by', $worker)
                ->where('lock_token', $lockToken)
                ->update([
                    'status'       => $newStatus,
                    'retry_count'  => $retryCount,
                    'available_at' => now()->addSeconds($backoffSeconds),
                    'locked_at'    => null,
                    'locked_by'    => null,
                    'lock_token'   => null,
                    'last_error'   => substr($e->getMessage(), 0, 1000),
                    'updated_at'   => now(),
                ]);
        }
    }

    private function invalidateCandidateCaches(int $candidateId, int $companyId, array $payload): void
    {
        try {
            GeneralMethod::cacheClean("candidate:{$candidateId}");
            GeneralMethod::cacheClean("candidate:{$candidateId}:resume");

            $submittedBy = (int) ($payload['submitted_by'] ?? 0);
            if ($submittedBy > 0) {
                GeneralMethod::cacheClean("allcvtracker_version_user_{$submittedBy}");
                GeneralMethod::cacheClean("allapptracker_version_user_{$submittedBy}");
            }
        } catch (\Throwable $e) {
            Log::warning('ProcessOutboxEventJob: Cache invalidation warning', ['error' => $e->getMessage()]);
        }
    }

    private function invalidateBenchCaches(int $candidateId, int $companyId): void
    {
        try {
            \Illuminate\Support\Facades\Redis::sadd('bench:dirty_tenants', $companyId);
            \Illuminate\Support\Facades\Redis::del("bench:hero:total:{$companyId}");
            \Illuminate\Support\Facades\Redis::del("bench:hero:bench:{$companyId}");
            \Illuminate\Support\Facades\Redis::del("bench:hero:pool:{$companyId}");
            \Illuminate\Support\Facades\Redis::del("bench:hero:hold:{$companyId}");
            \Illuminate\Support\Facades\Redis::del("ats:allbenchcandidates:{$companyId}");
            GeneralMethod::cacheClean("candidate:{$candidateId}");
            GeneralMethod::cacheClean("allbenchcandidates_version_company_{$companyId}");
        } catch (\Throwable $e) {
            Log::warning('ProcessOutboxEventJob: Bench cache invalidation warning', ['error' => $e->getMessage()]);
        }
    }
}
