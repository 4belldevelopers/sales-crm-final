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

    /**
     * Ensure event is only broadcast after open DB transactions commit.
     * Prevents ghost-tenant updates on DB rollback.
     */
    public bool $afterCommit = true;

    /**
     * Maximum number of attempts before marking broadcast job as failed.
     * Prevents runaway retry hammering on WebSocket connection outages.
     */
    public int $tries = 3;

    /**
     * Backoff delays in seconds between retries [10s, 30s, 60s].
     * Prevents rapid hammer loops during transient network or Reverb restarts.
     */
    public array $backoff = [10, 30, 60];

    /**
     * Maximum execution time for the broadcast job in seconds.
     */
    public int $timeout = 15;

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

    /**
     * Handle permanent broadcast job failure with structured error logging.
     * Preserves audit visibility without exposing sensitive tokens or secrets.
     */
    public function failed(?\Throwable $exception): void
    {
        \Illuminate\Support\Facades\Log::warning('ConsultancyPartnershipUpdated: WebSocket broadcast permanently failed', [
            'company_id' => $this->companyId,
            'mutation_type' => $this->mutationType,
            'spartnerAll' => $this->spartnerAll,
            'sentRequest' => $this->sentRequest,
            'error' => $exception ? $exception->getMessage() : 'Unknown error',
        ]);
    }
}
