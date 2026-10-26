<?php

namespace App\Events;

use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Class SuperAdminBenchApprovalDeltaEvent
 *
 * INTENT:
 * Broadcasts real-time delta updates for Bench Candidate Approval state transitions
 * (Pending -> Approved / Rejected, Auto-approval toggle) to authenticated Super Admin
 * sessions via Laravel Reverb. Enables 0ms client UI delta patching, KPI badge synchronization,
 * and multi-tab consistency under 10K concurrency with zero database read strain.
 *
 * TENANCY NOTE:
 * Broadcasts globally across Super Admin session boundaries on private channel 'superadmin.bench-candidates'.
 *
 * Author: Antigravity AI — Phase 1 Outbox & Idempotency Architecture
 * Date: October 26, 2026
 * Complies with docs/CODE_DOCUMENTATION_STANDARD.md.
 */
class SuperAdminBenchApprovalDeltaEvent implements ShouldBroadcast
{
    use Dispatchable, SerializesModels;

    /**
     * Create a new event instance.
     *
     * @param  array<int>  $candidateIds  List of updated candidate IDs
     * @param  int  $superadminStatus  Target status (0 = Pending, 1 = Approved, 2 = Rejected)
     * @param  string  $statusLabel  Human-readable label ('Approved', 'Rejected', etc.)
     * @param  string  $eventType  Canonical outbox event type
     * @param  array<string, int>  $counts  Authoritative platform bench counts
     * @param  string|null  $updatedBy  Name or email of administrator
     * @param  int  $entityVersion  Sequential event/aggregate version
     * @param  array<string, mixed>  $extra  Supplementary payload metadata (idempotency key, event ID)
     */
    public function __construct(
        public readonly array $candidateIds,
        public readonly int $superadminStatus,
        public readonly string $statusLabel,
        public readonly string $eventType = 'CANDIDATE_SUPERADMIN_APPROVED',
        public readonly array $counts = [],
        public readonly ?string $updatedBy = null,
        public readonly int $entityVersion = 1,
        public readonly array $extra = []
    ) {}

    /**
     * Get the private channel the event should broadcast on.
     */
    public function broadcastOn(): PrivateChannel
    {
        return new PrivateChannel('superadmin.bench-candidates');
    }

    /**
     * The event's broadcast name on Reverb.
     */
    public function broadcastAs(): string
    {
        return 'bench.approval.updated';
    }

    /**
     * Get the data array to broadcast to clients.
     *
     * @return array<string, mixed>
     */
    public function broadcastWith(): array
    {
        return [
            'candidate_ids' => $this->candidateIds,
            'superadmin_status' => $this->superadminStatus,
            'status_label' => $this->statusLabel,
            'event_type' => $this->eventType,
            'counts' => $this->counts,
            'updated_by' => $this->updatedBy,
            'entity_version' => $this->entityVersion,
            'timestamp' => now()->toIso8601String(),
            'extra' => $this->extra,
        ];
    }
}
