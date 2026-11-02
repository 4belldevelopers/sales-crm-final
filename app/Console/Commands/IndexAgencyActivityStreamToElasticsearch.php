<?php

namespace App\Console\Commands;

use App\Models\AgencyActivityLog;
use App\Services\ElasticService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Command: IndexAgencyActivityStreamToElasticsearch
 *
 * Intent: Initializes the Elasticsearch index for the Agency 360° Activity Stream.
 * Also provides a historical backfill engine from recommendation_relation,
 * job_applications, updatetimeline, and interview_schedules into agency_activity_logs
 * and Elasticsearch.
 */
class IndexAgencyActivityStreamToElasticsearch extends Command
{
    protected $signature = 'elasticsearch:index-agency-activity-stream
                            {--fresh : Delete and recreate index}
                            {--backfill : Backfill historical events from database into activity logs & Elasticsearch}';

    protected $description = 'Initializes and backfills the Agency Activity Stream in Elasticsearch';

    public function handle(ElasticService $elastic): int
    {
        $indexName = defined('ELASTIC_AGENCY_ACTIVITY_STREAM') ? ELASTIC_AGENCY_ACTIVITY_STREAM : 'local_agency_activity_stream';

        if ($this->option('fresh') && $elastic->indexExists($indexName)) {
            $elastic->getClient()->indices()->delete(['index' => $indexName]);
            $this->info("Deleted existing index: {$indexName}");
        }

        if (! $elastic->indexExists($indexName)) {
            $this->createIndexWithMappings($elastic, $indexName);
            $this->info("Created index: {$indexName}");
        } else {
            $this->info("Index already exists: {$indexName}");
        }

        if ($this->option('backfill')) {
            $this->backfillHistoricalEvents($elastic, $indexName);
        }

        return Command::SUCCESS;
    }

    protected function createIndexWithMappings(ElasticService $elastic, string $indexName): void
    {
        $params = [
            'index' => $indexName,
            'body' => [
                'settings' => [
                    'number_of_shards' => 1,
                    'number_of_replicas' => 1,
                    'refresh_interval' => '1s',
                ],
                'mappings' => [
                    'properties' => [
                        'event_uuid' => ['type' => 'keyword'],
                        'agency_company_id' => ['type' => 'integer'],
                        'service_company_id' => ['type' => 'integer'],
                        'category' => ['type' => 'keyword'],
                        'event_type' => ['type' => 'keyword'],
                        'event_title' => [
                            'type' => 'text',
                            'fields' => ['keyword' => ['type' => 'keyword']],
                        ],
                        'event_description' => ['type' => 'text'],
                        'actor_id' => ['type' => 'integer'],
                        'actor_name' => [
                            'type' => 'text',
                            'fields' => ['keyword' => ['type' => 'keyword']],
                        ],
                        'actor_role' => ['type' => 'keyword'],
                        'source' => ['type' => 'keyword'],
                        'entity_type' => ['type' => 'keyword'],
                        'entity_id' => ['type' => 'long'],
                        'related_job_id' => ['type' => 'integer'],
                        'related_job_title' => [
                            'type' => 'text',
                            'fields' => ['keyword' => ['type' => 'keyword']],
                        ],
                        'related_candidate_id' => ['type' => 'long'],
                        'related_candidate_name' => [
                            'type' => 'text',
                            'fields' => ['keyword' => ['type' => 'keyword']],
                        ],
                        'related_client_id' => ['type' => 'integer'],
                        'related_client_name' => [
                            'type' => 'text',
                            'fields' => ['keyword' => ['type' => 'keyword']],
                        ],
                        'previous_value' => ['type' => 'object', 'enabled' => false],
                        'new_value' => ['type' => 'object', 'enabled' => false],
                        'metadata' => ['type' => 'object', 'enabled' => false],
                        'visibility_scope' => ['type' => 'keyword'],
                        'agency_activity_sequence' => ['type' => 'long'],
                        'created_at' => [
                            'type' => 'date',
                            'format' => 'strict_date_optional_time||epoch_millis',
                        ],
                    ],
                ],
            ],
        ];

        $elastic->getClient()->indices()->create($params);
    }

