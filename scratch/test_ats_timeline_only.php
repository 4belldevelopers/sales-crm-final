<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$elastic = app(\App\Services\ElasticService::class);
$client = $elastic->getClient();

$res = $client->search([
    'index' => 'ats_candidate_timeline',
    'body' => [
        'query' => [
            'bool' => [
                'must' => [
                    ['term' => ['job_id' => 2]],
                    ['terms' => ['application_id' => [3, 25, 34, 154, 185]]]
                ]
            ]
        ],
        'sort' => [['created_at' => ['order' => 'desc']]]
    ]
]);

echo "Hits in ats_candidate_timeline ONLY: " . count($res['hits']['hits']) . "\n";
foreach ($res['hits']['hits'] as $h) {
    $s = $h['_source'];
    echo "ID: {$h['_id']} | app: {$s['application_id']} | process: {$s['master_process_name']} | by: {$s['name']} ({$s['cur_companyname']}) | created_at: {$s['created_at']}\n";
}
