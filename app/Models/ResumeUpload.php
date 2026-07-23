<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;
use RuntimeException;

/**
 * Single source of truth for resume lifecycle state.
 *
 * Backward compatibility: rows in this table may or may not have a matching
 * job_applications entry. Phase 2+ writes both atomically. Legacy rows on
 * job_applications (pre-Phase 1) are linked retroactively via the
 * reconcile command in Phase 5.
 *
 * Tenant isolation follows the project rule (CLAUDE.md BUG-03,
 * memory/tenant-isolation-blanket-scopes-break-marketplace.md): every
 * external query MUST filter by company_id. The package-scoped helper
 * {@see self::tenantScoped()} keeps misuse loud at the call site.
 *
 * State machine — see also the architecture review in
 * ~/.claude/plans/noble-sprouting-river.md (Phase OAuth 2026-07-23 update):
 *
 *   Created ¬─> Uploading ¬─> Uploaded ¬─> Parsing ¬─> Parsed
 *                   │              │            │           │
 *                   ▼              ▼            ▼           ▼
 *             UploadFailed   UploadFailed  ParseFailed  Replaced / AbortedBySubmit
 *                                            │
 *                                            ▼
 *                                       ManualReviewRequired
 *
 *                              Parsed ───Linked───> AbortedBySubmit ──Linked──> Linked──> Completed
 *                                                 (transient)
 *                                                 \-> Replaced
 *
 *                 Linked ──Completed side-effects failed──> AbortedBySubmit (retry)
 *
 *    Parsed | AbortedBySubmit | Replaced  ──GC──>  Cleaning  ¬─┬──> (deleted)
 *                                                              └──> DeleteFailed
 *    Parsed | AbortedBySubmit | Replaced  ──expires_at─> Expired
 *
 * Status transitions are enforced via the explicit {@see self::transitionTo()}
 * method; the audit row is written in the same DB transaction so the trail
 * can never drift from the row state.
 */
class ResumeUpload extends Model
{
    protected $table = 'resume_uploads';

    public const STATUS_CREATED = 'Created';
    public const STATUS_UPLOADING = 'Uploading';
    public const STATUS_UPLOADED = 'Uploaded';
    public const STATUS_UPLOAD_FAILED = 'UploadFailed';
    public const STATUS_PARSING = 'Parsing';
    public const STATUS_PARSED = 'Parsed';
    public const STATUS_PARSE_FAILED = 'ParseFailed';
    public const STATUS_MANUAL_REVIEW_REQUIRED = 'ManualReviewRequired';
    public const STATUS_LINKED = 'Linked';
    public const STATUS_ABORTED_BY_SUBMIT = 'AbortedBySubmit';
    public const STATUS_REPLACED = 'Replaced';
    public const STATUS_COMPLETED = 'Completed';
    public const STATUS_CLEANING = 'Cleaning';
    public const STATUS_DELETE_FAILED = 'DeleteFailed';
    public const STATUS_EXPIRED = 'Expired';
    public const STATUS_REDACTED_FOR_GDPR = 'RedactedForGDPR';

    /**
     * Allowed transitions. Keys are current state, values are arrays of legal
     * next states. Anything outside this map is rejected by transitionTo() so
     * accidental API calls cannot put a session into an unrecoverable state.
     */
    private const TRANSITIONS = [
        self::STATUS_CREATED => [self::STATUS_UPLOADING, self::STATUS_UPLOADED, self::STATUS_UPLOAD_FAILED, self::STATUS_EXPIRED],
        self::STATUS_UPLOADING => [self::STATUS_UPLOADED, self::STATUS_UPLOAD_FAILED, self::STATUS_EXPIRED],
        self::STATUS_UPLOADED => [self::STATUS_PARSING, self::STATUS_PARSE_FAILED],
        self::STATUS_UPLOAD_FAILED => [self::STATUS_CLEANING, self::STATUS_EXPIRED],
        self::STATUS_PARSING => [self::STATUS_PARSED, self::STATUS_PARSE_FAILED, self::STATUS_MANUAL_REVIEW_REQUIRED],
        self::STATUS_PARSED => [self::STATUS_LINKED, self::STATUS_ABORTED_BY_SUBMIT, self::STATUS_REPLACED, self::STATUS_CLEANING, self::STATUS_EXPIRED],
        self::STATUS_PARSE_FAILED => [self::STATUS_MANUAL_REVIEW_REQUIRED, self::STATUS_REPLACED, self::STATUS_CLEANING, self::STATUS_EXPIRED],
        self::STATUS_MANUAL_REVIEW_REQUIRED => [self::STATUS_LINKED, self::STATUS_REPLACED, self::STATUS_CLEANING, self::STATUS_EXPIRED],
        self::STATUS_LINKED => [self::STATUS_COMPLETED, self::STATUS_REPLACED, self::STATUS_ABORTED_BY_SUBMIT],
        self::STATUS_ABORTED_BY_SUBMIT => [self::STATUS_LINKED, self::STATUS_CLEANING, self::STATUS_EXPIRED],
        self::STATUS_REPLACED => [self::STATUS_CLEANING, self::STATUS_EXPIRED],
        self::STATUS_COMPLETED => [self::STATUS_REDACTED_FOR_GDPR],
        self::STATUS_CLEANING => [self::STATUS_EXPIRED, self::STATUS_DELETE_FAILED],
        self::STATUS_DELETE_FAILED => [self::STATUS_CLEANING],
        self::STATUS_EXPIRED => [],
        self::STATUS_REDACTED_FOR_GDPR => [],
    ];

