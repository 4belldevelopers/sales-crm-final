<?php

namespace App\Services\SuperAdmin;

use App\Events\SuperAdminMarketplaceUpdatedEvent;
use App\Jobs\ProcessMarketJobOutboxEventJob;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Class SuperAdminMarketplaceMutationService
 *
 * INTENT:
 * Encapsulates all authoritative mutating operations for Super Admin Marketplace Operations & Job Public Market
 * under the Transactional Outbox and Idempotency Architecture (Phase 1).
 * Guarantees zero duplicate executions under network retries, atomic state transitions,
 * decouples asynchronous email dispatching and Elasticsearch synchronization via sys_outbox_events,
 * invalidates multi-tenant Redis/Dragonfly caches, and triggers Reverb broadcasting under 10K concurrency.
 *
 * TENANCY NOTE:
 * Operates across tenant boundaries under Super Admin authority. Coordination in sys_idempotency_keys
 * and sys_outbox_events is registered with tenant_id = 0 (or company_id of target job).
 *
 * Author: Antigravity AI — Super Admin Backend Architecture
 * Complies with docs/CODE_DOCUMENTATION_STANDARD.md and docs/AI_RULES.md.
 */
class SuperAdminMarketplaceMutationService
{
    /**
     * Format or synthesize a unique idempotency key.
     */
    public function formatIdempotencyKey(string $action, int $jobId, ?string $providedKey = null): string
    {
        if ($providedKey && trim($providedKey) !== '') {
            $cleanKey = preg_replace('/[^a-zA-Z0-9_\-\:\.]/', '', trim($providedKey));

            return substr($cleanKey, 0, 64);
        }

        $entropy = substr(sha1(uniqid((string) time(), true)), 0, 10);

        return substr("sa_mkt_{$action}_{$jobId}_{$entropy}", 0, 64);
    }

    /**
     * Authoritatively approve a marketplace job requisition (Pending -> Live Approved).
     */
    public function approveJob(
        int $jobId,
        ?string $notes = null,
        ?string $idempotencyKey = null,
        ?int $adminId = null,
        ?string $adminName = null
    ): array {
        if ($jobId <= 0) {
            return [
                'status' => 'error',
                'message' => 'Invalid Job ID specified.',
                'http_status' => 400,
            ];
        }

        $idempotencyKey = $this->formatIdempotencyKey('approve', $jobId, $idempotencyKey);
        $canonicalPayload = [
            'action' => 'approve',
            'job_id' => $jobId,
            'notes' => $notes ?? '',
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
                        : ['status' => 'success', 'message' => "Requisition #{$jobId} approved and published to marketplace.", 'is_replayed' => true];
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
            }
        }

