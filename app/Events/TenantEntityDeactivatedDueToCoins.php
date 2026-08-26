<?php

namespace App\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * TenantEntityDeactivatedDueToCoins — V2 Coins Engine Domain Event.
 *
 * Intent: Fired asynchronously when a team user or job posting is deactivated
 * due to exhausted operational coin balance during daily operational deductions.
 *
 * Tenancy Note: Carries companyId to enforce tenant context during background ES status sync.
 *
 * Author: Antigravity AI
 * Date: August 26, 2026
 */
class TenantEntityDeactivatedDueToCoins
{
    use Dispatchable, SerializesModels;

    public int $companyId;

    public int $entityId;

    public string $entityType; // 'user' or 'job'

    public mixed $newStatus; // 0 for user, 'inactive' for job

    /**
     * Create a new event instance.
     *
     * @param  int  $companyId  Target company tenant ID.
     * @param  int  $entityId  Target entity ID (user ID or job ID).
     * @param  string  $entityType  Entity type ('user' or 'job').
     * @param  mixed  $newStatus  Updated status value (0 or 'inactive').
     */
    public function __construct(int $companyId, int $entityId, string $entityType, mixed $newStatus)
    {
        $this->companyId = $companyId;
        $this->entityId = $entityId;
        $this->entityType = $entityType;
        $this->newStatus = $newStatus;
    }
}
