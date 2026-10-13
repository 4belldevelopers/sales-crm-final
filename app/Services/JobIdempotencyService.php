<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Class JobIdempotencyService
 *
 * INTENT:
 * Implements the Two-Tier Idempotency Architecture (sys_idempotency_keys + Redis)
 * as specified in the JERO God-Level Hiring Team Engine (v7.2 Diamond Standard).
 *
 * GUARANTEES:
 * 1. Absolute Transactional Authority in MySQL (sys_idempotency_keys).
 * 2. Fail-Open Redis Resiliency: Redis downtime or timeout never prevents job mutations (TC-20, TC-26).
 * 3. Payload Hash Defense (P0 Invariant): Key reuse with modified payload rejected with 409 Conflict (TC-09, TC-31).
 * 4. Replay Protection: Duplicate submission with identical hash returns cached original result (TC-08).
 * 5. Concurrent Race Safety: Handles concurrent identical claims gracefully (TC-32).
 * 6. Structured Observability Tracing for audits and metrics (Section 2.8).
 *
 * TENANCY NOTE:
 * Strictly tenant-scoped by tenant_id (company_id) and idempotency_key.
 */
class JobIdempotencyService
{
    /**
     * Generate canonical SHA-256 hash of the request payload.
     *
     * Normalizes field names, trims strings, sorts arrays to ensure identical
     * semantic payloads produce identical hashes regardless of key ordering.
     */
    public function computeRequestHash(array $payload): string
    {
        $canonical = $this->canonicalizePayload($payload);

        return hash('sha256', json_encode($canonical, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
    }

    /**
     * Canonicalize payload for deterministic hashing.
     */
    protected function canonicalizePayload(array $data): array
    {
        // Strip non-semantic ephemeral parameters (e.g., _token, operation_id)
        unset($data['_token'], $data['operation_id']);

        $normalized = [];
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $normalized[$key] = $this->canonicalizePayload($value);
            } elseif (is_string($value)) {
                $normalized[$key] = trim($value);
            } else {
                $normalized[$key] = $value;
            }
        }

        ksort($normalized);

        return $normalized;
    }

    /**
     * Generate bounded idempotency key (<= 64 chars) for MySQL schema compliance.
     */
    public function formatKey(int $userId, string $operationId): string
    {
        $cleanOp = preg_replace('/[^a-zA-Z0-9_-]/', '', $operationId);
        $prefix = "job:u{$userId}:";
        $maxOpLen = 64 - strlen($prefix);
        if (strlen($cleanOp) > $maxOpLen) {
            $hash = substr(hash('sha256', $cleanOp), 0, 16);
            $cleanOp = substr($cleanOp, 0, $maxOpLen - 17).'_'.$hash;
        }
        $key = $prefix.$cleanOp;
        if (strlen($key) > 64) {
            $key = substr($key, 0, 64);
        }

        return $key;
    }

