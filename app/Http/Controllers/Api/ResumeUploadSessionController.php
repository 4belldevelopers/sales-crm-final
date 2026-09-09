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

    private function fail(string $message, int $code = 400, array $extra = []): JsonResponse
    {
        return response()->json(array_merge([
            'success' => false,
            'message' => $message,
        ], $extra), $code);
    }
}
