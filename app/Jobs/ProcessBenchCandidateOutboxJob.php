<?php

namespace App\Jobs;

use App\Events\SuperAdminBenchApprovalDeltaEvent;
use App\Helper\GeneralMethod;
use App\Services\ElasticService;
use App\Services\MailService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Class ProcessBenchCandidateOutboxJob
 *
 * INTENT:
 * Decoupled Transactional Outbox Worker for Super Admin Bench Candidate Approval mutations.
 * Claims pending events from sys_outbox_events (aggregate_type 'BenchCandidate'),
 * synchronizes Elasticsearch index ELASTIC_CANDIDATES_BENCH with MySQL state changes,
 * invalidates multi-tenant analytics and listing caches in Redis/Dragonfly,
 * dispatches candidate approval notification emails asynchronously,
 * and broadcasts real-time Reverb WebSocket events under 10K concurrency.
 *
 * TENANCY NOTE:
 * Operates across tenant boundaries under Super Admin moderation (tenant_id = 0 in outbox).
 * Candidate documents in Elasticsearch are scoped by their individual candidate IDs and company IDs.
 *
 * Author: Antigravity AI — Phase 1 Outbox & Idempotency Architecture
 * Date: October 26, 2026
 * Complies with docs/CODE_DOCUMENTATION_STANDARD.md.
 */
class ProcessBenchCandidateOutboxJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 5;

    public int $backoff = 10;

    public int $timeout = 60;

    /**
     * Create a new job instance.
     *
     * @param  int|null  $specificEventId  Optional outbox event ID to process immediately
     */
    public function __construct(
        public readonly ?int $specificEventId = null
    ) {
        $this->onQueue('candidate-index');
    }

    /**
     * Execute the job.
     */
    public function handle(ElasticService $elasticService): void
    {
        if ($this->specificEventId !== null) {
            $this->processEvent($this->specificEventId, $elasticService);

            return;
        }

        // Batch processing when triggered periodically by worker or scheduler
        $events = DB::table('sys_outbox_events')
            ->where('aggregate_type', 'BenchCandidate')
            ->where('status', 'PENDING')
            ->where('available_at', '<=', now())
            ->orderBy('id', 'asc')
            ->limit(25)
            ->pluck('id')
            ->toArray();

        foreach ($events as $eventId) {
            $this->processEvent((int) $eventId, $elasticService);
        }
    }

    /**
     * Process an individual claimed outbox event.
     */
    public function processEvent(int $eventId, ElasticService $elasticService): void
    {
        DB::beginTransaction();
        try {
            $event = DB::table('sys_outbox_events')
                ->where('id', $eventId)
                ->where('aggregate_type', 'BenchCandidate')
                ->whereIn('status', ['PENDING', 'PROCESSING'])
                ->lockForUpdate()
                ->first();

            if (! $event) {
                DB::rollBack();

                return;
            }

            // Mark status as PROCESSING
            DB::table('sys_outbox_events')
                ->where('id', $eventId)
                ->update([
                    'status' => 'PROCESSING',
                    'locked_at' => now(),
                    'attempts' => DB::raw('attempts + 1'),
                ]);

            DB::commit();

            $payload = is_string($event->payload) ? json_decode($event->payload, true) : (array) $event->payload;
            $payload = $payload ?? [];

            $eventType = (string) ($event->event_type ?? 'CANDIDATE_SUPERADMIN_APPROVED');
            $candidateIds = isset($payload['candidate_ids']) && is_array($payload['candidate_ids'])
                ? array_map('intval', $payload['candidate_ids'])
                : [];

            if (! empty($event->aggregate_id) && empty($candidateIds)) {
                $candidateIds = [(int) $event->aggregate_id];
            }

            $superadminStatus = (int) ($payload['superadmin_status'] ?? 1);
            $adminName = (string) ($payload['admin_name'] ?? 'Super Admin');

            // 1. Synchronize Elasticsearch (ELASTIC_CANDIDATES_BENCH)
            $this->syncElasticsearch($elasticService, $candidateIds, $superadminStatus, $eventType);

            // 2. Invalidate Redis / Dragonfly Caches
            $this->invalidateCaches($candidateIds);

            // 3. Process Asynchronous Notification Emails (if approving candidates)
            if ($eventType === 'CANDIDATE_SUPERADMIN_APPROVED' && ! empty($candidateIds)) {
                $this->dispatchApprovalEmails($candidateIds);
            }

            // 4. Fetch fresh authoritative platform counts
            $counts = $this->getFreshCounts($elasticService);

            // 5. Broadcast real-time Reverb WebSocket delta event
            try {
                $statusLabel = $superadminStatus === 1 ? 'Approved' : ($superadminStatus === 2 ? 'Rejected' : 'Pending');
                broadcast(new SuperAdminBenchApprovalDeltaEvent(
                    candidateIds: $candidateIds,
                    superadminStatus: $superadminStatus,
                    statusLabel: $statusLabel,
                    eventType: $eventType,
                    counts: $counts,
                    updatedBy: $adminName,
                    entityVersion: (int) ($event->aggregate_version ?? 1),
                    extra: [
                        'idempotency_key' => $event->idempotency_key,
                        'event_id' => $event->id,
                    ]
                ));
            } catch (\Throwable $broadcastEx) {
                Log::warning('[BENCH_OUTBOX] Reverb broadcast warning: '.$broadcastEx->getMessage(), [
                    'event_id' => $eventId,
                ]);
            }

            // 6. Mark Outbox Record as COMPLETED
            DB::table('sys_outbox_events')
                ->where('id', $eventId)
                ->update([
                    'status' => 'COMPLETED',
                    'processed_at' => now(),
                    'last_error' => null,
                ]);

        } catch (\Throwable $e) {
            DB::rollBack();

            Log::error('[BENCH_OUTBOX] Worker failed processing event #'.$eventId.': '.$e->getMessage(), [
                'event_id' => $eventId,
                'trace' => $e->getTraceAsString(),
            ]);

            $attempts = (int) (DB::table('sys_outbox_events')->where('id', $eventId)->value('attempts') ?? 1);
            $nextStatus = ($attempts >= $this->tries) ? 'DEAD_LETTER' : 'FAILED';
            $backoffSecs = min(300, 5 * pow(2, min($attempts, 6)));

            DB::table('sys_outbox_events')
                ->where('id', $eventId)
                ->update([
                    'status' => $nextStatus,
                    'last_error' => substr($e->getMessage(), 0, 1000),
                    'available_at' => now()->addSeconds($backoffSecs),
                ]);
        }
    }

    /**
     * Synchronize candidate status updates to Elasticsearch cluster.
     *
     * @param  array<int>  $candidateIds
     */
    protected function syncElasticsearch(
        ElasticService $elasticService,
        array $candidateIds,
        int $superadminStatus,
        string $eventType
    ): void {
        if (empty($candidateIds)) {
            return;
        }

        $indexName = defined('ELASTIC_CANDIDATES_BENCH') ? ELASTIC_CANDIDATES_BENCH : config('constants.ELASTIC_CANDIDATES_BENCH', 'elastic_candidates_bench');

        if (! $elasticService->indexExists($indexName)) {
            Log::warning("[BENCH_OUTBOX] Elasticsearch index '{$indexName}' does not exist; skipping direct sync.");

            return;
        }

        $esClient = $elasticService->getClient();

        foreach ($candidateIds as $candId) {
            try {
                if ($esClient->exists(['index' => $indexName, 'id' => (int) $candId])) {
                    $docData = [
                        'superadminstatus' => (int) $superadminStatus,
                        'updated_at' => now()->format('Y-m-d H:i:s'),
                    ];

                    // If approved, candidate is live on marketplace (status = 2)
                    if ($superadminStatus === 1) {
                        $docData['status'] = 2;
                    }

                    $esClient->update([
                        'index' => $indexName,
                        'id' => (int) $candId,
                        'body' => [
                            'doc' => $docData,
                        ],
                    ]);
                }
            } catch (\Throwable $ex) {
                Log::warning("[BENCH_OUTBOX] Failed updating candidate #{$candId} in Elasticsearch: ".$ex->getMessage());
            }
        }
    }

    /**
     * Invalidate tenant-scoped and platform-scoped cache keys in Redis/Dragonfly.
     *
     * @param  array<int>  $candidateIds
     */
    protected function invalidateCaches(array $candidateIds): void
    {
        Cache::forget('superadmin:bench:counts:v1');

        if (! empty($candidateIds)) {
            $companyIds = DB::table('job_applications')
                ->whereIn('id', $candidateIds)
                ->pluck('company_id')
                ->unique()
                ->toArray();

            foreach ($companyIds as $companyId) {
                Cache::increment('allbenchcandidates_version_company_'.$companyId);
                Cache::forget('team:workload:v1:'.$companyId);
            }
        }
    }

    /**
     * Dispatch candidate approval notification emails asynchronously in background worker.
     * Preserves exact original business logic from SuperAdminCompanyController::candidateApproval.
     *
     * @param  array<int>  $candidateIds
     */
    protected function dispatchApprovalEmails(array $candidateIds): void
    {
        try {
            $mailService = app(MailService::class);
            $basecompany = GeneralMethod::baseCompany();
            $senderName = $basecompany->companyName ?: '';
            $fromAddress = $basecompany->companyEmail ?: '';
            $attachments = [];
            $benchstatus = 'Live on Platform';
            $cc = [];

            $checkMailPermission = GeneralMethod::mailPermission(27);
            if (! $checkMailPermission) {
                return;
            }

            $mailTypeSubject = GeneralMethod::mailTypeSubject(27, 2);
            if (! $mailTypeSubject) {
                return;
            }

            foreach ($candidateIds as $candidateId) {
                $rowdata = DB::table('job_applications')
                    ->join('companies', 'companies.id', '=', 'job_applications.company_id')
                    ->select('job_applications.*', 'companies.company_name')
                    ->where('job_applications.id', $candidateId)
                    ->first();

                if (! $rowdata) {
                    continue;
                }

                $subject = str_replace(
                    ['[{BENCH_CANDIDATE_NAME}]', '[{STATUS}]'],
                    [$rowdata->full_name, $benchstatus],
                    $mailTypeSubject->subject
                );

                $template_description = $mailTypeSubject->body_description;
                $template_description = str_replace(
                    ['[{VENDOR_COMPANY_NAME}]', '[{BENCH_CANDIDATE_NAME}]', '[{BENCH_ID}]'],
                    [$rowdata->company_name, $rowdata->full_name, $rowdata->id],
                    $template_description
                );

                $notificationemail = GeneralMethod::notification($rowdata->company_id, 'Sales');
                $defaultCompany = DB::table('companies')->where('id', $rowdata->company_id)->first();
                $to_Mail_Id = $notificationemail->email ?? $defaultCompany->company_email ?? '';

                if (! empty($to_Mail_Id)) {
                    $datamail = ['message' => $template_description];
                    $mailService->sendEmail(
                        $to_Mail_Id,
                        $subject,
                        'email.common.index',
                        (object) $datamail,
                        $attachments,
                        $fromAddress,
                        $senderName,
                        $cc
                    );
                }
            }
        } catch (\Throwable $mailEx) {
            Log::warning('[BENCH_OUTBOX] Email dispatch warning: '.$mailEx->getMessage());
        }
    }

    /**
     * Compute fresh counts for Super Admin bench status tabs.
     *
     * @return array<string, int>
     */
    protected function getFreshCounts(ElasticService $elasticService): array
    {
        $counts = [
            'pending' => 0,
            'approve' => 0,
            'reject' => 0,
            'place' => 0,
            'pool' => 0,
        ];

        try {
            $indexName = defined('ELASTIC_CANDIDATES_BENCH') ? ELASTIC_CANDIDATES_BENCH : config('constants.ELASTIC_CANDIDATES_BENCH', 'elastic_candidates_bench');

            if ($elasticService->indexExists($indexName)) {
                $client = $elasticService->getClient();

                // Term filters matching controller logic
                $searchPending = $client->count([
                    'index' => $indexName,
                    'body' => ['query' => ['bool' => ['filter' => [
                        ['term' => ['superadminstatus' => 0]],
                        ['term' => ['status' => 1]],
                    ]]]],
                ]);
                $counts['pending'] = $searchPending['count'] ?? 0;

                $searchApprove = $client->count([
                    'index' => $indexName,
                    'body' => ['query' => ['bool' => ['filter' => [
                        ['term' => ['superadminstatus' => 1]],
                        ['term' => ['status' => 2]],
                    ]]]],
                ]);
                $counts['approve'] = $searchApprove['count'] ?? 0;

                $searchReject = $client->count([
                    'index' => $indexName,
                    'body' => ['query' => ['bool' => ['filter' => [
                        ['term' => ['superadminstatus' => 2]],
                        ['term' => ['status' => 2]],
                    ]]]],
                ]);
                $counts['reject'] = $searchReject['count'] ?? 0;
            }
        } catch (\Throwable $countEx) {
            // Fallback counts
            $counts['pending'] = DB::table('job_applications')->where('status', 1)->where('superadminstatus', 0)->count();
            $counts['approve'] = DB::table('job_applications')->where('status', 2)->where('superadminstatus', 1)->count();
            $counts['reject'] = DB::table('job_applications')->where('superadminstatus', 2)->count();
        }

        Cache::put('superadmin:bench:counts:v1', $counts, 300);

        return $counts;
    }
}
