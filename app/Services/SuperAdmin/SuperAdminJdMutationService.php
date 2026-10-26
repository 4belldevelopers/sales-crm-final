<?php

namespace App\Services\SuperAdmin;

use App\Jobs\ProcessJdOutboxEventJob;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Class SuperAdminJdMutationService
 *
 * INTENT:
 * Encapsulates all mutating operations for the Job Description (JD) Master Catalog under the
 * Transactional Outbox and Idempotency Architecture (Phase 5).
 * Guarantees zero duplicate executions under network retries, atomic state transitions,
 * bypasses the Taxonomy V2 database trigger safely via session variable override,
 * invalidates multi-tenant Redis caches, and dispatches Reverb WebSockets broadcasting under 10K concurrency.
 *
 * TENANCY NOTE:
 * Operates on the global master JD catalog (tenant_id = 0). Tenant boundary coordination
 * is enforced via sys_idempotency_keys and sys_outbox_events with tenant_id = 0.
 *
 * Author: Antigravity AI — Phase 5 Outbox & Idempotency Architecture
 * Date: October 26, 2026
 * Complies with docs/CODE_DOCUMENTATION_STANDARD.md.
 */
class SuperAdminJdMutationService
{
    /**
     * Synthesize or format an idempotency key for a single JD mutation.
     */
    public function formatIdempotencyKey(int $jdId, int $newStatus, ?string $providedKey = null): string
    {
        if ($providedKey && trim($providedKey) !== '') {
            $cleanKey = preg_replace('/[^a-zA-Z0-9_\-\:\.]/', '', trim($providedKey));
            return substr($cleanKey, 0, 64);
        }

        $entropy = substr(sha1(uniqid((string) $jdId, true)), 0, 12);
        return substr("sa_jd_{$jdId}_st{$newStatus}_{$entropy}", 0, 64);
    }

