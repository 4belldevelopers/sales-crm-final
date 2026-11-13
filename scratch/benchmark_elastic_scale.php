<?php

/**
 * Super Admin Company Management - Phase 5.1
 * Realistic Dataset Scale Benchmark (10,000, 50,000, 100,000 Companies)
 *
 * Isolated Elasticsearch index: benchmark_companies_scale
 * NEVER modifies production data or production indices.
 */

require_once __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Services\ElasticService;

$elasticService = app(ElasticService::class);
$client = $elasticService->getClient();
$benchmarkIndex = 'benchmark_companies_scale';

echo "================================================================================\n";
echo "   SUPER ADMIN COMPANY MANAGEMENT - REALISTIC DATASET SCALE BENCHMARK\n";
echo "================================================================================\n\n";

// 1. Clean up old benchmark index if exists
if ($client->indices()->exists(['index' => $benchmarkIndex])) {
    echo "Cleaning up existing benchmark index...\n";
    $client->indices()->delete(['index' => $benchmarkIndex]);
}

// 2. Create benchmark index with production-equivalent mapping
echo "Creating isolated benchmark index: {$benchmarkIndex}...\n";
$client->indices()->create([
    'index' => $benchmarkIndex,
    'body' => [
        'settings' => [
            'number_of_shards' => 1,
            'number_of_replicas' => 0,
            'refresh_interval' => '-1', // Disable refresh during bulk load for maximum ingestion speed
        ],
        'mappings' => [
            'properties' => [
                'id' => ['type' => 'long'],
                'company_name' => [
                    'type' => 'text',
                    'fields' => ['raw' => ['type' => 'keyword']],
                ],
                'company_email' => ['type' => 'keyword'],
                'company_phone' => ['type' => 'keyword'],
                'website' => ['type' => 'keyword'],
                'status' => ['type' => 'keyword'],
                'accountVerify' => ['type' => 'integer'],
                'package_type' => ['type' => 'keyword'],
                'package_id' => ['type' => 'integer'],
                'jobs_count' => ['type' => 'integer'],
                'users_count' => ['type' => 'integer'],
                'location' => ['type' => 'text', 'fields' => ['raw' => ['type' => 'keyword']]],
                'aggregate_version' => ['type' => 'integer'],
                'created_at' => ['type' => 'date'],
                'curr_date' => ['type' => 'date'],
            ],
        ],
    ],
]);

$results = [
    'benchmark_metadata' => [
        'module' => 'SuperAdminCompany',
        'benchmark_target' => 'Elasticsearch Realistic Scale Ingestion & Query Performance',
        'timestamp' => now()->toIso8601String(),
    ],
    'scale_tiers' => [],
];

$tiers = [10000, 50000, 100000];
$currentDocCount = 0;

$firstNames = ['Apex', 'Nova', 'Vertex', 'Starlight', 'Quantum', 'Nexus', 'Horizon', 'Pinnacle', 'Synergy', 'Catalyst', 'Vanguard', 'Omni', 'BlueWave', 'Alpha', 'Zenith'];
$industries = ['Technologies', 'Consulting', 'Global', 'Solutions', 'Logistics', 'Healthcare', 'Fintech', 'Holdings', 'Ventures', 'Dynamics'];
$cities = ['New York', 'London', 'San Francisco', 'Bengaluru', 'Singapore', 'Berlin', 'Tokyo', 'Sydney', 'Toronto', 'Dubai'];

