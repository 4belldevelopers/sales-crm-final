<?php

namespace App\Services;

use App\Helper\GeneralMethod;
use App\Jobs\ProcessBenchRequestOutboxEventJob;
use App\Models\Job;
use App\Models\JobApplication;
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
     * @param int $tenantId Current company ID (source_from_company_id)
     * @param int $jobId Target job requirement ID
     * @param int $candidateId Target bench candidate ID (job_applications ID)
     * @param int $userId Authenticated user ID
     * @param string|null $clientKey Client-supplied idempotency key
     * @return array Standardized response array with status, message, and optional metadata
     */
    public function executeRecommendationMutation(
        int $tenantId,
        int $jobId,
        int $candidateId,
        int $userId,
        ?string $clientKey = null
    ): array {
        $canonicalPayload = [
            'tenant_id' => $tenantId,
            'job_id' => $jobId,
            'candidate_id' => $candidateId,
            'action' => 'recommend_internal_req',
        ];
        $canonicalHash = hash('xxh128', json_encode($canonicalPayload));

        // 1. Fallback or validate client idempotency key
        $idempotencyKey = !empty($clientKey)
            ? $clientKey
            : "idemp:bench_req:{$tenantId}:{$jobId}:{$candidateId}:" . substr($canonicalHash, 0, 16);

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

        if (!$inserted) {
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
        $lockKey = 'lock:' . md5($idempotencyKey);
        $lock = Cache::lock($lockKey, 10);

        if (!$lock->get()) {
            return [
                'status' => 'warning',
                'message' => 'Request already processing. Please wait.',
            ];
        }

        try {
            // Authoritative Pre-conditions: Fetch Job and Candidate
            $job = Job::withoutGlobalScopes()->findOrFail($jobId);

            $source_from_company_id = $tenantId;
            $source_from_user_id = $userId;
            $source_from_type = 1; // 1 for Bench
            $source_company_id = (int) $job->company_id;
            $source_application_id = $candidateId;
            $source_jobid = $jobId;

            // Concurrency check — prevent duplicate assignment submission in rec_candidate_assign
            $alreadyAssigned = DB::table('rec_candidate_assign')
                ->where('source_jobid', $source_jobid)
                ->where('source_application_id', $source_application_id)
                ->where('source_from_company_id', $source_from_company_id)
                ->whereIn('ac_status', [0, 1])
                ->exists();

            if ($alreadyAssigned) {
                $duplicateResponse = [
                    'status' => 'warning',
                    'message' => 'Candidate request already submitted for this job.',
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
                Log::error('[BenchRequestMutationService] Transaction failed: ' . $txException->getMessage(), [
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

            // 7. Post-Commit Asynchronous Outbox Worker & Cache Invalidation
            if ($outboxId) {
                try {
                    ProcessBenchRequestOutboxEventJob::dispatch($outboxId)->onQueue('candidate-index');
                } catch (\Throwable $qEx) {
                    Log::warning('[BenchRequestMutationService] Outbox worker queue dispatch deferred: ' . $qEx->getMessage());
                }
            }

            // Invalidate recommendation version caches for this user & tenant
            $cachekeys = [
                'allbenchrecommendreq_version_user_' . $userId,
                'allbenchrecommendinternalreq_version_user_' . $userId,
                'allbenchrecommendreq_version_company_' . $source_from_company_id,
                'allbenchrecommendinternalreq_version_company_' . $source_from_company_id,
            ];
            foreach ($cachekeys as $key) {
                GeneralMethod::cacheClean($key);
            }

            return $successResponse;

        } finally {
            $lock->release();
        }
    }
}
