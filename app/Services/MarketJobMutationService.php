<?php

namespace App\Services;

use App\Jobs\ProcessMarketJobOutboxEventJob;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * MarketJobMutationService — Authoritative Transactional Outbox & Idempotency Engine.
 *
 * Intent:
 * Phase 4 Backend Hardening for Market Job mutations (claims, acceptances, terms agreements).
 * Guarantees zero duplicate claims under high concurrency via sys_idempotency_keys,
 * atomic database mutations with transactional outbox logging (sys_outbox_events),
 * and decoupled asynchronous side effects.
 *
 * Tenancy:
 * Strictly scoped by tenant_id ($companyId).
 */
class MarketJobMutationService
{
    /**
     * Executes an authoritative Market Job mutation with idempotency protection and transactional outbox.
     *
     * @param  int  $tenantId  Claiming company ID
     * @param  int  $jobId  Target job ID
     * @param  string|null  $idempotencyKey  Unique client-supplied or synthesized key
     * @param  array  $requestPayload  Raw request input for payload hash verification
     * @param  callable  $mutationCallback  Domain logic callback executing MySQL writes
     * @return array Response array containing status and payload
     */
    public function executeClaimMutation(
        int $tenantId,
        int $jobId,
        ?string $idempotencyKey,
        array $requestPayload,
        callable $mutationCallback
    ): array {
        $canonicalHash = $this->computeCanonicalHash($requestPayload);

        // Fallback key if client did not supply idempotency key
        if (empty($idempotencyKey)) {
            $idempotencyKey = 'mkt_'.$tenantId.'_'.$jobId.'_'.substr($canonicalHash, 0, 16);
        }

        // 1. Atomic Idempotency Claim Check
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
                        'message' => 'Idempotency key payload mismatch. A different operation used this key.',
                    ];
                }

                if ($existing->status === 'COMPLETED') {
                    $cachedBody = json_decode($existing->response_body, true);

                    return is_array($cachedBody) ? $cachedBody : ['status' => 'success'];
                }

                if ($existing->status === 'PROCESSING') {
                    return [
                        'status' => 'waiting',
                        'message' => 'This request is already being processed. Please wait.',
                    ];
                }

                if ($existing->status === 'FAILED_FINAL') {
                    $cachedBody = json_decode($existing->response_body, true);

                    return is_array($cachedBody) ? $cachedBody : ['status' => 'error', 'message' => $existing->last_error];
                }
            }
        }

        // 2. Execute domain mutation and outbox record inside DB Transaction
        DB::beginTransaction();
        try {
            $responseData = $mutationCallback();
            if (! is_array($responseData)) {
                $responseData = ['status' => 'success'];
            }

            // Invalidate Redis/Cache version for tenant
            $versionKey = 'tenant:'.$tenantId.':market_jobs:version';
            $currentVersion = (int) Cache::get($versionKey, 1);
            $newVersion = $currentVersion + 1;
            Cache::put($versionKey, $newVersion, now()->addDays(30));

            // 3. Atomically write to sys_outbox_events
            $outboxId = DB::table('sys_outbox_events')->insertGetId([
                'tenant_id' => $tenantId,
                'idempotency_key' => $idempotencyKey,
                'aggregate_type' => 'MarketJob',
                'aggregate_id' => (int) $jobId,
                'aggregate_version' => $newVersion,
                'event_type' => 'MarketJobClaimed',
                'payload' => json_encode(array_merge($requestPayload, [
                    'tenant_id' => $tenantId,
                    'job_id' => $jobId,
                    'aggregate_version' => $newVersion,
                    'response_data' => $responseData,
                    'timestamp' => now()->toIso8601String(),
                ])),
                'status' => 'PENDING',
                'attempts' => 0,
                'available_at' => now(),
                'created_at' => now(),
            ]);

            // 4. Update idempotency status to COMPLETED
            DB::table('sys_idempotency_keys')
                ->where('tenant_id', $tenantId)
                ->where('idempotency_key', $idempotencyKey)
                ->update([
                    'status' => 'COMPLETED',
                    'response_status' => 200,
                    'response_body' => json_encode($responseData),
                    'completed_at' => now(),
                    'updated_at' => now(),
                ]);

            DB::commit();

            // 5. Asynchronously dispatch outbox relay worker
            try {
                ProcessMarketJobOutboxEventJob::dispatch((int) $outboxId);
            } catch (\Throwable $queueEx) {
                Log::warning('[MarketJobMutationService] Direct outbox job dispatch deferred: '.$queueEx->getMessage());
            }

            return $responseData;

        } catch (\Throwable $e) {
            DB::rollBack();

            Log::error('[MarketJobMutationService] Mutation failed: '.$e->getMessage(), [
                'tenant_id' => $tenantId,
                'job_id' => $jobId,
                'trace' => $e->getTraceAsString(),
            ]);

            DB::table('sys_idempotency_keys')
                ->where('tenant_id', $tenantId)
                ->where('idempotency_key', $idempotencyKey)
                ->update([
                    'status' => 'FAILED_FINAL',
                    'response_status' => 500,
                    'response_body' => json_encode(['status' => 'error', 'message' => $e->getMessage()]),
                    'last_error' => $e->getMessage(),
                    'updated_at' => now(),
                ]);

            return [
                'status' => 'error',
                'message' => 'An error occurred while processing your request: '.$e->getMessage(),
            ];
        }
    }

    /**
     * Compute deterministic canonical hash of request parameters.
     */
    public function computeCanonicalHash(array $payload): string
    {
        ksort($payload);
        foreach ($payload as $key => $value) {
            if (is_array($value)) {
                $payload[$key] = $this->sortArrayRecursive($value);
            }
        }

        return hash('sha256', json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
    }

    private function sortArrayRecursive(array $array): array
    {
        ksort($array);
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $array[$key] = $this->sortArrayRecursive($value);
            }
        }

        return $array;
    }
}
