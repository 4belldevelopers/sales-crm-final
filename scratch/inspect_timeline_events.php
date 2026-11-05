<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$elastic = app(App\Services\ElasticService::class);
$res = $elastic->getClient()->search([
    'index' => 'ats_candidate_timeline',
    'size' => 5,
    'body' => [
        'query' => [
            'bool' => [
                'must' => [
                    ['term' => ['job_id' => 2]]
                ]
            ]
        ]
    ]
]);

foreach ($res['hits']['hits'] as $h) {
    $s = $h['_source'];
    echo "ID: " . $h['_id'] . " | event_id: " . ($s['event_id'] ?? '') . " | candidate_name: " . ($s['candidate_name'] ?? '') . " | master_process_name: " . ($s['master_process_name'] ?? '') . " | prev_process_name: " . ($s['prev_process_name'] ?? '') . "\n";
    echo "  name: " . ($s['name'] ?? '') . " | cur_companyname: " . ($s['cur_companyname'] ?? '') . " | event_by_company_id: " . ($s['event_by_company_id'] ?? '') . " | event_by_user_id: " . ($s['event_by_user_id'] ?? '') . "\n";
    echo "  thirdparty_company_id: " . ($s['thirdparty_company_id'] ?? '') . " | ClientComID: " . ($s['ClientComID'] ?? '') . "\n";
    echo "\n";
}
