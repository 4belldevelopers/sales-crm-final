<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Services\ElasticService;

$elastic = app(ElasticService::class);

echo "=== CHECKING ALL USERS IN MYSQL VS ELASTICSEARCH ===\n";
$users = User::all(['id', 'company_id', 'name', 'email', 'status', 'password']);
$missingFromEs = [];

foreach ($users as $u) {
    try {
        $res = $elastic->getClient()->get([
            'index' => ELASTIC_USERS_LIST,
            'id' => (string) $u->id
        ]);
    } catch (\Throwable $e) {
        $missingFromEs[] = $u;
    }
}

echo "Total MySQL users: " . $users->count() . "\n";
echo "Missing from Elasticsearch: " . count($missingFromEs) . "\n";
foreach ($missingFromEs as $m) {
    echo " - Missing User ID: {$m->id} | Company: {$m->company_id} | Name: {$m->name} | Email: {$m->email}\n";
}
