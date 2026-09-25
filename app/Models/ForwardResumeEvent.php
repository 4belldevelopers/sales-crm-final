<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class ForwardResumeEvent
 *
 * Intent: Forensic audit log and event journal capturing all state transitions
 * in the Forward Resume pipeline for compliance, debugging, and self-healing reconciliation.
 */
class ForwardResumeEvent extends Model
{
    public $timestamps = false;

    protected $table = 'forward_resume_events';

    protected $fillable = [
        'event_id',
        'forward_request_id',
        'event_type',
        'previous_state',
        'new_state',
        'actor_type',
        'actor_id',
        'metadata',
        'created_at',
    ];

    protected $casts = [
        'metadata' => 'array',
        'actor_id' => 'integer',
        'created_at' => 'datetime',
    ];

    public function scopeByForwardRequest($query, string $forwardRequestId)
    {
        return $query->where('forward_request_id', $forwardRequestId);
    }
}
