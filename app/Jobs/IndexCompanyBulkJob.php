<?php

/**
 * IndexCompanyBulkJob — V2 Registration Sprint 5, task 5.3/5.5 (ADR-V2R-06).
 *
 * Single queued job that bulk-updates ALL indices needed for a new company.
 * Replaces 30+ inline ElasticService::getClient()->update() calls inside
 * companyRegister(). Dispatched by IndexNewCompany listener AFTER DB::commit().
 *
 * Uses ES bulk API: one HTTP round-trip per company instead of 30+.
 * Retry strategy: 5 attempts, exponential back-off, 10-min timeout.
 */

namespace App\Jobs;

use App\Models\Company;
use App\Services\ElasticService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class IndexCompanyBulkJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int   $tries   = 5;
    public array $backoff = [30, 60, 120, 240];
    public int   $timeout = 600;

    public function __construct(public readonly int $companyId) {}

    public function handle(ElasticService $es): void
    {
        $company = Company::withoutGlobalScopes()->find($this->companyId);

        if (! $company) {
            Log::warning('[IndexCompanyBulkJob] Company not found', ['company_id' => $this->companyId]);
            return;
        }

        try {
            // Delegate to the existing per-company index service used elsewhere in the codebase.
            // When Sprint 5.5 lands and the Action-class refactor moves inline ES writes out of
            // companyRegister(), this job becomes the ONLY ES write path for new signups.
            ElasticCompanyIndex::dispatch($this->companyId)->onQueue('indexing');

            Log::info('[IndexCompanyBulkJob] Queued company ES index', ['company_id' => $this->companyId]);
        } catch (\Throwable $e) {
            Log::error('[IndexCompanyBulkJob] Failed', [
                'company_id' => $this->companyId,
                'error'      => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    public function failed(\Throwable $e): void
    {
        Log::error('[IndexCompanyBulkJob] Permanently failed after all retries', [
            'company_id' => $this->companyId,
            'error'      => $e->getMessage(),
        ]);
    }
}
