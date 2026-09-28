<?php

namespace App\Console\Commands;

use App\Services\CandidateElasticRebuildService;
use Illuminate\Console\Command;

/**
 * Class RebuildCandidateElasticsearchCommand
 *
 * Intent: Watermark-Guaranteed Elasticsearch Rebuild Command per docs/drawer_candidate.md.
 */
class RebuildCandidateElasticsearchCommand extends Command
{
    protected $signature = 'candidates:rebuild-watermark {--alias=candidates_index} {--dry-run}';

    protected $description = 'Execute watermark-guaranteed candidate Elasticsearch index rebuild';

    public function handle(CandidateElasticRebuildService $rebuildService): int
    {
        $alias = (string) $this->option('alias');
        $dryRun = (bool) $this->option('dry-run');

        $this->info("Initiating watermark-guaranteed rebuild for alias '{$alias}'".($dryRun ? ' (DRY RUN)' : '').'...');

        $result = $rebuildService->executeRebuild($alias, $dryRun);

        $this->table(
            ['Metric / Parameter', 'Value'],
            [
                ['Status', $result['status']],
                ['Backing Index', $result['index_name']],
                ['Watermark Outbox ID', $result['watermark_id']],
                ['Snapshot Rows Streamed', $result['snapshot_count']],
                ['Replayed Catchup Events', $result['replayed_events_count']],
                ['Target Alias', $result['alias']],
                ['Dry Run Mode', $result['dry_run'] ? 'Yes' : 'No'],
            ]
        );

        $this->info('Watermark rebuild protocol completed successfully.');

        return 0;
    }
}
