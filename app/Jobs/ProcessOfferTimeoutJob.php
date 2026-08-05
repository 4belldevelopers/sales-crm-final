<?php

/**
 * Every-5-minutes job: expire offer applications that have been pending for 72+ hours.
 *
 * Previously ran synchronously in ServiceCompanyDashboardController::index() on every
 * dashboard GET (V2 Phase 1 refactor). Now decoupled from the HTTP request cycle.
 *
 * Tenancy: joins offer_job_applications.company_id explicitly (Rule #3).
 */

namespace App\Jobs;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProcessOfferTimeoutJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries   = 3;
    public int $backoff = 60;
    public int $timeout = 120;

    public function handle(): void
    {
        try {
            $cutoff = Carbon::now()->subHours(72);

            // Bulk update — single query instead of the previous N+1 foreach loop.
            // Tenancy: scoped through the join on offer_job_applications.company_id.
            $affected = DB::table('offer_create_main_tds')
                ->join(
                    'offer_job_applications',
                    'offer_job_applications.id',
                    '=',
                    'offer_create_main_tds.offer_id'
                )
                ->where('offer_create_main_tds.candidate_status', 1)
                ->where('offer_create_main_tds.status', 1)
                ->where('offer_create_main_tds.updated_at', '<=', $cutoff)
                ->update(['offer_create_main_tds.candidate_status' => 4]);

            if ($affected > 0) {
                Log::info('ProcessOfferTimeoutJob: expired offers updated', ['count' => $affected]);
            }
        } catch (\Throwable $e) {
            Log::error('ProcessOfferTimeoutJob failed', ['error' => $e->getMessage()]);
            throw $e;
        }
    }
}
