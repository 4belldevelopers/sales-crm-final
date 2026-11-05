<?php
require_once __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Services\ElasticService;

$es = app(ElasticService::class)->getClient();
$job2 = $es->search([
    'index' => 'ats_jobs_elastic',
    'body' => [
        'query' => ['term' => ['id' => 2]]
    ]
]);
echo "Job 2 in ats_jobs_elastic:\n";
if (!empty($job2['hits']['hits'])) {
    $src = $job2['hits']['hits'][0]['_source'];
    foreach (['id', 'title', 'company_id', 'client_id', 'company_name', 'client_name', 'clients_name', 'logo', 'logo_logical_path'] as $k) {
        echo "  $k: " . (is_scalar($src[$k] ?? null) ? $src[$k] : json_encode($src[$k] ?? null)) . "\n";
    }
} else {
    echo "  Not found\n";
}

$job3 = $es->search([
    'index' => 'ats_jobs_elastic',
    'body' => [
        'query' => ['term' => ['id' => 3]]
    ]
]);
echo "Job 3 in ats_jobs_elastic:\n";
if (!empty($job3['hits']['hits'])) {
    $src = $job3['hits']['hits'][0]['_source'];
    foreach (['id', 'title', 'company_id', 'client_id', 'company_name', 'client_name', 'clients_name', 'logo', 'logo_logical_path'] as $k) {
        echo "  $k: " . (is_scalar($src[$k] ?? null) ? $src[$k] : json_encode($src[$k] ?? null)) . "\n";
    }
} else {
    echo "  Not found\n";
}
