<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$elasticService = app(App\Services\ElasticService::class);
$company_id = 2;
$to_company_id = 4;

echo "=== ES ELASTIC_INERTVIEW_TRACKER documents for from_company_id=2, to_company_id=4 ===\n";
if ($elasticService->indexExists(ELASTIC_INERTVIEW_TRACKER)) {
    $res = $elasticService->getClient()->search([
        'index' => ELASTIC_INERTVIEW_TRACKER,
        'body' => [
            'size' => 50,
            'query' => [
                'bool' => [
                    'must' => [
                        ['term' => ['from_company_id' => $company_id]],
                        ['term' => ['to_company_id' => $to_company_id]],
                    ]
                ]
            ]
        ]
    ]);
    print_r($res['hits']['hits']);
} else {
    echo "Index does not exist\n";
}

echo "\n=== ES ELASTIC_CANDIDATES documents for from_company_id=2, ClientComID/to_company_id/thirdparty_company_id=4 ===\n";
if ($elasticService->indexExists(ELASTIC_CANDIDATES)) {
    $resCand = $elasticService->getClient()->search([
        'index' => ELASTIC_CANDIDATES,
        'body' => [
            'size' => 50,
            'query' => [
                'bool' => [
                    'must' => [
                        ['term' => ['from_company_id' => $company_id]],
                        [
                            'bool' => [
                                'should' => [
                                    ['term' => ['ClientComID' => $to_company_id]],
                                    ['term' => ['to_company_id' => $to_company_id]],
                                    ['term' => ['thirdparty_company_id' => $to_company_id]],
                                ],
                                'minimum_should_match' => 1
                            ]
                        ]
                    ]
                ]
            ]
        ]
    ]);
    foreach ($resCand['hits']['hits'] as $hit) {
        $src = $hit['_source'];
        echo "Candidate ID: " . ($src['id'] ?? 'N/A') . " | Name: " . ($src['full_name'] ?? 'N/A') . " | master_status_id: " . ($src['master_status_id'] ?? 'N/A') . " | job_master_status_id: " . ($src['job_master_status_id'] ?? 'N/A') . "\n";
    }
}
