<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;

/**
 * Class ReconcileBenchHeroCountersCommand
 *
 * Intent: Reconciles Redis hero pipeline counters with MySQL without full table scan strain.
 * Scope: Strictly quarantined to Service Company bench candidate domain (Rule 17).
 */
class ReconcileBenchHeroCountersCommand extends Command
{
    protected $signature = 'talents:reconcile-hero-counters 
                            {--company_id= : Reconcile specific company}
                            {--all : Force sweep of active companies in last 24h}';

    protected $description = 'Reconciles Redis hero pipeline counters with MySQL without table scan strain';

    public function handle(): int
    {
        $specificCompany = $this->option('company_id');
        $forceAll = $this->option('all');

        $tenantIds = [];

        if ($specificCompany) {
            $tenantIds = [(int) $specificCompany];
        } elseif ($forceAll) {
            // Nightly active window sweep (never dormant companies)
            $tenantIds = DB::table('companies')
                ->where('status', 'active')
                ->where('updated_at', '>=', now()->subHours(24))
                ->pluck('id')
                ->toArray();
        } else {
            // Standard execution: Pop up to 50 dirty tenants from Redis (zero wasted MySQL CPU)
            try {
                $tenantIds = Redis::spop('bench:dirty_tenants', 50) ?: [];
            } catch (\Throwable $e) {
                Log::warning('RECONCILE_HERO_REDIS_ERROR: fallback to empty dirty tenants', ['error' => $e->getMessage()]);
                $tenantIds = [];
            }
        }

        if (empty($tenantIds)) {
            $this->info('Zero dirty tenants pending counter reconciliation.');
            return 0;
        }

        $this->info('Reconciling hero counters for ' . count($tenantIds) . ' tenants...');

        foreach ($tenantIds as $cid) {
            // Uses compound index idx_bench_company_type_status on (company_id, condidate_type, status)
            $counts = DB::table('job_applications')
                ->where('company_id', $cid)
                ->where('condidate_type', 'bench_condidate')
                ->selectRaw('
                    COUNT(*) as total_count,
                    SUM(CASE WHEN status = 2 THEN 1 ELSE 0 END) as bench_count,
                    SUM(CASE WHEN status = 1 THEN 1 ELSE 0 END) as pool_count,
                    SUM(CASE WHEN status = 4 THEN 1 ELSE 0 END) as hold_count
                ')
                ->first();

            $total = (int) ($counts->total_count ?? 0);
            $bench = (int) ($counts->bench_count ?? 0);
            $pool  = (int) ($counts->pool_count ?? 0);
            $hold  = (int) ($counts->hold_count ?? 0);

            try {
                Redis::mset([
                    "bench:hero:total:{$cid}" => $total,
                    "bench:hero:bench:{$cid}" => $bench,
                    "bench:hero:pool:{$cid}"  => $pool,
                    "bench:hero:hold:{$cid}"  => $hold,
                ]);
            } catch (\Throwable $e) {
                Log::warning('RECONCILE_HERO_REDIS_MSET_FAILED', ['company_id' => $cid, 'error' => $e->getMessage()]);
            }
        }

        $this->info('Hero counter reconciliation completed successfully.');
        return 0;
    }
}
