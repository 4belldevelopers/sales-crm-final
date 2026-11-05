<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$elastic = app(App\Services\ElasticService::class);
foreach ([2, 19] as $jid) {
    $res = $elastic->getClient()->search([
        'index' => 'ats_jobs_elastic',
        'body' => ['query' => ['term' => ['id' => $jid]]]
    ]);
    $src = $res['hits']['hits'][0]['_source'] ?? [];
    echo "=== JOB {$jid} ===\n";
    echo "title: " . ($src['title'] ?? '') . "\n";
    echo "comId: " . ($src['comId'] ?? '') . "\n";
    echo "company_name: " . ($src['company_name'] ?? '') . "\n";
    echo "client_id: " . ($src['client_id'] ?? '') . "\n";
    echo "client_name: " . ($src['client_name'] ?? '') . "\n";
    echo "client_or_company_name: " . ($src['client_or_company_name'] ?? '') . "\n";
    echo "self_company: " . ($src['self_company'] ?? '') . "\n";
    echo "logo: " . ($src['logo'] ?? '') . "\n";
    echo "client_logo: " . ($src['client_logo'] ?? ($src['client_logo_url'] ?? '')) . "\n";
    echo "location_id: " . ($src['location_id'] ?? '') . "\n";
    echo "employment_type: " . ($src['employment_type'] ?? '') . "\n";
    echo "work_shifts: " . ($src['work_shifts'] ?? '') . "\n";
    echo "client_city_jobs: " . json_encode($src['client_city_jobs'] ?? []) . "\n";
    echo "client_location: " . json_encode($src['client_location'] ?? []) . "\n";
    echo "\n";
}
