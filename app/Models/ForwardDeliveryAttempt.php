<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class ForwardDeliveryAttempt
 *
 * Intent: Tracks individual recipient email delivery attempts within a forward request.
 * Enforces authoritative application-level idempotency via UNIQUE(forward_request_id, recipient_email).
 */
class ForwardDeliveryAttempt extends Model
{
    protected $table = 'forward_delivery_attempts';

    protected $fillable = [
        'forward_request_id',
        'recipient_email',
        'status',
        'attempt_count',
        'idempotency_key',
        'provider_message_id',
        'last_error',
        'sent_at',
    ];

    protected $casts = [
        'attempt_count' => 'integer',
        'sent_at' => 'datetime',
    ];

    public function forwardRequest(): BelongsTo
    {
        return $this->belongsTo(ForwardRequest::class, 'forward_request_id');
    }
}