    /**
     * Update the status of a single Job Description (0 = Pending, 1 = Approved, 2 = Declined).
     *
     * @param int $jdId Target Job Description ID
     * @param int $newStatus Target status (0, 1, or 2)
     * @param string|null $idempotencyKey Client-supplied or generated idempotency key
     * @param int|null $adminId Authenticated administrator user ID
     * @param string|null $adminName Authenticated administrator name
     * @return array Response payload
     */
    public function updateStatus(
        int $jdId,
        int $newStatus,
        ?string $idempotencyKey = null,
        ?int $adminId = null,
        ?string $adminName = null
    ): array {
        $idempotencyKey = $this->formatIdempotencyKey($jdId, $newStatus, $idempotencyKey);
        $canonicalPayload = [
            'jd_id' => $jdId,
            'new_status' => $newStatus,
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
                    return is_array($cachedBody) ? $cachedBody : ['status' => 'success', 'data' => ['replayed' => true]];
                }

                if ($existing->status === 'PROCESSING') {
                    abort(409, 'A status mutation for this Job Description is already currently processing.');
                }

                if ($existing->status === 'FAILED_FINAL') {
                    $cachedBody = json_decode($existing->response_body, true);
                    return is_array($cachedBody) ? $cachedBody : ['status' => 'error', 'message' => $existing->last_error];
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

        // 2. Execute domain mutation inside MySQL Transaction
        DB::beginTransaction();
        try {
            // Safe Taxonomy V2 trigger bypass override for Super Admin session
            DB::statement('SET @ALLOW_LEGACY_JD_MUTATION = 1;');

            $jd = DB::table('jd_details')
                ->where('id', $jdId)
                ->lockForUpdate()
                ->first();

            if (! $jd) {
                DB::rollBack();
                abort(404, "Job Description #{$jdId} not found.");
            }

            $oldStatus = (int) $jd->status;

            // Execute status update
            DB::table('jd_details')
                ->where('id', $jdId)
                ->update([
                    'status' => $newStatus,
                ]);

            $statusText = match ($newStatus) {
                1 => 'Approved',
                2 => 'Declined',
                default => 'Pending',
            };

            $domainResult = [
                'status' => 'success',
                'message' => "Job Description '{$jd->title}' has been successfully marked as {$statusText}.",
                'data' => [
                    'jd_id' => $jdId,
                    'title' => $jd->title,
                    'old_status' => $oldStatus,
                    'new_status' => $newStatus,
                    'idempotency_key' => $idempotencyKey,
                ],
            ];

            // 3. Atomically write to sys_outbox_events
            $outboxId = DB::table('sys_outbox_events')->insertGetId([
                'tenant_id' => 0,
                'idempotency_key' => $idempotencyKey,
                'aggregate_type' => 'JdMaster',
                'aggregate_id' => $jdId,
                'aggregate_version' => 1,
                'event_type' => 'JD_STATUS_UPDATED',
                'payload' => json_encode([
                    'jd_id' => $jdId,
                    'title' => $jd->title,
                    'old_status' => $oldStatus,
                    'new_status' => $newStatus,
                    'admin_id' => $adminId,
                    'admin_name' => $adminName ?? 'Super Admin',
                    'timestamp' => Carbon::now()->toIso8601String(),
                ]),
                'status' => 'PENDING',
                'attempts' => 0,
                'available_at' => Carbon::now(),
                'created_at' => Carbon::now(),
            ]);

            // 4. Update Idempotency Key Status to COMPLETED
            DB::table('sys_idempotency_keys')
                ->where('tenant_id', 0)
                ->where('idempotency_key', $idempotencyKey)
                ->update([
                    'status' => 'COMPLETED',
                    'response_status' => 200,
                    'response_body' => json_encode($domainResult),
                    'completed_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);

            DB::commit();

            // 5. Asynchronous Outbox Worker Dispatch with Fast-Path Execution
            if ($outboxId) {
                try {
                    (new ProcessJdOutboxEventJob($outboxId))->handle();
                } catch (\Throwable $fastPathEx) {
                    Log::warning("[JD_MUTATION] Fast-path sync queued to background: {$fastPathEx->getMessage()}", [
                        'outbox_id' => $outboxId,
                    ]);
                    ProcessJdOutboxEventJob::dispatch($outboxId);
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

            Log::error("[JD_MUTATION] Failed to update JD #{$jdId} status: " . $e->getMessage(), [
                'jd_id' => $jdId,
                'new_status' => $newStatus,
                'trace' => $e->getTraceAsString(),
            ]);

            throw $e;
        }
    }

    /**
     * Batch update multiple Job Descriptions to a target status with deadlock prevention.
     *
     * @param array<int> $jdIds Array of JD IDs
     * @param int $newStatus Target status (1 = Approved, 2 = Declined)
     * @param string|null $idempotencyKey Client-supplied or generated idempotency key
     * @param int|null $adminId Authenticated administrator user ID
     * @param string|null $adminName Authenticated administrator name
     * @return array Response payload
     */
    public function batchUpdateStatus(
        array $jdIds,
        int $newStatus,
        ?string $idempotencyKey = null,
        ?int $adminId = null,
        ?string $adminName = null
    ): array {
        // Sanitize and sort IDs ascending to prevent MySQL deadlocks
        $cleanIds = array_values(array_unique(array_filter(array_map('intval', $jdIds))));
        sort($cleanIds);

        if (empty($cleanIds)) {
            return [
                'status' => 'error',
                'message' => 'No valid Job Description IDs provided for batch operation.',
            ];
        }

        if (! $idempotencyKey || trim($idempotencyKey) === '') {
            $idempotencyKey = 'sa_jd_batch_' . substr(sha1(json_encode($cleanIds) . '_' . $newStatus . '_' . microtime(true)), 0, 24);
        } else {
            $idempotencyKey = substr(preg_replace('/[^a-zA-Z0-9_\-\:\.]/', '', trim($idempotencyKey)), 0, 64);
        }

        $canonicalPayload = [
            'jd_ids' => $cleanIds,
            'new_status' => $newStatus,
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
                    return is_array($cachedBody) ? $cachedBody : ['status' => 'success', 'data' => ['replayed' => true]];
                }

                if ($existing->status === 'PROCESSING') {
                    abort(409, 'Batch mutation for these Job Descriptions is currently in progress.');
                }
            }
        }

        // 2. Execute Batch Update inside MySQL Transaction
        DB::beginTransaction();
        $outboxIds = [];
        try {
            DB::statement('SET @ALLOW_LEGACY_JD_MUTATION = 1;');

            $records = DB::table('jd_details')
                ->whereIn('id', $cleanIds)
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            DB::table('jd_details')
                ->whereIn('id', $cleanIds)
                ->update(['status' => $newStatus]);

            $statusText = match ($newStatus) {
                1 => 'Approved',
                2 => 'Declined',
                default => 'Pending',
            };

            foreach ($cleanIds as $id) {
                $record = $records->get($id);
                $title = $record ? $record->title : "JD #{$id}";
                $oldStatus = $record ? (int) $record->status : 0;

                $outboxId = DB::table('sys_outbox_events')->insertGetId([
                    'tenant_id' => 0,
                    'idempotency_key' => "{$idempotencyKey}_{$id}",
                    'aggregate_type' => 'JdMaster',
                    'aggregate_id' => $id,
                    'aggregate_version' => 1,
                    'event_type' => 'JD_STATUS_UPDATED',
                    'payload' => json_encode([
                        'jd_id' => $id,
                        'title' => $title,
                        'old_status' => $oldStatus,
                        'new_status' => $newStatus,
                        'admin_id' => $adminId,
                        'admin_name' => $adminName ?? 'Super Admin',
                        'timestamp' => Carbon::now()->toIso8601String(),
                    ]),
                    'status' => 'PENDING',
                    'attempts' => 0,
                    'available_at' => Carbon::now(),
                    'created_at' => Carbon::now(),
                ]);
                $outboxIds[] = $outboxId;
            }

            $domainResult = [
                'status' => 'success',
                'message' => count($cleanIds) . " Job Descriptions have been successfully marked as {$statusText}.",
                'data' => [
                    'updated_count' => count($cleanIds),
                    'jd_ids' => $cleanIds,
                    'new_status' => $newStatus,
                    'idempotency_key' => $idempotencyKey,
                ],
            ];

            // Update idempotency key to COMPLETED
            DB::table('sys_idempotency_keys')
                ->where('tenant_id', 0)
                ->where('idempotency_key', $idempotencyKey)
                ->update([
                    'status' => 'COMPLETED',
                    'response_status' => 200,
                    'response_body' => json_encode($domainResult),
                    'completed_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);

            DB::commit();

            // Invalidate Redis caches immediately
            Cache::forget('superadmin:jd:counts:v1');
            Cache::forget('superadmin:jd:list:status_0');
            Cache::forget('superadmin:jd:list:status_1');
            Cache::forget('superadmin:jd:list:status_2');

            // Dispatch outbox events
            foreach ($outboxIds as $oid) {
                try {
                    (new ProcessJdOutboxEventJob($oid))->handle();
                } catch (\Throwable $ex) {
                    ProcessJdOutboxEventJob::dispatch($oid);
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

            Log::error('[JD_MUTATION] Batch update failed: ' . $e->getMessage(), [
                'ids' => $cleanIds,
                'trace' => $e->getTraceAsString(),
            ]);

            throw $e;
        }
    }

    /**
     * Delete a Job Description with transactional outbox and idempotency guarantees.
     *
     * @param int $jdId Target Job Description ID
     * @param string|null $idempotencyKey Client-supplied or generated idempotency key
     * @param int|null $adminId Authenticated administrator user ID
     * @param string|null $adminName Authenticated administrator name
     * @return array Response payload
     */
    public function deleteJd(
        int $jdId,
        ?string $idempotencyKey = null,
        ?int $adminId = null,
        ?string $adminName = null
    ): array {
        if (! $idempotencyKey || trim($idempotencyKey) === '') {
            $idempotencyKey = "sa_jd_del_{$jdId}_" . substr(sha1(microtime(true) . (string) $jdId), 0, 16);
        } else {
            $idempotencyKey = substr(preg_replace('/[^a-zA-Z0-9_\-\:\.]/', '', trim($idempotencyKey)), 0, 64);
        }

        $canonicalHash = sha1(json_encode(['jd_id' => $jdId, 'action' => 'delete', 'admin_id' => $adminId]));

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

            if ($existing && $existing->status === 'COMPLETED') {
                $cachedBody = json_decode($existing->response_body, true);
                return is_array($cachedBody) ? $cachedBody : ['status' => 'success', 'data' => ['replayed' => true]];
            }
        }

        DB::beginTransaction();
        try {
            DB::statement('SET @ALLOW_LEGACY_JD_MUTATION = 1;');

            $jd = DB::table('jd_details')->where('id', $jdId)->lockForUpdate()->first();
            if (! $jd) {
                DB::rollBack();
                abort(404, "Job Description #{$jdId} not found.");
            }

            DB::table('jd_details')->where('id', $jdId)->delete();

            $domainResult = [
                'status' => 'success',
                'message' => "Job Description '{$jd->title}' has been successfully deleted.",
                'data' => [
                    'jd_id' => $jdId,
                    'deleted' => true,
                    'idempotency_key' => $idempotencyKey,
                ],
            ];

            $outboxId = DB::table('sys_outbox_events')->insertGetId([
                'tenant_id' => 0,
                'idempotency_key' => $idempotencyKey,
                'aggregate_type' => 'JdMaster',
                'aggregate_id' => $jdId,
                'aggregate_version' => 1,
                'event_type' => 'JD_DELETED',
                'payload' => json_encode([
                    'jd_id' => $jdId,
                    'title' => $jd->title,
                    'old_status' => (int) $jd->status,
                    'new_status' => -1,
                    'admin_id' => $adminId,
                    'admin_name' => $adminName ?? 'Super Admin',
                    'timestamp' => Carbon::now()->toIso8601String(),
                ]),
                'status' => 'PENDING',
                'attempts' => 0,
                'available_at' => Carbon::now(),
                'created_at' => Carbon::now(),
            ]);

            DB::table('sys_idempotency_keys')
                ->where('tenant_id', 0)
                ->where('idempotency_key', $idempotencyKey)
                ->update([
                    'status' => 'COMPLETED',
                    'response_status' => 200,
                    'response_body' => json_encode($domainResult),
                    'completed_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);

            DB::commit();

            // Invalidate Redis caches
            Cache::forget('superadmin:jd:counts:v1');
            Cache::forget('superadmin:jd:list:status_0');
            Cache::forget('superadmin:jd:list:status_1');
            Cache::forget('superadmin:jd:list:status_2');

            try {
                (new ProcessJdOutboxEventJob($outboxId))->handle();
            } catch (\Throwable $ex) {
                ProcessJdOutboxEventJob::dispatch($outboxId);
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
}
