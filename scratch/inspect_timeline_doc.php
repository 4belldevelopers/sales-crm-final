<?php

require_once __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$es = app(\App\Services\ElasticService::class);

foreach (['ats_candidate_timeline', 'local_candidate_timeline'] as $idx) {
    if ($es->indexExists($idx)) {
        $res = $es->getClient()->search([
            'index' => $idx,
            'size' => 2
        ]);
        echo "Found index: {$idx}, total: " . ($res['hits']['total']['value'] ?? count($res['hits']['hits'])) . "\n";
        foreach ($res['hits']['hits'] as $hit) {
            echo "--- HIT " . $hit['_id'] . " ---\n";
            $src = $hit['_source'];
            echo "master_process_name: " . ($src['master_process_name'] ?? 'N/A') . "\n";
            echo "prev_process_name: " . ($src['prev_process_name'] ?? 'N/A') . "\n";
            echo "name (user): " . ($src['name'] ?? 'N/A') . "\n";
            echo "event_by_user_id: " . ($src['event_by_user_id'] ?? 'N/A') . "\n";
            echo "cur_companyname: " . ($src['cur_companyname'] ?? 'N/A') . "\n";
            echo "candidate_name: " . ($src['candidate_name'] ?? 'N/A') . "\n";
            echo "Keys: " . implode(', ', array_keys($src)) . "\n\n";
        }
        break;
    }
}
