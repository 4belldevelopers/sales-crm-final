<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Services\ElasticService;

$elastic = app(ElasticService::class);
$mapping = $elastic->getClient()->indices()->getMapping([
    'index' => ELASTIC_USERS_LIST
]);

$props = $mapping[ELASTIC_USERS_LIST]['mappings']['properties'] ?? [];
echo "password mapping:\n";
print_r($props['password'] ?? 'NOT FOUND');
echo "email_verification_code mapping:\n";
print_r($props['email_verification_code'] ?? 'NOT FOUND');
echo "status mapping:\n";
print_r($props['status'] ?? 'NOT FOUND');