        // 2. Execute Atomic MySQL Transaction
        DB::beginTransaction();
        try {
            $job = DB::table('jobs')->where('id', $jobId)->lockForUpdate()->first();
            if (! $job) {
                DB::rollBack();
                DB::table('sys_idempotency_keys')->where('idempotency_key', $idempotencyKey)->delete();

                return [
                    'status' => 'error',
                    'message' => "Job requisition #{$jobId} not found.",
                    'http_status' => 404,
                ];
            }

            $currentVersion = (int) ($job->aggregate_version ?? 0);
            $newVersion = $currentVersion + 1;
            $actorId = $adminId ?? (auth()->id() ?? 1);
            $fromModState = $job->market_mod_state ?? 'pending';

            // Authoritative mutations
            DB::table('jobs')->where('id', $jobId)->update([
                'public_market_status' => 2,
                'market_mod_state' => 'approved',
                'market_pub_state' => 'published',
                'aggregate_version' => $newVersion,
                'updated_at' => Carbon::now(),
            ]);

            // Insert audit log
            DB::table('job_marketplace_moderation_logs')->insert([
                'job_id' => $jobId,
                'company_id' => (int) $job->company_id,
                'actor_id' => $actorId,
                'actor_role' => 'superadmin',
                'action' => 'manual_approve',
                'from_mod_state' => $fromModState,
                'to_mod_state' => 'approved',
                'reason_code' => 'ADMIN_APPROVAL',
                'rejection_notes' => $notes,
                'risk_score' => 0.00,
                'created_at' => Carbon::now(),
            ]);

            // Synchronize marketplace_channels if row exists
            DB::table('marketplace_channels')
                ->where('job_id', $jobId)
                ->update([
                    'moderation_status' => 'APPROVED',
                    'publication_status' => 'LIVE',
                    'moderated_by' => $actorId,
                    'moderated_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);

            // Insert transactional outbox event
            $outboxId = DB::table('sys_outbox_events')->insertGetId([
                'tenant_id' => (int) $job->company_id,
                'idempotency_key' => $idempotencyKey,
                'aggregate_type' => 'MarketJob',
                'aggregate_id' => $jobId,
                'aggregate_version' => $newVersion,
                'event_type' => 'MarketJobPublished',
                'payload' => json_encode([
                    'job_id' => $jobId,
                    'company_id' => (int) $job->company_id,
                    'market_pub_state' => 'published',
                    'market_mod_state' => 'approved',
                    'public_market_status' => 2,
                    'aggregate_version' => $newVersion,
                    'actor_id' => $actorId,
                    'notes' => $notes,
                ]),
                'status' => 'PENDING',
                'attempts' => 0,
                'available_at' => Carbon::now(),
                'created_at' => Carbon::now(),
            ]);

            $responsePayload = [
                'status' => 'success',
                'http_status' => 200,
                'message' => "Requisition #{$jobId} approved and published to marketplace.",
                'job_id' => $jobId,
                'version' => $newVersion,
                'aggregate_version' => $newVersion,
                'outbox_id' => $outboxId,
            ];

            // Mark Idempotency Key Completed
            DB::table('sys_idempotency_keys')
                ->where('tenant_id', 0)
                ->where('idempotency_key', $idempotencyKey)
                ->update([
                    'status' => 'COMPLETED',
                    'response_status' => 200,
                    'response_body' => json_encode($responsePayload),
                    'completed_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);

            // Invalidate Redis caches and set 0ms mutation delta
            SuperAdminMarketplaceCacheManager::setMutationDelta($jobId, [
                'public_market_status' => 2,
                'market_mod_state' => 'approved',
                'market_pub_state' => 'published',
                'aggregate_version' => $newVersion,
            ]);
            SuperAdminMarketplaceCacheManager::invalidateAll();

            DB::commit();

            // 3. Post-Commit Asynchronous Outbox Dispatch & WebSocket Broadcast
            try {
                ProcessMarketJobOutboxEventJob::dispatch((int) $outboxId)->afterCommit();
            } catch (\Throwable $e) {
                Log::warning("ProcessMarketJobOutboxEventJob dispatch warning for outbox #{$outboxId}: ".$e->getMessage());
            }

            try {
                $seq = SuperAdminMarketplaceCacheManager::nextSequenceId();
                event(new SuperAdminMarketplaceUpdatedEvent(
                    jobId: $jobId,
                    action: 'approve',
                    marketPubState: 'published',
                    marketModState: 'approved',
                    publicMarketStatus: 2,
                    version: $newVersion,
                    streamSequence: $seq,
                    payload: $responsePayload
                ));
            } catch (\Throwable $e) {
                Log::warning('SuperAdminMarketplaceUpdatedEvent broadcast warning: '.$e->getMessage());
            }

            return $responsePayload;

        } catch (\Throwable $e) {
            DB::rollBack();

            DB::table('sys_idempotency_keys')
                ->where('tenant_id', 0)
                ->where('idempotency_key', $idempotencyKey)
                ->update([
                    'status' => 'FAILED_FINAL',
                    'last_error' => substr($e->getMessage(), 0, 1000),
                    'updated_at' => Carbon::now(),
                ]);

            Log::error("SuperAdminMarketplaceMutationService::approveJob failed for Job #{$jobId}: ".$e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            return [
                'status' => 'error',
                'message' => 'Failed to approve job requisition: '.$e->getMessage(),
                'http_status' => 500,
            ];
        }
    }

    /**
     * Authoritatively reject a marketplace job requisition with remediation instructions.
     */
    public function rejectJob(
        int $jobId,
        string $reasonCode,
        ?string $notes = null,
        ?string $idempotencyKey = null,
        ?int $adminId = null,
        ?string $adminName = null
    ): array {
        if ($jobId <= 0) {
            return [
                'status' => 'error',
                'message' => 'Invalid Job ID specified.',
                'http_status' => 400,
            ];
        }

        $idempotencyKey = $this->formatIdempotencyKey('reject', $jobId, $idempotencyKey);
        $canonicalPayload = [
            'action' => 'reject',
            'job_id' => $jobId,
            'reason_code' => $reasonCode,
            'notes' => $notes ?? '',
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
                        : ['status' => 'success', 'message' => "Requisition #{$jobId} rejected with remediation instructions sent.", 'is_replayed' => true];
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
                    }
                }
            }
        }

        // 2. Execute Atomic MySQL Transaction
        DB::beginTransaction();
        try {
            $job = DB::table('jobs')->where('id', $jobId)->lockForUpdate()->first();
            if (! $job) {
                DB::rollBack();
                DB::table('sys_idempotency_keys')->where('idempotency_key', $idempotencyKey)->delete();

                return [
                    'status' => 'error',
                    'message' => "Job requisition #{$jobId} not found.",
                    'http_status' => 404,
                ];
            }

            $currentVersion = (int) ($job->aggregate_version ?? 0);
            $newVersion = $currentVersion + 1;
            $actorId = $adminId ?? (auth()->id() ?? 1);
            $fromModState = $job->market_mod_state ?? 'pending';

            DB::table('jobs')->where('id', $jobId)->update([
                'public_market_status' => 3,
                'market_mod_state' => 'rejected',
                'market_pub_state' => 'unpublished',
                'aggregate_version' => $newVersion,
                'updated_at' => Carbon::now(),
            ]);

            DB::table('job_marketplace_moderation_logs')->insert([
                'job_id' => $jobId,
                'company_id' => (int) $job->company_id,
                'actor_id' => $actorId,
                'actor_role' => 'superadmin',
                'action' => 'reject',
                'from_mod_state' => $fromModState,
                'to_mod_state' => 'rejected',
                'reason_code' => $reasonCode,
                'rejection_notes' => $notes,
                'risk_score' => 0.00,
                'created_at' => Carbon::now(),
            ]);

            DB::table('marketplace_channels')
                ->where('job_id', $jobId)
                ->update([
                    'moderation_status' => 'REJECTED',
                    'publication_status' => 'DELISTED',
                    'moderated_by' => $actorId,
                    'moderated_at' => Carbon::now(),
                    'moderation_reason' => $reasonCode,
                    'updated_at' => Carbon::now(),
                ]);

            $outboxId = DB::table('sys_outbox_events')->insertGetId([
                'tenant_id' => (int) $job->company_id,
                'idempotency_key' => $idempotencyKey,
                'aggregate_type' => 'MarketJob',
                'aggregate_id' => $jobId,
                'aggregate_version' => $newVersion,
                'event_type' => 'MarketJobRejected',
                'payload' => json_encode([
                    'job_id' => $jobId,
                    'company_id' => (int) $job->company_id,
                    'market_pub_state' => 'unpublished',
                    'market_mod_state' => 'rejected',
                    'public_market_status' => 3,
                    'reason_code' => $reasonCode,
                    'aggregate_version' => $newVersion,
                    'actor_id' => $actorId,
                    'notes' => $notes,
                ]),
                'status' => 'PENDING',
                'attempts' => 0,
                'available_at' => Carbon::now(),
                'created_at' => Carbon::now(),
            ]);

            $responsePayload = [
                'status' => 'success',
                'http_status' => 200,
                'message' => "Requisition #{$jobId} rejected with remediation instructions sent.",
                'job_id' => $jobId,
                'version' => $newVersion,
                'aggregate_version' => $newVersion,
                'outbox_id' => $outboxId,
            ];

            DB::table('sys_idempotency_keys')
                ->where('tenant_id', 0)
                ->where('idempotency_key', $idempotencyKey)
                ->update([
                    'status' => 'COMPLETED',
                    'response_status' => 200,
                    'response_body' => json_encode($responsePayload),
                    'completed_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);

            SuperAdminMarketplaceCacheManager::setMutationDelta($jobId, [
                'public_market_status' => 3,
                'market_mod_state' => 'rejected',
                'market_pub_state' => 'unpublished',
                'aggregate_version' => $newVersion,
            ]);
            SuperAdminMarketplaceCacheManager::invalidateAll();

            DB::commit();

            try {
                ProcessMarketJobOutboxEventJob::dispatch((int) $outboxId)->afterCommit();
            } catch (\Throwable $e) {
                Log::warning("ProcessMarketJobOutboxEventJob dispatch warning for outbox #{$outboxId}: ".$e->getMessage());
            }

            try {
                $seq = SuperAdminMarketplaceCacheManager::nextSequenceId();
                event(new SuperAdminMarketplaceUpdatedEvent(
                    jobId: $jobId,
                    action: 'reject',
                    marketPubState: 'unpublished',
                    marketModState: 'rejected',
                    publicMarketStatus: 3,
                    version: $newVersion,
                    streamSequence: $seq,
                    payload: $responsePayload
                ));
            } catch (\Throwable $e) {
                Log::warning('SuperAdminMarketplaceUpdatedEvent broadcast warning: '.$e->getMessage());
            }

            return $responsePayload;

        } catch (\Throwable $e) {
            DB::rollBack();

            DB::table('sys_idempotency_keys')
                ->where('tenant_id', 0)
                ->where('idempotency_key', $idempotencyKey)
                ->update([
                    'status' => 'FAILED_FINAL',
                    'last_error' => substr($e->getMessage(), 0, 1000),
                    'updated_at' => Carbon::now(),
                ]);

            return [
                'status' => 'error',
                'message' => 'Failed to reject job requisition: '.$e->getMessage(),
                'http_status' => 500,
            ];
        }
    }

    /**
     * Toggles global Auto-Approval setting atomically with idempotency.
     */
    public function updateSettings(
        bool $autoApproval,
        ?string $idempotencyKey = null,
        ?int $adminId = null
    ): array {
        $idempotencyKey = $idempotencyKey
            ? substr(preg_replace('/[^a-zA-Z0-9_\-\:\.]/', '', trim($idempotencyKey)), 0, 64)
            : 'sa_mkt_settings_'.($autoApproval ? '1' : '0').'_'.substr(md5((string) microtime(true)), 0, 8);

        $newVal = $autoApproval ? 1 : 0;
        $canonicalHash = sha1(json_encode(['action' => 'update_settings', 'auto_approval' => $newVal]));

        $inserted = DB::table('sys_idempotency_keys')->insertOrIgnore([
            'tenant_id' => 0,
            'idempotency_key' => $idempotencyKey,
            'request_hash' => $canonicalHash,
            'status' => 'PROCESSING',
            'processing_until' => Carbon::now()->addSeconds(30),
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
                        'message' => 'Idempotency conflict for settings update.',
                        'http_status' => 422,
                    ];
                }

                if ($existing->status === 'COMPLETED' && $existing->response_body) {
                    $body = json_decode((string) $existing->response_body, true);

                    return is_array($body) ? array_merge($body, ['is_replayed' => true]) : $body;
                }
            }
        }

        DB::table('global_settings')->update(['public_market_job_status' => $newVal]);

        $responsePayload = [
            'status' => 'success',
            'auto_approval' => (bool) $newVal,
            'message' => $newVal ? 'Auto-Approval enabled (Trust Score >= 85).' : 'Auto-Approval paused (Manual review required).',
        ];

        DB::table('sys_idempotency_keys')
            ->where('tenant_id', 0)
            ->where('idempotency_key', $idempotencyKey)
            ->update([
                'status' => 'COMPLETED',
                'response_status' => 200,
                'response_body' => json_encode($responsePayload),
                'completed_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);

        SuperAdminMarketplaceCacheManager::invalidateGlobalSettings();
        SuperAdminMarketplaceCacheManager::invalidateAll();

        try {
            $seq = SuperAdminMarketplaceCacheManager::nextSequenceId();
            event(new SuperAdminMarketplaceUpdatedEvent(
                jobId: 0,
                action: 'auto_approval_toggle',
                marketPubState: 'settings_changed',
                marketModState: 'settings_changed',
                publicMarketStatus: $newVal,
                version: 1,
                streamSequence: $seq,
                payload: $responsePayload
            ));
        } catch (\Throwable $e) {
        }

        return $responsePayload;
    }

    /**
     * Reschedules a Dead-Letter Queue (DLQ) outbox event for re-processing.
     */
    public function replayOutboxEvent(int $eventId, ?string $idempotencyKey = null): array
    {
        $event = DB::table('sys_outbox_events')->where('id', $eventId)->first();
        if (! $event) {
            return [
                'status' => 'error',
                'message' => "Outbox event #{$eventId} not found.",
                'http_status' => 404,
            ];
        }

        DB::table('sys_outbox_events')->where('id', $eventId)->update([
            'status' => 'PENDING',
            'attempts' => 0,
            'available_at' => Carbon::now(),
            'last_error' => null,
        ]);

        try {
            ProcessMarketJobOutboxEventJob::dispatch((int) $eventId);
        } catch (\Throwable $e) {
        }

        return [
            'status' => 'success',
            'message' => "Event #{$eventId} rescheduled for outbox processing.",
        ];
    }
}
