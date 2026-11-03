<?php

namespace App\Services;

use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;

/**
 * RegistrationIdempotencyHandler
 *
 * Implements INV-08 & INV-16: Canonical Request-Hash Idempotency Protocol (IDEMPOTENCY-01).
 *
 * Enforces database-level idempotency keyed to (tenant_id, invitation_id, idempotency_key).
 * Replays identical requests deterministically, rejects altered payloads with 409,
 * and handles in-progress concurrency gracefully via non-blocking 409 + Retry-After.
 */
class RegistrationIdempotencyHandler
{
    /**
     * Executes the callable action within an idempotent safety envelope.
     *
     * @param  callable(): JsonResponse  $action
     */
    public function handle(Request $request, int $tenantId, int $invitationId, callable $action): JsonResponse
    {
        $idempotencyKey = $request->header('Idempotency-Key') ?? $request->input('idempotency_key');
        if (empty($idempotencyKey)) {
            return response()->json([
                'error' => 'MISSING_IDEMPOTENCY_KEY',
                'message' => 'The Idempotency-Key header is mandatory for this request.',
            ], 400);
        }

        // Canonical payload hash (excluding volatile CSRF tokens and confirmation fields)
        $canonicalPayload = json_encode($request->except(['_token', 'password_confirmation', 'idempotency_key']), JSON_UNESCAPED_SLASHES);
        $requestHash = hash('sha256', $canonicalPayload);
        $endpoint = $request->path();

        try {
            // Step 1: Claim execution slot atomically
            $recordId = DB::table('registration_idempotency_keys')->insertGetId([
                'tenant_id' => $tenantId,
                'invitation_id' => $invitationId,
                'idempotency_key' => (string) $idempotencyKey,
                'endpoint' => $endpoint,
                'request_hash' => $requestHash,
                'status' => 'IN_PROGRESS',
                'created_at' => now(),
                'expires_at' => now()->addHours(24),
            ]);
        } catch (QueryException $e) {
            // MySQL error code 23000 = Unique constraint violation (1062 Duplicate entry)
            if ($e->getCode() == 23000) {
                return $this->handleExistingKey($tenantId, $invitationId, (string) $idempotencyKey, $requestHash);
            }
            throw $e;
        }

        // Step 2: Execute transactional payload
        try {
            /** @var JsonResponse $response */
            $response = $action();

            $statusCode = $response->getStatusCode();
            $responseData = $response->getData(true);

            DB::table('registration_idempotency_keys')
                ->where('id', $recordId)
                ->update([
                    'status' => 'COMPLETED',
                    'response_code' => $statusCode,
                    'response_body' => json_encode($responseData),
                    'updated_at' => now(),
                ]);

            return $response;
        } catch (Throwable $e) {
            DB::table('registration_idempotency_keys')
                ->where('id', $recordId)
                ->update([
                    'status' => 'FAILED',
                    'response_code' => 500,
                    'response_body' => json_encode(['error' => 'INTERNAL_SERVER_ERROR', 'message' => $e->getMessage()]),
                    'updated_at' => now(),
                ]);
            throw $e;
        }
    }

    /**
     * Resolves an existing idempotency record against the incoming request.
     */
    private function handleExistingKey(int $tenantId, int $invitationId, string $key, string $requestHash): JsonResponse
    {
        $existing = DB::table('registration_idempotency_keys')
            ->where('tenant_id', $tenantId)
            ->where('invitation_id', $invitationId)
            ->where('idempotency_key', $key)
            ->first();

        if (! $existing) {
            return response()->json([
                'error' => 'IDEMPOTENCY_RACE_CONDITION',
                'message' => 'Idempotency key state could not be resolved.',
            ], 409);
        }

        // Rule 1: Key reused with an altered payload
        if ($existing->request_hash !== $requestHash) {
            return response()->json([
                'error' => 'IDEMPOTENCY_KEY_REUSED_WITH_DIFFERENT_REQUEST',
                'message' => 'The provided Idempotency-Key was previously used with a different request payload.',
            ], 409);
        }

        // Rule 2: Request is still being processed by a concurrent thread/worker
        if ($existing->status === 'IN_PROGRESS') {
            return response()->json([
                'error' => 'REQUEST_IN_PROGRESS',
                'message' => 'A request with this Idempotency-Key is currently being processed. Please retry shortly.',
            ], 409, ['Retry-After' => '2']);
        }

        // Rule 3: Deterministic cached replay
        if ($existing->status === 'COMPLETED') {
            $body = json_decode((string) $existing->response_body, true) ?: [];

            return response()->json(
                $body,
                $existing->response_code ?: 200,
                ['X-Idempotent-Replay' => 'true']
            );
        }

        // Rule 4: Previous failure replay
        $body = json_decode((string) $existing->response_body, true) ?: [];

        return response()->json(
            $body,
            $existing->response_code ?: 500,
            ['X-Idempotent-Failed' => 'true']
        );
    }
}
