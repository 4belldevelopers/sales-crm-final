<?php

namespace App\Services;

use App\Helper\GeneralMethod;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * MarketplaceReconciliationService
 *
 * Implements Architecture V2.2 Master Specification:
 * - Automated self-healing reconciliation between MySQL authoritative state and Elasticsearch
 * - Stalled outbox lock recovery
 * - Coin ledger audit & consistency verification
 */
class MarketplaceReconciliationService
{
    protected ElasticService $elasticService;

    public function __construct(ElasticService $elasticService)
    {
        $this->elasticService = $elasticService;
    }

    /**
     * Recover stalled outbox events where worker crashed mid-processing.
     *
     * @return int Number of stalled events recovered
     */
    public function recoverStalledOutboxEvents(int $stalledMinutes = 5): int
    {
        $threshold = now()->subMinutes($stalledMinutes);

        $recovered = DB::table('sys_outbox_events')
            ->where('aggregate_type', 'MarketJob')
            ->where('status', 'PROCESSING')
            ->where('locked_at', '<=', $threshold)
            ->update([
                'status' => 'PENDING',
                'locked_at' => null,
            ]);

        if ($recovered > 0) {
            Log::info("[MarketplaceReconciliationService] Recovered {$recovered} stalled outbox events.");
        }

        return (int) $recovered;
    }

    /**
     * Scan and heal state drift between MySQL jobs and Elasticsearch ats_jobs index.
     *
     * @return array Contains 'scanned', 'repaired', 'errors'
     */
    public function healProjectionDrift(int $limit = 100): array
    {
        $repaired = 0;
        $errors = 0;

        $jobs = DB::table('jobs')
            ->select('id', 'company_id', 'market_pub_state', 'market_mod_state', 'public_market_status', 'state_version')
            ->whereIn('market_pub_state', ['published', 'pending_review', 'unpublished'])
            ->orWhere('updated_at', '>=', now()->subHours(2))
            ->orderBy('updated_at', 'desc')
            ->limit($limit)
            ->get();

        if ($jobs->isEmpty()) {
            return ['scanned' => 0, 'repaired' => 0, 'errors' => 0];
        }

        $client = $this->elasticService->getClient();

        foreach ($jobs as $job) {
            try {
                $needsRepair = false;

                $params = [
                    'index' => ELASTIC_JOBS,
                    'id' => (string) $job->id,
                ];

                if (! $this->elasticService->indexExists(ELASTIC_JOBS)) {
                    break;
                }

                try {
                    $esDoc = $client->getSource($params);
                } catch (\Throwable $e) {
                    $esDoc = null;
                }

                if (! $esDoc) {
                    $needsRepair = true;
                } else {
                    $esPubState = $esDoc['market_pub_state'] ?? null;
                    $esModState = $esDoc['market_mod_state'] ?? null;
                    $esStatus = $esDoc['public_market_status'] ?? null;

                    if ($esPubState !== $job->market_pub_state || (int) $esStatus !== (int) $job->public_market_status) {
                        $needsRepair = true;
                    }
                }

                if ($needsRepair) {
                    GeneralMethod::elasticJobsAddEditUpdateIndex($job->id);
                    $repaired++;
                    Log::info("[MarketplaceReconciliationService] Self-healed drift for Job #{$job->id}.");
                }
            } catch (\Throwable $err) {
                $errors++;
                Log::error("[MarketplaceReconciliationService] Error healing Job #{$job->id}: {$err->getMessage()}");
            }
        }

        return [
            'scanned' => $jobs->count(),
            'repaired' => $repaired,
            'errors' => $errors,
        ];
    }

    /**
     * Audit company coin ledger against company_coins balance.
     *
     * @return array Analysis result
     */
    public function auditCompanyCoinConsistency(int $companyId): array
    {
        $currentBalance = (float) (DB::table('company_coins')
            ->where('company_id', $companyId)
            ->where('event_type', 'operational')
            ->value('balance_coin') ?? 0.00);

        $credits = (float) (DB::table('coin_ledger')
            ->where('company_id', $companyId)
            ->where('account_type', 'operational')
            ->where('entry_type', 'CREDIT')
            ->sum('amount') ?? 0.00);

        $debits = (float) (DB::table('coin_ledger')
            ->where('company_id', $companyId)
            ->where('account_type', 'operational')
            ->where('entry_type', 'DEBIT')
            ->sum('amount') ?? 0.00);

        $ledgerCalculated = round($credits - $debits, 2);

        return [
            'company_id' => $companyId,
            'current_balance' => $currentBalance,
            'ledger_credits' => $credits,
            'ledger_debits' => $debits,
            'ledger_calculated' => $ledgerCalculated,
        ];
    }
}
