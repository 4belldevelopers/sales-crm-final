<?php

namespace App\Events;

use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Class AppTrackerCandidateAssignedEvent
 *
 * Intent: Real-time Reverb WebSocket broadcast event for candidate assignment to a job.
 * Dispatched by the decoupled Reverb outbox worker with sub-50ms latency.
 * Tenancy: Broadcast on tenant-isolated private channel: tenant.{companyId}.job.{jobId}
 * Complies with docs/CODE_DOCUMENTATION_STANDARD.md.
 */
class AppTrackerCandidateAssignedEvent implements ShouldBroadcastNow
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public readonly int $tenantId,
        public readonly int $jobId,
        public readonly int $version,
        public readonly int $candidateId,
        public readonly int $wiseId,
        public readonly int $masterStatusId = 8,
        public readonly array $metadata = []
    ) {}

    public function broadcastOn(): PrivateChannel
    {
        return new PrivateChannel("tenant.{$this->tenantId}.job.{$this->jobId}");
    }

    public function broadcastAs(): string
    {
        return 'candidate.assigned';
    }

    public function broadcastWith(): array
    {
        return [
            'tenant_id' => $this->tenantId,
            'job_id' => $this->jobId,
            'version' => $this->version,
            'candidate_id' => $this->candidateId,
            'wise_id' => $this->wiseId,
            'master_status_id' => $this->masterStatusId,
            'metadata' => $this->metadata,
            'timestamp' => now()->toIso8601String(),
        ];
    }
}
