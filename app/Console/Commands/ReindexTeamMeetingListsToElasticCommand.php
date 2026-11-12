<?php

namespace App\Console\Commands;

use App\Services\ElasticService;
use App\Services\SuperAdmin\SuperAdminTeamMeetingCacheManager;
use App\Services\SuperAdmin\SuperAdminTeamMeetingReadService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Class ReindexTeamMeetingListsToElasticCommand
 *
 * Intent: Authoritatively synchronizes and denormalizes the Super Admin Microsoft Teams Meet Link Pool
 * into Elasticsearch index ELASTIC_TEAM_MEETING_LISTS (local_team_meeting_lists) and purges L2 Redis/Dragonfly caches.
 *
 * Signature: php artisan team-meeting:reindex {--fresh}
 *
 * Author: Antigravity AI — Super Admin Backend Architecture
 * Complies with docs/CODE_DOCUMENTATION_STANDARD.md and docs/AI_RULES.md.
 */
class ReindexTeamMeetingListsToElasticCommand extends Command
{
    protected $signature = 'team-meeting:reindex {--fresh : Drop and recreate the index}';

    protected $description = 'Reindex Super Admin Teams Meet links into Elasticsearch with complete schema verification';

    public function handle(ElasticService $elasticService, SuperAdminTeamMeetingReadService $readService): int
    {
        $this->info('Starting Super Admin Teams Meeting Links Elasticsearch reindexing...');

        $indexName = $readService->getIndexName();

        if ($this->option('fresh')) {
            if ($elasticService->indexExists($indexName)) {
                $this->warn("Dropping existing index {$indexName}...");
                $elasticService->getClient()->indices()->delete(['index' => $indexName]);
            }
        }

        if (! $elasticService->indexExists($indexName)) {
            $this->info("Elasticsearch index {$indexName} does not exist. Creating with complete mappings...");

            $elasticService->getClient()->indices()->create([
                'index' => $indexName,
                'body' => [
                    'settings' => [
                        'number_of_shards' => 1,
                        'number_of_replicas' => 0,
                        'index.requests.cache.enable' => true,
                    ],
                    'mappings' => $readService->getIndexMapping(),
                ],
            ]);

            $this->info("Created index {$indexName}.");
        }

        $records = DB::table('team_meeting_lists')->orderBy('id')->get();
        $total = $records->count();

        if ($total === 0) {
            $this->warn('No records found in MySQL `team_meeting_lists` table.');
            SuperAdminTeamMeetingCacheManager::flushCaches();

            return self::SUCCESS;
        }

        $this->info("Found {$total} meeting link(s) to index.");
        $progressBar = $this->output->createProgressBar($total);
        $progressBar->start();

        $chunk = [];
        $indexed = 0;

        foreach ($records as $record) {
            $doc = $readService->buildDocument($record, 1);

            $chunk['body'][] = [
                'index' => [
                    '_index' => $indexName,
                    '_id' => (string) $record->id,
                ],
            ];
            $chunk['body'][] = $doc;

            if (count($chunk['body']) >= 500) {
                $elasticService->getClient()->bulk($chunk);
                $indexed += count($chunk['body']) / 2;
                $progressBar->advance(count($chunk['body']) / 2);
                $chunk = [];
            }
        }

        if (! empty($chunk['body'])) {
            $elasticService->getClient()->bulk($chunk);
            $indexed += count($chunk['body']) / 2;
            $progressBar->advance(count($chunk['body']) / 2);
        }

        $progressBar->finish();
        $this->newLine();

        // Refresh index
        $elasticService->getClient()->indices()->refresh(['index' => $indexName]);

        $this->info("Successfully indexed {$indexed} meeting links into {$indexName}.");

        // Flush L2 caches
        SuperAdminTeamMeetingCacheManager::flushCaches();
        $this->info('Flushed L2 Dragonfly/Redis caches.');

        return self::SUCCESS;
    }
}
