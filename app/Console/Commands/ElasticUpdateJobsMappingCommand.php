<?php

namespace App\Console\Commands;

use App\Services\ElasticService;
use Illuminate\Console\Command;

/**
 * Applies the edge_ngram analyzer to ats_jobs_elastic for fast typeahead search.
 *
 * NOTE: Changing index analysis settings requires closing the index first.
 * Run during a maintenance window. The index is briefly unavailable (~2s).
 *
 * Usage: php artisan elastic:update-jobs-mapping
 */
class ElasticUpdateJobsMappingCommand extends Command
{
    protected $signature = 'elastic:update-jobs-mapping {--force : Skip the confirmation prompt}';
    protected $description = 'Apply edge_ngram analyzer to ats_jobs_elastic for typeahead search';

    public function handle(ElasticService $elastic): int
    {
        $index = ELASTIC_JOBS;

        if (!$elastic->indexExists($index)) {
            $this->error("Index [{$index}] does not exist.");
            return self::FAILURE;
        }

        if (!$this->option('force') && !$this->confirm("This will briefly close [{$index}]. Continue?", false)) {
            return self::SUCCESS;
        }

        $client = $elastic->getClient();

        $this->info("Closing index [{$index}]...");
        $client->indices()->close(['index' => $index]);

        $this->info('Applying edge_ngram analyzer settings...');
        $client->indices()->putSettings([
            'index' => $index,
            'body'  => [
                'analysis' => [
                    'filter' => [
                        'edge_ngram_filter' => [
                            'type'     => 'edge_ngram',
                            'min_gram' => 2,
                            'max_gram' => 15,
                        ],
                    ],
                    'analyzer' => [
                        'title_edge_ngram' => [
                            'type'      => 'custom',
                            'tokenizer' => 'standard',
                            'filter'    => ['lowercase', 'edge_ngram_filter'],
                        ],
                    ],
                ],
            ],
        ]);

        $this->info('Applying title.search field mapping...');
        $client->indices()->putMapping([
            'index' => $index,
            'body'  => [
                'properties' => [
                    'title' => [
                        'type'   => 'text',
                        'fields' => [
                            'search' => [
                                'type'            => 'text',
                                'analyzer'        => 'title_edge_ngram',
                                'search_analyzer' => 'standard',
                            ],
                            'keyword' => [
                                'type'         => 'keyword',
                                'ignore_above' => 256,
                            ],
                        ],
                    ],
                ],
            ],
        ]);

        $this->info("Reopening index [{$index}]...");
        $client->indices()->open(['index' => $index]);

        $this->info("Done. Re-index all documents with: php artisan elastic:reindex-jobs");
        return self::SUCCESS;
    }
}
