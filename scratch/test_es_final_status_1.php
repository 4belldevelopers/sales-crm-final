<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$elastic = app(\App\Services\ElasticService::class);
$client = $elastic->getClient();

$scopeMust = [
    [
        'nested' => [
            'path' => 'recommendation_relation',
            'query' => [
                'bool' => [
                    'must' => [
                        ['term' => ['recommendation_relation.from_company_id' => 2]],
                        ['term' => ['recommendation_relation.to_company_id' => 3]],
                        ['term' => ['recommendation_relation.final_status' => 1]],
                    ],
                ],
            ],
        ],
    ],
];

$res = $client->search([
    'index' => 'ats_jobs_elastic',
    'body' => [
        'query' => ['bool' => ['must' => $scopeMust]]
    ]
]);
echo "Jobs found when requiring final_status=1: " . count($res['hits']['hits']) . "\n";
