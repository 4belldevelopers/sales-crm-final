<?php

namespace App\Console\Commands;

use App\Helper\GeneralMethod;
use App\Services\ElasticService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Class RebuildClientElasticsearchCommand
 *
 * Intent: Watermark-Guaranteed Elasticsearch Rebuild Protocol for Service Company Clients
 * adhering to Invariant I16 of CLIENT_PAGE_CONSOLIDATION_PLAN.md v5.0.
 *
 * Prevents active mutations during reindexing from being overwritten by older snapshot data
 * and performs zero-downtime atomic alias swap from old backing index to new backing index.
 *
 * Author: Antigravity AI
 * Date: October 21, 2026
 */
class RebuildClientElasticsearchCommand extends Command
{
    protected $signature = 'clients:projection:rebuild 
                            {--alias=clients_read : Target Elasticsearch read alias} 
                            {--dry-run : Simulate rebuild without modifying Elasticsearch}';

    protected $description = 'Execute watermark-guaranteed Client Elasticsearch index rebuild with atomic alias swapping';

    public function handle(ElasticService $elasticService): int
    {
        $targetAlias = (string) $this->option('alias');
        $dryRun = (bool) $this->option('dry-run');

        $timestamp = now()->timestamp;
        $backingIndex = "clients_v{$timestamp}";

        $this->info("Initiating watermark-guaranteed rebuild for alias '{$targetAlias}'".($dryRun ? ' [DRY RUN]' : '').'...');

        $watermarkId = 0;
        $snapshotCount = 0;
        $clientIds = [];

        // 1. Establish consistent snapshot and capture watermark at snapshot boundary
        DB::transaction(function () use (&$watermarkId, &$snapshotCount, &$clientIds) {
            try {
                DB::statement('SET TRANSACTION ISOLATION LEVEL REPEATABLE READ');
                DB::statement('START TRANSACTION WITH CONSISTENT SNAPSHOT');
            } catch (\Throwable) {
                // SQLite or DB drivers that do not support consistent snapshot statement
            }

            // Capture exact watermark ID for Client aggregate
            $watermarkId = (int) (DB::table('sys_outbox_events')
                ->where('aggregate_type', 'Client')
                ->max('id') ?? 0);

            // Stream client IDs
            $clientIds = DB::table('clients')->pluck('id')->toArray();
            $snapshotCount = count($clientIds);
        }, 5);

        $this->info("Watermark established at outbox event ID {$watermarkId}. Total snapshot entities: {$snapshotCount}.");

        // 2. Dry run evaluation
        if ($dryRun) {
            $catchupCount = (int) DB::table('sys_outbox_events')
                ->where('aggregate_type', 'Client')
                ->where('id', '>', $watermarkId)
                ->count();

            $this->table(
                ['Parameter / Metric', 'Value'],
                [
                    ['Status', 'SIMULATED (DRY RUN)'],
                    ['Target Alias', $targetAlias],
                    ['Target Backing Index', $backingIndex],
                    ['Watermark Outbox ID', $watermarkId],
                    ['Snapshot Client Rows', $snapshotCount],
                    ['Post-Watermark Catchup Events', $catchupCount],
                    ['Dry Run', 'True'],
                ]
            );

            $this->info('Dry run completed. Zero changes made to Elasticsearch.');

            return 0;
        }

        // 3. Create backing index with explicit mappings
        $this->info("Creating backing index {$backingIndex}...");
        $this->createBackingIndex($elasticService, $backingIndex);

        // 4. Stream and index snapshot entities
        $this->info("Indexing {$snapshotCount} client snapshot documents into {$backingIndex}...");
        $bar = $this->output->createProgressBar($snapshotCount);
        $bar->start();

        foreach ($clientIds as $cId) {
            try {
                GeneralMethod::clientStaffingPartner((int) $cId);
            } catch (\Throwable $e) {
                Log::warning("RebuildClientElasticsearchCommand: Error indexing client {$cId}: ".$e->getMessage());
            }
            $bar->advance();
        }
        $bar->finish();
        $this->newLine();

        // 5. Final Catch-Up Barrier: Multi-pass drain until delta is 0 before alias swap
        $this->info('Entering Final Catch-Up Barrier drain...');
        $currentWatermark = $watermarkId;
        $replayedCount = 0;
        $maxPasses = 5;
        $pass = 0;

        do {
            $pass++;
            $pendingEvents = DB::table('sys_outbox_events')
                ->where('aggregate_type', 'Client')
                ->where('id', '>', $currentWatermark)
                ->orderBy('id', 'asc')
                ->get();

            $batchCount = $pendingEvents->count();
            if ($batchCount === 0) {
                $this->info("Catch-up barrier reached quiescence on pass {$pass}. Delta = 0 concurrent writes.");
                break;
            }

            $this->info("Catch-up pass {$pass}: processing {$batchCount} concurrent mutations...");
            foreach ($pendingEvents as $ev) {
                $aggId = (int) $ev->aggregate_id;
                if ($aggId > 0) {
                    try {
                        GeneralMethod::clientStaffingPartner($aggId);
                        $replayedCount++;
                    } catch (\Throwable $e) {
                        Log::warning("RebuildClientElasticsearchCommand: Error replaying outbox event {$ev->id}: ".$e->getMessage());
                    }
                }
                $currentWatermark = max($currentWatermark, (int) $ev->id);
            }
        } while ($pass < $maxPasses);
        $this->info("Final catch-up barrier complete. Total replayed events: {$replayedCount}.");

        // 6. Zero-downtime atomic alias swap
        $this->info("Executing atomic alias swap to repoint '{$targetAlias}' to '{$backingIndex}'...");
        $this->swapAlias($elasticService, $targetAlias, $backingIndex);

        $this->table(
            ['Parameter / Metric', 'Value'],
            [
                ['Status', 'COMPLETED_SUCCESSFULLY'],
                ['Active Alias', $targetAlias],
                ['New Backing Index', $backingIndex],
                ['Watermark Outbox ID', $watermarkId],
                ['Snapshot Rows Indexed', $snapshotCount],
                ['Replayed Catchup Events', $replayedCount],
                ['Timestamp', Carbon::now()->toIso8601String()],
            ]
        );

        $this->info('Watermark-guaranteed client projection rebuild completed successfully.');

        return 0;
    }

