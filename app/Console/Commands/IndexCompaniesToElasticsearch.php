<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\ElasticCampany;
use App\Services\ElasticService;
use Carbon\Carbon;


class IndexCompaniesToElasticsearch extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'elasticsearch:index-companies 
    {--batch-size=500 : Number of records per batch}
    {--fresh : Delete and recreate the index}';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Index Companies to Elasticsearch using bulk API';
    /**
     * Create a new command instance.
     *
     * @return void
     */

     public function handle()
     {
         $elastic = new ElasticService();
         $indexName = ELASTIC_COMPANIES;
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
             ElasticCampany::query()->count()
         );
 
         ElasticCampany::query()
             ->chunkById($batchSize, function ($companys) use ($elastic, $indexName, $progressBar) {
                 $params = ['body' => []];
                 
                 foreach ($companys as $company) {
                     $params['body'][] = [
                         'index' => [
                             '_index' => $indexName,
                             '_id' => $company->id,
                         ]
                     ];
                    //  dd($params);
                    //  $elastic->getClient()->indices()->refresh(['index' => $indexName]);
                     $params['body'][] = $this->formatCandidateData($company);
                 }
 
                 try {
                     $elastic->bulkIndex($params);
                     $progressBar->advance($companys->count());
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
                 'number_of_replicas' => 1,
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
                 '_source' => [
                     'excludes' => [
                         'zoom_api_secret',
                         'zoom_access_token',
                         'google_access_token',
                         'google_refresh_token',
                         'microsoft_api_secret',
                         'stripe_id',
                         'card_brand',
                         'card_last_four',
                     ]
                 ],
                 'properties' => [
                     'id' => ['type' => 'integer'],
                     'sub_domain' => [
                         'type' => 'text',
                         'fields' => [
                             'keyword' => [
                                 'type' => 'keyword',
                                 'ignore_above' => 256
                             ]
                         ]
                     ],
                     'package_id' => ['type' => 'integer'],
                     'company_name' => [
                        'type' => 'text',
                        'fields' => [
                            'keyword' => [
                                'type' => 'keyword',
                                'ignore_above' => 256
                            ]
                        ]
                    ],
                    'company_email' => ['type' => 'keyword', 'ignore_above' => 256],
                    'calling_code' => [
                        'type' => 'text',
                        'fields' => [
                            'keyword' => [
                                'type' => 'keyword',
                                'ignore_above' => 256
                            ]
                        ]
                    ],
                    'company_phone' => ['type' => 'keyword', 'ignore_above' => 32],
                    'logo' => [
                        'type' => 'text',
                        'fields' => [
                            'keyword' => [
                                'type' => 'keyword',
                                'ignore_above' => 256
                            ]
                        ]
                    ],
                    'address' => [
                        'type' => 'text',
                        'fields' => [
                            'keyword' => [
                                'type' => 'keyword',
                                'ignore_above' => 256
                            ]
                        ]
                    ],
                    'website' => [
                        'type' => 'text',
                        'fields' => [
                            'keyword' => [
                                'type' => 'keyword',
                                'ignore_above' => 256
                            ]
                        ]
                    ],
                    'locale' => [
                        'type' => 'text',
                        'fields' => [
                            'keyword' => [
                                'type' => 'keyword',
                                'ignore_above' => 256
                            ]
                        ]
                    ],
                    'timezone' => [
                        'type' => 'text',
                        'fields' => [
                            'keyword' => [
                                'type' => 'keyword',
                                'ignore_above' => 256
                            ]
                        ]
                    ],
                    'status' => ['type' => 'keyword', 'ignore_above' => 256],
                    'show_in_frontend' => ['type' => 'keyword', 'ignore_above' => 256],
                    'career_page_link' => [
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
                    'package_type' => [
                        'type' => 'text',
                        'fields' => [
                            'keyword' => [
                                'type' => 'keyword',
                                'ignore_above' => 256
                            ]
                        ]
                    ], 
                    'licence_expire_on' => [
                        'type' => 'date',
                        'format' => 'strict_date_optional_time||epoch_millis'
                    ],
                    'job_opening_title' => [
                        'type' => 'text',
                        'fields' => [
                            'keyword' => [
                                'type' => 'keyword',
                                'ignore_above' => 256
                            ]
                        ]
                    ], 
                    'job_opening_text' => [
                        'type' => 'text',
                        'fields' => [
                            'keyword' => [
                                'type' => 'keyword',
                                'ignore_above' => 256
                            ]
                        ]
                    ], 
                    'linkedin' => ['type' => 'keyword', 'ignore_above' => 256],
                    'stripe_id' => [
                        'type' => 'text',
                        'fields' => [
                            'keyword' => [
                                'type' => 'keyword',
                                'ignore_above' => 256
                            ]
                        ]
                    ], 
                    'card_brand' => [
                        'type' => 'text',
                        'fields' => [
                            'keyword' => [
                                'type' => 'keyword',
                                'ignore_above' => 256
                            ]
                        ]
                    ], 
                    'card_last_four' => [
                        'type' => 'text',
                        'fields' => [
                            'keyword' => [
                                'type' => 'keyword',
                                'ignore_above' => 256
                            ]
                        ]
                    ],                    
                    'trial_ends_at' => [
                        'type' => 'date',
                        'format' => 'strict_date_optional_time||epoch_millis'
                    ],
                    'featured' => ['type' => 'integer'],
                    'featured_start_date' => [
                        'type' => 'date',
                        'format' => 'strict_date_optional_time||epoch_millis'
                    ],
                    'featured_end_date' => [
                        'type' => 'date',
                        'format' => 'strict_date_optional_time||epoch_millis'
                    ],
                    'login_background' => [
                        'type' => 'text',
                        'fields' => [
                            'keyword' => [
                                'type' => 'keyword',
                                'ignore_above' => 256
                            ]
                        ]
                    ], 
                    'delete_account_at' => [
                        'type' => 'date',
                        'format' => 'strict_date_optional_time||epoch_millis'
                    ],
                    'app_duplicate' => ['type' => 'integer'],
                    'app_duplicate_days' => ['type' => 'integer'],
                    'zoom_status' => ['type' => 'integer'],
                    'zoom_api_key' => [
                        'type' => 'text',
                        'fields' => [
                            'keyword' => [
                                'type' => 'keyword',
                                'ignore_above' => 256
                            ]
                        ]
                    ], 
                    'zoom_api_secret' => [
                        'type' => 'text',
                        'fields' => [
                            'keyword' => [
                                'type' => 'keyword',
                                'ignore_above' => 256
                            ]
                        ]
                    ], 
                    'zoom_email' => [
                        'type' => 'text',
                        'fields' => [
                            'keyword' => [
                                'type' => 'keyword',
                                'ignore_above' => 256
                            ]
                        ]
                    ], 
                    'zoom_access_token' => [
                        'type' => 'text',
                        'fields' => [
                            'keyword' => [
                                'type' => 'keyword',
                                'ignore_above' => 256
                            ]
                        ]
                    ], 
                    'google_status' => ['type' => 'integer'],
                    'google_client_id' => [
                        'type' => 'text',
                        'fields' => [
                            'keyword' => [
                                'type' => 'keyword',
                                'ignore_above' => 256
                            ]
                        ]
                    ], 
                    'google_client_secret' => [
                        'type' => 'text',
                        'fields' => [
                            'keyword' => [
                                'type' => 'keyword',
                                'ignore_above' => 256
                            ]
                        ]
                    ], 
                    'google_access_token' => [
                        'type' => 'text',
                        'fields' => [
                            'keyword' => [
                                'type' => 'keyword',
                                'ignore_above' => 256
                            ]
                        ]
                    ], 
                    'google_expires_in' => ['type' => 'integer'],
                    'google_refresh_token' => [
                        'type' => 'text',
                        'fields' => [
                            'keyword' => [
                                'type' => 'keyword',
                                'ignore_above' => 256
                            ]
                        ]
                    ],
                    'google_scope' => [
                        'type' => 'text',
                        'fields' => [
                            'keyword' => [
                                'type' => 'keyword',
                                'ignore_above' => 256
                            ]
                        ]
                    ],
                    'google_token_type' => [
                        'type' => 'text',
                        'fields' => [
                            'keyword' => [
                                'type' => 'keyword',
                                'ignore_above' => 256
                            ]
                        ]
                    ],
                    'google_verify' => ['type' => 'integer'],
                    'microsoft_status' => ['type' => 'integer'],
                    'microsoft_api_key' => [
                        'type' => 'text',
                        'fields' => [
                            'keyword' => [
                                'type' => 'keyword',
                                'ignore_above' => 256
                            ]
                        ]
                    ],
                    'microsoft_api_secret' => [
                        'type' => 'text',
                        'fields' => [
                            'keyword' => [
                                'type' => 'keyword',
                                'ignore_above' => 256
                            ]
                        ]
                    ],
                    'category_id' => ['type' => 'integer'],
                    'company_size' => ['type' => 'integer'],
                    'company_type' => ['type' => 'integer'],
                    'founded' => ['type' => 'integer'],
                    'description' => [
                        'type' => 'text',
                        'fields' => [
                            'keyword' => [
                                'type' => 'keyword',
                                'ignore_above' => 256
                            ]
                        ]
                    ],
                    'industries' => ['type' => 'integer'],
                    'acount_type' => ['type' => 'integer'],
                    'language' => ['type' => 'integer'],
                    'currency' => ['type' => 'integer'],
                    'time_format' => ['type' => 'integer'],
                    'date_format' => ['type' => 'integer'],
                    'industries_type' => ['type' => 'integer'],
                    'success_per' => ['type' => 'integer'],
                    'terms' => [
                        'type' => 'text',
                        'fields' => [
                            'keyword' => [
                                'type' => 'keyword',
                                'ignore_above' => 256
                            ]
                        ]
                    ],
                    'offer_terms	' => [
                        'type' => 'text',
                        'fields' => [
                            'keyword' => [
                                'type' => 'keyword',
                                'ignore_above' => 256
                            ]
                        ]
                    ],
                    'last_login' => [
                        'type' => 'date',
                        'format' => 'strict_date_optional_time||epoch_millis'
                    ],
                    'leadCount' => ['type' => 'integer']
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
 
     protected function formatCandidateData(ElasticCampany $company): array
     {
        // dd($company);
         return [
             'id' => $company->id,
             'sub_domain' => $company->sub_domain,
             'package_id' => $company->package_id,
             'company_name' => $company->company_name,
             'company_email' => $company->company_email,
             'calling_code' => $company->calling_code,
             'status' => $company->status,
             'show_in_frontend' => $company->show_in_frontend,
             'career_page_link' => $company->career_page_link,
             'created_at' => Carbon::parse($company->created_at)->toIso8601String(),
             'updated_at' => Carbon::parse($company->updated_at)->toIso8601String(),
             'package_type' => $company->package_type,
             'licence_expire_on' =>  Carbon::parse($company->licence_expire_on)->toIso8601String(),
             'job_opening_title' => $company->job_opening_title,
             'job_opening_text' => $company->job_opening_text,
             'linkedin' => $company->linkedin,
             'stripe_id' => $company->stripe_id,
             'card_brand' => $company->card_brand,
             'card_last_four' => $company->card_last_four,
             'trial_ends_at' => Carbon::parse($company->trial_ends_at)->toIso8601String(),
             'featured' => $company->featured,
             'featured_start_date' =>  Carbon::parse($company->featured_start_date)->toIso8601String(),
             'featured_end_date' =>  Carbon::parse($company->featured_end_date)->toIso8601String(),
             'login_background' => $company->login_background,
             'delete_account_at' => Carbon::parse($company->delete_account_at)->toIso8601String(),
             'app_duplicate' => $company->app_duplicate,
             'app_duplicate_days' => $company->app_duplicate_days,
             'zoom_status' => $company->zoom_status,
             'zoom_api_key' => $company->zoom_api_key,
             'zoom_api_secret' => $company->zoom_api_secret,
             'zoom_email' => $company->zoom_email,
             'zoom_access_token' => $company->zoom_access_token,
             'google_status' => $company->google_status,
             'google_client_id' => $company->google_client_id,
             'google_client_secret' => $company->google_client_secret,
             'google_access_token' => $company->google_access_token,
             'google_expires_in' => $company->google_expires_in,
             'google_refresh_token' => $company->google_refresh_token,
             'google_scope' => $company->google_scope,
             'google_token_type' => $company->google_token_type,
             'google_verify' => $company->google_verify,
             'microsoft_status' => $company->microsoft_status,
             'microsoft_api_key' => $company->microsoft_api_key,
             'microsoft_api_secret' => $company->microsoft_api_secret,
             'category_id' => $company->category_id,
             'company_size' => $company->company_size,
             'company_type' => $company->company_type,
             'founded' => $company->founded,
             'description' => $company->description,
             'industries' => $company->industries,
             'acount_type' => $company->acount_type,
             'language' => $company->language,
             'currency' => $company->currency,
             'time_format' => $company->time_format,
             'date_format' => $company->date_format,
             'industries_type' => $company->industries_type,
             'success_per' => $company->success_per,
             'terms' => $company->terms,
             'offer_terms' => $company->offer_terms,
             'last_login' => Carbon::parse($company->last_login)->toIso8601String(),
             // Add other fields as needed
         ];
     }
}
