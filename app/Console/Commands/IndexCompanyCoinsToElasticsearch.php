<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\ElasticCampanyCoins;
use App\Services\ElasticService;
use Carbon\Carbon;

class IndexCompanyCoinsToElasticsearch extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'elasticsearch:index-companies-coins  
    {--batch-size=500 : Number of records per batch}
    {--fresh : Delete and recreate the index}';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Index Companies coins  to Elasticsearch using bulk API';

    /**
     * Create a new command instance.
     *
     * @return void
     */

     public function handle()
     {
         $elastic = new ElasticService();
         $indexName = ELASTIC_COMPANIES_COINS;
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
             ElasticCampanyCoins::query()->count()
         );
 
         ElasticCampanyCoins::query()
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
                     'coin_order_id' => ['type' => 'integer'],
                     'company_id' => ['type' => 'integer'],
                     'event_type' => ['type' => 'keyword', 'ignore_above' => 256],                     
                     'currency' => [
                        'type' => 'text',
                        'fields' => [
                            'keyword' => [
                                'type' => 'keyword',
                                'ignore_above' => 256
                            ]
                        ]
                     ],
                     'amount' => ['type' => 'scaled_float', 'scaling_factor' => 100],
                     'discount' => ['type' => 'scaled_float', 'scaling_factor' => 100],
                     'coins' => ['type' => 'scaled_float', 'scaling_factor' => 100],
                     'actual_coins' => ['type' => 'scaled_float', 'scaling_factor' => 100],
                     'balance_coin' => ['type' => 'scaled_float', 'scaling_factor' => 100],
                     'status' => ['type' => 'keyword', 'ignore_above' => 256],     
                     'free_pack_status' => ['type' => 'keyword', 'ignore_above' => 256],                      
                    'start_date' => [
                        'type' => 'date',
                        'format' => 'strict_date_optional_time||epoch_millis'
                    ], 
                    'end_date' => [
                        'type' => 'date',
                        'format' => 'strict_date_optional_time||epoch_millis'
                    ], 
                    'free_update_date' => [
                        'type' => 'date',
                        'format' => 'strict_date_optional_time||epoch_millis'
                    ], 
                    'auto_team_coins' => [
                        'type' => 'date',
                        'format' => 'strict_date_optional_time||epoch_millis'
                    ], 
                    'auto_job_coins' => [
                        'type' => 'date',
                        'format' => 'strict_date_optional_time||epoch_millis'
                    ],              
                     'created_at' => [
                        'type' => 'date',
                        'format' => 'strict_date_optional_time||epoch_millis'
                     ],
                     'updated_at' => [
                        'type' => 'date',
                        'format' => 'strict_date_optional_time||epoch_millis'
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
 
     protected function formatCandidateData(ElasticCampanyCoins $company): array
     {
        // dd($company);
         return [
             'id' => $company->id,
             'coin_order_id' => $company->coin_order_id,
             'company_id' => $company->company_id,
             'event_type' => $company->event_type,
             'currency' => $company->currency,
             'amount' => $company->amount,
             'discount' => $company->discount,
             'coins' => $company->coins,
             'actual_coins' => $company->actual_coins,
             'balance_coin' => $company->balance_coin,
             'status' => $company->status,
             'free_pack_status' => $company->free_pack_status,
             'start_date' =>  Carbon::parse($company->start_date)->toIso8601String(),
             'end_date' =>  Carbon::parse($company->start_date)->toIso8601String(),
             'free_update_date' =>  Carbon::parse($company->start_date)->toIso8601String(),
             'auto_team_coins' =>  Carbon::parse($company->start_date)->toIso8601String(),
             'auto_job_coins' =>  Carbon::parse($company->start_date)->toIso8601String(),
             'created_at' => Carbon::parse($company->created_at)->toIso8601String(),
             'updated_at' => Carbon::parse($company->updated_at)->toIso8601String(),
             // Add other fields as needed
         ];
     }
}
