<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * TeamInvitationAuditLog Model
 *
 * Implements immutable forensic audit logging for invitation lifecycle actions.
 *
 * @property int $id
 * @property int $invitation_id
 * @property int|null $actor_id
 * @property string $action
 * @property string|null $ip_address
 * @property string|null $user_agent
 * @property array|null $payload_snapshot
 * @property Carbon $created_at
 */
class TeamInvitationAuditLog extends Model
{
    protected $table = 'team_invitation_audit_logs';

    public $timestamps = false;

    protected $guarded = ['id'];

    protected $casts = [
        'payload_snapshot' => 'array',
        'invitation_id'    => 'integer',
        'actor_id'         => 'integer',
        'created_at'       => 'datetime',
    ];

    public function invitation(): BelongsTo
    {
        return $this->belongsTo(TeamInvitation::class, 'invitation_id');
    }

    public function actor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'actor_id');
    }
}