    protected $guarded = ['id'];

    protected $casts = [
        'parsed_data_json' => 'array',
        'file_size_bytes' => 'integer',
        'upload_started_at' => 'datetime',
        'upload_completed_at' => 'datetime',
        'parse_started_at' => 'datetime',
        'parse_completed_at' => 'datetime',
        'linked_at' => 'datetime',
        'expires_at' => 'datetime',
        'parse_attempt_count' => 'integer',
        'cleanup_attempt_count' => 'integer',
    ];

    public function audits(): HasMany
    {
        return $this->hasMany(UploadSessionAudit::class, 'session_uuid', 'uuid')
            ->orderBy('created_at', 'desc');
    }

    /**
     * Returns a query filtered to the tenant. Use this on every read site.
     * Direct DB::table('resume_uploads') calls are anti-pattern; if you need
     * raw SQL (e.g. for the GC batch update), mirror this WHERE clause.
     */
    public static function tenantScoped(int $companyId)
    {
        return self::query()->where('company_id', $companyId);
    }

    /**
     * Returns a session by uuid FOR a given company. Throws if not found OR
     * the row exists but belongs to another company — never allow the lookup
     * to be a cross-tenant signal.
     */
    public static function forCompany(string $uuid, int $companyId): self
    {
        $row = self::query()->where('uuid', $uuid)->first();
        if (! $row) {
            throw new RuntimeException("Session not found: {$uuid}");
        }
        if ((int) $row->company_id !== $companyId) {
            // Don't leak existence to a wrong tenant. Treat as not-found.
            throw new RuntimeException("Session not found: {$uuid}");
        }
        return $row;
    }

    public function isTerminal(): bool
    {
        return empty(self::TRANSITIONS[$this->status] ?? []);
    }

    public function canTransitionTo(string $newStatus): bool
    {
        return in_array($newStatus, self::TRANSITIONS[$this->status] ?? [], true);
    }

    /**
     * Atomically write the next status AND a row to upload_session_audit.
     * Both inside the same DB transaction so the trail can never drift from
     * the row state. The GC job depends on this property: a row in Cleaning
     * state with no matching CleaningStart audit is treated as stuck.
     *
     * @param string $action Audit action verb (Created, UploadStart, ...). See audit table docblock.
     * @param string|null $actorId User id triggering the transition. Null for system (GC).
     * @param array $metadata Free-form metadata for the audit row.
     */
    public function transitionTo(
        string $newStatus,
        ?int $candidateId = null,
        ?string $action = null,
        ?int $actorId = null,
        array $metadata = [],
    ): self {
        if (! $this->canTransitionTo($newStatus)) {
            throw new RuntimeException(
                "Illegal resume_uploads transition: {$this->status} -> {$newStatus} for session {$this->uuid}",
            );
        }
        $from = $this->status;
        $this->status = $newStatus;
        if ($candidateId !== null) {
            $this->candidate_id = $candidateId;
        }

        DB::transaction(function () use ($newStatus, $from, $action, $actorId, $metadata) {
            $this->save();
            UploadSessionAudit::record(
                sessionUuid: $this->uuid,
                companyId: (int) $this->company_id,
                action: $action ?? $this->defaultActionForTransition($from, $newStatus),
                actorId: $actorId,
                requestId: request()?->header('X-Request-Id'),
                metadata: array_merge(['from' => $from, 'to' => $newStatus], $metadata),
            );
        });

        return $this;
    }

    private function defaultActionForTransition(string $from, string $to): string
    {
        // For monitoring dashboards to have a stable vocabulary, default every
        // transition to an inferred action. The caller should override if
        // a more specific action exists (e.g. Linked vs Replay).
        return match (true) {
            $from === self::STATUS_CREATED && $to === self::STATUS_UPLOADING => 'UploadStart',
            $to === self::STATUS_UPLOADED => 'UploadSuccess',
            $to === self::STATUS_UPLOAD_FAILED => 'UploadFailed',
            $to === self::STATUS_PARSED => 'ParseDone',
            $to === self::STATUS_PARSE_FAILED => 'ParseFailed',
            $to === self::STATUS_LINKED => 'Linked',
            $to === self::STATUS_ABORTED_BY_SUBMIT => 'Aborted',
            $to === self::STATUS_REPLACED => 'Replaced',
            $to === self::STATUS_COMPLETED => 'Completed',
            $to === self::STATUS_CLEANING => 'CleaningStart',
            $to === self::STATUS_DELETE_FAILED => 'DeleteFailed',
            $to === self::STATUS_EXPIRED => 'Expired',
            $to === self::STATUS_REDACTED_FOR_GDPR => 'RedactedForGDPR',
            default => ucfirst($to),
        };
    }

    public function setExpiresFromConfig(int $hours): void
    {
        if (! $this->upload_completed_at) {
            // Set expires_at in the same proportion even if not yet completed —
            // allows UI to display "expires in N hours" before finalization.
            $base = $this->upload_started_at ?? Carbon::now();
            $this->expires_at = $base->copy()->addHours($hours);
            return;
        }
        $this->expires_at = $this->upload_completed_at->copy()->addHours($hours);
    }
}
