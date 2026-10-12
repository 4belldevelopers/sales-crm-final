<?php

namespace App\Events;

use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Class DepartmentMutatedEvent
 *
 * Intent: Broadcast lightweight operational department invalidation signals across recruiter tabs via Laravel Reverb.
 * Allows client-side Department Intelligence Engines to invalidate IndexedDB and L1 RAM caches and fetch fresh manifests.
 * Tenancy Note: Scoped strictly to private channel company.{companyId}.
 *
 * Complies with docs/CODE_DOCUMENTATION_STANDARD.md and docs/AI_RULES.md.
 */
class DepartmentMutatedEvent implements ShouldBroadcast
{
    use Dispatchable, SerializesModels;

    /**
     * Create a new event instance.
     *
     * @param  int  $companyId  Tenant company identifier
     * @param  int  $categoryId  Mutated department identifier
     * @param  string  $operation  'created' | 'updated' | 'deleted'
     * @param  int  $version  Monotonically increasing version after mutation
     * @param  int  $previousVersion  Version immediately preceding mutation
     * @param  array  $data  Compact delta payload
     */
    public function __construct(
        public readonly int $companyId,
        public readonly int $categoryId,
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
        return 'department.mutated';
    }
}
