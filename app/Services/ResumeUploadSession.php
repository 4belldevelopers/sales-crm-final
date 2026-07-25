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
        string $resourceType = ResumeUpload::RESOURCE_TYPE_JOB_APPLICATION,
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
            $uploadResult, $originalFilename, $file, $now, $expiryHours, $resourceType,
        ) {
            $storedFilename = basename($uploadResult['path']);
            $row = ResumeUpload::create([
                'uuid' => $uuid,
                'company_id' => $companyId,
                'user_id' => $userId,
                'job_id' => $jobId,
                'resource_type' => $resourceType,
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
                metadata: ['r2_path' => $row->r2_path, 'resource_type' => $resourceType],
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
            ->where('resource_type', ResumeUpload::RESOURCE_TYPE_JOB_APPLICATION)
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
            metadata: ['candidate_id' => $candidateId, 'resource_type' => 'job_application'],
        );

        return true;
    }

    /**
     * Same Link semantics as tryLink() but the session was created with
     * resource_type='bench_application', so we write into bench_application_id
     * instead of candidate_id. Used by the bench manager flow.
     */
    public function tryLinkBench(
        string $uuid,
        int $companyId,
        int $benchApplicationId,
        ?int $actorId = null,
        ?string $requestId = null,
    ): bool {
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
            ->where('resource_type', ResumeUpload::RESOURCE_TYPE_BENCH_APPLICATION)
            ->whereIn('status', [
                ResumeUpload::STATUS_PARSED,
                ResumeUpload::STATUS_MANUAL_REVIEW_REQUIRED,
                ResumeUpload::STATUS_ABORTED_BY_SUBMIT,
            ])
            ->whereNull('bench_application_id')
            ->update([
                'status' => ResumeUpload::STATUS_LINKED,
                'bench_application_id' => $benchApplicationId,
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
            metadata: ['bench_application_id' => $benchApplicationId, 'resource_type' => 'bench_application'],
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

    /**
     * Phase 4 — create a session that will receive bytes via a presigned PUT
     * (browser → R2 directly), instead of routing them through Laravel.
     *
     * Returns:
     *   - session: ResumeUpload row in status=Created with the canonical
     *     R2 path reserved; no bytes are written.
     *   - presigned: {url, headers[], ttl_seconds, expires_at, r2_path, method}
     *     Compiled from ResumeUploadService::presignPut() and the same
     *     FilesystemManager that backs our reads.
     *   - replayed: true if the idempotency-key matched an existing live
     *     session; the response carries a NEW presigned URL — the browser
     *     re-PUTs to the same R2 path, which is a no-op or overwrites
     *     piecewise-partial bytes depending on R2 idempotency semantics.
     *     We rely on the verify-at-finalize step to detect a partial upload.
     *
     * Failure modes:
     *   - Missing S3/R2 adapter → RuntimeException from presignPut().
     *     Caller (controller) maps to 422 and falls back to createOrReplay()
     *     (the legacy multipart write).
     *
     * @return array{session: ResumeUpload, replayed: bool, presigned: array}
     */
    public function createOrPresign(
        string $originalFilename,
        string $companyName,
        int|string $companyId,
        int $userId,
        ?string $mimeType = null,
        ?int $approxSizeBytes = null,
        ?string $idempotencyKey = null,
        ?int $jobId = null,
        ?int $actorIp = null,
        ?string $userAgent = null,
        ?string $requestId = null,
        int $expiryHours = self::DEFAULT_EXPIRY_HOURS,
        string $resourceType = ResumeUpload::RESOURCE_TYPE_JOB_APPLICATION,
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
                    metadata: ['idempotency_key' => substr((string) $idempotencyKey, 0, 64), 'path' => 'presigned'],
                );
                // Mint a fresh presigned URL for the same path so the
                // browser can re-PUT for a stale-session retry.
                $presigned = $this->uploads->presignPut(
                    r2Path: $existing->r2_path,
                    mimeType: $existing->mime_type,
                    maxBytes: $existing->file_size_bytes > 0 ? (int) $existing->file_size_bytes : null,
                );
                return ['session' => $existing, 'replayed' => true, 'presigned' => $presigned];
            }
        }

        // Reserve a filename + path BEFORE the browser PUTs, so we have a
        // stable target the controller can hand back. uniqueFileName mirrors
        // uploadDirect() so legacy + Phase 4 paths colocate.
        $extension = strtolower((string) pathinfo($originalFilename, PATHINFO_EXTENSION));
        if ($extension === '') {
            $extension = 'bin';
        }
        $finalName = uniqid('JERO_', true) . '.' . $extension;
        $destination = $this->uploads->computeDestination($companyName, $companyId, $finalName);

        // Sign FIRST. signing is a pure compute operation (getCommand +
        // createPresignedRequest) — no S3 side effect. If the adapter is
        // not S3-compatible, this throws RuntimeException before any DB
        // row exists, so we never leak a Created session with no signed URL.
        $presigned = $this->uploads->presignPut(
            r2Path: $destination['path'],
            mimeType: $mimeType ?: 'application/pdf',
            maxBytes: $approxSizeBytes,
        );

        $now = Carbon::now();
        $uuid = (string) Str::uuid();

        $session = DB::transaction(function () use (
            $uuid, $companyId, $userId, $jobId, $idempotencyKey,
            $originalFilename, $finalName, $destination, $mimeType, $approxSizeBytes,
            $now, $expiryHours, $resourceType,
        ) {
            $row = ResumeUpload::create([
                'uuid' => $uuid,
                'company_id' => $companyId,
                'user_id' => $userId,
                'job_id' => $jobId,
                'resource_type' => $resourceType,
                'original_filename' => $originalFilename,
                'stored_filename' => $finalName,
                'r2_path' => $destination['path'],
                'r2_url' => $destination['url'],
                'mime_type' => $mimeType ?: 'application/pdf',
                'file_size_bytes' => (int) ($approxSizeBytes ?? 0),
                'idempotency_key' => $idempotencyKey,
                'status' => ResumeUpload::STATUS_CREATED,
                'upload_started_at' => $now,
                'expires_at' => $now->copy()->addHours($expiryHours),
            ]);
            UploadSessionAudit::record(
                sessionUuid: $row->uuid,
                companyId: (int) $row->company_id,
                action: 'Created',
                actorId: $userId,
                metadata: ['r2_path' => $row->r2_path, 'resource_type' => $resourceType, 'path' => 'presigned'],
            );
            return $row;
        });

        return ['session' => $session, 'replayed' => false, 'presigned' => $presigned];
    }

    /**
     * Phase 4 — finalize a session whose bytes landed directly on R2.
     *
     * Steps:
     *   1. Re-resolve the session under tenant scope + verify status ∈
     *      {Created, Uploading}. We accept Uploading because a slow client
     *      may retry going through presign twice.
     *   2. Head-check the object on R2 (exists + size matches clientBytes).
     *   3. Transition Created -> Uploading -> Uploaded in two DB writes, with
     *      UploadStart + UploadSuccess audit rows in the same transactions.
     *   4. Record file_size_bytes (server-trusted) and dispatch ParseResumeJob
     *      onto the same queue as createOrReplay().
     *
     * If the session is already Uploaded/Parsing/Parsed (a duplicate finalize),
     * we STILL verify the object so a stale client can't claim a candidate
     * row pointing at a missing R2 file, then return the current state
     * idempotently with ok=true.
     *
     * @return array{ok: bool, reason?: string, session?: ResumeUpload, r2_url?: string, file_size_bytes?: int, replayed?: bool}
     */
    public function finalize(
        string $uuid,
        int $companyId,
        int $userId,
        ?int $clientBytes = null,
        ?string $idempotencyKey = null,
        ?int $actorIp = null,
        ?string $userAgent = null,
        ?string $requestId = null,
    ): array {
        $session = ResumeUpload::query()
            ->where('uuid', $uuid)
            ->where('company_id', $companyId)
            ->first();
        if ($session === null) {
            return ['ok' => false, 'reason' => 'session_not_found'];
        }

        $verify = $this->uploads->verifyObjectForPresign($session->r2_path, $clientBytes);
        if (! ($verify['ok'] ?? false)) {
            UploadSessionAudit::record(
                sessionUuid: $session->uuid,
                companyId: (int) $session->company_id,
                action: 'UploadFailed',
                actorId: $userId,
                actorIp: $actorIp,
                userAgent: $userAgent,
                requestId: $requestId,
                metadata: [
                    'reason' => $verify['reason'] ?? 'unknown',
                    'r2_path' => $session->r2_path,
                    'client_bytes' => $clientBytes,
                ],
            );
            return [
                'ok' => false,
                'reason' => $verify['reason'] ?? 'verify_failed',
                'session' => $session,
            ];
        }

        // Idempotent finalize: if the session already advanced past Uploaded,
        // just return its current state. The parser job was already queued.
        // Phase 8 — QueuedForParsing is the post-finalize state for the
        // standard happy path; a second finalize() observes this and
        // returns the same replay shape without re-dispatching.
        if (in_array($session->status, [
            ResumeUpload::STATUS_UPLOADED,
            ResumeUpload::STATUS_QUEUED_FOR_PARSING,
            ResumeUpload::STATUS_PARSING,
            ResumeUpload::STATUS_PARSED,
            ResumeUpload::STATUS_LINKED,
            ResumeUpload::STATUS_COMPLETED,
        ], true)) {
            return [
                'ok' => true,
                'replayed' => true,
                'session' => $session,
                'r2_url' => $session->r2_url,
                'file_size_bytes' => (int) $session->file_size_bytes,
            ];
        }

        $now = Carbon::now();
        $serverSize = (int) ($verify['size'] ?? $clientBytes ?? 0);

        // Created -> Uploading: hard-write; the transition map permits it and
        // the conditional WHERE both prevents a second finalize from clobbering
        // and gates on the lateral move Uploading -> Uploaded.
        DB::transaction(function () use ($session, $now, $userId, $actorIp, $userAgent, $requestId) {
            DB::table('resume_uploads')
                ->where('id', $session->id)
                ->where('status', ResumeUpload::STATUS_CREATED)
                ->update(['status' => ResumeUpload::STATUS_UPLOADING, 'updated_at' => $now]);
            UploadSessionAudit::record(
                sessionUuid: $session->uuid,
                companyId: (int) $session->company_id,
                action: 'UploadStart',
                actorId: $userId,
                actorIp: $actorIp,
                userAgent: $userAgent,
                requestId: $requestId,
                metadata: ['r2_path' => $session->r2_path, 'path' => 'presigned'],
            );
        });

        DB::transaction(function () use ($session, $now, $serverSize, $verify, $userId, $actorIp, $userAgent, $requestId) {
            DB::table('resume_uploads')
                ->where('id', $session->id)
                ->where('status', ResumeUpload::STATUS_UPLOADING)
                ->update([
                    'status' => ResumeUpload::STATUS_UPLOADED,
                    'upload_completed_at' => $now,
                    'file_size_bytes' => $serverSize,
                    'updated_at' => $now,
                ]);
            UploadSessionAudit::record(
                sessionUuid: $session->uuid,
                companyId: (int) $session->company_id,
                action: 'UploadSuccess',
                actorId: $userId,
                actorIp: $actorIp,
                userAgent: $userAgent,
                requestId: $requestId,
                metadata: [
                    'file_size_bytes' => $serverSize,
                    'mime_type' => $session->mime_type,
                    'path' => 'presigned',
                    'verify_size' => (int) ($verify['size'] ?? 0),
                ],
            );
        });

        // Refresh the in-memory model + atomically transition Uploaded
        // to QueuedForParsing + dispatch on the dedicated parse
        // queue (Phase 8). The transitionTo() call is the same
        // shape the Linked/Reconciled/Repair paths already use:
        // row update + audit row commit in one DB transaction.
        // The dispatch seam does NOT rely on this for correctness —
        // a second finalize() observes AlreadyQueued in the row state
        // and returns the idempotent replay path above — but it
        // makes the in-flight state observable from the DB during
        // bursts.
        $session->refresh();
        if ($session->status === ResumeUpload::STATUS_UPLOADED) {
            $session->transitionTo(
                ResumeUpload::STATUS_QUEUED_FOR_PARSING,
                action: 'Queued',
                actorId: $userId,
                actorIp: $actorIp,
                userAgent: $userAgent,
                requestId: $requestId,
                metadata: [
                    'queue' => (string) config('resumes.parser_queue', 'parse_resumes'),
                    'connection' => (string) config('resumes.parser_connection', 'redis_parse_resumes'),
                ],
            );
        }
        ParseResumeJob::dispatch($session->uuid, (int) $session->company_id, $userId)
            ->onConnection((string) config('resumes.parser_connection', 'redis_parse_resumes'))
            ->onQueue((string) config('resumes.parser_queue', 'parse_resumes'));

        return [
            'ok' => true,
            'replayed' => false,
            'session' => $session,
            'r2_url' => $session->r2_url,
            'file_size_bytes' => $serverSize,
        ];
    }
}
