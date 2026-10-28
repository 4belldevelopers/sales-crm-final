<?php

namespace App\Events;

use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;

/**
 * Class PipelineUpdated
 *
 * Broadcast event emitted whenever candidate pipeline stages mutate.
 * Implements strict transaction commit safety ($afterCommit = true) and
 * monotonic sequence tokens (seq) for idempotent browser reducer updates.
 *
 * Channel: private-company.{companyId}
 * Event:   pipeline.updated
 */
class PipelineUpdated implements ShouldBroadcast
{
    use Dispatchable, SerializesModels;

    /**
     * Defer broadcast until active DB transaction commits.
     * Prevents WebSocket notifications on rolled-back transactions.
     */
    public bool $afterCommit = true;

    public readonly string $eventId;
    public readonly string $occurredAt;

    public function __construct(
        public readonly int $companyId,
        public readonly int $clientId,
        public readonly int $revision,
        public readonly array $changed = [],
        public readonly array $pv = []
    ) {
        $this->eventId = (string) Str::uuid();
        $this->occurredAt = now()->toIso8601String();
    }

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
        $appId = (int) ($this->changed['application_id'] ?? 0);
        $jobId = (int) ($this->changed['job_id'] ?? 0);
        $candId = (int) ($this->changed['candidate_id'] ?? 0);
        $fromStatus = (int) ($this->changed['from_status'] ?? 0);
        $toStatus = (int) ($this->changed['to_status'] ?? ($this->changed['new_status'] ?? 0));

        return [
            'tenant_id' => $this->companyId,
            'client_id' => $this->clientId,
            'application_id' => $appId,
            'job_id' => $jobId,
            'candidate_id' => $candId,
            'from_status' => $fromStatus,
            'to_status' => $toStatus,
            'seq' => $this->revision,
            'revision' => $this->revision,
            'event_id' => $this->eventId,
            'occurred_at' => $this->occurredAt,
            'event' => 'pipeline.updated',
            'changed' => $this->changed,
            'pv' => $this->pv,
        ];
    }
}
