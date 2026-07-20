<?php

namespace App\Jobs;

use App\Services\OwnershipEngine\OwnershipEngine;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * Ownership Engine, Phase 3 (shadow mode) — docs/OWNERSHIP_ENGINE_DESIGN.md §13/§18.1:
 * Company Reservation attempts are queued off the hot candidate-creation request path
 * (Upload, Similar Candidates, Internal Bench, Agency Bench, Career Page, Copy-to-Other-Job)
 * — none of those endpoints wait on the ownership lock. Only claimForAgencySourcing()
 * (the InReview->Sourcing handoff) stays synchronous, per the same design section.
 *
 * Failure isolation matches the inline call sites it replaces: any exception is caught and
 * logged here, never rethrown — a failed reservation attempt must never retry-storm the
 * queue or surface anywhere a user can see it, since this is still shadow mode (the legacy
 * duplicate logic remains the only thing production behavior depends on).
 */
class AttemptOwnershipCompanyReservation implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $addJobWiseCondidateId;

    public function __construct(int $addJobWiseCondidateId)
    {
        $this->addJobWiseCondidateId = $addJobWiseCondidateId;

        if (empty($this->uuid)) {
            $this->uuid = (string) Str::uuid();
        }
    }

    public function handle(OwnershipEngine $engine): void
    {
        try {
            $engine->attemptCompanyReservation($this->addJobWiseCondidateId);
        } catch (\Throwable $e) {
            Log::warning('OwnershipEngine::attemptCompanyReservation failed (Phase 3 shadow mode, non-blocking)', [
                'row_id' => $this->addJobWiseCondidateId,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Do not retry — shadow mode. A failed reservation attempt is logged (above) and left
     * as-is; Phase 4's backfill/rebuild is what a stale/missing ledger entry gets corrected
     * by, not automatic queue retries racing a not-yet-understood failure.
     */
    public $tries = 1;
}
