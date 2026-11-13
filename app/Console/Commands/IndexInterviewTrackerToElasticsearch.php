<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\ElasticService;

/**
 * Command to initialize and index ELASTIC_INERTVIEW_TRACKER (local_interview_tracker_elastic).
 *
 * INTENT: Provides explicit schema mapping for interview schedule tracking.
 * TENANCY: Scoped by from_company_id and to_company_id.
 */
class IndexInterviewTrackerToElasticsearch extends Command
{
    protected $signature = 'elasticsearch:index-interview-tracker
                            {--fresh : Delete and recreate the index}
                            {--reindex : Re-index all interview schedules from MySQL into Elasticsearch}';

    protected $description = 'Create and index ELASTIC_INERTVIEW_TRACKER with explicit date and tenant mappings';

    public function handle()
    {
        $elastic = new ElasticService();
        $indexName = ELASTIC_INERTVIEW_TRACKER;

        if ($this->option('fresh') && $elastic->indexExists($indexName)) {
            $elastic->getClient()->indices()->delete(['index' => $indexName]);
            $this->info("Deleted existing index: {$indexName}");
        }

        if (!$elastic->indexExists($indexName)) {
            $this->createIndexWithMappings($elastic, $indexName);
            $this->info("Created index {$indexName}.");
        } else {
            $this->info("Index {$indexName} already exists.");
        }

        if ($this->option('reindex')) {
            $this->info("Starting reindexing of all interview schedules...");
            $count = 0;
            \App\Models\InterviewSchedule::chunk(100, function ($schedules) use (&$count) {
                foreach ($schedules as $schedule) {
                    \App\Helpers\GeneralMethod::interviewTrackerAddEditUpload($schedule->id);
                    $count++;
                }
                $this->info("Indexed {$count} interview schedules so far...");
            });
            $this->info("Completed reindexing {$count} interview schedules into {$indexName}.");
        }
    }

    protected function createIndexWithMappings(ElasticService $elastic, string $indexName): void
    {
        $indexConfig = [
            'settings' => [
                'number_of_shards'   => 1,
                'number_of_replicas' => 1,
            ],
            'mappings' => [
                'properties' => [
                    'id'              => ['type' => 'integer'],
                    'job_id'          => ['type' => 'integer'],
                    'candidate_id'    => ['type' => 'integer'],
                    'from_company_id' => ['type' => 'integer'],
                    'to_company_id'   => ['type' => 'integer'],
                    'schedule_date'   => [
                        'type'   => 'date',
                        'format' => 'strict_date_optional_time||epoch_millis',
                    ],
                    'created_at'      => [
                        'type'   => 'date',
                        'format' => 'strict_date_optional_time||epoch_millis',
                    ],
                    'updated_at'      => [
                        'type'   => 'date',
                        'format' => 'strict_date_optional_time||epoch_millis',
                    ],
                ],
            ],
        ];

        $elastic->getClient()->indices()->create([
            'index' => $indexName,
            'body'  => $indexConfig,
        ]);
    }
}
