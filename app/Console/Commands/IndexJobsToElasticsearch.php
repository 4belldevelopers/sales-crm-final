<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\ElasticJobs;
use App\Services\ElasticService;
use Carbon\Carbon;

class IndexJobsToElasticsearch extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'elasticsearch:index-jobs  
    {--batch-size=500 : Number of records per batch}
    {--fresh : Delete and recreate the index}';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Index jobs to Elasticsearch using bulk API';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function handle()
     {
         $elastic = new ElasticService();
         $indexName = ELASTIC_JOBS;
         $batchSize = (int)$this->option('batch-size');
 
         // Fresh index option
        //  $elastic->getClient()->indices()->refresh(['index' => $indexName]);
        //  $this->deleteIndex($elastic, $indexName);
        //  dd($this->option('fresh'));
         if ($this->option('fresh')) {
             $this->deleteIndex($elastic, $indexName);
             $this->createIndexWithMappings($elastic, $indexName);
             $this->info("Created fresh index: {$indexName}");
         }
 
         // Verify index exists
         if (!$elastic->indexExists($indexName)) {
             $this->createIndexWithMappings($elastic, $indexName);
         }
 
         $this->info("Starting bulk indexing (Batch size: {$batchSize})...");
         
         $progressBar = $this->output->createProgressBar(
             ElasticJobs::query()->count()
         );
 
         ElasticJobs::query()
             ->chunkById($batchSize, function ($rows) use ($elastic, $indexName, $progressBar) {
                 $params = ['body' => []];
                 
                 foreach ($rows as $row) {
                     $params['body'][] = [
                         'index' => [
                             '_index' => $indexName,
                             '_id' => $row->id,
                         ]
                     ];
                    //  dd($params);
                    //  $elastic->getClient()->indices()->refresh(['index' => $indexName]);
                     $params['body'][] = $this->formatCandidateData($row);
                 }
 
                 try {
                     $elastic->bulkIndex($params);
                     $progressBar->advance($rows->count());
                 } catch (\Exception $e) {
                     $this->line('');
                     $this->error("Batch failed: " . $e->getMessage());
                 }
             });
 
         $progressBar->finish();
         $this->line('');
         $this->info("Indexing completed successfully!");
     }
 
     protected function createIndexWithMappings(ElasticService $elastic, string $indexName): void
     {
         // Define the complete index configuration
         $indexConfig = [
             'settings' => [
                 'number_of_shards' => 1,
                 'number_of_replicas' => 0,
                 'analysis' => [
                     'analyzer' => [
                         'comma_analyzer' => [
                             'type' => 'custom',
                             'tokenizer' => 'pattern',
                             'pattern' => '\\s*,\\s*', // Handles spaces around commas
                             'filter' => ['lowercase', 'trim']
                         ],
                         'number_list_analyzer' => [
                             'type' => 'custom',
                             'tokenizer' => 'number_list_tokenizer',
                             'filter' => ['trim']
                         ]
                     ],
                     'tokenizer' => [
                         'number_list_tokenizer' => [
                             'type' => 'pattern',
                             'pattern' => '\\s*,\\s*',
                             'group' => -1
                         ]
                     ]
                 ]
             ],
             'mappings' => [
                 'properties' => [
                     'id' => ['type' => 'integer'],                   
                     'title' => [
                        'type' => 'text',
                        'fields' => [
                            'keyword' => [
                                'type' => 'keyword',
                                'ignore_above' => 256
                            ]
                        ]
                     ],
                     'slug' => [
                        'type' => 'text',
                        'fields' => [
                            'keyword' => [
                                'type' => 'keyword',
                                'ignore_above' => 256
                            ]
                        ]
                     ],
                     'company_id' => ['type' => 'integer'],
                     'job_description' => [
                        'type' => 'text',
                        'fields' => [
                            'keyword' => [
                                'type' => 'keyword',
                                'ignore_above' => 256
                            ]
                        ]
                     ],
                     'job_requirement' => [
                        'type' => 'text',
                        'fields' => [
                            'keyword' => [
                                'type' => 'keyword',
                                'ignore_above' => 256
                            ]
                        ]
                     ],
                     'total_positions' => ['type' => 'integer'],
                     'location_id' => ['type' => 'integer'],
                     'category_id' => ['type' => 'integer'],                                 
                     'start_date' => [
                        'type' => 'date',
                        'format' => 'strict_date_optional_time||epoch_millis'
                     ],
                     'end_date' => [
                        'type' => 'date',
                        'format' => 'strict_date_optional_time||epoch_millis'
                     ],
                     'status' => ['type' => 'keyword', 'ignore_above' => 256],
                     'required_columns' => [
                        'type' => 'text',
                        'fields' => [
                            'keyword' => [
                                'type' => 'keyword',
                                'ignore_above' => 256
                            ]
                        ]
                     ],
                     'meta_details' => [
                        'type' => 'text',
                        'fields' => [
                            'keyword' => [
                                'type' => 'keyword',
                                'ignore_above' => 256
                            ]
                        ]
                     ], 
                     'section_visibility' => [
                        'type' => 'text',
                        'fields' => [
                            'keyword' => [
                                'type' => 'keyword',
                                'ignore_above' => 256
                            ]
                        ]
                     ],                                   
                     'created_at' => [
                        'type' => 'date',
                        'format' => 'strict_date_optional_time||epoch_millis'
                     ],
                     'updated_at' => [
                        'type' => 'date',
                        'format' => 'strict_date_optional_time||epoch_millis'
                     ],
                     'priority' => ['type' => 'integer'],
                     'client_id' => ['type' => 'integer'],
                     'min_exp' => ['type' => 'integer'],
                     'max_exp' => ['type' => 'integer'],
                     'min_salary' => ['type' => 'integer'],
                     'max_salary' => ['type' => 'integer'],
                     'qualification' => ['type' => 'integer'],
                     'employment_type' => ['type' => 'integer'],
                     'min_salary_currency' => ['type' => 'integer'],
                     'max_salary_currency' => ['type' => 'integer'],
                     'min_sal_mode' => ['type' => 'integer'],
                     'max_sal_mode' => ['type' => 'integer'],
                     'carrier_website' => ['type' => 'integer'],  
                     'work_type' => [
                        'type' => 'text',
                        'fields' => [
                            'keyword' => [
                                'type' => 'keyword',
                                'ignore_above' => 256
                            ]
                        ]
                     ],
                     'show_vendor' => [
                        'type' => 'text',
                        'fields' => [
                            'keyword' => [
                                'type' => 'keyword',
                                'ignore_above' => 256
                            ]
                        ]
                     ],   
                     'closeType' => ['type' => 'integer'],   
                     'min_yearly' => ['type' => 'scaled_float', 'scaling_factor' => 100],
                     'min_monthly' => ['type' => 'scaled_float', 'scaling_factor' => 100],
                     'min_hourly' => ['type' => 'scaled_float', 'scaling_factor' => 100],
                     'max_yearly' => ['type' => 'scaled_float', 'scaling_factor' => 100],
                     'max_monthly' => ['type' => 'scaled_float', 'scaling_factor' => 100],
                     'max_hourly' => ['type' => 'scaled_float', 'scaling_factor' => 100],   
                     'auto_date_job' => [
                        'type' => 'date',
                        'format' => 'strict_date_optional_time||epoch_millis'
                     ],
                     'public_market_status' => ['type' => 'integer'],   
                     'work_shifts' => ['type' => 'keyword', 'ignore_above' => 256],
                     'category_name' => [
                        'type' => 'text',
                        'fields' => [
                            'keyword' => ['type' => 'keyword', 'ignore_above' => 256]
                        ]
                     ],
                     'client_name' => [
                        'type' => 'text',
                        'fields' => [
                            'keyword' => ['type' => 'keyword', 'ignore_above' => 256]
                        ]
                     ],
                     'vendor_added_count' => ['type' => 'integer'],
                     'job_metronics_skills' => [
                        'type' => 'nested',
                        'properties' => [
                            'id' => ['type' => 'integer'],
                            'job_id' => ['type' => 'integer'],
                            'company_id' => ['type' => 'integer'],
                            'skill_name' => [
                                'type' => 'text',
                                'fields' => [
                                    'keyword' => ['type' => 'keyword', 'ignore_above' => 256]
                                ]
                            ]
                        ]
                     ]
                 ]
             ]
         ];
 
     
         // Delete existing index if it exists
         if ($elastic->getClient()->indices()->exists(['index' => $indexName])) {
             $elastic->getClient()->indices()->delete(['index' => $indexName]);
         }
 
         // Create new index with proper configuration
         $response = $elastic->getClient()->indices()->create([
             'index' => $indexName,
             'body' => $indexConfig
         ]);
 
     }
 
     protected function deleteIndex(ElasticService $elastic, string $indexName): void
     {
         if ($elastic->indexExists($indexName)) {
             $elastic->getClient()->indices()->delete(['index' => $indexName]);
         }
     }
 
     protected function formatCandidateData(ElasticJobs $row): array
     {
        // dd($company);
         return [
             'id' => $row->id,
             'title' => $row->title,
             'slug' => $row->slug,
             'company_id' => $row->company_id,
             'job_description' => $row->job_description,
             'job_requirement' => $row->job_requirement,
             'total_positions' => $row->total_positions,
             'location_id' => $row->location_id,
             'category_id' => $row->category_id,
             'start_date' => Carbon::parse($row->start_date)->toIso8601String(),
             'end_date' => Carbon::parse($row->end_date)->toIso8601String(),
             'status' => $row->status,
             'required_columns' => $row->required_columns,
             'meta_details' => $row->meta_details,
             'section_visibility' => $row->section_visibility,
             'created_at' => Carbon::parse($row->created_at)->toIso8601String(),
             'updated_at' => Carbon::parse($row->updated_at)->toIso8601String(),
             'priority' => $row->priority,
             'client_id' => $row->client_id,
             'min_exp' => $row->min_exp,
             'max_exp' => $row->max_exp,
             'min_salary' => $row->min_salary,
             'max_salary' => $row->max_salary,
             'qualification' => $row->qualification,
             'employment_type' => $row->employment_type,
             'min_salary_currency' => $row->min_salary_currency,
             'max_salary_currency' => $row->max_salary_currency,
             'min_sal_mode' => $row->min_sal_mode,
             'max_sal_mode' => $row->max_sal_mode,
             'carrier_website' => $row->carrier_website,
             'work_type' => $row->work_type,
             'show_vendor' => $row->show_vendor,
             'closeType' => $row->closeType,
             'min_yearly' => $row->min_yearly,
             'min_monthly' => $row->min_monthly,
             'min_hourly' => $row->min_hourly,
             'max_yearly' => $row->max_yearly,
             'max_monthly' => $row->max_monthly,
             'max_hourly' => $row->max_hourly,
             'auto_date_job' => Carbon::parse($row->auto_date_job)->toIso8601String(),
             'public_market_status' => $row->public_market_status,
             'work_shifts' => $row->work_shifts,
             'category_name' => \Illuminate\Support\Facades\DB::table('job_categories')->where('id', $row->category_id)->value('name') ?? '',
             'client_name' => $row->client_name ?? '',
             'vendor_added_count' => (int) \Illuminate\Support\Facades\DB::table('market_job_added_count')->where('job_id', $row->id)->where('status', 0)->count(),
             'job_metronics_skills' => \Illuminate\Support\Facades\DB::table('job_metronics_skills')->where('job_id', $row->id)->get()->map(function ($s) {
                 return [
                     'id' => $s->id ?? null,
                     'job_id' => $s->job_id ?? null,
                     'company_id' => $s->company_id ?? null,
                     'skill_name' => $s->skill_name ?? '',
                 ];
             })->toArray(),
         ];
     }
}
