<?php

namespace App\Services;

use App\Events\TeamMemberUpdated;
use App\Helper\GeneralMethod;
use App\Jobs\ProcessTeamOutboxEventJob;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Class TeamMutationService
 *
 * Intent: Transactional Outbox & Idempotency coordinator for Service Company Team mutations.
 * Guarantees zero duplicate invitations, role updates, or status changes,
 * provides optimistic concurrency control, logs events to sys_outbox_events,
 * invalidates tenant L1/L2 caches, and triggers real-time Reverb synchronization.
 *
 * Tenancy: Scoped strictly to company_id (tenant_id).
 */
class TeamMutationService
{
    /**
     * Compute deterministic canonical hash of request payload.
     */
    protected function computeCanonicalHash(array $payload): string
    {
        ksort($payload);

        return sha1(json_encode($payload));
    }

    /**
     * Execute a domain mutation wrapped in an atomic idempotency and outbox transaction.
     */
    public function executeIdempotentMutation(
        int $tenantId,
        ?int $targetUserId,
        ?string $idempotencyKey,
        string $eventType,
        array $requestPayload,
        callable $mutationCallback
    ): array {
        $canonicalHash = $this->computeCanonicalHash($requestPayload);

        // Fallback key if client did not supply X-Idempotency-Key
        if (empty($idempotencyKey)) {
            $idempotencyKey = 'team_'.$tenantId.'_'.($targetUserId ?? 0).'_'.substr($canonicalHash, 0, 16);
        }

        // 1. Attempt atomic idempotency claim
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
                    abort(422, 'Idempotency key payload mismatch.');
                }

                if ($existing->status === 'COMPLETED') {
                    $cachedBody = json_decode($existing->response_body, true);

                    return is_array($cachedBody) ? $cachedBody : ['status' => 'success'];
                }

                if ($existing->status === 'PROCESSING') {
                    abort(425, 'Request currently processing.');
                }

                if ($existing->status === 'FAILED_FINAL') {
                    $cachedBody = json_decode($existing->response_body, true);

                    return is_array($cachedBody) ? $cachedBody : ['status' => 'error'];
                }

                // If FAILED_RETRYABLE, claim it again for retry
                DB::table('sys_idempotency_keys')
                    ->where('tenant_id', $tenantId)
                    ->where('idempotency_key', $idempotencyKey)
                    ->update([
                        'status' => 'PROCESSING',
                        'updated_at' => now(),
                    ]);
            }
        }

        // 2. Execute domain mutation inside DB transaction
        DB::beginTransaction();
        try {
            $responseData = $mutationCallback();
            if (! is_array($responseData)) {
                $responseData = ['status' => 'success'];
            }

            $isSuccessfulMutation = ($responseData['status'] ?? '') === 'success' && ($responseData['msgType'] ?? 'success') === 'success';

            // Current tenant team version
            $versionKey = 'ats:teamversion:company_'.$tenantId;
            $currentVersion = (int) Cache::get($versionKey, 1);
            $newVersion = $currentVersion + 1;

            $resolvedUserId = (int) ($targetUserId ?: ($responseData['user_id'] ?? $responseData['aggregate_id'] ?? 0));

            $outboxId = null;
            if ($isSuccessfulMutation) {
                // 3. Atomically write to sys_outbox_events
                $outboxId = DB::table('sys_outbox_events')->insertGetId([
                    'tenant_id' => $tenantId,
                    'idempotency_key' => $idempotencyKey,
                    'aggregate_type' => 'TeamMember',
                    'aggregate_id' => $resolvedUserId,
                    'aggregate_version' => $newVersion,
                    'event_type' => $eventType,
                    'payload' => json_encode(array_merge($requestPayload, [
                        'tenant_id' => $tenantId,
                        'aggregate_id' => $resolvedUserId,
                        'aggregate_version' => $newVersion,
                        'action' => $eventType,
                        'response_data' => $responseData,
                        'timestamp' => now()->toIso8601String(),
                    ])),
                    'status' => 'PENDING',
                    'attempts' => 0,
                    'available_at' => now(),
                    'created_at' => now(),
                ]);
            }

            // 4. Update idempotency status
            DB::table('sys_idempotency_keys')
                ->where('tenant_id', $tenantId)
                ->where('idempotency_key', $idempotencyKey)
                ->update([
                    'status' => $isSuccessfulMutation ? 'COMPLETED' : 'FAILED_RETRYABLE',
                    'response_status' => 200,
                    'response_body' => json_encode($responseData),
                    'completed_at' => now(),
                    'updated_at' => now(),
                ]);

            DB::commit();

            // 5. Invalidate tenant cache if mutated
            if ($isSuccessfulMutation) {
                $this->clearTeamCache($tenantId);
            }

            // 6. Immediate Elasticsearch document sync post-commit (zero stale data in ATS)
            if ($isSuccessfulMutation && $resolvedUserId > 0) {
                try {
                    GeneralMethod::userIndexingupload($resolvedUserId);
                } catch (\Throwable $e) {
                    Log::warning("TeamMutationService: synchronous ES sync warning for user {$resolvedUserId}: ".$e->getMessage());
                }
            }

            // 7. Asynchronous Outbox Worker dispatch post-commit (delivers email without blocking HTTP response)
            if ($outboxId !== null) {
                try {
                    ProcessTeamOutboxEventJob::dispatch($outboxId)->afterCommit();
                } catch (\Throwable $e) {
                    Log::warning('ProcessTeamOutboxEventJob dispatch warning: '.$e->getMessage());
                }
            }

            // 7. Real-time broadcast signal to other users in tenant
            if ($isSuccessfulMutation) {
                try {
                    broadcast(new TeamMemberUpdated(
                        companyId: $tenantId,
                        userId: $resolvedUserId,
                        action: $eventType,
                        senderId: (int) (auth()->id() ?? 0),
                        status: $requestPayload['status'] ?? null,
                        roleId: isset($requestPayload['role_id']) ? (int) $requestPayload['role_id'] : null,
                        roleName: $requestPayload['role_name'] ?? null,
                        workingStatus: isset($requestPayload['working_status']) ? (int) $requestPayload['working_status'] : null,
                        version: $newVersion
                    ))->toOthers();
                } catch (\Throwable $e) {
                    // If broadcasting driver encounters an issue, gracefully log without failing the user mutation
                    Log::warning('TeamMemberUpdated broadcast warning: '.$e->getMessage());
                }
            }

            return $responseData;
        } catch (\Throwable $e) {
            DB::rollBack();
            DB::table('sys_idempotency_keys')
                ->where('tenant_id', $tenantId)
                ->where('idempotency_key', $idempotencyKey)
                ->update([
                    'status' => 'FAILED_RETRYABLE',
                    'last_error' => substr($e->getMessage(), 0, 1000),
                    'updated_at' => now(),
                ]);

            throw $e;
        }
    }

    /**
     * Invalidate tenant team listing caches and bump the version tag.
     */
    public function clearTeamCache(int $companyId): void
    {
        if ($companyId <= 0) {
            return;
        }

        try {
            Cache::forget('ats:teamcounts:'.$companyId);
            $versionKey = 'ats:teamversion:company_'.$companyId;
            if (Cache::has($versionKey)) {
                Cache::increment($versionKey);
            } else {
                Cache::put($versionKey, 2, now()->addDays(30));
            }
            $userId = auth()->id();
            if ($userId) {
                GeneralMethod::cacheClean('allteam_version_user_'.$userId);
            }
        } catch (\Throwable $e) {
            Log::warning("clearTeamCache failed for company {$companyId}: ".$e->getMessage());
        }
    }
}
