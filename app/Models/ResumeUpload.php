<?php
namespace App\Models;

use App\Enums\ResumeUploadStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

// Intent: Eloquent model for the resume_uploads table.
// Follows existing project patterns — no repository layer (CLAUDE.md rule 10).
// Tenancy is enforced via scopeForCompany — never call ResumeUpload::all().

class ResumeUpload extends Model
{
    protected $table = 'resume_uploads';

    protected $fillable = [
        'uuid',
        'company_id',
        'user_id',
        'original_filename',
        'stored_filename',
        'file_hash',
        'mime_type',
        'file_size_bytes',
        'r2_temp_key',
        'r2_temp_url',
        'r2_url',
        'idempotency_key',
        'status',
        'candidate_id',
        'bench_application_id',
        'linked_candidate_id',
        'parser_started_at',
        'parser_completed_at',
        'parser_attempts',
        'last_error',
        'last_parser_pod_id',
        'upload_completed_at',
        'expires_at',
        'cleanup_reason',
    ];

    protected $casts = [
        'file_size_bytes'       => 'integer',
        'parser_attempts'       => 'integer',
        'parser_started_at'     => 'datetime',
        'parser_completed_at'   => 'datetime',
        'upload_completed_at'   => 'datetime',
        'expires_at'            => 'datetime',
    ];

    // ─── Scopes ───────────────────────────────────────────────────────────

    /** CRITICAL: always scope by company_id (CLAUDE.md rule 3) */
    public function scopeForCompany($query, int|string $companyId)
    {
        return $query->where('company_id', (int) $companyId);
    }

    public function scopeByUuid($query, string $uuid)
    {
        return $query->where('uuid', $uuid);
    }

    public function scopeNonTerminal($query)
    {
        return $query->whereNotIn('status', [
            ResumeUploadStatus::Completed->value,
            ResumeUploadStatus::Failed->value,
            ResumeUploadStatus::Cancelled->value,
            ResumeUploadStatus::Expired->value,
            ResumeUploadStatus::Reconciled->value,
        ]);
    }

    /** Find sessions whose TTL has elapsed and are still non-terminal */
    public function scopeExpiredSessions($query)
    {
        return $query->whereNotIn('status', [
            ResumeUploadStatus::Completed->value,
            ResumeUploadStatus::Failed->value,
            ResumeUploadStatus::Cancelled->value,
            ResumeUploadStatus::Reconciled->value,
        ])->where('expires_at', '<', now());
    }

    /** Find uploads stuck in Uploading state for > 5 min */
    public function scopeStuck($query)
    {
        return $query->where('status', ResumeUploadStatus::Uploading->value)
                     ->where('updated_at', '<', now()->subMinutes(5));
    }

    // ─── Helpers ──────────────────────────────────────────────────────────

    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    public function markFailed(string $reason): void
    {
        $this->update([
            'status'     => ResumeUploadStatus::Failed->value,
            'last_error' => $reason,
        ]);
    }

    public function statusEnum(): ResumeUploadStatus
    {
        return ResumeUploadStatus::from($this->status);
    }

    // ─── Relations ────────────────────────────────────────────────────────

    public function parseResult(): HasOne
    {
        return $this->hasOne(ResumeParseResult::class, 'resume_upload_id');
    }

    public function audits(): HasMany
    {
        return $this->hasMany(UploadSessionAudit::class, 'resume_upload_id');
    }
}
