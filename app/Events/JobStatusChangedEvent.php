<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * JobStatusChangedEvent
 *
 * Real-time WebSocket acceleration event dispatched via Laravel Reverb.
 * In accordance with JERO Architecture Standard:
 * "Reverb accelerates convergence; it does not guarantee convergence."
 * Delivers Tier 1 instant optimistic RAM/DOM patch to active browser drawers.
 */
class JobStatusChangedEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public array $payload
    ) {}

    /**
     * Broadcast on private tenant/company channel.
     */
    public function broadcastOn(): array
    {
        $companyId = (int) ($this->payload['company_id'] ?? 0);

        return [
            new PrivateChannel("company.{$companyId}"),
        ];
    }

    /**
     * Event name for JavaScript listeners.
     */
    public function broadcastAs(): string
    {
        return 'job.status.changed';
    }
}
