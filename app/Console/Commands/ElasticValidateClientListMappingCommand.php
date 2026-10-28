<?php

namespace App\Console\Commands;

use App\Services\ElasticService;
use Illuminate\Console\Command;

/**
 * Validates that ELASTIC_CLIENT_LIST has explicit mappings for required sort fields:
 * - company_name.keyword
 * - name.keyword
 * - active_jobs_count
 *
 * Usage: php artisan elastic:validate-client-mapping
 * Returns 0 on success, 1 on validation failure.
 */
class ElasticValidateClientListMappingCommand extends Command
{
    protected $signature = 'elastic:validate-client-mapping';
    protected $description = 'Validate that required sort fields are explicitly mapped in ELASTIC_CLIENT_LIST';

    public function handle(ElasticService $elastic): int
    {
        $index = defined('ELASTIC_CLIENT_LIST') ? ELASTIC_CLIENT_LIST : 'local_client_list_elastic';

        if (! $elastic->indexExists($index)) {
            $this->error("Validation Failed: Index [{$index}] does not exist.");
            return self::FAILURE;
        }

        try {
            $client = $elastic->getClient();
            $mappingResponse = $client->indices()->getMapping(['index' => $index]);
            $properties = $mappingResponse[$index]['mappings']['properties'] ?? [];

            $missing = [];

            // 1. Validate company_name.keyword
            if (! isset($properties['company_name']['fields']['keyword']) && ! isset($properties['company_name.keyword'])) {
                $missing[] = 'company_name.keyword';
            }

            // 2. Validate name.keyword
            if (! isset($properties['name']['fields']['keyword']) && ! isset($properties['name.keyword'])) {
                $missing[] = 'name.keyword';
            }

            // 3. Validate active_jobs_count
            if (! isset($properties['active_jobs_count'])) {
                $missing[] = 'active_jobs_count';
            }

            if (! empty($missing)) {
                $this->error("Validation Failed: Index [{$index}] is missing required sort mappings: " . implode(', ', $missing));
                $this->warn("Run 'php artisan elastic:update-client-mapping' to apply missing sort mappings.");
                return self::FAILURE;
            }

            $this->info("✓ Index [{$index}] healthy: company_name.keyword, name.keyword, and active_jobs_count are all explicitly mapped.");
            return self::SUCCESS;
        } catch (\Throwable $e) {
            $this->error("Failed to inspect mappings on [{$index}]: {$e->getMessage()}");
            return self::FAILURE;
        }
    }
}
