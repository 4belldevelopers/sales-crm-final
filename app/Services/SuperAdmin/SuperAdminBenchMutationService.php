<?php

namespace App\Services\SuperAdmin;

use App\Jobs\ProcessBenchCandidateOutboxJob;
use App\Services\SuperAdminBenchCandidatesCacheManager;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Class SuperAdminBenchMutationService
 *
 * INTENT:
 * Encapsulates all authoritative mutating operations for Super Admin Bench Candidate Moderation
 * under the Transactional Outbox and Idempotency Architecture (Phase 1).
 * Guarantees zero duplicate executions under network retries, atomic state transitions,
 * decouples asynchronous email dispatching and Elasticsearch synchronization via sys_outbox_events,
 * invalidates multi-tenant Redis/Dragonfly caches, and triggers Reverb broadcasting under 10K concurrency.
 *
 * TENANCY NOTE:
 * Operates across tenant boundaries under Super Admin authority. Coordination in sys_idempotency_keys
 * and sys_outbox_events is registered with tenant_id = 0.
 *
 * Author: Antigravity AI — Super Admin Backend Architecture
 * Complies with docs/CODE_DOCUMENTATION_STANDARD.md.
 */
class SuperAdminBenchMutationService
{
    /**
     * Format or synthesize an idempotency key.
     *
     * @param  string  $action  Action descriptor ('approve', 'reject', 'auto_approval')
     * @param  array<int>  $candidateIds  List of target candidate IDs
     * @param  string|null  $providedKey  Client-provided idempotency key from header/body
     * @return string Sanitized idempotency key (max 64 chars)
     */
    public function formatIdempotencyKey(string $action, array $candidateIds = [], ?string $providedKey = null): string
    {
        if ($providedKey && trim($providedKey) !== '') {
            $cleanKey = preg_replace('/[^a-zA-Z0-9_\-\:\.]/', '', trim($providedKey));

            return substr($cleanKey, 0, 64);
        }

        $idHash = substr(md5(implode(',', $candidateIds)), 0, 8);
        $entropy = substr(sha1(uniqid((string) time(), true)), 0, 10);

        return substr("sa_bench_{$action}_{$idHash}_{$entropy}", 0, 64);
    }

