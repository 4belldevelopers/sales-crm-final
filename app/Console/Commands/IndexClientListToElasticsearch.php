<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\ElasticService;

/**
 * Command to initialize and index ELASTIC_CLIENT_LIST (local_client_list_elastic).
 *
 * INTENT: Provides a first-class schema command for ELASTIC_CLIENT_LIST with explicit
 * nested mappings for client_location, td_industries, and client_contact_info.
 * TENANCY: Scoped by company_id and self_company.
 */
class IndexClientListToElasticsearch extends Command
{
    protected $signature = 'elasticsearch:index-client-list
                            {--fresh : Delete and recreate the index with explicit mappings}';

    protected $description = 'Create and index ELASTIC_CLIENT_LIST with nested mappings for location and industries';

    public function handle()
    {
        $elastic = new ElasticService();
        $indexName = ELASTIC_CLIENT_LIST;

        if ($this->option('fresh') && $elastic->indexExists($indexName)) {
            $elastic->getClient()->indices()->delete(['index' => $indexName]);
            $this->info("Deleted existing index: {$indexName}");
        }

        if (!$elastic->indexExists($indexName)) {
            $this->createIndexWithMappings($elastic, $indexName);
            $this->info("Created index {$indexName} with explicit nested mappings.");
        } else {
            $this->info("Index {$indexName} already exists.");
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
                    'company_id'      => ['type' => 'long'],
                    'self_company'    => ['type' => 'integer'],
                    'name'            => ['type' => 'text', 'fields' => ['keyword' => ['type' => 'keyword']]],
                    'client_location' => [
                        'type' => 'nested',
                        'properties' => [
                            'city_id'      => ['type' => 'integer'],
                            'city_name'    => ['type' => 'keyword'],
                            'state_id'     => ['type' => 'integer'],
                            'state_name'   => ['type' => 'keyword'],
                            'country_id'   => ['type' => 'integer'],
                            'country_name' => ['type' => 'keyword'],
                        ],
                    ],
                    'td_industries' => [
                        'type' => 'nested',
                        'properties' => [
                            'id'       => ['type' => 'integer'],
                            'ind_name' => ['type' => 'keyword'],
                        ],
                    ],
                    'client_contact_info' => [
                        'type' => 'nested',
                        'properties' => [
                            'contact_person_name' => ['type' => 'text', 'fields' => ['keyword' => ['type' => 'keyword']]],
                            'email'               => ['type' => 'keyword'],
                        ],
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
