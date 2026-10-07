<?php
namespace App\Services;

use App\Enums\ResumeUploadStatus;
use App\Models\ResumeUpload;
use App\Models\UploadSessionAudit;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Intent: Orchestrates the resume upload session lifecycle.
 * Every v2 endpoint delegates here. Stateless — all state lives in MySQL.
 *
 * Invariants enforced:
 * - INV-1: new file pick → new key (enforced by JS) → new session here
 * - INV-4: same key + company → replays the existing non-terminal session
 *
 * Tenancy: company_id and user_id MUST come from Auth::user(), never from
 * request body (CLAUDE.md rule 3).
 */
class ResumeUploadSession
{
    /**
     * Create a new upload session OR replay an existing one for the same
     * Idempotency-Key header (transport retry safety — INV-4).
     *
     * The browser generates a fresh key per presign request. Two file picks
     * → two keys → two sessions, even for identical bytes. A network retry
     * of the same fetch() carries the same key → same session returned.
     *
     * @throws \InvalidArgumentException for malformed Idempotency-Key
     */
    public function createOrPresign(
        int     $companyId,
        int     $userId,
        string  $idempotencyKey,
        string  $originalFilename,
        int     $fileSizeBytes,
        string  $mimeType,
        string  $clientIp,
        ?string $sha256Hex = null,
    ): array {
        // Validate: UUID (36 chars with dashes) or hex string (32–64 chars)
        if (!preg_match('/^[a-f0-9\-]{36,64}$/i', $idempotencyKey)) {
            throw new \InvalidArgumentException('Invalid Idempotency-Key format');
        }

        // INV-4 — replay lookup (non-unique index; last non-terminal match wins)
        $existing = ResumeUpload::forCompany($companyId)
            ->where('idempotency_key', $idempotencyKey)
            ->nonTerminal()
            ->orderByDesc('id')
            ->first();

        if ($existing) {
            UploadSessionAudit::record($existing->id, 'ActionSessionReplayed', [
                'previous_created_at' => $existing->created_at->toIso8601String(),
            ], $existing->company_id, $existing->user_id);

            return $this->buildResponse(
                $existing,
                $this->generatePresignedUrl($existing->uuid),
                replayed: true,
            );
        }

        // Create new session row
        $uuid      = (string) Str::uuid();
        $expiresAt = now()->addSeconds(config('resumes.session.ttl', 1800));

        try {
            $session = ResumeUpload::create([
                'uuid'              => $uuid,
                'company_id'        => $companyId,
                'user_id'           => $userId,
                'original_filename' => $originalFilename,
                'file_size_bytes'   => $fileSizeBytes,
                'mime_type'         => $mimeType,
                'status'            => ResumeUploadStatus::Pending->value,
                'idempotency_key'   => $idempotencyKey,
                'expires_at'        => $expiresAt,
                'sha256_hex'        => $sha256Hex,
            ]);
        } catch (\Illuminate\Database\QueryException $e) {
            // Issue 7: Race window — two concurrent retries both found no row.
            // Re-run the lookup; the winner is now visible in the index.
            if (str_starts_with((string) $e->getCode(), '23')) {
                $session = ResumeUpload::forCompany($companyId)
                    ->where('idempotency_key', $idempotencyKey)
                    ->nonTerminal()
                    ->orderByDesc('id')
                    ->firstOrFail();

                return $this->buildResponse(
                    $session,
                    $this->generatePresignedUrl($session->uuid),
                    replayed: true,
                );
            }
            throw $e;
        }

        UploadSessionAudit::record($session->id, 'ActionSessionCreated', [
            'ip_hash' => hash('sha256', $clientIp . now()->format('Y-m-d')),
        ], $companyId, $userId);

        return $this->buildResponse(
            $session,
            $this->generatePresignedUrl($uuid),
            replayed: false,
        );
    }

    /**
     * Mark a session as Uploaded after the browser confirms R2 PUT completed.
     * Idempotent — calling twice on an already-Uploaded session is a safe no-op.
     *
     * R2 HEAD check runs BEFORE the DB conditional-UPDATE so the network call
     * never holds a DB lock open (Issue 5 fix).
     *
     * @throws \RuntimeException (code 404) if session not found for this tenant
     */
    public function finalize(string $uuid, int $companyId): array
    {
        $session = ResumeUpload::byUuid($uuid)->forCompany($companyId)->first();
        if (!$session) {
            throw new \RuntimeException('Session not found', 404);
        }

        // Issue 5: preflight outside any transaction — no DB lock during network I/O
        $r2Exists = $this->headCheck($uuid);

        if (!$r2Exists) {
            Log::warning('jobs.resume.finalize_r2_missing', [
                'uuid'       => $uuid,
                'company_id' => $companyId,
                'status'     => $session->status,
            ]);
        }

        // Conditional UPDATE — second finalize call finds 0 rows → idempotent no-op
        $updated = DB::table('resume_uploads')
            ->where('uuid', $uuid)
            ->whereIn('status', [
                ResumeUploadStatus::Pending->value,
                ResumeUploadStatus::Uploading->value,
            ])
            ->update([
                'status'              => ResumeUploadStatus::Uploaded->value,
                'upload_completed_at' => now(),
                'updated_at'          => now(),
            ]);

        $action  = $updated === 0 ? 'ActionFinalizeIdempotent' : 'ActionUploadCompleted';
        UploadSessionAudit::record($session->id, $action, [
            'previous_status' => $session->status,
            'r2_verified'     => $r2Exists,
        ], $companyId);

        return [
            'uuid'                => $session->uuid,
            'status'              => ResumeUploadStatus::Uploaded->value,
            'r2_verified'         => $r2Exists,
            'linked_candidate_id' => $session->linked_candidate_id,
        ];
    }

