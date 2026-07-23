<?php

namespace App\Services;

use App\Jobs\ParseResumeJob;
use App\Models\ResumeUpload;
use App\Models\UploadSessionAudit;
use Carbon\Carbon;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use RuntimeException;

/**
 * Phase 2 service: orchestrates the resume upload session lifecycle.
 *
 * Responsibilities:
 *   1. Create (or replay by idempotency-key) — writes row + R2 file
 *   2. Finalize — Uploading -> Uploaded + records upload_completed_at
 *   3. Parse asynchronously — sets Parsing -> Parsed
 *   4. Atomic Link — conditional UPDATE on (status='Parsed' AND candidate_id IS NULL)
 *      so two concurrent submits cannot both create candidates.
 *   5. Replace — old session goes Parsed -> Replaced when a new Linked transition happens.
 *   6. Cancel — owner-initiated cleanup (Uploaded -> Cleaning -> ... -> deleted)
 *
 * Tenant isolation: every public method accepts $companyId explicitly. All
 * DB reads use company_id in the WHERE clause. We deliberately do NOT use
 * a global scope here (see CLAUDE.md BUG-03 / memory note about why blanket
 * scopes break multi-tenant marketplace reads). Caller is responsible for
 * passing the right companyId; the forCompany() helper enforces it on lookup.
 *
 * Backward compatibility:
 *   - The existing service-company uploadresume() flow continues to work
 *     unchanged when sessionUuid is absent.
 *   - The new flow is opt-in: the JS client must send #upload_session_uuid.
 */
class ResumeUploadSession
{
    public const DEFAULT_EXPIRY_HOURS = 24;

    public function __construct(
        private readonly ResumeUploadService $uploads,
        private readonly string $disk = 'r2',
    ) {}

    /**
     * Create a new session, or replay an existing one if Idempotency-Key matches.
     *
     * @return array{session: ResumeUpload, replayed: bool}
     */
    public function createOrReplay(
        UploadedFile|string $file,
        string $originalFilename,
        string $companyName,
        int|string $companyId,
        int $userId,
        ?string $idempotencyKey = null,
        ?int $jobId = null,
        ?int $actorIp = null,
        ?string $userAgent = null,
        ?string $requestId = null,
        int $expiryHours = self::DEFAULT_EXPIRY_HOURS,
    ): array {
        if ($idempotencyKey !== null && $idempotencyKey !== '') {
            $existing = ResumeUpload::query()
                ->where('company_id', $companyId)
                ->where('idempotency_key', $idempotencyKey)
                ->whereNotIn('status', [
                    ResumeUpload::STATUS_CLEANING,
                    ResumeUpload::STATUS_DELETE_FAILED,
                    ResumeUpload::STATUS_EXPIRED,
                    ResumeUpload::STATUS_REDACTED_FOR_GDPR,
                ])
                ->orderByDesc('id')
                ->first();
            if ($existing !== null) {
                UploadSessionAudit::record(
                    sessionUuid: $existing->uuid,
                    companyId: (int) $existing->company_id,
                    action: 'Replay',
                    actorId: $userId,
                    actorIp: $actorIp,
                    userAgent: $userAgent,
                    requestId: $requestId,
                    metadata: ['idempotency_key' => substr((string) $idempotencyKey, 0, 64)],
                );
                return ['session' => $existing, 'replayed' => true];
            }
        }

        // Write to R2 final path. If this throws, we abort before creating a row,
        // so the user can retry without leaving a stuck Created row.
        $uploadResult = $this->uploads->uploadDirect(
            $file,
            $originalFilename,
            $companyName,
            $companyId,
            now(),
        );

        $now = Carbon::now();
        $uuid = (string) Str::uuid();

        $session = DB::transaction(function () use (
            $uuid, $companyId, $userId, $jobId, $idempotencyKey,
            $uploadResult, $originalFilename, $file, $now, $expiryHours,
        ) {
            $storedFilename = basename($uploadResult['path']);
            $row = ResumeUpload::create([
                'uuid' => $uuid,
                'company_id' => $companyId,
                'user_id' => $userId,
                'job_id' => $jobId,
                'original_filename' => $originalFilename,
                'stored_filename' => $storedFilename,
                'r2_path' => $uploadResult['path'],
                'r2_url' => $uploadResult['url'],
                'mime_type' => $file instanceof UploadedFile
                    ? ($file->getClientMimeType() ?: 'application/pdf')
                    : 'application/pdf',
                'file_size_bytes' => $this->resolveSize($file, $uploadResult['path']),
                'idempotency_key' => $idempotencyKey,
                'status' => ResumeUpload::STATUS_UPLOADED,
                'upload_started_at' => $now,
                'upload_completed_at' => $now,
                'expires_at' => $now->copy()->addHours($expiryHours),
            ]);

            UploadSessionAudit::record(
                sessionUuid: $row->uuid,
                companyId: (int) $row->company_id,
                action: 'Created',
                actorId: $userId,
                metadata: ['r2_path' => $row->r2_path],
            );
            UploadSessionAudit::record(
                sessionUuid: $row->uuid,
                companyId: (int) $row->company_id,
                action: 'UploadSuccess',
                actorId: $userId,
                metadata: [
                    'file_size_bytes' => $row->file_size_bytes,
                    'mime_type' => $row->mime_type,
                ],
            );

            return $row;
        });

        // Parse asynchronously. The stub marks the session Parsed quickly; Phase 3
        // will swap this for the real parser client (e.g. the existing internal
        // resume parser). We dispatch onto 'emails' so it's the same queue as the
        // candidates' indexing work.
        ParseResumeJob::dispatch($session->uuid, (int) $session->company_id, $userId)
            ->onConnection('emails');

        return ['session' => $session, 'replayed' => false];
    }

