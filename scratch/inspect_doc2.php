<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Services\ElasticService;

$elastic = app(ElasticService::class);
$doc2 = $elastic->getClient()->get([
    'index' => ELASTIC_USERS_LIST,
    'id' => '2'
]);

echo "Doc 2 source:\n";
print_r($doc2['_source']);
