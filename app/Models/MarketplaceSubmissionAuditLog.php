<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class MarketplaceSubmissionAuditLog
 *
 * Intent:
 * Immutable chronological ledger of all lifecycle status changes, reviews, notes, and actions
 * on a candidate submission with cryptographic state snapshot hashes and distributed correlation IDs.
 *
 * Tenancy:
 * Scoped by submission_id.
 */
class MarketplaceSubmissionAuditLog extends Model
{
    public $timestamps = false;

    protected $table = 'marketplace_submission_audit_logs';

    protected $fillable = [
        'submission_id',
        'actor_id',
        'actor_role',
        'action',
        'from_status',
        'to_status',
        'source',
        'reason',
        'ip_address',
        'correlation_id',
        'snapshot_hash',
        'created_at',
    ];

    protected $casts = [
        'submission_id' => 'integer',
        'actor_id' => 'integer',
        'created_at' => 'datetime',
    ];

    /**
     * Submission associated with this log entry.
     */
    public function submission(): BelongsTo
    {
        return $this->belongsTo(MarketplaceSubmission::class, 'submission_id');
    }

    /**
     * User who executed the action.
     */
    public function actor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'actor_id');
    }
}