foreach ($tiers as $targetCount) {
    echo "\n>>> Ingesting to Tier: {$targetCount} Documents (Current: {$currentDocCount})...\n";
    $needed = $targetCount - $currentDocCount;
    $chunkSize = 2500;
    $ingestT0 = microtime(true);

    while ($needed > 0) {
        $batchSize = min($needed, $chunkSize);
        $bulkParams = ['body' => []];

        for ($i = 0; $i < $batchSize; $i++) {
            $docId = $currentDocCount + $i + 1;
            $name = $firstNames[array_rand($firstNames)] . ' ' . $industries[array_rand($industries)] . ' ' . $docId;
            $status = ($docId % 5 === 0) ? 'deactive' : 'active';
            $verify = ($docId % 3 === 0) ? 1 : 0;
            $pkgType = ($docId % 2 === 0) ? 'annual' : 'monthly';

            $bulkParams['body'][] = [
                'index' => [
                    '_index' => $benchmarkIndex,
                    '_id' => (string) $docId,
                ],
            ];
            $bulkParams['body'][] = [
                'id' => $docId,
                'company_name' => $name,
                'company_email' => "contact{$docId}@example.com",
                'company_phone' => "+1-555-" . str_pad($docId, 4, '0', STR_PAD_LEFT),
                'website' => "https://company{$docId}.example.org",
                'status' => $status,
                'accountVerify' => $verify,
                'package_type' => $pkgType,
                'package_id' => ($docId % 4) + 1,
                'jobs_count' => ($docId % 25),
                'users_count' => ($docId % 10) + 1,
                'location' => $cities[array_rand($cities)],
                'aggregate_version' => 1,
                'created_at' => now()->subDays($docId % 365)->toIso8601String(),
                'curr_date' => now()->toIso8601String(),
            ];
        }

        $client->bulk($bulkParams);
        $currentDocCount += $batchSize;
        $needed -= $batchSize;
        echo ".";
    }

    $ingestSec = microtime(true) - $ingestT0;
    $ingestRps = round(($targetCount - ($currentDocCount - ($targetCount - ($currentDocCount - $needed)))) / max($ingestSec, 0.001), 1);
    echo "\n  Ingestion to {$targetCount} docs complete in " . round($ingestSec, 2) . "s\n";

    // Enable refresh and refresh index for querying
    $client->indices()->putSettings([
        'index' => $benchmarkIndex,
        'body' => ['settings' => ['refresh_interval' => '1s']],
    ]);
    $client->indices()->refresh(['index' => $benchmarkIndex]);

    echo "  Running Query Benchmarks for {$targetCount} documents...\n";

    // Benchmark Suite:
    $queries = [
        'default_listing' => [
            'query' => ['match_all' => new \stdClass()],
            'from' => 0,
            'size' => 10,
            'sort' => [['id' => ['order' => 'desc']]],
        ],
        'exact_filter_status_verify' => [
            'query' => [
                'bool' => [
                    'filter' => [
                        ['term' => ['status' => 'active']],
                        ['term' => ['accountVerify' => 1]],
                    ],
                ],
            ],
            'from' => 0,
            'size' => 10,
        ],
        'fulltext_search_prefix' => [
            'query' => [
                'bool' => [
                    'must' => [
                        ['match_phrase_prefix' => ['company_name' => 'Nova Technologies']],
                    ],
                ],
            ],
            'from' => 0,
            'size' => 10,
        ],
        'kpi_aggregations' => [
            'size' => 0,
            'aggs' => [
                'by_status' => ['terms' => ['field' => 'status']],
                'by_verification' => ['terms' => ['field' => 'accountVerify']],
                'by_package' => ['terms' => ['field' => 'package_type']],
            ],
        ],
        'deep_pagination_page_50' => [
            'query' => ['term' => ['status' => 'active']],
            'from' => 500,
            'size' => 10,
        ],
        'deep_pagination_page_500' => [
            'query' => ['term' => ['status' => 'active']],
            'from' => 5000,
            'size' => 10,
        ],
    ];

    $tierBenchResults = [];
    $iterationsPerQuery = 50;

    foreach ($queries as $qName => $body) {
        $latencies = [];
        for ($k = 0; $k < $iterationsPerQuery; $k++) {
            $t0 = microtime(true);
            $resp = $client->search([
                'index' => $benchmarkIndex,
                'body' => $body,
            ]);
            $latencies[] = (microtime(true) - $t0) * 1000;
        }

        sort($latencies);
        $cnt = count($latencies);
        $p50 = $latencies[(int) ceil(0.50 * $cnt) - 1];
        $p95 = $latencies[(int) ceil(0.95 * $cnt) - 1];
        $p99 = $latencies[(int) ceil(0.99 * $cnt) - 1];
        $min = $latencies[0];
        $max = $latencies[$cnt - 1];
        $avg = array_sum($latencies) / $cnt;

        $tierBenchResults[$qName] = [
            'samples' => $cnt,
            'min_ms' => round($min, 2),
            'avg_ms' => round($avg, 2),
            'p50_ms' => round($p50, 2),
            'p95_ms' => round($p95, 2),
            'p99_ms' => round($p99, 2),
            'max_ms' => round($max, 2),
            'hits' => $resp['hits']['total']['value'] ?? ($resp['hits']['total'] ?? 0),
        ];

        echo sprintf("    %-30s | p50: %6.2f ms | p95: %6.2f ms | p99: %6.2f ms | hits: %d\n",
            $qName, $tierBenchResults[$qName]['p50_ms'], $tierBenchResults[$qName]['p95_ms'], $tierBenchResults[$qName]['p99_ms'], $tierBenchResults[$qName]['hits']);
    }

    $results['scale_tiers']["tier_{$targetCount}"] = [
        'document_count' => $targetCount,
        'query_benchmarks' => $tierBenchResults,
    ];

    // Put settings back for next ingest
    if ($targetCount < 100000) {
        $client->indices()->putSettings([
            'index' => $benchmarkIndex,
            'body' => ['settings' => ['refresh_interval' => '-1']],
        ]);
    }
}

// Clean up benchmark index to leave zero footprint
echo "\nCleaning up benchmark index {$benchmarkIndex}...\n";
$client->indices()->delete(['index' => $benchmarkIndex]);
echo "Benchmark index deleted successfully.\n";

$resultsDir = dirname(__DIR__) . '/docs/audit documents/superadmin_company/phase5';
if (! is_dir($resultsDir)) {
    mkdir($resultsDir, 0755, true);
}
$jsonFile = $resultsDir . '/elastic_scale_benchmark_results.json';
file_put_contents($jsonFile, json_encode($results, JSON_PRETTY_PRINT));
echo "Realistic Scale Benchmark Saved To: {$jsonFile}\n";
