<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$elastic = app(\App\Services\ElasticService::class);
$res = $elastic->getClient()->search([
    'index' => 'ats_candidates_elastic,local_candidates_elastic',
    'body' => [
        'size' => 10,
        'query' => [
            'terms' => ['id' => [1, 2, 3, 25, 28, 34, 154, 185]]
        ]
    ]
]);
foreach ($res['hits']['hits'] as $h) {
    $s = $h['_source'];
    echo "ID: {$h['_id']} | job_id: " . ($s['job_id'] ?? $s['jobId'] ?? 'none') . " | full_name: " . ($s['full_name'] ?? 'none') . " | created_at: " . ($s['created_at'] ?? 'none') . "\n";
}
