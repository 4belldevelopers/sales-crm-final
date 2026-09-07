<?php

namespace App\Http\Controllers\ServiceCompany;

// Intent: Thin HTTP adapter for the resume upload v2 session lifecycle.
// All business logic lives in ResumeUploadSession service — this class only
// reads auth, headers, and delegates; never implements upload logic itself.
//
// All 4 endpoints are behind `auth` + `EnsureOneDeviceLogin` (inherited from
// the route group). Feature flags gate each method individually so they can
// be toggled without a deploy.
//
// Tenancy: company_id and user_id are read from auth()->user() only — never
// from the request body (CLAUDE.md rule 3).

use App\Http\Requests\StoreResumeSessionRequest;
use App\Services\ResumeUploadSession;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;

class ResumeUploadSessionController extends Controller
{
    public function __construct(private readonly ResumeUploadSession $sessions) {}

    /**
     * POST /service_company/resume/v2/sessions
     * Step 1 — create (or replay) an upload session and return a presigned PUT URL.
     *
     * Header: Idempotency-Key  (UUID or 64-char hex, generated fresh per file pick)
     * Body:   original_filename, file_size_bytes, mime_type, sha256_hex?
     */
    public function store(StoreResumeSessionRequest $request): JsonResponse
    {
        abort_unless(config('resumes.features.presign_endpoint') && \Illuminate\Support\Facades\Schema::hasTable('resume_uploads'), 404);

        $key = $request->header('Idempotency-Key', '');
        if (empty($key)) {
            return response()->json([
                'success' => false,
                'error'   => 'missing_idempotency_key',
                'message' => 'Idempotency-Key header is required.',
            ], 422);
        }

        $user = auth()->user();

        try {
            $result = $this->sessions->createOrPresign(
                companyId:        (int) $user->company_id,
                userId:           (int) $user->id,
                idempotencyKey:   $key,
                originalFilename: $request->input('original_filename'),
                fileSizeBytes:    (int) $request->input('file_size_bytes'),
                mimeType:         $request->input('mime_type'),
                clientIp:         $request->ip(),
                sha256Hex:        $request->input('sha256_hex'),
            );
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'success' => false,
                'error'   => 'invalid_idempotency_key',
                'message' => $e->getMessage(),
            ], 422);
        } catch (\Throwable $e) {
            Log::error('resume.v2.store_failed', [
                'user_id'    => $user->id,
                'company_id' => $user->company_id,
                'error'      => $e->getMessage(),
            ]);
            return response()->json([
                'success' => false,
                'error'   => 'server_error',
                'message' => 'Could not create upload session. Please try again.',
            ], 500);
        }

        $status = $result['replayed'] ? 200 : 201;
        return response()->json(['success' => true, ...$result], $status);
    }

    /**
     * POST /service_company/resume/v2/sessions/{uuid}/finalize
     * Step 2 — called after the browser confirms the R2 PUT completed.
     * Idempotent: calling twice on the same session is safe.
     */
    public function finalize(Request $request, string $uuid): JsonResponse
    {
        abort_unless(config('resumes.features.finalize_endpoint'), 404);

        $user = auth()->user();

        try {
            $result = $this->sessions->finalize($uuid, (int) $user->company_id);
        } catch (\RuntimeException $e) {
            if ($e->getCode() === 404) {
                return response()->json([
                    'success' => false,
                    'error'   => 'not_found',
                    'message' => 'Upload session not found.',
                ], 404);
            }
            Log::error('resume.v2.finalize_failed', [
                'uuid'       => $uuid,
                'user_id'    => $user->id,
                'company_id' => $user->company_id,
                'error'      => $e->getMessage(),
            ]);
            return response()->json([
                'success' => false,
                'error'   => 'server_error',
                'message' => 'Could not finalize upload session.',
            ], 500);
        }

        return response()->json(['success' => true, ...$result]);
    }

    /**
     * GET /service_company/resume/v2/sessions/{uuid}
     * Poll session status — used by the browser during and after upload.
     */
    public function show(Request $request, string $uuid): JsonResponse
    {
        abort_unless(config('resumes.features.presign_endpoint'), 404);

        $user    = auth()->user();
        $session = $this->sessions->get($uuid, (int) $user->company_id);

        if (!$session) {
            return response()->json([
                'success' => false,
                'error'   => 'not_found',
                'message' => 'Upload session not found.',
            ], 404);
        }

        return response()->json([
            'success'             => true,
            'uuid'                => $session->uuid,
            'status'              => $session->status,
            'original_filename'   => $session->original_filename,
            'file_size_bytes'     => $session->file_size_bytes,
            'upload_completed_at' => $session->upload_completed_at?->toIso8601String(),
            'expires_at'          => $session->expires_at?->toIso8601String(),
            'linked_candidate_id' => $session->linked_candidate_id,
        ]);
    }

    /**
     * DELETE /service_company/resume/v2/sessions/{uuid}
     * Cancel an in-progress upload. Idempotent.
     */
    public function cancel(Request $request, string $uuid): JsonResponse
    {
        abort_unless(config('resumes.features.cancel'), 404);

        $user = auth()->user();

        try {
            $this->sessions->cancel($uuid, (int) $user->company_id);
        } catch (\RuntimeException $e) {
            if ($e->getCode() === 404) {
                return response()->json([
                    'success' => false,
                    'error'   => 'not_found',
                    'message' => 'Upload session not found.',
                ], 404);
            }
            return response()->json([
                'success' => false,
                'error'   => 'server_error',
                'message' => 'Could not cancel upload session.',
            ], 500);
        }

        return response()->json(['success' => true, 'message' => 'Upload cancelled.']);
    }
}
