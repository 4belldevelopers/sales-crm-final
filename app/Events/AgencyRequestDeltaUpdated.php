<?php

namespace App\Events;

use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * AgencyRequestDeltaUpdated — Broadcasts real-time partner agency request/invitation delta updates.
 *
 * Intent:
 * Phase 4 Real-Time Delta Synchronization. Notifies all active recruiter browser sessions
 * in the tenant company when an agency invitation request is sent, cancelled, or status changes.
 * Enables instant surgical DOM button updates and atomic cache invalidation with zero full-page
 * or table reloading, minimizing server and database read strain.
 *
 * Tenancy:
 * Scoped to private tenant channel: company.{companyId}
 * Complies with docs/CODE_DOCUMENTATION_STANDARD.md.
 */
class AgencyRequestDeltaUpdated implements ShouldBroadcast
{
    use Dispatchable, SerializesModels;

    /**
     * Create a new event instance.
     *
     * @param  int  $companyId  Tenant company ID receiving the delta broadcast
     * @param  int  $agencyId  Target partner agency company ID
     * @param  string  $eventType  Action type: 'sent', 'cancelled', 'connected', 'status_changed'
     * @param  int  $entityVersion  Aggregate/state version counter
     * @param  int|null  $invitationId  Primary key of the staffing_company_invitation record
     * @param  array  $extra  Additional context payload
     */
    public function __construct(
        public readonly int $companyId,
        public readonly int $agencyId,
        public readonly string $eventType = 'sent',
        public readonly int $entityVersion = 1,
        public readonly ?int $invitationId = null,
        public readonly array $extra = []
    ) {}

    /**
     * Get the channels the event should broadcast on.
     */
    public function broadcastOn(): PrivateChannel
    {
        return new PrivateChannel("company.{$this->companyId}");
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'agency.request.delta';
    }

    /**
     * Get the data to broadcast.
     *
     * @return array<string, mixed>
     */
    public function broadcastWith(): array
    {
        return [
            'company_id' => $this->companyId,
            'agency_id' => $this->agencyId,
            'event_type' => $this->eventType,
            'entity_version' => $this->entityVersion,
            'invitation_id' => $this->invitationId,
            'timestamp' => now()->toISOString(),
            'extra' => $this->extra,
        ];
    }
}
