<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Services\ElasticService;

$es = app(ElasticService::class);
if ($es->indexExists('ats_candidate_timeline')) {
    $res = $es->getClient()->search([
        'index' => 'ats_candidate_timeline',
        'size' => 3
    ]);
    print_r($res['hits']['hits']);
} else {
    echo "ats_candidate_timeline not found\n";
}
