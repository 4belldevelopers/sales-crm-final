<?php

/**
 * OperationalCoinUpdated — V2 Dashboard Phase 5 broadcast event.
 *
 * Fired by ProcessOperationalCoinDeductionsJob after coin balances change.
 * The dashboard coin balance card re-fetches /api/v2/.../kpis instead of
 * waiting for the next page load or poll cycle.
 *
 * Channel: private-company.{companyId}.dashboard
 */

namespace App\Events;

use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OperationalCoinUpdated implements ShouldBroadcast
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public readonly int   $companyId,
        public readonly float $newBalance,
        public readonly float $deductedAmount,
        public readonly string $deductionType = 'team',  // 'team' | 'job'
    ) {}

    public function broadcastOn(): PrivateChannel
    {
        return new PrivateChannel("company.{$this->companyId}.dashboard");
    }

    public function broadcastAs(): string
    {
        return 'OperationalCoinUpdated';
    }

    public function broadcastWith(): array
    {
        return [
            'newBalance'    => $this->newBalance,
            'deducted'      => $this->deductedAmount,
            'deductionType' => $this->deductionType,
        ];
    }
}
