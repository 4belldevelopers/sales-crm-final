<?php

namespace App\Services;

use App\Events\JobStatusChanged;
use App\Helper\GeneralMethod;
use App\Jobs\ProcessJobOutboxEventJob;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Class SuperAdminJobMutationService
 *
 * Intent: Encapsulates atomic write mutations for Job requisitions executed by Super Admin.
 * Enforces transactional outbox persistence (sys_outbox_events), RFC-4122/hash-based
 * distributed idempotency (sys_idempotency_keys) with concurrent spin-wait replay,
 * row-locked concurrency safety, atomic multi-tenant cache invalidation via
 * SuperAdminJobsCacheManager, authoritative mutation delta overlay, and dual-store
 * synchronization with Elasticsearch (ELASTIC_JOBS).
 *
 * Tenancy Note: Universal Super Admin execution context scoped to target tenant_id ($job->company_id).
 * Database Truth: database/schema/mysql-schema.sql (jobs, job_status_logs, sys_idempotency_keys, sys_outbox_events).
 *
 * Author: Antigravity AI
 * Complies with docs/CODE_DOCUMENTATION_STANDARD.md.
 */
class SuperAdminJobMutationService
{
    /**
     * Atomically update a job's status with idempotency and outbox event persistence.
     *
     * @param  int  $jobId  Target job identifier
     * @param  string  $status  Normalized status ('active', 'hold', 'inactive')
     * @param  string|null  $idempotencyKey  Unique client or server mutation token
     * @param  array  $options  Additional metadata or actor options
     * @return array Standardized mutation result payload
     */
    public function updateJobStatus(int $jobId, string $status, ?string $idempotencyKey = null, array $options = []): array
    {
        $status = strtolower(trim($status));
        if ($status === 'hiring') {
            $status = 'active';
        } elseif ($status === 'closed') {
            $status = 'inactive';
        }

        if (! in_array($status, ['active', 'hold', 'inactive'], true)) {
            return [
                'status' => 'error',
                'message' => 'Invalid status. Status must be active, hold, or inactive.',
                'http_status' => 422,
            ];
        }

        $job = DB::table('jobs')->where('id', $jobId)->first();
        if (! $job) {
            return [
                'status' => 'error',
                'message' => 'Job not found.',
                'http_status' => 404,
            ];
        }

        $tenantId = (int) ($job->company_id ?? 0);
        $userId = (int) ($options['user_id'] ?? auth()->id() ?? 1);

        $idempotencyKey = $idempotencyKey
            ?: ('sa_job_stat_'.$jobId.'_'.substr(md5($jobId.':'.$status.':'.now()->format('YmdH')), 0, 16));

        $canonicalHash = hash('sha256', "{$tenantId}:job_status:{$jobId}:{$status}");

        // 1. Atomic Idempotency Lease Claim (sys_idempotency_keys table)
        $claimed = DB::table('sys_idempotency_keys')->insertOrIgnore([
            'tenant_id' => $tenantId,
            'idempotency_key' => $idempotencyKey,
            'request_hash' => $canonicalHash,
            'status' => 'PROCESSING',
            'expires_at' => now()->addHours(24),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        if ($claimed === 0) {
            $existing = DB::table('sys_idempotency_keys')
                ->where('tenant_id', $tenantId)
                ->where('idempotency_key', $idempotencyKey)
                ->first();

            if ($existing && $existing->status === 'COMPLETED' && $existing->response_body) {
                $payload = json_decode((string) $existing->response_body, true);
                if (is_array($payload)) {
                    $payload['is_replayed'] = true;

                    return $payload;
                }
            }

            // Concurrent Request Handling: bounded spin-wait (up to 1.5s) for in-flight transaction
            if ($existing && $existing->status === 'PROCESSING') {
                $maxWaitMs = 1500;
                $stepMs = 50;
                $elapsedMs = 0;

                while ($elapsedMs < $maxWaitMs) {
                    usleep($stepMs * 1000);
                    $elapsedMs += $stepMs;

                    $poll = DB::table('sys_idempotency_keys')
                        ->where('tenant_id', $tenantId)
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
                        return [
                            'status' => 'error',
                            'message' => 'Status update failed: '.($poll->last_error ?? 'Unknown error'),
                            'http_status' => 500,
                        ];
                    }
                }

                return [
                    'status' => 'error',
                    'message' => 'This job status update is currently being processed. Please do not double submit.',
                    'http_status' => 409,
                ];
            }
        }

        // 2. Transactional Write + Outbox Event Append
        $outboxId = null;
        DB::beginTransaction();
        try {
            // Row-lock the job record to guarantee isolation
            $lockedJob = DB::table('jobs')->where('id', $jobId)->lockForUpdate()->first();
            $oldStatus = (string) ($lockedJob->status ?? $job->status ?? 'active');

            $updateData = [
                'status' => $status,
                'updated_at' => now(),
            ];

            if ($status === 'active') {
                $updateData['auto_date_job'] = Carbon::today();
            } elseif ($status === 'inactive') {
                $updateData['public_market_status'] = 0;
            }

            DB::table('jobs')->where('id', $jobId)->update($updateData);

            // Log status transition for auditability
            DB::table('job_status_logs')->insert([
                'job_id' => $jobId,
                'company_id' => $tenantId,
                'from_status' => $oldStatus,
                'to_status' => $status,
                'changed_by' => $userId,
                'created_at' => Carbon::now(),
            ]);

            // Append to sys_outbox_events with initial status PENDING
            $outboxPayload = [
                'job_id' => $jobId,
                'company_id' => $tenantId,
                'old_status' => $oldStatus,
                'new_status' => $status,
                'title' => (string) ($lockedJob->title ?? $job->title ?? ''),
                'changed_by' => $userId,
                'timestamp' => now()->toIso8601String(),
            ];

            $outboxId = DB::table('sys_outbox_events')->insertGetId([
                'tenant_id' => $tenantId,
                'idempotency_key' => $idempotencyKey,
                'aggregate_type' => 'job',
                'aggregate_id' => $jobId,
                'aggregate_version' => 1,
                'event_type' => 'job.status_updated',
                'payload' => json_encode($outboxPayload),
                'status' => 'PENDING',
                'available_at' => now(),
                'created_at' => now(),
            ]);

            $responsePayload = [
                'status' => 'success',
                'message' => 'Job status updated successfully.',
                'job_id' => $jobId,
                'new_status' => $status,
                'outbox_id' => $outboxId,
            ];

            // 3. Mark Idempotency Key COMPLETED
            DB::table('sys_idempotency_keys')
                ->where('tenant_id', $tenantId)
                ->where('idempotency_key', $idempotencyKey)
                ->update([
                    'status' => 'COMPLETED',
                    'response_status' => 200,
                    'response_body' => json_encode($responsePayload),
                    'completed_at' => now(),
                    'updated_at' => now(),
                ]);

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();

            DB::table('sys_idempotency_keys')
                ->where('tenant_id', $tenantId)
                ->where('idempotency_key', $idempotencyKey)
                ->update([
                    'status' => 'FAILED_FINAL',
                    'last_error' => $e->getMessage(),
                    'updated_at' => now(),
                ]);

            Log::error("SuperAdminJobMutationService::updateJobStatus failure for Job #{$jobId}: ".$e->getMessage());

            return [
                'status' => 'error',
                'message' => 'Failed to update job status: '.$e->getMessage(),
                'http_status' => 500,
            ];
        }

        // 4. Store Authoritative In-Memory Mutation Delta & Invalidate Caches
        SuperAdminJobsCacheManager::setMutationDelta($jobId, [
            'status' => $status,
            'auto_date_job' => $updateData['auto_date_job'] ?? null,
            'public_market_status' => $updateData['public_market_status'] ?? null,
        ]);
        SuperAdminJobsCacheManager::invalidateAll($jobId);

        // 5. Fast-Path Dual-Store Synchronization & Strictly-Fenced Outbox Completion
        $esSuccess = false;
        $broadcastSuccess = false;

        try {
            GeneralMethod::elasticJobsAddEditUpdateIndex($jobId);
            $esSuccess = true;
        } catch (\Throwable $e) {
            Log::warning("SuperAdminJobMutationService fast-path ES sync warning for Job #{$jobId}: ".$e->getMessage());
        }

        try {
            $sequenceId = (int) (microtime(true) * 1000);
            broadcast(new JobStatusChanged(
                companyId: (int) $tenantId,
                jobId: (int) $jobId,
                oldStatus: (string) $oldStatus,
                newStatus: (string) $status,
                jobTitle: (string) ($job->title ?? ''),
                sequenceId: $sequenceId
            ));
            $broadcastSuccess = true;
        } catch (\Throwable $e) {
            Log::warning('SuperAdminJobMutationService JobStatusChanged broadcast warning: '.$e->getMessage());
        }

        // 6. Strict Outbox Completion Semantics
        if ($outboxId) {
            if ($esSuccess && $broadcastSuccess) {
                // Both side-effects succeeded synchronously
                DB::table('sys_outbox_events')
                    ->where('id', $outboxId)
                    ->update([
                        'status' => 'COMPLETED',
                        'processed_at' => now(),
                        'last_error' => null,
                    ]);
            } else {
                // One or both failed: keep event for worker retry with exponential backoff
                DB::table('sys_outbox_events')
                    ->where('id', $outboxId)
                    ->update([
                        'status' => 'FAILED',
                        'attempts' => 1,
                        'available_at' => now()->addSeconds(5),
                        'last_error' => 'Fast-path incomplete: ES='.($esSuccess ? 'OK' : 'FAIL').', Broadcast='.($broadcastSuccess ? 'OK' : 'FAIL'),
                    ]);
                ProcessJobOutboxEventJob::dispatch((int) $outboxId);
            }
        }

        return $responsePayload;
    }

    /**
     * Invalidate all in-memory Redis/Dragonfly query and entity caches for jobs.
     * Delegates to centralized SuperAdminJobsCacheManager for O(1) invalidation.
     *
     * @param  int|null  $jobId  Optional specific job identifier
     */
    public function flushJobsCaches(?int $jobId = null): void
    {
        SuperAdminJobsCacheManager::invalidateAll($jobId);
    }
}
