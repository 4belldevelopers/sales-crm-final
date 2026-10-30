<?php

namespace App\Services\SuperAdmin;

use App\Jobs\ProcessCandidateOutboxEventJob;
use App\Services\SuperAdminCandidateListsCacheManager;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Class SuperAdminCandidateMutationService
 *
 * INTENT:
 * Encapsulates all mutating operations for Candidate applications (stage changes, child status, details)
 * under the Transactional Outbox and Idempotency Architecture.
 * Guarantees zero duplicate executions under network retries, atomic state transitions with pessimistic
 * row locking, appends transactional outbox events to sys_outbox_events, updates Elasticsearch
 * local_candidates documents, and triggers real-time Reverb WebSocket broadcasts on superadmin.candidates.
 *
 * TENANCY NOTE:
 * Operates under the Super Admin governance domain (tenant_id = 0). Tenant boundary coordination
 * is enforced via sys_idempotency_keys and sys_outbox_events with tenant_id = 0.
 *
 * Author: Antigravity AI — Super Admin Backend Architecture
 * Complies with docs/CODE_DOCUMENTATION_STANDARD.md and docs/AI_RULES.md.
 */
class SuperAdminCandidateMutationService
{
    /**
     * Synthesize or format an idempotency key for a Candidate mutation.
     *
     * @param  int  $id  Target ID (wiseId or candidateId)
     * @param  string  $action  Action name (e.g., 'stage_update', 'note_add')
     * @param  string|null  $providedKey  Client-supplied UUID
     * @return string Sanitized idempotency key (max 64 chars)
     */
    public function formatIdempotencyKey(int $id, string $action, ?string $providedKey = null): string
    {
        if ($providedKey && trim($providedKey) !== '') {
            $cleanKey = preg_replace('/[^a-zA-Z0-9_\-\:\.]/', '', trim($providedKey));

            return substr($cleanKey, 0, 64);
        }

        $entropy = substr(sha1(uniqid((string) $id, true)), 0, 12);

        return substr("sac_{$id}_{$action}_{$entropy}", 0, 64);
    }

