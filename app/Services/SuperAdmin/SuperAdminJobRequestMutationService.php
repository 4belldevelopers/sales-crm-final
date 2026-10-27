<?php

namespace App\Services\SuperAdmin;

use App\Jobs\ProcessJobRequestOutboxEventJob;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Class SuperAdminJobRequestMutationService
 *
 * INTENT:
 * Encapsulates all mutating operations for Job Collaboration Requests (recommendation_relation table)
 * under the Transactional Outbox and Idempotency Architecture (Phase 2 & Phase 7).
 * Guarantees zero duplicate executions under network retries, atomic state transitions with pessimistic
 * row locking, appends transactional outbox events to sys_outbox_events, updates Elasticsearch
 * local_jobs_request documents, and triggers real-time Reverb WebSocket broadcasts on superadmin-job-requests.
 *
 * TENANCY NOTE:
 * Operates under the Super Admin governance domain (tenant_id = 0). Tenant boundary coordination
 * is enforced via sys_idempotency_keys and sys_outbox_events with tenant_id = 0.
 *
 * Author: Antigravity AI — Phase 2 Outbox & Idempotency Architecture
 * Date: October 27, 2026
 * Complies with docs/CODE_DOCUMENTATION_STANDARD.md and docs/AI_RULES.md.
 */
class SuperAdminJobRequestMutationService
{
    /**
     * Synthesize or format an idempotency key for a Job Request mutation.
     *
     * @param int $recRelationId Target recommendation_relation ID
     * @param string $action Action name (e.g., 'accept', 'reject', 'terms')
     * @param string|null $providedKey Client-supplied UUID
     * @return string Sanitized idempotency key (max 64 chars)
     */
    public function formatIdempotencyKey(int $recRelationId, string $action, ?string $providedKey = null): string
    {
        if ($providedKey && trim($providedKey) !== '') {
            $cleanKey = preg_replace('/[^a-zA-Z0-9_\-\:\.]/', '', trim($providedKey));
            return substr($cleanKey, 0, 64);
        }

        $entropy = substr(sha1(uniqid((string) $recRelationId, true)), 0, 12);
        return substr("sajr_{$recRelationId}_{$action}_{$entropy}", 0, 64);
    }

