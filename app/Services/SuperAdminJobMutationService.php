<?php

namespace App\Services;

use App\Events\JobStatusChanged;
use App\Helper\GeneralMethod;
use App\Jobs\ProcessJobOutboxEventJob;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * Class SuperAdminJobMutationService
 *
 * Intent: Encapsulates atomic write mutations for Job requisitions executed by Super Admin.
 * Enforces transactional outbox persistence (sys_outbox_events), dedicated delivery ledger
 * (sys_outbox_deliveries), RFC-4122/hash-based distributed idempotency (sys_idempotency_keys)
 * with fencing tokens (owner_token, lease_version), expired lease recovery, row-locked
 * concurrency safety, authoritative aggregate versioning on jobs table (OCC), atomic
 * multi-tenant cache invalidation via SuperAdminJobsCacheManager, and atomic dual-store
 * synchronization with Elasticsearch (ELASTIC_JOBS).
 *
 * Tenancy Note: Universal Super Admin execution context scoped to target tenant_id ($job->company_id).
 * Database Truth: database/schema/mysql-schema.sql (jobs, job_status_logs, sys_idempotency_keys, sys_outbox_events, sys_outbox_deliveries).
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
        $ownerToken = (string) Str::uuid();

        // 1. Atomic Idempotency Lease Claim (sys_idempotency_keys table with fencing token)
        $claimed = DB::table('sys_idempotency_keys')->insertOrIgnore([
            'tenant_id' => $tenantId,
            'idempotency_key' => $idempotencyKey,
            'request_hash' => $canonicalHash,
            'owner_token' => $ownerToken,
            'lease_version' => 1,
            'status' => 'PROCESSING',
            'processing_until' => now()->addSeconds(60),
            'expires_at' => now()->addHours(24),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        if ($claimed === 0) {
            $existing = DB::table('sys_idempotency_keys')
                ->where('tenant_id', $tenantId)
                ->where('idempotency_key', $idempotencyKey)
                ->first();

            // Requirement: Same idempotency key with DIFFERENT payload must be explicitly REJECTED
            if ($existing && $existing->request_hash !== $canonicalHash) {
                return [
                    'status' => 'error',
                    'message' => 'Idempotency key mismatch: this idempotency key was previously submitted with a different payload.',
                    'http_status' => 422,
                ];
            }

            // Return cached response if already completed
            if ($existing && $existing->status === 'COMPLETED' && $existing->response_body) {
                $payload = json_decode((string) $existing->response_body, true);
                if (is_array($payload)) {
                    $payload['is_replayed'] = true;

                    return $payload;
                }
            }

            // Expired Lease Recovery: If worker crashed or timed out (> 2 minutes or past processing_until), reclaim lease with new fencing token
            if ($existing && $existing->status === 'PROCESSING') {
                $isLeaseExpired = ($existing->processing_until && Carbon::parse($existing->processing_until)->isPast())
                    || ($existing->expires_at && Carbon::parse($existing->expires_at)->isPast())
                    || ($existing->updated_at && Carbon::parse($existing->updated_at)->lt(now()->subMinutes(2)));

                if ($isLeaseExpired) {
                    $reclaimed = DB::table('sys_idempotency_keys')
                        ->where('tenant_id', $tenantId)
                        ->where('idempotency_key', $idempotencyKey)
                        ->where('status', 'PROCESSING')
                        ->update([
                            'owner_token' => $ownerToken,
                            'lease_version' => DB::raw('lease_version + 1'),
                            'processing_until' => now()->addSeconds(60),
                            'updated_at' => now(),
                            'expires_at' => now()->addHours(24),
                        ]);

                    if ($reclaimed > 0) {
                        $claimed = 1; // Successfully reclaimed stuck lease with new fencing token
                    }
                }
            }

            // Concurrent Request Handling: bounded spin-wait (up to 1.5s) for in-flight transaction
            if ($claimed === 0 && $existing && $existing->status === 'PROCESSING') {
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
        // CRITICAL RULE: Do NOT hold MySQL row locks while performing external network operations.
        $outboxId = null;
        $newAggregateVersion = 1;
        $outboxPayload = [];

        DB::beginTransaction();
        try {
            // Row-lock the job record to guarantee isolation
            $lockedJob = DB::table('jobs')->where('id', $jobId)->lockForUpdate()->first();
            $oldStatus = (string) ($lockedJob->status ?? $job->status ?? 'active');

            // Monotonic Aggregate Version computation from authoritative jobs table
            $currentAggregateVersion = (int) ($lockedJob->aggregate_version ?? 0);
            $newAggregateVersion = $currentAggregateVersion + 1;

            $updateData = [
                'status' => $status,
                'aggregate_version' => $newAggregateVersion,
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

            // Append to sys_outbox_events with independent destination tracking
            $outboxPayload = [
                'schema_version' => '1.1',
                'job_id' => $jobId,
                'company_id' => $tenantId,
                'old_status' => $oldStatus,
                'new_status' => $status,
                'title' => (string) ($lockedJob->title ?? $job->title ?? ''),
                'changed_by' => $userId,
                'aggregate_version' => $newAggregateVersion,
                'timestamp' => now()->toIso8601String(),
                'destinations' => [
                    'redis' => [
                        'status' => 'PENDING',
                        'attempts' => 0,
                        'completed_at' => null,
                        'last_error' => null,
                    ],
                    'elasticsearch' => [
                        'status' => 'PENDING',
                        'attempts' => 0,
                        'completed_at' => null,
                        'last_error' => null,
                    ],
                    'websocket' => [
                        'status' => 'PENDING',
                        'attempts' => 0,
                        'completed_at' => null,
                        'last_error' => null,
                    ],
                ],
            ];

            $outboxId = DB::table('sys_outbox_events')->insertGetId([
                'tenant_id' => $tenantId,
                'idempotency_key' => $idempotencyKey,
                'aggregate_type' => 'job',
                'aggregate_id' => $jobId,
                'aggregate_version' => $newAggregateVersion,
                'event_type' => 'job.status_updated',
                'payload' => json_encode($outboxPayload),
                'status' => 'PENDING',
                'available_at' => now(),
                'created_at' => now(),
            ]);

            // Dedicated sys_outbox_deliveries ledger to prevent lost updates across concurrent workers
            DB::table('sys_outbox_deliveries')->insert([
                [
                    'outbox_id' => $outboxId,
                    'destination' => 'redis',
                    'status' => 'PENDING',
                    'attempts' => 0,
                    'owner_token' => null,
                    'lease_expires_at' => null,
                    'completed_at' => null,
                    'last_error' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'outbox_id' => $outboxId,
                    'destination' => 'elasticsearch',
                    'status' => 'PENDING',
                    'attempts' => 0,
                    'owner_token' => null,
                    'lease_expires_at' => null,
                    'completed_at' => null,
                    'last_error' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'outbox_id' => $outboxId,
                    'destination' => 'websocket',
                    'status' => 'PENDING',
                    'attempts' => 0,
                    'owner_token' => null,
                    'lease_expires_at' => null,
                    'completed_at' => null,
                    'last_error' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ]);

            $responsePayload = [
                'status' => 'success',
                'message' => 'Job status updated successfully.',
                'job_id' => $jobId,
                'new_status' => $status,
                'outbox_id' => $outboxId,
                'aggregate_version' => $newAggregateVersion,
            ];

            // 3. Mark Idempotency Key COMPLETED inside same atomic transaction with fencing verification
            $claimedRows = DB::table('sys_idempotency_keys')
                ->where('tenant_id', $tenantId)
                ->where('idempotency_key', $idempotencyKey)
                ->where('owner_token', $ownerToken)
                ->update([
                    'status' => 'COMPLETED',
                    'response_status' => 200,
                    'response_body' => json_encode($responsePayload),
                    'completed_at' => now(),
                    'updated_at' => now(),
                ]);

            if ($claimedRows === 0) {
                throw new \RuntimeException("Fencing token validation failed: worker {$ownerToken} lost lease before commit.");
            }

            // COMMIT MYSQL TRANSACTION IMMEDIATELY BEFORE ANY EXTERNAL NETWORK OPERATIONS
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

        // 4. Post-Commit Independent Destination Publishing (Zero locks held)
        $destinations = $outboxPayload['destinations'];
        $hasErrors = false;

        // Destination 1: Redis Invalidation & Mutation Delta
        try {
            SuperAdminJobsCacheManager::setMutationDelta($jobId, [
                'status' => $status,
                'auto_date_job' => $updateData['auto_date_job'] ?? null,
                'public_market_status' => $updateData['public_market_status'] ?? null,
                'aggregate_version' => $newAggregateVersion,
            ]);
            SuperAdminJobsCacheManager::invalidateAll($jobId);

            DB::table('sys_outbox_deliveries')
                ->where('outbox_id', $outboxId)
                ->where('destination', 'redis')
                ->update([
                    'status' => 'COMPLETED',
                    'completed_at' => now(),
                    'last_error' => null,
                    'updated_at' => now(),
                ]);

            $destinations['redis']['status'] = 'COMPLETED';
            $destinations['redis']['completed_at'] = now()->toIso8601String();
        } catch (\Throwable $e) {
            $destinations['redis']['status'] = 'FAILED';
            $destinations['redis']['last_error'] = substr($e->getMessage(), 0, 500);
            $hasErrors = true;

            DB::table('sys_outbox_deliveries')
                ->where('outbox_id', $outboxId)
                ->where('destination', 'redis')
                ->update([
                    'status' => 'FAILED',
                    'attempts' => DB::raw('attempts + 1'),
                    'last_error' => substr($e->getMessage(), 0, 500),
                    'updated_at' => now(),
                ]);

            Log::warning("SuperAdminJobMutationService Redis destination failed for Job #{$jobId}: ".$e->getMessage());
        }

        // Destination 2: Elasticsearch Dual-Store Synchronization with Painless OCC
        try {
            GeneralMethod::elasticJobsAddEditUpdateIndex($jobId, $newAggregateVersion);

            DB::table('sys_outbox_deliveries')
                ->where('outbox_id', $outboxId)
                ->where('destination', 'elasticsearch')
                ->update([
                    'status' => 'COMPLETED',
                    'completed_at' => now(),
                    'last_error' => null,
                    'updated_at' => now(),
                ]);

            $destinations['elasticsearch']['status'] = 'COMPLETED';
            $destinations['elasticsearch']['completed_at'] = now()->toIso8601String();
        } catch (\Throwable $e) {
            $destinations['elasticsearch']['status'] = 'FAILED';
            $destinations['elasticsearch']['last_error'] = substr($e->getMessage(), 0, 500);
            $hasErrors = true;

            DB::table('sys_outbox_deliveries')
                ->where('outbox_id', $outboxId)
                ->where('destination', 'elasticsearch')
                ->update([
                    'status' => 'FAILED',
                    'attempts' => DB::raw('attempts + 1'),
                    'last_error' => substr($e->getMessage(), 0, 500),
                    'updated_at' => now(),
                ]);

            Log::warning("SuperAdminJobMutationService ES destination failed for Job #{$jobId}: ".$e->getMessage());
        }

        // Destination 3: Reverb WebSocket Broadcasting with Monotonic Sequence & Aggregate Version
        try {
            broadcast(new JobStatusChanged(
                companyId: (int) $tenantId,
                jobId: (int) $jobId,
                oldStatus: (string) $oldStatus,
                newStatus: (string) $status,
                jobTitle: (string) ($job->title ?? ''),
                aggregateVersion: $newAggregateVersion
            ));

            DB::table('sys_outbox_deliveries')
                ->where('outbox_id', $outboxId)
                ->where('destination', 'websocket')
                ->update([
                    'status' => 'COMPLETED',
                    'completed_at' => now(),
                    'last_error' => null,
                    'updated_at' => now(),
                ]);

            $destinations['websocket']['status'] = 'COMPLETED';
            $destinations['websocket']['completed_at'] = now()->toIso8601String();
        } catch (\Throwable $e) {
            // Reverb broadcast is an acceleration push; transmission failure is non-fatal for transactional persistence
            DB::table('sys_outbox_deliveries')
                ->where('outbox_id', $outboxId)
                ->where('destination', 'websocket')
                ->update([
                    'status' => 'DEGRADED',
                    'last_error' => substr($e->getMessage(), 0, 500),
                    'updated_at' => now(),
                ]);

            $destinations['websocket']['status'] = 'DEGRADED';
            $destinations['websocket']['last_error'] = substr($e->getMessage(), 0, 500);
            Log::warning("SuperAdminJobMutationService WebSocket broadcast non-fatal exception for Job #{$jobId}: ".$e->getMessage());
        }

        // 5. Update Outbox Event with Independent Destination States
        $outboxPayload['destinations'] = $destinations;
        if ($outboxId) {
            if (! $hasErrors) {
                DB::table('sys_outbox_events')
                    ->where('id', $outboxId)
                    ->update([
                        'status' => 'COMPLETED',
                        'payload' => json_encode($outboxPayload),
                        'processed_at' => now(),
                        'last_error' => null,
                    ]);
            } else {
                DB::table('sys_outbox_events')
                    ->where('id', $outboxId)
                    ->update([
                        'status' => 'FAILED',
                        'payload' => json_encode($outboxPayload),
                        'attempts' => 1,
                        'available_at' => now()->addSeconds(5),
                        'last_error' => 'Partial fast-path failure; dispatched to worker for retry.',
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
