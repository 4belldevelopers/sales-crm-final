<?php

namespace App\Events;

use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Class CommercialsUpdated
 *
 * Intent: Real-time broadcast signal over Laravel Reverb when commercial terms
 * (Fulltime or Contract) are updated for an organization.
 * Security Invariant: Broadcasts ONLY lightweight version metadata, NEVER sensitive financial rates!
 *
 * Tenancy: Broadcast strictly on private company channel: company.{companyId}
 */
class CommercialsUpdated implements ShouldBroadcast
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public readonly int $companyId,
        public readonly string $serviceType, // 'fulltime' | 'contract'
        public readonly int $version = 1,
        public readonly ?int $senderId = null,
        public readonly array $meta = []
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
        return 'commercials.updated';
    }

    /**
     * Get the data to broadcast (Lightweight metadata only).
     *
     * @return array<string, mixed>
     */
    public function broadcastWith(): array
    {
        return [
            'company_id' => $this->companyId,
            'service_type' => $this->serviceType,
            'version' => $this->version,
            'sender_id' => $this->senderId,
            'meta' => $this->meta,
            'timestamp' => now()->toIso8601String(),
        ];
    }
}
