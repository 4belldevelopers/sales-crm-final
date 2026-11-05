<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$elastic = app(\App\Services\ElasticService::class);
$client = $elastic->getClient();

echo "=== ats_jobs_elastic for id=1 ===\n";
try {
    $res = $client->get(['index' => 'ats_jobs_elastic', 'id' => 1]);
    echo "ats_jobs_elastic Job 1:\n";
    echo "  Title: " . $res['_source']['title'] . "\n";
    echo "  Created at: " . $res['_source']['created_at'] . "\n";
    echo "  Status: " . $res['_source']['status'] . "\n";
} catch (\Throwable $e) {
    echo "ats_jobs_elastic error: " . $e->getMessage() . "\n";
}

echo "\n=== local_jobs_elastic for id=1 ===\n";
try {
    $res = $client->get(['index' => 'local_jobs_elastic', 'id' => 1]);
    echo "local_jobs_elastic Job 1:\n";
    echo "  Title: " . $res['_source']['title'] . "\n";
    echo "  Created at: " . $res['_source']['created_at'] . "\n";
    echo "  Status: " . $res['_source']['status'] . "\n";
} catch (\Throwable $e) {
    echo "local_jobs_elastic error: " . $e->getMessage() . "\n";
}
