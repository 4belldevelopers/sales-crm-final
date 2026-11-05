<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Services\ElasticService;

$es = app(ElasticService::class);
$res = $es->getClient()->search([
    'index' => 'ats_jobs_elastic',
    'body' => [
        'query' => [
            'term' => ['id' => 2]
        ]
    ]
]);
print_r($res['hits']['hits']);
