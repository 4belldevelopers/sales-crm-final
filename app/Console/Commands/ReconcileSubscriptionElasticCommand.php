<?php

namespace App\Console\Commands;

use App\Helper\GeneralMethod;
use App\Services\ElasticCaompanyIndex;
use App\Services\ElasticService;
use App\Services\SuperAdmin\SuperAdminSubscriptionCacheManager;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Class ReconcileSubscriptionElasticCommand
 *
 * Intent: Automated reconciliation between authoritative MySQL tables (companies, company_coins, sys_outbox_events)
 * and the Elasticsearch read projection (local_companies_elastic).
 *
 * Identifies:
 *  - Missing company documents in Elasticsearch
 *  - Company status discrepancies (active/inactive)
 *  - Coin balance and subscription package drifts
 *  - Stale aggregate versions
 *  - Stuck or unprocessed transactional outbox events
 *
 * Provides safe, auditable repair operations via --repair.
 *
 * Usage:
 *   php artisan subscription:reconcile --dry-run
 *   php artisan subscription:reconcile --repair
 *   php artisan subscription:reconcile --chunk=100
 *
 * Author: Antigravity AI — Super Admin Backend Architecture (Phase 3 Hardening)
 * Complies with docs/CODE_DOCUMENTATION_STANDARD.md and docs/AI_RULES.md.
 */
class ReconcileSubscriptionElasticCommand extends Command
{
    protected $signature = 'subscription:reconcile 
                            {--dry-run : Detect and report discrepancies without applying modifications}
                            {--repair : Safely repair and synchronize detected discrepancies}
                            {--chunk=100 : Number of records to evaluate per chunk}
                            {--from-id=0 : Resume scanning from specific company ID}';

    protected $description = 'Automated reconciliation and self-healing between authoritative MySQL and Elasticsearch read projection for Super Admin Subscriptions';

