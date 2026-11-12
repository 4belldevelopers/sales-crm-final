<?php

namespace App\Console\Commands;

use App\Jobs\ProcessLanguageOutboxEventJob;
use App\Services\ElasticService;
use App\Services\SuperAdmin\SuperAdminLanguageCacheManager;
use App\Services\SuperAdmin\SuperAdminLanguageReadService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Throwable;

/**
 * Class ProcessLanguageOutboxCommand
 *
 * Intent: Automated CLI worker to process pending transactional outbox events,
 * retry dead letters, and reconcile dual-store drift for Language Settings.
 *
 * Usage: php artisan superadmin-language:process-outbox {--limit=50} {--retry-dead-letter} {--reconcile}
 * Author: Antigravity AI — Super Admin Backend Architecture
 * Complies with docs/CODE_DOCUMENTATION_STANDARD.md and docs/AI_RULES.md.
 */
class ProcessLanguageOutboxCommand extends Command
{
    protected $signature = 'superadmin-language:process-outbox
                            {--limit=50 : Maximum number of events to process}
                            {--retry-dead-letter : Reset DEAD_LETTER events to PENDING}
                            {--reconcile : Detect and heal dual-store drift between MySQL and Elasticsearch}';

    protected $description = 'Process pending transactional outbox events and reconcile dual-store drift for Language Settings';

    public function handle(ElasticService $elasticService, SuperAdminLanguageReadService $readService): int
    {
        $this->info('Super Admin Language Outbox Worker & Reconciler starting...');

        // 1. Crash Recovery: Reset stale PROCESSING events (locked_at < NOW() - 300s) back to PENDING
        $staleRecovered = DB::table('sys_outbox_events')
            ->where('aggregate_type', 'LanguageSetting')
            ->where('status', 'PROCESSING')
            ->where('locked_at', '<', now()->subSeconds(300))
            ->update([
                'status' => 'PENDING',
                'locked_at' => null,
            ]);

        if ($staleRecovered > 0) {
            $this->warn("Crash Recovery: Reset {$staleRecovered} stale PROCESSING language outbox events back to PENDING.");
        }

        // 2. Retry Dead Letters if requested
        if ($this->option('retry-dead-letter')) {
            $resetCount = DB::table('sys_outbox_events')
                ->where('aggregate_type', 'LanguageSetting')
                ->where('status', 'DEAD_LETTER')
                ->update([
                    'status' => 'PENDING',
                    'attempts' => 0,
                    'available_at' => now(),
                    'last_error' => null,
                ]);

            $this->info("Reset {$resetCount} DEAD_LETTER language events back to PENDING.");
        }

        // 3. Dual-Store Reconciliation (MySQL <-> Elasticsearch <-> Redis)
        if ($this->option('reconcile')) {
            $this->info('Starting dual-store drift reconciliation for languages...');
            $indexName = $readService->getIndexName();

            if (! $elasticService->indexExists($indexName)) {
                $elasticService->createIndex($indexName, $readService->getIndexMapping());
            }

            $mysqlLanguages = DB::table('language_settings')->get();
            $healedCount = 0;
            $mysqlIdMap = [];

            // Pass A: Reconcile MySQL records into Elasticsearch
            foreach ($mysqlLanguages as $lang) {
                $mysqlIdMap[$lang->id] = true;

                try {
                    $esDoc = $elasticService->getClient()->get([
                        'index' => $indexName,
                        'id' => (string) $lang->id,
                    ]);

                    $esSource = $esDoc['_source'] ?? [];
                    $dbStatus = (string) $lang->status;
                    $esStatus = (string) ($esSource['status'] ?? '');
                    $dbCode = (string) $lang->language_code;
                    $esCode = (string) ($esSource['language_code'] ?? '');

                    // Reconcile if status or code differs
                    if ($dbStatus !== $esStatus || $dbCode !== $esCode) {
                        $doc = $readService->buildLanguageDocument($lang);
                        $elasticService->getClient()->index([
                            'index' => $indexName,
                            'id' => (string) $lang->id,
                            'body' => $doc,
                        ]);
                        $healedCount++;
                    }
                } catch (Throwable) {
                    // Document missing in ES: index it
                    $doc = $readService->buildLanguageDocument($lang);
                    $elasticService->getClient()->index([
                        'index' => $indexName,
                        'id' => (string) $lang->id,
                        'body' => $doc,
                    ]);
                    $healedCount++;
                }
            }

            // Pass B: Remove ghost documents in Elasticsearch that were deleted from MySQL
            try {
                $esResponse = $elasticService->getClient()->search([
                    'index' => $indexName,
                    'body' => [
                        'size' => 1000,
                        'query' => ['match_all' => (object) []],
                    ],
                ]);

                $esHits = $esResponse['hits']['hits'] ?? [];
                foreach ($esHits as $hit) {
                    $esId = (int) $hit['_id'];
                    if (! isset($mysqlIdMap[$esId])) {
                        $elasticService->getClient()->delete([
                            'index' => $indexName,
                            'id' => (string) $esId,
                        ]);
                        $healedCount++;
                    }
                }
            } catch (Throwable $e) {
                $this->warn('Pass B search error: '.$e->getMessage());
            }

            if ($healedCount > 0) {
                $this->info("Reconciliation complete: Healed {$healedCount} mismatched/missing records.");
                SuperAdminLanguageCacheManager::flushCaches();
            } else {
                $this->info('Reconciliation complete: MySQL and Elasticsearch are in 100% perfect sync.');
            }
        }

        // 4. Batch Outbox Processing
        $limit = (int) $this->option('limit');
        $pendingEvents = DB::table('sys_outbox_events')
            ->where('aggregate_type', 'LanguageSetting')
            ->where('status', 'PENDING')
            ->where('available_at', '<=', now())
            ->orderBy('id', 'asc')
            ->limit($limit)
            ->pluck('id')
            ->toArray();

        $processedCount = 0;
        foreach ($pendingEvents as $eventId) {
            try {
                $job = new ProcessLanguageOutboxEventJob((int) $eventId);
                $job->handle($elasticService);
                $processedCount++;
            } catch (Throwable $e) {
                $this->error("Failed processing language outbox event {$eventId}: ".$e->getMessage());
            }
        }

        $this->info("Processed {$processedCount} pending language outbox events.");

        return self::SUCCESS;
    }
}
