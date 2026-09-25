<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class OutboxEvent
 *
 * Intent: Transactional Outbox pattern implementation for Forward Resume dispatches.
 * Guarantees at-least-once publication to queue without dual-write inconsistency.
 */
class OutboxEvent extends Model
{
    protected $table = 'outbox_events';

    protected $fillable = [
        'event_id',
        'aggregate_type',
        'aggregate_id',
        'event_type',
        'payload',
        'status',
        'attempts',
        'published_at',
    ];

    protected $casts = [
        'payload' => 'array',
        'attempts' => 'integer',
        'published_at' => 'datetime',
    ];

    public function scopePending($query)
    {
        return $query->where('status', 'PENDING');
    }
}
