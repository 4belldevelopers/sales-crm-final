<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class MarketplaceSubmission
 *
 * Intent:
 * The single canonical business record (submission_id) representing a candidate submission
 * from a partner staffing agency to a client job across either Agency Hiring or Bench Talent channels.
 * Maintains compatibility with operational ATS reference (job_applications.id).
 *
 * Tenancy:
 * Scoped by company_id (Hiring Client Tenant) and accessible by agency_id (Partner Agency).
 */
class MarketplaceSubmission extends Model
{
    use BelongsToTenant;

    protected $table = 'marketplace_submissions';

    protected $fillable = [
        'job_id',
        'company_id',
        'agency_id',
        'candidate_id',
        'channel',
        'source',
        'application_id',
        'lifecycle_status',
        'version',
    ];

    protected $casts = [
        'job_id' => 'integer',
        'company_id' => 'integer',
        'agency_id' => 'integer',
        'candidate_id' => 'integer',
        'application_id' => 'integer',
        'version' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Target requisition.
     */
    public function job(): BelongsTo
    {
        return $this->belongsTo(Job::class, 'job_id');
    }

    /**
     * Hiring client company tenant.
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    /**
     * Partner agency company tenant.
     */
    public function agency(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'agency_id');
    }

    /**
     * Submitted candidate profile.
     */
    public function candidate(): BelongsTo
    {
        return $this->belongsTo(JobApplication::class, 'candidate_id');
    }

    /**
     * Operational ATS application record.
     */
    public function application(): BelongsTo
    {
        return $this->belongsTo(JobApplication::class, 'application_id');
    }

    /**
     * Granular audit trail logs for this submission.
     */
    public function auditLogs(): HasMany
    {
        return $this->hasMany(MarketplaceSubmissionAuditLog::class, 'submission_id');
    }
}
