<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Services\ElasticJobIndex;

$jobIndexer = app(ElasticJobIndex::class);
$res = $jobIndexer->jobIndexElastic(1);
echo "Job 1 reindexed result: " . json_encode($res) . "\n";
