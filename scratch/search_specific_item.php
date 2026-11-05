<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$elastic = app(\App\Services\ElasticService::class);
$indices = ['ats_candidate_timeline', 'local_candidate_timeline', 'local_agency_activity_stream', 'ats_jobs_elastic', 'local_jobs_elastic', 'ats_candidates_elastic', 'local_candidates_elastic'];

foreach ($indices as $idx) {
    try {
        $res = $elastic->getClient()->search([
            'index' => $idx,
            'body' => [
                'query' => [
                    'query_string' => [
                        'query' => '*1789561409* OR *Lead Java Developer* OR *Chief Java Architect*'
                    ]
                ]
            ]
        ]);
        if (!empty($res['hits']['hits'])) {
            echo "MATCH IN $idx (count: " . count($res['hits']['hits']) . "):\n";
            foreach ($res['hits']['hits'] as $h) {
                echo "  ID: {$h['_id']} | " . json_encode($h['_source']) . "\n\n";
            }
        }
    } catch (\Throwable $e) {
    }
}
