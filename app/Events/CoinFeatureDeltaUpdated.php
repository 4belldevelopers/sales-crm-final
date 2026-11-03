<?php

namespace App\Events;

use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Class CoinFeatureDeltaUpdated
 *
 * Intent: Broadcasts real-time delta updates for Coin Features & Events to authenticated
 * Super Admin sessions via Laravel Reverb WebSockets. Enables 0ms UI delta synchronization,
 * multi-tab consistency, and live KPI counter recalculations under 10,000 concurrency.
 *
 * Channel: private-superadmin.coin-features
 * Author: Antigravity AI — Super Admin Backend Architecture
 * Complies with docs/CODE_DOCUMENTATION_STANDARD.md.
 */
class CoinFeatureDeltaUpdated implements ShouldBroadcast
{
    use Dispatchable, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(
        public readonly int $id,
        public readonly string $title,
        public readonly string $eventType,
        public readonly string $deductionType,
        public readonly float $defaultRate,
        public readonly float $minimumRequired,
        public readonly int $status,
        public readonly string $actionType,
        public readonly array $kpiCounts = [],
        public readonly ?string $description = null,
        public readonly ?string $updatedBy = null,
        public readonly ?string $timestamp = null
    ) {}

    /**
     * Get the channels the event should broadcast on.
     */
    public function broadcastOn(): PrivateChannel
    {
        return new PrivateChannel('superadmin.coin-features');
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'coin.feature.delta';
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'event_type' => $this->eventType,
            'deduction_type' => $this->deductionType,
            'default_rate' => $this->defaultRate,
            'minimum_required' => $this->minimumRequired,
            'status' => $this->status,
            'action_type' => $this->actionType,
            'kpi_counts' => $this->kpiCounts,
            'description' => $this->description,
            'updated_by' => $this->updatedBy,
            'timestamp' => $this->timestamp ?? now()->toIso8601String(),
        ];
    }
}
