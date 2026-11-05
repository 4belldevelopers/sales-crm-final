<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$elastic = app(\App\Services\ElasticService::class);
$res = $elastic->getClient()->search([
    'index' => 'ats_candidate_timeline',
    'body' => [
        'size' => 50,
        'query' => [
            'match_all' => new \stdClass()
        ]
    ]
]);

echo "Total docs in ats_candidate_timeline: " . count($res['hits']['hits']) . "\n";
foreach ($res['hits']['hits'] as $h) {
    $s = $h['_source'];
    if (str_contains($s['created_at'], '2026-09') || str_contains($s['created_at'], '2026-10-08')) {
        echo "ID: {$h['_id']} | job_id: {$s['job_id']} | app_id: {$s['application_id']} | name: {$s['name']} | cur_comp: {$s['cur_companyname']} | created_at: {$s['created_at']}\n";
    }
}
