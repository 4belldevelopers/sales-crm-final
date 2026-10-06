<?php

namespace App\Events;

use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * ConsultancyPartnershipUpdated — Broadcasts staffing partner / consultancy mutation events over Laravel Reverb.
 *
 * Intent:
 * Notifies all active recruiter browser sessions within the company when an agency partner invitation
 * or relationship is mutated so that browser-side IndexedDB and RAM caches are instantly purged
 * and counter badges update in real-time with zero full-page reloads and zero backend polling.
 *
 * Tenancy:
 * Scoped strictly to private company channel: company.{companyId}
 */
class ConsultancyPartnershipUpdated implements ShouldBroadcast
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public readonly int $companyId,
        public readonly string $mutationType,
        public readonly ?int $spartnerAll = null,
        public readonly ?int $sentRequest = null,
        public readonly array $extra = []
    ) {}

    public function broadcastOn(): PrivateChannel
    {
        return new PrivateChannel("company.{$this->companyId}");
    }

    public function broadcastAs(): string
    {
        return 'consultancy.updated';
    }

    public function broadcastWith(): array
    {
        return [
            'company_id' => $this->companyId,
            'mutation_type' => $this->mutationType,
            'spartnerAll' => $this->spartnerAll,
            'sentRequest' => $this->sentRequest,
            'extra' => $this->extra,
            'timestamp' => now()->toIso8601String(),
        ];
    }
}