    protected function backfillHistoricalEvents(ElasticService $elastic, string $indexName): void
    {
        $this->info('Starting historical activity backfill...');

        $userMap = DB::table('users')->pluck('name', 'id')->toArray();
        $companyMap = DB::table('companies')->pluck('company_name', 'id')->toArray();
        $jobMap = DB::table('jobs')->select('id', 'title', 'company_id', 'client_id')->get()->keyBy('id');
        $clientMap = DB::table('clients')->pluck('name', 'id')->toArray();

        $insertedCount = 0;
        $batch = [];

        // 1. Backfill Job Shares from recommendation_relation
        $relations = DB::table('recommendation_relation')->orderBy('id', 'asc')->get();
        foreach ($relations as $rel) {
            $agencyId = (int) $rel->to_company_id;
            $serviceCompanyId = (int) $rel->from_company_id;
            $job = $jobMap->get($rel->job_id);
            $jobTitle = $job ? $job->title : "Job #{$rel->job_id}";
            $clientName = ($job && ! empty($job->client_id) && isset($clientMap[$job->client_id]))
                ? $clientMap[$job->client_id]
                : ($companyMap[$serviceCompanyId] ?? 'Employer');
            $sharerName = $userMap[$rel->from_user_id] ?? 'Account Lead';

            $uuid = (string) Str::uuid();
            $createdAt = ! empty($rel->created_at) ? Carbon::parse($rel->created_at)->toIso8601String() : now()->toIso8601String();
            $timestamp = Carbon::parse($createdAt)->timestamp;

            $statusText = match ((int) $rel->final_status) {
                1 => 'Job shared and accepted',
                2 => 'Job reciprocal share approved',
                3 => 'Job share declined',
                default => 'Job requisition shared',
            };

            $eventData = [
                'event_uuid' => $uuid,
                'agency_company_id' => $agencyId,
                'service_company_id' => $serviceCompanyId,
                'category' => 'JOBS',
                'event_type' => 'job.shared',
                'event_title' => "Requirement shared: {$jobTitle}",
                'event_description' => "{$sharerName} shared requirement '{$jobTitle}' with {$statusText}.",
                'actor_id' => (int) ($rel->from_user_id ?? 0),
                'actor_name' => $sharerName,
                'actor_role' => 'account_lead',
                'source' => 'INTERNAL_ATS',
                'entity_type' => 'Job',
                'entity_id' => (int) $rel->job_id,
                'related_job_id' => (int) $rel->job_id,
                'related_job_title' => $jobTitle,
                'related_client_id' => $job ? (int) $job->client_id : null,
                'related_client_name' => $clientName,
                'visibility_scope' => 'PUBLIC',
                'created_at' => $createdAt,
                'agency_activity_sequence' => $timestamp * 1000 + ($rel->id % 1000),
            ];

            $batch[] = $eventData;
            $insertedCount++;

            if (count($batch) >= 100) {
                $this->flushBatch($batch, $elastic, $indexName);
                $batch = [];
            }
        }

        // 2. Backfill Candidate Submissions
        $candidates = DB::table('job_applications')
            ->whereNotNull('thirdparty_company_id')
            ->where('thirdparty_company_id', '>', 0)
            ->orderBy('id', 'asc')
            ->get();

        foreach ($candidates as $cand) {
            $agencyId = (int) $cand->thirdparty_company_id;
            $serviceCompanyId = (int) $cand->company_id;
            $job = $jobMap->get($cand->job_id);
            $jobTitle = $job ? $job->title : "Job #{$cand->job_id}";
            $clientName = ($job && ! empty($job->client_id) && isset($clientMap[$job->client_id]))
                ? $clientMap[$job->client_id]
                : ($companyMap[$serviceCompanyId] ?? 'Employer');
            $recruiterName = $userMap[$cand->submitted_by] ?? ($companyMap[$agencyId] ?? 'Agency Recruiter');

            $uuid = (string) Str::uuid();
            $createdAt = ! empty($cand->created_at) ? Carbon::parse($cand->created_at)->toIso8601String() : now()->toIso8601String();
            $timestamp = Carbon::parse($createdAt)->timestamp;

            $candName = ucwords($cand->full_name ?? "Candidate #{$cand->id}");

            $eventData = [
                'event_uuid' => $uuid,
                'agency_company_id' => $agencyId,
                'service_company_id' => $serviceCompanyId,
                'category' => 'CANDIDATES',
                'event_type' => 'candidate.submitted',
                'event_title' => "Candidate Submitted: {$candName}",
                'event_description' => "{$recruiterName} submitted candidate {$candName} for {$jobTitle}.",
                'actor_id' => (int) ($cand->submitted_by ?? 0),
                'actor_name' => $recruiterName,
                'actor_role' => 'agency_recruiter',
                'source' => 'AGENCY_PORTAL',
                'entity_type' => 'Candidate',
                'entity_id' => (int) $cand->id,
                'related_job_id' => (int) $cand->job_id,
                'related_job_title' => $jobTitle,
                'related_candidate_id' => (int) $cand->id,
                'related_candidate_name' => $candName,
                'related_client_id' => $job ? (int) $job->client_id : null,
                'related_client_name' => $clientName,
                'visibility_scope' => 'PUBLIC',
                'created_at' => $createdAt,
                'agency_activity_sequence' => $timestamp * 1000 + ($cand->id % 1000),
            ];

            $batch[] = $eventData;
            $insertedCount++;

            if (count($batch) >= 100) {
                $this->flushBatch($batch, $elastic, $indexName);
                $batch = [];
            }
        }

        // 3. Backfill Interviews
        $interviews = DB::table('interview_schedules')
            ->join('job_applications', 'job_applications.id', '=', 'interview_schedules.job_application_id')
            ->whereNotNull('job_applications.thirdparty_company_id')
            ->where('job_applications.thirdparty_company_id', '>', 0)
            ->select('interview_schedules.*', 'job_applications.thirdparty_company_id', 'job_applications.full_name as cand_name', 'job_applications.job_id as app_job_id')
            ->orderBy('interview_schedules.id', 'asc')
            ->get();

        foreach ($interviews as $it) {
            $agencyId = (int) $it->thirdparty_company_id;
            $serviceCompanyId = (int) $it->company_id;
            $jobId = (int) ($it->app_job_id ?: 0);
            $job = $jobMap->get($jobId);
            $jobTitle = $job ? $job->title : "Job #{$jobId}";
            $candName = ucwords($it->cand_name ?? 'Candidate');
            $clientName = ($job && ! empty($job->client_id) && isset($clientMap[$job->client_id]))
                ? $clientMap[$job->client_id]
                : ($companyMap[$serviceCompanyId] ?? 'Employer');

            $uuid = (string) Str::uuid();
            $createdAt = ! empty($it->created_at) ? Carbon::parse($it->created_at)->toIso8601String() : now()->toIso8601String();
            $timestamp = Carbon::parse($createdAt)->timestamp;

            $eventData = [
                'event_uuid' => $uuid,
                'agency_company_id' => $agencyId,
                'service_company_id' => $serviceCompanyId,
                'category' => 'INTERVIEWS',
                'event_type' => 'interview.scheduled',
                'event_title' => "Interview Scheduled: {$candName}",
                'event_description' => "Technical evaluation scheduled for {$candName} ({$jobTitle}) on {$it->schedule_date} at {$it->schedule_from_time}.",
                'actor_id' => 0,
                'actor_name' => 'Recruitment Operations',
                'actor_role' => 'recruiter',
                'source' => 'INTERNAL_ATS',
                'entity_type' => 'Interview',
                'entity_id' => (int) $it->id,
                'related_job_id' => $jobId,
                'related_job_title' => $jobTitle,
                'related_candidate_id' => (int) $it->job_application_id,
                'related_candidate_name' => $candName,
                'related_client_id' => $job ? (int) $job->client_id : null,
                'related_client_name' => $clientName,
                'visibility_scope' => 'PUBLIC',
                'created_at' => $createdAt,
                'agency_activity_sequence' => $timestamp * 1000 + ($it->id % 1000),
            ];

            $batch[] = $eventData;
            $insertedCount++;

            if (count($batch) >= 100) {
                $this->flushBatch($batch, $elastic, $indexName);
                $batch = [];
            }
        }

        // Flush remaining
        if (! empty($batch)) {
            $this->flushBatch($batch, $elastic, $indexName);
        }

        $this->info("Successfully backfilled {$insertedCount} activities into MySQL and Elasticsearch!");
    }

