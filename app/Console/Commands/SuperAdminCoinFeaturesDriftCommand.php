<?php

namespace App\Console\Commands;

use App\Services\ElasticCoinEventsIndex;
use App\Services\ElasticService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Class SuperAdminCoinFeaturesDriftCommand
 *
 * Intent: Reconciler and data integrity diagnostic CLI for the Super Admin Coin Features subsystem.
 * Compares MySQL authoritative records (coin_events) against Elasticsearch read documents (local_coin_events).
 * Detects missing records, extraneous records, and field drift, with automated healing capabilities.
 *
 * Signature: php artisan superadmin:coin-features:drift {--heal : Automatically reconcile and heal drift} {--json}
 *
 * Author: Antigravity AI — Super Admin Backend Architecture
 * Complies with docs/CODE_DOCUMENTATION_STANDARD.md.
 */
class SuperAdminCoinFeaturesDriftCommand extends Command
{
    protected $signature = 'superadmin:coin-features:drift 
                            {--heal : Automatically heal drift by reindexing missing/drifted records into Elasticsearch} 
                            {--json : Output drift results as JSON}';

    protected $description = 'Detect and reconcile data drift between MySQL coin_events and Elasticsearch local_coin_events';

    public function handle(ElasticCoinEventsIndex $elasticIndex, ElasticService $elasticService): int
    {
        $indexName = $elasticIndex->getIndexName();
        $elasticIndex->ensureIndexExists();

        $dbRecords = DB::table('coin_events')->orderBy('id', 'asc')->get()->keyBy('id');
        $dbCount = $dbRecords->count();

        // Fetch all ES records
        $esHits = [];
        $response = $elasticService->getClient()->search([
            'index' => $indexName,
            'body' => [
                'size' => 500,
                '_source' => ['id', 'event_type', 'title', 'default_rate', 'description', 'deduction_type', 'minimum_required', 'status'],
            ],
        ]);

        foreach ($response['hits']['hits'] ?? [] as $hit) {
            $id = (int) ($hit['_source']['id'] ?? $hit['_id']);
            $esHits[$id] = $hit['_source'];
        }

        $missingInEs = [];
        $missingInDb = [];
        $attributeDrifts = [];

        foreach ($dbRecords as $id => $dbRow) {
            if (! isset($esHits[$id])) {
                $missingInEs[] = (int) $id;

                continue;
            }

            $esDoc = $esHits[$id];
            $diffs = [];

            if ((string) ($esDoc['event_type'] ?? '') !== (string) $dbRow->event_type) {
                $diffs['event_type'] = ['db' => $dbRow->event_type, 'es' => $esDoc['event_type'] ?? null];
            }
            if ((int) ($esDoc['status'] ?? 0) !== (int) $dbRow->status) {
                $diffs['status'] = ['db' => (int) $dbRow->status, 'es' => (int) ($esDoc['status'] ?? 0)];
            }
            if (abs(((float) ($esDoc['default_rate'] ?? 0.0)) - ((float) $dbRow->default_rate)) > 0.001) {
                $diffs['default_rate'] = ['db' => (float) $dbRow->default_rate, 'es' => (float) ($esDoc['default_rate'] ?? 0.0)];
            }
            if (abs(((float) ($esDoc['minimum_required'] ?? 0.0)) - ((float) $dbRow->minimum_required)) > 0.001) {
                $diffs['minimum_required'] = ['db' => (float) $dbRow->minimum_required, 'es' => (float) ($esDoc['minimum_required'] ?? 0.0)];
            }
            if ((string) ($esDoc['deduction_type'] ?? '') !== (string) $dbRow->deduction_type) {
                $diffs['deduction_type'] = ['db' => $dbRow->deduction_type, 'es' => $esDoc['deduction_type'] ?? null];
            }

            if (! empty($diffs)) {
                $attributeDrifts[$id] = $diffs;
            }
        }

        foreach ($esHits as $id => $esDoc) {
            if (! isset($dbRecords[$id])) {
                $missingInDb[] = (int) $id;
            }
        }

        $driftCount = count($missingInEs) + count($missingInDb) + count($attributeDrifts);
        $healedCount = 0;

        if ($this->option('heal') && $driftCount > 0) {
            // Re-index missing in ES
            foreach ($missingInEs as $id) {
                if (isset($dbRecords[$id])) {
                    $elasticIndex->indexEvent($dbRecords[$id]);
                    $healedCount++;
                }
            }

            // Re-index attribute drifts
            foreach (array_keys($attributeDrifts) as $id) {
                if (isset($dbRecords[$id])) {
                    $elasticIndex->indexEvent($dbRecords[$id]);
                    $healedCount++;
                }
            }

            // Remove extraneous ES docs
            foreach ($missingInDb as $id) {
                $elasticIndex->deleteEvent($id);
                $healedCount++;
            }
        }

        $report = [
            'status' => ($driftCount === 0) ? 'IN_SYNC' : ($this->option('heal') ? 'HEALED' : 'DRIFT_DETECTED'),
            'timestamp' => now()->toIso8601String(),
            'database_count' => $dbCount,
            'elasticsearch_count' => count($esHits),
            'drift_count' => $driftCount,
            'missing_in_es' => $missingInEs,
            'missing_in_db' => $missingInDb,
            'attribute_drifts' => $attributeDrifts,
            'healed_count' => $healedCount,
        ];

        if ($this->option('json')) {
            $this->line(json_encode($report, JSON_PRETTY_PRINT));

            return self::SUCCESS;
        }

        $this->info('===============================================================');
        $this->info(" Super Admin Coin Features Drift Report: [{$report['status']}]");
        $this->info('===============================================================');
        $this->table(
            ['Metric', 'Value'],
            [
                ['MySQL Total Records', $dbCount],
                ['Elasticsearch Total Docs', count($esHits)],
                ['Missing in Elasticsearch', count($missingInEs).' ('.implode(', ', $missingInEs).')'],
                ['Missing in MySQL Database', count($missingInDb).' ('.implode(', ', $missingInDb).')'],
                ['Records with Attribute Drift', count($attributeDrifts).' ('.implode(', ', array_keys($attributeDrifts)).')'],
                ['Healed Records', $healedCount],
            ]
        );

        if ($driftCount > 0 && ! $this->option('heal')) {
            $this->warn('Run with --heal to automatically synchronize and eliminate drift.');
        }

        return ($driftCount > 0 && ! $this->option('heal')) ? self::FAILURE : self::SUCCESS;
    }
}
