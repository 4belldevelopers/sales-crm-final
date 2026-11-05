<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$elastic = app(\App\Services\ElasticService::class);
$indices = $elastic->getClient()->cat()->indices(['format' => 'json']);
foreach ($indices as $idx) {
    $name = $idx['index'];
    if (str_starts_with($name, '.')) continue;
    try {
        $res = $elastic->getClient()->search([
            'index' => $name,
            'body' => [
                'query' => [
                    'query_string' => ['query' => '*2026-09-21*']
                ]
            ]
        ]);
        if (!empty($res['hits']['hits'])) {
            echo "MATCH IN $name: " . count($res['hits']['hits']) . "\n";
            foreach ($res['hits']['hits'] as $h) {
                echo "  ID: {$h['_id']} | " . json_encode($h['_source']) . "\n";
            }
        }
    } catch (\Throwable $e) {}
}
