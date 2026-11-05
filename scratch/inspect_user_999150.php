<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Services\ElasticService;

$u = User::find(999150);
if ($u) {
    echo "MySQL last_login: " . var_export($u->last_login, true) . "\n";
    echo "MySQL password: " . var_export($u->password, true) . "\n";
    echo "MySQL status: " . var_export($u->status, true) . "\n";
}
$elastic = app(ElasticService::class)->getClient();
try {
    $doc = $elastic->get(['index' => ELASTIC_USERS_LIST, 'id' => '999150']);
    echo "ES last_login: " . var_export($doc['_source']['last_login'] ?? null, true) . "\n";
    echo "ES password: " . var_export($doc['_source']['password'] ?? null, true) . "\n";
    echo "ES status: " . var_export($doc['_source']['status'] ?? null, true) . "\n";
} catch (\Throwable $e) {
    echo "ES error: " . $e->getMessage() . "\n";
}
