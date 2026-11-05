<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$elastic = app(\App\Services\ElasticService::class);
$res = $elastic->getClient()->search([
    'index' => 'local_candidate_timeline',
    'body' => [
        'size' => 50,
        'query' => [
            'query_string' => [
                'query' => '*2026-09-21*'
            ]
        ]
    ]
]);

echo "Hits in local_candidate_timeline for 2026-09-21: " . count($res['hits']['hits']) . "\n";
foreach ($res['hits']['hits'] as $h) {
    $s = $h['_source'];
    echo "ID: {$h['_id']} | job_id: {$s['job_id']} | app_id: {$s['application_id']} | name: {$s['name']} | title: {$s['title']} | cur_comp: {$s['cur_companyname']} | created_at: {$s['created_at']}\n";
}