    /**
     * Atomically claim and Link a session to a candidate row.
     *
     * Returns true on success, false on contention (the row was already linked
     * by another submit or has terminal status). Caller decides how to surface
     * the false case (recommended: 409 Conflict).
     */
    public function tryLink(
        string $uuid,
        int $companyId,
        int $candidateId,
        ?int $actorId = null,
        ?string $requestId = null,
    ): bool {
        // Read once for company check + sentinel. The actual write is conditional.
        $row = ResumeUpload::query()
            ->where('uuid', $uuid)
            ->where('company_id', $companyId)
            ->first();
        if ($row === null) {
            return false;
        }

        $now = Carbon::now();

        $updated = DB::table('resume_uploads')
            ->where('uuid', $uuid)
            ->where('company_id', $companyId)
            ->whereIn('status', [
                ResumeUpload::STATUS_PARSED,
                ResumeUpload::STATUS_MANUAL_REVIEW_REQUIRED,
                ResumeUpload::STATUS_ABORTED_BY_SUBMIT,
            ])
            ->whereNull('candidate_id')
            ->update([
                'status' => ResumeUpload::STATUS_LINKED,
                'candidate_id' => $candidateId,
                'linked_at' => $now,
                'updated_at' => $now,
            ]);

        if ($updated === 0) {
            return false;
        }

        UploadSessionAudit::record(
            sessionUuid: $uuid,
            companyId: $companyId,
            action: 'Linked',
            actorId: $actorId,
            requestId: $requestId,
            metadata: ['candidate_id' => $candidateId],
        );

        return true;
    }