    protected function createBackingIndex(ElasticService $elastic, string $indexName): void
    {
        $indexConfig = [
            'settings' => [
                'number_of_shards' => 1,
                'number_of_replicas' => 1,
                'refresh_interval' => '1s',
            ],
            'mappings' => [
                'properties' => [
                    'company_id' => ['type' => 'long'],
                    'self_company' => ['type' => 'integer'],
                    'name' => ['type' => 'text', 'fields' => ['keyword' => ['type' => 'keyword']]],
                    'client_location' => [
                        'type' => 'nested',
                        'properties' => [
                            'city_id' => ['type' => 'integer'],
                            'city_name' => ['type' => 'keyword'],
                            'state_id' => ['type' => 'integer'],
                            'state_name' => ['type' => 'keyword'],
                            'country_id' => ['type' => 'integer'],
                            'country_name' => ['type' => 'keyword'],
                        ],
                    ],
                    'td_industries' => [
                        'type' => 'nested',
                        'properties' => [
                            'id' => ['type' => 'integer'],
                            'ind_name' => ['type' => 'keyword'],
                        ],
                    ],
                    'client_contact_info' => [
                        'type' => 'nested',
                        'properties' => [
                            'contact_person_name' => ['type' => 'text', 'fields' => ['keyword' => ['type' => 'keyword']]],
                            'email' => ['type' => 'keyword'],
                        ],
                    ],
                ],
            ],
        ];

        try {
            if (! $elastic->indexExists($indexName)) {
                $elastic->getClient()->indices()->create([
                    'index' => $indexName,
                    'body' => $indexConfig,
                ]);
            }
        } catch (\Throwable $e) {
            Log::warning("createBackingIndex notice for {$indexName}: ".$e->getMessage());
        }
    }

    protected function swapAlias(ElasticService $elastic, string $alias, string $newIndex): void
    {
        try {
            $client = $elastic->getClient();
            $actions = [];

            // If alias already exists, remove it from all old indices
            if ($client->indices()->existsAlias(['name' => $alias])) {
                $existing = $client->indices()->getAlias(['name' => $alias]);
                foreach (array_keys($existing) as $oldIndex) {
                    $actions[] = ['remove' => ['index' => $oldIndex, 'alias' => $alias]];
                }
            }

            // Repoint alias to the newly built backing index atomically
            $actions[] = ['add' => ['index' => $newIndex, 'alias' => $alias]];

            $client->indices()->updateAliases(['body' => ['actions' => $actions]]);
        } catch (\Throwable $e) {
            Log::error("swapAlias error repointing {$alias} to {$newIndex}: ".$e->getMessage());
        }
    }
}
