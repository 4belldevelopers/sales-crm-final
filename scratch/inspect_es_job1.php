<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$elastic = app(\App\Services\ElasticService::class);
$client = $elastic->getClient();

echo "=== 1. SEARCH FOR seq 1789561409 in ats_activity_timeline ===\n";
try {
    $res = $client->search([
        'index' => 'ats_activity_timeline',
        'body' => [
            'query' => [
                'bool' => [
                    'should' => [
                        ['term' => ['client_activity_sequence' => 1789561409]],
                        ['term' => ['_id' => 'seq-1789561409']],
                    ]
                ]
            ]
        ]
    ]);
    print_r($res['hits']['hits']);
} catch (\Throwable $e) {
    echo "ats_activity_timeline error: " . $e->getMessage() . "\n";
}

echo "\n=== 2. SEARCH FOR JOB 1 in ats_activity_timeline ===\n";
try {
    $res = $client->search([
        'index' => 'ats_activity_timeline',
        'body' => [
            'query' => [
                'term' => ['job_id' => 1]
            ]
        ]
    ]);
    echo "Total hits for job_id=1: " . ($res['hits']['total']['value'] ?? count($res['hits']['hits'])) . "\n";
    foreach ($res['hits']['hits'] as $h) {
        echo "ID: {$h['_id']} | Source: " . json_encode($h['_source']) . "\n";
    }
} catch (\Throwable $e) {
    echo "error: " . $e->getMessage() . "\n";
}

echo "\n=== 3. SEARCH FOR JOB 1 in ats_jobs_elastic ===\n";
try {
    $res = $client->search([
        'index' => 'ats_jobs_elastic',
        'body' => [
            'query' => [
                'term' => ['id' => 1]
            ]
        ]
    ]);
    echo "Total hits for job 1 in ats_jobs_elastic: " . count($res['hits']['hits']) . "\n";
    foreach ($res['hits']['hits'] as $h) {
        echo "Source: " . json_encode($h['_source']) . "\n";
    }
} catch (\Throwable $e) {
    echo "error: " . $e->getMessage() . "\n";
}