    public function handle(ElasticService $elasticService, ElasticCaompanyIndex $indexer): int
    {
        $isDryRun = (bool) $this->option('dry-run') && ! $this->option('repair');
        $shouldRepair = (bool) $this->option('repair');
        $chunkSize = max(10, (int) $this->option('chunk'));
        $fromId = max(0, (int) $this->option('from-id'));

        $this->info('================================================================================');
        $this->info('   SUPER ADMIN SUBSCRIPTION CQRS RECONCILIATION & SELF-HEALING ENGINE');
        $this->info('================================================================================');
        $this->line(sprintf('Mode: <fg=%s>%s</>', $shouldRepair ? 'green' : 'yellow', $shouldRepair ? 'REPAIR & SYNCHRONIZE' : 'DRY-RUN / AUDIT ONLY'));
        $this->line("Chunk size: {$chunkSize}");
        if ($fromId > 0) {
            $this->line("Resuming from Company ID: {$fromId}");
        }

        $indexName = config('constants.ELASTIC_COMPANIES_INDEX', defined('ELASTIC_COMPANIES_INDEX') ? ELASTIC_COMPANIES_INDEX : 'local_companies_elastic');
        $client = $elasticService->getClient();

        if (! $elasticService->indexExists($indexName)) {
            $this->error("Elasticsearch index '{$indexName}' does not exist! Run 'php artisan subscription:reindex' first.");
            return Command::FAILURE;
        }

        $superAdminCompanyIds = GeneralMethod::superAdminCompanyIds();

        $query = DB::table('companies')
            ->whereNotIn('id', $superAdminCompanyIds);

        if ($fromId > 0) {
            $query->where('id', '>=', $fromId);
        }

        $query->orderBy('id', 'asc');

        $totalCompanies = $query->count();
        $this->info("Scanning {$totalCompanies} non-superadmin companies from MySQL against Elasticsearch...");

        $stats = [
            'total_scanned' => 0,
            'in_sync' => 0,
            'missing_doc' => 0,
            'status_mismatch' => 0,
            'coin_mismatch' => 0,
            'stale_version' => 0,
            'unprocessed_outbox' => 0,
            'repaired' => 0,
            'repair_errors' => 0,
        ];

        $discrepancies = [];
        $bar = $this->output->createProgressBar($totalCompanies);
        $bar->start();

        $query->chunk($chunkSize, function ($companies) use (
            &$stats,
            &$discrepancies,
            $client,
            $indexName,
            $indexer,
            $shouldRepair,
            $bar
        ) {
            $companyIds = $companies->pluck('id')->toArray();

            // Fetch Elasticsearch documents in batch using mget
            $esDocs = [];
            try {
                $mgetResponse = $client->mget([
                    'index' => $indexName,
                    'body' => [
                        'ids' => array_map('strval', $companyIds),
                    ],
                ]);

                foreach ($mgetResponse['docs'] ?? [] as $doc) {
                    if (! empty($doc['found'])) {
                        $esDocs[(int) $doc['_id']] = $doc['_source'] ?? [];
                    }
                }
            } catch (Throwable $e) {
                Log::warning("ReconcileSubscriptionElasticCommand: mget failed for batch: " . $e->getMessage());
            }

            // Fetch coin totals from MySQL for batch
            $mysqlCoinTotals = DB::table('company_coins')
                ->whereIn('company_id', $companyIds)
                ->groupBy('company_id')
                ->select('company_id', DB::raw('SUM(balance_coin) as total_balance'), DB::raw('COUNT(*) as total_records'))
                ->get()
                ->keyBy('company_id');

            // Fetch pending/failed outbox events for batch
            $outboxIssues = DB::table('sys_outbox_events')
                ->whereIn('aggregate_id', $companyIds)
                ->whereIn('status', ['FAILED', 'DEAD_LETTER'])
                ->groupBy('aggregate_id')
                ->select('aggregate_id', DB::raw('COUNT(*) as issue_count'))
                ->get()
                ->keyBy('aggregate_id');

            foreach ($companies as $company) {
                $stats['total_scanned']++;
                $cid = (int) $company->id;
                $esDoc = $esDocs[$cid] ?? null;

                $companyDiscrepancy = [];

                // 1. Check Missing Document
                if (! $esDoc) {
                    $stats['missing_doc']++;
                    $companyDiscrepancy[] = 'MISSING_IN_ELASTIC';
                } else {
                    // 2. Check Status Mismatch
                    $mysqlStatus = (string) $company->status;
                    $esStatus = (string) ($esDoc['status'] ?? '');
                    if ($mysqlStatus !== $esStatus) {
                        $stats['status_mismatch']++;
                        $companyDiscrepancy[] = "STATUS_MISMATCH(MySQL:{$mysqlStatus} vs ES:{$esStatus})";
                    }

                    // 3. Check Coin Balance & Records
                    $mysqlCoins = $mysqlCoinTotals[$cid] ?? null;
                    $mysqlTotalBalance = $mysqlCoins ? (float) $mysqlCoins->total_balance : 0.0;
                    $esCoinsArray = $esDoc['company_coins'] ?? [];
                    $esTotalBalance = 0.0;
                    if (is_array($esCoinsArray)) {
                        foreach ($esCoinsArray as $c) {
                            $esTotalBalance += (float) ($c['balance_coin'] ?? 0);
                        }
                    }

                    if (abs($mysqlTotalBalance - $esTotalBalance) > 0.01) {
                        $stats['coin_mismatch']++;
                        $companyDiscrepancy[] = "COIN_BALANCE_MISMATCH(MySQL:{$mysqlTotalBalance} vs ES:{$esTotalBalance})";
                    }

                    // 4. Check Aggregate Version Consistency
                    $redisVersion = (int) Cache::get("company:version:{$cid}", 1);
                    $esVersion = (int) ($esDoc['aggregate_version'] ?? 0);
                    if ($redisVersion > $esVersion && $esVersion > 0) {
                        $stats['stale_version']++;
                        $companyDiscrepancy[] = "STALE_VERSION(Redis:v{$redisVersion} > ES:v{$esVersion})";
                    }
                }

                // 5. Check Unprocessed/Dead-Letter Outbox Events
                if (isset($outboxIssues[$cid])) {
                    $stats['unprocessed_outbox']++;
                    $companyDiscrepancy[] = "OUTBOX_ISSUES({$outboxIssues[$cid]->issue_count} failed/dead events)";
                }

                if (! empty($companyDiscrepancy)) {
                    $discrepancies[$cid] = [
                        'company_id' => $cid,
                        'name' => $company->company_name ?? 'N/A',
                        'issues' => implode('; ', $companyDiscrepancy),
                    ];

                    // 6. Execute Self-Healing Repair if --repair requested
                    if ($shouldRepair) {
                        try {
                            // Concurrency Guard: Check if an outbox mutation is actively in-flight for this company
                            $isMutating = DB::table('sys_outbox_events')
                                ->where('aggregate_type', 'SubscriptionCompany')
                                ->where('aggregate_id', $cid)
                                ->where('status', 'PROCESSING')
                                ->where('locked_at', '>=', now()->subMinutes(5))
                                ->exists();

                            if ($isMutating) {
                                Log::warning("ReconcileSubscriptionElasticCommand: Skipping repair for company #{$cid} due to active in-flight mutation lease");
                                $stats['repair_errors']++;
                                continue;
                            }

                            $indexer->companyIndexElastic($cid);

                            // Resolve any dead-letter/failed outbox events since state is now authoritatively synced
                            if (isset($outboxIssues[$cid])) {
                                DB::table('sys_outbox_events')
                                    ->where('aggregate_id', $cid)
                                    ->whereIn('status', ['FAILED', 'DEAD_LETTER'])
                                    ->update([
                                        'status' => 'COMPLETED',
                                        'processed_at' => now(),
                                        'last_error' => 'Self-healed and reconciled authoritatively by subscription:reconcile',
                                    ]);
                            }

                            SuperAdminSubscriptionCacheManager::flushCaches($cid);
                            $stats['repaired']++;

                            Log::info("ReconcileSubscriptionElasticCommand: Repaired company #{$cid} ({$company->company_name})", [
                                'company_id' => $cid,
                                'resolved_issues' => $companyDiscrepancy,
                            ]);
                        } catch (Throwable $e) {
                            $stats['repair_errors']++;
                            Log::error("ReconcileSubscriptionElasticCommand: Repair failed for company #{$cid}: " . $e->getMessage());
                        }
                    }
                } else {
                    $stats['in_sync']++;
                }

                $bar->advance();
            }
        });

        $bar->finish();
        $this->newLine(2);

        // Display Summary Table
        $this->table(
            ['Metric', 'Count', 'Status'],
            [
                ['Total Companies Scanned', $stats['total_scanned'], 'INFO'],
                ['100% In Sync (Authoritative Match)', $stats['in_sync'], $stats['in_sync'] === $stats['total_scanned'] ? 'OK' : 'PARTIAL'],
                ['Missing ES Documents', $stats['missing_doc'], $stats['missing_doc'] > 0 ? 'CRITICAL' : 'OK'],
                ['Company Status Mismatches', $stats['status_mismatch'], $stats['status_mismatch'] > 0 ? 'WARNING' : 'OK'],
                ['Coin Balance Discrepancies', $stats['coin_mismatch'], $stats['coin_mismatch'] > 0 ? 'WARNING' : 'OK'],
                ['Stale Aggregate Versions', $stats['stale_version'], $stats['stale_version'] > 0 ? 'WARNING' : 'OK'],
                ['Failed/Dead-Letter Outbox Events', $stats['unprocessed_outbox'], $stats['unprocessed_outbox'] > 0 ? 'WARNING' : 'OK'],
                ['Successfully Repaired / Healed', $stats['repaired'], $stats['repaired'] > 0 ? 'REPAIRED' : 'NONE'],
                ['Repair Errors', $stats['repair_errors'], $stats['repair_errors'] > 0 ? 'ERROR' : 'OK'],
            ]
        );

        if (! empty($discrepancies)) {
            $this->warn(sprintf('Found %d companies with discrepancies:', count($discrepancies)));
            $sample = array_slice($discrepancies, 0, 15);
            $this->table(
                ['Company ID', 'Company Name', 'Discrepancy Details'],
                array_map(fn($d) => [$d['company_id'], $d['name'], $d['issues']], $sample)
            );
            if (count($discrepancies) > 15) {
                $this->line(sprintf('...and %d more. Run with --repair to heal all.', count($discrepancies) - 15));
            }
        } else {
            $this->info('PERFECT CONSISTENCY: 100% of MySQL companies, statuses, and coin balances match Elasticsearch read projections.');
        }

        // Flush L2 caches after repair
        if ($shouldRepair && $stats['repaired'] > 0) {
            SuperAdminSubscriptionCacheManager::flushCaches();
            $this->info('Global Redis / Dragonfly L2 caches flushed to reflect authoritative repairs.');
        }

        return Command::SUCCESS;
    }
}
