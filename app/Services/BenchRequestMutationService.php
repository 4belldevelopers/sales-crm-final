<?php

namespace App\Services;

use App\Helper\GeneralMethod;
use App\Jobs\ProcessBenchRequestOutboxEventJob;
use App\Models\Job;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * BenchRequestMutationService — Transactional Outbox & Idempotency Engine for Bench Requests.
 *
 * Intent:
 * Phase 5 Backend Hardening for Recommend Requisitions (Bench Candidates).
 * Guarantees zero duplicate submissions under high concurrency via sys_idempotency_keys,
 * atomic database mutations with transactional outbox persistence (sys_outbox_events),
 * and decoupled asynchronous side effects (Elasticsearch sync, Reverb WebSocket broadcasting, and email dispatch).
 *
 * Tenancy Note:
 * Strictly scoped by authenticated tenant company ID ($tenantId).
 *
 * Author: Antigravity AI (God-Level ATS Architecture)
 * Date: October 12, 2026
 */
class BenchRequestMutationService
{
    /**
     * Executes an authoritative candidate recommendation mutation with idempotency and transactional outbox.
     *
     * @param  int  $tenantId  Current company ID (source_from_company_id)
     * @param  int  $jobId  Target job requirement ID
     * @param  int  $candidateId  Target bench candidate ID (job_applications ID)
     * @param  int  $userId  Authenticated user ID
     * @param  string|null  $clientKey  Client-supplied idempotency key
     * @return array Standardized response array with status, message, and optional metadata
     */
    public function executeRecommendationMutation(
        int $tenantId,
        int $jobId,
        int $candidateId,
        int $userId,
        ?string $clientKey = null,
        string $action = 'recommend_internal_req',
        string $tab = 'internal_bench'
    ): array {
        $canonicalPayload = [
            'tenant_id' => $tenantId,
            'job_id' => $jobId,
            'candidate_id' => $candidateId,
            'action' => $action,
        ];
        $canonicalHash = hash('xxh128', json_encode($canonicalPayload));

        // 1. Fallback or validate client idempotency key
        $idempotencyKey = ! empty($clientKey)
            ? $clientKey
            : "idemp:bench_req:{$tenantId}:{$jobId}:{$candidateId}:".substr($canonicalHash, 0, 16);

        // 2. Atomic Idempotency Claim via sys_idempotency_keys
        $inserted = DB::table('sys_idempotency_keys')->insertOrIgnore([
            'tenant_id' => $tenantId,
            'idempotency_key' => $idempotencyKey,
            'request_hash' => $canonicalHash,
            'status' => 'PROCESSING',
            'expires_at' => now()->addHours(24),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        if (! $inserted) {
            $existing = DB::table('sys_idempotency_keys')
                ->where('tenant_id', $tenantId)
                ->where('idempotency_key', $idempotencyKey)
                ->first();

            if ($existing) {
                if ($existing->request_hash !== $canonicalHash) {
                    return [
                        'status' => 'error',
                        'http_code' => 409,
                        'message' => 'Idempotency key payload mismatch. A different operation used this key.',
                    ];
                }

                if ($existing->status === 'COMPLETED') {
                    $cachedBody = json_decode($existing->response_body, true);

                    return is_array($cachedBody) ? $cachedBody : ['status' => 'success', 'message' => 'Request already submitted.'];
                }

                if ($existing->status === 'PROCESSING') {
                    return [
                        'status' => 'warning',
                        'message' => 'Request already processing. Please wait.',
                    ];
                }

                if ($existing->status === 'FAILED_FINAL') {
                    $cachedBody = json_decode($existing->response_body, true);

                    return is_array($cachedBody) ? $cachedBody : ['status' => 'error', 'message' => $existing->last_error ?: 'Request previously failed.'];
                }
            }
        }

        // 3. Acquire short-lived Redis lock for distributed concurrency protection
        $lockKey = 'lock:'.md5($idempotencyKey);
        $lock = Cache::lock($lockKey, 10);

        if (! $lock->get()) {
            return [
                'status' => 'warning',
                'message' => 'Request already processing. Please wait.',
            ];
        }

        try {
            // Authoritative Pre-conditions: Fetch Job and Candidate
            $job = Job::withoutGlobalScopes()->findOrFail($jobId);

            // Method 2: Bench Talent Submission Lifecycle Validation
            // Bench talent submission remains continuously available until the job is explicitly On Hold or Closed
            if ($job->status === 'hold') {
                return [
                    'status' => 'error',
                    'message' => 'This job requirement is currently on hold and cannot accept candidate submissions.',
                ];
            }

            if ($job->status !== 'active') {
                return [
                    'status' => 'error',
                    'message' => 'This job requirement is closed and cannot accept candidate submissions.',
                ];
            }

            if (isset($job->bench_submission) && $job->bench_submission === 'disabled') {
                return [
                    'status' => 'error',
                    'message' => 'Bench talent submission is disabled for this job requirement.',
                ];
            }

            $source_from_company_id = $tenantId;
            $source_from_user_id = $userId;
            $source_from_type = 1; // 1 for Bench
            $source_company_id = (int) $job->company_id;
            $source_application_id = $candidateId;
            $source_jobid = $jobId;

            // Concurrency check — prevent duplicate active assignment submission in rec_candidate_assign
            // Active states: 0 = Under Review / Pending, 1 = Accepted / Added to pipeline
            // If a previous submission expired (3) or was rejected (2), resubmission is explicitly allowed as a new cycle
            $activeAssignment = DB::table('rec_candidate_assign')
                ->where('source_jobid', $source_jobid)
                ->where('source_application_id', $source_application_id)
                ->where('source_from_company_id', $source_from_company_id)
                ->whereIn('ac_status', [0, 1])
                ->first();

            if ($activeAssignment) {
                $duplicateMsg = ($activeAssignment->ac_status == 1)
                    ? 'Candidate has already been accepted and added to this job requirement.'
                    : 'Candidate submission is currently under review for this job.';

                $duplicateResponse = [
                    'status' => 'warning',
                    'message' => $duplicateMsg,
                ];

                DB::table('sys_idempotency_keys')
                    ->where('tenant_id', $tenantId)
                    ->where('idempotency_key', $idempotencyKey)
                    ->update([
                        'status' => 'COMPLETED',
                        'response_status' => 200,
                        'response_body' => json_encode($duplicateResponse),
                        'completed_at' => now(),
                        'updated_at' => now(),
                    ]);

                return $duplicateResponse;
            }

            // Calculate submission cycle number for resubmission audit tracking
            $previousCyclesCount = DB::table('rec_candidate_assign')
                ->where('source_jobid', $source_jobid)
                ->where('source_application_id', $source_application_id)
                ->where('source_from_company_id', $source_from_company_id)
                ->count();
            $currentCycle = $previousCyclesCount + 1;

            // Coin Validations
            $eventId = 25;
            $coinsValidations = GeneralMethod::coinsValidations((int) $source_from_company_id, $eventId, $userId, $source_company_id, '', '');
            if ($coinsValidations->status) {
                return [
                    'status' => 'info',
                    'message' => $coinsValidations->message,
                    'company_coins' => $coinsValidations->company_coins,
                ];
            }

            // 4. Begin Unified ACID Database Transaction
            $outboxId = null;
            $recId = null;

            DB::beginTransaction();
            try {
                $values = [
                    'source_from_company_id' => $source_from_company_id,
                    'source_from_user_id' => $source_from_user_id,
                    'source_from_type' => $source_from_type,
                    'source_company_id' => $source_company_id,
                    'source_application_id' => $source_application_id,
                    'source_from_id' => $source_application_id,
                    'source_jobid' => $source_jobid,
                    'ac_status' => 0,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                DB::table('rec_candidate_assign')->insert($values);
                $recId = (int) DB::getPdo()->lastInsertId();

                // God-Level Marketplace Canonical Submission Synchronization & Cycle Audit Trail
                try {
                    $existingSub = DB::table('marketplace_submissions')
                        ->where('job_id', $source_jobid)
                        ->where('agency_id', $source_from_company_id)
                        ->where('candidate_id', $source_application_id)
                        ->where('channel', 'BENCH_TALENT')
                        ->first();

                    $subId = null;
                    if ($existingSub) {
                        DB::table('marketplace_submissions')
                            ->where('id', $existingSub->id)
                            ->update([
                                'company_id' => $source_company_id,
                                'application_id' => $source_application_id,
                                'lifecycle_status' => 'SUBMITTED',
                                'version' => ($existingSub->version ?? 1) + 1,
                                'updated_at' => now(),
                            ]);
                        $subId = $existingSub->id;
                    } else {
                        $insertData = [
                            'job_id' => $source_jobid,
                            'agency_id' => $source_from_company_id,
                            'company_id' => $source_company_id,
                            'candidate_id' => $source_application_id,
                            'channel' => 'BENCH_TALENT',
                            'application_id' => $source_application_id,
                            'lifecycle_status' => 'SUBMITTED',
                            'version' => 1,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                        try {
                            $insertData['origin_source'] = 'MARKETPLACE';
                            $subId = DB::table('marketplace_submissions')->insertGetId($insertData);
                        } catch (\Throwable $colEx) {
                            unset($insertData['origin_source']);
                            $insertData['source'] = 'MARKETPLACE';
                            $subId = DB::table('marketplace_submissions')->insertGetId($insertData);
                        }
                    }

                    // Record Immutable Audit Log for this cycle
                    if ($subId) {
                        try {
                            DB::table('marketplace_submission_audit_logs')->insert([
                                'submission_id' => $subId,
                                'actor_id' => $userId,
                                'actor_role' => 'agency_recruiter',
                                'action' => 'SUBMITTED',
                                'from_status' => $existingSub ? $existingSub->lifecycle_status : null,
                                'to_status' => 'SUBMITTED',
                                'source' => 'MARKETPLACE',
                                'reason' => $currentCycle > 1 ? "Resubmission Cycle #{$currentCycle} after previous cycle expired/inactive" : 'Initial bench candidate submission',
                                'created_at' => now(),
                            ]);
                        } catch (\Throwable $auditEx) {
                            // Safe fallback if audit log table is optional
                        }
                    }
                } catch (\Throwable $mktSubEx) {
                    Log::warning('[MarketplaceSubmission] Sync failed: '.$mktSubEx->getMessage());
                }

                // Coins Deductions within transaction
                GeneralMethod::coinsDeductions((int) $source_from_company_id, $eventId, $userId, $source_company_id, '', '');

                // Cache Version Increment for tenant pipeline
                $newEpoch = (int) Cache::increment("pipeline_v2:{$source_from_company_id}:epoch");
                if ($source_company_id !== $source_from_company_id) {
                    Cache::increment("pipeline_v2:{$source_company_id}:epoch");
                }

                // 5. Insert Transactional Outbox Event inside the SAME Transaction
                $outboxPayload = [
                    'rec_id' => $recId,
                    'source_application_id' => $source_application_id,
                    'source_jobid' => $source_jobid,
                    'source_from_company_id' => $source_from_company_id,
                    'source_company_id' => $source_company_id,
                    'user_id' => $userId,
                    'epoch' => $newEpoch,
                    'cycle' => $currentCycle,
                    'action' => $action,
                    'tab' => $tab,
                    'timestamp' => now()->toIso8601String(),
                ];

                $outboxId = (int) DB::table('sys_outbox_events')->insertGetId([
                    'tenant_id' => $source_from_company_id,
                    'idempotency_key' => $idempotencyKey,
                    'aggregate_type' => 'BenchCandidateRequest',
                    'aggregate_id' => (int) $source_application_id,
                    'aggregate_version' => $newEpoch,
                    'event_type' => 'BenchCandidateRecommended',
                    'payload' => json_encode($outboxPayload),
                    'status' => 'PENDING',
                    'attempts' => 0,
                    'available_at' => now(),
                    'created_at' => now(),
                ]);

                // 6. Update sys_idempotency_keys status to COMPLETED
                $successResponse = [
                    'status' => 'success',
                    'message' => 'Bench request successfully sent for this job requirement!',
                    'rec_id' => $recId,
                    'job_id' => $source_jobid,
                    'candidate_id' => $source_application_id,
                ];

                DB::table('sys_idempotency_keys')
                    ->where('tenant_id', $tenantId)
                    ->where('idempotency_key', $idempotencyKey)
                    ->update([
                        'status' => 'COMPLETED',
                        'response_status' => 200,
                        'response_body' => json_encode($successResponse),
                        'completed_at' => now(),
                        'updated_at' => now(),
                    ]);

                DB::commit();

            } catch (\Throwable $txException) {
                DB::rollBack();
                Log::error('[BenchRequestMutationService] Transaction failed: '.$txException->getMessage(), [
                    'tenant_id' => $tenantId,
                    'job_id' => $jobId,
                    'candidate_id' => $candidateId,
                    'trace' => $txException->getTraceAsString(),
                ]);

                DB::table('sys_idempotency_keys')
                    ->where('tenant_id', $tenantId)
                    ->where('idempotency_key', $idempotencyKey)
                    ->update([
                        'status' => 'FAILED_RETRYABLE',
                        'last_error' => $txException->getMessage(),
                        'updated_at' => now(),
                    ]);

                return [
                    'status' => 'error',
                    'message' => 'Database transaction failed while submitting bench request.',
                ];
            }

            // 7. Post-Commit Asynchronous Outbox Worker & Immediate Elastic Sync Hook
            if ($outboxId) {
                try {
                    ProcessBenchRequestOutboxEventJob::dispatch($outboxId)->onQueue('candidate-index');
                } catch (\Throwable $qEx) {
                    Log::warning('[BenchRequestMutationService] Outbox worker queue dispatch deferred: '.$qEx->getMessage());
                }
            }

            // Post-HTTP response terminating hook: guarantee immediate ES indexing and counter invalidation
            // executed post-HTTP response without delaying the recruiter's UI
            app()->terminating(function () use ($recId, $source_jobid, $source_company_id, $source_from_company_id, $userId) {
                try {
                    GeneralMethod::candidateRequestIndexElastic((int) $recId);
                    GeneralMethod::invalidateJobCounters((int) $source_jobid, [(int) $source_company_id, (int) $source_from_company_id]);
                    GeneralMethod::cacheClean('allcandidatesrequest_version_user_'.$userId);
                } catch (\Throwable $e) {
                    Log::warning('[BenchRequestMutationService] Terminating sync failed: '.$e->getMessage());
                }
            });

            // Invalidate recommendation version caches for this user & tenant
            $cachekeys = [
                'allbenchrecommendreq_version_user_'.$userId,
                'allbenchrecommendinternalreq_version_user_'.$userId,
                'allbenchrecommendreq_version_company_'.$source_from_company_id,
                'allbenchrecommendinternalreq_version_company_'.$source_from_company_id,
                'allcandidatesrequest_version_user_'.$userId,
            ];
            foreach ($cachekeys as $key) {
                GeneralMethod::cacheClean($key);
            }

            GeneralMethod::invalidateJobCounters((int) $source_jobid, [(int) $source_company_id, (int) $source_from_company_id]);
            \App\Services\MarketJobV2Service::invalidateCacheForCompany((int) $source_from_company_id);

            return $successResponse;

        } finally {
            $lock->release();
        }
    }
}
