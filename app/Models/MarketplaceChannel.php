<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class MarketplaceChannel
 *
 * Intent:
 * Normalized representation of a requisition's engagement channel (Agency Hiring vs. Bench Talent).
 * Eliminates column bloat on core jobs table and decouples channel-level moderation, publication,
 * and capacity limits from the underlying ATS job lifecycle.
 *
 * Tenancy:
 * Scoped by company_id (Hiring Client Tenant).
 */
class MarketplaceChannel extends Model
{
    use BelongsToTenant;

    protected $table = 'marketplace_channels';

    protected $fillable = [
        'job_id',
        'company_id',
        'channel_type',
        'enabled',
        'moderation_status',
        'publication_status',
        'approval_mode',
        'vendor_limit',
        'moderated_by',
        'moderated_at',
        'moderation_reason',
        'published_by',
        'published_at',
        'paused_by',
        'paused_at',
        'rejected_by',
        'rejected_at',
        'closed_at',
    ];

    protected $casts = [
        'enabled' => 'boolean',
        'vendor_limit' => 'integer',
        'moderated_at' => 'datetime',
        'published_at' => 'datetime',
        'paused_at' => 'datetime',
        'rejected_at' => 'datetime',
        'closed_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Requisition associated with this channel.
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
     * Super Admin who moderated this channel.
     */
    public function moderator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'moderated_by');
    }
}
