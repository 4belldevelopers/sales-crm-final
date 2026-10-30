<?php

namespace App\Events;

use App\Services\SuperAdminJobsCacheManager;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * JobStatusChanged — Real-time event emitted when job status transitions (active/inactive/hold).
 *
 * Intent:
 * Broadcasts status changes over Laravel Reverb so open ATS and public career portals
 * immediately reflect the updated status without full page reloads.
 *
 * Security & Tenancy:
 * - Private company channel: company.{companyId}
 * - Public career channel: career.{companyId}
 * - Private Super Admin channel: superadmin.jobs (authorized in routes/channels.php)
 * - Causal event ordering guaranteed via monotonic microsecond sequence_id.
 */
class JobStatusChanged implements ShouldBroadcast
{
    use Dispatchable, SerializesModels;

    public readonly int $sequenceId;

    public readonly int $aggregateVersion;

    public readonly string $eventId;

    public readonly int $streamSequence;

    public function __construct(
        public readonly int $companyId,
        public readonly int $jobId,
        public readonly string $oldStatus,
        public readonly string $newStatus,
        public readonly string $jobTitle = '',
        ?int $sequenceId = null,
        int $aggregateVersion = 1,
        ?string $eventId = null,
        ?int $streamSequence = null
    ) {
        $this->eventId = $eventId ?? (string) \Illuminate\Support\Str::uuid();
        $this->streamSequence = $streamSequence ?? (int) \Illuminate\Support\Facades\Cache::increment('stream:sa:jobs:seq');
        $this->sequenceId = $sequenceId ?? $this->streamSequence;
        $this->aggregateVersion = $aggregateVersion;
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel("company.{$this->companyId}"),
            new Channel("career.{$this->companyId}"),
            new PrivateChannel('superadmin.jobs'),
            new Channel('superadmin-jobs'), // Backward-compatibility fallback
        ];
    }

    public function broadcastAs(): string
    {
        return 'JobStatusChanged';
    }

    public function broadcastWith(): array
    {
        return [
            'event_id' => $this->eventId,
            'companyId' => $this->companyId,
            'jobId' => $this->jobId,
            'oldStatus' => $this->oldStatus,
            'newStatus' => $this->newStatus,
            'jobTitle' => $this->jobTitle,
            'stream_sequence' => $this->streamSequence,
            'sequence_id' => $this->sequenceId,
            'aggregate_version' => $this->aggregateVersion,
            'version' => SuperAdminJobsCacheManager::getVersion(),
            'tenant_version' => (int) cache()->get('tenant_'.$this->companyId.'_client_version', 1),
            'timestamp' => now()->toIso8601String(),
        ];
    }
}