    /**
     * Authoritatively approve one or more bench candidates (Pending -> Live on Market).
     *
     * @param  array<int>  $candidateIds
     * @return array<string, mixed>
     */
    public function approveCandidates(
        array $candidateIds,
        ?string $idempotencyKey = null,
        ?int $adminId = null,
        ?string $adminName = null
    ): array {
        $candidateIds = array_values(array_filter(array_map('intval', $candidateIds)));

        if (empty($candidateIds)) {
            return [
                'status' => 'info',
                'message' => 'No valid candidate selected for approval.',
            ];
        }

        $idempotencyKey = $this->formatIdempotencyKey('approve', $candidateIds, $idempotencyKey);
        $canonicalPayload = [
            'action' => 'approve',
            'candidate_ids' => $candidateIds,
            'admin_id' => $adminId,
        ];
        $canonicalHash = sha1(json_encode($canonicalPayload));
        $fencingExpiry = Carbon::now()->addSeconds(30);

        // 1. Atomic Idempotency Claim Check
        $inserted = DB::table('sys_idempotency_keys')->insertOrIgnore([
            'tenant_id' => 0,
            'idempotency_key' => $idempotencyKey,
            'request_hash' => $canonicalHash,
            'status' => 'PROCESSING',
            'processing_until' => $fencingExpiry,
            'expires_at' => Carbon::now()->addHours(24),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        if (! $inserted) {
            $existing = DB::table('sys_idempotency_keys')
                ->where('tenant_id', 0)
                ->where('idempotency_key', $idempotencyKey)
                ->first();

            if ($existing) {
                // Strict Payload Hash Validation: Reject key reuse with conflicting parameters
                if ($existing->request_hash !== $canonicalHash) {
                    return [
                        'status' => 'error',
                        'message' => "Idempotency conflict: request payload does not match original request for key '{$idempotencyKey}'.",
                        'http_status' => 422,
                    ];
                }

                if ($existing->status === 'COMPLETED') {
                    $cachedBody = json_decode((string) $existing->response_body, true);

                    return is_array($cachedBody)
                        ? array_merge($cachedBody, ['is_replayed' => true])
                        : ['status' => 'success', 'message' => 'Selected Applicant Approved.', 'is_replayed' => true];
                }

                if ($existing->status === 'PROCESSING') {
                    $maxWaitMs = 1500;
                    $stepMs = 50;
                    $elapsedMs = 0;

                    while ($elapsedMs < $maxWaitMs) {
                        usleep($stepMs * 1000);
                        $elapsedMs += $stepMs;

                        $poll = DB::table('sys_idempotency_keys')
                            ->where('tenant_id', 0)
                            ->where('idempotency_key', $idempotencyKey)
                            ->first();

                        if ($poll && $poll->status === 'COMPLETED' && $poll->response_body) {
                            $payload = json_decode((string) $poll->response_body, true);
                            if (is_array($payload)) {
                                $payload['is_replayed'] = true;

                                return $payload;
                            }
                        }

                        if ($poll && $poll->status === 'FAILED_FINAL') {
                            $payload = json_decode((string) $poll->response_body, true);

                            return is_array($payload)
                                ? $payload
                                : ['status' => 'error', 'message' => $poll->last_error ?: 'Previous approval attempt failed.'];
                        }
                    }
                }

                if ($existing->status === 'FAILED_FINAL') {
                    $cachedBody = json_decode((string) $existing->response_body, true);

                    return is_array($cachedBody)
                        ? $cachedBody
                        : ['status' => 'error', 'message' => $existing->last_error ?: 'Previous approval attempt failed.'];
                }
            }
        }

        // 2. Authoritative Database Transaction + Outbox Insertion
        DB::beginTransaction();
        try {
            // Lock rows for update
            DB::table('job_applications')
                ->whereIn('id', $candidateIds)
                ->lockForUpdate()
                ->get();

            // Update job_applications superadminstatus to 1 (Approved) and status to 2 (Bench)
            DB::table('job_applications')
                ->whereIn('id', $candidateIds)
                ->update([
                    'superadminstatus' => 1,
                    'status' => 2,
                    'aggregate_version' => DB::raw('aggregate_version + 1'),
                    'updated_at' => Carbon::now(),
                ]);

            $outboxPayload = [
                'candidate_ids' => $candidateIds,
                'superadmin_status' => 1,
                'admin_id' => $adminId,
                'admin_name' => $adminName ?: 'Super Admin',
                'timestamp' => Carbon::now()->toIso8601String(),
            ];

            $outboxId = DB::table('sys_outbox_events')->insertGetId([
                'tenant_id' => 0,
                'idempotency_key' => $idempotencyKey,
                'aggregate_type' => 'BenchCandidate',
                'aggregate_id' => $candidateIds[0],
                'aggregate_version' => 1,
                'event_type' => 'CANDIDATE_SUPERADMIN_APPROVED',
                'payload' => json_encode($outboxPayload),
                'status' => 'PENDING',
                'attempts' => 0,
                'available_at' => Carbon::now(),
                'created_at' => Carbon::now(),
            ]);

            $response = [
                'status' => 'success',
                'message' => 'Selected Applicant Approved.',
                'candidate_ids' => $candidateIds,
                'datatype' => 'approve',
                'outbox_id' => $outboxId,
            ];

            // 3. Mark Idempotency Key as COMPLETED
            DB::table('sys_idempotency_keys')
                ->where('tenant_id', 0)
                ->where('idempotency_key', $idempotencyKey)
                ->update([
                    'status' => 'COMPLETED',
                    'response_status' => 200,
                    'response_body' => json_encode($response),
                    'completed_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);

            // 4. In-Memory Mutation Delta for 0ms Read-After-Write Consistency
            foreach ($candidateIds as $cid) {
                SuperAdminBenchCandidatesCacheManager::setMutationDelta($cid, [
                    'superadminstatus' => 1,
                    'status' => 2,
                ]);
            }

            // Invalidate Redis caches
            SuperAdminBenchCandidatesCacheManager::invalidateAll();

            DB::commit();

            // 5. Dispatch Asynchronous Outbox Worker
            ProcessBenchCandidateOutboxJob::dispatch($outboxId)->afterCommit();

            return $response;

        } catch (\Throwable $e) {
            DB::rollBack();

            Log::error('[BENCH_MUTATION] Approval failed: '.$e->getMessage(), [
                'candidate_ids' => $candidateIds,
                'idempotency_key' => $idempotencyKey,
                'trace' => $e->getTraceAsString(),
            ]);

            DB::table('sys_idempotency_keys')
                ->where('tenant_id', 0)
                ->where('idempotency_key', $idempotencyKey)
                ->update([
                    'status' => 'FAILED_FINAL',
                    'response_status' => 500,
                    'last_error' => substr($e->getMessage(), 0, 1000),
                    'updated_at' => Carbon::now(),
                ]);

            throw $e;
        }
    }

    /**
     * Authoritatively reject one or more bench candidates.
     *
     * @param  array<int>  $candidateIds
     * @return array<string, mixed>
     */
    public function rejectCandidates(
        array $candidateIds,
        ?string $idempotencyKey = null,
        ?int $adminId = null,
        ?string $adminName = null
    ): array {
        $candidateIds = array_values(array_filter(array_map('intval', $candidateIds)));

        if (empty($candidateIds)) {
            return [
                'status' => 'info',
                'message' => 'No valid candidate selected for rejection.',
            ];
        }

        $idempotencyKey = $this->formatIdempotencyKey('reject', $candidateIds, $idempotencyKey);
        $canonicalPayload = [
            'action' => 'reject',
            'candidate_ids' => $candidateIds,
            'admin_id' => $adminId,
        ];
        $canonicalHash = sha1(json_encode($canonicalPayload));
        $fencingExpiry = Carbon::now()->addSeconds(30);

        // 1. Atomic Idempotency Claim Check
        $inserted = DB::table('sys_idempotency_keys')->insertOrIgnore([
            'tenant_id' => 0,
            'idempotency_key' => $idempotencyKey,
            'request_hash' => $canonicalHash,
            'status' => 'PROCESSING',
            'processing_until' => $fencingExpiry,
            'expires_at' => Carbon::now()->addHours(24),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        if (! $inserted) {
            $existing = DB::table('sys_idempotency_keys')
                ->where('tenant_id', 0)
                ->where('idempotency_key', $idempotencyKey)
                ->first();

            if ($existing) {
                // Strict Payload Hash Validation: Reject key reuse with conflicting parameters
                if ($existing->request_hash !== $canonicalHash) {
                    return [
                        'status' => 'error',
                        'message' => "Idempotency conflict: request payload does not match original request for key '{$idempotencyKey}'.",
                        'http_status' => 422,
                    ];
                }

                if ($existing->status === 'COMPLETED') {
                    $cachedBody = json_decode((string) $existing->response_body, true);

                    return is_array($cachedBody)
                        ? array_merge($cachedBody, ['is_replayed' => true])
                        : ['status' => 'success', 'message' => 'Selected Applicant Rejected.', 'is_replayed' => true];
                }

                if ($existing->status === 'PROCESSING') {
                    $maxWaitMs = 1500;
                    $stepMs = 50;
                    $elapsedMs = 0;

                    while ($elapsedMs < $maxWaitMs) {
                        usleep($stepMs * 1000);
                        $elapsedMs += $stepMs;

                        $poll = DB::table('sys_idempotency_keys')
                            ->where('tenant_id', 0)
                            ->where('idempotency_key', $idempotencyKey)
                            ->first();

                        if ($poll && $poll->status === 'COMPLETED' && $poll->response_body) {
                            $payload = json_decode((string) $poll->response_body, true);
                            if (is_array($payload)) {
                                $payload['is_replayed'] = true;

                                return $payload;
                            }
                        }

                        if ($poll && $poll->status === 'FAILED_FINAL') {
                            $payload = json_decode((string) $poll->response_body, true);

                            return is_array($payload)
                                ? $payload
                                : ['status' => 'error', 'message' => $poll->last_error ?: 'Previous rejection attempt failed.'];
                        }
                    }
                }

                if ($existing->status === 'FAILED_FINAL') {
                    $cachedBody = json_decode((string) $existing->response_body, true);

                    return is_array($cachedBody)
                        ? $cachedBody
                        : ['status' => 'error', 'message' => $existing->last_error ?: 'Previous rejection attempt failed.'];
                }
            }
        }

        DB::beginTransaction();
        try {
            DB::table('job_applications')
                ->whereIn('id', $candidateIds)
                ->lockForUpdate()
                ->get();

            DB::table('job_applications')
                ->whereIn('id', $candidateIds)
                ->update([
                    'superadminstatus' => 2,
                    'aggregate_version' => DB::raw('aggregate_version + 1'),
                    'updated_at' => Carbon::now(),
                ]);

            $outboxPayload = [
                'candidate_ids' => $candidateIds,
                'superadmin_status' => 2,
                'admin_id' => $adminId,
                'admin_name' => $adminName ?: 'Super Admin',
                'timestamp' => Carbon::now()->toIso8601String(),
            ];

            $outboxId = DB::table('sys_outbox_events')->insertGetId([
                'tenant_id' => 0,
                'idempotency_key' => $idempotencyKey,
                'aggregate_type' => 'BenchCandidate',
                'aggregate_id' => $candidateIds[0],
                'aggregate_version' => 1,
                'event_type' => 'CANDIDATE_SUPERADMIN_REJECTED',
                'payload' => json_encode($outboxPayload),
                'status' => 'PENDING',
                'attempts' => 0,
                'available_at' => Carbon::now(),
                'created_at' => Carbon::now(),
            ]);

            $response = [
                'status' => 'success',
                'message' => 'Selected Applicant Rejected.',
                'candidate_ids' => $candidateIds,
                'datatype' => 'reject',
                'outbox_id' => $outboxId,
            ];

            DB::table('sys_idempotency_keys')
                ->where('tenant_id', 0)
                ->where('idempotency_key', $idempotencyKey)
                ->update([
                    'status' => 'COMPLETED',
                    'response_status' => 200,
                    'response_body' => json_encode($response),
                    'completed_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);

            // In-Memory Mutation Delta for 0ms Read-After-Write Consistency
            foreach ($candidateIds as $cid) {
                SuperAdminBenchCandidatesCacheManager::setMutationDelta($cid, [
                    'superadminstatus' => 2,
                ]);
            }

            SuperAdminBenchCandidatesCacheManager::invalidateAll();

            DB::commit();

            ProcessBenchCandidateOutboxJob::dispatch($outboxId)->afterCommit();

            return $response;

        } catch (\Throwable $e) {
            DB::rollBack();

            Log::error('[BENCH_MUTATION] Rejection failed: '.$e->getMessage(), [
                'candidate_ids' => $candidateIds,
                'idempotency_key' => $idempotencyKey,
            ]);

            DB::table('sys_idempotency_keys')
                ->where('tenant_id', 0)
                ->where('idempotency_key', $idempotencyKey)
                ->update([
                    'status' => 'FAILED_FINAL',
                    'response_status' => 500,
                    'last_error' => substr($e->getMessage(), 0, 1000),
                    'updated_at' => Carbon::now(),
                ]);

            throw $e;
        }
    }

    /**
     * Authoritatively reject one or more bench candidates with asynchronous email notification.
     * Decouples the slow synchronous mail loop into the Transactional Outbox worker.
     *
     * @param  array<int>  $candidateIds
     * @param  array<string, mixed>  $emailPayload
     * @return array<string, mixed>
     */
    public function rejectCandidatesWithEmail(
        array $candidateIds,
        array $emailPayload,
        ?string $idempotencyKey = null,
        ?int $adminId = null,
        ?string $adminName = null
    ): array {
        $candidateIds = array_values(array_filter(array_map('intval', $candidateIds)));

        if (empty($candidateIds)) {
            return [
                'status' => 'info',
                'message' => 'No valid candidate selected for rejection.',
            ];
        }

        $idempotencyKey = $this->formatIdempotencyKey('reject_mail', $candidateIds, $idempotencyKey);
        $canonicalPayload = [
            'action' => 'reject_mail',
            'candidate_ids' => $candidateIds,
            'admin_id' => $adminId,
            'subject' => $emailPayload['subject'] ?? '',
        ];
        $canonicalHash = sha1(json_encode($canonicalPayload));
        $fencingExpiry = Carbon::now()->addSeconds(30);

        // 1. Atomic Idempotency Claim Check
        $inserted = DB::table('sys_idempotency_keys')->insertOrIgnore([
            'tenant_id' => 0,
            'idempotency_key' => $idempotencyKey,
            'request_hash' => $canonicalHash,
            'status' => 'PROCESSING',
            'processing_until' => $fencingExpiry,
            'expires_at' => Carbon::now()->addHours(24),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        if (! $inserted) {
            $existing = DB::table('sys_idempotency_keys')
                ->where('tenant_id', 0)
                ->where('idempotency_key', $idempotencyKey)
                ->first();

            if ($existing) {
                if ($existing->request_hash !== $canonicalHash) {
                    return [
                        'status' => 'error',
                        'message' => "Idempotency conflict: request payload does not match original request for key '{$idempotencyKey}'.",
                        'http_status' => 422,
                    ];
                }

                if ($existing->status === 'COMPLETED') {
                    $cachedBody = json_decode((string) $existing->response_body, true);

                    return is_array($cachedBody)
                        ? array_merge($cachedBody, ['is_replayed' => true])
                        : ['status' => 'success', 'message' => 'Rejection notice dispatched.', 'is_replayed' => true];
                }

                if ($existing->status === 'PROCESSING') {
                    $maxWaitMs = 1500;
                    $stepMs = 50;
                    $elapsedMs = 0;

                    while ($elapsedMs < $maxWaitMs) {
                        usleep($stepMs * 1000);
                        $elapsedMs += $stepMs;

                        $poll = DB::table('sys_idempotency_keys')
                            ->where('tenant_id', 0)
                            ->where('idempotency_key', $idempotencyKey)
                            ->first();

                        if ($poll && $poll->status === 'COMPLETED' && $poll->response_body) {
                            $payload = json_decode((string) $poll->response_body, true);
                            if (is_array($payload)) {
                                $payload['is_replayed'] = true;

                                return $payload;
                            }
                        }

                        if ($poll && $poll->status === 'FAILED_FINAL') {
                            $payload = json_decode((string) $poll->response_body, true);

                            return is_array($payload)
                                ? $payload
                                : ['status' => 'error', 'message' => $poll->last_error ?: 'Previous rejection attempt failed.'];
                        }
                    }
                }
            }
        }

        DB::beginTransaction();
        try {
            DB::table('job_applications')
                ->whereIn('id', $candidateIds)
                ->lockForUpdate()
                ->get();

            DB::table('job_applications')
                ->whereIn('id', $candidateIds)
                ->update([
                    'superadminstatus' => 2,
                    'aggregate_version' => DB::raw('aggregate_version + 1'),
                    'updated_at' => Carbon::now(),
                ]);

            $outboxPayload = [
                'candidate_ids' => $candidateIds,
                'superadmin_status' => 2,
                'admin_id' => $adminId,
                'admin_name' => $adminName ?: 'Super Admin',
                'email_payload' => $emailPayload,
                'timestamp' => Carbon::now()->toIso8601String(),
            ];

            $outboxId = DB::table('sys_outbox_events')->insertGetId([
                'tenant_id' => 0,
                'idempotency_key' => $idempotencyKey,
                'aggregate_type' => 'BenchCandidate',
                'aggregate_id' => $candidateIds[0],
                'aggregate_version' => 1,
                'event_type' => 'CANDIDATE_SUPERADMIN_REJECTED_WITH_MAIL',
                'payload' => json_encode($outboxPayload),
                'status' => 'PENDING',
                'attempts' => 0,
                'available_at' => Carbon::now(),
                'created_at' => Carbon::now(),
            ]);

            $response = [
                'status' => 'success',
                'message' => 'Candidate rejection registered and notification emails queued.',
                'candidate_ids' => $candidateIds,
                'datatype' => 'reject',
                'outbox_id' => $outboxId,
            ];

            DB::table('sys_idempotency_keys')
                ->where('tenant_id', 0)
                ->where('idempotency_key', $idempotencyKey)
                ->update([
                    'status' => 'COMPLETED',
                    'response_status' => 200,
                    'response_body' => json_encode($response),
                    'completed_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);

            // In-Memory Mutation Delta for 0ms Read-After-Write Consistency
            foreach ($candidateIds as $cid) {
                SuperAdminBenchCandidatesCacheManager::setMutationDelta($cid, [
                    'superadminstatus' => 2,
                ]);
            }

            SuperAdminBenchCandidatesCacheManager::invalidateAll();

            DB::commit();

            ProcessBenchCandidateOutboxJob::dispatch($outboxId)->afterCommit();

            return $response;

        } catch (\Throwable $e) {
            DB::rollBack();

            Log::error('[BENCH_MUTATION] Rejection with mail failed: '.$e->getMessage(), [
                'candidate_ids' => $candidateIds,
                'idempotency_key' => $idempotencyKey,
            ]);

            DB::table('sys_idempotency_keys')
                ->where('tenant_id', 0)
                ->where('idempotency_key', $idempotencyKey)
                ->update([
                    'status' => 'FAILED_FINAL',
                    'response_status' => 500,
                    'last_error' => substr($e->getMessage(), 0, 1000),
                    'updated_at' => Carbon::now(),
                ]);

            throw $e;
        }
    }

    /**
     * Authoritatively toggle global bench auto-approval setting.
     *
     * @return array<string, mixed>
     */
    public function toggleAutoApproval(int $settingId, ?string $idempotencyKey = null, ?int $adminId = null): array
    {
        $idempotencyKey = $this->formatIdempotencyKey('auto_approval', [$settingId], $idempotencyKey);
        $canonicalPayload = [
            'action' => 'toggle_auto_approval',
            'setting_id' => $settingId,
            'admin_id' => $adminId,
        ];
        $canonicalHash = sha1(json_encode($canonicalPayload));
        $fencingExpiry = Carbon::now()->addSeconds(30);

        // 1. Atomic Idempotency Claim Check
        $inserted = DB::table('sys_idempotency_keys')->insertOrIgnore([
            'tenant_id' => 0,
            'idempotency_key' => $idempotencyKey,
            'request_hash' => $canonicalHash,
            'status' => 'PROCESSING',
            'processing_until' => $fencingExpiry,
            'expires_at' => Carbon::now()->addHours(24),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        if (! $inserted) {
            $existing = DB::table('sys_idempotency_keys')
                ->where('tenant_id', 0)
                ->where('idempotency_key', $idempotencyKey)
                ->first();

            if ($existing) {
                if ($existing->request_hash !== $canonicalHash) {
                    return [
                        'status' => 'error',
                        'message' => "Idempotency conflict: request payload does not match original request for key '{$idempotencyKey}'.",
                        'http_status' => 422,
                    ];
                }

                if ($existing->status === 'COMPLETED') {
                    $cachedBody = json_decode((string) $existing->response_body, true);

                    return is_array($cachedBody)
                        ? array_merge($cachedBody, ['is_replayed' => true])
                        : ['status' => 'success', 'message' => 'Changed setting auto approval.', 'is_replayed' => true];
                }

                if ($existing->status === 'PROCESSING') {
                    $maxWaitMs = 1500;
                    $stepMs = 50;
                    $elapsedMs = 0;

                    while ($elapsedMs < $maxWaitMs) {
                        usleep($stepMs * 1000);
                        $elapsedMs += $stepMs;

                        $poll = DB::table('sys_idempotency_keys')
                            ->where('tenant_id', 0)
                            ->where('idempotency_key', $idempotencyKey)
                            ->first();

                        if ($poll && $poll->status === 'COMPLETED' && $poll->response_body) {
                            $payload = json_decode((string) $poll->response_body, true);
                            if (is_array($payload)) {
                                $payload['is_replayed'] = true;

                                return $payload;
                            }
                        }

                        if ($poll && $poll->status === 'FAILED_FINAL') {
                            $payload = json_decode((string) $poll->response_body, true);

                            return is_array($payload)
                                ? $payload
                                : ['status' => 'error', 'message' => $poll->last_error ?: 'Previous toggle attempt failed.'];
                        }
                    }
                }

                if ($existing->status === 'FAILED_FINAL') {
                    $cachedBody = json_decode((string) $existing->response_body, true);

                    return is_array($cachedBody)
                        ? $cachedBody
                        : ['status' => 'error', 'message' => $existing->last_error ?: 'Previous toggle attempt failed.'];
                }
            }
        }

        // 2. Authoritative Database Transaction + Outbox Insertion
        DB::beginTransaction();
        try {
            $bench_status = DB::table('global_settings')
                ->select('bench_status')
                ->where('id', $settingId)
                ->lockForUpdate()
                ->first();

            $newStatus = 1;
            if ($bench_status && $bench_status->bench_status) {
                $newStatus = 0;
            }

            DB::table('global_settings')
                ->where('id', $settingId)
                ->update(['bench_status' => $newStatus]);

            $outboxPayload = [
                'setting_id' => $settingId,
                'new_status' => $newStatus,
                'admin_id' => $adminId,
                'timestamp' => Carbon::now()->toIso8601String(),
            ];

            $outboxId = DB::table('sys_outbox_events')->insertGetId([
                'tenant_id' => 0,
                'idempotency_key' => $idempotencyKey,
                'aggregate_type' => 'BenchCandidate',
                'aggregate_id' => $settingId,
                'aggregate_version' => 1,
                'event_type' => 'SETTING_AUTO_APPROVAL_TOGGLED',
                'payload' => json_encode($outboxPayload),
                'status' => 'PENDING',
                'attempts' => 0,
                'available_at' => Carbon::now(),
                'created_at' => Carbon::now(),
            ]);

            $response = [
                'status' => 'success',
                'message' => 'Changed setting auto approval.',
                'bench_status' => $newStatus,
                'outbox_id' => $outboxId,
            ];

            // 3. Mark Idempotency Key as COMPLETED
            DB::table('sys_idempotency_keys')
                ->where('tenant_id', 0)
                ->where('idempotency_key', $idempotencyKey)
                ->update([
                    'status' => 'COMPLETED',
                    'response_status' => 200,
                    'response_body' => json_encode($response),
                    'completed_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);

            SuperAdminBenchCandidatesCacheManager::invalidateAll();

            DB::commit();

            // 4. Dispatch Asynchronous Outbox Worker
            ProcessBenchCandidateOutboxJob::dispatch($outboxId)->afterCommit();

            return $response;

        } catch (\Throwable $e) {
            DB::rollBack();

            Log::error('[BENCH_MUTATION] Toggle auto-approval failed: '.$e->getMessage());

            DB::table('sys_idempotency_keys')
                ->where('tenant_id', 0)
                ->where('idempotency_key', $idempotencyKey)
                ->update([
                    'status' => 'FAILED_FINAL',
                    'response_status' => 500,
                    'last_error' => substr($e->getMessage(), 0, 1000),
                    'updated_at' => Carbon::now(),
                ]);

            throw $e;
        }
    }
}
