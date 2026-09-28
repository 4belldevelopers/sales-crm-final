<?php

namespace App\Services;

use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

/**
 * Class CandidateMutationService
 *
 * Intent: Transactionally Coordinated Idempotency State Machine and Optimistic Concurrency Control (OCC)
 * engine per docs/drawer_candidate.md.
 */
class CandidateMutationService
{
    /**
     * Compute canonical hash (keys sorted lexicographically, unescaped slashes & unicode).
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

    /**
     * Execute transactional mutation with atomic idempotency claiming, OCC aggregate_version, and outbox event.
     */
    public function mutateCandidate(
        int $tenantId,
        int $candidateId,
        string $idempotencyKey,
        string $eventType,
        array $requestPayload,
        int $expectedVersion,
        callable $mutationCallback
    ): JsonResponse {
        $canonicalHash = $this->computeCanonicalHash($requestPayload);

        return DB::transaction(function () use (
            $tenantId,
            $candidateId,
            $idempotencyKey,
            $canonicalHash,
            $eventType,
            $requestPayload,
            $expectedVersion,
            $mutationCallback
        ) {
            // 1. Attempt atomic claim
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
                    ->lockForUpdate()
                    ->first();

                if (! $existing) {
                    abort(500, 'Idempotency record race condition.');
                }

                if ($existing->request_hash !== $canonicalHash) {
                    abort(422, 'Idempotency key payload mismatch.');
                }

                if ($existing->status === 'COMPLETED') {
                    $body = json_decode($existing->response_body, true);

                    return response()->json($body, (int) $existing->response_status);
                }

                if ($existing->status === 'PROCESSING') {
                    abort(425, 'Request currently processing.');
                }

                if ($existing->status === 'FAILED_FINAL') {
                    $body = json_decode($existing->response_body, true);

                    return response()->json($body, (int) $existing->response_status);
                }

                // FAILED_RETRYABLE falls through to attempt re-execution
            }

            try {
                // 2. Execute mutation & OCC version bump
                $newVersion = $expectedVersion + 1;

                // Pre-check / execute OCC update
                $affected = DB::table('job_applications')
                    ->where('id', $candidateId)
                    ->where('company_id', $tenantId)
                    ->where('aggregate_version', $expectedVersion)
                    ->update([
                        'aggregate_version' => $newVersion,
                        'updated_at' => now(),
                    ]);

                if ($affected === 0) {
                    // OCC conflict is a deterministic application failure: FAILED_FINAL
                    $conflictPayload = [
                        'status' => 'conflict',
                        'message' => 'Candidate modified by another user.',
                        'expected_version' => $expectedVersion,
                    ];

                    DB::table('sys_idempotency_keys')
                        ->where('tenant_id', $tenantId)
                        ->where('idempotency_key', $idempotencyKey)
                        ->update([
                            'status' => 'FAILED_FINAL',
                            'response_status' => 409,
                            'response_body' => json_encode($conflictPayload),
                            'completed_at' => now(),
                            'updated_at' => now(),
                        ]);

                    return response()->json($conflictPayload, 409);
                }

                // Execute the domain mutation callback
                $responseData = $mutationCallback($candidateId, $tenantId, $newVersion);
                if (! is_array($responseData)) {
                    $responseData = [
                        'status' => 'success',
                        'candidate_id' => $candidateId,
                        'aggregate_version' => $newVersion,
                    ];
                }
                $responseData['aggregate_version'] = $newVersion;

                // 3. Insert Outbox Event
                DB::table('sys_outbox_events')->insert([
                    'tenant_id' => $tenantId,
                    'idempotency_key' => $idempotencyKey,
                    'aggregate_type' => 'Candidate',
                    'aggregate_id' => $candidateId,
                    'aggregate_version' => $newVersion,
                    'event_type' => $eventType,
                    'payload' => json_encode(array_merge($requestPayload, ['aggregate_version' => $newVersion])),
                    'status' => 'PENDING',
                    'attempts' => 0,
                    'available_at' => now(),
                    'created_at' => now(),
                ]);

                // 4. Mark Idempotency Record COMPLETED
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

                // Update in-memory projection with new version
                CandidateDrawerReadService::recordCandidateProjection([
                    'id' => $candidateId,
                    'company_id' => $tenantId,
                    'aggregate_version' => $newVersion,
                ]);

                return response()->json($responseData, 200);

            } catch (\Throwable $e) {
                $statusCode = ($e instanceof HttpResponseException) ? $e->getResponse()->getStatusCode() : 0;
                if (! in_array($statusCode, [409, 422])) {
                    // Infrastructure or unexpected failure: mark FAILED_RETRYABLE
                    DB::table('sys_idempotency_keys')
                        ->where('tenant_id', $tenantId)
                        ->where('idempotency_key', $idempotencyKey)
                        ->update([
                            'status' => 'FAILED_RETRYABLE',
                            'last_error' => substr($e->getMessage(), 0, 1000),
                            'updated_at' => now(),
                        ]);
                }

                throw $e;
            }
        });
    }
}
