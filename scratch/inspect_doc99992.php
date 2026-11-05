<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Services\ElasticService;

$elastic = app(ElasticService::class);
$doc = $elastic->getClient()->get([
    'index' => ELASTIC_USERS_LIST,
    'id' => '99992'
]);

echo "Doc 99992 source:\n";
print_r($doc['_source']);
