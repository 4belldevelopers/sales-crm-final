<?php

namespace App\Events;

use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * BenchRequestStatusUpdated — Real-time Reverb broadcast event for Bench Talent Request status transitions (Phase 4).
 *
 * Intent:
 * Broadcasts status updates (e.g. Pending -> Accepted / ac_status = 1) across tenant-isolated
 * private channels to ensure active recruiters see badge counters adjust and RAM cache
 * automatically evict stale entries in <50ms (Rule 5 & Rule 6).
 *
 * Tenancy Note:
 * Broadcasts strictly to private-company.{companyId}.job.{jobId}.
 *
 * Author: Antigravity AI
 * Date: October 1, 2026
 */
class BenchRequestStatusUpdated implements ShouldBroadcast
{
    use Dispatchable, SerializesModels;

    /**
     * @param int $companyId Target company ID
     * @param int $jobId Target job ID
     * @param int $requestId rec_candidate_assign primary key
     * @param int $oldStatus Previous ac_status (e.g. 0)
     * @param int $newStatus New ac_status (e.g. 1)
     * @param string $oldTab Previous tab name
     * @param string $newTab New tab name
     */
    public function __construct(
        public readonly int $companyId,
        public readonly int $jobId,
        public readonly int $requestId,
        public readonly int $oldStatus,
        public readonly int $newStatus,
        public readonly string $oldTab = 'pool',
        public readonly string $newTab = 'existing'
    ) {}

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<\Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel("company.{$this->companyId}.job.{$this->jobId}"),
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'bench.request.status_updated';
    }

    /**
     * Get the data to broadcast.
     *
     * @return array<string, mixed>
     */
    public function broadcastWith(): array
    {
        return [
            'requestId' => $this->requestId,
            'jobId' => $this->jobId,
            'oldStatus' => $this->oldStatus,
            'newStatus' => $this->newStatus,
            'old_tab' => $this->oldTab,
            'new_tab' => $this->newTab,
            'timestamp' => now()->toIso8601String(),
        ];
    }
}
