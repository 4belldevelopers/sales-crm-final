<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ResumeUploadSession;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use RuntimeException;

/**
 * Phase 2 JSON API for the resume upload session lifecycle.
 *
 * Endpoints:
 *   POST   /api/resume-sessions               create or replay by Idempotency-Key
 *   GET    /api/resume-sessions/active        current user's active session (for recovery)
 *   GET    /api/resume-sessions/{uuid}        fetch by uuid (tenant-scoped)
 *   POST   /api/resume-sessions/{uuid}/cancel user-initiated cleanup
 *
 * Backward compat: existing /uploadresume (multipart) flow continues to work
 * unchanged for clients that don't send Idempotency-Key.
 *
 * Tenant isolation: every read goes through {@see ResumeUploadSession::activeForUser()}
 * which filters by both company_id AND user_id, AND every write is
 * company_id-filtered; we never expose a row from another tenant.
 *
 * Idempotency:
 *   Same Idempotency-Key from the same company reroutes to an existing active
 *   session (sets the audit action Replay). This protects against
 *   React Strict Mode double-effects, network retries, and idempotent submitter
 *   plugins from creating duplicate R2 files.
 */
class ResumeUploadSessionController extends Controller
{
    public function __construct(
        private readonly ResumeUploadSession $sessions,
    ) {}

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'file' => 'required|file|mimes:pdf,doc,docx,rtf|max:6048',
            'job_id' => 'nullable|integer|min:1',
        ], [
            'file.mimes' => 'Only PDF, DOC and DOCX are accepted.',
            'file.max' => 'Resume must be 6 MB or smaller.',
        ]);

        $user = $request->user();
        $companyId = (int) ($user->company_id ?? 0);
        if ($companyId === 0) {
            return $this->fail('No company context', 403);
        }

        $idempotencyKey = $request->header('Idempotency-Key');

        /** @var \Illuminate\Http\UploadedFile $file */
        $file = $request->file('file');
        try {
            $result = $this->sessions->createOrReplay(
                file: $file,
                originalFilename: $file->getClientOriginalName(),
                companyName: (string) ($user->company->company_name ?? 'unknown'),
                companyId: (string) $companyId,
                userId: (int) $user->id,
                idempotencyKey: $idempotencyKey,
                jobId: $request->input('job_id'),
                actorIp: $request->ip(),
                userAgent: $request->userAgent(),
                requestId: $request->header('X-Request-Id'),
            );
        } catch (RuntimeException $e) {
            return $this->fail($e->getMessage(), 422, ['code' => 'upload_failed']);
        }

        $session = $result['session'];
        return response()->json([
            'session_uuid' => $session->uuid,
            'r2_url' => $session->r2_url,
            'expires_at' => $session->expires_at?->toIso8601String(),
            'status' => $session->status,
            'parsed_data' => $session->parsed_data_json,
            'replayed' => $result['replayed'],
        ], $result['replayed'] ? 200 : 201);
    }

    public function active(Request $request): JsonResponse
    {
        $user = $request->user();
        $companyId = (int) ($user->company_id ?? 0);
        if ($companyId === 0) {
            return $this->fail('No company context', 403);
        }
        $session = $this->sessions->activeForUser($companyId, (int) $user->id);
        if ($session === null) {
            return response()->json(['session' => null], 200);
        }
        return response()->json([
            'session' => [
                'session_uuid' => $session->uuid,
                'r2_url' => $session->r2_url,
                'expires_at' => $session->expires_at?->toIso8601String(),
                'status' => $session->status,
                'parsed_data' => $session->parsed_data_json,
            ],
        ], 200);
    }

    public function show(Request $request, string $uuid): JsonResponse
    {
        $user = $request->user();
        $companyId = (int) ($user->company_id ?? 0);
        if ($companyId === 0) {
            return $this->fail('No company context', 403);
        }
        $session = \App\Models\ResumeUpload::forCompany($uuid, $companyId);

        // Phase 9.2 — the localStorage session cache can rehydrate a stale
        // session UUID long after the server has expired it. If we returned
        // 200 for an expired row, the verifier would report linked=false,
        // and the submit-side recovery modal would prompt "Retry is safe"
        // for a session that GC has already reaped. That re-submits the
        // candidate POST and creates a duplicate row.
        //
        // Map both "past expires_at" and stale terminal states to 404.
        // forCompany() already normalizes wrong-tenant to 404 too, so the
        // verifier cannot tell expiry from cross-tenant — the right shape
        // for "you cannot recover this session".
        if ($session->isExpired()) {
            return $this->fail('Session not found', 404, ['code' => 'session_not_found']);
        }

        // Phase 9.1 — network-recovery verifier needs to know whether
        // a candidate was actually persisted server-side. The session
        // row carries candidate_id (for job_applications) or
        // bench_application_id (for bench) once the controller's
        // commit-successful branch fires. Expose BOTH so the submit-
        // side recovery modal can answer "was this submission
        // received?" without guessing — false-negatives here would
        // let recruiters re-submit and produce duplicate candidates.
        return response()->json([
            'session' => [
                'session_uuid' => $session->uuid,
                'r2_url' => $session->r2_url,
                'expires_at' => $session->expires_at?->toIso8601String(),
                'status' => $session->status,
                'parsed_data' => $session->parsed_data_json,
                // Recovery-relevant booleans.
                'linked' => (bool) ($session->candidate_id !== null || $session->bench_application_id !== null),
                'candidate_id' => $session->candidate_id !== null ? (int) $session->candidate_id : null,
                'bench_application_id' => $session->bench_application_id !== null ? (int) $session->bench_application_id : null,
            ],
        ], 200);
    }

    public function cancel(Request $request, string $uuid): JsonResponse
    {
        $user = $request->user();
        $companyId = (int) ($user->company_id ?? 0);
        if ($companyId === 0) {
            return $this->fail('No company context', 403);
        }
        $ok = $this->sessions->cancel($uuid, $companyId, (int) $user->id);
        return response()->json(['cancelled' => $ok], $ok ? 200 : 409);
    }

    /**
     * Phase 4 — issue a presigned PUT URL so the browser uploads bytes
     * directly to R2, bypassing PHP. The browser then calls finalize()
     * after the PUT resolves to flip the session to Uploaded + dispatch
     * the parser.
     *
     * Payload:
     *   - original_filename (string, required)
     *   - mime_type (string, optional but recommended)
     *   - size_bytes (int, optional; honored as R2 Content-Length)
     *   - job_id (int, optional)
     *
     * Headers:
     *   - Idempotency-Key (optional; replay returns the same session)
     */
    public function presign(Request $request): JsonResponse
    {
        $request->validate([
            'original_filename' => 'required|string|max:255',
            'mime_type' => 'nullable|string|max:127',
            'size_bytes' => 'nullable|integer|min:0|max:6048',
            'job_id' => 'nullable|integer|min:1',
            'resource_type' => 'nullable|in:job_application,bench_application',
        ]);

        $user = $request->user();
        $companyId = (int) ($user->company_id ?? 0);
        if ($companyId === 0) {
            return $this->fail('No company context', 403);
        }

        $idempotencyKey = $request->header('Idempotency-Key');
        try {
            $result = $this->sessions->createOrPresign(
                originalFilename: (string) $request->input('original_filename'),
                companyName: (string) ($user->company->company_name ?? 'unknown'),
                companyId: (string) $companyId,
                userId: (int) $user->id,
                mimeType: $request->input('mime_type'),
                approxSizeBytes: $request->input('size_bytes') !== null ? (int) $request->input('size_bytes') : null,
                idempotencyKey: $idempotencyKey,
                jobId: $request->input('job_id'),
                actorIp: $request->ip(),
                userAgent: $request->userAgent(),
                requestId: $request->header('X-Request-Id'),
                resourceType: (string) $request->input('resource_type', 'job_application'),
            );
        } catch (RuntimeException $e) {
            // The Phase 4 contract: a missing S3 adapter is a typed error.
            // Surface 422 + a stable code so the JS client can fall back to
            // the legacy /uploadresume multipart endpoint.
            return $this->fail($e->getMessage(), 422, ['code' => 'presign_unavailable']);
        }

        $session = $result['session'];
        $presigned = $result['presigned'];
        return response()->json([
            'session_uuid' => $session->uuid,
            'r2_path' => $presigned['r2_path'] ?? $session->r2_path,
            'r2_url' => $session->r2_url,
            'upload_url' => $presigned['url'] ?? null,
            'headers' => $presigned['headers'] ?? [],
            'method' => $presigned['method'] ?? 'PUT',
            'ttl_seconds' => $presigned['ttl_seconds'] ?? null,
            'expires_at' => $session->expires_at?->toIso8601String(),
            'status' => $session->status,
            'replayed' => $result['replayed'],
        ], $result['replayed'] ? 200 : 201);
    }

    /**
     * Phase 4 — confirm the presigned PUT landed and flip the session
     * Created -> Uploading -> Uploaded, then queue the parser.
     *
     * Payload:
     *   - client_bytes (int, optional — bytestream length the browser PUT)
     *
     * Response (200 ok):
     *   - session_uuid, status=Uploaded, r2_url, file_size_bytes, ready_to_link=true
     *
     * Response (422):
     *   - reason: 'missing' | 'empty' | 'size_mismatch' | 'verify_failed'
     *   The JS client surfaces this as an error chip and asks the user
     *   to retry.
     */
    public function finalize(Request $request, string $uuid): JsonResponse
    {
        $request->validate([
            'client_bytes' => 'nullable|integer|min:0',
        ]);

        $user = $request->user();
        $companyId = (int) ($user->company_id ?? 0);
        if ($companyId === 0) {
            return $this->fail('No company context', 403);
        }

        $r = $this->sessions->finalize(
            uuid: $uuid,
            companyId: $companyId,
            userId: (int) $user->id,
            clientBytes: $request->input('client_bytes') !== null ? (int) $request->input('client_bytes') : null,
            idempotencyKey: $request->header('Idempotency-Key'),
            actorIp: $request->ip(),
            userAgent: $request->userAgent(),
            requestId: $request->header('X-Request-Id'),
        );
        if (! ($r['ok'] ?? false)) {
            return $this->fail(
                'Resume could not be verified on R2',
                422,
                ['code' => 'finalize_failed', 'reason' => $r['reason'] ?? 'verify_failed'],
            );
        }

        $session = $r['session'];
        return response()->json([
            'session_uuid' => $session->uuid,
            'status' => $session->status,
            'r2_url' => $session->r2_url,
            'file_size_bytes' => (int) ($r['file_size_bytes'] ?? 0),
            'replayed' => (bool) ($r['replayed'] ?? false),
            'ready_to_link' => true,
        ], 200);
    }

    private function fail(string $message, int $code = 400, array $extra = []): JsonResponse
    {
        return response()->json(array_merge([
            'success' => false,
            'message' => $message,
        ], $extra), $code);
    }
}
