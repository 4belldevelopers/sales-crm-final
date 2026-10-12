<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * PlacementRecordUpdated — Real-time event broadcast over Laravel Reverb.
 *
 * Intent:
 * Broadcasts placement mutations (commercial configuration changes, assignment status
 * updates, expected end date modifications) across all active recruiter sessions
 * within the tenant company so that client DOM cells update in-place without page reload.
 *
 * Tenancy:
 * Strictly broadcast on private channel: company.{companyId}
 */
class PlacementRecordUpdated implements ShouldBroadcast
{
    use Dispatchable, SerializesModels;

    public int $companyId;
    public int $candidateId;
    public int $wiseId;
    public string $action;
    public array $payload;

    public function __construct(
        int $companyId,
        int $candidateId,
        int $wiseId = 0,
        string $action = 'updated',
        array $payload = []
    ) {
        $this->companyId   = $companyId;
        $this->candidateId = $candidateId;
        $this->wiseId      = $wiseId;
        $this->action      = $action;
        $this->payload     = $payload;
    }

    public function broadcastOn(): Channel
    {
        return new PrivateChannel("company.{$this->companyId}");
    }

    public function broadcastAs(): string
    {
        return 'placement.record.updated';
    }

    public function broadcastWith(): array
    {
        return [
            'company_id'   => $this->companyId,
            'candidate_id' => $this->candidateId,
            'wise_id'      => $this->wiseId,
            'action'       => $this->action,
            'payload'      => $this->payload,
            'timestamp'    => microtime(true),
        ];
    }
}
