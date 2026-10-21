<?php

namespace App\Events;

use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Class CustomPlanRequestDeltaUpdated
 *
 * Intent: Broadcasts real-time delta mutations for Custom Plan Requests to authenticated
 * Super Admin sessions via Laravel Reverb WebSockets. Enables 0ms UI delta patching,
 * KPI counter synchronization, and multi-tab consistency under 10K concurrency.
 *
 * Channel: private-superadmin.custom-plans (Restricted strictly to super-admin role).
 *
 * Author: Antigravity AI
 * Date: October 21, 2026
 */
class CustomPlanRequestDeltaUpdated implements ShouldBroadcast
{
    use Dispatchable, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(
        public readonly int $id,
        public readonly string $reference,
        public readonly string $eventType,
        public readonly string $newStatus,
        public readonly ?string $previousStatus = null,
        public readonly int $entityVersion = 1,
        public readonly ?string $statusBadge = null,
        public readonly array $counts = [],
        public readonly ?string $lastUpdated = null,
        public readonly ?string $adminNotes = null,
        public readonly ?string $updatedBy = null,
        public readonly ?int $companyId = null,
        public readonly array $extra = []
    ) {}

    /**
     * Get the channels the event should broadcast on.
     */
    public function broadcastOn(): PrivateChannel
    {
        return new PrivateChannel('superadmin.custom-plans');
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'custom.plan.delta';
    }

    /**
     * Get the data to broadcast.
     *
     * @return array<string, mixed>
     */
    public function broadcastWith(): array
    {
        return [
            'id' => $this->id,
            'reference' => $this->reference,
            'event_type' => $this->eventType,
            'new_status' => $this->newStatus,
            'previous_status' => $this->previousStatus,
            'entity_version' => $this->entityVersion,
            'status_badge' => $this->statusBadge,
            'counts' => $this->counts,
            'last_updated' => $this->lastUpdated,
            'admin_notes' => $this->adminNotes,
            'updated_by' => $this->updatedBy,
            'company_id' => $this->companyId,
            'extra' => $this->extra,
            'timestamp' => now()->toIso8601String(),
        ];
    }
}