    /**
     * Mark a previously Linked session as Replaced by a new session, only if
     * the new session reached Linked. This is what protects against losing
     * the old resume when the user picks a new file in EDIT flow.
     */
    public function markReplacedAfterReplacementLinked(
        string $oldUuid,
        int $companyId,
        string $newUuid,
        ?int $actorId = null,
    ): bool {
        $updated = DB::table('resume_uploads')
            ->where('uuid', $oldUuid)
            ->where('company_id', $companyId)
            ->whereIn('status', [ResumeUpload::STATUS_LINKED])
            ->update([
                'status' => ResumeUpload::STATUS_REPLACED,
                'updated_at' => Carbon::now(),
            ]);
        if ($updated === 0) {
            return false;
        }
        UploadSessionAudit::record(
            sessionUuid: $oldUuid,
            companyId: $companyId,
            action: 'Replaced',
            actorId: $actorId,
            metadata: ['replaced_by' => $newUuid],
        );
        // Update the new session's replaces_session_uuid so the audit graph
        // is reversible.
        DB::table('resume_uploads')
            ->where('uuid', $newUuid)
            ->where('company_id', $companyId)
            ->update(['replaces_session_uuid' => $oldUuid]);
        return true;
    }

    /**
     * User-initiated cancel. Marks the row Cleaning; the nightly GC job takes
     * care of the R2 deletion + row hard delete.
     */
    public function cancel(string $uuid, int $companyId, ?int $actorId = null): bool
    {
        $updated = DB::table('resume_uploads')
            ->where('uuid', $uuid)
            ->where('company_id', $companyId)
            ->whereIn('status', [
                ResumeUpload::STATUS_UPLOADED,
                ResumeUpload::STATUS_PARSED,
                ResumeUpload::STATUS_PARSE_FAILED,
                ResumeUpload::STATUS_MANUAL_REVIEW_REQUIRED,
                ResumeUpload::STATUS_ABORTED_BY_SUBMIT,
                ResumeUpload::STATUS_REPLACED,
            ])
            ->update([
                'status' => ResumeUpload::STATUS_CLEANING,
                'updated_at' => Carbon::now(),
            ]);
        if ($updated === 0) {
            return false;
        }
        UploadSessionAudit::record(
            sessionUuid: $uuid,
            companyId: $companyId,
            action: 'CleaningStart',
            actorId: $actorId,
            metadata: ['cancel' => true],
        );
        // The nightly GC sweep will hard-delete R2 + row for any row in
        // Cleaning (or ActiveReplaced/Replaced/Parsed past expiry).
        // For instant feedback we run a focused per-row cleanup here:
        $session = ResumeUpload::query()
            ->where('uuid', $uuid)
            ->where('company_id', $companyId)
            ->first();
        if ($session !== null) {
            try {
                \Illuminate\Support\Facades\Storage::disk('r2')->delete($session->r2_path);
            } catch (\Throwable $e) {
                report($e);
            }
            DB::table('resume_uploads')->where('id', $session->id)->delete();
        }
        return true;
    }

    /**
     * Returns the active session (Parsed/Parsing/Uploaded/AbortedBySubmit) for
     * the calling user so the form can pre-fill parsed data on a re-open.
     * Returns null if no active session exists.
     */
    public function activeForUser(int $companyId, int $userId): ?ResumeUpload
    {
        return ResumeUpload::query()
            ->where('company_id', $companyId)
            ->where('user_id', $userId)
            ->whereIn('status', [
                ResumeUpload::STATUS_UPLOADED,
                ResumeUpload::STATUS_PARSING,
                ResumeUpload::STATUS_PARSED,
                ResumeUpload::STATUS_ABORTED_BY_SUBMIT,
            ])
            ->where(function ($q) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>', Carbon::now());
            })
            ->orderByDesc('id')
            ->first();
    }

    private function resolveSize(UploadedFile|string $file, string $r2Path): int
    {
        if ($file instanceof UploadedFile) {
            return (int) $file->getSize();
        }
        // Re-read from R2 if file was raw content.
        $size = \Illuminate\Support\Facades\Storage::disk($this->disk)->size($r2Path);
        if ($size === false) {
            throw new RuntimeException("Cannot resolve size for {$r2Path}");
        }
        return (int) $size;
    }
}
