<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class ForwardRequest
 *
 * Intent: Tracks a high-level candidate resume forward operation, coordinating
 * multi-recipient fan-out and enforcing the delivery barrier before attachment deletion.
 * Tenancy: Enforces company_id scoping via scopeForCompany.
 */
class ForwardRequest extends Model
{
    protected $table = 'forward_requests';

    protected $fillable = [
        'uuid',
        'company_id',
        'user_id',
        'candidate_count',
        'recipient_count',
        'status',
        'completed_at',
    ];

    protected $casts = [
        'candidate_count' => 'integer',
        'recipient_count' => 'integer',
        'completed_at' => 'datetime',
    ];

    /**
     * Tenant scoping: Always filter by company_id (CLAUDE.md rule 3)
     */
    public function scopeForCompany($query, int|string $companyId)
    {
        return $query->where('company_id', (int) $companyId);
    }

    public function scopeByUuid($query, string $uuid)
    {
        return $query->where('uuid', $uuid);
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(ForwardAttachment::class, 'forward_request_id');
    }

    public function deliveryAttempts(): HasMany
    {
        return $this->hasMany(ForwardDeliveryAttempt::class, 'forward_request_id');
    }
}
