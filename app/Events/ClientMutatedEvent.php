<?php

namespace App\Events;

use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Class ClientMutatedEvent
 *
 * Intent: Broadcast lightweight operational invalidation signals across recruiter tabs via Laravel Reverb.
 * Includes sequence version and previous_version to allow clients to detect dropped packets.
 * Tenancy Note: Scoped strictly to private channel company.{companyId}.
 *
 * Complies with docs/CODE_DOCUMENTATION_STANDARD.md.
 */
class ClientMutatedEvent implements ShouldBroadcast
{
    use Dispatchable, SerializesModels;

    /**
     * Create a new event instance.
     *
     * @param  int  $companyId  Tenant company identifier
     * @param  int  $clientId  Mutated client identifier
     * @param  string  $operation  'created' | 'updated' | 'deleted' | 'renamed'
     * @param  int  $version  Monotonically increasing version after mutation
     * @param  int  $previousVersion  Version immediately preceding mutation
     * @param  array  $data  Compact delta payload
     */
    public function __construct(
        public readonly int $companyId,
        public readonly int $clientId,
        public readonly string $operation,
        public readonly int $version,
        public readonly int $previousVersion,
        public readonly array $data = []
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
        return 'client.mutated';
    }
}
