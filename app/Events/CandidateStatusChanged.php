<?php

/**
 * CandidateStatusChanged — V2 Dashboard Phase 5 broadcast event.
 *
 * Fired whenever a candidate's master_status_id changes (pipeline stage move).
 * The dashboard client receives this via the private company channel and refreshes
 * the KPI counters without polling.
 *
 * Channel: private-company.{companyId}.dashboard
 */

namespace App\Events;

use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CandidateStatusChanged implements ShouldBroadcast
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public readonly int    $companyId,
        public readonly int    $applicationId,
        public readonly int    $oldStatus,
        public readonly int    $newStatus,
        public readonly string $jobTitle = '',
    ) {}

    public function broadcastOn(): PrivateChannel
    {
        return new PrivateChannel("company.{$this->companyId}.dashboard");
    }

    public function broadcastAs(): string
    {
        return 'CandidateStatusChanged';
    }

    public function broadcastWith(): array
    {
        return [
            'applicationId' => $this->applicationId,
            'oldStatus'     => $this->oldStatus,
            'newStatus'     => $this->newStatus,
            'jobTitle'      => $this->jobTitle,
        ];
    }
}
