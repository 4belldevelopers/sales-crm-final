<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$elastic = app(App\Services\ElasticService::class);
$indices = ['ats_clients_elastic', 'local_clients_elastic', 'local_companies_elastic', 'ats_companies_elastic'];
foreach ($indices as $idx) {
    if ($elastic->indexExists($idx)) {
        try {
            $res = $elastic->getClient()->search([
                'index' => $idx,
                'size' => 5,
                'body' => [
                    'query' => [
                        'bool' => [
                            'should' => [
                                ['term' => ['id' => 18]],
                                ['term' => ['client_id' => 18]],
                            ]
                        ]
                    ]
                ]
            ]);
            echo "Index {$idx}: found " . count($res['hits']['hits']) . " hits\n";
            foreach ($res['hits']['hits'] as $h) {
                print_r($h['_source']);
            }
        } catch (\Throwable $e) {
            echo "Index {$idx} search error: " . $e->getMessage() . "\n";
        }
    } else {
        echo "Index {$idx} does not exist\n";
    }
}
