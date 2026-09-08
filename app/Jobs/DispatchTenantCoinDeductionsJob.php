<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * DispatchTenantCoinDeductionsJob — V2 Coins Engine Parent Dispatcher Job.
 *
 * Intent: Scheduled every 5 minutes in Kernel. Queries distinct active tenant company IDs
 * from company_coins and dispatches ProcessCompanyCoinDeductionJob instances in parallel chunks of 50
 * onto the 'coins' queue.
 *
 * Tenancy Note: Fetches all distinct active company_id tenants and dispatches per-tenant jobs.
 *
 * Author: Antigravity AI
 * Date: August 26, 2026
 */
class DispatchTenantCoinDeductionsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public int $timeout = 120;

    /**
     * Execute the tenant dispatcher job.
     */
    public function handle(): void
    {
        $companies = DB::table('company_coins')
            ->where('event_type', 'operational')
            ->distinct()
            ->pluck('company_id');

        $count = 0;
        $companies->chunk(50)->each(function ($chunk) use (&$count) {
            foreach ($chunk as $companyId) {
                ProcessCompanyCoinDeductionJob::dispatch((int) $companyId)->onQueue('coins');
                $count++;
            }
        });

        Log::info('DispatchTenantCoinDeductionsJob: Dispatched per-company coin jobs', [
            'total_companies' => $count,
        ]);
    }
}
