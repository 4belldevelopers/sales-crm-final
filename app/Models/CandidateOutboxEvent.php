<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * Intent: Transactional Outbox event entity for resilient, idempotent async processing.
 * Maps to candidate_outbox_events table.
 */
class CandidateOutboxEvent extends Model
{
    protected $table = 'candidate_outbox_events';

    protected $guarded = ['id'];

    protected $casts = [
        'payload' => 'array',
        'retry_count' => 'integer',
        'event_version' => 'integer',
        'available_at' => 'datetime',
        'locked_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function scopeForCompany(Builder $query, int $companyId): Builder
    {
        return $query->where('company_id', $companyId);
    }

    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', 'pending');
    }

    public function scopeClaimable(Builder $query): Builder
    {
        return $query->where('status', 'pending')
            ->where('available_at', '<=', now())
            ->where(function (Builder $sub) {
                $sub->whereNull('locked_at')
                    ->orWhere('locked_at', '<', now()->subMinutes(5));
            });
    }
}
