<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$elastic = app(\App\Services\ElasticService::class);
$res = $elastic->getClient()->get(['index' => 'ats_jobs_elastic', 'id' => 1]);
print_r($res['_source']['recommendation_relation']);
