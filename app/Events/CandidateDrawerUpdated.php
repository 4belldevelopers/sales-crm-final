<?php

namespace App\Events;

use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * CandidateDrawerUpdated — Broadcasts candidate drawer update signal over Laravel Reverb.
 *
 * Intent:
 * Notifies all active browser sessions in the company when a candidate is mutated
 * (status change, skill rating, note update) so that client-side L1 RAM caches are
 * invalidated immediately across all recruiter tabs with zero user latency.
 *
 * Tenancy:
 * Broadcast on private company channel: company.{companyId}
 */
class CandidateDrawerUpdated implements ShouldBroadcast
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public readonly int $companyId,
        public readonly int $candidateId,
        public readonly int $entityVersion,
        public readonly string $mutationType = 'updated',
        public readonly array $wiseIds = [],
        public readonly ?int $skillMatch = null,
        public readonly ?string $skills = null,
        public readonly ?string $recruiterNote = null,
        public readonly ?int $stageId = null,
        public readonly ?int $oldStageId = null,
        public readonly ?int $jobId = null,
        public readonly ?string $resume = null,
        public readonly array $extra = []
    ) {}

    public function broadcastOn(): PrivateChannel
    {
        return new PrivateChannel("company.{$this->companyId}");
    }

    public function broadcastAs(): string
    {
        return 'candidate.drawer.updated';
    }

    public function broadcastWith(): array
    {
        $payload = [
            'candidate_id' => $this->candidateId,
            'wise_ids' => $this->wiseIds,
            'entity_version' => $this->entityVersion,
            'mutation_type' => $this->mutationType,
            'timestamp' => now()->toIso8601String(),
        ];

        if ($this->skillMatch !== null) {
            $payload['skill_match'] = $this->skillMatch;
        }
        if ($this->skills !== null) {
            $payload['skills'] = $this->skills;
        }
        if ($this->recruiterNote !== null) {
            $payload['recruiter_note'] = $this->recruiterNote;
        }
        if ($this->stageId !== null) {
            $payload['stage_id'] = $this->stageId;
        }
        if ($this->oldStageId !== null) {
            $payload['old_stage_id'] = $this->oldStageId;
        }
        if ($this->jobId !== null) {
            $payload['job_id'] = $this->jobId;
        }
        if ($this->resume !== null) {
            $payload['resume'] = $this->resume;
        }
        if (! empty($this->extra)) {
            $payload['extra'] = $this->extra;
        }

        return $payload;
    }
}
