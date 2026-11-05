<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$elastic = app(App\Services\ElasticService::class);
$jobId = $argv[1] ?? 19;
$res = $elastic->getClient()->search([
    'index' => 'ats_jobs_elastic',
    'body' => [
        'query' => [
            'term' => ['id' => (int)$jobId]
        ]
    ]
]);
$src = $res['hits']['hits'][0]['_source'] ?? [];
echo "Job ID: " . ($src['id'] ?? '') . "\n";
echo "Title: " . ($src['title'] ?? '') . "\n";
echo "comId: " . ($src['comId'] ?? '') . "\n";
echo "client_id: " . ($src['client_id'] ?? '') . "\n";
echo "client_name: " . ($src['client_name'] ?? '') . "\n";
echo "company_name: " . ($src['company_name'] ?? '') . "\n";
echo "client_or_company_name: " . ($src['client_or_company_name'] ?? '') . "\n";
echo "logo: " . ($src['logo'] ?? '') . "\n";
echo "logo_logical_path: " . ($src['logo_logical_path'] ?? '') . "\n";
echo "self_company: " . ($src['self_company'] ?? '') . "\n";
print_r($src['client_location'] ?? []);

