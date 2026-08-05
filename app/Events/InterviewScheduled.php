<?php

/**
 * InterviewScheduled — V2 Dashboard Phase 5 broadcast event.
 *
 * Fired when a new interview is created or rescheduled.
 * The dashboard client receives this and updates the "Today's Interviews"
 * counter + list panel without polling.
 *
 * Channel: private-company.{companyId}.dashboard
 */

namespace App\Events;

use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class InterviewScheduled implements ShouldBroadcast
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public readonly int    $companyId,
        public readonly int    $interviewId,
        public readonly string $scheduleDate,
        public readonly string $stage = '',
        public readonly string $status = 'pending',
    ) {}

    public function broadcastOn(): PrivateChannel
    {
        return new PrivateChannel("company.{$this->companyId}.dashboard");
    }

    public function broadcastAs(): string
    {
        return 'InterviewScheduled';
    }

    public function broadcastWith(): array
    {
        return [
            'interviewId'  => $this->interviewId,
            'scheduleDate' => $this->scheduleDate,
            'stage'        => $this->stage,
            'status'       => $this->status,
        ];
    }
}
