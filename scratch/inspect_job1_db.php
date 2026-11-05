<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$job = DB::table('jobs')->where('id', 1)->first();
$rec = DB::table('recommendation_relation')->where('job_id', 1)->get();
echo "Job 1 status in DB: {$job->status} | updated_at: {$job->updated_at}\n";
foreach ($rec as $r) {
    echo "  Rec ID: {$r->id} | from: {$r->from_company_id} -> to: {$r->to_company_id} | final_status: {$r->final_status} | updated_at: {$r->updated_at}\n";
}
