<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * StageMoved — Step 13 of the Apptracker performance plan.
 *
 * Broadcast on the public pipeline.{jobId} channel whenever a candidate's
 * pipeline stage (master_status_id) changes. The Apptracker Echo client
 * receives this and refreshes the counter badges in <50ms without a page
 * reload. Broadcast via Reverb WebSockets.
 *
 * Channel: pipeline.{jobId}  (public — no auth required, counter data is
 * not private since it's just aggregate numbers already visible on the page).
 */
class StageMoved implements ShouldBroadcast
{
    use Dispatchable, SerializesModels;

    public int $jobId;
    public int $applicationId;
    public int $newStatus;
    public int $companyId;

    public function __construct(int $jobId, int $applicationId, int $newStatus, int $companyId)
    {
        $this->jobId         = $jobId;
        $this->applicationId = $applicationId;
        $this->newStatus     = $newStatus;
        $this->companyId     = $companyId;
    }

    public function broadcastOn(): Channel
    {
        return new Channel("pipeline.{$this->jobId}");
    }

    public function broadcastAs(): string
    {
        return 'StageMoved';
    }

    public function broadcastWith(): array
    {
        return [
            'jobId'         => $this->jobId,
            'applicationId' => $this->applicationId,
            'newStatus'     => $this->newStatus,
        ];
    }
}
