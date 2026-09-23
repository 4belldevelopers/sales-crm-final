<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$elasticService = app(App\Services\ElasticService::class);
$company_id = 2;
$to_company_id = 4;

echo "=== Testing Offers Query with master_status_id = 5 filter ===\n";
$paramsOffer = [
    'index' => ELASTIC_CANDIDATES,
    'body' => [
        'track_total_hits' => true,
        'size' => 0,
        'query' => [
            'bool' => [
                'must' => [
                    ['term' => ['from_company_id' => $company_id]],
                    [
                        'bool' => [
                            'should' => [
                                ['term' => ['ClientComID' => $to_company_id]],
                                ['term' => ['thirdparty_company_id' => $to_company_id]],
                            ],
                            'minimum_should_match' => 1,
                        ],
                    ],
                    ['term' => ['final_status' => 1]],
                    [
                        'bool' => [
                            'should' => [
                                ['term' => ['job_master_status_id' => 5]],
                                ['term' => ['master_status_id' => 5]],
                            ],
                            'minimum_should_match' => 1,
                        ],
                    ],
                ],
            ],
        ],
    ],
];
$resOffer = $elasticService->getClient()->search($paramsOffer);
echo "ES Offer hits count: " . $resOffer['hits']['total']['value'] . "\n";

echo "\n=== Testing Candidates Interviews Query with ClientComID / thirdparty_company_id filter ===\n";
$recentParamsMonth = [
    'index' => ELASTIC_INERTVIEW_TRACKER,
    'body' => [
        'track_total_hits' => true,
        'size' => 10,
        'query' => [
            'bool' => [
                'must' => [
                    ['term' => ['from_company_id' => $company_id]],
                    [
                        'bool' => [
                            'should' => [
                                ['term' => ['ClientComID' => $to_company_id]],
                                ['term' => ['thirdparty_company_id' => $to_company_id]],
                            ],
                            'minimum_should_match' => 1,
                        ],
                    ],
                ],
            ],
        ],
    ],
];
$resInterview = $elasticService->getClient()->search($recentParamsMonth);
echo "ES Interview hits count: " . $resInterview['hits']['total']['value'] . "\n";
foreach ($resInterview['hits']['hits'] as $hit) {
    $src = $hit['_source'];
    echo "  - Candidate: " . ($src['full_name'] ?? 'N/A') . " | ClientComID: " . ($src['ClientComID'] ?? '0') . " | thirdparty_company_id: " . ($src['thirdparty_company_id'] ?? '0') . "\n";
}
