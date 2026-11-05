<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$elastic = app(\App\Services\ElasticService::class);
$client = $elastic->getClient();

echo "=== ats_candidate_timeline for app_id=3 ===\n";
try {
    $res = $client->search([
        'index' => 'ats_candidate_timeline',
        'body' => ['query' => ['term' => ['application_id' => 3]]]
    ]);
    foreach ($res['hits']['hits'] as $h) {
        $s = $h['_source'];
        echo "ID: {$h['_id']} | job_id: {$s['job_id']} | master_process: {$s['master_process_name']} | created_at: {$s['created_at']}\n";
    }
} catch (\Throwable $e) { echo $e->getMessage() . "\n"; }

echo "\n=== local_candidate_timeline for app_id=3 ===\n";
try {
    $res = $client->search([
        'index' => 'local_candidate_timeline',
        'body' => ['query' => ['term' => ['application_id' => 3]]]
    ]);
    foreach ($res['hits']['hits'] as $h) {
        $s = $h['_source'];
        echo "ID: {$h['_id']} | job_id: {$s['job_id']} | master_process: {$s['master_process_name']} | created_at: {$s['created_at']}\n";
    }
} catch (\Throwable $e) { echo $e->getMessage() . "\n"; }
