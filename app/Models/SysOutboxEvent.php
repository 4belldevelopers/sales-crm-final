<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * Class SysOutboxEvent
 *
 * Intent: Transactional Outbox entity for ATS Candidate Drawer CQRS and Watermark-Guaranteed ES Rebuilds
 * per docs/drawer_candidate.md.
 */
class SysOutboxEvent extends Model
{
    public $timestamps = false;

    protected $table = 'sys_outbox_events';

    protected $guarded = ['id'];

    protected $casts = [
        'tenant_id' => 'integer',
        'aggregate_id' => 'integer',
        'aggregate_version' => 'integer',
        'payload' => 'array',
        'attempts' => 'integer',
        'available_at' => 'datetime',
        'processed_at' => 'datetime',
        'locked_at' => 'datetime',
        'created_at' => 'datetime',
    ];

    public function scopeForTenant(Builder $query, int $tenantId): Builder
    {
        return $query->where('tenant_id', $tenantId);
    }

    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', 'PENDING');
    }

    public function scopeDeadLetter(Builder $query): Builder
    {
        return $query->where('status', 'DEAD_LETTER');
    }
}
