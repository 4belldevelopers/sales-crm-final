<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Services\ElasticService;

$elastic = app(ElasticService::class);

$query = [
    'index' => ELASTIC_USERS_LIST,
    'body' => [
        'query' => [
            'bool' => [
                'must' => [
                    ['term' => ['company_id' => 2]],
                    [
                        'bool' => [
                            'should' => [
                                ['wildcard' => ['email_verification_code' => '?*']],
                                ['bool' => ['must_not' => [['wildcard' => ['password' => '?*']]]]],
                            ],
                            'minimum_should_match' => 1,
                        ]
                    ]
                ]
            ]
        ]
    ]
];

$res = $elastic->getClient()->search($query);
echo "Hits count: " . count($res['hits']['hits']) . "\n";
foreach ($res['hits']['hits'] as $hit) {
    echo "Hit ID: " . $hit['_source']['id'] . " | Name: " . $hit['_source']['name'] . " | Password: " . $hit['_source']['password'] . "\n";
}
