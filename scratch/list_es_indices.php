<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$elastic = app(\App\Services\ElasticService::class);
$indices = $elastic->getClient()->cat()->indices(['format' => 'json']);
foreach ($indices as $idx) {
    echo $idx['index'] . " (docs: " . $idx['docs.count'] . ")\n";
}