    /**
     * Update the final_status of a Job Collaboration Request (0 = Pending, 1 = Accepted, 2 = Rejected, 3 = Closed).
     *
     * @param int $recRelationId Target recommendation_relation ID
     * @param int $finalStatus Target final_status (0, 1, 2, or 3)
     * @param string|null $idempotencyKey Client-supplied or generated idempotency key
     * @param int|null $adminId Authenticated administrator user ID
     * @param string|null $adminName Authenticated administrator name
     * @param string|null $accepterms Optional contract terms string
     * @return array Standardized JSON response payload
     */
    public function updateJobRequestStatus(
        int $recRelationId,
        int $finalStatus,
        ?string $idempotencyKey = null,
        ?int $adminId = null,
        ?string $adminName = null,
        ?string $accepterms = null
    ): array {
        $action = match ($finalStatus) {
            1 => 'accept',
            2, 3 => 'reject',
            default => 'reset',
        };

        $idempotencyKey = $this->formatIdempotencyKey($recRelationId, $action, $idempotencyKey);
        $canonicalPayload = [
            'rec_relation_id' => $recRelationId,
            'final_status' => $finalStatus,
            'admin_id' => $adminId,
            'accepterms' => $accepterms,
        ];
        $canonicalHash = sha1(json_encode($canonicalPayload));

        // 1. Atomic Idempotency Claim Check
        $inserted = DB::table('sys_idempotency_keys')->insertOrIgnore([
            'tenant_id' => 0,
            'idempotency_key' => $idempotencyKey,
            'request_hash' => $canonicalHash,
            'status' => 'PROCESSING',
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
                if ($existing->status === 'COMPLETED') {
                    $cachedBody = json_decode($existing->response_body, true);
                    return is_array($cachedBody)
                        ? array_merge($cachedBody, ['replayed' => true])
                        : ['status' => 'success', 'data' => ['replayed' => true]];
                }

                if ($existing->status === 'PROCESSING') {
                    abort(409, 'A status mutation for this Job Collaboration Request is already in progress.');
                }

                if ($existing->status === 'FAILED_FINAL') {
                    $cachedBody = json_decode($existing->response_body, true);
                    return is_array($cachedBody)
                        ? $cachedBody
                        : ['status' => 'error', 'message' => $existing->last_error];
                }

                // If FAILED_RETRYABLE, reclaim the lease
                DB::table('sys_idempotency_keys')
                    ->where('tenant_id', 0)
                    ->where('idempotency_key', $idempotencyKey)
                    ->update([
                        'status' => 'PROCESSING',
                        'updated_at' => Carbon::now(),
                    ]);
            }
        }

        // 2. Execute domain mutation inside MySQL Transaction with pessimistic lock
        DB::beginTransaction();
        try {
            $rec = DB::table('recommendation_relation')
                ->where('id', $recRelationId)
                ->lockForUpdate()
                ->first();

            if (! $rec) {
                DB::rollBack();
                abort(404, "Job Collaboration Request #{$recRelationId} not found.");
            }

            $oldStatus = (int) $rec->final_status;
            $toStatus = ($finalStatus === 1) ? 1 : (($finalStatus >= 2) ? 2 : 0);
            $now = Carbon::now();

            $updateData = [
                'final_status' => $finalStatus,
                'to_status' => $toStatus,
                'to_user_id' => $adminId ?? 0,
                'to_created_at' => $now->toDateString(),
                'updated_at' => $now,
            ];

            if ($accepterms !== null) {
                $updateData['accepterms'] = $accepterms;
            }

            DB::table('recommendation_relation')
                ->where('id', $recRelationId)
                ->update($updateData);

            $statusText = match ($finalStatus) {
                1 => 'Accepted',
                2 => 'Rejected',
                3 => 'Closed',
                default => 'Pending',
            };

            $domainResult = [
                'status' => 'success',
                'message' => "Job Collaboration Request #{$recRelationId} has been successfully marked as {$statusText}.",
                'data' => [
                    'rec_relation_id' => $recRelationId,
                    'job_id' => (int) $rec->job_id,
                    'old_status' => $oldStatus,
                    'new_status' => $finalStatus,
                    'status_label' => $statusText,
                    'idempotency_key' => $idempotencyKey,
                ],
            ];

            // 3. Atomically write to sys_outbox_events
            $outboxId = DB::table('sys_outbox_events')->insertGetId([
                'tenant_id' => 0,
                'idempotency_key' => $idempotencyKey,
                'aggregate_type' => 'JobRequest',
                'aggregate_id' => $recRelationId,
                'aggregate_version' => 1,
                'event_type' => 'JOB_REQUEST_STATUS_UPDATED',
                'payload' => json_encode([
                    'rec_relation_id' => $recRelationId,
                    'job_id' => (int) $rec->job_id,
                    'old_status' => $oldStatus,
                    'final_status' => $finalStatus,
                    'to_status' => $toStatus,
                    'to_user_id' => $adminId,
                    'to_created_at' => $now->toDateString(),
                    'accepterms' => $accepterms ?? $rec->accepterms,
                    'admin_id' => $adminId,
                    'admin_name' => $adminName ?? 'Super Admin',
                    'action_type' => 'status_updated',
                    'timestamp' => $now->toIso8601String(),
                ]),
                'status' => 'PENDING',
                'attempts' => 0,
                'available_at' => $now,
                'created_at' => $now,
            ]);

            // 4. Update Idempotency Key Status to COMPLETED
            DB::table('sys_idempotency_keys')
                ->where('tenant_id', 0)
                ->where('idempotency_key', $idempotencyKey)
                ->update([
                    'status' => 'COMPLETED',
                    'response_status' => 200,
                    'response_body' => json_encode($domainResult),
                    'completed_at' => $now,
                    'updated_at' => $now,
                ]);

            DB::commit();

            // 5. Asynchronous Outbox Worker Dispatch with Fast-Path Execution
            if ($outboxId) {
                try {
                    (new ProcessJobRequestOutboxEventJob($outboxId))->handle();
                } catch (\Throwable $fastPathEx) {
                    Log::warning("[JOB_REQUEST_MUTATION] Fast-path sync queued to background: {$fastPathEx->getMessage()}", [
                        'outbox_id' => $outboxId,
                    ]);
                    ProcessJobRequestOutboxEventJob::dispatch($outboxId);
                }
            }

            return $domainResult;

        } catch (\Throwable $e) {
            DB::rollBack();

            // Record failure in sys_idempotency_keys
            DB::table('sys_idempotency_keys')
                ->where('tenant_id', 0)
                ->where('idempotency_key', $idempotencyKey)
                ->update([
                    'status' => 'FAILED_RETRYABLE',
                    'last_error' => substr($e->getMessage(), 0, 1000),
                    'updated_at' => Carbon::now(),
                ]);

            Log::error("[JOB_REQUEST_MUTATION] Failed to update Job Request #{$recRelationId}: " . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            throw $e;
        }
    }

    /**
     * Update the contract terms (accepterms) of a Job Collaboration Request.
     *
     * @param int $recRelationId Target recommendation_relation ID
     * @param string $terms Contract terms content
     * @param string|null $idempotencyKey Client-supplied or generated idempotency key
     * @param int|null $adminId Authenticated administrator user ID
     * @param string|null $adminName Authenticated administrator name
     * @return array Standardized JSON response payload
     */
    public function updateJobRequestTerms(
        int $recRelationId,
        string $terms,
        ?string $idempotencyKey = null,
        ?int $adminId = null,
        ?string $adminName = null
    ): array {
        $idempotencyKey = $this->formatIdempotencyKey($recRelationId, 'terms', $idempotencyKey);
        $canonicalPayload = [
            'rec_relation_id' => $recRelationId,
            'terms' => $terms,
            'admin_id' => $adminId,
        ];
        $canonicalHash = sha1(json_encode($canonicalPayload));

        // 1. Atomic Idempotency Claim Check
        $inserted = DB::table('sys_idempotency_keys')->insertOrIgnore([
            'tenant_id' => 0,
            'idempotency_key' => $idempotencyKey,
            'request_hash' => $canonicalHash,
            'status' => 'PROCESSING',
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
                if ($existing->status === 'COMPLETED') {
                    $cachedBody = json_decode($existing->response_body, true);
                    return is_array($cachedBody)
                        ? array_merge($cachedBody, ['replayed' => true])
                        : ['status' => 'success', 'data' => ['replayed' => true]];
                }

                if ($existing->status === 'PROCESSING') {
                    abort(409, 'A terms update for this Job Collaboration Request is already in progress.');
                }

                DB::table('sys_idempotency_keys')
                    ->where('tenant_id', 0)
                    ->where('idempotency_key', $idempotencyKey)
                    ->update([
                        'status' => 'PROCESSING',
                        'updated_at' => Carbon::now(),
                    ]);
            }
        }

        // 2. MySQL Transaction with pessimistic lock
        DB::beginTransaction();
        try {
            $rec = DB::table('recommendation_relation')
                ->where('id', $recRelationId)
                ->lockForUpdate()
                ->first();

            if (! $rec) {
                DB::rollBack();
                abort(404, "Job Collaboration Request #{$recRelationId} not found.");
            }

            $now = Carbon::now();
            DB::table('recommendation_relation')
                ->where('id', $recRelationId)
                ->update([
                    'accepterms' => $terms,
                    'updated_at' => $now,
                ]);

            $domainResult = [
                'status' => 'success',
                'message' => "Contract terms for Job Request #{$recRelationId} have been successfully updated.",
                'data' => [
                    'rec_relation_id' => $recRelationId,
                    'job_id' => (int) $rec->job_id,
                    'accepterms' => $terms,
                    'idempotency_key' => $idempotencyKey,
                ],
            ];

            // 3. Atomically write to sys_outbox_events
            $outboxId = DB::table('sys_outbox_events')->insertGetId([
                'tenant_id' => 0,
                'idempotency_key' => $idempotencyKey,
                'aggregate_type' => 'JobRequest',
                'aggregate_id' => $recRelationId,
                'aggregate_version' => 1,
                'event_type' => 'JOB_REQUEST_TERMS_UPDATED',
                'payload' => json_encode([
                    'rec_relation_id' => $recRelationId,
                    'job_id' => (int) $rec->job_id,
                    'final_status' => (int) $rec->final_status,
                    'accepterms' => $terms,
                    'admin_id' => $adminId,
                    'admin_name' => $adminName ?? 'Super Admin',
                    'action_type' => 'terms_updated',
                    'timestamp' => $now->toIso8601String(),
                ]),
                'status' => 'PENDING',
                'attempts' => 0,
                'available_at' => $now,
                'created_at' => $now,
            ]);

            // 4. Update Idempotency Key Status
            DB::table('sys_idempotency_keys')
                ->where('tenant_id', 0)
                ->where('idempotency_key', $idempotencyKey)
                ->update([
                    'status' => 'COMPLETED',
                    'response_status' => 200,
                    'response_body' => json_encode($domainResult),
                    'completed_at' => $now,
                    'updated_at' => $now,
                ]);

            DB::commit();

            // 5. Fast-path outbox execution
            if ($outboxId) {
                try {
                    (new ProcessJobRequestOutboxEventJob($outboxId))->handle();
                } catch (\Throwable $fastPathEx) {
                    Log::warning("[JOB_REQUEST_MUTATION] Fast-path terms sync queued: {$fastPathEx->getMessage()}", [
                        'outbox_id' => $outboxId,
                    ]);
                    ProcessJobRequestOutboxEventJob::dispatch($outboxId);
                }
            }

            return $domainResult;

        } catch (\Throwable $e) {
            DB::rollBack();

            DB::table('sys_idempotency_keys')
                ->where('tenant_id', 0)
                ->where('idempotency_key', $idempotencyKey)
                ->update([
                    'status' => 'FAILED_RETRYABLE',
                    'last_error' => substr($e->getMessage(), 0, 1000),
                    'updated_at' => Carbon::now(),
                ]);

            throw $e;
        }
    }

    /**
     * Bulk update status for multiple Job Collaboration Requests.
     *
     * @param array<int> $recRelationIds Array of recommendation_relation IDs
     * @param int $finalStatus Target final_status (1: Accept, 2: Reject)
     * @param string|null $idempotencyKey Client idempotency key prefix
     * @param int|null $adminId Authenticated administrator user ID
     * @param string|null $adminName Authenticated administrator name
     * @return array Standardized summary response
     */
    public function batchUpdateStatus(
        array $recRelationIds,
        int $finalStatus,
        ?string $idempotencyKey = null,
        ?int $adminId = null,
        ?string $adminName = null
    ): array {
        $results = [];
        $successCount = 0;
        $failCount = 0;

        foreach ($recRelationIds as $relId) {
            $singleKey = $idempotencyKey ? "{$idempotencyKey}_{$relId}" : null;
            try {
                $res = $this->updateJobRequestStatus(
                    recRelationId: (int) $relId,
                    finalStatus: $finalStatus,
                    idempotencyKey: $singleKey,
                    adminId: $adminId,
                    adminName: $adminName
                );
                $results[] = $res;
                $successCount++;
            } catch (\Throwable $e) {
                $results[] = [
                    'status' => 'error',
                    'rec_relation_id' => $relId,
                    'message' => $e->getMessage(),
                ];
                $failCount++;
            }
        }

        return [
            'status' => 'success',
            'message' => "Batch update processed: {$successCount} succeeded, {$failCount} failed.",
            'total' => count($recRelationIds),
            'success_count' => $successCount,
            'fail_count' => $failCount,
            'details' => $results,
        ];
    }
}
