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

        $host = env('ELASTICSEARCH_HOST', 'localhost');
        $port = env('ELASTICSEARCH_PORT', 9200);
        $user = env('ELASTICSEARCH_USER');
        $pass = env('ELASTICSEARCH_PASS');

        // $this->client = ClientBuilder::create()
        //     ->setHosts(["http://{$host}:{$port}"])
        //     ->build();

        $this->client = ClientBuilder::create()
        ->setHosts([
            [
                'host' => $host,
                'port' => $port,
                'scheme' => 'http',
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
