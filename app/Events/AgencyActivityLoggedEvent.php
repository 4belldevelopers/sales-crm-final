<?php

namespace App\Events;

use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Class AgencyActivityLoggedEvent
 *
 * Intent: Broadcast real-time live activity notifications over Laravel Reverb
 * to open Agency Drawer tabs, preventing stale data and eliminating page reload strain.
 *
 * Tenancy: Scoped to private channel company.{companyId}.
 */
class AgencyActivityLoggedEvent implements ShouldBroadcast
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public readonly int $companyId,
        public readonly int $agencyCompanyId,
        public readonly array $activityPayload,
        public readonly int $version
    ) {}

    public function broadcastOn(): PrivateChannel
    {
        return new PrivateChannel("company.{$this->companyId}");
    }

    public function broadcastAs(): string
    {
        return 'agency.activity.logged';
    }

    public function broadcastWith(): array
    {
        return [
            'agency_company_id' => $this->agencyCompanyId,
            'activity' => $this->activityPayload,
            'version' => $this->version,
            'timestamp' => now()->toIso8601String(),
        ];
    }
}
