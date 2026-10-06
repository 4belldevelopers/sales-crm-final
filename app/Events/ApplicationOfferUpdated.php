<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * ApplicationOfferUpdated
 *
 * Real-time event broadcast over WebSocket / Laravel Reverb to keep all
 * connected recruiter browsers synchronized when an offer is created, updated,
 * or salary/dates change.
 *
 * Channel: company.{companyId} (Private channel scoped strictly by tenant)
 */
class ApplicationOfferUpdated implements ShouldBroadcast
{
    use Dispatchable, SerializesModels;

    public int $companyId;
    public int $offerId;
    public ?int $applicationId;
    public string $action;
    public array $payload;

    public function __construct(int $companyId, int $offerId, ?int $applicationId = null, string $action = 'updated', array $payload = [])
    {
        $this->companyId     = $companyId;
        $this->offerId       = $offerId;
        $this->applicationId = $applicationId;
        $this->action        = $action;
        $this->payload       = $payload;
    }

    public function broadcastOn(): Channel
    {
        return new PrivateChannel("company.{$this->companyId}");
    }

    public function broadcastAs(): string
    {
        return 'offer.updated';
    }

    public function broadcastWith(): array
    {
        return [
            'company_id'     => $this->companyId,
            'offer_id'       => $this->offerId,
            'application_id' => $this->applicationId,
            'action'         => $this->action,
            'payload'        => $this->payload,
            'timestamp'      => microtime(true),
        ];
    }
}
