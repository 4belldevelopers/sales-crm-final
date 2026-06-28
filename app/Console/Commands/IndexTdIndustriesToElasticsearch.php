<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\ElasticTdIndustries;
use App\Services\ElasticService;
use Carbon\Carbon;

class IndexTdIndustriesToElasticsearch extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'elasticsearch:index-td-industries  
    {--batch-size=500 : Number of records per batch}
    {--fresh : Delete and recreate the index}';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Index job td industries to Elasticsearch using bulk API';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function handle()
     {
         $elastic = new ElasticService();
         $indexName = ELASTIC_TD_INDUSTRIES;
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
             ElasticTdIndustries::query()->count()
         );
 
         ElasticTdIndustries::query()
             ->chunkById($batchSize, function ($rows) use ($elastic, $indexName, $progressBar) {
                 $params = ['body' => []];
                 
                 foreach ($rows as $row) {
                     $params['body'][] = [
                         'index' => [
                             '_index' => $indexName,
                             '_id' => $row->ind_id,
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
                     'ind_id' => ['type' => 'integer'],                 
                     'ind_name' => [
                        'type' => 'text',
                        'fields' => [
                            'keyword' => [
                                'type' => 'keyword',
                                'ignore_above' => 256
                            ]
                        ]
                     ],
                     'status' => ['type' => 'keyword', 'ignore_above' => 256],          
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
 
     protected function formatCandidateData(ElasticTdIndustries $row): array
     {
        // dd($company);
         return [
             'id' => $row->ind_id,
             'ind_id' => $row->ind_id,
             'ind_name' => $row->ind_name,
             'status' => $row->status,
             'created_at' => Carbon::parse($row->created_at)->toIso8601String(),
             'updated_at' => Carbon::parse($row->updated_at)->toIso8601String(),
             // Add other fields as needed
         ];
     }
}
