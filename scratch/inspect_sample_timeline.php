<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$elastic = app(App\Services\ElasticService::class);
$res = $elastic->getClient()->search([
    'index' => 'ats_candidate_timeline',
    'size' => 10,
    '_source' => ['job_id', 'application_id', 'master_process_name', 'prev_process_name', 'event_id', 'name', 'cur_companyname', 'event_by_company_id']
]);
foreach ($res['hits']['hits'] as $h) {
    print_r($h['_source']);
}