    /**
     * Read session state. Returns null when not found or wrong tenant.
     */
    public function get(string $uuid, int $companyId): ?ResumeUpload
    {
        return ResumeUpload::byUuid($uuid)->forCompany($companyId)->first();
    }

    /**
     * Cancel an in-progress upload. Idempotent — double-cancel is a no-op.
     *
     * @throws \RuntimeException (code 404) if session not found for this tenant
     */
    public function cancel(string $uuid, int $companyId): void
    {
        $session = ResumeUpload::byUuid($uuid)->forCompany($companyId)->first();
        if (!$session) {
            throw new \RuntimeException('Session not found', 404);
        }

        $updated = DB::table('resume_uploads')
            ->where('uuid', $uuid)
            ->whereNotIn('status', [
                ResumeUploadStatus::Cancelled->value,
                ResumeUploadStatus::Reconciled->value,
                ResumeUploadStatus::Expired->value,
                ResumeUploadStatus::Completed->value,
            ])
            ->update([
                'status'     => ResumeUploadStatus::Cancelled->value,
                'updated_at' => now(),
            ]);

        $action = $updated === 0 ? 'ActionCancelIdempotent' : 'ActionCancelled';
        UploadSessionAudit::record($session->id, $action, [
            'previous_status' => $session->status,
        ], $companyId);

        // Best-effort R2 cleanup — non-critical; 30-day lifecycle rule also cleans up
        try {
            Storage::disk('r2')->delete('resumes/' . $uuid);
        } catch (\Throwable $e) {
            Log::warning('jobs.resume.r2_delete_failed', [
                'uuid'  => $uuid,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Move a v2 resume from the temp presign path (resumes/{uuid}) to the
     * company-structured CDN path that v1 uses:
     *   Companies/{slug}_{id}/Resumes/{YYYY}/{MM}/{original_filename}
     *
     * Returns the public file.jero.works CDN URL on success.
     * Fail-open: on any R2 error, logs and returns a CDN URL for the temp path
     * so the form submission still records a usable URL.
     *
     * Call this AFTER the R2 HEAD check confirms the file exists at the temp path.
     */
    public function moveToCompanyPath(ResumeUpload $session): string
    {
        $companyName = DB::table('companies')
            ->where('id', $session->company_id)
            ->value('company_name');
        $slug = strtolower(trim($companyName ?? 'unknown'));
        $slug = preg_replace('/[^a-z0-9_-]/i', '_', $slug);
        $companyFolder = $slug . '_' . $session->company_id;

        $year     = now()->format('Y');
        $month    = now()->format('m');
        $srcPath  = 'resumes/' . $session->uuid;

        // Intent: Prevent R2 object key collisions when candidates upload files with identical original names.
        $origName = $session->original_filename ?? 'resume.pdf';
        $ext = pathinfo($origName, PATHINFO_EXTENSION) ?: 'pdf';
        $baseName = pathinfo($origName, PATHINFO_FILENAME);
        $safeBaseName = preg_replace('/[^a-zA-Z0-9_\-]/', '_', $baseName);
        $candidatePrefix = $session->linked_candidate_id ?? $session->candidate_id ?? '0';
        $safeFilename = "{$candidatePrefix}_{$safeBaseName}_{$session->uuid}.{$ext}";
        $destPath = "Companies/{$companyFolder}/Resumes/{$year}/{$month}/{$safeFilename}";

        try {
            Storage::disk('r2')->copy($srcPath, $destPath);
            Storage::disk('r2')->delete($srcPath);   // best-effort; lifecycle rule handles orphans
        } catch (\Throwable $e) {
            Log::warning('resume.move_company_path_failed', [
                'uuid'  => $session->uuid,
                'dest'  => $destPath,
                'error' => $e->getMessage(),
            ]);
            return 'https://file.jero.works/' . $srcPath;
        }

        return 'https://file.jero.works/' . $destPath;
    }

    // ─── Private helpers ──────────────────────────────────────────────────────

    /**
     * Generate a presigned PUT URL for direct browser-to-R2 upload.
     * Uses Flysystem S3 temporaryUploadUrl (requires flysystem-aws-s3-v3 ≥3.10).
     */
    private function generatePresignedUrl(string $uuid): string
    {
        $disk   = Storage::disk('r2');
        $path   = 'resumes/' . $uuid;
        $ttl    = config('resumes.r2.presign_ttl', 600);
        // temporaryUploadUrl returns ['url' => ..., 'headers' => ...] in Flysystem 3.x
        $result = $disk->temporaryUploadUrl($path, now()->addSeconds($ttl));

        return is_array($result) ? $result['url'] : $result;
    }

    /** HEAD check against R2 — returns false on any error (fail-open). */
    private function headCheck(string $uuid): bool
    {
        try {
            return Storage::disk('r2')->exists('resumes/' . $uuid);
        } catch (\Throwable) {
            return false;
        }
    }

    private function buildResponse(ResumeUpload $session, string $presignedUrl, bool $replayed): array
    {
        return [
            'uuid'              => $session->uuid,
            'status'            => $session->status,
            'upload_url'        => $presignedUrl,
            'expires_at'        => $session->expires_at?->toIso8601String(),
            'original_filename' => $session->original_filename,
            'file_size_bytes'   => $session->file_size_bytes,
            'replayed'          => $replayed,
        ];
    }
}
