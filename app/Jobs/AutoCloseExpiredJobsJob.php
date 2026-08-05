<?php

/**
 * Hourly background job: close expired jobs & renew free packages for ALL companies.
 *
 * Previously called synchronously in ServiceCompanyDashboardController::index() on every
 * dashboard GET (V2 Phase 1 refactor). Both service calls are now decoupled from the
 * HTTP request cycle.
 *
 * Tenancy: service methods already accept company_id explicitly (Rule #3).
 */

namespace App\Jobs;

use App\Services\AutoCloseJobsService;
use App\Services\AutoRenewFreePackage;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AutoCloseExpiredJobsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries   = 2;
    public int $backoff = 120;
    public int $timeout = 600;

    public function handle(AutoCloseJobsService $autoCloseJobsService, AutoRenewFreePackage $autoRenewFreePackage): void
    {
        $companyIds = DB::table('companies')
            ->where('status', 'active')
            ->pluck('id');

        foreach ($companyIds as $companyId) {
            try {
                $autoCloseJobsService->closeExpiredJobs((int) $companyId);
                $autoRenewFreePackage->autoRenewFreePackageCompany((int) $companyId);
            } catch (\Throwable $e) {
                Log::error('AutoCloseExpiredJobsJob: company failed', [
                    'company_id' => $companyId,
                    'error'      => $e->getMessage(),
                ]);
            }
        }
    }
}
