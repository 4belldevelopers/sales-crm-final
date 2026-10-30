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

    public function __construct(
        public readonly int $companyId,
        public readonly int $jobId,
        public readonly string $oldStatus,
        public readonly string $newStatus,
        public readonly string $jobTitle = '',
        ?int $sequenceId = null
    ) {
        $this->sequenceId = $sequenceId ?? (int) (microtime(true) * 1000);
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
            'companyId' => $this->companyId,
            'jobId' => $this->jobId,
            'oldStatus' => $this->oldStatus,
            'newStatus' => $this->newStatus,
            'jobTitle' => $this->jobTitle,
            'sequence_id' => $this->sequenceId,
            'version' => SuperAdminJobsCacheManager::getVersion(),
            'tenant_version' => (int) cache()->get('tenant_'.$this->companyId.'_client_version', 1),
            'timestamp' => now()->toIso8601String(),
        ];
    }
}
