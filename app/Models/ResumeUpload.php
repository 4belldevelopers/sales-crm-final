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
 *   Created ¬─> Uploading ¬─> Uploaded ¬─> QueuedForParsing ¬─> Parsing ¬─> Parsed
 *                   │              │              │               │           │
 *                   ▼              ▼              ▼               ▼           ▼
 *             UploadFailed   UploadFailed      (re-dispatched) ParseFailed  Replaced / AbortedBySubmit
 *                                            │                 │
 *                                            ▼                 ▼
 *                                          Cleaning       ManualReviewRequired
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
 * Phase 8 added STATUS_QUEUED_FOR_PARSING so the row's DB column
 * reflects "this row is in the parse queue but the worker hasn't yet
 * claimed it". The conditional UPDATE pattern that already guards
 * the parse_* transitions now extends to Uploaded → QueuedForParsing
 * → Parsing → Parsed. Analysts who want "time-in-queue" can compute
 * it from upload_completed_at → parse_started_at (the latter only
 * populated once the worker sets status=Parsing).
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
    public const STATUS_QUEUED_FOR_PARSING = 'QueuedForParsing';
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
    public const STATUS_RECONCILED = 'Reconciled';

    public const RESOURCE_TYPE_JOB_APPLICATION = 'job_application';
    public const RESOURCE_TYPE_BENCH_APPLICATION = 'bench_application';

    /**
     * Canonical list of terminal states. Reconciled was added in Phase 5 so
     * the weekly Reconciler can leave rows as a forensic trail rather than
     * hard-delete them. Use ::isTerminal() if you need a single source of
     * "should we GC this anyway?".
     */
    public const TERMINAL_STATES = [
        self::STATUS_REDACTED_FOR_GDPR,
        self::STATUS_EXPIRED,
        self::STATUS_RECONCILED,
        self::STATUS_DELETE_FAILED,
    ];

    /**
     * Allowed transitions. Keys are current state, values are arrays of legal
     * next states. Anything outside this map is rejected by transitionTo() so
     * accidental API calls cannot put a session into an unrecoverable state.
     *
     * Phase 5 added STATUS_RECONCILED as the terminal state set by the
     * weekly Reconciler when it culls an Uploaded-but-never-linked row.
     */
    private const TRANSITIONS = [
        self::STATUS_CREATED => [self::STATUS_UPLOADING, self::STATUS_UPLOADED, self::STATUS_UPLOAD_FAILED, self::STATUS_EXPIRED],
        self::STATUS_UPLOADING => [self::STATUS_UPLOADED, self::STATUS_UPLOAD_FAILED, self::STATUS_EXPIRED],
        self::STATUS_UPLOADED => [
            self::STATUS_QUEUED_FOR_PARSING,                       // Phase 8
            self::STATUS_PARSING,                                  // legacy fallthrough for pre-Phase-8 rows
            self::STATUS_PARSE_FAILED,
            self::STATUS_RECONCILED,
            self::STATUS_CLEANING,
        ],
        self::STATUS_QUEUED_FOR_PARSING => [                       // Phase 8
            self::STATUS_PARSING,
            self::STATUS_PARSE_FAILED,
            self::STATUS_CLEANING,
        ],
        self::STATUS_UPLOAD_FAILED => [self::STATUS_CLEANING, self::STATUS_EXPIRED],
        self::STATUS_PARSING => [self::STATUS_PARSED, self::STATUS_PARSE_FAILED, self::STATUS_MANUAL_REVIEW_REQUIRED, self::STATUS_CLEANING],
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
        self::STATUS_RECONCILED => [],
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
     * Phase 5 — centralize lifecycle predicates.
     *
     * Before Phase 5 each subsystem (UI banner, GC clock, reconciler,
     * repair) wrote its own boolean expressions inline. That drift cost us
     * a UI bug where the recovery banner appeared for AbortedBySubmit rows
     * (those aren't recoverable). All four helpers below are now the
     * canonical "what's the deal with this row" answers.
     *
     * All methods are pure (depend only on the model's own state and an
     * externally-supplied `now` snapshot for test determinism).
     */

    /**
     * Has the session passed expires_at OR is it in a recognized terminal
     * state? Used by GC sweeps and the UI to decide whether to display
     * "draft expired" messaging.
     *
     * NOTE: a session can be Uploaded but past its expires_at and still
     * have neither Uploaded->Reconciled nor Cleaning. That is the orphan
     * case Phase 5 was created for.
     */
    public function isExpired(?Carbon $now = null): bool
    {
        $now ??= Carbon::now();
        if (in_array($this->status, self::TERMINAL_STATES, true)) {
            return true;
        }
        return $this->expires_at !== null && $this->expires_at->lessThan($now);
    }

    /**
     * Is this row an orphan? An orphan is a row whose bytes are on R2 but
     * for which no caller will ever link them to a candidate — the user
     * dropped off without submitting. The reconciliation clock culls
     * these.
     *
     * Strict definition:
     *   - status is Uploaded (file is on R2, parser may or may not have run)
     *   - past upload_completed_at + orphan_grace_hours (default 24h)
     *   - candidate_id IS NULL AND bench_application_id IS NULL
     *   - status has NOT advanced to Linked/Replaced/Completed etc.
     *
     * If a parser ran and Parsed, the row is reusable on form re-open —
     * NOT an orphan. If Linked, definitely not. If AbortedBySubmit
     * the row may still be Replay-capable, so it's pending, not orphaned.
     */
    public function isOrphan(int $orphanGraceHours, ?Carbon $now = null): bool
    {
        $now ??= Carbon::now();
        if ($this->status !== self::STATUS_UPLOADED) {
            return false;
        }
        if ($this->candidate_id !== null || $this->bench_application_id !== null) {
            return false;
        }
        $base = $this->upload_completed_at ?? $this->upload_started_at ?? $this->created_at;
        if ($base === null) {
            return false;
        }
        return $base->copy()->addHours($orphanGraceHours)->lessThan($now);
    }

    /**
     * Could the user still meaningfully resume this draft? Used by the
     * recruiter UI's "active session" banner and by `activeForUser()`.
     *
     * Recovery is allowed while:
     *   - The session is in a non-terminal state
     *   - It has parsed data (so we can pre-fill the form) OR
     *     it is still in mid-flight (Uploading/Uploaded/Parsing)
     *   - It has not yet linked/replaced/expired
     *
     * Note: Parsed + already Linked without completing side-effects
     * (AbortedBySubmit) is intentionally NOT recoverable — the user
     * either retries (Replayed) or starts a new upload; auto-resume
     * would create phantom candidates.
     */
    public function isRecoverable(): bool
    {
        if (in_array($this->status, self::TERMINAL_STATES, true)) {
            return false;
        }
        if (in_array($this->status, [
            self::STATUS_LINKED,
            self::STATUS_REPLACED,
            self::STATUS_COMPLETED,
            self::STATUS_ABORTED_BY_SUBMIT,
        ], true)) {
            return false;
        }
        // Created without bytes, with a cancelled intent — not recoverable.
        if ($this->status === self::STATUS_CREATED && $this->r2_path === null) {
            return false;
        }
        return true;
    }

    /**
     * Should repair / GC / reconciler put this on the work list RIGHT NOW?
     * The need depends only on the row's own state plus a now snapshot —
     * external timing params (timeout_minutes etc.) are supplied by the
     * caller because they live in config/resumes.php.
     *
     * Use this when scheduling ("is this row worth a cron look?") — for
     * write eligibility ("can we delete this row?") see isOrphan() above.
     */
    public function needsCleanup(?Carbon $now = null): bool
    {
        $now ??= Carbon::now();
        if ($this->isOrphan(
            (int) config('resumes.orphan_grace_hours', 24),
            $now,
        )) {
            return true;
        }
        if ($this->isExpired($now)) {
            return true;
        }
        return false;
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
     * @param int|null $actorId User id triggering the transition. Null for system (GC).
     * @param int|null $actorIp ip2long() of the caller. Optional — falls through to the live request's ip() if absent.
     * @param string|null $userAgent UA string of the caller. Optional.
     * @param string|null $requestId Caller's X-Request-Id. Optional — falls through to the live request.
     * @param array $metadata Free-form metadata for the audit row.
     */
    public function transitionTo(
        string $newStatus,
        ?int $candidateId = null,
        ?string $action = null,
        ?int $actorId = null,
        ?int $actorIp = null,
        ?string $userAgent = null,
        ?string $requestId = null,
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

        DB::transaction(function () use ($newStatus, $from, $action, $actorId, $actorIp, $userAgent, $requestId, $metadata) {
            $this->save();
            UploadSessionAudit::record(
                sessionUuid: $this->uuid,
                companyId: (int) $this->company_id,
                action: $action ?? $this->defaultActionForTransition($from, $newStatus),
                actorId: $actorId,
                actorIp: $actorIp ?? ip2long((string) request()?->ip()),
                userAgent: $userAgent,
                requestId: $requestId ?? request()?->header('X-Request-Id'),
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
            $from === self::STATUS_UPLOADED && $to === self::STATUS_QUEUED_FOR_PARSING => 'Queued',     // Phase 8
            $from === self::STATUS_QUEUED_FOR_PARSING && $to === self::STATUS_PARSING => 'PickedUp',  // Phase 8
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
