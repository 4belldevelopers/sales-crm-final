<?php

namespace App\Console\Commands;

use App\Services\ElasticService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * ReindexAtsJobsV2
 *
 * Intent: Command to update `ats_jobs` Elasticsearch mapping with denormalized read-model fields
 * (category_name, client_name, company details, skill_tags, vendor_added_count) and bulk-reindex
 * jobs to eliminate runtime N+1 database queries on the Market Jobs page.
 * Tenancy: Processes jobs across tenants while preserving company_id (comId) on every ES document.
 */
class ReindexAtsJobsV2 extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reindex:ats_jobs_v2
                            {--batch-size=200 : Number of job records per indexing batch}
                            {--update-mapping-only : Only update mapping properties without bulk re-indexing}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Updates ats_jobs Elasticsearch mapping with denormalized V2 read-model fields and bulk reindexes jobs.';

    /**
     * Execute the console command.
     */
    public function handle(ElasticService $elastic): int
    {
        $indexName = ELASTIC_JOBS;
        $batchSize = (int) $this->option('batch-size');

        $this->info("=== Starting Find Jobs V2 Denormalization Reindex for [{$indexName}] ===");

        if (! $elastic->indexExists($indexName)) {
            $this->error("Index [{$indexName}] does not exist. Please run 'php artisan elasticsearch:index-jobs --fresh' first.");

            return self::FAILURE;
        }

        $client = $elastic->getClient();

        // 1. Update Elasticsearch Mapping with V2 Denormalized Properties
        $this->info("Updating mapping for index [{$indexName}]...");
        try {
            $client->indices()->putMapping([
                'index' => $indexName,
                'body' => [
                    'properties' => [
                        'category_name' => [
                            'type' => 'text',
                            'fields' => [
                                'keyword' => ['type' => 'keyword', 'ignore_above' => 256],
                            ],
                        ],
                        'company_size_from' => ['type' => 'integer'],
                        'company_size' => ['type' => 'integer'],
                        'industry_name' => [
                            'type' => 'text',
                            'fields' => [
                                'keyword' => ['type' => 'keyword', 'ignore_above' => 256],
                            ],
                        ],
                        'vendor_added_count' => ['type' => 'integer'],
                        'job_metronics_skills' => [
                            'type' => 'nested',
                            'properties' => [
                                'id' => ['type' => 'long'],
                                'job_id' => ['type' => 'long'],
                                'company_id' => ['type' => 'long'],
                                'skill_name' => [
                                    'type' => 'text',
                                    'fields' => [
                                        'keyword' => ['type' => 'keyword', 'ignore_above' => 256],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ]);
            $this->info('Mapping update completed successfully.');
        } catch (\Exception $e) {
            $this->error('Mapping update warning/error: '.$e->getMessage());
        }

        if ($this->option('update-mapping-only')) {
            $this->info('Option --update-mapping-only specified. Exiting.');

            return self::SUCCESS;
        }

        // 2. Query Jobs and Bulk Index with Denormalized Metadata
        $totalJobs = DB::table('jobs')->count();
        $this->info("Found {$totalJobs} total jobs to process.");

        $progressBar = $this->output->createProgressBar($totalJobs);
        $progressBar->start();

        DB::table('jobs')
            ->orderBy('id', 'asc')
            ->chunk($batchSize, function ($jobs) use ($client, $indexName, $progressBar) {
                $jobIds = $jobs->pluck('id')->toArray();
                $companyIds = array_unique(array_filter($jobs->pluck('company_id')->toArray()));
                $categoryIds = array_unique(array_filter($jobs->pluck('category_id')->toArray()));

                // Bulk pre-fetch Category Names
                $categories = DB::table('job_categories')
                    ->whereIn('id', $categoryIds)
                    ->pluck('name', 'id')
                    ->toArray();

                // Bulk pre-fetch Skills
                $skillsByJob = DB::table('job_metronics_skills')
                    ->whereIn('job_id', $jobIds)
                    ->get()
                    ->groupBy('job_id');

                // Bulk pre-fetch Vendor Added Counts
                $vendorCounts = DB::table('market_job_added_count')
                    ->select('job_id', DB::raw('COUNT(*) as total'))
                    ->whereIn('job_id', $jobIds)
                    ->where('status', 0)
                    ->groupBy('job_id')
                    ->pluck('total', 'job_id')
                    ->toArray();

                $params = ['body' => []];

                foreach ($jobs as $job) {
                    $jobSkills = isset($skillsByJob[$job->id]) ? $skillsByJob[$job->id]->map(function ($s) {
                        return [
                            'id' => $s->id ?? null,
                            'job_id' => $s->job_id ?? null,
                            'company_id' => $s->company_id ?? null,
                            'skill_name' => $s->skill_name ?? '',
                        ];
                    })->toArray() : [];

                    $docData = [
                        'id' => (int) $job->id,
                        'title' => $job->title,
                        'slug' => $job->slug,
                        'comId' => (int) $job->company_id, // Preserving comId typo key for ES filters
                        'company_id' => (int) $job->company_id,
                        'client_name' => $job->client_name ?? '',
                        'category_id' => (int) $job->category_id,
                        'category_name' => $categories[$job->category_id] ?? '',
                        'job_description' => $job->job_description ?? '',
                        'job_requirement' => $job->job_requirement ?? '',
                        'total_positions' => (int) $job->total_positions,
                        'status' => $job->status,
                        'min_exp' => (int) $job->min_exp,
                        'max_exp' => (int) $job->max_exp,
                        'min_salary' => (int) $job->min_salary,
                        'max_salary' => (int) $job->max_salary,
                        'employment_type' => (int) $job->employment_type,
                        'min_salary_currency' => (int) $job->min_salary_currency,
                        'max_salary_currency' => (int) $job->max_salary_currency,
                        'min_sal_mode' => (int) $job->min_sal_mode,
                        'max_sal_mode' => (int) $job->max_sal_mode,
                        'work_type' => $job->work_type ?? '',
                        'min_yearly' => (float) $job->min_yearly,
                        'min_monthly' => (float) $job->min_monthly,
                        'min_hourly' => (float) $job->min_hourly,
                        'max_yearly' => (float) $job->max_yearly,
                        'max_monthly' => (float) $job->max_monthly,
                        'max_hourly' => (float) $job->max_hourly,
                        'public_market_status' => (int) $job->public_market_status,
                        'vendor_added_count' => (int) ($vendorCounts[$job->id] ?? 0),
                        'job_metronics_skills' => $jobSkills,
                        'created_at' => $job->created_at ? Carbon::parse($job->created_at)->toIso8601String() : null,
                        'updated_at' => $job->updated_at ? Carbon::parse($job->updated_at)->toIso8601String() : null,
                    ];

                    $params['body'][] = [
                        'update' => [
                            '_index' => $indexName,
                            '_id' => $job->id,
                        ],
                    ];
                    $params['body'][] = [
                        'doc' => $docData,
                        'doc_as_upsert' => true,
                    ];
                }

                if (! empty($params['body'])) {
                    try {
                        $client->bulk($params);
                    } catch (\Exception $e) {
                        $this->error('Bulk update chunk failed: '.$e->getMessage());
                    }
                }

                $progressBar->advance(count($jobs));
            });

        $progressBar->finish();
        $this->line('');
        $this->info('=== Find Jobs V2 Denormalization Reindex Completed Successfully! ===');

        return self::SUCCESS;
    }
}
