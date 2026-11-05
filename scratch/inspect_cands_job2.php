<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== CANDIDATES ON JOB 2 in add_job_wise_condidates ===\n";
$rows = DB::table('add_job_wise_condidates')->where('job_id', 2)->get();
foreach ($rows as $r) {
    print_r($r);
    break;
}

echo "\n=== TIMELINE EVENTS FOR JOB 2 in ats_candidate_timeline ===\n";
$elastic = app(\App\Services\ElasticService::class);
$res = $elastic->getClient()->search([
    'index' => 'ats_candidate_timeline',
    'body' => [
        'size' => 20,
        'query' => ['term' => ['job_id' => 2]]
    ]
]);
foreach ($res['hits']['hits'] as $h) {
    $s = $h['_source'];
    echo "ID: {$h['_id']} | app_id: {$s['application_id']} | event_id: {$s['event_id']} | master_process: {$s['master_process_name']} | event_by_user_id: {$s['event_by_user_id']} | name: {$s['name']} | cur_companyname: {$s['cur_companyname']} | created_at: {$s['created_at']}\n";
}
