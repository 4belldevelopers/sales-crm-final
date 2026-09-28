<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Class SecurityContextChangedEvent
 *
 * Intent: Broadcasts SECURITY_CONTEXT_CHANGED over Reverb/WebSockets to invalidate client L1 RAM
 * and redact confidential DOM elements mid-session per docs/drawer_candidate.md.
 */
class SecurityContextChangedEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public int $tenantId,
        public ?int $userId,
        public int $permissionVersion,
        public string $authContextHash,
        public string $type = 'SECURITY_CONTEXT_CHANGED'
    ) {}

    public function broadcastOn(): array
    {
        $channels = [
            new PrivateChannel('jero_ats_candidate_lifecycle_' . $this->tenantId),
        ];

        if ($this->userId) {
            $channels[] = new PrivateChannel('user.' . $this->userId);
        }

        return $channels;
    }

    public function broadcastAs(): string
    {
        return 'SECURITY_CONTEXT_CHANGED';
    }

    public function broadcastWith(): array
    {
        return [
            'type' => $this->type,
            'tenant_id' => $this->tenantId,
            'user_id' => $this->userId,
            'permission_version' => $this->permissionVersion,
            'auth_context_hash' => $this->authContextHash,
            'timestamp' => now()->timestamp,
        ];
    }
}
