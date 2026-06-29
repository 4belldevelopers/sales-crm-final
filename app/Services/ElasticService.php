<?php

namespace App\Services;

// use Elastic\Elasticsearch\Client;
// use Elastic\Elasticsearch\ClientBuilder;
use Elasticsearch\ClientBuilder;


class ElasticService
{
    // protected Client $client;
    protected $client;

    public function __construct()
    {

        // Read from config(), never env() directly: env() returns null under
        // config:cache / Octane, which strips the ES credentials and causes a 401
        // ("missing authentication credentials"). See CLAUDE.md Rule #5 / BUG-02.
        $host   = config('elasticsearch.host', 'localhost');
        $port   = config('elasticsearch.port', 9200);
        $scheme = config('elasticsearch.scheme', 'http');
        $user   = config('elasticsearch.user');
        $pass   = config('elasticsearch.pass');

        $this->client = ClientBuilder::create()
        ->setHosts([
            [
                'host' => $host,
                'port' => $port,
                'scheme' => $scheme,
                'user' => $user,
                'pass' => $pass,
            ]
        ])
        ->build();

    }

    public function getClient()
    {
        return $this->client;
    }

    public function bulkIndex(array $params): array
    {
        return $this->client->bulk($params);
    }

    public function indexExists(string $index): bool
    {
        try {
            return $this->client->indices()->exists(['index' => $index]);
        } catch (\Exception $e) {
            return false;
        }
    }

    public function createIndex(string $index, array $mappings): array
    {
        return $this->client->indices()->create([
            'index' => $index,
            'body' => [
                'mappings' => $mappings
            ]
        ]);
    }
}
