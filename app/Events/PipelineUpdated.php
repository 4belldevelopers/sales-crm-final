<?php

namespace App\Events;

use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Class PipelineUpdated
 *
 * Broadcast event emitted whenever candidate pipeline stages mutate.
 * Delivers lightweight delta state and monotonic revision tokens across
 * team recruiter sessions via Laravel Reverb WebSockets.
 *
 * Channel: private-company.{companyId}
 * Event:   pipeline.updated
 */
class PipelineUpdated implements ShouldBroadcast
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public readonly int $companyId,
        public readonly int $clientId,
        public readonly int $revision,
        public readonly array $changed = [],
        public readonly array $pv = []
    ) {}

    public function broadcastOn(): PrivateChannel
    {
        return new PrivateChannel("company.{$this->companyId}");
    }

    public function broadcastAs(): string
    {
        return 'pipeline.updated';
    }

    public function broadcastWith(): array
    {
        return [
            'client_id' => $this->clientId,
            'revision' => $this->revision,
            'event' => 'pipeline.updated',
            'changed' => $this->changed,
            'pv' => $this->pv,
        ];
    }
}
