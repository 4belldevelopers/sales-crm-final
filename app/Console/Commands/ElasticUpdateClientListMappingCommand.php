<?php

namespace App\Console\Commands;

use App\Services\ElasticService;
use Illuminate\Console\Command;

/**
 * Ensures ELASTIC_CLIENT_LIST has explicit mappings for company_name.keyword,
 * name.keyword, and active_jobs_count without relying on unmapped_type fallbacks.
 *
 * Usage: php artisan elastic:update-client-mapping
 */
class ElasticUpdateClientListMappingCommand extends Command
{
    protected $signature = 'elastic:update-client-mapping';
    protected $description = 'Ensure explicit mappings for sort fields in ELASTIC_CLIENT_LIST';

    public function handle(ElasticService $elastic): int
    {
        $index = defined('ELASTIC_CLIENT_LIST') ? ELASTIC_CLIENT_LIST : 'local_client_list_elastic';

        if (! $elastic->indexExists($index)) {
            $this->warn("Index [{$index}] does not exist yet. It will receive updated mappings upon creation.");
            return self::SUCCESS;
        }

        $client = $elastic->getClient();

        $this->info("Applying explicit sort field mappings to [{$index}]...");

        try {
            $client->indices()->putMapping([
                'index' => $index,
                'body' => [
                    'properties' => [
                        'company_name' => [
                            'type' => 'text',
                            'fields' => [
                                'keyword' => [
                                    'type' => 'keyword',
                                    'ignore_above' => 256,
                                ],
                            ],
                        ],
                        'name' => [
                            'type' => 'text',
                            'fields' => [
                                'keyword' => [
                                    'type' => 'keyword',
                                    'ignore_above' => 256,
                                ],
                            ],
                        ],
                        'active_jobs_count' => [
                            'type' => 'integer',
                        ],
                    ],
                ],
            ]);

            $this->info("Successfully applied explicit mappings to [{$index}].");
            return self::SUCCESS;
        } catch (\Throwable $e) {
            $this->error("Failed to update mappings on [{$index}]: {$e->getMessage()}");
            return self::FAILURE;
        }
    }
}
