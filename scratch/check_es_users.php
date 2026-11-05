<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Services\ElasticService;

$es = app(ElasticService::class)->getClient();

try {
    $res = $es->search([
        'index' => ELASTIC_USERS_LIST,
        'body' => [
            'query' => [
                'term' => ['company_id' => 2]
            ]
        ]
    ]);
    echo "Total in ES for company 2: " . ($res['hits']['total']['value'] ?? 0) . "\n";
    foreach ($res['hits']['hits'] as $hit) {
        $src = $hit['_source'];
        echo "ES User ID: " . ($src['id'] ?? $src['user_id'] ?? 'none') . " | Name: " . ($src['name'] ?? '') . " | Email: " . ($src['email'] ?? '') . " | Status: " . ($src['status'] ?? '') . " | Pass: " . (isset($src['password']) ? substr($src['password'], 0, 10) : 'NO_KEY') . " | PassLen: " . strlen($src['password'] ?? '') . "\n";
    }
} catch (\Throwable $e) {
    echo "ES Error: " . $e->getMessage() . "\n";
}
