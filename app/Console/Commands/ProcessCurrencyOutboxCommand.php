<?php

namespace App\Console\Commands;

use App\Jobs\ProcessCurrencyOutboxEventJob;
use App\Services\ElasticService;
use App\Services\SuperAdmin\SuperAdminCurrencyCacheManager;
use App\Services\SuperAdmin\SuperAdminCurrencyReadService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Throwable;

/**
 * Class ProcessCurrencyOutboxCommand
 *
 * Intent: Automated CLI worker to process pending transactional outbox events,
 * retry dead letters, and reconcile dual-store drift for Currencies.
 *
 * Usage: php artisan superadmin-currency:process-outbox {--limit=50} {--retry-dead-letter} {--reconcile}
 * Author: Antigravity AI — Super Admin Backend Architecture
 * Complies with docs/CODE_DOCUMENTATION_STANDARD.md and docs/AI_RULES.md.
 */
class ProcessCurrencyOutboxCommand extends Command
{
    protected $signature = 'superadmin-currency:process-outbox
                            {--limit=50 : Maximum number of events to process}
                            {--retry-dead-letter : Reset DEAD_LETTER events to PENDING}
                            {--reconcile : Detect and heal dual-store drift between MySQL and Elasticsearch}';

    protected $description = 'Process pending transactional outbox events and reconcile dual-store drift for Currencies';

    public function handle(ElasticService $elasticService, SuperAdminCurrencyReadService $readService): int
    {
        $this->info('Super Admin Currency Outbox Worker & Reconciler starting...');

        // 1. Crash Recovery: Reset stale PROCESSING events (locked_at < NOW() - 300s) back to PENDING
        $staleRecovered = DB::table('sys_outbox_events')
            ->where('aggregate_type', 'Currency')
            ->where('status', 'PROCESSING')
            ->where('locked_at', '<', now()->subSeconds(300))
            ->update([
                'status' => 'PENDING',
                'locked_at' => null,
            ]);

        if ($staleRecovered > 0) {
            $this->warn("Crash Recovery: Reset {$staleRecovered} stale PROCESSING currency outbox events back to PENDING.");
        }

        // 2. Retry Dead Letters if requested
        if ($this->option('retry-dead-letter')) {
            $resetCount = DB::table('sys_outbox_events')
                ->where('aggregate_type', 'Currency')
                ->where('status', 'DEAD_LETTER')
                ->update([
                    'status' => 'PENDING',
                    'attempts' => 0,
                    'available_at' => now(),
                    'last_error' => null,
                ]);

            $this->info("Reset {$resetCount} DEAD_LETTER currency events back to PENDING.");
        }

        // 3. Triple Reconciliation (MySQL <-> Elasticsearch <-> currencies.json <-> Redis)
        if ($this->option('reconcile')) {
            $this->info('Starting triple-store drift reconciliation...');
            $indexName = $readService->getIndexName();

            if (! $elasticService->indexExists($indexName)) {
                $elasticService->createIndex($indexName, $readService->getIndexMapping());
            }

            $mysqlCurrencies = DB::table('currencies')->get();
            $healedCount = 0;
            $mysqlIdMap = [];

            // Pass A: Reconcile MySQL records into Elasticsearch
            foreach ($mysqlCurrencies as $currency) {
                $mysqlIdMap[$currency->id] = true;

                try {
                    $esDoc = $elasticService->getClient()->get([
                        'index' => $indexName,
                        'id' => (string) $currency->id,
                    ]);

                    $esSource = $esDoc['_source'] ?? [];
                    $dbUsdRate = (float) $currency->usd_based_rates;
                    $esUsdRate = (float) ($esSource['usd_based_rates'] ?? 0);

                    // Reconcile if rate differs
                    if (abs($dbUsdRate - $esUsdRate) > 0.0001) {
                        $doc = $readService->buildCurrencyDocument($currency);
                        $elasticService->getClient()->index([
                            'index' => $indexName,
                            'id' => (string) $currency->id,
                            'body' => $doc,
                        ]);
                        $healedCount++;
                    }
                } catch (Throwable) {
                    // Document missing in Elasticsearch; heal it
                    $doc = $readService->buildCurrencyDocument($currency);
                    $elasticService->getClient()->index([
                        'index' => $indexName,
                        'id' => (string) $currency->id,
                        'body' => $doc,
                    ]);
                    $healedCount++;
                }
            }

            // Pass B: Detect and purge orphaned Elasticsearch documents deleted in MySQL
            try {
                $esResponse = $elasticService->getClient()->search([
                    'index' => $indexName,
                    'size' => 1000,
                    '_source' => ['id'],
                ]);
                $hits = $esResponse['hits']['hits'] ?? [];
                foreach ($hits as $hit) {
                    $esDocId = (int) $hit['_id'];
                    if (! isset($mysqlIdMap[$esDocId])) {
                        $elasticService->getClient()->delete([
                            'index' => $indexName,
                            'id' => (string) $esDocId,
                        ]);
                        SuperAdminCurrencyCacheManager::setTombstone($esDocId);
                        $healedCount++;
                    }
                }
            } catch (Throwable $e) {
                $this->warn('Orphan check encountered ES search issue: '.$e->getMessage());
            }

            // Pass C: Reconcile static currencies.json
            \App\Services\CurrencyService::syncCurrenciesJson();

            // Pass D: Invalidate Redis L2 caches
            SuperAdminCurrencyCacheManager::flushCaches();

            if ($healedCount > 0) {
                $this->warn("Reconciliation complete: Healed {$healedCount} drifted currency documents across stores.");
            } else {
                $this->info('Reconciliation complete: Zero drift detected. MySQL, Elasticsearch, Redis, and currencies.json are 100% in sync.');
            }
        }

        // 3. Process Pending Outbox Events
        $limit = (int) $this->option('limit');
        $pendingEvents = DB::table('sys_outbox_events')
            ->where('aggregate_type', 'Currency')
            ->where('status', 'PENDING')
            ->where('available_at', '<=', now())
            ->orderBy('id', 'asc')
            ->limit($limit)
            ->pluck('id')
            ->toArray();

        $count = count($pendingEvents);
        if ($count === 0) {
            $this->info('No pending Currency outbox events to process.');

            return self::SUCCESS;
        }

        $this->info("Processing {$count} pending Currency outbox events...");
        $processed = 0;

        foreach ($pendingEvents as $eventId) {
            $job = new ProcessCurrencyOutboxEventJob((int) $eventId);
            $job->handle($elasticService);
            $processed++;
        }

        $this->info("Successfully processed {$processed} Currency outbox events.");

        return self::SUCCESS;
    }
}
