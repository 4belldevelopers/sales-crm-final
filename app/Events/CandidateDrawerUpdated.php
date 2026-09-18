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
        public readonly string $mutationType = 'updated'
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
        return [
            'candidate_id' => $this->candidateId,
            'entity_version' => $this->entityVersion,
            'mutation_type' => $this->mutationType,
            'timestamp' => now()->toIso8601String(),
        ];
    }
}
