<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\ElasticRecommendationRelation;
use App\Services\ElasticService;
use Carbon\Carbon;

class IndexRecommendationRelationToElasticsearch extends Command
{
     /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'elasticsearch:index-recommendation-relation-elastic  
    {--batch-size=500 : Number of records per batch}
    {--fresh : Delete and recreate the index}';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Index recommendation relation to Elasticsearch using bulk API';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function handle()
     {
         $elastic = new ElasticService();
         $indexName = ELASTIC_RECOMMENDATION_RELATION;
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
             ElasticRecommendationRelation::query()->count()
         );

         ElasticRecommendationRelation::query()
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
                     'from_company_id' => ['type' => 'integer'],
                     'from_user_id' => ['type' => 'integer'], 
                     'from_status' => ['type' => 'integer'],                                                 
                     'from_created_at' => [
                        'type' => 'date',
                        'format' => 'strict_date_optional_time||epoch_millis'
                     ],
                     'to_company_id' => ['type' => 'integer'],
                     'to_user_id' => ['type' => 'integer'], 
                     'job_id' => ['type' => 'integer'],
                     'to_status' => ['type' => 'integer'],                              
                     'to_created_at' => [
                        'type' => 'date',
                        'format' => 'strict_date_optional_time||epoch_millis'
                     ],
                     'final_status' => ['type' => 'integer'],
                     'conversation_id' => ['type' => 'integer'],
                     'add_status' => ['type' => 'integer'],          
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
 
     protected function formatCandidateData(ElasticRecommendationRelation $row): array
     {
        // dd($company);
         return [
             'id' => $row->id,
             'from_company_id' => $row->from_company_id,
             'from_client_id' => $row->from_client_id,
             'from_user_id' => $row->from_user_id,
             'from_status' => $row->from_status,
             'from_created_at' => Carbon::parse($row->from_created_at)->toIso8601String(),
             'to_company_id' => $row->to_company_id,
             'to_user_id' => $row->to_user_id,
             'job_id' => $row->job_id,
             'to_status' => $row->to_status,
             'to_created_at' => Carbon::parse($row->created_at)->toIso8601String(),
             'final_status' => $row->final_status,
             'conversation_id' => $row->conversation_id,
             'add_status' => $row->add_status,
             'created_at' => Carbon::parse($row->created_at)->toIso8601String(),
             'updated_at' => Carbon::parse($row->updated_at)->toIso8601String(),
             // Add other fields as needed
         ];
     }
}
