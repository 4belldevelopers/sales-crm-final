<?php

namespace App\Events;

use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * MarketJobDeltaUpdated — Broadcasts real-time market job delta updates.
 *
 * Intent:
 * Phase 3 Real-Time Delta Synchronization. Notifies all active recruiter browser sessions
 * in the tenant company when a market job is claimed, updated, or status changes, enabling
 * instant surgical DOM card patching and atomic cache synchronization with zero polling
 * and zero avoidable server/database read strain.
 *
 * Tenancy:
 * Scoped to private tenant channel: company.{companyId}
 */
class MarketJobDeltaUpdated implements ShouldBroadcast
{
    use Dispatchable, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(
        public readonly int $companyId,
        public readonly int $jobId,
        public readonly string $eventType = 'updated',
        public readonly int $entityVersion = 1,
        public readonly ?string $actionType = null,
        public readonly ?string $statusLabel = null,
        public readonly ?int $vendorAddedCount = null,
        public readonly ?int $benchMatchCount = null,
        public readonly ?int $remainingSlots = null,
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
        return 'market.job.delta';
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
            'job_id' => $this->jobId,
            'event_type' => $this->eventType,
            'entity_version' => $this->entityVersion,
            'action_type' => $this->actionType,
            'status_label' => $this->statusLabel,
            'vendor_added_count' => $this->vendorAddedCount,
            'bench_match_count' => $this->benchMatchCount,
            'remaining_slots' => $this->remainingSlots,
            'extra' => $this->extra,
            'timestamp' => now()->toIso8601String(),
        ];
    }
}