    /**
     * Attempt to claim or verify an idempotency key.
     *
     * @return array{status: string, code?: int, message?: string, error_code?: string, data?: array, lock?: mixed}
     */
    public function claimKey(int $tenantId, int $userId, string $operationId, array $payload): array
    {
        $idempotencyKey = $this->formatKey($userId, $operationId);
        $requestHash = $this->computeRequestHash($payload);

        // --- TIER 1: MySQL sys_idempotency_keys Absolute Authority Check ---
        try {
            $existing = DB::table('sys_idempotency_keys')
                ->where('tenant_id', $tenantId)
                ->where('idempotency_key', $idempotencyKey)
                ->first();

            if ($existing) {
                // 1. P0 Invariant: Payload Hash Defense
                if ($existing->request_hash !== $requestHash) {
                    Log::warning('[JobIdempotencyService] 409 IDEMPOTENCY_KEY_REUSE detected', [
                        'tenant_id' => $tenantId,
                        'actor_user_id' => $userId,
                        'idempotency_key' => $idempotencyKey,
                        'stored_hash' => $existing->request_hash,
                        'incoming_hash' => $requestHash,
                    ]);

                    return [
                        'status' => 'conflict',
                        'code' => 409,
                        'error_code' => 'IDEMPOTENCY_KEY_REUSE',
                        'message' => 'Idempotency key reuse with modified payload detected.',
                    ];
                }

                // 2. Replay Protection: Cached Success
                if ($existing->status === 'COMPLETED') {
                    $cachedBody = json_decode($existing->response_body, true) ?? [
                        'status' => 'success',
                        'message' => 'Job created successfully (cached response).',
                    ];

                    return [
                        'status' => 'cached',
                        'code' => (int) ($existing->response_status ?: 200),
                        'data' => $cachedBody,
                    ];
                }

                // 3. In-Progress Check with Stale Lock Recovery
                if ($existing->status === 'PROCESSING') {
                    $expiresAt = Carbon::parse($existing->expires_at);
                    if ($expiresAt->isFuture()) {
                        return [
                            'status' => 'conflict',
                            'code' => 409,
                            'error_code' => 'OPERATION_IN_PROGRESS',
                            'message' => 'This operation is currently being processed. Please wait.',
                        ];
                    }

                    // Stale processing lock expired; reclaim it
                    $redisLock = $this->acquireRedisLock($tenantId, $idempotencyKey);
                    DB::table('sys_idempotency_keys')
                        ->where('id', $existing->id)
                        ->update([
                            'status' => 'PROCESSING',
                            'expires_at' => Carbon::now()->addMinutes(5),
                            'updated_at' => Carbon::now(),
                        ]);

                    return [
                        'status' => 'proceed',
                        'idempotency_key' => $idempotencyKey,
                        'request_hash' => $requestHash,
                        'lock' => $redisLock ?: null,
                    ];
                } else {
                    // Retryable failed state; reactivate to PROCESSING
                    $redisLock = $this->acquireRedisLock($tenantId, $idempotencyKey);
                    DB::table('sys_idempotency_keys')
                        ->where('id', $existing->id)
                        ->update([
                            'status' => 'PROCESSING',
                            'expires_at' => Carbon::now()->addMinutes(5),
                            'updated_at' => Carbon::now(),
                        ]);

                    return [
                        'status' => 'proceed',
                        'idempotency_key' => $idempotencyKey,
                        'request_hash' => $requestHash,
                        'lock' => $redisLock ?: null,
                    ];
                }
            }

            // --- TIER 2: New Operation: Acquire Redis Lock & Insert in MySQL ---
            $redisLock = $this->acquireRedisLock($tenantId, $idempotencyKey);
            if ($redisLock === false) {
                return [
                    'status' => 'conflict',
                    'code' => 409,
                    'error_code' => 'OPERATION_IN_PROGRESS',
                    'message' => 'This operation is currently being processed. Please wait.',
                ];
            }

            try {
                DB::table('sys_idempotency_keys')->insert([
                    'tenant_id' => $tenantId,
                    'idempotency_key' => $idempotencyKey,
                    'request_hash' => $requestHash,
                    'status' => 'PROCESSING',
                    'expires_at' => Carbon::now()->addMinutes(5),
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);
            } catch (\Illuminate\Database\QueryException $qe) {
                if ($redisLock) {
                    try {
                        $redisLock->release();
                    } catch (\Throwable $re) {
                    }
                }
                $winner = DB::table('sys_idempotency_keys')
                    ->where('tenant_id', $tenantId)
                    ->where('idempotency_key', $idempotencyKey)
                    ->first();

                if ($winner) {
                    if ($winner->request_hash !== $requestHash) {
                        return [
                            'status' => 'conflict',
                            'code' => 409,
                            'error_code' => 'IDEMPOTENCY_KEY_REUSE',
                            'message' => 'Idempotency key reuse with modified payload detected.',
                        ];
                    }
                    if ($winner->status === 'COMPLETED') {
                        $cachedBody = json_decode($winner->response_body, true) ?? ['status' => 'success'];

                        return [
                            'status' => 'cached',
                            'code' => (int) ($winner->response_status ?: 200),
                            'data' => $cachedBody,
                        ];
                    }
                }

                return [
                    'status' => 'conflict',
                    'code' => 409,
                    'error_code' => 'CONCURRENT_MUTATION_RACE',
                    'message' => 'Duplicate concurrent request detected.',
                ];
            }

            return [
                'status' => 'proceed',
                'idempotency_key' => $idempotencyKey,
                'request_hash' => $requestHash,
                'lock' => $redisLock ?: null,
            ];
        } catch (\Throwable $e) {
            throw $e;
        }
    }

    /**
     * Helper to acquire Redis acceleration lock (Fail-Open Resiliency).
     * Returns Lock instance on success, null on Redis failure (fail-open), or false if contested.
     */
    protected function acquireRedisLock(int $tenantId, string $idempotencyKey)
    {
        try {
            $lockKey = "idemp:lock:{$tenantId}:{$idempotencyKey}";
            $lock = Cache::lock($lockKey, 30);
            if (! $lock->get()) {
                return false;
            }

            return $lock;
        } catch (\Throwable $e) {
            Log::notice('[JobIdempotencyService] Redis lock unavailable, failing open to MySQL authority: '.$e->getMessage());

            return null;
        }
    }

    /**
     * Mark operation as COMPLETED with cached response body.
     */
    public function markCompleted(int $tenantId, string $idempotencyKey, int $statusCode, array $responseBody, $redisLock = null): void
    {
        try {
            DB::table('sys_idempotency_keys')
                ->where('tenant_id', $tenantId)
                ->where('idempotency_key', $idempotencyKey)
                ->update([
                    'status' => 'COMPLETED',
                    'response_status' => $statusCode,
                    'response_body' => json_encode($responseBody, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
                    'completed_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);
        } catch (\Throwable $e) {
            Log::error('[JobIdempotencyService] Failed to mark idempotency key COMPLETED: '.$e->getMessage());
        } finally {
            if ($redisLock) {
                try {
                    $redisLock->release();
                } catch (\Throwable $re) {
                }
            }
        }
    }

    /**
     * Mark operation as FAILED_RETRYABLE or FAILED_FINAL.
     */
    public function markFailed(int $tenantId, string $idempotencyKey, string $errorMessage, bool $retryable = true, $redisLock = null): void
    {
        try {
            DB::table('sys_idempotency_keys')
                ->where('tenant_id', $tenantId)
                ->where('idempotency_key', $idempotencyKey)
                ->update([
                    'status' => $retryable ? 'FAILED_RETRYABLE' : 'FAILED_FINAL',
                    'last_error' => $errorMessage,
                    'updated_at' => Carbon::now(),
                ]);
        } catch (\Throwable $e) {
            Log::error('[JobIdempotencyService] Failed to mark idempotency key FAILED: '.$e->getMessage());
        } finally {
            if ($redisLock) {
                try {
                    $redisLock->release();
                } catch (\Throwable $re) {
                }
            }
        }
    }

    /**
     * Emit structured observability trace log (Section 2.8).
     */
    public function logOperationTrace(array $trace): void
    {
        $logPayload = [
            'operation_id' => $trace['operation_id'] ?? null,
            'tenant_id' => (int) ($trace['tenant_id'] ?? 0),
            'actor_user_id' => (int) ($trace['actor_user_id'] ?? 0),
            'job_id' => (int) ($trace['job_id'] ?? 0),
            'request_hash' => $trace['request_hash'] ?? null,
            'idempotency_status' => $trace['idempotency_status'] ?? 'COMPLETED',
            'execution_duration_ms' => round((float) ($trace['execution_duration_ms'] ?? 0), 2),
            'db_transaction_ms' => round((float) ($trace['db_transaction_ms'] ?? 0), 2),
            'redis_acceleration_ms' => round((float) ($trace['redis_acceleration_ms'] ?? 0), 2),
            'es_projection_status' => $trace['es_projection_status'] ?? 'QUEUED',
        ];

        Log::info('[JERO_OPERATION_TRACE] '.json_encode($logPayload, JSON_UNESCAPED_SLASHES));
    }

    /**
     * Alias for claimKey for expressive testing and interface compatibility.
     */
    public function claim(int $tenantId, int $userId, string $operationId, array $payload): array
    {
        $res = $this->claimKey($tenantId, $userId, $operationId, $payload);
        $status = 'CLAIMED';
        if ($res['status'] === 'conflict') {
            $status = 'CONFLICT';
        } elseif ($res['status'] === 'cached') {
            $status = 'REPLAY';
        }

        return [
            'status' => $status,
            'is_replay' => ($res['status'] === 'cached'),
            'is_conflict' => ($res['status'] === 'conflict'),
            'status_code' => $res['code'] ?? 200,
            'response' => $res['data'] ?? (isset($res['error_code']) ? ['code' => $res['error_code'], 'message' => $res['message']] : []),
            'lock' => $res['lock'] ?? null,
        ];
    }

    /**
     * Alias for markCompleted for expressive testing and interface compatibility.
     */
    public function complete(int $tenantId, int $userId, string $operationId, array $payload, array $response, int $statusCode = 200): bool
    {
        $idempotencyKey = $this->formatKey($userId, $operationId);
        $this->markCompleted($tenantId, $idempotencyKey, $statusCode, $response);

        return true;
    }
}
