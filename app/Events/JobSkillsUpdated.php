<?php

namespace App\Events;

use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * JobSkillsUpdated — Broadcasts job skills update signal over Laravel Reverb.
 *
 * Intent:
 * Notifies all active browser sessions in the company when a job's skills are mutated
 * (added, edited, or removed) so that client-side L1 RAM caches and L2 IndexedDB for
 * associated candidate drawers, as well as Forward Resume WASM compiled attachments,
 * are invalidated immediately across all recruiter tabs with zero user latency.
 *
 * Tenancy:
 * Broadcast on private company channel: company.{companyId}
 */
class JobSkillsUpdated implements ShouldBroadcast
{
    use Dispatchable, SerializesModels;

    /**
     * @param int $companyId
     * @param int $jobId
     * @param array $candidateIds IDs of candidates associated with this job
     * @param array $wiseIds Job-wise association IDs
     * @param array $skills Current updated skills list
     * @param int $entityVersion
     */
    public function __construct(
        public readonly int $companyId,
        public readonly int $jobId,
        public readonly array $candidateIds = [],
        public readonly array $wiseIds = [],
        public readonly array $skills = [],
        public readonly int $entityVersion = 0
    ) {}

    public function broadcastOn(): PrivateChannel
    {
        return new PrivateChannel("company.{$this->companyId}");
    }

    public function broadcastAs(): string
    {
        return 'job.skills.updated';
    }

    public function broadcastWith(): array
    {
        return [
            'company_id' => $this->companyId,
            'job_id' => $this->jobId,
            'candidate_ids' => array_values(array_map('intval', $this->candidateIds)),
            'wise_ids' => array_values(array_map('intval', $this->wiseIds)),
            'skills' => $this->skills,
            'entity_version' => $this->entityVersion ?: time(),
            'timestamp' => now()->toIso8601String(),
        ];
    }
}