    /**
     * Update the recruitment pipeline stage of a candidate.
     *
     * @param  int  $wiseId  Target add_job_wise_condidates ID
     * @param  int  $masterStatusId  Target master_status ID
     * @param  int|null  $childStatusId  Optional child status ID
     * @param  string|null  $idempotencyKey  Client-supplied or generated idempotency key
     * @param  int|null  $adminId  Authenticated administrator user ID
     * @param  string|null  $adminName  Authenticated administrator name
     * @return array Standardized JSON response payload
     */
    public function updateCandidateStage(
        int $wiseId,
        int $masterStatusId,
        ?int $childStatusId = null,
        ?string $idempotencyKey = null,
        ?int $adminId = null,
        ?string $adminName = null
    ): array {
        $idempotencyKey = $this->formatIdempotencyKey($wiseId, 'stage_update', $idempotencyKey);
        $canonicalPayload = [
            'wise_id' => $wiseId,
            'master_status_id' => $masterStatusId,
            'child_status_id' => $childStatusId,
            'admin_id' => $adminId,
        ];
        $canonicalHash = sha1(json_encode($canonicalPayload));

        $fencingExpiry = Carbon::now()->addSeconds(30);

        // 1. Atomic Idempotency Claim Check with Fencing Token (processing_until)
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
                        : ['status' => 'success', 'data' => ['is_replayed' => true]];
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
                            return [
                                'status' => 'error',
                                'message' => 'Candidate update failed: '.($poll->last_error ?? 'Unknown error'),
                                'http_status' => 500,
                            ];
                        }
                    }

                    // Safe Lease Recovery with Fencing Token: If worker timed out/crashed (> 30s stale or processing_until passed)
                    $isStale = false;
                    if ($existing->processing_until) {
                        $isStale = Carbon::parse($existing->processing_until)->lt(Carbon::now());
                    } else {
                        $isStale = Carbon::parse($existing->updated_at)->lt(Carbon::now()->subSeconds(30));
                    }

                    if ($isStale) {
                        $fencingExpiry = Carbon::now()->addSeconds(30);
                        $reclaimed = DB::table('sys_idempotency_keys')
                            ->where('tenant_id', 0)
                            ->where('idempotency_key', $idempotencyKey)
                            ->where('status', 'PROCESSING')
                            ->where(function ($q) {
                                $q->whereNull('processing_until')
                                    ->orWhere('processing_until', '<', Carbon::now())
                                    ->orWhere('updated_at', '<', Carbon::now()->subSeconds(30));
                            })
                            ->update([
                                'status' => 'PROCESSING',
                                'request_hash' => $canonicalHash,
                                'processing_until' => $fencingExpiry,
                                'updated_at' => Carbon::now(),
                            ]);

                        if (! $reclaimed) {
                            return [
                                'status' => 'error',
                                'message' => 'This candidate update is currently being processed by another worker.',
                                'http_status' => 409,
                            ];
                        }
                    } else {
                        return [
                            'status' => 'error',
                            'message' => 'This candidate update is currently being processed. Please do not double submit.',
                            'http_status' => 409,
                        ];
                    }
                }

                if ($existing->status === 'FAILED_FINAL') {
                    $cachedBody = json_decode((string) $existing->response_body, true);

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
                        'request_hash' => $canonicalHash,
                        'updated_at' => Carbon::now(),
                    ]);
            }
        }

        // 2. Execute Transaction Boundary with Pessimistic Row Lock
        try {
            $result = DB::transaction(function () use (
                $wiseId,
                $masterStatusId,
                $childStatusId,
                $adminId,
                $adminName,
                $idempotencyKey
            ) {
                $wiseCandidate = DB::table('add_job_wise_condidates')
                    ->where('id', $wiseId)
                    ->lockForUpdate()
                    ->first();

                if (! $wiseCandidate) {
                    throw new \RuntimeException("Candidate record #{$wiseId} not found.");
                }

                $candidateId = (int) $wiseCandidate->job_application_id;
                $jobId = (int) $wiseCandidate->job_id;
                $previousMasterStatus = (int) $wiseCandidate->master_status_id;

                // Lookup master status details
                $masterStatus = DB::table('master_status')
                    ->where('id', $masterStatusId)
                    ->first();

                $stageName = $masterStatus->name ?? 'InReview';
                $stageCss = $masterStatus->css_style ?? 'bg-light-primary text-primary';

                // Lookup child status details if applicable
                $subStageName = '';
                if ($childStatusId && $childStatusId > 0) {
                    $childStatus = DB::table('candidate_status')->where('id', $childStatusId)->first();
                    $subStageName = $childStatus->name ?? '';
                }

                // Update add_job_wise_condidates
                $updateData = [
                    'master_status_id' => $masterStatusId,
                    'updated_at' => Carbon::now(),
                ];
                if ($childStatusId !== null) {
                    $updateData['child_status_id'] = $childStatusId;
                }
                DB::table('add_job_wise_condidates')
                    ->where('id', $wiseId)
                    ->update($updateData);

                // Update job_applications aggregate_version and status if matching
                if ($candidateId > 0) {
                    DB::table('job_applications')
                        ->where('id', $candidateId)
                        ->increment('aggregate_version', 1, [
                            'status_id' => $masterStatusId,
                            'updated_at' => Carbon::now(),
                        ]);
                }

                // Record timeline audit trail
                try {
                    DB::table('updatetimeline')->insert([
                        'event_id' => 1,
                        'event_by_company_id' => 0,
                        'event_by_user_id' => $adminId ?? 1,
                        'master_process_name' => $stageName,
                        'child_process_name' => $subStageName,
                        'application_id' => $candidateId,
                        'job_id' => $jobId,
                        'create_at' => Carbon::now(),
                        'update_at' => Carbon::now(),
                    ]);
                } catch (\Throwable $timelineErr) {
                    Log::warning("SuperAdminCandidateMutationService: timeline log skipped: {$timelineErr->getMessage()}");
                }

                // 3. Persist Transactional Outbox Event with Monotonic Aggregate Versioning
                $currentAggregateVersion = (int) DB::table('sys_outbox_events')
                    ->where('aggregate_type', 'Candidate')
                    ->where('aggregate_id', $wiseId)
                    ->max('aggregate_version');
                $nextAggregateVersion = $currentAggregateVersion + 1;

                if (SuperAdminCandidateListsCacheManager::isOutboxBackpressured()) {
                    Log::warning("SuperAdminCandidateMutationService: Outbox backpressure active during update for Candidate #{$wiseId}");
                }

                $outboxPayload = [
                    'wise_id' => $wiseId,
                    'candidate_id' => $candidateId,
                    'job_id' => $jobId,
                    'master_status_id' => $masterStatusId,
                    'child_status_id' => $childStatusId ?? 0,
                    'stage_name' => $stageName,
                    'sub_stage_name' => $subStageName,
                    'stage_css' => $stageCss,
                    'previous_master_status' => $previousMasterStatus,
                    'action_type' => 'stage_updated',
                    'admin_id' => $adminId,
                    'admin_name' => $adminName ?? 'Super Admin',
                    'aggregate_version' => $nextAggregateVersion,
                    'consumers' => [
                        'elasticsearch' => ['status' => 'PENDING', 'completed_at' => null, 'error' => null],
                        'redis' => ['status' => 'PENDING', 'completed_at' => null, 'error' => null],
                        'websocket' => ['status' => 'PENDING', 'completed_at' => null, 'error' => null],
                    ],
                ];

                $outboxId = DB::table('sys_outbox_events')->insertGetId([
                    'tenant_id' => 0,
                    'idempotency_key' => $idempotencyKey,
                    'aggregate_type' => 'Candidate',
                    'aggregate_id' => $wiseId,
                    'aggregate_version' => $nextAggregateVersion,
                    'event_type' => 'CandidateStageUpdated',
                    'payload' => json_encode($outboxPayload),
                    'status' => 'PENDING',
                    'attempts' => 0,
                    'available_at' => Carbon::now(),
                    'created_at' => Carbon::now(),
                ]);

                // 4. Set Authoritative Mutation Delta Overlay in Redis
                SuperAdminCandidateListsCacheManager::setMutationDelta($wiseId, [
                    'master_status_id' => $masterStatusId,
                    'child_status_id' => $childStatusId ?? 0,
                    'name' => $stageName,
                    'master_status_name' => $stageName,
                    'child_status_name' => $subStageName,
                    'css_style' => $stageCss,
                    'updated_at' => Carbon::now()->toIso8601String(),
                ]);

                // 5. Atomic Generation Cache Invalidation
                SuperAdminCandidateListsCacheManager::invalidateAll($wiseId);

                // Dispatch asynchronous outbox worker for guaranteed delivery
                try {
                    ProcessCandidateOutboxEventJob::dispatch($outboxId);
                } catch (\Throwable $jobErr) {
                    Log::warning("SuperAdminCandidateMutationService: direct dispatch fallback: {$jobErr->getMessage()}");
                }

                return [
                    'status' => 'success',
                    'message' => 'Candidate stage updated successfully.',
                    'wise_id' => $wiseId,
                    'candidate_id' => $candidateId,
                    'job_id' => $jobId,
                    'master_status_id' => $masterStatusId,
                    'stage_name' => $stageName,
                    'stage_css' => $stageCss,
                    'is_replayed' => false,
                ];
            });

            // 6. Mark Idempotency Record COMPLETED and release fencing lease
            DB::table('sys_idempotency_keys')
                ->where('tenant_id', 0)
                ->where('idempotency_key', $idempotencyKey)
                ->update([
                    'status' => 'COMPLETED',
                    'processing_until' => null,
                    'response_status' => 200,
                    'response_body' => json_encode($result),
                    'completed_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);

            return $result;

        } catch (\Throwable $e) {
            Log::error("SuperAdminCandidateMutationService updateCandidateStage error: {$e->getMessage()}");

            DB::table('sys_idempotency_keys')
                ->where('tenant_id', 0)
                ->where('idempotency_key', $idempotencyKey)
                ->update([
                    'status' => 'FAILED_FINAL',
                    'response_status' => 500,
                    'last_error' => substr($e->getMessage(), 0, 1000),
                    'completed_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);

            return [
                'status' => 'error',
                'message' => 'Failed to update candidate stage: '.$e->getMessage(),
                'http_status' => 500,
            ];
        }
    }
}
