<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$elastic = app(\App\Services\ElasticService::class);
$res = $elastic->getClient()->search([
    'index' => 'ats_jobs_elastic',
    'body' => [
        'query' => [
            'match_phrase' => ['title' => 'lead java developer']
        ]
    ]
]);
echo "Hits: " . count($res['hits']['hits']) . "\n";
foreach ($res['hits']['hits'] as $h) {
    echo "ID: {$h['_id']} | " . json_encode([
        'id' => $h['_source']['id'] ?? null,
        'title' => $h['_source']['title'] ?? null,
        'company_id' => $h['_source']['company_id'] ?? $h['_source']['comId'] ?? null,
        'client_id' => $h['_source']['client_id'] ?? null,
        'status' => $h['_source']['status'] ?? null,
        'created_at' => $h['_source']['created_at'] ?? null,
        'recommendation_relation' => $h['_source']['recommendation_relation'] ?? null,
    ]) . "\n";
}
