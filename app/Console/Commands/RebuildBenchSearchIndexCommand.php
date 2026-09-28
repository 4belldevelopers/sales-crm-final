<?php

namespace App\Console\Commands;

use App\Services\ElasticService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Class RebuildBenchSearchIndexCommand
 *
 * Intent: Rebuilds bench Elasticsearch index from MySQL with zero search downtime via alias swap.
 * Scope: Service Company bench candidates search projection.
 */
class RebuildBenchSearchIndexCommand extends Command
{
    protected $signature = 'bench:rebuild-search-index {--batch=500 : Batch size for streaming}';

    protected $description = 'Rebuilds bench Elasticsearch index from MySQL with zero search downtime via alias swap';

    public function handle(ElasticService $elasticService): int
    {
        $client = $elasticService->getClient();
        $aliasName = defined('ELASTIC_CANDIDATES_BENCH') ? ELASTIC_CANDIDATES_BENCH : config('elasticsearch.indices.bench_candidates', 'candidates_bench');
        $newIndex = 'candidates_bench_' . time();

        $this->info("Creating new Elasticsearch index: {$newIndex}...");

        $mappingConfig = [
            'settings' => [
                'number_of_shards' => 1,
                'number_of_replicas' => 0,
            ],
            'mappings' => [
                'properties' => [
                    'id' => ['type' => 'long'],
                    'company_id' => ['type' => 'long'],
                    'full_name' => ['type' => 'text'],
                    'email' => ['type' => 'keyword'],
                    'phone_number' => ['type' => 'keyword'],
                    'status' => ['type' => 'integer'],
                    'aggregate_version' => ['type' => 'integer'],
                    'resume_version' => ['type' => 'integer'],
                    'assigned_recruiters' => ['type' => 'integer'],
                    'created_at' => ['type' => 'date'],
                ],
            ],
        ];

        try {
            $client->indices()->create([
                'index' => $newIndex,
                'body' => $mappingConfig,
            ]);
        } catch (\Throwable $e) {
            $this->error('Failed to create index: ' . $e->getMessage());
            return 1;
        }

        $batchSize = (int) $this->option('batch');
        $totalMigrated = 0;

        $this->info("Streaming candidates from MySQL in batches of {$batchSize}...");

        DB::table('job_applications')
            ->where('condidate_type', 'bench_condidate')
            ->orderBy('id')
            ->chunk($batchSize, function ($candidates) use ($client, $newIndex, &$totalMigrated) {
                $params = ['body' => []];
                foreach ($candidates as $cand) {
                    $assigned = DB::table('bench_assign')
                        ->where('bench_applications_id', $cand->id)
                        ->pluck('user_id')
                        ->map(fn($id) => (int) $id)
                        ->toArray();

                    $params['body'][] = [
                        'index' => [
                            '_index' => $newIndex,
                            '_id' => "bench_candidate:{$cand->id}",
                            '_version' => $cand->aggregate_version ?? 1,
                            'version_type' => 'external_gte',
                        ],
                    ];
                    $params['body'][] = [
                        'id' => $cand->id,
                        'company_id' => (int) $cand->company_id,
                        'full_name' => $cand->full_name,
                        'email' => $cand->email,
                        'phone_number' => $cand->phone,
                        'status' => (int) $cand->status,
                        'aggregate_version' => (int) ($cand->aggregate_version ?? 1),
                        'resume_version' => (int) ($cand->resume_version ?? 1),
                        'assigned_recruiters' => $assigned,
                        'created_at' => $cand->created_at,
                    ];
                }

                if (! empty($params['body'])) {
                    $client->bulk($params);
                    $totalMigrated += count($candidates);
                    $this->output->write('.');
                }
            });

        $this->newLine();
        $this->info("Migrated {$totalMigrated} documents. Performing atomic alias swap...");

        // Atomic Alias Swap
        $actions = [
            ['add' => ['index' => $newIndex, 'alias' => $aliasName]],
        ];

        try {
            if ($client->indices()->existsAlias(['name' => $aliasName])) {
                $existing = array_keys($client->indices()->getAlias(['name' => $aliasName]));
                foreach ($existing as $oldIndex) {
                    $actions[] = ['remove' => ['index' => $oldIndex, 'alias' => $aliasName]];
                }
            }

            $client->indices()->updateAliases(['body' => ['actions' => $actions]]);
            $this->info("Alias {$aliasName} now points to {$newIndex}. Zero downtime rebuild complete!");
        } catch (\Throwable $e) {
            $this->error('Failed to swap alias: ' . $e->getMessage());
            return 1;
        }

        return 0;
    }
}
