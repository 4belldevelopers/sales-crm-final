<?php

namespace App\Events;

use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Class CoinTransactionUpdated
 *
 * Intent: High-throughput broadcast event for coin transaction log updates across recruiter tabs.
 * Decoupled from MySQL transaction; broadcasts via Laravel Reverb on private channel company.{companyId}.
 *
 * Tenancy Note: Broadcast on private tenant channel company.{companyId} with strict authorization.
 * Minimal authorized payload prevents financial detail leakage across clients.
 *
 * Complies with docs/CODE_DOCUMENTATION_STANDARD.md.
 */
class CoinTransactionUpdated implements ShouldBroadcast
{
    use Dispatchable, SerializesModels;

    /**
     * Create a new event instance.
     *
     * @param  int  $companyId  Tenant company identifier
     * @param  string  $eventType  'operational' | 'sales'
     * @param  string  $transectionType  'credit' | 'debit'
     * @param  float  $coins  Transacted coins amount
     * @param  float  $balanceCoins  Remaining running balance
     * @param  string  $event  Description of the coin event
     * @param  string|null  $eventId  Optional unique event correlation id
     */
    public function __construct(
        public readonly int $companyId,
        public readonly string $eventType,
        public readonly string $transectionType,
        public readonly float $coins,
        public readonly float $balanceCoins,
        public readonly string $event = '',
        public readonly ?string $eventId = null
    ) {}

    /**
     * Get the channels the event should broadcast on.
     */
    public function broadcastOn(): PrivateChannel
    {
        return new PrivateChannel("company.{$this->companyId}");
    }

    /**
     * Broadcast as a specific event name.
     */
    public function broadcastAs(): string
    {
        return 'coin.transaction.updated';
    }

    /**
     * Get the data to broadcast.
     * Minimal authorized payload to prevent information leakage.
     *
     * @return array<string, mixed>
     */
    public function broadcastWith(): array
    {
        return [
            'event_id' => $this->eventId ?? ('tx_'.uniqid()),
            'company_id' => $this->companyId,
            'event_type' => $this->eventType,
            'transection_type' => $this->transectionType,
            'coins' => $this->coins,
            'balance_coins' => $this->balanceCoins,
            'event' => $this->event,
            'timestamp' => now()->toIso8601String(),
        ];
    }
}
