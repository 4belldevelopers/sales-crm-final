<?php

namespace App\Events;

use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Class TeamMemberUpdated
 *
 * Intent: Real-time broadcast signal over Laravel Reverb when a team member is
 * mutated (status change, role update, invitation, deactivation, working status change).
 * Enables instant multi-tab & multi-user L1 RAM cache invalidation and DOM delta patching
 * across all concurrent recruiters in the company with zero server reload strain.
 *
 * Tenancy: Broadcast strictly on private company channel: company.{companyId}
 */
class TeamMemberUpdated implements ShouldBroadcast
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public readonly int $companyId,
        public readonly int $userId,
        public readonly string $action,
        public readonly ?int $senderId = null,
        public readonly ?string $status = null,
        public readonly ?int $roleId = null,
        public readonly ?string $roleName = null,
        public readonly ?int $workingStatus = null,
        public readonly int $version = 1,
        public readonly array $extra = []
    ) {}

    /**
     * Get the channels the event should broadcast on.
     */
    public function broadcastOn(): PrivateChannel
    {
        return new PrivateChannel("company.{$this->companyId}");
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'team.member.updated';
    }

    /**
     * Get the data to broadcast.
     *
     * @return array<string, mixed>
     */
    public function broadcastWith(): array
    {
        return [
            'company_id' => $this->companyId,
            'user_id' => $this->userId,
            'action' => $this->action,
            'sender_id' => $this->senderId,
            'status' => $this->status,
            'role_id' => $this->roleId,
            'role_name' => $this->roleName,
            'working_status' => $this->workingStatus,
            'version' => $this->version,
            'extra' => $this->extra,
            'timestamp' => now()->toIso8601String(),
        ];
    }
}