    protected function flushBatch(array $batch, ElasticService $elastic, string $indexName): void
    {
        // 1. Bulk insert to MySQL agency_activity_logs (write store) with strict uniform column normalization
        $dbRows = array_map(function ($ev) {
            $createdAt = Carbon::parse($ev['created_at'])->format('Y-m-d H:i:s');

            return [
                'event_uuid' => $ev['event_uuid'],
                'agency_company_id' => (int) $ev['agency_company_id'],
                'service_company_id' => (int) ($ev['service_company_id'] ?? 0),
                'category' => $ev['category'],
                'event_type' => $ev['event_type'],
                'event_title' => $ev['event_title'],
                'event_description' => $ev['event_description'] ?? null,
                'actor_id' => (int) ($ev['actor_id'] ?? 0),
                'actor_name' => $ev['actor_name'] ?? 'System',
                'actor_role' => $ev['actor_role'] ?? 'system',
                'source' => $ev['source'] ?? 'INTERNAL_ATS',
                'entity_type' => $ev['entity_type'] ?? 'Agency',
                'entity_id' => (int) ($ev['entity_id'] ?? 0),
                'related_job_id' => ! empty($ev['related_job_id']) ? (int) $ev['related_job_id'] : null,
                'related_job_title' => $ev['related_job_title'] ?? null,
                'related_candidate_id' => ! empty($ev['related_candidate_id']) ? (int) $ev['related_candidate_id'] : null,
                'related_candidate_name' => $ev['related_candidate_name'] ?? null,
                'related_client_id' => ! empty($ev['related_client_id']) ? (int) $ev['related_client_id'] : null,
                'related_client_name' => $ev['related_client_name'] ?? null,
                'previous_value' => isset($ev['previous_value']) ? json_encode($ev['previous_value']) : null,
                'new_value' => isset($ev['new_value']) ? json_encode($ev['new_value']) : null,
                'metadata' => isset($ev['metadata']) ? json_encode($ev['metadata']) : null,
                'visibility_scope' => $ev['visibility_scope'] ?? 'PUBLIC',
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ];
        }, $batch);

        DB::table('agency_activity_logs')->insertOrIgnore($dbRows);

        // 2. Bulk index to Elasticsearch (read store)
        $bulkParams = ['body' => []];
        foreach ($batch as $doc) {
            $bulkParams['body'][] = [
                'index' => [
                    '_index' => $indexName,
                    '_id' => $doc['event_uuid'],
                ],
            ];
            $bulkParams['body'][] = $doc;
        }

        if (! empty($bulkParams['body'])) {
            try {
                $elastic->getClient()->bulk($bulkParams);
            } catch (\Throwable $e) {
                $this->warn("ES Bulk index warning: {$e->getMessage()}");
            }
        }
    }
}
