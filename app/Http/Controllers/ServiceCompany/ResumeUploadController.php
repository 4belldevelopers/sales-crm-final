<?php

/**
 * Handles pre-signed R2 upload URLs and resume metadata storage for the
 * Service Company candidate module. Returns JSON — consumed by the blade JS.
 *
 * Routes (all under auth + EnsureOneDeviceLogin):
 *   POST service_company/resume/presign   → generate upload URL
 *   POST service_company/resume/store     → store metadata + dispatch ParseResumeJob
 *   GET  service_company/resume/{id}/status → poll parse status
 *   POST service_company/resume/{id}/retry  → re-dispatch failed parse
 */

namespace App\Http\Controllers\ServiceCompany;

use App\Jobs\ParseResumeJob;
use App\Models\CandidateResumeUpload;
use App\Services\R2PresignService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ResumeUploadController extends Controller
{
    private static array $allowedMimes = [
        'application/pdf',
        'application/msword',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
    ];

    private static array $allowedExtensions = ['pdf', 'doc', 'docx'];

    /**
     * Step 1 — Generate a pre-signed PUT URL so the browser uploads directly to R2.
     * Never use application/octet-stream as content_type — always pass the real MIME.
     */
    public function presign(Request $request, R2PresignService $r2): JsonResponse
    {
        $v = Validator::make($request->all(), [
            'filename'     => 'required|string|max:255',
            'content_type' => 'required|string|max:100',
        ]);

        if ($v->fails()) {
            return response()->json(['success' => false, 'message' => $v->errors()->first()], 422);
        }

        $ext = strtolower(pathinfo($request->filename, PATHINFO_EXTENSION));

        if (!in_array($ext, self::$allowedExtensions, true)) {
            return response()->json(['success' => false, 'message' => 'Only PDF, DOC, DOCX files are allowed.'], 422);
        }

        if (!in_array($request->content_type, self::$allowedMimes, true)) {
            return response()->json(['success' => false, 'message' => 'Invalid content type.'], 422);
        }

        try {
            $result = $r2->generateUploadUrl($request->filename, $request->content_type, 900);
            return response()->json(['success' => true, ...$result]);
        } catch (\Throwable $e) {
            Log::error('ResumeUploadController::presign failed', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Could not generate upload URL.'], 500);
        }
    }

    /**
     * Step 2 — Called after the browser finishes the direct R2 PUT.
     * Stores metadata and dispatches ParseResumeJob.
     */
    public function store(Request $request): JsonResponse
    {
        $v = Validator::make($request->all(), [
            'object_key'        => 'required|string|max:500',
            'original_filename' => 'required|string|max:255',
            'mime_type'         => 'required|string|max:100',
            'file_size'         => 'required|integer|min:1|max:10485760', // 10 MB hard cap
            'candidate_id'      => 'nullable|integer',
        ]);

        if ($v->fails()) {
            return response()->json(['success' => false, 'message' => $v->errors()->first()], 422);
        }

        $ext = strtolower(pathinfo($request->original_filename, PATHINFO_EXTENSION));
        if (!in_array($ext, self::$allowedExtensions, true)) {
            return response()->json(['success' => false, 'message' => 'File type not permitted.'], 422);
        }

        try {
            $user   = Auth::user();
            $record = CandidateResumeUpload::create([
                'candidate_id'      => $request->candidate_id ?: null,
                'company_id'        => $user?->company_id,
                'object_key'        => $request->object_key,
                'original_filename' => $request->original_filename,
                'mime_type'         => $request->mime_type,
                'file_size'         => (int) $request->file_size,
                'uploaded_by'       => $user?->id,
                'status'            => 'Queued',
            ]);

            ParseResumeJob::dispatch($record->id)->onQueue('default');

            return response()->json([
                'success'          => true,
                'resume_upload_id' => $record->id,
                'message'          => 'Resume queued for parsing.',
            ]);
        } catch (\Throwable $e) {
            Log::error('ResumeUploadController::store failed', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Could not queue resume.'], 500);
        }
    }

    /**
     * Step 3 — Polling endpoint: returns current parse status + parsed data once complete.
     */
    public function status(int $id): JsonResponse
    {
        $record = CandidateResumeUpload::withoutGlobalScopes()->find($id);

        if (!$record) {
            return response()->json(['success' => false, 'message' => 'Not found.'], 404);
        }

        // Only the owner company can check status
        $user = Auth::user();
        if ($record->company_id && $record->company_id !== $user?->company_id) {
            return response()->json(['success' => false, 'message' => 'Forbidden.'], 403);
        }

        return response()->json([
            'success'     => true,
            'status'      => $record->status,
            'parsed_data' => $record->status === 'Completed' ? $record->parsed_data : null,
            'error_log'   => $record->error_log,
        ]);
    }

    /**
     * Retry — re-dispatches ParseResumeJob without re-uploading the file.
     */
    public function retry(int $id): JsonResponse
    {
        $record = CandidateResumeUpload::withoutGlobalScopes()->find($id);

        if (!$record) {
            return response()->json(['success' => false, 'message' => 'Not found.'], 404);
        }

        $user = Auth::user();
        if ($record->company_id && $record->company_id !== $user?->company_id) {
            return response()->json(['success' => false, 'message' => 'Forbidden.'], 403);
        }

        $record->update(['status' => 'Queued', 'error_log' => null]);
        ParseResumeJob::dispatch($record->id)->onQueue('default');

        return response()->json(['success' => true, 'message' => 'Re-queued for parsing.']);
    }
}
