<?php

namespace App\Events;

use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * BenchRequestCreated — Real-time Reverb broadcast event for Bench Talent Request creation (Phase 4).
 *
 * Intent:
 * Broadcasts to tenant-isolated private channels for both the requesting company and the
 * job-owning company so client-side browser stores invalidate RAM cache and update tab
 * counter badges without any polling or page reload (Rule 4 & Rule 5).
 *
 * Tenancy Note:
 * Broadcasts strictly to private-company.{companyId}.job.{jobId}.
 *
 * Author: Antigravity AI
 * Date: October 1, 2026
 */
class BenchRequestCreated implements ShouldBroadcast
{
    use Dispatchable, SerializesModels;

    /**
     * @param int $companyId Target company ID to receive broadcast
     * @param int $jobId Target job ID
     * @param int $requestId rec_candidate_assign primary key
     * @param int $candidateId job_applications primary key
     * @param string $tab Tab affected (e.g. 'internal_bench', 'public_bench', 'pool')
     */
    public function __construct(
        public readonly int $companyId,
        public readonly int $jobId,
        public readonly int $requestId,
        public readonly int $candidateId,
        public readonly string $tab = 'pool'
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
        return 'bench.request.created';
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
            'candidateId' => $this->candidateId,
            'tab' => $this->tab,
            'timestamp' => now()->toIso8601String(),
        ];
    }
}
