<?php

namespace App\Events;

use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Class SuperAdminMarketplaceUpdatedEvent
 *
 * INTENT:
 * Broadcasts real-time delta updates for Marketplace Job Requisitions and Moderation state transitions
 * (Pending -> Live / Rejected / Quarantine, Auto-approval toggle, Outbox replays) to authenticated
 * Super Admin sessions via Laravel Reverb / WebSockets.
 *
 * TENANCY NOTE:
 * Scoped globally to authorized Super Admin users on private channel 'superadmin.marketplace'.
 *
 * Author: Antigravity AI — Super Admin Backend Architecture
 * Complies with docs/CODE_DOCUMENTATION_STANDARD.md.
 */
class SuperAdminMarketplaceUpdatedEvent implements ShouldBroadcast
{
    use Dispatchable, SerializesModels;

    /**
     * Create a new event instance.
     *
     * @param  int  $jobId  Target Job ID
     * @param  string  $action  Action name ('approve', 'reject', 'auto_approval_toggle', 'replay')
     * @param  string  $marketPubState  Publication state ('published', 'pending_review', 'unpublished')
     * @param  string  $marketModState  Moderation state ('approved', 'pending', 'rejected', 'quarantined')
     * @param  int  $publicMarketStatus  Numeric status (1=pending, 2=approved, 3=rejected, 4=hold)
     * @param  int  $version  Authoritative aggregate version
     * @param  int  $streamSequence  Monotonic stream sequence ID
     * @param  array  $payload  Additional delta details
     */
    public function __construct(
        public readonly int $jobId,
        public readonly string $action,
        public readonly string $marketPubState,
        public readonly string $marketModState,
        public readonly int $publicMarketStatus,
        public readonly int $version = 1,
        public readonly ?int $streamSequence = null,
        public readonly array $payload = []
    ) {}

    /**
     * Get the private channel the event should broadcast on.
     */
    public function broadcastOn(): PrivateChannel
    {
        return new PrivateChannel('superadmin.marketplace');
    }

    /**
     * The event's broadcast name on Reverb.
     */
    public function broadcastAs(): string
    {
        return 'marketplace.job.updated';
    }

    /**
     * Get the data array to broadcast to clients.
     *
     * @return array<string, mixed>
     */
    public function broadcastWith(): array
    {
        return [
            'job_id' => $this->jobId,
            'action' => $this->action,
            'market_pub_state' => $this->marketPubState,
            'market_mod_state' => $this->marketModState,
            'public_market_status' => $this->publicMarketStatus,
            'version' => $this->version,
            'stream_sequence' => $this->streamSequence ?? $this->version,
            'timestamp' => now()->toIso8601String(),
            'payload' => $this->payload,
        ];
    }
}
